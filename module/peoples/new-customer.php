<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Customers
        <small>New Customer</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Customers</a></li>
        <li class="active">New Customer</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

      <div class="row">
        <!-- left column-->
        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Add new Customer</h3>
            </div>
            <!-- Form start -->
            <form method="post" role="form" id="jqFormAdd" action="<?php echo full_website_address(); ?>/xhr/?module=peoples&page=newCustomer">
              <div class="box-body">
                
                <div class="form-group required">
                  <label for="customerName">Customer Name:</label>
                  <input type="text" name="customerName" id="customerName" class="form-control" required>
                </div>
                <div class="form-group">
                  <label for="customerNameLocalLen">Customer Name in Local Language:</label>
                  <input type="text" name="customerNameLocalLen" id="customerNameLocalLen" value="" class="form-control">
                </div>
                <div class="form-group required">
                  <label for="customerOpeningBalance">Opening Balance</label>
                  <input type="number" name="customerOpeningBalance" value="0" id="customerOpeningBalance" class="form-control" required>
                </div>
                <div class="form-group required">
                  <label for="customerDistrict">Customer District:</label>
                  <select name="customerDistrict" id="customerDistrict" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=districtList" style="width: 100%;" required>
                    <option value="">Select district....</option>
                  </select>
                </div>
                <div class="form-group required">
                  <label for="customerDivision">Division::</label>
                  <select name="customerDivision" id="customerDivision" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=divisionList" style="width: 100%;" required>
                    <option value="">Select division....</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="customerPostalCode">Postal Code:</label>
                  <input type="text" name="customerPostalCode" id="customerPostalCode" class="form-control">
                </div>
                <div class="form-group">
                  <label for="customerCountry">Country:</label>
                  <select name="customerCountry" id="customerCountry" class="form-control">
                    <option value="Bangladesh">Bangladesh</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="customerAddress">Customer Address:</label>
                  <textarea name="customerAddress" id="customerAddress" rows="3" class="form-control"></textarea>
                </div>
                <div class="form-group required">
                  <label for="customerPhone">Phone:</label>
                  <input type="text" name="customerPhone" id="customerPhone" class="form-control" required>
                </div>
                <div class="form-group">
                  <label for="customerEmail">Email:</label>
                  <input type="email" name="customerEmail" id="customerEmail" class="form-control">
                </div>
                <div class="form-group">
                  <label for="customerWebsite">Website:</label>
                  <input type="text" name="customerWebsite" id="customerWebsite" class="form-control">
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

