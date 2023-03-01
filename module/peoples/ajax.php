<?php

// Add new user group page
if(isset($_GET['page']) and $_GET['page'] == "newEmployee") {


    // Upload the image and store upload information in $empPhoto variable
    $empPhoto = NULL;
    if($_FILES["userPhoto"]["size"] > 0) {

        $empPhoto = easyUpload($_FILES["userPhoto"], "employees/{$_POST["empId"]}. {$_POST["empfName"]} {$_POST["emplName"]}");

        if(!isset($empPhoto["success"])) {
            return _e($empPhoto);
        } else {
            $empPhoto = $empPhoto["fileName"];
        }
        
    }
    
    $returnMsg = easyInsert(
        "employees", // Table name
        array( // Fileds Name and value
            "emp_PIN"                       => $_POST["empId"],
            "emp_department_id"             => $_POST["empDepartment"],
            "emp_email"                     => $_POST["empEmail"],
            "emp_firstname"                 => $_POST["empfName"],
            "emp_lastname"                  => $_POST["emplName"],
            "emp_positions"                 => $_POST["empDesignation"],
            "emp_working_area"              => $_POST["empWorkingArea"],
            "emp_fathers_name"              => $_POST["empFathersName"],
            "emp_mothers_name"              => $_POST["empMothersName"],
            "emp_nationality"               => $_POST["empNationality"],
            "emp_gender"                    => $_POST["empGender"],
            "emp_marital_status"            => $_POST["empMaritalStatus"],
            "emp_religion"                  => $_POST["empReligion"],
            "emp_country"                   => $_POST["empCountry"],
            "emp_present_address"           => $_POST["empPresentAddress"],
            "emp_permanent_address"         => $_POST["empPermenentAddress"],
            "emp_contact_number"            => $_POST["empContactNumber"],
            "emp_work_number"               => $_POST["empWorkNumber"],
            "emp_emergency_contact_number"  => $_POST["empEmergencyContactNumber"],
            "emp_date_of_birth"             => empty($_POST["empDOB"]) ? NULL : $_POST["empDOB"],
            "emp_national_id"               => $_POST["empNID"],
            "emp_type"                      => $_POST["empType"],
            "emp_nature"                    => $_POST["empNature"],
            "emp_photo"                     => $empPhoto,
            "emp_salary"                    => $_POST["empSalary"],
            "emp_opening_salary"            => $_POST["empOpeningSalary"],
            "emp_opening_overtime"          => $_POST["empOpeningOvertime"],
            "emp_opening_bonus"             => $_POST["empOpeningBonus"],
            "emp_join_date"                 => $_POST["empJoinDate"],
            "emp_add_by"                    => $_SESSION["uid"]
        ),
        array( // No duplicate allow.
            "emp_PIN"       => $_POST["empId"],
            " OR emp_email" => $_POST["empEmail"]
        ),
        true
    );
    
    if(isset($returnMsg["status"]) and $returnMsg["status"] === "success") {

        _s("New employee added successfully.");

        $emp_id = $returnMsg["last_insert_id"];
        // Update salary info
        $updatePayableSalary = easyUpdate(
          "employees",
          array (
            "emp_payable_salary"    => getEmployeePayableAmount($emp_id, "salary"),
            "emp_payable_overtime"  => getEmployeePayableAmount($emp_id, "overtime"),
            "emp_payable_bonus"     => getEmployeePayableAmount($emp_id, "bonus")
          ),
          array (
              "emp_id"    => $emp_id
          )
        );

    } else {

        _e($returnMsg);

    }


}


/*************************** Employee List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "employeeList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "abs(emp_PIN)",
        "emp_photo",
        "emp_firstname",
        "dep_name",
        "emp_type",
        "emp_nature",
        "emp_join_date",
        "emp_salary"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "employees",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
      $requestData['length'] = $totalRecords;
    }
 
    if(!empty($requestData["search"]["value"])) {  // get data with search
        
        $getData = easySelect(
            "employees as employee",
            "emp_id, emp_PIN, emp_department_id, emp_firstname, emp_lastname, emp_type, emp_nature, emp_positions, emp_contact_number, emp_emergency_contact_number, emp_join_date, emp_salary, emp_photo, dep_name",
            array(
                "left join {$table_prefix}emp_department on emp_department_id = dep_id"
            ),
            array (
                "employee.is_trash = 0 and (emp_PIN" => $requestData['search']['value'],
                " OR emp_firstname LIKE" => $requestData['search']['value'] . "%",
                " OR emp_lastname LIKE" => $requestData['search']['value'] . "%",
                " OR emp_positions LIKE" => $requestData['search']['value'] . "%",
                " OR emp_contact_number LIKE" => $requestData['search']['value'] . "%",
                " OR emp_join_date LIKE" => $requestData['search']['value'] . "%",
                " OR emp_salary LIKE" => $requestData['search']['value'] . "%",
                " OR dep_name LIKE" => $requestData['search']['value'] . "%",
                ")"
            ),
            array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        );

        $totalFilteredRecords = $getData ? $getData["count"] : 0;

    } else { // Get data withouth search

        $getDateRange = !empty($requestData['columns'][7]['search']['value'])  ? safe_input($requestData['columns'][7]['search']['value']) : "1970-01-01 - " . date("Y-12-31");
        $dateRange = explode(" - ", $getDateRange);

        /** For dynamic operator searching from datatable */
        $departmentFilter = (array)json_decode($requestData['columns'][4]['search']['value']);
        $empTypeFilter = (array)json_decode($requestData['columns'][5]['search']['value']);
        $empNatureFilter = (array)json_decode($requestData['columns'][6]['search']['value']);

        if( isset($departmentFilter["operator"]) ) {
            $departmentFilter = "and emp_department_id {$departmentFilter["operator"]} '{$departmentFilter["search"]}'";
        } else {
            $departmentFilter = empty($requestData['columns'][4]['search']['value']) ? "" : "and emp_department_id = '{$requestData['columns'][4]['search']['value']}'";
        }

        if( isset($empTypeFilter["operator"]) ) {
            $empTypeFilter = "and emp_type {$empTypeFilter["operator"]} '{$empTypeFilter["search"]}'";
        } else {
            $empTypeFilter = empty($requestData['columns'][5]['search']['value']) ? "" : "and emp_type = '{$requestData['columns'][5]['search']['value']}'";
        }

        if( isset($empNatureFilter["operator"]) ) {
            $empNatureFilter = "and emp_nature {$empNatureFilter["operator"]} '{$empNatureFilter["search"]}'";
        } else {
            $empNatureFilter = empty($requestData['columns'][6]['search']['value']) ? "" : "and emp_nature = '{$requestData['columns'][6]['search']['value']}'";
        }

        $getData = easySelectA(array(
            "table"     => "employees as employee",
            "fields"    => "emp_id, emp_PIN, emp_department_id, emp_firstname, emp_lastname, emp_type, emp_nature, emp_positions, emp_contact_number, emp_emergency_contact_number, emp_join_date, emp_salary, emp_photo, dep_name",
            "join"      => array(
                "left join {$table_prefix}emp_department on emp_department_id = dep_id"
            ),
            "where"     => array(
                "employee.is_trash = 0",
                " $empTypeFilter $departmentFilter $empNatureFilter AND emp_join_date between '{$dateRange[0]}' and '{$dateRange[1]}'"
            ),
            "orderby" => array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit" => array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));

    }

    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['count']) and $getData['count'] > 0) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = $value["emp_contact_number"];
            $allNestedData[] = $value["emp_PIN"];
            $allNestedData[] = empty($value['emp_photo']) ? "<img width='80px' height='80px' src='".full_website_address()."/assets/images/defaultUserPic.png' class='img-circle'/>" : "<img width='80px' height='80px' src='".full_website_address()."/images/?for=employees&id={$value['emp_id']}&q=YTozOntzOjI6Iml3IjtpOjE4MDtzOjI6ImloIjtpOjE4MDtzOjI6ImlxIjtpOjcwO30=&v=". strlen($value['emp_photo']) ."' class='img-circle'/>";
            $allNestedData[] = "<strong>{$value["emp_firstname"]} {$value["emp_lastname"]}</strong><br/> {$value["emp_positions"]} <br/>Contact: {$value["emp_contact_number"]}, {$value["emp_emergency_contact_number"]}";
            $allNestedData[] = $value["dep_name"];
            $allNestedData[] = $value["emp_type"];
            $allNestedData[] = $value["emp_nature"];
            $allNestedData[] = $value["emp_join_date"];
            $allNestedData[] = to_money($value["emp_salary"]);
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=true&module=peoples&page=editEmployee&id='. $value["emp_id"] .'"  data-target="#modalDefaultXlg"><i class="fa fa-edit"></i> Edit</a></li>
                                    <li><a data-toggle="modal" href="'. full_website_address() .'/info/?icheck=false&module=sms&page=sendSMS&number='. $value["emp_contact_number"] .'&name='. urlencode($value["emp_firstname"] .' '. $value["emp_lastname"]) .'"  data-target="#modalDefault"><i class="fa fa-paper-plane"></i> Send SMS</a></li>
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=peoples&page=deleteEmployee" data-to-be-deleted="'. $value["emp_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                    </ul>
                                </div>';
            
            $allData[] = $allNestedData;
        }
    }
    

    $jsonData = array (
        "draw"              => intval( $requestData['draw'] ),
        "recordsTotal"      => intval( $totalRecords ),
        "recordsFiltered"   => intval( $totalFilteredRecords ),
        "data"              => $allData
    );
    
    // Encode in Json Formate
    echo json_encode($jsonData); 
    return;
}


/***************** Delete Employee ****************/
// Delete Group
if(isset($_GET['page']) and $_GET['page'] == "deleteEmployee") {

    $deleteData = easyDelete(
        "employees",
        array(
            "emp_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {
      echo '{
          "title": "'. __("The employee has been successfully deleted.") .'",
          "icon": "success"
      }';
    } 

}

