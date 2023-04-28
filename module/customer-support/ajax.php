<?php

/*************************** Representative List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "representativeList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "abs(emp_PIN)",
        "emp_photo",
        "emp_firstname",
        "emp_working_area",
        "emp_contact_number"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "employees",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0 and emp_department_id = 2"
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
      $requestData['length'] = $totalRecords;
    }
 
    if(!empty($requestData["search"]["value"])) {  // get data with search
        
        $getData = easySelect(
            "employees as employee",
            "emp_id, emp_PIN, emp_firstname, emp_lastname, emp_positions, emp_working_area, emp_contact_number, emp_emergency_contact_number, emp_photo",
            array(),
            array (
                "employee.is_trash = 0 and emp_department_id = 2 and (emp_PIN = '". safe_input($requestData['search']['value']) ."' ",
                " OR emp_firstname LIKE" => $requestData['search']['value'] . "%",
                " OR emp_lastname LIKE" => $requestData['search']['value'] . "%",
                " OR emp_positions LIKE" => $requestData['search']['value'] . "%",
                " OR emp_working_area LIKE" => $requestData['search']['value'] . "%",
                " OR emp_contact_number LIKE" => $requestData['search']['value'] . "%",
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
            "employees as employee",
            "emp_id, emp_PIN, emp_firstname, emp_lastname, emp_positions, emp_working_area, emp_contact_number, emp_emergency_contact_number, emp_photo",
            array(),
            array("employee.is_trash = 0 and emp_department_id = 2"),
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
            $allNestedData[] = $value["emp_PIN"];
            $allNestedData[] = empty($value['emp_photo']) ? "<img width='60px' height='60px' src='".full_website_address()."/assets/images/defaultUserPic.png' class='img-circle'/>" : "<img width='60px' height='60px' src='".full_website_address()."/images/?for=employees&id={$value['emp_id']}&q=YTozOntzOjI6Iml3IjtpOjE4MDtzOjI6ImloIjtpOjE4MDtzOjI6ImlxIjtpOjcwO30=&v=". strlen($value['emp_photo']) ."' class='img-circle'/>";
            $allNestedData[] = "<strong>{$value["emp_firstname"]} {$value["emp_lastname"]},</strong><br/> {$value["emp_positions"]}";
            $allNestedData[] = $value["emp_working_area"];
            $allNestedData[] = $value["emp_contact_number"];
            
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
if(isset($_GET['page']) and $_GET['page'] == "productList") {
    
    $requestData = $_REQUEST;
  
    $getData = [];
  
    // List of all columns name
    $columns = array(
        "",
        "product_name",
        "product_edition",
        "category_name",
        "product_sale_price",
        "product_description"
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
  
    if(!empty($requestData["search"]["value"]) or !empty($requestData["columns"][2]['search']['value']) or !empty($requestData["columns"][3]['search']['value'])) {  // get data with search
        
        $getData = easySelect(
          "products as product",
          "product_name, product_description, round(product_sale_price, 2) as product_sale_price, category_name, product_edition",
          array (
            "left join {$table_prefix}product_category on product_category_id = category_id"
          ),
            array (
                "product.is_trash = 0 and (product_name LIKE '". safe_input($requestData['search']['value']) ."%' ",
                " OR category_name LIKE" => $requestData['search']['value'] . "%",
                ")",
                " AND product_category_id" => $requestData["columns"][3]['search']['value'],
                " AND product_edition" => $requestData["columns"][2]['search']['value']
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
            "product_name, product_description, round(product_sale_price, 2) as product_sale_price, category_name, product_edition",
            array (
              "left join {$table_prefix}product_category on product_category_id = category_id"
            ),
            array("product.is_trash = 0 and product_type != 'Child'"),
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
            $allNestedData[] = $value["product_name"] . "<br/> <b>Price: </b>" . $value["product_sale_price"];
            $allNestedData[] = $value["product_edition"];
            $allNestedData[] = $value["category_name"];
            $allNestedData[] = $value["product_description"];
            
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


/*************************** getCallerDetails ***********************/
if(isset($_GET['page']) and $_GET['page'] == "getCallerDetails") {

    $caller = str_replace("+88", "",  safe_input($_POST["caller"]) );

    $callerData = easySelectD("
        SELECT
            person_full_name as name,
            person_type as type,
            person_address as address,
            concat(person_designation, ', ', institute_name) as designation
        FROM {$table_prefix}persons 
        left join {$table_prefix}institute on institute_id = person_institute
        WHERE person_phone like '%{$caller}'
        UNION ALL
        SELECT
            customer_name as name,
            'Library/ Customer' as type,
            customer_address as address,
            customer_type as designation
        FROM {$table_prefix}customers
        WHERE customer_phone like '%{$caller}'
        UNION ALL
        SELECT
            concat(emp_firstname, ' ', emp_lastname) as name,
            'Internal Employees' as type,
            emp_present_address as address,
            emp_positions as designation
        FROM {$table_prefix}employees
        WHERE emp_contact_number like '%{$caller}'
    ");

    // Get the last call datetime
    $lastCall = easySelectA(array(
        "table"     => "calls",
        "fields"    => "call_datetime as lastCall, concat(emp_firstname, ' ', emp_lastname) as agent_name, call_status, ( select count(call_id) from {$table_prefix}calls where client_identity like '%{$caller}' ) as callCount ",
        "join"      => array(
            "left join {$table_prefix}users on representative = user_id",
            "left join {$table_prefix}employees on user_emp_id = emp_id",
        ),
        "where"     => array(
            "client_identity like '%{$caller}'"
        ),
        "orderby"   => array(
            "call_id" => "DESC"
        ),
        "limit"     => array(
            "start"     => 0,
            "length"    => 1,
        )
    ));


    $callerDetails = array(
        "details"       => array(),
        "lastCallTime"      => "",
        "totalCallCount"    => 0,
        "agentName"         => "",
        "call_status"       => ""
    );

    if($lastCall !== false) {
        $callerDetails["lastCallTime"] = $lastCall["data"][0]["lastCall"];
        $callerDetails["call_status"] = $lastCall["data"][0]["call_status"];
        $callerDetails["agentName"] = $lastCall["data"][0]["agent_name"];
        $callerDetails["totalCallCount"] = $lastCall["data"][0]["callCount"];
    }


    if($callerData !== false) {
  
        $callerDetails["details"] = $callerData["data"][0];

    } 

    echo json_encode($callerDetails);

}


/*************************** getCallerDetails ***********************/
if(isset($_GET['page']) and $_GET['page'] == "getCallerAllDetails") {

    $caller = str_replace("+88", "",  safe_input($_POST["caller"]) );

    $callerData = easySelectD("
        SELECT
            person_id as id,
            1 as has_sc,
            '/xhr/?icheck=false&module=marketing&page=editPerson&id=' as editUri,
            person_full_name as name,
            person_type as type,
            CONCAT( COALESCE(person_address, ''), ', ', COALESCE(upazila_name, ''), ', ', COALESCE(district_name, ''), ', ', COALESCE(division_name, '') ) as address,
            concat( if(person_designation is null, '', person_designation) , ', ', if(institute_name is null, '', institute_name) ) as designation
        FROM {$table_prefix}persons 
        left join {$table_prefix}institute on institute_id = person_institute
        left join {$table_prefix}districts on district_id = person_district
        left join {$table_prefix}divisions on division_id = person_division
        left join {$table_prefix}upazilas on upazila_id = person_upazila
        WHERE person_phone like '%{$caller}'
        UNION ALL
        SELECT
            customer_id as id,
            0 as has_sc,
            '/xhr/?icheck=false&module=peoples&page=editCustomer&id=' as editUri,
            customer_name as name,
            'Library/ Customer' as type,
            customer_address as address,
            customer_type as designation
        FROM {$table_prefix}customers
        WHERE customer_phone like '%{$caller}'
        UNION ALL
        SELECT
            emp_id as id,
            0 as has_sc,
            '/xhr/?icheck=true&module=peoples&page=editEmployee&id=' as editUri,
            concat(emp_firstname, ' ', emp_lastname) as name,
            'Internal Employees' as type,
            emp_present_address as address,
            emp_positions as designation
        FROM {$table_prefix}employees
        WHERE emp_contact_number like '%{$caller}'
    ");

    // Get Call History
    $getCallHistory = easySelectA(array(
        "table"     => "calls",
        "fields"    => "call_datetime, concat(emp_firstname, ' ', emp_lastname) as agent_name, call_status, call_direction, 
                        if(call_reason is null, 'Unknown', call_reason) as call_reason, duration, feedback",
        "join"      => array(
            "left join {$table_prefix}users on user_id = representative",
            "left join {$table_prefix}employees on emp_id = user_emp_id",
        ),
        "where"     => array(
            "client_identity like '%{$caller}'"
        ),
        "orderby"   => array(
            "call_id" => "DESC"
        ),
        "limit"     => array(
            "start"     => 0,
            "length"    => 5,
        )
    ));


    $callerDetails = array(
        "details"           => array(),
        "callHistory"       => array(),
        "totalCallCount"    => 0,
        "smsHistory"        => array(),
        "totalSmsCount"     => array(),
        "caseHistory"       => array(),
        "orderHistory"       => array()
    );

    if($getCallHistory !== false) {

        $callerDetails["callHistory"] = $getCallHistory["data"];
        $callerDetails["totalCallCount"] = easySelectD("select count(*) as totalCallCount from {$table_prefix}calls where client_identity like '%{$caller}'")["data"][0]["totalCallCount"];

    }


    if($callerData !== false) {
  
        $callerDetails["details"] = $callerData["data"][0];

    } 


    // Get SMS History
    $getSmsHistory = easySelectA(array(
        "table"     => "sms_sender as sms",
        "fields"    => "send_time, concat(emp_firstname, ' ', emp_lastname) as agent_name, left(sms_text, 50) as sms_text",
        "join"      => array(
            "left join {$table_prefix}users on user_id = send_by",
            "left join {$table_prefix}employees on emp_id = user_emp_id",
        ),
        "where"     => array(
            "sms.is_trash = 0 and sms.send_to like '%{$caller}'"
        ),
        "orderby"   => array(
            "sms_id" => "DESC"
        ),
        "limit"     => array(
            "start"     => 0,
            "length"    => 5,
        )
    ));

    if($getSmsHistory !== false) {

        $callerDetails["smsHistory"] = $getSmsHistory["data"];
        $callerDetails["totalSmsCount"] = easySelectD("select count(*) as totalSmsCount from {$table_prefix}sms_sender where send_to like '%{$caller}'")["data"][0]["totalSmsCount"];

    }


    $caseHistory = easySelectA(array(
        "table"     => "cases as cases",
        "fields"    => "case_id, case_datetime, case_title, case_status, 
                        case_added_by_agent, concat(posted_by_employee.emp_firstname, ' ', posted_by_employee.emp_lastname) as posted_by_name",
        "join"      => array(
            "left join {$table_prefix}users as posted_user on posted_user.user_id = case_added_by_agent",
            "left join {$table_prefix}employees as posted_by_employee on posted_by_employee.emp_id = posted_user.user_emp_id",
            "left join {$table_prefix}persons on person_id = case_person",
            "left join {$table_prefix}customers on customer_id = case_customer",
        ),
        "where"     => array(
            "cases.is_trash = 0 AND (",
            " customer_phone LIKE" => '%' . $caller,
            " OR person_phone LIKE" => '%' . $caller,
            ")"
        ),
        "limit" => array(
            "start" => 0,
            "length" => 5
        )
    ));

    if($caseHistory !== false) {
        $callerDetails["caseHistory"] = $caseHistory["data"];
    }


    // Select Orders
    $orderHistory = easySelectA(array(
        "table"     => "sales as sale",
        "fields"    => "sales_id as id, sales_delivery_date as date, sales_status as status, sales_reference as reference, round(sales_grand_total, 2) as total, sales_payment_status as payment_status",
        "join"      => array(
            "left join {$table_prefix}customers on customer_id = sales_customer_id"
        ),
        "where"     => array(
            "sale.is_trash = 0 AND customer_phone LIKE" => '%' . $caller,
        ),
        "limit" => array(
            "start" => 0,
            "length" => 5
        )
    ));

    if($orderHistory !== false) {
        $callerDetails["orderHistory"] = $orderHistory["data"];
    }

    echo json_encode($callerDetails);
    

}



/*************************** get Call history ***********************/
if(isset($_GET['page']) and $_GET['page'] == "getCallHistoryData") {

    $caller = str_replace("+88", "",  safe_input($_POST["caller"]) );


    $getCallHistory = easySelectA(array(
        "table"     => "calls",
        "fields"    => "call_datetime, concat(emp_firstname, ' ', emp_lastname) as agent_name, call_status, call_direction, 
                        if(call_reason is null, 'Unknown', call_reason) as call_reason, duration, feedback",
        "join"      => array(
            "left join {$table_prefix}users on user_id = representative",
            "left join {$table_prefix}employees on emp_id = user_emp_id",
        ),
        "where"     => array(
            "client_identity like '%{$caller}'"
        ),
        "orderby"   => array(
            "call_id" => "DESC"
        ),
        "limit"     => array(
            "start"     => 5,
            "length"    => 100,
        )
    ));


    if( $getCallHistory !== false ) {

        echo json_encode($getCallHistory["data"]);

    } else {

        echo json_encode(array());

    }


}



/*************************** getCallerDetails ***********************/
if(isset($_GET['page']) and $_GET['page'] == "sendSMS") {

    if(send_sms($_POST["number"], $_POST["text"])) {

        echo "1";

    } else {
        echo "0";
    }

}


/*************************** getCallerDetails ***********************/
if(isset($_GET['page']) and $_GET['page'] == "addCallLog") {

    $insertCall = easyInsert(
       "calls",
       array(
           "call_datetime"      => date("Y-m-d H:i:s"),
           "call_direction"     => $_POST["direction"] === "incoming" ? "Incoming" : "Outgoing",
           "call_reason"        => "Unknown",
           "client_identity"    => $_POST["caller"],
           "call_status"        => $_POST["status"],
           "duration"           => $_POST["duration"],
           "feedback"           => $_POST["feedback"],
           "reviewer"           => empty($_POST["reviewer"]) ? NULL : $_POST["reviewer"],
           "representative"     => $_SESSION["uid"]
       ),
       array(),
       true
    );

    if($insertCall !== false) {

        // Update last call time in person list
        easyUpdate(
            "persons",
            array(
                "last_call_time"    => date("Y-m-d H:i:s")
            ),
            array(
                "person_phone"  => $_POST["caller"]
            )
        );
        
        echo $insertCall["last_insert_id"];

    }

}


/*************************** Representative Call List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "myCallList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "call_id",
        "call_direction",
        "call_status",
        "client_identity",
        "duration",
        "feedback",
        "representative"
    );
    
    // Count Total recrods

    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "calls",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0 and (representative = {$_SESSION["uid"]} or reviewer = {$_SESSION["uid"]} )"
        )
    ))["data"][0]["totalRow"];
 
    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if(!empty($requestData["search"]["value"]) or 
        !empty($requestData["columns"][1]['search']['value']) or 
        !empty($requestData["columns"][2]['search']['value']) or 
        !empty($requestData["columns"][3]['search']['value']) or 
        !empty($requestData["columns"][4]['search']['value']) 
    ) {  // get data with search

        $dateFilter = "";
        $specimenDateFilter = "";
        if(!empty($requestData["columns"][1]['search']['value'])) {
            $dateRange = explode(" - ", safe_input($requestData["columns"][1]['search']['value']));
            $dateFilter = "and date(call_datetime) BETWEEN '{$dateRange[0]}' and '{$dateRange[1]}'";
            $specimenDateFilter = "where date(scd_add_on) BETWEEN '{$dateRange[0]}' and '{$dateRange[1]}'";
        }

        $search = "";
        if( !empty($requestData['search']['value']) ) {
            $search = "and ( person_full_name LIKE '". safe_input($requestData['search']['value']) ."%' or client_identity LIKE '". safe_input($requestData['search']['value']) ."%' ) ";
        }
        
        $getData = easySelectA(array(
            "table"     => "calls as calls",
            "fields"    => "call_id, call_datetime, call_direction, call_status, specimen_product_details, person_full_name, person_type, person_address, designation, 
                            client_identity, duration, feedback, call_reason",
            "join"      => array(
                "left join ( select 
                                person_full_name,
                                person_type,
                                concat(
                                    if(person_address = '', '', concat(person_address, ', ') ), 
                                    if(upazila_name is null, '', concat(upazila_name, ', ') ), 
                                    if(district_name is null, '', concat(district_name, ', ') ), 
                                    if(division_name is null, '', concat(division_name) )
                                ) as person_address,
                                product_details as specimen_product_details,
                                person_phone,
                                concat(if(person_designation is null, person_designation, ''), ', ', institute_name) as designation
                            FROM {$table_prefix}persons
                            left join {$table_prefix}districts on district_id = person_district
                            left join {$table_prefix}divisions on division_id = person_division
                            left join {$table_prefix}upazilas on upazila_id = person_upazila
                            left join {$table_prefix}institute on institute_id = person_institute
                            left join ( select
                                    group_concat(product_name, '- ', round(scd_product_qnt, 2) SEPARATOR '<br/>' ) as product_details,
                                    scd_person_id
                                from {$table_prefix}sc_distribution
                                left join {$table_prefix}products on product_id = scd_product_id
                                where is_bundle_item = 0
                                group by scd_person_id
                            ) as specimen_details on scd_person_id = person_id
                ) as person on person.person_phone = calls.client_identity"
            ),
            "where"     => array(
                "calls.is_trash = 0",
                " AND call_direction"  => $requestData["columns"][2]['search']['value'],
                " AND call_reason"  => $requestData["columns"][3]['search']['value'],
                " AND call_status"  => $requestData["columns"][4]['search']['value'],
                " AND (representative = {$_SESSION["uid"]} or reviewer = {$_SESSION["uid"]} ) $search $dateFilter"
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
            "table"     => "calls as calls",
            "fields"    => "call_id, call_datetime, call_direction, call_status, specimen_product_details, person_full_name, person_type, person_address, designation, 
                            client_identity, duration, feedback, call_reason",
            "join"      => array(
                "left join ( select 
                                person_full_name,
                                person_type,
                                concat(
                                    if(person_address = '', '', concat(person_address, ', ') ), 
                                    if(upazila_name is null, '', concat(upazila_name, ', ') ), 
                                    if(district_name is null, '', concat(district_name, ', ') ), 
                                    if(division_name is null, '', concat(division_name) )
                                ) as person_address,
                                product_details as specimen_product_details,
                                person_phone,
                                concat(if(person_designation is null, person_designation, ''), ', ', institute_name) as designation
                            FROM {$table_prefix}persons
                            left join {$table_prefix}districts on district_id = person_district
                            left join {$table_prefix}divisions on division_id = person_division
                            left join {$table_prefix}upazilas on upazila_id = person_upazila
                            left join {$table_prefix}institute on institute_id = person_institute
                            left join ( select
                                    group_concat(product_name, '- ', round(scd_product_qnt, 2) SEPARATOR '<br/>' ) as product_details,
                                    scd_person_id
                                from {$table_prefix}sc_distribution
                                left join {$table_prefix}products on product_id = scd_product_id
                                where is_bundle_item = 0
                                group by scd_person_id
                            ) as specimen_details on scd_person_id = person_id
                ) as person on person.person_phone = calls.client_identity"
            ),
            "where"     => array(
                "calls.is_trash = 0",
                " and (representative = {$_SESSION["uid"]} or reviewer = {$_SESSION["uid"]} )"
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
    if($getData) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            
            $callStatus = "";
            if($value["call_status"] === "Answered") {
                $callStatus = "<span style='padding: 2px 5px;' class='text-center btn-success'>Answered</span>";
            } else if($value["call_status"] === "Rejected") {
                $callStatus = "<span style='padding: 2px 5px;' class='text-center btn-danger'>Rejected</span>";
            } else {
                $callStatus = "<span style='padding: 2px 5px;' class='text-center btn-info'>{$value["call_status"]}</span>";
            }

            $allNestedData[] = "";
            $allNestedData[] = $value["call_datetime"];
            $allNestedData[] = $value["call_direction"];
            $allNestedData[] = $value["call_reason"];
            $allNestedData[] = $callStatus;
            $allNestedData[] = "{$value["client_identity"]}<br/>{$value["person_full_name"]}, {$value["person_type"]} <br/> {$value["designation"]}, {$value["person_address"]}";
            $allNestedData[] = $value["specimen_product_details"];
            $allNestedData[] = $value["duration"];
            $allNestedData[] = $value["feedback"] . '<a style="margin-left: 10px;" data-toggle="modal" data-target="#modalDefault" href="'. full_website_address() .'/xhr/?module=customer-support&page=editCallFeedback&call_id='. $value["call_id"] .'"><i class="fa fa-edit"></i></a>';
            
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


/*************************** Representative Call List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "allCallList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "call_id",
        "call_direction",
        "call_status",
        "client_identity",
        "person_type",
        "duration",
        "feedback",
        "feedback_informative",
        "sale_our_product",
        "use_our_product",
        "mr_feedback",
        "other_info",
        "representative"
    );
    
    // Count Total recrods

    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "calls",
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
        !empty($requestData["columns"][6]['search']['value']) or 
        !empty($requestData["columns"][10]['search']['value']) or 
        !empty($requestData["columns"][16]['search']['value']) 
    ) {  // get data with search

        $dateFilter = "";
        $specimenDateFilter = "";
        if(!empty($requestData["columns"][1]['search']['value'])) {
            $dateRange = explode(" - ", safe_input($requestData["columns"][1]['search']['value']));
            $dateFilter = "and date(call_datetime) BETWEEN '{$dateRange[0]}' and '{$dateRange[1]}'";
            $specimenDateFilter = "where date(scd_add_on) BETWEEN '{$dateRange[0]}' and '{$dateRange[1]}'";
        }

        $search = "";
        if( !empty($requestData['search']['value']) ) {
            $search = "and ( person_full_name LIKE '". safe_input($requestData['search']['value']) ."%' or client_identity LIKE '". safe_input($requestData['search']['value']) ."%' ) ";
        }
        
        $getData = easySelectA(array(
            "table"     => "calls as calls",
            "fields"    => "call_id, call_datetime, call_direction, call_status, call_reason, specimen_product_details, person_full_name, person_type, 
                            person_address, designation, client_identity, duration, feedback, concat(emp_firstname, ' ', emp_lastname) as representative,
                            feedback_informative, sale_our_product, use_our_product, mr_feedback, other_info, specimen_copy_received",
            "join"      => array(
                "left join {$table_prefix}users on representative = user_id",
                "left join {$table_prefix}employees on user_emp_id = emp_id",
                "left join ( select 
                                person_full_name,
                                person_type,
                                concat(
                                    if(person_address = '', '', concat(person_address, ', ') ), 
                                    if(upazila_name is null, '', concat(upazila_name, ', ') ), 
                                    if(district_name is null, '', concat(district_name, ', ') ), 
                                    if(division_name is null, '', concat(division_name) )
                                ) as person_address,
                                product_details as specimen_product_details,
                                person_phone,
                                concat(if(person_designation is null, person_designation, ''), ', ', institute_name) as designation
                            FROM {$table_prefix}persons
                            left join {$table_prefix}districts on district_id = person_district
                            left join {$table_prefix}divisions on division_id = person_division
                            left join {$table_prefix}upazilas on upazila_id = person_upazila
                            left join {$table_prefix}institute on institute_id = person_institute
                            left join ( select
                                    group_concat(product_name, '- ', round(scd_product_qnt, 2) SEPARATOR '<br/>' ) as product_details,
                                    scd_person_id
                                from {$table_prefix}sc_distribution
                                left join {$table_prefix}products on product_id = scd_product_id
                                where is_bundle_item = 0
                                group by scd_person_id
                            ) as specimen_details on scd_person_id = person_id
                ) as person on person.person_phone = calls.client_identity"
            ),
            "where"     => array(
                "calls.is_trash = 0",
                " AND call_direction"  => $requestData["columns"][2]['search']['value'],
                " AND call_status"  => $requestData["columns"][3]['search']['value'],
                " AND call_reason"  => $requestData["columns"][4]['search']['value'],
                " AND person_type"  => $requestData["columns"][6]['search']['value'],
                " AND specimen_copy_received"  => $requestData["columns"][10]['search']['value'],
                " AND representative"  => $requestData["columns"][16]['search']['value'],
                " $search $dateFilter"
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
            "table"     => "calls as calls",
            "fields"    => "call_id, call_datetime, call_direction, call_status, call_reason, specimen_product_details, person_full_name, person_type, 
                            person_address, designation, client_identity, duration, feedback, concat(emp_firstname, ' ', emp_lastname) as representative,
                            feedback_informative, sale_our_product, use_our_product, mr_feedback, other_info, specimen_copy_received
                            ",
            "join"      => array(
                "left join {$table_prefix}users on representative = user_id",
                "left join {$table_prefix}employees on user_emp_id = emp_id",
                "left join ( select 
                                person_full_name,
                                person_type,
                                concat(
                                    if(person_address = '', '', concat(person_address, ', ') ), 
                                    if(upazila_name is null, '', concat(upazila_name, ', ') ), 
                                    if(district_name is null, '', concat(district_name, ', ') ), 
                                    if(division_name is null, '', concat(division_name) )
                                ) as person_address,
                                product_details as specimen_product_details,
                                person_phone,
                                concat(if(person_designation is null, person_designation, ''), ', ', institute_name) as designation
                            FROM {$table_prefix}persons
                            left join {$table_prefix}districts on district_id = person_district
                            left join {$table_prefix}divisions on division_id = person_division
                            left join {$table_prefix}upazilas on upazila_id = person_upazila
                            left join {$table_prefix}institute on institute_id = person_institute
                            left join ( select
                                    group_concat(product_name, '- ', round(scd_product_qnt, 2) SEPARATOR '<br/>' ) as product_details,
                                    scd_person_id
                                from {$table_prefix}sc_distribution
                                left join {$table_prefix}products on product_id = scd_product_id
                                where is_bundle_item = 0
                                group by scd_person_id
                            ) as specimen_details on scd_person_id = person_id
                ) as person on person.person_phone = calls.client_identity"
            ),
            "where"     => array(
                "calls.is_trash = 0"
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
    if($getData) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            
            $callStatus = "";
            if($value["call_status"] === "Answered") {
                $callStatus = "<span style='padding: 2px 5px;' class='text-center btn-success'>Answered</span>";
            } else if($value["call_status"] === "Rejected") {
                $callStatus = "<span style='padding: 2px 5px;' class='text-center btn-danger'>Rejected</span>";
            } else {
                $callStatus = "<span style='padding: 2px 5px;' class='text-center btn-info'>{$value["call_status"]}</span>";
            }

            $allNestedData[] = "";
            $allNestedData[] = $value["call_datetime"];
            $allNestedData[] = $value["call_direction"];
            $allNestedData[] = $callStatus;
            $allNestedData[] = $value["call_reason"];
            $allNestedData[] = "{$value["client_identity"]}<br/>{$value["person_full_name"]} <br/> {$value["designation"]}, {$value["person_address"]}";
            $allNestedData[] = $value["person_type"];
            $allNestedData[] = $value["specimen_product_details"];
            $allNestedData[] = $value["duration"];
            $allNestedData[] = $value["feedback"] . '<a style="margin-left: 10px;" data-toggle="modal" data-target="#modalDefault" href="'. full_website_address() .'/xhr/?module=customer-support&page=editCallFeedback&call_id='. $value["call_id"] .'"><i class="fa fa-edit"></i></a>';
            $allNestedData[] = $value["specimen_copy_received"];
            $allNestedData[] = $value["feedback_informative"];
            $allNestedData[] = $value["sale_our_product"];
            $allNestedData[] = $value["use_our_product"];
            $allNestedData[] = $value["mr_feedback"];
            $allNestedData[] = $value["other_info"];
            $allNestedData[] = $value["representative"];
            
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


/************************** Edit Call Feedback **********************/
if(isset($_GET['page']) and $_GET['page'] == "editCallFeedback") {

    $getCallData = easySelectA(array(
        "table"     => "calls",
        "fields"    => "feedback, client_identity, reviewer, concat(emp_firstname, ' ', emp_lastname) as reviewer_name",
        "join"      => array(
            "left join {$table_prefix}users on reviewer = user_id",
            "left join {$table_prefix}employees on user_emp_id = emp_id"
        ),
        "where"     => array(
            "call_id"   => $_GET["call_id"]
        )
    ))["data"][0];
  
    // Include the modal header
    modal_header("Edit feedback for {$getCallData["client_identity"]}", full_website_address() . "/xhr/?module=customer-support&page=updateCallFeedback");
    
    ?>
      <div class="box-body">

        <div class="form-group required">
            <label for="feedback">Feedback:</label>
            <textarea name="feedback" id="feedback" cols="30" rows="6" class="form-control" placeholder="Please enter case/ issue/ problem details here"> <?php echo $getCallData["feedback"]; ?> </textarea>
        </div>
        <div class="form-group required">
            <label for="reviewer">Reviewer:</label>
            <select name="reviewer" id="reviewer" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=userList" style="width: 100%;">
                <option value=""><?= __("Select feedback reviewer"); ?>....</option>
                <option selected value="<?php echo $getCallData["reviewer"]; ?>"> <?php echo $getCallData["reviewer_name"]; ?> </option>
            </select>
        </div>
        <input type="hidden" name="callId" value="<?php echo safe_entities($_GET["call_id"]); ?>">
        
      </div>
      <!-- /Box body-->
  
    <?php
  
    // Include the modal footer
    modal_footer();
  
}


/*************************** updateCallFeedback ***********************/
if(isset($_GET['page']) and $_GET['page'] == "updateCallFeedback") {

    $call_id = empty($_POST["callId"]) ? "" : $_POST["callId"];

    // if $_POST["callId"] there have a caller number to search the call_id in database
    if( $call_id === "" and isset( $_POST["caller"] ) ) {
    
        $lastCallOfThisCaller = easySelectD("SELECT call_id FROM {$table_prefix}calls where client_identity = '". safe_input($_POST["caller"]) ."' and date(call_datetime) = current_date order by call_id DESC limit 0,1");

        if($lastCallOfThisCaller !== false) {
            $call_id = $lastCallOfThisCaller["data"][0]["call_id"];
        }

    }

    // If there caller id is empty then return an error msg
    if($call_id === "") {
        return _e("There is no call found regarding this number.");
    }

    $updateCallsFeedback = easyUpdate(
        "calls",
        array(
            "feedback"               => $_POST["feedback"],
            "call_reason"            => $_POST["reason"],
            "specimen_copy_received" => empty($_POST["specimenReceived"]) ? NULL : $_POST["specimenReceived"],
            "feedback_informative"   => empty($_POST["informative"]) ? NULL : $_POST["informative"],
            "sale_our_product"       => empty($_POST["saleOurProduct"]) ? NULL : $_POST["saleOurProduct"],
            "use_our_product"        => empty($_POST["userOurProduct"]) ? NULL : $_POST["userOurProduct"],
            "mr_feedback"            => empty($_POST["mrFeedback"]) ? NULL : $_POST["mrFeedback"],
            "other_info"             => $_POST["otherInformation"],
            "reviewer"               => empty($_POST["reviewer"]) ? NULL : $_POST["reviewer"],
        ),
        array(
            "call_id"    => $call_id
        )
    );

    if($updateCallsFeedback !== false) {
        _s("Successfully updated");
    } else {
        _e($updateCallsFeedback);
    }

}

/************************** New Case **********************/
if(isset($_GET['page']) and $_GET['page'] == "newCase") {
  
    // Include the modal header
    modal_header("Add New Case", full_website_address() . "/xhr/?module=customer-support&page=addNewCase");
    
    ?>
      <div class="box-body">

        <script src="<?php echo full_website_address(); ?>/assets/3rd-party/tinymce_6.1.2/tinymce.min.js"></script>

        <div class="row">

            <div class="col-md-7">

                <div class="form-group required">
                    <label for="caseTitle"><?= __("Case Title:"); ?></label>
                    <input type="text" name="caseTitle" id="caseTitle" class="form-control" required>
                </div>
                <div class="form-group required">
                    <label for="caseDetails">Case/ Issue details:</label>
                    <textarea name="caseDetails" id="caseDetails" cols="30" rows="8" class="form-control" placeholder="Please enter case/ issue/ problem details here"></textarea>
                </div>
                <div class="form-group">
                    <label for="caseNote">Note/ Private Reply:</label>
                    <textarea name="caseNote" id="caseNote" cols="30" rows="4" class="form-control" placeholder="Enter Note/ Private reply here. This will not visible to customers."></textarea>
                </div>
                <div class="form-group">
                    <label for="caseAttachment">Attachment</label>
                    <input type="file" name="caseAttachment[]" id="caseAttachment" multiple accept="image/png, image/jpeg" class="form-control">
                    <small style='padding: 2px;' class='alert-danger'>Note: Max Upload Size: <?php echo $_SETTINGS["MAX_UPLOAD_SIZE"]; ?>MB. Only JPEG and PNG image types are allowed to upload</small>    
                </div>
                
                
            </div>

            <div class="col-md-5">

                <div class="form-group required">
                    <label for="casePriority"><?= __("Prority:"); ?></label>
                    <select name="casePriority" id="casePriority" class="form-control" style="width: 100%;" required>
                        <option value=""><?= __("Select Prority"); ?>....</option>
                        <?php 
                            $priority = array('Low', 'Medium', 'High', 'Critical');
                            foreach($priority as $priority) {
                                echo "<option value='{$priority}'>{$priority}</option>";
                            }
                        ?>

                    </select>
                </div>

                <div class="form-group required">
                    <label for="caseType"><?= __("Type:"); ?></label>
                    <select name="caseType" id="caseType" class="form-control" style="width: 100%;" required>
                        <option value=""><?= __("Select Type"); ?>....</option>
                        <?php 
                            $type = array('Refund Request', 'Packaging Issues', 'Delivery Issue', 'Technical Issues', 'Query', 'Damaged Item', 'Exchange', 'Others');
                            foreach($type as $type) {
                                echo "<option value='{$type}'>{$type}</option>";
                            }
                        ?>

                    </select>
                </div>

                <div class="form-group">
                    <label for="caseStatus"><?= __("Status:"); ?></label>
                    <select name="caseStatus" id="caseStatus" class="form-control" style="width: 100%;">
                        <option value=""><?= __("Select Status"); ?>....</option>
                        <?php 
                            $status = array('Pending', 'Open', 'Replied', 'Customer Responded', 'Solved', 'Informed', 'On Hold');
                            foreach($status as $status) {
                                echo "<option value='{$status}'>{$status}</option>";
                            }
                        ?>

                    </select>
                </div>

                <div class="form-group">
                    <label for="caseCustomer"><?= __("Customer:"); ?></label>
                    <select name="caseCustomer" id="caseCustomer" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=customerList" style="width: 100%;">
                        <option value=""><?= __("Select Customer"); ?>....</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="casePerson"><?= __("Person/ Lead:"); ?></label>
                    <select name="casePerson" id="casePerson" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=personList" style="width: 100%;">
                        <option value=""><?= __("Select Person/ Lead"); ?>....</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="caseSite"><?= __("Site:"); ?></label>
                    <input type="text" name="caseSite" id="caseSite" class="form-control">
                </div>

                <div class="form-group">
                    <label for="caseAssignTo"><?= __("Assign To:"); ?></label>
                    <select name="caseAssignTo" id="caseAssignTo" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=userList" style="width: 100%;">
                        <option value=""><?= __("Select user"); ?>....</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="caseBelongsTo"><?= __("Belongs To:"); ?></label>
                    <select name="caseBelongsTo" id="caseBelongsTo" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=employeeList" style="width: 100%;">
                        <option value=""><?= __("Select Employee"); ?>....</option>
                    </select>
                </div>

            </div>


        </div>

        <script>
            // Initialize the editor
            tinymce.init({
                selector: '#caseDetails',
                menubar: false,
                plugins: 'link',
                toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | link | outdent indent',
                statusbar: false,
                height : 270,
                default_link_target: "_blank",
                branding: false,
                invalid_elements : 'em,input,textarea,button',
            });
        </script>

        
      </div>
      <!-- /Box body-->
  
    <?php
  
    // Include the modal footer
    modal_footer();
  
}


if(isset($_GET['page']) and $_GET['page'] == "addNewCase") {


    if( empty($_POST["caseTitle"]) ) {
        return _e("Please enter case title");
    } else if( empty($_POST["caseDetails"]) ) {
        return _e("Please enter case detials");
    } else if( empty($_POST["casePriority"]) ) {
        return _e("Please select priority");
    } else if( empty($_POST["caseType"]) ) {
        return _e("Please select case type");
    }

    $attachmentList = array();
    $hasErrorOnUpload = false;

    if( $_FILES["caseAttachment"]["size"][0] > 0 ) {

        $maxUploadSize = $_SETTINGS["MAX_UPLOAD_SIZE"] * 1024 * 1024;

        foreach($_FILES["caseAttachment"]["size"] as $index => $size)  {

            if ($maxUploadSize < $size) {
                $hasErrorOnUpload = true;
                break;
            }

            $mimeType = strtolower($_FILES["caseAttachment"]["type"][$index]);
            $extension = explode(".", $_FILES["caseAttachment"]["name"][$index]);
            $extension = end($extension);

            if( 
                !isset( $_SETTINGS["VALID_IMAGE_TYPE_FOR_UPLOAD"][$extension] ) or 
                (   
                    isset($_SETTINGS["VALID_IMAGE_TYPE_FOR_UPLOAD"][$extension]) and 
                    !in_array( $mimeType, $_SETTINGS["VALID_IMAGE_TYPE_FOR_UPLOAD"][$extension] ) 
                ) 
            ) {
                $hasErrorOnUpload = true;
                break;
            }

            if($size > 0) {

                array_push($attachmentList, array(
                    "name"  => $_FILES["caseAttachment"]["name"][$index],
                    "type"  => $_FILES["caseAttachment"]["type"][$index],
                    "tmp_name"  => $_FILES["caseAttachment"]["tmp_name"][$index],
                    "error"  => $_FILES["caseAttachment"]["error"][$index],
                    "size"  => $_FILES["caseAttachment"]["size"][$index]
                ));

            }

        }

    }

    if($hasErrorOnUpload) {
        return _e("There have an error to upload the images. Please check the image type and size.");
    }

    // Insert case
    $insertCase = easyInsert(
        "cases",
        array(
            "case_datetime"     => date("Y-m-d H:i:s"),
            "case_title"        => $_POST["caseTitle"],
            "case_priority"     => $_POST["casePriority"],
            "case_type"         => $_POST["caseType"],
            "case_status"       => empty($_POST["caseStatus"]) ? 'Open' : $_POST["caseStatus"],
            "case_site"         => $_POST["caseSite"],
            "case_customer"     => empty($_POST["caseCustomer"]) ? NULL : $_POST["caseCustomer"],
            "case_person"       => empty($_POST["casePerson"]) ? NULL : $_POST["casePerson"],
            "case_assigned_to"  => empty($_POST["caseAssignTo"]) ? NULL : $_POST["caseAssignTo"],
            "case_belongs_to"  => empty($_POST["caseBelongsTo"]) ? NULL : $_POST["caseBelongsTo"],
            "case_added_by_agent" => $_SESSION["uid"]
        ),
        array(),
        true
    );

    //print_r($insertCase);

    if( isset($insertCase["status"]) and $insertCase["status"] === 'success' ) {


        $uploadedAttachment = array();

        if( count($attachmentList) > 0 ) {

            foreach($attachmentList as $attached)  {

                $attachmentUploadDir = "attachments/cases/{$_POST["caseType"]}/" . date("Y-m-d");
                $uploadCaseAttachment = easyUpload($attached, $attachmentUploadDir );

                if(!isset($uploadCaseAttachment["success"])) {
                    $hasErrorOnUpload = true;
                } else {
                    array_push($uploadedAttachment, $attachmentUploadDir . "/".$uploadCaseAttachment["fileName"]);
                }

            }

        }


        // Insert case public replies
        easyInsert(
            "case_replies",
            array(
                "reply_type"        => 'Public',
                'reply_case_id'     => $insertCase["last_insert_id"],
                "reply_datetime"    => date("Y-m-d H:i:s"),
                "reply_details"     => purify_html($_POST["caseDetails"]) ,
                "reply_attachment"  => serialize($uploadedAttachment),
                "reply_by_agent"    => $_SESSION["uid"]
            )
        );

        // Insert case private replies
        if( !empty($_POST["caseNote"]) ) {
            easyInsert(
                "case_replies",
                array(
                    "reply_type"        => 'Private',
                    'reply_case_id'     => $insertCase["last_insert_id"],
                    "reply_datetime"    => date("Y-m-d H:i:s"),
                    "reply_details"     => purify_html($_POST["caseNote"]),
                    "reply_attachment"  => NULL,
                    "reply_by_agent"    => $_SESSION["uid"]
                )
            );
        }
        
        _s("Case has been added successfully");

    }

}


if(isset($_GET['page']) and $_GET['page'] == "addCaseReply") {


    if( empty($_POST["caseReply"]) ) {
        return _e("Please enter case reply");
    } else if( empty($_POST["replyMode"]) ) {
        return _e("Please select reply mode");
    }

    $attachmentList = array();
    $hasErrorOnUpload = false;

    if( $_FILES["caseReplyAttachment"]["size"][0] > 0 ) {

        $maxUploadSize = $_SETTINGS["MAX_UPLOAD_SIZE"] * 1024 * 1024;

        foreach($_FILES["caseReplyAttachment"]["size"] as $index => $size)  {

            if ($maxUploadSize < $size) {
                $hasErrorOnUpload = true;
                break;
            }

            $extensionName = strtolower(explode("/", $_FILES["caseReplyAttachment"]["type"][$index])[1]);
            if(!in_array($extensionName, $_SETTINGS["VALID_IMAGE_TYPE_FOR_UPLOAD"])) {
                $hasErrorOnUpload = true;
                break;
            }

            if($size > 0) {

                array_push($attachmentList, array(
                    "name"  => $_FILES["caseReplyAttachment"]["name"][$index],
                    "type"  => $_FILES["caseReplyAttachment"]["type"][$index],
                    "tmp_name"  => $_FILES["caseReplyAttachment"]["tmp_name"][$index],
                    "error"  => $_FILES["caseReplyAttachment"]["error"][$index],
                    "size"  => $_FILES["caseReplyAttachment"]["size"][$index]
                ));

            }

        }

    }

    if($hasErrorOnUpload) {
        return _e("There have an error to upload the images. Please check the image type and size.");
    }


    $uploadedAttachment = array();

    if( count($attachmentList) > 0 ) {

        foreach($attachmentList as $attached)  {

            $attachmentUploadDir = "attachments/cases/{$_POST["caseType"]}/" . date("Y-m-d");
            $uploadCaseAttachment = easyUpload($attached, $attachmentUploadDir );

            if(!isset($uploadCaseAttachment["success"])) {
                $hasErrorOnUpload = true;
            } else {
                array_push($uploadedAttachment, $attachmentUploadDir . "/".$uploadCaseAttachment["fileName"]);
            }

        }

    }


    // Insert case public replies
    $addCaseReyply = easyInsert(
        "case_replies",
        array(
            "reply_type"        => $_POST["replyMode"],
            'reply_case_id'     => $_POST["case_id"],
            "reply_datetime"    => date("Y-m-d H:i:s"),
            "reply_details"     => purify_html($_POST["caseReply"]),
            "reply_attachment"  => serialize($uploadedAttachment),
            "reply_by_agent"    => $_SESSION["uid"]
        ),
        array(),
        true
    );
  

    
    if( isset($addCaseReyply["status"]) and $addCaseReyply["status"] === 'success' ) {

        $selectReplies = easySelectA(array(
            "table"     => "case_replies as reply",
            "fields"    => "reply_id, reply_type, reply_datetime, reply_details, reply_attachment,
                            if(emp_firstname is null, customer_name, concat(emp_firstname, ' ', emp_lastname)) as reply_by,
                            reply_by_customer
                            ",
            "join"      => array(
                "left join {$table_prefix}users on user_id = reply_by_agent",
                "left join {$table_prefix}employees on emp_id = user_emp_id",
                "left join {$table_prefix}customers on customer_id = reply_by_customer"
            ),
            "where"     => array(
                "reply.is_trash = 0 and reply_id"    => $addCaseReyply["last_insert_id"]
            )
        ));

        if($selectReplies !== false) {

            $reply = $selectReplies["data"][0];

            $time_elaps = time_elapsed_string($reply["reply_datetime"]) . " (" .  date("d F, Y h:m A", strtotime($reply["reply_datetime"]))  .")";

            $attachment = "";
            if($reply["reply_attachment"] !== null) {

                $listAttachment = unserialize( html_entity_decode($reply["reply_attachment"]) );

                foreach($listAttachment as $image) {
                    $attachment .= "<li><img width='80' height='80' src='". full_website_address() ."/assets/upload/{$image}'></li>";
                }

            }


            // Define user and reply type
            $userType = "<span><i class='fa fa-user'></i> Staff</span>";
            $replyType = "<span><i class='fa fa-globe'></i> Public</span>";
            $replyClass = 'reply';
            $replyTypeMoveTo = 'Private';
            if( $reply["reply_by_customer"] !== null ) {
                $userType = "<span><i class='fa fa-user'></i> Customer</span>";
            }

            if( $reply["reply_type"] === "Private" ) {
                $replyType = "<span><i class='fa fa-lock'></i> Private</span>";
                $replyClass = 'reply private';
                $replyTypeMoveTo = 'Public';
            }

            // If there have any attachment then show them
            if($attachment !== "") {
                $attachment = "<div class='attachment'>
                                    <p>Attachment:</p>
                                    <ul class='imageAttachment'>
                                        {$attachment}
                                    </ul>
                                </div>";
            }
            
            
            echo "<div class='{$replyClass}'>
                    
                    <div class='title'>
                        <span>{$reply["reply_by"]}</span>
                        <span>
                            {$time_elaps}
                        
                            <div class='btn-group'>
                                
                                <button type='button' class='btn btn-link' data-toggle='dropdown'>
                                    <i class='fa fa-ellipsis-v'></i>
                                </button>
                                <ul class='dropdown-menu dropdown-menu-right' role='menu'>

                                    <li><a class='deleteEntry' removeParent='.reply' href='". full_website_address() . "/xhr/?module=customer-support&page=deleteCase data-to-be-deleted={$reply["reply_id"]}'> Delete</a></li>
                                    <li> <a class='updateEntry' href='". full_website_address() . "/xhr/?module=customer-support&page=updateCaseReplyType' data-to-be-updated='{$reply["reply_id"]}'>Move to {$replyTypeMoveTo} </a> </li>

                                </ul>

                            </div>
                        </span>

                    </div>
                    <div class='type'>
                        {$userType}
                        {$replyType}
                    </div>

                    <div class='reply-details'>
                        ". str_replace('\n', '</br>', html_entity_decode($reply["reply_details"]) ) ."
                    </div>
                    $attachment
                    
                </div>";

        }

    }

}


/*************************** case List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "caseList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "case_datetime",
        "requester",
        "case_title",
        "FIELD(case_is_pin, 1, 0),
        FIELD(case_status, 'Pending', 'Customer Responded', 'Open', 'Informed', 'On Hold', 'Replied', 'Closed', 'Solved'),
        FIELD(case_priority, 'Critical', 'High', 'Medium', 'Low'),
        case_datetime"
    );
    
    // Count Total recrods

    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "cases",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];
 
    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }


 if( !empty($requestData["search"]["value"])) {  // get data with only search

        $getData = easySelectA(array(
            "table"     => "cases as cases",
            "fields"    => "case_id, case_datetime, case_title, case_priority, case_type, case_status, last_reply, 
                            case_assigned_to, concat(assigned_to_employee.emp_firstname, ' ', assigned_to_employee.emp_lastname) as case_assign_to_name, 
                            case_belongs_to, concat(belongs_to.emp_firstname, ' ', belongs_to.emp_lastname) as case_belongs_to_name,
                            case_added_by_agent, concat(posted_by_employee.emp_firstname, ' ', posted_by_employee.emp_lastname) as posted_by_name, 
                            if(person_full_name is null, 
                                concat(customer_name, '<br/>', customer_phone),
                                concat(person_full_name, '<br/>', person_phone, ', ', person_email)
                            ) as requester",
            "join"      => array(
                "left join {$table_prefix}users as assigned_user on assigned_user.user_id = case_assigned_to",
                "left join {$table_prefix}employees as assigned_to_employee on assigned_to_employee.emp_id = assigned_user.user_emp_id",

                "left join {$table_prefix}users as posted_user on posted_user.user_id = case_added_by_agent",
                "left join {$table_prefix}employees as posted_by_employee on posted_by_employee.emp_id = posted_user.user_emp_id",

                "left join {$table_prefix}employees as belongs_to on belongs_to.emp_id = case_belongs_to",
                "left join {$table_prefix}persons on person_id = case_person",
                "left join {$table_prefix}customers on customer_id = case_customer",
                "left join (select
                        max(reply_datetime) as last_reply,
                        reply_case_id
                    from {$table_prefix}case_replies
                    where is_trash = 0
                    group by reply_case_id
                ) as replies on replies.reply_case_id = case_id"
            ),
            "where"     => array(
                "cases.is_trash = 0 AND (",
                " customer_name LIKE '". safe_input($requestData["search"]["value"]) ."' ",
                " OR customer_phone LIKE" => $requestData["search"]["value"] . '%',
                " OR person_full_name LIKE" => '%' . $requestData["search"]["value"] . '%',
                " OR person_phone LIKE" => $requestData["search"]["value"] . '%',
                ")",
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

    } else if( !empty($requestData["search"]["value"]) or
        !empty($requestData["columns"][1]['search']['value']) or 
        !empty($requestData["columns"][3]['search']['value']) or 
        !empty($requestData["columns"][4]['search']['value']) or 
        !empty($requestData["columns"][5]['search']['value']) or 
        !empty($requestData["columns"][6]['search']['value']) or
        !empty($requestData["columns"][7]['search']['value']) or
        !empty($requestData["columns"][8]['search']['value']) or
        !empty($requestData["columns"][10]['search']['value'])
    ) {  // get data with search and filters

        $dateFilter = "";
        if(!empty($requestData["columns"][1]['search']['value'])) {
            $dateRange = explode(" - ", safe_input($requestData["columns"][1]['search']['value']));
            $dateFilter = " AND date(case_datetime) BETWEEN '{$dateRange[0]}' and '{$dateRange[1]}'";
        }

        $getData = easySelectA(array(
            "table"     => "cases as cases",
            "fields"    => "case_id, case_datetime, case_title, case_priority, case_type, case_status, last_reply, 
                            case_assigned_to, concat(assigned_to_employee.emp_firstname, ' ', assigned_to_employee.emp_lastname) as case_assign_to_name, 
                            case_belongs_to, concat(belongs_to.emp_firstname, ' ', belongs_to.emp_lastname) as case_belongs_to_name,
                            case_added_by_agent, concat(posted_by_employee.emp_firstname, ' ', posted_by_employee.emp_lastname) as posted_by_name, 
                            if(person_full_name is null, 
                                concat(customer_name, '<br/>', customer_phone),
                                concat(person_full_name, '<br/>', person_phone, ', ', person_email)
                            ) as requester",
            "join"      => array(
                "left join {$table_prefix}users as assigned_user on assigned_user.user_id = case_assigned_to",
                "left join {$table_prefix}employees as assigned_to_employee on assigned_to_employee.emp_id = assigned_user.user_emp_id",

                "left join {$table_prefix}users as posted_user on posted_user.user_id = case_added_by_agent",
                "left join {$table_prefix}employees as posted_by_employee on posted_by_employee.emp_id = posted_user.user_emp_id",

                "left join {$table_prefix}employees as belongs_to on belongs_to.emp_id = case_belongs_to",
                "left join {$table_prefix}persons on person_id = case_person",
                "left join {$table_prefix}customers on customer_id = case_customer",
                "left join (select
                        max(reply_datetime) as last_reply,
                        reply_case_id
                    from {$table_prefix}case_replies
                    where is_trash = 0
                    group by reply_case_id
                ) as replies on replies.reply_case_id = case_id"
            ),
            "where"     => array(
                "cases.is_trash = 0 AND (",
                " COALESCE(customer_name, '') LIKE '%". safe_input($requestData["columns"][2]['search']['value']) ."%' ",
                " OR customer_phone LIKE" => $requestData["columns"][3]['search']['value'] . '%',
                " OR COALESCE(person_full_name, '') LIKE" => '%' . $requestData["columns"][2]['search']['value'] . '%',
                " OR person_phone LIKE" => $requestData["columns"][3]['search']['value'] . '%',
                ")",
                " AND case_priority"    => $requestData["columns"][4]['search']['value'],
                " AND case_type"        => $requestData["columns"][5]['search']['value'],
                " AND case_status"      => $requestData["columns"][6]['search']['value'],
                " AND case_assigned_to" => $requestData["columns"][7]['search']['value'],
                " AND case_belongs_to"  => $requestData["columns"][8]['search']['value'],
                " AND case_added_by_agent"  => $requestData["columns"][10]['search']['value'],
                " {$dateFilter}"
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
            "table"     => "cases as cases",
            "fields"    => "case_id, case_datetime, case_title, case_priority, case_type, case_status, last_reply, 
                            case_assigned_to, concat(assigned_to_employee.emp_firstname, ' ', assigned_to_employee.emp_lastname) as case_assign_to_name, 
                            case_belongs_to, concat(belongs_to.emp_firstname, ' ', belongs_to.emp_lastname) as case_belongs_to_name,
                            case_added_by_agent, concat(posted_by_employee.emp_firstname, ' ', posted_by_employee.emp_lastname) as posted_by_name, 
                            if(person_full_name is null, 
                                concat(customer_name, '<br/>', customer_phone),
                                concat(person_full_name, '<br/>', person_phone, ', ', person_email)
                            ) as requester",
            "join"      => array(
                "left join {$table_prefix}users as assigned_user on assigned_user.user_id = case_assigned_to",
                "left join {$table_prefix}employees as assigned_to_employee on assigned_to_employee.emp_id = assigned_user.user_emp_id",

                "left join {$table_prefix}users as posted_user on posted_user.user_id = case_added_by_agent",
                "left join {$table_prefix}employees as posted_by_employee on posted_by_employee.emp_id = posted_user.user_emp_id",

                "left join {$table_prefix}employees as belongs_to on belongs_to.emp_id = case_belongs_to",
                "left join {$table_prefix}persons on person_id = case_person",
                "left join {$table_prefix}customers on customer_id = case_customer",
                "left join (select
                        max(reply_datetime) as last_reply,
                        reply_case_id
                    from {$table_prefix}case_replies
                    where is_trash = 0
                    group by reply_case_id
                ) as replies on replies.reply_case_id = case_id"
            ),
            "where"     => array(
                "cases.is_trash = 0"
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
    if($getData) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            
            $caseStatus = "";
            if($value["case_status"] === "Pending") {
                $caseStatus = "<span class='text-center btn btn-sm btn-danger'>Pending</span>";
            } else if($value["case_status"] === "Solved") {
                $caseStatus = "<span class='text-center btn btn-sm btn-success'>Solved</span>";
            } else {
                $caseStatus = "<span class='text-center btn btn-sm btn-info'>{$value["case_status"]}</span>";
            }

            $casePriority = "";
            if($value["case_priority"] === "Critical") {
                $casePriority = "<span class='text-center btn btn-sm btn-danger'>Critical</span>";
            } else if($value["case_priority"] === "High") {
                $casePriority = "<span class='text-center btn btn-sm btn-warning'>High</span>";
            }  else if($value["case_priority"] === "Medium") {
                $casePriority = "<span class='text-center btn btn-sm btn-info'>Medium</span>";
            } else {
                $casePriority = "<span class='text-center btn btn-sm btn-light'>Low</span>";
            }

            $allNestedData[] = "";
            $allNestedData[] = $value["case_datetime"];
            $allNestedData[] = '<a href="'. full_website_address() .'/customer-support/case-list/?case_id='. $value["case_id"] .'"> '. $value["case_title"] .' </a>';
            $allNestedData[] = $value["requester"];
            $allNestedData[] = $casePriority;
            $allNestedData[] = $value["case_type"];
            $allNestedData[] = $caseStatus;
            $allNestedData[] = $value["case_assign_to_name"];
            $allNestedData[] = $value["case_belongs_to_name"];
            $allNestedData[] = $value["last_reply"] === null ? 'No Reply' : time_elapsed_string($value["last_reply"] );
            $allNestedData[] = $value["posted_by_name"];
            
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        <li><a href="'. full_website_address() .'/customer-support/case-list/?case_id='. $value["case_id"] .'"><i class="fa fa-plus-circle"></i> Add Reply</a></li>
                                        <li><a class="'. ( current_user_can("customer_support_cases.Delete") ? "" : "restricted " ) .'deleteEntry" href="'. full_website_address() . '/xhr/?module=customer-support&page=deleteCase" data-to-be-deleted="'. $value["case_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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


/************************** New Case **********************/
if(isset($_GET['page']) and $_GET['page'] == "deleteCase") {

    if(current_user_can("customer_support_cases.Delete") !== true) {
        echo '{
            "title": "Sorry!",
            "text": "you do not have permission to delete case.",
            "showConfirmButton": true,
            "showCloseButton": true,
            "toast": false,
            "icon": "error"
        }';
        return;
    }

    $deleteData = easyDelete(
        "cases",
        array(
            "case_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {
        echo '{
            "title": "The case has been deleted successfully."
        }';
    } 

}


/************************** New Case **********************/
if(isset($_GET['page']) and $_GET['page'] == "deleteCaseReply") {

    if(current_user_can("customer_support_cases.Delete") !== true) {
        echo '{
            "title": "Sorry!",
            "text": "you do not have permission to delete case reply.",
            "showConfirmButton": true,
            "showCloseButton": true,
            "toast": false,
            "icon": "error"
        }';
        return;
    }

    $deleteData = easyDelete(
        "case_replies",
        array(
            "reply_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {
        echo '{
            "title": "The reply has been deleted successfully."
        }';
    } 

}

