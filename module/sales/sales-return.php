  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
  
    <section class="content-header">
      <h1>
        <?= __("Product Return"); ?>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Purchase</a></li>
        <li class="active">Return List</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
    
      <div class="row">
        <div class="col-xs-12">
          <div class="box">

          <div class="box-header">
              <h3 class="box-title"><?= __("Return List"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" width="100%">
                <thead>                
                  <tr>
                    <th></th>
                    <th class="sort text-center"><?= __("Date"); ?></th>
                    <th class="defaultOrder text-center"><?= __("Reference"); ?></th>
                    <th style="sort width: 180px!important;" class="text-center"><?= __("Customer"); ?></th>
                    <th class="sort countTotal text-center"><?= __("Total"); ?></th>
                    <th class="sort countTotal text-center"><?= __("Discount"); ?></th>
                    <th class="sort countTotal text-center"><?= __("Shipping"); ?></th>
                    <th class="countTotal text-center"><?= __("Surcharge"); ?></th>
                    <th class="sort countTotal text-center"><?= __("Grand Total"); ?></th>
                    <th class="sort countTotal text-center"><?= __("Paid Amount"); ?></th>
                    <th class="sort countTotal text-center"><?= __("Due"); ?></th>
                    <th class="no-sort countTotal text-center"><?= __("Cash In"); ?></th>
                    <th class="no-sort"><?= __("Sales Note"); ?></th>
                    <th class="sort text-center no-print"><?= __("Status"); ?></th>
                    <th class="no-sort text-right no-print" width="80px !important;"><?= __("Action"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                    <tr>
                        <th></th>
                        <th class="col-md-1 no-print"><input style="width: 160px" type="text" name="salesDate" id="salesDate" placeholder="<?php echo date("Y-m-d"); ?>" class="form-control input-sm" autocomplete="off"></th>
                        <th class="col-md-1 no-print"><input style="width: 120px" type="text" name="saleReference" id="saleReference" placeholder="<?= __("Reference Filter"); ?>" class="form-control input-sm"></th>
                        <th class="col-md-1 no-print"><input type="text" name="saleCustomer" id="saleCustomer" placeholder="<?= __("Customer Filter"); ?>" class="form-control input-sm"></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th><?= __("Sales Note"); ?></th>
                        <th class="col-md-1 no-print">
                        <select style="width: 120px" name="PaymentStatus" id="PaymentStatus" class="no-print form-control">
                            <option value=""><?= __("All"); ?></option>
                            <option value="paid"><?= __("Paid"); ?></option>
                            <option value="partial"><?= __("Partial"); ?></option>
                            <option value="due"><?= __("Due"); ?></option>
                        </select>
                        </th>
                        <th class="no-print"><?= __("Action"); ?></th>
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

    var scrollY = "50vh";
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=sales&page=salesProductReturnList";

    $('#purchaseDate').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true
    });

  </script>
