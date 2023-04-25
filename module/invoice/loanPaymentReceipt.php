<?php 

$selectLoan = easySelectA(array(
    "table"     => "loan",
    "fields"    => "loan_id, loan_amount, loan_installment_starting_from, loan_installment_amount, loan_granter, loan_details,
                    date(loan_pay_on) as loan_date, emp_firstname, emp_lastname, emp_PIN, emp_positions",
    "join"      => array(
        "left join {$table_prefix}employees on emp_id = loan_borrower"
    ),
    "where" => array(
        "loan_id"   => $_GET["id"]
    )
));

// Print error msg if there has no sales
if($selectLoan === false) {
    echo "<div class='no-print'>
            <div class='alert alert-danger'>Sorry there is no loan found!</div>
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
            <td style="padding: 0" class="col-md-3">Reference: <?php echo "LOAN_PAY/" . sprintf("%04s", $loan["loan_id"])  ; ?></td>
            <td style="padding: 0" class="col-md-3 text-right">Date: <?php echo date("d M, Y", strtotime($loan["loan_date"])); ?></td>
        </tr>
    </tbody>
</table>
<br/>
<p style="border-bottom: 3px double;text-align: center;font-size: 20px;font-weight: bold;">Loan Payment Receipt</p>
<p></p>

<?php 
$payee = $loan["emp_firstname"] . ' ' . $loan["emp_lastname"] . ' (' . $loan["emp_PIN"] . '), ' . $loan["emp_positions"];
?>
<p><strong>Payee:</strong> <?php echo $payee; ?></p>


<br/>
<p style="line-height: 24px;">
    The undersigned acknowledge that the total owed Tk. BDT <strong><?php echo number_format($loan["loan_amount"], 2); ?></strong>; The sum of <strong><?php echo spellNumbers($loan["loan_amount"]); ?></strong>.
    The loan will be adjusted from his/her salary by monthly Tk BDT <strong><?php echo number_format($loan["loan_installment_amount"], 2); ?></strong>.
</p>

<br/>
<p>
    <strong>Loan Granter: </strong> <?php echo $loan["loan_granter"]; ?> 
</p>
<p>
    <strong>Description: </strong> <?php echo $loan["loan_details"]; ?> 
</p>

<p style="display: block; margin-top: 120px;"><span style="border-top: 3px double;"> Received By </span></p>

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