/***************** update case type/ mode ****************/
if(isset($_GET['page']) and $_GET['page'] == "updateCaseReplyType") {


    $replyMode = easySelectA(array(
        "table"     => "case_replies",
        "fields"    => "reply_type",
        "where"     => array(
            "reply_id"    => $_POST["datatoUpdate"]
        )
    ))["data"][0]["reply_type"];
    

    easyUpdate(
        "case_replies",
        array(
            "reply_type"    => $replyMode === "Private" ? 'Public' : 'Private'
        ),
        array(
            "reply_id"    => $_POST["datatoUpdate"]
        )
    );

    echo '{
        "title": "Updated successfully. Please reload to see the changes."
    }';

}


/*************************** case List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "updateCase") {

    easyUpdate(
        "cases",
        array(
            "case_title"        => $_POST["caseTitle"],
            "case_priority"     => $_POST["casePriority"],
            "case_type"         => $_POST["caseType"],
            "case_status"       => empty($_POST["caseStatus"]) ? 'Open' : $_POST["caseStatus"],
            "case_site"         => $_POST["caseSite"],
            "case_customer"     => empty($_POST["caseCustomer"]) ? NULL : $_POST["caseCustomer"],
            "case_person"       => empty($_POST["casePerson"]) ? NULL : $_POST["casePerson"],
            "case_assigned_to"  => empty($_POST["caseAssignTo"]) ? NULL : $_POST["caseAssignTo"],
            "case_belongs_to"   => empty($_POST["caseBelongsTo"]) ? NULL : $_POST["caseBelongsTo"]
        ),
        array(
            "case_id"   => $_POST["case_id"]
        )
    );


    echo '{
        "title": "Case properties has been updated successfully."
    }';

}

/*************************** case List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "smsList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "sms_id",
        "send_to",
        "sms_text",
        "status",
        "emp_firstname"
    );
    
    // Count Total recrods

    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "cases",
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
            "table"     => "sms_sender as sms_sender",
            "fields"    => "send_to, send_time, sms_text, status, concat(emp_firstname, ' ', emp_lastname) as sms_sent_by",
            "join"      => array(
                "left join {$table_prefix}users on send_by = user_id",
                "left join {$table_prefix}employees on user_emp_id = emp_id",
            ),
            "where"     => array(
                "sms_sender.is_trash = 0 and send_by"   => $_SESSION["uid"],
                " and ( send_to LIKE" => $requestData['search']['value'] . "%",
                " or emp_firstname LIKE" => $requestData['search']['value'] . "%",
                ")"
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
            "table"     => "sms_sender as sms_sender",
            "fields"    => "send_to, send_time, sms_text, status, concat(emp_firstname, ' ', emp_lastname) as sms_sent_by",
            "join"      => array(
                "left join {$table_prefix}users on send_by = user_id",
                "left join {$table_prefix}employees on user_emp_id = emp_id",
            ),
            "where"     => array(
                "sms_sender.is_trash = 0 and send_by"   => $_SESSION["uid"]
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
    if($getData) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            

            $allNestedData[] = "";
            $allNestedData[] = $value["send_time"];
            $allNestedData[] = $value["send_to"];
            $allNestedData[] = $value["sms_text"];
            $allNestedData[] = $value["status"];
            $allNestedData[] = $value["sms_sent_by"];
            
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


/*************************** getCallerDetails ***********************/
if(isset($_GET['page']) and $_GET['page'] == "addNewNote") {

    $insertNote = easyInsert(
       "notes",
       array(
           "note_type"          => $_POST["type"],
           "note_text"          => $_POST["note"],
           "note_created_by"    => $_SESSION["uid"]
       )
    );

}


