<?php

/*************************** New Purchase ***********************/
if(isset($_GET['page']) and $_GET['page'] == "newPurchase") {

    //print_r($_POST);

    //echo "Hi";

    //exit();

    // Check if the biller is set
    if(!isset($_SESSION["aid"])) {
        return _e("You must set you as a biller to make purchase");
    }

    $accounts_balance = accounts_balance($_SESSION["aid"]);
    
    if( empty($_POST["purchaseCompany"]) ) {
        return _e("Please select company");
    } elseif( empty($_POST["purchaseWarehouse"]) ) {
        return _e("Please select warehouse");
    } elseif( count($_POST["productID"]) < 1 ) {
        return _e("Please select at least one product");
    } elseif(!negative_value_is_allowed($_SESSION["aid"]) and $accounts_balance < $_POST["purchasePaidAmount"] ) {
        return _e("Payment amount is exceeded of account balance (%.2f)", number_format($accounts_balance, 2));
    }

    // Insert purchase data
    $purchaseData = $_POST;

    $productPurchasePrice = $purchaseData["productPurchasePrice"];
    $productDiscount = $purchaseData["productPurchaseDiscount"];
    $productQnt = $purchaseData["productQnt"];

    // Calculate the Product purchase Grand Total amount
    $PurchaseTotalAmount = 0;
    $purchaseTotalItemDiscount = 0;
    
    foreach($productPurchasePrice as $key => $value) {
        
        $value = empty($value) ? 0 : $value;
        $purchaseTotalItemDiscount += calculateDiscount($value, $productDiscount[$key]) * $productQnt[$key];
        
        $PurchaseTotalAmount += $productQnt[$key] * $value;

    }

    $totalPurchasePrice = $PurchaseTotalAmount - $purchaseTotalItemDiscount;

    $purchaseDiscount = calculateDiscount($totalPurchasePrice, $purchaseData["purchaseDiscountValue"]);

    $tariffCharges = array_sum($purchaseData["tariffChargesAmount"]);
    $shipping = empty($purchaseData["purchaseShipping"]) ? 0 : $purchaseData["purchaseShipping"];

    // calculate grand total with rounding the decimal places
    $grandTotal = round( ($totalPurchasePrice + $tariffCharges + $shipping) -  $purchaseDiscount, get_options("decimalPlaces") );
    $paidAmount = empty($purchaseData["purchasePaidAmount"]) ? 0 : $purchaseData["purchasePaidAmount"];

    // Generate the payment status
    $salesPaymentStatus = "due";
    if($grandTotal <= $paidAmount) {

        $salesPaymentStatus = "paid";

    } else if($grandTotal > $paidAmount and $paidAmount > 0) {

        $salesPaymentStatus = "partial";

    }

    // Upload the image
    $purchaseBill = NULL;
    if($_FILES["purchaseBillAttachment"]["size"] > 0) {

        $billerCompanyName = easySelectA(array(
            "table"     => "companies",
            "fields"    => "concat(company_name, '_', company_id) AS company_name",
            "where"     => array(
                "company_id"    => $purchaseData["purchaseCompany"]
            )
        ))["data"][0]["company_name"];

        // generate the filename based on reference and date
        $fileName = empty($purchaseData["purchaseReference"]) ? $purchaseData["purchaseCompany"] ."_". time() : $purchaseData["purchaseCompany"] ."_". $purchaseData["purchaseReference"] . "_" . time();
        $purchaseBill = easyUpload($_FILES["purchaseBillAttachment"], "bills/companies/{$billerCompanyName}", $fileName);

        if(!isset($purchaseBill["success"])) {
            return _e($purchaseBill);
        } else {
            $purchaseBill = $purchaseBill["fileName"];
        }
        
    }

    
    // Insert data into product_purchase table
    $insertPurchase = easyInsert(
        "purchases",
        array (
            "purchase_date"                     => $purchaseData["purchaseDate"],
            "purchase_status"                   => $purchaseData["purchaseStatus"],
            "purchase_reference"                => empty($purchaseData["purchaseReference"]) ? NULL : $purchaseData["purchaseReference"],
            "purchase_company_id"               => $purchaseData["purchaseCompany"],
            "purchase_warehouse_id"             => $purchaseData["purchaseWarehouse"],
            "purchase_shop_id"                  => $_SESSION["sid"],
            "purchase_quantity"                 => array_sum($purchaseData["productQnt"]),
            "purchase_total_amount"             => $PurchaseTotalAmount,
            "purchase_product_discount"         => $purchaseTotalItemDiscount,
            "purchase_discount"                 => $purchaseDiscount,
            "purchase_tariff_charges"           => $tariffCharges,
            "purchase_tariff_charges_details"   => serialize( array("tariff" => $purchaseData["tariffChargesName"], "value" => $purchaseData["tariffChargesAmount"]) ),
            "purchase_shipping"                 => $shipping,
            "purchase_grand_total"              => $grandTotal,
            "purchase_paid_amount"              => $paidAmount,
            "purchase_change"                   => $grandTotal < $paidAmount ? $paidAmount - $grandTotal : 0,
            "purchase_due"                      => $grandTotal > $paidAmount ? $grandTotal - $paidAmount : 0,
            "purchase_payment_status"           => $salesPaymentStatus,
            "purchase_payment_method"           => $purchaseData["purchasePaymentMethod"],
            "purchase_total_item"               => count($purchaseData["productQnt"]),
            "purchase_note"                     => $purchaseData["purchaseDescription"],
            "purchase_attachments"              => $purchaseBill,
            "purchase_created_by"               => $_SESSION["uid"]
        ),
        array(),
        true
    );

    // check if the purchase successfully inserted then got to next for adding purchase item
    if( isset($insertPurchase["status"]) and $insertPurchase["status"] === "success") {
        
        foreach($purchaseData["productID"] as $key => $productId) {
            
            // Calculate the discount
            $productPurchaseDiscount = calculateDiscount($productPurchasePrice[$key], $productDiscount[$key]);

            // Calculate the amount after discount
            $itemAmoutnAfterDiscount = $productPurchasePrice[$key] - $productPurchaseDiscount;

            $insertPurchaseItem = easyInsert(
                "product_stock",
                array (
                    "stock_type"            => ( $purchaseData["purchaseStatus"] === "Received" ) ? 'purchase' : 'purchase-order',
                    "stock_entry_date"      => $purchaseData["purchaseDate"],
                    "stock_purchase_id"     => $insertPurchase["last_insert_id"],
                    "stock_shop_id"         => $_SESSION["sid"],
                    "stock_product_id"      => $productId,
                    "stock_batch_id"        => empty($purchaseData["productBatch"][$key]) ? NULL : $purchaseData["productBatch"][$key],
                    "stock_warehouse_id"    => $purchaseData["purchaseWarehouse"],
                    "stock_item_qty"        => $productQnt[$key],
                    "stock_item_price"      => $productPurchasePrice[$key],
                    "stock_item_discount"   => $productPurchaseDiscount,
                    "stock_item_subtotal"   => $productQnt[$key] * $itemAmoutnAfterDiscount, // Calculate the items total amount
                    "stock_created_by"      => $_SESSION["uid"]
                )
            );

            // Select bundle products or sub products 
            $subProducts = easySelectA(array(
                "table"     => "products as product",
                "fields"    => "bg_item_product_id, 
                                sub_product.product_purchase_price as purchase_price,
                                bg_product_qnt
                                ",
                "join"      => array(
                    "inner join {$table_prefeix}bg_product_items as bg_product on bg_product_id = product_id",
                    "left join {$table_prefeix}products as sub_product on sub_product.product_id = bg_item_product_id"
                ),
                "where"     => array(
                    "( product.has_sub_product = 1 or product.product_type = 'Bundle' ) and bg_product.is_raw_materials = 0 and product.product_id = {$productId}"
                )
            ));

            // Insert sub/ bundle products
            if($subProducts !== false) {

                $subProducts = $subProducts["data"];
                foreach($subProducts as $spKey => $sp) {
                
                    // Calculate the discount
                    $productPurchaseDiscount = calculateDiscount($sp["purchase_price"], $productDiscount[$key]);
        
                    // Calculate the amount after discount
                    $itemAmoutnAfterDiscount = $sp["purchase_price"] - $productPurchaseDiscount;

                    $totalSubProductQty = $productQnt[$key] * $sp["bg_product_qnt"];
        
                    $insertPurchaseItem = easyInsert(
                        "product_stock",
                        array (
                            "stock_type"            => ( $purchaseData["purchaseStatus"] === "Received" ) ? 'purchase' : 'purchase-order',
                            "stock_entry_date"      => $purchaseData["purchaseDate"],
                            "stock_purchase_id"     => $insertPurchase["last_insert_id"],
                            "stock_shop_id"         => $_SESSION["sid"],
                            "stock_product_id"      => $sp["bg_item_product_id"],
                            "stock_batch_id"        => NULL,
                            "stock_warehouse_id"    => $purchaseData["purchaseWarehouse"],
                            "stock_item_qty"        => $totalSubProductQty,
                            "stock_item_price"      => $sp["purchase_price"],
                            "stock_item_discount"   => $productPurchaseDiscount,
                            "stock_item_subtotal"   => $totalSubProductQty * $itemAmoutnAfterDiscount, // Calculate the items total amount
                            "stock_created_by"      => $_SESSION["uid"],
                            "is_bundle_item"        => 1
                        )
                    );
        
                }

            }

        }

        

        // if paid amount grater then zero in product purchase
        // then ad to expenses
        if($paidAmount > 0) {

            // Payment reference for BILL
            $paymentReferences = payment_reference("bill");

            // Upload the purchase bill payment attachment, cheque etc
            $purchasePaymentAttachment = NULL;
            if($_FILES["purchasePaymentAttachment"]["size"] > 0) {

                $billerCompanyName = easySelectA(array(
                    "table"     => "companies",
                    "fields"    => "concat(company_name, '_', company_id) AS company_name",
                    "where"     => array(
                        "company_id"    => $purchaseData["purchaseCompany"]
                    )
                ))["data"][0]["company_name"];

                // generate the filename based on reference and date
                $fileName = empty($purchaseData["purchaseReference"]) ? $purchaseData["purchaseCompany"] ."_". time() : $purchaseData["purchaseCompany"] ."_". $purchaseData["purchaseReference"] . "_" . time();
                $purchasePaymentAttachment = easyUpload($_FILES["purchasePaymentAttachment"], "cheque/companies/{$billerCompanyName}", $fileName);

                if(!isset($purchasePaymentAttachment["success"])) {
                    return _e($purchasePaymentAttachment);
                } else {
                    $purchasePaymentAttachment = $purchasePaymentAttachment["fileName"];
                }
                
            }

            // Insert the Bill Payment
            $insertPurchasePayment = easyInsert (
                "payments",
                array (
                    "payment_date"              => $purchaseData["purchaseDate"],
                    "payment_to_company"        => $purchaseData["purchaseCompany"],
                    "payment_purchase_id"       => $insertPurchase["last_insert_id"],
                    "payment_type"              => "Bill",
                    "payment_status"            => "Complete",
                    "payment_amount"            => $paidAmount,
                    "payment_from"              => $_SESSION["aid"],
                    "payment_description"       => "Payment Made on Product Purchase",
                    "payment_method"            => $purchaseData["purchasePaymentMethod"],
                    "payment_cheque_no"         => empty($purchaseData["purchasePaymentChequeNo"]) ? NULL : $purchaseData["purchasePaymentChequeNo"],
                    "payment_cheque_date"       => empty($purchaseData["purchasePaymentChequeDate"]) ? NULL : $purchaseData["purchasePaymentChequeDate"],
                    "payment_attachement"       => $purchasePaymentAttachment,
                    "payment_reference"         => $paymentReferences,
                    "payment_made_by"           => $_SESSION["uid"]
                ),
                array(),
                true
            );

            if(isset($insertPurchasePayment["status"]) and $insertPurchasePayment["status"] === "success" ) {

                // Insert payment items
                easyInsert(
                    "payment_items",
                    array (
                        "payment_items_payments_id" => $insertPurchasePayment["last_insert_id"],
                        "payment_items_date"        => $purchaseData["purchaseDate"],
                        "payment_items_type"        => "Bill",
                        "payment_items_description" => "",
                        "payment_items_company"     => $purchaseData["purchaseCompany"],
                        "payment_items_amount"      => $paidAmount,
                        "payment_items_accounts"    => $_SESSION["aid"],
                        "payment_items_made_by"     => $_SESSION["uid"]
                    )
                );
                
                // Update Accounts Balance
                updateAccountBalance($_SESSION["aid"]);

            }

        }

        _s("Purchase has been successfully added");

    } else {
        _e($insertPurchase);
    }

}



