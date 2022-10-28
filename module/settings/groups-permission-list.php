  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Settings"); ?>
        <a data-toggle="modal" data-target="#modalDefaultMdm" href="<?php echo full_website_address(); ?>/xhr/?icheck=true&module=settings&page=newGroupPermission" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> <?= __("New Group"); ?></a>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?= __("Group List"); ?></h3>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjax" class="table table-bordered table-striped table-hover" width="100%;">
                <thead>
                  <tr>
                    <th><?= __("Group Id"); ?></th>
                    <th><?= __("Group Name"); ?></th>
                    <th class="no-sort" width="100px"><?= __("Action"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th><?= __("Group Id"); ?></th>
                    <th><?= __("Group Name"); ?></th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=settings&page=groupList";
  </script>