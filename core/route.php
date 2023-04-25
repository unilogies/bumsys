<?php

// Remove directory browsing elements
$pageSlug = str_replace(
    array(
        "./",
        "../",
        ".",
        ".."
    ),
    "",
    $pageSlug
);


// Build the module page link
$ModulePageLink = DIR_MODULE . $pageSlug . ".php";


// Check if current user has access to access current page
if(is_login() === true and !current_user_can_visit_this_page()) {
    require ERROR_PAGE . "403.php";
    exit();

}


// increase the $_SESSION["LAST_ACTIVITY"] time
// The LAST_ACTIVITY increasing time session must be declared after is_login check.
$_SESSION["LAST_ACTIVITY"] = time();


$staticPage = array (
    "invoice-print" => DIR_THEME . "invoice-print.php",
    "print"         => DIR_THEME . "print.php",
    "api/v1"        => SYSTEM_API . "v1/index.php",
    "login"         => SYSTEM_DOOR . "login.php",
    "logout"        => SYSTEM_DOOR . "logout.php",
    "xhr"           => APPS . "xhr/index.php", 
    "info"          => APPS . "xhr/info.php",
    "images"        => APPS . "generator/image-generator.php",
    "barcode"       => APPS . "generator/barcode-generator.php",
    "css"           => APPS . "generator/css-generator.php",
    "js"           => APPS . "generator/js-generator.php"
);


if(is_home()) {

    header("location: home/");

} else if(array_key_exists($pageSlug, $staticPage) and file_exists($staticPage[$pageSlug])) {

    // CSRF protection
    // check if X_CSRF_TOKEN is set or not and is not match with session, then block to load page.
    if( ($pageSlug === "xhr" or $pageSlug === "info") and ( !isset($_SERVER["HTTP_X_CSRF_TOKEN"]) or (isset($_SERVER["HTTP_X_CSRF_TOKEN"]) and $_SERVER["HTTP_X_CSRF_TOKEN"] !== $_SESSION["csrf_token"] ) )  ) {
        
        header('HTTP/1.0 403 Forbidden');
        die("<strong>Error:</strong> You have no permission to access this server.");

    }

    // This is for static page loading
    require $staticPage[$pageSlug];
    

} else if(file_exists($ModulePageLink) and strtolower(basename($ModulePageLink)) !== "ajax.php" ) {
    
    // This is for dynamic page loading based on url
    require "loadModule.php";
    
}  else {

    header('HTTP/1.0 404 Not Found');
    require ERROR_PAGE . "404.php";

}


?>