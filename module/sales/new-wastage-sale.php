<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("Wastage Sale"); ?>
        </h1>
    </section>

    <style>

        .tableBodyScroll tbody td {
            padding: 6px 4px !important;
            vertical-align: middle !important;
        }

        .tableBodyScroll tbody {
            display: block;
            overflow: auto;
            height: 42vh;
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


        if(isset($_GET["action"]) and $_GET["action"] == "addWastageSale") {
            

            $tariffCharges = array_sum($_POST["tariffChargesAmount"]);
            $wastageSalesPaidAmount = empty($_POST["wastageSalePaidAmount"]) ? 0 : $_POST["wastageSalePaidAmount"];

            $insertWastageSale = easyInsert(
                "wastage_sale",
                array(
                    "wastage_sale_date"                     => $_POST["wastageSaleDate"],
                    "wastage_sale_reference"                => $_POST["wastageSaleReference"],
                    "wastage_sale_customer"                 => $_POST["wastageSaleCustomer"],
                    "wastage_sale_tariff_charges"           => $tariffCharges,
                    "wastage_sale_tariff_charges_details"   => serialize($_POST["tariffChargesName"]),
                    "wastage_sale_paid_amount"              => $wastageSalesPaidAmount,
                    "wastage_sale_note"                     => $_POST["wastageSaleNote"]
                ),
                array(),
                true
            );


            if( isset( $insertWastageSale["status"] ) and $insertWastageSale["status"] === "success" ) {

                $netTotal = 0;

                // Insert wastage sale items
                foreach( $_POST["wastageSaleItem"] as $key => $itemName ) {

                    $netTotal += $subtotal = $_POST["wastageSaleItemPrice"][$key] * $_POST["wastageSaleItemQnt"][$key];

                    easyInsert(
                        "wastage_sale_items",
                        array(
                            "wastage_sale_id"               => $insertWastageSale["last_insert_id"],
                            "wastage_sale_items_details"    => $itemName,
                            "wastage_sale_items_price"      => $_POST["wastageSaleItemPrice"][$key],
                            "wastage_sale_items_qnt"        => $_POST["wastageSaleItemQnt"][$key],
                            "wastage_sale_items_subtotal"   => $subtotal
                        )
                    );

                }

                $discount = calculateDiscount($netTotal, $_POST["wastageSaleDiscountValue"]);
                $grandTotall = ($netTotal - $discount) + $tariffCharges;

                // Update wastage sale with calcualted data
                easyUpdate(
                    "wastage_sale",
                    array(
                        "wastage_sale_total_amount"             => $netTotal,
                        "wastage_sale_discount"                 => $discount,
                        "wastage_sale_grand_total"              => $grandTotall,
                        "wastage_sale_due_amount"               => ($grandTotall > $_POST["wastageSalePaidAmount"]) ? $grandTotall - (int)$_POST["wastageSalePaidAmount"] : 0,
                        "wastage_sale_created_by"               => $_SESSION["uid"]
                    ),
                    array(
                        "wastage_sale_id"       => $insertWastageSale["last_insert_id"]
                    )
                );

                // Insert Wastage Sales Payment into received payments table
                if($wastageSalesPaidAmount > 0) {
                    easyInsert(
                        "received_payments",
                        array (
                            "received_payments_type"        => "Wastage Sales Payments",
                            "received_payments_datetime"    => $_POST["wastageSaleDate"] . date(" H:i:s"),
                            "received_payments_accounts"    => $_POST["wastageSaleAccounts"],
                            "received_payments_from"        => $_POST["wastageSaleCustomer"],
                            "received_payments_amount"      => $wastageSalesPaidAmount,
                            "received_payments_method"      => 'Cash',
                            "received_payments_add_by"      => $_SESSION["uid"],
                            "received_payments_details"     => $_POST["wastageSaleNote"]
                        )
                    );

                    // Update the account balance
                    updateAccountBalance($_POST["wastageSaleAccounts"]);
                    
                }

                $rdrTo = full_website_address() . "/sales/wastage-sale-list/?action=addWastageSale";
                redirect($rdrTo);

            }

      }

    ?>
    

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="box box-default">

            <div class="box-header with-border">
                <h3 class="box-title"><?= __("Add Wastage Sale"); ?></h3>
            </div> <!-- box box-default -->

            <div class="box-body">

                <!-- Form start -->
                <form method="post" id="wastageSaleForm" role="form" action="<?php echo full_website_address(); ?>/sales/new-wastage-sale/?action=addWastageSale" enctype="multipart/form-data">

                    <div class="row">


                        <div class="form-group col-sm-3 required">
                            <label for="wastageSaleDate"><?= __("Date:"); ?></label>
                            <div class="input-group data">
                                <div class="input-group-addon">
                                    <li class="fa fa-calendar"></li>
                                </div>
                                <input type="text" name="wastageSaleDate" id="wastageSaleDate" value="<?php echo date("Y-m-d"); ?>" class="form-control pull-right datePicker" required>
                            </div>
                        </div>
                        <div class="form-group col-sm-5 required">
                            <label for="wastageSaleCustomer" class="required"><?= __("Customer:"); ?></label>
                            <i data-toggle="tooltip" data-placement="right" title="From where the product is wastageSaleing from. Eg. Customers" class="fa fa-question-circle"></i>
                            <select name="wastageSaleCustomer" id="wastageSaleCustomer" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=customerList" style="width: 100%;" required>
                                <option value=""><?= __("Select Customer"); ?>....</option>
                            </select>
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="wastageSaleReference"><?= __("Reference:"); ?></label>
                            <input type="text" name="wastageSaleReference" id="wastageSaleReference" class="form-control">
                        </div>
                        

                        <!-- Full Column -->
                        <div class="col-sm-12">
                            <label for=""><?= __("Item Details"); ?></label>
                        </div>
                        <div class="col-sm-12">
                            <table id="productTable" class="tableBodyScroll table table-bordered table-striped table-hover">
                                <thead>
                                    <tr class="bg-primary">
                                        <th class="col-md-5 text-center"><?= __("Item Name and Details"); ?></th>
                                        <th class="col-md-2 text-center"><?= __("Quantity"); ?></th>
                                        <th class="col-md-2 text-center"><?= __("Price"); ?></th>
                                        <th class="text-center"><?= __("Subtotal"); ?></th>
                                        <th style="width: 30px; !important">
                                            <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td class="col-md-5"> <input type="text" name="wastageSaleItem[]" placeholder="<?= __("Enter Item name and details"); ?>" class="wastageSaleItem form-control" required> </td>
                                        <td class="col-md-2"> <input type="number" step="any" name="wastageSaleItemQnt[]" class="wastageSaleItemQnt form-control" > </td>
                                        <td class="col-md-2"> <input type="number" step="any" name="wastageSaleItemPrice[]" class="wastageSaleItemPrice form-control" required> </td>
                                        <td class="text-right wastageSaleItemSubtotal">0.00</td>
                                        <td style="width: 30px !important;"></td>
                                    </tr>
                                </tbody>

                                <tfoot>
                                </tfoot>
                            </table>


                            <div class="row">

                                <div class="col-md-6">
                                    <button type="button" id="addWastageSaleRow" class="btn btn-primary"><i class="fa fa-plus"></i> <?= __("Add Row"); ?></button>
                                    <button data-toggle="modal" data-target="#finalizeWastageSale" type="button" class="btn btn-primary pull-right"><i class="fa fa-shopping-cart"></i> <?= __("Payment"); ?></button>
                                </div>

                                <div class="col-md-6">

                                    <table style="margin-bottom: 0px;" class="table">
                                        <tr class="bg-info">
                                            <td><?= __("Items"); ?></td>
                                            <td id="totalItems" class="text-right">1(0)</td>
                                            <td></td>
                                            <td><?= __("Total"); ?></td>
                                            <td class="totalWastageSalePrice text-right">0.00</td>
                                            <td></td>
                                        </tr>
                                        <tr class="bg-info">
                                            <td><?= __("Tariff & Charges"); ?> <a data-toggle="modal" data-target="#wastageSaleTariffCharges" href="#"> <i class="fa fa-edit"></i> </a> </td>
                                            <td class="totalTariffCharges text-right">(+) 0.00</td>
                                            <td></td>
                                            <td><?= __("Discount"); ?> <a data-toggle="modal" data-target="#wastageSaleDiscount" href="#"> <i class="fa fa-edit"></i> </a> </td>
                                            <td class="totalWastageSaleDiscount text-right">(-) 0.00</td>
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

                        <div class="modal fade" id="wastageSaleDiscount">
                            <div class="modal-dialog ">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?= __("Wastage Sale Discount"); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="wastageSaleDiscountValue"><?= __("Wastage Sale Discount"); ?></label>
                                            <input type="text" name="wastageSaleDiscountValue" id="wastageSaleDiscountValue" class="form-control">
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
                        <div class="modal fade" id="finalizeWastageSale">
                            <div class="modal-dialog ">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?= __("Finalize Wastage Sale"); ?></h4>
                                    </div>
                                    <div class="modal-body">

                                        <div class="form-group row">
                                            <label class="col-md-3" for="wastageSaleNetTotal"><?= __("Net Total:"); ?></label>
                                            <div class="col-md-9">
                                                <input type="number" name="wastageSaleNetTotal" id="wastageSaleNetTotal" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-3" for="wastageSalePaidAmount"><?= __("Payment:"); ?></label>
                                            <div class="col-md-9">
                                                <input type="number" name="wastageSalePaidAmount" id="wastageSalePaidAmount" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-3" for="wastageSaleDue"><?= __("Due:"); ?></label>
                                            <div class="col-md-9">
                                                <input type="number" name="wastageSaleDue" id="wastageSaleDue" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-3" for="wastageSaleAccounts"><?= __("Accounts"); ?></label>
                                            <div class="col-md-9">
                                                <select name="wastageSaleAccounts" id="wastageSaleAccounts" class="form-control select2" style="width: 100%;">
                                                    <?php
                                                    $selectAccounts = easySelect("accounts", "accounts_id, accounts_name", array(), array("is_trash" => 0));
                                                    echo "<option value=''>Select accounts</option>";
                                                    foreach($selectAccounts["data"] as $accounts) {
                                                        echo "<option value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <small><?= __("In which account the payment will be stored."); ?></small>
                                                <br/>
                                            </div>
                                        </div>
    
                                        <div class="form-group row">
                                            <label class="col-md-3" for="wastageSaleNote"><?= __("Note:"); ?></label>
                                            <div class="col-md-9">
                                                <textarea name="wastageSaleNote" id="wastageSaleNote" cols="30" rows="3" class="form-control"></textarea>
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

                        <div class="modal fade" id="wastageSaleTariffCharges">
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

                    </div><!-- row-->  

                </form> <!-- Form End -->

            </div> <!-- box-body -->

        </div> <!-- content container-fluid -->

    </section> <!-- Main content End tag -->
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
    var wastageSalePageUrl = window.location.href;
</script>