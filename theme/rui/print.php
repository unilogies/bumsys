<?php

$maxWidth = isset($_GET["paperWidth"]) ? safe_entities($_GET["paperWidth"]) ."px" : get_options("invoiceWidth");

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Print</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  <!-- Include all CSS -->
  <link rel="stylesheet" href="<?php echo full_website_address(); ?>/css/">

  <style>
  #wrapper {
    max-width: <?php echo $maxWidth; ?>;
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
    body {
      font-size: 10px;
    }

    th.rotate > div > span {
      font-size: 9px !important;
    }

    th.rotate {
      height: 100px !important;
      line-height: .85 !important;
    }

    th.rotate > div {
      width: 10px !important;
    }

    body * {
        font-size: 14px;
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

  body {
    overflow: auto;
    font-size: 14px;
  }

  th.rotate {
    height: 200px;
    white-space: nowrap;
    overflow: hidden;
  }

  th.rotate > div {
    transform: rotate(-90deg);
    width: 25px;
    vertical-align: middle;
    font-size: 14px;
  }


  </style>
</head>
<body>
    <div id="wrapper">

        <?php 

        // Print the msg if exitst
        if(isset($_GET["msg"])) {
            echo "<div class='no-print'>
                    <div class='alert alert-success'>". safe_entities($_GET['msg']) ."</div>
                </div>";
        } 

        if(isset($_GET["page"]) and !empty($_GET["page"])) {

            
            $printPage = DIR_MODULE . "print/". basename("{$_GET['page']}.php");

            if(file_exists($printPage)) {
                require $printPage;
            }

        }

        ?>

    </div>

</body>
</html>
