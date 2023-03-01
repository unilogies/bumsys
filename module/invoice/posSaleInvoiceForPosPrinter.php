<style>
    .table-striped>tbody>tr>td {
        border-bottom: 1px solid #969595 !important;
    }

    @page {
        margin: 0;
    }
</style>

<?php

// Select sales
$selectSale = easySelect(
    "sales",
    "*",
    array(
        "left join {$table_prefix}customers on sales_customer_id = customer_id"
    ),
    array(
        "sales_id"  => $_GET["id"]
    )
);

//print_r($selectSale);
// Print error msg if there has no sales
if (!$selectSale) {
    echo "<div class='no-print'>
          <div class='alert alert-danger'>Sorry there is now sales found! Please check the sales id.</div>
        </div>";
    exit();
}

$selectSalesItems = easySelectA(array(
    "table"   => "product_stock",
    "fields"  => "stock_item_price, stock_item_qty, stock_item_discount, stock_item_subtotal, product_name, left(product_group, 3) as product_group, if(batch_number is null, '', concat('(', batch_number, ')') ) as batch_number",
    "join"    => array(
        "left join {$table_prefix}products on stock_product_id = product_id",
        "left join {$table_prefix}product_batches as product_batches on stock_product_id = product_batches.product_id and stock_batch_id = batch_id"
    ),
    "where" => array(
        "is_bundle_item = 0 and stock_sales_id"  => $_GET["id"],
    )
));

if (!$selectSalesItems) {
    echo "<div class='no-print'>
          <div class='alert alert-danger'>Sorry there is now sales found! Please check the sales id.</div>
        </div>";
    exit();
}

$sales = $selectSale["data"][0];

$selectShop = easySelect(
    "shops",
    "shop_name, shop_address, shop_phone",
    array(),
    array(
        "shop_id" => $_SESSION["sid"]
    )
)["data"][0];


?>

<div class="shopLogo text-center">
    <img height="60px" src="<?php echo full_website_address() ?>/assets/images/Wilson-Pharma-Logo.png" alt="logo">
    <h3 style="font-weight: bold; font-size: 25px; padding-top: 0px; margin-top: 0px; margin-bottom: 5px;"><?php echo get_options("companyName"); ?></h3>
    <p style="line-height: 1;" class="text-center">An International Standard Medicine Shop.</p>
    <p style="line-height: 1;" class="text-center"><?php echo $selectShop["shop_address"]; ?></p>
    <p style="line-height: 1;">Mobile: <?php echo $selectShop["shop_phone"]; ?></p>
    <!-- <p style="font-size: 20px; line-height: 1;">وَإِذَا مَرِضْتُ فَهُوَ يَشْفِينِ</p> -->
    <p style="font-size: 12px; line-height: 1.25;">আমি যখন অসুস্থ হই, তখন তিনিই (আল্লাহ) <br/> আমাকে সুস্থতা দান করেন। (আল-কুরআন, ২৬:৮০)</p>
    <!-- <p style="font-size: 13px; line-height: 1.25;">It is He (Allah) who gives me cure when <br />I become sick. (Al-Quran, 26:80)</p> -->
    <p></p>
</div>

<table width="100%">
    <tbody>
        <tr>
            <td style="padding: 0" class="col-md-7">Ref: <?php echo $selectSale["data"][0]["sales_reference"]; ?></td>
            <td style="padding: 0" class="col-md-5 text-right">Date: <?php echo date("d/m/Y", strtotime($selectSale["data"][0]["sales_delivery_date"])) ?> <?php echo date(" H:i", strtotime($selectSale["data"][0]["sales_update_on"])) ?></td>
        </tr>
        <tr>
            <td colspan="2"><strong>Customer: <?php echo !empty($selectSale["data"][0]["customer_name_in_local_len"]) ? $selectSale["data"][0]["customer_name_in_local_len"] : $selectSale["data"][0]["customer_name"] ?> (<?php echo $selectSale["data"][0]["customer_phone"] ?>) </strong> </td>
        </tr>
    </tbody>
</table>

<br />

