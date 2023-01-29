<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo __("Product Sales Return"); ?>
        </h1>
    </section>

    <style>
        .btn-product {
            border: 1px solid #eee;
            cursor: pointer;
            height: 115px;
            width: 11.8%;
            margin: 0 0 3px 2px;
            padding: 2px;
            min-width: 98px;
            overflow: hidden;
            display: inline-block;
            font-size: 13px;
            background-color: white;
        }

        .btn-product span {
            display: table-cell;
            height: 45px;
            line-height: 15px;
            vertical-align: middle;
            text-transform: uppercase;
            width: 11.5%;
            min-width: 94px;
            overflow: hidden;
        }

        #productListContainer {
            max-height: 680px;
            overflow: auto;
        }

        .tableBodyScroll tbody td {
            padding: 6px 4px !important;
            vertical-align: middle !important;
        }

        .tableBodyScroll tbody {
            display: block;
            overflow: auto;
            height: 33.7vh;
        }

        .tableBodyScroll thead,
        .tableBodyScroll tbody tr {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .tableBodyScroll thead {
            width: calc(100% - 3px);
        }

        .tableBodyScroll tbody::-webkit-scrollbar {
            width: 4px;
        }

        .tableBodyScroll tbody::-webkit-scrollbar-track {
            -webkit-box-shadow: inset 0 0 6px #337ab7;
            border-radius: 10px;
        }

        .tableBodyScroll tbody::-webkit-scrollbar-thumb {
            border-radius: 10px;
            -webkit-box-shadow: inset 0 0 6px #337ab7;
        }

        .image_preview:hover {
            position: absolute;
            z-index: 1;
            height: 500px !important;
            background: transparent;
        }

    </style>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="box box-default">

            <div class="box-header with-border">
                <h3 class="box-title"><?php echo __("Add Sale Return"); ?></h3>
            </div> <!-- box box-default -->

            <div class="box-body">

                <!-- Form start -->
                <form method="post" id="inlineForm" role="form" action="<?php echo full_website_address(); ?>/xhr/?module=stock-management&page=newReturn" enctype="multipart/form-data">

                    <div class="row">

                        <?php

                            $customer_name = "Walk-in-Customer";
                            $customer_id = "1";
                            $total_amount = 0.00;
                            $discount = 0.00;
                            $shipping = 0.00;
                            $grand_total = 0.00;


                            /** If sale_id is set then retrive the product from sales for returning */
                            if( isset($_GET["sale_id"]) and !empty($_GET["sale_id"]) ) {

                                $selectSales = easySelectA(array(
                                    "table"     => "sales",
                                    "fields"    => "sales_customer_id, customer_name, sales_quantity, round(sales_total_amount, 2) as sales_total_amount, sales_product_discount, 
                                                    round(sales_discount, 2) as sales_discount, sales_tariff_charges, sales_tariff_charges_details, round(sales_shipping, 2) as sales_shipping, 
                                                    round(sales_grand_total, 2) as sales_grand_total, round(sales_paid_amount, 2) as sales_paid_amount, sales_change, sales_due",
                                    "join"      => array(
                                        "left join {$table_prefeix}customers on customer_id = sales_customer_id"
                                    ),
                                    "where"     => array(
                                        "sales_id"  => $_GET["sale_id"]
                                    )
                                ))["data"][0];

                                $customer_name = $selectSales["customer_name"];
                                $customer_id = $selectSales["sales_customer_id"];

                                $total_amount = $selectSales["sales_total_amount"];
                                $discount = $selectSales["sales_discount"];
                                $shipping = $selectSales["sales_shipping"];
                                $grand_total = $selectSales["sales_grand_total"];
                                
                            }

                        ?>

                        <div class="form-group col-sm-3 required">
                            <label for="salesReturnDate"><?php echo __("Return Date:"); ?></label>
                            <div class="input-group data">
                                <div class="input-group-addon">
                                    <li class="fa fa-calendar"></li>
                                </div>
                                <input type="text" name="salesReturnDate" id="salesReturnDate" value="<?php echo date("Y-m-d"); ?>" class="form-control pull-right datePicker" required>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 required">
                            <label for="returnCustomer" class="required"><?php echo __("Customer:"); ?></label>
                            <i data-toggle="tooltip" data-placement="right" title="From where the product is returning from. Eg. Customers" class="fa fa-question-circle"></i>
                            <select name="returnCustomer" id="returnCustomer" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=customerList" style="width: 100%;" required>
                                <option value=""><?php echo __("Select Customer"); ?>....</option>
                                <option selected value="<?php echo $customer_id; ?>"><?php echo $customer_name ?></option>
                            </select>
                        </div>
                        <div class="form-group col-sm-3">

                            <label for="returnWarehouse"><?php echo __("Warehouse:"); ?></label>
                            <select name="returnWarehouse" id="returnWarehouse" class="form-control" required>
                                <?php
                                    $selectWarehouse = easySelectA(array(
                                        "table"     =>"warehouses",
                                        "fields"    => "warehouse_id, warehouse_name",
                                        "where"     => array(
                                            "is_trash=0"
                                        )
                                    ));

                                    $warehouses = $selectWarehouse["data"];

                                    foreach($warehouses as $warehouse) {
                                        $selected = $_SESSION["wid"] == $warehouse['warehouse_id'] ? "selected" : "";
                                        echo "<option {$selected} value='{$warehouse['warehouse_id']}'>{$warehouse['warehouse_name']}</option>";
                                    }

                                ?>
                            </select>
                        </div>
                        <div class="imageContainer">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for=""><?php echo __("Bill/ Receipt Attachment:"); ?></label>
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-default btn-file">
                                                <?php echo __("Select photo"); ?> <input type="file" name="purchaseBillAttachment" class="imageToUpload">
                                            </span>
                                        </span>
                                        <input type="text" class="form-control imageNameShow" readonly>
                                    </div>
                            
                                </div>
                            </div>
                        </div>

                        <!-- Full Column -->
                        <div class="col-sm-12">
                            <label for=""><?php echo __("Product Details"); ?></label>
                        </div>
                        <div class="col-sm-12">
                            <table id="productTable" class="tableBodyScroll table table-bordered table-striped table-hover">
                                <thead>
                                    <tr class="bg-primary">
                                        <th class="col-md-4 text-center"><?= __("Product Name and Details"); ?></th>
                                        <th class="col-md-2 text-center"><?php echo __("Batch No"); ?></th>
                                        <th class="col-md-1 text-center"><?php echo __("Quantity"); ?></th>
                                        <th class="col-md-1 text-center"><?php echo __("Unit"); ?></th>
                                        <th class="col-md-1 text-center"><?php echo __("Price"); ?></th>
                                        <th class="col-md-1 text-center"><?php echo __("Discount"); ?></th>
                                        <th class="text-center"><?php echo __("Subtotal"); ?></th>
                                        <th style="width: 30px !important;">
                                            <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php

                                        /** If sale_id is set then retrive the product from sales for returning */
                                        if( isset($_GET["sale_id"]) and !empty($_GET["sale_id"]) ) {
                                            
                                            $soldProduct = easySelectA(array(
                                                "table"     => "product_stock",
                                                "fields"    => "stock_product_id, product_name, product_unit, batch_number, stock_batch_id, round(stock_item_price, 2) as stock_item_price, 
                                                                round(stock_item_qty, 2) as stock_item_qty, round(stock_item_discount, 2) as stock_item_discount, 
                                                                round(stock_item_subtotal, 2) as stock_item_subtotal",  
                                                "join"      => array(
                                                    "left join {$table_prefeix}products on product_id = stock_product_id",
                                                    "left join {$table_prefeix}product_batches on batch_id = stock_batch_id"
                                                ),
                                                "where"     => array(
                                                    "stock_sales_id"    => $_GET["sale_id"]
                                                )
                                            ));

                                            foreach($soldProduct["data"] as $pKey => $product) {

                                                $productBatchHtml = "<input type='hidden' name='productBatch[]' value=''>";

                                                if( !empty($product["stock_batch_id"]) ) {
                                                    $productBatchHtml = '<select name="productBatch[]" id="productBatch" class="form-control select2Ajax" select2-ajax-url="'. full_website_address() .'/info/?module=select2&page=batchList&pid='. $product["stock_product_id"] .'" required>
                                                                            <option value=""><?= __("Select Batch"); ?>....</option>
                                                                            <option selected value="'. $product["stock_batch_id"] .'">'. $product["batch_number"] .'</option>
                                                                        </select>';
                                                }

                                                echo '<tr>
                                                    <input type="hidden" name="productID[]" class="productID" value="'. $product["stock_product_id"] .'">
                                                    <td class="col-md-4">'. $product["product_name"] .'</td>
                                                    <td class="col-md-2">'. $productBatchHtml .'</td>
                                                    <td class="col-md-1"><input onclick = "this.select()" type="text" name="productQnt[]" value="'. $product["stock_item_qty"] .'" class="productQnt form-control text-center"></td>
                                                    <td class="col-md-1">'. $product["product_unit"] .'</td>
                                                    <td class="text-right col-md-1"><input onclick = "this.select()" type="text" name="productReturnPrice[]" value="'. $product["stock_item_price"] .'" class="productReturnPrice form-control text-center" step="any"></td>
                                                    <input type="hidden" name="productReturnMainPrice[]" value="'. $product["stock_item_price"] .'" step="any">
                                                    <td class="text-right col-md-1"><input onclick = "this.select()" type="text" name="productReturnDiscount[]" value="'. $product["stock_item_discount"] .'" placeholder="10% or 10" class="productReturnDiscount form-control text-center"></td>
                                                    <td class="text-right subTotal">'. $product["stock_item_subtotal"] .'</td>
                                                    <td style="width: 30px; !important">
                                                        <i style="cursor: pointer;" class="fa fa-trash-o removeThisProduct"></i>
                                                    </td>
                                                </tr>';

                                            }

                                        }


                                    ?>

                                </tbody>

                                <tfoot>
                                </tfoot>
                            </table>

                            <div class="row">

                                <div class="col-md-6">

                                    <div class="form-group">
                                        <select name="selectProduct" id="selectProduct" class="form-control pull-left select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=productList" style="width: 100%;">
                                            <option value=""><?php echo __("Select Product"); ?>....</option>
                                        </select>
                                    </div>
                                    <p class="text-center">-- <?php echo __("OR"); ?> --</p>
                                    <div class="text-center">
                                        <button data-toggle="modal" data-target="#browseProduct" type="button" class="btn btn-info"><i class="fa fa-folder-open"></i> <?php echo __("browse Product"); ?></button>
                                    </div>
                                    <br/>

                                    <!-- Browse Product Modal -->
                                    <div class="modal fade" id="browseProduct">
                                        <div class="modal-dialog modal-mdm">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title"><?php echo __("Browse Product"); ?> <small><?php echo __("Click on the product to add"); ?></small> </h4>
                                                </div>
                                                <div class="modal-body">

                                                    <!-- Product Filter -->
                                                    <div class="row">

                                                        <?php load_product_filters(); ?>

                                                    </div>
                                                    <!-- /Product Filter -->

                                                    <div class="row box-body" id="productListContainer">
                                                        <!-- Here the products will be shown -->
                                                    </div>

                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __("Close"); ?></button>
                                                </div>
                                            </div>
                                            <!-- /. Browse Product modal-content -->
                                        </div>
                                        <!-- /. Browse Product modal-dialog -->
                                    </div>
                                    <!-- /.Browse Product modal -->

                                </div>

                                <div class="col-md-6">

                                    <table style="margin-bottom: 0px;" class="table">
                                        <tr class="bg-info">
                                            <td><?= __("Items"); ?></td>
                                            <td id="totalItems" class="text-right">0(0)</td>
                                            <td></td>
                                            <td><?= __("Total"); ?></td>
                                            <td class="totalReturnPrice text-right"><?php echo $total_amount; ?></td>
                                            <td></td>
                                        </tr>
                                        <tr class="bg-info">
                                            <td><?= __("Tariff & Charges"); ?> <a data-toggle="modal" data-target="#returnTariffCharges" href="#"> <i class="fa fa-edit"></i> </a> </td>
                                            <td class="totalTariffCharges text-right">(+) 0.00</td>
                                            <td></td>
                                            <td><?= __("Discount"); ?> <a data-toggle="modal" data-target="#returnDiscount" href="#"> <i class="fa fa-edit"></i> </a> </td>
                                            <td class="totalReturnDiscount text-right">(-) <?php echo $discount; ?></td>
                                            <td></td>
                                        </tr>
                                        <tr style="font-weight: bold; background: #333; color: #fff;">
                                            <td colspan="3"><?= __("Net Total"); ?></td>
                                            <td colspan="2" class="netTotal text-right"><?php echo number_format($total_amount - $discount, 2); ?></td>
                                            <td></td>
                                        </tr>
                                    </table>
                                </div>

                            </div>

                        </div>
                        <!-- Full Column -->

                        <div class="modal fade" id="returnDiscount">
                            <div class="modal-dialog ">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?php echo __("Purchase Discount"); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="returnDiscountValue"><?php echo __("Purchase Discount"); ?></label>
                                            <input type="text" name="returnDiscountValue" id="returnDiscountValue" value="<?php echo $discount; ?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo __("Close"); ?></button>
                                        <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo __("Update"); ?></button>
                                    </div>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                        <!-- /.modal -->

                        <div class="modal fade" id="returnTariffCharges">
                            <div class="modal-dialog ">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?php echo __("Tariff & Charges"); ?></h4>
                                    </div>
                                    <div class="modal-body">

                                        <div id="tariffCharges">

                                            <div class="row">
                                                <div class="col-md-7">
                                                    <select name="tariffChargesName[]" class="form-control select2Ajax tariffChargesName" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=tariffCharges">
                                                        <option value=""><?php echo __("Select Tariff/Charges"); ?></option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="number" name="tariffChargesAmount[]" class="form-control tariffChargesAmount" step="any">
                                                </div>
                                            </div>

                                        </div>

                                        <br/>
                                        <div class="text-center">
                                            <span style="cursor: pointer;" class="btn btn-primary" id="addTariffChargesRow">
                                                <i style="padding: 5px;" class="fa fa-plus-circle"></i>
                                            </span>
                                        </div>

                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo __("Close"); ?></button>
                                        <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo __("Update"); ?></button>
                                    </div>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                        <!-- /.modal -->

                        <!-- Modal -->
                        <div class="modal fade" id="finalizeReturn">
                            <div class="modal-dialog ">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?php echo __("Finalize Return"); ?></h4>
                                    </div>
                                    <div class="modal-body">

                                        <div class="form-group row">
                                            <label class="col-md-4" for="returnNetTotal"><?= __("Net Total"); ?></label>
                                            <div class="col-md-8">
                                                <input disabled type="number" name="returnNetTotal" id="returnNetTotal" value="<?php echo $total_amount - $discount; ?>" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4" for="returnShipping"><?php echo __("Shipping"); ?></label>
                                            <div class="col-md-8">
                                                <input type="number" onclick="this.select();" name="returnShipping" id="returnShipping" class="form-control" value="<?php echo $shipping; ?>" step="any">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4" for="returnSurcharge"><?php echo __("Surcharge"); ?></label>
                                            <div class="col-md-8">
                                                <input type="number" onclick="this.select();" name="returnSurcharge" id="returnSurcharge" class="form-control" value="0" step="any">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4" for="returnGrandTotal"><?= __("Grand Total"); ?></label>
                                            <div class="col-md-8">
                                                <input disabled type="number" name="returnGrandTotal" id="returnGrandTotal" value="<?php echo $grand_total; ?>" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4" for="returnPaidAmount"><?= __("Paid Amount"); ?></label>
                                            <div class="col-md-8">
                                                <input type="number" onclick="this.select();" name="returnPaidAmount" id="returnPaidAmount" class="form-control" step="any">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4" for="returnPaymentMethod"><?= __("Payment Method"); ?></label>
                                            <div class="col-md-8">
                                                <select name="returnPaymentMethod" id="returnPaymentMethod" class="form-control select2" style="width: 100%">
                                                    <?php
                                                        $paymentMethod = array("Cash", "Bank Transfer", "Cheque", "Others");
                                                        
                                                        foreach($paymentMethod as $method) {
                                                            echo "<option value='{$method}'>{$method}</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div id="hiddenItem" style="display: none;">
                                            <div class="form-group row">
                                                <label class="col-md-4" for="returnPaymentAttachment"><?= __("Attachment"); ?></label>
                                                <div class="col-md-8">
                                                    <input type="file" name="returnPaymentAttachment" id="returnPaymentAttachment" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-4" for="returnPaymentChequeNo"><?= __("Cheque No"); ?></label>
                                                <div class="col-md-8">
                                                    <input type="text" name="returnPaymentChequeNo" id="returnPaymentChequeNo" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-4" for="returnPaymentChequeDate"><?= __("Cheque Date:"); ?></label>
                                                <div class="col-md-8">
                                                    <input type="text" name="returnPaymentChequeDate" id="returnPaymentChequeDate" value="" class="form-control datePicker">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-4" for="returnChangeAmount">Change</label>
                                            <div class="col-md-8">
                                                <input disabled type="number" name="returnChangeAmount" id="returnChangeAmount" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4" for="returnDueAmount">Due</label>
                                            <div class="col-md-8">
                                                <input disabled type="number" name="returnDueAmount" id="returnDueAmount" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4" for="returnDescription">Return Note</label>
                                            <div class="col-md-8">
                                                <textarea name="returnDescription" id="returnDescription" cols="30" rows="3" class="form-control"></textarea>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo __("Close"); ?></button>
                                        <button id="returnSubmit" type="submit" class="btn btn-primary"><?php echo __("Submit"); ?></button>
                                    </div>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                        <!-- /.modal -->

                    </div><!-- row-->

                    <div class="box-footer">
                        <button data-toggle="modal" data-target="#finalizeReturn" type="button" class="btn btn-primary"><i class="fa fa-shopping-cart"></i> Return</button>
                    </div>

                </form> <!-- Form End -->

            </div> <!-- box-body -->

        </div> <!-- content container-fluid -->

    </section> <!-- Main content End tag -->
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>

   var productReturnPageUrl = window.location.href;

   /* Browse Product while the modal open */
   $("#browseProduct").on("show.bs.modal", function(e) {

        BMS.PRODUCT.showProduct({
            category: $("#productCategoryFilter").val(), 
            brand: $("#productBrandFilter").val(), 
            edition: $("#productEditionFilter").val(),
            generic: $("#productGenericFilter").val(),
            author: $("#productAuthorFilter").val(),
        });

    });

    $(document).on("change", "#returnPaymentMethod", function() {
        if(this.value == "Cheque") {
            
            $("#hiddenItem").css("display", "block");

        } else {

            $("#hiddenItem").css("display", "none");

        }
    });

</script>