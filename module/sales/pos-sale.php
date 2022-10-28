  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?php echo __("Sales"); ?>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo __("POS sale List"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" style="width: 100%;">
                <thead>
                  <tr>
                    <th></th>
                    <th style="width: 80px;" class="text-center"><?php echo __("Date"); ?></th>
                    <th style="width: 160px;" class="text-center"><?php echo __("Shop"); ?></th>
                    <th class="defaultOrder text-center"><?php echo __("Reference"); ?></th>
                    <th style="sort width: 180px!important;" class="text-center"><?php echo __("Customer"); ?></th>
                    <th class="sort countTotal text-center"><?php echo __("Total"); ?></th>
                    <th class="sort countTotal text-center"><?php echo __("Discount"); ?></th>
                    <th class="sort countTotal text-center"><?php echo __("Shipping"); ?></th>
                    <th class="sort countTotal text-center"><?php echo __("Grand Total"); ?></th>
                    <th class="sort countTotal text-center"><?php echo __("Paid Amount"); ?></th>
                    <th class="sort countTotal text-center"><?php echo __("Due"); ?></th>
                    <th class="no-sort countTotal text-center"><?php echo __("Cash In"); ?></th>
                    <th class="no-sort"><?php echo __("Sales Note"); ?></th>
                    <th class="sort text-center no-print"><?php echo __("Status"); ?></th>
                    <th class="no-sort text-right no-print" width="80px !important;"><?php echo __("Action"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th class="col-md-1 no-print"><input style="width: 160px" type="text" name="salesDate" id="salesDate" placeholder="<?php echo date("Y-m-d"); ?>" class="form-control input-sm" autocomplete="off"></th>
                    <th>
                        <select id="purchaseShopFilter" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=shopList" style="width: 100%;">
                            <option value=""><?= __("Select Shop"); ?>....</option>
                        </select>
                    </th>
                    <th class="col-md-1 no-print"><input style="width: 120px" type="text" name="saleReference" id="saleReference" placeholder="<?php echo __("Reference Filter"); ?>" class="form-control input-sm"></th>
                    <th class="col-md-1 no-print"><input type="text" name="saleCustomer" id="saleCustomer" placeholder="<?php echo __("Customer Filter"); ?>" class="form-control input-sm"></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th><?php echo __("Sales Note"); ?></th>
                    <th class="col-md-1 no-print">
                      <select style="width: 120px" name="PaymentStatus" id="PaymentStatus" class="no-print form-control">
                        <option value=""><?php echo __("All"); ?></option>
                        <option value="paid"><?php echo __("Paid"); ?></option>
                        <option value="partial"><?php echo __("Partial"); ?></option>
                        <option value="due"><?php echo __("Due"); ?></option>
                      </select>
                    </th>
                    <th class="no-print"><?php echo __("Action"); ?></th>
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

    BMS.FUNCTIONS.dateRangePickerPreDefined({selector: "#salesDate"});

    /* Column Defference target column of data table */
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=sales&page=posSaleList";
    
  </script>