/************************** New Representative **********************/
if(isset($_GET['page']) and $_GET['page'] == "newRepresentative") {
  
    // Include the modal header
    modal_header("Add New Customer Care Representative", full_website_address() . "/xhr/?module=customer-support&page=addNewRepresentative");
    
    ?>
      <div class="box-body">

        <div class="form-group required">
            <label for="representativeUser"><?= __("User:"); ?></label>
            <select name="representativeUser" id="representativeUser" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=userList" style="width: 100%;" required>
                <option value=""><?= __("Select user"); ?>....</option>
            </select>
        </div>
        <div class="form-group required">
            <label for="sipUsername">SIP Username</label>
            <input type="text" name="sipUsername" id="sipUsername" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="sipPassword">SIP Password</label>
            <input type="password" name="sipPassword" id="sipPassword" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="sipDomain">SIP Domain</label>
            <input type="text" name="sipDomain" id="sipDomain" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="sipSocketAddress">SIP Socket Address</label>
            <input type="text" name="sipSocketAddress" id="sipSocketAddress" class="form-control" required>
        </div>

      </div>
      <!-- /Box body-->
  
    <?php
  
    // Include the modal footer
    modal_footer();
  
}

if(isset($_GET['page']) and $_GET['page'] == "addNewRepresentative") {

    if(empty($_POST["representativeUser"])) {
        return _e("Please select user");
    } elseif(empty($_POST["sipUsername"])) {
        return _e("Please enter SIP username");
    } elseif(empty($_POST["sipPassword"])) {
        return _e("Please enter SIP password");
    } elseif(empty($_POST["sipDomain"])) {
        return _e("Please enter SIP domain");
    } elseif(empty($_POST["sipSocketAddress"])) {
        return _e("Please enter SIP socket address");
    }

    // Insert sip credentials
    $insertSip = easyInsert(
        "sip_credentials",
        array(
            "sip_representative"    => $_POST["representativeUser"],
            "sip_username"          => $_POST["sipUsername"],
            "sip_password"          => $_POST["sipPassword"],
            "sip_domain"            => $_POST["sipDomain"],
            "sip_websocket_addr"    => $_POST["sipSocketAddress"],
            "sip_created_by"        => $_SESSION["uid"]
        )
    );

    if($insertSip !== false) {
        _s("Successfully addded");
    } else {
        _e($insertSip);
    }

}


