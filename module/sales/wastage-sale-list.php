  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">

      <?php 

        if(isset($_GET["action"]) and $_GET["action"] == "addWastageSale") {
          // Show a success msg
          echo _s("Successfully completed.");
        }

      ?>
      
  </section>

  
    <section class="content-header">
      <h1>
        <?= __("Wastage Sale"); ?>
        <a href="<?php echo full_website_address(); ?>/sales/new-wastage-sale/" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> <?= __("Add New"); ?></a>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
    
      <div class="row">
        <div class="col-xs-12">
          <div class="box">

          <div class="box-header">
              <h3 class="box-title"><?= __("Wastage Sale List"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-bordered table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th><?= __("Date"); ?></th>
                    <th><?= __("Referense"); ?></th>
                    <th><?= __("Customer"); ?></th>
                    <th class="countTotal text-right"><?= __("Grand Total"); ?></th>
                    <th class="countTotal text-right"><?= __("Paid Amount"); ?></th>
                    <th class="countTotal text-right"><?= __("Due"); ?></th>
                    <th><?= __("Details"); ?></th>
                    <th class="text-center no-sort no-print" width="100px"><?= __("Action"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th><?= __("Data"); ?></th>
                    <th><?= __("Referense"); ?></th>
                    <th><?= __("Customer"); ?></th>
                    <th><?= __("Grand Total"); ?></th>
                    <th></th>
                    <th></th>
                    <th><?= __("Details"); ?></th>
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
    
    /* Column Defference target column of data table */
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=sales&page=wastageSaleList";

  </script>
