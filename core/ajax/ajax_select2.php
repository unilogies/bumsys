<?php

// Select2 employee List
if(isset($_GET['page']) and $_GET['page'] == "employeeList") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectEmployee = easySelect(
        "employees",
        "emp_id, emp_PIN, emp_firstname, emp_lastname, emp_positions",
        array(),
        array (
            "is_trash = 0 and emp_type != 'Past'",
            " AND (emp_firstname LIKE '%". safe_input($search) ."%'",
            " or emp_lastname LIKE" => "%{$search}%",
            " or emp_PIN" => "{$search}",
            ")"
        ),
        array(),
        array (
            "start" => 0,
            "length" => 9
        )
    );

    $select2Json = [];

    if($selectEmployee)
    foreach($selectEmployee["data"] as $employees) {
        $select2Json[] = array(
            'id' => $employees['emp_id'],
            'text' => $employees['emp_PIN'] . '. ' . $employees['emp_firstname'] . ' ' . $employees['emp_lastname'] . ' (' . $employees['emp_positions'] . ')'
        );
    }

    echo html_entity_decode(json_encode($select2Json));

}


// Select2 employee List
if(isset($_GET['page']) and $_GET['page'] == "MRList") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectEmployee = easySelect(
        "employees",
        "emp_id, emp_PIN, emp_firstname, emp_lastname, emp_positions",
        array(),
        array (
            "is_trash = 0 and emp_type != 'Past'",
            " AND (emp_firstname LIKE '%". safe_input($search) ."%'",
            " or emp_lastname LIKE" => "%{$search}%",
            " or emp_PIN" => "{$search}",
            ")"
        ),
        array(),
        array (
            "start" => 0,
            "length" => 30
        )
    );

    $select2Json = [];

    if($selectEmployee)
    foreach($selectEmployee["data"] as $employees) {
        $select2Json[] = array(
            'id' => $employees['emp_id'],
            'text' => $employees['emp_PIN'] . '. ' . $employees['emp_firstname'] . ' ' . $employees['emp_lastname'] . ' (' . $employees['emp_positions'] . ')'
        );
    }

    echo html_entity_decode(json_encode($select2Json));

}

// Select2 employee List All
if(isset($_GET['page']) and $_GET['page'] == "employeeListAll") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectEmployee = easySelect(
        "employees",
        "emp_id, emp_PIN, emp_firstname, emp_lastname, emp_positions",
        array(),
        array (
            "is_trash = 0 and emp_firstname LIKE '%". safe_input($search) ."%'",
            " or emp_lastname LIKE" => "%{$search}%",
            " or emp_PIN" => "{$search}"
        ),
        array(),
        array (
            "start" => 0,
            "length" => 9
        )
    );

    $select2Json = [];

    if($selectEmployee)
    foreach($selectEmployee["data"] as $employees) {
        $select2Json[] = array(
            'id' => $employees['emp_id'],
            'text' => $employees['emp_PIN'] . '. ' . $employees['emp_firstname'] . ' ' . $employees['emp_lastname'] . ' (' . $employees['emp_positions'] . ')'
        );
    }
    

    echo html_entity_decode(json_encode($select2Json));
}


// Select2 employee group List
if(isset($_GET['page']) and $_GET['page'] == "empGroupList") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectEmpGroup = easySelect(
        "emp_group",
        "group_id, group_name",
        array(),
        array (
            "is_trash = 0 and group_name LIKE '%". safe_input($search) ."%'"
        ),
        array(),
        array (
            "start" => 0,
            "length" => 9
        )
    );

    $select2Json = [];
    if($selectEmpGroup) {
        foreach($selectEmpGroup["data"] as $empGroup) {
            $select2Json[] = array(
                'id' => $empGroup['group_id'],
                'text' => $empGroup['group_name']
            );
        }
    }
    

    echo html_entity_decode(json_encode($select2Json));
    
}


