  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Other Incomes"); ?>
        <?php if(current_user_can("incomes.Add")) {
          echo '<a data-toggle="modal" data-target="#modalDefault" href="'. full_website_address() .'/xhr/?tooltip=true&select2=true&module=incomes&page=newIncome" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> '. __("Add") . '</a>';
        } ?>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
          
            <!-- Box header -->
            <div class="box-header">
              <h3 class="box-title"><?= __("Income List"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>

            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th><?= __("Date"); ?></th>
                    <th><?= __("Shop"); ?></th>
                    <th><?= __("Customer"); ?></th>
                    <th><?= __("Accounts"); ?></th>
                    <th class="countTotal text-center"><?= __("Amount"); ?></th>
                    <th class="no-sort col-md-3"><?= __("Description"); ?></th>
                    <th class="no-sort"><?= __("Action"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th><?= __("Date"); ?></th>
                    <th><?= __("Shop"); ?></th>
                    <th><?= __("Customer"); ?></th>
                    <th><?= __("Accounts"); ?></th>
                    <th><?= __("Amount"); ?></th>
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
    
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=incomes&page=incomeList";
  </script>
