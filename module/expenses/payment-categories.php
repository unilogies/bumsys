  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Payment Categories"); ?>
        <?php if(current_user_can("payment_categories.Add")) {
          echo '<a data-toggle="modal" data-target="#modalDefault" href="'. full_website_address() .'/xhr/?tooltip=true&module=expenses&page=newPaymentCategory" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> '. __("Add") . '</a>';
        } ?>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Payment Categories</a></li>
        <li class="active">Category List</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">

            <div class="box-body">
              <table id="dataTableWithAjax" class="table table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th><?= __("Category Name"); ?></th>
                    <th><?= __("Shop Name"); ?></th>
                    <th class="no-sort no-print" width="100px"><?= __("Action"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th><?= __("Category Name"); ?></th>
                    <th><?= __("Shop Name"); ?></th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=expenses&page=paymentCategoryList";
  </script>