/*************************** New Purchase ***********************/
if(isset($_GET['page']) and $_GET['page'] == "updatePurchase") {

    //print_r($_POST);

    //exit();

    // Check if the biller is set
    if(!isset($_SESSION["aid"])) {
        return _e("You must set you as a biller to update purchase");
    }

    $selectPurchase = easySelectA(array(
        "table"     => "purchases",
        "where"     => array(
            "purchase_id"   => $_POST["purchase_id"]
        )
    ));

    $accounts_balance = accounts_balance($_SESSION["aid"]);
    
    if( empty($_POST["purchaseCompany"]) ) {
        return _e("Please select company");
    } elseif( empty($_POST["purchaseWarehouse"]) ) {
        return _e("Please select warehouse");
    } elseif( count($_POST["productID"]) < 1 ) {
        return _e("Please select at least one product");
    }

    // Insert purchase data
    $purchaseData = $_POST;

    $productPurchasePrice = $purchaseData["productPurchasePrice"];
    $productDiscount = $purchaseData["productPurchaseDiscount"];
    $productQnt = $purchaseData["productQnt"];

    // Calculate the Product purchase Grand Total amount
    $PurchaseTotalAmount = 0;
    $purchaseTotalItemDiscount = 0;
    
    foreach($productPurchasePrice as $key => $value) {
        
        $value = empty($value) ? 0 : $value;
        $purchaseTotalItemDiscount += calculateDiscount($value, $productDiscount[$key]) * $productQnt[$key];
        
        $PurchaseTotalAmount += $productQnt[$key] * $value;

    }

    $totalPurchasePrice = $PurchaseTotalAmount - $purchaseTotalItemDiscount;

    $purchaseDiscount = calculateDiscount($totalPurchasePrice, $purchaseData["purchaseDiscountValue"]);

    $tariffCharges = array_sum($purchaseData["tariffChargesAmount"]);
    $shipping = empty($purchaseData["purchaseShipping"]) ? 0 : $purchaseData["purchaseShipping"];

    // calculate grand total with rounding the decimal places
    $grandTotal = round( ($totalPurchasePrice + $tariffCharges + $shipping) -  $purchaseDiscount, get_options("decimalPlaces") );
    $paidAmount = empty($purchaseData["purchasePaidAmount"]) ? 0 : $purchaseData["purchasePaidAmount"];

    // Generate the payment status
    $salesPaymentStatus = "due";
    if($grandTotal <= $paidAmount) {

        $salesPaymentStatus = "paid";

    } else if($grandTotal > $paidAmount and $paidAmount > 0) {

        $salesPaymentStatus = "partial";

    }

    // Upload the image
    if($_FILES["purchaseBillAttachment"]["size"] > 0) {

        $billerCompanyName = easySelectA(array(
            "table"     => "companies",
            "fields"    => "concat(company_name, '_', company_id) AS company_name",
            "where"     => array(
                "company_id"    => $purchaseData["purchaseCompany"]
            )
        ))["data"][0]["company_name"];

        // generate the filename based on reference and date
        $fileName = empty($purchaseData["purchaseReference"]) ? $purchaseData["purchaseCompany"] ."_". time() : $purchaseData["purchaseCompany"] ."_". $purchaseData["purchaseReference"] . "_" . time();
        $purchaseBill = easyUpload($_FILES["purchaseBillAttachment"], "bills/companies/{$billerCompanyName}", $fileName);

        if(!isset($purchaseBill["success"])) {
            return _e($purchaseBill);
        } else {
            $purchaseBill = $purchaseBill["fileName"];
        }

        // update the purchase attachment
        $updatePurchase = easyUpdate(
            "purchases",
            array (
                "purchase_attachments"  => $purchaseBill,
            ),
            array(
                "purchase_id"   => $_POST["purchase_id"]
            )
        );
        
    }

    
    // update purchase
    $updatePurchase = easyUpdate(
        "purchases",
        array (
            "purchase_date"                     => $purchaseData["purchaseDate"],
            "purchase_status"                   => $purchaseData["purchaseStatus"],
            "purchase_reference"                => empty($purchaseData["purchaseReference"]) ? NULL : $purchaseData["purchaseReference"],
            "purchase_company_id"               => $purchaseData["purchaseCompany"],
            "purchase_warehouse_id"             => $purchaseData["purchaseWarehouse"],
            "purchase_shop_id"                  => $_SESSION["sid"],
            "purchase_quantity"                 => array_sum($purchaseData["productQnt"]),
            "purchase_total_amount"             => $PurchaseTotalAmount,
            "purchase_product_discount"         => $purchaseTotalItemDiscount,
            "purchase_discount"                 => $purchaseDiscount,
            "purchase_tariff_charges"           => $tariffCharges,
            "purchase_tariff_charges_details"   => serialize( array("tariff" => $purchaseData["tariffChargesName"], "value" => $purchaseData["tariffChargesAmount"]) ),
            "purchase_shipping"                 => $shipping,
            "purchase_grand_total"              => $grandTotal,
            //"purchase_paid_amount"              => $paidAmount, //Paid amount cannot be edited
            "purchase_change"                   => $grandTotal < $paidAmount ? $paidAmount - $grandTotal : 0,
            "purchase_due"                      => $grandTotal > $paidAmount ? $grandTotal - $paidAmount : 0,
            "purchase_payment_status"           => $salesPaymentStatus,
            "purchase_payment_method"           => $purchaseData["purchasePaymentMethod"],
            "purchase_total_item"               => count($purchaseData["productQnt"]),
            "purchase_note"                     => $purchaseData["purchaseDescription"],
            "purchase_created_by"               => $_SESSION["uid"]
        ),
        array(
            "purchase_id"   => $_POST["purchase_id"]
        )
    );

    // check if the purchase successfully inserted then got to next for adding purchase item
    if( $updatePurchase === true ) {

        // Delete Preivous purchase product
        easyPermDelete(
            "product_stock",
            array(
                "stock_purchase_id" => $_POST["purchase_id"]
            )
        );

        
        foreach($purchaseData["productID"] as $key => $productId) {
            
            // Calculate the discount
            $productPurchaseDiscount = calculateDiscount($productPurchasePrice[$key], $productDiscount[$key]);

            // Calculate the amount after discount
            $itemAmoutnAfterDiscount = $productPurchasePrice[$key] - $productPurchaseDiscount;

            $insertPurchaseItem = easyInsert(
                "product_stock",
                array (
                    "stock_type"            => ( $purchaseData["purchaseStatus"] === "Received" ) ? 'purchase' : 'purchase-order',
                    "stock_entry_date"      => $purchaseData["purchaseDate"],
                    "stock_purchase_id"     => $_POST["purchase_id"],
                    "stock_shop_id"         => $_SESSION["sid"],
                    "stock_product_id"      => $productId,
                    "stock_batch_id"        => empty($purchaseData["productBatch"][$key]) ? NULL : $purchaseData["productBatch"][$key],
                    "stock_warehouse_id"    => $purchaseData["purchaseWarehouse"],
                    "stock_item_qty"        => $productQnt[$key],
                    "stock_item_price"      => $productPurchasePrice[$key],
                    "stock_item_discount"   => $productPurchaseDiscount,
                    "stock_item_subtotal"   => $productQnt[$key] * $itemAmoutnAfterDiscount, // Calculate the items total amount
                    "stock_created_by"      => $_SESSION["uid"]
                )
            );

            // Select products, which have sub products and insert sub/bundle products
            $subProducts = easySelectA(array(
                "table"     => "products as product",
                "fields"    => "bg_item_product_id, 
                                sub_product.product_purchase_price as purchase_price,
                                bg_product_qnt
                                ",
                "join"      => array(
                    "inner join {$table_prefeix}bg_product_items as bg_product on bg_product_id = product_id",
                    "left join {$table_prefeix}products as sub_product on sub_product.product_id = bg_item_product_id"
                ),
                "where"     => array(
                    "( product.has_sub_product = 1 or product.product_type = 'Bundle' ) and bg_product.is_raw_materials = 0 and product.product_id = {$productId}"
                )
            ));

            // Insert sub/ bundle products
            if($subProducts !== false) {

                $subProducts = $subProducts["data"];
                foreach($subProducts as $spKey => $sp) {
                
                    // Calculate the discount
                    $productPurchaseDiscount = calculateDiscount($sp["purchase_price"], $productDiscount[$key]);
        
                    // Calculate the amount after discount
                    $itemAmoutnAfterDiscount = $sp["purchase_price"] - $productPurchaseDiscount;

                    $totalSubProductQty = $productQnt[$key] * $sp["bg_product_qnt"];
        
                    $insertPurchaseItem = easyInsert(
                        "product_stock",
                        array (
                            "stock_type"            => ( $purchaseData["purchaseStatus"] === "Received" ) ? 'purchase' : 'purchase-order',
                            "stock_entry_date"      => $purchaseData["purchaseDate"],
                            "stock_purchase_id"     => $_POST["purchase_id"],
                            "stock_shop_id"         => $_SESSION["sid"],
                            "stock_product_id"      => $sp["bg_item_product_id"],
                            "stock_batch_id"        => NULL, // Sub product do not have batches.
                            "stock_warehouse_id"    => $purchaseData["purchaseWarehouse"],
                            "stock_item_qty"        => $totalSubProductQty,
                            "stock_item_price"      => $sp["purchase_price"],
                            "stock_item_discount"   => $productPurchaseDiscount,
                            "stock_item_subtotal"   => $totalSubProductQty * $itemAmoutnAfterDiscount, // Calculate the items total amount
                            "stock_created_by"      => $_SESSION["uid"],
                            "is_bundle_item"        => 1
                        )
                    );
        
                }

            }

        }


        _s("Purchase has been successfully updated");
        

    } else {

        _e($updatePurchase);

    }

}


/*************************** Product Purchase List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "productPurchaseList") {
    
    $requestData = $_REQUEST;
    $getData = [];
    
    // List of all columns name
    $columns = array(
        "",
        "purchase_add_on",
        "purchase_id",
        "company_name",
        "purchase_grand_total"
    );

    $shopId = "%";
    if( !is_super_admin() ) {
            $shopId = $_SESSION["sid"];
    }
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "purchases",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0 and is_return = 0 and purchase_shop_id like '{$shopId}'"
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }


    if(     !empty($requestData["search"]["value"]) or
            !empty($requestData["columns"][1]['search']['value']) or
            !empty($requestData["columns"][2]['search']['value']) or
            !empty($requestData["columns"][4]['search']['value']) or
            !empty($requestData["columns"][12]['search']['value']) 
        ) {  // get data with search

            $dateRange[0] = "";
            $dateRange[1] = "";
            $dateFilter = "";
            if(!empty($requestData["columns"][1]['search']['value'])) {
                $dateRange = explode(" - ", safe_input($requestData["columns"][1]['search']['value']));
                $dateFilter = "and purchase_date BETWEEN '{$dateRange[0]}' and '{$dateRange[1]}'";
            }
        
        $getData = easySelect(
                "purchases as product_purchase",
                "purchase_id, purchase_date, shop_name, purchase_note, purchase_payment_status, purchase_reference, purchase_company_id, company_name, round(purchase_total_amount, 2) as purchase_total_amount, 
                    round(purchase_product_discount, 2) as purchase_product_discount, round(purchase_discount, 2) as purchase_discount, round(purchase_shipping, 2) as purchase_shipping, 
                    round(purchase_grand_total, 2) as purchase_grand_total, round(purchase_paid_amount, 2) as purchase_paid_amount, round(purchase_due, 2) as purchase_due",
            array (
                "left join {$table_prefeix}companies on company_id = purchase_company_id",
                "left join {$table_prefeix}shops on shop_id = purchase_shop_id"
            ),
            array (
                "product_purchase.is_return = 0 and product_purchase.is_trash = 0 and (",
                " purchase_reference LIKE '". safe_input($requestData['search']['value']) ."%' ",
                " or company_name LIKE" => $requestData['search']['value'] . "%",
                ")",
                " AND purchase_shop_id" => $requestData["columns"][2]['search']['value'],
                " AND purchase_company_id" => $requestData["columns"][4]['search']['value'],
                " AND purchase_payment_status" => $requestData["columns"][12]['search']['value'],
                " AND purchase_shop_id like '{$shopId}' $dateFilter"
            ),
            array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        );

        $totalFilteredRecords = $getData ? $getData["count"] : 0;

    } else { // Get data withouth search

        $getData = easySelect(
            "purchases as product_purchase",
            "purchase_id, purchase_date, shop_name, purchase_note, purchase_payment_status, purchase_reference, purchase_company_id, company_name, round(purchase_total_amount, 2) as purchase_total_amount, 
            round(purchase_product_discount, 2) as purchase_product_discount, round(purchase_discount, 2) as purchase_discount, round(purchase_shipping, 2) as purchase_shipping, 
            round(purchase_grand_total, 2) as purchase_grand_total, round(purchase_paid_amount, 2) as purchase_paid_amount, round(purchase_due, 2) as purchase_due",
            array (
            "left join {$table_prefeix}companies on company_id = purchase_company_id",
            "left join {$table_prefeix}shops on shop_id = purchase_shop_id"
            ),
            array("product_purchase.is_trash = 0 and product_purchase.is_return = 0 and purchase_shop_id like '{$shopId}'"),
            array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        );

    }

    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['count']) and $getData['count'] > 0) {
        
        foreach($getData['data'] as $key => $value) {

            $paymentStatus = "";
            if($value["purchase_payment_status"] === "paid") {
                $paymentStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-success'>Paid</span>";
            } else if($value["purchase_payment_status"] === "partial") {
                $paymentStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-warning'>Partial</span>";
            } else {
                $paymentStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-danger'>Due</span>";
            }

            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["purchase_date"];
            $allNestedData[] = $value["shop_name"];
            $allNestedData[] = $value["purchase_reference"];
            $allNestedData[] = $value["company_name"];
            $allNestedData[] = $value["purchase_total_amount"];
            $allNestedData[] = $value["purchase_product_discount"] + $value["purchase_discount"];
            $allNestedData[] = $value["purchase_shipping"];
            $allNestedData[] = $value["purchase_grand_total"];
            $allNestedData[] = $value["purchase_paid_amount"];
            $allNestedData[] = $value["purchase_due"];
            $allNestedData[] = $value["purchase_grand_total"] - $value["purchase_due"];
            $allNestedData[] = $value["purchase_note"];
            $allNestedData[] = $paymentStatus;
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?module=stock-management&page=viewPurchasedProduct&id='. $value["purchase_id"] .'"  data-target="#modalDefault"><i class="fa fa-eye"></i> View Products</a></li>
                                        <li><a href="'. full_website_address() .'/stock-management/edit-purchase/?id='. $value["purchase_id"] .'"><i class="fa fa-edit"></i> Edit Purchase</a></li>
                                        <li><a data-toggle="modal" data-target="#modalDefault" href="'. full_website_address() .'/xhr/?module=stock-management&page=addPurchasePayments&purchase_id='. $value["purchase_id"] .'&cid='. $value["purchase_company_id"] .'"><i class="fa fa-money"></i> Add Payment</a></li>
                                        <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=stock-management&page=deletePurchasedProduct" data-to-be-deleted="'. $value["purchase_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                    </ul>
                                </div>';
            
            $allData[] = $allNestedData;
        }
    }
    

    $jsonData = array (
        "draw"              => intval( $requestData['draw'] ),
        "recordsTotal"      => intval( $totalRecords ),
        "recordsFiltered"   => intval( $totalFilteredRecords ),
        "data"              => $allData
    );
    
    // Encode in Json Formate
    echo json_encode($jsonData); 

}


/************************** addPurchasePayments **********************/
if(isset($_GET['page']) and $_GET['page'] == "addPurchasePayments") {
  
    // Include the modal header
    modal_header("Add Purchase Payments", full_website_address() . "/xhr/?module=stock-management&page=submitPurchasePayments");

    $purchaseDueAmount = easySelectA(array(
        "table"     => "purchases",
        "fields"    => "purchase_due",
        "where"     => array(
            "purchase_id"  => $_GET["purchase_id"]
        )
    ))["data"][0]["purchase_due"];
    
    ?>
      <div class="box-body">
        
        <div class="form-group required">
            <label for="purchasePaymentDate"><?= __("Date:"); ?></label>
            <input type="text" name="purchasePaymentDate" id="purchasePaymentDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker">
        </div>
        <div class="form-group required">
            <label for="addPurchasePaymentsAmount"><?= __("Amount:"); ?></label>
            <input type="number" name="addPurchasePaymentsAmount" id="addPurchasePaymentsAmount" onclick="this.select();" value="<?php echo number_format($purchaseDueAmount, 2, ".", ""); ?>" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="addPurchasePaymentsMethod"><?= __("Payment Method:"); ?></label>
            <select name="addPurchasePaymentsMethod" id="addPurchasePaymentsMethod" class="form-control select2" style="width: 100%">
                <?php
                    $paymentMethod = array("Cash", "Bank Transfer", "Cheque", "Others");
                    
                    foreach($paymentMethod as $method) {
                        echo "<option value='{$method}'>{$method}</option>";
                    }
                ?>
            </select>
        </div>
        <div id="hiddenItem" style="display: none;">
            <div class="form-group">
                <label for="purchasePaymentChequeNo"><?= __("Cheque No"); ?></label>
                <input type="text" name="purchasePaymentChequeNo" id="purchasePaymentChequeNo" class="form-control">
            </div>
            <div class="form-group">
                <label for="purchasePaymentChequeDate"><?= __("Cheque Date:"); ?></label>
                <input type="text" name="purchasePaymentChequeDate" id="purchasePaymentChequeDate" value="" class="form-control datePicker">
            </div>
            <div class="form-group">
                <label for="purchasePaymentAttachment"><?= __("Attachment"); ?></label>
                <input type="file" name="purchasePaymentAttachment" id="purchasePaymentAttachment" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="addPurchasePaymentsDescription"><?= __("Description:"); ?></label>
            <textarea name="addPurchasePaymentsDescription" id="addPurchasePaymentsDescription" rows="3" class="form-control"></textarea>
        </div>
        <input type="hidden" name="addPurchasePaymentsCompanyId" value="<?php echo safe_entities($_GET["cid"]); ?>">
        <input type="hidden" name="addPurchasePaymentsPurchaseId" value="<?php echo safe_entities($_GET["purchase_id"]); ?>">

        <div id="ajaxSubmitMsg"></div>

        <script>
            $(document).on("change", "#addPurchasePaymentsMethod", function() {
                if($("#addPurchasePaymentsMethod").val() == "Cheque") {
                    $("#hiddenItem").css("display", "block");
                } else {
                    $("#hiddenItem").css("display", "none");
                }
            });
        </script>

      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Add Payments");
    
}


