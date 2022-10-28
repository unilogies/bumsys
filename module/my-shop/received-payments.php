  <!-- Content Wrapper. Contains page content -->  
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Received Payments"); ?>
        <a data-toggle="modal" data-target="#modalDefault" href="<?php echo full_website_address(); ?>/xhr/?tooltip=true&select2=true&module=my-shop&page=shopAddReceivedPayments" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> <?= __("Add Payments"); ?></a>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Received Payments</a></li>
        <li class="active">Received Payments List</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
          
            <!-- Box header -->
            <div class="box-header">
              <h3 class="box-title"><?= __("Received Payments"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>

            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th class="col-md-2"><?= __("Date"); ?></th>
                    <th><?= __("Receipt"); ?></th>
                    <th><?= __("Customer"); ?></th>
                    <th class="countTotal"><?= __("Amount"); ?></th>
                    <th><?= __("Details"); ?></th>
                    <th class="no-sort"><?= __("Action"); ?></th>
                  </tr>
                </thead>

                <tfoot>
                  <tr>
                    <th></th>
                    <th><?= __("Date"); ?></th>
                    <th><?= __("Receipt"); ?></th>
                    <th><?= __("Customer"); ?></th>
                    <th><?= __("Amount"); ?></th>
                    <th><?= __("Details"); ?></th>
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

    /* Column Defference target column of data table */
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=my-shop&page=shopReceivedPaymentsList";
  </script>
