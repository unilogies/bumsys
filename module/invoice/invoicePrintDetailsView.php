<?php

/** the $print_type and $selectShop variable is declared on /theme/invoice-print.php file */

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

// Select Sales item
$selectSalesItems = easySelectA(array(
    "table"   => "product_stock",
    "fields"  => "stock_item_price, stock_item_qty, stock_item_description, stock_item_discount, stock_item_subtotal, product_name, if(batch_number is null, '', concat('(', batch_number, ')') ) as batch_number",
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

?>

<div class="wrapper">
    <!-- Main content -->
    <section class="invoice">

        <!-- title row -->
        <div class="row">
            <div class="col-xs-12">

                <?php

                    if (get_options("invoiceShowShopLogo") and !empty( $selectShop["shop_logo"] ) ) {
                        echo '<img style="width: 486px;" src="'. full_website_address() .'/images/?for=shopLogo&id='. $_SESSION["sid"] .'" alt="' . $selectShop['shop_name'] . '">';
                    }

                    ?>

                <h2 class="page-header">
                    <?php echo get_options("companyName"); ?>
                    <small class="pull-right">Date: <?php echo date("d/m/Y", strtotime($selectSale["data"][0]["sales_delivery_date"])) ?></small>
                </h2>
            </div>
            <!-- /.col -->
        </div>

        <!-- info row -->
        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
                From
                <address>
                    <strong><?php echo $selectShop["shop_name"]; ?></strong><br>
                    <?php echo $selectShop["shop_address"]; ?> <br/>
                    Phone: <?php echo $selectShop["shop_phone"]; ?><br>
                    Email: <?php echo $selectShop["shop_email"]; ?>
                </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
                To
                <address>
                    <strong><?php echo $selectSale["data"][0]["customer_name"]; ?></strong><br>
                    <?php echo $selectSale["data"][0]["sales_shipping_address"]; ?><br>
                    Phone: <?php echo $selectSale["data"][0]["customer_phone"]; ?><br>
                    Email: <?php echo $selectSale["data"][0]["customer_email"]; ?>
                </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
                <b>Invoice <?php echo $selectSale["data"][0]["sales_reference"] ?></b><br>
                <br>
                <b>Payment Status:</b> <?php echo ucfirst($selectSale["data"][0]["sales_payment_status"]); ?><br>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- Table row -->
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Qty</th>
                            <th>Product</th>
                            <th>Description</th>
                            <th class='text-center'>Price</th>
                            <th class='text-right'>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
     

                        <?php

                        foreach ($selectSalesItems["data"] as $key => $saleItems) {

                            $salePrice = "";
                            if (empty($saleItems["stock_item_discount"]) or $saleItems["stock_item_discount"] == 0) {

                                $salePrice = to_money($saleItems['stock_item_price'], 2);

                            } else if( get_options("invoiceShowProductDiscount") ) {

                                $salePrice = "<span>" . to_money($saleItems['stock_item_price'] - $saleItems['stock_item_discount'], 2) . "</span><br/><span><del><small>" . to_money($saleItems['stock_item_price'], 2) . "</small></del></span>";

                            } else {

                                $salePrice = to_money($saleItems['stock_item_price'] - $saleItems['stock_item_discount'], 2);

                            }

                            echo "<tr>";
                            echo " <td style='vertical-align: middle;'>" . number_format($saleItems['stock_item_qty'], 0) . "</td>";
                            echo " <td style='vertical-align: middle;'>{$saleItems['product_name']}</td>";
                            echo " <td style='vertical-align: middle;'>{$saleItems['stock_item_description']}</td>";
                            echo " <td class='text-right' style='vertical-align: middle; line-height: 1;'>{$salePrice} </td>";
                            echo " <td style='vertical-align: middle;' class='text-right'>" . to_money($saleItems['stock_item_subtotal'], 2) . "</td>";
                            echo "</tr>";
                        }

                        ?>
                       
                    </tbody>
                </table>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <div class="row">
            <!-- accepted payments column -->
            <div class="col-xs-6">
                <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                    <?php 
                    echo "<b>Note: </b>" . $selectSale["data"][0]["sales_note"] . "<br/>";
                    echo get_options("invoiceFooter"); 
                    ?>
                </p>
            </div>
            <!-- /.col -->
            <div class="col-xs-6">

                <div class="table-responsive">
                    <table class="table">
                        <tr>
                            <th style="width:50%">Subtotal:</th>
                            <td class="text-right"><?php echo to_money(($sales["sales_total_amount"] - $sales["sales_product_discount"]), 2) ?></td>
                        </tr>
                        <tr>
                            <th>Discount </th>
                            <td class="text-right"> (-) <?php echo to_money($sales["sales_discount"], 2) ?></td>
                        </tr>
                        <tr>
                            <th>Tariff & Charges:</th>
                            <td class="text-right">(+) <?php echo to_money($sales["sales_tariff_charges"], 2) ?></td>
                        </tr>
                        <tr>
                            <th>Shipping:</th>
                            <td class="text-right"><?php echo to_money($sales["sales_shipping"], 2) ?></td>
                        </tr>
                        <tr>
                            <th>Grand Total:</th>
                            <td class="text-right"><?php echo to_money($sales["sales_grand_total"], 2) ?></td>
                        </tr>
                        <tr>
                            <th>Paid Amount:</th>
                            <td class="text-right"><?php echo to_money($sales["sales_paid_amount"], 2) ?></td>
                        </tr>
                        <tr>
                            <th>Due Amount:</th>
                            <td class="text-right"><?php echo to_money($sales["sales_due"], 2) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- ./wrapper -->