  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      <?= __("Advance Bill Payments Return"); ?>
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
              <h3 class="box-title"><?= __("Advance Bills Payment Return List"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>

            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th><?= __("Date"); ?></th>
                    <th><?= __("Employee"); ?></th>
                    <th><?= __("Accounts"); ?></th>
                    <th class="countTotal"><?= __("Amount"); ?></th>
                    <th><?= __("Description"); ?></th>
                    <th class="no-sort"><?= __("Action"); ?></th>
                  </tr>
                </thead>

                <tfoot>
                  <tr>
                    <th></th>
                    <th><?= __("Date"); ?></th>
                    <th><?= __("Employee"); ?></th>
                    <th><?= __("Accounts"); ?></th>
                    <th></th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=expenses&page=advancePaymentReturnList";
  </script>