// Select2 User group List
if(isset($_GET['page']) and $_GET['page'] == "userGroupList") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectEmpGroup = easySelect(
        "user_group",
        "group_id, group_name",
        array(),
        array (
            "is_trash = 0 and group_name LIKE '%". safe_input($search) ."%'"
        ),
        array(),
        array (
            "start" => 0,
            "length" => 9
        )
    );

    $select2Json = [];
    if($selectEmpGroup) {
        foreach($selectEmpGroup["data"] as $empGroup) {
            $select2Json[] = array(
                'id' => $empGroup['group_id'],
                'text' => $empGroup['group_name']
            );
        }
    }
    

    echo html_entity_decode(json_encode($select2Json));
}


// Select2 Shop List
if(isset($_GET['page']) and $_GET['page'] == "shopList") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectShop = easySelect(
        "shops",
        "shop_id, shop_name",
        array(),
        array (
            "is_trash = 0 and shop_name LIKE '%". safe_input($search) ."%'"
        ),
        array(),
        array (
            "start" => 0,
            "length" => 9
        )
    );

    $select2Json = [];
    if($selectShop)
    foreach($selectShop["data"] as $shops) {
        $select2Json[] = array(
            'id' => $shops['shop_id'],
            'text' => $shops['shop_name']
        );
    }

    echo html_entity_decode(json_encode($select2Json));
}


// Select2 product Category List
if(isset($_GET['page']) and $_GET['page'] == "productCategoryList") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectCategory = easySelect(
        "product_category",
        "category_id, category_name",
        array(),
        array (
            "is_trash = 0 and category_name LIKE '%". safe_input($search) ."%'"
        ),
        array(),
        array (
            "start" => 0,
            "length" => 25
        )
    );

    $select2Json = [];
    
    if($selectCategory) {
        foreach($selectCategory["data"] as $productCategory) {
            $select2Json[] = array(
                'id' => $productCategory['category_id'],
                'text' => $productCategory['category_name']
            );
        }
    }
    

    echo html_entity_decode(json_encode($select2Json));
}


// Select2 item units List
if(isset($_GET['page']) and $_GET['page'] == "itemUnitList") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectUnit = easySelect(
        "product_units",
        "unit_id, unit_name",
        array(),
        array (
            "is_trash = 0 and unit_name LIKE '%". safe_input($search) ."%'"
        ),
        array(),
        array (
            "start" => 0,
            "length" => 9
        )
    );

    $select2Json = [];
    if($selectUnit)
    foreach($selectUnit["data"] as $itemUnit) {
        $select2Json[] = array(
            'id' => $itemUnit['unit_id'],
            'text' => $itemUnit['unit_name']
        );
    }

    echo html_entity_decode(json_encode($select2Json));
}

// Select2 item units List
if(isset($_GET['page']) and $_GET['page'] == "itemUnitListByName") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectUnit = easySelect(
        "product_units",
        "unit_name",
        array(),
        array (
            "is_trash = 0 and unit_name LIKE '%". safe_input($search) ."%'"
        ),
        array(),
        array (
            "start" => 0,
            "length" => 9
        )
    );

    $select2Json = [];
    if($selectUnit)
    foreach($selectUnit["data"] as $itemUnit) {
        $select2Json[] = array(
            'id' => $itemUnit['unit_name'],
            'text' => $itemUnit['unit_name']
        );
    }

    echo html_entity_decode(json_encode($select2Json));
}

// Select2 item units List
if(isset($_GET['page']) and $_GET['page'] == "productBrandList") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectProductBrand = easySelect(
        "product_brands",
        "brand_id, brand_name",
        array(),
        array (
            "is_trash = 0 and brand_name LIKE '%". safe_input($search) ."%'"
        ),
        array(),
        array (
            "start" => 0,
            "length" => 9
        )
    );

    $select2Json = [];
    if($selectProductBrand)
    foreach($selectProductBrand["data"] as $brand) {
        $select2Json[] = array(
            'id' => $brand['brand_id'],
            'text' => $brand['brand_name']
        );
    }

    echo html_entity_decode(json_encode($select2Json));
}


