<?php

  if(isset($_GET["cid"])) {
    require "expense-report/expense-report-single.php";
  } else if(isset($_GET["paymentType"])) {
    require "expense-report/expense-report-non-cat.php";
  } else { 
    require "expense-report/expense-report-all.php";
  }

?>