/*************************** case List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "callCenterRepresentativeList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "emp_firstname",
        "sip_username",
        "sip_domain",
        "sip_websocket_addr"
    );
    
    // Count Total recrods

    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "sip_credentials",
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
            "table"     => "sip_credentials as sip_credentials",
            "fields"    => "sip_id, sip_username, sip_domain, sip_websocket_addr, concat(emp_firstname, ' ', emp_lastname) as callCenterRepresentative",
            "join"      => array(
                "left join {$table_prefix}users on sip_representative = user_id",
                "left join {$table_prefix}employees on user_emp_id = emp_id",
            ),
            "where"     => array(
                "sip_credentials.is_trash = 0",
                " or emp_firstname LIKE" => $requestData['search']['value'] . "%",
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
            "table"     => "sip_credentials as sip_credentials",
            "fields"    => "sip_id, sip_username, sip_domain, sip_websocket_addr, concat(emp_firstname, ' ', emp_lastname) as callCenterRepresentative",
            "join"      => array(
                "left join {$table_prefix}users on sip_representative = user_id",
                "left join {$table_prefix}employees on user_emp_id = emp_id",
            ),
            "where"     => array(
                "sip_credentials.is_trash = 0"
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
    if($getData) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["callCenterRepresentative"];
            $allNestedData[] = $value["sip_username"];
            $allNestedData[] = $value["sip_domain"];
            $allNestedData[] = $value["sip_websocket_addr"];
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        <li><a data-toggle="modal" data-target="#modalDefault" href="'. full_website_address() .'/xhr/?module=customer-support&page=editCCRepresentative&id='. $value["sip_id"] .'"><i class="fa fa-edit"></i> Edit</a></li>
                                        <li><a class="'. ( current_user_can("customer_support_representative.Delete") ? "" : "restricted " ) .'deleteEntry" href="'. full_website_address() . '/xhr/?module=customer-support&page=deleteCustomerCareRepresentative" data-to-be-deleted="'. $value["sip_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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



/***************** Delete CustomerCareRepresentative ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteCustomerCareRepresentative") {

    if(current_user_can("customer_support_representative.Delete") !== true) {
        echo '{
            "title": "Sorry!",
            "text": "you do not have permission to delete this accounts.",
            "showConfirmButton": true,
            "showCloseButton": true,
            "toast": false,
            "icon": "error"
        }';
        return;
    }

    $deleteData = easyDelete(
        "sip_credentials",
        array(
            "sip_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {
        echo '{
            "title": "The accounts has been deleted successfully."
        }';
    } 
}


/************************** New Representative **********************/
if(isset($_GET['page']) and $_GET['page'] == "editCCRepresentative") {
  
    // Include the modal header
    modal_header("Edit Customer Care Representative", full_website_address() . "/xhr/?module=customer-support&page=updateRepresentative");

    // Select
    $selectCCR = easySelectA(array(
        "table"     => "sip_credentials as sip_credentials",
        "fields"    => "sip_id, sip_representative, sip_username, sip_password, sip_domain, sip_websocket_addr, concat(emp_firstname, ' ', emp_lastname) as callCenterRepresentative",
        "join"      => array(
            "left join {$table_prefix}users on sip_representative = user_id",
            "left join {$table_prefix}employees on user_emp_id = emp_id",
        ),
        "where"     => array(
            "sip_credentials.sip_id"    => $_GET["id"]
        )
    ))["data"][0];
    
    ?>
      <div class="box-body">

        <div class="form-group required">
            <label for="representativeUser"><?= __("User:"); ?></label>
            <select name="representativeUser" id="representativeUser" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=userList" style="width: 100%;" required>
                <option value=""><?= __("Select user"); ?>....</option>
                <option selected value="<?php echo $selectCCR["sip_representative"]; ?>"><?php echo $selectCCR["callCenterRepresentative"]; ?></option>
            </select>
        </div>
        <div class="form-group required">
            <label for="sipUsername">SIP Username</label>
            <input type="text" name="sipUsername" id="sipUsername" value="<?php echo $selectCCR["sip_username"]; ?>" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="sipPassword">SIP Password</label>
            <input type="password" name="sipPassword" id="sipPassword" value="<?php echo $selectCCR["sip_password"]; ?>" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="sipDomain">SIP Domain</label>
            <input type="text" name="sipDomain" id="sipDomain" value="<?php echo $selectCCR["sip_domain"]; ?>" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="sipSocketAddress">SIP Socket Address</label>
            <input type="text" name="sipSocketAddress" id="sipSocketAddress" value="<?php echo $selectCCR["sip_websocket_addr"]; ?>" class="form-control" required>
        </div>
        <input type="hidden" name="sip_id" value="<?php echo safe_entities($_GET["id"]); ?>">

      </div>
      <!-- /Box body-->
  
    <?php
  
    // Include the modal footer
    modal_footer();
  
}