/************************** submitPurchasePayments **********************/
if(isset($_GET['page']) and $_GET['page'] == "submitPurchasePayments") {

    
    if(empty($_POST["addPurchasePaymentsAmount"])) {
        return _e("Please enter payment amount");
    }

    $getPurchaseData = easySelectA(array(
        "table"     => "purchases",
        "fields"    => "purchase_grand_total, purchase_paid_amount, purchase_due",
        "where"     => array(
            "purchase_id"  => $_POST["addPurchasePaymentsPurchaseId"]
        )
    ))["data"][0];

    $selectPurchaseGrandTotal = $getPurchaseData["purchase_grand_total"];
    $totalPurchasePayments = $_POST["addPurchasePaymentsAmount"] + $getPurchaseData["purchase_paid_amount"];

    if( $selectPurchaseGrandTotal <= $getPurchaseData["purchase_paid_amount"] ) {
        return _e("The purchase already paid.");
    } else if( $_POST["addPurchasePaymentsAmount"] > $getPurchaseData["purchase_due"] ) {
        return _e("Paid amount can not be more then due amount.");
    }

    // Upload the attachment
    $paymentAttachment = NULL;
    if($_FILES["purchasePaymentAttachment"]["size"] > 0) {

        $paymentAttachment = easyUpload($_FILES["purchasePaymentAttachment"], "attachments/payments/cheque/" . date("M, Y"), $_POST["purchasePaymentChequeNo"] );

        if(!isset($paymentAttachment["success"])) {
            return _e($paymentAttachment);
        } else {
            $paymentAttachment = $paymentAttachment["fileName"];
        }
        
    }

    // Payment reference for BILL
    $paymentReferences = payment_reference("bill");

    // Insert the Bill Payment
    $insertPurchasePayment = easyInsert (
        "payments",
        array (
            "payment_date"              => $_POST["purchasePaymentDate"],
            "payment_to_company"        => $_POST["addPurchasePaymentsCompanyId"],
            "payment_purchase_id"       => $_POST["addPurchasePaymentsPurchaseId"],
            "payment_type"              => "Bill",
            "payment_status"            => "Complete",
            "payment_amount"            => $_POST["addPurchasePaymentsAmount"],
            "payment_from"              => $_SESSION["aid"],
            "payment_description"       => $_POST["addPurchasePaymentsDescription"],
            "payment_method"            => $_POST["addPurchasePaymentsMethod"],
            "payment_cheque_no"         => empty($_POST["purchasePaymentChequeNo"]) ? NULL : $_POST["purchasePaymentChequeNo"],
            "payment_cheque_date"       => empty($_POST["purchasePaymentChequeDate"]) ? NULL : $_POST["purchasePaymentChequeDate"],
            "payment_attachement"       => $paymentAttachment,
            "payment_reference"         => $paymentReferences,
            "payment_made_by"           => $_SESSION["uid"]
        ),
        array(),
        true
    );

    if(isset($insertPurchasePayment["status"]) and $insertPurchasePayment["status"] === "success" ) {


        $purchasePaymentStatus = "due";
        if($selectPurchaseGrandTotal <= $totalPurchasePayments) {

            $purchasePaymentStatus = "paid";

        } else if($selectPurchaseGrandTotal > $totalPurchasePayments and $totalPurchasePayments > 0) {

            $purchasePaymentStatus = "partial";

        }

        // Update Payments in Purchase
        easyUpdate(
            "purchases",
            array (
                "purchase_paid_amount"      => $totalPurchasePayments,
                "purchase_due"              => ( $totalPurchasePayments >= $selectPurchaseGrandTotal) ? 0 : ($selectPurchaseGrandTotal - $totalPurchasePayments ),
                "purchase_payment_status"   => $purchasePaymentStatus,

            ), 
            array (
                "purchase_id"  => $_POST["addPurchasePaymentsPurchaseId"]
            )
        );

        // Insert payment items
        easyInsert(
            "payment_items",
            array (
                "payment_items_payments_id" => $insertPurchasePayment["last_insert_id"],
                "payment_items_date"        => $_POST["purchasePaymentDate"],
                "payment_items_type"        => "Bill",
                "payment_items_description" => "",
                "payment_items_company"     => $_POST["addPurchasePaymentsCompanyId"],
                "payment_items_amount"      => $_POST["addPurchasePaymentsAmount"],
                "payment_items_accounts"    => $_SESSION["aid"],
                "payment_items_made_by"     => $_SESSION["uid"]
            )
        );
        
        // Update Accounts Balance
        updateAccountBalance($_SESSION["aid"]);

        $successMsg = sprintf(__("Payment successfully added. The reference number is: <strong>%s</strong>. Please <a %s>click here to print</a> the receipt."), $paymentReferences, "onClick='BMS.MAIN.printPage(this.href, event);' href='".full_website_address()."/invoice-print/?autoPrint=true&invoiceType=paymentReceipt&id={$insertPurchasePayment['last_insert_id']}'");
        
        echo "<div class='alert alert-success'>{$successMsg}</div>";

    }


}


/*************************** New Purchase ***********************/
if(isset($_GET['page']) and $_GET['page'] == "newPurchaseReturn") {

    //print_r($_FILES);

    //exit();

    // Check if the biller is set
    if(!isset($_SESSION["aid"])) {
        return _e("You must set you as a biller to make purchase return");
    }

    if( empty($_POST["purchaseCompany"]) ) {
        return _e("Please select company");
    } elseif( empty($_POST["purchaseWarehouse"]) ) {
        return _e("Please select warehouse");
    } elseif( count($_POST["productID"]) < 1 ) {
        return _e("Please select at least one product");
    }

    // Insert purchase data
    $purchaseData = $_POST;

    $productPurchasePrice = $purchaseData["productPurchasePrice"];
    $productDiscount = $purchaseData["productPurchaseDiscount"];
    $productQnt = $purchaseData["productQnt"];

    // Calculate the Product purchase Grand Total amount
    $PurchaseTotalAmount = 0;
    $purchaseTotalItemDiscount = 0;
    
    foreach($productPurchasePrice as $key => $value) {
        
        $value = empty($value) ? 0 : $value;
        $purchaseTotalItemDiscount += calculateDiscount($value, $productDiscount[$key]) * $productQnt[$key];
        
        $PurchaseTotalAmount += $productQnt[$key] * $value;

    }

    $totalPurchasePrice = $PurchaseTotalAmount - $purchaseTotalItemDiscount;

    $purchaseDiscount = calculateDiscount($totalPurchasePrice, $purchaseData["purchaseDiscountValue"]);

    $tariffCharges = array_sum($purchaseData["tariffChargesAmount"]);
    $shipping = empty($purchaseData["purchaseShipping"]) ? 0 : $purchaseData["purchaseShipping"];
    $grandTotal = ($totalPurchasePrice + $tariffCharges + $shipping) -  $purchaseDiscount;
    $paidAmount = empty($purchaseData["purchasePaidAmount"]) ? 0 : $purchaseData["purchasePaidAmount"];

    // Generate the payment status
    $salesPaymentStatus = "due";
    if($grandTotal <= $paidAmount) {

        $salesPaymentStatus = "paid";

    } else if($grandTotal > $paidAmount and $paidAmount > 0) {

        $salesPaymentStatus = "partial";

    }

    // Upload the image
    $purchaseBill = NULL;
    if($_FILES["purchaseBillAttachment"]["size"] > 0) {

        $billerCompanyName = easySelectA(array(
            "table"     => "companies",
            "fields"    => "concat(company_name, '_', company_id) AS company_name",
            "where"     => array(
                "company_id"    => $purchaseData["purchaseCompany"]
            )
        ))["data"][0]["company_name"];

        // generate the filename based on reference and date
        $fileName = empty($purchaseData["purchaseReference"]) ? $purchaseData["purchaseCompany"] ."_". time() : $purchaseData["purchaseCompany"] ."_". $purchaseData["purchaseReference"] . "_" . time();
        $purchaseBill = easyUpload($_FILES["purchaseBillAttachment"], "bills/companies/{$billerCompanyName}", $fileName);

        if(!isset($purchaseBill["success"])) {
            return _e($purchaseBill);
        } else {
            $purchaseBill = $purchaseBill["fileName"];
        }
        
    }

    
    // Insert data into product_purchase table
    $insertPurchaseReturn = easyInsert(
        "purchases",
        array (
            "purchase_date"                     => $purchaseData["purchaseDate"],
            "purchase_reference"                => empty($purchaseData["purchaseReference"]) ? NULL : $purchaseData["purchaseReference"],
            "purchase_company_id"               => $purchaseData["purchaseCompany"],
            "purchase_warehouse_id"             => $purchaseData["purchaseWarehouse"],
            "purchase_shop_id"                  => $_SESSION["sid"],
            "purchase_quantity"                 => array_sum($purchaseData["productQnt"]),
            "purchase_total_amount"             => $PurchaseTotalAmount,
            "purchase_product_discount"         => $purchaseTotalItemDiscount,
            "purchase_discount"                 => $purchaseDiscount,
            "purchase_tariff_charges"           => $tariffCharges,
            "purchase_tariff_charges_details"   => serialize($purchaseData["tariffChargesName"]),
            "purchase_shipping"                 => $shipping,
            "purchase_grand_total"              => $grandTotal,
            "purchase_paid_amount"              => $paidAmount,
            "purchase_change"                   => $grandTotal < $paidAmount ? $paidAmount - $grandTotal : 0,
            "purchase_due"                      => $grandTotal > $paidAmount ? $grandTotal - $paidAmount : 0,
            "purchase_payment_status"           => $salesPaymentStatus,
            "purchase_payment_method"           => $purchaseData["purchasePaymentMethod"],
            "purchase_total_item"               => count($purchaseData["productQnt"]),
            "purchase_note"                     => $purchaseData["purchaseDescription"],
            "purchase_attachments"              => $purchaseBill,
            "purchase_created_by"               => $_SESSION["uid"],
            "is_return"                         => 1
        ),
        array(),
        true
    );

    // check if the purchase successfully inserted then got to next for adding purchase item
    if( isset($insertPurchaseReturn["status"]) and $insertPurchaseReturn["status"] === "success") {
        
        foreach($purchaseData["productID"] as $key => $productId) {
            
            // Calculate the discount
            $productPurchaseDiscount = calculateDiscount($productPurchasePrice[$key], $productDiscount[$key]);

            // Calculate the amount after discount
            $itemAmoutnAfterDiscount = $productPurchasePrice[$key] - $productPurchaseDiscount;

            $insertPurchaseItem = easyInsert(
                "product_stock",
                array (
                    "stock_type"            => 'purchase-return',
                    "stock_entry_date"      => $purchaseData["purchaseDate"],
                    "stock_purchase_id"     => $insertPurchaseReturn["last_insert_id"],
                    "stock_shop_id"         => $_SESSION["sid"],
                    "stock_product_id"      => $productId,
                    "stock_batch_id"        => empty($purchaseData["productBatch"][$key]) ? NULL : $purchaseData["productBatch"][$key],
                    "stock_warehouse_id"    => $purchaseData["purchaseWarehouse"],
                    "stock_item_qty"        => $productQnt[$key],
                    "stock_item_price"      => $productPurchasePrice[$key],
                    "stock_item_discount"   => $productPurchaseDiscount,
                    "stock_item_subtotal"   => $productQnt[$key] * $itemAmoutnAfterDiscount, // Calculate the items total amount
                    "stock_created_by"      => $_SESSION["uid"]
                )
            );

            // Select products, which have sub products and insert sub/bundle products
            $subProducts = easySelectA(array(
                "table"     => "products as product",
                "fields"    => "bg_item_product_id, 
                                sub_product.product_purchase_price as purchase_price,
                                bg_product_qnt
                                ",
                "join"      => array(
                    "inner join {$table_prefeix}bg_product_items as bg_product on bg_product_id = product_id",
                    "left join {$table_prefeix}products as sub_product on sub_product.product_id = bg_item_product_id"
                ),
                "where"     => array(
                    "( product.has_sub_product = 1 or product.product_type = 'Bundle' ) and bg_product.is_raw_materials = 0 and product.product_id = {$productId}"
                )
            ));

            // Insert sub/ bundle products
            if($subProducts !== false) {

                $subProducts = $subProducts["data"];
                foreach($subProducts as $spKey => $sp) {
                
                    // Calculate the discount
                    $productPurchaseDiscount = calculateDiscount($sp["purchase_price"], $productDiscount[$key]);
        
                    // Calculate the amount after discount
                    $itemAmoutnAfterDiscount = $sp["purchase_price"] - $productPurchaseDiscount;

                    $totalSubProductQty = $productQnt[$key] * $sp["bg_product_qnt"];
        
                    $insertPurchaseItem = easyInsert(
                        "product_stock",
                        array (
                            "stock_type"            => 'purchase-return',
                            "stock_entry_date"      => $purchaseData["purchaseDate"],
                            "stock_purchase_id"     => $insertPurchaseReturn["last_insert_id"],
                            "stock_shop_id"         => $_SESSION["sid"],
                            "stock_product_id"      => $sp["bg_item_product_id"],
                            "stock_batch_id"        => NULL,
                            "stock_warehouse_id"    => $purchaseData["purchaseWarehouse"],
                            "stock_item_qty"        => $totalSubProductQty,
                            "stock_item_price"      => $sp["purchase_price"],
                            "stock_item_discount"   => $productPurchaseDiscount,
                            "stock_item_subtotal"   => $totalSubProductQty * $itemAmoutnAfterDiscount, // Calculate the items total amount
                            "stock_created_by"      => $_SESSION["uid"],
                            "is_bundle_item"        => 1
                        )
                    );
        
                }

            }

        }


        // if return paid amount grater then zero in product purchase return
        // then add to incoming return payment
        if($paidAmount > 0) {

            // Upload the purchase bill payment attachment, cheque etc
            $purchasePaymentAttachment = NULL;
            if($_FILES["purchasePaymentAttachment"]["size"] > 0) {

                $billerCompanyName = easySelectA(array(
                    "table"     => "companies",
                    "fields"    => "concat(company_name, '_', company_id) AS company_name",
                    "where"     => array(
                        "company_id"    => $purchaseData["purchaseCompany"]
                    )
                ))["data"][0]["company_name"];

                // generate the filename based on reference and date
                $fileName = empty($purchaseData["purchaseReference"]) ? $purchaseData["purchaseCompany"] ."_". time() : $purchaseData["purchaseCompany"] ."_". $purchaseData["purchaseReference"] . "_" . time();
                $purchasePaymentAttachment = easyUpload($_FILES["purchasePaymentAttachment"], "cheque/companies/{$billerCompanyName}", $fileName);

                if(!isset($purchasePaymentAttachment["success"])) {
                    return _e($purchasePaymentAttachment);
                } else {
                    $purchasePaymentAttachment = $purchasePaymentAttachment["fileName"];
                }
                
            }

            
            // Insert the outgoing return payment 
            $insertPurchaseReturnPayment = easyInsert (
                "payments_return",
                array (
                    "payments_return_type"              => "Incoming",
                    "payments_return_date"              => $purchaseData["purchaseDate"] . " " . date("H:i:s"),
                    "payments_return_company_id"        => $purchaseData["purchaseCompany"],
                    "payments_return_purchase_id"       => $insertPurchaseReturn["last_insert_id"],
                    "payments_return_accounts"          => $_SESSION["aid"],
                    "payments_return_amount"            => $paidAmount,
                    "payments_return_description"       => "Made on purchase return",
                    "payment_return_method"             => $purchaseData["purchasePaymentMethod"],
                    "payment_return_cheque_no"          => empty($purchaseData["purchasePaymentChequeNo"]) ? NULL : $purchaseData["purchasePaymentChequeNo"],
                    "payment_return_cheque_date"        => empty($purchaseData["purchasePaymentChequeDate"]) ? NULL : $purchaseData["purchasePaymentChequeDate"],
                    "payment_return_attachement"        => $purchasePaymentAttachment,
                    "payments_return_by"                => $_SESSION["uid"]
                )
            );

            // Update Accounts Balance
            updateAccountBalance($_SESSION["aid"]);

        }

        _s("Purchase return has been successfully added");

    } else {

        _e($insertPurchaseReturn);

    }

}


