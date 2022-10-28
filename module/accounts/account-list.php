  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Accounts"); ?>
        <small><?= __("Account List"); ?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Accounts</a></li>
        <li class="active">Account List</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?= __("Account List"); ?></h3>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-bordered table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th><?= __("Account Name"); ?></th>
                    <th><?= __("Type"); ?></th>
                    <th class="countTotal"><?= __("Balance"); ?></th>
                    <th><?= __("Bank Name"); ?></th>
                    <th><?= __("Bank Acc. No."); ?></th>
                    <th><?= __("Details"); ?></th>
                    <th class="no-sort" width="100px"><?= __("Action"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th><?= __("Account Name"); ?></th>
                    <th><?= __("Type"); ?></th>
                    <th><?= __("Balance"); ?></th>
                    <th><?= __("Bank Name"); ?></th>
                    <th><?= __("Bank Acc. No."); ?></th>
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
    /*Column Defference target column of data table */
    var dataTableSumColumn = [2];
    var scrollY = "";
    var targets = [6];
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=accounts&page=accountList";
  </script>
