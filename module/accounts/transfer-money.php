  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      <?= __("Transfers Money"); ?>
        <?php 
          if(current_user_can("transfer_money.Add")) {
            echo '<a data-toggle="modal" data-target="#modalDefault" href="'. full_website_address() .'/xhr/?tooltip=true&select2=true&module=accounts&page=newTransfer" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add</a>';
          }
        ?>

      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Transfers</a></li>
        <li class="active">Transfer List</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
          
            <!-- Box header -->
            <div class="box-header">
              <h3 class="box-title"><?= __("Transfer List"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>

            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th style="width: 130px;"><?= __("Date"); ?></th>
                    <th style="width: 180px;"><?= __("From Accounts"); ?></th>
                    <th style="width: 180px;"><?= __("To Accounts"); ?></th>
                    <th class="countTotal text-center"><?= __("Amount"); ?></th>
                    <th><?= __("Description"); ?></th>
                    <th><?= __("Action"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th class="no-print">
                        <input style="width: 130px;" type="text" placeholder="<?= __("Select Date"); ?>" id="transferMoneyDate" class="form-control" autocomplete="off">
                    </th>
                    <th>
                        <select style="width: 180px" id="transferMoneyFromAccounts" class="form-control select2" style="width: 100%">
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
                        <select style="width: 180px" id="transferMoneyToAccounts" class="form-control select2" style="width: 100%">
                            <option value="">All Accounts...</option>
                            <?php
                                foreach($selectAccounts["data"] as $accounts) {
                                    echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                                }
                            ?>
                        </select>
                    </th>
                    <th><?= __("Amount"); ?></th>
                    <th><?= __("Description"); ?></th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=accounts&page=transferList";

    BMS.FUNCTIONS.dateRangePickerPreDefined({selector: "#transferMoneyDate"});

  </script>
