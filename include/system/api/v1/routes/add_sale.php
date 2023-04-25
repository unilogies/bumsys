<?php


if( empty($secretData["api_shop_id"]) or empty($secretData["api_accounts_id"]) or empty($secretData["api_warehouse_id"]) ) {
 
    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! This credentials has no permission to add sale."
    ));

} else if( empty($_POST["order_date"]) ) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Order date can not be empty."
    ));

} else if( empty($_POST["reference"]) ) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Reference can not be empty."
    ));

} else if( empty($_POST["customer_id"]) ) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Customer can not be empty."
    ));

} else if( empty($_POST["item"]) or 
            empty($_POST["item"][0]["pid"]) or 
            !isset($_POST["item"][0]["price"]) or 
            !isset($_POST["item"][0]["qty"]) or 
            !isset($_POST["item"][0]["discount"]) or 
            empty($_POST["item"][0]["subtotal"]) 
        ) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Please select as least one item."
    ));

} else if( empty($_POST["item"][0]["pid"]  ) ) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Please select as least one item."
    ));

} else if( !isset($_POST["shipping"]) or $_POST["shipping"] < 0 ) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Shipping can not be empty."
    ));

} else if( !isset($_POST["total_amount"]) or $_POST["total_amount"] < 0 ) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Total amount can not be empty."
    ));

} else if( !isset($_POST["total_amount"]) or $_POST["total_amount"] < 0 ) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Total amount can not be empty."
    ));

} else if( !isset($_POST["item_discount"]) or $_POST["item_discount"] < 0 ) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Item discount can not be empty."
    ));

} else if(!isset($_POST["order_discount"]) or $_POST["order_discount"] < 0 ) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Order discount can not be empty."
    ));

} else if( !isset($_POST["tariff_charges"]) or $_POST["tariff_charges"] < 0 ) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Tariff and Charges can not be empty."
    ));

} else if( !isset($_POST["adjustment"]) or $_POST["adjustment"] < 0 ) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Adjust amount can not be empty."
    ));

} else if( !isset($_POST["grand_total"]) or $_POST["grand_total"] < 0 ) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Grand total can not be empty."
    ));

} else if( !isset($_POST["paid_amount"]) or $_POST["paid_amount"] < 0 ) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Paid amount can not be empty."
    ));

} else if( !isset($_POST["due_amount"]) or $_POST["due_amount"] < 0 ) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Due amount can not be empty."
    ));

} else if( empty($_POST["payment_status"]) ) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Payment status can not be empty."
    ));

} else if( empty($_POST["shipping_address"]) ) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Shipping address can not be empty."
    ));

} else if( !in_array($_POST["payment_status"], array('paid', 'due', 'partial') ) ) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Payment status must be in ('paid', 'due', 'partial')."
    ));

}  else {


    // Check if the reference already exist
    $referenceIsExist = easySelectA(array(
        "table" => "sales",
        "where" => array(
            "sales_reference"  => $_POST["reference"],
        )
    ));

    if( $referenceIsExist !== false ) {
        
        echo json_encode(array(
            "status"    => "error",
            "msg"       => "Sorry! The reference number already exists."
        ));

        exit();

    }


    // Generate the payment status
    $salesPaymentStatus = "due";
    if( abs($_POST["grand_total"]) <= abs($_POST["paid_amount"])) {

        $salesPaymentStatus = "paid";

    } else if( abs($_POST["grand_total"]) > abs($_POST["paid_amount"]) and abs($_POST["paid_amount"]) > 0) {

        $salesPaymentStatus = "partial";

    }

    // Insert sales
    $insertSales = easyInsert(
        "sales",
        array (
            "sales_status"                  => "Delivered", //"Processing",
            "sales_order_date"              => $_POST["order_date"],
            "sales_delivery_date"           => $_POST["order_date"],
            "sales_reference"               => $_POST["reference"],
            "sales_customer_id"             => $_POST["customer_id"],
            "sales_warehouse_id"            => $secretData["api_warehouse_id"],
            "sales_shop_id"                 => $secretData["api_shop_id"],
            "sales_shipping"                => $_POST["shipping"],
            "sales_created_by"              => NULL,
            "sales_total_packets"           => NULL,
            "sales_tariff_charges_details"  => serialize( array("tariff" => array(), "value" => array()) ),
            "sales_by_pos"                  => 0,
            "sales_by_website"              => 1,
            "sales_note"                    => empty($_POST["sale_note"]) ? '' : $_POST["sale_note"],
            "sales_total_amount"            => $_POST["total_amount"],
            "sales_product_discount"        => $_POST["item_discount"],
            "sales_discount"                => $_POST["order_discount"],
            "sales_tariff_charges"          => $_POST["tariff_charges"],
            "sales_adjustment"              => $_POST["adjustment"],
            "sales_grand_total"             => $_POST["grand_total"],
            "sales_paid_amount"             => $_POST["paid_amount"],
            "sales_change"                  => 0,
            "sales_due"                     => $_POST["due_amount"],
            "sales_payment_status"          => $salesPaymentStatus, //$_POST["payment_status"],
            "sales_quantity"                => 0,
            "sales_shipping_address"        => $_POST["shipping_address"]
        ),
        array(),
        true
    );
    
    
    // if sale not insert then throw an error 
    if( !isset($insertSales["last_insert_id"]) ) {
    
        $returnError = array (
            "status"   => "error",
            "msg"  =>  __("An unknown error occurred. Please contact with the administrator.")
        );
    
        echo json_encode($returnError);

    } else {

        $sales_id = $insertSales["last_insert_id"];

        $insertSaleItems = "INSERT INTO {$table_prefix}product_stock(
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


        $total_item = 0;
        $total_qty = 0;

        // Insert product items into sale table
        foreach ($_POST["item"] as $item) {
            
   
            $total_item += 1;
            $total_qty += $item['qty'];

            $insertSaleItems .= "
            (
                'sale',
                '". safe_input($_POST["order_date"]) ."',
                '{$sales_id}',
                '". $secretData["api_warehouse_id"] ."',
                '". $secretData["api_shop_id"] ."',
                '". safe_input($item["pid"]) ."',
                NULL,
                '". safe_input($item['price']) ."',
                '". safe_input($item['qty']) ."',
                '". safe_input($item['discount']) ."',
                '". safe_input($item['subtotal']) ."',
                '',
                NULL,
                '". 0 ."'
            ),";

            

            // Select bundle products or sub products 
            $subProducts = easySelectA(array(
                "table"     => "products as product",
                "fields"    => "bg_item_product_id, 
                                bg_product_price as sale_price,
                                bg_product_qnt
                                ",
                "join"      => array(
                    "inner join {$table_prefix}bg_product_items as bg_product on bg_product_id = product_id"
                ),
                "where"     => array(
                    "( product.has_sub_product = 1 or product.product_type = 'Bundle' ) and bg_product.is_raw_materials = 0 and product.product_id = {$item["pid"]}"
                )
            ));
            

            // Insert sub/ bundle products
            if($subProducts !== false) {


                foreach($subProducts["data"] as $bpKey => $bp) {

                    // Store the Bundle/ Sub Product Item Sale Price
                    $bpItemSalePrice = $bp["sale_price"];

                    // Calculate the Bundle/Sub item quantity
                    $bpItemQnt = $item["qty"] * $bp["bg_product_qnt"];

                    // In bundle/Sub item, the discount takes from bundle product not from the item product
                    $bpItemDiscountAmount = calculateDiscount( $bpItemSalePrice, $item["discount"] );

                    $bpItemSubTotal = ( $bpItemSalePrice - $bpItemDiscountAmount) * $bpItemQnt;


                    $insertSaleItems .= "
                    (
                        'sale',
                        '". safe_input($_POST["order_date"]) ."',
                        '{$sales_id}',
                        '". $secretData["api_warehouse_id"] ."',
                        '". $secretData["api_shop_id"] ."',
                        '". $bp["bg_item_product_id"] ."',
                        NULL,
                        '". $bpItemSalePrice ."',
                        '". $bpItemQnt ."',
                        '". $bpItemDiscountAmount ."',
                        '". $bpItemSubTotal ."',
                        '',
                        NULL,
                        '". 1 ."'
                    ),";
                    
                }
            }

        }


        if($_POST["paid_amount"] > 0) {

            easyInsert(
                "received_payments",
                array (
                    "received_payments_type"        => "Sales Payments",
                    "received_payments_datetime"    => $_POST["order_date"] . date(" H:i:s"),
                    "received_payments_shop"        => $secretData["api_shop_id"],
                    "received_payments_accounts"    => $secretData["api_accounts_id"],
                    "received_payments_sales_id"    => $sales_id,
                    "received_payments_from"        => $_POST["customer_id"],
                    "received_payments_amount"      => $_POST["paid_amount"],
                    "received_payments_method"      => "Cash",
                    "received_payments_reference"   => $_POST["payment_reference"],
                    "received_payments_details"     => '',
                )
            );

            
            // Update Account Balance if there are in paid amount
            updateAccountBalance($secretData["api_accounts_id"]);
    
        }


        // Update the Sale 
        $updateSale = easyUpdate(
            "sales",
            array (
                "sales_total_item"    => $total_item,
                "sales_quantity"      => $total_qty
            ),
            array (
                "sales_id"  => $sales_id
            )
        );

        
        // Insert sale items
        runQuery(substr_replace($insertSaleItems, ";", -1, 1));


        echo json_encode(array (
            "status"   => "success",
            "sale_id"  => $sales_id
        ));


    }
    

}


?>