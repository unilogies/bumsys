  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Peoples"); ?>
        <small><?= __("Biller List"); ?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Billers</a></li>
        <li class="active">Biller List</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?= __("Biller List"); ?></h3>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjax" class="table table-bordered table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th class="no-sort" width="60px"><?= __("Photo"); ?></th>
                    <th><?= __("Biller Name"); ?></th>
                    <th><?= __("Biller Contact"); ?></th>
                    <th><?= __("Biller Shop"); ?></th>
                    <th class="no-sort"><?= __("Biller Accounts"); ?></th>
                    <th class="no-sort"><?= __("Biller Warehouse"); ?></th>
                    <th class="no-print no-sort" width="100px"><?= __("Action"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th><?= __("Photo"); ?></th>
                    <th><?= __("Biller Name"); ?></th>
                    <th><?= __("Biller Contact"); ?></th>
                    <th><?= __("Biller Shop"); ?></th>
                    <th><?= __("Biller Accounts"); ?></th>
                    <th><?= __("Biller Warehouse"); ?></th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=peoples&page=billerList";
  </script>
