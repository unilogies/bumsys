<?php

// Select the Accounts
$selectAccounts = easySelect("accounts", "accounts_id, accounts_name", array(), array("is_trash" => 0));

/************************** New payments category **********************/
if(isset($_GET['page']) and $_GET['page'] == "newPaymentCategory") {
  
    // Include the modal header
    modal_header("New Payment Category", full_website_address() . "/xhr/?module=expenses&page=addPaymentCategory");
    
    ?>
      <div class="box-body">
        
        <div class="form-group">
            <label for="categoryName"><?= __("Category Name"); ?></label>
            <input type="text" name="categoryName" id="categoryName" class="form-control">
        </div>
        <div class="form-group">
            <label for="categoryShopId"><?= __("Shop:"); ?> </label>
            <i data-toggle="tooltip" data-placement="right" title="In which shop the category will appear." class="fa fa-question-circle"></i>
            <select name="categoryShopId" id="categoryShopId" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=shopList" style="width: 100%;">
                <option value=""><?= __("Select Shop"); ?>....</option>
            </select>
        </div>
              
      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer();
  
}


if(isset($_GET['page']) and $_GET['page'] == "addPaymentCategory") {

    if(current_user_can("payment_categories.Add") !== true) {
        echo '{
            "title": "Sorry!",
            "text": "'. __("You do not have permission to add payment category.") .'",
            "showConfirmButton": true,
            "showCloseButton": true,
            "toast": false,
            "timer": false,
            "icon": "error"
        }';
        return;
    }

    if(empty($_POST["categoryName"])) {
        return _e("Please enter category name.");
    }

    $insertPaymentCategory = easyInsert(
        "payments_categories",
        array (
            "payment_category_name"     => $_POST["categoryName"],
            "payment_category_shop_id"  => empty($_POST["categoryShopId"]) ? NULL : $_POST["categoryShopId"]
        ),
        array (
            "payment_category_name"  => $_POST["categoryName"]
        )
    );
    
    if(strlen($insertPaymentCategory) < 2) {
        _s("Payment category successfully added.");
    } else {
        _e($insertPaymentCategory);
    }

}



/*************************** Payment Category List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "paymentCategoryList") {

    if( !current_user_can("payment_categories.View") ) {
        return _e("Sorry! you do not have permission to view payment category list");
    }
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "payment_category_name",
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "payments_categories",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if(!empty($requestData["search"]["value"])) {  // get data with search
        
        $getData = easySelect(
            "payments_categories as payments_category",
            "payment_category_id, payment_category_name, payment_category_shop_id, shop_name",
            array (
                "left join {$table_prefix}shops on payment_category_shop_id = shop_id"
            ),
            array (
                "payments_category.is_trash = 0 and payment_category_name LIKE" => "%".$requestData['search']['value'] . "%"
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
            "payments_categories as payments_category",
            "payment_category_id, payment_category_name, payment_category_shop_id, shop_name",
            array (
                "left join {$table_prefix}shops on payment_category_shop_id = shop_id"
            ),
            array("payments_category.is_trash = 0"),
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
    if($getData !== false) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = $value["payment_category_name"];
            $allNestedData[] = $value["shop_name"];
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=expenses&page=editPaymentCategory&id='. $value["payment_category_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=expenses&page=deletePaymentCategory" data-to-be-deleted="'. $value["payment_category_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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



// Delete Payment Category
if(isset($_GET['page']) and $_GET['page'] == "deletePaymentCategory") {

    
    if(current_user_can("payment_categories.Delete") !== true) {
        echo '{
            "title": "Sorry!",
            "text": "'. __("you do not have permission to delete categories.") .'",
            "showConfirmButton": true,
            "showCloseButton": true,
            "toast": false,
            "icon": "error"
        }';
        return;
    }

    $deleteData = easyDelete(
        "payments_categories",
        array(
            "payment_category_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {
        echo 1;
    } 

}


/************************** Update payments category **********************/
if(isset($_GET['page']) and $_GET['page'] == "editPaymentCategory") {

    if( !current_user_can("payment_categories.Edit") ) {
        return _e("Sorry! you do not have permission to edit payment category");
    }
  
    // Include the modal header
    modal_header("Edit Payment Category", full_website_address() . "/xhr/?module=expenses&page=updatePaymentCategory");

    $selectPaymentCategory = easySelectA(array(
        "table"     => "payments_categories",
        "fields"    => "payment_category_name, payment_category_shop_id, shop_name",
        "join"      => array(
            "left join {$table_prefix}shops on shop_id = payment_category_shop_id"
        ),
        "where"     => array(
            "payment_category_id"   => $_GET["id"],
        )
    ))["data"][0];
    
    ?>
      <div class="box-body">
        
            <div class="form-group">
                <label for="categoryName">Category Name</label>
                <input type="text" name="categoryName" id="categoryName" value="<?php echo $selectPaymentCategory["payment_category_name"]; ?>" class="form-control">
                <input type="hidden" name="categoryNameID" value="<?php echo safe_entities($_GET["id"]); ?>">
            </div>
            <div class="form-group">
                <label for="categoryShopId"><?= __("Shop:"); ?> </label>
                <i data-toggle="tooltip" data-placement="right" title="In which shop the category will appear." class="fa fa-question-circle"></i>
                <select name="categoryShopId" id="categoryShopId" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=shopList" style="width: 100%;">
                    <option value=""><?= __("Select Shop"); ?>....</option>
                    <option selected value="<?php echo $selectPaymentCategory["payment_category_shop_id"]; ?>"><?php echo empty($selectPaymentCategory["shop_name"]) ? __("Select Shop...") : $selectPaymentCategory["shop_name"]; ?></option>
                </select>
            </div>

      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer();
  
}
  

if(isset($_GET['page']) and $_GET['page'] == "updatePaymentCategory") {

    if( !current_user_can("payment_categories.Edit") ) {
        return _e("Sorry! you do not have permission to edit payment category");
    }

    if( empty($_POST["categoryName"]) ) {
        return _e("Please enter category name");
    }

    $updatePaymentCategory = easyUpdate(
        "payments_categories",
        array (
            "payment_category_name"     => $_POST["categoryName"],
            "payment_category_shop_id"  => empty($_POST["categoryShopId"]) ? NULL : $_POST["categoryShopId"]
        ),
        array (
            "payment_category_id"  => $_POST["categoryNameID"]
        ),
        true
    );
    
    if($updatePaymentCategory === true) {
        _s("Payment category successfully updated.");
    } else {
        _e($updatePaymentCategory);
    }

}



