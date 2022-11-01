<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("Raw Materials Attachment"); ?>
            <small><?= __("Product List"); ?></small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><?= __("Product List"); ?></h3>
                        <div class="printButtonPosition"></div>
                    </div>
                    <!-- Box header -->
                    <div class="box-body">
                        <table id="dataTableWithAjaxExtend" class="table table-bordered table-striped table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th><?= __("Product Code"); ?></th>
                                    <th><?= __("Product Name"); ?></th>
                                    <th><?= __("Group"); ?></th>
                                    <th><?= __("Generic"); ?></th>
                                    <th><?= __("Edition"); ?></th>
                                    <th><?= __("Product Category"); ?></th>
                                    <th style="width: 140px;"><?= __("Dimension"); ?></th>
                                    <th style="width: 140px;"><?= __("Price"); ?></th>
                                    <th class="no-sort no-print" width="100px"><?= __("Action"); ?></th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th><?= __("Product Code"); ?></th>
                                    <th><?= __("Product Name"); ?></th>
                                    <th><?= __("Group"); ?></th>
                                    <th><?= __("Generic"); ?></th>
                                    <th>
                                        <select name="productEdition" id="productEdition" class="form-control select2">
                                            <option value=""><?= __("All Editions..."); ?></option>
                                            <?php

                                            $selectProductYear = easySelectA(array(
                                                "table"   => "products",
                                                "fields"  => "product_edition",
                                                "groupby" => "product_edition"
                                            ));

                                            if ($selectProductYear) {
                                                foreach ($selectProductYear["data"] as $key => $value) {
                                                    echo "<option value='{$value['product_edition']}'>{$value['product_edition']}</option>";
                                                }
                                            }

                                            ?>
                                        </select>
                                    </th>
                                    <th>
                                        <select style="width: 180px;" name="productCategory" id="productCategory" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productCategoryList">
                                            <option value=""><?= __("Category"); ?>...</option>
                                        </select>
                                    </th>
                                    <th><?= __("Dimension"); ?></th>
                                    <th><?= __("Price"); ?></th>
                                    <th><?= __("Action"); ?></th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=production&page=rmaProductList";
</script>