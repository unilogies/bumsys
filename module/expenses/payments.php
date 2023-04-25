
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Payments"); ?>
        <a data-toggle="modal" data-target="#modalDefaultMdm" href="<?php echo full_website_address(); ?>/xhr/?tooltip=true&select2=true&module=expenses&page=billPay" class="btn btn-sm btn-primary"><i class="fa fa-money"></i> <?= __("Bill Pay"); ?></a>
        <a data-toggle="modal" data-target="#modalDefaultXlg" href="<?php echo full_website_address(); ?>/xhr/?tooltip=true&select2=true&module=expenses&page=salaryPay" class="btn btn-sm btn-primary"><i class="fa fa-money"></i> <?= __("Salary Pay"); ?></a>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Payments</a></li>
        <li class="active">Payment List</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
          
            <!-- Box header -->
            <div class="box-header">
              <h3 class="box-title"><?= __("Payments"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>

            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th class="px120"><?= __("Date"); ?></th>
                    <th class="defaultOrder"><?= __("Reference"); ?></th>
                    <th class="no-sort"><?= __("Paid to"); ?></th>
                    <th class="countTotal"><?= __("Amount"); ?></th>
                    <th><?= __("Payment From"); ?></th>
                    <th class="dtDescription"><?= __("Description"); ?></th>
                    <th><?= __("Satus"); ?></th>
                    <th><?= __("Method"); ?></th>
                    <th class="no-sort no-print"><?= __("Action"); ?></th>
                  </tr>
                </thead>

                <tfoot>
                  <tr>
                    <th></th>
                    <!-- Payment Date -->
                    <th class="no-print">
                        <input style="width: 130px;" type="text" placeholder="<?= __("Select Date"); ?>" name="paymentDate" id="paymentDate" class="form-control" autocomplete="off">
                    </th>

                    <!-- Reference -->
                    <th class="no-print"><input style="width: 80px;" type="text" placeholder="<?= __("Reference"); ?>" name="paymentReference" id="paymentReference" class="form-control" autocomplete="off"></th>
                    
                    <!-- Compnay or Employee -->
                    <th class="no-print col-md-1"><input style="width: 160px" type="text" name="paidTo" placeholder="<?= __("Enter name or ID"); ?>" id="paidTo" value="" class="form-control"></th>
                    
                    <th>Amount</th>
                    <th class="no-print">
                        <select id="paymentFromAccountFilter" class="form-control select2" style="width: 100%;" required>
                            <option value="">All...</option>
                            <?php
                                $selectAccounts = easySelect("accounts", "accounts_id, accounts_name", array(), array("is_trash" => 0));
                                foreach($selectAccounts["data"] as $accounts) {
                                    echo "<option value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                                }
                            ?>
                        </select>
                    </th>
                    <th>Description</th>
                    <th>Status</th>
                    <!-- Payment Method -->
                    <th class="no-print col-md-1">
                      <select style="width: 120px" name="paymentMethod" id="paymentMethod" class="form-control select2" style="width: 100%">
                        <option value="">Method...</option>
                        <?php
                            $paymentMethod = array("Cash", "Bank Transfer", "Cheque", "Others");
                            
                            foreach($paymentMethod as $method) {
                                echo "<option value='{$method}'>{$method}</option>";
                            }
                        ?>
                      </select>
                    </th>
                    <th>Action</th>

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

    BMS.FUNCTIONS.dateRangePickerPreDefined({selector: "#paymentDate"});
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=expenses&page=expensesList";

  </script>