/************************** Bill Payment **********************/
if(isset($_GET['page']) and $_GET['page'] == "billPay") {
  
    // Include the modal header
    modal_header("New Bill Payment", full_website_address() . "/xhr/?module=expenses&page=newBillPay");
    
    ?>
      <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group required">
                    <label for="paymentsDate"><?= __("Payment Date:"); ?></label>
                    <input type="text" name="paymentsDate" id="paymentsDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group required">
                    <label for="paymentFromAccount"><?= __("Accounts"); ?></label>
                    <i data-toggle="tooltip" data-placement="right" title="<?= __("From which accounts the payment will be made."); ?>" class="fa fa-question-circle"></i>
                    <select name="paymentFromAccount" id="paymentFromAccount" class="form-control select2" style="width: 100%;" required>
                        <?php
                            
                            foreach($selectAccounts["data"] as $accounts) {
                                $selected = (isset($_SESSION['aid']) and $_SESSION['aid'] === $accounts['accounts_id']) ? "selected" : "";
                                echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
            
        </div> 
        <br/>

        <div id="PaymentRow" style="padding-top: 10px; padding-left: 10px;" class="bg-info">
            <div class="row">
                <!-- Column One -->
                <div class="col-md-4">
                    <div class="form-group required">
                        <label for="paymentCategory"><?= __("Payment Category:"); ?></label>
                        <select name="paymentCategory[]" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=paymentCategoryList" style="width: 100%;" required>
                            <option value=""><?= __("Select category"); ?>....</option>
                        </select>
                    </div>
                </div>
                <!-- Column Two -->
                <div class="col-md-3">
                    <div class="form-group required">
                        <label for="paymentAmount"><?= __("Amount"); ?></label>
                        <input type="number" name="paymentAmount[]" class="form-control paymentAmount" required>
                    </div>
                </div>
                <!-- Column Three -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="paymentNote"><?= __("Note (Narration)"); ?><span style="font-size: 18px;"></span></label>
                        <input type="text" name="paymentNote[]" class="form-control">
                    </div>
                </div>
            </div>
        </div>
        <div style="padding-top: 10px; padding-left: 10px; padding-bottom: 5px;" class="bg-info">
            <p style="font-weight: bold;"><?= __("Total Amount:"); ?>
                <span id="totalBillPayAmount">0.00</span>
            </p>
        </div> 

        <!-- Add payment row button -->
        <div style="width: 80px; display: block; margin: 20px auto;">
            <span style="cursor: pointer;" class="btn btn-primary" id="addPaymentRow">
                <i style="padding: 5px;" class="fa fa-plus-circle"></i>
            </span>
        </div>
        
        <div class="row">

            <div class="col-md-6">
                <div class="form-group">
                    <label for="paymentCompany"><?= __("Company"); ?><span style="font-size: 18px;"></span></label>
                    <select name="paymentCompany" id="paymentCompany" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=CompanyList" style="width: 100%;">
                        <option value=""><?= __("Select Company"); ?>....</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div style="display: none;" class="form-group">
                    <label for="paymentDescription"><?= __("Description:"); ?></label>
                    <textarea name="paymentDescription" id="paymentDescription" rows="3" class="form-control"></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group required">
                    <label for="paymentMethods"><?= __("Payment Method"); ?></label>
                    <select name="paymentMethods" id="paymentMethods" class="form-control select2" style="width: 100%">
                        <?php
                            $paymentMethod = array("Cash", "Bank Transfer", "Cheque", "Others");
                            
                            foreach($paymentMethod as $method) {
                                echo "<option value='{$method}'>{$method}</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>

            <div id="hiddenItem" style="display: none;">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="paymentChequeNo"><?= __("Cheque No"); ?></label>
                        <input type="text" name="paymentChequeNo" id="paymentChequeNo" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="paymentChequeDate"><?= __("Cheque Date:"); ?></label>
                        <input type="text" name="paymentChequeDate" id="paymentChequeDate" value="" class="form-control datePicker">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="paymentReference"><?= __("Reference:"); ?></label>
                        <input type="text" name="paymentReference" id="paymentReference" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="paymentAttachment"><?= __("Attachment"); ?></label>
                        <input type="file" name="paymentAttachment" id="paymentAttachment" class="form-control">
                    </div>
                </div>
            </div>

        </div>

        <div id="ajaxSubmitMsg"></div>

      </div>
       
        <script>

        $(document).on("change", "#paymentMethods", function() {
            if($("#paymentMethods").val() == "Cheque") {
                $("#hiddenItem").css("display", "block");
            } else {
                $("#hiddenItem").css("display", "none");
            }
        });

        /* Add Payment salary row 
        The first is used to remove envent listener. */
        $(document).off("click","#addPaymentRow");
        $(document).on("click","#addPaymentRow", function() {

            var html = '<div class="row"> \
                <div class="col-md-4"> \
                    <div class="form-group required"> \
                        <select name="paymentCategory[]" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=paymentCategoryList" style="width: 100%;" required> \
                            <option value="">Select category....</option> \
                        </select> \
                    </div> \
                </div> \
                <div class="col-md-3"> \
                    <div class="form-group required"> \
                        <input type="number" name="paymentAmount[]" class="form-control paymentAmount" required> \
                    </div> \
                </div> \
                <div class="col-md-4"> \
                    <div class="form-group required"> \
                        <input type="text" name="paymentNote[]" class="form-control"> \
                    </div> \
                </div> \
                <div class="col-xs-1"> \
                    <i style="cursor: pointer; padding: 10px 5px 0 0;" class="fa fa-trash-o removePaymentRow"></i> \
                </div> \
            </div>';

            $("#PaymentRow").append(html);

        });

        /* Remove Salary payments row */
        $(document).on("click", ".removePaymentRow", function() {
            $(this).closest(".row").css("background-color", "whitesmoke").hide("slow", function() {
                $(this).closest(".row").remove();
            });
        });

        $(document).on("blur keyup", ".paymentAmount", function() {

            var totalAmount = 0;
            $(".paymentAmount").each( function() {
                totalAmount += Number($(this).val());
            });

            $("#totalBillPayAmount").html(totalAmount);

        });
        
      </script>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Make Payment");
  
}

/************************** Bill Payment **********************/
if(isset($_GET['page']) and $_GET['page'] == "newBillPay") {

    if( !current_user_can("payments.Add") ) {
        return _e("Sorry! you do not have permission to pay bill");
    }

    $accounts_balance = accounts_balance($_POST["paymentFromAccount"]);
    $totalPaymentAmount = array_sum($_POST["paymentAmount"]);

    if(empty($_POST["paymentsDate"])) {
        return _e("Please enter payment date");
    } else if(empty($_POST["paymentFromAccount"])) {
        return _e("Please select accounts");
    } else if(empty($_POST["paymentMethods"])) {
        return _e("Please select payment method");
    } else if($_POST["paymentMethods"] === "Cheque" and empty($_POST["paymentChequeNo"])) {
        return _e("Please enter check no.");
    }  else if(!negative_value_is_allowed($_POST["paymentFromAccount"]) and $accounts_balance < $totalPaymentAmount ) {
        return _e("Payment amount is exceeded of account balance (%.2f)", number_format($accounts_balance, 2));
    }


    // Upload the attachment
    $paymentAttachment = NULL;
    if($_FILES["paymentAttachment"]["size"] > 0) {
 
        $paymentAttachment = easyUpload($_FILES["paymentAttachment"], "attachments/payments/cheque/" . date("M, Y"), safe_entities($_POST["paymentChequeNo"]) );

        if(!isset($paymentAttachment["success"])) {
            return _e($paymentAttachment);
        } else {
            $paymentAttachment = $paymentAttachment["fileName"];
        }
         
    }

    $paymentReferences = payment_reference("bill");

    // Insert the Bill Payment
    $insertBillPay = easyInsert (
        "payments",
        array (
            "payment_date"              => $_POST["paymentsDate"],
            "payment_to_company"        => empty($_POST["paymentCompany"]) ? NULL : $_POST["paymentCompany"],
            "payment_status"            => "Complete",
            "payment_amount"            => $totalPaymentAmount,
            "payment_from"              => $_POST["paymentFromAccount"],
            "payment_description"       => $_POST["paymentDescription"],
            "payment_method"            => $_POST["paymentMethods"],
            "payment_cheque_no"         => empty($_POST["paymentChequeNo"]) ? NULL : $_POST["paymentChequeNo"], 
            "payment_cheque_date"       => empty($_POST["paymentChequeDate"]) ? NULL : $_POST["paymentChequeDate"],
            "payment_attachement"       => $paymentAttachment,
            "payment_reference"         => $paymentReferences,
            "payment_made_by"           => $_SESSION["uid"]
        ),
        array (
            "payment_date"                  => $_POST["paymentsDate"],
            " AND payment_to_company"       => empty($_POST["paymentCompany"]) ? NULL : $_POST["paymentCompany"],
            " AND payment_amount"           => $totalPaymentAmount,
            " AND payment_from"             => $_POST["paymentFromAccount"],
            " AND payment_method"           => $_POST["paymentMethods"],
            " AND payment_to_employee"      => "",
            " AND payment_made_by"          => $_SESSION["uid"]
        ),
        true
    );

    if(isset($insertBillPay["status"]) and $insertBillPay["status"] === "success" ) {

        // Insert payment items if successfully inserted in payments table
        foreach($_POST["paymentCategory"] as $key => $categoryID) {

            // Insert payment items
            easyInsert(
                "payment_items",
                array (
                    "payment_items_payments_id" => $insertBillPay["last_insert_id"],
                    "payment_items_date"        => $_POST["paymentsDate"],
                    "payment_items_type"        => "Bill",
                    "payment_items_company"     => empty($_POST["paymentCompany"]) ? NULL : $_POST["paymentCompany"],
                    "payment_items_category_id" => $categoryID,
                    "payment_items_amount"      => $_POST["paymentAmount"][$key],
                    "payment_items_description" => $_POST["paymentNote"][$key],
                    "payment_items_accounts"    => $_POST["paymentFromAccount"],
                    "payment_items_made_by"     => $_SESSION["uid"]
                )
            );

        }

        // Update Accounts Balance
        updateAccountBalance($_POST["paymentFromAccount"]);

        echo "<div class='alert alert-success'>" . sprintf(__("Payment successfully added. The reference number is: <strong>%s</strong>. Please <a %s>click here to print</a> the receipt."), $paymentReferences, " onClick='BMS.MAIN.printPage(this.href, event);' href='" . full_website_address()."/invoice-print/?autoPrint=true&invoiceType=paymentReceipt&id={$insertBillPay['last_insert_id']}'") . "</div>";

    } else {
        _e($insertBillPay);
    }

}


/************************** Salary Payment **********************/
if(isset($_GET['page']) and $_GET['page'] == "salaryPay") {
  
    // Include the modal header
    modal_header("New Salary Payment", full_website_address() . "/xhr/?module=expenses&page=newSalaryPay");
    
    ?>
      <div class="box-body">
        
        <div class="row">

            <!-- Left Column One --> 
            <div class="col-md-5">

                <div class="form-group required">
                    <label for="paymentDate"><?= __("Payment Date:"); ?></label>
                    <input type="text" name="paymentDate" id="paymentDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker" required>
                </div>
                <div class="form-group required">
                    <label for="paymentEmployee"><?= __("Employee"); ?></label>
                    <select name="paymentEmployee" id="paymentEmployee" select2-minimum-input-length="1" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=employeeListAll" style="width: 100%;" required>
                        <option value=""><?= __("Select Employee"); ?>....</option>
                    </select>
                </div>

            </div>

            <!-- Right Column One --> 
            <div class="col-md-7">

                <div style="height: 135px;" class="bg-info">
                    <table id="salaryInfo" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-right"><?= __("Salary"); ?></th>
                                <th class="text-right"><?= __("Overtime"); ?></th>
                                <th class="text-right"><?= __("Bonus"); ?></th>
                                <th class="text-right"><?= __("Total"); ?></th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <tr>
                                <td class="text-right">0.00</td>
                                <td class="text-right">0.00</td>
                                <td class="text-right">0.00</td>
                                <td class="text-right">0.00</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="bg-primary text-center" colspan="4"><?= __("Last taken loan: %.2f; Paid: %.2f; Monthly: %.2f", 0.00, 0.00, 0.00); ?></th>
                            </tr>
                        </tfoot>

                    </table>
                </div> <!-- ./Bg-info -->

            </div>

        </div> <!-- .Row -->

        <div class="row">

            <!-- Left Column Two --> 
            <div class="col-md-5">
                
                <div class="form-group required">
                    <label for="paymentFromAccount"><?= __("Accounts"); ?></label>
                    <i data-toggle="tooltip" data-placement="right" title="<?= __("From which accounts the payment is made."); ?>" class="fa fa-question-circle"></i>
                    <select name="paymentFromAccount" id="paymentFromAccount" class="form-control select2" style="width: 100%;" required>
                        <?php
                            
                            foreach($selectAccounts["data"] as $accounts) {
                                $selected = (isset($_SESSION['aid']) and $_SESSION['aid'] === $accounts['accounts_id']) ? "selected" : "";
                                echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                            }
                        ?>
                    </select>
                </div>

                <div style="display: none" class="form-group">
                    <label for="paymentDescription"><?= __("Description:"); ?></label>
                    <textarea name="paymentDescription" id="paymentDescription" rows="3" class="form-control"></textarea>
                </div>

                <div class="form-group required">
                    <label for="paymentMethods"><?= __("Payment Method"); ?></label>
                    <select name="paymentMethods" id="paymentMethods" class="form-control select2" style="width: 100%">
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
                        <label for="paymentChequeNo"><?= __("Cheque No"); ?></label>
                        <input type="text" name="paymentChequeNo" id="paymentChequeNo" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="paymentChequeDate"><?= __("Cheque Date:"); ?></label>
                        <input type="text" name="paymentChequeDate" id="paymentChequeDate" value="" class="form-control datePicker">
                    </div>
                    <div class="form-group">
                        <label for="paymentReference"><?= __("Reference:"); ?></label>
                        <input type="text" name="paymentReference" id="paymentReference" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="paymentAttachment"><?= __("Attachment"); ?></label>
                        <input type="file" name="paymentAttachment" id="paymentAttachment" class="form-control">
                    </div>
                </div>

            </div> <!-- /. Left Column -->
            
            <!-- Right Column Two --> 
            <div class="col-md-7">

                <div id="salaryPaymentRow">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group required">
                                <label for="paymentsType"><?= __("Salary Type"); ?></label>
                                <select name="paymentsType[]" class="paymentsType form-control" style="width: 100%;" required>
                                    <option value="Salary"><?= __("Salary"); ?></option>
                                    <option value="Overtime"><?= __("Overtime"); ?></option>
                                    <option value="Bonus"><?= __("Bonus"); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group required">
                                <label for="paymentAmount"><?= __("Amount"); ?></label>
                                <input type="number" name="paymentAmount[]" class="paymentAmount form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="paymentAmount"><?= __("Note:"); ?> <span style="font-size: 18px;"></span> </label>
                                <input type="text" name="salaryPaymentNote[]" class="salaryPaymentNote form-control">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Add payment row button -->
                <div style="width: 80px; display: block; margin: 20px auto;">
                    <span style="cursor: pointer;" class="btn btn-primary" id="addSalaryPaymentRow">
                        <i style="padding: 5px;" class="fa fa-plus-circle"></i>
                    </span>
                </div>

            </div>
        
        </div> <!-- .Row -->
        
        <div id="ajaxSubmitMsg"></div>

      </div>
      <!-- /Box body-->
      
    <?php
  
    // Include the modal footer
    modal_footer("Pay Salary");
  
}


/*************************** New Salary Pay ***********************/
if(isset($_GET['page']) and $_GET['page'] == "newSalaryPay") {

    if( !current_user_can("payments.Add") ) {
        return _e("Sorry! you do not have permission to pay salary");
    }

    $emp_id = safe_input($_POST["paymentEmployee"]);
    $totalPaymentAmount = array_sum($_POST["paymentAmount"]);
    $accounts_balance = accounts_balance($_POST["paymentFromAccount"]);

    if(empty($_POST["paymentEmployee"])) {
        return _e("Please select employee.");
    } else if(!negative_value_is_allowed($_POST["paymentFromAccount"]) and $accounts_balance < $totalPaymentAmount ) {
        return _e("Payment amount is exceeded of account balance (%.2f)", number_format($accounts_balance, 2));
    }

    // Upload the attachment
    $paymentAttachment = NULL;
    if($_FILES["paymentAttachment"]["size"] > 0) {

        $paymentAttachment = easyUpload($_FILES["paymentAttachment"], "attachments/payments/cheque/" . date("M, Y"), safe_entities($_POST["paymentChequeNo"]) );

        if(!isset($paymentAttachment["success"])) {
            return _e($paymentAttachment);
        } else {
            $paymentAttachment = $paymentAttachment["fileName"];
        }
        
    }


    // Insert Salary into Payment
    $insertPaymentSalary = easyInsert(
        "payments",
        array (
            "payment_date"              => $_POST["paymentDate"],
            "payment_to_employee"       => $_POST["paymentEmployee"],
            "payment_amount"            => $totalPaymentAmount,
            "payment_status"            => "Complete",
            "payment_from"              => $_POST["paymentFromAccount"],
            "payment_description"       => $_POST["paymentDescription"],
            "payment_method"            => $_POST["paymentMethods"],
            "payment_cheque_no"         => empty($_POST["paymentChequeNo"]) ? NULL : $_POST["paymentChequeNo"], 
            "payment_cheque_date"       => empty($_POST["paymentChequeDate"]) ? NULL : $_POST["paymentChequeDate"],
            "payment_attachement"       => $paymentAttachment,
            "payment_reference"         => payment_reference("salary"),
            "payment_type"              => 'Salary',
            "payment_made_by"           => $_SESSION["uid"]
        ),
        array (
            "is_trash = 0 and payment_date" => $_POST["paymentDate"],
            " AND payment_to_employee"      => $_POST["paymentEmployee"],
            " AND payment_amount"           => $totalPaymentAmount
        ),
        true 
    );


    if(isset($insertPaymentSalary["status"]) and $insertPaymentSalary["status"] === "success" ) {

        // Insert payment items if successfully inserted in payments table
        foreach($_POST["paymentsType"] as $key => $PaymentType) {

            // Insert payment items
            easyInsert(
                "payment_items",
                array (
                    "payment_items_payments_id" => $insertPaymentSalary["last_insert_id"],
                    "payment_items_date"        => $_POST["paymentDate"],
                    "payment_items_type"        => $PaymentType,
                    "payment_items_employee"    => $_POST["paymentEmployee"],
                    "payment_items_amount"      => $_POST["paymentAmount"][$key],
                    "payment_items_accounts"    => $_POST["paymentFromAccount"],
                    "payment_items_description" => $_POST["salaryPaymentNote"][$key],
                    "payment_items_made_by"     => $_SESSION["uid"]
                )
            );

        }

        // Update employee payable Salary, Overtime and Bonus
        $updatePayableSalary = easyUpdate(
          "employees",
          array (
            "emp_payable_salary"    => getEmployeePayableAmount($emp_id, "salary"),
            "emp_payable_overtime"  => getEmployeePayableAmount($emp_id, "overtime"),
            "emp_payable_bonus"     => getEmployeePayableAmount($emp_id, "bonus")
          ),
          array (
              "emp_id"    => $emp_id
          )
        );

        // Update Accounts Balance
        updateAccountBalance($_POST["paymentFromAccount"]);

        $successMsg = sprintf(__("Salary successfully paid. Please <a %s>click here to print</a> the receipt."), "onClick='BMS.MAIN.printPage(this.href, event);' href='".full_website_address()."/invoice-print/?autoPrint=true&invoiceType=paymentReceipt&id={$insertPaymentSalary['last_insert_id']}'");

        echo "<div class='alert alert-success'>{$successMsg}</div>";

    } else {
        _e($insertPaymentSalary);
    }

}


/*************************** Expenses List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "expensesList") {

    if( !current_user_can("payments.View") ) {
        return _e("Sorry! you do not have permission to view expense list");
    }
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "payment_date",
        "payment_id",
        "",
        "payment_amount",
        "payment_from",
        "payment_description",
        "payment_status",
        "payment_method"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "payments",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];
 
    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if(!empty($requestData["search"]["value"])) {  // get data with search
      
        $getData = easySelect(
            "payments as payment",
            "payment_id, payment_reference, payment_status, if(item_description is not null AND item_description != '', item_description, combine_description(payment_description, item_description)) as payment_description, company_name, emp_firstname, emp_lastname, emp_PIN, accounts_name, payment_date, payment_amount, payment_from, payment_method",
            array (
                "left join {$table_prefix}accounts on payment_from = accounts_id",
                "left join {$table_prefix}companies on payment_to_company = company_id",
                "left join {$table_prefix}employees on payment_to_employee = emp_id",
                "left join ( select payment_items_payments_id, group_concat(payment_items_description SEPARATOR ', ' ) as item_description from {$table_prefix}payment_items group by payment_items_payments_id ) as payment_items on payment_items_payments_id = payment_id"
            ),
            array (
                "payment.is_trash = 0 and (company_name LIKE" => $requestData['search']['value'] . "%",
                " OR emp_firstname LIKE" => $requestData['search']['value'] . "%",
                ")"
            ),
            array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir'],
                "emp_firstname" => $requestData['order'][0]['dir'],
            ),
            array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        );
    
        $totalFilteredRecords = $getData ? $getData["count"] : 0;
  
    } else if(!empty($requestData["columns"][1]['search']['value']) or !empty($requestData["columns"][2]['search']['value']) or !empty($requestData["columns"][3]['search']['value']) or !empty($requestData["columns"][8]['search']['value']) ) { // Get data with search by column
        
        
        $dateRange[0] = "";
        $dateRange[1] = "";
        if(!empty($requestData["columns"][1]['search']['value'])) {
            $dateRange = explode(" - ", safe_input($requestData["columns"][1]['search']['value']));
        }

        $getData = easySelect(
            "payments as payment",
            "payment_id, payment_reference, payment_status, if(item_description is not null AND item_description != '', item_description, combine_description(payment_description, item_description)) as payment_description, company_name, emp_firstname, emp_lastname, emp_PIN, accounts_name, payment_date, payment_amount, payment_from, payment_method",
            array (
                "left join {$table_prefix}accounts on payment_from = accounts_id",
                "left join {$table_prefix}companies on payment_to_company = company_id",
                "left join {$table_prefix}employees on payment_to_employee = emp_id",
                "left join ( select payment_items_payments_id, group_concat(payment_items_description SEPARATOR ', ' ) as item_description from {$table_prefix}payment_items group by payment_items_payments_id ) as payment_items on payment_items_payments_id = payment_id"
            ),
            array (
              "payment.is_trash = 0",
              " and (company_name LIKE '". safe_input($requestData["columns"][3]['search']['value']) ."%' ",
              " OR concat(emp_firstname, ' ', emp_lastname) LIKE '%". safe_input($requestData["columns"][3]['search']['value']) ."%'",
              " OR emp_PIN" => $requestData["columns"][3]['search']['value'],
              $requestData["columns"][3]['search']['value'] ? "" : " OR payment_to_company is null or payment_to_employee is null", // this line is required for while searching if the paid to column is null
             // if the 3rd (Actually 4th) column empty then execute this query OR payment_to_company is null or payment_to_employee is null
              ")",
              " AND payment_reference LIKE" => $requestData["columns"][2]['search']['value'] . "%",
              " AND payment_method" => $requestData["columns"][8]['search']['value'],
              " AND payment_date BETWEEN '{$dateRange[0]}' and '{$dateRange[1]}'"
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
          "payments as payment",
          "payment_id, payment_reference, payment_status, if(item_description is not null AND item_description != '', item_description, combine_description(payment_description, item_description)) as payment_description, item_description, company_name, emp_firstname, emp_lastname, emp_PIN, accounts_name, payment_date, payment_amount, payment_from, payment_method",
          array (
            "left join {$table_prefix}accounts on payment_from = accounts_id",
            "left join {$table_prefix}companies on payment_to_company = company_id",
            "left join {$table_prefix}employees on payment_to_employee = emp_id",
            "left join ( select payment_items_payments_id, group_concat(payment_items_description SEPARATOR ', ' ) as item_description from {$table_prefix}payment_items group by payment_items_payments_id ) as payment_items on payment_items_payments_id = payment_id"
          ),
          array("payment.is_trash = 0"),
          array (
              $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir'],
              " emp_firstname" => $requestData['order'][0]['dir']
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
            $emp_PIN = !empty($value['emp_PIN']) ? " ({$value['emp_PIN']})" : "";
            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["payment_date"];
            $allNestedData[] = $value["payment_reference"];
            $allNestedData[] = $value["company_name"] . $value["emp_firstname"] . ' ' . $value["emp_lastname"] . $emp_PIN ;
            $allNestedData[] = $value["payment_amount"];
            $allNestedData[] = $value["accounts_name"];
            $allNestedData[] = $value["payment_description"];
            $allNestedData[] = $value["payment_status"];
            $allNestedData[] = $value["payment_method"];
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a <a onClick=\'BMS.MAIN.printPage(this.href, event);\' target="_blank" href="'. full_website_address() .'/invoice-print/?autoPrint=true&invoiceType=paymentReceipt&id='. $value["payment_id"] .'"><i class="fa fa-print"></i> Print Receipt</a></li>
                                    <li><a target="_blank" href="'. full_website_address() .'/invoice-print/?invoiceType=paymentReceipt&id='. $value["payment_id"] .'"><i class="fa fa-eye"></i> View Receipt</a></li>
                                    <li><a class="'. ( current_user_can("payments.Delete") ? "" : "restricted " ) .'deleteEntry" href="'. full_website_address() . '/xhr/?module=expenses&page=deletePayment" data-to-be-deleted="'. $value["payment_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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


/***************** Delete Payments ****************/
if(isset($_GET['page']) and $_GET['page'] == "deletePayment") {

    if(current_user_can("payments.Delete") !== true) {
        echo '{
            "title": "Sorry!",
            "text": "'. __("You do not have permission to delete payment items.") .'",
            "showConfirmButton": true,
            "showCloseButton": true,
            "timer": false,
            "toast": false,
            "icon": "error"
        }';
        return;
    }

    // Select the account where the payment made from
    $deletedPaymentData = easySelect(
        "payments",
        "payment_from, payment_to_employee, payment_purchase_id, payment_type",
        array(),
        array(
            "payment_id" => $_POST["datatoDelete"]
        )
    )["data"][0];


    // Delete the payment.
    $deleteData = easyDelete(
        "payments",
        array(
            "payment_id " => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {
        
        // Update Accounts Balance
        updateAccountBalance( $deletedPaymentData["payment_from"] );

        if( $deletedPaymentData["payment_to_employee"] !== NULL or $deletedPaymentData["payment_to_employee"] > 0 ) {

            $emp_id = $deletedPaymentData["payment_to_employee"];
            
            // Update employee payable Salary, Overtime and Bonus
            $updatePayableSalary = easyUpdate(
                "employees",
                array (
                    "emp_payable_salary"    => getEmployeePayableAmount($emp_id, "salary"),
                    "emp_payable_overtime"  => getEmployeePayableAmount($emp_id, "overtime"),
                    "emp_payable_bonus"     => getEmployeePayableAmount($emp_id, "bonus")
                ),
                array (
                    "emp_id"    => $emp_id
                )
            );

        } 
        
        // Update purchase payments when delete only the payments
        if( $deletedPaymentData["payment_purchase_id"] !== NULL or $deletedPaymentData["payment_purchase_id"] > 0 ) {

            // Get total payment amount regarding this purchase
            $totalPurchasePayment = 0;

            $getPurchasePaidAmount = easySelectD("SELECT 
                        sum(payment_amount) as payment_amount 
                    from {$table_prefix}payments 
                    where is_trash = 0 and payment_purchase_id = '{$deletedPaymentData["payment_purchase_id"]}' 
                    group by payment_purchase_id"
            );

            if($getPurchasePaidAmount !== false) {
                $totalPurchasePayment = $getPurchasePaidAmount["data"][0]["payment_amount"];
            }

            // Get purchase grand total
            $purchaseGrandTotal = easySelectD("SELECT purchase_grand_total from {$table_prefix}purchases where purchase_id = {$deletedPaymentData["payment_purchase_id"]}")["data"][0]["purchase_grand_total"];

            $purchaseDue = $purchaseGrandTotal - $totalPurchasePayment;

            // Generate the payment status
            $purchasePaymentStatus = "due";
            if($purchaseGrandTotal <= $totalPurchasePayment) {

                $purchasePaymentStatus = "paid";

            } else if($purchaseGrandTotal > $totalPurchasePayment and $totalPurchasePayment > 0) {

                $purchasePaymentStatus = "partial";

            }
            
            // Update the purchase
            easyUpdate(
                "purchases",
                array(
                    "purchase_paid_amount"      => $totalPurchasePayment,
                    "purchase_due"              => $purchaseDue,
                    "purchase_payment_status"   => $purchasePaymentStatus
                ),
                array(
                    "purchase_id"   => $deletedPaymentData["payment_purchase_id"]
                )
            );

        }
        
        echo '{
            "title": "'. __("The payment item has been deleted successfully.") .'"
        }';

    }

}


/*************************** Payment Return List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "paymentsReturnList") {

    if( !current_user_can("payments_return.View") ) {
        return _e("Sorry! you do not have permission to view payment return list");
    }
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "payments_return_date",
        ""
    );
    
    // Count Total recrods
    $paymentReturn = easySelectA(array(
        "table" => "payments_return",
        "where" => array(
            "is_trash = 0"
        )
    ));
    $totalFilteredRecords = $totalRecords = $paymentReturn ? $paymentReturn["count"] : 0;

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if(  !empty($requestData["search"]["value"]) or
        !empty($requestData["columns"][1]['search']['value']) or
        !empty($requestData["columns"][2]['search']['value']) or
        !empty($requestData["columns"][3]['search']['value'])
    
    ) {  // get data with search


        $dateRange[0] = "";
        $dateRange[1] = "";
        if(!empty($requestData["columns"][1]['search']['value'])) {
            $dateRange = explode(" - ", safe_input($requestData["columns"][1]['search']['value']));
        }


        $getData = easySelectA(array(
            "table"     => "payments_return as payments_return",
            "fields"    => "payments_return_id, payments_return_date, payments_return_type, emp_firstname, emp_lastname, emp_PIN, accounts_name, customer_name, company_name, round(payments_return_amount, 2) as payments_return_amount, payments_return_description",
            "join"      => array(
                "left join {$table_prefix}employees on payments_return_emp_id = emp_id",
                "left join {$table_prefix}companies on company_id = payments_return_company_id",
                "left join {$table_prefix}customers on customer_id = payments_return_customer_id",
                "left join {$table_prefix}accounts on payments_return_accounts = accounts_id"
            ),
            "where" => array(
                "payments_return.is_trash=0 AND (",
                " emp_firstname LIKE '". safe_input($requestData["columns"][3]['search']['value']) ."%'",
                " OR emp_PIN" => $requestData["columns"][3]['search']['value'],
                " OR customer_name LIKE" => $requestData["columns"][3]['search']['value'] . "%",
                " OR company_name LIKE" => $requestData["columns"][3]['search']['value'] . "%",
                ")",
                " AND payments_return_type" => $requestData["columns"][2]['search']['value'],
                " AND date(payments_return_date) between '{$dateRange[0]}' and '{$dateRange[1]}'"
                
            ),
            "orderby"   => array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit" => array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));

        $totalFilteredRecords = $getData ? $getData["count"] : 0;

    } else { // Get data withouth search

        $getData = easySelectA(array(
            "table"     => "payments_return as payments_return",
            "fields"    => "payments_return_id, payments_return_date, payments_return_type, emp_firstname, emp_lastname, emp_PIN, accounts_name, customer_name, company_name, round(payments_return_amount, 2) as payments_return_amount, payments_return_description",
            "join"      => array(
                "left join {$table_prefix}employees on payments_return_emp_id = emp_id",
                "left join {$table_prefix}companies on company_id = payments_return_company_id",
                "left join {$table_prefix}customers on customer_id = payments_return_customer_id",
                "left join {$table_prefix}accounts on payments_return_accounts = accounts_id"
            ),
            "where" => array(
                "payments_return.is_trash=0"
            ),
            "orderby"   => array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit" => array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));

    }

    $allData = [];

    // Check if there have more then zero data
    if($getData !== false) {

        //print_r($getData);
        
        foreach($getData['data'] as $key => $value) {

            $paidFromTo = "";
            if($value["emp_firstname"] !== NULL) {
                $paidFromTo = $value["emp_firstname"] ." ". $value["emp_lastname"] . ' (' . $value["emp_PIN"] . ')';
            } else if($value["company_name"] !== NULL) {
                $paidFromTo = $value["company_name"];
            } elseif($value["customer_name"] !== NULL) {
                $paidFromTo = $value["customer_name"];
            }

            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["payments_return_date"];
            $allNestedData[] = $value["payments_return_type"];
            $allNestedData[] = $paidFromTo;
            $allNestedData[] = $value["accounts_name"];
            $allNestedData[] = $value["payments_return_amount"];
            $allNestedData[] = $value["payments_return_description"];
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=expenses&page=deletePaymentReturn" data-to-be-deleted="'. $value["payments_return_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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



/************************** Add Salary **********************/
if(isset($_GET['page']) and $_GET['page'] == "addSalary") {
  
    // Include the modal header
    modal_header("Add Salary", full_website_address() . "/xhr/?module=expenses&page=addMonthlySalary");
    
    ?>
      <div class="box-body">

        <div class="row">
            <div class="form-group col-md-6 required">
                <label for="employeeId"><?= __("Employee"); ?></label>
                <select name="employeeId" id="employeeId" select2-minimum-input-length="1" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=employeeList" style="width: 100%;" required>
                    <option value=""><?= __("Select Employee"); ?>....</option>
                </select>
            </div>
            <div class="form-group col-md-6 required">
                <label for="salaryAddDate"><?= __("Date:"); ?></label>
                <input type="text" name="salaryAddDate" id="salaryAddDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker" required>
            </div>
        </div>
        
        <div class="row">
            <div class="form-group col-md-6 required">
                <label for="addSalaryMonth"><?= __("Salary / Installment Month"); ?></label>
                <select name="addSalaryMonth" id="addSalaryMonth" class="form-control" style="width: 100%">
                    <?php
                        $salaryMonth = array (
                            "01"   => "January",
                            "02"   => "February",
                            "03"   => "March",
                            "04"   => "April",
                            "05"   => "May",
                            "06"   => "June",
                            "07"   => "July",
                            "08"   => "August",
                            "09"   => "September",
                            "10"   => "October",
                            "11"   => "November",
                            "12"   => "December"
                        );
                        
                        foreach($salaryMonth as $monthKey => $monthValue) {
                            $selectedMonth = (date("m") == $monthKey) ? "selected" : "";
                            echo "<option $selectedMonth value='{$monthKey}'>{$monthValue}</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="form-group col-md-6 required">
                <label for="salaryYear"><?= __("Salary / Installment Year"); ?></label>
                <input type="number" name="salaryYear" id="salaryYear" value="<?php echo date("Y"); ?>" onclick="this.select();" class="form-control" required>
            </div>
        </div>
        
        <div class="row">
            <div class="form-group col-md-6 required">
                <label for="salaryTypes"><?= __("Salary Type"); ?></label>
                <select name="salaryTypes" id="salaryTypes" class="form-control select2" style="width: 100%;" required>
                    <?php
                        $salaryType = array("Salary", "Overtime", "Bonus");
                        
                        foreach($salaryType as $salaryType) {
                            echo "<option value='{$salaryType}'>{$salaryType}</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="form-group col-md-6 required">
                <label for="salaryAmount"><?= __("Amount"); ?></label>
                <input onclick="this.select();" type="number" name="salaryAmount" id="salaryAmount" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label for="salaryDescription"><?= __("Description"); ?></label>
            <textarea name="salaryDescription" id="salaryDescription" rows="3" class="form-control"></textarea>
        </div>

        <div class="box box-info">
            <div class="box-header">
                <h3 class="box-title"><?= __("Loan Installment"); ?></h3>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="installmentAmount"><?= __("Amount:"); ?> </label>
                    <div class="col-sm-10">
                        <span id="installmentAmount">0.00</span>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="installmentOption"><?= __("Options:"); ?> </label>
                    <div class="col-sm-10">
                        <select name="installmentOption" id="installmentOption" class="form-control select2" style="width: 100%">
                            <option value=""><?= __("Selece One"); ?>...</option>
                            <option value="2"><?= __("Don't cut off Installment in current month"); ?></option>
                            <option value="3"><?= __("Cut off all loan"); ?></option>
                        </select>
                    </div>
                </div>
            </div>
                
        </div>

        <div id="ajaxSubmitMsg"></div>

      </div>

      <!-- /Box body-->

      <script>

        $(document).off("change", "#employeeId, #installmentOption, #salaryTypes, #addSalaryMonth");
        $(document).on("change", "#employeeId, #installmentOption, #salaryTypes, #addSalaryMonth", function() {
            
            if($("#employeeId").val() === "") {
                alert("Please select employee");
                return;
            } 
            
            if($("#salaryTypes").val() != "Salary") {
                $("#installmentAmount").html(0);
                return;
            }

            $.post(
                "<?php echo full_website_address(); ?>/info/?module=data&page=getEmpLoanLoanData",
                {
                    empId: $("#employeeId").val(),
                    month: $("#addSalaryMonth").val(),
                    year: $("#salaryYear").val()
                },

                function (data, status) {

                    if(data == 0) {
                        $("#installmentAmount").html(0);
                        return;
                    }

                    var empLoanData = JSON.parse(data);

                    var installmentOption = $("#installmentOption").val();

                    // Add salary ammount
                    $("#salaryAmount").val(empLoanData.salary);

                    if ( installmentOption == 2 ) {

                        $("#installmentAmount").html(0);

                    } else if ( installmentOption == 3 ) {

                        $("#installmentAmount").html( Number(empLoanData.totalLoan) - Number(empLoanData.totalLoanPaid) );

                    } else {

                        $("#installmentAmount").html(Number(empLoanData.totalInstallmentAmount));

                    }                  

                }
            );
        })
      </script>

    <?php
  
    // Include the modal footer
    modal_footer("Add Salary");
  
}


/************************** Add Monthly Salary **********************/
if(isset($_GET['page']) and $_GET['page'] == "addMonthlySalary") {

    if( !current_user_can("Salary.Add") ) {
        return _e("Sorry! you do not have permission to add salary");
    }

    if(empty($_POST["employeeId"])) {
        return _e("Please select employee");
    } elseif(empty($_POST["addSalaryMonth"])) {
        return _e("Please select month");
    } elseif(empty($_POST["salaryYear"])) {
        return _e("Please enter year");
    } elseif(empty($_POST["salaryTypes"])) {
        return _e("Please select salary type");
    } elseif(empty($_POST["salaryAmount"])) {
        return _e("Please enter amount");
    }


    // Check if Don't need to cut off Installment in current month
    if($_POST["installmentOption"] != 2 and $_POST["salaryTypes"] === "Salary") {

        $emp_id = safe_input($_POST["employeeId"]);
        $month = safe_input($_POST["addSalaryMonth"]);
        $year = safe_input($_POST["salaryYear"]);

        $getLoanDetails = easySelectD(
            "select 
                loan_id, loan_amount, loan_installment_amount, 
                if(thisMonthInstallmentPayingStatus is null, 0, 1) as thisMonthInstallmentPayingStatus,
                if(loan_paid_amount is null, 0, loan_paid_amount) as loan_paid_amount
            from {$table_prefix}loan as loan
            left join (select 
                    loan_ids, 
                    sum(loan_installment_paying_amount) as loan_paid_amount 
                from {$table_prefix}loan_installment where is_trash = 0 group by loan_ids
            ) as totalPaidAmount on loan_id = totalPaidAmount.loan_ids
            left join (select 
                    loan_ids, 1 as thisMonthInstallmentPayingStatus
                from {$table_prefix}loan_installment where is_trash = 0 and MONTH(loan_installment_date) = {$month} and year(loan_installment_date) = {$year} group by loan_ids 
            ) as thisMonthStatus on loan_id = thisMonthStatus.loan_ids
            where loan.is_trash = 0 and loan_borrower = {$emp_id} and loan_installment_starting_from <= '{$year}-{$month}-01'
            and ( loan_paid_amount is null or loan_paid_amount < loan_amount)" 
            // loan_paid_amount can be NULL on left join if there is no recrods, for that the is null check.
            // We can also use HAVING cluese without using is null check. But it will raise a error with full_group_by mode.
        );

        // Check if there any Loan Data Exists
        if(isset($getLoanDetails["data"])) {

            foreach($getLoanDetails["data"] as $key => $loan) {

                // Total cut loan will be stored in this variable
                $totalDeductedLoanFromSalary = 0;

                $unpaidLoan = $loan["loan_amount"] - $loan["loan_paid_amount"];

                // Check if the loan installment already paid
                // and installmentOption is not 3. 
                // 3 means cut off All loan
                if($loan["thisMonthInstallmentPayingStatus"] == 1 and $_POST["installmentOption"] != 3) {
                    continue;
                }

                // Calculate unpaid loan
                if($_POST["installmentOption"] == 3) {
                    $totalDeductedLoanFromSalary = $unpaidLoan;
                } else {
                    $totalDeductedLoanFromSalary = ($unpaidLoan >= $loan["loan_installment_amount"]) ? $loan["loan_installment_amount"] : $unpaidLoan;
                }

                // Cut loan installment
                 easyInsert(
                    "loan_installment",
                    array (
                        "loan_ids"                          => $loan["loan_id"],
                        "loan_installment_provider"         => $_POST["employeeId"],
                        "loan_installment_paying_amount"    => $totalDeductedLoanFromSalary,
                        "loan_installment_description"      => "Installment deducted on salary ad",
                        "loan_installment_date"             => $_POST["salaryYear"] .'-'. $_POST["addSalaryMonth"] . '-01',
                        "loan_installment_paying_date"      => $_POST["salaryAddDate"] . " " . date("H:i:s"),
                        "loan_installment_receive_by"       => $_SESSION["uid"]
                    )
                );

            }
        }
    }
    

    // Insert salary
    $insertSalary = easyInsert(
        "salaries",
        array (
            "salary_emp_id"         => $_POST["employeeId"],
            "salary_type"           => $_POST["salaryTypes"],
            "salary_month"          => $_POST["salaryYear"] .'-'. $_POST["addSalaryMonth"] . '-01',
            "salary_amount"         => $_POST["salaryAmount"],
            "salary_description"    => $_POST["salaryDescription"],
            "salary_add_on"         => $_POST["salaryAddDate"] . " " . date("H:i:s"),
            "salary_add_by"         => $_SESSION["uid"]
        ),
        array (
            "salary_emp_id"     => $_POST["employeeId"],
            " AND salary_type"  => $_POST["salaryTypes"],
            " AND salary_month" => $_POST["salaryYear"] .'-'. $_POST["addSalaryMonth"] . '-01',
            " AND date(salary_add_on)" => $_POST["salaryAddDate"],
            " AND salary_amount" => $_POST["salaryAmount"],
        )
    );
    
    $emp_id = safe_input($_POST["employeeId"]);

    // Update employee payable Salary, Overtime and Bonus
    $updatePayableSalary = easyUpdate(
        "employees",
        array (
          "emp_payable_salary"    => getEmployeePayableAmount($emp_id, "salary"),
          "emp_payable_overtime"  => getEmployeePayableAmount($emp_id, "overtime"),
          "emp_payable_bonus"     => getEmployeePayableAmount($emp_id, "bonus")
        ),
        array (
            "emp_id"    => $emp_id
        )
      );

    if($insertSalary === true and $updatePayableSalary === true) {
        _s("%s successfully added", safe_entities($_POST['salaryTypes']) );
    } else {
        _e($insertSalary . $updatePayableSalary);
    }

}


/*************************** Added Salary List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "salaryList") {

    if( !current_user_can("Salary.View") ) {
        return _e("Sorry! you do not have permission to view salary list");
    }
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "salary_month",
        "emp_firstname",
        "salary_type",
        "salary_amount",
        "salary_description"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "salaries",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];
 
    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if(!empty($requestData["search"]["value"])) {  // get data with search
      
        $getData = easySelect(
            "salaries as salary",
            "salary_id, salary_month, emp_firstname, emp_lastname, emp_PIN, salary_type, salary_amount, salary_description",
            array (
              "left join {$table_prefix}employees on salary_emp_id = emp_id"
            ),
            array (
                "salary.is_trash = 0",
                " AND emp_firstname LIKE" => $requestData['search']['value'] . "%"
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
  
    } else if(!empty($requestData["columns"][1]['search']['value']) or !empty($requestData["columns"][2]['search']['value']) or !empty($requestData["columns"][3]['search']['value'])) { // Get data with search by column

        $dateRange = explode(" - ", safe_input($requestData["columns"][1]['search']['value']));
     
        $salaryMonthFrom = !empty($dateRange[0]) ? DateTime::createFromFormat('d M, Y', '01 '.$dateRange[0])->format('Y-m-d') : "";
        $salaryMonthTo = !empty($dateRange[1]) ? DateTime::createFromFormat('d M, Y', '28 '.$dateRange[1])->format('Y-m-d') : "";

        $getData = easySelect(
            "salaries as salary",
            "salary_id, emp_PIN, salary_month, emp_firstname, emp_lastname, emp_PIN, salary_type, salary_amount, salary_description",
            array (
              "left join {$table_prefix}employees on salary_emp_id = emp_id"
            ),
            array (
              "salary.is_trash = 0 and ( emp_firstname LIKE '". $requestData["columns"][2]['search']['value'] ."%' ",
              " OR emp_PIN" => $requestData["columns"][2]['search']['value'],
              ")",
              " AND salary_type" => $requestData["columns"][3]['search']['value'],
              " AND (salary_month BETWEEN '{$salaryMonthFrom}' and '{$salaryMonthTo}')"
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
            "salaries as salary",
            "salary_id, salary_month, emp_firstname, emp_lastname, emp_PIN, salary_type, salary_amount, salary_description",
            array (
            "left join {$table_prefix}employees on salary_emp_id = emp_id"
            ),
            array("salary.is_trash=0"),
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
            $allNestedData[] = date_format(date_create($value["salary_month"]), "F, Y");
            $allNestedData[] = $value["emp_firstname"] . ' ' . $value["emp_lastname"] . ' (' . $value["emp_PIN"] . ')';
            $allNestedData[] = $value["salary_type"];
            $allNestedData[] = $value["salary_amount"];
            $allNestedData[] = $value["salary_description"];
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=expenses&page=editMonthlySalary&id='. $value["salary_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                        <li><a class="'. ( current_user_can("salary.Delete") ? "" : "restricted " ) .'deleteEntry" href="'. full_website_address() . '/xhr/?module=expenses&page=deleteSalary" data-to-be-deleted="'. $value["salary_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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


/***************** Delete Salary ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteSalary") {

    if(current_user_can("salary.Delete") !== true) {
        echo '{
            "title": "Sorry!",
            "text": "'. __("You do not have permission to delete salary.") .'",
            "showConfirmButton": true,
            "showCloseButton": true,
            "timer": false,
            "toast": false,
            "icon": "error"
        }';
        return;
    }

    // Delete salary.
    $deleteData = easyDelete(
        "salaries",
        array(
            "salary_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {
        
        echo '{
            "title": "'. __("The salary has been deleted successfully.") .'"
        }';

    } 
}

/************************** Edit Salary **********************/
if(isset($_GET['page']) and $_GET['page'] == "editMonthlySalary") {

    if( !current_user_can("Salary.Edit") ) {
        return _e("Sorry! you do not have permission to edit salary");
    }
  
    // Include the modal header
    modal_header("Edit Salary", full_website_address() . "/xhr/?module=expenses&page=updateMonthlySalary");

    $selectSalary = easySelect(
        "salaries",
        "*",
        array (
            "left join {$table_prefix}employees on salary_emp_id = emp_id"
        ),
        array (
            "salary_id"     => $_GET["id"]
        )
    )["data"][0];
    
    ?>
      <div class="box-body">
     
        <div class="form-group required">
            <label for="employeeId"><?= __("Employee"); ?></label>
            <select id="employeeId" select2-minimum-input-length="1" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=employeeList" style="width: 100%;" disabled>
                <option value="<?php echo $selectSalary["emp_id"]; ?>"><?php echo $selectSalary["emp_firstname"] . ' ' . $selectSalary["emp_lastname"]; ?></option>
            </select>
        </div>
        <div class="form-group required">
            <label for="salaryMonth"><?= __("Salary Month"); ?></label>
            <select name="salaryMonth" id="salaryMonth" class="form-control select2" style="width: 100%">
                <?php
                    $salaryMonth = array (
                        "01"   => "January",
                        "02"   => "February",
                        "03"   => "March",
                        "04"   => "April",
                        "05"   => "May",
                        "06"   => "June",
                        "07"   => "July",
                        "08"   => "August",
                        "09"   => "September",
                        "10"   => "October",
                        "11"   => "November",
                        "12"   => "December"
                    );
                    
                    foreach($salaryMonth as $monthKey => $monthValue) {
                        $selectedMonth = (date("m", strtotime($selectSalary["salary_month"])) == $monthKey) ? "selected" : "";
                        echo "<option $selectedMonth value='{$monthKey}'>{$monthValue}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="salaryYear"><?= __("Salary Year"); ?></label>
            <input type="number" name="salaryYear" id="salaryYear" value="<?php echo date("Y", strtotime($selectSalary["salary_month"])); ?>" onclick="this.select();" class="form-control">
        </div>
        <div class="form-group required">
            <label for="salaryType"><?= __("Type"); ?></label>
            <select name="salaryType" id="salaryType" class="form-control select2" style="width: 100%;" required>
                <?php
                    $salaryType = array("Salary", "Overtime", "Bonus");
                    
                    foreach($salaryType as $salaryType) {
                        $selected = ($salaryType === $selectSalary["salary_type"]) ? "selected" : "";
                        echo "<option {$selected} value='{$salaryType}'>{$salaryType}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group required">
            <label for="salaryAmount"><?= __("Amount"); ?></label>
            <input type="number" onclick="this.select()" name="salaryAmount" value="<?php echo number_format($selectSalary["salary_amount"], 0, "", ""); ?>" id="salaryAmount" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="salaryDescription"><?= __("Description"); ?></label>
            <textarea name="salaryDescription" id="salaryDescription" rows="3" class="form-control"> <?php echo $selectSalary["salary_description"]; ?> </textarea>
        </div>
        <input type="hidden" name="salaryId" value="<?php echo safe_entities($_GET["id"]); ?>">
        <input type="hidden" name="employeeId" value="<?php echo $selectSalary["salary_emp_id"]; ?>">
        <div id="ajaxSubmitMsg"></div>

      </div>

      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Update Salary");
  
}


/************************** Update Monthly Salary **********************/
if(isset($_GET['page']) and $_GET['page'] == "updateMonthlySalary") {

    if( !current_user_can("Salary.Edit") ) {
        return _e("Sorry! you do not have permission to edit salary");
    }

    if(empty($_POST["salaryMonth"])) {
        return _e("Please select month");
    } elseif(empty($_POST["salaryYear"])) {
        return _e("Please enter year");
    } elseif(empty($_POST["salaryType"])) {
        return _e("Please select salary type");
    } elseif(empty($_POST["salaryAmount"])) {
        return _e("Please enter amount");
    }

    // Update salary
    $UpdateSalary = easyUpdate(
        "salaries",
        array (
            "salary_type"           => $_POST["salaryType"],
            "salary_month"          => $_POST["salaryYear"] .'-'. $_POST["salaryMonth"] . '-01',
            "salary_amount"         => $_POST["salaryAmount"],
            "salary_description"    => $_POST["salaryDescription"],
            "salary_update_by"      => $_SESSION["uid"]
        ),
        array (
            "salary_id" => $_POST["salaryId"]
        )
    );

    $emp_id = safe_input($_POST["employeeId"]);

    // Update employee payable Salary, Overtime and Bonus
    $updatePayableSalary = easyUpdate(
        "employees",
        array (
          "emp_payable_salary"    => getEmployeePayableAmount($emp_id, "salary"),
          "emp_payable_overtime"  => getEmployeePayableAmount($emp_id, "overtime"),
          "emp_payable_bonus"     => getEmployeePayableAmount($emp_id, "bonus")
        ),
        array (
            "emp_id"    => $emp_id
        )
    );

    if($UpdateSalary === true and $updatePayableSalary === true) {
        _s("%s successfully updated", safe_entities($_POST['salaryType']) );
    } else {
        _e($insertSalary . $updatePayableSalary);
    }

}


/************************** Add Bill **********************/
if(isset($_GET['page']) and $_GET['page'] == "newBill") {
  
    // Include the modal header
    modal_header("New Bill", full_website_address() . "/xhr/?module=expenses&page=addNewBill");
    
    ?>
    <div class="box-body">      
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group required">
                    <label for="billsDate"><?= __("Bill Date:"); ?></label>
                    <input type="text" name="billsDate" id="billsDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group required">
                    <label for="billCompany"><?= __("Company"); ?></label>
                    <select name="billCompany" id="billCompany" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=CompanyList" style="width: 100%;" required>
                        <option value=""><?= __("Select Company"); ?>....</option>
                    </select>
                </div>
            </div>
        </div>

        <br/>
        <div id="billRow" style="padding-top: 10px; padding-left: 10px;" class="bg-info">

            <div class="row">
                <!-- Column One -->
                <div class="col-md-4">
                    <div class="form-group required">
                        <label for="billCategory"><?= __("Category:"); ?></label>
                        <select name="billCategory[]" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=paymentCategoryList" style="width: 100%;" required>
                            <option value=""><?= __("Select category"); ?>....</option>
                        </select>
                    </div>
                </div>
                <!-- Column Two -->
                <div class="col-md-3">
                    <div class="form-group required">
                        <label for="billAmount"><?= __("Amount"); ?></label>
                        <input type="number" name="billAmount[]" class="form-control billAmount" required>
                    </div>
                </div>
                 <!-- Column Three -->
                 <div class="col-md-4">
                    <div class="form-group">
                        <label for="billNote"><?= __("Note (Naration)"); ?> <span style="font-size: 18px;"></span> </label>
                        <input type="text" name="billNote[]" class="form-control">
                    </div>
                </div>
            </div>

        </div>
        <div style="padding-top: 10px; padding-left: 10px; padding-bottom: 5px;" class="bg-info">
            <p style="font-weight: bold;"><?= __("Total Bill:"); ?>
                <span id="totalBillAmount">0.00</span>
            </p>
        </div> 

        <!-- Add payment row button -->
        <div style="width: 80px; display: block; margin: 20px auto;">
            <span style="cursor: pointer;" class="btn btn-primary" id="addBillRow">
                <i style="padding: 5px;" class="fa fa-plus-circle"></i>
            </span>
        </div>

        <div style="display: none;" class="form-group">
            <label for="paymentDescription"><?= __("Description:"); ?></label>
            <textarea name="paymentDescription" id="paymentDescription" rows="3" class="form-control"></textarea>
        </div>
    
        <div id="ajaxSubmitMsg"></div>

    </div>
         
    <script>


        /* Add Payment salary row 
         The first is used to remove envent listener */
        $(document).off("click","#addBillRow");
        $(document).on("click","#addBillRow", function() {

            var html = '<div class="row"> \
                    <div class="col-md-4"> \
                        <div class="form-group required"> \
                            <select name="billCategory[]" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=paymentCategoryList" style="width: 100%;" required> \
                                <option value="">Select category....</option> \
                            </select> \
                        </div> \
                    </div> \
                    <div class="col-md-3"> \
                        <div class="form-group required"> \
                            <input type="number" name="billAmount[]" class="form-control billAmount" required> \
                        </div> \
                    </div> \
                    <div class="col-md-4"> \
                        <div class="form-group"> \
                            <input type="text" name="billNote[]" class="form-control"> \
                        </div> \
                    </div> \
                    <div class="col-md-1"> \
                        <i style="cursor: pointer; padding: 10px 5px 0 0;" class="fa fa-trash-o removeBillRow"></i> \
                    </div> \
                </div> \
            </div>';

            $("#billRow").append(html);

        });

        /* Remove Bill row */
        $(document).on("click", ".removeBillRow", function() {
            $(this).closest(".row").css("background-color", "whitesmoke").hide("slow", function() {
                $(this).closest(".row").remove();
            });
        });


        $(document).on("blur keyup", ".billAmount", function() {

            var totalAmount = 0;
            $(".billAmount").each( function() {
                totalAmount += Number($(this).val());
            });

            $("#totalBillAmount").html(totalAmount);

        });

      </script>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Add Bill");
  
}


/************************** Add New Bill **********************/
if(isset($_GET['page']) and $_GET['page'] == "addNewBill") {

    if( !current_user_can("bills.Add") ) {
        return _e("Sorry! you do not have permission to add bill");
    }

    if(empty($_POST["billsDate"]))  {
        return _e("Please select bill date");
    } elseif(empty($_POST["billCompany"]))  {
        return _e("Please select company");
    }

    // Select last payment references
    $selectBillReference = easySelect(
        "bills",
        "bills_reference",
        array(),
        array (
            "bills_add_by"   => $_SESSION['uid'],
            " AND bills_reference LIKE 'BILL%'",
            " AND bills_reference is not null"
        ),
        array (
            "bills_id" => "DESC"
        ),
        array (
            "start" => 0,
            "length" => 1
        )
    );

    // Referense Format: SALE/POS/n
    $billReferences = "BILL/{$_SESSION['uid']}/";

    // check if there is minimum one records
    if($selectBillReference !== false) {
        $getLastReferenceNo = (int)explode($billReferences, $selectBillReference["data"][0]["bills_reference"])[1];
        $billReferences = $billReferences . ($getLastReferenceNo+1);
    } else {
        $billReferences = "BILL/{$_SESSION['uid']}/1";
    }

    $totalBill = array_sum($_POST["billAmount"]);
    // Insert Bill
    $insertBill = easyInsert(
        "bills",
        array (
            "bills_reference"   => $billReferences,
            "bills_date"        => $_POST["billsDate"],
            "bills_company_id"  => $_POST["billCompany"],
            "bills_amount"      => $totalBill,
            "bills_description" => $_POST["paymentDescription"],
            "bills_add_by"      => $_SESSION["uid"]
        ),
        array (
            "bills_date"        => $_POST["billsDate"],
            " AND bills_company_id"  => $_POST["billCompany"],
            " AND bills_amount"      => $totalBill
        ),
        true
    );


    if(isset($insertBill["status"]) and $insertBill["status"] === "success" ) {

        foreach($_POST["billAmount"] as $key => $billAmount) {

            // Insert Bill items
            easyInsert(
                "bill_items",
                array (
                    "bill_items_bill_id"    => $insertBill["last_insert_id"],
                    "bill_items_date"       => $_POST["billsDate"],
                    "bill_items_company"    => $_POST["billCompany"],
                    "bill_items_category"   => $_POST["billCategory"][$key],
                    "bill_items_amount"     => $billAmount,
                    "bill_items_note"       => $_POST["billNote"][$key],
                    "bill_items_add_by"     => $_SESSION["uid"]
                )
            );
    
        }

        echo "<div class='alert alert-success'>". sprintf(__("Bill successfully added. The Reference is: %s"), $billReferences) ."</div>";

    } else {
        _e($insertBill);
    }
}


/*************************** Bills List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "billsList") {

    if( !current_user_can("bills.View") ) {
        return _e("Sorry! you do not have permission to view bill list");
    }
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "bills_date",
        "company_name",
        "bills_reference",
        "bills_amount",
        "bills_description"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "bills",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if(!empty($requestData["search"]["value"])) {  // get data with search
        
        $getData = easySelect(
            "bills as bills",
            "bills_id, bills_date, bills_reference, bills_company_id, company_name, bills_amount, all_description",
            array (
                "left join {$table_prefix}companies on bills_company_id = company_id",
                "left join ( select bill_items_bill_id, group_concat(bill_items_note SEPARATOR ', ') as all_description from {$table_prefix}bill_items group by bill_items_bill_id ) as bill_items on bill_items_bill_id = bills_id"
            ),
            array (
                "bills.is_trash = 0 and company_name LIKE" => $requestData['search']['value'] . "%"
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
            "bills as bills",
            "bills_id, bills_date, bills_reference, bills_company_id, company_name, bills_amount, all_description",
            array (
                "left join {$table_prefix}companies on bills_company_id = company_id",
                "left join ( select bill_items_bill_id, group_concat(bill_items_note SEPARATOR ', ') as all_description from {$table_prefix}bill_items group by bill_items_bill_id ) as bill_items on bill_items_bill_id = bills_id"
            ),
            array("bills.is_trash = 0"),
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
            $allNestedData[] = $value["bills_date"];
            $allNestedData[] = $value["company_name"];
            $allNestedData[] = $value["bills_reference"];
            $allNestedData[] = $value["bills_amount"];
            $allNestedData[] = $value["all_description"];
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=expenses&page=companyBillDetails&bill_id='. $value["bills_id"] .'"  data-target="#modalDefault"><i class="fa fa-eye"></i> View Bill</a></li>
                                        <li><a class="'. ( current_user_can("bills.Delete") ? "" : "restricted " ) .'deleteEntry" href="'. full_website_address() . '/xhr/?module=expenses&page=deleteBills" data-to-be-deleted="'. $value["bills_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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


/***************** Delete bill ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteBills") {

    if(current_user_can("bills.Delete") !== true) {
        echo '{
            "title": "Sorry!",
            "text": "'. __("You do not have permission to delete Bills.") .'",
            "showConfirmButton": true,
            "showCloseButton": true,
            "timer": false,
            "toast": false,
            "icon": "error"
        }';
        return;
    }

    // Delete Bill.
    $deleteData = easyDelete(
        "bills",
        array(
            "bills_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {
        
        echo '{
            "title": "'. __("The Bill has been deleted successfully.") .'"
        }';

    } 
}



/************************** Bill Details **********************/
if(isset($_GET['page']) and $_GET['page'] == "companyBillDetails") {

    if( !current_user_can("bills.View") ) {
        return _e("Sorry! you do not have permission to view bill");
    }
  
    // Include the modal header
    modal_header("Bill Details", "");

    $selectBillItems = easySelect(
        "bill_items",
        "bill_items_category, payment_category_name, round(bill_items_amount, 2) as bill_items_amount, bill_items_note",
        array (
            "left join {$table_prefix}payments_categories on bill_items_category = payment_category_id"
        ),
        array (
            "bill_items_bill_id"     => $_GET["bill_id"]
        )
    )["data"];
    
    ?>
      <div class="box-body">

        <table class="table">
            <tr>
                <th><?= __("Category"); ?></th>
                <th><?= __("Amount"); ?></th>
                <th><?= __("Note"); ?></th>
            </tr>
            
        <?php 

            foreach($selectBillItems as $key => $billItems) {
                echo "<tr>";
                echo "<td>{$billItems['payment_category_name']}</td>";
                echo "<td>{$billItems['bill_items_amount']}</td>";
                echo "<td>{$billItems['bill_items_note']}</td>";
                echo "</tr>";
            }

        ?>

        </table>
    
      </div>
      <!-- /Box body-->

    <?php
  
}



/************************** Due Bill Pay **********************/
if(isset($_GET['page']) and $_GET['page'] == "dueBillPay") {

    // Include the modal header
    modal_header("Due Bill Payment", full_website_address() . "/xhr/?module=expenses&page=newDueBillPay");
    
    ?>
      <div class="box-body">      
        
        <div class="form-group required">
            <label for="dueBillPaymentDate"><?= __("Date:"); ?></label>
            <input type="text" name="dueBillPaymentDate" id="dueBillPaymentDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker" required>
        </div>
        <div class="form-group required">
            <label for="dueBillPaymentCompany"><?= __("Company"); ?></label>
            <select name="dueBillPaymentCompany" id="dueBillPaymentCompany" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=CompanyList" style="width: 100%;" required>
                <option value=""><?= __("Select Company"); ?>....</option>
            </select>
        </div>
        <!-- Show Bill details  -->
        <table id="dueBillDetails" class="table">
            <tbody>
                <tr class="bg-info">
                    <th class="text-right"><?= __("Opening Balance"); ?></th>
                    <th class="text-right"><?= __("Total Bill"); ?></th>
                    <th class="text-right"><?= __("Total Paid:"); ?></th>
                    <th class="text-right"><?= __("Adjustment:"); ?></th>
                    <th class="text-right"><?= __("Total Due:"); ?></th>
                </tr>
                <tr class="bg-info">
                    <td class="text-right">0.00</td>
                    <td class="text-right">0.00</td>
                    <td class="text-right">0.00</td>
                    <td class="text-right">0.00</td>
                    <td class="text-right">0.00</td>
                </tr>
            </tbody>
        </table>
        <br/>
        <div class="form-group required">
            <label for="dueBillPaymentAmount"><?= __("Amount"); ?></label>
            <input type="number" name="dueBillPaymentAmount" id="dueBillPaymentAmount" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="dueBillPaymentAccount"><?= __("Accounts"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="Which accounts the bill is paying from" class="fa fa-question-circle"></i>
            <select name="dueBillPaymentAccount" id="dueBillPaymentAccount" class="form-control select2" style="width: 100%;" required>
                <?php
                    
                    foreach($selectAccounts["data"] as $accounts) {
                        $selected = (isset($_SESSION['aid']) and $_SESSION['aid'] === $accounts['accounts_id']) ? "selected" : "";
                        echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="dueBillPaymentDescription"><?= __("Description:"); ?></label>
            <textarea name="dueBillPaymentDescription" id="dueBillPaymentDescription" rows="3" class="form-control"></textarea>
        </div>
        <div class="form-group required">
            <label for="dueBillPaymentMethod">Payment Method</label>
            <select name="dueBillPaymentMethod" id="dueBillPaymentMethod" class="form-control select2" style="width: 100%">
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
                <label for="dueBillPaymentChequeNo"><?= __("Cheque No"); ?></label>
                <input type="text" name="dueBillPaymentChequeNo" id="dueBillPaymentChequeNo" class="form-control">
            </div>
            <div class="form-group">
                <label for="dueBillPaymentChequeDate"><?= __("Cheque Date:"); ?></label>
                <input type="text" name="dueBillPaymentChequeDate" id="dueBillPaymentChequeDate" value="" class="form-control datePicker">
            </div>
            <div class="form-group">
                <label for="dueBillPaymentAttachment"><?= __("Attachment"); ?></label>
                <input type="file" name="dueBillPaymentAttachment" id="dueBillPaymentAttachment" class="form-control">
            </div>
        </div>
    
        <div id="ajaxSubmitMsg"></div>

      </div>
      
   
        <script>

        $(document).on("change", "#dueBillPaymentMethod", function() {
            if($("#dueBillPaymentMethod").val() == "Cheque") {
                $("#hiddenItem").css("display", "block");
            } else {
                $("#hiddenItem").css("display", "none");
            }
        });

        /* Show Due bill details */
        $(document).off("change", "#dueBillPaymentCompany");
        $(document).on("change", "#dueBillPaymentCompany", function() {

            $.post(
                "<?php echo full_website_address(); ?>/info/?module=data&page=getCompanyDueBillDetails",
                {
                    company_id: $("#dueBillPaymentCompany").val()
                },

                function(data, status) {
                    var companyDueBill = JSON.parse(data);
                    
                    $("#dueBillDetails > tbody > tr:nth-child(2) > td:nth-child(1)").html( tsd(companyDueBill.company_opening_balance) );
                    $("#dueBillDetails > tbody > tr:nth-child(2) > td:nth-child(2)").html( tsd(companyDueBill.bills_amount_sum) );
                    $("#dueBillDetails > tbody > tr:nth-child(2) > td:nth-child(3)").html( tsd(companyDueBill.payment_amount_sum) );
                    $("#dueBillDetails > tbody > tr:nth-child(2) > td:nth-child(4)").html( tsd(companyDueBill.adjustment_amount_sum) );
                    $("#dueBillDetails > tbody > tr:nth-child(2) > td:nth-child(5)").html( tsd( ( Number(companyDueBill.company_opening_balance) + Number(companyDueBill.bills_amount_sum) ) - ( Number(companyDueBill.payment_amount_sum) + Number(companyDueBill.adjustment_amount_sum) ) ) );

                }
            );

        });

      </script>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Pay Due Bill");

}


/************************** Bill Payment **********************/
if(isset($_GET['page']) and $_GET['page'] == "newDueBillPay") {

    if( !current_user_can("payments.Add") ) {
        return _e("Sorry! you do not have permission to pay due bill");
    }

    $accounts_balance = accounts_balance($_POST["dueBillPaymentAccount"]);

    if(empty($_POST["dueBillPaymentDate"])) {
        return _e("Please enter payment date");
    } else if(empty($_POST["dueBillPaymentCompany"])) {
        return _e("Please select company");
    } else if(empty($_POST["dueBillPaymentAccount"])) {
        return _e("Please select accounts");
    } else if(empty($_POST["dueBillPaymentAmount"])) {
        return _e("Please enter amount");
    } else if(empty($_POST["dueBillPaymentMethod"])) {
        return _e("Please select payment method");
    } else if($_POST["dueBillPaymentMethod"] === "Cheque" and empty($_POST["dueBillPaymentChequeNo"])) {
        return _e("Please enter check no.");
    }  else if(!negative_value_is_allowed($_POST["dueBillPaymentAccount"]) and $accounts_balance < $_POST["dueBillPaymentAmount"] ) {
        return _e("Payment amount is exceeded of account balance (%.2f)", number_format($accounts_balance, 2));
    }


    // Upload the attachment
    $paymentAttachment = NULL;
    if($_FILES["dueBillPaymentAttachment"]["size"] > 0) {

        $paymentAttachment = easyUpload($_FILES["dueBillPaymentAttachment"], "attachments/payments/cheque/" . date("M, Y"), safe_entities($_POST["dueBillPaymentChequeNo"]) );

        if(!isset($paymentAttachment["success"])) {
            return _e($paymentAttachment);
        } else {
            $paymentAttachment = $paymentAttachment["fileName"];
        }
        
    }



    // Payment reference for BILL
    $paymentReferences = payment_reference("bill");

    // Insert the Bill Payment
    $insertDueBillPay = easyInsert (
        "payments",
        array (
            "payment_date"              => $_POST["dueBillPaymentDate"],
            "payment_to_company"        => $_POST["dueBillPaymentCompany"],
            "payment_status"            => "Complete",
            "payment_amount"            => $_POST["dueBillPaymentAmount"],
            "payment_from"              => $_POST["dueBillPaymentAccount"],
            "payment_description"       => $_POST["dueBillPaymentDescription"],
            "payment_method"            => $_POST["dueBillPaymentMethod"],
            "payment_cheque_no"         => empty($_POST["dueBillPaymentChequeNo"]) ? NULL : $_POST["dueBillPaymentChequeNo"], 
            "payment_cheque_date"       => empty($_POST["dueBillPaymentChequeDate"]) ? NULL : $_POST["dueBillPaymentChequeDate"],
            "payment_attachement"       => $paymentAttachment,
            "payment_reference"         => $paymentReferences,
            "payment_type"              => "Due Bill",
            "payment_made_by"           => $_SESSION["uid"]
        ),
        array (
            "payment_date"                  => $_POST["dueBillPaymentDate"],
            " AND payment_amount"           => $_POST["dueBillPaymentAmount"],
            " AND payment_from"             => $_POST["dueBillPaymentAccount"],
            " AND payment_method"           => $_POST["dueBillPaymentMethod"],
            " AND payment_description"      => $_POST["dueBillPaymentDescription"],
            " AND payment_type"             => "Due Bill",
            " AND payment_to_company"       => $_POST["dueBillPaymentCompany"],
            " AND payment_made_by"          => $_SESSION["uid"]
        ),
        true
    );

    if(isset($insertDueBillPay["status"]) and $insertDueBillPay["status"] === "success" ) {

        // Insert payment items
        easyInsert(
            "payment_items",
            array (
                "payment_items_payments_id" => $insertDueBillPay["last_insert_id"],
                "payment_items_date"        => $_POST["dueBillPaymentDate"],
                "payment_items_type"        => "Bill",
                "payment_items_company"     => $_POST["dueBillPaymentCompany"],
                "payment_items_amount"      => $_POST["dueBillPaymentAmount"],
                "payment_items_accounts"    => $_POST["dueBillPaymentAccount"],
                "payment_items_description" => '',
                "payment_items_made_by"     => $_SESSION["uid"]
            )
        );
        

        // Update Accounts Balance
        updateAccountBalance($_POST["dueBillPaymentAccount"]);

        $successMsg = sprintf(__("Payment successfully added. The reference number is: <strong>%s</strong>. Please <a %s>click here to print</a> the receipt."), $paymentReferences, "onClick='BMS.MAIN.printPage(this.href, event);' href='".full_website_address()."/invoice-print/?autoPrint=true&invoiceType=paymentReceipt&id={$insertDueBillPay['last_insert_id']}'");
        
        echo "<div class='alert alert-success'>{$successMsg}</div>";
        
    } else {
        _e($insertDueBillPay);
    }

}


/************************** Pay Advance Payment **********************/
if(isset($_GET['page']) and $_GET['page'] == "payAdvancePayment") {
  
    // Include the modal header
    modal_header("Pay Advance Payment", full_website_address() . "/xhr/?module=expenses&page=payNewAdvancePayment");
    
    ?>
      <div class="box-body">      
        
        <div class="form-group required">
            <label for="advancePaymentsDate"><?= __("Date:"); ?></label>
            <input type="text" name="advancePaymentsDate" id="advancePaymentsDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker" required>
        </div>
        <div class="form-group required">
            <label for="advancePaymentPaymentEmployee"><?= __("Employee"); ?></label>
            <select name="advancePaymentPaymentEmployee" id="advancePaymentPaymentEmployee" select2-minimum-input-length="1" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=employeeList" style="width: 100%;" required>
                <option value=""><?= __("Select Employee"); ?>....</option>
            </select>
        </div>
        <div class="form-group required">
            <label for="advancePaymentPaymentsFrom"><?= __("Accounts"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="Which accounts the advance is paying from" class="fa fa-question-circle"></i>
            <select name="advancePaymentPaymentsFrom" id="advancePaymentPaymentsFrom" class="form-control select2" style="width: 100%;" required>
                <?php
                    
                    foreach($selectAccounts["data"] as $accounts) {
                        $selected = (isset($_SESSION['aid']) and $_SESSION['aid'] === $accounts['accounts_id']) ? "selected" : "";
                        echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group required">
            <label for="advancePaymentAmount"><?= __("Amount"); ?></label>
            <input type="number" name="advancePaymentAmount" id="advancePaymentAmount" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="advancePaymentPaymentMethods"><?= __("Payment Method"); ?></label>
            <select name="advancePaymentPaymentMethods" id="advancePaymentPaymentMethods" class="form-control select2" style="width: 100%">
                <?php
                    $paymentMethod = array("Cash", "Bank Transfer", "Cheque", "Others");
                    
                    foreach($paymentMethod as $method) {
                        echo "<option value='{$method}'>{$method}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="advancePaymentPaymentDescription"><?= __("Description:"); ?></label>
            <textarea name="advancePaymentPaymentDescription" id="advancePaymentPaymentDescription" rows="3" class="form-control"></textarea>
        </div>
    
        <div id="ajaxSubmitMsg"></div>

      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Pay Advance Payment");
  
}


/************************** Pay Advance Payment **********************/
if(isset($_GET['page']) and $_GET['page'] == "payNewAdvancePayment") {

    if( !current_user_can("advance_bill_payments.Add") ) {
        return _e("Sorry! you do not have permission to add advance payment");
    }

    $accounts_balance = accounts_balance($_POST["advancePaymentPaymentsFrom"]);

    if(empty($_POST["advancePaymentsDate"]))  {
        return _e("Please select payment date");
    } elseif(empty($_POST["advancePaymentPaymentsFrom"]))  {
        return _e("Please select accounts");
    } elseif(empty($_POST["advancePaymentPaymentEmployee"]))  {
        return _e("Please select employee");
    } elseif(empty($_POST["advancePaymentAmount"]) and $_POST["advancePaymentAmount"] !== 0)  {
        return _e("Please Enter Amount");
    } else if(!negative_value_is_allowed($_POST["advancePaymentPaymentsFrom"]) and $accounts_balance < $_POST["advancePaymentAmount"] ) {
        return _e("Payment amount is exceeded of account balance (%.2f)", number_format($accounts_balance, 2));
    }

    // Select last payment references
    $selectAdvancePaymentReference = easySelect(
        "advance_payments",
        "advance_payment_reference",
        array(),
        array (
            "advance_payment_pay_by"   => $_SESSION['uid'],
            " AND advance_payment_reference is not null"
        ),
        array (
            "advance_payment_id" => "DESC"
        ),
        array (
            "start" => 0,
            "length" => 1
        )
    );

    // Referense Format: SALE/POS/n
    $paymentReferences = "ADVANCE/{$_SESSION['uid']}/";

    // check if there is minimum one records
    if($selectAdvancePaymentReference !== false) {
        $getLastReferenceNo = (int)explode($paymentReferences, $selectAdvancePaymentReference["data"][0]["advance_payment_reference"])[1];
        $paymentReferences = $paymentReferences . ($getLastReferenceNo+1);
    } else {
        $paymentReferences = "ADVANCE/{$_SESSION['uid']}/1";
    }



    // Insert Payment
    $payAdvancePayment = easyInsert(
        "advance_payments",
        array (
            "advance_payment_date"             => $_POST["advancePaymentsDate"],
            "advance_payment_reference"        => $paymentReferences,
            "advance_payment_pay_to"           => $_POST["advancePaymentPaymentEmployee"],
            "advance_payment_amount"           => $_POST["advancePaymentAmount"],
            "advance_payment_pay_from"         => $_POST["advancePaymentPaymentsFrom"],
            "advance_payment_description"      => $_POST["advancePaymentPaymentDescription"],
            "advance_payment_payment_method"   => $_POST["advancePaymentPaymentMethods"],
            "advance_payment_pay_by"           => $_SESSION["uid"]   
        ),
        array (
            "advance_payment_date"             => $_POST["advancePaymentsDate"],
            " AND advance_payment_pay_to"      => $_POST["advancePaymentPaymentEmployee"],
            " AND advance_payment_amount"      => $_POST["advancePaymentAmount"]
        ),
        true
    );


    if(isset($payAdvancePayment["status"]) and $payAdvancePayment["status"] === "success" ) { 
        // Update Accounts Balance
        updateAccountBalance($_POST["advancePaymentPaymentsFrom"]);

        $successMsg = sprintf(
                            __("Advance payment successfully added. The reference is: %s. Please <a %s>click here to print</a> the receipt."), 
                            $paymentReferences, 
                            "onClick='BMS.MAIN.printPage(this.href, event);' href='".full_website_address()."/invoice-print/?autoPrint=true&invoiceType=payAdvance&id={$payAdvancePayment['last_insert_id']}'"
                        );

        echo "<div class='alert alert-success'>{$successMsg}</div>";

    } else {
        _e($payAdvancePayment);
    }
}


/*************************** Advance Payment Payments Details ***********************/
if(isset($_GET['page']) and $_GET['page'] == "advancePaymentOverview") {

    if( !current_user_can("advance_bill_payments.View") ) {
        return _e("Sorry! you do not have permission to view advance payment");
    }
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "emp_firstname",
        "",
        "",
        "",
        ""
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "advance_payments",
        "fields" => "count(distinct advance_payment_pay_to) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];
 
    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if(
        !empty($requestData["search"]["value"]) or
        !empty($requestData["columns"][1]['search']['value'])
    
    ) {  // get data with search

        $dateRange[0] = "";
        $dateRange[1] = "";
        if(!empty($requestData["columns"][1]['search']['value'])) {
            $dateRange = explode(" - ", safe_input($requestData["columns"][1]['search']['value']));
        }
      
        $getData = easySelect(
            "employees",
            "emp_id, emp_firstname, emp_lastname, emp_PIN, if(advance_payment_amount_sum is null, 0, advance_payment_amount_sum) as advance_paid_amount, if(payments_return_amount_sum is null, 0, payments_return_amount_sum) + if(payment_amount_sum is null, 0, payment_amount_sum) as advance_adjust_amount",
            array (
                "left join ( select advance_payment_pay_to, sum(advance_payment_amount) as advance_payment_amount_sum from {$table_prefix}advance_payments where is_trash = 0 and advance_payment_date between '{$dateRange[0]}' and '{$dateRange[1]}' group by advance_payment_pay_to ) as get_advance_payments on advance_payment_pay_to = emp_id",
                "left join ( select payment_to_employee, sum(payment_amount) as payment_amount_sum from {$table_prefix}payments where is_trash = 0 and payment_date between '{$dateRange[0]}' and '{$dateRange[1]}' and payment_type = 'Advance Adjustment' group by payment_to_employee ) as get_payments on payment_to_employee = emp_id",
                "left join ( select payments_return_emp_id, sum(payments_return_amount) as payments_return_amount_sum from {$table_prefix}payments_return where is_trash = 0 and date(payments_return_date) between '{$dateRange[0]}' and '{$dateRange[1]}' group by payments_return_emp_id ) as get_advance_return on payments_return_emp_id = emp_id "
            ),
            array (
                "advance_payment_amount_sum > 0",
                " AND ( emp_firstname LIKE  '". safe_input($requestData['search']['value']) ."%' ",
                " OR emp_lastname LIKE" => $requestData['search']['value'] . "%",
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
            "employees",
            "emp_id, emp_firstname, emp_lastname, emp_PIN, if(advance_payment_amount_sum is null, 0, advance_payment_amount_sum) as advance_paid_amount, if(payments_return_amount_sum is null, 0, payments_return_amount_sum) + if(payment_amount_sum is null, 0, payment_amount_sum) as advance_adjust_amount",
            array (
                "left join ( select advance_payment_pay_to, sum(advance_payment_amount) as advance_payment_amount_sum from {$table_prefix}advance_payments where is_trash = 0 group by advance_payment_pay_to ) as get_advance_payments on advance_payment_pay_to = emp_id",
                "left join ( select payment_to_employee, sum(payment_amount) as payment_amount_sum from {$table_prefix}payments where is_trash = 0 and payment_type = 'Advance Adjustment' group by payment_to_employee ) as get_payments on payment_to_employee = emp_id",
                "left join ( select payments_return_emp_id, sum(payments_return_amount) as payments_return_amount_sum from {$table_prefix}payments_return where is_trash = 0 group by payments_return_emp_id ) as get_advance_return on payments_return_emp_id = emp_id "
            ),
            array (
                "advance_payment_amount_sum > 0"
            ),
            array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir'],
                " emp_firstname" => $requestData['order'][0]['dir']
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
            $allNestedData[] = $value["emp_firstname"] . ' ' . $value["emp_lastname"] . ' (' . $value["emp_PIN"] . ')';
            $allNestedData[] = $value["advance_paid_amount"];
            $allNestedData[] = $value["advance_adjust_amount"];
            $allNestedData[] = $value["advance_paid_amount"] - $value["advance_adjust_amount"];

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


/*************************** advancePaymentList ***********************/
if(isset($_GET['page']) and $_GET['page'] == "advancePaymentList") {

    if( !current_user_can("advance_bill_payments.View") ) {
        return _e("Sorry! you do not have permission to view advance payment");
    }
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "advance_payment_date",
        "advance_payment_reference",
        "emp_firstname",
        "accounts_name",
        "advance_payment_amount",
        "advance_payment_description"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "advance_payments",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if( !empty($requestData["search"]["value"]) or
        !empty($requestData["columns"][3]['search']['value'])
    
    ) {  // get data with search

        $getData = easySelectA(array(
            "table"     => "advance_payments as advance_payment",
            "fields"    => "advance_payment_id, advance_payment_date, advance_payment_reference, emp_firstname, emp_lastname, emp_PIN, accounts_name, advance_payment_amount, advance_payment_description",
            "join"      => array(
                "left join {$table_prefix}employees on emp_id = advance_payment_pay_to",
                "left join {$table_prefix}accounts on accounts_id = advance_payment_pay_from"
            ),
            "where" => array(
                "advance_payment.is_trash=0",
                " AND ( emp_firstname like '%". safe_input($requestData['search']['value']) ."%' ",
                " or accounts_name like" => "%".$requestData['search']['value'] . "%",
                " or advance_payment_description like"  => "%".$requestData['search']['value'] . "%",
                ")",
                " AND advance_payment_pay_to"  => $requestData["columns"][3]['search']['value']
            ),
            "orderby"   => array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit" => array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));

        $totalFilteredRecords = $getData ? $getData["count"] : 0;

    } else { // Get data withouth search


        $getData = easySelectA(array(
            "table"     => "advance_payments as advance_payment",
            "fields"    => "advance_payment_id, advance_payment_date, advance_payment_reference, emp_firstname, emp_lastname, emp_PIN, accounts_name, advance_payment_amount, advance_payment_description",
            "join"      => array(
                "left join {$table_prefix}employees on advance_payment_pay_to = emp_id",
                "left join {$table_prefix}accounts on advance_payment_pay_from = accounts_id"
            ),
            "where" => array(
                "advance_payment.is_trash=0"
            ),
            "orderby"   => array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit" => array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));

    }

    $allData = [];

    // Check if there have more then zero data
    if($getData !== false) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["advance_payment_date"];
            $allNestedData[] = $value["advance_payment_reference"];
            $allNestedData[] = $value["emp_firstname"] ." ". $value["emp_lastname"] . ' (' . $value["emp_PIN"] . ')';
            $allNestedData[] = $value["accounts_name"];
            $allNestedData[] = $value["advance_payment_amount"];
            $allNestedData[] = $value["advance_payment_description"];
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a <a onClick=\'BMS.MAIN.printPage(this.href, event);\' href="'. full_website_address() .'/invoice-print/?autoPrint=true&invoiceType=payAdvance&id='. $value["advance_payment_id"] .'"><i class="fa fa-print"></i> Print</a></li>
                                    <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=expenses&page=editAdvancePayment&id='. $value["advance_payment_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=expenses&page=deleteAdvancePayment" data-to-be-deleted="'. $value["advance_payment_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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


/***************** Delete Advance Payment Payments ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteAdvancePayment") {

    if(current_user_can("advance_bill_payments.Delete") !== true) {
        echo '{
            "title": "Sorry!",
            "text": "'. __("You do not have permission to delete advance payment.") .'",
            "showConfirmButton": true,
            "showCloseButton": true,
            "timer": false,
            "toast": false,
            "icon": "error"
        }';
        return;
    }

    // Select the accounts
    $AdvancePaymentAccounts = easySelectA(array(
        "table"     => "advance_payments",
        "fields"    => "advance_payment_pay_from",
        "where"     => array(
            "advance_payment_id" => $_POST["datatoDelete"]
        )
    ))["data"][0]["advance_payment_pay_from"];
    
    
    // delete the advance payment
    $deleteAdvancePayment = easyDelete(
        "advance_payments",
        array(
            "advance_payment_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteAdvancePayment === true) {
        updateAccountBalance($AdvancePaymentAccounts);
            
        echo '{
            "title": "'. __("Has been deleted successfully.") .'"
        }';
    }

}


/************************** Edit Advance Payment **********************/
if(isset($_GET['page']) and $_GET['page'] == "editAdvancePayment") {

    if( !current_user_can("advance_bill_payments.Edit") ) {
        return _e("Sorry! you do not have permission to edit advance payment");
    }
  
    // Include the modal header
    modal_header("Edit Advance Payment", full_website_address() . "/xhr/?module=expenses&page=updateAdvancePayment");
    
    $AdvancePayment = easySelectA(array(
        "table"     => "advance_payments",
        "fields"    => "advance_payment_id, advance_payment_date, advance_payment_reference, advance_payment_pay_to, emp_firstname, emp_lastname, advance_payment_pay_from, advance_payment_amount, advance_payment_description, advance_payment_payment_method",
        "join"      => array(
            "left join {$table_prefix}employees on advance_payment_pay_to = emp_id"
        ),
        "where"     => array(
            "advance_payment_id" => $_GET["id"]
        )
    ))["data"][0];

    ?>
      <div class="box-body">      
        
        <div class="form-group required">
            <label for="advancePaymentsDate"><?= __("Date:"); ?></label>
            <input type="text" name="advancePaymentsDate" id="advancePaymentsDate" value="<?php echo $AdvancePayment["advance_payment_date"]; ?>" class="form-control datePicker" required>
        </div>
        <div class="form-group required">
            <label for="advancePaymentPaymentEmployee"><?= __("Employee"); ?></label>
            <select name="advancePaymentPaymentEmployee" id="advancePaymentPaymentEmployee" select2-minimum-input-length="1" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=employeeList" style="width: 100%;" required>
                <option value="<?= $AdvancePayment["advance_payment_pay_to"]; ?>"><?= $AdvancePayment["emp_firstname"] . " " .  $AdvancePayment["emp_lastname"] ;?></option>
            </select>
        </div>
        <div class="form-group required">
            <label for="advancePaymentPaymentsFrom">Accounts</label>
            <i data-toggle="tooltip" data-placement="right" title="<?= __("Which accounts the advance is paying from"); ?>" class="fa fa-question-circle"></i>
            <select name="advancePaymentPaymentsFrom" id="advancePaymentPaymentsFrom" class="form-control select2" style="width: 100%;" required>
                <?php 
                    foreach($selectAccounts["data"] as $accounts) {
                        $selected = ( $AdvancePayment["advance_payment_pay_from"] == $accounts['accounts_id'] ) ? "selected" : "";
                        echo "<option $selected value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group required">
            <label for="advancePaymentAmount">Amount</label>
            <input type="number" onclick="this.select();" name="advancePaymentAmount" id="advancePaymentAmount" value="<?= number_format($AdvancePayment["advance_payment_amount"], 2, ".", ""); ?>" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="advancePaymentPaymentMethods">Payment Method</label>
            <select name="advancePaymentPaymentMethods" id="advancePaymentPaymentMethods" class="form-control select2" style="width: 100%">
                <?php
                    $paymentMethod = array("Cash", "Bank Transfer", "Cheque", "Others");
                    
                    foreach($paymentMethod as $method) {
                        $selected = ( $method ===  $AdvancePayment["advance_payment_payment_method"] ) ? "selected" : "";
                        echo "<option $selected value='{$method}'>{$method}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="advancePaymentPaymentDescription">Description:</label>
            <textarea name="advancePaymentPaymentDescription" id="advancePaymentPaymentDescription" rows="3" class="form-control"><?= $AdvancePayment["advance_payment_description"]; ?></textarea>
        </div>
        <input type="hidden" name="advancePaymentId" value="<?php echo safe_entities($_GET["id"]) ; ?>">
    
        <div id="ajaxSubmitMsg"></div>

      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Update Advance Payment");
  
}


/************************** Update Advance Payment **********************/
if(isset($_GET['page']) and $_GET['page'] == "updateAdvancePayment") {

    if( !current_user_can("advance_bill_payments.Edit") ) {
        return _e("Sorry! you do not have permission to edit advance payment");
    }

    $accounts_balance = accounts_balance($_POST["advancePaymentPaymentsFrom"]);
    
    // Add the privouse data
    $selectPriviousData = easySelectA(array(
        "table"     => "advance_payments",
        "fields"    => "advance_payment_pay_from, advance_payment_amount",
        "where"     => array(
            "advance_payment_id" => $_POST["advancePaymentId"]
        )
    ))["data"][0];
    
    // Add the previous balance with account balance if the account is not changed
    if( $_POST["advancePaymentPaymentsFrom"] === $selectPriviousData["advance_payment_pay_from"] ) {
        $accounts_balance += $selectPriviousData["advance_payment_amount"];
    }

    if(empty($_POST["advancePaymentsDate"]))  {
        return _e("Please select payment date");
    } elseif(empty($_POST["advancePaymentPaymentsFrom"]))  {
        return _e("Please select accounts");
    } elseif(empty($_POST["advancePaymentPaymentEmployee"]))  {
        return _e("Please select employee");
    } elseif(empty($_POST["advancePaymentAmount"]) and $_POST["advancePaymentAmount"] !== 0)  {
        return _e("Please Enter Amount");
    } else if(!negative_value_is_allowed($_POST["advancePaymentPaymentsFrom"]) and $accounts_balance <  $_POST["advancePaymentAmount"]  ) {
        return _e("Payment amount is exceeded of account balance (%.2f)", number_format($accounts_balance, 2));
    }


    // update Payment
    $updateAdvancePayment = easyUpdate(
        "advance_payments",
        array (
            "advance_payment_date"             => $_POST["advancePaymentsDate"],
            "advance_payment_pay_to"           => $_POST["advancePaymentPaymentEmployee"],
            "advance_payment_amount"           => $_POST["advancePaymentAmount"],
            "advance_payment_pay_from"         => $_POST["advancePaymentPaymentsFrom"],
            "advance_payment_description"      => $_POST["advancePaymentPaymentDescription"],
            "advance_payment_payment_method"   => $_POST["advancePaymentPaymentMethods"]
        ),
        array(
            "advance_payment_id"    => $_POST["advancePaymentId"]
        )
    );


    if($updateAdvancePayment === true) {      

        // Update Accounts Balance for update accounts
        updateAccountBalance($_POST["advancePaymentPaymentsFrom"]);

        // Update Accounts Balance for previous accounts if the account is changed
        if( $_POST["advancePaymentPaymentsFrom"] !== $selectPriviousData["advance_payment_pay_from"] ) {
            updateAccountBalance( $selectPriviousData["advance_payment_pay_from"] );
        }
    
        _s("Advance payment successfully updated.");

    } else {
        _e($payAdvancePayment);
    }
}


/************************** Advance Payment Adjustment List **********************/
if(isset($_GET['page']) and $_GET['page'] == "advancePaymentAdjustmentList") {

    if( !current_user_can("payment_adjustment.Edit") ) {
        return _e("Sorry! you do not have permission to view payment adjustment");
    }

    // Include the modal header
    modal_header("Advance Adjustment List", "");
    
    $selectAdvanceAdjustmentItems = easySelect(
        "payments",
        "payment_id, payment_date, payment_reference, payment_to_employee, payment_amount",
        array(),
        array (
            "payment_to_employee" => $_GET["emp_id"],
            " AND payment_type = 'Advance Adjustment'",
            " AND is_trash = 0"
        ),
        array (
            "payment_id"    => "DESC"
        )
    );

    if($selectAdvanceAdjustmentItems === false) {
        return _e("<b>Error:</b> No items found");
    }

    echo "<table class='table'>";
    echo "</tr>";
    echo "<th>Date</th> <th>Reference</th> <th>Amount</th>";
    echo "</tr>";
    
    foreach($selectAdvanceAdjustmentItems["data"] as $key => $api) { // api = advance payment items
        echo "<tr>";
        echo "<td>". $api["payment_date"] ."</td>";
        echo "<td>". $api["payment_reference"] ."</td>";
        echo "<td>". $api["payment_amount"] ."</td>";
        echo "</tr>";
    }

    echo "</table>";
    
}

/************************** Pay Advance Payment **********************/
if(isset($_GET['page']) and $_GET['page'] == "adjustAdvancePayment") {
  
    // Include the modal header
    modal_header("Adjust advance payment", full_website_address() . "/xhr/?module=expenses&page=adjustAdvancePaymentSubmit");
    
    ?>
    
    <div class="box-body">

        <div class="row">

            <div class="col-md-8">
                <div class="row">
                    <div class="form-group col-md-6 required">
                        <label for="advancePaymentAdjustEmployee"><?= __("Employee"); ?></label>
                        <select name="advancePaymentAdjustEmployee" id="advancePaymentAdjustEmployee" select2-minimum-input-length="1" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=employeeList" style="width: 100%;" required>
                            <option value=""><?= __("Select Employee"); ?>....</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="paymentCompany"><?= __("Company"); ?> <span style="font-size: 18px;"></span> </label>
                        <select name="paymentCompany" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=CompanyList" style="width: 100%;">
                            <option value=""><?= __("Select Company"); ?>....</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6 required">
                        <label for="advancePaymentAdjustDate"><?= __("Date:"); ?></label>
                        <input type="text" name="advancePaymentAdjustDate" id="advancePaymentAdjustDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="advancePaymentAdjustAccount"><?= __("Accounts"); ?> <span style="font-size: 18px;"></span></label>
                        <i data-toggle="tooltip" data-placement="right" title="Which account the additional amount will be added or cut from" class="fa fa-question-circle"></i>
                        <select name="advancePaymentAdjustAccount" id="advancePaymentAdjustAccount" class="form-control select2" style="width: 100%;">
                            <option value=""><?= __("Select Accounts"); ?>...</option>
                            <?php
                                
                                foreach($selectAccounts["data"] as $accounts) {
                                    echo "<option value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-xs-4">
                <br/>
                <table id="advancePaymentInfo" class="table">
                    <tbody>
                    
                        <tr class="bg-info">
                            <th class="text-right"><?= __("Total Paid"); ?></th>
                            <th class="text-right">0.00</th>
                        </tr>
                        <tr class="bg-info">
                            <th class="text-right"><?= __("Total Adjust:"); ?></th>
                            <th class="text-right">0.00</th>
                        </tr>
                        <tr class="bg-info">
                            <th class="text-right"><?= __("Total Due:"); ?></th>
                            <th class="text-right">0.00</th>
                        </tr>

                    </tbody>
                </table>
            </div>
            
        </div> <!-- /.Row -->

        <hr/>

        <div class="advancePaymentsItems">
            <div class="row">

                <div class="col-md-4">
                    <div class="form-group required">
                        <label for="paymentCategory"><?= __("Payment Category:"); ?></label>
                        <select name="paymentCategory[]" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=paymentCategoryList" style="width: 100%;" required>
                            <option value=""><?= __("Select category"); ?>....</option>
                        </select>
                    </div>    
                </div>

                <div class="col-md-3">
                    <div class="form-group required">
                        <label for="paymentAmount"><?= __("Amount"); ?></label>
                        <input type="number" name="paymentAmount[]" class="form-control paymentAmount" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="paymentNarration"><?= __("Narration"); ?> <span style="font-size: 18px;"></span> </label>
                        <input type="text" name="paymentNarration[]" class="form-control">
                    </div>
                </div>

            </div>

        </div>

        <div style="padding-top: 10px; padding-left: 10px; padding-bottom: 5px; margin-bottom: 8px;" class="bg-info col-md-11">
            <p style="font-weight: bold;"><?= __("Total Amount:"); ?>
                <span id="totalAdjustAmount">0.00</span>
            </p>
            <p></p>
        </div> 
        
        <!-- Add payment row button -->
        <div style="width: 80px; display: block; margin: 20px auto;">
            <span style="cursor: pointer;" class="btn btn-primary" id="addAdvancePaymentAdjustmentRow">
                <i style="padding: 5px;" class="fa fa-plus-circle"></i>
            </span>
        </div>
        
        <div id="ajaxSubmitMsg"></div>

    </div> <!-- /.box-body -->
      
    <script>

        /* Add advance Payments row
         The first is used to remove envent listener. */
        $(document).off("click","#addAdvancePaymentAdjustmentRow");
        $(document).on("click","#addAdvancePaymentAdjustmentRow", function() {

            var html = '<div class="row"> \
                <div class="col-md-4"> \
                    <div class="form-group"> \
                        <select name="paymentCategory[]" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=paymentCategoryList" style="width: 100%;" required> \
                            <option value=""><?= __("Select category"); ?>....</option> \
                        </select>  \
                    </div> \
                </div> \
                <div class="col-md-3"> \
                    <div class="form-group"> \
                        <input type="number" name="paymentAmount[]" class="form-control paymentAmount" required> \
                    </div> \
                </div> \
                <div class="col-md-4"> \
                    <div class="form-group"> \
                        <input type="text" name="paymentNarration[]" class="form-control"> \
                    </div> \
                </div> \
                <div class="col-xs-1"> \
                    <i style="cursor: pointer; padding: 10px 5px 0 0;" class="fa fa-trash-o" id="removeAdvancePaymentAdjustmentRow"></i> \
                </div> \
            </div>';

            $(".advancePaymentsItems").append(html);

        });
        

        /* Remove advance payments row */
        $(document).on("click", "#removeAdvancePaymentAdjustmentRow", function() {
            $(this).closest(".row").css("background-color", "whitesmoke").hide("slow", function() {
                $(this).closest(".row").remove();
            });
        });

        $(document).off("change", "#advancePaymentAdjustEmployee");
        $(document).on("change", "#advancePaymentAdjustEmployee", function() {

            $.post(
                "<?php echo full_website_address(); ?>/info/?module=data&page=getEmployeeAdvancePaymentsData",
                {
                    empId: $("#advancePaymentAdjustEmployee").val()
                },

                function(data, status) {
                    var apd = JSON.parse(data); /* apd = advance Payment data */
                    
                    $("#advancePaymentInfo > tbody > tr:nth-child(1) > th:nth-child(2)").html( tsd(apd.advance_paid_amount) );
                    $("#advancePaymentInfo > tbody > tr:nth-child(2) > th:nth-child(2)").html( tsd(apd.advance_adjust_amount) );
                    $("#advancePaymentInfo > tbody > tr:nth-child(3) > th:nth-child(2)").html( tsd(apd.advance_paid_amount - apd.advance_adjust_amount) );

                }
            );

        });

        $(document).on("blur keyup", ".paymentAmount", function() {

            var totalAmount = 0;
            $(".paymentAmount").each( function() {
                totalAmount += Number($(this).val());
            });

            $("#totalAdjustAmount").html(totalAmount);

        });
        

      </script>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Submit");
  
}


/************************** Adjust Advance Payment **********************/
if(isset($_GET['page']) and $_GET['page'] == "adjustAdvancePaymentSubmit") {

    if( !current_user_can("payment_adjustment.Add") ) {
        return _e("Sorry! you do not have permission to add payment adjustment");
    }

    $empAdvancePayment = easySelectD("
        select emp_id,
            if(advance_payment_amount_sum is null, 0, advance_payment_amount_sum) as advance_paid_amount,
            if(payments_return_amount_sum is null, 0, payments_return_amount_sum) + if(payment_amount_sum is null, 0, payment_amount_sum) as advance_adjust_amount
        from {$table_prefix}employees
        left join ( select advance_payment_pay_to, sum(advance_payment_amount) as advance_payment_amount_sum from {$table_prefix}advance_payments group by advance_payment_pay_to ) as get_advance_payments on advance_payment_pay_to = emp_id
        left join ( select payment_to_employee, sum(payment_amount) as payment_amount_sum from {$table_prefix}payments where payment_type = 'Advance Adjustment' group by payment_to_employee ) as get_payments on payment_to_employee = emp_id
        left join ( select payments_return_emp_id, sum(payments_return_amount) as payments_return_amount_sum from {$table_prefix}payments_return group by payments_return_emp_id ) as get_advance_return on payments_return_emp_id = emp_id
        where emp_id = " . safe_input($_POST['advancePaymentAdjustEmployee'])
    )["data"][0];

    $totalPaymentAmount = array_sum($_POST["paymentAmount"]);
    $totalDueAmount = $empAdvancePayment["advance_paid_amount"] - $empAdvancePayment["advance_adjust_amount"];

    /*
    if( empty($_POST["advancePaymentAdjustAccount"]) and $totalPaymentAmount > $totalDueAmount ) {
         Amount is exceeded of due amount. If you want to pay the exceeded amount, please select the accounts. </div>";
        return;
    }   */


    /**
     * If total adjust payment amount is exceeded from total due amount and accounts is selected,
     * then exceeded amount will be added on payment amount and adjust the accounts balance.
     * 
     * If total adjust payment is lower from total due amount and accounts is selected,
     * then lower amount will be added on the return payment and adjust the accounts balance.
     */
    
    // Check if the account is selected
    if( !empty($_POST["advancePaymentAdjustAccount"]) ) {

        // If total payment is exceeded then total due amount
        if( $totalPaymentAmount > $totalDueAmount ) {
            
            $extraPaymentAmountOnAdjustment = $totalPaymentAmount - $totalDueAmount;

            // Select last payment references
            $selectAdvancePaymentReference = easySelect(
                "advance_payments",
                "advance_payment_reference",
                array(),
                array (
                    "advance_payment_pay_by"   => $_SESSION['uid'],
                    " AND advance_payment_reference is not null"
                ),
                array (
                    "advance_payment_id" => "DESC"
                ),
                array (
                    "start" => 0,
                    "length" => 1
                )
            );

            // Referense Format: SALE/POS/n
            $paymentReferences = "ADVANCE/{$_SESSION['uid']}/";

            // check if there is minimum one records
            if($selectAdvancePaymentReference !== false) {
                $getLastReferenceNo = (int)explode($paymentReferences, $selectAdvancePaymentReference["data"][0]["advance_payment_reference"])[1];
                $paymentReferences = $paymentReferences . ($getLastReferenceNo+1);
            } else {
                $paymentReferences = "ADVANCE/{$_SESSION['uid']}/1";
            }

            // Insert Payment
            $payAdvancePayment = easyInsert(
                "advance_payments",
                array (
                    "advance_payment_date"             => $_POST["advancePaymentAdjustDate"],
                    "advance_payment_reference"        => $paymentReferences,
                    "advance_payment_pay_to"           => $_POST["advancePaymentAdjustEmployee"],
                    "advance_payment_amount"           => $extraPaymentAmountOnAdjustment,
                    "advance_payment_pay_from"         => $_POST["advancePaymentAdjustAccount"],
                    "advance_payment_description"      => "Payment made on adjustment",
                    "advance_payment_pay_by"           => $_SESSION["uid"]   
                )
            );


        // If total adjust payment is lower from total due amount
        } else if( $totalPaymentAmount < $totalDueAmount ) {

            $extraReturnOnAdjustment = $totalDueAmount - $totalPaymentAmount;

            // Insert return amount 
            $insertReturn = easyInsert(
                "payments_return",
                array (
                    "payments_return_date"              => $_POST["advancePaymentAdjustDate"],
                    "payments_return_emp_id"            => $_POST["advancePaymentAdjustEmployee"],
                    "payments_return_accounts"          => $_POST["advancePaymentAdjustAccount"],
                    "payments_return_amount"            => $extraReturnOnAdjustment,
                    "payments_return_description"       => "Return made on adjustment",
                    "payments_return_by"                => $_SESSION["uid"]
                )
            );

        }

        // Update Accounts Balance
        updateAccountBalance($_POST["advancePaymentAdjustAccount"]);

    }

    
    // Select last payment references
    $selectPaymentReference = easySelect(
        "payments",
        "payment_reference",
        array(),
        array (
            "payment_made_by"   => $_SESSION['uid'],
            " AND payment_reference LIKE 'ADJUST%'",
            " AND payment_reference is not null"
        ),
        array (
            "payment_id" => "DESC"
        ),
        array (
            "start" => 0,
            "length" => 1
        )
    );

    // Referense Format: SALE/POS/n
    $paymentReferences = "ADJUST/{$_SESSION['uid']}/";

    // check if there is minimum one records
    if($selectPaymentReference !== false) {
        $getLastReferenceNo = (int)explode($paymentReferences, $selectPaymentReference["data"][0]["payment_reference"])[1];
        $paymentReferences = $paymentReferences . ($getLastReferenceNo+1);
        
    } else {
        $paymentReferences = "ADJUST/{$_SESSION['uid']}/1";
    }

    // Insert the Bill Payment
    $insertAdjustPayment = easyInsert (
        "payments",
        array (
            "payment_date"              => $_POST["advancePaymentAdjustDate"],
            "payment_to_employee"       => $_POST["advancePaymentAdjustEmployee"],
            "payment_to_company"        => empty($_POST["paymentCompany"]) ? NULL : $_POST["paymentCompany"],
            "payment_status"            => "Complete",
            "payment_amount"            => $totalPaymentAmount,
            "payment_from"              => NULL,
            "payment_reference"         => $paymentReferences,
            "payment_description"       => "Advance Payment Adjustment",
            "payment_type"              => "Advance Adjustment",
            "payment_made_by"           => $_SESSION["uid"]
        ),
        array (
            "payment_date"              => $_POST["advancePaymentAdjustDate"],
            " AND payment_to_employee"  => $_POST["advancePaymentAdjustEmployee"],
            " AND payment_amount"       => $totalPaymentAmount,
            " AND payment_description"  => "Advance Payment Adjustment",
            " AND payment_made_by"      => $_SESSION["uid"]
        ),
        true
    );

    if(isset($insertAdjustPayment["status"]) and $insertAdjustPayment["status"] === "success" ) {

        // Insert payment items if successfully inserted in payments table
        foreach($_POST["paymentCategory"] as $key => $categoryID) {

            // Insert payment items
            easyInsert(
                "payment_items",
                array (
                    "payment_items_payments_id" => $insertAdjustPayment["last_insert_id"],
                    "payment_items_date"        => $_POST["advancePaymentAdjustDate"],
                    "payment_items_employee"    => $_POST["advancePaymentAdjustEmployee"],
                    "payment_items_type"        => "Bill",
                    "payment_items_company"     => empty($_POST["paymentCompany"]) ? NULL : $_POST["paymentCompany"],
                    "payment_items_category_id" => $categoryID,
                    "payment_items_amount"      => $_POST["paymentAmount"][$key],
                    "payment_items_description" => $_POST["paymentNarration"][$key],
                    "payment_items_made_by"     => $_SESSION["uid"]
                )
            );

        }

        //echo "<div class='alert alert-success'>" . sprintf(__("Payment successfully added. The reference number is: <strong>%s</strong>"), $paymentReferences) . "</div>";

        echo "<div class='alert alert-success'>" . sprintf(__("Payment successfully added. The reference number is: <strong>%s</strong>. Please <a %s>click here to print</a> the receipt."), $paymentReferences, " onClick='BMS.MAIN.printPage(this.href, event);' href='" . full_website_address()."/invoice-print/?autoPrint=true&invoiceType=paymentReceipt&id={$insertAdjustPayment['last_insert_id']}'") . "</div>";

    } else {
        _e($insertAdjustPayment);
    }

}


/************************** Pay Advance Payment **********************/
if(isset($_GET['page']) and $_GET['page'] == "returnAdvancePayment") {
  
    // Include the modal header
    modal_header("Return advance payment", full_website_address() . "/xhr/?module=expenses&page=returnAdvancePaymentSubmit");
    
    ?>
    
    <div class="box-body">
        <div class="form-group required">
            <label for="returnAdvancePaymentEmployee"><?= __("Employee"); ?></label>
            <select name="returnAdvancePaymentEmployee" id="returnAdvancePaymentEmployee" select2-minimum-input-length="1" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=employeeList" style="width: 100%;" required>
                <option value=""><?= __("Select Employee"); ?>....</option>
            </select>
        </div>
        <div class="form-group required">
            <label for="returnAdvancePaymentsDate"><?= __("Date:"); ?></label>
            <input type="text" name="returnAdvancePaymentsDate" id="returnAdvancePaymentsDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker" required>
        </div>
        <div class="form-group required">
            <label for="returnAdvancePaymentAccounts"><?= __("To Accounts"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="Which accounts the return will be added" class="fa fa-question-circle"></i>
            <select name="returnAdvancePaymentAccounts" id="returnAdvancePaymentAccounts" class="form-control select2" style="width: 100%;" required>
                <?php
                    
                    foreach($selectAccounts["data"] as $accounts) {
                        $selected = (isset($_SESSION['aid']) and $_SESSION['aid'] === $accounts['accounts_id']) ? "selected" : "";
                        echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group required">
            <label for="returnableAdvancePaymentAmount">Returnable Amount</label>
            <input type="number" name="returnableAdvancePaymentAmount" id="returnableAdvancePaymentAmount" value="" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="returnPaymentPaymentDescription"><?= __("Description:"); ?></label>
            <textarea name="returnPaymentPaymentDescription" id="returnPaymentPaymentDescription" rows="3" class="form-control"></textarea>
        </div>
    
        <div id="ajaxSubmitMsg"></div>

    </div> <!-- /.box-body -->
      
        <script>

        $(document).off("change", "#returnAdvancePaymentEmployee");
        $(document).on("change", "#returnAdvancePaymentEmployee", function() {

            $.post(
                "<?php echo full_website_address(); ?>/info/?module=data&page=getEmployeeAdvancePaymentsData",
                {
                    empId: $("#returnAdvancePaymentEmployee").val()
                },

                function(data, status) {
                    var apd = JSON.parse(data); /* apd = advance Payment data */
                    $("#returnableAdvancePaymentAmount").val( (apd.advance_paid_amount - apd.advance_adjust_amount).toFixed(0) );
                }
            );

        });

      </script>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Submit");
  
}


/************************** Return Advance Payment Payments **********************/
if(isset($_GET['page']) and $_GET['page'] == "returnAdvancePaymentSubmit") {

    if( !current_user_can("payments_return.Add") ) {
        return _e("Sorry! you do not have permission to return advance payment");
    }

    $emp_id = safe_input($_POST["returnAdvancePaymentEmployee"]);

    $getEmpAdvancePaymentData = easySelectD("
        select emp_id,
            if(advance_payment_amount_sum is null, 0, advance_payment_amount_sum) as advance_paid_amount,
            if(payments_return_amount_sum is null, 0, payments_return_amount_sum) + if(payment_amount_sum is null, 0, payment_amount_sum) as advance_adjust_amount
        from {$table_prefix}employees
        left join ( select advance_payment_pay_to, sum(advance_payment_amount) as advance_payment_amount_sum from {$table_prefix}advance_payments where is_trash = 0 group by advance_payment_pay_to ) as get_advance_payments on advance_payment_pay_to = emp_id
        left join ( select payment_to_employee, sum(payment_amount) as payment_amount_sum from {$table_prefix}payments where is_trash = 0 and payment_type = 'Advance Adjustment' group by payment_to_employee ) as get_payments on payment_to_employee = emp_id
        left join ( select payments_return_emp_id, sum(payments_return_amount) as payments_return_amount_sum from {$table_prefix}payments_return where is_trash = 0 group by payments_return_emp_id ) as get_advance_return on payments_return_emp_id = emp_id
            where emp_id = {$emp_id}"
    )["data"][0];

    if( ( $_POST["returnableAdvancePaymentAmount"] + $getEmpAdvancePaymentData["advance_adjust_amount"] ) >  $getEmpAdvancePaymentData["advance_paid_amount"] ) {
        return _e("Amount is exceeded of paid amount");
    }

    // Insert return amount 
    $insertReturn = easyInsert(
        "payments_return",
        array (
            "payments_return_date"              => $_POST["returnAdvancePaymentsDate"] . date(" H:i:s"),
            "payments_return_type"              => "Incoming",
            "payments_return_emp_id"            => $_POST["returnAdvancePaymentEmployee"],
            "payments_return_accounts"          => $_POST["returnAdvancePaymentAccounts"],
            "payments_return_amount"            => $_POST["returnableAdvancePaymentAmount"],
            "payments_return_description"       => $_POST["returnPaymentPaymentDescription"],
            "payments_return_by"                => $_SESSION["uid"]
        )
    );
    
    
    if($insertReturn === true) {
        // Update Accounts Balance
        updateAccountBalance($_POST["returnAdvancePaymentAccounts"]);

        _s("Return successfully completed.");

    } else {
        _e($insertReturn);
    }

}


/************************** Return Customer Payment **********************/
if(isset($_GET['page']) and $_GET['page'] == "returnCustomerPayment") {
  
    // Include the modal header
    modal_header("Return Customer Payment", full_website_address() . "/xhr/?module=expenses&page=returnCustomerPaymentSubmit");
    
    ?>
    
    <div class="box-body">
        <div class="form-group required">
            <label for="returnPaymentCustomer"><?= __("Customer"); ?></label>
            <select name="returnPaymentCustomer" id="returnPaymentCustomer" select2-minimum-input-length="1" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=customerList" style="width: 100%;" required>
                <option value=""><?= __("Select Customer"); ?>...</option>
            </select>
        </div>
        <div class="form-group required">
            <label for="returnAdvancePaymentsDate"><?= __("Date:"); ?></label>
            <input type="text" name="returnAdvancePaymentsDate" id="returnAdvancePaymentsDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker" required>
        </div>
        <div class="form-group required">
            <label for="returnAdvanceDirection"><?= __("Direction/ Type:"); ?></label>
            <select name="returnAdvanceDirection" id="returnAdvanceDirection" class="form-control" required>
                <option value="">Select direction</option>
                <option value="Outgoing">Outgoing</option>
                <option value="Incoming">Incoming</option>
            </select>
        </div>
        <div class="form-group required">
            <label for="returnPaymentAccounts"><?= __("Accounts"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="Which accounts the return amount will ad deduct" class="fa fa-question-circle"></i>
            <select name="returnPaymentAccounts" id="returnPaymentAccounts" class="form-control select2" style="width: 100%;" required>
                <?php
                    
                    foreach($selectAccounts["data"] as $accounts) {
                        $selected = (isset($_SESSION['aid']) and $_SESSION['aid'] === $accounts['accounts_id']) ? "selected" : "";
                        echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group required">
            <label for="returnablePaymentAmount">Returnable Amount</label>
            <input type="number" name="returnablePaymentAmount" id="returnablePaymentAmount" value="" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="returnPaymentDescription"><?= __("Description:"); ?></label>
            <textarea name="returnPaymentDescription" id="returnPaymentDescription" rows="3" class="form-control"></textarea>
        </div>
    
        <div id="ajaxSubmitMsg"></div>

    </div> <!-- /.box-body -->
      

    <?php
  
    // Include the modal footer
    modal_footer("Submit");
  
}


/************************** Return Advance Payment Payments **********************/
if(isset($_GET['page']) and $_GET['page'] == "returnCustomerPaymentSubmit") {

    if( !current_user_can("payments_return.Add") ) {
        return _e("Sorry! you do not have permission to return advance payment");
    }


    // Insert return amount 
    $insertReturn = easyInsert(
        "payments_return",
        array (
            "payments_return_date"              => $_POST["returnAdvancePaymentsDate"] . date(" H:i:s"),
            "payments_return_type"              => $_POST["returnAdvanceDirection"],
            "payments_return_customer_id"       => $_POST["returnPaymentCustomer"],
            "payments_return_accounts"          => $_POST["returnPaymentAccounts"],
            "payments_return_amount"            => $_POST["returnablePaymentAmount"],
            "payments_return_description"       => $_POST["returnPaymentDescription"],
            "payments_return_by"                => $_SESSION["uid"]
        )
    );
    
    
    if($insertReturn === true) {

        // Update Accounts Balance
        updateAccountBalance($_POST["returnPaymentAccounts"]);

        _s("Return successfully completed.");

    } else {
        _e($insertReturn);
    }

}



/*************************** advancePaymentReturnList ***********************/
if(isset($_GET['page']) and $_GET['page'] == "advancePaymentReturnList") {

    if( !current_user_can("payments_return.View") ) {
        return _e("Sorry! you do not have permission to view advance payment return list");
    }
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "payments_return_date",
        ""
    );
    
    // Count Total recrods
    $paymentReturn = easySelectA(array(
        "table" => "payments_return",
        "where" => array(
            "is_trash = 0"
        )
    ));
    $totalFilteredRecords = $totalRecords = $paymentReturn ? $paymentReturn["count"] : 0;

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if(!empty($requestData["search"]["value"])) {  // get data with search

        $getData = easySelectA(array(
            "table"     => "payments_return as payments_return",
            "fields"    => "payments_return_id, payments_return_date, emp_firstname, emp_lastname, emp_PIN, accounts_name, payments_return_amount, payments_return_description",
            "join"      => array(
                "left join {$table_prefix}employees on payments_return_emp_id = emp_id",
                "left join {$table_prefix}accounts on payments_return_accounts = accounts_id"
            ),
            "where" => array(
                "payments_return.is_trash=0 and emp_firstname like" => "%".$requestData['search']['value'] . "%",
                " or accounts_name like" => "%".$requestData['search']['value'] . "%",
                " or payments_return_description like"  => "%".$requestData['search']['value'] . "%"
            ),
            "orderby"   => array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit" => array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));

        $totalFilteredRecords = $getData ? $getData["count"] : 0;

    } else { // Get data withouth search

        $getData = easySelectA(array(
            "table"     => "payments_return as payments_return",
            "fields"    => "payments_return_id, payments_return_date, emp_firstname, emp_lastname, emp_PIN, accounts_name, payments_return_amount, payments_return_description",
            "join"      => array(
                "left join {$table_prefix}employees on payments_return_emp_id = emp_id",
                "left join {$table_prefix}accounts on payments_return_accounts = accounts_id"
            ),
            "where" => array(
                "payments_return.is_trash=0"
            ),
            "orderby"   => array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit" => array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));

    }

    $allData = [];

    // Check if there have more then zero data
    if($getData !== false) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["payments_return_date"];
            $allNestedData[] = $value["emp_firstname"] ." ". $value["emp_lastname"] . ' (' . $value["emp_PIN"] . ')';
            $allNestedData[] = $value["accounts_name"];
            $allNestedData[] = $value["payments_return_amount"];
            $allNestedData[] = $value["payments_return_description"];
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=expenses&page=deletePaymentReturn" data-to-be-deleted="'. $value["payments_return_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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


// Delete Payment Return
if(isset($_GET['page']) and $_GET['page'] == "deletePaymentReturn") {

    if( !current_user_can("payments_return.Delete") ) {
        return _e("Sorry! you do not have permission to delete advance payment return");
    }

    $selectDeletedPaymentReturn = easySelectA(array(
        "table"     => "payments_return",
        "fields"    => "payments_return_sales_id, payments_return_purchase_id",
        "where"     => array(
            "payments_return_id" => $_POST["datatoDelete"]
        )
    ))["data"][0];

    $deleteData = easyDelete(
        "payments_return",
        array(
            "payments_return_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {


        // Update sales status, Paid amount and deus after deleting payment return
        if($selectDeletedPaymentReturn["payments_return_sales_id"] !== NULL or $selectDeletedPaymentReturn["payments_return_sales_id"] > 0 ) {

            $totalReturnAmount = 0;
            $getReturnAmount = easySelectD("SELECT sum(payments_return_amount) as totalReturnAmount FROM {$table_prefix}payments_return where is_trash = 0 and payments_return_sales_id = {$selectDeletedPaymentReturn["payments_return_sales_id"]} group by payments_return_sales_id");

            if($getReturnAmount !== false) {
                $totalReturnAmount = $getReturnAmount["data"][0]["totalReturnAmount"];
            }

            // Select sales
            $selectSales = easySelectD("SELECT sales_grand_total, is_return from {$table_prefix}sales where sales_id = {$selectDeletedPaymentReturn["payments_return_sales_id"]} ")["data"][0];

 
            // Generate the payment status
            $salesPaymentStatus = "due";
            if( abs($selectSales["sales_grand_total"]) <= $totalReturnAmount) {

                $salesPaymentStatus = "paid";

            } else if( abs($selectSales["sales_grand_total"]) > $totalReturnAmount and $totalReturnAmount > 0) {

                $salesPaymentStatus = "partial";

            }

            easyUpdate(
                "sales",
                array(
                    "sales_paid_amount" => $selectSales["is_return"] == 0 ? - $totalReturnAmount : $totalReturnAmount,
                    "sales_due" => $selectSales["is_return"] == 0 ? $selectSales["sales_grand_total"] + $totalReturnAmount : $selectSales["sales_grand_total"] - $totalReturnAmount,
                    "sales_payment_status"  => $salesPaymentStatus
                ),
                array(
                    "sales_id"  => $selectDeletedPaymentReturn["payments_return_sales_id"]
                )
            );

        }


        // Update purchase 
        if($selectDeletedPaymentReturn["payments_return_purchase_id"] !== NULL or $selectDeletedPaymentReturn["payments_return_purchase_id"] > 0 ) {


            $totalPurchaseReturnPayment = 0;
            $getPurchaseReturnAmount = easySelectD("SELECT sum(payments_return_amount) as totalReturnAmount FROM {$table_prefix}payments_return where is_trash = 0 and payments_return_purchase_id = {$selectDeletedPaymentReturn["payments_return_purchase_id"]} group by payments_return_sales_id");
            if($getPurchaseReturnAmount !== false) {
                $totalPurchaseReturnPayment = $getPurchaseReturnAmount["data"][0]["totalReturnAmount"];
            }

            $purchaseReturnGrandTotal = easySelectD("SELECT purchase_grand_total from {$table_prefix}purchases where purchase_id = {$selectDeletedPaymentReturn["payments_return_purchase_id"]}")["data"][0]["purchase_grand_total"];


            $purchasePaymentStatus = "due";
            if($purchaseReturnGrandTotal <= $totalPurchaseReturnPayment) {

                $purchasePaymentStatus = "paid";

            } else if($purchaseReturnGrandTotal > $totalPurchaseReturnPayment and $totalPurchaseReturnPayment > 0) {

                $purchasePaymentStatus = "partial";

            }

            easyUpdate(
                "purchases",
                array(
                    "purchase_paid_amount"      => $totalPurchaseReturnPayment,
                    "purchase_due"              => $purchaseReturnGrandTotal - $totalPurchaseReturnPayment,
                    "purchase_payment_status"   => $purchasePaymentStatus
                ), 
                array(
                    "purchase_id"   => $selectDeletedPaymentReturn["payments_return_purchase_id"]
                )
            );

        }

        echo 1;

    } 

}


/************************** newPaymentAdjustment **********************/
if(isset($_GET['page']) and $_GET['page'] == "newPaymentAdjustment") {
  
    // Include the modal header
    modal_header("New Payment Adjustment", full_website_address() . "/xhr/?module=expenses&page=addNewPaymentAdjustment");
    
    ?>
      <div class="box-body">      
        

            <div class="form-group required">
                <label for="adjustmentDate"><?= __("Adjustment Date:"); ?></label>
                <input type="text" name="adjustmentDate" id="adjustmentDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker" required>
            </div>

            <div class="form-group required">
                <label for="adjustmentCompany"><?= __("Company"); ?></label>
                <select name="adjustmentCompany" id="adjustmentCompany" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=CompanyList" style="width: 100%;" required>
                    <option value=""><?= __("Select Company"); ?>....</option>
                </select>
            </div>
            <div class="form-group">
                <label for="adjustmentAmount"><?= __("Amount"); ?></label>
                <input type="number" name="adjustmentAmount" id="adjustmentAmount" class="form-control">
            </div>
            <div class="form-group">
                <label for="paymentAdjustmentDescription"><?= __("Description:"); ?></label>
                <textarea name="paymentAdjustmentDescription" id="paymentAdjustmentDescription" rows="3" class="form-control"></textarea>
            </div>

            <div id="ajaxSubmitMsg"></div>

      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer();
  
}


if(isset($_GET['page']) and $_GET['page'] == "addNewPaymentAdjustment") {

    if( !current_user_can("payment_adjustment.Add") ) {
        return _e("Sorry! you do not have permission to adjust payment");
    }

    if(empty($_POST["adjustmentDate"]))  {
        return _e("Please select adjustment date");
    } elseif(empty($_POST["adjustmentCompany"]))  {
        return _e("Please select company");
    } elseif(empty($_POST["adjustmentAmount"]))  {
        return _e("Please enter amount");
    }

    $insertAdjustment = easyInsert(
        "payment_adjustment",
        array(
            "pa_date"           => $_POST["adjustmentDate"],
            "pa_company"        => $_POST["adjustmentCompany"],
            "pa_amount"         => $_POST["adjustmentAmount"],
            "pa_description"    => $_POST["paymentAdjustmentDescription"],
            "pa_add_by"         => $_SESSION["uid"]
        )
    );

    if($insertAdjustment === true) {
        _s("Successfully Added");
    } else {
        _e($insertAdjustment);
    }

}



/*************************** paymentAdjustmentList ***********************/
if(isset($_GET['page']) and $_GET['page'] == "paymentAdjustmentList") {
    

    if( !current_user_can("payment_adjustment.View") ) {
        return _e("Sorry! you do not have permission to view adjust payment list");
    }


    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "pa_date",
        "company_name",
        "pa_amount",
        "pa_description"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "payment_adjustment",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];
    
    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if(!empty($requestData["search"]["value"])) {  // get data with search
        

        $getData = easySelectA(array(
            "table"    => "payment_adjustment as payment_adjustment",
            "join"     => array(
                "left join {$table_prefix}companies on pa_company = company_id"
            ),
            "where"     => array(
                "payment_adjustment.is_trash = 0 and company_name like " => $requestData['search']['value'] . "%",
                " or pa_description like"   => "%".$requestData['search']['value'] . "%"
            ),
            "orderby"   => array(
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit" => array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));

        $totalFilteredRecords = $getData ? $getData["count"] : 0;

    } else { // Get data withouth search

        $getData = easySelectA(array(
            "table"    => "payment_adjustment as payment_adjustment",
            "join"     => array(
                "left join {$table_prefix}companies on pa_company = company_id"
            ),
            "where"     => array(
                "payment_adjustment.is_trash = 0"
            ),
            "orderby"   => array(
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit" => array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));

    }

    $allData = [];

    // Check if there have more then zero data
    if($getData !== false) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["pa_date"];
            $allNestedData[] = $value["company_name"];
            $allNestedData[] = $value["pa_amount"];
            $allNestedData[] = $value["pa_description"];
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=expenses&page=editPaymentAdjustment&id='. $value["pa_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=expenses&page=deletePaymentAdjustment" data-to-be-deleted="'. $value["pa_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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


// Delete Payment Adjustment
if(isset($_GET['page']) and $_GET['page'] == "deletePaymentAdjustment") {

    if( !current_user_can("payment_adjustment.Delete") ) {
        return _e("Sorry! you do not have permission to delete adjust payment");
    }

    $deleteData = easyDelete(
        "payment_adjustment",
        array(
            "pa_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {
        echo 1;
    } 

}


/************************** newPaymentAdjustment **********************/
if(isset($_GET['page']) and $_GET['page'] == "editPaymentAdjustment") {

    if( !current_user_can("payment_adjustment.Edit") ) {
        return _e("Sorry! you do not have permission to edit adjust payment");
    }
  
    // Include the modal header
    modal_header("Edit Payment Adjustment", full_website_address() . "/xhr/?module=expenses&page=updatePaymentAdjustment");

    $pa = easySelectA(array(
        "table" => "payment_adjustment",
        "where" => array(
            "pa_id" => $_GET["id"]
        ),
        "join"  => array(
            "left join {$table_prefix}companies on pa_company = company_id"
        )
    ))["data"][0];
    
    ?>
      <div class="box-body">      
        

            <div class="form-group required">
                <label for="adjustmentDate"><?= __("Bill Date:"); ?></label>
                <input type="text" name="adjustmentDate" id="adjustmentDate" value="<?= $pa["pa_date"]; ?>" class="form-control datePicker" required>
            </div>

            <div class="form-group required">
                <label for="adjustmentCompany"><?= __("Company"); ?></label>
                <select name="adjustmentCompany" id="adjustmentCompany" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=CompanyList" style="width: 100%;" required>
                    <option value="<?= $pa["pa_company"]; ?>"><?= $pa["company_name"]; ?></option>
                </select>
            </div>
            <div class="form-group">
                <label for="adjustmentAmount"><?= __("Amount"); ?></label>
                <input type="number" name="adjustmentAmount" id="adjustmentAmount" onclick="this.select();" value="<?= number_format($pa["pa_amount"],2); ?>"  class="form-control">
            </div>
            <div class="form-group">
                <label for="paymentAdjustmentDescription"><?= __("Description:"); ?></label>
                <textarea name="paymentAdjustmentDescription" id="paymentAdjustmentDescription" rows="3" class="form-control"> <?= $pa["pa_description"]; ?> </textarea>
            </div>
            <input type="hidden" name="paymentAdjustmentId" value="<?php echo safe_entities($_GET["id"]) ; ?>">

            <div id="ajaxSubmitMsg"></div>

      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer();
  
}


if(isset($_GET['page']) and $_GET['page'] == "updatePaymentAdjustment") {

    if( !current_user_can("payment_adjustment.Edit") ) {
        return _e("Sorry! you do not have permission to edit adjust payment");
    }

    if(empty($_POST["adjustmentDate"]))  {
        return _e("Please select adjustment date");
    } elseif(empty($_POST["adjustmentCompany"]))  {
        return _e("Please select company");
    } elseif(empty($_POST["adjustmentAmount"]))  {
        return _e("Please enter amount");
    }

    $updateAdjustment = easyUpdate(
        "payment_adjustment",
        array(
            "pa_date"           => $_POST["adjustmentDate"],
            "pa_company"        => $_POST["adjustmentCompany"],
            "pa_amount"         => $_POST["adjustmentAmount"],
            "pa_description"    => $_POST["paymentAdjustmentDescription"]
        ),
        array(
            "pa_id" => $_POST["paymentAdjustmentId"]
        )
    );

    if($updateAdjustment === true) {
        _s("Successfully updated.");
    } else {
        _e($updateAdjustment);
    }

}

?>