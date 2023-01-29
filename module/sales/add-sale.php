  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <style>
        .select2-container--default .select2-results > .select2-results__options {
            max-height: 400px;
        }
    </style>

    <script>
        var config = {
            posSaleAutoMarkAsPaid: '<?php echo get_options("posSaleAutoMarkAsPaid"); ?>',
            posSaleAutoAdjustAmount: '<?php echo get_options("posSaleAutoAdjustAmount"); ?>'
        };
    </script>

      <!-- Main content -->
      <section class="content container-fluid">

          <div class="row">

              <!-- Left Column -->
              <div class="col-md-7">
                  <form id="posSale" action="#">
                      <div class="box box-primary" style="margin-bottom: 0;">

                          <div class="box-body">

                              <div title="Select Customer" data-toggle="tooltip" class="form-group">
                                  <select style="width:100%" name="customers" id="customers" class="form-control select2Ajax"
                                      select2-create-new-url="<?php echo full_website_address(); ?>/xhr/?tooltip=true&module=peoples&page=newCustomer"
                                      select2-minimum-input-length="1"
                                      select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=customerList">
                                      <option value="1">Walk-in Customer</option>
                                  </select>
                                  <input type="hidden" name="customersId" id="customersId" value="">
                              </div>
                              <div title="Select Warehouse" data-toggle="tooltip" class="form-group">
                                  <select name="warehouse" id="warehouse" class="form-control select2">
                                      <?php
                                            $selectWarehouse = easySelectA(array(
                                                "table"     =>"warehouses",
                                                "fields"    => "warehouse_id, warehouse_name",
                                                "where"     => array(
                                                    "is_trash=0 and warehouse_shop" => $_SESSION["sid"]
                                                )
                                            ));
                                            foreach($selectWarehouse["data"] as $warehouse) {
                                                $selected = $_SESSION["wid"] == $warehouse['warehouse_id'] ? "selected" : "";
                                                echo "<option {$selected} value='{$warehouse['warehouse_id']}'>{$warehouse['warehouse_name']}</option>";
                                            }
                                        ?>
                                  </select>
                                  <input type="hidden" name="warehouseId" id="warehouseId" value="">
                                  <input type="hidden" name="userShopId" value="<?php echo $_SESSION["sid"]; ?>">
                              </div>
                              <div class="form-group">
                                  <div class="input-group">
                                    
                                        <div title="Search Product" data-toggle="tooltip">
                                            <select name="selectPosProduct" id="selectPosProduct"
                                                class="form-control pull-left select2Ajax" select2-minimum-input-length="1"
                                                select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=productListForPos&wid=<?php echo $_SESSION["wid"]; ?>"
                                                style="width: 100%;">
                                                <option value=""><?php echo __("Search Product, Enter or Scan Barcode"); ?>....</option>
                                            </select>
                                        </div>
                                      <div data-toggle="modal" data-target="#customerPurchaseList" style="cursor: pointer;" class="input-group-addon btn-primary btn-hover">
                                          <i title="Browse Customer Purchase" data-toggle="tooltip" class="fa fa-folder-open"></i>
                                      </div>

                                  </div>
                              </div>
                          </div> <!--  Box body-->

                          <!--  Product List-->
                          <div class="product-list">

                              <table id="productTable"
                                  class="tableBodyScroll table table-bordered table-striped table-hover">
                                  <thead>
                                      <tr class="bg-primary">
                                          <th class="col-md-7 text-center"><?php echo __("Product"); ?></th>
                                          <th class="col-md-2 text-center"><?php echo __("Price"); ?></th>
                                          <th class="col-md-2 text-center"><?php echo __("Quantity"); ?></th>
                                          <th class="col-md-2 text-center"><?php echo __("Unit"); ?></th>
                                          <th class="col-md-3 text-center"><?php echo __("Subtotal"); ?></th>
                                          <th class="text-center" style="width: 25px !important;">
                                              <i class="fa fa-trash-o"
                                                  style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                          </th>
                                      </tr>
                                  </thead>

                                  <tbody>

                                  </tbody>

                              </table>

                          </div>

                          <div class="box-body">

                              <table style="width: 100%; background: #ecf0f5;" id="posCalculationTable">
                                  <tbody>
                                      <tr>
                                          <td class="col-md-3"><?php echo __("Items"); ?></td>
                                          <td class="text-right col-md-3 totalItemAndQnt">0(0)</td>
                                          <td class="col-md-3"><?php echo __("Total"); ?></td>
                                          <td class="text-right col-md-3 totalAmount">0.00</td>
                                      </tr>
                                      <tr>
                                          <td><?php echo __("Tariff & Charges"); ?> <a data-toggle="modal"
                                                  data-target="#salesTariffCharges" href="#"> <i class="fa fa-edit"></i>
                                              </a> </td>
                                          <td class="text-right totalTariffChargesAmount">(+) 0.00</td>
                                          <td><?php echo __("Discount"); ?> <a data-toggle="modal" data-target="#orderDiscount"
                                                  href="#"> <i class="fa fa-edit"></i> </a> </td>
                                          <td class="text-right totalOrderDiscountAmount">(-) 0.00</td>
                                      </tr>
                                      <tr style="font-weight: bold; background: #333; color: #fff;">
                                          <td><?php echo __("Total Packet(s)"); ?></td>
                                          <td class="text-right displayTotalPackets">0</td>
                                          <td><?php echo __("Net Total"); ?></td>
                                          <td class="text-right netTotalAmount">0.00</td>
                                      </tr>

                                  </tbody>
                              </table>
                              <div class="post-action">

                                  <div style="float: left;" class="btn-group">
                                      <button type="button" onClick="BMS.POS.clearScreen()"
                                          class="btn btn-danger btn-block btn-flat"><?php echo __("Cancel"); ?></button>
                                  </div>

                                  <div style="float: right;" class="btn-group">
                                      <button data-toggle="modal" data-target="#payment" type="button"
                                          class="btn btn-success btn-block btn-flat"><i class="fa fa-money"></i>
                                          <?php echo __("Paymnet"); ?></button>
                                  </div>

                              </div>
                          </div>
                      </div>

                      <div class="modal fade" id="productSaleDetails">
                          <div class="modal-dialog ">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                          <span aria-hidden="true">&times;</span></button>
                                      <h4 class="modal-title"><?php echo __("Product Description"); ?></h4>
                                      <!-- Product name will display here -->
                                  </div>
                                  <div class="modal-body">
                                      <div class="form-group">
                                          <label for="productSaleItemPrice"><?php echo __("Price"); ?></label>
                                          <input <?php echo $_SESSION["allow_changing_price"] ?: "disabled"; ?> type="text" id="productSaleItemPrice" class="form-control" value=""
                                              onclick="this.select()">
                                      </div>
                                      <div class="form-group">
                                          <label for="productSaleItemDiscount"><?php echo __("Discount"); ?></label>
                                          <input <?php echo $_SESSION["allow_changing_price"] ?: "disabled"; ?> type="text" id="productSaleItemDiscount" class="form-control" value=""
                                              onclick="this.select()">
                                      </div>
                                      <div class="form-group">
                                          <label for="productSaleItemPacket"><?php echo __("Packet"); ?></label>
                                          <input type="text" id="productSaleItemPacket" class="form-control" value=""
                                              onclick="this.select()">
                                      </div>
                                      <div class="form-group">
                                          <label for="productSaleItemDetails"><?php echo __("Details"); ?></label>
                                          <textarea id="productSaleItemDetails" rows="3" class="form-control"
                                              placeholder="Eg: Product EMI or serial"></textarea>
                                      </div>
                                      <input type="hidden" class="rowId" value="">

                                  </div>
                                  <div class="modal-footer">
                                      <button type="button" class="btn btn-default pull-left"
                                          data-dismiss="modal"><?php echo __("Close"); ?></button>
                                      <button type="button" class="btn btn-primary"
                                          data-dismiss="modal"><?php echo __("Update"); ?></button>
                                  </div>
                              </div>
                              <!-- /.modal-content -->
                          </div>
                          <!-- /.modal-dialog -->
                      </div>
                      <!-- /.modal -->

                      <div class="modal fade" id="salesTariffCharges">
                          <div class="modal-dialog ">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                          <span aria-hidden="true">&times;</span></button>
                                      <h4 class="modal-title"><?php echo __("Tariff & Charges"); ?></h4>
                                  </div>
                                  <div class="modal-body">

                                      <div id="tariffCharges">

                                          <div class="row">
                                              <div class="col-md-7">
                                                  <select name="tariffChargesName[]"
                                                      class="form-control select2Ajax tariffChargesName"
                                                      select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=tariffCharges">
                                                      <option value=""><?php echo __("Select Tariff/Charges"); ?></option>
                                                  </select>
                                              </div>
                                              <div class="col-md-4">
                                                  <input type="number" name="tariffChargesAmount[]"
                                                      class="form-control tariffChargesAmount" step="any">
                                              </div>
                                          </div>

                                      </div>

                                      <br />
                                      <div class="text-center">
                                          <span style="cursor: pointer;" class="btn btn-primary"
                                              id="addTariffChargesRow">
                                              <i style="padding: 5px;" class="fa fa-plus-circle"></i>
                                          </span>
                                      </div>

                                  </div>

                                  <div class="modal-footer">
                                      <button type="button" class="btn btn-default pull-left"
                                          data-dismiss="modal"><?php echo __("Close"); ?></button>
                                      <button type="button" class="btn btn-primary"
                                          data-dismiss="modal"><?php echo __("Update"); ?></button>
                                  </div>
                              </div>
                              <!-- /.modal-content -->
                          </div>
                          <!-- /.modal-dialog -->
                      </div>
                      <!-- /.modal -->

                      <div class="modal fade" id="orderDiscount">
                          <div class="modal-dialog ">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                          <span aria-hidden="true">&times;</span></button>
                                      <h4 class="modal-title"><?php echo __("Order Discount"); ?></h4>
                                  </div>
                                  <div class="modal-body">
                                      <div class="form-group">
                                          <label for="orderDiscountValue"><?php echo __("Order Discount"); ?></label>
                                          <input type="text" name="orderDiscountValue" value="0" id="orderDiscountValue"
                                              class="form-control">
                                      </div>
                                  </div>
                                  <div class="modal-footer">
                                      <button type="button" class="btn btn-default pull-left"
                                          data-dismiss="modal"><?php echo __("Close"); ?></button>
                                      <button type="button" class="btn btn-primary"
                                          data-dismiss="modal"><?php echo __("Update"); ?></button>
                                  </div>
                              </div>
                              <!-- /.modal-content -->
                          </div>
                          <!-- /.modal-dialog -->
                      </div>
                      <!-- /.modal -->

                      <div class="modal right fade"  id="customerPurchaseList">
                          <div style="width: 350px;" class="modal-dialog ">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                          <span aria-hidden="true">&times;</span></button>
                                      <h4 class="modal-title"><?php echo __("Purchase List"); ?></h4>
                                  </div>

                                    <div class="modal-body">
                                        
                                        <input type="search" class="form-control searchInvoiceInPos" placeholder="Search Invoice & hit Enter">
                                        <br/>
                                        <div id="showPurchaseList">
                                                
                                            <ul class="products-list product-list-in-box">

                                            </ul>

                                        </div>
                                        
                                        <div style="display: none;" id="showPurchaseProductList">

                                            <input onClick="backToPurchaseList()" class="btn btn-primary" type="button" value="Back"><br/><br/>
                                            <div class="saleReferenceShow"></div>
                                            <hr>

                                            <ul class="products-list product-list-in-box">

                                            </ul>

                                        </div>
                                
                                    </div>
                              </div>
                              <!-- /.modal-content -->
                          </div>
                          <!-- /.modal-dialog -->
                      </div>
                      <!-- /.modal -->

                      <div class="modal fade" id="payment">
                          <div style="width: 820px;" class="modal-dialog ">
                              <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?php echo __("Finalize Sale"); ?></h4>
                                    </div>
                                    <div class="modal-body">

                                        <!-- Payment Details -->
                                        <div style="padding-left: 0 !important;" class="col-sm-10">
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="salesDate"><?php echo __("Sale Date:"); ?></label>
                                                    <input type="text" name="salesDate" value="<?php echo date("Y-m-d"); ?>" id="salesDate" class="form-control datePicker">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="salesStatus"><?php echo __("Sale Status:"); ?></label>
                                                    <select name="salesStatus" id="salesStatus" class="form-control select2" style="width: 100%;">
                                                        <option value="">Select Status...</option>
                                                        <?php 
                                                            $saleStatus = array('Order Placed', 'In Production', 'Processing', 'Confirmed', 'Hold', 'Delivered', 'Cancelled');
                                                            foreach($saleStatus as $status) {
                                                                $selected = $status === "Delivered" ? "selected" : "";
                                                                echo "<option {$selected} value='{$status}'>{$status}</option>";
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <table id="finalizeSale"
                                                class="table table-bordered table-striped table-hover">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="2">
                                                            <input type="radio" name="saleOptions" value="wastage"
                                                                id="salesIsWastage">
                                                            <label for="salesIsWastage"><?php echo __("Sale is wastage"); ?></label>
                                                        </td>
                                                        <td class="text-right"><strong><?php echo __("Net Total"); ?></strong>
                                                        </td>
                                                        <td style="font-size: 20px; font-weight: bold;" class="text-right">0.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2">
                                                            <div class="input-group">
                                                                <span
                                                                    class="input-group-addon"><?php echo __("Packet(s)"); ?></span>
                                                                <input type="text" name="totalPackets" id="totalPackets"
                                                                    onclick="this.select()" class="form-control">
                                                                <span class="input-group-addon">@</span>
                                                                <input type="text" name="packetShippingRate"
                                                                    id="packetShippingRate" onclick="this.select()"
                                                                    value="75" class="form-control">
                                                                <span
                                                                    class="input-group-addon"><?php echo __("Tk. Rate"); ?></span>
                                                            </div>
                                                        </td>
                                                        <td class="text-right col-md-2" style="vertical-align: middle;">
                                                            <strong><?php echo __("Shipping"); ?></strong>
                                                        </td>
                                                        <td class="col-md-3">
                                                            <input type="number" name="shippingCharge" id="shippingCharge"
                                                                onclick="this.select()" class="form-control" step="any">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="vertical-align: middle;" class="text-right"
                                                            colspan="3"><strong><?php echo __("Adjust Amount"); ?></strong></td>
                                                        <td class="text-right">
                                                            <input type="number" name="adjustAmount" id="adjustAmount"
                                                                class="form-control" step="any">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-right" colspan="3">
                                                            <strong><?php echo __("Grand Total"); ?></strong>
                                                        </td>
                                                        <td style="background-color: green; color: white; font-size: 20px; font-weight: bold; line-height: 1;" class="text-right">0.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 4px;" colspan="4">

                                                                <div class="paymentMethodBox">

                                                                    <div style="margin: 0;" class="row">
                                                                        <div class="form-group pull-right col-md-3 adjustFormGroupPadding">
                                                                            <label>Amount</label>
                                                                            <input type="number" onclick="this.select();" name="posSalePaymentAmount[]" class="form-control posSalePaymentAmount" step="any">
                                                                        </div>
                                                                        <div class="form-group pull-right col-md-3 adjustFormGroupPadding">
                                                                            <label>Reference/Info</label>
                                                                            <input type="text" name="posSalePaymentReference[]" class="form-control">
                                                                        </div>
                                                                        <div style="display: none;" class="posSalePaymentBankAccount form-group pull-right col-md-3 adjustFormGroupPadding">
                                                                            <label>Bank Account</label>
                                                                            <select name="posSalePaymentBankAccount[]" class="form-control pull-left select2Ajax" 
                                                                                select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=accountList" style="width: 100%;">
                                                                                <option value=""><?php echo __("Select Account"); ?>....</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group pull-right col-md-3 adjustFormGroupPadding">
                                                                            <label>Payment Method</label>
                                                                            <select name="posSalePaymentMethod[]" class="posSalePaymentMethod form-control">
                                                                                <option value="Cash"><?php echo __("Cash"); ?></option>
                                                                                <option value="Bank Transfer"><?php echo __("Bank Transfer"); ?></option>
                                                                                <option value="Cheque"><?php echo __("Cheque"); ?></option>
                                                                                <option value="Card"><?php echo __("Card"); ?></option>
                                                                                <option value="Others"><?php echo __("Others"); ?></option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                </div>

                                                                <div class="text-center">
                                                                    <div id="addPosSalePaymentRow" class="form-group btn btn-primary">
                                                                        <i class="fa fa-plus"></i>
                                                                    </div>
                                                                </div>
                                                    
                                                        </td>

                                                    </tr>
                                                    <tr>
                                                        <td class="text-right" colspan="3">
                                                            <strong><?php echo __("Change/ Return"); ?></strong>
                                                        </td>
                                                        <td class="text-right">0.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-right" colspan="3">
                                                            <strong><?php echo __("Due"); ?></strong>
                                                        </td>
                                                        <td class="text-right">0.00</td>
                                                    </tr>

                                                </tbody>
                                            </table>

                                        </div>

                                        <!-- Quick Cash -->
                                        <div class="col-sm-2 text-center">

                                            <h3
                                                style="font-size: 20px; font-weight: bold; margin: 0; padding: 0 0 2px 0;">
                                                <?php echo __("Quick Cash"); ?></h3>

                                            <div style="display: block;" class="btn-group btn-group-vertical">
                                                <button type="button" class="btn btn-lg btn-info quick-cash"
                                                    id="quickPayableAmount">0</button>
                                                <button type="button"
                                                    class="btn btn-md btn-warning quick-cash">10</button>
                                                <button type="button"
                                                    class="btn btn-md btn-warning quick-cash">50</button>
                                                <button type="button"
                                                    class="btn btn-md btn-warning quick-cash">100</button>
                                                <button type="button"
                                                    class="btn btn-md btn-warning quick-cash">500</button>
                                                <button type="button"
                                                    class="btn btn-md btn-warning quick-cash">1000</button>
                                                <button type="button"
                                                    class="btn btn-md btn-warning quick-cash">5000</button>
                                            </div>

                                        </div>

                                        <div class="clearfix"></div>
                                        <br/>
                                        <div class="form-group col-md-6">
                                            <label for="salesNote"><?php echo __("Sale note:"); ?></label>
                                            <textarea name="salesNote" id="salesNote" rows="4"
                                                class="form-control"></textarea>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="salesShipingAddress"><?php echo __("Shipping Address:"); ?></label>
                                            <textarea name="salesShipingAddress" id="salesShipingAddress" rows="4"
                                                class="form-control"></textarea>
                                        </div>
                                        <div id="posErrorMsg"></div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo __("Close"); ?></button>
                                        <button name="posAction" value="sale_is_confirmed" style="visibility: hidden;" type="submit"></button>
                                        <button name="posAction" value="sale_is_hold" type="submit" class="posSubmit btn btn-warning"><i class="fa fa-pause-circle"></i> <?php echo __("Hold"); ?></button>
                                        <button name="posAction" value="sale_is_confirmed" type="submit" class="posSubmit btn btn-success"><i class="fa fa-check-circle"></i> <?php echo __("Confirm"); ?></button>
                                    </div>
                                    
                              </div>
                              <!-- /.modal-content -->
                          </div>
                          <!-- /.modal-dialog -->
                      </div>
                      <!-- /.modal -->
                  </form>
              </div>
              <!-- /Left Column -->

              <!-- Right Column -->

              <div class="col-md-5">

                  <!-- Product Filter -->
                  <div class="row">


                      <?php load_product_filters(); ?>


                  </div>
                  <!-- /Product Filter -->

                  <div id="productListDiv" class="box box-success">

                      <div id="productListContainer" class="box-body">
                          <!-- Here the products will be showen -->

                      </div>

                  </div>

              </div>
              <!-- /Right Column -->

          </div>

      </section> <!-- Main content End tag -->
      <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <script>
        /** On load */
        $(function() {

            /** Load Product */
            BMS.PRODUCT.showProduct();

            /** Set the warehouse id and customer id to a hidden input filed */
            $("#customersId").val($("#customers").val());
            $("#warehouseId").val($("#warehouse").val());

        });

        var posPageUrl = window.location.href;
  </script>