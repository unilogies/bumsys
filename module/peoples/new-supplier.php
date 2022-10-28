<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Suppliers
        <small>New Supplier</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Suppliers</a></li>
        <li class="active">New Supplier</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

      <div class="row">
        <!-- left column-->
        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Add new Supplier</h3>
            </div>
            <!-- Form start -->
            <form method="post" role="form" id="jqFormAdd" action="<?php echo full_website_address(); ?>/xhr/?module=peoples&page=newSupplier">
              <div class="box-body">
                
                <div class="form-group">
                  <label for="supplierName">Supplier Name:</label>
                  <input type="text" name="supplierName" id="supplierName" class="form-control">
                </div>
                <div class="form-group">
                  <label for="supplierAddress">Supplier Address:</label>
                  <textarea name="supplierAddress" id="supplierAddress" rows="3" class="form-control"></textarea>
                </div>
                <div class="form-group">
                  <label for="supplierCity">Supplier City:</label>
                  <input type="text" name="supplierCity" id="supplierCity" class="form-control">
                </div>
                <div class="form-group">
                  <label for="supplierState">State:</label>
                  <input type="text" name="supplierState" id="supplierState" class="form-control">
                </div>
                <div class="form-group">
                  <label for="supplierPostalCode">Postal Code:</label>
                  <input type="text" name="supplierPostalCode" id="supplierPostalCode" class="form-control">
                </div>
                <div class="form-group">
                  <label for="supplierCountry">Country:</label>
                  <input type="text" name="supplierCountry" id="supplierCountry" class="form-control">
                </div>
                <div class="form-group">
                  <label for="supplierPhone">Phone:</label>
                  <input type="text" name="supplierPhone" id="supplierPhone" class="form-control">
                </div>
                <div class="form-group">
                  <label for="supplierEmail">Email:</label>
                  <input type="email" name="supplierEmail" id="supplierEmail" class="form-control">
                </div>
                <div class="form-group">
                  <label for="supplierWebsite">Website:</label>
                  <input type="text" name="supplierWebsite" id="supplierWebsite" class="form-control">
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

