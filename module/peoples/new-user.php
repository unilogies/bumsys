<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Peoples
        <small>New User</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Peoples</a></li>
        <li class="active">New User</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

      <div class="row">
        <!-- left column-->
        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Add new User</h3>
            </div>
            <!-- Form start -->
            <form method="post" role="form" id="jqFormAdd" action="<?php echo full_website_address(); ?>/xhr/?module=peoples&page=newUser">
              <div class="box-body">
                <div class="form-group">
                  <label for="employeeID" class="required">Employee:</label>
                  <select name="employeeID" id="employeeID" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=employeeList" style="width: 100%;">
                    <option value="">Select employee....</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="empGroup">User Group:</label>
                  <select name="empGroup" id="empGroup" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=empGroupList" style="width: 100%;">
                    <option value="">Select user group....</option>

                  </select>
                </div>
                <div class="form-group">
                  <label for="userPassword">User Password:</label>
                  <input type="password" name="userPassword" id="userPassword" class="form-control">
                </div>
                <div class="form-group">
                  <label for="confirmUserPassword">Confirm User Password:</label>
                  <input type="password" name="confirmUserPassword" id="confirmUserPassword" class="form-control">
                </div>
                      
              </div>
              <!-- /Box body-->
              <div class="box-footer">
                <button type="submit" id="jqAjaxButton" class="btn btn-primary">Submit</button>
              </div>
            </form>
            <!-- Form End -->
          </div>
          <!-- left column End-->
          
        </div>

      </div>

    </section> <!-- Main content End tag -->
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