/*************************** Product Purchase Return List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "productPurchaseReturnList") {
    
    $requestData = $_REQUEST;
    $getData = [];
    
    // List of all columns name
    $columns = array(
        "",
        "purchase_add_on",
        "purchase_id",
        "company_name",
        "purchase_grand_total"
    );

    $shopId = "%";
    if( !is_super_admin() ) {
            $shopId = $_SESSION["sid"];
    }
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "purchases",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0 and is_return = 0 and purchase_shop_id like '{$shopId}'"
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }


    if(     !empty($requestData["search"]["value"]) or
            !empty($requestData["columns"][1]['search']['value']) or
            !empty($requestData["columns"][2]['search']['value']) or
            !empty($requestData["columns"][4]['search']['value']) or
            !empty($requestData["columns"][12]['search']['value']) 
        ) {  // get data with search

            $dateRange[0] = "";
            $dateRange[1] = "";
            $dateFilter = "";
            if(!empty($requestData["columns"][1]['search']['value'])) {
                $dateRange = explode(" - ", safe_input($requestData["columns"][1]['search']['value']));
                $dateFilter = "and purchase_date BETWEEN '{$dateRange[0]}' and '{$dateRange[1]}'";
            }
        
        $getData = easySelect(
                "purchases as product_purchase",
                "purchase_id, purchase_date, shop_name, purchase_note, purchase_payment_status, purchase_reference, purchase_company_id, company_name, round(purchase_total_amount, 2) as purchase_total_amount, 
                    round(purchase_product_discount, 2) as purchase_product_discount, round(purchase_discount, 2) as purchase_discount, round(purchase_shipping, 2) as purchase_shipping, 
                    round(purchase_grand_total, 2) as purchase_grand_total, round(purchase_paid_amount, 2) as purchase_paid_amount, round(purchase_due, 2) as purchase_due",
            array (
                "left join {$table_prefeix}companies on company_id = purchase_company_id",
                "left join {$table_prefeix}shops on shop_id = purchase_shop_id"
            ),
            array (
                "product_purchase.is_return = 1 and product_purchase.is_trash = 0 and (",
                " purchase_reference LIKE '". safe_input($requestData['search']['value']) ."%' ",
                " or company_name LIKE" => $requestData['search']['value'] . "%",
                ")",
                " AND purchase_shop_id" => $requestData["columns"][2]['search']['value'],
                " AND purchase_company_id" => $requestData["columns"][4]['search']['value'],
                " AND purchase_payment_status" => $requestData["columns"][12]['search']['value'],
                " AND purchase_shop_id like '{$shopId}' $dateFilter"
            ),
            array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        );

        $totalFilteredRecords = $getData ? $getData["count"] : 0;

    } else { // Get data withouth search

        $getData = easySelect(
            "purchases as product_purchase",
            "purchase_id, purchase_date, shop_name, purchase_note, purchase_payment_status, purchase_reference, purchase_company_id, company_name, round(purchase_total_amount, 2) as purchase_total_amount, 
            round(purchase_product_discount, 2) as purchase_product_discount, round(purchase_discount, 2) as purchase_discount, round(purchase_shipping, 2) as purchase_shipping, 
            round(purchase_grand_total, 2) as purchase_grand_total, round(purchase_paid_amount, 2) as purchase_paid_amount, round(purchase_due, 2) as purchase_due",
            array (
            "left join {$table_prefeix}companies on company_id = purchase_company_id",
            "left join {$table_prefeix}shops on shop_id = purchase_shop_id"
            ),
            array("product_purchase.is_trash = 0 and product_purchase.is_return = 1 and purchase_shop_id like '{$shopId}'"),
            array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        );

    }

    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['count']) and $getData['count'] > 0) {
        
        foreach($getData['data'] as $key => $value) {

            $paymentStatus = "";
            if($value["purchase_payment_status"] === "paid") {
                $paymentStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-success'>Paid</span>";
            } else if($value["purchase_payment_status"] === "partial") {
                $paymentStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-warning'>Partial</span>";
            } else {
                $paymentStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-danger'>Due</span>";
            }

            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["purchase_date"];
            $allNestedData[] = $value["shop_name"];
            $allNestedData[] = $value["purchase_reference"];
            $allNestedData[] = $value["company_name"];
            $allNestedData[] = $value["purchase_total_amount"];
            $allNestedData[] = $value["purchase_product_discount"] + $value["purchase_discount"];
            $allNestedData[] = $value["purchase_shipping"];
            $allNestedData[] = $value["purchase_grand_total"];
            $allNestedData[] = $value["purchase_paid_amount"];
            $allNestedData[] = $value["purchase_due"];
            $allNestedData[] = $value["purchase_grand_total"] - $value["purchase_due"];
            $allNestedData[] = $value["purchase_note"];
            $allNestedData[] = $paymentStatus;
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?module=stock-management&page=viewPurchasedProduct&id='. $value["purchase_id"] .'"  data-target="#modalDefault"><i class="fa fa-eye"></i> View Products</a></li>
                                        <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=stock-management&page=deletePurchasedProduct" data-to-be-deleted="'. $value["purchase_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                    </ul>
                                </div>';
            
            $allData[] = $allNestedData;
        }
    }
    

    $jsonData = array (
        "draw"              => intval( $requestData['draw'] ),
        "recordsTotal"      => intval( $totalRecords ),
        "recordsFiltered"   => intval( $totalFilteredRecords ),
        "data"              => $allData
    );
    
    // Encode in Json Formate
    echo json_encode($jsonData); 

}


/***************** Delete Purchase ****************/
if(isset($_GET['page']) and $_GET['page'] == "deletePurchasedProduct") {

  $deleteData = easyDelete(
      "purchases",
      array(
          "purchase_id" => $_POST["datatoDelete"]
      )
  );

  if($deleteData === true) {

    // Delete Payments if there any payment payments in this purchase
    easyDelete(
        "payments",
        array(
            "payment_purchase_id" => $_POST["datatoDelete"]
        )
    );

    // Delete Payment returns, If there have any return in this purchase return;
    easyDelete(
        "payments_return",
        array(
            "payments_return_purchase_id"   => $_POST["datatoDelete"]
        )
    );


    echo '{
        "title": "'. __("The purchase has been successfully deleted.") .'",
        "icon": "success"
    }';
    
  } 

}

/************************** Purchased Product **********************/
if(isset($_GET['page']) and $_GET['page'] == "viewPurchasedProduct") {
  

  // Select Purchased Item
  $selectPurchasedItems = easySelect(
      "product_stock",
      "stock_product_id, product_name, product_unit, stock_item_qty",
      array (
        "left join {$table_prefeix}products on product_id = stock_product_id"
      ),
      array (
          "is_bundle_item = 0 and stock_purchase_id" => $_GET["id"]
      )
  );


  ?>

  <div class="modal-header">
      <h4 class="modal-title"><?= __("Purchased Items"); ?></h4>
  </div>

  <div class="modal-body">

    <table class="table table-striped table-condensed">
      <tbody>
        <tr>
            <td><?= __("Products"); ?></td>
            <td><?= __("Quantity"); ?></td>
            <td><?= __("Unit"); ?></td>
        </tr>

      <?php 

          foreach($selectPurchasedItems["data"] as $key => $purcahedItem) {

            echo "<tr>";
            echo " <td>{$purcahedItem['product_name']}</td>";
            echo " <td>{$purcahedItem['stock_item_qty']}</td>";
            echo " <td>{$purcahedItem['product_unit']}</td>";
            echo "</tr>";

          }

      ?>     

      </tbody>

  </table>
      
  </div> <!-- /.modal-body -->

  <?php

}