if(isset($_GET['page']) and $_GET['page'] == "updateRepresentative") {

    if(empty($_POST["representativeUser"])) {
        return _e("Please select user");
    } elseif(empty($_POST["sipUsername"])) {
        return _e("Please enter SIP username");
    } elseif(empty($_POST["sipPassword"])) {
        return _e("Please enter SIP password");
    } elseif(empty($_POST["sipDomain"])) {
        return _e("Please enter SIP domain");
    } elseif(empty($_POST["sipSocketAddress"])) {
        return _e("Please enter SIP socket address");
    }

    // Insert sip credentials
    $updateSip = easyUpdate(
        "sip_credentials",
        array(
            "sip_representative"    => $_POST["representativeUser"],
            "sip_username"          => $_POST["sipUsername"],
            "sip_password"          => $_POST["sipPassword"],
            "sip_domain"            => $_POST["sipDomain"],
            "sip_websocket_addr"    => $_POST["sipSocketAddress"],
            "sip_created_by"        => $_SESSION["uid"]
        ), 
        array(
            "sip_id"    => $_POST["sip_id"]
        )
    );

    if($updateSip !== false) {
        _s("Successfully updated");
    } else {
        _e($updateSip);
    }

}


/*************************** Call List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "noteList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "note_id",
        "note_type",
        "note_text"
    );
    
    // Count Total recrods

    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "notes",
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
            "table"     => "notes as notes",
            "fields"    => "note_id, note_type, note_text",
            "where"     => array(
                "notes.is_trash = 0 and note_created_by"  => $_SESSION["uid"],
                " and note_text LIKE" => $requestData['search']['value'] . "%",
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
            "table"     => "notes as notes",
            "fields"    => "note_id, note_type, note_text",
            "where"     => array(
                "notes.is_trash = 0 and note_created_by"  => $_SESSION["uid"]
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
    if($getData) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];

            $allNestedData[] = "";
            $allNestedData[] = $value["note_id"];
            $allNestedData[] = $value["note_type"];
            $allNestedData[] = $value["note_text"];
             $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        <li><a class="'. ( current_user_can("customer_support_note.Edit") ? "" : "restricted " ) .'" data-toggle="modal" href="'. full_website_address() .'/xhr/?module=customer-support&page=editNote&id='. $value["note_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                        <li><a class="'. ( current_user_can("customer_support_note.Delete") ? "" : "restricted " ) .'deleteEntry" href="'. full_website_address() . '/xhr/?module=customer-support&page=deleteNote" data-to-be-deleted="'. $value["note_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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


/***************** Delete Notes or Feedback ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteNote") {

    if(current_user_can("customer_support_note.Delete") !== true) {
        echo '{
            "title": "Sorry!",
            "text": "you do not have permission to delete note or feedback.",
            "showConfirmButton": true,
            "showCloseButton": true,
            "toast": false,
            "icon": "error"
        }';
        return;
    }

    $deleteData = easyDelete(
        "notes",
        array(
            "note_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {
        echo '{
            "title": "The note or feedback has been deleted successfully."
        }';
    } 
}


/************************** New Case **********************/
if(isset($_GET['page']) and $_GET['page'] == "editNote") {

    // select note
    $note = easySelectA(array(
        "table"     => "notes",
        "fields"    => "note_type, note_text",
        "where"     => array(
            "note_id"   => $_GET["id"]
        )
    ))["data"][0];
  
    // Include the modal header
    modal_header("Edit {$note['note_type']}", full_website_address() . "/xhr/?module=customer-support&page=updateNote");
    
    ?>
      <div class="box-body">

        <div class="form-group required">
            <label for="noteText"><?php echo __(  ucfirst($note['note_type']) . " text"); ?></label>
            <textarea name="noteText" id="noteText" cols="30" rows="6" class="form-control"> <?php echo $note['note_text']; ?> </textarea>
        </div>
        <input type="hidden" name="note_id" value="<?php echo safe_entities($_GET["id"]); ?>">
        
      </div>
      <!-- /Box body-->
  
    <?php
  
    // Include the modal footer
    modal_footer();
  
}


