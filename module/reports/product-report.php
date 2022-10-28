<?php

  if(isset($_GET["pid"])) {
    require "product-report/product-report-single.php";
  } else {
    require "product-report/product-report-all.php";
  }

?>