// Select2 Generic List
if(isset($_GET['page']) and $_GET['page'] == "productGenericList") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectProductBrand = easySelect(
        "product_generic",
        "generic_name, generic_name",
        array(),
        array (
            "is_trash = 0 and generic_name LIKE '%". safe_input($search) ."%'"
        ),
        array(),
        array (
            "start" => 0,
            "length" => 9
        )
    );

    $select2Json = [];
    if($selectProductBrand)
    foreach($selectProductBrand["data"] as $brand) {
        $select2Json[] = array(
            'id' => $brand['generic_name'],
            'text' => $brand['generic_name']
        );
    }

    echo html_entity_decode(json_encode($select2Json));
}


// Select2 supplierList and Binders List
if(isset($_GET['page']) and $_GET['page'] == "supplierBinderList") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectSupplier = easySelect(
        "companies",
        "company_id, company_name",
        array(),
        array (
            "is_trash = 0 and company_name LIKE '%". safe_input($search) ."%'"
        ),
        array(),
        array (
            "start" => 0,
            "length" => 9
        )
    );
  
    $select2Json = [];
    if($selectSupplier)
    foreach($selectSupplier["data"] as $supplier) {
        $select2Json[] = array(
            'id' => $supplier['company_id'],
            'text' => $supplier['company_name']
        );
    }

    echo html_entity_decode(json_encode($select2Json));
}


// Select2 product List
if(isset($_GET['page']) and $_GET['page'] == "productListForPos") {
    
    $search = isset($_GET['q']) ? safe_input($_GET['q']) : "";
    $warehouseFilter = isset($_GET['wid']) ? " where warehouse = '". safe_input($_GET['wid']) . "'" : "";

   // $search = preg_replace("/[\s_-]/", "[. -_]", $search);

    $searchQuery = !empty($search) ? " AND (product_name like '$search%' OR product_edition = '$search' OR product_group LiKE '$search%' OR product_code LiKE '$search%' )" : "";

    $selectProduct = easySelectA(array(
        "table"     => "products as product",
        "fields"    => "product.product_id as product_id, brand_name, concat(product_name, ' ', if(product_group is null, '', product_group) ) as product_name, 
                        round( COALESCE(purchase_price, product_purchase_price), 2) as product_purchase_price, 
                        round( COALESCE(sale_price, product_sale_price), 2) as product_sale_price, 
                        
                        product_generic, concat( if(stock_in is null, 0, round(stock_in, 2)), ' ', if(product_unit is null, '', product_unit) ) as stock_in",
        "join"      => array(
            "left join (select -- make inner join to show only stock in product
                            vp_id,
                            sum(base_stock_in/base_qty) as stock_in
                        from product_base_stock
                        {$warehouseFilter}
                        group by vp_id
            ) as pbs on pbs.vp_id = product.product_id",
            // Because Of we have different price based on shop
            "left join (SELECT
                            product_id,
                            purchase_price,
                            sale_price
                FROM {$table_prefix}product_price    
                WHERE shop_id = '{$_SESSION['sid']}'
            ) as product_price on  product_price.product_id = product.product_id ",
            "left join {$table_prefix}product_brands on brand_id = product_brand_id"
        ),
        "where"     => array (
            "product.is_trash = 0 and is_disabled = 0 and product_parent_id is null {$searchQuery}"
        ),
        "orderby"   => array(
            "product_id"    => "DESC"
        ),
        "limit" => array(
            "start" => 0,
            "length" => 50
        )

    ));
    
  
    $select2Json = [];
    if($selectProduct !== false) {

        foreach($selectProduct["data"] as $product) {
            $select2Json[] = array(
                'id' => $product['product_id'],
                'text' => array(
                    $product['product_name'],
                    $product['product_generic'],
                    $product['product_purchase_price'],
                    $product['product_sale_price'],
                    $product['stock_in'],
                    $product['brand_name']
                )
            );
        }

    }
   

    echo json_encode($select2Json);

}


