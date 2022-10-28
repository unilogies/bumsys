<?php

if( empty($secretData["api_shop_id"]) or empty($secretData["api_accounts_id"]) or empty($secretData["api_warehouse_id"]) ) {
 
    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! This credentials has no permission to add sale."
    ));

} else if( empty($_POST["order_date"]) ) {



}


exit();



$insertSales = easyInsert(
    "sales",
    array (
        "sales_status"                  => "Processing",
        "sales_order_date"              => $_POST["order_date"],
        "sales_delivery_date"           => null,
        "sales_reference"               => $_POST["reference"],
        "sales_customer_id"             => $_POST["customer_id"],
        "sales_warehouse_id"            => $secretData["api_warehouse_id"],
        "sales_shop_id"                 => $secretData["api_shop_id"],
        "sales_quantity"                => array_sum( $_POST["item_qty"] ),
        "sales_shipping"                => $_POST["shipping"],
        "sales_created_by"              => NULL,
        "sales_total_item"              => count( $_POST["item_qty"] ),
        "sales_total_packets"           => NULL,
        "sales_tariff_charges_details"  => serialize( array("tariff" => array(), "value" => array()) ),
        "sales_by_pos"                  => 0,
        "sales_by_website"              => 1,
        "sales_note"                    => $_POST["sale_note"],
        "sales_total_amount"            => $_POST["total_amount"],
        "sales_product_discount"        => $_POST["product_discount"],
        "sales_discount"                => $_POST["sales_discount"],
        "sales_tariff_charges"          => $_POST["tariff_charges"],
        "sales_adjustment"              => $_POST["adjustment"],
        "sales_grand_total"             => $_POST["grand_total"],
        "sales_paid_amount"             => $_POST["paid_amount"],
        "sales_change"                  => 0,
        "sales_due"                     => $_POST["due_amount"],
        "sales_payment_status"          => $_POST["payment_status"]
    ),
    array(),
    true
);



?>