  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">

      <?php 

        if(isset($_GET["action"]) and $_GET["action"] == "addPurchase") {
          // Show a success msg
          echo _s("Purchase has been successfully completed.");
        }

      ?>
      
  </section>

  
    <section class="content-header">
      <h1>
        <?= __("Purchases List"); ?>
        <a href="<?php echo full_website_address(); ?>/stock-management/new-purchase/" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> <?= __("Add New"); ?></a>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Purchase</a></li>
        <li class="active">Purchase List</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
    
      <div class="row">
        <div class="col-xs-12">
          <div class="box">

          <div class="box-header">
              <h3 class="box-title"><?= __("Purchase List"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-bordered table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th class="defaultOrder"><?php echo __("Date"); ?></th>
                    <th><?php echo __("Shop"); ?></th>
                    <th><?php echo __("Referense No"); ?></th>
                    <th><?php echo __("Supplier"); ?></th>
                    <th class="countTotal no-sort"><?php echo __("Total"); ?></th>
                    <th class="countTotal no-sort"><?php echo __("Discount"); ?></th>
                    <th class="countTotal no-sort"><?php echo __("Shipping"); ?></th>
                    <th class="countTotal no-sort"><?php echo __("Grand Total"); ?></th>
                    <th class="countTotal no-sort"><?php echo __("Paid Amount"); ?></th>
                    <th class="countTotal no-sort"><?php echo __("Due"); ?></th>
                    <th class="countTotal no-sort"><?php echo __("Cash Out"); ?></th>
                    <th class="no-sort"><?php echo __("Note"); ?></th>
                    <th class="no-sort"><?php echo __("Payment"); ?></th>                    
                    <th class="text-center no-sort no-print" width="100px"><?php echo __("Action"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th class="col-md-1 no-print"><input style="width: 160px" type="text" name="purchaseDate" id="purchaseDate" placeholder="<?php echo date("Y-m-d"); ?>" class="form-control input-sm" autocomplete="off"></th>
                    <th class="no-print">
                        <select id="purchaseShopFilter" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=shopList" style="width: 100%;">
                            <option value=""><?= __("Select Shop"); ?>....</option>
                        </select>
                    </th>
                    <th><?php echo __("Referense"); ?></th>
                    <th class="no-print">
                        <select id="purchaseSupplierFilter" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=supplierBinderList" style="width: 100%;" required>
                            <option value=""><?= __("Select Supplier"); ?>....</option>
                        </select>
                    </th>
                    <th><?php echo __("Total"); ?></th>
                    <th><?php echo __("Discount"); ?></th>
                    <th><?php echo __("Shipping"); ?></th>
                    <th><?php echo __("Grand Total"); ?></th>
                    <th><?php echo __("Paid Amount"); ?></th>
                    <th><?php echo __("Due"); ?></th>
                    <th><?php echo __("Cash Out"); ?></th>
                    <th><?php echo __("Note"); ?></th>
                    <th class="col-md-1 no-print">
                      <select style="width: 120px" name="PaymentStatus" id="PaymentStatus" class="no-print form-control">
                        <option value="">All</option>
                        <option value="paid">Paid</option>
                        <option value="partial">Partial</option>
                        <option value="due">Due</option>
                      </select>
                    </th>
                    <th><?php echo __("Action"); ?></th>
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
    
    BMS.FUNCTIONS.dateRangePickerPreDefined({selector: "#purchaseDate"});

    /* Column Defference target column of data table */
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=stock-management&page=productPurchaseList";

  </script>