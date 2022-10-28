<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Vendors
        <small>New Vendor</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Vendors</a></li>
        <li class="active">New Vendor</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

      <div class="row">
        <!-- left column-->
        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Add new Vendor</h3>
            </div>
            <!-- Form start -->
            <form method="post" role="form" id="jqFormAdd" action="<?php echo full_website_address(); ?>/xhr/?module=peoples&page=newVendor">
              <div class="box-body">
                
                <div class="form-group">
                  <label for="vendorName">Vendor Name:</label>
                  <input type="text" name="vendorName" id="vendorName" class="form-control">
                </div>
                <div class="form-group">
                  <label for="vendorContactPerson">Contact Person Name:</label>
                  <input type="text" name="vendorContactPerson" id="vendorContactPerson" class="form-control">
                </div>
                <div class="form-group">
                  <label for="vendorAddress">Vendor Address:</label>
                  <textarea name="vendorAddress" id="vendorAddress" rows="3" class="form-control"></textarea>
                </div>
                <div class="form-group">
                  <label for="vendorCity">Vendor City:</label>
                  <input type="text" name="vendorCity" id="vendorCity" class="form-control">
                </div>
                <div class="form-group">
                  <label for="vendorState">State:</label>
                  <input type="text" name="vendorState" id="vendorState" class="form-control">
                </div>
                <div class="form-group">
                  <label for="vendorPostalCode">Postal Code:</label>
                  <input type="text" name="vendorPostalCode" id="vendorPostalCode" class="form-control">
                </div>
                <div class="form-group">
                  <label for="vendorCountry">Country:</label>
                  <input type="text" name="vendorCountry" id="vendorCountry" class="form-control">
                </div>
                <div class="form-group">
                  <label for="vendorPhone">Phone:</label>
                  <input type="text" name="vendorPhone" id="vendorPhone" class="form-control">
                </div>
                <div class="form-group">
                  <label for="vendorEmail">Email:</label>
                  <input type="email" name="vendorEmail" id="vendorEmail" class="form-control">
                </div>
                <div class="form-group">
                  <label for="vendorWebsite">Website:</label>
                  <input type="text" name="vendorWebsite" id="vendorWebsite" class="form-control">
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

