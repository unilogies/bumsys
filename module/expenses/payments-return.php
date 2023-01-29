
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Payments Return"); ?>
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
              <h3 class="box-title"><?= __("Payments Return"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>

            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th class="px120"><?= __("Date"); ?></th>
                    <th>Direction</th>
                    <th>Paid From/ To</th>
                    <th><?= __("Accounts"); ?></th>
                    <th class="countTotal"><?= __("Amount"); ?></th>
                    <th><?= __("Description"); ?></th>
                    <th class="no-sort no-print"><?= __("Action"); ?></th>
                  </tr>
                </thead>

                <tfoot>
                  <tr>
                    
                  <th></th>
                    <th class="no-print">
                        <input style="width: 130px;" type="text" placeholder="<?= __("Select Date"); ?>" id="paymentReturnDate" class="form-control" autocomplete="off">
                    </th>
                    <th>
                        <select id="paymentReturnDirection" class="form-control select2" style="width: 100%">
                            <option value="">All</option>
                            <option value="Incoming">Incoming</option>
                            <option value="Outgoing">Outgoing</option>
                        </select>
                    </th>
                    <th class="no-print col-md-1">
                        <input style="width: 160px" type="text" placeholder="<?= __("Enter name or ID"); ?>" id="paidToFrom" value="" class="form-control">
                    </th>
                    <th><?= __("Accounts"); ?></th>
                    <th class="countTotal"><?= __("Amount"); ?></th>
                    <th><?= __("Description"); ?></th>
                    <th class="no-sort no-print"><?= __("Action"); ?></th>
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

    BMS.FUNCTIONS.dateRangePickerPreDefined({selector: "#paymentReturnDate"});
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=expenses&page=paymentsReturnList";

  </script>