if(isset($_GET['page']) and $_GET['page'] == "updateNote") {

    // update note
    $updateNote = easyUpdate(
        "notes",
        array(
            "note_text"     => $_POST["noteText"],
        ),
        array(
            "note_id"   => $_POST["note_id"]
        )
    );

    if($updateNote !== false) {
        echo _s("Successfully updated");
    }

}



/************************** new Voice Message Entry **********************/
if(isset($_GET['page']) and $_GET['page'] == "newVoiceMessageEntry") {
  
    // Include the modal header
    modal_header("Add New Voice Messae", full_website_address() . "/xhr/?module=customer-support&page=addNewVoiceMessageEntry");
    
    ?>
      <div class="box-body">

        <div class="form-group required">
            <label for="vmDescription">Description:</label>
            <input type="text" name="vmDescription" id="vmDescription" class="form-control">
        </div>
        <div class="form-group required">
            <label for="vmRecord">Record:</label>
            <input type="file" name="vmRecord" id="vmRecord" class="form-control" accept="audio/*">
        </div>
        <div class="form-group required">
            <label for="vmNumbers">Numbers: </label>
            <textarea name="vmNumbers" id="vmNumbers" cols="30" rows="8" placeholder="Please enter numbers with comma seperated" class="form-control"></textarea>
        </div>
        
      </div>
      <!-- /Box body-->
  
    <?php
  
    // Include the modal footer
    modal_footer();
  
}


