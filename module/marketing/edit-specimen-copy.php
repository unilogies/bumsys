<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("Edit Specimen Copy"); ?>
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
            max-height: 640px;
            overflow: auto;
        }

        .tableBodyScroll tbody td {
            padding: 6px 4px !important;
            vertical-align: middle !important;
        }

        .tableBodyScroll tbody {
            display: block;
            overflow: auto;
            height: 40vh;
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

    if(isset($_GET["action"]) and $_GET["action"] == "updateSpecimenCopy" and !empty($_POST)) {
          
        // Insert specimen copy
        
        $updateSC = easyUpdate(
            "specimen_copies",
            array(
                "sc_date"           => $_POST["sctransferDate"],
                "sc_type"           => $_POST["scType"],
                "sc_warehouse_id"   => $_POST["scTransferWarehouseId"],
                "sc_employee_id"    => $_POST["scTransferRepresentative"],
                "sc_add_by"         => $_SESSION["uid"]
            ),
            array(
                "sc_id" => $_POST["scId"]
            )
        );

        if( $updateSC === true ) {

            // Delete previous all Specimen Copy Items
            easyPermDelete(
                "product_stock",
                array(
                    "stock_sc_id"   => $_POST["scId"]
                )
            );

            // Insert SC product items
            foreach($_POST["productID"] as $key => $productId) {

                // Insert specimen copy items
                easyInsert(
                    "product_stock",
                    array (
                        "stock_type"            => ( $_POST["scType"] === "Dispatch" ) ? 'specimen-copy' : 'specimen-copy-return',
                        "stock_entry_date"      => $_POST["sctransferDate"],
                        "stock_sc_id"           => $_POST["scId"],
                        "stock_shop_id"         => $_SESSION["sid"],
                        "stock_product_id"      => $productId,
                        "stock_batch_id"        => NULL, // This batch option will integrate later, because in this function for publication there will be no expriy product
                        "stock_employee_id"     => $_POST["scTransferRepresentative"],
                        "stock_warehouse_id"    => $_POST["scTransferWarehouseId"],
                        "stock_item_qty"        => $_POST["productQnt"][$key],
                        "stock_created_by"      => $_SESSION["uid"]
                    )
                );

                // Select products, which have sub products and insert sub/bundle products
                $subProducts = easySelectA(array(
                    "table"     => "products as product",
                    "fields"    => "bg_item_product_id,
                                    bg_product_qnt
                                    ",
                    "join"      => array(
                        "inner join {$table_prefix}bg_product_items as bg_product on bg_product_id = product_id"
                    ),
                    "where"     => array(
                        "( product.has_sub_product = 1 or product.product_type = 'Bundle' ) and bg_product.is_raw_materials = 0 and product.product_id = '{$productId}'"
                    )
                ));

                // Insert sub/ bundle products
                if($subProducts !== false) {

                    foreach($subProducts["data"] as $spKey => $sp) {

                        easyInsert(
                            "product_stock",
                            array (
                                "stock_type"            => ( $_POST["scType"] === "Dispatch" ) ? 'specimen-copy' : 'specimen-copy-return',
                                "stock_entry_date"      => $_POST["sctransferDate"],
                                "stock_sc_id"           => $_POST["scId"],
                                "stock_shop_id"         => $_SESSION["sid"],
                                "stock_product_id"      => $sp["bg_item_product_id"],
                                "stock_batch_id"        => NULL, // This batch option will integrate later, becase in this function for publication there will be no expriy product
                                "stock_employee_id"     => $_POST["scTransferRepresentative"],
                                "stock_warehouse_id"    => $_POST["scTransferWarehouseId"],
                                "stock_item_qty"        => $_POST["productQnt"][$key] * $sp["bg_product_qnt"],
                                "stock_created_by"      => $_SESSION["uid"],
                                "is_bundle_item"        => 1
                            )
                        );
            
                    }

                }
    
            }

        }
  
        $rdrTo = full_website_address() . "/invoice-print/?autoPrint=true&invoiceType=scpecimenCopy&msg=Successfully Updated&id=". $_POST["scId"];
        redirect($rdrTo);
        
      }

    ?>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="box box-default">

            <div class="box-header with-border">
                <h3 class="box-title"><?= __("Edit Specimen Copy"); ?></h3>
            </div> <!-- box box-default -->

            <div class="box-body">

                <?php 

                    $specimenCopy = easySelectA(array(
                        "table" => "specimen_copies",
                        "join"  => array(
                            "left join {$table_prefix}employees on emp_id = sc_employee_id"
                        ),
                        "where" => array(
                            "sc_id" => $_GET["edit"]
                        )
                    ));

                    if($specimenCopy === false) {

                        echo _e("Sorry! No entry found");

                    } else {

                        $sc = $specimenCopy["data"][0];

                ?>

                <!-- Form start -->
                <form method="post" id="specimenCopyForm" role="form" action="<?php echo full_website_address(); ?>/marketing/edit-specimen-copy/?action=updateSpecimenCopy" enctype="multipart/form-data">

                    <div class="row">

                        <div style="margin-top: 2px;" class="col-md-5">

                            <div class="form-group required">
                                <label for="sctransferDate"><?= __("Date:"); ?></label>
                                <div class="input-group data">
                                    <div class="input-group-addon">
                                        <li class="fa fa-calendar"></li>
                                    </div>
                                    <input type="text" name="sctransferDate" id="sctransferDate" value="<?php echo $sc["sc_date"]; ?>" class="form-control pull-right datePicker" required>
                                </div>
                            </div>
                            <div class="form-group required">
                                <label for="scType"><?= __("Type"); ?></label>
                                <select name="scType" id="scType" class="form-control">
                                    <option <?php echo $sc["sc_type"] === "Dispatch" ? "selected" : ""; ?> value="Dispatch">Dispatch</option>
                                    <option <?php echo $sc["sc_type"] === "Return" ? "selected" : ""; ?> value="Return">Return</option>
                                </select>
                            </div>
                            <div class="form-group required">
                                <label for="scTransferWarehouse"><?= __("Warehouse:"); ?></label>
                                <select name="scTransferWarehouse" id="scTransferWarehouse" class="form-control" required>
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
                                            $selected = $sc["sc_warehouse_id"] === $warehouse['warehouse_id'] ? "selected" : "";
                                            echo "<option {$selected} value='{$warehouse['warehouse_id']}'>{$warehouse['warehouse_name']}</option>";
                                        }

                                    ?>
                                </select>
                                <input type="hidden" name="scTransferWarehouseId" id="scTransferWarehouseId" value="<?php echo $sc["sc_warehouse_id"]; ?>">
                            </div>

                            <div class="form-group required">
                                <label for="scTransferRepresentative"><?= __("Representative:"); ?></label>
                                <select name="scTransferRepresentative" id="scTransferRepresentative" select2-minimum-input-length="1" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=employeeListAll" style="width: 100%;" required>
                                    <option value=""><?= __("Select Representative"); ?>....</option>
                                    <option selected value="<?php echo $sc["sc_employee_id"]; ?>"><?php echo $sc["emp_firstname"] . " ". $sc["emp_lastname"] ;?></option>
                                </select>
                            </div>
                            <input type="hidden" name="scId" value="<?php echo safe_entities($_GET["edit"]); ?>">

                            <div class="form-group">
                                <label for=""><?= __("Search Products"); ?></label>
                                <select name="selectProduct" id="selectProduct" class="form-control pull-left select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=productList" style="width: 100%;">
                                    <option value=""><?= __("Select Product"); ?>....</option>
                                </select>
                            </div>
                            <p class="text-center">-- <?= __("OR"); ?> --</p>
                            <div class="text-center">
                                <button data-toggle="modal" data-target="#browseProduct" type="button" class="btn btn-info"><i class="fa fa-folder-open"></i> <?= __("browse Product"); ?></button>
                            </div>
                            <br />

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

                        <!-- Right Column -->
                        <div class="col-sm-7">
                            <label for=""><?= __("Product Details"); ?></label>
                            <table id="productTable" class="tableBodyScroll table table-bordered table-striped table-hover">
                                <thead>
                                    <tr class="bg-primary">
                                        <th class="col-md-6 text-center"><?= __("Product Name"); ?></th>
                                        <th class="col-md-3 text-center"><?= __("Quantity"); ?></th>
                                        <th class="col-md-3 text-center"><?= __("Unit"); ?></th>
                                        <th style="width: 28px !important;">
                                            <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php 
                                        $selectScProduct = easySelectA(array(
                                            "table"     => "product_stock",
                                            "fields"    => "stock_product_id, product_name, product_unit, stock_item_qty",
                                            "join"      => array(
                                                "left join {$table_prefix}products on product_id = stock_product_id"
                                            ),
                                            "where"     => array(
                                                "stock_sc_id"   => $_GET["edit"]
                                            )
                                        ));

                                        if($selectScProduct !== false) {
                                            foreach($selectScProduct["data"] as $scProduct) {
                                                echo "<tr>
                                                    <input type='hidden' name='productID[]' class='productID' value='{$scProduct['stock_product_id']}'>
                                                    <td class='col-md-6'>{$scProduct['product_name']}</td>
                                                    <td class='col-md-3'>
                                                        <input onclick='this.select()' type='text' name='productQnt[]' value='{$scProduct['stock_item_qty']}' class='productQnt form-control text-center'>
                                                    </td>
                                                    <td class='col-md-3'>{$scProduct['product_unit']}</td>
                                                    <td style='width: 28px !important;'>
                                                        <i style='cursor: pointer;' class='fa fa-trash-o removeThisProduct'></i>
                                                    </td>
                                                </tr>";
                                            }
                                        }
                                    ?>
                                </tbody>

                                <tfoot>
                                </tfoot>
                            </table>
                        </div>

                    </div>

                    <div class="box-footer">
                        <button data-toggle="modal" type="submit" class="btn btn-primary"><i class="fa fa-shopping-cart"></i> <?= __("Update"); ?></button>
                    </div>

                </form> <!-- Form End -->

                <?php } ?>

            </div> <!-- box-body -->

        </div> <!-- content container-fluid -->

    </section> <!-- Main content End tag -->
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>

    var specimenCopyPageUrl = window.location.href;

    /* Browse Product */
    $("#browseProduct").on("show.bs.modal", function(e) {

        BMS.PRODUCT.showProduct();

    });

    
</script>