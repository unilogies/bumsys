  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Requrements"); ?>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      
    <div id="test" class="row">
            
            <div class="col-md-6">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><?= __("Product Requirements"); ?></h3>
                    </div>
                    <!-- Box header -->
                    <div class="box-body">
                        <table dt-height="30vh" dt-data-url="<?php echo full_website_address(); ?>/xhr/?module=production&page=productRequirments" class="dataTableWithAjaxExtend addToSMSBox table table-bordered table-striped table-hover" width="100%">
                            <thead>
                            <tr>
                                <th></th>
                                <th><?php echo __("Product Name"); ?></th>
                                <th><?php echo __("Ordered"); ?></th>
                                <th><?php echo __("Stock"); ?></th>
                                <th><?php echo __("Require"); ?></th>
                            </tr>
                            </thead>
            
                            <tfoot>
                            <tr>
                                <th></th>
                                <th><?php echo __("Product Name"); ?></th>
                                <th><?php echo __("Ordered"); ?></th>
                                <th><?php echo __("Stock"); ?></th>
                                <th><?php echo __("Require"); ?></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- box body-->
                </div>
                <!-- box -->

            </div>
            <!-- col-md-6-->

            <div class="col-md-6">
                
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><?= __("Raw Material Requirements"); ?></h3>
                    </div>
                    <!-- Box header -->
                    <div class="box-body">

                        <div class="box-body">
                            <table dt-height="30vh" dt-data-url="<?php echo full_website_address(); ?>/xhr/?module=production&page=rawMaterialRequirments" class="dataTableWithAjaxExtend addToSMSBox table table-bordered table-striped table-hover" width="100%">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th style="width: 150px;"><?= __("Material Name"); ?></th>
                                    <th style="width: 100px;"><?= __("Require"); ?></th>
                                    <th style="width: 120px;"><?= __("Stock"); ?></th>
                                    <th style="width: 300px;"><?= __("Need to Buy"); ?></th>
                                </tr>
                                </thead>
                
                                <tfoot>
                                <tr>
                                    <th></th>
                                    <th style="width: 150px;"><?= __("Material Name"); ?></th>
                                    <th style="width: 100px;"><?= __("Require"); ?></th>
                                    <th style="width: 120px;"><?= __("Stock"); ?></th>
                                    <th style="width: 300px;"><?= __("Need to Buy"); ?></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                    </div>
                    <!-- box body-->
                </div>
                <!-- box -->

            </div>
            <!-- col-md-6-->
            
        </div>
        <!-- row-->

    </section> <!-- Main content End tag -->
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <script>
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=stock-management&page=stockEntryList";
  </script>