/***************** Sale Return ****************/
if(isset($_GET['page']) and $_GET['page'] == "newReturn") {

    //print_r($_POST);

    //exit();


    // Check if the biller is set
    if(!isset($_SESSION["aid"])) {
        return _e("You must set you as a biller to make return");
    } elseif( empty($_POST["returnCustomer"]) ) {
        return _e("Please select customer");
    } elseif( empty($_POST["returnWarehouse"]) ) {
        return _e("Please select warehouse");
    } elseif( !isset($_POST["productID"])) {
        return _e("Please select at least one product");
    }


    $selectReturnReference = easySelect(
        "sales",
        "sales_reference",
        array(),
        array (
            "is_return = 1",
            " AND sales_reference LIKE 'RETURN/{$_SESSION['sid']}{$_SESSION['uid']}/%'",
            " AND sales_reference is not null"
        ),
        array (
            "sales_id" => "DESC"
        ),
        array (
            "start" => 0,
            "length" => 1
        )
    );
    

    // Referense Format: RETURN/n
    $returnReferences = "RETURN/".$_SESSION['sid'].$_SESSION['uid']."/";

    // check if there is minimum one records
    if($selectReturnReference) {
        $getLastReferenceNo = explode($returnReferences, $selectReturnReference["data"][0]["sales_reference"])[1];
        $returnReferences = $returnReferences . ((int)$getLastReferenceNo+1);
    } else {
        $returnReferences = "RETURN/".$_SESSION['sid'].$_SESSION['uid']."/1";
    }

    $getData = $_POST; 
    $returnPaidAmount = empty($getData["returnPaidAmount"]) ? 0 : $getData["returnPaidAmount"];

    $surcharge = empty($_POST["returnSurcharge"]) ? 0 : $_POST["returnSurcharge"];
    $shipping = empty($_POST["returnShipping"]) ? 0 : $_POST["returnShipping"];

    // Insert Sales into db
    $insertSalesReturn = easyInsert(
        "sales",
        array (
            "sales_delivery_date"           => $_POST["salesReturnDate"],
            "sales_reference"               => $returnReferences,
            "sales_customer_id"             => $_POST["returnCustomer"],
            "sales_warehouse_id"            => $_POST["returnWarehouse"],
            "sales_shop_id"                 => $_SESSION["sid"],
            "sales_quantity"                => array_sum($_POST["productQnt"]),
            "sales_shipping"                => $shipping,
            "sales_surcharge"               => $surcharge,
            "sales_paid_amount"             => empty($_POST["returnPaidAmount"]) ? 0 : $_POST["returnPaidAmount"],
            "sales_payment_method"          => $_POST["returnPaymentMethod"],
            "sales_created_by"              => $_SESSION["uid"],
            "sales_total_item"              => count($getData["productID"]),
            "sales_total_packets"           => 0,
            "sales_tariff_charges_details"  => $getData["tariffChargesName"] ? serialize($getData["tariffChargesName"]) : "",
            "sales_by_pos"                  => 0,
            "sales_note"                    => $_POST["returnDescription"],
            "is_return"                     => 1
        ),
        array(),
        true
    );

    // check if the purchase successfully inserted then got to next for adding purchase item
    if( isset($insertSalesReturn["status"]) and $insertSalesReturn["status"] === "success") {

        $salesTotalAmount = 0;
        $salesTotalProductDiscount = 0;
        $salesTotalOrderDiscount = 0;
        $salesGrandTotal = 0;
        $salesChanges = 0;

        $insertSaleReturnItems = "INSERT INTO {$table_prefeix}product_stock(
            stock_type,
            stock_entry_date,
            stock_sales_id,
            stock_warehouse_id,
            stock_shop_id,
            stock_product_id,
            stock_batch_id,
            stock_item_price,
            stock_item_qty,
            stock_item_discount,
            stock_item_subtotal,
            stock_created_by,
            is_bundle_item
        ) VALUES ";
        
        // Insert product items into sale table
        foreach($getData["productID"] as $key => $productId) {

            // Calculate the total amount
            $salesTotalAmount += $getData["productReturnPrice"][$key] * $getData["productQnt"][$key];

            // Calculate the product/items Discount
            $itemDiscountAmount = calculateDiscount($getData["productReturnPrice"][$key], $getData["productReturnDiscount"][$key]);

            // Calculate the total product/items Discount
            $salesTotalProductDiscount += $itemDiscountAmount * $getData["productQnt"][$key];

            // Calculate item amount after discount
            $itemAmoutnAfterDiscount = $getData["productReturnPrice"][$key] - $itemDiscountAmount;

            $salesItemSubTotal = $getData["productQnt"][$key] * $itemAmoutnAfterDiscount;

            $insertSaleReturnItems .= "
            (
                'sale-return',
                '". safe_input($_POST["salesReturnDate"]) ."',
                '{$insertSalesReturn["last_insert_id"]}',
                '". safe_input($_POST["returnWarehouse"]) ."',
                '". $_SESSION["sid"] ."',
                '". safe_input($productId) ."',
                ". ( empty($getData["productBatch"][$key]) ? "NULL" : "'". safe_input($getData["productBatch"][$key]) . "'" ) .",
                '". safe_input($getData["productReturnPrice"][$key]) ."',
                '". safe_input($getData["productQnt"][$key]) ."',
                '". $itemDiscountAmount ."',
                '". $salesItemSubTotal ."',
                '". $_SESSION["uid"] ."',
                '". 0 ."'
            ),";

            // Select products, which have sub products and insert sub/bundle products
            $subProducts = easySelectA(array(
                "table"     => "products as product",
                "fields"    => "bg_item_product_id, 
                                bg_product_price as sale_price,
                                bg_product_qnt
                                ",
                "join"      => array(
                    "inner join {$table_prefeix}bg_product_items as bg_product on bg_product_id = product_id"
                ),
                "where"     => array(
                    "( product.has_sub_product = 1 or product.product_type = 'Bundle' ) and bg_product.is_raw_materials = 0 and product.product_id = {$productId}"
                )
            ));
            

            // Insert sub/ bundle products
            if($subProducts !== false) {

                // check if the bundle product sale price is changed by user
                $increasedRate = "0%";
                $decreasedRate = "0%";
                if( $getData["productReturnPrice"][$key] > $getData["productReturnMainPrice"][$key] ) { // If the price is Increased 

                    // Calculate the increased amount
                    $increasedAmount = $getData["productReturnPrice"][$key] - $getData["productReturnMainPrice"][$key];
                    
                    // Calculate the increased purcentage
                    $increasedRate = ( $increasedAmount * 100 ) / $getData["productReturnMainPrice"][$key] ;

                } else if( $getData["productReturnPrice"][$key] < $getData["productReturnMainPrice"][$key] ) { // If the price is decrased

                    // Calculate the decreased amount 
                    $decreasedAmount = $getData["productReturnMainPrice"][$key] - $getData["productReturnPrice"][$key];
                    
                    // Calculate the decreased purcentage
                    $decreasedRate = ( $decreasedAmount * 100 ) / $getData["productReturnMainPrice"][$key] ;

                }


                foreach($subProducts["data"] as $bpKey => $bp) {

                    // Store the Bundle/ Sub Product Item Sale Price
                    $bpItemSalePrice = $bp["sale_price"];

                    // Check if increased is not 0%
                    if( $increasedRate != "0%" ) {

                        // Increase the price if it was increased in Bundle price by user
                        $bpItemSalePrice += calculateDiscount($bpItemSalePrice, $increasedRate . "%");

                    } else if( $decreasedRate != "0%" ) {

                        // Decreased the price if it was increased in Bundle price by user
                        $bpItemSalePrice -= calculateDiscount($bpItemSalePrice, $decreasedRate  . "%");

                    } 


                    // Calculate the Bundle/Sub item quantity
                    $bpItemQnt = $getData["productQnt"][$key] * $bp["bg_product_qnt"];

                    // In bundle/Sub item, the discount takes from bundle product not from the item product
                    $bpItemDiscountAmount = calculateDiscount( $bpItemSalePrice, $getData["productReturnDiscount"][$key] );

                    $bpItemSubTotal = ( $bpItemSalePrice - $bpItemDiscountAmount) * $bpItemQnt;

                    $insertSaleReturnItems .= "
                    (
                        'sale-return',
                        '". safe_input($_POST["salesReturnDate"]) ."',
                        '{$insertSalesReturn["last_insert_id"]}',
                        '". safe_input($_POST["returnWarehouse"]) ."',
                        '". $_SESSION["sid"] ."',
                        '". $bp["bg_item_product_id"] ."',
                        NULL,
                        '". $bpItemSalePrice ."',
                        '". $bpItemQnt ."',
                        '". $bpItemDiscountAmount ."',
                        '". $bpItemSubTotal ."',
                        '". $_SESSION["uid"] ."',
                        '". 1 ."'
                    ),";
                    
                }
            }
        
        }


        // Calculate subtotal by minusing product discount
        $subtotal = $salesTotalAmount - $salesTotalProductDiscount;

        // Calculate order discount from subtotal
        $salesOrderDiscount = calculateDiscount($subtotal, $getData["returnDiscountValue"]);

        // Calculate total amount after discount
        $salesAmoutnAfterDiscount = $subtotal - $salesOrderDiscount; 

        // Calculate Sales Tariff
        $tariffCharges =  array_sum($getData["tariffChargesAmount"]);

        // Calculate Net total (Amount after discount + Tax)
        $netTotal = $salesAmoutnAfterDiscount + $tariffCharges; 

        // Calculate Grand total by Adding shiping charge with net total
        $salesGrandTotal = ( $netTotal + $shipping) - $surcharge;

        // Calculate Change amount
        $salesChanges = ($salesGrandTotal < $returnPaidAmount) ? ($returnPaidAmount - $salesGrandTotal) : 0;

        // Calculate Due amount
        $salesDue = ($salesGrandTotal > $returnPaidAmount) ? ($salesGrandTotal - $returnPaidAmount) : 0;

        // Generate the payment status
        $salesPaymentStatus = "due";
        if($salesGrandTotal <= $returnPaidAmount) {

            $salesPaymentStatus = "paid";

        } else if($salesGrandTotal > $returnPaidAmount and $returnPaidAmount > 0) {

            $salesPaymentStatus = "partial";

        }

        // Update the Return
        easyUpdate(
            "sales",
            array (
                "sales_total_amount"            => $salesTotalAmount,
                "sales_product_discount"        => $salesTotalProductDiscount,
                "sales_discount"                => $salesOrderDiscount,
                "sales_tariff_charges"          => $tariffCharges,
                "sales_grand_total"             => $salesGrandTotal,
                "sales_paid_amount"             => $returnPaidAmount,
                "sales_change"                  => $salesChanges,
                "sales_due"                     => $salesDue,
                "sales_status"                  => "Delivered",
                "sales_payment_status"          => $salesPaymentStatus
            ),
            array (
                "sales_id"  => $insertSalesReturn["last_insert_id"]
            )
        );

        // Insert sale return items
        $conn->query(substr_replace($insertSaleReturnItems, ";", -1, 1));


        // If there have return amount to customer then add into payments_return table as ourgoing
        if( $returnPaidAmount > 0 ) {

            easyInsert(
                "payments_return",
                array(
                    "payments_return_type"          => "Outgoing",
                    "payments_return_date"          => $_POST["salesReturnDate"] . date(" H:i:s"),
                    "payments_return_accounts"      => $_SESSION["aid"],
                    "payments_return_customer_id"   => $_POST["returnCustomer"],
                    "payments_return_sales_id"      => $insertSalesReturn["last_insert_id"],
                    "payments_return_amount"        => $salesGrandTotal - $salesDue,
                    "payments_return_description"   => "Return payment made on product return",
                    "payments_return_by"            => $_SESSION["uid"]
                )
            );
        
        }

        /**
         * If there have ammount to return and the customer want to keep it as balance
         * Then, add the amount as received payment. 
         * 
         * In this situation the salesDue treated as return
         * 
         * *****************************************************
         * This fearus ultimately not required. Now Commenting. Will check later.
         * *******************************************************
         * 
         */
        /*
        if( $salesDue > 0 ) {
            easyInsert(
                "received_payments",
                array (
                    "received_payments_type"        => "Received Payments",
                    "received_payments_datetime"    => $_POST["salesReturnDate"] . date(" H:i:s"),
                    "received_payments_shop"        => $_SESSION["sid"],
                    "received_payments_accounts"    => $_SESSION["aid"],
                    "received_payments_sales_id"    => $insertSalesReturn["last_insert_id"],
                    "received_payments_from"        => $_POST["returnCustomer"],
                    "received_payments_amount"      => $salesDue,
                    "received_payments_method"      => $_POST["returnPaymentMethod"],
                    "received_payments_details"     => "Added as balance from return",
                    "received_payments_add_by"      => $_SESSION["uid"]
                )
            );
        } */

        _s("Return has been successfully added");

    } else {

        _e($insertSalesReturn);

    }

}


/*************************** new Stock Transfer ***********************/
if(isset($_GET['page']) and $_GET['page'] == "newStockTransfer") {

    //print_r($_POST);
    //exit();
    
    if( empty($_POST["stockTransferFromWarehouseId"]) ) {
        return _e("Please select from warehouse");
    } elseif( empty($_POST["stockTransferToWarehouseId"]) ) {
        return _e("Please select to warehouse");
    } elseif( !isset($_POST["productID"]) ) {
        return _e("Please select at least one product");
    }


    $transferData = $_POST;

    // Insert Transfer
    $insertTransfer = easyInsert(
        "stock_transfer",
        array(
            "stock_transfer_date"                   => $transferData["transferDate"],
            "stock_transfer_status"                 => get_options("autoConfirmStockTransfer") === "Yes" ? "Confirmed" : "Awaiting Confirmation", // This will be automat later, such confirm and rejected by user
            "stock_transfer_reference"              => $transferData["stockTransferReference"],
            "stock_transfer_from_warehouse"         => $transferData["stockTransferFromWarehouseId"],
            "stock_transfer_to_warehouse"           => $transferData["stockTransferToWarehouseId"],
            "stock_transfer_remarks"                => $transferData["stockTransferDescription"]
        ),
        array(),
        true
    );

    $stockTransferTotalAmount = 0;
    $stockTransferItemDiscount = 0;

    if($insertTransfer["status"] === "success") {

        $insertStockTransferItems = "INSERT INTO {$table_prefeix}product_stock(
            stock_type,
            stock_entry_date,
            stock_transfer_id,
            stock_warehouse_id,
            stock_shop_id,
            stock_product_id,
            stock_batch_id,
            stock_item_price,
            stock_item_qty,
            stock_item_discount,
            stock_item_subtotal,
            stock_created_by,
            is_bundle_item
        ) VALUES ";

        foreach( $transferData["productID"] as $key => $productId ) {

            $transferItemDiscount = calculateDiscount($transferData["productPurchasePrice"][$key], $transferData["productPurchaseDiscount"][$key]);
            $totalItemPrice = $transferData["productQnt"][$key] * $transferData["productPurchasePrice"][$key];
            
            $stockTransferItemDiscount += $transferItemDiscount * $transferData["productQnt"][$key];
            $stockTransferTotalAmount += $totalItemPrice;


            /** Generate Stock out product */
            $insertStockTransferItems .= "
            (
                'transfer-out',
                '". safe_input($transferData["transferDate"]) ."',
                '{$insertTransfer["last_insert_id"]}',
                '". safe_input($transferData["stockTransferFromWarehouseId"]) ."',
                '". $_SESSION["sid"] ."',
                '". safe_input($productId) ."',
                ". ( empty($getData["productBatch"][$key]) ? "NULL" : "'". safe_input($getData["productBatch"][$key]) . "'" ) .",
                '". safe_input($transferData["productPurchasePrice"][$key]) ."',
                '{$transferData["productQnt"][$key]}',
                '". $transferItemDiscount ."',
                '". $totalItemPrice ."',
                '". $_SESSION["uid"] ."',
                '". 0 ."'
            ),";

            /** Generate Stock in product */
            $insertStockTransferItems .= "
            (
                '". ( get_options("autoConfirmStockTransfer") === "Yes" ? "transfer-in" : "undeclared" ) ."',
                '". safe_input($transferData["transferDate"]) ."',
                '{$insertTransfer["last_insert_id"]}',
                '". safe_input($transferData["stockTransferToWarehouseId"]) ."',
                '". $_SESSION["sid"] ."',
                '". safe_input($productId) ."',
                ". ( empty($getData["productBatch"][$key]) ? "NULL" : "'". safe_input($getData["productBatch"][$key]) . "'" ) .",
                '". safe_input($transferData["productPurchasePrice"][$key]) ."',
                '{$transferData["productQnt"][$key]}',
                '". $transferItemDiscount ."',
                '". $totalItemPrice ."',
                '". $_SESSION["uid"] ."',
                '". 0 ."'
            ),";


            // Select products, which have sub products and insert sub/bundle products
            $subProducts = easySelectA(array(
                "table"     => "products as product",
                "fields"    => "bg_item_product_id, 
                                sub_product.product_purchase_price as purchase_price,
                                bg_product_qnt
                                ",
                "join"      => array(
                    "inner join {$table_prefeix}bg_product_items as bg_product on bg_product_id = product_id",
                    "left join {$table_prefeix}products as sub_product on sub_product.product_id = bg_item_product_id"
                ),
                "where"     => array(
                    "( product.has_sub_product = 1 or product.product_type = 'Bundle' ) and bg_product.is_raw_materials = 0 and product.product_id = {$productId}"
                )
            ));

            // Insert sub/ bundle products
            if($subProducts !== false) {

                foreach($subProducts["data"] as $spKey => $sp) {

                    $transferItemDiscount = calculateDiscount($sp["purchase_price"], $transferData["productPurchaseDiscount"][$key]);
                    $totalSubProductQty = $transferData["productQnt"][$key] * $sp["bg_product_qnt"];
                
                    /** Generate Stock out product for sub/bundle products */
                    $insertStockTransferItems .= "
                    (
                        'transfer-out',
                        '". safe_input($transferData["transferDate"]) ."',
                        '{$insertTransfer["last_insert_id"]}',
                        '". safe_input($transferData["stockTransferFromWarehouseId"]) ."',
                        '". $_SESSION["sid"] ."',
                        '". $sp["bg_item_product_id"] ."',
                        NULL,
                        '". $sp["purchase_price"] ."',
                        '{$totalSubProductQty}',
                        '". $transferItemDiscount ."',
                        '". $totalSubProductQty * $transferItemDiscount ."',
                        '". $_SESSION["uid"] ."',
                        '". 1 ."'
                    ),";

                    /** Generate Stock in product for sub/bundle products */
                    $insertStockTransferItems .= "
                    (
                        '". ( get_options("autoConfirmStockTransfer") === "Yes" ? "transfer-in" : "undeclared" ) ."',
                        '". safe_input($transferData["transferDate"]) ."',
                        '{$insertTransfer["last_insert_id"]}',
                        '". safe_input($transferData["stockTransferToWarehouseId"]) ."',
                        '". $_SESSION["sid"] ."',
                        '". $sp["bg_item_product_id"] ."',
                        NULL,
                        '". $sp["purchase_price"] ."',
                        '{$totalSubProductQty}',
                        '". $transferItemDiscount ."',
                        '". $totalSubProductQty * $transferItemDiscount ."',
                        '". $_SESSION["uid"] ."',
                        '". 1 ."'
                    ),";
        
                }

            }

        }

        // Update the trasfer table
        easyUpdate(
            "stock_transfer",
            array(
                "stock_transfer_total_amount"           => $stockTransferTotalAmount,
                "stock_transfer_item_total_discount"    => $stockTransferItemDiscount,
                "stock_transfer_grand_total"            => $stockTransferTotalAmount - $stockTransferItemDiscount,
            ),
            array(
                "stock_transfer_id"     => $insertTransfer["last_insert_id"]
            )
        );

        // Insert stock transfer item
        runQuery(substr_replace($insertStockTransferItems, ";", -1, 1));

        _s("Transfer has been successfully added");

    } else {

        _e($insertTransfer);

    }

}


