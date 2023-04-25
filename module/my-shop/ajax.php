<?php

// Select the Accounts
$selectAccounts = easySelect("accounts", "accounts_id, accounts_name", array(), array("is_trash" => 0));

/*************************** Pos Sale List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "posSaleList") {

    if( !current_user_can("myshop_pos_sales.View") ) {
        return _e("Sorry! you do not have permission to view sale list");
    }
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "sales_delivery_date",
        "sales_id",
        "customer_name",
        "sales_grand_total",
        "sales_product_discount",
        "sales_grand_total",
        "sales_paid_amount",
        "sales_due",
        "",
        "sales_payment_status"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "sales",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0 and sales_shop_id" => $_SESSION["sid"]
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if(!empty($requestData["search"]["value"])) {  // get data with search
      
        $getData = easySelect(
            "sales as sales",
            "sales_id, sales_delivery_date, sales_status, sales_shop_id, sales_reference, sales_customer_id, customer_name, customer_phone, sales_total_amount, sales_product_discount, 
            sales_discount, sales_change, sales_shipping, sales_grand_total, sales_paid_amount, sales_due, sales_payment_status, upazila_name, district_name",
            array (
                "left join {$table_prefix}customers on customer_id = sales_customer_id",
                "left join {$table_prefix}upazilas on upazila_id = customer_upazila",
                "left join {$table_prefix}districts on district_id = customer_district"
            ),
            array (
                "sales.is_trash = 0 and sales.is_return = 0 and sales.sales_delivery_date is not null and sales_shop_id" => $_SESSION["sid"],
                " AND customer_name LIKE" => $requestData['search']['value'] . "%",
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
  
    } else if(!empty($requestData["columns"][1]['search']['value']) or 
            !empty($requestData["columns"][2]['search']['value']) or 
            !empty($requestData["columns"][3]['search']['value']) or 
            !empty($requestData["columns"][12]['search']['value']) or
            !empty($requestData["columns"][13]['search']['value'])
        ) { // Get data with search by column
        
        $dateRange[0] = "";
        $dateRange[1] = "";
        if(!empty($requestData["columns"][1]['search']['value'])) {
            $dateRange = explode(" - ", safe_input($requestData["columns"][1]['search']['value']));
        }
        
        $getData = easySelect(
            "sales as sales",
            "sales_id, sales_delivery_date, sales_status, sales_shop_id, sales_reference, sales_customer_id, customer_name, customer_phone,
            sales_total_amount, sales_product_discount, sales_discount, sales_change, sales_shipping, sales_grand_total, sales_paid_amount, 
            sales_due, sales_payment_status, upazila_name, district_name",
            array (
                "left join {$table_prefix}customers on customer_id = sales_customer_id",
                "left join {$table_prefix}upazilas on upazila_id = customer_upazila",
                "left join {$table_prefix}districts on district_id = customer_district"
            ),
            array (
              "sales.is_trash = 0 and sales.is_return = 0 and sales.sales_delivery_date is not null and sales_shop_id" => $_SESSION["sid"],
              " AND sales_reference LIKE" => "%" . $requestData["columns"][2]['search']['value'] . "%",
              " AND customer_name LIKE" => "%" . $requestData["columns"][3]['search']['value'] . "%",
              " AND sales_payment_status" => $requestData["columns"][12]['search']['value'],
              " AND sales_status" => $requestData["columns"][13]['search']['value'],
              " AND (sales_delivery_date BETWEEN '{$dateRange[0]}' and '{$dateRange[1]}')"
            ),
            array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        );

        $totalFilteredRecords = $getData ? $getData['count'] : 0;
  
        
    } else { // Get data withouth search
  
      $getData = easySelect(
          "sales as sales",
          "sales_id, sales_delivery_date, sales_status, sales_shop_id, sales_reference, sales_customer_id, customer_name, customer_phone, sales_total_amount, sales_product_discount, 
          sales_discount, sales_change, sales_shipping, sales_grand_total, sales_paid_amount, sales_due, sales_payment_status, upazila_name, district_name",
          array (
            "left join {$table_prefix}customers on customer_id = sales_customer_id",
            "left join {$table_prefix}upazilas on upazila_id = customer_upazila",
            "left join {$table_prefix}districts on district_id = customer_district"
          ),
          array (
            "sales.is_trash = 0 and sales.is_return = 0 and sales.sales_delivery_date is not null and sales_shop_id" => $_SESSION["sid"],
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
            
            $getSalesPaymentStatus = "";
            if($value["sales_payment_status"] === "paid") {
                $getSalesPaymentStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-success'>Paid</span>";
            } else if($value["sales_payment_status"] === "partial") {
                $getSalesPaymentStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-warning'>Partial</span>";
            } else {
                $getSalesPaymentStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-danger'>Due</span>";
            }

            $saleStatus = "";
            if($value["sales_status"] === "Delivered") {
                $saleStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-success'>Delivered</span>";
            } else {
                $saleStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-warning'>{$value["sales_status"]}</span>";
            }

            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = "<iledit>{$value["sales_delivery_date"]}</iledit>";
            $allNestedData[] = "<a data-toggle='modal' data-target='#modalDefault' href='" . full_website_address() . "/xhr/?module=reports&page=showInvoiceProducts&id={$value['sales_id']}'>{$value['sales_reference']}</a>";
            $allNestedData[] = "<iledit data-val='{$value["sales_customer_id"]}'>{$value['customer_name']}, {$value['upazila_name']}, {$value['district_name']}</iledit>";
            $allNestedData[] = "<span class='copyThis'>{$value["customer_phone"]}</span>";
            $allNestedData[] = $value["sales_total_amount"];
            $allNestedData[] = $value["sales_product_discount"] + $value["sales_discount"];
            $allNestedData[] = $value["sales_shipping"];
            $allNestedData[] = $value["sales_grand_total"];
            $allNestedData[] = $value["sales_paid_amount"];
            $allNestedData[] = $value["sales_due"];
            $allNestedData[] = $value["sales_grand_total"] - $value["sales_due"];
            $allNestedData[] = $getSalesPaymentStatus;
            $allNestedData[] = "<iledit data-val='{$value["sales_status"]}'>{$saleStatus}</iledit>";
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        <li><a onClick=\'BMS.MAIN.printPage(this.href, event);\' href="'. full_website_address() .'/invoice-print/?autoPrint=true&invoiceType=posSale&id='. $value["sales_id"] .'"><i class="fa fa-print"></i> Print Invoice</a></li>
                                        <li><a target="_blank" href="'. full_website_address() .'/invoice-print/?invoiceType=posSale&id='. $value["sales_id"] .'"><i class="fa fa-edit"></i> View Purchase</a></li>
                                        <li><a data-toggle="modal" data-target="#modalDefault" href="'. full_website_address() .'/xhr/?module=my-shop&page=addPostSalesPayments&sales_id='. $value["sales_id"] .'&cid='. $value["sales_customer_id"] .'"><i class="fa fa-money"></i> Add Payment</a></li>
                                        <li><a href="'. full_website_address() .'/sales/pos/?edit='. $value["sales_id"] .'"><i class="fa fa-edit"></i> Edit Sale</a></li>
                                        <li><a onclick="getContent(this.href, event);" href="'. full_website_address() .'/stock-management/new-sales-return/?sale_id='. $value["sales_id"] .'"><i class="fa fa-undo"></i> Return</a></li>
                                        <li><a class="' . (current_user_can("myshop_pos_sales.Delete") ? "" : "restricted ") . 'deleteEntry" href="'. full_website_address() . '/xhr/?module=my-shop&page=deletePosSales" data-to-be-deleted="'. $value["sales_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                    </ul>
                                </div>' . "<pkey>{$value["sales_id"]}</pkey>";
            
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



/************************** Update sales data **********************/
if(isset($_GET['page']) and $_GET['page'] == "changeSaleStatus") {

  
    if( !current_user_can("myshop_pos_sales.Edit") ) {
        return _e("Sorry! you do not have permission to edit sale status");
    }

    // Update sales status
    $updateData = easyUpdate(
        "sales",
        array(
            "sales_status"  => $_POST["newData"]
        ),
        array(
            "sales_id"      => $_POST["pkey"]
        )
    );
    

    if($updateData === true) {
        
        echo '{
            "error": "false"
        }';

        $sales_status = $_POST["newData"];

        // Declare stock type
        $stock_type = "undeclared";
        if($sales_status === "Delivered") {
            $stock_type = "sale";
        } elseif($sales_status === "Order Placed") {
            $stock_type = "sale-order";
        } elseif($sales_status === "In Production") {
            $stock_type = "sale-production";
        } elseif($sales_status === "Processing") {
            $stock_type = "sale-processing";
        }

        // update stock type inproduct_stock
        easyUpdate(
            "product_stock",
            array(
                "stock_type"    => $stock_type
            ),
            array(
                "stock_sales_id" => $_POST["pkey"]
            )
        );


    } else {

        echo '{
            "error": "true",
            "msg": "'. $updateData .'"
        }';

    }

    
}