// Select2 product List
if(isset($_GET['page']) and $_GET['page'] == "productList") {
    
    $search = isset($_GET['q']) ? safe_input($_GET['q']) : "";

   // $search = preg_replace("/[\s_-]/", "[. -_]", $search);

    $searchQuery = !empty($search) ? " and product_name like '$search%' OR product_edition = '$search' OR product_group LiKE '$search%' OR product_code LiKE '$search%'" : "";

    $selectProduct = easySelectA(array(
        "table"     => "products as product",
        "fields"    => "product.product_id as product_id, brand_name, concat(product_name, ' ', if(product_group is null, '', product_group) ) as product_name, 

                        round( COALESCE(purchase_price, product_purchase_price), 2) as product_purchase_price, 
                        round( COALESCE(sale_price, product_sale_price), 2) as product_sale_price, 
                        
                        product_generic, if(stock_in is null, 0, round(stock_in, 2)) as stock_in",
        "join"      => array(
            "left join (select
                            vp_id,
                            sum(base_stock_in/base_qty) as stock_in
                        from product_base_stock
                        group by vp_id
            ) as pbs on pbs.vp_id = product.product_id",
            // Because Of we have different price based on shop
            "left join (SELECT
                            product_id,
                            purchase_price,
                            sale_price
                FROM {$table_prefix}product_price    
                WHERE shop_id = '{$_SESSION['sid']}'
            ) as product_price on product_price.product_id = product.product_id ",
            "left join {$table_prefix}product_brands on brand_id = product_brand_id"
        ),
        "where"     => array (
            "product.is_trash = 0 and is_disabled = 0 and product_parent_id is null {$searchQuery}"
        ),
        "orderby"   => array(
            "product.product_id"    => "DESC"
        ),
        "limit" => array(
            "start" => 0,
            "length" => 50
        )

    ));
  
    $select2Json = [];
    if($selectProduct)
    foreach($selectProduct["data"] as $product) {
        $select2Json[] = array(
            'id' => $product['product_id'],
            'text' => array(
                $product['product_name'],
                $product['product_generic'],
                $product['product_purchase_price'],
                $product['product_sale_price'],
                $product['stock_in'],
                $product['brand_name']
            )
        );
    }

    // Bandage roll 4&quot; (bsmi)
    echo json_encode($select2Json);

}


// Select2 product List
if(isset($_GET['page']) and $_GET['page'] == "productListAll") {
    
    $search = isset($_GET['q']) ? safe_input($_GET['q']) : "";

   //$search = preg_replace("/[\s_-]/", "[. -_]", $search);

    $searchQuery = !empty($search) ? " and product_name like '$search%' OR product_edition = '$search' OR product_group LiKE '$search%' OR product_code LiKE '$search%'" : "";

    $selectProduct = easySelectA(array(
        "table"     => "products as product",
        "fields"    => "product.product_id as product_id, concat(product_name, ' ', if(product_group is null, '', product_group) ) as product_name, if(product_unit is null, '', product_unit) as product_unit",
        "where"     => array (
            "product.is_trash = 0 and is_disabled = 0 {$searchQuery}"
        ),
        "orderby"   => array(
            "product_id"    => "DESC"
        ),
        "limit" => array(
            "start" => 0,
            "length" => 50
        )

    ));
  
    $select2Json = [];
    if($selectProduct)
    foreach($selectProduct["data"] as $product) {
        $select2Json[] = array(
            'id' => $product['product_id'],
            'text' => $product['product_name'] . " " . $product['product_unit']
        );
    }

    echo json_encode($select2Json);
    
}


// Select2 Customer List
if(isset($_GET['page']) and $_GET['page'] == "customerList") {
    
    $search = isset($_GET['q']) ? safe_input($_GET['q'])  : "";

    $selectCustomer = easySelectA(array(
        "table"     => "customers as customer",
        "fields"    => "customer_id, customer_name, upazila_name, district_name",
        "join"      => array(
            "left join {$table_prefix}upazilas on customer_upazila = upazila_id",
            "left join {$table_prefix}districts on customer_district = district_id"
        ),
        "where"     => array(
            "customer.is_trash = 0",
            " and ( customer.customer_name LIKE '%{$search}%'",
            " or customer_phone LIKE" =>  "{$search}%",
            " or district_name LIKE" =>  "{$search}%",
            " or customer_id" =>  $search,
            ")"
        ),
        "limit"     => array(
            "start" => 0,
            "length" => 30
        )
    ));

    $select2Json = [];
    if($selectCustomer)
    foreach($selectCustomer["data"] as $customers) {
        $select2Json[] = array(
            'id' => $customers['customer_id'],
            'text' => "{$customers['customer_name']} ({$customers['customer_id']}), {$customers['upazila_name']}, {$customers['district_name']}"
        );
    }

    echo html_entity_decode(json_encode($select2Json));

}


