<?php
header("Cache-Control: private, max-age=10800, pre-check=10800");
header("Pragma: private");
header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));

$imageLocation = "";
$imageBloob = "";

if(isset($_GET["for"]) and isset($_GET["id"]) and $_GET["for"] === "products") {

    // Select Product Images
    $selectProductImage = easySelectA(array(
        "table"     => "products",
        "fields"    => "product_photo, product_code",
        "where"     => array(
            "product_id"    => isset($_GET["id"]) ? $_GET["id"] : ""
        )
    ));
    
    if($selectProductImage) {
        
        $imageLocation = DIR_UPLOAD . "products/" . $selectProductImage["data"][0]["product_code"] . "/" . $selectProductImage["data"][0]["product_photo"];
    
    }

} else if(isset($_GET["for"]) and isset($_GET["id"]) and $_GET["for"] === "employees") {

    // Select Employee Image
    $selectEmployeeImage = easySelectA(array(
        "table"     => "employees",
        "fields"    => "emp_PIN, emp_firstname, emp_lastname, emp_photo",
        "where"     => array(
            "emp_id"    => isset($_GET["id"]) ? $_GET["id"] : ""
        )
    ));

    //print_r($selectEmployeeImage);

    //echo $selectEmployeeImage["emp_id"];
    
    if($selectEmployeeImage) {

        $selectEmployeeImage = $selectEmployeeImage["data"][0];

        $imageLocation = DIR_UPLOAD . "employees/{$selectEmployeeImage["emp_PIN"]}. {$selectEmployeeImage["emp_firstname"]} {$selectEmployeeImage["emp_lastname"]}/" . $selectEmployeeImage["emp_photo"];

    
    }

} else if(isset($_GET["for"]) and isset($_GET["id"]) and $_GET["for"] === "shopLogo") {

    $selectShop = easySelectA(array(
        "table"     => "shops",
        "fields"    => "shop_logo",
        "where"     => array (
            "shop_id" => $_GET["id"]
        )
    ));

    if(!empty($selectShop["data"][0]["shop_logo"])) {

        $imageLocation = DIR_UPLOAD . "logos/shop/" . $selectShop["data"][0]["shop_logo"];

    }
    
}

// Check the image bloob is not empty
if(!empty($imageLocation)) {

    $if = ( isset($_GET["q"]) and !empty($_GET["q"]) ) ?  unserialize(base64_decode($_GET["q"])) : "";
    $imageType = !empty( $_GET["t"] ) ? $_GET["t"] : "image/webp";

    header('Content-Type: '. $imageType);

    $image = imagecreatefromstring( file_get_contents($imageLocation) );

    // Change the image dimention
    if( isset($if["ih"]) and isset($if["iw"]) and (int)$if["ih"] > 0 and (int)$if["iw"] > 0 ) {

        $image = imagescale($image, $if["iw"], $if["ih"]);

    }

    // Change the image qualtiy
    $quality = ( isset($if["iq"]) and (int)$if["iq"] > 0 ) ? $if["iq"] : 100;

    // Display the image based on type
    if( $imageType === "image/jpeg" ) {

        echo imagejpeg($image, NULL, $quality);

    } else if( $imageType === "image/png" ) { 

        // Transparent image alpha blending
        imageAlphaBlending($image, true);
        imageSaveAlpha($image, true);

        // PNG/ Webp image Compression level 0-9. 
        // Where jpeg is 0-100
        echo imagepng($image, NULL, ($quality/10)-1 );

    } else if( $imageType === "image/gif" ) {

        echo imagegif($image);

    } else if( $imageType === "image/webp" ) {

        // Convert to RGB
        // This is required to creating webp
        imagepalettetotruecolor($image);
        echo imagewebp($image, NULL, $quality);

    }

    // Destroy the image
    imagedestroy($image);


} else {

    // display 404 page
    require ERROR_PAGE . "404.php";

}








?>