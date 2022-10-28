<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("Others Settings"); ?>
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
                                <label class="col-sm-3" for="autoConfirmStockTransfer"><?= __("Confirm stock transfer automatically?"); ?></label>
                                <div class="col-sm-6">
                                    <select name="autoConfirmStockTransfer" id="autoConfirmStockTransfer" class="select2 form-control">
                                        <option <?= get_options("autoConfirmStockTransfer") !== "Yes" ?: "selected"; ?> value="Yes">Yes</option>
                                        <option <?= get_options("autoConfirmStockTransfer") !== "No" ?: "selected"; ?> value="No">No</option>
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