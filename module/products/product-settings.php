<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("Product Settings"); ?>
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
                                <label class="col-sm-3" for="productSettingsCanAddGeneric"><?= __("Can Add Product Generic:"); ?></label>
                                <div class="col-sm-6">
                                    <select name="productSettingsCanAddGeneric" id="productSettingsCanAddGeneric" class="form-control select2">
                                        <option <?php echo get_options("productSettingsCanAddGeneric") == "1" ? "selected" : ""; ?> value="1">Yes</option>
                                        <option <?php echo get_options("productSettingsCanAddGeneric") == "0" ? "selected" : ""; ?> value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3" for="productSettingsCanAddBrands"><?= __("Can Add Product Brand:"); ?></label>
                                <div class="col-sm-6">
                                    <select name="productSettingsCanAddBrands" id="productSettingsCanAddBrands" class="form-control select2">
                                        <option <?php echo get_options("productSettingsCanAddBrands") == "1" ? "selected" : ""; ?> value="1">Yes</option>
                                        <option <?php echo get_options("productSettingsCanAddBrands") == "0" ? "selected" : ""; ?> value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3" for="productSettingsCanAddBookInfo"><?= __("Can Add Book Info:"); ?></label>
                                <div class="col-sm-6">
                                    <select name="productSettingsCanAddBookInfo" id="productSettingsCanAddBookInfo" class="form-control select2">
                                        <option <?php echo get_options("productSettingsCanAddBookInfo") == "1" ? "selected" : ""; ?> value="1">Yes</option>
                                        <option <?php echo get_options("productSettingsCanAddBookInfo") == "0" ? "selected" : ""; ?> value="0">No</option>
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