/************************** Edit Employee **********************/
if(isset($_GET['page']) and $_GET['page'] == "editEmployee") {

    $selectEmployee = easySelect(
        "employees",
        "*",
        array(),
        array(
            "emp_id" => $_GET['id']
        )
    );

    $employee = $selectEmployee["data"][0];
    // Include the modal header
    modal_header("Edit Employee", full_website_address() . "/xhr/?module=peoples&page=updateEmployee");
    
    ?>

            <div class="row">
              <!-- Column one -->
              <div class="col-md-4">
                <div class="form-group">
                  <label for="empId"><?= __("Employee ID:"); ?></label>
                  <input type="number" value="<?php echo $employee["emp_PIN"]; ?>" name="empId" class="form-control" id="empId" placeholder="Eneter employee ID">
                </div>
                <div class="form-group">
                  <label for="empDepartment"><?= __("Employee Department:"); ?></label>
                  <select class='form-control' name="empDepartment" id="empDepartment">
                    <option value=""><?= __("Select One"); ?>...</option>
                    <?php
                      // Select all department form database
                      $selectDepartment = easySelect("emp_department");
                      foreach($selectDepartment['data'] as $dep_key => $dep_value) {
                          $selected = ($employee["emp_department_id"] == $dep_value['dep_id']) ? "selected" : "";
                        echo "<option {$selected} value='{$dep_value['dep_id']}'>{$dep_value['dep_name']}</option>";
                      }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="empfName"><?= __("Employee First Name:"); ?></label>
                  <input type="text" value="<?php echo $employee["emp_firstname"]; ?>" name="empfName" class="form-control" id="empfName" placeholder="Enter First Name">
                </div>
                <div class="form-group">
                  <label for="emplName"><?= __("Employee Last Name:"); ?></label>
                  <input type="text" value="<?php echo $employee["emp_lastname"]; ?>" name="emplName" class="form-control" id="emplName" placeholder="Enter Last Name">
                </div>
                <div class="form-group">
                  <label for="empEmail"><?= __("Employee Email:"); ?></label>
                  <input type="email" value="<?php echo $employee["emp_email"]; ?>" name="empEmail" class="form-control" id="empEmail" placeholder="Enter email">
                </div>
                <div class="form-group">
                  <label for="empDesignation"><?= __("Employee Designation:"); ?></label>
                  <input type="text" value="<?php echo $employee["emp_positions"]; ?>" name="empDesignation" class="form-control" id="empDesignation" placeholder="Enter Employee Designation">
                </div>
                <div class="form-group required">
                    <label for="empWorkingArea"><?= __("Working Area"); ?></label>
                    <input type="text" name="empWorkingArea" id="empWorkingArea" value="<?php echo $employee["emp_working_area"]; ?>"  class="form-control" placeholder="Eg. Head office" required>
                </div>
                <div class="form-group">
                  <label for="empFathersName"><?= __("Employee Fathers Name:"); ?></label>
                  <input type="text" value="<?php echo $employee["emp_fathers_name"]; ?>" name="empFathersName" class="form-control" id="empFathersName" placeholder="Enter employee fathers name">
                </div>
                <div class="form-group">
                  <label for="empMothersName"><?= __("Employee Mothers Name:"); ?></label>
                  <input type="text" value="<?php echo $employee["emp_mothers_name"]; ?>" name="empMothersName" class="form-control" id="empMothersName" placeholder="Enter mothers name">
                </div>
                <div class="form-group">
                  <label for="empNationality"><?= __("Employee Nationality:"); ?></label>
                  <input type="text" value="<?php echo $employee["emp_nationality"]; ?>" name="empNationality" class="form-control" id="empNationality" placeholder="Enter employee nationality">
                </div>
                <div class="form-group">
                  <label for="empGender"><?= __("Employee Gender:"); ?></label>
                  <select name="empGender" id="empGender" class="form-control">
                    <option value="Male"><?= __("Male"); ?></option>
                    <option value="Female"><?= __("Female"); ?></option>
                    <option value="Other"><?= __("Other"); ?></option>
                  </select>
                </div>
              </div>
              <!-- Column one -->

              <!-- Column two -->
              <div class="col-md-4">
                
                <div class="form-group">
                  <label for="empMaritalStatus"><?= __("Merital Status:"); ?></label>
                  <select name="empMaritalStatus" id="empMaritalStatus" class="form-control">
                    <?php
                        $maritalStatus = array("Single", "Married", "Devorced", "Widowed");
                        foreach($maritalStatus as $mStatus) {
                            $selected = ($employee["emp_marital_status"] == $mStatus) ? "selected" : "";
                            echo "<option {$selected} vale='{$mStatus}'>{$mStatus}</option>";
                        }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="empReligion"><?= __("Employee Religion:"); ?></label>
                  <input type="text" value="<?php echo $employee["emp_religion"]; ?>" name="empReligion" id="empReligion" class="form-control" placeholder="<?= __("Enter employee religion"); ?>">
                </div>
                <div class="form-group">
                  <label for="empCountry"><?= __("Employee Country:"); ?></label>
                  <input type="text" value="<?php echo $employee["emp_country"]; ?>" name="empCountry" id="empCountry" class="form-control" placeholder="<?= __("Enter employee country"); ?>">
                </div>                      
                <div class="form-group">
                  <label for="empPresentAddress"><?= __("Present Address:"); ?></label>
                  <textarea name="empPresentAddress" id="empPresentAddress" rows="3" class="form-control"><?php echo $employee["emp_present_address"]; ?> </textarea>
                </div>
                <div class="form-group">
                  <label for="empPermenentAddress"><?= __("Permenent Address:"); ?></label>
                  <textarea name="empPermenentAddress" id="empPermenentAddress" rows="3" class="form-control"><?php echo $employee["emp_permanent_address"]; ?> </textarea>
                </div>
                <div class="form-group">
                  <label for="empContactNumber"><?= __("Employee Contact Number:"); ?></label>
                  <input type="number" value="<?php echo $employee["emp_contact_number"]; ?>" name="empContactNumber" id="empContactNumber" class="form-control" placeholder="Enter contact number">
                </div>
                <div class="form-group">
                  <label for="empWorkNumber"><?= __("Employee Work Number:"); ?></label>
                  <input type="text" name="empWorkNumber" value="<?php echo $employee["emp_work_number"]; ?>" id="empWorkNumber" class="form-control" placeholder="Enter work number">
                </div>
                <div class="form-group">
                  <label for="empEmergencyContactNumber"><?= __("Employee Emergency Contact:"); ?></label>
                  <input type="number" value="<?php echo $employee["emp_emergency_contact_number"]; ?>" name="empEmergencyContactNumber" id="empEmergencyContactNumber" class="form-control" placeholder="Enter employee emergency contact number">
                </div>
                <div class="form-group">
                  <label for="empDOB"><?= __("Employee Date of Birth:"); ?></label>
                  <div class="input-group data">
                    <div class="input-group-addon">
                      <li class="fa fa-calendar"></li>
                    </div>
                    <input type="text" value="<?php echo $employee["emp_date_of_birth"]; ?>" name="empDOB" id="datepicker" class="form-control pull-right datePicker">
                  </div>
                </div>
                <div class="form-group">
                  <label for="empNID"><?= __("Employee NID:"); ?></label>
                  <input type="number" value="<?php echo $employee["emp_national_id"]; ?>" name="empNID" id="empNID" class="form-control" placeholder="<?= __("Enter employee NID"); ?>">
                </div>
                
                
              </div>
              <!-- Column two -->

              <!-- Column three -->
              <div class="col-md-4">
                
                <div class="form-group">
                  <label for="empType"><?= __("Employee Type"); ?></label>
                  <select name="empType" id="empType" class="form-control">
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
                <div class="form-group">
                  <label for="empNature"><?= __("Employee Nature"); ?></label>
                  <select name="empNature" id="empNature" class="form-control">
                    <option value=""><?= __("Select One"); ?>...</option>
                    <?php
                        $empNature = array("Full-Time", "Part-Time", "Fixed-Term", "Hourly", "Manage");
                        foreach($empNature as $eNature) {
                            $selected = ($employee["emp_nature"] == $eNature) ? "selected" : "";
                            echo "<option {$selected} value='{$eNature}'>$eNature</option>";
                        }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="empSalary"><?= __("Employee Salary:"); ?></label>
                  <input type="number" value="<?php echo $employee["emp_salary"]; ?>" name="empSalary" id="empSalary" class="form-control" placeholder="Enter salary" value="0">
                </div>
                <div class="form-group">
                  <label for="empOpeningSalary"><?= __("Opening Salary:"); ?></label>
                  <input type="number" value="<?php echo $employee["emp_opening_salary"]; ?>" name="empOpeningSalary" id="empOpeningSalary" class="form-control" placeholder="Enter salary" value="0">
                </div>
                <div class="form-group">
                  <label for="empOpeningOvertime"><?= __("Opening Overtime:"); ?></label>
                  <input type="number" value="<?php echo $employee["emp_opening_overtime"]; ?>" name="empOpeningOvertime" id="empOpeningOvertime" class="form-control" placeholder="Enter salary" value="0">
                </div>
                <div class="form-group">
                  <label for="empOpeningBonus"><?= __("Opening Bonus:"); ?></label>
                  <input type="number" value="<?php echo $employee["emp_opening_bonus"]; ?>" name="empOpeningBonus" id="empOpeningBonus" class="form-control" placeholder="Enter salary" value="0">
                </div>
                <div class="form-group">
                  <label for="empJoinDate"><?= __("Employee Joining Data:"); ?></label>
                  <div class="input-group data">
                    <div class="input-group-addon">
                      <li class="fa fa-calendar"></li>
                    </div>
                    <input type="text" value="<?php echo $employee["emp_join_date"]; ?>" name="empJoinDate" id="empJoinDate" class="form-control pull-right datePicker">
                  </div>
                </div>

                <div class="imageContainer">
                    <div class="form-group">
                        <label for="">Employee Photo: </label>
                        <div class="image_preview" style="width: 60%; margin: auto;">
                            <img style="margin: auto;" class="previewing" width="100%" height="auto" src="<?php echo empty($employee['emp_photo']) ? full_website_address()."/assets/images/defaultUserPic.png" : full_website_address()."/images/?for=employees&id=". $employee['emp_id']; ?>" />
                        </div>
                        <br/>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <span class="btn btn-default btn-file">
                                    <?= __("Select photo"); ?> <input type="file" name="userPhoto" class="imageToUpload">
                                </span>
                            </span>
                            <input type="text" class="form-control imageNameShow" readonly>
                        </div>
                    </div>
                </div>

              </div>
              <!-- Column three -->
            </div> <!-- row -->

            <input type="hidden" name="emp_id" value="<?php echo safe_entities($_GET['id']); ?>">


    <?php

    // Include the modal footer
    modal_footer();

    return;

}

//*******************************  Update Employee ******************** */
if(isset($_GET['page']) and $_GET['page'] == "updateEmployee") {


    // Upload the image and update the employee photos
    if($_FILES["userPhoto"]["size"] > 0) {

        $empPhoto = easyUpload($_FILES["userPhoto"], "employees/{$_POST["empId"]}. {$_POST["empfName"]} {$_POST["emplName"]}");

        if(!isset($empPhoto["success"])) {
            return _e($empPhoto);
        } else {
            $empPhoto = $empPhoto["fileName"];
        }

        // Update Photo
        $updateDepartment = easyUpdate(
            "employees",
            array(
                "emp_photo"         => $empPhoto
            ),
            array(
                "emp_id" => $_POST["emp_id"]
            )
        );
        
    }

    // Update Other Information
    $updateDepartment = easyUpdate(
        "employees",
        array(
            "emp_PIN"                       => $_POST["empId"],
            "emp_department_id"             => $_POST["empDepartment"],
            "emp_email"                     => $_POST["empEmail"],
            "emp_firstname"                 => $_POST["empfName"],
            "emp_lastname"                  => $_POST["emplName"],
            "emp_positions"                 => $_POST["empDesignation"],
            "emp_working_area"              => $_POST["empWorkingArea"],
            "emp_fathers_name"              => $_POST["empFathersName"],
            "emp_mothers_name"              => $_POST["empMothersName"],
            "emp_nationality"               => $_POST["empNationality"],
            "emp_gender"                    => $_POST["empGender"],
            "emp_marital_status"            => $_POST["empMaritalStatus"],
            "emp_religion"                  => $_POST["empReligion"],
            "emp_country"                   => $_POST["empCountry"],
            "emp_present_address"           => $_POST["empPresentAddress"],
            "emp_permanent_address"         => $_POST["empPermenentAddress"],
            "emp_contact_number"            => $_POST["empContactNumber"],
            "emp_work_number"               => $_POST["empWorkNumber"],
            "emp_emergency_contact_number"  => $_POST["empEmergencyContactNumber"],
            "emp_date_of_birth"             => $_POST["empDOB"],
            "emp_national_id"               => $_POST["empNID"],
            "emp_type"                      => $_POST["empType"],
            "emp_nature"                    => $_POST["empNature"],
            "emp_salary"                    => $_POST["empSalary"],
            "emp_opening_salary"            => $_POST["empOpeningSalary"],
            "emp_opening_overtime"          => $_POST["empOpeningOvertime"],
            "emp_opening_bonus"             => $_POST["empOpeningBonus"],
            "emp_join_date"                 => $_POST["empJoinDate"],
            "emp_update_by"                 => $_SESSION["uid"]
        ),
        array(
            "emp_id" => $_POST["emp_id"]
        )
    );

    if($updateDepartment === true) {
        echo _s("Employee successfully updated.");

        $emp_id = safe_input($_POST["emp_id"]);
        // Update salary info
        $updatePayableSalary = easyUpdate(
          "employees",
          array (
            "emp_payable_salary"    => getEmployeePayableAmount($emp_id, "salary"),
            "emp_payable_overtime"  => getEmployeePayableAmount($emp_id, "overtime"),
            "emp_payable_bonus"     => getEmployeePayableAmount($emp_id, "bonus")
          ),
          array (
              "emp_id"    => $emp_id
          )
        );


    } else {
        _e($updateDepartment);
    }

}



