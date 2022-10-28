  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Shops"); ?>
        <a data-toggle="modal" data-target="#modalDefault" href="<?php echo full_website_address(); ?>/xhr/?tooltip=true&module=settings&page=newShop" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> <?= __("Add New"); ?></a>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Shop</a></li>
        <li class="active">Shop List</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?= __("Shop List"); ?></h3>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjax" class="table table-bordered table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th><?= __("Shop Name"); ?></th>
                    <th><?= __("Shop Address"); ?></th>
                    <th><?= __("City"); ?></th>
                    <th><?= __("State"); ?></th>
                    <th><?= __("Postal_Code"); ?></th>
                    <th><?= __("Country"); ?></th>
                    <th><?= __("Phone"); ?></th>
                    <th><?= __("Email"); ?></th>
                    <th><?= __("Invoice Footer"); ?></th>
                    <th class="no-sort" width="100px"><?= __("Action"); ?></th>
                  </tr>
                </thead>

                <tfoot>
                  <tr>
                    <th><?= __("Shop Name"); ?></th>
                    <th><?= __("Shop Address"); ?></th>
                    <th><?= __("City"); ?></th>
                    <th><?= __("State"); ?></th>
                    <th><?= __("Postal_Code"); ?></th>
                    <th><?= __("Country"); ?></th>
                    <th><?= __("Phone"); ?></th>
                    <th><?= __("Email"); ?></th>
                    <th><?= __("Invoice Footer"); ?></th>
                    <th class="no-sort" width="100px"><?= __("Action"); ?></th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=settings&page=shopList";
  </script>
