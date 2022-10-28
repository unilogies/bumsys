<?php

  if(isset($_GET["cid"])) {
    require "customer-report/customer-report-single.php";
  } else {
    require "customer-report/customer-report-all.php";
  }

?>