/*************************** stockTransferList ***********************/
if(isset($_GET['page']) and $_GET['page'] == "stockTransferList") {
    
    $requestData = $_REQUEST;
    $getData = [];
    
    // List of all columns name
    $columns = array(
        "",
        "stock_transfer_add_on",
        "stock_transfer_reference",
        "warehouseFromName",
        "warehouseToName",
        "stock_transfer_grand_total",
        "stock_transfer_remarks"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "stock_transfer",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }

    if(!empty($requestData["search"]["value"])) {  // get data with search
        

        $getData = easySelectA(array(
            "table"   => "stock_transfer as stock_transfer",
            "fields"  => "stock_transfer_id, stock_transfer_status, stock_transfer_date, combine_description(stock_transfer_id, stock_transfer_reference) as stock_transfer_reference, 
                        stock_transfer_reject_note, stock_transfer_grand_total, stock_transfer_remarks, warehouseFrom.warehouse_name as warehouseFromName, 
                        stock_transfer_to_warehouse, warehouseTo.warehouse_name as warehouseToName",
            "join"    => array(
            "left join {$table_prefeix}warehouses as warehouseFrom on warehouseFrom.warehouse_id = stock_transfer_from_warehouse",
            "left join {$table_prefeix}warehouses as warehouseTo on warehouseTo.warehouse_id = stock_transfer_to_warehouse"
            ),
            "where" => array (
            "stock_transfer.is_trash = 0 and stock_transfer_reference LIKE" => $requestData['search']['value'] . "%",
            " OR warehouseFrom.warehouse_name LIKE" => $requestData['search']['value'] . "%"
            ),
            "orderby" => array(
            $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit" => array (
            "start" => $requestData['start'],
            "length" => $requestData['length']
            )
        ));

        $totalFilteredRecords = $getData ? $getData["count"] : 0;

    } else { // Get data withouth search

        $getData = easySelectA(array(
        "table"   => "stock_transfer as stock_transfer",
        "fields"  => "stock_transfer_id, stock_transfer_status, stock_transfer_date, combine_description(stock_transfer_id, stock_transfer_reference) as stock_transfer_reference, 
                    stock_transfer_reject_note, stock_transfer_grand_total, stock_transfer_remarks, warehouseFrom.warehouse_name as warehouseFromName, 
                    stock_transfer_to_warehouse, warehouseTo.warehouse_name as warehouseToName",
        "join"    => array(
            "left join {$table_prefeix}warehouses as warehouseFrom on warehouseFrom.warehouse_id = stock_transfer_from_warehouse",
            "left join {$table_prefeix}warehouses as warehouseTo on warehouseTo.warehouse_id = stock_transfer_to_warehouse"
        ),
        "where" => array(
            "stock_transfer.is_trash = 0"
        ),
        "orderby" => array(
            $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
        ),
        "limit" => array (
            "start" => $requestData['start'],
            "length" => $requestData['length']
        )
        ));

    }

    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['count']) and $getData['count'] > 0) {
        
        foreach($getData['data'] as $key => $value) {

            $transferStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-warning'>Awaiting Confirmation</span>";
            $confirmStockStransfer = "";
            $rejectNote = "";

            if($value["stock_transfer_status"] === "Confirmed" ) {
                $transferStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-success'>Confirmed</span>";
            } else if($value["stock_transfer_status"] === "Rejected" ) {
                $transferStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-danger'>Rejected</span>";

                if( !empty($value["stock_transfer_reject_note"]) ) {
                    $rejectNote = "<b>Reject Note: </b>" . $value["stock_transfer_reject_note"];
                }
                
            }

            /**
             * Supper admin or the warehouse where transfer in the stock can confirm or reject the transfer
             */
            if( $value["stock_transfer_status"] !== "Confirmed" and ( $_SESSION["wid"] === $value["stock_transfer_to_warehouse"] or is_super_admin() ) ) {
                $confirmStockStransfer = '<li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?module=stock-management&page=confirmRejectStockTransfer&id='. $value["stock_transfer_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Confirm/ Reject</a></li>';
            }

            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["stock_transfer_date"];
            $allNestedData[] = "Stock_Transfer/".$value["stock_transfer_reference"];
            $allNestedData[] = $value["warehouseFromName"];
            $allNestedData[] = $value["warehouseToName"];
            $allNestedData[] = $value["stock_transfer_grand_total"];
            $allNestedData[] = $value["stock_transfer_remarks"] . $rejectNote;
            $allNestedData[] = $transferStatus;
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        '. $confirmStockStransfer .'
                                        <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?module=stock-management&page=viewTransferedProduct&id='. $value["stock_transfer_id"] .'"  data-target="#modalDefault"><i class="fa fa-eye"></i> View Items</a></li>
                                        <li><a target="_blank" href="'. full_website_address() .'/invoice-print/?autoPrint=true&invoiceType=stockTransferVoucher&id='. $value["stock_transfer_id"] .'"><i class="fa fa-print"></i>Print Voucher</a></li>
                                        <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=stock-management&page=deleteStockTransfer" data-to-be-deleted="'. $value["stock_transfer_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                    </ul>
                                </div>';
            
            $allData[] = $allNestedData;
        }
    }
    

    $jsonData = array (
        "draw"              => intval( $requestData['draw'] ),
        "recordsTotal"      => intval( $totalRecords ),
        "recordsFiltered"   => intval( $totalFilteredRecords ),
        "data"              => $allData
    );
    
    // Encode in Json Formate
    echo json_encode($jsonData); 

}

/***************** Delete Stock Transfer ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteStockTransfer") {
  
  $deleteData = easyDelete(
      "stock_transfer",
      array(
          "stock_transfer_id" => $_POST["datatoDelete"]
      )
  );

  if($deleteData === true) {
    echo '{
        "title": "'. __("Stock transfer has been successfully deleted.") .'",
        "icon": "success"
    }';
  } 

}

/************************** Purchased Product **********************/
if(isset($_GET['page']) and $_GET['page'] == "viewTransferedProduct") {

  // Select Purchased Item
  $selectTransferedItems = easySelectA(array(
        "table"   => "product_stock",
        "fields"  => "round(stock_item_qty, 2) as stock_item_qty, 
                        round(stock_item_discount, 2) as stock_item_discount, product_unit, round(stock_item_subtotal, 2) as stock_item_subtotal, product_name, if(batch_number is null, '', concat('(', batch_number, ')') ) as batch_number",
        "join"    => array(
        "left join {$table_prefeix}products on stock_product_id = product_id",
        "left join {$table_prefeix}product_batches as product_batches on stock_product_id = product_batches.product_id and stock_batch_id = batch_id"
        ),
        "where" => array(
        "is_bundle_item = 0 and stock_type = 'transfer-out' and stock_transfer_id"  => $_GET["id"],
        )
    ));


  ?>

  <div class="modal-header">
      <h4 class="modal-title"><?= __("Stock Transfered Items"); ?></h4>
  </div>

  <div class="modal-body">

    <table class="table table-striped table-condensed">
      <tbody>
        <tr>
            <td><?= __("Products"); ?></td>
            <td><?= __("Quantity"); ?></td>
            <td><?= __("Unit"); ?></td>
        </tr>

      <?php 

          foreach($selectTransferedItems["data"] as $key => $transferedItem) {

          echo "<tr>";
          echo " <td>{$transferedItem['product_name']}</td>";
          echo " <td>{$transferedItem['stock_item_qty']}</td>";
          echo " <td>{$transferedItem['product_unit']}</td>";
          echo "</tr>";

          }

      ?>     

      </tbody>

  </table>
      
  </div> <!-- /.modal-body -->

  <?php

}


/************************** Add new Batch **********************/
if(isset($_GET['page']) and $_GET['page'] == "confirmRejectStockTransfer") {

    // Include the modal header
    modal_header("Change Stock Transfer Status", full_website_address() . "/xhr/?module=stock-management&page=updateStockTransferStatus");
    
    ?>
      <div class="box-body">
        
        <div class="form-group required">
            <label for="stockTransferStatus"><?= __("Product:"); ?></label>
            <select name="stockTransferStatus" id="stockTransferStatus" class="form-control" required>
                <option value=""><?= __("Status"); ?>...</option>
                <option value="Confirmed">Confirm</option>
                <option value="Rejected">Reject</option>
            </select>
        </div>
        <div class="form-group">
            <label for="stockTransferStatusNote"><?= __("Note:"); ?></label>
            <textarea name="stockTransferStatusNote" id="stockTransferStatusNote" rows="3" class="form-control"></textarea>
        </div>
        <input type="hidden" name="stockTransferId" value="<?php echo safe_entities($_GET["id"]); ?>">
              
      </div>
      <!-- /Box body-->
  
    <?php
  
    // Include the modal footer
    modal_footer();
  
}


// Add new Warehouse
if(isset($_GET['page']) and $_GET['page'] == "updateStockTransferStatus") {

    if(empty($_POST["stockTransferStatus"])) {
        return _e("Please select status.");
    }
    
    $returnMsg = easyUpdate(
        "stock_transfer", // Table name
        array( // Fileds Name and value
            "stock_transfer_status"         => $_POST["stockTransferStatus"],
            "stock_transfer_reject_note"    => $_POST["stockTransferStatusNote"]
        ),
        array( // No duplicate allow.
            "stock_transfer_id"   => $_POST["stockTransferId"]
        )
    );

    if($returnMsg === true) {

        _s("Status updated successfully.");


        // if ocnfirm the stock transfer then change product stock status undeclared to transfer_in
        if( $_POST["stockTransferStatus"] === "Confirmed" ) {

            easyUpdate(
                "product_stock",
                array(
                    "stock_type" => "transfer-in"
                ),
                array(
                    "stock_type = 'undeclared' and stock_transfer_id" => $_POST["stockTransferId"]
                )
            );

        } else {

            // if reject then change product stock status to undeclared
            easyUpdate(
                "product_stock",
                array(
                    "stock_type" => "undeclared"
                ),
                array(
                    "stock_transfer_id" => $_POST["stockTransferId"]
                )
            );

        }



    } else {
        _e($returnMsg);
    }

}


/************************** Add new Warehouse **********************/
if(isset($_GET['page']) and $_GET['page'] == "newWarehouse") {

  // Include the modal header
  modal_header("New Warehouse", full_website_address() . "/xhr/?module=stock-management&page=addNewWarehouse");
  
  ?>
    <div class="box-body">
      
        <div class="form-group required">
            <label for="warehouseName"><?= __("Warehouse Name:"); ?></label>
            <input type="text" name="warehouseName" id="warehouseName" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="warehouseShopId"><?= __("Shop:"); ?> </label>
            <i data-toggle="tooltip" data-placement="right" title="In which shop the warehouse is belongs to." class="fa fa-question-circle"></i>
            <select name="warehouseShopId" id="warehouseShopId" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=shopList" style="width: 100%;">
                <option value=""><?= __("Select Shop"); ?>....</option>
            </select>
        </div>
        <div class="form-group">
            <label for="warehouseContacts"><?= __("Warehouse Contacts:"); ?></label>
            <textarea name="warehouseContacts" id="warehouseContacts" rows="3" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label for="warehouseLocation"><?= __("Warehouse Location:"); ?></label>
            <textarea name="warehouseLocation" id="warehouseLocation" rows="3" class="form-control"></textarea>
        </div>
            
    </div>
    <!-- /Box body-->

  <?php

  // Include the modal footer
  modal_footer();

}


