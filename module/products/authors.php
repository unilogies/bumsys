  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Authors"); ?>
        <a data-toggle="modal" data-target="#modalDefault" href="<?php echo full_website_address(); ?>/xhr/?tooltip=true&module=products&page=newAuthor" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> <?= __("Add New"); ?></a>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
          
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjax" class="table table-bordered table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th><?= __("Author Name"); ?></th>
                    <th><?= __("Birth Date"); ?></th>
                    <th><?= __("Death Date"); ?></th>
                    <th><?= __("Mobile"); ?></th>
                    <th><?= __("Address"); ?></th>
                    <th><?= __("Description"); ?></th>
                    <th class="no-sort" width="100px"><?= __("Action"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th><?= __("Author Name"); ?></th>
                    <th><?= __("Birth Date"); ?></th>
                    <th><?= __("Death Date"); ?></th>
                    <th><?= __("Mobile"); ?></th>
                    <th><?= __("Address"); ?></th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=products&page=authorList";
  </script>