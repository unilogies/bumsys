<?php 

require LOAD_LIB . "barcode-master/barcode.php";

$generator = new barcode_generator();
$format = (isset($_GET['f']) ? $_GET['f'] : 'png');
$generator->output_image($format, $_GET['s'], $_GET['d'], $_GET);

?>