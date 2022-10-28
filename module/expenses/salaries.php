  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Salaries"); ?>
        <a data-toggle="modal" data-target="#modalDefaultMdm" href="<?php echo full_website_address(); ?>/xhr/?select2=true&module=expenses&page=addSalary" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> <?= __("Add"); ?></a>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Salaries</a></li>
        <li class="active">Salary List</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
          
            <!-- Box header -->
            <div class="box-header">
              <h3 class="box-title"><?= __("Salaries"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>

            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th><?= __("Month"); ?></th>
                    <th><?= __("Name"); ?></th>
                    <th><?= __("Type"); ?></th>
                    <th class="countTotal"><?= __("Amount"); ?></th>
                    <th><?= __("Description"); ?></th>
                    <th class="no-print no-sort" width="100px"><?= __("Action"); ?></th>
                  </tr>
                </thead>

                <tfoot>
                  <tr>
                    <th></th>
                    <th class="col-md-2 no-print"><input style="width: 200px" type="text" autoComplete="off" name="salaryMonth" id="salaryMonth" placeholder="<?= __("Select month"); ?>" class="form-control"></th>
                    <th class="col-md-3 no-print"> <input style="width: 240px" type="text" name="employeeName" id="employeeName" placeholder="<?= __("Enter name or ID"); ?>" class="form-control"> </th>
                    <th class="col-md-2 no-print">
                    <select style="width: 140px" name="salaryType" id="salaryType" class="form-control select2" style="width: 100%;" required>
                      <option value=""><?= __("Select All"); ?>....</option>
                      <?php
                          $salaryType = array("Salary", "Overtime", "Bonus");
                          
                          foreach($salaryType as $salaryType) {
                              echo "<option value='{$salaryType}'>{$salaryType}</option>";
                          }
                      ?>
                    </select>
                    </th>
                    <th><?= __("Amount"); ?></th>
                    <th class="no-print"><?= __("Description"); ?></th>
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

    BMS.FUNCTIONS.dateRangePickerPreDefined({
      selector: "#salaryMonth",
      format: "MMM, YYYY",
    });
    
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=expenses&page=salaryList";
  </script>
