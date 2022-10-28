<?php

/* These are not required. Now commenting. Will check later
if(!access_is_permitted()) {
    require DIR_THEME . "501.php";
    exit();
}
*/

if( isset( $_GET['contentOnly'] ) and $_GET['contentOnly'] === "true") {

    // Load only the module page
    require $ModulePageLink;

} else {

    // Load full page
    require DIR_THEME . "header.php"; 
    require $ModulePageLink;
    require DIR_THEME . "footer.php";

}


?>
