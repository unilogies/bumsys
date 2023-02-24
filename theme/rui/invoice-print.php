<?php

$selectShop = easySelect(
    "shops",
    "*",
    array(),
    array(
        "shop_id" => $_SESSION["sid"]
    )
)['data'][0];

$print_type = $selectShop["shop_invoice_type"];

$maxWidth = get_options("invoiceWidth");
if( isset($_GET["paperWidth"]) and !empty($_GET["paperWidth"]) ) {

    $maxWidth = safe_entities($_GET["paperWidth"]);

} else if( $print_type === "details" and $_GET["invoiceType"] === "posSale" ) {
    
    $maxWidth = "100%";

}


?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Print Invoice</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- Include all CSS -->
    <link rel="stylesheet" href="<?php echo full_website_address(); ?>/css/">

    <style>
        #wrapper {
            max-width: <?php echo $maxWidth; ?>px;
            margin: 0 auto;
            padding-top: 20px;
        }

        @media print {

            .no-print,
            .no-print * {
                display: none !important;
            }

            .text-right,
            .text-right * {
                text-align: right;
            }

            .col-md-3 {
                width: 25%;
            }


            body * {
                font-size: "<?php echo get_options("invoiceType") === "pos" ? "11px" : "14px"; ?>";
            }

            /*
                Facing problem. Will check later. No commenting
                .rotatePrint {
                    transform: rotate(-90deg) translate(-100%, 0);
                    transform-origin: 0 0;
                    margin: auto 0 !important;;
                }
                */

        }

        .table-striped>tbody>tr>td {
            border-top: 1px solid #ddd;
            padding: 2px;
        }

        .table-condensed>tfoot>tr>th {
            border-top: 1px solid #ddd;
            padding: 2px;
        }
    </style>

</head>

<body class="hold-transition">

    <div class="rotatePrint" style="padding-top:0px;" id="wrapper">

        <?php
        // Print the msg if exitst
        if (isset($_GET["msg"])) {
            echo "<div class='no-print'>
                <div class='alert alert-success'>" . safe_input($_GET['msg']) . "</div>
              </div>";
        }

        if (isset($_GET["invoiceType"]) and !empty($_GET["invoiceType"])) {


            $invoicePage = DIR_MODULE . "invoice/". basename("{$_GET['invoiceType']}.php");

            if (file_exists($invoicePage)) {
                require $invoicePage;
            }
        }

        ?>

    </div>

</body>

</html>