<?php 
  // Max width = 360px;
  // Select Advance Payments
  $selectAdvanePayments = easySelectA(array(
      "table"     => "advance_payments as advance_payment",
      "fields"    => "advance_payment_id, advance_payment_date, advance_payment_reference, advance_payment_payment_method, emp_PIN, emp_positions, emp_firstname, emp_lastname, accounts_name, advance_payment_amount, advance_payment_description",
      "join"      => array(
          "left join {$table_prefeix}employees on advance_payment_pay_to = emp_id",
          "left join {$table_prefeix}accounts on advance_payment_pay_from = accounts_id"
      ),
      "where" => array(
          "advance_payment.is_trash=0 and advance_payment.advance_payment_id" => $_GET["id"]
      ),
  ))["data"][0];

  // Print error msg if there has no sales
  if(!is_array($selectAdvanePayments)) {
    echo "<div class='no-print'>
            <div class='alert alert-danger'>Sorry there is no payment found! Please check the payment id.</div>
          </div>";
    exit();
  }

  
?>

<div class="shopLogo text-center">
  <h3 style="font-weight: bold; font-size: 21.5px; text-transform: uppercase; margin: 0;"><?php echo get_options("companyName"); ?></h3>
  <p class="text-center">32-Purana Paltan, Dhaka-1000</p>
  <p></p>
</div>

<table class="no-border"> 
  <tbody>
    <tr>
      <td style="padding: 0" class="col-md-3">Reference: <?php echo $selectAdvanePayments["advance_payment_reference"]; ?></td>
      <td style="padding: 0" class="col-md-3 text-right">Date: <?php echo date("d M, Y", strtotime($selectAdvanePayments["advance_payment_date"])); ?></td>
    </tr>
  </tbody>
</table>
<br/>
<p style="border-bottom: 3px double;text-align: center;font-size: 20px;font-weight: bold;">Advance Payment Receipt</p>
<p></p>
<p><strong>Payee:</strong> <?php echo $selectAdvanePayments["emp_firstname"] . ' ' . $selectAdvanePayments["emp_lastname"] . ' (' . $selectAdvanePayments["emp_PIN"] . '), ' . $selectAdvanePayments["emp_positions"]; ?></p>

<table class="table">
  <thead>
    <tr>
      <th>Type</th>
      <th class="text-right">Amount</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Advance Payment</td>
      <td class="text-right"><?= to_money($selectAdvanePayments["advance_payment_amount"]); ?></td>
    </tr>
  </tbody>
  <tfoot>
      <tr>
        <th>Total:</th>
        <th class="text-right"><?php echo to_money($selectAdvanePayments["advance_payment_amount"]); ?></th>
      </tr>
  </tfoot>

</table>

<p style="line-height: 24px;">Received Tk. BDT <strong><?php echo to_money($selectAdvanePayments["advance_payment_amount"]); ?></strong>; The sum of <strong><?php echo spellNumbers($selectAdvanePayments["advance_payment_amount"]); ?></strong>. </p>

<p>
  <strong>Description: </strong> <?php echo $selectAdvanePayments["advance_payment_description"]; ?> 
</p>
<p>
  <strong>Payment Method:</strong> <?php echo $selectAdvanePayments["advance_payment_payment_method"]; ?> <br/>
</p>

<p style="display: block; margin-top: 80px;"><span style="border-top: 3px double;"> Received By </span></p>

<p style="border-top: 1px solid; border-bottom: 1px solid; padding: 5px;">
  <strong>Printed On: </strong> <?php echo date("d M, Y h:i A"); ?>
</p>

<div class="no-print">
  <hr/>
  <div class="form-group">
    <a class="btn btn-primary" href="<?php echo full_website_address(); ?>/expenses/advance-bill-payments/advance-payments-list/">Back to Advance Payments</a>
    <button type="button" onclick="print();" class="btn btn-primary">Print</button>
  </div>
</div>