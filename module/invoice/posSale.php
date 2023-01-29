<?php 

$print_type = get_options("printerType");

if( $print_type === "normal" ) {
    require "posSaleForNormalPrinter.php";
} else {
    require "posSaleInvoiceForPosPrinter.php";
}

?>