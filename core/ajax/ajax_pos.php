<?php

$getData = $_POST; 

//var_dump(min($getData["productQnt"]) < 0);



//print_r($getData);

//exit();

$salesDate = $getData["salesDate"];

$customerId = $getData["customersId"];
$warehouseId = $getData["warehouseId"];
$salesQuantity = array_sum($getData["productQnt"]);

$salesShippingCharge = empty($getData["shippingCharge"]) ? 0 : $getData["shippingCharge"];
$salesTotalItems = count($getData["productID"]);
$salesNote = $getData["salesNote"];



$sales_id = "";
$sales_status = ( isset($_POST["posAction"]) and $_POST["posAction"] === "sale_is_hold" ) ? "Hold" : $_POST["salesStatus"];

if( isset($_POST["salesId"]) ) { // If there is a sales ID then update the sales

    $sales_id = $_POST["salesId"];

    // Insert Sales into db
    $updateSales = easyUpdate(
        "sales",
        array (
            "sales_status"                  => $sales_status, //"Delivered",
            "sales_delivery_date"           => $salesDate,
            "sales_customer_id"             => $customerId,
            "sales_shop_id"                 => $getData["userShopId"],
            "sales_quantity"                => $salesQuantity,
            "sales_shipping"                => $salesShippingCharge,
            "sales_update_by"               => $_SESSION["uid"],
            "sales_total_item"              => $salesTotalItems,
            "sales_total_packets"           => $getData["totalPackets"],
            "sales_tariff_charges_details"  => serialize( array("tariff" => $getData["tariffChargesName"], "value" => $getData["tariffChargesAmount"]) ),
            "sales_by_pos"                  => 1,
            "sales_note"                    => $salesNote,
            "is_wastage"                    => ( isset($getData["saleOptions"]) and $getData["saleOptions"] === "wastage" ) ? 1 : 0
            //"is_return"                     => ( isset($getData["saleOptions"]) and $getData["saleOptions"] === "return" ) ? 1 : 0,
        ),
        array (
            "sales_id"  => $sales_id
        )
    );
    

} else { // If there is no sales id defined then insert new one

    $selectSalesReference = easySelect(
        "sales",
        "sales_reference",
        array(),
        array (
            "sales_by_pos" => 1,
            " AND sales_reference LIKE 'SALE/POS/{$_SESSION['sid']}{$_SESSION['uid']}/%'",
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

    // Referense Format: SALE/POS/n
    $salesReferences = "SALE/POS/".$_SESSION['sid'].$_SESSION['uid']."/";

    // check if there is minimum one records
    if($selectSalesReference) {
        $getLastReferenceNo = explode($salesReferences, $selectSalesReference["data"][0]["sales_reference"])[1];
        $salesReferences = $salesReferences . ((int)$getLastReferenceNo+1);
    } else {
        $salesReferences = "SALE/POS/".$_SESSION['sid'].$_SESSION['uid']."/1";
    }

    // Insert Sales into db
    $insertSales = easyInsert(
        "sales",
        array (
            "sales_status"                  => $sales_status, //$_POST["posAction"] === "sale_is_hold" ? "Hold" : "Delivered",
            "sales_order_date"              => ( isset($_POST["orderDate"]) and !empty($_POST["orderDate"]) ) ? $_POST["orderDate"] : null,
            "sales_delivery_date"           => $salesDate,
            "sales_reference"               => $salesReferences,
            "sales_customer_id"             => $customerId,
            "sales_warehouse_id"            => $warehouseId,
            "sales_shop_id"                 => $getData["userShopId"],
            "sales_quantity"                => $salesQuantity,
            "sales_shipping"                => $salesShippingCharge,
            "sales_created_by"              => $_SESSION["uid"],
            "sales_total_item"              => $salesTotalItems,
            "sales_total_packets"           => $getData["totalPackets"],
            "sales_tariff_charges_details"  => serialize( array("tariff" => $getData["tariffChargesName"], "value" => $getData["tariffChargesAmount"]) ),
            "sales_by_pos"                  => 1,
            "sales_note"                    => $salesNote,
            "is_wastage"                    => ( isset($getData["saleOptions"]) and $getData["saleOptions"] === "wastage" ) ? 1 : 0,
            //"is_return"                     => ( isset($getData["saleOptions"]) and $getData["saleOptions"] === "return" ) ? 1 : 0
            "is_exchange"                   => min($getData["productQnt"]) < 0 ? 1 : 0 // if there negative quantity then mark as exchange
        ),
        array(),
        true
    );

    // if sale not insert then throw an error 
    if( !isset($insertSales["last_insert_id"]) ) {

        $returnError = array (
            "saleStatus"   => "error",
            "msg"  =>  __("An unknown error occured. Please contact with the administrator.")
        );

        echo json_encode($returnError);
        return;
    }

    $sales_id = $insertSales["last_insert_id"];

}


// Need to generator after insert sales items
$salesTotalAmount = 0;
$salesTotalProductDiscount = 0;
$salesTotalOrderDiscount = 0;
$salesGrandTotal = 0;
$salesChanges = 0;


$insertSaleItems = "INSERT INTO {$table_prefeix}product_stock(
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
    stock_item_description,
    stock_created_by,
    is_bundle_item
) VALUES ";


// 'Order Placed', 'In Production', 'Processing', 'Hold', 'Delivered', 'Cancelled'
/**
 * 'initial', 'sale-production', 'sale-processing', 'sale', 'sale-order', 'wastage-sale', 'sale-return', 'purchase', 'purchase-order', 
 * 'purchase-return', 'transfer-in', 'transfer-out', 'specimen-copy', 'specimen-copy-return', 'undeclared'
 */

// Declare stock type
$stock_type = "undeclared";
if( isset($_POST["saleOptions"]) and $_POST["saleOptions"] === "wastage" ) {
    $stock_type = "wastage-sale";
} else if( $_POST["posAction"] === "sale_is_hold") { // if sale is hold then stock type will undeclared
    $stock_type = "undeclared";
} elseif($sales_status === "Delivered") {
    $stock_type = "sale";
} elseif($sales_status === "Order Placed") {
    $stock_type = "sale-order";
} elseif($sales_status === "In Production") {
    $stock_type = "sale-production";
} elseif($sales_status === "Processing") {
    $stock_type = "sale-processing";
}

// Insert product items into sale table
foreach($getData["productID"] as $key => $productId) {

    /**
     * For expiry products add batches
     * 
     * If the batch is not set then set it programatically
     */
    if( $getData["productHasExpiryDate"][$key] and empty($getData["productBatch"][$key]) ) {

        $select_batch_product = easySelectA(array(
            "table"     => "product_batches as product_batches",
            "fields"    => "product_batches.product_id as pid, batch_number, product_batches.batch_id as batch_id, batch_expiry_date, if(stock_in is null, 0, round(stock_in, 2) ) as stock_in",
            "join"  => array(
                "left join ( select 
                                vp_id, 
                                warehouse, 
                                batch_id,
                                sum(base_stock_in/base_qty) as stock_in 
                            FROM product_base_stock 
                            where warehouse = '{$warehouseId}' 
                            group by batch_id 
                        ) as product_base_stock on product_base_stock.vp_id = product_batches.product_id and product_base_stock.batch_id = product_batches.batch_id"
            ),
            "where"     => array(
                "product_batches.is_trash = 0 and stock_in > 0 and date(batch_expiry_date) > curdate() and product_batches.product_id"    => $productId
            ),
            "orderby"   => array(
                "batch_expiry_date" => "ASC" // which batch expire first
            )
        ));
        
        
        $totalBatchProductQnt = $getData["productQnt"][$key];
        
        foreach($select_batch_product["data"] as $index=> $batchProduct) {

            /** Calculate the current batch quantity */
            $currentBatchQnt = $totalBatchProductQnt > $batchProduct["stock_in"] ? $batchProduct["stock_in"] : $totalBatchProductQnt;

            // Calculate the total amount
            $salesTotalAmount += $getData["productSalePirce"][$key] * $currentBatchQnt;

            // Calculate the product/items Discount
            $itemDiscountAmount = calculateDiscount($getData["productSalePirce"][$key], $getData["productDiscount"][$key]);

            // Calculate the total product/items Discount
            $salesTotalProductDiscount += $itemDiscountAmount * $currentBatchQnt;

            // Calculate item amount after discount
            $itemAmoutnAfterDiscount = $getData["productSalePirce"][$key] - $itemDiscountAmount;

            $salesItemSubTotal = $currentBatchQnt * $itemAmoutnAfterDiscount;

            $insertSaleItems .= "
            (
                '{$stock_type}',
                '". safe_input($salesDate) ."',
                '{$sales_id}',
                '". safe_input($warehouseId) ."',
                '". $getData["userShopId"] ."',
                '". safe_input($productId) ."',
                '{$batchProduct["batch_id"]}',
                '". safe_input($getData["productSalePirce"][$key]) ."',
                '{$currentBatchQnt}',
                '". $itemDiscountAmount ."',
                '". $salesItemSubTotal ."',
                '". safe_input($getData["productItemDetails"][$key]) ."',
                '". $_SESSION["uid"] ."',
                '". 0 ."'
            ),";

            
            /**
             * If totalBatchProductQnt is less then or equal to of current batch stock in
             * the break the loop
             */
            if($totalBatchProductQnt <= $batchProduct["stock_in"]) {
                break;
            }
            
            /**
             * If totalBatchProductQnt is not less then or equal to of current batch stock in
             * the minus the batch stock value from totalBatchProductQnt and continue the loop
             */
            $totalBatchProductQnt -= $batchProduct["stock_in"];
        
        }

    } else {

        // Calculate the total amount
        $salesTotalAmount += $getData["productSalePirce"][$key] * $getData["productQnt"][$key];

        // Calculate the product/items Discount
        $itemDiscountAmount = calculateDiscount($getData["productSalePirce"][$key], $getData["productDiscount"][$key]);

        // Calculate the total product/items Discount
        $salesTotalProductDiscount += $itemDiscountAmount * $getData["productQnt"][$key];

        // Calculate item amount after discount
        $itemAmoutnAfterDiscount = $getData["productSalePirce"][$key] - $itemDiscountAmount;

        $salesItemSubTotal = $getData["productQnt"][$key] * $itemAmoutnAfterDiscount;

        $insertSaleItems .= "
        (
            '{$stock_type}',
            '". safe_input($salesDate) ."',
            '{$sales_id}',
            '". safe_input($warehouseId) ."',
            '". $getData["userShopId"] ."',
            '". safe_input($productId) ."',
            ". ( empty($getData["productBatch"][$key]) ? "NULL" : "'". safe_input($getData["productBatch"][$key]) . "'" ) .",
            '". safe_input($getData["productSalePirce"][$key]) ."',
            '". safe_input($getData["productQnt"][$key]) ."',
            '". $itemDiscountAmount ."',
            '". $salesItemSubTotal ."',
            '". safe_input($getData["productItemDetails"][$key]) ."',
            '". $_SESSION["uid"] ."',
            '". 0 ."'
        ),";

    }

    // Select bundle products or sub products 
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
        if( $getData["productSalePirce"][$key] > $getData["productMainSalePirce"][$key] ) { // If the price is Increased 

            // Calculate the increased amount
            $increasedAmount = $getData["productSalePirce"][$key] - $getData["productMainSalePirce"][$key];
            
            // Calculate the increased purcentage
            $increasedRate = ( $increasedAmount * 100 ) / $getData["productMainSalePirce"][$key] ;

        } else if( $getData["productSalePirce"][$key] < $getData["productMainSalePirce"][$key] ) { // If the price is decrased

            // Calculate the decreased amount 
            $decreasedAmount = $getData["productMainSalePirce"][$key] - $getData["productSalePirce"][$key];
            
            // Calculate the decreased purcentage
            $decreasedRate = ( $decreasedAmount * 100 ) / $getData["productMainSalePirce"][$key] ;

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
            $bpItemDiscountAmount = calculateDiscount( $bpItemSalePrice, $getData["productDiscount"][$key] );

            $bpItemSubTotal = ( $bpItemSalePrice - $bpItemDiscountAmount) * $bpItemQnt;


            $insertSaleItems .= "
            (
                '{$stock_type}',
                '". safe_input($salesDate) ."',
                '{$sales_id}',
                '". safe_input($warehouseId) ."',
                '". $getData["userShopId"] ."',
                '". $bp["bg_item_product_id"] ."',
                NULL,
                '". $bpItemSalePrice ."',
                '". $bpItemQnt ."',
                '". $bpItemDiscountAmount ."',
                '". $bpItemSubTotal ."',
                '',
                '". $_SESSION["uid"] ."',
                '". 1 ."'
            ),";
            
        }
    }

}



