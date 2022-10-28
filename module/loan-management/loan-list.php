  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Loan Management"); ?>
        <a data-toggle="modal" data-target="#modalDefault" href="<?php echo full_website_address(); ?>/xhr/?select2=true&tooltip=true&module=loan-management&page=payLoan" class="btn btn-sm btn-primary"><i class="fa fa-money"></i> <?= __("Pay Loan"); ?></a>
      </h1>

    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
          
            <!-- Box header -->
            <div class="box-header">
              <h3 class="box-title"><?= __("Loan"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th><?= __("Date"); ?></th>
                    <th><?= __("Loan Borrower"); ?></th>
                    <th><?= __("Paying From"); ?></th>
                    <th class="countTotal"><?= __("Loan Amount"); ?></th>
                    <th class="countTotal"><?= __("Installment Amount"); ?></th>
                    <th class="countTotal"><?= __("Total Paid"); ?></th>
                    <th class="countTotal"><?= __("Due"); ?></th>
                    <th class="no-sort no-print"><?= __("Action"); ?></th>
                  </tr>
                </thead>

                <tfoot>
                  <tr>
                    <th></th>
                    <th class="col-md-2"></th>
                    <th class="col-md-2"></th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=loan-management&page=loanList";
  </script>