if(isset($_GET['page']) and $_GET['page'] == "addNewVoiceMessageEntry") {

    if( empty($_POST["vmDescription"]) ) {
        return _e("Please enter description");
    } else if( $_FILES["vmRecord"]["size"] < 1 ) {
        return _e("Please select record");
    } elseif( empty($_POST["vmNumbers"]) ) {
        return _e("Please enter numbers");
    }


    // Upload the Record
    $vmRecord = NULL;
    if($_FILES["vmRecord"]["size"] > 0) {
 
            $vmRecord = easyUpload(
                $_FILES["vmRecord"], 
                "media/sounds/voice-message/",
                $_POST["vmDescription"]."_". time(),
                "audio"
            );
 
            if(!isset($vmRecord["success"])) {
                return _e($vmRecord);
            } else {
                $vmRecord = $vmRecord["fileName"];
            }
         
    }

    // Insert case
    $insertVoiceMessage = easyInsert(
        "voice_message",
        array(
            "vm_description"    => $_POST["vmDescription"],
            "vm_record"         => $vmRecord,
            "vm_added_on"       => date("Y-m-d H:i:s"),
            "vm_added_by"       => $_SESSION["uid"]
        ),
        array(),
        true
    );

    if( isset($insertVoiceMessage["status"]) and $insertVoiceMessage["status"] === "success" ) {

        // insert numbers
        $numbers = explode(",", $_POST["vmNumbers"] );

        $insertNumbers = "INSERT INTO {$table_prefix}calls(
            call_type,
            call_status,
            client_identity,
            representative,
            vm_id
        ) VALUES ";

        foreach($numbers as $number) {

            $insertNumbers .= "(
                'Voice Message',
                'Pending',
                '{$number}',
                '{$_SESSION["uid"]}',
                '{$insertVoiceMessage["last_insert_id"]}'
            ),";

        }

        // Insert numbers in call table
        $conn->query(substr_replace($insertNumbers, ";", -1, 1));

        echo _s("Case added successfully");

    }
    
}


