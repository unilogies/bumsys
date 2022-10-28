
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Reports"); ?>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?= __("Employee Reports"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" style="width: 100%;">
                <thead>
                  <tr>
                    <th></th>
                    <th><?= __("Employee Name"); ?></th>
                    <th><?= __("Employee Department"); ?></th>
                    <th><?= __("Employee Type"); ?></th>

                    <th class="countTotal"><?= __("Salary Added"); ?></th>
                    <th class="countTotal"><?= __("Overtime Added"); ?></th>
                    <th class="countTotal"><?= __("Bonus Added"); ?></th>
                    <th class="highlightWithCountTotal"><?= __("Total Wage"); ?></th>

                    <th class="countTotal"><?= __("Salary Paid"); ?></th>
                    <th class="countTotal"><?= __("Overtime Paid"); ?></th>
                    <th class="countTotal"><?= __("Bonus Paid"); ?></th>
                    <th class="highlightWithCountTotal"><?= __("Total Paid"); ?></th>

                    <th class="countTotal"><?= __("Opening Salary"); ?></th>
                    <th class="countTotal"><?= __("Opening Overtime"); ?></th>
                    <th class="countTotal"><?= __("Opening Bonus"); ?></th>
                    <th class="highlightWithCountTotal"><?= __("Opening Balance"); ?></th>
                    
                    <th class="countTotal"><?= __("Salary Due"); ?></th>
                    <th class="countTotal"><?= __("Overtime Due"); ?></th>
                    <th class="countTotal"><?= __("Bonus Due"); ?></th>
                    <th class="countTotal"><?= __("Loan Adjust"); ?></th>
                    <th class="highlightWithCountTotal"><?= __("Total Due"); ?></th>

                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th class="no-print"><input type="text" name="employeeReportDateRange" id="employeeReportDateRange" class="form-control" value="<?= date("1970-01-01") . " - " . date("Y-12-31"); ?>" autoComplete="off" required></th>
                    <th class="no-print">

                        <div class="input-group">
                            
                            <span style="padding: 0;" class="input-group-addon">
                                
                                <button title="Mark is not" class="toggleNotButton" style="background-color: white; font-weight: bold; border: none; height: 20px; width: 20px;" type="button"></button>
                                <input type="hidden" class="notThisValueFilter" value="=">

                            </span>

                            <select style="width: 140px;" class='form-control select2' name="empDepartment" id="empDepartment" required>
                                <option value="">Select One...</option>
                                <?php
                                    // Select all department form database
                                    $selectDepartment = easySelectA(array(
                                        "table"   => "emp_department",
                                        "where"   => array(
                                        "is_trash = 0"
                                        ),
                                        "orderby" => array(
                                        "dep_name"  => "ASC"
                                        )
                                    ));
                                    foreach($selectDepartment['data'] as $dep_key => $dep_value) {
                                        echo "<option value='{$dep_value['dep_id']}'>{$dep_value['dep_name']}</option>";
                                    }
                                ?>
                            </select>

                        </div>
                        
                    </th>
                    <th class="no-print">

                        <div class="input-group">
                            
                            <span style="padding: 0;" class="input-group-addon">
                                
                                <button title="Mark is not" class="toggleNotButton" style="background-color: white; font-weight: bold; border: none; height: 20px; width: 20px;" type="button"></button>
                                <input type="hidden" class="notThisValueFilter" value="=">

                            </span>

                            <select style="width: 120px;" class='form-control select2' name="empType" id="empType">
                                <option value=""><?= __("Select One"); ?>...</option>
                                <?php 
                                    $empType = array("Permanent", "Temporary", "Probation", "Past");
                                    foreach($empType as $eType) {
                                        echo "<option value='{$eType}'>{$eType}</option>";
                                    }
                                ?>
                            </select>

                        </div>
                        
                    </th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
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
    BMS.FUNCTIONS.dateRangePickerPreDefined({selector: "#employeeReportDateRange"});
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=reports&page=employeeReports";


  </script>
