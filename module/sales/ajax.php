<?php

// Select the Accounts
$selectAccounts = easySelect("accounts", "accounts_id, accounts_name", array(), array("is_trash" => 0));

/*************************** Pos Sale List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "posSaleList") {

    if( !current_user_can("sale_pos_sale.View") ) {
        return _e("Sorry! you do not have permission to view sale list");
    }
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "sales_id",
        "shop_name",
        "sales_id",
        "customer_name",
        "sales_total_amount",
        "sales_product_discount",
        "sales_shipping",
        "sales_grand_total",
        "sales_paid_amount",
        "sales_due",
        "",
        "",
        "sales_payment_status"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "sales",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if( !empty($requestData["search"]["value"]) or 
        !empty($requestData["columns"][1]['search']['value']) or 
        !empty($requestData["columns"][2]['search']['value']) or 
        !empty($requestData["columns"][3]['search']['value']) or 
        !empty($requestData["columns"][4]['search']['value']) or 
        !empty($requestData["columns"][13]['search']['value']) or
        !empty($requestData["columns"][14]['search']['value'])
    ) { // Get data with search by column
        
        $dateRange[0] = "";
        $dateRange[1] = "";
        if(!empty($requestData["columns"][1]['search']['value'])) {
            $dateRange = explode(" - ", safe_input($requestData["columns"][1]['search']['value']));
        }

        $getData = easySelect(
            "sales as sale",
            "sales_id, sales_delivery_date, sales_note, sales_reference, shop_name, sales_customer_id, customer_name, round(sales_surcharge, 2) as sales_surcharge, 
             round(sales_total_amount, 2) as sales_total_amount, round(sales_product_discount, 2) as sales_product_discount, round(sales_discount, 2) as sales_discount,
             round(sales_shipping, 2) as sales_shipping, round(sales_grand_total, 2) as sales_grand_total, round(sales_paid_amount, 2) as sales_paid_amount, 
             round(sales_due, 2) as sales_due, sales_payment_status, upazila_name, district_name, 
             concat(emp_firstname, ' ', emp_lastname) as sold_by
            ",
            array (
                "left join {$table_prefix}customers on sales_customer_id = customer_id",
                "left join {$table_prefix}upazilas on customer_upazila = upazila_id",
                "left join {$table_prefix}districts on customer_district = district_id",
                "left join {$table_prefix}shops on shop_id = sales_shop_id",
                "left join {$table_prefix}users on user_id = sales_created_by",
                "left join {$table_prefix}employees on emp_id = user_emp_id"
            ),
            array (
              "sale.is_trash = 0 and sale.is_return = 0",
              " AND sales_reference LIKE" => $requestData["columns"][3]['search']['value'] . "%",
              " AND customer_name LIKE" => $requestData["columns"][4]['search']['value'] . "%",
              " AND sales_payment_status" => $requestData["columns"][13]['search']['value'],
              " AND (sales_delivery_date BETWEEN '{$dateRange[0]}' and '{$dateRange[1]}')",
              " AND sales_shop_id" => $requestData["columns"][2]['search']['value'],
              " AND sales_created_by" => $requestData["columns"][14]['search']['value']
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
          "sales as sale",
          "sales_id, sales_delivery_date, sales_note, sales_reference, shop_name, sales_customer_id, customer_name, round(sales_surcharge, 2) as sales_surcharge, 
          round(sales_total_amount, 2) as sales_total_amount, round(sales_product_discount, 2) as sales_product_discount, round(sales_discount, 2) as sales_discount, 
          round(sales_shipping, 2) as sales_shipping, round(sales_grand_total, 2) as sales_grand_total, round(sales_paid_amount, 2) as sales_paid_amount, 
          round(sales_due, 2) as sales_due, sales_payment_status, upazila_name, district_name,
          concat(emp_firstname, ' ', emp_lastname) as sold_by
          ",
          array (
            "left join {$table_prefix}customers on sales_customer_id = customer_id",
            "left join {$table_prefix}upazilas on customer_upazila = upazila_id",
            "left join {$table_prefix}districts on customer_district = district_id",
            "left join {$table_prefix}shops on shop_id = sales_shop_id",
            "left join {$table_prefix}users on user_id = sales_created_by",
            "left join {$table_prefix}employees on emp_id = user_emp_id"
          ),
          array("sale.is_trash = 0 and sale.is_return = 0"),
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
    if($getData) {
        
        foreach($getData['data'] as $key => $value) {

            $getSalesPaymentStatus = "";
            if($value["sales_payment_status"] === "paid") {
                $getSalesPaymentStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-success'>Paid</span>";
            } else if($value["sales_payment_status"] === "partial") {
                $getSalesPaymentStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-warning'>Partial</span>";
            } else {
                $getSalesPaymentStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-danger'>Due</span>";
            }

            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["sales_delivery_date"];
            $allNestedData[] = $value["shop_name"];
            $allNestedData[] = $value["sales_reference"];
            $allNestedData[] = "{$value['customer_name']}, {$value['upazila_name']}, {$value['district_name']}";
            $allNestedData[] = $value["sales_total_amount"];
            $allNestedData[] = $value["sales_product_discount"] + $value["sales_discount"];
            $allNestedData[] = $value["sales_shipping"];
            $allNestedData[] = $value["sales_grand_total"];
            $allNestedData[] = $value["sales_paid_amount"];
            $allNestedData[] = $value["sales_due"];
            $allNestedData[] = $value["sales_grand_total"] - $value["sales_due"];
            $allNestedData[] = $value["sales_note"];
            $allNestedData[] = $getSalesPaymentStatus;
            $allNestedData[] = $value["sold_by"];
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        <li><a target="_blank" href="'. full_website_address() .'/invoice-print/?invoiceType=posSaleView&id='. $value["sales_id"] .'"><i class="fa fa-edit"></i> View Purchase</a></li>
                                        <li><a target="_blank" href="'. full_website_address() .'/invoice-print/?autoPrint=true&invoiceType=posSaleView&id='. $value["sales_id"] .'"><i class="fa fa-print"></i>Print Invoice</a></li>
                                        <li><a data-toggle="modal" data-target="#modalDefault" href="'. full_website_address() .'/xhr/?module=my-shop&page=addPostSalesPayments&sales_id='. $value["sales_id"] .'&cid='. $value["sales_customer_id"] .'"><i class="fa fa-money"></i> Add Payment</a></li>
                                        <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=my-shop&page=deletePosSales" data-to-be-deleted="'. $value["sales_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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


/*************************** Pos Sale List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "salesProductReturnList") {

    if( !current_user_can("sale_return.View") ) {
        return _e("Sorry! you do not have permission to view sale return list");
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
        "",
        "sales_payment_status"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "sales",
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
            "sales as sale",
            "sales_id, sales_delivery_date, sales_note, sales_reference, sales_customer_id, customer_name, round(sales_total_amount, 2) as sales_total_amount, round(sales_product_discount, 2) as sales_product_discount, round(sales_discount, 2) as sales_discount, round(sales_shipping, 2) as sales_shipping, sales_surcharge, round(sales_grand_total, 2) as sales_grand_total, round(sales_paid_amount, 2) as sales_paid_amount, round(sales_due, 2) as sales_due, sales_payment_status, upazila_name, district_name",
            array (
              "left join {$table_prefix}customers on sales_customer_id = customer_id",
              "left join {$table_prefix}upazilas on customer_upazila = upazila_id",
              "left join {$table_prefix}districts on customer_district = district_id"
            ),
            array (
                "sale.is_trash = 0 and sale.is_return = 1",
                " AND customer_name LIKE" => $requestData['search']['value'] . "%",
                " OR sales_reference LIKE" => $requestData['search']['value'] . "%"
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
  
    } else if(
            !empty($requestData["columns"][1]['search']['value']) or 
            !empty($requestData["columns"][2]['search']['value']) or 
            !empty($requestData["columns"][3]['search']['value']) or 
            !empty($requestData["columns"][13]['search']['value'])
        ) { // Get data with search by column
        
        $dateRange[0] = "";
        $dateRange[1] = "";
        if(!empty($requestData["columns"][1]['search']['value'])) {
            $dateRange = explode(" - ", safe_input($requestData["columns"][1]['search']['value']));
        }

        $getData = easySelect(
            "sales as sale",
            "sales_id, sales_delivery_date, sales_note, sales_reference, sales_customer_id, customer_name, round(sales_total_amount, 2) as sales_total_amount, round(sales_product_discount, 2) as sales_product_discount, round(sales_discount, 2) as sales_discount, round(sales_shipping, 2) as sales_shipping, sales_surcharge, round(sales_grand_total, 2) as sales_grand_total, round(sales_paid_amount, 2) as sales_paid_amount, round(sales_due, 2) as sales_due, sales_payment_status, upazila_name, district_name",
            array (
                "left join {$table_prefix}customers on sales_customer_id = customer_id",
                "left join {$table_prefix}upazilas on customer_upazila = upazila_id",
                "left join {$table_prefix}districts on customer_district = district_id"
            ),
            array (
              "sale.is_trash = 0 and sale.is_return = 1",
              " AND sales_reference LIKE" => $requestData["columns"][2]['search']['value'] . "%",
              " AND customer_name LIKE" => $requestData["columns"][3]['search']['value'] . "%",
              " AND sales_payment_status" => $requestData["columns"][13]['search']['value'],
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

        $totalFilteredRecords = $getData ? $getData["count"] : 0;
        
    } else { // Get data withouth search
  
      $getData = easySelect(
          "sales as sale",
          "sales_id, sales_delivery_date, sales_note, sales_reference, sales_customer_id, customer_name, round(sales_total_amount, 2) as sales_total_amount, round(sales_product_discount, 2) as sales_product_discount, round(sales_discount, 2) as sales_discount, round(sales_shipping, 2) as sales_shipping, sales_surcharge, round(sales_grand_total, 2) as sales_grand_total, round(sales_paid_amount, 2) as sales_paid_amount, round(sales_due, 2) as sales_due, sales_payment_status, upazila_name, district_name",
          array (
            "left join {$table_prefix}customers on sales_customer_id = customer_id",
            "left join {$table_prefix}upazilas on customer_upazila = upazila_id",
            "left join {$table_prefix}districts on customer_district = district_id"
          ),
          array("sale.is_trash = 0 and sale.is_return = 1"),
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
    if($getData) {
        
        foreach($getData['data'] as $key => $value) {

            $getSalesPaymentStatus = "";
            if($value["sales_payment_status"] === "paid") {
                $getSalesPaymentStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-success'>Paid</span>";
            } else if($value["sales_payment_status"] === "partial") {
                $getSalesPaymentStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-warning'>Partial</span>";
            } else {
                $getSalesPaymentStatus = "<span style='padding: 2px 5px; display: block;' class='text-center btn-danger'>Due</span>";
            }

            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["sales_delivery_date"];
            $allNestedData[] = $value["sales_reference"];
            $allNestedData[] = "{$value['customer_name']}, {$value['upazila_name']}, {$value['district_name']}";
            $allNestedData[] = $value["sales_total_amount"];
            $allNestedData[] = $value["sales_product_discount"] + $value["sales_discount"];
            $allNestedData[] = $value["sales_shipping"];
            $allNestedData[] = $value["sales_surcharge"];
            $allNestedData[] = $value["sales_grand_total"];
            $allNestedData[] = $value["sales_paid_amount"];
            $allNestedData[] = $value["sales_due"];
            $allNestedData[] = $value["sales_grand_total"] - $value["sales_due"];
            $allNestedData[] = $value["sales_note"];
            $allNestedData[] = $getSalesPaymentStatus;
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a target="_blank" href="'. full_website_address() .'/invoice-print/?invoiceType=posSaleView&id='. $value["sales_id"] .'"><i class="fa fa-edit"></i> View Purchase</a></li>
                                    <li><a target="_blank" href="'. full_website_address() .'/invoice-print/?autoPrint=true&invoiceType=posSaleView&id='. $value["sales_id"] .'"><i class="fa fa-print"></i>Print Invoice</a></li>
                                    <li><a target="_blank" href="'. full_website_address() .'/sales/pos/?edit='. $value["sales_id"] .'"><i class="fa fa-edit"></i>Edit Sale</a></li>
                                    <li><a data-toggle="modal" data-target="#modalDefault" href="'. full_website_address() .'/xhr/?module=sales&page=addPostReturnPayments&sales_id='. $value["sales_id"] .'&cid='. $value["sales_customer_id"] .'"><i class="fa fa-money"></i> Return Payment</a></li>
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=my-shop&page=deletePosSales" data-to-be-deleted="'. $value["sales_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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


/************************** Shop POS Sales Add Payments **********************/
if(isset($_GET['page']) and $_GET['page'] == "addPostReturnPayments") {
  
    if( !current_user_can("sale_return.Add") ) {
        return _e("Sorry! you do not have permission to return payment");
    }

    // Include the modal header
    modal_header("Return Payments", full_website_address() . "/xhr/?module=sales&page=submitPostReturnPayments");

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
    modal_footer("Return Payments");

}


