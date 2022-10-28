<?php

/* These are not required. Now commenting. Will check later
if (!access_is_permitted()) {
    require ERROR_PAGE . "501.php";
    exit();
}

if (is_login() !== true) {
    $rdr_to = full_website_address() . "/login/";
    header("location: {$rdr_to}");
    exit();
}
*/

$maxWidth = isset($_GET["paperWidth"]) ? $_GET["paperWidth"] : get_options("invoiceWidth");

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
                font-size: "<?php echo get_options("printerType") === "normal" ? "14px" : "11px"; ?>";
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
            $invoicePage = DIR_MODULE . "invoice/{$_GET['invoiceType']}.php";

            if (file_exists($invoicePage)) {
                require $invoicePage;
            }
        }

        ?>

    </div>

</body>

</html>