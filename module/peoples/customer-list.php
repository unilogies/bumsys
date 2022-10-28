  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Customers"); ?>
        <small><?= __("Customer List"); ?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Customers</a></li>
        <li class="active">Customer List</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?= __("Customers List"); ?></h3>
              <div class="printButtonPosition">
               <div style="float:right;" class="btn-group">
                    <button type="button" class="btn btn-sm btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                        <?= __("action"); ?>
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                      <li>
                        <button  class="btn btn-sm btn-block btn-default" onclick="sendBulkSMS( '<?= full_website_address() .'/info/?icheck=false&module=sms&page=sendBulkSMS&type=greetings&numbers='; ?>'  );"> <i class="fa fa-paper-plane"></i> <?= __("Send SMS"); ?> </button>
                      </li>
                    </ul> 
                </div>
              </div>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-bordered table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th><?= __("Customer Name"); ?></th>
                    <th><?= __("District"); ?></th>
                    <th><?= __("Division"); ?></th>
                    <th><?= __("Address"); ?></th>
                    <th><?= __("Contact"); ?></th>
                    <th class="no-print no-sort" width="100px"><?= __("Action"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th><?= __("Customer Name"); ?></th>
                    <th><?= __("District"); ?></th>
                    <th><?= __("Division"); ?></th>
                    <th><?= __("Address"); ?></th>
                    <th><?= __("Contact"); ?></th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=peoples&page=customerList";
  </script>
