  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Expenses"); ?>
        <a data-toggle="modal" data-target="#modalDefault" href="<?php echo full_website_address(); ?>/xhr/?tooltip=true&select2=true&module=my-shop&page=addShopExpense" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> <?= __("Add Expense"); ?></a>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Payments</a></li>
        <li class="active">Payment List</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
          
            <!-- Box header -->
            <div class="box-header">
              <h3 class="box-title"><?= __("Payments"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>

            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th><?= __("Date"); ?></th>
                    <th><?= __("Reference"); ?></th>
                    <th class="no-sort"><?= __("Paid to"); ?></th>
                    <th class="countTotal"><?= __("Amount"); ?></th>
                    <th><?= __("Description"); ?></th>
                    <th><?= __("Method"); ?></th>
                    <th class="no-sort no-print"><?= __("Action"); ?></th>
                  </tr>
                </thead>

                <tfoot>
                  <tr>
                    <th></th>
                    <!-- Payment Date -->
                    <th class="no-print col-md-1"><input style="width: 120px" type="text" placeholder="Select Date" name="paymentDate" id="paymentDate" class="form-control" autocomplete="off"></th>
                      
                    <th><?= __("Reference"); ?></th>
                    
                    <!-- Compnay or Employee -->
                    <th class="no-print col-md-2"><input type="text" name="paidTo" placeholder="Enter name or ID" id="paidTo" class="form-control"></th>
                    
                    <th><?= __("Amount"); ?></th>
                    <th class="no-print"><?= __("Description"); ?></th>
                    
                    <!-- Payment Method -->
                    <th class="no-print col-md-1">
                      <select style="width: 140px" name="paymentMethod" id="paymentMethod" class="form-control select2" style="width: 100%">
                        <option value=""><?= __("Method"); ?>...</option>
                        <?php
                            $paymentMethod = array("Cash", "Bank Transfer", "Cheque", "Others");
                            
                            foreach($paymentMethod as $method) {
                                echo "<option value='{$method}'>{$method}</option>";
                            }
                        ?>
                      </select>
                    </th>
                    <th>Action</th>

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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=my-shop&page=shopExpensesList";
    BMS.FUNCTIONS.datePicker({selector: "#paymentDate"});

  </script>