// Calculate total Payments
$salesPaidAmount = array_sum($_POST["posSalePaymentAmount"]);

// delete previous payment in purpose of editing sale
easyDelete(
    "received_payments",
    array(
        "received_payments_sales_id"    => $sales_id,
    )
);



//echo "Sales Total amount: $salesTotalAmount \n";
//echo "Total Product Discount $salesTotalProductDiscount \n";

// Calculate subtotal by minusing product discount
$subtotal = $salesTotalAmount - $salesTotalProductDiscount;
//echo "Subtotal: $subtotal \n";

// Calculate order discount from subtotal
$salesOrderDiscount = calculateDiscount($subtotal, $getData["orderDiscountValue"]);
//echo "Order Discount: $salesOrderDiscount \n";

// Calculate total amount after discount
$salesAmountAfterDiscount = $subtotal - $salesOrderDiscount; 
//echo "Amount After Discount: $salesAmountAfterDiscount \n";

// Calculate Sales Tariff
$tariffCharges =  array_sum($getData["tariffChargesAmount"]);
//echo "Tariff: $tariffCharges \n";

// Calculate Net total (Amount after discount + Tax)
$netTotal = $salesAmountAfterDiscount + $tariffCharges; 
//echo "Net total: $netTotal \n";

$adjustAmount = empty($getData["adjustAmount"]) ? 0 : $getData["adjustAmount"];

