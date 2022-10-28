<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("Stock Transfer"); ?>
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
    </style>

    <?php 

      // Check if the biller is set
      if(!isset($_SESSION["aid"])) {
        
        echo _e("You must set you as a biller to make transfer");

      }

    ?>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="box box-default">

            <div class="box-header with-border">
                <h3 class="box-title"><?= __("New Stock Transfer"); ?></h3>
            </div> <!-- box box-default -->

            <div class="box-body">

                <!-- Form start -->
                <form method="post" id="inlineForm" role="form" action="<?php echo full_website_address(); ?>/xhr/?module=stock-management&page=newStockTransfer" enctype="multipart/form-data">

                    <div class="row">

                        <div class="form-group col-sm-3 required">
                            <label for="transferDate"><?= __("Transfer Date:"); ?></label>
                            <div class="input-group data">
                                <div class="input-group-addon">
                                    <li class="fa fa-calendar"></li>
                                </div>
                                <input type="text" name="transferDate" id="transferDate" value="<?php echo date("Y-m-d"); ?>" class="form-control pull-right datePicker" required>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 required">
                            <label for="stockTransferFromWarehouse"><?= __("From Warehouse:"); ?></label>
                            <select name="stockTransferFromWarehouse" id="stockTransferFromWarehouse" class="form-control" required>
                                <option value=""><?= __("Select warehouse"); ?>...</option>
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
                                        echo "<option value='{$warehouse['warehouse_id']}'>{$warehouse['warehouse_name']}</option>";
                                    }

                                ?>
                            </select>
                            <input type="hidden" name="stockTransferFromWarehouseId" id="stockTransferFromWarehouseId" value="">
                        </div>

                        <div class="form-group col-sm-3 required">
                            <label for="stockTransferToWarehouse"><?= __("To Warehouse:"); ?></label>
                            <select name="stockTransferToWarehouse" id="stockTransferToWarehouse" class="form-control" required>
                                <option value=""><?= __("Select warehouse"); ?>...</option>
                                <?php

                                    foreach($warehouses as $warehouse) {
                                        echo "<option value='{$warehouse['warehouse_id']}'>{$warehouse['warehouse_name']}</option>";
                                    }

                                ?>
                            </select>
                            <input type="hidden" name="stockTransferToWarehouseId" id="stockTransferToWarehouseId" value="">
                        </div>
                        
                        <div class="form-group col-sm-3">
                            <label for="stockTransferReference"><?= __("Reference:"); ?></label>
                            <input type="text" name="stockTransferReference" id="stockTransferReference" class="form-control">
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
                                </tbody>

                                <tfoot>
                                </tfoot>
                            </table>


                            <div class="row">

                                <div class="col-md-6">

                                    <div class="form-group">
                                        <select name="selectStockTransferProduct" id="selectStockTransferProduct" class="form-control pull-left select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=productList" style="width: 100%;">
                                            <option value=""><?= __("Select Product"); ?>....</option>
                                        </select>
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
                                        <tr style="font-weight: bold; background: #333; color: #fff;">
                                            <td><?= __("Items"); ?></td>
                                            <td id="totalItems" class="text-right">0(0)</td>
                                            <td></td>
                                            <td><?= __("Total"); ?></td>
                                            <td class="totalPurchasePrice text-right">0.00</td>
                                            <td></td>
                                        </tr>
                                    </table>

                                    <div class="form-group">
                                        <label for="stockTransferDescription"><?= __("Description:"); ?></label>
                                        <textarea name="stockTransferDescription" id="stockTransferDescription" cols="30" rows="3" class="form-control"></textarea>
                                    </div>

                                </div>

                            </div>

                        </div>
                        <!-- Full Column -->

                    </div><!-- row-->

                    <div class="box-footer">
                        <button data-toggle="modal"  type="submit" class="btn btn-primary"><i class="fa fa-shopping-cart"></i> <?= __("Transfer"); ?></button>
                    </div>

                </form> <!-- Form End -->

            </div> <!-- box-body -->

        </div> <!-- content container-fluid -->

    </section> <!-- Main content End tag -->
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
    
    var transferStockPageUrl = window.location.href;
    
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

</script>