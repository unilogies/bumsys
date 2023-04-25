<?php 


    $selectLoan = easySelectA(array(
        "table"     => "loan",
        "fields"    => "emp_firstname, emp_lastname, loan_amount, loan_installment_interval, ",
        "join"      => array(
            "left join {$table_prefix}employees on loan_borrower = emp_id"
        )
    ));

    // Print error msg if there has no sales
    if($selectLoan === false) {
        echo "<div class='no-print'>
                <div class='alert alert-danger'>Sorry there is no payment found! Please check the payment id.</div>
            </div>";
        exit();
    }


    $loan = $selectLoan["data"][0];
  
?>

<div class="shopLogo text-center">
  <h3 style="font-weight: bold; font-size: 21.5px; text-transform: uppercase; margin: 0;"><?php echo get_options("companyName"); ?></h3>
  <p class="text-center">32-Purana Paltan, Dhaka-1000</p>
  <p></p>
</div>

<table class="no-border"> 
  <tbody>
    <tr>
      <td style="padding: 0" class="col-md-3">Reference: <?php echo $selectPayment["payment_reference"]; ?></td>
      <td style="padding: 0" class="col-md-3 text-right">Date: <?php echo date("d M, Y", strtotime($selectPayment["payment_date"])); ?></td>
    </tr>
  </tbody>
</table>
<br/>
<p style="border-bottom: 3px double;text-align: center;font-size: 20px;font-weight: bold;">Payment Receipt</p>
<p></p>
<?php 
  $payee = "";
  if(!empty($selectPayment["emp_firstname"])) {
    $payee = $selectPayment["emp_firstname"] . ' ' . $selectPayment["emp_lastname"] . ' (' . $selectPayment["emp_PIN"] . '), ' . $selectPayment["emp_positions"];
  } else {
    $payee = $selectPayment["company_name"];
  }
?>
<p><strong>Payee:</strong> <?php echo $payee; ?></p>

<table class="table">
  <thead>
    <tr>
      <th>Type</th>
      <th class="text-right">Amount</th>
    </tr>
  </thead>
  <tbody>
    <?php 


      $selectPaymentItems = easySelectA(array(
        "table" => "payment_items",
        "fields" => " combine_description(payment_items_type, payment_category_name) as payment_type, payment_items_amount, payment_items_description",
        "join"  => array(
          "left join {$table_prefix}payments_categories on payment_items_category_id = payment_category_id"
        ),
        "where" => array (
          "payment_items_payments_id" => $_GET["id"]
        )
      ))["data"];

      foreach($selectPaymentItems as $paymentItem) {
        
        $paymentDescription = (!empty($paymentItem['payment_items_description'])) ? " ({$paymentItem['payment_items_description']})" : "";

        echo "<tr>";
        echo "<td> ". $paymentItem['payment_type'] . $paymentDescription . " </td>";
        echo "<td class='text-right'> ". number_format($paymentItem['payment_items_amount'], 2) ." </td>";
        echo "</tr>";
      }
    ?>
  </tbody>
  <tfoot>
      <tr>
        <th>Total:</th>
        <th class="text-right"><?php echo number_format($selectPayment["payment_amount"], 2); ?></th>
      </tr>
  </tfoot>

</table>

<p style="line-height: 24px;">Received Tk. BDT <strong><?php echo number_format($selectPayment["payment_amount"], 2); ?></strong>; The sum of <strong><?php echo spellNumbers($selectPayment["payment_amount"]); ?></strong>. </p>

<p>
  <strong>Description: </strong> <?php echo $selectPayment["payment_description"]; ?> 
</p>
<p>
  <strong>Payment Method:</strong> <?php echo $selectPayment["payment_method"]; ?> <br/>
  <?php 
    if($selectPayment["payment_method"] === "Cheque") {
      echo "<strong>Cheque No: </strong> {$selectPayment['payment_cheque_no']} <br/>";
      echo "<strong>Cheque Date: </strong> {$selectPayment['payment_cheque_date']} ";
    }
  ?>
  
</p>

<p style="display: block; margin-top: 80px;"><span style="border-top: 3px double;"> Received By </span></p>

<p style="border-top: 1px solid; border-bottom: 1px solid; padding: 5px;">
  <strong>Printed On: </strong> <?php echo date("d M, Y h:i A"); ?>
</p>

<div class="no-print">
  <hr/>
  <div class="form-group">
    <a class="btn btn-primary" href="<?php echo full_website_address(); ?>/expenses/payments/">Back to Payments</a>
    <button type="button" onclick="print();" class="btn btn-primary">Print</button>
  </div>
</div>