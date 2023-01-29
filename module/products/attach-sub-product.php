<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo __("Sub Product Attachment"); ?>
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


    <!-- Main content -->
    <section class="content container-fluid">

        <?php 

            $selectProduct = easySelectA(array(
                "table"     => "products",
                "fields"    => "product_name, has_sub_product",
                "where"     => array(
                    "has_sub_product = 1 and product_id"    => isset($_GET["pid"]) ? $_GET["pid"] : ""
                )
            ));

            if($selectProduct === false or empty($_GET["pid"]) ) {

                echo "<div class='alert alert-danger'>Sorry! this product is not eligible to link sub product.</div>";
                
            } else {

                $productName = $selectProduct["data"][0]["product_name"];
            

        ?>

            <!-- Form start -->
            <form method="post" role="form" id="jqFormAdd" action="<?php echo full_website_address(); ?>/xhr/?module=products&page=attachSubProduct" enctype="multipart/form-data">

                <div id="bundleProductsContainer" class="box box-default">
                    <div class="box-header">
                        <h3 class="box-title bundleProduct"><?php echo __("Attach/ Link sub product for ") . "<b>" . $productName . "</b>"; ?></h3>
                    </div>
                    <div class="box-body">

                        <div class="row">

                            <div style="margin-top: 2px;" class="col-md-4">

                                <div class="form-group">
                                    <label for=""><?php echo __("Search Products"); ?></label>
                                    <select name="selectProduct" id="selectProduct" class="form-control pull-left select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=productList" style="width: 100%;">
                                        <option value=""><?php echo __("Select Product"); ?>....</option>
                                    </select>
                                </div>
                                <p class="text-center">-- <?php echo __("OR"); ?> --</p>
                                <div class="text-center">
                                    <button data-toggle="modal" data-target="#browseProduct" type="button" class="btn btn-info"><i class="fa fa-folder-open"></i> <?php echo __("browse Product"); ?></button>
                                </div>
                                <input type="hidden" name="mainProduct" value="<?php echo safe_entities($_GET["pid"]); ?>">
                                <br />

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

                            <div class="col-sm-8">
                                <table id="productTable" class="tableBodyScroll table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr class="bg-primary">
                                            <th class="col-md-5 text-center"><?php echo __("Product Name"); ?></th>
                                            <th class="col-md-3 text-center"><?php echo __("Quantity"); ?></th>
                                            <th class="col-md-2 text-center"><?php echo __("Unit"); ?></th>
                                            <th class="col-md-2 text-center gbProductPrice"><?php echo __("Price"); ?></th>
                                            <th style="width: 28px !important;">
                                                <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                            <?php 

                                                $selectSubProducts = easySelectA(array(
                                                    "table"     => "bg_product_items",
                                                    "fields"    => "bg_item_product_id, product_name, product_unit, round(bg_product_price, 2) as bg_product_price, round(bg_product_qnt, 2) as bg_product_qnt",
                                                    "join"      => array(
                                                        "left join {$table_prefix}products on bg_item_product_id = product_id"
                                                    ),
                                                    "where"     => array(
                                                        "is_raw_materials = 0 and bg_product_id"     => $_GET["pid"]
                                                    )
                                                ));


                                                // Display sub products
                                                if($selectSubProducts !== false) {

                                                    foreach($selectSubProducts["data"] as $subProducts) {

                                                        echo '<tr>
                                                                <input type="hidden" name="bgProductID[]" class="productID" value="'. $subProducts["bg_item_product_id"] .'">
                                                                <td class="col-md-5">'. $subProducts["product_name"] .'</td>
                                                                <td class="col-md-3"><input onclick = "this.select()" type="text" name="bgProductQnt[]" value="'. $subProducts["bg_product_qnt"] .'" class="productQnt form-control text-center"></td>
                                                                <td class="col-md-2">'. $subProducts["product_unit"] .'</td>
                                                                <td class="text-right col-md-2 gbProductPrice"><input onclick = "this.select()" type="text" name="bgProductSalePrice[]" value="'. $subProducts["bg_product_price"] .'" class="productSalePrice form-control text-center" step="any"></td>
                                                                <td style="width: 28px !important; cursor: pointer;">
                                                                    <i class="fa fa-trash-o removeThisProduct"></i>
                                                                </td>
                                                            </tr>';

                                                    }

                                                }

                

                                            ?>

                                    </tbody>

                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>

                        </div> <!-- row -->

                    </div> <!-- ./box-body -->

                    <div class="box-footer">
                        <button type="submit" id="jqAjaxButton" class="btn btn-primary"><?php echo __("Update Sub Product"); ?></button>
                    </div>

                </div> <!--  Box End -->


            </form> <!-- Form End -->

        <?php } ?>

    </section> <!-- Main content End tag content container-fluid -->
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->



<script>

    var addProductPageUrl = window.location.href;

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