// Calculate Grand total by Adding shiping charge with net total
$salesGrandTotal = $netTotal + $salesShippingCharge + $adjustAmount;
//echo "Grand Total: $salesGrandTotal \n";

//Round Sales grand total with max decimal place in calculation
$salesGrandTotal = round($salesGrandTotal, get_options("decimalPlaces") );


// Calculate Change amount
$salesChanges = ( abs($salesGrandTotal) < abs($salesPaidAmount)) ? ($salesPaidAmount - $salesGrandTotal) : 0;
//echo "Change: $salesChanges \n";

// Calculate Due amount
$salesDue = (abs($salesGrandTotal) > abs($salesPaidAmount)) ? ($salesGrandTotal - $salesPaidAmount ) : 0;
//echo "Due: $salesDue \n";


// Insert each payments
foreach( $_POST["posSalePaymentAmount"] as $paymentKey => $paymentAmount ) {

    // Insert Sales Payment into received payments table
    if($paymentAmount > 0) {

        easyInsert(
            "received_payments",
            array (
                "received_payments_type"        => "Sales Payments",
                "received_payments_datetime"    => $salesDate . date(" H:i:s"),
                "received_payments_shop"        => $_SESSION["sid"],
                "received_payments_accounts"    => empty($_POST["posSalePaymentBankAccount"][$paymentKey]) ? $_SESSION["aid"] : $_POST["posSalePaymentBankAccount"][$paymentKey],
                "received_payments_sales_id"    => $sales_id,
                "received_payments_from"        => $customerId,
                "received_payments_amount"      => $paymentAmount, //$salesGrandTotal - $salesDue
                                                    /** 
                                                     * This now is deprected. Will be remove in near update.
                                                     * *****************************************************
                                                     * Here we substract salesDue from salesGrandTotal, and do not use salesPaidAmount directly
                                                     * Becase salesPaidAmount can be grater then salesGrandTotal and there can have a change amount.
                                                     * Suppose, Total bill is 490, customer paid 500. Here we can not insert piad amount 500. We have insert
                                                     * 490 as paid amount and the rest will be changed. 
                                                    */
                "received_payments_method"      => $_POST["posSalePaymentMethod"][$paymentKey],
                "received_payments_reference"   => $_POST["posSalePaymentReference"][$paymentKey],
                "received_payments_details"     => '',
                "received_payments_add_by"      => $_SESSION["uid"]
            )
        );

    } else if( $paymentAmount < 0 ) {

        // If there have return amount to customer then add into payments_return table as ourgoing
        easyInsert(
            "payments_return",
            array(
                "payments_return_type"          => "Outgoing",
                "payments_return_date"          => $salesDate . date(" H:i:s"),
                "payments_return_accounts"      => empty($_POST["posSalePaymentBankAccount"][$paymentKey]) ? $_SESSION["aid"] : $_POST["posSalePaymentBankAccount"][$paymentKey],
                "payments_return_sales_id"      => $sales_id,
                "payments_return_customer_id"   => $customerId,
                "payment_return_method"         => $_POST["posSalePaymentMethod"][$paymentKey],
                "payments_return_amount"        => abs((int)$paymentAmount),
                "payments_return_description"   => "Return payment made on product return",
                "payments_return_by"            => $_SESSION["uid"]
            )
        );

    }

    // Update Accounts Balance
    if( empty($_POST["posSalePaymentBankAccount"][$paymentKey]) ) {
        
        updateAccountBalance($_SESSION["aid"]);

    } else {
        
        updateAccountBalance($_POST["posSalePaymentBankAccount"][$paymentKey]);

    }

}