// Select2 Payment Category List
if(isset($_GET['page']) and $_GET['page'] == "paymentCategoryList") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectPaymentCategory = easySelect(
        "payments_categories",
        "*",
        array(),
        array (
            "is_trash = 0 and payment_category_name LIKE" =>  "%{$search}%"
        ),
        array(),
        array (
            "start" => 0,
            "length" => 9
        )
    );

   // print_r($selectPaymentCategory);

    $select2Json = [];
    if($selectPaymentCategory) {

        foreach($selectPaymentCategory["data"] as $PaymentCategory) {
            $select2Json[] = array(
                'id' => $PaymentCategory['payment_category_id'],
                'text' => $PaymentCategory['payment_category_name']
            );
        }
    }
    
    echo html_entity_decode(json_encode($select2Json));

}

// Select2 Shop Payment Category List
if(isset($_GET['page']) and $_GET['page'] == "shopPaymentCategoryList") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectPaymentCategory = easySelect(
        "payments_categories",
        "*",
        array(),
        array (
            "is_trash = 0 and payment_category_shop_id"  => $_SESSION["sid"],
            " AND payment_category_name LIKE" =>  "%{$search}%"
        ),
        array(),
        array (
            "start" => 0,
            "length" => 9
        )
    );


    $select2Json = [];
    if($selectPaymentCategory) {

        foreach($selectPaymentCategory["data"] as $PaymentCategory) {
            $select2Json[] = array(
                'id' => $PaymentCategory['payment_category_id'],
                'text' => $PaymentCategory['payment_category_name']
            );
        }
    }
    
    echo html_entity_decode(json_encode($select2Json));

}


// Select2 Payment Company List
if(isset($_GET['page']) and $_GET['page'] == "CompanyList") {
    
    $companyType = isset($_GET["type"]) ? $_GET["type"] : "";
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectCompany = easySelect(
        "companies",
        "company_id, company_name, company_phone, company_type",
        array(),
        array (
            "is_trash = 0 and (company_name LIKE '%". safe_input($search) ."%'",
            " OR company_phone LIKE" =>  "%{$search}%",
            " OR company_id" =>  $search,
            ")",
            " AND company_type" => $companyType
        ),
        array(),
        array (
            "start" => 0,
            "length" => 9
        )
    );

    $select2Json = [];
    if($selectCompany)
    foreach($selectCompany["data"] as $company) {
        $select2Json[] = array(
            'id' => $company['company_id'],
            'text' => $company['company_name'] . " ({$company['company_id']})"
        );
    }

    echo html_entity_decode(json_encode($select2Json));
    
}


// Select2 Disctrict List
if(isset($_GET['page']) and $_GET['page'] == "instituteList") {

    $upazilaName = isset($_GET['q']) ? safe_input($_GET['q']) : "";

    $selectUpazila = easySelectA(array(
        "table" => "institute",
        "fields" => "institute_id, institute_name, upazila_name, district_name",
        "where" => array(
            "institute_name LIKE '%{$upazilaName}%'",
            " and institute_upazila"   => isset($_GET["upazila_id"]) ? $_GET["upazila_id"] : ""
        ),
        "join"  => array(
            "left join {$table_prefix}upazilas on institute_upazila = upazila_id",
            "left join {$table_prefix}districts on upazila_district_id = district_id"
        )
    ));

    $select2Json = [];
    if($selectUpazila) {
        foreach($selectUpazila["data"] as $upazila) {
            $select2Json[] = array(
                'id' => $upazila['institute_id'],
                'text' => "{$upazila['institute_name']}, {$upazila['upazila_name']}, {$upazila['district_name']}"
            );
        }
    }

    echo html_entity_decode(json_encode($select2Json));
}

