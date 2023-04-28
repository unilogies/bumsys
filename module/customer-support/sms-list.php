  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Customer Support"); ?>
        <small><?= __("SMS List"); ?></small>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?= __("SMS List"); ?></h3>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-bordered table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th class="defaultOrder"><?php echo __("Date"); ?></th>
                    <th><?php echo __("To"); ?></th>
                    <th><?php echo __("Text"); ?></th>
                    <th><?php echo __("Status"); ?></th>
                    <th><?php echo __("Send by"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th><?php echo __("Date"); ?></th>
                    <th><?php echo __("To"); ?></th>
                    <th><?php echo __("Text"); ?></th>
                    <th><?php echo __("Status"); ?></th>
                    <th><?php echo __("Send by"); ?></th>
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
    /*Column Defference target column of data table */
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=customer-support&page=smsList";
  </script>
