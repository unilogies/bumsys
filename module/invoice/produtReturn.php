<?php 

// Select return
$selectReturn = easySelect(
    "sales",
    "*",
    array (
      "left join {$table_prefeix}customers on sales_customer_id = customer_id"
    ),
    array (
      "is_return = 1 and sales_id"  => $_GET["id"]
    )
);

//print_r($selectSale);
// Print error msg if there has no sales
if($selectReturn["count"] !== 1) {
  echo "<div class='no-print'>
          <div class='alert alert-danger'>Sorry there is now return found! Please check the return id.</div>
        </div>";
  exit();
}

// Select return item
$selectProductReturnItems = easySelectA(array(
    "table"   => "product_stock",
    "fields"  => "stock_item_price, stock_item_qty, stock_item_discount, stock_item_subtotal, product_name, if(batch_number is null, '', concat('(', batch_number, ')') ) as batch_number",
    "join"    => array(
      "left join {$table_prefeix}products on stock_product_id = product_id",
      "left join {$table_prefeix}product_batches as product_batches on stock_product_id = product_batches.product_id and stock_batch_id = batch_id"
    ),
    "where" => array(
      "is_bundle_item = 0 and stock_sales_id"  => $_GET["id"],
    )
));

$return = $selectReturn["data"][0];

?>

<div class="shopLogo text-center">
<?php 
  $selectShop = easySelect(
    "shops", 
    "shop_name, shop_invoice_footer, shop_logo", 
    array(),
    array (
      "shop_id" => $_SESSION["sid"]
    )
);

?>
<img style="width: 486px;" src="<?php echo full_website_address() . "/images/?for=shopLogo&id=". $_SESSION["sid"]; ?>" alt="<?php echo $selectShop['data'][0]['shop_name']; ?>">
<p></p>
</div>

<table> 
<tbody>
  <tr>
    <td style="padding: 0" class="col-md-3">Reference No: <?php echo  $return["sales_reference"]; ?></td>
    <td style="padding: 0" class="col-md-3 text-right">Date: <?php echo date("d/m/Y", strtotime($return["sales_delivery_date"])); ?></td>
  </tr>
  <tr>
    <td colspan="2"><strong>Customer: <?php echo !empty($return["customer_name_in_local_len"]) ? $return["customer_name_in_local_len"] : $return["customer_name"] ?> (<?php echo $return["customer_phone"] ?>) </strong> </td>
  </tr>
</tbody>
</table>

<br/>

<table class="table table-striped table-condensed">
<tbody>
  <tr>
    <td>সংখ্যা</td>
    <td>বিবরণ</td>
    <td>মূল্য</td>
    <td class="text-right">মোট টাকা</td>
  </tr>
  <?php 

    foreach($selectProductReturnItems["data"] as $key => $returnItems) {
      echo "<tr>";
      echo " <td>{$returnItems['stock_item_qty']}</td>";
      echo " <td>{$returnItems['product_name']}</td>";
      echo " <td>" . number_format($returnItems['stock_item_price'],2) . "</td>";
      echo " <td class='text-right'>" . number_format($returnItems['stock_item_subtotal'],2) . "</td>";
      echo "</tr>";

    }

  ?>      

</tbody>

<tfoot>

  <tr>
    <th colspan="3" class="text-right">Total:</th>
    <th class="text-right"> <?php echo number_format( $return["sales_total_amount"], 2) ?></th>
  </tr>

  <!-- If no discount found then hide the discount row -->
  <?php if( $return["sales_product_discount"] > 0 or $return["sales_discount"] > 0 ) { ?>
    <tr>
      <th colspan="3" class="text-right">Discount:</th>
      <th class="text-right">(-) <?php echo number_format( ($return["sales_product_discount"] + $return["sales_discount"] ), 2) ?></th>
    </tr>
  <?php } ?>

  <!-- If Surcharge found then hide the tax or shipping row -->
  <?php if( $return["sales_surcharge"] > 0 ) { ?>
    <tr>
      <th colspan="3" class="text-right">Surcharge:</th>
      <th class="text-right">(-) <?php echo number_format($return["sales_surcharge"], 2) ?></th>
    </tr>
  <?php } ?>
  
  <tr>
    <th colspan="3" class="text-right">Grand Total:</th>
    <th class="text-right"><?php echo number_format($return["sales_grand_total"], 2) ?></th>
  </tr>

</tfoot>

</table>

<div class="no-print">
<hr/>
<div class="form-group">
  <a class="btn btn-primary" href="<?php echo full_website_address(); ?>/stock-management/new-sales-return/">Back to Return</a>
  <button type="button" onclick="print();" class="btn btn-primary">Print</button>
</div>
</div>