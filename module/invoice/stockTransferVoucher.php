<?php 

// Select Stock Transfer
$selectStockTransfer = easySelectA(array(
  "table"   => "stock_transfer as stock_transfer",
  "fields"  => "stock_transfer_id, stock_transfer_date, combine_description(stock_transfer_id, stock_transfer_reference) as stock_transfer_reference, stock_transfer_grand_total, stock_transfer_remarks, warehouseFrom.warehouse_name as warehouseFromName, warehouseTo.warehouse_name as warehouseToName",
  "join"    => array(
    "left join {$table_prefix}warehouses as warehouseFrom on warehouseFrom.warehouse_id = stock_transfer_from_warehouse",
    "left join {$table_prefix}warehouses as warehouseTo on warehouseTo.warehouse_id = stock_transfer_to_warehouse"
  ),
  "where" => array(
    "stock_transfer.stock_transfer_id"  => $_GET["id"]
  )
));


// Print error msg if there has no sales
if($selectStockTransfer["count"] < 1) {
  echo "<div class='no-print'>
          <div class='alert alert-danger'>Sorry! No stock transfer found.</div>
        </div>";
  exit();
}

$stockTransferItems = easySelectA(array(
    "table"   => "product_stock",
    "fields"  => "round(stock_item_price, 2) as stock_item_price, round(stock_item_qty, 2) as stock_item_qty, 
                    round(stock_item_discount, 2) as stock_item_discount, round(stock_item_subtotal, 2) as stock_item_subtotal, product_name, if(batch_number is null, '', concat('(', batch_number, ')') ) as batch_number",
    "join"    => array(
      "left join {$table_prefix}products on stock_product_id = product_id",
      "left join {$table_prefix}product_batches as product_batches on stock_product_id = product_batches.product_id and stock_batch_id = batch_id"
    ),
    "where" => array(
      "is_bundle_item = 0 and stock_type = 'transfer-out' and stock_transfer_id"  => $_GET["id"],
    )
));


?>

<div class="shopLogo text-center">
<h3 style="font-weight: bold; font-size: 30px;"><?php echo get_options("companyName"); ?></h3>
<p class="text-center">32, Bangla Bazar, Dhaka</p>
<p></p>
</div>

<table> 
<tbody>
  <tr>
    <td style="padding: 0" class="col-md-3">Reference No: Stock_Transfer/<?php echo $selectStockTransfer["data"][0]["stock_transfer_reference"]; ?> </td>
    <td style="padding: 0" class="col-md-3 text-right">Date: <?php echo date("d/m/Y", strtotime($selectStockTransfer["data"][0]["stock_transfer_date"])) ?></td>
  </tr>
  <tr>
    <td colspan="2"><strong>From Warehouse: </strong> <?php echo $selectStockTransfer["data"][0]["warehouseFromName"]; ?> <strong>To Warehouse: </strong> <?php echo $selectStockTransfer["data"][0]["warehouseToName"]; ?>  </td>
  </tr>
</tbody>
</table>

<br/>

<table class="table table-striped table-condensed">
<tbody>
  <tr>
    <td style="width: 50px;">সংখ্যা</td>
    <td>বিবরণ</td>
    <td>মূল্য</td>
    <td>মোট টাকা</td>
  </tr>
  <?php 

    foreach($stockTransferItems["data"] as $key => $sTItems) {

      echo "<tr>";
      echo " <td style='vertical-align: middle;'>". $sTItems['stock_item_qty'] ."</td>";
      echo " <td style='vertical-align: middle;'>{$sTItems['product_name']}</td>";
      echo " <td style='vertical-align: middle;'>{$sTItems['stock_item_price']}</td>";
      echo " <td style='vertical-align: middle;'>{$sTItems['stock_item_subtotal']}</td>";
      
      echo "</tr>";

    }

  ?>      

</tbody>

</table>

<div style="padding: 9px; border: 1px solid #ddd; border-radius: 3px; background-color: #ddd;">

</div>

<div class="no-print">
<hr/>
<div class="form-group">
  <a class="btn btn-primary" href="<?php echo full_website_address(); ?>/stock-management/stock-transfer-list/">Back</a>
  <button type="button" onclick="print();" class="btn btn-primary">Print</button>
</div>
</div>