// Select2 Disctrict List
if(isset($_GET['page']) and $_GET['page'] == "upazilaList") {

    $upazilaName = isset($_GET['q']) ? safe_input($_GET['q']) : "";

    $selectUpazila = easySelectA(array(
        "table" => "upazilas",
        "where" => array(
            "(upazila_name LIKE '%{$upazilaName}%'",
            " or upazila_bn_name LIKE" => "%{$upazilaName}%",
            ")",
            " and upazila_district_id"   => isset($_GET["district_id"]) ? $_GET["district_id"] : ""
        )
    ));

    $select2Json = [];
    if($selectUpazila)
    foreach($selectUpazila["data"] as $upazila) {
        $select2Json[] = array(
            'id' => $upazila['upazila_id'],
            'text' => $upazila['upazila_name']
        );
    }

    echo html_entity_decode(json_encode($select2Json));
}

// Select2 Disctrict List
if(isset($_GET['page']) and $_GET['page'] == "districtList") {

    $districtName = isset($_GET['q']) ? safe_input($_GET['q']) : "";

    $selectDistrict = easySelectA(array(
        "table" => "districts",
        "where" => array(
            "(district_name LIKE '%". $districtName ."%'",
            " or district_bn_name LIKE" => "%{$districtName}%",
            ")",
            " and district_division_id"   => isset($_GET["division_id"]) ? $_GET["division_id"] : ""
        )
    ));

    $select2Json = [];
    if($selectDistrict)
    foreach($selectDistrict["data"] as $district) {
        $select2Json[] = array(
            'id' => $district['district_id'],
            'text' => $district['district_name']
        );
    }

    echo html_entity_decode(json_encode($select2Json));
}

// Select2 Division List
if(isset($_GET['page']) and $_GET['page'] == "divisionList") {

    $divisionName = isset($_GET['q']) ? $_GET['q'] : "";

    $selectDivision = easySelect(
        "divisions",
        "*",
        array(),
        array (
            "division_name LIKE" =>  "%{$divisionName}%",
            " or division_bn_name LIKE" =>  "%{$divisionName}%"
        )
    );

    $select2Json = [];
    if($selectDivision)
    foreach($selectDivision["data"] as $division) {
        $select2Json[] = array(
            'id' => $division['division_id'],
            'text' => $division['division_name']
        );
    }

    echo html_entity_decode(json_encode($select2Json));
}


// Select2 Journal List
if(isset($_GET['page']) and $_GET['page'] == "journalList") {

    $journalName = isset($_GET['q']) ? $_GET['q'] : "";

    $selectJournal = easySelect(
        "journals",
        "*",
        array(),
        array (
            "is_trash = 0 and journals_name LIKE '". safe_input($journalName) ."%'"
        )
    );

    $select2Json = [];
    if($selectJournal)
    foreach($selectJournal["data"] as $journals) {
        $select2Json[] = array(
            'id' => $journals['journals_id'],
            'text' => $journals['journals_name']
        );
    }

    echo html_entity_decode(json_encode($select2Json));
}

// Select2 Journal List
if(isset($_GET['page']) and $_GET['page'] == "tariffCharges") {

    $tcName = isset($_GET['q']) ? $_GET['q'] : "";

    $selectTC = easySelectA(array(
        "table" => "tariff_and_charges",
        "where" => array(
            "tc_name"   => $tcName
        )
    ));

    $select2Json = [];
    if($selectTC)
    foreach($selectTC["data"] as $tc) {
        $select2Json[] = array(
            'id' => $tc['tc_name'] . ": " . $tc['tc_value'],
            'text' => $tc['tc_name'] . ": " . $tc['tc_value']
        );
    }

    echo html_entity_decode(json_encode($select2Json));
}


// personList
if(isset($_GET['page']) and $_GET['page'] == "personList") {

    $personSearch = isset($_GET['q']) ? $_GET['q'] : "";

    $selectPerson = easySelectA(array(
        "table" => "persons as person",
        "fields"    => "person_id, person_full_name, institute_name",
        "join"  => array(
            "left join {$table_prefix}institute on person_institute = institute_id"
        ),
        "where" => array(
            "person.is_trash = 0 and ( person_full_name LIKE '%". $personSearch ."%'",
            " or person_phone LIKE" =>  "%{$personSearch}%",
            " or person_designation LIKE" =>  "%{$personSearch}%",
            ")"
        ),
        "limit" => array(
            "start" => 0,
            "length" => 25
        )
    ));

    $select2Json = [];
    if($selectPerson)
    foreach($selectPerson["data"] as $person) {
        $select2Json[] = array(
            'id' => $person['person_id'],
            'text' => $person['person_full_name'] . ", ". $person['institute_name']
        );
    }

    echo html_entity_decode(json_encode($select2Json));

}


