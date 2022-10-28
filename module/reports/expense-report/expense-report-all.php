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
              <h3 class="box-title"><?= __("Expense Reports"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" style="width: 90%;">
                <thead>
                  <tr>
                    <th></th>
                    <th><?= __("Head Name"); ?></th>
                    <th class="no-sort countTotal"><?= __("Total Amount"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th><input type="text" name="expenseReportDateRange" id="expenseReportDateRange" class="form-control" value="<?= date("1970-01-01") . " - " . date("Y-12-31"); ?>" autoComplete="off" required></th>
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
    BMS.FUNCTIONS.dateRangePickerPreDefined({selector: "#expenseReportDateRange"});
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=reports&page=expenseReportsAll";
  </script>