<table class="table table-striped table-condensed">
    <tbody>
        <tr>
            <td class="text-center" style="width: 24px;">Qty</td>
            <td class="text-center">Items</td>
            <td class="text-center" style="width: 55px;">Price</td>
            <td style="width: 65px;" class="text-right">Total</td>
        </tr>
        <?php

        foreach ($selectSalesItems["data"] as $key => $saleItems) {

            $salePrice = "";
            if (empty($saleItems["stock_item_discount"]) or $saleItems["stock_item_discount"] == 0) {
                $salePrice = to_money($saleItems['stock_item_price'], 2);
            } else {
                $salePrice = "<span>" . to_money($saleItems['stock_item_price'] - $saleItems['stock_item_discount'], 2) . "</span><br/><span><del><small>" . to_money($saleItems['stock_item_price'], 2) . "</small></del></span>";
            }

            echo "<tr>";
            echo " <td style='vertical-align: middle;'>" . number_format($saleItems['stock_item_qty'], 0) . "</td>";
            echo " <td style='vertical-align: middle;'>". ( empty($saleItems['product_group']) ? "" : $saleItems['product_group'] . ". " ) ." {$saleItems['product_name']}</td>";
            echo " <td class='text-right' style='vertical-align: middle; line-height: 1;'>{$salePrice} </td>";
            echo " <td style='vertical-align: middle;' class='text-right'>" . to_money($saleItems['stock_item_subtotal'], 2) . "</td>";
            echo "</tr>";
        }

        ?>

    </tbody>

    <tfoot>

        <tr>
            <th colspan="2"> Item Count: <?php echo count($selectSalesItems["data"]); ?> </th>
            <th class="text-right">Total:</th>
            <th class="text-right"><?php echo to_money(($sales["sales_total_amount"] - $sales["sales_product_discount"]), 2) ?></th>
        </tr>

        <!-- If no discount found then hide the discount row -->
        <?php if ($sales["sales_discount"] != 0) { ?>
            <tr>
                <th colspan="3" class="text-right">Discount: (-)</th>
                <th class="text-right"> <?php echo to_money($sales["sales_discount"], 2) ?></th>
            </tr>
        <?php } ?>

        <!-- If no  shipping found then hide the shipping row -->
        <?php if ($sales["sales_shipping"] > 0) { ?>
            <tr>
                <th colspan="3" class="text-right">Transport + Packet : (+)</th>
                <th class="text-right"> <?php echo to_money($sales["sales_shipping"], 2) ?></th>
            </tr>
        <?php } ?>

        <!-- If no sales_tariff_charges found then hide the sales_tariff_charges row -->
        <?php if ($sales["sales_tariff_charges"] > 0) { ?>

            <tr>
                <th colspan="3" class="text-right">Tariff & Charges (<?php echo implode(", ", unserialize(html_entity_decode($sales["sales_tariff_charges_details"]))); ?>) : (+)</th>
                <th class="text-right"> <?php echo to_money($sales["sales_tariff_charges"], 2) ?></th>
            </tr>
        <?php } ?>

        <tr>
            <th colspan="3" class="text-right">Adjust: (±)</th>
            <th class="text-right"><?php echo to_money($sales["sales_adjustment"], 2) ?></th>
        </tr>

        <tr>
            <th colspan="3" class="text-right">Grand Total:</th>
            <th class="text-right"><?php echo to_money($sales["sales_grand_total"], 2) ?></th>
        </tr>
        <tr>
            <th colspan="3" class="text-right">Paid Amount:</th>
            <th class="text-right"><?php echo to_money($sales["sales_paid_amount"], 2) ?></th>
        </tr>
        <tr>
            <th colspan="2" class="text-left">
                Change: <?php echo to_money($sales["sales_change"], 2) ?>
            </th>
            <th class="text-right">Due:</th>
            <th class="text-right"><?php echo to_money($sales["sales_due"], 2) ?></th>
        </tr>

    </tfoot>

</table>

<div>
    <p style="font-size: 11px; margin: 0; line-height: 1.45; font-family: kalpurush;">
        * বিক্রিত পণ্য ২ দিনের মধ্যে ফেরত ও পরিবর্তনযোগ্য। <br />
        * <span style="font-family: Times; font-size: 12.4px;">Sold items can be returned & changed within 2 days. <br /></span>
        * ডায়াবেটিস স্ট্রীপ, ফ্রিজিং আইটেম, ফ্রিজিং ইনজেক্শন ও কাঁটা-ছেড়া ঔষুধ পরিবর্তনযোগ্য /ফেরতযোগ্য নয়। <br />
        * রিসিপ্ট সাথে আনতে হবে/ থাকতে হবে।
        <?php //echo str_replace("\n", "<br/>",$selectShop['data'][0]["shop_invoice_footer"]); 
        ?>
    </p>
</div>
<p class="text-center">
    <img height="40px;" width="160px" src="<?php echo full_website_address(); ?>/barcode/?f=png&s=code-128&d=<?php echo urldecode($selectSale["data"][0]["sales_reference"]); ?>&sf=.9&ms=r&md=0.1" alt="" srcset="">
</p>

<div class="no-print">
    <hr />
    <div class="form-group">
        <a class="btn btn-primary" href="<?php echo full_website_address(); ?>/sales/pos/">Back to POS</a>
        <button type="button" onclick="print();" class="btn btn-primary">Print</button>
    </div>
</div>