// Select2 Subject List
if(isset($_GET['page']) and $_GET['page'] == "subjectList") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectSubject = easySelectA(array(
        "table"   => "persons_subject",
        "fields"  => "subject_name",
        "where"   => array(
            "subject_name LIKE" =>  "%{$search}%"
        ),
        "groupby" => "subject_name",
        "limit"   => array (
            "start" => 0,
            "length" => 25
        )
    ));

    $select2Json = [];
    
    if($selectSubject) {
        foreach($selectSubject["data"] as $subject) {
            $select2Json[] = array(
                'id' => $subject['subject_name'],
                'text' => $subject['subject_name']
            );
        }
    }

    echo html_entity_decode(json_encode($select2Json));

}

// Select2 Subject List
if(isset($_GET['page']) and $_GET['page'] == "authorList") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectSubject = easySelectA(array(
        "table"   => "product_authors",
        "fields"  => "author_id, author_name",
        "where"   => array(
            "author_name LIKE" =>  "%{$search}%"
        ),
        "limit"   => array (
            "start" => 0,
            "length" => 25
        )
    ));

    $select2Json = [];
    
    if($selectSubject) {
        foreach($selectSubject["data"] as $subject) {
            $select2Json[] = array(
                'id' => $subject['author_id'],
                'text' => $subject['author_name']
            );
        }
    }

    echo html_entity_decode(json_encode($select2Json));
    
}

// Select2 Batch List
if(isset($_GET['page']) and $_GET['page'] == "batchList") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";
    $product_id = isset($_GET['pid']) ? $_GET['pid'] : "";

    $selectSubject = easySelectA(array(
        "table"   => "product_batches",
        "fields"  => "batch_id, batch_number",
        "where"   => array(
            "is_trash = 0 and date(batch_expiry_date) >= curdate() and batch_number LIKE '". $search ."%'",
            " and product_id"   => $product_id
        ),
        "limit"   => array (
            "start" => 0,
            "length" => 25
        )
    ));

    $select2Json = [];
    
    if($selectSubject) {

        foreach($selectSubject["data"] as $subject) {
            $select2Json[] = array(
                'id' => $subject['batch_id'],
                'text' => $subject['batch_number']
            );
        }

    }

    echo html_entity_decode(json_encode($select2Json));
    
}


// Select2 Product Group List
if(isset($_GET['page']) and $_GET['page'] == "productGroupList") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectProductGroup = easySelectA(array(
        "table"   => "products",
        "fields"  => "product_group",
        "where"   => array(
            "is_trash = 0 and product_group LIKE '". safe_input($search) ."%'"
        ),
        "groupby"   => "product_group",
        "limit"   => array (
            "start" => 0,
            "length" => 25
        )
    ));

    $select2Json = [];
    
    if($selectProductGroup) {

        foreach($selectProductGroup["data"] as $group) {
            $select2Json[] = array(
                'id' => $group['product_group'],
                'text' => $group['product_group']
            );
        }

    }

    echo html_entity_decode(json_encode($select2Json));
    
}


// Select2 Product Edition List
if(isset($_GET['page']) and $_GET['page'] == "productEditionList") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectProductEdition = easySelectA(array(
        "table"   => "products",
        "fields"  => "product_edition",
        "where"   => array(
            "is_trash = 0 and product_edition LIKE '". safe_input($search) ."%'"
        ),
        "groupby"   => "product_edition",
        "limit"   => array (
            "start" => 0,
            "length" => 25
        )
    ));

    $select2Json = [];
    
    if($selectProductEdition) {

        foreach($selectProductEdition["data"] as $group) {
            $select2Json[] = array(
                'id' => $group['product_edition'],
                'text' => $group['product_edition']
            );
        }

    }

    echo html_entity_decode(json_encode($select2Json));
    
}


