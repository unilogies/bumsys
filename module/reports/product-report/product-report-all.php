  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?php echo __("Reports"); ?>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo  __("Product Reports"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" style="width: 100%;">
                <thead>
                  <tr>
                    <th></th>
                    <th style="width: 180px;"><?php echo __("Product Name"); ?></th>
                    <th style="width: 160px;"><?php echo __("Brand"); ?></th>
                    <th style="width: 120px;"><?php echo __("Category"); ?></th>
                    <th class="hideit" style="width: 110px;"><?php echo __("Edition"); ?></th>
                    <th class="no-sort countTotal"><?php echo __("Initial"); ?></th>
                    <th class="no-sort countTotal"><?php echo __("Production"); ?></th>
                    <th class="no-sort countTotal"><?php echo __("Purchased"); ?></th>
                    <th class="no-sort countTotal"><?php echo __("Purchased Return"); ?></th>
                    <th class="no-sort countTotal"><?php echo __("Stck.Trsfr.In"); ?></th>
                    <th class="no-sort countTotal"><?php echo __("Stck.Trsfr.Out"); ?></th>
                    <th class="countTotal"><?php echo __("Total Sold"); ?></th>
                    <th class=""><?php echo __("Sold In"); ?></th>
                    <th class="no-sort countTotal"><?php echo __("Sales Return"); ?></th>
                    <th class="hideit no-sort countTotal"><?php echo __("Specimen"); ?></th>
                    <th class="hideit no-sort countTotal"><?php echo __("Specimen Return"); ?></th>
                    <th class="no-sort countTotal"><?php echo __("Expired"); ?></th>
                    <th class="countTotal"><?php echo __("Stock"); ?></th>
                    <th class="no-sort"><?php echo __("Unit"); ?></th>
                    <th class="no-sort countTotal"><?php echo __("Stock Value"); ?></th>
                    <th class="no-sort countTotal"><?php echo __("Stock Balance"); ?></th>
                    <th class="no-sort countTotal"><?php echo __("Purchased"); ?></th>
                    <th class="no-sort countTotal"><?php echo __("Sold"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th>
                      <select style="width: 180px;" name="productReportWarehouseSelection" id="productReportWarehouseSelection" class="form-control select2 no-print" required>
                          <option value=""><?php echo __("All Warehouse"); ?>...</option>
                          <?php

                              $selectWarehouse = easySelectA(array(
                                  "table"     =>"warehouses",
                                  "fields"    => "warehouse_id, warehouse_name",
                                  "where"     => array(
                                      "is_trash=0"
                                  )
                              ));

                              if($selectWarehouse) {
                                
                                foreach($selectWarehouse["data"] as $warehouse) {
                                  echo "<option value='{$warehouse['warehouse_id']}'>{$warehouse['warehouse_name']}</option>";
                                }

                              }
                              
                          ?>
                      </select>
                    </th>
                    <th>
                      <select style="width: 180px;" name="productReportBrand" id="productReportBrand" class="form-control select2Ajax no-print" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productBrandList" style="width: 100%;">
                        <option value=""><?php echo __("Brand"); ?>...</option>
                      </select>
                    </th>
                    <th>
                      <select style="width: 180px;" name="productReportCategory" id="productReportCategory" class="form-control select2Ajax no-print" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productCategoryList" style="width: 100%;">
                        <option value=""><?php echo __("Category"); ?>...</option>
                      </select>
                    </th>
                    <th>
                      <select style="width: 160px;" name="productReportEdition" id="productReportEdition" class="form-control select2 no-print">
                        <option value=""><?php echo __("All Editions"); ?></option>
                        <?php 

                          $selectProductYear = easySelectA(array(
                            "table"   => "products",
                            "fields"  => "product_edition",
                            "groupby" => "product_edition"
                          ));
                          
                          if($selectProductYear) {
                            
                            foreach($selectProductYear["data"] as $key => $value) {
                              echo "<option value='{$value['product_edition']}'>{$value['product_edition']}</option>";
                            }

                          }
                          
                        ?>
                      </select>
                    </th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="no-print">
                        <textarea style="width: 65px; cursor:pointer; caret-color: transparent; white-space: break-spaces; font-size: 12px; padding: 0; overflow: hidden; resize: none;" name="soldInDateRange" id="soldInDateRange" class="btn btn-default"></textarea>
                    </th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
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
        BMS.FUNCTIONS.dateRangePickerPreDefined({
            selector: "#soldInDateRange", 
            ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
        });
        var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=reports&page=productReports";
  </script>