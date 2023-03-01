<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("Product Purchases Return"); ?>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Purchase</a></li>
            <li class="active">Purchase List</li>
        </ol>
    </section>

    <style>
        .btn-product {
            border: 1px solid #eee;
            cursor: pointer;
            height: 115px;
            width: 11.8%;
            margin: 0 0 3px 2px;
            padding: 2px;
            min-width: 98px;
            overflow: hidden;
            display: inline-block;
            font-size: 13px;
            background-color: white;
        }

        .btn-product span {
            display: table-cell;
            height: 45px;
            line-height: 15px;
            vertical-align: middle;
            text-transform: uppercase;
            width: 11.5%;
            min-width: 94px;
            overflow: hidden;
        }

        #productListContainer {
            max-height: 680px;
            overflow: auto;
        }

        .tableBodyScroll tbody td {
            padding: 6px 4px !important;
            vertical-align: middle !important;
        }

        .tableBodyScroll tbody {
            display: block;
            overflow: auto;
            height: 33.7vh;
        }

        .tableBodyScroll thead,
        .tableBodyScroll tbody tr {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .tableBodyScroll thead {
            width: calc(100% - 3px);
        }

        .tableBodyScroll tbody::-webkit-scrollbar {
            width: 4px;
        }

        .tableBodyScroll tbody::-webkit-scrollbar-track {
            -webkit-box-shadow: inset 0 0 6px #337ab7;
            border-radius: 10px;
        }

        .tableBodyScroll tbody::-webkit-scrollbar-thumb {
            border-radius: 10px;
            -webkit-box-shadow: inset 0 0 6px #337ab7;
        }
    </style>

    <?php 

      // Check if the biller is set
      if(!isset($_SESSION["aid"])) {
        
        echo _e("You must set you as a biller to add purchase return");

      } else if(isset($_GET["action"]) and $_GET["action"] == "addPurchaseReturn") {
          
        
        $purchaseData = $_POST;

        $productPurchasePrice = $purchaseData["productPurchasePrice"];
        $productDiscount = $purchaseData["productPurchaseDiscount"];
        $productQnt = $purchaseData["productQnt"];

        // Calculate the Product purchase Grand Total amount
        $PurchaseTotalAmount = 0;
        $purchaseTotalItemDiscount = 0;
        
        foreach($productPurchasePrice as $key => $value) {
          
          $value = empty($value) ? 0 : $value;
          $purchaseTotalItemDiscount += calculateDiscount($value, $productDiscount[$key]) * $productQnt[$key];
          
          $PurchaseTotalAmount += $productQnt[$key] * $value;

        }

        $totalPurchasePrice = $PurchaseTotalAmount - $purchaseTotalItemDiscount;

        $purchaseDiscount = calculateDiscount($totalPurchasePrice, $purchaseData["purchaseDiscountValue"]);

        $tariffCharges = array_sum($purchaseData["tariffChargesAmount"]);
        $shipping = empty($purchaseData["purchaseShipping"]) ? 0 : $purchaseData["purchaseShipping"];
        $grandTotal = ($totalPurchasePrice + $tariffCharges + $shipping) -  $purchaseDiscount;
        $paidAmount = empty($purchaseData["purchasePaidAmount"]) ? 0 : $purchaseData["purchasePaidAmount"];
        
        // Insert data into product_purchase table
        $insertPurchase = easyInsert(
          "product_purchase",
          array (
            "purchase_company_id"             => $purchaseData["purchaseCompany"],
            "purchase_is_returned"            => 1,
            "purchase_accounts_id"            => $_SESSION["aid"],
            "purchase_warehouse_id"           => $purchaseData["purchaseWarehouse"],
            "purchase_total_amount"           => $PurchaseTotalAmount,
            "purchase_item_total_discount"    => $purchaseTotalItemDiscount,
            "purchase_discount"               => $purchaseDiscount,
            "purchase_tariff_charges"         => $tariffCharges,
            "purchase_tariff_charges_details" => serialize($purchaseData["tariffChargesName"]),
            "purchase_shipping"               => $shipping,
            "purchase_grand_total"            => $grandTotal,
            "purchase_paid_amount"            => $paidAmount,
            "purchase_due_amount"             => $grandTotal > $paidAmount ? $grandTotal - $paidAmount : 0,
            "purchase_change_amount"          => $grandTotal < $paidAmount ? $paidAmount - $grandTotal : 0,
            "purchase_date"                   => $purchaseData["purchaseDate"],
            "purchase_reference"              => empty($purchaseData["purchaseReference"]) ? " " : $purchaseData["purchaseReference"],
            "purchase_description"            => $purchaseData["purchaseDescription"],
            "purchase_add_by"                 => $_SESSION["uid"]
          ),
          array(),
          true
        );

        // check if the purchase successfully inserted then got to next for adding purchase item
        if($insertPurchase["status"] === "success") {

            // Insert purchase item
            foreach($purchaseData["productID"] as $key => $productId) {

                // Calculate the discount
                $productPurchaseDiscount = calculateDiscount($productPurchasePrice[$key], $productDiscount[$key]);

                // Calculate the amount after discount
                $itemAmoutnAfterDiscount = $productPurchasePrice[$key] - $productPurchaseDiscount;

                $insertPurchaseItem = easyInsert(
                    "product_purchase_items",
                    array (
                        "purchase_item_purchase_id"   => $insertPurchase["last_insert_id"],
                        "purchase_item_is_returned"   => 1,
                        "purchase_item_product_id"    => $productId, 
                        "purchase_item_warehouse_id"  => $purchaseData["purchaseWarehouse"],
                        "purchase_item_quantity"      => $productQnt[$key],
                        "purchase_item_unit"          => $purchaseData["unitItem"][$key],
                        "purchase_item_product_price" => $productPurchasePrice[$key],
                        "purchase_product_discount"   => $productPurchaseDiscount,
                        "purchase_item_total_price"   => $productQnt[$key] * $itemAmoutnAfterDiscount // Calculate the items total amount
                    )
                );

                // Check if the product is bundle
                // Then insert bundle Products items
                if(product_type($productId)["is_bundle"]) {
            
                    // check if the bundle product sale price is changed by user
                    $increasedRate = "0%";
                    $decreasedRate = "0%";
                    if( $purchaseData["productPurchasePrice"][$key] > $purchaseData["productMainPurchasePrice"][$key] ) { // If the price is Increased 
            
                        // Calculate the increased amount
                        $increasedAmount = $purchaseData["productPurchasePrice"][$key] - $purchaseData["productMainPurchasePrice"][$key];
                        
                        // Calculate the increased purcentage
                        $increasedRate = ( $increasedAmount * 100 ) / $purchaseData["productMainPurchasePrice"][$key] ;
            
                    } else if( $purchaseData["productPurchasePrice"][$key] < $purchaseData["productMainPurchasePrice"][$key] ) { // If the price is decrased
            
                        // Calculate the decreased amount 
                        $decreasedAmount = $purchaseData["productMainPurchasePrice"][$key] - $purchaseData["productPurchasePrice"][$key];
                        
                        // Calculate the decreased purcentage
                        $decreasedRate = ( $decreasedAmount * 100 ) / $purchaseData["productMainPurchasePrice"][$key] ;
            
                    }

                    $selectBundleProducts = easySelectA(array(
                        "table"     => "bg_product_items as bg_product_items",
                        "fields"    => "bg_item_product_id, bg_product_unit_qnt, bg_product_unit, puv_purchase_price",
                        "join"      => array(
                            "left join {$table_prefix}product_unit_variants as puv on puv.puv_product_id = bg_item_product_id and puv.puv_name = bg_product_items.bg_product_unit"
                        ),
                        "where"     => array(
                            "bg_product_id" => $productId
                        )
                    ));

                    foreach($selectBundleProducts["data"] as $bpKey => $bp) {

                        // Store the Bundle Product Item Purchase Price
                        $bpItemPurchasePrice = $bp["puv_purchase_price"];

                        // Check if increased is not 0%
                        if( $increasedRate != "0%" ) {

                            // Increase the price if it was increased in Bundle price by user
                            $bpItemPurchasePrice += calculateDiscount($bpItemPurchasePrice, $increasedRate . "%");

                        } else if( $decreasedRate != "0%" ) {

                            // Decreased the price if it was increased in Bundle price by user
                            $bpItemPurchasePrice -= calculateDiscount($bpItemPurchasePrice, $decreasedRate  . "%");

                        } 


                        // Calculate the Bundle item quantity
                        $bpItemQnt = $purchaseData["productQnt"][$key] * $bp["bg_product_unit_qnt"];

                        // In bundle item, the discount takes from bundle product not from the item product
                        $bpItemDiscountAmount = calculateDiscount( $bpItemPurchasePrice, $purchaseData["productPurchaseDiscount"][$key] );

                        $bpItemSubTotal = ( $bpItemPurchasePrice - $bpItemDiscountAmount) * $bpItemQnt;

                        easyInsert(
                            "product_purchase_items",
                            array (
                                "purchase_item_purchase_id"   => $insertPurchase["last_insert_id"],
                                "purchase_item_is_returned"   => 1,
                                "purchase_item_product_id"    => $bp["bg_item_product_id"], 
                                "purchase_item_warehouse_id"  => $purchaseData["purchaseWarehouse"],
                                "purchase_item_quantity"      => $bpItemQnt,
                                "purchase_item_unit"          => $bp["bg_product_unit"],
                                "purchase_item_product_price" => $bpItemPurchasePrice,
                                "purchase_product_discount"   => $bpItemDiscountAmount,
                                "purchase_item_total_price"   => $bpItemSubTotal,
                                "is_bundle_item"              => 1
                            )
                        );

                    }

                } 

            }

            // if paid amount grater then zero in product purchase
            // then ad to expenses
            if($paidAmount > 0) {

                 // Payment reference for BILL
                $paymentReferences = payment_reference("bill");

                // Insert the Bill Payment
                $insertPurchasePayment = easyInsert (
                    "payments",
                    array (
                        "payment_date"              => $purchaseData["purchaseDate"],
                        "payment_to_company"        => $purchaseData["purchaseCompany"],
                        "payment_status"            => "Complete",
                        "payment_amount"            => $paidAmount,
                        "payment_from"              => $_SESSION["aid"],
                        "payment_description"       => "Payment Made on Product Purchase",
                        "payment_method"            => $purchaseData["purchasePaymentMethod"],
                        "payment_reference"         => $paymentReferences,
                        "payment_made_by"           => $_SESSION["uid"]
                    ),
                    array(),
                    true
                );

                if(isset($insertPurchasePayment["status"]) and $insertPurchasePayment["status"] === "success" ) {

                    // Insert payment items
                    easyInsert(
                        "payment_items",
                        array (
                            "payment_items_payments_id" => $insertPurchasePayment["last_insert_id"],
                            "payment_items_date"        => $purchaseData["purchaseDate"],
                            "payment_items_type"        => "Bill",
                            "payment_items_description" => "",
                            "payment_items_company"     => $purchaseData["purchaseCompany"],
                            "payment_items_amount"      => $paidAmount,
                            "payment_items_accounts"    => $_SESSION["aid"],
                            "payment_items_made_by"     => $_SESSION["uid"]
                        )
                    );
                    
                    // Update Accounts Balance
                    updateAccountBalance($_SESSION["aid"]);
  
                }

            }

            $rdrTo = full_website_address() . "/stock-management/purchase-return-list/?action=addPurchaseReturn";
            redirect($rdrTo);

        }

      }

    ?>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="box box-default">

            <div class="box-header with-border">
                <h3 class="box-title"><?= __("Add Purchase Return"); ?></h3>
            </div> <!-- box box-default -->

            <div class="box-body">

                <!-- Form start -->
                <form method="post" id="productPurchaseReturn" role="form" action="<?php echo full_website_address(); ?>/stock-management/new-purchase-return/?action=addPurchaseReturn" enctype="multipart/form-data">

                    <div class="row">


                        <div class="form-group col-sm-3 required">
                            <label for="purchaseDate"><?= __("Purchase Date:"); ?></label>
                            <div class="input-group data">
                                <div class="input-group-addon">
                                    <li class="fa fa-calendar"></li>
                                </div>
                                <input type="text" name="purchaseDate" id="purchaseDate" value="<?php echo date("Y-m-d"); ?>" class="form-control pull-right datePicker" required>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 required">
                            <label for="purchaseCompany" class="required"><?= __("Company:"); ?></label>
                            <i data-toggle="tooltip" data-placement="right" title="<?= __("From where the product is coming from. Eg. Supplier or Binders"); ?>" class="fa fa-question-circle"></i>
                            <select name="purchaseCompany" id="purchaseCompany" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=supplierBinderList" style="width: 100%;" required>
                                <option value=""><?= __("Select Company"); ?>....</option>
                            </select>
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="purchaseReference"><?= __("Reference:"); ?></label>
                            <input type="text" name="purchaseReference" id="purchaseReference" class="form-control">
                        </div>
                        
                        <div class="form-group col-sm-3">

                            <label for="purchaseWarehouse"><?= __("Warehouse:"); ?></label>
                            <select name="purchaseWarehouse" id="purchaseWarehouse" class="form-control" required>
                                <?php
                                    $selectWarehouse = easySelectA(array(
                                        "table"     =>"warehouses",
                                        "fields"    => "warehouse_id, warehouse_name",
                                        "where"     => array(
                                            "is_trash=0"
                                        )
                                    ));

                                    $warehouses = $selectWarehouse["data"];

                                    foreach($warehouses as $warehouse) {
                                    echo "<option value='{$warehouse['warehouse_id']}'>{$warehouse['warehouse_name']}</option>";
                                    }

                                ?>
                            </select>
                        </div>


                        <!-- Full Column -->
                        <div class="col-sm-12">
                            <label for=""><?= __("Product Details"); ?></label>
                        </div>
                        <div class="col-sm-12">
                            <table id="productTable" class="tableBodyScroll table table-bordered table-striped table-hover">
                                <thead>
                                    <tr class="bg-primary">
                                        <th class="col-md-4 text-center"><?= __("Product Name (Product Code)"); ?></th>
                                        <th class="col-md-1 text-center"><?= __("Quantity"); ?></th>
                                        <th style="width: 12%; !important" class="text-center"><?= __("Unit"); ?></th>
                                        <th class="col-md-1 text-center"><?= __("Price"); ?></th>
                                        <th class="col-md-1 text-center"><?= __("Discount"); ?></th>
                                        <th class="text-center"><?= __("Subtotal"); ?></th>
                                        <th style="width: 30px !important;">
                                            <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                </tbody>

                                <tfoot>
                                </tfoot>
                            </table>

                            <div class="row">

                                <div class="col-md-6">

                                    <div class="form-group">
                                        <div class="input-group">
                                            <select name="selectProduct" id="selectProduct" class="form-control pull-left select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=productList" style="width: 100%;">
                                                <option value=""><?= __("Select Product"); ?>....</option>
                                            </select>

                                            <div style="cursor: pointer;" class="input-group-addon btn-primary btn-hover" id="addProductButton">
                                                <i class="fa fa-plus-circle "></i>
                                            </div>

                                        </div>
                                    </div>
                                    <p class="text-center">-- <?= __("OR"); ?> --</p>
                                    <div class="text-center">
                                        <button data-toggle="modal" data-target="#browseProduct" type="button" class="btn btn-info"><i class="fa fa-folder-open"></i> <?= __("browse Product"); ?></button>
                                    </div>
                                    <br/>

                                    <!-- Browse Product Modal -->
                                    <div class="modal fade" id="browseProduct">
                                        <div class="modal-dialog modal-mdm">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title"><?= __("Browse Product"); ?> <small><?= __("Click on the product to add"); ?></small> </h4>
                                                </div>
                                                <div class="modal-body">

                                                    <!-- Product Filter -->
                                                    <div class="row">

                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <select name="productCategory" id="productCategory" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productCategoryList">
                                                                    <option value=""><?= __("All Category"); ?></option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <select name="productBrand" id="productBrand" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productBrandList">
                                                                    <option value=""><?= __("All Brand"); ?></option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <select style="width:100%;" name="productYears" id="productYears" class="form-control select2">
                                                                    <option value=""><?= __("All Year"); ?></option>
                                                                    <?php 

                                                                        $selectProductYear = easySelectA(array(
                                                                        "table"   => "products",
                                                                        "fields"  => "product_year",
                                                                        "groupby" => "product_year"
                                                                        ))["data"];
                                                                        
                                                                        foreach($selectProductYear as $key => $value) {
                                                                        echo "<option value='{$value['product_year']}'>{$value['product_year']}</option>";
                                                                        }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- /Product Filter -->

                                                    <div class="row box-body" id="productListContainer">
                                                        <!-- Here the products will be shown -->
                                                    </div>

                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= __("Close"); ?></button>
                                                </div>
                                            </div>
                                            <!-- /. Browse Product modal-content -->
                                        </div>
                                        <!-- /. Browse Product modal-dialog -->
                                    </div>
                                    <!-- /.Browse Product modal -->

                                </div>

                                <div class="col-md-6">

                                    <table style="margin-bottom: 0px;" class="table">
                                        <tr class="bg-info">
                                            <td><?= __("Items"); ?></td>
                                            <td id="totalItems" class="text-right">0(0)</td>
                                            <td></td>
                                            <td><?= __("Total"); ?></td>
                                            <td class="totalPurchasePrice text-right">0.00</td>
                                            <td></td>
                                        </tr>
                                        <tr class="bg-info">
                                            <td><?= __("Tariff & Charges"); ?> <a data-toggle="modal" data-target="#purchaseTariffCharges" href="#"> <i class="fa fa-edit"></i> </a> </td>
                                            <td class="totalTariffCharges text-right">(+) 0.00</td>
                                            <td></td>
                                            <td><?= __("Discount"); ?> <a data-toggle="modal" data-target="#purchaseDiscount" href="#"> <i class="fa fa-edit"></i> </a> </td>
                                            <td class="totalPurchaseDiscount text-right">(-) 0.00</td>
                                            <td></td>
                                        </tr>
                                        <tr style="font-weight: bold; background: #333; color: #fff;">
                                            <td colspan="3"><?= __("Net Total"); ?></td>
                                            <td colspan="2" class="netTotal text-right">0.00</td>
                                            <td></td>
                                        </tr>
                                    </table>
                                </div>

                            </div>

                        </div>
                        <!-- Full Column -->

                        <div class="modal fade" id="purchaseDiscount">
                            <div class="modal-dialog ">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?= __("Purchase Discount"); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="purchaseDiscountValue"><?= __("Purchase Discount"); ?></label>
                                            <input type="text" name="purchaseDiscountValue" id="purchaseDiscountValue" class="form-control">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= __("Close"); ?></button>
                                        <button type="button" class="btn btn-primary" data-dismiss="modal"><?= __("Update"); ?></button>
                                    </div>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                        <!-- /.modal -->

                        <div class="modal fade" id="purchaseTariffCharges">
                            <div class="modal-dialog ">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?= __("Tariff & Charges"); ?></h4>
                                    </div>
                                    <div class="modal-body">

                                        <div id="tariffCharges">

                                            <div class="row">
                                                <div class="col-md-7">
                                                    <select name="tariffChargesName[]" class="form-control select2Ajax tariffChargesName" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=tariffCharges">
                                                        <option value=""><?= __("Select Tariff/Charges"); ?></option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="number" name="tariffChargesAmount[]" class="form-control tariffChargesAmount" step="any">
                                                </div>
                                            </div>

                                        </div>

                                        <br/>
                                        <div class="text-center">
                                            <span style="cursor: pointer;" class="btn btn-primary" id="addTariffChargesRow">
                                                <i style="padding: 5px;" class="fa fa-plus-circle"></i>
                                            </span>
                                        </div>

                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= __("Close"); ?></button>
                                        <button type="button" class="btn btn-primary" data-dismiss="modal"><?= __("Update"); ?></button>
                                    </div>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                        <!-- /.modal -->

                        <!-- Modal -->
                        <div class="modal fade" id="finalizePurchase">
                            <div class="modal-dialog ">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?= __("Finalize Purchase"); ?></h4>
                                    </div>
                                    <div class="modal-body">

                                        <div class="form-group row">
                                            <label class="col-md-4" for="purchaseNetTotal"><?= __("Net Total"); ?></label>
                                            <div class="col-md-8">
                                                <input disabled type="number" name="purchaseNetTotal" id="purchaseNetTotal" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4" for="purchaseShipping"><?= __("Shipping"); ?></label>
                                            <div class="col-md-8">
                                                <input type="number" onclick="this.select();" name="purchaseShipping" id="purchaseShipping" class="form-control" value="0" step="any">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-4" for="purchaseGrandTotal"><?= __("Grand Total"); ?></label>
                                            <div class="col-md-8">
                                                <input disabled type="number" name="purchaseGrandTotal" id="purchaseGrandTotal" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4" for="purchasePaidAmount"><?= __("Paid Amount"); ?></label>
                                            <div class="col-md-8">
                                                <input type="number" onclick="this.select();" name="purchasePaidAmount" id="purchasePaidAmount" class="form-control" step="any">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4" for="purchasePaymentMethod"><?= __("Payment Method"); ?></label>
                                            <div class="col-md-8">
                                                <select name="purchasePaymentMethod" id="purchasePaymentMethod" class="form-control select2" style="width: 100%">
                                                    <?php
                                                        $paymentMethod = array("Cash", "Bank Transfer", "Cheque", "Others");
                                                        
                                                        foreach($paymentMethod as $method) {
                                                            echo "<option value='{$method}'>{$method}</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4" for="purchaseChangeAmount">Change</label>
                                            <div class="col-md-8">
                                                <input disabled type="number" name="purchaseChangeAmount" id="purchaseChangeAmount" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4" for="purchaseDueAmount">Due</label>
                                            <div class="col-md-8">
                                                <input disabled type="number" name="purchaseDueAmount" id="purchaseDueAmount" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4" for="purchaseDescription">Description</label>
                                            <div class="col-md-8">
                                                <textarea name="purchaseDescription" id="purchaseDescription" cols="30" rows="3" class="form-control"></textarea>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                                        <button id="purchaseSubmit" type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                        <!-- /.modal -->

                    </div><!-- row-->

                    <div class="box-footer">
                        <button data-toggle="modal" data-target="#finalizePurchase" type="button" class="btn btn-primary"><i class="fa fa-shopping-cart"></i> Add Purchase Return</button>
                    </div>

                </form> <!-- Form End -->

            </div> <!-- box-body -->

        </div> <!-- content container-fluid -->

    </section> <!-- Main content End tag -->
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>

   var purchasePageUrl = window.location.href;

   /* Browse Product while the modal open */
   $("#browseProduct").on("show.bs.modal", function(e) {

        BMS.PRODUCT.showProduct();

    });

</script>