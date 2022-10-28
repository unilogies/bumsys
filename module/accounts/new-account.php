<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Accounts
        <small>New Account</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Accounts</a></li>
        <li class="active">New Account</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

      <div class="row">
        <!-- left column-->
        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Add new Account</h3>
            </div>
            <!-- Form start -->
            <form method="post" role="form" id="jqFormAdd" action="<?php echo full_website_address(); ?>/xhr/?module=accounts&page=newAccount">
              <div class="box-body">
                
                <div class="form-group">
                  <label for="accountName">Account Name:</label>
                  <input type="text" name="accountName" id="accountName" class="form-control">
                </div>
                <div class="form-group">
                  <label for="accountType">Account Type:</label>
                  <select name="accountType" id="accountType" class="form-control">
                    <option value="Local (Cash)">Local (Cash)</option>
                    <option value="Bank (Savings)">Bank (Savings)</option>
                    <option value="Bank (Current)">Bank (Current)</option>
                    <option value="Card (Credit)">Card (Credit)</option>
                    <option value="Card (Debit)">Card (Debit)</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="accountCurrency">Currency:</label>
                  <select name="accountCurrency" id="accountCurrency" class="form-control">
                    <option value="BDT">BDT</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="openingBalance">Opening Balance:</label>
                  <input type="number" name="openingBalance" id="openingBalance" class="form-control">
                </div>
                <div class="form-group">
                  <label for="bankName">Bank Name:</label>
                  <input type="text" name="bankName" id="bankName" class="form-control">
                </div>
                <div class="form-group">
                  <label for="bankAccNumber">Bank Account Number:</label>
                  <input type="number" name="bankAccNumber" id="bankAccNumber" class="form-control">
                </div>
                <div class="form-group">
                  <label for="bankAccDetails">Bank Account Details:</label>
                  <textarea name="bankAccDetails" id="bankAccDetails" rows="3" class="form-control"></textarea>
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

