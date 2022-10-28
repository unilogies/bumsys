<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("Locationwise Sales Report"); ?>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-body">

                        <form id="locationWiseSaleReort" action="">
                            <div class="row">
                                <div class="col-md-5 form-group">
                                    <label for="selectProductForLocationReport"><?= __("Select Product"); ?></label>
                                    <select name="selectProductForLocationReport" id="selectProductForLocationReport" class="form-control pull-left select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=productListAll" style="width: 100%;">
                                        <option value=""><?= __("Select Product"); ?>....</option>
                                    </select>
                                </div>
                                <div class="col-md-5 form-group">
                                    <label for="selectLocation"><?= __("Select District"); ?></label>
                                    <select name="selectLocation" id="selectLocation" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=districtList" style="width: 100%;" required>
                                        <option value=""><?= __("Select Location"); ?>....</option>
                                    </select>
                                </div>
                                <div style="margin-top: 5px;" class="col-md-2">
                                    <label for=""></label>
                                    <input type="submit" value="Submit" class="form-control">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div id="customerReport" class="box">
                    <div class="box-header">
                        <h3 class="box-title"></h3>
                        <div class="printButtonPosition"></div>
                    </div>
                    <!-- Box header -->
                    <div class="box-body">
                        <table id="dataTableWithAjaxExtend" class="fixedDateWidthOnPrint table table-striped table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="no-sort"><?= __("Customer Name"); ?></th>
                                    <th class="no-sort"><?= __("Location"); ?></th>
                                    <th class="countTotal no-sort"><?= __("Quantity"); ?></th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                    <!-- box body-->
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

<script>
    /* Column Defference target column of data table */
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=reports&page=locationWiseSalesReport";
    var defaultiDisplayLength = -1;
</script>