/************************** Shop POS Sales Add Payments **********************/
if(isset($_GET['page']) and $_GET['page'] == "addPostSalesPayments") {

    if( !current_user_can("myshop_received_payments.Add") ) {
        return _e("Sorry! you do not have permission to add sales payment");
    }
  
    // Include the modal header
    modal_header("Add Payments", full_website_address() . "/xhr/?module=my-shop&page=submitPostSalesPayments");

    $salesDueAmount = easySelectA(array(
        "table"     => "sales",
        "fields"    => "sales_due",
        "where"     => array(
            "sales_id"  => $_GET["sales_id"]
        )
    ))["data"][0]["sales_due"];
    
    ?>
      <div class="box-body">
        
        <div class="form-group required">
            <label for="salesReceivedPaymentDate"><?= __("Date:"); ?></label>
            <input type="text" name="salesReceivedPaymentDate" id="salesReceivedPaymentDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker">
        </div>
        <div class="form-group required">
            <label for="addSalesPaymentsAmount"><?= __("Amount:"); ?></label>
            <input type="number" name="addSalesPaymentsAmount" id="addSalesPaymentsAmount" onclick="this.select();" value="<?php echo number_format($salesDueAmount, 2, ".", ""); ?>" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="addSalesPaymentsMethod"><?= __("Payment Method:"); ?></label>
            <select name="addSalesPaymentsMethod" id="addSalesPaymentsMethod" class="form-control select2" style="width: 100%">
                <?php
                    $paymentMethod = array("Cash", "Bank Transfer", "Cheque", "Others");
                    
                    foreach($paymentMethod as $method) {
                        echo "<option value='{$method}'>{$method}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="addSalesPaymentsDescription"><?= __("Description:"); ?></label>
            <textarea name="addSalesPaymentsDescription" id="addSalesPaymentsDescription" rows="3" class="form-control"></textarea>
        </div>
        <input type="hidden" name="addSalesPaymentsCustomerId" value="<?php echo safe_entities($_GET["cid"]); ?>">
        <input type="hidden" name="addSalesPaymentsSalesId" value="<?php echo safe_entities($_GET["sales_id"]); ?>">

        <div id="ajaxSubmitMsg"></div>

      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Add Payments");

}


/************************** Shop POS Sales Add Payments **********************/
if(isset($_GET['page']) and $_GET['page'] == "submitPostSalesPayments") {

    if( !current_user_can("myshop_received_payments.Add") ) {
        return _e("Sorry! you do not have permission to add sales payment");
    }
    
    if(empty($_POST["addSalesPaymentsAmount"])) {
        return _e("Please enter payment amount");
    }

    // Select sales grand total
    $getSalesDetails = easySelect(
        "sales",
        "sales_grand_total, sales_paid_amount",
        array(),
        array (
            "sales_id"  => $_POST["addSalesPaymentsSalesId"]
        )
    )["data"][0];

    $selectSalesGrandTotal = $getSalesDetails["sales_grand_total"];
    $totalReceivedPayments = $_POST["addSalesPaymentsAmount"] + $getSalesDetails["sales_paid_amount"];

    if( $selectSalesGrandTotal <= $getSalesDetails["sales_paid_amount"] ) {
        return _e("The sales already paid.");
    }


    // Insert Payment into received payments table
    $addPayments = easyInsert(
        "received_payments",
        array (
            "received_payments_type"        => "Sales Payments",
            "received_payments_datetime"    => $_POST["salesReceivedPaymentDate"] . date(" H:i:s"),
            "received_payments_shop"        => $_SESSION["sid"],
            "received_payments_accounts"    => $_SESSION["aid"],
            "received_payments_sales_id"    => $_POST["addSalesPaymentsSalesId"],
            "received_payments_from"        => $_POST["addSalesPaymentsCustomerId"],
            "received_payments_amount"      => $_POST["addSalesPaymentsAmount"],
            "received_payments_details"     => $_POST["addSalesPaymentsDescription"],
            "received_payments_method"      => $_POST["addSalesPaymentsMethod"],
            "received_payments_add_on"      => date("Y-m-d H:i:s"),
            "received_payments_add_by"      => $_SESSION["uid"]
        )
    );


    // Generate the payment status
    $salesPaymentStatus = "";
    if($selectSalesGrandTotal <= $totalReceivedPayments) {

        $salesPaymentStatus = "paid";

    } else if($selectSalesGrandTotal > $totalReceivedPayments and $totalReceivedPayments > 0) {

        $salesPaymentStatus = "partial";

    } else {

        $salesPaymentStatus = "due";
    }

    // Update Payments in Sales
    easyUpdate(
        "sales",
        array (
            "sales_paid_amount"     => $totalReceivedPayments,
            "sales_due"             => ( $totalReceivedPayments >= $selectSalesGrandTotal) ? 0 : ($selectSalesGrandTotal - $totalReceivedPayments ),
            "sales_payment_status"  => $salesPaymentStatus
        ), 
        array (
            "sales_id"  => $_POST["addSalesPaymentsSalesId"]
        )
    );

    if($addPayments === true) {
        
        // Update Customer Payment Info
        // updateCustomerPaymentInfo($_POST["addSalesPaymentsCustomerId"]);
        
        // Update Accounts Balance
        updateAccountBalance($_SESSION["aid"]);

        _s("Payment successfully added.");

    }

}
  



/************************** Edit Sale note and shipping Address **********************/
if(isset($_GET['page']) and $_GET['page'] == "editSaleNote") {
  

    if( !current_user_can("myshop_pos_sales.Edit") ) {
        return _e("Sorry! you do not have permission to edit sales");
    }

    // Include the modal header
    modal_header("Edit Sale Note and Shipping Address", full_website_address() . "/xhr/?module=my-shop&page=updateSaleNote");

    $sale = easySelectA(array(
        "table"     => "sales",
        "fields"    => "sales_note, sales_shipping_address",
        "where"     => array(
            "sales_id"  => $_GET["id"]
        )
    ))["data"][0];
    
    ?>

      <div class="box-body">    
        
        <div class="form-group">
            <label for="salesNote"><?= __("Sale Note:"); ?></label>
            <textarea name="salesNote" id="salesNote" rows="3" class="form-control"><?php echo $sale["sales_note"]; ?></textarea>
        </div>
        <div class="form-group">
            <label for="salesShippingAddress"><?= __("Shipping Address:"); ?></label>
            <textarea name="salesShippingAddress" id="salesShippingAddress" rows="3" class="form-control"><?php echo $sale["sales_shipping_address"]; ?></textarea>
        </div>
        <input type="hidden" name="sales_id" value="<?php echo safe_entities($_GET["id"]); ?>">

        <div id="ajaxSubmitMsg"></div>

      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Update");

}


/************************** Update Sale Note **********************/
if(isset($_GET['page']) and $_GET['page'] == "updateSaleNote") {

    if( !current_user_can("myshop_pos_sales.Edit") ) {
        return _e("Sorry! you do not have permission to edit sales");
    }

    // Update Sales
    $updateSale = easyUpdate(
        "sales",
        array (
            "sales_note"                => $_POST["salesNote"],
            "sales_shipping_address"    => $_POST["salesShippingAddress"],
        ), 
        array (
            "sales_id"  => $_POST["sales_id"]
        )
    );


    if( $updateSale === true ) {
        _s("Successfully updated.");
    } else {
        _e($updateSale);
    }


}



/***************** Delete POS Sale ****************/
if(isset($_GET['page']) and $_GET['page'] == "deletePosSales") {


    if(!current_user_can("myshop_pos_sales.Delete")) {
        echo "{error: true, msg: 'Error: You do not have permission to delete sale'}";
        exit();
    }

    // Select the Customer ID
    $customerId = easySelect(
        "sales",
        "sales_customer_id",
        array(),
        array (
            "sales_id"  => $_POST["datatoDelete"]
        )
    )["data"][0]["sales_customer_id"];


    // Delete Sales
    $deleteSales = easyDelete(
        "sales",
        array(
            "sales_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteSales === true) {

        // Delete Received Payments Regarding this sales
        easyDelete(
            "received_payments",
            array(
                "received_payments_sales_id" => $_POST["datatoDelete"],
                " AND received_payments_type"  => "Sales Payments"
            )
        );

        // Delete Payment Return Regarding this sales
        easyDelete(
            "payments_return",
            array(
                "payments_return_sales_id"  => $_POST["datatoDelete"]
            )
        );
        
        // Update Accounts Balance
        updateAccountBalance($_SESSION["aid"]);

        echo 1;

    } 

}


/************************** Expense in My Shop **********************/
if(isset($_GET['page']) and $_GET['page'] == "addShopExpense") {
  
    // Include the modal header
    modal_header("Add New Expense", full_website_address() . "/xhr/?module=my-shop&page=newShopExpense");
    
    ?>
      <div class="box-body">
 
        <div class="form-group required">
            <label for="paymentsAddDate"><?= __("Expense Date:"); ?></label>
            <input type="text" name="paymentsAddDate" id="paymentsAddDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker" required>
        </div>
        <div class="form-group required">
            <label for="paymentCategory"><?= __("Expense Category:"); ?></label>
            <select name="paymentCategory" id="paymentCategory" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=shopPaymentCategoryList" style="width: 100%;" required>
                <option value=""><?= __("Select category"); ?>....</option>
            </select>
        </div>
        <div class="form-group required">
            <label for="paymentAmount"><?= __("Amount:"); ?></label>
            <input type="number" name="paymentAmount" id="paymentAmount" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="paymentCompany"><?= __("Company:"); ?></label>
            <select name="paymentCompany" id="paymentCompany" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=CompanyList" style="width: 100%;">
                <option value=""><?= __("Select Company"); ?>....</option>
            </select>
        </div>
        <div class="form-group">
            <label for="paymentDescription"><?= __("Description:"); ?></label>
            <textarea name="paymentDescription" id="paymentDescription" rows="3" class="form-control"></textarea>
        </div>
        <div class="form-group required">
            <label for="paymentMethods"><?= __("Payment Method:"); ?></label>
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
                <label for="paymentChequeNo"><?= __("Cheque No:"); ?></label>
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
                <label for="paymentAttachment"><?= __("Attachment:"); ?></label>
                <input type="file" name="paymentAttachment" id="paymentAttachment" class="form-control">
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
        
      </script>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Add Expense");
  
}

/************************** Add Expense in My Shop **********************/
if(isset($_GET['page']) and $_GET['page'] == "newShopExpense") {

    if( !current_user_can("myshop_expenses.Add") ) {
        return _e("Sorry! you do not have permission to add expenses");
    }

    $accounts_balance = accounts_balance($_SESSION["aid"]);

    if(empty($_POST["paymentsAddDate"])) {
        return _e("Please enter payment date");
    } else if(empty($_POST["paymentCategory"])) {
        return _e("Please select payment category");
    } else if(empty($_POST["paymentAmount"])) {
        return _e("Please enter payment amount");
    } else if(empty($_POST["paymentMethods"])) {
        return _e("Please select payment method");
    } else if($_POST["paymentMethods"] === "Cheque" and empty($_POST["paymentChequeNo"])) {
        return _e("Please enter check no.");
    } else if( !negative_value_is_allowed($_SESSION["aid"]) and $accounts_balance < $_POST["paymentAmount"] ) {
        return _e("Payment amount is exceeded of account balance (%.2f)". number_format($accounts_balance, 2));
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

    // Select last payment references
    $selectPaymentReference = easySelect(
        "payments",
        "payment_reference",
        array(),
        array (
            "payment_made_by"   => $_SESSION['uid'],
            " AND payment_reference LIKE 'BILL_PAY%'",
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
    $paymentReferences = "BILL_PAY/{$_SESSION['uid']}/";

    // check if there is minimum one records
    if($selectPaymentReference !== false) {
        $getLastReferenceNo = (int)explode($paymentReferences, $selectPaymentReference["data"][0]["payment_reference"])[1];
        $paymentReferences = $paymentReferences . ($getLastReferenceNo+1);
    } else {
        $paymentReferences = "BILL_PAY/{$_SESSION['uid']}/1";
    }

    // Insert the Bill Payment
    $insertBillPay = easyInsert (
        "payments",
        array (
            "payment_date"              => $_POST["paymentsAddDate"],
            "payment_to_company"        => empty($_POST["paymentCompany"]) ? NULL : $_POST["paymentCompany"],
            "payment_amount"            => $_POST["paymentAmount"],
            "payment_from"              => $_SESSION["aid"],
            "payment_method"            => $_POST["paymentMethods"],
            "payment_description"       => $_POST["paymentDescription"],
            "payment_cheque_no"         => empty($_POST["paymentChequeNo"]) ? NULL : $_POST["paymentChequeNo"], 
            "payment_cheque_date"       => empty($_POST["paymentChequeDate"]) ? NULL : $_POST["paymentChequeDate"],
            "payment_attachement"       => $paymentAttachment,
            "payment_reference"         => $paymentReferences,
            "payment_status"            => "Complete",
            "payment_made_by"           => $_SESSION["uid"]
        ), 
        array (
            "payment_date"                  => $_POST["paymentsAddDate"],
            " AND payment_amount"           => $_POST["paymentAmount"],
            " AND payment_from"             => $_SESSION["aid"],
            " AND payment_method"           => $_POST["paymentMethods"],
            " AND payment_made_by"          => $_SESSION["uid"]
        ),
        true
    );

    if(isset($insertBillPay["status"]) and $insertBillPay["status"] === "success" ) {

        // Insert payment items if successfully inserted in payments table
        // Insert payment items
        easyInsert(
            "payment_items",
            array (
                "payment_items_payments_id" => $insertBillPay["last_insert_id"],
                "payment_items_date"        => $_POST["paymentsAddDate"],
                "payment_items_type"        => "Bill",
                "payment_items_company"     => empty($_POST["paymentCompany"]) ? NULL : $_POST["paymentCompany"],
                "payment_items_category_id" => $_POST["paymentCategory"],
                "payment_items_amount"      => $_POST["paymentAmount"],
                "payment_items_description" => $_POST["paymentDescription"],
                "payment_items_accounts"    => $_SESSION["aid"],
                "payment_items_made_by"     => $_SESSION["uid"]
            )
        );

        // Update Accounts Balance
        updateAccountBalance($_SESSION["aid"]);

        //echo "<div class='alert alert-success'>". sprintf(__("Expense successfully added. Reference No: <strong>%s</strong>"), $paymentReferences) ."</div>";

        echo "<div class='alert alert-success'>" . sprintf(__("Expense successfully added. Reference No: <strong>%s</strong>. Please <a %s>click here to print</a> the receipt."), $paymentReferences, " onClick='BMS.MAIN.printPage(this.href, event);' href='" . full_website_address()."/invoice-print/?autoPrint=true&invoiceType=paymentReceipt&id={$insertBillPay['last_insert_id']}'") . "</div>";

    } else {
        _e($insertBillPay);
    }

}



/*************************** My Shop Expenses List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "shopExpensesList") {

    if( !current_user_can("myshop_expenses.View") ) {
        return _e("Sorry! you do not have permission to view expenses");
    }
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "payment_date",
        "payment_reference",
        "",
        "payment_amount",
        "payment_description",
        "payment_method"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "payments",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0 and payment_from" => $_SESSION["aid"]
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if(!empty($requestData["search"]["value"])) {  // get data with search
      
        $getData = easySelect(
            "payments as payment",
            "payment_id, if(item_description is not null, item_description, combine_description(payment_description, item_description)) as payment_description, payment_reference, company_name, emp_firstname, emp_lastname, payment_date, payment_amount, payment_from, payment_method",
            array (
                "left join {$table_prefix}companies on payment_to_company = company_id",
                "left join {$table_prefix}employees on payment_to_employee = emp_id",
                "left join ( select payment_items_payments_id, group_concat(payment_items_description SEPARATOR ', ' ) as item_description from {$table_prefix}payment_items group by payment_items_payments_id ) as payment_items on payment_items_payments_id = payment_id"
            ),
            array (
                "payment.is_trash = 0 and payment_from"  => $_SESSION["aid"],
                " AND company_name LIKE" => $requestData['search']['value'] . "%",
                " OR emp_firstname LIKE" => $requestData['search']['value'] . "%",
                " OR payment_description LIKE" => $requestData['search']['value'] . "%",
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
  
    } else if(!empty($requestData["columns"][1]['search']['value']) or !empty($requestData["columns"][3]['search']['value']) or !empty($requestData["columns"][6]['search']['value'])) { // Get data with search by column
  
        $getData = easySelect(
            "payments as payment",
            "payment_id, if(item_description is not null, item_description, combine_description(payment_description, item_description)) as payment_description, payment_reference, company_name, emp_firstname, emp_lastname, payment_date, payment_amount, payment_from, payment_method",
            array (
                "left join {$table_prefix}companies on payment_to_company = company_id",
                "left join {$table_prefix}employees on payment_to_employee = emp_id",
                "left join ( select payment_items_payments_id, group_concat(payment_items_description SEPARATOR ', ' ) as item_description from {$table_prefix}payment_items group by payment_items_payments_id ) as payment_items on payment_items_payments_id = payment_id"
            ),
            array (
              "payment.is_trash = 0 and payment_from"  => $_SESSION["aid"],
              " AND ( coalesce(company_name, '') LIKE '". safe_input($requestData["columns"][3]['search']['value']) ."%' ",
              " OR coalesce(emp_firstname, '') LIKE" => "%".$requestData["columns"][3]['search']['value'] . "%",
              " OR coalesce(emp_PIN, '') " => $requestData["columns"][3]['search']['value'],
              ")",
              " AND payment_date" => $requestData["columns"][1]['search']['value'],
              " AND payment_method" => $requestData["columns"][6]['search']['value']
            ), 
            array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        );
  
        
    } else { // Get data withouth search
    
      $getData = easySelect(
          "payments as payment",
          "payment_id, if(item_description is not null, item_description, combine_description(payment_description, item_description)) as payment_description, payment_reference, company_name, emp_firstname, emp_lastname, payment_date, payment_amount, payment_from, payment_method",
          array (
            "left join {$table_prefix}companies on payment_to_company = company_id",
            "left join {$table_prefix}employees on payment_to_employee = emp_id",
            "left join ( select payment_items_payments_id, group_concat(payment_items_description SEPARATOR ', ' ) as item_description from {$table_prefix}payment_items group by payment_items_payments_id ) as payment_items on payment_items_payments_id = payment_id"
          ),
          array (
            "payment.is_trash = 0 and payment_from"  => $_SESSION["aid"]
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
            $allNestedData[] = $value["payment_date"];
            $allNestedData[] = $value["payment_reference"];
            $allNestedData[] = $value["company_name"] . $value["emp_firstname"] . ' ' . $value["emp_lastname"];
            $allNestedData[] = $value["payment_amount"];
            $allNestedData[] = $value["payment_description"];
            $allNestedData[] = $value["payment_method"];
             // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        <li><a <a onClick=\'BMS.MAIN.printPage(this.href, event);\' target="_blank" href="'. full_website_address() .'/invoice-print/?invoiceType=paymentReceipt&id='. $value["payment_id"] .'"><i class="fa fa-print"></i> Print Receipt</a></li>
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



/************************** Shop Advance Collection **********************/
if(isset($_GET['page']) and $_GET['page'] == "shopAdvanceCollection") {
  
    // Include the modal header
    modal_header("Collect Advance", full_website_address() . "/xhr/?module=my-shop&page=newShopAdvanceCollection");
    
    ?>
      <div class="box-body">
        
        <div class="form-group">
            <label for="advanceCollectionDate"><?= __("Date:"); ?></label>
            <input type="text" name="advanceCollectionDate" id="advanceCollectionDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker">
        </div>
        <div class="form-group required">
            <label for="advanceCollectionFrom"><?= __("Customer:"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="From which customer the advance has been collected." class="fa fa-question-circle"></i>
            <select name="advanceCollectionFrom" id="advanceCollectionFrom" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=customerList" style="width: 100%;" required>
                <option value=""><?= __("Select Customer"); ?>....</option>
            </select>
        </div>
        <div class="form-group required">
            <label for="advanceCollectionAmount"><?= __("Amount:"); ?></label>
            <input type="number" name="advanceCollectionAmount" id="advanceCollectionAmount" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="advanceCollectionAccounts"><?= __("Accounts:"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="In which account the money will be added" class="fa fa-question-circle"></i>
            <select name="advanceCollectionAccounts" id="advanceCollectionAccounts" class="form-control select2" style="width: 100%;" required>
                <option value=""><?= __("Select Accounts"); ?>...</option>
                <?php
                    
                    foreach($selectAccounts["data"] as $accounts) {
                        echo "<option value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="advanceCollectionBonus"><?= __("Bonus:"); ?></label>
            <input type="number" name="advanceCollectionBonus" id="advanceCollectionBonus" class="form-control">
        </div>
        <div class="form-group">
            <label for="advanceDescription"><?= __("Description:"); ?></label>
            <textarea name="advanceDescription" id="advanceDescription" rows="3" class="form-control"></textarea>
        </div>
        <div class="form-group required">
            <label for="advancePaymentMethods"><?= __("Payment Method:"); ?></label>
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
                <label for="advancePaymentChequeNo"><?= __("Cheque No:"); ?></label>
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
        

      </div>
      <!-- /Box body-->
      
      <script>
        
        $(document).on("change", "#advancePaymentMethods", function() {
            if($("#advancePaymentMethods").val() == "Cheque") {
                $("#hiddenItem").css("display", "block");
            } else {
                $("#hiddenItem").css("display", "none");
            }
        });
      </script>

    <?php
  
    // Include the modal footer
    modal_footer("Add Advance");
}


/************************** Add Shop Advance Collection **********************/
if(isset($_GET['page']) and $_GET['page'] == "newShopAdvanceCollection") {

    if( !current_user_can("myshop_advance_collection.Add") ) {
        return _e("Sorry! you do not have permission to add advance collection");
    }

    if(empty($_POST["advanceCollectionFrom"])) {
        return _e("Please select customer");
    } else if(empty($_POST["advanceCollectionAmount"])) {
        return _e("Please enter advance amount.");
    } else if(empty($_POST["advanceCollectionAccounts"])) {
        return _("Please select accounts");
    }

    $advancedCollectionTime = $_POST["advanceCollectionDate"] . date(" H:i:s");
    // Insert Advance collection
    $inserAdvanceCollection = easyInsert(
        "received_payments",
        array (
            "received_payments_type"        => "Advance Collection",
            "received_payments_shop"        => $_SESSION["sid"],
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
            "received_payments_shop"            => $_SESSION["sid"],
            " AND received_payments_accounts"   => $_POST["advanceCollectionAccounts"],
            " AND received_payments_from"       => $_POST["advanceCollectionFrom"],
            " AND received_payments_amount"     => $_POST["advanceCollectionAmount"],
            " AND received_payments_type"       => "Advance Collection"
        ),
        true
    );

    if( isset($inserAdvanceCollection["status"]) and $inserAdvanceCollection["status"] === 'success' ) {

        // Update Customer Payment Info
        // This function call is no longer required
        // updateCustomerPaymentInfo($_POST["advanceCollectionFrom"]);
        
        // Update Accounts Balance
        updateAccountBalance($_POST["advanceCollectionAccounts"]);

        $successMsg = sprintf(__("Advance successfully added. <a %s>Click Here</a> to print the receipt."), " onClick='BMS.MAIN.printPage(this.href, event);' href='". full_website_address() ."/invoice-print/?invoiceType=moneyReceipt&id=". $inserAdvanceCollection["last_insert_id"] ."'");

        echo "<div class='alert alert-success'>{$successMsg}</div>";

        $customerInfo = easySelectA(array(
            "table"     => "customers",
            "fileds"    => "customer_phone, send_notif",
            "where"     => array(
                "customer_id"   => $_POST["advanceCollectionFrom"]
            )
        ))["data"][0];

        // If Customer notification is enable then send it
        if( $customerInfo["send_notif"] ) {
            
            $customerNumber = $customerInfo["customer_phone"];
            $t = send_sms($customerNumber, "Dear sir, Tk " . number_format($_POST["advanceCollectionAmount"], 2) . " has been received successfully as on ". date("d M, Y", strtotime($_POST["advanceCollectionDate"]) ) . ". Thank You. \n-". get_options("companyName") );

        }


    } else {
        _e($inserAdvanceCollection);
    }
    
}


/*************************** My Shop Advance Collection List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "shopAdvanceCollectionList") {

    if( !current_user_can("myshop_advance_collection.View") ) {
        return _e("Sorry! you do not have permission to view advance collection list");
    }
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "received_payments_datetime",
        "customer_name",
        "received_payments_amount",
        "received_payments_bonus",
        "received_payments_details"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "received_payments",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0 and received_payments_shop" => $_SESSION["sid"], 
            " AND received_payments_type" => "Advance Collection" 
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if(!empty($requestData["search"]["value"])) {  // get data with search
      
        $getData = easySelect(
            "received_payments as received_payment",
            "received_payments_id, customer_name, received_payments_shop, received_payments_from, received_payments_amount, received_payments_bonus, received_payments_details, received_payments_method, received_payments_cheque_no, received_payments_cheque_date, received_payments_datetime, upazila_name, district_name",
            array (
                "left join {$table_prefix}customers on received_payments_from = customer_id",
                "left join {$table_prefix}upazilas on customer_upazila = upazila_id",
                "left join {$table_prefix}districts on customer_district = district_id"
            ),
            array (
                "received_payment.is_trash = 0 and received_payments_type" => "Advance Collection",
                " AND received_payments_shop"  => $_SESSION["sid"],
                " AND customer_name LIKE" => $requestData['search']['value'] . "%"
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
            "received_payments_id, customer_name, received_payments_shop, received_payments_from, received_payments_amount, received_payments_bonus, received_payments_details, received_payments_method, received_payments_cheque_no, received_payments_cheque_date, received_payments_datetime, upazila_name, district_name",
            array (
                "left join {$table_prefix}customers on received_payments_from = customer_id",
                "left join {$table_prefix}upazilas on customer_upazila = upazila_id",
                "left join {$table_prefix}districts on customer_district = district_id"
            ),
            array (
                "received_payment.is_trash = 0 and received_payments_type" => "Advance Collection",
                " AND received_payments_shop"  => $_SESSION["sid"]
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
            $allNestedData[] = "Receipt-" . $value["received_payments_id"];
            $allNestedData[] = "{$value["customer_name"]}, {$value["upazila_name"]}, {$value["district_name"]}";
            $allNestedData[] = $value["received_payments_amount"];
            $allNestedData[] = $value["received_payments_bonus"];
            $allNestedData[] = "<strong>Description:</strong> {$value['received_payments_details']}; <strong>Method:</strong> {$value['received_payments_method']}; <br/> <strong>Cheque No:</strong> {$value['received_payments_cheque_no']}; <strong>Cheque Date:</strong> {$value['received_payments_cheque_date']}";
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a onClick=\'BMS.MAIN.printPage(this.href, event);\' href="'. full_website_address() .'/invoice-print/?invoiceType=moneyReceipt&id='. $value["received_payments_id"] .'"><i class="fa fa-print"></i> Print Receipt</a></li>
                                    <li><a data-toggle="modal" data-target="#modalDefault" href="'. full_website_address() .'/xhr/?module=my-shop&page=editShopAdvanceCollection&id='. $value["received_payments_id"] .'"><i class="fa fa-edit"></i> Edit Payment</a></li>
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=my-shop&page=deleteAdvanceCollection" data-to-be-deleted="'. $value["received_payments_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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


/************************** Shop Advance Collection edit **********************/
if(isset($_GET['page']) and $_GET['page'] == "editShopAdvanceCollection") {

    if( !current_user_can("myshop_advance_collection.Edit") ) {
        return _e("Sorry! you do not have permission to edit advance collection");
    }
  
    // Include the modal header
    modal_header("Edit Advance Collection", full_website_address() . "/xhr/?module=my-shop&page=updateShopAdvanceCollection");

    $ac = easySelectA(array(
        "table" => "received_payments",
        "where" => array(
            "received_payments_id" => $_GET["id"]
        ),
        "join"  => array(
            "left join {$table_prefix}customers on received_payments_from = customer_id"
        )
    ))["data"][0];
    
    ?>
      <div class="box-body">
        
        <div class="form-group">
            <label for="advanceCollectionDate"><?= __("Date:"); ?></label>
            <input type="text" name="advanceCollectionDate" id="advanceCollectionDate" value="<?php echo date("Y-m-d", strtotime($ac["received_payments_datetime"]) ); ?>" class="form-control datePicker">
        </div>
        <div class="form-group required">
            <label for="advanceCollectionFrom"><?= __("Customer:"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="From which customer the advance has been collected." class="fa fa-question-circle"></i>
            <select name="advanceCollectionFrom" id="advanceCollectionFrom" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=customerList" style="width: 100%;" required>
                <option value=""><?= __("Select Customer"); ?>....</option>
                <option selected value="<?= $ac["received_payments_from"]; ?>"><?= $ac["customer_name"]; ?></option>
            </select>
        </div>
        <div class="form-group required">
            <label for="advanceCollectionAmount"><?= __("Amount:"); ?></label>
            <input type="number" name="advanceCollectionAmount" id="advanceCollectionAmount" value="<?php echo number_format($ac["received_payments_amount"], 2, ".", ""); ?>" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="advanceCollectionAccounts"><?= __("Accounts:"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="In which account the money will be added" class="fa fa-question-circle"></i>
            <select name="advanceCollectionAccounts" id="advanceCollectionAccounts" class="form-control select2" style="width: 100%;" required>
                <option value=""><?= __("Select Accounts"); ?>...</option>
                <?php
                    
                    foreach($selectAccounts["data"] as $accounts) {
                        $selected = ( $ac["received_payments_accounts"] == $accounts['accounts_id'] ) ? "selected" : "";
                        echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="advanceCollectionBonus"><?= __("Bonus:"); ?></label>
            <input type="number" name="advanceCollectionBonus" id="advanceCollectionBonus" value="<?php echo number_format($ac["received_payments_bonus"], 2, ".", ""); ?>" class="form-control">
        </div>
        <div class="form-group">
            <label for="advanceDescription"><?= __("Description:"); ?></label>
            <textarea name="advanceDescription" id="advanceDescription" rows="3" class="form-control"> <?php echo $ac["received_payments_details"]; ?> </textarea>
        </div>
        <div class="form-group required">
            <label for="advancePaymentMethods"><?= __("Payment Method:"); ?></label>
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
                <label for="advancePaymentChequeNo"><?= __("Cheque No:"); ?></label>
                <input type="text" name="advancePaymentChequeNo" id="advancePaymentChequeNo" value="<?php echo $ac["received_payments_cheque_no"]; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="advancePaymentChequeDate"><?= __("Cheque Date:"); ?></label>
                <input type="text" name="advancePaymentChequeDate" id="advancePaymentChequeDate" value="<?php echo $ac["received_payments_cheque_date"]; ?>" class="form-control datePicker">
            </div>
            <div class="form-group">
                <label for="advancePaymentReference"><?= __("Reference:"); ?></label>
                <input type="text" name="advancePaymentReference" id="advancePaymentReference" value="<?php echo $ac["received_payments_reference"]; ?>" class="form-control">
            </div>
        </div>
        <input type="hidden" name="shopAdvanceCollectionId" value ="<?php echo safe_entities($_GET["id"]); ?>">

        <div id="ajaxSubmitMsg"></div>
        

      </div>
      <!-- /Box body-->
      
      <script>
        
        $(document).on("change", "#advancePaymentMethods", function() {
            if($("#advancePaymentMethods").val() == "Cheque") {
                $("#hiddenItem").css("display", "block");
            } else {
                $("#hiddenItem").css("display", "none");
            }
        });
      </script>

    <?php
  
    // Include the modal footer
    modal_footer("Update Advance Collection");
}


/************************** Add Shop Advance Collection **********************/
if(isset($_GET['page']) and $_GET['page'] == "updateShopAdvanceCollection") {

    if( !current_user_can("myshop_advance_collection.Edit") ) {
        return _e("Sorry! you do not have permission to edit advance collection");
    }

    if(empty($_POST["advanceCollectionFrom"])) {
        return _e("Please select customer");
    } else if(empty($_POST["advanceCollectionAmount"])) {
        return _e("Please enter advance amount.");
    } else if(empty($_POST["advanceCollectionAccounts"])) {
        return _("Please select accounts");
    }

    $advancedCollectionTime = $_POST["advanceCollectionDate"] . date(" H:i:s");
    // Update Advance collection
    $updateAdvanceCollection = easyUpdate(
        "received_payments",
        array (
            "received_payments_type"        => "Advance Collection",
            "received_payments_shop"        => $_SESSION["sid"],
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
            "received_payments_id"  => $_POST["shopAdvanceCollectionId"]
        ),
        true
    );

    if( $updateAdvanceCollection === true ) {

        // Update Accounts Balance
        updateAccountBalance($_POST["advanceCollectionAccounts"]);

        $successMsg = sprintf(__("Advance collected successfully updated. <a %s>Click Here</a> to print the receipt."), " onClick='BMS.MAIN.printPage(this.href, event);' href='". full_website_address() ."/invoice-print/?invoiceType=moneyReceipt&id=". safe_entities($_POST["shopAdvanceCollectionId"]) ."'");

        echo "<div class='alert alert-success'>{$successMsg}</div>";

        $customerInfo = easySelectA(array(
            "table"     => "customers",
            "fileds"    => "customer_phone, send_notif",
            "where"     => array(
                "customer_id"   => $_POST["advanceCollectionFrom"]
            )
        ))["data"][0];

        // If Customer notification is enable then send it
        if( $customerInfo["send_notif"] ) {
            
            $customerNumber = $customerInfo["customer_phone"];
            $t = send_sms($customerNumber, "Dear sir, Tk " . number_format($_POST["advanceCollectionAmount"], 2) . " has been updated successfully as on ". date("d M, Y", strtotime($_POST["advanceCollectionDate"]) ) . ". Thank You. \n-". get_options("companyName") );

        }


    } else {
        _e($updateAdvanceCollection);
    }
    
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
        //updateCustomerPaymentInfo( $selectAdvanceCollection["received_payments_from"] );

        // Update Accounts Balance
        updateAccountBalance( $selectAdvanceCollection["received_payments_accounts"] );

        echo '{
            "title": "'. __("The entry has been deleted successfully.") .'"
        }';
    } 
}



/************************** Shop Add Received Payments **********************/
if(isset($_GET['page']) and $_GET['page'] == "shopAddReceivedPayments") {
  
    // Include the modal header
    modal_header("Add Received Payment", full_website_address() . "/xhr/?module=my-shop&page=newShopAddReceivedPayments");
    
    ?>
      <div class="box-body">
        
        <div class="form-group required">
            <label for="receivedPaymentDate"><?= __("Date:"); ?></label>
            <input type="text" name="receivedPaymentDate" id="receivedPaymentDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker">
        </div>
        <div class="form-group required">
            <label for="receivedPaymentsFrom"><?= __("Customer:"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="From which customer the payments is receving from." class="fa fa-question-circle"></i>
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
                        <td class="text-right">Shipping:</td>
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
            <label for="receivedPaymentsAmount"><?= __("Received Amount:"); ?></label>
            <input type="number" name="receivedPaymentsAmount" id="receivedPaymentsAmount" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="receivedPaymentsAccount"><?= __("Accounts:"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="In which account the money will be added" class="fa fa-question-circle"></i>
            <select name="receivedPaymentsAccount" id="receivedPaymentsAccount" class="form-control select2" style="width: 100%;" required>
                <option value=""><?= __("Select Accounts"); ?>...</option>
                <?php
                    
                    foreach($selectAccounts["data"] as $accounts) {
                        echo "<option value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="receivedPaymentsDescription"><?= __("Description:"); ?></label>
            <textarea name="receivedPaymentsDescription" id="receivedPaymentsDescription" rows="3" class="form-control"></textarea>
        </div>
        <div class="form-group required">
            <label for="receivedPaymentsMethods"><?= __("Payment Method:"); ?></label>
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
                <label for="receivedPaymentsChequeNo"><?= __("Cheque No:"); ?></label>
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

            $(document).on("change", "#receivedPaymentsFrom", function() {

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


/************************** Add Shop Received Payments **********************/
if(isset($_GET['page']) and $_GET['page'] == "newShopAddReceivedPayments") {

    if( !current_user_can("myshop_received_payments.Add") ) {
        return _e("Sorry! you do not have permission to add payment");
    }

    if(empty($_POST["receivedPaymentsFrom"])) {
        return _e("Please select customer");
    } else if(empty($_POST["receivedPaymentsAmount"])) {
        return _e("Please enter payments amount.");
    }


    // Insert Received Payment
    $inserReceivePayments = easyInsert(
        "received_payments",
        array (
            "received_payments_type"        => "Received Payments",
            "received_payments_shop"        => $_SESSION["sid"],
            "received_payments_accounts"    => $_POST["receivedPaymentsAccount"],
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
            "received_payments_shop"            => $_SESSION["sid"],
            " AND received_payments_accounts"   => $_POST["receivedPaymentsAccount"],
            " AND received_payments_from"       => $_POST["receivedPaymentsFrom"],
            " AND received_payments_amount"     => $_POST["receivedPaymentsAmount"],
            " AND received_payments_type"       => "Received Payments"
        ),
        true
    );


    if( isset($inserReceivePayments["status"]) and $inserReceivePayments["status"] === 'success' ) {

        // Update Customer Payment Info
        // updateCustomerPaymentInfo($_POST["receivedPaymentsFrom"]);

        // Update Accounts Balance
        updateAccountBalance($_POST["receivedPaymentsAccount"]);

        $successMsg = sprintf(__("Payment received successfully. <a %s>Click Here</a> to print the receipt."), " onClick='BMS.MAIN.printPage(this.href, event);' href='". full_website_address() ."/invoice-print/?invoiceType=moneyReceipt&id=". $inserReceivePayments["last_insert_id"] ."'");

        echo "<div class='alert alert-success'>{$successMsg}</div>";

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
        


    } else {
        _e($inserReceivePayments);
    }
    
}


/************************** Add Discount **********************/
if(isset($_GET['page']) and $_GET['page'] == "addDiscount") {
  
    // Include the modal header
    modal_header("Add Discount", full_website_address() . "/xhr/?module=my-shop&page=addNewDiscount");
    
    ?>
      <div class="box-body">

        <div class="form-group">
            <label for="discountDate"><?= __("Date:"); ?></label>
            <input type="text" name="discountDate" id="discountDate" value="<?= date("Y-m-d"); ?>" class="form-control datePicker">
        </div>
        <div class="form-group required">
            <label for="discountCustomer"><?= __("Customer:"); ?></label>
            <select name="discountCustomer" id="discountCustomer" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=customerList" style="width: 100%;" required>
                <option value=""><?= __("Select Customer:"); ?>....</option>
            </select>
        </div>
        <div class="form-group required">
            <label for="discountAmount"><?= __("Discount Amount:"); ?></label>
            <input type="number" name="discountAmount" id="discountAmount" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="discountDescription"><?= __("Description:"); ?></label>
            <textarea name="discountDescription" id="discountDescription" rows="3" class="form-control"></textarea>
        </div>

        <div id="ajaxSubmitMsg"></div>

      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Add Discount");
  
}


/************************** Add New Discount **********************/
if(isset($_GET['page']) and $_GET['page'] == "addNewDiscount") {

    if( !current_user_can("myshop_discount.Add") ) {
        return _e("Sorry! you do not have permission to add discount");
    }

    if(empty($_POST["discountCustomer"])) {
        return _e("Please select customer");
    }  else if(empty($_POST["discountAmount"])) {
        return _e("Please enter discount amount.");
    }

    // Insert discount
    $inserDiscount = easyInsert(
        "received_payments",
        array (
            "received_payments_type"        => "Discounts",
            "received_payments_accounts"    => NULL,
            "received_payments_shop"        => $_SESSION["sid"],
            "received_payments_from"        => $_POST["discountCustomer"],
            "received_payments_amount"      => $_POST["discountAmount"],
            "received_payments_details"     => $_POST["discountDescription"],
            "received_payments_datetime"    => $_POST["discountDate"] . date(" H:i:s"),
            "received_payments_add_by"      => $_SESSION["uid"]
        ), 
        array (
            "received_payments_shop"            => $_SESSION["sid"],
            " AND received_payments_from"       => $_POST["discountCustomer"],
            " AND received_payments_amount"     => $_POST["discountAmount"],
            " AND received_payments_type"       => "Discounts",
        )
    );

    if($inserDiscount === true) {
        // Update Customer Payment Info
        // updateCustomerPaymentInfo($_POST["discountCustomer"]);

        _s("Discounts Successfully added.");

    } else {

        _e($inserDiscount);

    }
    
}


/*************************** My Shop Received Payments List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "shopReceivedPaymentsList") {

    if( !current_user_can("myshop_received_payments.View") ) {
        return _e("Sorry! you do not have permission to view receive payment list");
    }
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "received_payments_datetime",
        "customer_name",
        "received_payments_amount",
        "received_payments_bonus",
        "received_payments_details"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "received_payments",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0 and received_payments_shop" => $_SESSION["sid"], 
            " AND received_payments_type" => "Received Payments"
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if(!empty($requestData["search"]["value"])) {  // get data with search
      
        $getData = easySelect(
            "received_payments as received_payment",
            "received_payments_id, customer_name, received_payments_shop, received_payments_from, received_payments_amount, received_payments_details, received_payments_method, received_payments_cheque_no, received_payments_cheque_date, received_payments_datetime, upazila_name, district_name",
            array (
                "left join {$table_prefix}customers on received_payments_from = customer_id",
                "left join {$table_prefix}upazilas on customer_upazila = upazila_id",
                "left join {$table_prefix}districts on customer_district = district_id"
            ),
            array (
                "received_payment.is_trash = 0 and received_payments_type" => "Received Payments",
                " AND received_payments_shop"  => $_SESSION["sid"],
                " AND customer_name LIKE" => $requestData['search']['value'] . "%"
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
            "received_payments_id, customer_name, received_payments_shop, received_payments_from, received_payments_amount, received_payments_details, received_payments_method, received_payments_cheque_no, received_payments_cheque_date, received_payments_datetime, upazila_name, district_name",
            array (
                "left join {$table_prefix}customers on received_payments_from = customer_id",
                "left join {$table_prefix}upazilas on customer_upazila = upazila_id",
                "left join {$table_prefix}districts on customer_district = district_id"
            ),
            array (
                "received_payment.is_trash = 0 and received_payments_type" => "Received Payments",
                " AND received_payments_shop"  => $_SESSION["sid"]
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
            $allNestedData[] = "Receipt-" . $value["received_payments_id"];
            $allNestedData[] = "{$value["customer_name"]}, {$value["upazila_name"]}, {$value["district_name"]}";
            $allNestedData[] = $value["received_payments_amount"];
            $allNestedData[] = "<strong>Description:</strong> {$value['received_payments_details']}; <strong>Method:</strong> {$value['received_payments_method']}; <br/> <strong>Cheque No:</strong> {$value['received_payments_cheque_no']}; <strong>Cheque Date:</strong> {$value['received_payments_cheque_date']}";
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a onClick=\'BMS.MAIN.printPage(this.href, event);\' href="'. full_website_address() .'/invoice-print/?invoiceType=moneyReceipt&id='. $value["received_payments_id"] .'"><i class="fa fa-print"></i> Print Receipt</a></li>
                                    <li><a data-toggle="modal" data-target="#modalDefault" href="'. full_website_address() .'/xhr/?module=my-shop&page=editReceivedPayment&id='. $value["received_payments_id"] .'"><i class="fa fa-edit"></i> Edit Payment</a></li>
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=my-shop&page=deleteReceivedPayment" data-to-be-deleted="'. $value["received_payments_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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

    if(current_user_can("myshop_received_payments.Delete") !== true) {
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

    if( !current_user_can("myshop_received_payments.Edit") ) {
        return _e("Sorry! you do not have permission to edit receive payment");
    }
  
    // Include the modal header
    modal_header("Edit Received Payment", full_website_address() . "/xhr/?module=my-shop&page=updateReceivedPayments");

    $rp = easySelectA(array(
        "table" => "received_payments",
        "where" => array(
            "received_payments_id" => $_GET["id"]
        ),
        "join"  => array(
            "left join {$table_prefix}customers on received_payments_from = customer_id"
        )
    ))["data"][0];
    
    ?>
      <div class="box-body">
        
        <div class="form-group required">
            <label for="receivedPaymentDate"><?= __("Date:"); ?></label>
            <input type="text" name="receivedPaymentDate" id="receivedPaymentDate" value="<?php echo date("Y-m-d", strtotime($rp["received_payments_datetime"]) ); ?>" class="form-control datePicker">
        </div>
        <div class="form-group required">
            <label for="receivedPaymentsFrom"><?= __("Customer:"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="From which customer the payments is receving from." class="fa fa-question-circle"></i>
            <select name="receivedPaymentsFrom" id="receivedPaymentsFrom" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=customerList" style="width: 100%;" required>
                <option value="<?= $rp["received_payments_from"]; ?>"><?= $rp["customer_name"]; ?></option>
            </select>
        </div>
        <div class="form-group required">
            <label for="receivedPaymentsAmount"><?= __("Received Amount:"); ?></label>
            <input type="number" name="receivedPaymentsAmount" id="receivedPaymentsAmount" value="<?= number_format($rp["received_payments_amount"], 2, ".", ""); ?>" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="receivedPaymentsAccount"><?= __("Accounts:"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="In which account the money will be added" class="fa fa-question-circle"></i>
            <select name="receivedPaymentsAccount" id="receivedPaymentsAccount" class="form-control select2" style="width: 100%;" required>
                <?php
                    
                    foreach($selectAccounts["data"] as $accounts) {
                        $selected = ( $rp["received_payments_accounts"] == $accounts['accounts_id'] ) ? "selected" : "";
                        echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="receivedPaymentsDescription"><?= __("Description:"); ?></label>
            <textarea name="receivedPaymentsDescription" id="receivedPaymentsDescription" rows="3" class="form-control"><?= $rp["received_payments_details"]; ?></textarea>
        </div>
        <div class="form-group required">
            <label for="receivedPaymentsMethods"><?= __("Payment Method:"); ?></label>
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
                <label for="receivedPaymentsChequeNo"><?= __("Cheque No:"); ?></label>
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

    if( !current_user_can("myshop_received_payments.Edit") ) {
        return _e("Sorry! you do not have permission to edit receive payment");
    }

    if(empty($_POST["receivedPaymentsFrom"])) {
        return _e("Please select customer");
    } else if(empty($_POST["receivedPaymentsAmount"])) {
        return _e("Please enter payments amount.");
    }

    // Select the previous account
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
            "received_payments_type"        => "Received Payments",
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

        _s("Successfully Updated.");

    } else {
        _e($updateReceivedPayments);
    }
    
}



/*************************** My Shop Discounts List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "discountsList") {

    if( !current_user_can("myshop_discount.View") ) {
        return _e("Sorry! you do not have permission to view discount list");
    }
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "received_payments_datetime",
        "customer_name",
        "received_payments_amount",
        "received_payments_details"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "received_payments",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0 and received_payments_shop" => $_SESSION["sid"], 
            " AND received_payments_type" => "Discounts"
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if(!empty($requestData["search"]["value"])) {  // get data with search
      
        $getData = easySelect(
            "received_payments as received_payment",
            "received_payments_id, customer_name, received_payments_shop, received_payments_from, received_payments_amount, received_payments_details, received_payments_datetime",
            array (
                "left join {$table_prefix}customers on received_payments_from = customer_id"
            ),
            array (
                "received_payment.is_trash = 0 and received_payments_type" => "Discounts",
                " AND received_payments_shop"  => $_SESSION["sid"],
                " AND customer_name LIKE" => $requestData['search']['value'] . "%"
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
            "received_payments_id, customer_name, received_payments_shop, received_payments_from, received_payments_amount, received_payments_details, received_payments_datetime",
            array (
                "left join {$table_prefix}customers on received_payments_from = customer_id"
            ),
            array (
                "received_payment.is_trash = 0 and received_payments_type" => "Discounts",
                " AND received_payments_shop"  => $_SESSION["sid"]
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
            $allNestedData[] = $value["customer_name"];
            $allNestedData[] = to_money($value["received_payments_amount"]);
            $allNestedData[] = $value['received_payments_details'];
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a data-toggle="modal" data-target="#modalDefault" href="'. full_website_address() .'/xhr/?module=my-shop&page=editDiscount&id='. $value["received_payments_id"] .'"><i class="fa fa-edit"></i> Edit Discount</a></li>
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=my-shop&page=deleteDiscount" data-to-be-deleted="'. $value["received_payments_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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


/************************** Edit Discount **********************/
if(isset($_GET['page']) and $_GET['page'] == "editDiscount") {

    if( !current_user_can("myshop_discount.Edit") ) {
        return _e("Sorry! you do not have permission to edit discount");
    }
  
    // Include the modal header
    modal_header("Edit Discount", full_website_address() . "/xhr/?module=my-shop&page=updateDiscount");

    $selectDiscount = easySelectA(array(
        "table"     => "received_payments",
        "fields"    => "received_payments_datetime, received_payments_from, received_payments_amount, received_payments_details, customer_name",
        "where" => array(
            "received_payments_type = 'Discounts' and received_payments_id" => $_GET["id"]
        ),
        "join"  => array(
            "left join {$table_prefix}customers on customer_id = received_payments_from"
        )
    ))["data"][0];
    
    ?>
      <div class="box-body">

        <div class="form-group">
            <label for="discountDate"><?= __("Date:"); ?></label>
            <input type="text" name="discountDate" id="discountDate" value="<?= date("Y-m-d", strtotime($selectDiscount["received_payments_datetime"]) ); ?>" class="form-control datePicker">
        </div>
        <div class="form-group">
            <label for="discountCustomer"><?= __("Customer:"); ?></label>
            <select name="discountCustomer" id="discountCustomer" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=customerList" style="width: 100%;" required>
                <option value="<?php echo $selectDiscount["received_payments_from"]; ?>"><?php echo $selectDiscount["customer_name"]; ?></option>
            </select>
        </div>
        <div class="form-group required">
            <label for="discountAmount"><?= __("Discount Amount"); ?></label>
            <input onclick="this.select();" type="number" name="discountAmount" id="discountAmount" value="<?php echo number_format($selectDiscount["received_payments_amount"], 2, ".", ""); ?>" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="discountDescription"><?= __("Description:"); ?></label>
            <textarea name="discountDescription" id="discountDescription" rows="3" class="form-control"><?php echo $selectDiscount["received_payments_details"]; ?></textarea>
        </div>
        <input type="hidden" name="discountId" value="<?php echo safe_entities($_GET["id"]); ?>">

        <div id="ajaxSubmitMsg"></div>

      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Update Discount");
  
}


/***************** Delete Discount ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteDiscount") {

    if( !current_user_can("myshop_discount.Delete") ) {
        return _e("Sorry! you do not have permission to delete discount");
    }

    $deleteData = easyDelete(
        "received_payments",
        array(
            "received_payments_type = 'Discounts' and  received_payments_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {
        echo '{
            "title": "'. __("The Discount has been deleted successfully.") .'"
        }';
    } 
}



/************************** Add New Discount **********************/
if(isset($_GET['page']) and $_GET['page'] == "updateDiscount") {

    if( !current_user_can("myshop_discount.Edit") ) {
        return _e("Sorry! you do not have permission to edit discount");
    }

    if(empty($_POST["discountCustomer"])) {
        return __("Please select customer");
    }  else if(empty($_POST["discountAmount"])) {
        return __("Please enter discount amount.");
    }

    // Update discount
    $updateDiscount = easyUpdate(
        "received_payments",
        array (
            "received_payments_from"        => $_POST["discountCustomer"],
            "received_payments_amount"      => $_POST["discountAmount"],
            "received_payments_details"     => $_POST["discountDescription"],
            "received_payments_datetime"    => $_POST["discountDate"] . date(" H:i:s"),
        ),
        array(
            "received_payments_id" => $_POST["discountId"]
        )
    );

    if($updateDiscount === true) {
        _s("Discounts Successfully added.");
    } else {
        _e($updateDiscount);
    }
    
}

/************************** Transfer Money from my shop **********************/
if(isset($_GET['page']) and $_GET['page'] == "myShopNewTransferBalance") {

    // Include the modal header
    modal_header("Transfer Money", full_website_address() . "/xhr/?module=my-shop&page=addNewmyShopTransferBalance");
    
    ?>

      <div class="box-body">
        
        <div class="form-group required">
            <label for="transferDate"><?= __("Date:"); ?></label>
            <input type="text" name="transferDate" id="transferDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker" required>
        </div>

        <div class="form-group required">
            <label for="transferAcountsTO"><?= __("To Accounts:"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="In which account the money is transfer to" class="fa fa-question-circle"></i>
            <select name="transferAcountsTO" id="transferAcountsTO" class="form-control select2" style="width: 100%;" required>
                <option value=""><?= __("Select Accounts"); ?>...</option>
                <?php
                    foreach($selectAccounts["data"] as $accounts) {
                        echo "<option value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>
      
        <div class="form-group required">
            <label for="transferAmount"><?= __("Amount:"); ?></label>
            <input type="number" name="transferAmount" id="transferAmount" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="transferDescription"><?= __("Description:"); ?></label>
            <textarea name="transferDescription" id="transferDescription" rows="3" class="form-control"></textarea>
        </div>

      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Add Transfer");
  
}


/************************** New Transfer Money **********************/
if(isset($_GET['page']) and $_GET['page'] == "addNewmyShopTransferBalance") {

    if( !current_user_can("myshop_transfer_balance.Add") ) {
        return _e("Sorry! you do not have permission to transfer balance");
    }

    $accounts_balance = accounts_balance($_SESSION["aid"]);

    if(empty($_POST["transferDate"])) {
        return _e("Please select transfer date");
    } elseif(empty($_POST["transferAcountsTO"])) {
        return _e("Please select to accounts");
    } elseif(empty($_POST["transferAmount"])) {
        return _e("Please enter transfer amount");
    } else if( !negative_value_is_allowed($_SESSION["aid"]) and $accounts_balance < $_POST["transferAmount"] ) {
        return _e("Transfer amount is exceeded of account balance (%.2f)", number_format($accounts_balance, 2));
    }

    $insertTransfer = easyInsert(
        "transfer_money",
        array (
            "transfer_money_date"       => $_POST["transferDate"],
            "transfer_money_from"       => $_SESSION["aid"],
            "transfer_money_to"         => $_POST["transferAcountsTO"],
            "transfer_money_amount"     => $_POST["transferAmount"],
            "transfer_money_description"=> $_POST["transferDescription"],
            "transfer_money_made_by"    => $_SESSION["uid"]
        ),
        array (
            "transfer_money_date"           => $_POST["transferDate"],
            " AND transfer_money_from"      => $_SESSION["aid"],
            " AND transfer_money_to"        => $_POST["transferAcountsTO"],
            " AND transfer_money_amount"    => $_POST["transferAmount"]
        )
    );

    if($insertTransfer === true) {
        // Update Accounts Balance
        updateAccountBalance($_SESSION["aid"]);
        updateAccountBalance($_POST["transferAcountsTO"]);

        _s("Transfer sucessfully completed");

    } else {
        _e($insertTransfer);
    }

}

/*************************** Transfer List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "transferBalanceList") {

    if( !current_user_can("myshop_transfer_balance.View") ) {
        return _e("Sorry! you do not have permission to view transfer balance");
    }
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "transfer_money_id",
        "from_accounts_name",
        "to_accounts_name",
        "transfer_money_amount",
        "transfer_money_description"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "transfer_money",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0 and (transfer_money_from"  => $_SESSION["aid"],
            " OR transfer_money_to"  => $_SESSION["aid"],
            ")"
        )
    ))["data"][0]["totalRow"];
    
    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
        
    $getData = easySelect(
        "transfer_money as transfer_money",
        "transfer_money_id, transfer_money_date, transfer_money_from, transfer_money_to, from_accounts.accounts_name as from_accounts_name, to_accounts.accounts_name as to_accounts_name, transfer_money_amount, transfer_money_description",
        array (
            "inner join {$table_prefix}accounts as from_accounts on transfer_money_from = from_accounts.accounts_id",
            "inner join {$table_prefix}accounts as to_accounts on transfer_money_to = to_accounts.accounts_id"
        ),
        array (
            "transfer_money.is_trash = 0 and (transfer_money_from"  => $_SESSION["aid"],
            " OR transfer_money_to"  => $_SESSION["aid"],
            ")",
            " AND transfer_money_description LIKE" => (!empty($requestData['search']['value'])) ? "%{$requestData['search']['value']}%" : ""
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

    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['data'])) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["transfer_money_date"];
            $allNestedData[] = $value["from_accounts_name"];
            $allNestedData[] = $value["to_accounts_name"];
            $allNestedData[] = $value["transfer_money_amount"];
            $allNestedData[] = $value["transfer_money_description"];
            
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


?>