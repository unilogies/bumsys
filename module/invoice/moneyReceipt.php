<?php 

// Select Received payments
$selectPayment = easySelectA(array(
  "table" => "received_payments",
  "fields" => "received_payments_id, received_payments_datetime, received_payments_type, received_payments_accounts, received_payments_shop, accounts_name, customer_name, received_payments_amount, received_payments_details, received_payments_method, received_payments_cheque_no, received_payments_cheque_date, received_payments_reference, upazila_name, district_name",
  "join"  => array(
    "left join {$table_prefeix}accounts on accounts_id = received_payments_accounts",
    "left join {$table_prefeix}customers on customer_id = received_payments_from",
    "left join {$table_prefeix}upazilas on customer_upazila = upazila_id",
    "left join {$table_prefeix}districts on customer_district = district_id"
  ),
  "where" => array(
    "received_payments_id"  => $_GET["id"]
  )
));


//print_r($selectSale);
// Print error msg if there has no sales
if($selectPayment["count"] !== 1) {
  echo "<div class='no-print'>
          <div class='alert alert-danger'>Sorry! there is no money receipt found!.</div>
        </div>";
  exit();
}

$payment = $selectPayment["data"][0];

?>

<div class="shopLogo text-center">
    <?php 
  $selectShop = easySelect(
    "shops", 
    "shop_name, shop_logo", 
    array(),
    array (
      "shop_id" => $payment["received_payments_shop"]
    )
  );


?>
    <img style="width: 486px;" src="<?php echo full_website_address() . "/images/?for=shopLogo&id=". $_SESSION["sid"]; ?>" alt="<?php echo $selectShop['data'][0]['shop_name']; ?>">

    <p></p>
</div>

<table>
    <tbody>
        <tr>
            <td style="padding: 0" class="col-md-3">Reference No: RSP/receipt-<?php echo $payment["received_payments_id"]; ?></td>
            <td style="padding: 0" class="col-md-3 text-right">Date: <?php echo date("d/m/Y", strtotime($payment["received_payments_datetime"])); ?></td>
        </tr>
    </tbody>
</table>

<br />
<p style="border-bottom: 3px double;text-align: center;font-size: 20px;font-weight: bold;">Payment Receipt</p>
<p>
  Received with thanks from 
  <span style="border-bottom: 1.5px dotted; font-weight: bold;"><?= $payment["customer_name"]; ?>, <?= $payment["upazila_name"]; ?>, <?= $payment["district_name"]; ?></span> 
  of <span style="border-bottom: 1.5px dotted; font-weight: bold;"><?= to_money($payment["received_payments_amount"]); ?></span> 
  the sum of <span style="font-weight: bold;"><?= spellNumbers($payment["received_payments_amount"]); ?></span>.
</p>
<p>
  <b>Payment Method: </b> <?= $payment["received_payments_method"]; ?><br/>
  <b>Note: </b> <?= $payment["received_payments_details"]; ?>
</p>

<?php 

  if( $payment["received_payments_method"] === "Cheque" ) {
    echo "<strong>Cheque No: </strong> {$payment['received_payments_cheque_no']} <br/>";
    echo "<strong>Cheque Date: </strong> {$payment['received_payments_cheque_date']}<br/> ";
    echo "<strong>Reference: </strong> {$payment['received_payments_reference']} ";
  } else if( $payment["received_payments_method"] === "Bank Transfer" ) {
    echo "<strong>Bank : </strong> {$payment['accounts_name']} <br/>";
  }

?>

<p style="display: block; margin-top: 80px;"><span style="border-top: 3px double;"> Received By </span></p>

<p style="border-top: 1px solid; border-bottom: 1px solid; padding: 5px;">
  <strong>Printed On: </strong> <?php echo date("d M, Y h:i A"); ?>
</p>

<div class="no-print">
    <hr />
    <div class="form-group">
        <button type="button" onclick="window.history.back();" class="btn btn-primary">Back</button>
        <button type="button" onclick="print();" class="btn btn-primary">Print</button>
    </div>
</div>