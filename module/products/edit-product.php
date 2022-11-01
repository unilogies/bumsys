<?php 

if(!empty($_GET["pid"]) ) {
    
    $product = easySelectA(array(
        "table"     => "products",
        "fields"    => "product_name, product_type, product_code, product_group, product_variations, has_sub_product, product_packet_qnt, category_name, product_category_id, 
                        product_edition, product_distributor_discount, product_wholesaler_discount, product_retailer_discount, product_consumer_discount,
                        brand_name, product_brand_id, product_generic, product_pages, product_isbn, product_published_date, 
                        round(product_purchase_price, 2) as product_purchase_price, round(product_sale_price, 2) as product_sale_price, product_initial_stock, has_expiry_date, 
                        product_weight, product_width, product_height, product_alert_qnt, product_description, maintain_stock, is_disabled",
        "join"      => array(
            "left join {$table_prefeix}product_category on product_category_id = category_id",
            "left join {$table_prefeix}product_brands on brand_id = product_brand_id"
        ),
        "where"     => array(
            "product_id"    => $_GET["pid"]
        )
    ))["data"][0];

}

?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo __("Products"); ?>
            <small><?php echo __("Edit Product"); ?></small>
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

        <?php if(!isset($product)): ?>

            <div class='alert alert-danger'>Sorry! No product found. Please check the product id</div>

        <?php else: ?>

        <!-- Form start -->
        <form method="post" role="form" id="jqFormUpdate" class="newProductAdd" action="<?php echo full_website_address(); ?>/xhr/?module=products&page=updateProduct" enctype="multipart/form-data">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title"><?= __("Edit Product: %s", $product["product_name"] ); ?></h3>
                </div> <!-- box box-default -->

                <div class="box-body">

                    <div class="row">

                        <div class="form-group col-md-6 required">
                            <label for="productName"><?php echo __("Product Name:"); ?></label>
                            <input type="text" name="productName" id="productName" value="<?php echo $product["product_name"]; ?>" class="form-control" required>
                            <input type="hidden" name="product_id" value="<?php echo htmlentities($_GET["pid"]); ?>">
                        </div>
                        <div class="form-group col-md-3 required">
                            <?php
                                if( $product["product_type"] === "Child" ) {
                                    echo "<div style='margin: 0' class='alert alert-warning'>Product type can not be changed for this product.</div>";
                                } else {
                            ?>
                            <label for="productType"><?php echo __("Product Type:"); ?></label>
                            <select name="productType" id="productType" class="form-control" required>
                                <?php 
                                    $productType = array('Normal', 'Bundle', 'Grouped', 'Variable', 'Child');
                                    foreach($productType as $type) {
                                        $selected = $product["product_type"] === $type ? "selected" : "";
                                        echo "<option $selected value='$type'>$type</option>";
                                    }
                                ?>
                            </select>
                            <?php } ?>
                        </div>
                        <div class="form-group required col-md-3">
                            <label for="productCode"><?php echo __("Product Code:"); ?></label>
                            <input type="text" name="productCode" id="productCode" value="<?php echo $product["product_code"]; ?>" onclick="select()" class="form-control" required>
                        </div>
                        <div class="form-group required col-lg-4 col-md-6">
                            <label for="productCategory"><?php echo __("Product Category:"); ?></label>
                            <select name="productCategory" id="productCategory" class="form-control select2Ajax" select2-create-new-url="<?php echo full_website_address(); ?>/xhr/?tooltip=true&module=products&page=newCategory" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productCategoryList" required>
                                <option value=""><?php echo __("Select Category"); ?>....</option>
                                <option selected value="<?php echo $product["product_category_id"]; ?>"><?php echo $product["category_name"]; ?></option>
                            </select>
                        </div>
                        <div class="form-group col-lg-2 col-md-3">
                            <label for="productName"><?php echo __("Product Group:"); ?></label>
                            <i data-toggle="tooltip" data-placement="top" title="Can be treated as sub categories. Not mandatory." class="fa fa-question-circle"></i>
                            <select name="productGroupSelect" id="productGroupSelect" class="form-control select2Ajax" select2-tag="true" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productGroupList">
                                <option value=""><?php echo __("Select Group"); ?>....</option>
                                <option <?php echo empty($product["product_group"]) ?: "selected"; ?> value="<?php echo $product["product_group"]; ?>"><?php echo $product["product_group"]; ?></option>
                            </select>
                        </div>
                        <div class="form-group col-lg-2 col-md-3">
                            <label for="maintainStockInventory"><?php echo __("Maintain Stock?"); ?></label>
                            <i data-toggle="tooltip" data-placement="left" title="Select Yes if want to maintain product stock / inventory. If you select no, you can sale product without having stock." class="fa fa-question-circle"></i>
                            <select name="maintainStockInventory" id="maintainStockInventory" class="form-control" required>
                                <option <?php echo ( $product["maintain_stock"] !== "0") ?: "selected"; ?> value="0">No</option>
                                <option <?php echo ( $product["maintain_stock"] !== "1") ?: "selected"; ?> value="1">Yes</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-2 col-md-3">
                            <label for="productIsDiscontinued"><?php echo __("Discontinued/ Disable?"); ?></label>
                            <i data-toggle="tooltip" data-placement="left" title="Select Yes if the product is discontinued or want to disable." class="fa fa-question-circle"></i>
                            <select name="productIsDiscontinued" id="productIsDiscontinued" class="form-control" required>
                                <option <?php echo ( $product["is_disabled"] !== "0") ?: "selected"; ?> value="0">No</option>
                                <option <?php echo ( $product["is_disabled"] !== "1") ?: "selected"; ?> value="1">Yes</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-2 col-md-3">
                            <label for="productHasExpiryDate"><?php echo __("Has expiry date?"); ?></label>
                            <i data-toggle="tooltip" data-placement="left" title="Select Yes if the product has expiry date and batch number." class="fa fa-question-circle"></i>
                            <select name="productHasExpiryDate" id="productHasExpiryDate" class="form-control" required>
                                <option <?php echo ( $product["has_expiry_date"] !== "0") ?: "selected"; ?> value="0">No</option>
                                <option <?php echo ( $product["has_expiry_date"] !== "1") ?: "selected"; ?> value="1">Yes</option>
                            </select>
                        </div>
                        <?php if (get_options("productSettingsCanAddBrands")) : ?>
                            <div class="form-group col-lg-2 col-md-3">
                                <label for="productBrandSelect"><?php echo __("Brand/Publisher:"); ?></label>
                                <select name="productBrandSelect" id="productBrandSelect" class="form-control select2Ajax" select2-create-new-url="<?php echo full_website_address(); ?>/xhr/?tooltip=true&module=products&page=newProductBrand" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productBrandList" required>
                                    <option value=""><?php echo __("Select Brand/Publisher"); ?>....</option>
                                    <option selected value="<?php echo $product["product_brand_id"]; ?>"><?php echo $product["brand_name"]; ?></option>
                                </select>
                            </div>
                        <?php endif; ?>

                        <?php if (get_options("productSettingsCanAddGeneric")) : ?>
                            <div class="form-group col-md-6 required">
                                <label for="productGenericSelect"><?php echo __("Generic:"); ?></label>
                                <select name="productGenericSelect" id="productGenericSelect" class="form-control select2Ajax" select2-create-new-url="<?php echo full_website_address(); ?>/xhr/?tooltip=true&module=products&page=newProductGeneric" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productGenericList" required>
                                    <option value=""><?php echo __("Select Generic"); ?>....</option>
                                    <option selected value="<?php echo $product["product_generic"]; ?>"><?php echo $product["product_generic"]; ?></option>
                                </select>
                            </div>
                        <?php endif; ?>

                        <?php if (get_options("productSettingsCanAddBookInfo")) : ?>
                            <div class="form-group col-md-2">
                                <label for="productTotalPages"><?php echo __("Total Page:"); ?></label>
                                <input type="text" name="productTotalPages" placeholder="Book's Total Pages" value="<?php echo $product["product_pages"]; ?>" id="productTotalPages" class="form-control">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="productISBN"><?php echo __("ISBN:"); ?></label>
                                <input type="text" name="productISBN" placeholder="Book's ISBN" value="<?php echo $product["product_isbn"]; ?>" id="productTotalPages" class="form-control">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="productPublishedDate"><?php echo __("Published Date:"); ?></label>
                                <input type="text" name="productPublishedDate" placeholder="Published Date" value="<?php echo $product["product_published_date"]; ?>" id="productPublishedDate" class="form-control datePicker">
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
                            <input type="number" name="productPurchasePrice" id="productPurchasePrice" value="<?php echo $product["product_purchase_price"]; ?>" class="form-control" step="any" required>
                        </div>
                        <div class="form-group col-md-2 required">
                            <label for="productSalePrice"><?php echo __("Sale Price"); ?></label>
                            <input type="number" name="productSalePrice" id="productSalePrice" value="<?php echo $product["product_sale_price"]; ?>" class="form-control" step="any" required>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="productDistributorDiscount"><?php echo __("Distributor Discount:"); ?></label>
                            <i data-toggle="tooltip" data-placement="bottom" title="<?php echo __("Percentage or Fixed amount."); ?>" class="fa fa-question-circle"></i>
                            <input type="text" name="productDistributorDiscount" id="productDistributorDiscount" value="<?php echo $product["product_distributor_discount"]; ?>" placeholder="Eg: 10 or 20%" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="productWholesalerDiscount"><?php echo __("Wholesaler Discount:"); ?></label>
                            <i data-toggle="tooltip" data-placement="bottom" title="<?php echo __("Percentage or Fixed amount."); ?>" class="fa fa-question-circle"></i>
                            <input type="text" name="productWholesalerDiscount" id="productWholesalerDiscount" value="<?php echo $product["product_wholesaler_discount"]; ?>" placeholder="Eg: 10 or 20%"  class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="productRetailerDiscount"><?php echo __("Retailer Discount:"); ?></label>
                            <i data-toggle="tooltip" data-placement="bottom" title="<?php echo __("Percentage or Fixed amount."); ?>" class="fa fa-question-circle"></i>
                            <input type="text" name="productRetailerDiscount" id="productRetailerDiscount" value="<?php echo $product["product_retailer_discount"]; ?>" placeholder="Eg: 10 or 20%"  class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="productConsumerDiscount"><?php echo __("Consumer Discount:"); ?></label>
                            <i data-toggle="tooltip" data-placement="bottom" title="<?php echo __("Percentage or Fixed amount."); ?>" class="fa fa-question-circle"></i>
                            <input type="text" name="productConsumerDiscount" id="productConsumerDiscount" value="<?php echo $product["product_consumer_discount"]; ?>" placeholder="Eg: 10 or 20%"  class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="productIntitalStock"><?php echo __("Initial Stock"); ?></label>
                            <i data-toggle="tooltip" data-placement="right" title="Opening or Initial stock of this product" class="fa fa-question-circle"></i>
                            <input <?php echo $product["has_expiry_date"] == 1 ? "disabled" : ""; ?> type="number" name="productIntitalStock" id="productIntitalStock" value="<?php echo $product["product_initial_stock"]; ?>" class="form-control productIntitalStock" step="any">
                        </div>
                        
                        <div class="form-group col-md-2">
                            <label for="productWeight"><?php echo __("Weight:"); ?></label>
                            <input type="text" name="productWeight" id="productWeight" value="<?php echo $product["product_weight"]; ?>" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="productWidth"><?php echo __("Width:"); ?></label>
                            <input type="text" name="productWidth" id="productWidth" value="<?php echo $product["product_width"]; ?>" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="productHeight"><?php echo __("Height:"); ?></label>
                            <input type="text" name="productHeight" id="productHeight" value="<?php echo $product["product_height"]; ?>" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="alertQuantity"><?php echo __("Alert Quantity"); ?></label>
                            <input type="number" name="alertQuantity" id="alertQuantity" value="<?php echo $product["product_alert_qnt"]; ?>" class="form-control" step="any">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="packetQuantity">Packet Quantity</label>
                            <i data-toggle="tooltip" data-placement="right" title="Quantity for per packet" class="fa fa-question-circle"></i>
                            <input type="number" name="packetQuantity" id="packetQuantity" value="<?php echo $product["product_packet_qnt"]; ?>" class="form-control" step="any">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="productDescription"><?php echo __("Product Description:"); ?></label>
                            <textarea name="productDescription" id="productDescription" rows="3" class="form-control"><?php echo $product["product_description"]; ?></textarea>
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
                                    <img style="margin: auto;" class="previewing" width="auto" height="140px" src="<?php echo full_website_address(); ?>/images/?for=products&id=<?php echo htmlentities($_GET["pid"]); ?>" />
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

                                    <?php 
                
                                        $selectSubProducts = easySelectA(array(
                                            "table"     => "bg_product_items",
                                            "fields"    => "bg_item_product_id, product_name, product_unit, round(bg_product_price, 2) as bg_product_price, round(bg_product_qnt, 2) as bg_product_qnt",
                                            "join"      => array(
                                                "left join {$table_prefeix}products on product_id = bg_item_product_id"
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

            </div> <!--  Box End -->

            <?php 

                // Product Variation is not available for child product
                if( $product["product_type"] !== "Child" ) {


                    $selectProductVariation = easySelectA(array(
                        "table"     => "products",
                        "fields"    => "product_id, product_name, product_type, product_code, product_group, product_variations,product_packet_qnt, 
                                        product_distributor_discount, product_wholesaler_discount, product_retailer_discount, product_consumer_discount,
                                        if(product_edition is null, '', product_edition) as product_edition, product_unit, product_generic, product_pages, product_isbn, product_published_date, 
                                        round(product_purchase_price, 2) as product_purchase_price, round(product_sale_price, 2) as product_sale_price, product_initial_stock, has_expiry_date, 
                                        product_weight, product_width, product_height, product_alert_qnt, product_description, maintain_stock, has_sub_product, is_disabled",
                        "where"     => array(
                            "is_trash = 0 and product_type = 'Child' and product_parent_id" => $_GET["pid"]
                        )
                    ));

 
            ?>

                <div style="<?php echo $selectProductVariation !== false ?: 'display: none;'; ?>" class="row" id="productVariationSection">
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

                                        $attributesAndVariations = empty($product["product_variations"]) ? array() : unserialize( html_entity_decode($product["product_variations"]) );

                                        echo '<div style="margin: 15px;" class="form-group row static-attribute">';
                                        echo '<label class="col-sm-2">Units:</label>';
                                        echo '  <div class="col-sm-8 attributeValue">';

                                        if ($selectUnit) {
                                            foreach ($selectUnit["data"] as $uVal) {

                                                $checked = ( isset($attributesAndVariations["Units"]) and $attributesAndVariations["Units"] !== NULL and in_array($uVal['unit_name'], $attributesAndVariations["Units"])) ? "checked" : "";
                                                echo "<input {$checked} class='units' type='checkbox' name='product_attribute[Units][]' id='{$uVal['unit_name']}' value='{$uVal['unit_name']}'>";
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

                                                $checked = ( isset($attributesAndVariations["Editions"]) and $attributesAndVariations["Editions"] !== NULL and in_array($edition['edition_name'], $attributesAndVariations["Editions"])) ? "checked" : "";
                                                echo "<input {$checked} class='editions' type='checkbox' name='product_attribute[Editions][]' id='{$edition['edition_name']}' value='{$edition['edition_name']}'>";
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
                                                "inner join {$table_prefeix}product_variations as product_variations on product_variations.pa_name = product_attributes.pa_name"
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

                                                    <?php 
                                                    
                                                    if($selectProductVariation !== false) {
                                                        $time = time();
                                                        foreach($selectProductVariation["data"] as $variationProduct) {

                                                            $variableProductShowName = "";

                                                            // Check if the variation array is not empty in database
                                                            if( empty($variationProduct["product_variations"]) ) {

                                                                $variableProductShowName = $variationProduct["product_name"];

                                                            } else {

                                                                $unserializedProductVariation = unserialize( html_entity_decode($variationProduct["product_variations"]) );
                                                                
                                                                if(!empty($variationProduct["product_unit"])) {
                                                                    
                                                                    // If there are any product unit, append with $unserializedProductVariation
                                                                    array_push($unserializedProductVariation, $variationProduct["product_unit"]);

                                                                }

                                                                $variableProductShowName = join(", ", $unserializedProductVariation);

                                                            }

                                                            

                                                            $variationId = $time++;

                                                            echo '<li style="overflow: auto;">
                                                                    <div data-toggle="collapse" data-parent=".product-variation-list" href="#variation_'. $variationId .'" style="cursor: pointer;">
                                                                        <input type="radio" name="defaultVariation" value="'. $variationProduct["product_code"] .'" class="defaultVariation stopPropagation" data-html="true" data-toggle="tooltip" data-placement="top" data-original-title="Mark this as <br/>default variation">
                                                                        <span class="handle">
                                                                            <i class="fa fa-ellipsis-v"></i>
                                                                            <i class="fa fa-ellipsis-v"></i>
                                                                        </span>
                                                                        <span class="text">'. $variableProductShowName .'</span>
                                                                        <div class="tools">
                                                                            <i class="fa fa-edit"></i>
                                                                            <i class="fa fa-trash-o removeThisVariation"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div id="variation_'. $variationId .'" class="panel-collapse collapse">
                                                                        <input type="hidden" name="edit[variation_product_id][]" class="variation_product_id" value="'. $variationProduct["product_id"] .'">
                                                                        <input type="hidden" name="edit[product_variation][Units][]" class="variation_product_id" value="'. $variationProduct["product_unit"] .'">
                                                                        <input type="hidden" name="edit[product_variation][Editions][]" class="variation_product_id" value="'. $variationProduct["product_edition"] .'">
                                                                        <div style="border-top: 1px solid #eee; margin-top: 15px; padding-bottom: 0px;" class="box-body">
                                                                            <div class="form-group required col-md-2">
                                                                                <label>'. __("Code:") .'</label>
                                                                                <input type="text" name="edit[productVariationCode][]" value="'. $variationProduct["product_code"] .'" onclick="select()" class="productVariationCode form-control" required>
                                                                            </div>
                                                                            <div class="form-group col-md-2">
                                                                                <label>'. __("Purchase Price") .'</label>
                                                                                <input type="number" name="edit[productVariationPurchasePrice][]" value="'. $variationProduct["product_purchase_price"] .'" class="form-control productVariationPurchasePrice" step="any">
                                                                            </div>
                                                                            <div class="form-group col-md-2">
                                                                                <label>'. __("Sale Price") .'</label>
                                                                                <input type="number" name="edit[productVariationSalePrice][]" value="'. $variationProduct["product_sale_price"] .'" class="form-control productVariationSalePrice" step="any">
                                                                            </div>
                                                                            <div class="form-group col-md-2">
                                                                                <label>'. __("Distributor Discount:") .'</label>
                                                                                <input type="text" name="edit[productDistributorVariationDiscount][]" value="'. $variationProduct["product_distributor_discount"] .'" class="form-control productVariationDiscount">
                                                                            </div>
                                                                            <div class="form-group col-md-2">
                                                                                <label>'. __("Wholesaler Discount:") .'</label>
                                                                                <input type="text" name="edit[productWholesalerVariationDiscount][]" value="'. $variationProduct["product_wholesaler_discount"] .'" class="form-control productVariationDiscount">
                                                                            </div>
                                                                            <div class="form-group col-md-2">
                                                                                <label>'. __("Retailer Discount:") .'</label>
                                                                                <input type="text" name="edit[productRetailerVariationDiscount][]" value="'. $variationProduct["product_retailer_discount"] .'" class="form-control productVariationDiscount">
                                                                            </div>
                                                                            <div class="form-group col-md-2">
                                                                                <label>'. __("Consumer Discount:") .'</label>
                                                                                <input type="text" name="edit[productConsumerVariationDiscount][]" value="'. $variationProduct["product_consumer_discount"] .'" class="form-control productVariationDiscount">
                                                                            </div>
                                                                            <div class="form-group col-md-2">
                                                                                <label>'. __("Initial Stock") .'</label>
                                                                                <i data-toggle="tooltip" data-placement="right" title="Opening or Initial stock of this variation" class="fa fa-question-circle"></i>
                                                                                <input readonly type="number" name="edit[productVariationIntitalStock][]" class="form-control productIntitalStock" value="'. $variationProduct["product_initial_stock"] .'" step="any">
                                                                            </div>
                                                                            <div class="form-group col-md-2">
                                                                                <label for="productVariationHasSubProduct">'. __("Has sub product?") .'</label>
                                                                                <i data-toggle="tooltip" data-placement="left" title="Select Yes if the variation has sub product." class="fa fa-question-circle"></i>
                                                                                <select name="edit[productVariationHasSubProduct][]" id="productVariationHasSubProduct" class="form-control" required>
                                                                                    <option '. ($variationProduct["has_sub_product"] == 0 ? "selected" : "") .' value="0">No</option>
                                                                                    <option '. ($variationProduct["has_sub_product"] == 1 ? "selected" : "") .' value="1">Yes</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="form-group col-md-2">
                                                                                <label>'. __("Weight:") .'</label>
                                                                                <input type="text" name="edit[productVariationWeight][]" value="'. $variationProduct["product_weight"] .'" class="form-control">
                                                                            </div>
                                                                            <div class="form-group col-md-2">
                                                                                <label>'. __("Width:") .'</label>
                                                                                <input type="text" name="edit[productVariationWidth][]" value="'. $variationProduct["product_height"] .'" class="form-control">
                                                                            </div>
                                                                            <div class="form-group col-md-2">
                                                                                <label>'. __("Height:") .'</label>
                                                                                <input type="text" name="edit[productVariationHeight][]" value="'. $variationProduct["product_width"] .'" class="form-control">
                                                                            </div>
                                                                            
                                                                            <div class="form-group col-md-6">
                                                                                <label>'. __("Description:") .'</label>
                                                                                <textarea name="edit[productVariationDescription][]" rows="3" class="form-control">'. $variationProduct["product_description"] .'</textarea>
                                                                            </div>
                                                                            <div class="imageContainer">
                                                                                <div class="col-md-3">
                                                                                    <div class="form-group">
                                                                                        <label for="">'. __("Product Photo:") .'</label>
                                                                                        <div class="input-group">
                                                                                            <span class="input-group-btn">
                                                                                                <span class="btn btn-default btn-file">
                                                                                                    '. __("Select photo") .' <input type="file" name="editProductVariationPhoto[]" class="imageToUpload">
                                                                                                </span>
                                                                                            </span>
                                                                                            <input type="text" class="form-control imageNameShow" readonly>
                                                                                        </div>
                                                                                        <div style="margin-top: 8px;" class="photoErrorMessage"></div>
                                                                                    </div>
                                                                                </div>
                                                                                <div style="margin-bottom: 5px;" class="form-group col-md-3">
                                                                                    <div style="height: 120px; text-align: center;" class="image_preview">
                                                                                        <img style="margin: auto;" class="previewing" width="auto" height="140px" src="'. full_website_address() .'/images/?for=products&id='. $variationProduct["product_id"] .'" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>';
                                                            }
                                                        }

                                                    ?>

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

            <?php } ?>

            <!-- END CUSTOM TABS -->

            <div class="box box-default">

                <div class="box-body">

                </div> <!-- ./box-body -->

                <div class="box-footer">
                    <button type="submit" id="jqAjaxButton" class="btn btn-primary"><?php echo __("Update Product"); ?></button>
                </div>

            </div>


        </form> <!-- Form End -->

        <?php endif; ?>

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