// Generate the payment status
$salesPaymentStatus = "due";
if( abs($salesGrandTotal) <= abs($salesPaidAmount)) {

    $salesPaymentStatus = "paid";

} else if( abs($salesGrandTotal) > abs($salesPaidAmount) and abs($salesPaidAmount) > 0) {

    $salesPaymentStatus = "partial";

}

// Update the Sale 
$updateSale = easyUpdate(
    "sales",
    array (
        "sales_total_amount"            => empty($salesTotalAmount) ? 0 : $salesTotalAmount,
        "sales_product_discount"        => empty($salesTotalProductDiscount) ? 0 : $salesTotalProductDiscount,
        "sales_discount"                => empty($salesOrderDiscount) ? 0 : $salesOrderDiscount,
        "sales_tariff_charges"          => empty($tariffCharges) ? 0 : $tariffCharges,
        "sales_shipping"                => empty($getData["shippingCharge"]) ? 0 : $getData["shippingCharge"],
        "sales_adjustment"              => empty($adjustAmount) ? 0 : $adjustAmount,
        "sales_grand_total"             => empty($salesGrandTotal) ? 0 : $salesGrandTotal,
        "sales_paid_amount"             => empty($salesPaidAmount) ? 0 : $salesPaidAmount,
        "sales_change"                  => empty($salesChanges) ? 0 : $salesChanges,
        "sales_due"                     => empty($salesDue) ? 0 : $salesDue,
        "sales_payment_status"          => $salesPaymentStatus
    ),
    array (
        "sales_id"  => $sales_id
    )
);



