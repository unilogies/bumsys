  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Journal Records"); ?>
        <?php if(current_user_can("journal_records.Add")) {
          echo '<a data-toggle="modal" data-target="#modalDefault" href="'. full_website_address() .'/xhr/?tooltip=true&select2=true&module=journals&page=newJournalRecords" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> ' . __("New Entry") . '</a>';
        } ?>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
          
            <!-- Box header -->
            <div class="box-header">
              <h3 class="box-title"></h3>
              <div class="printButtonPosition"></div>
            </div>

            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th><?= __("Date"); ?></th>
                    <th><?= __("Reference"); ?></th>
                    <th><?= __("Journal Name"); ?></th>
                    <th><?= __("Accounts"); ?></th>
                    <th><?= __("Payment type"); ?></th>
                    <th class="countTotal"><?= __("Amount"); ?></th>
                    <th><?= __("Narration"); ?></th>
                    <th><?= __("Action"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th class="no-print">
                        <input style="width: 130px;" type="text" placeholder="<?= __("Select Date"); ?>" id="journalRecordEntryDate" class="form-control" autocomplete="off">
                    </th>
                    <th><?= __("Reference"); ?></th>
                    <th>
                        <select id="journalRecordJournalId" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=journalList" style="width: 100%;" required>
                            <option value=""><?= __("Select Journal"); ?>....</option>
                        </select>
                    </th>
                    <th>
                        <select style="width: 180px" id="journalRecordPaymentAccounts" class="form-control select2" style="width: 100%">
                            <option value="">All Accounts...</option>
                            <?php
                                $selectAccounts = easySelect("accounts", "accounts_id, accounts_name", array(), array("is_trash" => 0));
                                foreach($selectAccounts["data"] as $accounts) {
                                    echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                                }
                            ?>
                        </select>
                    </th>
                    <th>
                        <select style="width: 180px" id="journalRecordPaymentType" class="form-control select2" style="width: 100%">
                            <option value="">All Type</option>
                            <option value="Incoming">Incoming</option>
                            <option value="Outgoing">Outgoing</option>
                        </select>
                    </th>
                    <th><?= __("Amount"); ?></th>
                    <th><?= __("Narration"); ?></th>
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
    
    var scrollY = "";
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=journals&page=journalRecordList";

    BMS.FUNCTIONS.dateRangePickerPreDefined({selector: "#journalRecordEntryDate"});

  </script>
