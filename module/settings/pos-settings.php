<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("POS Settings"); ?>
        </h1>
    </section>

    <style>
        .radiousPosition {
            margin-left: 10px;
            cursor: pointer;
        }
    </style>


    <!-- Main content -->
    <section class="content container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <!-- Form start -->
                    <form method="post" role="form" id="jqFormAdd" action="<?php echo full_website_address(); ?>/xhr/?module=settings&page=saveSystemSettings">
                        <div class="box-body">

                            <div class="form-group row">
                                <label class="col-sm-3" for="companyName"><?= __("Allow to"); ?></label>
                                <div class="col-sm-6">

                                    <input type="hidden" value="0" name="allowToAddStockOutProductInPOS">
                                    <input <?php echo get_options("allowToAddStockOutProductInPOS") ? "checked" : ""; ?> class="square" type="checkbox" value="1" name="allowToAddStockOutProductInPOS" id="allowToAddStockOutProductInPOS">
                                    <label for="allowToAddStockOutProductInPOS"> &nbsp;&nbsp;<?= __("Add stock out product"); ?> </label><br />
                                    <input type="hidden" value="0" name="allowToSaleStockOutProductInPOS">
                                    <input <?php echo get_options("allowToSaleStockOutProductInPOS") ? "checked" : ""; ?> class="square" type="checkbox" value="1" name="allowToSaleStockOutProductInPOS" id="allowToSaleStockOutProductInPOS">
                                    <label for="allowToSaleStockOutProductInPOS"> &nbsp;&nbsp;<?= __("Sale stock out product"); ?></label>

                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3" for="defaultProductCategory"><?= __("Default Category:"); ?></label>
                                <div class="col-sm-6">
                                    <select name="defaultProductCategory" id="defaultProductCategory" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productCategoryList">
                                        <option value=""><?= __("All Category"); ?></option>
                                        <?php
                                        if (!empty(get_options("defaultProductCategory"))) {

                                            $selectCategory = easySelectA(array(
                                                "table" => "product_category",
                                                "where" => array(
                                                    "category_id" => get_options("defaultProductCategory")
                                                )
                                            ))["data"][0];

                                            echo "<option selected value='{$selectCategory['category_id']}'>{$selectCategory['category_name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3" for="defaultProductBrand"><?= __("Default Brand:"); ?></label>
                                <div class="col-sm-6">
                                    <select name="defaultProductBrand" id="defaultProductBrand" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productBrandList">
                                        <option value=""><?= __("All Brand"); ?></option>
                                        <?php
                                        if (!empty(get_options("defaultProductBrand"))) {

                                            $selectBrand = easySelectA(array(
                                                "table" => "product_brands",
                                                "where" => array(
                                                    "brand_id" => get_options("defaultProductBrand")
                                                )
                                            ))["data"][0];

                                            echo "<option selected value='{$selectBrand['brand_id']}'>{$selectBrand['brand_name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3" for="defaultProductGeneric"><?= __("Default Generic:"); ?></label>
                                <div class="col-sm-6">
                                    <select name="defaultProductGeneric" id="defaultProductGeneric" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productGenericList">
                                        <option value=""><?= __("All Brand"); ?></option>
                                        <?php
                                        if (!empty(get_options("defaultProductGeneric"))) {
                                            echo "<option selected value='" . get_options("defaultProductGeneric") . "'>" . get_options("defaultProductGeneric") . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3" for="defaultProductEdition"><?= __("Default Edition:"); ?></label>
                                <div class="col-sm-6">
                                    <select name="defaultProductEdition" id="defaultProductEdition" class="form-control">
                                        <option value=""><?= __("All Year"); ?></option>
                                        <?php

                                        $selectProductYear = easySelectA(array(
                                            "table"   => "products",
                                            "fields"  => "product_edition",
                                            "where"   => array(
                                                "product_edition is not null and product_edition != ''"
                                            ),
                                            "groupby" => "product_edition"
                                        ))["data"];

                                        foreach ($selectProductYear as $key => $value) {
                                            $selected = get_options("defaultProductEdition") == $value['product_edition'] ? "selected" : "";
                                            echo "<option $selected value='{$value['product_edition']}'>{$value['product_edition']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3" for="defaultProductOrder"><?= __("Default Sorting:"); ?></label>
                                <div class="col-sm-4">
                                    <select name="defaultProductOrder" id="defaultProductOrder" class="form-control">
                                        <?php
                                        $sorting = array(
                                            "totalSoldQnt"  => "Max Sold Quantity",
                                            "product_name"  => "Product Name",
                                            "product_id"  => "Latest"
                                        );

                                        foreach ($sorting as $sKey => $sValue) {
                                            $selected = get_options("defaultProductOrder") == $sKey ? "selected" : "";
                                            echo "<option $selected value='$sKey'>$sValue</option>";
                                        }

                                        ?>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <select name="defaultProductOrderBy" id="defaultProductOrderBy" class="form-control">

                                        <?php
                                        $orderby = array(
                                            "ASC"   => "Ascending",
                                            "DESC"  => "Descending"
                                        );

                                        foreach ($orderby as $oKey => $oValue) {
                                            $selected = get_options("defaultProductOrderBy") == $oKey ? "selected" : "";
                                            echo "<option $selected value='$oKey'>$oValue</option>";
                                        }

                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3" for="maxProductDisplay"><?= __("Max Display Product:"); ?></label>
                                <div class="col-sm-6">
                                    <input type="text" name="maxProductDisplay" id="maxProductDisplay" value="<?php echo get_options("maxProductDisplay"); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3" for="defaultSaleQnt"><?= __("Default Sale Quantity:"); ?></label>
                                <div class="col-sm-6">
                                    <input type="text" name="defaultSaleQnt" id="defaultSaleQnt" value="<?php echo get_options("defaultSaleQnt"); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3" for="posSaleAutoAdjustAmount"><?= __("Auto Adjust Decimal Place:"); ?></label>
                                <div class="col-sm-6">
                                    <select name="posSaleAutoAdjustAmount" id="posSaleAutoAdjustAmount" class="form-control select2">
                                        <option <?php echo get_options("posSaleAutoAdjustAmount") == "1" ? "selected" : ""; ?> value="1">Yes</option>
                                        <option <?php echo get_options("posSaleAutoAdjustAmount") == "0" ? "selected" : ""; ?> value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3" for="posSaleAutoMarkAsPaid"><?= __("Auto Paid:"); ?></label>
                                <div class="col-sm-6">
                                    <select name="posSaleAutoMarkAsPaid" id="posSaleAutoMarkAsPaid" class="form-control select2">
                                        <option <?php echo get_options("posSaleAutoMarkAsPaid") == "1" ? "selected" : ""; ?> value="1">Yes</option>
                                        <option <?php echo get_options("posSaleAutoMarkAsPaid") == "0" ? "selected" : ""; ?> value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3" for="printerType"><?= __("Printer Type:"); ?></label>
                                <div class="col-sm-6">
                                    <select name="printerType" id="printerType" class="form-control select2">
                                        <option <?php echo get_options("printerType") == "normal" ? "selected" : ""; ?> value="normal">Normal Printer</option>
                                        <option <?php echo get_options("printerType") == "pos" ? "selected" : ""; ?> value="pos">POS Printer</option>
                                    </select>
                                </div>
                            </div>


                        </div>
                        <!-- box body-->
                        <div class="box-footer">
                            <button type="submit" id="jqAjaxButton" class="btn btn-primary"><?= __("Save Change"); ?></button>
                        </div>
                    </form>
                </div>
                <!-- box -->
            </div>
            <!-- col-xs-12-->
        </div>
        <!-- row-->

    </section> <!-- Main content End tag -->
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->