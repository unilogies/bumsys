<?php


/*************************** Product Reports ***********************/
if(isset($_GET['page']) and $_GET['page'] == "productReports") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "pid",
        "brand_name",
        "category_name",
        "product_year",
        "",
        "",
        "",
        "",
        "",
        "",
        "sale_qty",
        "sale_qty_in_range",
        "",
        "",
        "",
        "",
        "stock_qty"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
    "table" => "products",
    "fields" => "count(*) as totalRow",
    "where" => array(
      "is_trash = 0"
    )
  ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }

    $soldDateRange[0] = "";
    $soldDateRange[1] = "";
    if( !empty($requestData["columns"][12]['search']['value']) ) {
        
        $soldDateRange = explode(" - ", safe_input($requestData["columns"][12]['search']['value']));

    }
 
    if(!empty($requestData["search"]["value"]) or !empty($requestData["columns"][1]['search']['value']) or !empty($requestData["columns"][2]['search']['value']) or !empty($requestData["columns"][3]['search']['value']) or !empty($requestData["columns"][4]['search']['value']) ) {  // get data with search
        
        $edition_filter = empty($requestData["columns"][4]['search']['value']) ? "AND product.product_type != 'Child'" : "AND product_edition = {$requestData["columns"][4]['search']['value']} ";

        $warehouse_filter = empty($requestData["columns"][1]['search']['value']) ? "" : " = " . $requestData["columns"][1]['search']['value'];

        
        $getData = easySelectA(array(
            "table"     => "products as product",
            "fields"    => "
                            product.product_id as pid, product_type, concat(product_name, ' ', if(product_group is null, '', product_group) ) as product_name, 
                            brand_name, product_purchase_price, product_sale_price, product_edition, product_unit, product_category_id, category_name,
                            if(initial_qty is null, 0, round(initial_qty, 2) ) as initial_qty,
                            if(production_qty is null, 0, round(production_qty, 2)) as production_qty,
                            if(sale_qty is null, 0, round(sale_qty, 2) ) as sale_qty,
                            if(wastage_sale_qty is null, 0, round(wastage_sale_qty, 2) ) as wastage_sale_qty,
                            if(sale_return_qty is null, 0, round(sale_return_qty, 2) ) as sale_return_qty,
                            if(purchase_qty is null, 0, round(purchase_qty, 2) ) as purchase_qty,
                            if(purchase_order_qty is null, 0, round(purchase_order_qty, 2) ) as purchase_order_qty,
                            if(purchase_return_qty is null, 0, round(purchase_return_qty, 2) ) as purchase_return_qty,
                            if(transfer_in_qty is null, 0, round(transfer_in_qty, 2) ) as transfer_in_qty,
                            if(transfer_out_qty is null, 0, round(transfer_out_qty, 2) ) as transfer_out_qty,
                            if(specimen_copy_qty is null, 0, round(specimen_copy_qty, 2) ) as specimen_copy_qty,
                            if(specimen_copy_return_qty is null, 0, round(specimen_copy_return_qty, 2) ) as specimen_copy_return_qty,
                            if(expired_qty is null, 0, round(expired_qty, 2) ) as expired_qty,
                            if(stock_qty is null, 0, round(stock_qty, 2) ) as stock_qty,
                            if(sale_item_subtotal is null, 0, round(sale_item_subtotal, 2)) as total_sold_amount,
                            if(purchase_item_subtotal is null, 0, round(purchase_item_subtotal, 2)) as total_purchased_amount,
                            if(sale_qty_in_range is null, 0, round(sale_qty_in_range,2 )) as sale_qty_in_range,
                            child_product as child_product_list
            ",
            "join"      => array(
                "left join (
                    select
                        stock_product_id,
                        sum(case when stock_type = 'initial' then stock_item_qty end) as initial_qty,
                        sum(case when stock_type = 'sale-production' then stock_item_qty end) as production_qty,
                        sum(case when stock_type = 'sale' then stock_item_qty end) as sale_qty,
                        sum(case when stock_type = 'sale' and stock_entry_date between '{$soldDateRange[0]}' and '{$soldDateRange[1]}' then stock_item_qty end) as sale_qty_in_range,
                        sum(case when stock_type = 'sale' then stock_item_subtotal end) as sale_item_subtotal,
                        sum(case when stock_type = 'wastage-sale' then stock_item_qty end) as wastage_sale_qty,
                        sum(case when stock_type = 'sale-return' then stock_item_qty end) as sale_return_qty,
                        sum(case when stock_type = 'purchase' then stock_item_qty end) as purchase_qty,
                        sum(case when stock_type = 'purchase' then stock_item_subtotal end) as purchase_item_subtotal,
                        sum(case when stock_type = 'purchase-order' then stock_item_qty end) as purchase_order_qty,
                        sum(case when stock_type = 'purchase-return' then stock_item_qty end) as purchase_return_qty,
                        sum(case when stock_type = 'transfer-in' then stock_item_qty end) as transfer_in_qty,
                        sum(case when stock_type = 'transfer-out' then stock_item_qty end) as transfer_out_qty,
                        sum(case when stock_type = 'specimen-copy' then stock_item_qty end) as specimen_copy_qty,
                        sum(case when stock_type = 'specimen-copy-return' then stock_item_qty end) as specimen_copy_return_qty
                    from {$table_prefeix}product_stock
                    where is_trash = 0 and stock_warehouse_id $warehouse_filter
                    group by stock_product_id
                ) as product_stock on stock_product_id = product_id",
                "left join (
                    select
                        vp_id,
                        sum(case when batch_expiry_date < curdate() then base_stock_in/base_qty end) as expired_qty,
                        sum(case when batch_expiry_date is null or batch_expiry_date > curdate() then base_stock_in/base_qty end) as stock_qty
                from product_base_stock
                where warehouse $warehouse_filter
                group by vp_id
                ) as base_stock on base_stock.vp_id = product.product_id",
                "left join (
                    SELECT
                        product_parent_id,
                        group_concat(product_id) as child_product
                    FROM {$table_prefeix}products 
                    where is_trash = 0
                    group by product_parent_id
                ) as child_product on child_product.product_parent_id = product_id",
                "left join {$table_prefeix}product_category on product_category_id = category_id",
                "left join {$table_prefeix}product_brands on product_brand_id = brand_id",
            ),
            "where"     => array(
                "product.is_trash = 0 {$edition_filter}",
                " AND product_name LIKE" => "%" . $requestData['search']['value'] . "%",
                " AND product_brand_id"   => $requestData["columns"][2]['search']['value'],
                " AND product_category_id"   => $requestData["columns"][3]['search']['value']
            ),
            "orderby"   => array(
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit"     => array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));


        $totalFilteredRecords = $getData ? $getData['count'] : 0;

    } else { // Get data withouth search

        $getData = easySelectA(array(
            "table"     => "products as product",
            "fields"    => "
                            product.product_id as pid, product_type, concat(product_name, ' ', if(product_group is null, '', product_group) ) as product_name, 
                            brand_name, product_purchase_price, product_sale_price, product_edition, product_unit, product_category_id, category_name,
                            if(initial_qty is null, 0, round(initial_qty, 2) ) as initial_qty,
                            if(production_qty is null, 0, round(production_qty, 2)) as production_qty,
                            if(sale_qty is null, 0, round(sale_qty, 2) ) as sale_qty,
                            if(wastage_sale_qty is null, 0, round(wastage_sale_qty, 2) ) as wastage_sale_qty,
                            if(sale_return_qty is null, 0, round(sale_return_qty, 2) ) as sale_return_qty,
                            if(purchase_qty is null, 0, round(purchase_qty, 2) ) as purchase_qty,
                            if(purchase_order_qty is null, 0, round(purchase_order_qty, 2) ) as purchase_order_qty,
                            if(purchase_return_qty is null, 0, round(purchase_return_qty, 2) ) as purchase_return_qty,
                            if(transfer_in_qty is null, 0, round(transfer_in_qty, 2) ) as transfer_in_qty,
                            if(transfer_out_qty is null, 0, round(transfer_out_qty, 2) ) as transfer_out_qty,
                            if(specimen_copy_qty is null, 0, round(specimen_copy_qty, 2) ) as specimen_copy_qty,
                            if(specimen_copy_return_qty is null, 0, round(specimen_copy_return_qty, 2) ) as specimen_copy_return_qty,
                            if(expired_qty is null, 0, round(expired_qty, 2) ) as expired_qty,
                            if(stock_qty is null, 0, round(stock_qty, 2) ) as stock_qty,
                            if(sale_item_subtotal is null, 0, round(sale_item_subtotal, 2)) as total_sold_amount,
                            if(purchase_item_subtotal is null, 0, round(purchase_item_subtotal, 2)) as total_purchased_amount,
                            if(sale_qty_in_range is null, 0, round(sale_qty_in_range,2 )) as sale_qty_in_range,
                            child_product as child_product_list
            ",
            "join"      => array(
                "left join (
                    select
                        stock_product_id,
                        sum(case when stock_type = 'initial' then stock_item_qty end) as initial_qty,
                        sum(case when stock_type = 'sale-production' then stock_item_qty end) as production_qty,
                        sum(case when stock_type = 'sale' then stock_item_qty end) as sale_qty,
                        sum(case when stock_type = 'sale' and stock_entry_date between '{$soldDateRange[0]}' and '{$soldDateRange[1]}' then stock_item_qty end) as sale_qty_in_range,
                        sum(case when stock_type = 'sale' then stock_item_subtotal end) as sale_item_subtotal,
                        sum(case when stock_type = 'wastage-sale' then stock_item_qty end) as wastage_sale_qty,
                        sum(case when stock_type = 'sale-return' then stock_item_qty end) as sale_return_qty,
                        sum(case when stock_type = 'purchase' then stock_item_qty end) as purchase_qty,
                        sum(case when stock_type = 'purchase' then stock_item_subtotal end) as purchase_item_subtotal,
                        sum(case when stock_type = 'purchase-order' then stock_item_qty end) as purchase_order_qty,
                        sum(case when stock_type = 'purchase-return' then stock_item_qty end) as purchase_return_qty,
                        sum(case when stock_type = 'transfer-in' then stock_item_qty end) as transfer_in_qty,
                        sum(case when stock_type = 'transfer-out' then stock_item_qty end) as transfer_out_qty,
                        sum(case when stock_type = 'specimen-copy' then stock_item_qty end) as specimen_copy_qty,
                        sum(case when stock_type = 'specimen-copy-return' then stock_item_qty end) as specimen_copy_return_qty
                    from {$table_prefeix}product_stock
                    group by stock_product_id
                ) as product_stock on stock_product_id = product_id",
                "left join (
                    select
                        vp_id,
                        sum(case when batch_expiry_date < curdate() then base_stock_in/base_qty end) as expired_qty,
                        sum(case when batch_expiry_date is null or batch_expiry_date > curdate() then base_stock_in/base_qty end) as stock_qty
                from product_base_stock
                group by vp_id
                ) as base_stock on base_stock.vp_id = product.product_id",
                "left join (
                    SELECT
                        product_parent_id,
                        group_concat(product_id) as child_product
                    FROM {$table_prefeix}products 
                    where is_trash = 0
                    group by product_parent_id
                ) as child_product on child_product.product_parent_id = product_id",
                "left join {$table_prefeix}product_category on product_category_id = category_id",
                "left join {$table_prefeix}product_brands on product_brand_id = brand_id",
            ),
            "where"     => array(
                "product.is_trash = 0 and product.product_type != 'Child' "
            ),
            "orderby"   => array(
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit"     => array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));

    }

    $allData = [];

    //print_r($getData);

    // Check if there have more then zero data
    if($getData) {
        
        foreach($getData['data'] as $key => $value) {

            $allNestedData = [];

            /**
             * For variable to product, we have to show all variation count in main product
             * 
             * And When click on the variable product, The all variation will be shown
             */
            if( $value["product_type"] === "Variable" and $value["child_product_list"] !== null ) {

                $variations = easySelectA(array(
                    "table"     => "products as product",
                    "fields"    => "
                                    product.product_id as pid, product_type, concat(product_name, ' ', if(product_group is null, '', product_group) ) as product_name, 
                                    brand_name, product_purchase_price, product_sale_price, product_edition, product_unit, product_category_id, category_name,
                                    if(initial_qty is null, 0, round(initial_qty, 2)) as initial_qty,
                                    if(production_qty is null, 0, round(production_qty, 2)) as production_qty,
                                    if(sale_qty is null, 0, round(sale_qty, 2)) as sale_qty,
                                    if(wastage_sale_qty is null, 0, round(wastage_sale_qty, 2)) as wastage_sale_qty,
                                    if(sale_return_qty is null, 0, round(sale_return_qty, 2)) as sale_return_qty,
                                    if(purchase_qty is null, 0, round(purchase_qty, 2)) as purchase_qty,
                                    if(purchase_order_qty is null, 0, round(purchase_order_qty, 2)) as purchase_order_qty,
                                    if(purchase_return_qty is null, 0, round(purchase_return_qty, 2)) as purchase_return_qty,
                                    if(transfer_in_qty is null, 0, round(transfer_in_qty, 2)) as transfer_in_qty,
                                    if(transfer_out_qty is null, 0, round(transfer_out_qty, 2)) as transfer_out_qty,
                                    if(specimen_copy_qty is null, 0, round(specimen_copy_qty, 2)) as specimen_copy_qty,
                                    if(specimen_copy_return_qty is null, 0, round(specimen_copy_return_qty, 2)) as specimen_copy_return_qty,
                                    if(expired_qty is null, 0, round(expired_qty, 2)) as expired_qty,
                                    if(stock_qty is null, 0, round(stock_qty, 2)) as stock_qty,
                                    if(sale_item_subtotal is null, 0, round(sale_item_subtotal, 2)) as total_sold_amount,
                                    if(purchase_item_subtotal is null, 0, round(purchase_item_subtotal, 2)) as total_purchased_amount,
                                    if(sale_qty_in_range is null, 0, round(sale_qty_in_range,2 )) as sale_qty_in_range
                    ",
                    "join"      => array(
                        "left join (
                            select
                                stock_product_id,
                                sum(case when stock_type = 'initial' then stock_item_qty end) as initial_qty,
                                sum(case when stock_type = 'sale-production' then stock_item_qty end) as production_qty,
                                sum(case when stock_type = 'sale' then stock_item_qty end) as sale_qty,
                                sum(case when stock_type = 'sale' and stock_entry_date between '{$soldDateRange[0]}' and '{$soldDateRange[1]}' then stock_item_qty end) as sale_qty_in_range,
                                sum(case when stock_type = 'sale' then stock_item_subtotal end) as sale_item_subtotal,
                                sum(case when stock_type = 'wastage-sale' then stock_item_qty end) as wastage_sale_qty,
                                sum(case when stock_type = 'sale-return' then stock_item_qty end) as sale_return_qty,
                                sum(case when stock_type = 'purchase' then stock_item_qty end) as purchase_qty,
                                sum(case when stock_type = 'purchase' then stock_item_subtotal end) as purchase_item_subtotal,
                                sum(case when stock_type = 'purchase-order' then stock_item_qty end) as purchase_order_qty,
                                sum(case when stock_type = 'purchase-return' then stock_item_qty end) as purchase_return_qty,
                                sum(case when stock_type = 'transfer-in' then stock_item_qty end) as transfer_in_qty,
                                sum(case when stock_type = 'transfer-out' then stock_item_qty end) as transfer_out_qty,
                                sum(case when stock_type = 'specimen-copy' then stock_item_qty end) as specimen_copy_qty,
                                sum(case when stock_type = 'specimen-copy-return' then stock_item_qty end) as specimen_copy_return_qty
                            from {$table_prefeix}product_stock
                            where is_trash = 0 and stock_warehouse_id $warehouse_filter
                            group by stock_product_id
                        ) as product_stock on stock_product_id = product_id",
                        "left join (
                            select
                                vp_id,
                                sum(case when batch_expiry_date < curdate() then base_stock_in/base_qty end) as expired_qty,
                                sum(case when batch_expiry_date is null or batch_expiry_date > curdate() then base_stock_in/base_qty end) as stock_qty
                        from product_base_stock
                        where warehouse $warehouse_filter
                        group by vp_id
                        ) as base_stock on base_stock.vp_id = product.product_id",
                        "left join {$table_prefeix}product_category on product_category_id = category_id",
                        "left join {$table_prefeix}product_brands on product_brand_id = brand_id",
                    ),
                    "where"     => array(
                        "product.product_id in({$value['child_product_list']})"
                    ),
                
                ));



                $allChildProduct = [];
                
                // Store the total count for main product
                $mainProduct = array(
                    "initial_qty"   => 0,
                    "production_qty"   => 0,
                    "purchase_qty"   => 0,
                    "purchase_return_qty"   => 0,
                    "transfer_in_qty"   => 0,
                    "transfer_out_qty"   => 0,
                    "sale_qty"   => 0,
                    "sale_qty_in_range"   => 0,
                    "sale_return_qty"   => 0,
                    "specimen_copy_qty"   => 0,
                    "specimen_copy_return_qty"   => 0,
                    "expired_qty"   => 0,
                    "stock_qty"   => 0,
                    "stock_value"   => 0,
                    "stock_balance"   => 0,
                    "total_purchased_amount"   => 0,
                    "total_sold_amount"   => 0
                );

                if($variations !== false) {

                    // cp = child product
                    foreach($variations["data"] as $cpKey => $cpVal ) {

                        $childProduct = [];

                        $childProduct[] = "";
                        //$childProduct[] = "<a title='Show More Details' href='". full_website_address() ."/reports/product-report/?pid={$cpVal['pid']}'>{$cpVal['product_name']}</a>";
                        $childProduct[] = "<a title='Show More Details' href='". full_website_address() ."/reports/product-report/?pid={$cpVal['pid']}'>{$cpVal['product_name']}</a> 
                                    <a title='Update stock' style='padding-left: 5px; color: #a1a1a1;' class='updateEntry' href='". full_website_address() . "/xhr/?module=reports&page=updateProductStock' data-to-be-updated='". $cpVal["pid"] ."'><i class='fa fa-refresh'></i></a>";

                        $childProduct[] = $cpVal["brand_name"];
                        $childProduct[] = $cpVal["category_name"];
                        $childProduct[] = $cpVal["product_edition"];
                        $childProduct[] = $cpVal["initial_qty"];
                        $childProduct[] = $cpVal["production_qty"];
                        $childProduct[] = number_format($cpVal["purchase_qty"], 2);
                        $childProduct[] = $cpVal["purchase_return_qty"];
                        $childProduct[] = $cpVal["transfer_in_qty"];
                        $childProduct[] = $cpVal["transfer_out_qty"];
                        $childProduct[] = number_format($cpVal["sale_qty"], 2) ;
                        $childProduct[] = $cpVal["sale_qty_in_range"];
                        $childProduct[] = $cpVal["sale_return_qty"];
                        $childProduct[] = $cpVal["specimen_copy_qty"];
                        $childProduct[] = $cpVal["specimen_copy_return_qty"];
                        $childProduct[] = $cpVal["expired_qty"];
                        $childProduct[] = $cpVal["stock_qty"];
                        $childProduct[] = $cpVal["product_unit"];
                        $childProduct[] = $cpVal["stock_qty"] * $cpVal["product_sale_price"];
                        $childProduct[] = $cpVal["stock_qty"] * $cpVal["product_purchase_price"];
                        $childProduct[] = $cpVal["total_purchased_amount"];
                        $childProduct[] = $cpVal["total_sold_amount"];

                        $allChildProduct[] = $childProduct;


                    
                        // Store main product details    
                        $mainProduct["initial_qty"] += $cpVal["initial_qty"];
                        $mainProduct["production_qty"] += $cpVal["production_qty"];
                        $mainProduct["purchase_qty"] += $cpVal["purchase_qty"];
                        $mainProduct["purchase_return_qty"] += $cpVal["purchase_return_qty"];
                        $mainProduct["transfer_in_qty"] += $cpVal["transfer_in_qty"];
                        $mainProduct["transfer_out_qty"] += $cpVal["transfer_out_qty"];
                        $mainProduct["sale_qty"] += $cpVal["sale_qty"];
                        $mainProduct["sale_qty_in_range"] += $cpVal["sale_qty_in_range"];
                        $mainProduct["sale_return_qty"] += $cpVal["sale_return_qty"];
                        $mainProduct["specimen_copy_qty"] += $cpVal["specimen_copy_qty"];
                        $mainProduct["specimen_copy_return_qty"] += $cpVal["specimen_copy_return_qty"];
                        $mainProduct["expired_qty"] += $cpVal["expired_qty"];
                        $mainProduct["stock_qty"] += $cpVal["stock_qty"];
                        $mainProduct["stock_value"] += $cpVal["stock_qty"] * $cpVal["product_sale_price"];
                        $mainProduct["stock_balance"] += $cpVal["stock_qty"] * $cpVal["product_purchase_price"];
                        $mainProduct["total_purchased_amount"] += $cpVal["total_purchased_amount"];
                        $mainProduct["total_sold_amount"] += $cpVal["total_sold_amount"];

                    }

                }


                $allNestedData[] = "";
                $allNestedData[] = "<a title='Show More Details' class='has-child-row'>{$value['product_name']}</a>";
                $allNestedData[] = $value["brand_name"];
                $allNestedData[] = $value["category_name"];
                $allNestedData[] = "";
                $allNestedData[] = $mainProduct["initial_qty"];
                $allNestedData[] = $mainProduct["production_qty"];
                $allNestedData[] = $mainProduct["purchase_qty"];
                $allNestedData[] = $mainProduct["purchase_return_qty"];
                $allNestedData[] = $mainProduct["transfer_in_qty"];
                $allNestedData[] = $mainProduct["transfer_out_qty"];
                $allNestedData[] = $mainProduct["sale_qty"];
                $allNestedData[] = $mainProduct["sale_qty_in_range"];
                $allNestedData[] = $mainProduct["sale_return_qty"];
                $allNestedData[] = $mainProduct["specimen_copy_qty"];
                $allNestedData[] = $mainProduct["specimen_copy_return_qty"];
                $allNestedData[] = $mainProduct["expired_qty"];
                $allNestedData[] = $mainProduct["stock_qty"];
                $allNestedData[] = $value["product_unit"];
                $allNestedData[] = $mainProduct["stock_value"];
                $allNestedData[] = $mainProduct["stock_balance"];
                $allNestedData[] = $mainProduct["total_purchased_amount"];
                $allNestedData[] = $mainProduct["total_sold_amount"];
                $allNestedData["child"] = $allChildProduct;


            } else {

                
                $allNestedData[] = "";
                $allNestedData[] = "<a title='Show More Details' href='". full_website_address() ."/reports/product-report/?pid={$value['pid']}'>{$value['product_name']}</a> 
                                    <a title='Update stock' style='padding-left: 5px; color: #a1a1a1;' class='updateEntry' href='". full_website_address() . "/xhr/?module=reports&page=updateProductStock' data-to-be-updated='". $value["pid"] ."'><i class='fa fa-refresh'></i></a>";
                $allNestedData[] = $value["brand_name"];
                $allNestedData[] = $value["category_name"];
                $allNestedData[] = $value["product_edition"];
                $allNestedData[] = $value["initial_qty"];
                $allNestedData[] = $value["production_qty"];
                $allNestedData[] = $value["purchase_qty"];
                $allNestedData[] = $value["purchase_return_qty"];
                $allNestedData[] = $value["transfer_in_qty"];
                $allNestedData[] = $value["transfer_out_qty"];
                $allNestedData[] = $value["sale_qty"];
                $allNestedData[] = $value["sale_qty_in_range"];
                $allNestedData[] = $value["sale_return_qty"];
                $allNestedData[] = $value["specimen_copy_qty"];
                $allNestedData[] = $value["specimen_copy_return_qty"];
                $allNestedData[] = $value["expired_qty"];
                $allNestedData[] = $value["stock_qty"];
                $allNestedData[] = $value["product_unit"];
                $allNestedData[] = $value["stock_qty"] * $value["product_sale_price"];
                $allNestedData[] = $value["stock_qty"] * $value["product_purchase_price"];
                $allNestedData[] = $value["total_purchased_amount"];
                $allNestedData[] = $value["total_sold_amount"];

            }

            
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