//var_dump($salesChanges > 0 and $salesGrandTotal - $salesDue < 0);


/**
 *  ****************************************************************************************************
 *  This ultimately not required. Now commenting, will be deleted later
 *  ****************************************************************************************************
 * 
 * If there have ammount to return and the customer want to keep it as balance
 * Then, add the amount as received payment. 
 * 
 * In this situation the salesChange treated as return
 * and there will be no account selected, because the money/balance is adding from poruduct return
 * 
 */

 /*
if( $salesChanges > 0 and $salesGrandTotal - $salesDue < 0 ) {
    easyInsert(
        "received_payments",
        array (
            "received_payments_type"        => "Received Payments",
            "received_payments_datetime"    => $salesDate . date(" H:i:s"),
            "received_payments_shop"        => $_SESSION["sid"],
            "received_payments_accounts"    => NULL, // in this situation the there will be no accounts. Because the balance is adding from product return
            "received_payments_sales_id"    => $sales_id,
            "received_payments_from"        => $customerId,
            "received_payments_amount"      => abs($salesChanges),
            "received_payments_method"      => $salesPayingBy,
            "received_payments_details"     => "Added as balance from return",
            "received_payments_add_by"      => $_SESSION["uid"]
        )
    );
}
*/



// Return the Success msg
if($updateSale === true) {

    // Start the mysql Transaction
    runQuery("START TRANSACTION;");

    // Delete the previous sales items while updating
    if( isset($_POST["salesId"]) ) {

        easyPermDelete(
            "product_stock",
            array(
                "stock_sales_id"    => $sales_id
            )
        );
        
    }


    // Insert sale items
    runQuery(substr_replace($insertSaleItems, ";", -1, 1));


    if( !empty($conn->get_all_error)  ) {
    
        echo json_encode(array (
            "saleStatus"   => "error",
            "msg"  =>  __("Sorry! can not update the sales. Please check the error log for more information.")
        ));

        // If there have any error then rollback/undo the data
        runQuery("ROLLBACK;");
    
    } else {
        
        // If there have not any error then commit/save the data permanently
        runQuery("COMMIT;");

        echo json_encode(array (
            "saleStatus"   => "success",
            "salesId"  =>  $sales_id
        ));

    }
    
}

?>