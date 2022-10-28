  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Customer Ledger"); ?>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-body">

              <form id="customerLedgerForm" action="">
                <div class="row">
                  <div class="col-md-5 form-group">
                      <label for="customerLedgerDateRange"><?= __("Date range"); ?></label>
                      <input type="text" name="customerLedgerDateRange" id="customerLedgerDateRange" class="form-control dateRangePickerPreDefined" autoComplete="off" required>
                  </div>
                  <div class="col-md-5 form-group">
                    <label for="customerSelection"><?= __("Select Customer"); ?></label>
                    <select name="customerSelection" id="customerSelection" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=customerList" style="width: 100%;" required>
                      <option value=""><?= __("Select Customer"); ?>....</option>
                    </select>
                  </div>
                  <div style="margin-top: 5px;" class="col-md-2">
                      <label for=""></label>
                      <input type="submit" value="<?= __("Submit"); ?>" class="form-control">
                  </div>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-xs-12">

          <div style="display: block;" id="customerLedger" class="box">
            <div class="box-header">
              <h3 class="box-title"></h3>
              <div class="printButtonPosition"></div>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="fixedDateWidthOnPrint table table-striped table-hover" style="width: 100%;">
                <thead>
                  <tr>
                    <th></th>
                    <th class="col-md-2 no-sort"><?= __("Date"); ?></th>
                    <th class="no-sort"><?= __("Description"); ?></th>
                    <th class="countTotal no-sort"><?= __("Debit"); ?></th>
                    <th class="countTotal no-sort"><?= __("Credit"); ?></th>
                    <th class="no-sort text-right"><?= __("Balance"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
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

    /* Column Defference target column of data table */
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=ledgers&page=customerLedger";
    var defaultiDisplayLength = -1;

  </script>