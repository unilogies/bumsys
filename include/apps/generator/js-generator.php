<?php
header("Cache-Control: private, max-age=10800, pre-check=10800");
header("Pragma: private");
header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));

header("Content-type: application/javascript; charset: UTF-8");


if( isset($_GET["q"]) and $_GET["q"] === "head" ) {

    // Include jQuery 3
    include( DIR_ASSETS . "3rd-party/jquery/jquery-3.5.1.min.js");

    // Include jQuery 3
    include( DIR_ASSETS . "3rd-party/jquery-ui/jquery-ui-custom.min.js");

    // Include Moment Js
    include( DIR_ASSETS . "3rd-party/daterangepicker-master/moment.min.js");
    
    // Include Datepicker for multi date picker
    include( DIR_ASSETS . "3rd-party/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js");

    // Include daterange
    include( DIR_ASSETS . "3rd-party/daterangepicker-master/daterangepicker.min.js");

    // functions 
    include( DIR_ASSETS . "js/functions.min.js");

    // Include BMS JS
    include( DIR_ASSETS . "js/bms.min.js");


} else if( isset($_GET["q"]) and $_GET["q"] === "foot" ) {

    // Include Sweet Alert
    include( DIR_ASSETS . "3rd-party/sweetalert2-master/sweetalert2.all.min.js");

    // Include Bootstrap
    include( DIR_ASSETS . "3rd-party/bootstrap/dist/js/bootstrap.min.js");

    // Include Datatables
    include( DIR_ASSETS . "3rd-party/datatables.lib/v1.10.24/datatables.min.js");

    // Include Datatables Checkbox
    include( DIR_ASSETS . "3rd-party/datatables.lib/dataTables.checkboxes.min.js");

    // Include Select2
    include( DIR_ASSETS . "3rd-party/select2/dist/js/select2.full.min.js");

    // Include AdmiinLTE APP
    include( DIR_THEME . "dist/js/script.min.js");

    // Include Pace
    include( DIR_ASSETS . "3rd-party/pace/pace.min.js");

    // Include iCheck
    include( DIR_ASSETS . "3rd-party/iCheck/icheck.min.js");

    // Include slim scroll
    include( DIR_ASSETS . "3rd-party/jquery-slimscroll/jquery.slimscroll.min.js");
    

    // Include events and initiator
   include( DIR_ASSETS . "js/events.min.js");
   include( DIR_ASSETS . "js/initiator.min.js");

} 
?>