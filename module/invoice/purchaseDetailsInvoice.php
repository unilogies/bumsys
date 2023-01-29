<?php

// Select sales
$selectPurchase = easySelect(
    "purchases",
    "*",
    array(
        "left join {$table_prefix}companies on company_id = purchase_company_id",
    ),
    array(
        "purchase_id"  => $_GET["id"]
    )
);


// Print error msg if there has no sales
if ($selectPurchase === false) {
    echo "<div class='no-print'>
          <div class='alert alert-danger'>Sorry there is now purchase found! Please check the sales id.</div>
        </div>";
    exit();
}

// Select Sales item
$selectPurchaseItems = easySelectA(array(
    "table"   => "product_stock as item",
    "fields"  => "stock_item_price, stock_item_qty, stock_item_description, stock_item_discount, stock_item_subtotal, product_name, 
                if(batch_number is null, '', concat('(', batch_number, ')') ) as batch_number",
    "join"    => array(
        "left join {$table_prefix}products on stock_product_id = product_id",
        "left join {$table_prefix}product_batches as product_batches on stock_product_id = product_batches.product_id and stock_batch_id = batch_id"
    ),
    "where" => array(
        "item.is_trash = 0 and item.is_bundle_item = 0 and item.stock_purchase_id"  => $_GET["id"],
    )
));

if (!$selectPurchaseItems) {
    echo "<div class='no-print'>
          <div class='alert alert-danger'>Sorry there is now purchase found! Please check the sales id.</div>
        </div>";
    exit();
}

$purchase = $selectPurchase["data"][0];

?>

<div class="wrapper">
    <!-- Main content -->
    <section class="invoice">

        <!-- title row -->
        <div class="row">
            <div class="col-xs-12">

                <h2 class="page-header">
                    <?php echo get_options("companyName"); ?>
                    
                </h2>
            </div>
            <!-- /.col -->
        </div>

        <!-- info row -->
        <div class="row invoice-info">

            <div class="col-md-12">
                <b>Reference: <?php echo $purchase["purchase_status"] === "Ordered" ? "ORDER/{$purchase["purchase_shop_id"]}/{$purchase["purchase_id"]}" : $purchase["purchase_reference"]; ?></b>
                <small class="pull-right">Date: <?php echo date("d/m/Y", strtotime($purchase["purchase_date"])) ?></small>
            </div>
            <br/><br/>

        </div>
        <!-- /.row -->

        <!-- Table row -->
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Qty</th>
                            <th>Product</th>
                            <th class='text-center'>Price</th>
                            <th class='text-right'>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
     

                        <?php

                        foreach ($selectPurchaseItems["data"] as $key => $items) {

                            $salePrice = "";
                            if (empty($items["stock_item_discount"]) or $items["stock_item_discount"] == 0) {

                                $salePrice = to_money($items['stock_item_price'], 2);

                            } else if( get_options("invoiceShowProductDiscount") ) {

                                $salePrice = "<span>" . to_money($items['stock_item_price'] - $items['stock_item_discount'], 2) . "</span><br/><span><del><small>" . to_money($items['stock_item_price'], 2) . "</small></del></span>";

                            } else {

                                $salePrice = to_money($items['stock_item_price'] - $items['stock_item_discount'], 2);

                            }

                            echo "<tr>";
                            echo " <td style='vertical-align: middle;'>" . number_format($items['stock_item_qty'], 0) . "</td>";
                            echo " <td style='vertical-align: middle;'>{$items['product_name']}</td>";
                            echo " <td class='text-right' style='vertical-align: middle; line-height: 1;'>{$salePrice} </td>";
                            echo " <td style='vertical-align: middle;' class='text-right'>" . to_money($items['stock_item_subtotal'], 2) . "</td>";
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

            </div>
            <!-- /.col -->
            <div class="col-xs-6">

                <div class="table-responsive">
                    <table class="table">
                        <tr>
                            <th style="width:50%">Subtotal:</th>
                            <td class="text-right"><?php echo to_money(($purchase["purchase_total_amount"] - $purchase["purchase_product_discount"]), 2) ?></td>
                        </tr>
                        <tr>
                            <th>Discount </th>
                            <td class="text-right"> (-) <?php echo to_money($purchase["purchase_discount"], 2) ?></td>
                        </tr>
                        <tr>
                            <th>Tariff & Charges:</th>
                            <td class="text-right">(+) <?php echo to_money($purchase["purchase_tariff_charges"], 2) ?></td>
                        </tr>
                        <tr>
                            <th>Shipping:</th>
                            <td class="text-right"><?php echo to_money($purchase["purchase_shipping"], 2) ?></td>
                        </tr>
                        <tr>
                            <th>Grand Total:</th>
                            <td class="text-right"><?php echo to_money($purchase["purchase_grand_total"], 2) ?></td>
                        </tr>
                        <tr>
                            <th>Paid Amount:</th>
                            <td class="text-right"><?php echo to_money($purchase["purchase_paid_amount"], 2) ?></td>
                        </tr>
                        <tr>
                            <th>Due Amount:</th>
                            <td class="text-right"><?php echo to_money($purchase["purchase_due"], 2) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->  

    </section>
    <!-- /.content -->

    <div class="text-center">
        <button type="button" onclick="print();" class="btn btn-primary no-print">Print</button>
    </div>

</div>
<!-- ./wrapper -->