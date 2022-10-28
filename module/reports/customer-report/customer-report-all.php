
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Reports"); ?>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?= __("Customer Reports"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" style="width: 100%;">
                <thead>
                  <tr>
                    <th></th>
                    <th style="display: block;"><?= __("Customer Name"); ?></th>
                    <th class="no-sort countTotal"><?= __("Previous Balance"); ?></th>
                    <th class="no-sort countTotal"><?= __("Purchased"); ?></th>
                    <th class="no-sort countTotal"><?= __("Shipping"); ?></th>
                    <th class="no-sort countTotal"><?= __("Paid"); ?></th>
                    <th class="no-sort countTotal"><?= __("Bonus"); ?></th>
                    <th class="no-sort countTotal"><?= __("Return"); ?></th>
                    <th class="no-sort countTotal"><?= __("Discount"); ?></th>
                    <th class="no-sort countTotal"><?= __("Balance/ Due"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th><input type="text" name="customerReportDateRange" id="customerReportDateRange" class="form-control" value="<?= date("1970-01-01") . " - " . date("Y-12-31"); ?>" autoComplete="off" required></th>
                    <th></th>
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
    BMS.FUNCTIONS.dateRangePickerPreDefined({selector: "#customerReportDateRange"});
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=reports&page=customerReports";
  </script>
