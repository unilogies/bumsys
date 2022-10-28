<?php
header("Cache-Control: private, max-age=10800, pre-check=10800");
header("Pragma: private");
header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));

header("Content-type: text/css; charset: UTF-8");

// Include betstrep
include( DIR_ASSETS . "3rd-party/bootstrap/dist/css/bootstrap.min.css");

// Include Fontawsome
include( DIR_ASSETS . "3rd-party/font-awesome/css/font-awesome.min.css");

// Include Datatables
include( DIR_ASSETS . "3rd-party/datatables.lib/v1.10.24/datatables.min.css");

// Include Checkbox
include( DIR_ASSETS . "3rd-party/datatables.lib/dataTables.checkboxes.css");

// Include Select2
include( DIR_ASSETS . "3rd-party/select2/dist/css/select2.min.css");

// Include Date Range Picker Bs
//include( DIR_ASSETS . "3rd-party/bootstrap-daterangepicker-month-view/daterangepicker-bs3.css");

// Include Date Range Css
include( DIR_ASSETS . "3rd-party/daterangepicker-master/daterangepicker.min.css");

// Include Admin LET Theme Style
include( DIR_THEME . "dist/css/AdminLTE.min.css");

// Include Pace
include( DIR_ASSETS . "3rd-party/pace/pace.min.css");

/**
 * AdminLTE Skins. We have chosen the skin-blue for this starter
  *          page. However, you can choose any other skin. Make sure you
  *         apply the skin class to the body tag so the changes take effect
 */
include( DIR_THEME . "dist/css/skins/skin-blue.min.css");

// Include bootstrap datepicker
include( DIR_ASSETS . "3rd-party/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css");

// Include Icheck
include( DIR_ASSETS . "3rd-party/iCheck/square/blue.css");



?>