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
    
    // Biuld the page location
    $ajaxModulePage = DIR_BASE . "core/ajax/ajax_" . $_GET['module'] . ".php";

    // Check if the ajax page is exists
    if(file_exists($ajaxModulePage)) {
         // Load the ajax module page
        require $ajaxModulePage;
    }

}


?>