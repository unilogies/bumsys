<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Peoples
        <small>New Employee</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Peoples</a></li>
        <li class="active">Employee</li>
        <li class="active">New Employee</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

      <div class="box box-default">
        
        <div class="box-header with-border">
          <h3 class="box-title">Add Employee</h3>
        </div> <!-- box box-default -->

        <div class="box-body">
          <!-- Form start -->
          <form method="post" role="form" id="jqFormAdd" action="<?php echo full_website_address(); ?>/xhr/?module=peoples&page=newEmployee" enctype="multipart/form-data">

            <div class="row">
              <!-- Column one -->
              <div class="col-md-4">
                <div class="form-group required">
                  <label for="empId">Employee ID:</label>
                  <input type="number" name="empId" class="form-control" id="empId" placeholder="Eneter employee ID" required>
                </div>
                <div class="form-group required">
                  <label for="empDepartment">Employee Department:</label>
                  <select class='form-control' name="empDepartment" id="empDepartment" required>
                    <option value="">Select One...</option>
                    <?php
                      // Select all department form database
                      $selectDepartment = easySelect("emp_department");
                      foreach($selectDepartment['data'] as $dep_key => $dep_value) {
                        echo "<option value='{$dep_value['dep_id']}'>{$dep_value['dep_name']}</option>";
                      }
                    ?>
                  </select>
                </div>
                <div class="form-group required">
                  <label for="empfName">Employee First Name:</label>
                  <input type="text" name="empfName" class="form-control" id="empfName" placeholder="Enter First Name" required>
                </div>
                <div class="form-group required">
                  <label for="emplName">Employee Last Name:</label>
                  <input type="text" name="emplName" class="form-control" id="emplName" placeholder="Enter Last Name" required>
                </div>
                <div class="form-group">
                  <label for="empEmail">Employee Email:</label>
                  <input type="email" name="empEmail" class="form-control" id="empEmail" placeholder="Enter email">
                </div>
                <div class="form-group required">
                  <label for="empDesignation">Employee Designation:</label>
                  <input type="text" name="empDesignation" class="form-control" id="empDesignation" placeholder="Enter Employee Designation" required>
                </div>
                <div class="form-group required">
                    <label for="empWorkingArea">Working Area</label>
                    <input type="text" name="empWorkingArea" id="empWorkingArea" class="form-control" placeholder="Eg. Head office" required>
                </div>
                <div class="form-group">
                  <label for="empFathersName">Employee Fathers Name:</label>
                  <input type="text" name="empFathersName" class="form-control" id="empFathersName" placeholder="Enter employee fathers name">
                </div>
                <div class="form-group">
                  <label for="empMothersName">Employee Mothers Name:</label>
                  <input type="text" name="empMothersName" class="form-control" id="empMothersName" placeholder="Enter mothers name">
                </div>
                <div class="form-group">
                  <label for="empNationality">Employee Nationality:</label>
                  <input type="text" name="empNationality" class="form-control" id="empNationality" placeholder="Enter employee nationality">
                </div>
                <div class="form-group">
                  <label for="empGender">Employee Gender</label>
                  <select name="empGender" id="empGender" class="form-control">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                  </select>
                </div>

              </div>
              <!-- Column one -->

              <!-- Column two -->
              <div class="col-md-4">
                <div class="form-group">
                  <label for="empMaritalStatus">Merital Status:</label>
                  <select name="empMaritalStatus" id="empMaritalStatus" class="form-control">
                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                    <option value="Devorced">Devorced</option>
                    <option value="Widowed">Widowed</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="empReligion">Employee Religion</label>
                  <input type="text" name="empReligion" id="empReligion" class="form-control" placeholder="Enter employee religion">
                </div>
                <div class="form-group">
                  <label for="empCountry">Employee Country:</label>
                  <input type="text" name="empCountry" id="empCountry" class="form-control" placeholder="Enter employee country">
                </div>                      
                <div class="form-group required">
                  <label for="empPresentAddress">Present Address:</label>
                  <textarea name="empPresentAddress" id="empPresentAddress" rows="3" class="form-control" required></textarea>
                </div>
                <div class="form-group required">
                  <label for="empPermenentAddress">Permenent Address:</label>
                  <textarea name="empPermenentAddress" id="empPermenentAddress" rows="3" class="form-control" required></textarea>
                </div>
                <div class="form-group required">
                  <label for="empContactNumber">Employee Contact Number:</label>
                  <input type="text" name="empContactNumber" id="empContactNumber" class="form-control" placeholder="Enter contact number" required>
                </div>
                <div class="form-group">
                  <label for="empWorkNumber">Employee Work Number:</label>
                  <input type="text" name="empWorkNumber" id="empWorkNumber" class="form-control" placeholder="Enter work number">
                </div>
                <div class="form-group">
                  <label for="empEmergencyContactNumber">Employee Emergency Contact:</label>
                  <input type="text" name="empEmergencyContactNumber" id="empEmergencyContactNumber" class="form-control" placeholder="Enter employee emergency contact number">
                </div>
                <div class="form-group">
                  <label for="empDOB">Employee Date of Birth:</label>
                  <div class="input-group data">
                    <div class="input-group-addon">
                      <li class="fa fa-calendar"></li>
                    </div>
                    <input type="text" name="empDOB" id="datepicker" autocomplete="Off" class="form-control pull-right datePicker">
                  </div>
                </div>
                <div class="form-group">
                  <label for="empNID">Employee NID</label>
                  <input type="number" name="empNID" id="empNID" class="form-control" placeholder="Enter employee NID">
                </div>
                
              </div>
              <!-- Column two -->

              <!-- Column three -->
              <div class="col-md-4">
                <div class="form-group required">
                  <label for="empType">Employee Type</label>
                  <select name="empType" id="empType" class="form-control" required>
                    <option value="">Select One...</option>
                    <option value="Permanent">Permanent</option>
                    <option value="Temporary">Temporary</option>
                    <option value="Probation">Probation</option>
                    <option value="Past">Past</option>
                  </select>
                </div>
                <div class="form-group required">
                  <label for="empNature">Employee Nature</label>
                  <select name="empNature" id="empNature" class="form-control" required>
                    <option value="">Select One...</option>
                    <option value="Full-Time">Full-Time</option>
                    <option value="Part-Time">Part-Time</option>
                    <option value="Fixed-Term">Fixed-Term</option>
                    <option value="Hourly">Hourly</option>
                    <option value="Manage">Manage</option>
                  </select>
                </div>
                <div class="form-group required">
                  <label for="empSalary">Employee Salary:</label>
                  <input type="number" name="empSalary" id="empSalary" class="form-control" placeholder="Enter salary" value="0" required>
                </div>
                <div class="form-group">
                  <label for="empOpeningSalary">Opening Salary:</label>
                  <input type="number" name="empOpeningSalary" id="empOpeningSalary" class="form-control" placeholder="Enter salary" value="0">
                </div>
                <div class="form-group">
                  <label for="empOpeningOvertime">Opening Overtime:</label>
                  <input type="number" name="empOpeningOvertime" id="empOpeningOvertime" class="form-control" placeholder="Enter salary" value="0">
                </div>
                <div class="form-group">
                  <label for="empOpeningBonus">Opening Bonus:</label>
                  <input type="number" name="empOpeningBonus" id="empOpeningBonus" class="form-control" placeholder="Enter salary" value="0">
                </div>
                <div class="form-group required">
                  <label for="empJoinDate">Employee Joining Data:</label>
                  <div class="input-group data">
                    <div class="input-group-addon">
                      <li class="fa fa-calendar"></li>
                    </div>
                    <input type="text" name="empJoinDate" id="empJoinDate" class="form-control pull-right datePicker" autocomplete="Off" required>
                  </div>
                </div>
                <div class="imageContainer">
                    <div class="form-group">
                        <label for="">Employee Photo: </label>
                        <div class="image_preview" style="width: 60%; margin: auto;">
                            <img style="margin: auto;" class="previewing" width="100%" height="auto" src="<?php echo full_website_address(); ?>/assets/images/defaultUserPic.png" />
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

            <div class="box-footer">
               <button type="submit" id="jqAjaxButton" class="btn btn-primary">Add Employee</button>
            </div>
        
          </form> <!-- Form End --> 

        </div> <!-- box-body -->
        
      </div> <!-- content container-fluid -->

    </section> <!-- Main content End tag -->
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

