<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("Product Stock Ledger"); ?>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-body">

                        <form id="productLedgerForm" action="">
                            <div class="row">

                                <div class="col-md-7 form-group">
                                    <label for="productSelection"><?= __("Select Product"); ?></label>
                                    <select name="productSelection" id="productSelection" class="form-control pull-left select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=productListAll&wid=<?php echo $_SESSION["wid"]; ?>" style="width: 100%;">
                                        <option value=""><?= __("Select Product"); ?>....</option>
                                    </select>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label for="warehouseSelection"><?= __("Select Warehouse"); ?></label>
                                    <select name="warehouseSelection" id="warehouseSelection" class="form-control select2" style="width: 100%;">
                                        <option value=""><?= __("All Warehouse...."); ?></option>
                                        <?php
                                        $selectWarehouse = easySelectA(array(
                                            "table"     => "warehouses",
                                            "fields"    => "warehouse_id, warehouse_name",
                                            "where"     => array(
                                                "is_trash=0"
                                            )
                                        ));

                                        if ($selectWarehouse) {

                                            foreach ($selectWarehouse["data"] as $warehouse) {

                                                $selected = $_SESSION["wid"] == $warehouse['warehouse_id'] ? "selected" : "";
                                                echo "<option {$selected} value='{$warehouse['warehouse_id']}'>{$warehouse['warehouse_name']}</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div style="margin-top: 5px;" class="col-md-2">
                                    <label for=""></label>
                                    <input type="submit" value="<?= __("Submit"); ?>" class="form-control">
                                </div>
                            </div>
                        </form>



                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div style="display: block;" id="accountsLedger" class="box">
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
                                    <th class="px120 no-sort"><?= __("Date"); ?></th>
                                    <th class="px120 no-sort"><?= __("Reference"); ?></th>
                                    <th class="no-sort"><?= __("Description"); ?></th>
                                    <th class="countTotal no-sort"><?= __("Stock In"); ?></th>
                                    <th class="countTotal no-sort"><?= __("Stock Out"); ?></th>
                                    <th class="no-sort text-right"><?= __("Stock Qty"); ?></th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=reports&page=productLedger";
    var defaultiDisplayLength = -1;
</script>