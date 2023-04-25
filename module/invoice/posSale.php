<?php 

/** the $print_type and $selectShop variable is declared on /theme/invoice-print.php file */

if( $print_type === "normal" ) {

    require "posSaleForNormalPrinter.php";

} else if( $print_type === "details" ) {

    require "invoicePrintDetailsView.php";

} else {
    
    require "posSaleInvoiceForPosPrinter.php";

}

?>