/************************** New User **********************/
if(isset($_GET['page']) and $_GET['page'] == "newUser") {

  // Include the modal header
  modal_header("New User", full_website_address() . "/xhr/?module=peoples&page=addNewUser");
  
  ?>
    <div class="box-body">
        <div class="form-group required">
            <label for="employeeID"><?= __("Employee:"); ?></label>
            <select name="employeeID" id="employeeID" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=employeeList" style="width: 100%;" required>
                <option value=""><?= __("Select employee"); ?>....</option>
            </select>
        </div>
        <div class="form-group required">
            <label for="empGroup"><?= __("User Group:"); ?></label>
            <select name="empGroup" id="empGroup" class="form-control select2" style="width: 100%;" required>
                <option value=""><?= __("Select user group"); ?>....</option>
                <?php 
                    $SelectGroup = easySelect("user_group", "group_id, group_name");
                    foreach($SelectGroup["data"] as $emp_group) {
                    $selectedGroup = ($users["user_group_id"] == $emp_group["group_id"]) ? "selected" : "";
                    echo "<option {$selectedGroup} value='{$emp_group['group_id']}'>{$emp_group['group_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="userHomepage"><?= __("User Homepage:"); ?></label>
            <select name="userHomepage" id="userHomepage" class="form-control" style="width: 100%;">
                <option value=""><?= __("Select user homepage"); ?>....</option>
                <?php 
                    generateSelectOptions($default_menu);
                ?>
            </select>
        </div>
        
        <div class="form-group required">
            <label for="userName"><?= __("Username:"); ?></label>
            <input type="text" name="userName" id="userName" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="userEmail"><?= __("User Email:"); ?></label>
            <input type="text" name="userEmail" id="userEmail" class="form-control" required>
        </div>

        <div class="form-group required">
            <label for="userPassword"><?= __("User Password:"); ?></label>
            <input type="password" name="userPassword" id="userPassword" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="confirmUserPassword"><?= __("Confirm User Password:"); ?></label>
            <input type="password" name="confirmUserPassword" id="confirmUserPassword" class="form-control" required>
        </div>
    </div>
    <!-- /Box body-->

    <script>
    $(function () {
      /* Initialize Select2 Elements */
      $('#empGroup').select2();
    });
  </script>
  
  <?php

  // Include the modal footer
  modal_footer();

}

//*******************************  Add New user ******************** */
if(isset($_GET['page']) and $_GET['page'] == "addNewUser") {

  // Error handaling
  if(empty($_POST["employeeID"])) {
    return _e("Please select employee.");
  } else if(empty($_POST["empGroup"])) {
    return _e("Please select user group.");
  } else if(empty($_POST["userName"])) {
    return _e("Please enter username.");
  }else if(empty($_POST["userEmail"])) {
    return _e("Please enter user email.");
  } else if(empty($_POST["userPassword"])) {
    return _e("Please enter password.");
  } else if(strlen($_POST["userPassword"]) < 8) {
    return _e("Password must be at least 8 digit long.");
  } elseif(empty($_POST["userPassword"]) or $_POST["userPassword"] !== $_POST["confirmUserPassword"]) {  
    return _e("User password doesn't match. Please enter correctly");
  }
  
  // Check if all data is not empty
  if(!empty($_POST["employeeID"]) AND !empty($_POST["empGroup"]) AND !empty($_POST["userPassword"]) AND !empty($_POST["confirmUserPassword"]) AND $_POST["userPassword"] === $_POST["confirmUserPassword"]){
   
    $passwordHash = password_hash($_POST["confirmUserPassword"], PASSWORD_DEFAULT);

    // Insert the user into database
    $insertUser = easyInsert(
      "users",
      array(
        "user_emp_id"       => $_POST["employeeID"],
        "user_group_id"     => $_POST["empGroup"],
        "user_permissions"  => html_entity_decode(easySelectA(array(
            "table"     => "user_group",
            "fields"    => "group_permission",
            "where"     => array(
                "group_id"  => $_POST["empGroup"]
            )
        ))["data"][0]["group_permission"]),
        "user_pass"         => $passwordHash,
        "user_name"         => $_POST["userName"],
        "user_email"        => $_POST["userEmail"],
        "user_homepage"     => $_POST["userHomepage"]
      )
    );

    if($insertUser === true) {
          _s("User successfully added");
      } else {
          _e($insertUser);
      }
  }
  
}


/*************************** User List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "userList") {
    
  $requestData = $_REQUEST;
  $getData = [];

  // List of all columns name
  $columns = array(
      "emp_firstname",
      "group_name"
  );
  
  // Count Total recrods
  $totalFilteredRecords = $totalRecords = easySelectA(array(
    "table" => "users",
    "fields" => "count(*) as totalRow",
    "where" => array(
      "is_trash = 0"
    )
  ))["data"][0]["totalRow"];  

  if($requestData['length'] == -1) {
    $requestData['length'] = $totalRecords;
  }

  if(!empty($requestData["search"]["value"])) {  // get data with search
      
      $getData = easySelect(
          "users as user",
          "user_id, user_emp_id, user_group_id, emp_firstname, emp_lastname, emp_photo, group_name, user_status, user_locked_reason, emp_positions",
          array(
              "left join {$table_prefix}employees on user_emp_id = emp_id",
              "left join {$table_prefix}user_group on user_group_id = group_id"
          ),
          array (
              "user.is_trash = 0 and emp_firstname LIKE" => $requestData['search']['value'] . "%",
              " OR emp_lastname LIKE" => $requestData['search']['value'] . "%",
              " OR group_name LIKE" => $requestData['search']['value'] . "%"
          ),
          array (
              $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
          ),
          array (
              "start" => $requestData['start'],
              "length" => $requestData['length']
          )
      );
  
      $totalFilteredRecords = $getData ? $getData["count"] : 0;

  } else { // Get data withouth search

       $getData = easySelect(
          "users as user",
          "user_id, user_emp_id, user_group_id, emp_firstname, emp_lastname, emp_photo, group_name, user_status, user_locked_reason, emp_positions",
          array(
              "left join {$table_prefix}employees on user_emp_id = emp_id",
              "left join {$table_prefix}user_group on user_group_id = group_id"
          ),
          array("user.is_trash = 0"),
          array (
              $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
          ),
          array (
              "start" => $requestData['start'],
              "length" => $requestData['length']
          )
      );

  }

  $allData = [];
  // Check if there have more then zero data
  if(isset($getData['count']) and $getData['count'] > 0) {
      
      foreach($getData['data'] as $key => $value) {
          $allNestedData = [];
          $allNestedData[] = empty($value['emp_photo']) ? "<img width='80px' height='80px' src='".full_website_address()."/assets/images/defaultUserPic.png' class='img-circle'/>" : "<img width='80px' height='80px' src='".full_website_address()."/images/?for=employees&id={$value['user_emp_id']}&q=YTozOntzOjI6Iml3IjtpOjE4MDtzOjI6ImloIjtpOjE4MDtzOjI6ImlxIjtpOjcwO30=&v=". strlen($value['emp_photo']) ."' class='img-circle'/>";
          $allNestedData[] = "<strong>{$value["emp_firstname"]} {$value["emp_lastname"]}</strong><br/>{$value["emp_positions"]}";
          $allNestedData[] = "<strong>{$value["user_status"]}</strong> <br/>{$value["user_locked_reason"]}";
          $allNestedData[] = $value["group_name"];
          // The action button
          $allNestedData[] = '<div class="btn-group">
                                  <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                  action
                                  <span class="caret"></span>
                                  <span class="sr-only">Toggle Dropdown</span>
                                  </button>
                                  <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                  <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=peoples&page=editUser&id='. $value["user_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                  <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=true&module=peoples&page=editPermissions&id='. $value["user_id"] .'"  data-target="#modalDefaultMdm"><i class="fa fa-edit"></i> Edit Permissions</a></li>
                                  <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=peoples&page=deleteUser" data-to-be-deleted="'. $value["user_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                  </ul>
                              </div>';
          
          $allData[] = $allNestedData;
      }
  }
  

  $jsonData = array (
      "draw"              => intval( $requestData['draw'] ),
      "recordsTotal"      => intval( $totalRecords ),
      "recordsFiltered"   => intval( $totalFilteredRecords ),
      "data"              => $allData
  );
  
  // Encode in Json Formate
  echo json_encode($jsonData); 

  return;
}


/***************** Delete User ****************/
// Delete Group
if(isset($_GET['page']) and $_GET['page'] == "deleteUser") {

  $deleteData = easyDelete(
      "users",
      array(
          "user_id" => $_POST["datatoDelete"]
      )
  );

  if($deleteData === true) {
      echo 1;
  } 

}


//************************ Edit user permission ************************* */
if(isset($_GET['page']) and $_GET['page'] == "editPermissions") {

    $selectPermissions = easySelect(
        "users",
        "*",
        array(),
        array(
            "user_id" => $_GET['id']
        )
    );

    $user_permissions = unserialize( html_entity_decode($selectPermissions["data"][0]["user_permissions"]) ) ; // Permission Array

    // Include the modal header
    modal_header("Edit Users Permissions", full_website_address() . "/xhr/?module=peoples&page=updateUserPermission");
    
    ?>
            <div class="box-body">
                <input type="hidden" name="user_id" value="<?php echo safe_entities($_GET['id']); ?>">

                <style>
                
                .tableBodyScroll tbody {
                    display: block;
                    max-height: 550px;
                    overflow-y: scroll;
                }
                .tableBodyScroll thead, .tableBodyScroll tbody tr {
                    display: table;
                    width: 100%;
                    table-layout: fixed;
                }
                .tableBodyScroll thead {
                    width: calc( 100% - 1.1em );
                }

                </style>

                <table class="table table-bordered tableBodyScroll">
                    <thead>
                        <tr>
                            <th class="col-md-4">Permission</th>
                            <th class="col-md-2 text-center">View</th>
                            <th class="col-md-2 text-center">Add</th>
                            <th class="col-md-2 text-center">Edit</th>
                            <th class="col-md-2 text-center">Delete</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php                

                            foreach($defaultPermission as $permName => $permValue) {

                                $permDisplayName = ucfirst(str_replace("_", " ", $permName));

                                echo "<tr>";
                                    echo "<td class='col-md-4'>$permDisplayName</td>";

                                        foreach($default_role as $value) {

                                            $checked = in_array("$permName.$value", $user_permissions) ? "checked" : "";

                                            // If not specified the show all
                                            if(!is_array($permValue)) {
                                                
                                                echo "<td class='col-md-2 text-center'>
                                                    <input $checked type='checkbox' class='square' value='$permName.$value' name='userPermission[]'>
                                                </td>";

                                            } elseif( in_array($value, $permValue ) ) {

                                                echo "<td class='col-md-2 text-center'>
                                                    <input $checked type='checkbox' class='square' value='$permName.$value' name='userPermission[]'>
                                                </td>";

                                            } else {

                                                echo "<td class='col-md-2'></td>";

                                            }
                                        }
                                echo "</tr>";
                            }
                
                        ?>

                    </tbody>

                </table>
               
            </div>

    <?php

    // Include the modal footer
    modal_footer();

    return;

}


//*********************************  Group Update ******************** */
if(isset($_GET['page']) and $_GET['page'] == "updateUserPermission") {

    $updateGroup = easyUpdate(
        "users",
        array(
            "user_permissions"  => serialize($_POST['userPermission'])
        ),
        array(
            "user_id" => $_POST["user_id"]
        )
    );

    if($updateGroup === true) {
        _s("User permission have successfully updated.");
    } else {
       _e($updateGroup);
    }

}

/************************** Edit User **********************/
if(isset($_GET['page']) and $_GET['page'] == "editUser") {

  $selectUser = easySelect(
      "users",
      "*",
      array(),
      array(
          "user_id" => $_GET['id']
      )
  );

  $users = $selectUser["data"][0];
  // Include the modal header
  modal_header("Edit User", full_website_address() . "/xhr/?module=peoples&page=updateUser");
  
  ?>
    <div class="box-body">
        <div class="form-group">
            <label for="employeeID"><?= __("Employee:"); ?></label>
            <select name="employeeID" id="employeeID" class="form-control select2" style="width: 100%;">
                <option value=""><?= __("Select employee"); ?>....</option>
                <?php 
                    $selectEmployee = easySelect("employees", "emp_id, emp_PIN, emp_firstname, emp_lastname");
                    foreach($selectEmployee["data"] as $employee) {
                    $selected = ($users["user_emp_id"] == $employee["emp_id"]) ? "selected" : "";
                    echo "<option {$selected} value='{$employee['emp_id']}'>{$employee['emp_firstname']} {$employee['emp_lastname']} ({$employee['emp_PIN']})</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="empGroup"><?= __("User Group:"); ?></label>
            <select name="empGroup" id="empGroup" class="form-control select2" style="width: 100%;">
                <option value=""><?= __("Select user group"); ?>....</option>
                <?php 
                    $SelectGroup = easySelect("user_group", "group_id, group_name");
                    foreach($SelectGroup["data"] as $emp_group) {
                    $selectedGroup = ($users["user_group_id"] == $emp_group["group_id"]) ? "selected" : "";
                    echo "<option {$selectedGroup} value='{$emp_group['group_id']}'>{$emp_group['group_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="userHomepage"><?= __("User Homepage:"); ?></label>
            <select name="userHomepage" id="userHomepage" class="form-control" style="width: 100%;">
                <option value=""><?= __("Select user homepage"); ?>....</option>
                <?php 
                    generateSelectOptions($default_menu, $users["user_homepage"]);
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="userStatus"><?= __("User Status:"); ?></label>
            <select name="userStatus" id="userStatus" class="form-control" style="width: 100%;">
                <?php 
                    
                    $userStatus = array('Active', 'Lock', 'Ban');
                    foreach($userStatus as $status) {
                        $selected = $status === $users["user_status"] ? "selected" : "";
                        echo "<option {$selected} value='{$status}'>{$status}</option>";
                    }

                ?>
            </select>
        </div>
        <div class="form-group required">
            <label for="userName"><?= __("Username:"); ?></label>
            <input type="text" name="userName" id="userName" class="form-control" value="<?php echo $users["user_name"]; ?>" required>
        </div>
        <div class="form-group required">
            <label for="userEmail"><?= __("User Email:"); ?></label>
            <input type="text" name="userEmail" id="userEmail" class="form-control" value="<?php echo $users["user_email"]; ?>" required>
        </div>
        <div class="form-group">
            <label for="userPassword"><?= __("User Password:"); ?></label>
            <input type="password" name="userPassword" id="userPassword" class="form-control">
        </div>
        <div class="form-group">
            <label for="confirmUserPassword"><?= __("Confirm User Password:"); ?></label>
            <input type="password" name="confirmUserPassword" id="confirmUserPassword" class="form-control">
        </div>
        <input type="hidden" name="user_id" value="<?php echo safe_entities($_GET['id']); ?>">
            
    </div>
    <!-- /Box body-->
    <script>
    $(function () {
      /* Initialize Select2 Elements */
      $('#employeeID').select2();
      $('#empGroup').select2();
    });
  </script>
  <?php

  // Include the modal footer
  modal_footer();

}


//*******************************  Update User ******************** */
if(isset($_GET['page']) and $_GET['page'] == "updateUser") {

    // Error handaling
    if(empty($_POST["employeeID"])) {
        return _e("Please select employee.");
    } else if(empty($_POST["empGroup"])) {
        return _e("Please select user group.");
    } else if(empty($_POST["userName"])) {
        return _e("Please enter username.");
    } else if(empty($_POST["userEmail"])) {
        return _e("Please enter user email.");
    } elseif(!empty($_POST["userPassword"]) and strlen($_POST["userPassword"]) < 8 ) {
        return _e("Password must be at least 8 digit long.");
    } elseif($_POST["userPassword"] !== $_POST["confirmUserPassword"]) {  
        return _e("User password doesn't match. Please enter correctly");
    }

    // Check employee Id and emp group id is not empty
    if(!empty($_POST["employeeID"]) AND !empty($_POST["empGroup"])) {
      
      // if user password filed is not empty then update the user password. Otherwise keep the existing password. 
      if(!empty($_POST["userPassword"])) {
      
        $passwordHash = password_hash($_POST["confirmUserPassword"], PASSWORD_DEFAULT);
        // Update the user into database with password
        $updateUser = easyUpdate(
          "users",
          array(
            "user_emp_id"   => $_POST["employeeID"],
            "user_group_id" => $_POST["empGroup"],
            "user_permissions"  => html_entity_decode(easySelectA(array(
                "table"     => "user_group",
                "fields"    => "group_permission",
                "where"     => array(
                    "group_id"  => $_POST["empGroup"]
                )
            ))["data"][0]["group_permission"]),
            "user_pass"     => $passwordHash,
            "user_homepage"     => $_POST["userHomepage"],
            "user_status"       => $_POST["userStatus"],
            "user_name"         => $_POST["userName"],
            "user_email"        => $_POST["userEmail"],
            "user_locked_reason" => ""
          ),
          array(
            "user_id" => $_POST["user_id"]
          )
        );

      } else {
        
        // Update the user into database without password
        $updateUser = easyUpdate(
          "users",
          array(
            "user_emp_id"   => $_POST["employeeID"],
            "user_permissions"  => html_entity_decode(easySelectA(array(
                "table"     => "user_group",
                "fields"    => "group_permission",
                "where"     => array(
                    "group_id"  => $_POST["empGroup"]
                )
            ))["data"][0]["group_permission"]),
            "user_group_id" => $_POST["empGroup"],
            "user_homepage"     => $_POST["userHomepage"],
            "user_status"       => $_POST["userStatus"],
            "user_name"         => $_POST["userName"],
            "user_email"        => $_POST["userEmail"],
            "user_locked_reason" => ""
          ),
          array(
            "user_id" => $_POST["user_id"]
          )
        );

      }
      
      if($updateUser === true) {
            _s("User successfully updated.");
        } else {
            _e($updateUser);
        }

    }
    
}


/************************** Edit Profile **********************/
if(isset($_GET['page']) and $_GET['page'] == "editProfile") {

  $selectUser = easySelectA(array(
    "table"   => "users",
    "fields"  => "emp_firstname, emp_lastname, user_pass, user_language",
    "join"    => array(
      "left join {$table_prefix}employees on user_emp_id = emp_id"
    ),
    "where"   => array(
      "user_id" => $_GET["id"]
    )
  ));

  $users = $selectUser["data"][0];
  // Include the modal header
  modal_header("Edit Profile", full_website_address() . "/xhr/?module=peoples&page=updateProfile");
  
  ?>
    <div class="box-body">
      <div class="form-group">
        <label for=""><?= __("Name:"); ?></label>
        <?php echo $users["emp_firstname"] . ' ' . $users["emp_lastname"]; ?>
      </div>
      <div class="form-group">
        <label for="userLanguage"><?= __("Language:"); ?></label>
        <select name="userLanguage" id="userLanguage" class="form-control">
          <?php 
            
            static $lang = array(
              ""      => "Default",
              "bn_BD" => "Bengali (বাংলা)"
            );

            foreach($lang as $lang_code => $lang_name){
              $selected = $users["user_language"] === $lang_code ? "selected" : "";
              echo "<option $selected value='$lang_code'>$lang_name</option>";
            }

          ?>
        </select>
      </div>
      <div class="form-group">
        <label for="oldPassword"><?= __("Old Password:"); ?></label>
        <input type="password" name="oldPassword" id="oldPassword" class="form-control">
      </div>
      <div class="form-group">
        <label for="newPassword"><?= __("New Password:"); ?></label>
        <input type="password" name="newPassword" id="newPassword" class="form-control">
      </div>
      <div class="form-group">
        <label for="confirmNewPassword"><?= __("Confirm New Password:"); ?></label>
        <input type="password" name="confirmNewPassword" id="confirmNewPassword" class="form-control">
      </div>
      <input type="hidden" name="user_id" value="<?php echo safe_entities($_GET['id']); ?>">
            
    </div>
    <!-- /Box body-->

    <script>

      $(document).on("submit", "#modalForm", function(e) {
        e.preventDefault();

        if( $("#userLanguage").val() !== undefined && $("#userLanguage").val() !== '' ) {

          $.ajax({
            url: full_website_address + '/include/local/lang/'+ $("#userLanguage").val() +'.json',
            success: function(data) {
                console.log(data);

              localStorage.setItem("dtLang", JSON.stringify(data));
            },
            error: function() {
              localStorage.setItem("dtLang", "{}");
            }
          });

        } else {
          localStorage.setItem("dtLang", "{}");
        }
        
      });

    </script>

  <?php

  // Include the modal footer
  modal_footer();

}


//*******************************  Update user profile ******************** */
if(isset($_GET['page']) and $_GET['page'] == "updateProfile") {


  $userPass = easySelectA(array(
    "table"   => "users",
    "fields"  => "user_pass",
    "where"   => array(
      "user_id" => $_POST["user_id"]
    )
  ))["data"][0]["user_pass"];


  if( !empty($_POST["oldPassword"]) and !password_verify($_POST["oldPassword"], $userPass ) ) {

    return _e("You enter wrong old password.");

  } elseif(!empty($_POST["newPassword"]) and strlen($_POST["newPassword"]) < 8 ) {

    return _e("Password must be at least 8 digit long.");

  } elseif($_POST["newPassword"] !== $_POST["confirmNewPassword"]) {  

    return _e("User password doesn't match. Please enter correctly");

  }

  $passwordHash = password_hash($_POST["confirmNewPassword"], PASSWORD_DEFAULT);
  // Update the user into database with password
  $updateUser = easyUpdate(
    "users",
    array(
      "user_language" => $_POST["userLanguage"],
      "user_pass"     => empty($_POST["oldPassword"]) ? $userPass : $passwordHash
    ),
    array(
      "user_id" => $_POST["user_id"]
    )
  );

  
  if($updateUser === true) {
        
      // Set language cookie
      setcookie("lang", safe_entities($_POST["userLanguage"]), 0, "/");
    
      _s("Profile has been successfully updated.");

    } else {
        _e($updateUser);
    }

}

/************************** New Biller **********************/
if(isset($_GET['page']) and $_GET['page'] == "newBiller") {

  // Include the modal header
  modal_header("New Biller", full_website_address() . "/xhr/?module=peoples&page=addNewBiller");
  
  ?>
    <div class="box-body">
      <div class="form-group">
        <label for="userId"><?= __("Select User:"); ?></label>
        <select name="userId" id="userId" class="form-control select2" style="width: 100%;">
          <option value=""><?= __("Choose user"); ?>....</option>
          <?php 

            $selectUsers = easySelect(
              "users as user",
              "user_id, emp_PIN, emp_firstname, emp_lastname",
              array (
                "left join {$table_prefix}employees on user_emp_id = emp_id"
              ),
              array("user.is_trash = 0")
            );

            foreach($selectUsers["data"] as $users) {
              echo "<option value='{$users['user_id']}'>{$users['emp_firstname']} {$users['emp_lastname']} ({$users['emp_PIN']})</option>";
            }
          ?>
        </select>
      </div>
      <div class="form-group">
        <label for="billerAccounts"><?= __("Default Accounts"); ?></label>
        <select name="billerAccounts" id="billerAccounts" class="form-control select2" style="width: 100%;" required>
          <option value="">Select accounts...</option>
          <?php
              $selectAccounts = easySelect("accounts", "accounts_id, accounts_name", array(), array("is_trash=0"));
              
              if($selectAccounts) {
                foreach($selectAccounts["data"] as $accounts) {
                    echo "<option value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                }
              }
          ?>
        </select>
      </div>
      <div class="form-group">
        <label for="billerShop"><?= __("Default Shop:"); ?></label>
        <select name="billerShop" id="billerShop" class="form-control select2" style="width: 100%;">
          <option value=""><?= __("Select Shop"); ?>....</option>
          <?php 
            $SelectShop = easySelect("shops", "shop_id, shop_name, shop_city, shop_state", array(), array("is_trash=0"));
            
            if($SelectShop) {
              foreach($SelectShop["data"] as $shops) {
                echo "<option value='{$shops['shop_id']}'>{$shops['shop_name']} ({$shops['shop_city']}, {$shops['shop_state']})</option>";
              }
            }
          ?>
        </select>
      </div>
      <div class="form-group">
        <label for="billerWarehouse"><?= __("Default Warehouse"); ?></label>
        <select name="billerWarehouse" id="billerWarehouse" class="form-control select2" style="width: 100%;" required>
          <option value=""><?= __("Select Warehouse"); ?>...</option>
          <?php
              $selectWarehouse = easySelect("warehouses", "warehouse_id, warehouse_name", array(), array("is_trash=0"));
              
              if($selectWarehouse) {
                
                foreach($selectWarehouse["data"] as $warehouse) {
                  echo "<option value='{$warehouse['warehouse_id']}'>{$warehouse['warehouse_name']}</option>";
                }
            
              }
          ?>
        </select>
      </div>
            
    </div>
    <!-- /Box body-->

    <script>
    $(function () {
      /* Initialize Select2 Elements */
      $('#empGroup').select2();
    });
  </script>
  
  <?php

  // Include the modal footer
  modal_footer();

}

//*******************************  Add New Biller ******************** */
if(isset($_GET['page']) and $_GET['page'] == "addNewBiller") {

  // Error handaling
  if(empty($_POST["userId"])) {
    return _e("Please select user.");
  } else if(empty($_POST["billerShop"])) {
    return _e("Please select shop.");
  } else if(empty($_POST["billerAccounts"])) {
    return _e("Please select accounts.");
  } else if(empty($_POST["billerWarehouse"])) {
    return _e("Please select warehouse.");
  }
  
  // Check if all data is not empty
  if(!empty($_POST["userId"]) AND !empty($_POST["billerShop"])){
   
    // Insert the biller into database
    $insertBiller = easyInsert(
      "billers",
      array(
        "biller_user_id"      => $_POST["userId"],
        "biller_shop_id"      => $_POST["billerShop"],
        "biller_accounts_id"  => $_POST["billerAccounts"],
        "biller_warehouse_id" => $_POST["billerWarehouse"],
      ), 
      array (
        "biller_user_id"  => $_POST["userId"]
      )
    );
    
    if(strlen($insertBiller) < 5) {
          _s("New Biller successfully added.");
      } else {
          _e($insertBiller);
      }
  }
  
}


/*************************** Biller List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "billerList") {
    
  $requestData = $_REQUEST;
  $getData = [];

  // List of all columns name
  $columns = array(
      "emp_firstname",
      "emp_firstname",
      "emp_contact_number",
      "shop_name"
  );
  
  // Count Total recrods
  $totalFilteredRecords = $totalRecords = easySelectA(array(
    "table" => "billers",
    "fields" => "count(*) as totalRow",
    "where" => array(
      "is_trash = 0"
    )
  ))["data"][0]["totalRow"];

  if($requestData['length'] == -1) {
    $requestData['length'] = $totalRecords;
  }

  if(!empty($requestData["search"]["value"])) {  // get data with search
      
      $getData = easySelect(
          "billers as biller",
          "biller_user_id, biller_shop_id, biller_accounts_id, accounts_name, emp_id, emp_firstname, emp_lastname, emp_positions, emp_photo, emp_contact_number, emp_email, shop_name, shop_city, shop_state",
          array(
              "left join {$table_prefix}users on biller_user_id = user_id",  
              "left join {$table_prefix}shops on biller_shop_id = shop_id",
              "left join {$table_prefix}accounts on biller_accounts_id = accounts_id",
              "left join {$table_prefix}employees on user_emp_id = emp_id"
          ),
          array (
              "biller.is_trash = 0 and emp_firstname LIKE" => $requestData['search']['value'] . "%",
              " OR emp_lastname LIKE" => $requestData['search']['value'] . "%",
              " OR shop_name LIKE" => $requestData['search']['value'] . "%",
              " OR emp_contact_number LIKE" => $requestData['search']['value'] . "%"
          ),
          array (
              $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
          ),
          array (
              "start" => $requestData['start'],
              "length" => $requestData['length']
          )
      );
  
      $totalFilteredRecords = $getData ? $getData["count"] : 0;

  } else { // Get data withouth search

      $getData = easySelect(
          "billers as biller",
          "biller_user_id, biller_shop_id, biller_accounts_id, accounts_name, emp_id, emp_firstname, emp_lastname, emp_positions, emp_photo, emp_contact_number, emp_email, warehouse_name, shop_name, shop_city, shop_state",
          array(
              "left join {$table_prefix}users on biller_user_id = user_id",  
              "left join {$table_prefix}shops on biller_shop_id = shop_id",
              "left join {$table_prefix}accounts on biller_accounts_id = accounts_id",
              "left join {$table_prefix}warehouses on biller_warehouse_id = warehouse_id",
              "left join {$table_prefix}employees on user_emp_id = emp_id"
          ),
          array("biller.is_trash = 0"),
          array (
              $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
          ),
          array (
              "start" => $requestData['start'],
              "length" => $requestData['length']
          )
      );

  }

  $allData = [];
  // Check if there have more then zero data
  if(isset($getData['count']) and $getData['count'] > 0) {
      
      foreach($getData['data'] as $key => $value) {
          $allNestedData = [];
          $allNestedData[] = empty($value['emp_photo']) ? "<img width='80px' height='80px' src='".full_website_address()."/assets/images/defaultUserPic.png' class='img-circle'/>" : "<img width='80px' height='80px' src='".full_website_address()."/images/?for=employees&id={$value['emp_id']}&q=YTozOntzOjI6Iml3IjtpOjE4MDtzOjI6ImloIjtpOjE4MDtzOjI6ImlxIjtpOjcwO30=&v=". strlen($value['emp_photo']) ."' class='img-circle'/>";
          $allNestedData[] = "<strong>{$value["emp_firstname"]} {$value["emp_lastname"]}</strong><br/> {$value["emp_positions"]}";
          $allNestedData[] = $value["emp_contact_number"] . "</br>" . $value["emp_email"];
          $allNestedData[] = "{$value['shop_name']} ({$value['shop_city']}, {$value['shop_state']})";
          $allNestedData[] = $value['accounts_name'];
          $allNestedData[] = $value['warehouse_name'];
          // The action button
          $allNestedData[] = '<div class="btn-group">
                                  <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                  action
                                  <span class="caret"></span>
                                  <span class="sr-only">Toggle Dropdown</span>
                                  </button>
                                  <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                  <li><a class="'. ( current_user_can("peoples_biller.Delete") ? "" : "restricted " ) .'deleteEntry" href="'. full_website_address() . '/xhr/?module=peoples&page=deleteBiller" data-to-be-deleted="'. $value["biller_user_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                  <li><a class="'. ( current_user_can("peoples_biller.Edit") ? "" : "restricted " ) .'" data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=peoples&page=editBiller&id='. $value["biller_user_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit Biller</a></li>
                                  <li class="divider"></li>
                                  <li><a class="'. ( current_user_can("peoples_user.Edit") ? "" : "restricted " ) .'" data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=peoples&page=editUser&id='. $value["biller_user_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit this User</a></li>
                                  <li><a class="'. ( current_user_can("peoples_employee.Edit") ? "" : "restricted " ) .'" data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=peoples&page=editEmployee&id='. $value["emp_id"] .'"  data-target="#modalDefaultXlg"><i class="fa fa-edit"></i> Edit this Employee</a></li>
                                  <li><a class="'. ( current_user_can("settings_shops.Edit") ? "" : "restricted " ) .'" data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=settings&page=editShop&id='. $value["biller_shop_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit this Shop</a></li>
                                  </ul>
                              </div>';
          
          $allData[] = $allNestedData;
      }
  }
  

  $jsonData = array (
      "draw"              => intval( $requestData['draw'] ),
      "recordsTotal"      => intval( $totalRecords ),
      "recordsFiltered"   => intval( $totalFilteredRecords ),
      "data"              => $allData
  );
  
  // Encode in Json Formate
  echo json_encode($jsonData); 
}

/************************** Edit Biller **********************/
if(isset($_GET['page']) and $_GET['page'] == "editBiller") {

  // Include the modal header
  modal_header("Edit Biller", full_website_address() . "/xhr/?module=peoples&page=updateBiller");

  $selectBiller = easySelect(
    "billers",
    "",
    array(),
    array(
      "biller_user_id" => $_GET['id']
    )
  )["data"][0];
  
  ?>
    <div class="box-body">
      <div class="form-group">
        <label for="userId"><?= __("Select User:"); ?></label>
        <select disabled id="userId" class="form-control select2" style="width: 100%;">
          <option value=""><?= __("Choose user"); ?>....</option>
          <?php 

            $selectUsers = easySelect(
              "users as user",
              "user_id, emp_PIN, emp_firstname, emp_lastname",
              array (
                "left join {$table_prefix}employees on user_emp_id = emp_id"
              ),
              array("user.is_trash = 0")
            );

            foreach($selectUsers["data"] as $users) {
              $selected = ($selectBiller["biller_user_id"] === $users['user_id']) ? "selected" : "";
              echo "<option {$selected} value='{$users['user_id']}'>{$users['emp_firstname']} {$users['emp_lastname']} ({$users['emp_PIN']})</option>";
            }
          ?>
        </select>
        <input type="hidden" name="userId" value="<?php echo $selectBiller["biller_user_id"]; ?>">
      </div>
      <div class="form-group">
        <label for="billerShop"><?= __("Default Shop:"); ?></label>
        <select name="billerShop" id="billerShop" class="form-control select2" style="width: 100%;">
          <option value=""><?= __("Select Shop"); ?>....</option>
          <?php 
            $SelectShop = easySelect("shops", "shop_id, shop_name, shop_city, shop_state", array(), array("is_trash=0"));
            foreach($SelectShop["data"] as $shops) {
              $selected = ($selectBiller["biller_shop_id"] === $shops['shop_id']) ? "selected" : "";
              echo "<option {$selected} value='{$shops['shop_id']}'>{$shops['shop_name']} ({$shops['shop_city']}, {$shops['shop_state']})</option>";
            }
          ?>
        </select>
      </div>
      <div class="form-group">
        <label for="billerAccounts"><?= __("Default Accounts"); ?></label>
        <select name="billerAccounts" id="billerAccounts" class="form-control select2" style="width: 100%;" required>
          <option value=""><?= __("Select accounts"); ?>...</option>
          <?php
              $selectAccounts = easySelect("accounts", "accounts_id, accounts_name", array(), array("is_trash=0"));
              
              foreach($selectAccounts["data"] as $accounts) {
                  $selected = ($selectBiller["biller_accounts_id"] === $accounts['accounts_id']) ? "selected" : "";
                  echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
              }
          ?>
        </select>
      </div>
      <div class="form-group">
        <label for="billerWarehouse"><?= __("Default Warehouse"); ?></label>
        <select name="billerWarehouse" id="billerWarehouse" class="form-control select2" style="width: 100%;" required>
          <option value="">Select Warehouse...</option>
          <?php
              $selectWarehouse = easySelect("warehouses", "warehouse_id, warehouse_name", array(), array("is_trash=0"));
              
              if($selectWarehouse) {
                
                foreach($selectWarehouse["data"] as $warehouse) {
                  $selected = ($selectBiller["biller_warehouse_id"] === $warehouse['warehouse_id']) ? "selected" : "";
                  echo "<option $selected value='{$warehouse['warehouse_id']}'>{$warehouse['warehouse_name']}</option>";
                }
            
              }
          ?>
        </select>
      </div>
            
    </div>
    <!-- /Box body-->

    <script>
    $(function () {
      /* Initialize Select2 Elements */
      $('#empGroup').select2();
    });
  </script>
  
  <?php

  // Include the modal footer
  modal_footer();

}

//*******************************  Update Biller ******************** */
if(isset($_GET['page']) and $_GET['page'] == "updateBiller") {

  // Error handaling
  if(empty($_POST["billerShop"])) {
    return _e("Please select shop.");
  }  else if(empty($_POST["billerAccounts"])) {
    return _e("Please select accounts.");
  }
  
  // Insert the biller into database
  $updateBiller = easyUpdate(
    "billers",
    array(
      "biller_shop_id"      => $_POST["billerShop"],
      "biller_accounts_id"  => $_POST["billerAccounts"],
      "biller_warehouse_id" => $_POST["billerWarehouse"]
    ), 
    array (
      "biller_user_id"  => $_POST["userId"]
    )
  );
  
  if(strlen($updateBiller) < 5) {
      _s("Biller successfully updated");
    } else {
      _e($updateBiller);
    }
  
}


/***************** Delete Biller ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteBiller") {

  if(current_user_can("peoples_biller.Delete") !== true) {
    echo '{
        "title": "Sorry!",
        "text": "'. __("you do not have permission to delete biller.") .'",
        "showConfirmButton": true,
        "showCloseButton": true,
        "toast": false,
        "icon": "error"
    }';
    return;
  }

  $deleteData = easyDelete(
      "billers",
      array(
          "biller_user_id" => $_POST["datatoDelete"]
      )
  );

  if($deleteData === true) {
      echo 1;
  } else {
    echo $deleteData;
  }

}


/************************** Edit Customer **********************/
if(isset($_GET['page']) and $_GET['page'] == "newCustomer") {

  // Include the modal header
  modal_header("New Customer", full_website_address() . "/xhr/?module=peoples&page=addNewCustomer");

  $customerName = ( isset($_GET["val"]) and !is_numeric($_GET["val"]) ) ? safe_entities($_GET["val"]) : "";
  $customerMobile = ( isset($_GET["val"]) and is_numeric($_GET["val"]) ) ? safe_entities($_GET["val"]) : "";
  
  ?>
    <div class="box-body">
        
        <div class="row">

            <div class="form-group required col-md-12">
                <label for="customerName"><?= __("Customer Name:"); ?></label>
                <input type="text" name="customerName" id="customerName" value="<?php echo $customerName; ?>" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="customerNameLocalLen"><?= __("Customer Name in Local Language:"); ?></label>
                <input type="text" name="customerNameLocalLen" id="customerNameLocalLen" value="" class="form-control">
            </div>
            <div class="form-group required col-md-6">
                <label for="customerType"><?= __("Customer Type:"); ?></label>
                <select name="customerType" id="customerType" class="form-control" required>
                    <option value="">Select customer type</option>
                    <option value="Distributor">Distributor</option>
                    <option value="Wholesaler">Wholesaler</option>
                    <option value="Retailer">Retailer</option>
                    <option value="Consumer">Consumer</option>
                </select>
            </div>
            <div class="form-group required col-md-6">
                <label for="customerPhone"><?= __("Phone:"); ?></label>
                <input type="text" name="customerPhone" id="customerPhone" value="<?php echo $customerMobile; ?>" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
                <label for="customerEmail"><?= __("Email:"); ?></label>
                <input type="email" name="customerEmail" id="customerEmail" value="" class="form-control">
            </div>
            <div class="form-group required col-md-6">
                <label for="customerOpeningBalance"><?= __("Opening Balance"); ?></label>
                <input type="number" name="customerOpeningBalance" id="customerOpeningBalance" value="0" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="customerShippingRate"><?= __("Shipping Rate"); ?></label>
                <input type="number" name="customerShippingRate" id="customerShippingRate" value="0" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="customerDiscount"><?= __("Discount"); ?></label>
                <input type="text" name="customerDiscount" id="customerDiscount" value="0" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="customerSendNotification"><?= __("Notfication"); ?></label>
                <select name="customerSendNotification" id="customerSendNotification" class="form-control">
                    <option value="0">Don't Send</option>
                    <option value="1">Do Send</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="customerDivision"><?= __("Division:"); ?></label>
                <select name="customerDivision" id="customerDivision" class="form-control" style="width: 100%;">
                <option value=""><?= __("Select Division"); ?></option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="customerDistrict"><?= __("Customer District:"); ?></label>
                <select name="customerDistrict" id="customerDistrict" class="form-control" style="width: 100%;">
                <option value=""><?= __("Select District"); ?></option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="customerUpazila"><?= __("Customer Upazila:"); ?></label>
                <select name="customerUpazila" id="customerUpazila" class="form-control" style="width: 100%;">
                <option value=""><?= __("Select Upazila"); ?></option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="customerPostalCode"><?= __("Postal Code:"); ?></label>
                <input type="text" name="customerPostalCode" id="customerPostalCode" value="" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="customerCountry"><?= __("Country:"); ?></label>
                <select name="customerCountry" id="customerCountry" class="form-control">
                    <option value="Bangladesh"><?= __("Bangladesh"); ?></option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="customerWebsite"><?= __("Website:"); ?></label>
                <input type="text" name="customerWebsite" id="customerWebsite" value="" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label for="customerAddress"><?= __("Customer Address:"); ?></label>
            <textarea name="customerAddress" id="customerAddress" rows="3" class="form-control"> </textarea>
        </div>
        
            
    </div>
    <!-- /Box body-->

    <script>
    
      /* If division, district and upazila change then clear the lower fields */
      $(document).on("change", "#customerDivision", function() {
          $("#customerDistrict").val(null).trigger("change");
          $("#customerUpazila").val(null).trigger("change");
      });
      $(document).on("change", "#customerDistrict", function() {
          $("#customerUpazila").val(null).trigger("change");
      });


      $(document).on('mouseenter focus', '#customerDivision, #customerDistrict, #customerUpazila', function() {
        
        var select2AjaxUrl = '<?php echo full_website_address() ?>/info/?module=select2&page=';

        /* Initialize Select Ajax Elements */
        $(this).select2({
          placeholder: $(this).children('option:first').html(), /* Get the first option as placeholder */
          ajax: {
            url: function() {
              if( $(this)[0]['name'] === "customerDivision") {

                return select2AjaxUrl + "divisionList";

              } else if( $(this)[0]['name'] === "customerDistrict" ) {
                
                if($("#customerDivision").val() === "") {
                  
                  $(this).select2("close");
                  return alert("Please select the division");
                }
                /* Generate the url */
                return select2AjaxUrl + "districtList&division_id="+$("#customerDivision").val();
                
              } else if( $(this)[0]['name'] === "customerUpazila" ) {
  
                if($("#customerDistrict").val() === "") {
                  $(this).select2("close");
                  return alert("Please select the district");
                }
                /* Generate the url */
                return select2AjaxUrl + "upazilaList&district_id="+$("#customerDistrict").val();

              }

            },
            dataType: "json",
            delay: 250,
            processResults: function (data) {
              return {
                results: data
              };
            },
            cache: true
          }
        });

      });

    </script>

  <?php

  // Include the modal footer
  modal_footer("Add Customer");

}

//*******************************  Add New Customer ******************** */
if(isset($_GET['page']) and $_GET['page'] == "addNewCustomer") {

  // Error handaling
  if(empty($_POST["customerName"])) {
    return _e("Please enter customer name.");
  } else if(empty($_POST["customerPhone"])) {
    return _e("Please enter customer phone.");
  }
  
  // Check if all data is not empty
  if(!empty($_POST["customerName"]) AND !empty($_POST["customerPhone"])){
   
    // Insert the customer into database
    $insertCustomer = easyInsert(
      "customers",
      array(
        "customer_name"             => $_POST["customerName"],
        "customer_name_in_local_len"=> $_POST["customerNameLocalLen"],
        "customer_type"             => $_POST["customerType"],
        "customer_opening_balance"  => $_POST["customerOpeningBalance"],
        "customer_balance"          => ($_POST["customerOpeningBalance"] < 0) ? 0 : abs($_POST["customerOpeningBalance"]),
        "customer_due"              => ($_POST["customerOpeningBalance"] > 0) ? 0 : abs($_POST["customerOpeningBalance"]),
        "customer_shipping_rate"    => $_POST["customerShippingRate"],
        "customer_discount"         => $_POST["customerDiscount"],
        "customer_upazila"          => empty($_POST["customerUpazila"]) ? NULL : $_POST["customerUpazila"],
        "customer_district"         => empty($_POST["customerDistrict"]) ? NULL : $_POST["customerDistrict"],
        "customer_division"         => empty($_POST["customerDivision"]) ? NULL : $_POST["customerDivision"],
        "customer_address"          => $_POST["customerAddress"],
        "customer_postal_code"      => $_POST["customerPostalCode"],
        "customer_country"          => $_POST["customerCountry"],
        "customer_phone"            => $_POST["customerPhone"],
        "send_notif"                => $_POST["customerSendNotification"],
        "customer_email"            => $_POST["customerEmail"],
        "customer_website"          => empty($_POST["customerWebsite"]) ? '' : ''
      ),
      array(
        "customer_phone"            => $_POST["customerPhone"]
      )
    );
    
    if($insertCustomer === true) {
          _s("New customer successfully added.");
    } else {
        _e($insertCustomer);
    }
  }
  
}


/*************************** Customer List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "customerList") {
    
  $requestData = $_REQUEST;
  $getData = [];

  // List of all columns name for sorting
  $columns = array(
      "",
      "customer_name",
      "division_name",
      "division_name",
      "customer_address",
      "customer_phone"
  );
  
  // Count Total recrods
  $totalFilteredRecords = $totalRecords = easySelectA(array(
    "table" => "customers",
    "fields" => "count(*) as totalRow",
    "where" => array(
      "is_trash = 0"
    )
  ))["data"][0]["totalRow"];

  if($requestData['length'] == -1) {
    $requestData['length'] = $totalRecords;
  }

  if(!empty($requestData["search"]["value"])) {  // get data with search
      
      $getData = easySelect(
          "customers as customer",
          "customer_id, customer_name, district_name, division_name, customer_phone, customer_email, customer_website, customer_address",
          array (
            "left join {$table_prefix}districts on customer_district = district_id",
            "left join {$table_prefix}divisions on customer_division = division_id",
          ),
          array (
              "customer.is_trash = 0 and customer_name LIKE" => $requestData['search']['value'] . "%",
              " OR district_name LIKE" => $requestData['search']['value'] . "%",
              " OR division_name LIKE" => $requestData['search']['value'] . "%",
              " OR customer_phone LIKE" => $requestData['search']['value'] . "%",
              " OR customer_email LIKE" => $requestData['search']['value'] . "%",
              " OR customer_website LIKE" => $requestData['search']['value'] . "%"
          ),
          array (
              $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
          ),
          array (
              "start" => $requestData['start'],
              "length" => $requestData['length']
          )
      );
  
      $totalFilteredRecords = $getData ? $getData["count"] : 0;

  } else { // Get data withouth search

      $getData = easySelect(
          "customers as customer",
          "customer_id, customer_name, district_name, division_name, customer_phone, customer_email, customer_website, customer_address",
          array (
            "left join {$table_prefix}districts on customer_district = district_id",
            "left join {$table_prefix}divisions on customer_division = division_id",
          ),
          array("customer.is_trash = 0"),
          array (
              $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
          ),
          array (
              "start" => $requestData['start'],
              "length" => $requestData['length']
          )
      );

  }

  $allData = [];
  // Check if there have more then zero data
  if(isset($getData['count']) and $getData['count'] > 0) {
      
      foreach($getData['data'] as $key => $value) {
          $allNestedData = [];
          $allNestedData[] = "{$value['customer_phone']}";
          $allNestedData[] = "{$value['customer_name']}";
          $allNestedData[] = "{$value['district_name']}";
          $allNestedData[] = "{$value['division_name']}";
          $allNestedData[] = "{$value['customer_address']}";
          $allNestedData[] = "{$value['customer_phone']}; {$value['customer_email']}";
          // The action button
          $allNestedData[] = '<div class="btn-group">
                                  <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                  action
                                  <span class="caret"></span>
                                  <span class="sr-only">Toggle Dropdown</span>
                                  </button>
                                  <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                  <li><a class="'. ( current_user_can("peoples_customer.Delete") ? "" : "restricted " ) .'deleteEntry" href="'. full_website_address() . '/xhr/?module=peoples&page=deleteCustomer" data-to-be-deleted="'. $value["customer_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                  <li><a class="'. ( current_user_can("peoples_customer.Edit") ? "" : "restricted " ) .'" data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=peoples&page=editCustomer&id='. $value["customer_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit Customer</a></li>
                                  </ul>
                              </div>';
          
          $allData[] = $allNestedData;
      }
  }
  

  $jsonData = array (
      "draw"              => intval( $requestData['draw'] ),
      "recordsTotal"      => intval( $totalRecords ),
      "recordsFiltered"   => intval( $totalFilteredRecords ),
      "data"              => $allData
  );
  
  // Encode in Json Formate
  echo json_encode($jsonData); 
}


/***************** Delete Customer ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteCustomer") {

  $deleteData = easyDelete(
      "customers",
      array(
          "customer_id" => $_POST["datatoDelete"]
      )
  );

  if($deleteData === true) {

      echo '{
          "title": "'. __("The customer has been successfully deleted.") .'",
          "icon": "success"
      }';

  } else {

    echo '{
        "title": "Error: '.$deleteData.'",
        "icon": "error"
    }';

  }

}


/************************** Edit Customer **********************/
if(isset($_GET['page']) and $_GET['page'] == "editCustomer") {

  $selectCustomer = easySelect(
      "customers",
      "customer_name, customer_name_in_local_len, round(customer_opening_balance, 2) as customer_opening_balance, division_id, division_name, district_id, district_name, upazila_id, upazila_name,
      customer_postal_code, customer_address, customer_phone, customer_email, customer_website, customer_shipping_rate, customer_discount, send_notif, customer_type",
      array (
        "left join {$table_prefix}districts on customer_district = district_id",
        "left join {$table_prefix}divisions on customer_division = division_id",
        "left join {$table_prefix}upazilas on customer_upazila = upazila_id",
      ),
      array(
          "customer_id" => $_GET['id']
      )
  );

  $customers = $selectCustomer["data"][0];

  // Include the modal header
  modal_header("Edit Customer", full_website_address() . "/xhr/?module=peoples&page=updateCustomer");
  
  ?>
    <div class="box-body">
        <div class="row">
  
            <div class="form-group required col-md-12">
                <label for="customerName"><?= __("Customer Name:"); ?></label>
                <input type="text" name="customerName" id="customerName" value="<?php echo $customers["customer_name"]; ?>" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
                <label for="customerNameLocalLen"><?= __("Customer Name in Local Language:"); ?></label>
                <input type="text" name="customerNameLocalLen" id="customerNameLocalLen" value="<?php echo $customers["customer_name_in_local_len"]; ?>" class="form-control">
            </div>
            <div class="form-group required col-md-6">
                <label for="customerType"><?= __("Customer Type:"); ?></label>
                <select name="customerType" id="customerType" class="form-control" required>
                    <option value="">Select customer type</option>
                    <option <?php echo $customers["customer_type"] === "Distributor" ? "selected" : ""; ?> value="Distributor">Distributor</option>
                    <option <?php echo $customers["customer_type"] === "Wholesaler" ? "selected" : ""; ?> value="Wholesaler">Wholesaler</option>
                    <option <?php echo $customers["customer_type"] === "Retailer" ? "selected" : ""; ?> value="Retailer">Retailer</option>
                    <option <?php echo $customers["customer_type"] === "Consumer" ? "selected" : ""; ?> value="Consumer">Consumer</option>
                </select>
            </div>
            <div class="form-group required col-md-6">
                <label for="customerPhone"><?= __("Phone:"); ?></label>
                <input type="text" name="customerPhone" id="customerPhone" value="<?php echo $customers["customer_phone"]; ?>" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="customerEmail"><?= __("Email:"); ?></label>
                <input type="email" name="customerEmail" id="customerEmail" value="<?php echo $customers["customer_email"]; ?>" class="form-control">
            </div>
            <div class="form-group required col-md-6">
                <label for="customerOpeningBalance"><?= __("Opening Balance"); ?></label>
                <input type="number" name="customerOpeningBalance" id="customerOpeningBalance" value="<?php echo $customers["customer_opening_balance"]; ?>" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
                <label for="customerShippingRate"><?= __("Shipping Rate"); ?></label>
                <input type="number" name="customerShippingRate" id="customerShippingRate" value="<?php echo $customers["customer_shipping_rate"]; ?>" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="customerDiscount"><?= __("Discount"); ?></label>
                <input type="text" name="customerDiscount" id="customerDiscount" value="<?php echo $customers["customer_discount"]; ?>" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="customerSendNotification"><?= __("Notfication"); ?></label>
                <select name="customerSendNotification" id="customerSendNotification" class="form-control">
                    <option <?php echo $customers["send_notif"] === "0" ? "selected" : ""; ?> value="0">Don't Send</option>
                    <option <?php echo $customers["send_notif"] === "1" ? "selected" : ""; ?> value="1">Do Send</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="customerDivision"><?= __("Division:"); ?></label>
                <select name="customerDivision" id="customerDivision" class="form-control" style="width: 100%;">
                    <option value=""><?= __("Select Division"); ?></option>
                    <option selected value="<?php echo $customers["division_id"] ?>"><?php echo $customers["division_name"] ?></option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="customerDistrict"><?= __("Customer District:"); ?></label>
                <select name="customerDistrict" id="customerDistrict" class="form-control" style="width: 100%;">
                    <option value=""><?= __("Select District"); ?></option>
                    <option selected value="<?php echo $customers["district_id"] ?>"><?php echo $customers["district_name"] ?></option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="customerUpazila"><?= __("Customer Upazila:"); ?></label>
                <select name="customerUpazila" id="customerUpazila" class="form-control" style="width: 100%;">
                    <option value=""><?= __("Select Upazila"); ?></option>
                    <option selected value="<?php echo $customers["upazila_id"] ?>"><?php echo $customers["upazila_name"] ?></option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="customerPostalCode"><?= __("Postal Code:"); ?></label>
                <input type="text" name="customerPostalCode" id="customerPostalCode" value="<?php echo $customers["customer_postal_code"]; ?>" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="customerCountry"><?= __("Country:"); ?></label>
                <select name="customerCountry" id="customerCountry" class="form-control">
                    <option value="Bangladesh"><?= __("Bangladesh"); ?></option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="customerWebsite"><?= __("Website:"); ?></label>
                <input type="text" name="customerWebsite" id="customerWebsite" value="<?php echo $customers["customer_website"]; ?>" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label for="customerAddress"><?= __("Customer Address:"); ?></label>
            <textarea name="customerAddress" id="customerAddress" rows="3" class="form-control"> <?php echo $customers["customer_address"]; ?> </textarea>
        </div>

      <input type="hidden" name="customer_id" value="<?php echo safe_entities($_GET['id']); ?>">
            
    </div>
    <!-- /Box body-->

    <script>
    
      /* If division, district and upazila change then clear the lower fields */
      $(document).on("change", "#customerDivision", function() {
          $("#customerDistrict").val(null).trigger("change");
          $("#customerUpazila").val(null).trigger("change");
      });
      $(document).on("change", "#customerDistrict", function() {
          $("#customerUpazila").val(null).trigger("change");
      });


      $(document).on('mouseenter focus', '#customerDivision, #customerDistrict, #customerUpazila', function() {
        
        var select2AjaxUrl = '<?php echo full_website_address() ?>/info/?module=select2&page=';

        /* Initialize Select Ajax Elements */
        $(this).select2({
          placeholder: $(this).children('option:first').html(), /* Get the first option as placeholder */
          ajax: {
            url: function() {
              if( $(this)[0]['name'] === "customerDivision") {

                return select2AjaxUrl + "divisionList";

              } else if( $(this)[0]['name'] === "customerDistrict" ) {
                
                if($("#customerDivision").val() === "") {
                  
                  $(this).select2("close");
                  return alert("Please select the division");
                }
                /* Generate the url */
                return select2AjaxUrl + "districtList&division_id="+$("#customerDivision").val();
                
              } else if( $(this)[0]['name'] === "customerUpazila" ) {
  
                if($("#customerDistrict").val() === "") {
                  $(this).select2("close");
                  return alert("Please select the district");
                }
                /* Generate the url */
                return select2AjaxUrl + "upazilaList&district_id="+$("#customerDistrict").val();

              }

            },
            dataType: "json",
            delay: 250,
            processResults: function (data) {
              return {
                results: data
              };
            },
            cache: true
          }
        });

      });

    </script>

  <?php

  // Include the modal footer
  modal_footer();

}


//*******************************  Update Customer ******************** */
if(isset($_GET['page']) and $_GET['page'] == "updateCustomer") {

    // Error handaling
  if(empty($_POST["customerName"])) {
    return _e("Please enter customer name.");
  } else if(empty($_POST["customerPhone"])) {
    return _e("Please enter customer phone.");
  }
  
  // Check if all data is not empty
  if(!empty($_POST["customerName"]) AND !empty($_POST["customerPhone"])){
   
    // Update the customer into database
    $updateCustomer = easyUpdate(
      "customers",
      array(
        "customer_name"             => $_POST["customerName"],
        "customer_name_in_local_len"=> $_POST["customerNameLocalLen"],
        "customer_type"             => $_POST["customerType"],
        "customer_opening_balance"  => $_POST["customerOpeningBalance"],
        "customer_shipping_rate"    => $_POST["customerShippingRate"],
        "customer_discount"         => $_POST["customerDiscount"],
        "customer_upazila"          => empty($_POST["customerUpazila"]) ? NULL : $_POST["customerUpazila"],
        "customer_district"         => empty($_POST["customerDistrict"]) ? NULL : $_POST["customerDistrict"],
        "customer_division"         => empty($_POST["customerDivision"]) ? NULL : $_POST["customerDivision"],
        "customer_address"          => $_POST["customerAddress"],
        "customer_postal_code"      => $_POST["customerPostalCode"],
        "customer_country"          => $_POST["customerCountry"],
        "customer_phone"            => $_POST["customerPhone"],
        "send_notif"                => $_POST["customerSendNotification"],
        "customer_email"            => $_POST["customerEmail"],
        "customer_website"          => empty($_POST["customerWebsite"]) ? '' : ''
      ), 
      array (
        "customer_id" => $_POST["customer_id"] 
      )

    );
      
    if($updateCustomer === true) {
        _s("Customer successfully updated.");
    } else {
        _e($updateCustomer);
    }

  }
    
}


/************************** Edit Company **********************/
if(isset($_GET['page']) and $_GET['page'] == "newCompany") {

  // Include the modal header
  modal_header("New Company", full_website_address() . "/xhr/?module=peoples&page=addNewCompany");
  
  ?>
    <div class="box-body">
      <div class="form-group">
        <label for="companyName"><?= __("Company Name:"); ?></label>
        <input type="text" name="companyName" id="companyName" value="" class="form-control">
      </div>
      <div class="form-group required">
        <label for="companyOpeningBalance"><?= __("Opening Balance"); ?></label>
        <input type="number" name="companyOpeningBalance" value="" id="companyOpeningBalance" class="form-control" required>
      </div>
      <div class="form-group required">
        <label for="companyType"><?= __("Company Type"); ?></label>
        <select name="companyType" id="companyType" class="form-control select2" style="width: 100%;" required>
          <?php
            $companyType = array('Manufacturer', 'Supplier', 'Vendor', 'Assembler', 'Binders', 'Others');
            foreach($companyType as $companyType) {
              echo "<option value='{$companyType}'>{$companyType}</option>";
            }
          ?>
        </select>
      </div>
      <div class="form-group">
        <label for="companyContactPerson"><?= __("Contact Person Name:"); ?></label>
        <input type="text" name="companyContactPerson" id="companyContactPerson" value="" class="form-control">
      </div>
      <div class="form-group">
        <label for="companyAddress"><?= __("Company Address:"); ?></label>
        <textarea name="companyAddress" id="companyAddress" rows="3" class="form-control"> </textarea>
      </div>
      <div class="form-group">
        <label for="companyCity"><?= __("Company City:"); ?></label>
        <input type="text" name="companyCity" id="companyCity" value="" class="form-control">
      </div>
      <div class="form-group">
        <label for="companyState"><?= __("State:"); ?></label>
        <input type="text" name="companyState" id="companyState" value="" class="form-control">
      </div>
      <div class="form-group">
        <label for="companyPostalCode"><?= __("Postal Code:"); ?></label>
        <input type="text" name="companyPostalCode" id="companyPostalCode" value="" class="form-control">
      </div>
      <div class="form-group">
        <label for="companyCountry"><?= __("Country:"); ?></label>
        <input type="text" name="companyCountry" id="companyCountry" value="" class="form-control">
      </div>
      <div class="form-group">
        <label for="companyPhone"><?= __("Phone:"); ?></label>
        <input type="text" name="companyPhone" id="companyPhone" value="" class="form-control">
      </div>
      <div class="form-group">
        <label for="companyEmail"><?= __("Email:"); ?></label>
        <input type="email" name="companyEmail" id="companyEmail" value="" class="form-control">
      </div>
      <div class="form-group">
        <label for="companyWebsite"><?= __("Website:"); ?></label>
        <input type="text" name="companyWebsite" id="companyWebsite" value="" class="form-control">
      </div>
            
    </div>
    <!-- /Box body-->

  <?php

  // Include the modal footer
  modal_footer();

}

//*******************************  Add New Company ******************** */
if(isset($_GET['page']) and $_GET['page'] == "addNewCompany") {

  // Error handaling
  if(empty($_POST["companyName"])) {
    return _e("Please enter company name.");
  } else if(empty($_POST["companyPhone"])) {
    return _e("Please enter company phone.");
  } else if(empty($_POST["companyType"])) {
    return _e("Please select company type.");
  }
   
  // Insert the company into database
  $insertCompany = easyInsert(
    "companies",
    array(
      "company_name"           => $_POST["companyName"],
      "company_opening_balance"=> $_POST["companyOpeningBalance"],
      "company_type"           => $_POST["companyType"],
      "company_contact_person" => $_POST["companyContactPerson"],
      "company_address"        => $_POST["companyAddress"],
      "company_city"           => $_POST["companyCity"],
      "company_state"          => $_POST["companyState"],
      "company_postal_code"    => $_POST["companyPostalCode"],
      "company_country"        => $_POST["companyCountry"],
      "company_phone"          => $_POST["companyPhone"],
      "company_email"          => $_POST["companyEmail"],
      "company_website"        => $_POST["companyWebsite"],
      "company_add_by"         => $_SESSION["uid"]
    )
  );
  
  if($insertCompany === true) {
      _s("New company successfully added.");
  } else {
      _e($insertCompany);
  }
  
  
}


/*************************** Company List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "companyList") {
    
  $requestData = $_REQUEST;
  $getData = [];

  // List of all columns name for sorting
  $columns = array(
      "",
      "company_name",
      "company_type",
      "company_contact_person",
      "company_city",
      "company_email"
  );
  
  // Count Total recrods
  $totalFilteredRecords = $totalRecords = easySelectA(array(
    "table" => "companies",
    "fields" => "count(*) as totalRow",
    "where" => array(
      "is_trash = 0"
    )
  ))["data"][0]["totalRow"];

  if($requestData['length'] == -1) {
    $requestData['length'] = $totalRecords;
  }

  if(!empty($requestData["search"]["value"])) {  // get data with search
      
      $getData = easySelect(
          "companies",
          "*",
          array(),
          array (
              "is_trash = 0 and company_name LIKE" => $requestData['search']['value'] . "%",
              " OR company_type LIKE" => $requestData['search']['value'] . "%",
              " OR company_contact_person LIKE" => $requestData['search']['value'] . "%",
              " OR company_city LIKE" => $requestData['search']['value'] . "%",
              " OR company_state LIKE" => $requestData['search']['value'] . "%",
              " OR company_phone LIKE" => $requestData['search']['value'] . "%",
              " OR company_email LIKE" => $requestData['search']['value'] . "%",
              " OR company_website LIKE" => $requestData['search']['value'] . "%"
          ),
          array (
              $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
          ),
          array (
              "start" => $requestData['start'],
              "length" => $requestData['length']
          )
      );
  
      $totalFilteredRecords = $getData ? $getData["count"] : 0;

  } else { // Get data withouth search

      $getData = easySelect(
          "companies",
          "*",
          array(),
          array("is_trash = 0"),
          array (
              $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
          ),
          array (
              "start" => $requestData['start'],
              "length" => $requestData['length']
          )
      );

  }

  $allData = [];
  // Check if there have more then zero data
  if(isset($getData['count']) and $getData['count'] > 0) {
      
      foreach($getData['data'] as $key => $value) {
          $allNestedData = [];
          $allNestedData[] = "{$value['company_phone']}";
          $allNestedData[] = "{$value['company_name']}";
          $allNestedData[] = "{$value['company_type']}";
          $allNestedData[] = "{$value['company_contact_person']}";
          $allNestedData[] = "{$value['company_address']}, {$value['company_city']}, {$value['company_state']}";
          $allNestedData[] = "{$value['company_phone']}; {$value['company_email']}";
          // The action button
          $allNestedData[] = '<div class="btn-group">
                                  <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                  action
                                  <span class="caret"></span>
                                  <span class="sr-only">Toggle Dropdown</span>
                                  </button>
                                  <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                  <li><a class="'. ( current_user_can("peoples_company.Delete") ? "" : "restricted " ) .'deleteEntry" href="'. full_website_address() . '/xhr/?module=peoples&page=deleteCompany" data-to-be-deleted="'. $value["company_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                  <li><a class="'. ( current_user_can("peoples_company.Edit") ? "" : "restricted" ) .'" data-toggle="modal" href="'. full_website_address() .'/xhr/?select2=true&module=peoples&page=editCompany&id='. $value["company_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                  </ul>
                              </div>';
          
          $allData[] = $allNestedData;
      }
  }
  

  $jsonData = array (
      "draw"              => intval( $requestData['draw'] ),
      "recordsTotal"      => intval( $totalRecords ),
      "recordsFiltered"   => intval( $totalFilteredRecords ),
      "data"              => $allData
  );
  
  // Encode in Json Formate
  echo json_encode($jsonData); 
}


/***************** Delete Company ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteCompany") {

  $deleteData = easyDelete(
      "companies",
      array(
          "company_id" => $_POST["datatoDelete"]
      )
  );

  if($deleteData === true) {

      echo '{
          "title": "'. __("The company has been successfully deleted.") .'",
          "icon": "success"
      }';

  } else {

    echo '{
        "title": "Error: '.$deleteData.'",
        "icon": "error"
    }';

  }

}


/************************** Edit Company **********************/
if(isset($_GET['page']) and $_GET['page'] == "editCompany") {

  $selectCompanies = easySelect(
      "companies",
      "*",
      array(),
      array(
          "company_id" => $_GET['id']
      )
  );

  $companies = $selectCompanies["data"][0];

  // Include the modal header
  modal_header("Edit Company", full_website_address() . "/xhr/?module=peoples&page=updateCompany");
  
  ?>
    <div class="box-body">
      <div class="form-group">
        <label for="companyName"><?= __("Company Name:"); ?></label>
        <input type="text" name="companyName" id="companyName" value="<?php echo $companies["company_name"]; ?>" class="form-control">
      </div>
      <div class="form-group required">
        <label for="companyOpeningBalance"><?= __("Opening Balance"); ?></label>
        <input type="number" name="companyOpeningBalance" value="<?php echo $companies["company_opening_balance"]; ?>" id="companyOpeningBalance" class="form-control" required>
      </div>
      <div class="form-group required">
        <label for="companyType"><?= __("Company Type"); ?></label>
        <select name="companyType" id="companyType" class="form-control select2" style="width: 100%;" required>
          <?php
            $companyType = array('Manufacturer', 'Supplier', 'Vendor', 'Assembler', 'Binders', 'Others');
            foreach($companyType as $companyType) {
              $selected = ($companies["company_type"] === $companyType) ? "selected" : "";
              echo "<option {$selected} value='{$companyType}'>{$companyType}</option>";
            }
          ?>
        </select>
      </div>
      <div class="form-group">
        <label for="companyContactPerson"><?= __("Contact Person Name:"); ?></label>
        <input type="text" name="companyContactPerson" id="companyContactPerson" value="<?php echo $companies["company_contact_person"]; ?>" class="form-control">
      </div>
      <div class="form-group">
        <label for="companyAddress"><?= __("Company Address:"); ?></label>
        <textarea name="companyAddress" id="companyAddress" rows="3" class="form-control"> <?php echo $companies["company_address"]; ?> </textarea>
      </div>
      <div class="form-group">
        <label for="companyCity"><?= __("Company City:"); ?></label>
        <input type="text" name="companyCity" id="companyCity" value="<?php echo $companies["company_city"]; ?>" class="form-control">
      </div>
      <div class="form-group">
        <label for="companyState"><?= __("State:"); ?></label>
        <input type="text" name="companyState" id="companyState" value="<?php echo $companies["company_state"]; ?>" class="form-control">
      </div>
      <div class="form-group">
        <label for="companyPostalCode"><?= __("Postal Code:"); ?></label>
        <input type="text" name="companyPostalCode" id="companyPostalCode" value="<?php echo $companies["company_postal_code"] ;?>" class="form-control">
      </div>
      <div class="form-group">
        <label for="companyCountry"><?= __("Country:"); ?></label>
        <input type="text" name="companyCountry" id="companyCountry" value="<?php echo $companies["company_country"]; ?>" class="form-control">
      </div>
      <div class="form-group">
        <label for="companyPhone"><?= __("Phone:"); ?></label>
        <input type="text" name="companyPhone" id="companyPhone" value="<?php echo $companies["company_phone"]; ?>" class="form-control">
      </div>
      <div class="form-group">
        <label for="companyEmail"><?= __("Email:"); ?></label>
        <input type="email" name="companyEmail" id="companyEmail" value="<?php echo $companies["company_email"]; ?>" class="form-control">
      </div>
      <div class="form-group">
        <label for="companyWebsite"><?= __("Website:"); ?></label>
        <input type="text" name="companyWebsite" id="companyWebsite" value="<?php echo $companies["company_website"]; ?>" class="form-control">
      </div>
      <input type="hidden" name="company_id" value="<?php echo safe_entities($_GET['id']); ?>">
            
    </div>
    <!-- /Box body-->

  <?php

  // Include the modal footer
  modal_footer();

}


//*******************************  Update Company ******************** */
if(isset($_GET['page']) and $_GET['page'] == "updateCompany") {

  // Error handaling
  if(empty($_POST["companyName"])) {
    return _e("Please enter company name.");
  } else if(empty($_POST["companyPhone"])) {
    return _e("Please enter company phone.");
  } else if(empty($_POST["companyType"])) {
    return _e("Please select company type.");
  }
  
  // Check if all data is not empty
  if(!empty($_POST["companyName"]) AND !empty($_POST["companyPhone"])){
   
    // Update the company into database
    $updateCompany = easyUpdate(
      "companies",
      array(
        "company_name"           => $_POST["companyName"],
        "company_opening_balance"=> $_POST["companyOpeningBalance"],
        "company_type"           => $_POST["companyType"],
        "company_contact_person" => $_POST["companyContactPerson"],
        "company_address"        => $_POST["companyAddress"],
        "company_city"           => $_POST["companyCity"],
        "company_state"          => $_POST["companyState"],
        "company_postal_code"    => $_POST["companyPostalCode"],
        "company_country"        => $_POST["companyCountry"],
        "company_phone"          => $_POST["companyPhone"],
        "company_email"          => $_POST["companyEmail"],
        "company_website"        => $_POST["companyWebsite"],
        "company_update_by"      => $_SESSION["uid"]
      ), 
      array (
        "company_id" => $_POST["company_id"] 
      )

    );

    if($updateCompany === true) {
        _s("Company successfully updated.");
    } else {
        _e($updateCompany);
    }

  }
    
}



?>