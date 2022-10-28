<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Peoples
        <small>New Biller</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Peoples</a></li>
        <li class="active">New Biller</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

      <div class="row">
        <!-- left column-->
        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Add new Biller</h3>
            </div>
            <!-- /Box header-->

            <!-- Form start -->
            <form method="post" role="form" id="jqFormAdd" action="<?php echo full_website_address(); ?>/xhr/?module=peoples&page=newBiller">
              <div class="box-body">
                <div class="form-group">
                  <label for="userId">User:</label>
                  <select name="userId" id="userId" class="form-control select2" style="width: 100%;">
                    <option value="">Select user....</option>
                    <?php 
                     // $selectUser = easySelect("users", "emp_id, emp_PIN, emp_firstname, emp_lastname");

                      $selectUsers = easySelect(
                        "users",
                        "user_id, emp_PIN, emp_firstname, emp_lastname",
                        array (
                          "left join {$table_prefeix}employees on user_emp_id = emp_id"
                        )
                      );

                      foreach($selectUsers["data"] as $users) {
                        echo "<option value='{$users['user_id']}'>{$users['emp_firstname']} {$users['emp_lastname']} ({$users['emp_PIN']})</option>";
                      }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="billerShop">Shop:</label>
                  <select name="billerShop" id="billerShop" class="form-control select2" style="width: 100%;">
                    <option value="">Select Shop....</option>
                    <?php 
                      $SelectShop = easySelect("shops", "shop_id, shop_name, shop_city, shop_state");
                      foreach($SelectShop["data"] as $shops) {
                        echo "<option value='{$shops['shop_id']}'>{$shops['shop_name']} ({$shops['shop_city']}, {$shops['shop_state']})</option>";
                      }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="billerAccounts">Accounts</label>
                  <select name="billerAccounts" id="billerAccounts" class="form-control select2" style="width: 100%;" required>
                    <option value="">Select accounts...</option>
                    <?php
                        $selectAccounts = easySelect("accounts", "accounts_id, accounts_name");
                        
                        foreach($selectAccounts["data"] as $accounts) {
                            echo "<option value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                        }
                    ?>
                  </select>
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

