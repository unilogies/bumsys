  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?php echo __("Reports"); ?>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo  __("Expired Product List"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>
            <!-- Box header -->
            <div class="box-body">
                <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" style="width: 100%;">
                    <thead>
                        <tr>
                            <th></th>
                            <th><?php echo __("Product Name"); ?></th>
                            <th><?php echo __("Warehouse"); ?></th>
                            <th><?php echo __("Expired Qty"); ?></th>
                            <th><?php echo __("Batch Code"); ?></th>
                            <th><?php echo __("Expiry Date"); ?></th>
                        </tr>
                    </thead>
    
                    <tfoot>
                        <tr>
                            <th></th>
                            <th><?php echo __("Product Name"); ?></th>
                            <th><?php echo __("Warehouse"); ?></th>
                            <th><?php echo __("Expired Qty"); ?></th>
                            <th><?php echo __("Batch Code"); ?></th>
                            <th><?php echo __("Expiry Date"); ?></th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=reports&page=expiredProductList";
  </script>