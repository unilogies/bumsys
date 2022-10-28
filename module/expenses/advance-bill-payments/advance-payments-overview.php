  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Advance Bill Payments"); ?>
        <a data-toggle="modal" data-target="#modalDefault" href="<?php echo full_website_address(); ?>/xhr/?tooltip=true&select2=true&module=expenses&page=payAdvancePayment" class="btn btn-sm btn-primary"><i class="fa fa-money"></i> <?= __("Pay"); ?></a>
        <a data-toggle="modal" data-target="#modalDefaultMdm" href="<?php echo full_website_address(); ?>/xhr/?tooltip=true&select2=true&module=expenses&page=adjustAdvancePayment" class="btn btn-sm btn-primary"><i class="fa fa-adjust"></i> <?= __("Adjust"); ?></a>
        <a data-toggle="modal" data-target="#modalDefault" href="<?php echo full_website_address(); ?>/xhr/?tooltip=true&select2=true&module=expenses&page=returnAdvancePayment" class="btn btn-sm btn-primary"><i class="fa fa-undo"></i> <?= __("Return"); ?></a>
      </h1>

    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
          
            <!-- Box header -->
            <div class="box-header">
              <h3 class="box-title"><?= __("Advance Bills Overview"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>

            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th><?= __("Employee"); ?></th>
                    <th class="no-sort countTotal"><?= __("Paid Amount"); ?></th>
                    <th class="no-sort countTotal"><?= __("Adjusted Amount"); ?></th>
                    <th class="no-sort countTotal"><?= __("Due Amount"); ?></th>
                  </tr>
                </thead>

                <tfoot>
                  <tr>
                    <th></th>
                    <th>
                        <input style="width: 130px;" type="text" placeholder="<?= __("Select Date"); ?>" id="advancePaymentOverviewFilterDate" class="form-control" autocomplete="off">
                    </th>
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
    BMS.FUNCTIONS.dateRangePickerPreDefined({selector: "#advancePaymentOverviewFilterDate"});
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=expenses&page=advancePaymentOverview";
  </script>
