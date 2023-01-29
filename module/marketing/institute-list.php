  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Institutes"); ?>
        <?php if(current_user_can("institutes.Add")) {
          echo '<a data-toggle="modal" data-target="#modalDefault" href="'. full_website_address() .'/xhr/?module=marketing&page=newInstitute" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> '. __('Add') . '</a>';
        } ?>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?= __("Institute List"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-bordered table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th>#</th>
                    <th><?= __("Institute Name"); ?></th>
                    <th><?= __("Institute Type"); ?></th>
                    <th><?= __("EIIN"); ?></th>
                    <th><?= __("Location"); ?></th>
                    <th><?= __("Website"); ?></th>
                    <th class="no-print no-sort" width="100px"><?= __("Action"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th>#</th>
                    <th><?= __("Institute Name"); ?></th>
                    <th class="no-print"> 
                        <select name="instituteType" id="instituteType" class="form-control select2" style="width: 100%">
                            <option value="">Type...</option>
                            <?php
                                $instituteType = array('School', 'College', 'University', 'Coaching', 'Library', 'Store');
                                foreach($instituteType as $type) {
                                    echo "<option value='{$type}'>{$type}</option>";
                                }
                            ?>
                        </select>
                    </th>
                    <th><?= __("EIIN"); ?></th>
                    <th><?= __("Location"); ?></th>
                    <th><?= __("Website"); ?></th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=marketing&page=instituteList";
  </script>
