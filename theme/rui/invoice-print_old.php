<?php

if(!access_is_permitted()) {
  require ERROR_PAGE . "501.pho";
  exit();
}

if(is_login() !== true) {
  $rdr_to = full_website_address()."/login/";
  header("location: {$rdr_to}");
  exit();
}

$maxWidth = isset($_GET["paperWidth"]) ? $_GET["paperWidth"] : "480";

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
    .no-print, .no-print *  {
        display: none !important;
    }
    .text-right, .text-right * {
      text-align: right;
    }
    .col-md-3 {
      width: 25%;
    }
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

  <div id="wrapper">

    <?php 
      // Print the msg if exitst
      if(isset($_GET["msg"])) {
        echo "<div class='no-print'>
                <div class='alert alert-success'>". safe_input($_GET['msg']) ."</div>
              </div>";
      } 

      if(isset($_GET["invoiceType"]) and !empty($_GET["invoiceType"])) {
        $invoicePage = DIR_THEME . "invoice/{$_GET['invoiceType']}.php";

        if(file_exists($invoicePage)) {
          require $invoicePage;
        }

      }

    ?>

  </div>

</body>
</html>