// Add new Warehouse
if(isset($_GET['page']) and $_GET['page'] == "addNewWarehouse") {

    if(empty($_POST["warehouseName"])) {
        return _e("Please enter warehouse name.");
    }
    
    $returnMsg = easyInsert(
        "warehouses", // Table name
        array( // Fileds Name and value
            "warehouse_name"      => $_POST["warehouseName"],
            "warehouse_shop"      => empty($_POST["warehouseShopId"]) ? NULL : $_POST["warehouseShopId"],
            "warehouse_contacts"  => $_POST["warehouseContacts"],
            "warehouse_location"  => $_POST["warehouseLocation"]
        ),
        array( // No duplicate allow.
            "warehouse_name"   => $_POST["warehouseName"]
        )
    );

    if($returnMsg === true) {
        _s("New warehouse added successfully.");
    } else {
        _e($returnMsg);
    }

}


/*************************** Product Warehouse List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "productWarehouseList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "warehouse_name",
        "warehouse_contacts",
        "warehouse_location"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "warehouses",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }

    if(!empty($requestData["search"]["value"])) {  // get data with search
        
        $getData = easySelectA(array(
            "table"   => "warehouses as warehouse",
            "fields"  => "warehouse_id, warehouse_name, shop_name, warehouse_contacts, warehouse_location",
            "join"      => array(
                "left join {$table_prefeix}shops on shop_id = warehouse_shop"
            ),
            "where"   => array(
                "warehouse.is_trash = 0 and warehouse_name LIKE" => $requestData['search']['value'] . "%",
                " OR warehouse_contacts LIKE" => $requestData['search']['value'] . "%",
                " OR warehouse_location LIKE" => $requestData['search']['value'] . "%"
            ),
            "limit" => array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            ),
            "orderby"   => array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            )
        ));

        $totalFilteredRecords = $getData ? $getData["count"] : 0;

    } else { // Get data withouth search

        $getData = easySelectA(array(
            "table"   => "warehouses as warehouse",
            "fields"  => "warehouse_id, warehouse_name, shop_name, warehouse_contacts, warehouse_location",
            "join"      => array(
                "left join {$table_prefeix}shops on shop_id = warehouse_shop"
            ),
            "where"   => array(
                "warehouse.is_trash = 0"
            ),
            "limit" => array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            ),
            "orderby"   => array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            )
        ));

    }

  $allData = [];
  // Check if there have more then zero data
  if(isset($getData['count']) and $getData['count'] > 0) {
      
      foreach($getData['data'] as $key => $value) {
          $allNestedData = [];
          $allNestedData[] = $value["warehouse_name"];
          $allNestedData[] = $value["shop_name"];
          $allNestedData[] = $value["warehouse_contacts"];
          $allNestedData[] = $value["warehouse_location"];
          // The action button
          $allNestedData[] = '<div class="btn-group">
                                  <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                  action
                                  <span class="caret"></span>
                                  <span class="sr-only">Toggle Dropdown</span>
                                  </button>
                                  <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                  <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=stock-management&page=editWarehouse&id='. $value["warehouse_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                  <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=stock-management&page=deleteWarehouse" data-to-be-deleted="'. $value["warehouse_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                  </ul>
                              </div>';
          
          $allData[] = $allNestedData;
      }
  }
  

  $jsonData = array (
      "draw"              => intval( $requestData['draw'] ),
      "recordsTotal"      => intval( $totalRecords ),
      "recordsFiltered"   => intval( $totalFilteredRecords ),
      "data"              => $allData
  );
  
  // Encode in Json Formate
  echo json_encode($jsonData); 

}


/***************** Delete Warehouse ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteWarehouse") {

  $deleteData = easyDelete(
      "warehouses",
      array(
          "warehouse_id" => $_POST["datatoDelete"]
      )
  );

  if($deleteData === true) {
      echo 1;
  } 

}


/************************** Edit Warehouse **********************/
if(isset($_GET['page']) and $_GET['page'] == "editWarehouse") {

    $selectWarehouse = easySelectA(array(
        "table"     => "warehouses",
        "fields"    => "warehouse_name, warehouse_shop, shop_name, warehouse_contacts, warehouse_location",
        "join"      => array(
            "left join {$table_prefeix}shops on shop_id = warehouse_shop"
        ),
        "where"     => array(
            "warehouse_id" => $_GET['id']
        )
    ));

  $warehouse = $selectWarehouse["data"][0];

  // Include the modal header
  modal_header("Edit Warehouse", full_website_address() . "/xhr/?module=stock-management&page=updateWarehouse");
  
  ?>
    <div class="box-body">
      
        <div class="form-group required">
            <label for="warehouseName"><?= __("Warehouse Name:"); ?></label>
            <input type="text" name="warehouseName" id="warehouseName" value="<?php echo $warehouse["warehouse_name"] ?>" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="warehouseShopId"><?= __("Shop:"); ?> </label>
            <i data-toggle="tooltip" data-placement="right" title="In which shop the warehouse is belongs to." class="fa fa-question-circle"></i>
            <select name="warehouseShopId" id="warehouseShopId" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=shopList" style="width: 100%;">
                <option value=""><?= __("Select Shop"); ?>....</option>
                <option selected value="<?php echo $warehouse["warehouse_shop"] ?>"><?php echo $warehouse["shop_name"] ?></option>
            </select>
        </div>
        <div class="form-group">
            <label for="warehouseContacts"><?= __("Warehouse Contacts:"); ?></label>
            <textarea name="warehouseContacts" id="warehouseContacts" rows="3" class="form-control"> <?php echo $warehouse["warehouse_contacts"] ?> </textarea>
        </div>
        <div class="form-group">
            <label for="warehouseLocation"><?= __("Warehouse Location:"); ?></label>
            <textarea name="warehouseLocation" id="warehouseLocation" rows="3" class="form-control"> <?php echo $warehouse["warehouse_location"] ?> </textarea>
        </div>
        <input type="hidden" name="warehouse_id" value="<?php echo safe_entities($_GET['id']); ?>">
    </div>
    <!-- /Box body-->

  <?php

  // Include the modal footer
  modal_footer();

}


//*******************************  Update Warehouse ******************** */
if(isset($_GET['page']) and $_GET['page'] == "updateWarehouse") {

  // Update warehouse Information
  $updateWarehouse = easyUpdate(
      "warehouses",
      array(
          "warehouse_name"      => $_POST["warehouseName"],
          "warehouse_shop"      => $_POST["warehouseShopId"],
          "warehouse_contacts"  => $_POST["warehouseContacts"],
          "warehouse_location"  => $_POST["warehouseLocation"]
      ),
      array(
          "warehouse_id" => $_POST["warehouse_id"]
      )
  );

  if($updateWarehouse === true) {
      _s("Warehouse successfully updated.");
  } else {
      _e($updateWarehouse);
  }
}


/*************************** Product Return List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "ProductReturnList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "sales_delivery_date",
        "sales_id",
        "customer_name",
        "sales_grand_total",
        "sales_product_discount",
        "sales_grand_total",
        "sales_paid_amount",
        "sales_due",
        "",
        "sales_payment_status"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "sales",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0 and sales_shop_id" => $_SESSION["sid"]
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if(!empty($requestData["search"]["value"])) {  // get data with search
      
        $getData = easySelect(
            "sales as sales",
            "sales_id, sales_delivery_date, sales_status, sales_shop_id, sales_reference, sales_customer_id, customer_name, sales_total_amount, sales_product_discount, sales_discount, sales_change, sales_shipping, sales_grand_total, sales_paid_amount, sales_due, sales_payment_status, upazila_name, district_name",
            array (
                "left join {$table_prefeix}customers on customer_id = sales_customer_id",
                "left join {$table_prefeix}upazilas on upazila_id = customer_upazila",
                "left join {$table_prefeix}districts on district_id = customer_district"
            ),
            array (
                "sales.is_trash = 0 and sales.is_return = 1 and sales.sales_delivery_date is not null",
                " AND customer_name LIKE" => $requestData['search']['value'] . "%",
                " OR sales_reference LIKE" => $requestData['search']['value'] . "%"
            ),
            array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        );
  
        $totalFilteredRecords = $getData ? $getData["count"] : 0;
  
    } else if(!empty($requestData["columns"][1]['search']['value']) or !empty($requestData["columns"][2]['search']['value']) or !empty($requestData["columns"][3]['search']['value']) or !empty($requestData["columns"][11]['search']['value'])) { // Get data with search by column
        
        $dateRange[0] = "";
        $dateRange[1] = "";
        if(!empty($requestData["columns"][1]['search']['value'])) {
            $dateRange = explode(" - ", safe_input($requestData["columns"][1]['search']['value']));
        }
        
        $getData = easySelect(
            "sales as sales",
            "sales_id, sales_delivery_date, sales_status, sales_shop_id, sales_reference, sales_customer_id, customer_name, sales_total_amount, sales_product_discount, sales_discount, sales_change, sales_shipping, sales_grand_total, sales_paid_amount, sales_due, sales_payment_status, upazila_name, district_name",
            array (
                "left join {$table_prefeix}customers on customer_id = sales_customer_id",
                "left join {$table_prefeix}upazilas on upazila_id = customer_upazila",
                "left join {$table_prefeix}districts on district_id = customer_district"
            ),
            array (
              "sales.is_trash = 0 and sales.is_return = 1 and sales.sales_delivery_date is not null",
              " AND sales_reference LIKE" => "%" . $requestData["columns"][2]['search']['value'] . "%",
              " AND customer_name LIKE" => "%" . $requestData["columns"][3]['search']['value'] . "%",
              " AND sales_payment_status" => $requestData["columns"][11]['search']['value'],
              " AND (sales_delivery_date BETWEEN '{$dateRange[0]}' and '{$dateRange[1]}')"
            ),
            array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        );
  
        
    } else { // Get data withouth search
  
      $getData = easySelect(
          "sales as sales",
          "sales_id, sales_delivery_date, sales_status, sales_shop_id, sales_reference, sales_customer_id, customer_name, sales_total_amount, sales_product_discount, sales_discount, sales_change, sales_shipping, sales_grand_total, sales_paid_amount, sales_due, sales_payment_status, upazila_name, district_name",
          array (
            "left join {$table_prefeix}customers on customer_id = sales_customer_id",
            "left join {$table_prefeix}upazilas on upazila_id = customer_upazila",
            "left join {$table_prefeix}districts on district_id = customer_district"
          ),
          array (
            "sales.is_trash = 0 and sales.is_return = 1 and sales.sales_delivery_date is not null",
          ),
          array (
              $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
          ),
          array (
              "start" => $requestData['start'],
              "length" => $requestData['length']
          )
      );
  
  } 

  
    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['count']) and $getData['count'] > 0) {
        
        foreach($getData['data'] as $key => $value) {
            
            $getSalesPaymentStatus = "";
            if($value["sales_payment_status"] === "paid") {
                $getSalesPaymentStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-success'>Paid</span>";
            } else if($value["sales_payment_status"] === "partial") {
                $getSalesPaymentStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-warning'>Partial</span>";
            } else {
                $getSalesPaymentStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-danger'>Due</span>";
            }


            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = "<iledit>{$value["sales_delivery_date"]}</iledit>";
            $allNestedData[] = "<a data-toggle='modal' data-target='#modalDefault' href='" . full_website_address() . "/xhr/?module=reports&page=showInvoiceProducts&id={$value['sales_id']}'>{$value['sales_reference']}</a>";
            $allNestedData[] = "<iledit data-val='{$value["sales_customer_id"]}'>{$value['customer_name']}, {$value['upazila_name']}, {$value['district_name']}</iledit>";
            $allNestedData[] = $value["sales_total_amount"];
            $allNestedData[] = $value["sales_product_discount"] + $value["sales_discount"];
            $allNestedData[] = $value["sales_shipping"];
            $allNestedData[] = $value["sales_grand_total"];
            $allNestedData[] = $value["sales_paid_amount"];
            $allNestedData[] = $value["sales_due"];
            $allNestedData[] = $value["sales_grand_total"] - $value["sales_due"];
            $allNestedData[] = $getSalesPaymentStatus;
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        <li><a onClick=\'BMS.MAIN.printPage(this.href, event);\' href="'. full_website_address() .'/invoice-print/?autoPrint=true&invoiceType=posSale&id='. $value["sales_id"] .'"><i class="fa fa-print"></i> Print Invoice</a></li>
                                        <li><a target="_blank" href="'. full_website_address() .'/invoice-print/?invoiceType=posSale&id='. $value["sales_id"] .'"><i class="fa fa-edit"></i> View Return</a></li>
                                        <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=my-shop&page=deletePosSales" data-to-be-deleted="'. $value["sales_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                    </ul>
                                </div>' . "<pkey>{$value["sales_id"]}</pkey>";
            
            $allData[] = $allNestedData;

        }

    }
    
  
    $jsonData = array (
        "draw"              => intval( $requestData['draw'] ),
        "recordsTotal"      => intval( $totalRecords ),
        "recordsFiltered"   => intval( $totalFilteredRecords ),
        "data"              => $allData
    );
    
    // Encode in Json Formate
    echo json_encode($jsonData); 
  
}


/************************** Add new Batch **********************/
if(isset($_GET['page']) and $_GET['page'] == "newBatch") {

    // Include the modal header
    modal_header("New Batch", full_website_address() . "/xhr/?module=stock-management&page=addNewBatch");
    
    ?>
      <div class="box-body">
        
        <div class="form-group required">
            <label for="batchProductId"><?= __("Product:"); ?></label>
            <select name="batchProductId" id="batchProductId" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productList" required>
                <option value=""><?= __("Select Product"); ?>....</option>
            </select>
        </div>
        <div class="form-group required">
          <label for="batchNumber"><?= __("Batch Number:"); ?></label>
          <input type="text" name="batchNumber" id="batchNumber" placeholder="Enter batch number" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="batchManufacturingDate"><?= __("Manufacturing Date:"); ?> <small><?= __("Optional"); ?></small> </label>
          <input type="text" name="batchManufacturingDate" id="batchManufacturingDate" autoComplete="off" class="form-control datePicker">
        </div>
        <div class="form-group required">
          <label for="batchExpiryDate"><?= __("Expiry Date:"); ?></label>
          <input type="text" name="batchExpiryDate" id="batchExpiryDate" autoComplete="off" class="form-control datePicker" required>
        </div>
        <div class="form-group">
          <label for="batchDescription"><?= __("Description:"); ?></label>
          <textarea name="batchDescription" id="batchDescription" rows="3" class="form-control"></textarea>
        </div>
              
      </div>
      <!-- /Box body-->
  
    <?php
  
    // Include the modal footer
    modal_footer();
  
}


