<?php 

// Select Journal Record
$selectJournalRecord = easySelectA(array(
  "table" => "journal_records",
  "join"  => array(
    "left join {$table_prefeix}journals on journals_id = journal_records_journal_id"
  ),
  "where" => array(
    "journal_records_id"  => $_GET["id"]
  )
));



// Print error msg if there has no sales
if($selectJournalRecord["count"] !== 1) {
  echo "<div class='no-print'>
          <div class='alert alert-danger'>Sorry! there is no money receipt found!.</div>
        </div>";
  exit();
}

$journal = $selectJournalRecord["data"][0];

?>

<div class="shopLogo text-center">
  <h3 style="font-weight: bold; font-size: 21.5px; text-transform: uppercase; margin: 0;"><?php echo get_options("companyName"); ?></h3>
  <p class="text-center">32-Purana Paltan, Dhaka-1000</p>
  <p></p>
</div>

<table>
    <tbody>
        <tr>
            <td style="padding: 0" class="col-md-3">Reference No: <?php echo $journal["journal_records_reference"]; ?></td>
            <td style="padding: 0" class="col-md-3 text-right">Date: <?php echo date("d/m/Y", strtotime($journal["journal_records_datetime"])); ?></td>
        </tr>
    </tbody>
</table>

<br />
<p style="border-bottom: 3px double;text-align: center;font-size: 20px;font-weight: bold;">Money Receipt</p>
<p><b>Journal Name:</b> <?= $journal["journals_name"]; ?></p>

<table class="table">
  <thead>
    <tr>
      <th>Type</th>
      <th class="text-right">Amount</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?= $journal["journal_records_payments_type"]; ?> Payment</td>
      <td class="text-right"><?php echo number_format($journal["journal_records_payment_amount"], 2); ?></td>
    </tr>
  </tbody>
  <tfoot>
      <tr>
        <th>Total:</th>
        <th class="text-right"><?php echo number_format($journal["journal_records_payment_amount"], 2); ?></th>
      </tr>
  </tfoot>

</table>

<p style="line-height: 24px;"><b>In Word:</b> <?php echo spellNumbers($journal["journal_records_payment_amount"]); ?> </p>

<p>
  <b>Note: </b> <?= $journal["journal_records_narration"]; ?>
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