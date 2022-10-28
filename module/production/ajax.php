<?php

// Order List
if(isset($_GET['page']) and $_GET['page'] == "orderList") {
    
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
            "sales_id, sales_order_date, sales_delivery_date, sales_status, sales_shop_id, sales_reference, sales_customer_id, customer_name, sales_total_amount, sales_product_discount, sales_discount, sales_change, sales_shipping, sales_grand_total, sales_paid_amount, sales_due, sales_payment_status, upazila_name, district_name",
            array (
                "left join {$table_prefeix}customers on customer_id = sales_customer_id",
                "left join {$table_prefeix}upazilas on upazila_id = customer_upazila",
                "left join {$table_prefeix}districts on district_id = customer_district"
            ),
            array (
                "sales.is_trash = 0 and sales.is_return = 0 and sales.sales_order_date is not null and sales_shop_id" => $_SESSION["sid"],
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
  
    } else if(!empty($requestData["columns"][1]['search']['value']) or !empty($requestData["columns"][2]['search']['value']) or !empty($requestData["columns"][3]['search']['value']) or !empty($requestData["columns"][11]['search']['value'])) { // Get data with search by column
        
        $dateRange[0] = "";
        $dateRange[1] = "";
        if(!empty($requestData["columns"][1]['search']['value'])) {
            $dateRange = explode(" - ", safe_input($requestData["columns"][1]['search']['value']));
        }
        
        $getData = easySelect(
            "sales as sales",
            "sales_id, sales_order_date, sales_delivery_date, sales_status, sales_shop_id, sales_reference, sales_customer_id, customer_name, sales_total_amount, sales_product_discount, sales_discount, sales_change, sales_shipping, sales_grand_total, sales_paid_amount, sales_due, sales_payment_status, upazila_name, district_name",
            array (
                "left join {$table_prefeix}customers on customer_id = sales_customer_id",
                "left join {$table_prefeix}upazilas on upazila_id = customer_upazila",
                "left join {$table_prefeix}districts on district_id = customer_district"
            ),
            array (
              "sales.is_trash = 0 and sales.is_return = 0 and sales.sales_order_date is not null and sales_shop_id" => $_SESSION["sid"],
              " AND sales_reference LIKE" => "%" . $requestData["columns"][2]['search']['value'] . "%",
              " AND customer_name LIKE" => "%" . $requestData["columns"][3]['search']['value'] . "%",
              " AND sales_payment_status" => $requestData["columns"][11]['search']['value'],
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
  
        
    } else { // Get data withouth search
  
      $getData = easySelect(
          "sales as sales",
          "sales_id, sales_order_date, sales_delivery_date, sales_status, sales_shop_id, sales_reference, sales_customer_id, customer_name, sales_total_amount, sales_product_discount, sales_discount, sales_change, sales_shipping, sales_grand_total, sales_paid_amount, sales_due, sales_payment_status, upazila_name, district_name",
          array (
            "left join {$table_prefeix}customers on customer_id = sales_customer_id",
            "left join {$table_prefeix}upazilas on upazila_id = customer_upazila",
            "left join {$table_prefeix}districts on district_id = customer_district"
          ),
          array (
            "sales.is_trash = 0 and sales.is_return = 0 and sales.sales_order_date is not null and sales_shop_id" => $_SESSION["sid"],
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
            $allNestedData[] = $value["sales_order_date"];
            $allNestedData[] = "<iledit>{$value["sales_delivery_date"]}</iledit>";
            $allNestedData[] = "<a data-toggle='modal' data-target='#modalDefault' href='" . full_website_address() . "/xhr/?module=reports&page=showInvoiceProducts&id={$value['sales_id']}'>{$value['sales_reference']}</a>";
            $allNestedData[] = "<iledit data-val='{$value["sales_customer_id"]}'>{$value['customer_name']}, {$value['upazila_name']}, {$value['district_name']}</iledit>";
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
                                        <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=my-shop&page=deletePosSales" data-to-be-deleted="'. $value["sales_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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


/** Product Requrements */
if(isset($_GET['page']) and $_GET['page'] == "productRequirments") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        ""
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
      
        $getData = easySelectA(array(
            "table"     => "product_stock as product_stock",
            "fields"    => "stock_product_id, product_parent_id, product_name, product_unit, equal_unit_id, equal_unit_qnt, base_qty, sum(stock_item_qty*base_qty) as ordered_base_qty, if(stock_in is null, 0, stock_in) as stock_qty",
            "join"      => array(
                "left join {$table_prefeix}products on product_id = stock_product_id",
                "left join {$table_prefeix}product_units on unit_name = product_unit",
                "left join (select
                                vp_id,
                                base_qty,
                                sum(base_stock_in/base_qty) as stock_in
                            from product_base_stock
                            group by vp_id
                ) as pbs on pbs.vp_id = stock_product_id",
            ),
            "where"     => array(
                "product_stock.is_trash = 0 and is_bundle_item = 0 and stock_type = 'sale-order' and product_name LIKE" => $requestData['search']['value'] . "%"
            ),
            "groupby"   => "stock_product_id"
        ));
        
  
        $totalFilteredRecords = $getData ? $getData["count"] : 0;
  
    } else { // Get data withouth search

        
        $getData = easySelectA(array(
            "table"     => "product_stock as product_stock",
            "fields"    => "stock_product_id, product_parent_id, product_name, product_unit, equal_unit_id, equal_unit_qnt, base_qty, sum(stock_item_qty*base_qty) as ordered_base_qty, if(stock_in is null, 0, stock_in) as stock_qty",
            "join"      => array(
                "left join {$table_prefeix}products on product_id = stock_product_id",
                "left join {$table_prefeix}product_units on unit_name = product_unit",
                "left join (select
                                vp_id,
                                base_qty,
                                sum(base_stock_in/base_qty) as stock_in
                            from product_base_stock
                            group by vp_id
                ) as pbs on pbs.vp_id = stock_product_id",
            ),
            "where"     => array(
                "product_stock.is_trash = 0 and is_bundle_item = 0 and stock_type = 'sale-order'"
            ),
            "groupby"   => "stock_product_id"
        ));

  
    }

  
    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['count']) and $getData['count'] > 0) {

        $product_calculat = [];

        foreach($getData["data"] as $key => $val) {


            if( isset($product_calculat[$val["product_name"]]) ) {

                $product_calculat[$val["product_name"]]["total_ordered_qty"] += $val["ordered_base_qty"];

                if( $val["base_qty"] > $product_calculat[$val["product_name"]]["base_qty"] ) {

                    $product_calculat[$val["product_name"]]["base_qty"] = $val["base_qty"];
                    $product_calculat[$val["product_name"]]["product_unit"] = $val["product_unit"];
                    $product_calculat[$val["product_name"]]["stock_qty"] = $val["stock_qty"];
                    $product_calculat[$val["product_name"]]["product_id"] = $val["stock_product_id"];

                }


            } else {

                $product_calculat[$val["product_name"]]["total_ordered_qty"] = $val["ordered_base_qty"];
                $product_calculat[$val["product_name"]]["base_qty"] = $val["base_qty"];
                $product_calculat[$val["product_name"]]["product_unit"] = $val["product_unit"];
                $product_calculat[$val["product_name"]]["stock_qty"] = $val["stock_qty"];
                $product_calculat[$val["product_name"]]["product_id"] = $val["stock_product_id"];

            }
            
        }
        
        foreach($product_calculat as $key => $value) {
            
            $orderedQty = $value["total_ordered_qty"] / $value["base_qty"];
            $requireProduct = $orderedQty - $value["stock_qty"];

            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $key;
            $allNestedData[] = near_unit_qty($value["product_id"], $orderedQty, $value["product_unit"]);
            $allNestedData[] = near_unit_qty($value["product_id"], $value["stock_qty"], $value["product_unit"]);
            $allNestedData[] = near_unit_qty($value["product_id"], $requireProduct, $value["product_unit"]);
            
            
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



/**  rawMaterialRequirments */
if(isset($_GET['page']) and $_GET['page'] == "rawMaterialRequirments") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        ""
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
      
        $getData = easySelectA(array(
            "table"     => "product_stock as product_stock",
            "fields"    => "stock_product_id, product_parent_id, product_name, product_unit, equal_unit_id, equal_unit_qnt, base_qty, sum(stock_item_qty*base_qty) as ordered_base_qty, if(stock_in is null, 0, stock_in) as stock_qty",
            "join"      => array(
                "left join {$table_prefeix}products on product_id = stock_product_id",
                "left join {$table_prefeix}product_units on unit_name = product_unit",
                "left join (select
                                vp_id,
                                base_qty,
                                sum(base_stock_in/base_qty) as stock_in
                            from product_base_stock
                            group by vp_id
                ) as pbs on pbs.vp_id = stock_product_id",
            ),
            "where"     => array(
                "product_stock.is_trash = 0 and is_bundle_item = 1 and stock_type = 'sale-order' and product_name LIKE" => $requestData['search']['value'] . "%"
            ),
            "groupby"   => "stock_product_id"
        ));
        
  
        $totalFilteredRecords = $getData ? $getData["count"] : 0;
  
    } else { // Get data withouth search

        
        $getData = easySelectA(array(
            "table"     => "product_stock as product_stock",
            "fields"    => "stock_product_id, product_parent_id, product_name, product_unit, equal_unit_id, equal_unit_qnt, base_qty, sum(stock_item_qty*base_qty) as ordered_base_qty, if(stock_in is null, 0, stock_in) as stock_qty",
            "join"      => array(
                "left join {$table_prefeix}products on product_id = stock_product_id",
                "left join {$table_prefeix}product_units on unit_name = product_unit",
                "left join (select
                                vp_id,
                                base_qty,
                                sum(base_stock_in/base_qty) as stock_in
                            from product_base_stock
                            group by vp_id
                ) as pbs on pbs.vp_id = stock_product_id",
            ),
            "where"     => array(
                "product_stock.is_trash = 0 and is_bundle_item = 1 and stock_type = 'sale-order'"
            ),
            "groupby"   => "stock_product_id"
        ));

  
    }

  
    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['count']) and $getData['count'] > 0) {

        $product_calculat = [];

        foreach($getData["data"] as $key => $val) {


            if( isset($product_calculat[$val["product_name"]]) ) {

                $product_calculat[$val["product_name"]]["total_ordered_qty"] += $val["ordered_base_qty"];

                if( $val["base_qty"] > $product_calculat[$val["product_name"]]["base_qty"] ) {

                    $product_calculat[$val["product_name"]]["base_qty"] = $val["base_qty"];
                    $product_calculat[$val["product_name"]]["product_unit"] = $val["product_unit"];
                    $product_calculat[$val["product_name"]]["stock_qty"] = $val["stock_qty"];
                    $product_calculat[$val["product_name"]]["product_id"] = $val["stock_product_id"];

                }


            } else {

                $product_calculat[$val["product_name"]]["total_ordered_qty"] = $val["ordered_base_qty"];
                $product_calculat[$val["product_name"]]["base_qty"] = $val["base_qty"];
                $product_calculat[$val["product_name"]]["product_unit"] = $val["product_unit"];
                $product_calculat[$val["product_name"]]["stock_qty"] = $val["stock_qty"];
                $product_calculat[$val["product_name"]]["product_id"] = $val["stock_product_id"];

            }
            
        }
        
        foreach($product_calculat as $key => $value) {
            
            $orderedQty = $value["total_ordered_qty"] / $value["base_qty"];
            $needToBy = $orderedQty - $value["stock_qty"];

            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $key;
            $allNestedData[] = near_unit_qty($value["product_id"], $orderedQty, $value["product_unit"]);
            $allNestedData[] = near_unit_qty($value["product_id"], $value["stock_qty"], $value["product_unit"]);
            $allNestedData[] = near_unit_qty($value["product_id"], $needToBy, $value["product_unit"]);
            
            
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