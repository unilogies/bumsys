<?php

/* These are not required. Now commenting. Will check later
if(!access_is_permitted(true)) {
    require ERROR_PAGE . "403.php";
    exit();
}

if(is_login() !== true) {
    exit();
}
*/

if(isset($_GET['module'])) {

    $module = $_GET['module'];

    // Remove directory browsing elements
    $module = str_replace(
        array(
            "./",
            "../",
            ".",
            ".."
        ),
        "",
        $module
    );
    
    // Build the page location
    $ajaxModulePage = DIR_MODULE . $module . "/ajax.php";

    // Check if the ajax page is exists
    if(file_exists($ajaxModulePage)) {
         // Load the ajax module page
        require $ajaxModulePage;

        // If need iCheck then include it.
        if(isset($_GET["icheck"]) and $_GET["icheck"] === "true") {
            echo "<script> $('input[type=\"checkbox\"].square').iCheck({ checkboxClass: 'icheckbox_square-blue', }) </script>";
        }
        
        // If need tooltip then include it.
        if(isset($_GET["tooltip"]) and $_GET["tooltip"] === "true") {
            echo "<script> $(document).ready(function() { $('[data-toggle=\"tooltip\"]').tooltip(); }); </script>";
        }
        
        // If need select2 then include it.
        if(isset($_GET["select2"]) and $_GET["select2"] === "true") {
            echo "<script> $(document).ready(function() { $('.select2').select2(); }); </script>";
        }

    } else {

        echo "<div class='alert alert-danger'>Invalid module url. Please check the form action.</div>";

    }

} else {
    echo "<div class='alert alert-danger'>Invalid module url. Please check the form action.</div>";
}


?>