/************************** Shop POS Return Add Payments **********************/
if(isset($_GET['page']) and $_GET['page'] == "submitPostReturnPayments") {

    if( !current_user_can("sale_return.Add") ) {
        return _e("Sorry! you do not have permission to return payment");
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


    // Insert Return Payment
    $addPaymentReturn = easyInsert(
        "payments_return",
        array(
            "payments_return_type"          => "Outgoing",
            "payments_return_date"          => $_POST["salesReceivedPaymentDate"] . date(" H:i:s"),
            "payments_return_accounts"      => $_SESSION["aid"],
            "payments_return_customer_id"   => $_POST["addSalesPaymentsCustomerId"],
            "payment_return_method"         => $_POST["addSalesPaymentsMethod"],
            "payments_return_amount"        => $_POST["addSalesPaymentsAmount"],
            "payments_return_description"   => "Manual Payment Return for product return. " . $_POST["addSalesPaymentsDescription"],
            "payments_return_by"            => $_SESSION["uid"]
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

    if($addPaymentReturn === true) {
        
        // Update Customer Payment Info
        // updateCustomerPaymentInfo($_POST["addSalesPaymentsCustomerId"]);
        
        // Update Accounts Balance
        updateAccountBalance($_SESSION["aid"]);

        _s("Payment successfully added.");

    }

}
  


/*************************** Sales Return List ***********************/
  if(isset($_GET['page']) and $_GET['page'] == "salesProductReturnList_back") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "customer_name",
        "product_returns_date",
        "product_returns_reference",
        "product_returns_products_quantity",
        "product_returns_total_amount",
        "product_returns_total_discount",
        "product_returns_surcharge",
        "product_returns_grand_total"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "product_returns",
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
            "product_returns as product_return",
            "product_returns_id, product_returns_date, product_returns_reference, customer_name, product_returns_total_amount, product_returns_items_discount, product_returns_total_discount, product_returns_surcharge, product_returns_grand_total",
            array (
                "inner join {$table_prefix}customers on product_returns_customer_id = customer_id"
            ),
            array (
                "product_return.is_trash = 0 and customer_name LIKE" => $requestData["search"]["value"]."%"
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
          "product_returns as product_return",
          "product_returns_id, product_returns_date, product_returns_reference, customer_name, product_returns_total_amount, product_returns_items_discount, product_returns_total_discount, product_returns_surcharge, product_returns_grand_total",
          array (
            "inner join {$table_prefix}customers on product_returns_customer_id = customer_id"
          ),
          array("product_return.is_trash = 0"),
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
            $allNestedData[] = $value["customer_name"];
            $allNestedData[] = $value["product_returns_date"];
            $allNestedData[] = $value["product_returns_reference"];
            $allNestedData[] = $value["product_returns_total_amount"];
            $allNestedData[] = $value["product_returns_items_discount"] + $value["product_returns_total_discount"];
            $allNestedData[] = $value["product_returns_surcharge"];
            $allNestedData[] = $value["product_returns_grand_total"];
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a target="_blank" href="'. full_website_address() .'/invoice-print/?invoiceType=produtReturn&id='. $value["product_returns_id"] .'"><i class="fa fa-edit"></i> View Return</a></li>
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=stock-management&page=deleteReturn" data-to-be-deleted="'. $value["product_returns_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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


/*************************** wastageSaleList ***********************/
if(isset($_GET['page']) and $_GET['page'] == "wastageSaleList") {

    if( !current_user_can("wastage_sales.View") ) {
        return _e("Sorry! you do not have permission to view wastage sale");
    }
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "wastage_sale_add_on",
        "wastage_sale_date",
        "wastage_sale_id",
        "customer_name",
        "wastage_sale_grand_total",
        "wastage_sale_paid_amount",
        "wastage_sale_due_amount",
        "",
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "wastage_sale",
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
            "wastage_sale as wastage_sale",
            "wastage_sale_id, wastage_sale_date, wastage_sale_reference, customer_name, wastage_sale_grand_total, wastage_sale_paid_amount, 
            wastage_sale_due_amount, wastage_sale_note, wastage_sale_attachment, wastage_sale_add_on",
            array (
              "inner join {$table_prefix}customers on wastage_sale_customer = customer_id"
            ),
            array (
                "wastage_sale.is_trash = 0 and customer_name LIKE" => $requestData["search"]["value"]."%"
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
            "wastage_sale as wastage_sale",
            "wastage_sale_id, wastage_sale_date, wastage_sale_reference, customer_name, wastage_sale_grand_total, wastage_sale_paid_amount, 
            wastage_sale_due_amount, wastage_sale_note, wastage_sale_attachment, wastage_sale_add_on",
            array (
                "inner join {$table_prefix}customers on wastage_sale_customer = customer_id"
            ),
            array("wastage_sale.is_trash = 0"),
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
            $allNestedData[] = $value["wastage_sale_add_on"];
            $allNestedData[] = $value["wastage_sale_date"];
            $allNestedData[] = "Sale/Wastage/{$value['wastage_sale_id']} ({$value['wastage_sale_reference']})";
            $allNestedData[] = $value["customer_name"];
            $allNestedData[] = $value["wastage_sale_grand_total"];
            $allNestedData[] = $value["wastage_sale_paid_amount"];
            $allNestedData[] = $value["wastage_sale_due_amount"];
            $allNestedData[] = $value["wastage_sale_note"];
            $allNestedData[] = empty($value["wastage_sale_attachment"]) ? "" : "<a target='_blank' class='btn btn-xs btn-info' href='". full_website_address() ."/assets/upload/{$value["wastage_sale_attachment"]}'>Download</a>";
           
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a class="'. ( current_user_can("wastage_sales.Delete") ? "" : "restricted " ) .'deleteEntry" href="'. full_website_address() . '/xhr/?module=sales&page=deleteWastageSale" data-to-be-deleted="'. $value["wastage_sale_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                    <li><a data-toggle="modal" data-target="#modalDefault" href="'. full_website_address() . '/xhr/?module=sales&page=viewWastageSale&id='. $value["wastage_sale_id"] .'"><i class="fa fa-eye"></i> View</a></li>

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


/***************** Delete Accounts ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteWastageSale") {

    if(current_user_can("wastage_sales.Delete") !== true) {
        echo '{
            "title": "Sorry!",
            "text": "'. __("you do not have permission to delete wastage sale.") .'",
            "showConfirmButton": true,
            "showCloseButton": true,
            "toast": false,
            "icon": "error"
        }';
        return;
    }

    $deleteData = easyDelete(
        "wastage_sale",
        array(
            "wastage_sale_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {
        echo '{
            "title": "'. __("The wastage sale has been deleted successfully.") .'"
        }';
    } 
}

/************************** View Wastage Sale **********************/
if(isset($_GET['page']) and $_GET['page'] == "viewWastageSale") {

    if( !current_user_can("wastage_sales.View") ) {
        return _e("Sorry! you do not have permission to view wastage sale");
    }
  
    // Select Wastage sales
    $selectWastageSale = easySelect(
        "wastage_sale",
        "*",
        array (
            "left join {$table_prefix}customers on wastage_sale_customer = customer_id"
        ),
        array (
            "wastage_sale_id"  => $_GET["id"]
        )
    );

    // Select Sales item
    $selectWastageSalesItems = easySelect(
        "wastage_sale_items",
        "*",
        array(),
        array (
            "wastage_sale_id" => $_GET["id"]
        )
    );

    $wastageSales = $selectWastageSale["data"][0];

    ?>

    <div class="modal-header">
        <h4 class="modal-title">Wastage Sale Items</h4>
    </div>

    <div class="modal-body">

        <table> 
            <tbody>
                <tr>
                    <td style="padding: 0" class="col-md-3"><?= __("Reference No:"); ?> <?php echo "Sale/Wastage/" . $selectWastageSale["data"][0]["wastage_sale_id"] ?></td>
                    <td style="padding: 0" class="col-md-3 text-right"><?= __("Date:"); ?> <?php echo date("d/m/Y", strtotime($selectWastageSale["data"][0]["wastage_sale_date"])) ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong><?= __("Customer:"); ?>  <?php echo $selectWastageSale["data"][0]["customer_name"] ?></strong></td>
                </tr>
            </tbody>
        </table>
        
        <br/>

        <table class="table table-striped table-condensed">
            <tbody>
            <tr>
                <td>সংখ্যা</td>
                <td>বিবরণ</td>
                <td>মূল্য</td>
                <td class="text-right">মোট টাকা</td>
            </tr>

            <?php 

                foreach($selectWastageSalesItems["data"] as $key => $wSaleItems) {
       
                    echo "<tr>";
                    echo " <td>{$wSaleItems['wastage_sale_items_qnt']}</td>";
                    echo " <td>{$wSaleItems['wastage_sale_items_details']}</td>";
                    echo " <td>" . number_format($wSaleItems['wastage_sale_items_price'],2) . "</td>";
                    echo " <td class='text-right'>" . number_format($wSaleItems['wastage_sale_items_subtotal'],2) . "</td>";
                    echo "</tr>";

                }

            ?>     

            </tbody>

            <tfoot>  
                <tr>
                    <th colspan="3" class="text-right">Total:</th>
                    <th class="text-right"><?php echo number_format(($wastageSales["wastage_sale_grand_total"]), 2) ?></th>
                </tr>
            </tfoot>

        </table>
        
        <br/>

    </div> <!-- /.modal-body -->

    <?php
  
}


/*************************** Sales Discounts List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "discountsList") {

    if( !current_user_can("sales_discount.View") ) {
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
 
    if(!empty($requestData["search"]["value"]) OR
        !empty($requestData["columns"][2]['search']['value']) or 
        !empty($requestData["columns"][3]['search']['value']) 
    
    ) {  // get data with search
      
        $getData = easySelect(
            "received_payments as received_payment",
            "received_payments_id, customer_name, received_payments_shop, received_payments_from, round(received_payments_amount, 2) as received_payments_amount, received_payments_details, received_payments_datetime, shop_name",
            array (
                "left join {$table_prefix}customers on customer_id = received_payments_from",
                "left join {$table_prefix}shops on shop_id = received_payments_shop"
            ),
            array (
                "received_payment.is_trash = 0 and received_payments_type" => "Discounts",
                " AND received_payments_shop " => $requestData["columns"][2]['search']['value'],
                " AND customer_name LIKE " => "%{$requestData["columns"][3]['search']['value']}%"
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
            "received_payments_id, customer_name, received_payments_shop, received_payments_from, round(received_payments_amount, 2) as received_payments_amount, received_payments_details, received_payments_datetime, shop_name",
            array (
                "left join {$table_prefix}customers on customer_id = received_payments_from",
                "left join {$table_prefix}shops on shop_id = received_payments_shop"
            ),
            array (
                "received_payment.is_trash = 0 and received_payments_type" => "Discounts",
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
            $allNestedData[] = $value["shop_name"];
            $allNestedData[] = $value["customer_name"];
            $allNestedData[] = $value["received_payments_amount"];
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


?>