// Select2 Product Edition List
if(isset($_GET['page']) and $_GET['page'] == "userList") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectUser = easySelectA(array(
        "table"   => "users as users",
        "fields"  => "user_id, emp_PIN, emp_firstname, emp_lastname, emp_positions",
        "join"    => array(
            "left join {$table_prefix}employees on emp_id = user_emp_id"
        ),
        "where"   => array(
            "users.is_trash = 0 and emp_firstname LIKE '%". safe_input($search) ."%'",
            " or emp_lastname LIKE" =>  "%{$search}%",
            " or emp_PIN like" =>  "{$search}%"
        ),
        "limit"   => array (
            "start" => 0,
            "length" => 25
        )
    ));

    $select2Json = [];
    
    if($selectUser) {

        foreach($selectUser["data"] as $user) {
            $select2Json[] = array(
                'id' => $user['user_id'],
                'text' => $user['emp_firstname'] . " " . $user['emp_lastname']
            );
        }

    }

    echo html_entity_decode(json_encode($select2Json));
    
}



// Select2 Account List
if(isset($_GET['page']) and $_GET['page'] == "accountList") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectAccount = easySelectA(array(
        "table"   => "accounts",
        "fields"  => "accounts_id, accounts_name",
        "where"   => array(
            "is_trash = 0 and accounts_type like 'Bank%' ",
            " and accounts_name like" =>  "{$search}%"
        ),
        "limit"   => array (
            "start" => 0,
            "length" => 25
        )
    ));

    $select2Json = [];
    
    if($selectAccount) {

        foreach($selectAccount["data"] as $account) {
            $select2Json[] = array(
                'id' => $account['accounts_id'],
                'text' => $account['accounts_name']
            );
        }

    }

    echo html_entity_decode(json_encode($select2Json));
    
}



// Select2 Subject List
if(isset($_GET['page']) and $_GET['page'] == "personTagList") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectPersonTag = easySelectA(array(
        "table"   => "persons_tag",
        "fields"  => "tags",
        "where"   => array(
            "tags LIKE" =>  "%{$search}%"
        ),
        "groupby" => "tags",
        "limit"   => array (
            "start" => 0,
            "length" => 25
        )
    ));

    $select2Json = [];
    
    if($selectPersonTag) {
        foreach($selectPersonTag["data"] as $subject) {
            $select2Json[] = array(
                'id' => $subject['tags'],
                'text' => $subject['tags']
            );
        }
    }

    echo html_entity_decode(json_encode($select2Json));

}



// Select2 Subject List
if(isset($_GET['page']) and $_GET['page'] == "leadsDataSource") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectDataSource = easySelectA(array(
        "table"   => "persons",
        "fields"  => "leads_source",
        "where"   => array(
            "leads_source LIKE" =>  "%{$search}%"
        ),
        "groupby" => "leads_source",
        "limit"   => array (
            "start" => 0,
            "length" => 25
        )
    ));

    $select2Json = [];
    
    if($selectDataSource) {
        foreach($selectDataSource["data"] as $leads) {
            $select2Json[] = array(
                'id' => $leads['leads_source'],
                'text' => $leads['leads_source']
            );
        }
    }

    echo html_entity_decode(json_encode($select2Json));

}



// Select2 Subject List
if(isset($_GET['page']) and $_GET['page'] == "callReasonList") {
    
    $search = isset($_GET['q']) ? $_GET['q'] : "";

    $selectPersonTag = easySelectA(array(
        "table"   => "calls",
        "fields"  => "call_reason",
        "where"   => array(
            "call_reason LIKE" =>  "%{$search}%"
        ),
        "groupby" => "call_reason",
        "limit"   => array (
            "start" => 0,
            "length" => 25
        )
    ));

    $select2Json = [];
    
    if($selectPersonTag) {
        foreach($selectPersonTag["data"] as $subject) {
            $select2Json[] = array(
                'id' => $subject['call_reason'],
                'text' => $subject['call_reason']
            );
        }
    }

    echo html_entity_decode(json_encode($select2Json));

}


?>