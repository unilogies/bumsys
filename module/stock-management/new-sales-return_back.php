<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("Product Return"); ?>
        </h1>
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

        if(isset($_GET["action"]) and $_GET["action"] == "addReturn") {
            
        
            $selectReturnReference = easySelect(
                "product_returns",
                "product_returns_reference",
                array(),
                array (
                    "product_returns_reference LIKE 'RETURN/{$_SESSION['sid']}{$_SESSION['uid']}/%'",
                    " AND product_returns_reference is not null"
                ),
                array (
                    "product_returns_id" => "DESC"
                ),
                array (
                    "start" => 0,
                    "length" => 1
                )
            );
            
            // Referense Format: SALE/POS/n
            $returnReferences = "RETURN/".$_SESSION['sid'].$_SESSION['uid']."/";
            
            // check if there is minimum one records
            if($selectReturnReference) {
                $getLastReferenceNo = explode($returnReferences, $selectReturnReference["data"][0]["product_returns_reference"])[1];
                $returnReferences = $returnReferences . ($getLastReferenceNo+1);
            } else {
                $returnReferences = "RETURN/".$_SESSION['sid'].$_SESSION['uid']."/1";
            }
            
            
            $insertReturn = easyInsert(
                "product_returns",
                array (
                    "product_returns_date"              => $_POST["returnDate"],
                    "product_returns_pay_accounts"      => (isset($_POST["ReturnMoney"]) and !empty($_POST["returnAmountAccounts"])) ? $_POST["returnAmountAccounts"] : NULL,
                    "product_returns_warehouse_id"      => $_POST["returnWarehouse"],
                    "product_returns_reference"         => $returnReferences,
                    "product_returns_customer_id"       => $_POST["returnCustomer"],
                    "product_returns_products_quantity" => array_sum($_POST["productQnt"]),
                    "product_returns_note"              => $_POST["returnNote"],
                    "product_returns_shop_id"           => $_SESSION['sid'],
                    "product_returns_created_by"        => $_SESSION['uid'],
                    "product_returns_update_by"         => $_SESSION['uid']
                ),
                array(),
                true
            );
            

            $returnTotalAmount = 0;
            $returnTotalProductDiscount = 0;
            
            // Insert product items into sale table
            foreach($_POST["productID"] as $key => $productId) {
            
                // Calculate the total amount
                $returnTotalAmount += $itemReturnTotalAmount = $_POST["productReturnPrice"][$key] * $_POST["productQnt"][$key];
            
                // Calculate the product/items Discount
                $returnTotalProductDiscount += $itemDiscountAmount = calculateDiscount($_POST["productReturnPrice"][$key], $_POST["productReturnDiscount"][$key]) * $_POST["productQnt"][$key];
            
                $salesItemSubTotal = ( $_POST["productQnt"][$key] * $_POST["productReturnPrice"][$key] ) - $itemDiscountAmount;
            
                $insertReturnItems = easyInsert(
                    "product_return_items",
                    array (
                        "product_return_items_returns_id"       => $insertReturn["last_insert_id"],
                        "product_return_items_date"             => $_POST["returnDate"],
                        "product_return_items_customer_id"      => $_POST["returnCustomer"],
                        "product_return_items_warehouse_id"     => $_POST["returnWarehouse"],
                        "product_return_items_shop_id"          => $_SESSION['sid'],
                        "product_return_items_product_id"       => $productId,
                        "product_return_items_products_quantity"=> $_POST["productQnt"][$key],
                        "product_return_items_unit"             => $_POST["unitItem"][$key],
                        "product_return_items_sale_price"       => $_POST["productReturnPrice"][$key],
                        "product_return_items_total_amount"     => $itemReturnTotalAmount,
                        "product_return_items_discount"         => $itemDiscountAmount,
                        "product_return_items_grand_total"      => $salesItemSubTotal,
                        "product_return_items_created_by"       => $_SESSION['uid'],
                        "product_return_items_update_by"        => $_SESSION['uid']
                    )
                );

                // Check if the product is bundle
                // Then insert bundle Products items
                if(product_type($productId)["is_bundle"]) {

                    // check if the bundle product sale price is changed by user
                    $increasedRate = "0%";
                    $decreasedRate = "0%";
                    if( $_POST["productReturnPrice"][$key] > $_POST["productReturnMainPrice"][$key] ) { // If the price is Increased 
            
                        // Calculate the increased amount
                        $increasedAmount = $_POST["productReturnPrice"][$key] - $_POST["productReturnMainPrice"][$key];
                        
                        // Calculate the increased purcentage
                        $increasedRate = ( $increasedAmount * 100 ) / $_POST["productReturnMainPrice"][$key] ;
            
                    } else if( $_POST["productReturnPrice"][$key] < $_POST["productReturnMainPrice"][$key] ) { // If the price is decrased
            
                        // Calculate the decreased amount 
                        $decreasedAmount = $_POST["productReturnMainPrice"][$key] - $_POST["productReturnPrice"][$key];
                        
                        // Calculate the decreased purcentage
                        $decreasedRate = ( $decreasedAmount * 100 ) / $_POST["productReturnMainPrice"][$key] ;
            
                    }


                    $selectBundleProducts = easySelectA(array(
                        "table"     => "bg_product_items",
                        "where"     => array(
                            "bg_product_id" => $productId
                        )
                    ))["data"];


                    foreach($selectBundleProducts as $bpKey => $bp) {


                        // Store the Bundle Product Item Sale Price
                        $bpItemSalePrice = $bp["bg_product_unit_price"];

                        // Check if increased is not 0%
                        if( $increasedRate != "0%" ) {

                            // Increase the price if it was increased in Bundle price by user
                            $bpItemSalePrice += calculateDiscount($bpItemSalePrice, $increasedRate . "%");

                        } else if( $decreasedRate != "0%" ) {

                            // Decreased the price if it was increased in Bundle price by user
                            $bpItemSalePrice -= calculateDiscount($bpItemSalePrice, $decreasedRate  . "%");

                        } 


                        // Calculate the Bundle item quantity
                        $bpItemQnt = $_POST["productQnt"][$key] * $bp["bg_product_unit_qnt"];

                        // In bundle item, the discount takes from bundle product not from the item product
                        $bpItemDiscountAmount = calculateDiscount( $bpItemSalePrice, $_POST["productReturnDiscount"][$key] );

                        $bpItemSubTotal = ( $bpItemSalePrice - $bpItemDiscountAmount) * $bpItemQnt;
                        
                        easyInsert(
                            "product_return_items",
                            array (
                                "product_return_items_returns_id"       => $insertReturn["last_insert_id"],
                                "product_return_items_date"             => $_POST["returnDate"],
                                "product_return_items_customer_id"      => $_POST["returnCustomer"],
                                "product_return_items_warehouse_id"     => $_POST["returnWarehouse"],
                                "product_return_items_shop_id"          => $_SESSION['sid'],
                                "product_return_items_product_id"       => $bp["bg_item_product_id"],
                                "product_return_items_products_quantity"=> $bpItemQnt,
                                "product_return_items_unit"             => $bp["bg_product_unit"],
                                "product_return_items_sale_price"       => $bpItemSalePrice,
                                "product_return_items_total_amount"     => $bpItemSalePrice * $bpItemQnt,
                                "product_return_items_discount"         => $bpItemDiscountAmount,
                                "product_return_items_grand_total"      => $bpItemSubTotal,
                                "product_return_items_created_by"       => $_SESSION['uid'],
                                "product_return_items_update_by"        => $_SESSION['uid'],
                                "is_bundle_item"                        => 1
                            )
                        );

                    }

                } 
            
            }
            
            
            $surcharge = empty($_POST["returnSurcharge"]) ? 0 : $_POST["returnSurcharge"];
            
            // Calculate subtotal by minusing product discount
            $subtotal = $returnTotalAmount - $returnTotalProductDiscount;
            
            // Calculate order discount from subtotal
            $returnTotalDiscount = calculateDiscount($subtotal, $_POST["returnDiscountValue"]);
            
            // Calculate total amount after discount
            $returnAmoutnAfterDiscount = $subtotal - $returnTotalDiscount; 
            
            // Calculate Grand total by Adding shiping charge with net total
            $returnGrandTotal = $returnAmoutnAfterDiscount - $surcharge;
            //echo "Grand Total: $salesGrandTotal \n";
            
            
            // Update the Sale 
            $updateReturn = easyUpdate(
                "product_returns",
                array (
                    "product_returns_total_amount"      => $returnTotalAmount,
                    "product_returns_items_discount"    => $returnTotalProductDiscount,
                    "product_returns_total_discount"    => $returnTotalDiscount,
                    "product_returns_surcharge"         => $surcharge,
                    "product_returns_grand_total"       => $returnGrandTotal
                ),
                array (
                    "product_returns_id"  => $insertReturn["last_insert_id"]
                )
            );
            
            
            // Return the Success msg
            if($updateReturn === true and $insertReturn["status"] === "success") {
            
                // Update Customer Payment Info.  Will be deleted later
                // updateCustomerPaymentInfo($_POST["returnCustomer"]);
            
                // Update account Balance if return money is not empty
                if(isset($_POST["ReturnMoney"]) and !empty($_POST["returnAmountAccounts"]) ) {
                    updateAccountBalance($_POST["returnAmountAccounts"]);
                }

                $rdrTo = full_website_address() . "/invoice-print/?autoPrint=true&invoiceType=produtReturn&msg=Return successfully completed&id={$insertReturn['last_insert_id']}";
                redirect($rdrTo);
                
            }

      }

    ?>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="box box-default">

            <div class="box-header with-border">
                <h3 class="box-title"><?= __("Add Return"); ?></h3>
            </div> <!-- box box-default -->

            <div class="box-body">

                <!-- Form start -->
                <form method="post" id="productReturn" role="form" action="<?php echo full_website_address(); ?>/stock-management/new-sales-return/?action=addReturn" enctype="multipart/form-data">

                    <div class="row">

                        <div class="form-group col-sm-3 required">
                            <label for="returnDate"><?= __("Return Date:"); ?></label>
                            <div class="input-group data">
                                <div class="input-group-addon">
                                    <li class="fa fa-calendar"></li>
                                </div>
                                <input type="text" name="returnDate" id="returnDate" value="<?php echo date("Y-m-d"); ?>" class="form-control pull-right datePicker" required>
                            </div>
                        </div>
                        <div class="form-group col-sm-3 required">
                            <label for="returnCustomer" class="required"><?= __("Customer:"); ?></label>
                            <i data-toggle="tooltip" data-placement="right" title="From where the product is returning from. Eg. Customers" class="fa fa-question-circle"></i>
                            <select name="returnCustomer" id="returnCustomer" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=customerList" style="width: 100%;" required>
                                <option value=""><?= __("Select Customer"); ?>....</option>
                            </select>
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="returnReference"><?= __("Reference:"); ?></label>
                            <input type="text" name="returnReference" id="returnReference" class="form-control">
                        </div>
                        
                        <div class="form-group col-sm-3">

                            <label for="returnWarehouse"><?= __("Warehouse:"); ?></label>
                            <select name="returnWarehouse" id="returnWarehouse" class="form-control" required>
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
                            <label for="">Product Details</label>
                        </div>
                        <div class="col-sm-12">
                            <table id="productTable" class="tableBodyScroll table table-bordered table-striped table-hover">
                                <thead>
                                    <tr class="bg-primary">
                                        <th class="col-md-4 text-center"><?= __("Product Name and Details"); ?></th>
                                        <th class="col-md-2 text-center"><?php echo __("Batch No"); ?></th>
                                        <th class="col-md-1 text-center"><?php echo __("Quantity"); ?></th>
                                        <th class="col-md-1 text-center"><?php echo __("Unit"); ?></th>
                                        <th class="col-md-1 text-center"><?php echo __("Price"); ?></th>
                                        <th class="col-md-1 text-center"><?php echo __("Discount"); ?></th>
                                        <th class="text-center"><?php echo __("Subtotal"); ?></th>
                                        <th style="width: 30px; !important">
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
                                            <td class="totalReturnPrice text-right">0.00</td>
                                            <td></td>
                                        </tr>
                                        <tr class="bg-info">
                                            <td><?= __("Tariff & Charges"); ?> <a data-toggle="modal" data-target="#returnTariffCharges" href="#"> <i class="fa fa-edit"></i> </a> </td>
                                            <td class="totalTariffCharges text-right">(+) 0.00</td>
                                            <td></td>
                                            <td><?= __("Discount"); ?> <a data-toggle="modal" data-target="#returnDiscount" href="#"> <i class="fa fa-edit"></i> </a> </td>
                                            <td class="totalReturnDiscount text-right">(-) 0.00</td>
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

                        <div class="modal fade" id="returnDiscount">
                            <div class="modal-dialog ">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?= __("Return Discount"); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="returnDiscountValue"><?= __("Return Discount"); ?></label>
                                            <input type="text" name="returnDiscountValue" id="returnDiscountValue" class="form-control">
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

                        <div class="modal fade" id="returnTariffCharges">
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
                        <div class="modal fade" id="finalizeReturn">
                            <div class="modal-dialog ">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?= __("Finalize Return"); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group row">
                                            <label class="col-md-3" for="returnSurcharge"><?= __("Surcharge"); ?></label>
                                            <div class="col-md-9">
                                                <input type="number" onclick="this.select();" name="returnSurcharge" id="returnSurcharge" class="form-control" value="0" step="any">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-3" for="returnNote"><?= __("Return Note:"); ?></label>
                                            <div class="col-md-9">
                                                <textarea name="returnNote" id="returnNote" cols="30" rows="3" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="ReturnMoney">
                                            <input type="checkbox" name="ReturnMoney" id="ReturnMoney" value="true" class=""> <?= __("Return money"); ?>
                                            </label>
                                        </div>

                                        <div class="returnAmountAccountsDiv row" style="display: none;">
                                            <label class="col-md-3" for="returnAmountAccounts">Accounts</label>
                                            <div class="col-md-9">
                                                <select name="returnAmountAccounts" id="returnAmountAccounts" class="form-control select2" style="width: 100%;">
                                                    <?php
                                                    $selectAccounts = easySelect("accounts", "accounts_id, accounts_name", array(), array("is_trash" => 0));
                                                    echo "<option value=''>Select accounts</option>";
                                                    foreach($selectAccounts["data"] as $accounts) {
                                                        echo "<option value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <small><?= __("Which account the money will return from."); ?></small>
                                            </div>
                                        </div>


                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= __("Close"); ?></button>
                                        <button id="returnSubmit" type="submit" class="btn btn-primary"><?= __("Submit"); ?></button>
                                    </div>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                        <!-- /.modal -->

                    </div><!-- row-->

                    <div class="box-footer">
                        <button data-toggle="modal" data-target="#finalizeReturn" type="button" class="btn btn-primary"><i class="fa fa-shopping-cart"></i> <?= __("Return"); ?></button>
                    </div>

                </form> <!-- Form End -->

            </div> <!-- box-body -->

        </div> <!-- content container-fluid -->

    </section> <!-- Main content End tag -->
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>

    var productReturnPageUrl = window.location.href;

    /* Browse Product */
    $("#browseProduct").on("show.bs.modal", function(e) {

        BMS.PRODUCT.showProduct();
       
    });

</script>