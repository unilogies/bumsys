<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("Specimen Copy Distribution"); ?>
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

        if(isset($_GET["action"]) and $_GET["action"] == "distributeSpecimenCopy" and !empty($_POST)) {
            
            // Insert specimen copy distribution
            foreach($_POST["productID"] as $key => $productId) {

                easyInsert(
                    "sc_distribution",
                    array(
                        "scd_date"            => $_POST["scDistributionDate"],
                        "scd_distributor"     => $_POST["scDistributor"],
                        "scd_person_id"       => $_POST["scRecipient"],
                        "scd_product_id"      => $productId,
                        "scd_product_qnt"     => $_POST["productQnt"][$key],
                        "scd_add_by"          => $_SESSION["uid"]
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
                            "sc_distribution",
                            array(
                                "scd_distributor"     => $_POST["scDistributor"],
                                "scd_person_id"       => $_POST["scRecipient"],
                                "scd_product_id"      => $sp["bg_item_product_id"],
                                "scd_product_qnt"     => $_POST["productQnt"][$key] * $sp["bg_product_qnt"],
                                "scd_add_by"          => $_SESSION["uid"],
                                "is_bundle_item"      => 1
                            )
                        );
            
                    }

                }

            }
    
            $rdrTo = full_website_address() . "/marketing/specimen-copy-distributions/?action=distribute";
            redirect($rdrTo);
            
        }

    ?>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="box box-default">

            <div class="box-header with-border">
                <h3 class="box-title"><?= __("Distribute Specimen Copy"); ?></h3>
            </div> <!-- box box-default -->

            <div class="box-body">

                <!-- Form start -->
                <form method="post" role="form" id="scDistributionForm" action="<?php echo full_website_address(); ?>/marketing/new-sc-distribution/?action=distributeSpecimenCopy" enctype="multipart/form-data">

                    <div class="row">

                        <div style="margin-top: 2px;" class="col-md-5">

                            <div class="form-group required">
                                <label for="scDistributionDate"><?= __("Date:"); ?></label>
                                <div class="input-group data">
                                    <div class="input-group-addon">
                                        <li class="fa fa-calendar"></li>
                                    </div>
                                    <input type="text" name="scDistributionDate" id="scDistributionDate" value="<?php echo date("Y-m-d"); ?>" class="form-control pull-right datePicker" required>
                                </div>
                            </div>

                            <div class="form-group required">
                                <label for="scDistributor"><?= __("Distributor:"); ?></label>
                                <select name="scDistributor" id="scDistributor" select2-minimum-input-length="1" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=employeeListAll" style="width: 100%;" required>
                                    <option value=""><?= __("Select distributor"); ?>....</option>
                                </select>
                            </div>
                            <div class="form-group required">
                                <label for="scRecipient"><?= __("Recipient/Teacher:"); ?></label>
                                <div class="input-group">
                                <select name="scRecipient" id="scRecipient" select2-minimum-input-length="1" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=personList" style="width: 100%;" required>
                                    <option value=""><?= __("Select Recepient"); ?>....</option>
                                </select>
                                <div class="input-group-addon">
                                    <a data-toggle="modal" data-target="#modalDefaultMdm" href="<?php echo full_website_address(); ?>/xhr/?module=marketing&page=newPerson" ><i class="fa fa-plus"></i></a>
                                </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for=""><?= __("Search Products"); ?></label>
                                <div class="input-group">
                                    <select name="selectProduct" id="selectProduct" class="form-control pull-left select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=productList" style="width: 100%;">
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
                                </tbody>

                                <tfoot>
                                </tfoot>
                            </table>
                        </div>


                    </div>


                    <div class="box-footer">
                        <button data-toggle="modal" type="submit" class="btn btn-primary"><i class="fa fa-shopping-cart"></i> <?= __("Submit"); ?></button>
                    </div>

                </form> <!-- Form End -->

            </div> <!-- box-body -->

        </div> <!-- content container-fluid -->

    </section> <!-- Main content End tag -->
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>

    var scDistributionPageUrl = window.location.href;

    /* Browse Product */
    $("#browseProduct").on("show.bs.modal", function(e) {

        BMS.PRODUCT.showProduct();

    });

</script>