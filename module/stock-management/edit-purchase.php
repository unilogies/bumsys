<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("Edit Product Purchases"); ?>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Purchase</a></li>
            <li class="active">Purchase List</li>
        </ol>
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
                <h3 class="box-title"><?= __("Edit Purchase"); ?></h3>
            </div> <!-- box box-default -->

            <div class="box-body">

                <!-- Form start -->
                <form method="post" id="jqFormUpdate" role="form" action="<?php echo full_website_address(); ?>/xhr/?module=stock-management&page=updatePurchase" enctype="multipart/form-data">

                    <?php 

                        $selectPurchase = easySelectA(array(
                            "table"     => "purchases",
                            "fields"    => "purchase_date, purchase_reference, purchase_status, purchase_company_id, company_name, purchase_warehouse_id, warehouse_name, round(purchase_total_amount, 2) as purchase_total_amount, 
                                purchase_product_discount, round(purchase_discount, 2) as purchase_discount, round(purchase_tariff_charges, 2) as purchase_tariff_charges, purchase_tariff_charges_details, round(purchase_shipping, 2) as purchase_shipping, 
                                round(purchase_grand_total, 2) as purchase_grand_total, round(purchase_paid_amount, 2) as purchase_paid_amount, purchase_payment_method, round(purchase_change, 2) as purchase_change, round(purchase_due, 2) as purchase_due, 
                                purchase_total_item, round(purchase_quantity, 2) as purchase_quantity, purchase_note",
                            "join"      => array(
                                "left join {$table_prefeix}companies on company_id = purchase_company_id",
                                "left join {$table_prefeix}warehouses on warehouse_id = purchase_warehouse_id"
                            ),
                            "where"     => array(
                                "purchase_id"   => $_GET["id"]
                            )
                        ));

                        if( $selectPurchase == false ) {
                            echo _e("Sorry! No purchase found.");
                        } else {

                            $purchase = $selectPurchase["data"][0];
                        
                    ?>

                    <div class="row">

                        <input type="hidden" name="purchase_id" value="<?php echo $_GET["id"] ?>">
                        <div class="form-group col-sm-3 required">
                            <label for="purchaseDate"><?= __("Purchase Date:"); ?></label>
                            <div class="input-group data">
                                <div class="input-group-addon">
                                    <li class="fa fa-calendar"></li>
                                </div>
                                <input type="text" name="purchaseDate" id="purchaseDate" value="<?php echo $purchase["purchase_date"]; ?>" class="form-control pull-right datePicker" required>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 required">
                            <label for="purchaseCompany" class="required"><?= __("Company:"); ?></label>
                            <i data-toggle="tooltip" data-placement="right" title="<?= __("From where the product is coming from. Eg. Supplier or Binders"); ?>" class="fa fa-question-circle"></i>
                            <select name="purchaseCompany" id="purchaseCompany" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=supplierBinderList" style="width: 100%;" required>
                                <option value=""><?= __("Select Company"); ?>....</option>
                                <option selected value="<?php echo $purchase["purchase_company_id"]; ?>"><?php echo $purchase["company_name"]; ?></option>
                            </select>
                        </div>
                        <div class="form-group col-sm-3">
                            <label for="purchaseStatus"><?= __("Status:"); ?></label>
                            <select name="purchaseStatus" id="purchaseStatus" class="form-control">
                                <option <?php echo $purchase["purchase_status"] === "Received" ? "selected" : ""; ?> value="Received">Received</option>
                                <option <?php echo $purchase["purchase_status"] === "Ordered" ? "selected" : ""; ?> value="Ordered">Ordered</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-3">

                            <label for="purchaseWarehouse"><?= __("Warehouse:"); ?></label>
                            <select name="purchaseWarehouse" id="purchaseWarehouse" class="form-control" required>
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
                                        $selected = $purchase["purchase_warehouse_id"] == $warehouse['warehouse_id'] ? "selected" : "";
                                        echo "<option {$selected} value='{$warehouse['warehouse_id']}'>{$warehouse['warehouse_name']}</option>";
                                    }

                                ?>
                            </select>
                        </div>
                        <div class="form-group col-sm-3">
                            <label for="purchaseReference"><?= __("Reference:"); ?></label>
                            <input type="text" name="purchaseReference" id="purchaseReference" value="<?php echo $purchase["purchase_reference"]; ?>" class="form-control">
                        </div>
                

                        <div class="imageContainer">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for=""><?= __("Bill/ Receipt Attachment:"); ?></label>
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-default btn-file">
                                                <?= __("Select photo"); ?> <input type="file" name="purchaseBillAttachment" class="imageToUpload">
                                            </span>
                                        </span>
                                        <input type="text" class="form-control imageNameShow" readonly>
                                    </div>
                            
                                </div>
                            </div>
                            
                            <div style="margin-bottom: 10px;" class="form-group col-md-6">
                                <div style="height: 64px; text-align: center; overflow: hidden;" class="image_preview">
                                    <div class="photoErrorMessage"></div>
                                    <img style="margin: auto;" class="previewing" width="100%" height="auto" src="" />
                                </div>
                            </div>

                        </div>

                        <!-- Full Column -->
                        <div class="col-sm-12">
                            <label for=""><?= __("Product Details"); ?></label>
                        </div>
                        <div class="col-sm-12">
                            <table id="productTable" class="tableBodyScroll table table-bordered table-striped table-hover">
                                <thead>
                                    <tr class="bg-primary">
                                        <th class="col-md-4 text-center"><?= __("Product Name"); ?></th>
                                        <th class="col-md-2 text-center"><?= __("Batch No"); ?></th>
                                        <th class="col-md-1 text-center"><?= __("Quantity"); ?></th>
                                        <th class="col-md-1 text-center"><?= __("Unit"); ?></th>
                                        <th class="col-md-1 text-center"><?= __("Price"); ?></th>
                                        <th class="col-md-1 text-center"><?= __("Discount"); ?></th>
                                        <th class="text-center"><?= __("Subtotal"); ?></th>
                                        <th style="width: 30px !important;">
                                            <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php 
                                        $selectPurchaseProduct = easySelectA(array(
                                            "table"     => "product_stock",
                                            "fields"    => "product_name, product_unit, stock_product_id, stock_batch_id, round(stock_item_qty, 2) as stock_item_qty, round(stock_item_price, 2) as stock_item_price, round(stock_item_discount, 2) as stock_item_discount, round(stock_item_subtotal, 2) as stock_item_subtotal",
                                            "join"      => array(
                                                "left join {$table_prefeix}products on product_id = stock_product_id"
                                            ),
                                            "where"     => array(
                                                "stock_purchase_id" => $_GET["id"]
                                            )
                                        ));

                                        if($selectPurchaseProduct !== false) {

                                            foreach($selectPurchaseProduct["data"] as $key => $stock) {
                                                
                                                echo '<tr>
                                                        <input type="hidden" name="productID[]" class="productID" value="'. $stock["stock_product_id"] .'">
                                                        <input type="hidden" name="productBatch[]" class="productBatch" value="'. $stock["stock_batch_id"] .'">
                                                        <td class="col-md-4">'. $stock["product_name"] .'</td>
                                                        <td class="col-md-2">'. $stock["stock_batch_id"] .'</td>
                                                        <td class="col-md-1"><input onclick = "this.select()" type="text" name="productQnt[]" value="'. $stock["stock_item_qty"] .'" class="productQnt form-control text-center"></td>
                                                        <td class="col-md-1">'. $stock["product_unit"] .'</td>
                                                        <td class="text-right col-md-1"><input onclick = "this.select()" type="text" name="productPurchasePrice[]" value="'. $stock["stock_item_price"] .'" class="productPurchasePrice form-control text-center" step="any"></td>
                                                        <input type="hidden" name="productMainPurchasePrice[]" class="productMainPurchasePrice" value="'. $stock["stock_item_price"] .'" step="any">
                                                        <td class="text-right col-md-1"><input onclick = "this.select()" type="text" name="productPurchaseDiscount[]" value="'. $stock["stock_item_discount"] .'" placeholder="10% or 10" class="productPurchaseDiscount form-control text-center"></td>
                                                        <td class="text-right subTotal">'. $stock["stock_item_subtotal"] .'</td>
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
                                        <div class="input-group">
                                            <select name="selectProduct" id="selectProduct" class="form-control pull-left select2Ajax" 
                                                select2-minimum-input-length="1" 
                                                closeOnSelect = "false"
                                                select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=productList" style="width: 100%;">
                                                <option value=""><?= __("Select Product"); ?>....</option>
                                            </select>

                                            <div style="cursor: pointer;" class="input-group-addon btn-primary btn-hover" id="addProductButton">
                                                <i class="fa fa-plus-circle "></i>
                                            </div>

                                        </div>
                                    </div>
                                    <p class="text-center">-- <?= __("OR"); ?> --</p>
                                    <div class="text-center">
                                        <button data-toggle="modal" data-target="#browseProduct" type="button" class="btn btn-info"><i class="fa fa-folder-open"></i> <?= __("browse Product"); ?></button>
                                    </div>
                                    <br/>

                                    <!-- Browse Product Modal -->
                                    <div class="modal fade" id="browseProduct">
                                        <div class="modal-dialog modal-mdm">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title"><?= __("Browse Product"); ?> <small><?= __("Click on the product to add"); ?></small> </h4>
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
                                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= __("Close"); ?></button>
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
                                            <td id="totalItems" class="text-right"><?php echo $purchase["purchase_total_item"]; ?> (<?php echo $purchase["purchase_quantity"]; ?>)</td>
                                            <td></td>
                                            <td><?= __("Total"); ?></td>
                                            <td class="totalPurchasePrice text-right"><?php echo $purchase["purchase_total_amount"]; ?> </td>
                                            <td></td>
                                        </tr>
                                        <tr class="bg-info">
                                            <td><?= __("Tariff & Charges"); ?> <a data-toggle="modal" data-target="#purchaseTariffCharges" href="#"> <i class="fa fa-edit"></i> </a> </td>
                                            <td class="totalTariffCharges text-right">(+) <?php echo $purchase["purchase_tariff_charges"]; ?> </td>
                                            <td></td>
                                            <td><?= __("Discount"); ?> <a data-toggle="modal" data-target="#purchaseDiscount" href="#"> <i class="fa fa-edit"></i> </a> </td>
                                            <td class="totalPurchaseDiscount text-right">(-) <?php echo $purchase["purchase_discount"]; ?> </td>
                                            <td></td>
                                        </tr>
                                        <tr style="font-weight: bold; background: #333; color: #fff;">
                                            <td colspan="3"><?= __("Net Total"); ?></td>
                                            <td colspan="2" class="netTotal text-right"><?php echo $purchase["purchase_grand_total"] - $purchase["purchase_shipping"]; ?></td>
                                            <td></td>
                                        </tr>
                                    </table>
                                </div>

                            </div>

                        </div>
                        <!-- Full Column -->

                        <div class="modal fade" id="purchaseDiscount">
                            <div class="modal-dialog ">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?= __("Purchase Discount"); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="purchaseDiscountValue"><?= __("Purchase Discount"); ?></label>
                                            <input type="text" name="purchaseDiscountValue" id="purchaseDiscountValue" value="<?php echo $purchase["purchase_discount"]; ?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= __("Close"); ?></button>
                                        <button type="button" class="btn btn-primary" data-dismiss="modal"><?= __("Update"); ?></button>
                                    </div>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                        <!-- /.modal -->

                        <div class="modal fade" id="purchaseTariffCharges">
                            <div class="modal-dialog ">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?= __("Tariff & Charges"); ?></h4>
                                    </div>
                                    <div class="modal-body">

                                        <div id="tariffCharges">

                                            <?php 

                                                $tariff = unserialize(html_entity_decode($purchase["purchase_tariff_charges_details"]));

                                                if(isset( $tariff["tariff"] )) {

                                                    foreach($tariff["tariff"] as $tariffKey => $tariffVal) {
                                                        echo '<div class="row">
                                                            <div class="col-md-7">
                                                                <select name="tariffChargesName[]"
                                                                    class="form-control select2Ajax tariffChargesName"
                                                                    select2-ajax-url="'. full_website_address() .'/info/?module=select2&page=tariffCharges">
                                                                    <option value="'. $tariffVal .'">'. $tariffVal .'</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="number" name="tariffChargesAmount[]"
                                                                    class="form-control tariffChargesAmount" value="'. $tariff["value"][$tariffKey] .'" step="any">
                                                            </div>
                                                            <div class="col-md-1"> 
                                                                <i style="cursor: pointer; padding-top: 9px;" class="fa fa-trash-o removeThisTariffCharges"></i> 
                                                            </div> 
                                                            <br/><br/>
                                                        </div>';
                                                    }

                                                } else {

                                                    echo '<div class="row">
                                                            <div class="col-md-7">
                                                                <select name="tariffChargesName[]"
                                                                    class="form-control select2Ajax tariffChargesName"
                                                                    select2-ajax-url="'. full_website_address() .'/info/?module=select2&page=tariffCharges">
                                                                    <option value=""></option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="number" name="tariffChargesAmount[]"
                                                                    class="form-control tariffChargesAmount" value="" step="any">
                                                            </div>
                                                            <div class="col-md-1"> 
                                                                <i style="cursor: pointer; padding-top: 9px;" class="fa fa-trash-o removeThisTariffCharges"></i> 
                                                            </div> 
                                                            <br/><br/>
                                                        </div>';

                                                }

                                            ?>

                                        </div>

                                        <br/>
                                        <div class="text-center">
                                            <span style="cursor: pointer;" class="btn btn-primary" id="addTariffChargesRow">
                                                <i style="padding: 5px;" class="fa fa-plus-circle"></i>
                                            </span>
                                        </div>

                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= __("Close"); ?></button>
                                        <button type="button" class="btn btn-primary" data-dismiss="modal"><?= __("Update"); ?></button>
                                    </div>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                        <!-- /.modal -->

                        <!-- Modal -->
                        <div class="modal fade" id="finalizePurchase">
                            <div class="modal-dialog ">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?= __("Finalize Purchase"); ?></h4>
                                    </div>
                                    <div class="modal-body">

                                        <div class="form-group row">
                                            <label class="col-md-4" for="purchaseNetTotal"><?= __("Net Total"); ?></label>
                                            <div class="col-md-8">
                                                <input disabled type="number" name="purchaseNetTotal" id="purchaseNetTotal" value="<?php echo $purchase["purchase_grand_total"] - $purchase["purchase_shipping"]; ?>" class="form-control" step="any">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4" for="purchaseShipping"><?= __("Shipping"); ?></label>
                                            <div class="col-md-8">
                                                <input type="number" onclick="this.select();" name="purchaseShipping" id="purchaseShipping" class="form-control" value="<?php echo $purchase["purchase_shipping"]; ?>" step="any">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-4" for="purchaseGrandTotal"><?= __("Grand Total"); ?></label>
                                            <div class="col-md-8">
                                                <input disabled type="number" name="purchaseGrandTotal" id="purchaseGrandTotal" class="form-control" value="<?php echo $purchase["purchase_grand_total"]; ?>" step="any">
                                            </div>
                                        </div>
                                        <div class="form-group required row">
                                            <label class="col-md-4" for="purchasePaidAmount"><?= __("Paid Amount"); ?></label>
                                            <div class="col-md-8">
                                                <input type="number" onclick="this.select();" name="purchasePaidAmount" id="purchasePaidAmount" class="form-control" value="<?php echo $purchase["purchase_paid_amount"]; ?>" step="any" readonly required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4" for="purchasePaymentMethod"><?= __("Payment Method"); ?></label>
                                            <div class="col-md-8">
                                                <select name="purchasePaymentMethod" id="purchasePaymentMethod" class="form-control select2" style="width: 100%">
                                                    <?php
                                                        $paymentMethod = array("Cash", "Bank Transfer", "Cheque", "Others");
                                                        
                                                        foreach($paymentMethod as $method) {

                                                            $selected = $purchase["purchase_payment_method"] === $method ? "selected" : "";
                                                            echo "<option {$selected} value='{$method}'>{$method}</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div id="hiddenItem" style="<?php echo $purchase["purchase_payment_method"] !== "Cheque" ? "display: none;" : ""; ?>">
                                            <div class="form-group row">
                                                <label class="col-md-4" for="purchasePaymentAttachment"><?= __("Attachment"); ?></label>
                                                <div class="col-md-8">
                                                    <input type="file" name="purchasePaymentAttachment" id="purchasePaymentAttachment" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-4" for="purchasePaymentChequeNo"><?= __("Cheque No"); ?></label>
                                                <div class="col-md-8">
                                                    <input type="text" name="purchasePaymentChequeNo" id="purchasePaymentChequeNo" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-4" for="purchasePaymentChequeDate"><?= __("Cheque Date:"); ?></label>
                                                <div class="col-md-8">
                                                    <input type="text" name="purchasePaymentChequeDate" id="purchasePaymentChequeDate" value="" class="form-control datePicker">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-4" for="purchaseChangeAmount">Change</label>
                                            <div class="col-md-8">
                                                <input disabled type="number" name="purchaseChangeAmount" id="purchaseChangeAmount" value="<?php echo $purchase["purchase_change"]; ?>" class="form-control" step="any">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4" for="purchaseDueAmount">Due</label>
                                            <div class="col-md-8">
                                                <input disabled type="number" name="purchaseDueAmount" id="purchaseDueAmount" value="<?php echo $purchase["purchase_due"]; ?>" class="form-control" step="any">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4" for="purchaseDescription">Description</label>
                                            <div class="col-md-8">
                                                <textarea name="purchaseDescription" id="purchaseDescription" cols="30" rows="3" class="form-control"><?php echo $purchase["purchase_note"]; ?></textarea>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                                        <button id="purchaseSubmit" type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                        <!-- /.modal -->

                    </div><!-- row-->

                    <div class="box-footer">
                        <button data-toggle="modal" data-target="#finalizePurchase" type="button" class="btn btn-primary"><i class="fa fa-shopping-cart"></i> Update Purchase</button>
                    </div>

                    <?php } ?>

                </form> <!-- Form End -->

            </div> <!-- box-body -->

        </div> <!-- content container-fluid -->

    </section> <!-- Main content End tag -->
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>

   var purchasePageUrl = window.location.href;

   /* Browse Product while the modal open */
   $("#browseProduct").on("show.bs.modal", function(e) {

        BMS.PRODUCT.showProduct();

    });

    $(document).on("change", "#purchasePaymentMethod", function() {
        if(this.value == "Cheque") {
            
            $("#hiddenItem").css("display", "block");

        } else {

            $("#hiddenItem").css("display", "none");

        }
    });

</script>