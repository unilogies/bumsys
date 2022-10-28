
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">

      <?php 

        if(isset($_GET["action"]) and $_GET["action"] == "addStockTransfers") {
          // Show a success msg
          echo _e("Transfer has been successfully completed.");
        }

      ?>
      
  </section>

  
    <section class="content-header">
      <h1>
        <?= __("Stock Transfer List"); ?>
        <a href="<?php echo full_website_address(); ?>/stock-management/new-stock-transfer/" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> <?= __("Add New"); ?></a>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> <?= __("Purchase"); ?></a></li>
        <li class="active"><?= __("Purchase List"); ?></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
    
      <div class="row">
        <div class="col-xs-12">
          <div class="box">

          <div class="box-header">
              <h3 class="box-title"><?= __("Purchase List"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-bordered table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th><?= __("Date"); ?></th>
                    <th><?= __("Referense No"); ?></th>
                    <th><?= __("From Warehouse"); ?></th>
                    <th><?= __("To Warehouse"); ?></th>
                    <th class="countTotal text-center"><?= __("Grand Total"); ?></th>
                    <th><?= __("Description"); ?></th>
                    <th style="width: 130px;"><?= __("Status"); ?></th>
                    <th class="text-center no-sort no-print" width="100px"><?= __("Action"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th><?= __("Date"); ?></th>
                    <th><?= __("Referense No"); ?></th>
                    <th><?= __("From Warehouse"); ?></th>
                    <th><?= __("To Warehouse"); ?></th>
                    <th><?= __("Grand Total"); ?></th>
                    <th><?= __("Description"); ?></th>
                    <th><?= __("Status"); ?></th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=stock-management&page=stockTransferList";
  </script>
