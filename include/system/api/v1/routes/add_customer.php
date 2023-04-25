<?php

if(empty($_POST["customer_name"])) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Customer name can not be empty."
    ));

} else if(empty($_POST["customer_phone"])) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Customer phone can not be empty."
    ));

} else if(empty($_POST["customer_address"])) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Customer address can not be empty."
    ));

} else if(empty($_POST["customer_postal_code"])) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Customer postal code can not be empty."
    ));

} else if(empty($_POST["customer_country"])) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Customer country can not be empty."
    ));

} else {


    // Check if the customer already exists
    $customerIsExists = easySelectA(array(
        "table" => "customers",
        "where" => array(
            "customer_type = 'Consumer' and customer_phone" => $_POST["customer_phone"]
        )
    ));

    if( $customerIsExists !== false ) {

        echo json_encode(array(
            "status"        => "error",
            "customer_id"   => $customerIsExists["data"][0]["customer_id"],
            "msg"           => "Sorry! A customer already exists with this number."
        ));

        exit();
    
    }

    // Insert the customer into database
    $insertCustomer = easyInsert(
        "customers",
        array(
            "customer_name"             => $_POST["customer_name"],
            "customer_name_in_local_len"=> '',
            "customer_type"             => 'Consumer',
            "customer_opening_balance"  => 0,
            "customer_balance"          => 0,
            "customer_due"              => 0,
            "customer_shipping_rate"    => 0,
            "customer_discount"         => 0,
            "customer_upazila"          => empty($_POST["customer_upazila"]) ? NULL : $_POST["customer_upazila"],
            "customer_district"         => empty($_POST["customer_district"]) ? NULL : $_POST["customer_district"],
            "customer_division"         => empty($_POST["customer_division"]) ? NULL : $_POST["customer_division"],
            "customer_address"          => $_POST["customer_address"],
            "customer_postal_code"      => $_POST["customer_postal_code"],
            "customer_country"          => $_POST["customer_country"],
            "customer_phone"            => $_POST["customer_phone"],
            "send_notif"                => 0,
            "customer_email"            => empty($_POST["customer_email"]) ? '' : $_POST["customer_email"],
            "customer_website"          => ''
        ),
        array(),
        true
    );

    
    if( isset($insertCustomer["last_insert_id"]) ) {

        echo json_encode(array (
            "status"   => "success",
            "customer_id"  => $insertCustomer["last_insert_id"],
            "msg"       => "The customer has been added successfully."
        ));

    } else {
        
        echo json_encode(array (
            "status"   => "error",
            "msg"       => "An unknown error occurred. Please contact with administrator"
        ));

    }

}
   
    



?>