/*************************** Customer Reports ***********************/
if(isset($_GET['page']) and $_GET['page'] == "updateProductStock") {

    $pid = safe_input($_POST["datatoUpdate"]);

    // Delete previous stock belongs to this product id
    runQuery("DELETE FROM product_base_stock WHERE product_id = {$pid}");

    // Insert New Stocks
    runQuery("
        INSERT INTO product_base_stock 
        (product_id, vp_id, warehouse, base_stock_in, base_qty, batch_id, batch_expiry_date)
        SELECT 
                product_stock.stock_product_id as product_id,
                product.product_id AS vp_id, 
                stock_warehouse_id as warehouse, 
                sum(
                    (   -- All stock in sunch as purchase, return etc
                            CASE WHEN ( 
                                        stock_type = 'initial' OR 
                                        stock_type = 'adjustment' OR 
                                        stock_type = 'sale-production' OR
                                        stock_type = 'sale-return' OR
                                        stock_type = 'purchase' OR
                                        stock_type = 'specimen-copy-return' OR
                                        stock_type = 'transfer-in'
                                    ) and stock_item_qty IS NOT NULL 
                                THEN stock_item_qty 
                                ELSE 0 
                            END 
                    
                        - -- subtract stock out from stock in
                        
                        -- All stock out, such as sale, specimen copy etc
                        CASE WHEN ( 
                                    stock_type = 'sale' OR 
                                    stock_type = 'wastage-sale' OR
                                    stock_type = 'purchase-return' OR
                                    stock_type = 'specimen-copy' OR
                                    stock_type = 'transfer-out'
                                ) and stock_item_qty IS NOT NULL 
                            THEN stock_item_qty 
                            ELSE 0 
                        END
                    )
                
                    * -- And multiply with base quantity, which is taken from product unit 
                    
                    if(vp_unit.base_qnt is null, 1, vp_unit.base_qnt) 
            
                ) as base_stock_in,
            if(np_unit.base_qnt is null, 1, np_unit.base_qnt) as base_qty,
            stock_batch_id as batch_id,
            batch_expiry_date
        FROM `ro_products` as product
        left join ro_products as vp on vp.product_parent_id = product.product_parent_id and vp.product_variations = product.product_variations or vp.product_id = product.product_id
        left join ro_product_stock as product_stock on vp.product_id = product_stock.stock_product_id
        left join ro_product_units as vp_unit on vp.product_unit = vp_unit.unit_name -- vp = variable product
        left join ro_product_units as np_unit on product.product_unit = np_unit.unit_name -- np = normal product
        left join ro_product_batches as batch on batch.batch_id = stock_batch_id and batch.product_id = stock_product_id
        where stock_warehouse_id is not null and product_stock.is_trash = 0 and 
        product_stock.stock_product_id = '{$pid}'
        group by product.product_id, product_stock.stock_product_id, stock_warehouse_id, stock_batch_id;
    ");


    echo '{
        "title": "Stock has been successfully updated.",
        "icon": "success"
    }';


}

