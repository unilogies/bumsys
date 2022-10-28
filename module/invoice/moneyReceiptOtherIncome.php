<?php 

// Select Received incomes
$selectIncome = easySelectA(array(
  "table" => "incomes",
  "where" => array(
    "incomes_id"  => $_GET["id"]
  )
));


// Print error msg if there has no sales
if($selectIncome["count"] !== 1) {
  echo "<div class='no-print'>
          <div class='alert alert-danger'>Sorry! there is no money receipt found!.</div>
        </div>";
  exit();
}

$income = $selectIncome["data"][0];

?>

<div class="shopLogo text-center">
  <h3 style="font-weight: bold; font-size: 21.5px; text-transform: uppercase; margin: 0;"><?php echo get_options("companyName"); ?></h3>
  <p class="text-center">32-Purana Paltan, Dhaka-1000</p>
  <p></p>
</div>

<table>
    <tbody>
        <tr>
            <td style="padding: 0" class="col-md-3">Reference No: RSP/receipt-<?php echo $income["incomes_id"]; ?></td>
            <td style="padding: 0" class="col-md-3 text-right">Date: <?php echo date("d/m/Y", strtotime($income["incomes_date"])); ?></td>
        </tr>
    </tbody>
</table>

<br />
<p style="border-bottom: 3px double;text-align: center;font-size: 20px;font-weight: bold;">Money Receipt</p>
<p>
  Received with thanks <span style="border-bottom: 1.5px dotted; font-weight: bold;"><?= to_money($income["incomes_amount"]); ?></span> 
  , the sum of <span style="font-weight: bold;"><?= spellNumbers($income["incomes_amount"]); ?></span>.
</p>
<p>
  <b>Note: </b> <?= $income["incomes_description"]; ?>
</p>

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