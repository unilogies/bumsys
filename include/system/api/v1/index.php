<?php

/**
 * Api version 1
 */

// Get all headers
$header = getallheaders();

// print_r($header);

if( !isset($header["Secret"]) or empty($header["Secret"]) ) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! No secret key found."
    ));

    exit();

}

// Check if the secret is valid or not
$selectSecret = easySelectA(array(
    "table"     => "api_secrets",
    "where"     => array(
        "is_trash = 0 and api_status = 'Active' and api_secret_key"    => $header["Secret"]
    )
));



if( $selectSecret === false ) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! Invalid credentials is given."
    ));

    exit();

}


if( !isset($header["Route"]) or empty($header["Route"]) ) {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! No route found."
    ));

    exit();

}

// Get the route
$route = $header["Route"];


$api_module = SYSTEM_API . "v1/routes/{$route}.php";

if( file_exists($api_module) ) {

    $secretData = $selectSecret["data"][0];

    require_once($api_module);
    

} else {

    echo json_encode(array(
        "status"    => "error",
        "msg"       => "Sorry! The given route is invalid or not found."
    ));

}


?>