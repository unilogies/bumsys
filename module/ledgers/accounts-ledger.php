
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Accounts Ledger"); ?>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-body">

              <form id="accountsLedgerForm" action="">
                <div class="row">
                  <div class="col-md-5 form-group">
                      <label for="accountsLedgerDateRange"><?= __("Date range"); ?></label>
                      <input type="text" name="accountsLedgerDateRange" id="accountsLedgerDateRange" class="form-control dateRangePickerPreDefined" autoComplete="off" required>
                  </div>
                  <div class="col-md-5 form-group">
                    <label for="accountSelection"><?= __("Select Accounts"); ?></label>
                    <select name="accountSelection" id="accountSelection" class="form-control select2" style="width: 100%;" required>
                        <option value=""><?= __("Select accounts...."); ?></option>
                        <?php
                            $selectAccounts = easySelectA(array(
                              "table"   => "accounts",
                              "fields"  => "accounts_id, accounts_name",
                              "where"   => array(
                                "is_trash = 0"
                              )
                            ));
                            if($selectAccounts)
                            foreach($selectAccounts["data"] as $accounts) {
                                echo "<option value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                            }
                        ?>
                    </select>
                  </div>
                  <div style="margin-top: 5px;" class="col-md-2">
                      <label for=""></label>
                      <input type="submit" value="<?= __("Submit"); ?>" class="form-control">
                  </div>
                </div>
              </form>

              <div class="form-group">
                <div id="DtExportTopMessage">

                  <h2 style="font-weight: bold;" class="text-center"><?= get_options("companyName"); ?></h2>
                  <p class="text-center"><?= get_options("companyAddress");?></p>
                  <h3 class="text-center"><?= __("Accounts Ledger"); ?></h3>
                  <p class="text-center"><strong><?= __("Time:"); ?> <?php echo date("Y-m-d H:i:s");  ?> </strong> </p>
                  <br/>
                  <p><strong><?= __("Accounts Name:"); ?> </strong> <span id="accountsName"></span> </p>
                  <p><strong><?= __("Balance:"); ?> </strong> <span id="accountsBalance"></span> </p>
                  <p><strong><?= __("Date Range:"); ?> </strong> <span id="accountsLedgerDates"></span> </p>

                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-xs-12">

          <div style="display: block;" id="accountsLedger" class="box">
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
                    <th class="px120 no-sort"><?= __("Date"); ?></th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=ledgers&page=accountsLedger";
    var defaultiDisplayLength = -1;

  </script>