
<?php 

// Select sales
$selectSale = easySelect(
  "sales",
  "*",
  array (
    "left join {$table_prefeix}customers on sales_customer_id = customer_id"
  ),
  array (
    "sales_id"  => $_GET["id"]
  )
);

//print_r($selectSale);
// Print error msg if there has no sales
if($selectSale["count"] !== 1) {
  echo "<div class='no-print'>
          <div class='alert alert-danger'>Sorry there is now sales found! Please check the sales id.</div>
        </div>";
  exit();
}

// Select Sales item
$selectSalesItems = easySelectA(array(
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

if(!$selectSalesItems) {
  echo "<div class='no-print'>
          <div class='alert alert-danger'>Sorry there is now sales found! Please check the sales id.</div>
        </div>";
  exit();
}

$sales = $selectSale["data"][0];

?>

<div class="shopLogo text-center">
    <h3 style="font-weight: bold; font-size: 25px; padding-top: 0px; margin-top: -7px;"><?php echo get_options("companyName"); ?></h3>
    <p class="text-center">32, Bangla Bazar, Dhaka</p>
    <!-- <p style="font-size: 20px;">وَإِذَا مَرِضْتُ فَهُوَ يَشْفِينِ</p> -->
    <p style="font-size: 12px; line-height: 1.25;">আমি যখন অসুস্থ হই, তখন তিনিই (আল্লাহ) <br/> আমাকে সুস্থতা দান করেন। (আল-কুরআন, ২৬:৮০)</p>
    <p></p>
</div>

<table width="100%"> 
<tbody>
  <tr>
    <td style="padding: 0" class="col-md-7">Reference: <?php echo $selectSale["data"][0]["sales_reference"] ?></td>
    <td style="padding: 0" class="col-md-5 text-right">Date: <?php echo date("d/m/Y", strtotime($selectSale["data"][0]["sales_delivery_date"])) ?></td>
  </tr>
  <tr>
    <td colspan="2"><strong>Customer: <?php echo !empty($selectSale["data"][0]["customer_name_in_local_len"]) ? $selectSale["data"][0]["customer_name_in_local_len"] : $selectSale["data"][0]["customer_name"] ?> (<?php echo $selectSale["data"][0]["customer_phone"] ?>) </strong> </td>
  </tr>
</tbody>
</table>

<br/>

<table class="table table-striped table-condensed">
<tbody>
  <tr>
    <td style="width: 25px;">Qty</td>
    <td class="text-center">Item</td>
    <td class="text-center" style="width: 55px;">Price</td>
    <td style="width: 65px;" class="text-right">Total</td>
  </tr>
  <?php 

    foreach($selectSalesItems["data"] as $key => $saleItems) {
      
      $salePrice = "";
      if(empty($saleItems["stock_item_discount"]) or $saleItems["stock_item_discount"] == 0) {
        $salePrice = to_money($saleItems['stock_item_price'],2);
      } else {
        $salePrice = "<span>". to_money($saleItems['stock_item_price'] - $saleItems['stock_item_discount'],2) ."</span><br/><span><del><small>". to_money($saleItems['sale_item_price'],2) ."</small></del></span>";
      }

      echo "<tr>";
      echo " <td style='vertical-align: middle;'>". number_format($saleItems['stock_item_qty'],0) ."</td>";
      echo " <td style='vertical-align: middle;'>{$saleItems['product_name']} {$saleItems['batch_number']}</td>";
      echo " <td class='text-right' style='vertical-align: middle; line-height: 1;'>{$salePrice} </td>";
      echo " <td style='vertical-align: middle;' class='text-right'>" . to_money($saleItems['stock_item_subtotal'],2) . "</td>";
      echo "</tr>";

    }

  ?>      

</tbody>

<tfoot>

  <tr>
    <th> <?php echo number_format($sales["sales_quantity"],0); ?> </th>
    <th> Total Quantity </th>
    <th class="text-right">Total:</th>
    <th class="text-right"><?php echo to_money(($sales["sales_total_amount"] - $sales["sales_product_discount"]), 2) ?></th>
  </tr>
  
  <!-- If no discount found then hide the discount row -->
  <?php if($sales["sales_discount"] > 0) { ?>
    <tr>
      <th colspan="3" class="text-right">Discount:</th>
      <th class="text-right">(-) <?php echo to_money($sales["sales_discount"], 2) ?></th>
    </tr>
  <?php } ?>

  <!-- If no  shipping found then hide the shipping row -->
  <?php if($sales["sales_shipping"] > 0 ) { ?>
    <tr>
      <th colspan="3" class="text-right">Transport + Packet :</th>
      <th class="text-right">(+) <?php echo to_money( $sales["sales_shipping"], 2) ?></th>
    </tr>
  <?php } ?>

  <!-- If no sales_tariff_charges found then hide the sales_tariff_charges row -->
  <?php if($sales["sales_tariff_charges"] > 0 ) { ?>

    <tr>
      <th colspan="3" class="text-right">Tariff & Charges (<?php echo implode(", ", unserialize( html_entity_decode($sales["sales_tariff_charges_details"]) )); ?>):</th>
      <th class="text-right">(+) <?php echo to_money( $sales["sales_tariff_charges"], 2) ?></th>
    </tr>
  <?php } ?>
  
  <tr>
    <th colspan="3" class="text-right">Grand Total:</th>
    <th class="text-right"><?php echo to_money($sales["sales_grand_total"], 2) ?></th>
  </tr>
  <tr>
    <th colspan="3" class="text-right">Paid Amount:</th>
    <th class="text-right"><?php echo to_money($sales["sales_paid_amount"], 2) ?></th>
  </tr>
  <tr>
    <th colspan="3" class="text-right"> 
      Packet(s): <?php echo $sales["sales_total_packets"] ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      Change: <?php echo to_money($sales["sales_change"], 2) ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      Due Amount:
    </th>
    <th class="text-right"><?php echo to_money($sales["sales_due"], 2) ?></th>
  </tr>
 
</tfoot>

</table>

<div style="padding: 9px; border: 1px solid #ddd; border-radius: 3px; background-color: #ddd;">
<p>
    <?php //echo $selectShop['data'][0]["shop_invoice_footer"]; ?>
</p>
</div>

<div class="no-print">
<hr/>
<div class="form-group">
  <a class="btn btn-primary" href="<?php echo full_website_address(); ?>/pos/">Back to POS</a>
  <button type="button" onclick="print();" class="btn btn-primary">Print</button>
</div>
</div>

