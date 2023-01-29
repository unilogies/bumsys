  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("My Shop"); ?>
        <small><?= __("POS sale List"); ?></small>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?= __("POS sale List"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" style="width: 100%;">
                <thead>
                  <tr>
                    <th></th>
                    <th type="datePicker"
                        where-to-update="<?php echo full_website_address(); ?>/info/?module=data&page=updateInLine&tab=sales&p=sales_&f=delivery_date&t=id"
                    class="sort text-center px85"><?= __("Date"); ?></th>
                    <th class="defaultOrder text-center"><?= __("Reference"); ?></th>
                    <th type="select2" 
                        data-source="<?php echo full_website_address(); ?>/info/?module=select2&page=customerList"
                        where-to-update="<?php echo full_website_address(); ?>/info/?module=data&page=updateInLine&tab=sales&p=sales_&f=customer_id&t=id"
                        style="sort width: 180px!important;" class="text-center"><?= __("Customer"); ?>
                    </th>
                    <th class="sort text-center"><?= __("Phone Number"); ?></th>
                    <th class="sort countTotal text-center"><?= __("Total"); ?></th>
                    <th class="sort countTotal text-center"><?= __("Discount"); ?></th>
                    <th class="sort countTotal text-center"><?= __("Shipping"); ?></th>
                    <th class="sort countTotal text-center"><?= __("Grand Total"); ?></th>
                    <th class="sort countTotal text-center"><?= __("Paid Amount"); ?></th>
                    <th class="no-sort countTotal text-center"><?= __("Due"); ?></th>
                    <th class="no-sort countTotal text-center"><?= __("Cash In"); ?></th>
                    <th class="sort text-center no-print"><?= __("Payment"); ?></th>
                    <th type="select" 
                        data-options="Order Placed,In Production,Processing,Call not Picked,Confirmed,Hold,Delivered,Cancelled"
                        where-to-update="<?php echo full_website_address(); ?>/xhr/?module=my-shop&page=changeSaleStatus"
                        class="sort text-center no-print"><?= __("Status"); ?>
                    </th>
                    <th class="no-sort text-right no-print" width="80px !important;"><?= __("Action"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th class="col-md-1 no-print"><input style="width: 120px" type="text" name="salesDate" id="salesDate" placeholder="<?= __("Date Filter"); ?>" class="form-control input-sm" autoComplete="Off"></th>
                    <th class="col-md-1 no-print"><input style="width: 120px" type="text" name="saleReference" id="saleReference" placeholder="<?= __("Reference Filter"); ?>" class="form-control input-sm"></th>
                    <th class="col-md-1 no-print"><input type="text" name="saleCustomer" id="saleCustomer" placeholder="<?= __("Customer Filter"); ?>" class="form-control input-sm"></th>
                    <th class="sort text-center"><?= __("Phone Number"); ?></th>
                    <th><?= __("Total"); ?></th>
                    <th><?= __("Discount"); ?></th>
                    <th><?= __("Shipping"); ?></th>
                    <th><?= __("Grand Total"); ?></th>
                    <th><?= __("Paid Amount"); ?></th>
                    <th><?= __("Due"); ?></th>
                    <th><?= __("Chash In"); ?></th>
                    <th class="col-md-1 no-print">
                      <select style="width: 120px" name="PaymentStatus" id="PaymentStatus" class="no-print form-control">
                        <option value="">All...</option>
                        <option value="paid">Paid</option>
                        <option value="partial">Partial</option>
                        <option value="due">Due</option>
                      </select>
                    </th>
                    <th class="sort text-center no-print">
                        <select name="salesStatus" class="form-control">
                            <option value="">All...</option>
                            <?php 
                                $status = array('Order Placed', 'In Production', 'Processing', 'Call not Picked', 'Confirmed', 'Hold', 'Delivered', 'Cancelled');
                                foreach($status as $status) {
                                    echo "<option value'{$status}'>{$status}</option>";
                                }

                            ?>
                        </select>
                    </th>
                    <th class="no-print"><?= __("Action"); ?></th>
                  </tr>
                </tfoot>
              </table>
              <div class="love" id="love"></div>
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
    /* Column Defference target column of data table */
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=my-shop&page=posSaleList";

    BMS.FUNCTIONS.dateRangePickerPreDefined({selector: "#salesDate"});

  </script>
