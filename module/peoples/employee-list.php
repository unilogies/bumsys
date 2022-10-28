  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Peoples"); ?>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?= __("Employee List"); ?></h3>
              <div class="printButtonPosition">
                
                <div style="float:right;" class="btn-group">
                    <button type="button" class="btn btn-sm btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                        <?= __("action"); ?>
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                      <li>
                        <button  class="btn btn-sm btn-block btn-default" onclick="sendBulkSMS( '<?= full_website_address() .'/info/?icheck=false&module=sms&page=sendBulkSMS&type=normal&numbers='; ?>'  );"> <i class="fa fa-paper-plane"></i> <?= __("Send SMS"); ?> </button>
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
                    <th width="30px"><?= __("ID"); ?></th>
                    <th class="no-sort" width="80px"><?= __("Photo"); ?></th>
                    <th><?= __("Employee Name"); ?></th>
                    <th><?= __("Department"); ?></th>
                    <th><?= __("Type"); ?></th>
                    <th><?= __("Nature"); ?></th>
                    <th style="width: 120px"><?= __("Joining Date"); ?></th>
                    <th><?= __("Salary"); ?></th>
                    <th class="no-sort no-print" width="100px"><?= __("Action"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th><?= __("ID"); ?></th>
                    <th><?= __("Photo"); ?></th>
                    <th><?= __("Employee Name"); ?></th>
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
                                        $selected = ($employee["emp_type"] == $eType) ? "selected" : "";
                                        echo "<option {$selected} value='{$eType}'>{$eType}</option>";
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

                            <select style="width: 120px;" class='form-control select2' name="empNature" id="empNature">
                                <option value=""><?= __("Select One"); ?>...</option>
                                <?php 
                                    $empNature = array("Full-Time", "Part-Time", "Fixed-Term", "Hourly", "Manage");
                                    foreach($empNature as $eNature) {
                                        $selected = ($employee["emp_type"] == $eNature) ? "selected" : "";
                                        echo "<option {$selected} value='{$eNature}'>{$eNature}</option>";
                                    }
                                ?>
                            </select>

                        </div>
                        
                    </th>
                    <th class="no-print">
                        <input style="width: 160px" type="text" name="employeeJoiningDateRange" id="employeeJoiningDateRange" class="form-control" value="<?= date("1970-01-01") . " - " . date("Y-12-31"); ?>" autoComplete="off" required>
                    </th>
                    <th><?= __("Salary"); ?></th>
                    <th width="100px"><?= __("Action"); ?></th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=peoples&page=employeeList";
    BMS.FUNCTIONS.dateRangePickerPreDefined({selector: "#employeeJoiningDateRange"});
  </script>