/*************************** Customer Reports ***********************/
if(isset($_GET['page']) and $_GET['page'] == "customerReports") {
    
    $requestData = $_REQUEST;
    $getData = [];
    $search = safe_input($requestData['search']['value']);

    // List of all columns name
    $columns = array(
        "",
        "customer_name"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "customers",
        "fields" => "count(*) as totalRow",
        "where" => array(
        "is_trash = 0"
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }

    $getDateRange = ( isset( $requestData['columns'][1]['search']['value']) and !empty($requestData['columns'][1]['search']['value']) )  ? safe_input($requestData['columns'][1]['search']['value']) : "1970-01-01 - " . date("Y-12-31");
    $dateRange = explode(" - ", $getDateRange);

         
    $getData = easySelectD(
        "select customer_id, customer_name,
            if(sales_grand_total_in_filtered_date is null, 0, round(sales_grand_total_in_filtered_date, 2)) as sales_grand_total_in_filtered_date, 
            if(wastage_sale_grand_total_in_filtered_date is null, 0, round(wastage_sale_grand_total_in_filtered_date, 2)) as wastage_sale_grand_total_in_filtered_date,
            if(sales_shipping_in_filtered_date is null, 0, round(sales_shipping_in_filtered_date, 2)) as sales_shipping_in_filtered_date,
            if(product_returns_grand_total_in_filtered_date is null, 0, round(product_returns_grand_total_in_filtered_date, 2)) as product_returns_grand_total_in_filtered_date,
            if(received_payments_amount_in_filtered_date is null, 0, round(received_payments_amount_in_filtered_date, 2)) as received_payments_amount_in_filtered_date,
            if(received_payments_bonus_in_filtered_date is null, 0, round(received_payments_bonus_in_filtered_date, 2)) as received_payments_bonus_in_filtered_date,
            if(discounts_amount_in_filtered_date is null, 0, round(discounts_amount_in_filtered_date, 2)) as discounts_amount_in_filtered_date,
            round((
                    if(customer_opening_balance is null, 0, customer_opening_balance) +						
                    if(total_return_before_filtered_date is null, 0, total_return_before_filtered_date) +
                    if(received_payments_amount_before_filtered_date is null, 0, received_payments_amount_before_filtered_date) +
                    if(received_payments_bonus_before_filtered_date is null, 0, received_payments_bonus_before_filtered_date) +
                    if(discounts_amount_before_filtered_date is null, 0, discounts_amount_before_filtered_date)
            ) - ( 
                    if(sales_grand_total_before_filtered_date is null, 0, sales_grand_total_before_filtered_date) +
                    if(wastage_sale_grand_total_before_filtered_date is null, 0, wastage_sale_grand_total_before_filtered_date)
            ), 2) as previous_balance,
            upazila_name, district_name
        from {$table_prefeix}customers as customer
        left join {$table_prefeix}upazilas on customer_upazila = upazila_id
        left join {$table_prefeix}districts on customer_district = district_id
        left join (
            select
                sales_customer_id,
                sum( case when is_return = 0 and sales_delivery_date between '{$dateRange[0]}' and '{$dateRange[1]}' then sales_grand_total end ) as sales_grand_total_in_filtered_date,
                sum( case when is_return = 0 and sales_delivery_date < '{$dateRange[0]}' then sales_grand_total end ) as sales_grand_total_before_filtered_date,
                sum( case when is_return = 0 and sales_delivery_date between '{$dateRange[0]}' and '{$dateRange[1]}' then sales_shipping end ) as sales_shipping_in_filtered_date,
                sum( case when is_return = 1 and sales_delivery_date between '{$dateRange[0]}' and '{$dateRange[1]}' then sales_grand_total end ) as product_returns_grand_total_in_filtered_date,
                sum( case when is_return = 1 and sales_delivery_date < '{$dateRange[0]}' then sales_grand_total end ) as total_return_before_filtered_date
            from {$table_prefeix}sales where is_trash = 0 group by sales_customer_id
        ) as sales on customer_id = sales_customer_id
        left join ( select
                wastage_sale_customer,
                sum( case when wastage_sale_date between '{$dateRange[0]}' and '{$dateRange[1]}' then wastage_sale_grand_total end ) as wastage_sale_grand_total_in_filtered_date,
                sum( case when wastage_sale_date < '{$dateRange[0]}' then wastage_sale_grand_total end ) as wastage_sale_grand_total_before_filtered_date
            from {$table_prefeix}wastage_sale where is_trash = 0 group by wastage_sale_customer
        ) as wastage_sale on wastage_sale_customer = sales_customer_id
        left join ( select 
                received_payments_from, 
                sum( case when date(received_payments_datetime) between '{$dateRange[0]}' and '{$dateRange[1]}' then received_payments_amount end ) as received_payments_amount_in_filtered_date,
                sum( case when date(received_payments_datetime) < '{$dateRange[0]}' then received_payments_amount end ) as received_payments_amount_before_filtered_date,
                sum( case when date(received_payments_datetime) between '{$dateRange[0]}' and '{$dateRange[1]}' then received_payments_bonus end ) as received_payments_bonus_in_filtered_date,
                sum( case when date(received_payments_datetime) < '{$dateRange[0]}' then received_payments_bonus end ) as received_payments_bonus_before_filtered_date
            from {$table_prefeix}received_payments where is_trash = 0 and received_payments_type != 'Discounts' group by received_payments_from
        ) as received_payments on customer_id = received_payments.received_payments_from
        left join ( select 
                received_payments_from, 
                sum( case when date(received_payments_datetime) between '{$dateRange[0]}' and '{$dateRange[1]}' then received_payments_amount end ) as discounts_amount_in_filtered_date,
                sum( case when date(received_payments_datetime) < '{$dateRange[0]}' then received_payments_amount end ) as discounts_amount_before_filtered_date
            from {$table_prefeix}received_payments where is_trash = 0 and received_payments_type = 'Discounts' group by received_payments_from
        ) as given_discounts on customer_id = given_discounts.received_payments_from
        where customer.is_trash = 0 and customer_name like '{$search}%'
        group by customer_id order by customer_name {$requestData['order'][0]['dir']}
        LIMIT {$requestData['start']}, {$requestData['length']}
        "
    );

    $totalFilteredRecords = $getData ? $getData["count"] : 0;


    $allData = [];
    // Check if there have more then zero data
    if($getData) {
        
        foreach($getData['data'] as $key => $value) {


            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = "<a title='Show More Details' href='". full_website_address() ."/reports/customer-report/?cid={$value['customer_id']}'>{$value['customer_name']}, {$value['upazila_name']}, {$value['district_name']}</a>";
            $allNestedData[] = $value["previous_balance"];
            $allNestedData[] = ( $value["sales_grand_total_in_filtered_date"] + $value["wastage_sale_grand_total_in_filtered_date"] ) - $value["sales_shipping_in_filtered_date"];
            $allNestedData[] = $value["sales_shipping_in_filtered_date"];
            $allNestedData[] = $value["received_payments_amount_in_filtered_date"];
            $allNestedData[] = $value["received_payments_bonus_in_filtered_date"];
            $allNestedData[] = $value["product_returns_grand_total_in_filtered_date"];
            $allNestedData[] = $value["discounts_amount_in_filtered_date"];
            $allNestedData[] = round((    
                                    $value["previous_balance"] + $value["received_payments_amount_in_filtered_date"] + 
                                    $value["received_payments_bonus_in_filtered_date"] + $value["product_returns_grand_total_in_filtered_date"]) - 
                                ( 
                                    ($value["sales_grand_total_in_filtered_date"] + $value["wastage_sale_grand_total_in_filtered_date"]) - $value["discounts_amount_in_filtered_date"]
                                ), 2);
            
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


/*************************** Day By Day Customer Reports ***********************/
if(isset($_GET['page']) and $_GET['page'] == "customerStatement") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // Count Total recrods
    $totalFilteredRecords = $totalRecords = 0;
    $allData = [];

    if( isset($_GET["cid"]) ) {

        $dateRange = explode(" - ", safe_input($requestData["dateRange"]));
        $customer_id = safe_input($_GET["cid"]);

        $previous_balance = easySelectD("
			SELECT 
                @balance := (
						if(customer_opening_balance is null, 0, customer_opening_balance) +						
						if(total_returned_before_filtered_date is null, 0, total_returned_before_filtered_date) +
						if(total_payment_before_filtered_date is null, 0, total_payment_before_filtered_date)
				) - ( 
						if(total_purchased_before_filtered_date is null, 0, total_purchased_before_filtered_date) +
                        if(total_wastage_purched_before_filtered_date is null, 0, total_wastage_purched_before_filtered_date) +
                        if(total_payment_return_before_filtered_date is null, 0, total_payment_return_before_filtered_date)
				)
			FROM {$table_prefeix}customers as customers
			left join ( select
					sales_customer_id,
					sum(case when is_return = 0 then sales_grand_total end) as total_purchased_before_filtered_date,
                    sum(case when is_return = 1 then sales_due end) as total_returned_before_filtered_date
				from {$table_prefeix}sales where is_trash = 0 and sales_delivery_date < '{$dateRange[0]}' group by sales_customer_id
			) as sales on sales_customer_id = customer_id
            left join ( select
                    wastage_sale_customer,
                    sum(wastage_sale_grand_total) as total_wastage_purched_before_filtered_date
                from {$table_prefeix}wastage_sale where is_trash = 0 and wastage_sale_date < '{$dateRange[0]}' group by wastage_sale_customer
            ) as wastage_sale on wastage_sale_customer = customer_id
			left join ( select 
					received_payments_from,
					sum(received_payments_amount) + sum(received_payments_bonus) as total_payment_before_filtered_date
				from {$table_prefeix}received_payments where is_trash = 0 and date(received_payments_datetime) < '{$dateRange[0]}' group by received_payments_from
			) as payments on received_payments_from = customer_id
            left join (select
                    payments_return_customer_id,
                    sum(payments_return_amount) as total_payment_return_before_filtered_date
                from {$table_prefeix}payments_return
                where is_trash = 0 and payments_return_type = 'Outgoing' and date(payments_return_date) < '{$dateRange[0]}'
                group by payments_return_customer_id
            ) as payment_return on customer_id = payments_return_customer_id
			where customer_id = {$customer_id}
	    ");
 
            
        $getData = easySelectD("
            select dates, record_id, customers, reference, reference_link, description, purchase_amount, discount, shipping, debit, credit, @balance := ( @balance + credit ) - debit as balance from
            (   
                select 
                    1 as sortby,
                    sales_delivery_date as dates,
                    sales_id as record_id,
                    sales_customer_id as customers, 
                    sales_reference as reference, 
                    '/xhr/?module=reports&page=showInvoiceProducts&id=' as reference_link,
                    combine_description( if(is_exchange=1, 'Product Exchange', 'Product Purchase'), sales_note) as description, 
                    sales_total_amount as purchase_amount,
                    (sales_product_discount + sales_discount) as discount,
                    sales_shipping as shipping, 
                    if(sales_grand_total > 0, sales_grand_total, 0) as debit,
                    if(sales_grand_total < 0, abs(sales_grand_total), 0) as credit
                from {$table_prefeix}sales
                where is_trash = 0 and is_return = 0 and sales_delivery_date between '{$dateRange[0]}' and '{$dateRange[1]}' group by sales_id
                UNION ALL
                select 
                    2 as sortby,
                    sales_delivery_date as dates,
                    sales_id as record_id,
                    sales_customer_id as customers, 
                    sales_reference as reference, 
                    '/xhr/?module=reports&page=showInvoiceProducts&id=' as reference_link,
                    combine_description('Product Return', sales_note) as description, 
                    '',
                    '',
                    '',
                    0 as debit,
                    sales_grand_total as credit
                from {$table_prefeix}sales
                where is_trash = 0 and is_return = 1 and sales_delivery_date between '{$dateRange[0]}' and '{$dateRange[1]}' group by sales_id
                UNION ALL
                select
                    3 as sortby,
                    date(received_payments_datetime),
                    '',
                    received_payments_from, 
                    received_payments_reference,
                    '',
                    combine_description(received_payments_type, received_payments_details), 
                    '',
                    '',
                    '',
                    0 as debit,
					received_payments_amount as credit
                from {$table_prefeix}received_payments where is_trash = 0 and date(received_payments_datetime) between '{$dateRange[0]}' and '{$dateRange[1]}' group by received_payments_id
                UNION ALL
                select
                    4 as sortby,
                    date(received_payments_datetime),
                    '',
                    received_payments_from, 
                    received_payments_reference,
                    '',
                    combine_description('Given Bonus', received_payments_details), 
                    '',
                    '',
                    '',
                    0 as debit,
                    received_payments_bonus as credit
                from {$table_prefeix}received_payments where is_trash = 0 and 
                received_payments_bonus > 0 and 
                date(received_payments_datetime) between '{$dateRange[0]}' and '{$dateRange[1]}' 
                group by received_payments_id
                UNION ALL
                select
                    5 as sortby,
                    wastage_sale_date,
                    wastage_sale_id,
                    wastage_sale_customer, 
                    concat('Sale/Wastage/', wastage_sale_id),
                    '/xhr/?module=sales&page=viewWastageSale&id=',
                    combine_description('Wastage Sales', concat(wastage_sale_reference, ', ', wastage_sale_note)), 
                    '',
                    '',
                    '',
                    wastage_sale_grand_total as debit,
					0 as credit
                from {$table_prefeix}wastage_sale where is_trash = 0 and wastage_sale_date between '{$dateRange[0]}' and '{$dateRange[1]}' group by wastage_sale_id
                UNION ALL
                select
                    6 as sortby,
                    payments_return_date,
                    '',
                    payments_return_customer_id, 
                    '',
                    '',
                    combine_description('Payment return ', payments_return_description) as description,
                    '',
                    '',
                    '',
                    payments_return_amount as debit,
                    0 as credit
                from {$table_prefeix}payments_return where is_trash = 0 and payments_return_date between '{$dateRange[0]}' and '{$dateRange[1]}' group by payments_return_id
                UNION ALL
                select
                    7 as sortby,
                    incomes_date,
                    '',
                    incomes_from, 
                    '',
                    '',
                    combine_description('Received Payments ', incomes_description) as description,
                    '',
                    '',
                    '',
                    0 as debit,
					incomes_amount as credit
                from {$table_prefeix}incomes where is_trash = 0 and incomes_date between '{$dateRange[0]}' and '{$dateRange[1]}' group by incomes_id

            ) as get_data
            where customers = {$customer_id} and date(dates) between '{$dateRange[0]}' and '{$dateRange[1]}'
            order by dates, sortby
        ");

        $totalFilteredRecords = $totalRecords = $getData !== false ? $getData["count"] : 0;

        // Check if there have more then zero data
        if(isset($getData['count']) and $getData['count'] > 0) {
            
            foreach($getData['data'] as $key => $value) {
                
                $allNestedData = [];
                $allNestedData[] = "";
                $allNestedData[] = date("d/m/Y", strtotime($value["dates"]));
                $allNestedData[] = "<a data-toggle='modal' data-target='#modalDefault' href='" . full_website_address() . "{$value['reference_link']}{$value['record_id']}'>{$value['reference']}</a>";
                $allNestedData[] = $value["description"];
                $allNestedData[] = $value["purchase_amount"];
                $allNestedData[] = $value["discount"];
                $allNestedData[] = $value["shipping"];
                $allNestedData[] = number_format($value["debit"], 0, "", "");
                $allNestedData[] = number_format($value["credit"], 0, "", "");
                $allNestedData[] = number_format($value["balance"], 2) ;

                $allData[] = $allNestedData;
            }
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


/************************** Invoice Product **********************/
if(isset($_GET['page']) and $_GET['page'] == "showInvoiceProducts") {
  
    // Select sales
    $selectSale = easySelect(
        "sales",
        "*",
        array (
            "left join {$table_prefeix}customers on sales_customer_id = customer_id"
        ),
        array (
            "sales_id"  => $_GET["id"]
        )
    );

    // Select Sales item
    $selectSalesItems = easySelectA(array(
        "table"     => "product_stock",
        "fields"    => "product_name, stock_item_price, stock_item_qty, stock_item_subtotal",
        "join"      => array(
            "left join {$table_prefeix}products on product_id =  stock_product_id"
        ),
        "where" => array(
            "is_bundle_item = 0 and stock_sales_id" => $_GET["id"]
        )
    ));

    $sales = $selectSale["data"][0];

    ?>

    <div class="modal-header">
        <h4 class="modal-title">Invoice Items</h4>
    </div>

    <div class="modal-body">

        <table> 
            <tbody>
                <tr>
                    <td style="padding: 0" class="col-md-3">Reference No: <?php echo $selectSale["data"][0]["sales_reference"] ?></td>
                    <td style="padding: 0" class="col-md-3 text-right">Date: <?php echo date("d/m/Y", strtotime($selectSale["data"][0]["sales_delivery_date"])) ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Customer:  <?php echo $selectSale["data"][0]["customer_name"] ?></strong></td>
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

                foreach($selectSalesItems["data"] as $key => $saleItems) {

                    echo "<tr>";
                    echo " <td>{$saleItems['stock_item_qty']}</td>";
                    echo " <td>{$saleItems['product_name']}</td>";
                    echo " <td>" . number_format($saleItems['stock_item_price'],2) . "</td>";
                    echo " <td class='text-right'>" . number_format($saleItems['stock_item_subtotal'],2) . "</td>";
                    echo "</tr>";

                }

            ?>     

            </tbody>

            <tfoot>  
                <tr>
                    <th colspan="3" class="text-right">Total:</th>
                    <th class="text-right"><?php echo number_format(($sales["sales_total_amount"] - $sales["sales_product_discount"]), 2) ?></th>
                </tr>
            </tfoot>

        </table>
        
        <br/>

        <div class="no-print">
            <div style="display: block; width: 200px; margin: auto;" class="form-group">
                <a onClick='BMS.MAIN.printPage(this.href, event);' class="btn btn-primary" href="<?php echo full_website_address(); ?>/invoice-print/?autoPrint=true&invoiceType=posSale&id=<?php echo htmlentities($_GET["id"]); ?>"> <i class="fa fa-print"></i> Print</a>
                <a target="_blank" class="btn btn-primary" href="<?php echo full_website_address(); ?>/invoice-print/?invoiceType=posSale&id=<?php echo htmlentities($_GET["id"]); ?>"> <i class="fa fa-eye"></i> View Invoice</a>
            </div>
        </div>
    
    </div> <!-- /.modal-body -->

    <?php
  
}

/************************** Product Return Details **********************/
if(isset($_GET['page']) and $_GET['page'] == "showReturnProducts") {
  
    // Select return
    $selectReturn = easySelect(
        "product_returns",
        "*",
        array (
            "left join {$table_prefeix}customers on product_returns_customer_id = customer_id"
        ),
        array (
            "product_returns_id"  => $_GET["id"]
        )
    );

    // Select return item
    $selectProductReturnItems = easySelect(
        "product_return_items",
        "*",
        array(),
        array (
            "product_return_items_returns_id" => $_GET["id"]
        )
    );

    $return = $selectReturn["data"][0];

    ?>

    <div class="modal-header">
        <h4 class="modal-title">Return Items</h4>
    </div>

    <div class="modal-body">

        <table> 
            <tbody>
                <tr>
                    <td style="padding: 0" class="col-md-3">Reference No: <?php echo $return["product_returns_reference"]; ?></td>
                    <td style="padding: 0" class="col-md-3 text-right">Date: <?php echo date("d/m/Y", strtotime($return["product_returns_date"])); ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Customer:  <?php echo $return["customer_name"] ?></strong></td>
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

                foreach($selectProductReturnItems["data"] as $key => $returnItems) {
                    $productName = easySelect("products", "product_name", array(), array( "product_id" => $returnItems['product_return_items_product_id'] ));

                    echo "<tr>";
                    echo " <td>{$returnItems['product_return_items_products_quantity']}</td>";
                    echo " <td>{$productName['data'][0]['product_name']}</td>";
                    echo " <td>" . number_format($returnItems['product_return_items_sale_price'],2) . "</td>";
                    echo " <td class='text-right'>" . number_format($returnItems['product_return_items_grand_total'],2) . "</td>";
                    echo "</tr>";

                }

            ?>     

            </tbody>

            <tfoot>  
                <tr>
                    <th colspan="3" class="text-right">Total:</th>
                    <th class="text-right"><?php echo number_format($return["product_returns_total_amount"], 2) ?></th>
                </tr>
                <tr>
                    <th colspan="3" class="text-right">Discount:</th>
                    <th class="text-right"><?php echo number_format( ($return["product_returns_items_discount"] + $return["product_returns_total_discount"] ) , 2) ?></th>
                </tr>
                <tr>
                    <th colspan="3" class="text-right">Surcharge:</th>
                    <th class="text-right"><?php echo number_format($return["product_returns_surcharge"], 2) ?></th>
                </tr>
                <tr>
                    <th colspan="3" class="text-right">Grand Total:</th>
                    <th class="text-right"><?php echo number_format($return["product_returns_grand_total"], 2) ?></th>
                </tr>
            </tfoot>

        </table>
        
        <br/>

        <div class="no-print">
            <div style="display: block; width: 200px; margin: auto;" class="form-group">
                <a target="_blank" class="btn btn-primary" href="<?php echo full_website_address(); ?>/invoice-print/?autoPrint=true&invoiceType=produtReturn&id=<?php echo htmlentities($_GET["id"]); ?>"> <i class="fa fa-print"></i> Print</a>
            </div>
        </div>
    
    </div> <!-- /.modal-body -->

    <?php
  
}



/************************** Product and Company wise purchase report in Point of Sale **********************/
if(isset($_GET['page']) and $_GET['page'] == "totalPurchasedQuantityOfThisCustomer") {

        $cid = safe_input($_GET["cid"]);
        $pid = safe_input($_GET["pid"]);

        $selectPurchased = easySelectD("
            select 
                stock_entry_date as sales_delivery_date, 
                product_name, customer_name,
                stock_item_qty
            from {$table_prefeix}product_stock as product_stock 
            left join {$table_prefeix}products on stock_product_id = product_id
            left join {$table_prefeix}sales on sales_id = stock_sales_id
            left join {$table_prefeix}customers on sales_customer_id = customer_id
            where product_stock.is_trash = 0 and sales_customer_id = {$cid} and stock_product_id = {$pid}
        ");

    ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Purchased Report</h4>
    </div>

    <div class="modal-body">

    <?php 
        if($selectPurchased === false) {
            echo "<div class='alert alert-danger'>No purchased found</div>";
            return;
        }
        $totalPurchased = 0;
        echo "<b>Customer: </b>" . $selectPurchased["data"][0]["customer_name"];
        echo "<br/><br/>";
        
        echo "<table class='table'>";
        echo "<tr>";
        echo "<th>Date</th>";
        echo "<th>Product</th>";
        echo "<th>Quantity</th>";
        echo "</tr>";

        foreach($selectPurchased["data"] as $key => $value) {
            echo "<tr>";
            echo "<td>{$value['sales_delivery_date']}</td>";
            echo "<td>{$value['product_name']}</td>";
            echo "<td>". number_format($value['stock_item_qty'], 0) ."</td>";
            echo "</tr>";
            $totalPurchased += $value['stock_item_qty'];
        }

        echo "<tr>";
        echo "<th class='text-right' colspan='2'>Total:</th>";
        echo "<th>{$totalPurchased}</th>";
        echo "</tr>";

        echo "</table>";
    ?>
    
    </div> <!-- /.modal-body -->

    <?php
  
}


/*************************** Expense Reports All ***********************/
if(isset($_GET['page']) and $_GET['page'] == "expenseReportsAll") {
    
    $requestData = $_REQUEST;
    $getData = [];
    $search = safe_input($requestData['search']['value']);

    $getDateRange = ( isset( $requestData['columns'][1]['search']['value']) and !empty($requestData['columns'][1]['search']['value']) )  ? safe_input($requestData['columns'][1]['search']['value']) : "1970-01-01 - " . date("Y-12-31");
    $dateRange = explode(" - ", $getDateRange);

    // List of all columns name
    $columns = array(
        "",
        "customer_name"
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
         
    $getData = easySelectD(
        "SELECT 
            payment_category_id, payment_category_name,
            if(payment_items_amount_sum is null, 0, payment_items_amount_sum) + if(bill_items_amount_sum is null, 0, bill_items_amount_sum) as total_amount_in_this_category
        from {$table_prefeix}payments_categories as payments_categorie
        left join ( SELECT 
                payment_items_category_id, 
                sum(payment_items_amount) as payment_items_amount_sum 
            from {$table_prefeix}payment_items where is_trash = 0 and payment_items_date between '{$dateRange[0]}' and '{$dateRange[1]}' group by payment_items_category_id 
        ) as payments_items on payment_items_category_id = payment_category_id
        left join ( SELECT 
                bill_items_category, 
                sum(bill_items_amount) as bill_items_amount_sum 
            from {$table_prefeix}bill_items where is_trash = 0 and date(bill_items_add_on) between '{$dateRange[0]}' and '{$dateRange[1]}' group by bill_items_category 
        ) as bill_items on bill_items_category = payment_category_id
        where payments_categorie.is_trash = 0 and payment_category_name LIKE '{$search}%'
        having total_amount_in_this_category > 0
        order by payment_category_name {$requestData['order'][0]['dir']}
        LIMIT {$requestData['start']}, {$requestData['length']}
        "
    );


    $salaryPaymentData = easySelectD(
        "SELECT
            salary_type,
            sum(salary_amount) as total_salary_amount_by_type
        from {$table_prefeix}salaries where is_trash = 0 and salary_type LIKE '{$search}%' and salary_month between '{$dateRange[0]}' and '{$dateRange[1]}' group by salary_type
        "
    );
    

    if ( isset( $requestData['columns'][1]['search']['value']) and !empty($requestData['columns'][1]['search']['value']) ) {
        $totalFilteredRecords = $getData["count"] + $salaryPaymentData["count"];
    }
    
    
    $allData = [];

    // not category payments
    if(isset($salaryPaymentData['count']) and $salaryPaymentData['count'] > 0) {
        
        foreach($salaryPaymentData['data'] as $key => $value) {

            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = "<a href='". full_website_address() ."/reports/expense-report/?paymentType={$value['salary_type']}&dateRange={$getDateRange}'>{$value['salary_type']} Payments</a>";
            $allNestedData[] = $value["total_salary_amount_by_type"];
            $allData[] = $allNestedData;
        }
    }

    // Check if there have more then zero data in category payment
    if(isset($getData['count']) and $getData['count'] > 0) {
        
        foreach($getData['data'] as $key => $value) {

            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = "<a href='". full_website_address() ."/reports/expense-report/?cid={$value['payment_category_id']}&dateRange={$getDateRange}'>{$value['payment_category_name']}</a>";
            $allNestedData[] = $value["total_amount_in_this_category"];
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


/*************************** Expense Reports Signle ***********************/
if(isset($_GET['page']) and $_GET['page'] == "expenseReportsSignle") {
    
    $requestData = $_REQUEST;
    $getData = [];
    $search = safe_input($requestData['search']['value']);
    $cat_id = safe_input($_GET['cid']);

    $getDateRange = ( isset( $_GET["dateRange"] ) and !empty( $_GET["dateRange"] ) )  ? safe_input( $_GET["dateRange"] ) : "1970-01-01 - " . date("Y-12-31");
    $dateRange = explode(" - ", $getDateRange);

    // List of all columns name
    $columns = array(
        "",
        "customer_name"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectD(
        "SELECT category_id from
        (
            (select 
                payment_items_category_id as category_id
            from {$table_prefeix}payment_items where is_trash = 0 and payment_items_date between '{$dateRange[0]}' and '{$dateRange[1]}' order by payment_items_id DESC)
            UNION ALL
            (select 
                bill_items_category as category_id
            from {$table_prefeix}bill_items where is_trash = 0 and date(bill_items_add_on) between '{$dateRange[0]}' and '{$dateRange[1]}')
        ) as getData
        where category_id = {$cat_id}
        "
    )["count"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
         
    $getData = easySelectD(
        "SELECT category_id, item_date, item_amount, item_description from
        (
            (select 
                1 as sortby,
                payment_items_category_id as category_id,
                payment_items_date as item_date,
                payment_items_amount as item_amount,
                payment_items_description as item_description
            from {$table_prefeix}payment_items where is_trash = 0 and payment_items_date between '{$dateRange[0]}' and '{$dateRange[1]}' order by payment_items_id DESC)
            UNION ALL
            (select 
                2 as sortby,
                bill_items_category as category_id,
                bill_items_date as item_date,
                bill_items_amount as item_amount,
                bill_items_note as item_description
            from {$table_prefeix}bill_items where is_trash = 0 and date(bill_items_add_on) between '{$dateRange[0]}' and '{$dateRange[1]}')
        ) as getData
        where category_id = {$cat_id}
        order by item_date {$requestData['order'][0]['dir']}, sortby ASC, item_description DESC
        LIMIT {$requestData['start']}, {$requestData['length']}
        "
    );


    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['count']) and $getData['count'] > 0) {
        
        foreach($getData['data'] as $key => $value) {

            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["item_date"];
            $allNestedData[] = $value["item_amount"];
            $allNestedData[] = $value["item_description"];
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

/*************************** Expense Reports Non Category ***********************/
if(isset($_GET['page']) and $_GET['page'] == "expenseReportsNonCat") {
    
    $requestData = $_REQUEST;
    $getData = [];
    $search = safe_input($requestData['search']['value']);
    $paymentType = safe_input($_GET['paymentType']);

    $getDateRange = ( isset( $_GET["dateRange"] ) and !empty( $_GET["dateRange"] ) )  ? safe_input( $_GET["dateRange"] ) : "1970-01-01 - " . date("Y-12-31");
    $dateRange = explode(" - ", $getDateRange);

    // List of all columns name
    $columns = array(
        "",
        "salary_month"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectD(
        "SELECT
            count(*) as totalRow
        from {$table_prefeix}salaries as salaries
        where salaries.is_trash = 0 and salaries.salary_type = '{$paymentType}' and salaries.salary_month between '{$dateRange[0]}' and '{$dateRange[1]}'
        "
    )["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }

    $getData = easySelectD(
        "SELECT
            salary_month,
            concat( emp_firstname, ' ', emp_lastname ) as payee_name,
            salary_amount,
            salary_description
        from {$table_prefeix}salaries as salaries
        left join {$table_prefeix}employees on salary_emp_id = emp_id
        where salaries.is_trash = 0 and salaries.salary_type = '{$paymentType}' and salaries.salary_month between '{$dateRange[0]}' and '{$dateRange[1]}' and concat( emp_firstname, ' ', emp_lastname ) like '%{$search}%'  order by salary_id DESC
        "
    );
         
    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['count']) and $getData['count'] > 0) {
        
        foreach($getData['data'] as $key => $value) {

            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] =  date("M, Y", strtotime($value["salary_month"]) ) ;
            $allNestedData[] = $value["payee_name"];
            $allNestedData[] = $value["salary_amount"];
            $allNestedData[] = $value["salary_description"];
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


/*************************** Customer Reports ***********************/
if(isset($_GET['page']) and $_GET['page'] == "employeeReports") {
    
    $requestData = $_REQUEST;
    $getData = [];
    $search = safe_input($requestData['search']['value']);

 
    // List of all columns name
    $columns = array(
        "",
        "abs(emp_PIN)",
        "dep_name",
        "total_salary_added_in_range",
        "total_overtime_added_in_range",
        "total_bonus_added_in_range",
        "",
        "total_salary_paid_in_range",
        "total_overtime_paid_in_range",
        "total_bonus_paid_in_range",
        ""
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
    "table" => "employees",
    "fields" => "count(*) as totalRow",
    "where" => array(
      "is_trash = 0"
    )
  ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }

    $getDateRange = ( isset( $requestData['columns'][1]['search']['value']) and !empty($requestData['columns'][1]['search']['value']) )  ? safe_input($requestData['columns'][1]['search']['value']) : "1970-01-01 - " . date("Y-12-31");
    $dateRange = explode(" - ", $getDateRange);

    /** For dynamic operator searching from datatable */
    $departmentFilter = (array)json_decode($requestData['columns'][2]['search']['value']);
    $empTypeFilter = (array)json_decode($requestData['columns'][3]['search']['value']);

    if( isset($departmentFilter["operator"]) ) {
        $departmentFilter = "and emp_department_id {$departmentFilter["operator"]} '{$departmentFilter["search"]}'";
    } else {
        $departmentFilter = empty($requestData['columns'][2]['search']['value']) ? "" : "and emp_department_id = '{$requestData['columns'][2]['search']['value']}'";
    }

    if( isset($empTypeFilter["operator"]) ) {
        $empTypeFilter = "and emp_type {$empTypeFilter["operator"]} '{$empTypeFilter["search"]}'";
    } else {
        $empTypeFilter = empty($requestData['columns'][3]['search']['value']) ? "" : "and emp_type = '{$requestData['columns'][3]['search']['value']}'";
    }


         
    $getData = easySelectD(
        "select 
                emp_id, dep_name, emp_firstname, emp_lastname, emp_type, emp_PIN, emp_opening_salary, emp_opening_overtime, emp_opening_bonus,
                if(total_salary_added is null, 0, round(total_salary_added, 2)) as total_salary_added,
                if(total_overtime_added is null, 0, round(total_overtime_added, 2)) as total_overtime_added,
                if(total_bonus_added is null, 0, round(total_bonus_added, 2)) as total_bonus_added,
                if(total_salary_added_in_range is null, 0, round(total_salary_added_in_range, 2)) as total_salary_added_in_range,
                if(total_overtime_added_in_range is null, 0, round(total_overtime_added_in_range, 2)) as total_overtime_added_in_range,
                if(total_bonus_added_in_range is null, 0, round(total_bonus_added_in_range, 2)) as total_bonus_added_in_range,

                if(total_salary_paid is null, 0, round(total_salary_paid, 2)) as total_salary_paid,
                if(total_overtime_paid is null, 0, round(total_overtime_paid, 2)) as total_overtime_paid,
                if(total_bonus_paid is null, 0, round(total_bonus_paid, 2)) as total_bonus_paid,
                if(total_salary_paid_in_range is null, 0, round(total_salary_paid_in_range, 2)) as total_salary_paid_in_range,
                if(total_overtime_paid_in_range is null, 0, round(total_overtime_paid_in_range, 2)) as total_overtime_paid_in_range,
                if(total_bonus_paid_in_range is null, 0, round(total_bonus_paid_in_range, 2)) as total_bonus_paid_in_range,
                if(total_loan_adjustment is null, 0, round(total_loan_adjustment, 2)) as total_loan_adjustment
        from {$table_prefeix}employees as employee
        left join {$table_prefeix}emp_department on emp_department_id = dep_id
        left join ( select
                salary_emp_id,
                sum( case when salary_type = 'Salary' then salary_amount end ) as total_salary_added,
                sum( case when salary_type = 'Overtime' then salary_amount end ) as total_overtime_added,
                sum( case when salary_type = 'Bonus' then salary_amount end ) as total_bonus_added,
                sum( case when salary_type = 'Salary' and date(salary_add_on) between '{$dateRange[0]}' and '{$dateRange[1]}' then salary_amount end ) as total_salary_added_in_range,
                sum( case when salary_type = 'Overtime' and date(salary_add_on) between '{$dateRange[0]}' and '{$dateRange[1]}' then salary_amount end ) as total_overtime_added_in_range,
                sum( case when salary_type = 'Bonus' and date(salary_add_on) between '{$dateRange[0]}' and '{$dateRange[1]}' then salary_amount end ) as total_bonus_added_in_range

            from {$table_prefeix}salaries
            where is_trash = 0
            group by salary_emp_id
        ) as salaries on salary_emp_id = emp_id
        left join( select
                payment_items_employee,
                sum( case when payment_items_type = 'Salary' then payment_items_amount end ) as total_salary_paid,
                sum( case when payment_items_type = 'Overtime' then payment_items_amount end ) as total_overtime_paid,
                sum( case when payment_items_type = 'Bonus' then payment_items_amount end ) as total_bonus_paid,
                sum( case when payment_items_type = 'Salary' and payment_items_date between '{$dateRange[0]}' and '{$dateRange[1]}' then payment_items_amount end ) as total_salary_paid_in_range,
                sum( case when payment_items_type = 'Overtime' and payment_items_date between '{$dateRange[0]}' and '{$dateRange[1]}' then payment_items_amount end ) as total_overtime_paid_in_range,
                sum( case when payment_items_type = 'Bonus' and payment_items_date between '{$dateRange[0]}' and '{$dateRange[1]}' then payment_items_amount end ) as total_bonus_paid_in_range
            from {$table_prefeix}payment_items
            where is_trash = 0 
            group by payment_items_employee
        ) as payments on payment_items_employee = emp_id
        left join( select
                loan_installment_provider,
                sum(loan_installment_paying_amount) as total_loan_adjustment
            from {$table_prefeix}loan_installment where is_trash = 0
            group by loan_installment_provider
        ) as loan_installment on loan_installment_provider = emp_id
        where employee.is_trash = 0 $departmentFilter $empTypeFilter and ( emp_firstname like '{$search}%' or emp_PIN = '{$search}')
       
        order by {$columns[$requestData['order'][0]['column']]} {$requestData['order'][0]['dir']}
        LIMIT {$requestData['start']}, {$requestData['length']}
        "
    );

    $totalFilteredRecords = $getData ? $getData["count"] : 0;


    $allData = [];
    // Check if there have more then zero data
    if($getData) {
        
        foreach($getData['data'] as $key => $value) {


            $total_salary_due = ( $value["emp_opening_salary"] + $value["total_salary_added"] ) - ( $value["total_salary_paid"] + $value["total_loan_adjustment"] );
            $total_overtime_due = ( $value["emp_opening_overtime"] + $value["total_overtime_added"] ) - $value["total_overtime_paid"];
            $total_bonus_due = ( $value["emp_opening_bonus"] + $value["total_bonus_added"] ) - $value["total_bonus_paid"];
            $total_wage_due = $total_salary_due + $total_overtime_due + $total_bonus_due;

            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["emp_firstname"] . ' ' . $value["emp_lastname"] . ' (' . $value["emp_PIN"] . ')';
            $allNestedData[] = $value["dep_name"];
            $allNestedData[] = $value["emp_type"];
            $allNestedData[] = $value["total_salary_added_in_range"];
            $allNestedData[] = $value["total_overtime_added_in_range"];
            $allNestedData[] = $value["total_bonus_added_in_range"];
            $allNestedData[] = $value["total_salary_added_in_range"] + $value["total_overtime_added_in_range"] + $value["total_bonus_added_in_range"];

            $allNestedData[] = $value["total_salary_paid_in_range"];
            $allNestedData[] = $value["total_overtime_paid_in_range"];
            $allNestedData[] = $value["total_bonus_paid_in_range"];
            $allNestedData[] = $value["total_salary_paid_in_range"] + $value["total_overtime_paid_in_range"] + $value["total_bonus_paid_in_range"];

            $allNestedData[] = $value["emp_opening_salary"];
            $allNestedData[] = $value["emp_opening_overtime"];
            $allNestedData[] = $value["emp_opening_bonus"];
            $allNestedData[] = $value["emp_opening_salary"] + $value["emp_opening_overtime"] + $value["emp_opening_bonus"];

            $allNestedData[] = $total_salary_due;
            $allNestedData[] = $total_overtime_due;
            $allNestedData[] = $total_bonus_due;
            $allNestedData[] = $value["total_loan_adjustment"];
            $allNestedData[] = $total_wage_due;

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



/*************************** Expired Product Report ***********************/
if(isset($_GET['page']) and $_GET['page'] == "expiredProductList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "product_name",
        "expired_qty",
        "batch_number",
        "expiry_date"

    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectD(
        "SELECT count(*) as totalRow FROM product_base_stock where batch_expiry_date < curdate() "
    )["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if(!empty($requestData["search"]["value"]) ) {  // get data with search
        
 
        $getData = easySelectD("
            SELECT
                product_name,
                round(base_stock_in / base_qty, 2) as expired_qty,
                batch_number,
                pbs.batch_expiry_date as expiry_date
            FROM product_base_stock as pbs
            left join {$table_prefeix}products as product on product.product_id = pbs.product_id
            left join {$table_prefeix}product_batches as product_batches on product_batches.batch_id = pbs.batch_id
            WHERE pbs.batch_expiry_date < curdate() and base_stock_in > 0 and ( 
                product_name like '{$requestData["search"]["value"]}%'
                or batch_number like '{$requestData["search"]["value"]}%'
            )
            order by {$columns[$requestData['order'][0]['column']]} {$requestData['order'][0]['dir']}
            limit {$requestData['start']},{$requestData['length']}
        ");


        $totalFilteredRecords = $getData ? $getData["count"] : 0;

    } else { // Get data withouth search

        $getData = easySelectD("
            SELECT
                product_name,
                round(base_stock_in / base_qty, 2) as expired_qty,
                batch_number,
                warehouse_name,
                pbs.batch_expiry_date as expiry_date
            FROM product_base_stock as pbs
            left join {$table_prefeix}products as product on product.product_id = pbs.product_id
            left join {$table_prefeix}product_batches as product_batches on product_batches.batch_id = pbs.batch_id
            left join {$table_prefeix}warehouses on warehouse_id = warehouse
            WHERE pbs.batch_expiry_date < curdate()
            order by {$columns[$requestData['order'][0]['column']]} {$requestData['order'][0]['dir']}
            limit {$requestData['start']},{$requestData['length']}
        ");

    }

    $allData = [];
    // Check if there have more then zero data
    if($getData) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = "";

            $allNestedData[] = $value["product_name"];
            $allNestedData[] = $value["warehouse_name"];
            $allNestedData[] = $value["expired_qty"];
            $allNestedData[] = $value["batch_number"];
            $allNestedData[] = $value["expiry_date"];
            
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




/*************************** locationWiseSalesReport ***********************/
if(isset($_GET['page']) and $_GET['page'] == "locationWiseSalesReport") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // Count Total recrods
    $totalFilteredRecords = $totalRecords = 0;
    $allData = [];

    if( isset($_GET["pid"]) ) {


        $getData = easySelectA(array(
            "table"     => "product_stock as product_stock",
            "fields"    => "customer_name, sum(stock_item_qty) as total_item_qty, district_name",
            "join"      => array(
                "left join {$table_prefeix}sales on sales_id = stock_sales_id",
                "left join {$table_prefeix}customers on customer_id = sales_customer_id",
                "left join {$table_prefeix}districts on district_id = customer_district"
            ),
            "where"     => array(
                "product_stock.stock_type = 'sale' and product_stock.is_trash = 0 and customer_district" => $_GET["location"],
                " and stock_product_id" => $_GET["pid"]
            ),
            "groupby"   => "sales_customer_id"
        
        ));

        $totalFilteredRecords = $totalRecords = $getData !== false ? $getData["count"] : 0;

        // Check if there have more then zero data
        if(isset($getData['count']) and $getData['count'] > 0) {
            
            foreach($getData['data'] as $key => $value) {
                
                $allNestedData = [];
                $allNestedData[] = "";
                $allNestedData[] = $value["customer_name"];
                $allNestedData[] = $value["district_name"];
                $allNestedData[] = $value["total_item_qty"];

                $allData[] = $allNestedData;
            }
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


/*************************** Product Ledger ***********************/
if(isset($_GET['page']) and $_GET['page'] == "productLedger") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // Count Total recrods
    $totalFilteredRecords = $totalRecords = 0;
    $allData = [];

    if( isset($_GET["pid"]) ) {

        $pid = safe_input($_GET["pid"]);
        $wid = safe_input($_GET["wid"]);

        $warehouse_filter = "";
        if( !empty($wid) ) {
            $warehouse_filter = " AND stock_warehouse_id = '{$wid}'";
        }

        easySelectD("SELECT @balance := 0;");

        $getData = easySelectD("
            select entry_date, record_id, reference, record_user_id, reference_link, description, stock_in, stock_out, @balance := ( @balance + stock_in ) - stock_out as balance, emp_firstname, emp_lastname from
            (   
                SELECT 
                    1 as sortby,
                    concat(stock_entry_date, ' ', DATE_FORMAT(stock_item_add_on, '%H:%i:%s') ) as entry_date,
                    concat('STOCK/ENTRY/', stock_se_id) as reference,
                    stock_se_id as record_id,
                    stock_created_by as record_user_id,
                    '/xhr/?module=stock-management&page=viewStockEntryProduct&id=' as reference_link,
                    combine_description('Initial Stock Entry', se_note) as description,
                    stock_item_qty as stock_in,
                    0 as stock_out
                FROM {$table_prefeix}product_stock as initial
                LEFT JOIN {$table_prefeix}stock_entries on se_id = stock_se_id
                    WHERE initial.is_trash = 0 
                    AND initial.stock_type = 'initial'
                    AND initial.stock_product_id = '{$pid}'
                    {$warehouse_filter}
                    GROUP BY stock_id
                UNION ALL
                SELECT 
                    2 as sortby,
                    concat(stock_entry_date, ' ', DATE_FORMAT(stock_item_add_on, '%H:%i:%s') ) as entry_date,
                    if(purchase_reference is null or purchase_reference = '', concat('Purchase/', stock_purchase_id), purchase_reference ) as reference,
                    stock_purchase_id as record_id,
                    stock_created_by as record_user_id,
                    '/xhr/?module=stock-management&page=viewPurchasedProduct&id=' as reference_link,
                    combine_description('Purchase', purchase_note) as description,
                    stock_item_qty as stock_in,
                    0 as stock_out
                FROM {$table_prefeix}product_stock as purchase
                LEFT JOIN {$table_prefeix}purchases on purchase_id = stock_purchase_id
                    WHERE purchase.is_trash = 0 
                    AND purchase.stock_type = 'purchase'
                    AND purchase.stock_product_id = '{$pid}'
                    {$warehouse_filter}
                    GROUP BY stock_id
                UNION ALL
                SELECT 
                    3 as sortby,
                    concat(stock_entry_date, ' ', DATE_FORMAT(stock_item_add_on, '%H:%i:%s') ) as entry_date,
                    if(purchase_reference is null or purchase_reference = '', concat('Purchase/', stock_purchase_id), purchase_reference ) as reference,
                    stock_purchase_id as record_id,
                    stock_created_by as record_user_id,
                    '/xhr/?module=stock-management&page=viewPurchasedProduct&id=' as reference_link,
                    combine_description('Purchase Return', purchase_note) as description,
                    0 as stock_in,
                    stock_item_qty as stock_out
                FROM {$table_prefeix}product_stock as purchase_return
                LEFT JOIN {$table_prefeix}purchases on purchase_id = stock_purchase_id
                    WHERE purchase_return.is_trash = 0 
                    AND purchase_return.stock_type = 'purchase-return'
                    AND purchase_return.stock_product_id = '{$pid}'
                    {$warehouse_filter}
                    GROUP BY stock_id
                UNION ALL
                SELECT 
                    4 as sortby,
                    concat(stock_entry_date, ' ', DATE_FORMAT(stock_item_add_on, '%H:%i:%s') ) as entry_date,
                    sales_reference as reference,
                    stock_sales_id as record_id,
                    stock_created_by as record_user_id,
                    '/xhr/?module=reports&page=showInvoiceProducts&id=' as reference_link,
                    combine_description('Sale', sales_note) as description,
                    0 as stock_in,
                    stock_item_qty as stock_out
                FROM {$table_prefeix}product_stock as sales
                LEFT JOIN {$table_prefeix}sales on sales_id = stock_sales_id
                    WHERE sales.is_trash = 0 
                    AND sales.stock_type = 'sale'
                    AND sales.stock_product_id = '{$pid}'
                    {$warehouse_filter}
                    GROUP BY stock_id
                UNION ALL
                SELECT 
                    5 as sortby,
                    concat(stock_entry_date, ' ', DATE_FORMAT(stock_item_add_on, '%H:%i:%s') ) as entry_date,
                    sales_reference as reference,
                    stock_sales_id as record_id,
                    stock_created_by as record_user_id,
                    '/xhr/?module=reports&page=showInvoiceProducts&id=' as reference_link,
                    combine_description('Wastage Product Sale', sales_note) as description,
                    0 as stock_in,
                    stock_item_qty as stock_out
                FROM {$table_prefeix}product_stock as wastage_sale
                LEFT JOIN {$table_prefeix}sales on sales_id = stock_sales_id
                    WHERE wastage_sale.is_trash = 0 
                    AND wastage_sale.stock_type = 'wastage-sale'
                    AND wastage_sale.stock_product_id = '{$pid}'
                    {$warehouse_filter}
                    GROUP BY stock_id
                UNION ALL
                SELECT 
                    6 as sortby,
                    concat(stock_entry_date, ' ', DATE_FORMAT(stock_item_add_on, '%H:%i:%s') ) as entry_date,
                    sales_reference as reference,
                    stock_sales_id as record_id,
                    stock_created_by as record_user_id,
                    '/xhr/?module=reports&page=showInvoiceProducts&id=' as reference_link,
                    combine_description('Sale Return', sales_note) as description,
                    stock_item_qty as stock_in,
                    0 as stock_out
                FROM {$table_prefeix}product_stock as sale_return
                LEFT JOIN {$table_prefeix}sales on sales_id = stock_sales_id
                    WHERE sale_return.is_trash = 0 
                    AND sale_return.stock_type = 'sale-return'
                    AND sale_return.stock_product_id = '{$pid}'
                    {$warehouse_filter}
                    GROUP BY stock_id
                UNION ALL
                SELECT 
                    7 as sortby,
                    concat(stock_entry_date, ' ', DATE_FORMAT(stock_item_add_on, '%H:%i:%s') ) as entry_date,
                    if(stock_transfer_reference is null or stock_transfer_reference = '', concat('Transfer/', stock_transfer_in.stock_transfer_id), stock_transfer_reference ) as reference,
                    stock_transfer_in.stock_transfer_id as record_id,
                    stock_created_by as record_user_id,
                    '/xhr/?module=stock-management&page=viewTransferedProduct&id=' as reference_link,
                    concat('Stock Transfer in from ', warehouse_name) as description,
                    stock_item_qty as stock_in,
                    0 as stock_out
                FROM {$table_prefeix}product_stock as stock_transfer_in
                LEFT JOIN {$table_prefeix}stock_transfer as stock_transfer on stock_transfer.stock_transfer_id = stock_transfer_in.stock_transfer_id
                LEFT JOIN {$table_prefeix}warehouses on warehouse_id = stock_transfer_from_warehouse
                    WHERE stock_transfer_in.is_trash = 0 
                    AND stock_transfer_in.stock_type = 'transfer-in'
                    AND stock_transfer_in.stock_product_id = '{$pid}'
                    {$warehouse_filter}
                    GROUP BY stock_id
                UNION ALL
                SELECT 
                    8 as sortby,
                    concat(stock_entry_date, ' ', DATE_FORMAT(stock_item_add_on, '%H:%i:%s') ) as entry_date,
                    if(stock_transfer_reference is null or stock_transfer_reference = '', concat('Transfer/', stock_transfer_out.stock_transfer_id), stock_transfer_reference ) as reference,
                    stock_transfer_out.stock_transfer_id as record_id,
                    stock_created_by as record_user_id,
                    '/xhr/?module=stock-management&page=viewTransferedProduct&id=' as reference_link,
                    concat('Stock Transfer out to ', warehouse_name) as description,
                    0 as stock_in,
                    stock_item_qty as stock_out
                FROM {$table_prefeix}product_stock as stock_transfer_out
                LEFT JOIN {$table_prefeix}stock_transfer as stock_transfer on stock_transfer.stock_transfer_id = stock_transfer_out.stock_transfer_id
                LEFT JOIN {$table_prefeix}warehouses on warehouse_id = stock_transfer_to_warehouse
                    WHERE stock_transfer_out.is_trash = 0 
                    AND stock_transfer_out.stock_type = 'transfer-out'
                    AND stock_transfer_out.stock_product_id = '{$pid}'
                    {$warehouse_filter}
                    GROUP BY stock_id
                UNION ALL
                SELECT 
                    9 as sortby,
                    concat(stock_entry_date, ' ', DATE_FORMAT(stock_item_add_on, '%H:%i:%s') ) as entry_date,
                    concat('Specimen/', stock_sc_id) as reference,
                    stock_sc_id as record_id,
                    stock_created_by as record_user_id,
                    '/invoice-print/?autoPrint=false&invoiceType=scpecimenCopy&id=' as reference_link,
                    'Spcimen Copy' as description,
                    0 as stock_in,
                    stock_item_qty as stock_out
                FROM {$table_prefeix}product_stock as spcimen_copy
                LEFT JOIN {$table_prefeix}specimen_copies on sc_id = stock_sc_id
                    WHERE spcimen_copy.is_trash = 0 
                    AND spcimen_copy.stock_type = 'specimen-copy'
                    AND spcimen_copy.stock_product_id = '{$pid}'
                    {$warehouse_filter}
                    GROUP BY stock_id
                UNION ALL
                SELECT 
                    10 as sortby,
                    concat(stock_entry_date, ' ', DATE_FORMAT(stock_item_add_on, '%H:%i:%s') ) as entry_date,
                    concat('Specimen/', stock_sc_id) as reference,
                    stock_sc_id as record_id,
                    stock_created_by as record_user_id,
                    '/invoice-print/?autoPrint=false&invoiceType=scpecimenCopy&id=' as reference_link,
                    'Spcimen Copy' as description,
                    0 as stock_in,
                    stock_item_qty as stock_out
                FROM {$table_prefeix}product_stock as spcimen_copy_return
                LEFT JOIN {$table_prefeix}specimen_copies on sc_id = stock_sc_id
                    WHERE spcimen_copy_return.is_trash = 0 
                    AND spcimen_copy_return.stock_type = 'specimen-copy-return'
                    AND spcimen_copy_return.stock_product_id = '{$pid}'
                    {$warehouse_filter}
                    GROUP BY stock_id
                UNION ALL
                SELECT 
                    11 as sortby,
                    concat(stock_entry_date, ' ', DATE_FORMAT(stock_item_add_on, '%H:%i:%s') ) as entry_date,
                    concat('STOCK/ENTRY/', stock_se_id) as reference,
                    stock_se_id as record_id,
                    stock_created_by as record_user_id,
                    '/xhr/?module=stock-management&page=viewStockEntryProduct&id=' as reference_link,
                    combine_description('Adjustment', se_note) as description,
                    if(stock_item_qty < 0, 0, stock_item_qty) as stock_in,
                    if(stock_item_qty < 0, abs(stock_item_qty), 0) as stock_out
                FROM {$table_prefeix}product_stock as initial
                LEFT JOIN {$table_prefeix}stock_entries on se_id = stock_se_id
                    WHERE initial.is_trash = 0 
                    AND initial.stock_type = 'adjustment'
                    AND initial.stock_product_id = '{$pid}'
                    {$warehouse_filter}
                    GROUP BY stock_id
                

            ) as get_data
            LEFT JOIN {$table_prefeix}users as user on user.user_id = get_data.record_user_id
            LEFT JOIN {$table_prefeix}employees on emp_id = user_emp_id
            order by entry_date, sortby ASC
        ");



        // ('initial', 'sale-production', 'sale-processing', 'sale', 'sale-order', 'wastage-sale', 'sale-return', 'purchase', 'purchase-order', 
        //             'purchase-return', 'transfer-in', 'transfer-out', 'specimen-copy', 'specimen-copy-return', 'undeclared') default 'undeclared',
            

        //print_r($getData);

        $totalFilteredRecords = $totalRecords = $getData !== false ? $getData["count"] : 0;

        // Check if there have more then zero data
        if(isset($getData['count']) and $getData['count'] > 0) {
            
            foreach($getData['data'] as $key => $value) {

                //, record_id, reference, reference_link, description, stock_in, stock_out, @balance := ( @balance + stock_in ) - stock_out as balance from
                
                $allNestedData = [];
                $allNestedData[] = "";
                $allNestedData[] = $value["entry_date"];
                $allNestedData[] = "<a data-toggle='modal' data-target='#modalDefault' href='" . full_website_address() . "{$value['reference_link']}{$value['record_id']}'>{$value['reference']}</a>";
                $allNestedData[] = $value["description"] . " <small>by- {$value["emp_firstname"]} {$value["emp_lastname"]}</small>";
                $allNestedData[] = $value["stock_in"];
                $allNestedData[] = $value["stock_out"];
                $allNestedData[] = $value["balance"];

                $allData[] = $allNestedData;
            }
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