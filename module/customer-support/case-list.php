<?php 

if(isset( $_GET["case_id"] )) {

    require "case-reply-view.php";

} else {
    require "case-list-all.php";
}

?>