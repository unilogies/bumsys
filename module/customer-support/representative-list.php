  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Customer Support"); ?>
        <?php 
          if(current_user_can("customer_support_representative.Add")) {
            echo '<a data-toggle="modal" data-target="#modalDefault" href="'. full_website_address() .'/xhr/?module=customer-support&page=newRepresentative" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add</a>';
          }
        ?>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?= __("Representative List"); ?></h3>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-bordered table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th><?php echo __("Name"); ?></th>
                    <th><?php echo __("SIP User"); ?></th>
                    <th><?php echo __("SIP Domian"); ?></th>
                    <th><?php echo __("Socket URL"); ?></th>
                    <th><?php echo __("Action"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th><?php echo __("Name"); ?></th>
                    <th><?php echo __("SIP User"); ?></th>
                    <th><?php echo __("SIP Domian"); ?></th>
                    <th><?php echo __("Socket URL"); ?></th>
                    <th><?php echo __("Action"); ?></th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=customer-support&page=callCenterRepresentativeList";
  </script>
