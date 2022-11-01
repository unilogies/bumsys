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

        <?php 

            if( $sale["sales_shop_id"] != $_SESSION["sid"] ) {
                
                echo _e("Sorry! You do not have permission to edit this sales.");

            } else {

        ?>

          <div class="row">
            
              <!-- Left Column -->
              <div class="col-md-6">
                  <form id="posSale" action="#">
                      <div class="box box-primary" style="margin-bottom: 0;">

                          <div class="box-body">

                              <div title="Select Customer" data-toggle="tooltip" class="form-group">
                                  <select style="width:100%" name="customers" id="customers" class="form-control select2Ajax"
                                      select2-create-new-url="<?php echo full_website_address(); ?>/xhr/?tooltip=true&module=peoples&page=newCustomer"
                                      select2-minimum-input-length="1"
                                      select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=customerList">
                                      <option value="<?php echo $sale["sales_customer_id"]; ?>"><?php echo $sale["customer_name"]; ?></option>
                                  </select>
                                  <input type="hidden" name="customersId" id="customersId" value="<?php echo $sale["sales_customer_id"]; ?>">
                              </div>
                              <div title="Select Warehouse" data-toggle="tooltip" class="form-group">
                                  <select disabled name="warehouse" id="warehouse" class="form-control select2">
                                      <?php
                                            $selectWarehouse = easySelectA(array(
                                                "table"     =>"warehouses",
                                                "fields"    => "warehouse_id, warehouse_name",
                                                "where"     => array(
                                                    "is_trash=0 and warehouse_shop" => $_SESSION["sid"]
                                                )
                                            ));
                                            foreach($selectWarehouse["data"] as $warehouse) {
                                                $selected = $sale['sales_warehouse_id'] == $warehouse['warehouse_id'] ? "selected" : "";
                                                echo "<option {$selected} value='{$warehouse['warehouse_id']}'>{$warehouse['warehouse_name']}</option>";
                                            }
                                        ?>
                                  </select>
                                  <input type="hidden" name="warehouseId" id="warehouseId" value="<?php echo $sale["sales_warehouse_id"]; ?>">
                                  <input type="hidden" id="editSalesId" name="salesId" value="<?php echo htmlentities($_GET["edit"]); ?>">
                                  <input type="hidden" name="userShopId" value="<?php echo $sale["sales_shop_id"]; ?>">
                              </div>
                              <div class="form-group">
                                  <div class="input-group">
                                    
                                        <div title="Search Product" data-toggle="tooltip">
                                            <select name="selectPosProduct" id="selectPosProduct"
                                                class="form-control pull-left select2Ajax" select2-minimum-input-length="1"
                                                select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=productListForPos&wid=<?php echo $_SESSION["wid"]; ?>"
                                                style="width: 100%;">
                                                <option value=""><?= __("Search Product"); ?>....</option>
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
                                          <th class="col-md-7 text-center"><?= __("Product"); ?></th>
                                          <th class="col-md-2 text-center"><?= __("Price"); ?></th>
                                          <th class="col-md-2 text-center"><?= __("Quantity"); ?></th>
                                          <th class="col-md-2 text-center"><?= __("Unit"); ?></th>
                                          <th class="col-md-3 text-center"><?= __("Subtotal"); ?></th>
                                          <th class="text-center" style="width: 25px !important;">
                                              <i class="fa fa-trash-o"
                                                  style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                          </th>
                                      </tr>
                                  </thead>

                                  <tbody>

                                    <?php 

                                        $selectSoldItems = easySelectA(array(
                                            "table"     => "product_stock",
                                            "fields"    => "stock_product_id, product_name, product_unit, stock_batch_id, has_expiry_date, product_generic, round(stock_item_price, 2) as stock_item_price, round(stock_item_qty, 2) as stock_item_qty,
                                                            round(stock_item_discount, 2) as stock_item_discount, round(stock_item_subtotal, 2) as stock_item_subtotal, stock_item_description",
                                            "join"      => array(
                                                "left join {$table_prefeix}products on product_id = stock_product_id"
                                            ),
                                            "where"     => array(
                                                "is_bundle_item = 0 and stock_sales_id"    => $_GET["edit"]
                                            )
                                        ))["data"];

                                        foreach($selectSoldItems as $key => $item) {

                                            $displayProductPrice = "";
                                            if( $item["stock_item_discount"] == 0 ) {
                                                $displayProductPrice = $item["stock_item_price"];
                                            } else {
                                                $displayProductPrice = "<span>" . number_format( $item["stock_item_price"] - $item["stock_item_discount"], 2) . "</span><span><del><small>". $item["stock_item_price"] ."</small></del></span>";
                                            }
                                            $rowId = time() . $item["stock_product_id"];
                                            $generic = $item['product_generic'] === null ? "" : '<small style="cursor: zoom-in;" onClick="BMS.PRODUCT.getListByGeneric(\''. $item['product_generic'] .'\')"><i>'. $item['product_generic'] .'</i></small>';
                                        ?>
                                                <tr id='<?php echo $rowId; ?>'> 
                                                    <td class="col-md-7">
                                                        <span data-toggle="modal" data-target="#modalDefault" href="<?php echo full_website_address(); ?>/xhr/?module=reports&page=totalPurchasedQuantityOfThisCustomer&cid=<?php echo $sale["sales_customer_id"]; ?>&pid=<?php echo $item["stock_product_id"]; ?>"> <i class="fa fa-info-circle productDescription"></i> </span> 
                                                        <a href="#" data-toggle="modal" onclick="BMS.POS.editProductItemDetails('<?php echo $rowId; ?>', '<?php echo $item['product_name']; ?>')" data-target="#productSaleDetails"><?php echo $item['product_name']; ?></a>
                                                        <?php echo $generic; ?>
                                                    </td> 
                                                    <td class="col-md-2 displayProductPrice text-right"><?php echo $displayProductPrice; ?></td> 
                                                    <td class="col-md-2"><input onclick = "this.select()" type="text" name="productQnt[]" value="<?php echo $item['stock_item_qty']; ?>" class="productQnt form-control text-center" autoComplete="off"></td>
                                                    <td class="col-md-2"><?php echo $item['product_unit']; ?></td> 
                                                    <td class="col-md-3 subtotalCol text-right"><?php echo $item['stock_item_subtotal']; ?></td> 
                                                    <td style="width: 25px !important;"> 
                                                        <i style="cursor: pointer;" class="fa fa-trash-o removeThisProduct"></i> 
                                                    </td> 
                                                    <input type="hidden" name="productID[]" class="productID" value="<?php echo $item['stock_product_id']; ?>"> 
                                                    <input type="hidden" class="netSalesPrice" name="productSalePirce[]" value="<?php echo $item['stock_item_price']; ?>"> 
                                                    <input type="hidden" class="productMainSalePirce" name="productMainSalePirce[]" value="<?php echo $item['stock_item_price']; ?>"> 
                                                    <input type="hidden" class="productSO" value="0"> 
                                                    <input type="hidden" name="productDiscount[]" value="<?php echo $item['stock_item_discount']; ?>" class="productDiscount" autoComplete="off"> 
                                                    <input type="hidden" name="productBatch[]" value="<?php echo $item['stock_batch_id']; ?>">
                                                    <input type="hidden" name="productHasExpiryDate[]" value="<?php echo $item['has_expiry_date']; ?>"> 
                                                    <input type="hidden" name="productPacket[]" value="0" class="productPacket"> 
                                                    <input type="hidden" name="productItemDetails[]" class="productItemDetails" value="<?php echo $item['stock_item_description']; ?>"> 
                                                </tr>
                                    <?php } ?>

                                  </tbody>

                              </table>

                          </div>

                          <div class="box-body">

                              <table style="width: 100%; background: #ecf0f5;" id="posCalculationTable">
                                  <tbody>
                                      <tr>
                                          <td class="col-md-3"><?= __("Items"); ?></td>
                                          <td class="text-right col-md-3 totalItemAndQnt">0(0)</td>
                                          <td class="col-md-3"><?= __("Total"); ?></td>
                                          <td class="text-right col-md-3 totalAmount"><?php echo number_format($sale["sales_total_amount"] - $sale["sales_product_discount"], 2); ?></td>
                                      </tr>
                                      <tr>
                                          <td><?= __("Tariff & Charges"); ?> <a data-toggle="modal"
                                                  data-target="#salesTariffCharges" href="#"> <i class="fa fa-edit"></i>
                                              </a> </td>
                                          <td class="text-right totalTariffChargesAmount">(+) <?php echo $sale["sales_tariff_charges"]; ?></td>
                                          <td><?= __("Discount"); ?> <a data-toggle="modal" data-target="#orderDiscount"
                                                  href="#"> <i class="fa fa-edit"></i> </a> </td>
                                          <td class="text-right totalOrderDiscountAmount">(-) <?php echo $sale["sales_discount"]; ?></td>
                                      </tr>
                                      <tr style="font-weight: bold; background: #333; color: #fff;">
                                          <td><?= __("Total Packet(s)"); ?></td>
                                          <td class="text-right displayTotalPackets"><?php echo $sale["sales_total_packets"]; ?></td>
                                          <td><?= __("Net Total"); ?></td>
                                          <td class="text-right netTotalAmount"><?php echo to_money($sale["sales_total_amount"] - ( $sale["sales_product_discount"] + $sale["sales_tariff_charges"] + $sale["sales_discount"] )) ; ?></td>
                                      </tr>

                                  </tbody>
                              </table>
                              <div class="post-action">

                                  <div style="float: left;" class="btn-group">
                                      <button type="button" onClick="BMS.POS.clearScreen()"
                                          class="btn btn-danger btn-block btn-flat"><?= __("Cancel"); ?></button>
                                  </div>

                                  <div style="float: right;" class="btn-group">
                                      <button data-toggle="modal" data-target="#payment" type="button"
                                          class="btn btn-success btn-block btn-flat"><i class="fa fa-money"></i>
                                          <?= __("Paymnet"); ?></button>
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
                                      <h4 class="modal-title"><?= __("Product Description"); ?></h4>
                                      <!-- Product name will display here -->
                                  </div>
                                  <div class="modal-body">
                                      <div class="form-group">
                                          <label for="productSaleItemPrice"><?= __("Price"); ?></label>
                                          <input type="text" id="productSaleItemPrice" class="form-control" value=""
                                              onclick="this.select()">
                                      </div>
                                      <div class="form-group">
                                          <label for="productSaleItemDiscount"><?= __("Discount"); ?></label>
                                          <input type="text" id="productSaleItemDiscount" class="form-control" value=""
                                              onclick="this.select()">
                                      </div>
                                      <div class="form-group">
                                          <label for="productSaleItemPacket"><?= __("Packet"); ?></label>
                                          <input type="text" id="productSaleItemPacket" class="form-control" value=""
                                              onclick="this.select()">
                                      </div>
                                      <div class="form-group">
                                          <label for="productSaleItemDetails"><?= __("Details"); ?></label>
                                          <textarea id="productSaleItemDetails" rows="3" class="form-control"
                                              placeholder="Eg: Product EMI or serial"></textarea>
                                      </div>
                                      <input type="hidden" class="rowId" value="">

                                  </div>
                                  <div class="modal-footer">
                                      <button type="button" class="btn btn-default pull-left"
                                          data-dismiss="modal"><?= __("Close"); ?></button>
                                      <button type="button" class="btn btn-primary"
                                          data-dismiss="modal"><?= __("Update"); ?></button>
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
                                      <h4 class="modal-title"><?= __("Tariff & Charges"); ?></h4>
                                  </div>
                                  <div class="modal-body">

                                      <div id="tariffCharges">

                                            <?php 

                                                $tariff = unserialize(html_entity_decode($sale["sales_tariff_charges_details"]));

                                                if(!empty($tariff["tariff"] )) {

                                                    foreach($tariff["tariff"] as $tariffKey => $tariffVal) {
                                                        echo '<div class="row">
                                                            <div class="col-md-7">
                                                                <select name="tariffChargesName[]"
                                                                    class="form-control select2Ajax tariffChargesName"
                                                                    select2-ajax-url="'. full_website_address() .'/info/?module=select2&page=tariffCharges">
                                                                    <option value="'. $tariffVal .'">'. $tariffVal .'</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="number" name="tariffChargesAmount[]"
                                                                    class="form-control tariffChargesAmount" value="'. $tariff["value"][$tariffKey] .'" step="any">
                                                            </div>
                                                            
                                                        </div>';
                                                    }

                                                } else {


                                                    echo '<div class="row">
                                                            <div class="col-md-7">
                                                                <select name="tariffChargesName[]"
                                                                    class="form-control select2Ajax tariffChargesName"
                                                                    select2-ajax-url="'. full_website_address() .'/info/?module=select2&page=tariffCharges">
                                                                    <option value="">'. __("Select Tariff/Charges") .'</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="number" name="tariffChargesAmount[]"
                                                                    class="form-control tariffChargesAmount" value="0" step="any">
                                                            </div>
                                                            
                                                        </div>';

                                                }

                                            ?>

                                      </div>

                                      <br/>
                                      <div class="text-center">
                                          <span style="cursor: pointer;" class="btn btn-primary"
                                              id="addTariffChargesRow">
                                              <i style="padding: 5px;" class="fa fa-plus-circle"></i>
                                          </span>
                                      </div>

                                  </div>

                                  <div class="modal-footer">
                                      <button type="button" class="btn btn-default pull-left"
                                          data-dismiss="modal"><?= __("Close"); ?></button>
                                      <button type="button" class="btn btn-primary"
                                          data-dismiss="modal"><?= __("Update"); ?></button>
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
                                      <h4 class="modal-title"><?= __("Order Discount"); ?></h4>
                                  </div>
                                  <div class="modal-body">
                                      <div class="form-group">
                                          <label for="orderDiscountValue"><?= __("Order Discount"); ?></label>
                                          <input type="text" name="orderDiscountValue" id="orderDiscountValue" value="<?php echo $sale["sales_discount"]; ?>" class="form-control">
                                      </div>
                                  </div>
                                  <div class="modal-footer">
                                      <button type="button" class="btn btn-default pull-left"
                                          data-dismiss="modal"><?= __("Close"); ?></button>
                                      <button type="button" class="btn btn-primary"
                                          data-dismiss="modal"><?= __("Update"); ?></button>
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
                                      <h4 class="modal-title"><?= __("Purchase List"); ?></h4>
                                  </div>

                                    <div class="modal-body">

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
                                      <h4 class="modal-title"><?= __("Finalize Sale"); ?></h4>
                                  </div>
                                  <div class="modal-body">

                                      <!-- Payment Details -->
                                      <div style="padding-left: 0 !important;" class="col-sm-10">
                                          <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="salesDate"><?php echo __("Sale Date:"); ?></label>
                                                    <input type="text" name="salesDate" value="<?php echo $sale["sales_delivery_date"]; ?>" id="salesDate" class="form-control datePicker">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="salesStatus"><?php echo __("Sale Status:"); ?></label>
                                                    <select name="salesStatus" id="salesStatus" class="form-control select2" style="width: 100%;">
                                                        <option value="">Select Status...</option>
                                                        <?php 
                                                            $saleStatus = array('Order Placed', 'In Production', 'Processing', 'Hold', 'Delivered', 'Cancelled');
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
                                                          <label for="salesIsWastage"><?= __("Sale is wastage"); ?></label>
                                                      </td>
                                                      <td class="text-right"><strong><?= __("Net Total"); ?></strong>
                                                      </td>
                                                      <td style="font-size: 20px; font-weight: bold;" class="text-right">
                                                        <?php echo to_money($sale["sales_total_amount"] - ( $sale["sales_product_discount"] + $sale["sales_tariff_charges"] + $sale["sales_discount"] )) ; ?>
                                                    </td>
                                                  </tr>
                                                  <tr>
                                                      <td colspan="2">
                                                          <div class="input-group">
                                                              <span
                                                                  class="input-group-addon"><?= __("Packet(s)"); ?></span>
                                                              <input type="text" name="totalPackets" id="totalPackets" value="<?php echo $sale["sales_total_packets"]; ?>" onclick="this.select()" class="form-control">
                                                              <span class="input-group-addon">@</span>
                                                              <input type="text" name="packetShippingRate"
                                                                  id="packetShippingRate" onclick="this.select()"
                                                                  value="75" class="form-control">
                                                              <span
                                                                  class="input-group-addon"><?= __("Tk. Rate"); ?></span>
                                                          </div>
                                                      </td>
                                                      <td class="text-right col-md-2" style="vertical-align: middle;">
                                                          <strong><?= __("Shipping"); ?></strong>
                                                      </td>
                                                      <td class="col-md-3">
                                                          <input type="number" name="shippingCharge" id="shippingCharge"
                                                              onclick="this.select()" value="<?php echo $sale["sales_shipping"]; ?>" class="form-control" step="any">
                                                      </td>
                                                  </tr>
                                                  <tr>
                                                      <td style="vertical-align: middle;" class="text-right"
                                                          colspan="3"><strong><?= __("Adjust Amount"); ?></strong></td>
                                                      <td class="text-right">
                                                          <input type="number" name="adjustAmount" id="adjustAmount"
                                                              class="form-control" value="<?php echo $sale["sales_adjustment"]; ?>" step="any">
                                                      </td>
                                                  </tr>
                                                  <tr>
                                                      <td class="text-right" colspan="3">
                                                          <strong><?= __("Grand Total"); ?></strong>
                                                      </td>
                                                      <td style="background-color: green; color: white; font-size: 20px; font-weight: bold; line-height: 1;" class="text-right">
                                                        <?php echo $sale["sales_grand_total"]; ?>
                                                    </td>
                                                  </tr>
                                                  <tr>
                                                      <td style="padding: 4px;" colspan="4">

                                                            <div class="paymentMethodBox">

                                                                <?php 

                                                                    $selectSalePayments = easySelectA(array(
                                                                        "table"     => "received_payments as received_payments",
                                                                        "fields"    => "received_payments_method, received_payments_accounts, accounts_name, round(received_payments_amount, 2) as received_payments_amount, received_payments_reference",
                                                                        "join"      => array(
                                                                            "left join {$table_prefeix}accounts on received_payments_accounts = accounts_id"
                                                                        ),
                                                                        "where"     => array(
                                                                            "received_payments.is_trash = 0 and received_payments_sales_id"    => $_GET["edit"]
                                                                        )
                                                                    ));

                                                                    // Check if their any paid amount row
                                                                    if($selectSalePayments !== false) {

                                                                        foreach($selectSalePayments["data"] as $key => $payment) {

                                                                            echo '<div style="margin: 0;" class="row">
                                                                                    <div class="form-group pull-right col-md-3 adjustFormGroupPadding">
                                                                                        <label>Amount</label>
                                                                                        <span style="cursor:pointer; padding: 0 4px;" class="pull-right removePosPaymentItem" ><i class="fa fa-times"></i></span>
                                                                                        <input type="number" onclick="this.select();" name="posSalePaymentAmount[]" value="'. $payment["received_payments_amount"] .'" class="form-control posSalePaymentAmount" step="any">
                                                                                    </div>
                                                                                    <div class="form-group pull-right col-md-3 adjustFormGroupPadding">
                                                                                        <label>Reference/Info</label>
                                                                                        <input type="text" name="posSalePaymentReference[]" value="'. $payment["received_payments_reference"] .'" class="form-control">
                                                                                    </div>
                                                                                    <div style="'. ( $payment["received_payments_method"] !== "Cash" ?: "display: none;" ) .'" class="posSalePaymentBankAccount form-group pull-right col-md-3 adjustFormGroupPadding">
                                                                                        <label>Bank Account</label>
                                                                                        <select name="posSalePaymentBankAccount[]" class="form-control pull-left select2Ajax" 
                                                                                            select2-ajax-url="'. full_website_address() .'/info/?module=select2&page=accountList" style="width: 100%;">
                                                                                            <option value="'. $payment["received_payments_accounts"] .'">'. $payment["accounts_name"] .'</option>
                                                                                            <option value="">'. __("Select Account") .'....</option>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="form-group pull-right col-md-3 adjustFormGroupPadding">
                                                                                        <label>Payment Method</label>
                                                                                        <select name="posSalePaymentMethod[]" class="posSalePaymentMethod form-control">
                                                                                            <option '. ( $payment["received_payments_method"] !== "Cash" ?: "selected" ) .' value="Cash">'. __("Cash") .'</option>
                                                                                            <option '. ( $payment["received_payments_method"] !== "Bank Transfer" ?: "selected" ) .' value="Bank Transfer">'. __("Bank Transfer") .'</option>
                                                                                            <option '. ( $payment["received_payments_method"] !== "Cheque" ?: "selected" ) .' value="Cheque">'. __("Cheque") .'</option>
                                                                                            <option '. ( $payment["received_payments_method"] !== "Card" ?: "selected" ) .' value="Card">'. __("Card") .'</option>
                                                                                            <option  '. ( $payment["received_payments_method"] !== "Others" ?: "selected" ) .'value="Others">'. __("Others") .'</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>';

                                                                        }

                                                                    } else {

                                                                        echo '<div style="margin: 0;" class="row">
                                                                                <div class="form-group pull-right col-md-3 adjustFormGroupPadding">
                                                                                    <label>Amount</label>
                                                                                    <input type="number" onclick="this.select();" name="posSalePaymentAmount[]" value="0" class="form-control posSalePaymentAmount" step="any">
                                                                                </div>
                                                                                <div class="form-group pull-right col-md-3 adjustFormGroupPadding">
                                                                                    <label>Reference/Info</label>
                                                                                    <input type="text" name="posSalePaymentReference[]" class="form-control">
                                                                                </div>
                                                                                <div style="display: none;" class="posSalePaymentBankAccount form-group pull-right col-md-3 adjustFormGroupPadding">
                                                                                    <label>Bank Account</label>
                                                                                    <select name="posSalePaymentBankAccount[]" class="form-control pull-left select2Ajax" 
                                                                                        select2-ajax-url="'. full_website_address() .'/info/?module=select2&page=accountList" style="width: 100%;">
                                                                                        <option value=""><?php echo __("Select Account"); ?>....</option>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="form-group pull-right col-md-3 adjustFormGroupPadding">
                                                                                    <label>Payment Method</label>
                                                                                    <select name="posSalePaymentMethod[]" class="posSalePaymentMethod form-control">
                                                                                        <option value="Cash">'. __("Cash") .'</option>
                                                                                        <option value="Bank Transfer">'. __("Bank Transfer") .'</option>
                                                                                        <option value="Cheque">'. __("Cheque") .'</option>
                                                                                        <option value="Card">'. __("Card") .'</option>
                                                                                        <option value="Others">'. __("Others") .'</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>';
                                                                    }

                                                                ?>

                                                                
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
                                                          <strong><?= __("Change/ Return"); ?></strong>
                                                      </td>
                                                      <td class="text-right"><?php echo $sale["sales_change"]; ?></td>
                                                  </tr>
                                                  <tr>
                                                      <td class="text-right" colspan="3">
                                                          <strong><?= __("Due"); ?></strong>
                                                      </td>
                                                      <td class="text-right"><?php echo $sale["sales_due"]; ?></td>
                                                  </tr>

                                              </tbody>
                                          </table>
                                      </div>

                                      <!-- Quick Cash -->
                                      <div class="col-sm-2 text-center">

                                          <h3
                                              style="font-size: 20px; font-weight: bold; margin: 0; padding: 0 0 2px 0;">
                                              <?= __("Quick Cash"); ?></h3>

                                          <div style="display: block;" class="btn-group btn-group-vertical">
                                              <button type="button" class="btn btn-lg btn-info quick-cash"
                                                  id="quickPayableAmount"><?php echo $sale["sales_grand_total"]; ?></button>
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

                                      <div class="form-group">
                                          <label for="salesNote"><?= __("Sale note:"); ?></label>
                                          <textarea name="salesNote" id="salesNote" rows="4"
                                              class="form-control"><?php echo $sale["sales_note"]; ?></textarea>
                                      </div>
                                      <div id="posErrorMsg"></div>

                                  </div>
                                  <div class="modal-footer">
                                      <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= __("Close"); ?></button>
                                      <button name="posAction" value="sale_is_confirmed" type="submit" class="posSubmit btn btn-success"><i class="fa fa-check-circle"></i> <?= __(" Update"); ?></button>                                      
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

              <div class="col-md-6">

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

        <?php } ?>

      </section> <!-- Main content End tag -->
      <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <script>
    // On load
    $(function() {

        // Load Product
        BMS.PRODUCT.showProduct();

    });

    var posPageUrl = window.location.href;
  </script>