/************************** Add new Batch **********************/
if(isset($_GET['page']) and $_GET['page'] == "newBatchForSelectedProduct") {

    $product_name = easySelectA(array(
        "table"     => "products",
        "fields"    => "product_name",
        "where"     => array(
            "product_id"    => $_GET["pid"]
        )
    ))["data"][0]["product_name"];

    // Include the modal header
    modal_header("Add Batch for {$product_name}", full_website_address() . "/xhr/?module=stock-management&page=addNewBatch");
    
    ?>
      <div class="box-body">
        
        <input type="hidden" name="batchProductId" value="<?php echo safe_entities($_GET["pid"]); ?>">
        <div class="form-group required">
          <label for="batchNumber"><?= __("Batch Number:"); ?></label>
          <input type="text" name="batchNumber" id="batchNumber" value="<?php echo safe_entities($_GET["val"]); ?>" placeholder="Enter batch number" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="batchManufacturingDate"><?= __("Manufacturing Date:"); ?> <small><?= __("Optional"); ?></small> </label>
          <input type="text" name="batchManufacturingDate" id="batchManufacturingDate" autoComplete="off" class="form-control datePicker">
        </div>
        <div class="form-group required">
          <label for="batchExpiryDate"><?= __("Expiry Date:"); ?></label>
          <input type="text" name="batchExpiryDate" id="batchExpiryDate" autoComplete="off" class="form-control datePicker" autofocus required>
        </div>
        <div class="form-group">
          <label for="batchDescription"><?= __("Description:"); ?></label>
          <textarea name="batchDescription" id="batchDescription" rows="3" class="form-control"></textarea>
        </div>
              
      </div>
      <!-- /Box body-->
  
    <?php
  
    // Include the modal footer
    modal_footer();
  
  }
  
  
// Add new Warehouse
if(isset($_GET['page']) and $_GET['page'] == "addNewBatch") {
  
    if (empty($_POST["batchProductId"])) {
        return _e("Please select product.");
    } elseif (empty($_POST["batchNumber"])) {
        return _e("Please enter batch number.");
    } elseif (empty($_POST["batchExpiryDate"])) {
        return _e("Please select batch expiry date.");
    }
    
    $returnMsg = easyInsert(
        "product_batches", // Table name
        array( // Fileds Name and value
            "product_id"                => $_POST["batchProductId"],
            "batch_number"              => $_POST["batchNumber"],
            "batch_manufacturing_date"  => empty($_POST["batchManufacturingDate"]) ? NULL : $_POST["batchManufacturingDate"],
            "batch_expiry_date"         => $_POST["batchExpiryDate"],
            "batch_description"         => $_POST["batchDescription"]
        ),
        array( // No duplicate allow.
            "product_id"            => $_POST["batchProductId"],
            " AND batch_number"     => $_POST["batchNumber"],
        )
    );
  
    if($returnMsg === true) {
        _s("New batch has been added successfully.");
    } else {
        _e($returnMsg);
    }
  
}


/*************************** Batch List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "batchList") {
    
    $requestData = $_REQUEST;
    $getData = [];
  
    // List of all columns name
    $columns = array(
        "batch_id",
        "product_name",
        "batch_number",
        "batch_expiry_date",
        "batch_description"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "product_batches",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];
  
    if($requestData['length'] == -1) {
      $requestData['length'] = $totalRecords;
    }
  
    if(!empty($requestData["search"]["value"])) {  // get data with search

        $getData = easySelectA(array(
            "table"     => "product_batches as product_batches",
            "fields"    => "batch_id, concat(product_name, ' ', if(product_group is null, '', product_group) ) as product_name, batch_number, date(batch_expiry_date) as batch_expiry_date, batch_description",
            "join"      => array(
                "left join {$table_prefeix}products as products on products.product_id = product_batches.product_id"
            ),
            "where"     => array(
                "product_batches.is_trash = 0 and ( product_name LIKE" => $requestData['search']['value'] . "%",
                " OR batch_number LIKE" => $requestData['search']['value'] . "%",
                ")"
            ),
            "orderby" => array(
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit" => array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));
  
        $totalFilteredRecords = $getData ? $getData["count"] : 0;
  
    } else { // Get data withouth search

        $getData = easySelectA(array(
            "table"     => "product_batches as product_batches",
            "fields"    => "batch_id, concat(product_name, ' ', if(product_group is null, '', product_group) ) as product_name, batch_number, date(batch_expiry_date) as batch_expiry_date, batch_description",
            "join"      => array(
                "left join {$table_prefeix}products as products on products.product_id = product_batches.product_id"
            ),
            "where"     => array(
                "product_batches.is_trash = 0"
            ),
            "orderby" => array(
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit" => array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));
  
    }
  
    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['count']) and $getData['count'] > 0) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["product_name"];
            $allNestedData[] = $value["batch_number"];
            $allNestedData[] = $value["batch_expiry_date"];
            $allNestedData[] = $value["batch_description"];
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=stock-management&page=deleteBatch" data-to-be-deleted="'. $value["batch_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                    </ul>
                                </div>';
            
            $allData[] = $allNestedData;
        }
    }
    
  
    $jsonData = array (
        "draw"              => intval( $requestData['draw'] ),
        "recordsTotal"      => intval( $totalRecords ),
        "recordsFiltered"   => intval( $totalFilteredRecords ),
        "data"              => $allData
    );
    
    // Encode in Json Formate
    echo json_encode($jsonData); 
  
}
  
  
/***************** Delete Warehouse ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteBatch") {
  
    $deleteData = easyDelete(
        "product_batches",
        array(
            "batch_id" => $_POST["datatoDelete"]
        )
    );
  
    if($deleteData === true) {
        echo 1;
    } 
  
}


/*************************** new Stock Entry ***********************/
if(isset($_GET['page']) and $_GET['page'] == "newStockEntry") {


    //print_r($_POST);
    //exit();
    
    if( empty($_POST["stockEntryWarehouse"]) ) {
        return _e("Please select warehouse");
    } elseif( !isset($_POST["productID"]) ) {
        return _e("Please select at least one product");
    }

    // Insert Stock Entry
    $insertStockEntry = easyInsert(
        "stock_entries",
        array(
            "se_date"           => $_POST["stockEntryDate"],
            "se_type"           => $_POST["stockEntryType"],
            "se_warehouse_id"   => $_POST["stockEntryWarehouse"],
            "se_shop_id"        => $_SESSION["sid"],
            "se_note"           => $_POST["stockEntryNote"],
            "se_add_by"         => $_SESSION["uid"]
        ),
        array(),
        true
    );


    if( isset($insertStockEntry["status"]) and $insertStockEntry["status"] === "success") {

        $insertStockEntryItems = "INSERT INTO {$table_prefeix}product_stock(
            stock_type,
            stock_entry_date,
            stock_se_id,
            stock_warehouse_id,
            stock_shop_id,
            stock_product_id,
            stock_batch_id,
            stock_item_price,
            stock_item_qty,
            stock_item_discount,
            stock_item_subtotal,
            stock_created_by,
            is_bundle_item
        ) VALUES ";

        $stockType = "Initial";

        if($_POST["stockEntryType"] === "Production") {
            $stockType = "sale-production";
        } else if($_POST["stockEntryType"] === "Adjustment") {
            $stockType = "adjustment";
        }
    

        foreach( $_POST["productID"] as $key => $productId ) {

            /** Generate Stock Entry product */
            $insertStockEntryItems .= "
            (
                '{$stockType}',
                '". safe_input($_POST["stockEntryDate"]) ."',
                '{$insertStockEntry["last_insert_id"]}',
                '". safe_input($_POST["stockEntryWarehouse"]) ."',
                '". $_SESSION["sid"] ."',
                '". safe_input($productId) ."',
                ". ( empty($_POST["productBatch"][$key]) ? "NULL" : "'". safe_input($_POST["productBatch"][$key]) . "'" ) .",
                '". safe_input($_POST["productPurchasePrice"][$key]) ."',
                '{$_POST["productQnt"][$key]}',
                '0',
                '0',
                '". $_SESSION["uid"] ."',
                '". 0 ."'
            ),";


            // Insert Sub/ bundle products on for Initital stock type
            if($_POST["stockEntryType"]  === "Initial") {

                // Select products, which have sub products and insert sub/bundle products
                $subProducts = easySelectA(array(
                    "table"     => "products as product",
                    "fields"    => "bg_item_product_id, 
                                    sub_product.product_purchase_price as purchase_price,
                                    bg_product_qnt
                                    ",
                    "join"      => array(
                        "inner join {$table_prefeix}bg_product_items as bg_product on bg_product_id = product_id",
                        "left join {$table_prefeix}products as sub_product on sub_product.product_id = bg_item_product_id"
                    ),
                    "where"     => array(
                        "( product.has_sub_product = 1 or product.product_type = 'Bundle' ) and bg_product.is_raw_materials = 0 and product.product_id = {$productId}"
                    )
                ));

                // Insert sub/ bundle products
                if($subProducts !== false) {

                    foreach($subProducts["data"] as $spKey => $sp) {

                        $totalSubProductQty = $_POST["productQnt"][$key] * $sp["bg_product_qnt"];
                    
                        /** Generate Stock Entry product for sub/bundle products */
                        $insertStockEntryItems .= "
                        (
                            '{$stockType}',
                            '". safe_input($_POST["stockEntryDate"]) ."',
                            '{$insertStockEntry["last_insert_id"]}',
                            '". safe_input($_POST["stockEntryWarehouse"]) ."',
                            '". $_SESSION["sid"] ."',
                            '". $sp["bg_item_product_id"] ."',
                            NULL,
                            '". $sp["purchase_price"] ."',
                            '{$totalSubProductQty}',
                            '0',
                            '0',
                            '". $_SESSION["uid"] ."',
                            '". 1 ."'
                        ),";
            
                    }

                }

            }

        }


       // echo substr_replace($insertStockEntryItems, ";", -1, 1);

        // Insert stock transfer item
        $conn->query(substr_replace($insertStockEntryItems, ";", -1, 1));

        _s("Stock Entry has been successfully added.");

    } else {

        _e($insertTransfer);

    }

}



/*************************** stockEntryList ***********************/
if(isset($_GET['page']) and $_GET['page'] == "stockEntryList") {
    
    $requestData = $_REQUEST;
    $getData = [];
  
    // List of all columns name
    $columns = array(
        "",
        "se_date ",
        "se_type",
        "warehouse_name",
        "se_note"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "stock_entries",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];
  
    if($requestData['length'] == -1) {
      $requestData['length'] = $totalRecords;
    }
  
    if(!empty($requestData["search"]["value"])) {  // get data with search

        $getData = easySelectA(array(
            "table"     => "stock_entries as stock_entries",
            "fields"    => "se_id, se_date, se_type, warehouse_name, se_note",
            "join"      => array(
                "left join {$table_prefeix}warehouses on warehouse_id = se_warehouse_id"
            ),
            "where"     => array(
                "stock_entries.is_trash = 0 AND (",
                " warehouse_name LIKE" => $requestData['search']['value'] . "%",
                " OR se_note LIKE" => $requestData['search']['value'] . "%",
                ")"
            ),
            "orderby" => array(
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit" => array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));
  
        $totalFilteredRecords = $getData ? $getData["count"] : 0;
  
    } else { // Get data withouth search

        $getData = easySelectA(array(
            "table"     => "stock_entries as stock_entries",
            "fields"    => "se_id, se_date, se_type, warehouse_name, se_note",
            "join"      => array(
                "left join {$table_prefeix}warehouses on warehouse_id = se_warehouse_id"
            ),
            "where"     => array(
                "stock_entries.is_trash = 0"
            ),
            "orderby" => array(
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit" => array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));
  
    }
  
    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['count']) and $getData['count'] > 0) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["se_date"];
            $allNestedData[] = $value["se_type"];
            $allNestedData[] = $value["warehouse_name"];
            $allNestedData[] = $value["se_note"];
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?module=stock-management&page=viewStockEntryProduct&id='. $value["se_id"] .'"  data-target="#modalDefault"><i class="fa fa-eye"></i> View Products</a></li>
                                        <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=stock-management&page=deleteStockEntry" data-to-be-deleted="'. $value["se_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                    </ul>
                                </div>';
            
            $allData[] = $allNestedData;
        }
    }
    
  
    $jsonData = array (
        "draw"              => intval( $requestData['draw'] ),
        "recordsTotal"      => intval( $totalRecords ),
        "recordsFiltered"   => intval( $totalFilteredRecords ),
        "data"              => $allData
    );
    
    // Encode in Json Formate
    echo json_encode($jsonData); 
  
}


/************************** viewStockEntryProduct **********************/
if(isset($_GET['page']) and $_GET['page'] == "viewStockEntryProduct") {
  

    // Select StockEntry Item
    $selectStockEntryItems = easySelect(
        "product_stock as product_stock",
        "stock_product_id, product_name, product_unit, stock_item_qty",
        array (
          "left join {$table_prefeix}products on product_id = stock_product_id"
        ),
        array (
            "product_stock.is_trash = 0 and is_bundle_item = 0 and stock_se_id" => $_GET["id"]
        )
    );
  
  
    ?>
  
    <div class="modal-header">
        <h4 class="modal-title"><?= __("Stock Entry Items"); ?></h4>
    </div>
  
    <div class="modal-body">
  
      <table class="table table-striped table-condensed">
        <tbody>
          <tr>
              <td><?= __("Products"); ?></td>
              <td><?= __("Quantity"); ?></td>
              <td><?= __("Unit"); ?></td>
          </tr>
  
        <?php 
  
            foreach($selectStockEntryItems["data"] as $key => $item) {
  
                echo "<tr>";
                echo " <td>{$item['product_name']}</td>";
                echo " <td>{$item['stock_item_qty']}</td>";
                echo " <td>{$item['product_unit']}</td>";
                echo "</tr>";
  
            }
  
        ?>
  
        </tbody>
  
    </table>
        
    </div> <!-- /.modal-body -->
  
    <?php
  
}


/***************** Delete Purchase ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteStockEntry") {

    $deleteData = easyDelete(
        "stock_entries",
        array(
            "se_id" => $_POST["datatoDelete"]
        )
    );
  
    if($deleteData === true) {

        /**
         * If the stock entry deleted successfully then delete all items from product_stock
         * We have to do it by manual, because of the foreign key constraint can not do correctly
         */

         easyDelete(
            "product_stock",
            array(
                "stock_se_id" => $_POST["datatoDelete"]
            )
        );

      echo '{
          "title": "'. __("The entry has been successfully deleted.") .'",
          "icon": "success"
      }';


    } 
  
}

?>