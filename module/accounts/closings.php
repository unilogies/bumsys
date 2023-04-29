<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("Closings"); ?>
            <a data-toggle="modal" data-target="#modalDefault" href="<?php echo full_website_address(); ?>/xhr/?module=accounts&page=newClosings" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> <?= __("Add"); ?></a>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">

                    <!-- Box header -->
                    <div class="box-header">
                        <h3 class="box-title"><?= __("Closing List"); ?></h3>
                        <div class="printButtonPosition"></div>
                    </div>

                    <div class="box-body">
                        <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th><?= __("Customer"); ?></th>
                                    <th><?= __("Title"); ?></th>
                                    <th><?= __("Closing Date"); ?></th>
                                    <th><?= __("Actions"); ?></th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th><?= __("Customer"); ?></th>
                                    <th><?= __("Title"); ?></th>
                                    <th><?= __("Closing Date"); ?></th>
                                    <th><?= __("Actions"); ?></th>
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
    var scrollY = "";
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=accounts&page=closingList";
</script>