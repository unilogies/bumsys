<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo __("Products"); ?>
            <small><?php echo __("New Product"); ?></small>
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
            height: 20vh;
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

        <!-- Form start -->
        <form method="post" role="form" id="inlineForm" class="newProductAdd" action="<?php echo full_website_address(); ?>/xhr/?module=products&page=newProduct" enctype="multipart/form-data">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo __("New Product"); ?></h3>
                </div> <!-- box box-default -->

                <div class="box-body">

                    <div class="row">

                        <div class="form-group col-md-6 required">
                            <label for="productName"><?php echo __("Product Name:"); ?></label>
                            <input type="text" name="productName" id="productName" class="form-control" required>
                        </div>
                        <div class="form-group col-md-3 required">
                            <label for="productType"><?php echo __("Product Type:"); ?></label>
                            <select name="productType" id="productType" class="form-control" required>
                                <option value="Normal">Normal</option>
                                <option value="Bundle">Bundle</option>
                                <option value="Grouped">Grouped</option>
                                <option value="Variable">Variable</option>
                            </select>
                        </div>
                        <div class="form-group required col-md-3">
                            <label for="productCode"><?php echo __("Product Code:"); ?></label>
                            <input type="text" name="productCode" id="productCode" value="<?php echo round(microtime(true) * 1000); ?>" onclick="select()" class="form-control" required>
                        </div>
                        <div class="form-group required col-lg-4 col-md-6">
                            <label for="productCategory"><?php echo __("Product Category:"); ?></label>
                            <select name="productCategory" id="productCategory" class="form-control select2Ajax" select2-create-new-url="<?php echo full_website_address(); ?>/xhr/?tooltip=true&module=products&page=newCategory" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productCategoryList" required>
                                <option value=""><?php echo __("Select Category"); ?>....</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-2 col-md-3">
                            <label for="productName"><?php echo __("Product Group:"); ?></label>
                            <i data-toggle="tooltip" data-placement="top" title="Can be treated as sub categories. Not mandatory." class="fa fa-question-circle"></i>
                            <select name="productGroupSelect" id="productGroupSelect" class="form-control select2Ajax" select2-tag="true" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productGroupList">
                                <option value=""><?php echo __("Select Group"); ?>....</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-2 col-md-3">
                            <label for="maintainStockInventory"><?php echo __("Maintain Stock?"); ?></label>
                            <i data-toggle="tooltip" data-placement="left" title="Select Yes if want to maintain product stock / inventory. If you select no, you can sale product without having stock." class="fa fa-question-circle"></i>
                            <select name="maintainStockInventory" id="maintainStockInventory" class="form-control" required>
                                <option value="0">No</option>
                                <option selected value="1">Yes</option>
                            </select>
                        </div>
                        <!-- <div class="form-group col-md-3">
                            <label for="productHasSubProduct"><?php echo __("Has sub product?"); ?></label>
                            <i data-toggle="tooltip" data-placement="left" title="Select Yes if the product has sub product." class="fa fa-question-circle"></i>
                            <select name="productHasSubProduct" id="productHasSubProduct" class="form-control" required>
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div> -->
                        <div class="form-group col-lg-2 col-md-3">
                            <label for="productIsDiscontinued"><?php echo __("Discontinued/ Disable?"); ?></label>
                            <i data-toggle="tooltip" data-placement="left" title="Select Yes if the product is discontinued or want to disable." class="fa fa-question-circle"></i>
                            <select name="productIsDiscontinued" id="productIsDiscontinued" class="form-control" required>
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-2 col-md-3">
                            <label for="productHasExpiryDate"><?php echo __("Has expiry date?"); ?></label>
                            <i data-toggle="tooltip" data-placement="left" title="Select Yes if the product has expiry date and batch number." class="fa fa-question-circle"></i>
                            <select name="productHasExpiryDate" id="productHasExpiryDate" class="form-control" required>
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <?php if (get_options("productSettingsCanAddBrands")) : ?>
                            <div class="form-group col-lg-2 col-md-3">
                                <label for="productBrandSelect"><?php echo __("Brand/Publisher:"); ?></label>
                                <select name="productBrandSelect" id="productBrandSelect" class="form-control select2Ajax" select2-create-new-url="<?php echo full_website_address(); ?>/xhr/?tooltip=true&module=products&page=newProductBrand" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productBrandList" required>
                                    <option value=""><?php echo __("Select Brand/Publisher"); ?>....</option>
                                </select>
                            </div>
                        <?php endif; ?>

                        <?php if (get_options("productSettingsCanAddGeneric")) : ?>
                            <div class="form-group col-md-6 required">
                                <label for="productGenericSelect"><?php echo __("Generic:"); ?></label>
                                <select name="productGenericSelect" id="productGenericSelect" class="form-control select2Ajax" select2-create-new-url="<?php echo full_website_address(); ?>/xhr/?tooltip=true&module=products&page=newProductGeneric" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productGenericList" required>
                                    <option value=""><?php echo __("Select Generic"); ?>....</option>
                                </select>
                            </div>
                        <?php endif; ?>

                        <?php if (get_options("productSettingsCanAddBookInfo")) : ?>
                            <div class="form-group col-md-2">
                                <label for="productTotalPages"><?php echo __("Total Page:"); ?></label>
                                <input type="text" name="productTotalPages" placeholder="Book's Total Pages" id="productTotalPages" class="form-control">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="productISBN"><?php echo __("ISBN:"); ?></label>
                                <input type="text" name="productISBN" placeholder="Book's ISBN" id="productTotalPages" class="form-control">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="productPublishedDate"><?php echo __("Published Date:"); ?></label>
                                <input type="text" name="productPublishedDate" placeholder="Published Date" id="productPublishedDate" class="form-control datePicker">
                            </div>
                            <div class="form-group col-md-6 bookAuthor">
                                <label for="bookAuthor"><?php echo __("Author:"); ?></label>
                                <select style="height: 32px;" multiple name="bookAuthor[]" id="bookAuthor" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=authorList">
                                    <option value=""><?php echo __("Select Author"); ?>....</option>
                                </select>
                            </div>
                        <?php endif; ?>

                    </div>



                    <div class="row">

                        <div class="form-group col-md-2 required">
                            <label for="productPurchasePrice"><?php echo __("Purchase Price"); ?></label>
                            <i data-toggle="tooltip" data-placement="right" title="Costing or Purchase Price" class="fa fa-question-circle"></i>
                            <input type="number" name="productPurchasePrice" id="productPurchasePrice" class="form-control" step="any" required>
                        </div>
                        <div class="form-group col-md-2 required">
                            <label for="productSalePrice"><?php echo __("Sale Price"); ?></label>
                            <input type="number" name="productSalePrice" id="productSalePrice" class="form-control" step="any" required>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="productDistributorDiscount"><?php echo __("Distributor Discount:"); ?></label>
                            <i data-toggle="tooltip" data-placement="bottom" title="<?php echo __("Percentage or Fixed amount."); ?>" class="fa fa-question-circle"></i>
                            <input type="text" name="productDistributorDiscount" id="productDistributorDiscount" placeholder="Eg: 10 or 20%" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="productWholesalerDiscount"><?php echo __("Wholesaler Discount:"); ?></label>
                            <i data-toggle="tooltip" data-placement="bottom" title="<?php echo __("Percentage or Fixed amount."); ?>" class="fa fa-question-circle"></i>
                            <input type="text" name="productWholesalerDiscount" id="productWholesalerDiscount" placeholder="Eg: 10 or 20%"  class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="productRetailerDiscount"><?php echo __("Retailer Discount:"); ?></label>
                            <i data-toggle="tooltip" data-placement="bottom" title="<?php echo __("Percentage or Fixed amount."); ?>" class="fa fa-question-circle"></i>
                            <input type="text" name="productRetailerDiscount" id="productRetailerDiscount" placeholder="Eg: 10 or 20%"  class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="productConsumerDiscount"><?php echo __("Consumer Discount:"); ?></label>
                            <i data-toggle="tooltip" data-placement="bottom" title="<?php echo __("Percentage or Fixed amount."); ?>" class="fa fa-question-circle"></i>
                            <input type="text" name="productConsumerDiscount" id="productConsumerDiscount" placeholder="Eg: 10 or 20%"  class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="productIntitalStock"><?php echo __("Initial Stock"); ?></label>
                            <i data-toggle="tooltip" data-placement="right" title="Opening or Initial stock of this product" class="fa fa-question-circle"></i>
                            <input type="number" name="productIntitalStock" id="productIntitalStock" class="form-control productIntitalStock" step="any">
                        </div>
                        
                        <div class="form-group col-md-2">
                            <label for="productWeight"><?php echo __("Weight:"); ?></label>
                            <input type="text" name="productWeight" id="productWeight" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="productWidth"><?php echo __("Width:"); ?></label>
                            <input type="text" name="productWidth" id="productWidth" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="productHeight"><?php echo __("Height:"); ?></label>
                            <input type="text" name="productHeight" id="productHeight" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="alertQuantity"><?php echo __("Alert Quantity"); ?></label>
                            <input type="number" name="alertQuantity" id="alertQuantity" class="form-control" step="any">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="packetQuantity">Packet Quantity</label>
                            <i data-toggle="tooltip" data-placement="right" title="Quantity for per packet" class="fa fa-question-circle"></i>
                            <input type="number" name="packetQuantity" id="packetQuantity" class="form-control" step="any">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="productDescription"><?php echo __("Product Description:"); ?></label>
                            <textarea name="productDescription" id="productDescription" rows="3" class="form-control"></textarea>
                        </div>

                        <div class="imageContainer">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for=""><?php echo __("Product Photo:"); ?></label>
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-default btn-file">
                                                <?php echo __("Select photo"); ?> <input type="file" name="productPhoto" class="imageToUpload">
                                            </span>
                                        </span>
                                        <input type="text" class="form-control imageNameShow" readonly>
                                    </div>

                                    <div style="margin-top: 8px;" class="photoErrorMessage"></div>

                                </div>
                            </div>

                            <div style="margin-bottom: 5px;" class="form-group col-md-3">
                                <div style="height: 120px; text-align: center;" class="image_preview">
                                    <img style="margin: auto;" class="previewing" width="100%" height="auto" src="" />
                                </div>
                            </div>

                        </div>


                    </div>

                </div> <!-- ./box-body -->

            </div> <!--  Box End -->

            <div style="display: none;" id="bundleProductsContainer" class="box box-default">
                <div class="box-header">
                    <h3 class="box-title bundleProduct"><?php echo __("Bundle Product"); ?></h3>
                    <h3 class="box-title groupedProduct"><?php echo __("Grouped Product"); ?></h3>
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
                                </tbody>

                                <tfoot>
                                </tfoot>
                            </table>
                        </div>

                    </div> <!-- row -->

                </div> <!-- ./box-body -->

            </div> <!--  Box End -->


            <div style="display:none;" class="row" id="productVariationSection">
                <div class="col-md-12">
                    <!-- Custom Tabs -->
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li><a href="#tab_1" data-toggle="tab">Static Attributes</a></li>
                            <li><a href="#tab_2" data-toggle="tab">Dynamic Attributes</a></li>
                            <li class="active"><a href="#tab_3" data-toggle="tab">Variations</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane" id="tab_1">

                                <?php

                                    $selectUnit = easySelectA([
                                        "table" => "product_units",
                                        "where" => [
                                            "is_trash = 0"
                                        ]
                                    ]);

                                    echo '<div style="margin: 15px;" class="form-group row static-attribute">';
                                    echo '<label class="col-sm-2">Units:</label>';
                                    echo '  <div class="col-sm-8 attributeValue">';

                                    if ($selectUnit) {
                                        foreach ($selectUnit["data"] as $uVal) {

                                            echo "<input class='units' type='checkbox' name='product_attribute[Units][]' id='{$uVal['unit_name']}' value='{$uVal['unit_name']}'>";
                                            echo "<label for='{$uVal['unit_name']}'> &nbsp;&nbsp;{$uVal['unit_name']}</label><br/>";
                                            
                                        }
                                    }

                                    echo '  </div>';
                                    echo '</div>';


                                    $selectEdition = easySelectA([
                                        "table"     => "product_editions",
                                        "fields"    => "edition_name",
                                        "where" => array(
                                            "is_trash = 0"
                                        ),
                                        "orderby"   => array(
                                            "edition_name"   => "DESC"
                                        )
                                    ]);

                                    echo '<div style="margin: 15px;" class="form-group row static-attribute">';
                                    echo '<label class="col-sm-2">Edition:</label>';
                                    echo '  <div class="col-sm-8 attributeValue">';


                                    if ($selectEdition !== false) {
                                        foreach ($selectEdition["data"] as $edition) {

                                            echo "<input class='editions' type='checkbox' name='product_attribute[Editions][]' id='{$edition['edition_name']}' value='{$edition['edition_name']}'>";
                                            echo "<label for='{$edition['edition_name']}'> &nbsp;&nbsp;{$edition['edition_name']}</label><br/>";

                                        }
                                    }

                                    echo '  </div>';
                                    echo '</div>';

                                
                                ?>


                            </div>

                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="tab_2">
                                <?php

                                $selectAttributes = easySelectA([
                                    "table" => "product_attributes as product_attributes",
                                    "fields"    => "product_attributes.pa_name as attribute_name, group_concat(pv_name) as variations",
                                    "where" => [
                                        "product_attributes.is_trash=0 and product_variations.is_trash = 0"
                                    ],
                                    "groupby"   => "product_attributes.pa_name",
                                    "join"  => [
                                        "inner join {$table_prefix}product_variations as product_variations on product_variations.pa_name = product_attributes.pa_name"
                                    ]
                                ]);


                                if ($selectAttributes) {
                                    foreach ($selectAttributes["data"] as $paVal) { ?>

                                        <div style="margin: 15px;" class="form-group row attribute">
                                            <label class="col-sm-2"><?php echo $paVal['attribute_name']; ?>:</label>
                                            <input type="hidden" class="attributeName" value="<?php echo $paVal['attribute_name']; ?>">
                                            <div class="col-sm-8 attributeValue">
                                                <?php

                                                $variation = explode(",", $paVal["variations"]);

                                                foreach ($variation as $pv) {
                                                    echo "<input type='checkbox' name='product_attribute[{$paVal['attribute_name']}][]' id='pa_{$paVal['attribute_name']}_pv_{$pv}' value='{$pv}'>";
                                                    echo "<label for='pa_{$paVal['attribute_name']}_pv_{$pv}'> &nbsp;&nbsp;{$pv}</label><br/>";
                                                }

                                                ?>

                                            </div>
                                        </div>

                                <?php }
                                }

                                ?>
                            </div>

                            <!-- /.tab-pane -->
                            <div class="tab-pane active variation-list" id="tab_3">

                                <div class="row">

                                    <div class="col-md-12">
                                        <br />
                                        <div class="form-group">
                                            <input type="button" onclick="generateVariation();" value="Generate Variation" class="btn btn-primary">
                                            <input type="button" onclick="addVariation();" value="Add Variation" class="btn btn-primary">
                                            <input data-toggle="modal" data-target="#productVariationPrice" type="button" value="Set Prices" class="btn btn-primary">
                                            <input type="button" onclick="$('.collapse').collapse('show');" value="Expand All" class="btn btn-default">
                                            <input type="button" onclick="$('.collapse').collapse('hide');" value="Close All" class="btn btn-default">
                                        </div>
                                        <hr />
                                        <div class="box-body">

                                            <!-- All variation will be goes here -->
                                            <ul class="product-variation-list todo-list">

                                            </ul>

                                        </div>

                                    </div>

                                    <!-- Set pice Modal -->
                                    <div class="modal fade" id="productVariationPrice">
                                        <div class="modal-dialog modal-default">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title"><?php echo __("Product variation prices"); ?> </h4>
                                                </div>
                                                <div class="modal-body">

                                                    <div class="form-group">
                                                        <label><?php echo __("Purchase Price"); ?></label>
                                                        <input type="text" id="productVariationPurchasePriceSetter" value="" class="form-control">
                                                    </div>
                                                    <div class="form-group">
                                                        <label><?php echo __("Sale Price"); ?></label>
                                                        <input type="text" id="productVariationSelePriceSetter" value="" class="form-control">
                                                    </div>
                                                    <div class="form-group">
                                                        <label><?php echo __("Discount:"); ?></label>
                                                        <input type="text" id="productVariationDiscountSetter" value="" class="form-control">
                                                    </div>
                                                    <div class="form-group">
                                                        <label><?php echo __("Where, Unit:"); ?></label>
                                                        <select name="SetPricesWhereUnit" id="SetPricesWhereUnit" class="form-control">
                                                            <option value="">All Units</option>
                                                            <?php
                                                            if ($selectUnit) {
                                                                foreach ($selectUnit["data"] as $uVal) {
                                                                    echo "<option value='{$uVal['unit_name']}'>{$uVal['unit_name']}</option>";
                                                                }
                                                            }
                                                            ?>
                                                        </select>

                                                    </div>

                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo __("Close"); ?></button>
                                                    <button type="button" id="setVariationPrice" class="btn btn-primary" data-dismiss="modal"><?php echo __("Update"); ?></button>
                                                </div>
                                            </div>
                                            <!-- /. Browse Product modal-content -->
                                        </div>
                                        <!-- /. Browse Product modal-dialog -->
                                    </div>
                                    <!-- /.Browse Product modal -->


                                    <!-- /.box-body -->

                                </div>

                            </div>
                            <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div>
                    <!-- nav-tabs-custom -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- END CUSTOM TABS -->

            <div class="box box-default">

                <div class="box-body">

                </div> <!-- ./box-body -->

                <div class="box-footer">
                    <button type="submit" id="jqAjaxButton" class="btn btn-primary"><?php echo __("Add Product"); ?></button>
                </div>

            </div>


        </form> <!-- Form End -->

    </section> <!-- Main content End tag content container-fluid -->
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<style>
    .panel {
        margin-bottom: 0px !important;
        padding: 5px 0 !important;
        border-bottom: 1px solid #eee !important;
        border-radius: 0 !important;
    }

    .todo-list>li {
        border-radius: 2px;
        padding: 15px;
        background: transparent;
        border-left: none;
        border-bottom: 1px solid #eee;
        color: #444;
    }
</style>

<script>
    var addProductPageUrl = window.location.href;

    $(document).ready(function(e) {

        // jQuery UI sortable for variation
        $('.product-variation-list').sortable({
            placeholder: 'sort-highlight',
            handle: '.handle',
            forcePlaceholderSize: true,
            zIndex: 999999
        });

    });

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