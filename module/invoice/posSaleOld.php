<?php 

// Select sales
$selectSale = easySelect(
  "sales",
  "*",
  array (
    "left join {$table_prefix}customers on sales_customer_id = customer_id"
  ),
  array (
    "sales_id"  => $_GET["id"]
  )
);

//print_r($selectSale);
// Print error msg if there has no sales
if(!$selectSale) {
  echo "<div class='no-print'>
          <div class='alert alert-danger'>Sorry there is now sales found! Please check the sales id.</div>
        </div>";
  exit();
}

// Select Sales item
$selectSalesItems = easySelectA(array(
  "table"   => "sale_items",
  "fields"  => "sale_item_price, sale_item_quantity, sale_item_unit, sale_item_discount, sale_item_subtotal, product_name",
  "join"    => array(
    "left join {$table_prefix}products on sale_item_product_id = product_id"
  ),
  "where" => array(
    "is_bundle_item = 0 and sale_item_sales_id"  => $_GET["id"],
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
<?php 
  $selectShop = easySelect(
    "shops", 
    "shop_name, shop_invoice_footer", 
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
    <td style="padding: 0" class="col-md-3">Reference No: <?php echo $selectSale["data"][0]["sales_reference"] ?></td>
    <td style="padding: 0" class="col-md-3 text-right">Date: <?php echo date("d/m/Y", strtotime($selectSale["data"][0]["sales_date"])) ?></td>
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
    <td>সংখ্যা</td>
    <td>বিবরণ</td>
    <td style="width: 70px;">মূল্য</td>
    <td style="width: 100px;" class="text-right">মোট টাকা</td>
  </tr>
  <?php 

    foreach($selectSalesItems["data"] as $key => $saleItems) {
      
      $salePrice = "";
      if(empty($saleItems["sale_item_discount"]) or $saleItems["sale_item_discount"] == 0) {
        $salePrice = to_money($saleItems['sale_item_price'],2);
      } else {
        $salePrice = "<span>". to_money($saleItems['sale_item_price'] - $saleItems['sale_item_discount'],2) ."</span><br/><span><del><small>". to_money($saleItems['sale_item_price'],2) ."</small></del></span>";
      }

      echo "<tr>";
      echo " <td style='vertical-align: middle;'>". number_format($saleItems['sale_item_quantity'],0) ."</td>";
      echo " <td style='vertical-align: middle;'>{$saleItems['product_name']}</td>";
      echo " <td style='vertical-align: middle; line-height: 1;'>{$salePrice} </td>";
      echo " <td style='vertical-align: middle;' class='text-right'>" . to_money($saleItems['sale_item_subtotal'],2) . "</td>";
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
  <?php if($sales["sales_order_discount"] > 0) { ?>
    <tr>
      <th colspan="3" class="text-right">Discount:</th>
      <th class="text-right">(-) <?php echo to_money($sales["sales_order_discount"], 2) ?></th>
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
    <?php echo $selectShop['data'][0]["shop_invoice_footer"]; ?>
</p>
</div>

<div class="no-print">
<hr/>
<div class="form-group">
  <a class="btn btn-primary" href="<?php echo full_website_address(); ?>/sales/pos/">Back to POS</a>
  <button type="button" onclick="print();" class="btn btn-primary">Print</button>
</div>
</div>

