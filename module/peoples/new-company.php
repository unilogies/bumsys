<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Companies
        <small>New Company</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Companies</a></li>
        <li class="active">New Company</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

      <div class="row">
        <!-- left column-->
        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Add new Company</h3>
            </div>
            <!-- Form start -->
            <form method="post" role="form" id="jqFormAdd" action="<?php echo full_website_address(); ?>/xhr/?module=peoples&page=newCompany">
              <div class="box-body">
                
                <div class="form-group required">
                  <label for="companyName">Company Name:</label>
                  <input type="text" name="companyName" id="companyName" class="form-control" required>
                </div>
                <div class="form-group required">
                  <label for="companyOpeningBalance">Opening Balance</label>
                  <input type="number" name="companyOpeningBalance" value="0" id="companyOpeningBalance" class="form-control" required>
                </div>
                <div class="form-group required">
                  <label for="companyType">Company Type</label>
                  <select name="companyType" id="companyType" class="form-control select2" required>
                    <option value="">Select one...</option>
                    <?php
                      $companyType = array('Manufacturer', 'Supplier', 'Vendor', 'Assembler', 'Binders', 'Others');
                      foreach($companyType as $companyType) {
                        echo "<option value='{$companyType}'>{$companyType}</option>";
                      }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="companyContactPerson">Contact Person Name:</label>
                  <input type="text" name="companyContactPerson" id="companyContactPerson" class="form-control">
                </div>
                <div class="form-group">
                  <label for="companyAddress">Company Address:</label>
                  <textarea name="companyAddress" id="companyAddress" rows="3" class="form-control"></textarea>
                </div>
                <div class="form-group">
                  <label for="companyCity">Company City:</label>
                  <input type="text" name="companyCity" id="companyCity" class="form-control">
                </div>
                <div class="form-group">
                  <label for="companyState">State:</label>
                  <input type="text" name="companyState" id="companyState" class="form-control">
                </div>
                <div class="form-group">
                  <label for="companyPostalCode">Postal Code:</label>
                  <input type="text" name="companyPostalCode" id="companyPostalCode" class="form-control">
                </div>
                <div class="form-group">
                  <label for="companyCountry">Country:</label>
                  <input type="text" name="companyCountry" id="companyCountry" class="form-control">
                </div>
                <div class="form-group">
                  <label for="companyPhone">Phone:</label>
                  <input type="text" name="companyPhone" id="companyPhone" class="form-control">
                </div>
                <div class="form-group">
                  <label for="companyEmail">Email:</label>
                  <input type="email" name="companyEmail" id="companyEmail" class="form-control">
                </div>
                <div class="form-group">
                  <label for="companyWebsite">Website:</label>
                  <input type="text" name="companyWebsite" id="companyWebsite" class="form-control">
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

