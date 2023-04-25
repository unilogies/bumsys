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
            "is_trash = 0 and sales_order_date is not null and sales_shop_id" => $_SESSION["sid"]
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
                "left join {$table_prefix}customers on customer_id = sales_customer_id",
                "left join {$table_prefix}upazilas on upazila_id = customer_upazila",
                "left join {$table_prefix}districts on district_id = customer_district"
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
                "left join {$table_prefix}customers on customer_id = sales_customer_id",
                "left join {$table_prefix}upazilas on upazila_id = customer_upazila",
                "left join {$table_prefix}districts on district_id = customer_district"
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
            "left join {$table_prefix}customers on customer_id = sales_customer_id",
            "left join {$table_prefix}upazilas on upazila_id = customer_upazila",
            "left join {$table_prefix}districts on district_id = customer_district"
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
        "table" => "product_stock as product_stock",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0 and product_stock.is_trash = 0 and is_bundle_item = 0 and stock_type = 'sale-order'"
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
                "left join {$table_prefix}products on product_id = stock_product_id",
                "left join {$table_prefix}product_units on unit_name = product_unit",
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
                "left join {$table_prefix}products on product_id = stock_product_id",
                "left join {$table_prefix}product_units on unit_name = product_unit",
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
            $requireProduct =  $value["stock_qty"] > $orderedQty ? 0 : $orderedQty - $value["stock_qty"];

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
        "table" => "product_stock as product_stock",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "product_stock.is_trash = 0 and is_bundle_item = 1 and stock_type = 'sale-order'"
        ),
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if(!empty($requestData["search"]["value"])) {  // get data with search
      
        $getData = easySelectA(array(
            "table"     => "product_stock as product_stock",
            "fields"    => "stock_product_id, product_parent_id, product_name, product_unit, equal_unit_id, equal_unit_qnt, base_qty, sum(stock_item_qty*base_qty) as ordered_base_qty, if(stock_in is null, 0, stock_in) as stock_qty",
            "join"      => array(
                "left join {$table_prefix}products on product_id = stock_product_id",
                "left join {$table_prefix}product_units on unit_name = product_unit",
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
            "fields"    => "bg_item_product_id, product_parent_id, product_name, product_unit, equal_unit_id, equal_unit_qnt, 
                            bg_product_qnt,
                            if(base_qty is null, 1, base_qty) as base_qty, 
                            sum(if(base_qty is null, stock_item_qty * bg_product_qnt * 1, stock_item_qty * bg_product_qnt * base_qty)) as ordered_base_qty, 
                            if(stock_in is null, 0, stock_in) as stock_qty",
            "join"      => array(
                "left join {$table_prefix}bg_product_items on bg_product_id = stock_product_id",
                "left join {$table_prefix}products on product_id = bg_item_product_id",
                "left join {$table_prefix}product_units on unit_name = product_unit",
                "left join (select
                                vp_id,
                                base_qty,
                                sum(base_stock_in/base_qty) as stock_in
                            from product_base_stock
                            group by vp_id
                ) as pbs on pbs.vp_id = bg_item_product_id",
            ),
            "where"     => array(
                "product_stock.is_trash = 0 and is_raw_materials = 1 and stock_type = 'sale-order'"
            ),
            "groupby"   => "bg_item_product_id"
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
                    $product_calculat[$val["product_name"]]["product_id"] = $val["bg_item_product_id"];

                }


            } else {

                $product_calculat[$val["product_name"]]["total_ordered_qty"] = $val["ordered_base_qty"];
                $product_calculat[$val["product_name"]]["base_qty"] = $val["base_qty"];
                $product_calculat[$val["product_name"]]["product_unit"] = $val["product_unit"];
                $product_calculat[$val["product_name"]]["stock_qty"] = $val["stock_qty"];
                $product_calculat[$val["product_name"]]["product_id"] = $val["bg_item_product_id"];

            }
            
        }
        
        
        foreach($product_calculat as $key => $value) {
            
            $orderedQty = $value["total_ordered_qty"] / $value["base_qty"];
            $needToBy =  $orderedQty < $value["stock_qty"] ? 0 : $orderedQty - $value["stock_qty"];

            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["product_id"] . $key;
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



/*************************** Product List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "rmaProductList") {
    
    $requestData = $_REQUEST;
  
    $getData = [];
  
    // List of all columns name
    $columns = array(
        "",
        "product_id",
        "product_name",
        "product_group",
        "product_generic",
        "product_edition",
        "category_name",
        "product_weight",
        "product_sale_price"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
          "table" => "products",
          "fields" => "count(*) as totalRow",
          "where" => array(
              "is_trash = 0 and product_type != 'Child'"
          )
      ))["data"][0]["totalRow"];
  
    if($requestData['length'] == -1) {
      $requestData['length'] = $totalRecords;
    }
  
    if(!empty($requestData["search"]["value"]) or !empty($requestData["columns"][5]['search']['value']) or !empty($requestData["columns"][6]['search']['value'])) {  // get data with search
  
          // If there are any edition to filter, we do not need product_type != 'Child' filter
          $productEditionFilter = !empty($requestData["columns"][5]['search']['value']) ? " AND product_edition = '{$requestData["columns"][5]['search']['value']}' " : "";
        
          $getData = easySelect(
              "products as product",
              "product_id, product_code, product_name, product_type, product_group, product_generic, product_description, round(product_purchase_price, 2) as product_purchase_price, 
              round(product_sale_price, 2) as product_sale_price, category_name, product_edition, has_sub_product",
              array (
              "left join {$table_prefix}product_category on product_category_id = category_id"
              ),
              array (
                  "product.is_trash = 0 {$productEditionFilter} and (product_code LIKE '". safe_input($requestData['search']['value']) ."%' ",
                  " OR product_name LIKE" => $requestData['search']['value'] . "%",
                  " OR category_name LIKE" => $requestData['search']['value'] . "%",
                  ")",
                  " AND product_category_id" => $requestData["columns"][6]['search']['value'],
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
              "products as product",
              "product_id, product_code, product_name, product_group, product_generic, product_type, product_description, round(product_purchase_price, 2) as product_purchase_price, 
              round(product_sale_price, 2) as product_sale_price, category_name, product_edition, has_sub_product",
              array (
                  "left join {$table_prefix}product_category on product_category_id = category_id"
              ),
              array("product.is_trash = 0"),
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
  
              $subProductAttachment = "";
              if( $value["has_sub_product"] == 1 ) {
                  $subProductAttachment = '<li></li>';
              }
  
            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["product_code"];
            $allNestedData[] = $value["product_name"];
            $allNestedData[] = $value["product_group"];
            $allNestedData[] = $value["product_generic"];
            $allNestedData[] = $value["product_edition"];
            $allNestedData[] = $value["category_name"];
            $allNestedData[] = "";
            $allNestedData[] = $value["product_purchase_price"] . " / " . $value["product_sale_price"];
            // The action button
            $allNestedData[] = '<a class="btn btn-default" href="'. full_website_address() .'/production/link-raw-materials/?pid='. $value["product_id"] .'"><i class="fa fa-plus"></i> Link Raw Materials</a>';
            
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




/******************** Raw materials attachment *******************/
if(isset($_GET['page']) and $_GET['page'] == "linkRawMaterials") {


    $selectProduct = easySelectA(array(
        "table"     => "products",
        "fields"    => "product_id, product_type, product_name, has_sub_product, product_unit",
        "where"     => array(
            "product_id"    => $_POST["mainProduct"]
        )
    ));

    if($selectProduct === false ) {

        echo _e("Sorry! No product found to link raw materails.");
        
    } else {

        // Check, are there any bundle product or product which have sub product
        $allSubProductId = implode(",", $_POST["bgProductID"]);

        $checkSubProduct = easySelectA(array(
            "table"     => "products",
            "fields"    => "product_name",
            "where"     => array(
                "product_id in($allSubProductId) and (has_sub_product = 1 or product_type = 'Bundle') "
            )
        ));

        // If there are any sub product then return error
        if($checkSubProduct !== false) {

            _e("Sorry! <b>{$checkSubProduct['data'][0]['product_name']}</b> is a bundle or has sub product. The product, which is bundle or have sub product can not be attached or linked.");

        } else {


            // Start the mysql Transaction
            runQuery("START TRANSACTION;");


            $product = $selectProduct["data"][0];


            // Insert Bundle/ Sub product
            $insertSubProduct = "INSERT INTO {$table_prefix}bg_product_items(
                bg_product_id,
                bg_item_product_id,
                bg_product_price,
                bg_product_qnt,
                is_raw_materials
            ) VALUES";

            
            // If the main product is variable
            if( $product["product_type"] === "Variable" ) {

                /**
                 * If the main product is variable
                 * Then add sub product in all child product
                 */


                // Select all Child product of this variable product
                $childProducts = easySelectA(array(
                    "table" => "products",
                    "where" => array(
                        "is_trash = 0 and product_type = 'Child' and parent_product_id" => $_POST["mainProduct"]
                    )
                ));


                // If there is any child product in the variable product then add raw materials on child products
                if( $childProducts !== false ) {

                    foreach($childProducts["data"] as $childProduct) {

                        // Delete Privous bg product
                        easyPermDelete(
                            "bg_product_items",
                            array(
                                "bg_product_id" => $childProduct["product_id"]
                            )
                        );

                        
                        foreach($_POST["bgProductID"] as $pkey => $bgProductId) {

                            // If there have any unit in this child product
                            // Then multiply the bgProductQnt with unit base qty
                            if( !empty($childProduct['product_unit']) ) {

                                $bgProductQty = "(select base_qnt * ". $_POST["bgProductQnt"][$pkey] ." from {$table_prefix}product_units where unit_name = '{$childProduct['product_unit']}' )";

                            } else {
                                
                                $bgProductQty = safe_input($_POST["bgProductQnt"][$pkey]);

                            }

                            $insertSubProduct .= "(
                                '{$childProduct['product_id']}',
                                '{$bgProductId}',
                                '". safe_input($_POST["bgProductSalePrice"][$pkey]) ."',
                                '{$bgProductQty}',
                                '1'
                            ),";
                            

                        }

                    }

                }

            }


            /**
             * Either The main product is variable or normal or child
             * Add sub product on it along with child product
             */

            // Delete Privous bg product
            easyPermDelete(
                "bg_product_items",
                array(
                    "bg_product_id" => $_POST["mainProduct"]
                )
            );

            
            foreach($_POST["bgProductID"] as $pkey => $bgProductId) {

                // If there have any unit in this product
                // Then multiply the bgProductQnt with unit base qty
                if( !empty($product['product_unit']) ) {

                    $bgProductQty = "(select base_qnt * ". $_POST["bgProductQnt"][$pkey] ." from {$table_prefix}product_units where unit_name = '{$product['product_unit']}' )";

                } else {
                    
                    $bgProductQty = safe_input($_POST["bgProductQnt"][$pkey]);

                }

                $insertSubProduct .= "(
                    '{$product['product_id']}',
                    '{$bgProductId}',
                    '". safe_input($_POST["bgProductSalePrice"][$pkey]) ."',
                    '{$bgProductQty}',
                    '1'
                ),";
                

            }

            


            // Run query to insert sub products
            runQuery(substr_replace($insertSubProduct, ";", -1, 1));


            // Check if there is any error on inserting data
            if( !empty($get_all_db_error)  ) {
    
                _e( $get_all_db_error[0]. " Please check the error log for more information.");
    
                // If there are any error then rollback/undo the data
                runQuery("ROLLBACK;");
            
            } else {
                
                // If there have not any error then commit/save the data permanently
                runQuery("COMMIT;");
                _s("Successfully updatted");
    
            }
            

        }


    }


}



?>