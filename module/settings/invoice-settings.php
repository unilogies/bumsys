<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("Invoice Settings"); ?>
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
                                <label class="col-sm-3" for="invoiceType"><?= __("Invoice Type:"); ?></label>
                                <div class="col-sm-6">
                                    <select name="invoiceType" id="invoiceType" class="form-control select2">
                                        <option <?php echo get_options("invoiceType") == "normal" ? "selected" : ""; ?> value="normal">Normal View</option>
                                        <option <?php echo get_options("invoiceType") == "details" ? "selected" : ""; ?> value="details">Details View</option>
                                        <option <?php echo get_options("invoiceType") == "pos" ? "selected" : ""; ?> value="pos">For POS Printer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3" for="invoiceHeader"><?= __("Invoice Header"); ?></label>
                                <div class="col-sm-6">
                                    <textarea name="invoiceHeader" id="invoiceHeader" cols="30" rows="3" placeholder="Write invoice header text here" class="form-control"><?php echo get_options("invoiceHeader"); ?></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3" for="invoiceFooter"><?= __("Invoice Footer"); ?></label>
                                <div class="col-sm-6">
                                    <textarea name="invoiceFooter" id="invoiceFooter" cols="30" rows="3" placeholder="Write invoice footer text here" class="form-control"><?php echo get_options("invoiceFooter"); ?></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3" for="invoiceWidth"><?= __("Invoice Width (px)"); ?></label>
                                <div class="col-sm-6">
                                    <input type="text" name="invoiceWidth" id="invoiceWidth" class="form-control" placeholder="Invoice with in px. Eg 480." value="<?php echo get_options("invoiceWidth"); ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3" for="invoiceShowProductDiscount"><?= __("Show product discount"); ?></label>
                                <div class="col-sm-6">
                                    <select name="invoiceShowProductDiscount" id="invoiceShowProductDiscount" class="select2 form-control">
                                        <option <?= get_options("invoiceShowProductDiscount") !== "1" ?: "selected"; ?> value="1">Yes</option>
                                        <option <?= get_options("invoiceShowProductDiscount") !== "0" ?: "selected"; ?> value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3" for="invoiceShowShopLogo"><?= __("Show Shop Logo"); ?></label>
                                <div class="col-sm-6">
                                    <select name="invoiceShowShopLogo" id="invoiceShowShopLogo" class="select2 form-control">
                                        <option <?= get_options("invoiceShowShopLogo") !== "1" ?: "selected"; ?> value="1">Yes</option>
                                        <option <?= get_options("invoiceShowShopLogo") !== "0" ?: "selected"; ?> value="0">No</option>
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