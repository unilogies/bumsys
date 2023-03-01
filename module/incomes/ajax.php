<?php

// Select the Accounts
$selectAccounts = easySelect("accounts", "accounts_id, accounts_name", array(), array("is_trash" => 0));

/************************** Advance Collection **********************/
if(isset($_GET['page']) and $_GET['page'] == "advanceCollection") {
  
    // Include the modal header
    modal_header("Collect Advance", full_website_address() . "/xhr/?module=incomes&page=newAdvanceCollection");
    
    ?>
      <div class="box-body">
        <div class="form-group">
            <label for="advanceCollectionDate"><?= __("Date"); ?></label>
            <input type="text" name="advanceCollectionDate" id="advanceCollectionDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker">
        </div>
        <div class="form-group required">
            <label for="advanceCollectionFrom"><?= __("Customer"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="From which customer the advance has been collected." class="fa fa-question-circle"></i>
            <select name="advanceCollectionFrom" id="advanceCollectionFrom" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=customerList" style="width: 100%;" required>
                <option value=""><?= __("Select Customer"); ?>....</option>
            </select>
        </div>
        <div class="form-group required">
            <label for="advanceCollectionShop"><?= __("Shop"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="<?= __("Which shop belongs to of this advance collection"); ?>" class="fa fa-question-circle"></i>
            <select name="advanceCollectionShop" id="advanceCollectionShop" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=shopList" style="width: 100%;" required>
                <option value=""><?= __("Select Shop"); ?>....</option>
            </select>
        </div>
        <div class="form-group required">
            <label for="advanceCollectionAccounts"><?= __("Accounts"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="<?= __("In which accounts the advance will be added."); ?>" class="fa fa-question-circle"></i>
            <select name="advanceCollectionAccounts" id="advanceCollectionAccounts" class="form-control select2" style="width: 100%;" required>
                <?php
                    foreach($selectAccounts["data"] as $accounts) {
                        $selected = (isset($_SESSION['aid']) and $_SESSION['aid'] === $accounts['accounts_id']) ? "selected" : "";
                        echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group required">
            <label for="advanceCollectionAmount"><?= __("Amount"); ?></label>
            <input type="number" name="advanceCollectionAmount" id="advanceCollectionAmount" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="advanceCollectionBonus"><?= __("Bonus"); ?></label>
            <input type="number" name="advanceCollectionBonus" id="advanceCollectionBonus" class="form-control">
        </div>
        <div class="form-group">
            <label for="advanceDescription"><?= __("Description:"); ?></label>
            <textarea name="advanceDescription" id="advanceDescription" rows="3" class="form-control"></textarea>
        </div>

        <div class="form-group required">
            <label for="advancePaymentMethods"><?= __("Payment Method"); ?></label>
            <select name="advancePaymentMethods" id="advancePaymentMethods" class="form-control select2" style="width: 100%">
                <?php
                    $paymentMethod = array("Cash", "Bank Transfer", "Cheque", "Others");
                    
                    foreach($paymentMethod as $method) {
                        echo "<option value='{$method}'>{$method}</option>";
                    }
                ?>
            </select>
        </div>
        <div id="hiddenItem" style="display: none;">
            <div class="form-group">
                <label for="advancePaymentChequeNo"><?= __("Cheque No"); ?></label>
                <input type="text" name="advancePaymentChequeNo" id="advancePaymentChequeNo" class="form-control">
            </div>
            <div class="form-group">
                <label for="advancePaymentChequeDate"><?= __("Cheque Date:"); ?></label>
                <input type="text" name="advancePaymentChequeDate" id="advancePaymentChequeDate" value="" class="form-control datePicker">
            </div>
            <div class="form-group">
                <label for="advancePaymentReference"><?= __("Reference:"); ?></label>
                <input type="text" name="advancePaymentReference" id="advancePaymentReference" class="form-control">
            </div>
        </div>

        <div id="ajaxSubmitMsg"></div>


        <script>

            $(document).on("change", "#advancePaymentMethods", function() {
                if($("#advancePaymentMethods").val() == "Cheque") {
                    $("#hiddenItem").css("display", "block");
                } else {
                    $("#hiddenItem").css("display", "none");
                }
            });
        
        </script>
        

      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Add Advance");
}


/************************** Add Advance Collection **********************/
if(isset($_GET['page']) and $_GET['page'] == "newAdvanceCollection") {

    if(empty($_POST["advanceCollectionFrom"])) {
        return _e("Please select customer");
    } else if(empty($_POST["advanceCollectionAmount"])) {
        return _e("Please enter advance amount");
    }

    $advancedCollectionTime = $_POST["advanceCollectionDate"] . date(" H:i:s");

    // Insert Advance collection
    $inserAdvanceCollection = easyInsert(
        "received_payments",
        array (
            "received_payments_type"        => "Advance Collection",
            "received_payments_shop"        => $_POST["advanceCollectionShop"],
            "received_payments_accounts"    => $_POST["advanceCollectionAccounts"],
            "received_payments_from"        => $_POST["advanceCollectionFrom"],
            "received_payments_amount"      => $_POST["advanceCollectionAmount"],
            "received_payments_bonus"       => empty($_POST["advanceCollectionBonus"]) ? 0 : $_POST["advanceCollectionBonus"],
            "received_payments_details"     => $_POST["advanceDescription"],
            "received_payments_method"      => $_POST["advancePaymentMethods"],
            "received_payments_cheque_no"   => empty($_POST["advancePaymentChequeNo"]) ? NULL : $_POST["advancePaymentChequeNo"],
            "received_payments_cheque_date" => empty($_POST["advancePaymentChequeDate"]) ? NULL : $_POST["advancePaymentChequeDate"],
            "received_payments_reference"   => $_POST["advancePaymentReference"],
            "received_payments_datetime"    => $advancedCollectionTime,
            "received_payments_add_by"      => $_SESSION["uid"]
        ),
        array (
            "received_payments_shop"            => $_POST["advanceCollectionShop"],
            " AND received_payments_accounts"   => $_POST["advanceCollectionAccounts"],
            " AND received_payments_from"       => $_POST["advanceCollectionFrom"],
            " AND received_payments_amount"     => $_POST["advanceCollectionAmount"],
            " AND received_payments_type"       => "Advance Collection"
        ),
        true
    );

    if( isset($inserAdvanceCollection["status"]) and $inserAdvanceCollection["status"] === "success" ) {

        // Update Accounts Balance
        updateAccountBalance($_POST["advanceCollectionAccounts"]);

        $customerInfo = easySelectA(array(
            "table"     => "customers",
            "fileds"    => "customer_phone, send_notif",
            "where"     => array(
                "customer_id"   => $_POST["advanceCollectionFrom"]
            )
        ))["data"][0];


        $sucessMsg = sprintf(__("Advance successfully added. <a %s>Click Here</a> to print the receipt."), " onClick='BMS.MAIN.printPage(this.href, event);' href='". full_website_address() ."/invoice-print/?invoiceType=moneyReceipt&id=". $inserAdvanceCollection["last_insert_id"] ."'");
        
        echo "<div class='alert alert-success'>{$sucessMsg}</div>";

        // If Customer notification is enable then send it
        if( $customerInfo["send_notif"] ) {
            
            $customerNumber = $customerInfo["customer_phone"];
            $t = send_sms($customerNumber, "Dear sir, Tk " . number_format($_POST["advanceCollectionAmount"], 2) . " has been received successfully as on ". date("d M, Y", strtotime($_POST["advanceCollectionDate"]) ) . ". Thank You. \n-".get_options("companyName") );

        }


    } else {
        _e($inserAdvanceCollection);
    }
    
}



/*************************** Advance Collection List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "AdvanceCollectionList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "received_payments_datetime",
        "customer_name",
        "shop_name",
        "accounts_name",
        "received_payments_amount",
        "received_payments_bonus",
        "received_payments_details"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "received_payments",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0 and received_payments_type = 'Advance Collection'"
        )
    ))["data"][0]["totalRow"];
 
    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if(!empty($requestData["search"]["value"])) {  // get data with search
      
        $getData = easySelect(
            "received_payments as received_payment",
            "received_payments_id, customer_name, shop_name, accounts_name, received_payments_amount, received_payments_bonus, received_payments_details, received_payments_datetime, received_payments_cheque_no, received_payments_cheque_date, received_payments_reference, upazila_name, district_name",
            array (
                "left join {$table_prefix}customers on received_payments_from = customer_id",
                "left join {$table_prefix}upazilas on customer_upazila = upazila_id",
                "left join {$table_prefix}districts on customer_district = district_id",
                "left join {$table_prefix}shops on received_payments_shop = shop_id",
                "left join {$table_prefix}accounts on received_payments_accounts = accounts_id"
            ),
            array (
                "received_payment.is_trash = 0 and received_payments_type" => "Advance Collection",
                " AND (customer_name LIKE" => $requestData['search']['value'] . "%",
                " OR shop_name LIKE" => $requestData['search']['value'] . "%",
                " OR accounts_name LIKE" => $requestData['search']['value'] . "%",
                ")"
            ),
            array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        );
    
        $totalFilteredRecords = $getData ? $getData["count"] : 0;
  
    } else { // Get data withouth search
    
      $getData = easySelect(
            "received_payments as received_payment",
            "received_payments_id, customer_name, shop_name, accounts_name, received_payments_amount, received_payments_bonus, received_payments_details, received_payments_datetime, received_payments_cheque_no, received_payments_cheque_date, received_payments_reference, upazila_name, district_name",
            array (
                "left join {$table_prefix}customers on received_payments_from = customer_id",
                "left join {$table_prefix}upazilas on customer_upazila = upazila_id",
                "left join {$table_prefix}districts on customer_district = district_id",
                "left join {$table_prefix}shops on received_payments_shop = shop_id",
                "left join {$table_prefix}accounts on received_payments_accounts = accounts_id"
            ),
            array (
                "received_payment.is_trash = 0 and received_payments_type" => "Advance Collection"
            ),
            array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
      );
  
  } 

    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['count']) and $getData['count'] > 0) {
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = date("d M, Y h:i A", strtotime($value["received_payments_datetime"]));
            $allNestedData[] = "{$value["customer_name"]}, {$value["upazila_name"]}, {$value["district_name"]}";
            $allNestedData[] = $value["shop_name"];
            $allNestedData[] = $value["accounts_name"];
            $allNestedData[] = $value["received_payments_amount"];
            $allNestedData[] = $value["received_payments_bonus"];
            $allNestedData[] = "<strong>Description:</strong> {$value['received_payments_details']} <br/> <strong>Cheque No:</strong> {$value['received_payments_cheque_no']}; <strong>Cheque Date:</strong> {$value['received_payments_cheque_date']}";
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a onClick=\'BMS.MAIN.printPage(this.href, event);\' href="'. full_website_address() .'/invoice-print/?invoiceType=moneyReceipt&id='. $value["received_payments_id"] .'"><i class="fa fa-print"></i> Print Receipt</a></li>
                                    <li><a class="'. ( current_user_can("income_advance_collection.Edit") ? "" : "restricted " ) .'" data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=incomes&page=editAdvanceCollection&id='. $value["received_payments_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                    <li><a class="'. ( current_user_can("income_advance_collection.Delete") ? "" : "restricted " ) .'deleteEntry" href="'. full_website_address() . '/xhr/?module=incomes&page=deleteAdvanceCollection" data-to-be-deleted="'. $value["received_payments_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                    </ul>
                                </div>';
            
            $allData[] = $allNestedData;
        }
    }
    
  
    $jsonData = array (
        "draw"              => intval( $requestData['draw'] ),
        "recordsTotal"      => intval( $totalRecords ),
        "recordsFiltered"   => intval( $totalFilteredRecords ),
        "data"              => $allData
    );
    
    // Encode in Json Formate
    echo json_encode($jsonData); 
}


/***************** Delete Advance Collection ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteAdvanceCollection") {

    if(current_user_can("myshop_advance_collection.Delete") !== true) {
        echo '{
            "title": "Sorry!",
            "text": "'. __("you do not have permission to delete advance collection.") .'",
            "showConfirmButton": true,
            "showCloseButton": true,
            "toast": false,
            "icon": "error"
        }';
        return;
    }

    $selectAdvanceCollection = easySelectA(array(
        "table" => "received_payments",
        "where" => array(
            "received_payments_id" => $_POST["datatoDelete"]
        )
    ))["data"][0];

    $deleteData = easyDelete(
        "received_payments",
        array(
            "received_payments_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {

        // Update Customer Payment Info
        // updateCustomerPaymentInfo( $selectAdvanceCollection["received_payments_from"] );

        // Update Accounts Balance
        updateAccountBalance( $selectAdvanceCollection["received_payments_accounts"] );

        echo '{
            "title": "'. __("The entry has been deleted successfully.") .'"
        }';
    } 
}



/************************** Advance Collection **********************/
if(isset($_GET['page']) and $_GET['page'] == "editAdvanceCollection") {
  
    // Include the modal header
    modal_header("Edit Advance Collection", full_website_address() . "/xhr/?module=incomes&page=updateAdvanceCollection");
    
    $ac = easySelectA(array(
        "table" => "received_payments",
        "where" => array(
            "received_payments_id" => $_GET["id"]
        ),
        "join"  => array(
            "left join {$table_prefix}customers on received_payments_from = customer_id",
            "left join {$table_prefix}shops on received_payments_shop = shop_id"
        )
    ))["data"][0];

    ?>
      <div class="box-body">
        <div class="form-group">
            <label for="advanceCollectionDate"><?= __("Date"); ?></label>
            <input type="text" name="advanceCollectionDate" id="advanceCollectionDate" value="<?php echo date("Y-m-d", strtotime($ac["received_payments_datetime"]) ); ?>" class="form-control datePicker">
        </div>
        <div class="form-group required">
            <label for="advanceCollectionFrom"><?= __("Customer"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="<?= __("From which customer the advance has been collected."); ?>" class="fa fa-question-circle"></i>
            <select name="advanceCollectionFrom" id="advanceCollectionFrom" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=customerList" style="width: 100%;" required>
                <option value="<?= $ac["received_payments_from"]; ?>"><?= $ac["customer_name"]; ?></option>
            </select>
        </div>
        <div class="form-group required">
            <label for="advanceCollectionShop"><?= __("Shop"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="<?= __("Which shop belongs to of this advance collection"); ?>" class="fa fa-question-circle"></i>
            <select name="advanceCollectionShop" id="advanceCollectionShop" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=shopList" style="width: 100%;" required>
                <option value="<?= $ac["received_payments_shop"]; ?>"><?= $ac["shop_name"]; ?></option>
            </select>
        </div>
        <div class="form-group required">
            <label for="advanceCollectionAccounts"><?= __("Accounts"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="<?= __("In which accounts the advance will be added."); ?>" class="fa fa-question-circle"></i>
            <select name="advanceCollectionAccounts" id="advanceCollectionAccounts" class="form-control select2" style="width: 100%;" required>
                <?php
                    foreach($selectAccounts["data"] as $accounts) {
                        $selected = ( $ac["received_payments_accounts"] == $accounts['accounts_id'] ) ? "selected" : "";
                        echo "<option $selected value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group required">
            <label for="advanceCollectionAmount"><?= __("Amount"); ?></label>
            <input type="number" name="advanceCollectionAmount" id="advanceCollectionAmount" value="<?= number_format($ac["received_payments_amount"], 2, ".", ""); ?>" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="advanceCollectionBonus"><?= __("Bonus"); ?></label>
            <input type="number" name="advanceCollectionBonus" id="advanceCollectionBonus" value="<?= number_format($ac["received_payments_bonus"], 2, ".", ""); ?>" class="form-control">
        </div>
        <div class="form-group">
            <label for="advanceDescription"><?= __("Description:"); ?></label>
            <textarea name="advanceDescription" id="advanceDescription" rows="3" class="form-control"><?= $ac["received_payments_details"]; ?></textarea>
        </div>

        <div class="form-group required">
            <label for="advancePaymentMethods"><?= __("Payment Method"); ?></label>
            <select name="advancePaymentMethods" id="advancePaymentMethods" class="form-control select2" style="width: 100%">
                <?php
                    $paymentMethod = array("Cash", "Bank Transfer", "Cheque", "Others");
                    
                    foreach($paymentMethod as $method) {
                        $selected = ($ac["received_payments_method"] === $method) ? "selected" : "";
                        echo "<option $selected value='{$method}'>{$method}</option>";
                    }
                ?>
            </select>
        </div>
        <div id="hiddenItem" style="display: none;">
            <div class="form-group">
                <label for="advancePaymentChequeNo"><?= __("Cheque No"); ?></label>
                <input type="text" name="advancePaymentChequeNo" id="advancePaymentChequeNo" value="<?= $ac["received_payments_cheque_no"]; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="advancePaymentChequeDate"><?= __("Cheque Date:"); ?></label>
                <input type="text" name="advancePaymentChequeDate" id="advancePaymentChequeDate" value="<?= $ac["received_payments_cheque_date"]; ?>" class="form-control datePicker">
            </div>
            <div class="form-group">
                <label for="advancePaymentReference"><?= __("Reference:"); ?></label>
                <input type="text" name="advancePaymentReference" id="advancePaymentReference" value="<?= $ac["received_payments_reference"]; ?>" class="form-control">
            </div>
        </div>
        <input type="hidden" name="advanceCollectionId" value ="<?php echo safe_entities($_GET["id"]); ?>">

        <div id="ajaxSubmitMsg"></div>


        <script>

            $(function() {
                if($("#advancePaymentMethods").val() == "Cheque") {
                    $("#hiddenItem").css("display", "block");
                } else {
                    $("#hiddenItem").css("display", "none");
                }
            });

            $(document).on("change", "#advancePaymentMethods", function() {
                if($("#advancePaymentMethods").val() == "Cheque") {
                    $("#hiddenItem").css("display", "block");
                } else {
                    $("#hiddenItem").css("display", "none");
                }
            });
        
        </script>
        

      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Update");
}


/************************** update Advance Collection **********************/
if(isset($_GET['page']) and $_GET['page'] == "updateAdvanceCollection") {

    if(empty($_POST["advanceCollectionFrom"])) {
        return _e("Please select customer");
    } else if(empty($_POST["advanceCollectionAmount"])) {
        return _e("Please enter advance amount.");
    }

    // Select the previouse account
    $previousAccount = easySelectA(array(
        "table" => "received_payments",
        "fields" => "received_payments_accounts",
        "where" => array(
            "received_payments_id" => $_POST["advanceCollectionId"]
        )
    ))["data"][0]["received_payments_accounts"];

    // Insert Advance collection
    $updateAdvanceCollection = easyUpdate(
        "received_payments",
        array (
            "received_payments_shop"        => $_POST["advanceCollectionShop"],
            "received_payments_accounts"    => $_POST["advanceCollectionAccounts"],
            "received_payments_from"        => $_POST["advanceCollectionFrom"],
            "received_payments_amount"      => $_POST["advanceCollectionAmount"],
            "received_payments_bonus"       => empty($_POST["advanceCollectionBonus"]) ? 0 : $_POST["advanceCollectionBonus"],
            "received_payments_details"     => $_POST["advanceDescription"],
            "received_payments_method"      => $_POST["advancePaymentMethods"],
            "received_payments_cheque_no"   => empty($_POST["advancePaymentChequeNo"]) ? NULL : $_POST["advancePaymentChequeNo"],
            "received_payments_cheque_date" => empty($_POST["advancePaymentChequeDate"]) ? NULL : $_POST["advancePaymentChequeDate"],
            "received_payments_reference"   => $_POST["advancePaymentReference"],
            "received_payments_datetime"    => $_POST["advanceCollectionDate"]
        ),
        array (
            "received_payments_id"          => $_POST["advanceCollectionId"],
        )
    );

    if($updateAdvanceCollection === true) {

        // Update Customer Payment Info
        // This update might be not required. It will be check latter
        //updateCustomerPaymentInfo($_POST["advanceCollectionFrom"]);

        // Update Accounts Balance
        updateAccountBalance($_POST["advanceCollectionAccounts"]);

        // update account balance and customer payment info for if these are changed
        if( $_POST["advanceCollectionAccounts"] !== $previousAccount ) {
            
            // Update previouse Accounts Balance
            updateAccountBalance($previousAccount);

        }

        $sucessMsg = sprintf(__("Successfully updated. Please <a %s>Click Here</a> to print the receipt."), " onClick='BMS.MAIN.printPage(this.href, event);' href='". full_website_address() ."/invoice-print/?invoiceType=moneyReceipt&id=". safe_entities($_POST["advanceCollectionId"]) ."'");

        echo "<div class='alert alert-success'>{$sucessMsg}</div>";


    } else {
        _e($updateAdvanceCollection);
    }
    
}


/************************** Sales Received Payments **********************/
if(isset($_GET['page']) and $_GET['page'] == "addReceivedPayments") {
  
    // Include the modal header
    modal_header("New Received Payments", full_website_address() . "/xhr/?module=incomes&page=newReceivedPayments");
    
    ?>
      <div class="box-body">
        
        <div class="form-group required">
            <label for="receivedPaymentDate"><?= __("Date"); ?></label>
            <input type="text" name="receivedPaymentDate" id="receivedPaymentDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker">
        </div>
        <div class="form-group required">
            <label for="receivedPaymentsFrom"><?= __("Customer"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="From which customer the payment has been received." class="fa fa-question-circle"></i>
            <select name="receivedPaymentsFrom" id="receivedPaymentsFrom" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=customerList" style="width: 100%;" required>
                <option value=""><?= __("Select Customer"); ?>....</option>
            </select>
        </div>
        <div class="bg-info">
            <table id="companyPaymentInfo" class="table table-bordered table-striped table-hover">
                <tbody>
                    <tr>
                        <td class="text-right"><?= __("Total Sales:"); ?></td>
                        <td class="text-right">0.00</td>
                    </tr>
                    <tr>
                        <td class="text-right"><?= __("Shipping:"); ?></td>
                        <td class="text-right">0.00</td>
                    </tr>
                    <tr>
                        <td class="text-right"><?= __("Total Sales Paid:"); ?></td>
                        <td class="text-right">0.00</td>
                    </tr>
                    <tr>
                        <td class="text-right"><?= __("Total Sales Due:"); ?></td>
                        <td class="text-right">0.00</td>
                    </tr>
                    <tr>
                        <td class="text-right"><?= __("Total Received Payments:"); ?></td>
                        <td class="text-right">0.00</td>
                    </tr>
                    <tr>
                        <td class="text-right"><?= __("Total Given Bonus:"); ?></td>
                        <td class="text-right">0.00</td>
                    </tr>
                    <tr>
                        <td class="text-right"><?= __("Total Product Returns:"); ?></td>
                        <td class="text-right">0.00</td>
                    </tr>
                    <tr>
                        <td class="text-right"><?= __("Total Payments Returns:"); ?></td>
                        <td class="text-right">0.00</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;" class="bg-danger text-right"><?= __("Total Due"); ?></td>
                        <td style="font-weight: bold;" class="bg-danger text-right">0.00</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;" class="bg-success text-right"><?= __("Total Balance"); ?></td>
                        <td style="font-weight: bold;" class="bg-success text-right">0.00</td>
                    </tr>
                </tbody>
                
            </table>
        </div>
        <div class="form-group required">
            <label for="receivedPaymentsShop"><?= __("Shop"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="<?= __("Which shop belongs to of this received payments"); ?>" class="fa fa-question-circle"></i>
            <select name="receivedPaymentsShop" id="receivedPaymentsShop" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=shopList" style="width: 100%;" required>
                <option value=""><?= __("Select Shop"); ?>....</option>
            </select>
        </div>
        <div class="form-group required">
            <label for="receivedPaymentsAccounts">Accounts</label>
            <i data-toggle="tooltip" data-placement="right" title="<?= __("In which accounts the received payments will be added."); ?>" class="fa fa-question-circle"></i>
            <select name="receivedPaymentsAccounts" id="receivedPaymentsAccounts" class="form-control select2" style="width: 100%;" required>
                <?php
                    foreach($selectAccounts["data"] as $accounts) {
                        $selected = (isset($_SESSION['aid']) and $_SESSION['aid'] === $accounts['accounts_id']) ? "selected" : "";
                        echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group required">
            <label for="receivedPaymentsAmount"><?= __("Amount"); ?></label>
            <input type="number" name="receivedPaymentsAmount" id="receivedPaymentsAmount" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="receivedPaymentsDescription"><?= __("Description:"); ?></label>
            <textarea name="receivedPaymentsDescription" id="receivedPaymentsDescription" rows="3" class="form-control"></textarea>
        </div>

        <div class="form-group required">
            <label for="receivedPaymentsMethods"><?= __("Payment Method"); ?></label>
            <select name="receivedPaymentsMethods" id="receivedPaymentsMethods" class="form-control select2" style="width: 100%">
                <?php
                    $paymentMethod = array("Cash", "Bank Transfer", "Cheque", "Others");
                    
                    foreach($paymentMethod as $method) {
                        echo "<option value='{$method}'>{$method}</option>";
                    }
                ?>
            </select>
        </div>
        <div id="hiddenItem" style="display: none;">
            <div class="form-group">
                <label for="receivedPaymentsChequeNo"><?= __("Cheque No"); ?></label>
                <input type="text" name="receivedPaymentsChequeNo" id="receivedPaymentsChequeNo" class="form-control">
            </div>
            <div class="form-group">
                <label for="receivedPaymentsChequeDate"><?= __("Cheque Date:"); ?></label>
                <input type="text" name="receivedPaymentsChequeDate" id="receivedPaymentsChequeDate" value="" class="form-control datePicker">
            </div>
            <div class="form-group">
                <label for="receivedPaymentsReference"><?= __("Reference:"); ?></label>
                <input type="text" name="receivedPaymentsReference" id="receivedPaymentsReference" class="form-control">
            </div>
        </div>
        <div id="ajaxSubmitMsg"></div>

        <script>
        

            $(document).on("change", "#receivedPaymentsMethods", function() {
                if($("#receivedPaymentsMethods").val() == "Cheque") {
                    $("#hiddenItem").css("display", "block");
                } else {
                    $("#hiddenItem").css("display", "none");
                }
            });

            $(document).on("change", "#receivedPaymentsFrom", function(event) {
                
                event.stopImmediatePropagation();
                                
                $.post(
                    "<?php echo full_website_address(); ?>/info/?module=data&page=getCustomerPaymentInfo",
                    {
                        customerId: $("#receivedPaymentsFrom").val()
                    },
                    

                    function(data, status) {

                        /* Parse Json Data */
                        var paymentsData = JSON.parse(data);

                        var totalSalesPaid = Number(paymentsData.sales_grand_total) - Number(paymentsData.sales_due);
                        var totalDue = ( Number(paymentsData.sales_grand_total) + Number(paymentsData.total_payment_return) ) - (Number(paymentsData.total_received_payments) + Number(paymentsData.total_given_bonus) + Number(paymentsData.returns_grand_total) + (Number(paymentsData.customer_opening_balance)) );
                        var totalBalance = 0;

                        if(totalDue < 1) {
                            totalDue = 0;
                            totalBalance = (Number(paymentsData.total_received_payments) + Number(paymentsData.total_given_bonus) + Number(paymentsData.returns_grand_total) + (Number(paymentsData.customer_opening_balance)) ) - ( Number(paymentsData.sales_grand_total) + Number(paymentsData.total_payment_return) );
                        }
                        
                        /* Display the payment and sale data */
                        $("#companyPaymentInfo > tbody > tr:nth-child(1) > td:nth-child(2)").html(tsd( Number(paymentsData.sales_grand_total) - Number(paymentsData.sales_shipping) ));
                        $("#companyPaymentInfo > tbody > tr:nth-child(2) > td:nth-child(2)").html(tsd(paymentsData.sales_shipping));
                        $("#companyPaymentInfo > tbody > tr:nth-child(3) > td:nth-child(2)").html(tsd(totalSalesPaid));
                        $("#companyPaymentInfo > tbody > tr:nth-child(4) > td:nth-child(2)").html(tsd(paymentsData.sales_due));
                        $("#companyPaymentInfo > tbody > tr:nth-child(5) > td:nth-child(2)").html(tsd( Number(paymentsData.total_received_payments) - totalSalesPaid));
                        $("#companyPaymentInfo > tbody > tr:nth-child(6) > td:nth-child(2)").html(tsd(paymentsData.total_given_bonus));
                        $("#companyPaymentInfo > tbody > tr:nth-child(7) > td:nth-child(2)").html(tsd(paymentsData.returns_grand_total));
                        $("#companyPaymentInfo > tbody > tr:nth-child(8) > td:nth-child(2)").html(tsd(paymentsData.total_payment_return));
                        $("#companyPaymentInfo > tbody > tr:nth-child(9) > td:nth-child(2)").html(tsd(totalDue));
                        $("#companyPaymentInfo > tbody > tr:nth-child(10) > td:nth-child(2)").html(tsd(totalBalance));

                    }
                    
                );
            });
        
        </script>
        

      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Add Payments");
}


/************************** Received Payments **********************/
if(isset($_GET['page']) and $_GET['page'] == "newReceivedPayments") {

    if(empty($_POST["receivedPaymentsFrom"])) {
        return _e("Please select customer");
    } else if(empty($_POST["receivedPaymentsAmount"])) {
        return _e("Please enter received amount.");
    }


    // Insert Receive Payment
    $inserReceivedPayment = easyInsert(
        "received_payments",
        array (
            "received_payments_type"        => "Received Payments",
            "received_payments_shop"        => $_POST["receivedPaymentsShop"],
            "received_payments_accounts"    => $_POST["receivedPaymentsAccounts"],
            "received_payments_from"        => $_POST["receivedPaymentsFrom"],
            "received_payments_amount"      => $_POST["receivedPaymentsAmount"],
            "received_payments_details"     => $_POST["receivedPaymentsDescription"],
            "received_payments_method"      => $_POST["receivedPaymentsMethods"],
            "received_payments_cheque_no"   => empty($_POST["receivedPaymentsChequeNo"]) ? NULL : $_POST["receivedPaymentsChequeNo"],
            "received_payments_cheque_date" => empty($_POST["receivedPaymentsChequeDate"]) ? NULL : $_POST["receivedPaymentsChequeDate"],
            "received_payments_reference"   => $_POST["receivedPaymentsReference"],
            "received_payments_datetime"    => $_POST["receivedPaymentDate"] . date(" H:i:s"),
            "received_payments_add_by"      => $_SESSION["uid"]
        ), 
        array (
            "received_payments_shop"            => $_POST["receivedPaymentsShop"],
            " AND received_payments_accounts"   => $_POST["receivedPaymentsAccounts"],
            " AND received_payments_from"       => $_POST["receivedPaymentsFrom"],
            " AND received_payments_amount"     => $_POST["receivedPaymentsAmount"],
            " AND received_payments_type"       => "Received Payments",
        ),
        true
    );

    if( isset($inserReceivedPayment["status"]) and $inserReceivedPayment["status"] === "success" ) {

        // Update Accounts Balance
        updateAccountBalance($_POST["receivedPaymentsAccounts"]);

        $customerInfo = easySelectA(array(
            "table"     => "customers",
            "fileds"    => "customer_phone, send_notif",
            "where"     => array(
                "customer_id"   => $_POST["receivedPaymentsFrom"]
            )
        ))["data"][0];


        // If Customer notification is enable then send it
        if( $customerInfo["send_notif"] ) {
            
            $customerNumber = $customerInfo["customer_phone"];
            $t = send_sms($customerNumber, "Dear sir, Tk " . number_format($_POST["receivedPaymentsAmount"], 2) . " has been received successfully as on ". date("d M, Y", strtotime($_POST["receivedPaymentDate"]) ) . ". Thank You. \n-". get_options("companyName") );

        }

        $sucessMsg = sprintf(__("Payment has been received successfully. <a %s>Click Here</a> to print the receipt."), " onClick='BMS.MAIN.printPage(this.href, event);' href='". full_website_address() ."/invoice-print/?invoiceType=moneyReceipt&id=". $inserReceivedPayment["last_insert_id"] ."'");

        echo "<div class='alert alert-success'>{$sucessMsg}</div>";


    } else {
        return _e($inserReceivedPayment);
    }
    
}



/*************************** Receive Payment List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "receivedPaymentsList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "received_payments_datetime",
        "customer_name",
        "shop_name",
        "accounts_name",
        "received_payments_amount",
        "received_payments_bonus",
        "received_payments_details"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "received_payments",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0 and received_payments_type = 'Received Payments'"
        )
    ))["data"][0]["totalRow"];
 
    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if(!empty($requestData["search"]["value"])) {  // get data with search
      
        $getData = easySelect(
            "received_payments as received_payment",
            "received_payments_id, customer_name, shop_name, accounts_name, received_payments_amount, received_payments_bonus, received_payments_details, received_payments_datetime, received_payments_cheque_no, received_payments_cheque_date, received_payments_reference, upazila_name, district_name",
            array (
                "left join {$table_prefix}customers on received_payments_from = customer_id",
                "left join {$table_prefix}upazilas on customer_upazila = upazila_id",
                "left join {$table_prefix}districts on customer_district = district_id",
                "left join {$table_prefix}shops on received_payments_shop = shop_id",
                "left join {$table_prefix}accounts on received_payments_accounts = accounts_id"
            ),
            array (
                "received_payment.is_trash = 0 and received_payments_type" => "Received Payments",
                " AND (customer_name LIKE" => $requestData['search']['value'] . "%",
                " OR shop_name LIKE" => $requestData['search']['value'] . "%",
                " OR accounts_name LIKE" => $requestData['search']['value'] . "%",
                ")"
            ),
            array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        );
    
        $totalFilteredRecords = $getData ? $getData["count"] : 0;
  
    } else { // Get data withouth search
    
      $getData = easySelect(
            "received_payments as received_payment",
            "received_payments_id, customer_name, shop_name, accounts_name, received_payments_amount, received_payments_bonus, received_payments_details, received_payments_datetime, received_payments_cheque_no, received_payments_cheque_date, received_payments_reference, upazila_name, district_name",
            array (
                "left join {$table_prefix}customers on received_payments_from = customer_id",
                "left join {$table_prefix}upazilas on customer_upazila = upazila_id",
                "left join {$table_prefix}districts on customer_district = district_id",
                "left join {$table_prefix}shops on received_payments_shop = shop_id",
                "left join {$table_prefix}accounts on received_payments_accounts = accounts_id"
            ),
            array (
                "received_payment.is_trash = 0 and received_payments_type" => "Received Payments"
            ),
            array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
      );
  
  } 

    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['count']) and $getData['count'] > 0) {
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = date("d M, Y h:i A", strtotime($value["received_payments_datetime"]));
            $allNestedData[] = "{$value["customer_name"]}, {$value["upazila_name"]}, {$value["district_name"]}";
            $allNestedData[] = $value["shop_name"];
            $allNestedData[] = $value["accounts_name"];
            $allNestedData[] = $value["received_payments_amount"];
            $allNestedData[] = "<strong>Description:</strong> {$value['received_payments_details']} <br/> <strong>Cheque No:</strong> {$value['received_payments_cheque_no']}; <strong>Cheque Date:</strong> {$value['received_payments_cheque_date']}";
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a onClick=\'BMS.MAIN.printPage(this.href, event);\' href="'. full_website_address() .'/invoice-print/?invoiceType=moneyReceipt&id='. $value["received_payments_id"] .'"><i class="fa fa-print"></i> Print Receipt</a></li>
                                    <li><a class="'. ( current_user_can("income_received_payments.Edit") ? "" : "restricted " ) .'" data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=incomes&page=editReceivedPayment&id='. $value["received_payments_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                    <li><a class="'. ( current_user_can("income_received_payments.Delete") ? "" : "restricted " ) .'deleteEntry" href="'. full_website_address() . '/xhr/?module=incomes&page=deleteReceivedPayment" data-to-be-deleted="'. $value["received_payments_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                    </ul>
                                </div>';
            
            $allData[] = $allNestedData;
        }
    }
    
  
    $jsonData = array (
        "draw"              => intval( $requestData['draw'] ),
        "recordsTotal"      => intval( $totalRecords ),
        "recordsFiltered"   => intval( $totalFilteredRecords ),
        "data"              => $allData
    );
    
    // Encode in Json Formate
    echo json_encode($jsonData); 
}


/***************** Delete Received Payments ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteReceivedPayment") {

    if(current_user_can("income_received_payments.Delete") !== true) {
        echo '{
            "title": "Sorry!",
            "text": "'. __("you do not have permission to delete received payments.") .'",
            "showConfirmButton": true,
            "showCloseButton": true,
            "toast": false,
            "icon": "error"
        }';
        return;
    }

    $selectReceivedPayments = easySelectA(array(
        "table" => "received_payments",
        "where" => array(
            "received_payments_id" => $_POST["datatoDelete"]
        )
    ))["data"][0];

    $deleteData = easyDelete(
        "received_payments",
        array(
            "received_payments_id" => $_POST["datatoDelete"]
        )
    );


    if($deleteData === true) {

        // Update Customer Payment Info
        //updateCustomerPaymentInfo( $selectReceivedPayments["received_payments_from"] );

        // Update Accounts Balance
        updateAccountBalance( $selectReceivedPayments["received_payments_accounts"] );

        echo '{
            "title": "'. __("The entry has been deleted successfully.") .'"
        }';
    } 
}



/************************** Edit Received Payments **********************/
if(isset($_GET['page']) and $_GET['page'] == "editReceivedPayment") {
  
    // Include the modal header
    modal_header("Edit Received Payment", full_website_address() . "/xhr/?module=my-shop&page=updateReceivedPayments");

    $rp = easySelectA(array(
        "table" => "received_payments",
        "where" => array(
            "received_payments_id" => $_GET["id"]
        ),
        "join"  => array(
            "left join {$table_prefix}customers on received_payments_from = customer_id",
            "left join {$table_prefix}shops on received_payments_shop = shop_id"
        )
    ))["data"][0];
    
    ?>
      <div class="box-body">
        
        <div class="form-group required">
            <label for="receivedPaymentDate"><?= __("Date"); ?></label>
            <input type="text" name="receivedPaymentDate" id="receivedPaymentDate" value="<?php echo date("Y-m-d", strtotime($rp["received_payments_datetime"]) ); ?>" class="form-control datePicker">
        </div>
        <div class="form-group required">
            <label for="receivedPaymentsFrom"><?= __("Customer"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="<?= __("From which customer the payments is receving from."); ?>" class="fa fa-question-circle"></i>
            <select name="receivedPaymentsFrom" id="receivedPaymentsFrom" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=customerList" style="width: 100%;" required>
                <option value="<?= $rp["received_payments_from"]; ?>"><?= $rp["customer_name"]; ?></option>
            </select>
        </div>
        <div class="form-group required">
            <label for="receivedPaymentsShop"><?= __("Shop"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="<?= __("Which shop belongs to of this received payments"); ?>" class="fa fa-question-circle"></i>
            <select name="receivedPaymentsShop" id="receivedPaymentsShop" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=shopList" style="width: 100%;" required>
                <option value="<?= $rp["received_payments_shop"]; ?>"><?= $rp["shop_name"]; ?></option>
            </select>
        </div>
        <div class="form-group required">
            <label for="receivedPaymentsAccount"><?= __("Accounts:"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="<?= __("In which account the money will be added"); ?>" class="fa fa-question-circle"></i>
            <select name="receivedPaymentsAccount" id="receivedPaymentsAccount" class="form-control select2" style="width: 100%;" required>
                <?php
                    
                    foreach($selectAccounts["data"] as $accounts) {
                        $selected = ( $rp["received_payments_accounts"] == $accounts['accounts_id'] ) ? "selected" : "";
                        echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group required">
            <label for="receivedPaymentsAmount"><?= __("Received Amount"); ?></label>
            <input type="number" name="receivedPaymentsAmount" id="receivedPaymentsAmount" value="<?= number_format($rp["received_payments_amount"], 2, ".", ""); ?>" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="receivedPaymentsDescription"><?= __("Description:"); ?></label>
            <textarea name="receivedPaymentsDescription" id="receivedPaymentsDescription" rows="3" class="form-control"><?= $rp["received_payments_details"]; ?></textarea>
        </div>
        <div class="form-group required">
            <label for="receivedPaymentsMethods"><?= __("Payment Method"); ?></label>
            <select name="receivedPaymentsMethods" id="receivedPaymentsMethods" class="form-control select2" style="width: 100%">
                <?php
                    $paymentMethod = array("Cash", "Bank Transfer", "Cheque", "Others");
                    
                    foreach($paymentMethod as $method) {
                        $selected = ($rp["received_payments_method"] === $method) ? "selected" : "";
                        echo "<option $selected value='{$method}'>{$method}</option>";
                    }
                ?>
            </select>
        </div>
        <div id="hiddenItem" style="display: none;">
            <div class="form-group">
                <label for="receivedPaymentsChequeNo"><?= __("Cheque No"); ?></label>
                <input type="text" name="receivedPaymentsChequeNo" value="<?= $rp["received_payments_cheque_no"] ?>" id="receivedPaymentsChequeNo" class="form-control">
            </div>
            <div class="form-group">
                <label for="receivedPaymentsChequeDate"><?= __("Cheque Date:"); ?></label>
                <input type="text" name="receivedPaymentsChequeDate" id="receivedPaymentsChequeDate" value="<?= $rp["received_payments_cheque_date"] ?>" class="form-control datePicker">
            </div>
            <div class="form-group">
                <label for="receivedPaymentsReference"><?= __("Reference:"); ?></label>
                <input type="text" name="receivedPaymentsReference" id="receivedPaymentsReference" value="<?= $rp["received_payments_reference"] ?>" class="form-control">
            </div>
        </div>

        <input type="hidden" name="receivedPaymentId" value ="<?php echo safe_entities($_GET["id"]); ?>">

        <div id="ajaxSubmitMsg"></div>

        <script>

            $(function() {
                if($("#receivedPaymentsMethods").val() == "Cheque") {
                    $("#hiddenItem").css("display", "block");
                } else {
                    $("#hiddenItem").css("display", "none");
                }
            });

            $(document).on("change", "#receivedPaymentsMethods", function() {
                if($("#receivedPaymentsMethods").val() == "Cheque") {
                    $("#hiddenItem").css("display", "block");
                } else {
                    $("#hiddenItem").css("display", "none");
                }
            });

      </script>


      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Update Payments");
}


/************************** updateReceivedPayments **********************/
if(isset($_GET['page']) and $_GET['page'] == "updateReceivedPayments") {

    if(empty($_POST["receivedPaymentsFrom"])) {
        return _e("Please select customer");
    } else if(empty($_POST["receivedPaymentsAmount"])) {
        return _e("Please enter payments amount");
    }

    // Select the previouse account
    $previousAccount = easySelectA(array(
        "table" => "received_payments",
        "fields" => "received_payments_accounts",
        "where" => array(
            "received_payments_id" => $_POST["receivedPaymentId"]
        )
    ))["data"][0]["received_payments_accounts"];


    // Insert Advance collection
    $updateReceivedPayments = easyUpdate(
        "received_payments",
        array (
            "received_payments_shop"        => $_POST["receivedPaymentsShop"],
            "received_payments_accounts"    => $_POST["receivedPaymentsAccount"],
            "received_payments_from"        => $_POST["receivedPaymentsFrom"],
            "received_payments_amount"      => $_POST["receivedPaymentsAmount"],
            "received_payments_details"     => $_POST["receivedPaymentsDescription"],
            "received_payments_method"      => $_POST["receivedPaymentsMethods"],
            "received_payments_cheque_no"   => empty($_POST["receivedPaymentsChequeNo"]) ? NULL : $_POST["receivedPaymentsChequeNo"],
            "received_payments_cheque_date" => empty($_POST["receivedPaymentsChequeDate"]) ? NULL : $_POST["receivedPaymentsChequeDate"],
            "received_payments_reference"   => $_POST["receivedPaymentsReference"],
            "received_payments_datetime"    => $_POST["receivedPaymentDate"],
            "received_payments_add_by"      => $_SESSION["uid"]
        ), 
        array (
            "received_payments_id"          => $_POST["receivedPaymentId"],
        )
    );

    if($updateReceivedPayments === true) {
        // Update Customer Payment Info
        // This update might be not required. It will be check latter
        //updateCustomerPaymentInfo($_POST["receivedPaymentsFrom"]);

        // Update Accounts Balance
        updateAccountBalance($_POST["receivedPaymentsAccount"]);

        // update account balance and customer payment info for if these are changed
        if( $_POST["receivedPaymentsAccount"] !== $previousAccount ) {
            
            // Update previouse Accounts Balance
            updateAccountBalance($previousAccount);

        }

        $sucessMsg = sprintf(__("Successfully updated. Please <a %s>Click Here</a> to print the receipt."), " onClick='BMS.MAIN.printPage(this.href, event);' href='". full_website_address() ."/invoice-print/?invoiceType=moneyReceipt&id=". safe_entities($_POST["receivedPaymentId"]) ."'");

        echo "<div class='alert alert-success'>{$sucessMsg}</div>";


    } else {
        _e($updateReceivedPayments);
    }
    
}

/************************** Add Income **********************/
if(isset($_GET['page']) and $_GET['page'] == "newIncome") {

    // Include the modal header
    modal_header("Add Income", full_website_address() . "/xhr/?module=incomes&page=addNewIncome");
    
    ?>

      <div class="box-body">
        <div class="form-group required">
            <label for="incomeDate"><?= __("Date:"); ?></label>
            <input type="text" name="incomeDate" id="incomeDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker" required>
        </div>
        <div class="form-group required">
            <label for="incomeAccounts"><?= __("Accounts:"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="<?= __("In which accounts the amount will add."); ?>" class="fa fa-question-circle"></i>
            <select name="incomeAccounts" id="incomeAccounts" class="form-control select2" style="width: 100%;" required>
                <?php
                
                    foreach($selectAccounts["data"] as $accounts) {
                        $selected = (isset($_SESSION['aid']) and $_SESSION['aid'] === $accounts['accounts_id']) ? "selected" : "";
                        echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="shopId"><?= __("Shop:"); ?> </label>
            <i data-toggle="tooltip" data-placement="right" title="<?= __("In which shop the sales has completed."); ?>" class="fa fa-question-circle"></i>
            <select name="shopId" id="shopId" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=shopList" style="width: 100%;">
                <option value=""><?= __("Select Shop"); ?>....</option>
            </select>
        </div>
        <div class="form-group">
            <label for="incomeCustomer"><?= __("Customer"); ?></label>
            <select name="incomeCustomer" id="incomeCustomer" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=customerList" style="width: 100%;">
                <option value=""><?= __("Select Customer"); ?>....</option>
            </select>
        </div>
        <div class="form-group required">
            <label for="incomeAmount"><?= __("Amount:"); ?></label>
            <input type="number" name="incomeAmount" id="incomeAmount" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="incomeDescription"><?= __("Description:"); ?></label>
            <textarea name="incomeDescription" id="incomeDescription" rows="3" class="form-control"></textarea>
        </div>

      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer();
  
}


/************************** Add new Income **********************/
if(isset($_GET['page']) and $_GET['page'] == "addNewIncome") {

    if(current_user_can("incomes.Add") !== true) {
        return _e("Sorry! you do not have permission to add income.");
    }

    if(empty($_POST["incomeAccounts"])) {
        return _e("Please select accounts.");
    } else if(empty($_POST["incomeDate"])) {
        return _e("Please select income date.");
    } else if(empty($_POST["incomeAmount"])) {
        return _e("Please enter amount.");
    }

    $addIncome = easyInsert(
        "incomes",
        array (
            "incomes_accounts_id"   => $_POST["incomeAccounts"],
            "incomes_shop_id"       => empty($_POST["shopId"]) ? NULL : $_POST["shopId"],
            "incomes_from"          => empty($_POST["incomeCustomer"]) ? NULL : $_POST["incomeCustomer"],
            "incomes_date"          => $_POST["incomeDate"],
            "incomes_amount"        => $_POST["incomeAmount"],
            "incomes_description"   => $_POST["incomeDescription"],
            "incomes_add_by"        => $_SESSION["uid"]
        ),
        array(),
        true
    );

    if( isset($addIncome["status"]) and $addIncome["status"] === "success" ) {

        // Update Accounts Balance
        updateAccountBalance($_POST["incomeAccounts"]);

        $sucessMsg = sprintf(__("Income Successfully Added. <a %s>Click Here</a> to print the receipt."), " onClick='BMS.MAIN.printPage(this.href, event);' href='". full_website_address() ."/invoice-print/?invoiceType=moneyReceiptOtherIncome&id=". $addIncome["last_insert_id"] ."'");
        echo "<div class='alert alert-success'>$sucessMsg</div>";
    
    } else {
        _e($addIncome);
    }

}


/*************************** Accounts List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "incomeList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "incomes_add_on",
        "accounts_name",
        "shop_name",
        "incomes_amount"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "incomes",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }

    $dateTimeFormat = "F j, Y";
 
    if(!empty($requestData["search"]["value"])) {  // get data with search
        
        $getData = easySelect(
            "incomes as income",
            "incomes_id, incomes_accounts_id, incomes_shop_id, DATE_FORMAT(incomes_date, '%d/%m/%Y') as incomes_date, accounts_name, shop_name, customer_name, incomes_amount, incomes_description",
            array (
                "left join {$table_prefix}accounts on incomes_accounts_id = accounts_id",
                "left join {$table_prefix}shops on incomes_shop_id = shop_id",
                "left join {$table_prefix}customers on incomes_from = customer_id"
            ),
            array (
                "income.is_trash"  => 0,
                " AND accounts_name LIKE" => $requestData['search']['value'] . "%",
                " OR shop_name LIKE" => $requestData['search']['value'] . "%",
                " OR customer_name LIKE" => $requestData['search']['value'] . "%"
            ),
            array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        );

        $totalFilteredRecords = $getData ? $getData["count"] : 0;

    } else { // Get data withouth search

        $getData = easySelect(
            "incomes as income",
            "incomes_id, incomes_accounts_id, incomes_shop_id, DATE_FORMAT(incomes_date, '%d/%m/%Y') as incomes_date, accounts_name, shop_name, customer_name, incomes_amount, incomes_description",
            array (
                "left join {$table_prefix}accounts on incomes_accounts_id = accounts_id",
                "left join {$table_prefix}shops on incomes_shop_id = shop_id",
                "left join {$table_prefix}customers on incomes_from = customer_id"
            ),
            array(
                "income.is_trash"  => 0
            ),
            array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        );

    }

    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['data'])) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["incomes_date"];
            $allNestedData[] = $value["shop_name"];
            $allNestedData[] = $value["customer_name"];
            $allNestedData[] = $value["accounts_name"];
            $allNestedData[] = $value["incomes_amount"];
            $allNestedData[] = $value["incomes_description"];
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        <li><a onClick=\'BMS.MAIN.printPage(this.href, event);\' href="'. full_website_address() .'/invoice-print/?invoiceType=moneyReceiptOtherIncome&id='. $value["incomes_id"] .'"><i class="fa fa-print"></i> Print Receipt</a></li>
                                        <li><a class="'. ( current_user_can("incomes.Edit") ? "" : "restricted " ) .'" data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=incomes&page=editIncome&id='. $value["incomes_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                        <li><a class="'. ( current_user_can("incomes.Delete") ? "" : "restricted " ) .'deleteEntry" href="'. full_website_address() . '/xhr/?module=incomes&page=deleteIncome" data-to-be-deleted="'. $value["incomes_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                    </ul>
                                </div>';
            $allData[] = $allNestedData;
        }
    }
    

    $jsonData = array (
        "draw"              => intval( $requestData['draw'] ),
        "recordsTotal"      => intval( $totalRecords ),
        "recordsFiltered"   => intval( $totalFilteredRecords ),
        "data"              => $allData
    );
    
    // Encode in Json Formate
    echo json_encode($jsonData); 
}



/************************** Edit Income **********************/
if(isset($_GET['page']) and $_GET['page'] == "editIncome") {

    // Include the modal header
    modal_header("Edit Income", full_website_address() . "/xhr/?module=incomes&page=UpdateIncome");
    $selectIncome = easySelect(
        "incomes as income",
        "incomes_id, incomes_accounts_id, incomes_shop_id, incomes_date, accounts_name, shop_name, incomes_from, customer_name, incomes_amount, incomes_description",
        array (
            "left join {$table_prefix}accounts on incomes_accounts_id = accounts_id",
            "left join {$table_prefix}shops on incomes_shop_id = shop_id",
            "left join {$table_prefix}customers on incomes_from = customer_id"
        ),
        array (
            "incomes_id"        => $_GET["id"],
            " and income.is_trash"  => 0
        )
    );

    $income = $selectIncome["data"][0];
    
    ?>

      <div class="box-body">
        
        <div class="form-group required">
            <label for="incomeDate"><?= __("Date:"); ?></label>
            <input type="text" name="incomeDate" id="incomeDate" value="<?php echo $income["incomes_date"]; ?>" class="form-control datePicker" required>
        </div>
        <div class="form-group required">
            <label for="incomeAccounts"><?= __("Accounts:"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="<?= __("In which accounts the amount will add."); ?>" class="fa fa-question-circle"></i>
            <select name="incomeAccounts" id="incomeAccounts" class="form-control select2" style="width: 100%;" required>
                <?php
                    foreach($selectAccounts["data"] as $accounts) {
                        $selected = ($accounts['accounts_id'] === $income["incomes_accounts_id"]) ? "selected" : "";
                        echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="shopId"><?= __("Shop:"); ?> </label>
            <i data-toggle="tooltip" data-placement="right" title="<?= __("In which shop the sales has completed."); ?>" class="fa fa-question-circle"></i>
            <select name="shopId" id="shopId" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=shopList" style="width: 100%;">
                <option value="<?php echo $income["incomes_shop_id"]; ?>"><?php echo $income["shop_name"]; ?></option>
            </select>
        </div>
        <div class="form-group">
            <label for="incomeCustomer"><?= __("Customer"); ?></label>
            <select name="incomeCustomer" id="incomeCustomer" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=customerList" style="width: 100%;">
                <option value="<?php echo $income["incomes_from"]; ?>"><?php echo $income["customer_name"]; ?></option>
            </select>
        </div>
        <div class="form-group required">
            <label for="incomeAmount"><?= __("Amount:"); ?></label>
            <input type="number" name="incomeAmount" id="incomeAmount" class="form-control" value="<?php echo number_format($income["incomes_amount"], 0, "", ""); ?>" required>
        </div>
        <div class="form-group">
            <label for="incomeDescription"><?= __("Description:"); ?></label>
            <textarea name="incomeDescription" id="incomeDescription" rows="3" class="form-control"><?php echo $income["incomes_description"]; ?></textarea>
        </div>
        <input type="hidden" name="income_id" value="<?php echo safe_entities($_GET["id"]); ?>">

      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer();
  
}


/************************** Update Income **********************/
if(isset($_GET['page']) and $_GET['page'] == "UpdateIncome") {

    if( !current_user_can("incomes.Edit") ) {
        echo '{
            "title": "'. __("Sorry! You do not have permission to edit income") .'",
            "icon": "error"
        }';
        return;
    }

    if(empty($_POST["incomeAccounts"])) {
        return _e("Please select accounts.");
    } else if(empty($_POST["incomeDate"])) {
        return _e("Please select income date.");
    } else if(empty($_POST["incomeAmount"])) {
        return _e("Please enter amount.");
    }

    $updateIncome = easyUpdate(
        "incomes",
        array (
            "incomes_accounts_id"   => $_POST["incomeAccounts"],
            "incomes_shop_id"       => empty($_POST["shopId"]) ? NULL : $_POST["shopId"],
            "incomes_date"          => $_POST["incomeDate"],
            "incomes_from"          => empty($_POST["incomeCustomer"]) ? NULL : $_POST["incomeCustomer"],
            "incomes_amount"        => $_POST["incomeAmount"],
            "incomes_description"   => $_POST["incomeDescription"],
            "incomes_update_by"     => $_SESSION["uid"]
        ),
        array (
            "incomes_id"    => $_POST["income_id"]
        )
    );
    
    if($updateIncome === true) {
        // Update Accounts Balance
        updateAccountBalance($_POST["incomeAccounts"]);

        _s("Income Successfully updated.");

    } else {
        _e($updateIncome);
    }

}


/***************** Delete Income ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteIncome") {

    if(current_user_can("Income.Delete") !== true) {
        echo '{
            "title": "Sorry!",
            "text": "'. __("you do not have permission to delete Income.") .'",
            "showConfirmButton": true,
            "showCloseButton": true,
            "toast": false,
            "icon": "error"
        }';
        return;
    }

    $deleteData = easyDelete(
        "incomes",
        array(
            "incomes_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {
        echo '{
            "title": "'. __("The income has been deleted successfully.") .'"
        }';
    } 
}




?>