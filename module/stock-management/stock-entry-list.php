  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Stock Entry List"); ?>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
          
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-bordered table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th class="defaultOrder"><?php echo __("Date"); ?></th>
                    <th><?php echo __("Stock Type"); ?></th>
                    <th><?php echo __("Warehouse"); ?></th>
                    <th><?php echo __("Description"); ?></th>
                    <th class="no-sort" width="100px"><?php echo __("Action"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th><?php echo __("Date"); ?></th>
                    <th><?php echo __("Stock Type"); ?></th>
                    <th><?php echo __("Warehouse"); ?></th>
                    <th><?php echo __("Description"); ?></th>
                    <th><?php echo __("Action"); ?></th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=stock-management&page=stockEntryList";
  </script>