/*************************** Representative List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "voiceMessageList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "vm_added_on",
        "vm_description",
        "",
        "vm_status"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "voice_message",
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
            "table"     => "voice_message",
            "fields"    => "vm_id, vm_description, vm_record, vm_status, vm_added_on",
            "where"     => array(
                "is_trash = 0",
                " and vm_description LIKE" => "%". $requestData['search']['value'] . "%",
            ),
            "orderby"   => array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit" => array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));
       

        $totalFilteredRecords = $getData ? $getData["count"] : 0;

    } else { // Get data withouth search

        $getData = easySelectA(array(
            "table"     => "voice_message",
            "fields"    => "vm_id, vm_description, vm_record, vm_status, vm_added_on",
            "where"     => array(
                "is_trash = 0"
            ),
            "orderby"   => array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit" => array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));

    }

    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['count']) and $getData['count'] > 0) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["vm_added_on"];
            $allNestedData[] = $value["vm_description"];
            $allNestedData[] = "<audio controls src='". full_website_address() ."/assets/upload/media/sounds/voice-message/{$value["vm_record"]}'></aduio>";
            $allNestedData[] = $value["vm_status"];
            $allNestedData[] = "<button class='btn btn-primary startSendingVoiceMessage' value='{$value["vm_id"]}'>Start Sending</button> ";
            
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



/*************************** getVoiceMessageContact List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "getVoiceMessageContact") {

    // Update Voice Message to mark as sending
    easyUpdate(
        "voice_message",
        array(
            "vm_status" => "sending"
        ),
        array(
            "vm_id" => $_POST["id"]
        )
    );

    // Select recrods etc
    $record = easySelectA(array(
        "table"     => "voice_message",
        "fields"    => "vm_description, vm_record",
        "where"     => array(
            "vm_id" => $_POST["id"]
        )
    ))["data"][0];

    // Select contacts
    $contacts = easySelectA(array(
        "table"     => "calls",
        "fields"    => "client_identity",
        "where"     => array(
            "is_trash = 0 and call_status = 'Pending' and vm_id"  => $_POST["id"]
        )
    ));

    $data = array(
        "record"        => $record["vm_record"],
        "description"   => $record["vm_description"],
        "contacts"      => array()
    );

    if($contacts !== false) {
        foreach($contacts["data"] as $num) {
            array_push( $data["contacts"], $num["client_identity"] );
        }
    }
    

    echo json_encode($data);

}


if(isset($_GET['page']) and $_GET['page'] == "updateCallLog") {

    // update the calls log
    easyUpdate(
        "calls",
        array(
            "call_datetime"     => date("Y-m-d h:i:s"),
            "call_direction"    => "Outgoing",
            "call_status"       => $_POST["status"],
            "duration"          => $_POST["duration"]
        ),
        array(
            "vm_id"                 => $_POST["vm_id"],
            " and client_identity"  => $_POST["number"],
        )
    );

}


/*************************** agentWiseCallStatistics List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "agentWiseCallStatistics") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "emp_firstname",
        "talk_time",
        "total_answered",
        "total_not_answered",
        ""
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "sip_credentials",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
      $requestData['length'] = $totalRecords;
    }
 
    if(!empty($requestData["search"]["value"]) or !empty($requestData["columns"][1]['search']['value']) ) {  // get data with search

        $dateFilter = "";
        if(!empty($requestData["columns"][1]['search']['value'])) {
            $dateRange = explode(" - ", safe_input($requestData["columns"][1]['search']['value']));
            $dateFilter = "and date(call_datetime) BETWEEN '{$dateRange[0]}' and '{$dateRange[1]}'";
        }

        $getData = easySelectA(array(
            "table"     => "calls as calls",
            "fields"    => "emp_firstname, emp_lastname, emp_positions, 
                            sum( case when call_status = 'Answered' then duration end) as talk_time,
                            count(*) as total_call,
                            count( case when call_status = 'Answered' then duration end) as total_answered,
                            count( case when call_status = 'Missed' then duration end) as total_missed,
                            count( case when call_status = 'Not Answered' then duration end) as total_not_answered,
                            count( case when call_status = 'Busy' then duration end) as total_busy,
                            count( case when call_status = 'Unreachable' then duration end) as total_Unreachable
                            ",
            "join"      => array(
                "left join {$table_prefix}users on representative = user_id",
                "left join {$table_prefix}employees on user_emp_id = emp_id",
            ),
            "where"     => array(
                "calls.is_trash = 0 and emp_firstname like '{$requestData["search"]["value"]}%' $dateFilter"
            ),
            "orderby"   => array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "groupby"   => "representative",
            "limit" => array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));

        $totalFilteredRecords = $getData ? $getData["count"] : 0;

    } else { // Get data withouth search

        $getData = easySelectA(array(
            "table"     => "calls as calls",
            "fields"    => "emp_firstname, emp_lastname, emp_positions, 
                            sum( case when call_status = 'Answered' then duration end) as talk_time,
                            count(*) as total_call,
                            count( case when call_status = 'Answered' then duration end) as total_answered,
                            count( case when call_status = 'Missed' then duration end) as total_missed,
                            count( case when call_status = 'Not Answered' then duration end) as total_not_answered,
                            count( case when call_status = 'Busy' then duration end) as total_busy,
                            count( case when call_status = 'Unreachable' then duration end) as total_Unreachable
                            ",
            "join"      => array(
                "left join {$table_prefix}users on representative = user_id",
                "left join {$table_prefix}employees on user_emp_id = emp_id",
            ),
            "where"     => array(
                "calls.is_trash = 0"
            ),
            "orderby"   => array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "groupby"   => "representative",
            "limit" => array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));

    }

    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['count']) and $getData['count'] > 0) {
        
        foreach($getData['data'] as $key => $value) {

            // ATT = Average Talk Time
            $att = (empty($value["talk_time"]) ? 1 : $value["talk_time"]) / (empty($value["total_answered"]) ? 1 : $value["total_answered"]);
            $attSeconds = round($att) % 60;
            $att = round(( $att - $attSeconds ) / 60);

            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = "{$value["emp_firstname"]} {$value["emp_lastname"]}";
            $allNestedData[] = round($value["talk_time"] / 60) . " min(s)";
            $allNestedData[] = $att .".". $attSeconds;
            $allNestedData[] = $value["total_answered"];
            $allNestedData[] = $value["total_not_answered"];
            $allNestedData[] = $value["total_busy"];
            $allNestedData[] = $value["total_missed"];
            $allNestedData[] = $value["total_Unreachable"];
            $allNestedData[] = $value["total_call"];
            
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


/*************************** Person List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "personListForCallCenter") {
    
    $requestData = $_REQUEST;
    $getData = [];
  
    // List of all columns name for sorting
    $columns = array(
        "",
        "person_full_name",
        "person_designation",
        "institute_name",
        "person_phone",
        "person_address"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "persons",
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
            "persons as person",
            "person_id, person_full_name, person_type, person_designation, institute_name, person_phone, person_address, 
            upazila_name, district_name, division_name",
            array (
              "left join {$table_prefix}districts on person_district = district_id",
              "left join {$table_prefix}divisions on person_division = division_id",
              "left join {$table_prefix}upazilas on person_upazila = upazila_id",
              "left join {$table_prefix}institute on person_institute = institute_id",
            ),
            array (
              "person.is_trash = 0 and ( person_full_name LIKE '". safe_input($requestData['search']['value']) ."%' ",
              " OR person_phone LIKE" => $requestData['search']['value'] . "%",
              " OR person_email LIKE" => $requestData['search']['value'] . "%",
              " OR institute_name LIKE" => $requestData['search']['value'] . "%",
              " )"
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
            "persons as person",
            "person_id, person_full_name, person_type, person_designation, institute_name, person_phone, person_address, upazila_name, district_name, division_name",
            array (
              "left join {$table_prefix}districts on person_district = district_id",
              "left join {$table_prefix}divisions on person_division = division_id",
              "left join {$table_prefix}upazilas on person_upazila = upazila_id",
              "left join {$table_prefix}institute on person_institute = institute_id"
            ),
            array("person.is_trash = 0"),
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
            $allNestedData[] = "<b>{$value['person_full_name']}</b>, {$value['person_type']} <br/>{$value['person_designation']} {$value['institute_name']}";
            $allNestedData[] = "{$value['person_phone']}";
            $allNestedData[] = "{$value['person_address']}, {$value['upazila_name']}, {$value['district_name']}, {$value['division_name']}";
            $allNestedData[] = '<a data-toggle="modal" href="' . full_website_address() . '/xhr/?module=marketing&page=viewSpecimenProduct&id=' . $value["person_id"] . '"  data-target="#modalDefault">View Specimen</a>';
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        <li><a class="'. ( current_user_can("persons.Edit") ? "" : "restricted " ) .'" data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=marketing&page=editPerson&id='. $value["person_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit Person</a></li>
                                        <li><a class="'. ( current_user_can("persons.Delete") ? "" : "restricted " ) .'deleteEntry" href="'. full_website_address() . '/xhr/?module=marketing&page=deletePerson" data-to-be-deleted="'. $value["person_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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