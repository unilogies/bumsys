<?php


//************************ Create new group permission ************************* */
if(isset($_GET['page']) and $_GET['page'] == "newGroupPermission") {


    // Include the modal header
    modal_header("New Group Permission", full_website_address() . "/xhr/?module=settings&page=addNewGroupPermission");
    
    ?>
            <div class="box-body">
                <div class="form-group">
                    <label for="groupName"><?= __("Group name:"); ?></label>
                    <input type="text" name="groupName" class="form-control" id="groupName" placeholder="Enter group Name">
                </div>

                <style>
                
                .tableBodyScroll tbody {
                    display: block;
                    max-height: 600px;
                    overflow-y: scroll;
                }
                .tableBodyScroll thead, .tableBodyScroll tbody tr {
                    display: table;
                    width: 100%;
                    table-layout: fixed;
                }
                .tableBodyScroll thead {
                    width: calc( 100% - 1.1em );
                }

                </style>

                <table class="table table-bordered tableBodyScroll">
                    <thead>
                        <tr>
                            <th class="col-md-4"><?= __("Permission"); ?></th>
                            <th class="col-md-2 text-center">View</th>
                            <th class="col-md-2 text-center">Add</th>
                            <th class="col-md-2 text-center">Edit</th>
                            <th class="col-md-2 text-center">Delete</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php                
                            require_once DIR_CORE . "permissions.php";

                            foreach($defaultPermission as $permName => $permValue) {

                                $permDisplayName = ucfirst(str_replace("_", " ", $permName));

                                echo "<tr>";
                                    echo "<td class='col-md-4'>$permDisplayName</td>";

                                        foreach($default_role as $value) {

                                            // If not specified the show all
                                            if(!is_array($permValue)) {
                                                
                                                echo "<td class='col-md-2 text-center'>
                                                    <input type='checkbox' class='square' value='$permName.$value' name='groupPermission[]'>
                                                </td>";

                                            } elseif( in_array($value, $permValue ) ) {

                                                echo "<td class='col-md-2 text-center'>
                                                    <input type='checkbox' class='square' value='$permName.$value' name='groupPermission[]'>
                                                </td>";

                                            } else {

                                                echo "<td class='col-md-2'></td>";

                                            }

                                        }

                                echo "</tr>";
                                
                            }
                
                        ?>

                    </tbody>

                </table>


               
            </div>
            

    <?php

    // Include the modal footer
    modal_footer();

    return;

}


// Add new user group page
if(isset($_GET['page']) and $_GET['page'] == "addNewGroupPermission") {


    if(empty($_POST["groupName"])) {
       return _e("Please Enter group Name");
    } else if (empty($_POST["groupPermission"])) {
       return _e("Please select at least one permission.");
    }
    
    $returnMsg = easyInsert(
        "user_group", // Table name
        array( // Fileds Name and its value
            "group_name" => $_POST["groupName"],
            "group_permission" => serialize($_POST['groupPermission'])
        ),
        array( // No duplicate allow.
            "group_name" => $_POST["groupName"]
        )
    );

    if($returnMsg === true) {
        _s("New Group added successfully.");
    } else {
       _e($returnMsg);
    }

}

// Delete Group
if(isset($_GET['page']) and $_GET['page'] == "deleteGroup") {

    $deleteUserGroup = easyDelete(
        "user_group",
        array(
            "group_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteUserGroup === true) {
        echo 1;
    } 

    return;
}

if(isset($_GET['page']) and $_GET['page'] == "groupList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "group_id",
        "group_name",
        "group_permission"
    );

    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
    "table" => "user_group",
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
            "table"     => "user_group",
            "fields"    => "group_id, group_name",
            "where"     => array(
                "is_trash = 0",
                " AND group_id" => $requestData['search']['value'],
                " OR group_name LIKE" => $requestData['search']['value'] . "%",
            ),
            "orderby"   => array(
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit"     => array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));

        $totalFilteredRecords = $getData ? $getData["count"] : 0;

    } else { // Get data withouth search

        $getData = easySelectA(array(
            "table"     => "user_group",
            "fields"    => "group_id, group_name",
            "where"     => array(
                "is_trash = 0"
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
    
    // Check if there have more then zero data
    if(isset($getData['count']) and $getData['count'] > 0) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = $value["group_id"];
            $allNestedData[] = $value["group_name"];
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=true&module=settings&page=editGroup&id='. $value["group_id"] .'"  data-target="#modalDefaultMdm"><i class="fa fa-edit"></i> Edit</a></li>
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=settings&page=deleteGroup" data-to-be-deleted="'. $value["group_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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
    return;
}



//************************ Edit user permission ************************* */
if(isset($_GET['page']) and $_GET['page'] == "editGroup") {

    $selectGroup = easySelect(
        "user_group",
        "*",
        array(),
        array(
            "group_id" => $_GET['id']
        )
    );

    $group_name = $selectGroup["data"][0]["group_name"];
    $group_permission = unserialize( html_entity_decode($selectGroup["data"][0]["group_permission"]) ) ; // Permission Array

    // Include the modal header
    modal_header("Edit Users Group", full_website_address() . "/xhr/?module=settings&page=updategroup");
    
    ?>
            <div class="box-body">
                <div class="form-group">
                    <label for="groupName">Group name</label>
                    <input type="text" name="groupName" class="form-control" id="groupName" value="<?php echo $group_name; ?>" placeholder="Enter group Name">
                </div>
                <input type="hidden" name="group_id" value="<?php echo htmlentities($_GET['id']); ?>">

                <style>
                
                .tableBodyScroll tbody {
                    display: block;
                    max-height: 550px;
                    overflow-y: scroll;
                }
                .tableBodyScroll thead, .tableBodyScroll tbody tr {
                    display: table;
                    width: 100%;
                    table-layout: fixed;
                }
                .tableBodyScroll thead {
                    width: calc( 100% - 1.1em );
                }

                </style>

                <table class="table table-bordered tableBodyScroll">
                    <thead>
                        <tr>
                            <th class="col-md-4">Permission</th>
                            <th class="col-md-2 text-center">View</th>
                            <th class="col-md-2 text-center">Add</th>
                            <th class="col-md-2 text-center">Edit</th>
                            <th class="col-md-2 text-center">Delete</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php 

                            foreach($defaultPermission as $permName => $permValue) {

                                $permDisplayName = ucfirst(str_replace("_", " ", $permName));

                                echo "<tr>";
                                    echo "<td class='col-md-4'>$permDisplayName</td>";

                                        foreach($default_role as $value) {

                                            $checked = in_array("$permName.$value", $group_permission) ? "checked" : "";

                                            // If not specified the show all
                                            if(!is_array($permValue)) {
                                                
                                                echo "<td class='col-md-2 text-center'>
                                                    <input $checked type='checkbox' class='square' value='$permName.$value' name='groupPermission[]'>
                                                </td>";

                                            } elseif( in_array($value, $permValue ) ) {

                                                echo "<td class='col-md-2 text-center'>
                                                    <input $checked type='checkbox' class='square' value='$permName.$value' name='groupPermission[]'>
                                                </td>";

                                            } else {

                                                echo "<td class='col-md-2'></td>";

                                            }
                                        }
                                echo "</tr>";
                            }
                
                        ?>

                    </tbody>

                </table>
               
            </div>

    <?php

    // Include the modal footer
    modal_footer();

    return;

}


//*********************************  Group Update ******************** */
if(isset($_GET['page']) and $_GET['page'] == "updategroup") {

    $updateGroup = easyUpdate(
        "user_group",
        array(
            "group_name"        => $_POST["groupName"],
            "group_permission"  => serialize($_POST['groupPermission'])
        ),
        array(
            "group_id" => $_POST["group_id"]
        )
    );

    if($updateGroup === true) {
        _s("User group successfully updated.");
    } else {
       _e($updateGroup);
    }

}


/************************** Edit Department **********************/
if(isset($_GET['page']) and $_GET['page'] == "newDepartment") {

    // Include the modal header
    modal_header("Edit Users Group", full_website_address() . "/xhr/?module=settings&page=addNewnewDepartment");
    
    ?>

        <div class="form-group">
            <label for="departmentName"><?= __("Department name:"); ?></label>
            <input type="text" name="departmentName" class="form-control" id="groupdepartmentNameName" placeholder="Enter department Name">
        </div>

    <?php

    // Include the modal footer
    modal_footer();

}

/********************* Add new Department *******************/
if(isset($_GET['page']) and $_GET['page'] == "addNewnewDepartment") {
    

    if(empty($_POST["departmentName"])) {
       return _e("Please Enter department Name");
    } 
    
    $returnMsg = easyInsert(
        "emp_department", // Table name
        array( // field and fields value
            "dep_name" => $_POST["departmentName"]
        ),
        array( // No duplicate allow.
            "dep_name" => $_POST["departmentName"]
        )
    );

    if($returnMsg === true) {
        _s("New department added successfully.");
    } else {
       _e($returnMsg);
    }

}


/*************************** Department List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "departmentList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "dep_id",
        "dep_name"
    );

    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
    "table" => "emp_department",
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
            "emp_department",
            "dep_id, dep_name",
            array(),
            array (
                "is_trash = 0 and dep_id" => $requestData['search']['value'],
                " OR dep_id LIKE" => $requestData['search']['value'] . "%",
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
            "emp_department",
            "dep_id, dep_name",
            array(),
            array("is_trash = 0"),
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
            $allNestedData[] = $value["dep_id"];
            $allNestedData[] = $value["dep_name"];
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=true&module=settings&page=editDepartment&id='. $value["dep_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=settings&page=deleteDepartment" data-to-be-deleted="'. $value["dep_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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


/***************** Delete Department ****************/
// Delete Group
if(isset($_GET['page']) and $_GET['page'] == "deleteDepartment") {

    $deleteData = easyDelete(
        "emp_department",
        array(
            "dep_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {
        echo 1;
    } 
}

/************************** Edit Department **********************/
if(isset($_GET['page']) and $_GET['page'] == "editDepartment") {

    $selectGroup = easySelect(
        "emp_department",
        "*",
        array(),
        array(
            "dep_id" => $_GET['id']
        )
    );

    $department_name = $selectGroup["data"][0]["dep_name"];

    // Include the modal header
    modal_header("Edit Users Group", full_website_address() . "/xhr/?module=settings&page=updateDepartment");
    
    ?>

            <div class="form-group">
                <label for="departmentName"><?= __("Department name:"); ?></label>
                <input type="text" value = "<?php echo $department_name; ?>" name="departmentName" class="form-control" id="groupdepartmentNameName" placeholder="Enter department Name">
            </div>

            <input type="hidden" name="department_id" value="<?php echo htmlentities($_GET['id']); ?>">

    <?php

    // Include the modal footer
    modal_footer();

}

//*********************************  Update Department ******************** */
if(isset($_GET['page']) and $_GET['page'] == "updateDepartment") {

    $updateDepartment = easyUpdate(
        "emp_department",
        array(
            "dep_name" => $_POST["departmentName"]
        ),
        array(
            "dep_id" => $_POST["department_id"]
        )
    );

    if($updateDepartment === true) {
        _s("User group successfully updated.");
    } else {
       _e($updateDepartment);
    }
    
}


/************************** New Shop **********************/
if(isset($_GET['page']) and $_GET['page'] == "newShop") {

    // Include the modal header
    modal_header("New Shop", full_website_address() . "/xhr/?module=settings&page=addNewShop");
    
    ?>

            <div class="form-group">
                <label for="shopName"><?= __("Shop Name:"); ?></label>
                <input type="text" name="shopName" id="shopName" value = "" class="form-control">
            </div>
            <div class="form-group">
                <label for="shopAddress"><?= __("Shop Address:"); ?></label>
                <textarea name="shopAddress" id="shopAddress" rows="3" class="form-control"> </textarea>
            </div>
            <div class="form-group">
                <label for="shopCity"><?= __("Shop City:"); ?></label>
                <input type="text" name="shopCity" id="shopCity" value = "" class="form-control">
            </div>
            <div class="form-group">
                <label for="shopState"><?= __("Shop State:"); ?></label>
                <input type="text" name="shopState" id="shopState"value = ""  class="form-control">
            </div>
            <div class="form-group">
                <label for="shopPostalCode"><?= __("Shop Posal Code:"); ?></label>
                <input type="text" name="shopPostalCode" id="shopPostalCode" value = "" class="form-control">
            </div>
            <div class="form-group">
                <label for="shopCountry"><?= __("Shop Country:"); ?></label>
                <input type="text" name="shopCountry" id="shopCountry" value = "" class="form-control">
            </div>
            <div class="form-group">
                <label for="shopPhone"><?= __("Shop Contact No:"); ?></label>
                <input type="text" name="shopPhone" id="shopPhone" value = "" class="form-control">
            </div>
            <div class="form-group">
                <label for="shopEmail"><?= __("Shop Email:"); ?></label>
                <input type="email" name="shopEmail" id="shopEmail" value = "" class="form-control">
            </div>
            <div class="form-group">
                <label for="shopInvoiceFooter"><?= __("Shop Invoice Footer:"); ?></label>
                <textarea name="shopInvoiceFooter" id="shopInvoiceFooter" rows="3" class="form-control"> </textarea>
            </div>
            <div class="imageContainer">
                <div class="form-group">
                    <label for=""><?= __("Shop Logo:"); ?> </label>
                    <div class="image_preview" style="width: 60%; margin: auto;">
                        <img class='previewing' height='80px' src='' />
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-btn">
                            <span class="btn btn-default btn-file">
                                <?= __("Select photo"); ?> <input type="file" name="shopLogo" class="imageToUpload">
                            </span>
                        </span>
                        <input type="text" class="form-control imageNameShow" readonly>
                    </div>

                    <div style="margin-top: 8px;" class="photoErrorMessage"></div>
            
                </div>

            </div>
        
    <?php

    // Include the modal footer
    modal_footer();

    return;

}

//*********************************  Add mew Shop ******************** */
if(isset($_GET['page']) and $_GET['page'] == "addNewShop") {

    // Validate the Form
    if(empty($_POST["shopName"])) {
       return _e("Please enter shop name.");
    } else if(empty($_POST["shopAddress"])) {
       return _e("Please enter shop address.");
    } else if(empty($_POST["shopPhone"])) {
       return _e("Please enter shop contact number.");
    }

    // Upload the shop logo
    $shopLogo = NULL;
    if($_FILES["shopLogo"]["size"] > 0) {

        $shopLogo = easyUpload($_FILES["shopLogo"], "logos/shop", "shop_logo_".round(microtime(true)*1000));

        if(!isset($shopLogo["success"])) {
            return _e($shopLogo);
        } else {
            $shopLogo = $shopLogo["fileName"];
        }
        
    }

    // Now insert shop into database
    $insertShop = easyInsert(
        "shops",
        array (
            "shop_name"             => $_POST["shopName"],
            "shop_address"          => $_POST["shopAddress"],
            "shop_city"             => $_POST["shopCity"],
            "shop_state"            => $_POST["shopState"],
            "shop_postal_code"      => $_POST["shopPostalCode"],
            "shop_country"          => $_POST["shopCountry"],
            "shop_phone"            => $_POST["shopPhone"],
            "shop_email"            => $_POST["shopEmail"],
            "shop_invoice_footer"   => $_POST["shopInvoiceFooter"],
            "shop_logo"             => $shopLogo
        )
    );

    if($insertShop === true) {
        _s("New shop added successfully.");
    } else {
       _e($insertShop);
    }

}

/*************************** Department List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "shopList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "shop_name",
        "shop_address",
        "shop_city",
        "shop_state",
        "shop_postal_code",
        "shop_country",
        "shop_phone",
        "shop_email",
        "shop_invoice_footer"
    );

    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
    "table" => "shops",
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
            "shops",
            "*",
            array(),
            array (
                "is_trash=0 and shop_name LIKE" => $requestData['search']['value'] . "%",
                " or shop_address LIKE" => $requestData['search']['value'] . "%",
                " or shop_city LIKE" => $requestData['search']['value'] . "%",
                " or shop_state LIKE" => $requestData['search']['value'] . "%",
                " or shop_postal_code LIKE" => $requestData['search']['value'] . "%",
                " or shop_country LIKE" => $requestData['search']['value'] . "%",
                " or shop_phone LIKE" => $requestData['search']['value'] . "%",
                " or shop_email LIKE" => $requestData['search']['value'] . "%",
                " or shop_invoice_footer LIKE" => $requestData['search']['value'] . "%",
                
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
            "shops",
            "*",
            array(),
            array("is_trash=0"),
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
            $allNestedData[] = $value["shop_name"];
            $allNestedData[] = $value["shop_address"];
            $allNestedData[] = $value["shop_city"];
            $allNestedData[] = $value["shop_state"];
            $allNestedData[] = $value["shop_postal_code"];
            $allNestedData[] = $value["shop_country"];
            $allNestedData[] = $value["shop_phone"];
            $allNestedData[] = $value["shop_email"];
            $allNestedData[] = $value["shop_invoice_footer"];

            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=true&module=settings&page=editShop&id='. $value["shop_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=settings&page=deleteShop" data-to-be-deleted="'. $value["shop_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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

/***************** Delete Shop ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteShop") {

    $deleteData = easyDelete(
        "shops",
        array(
            "shop_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {
        echo 1;
    } else {
        echo $deleteData;
    }

}

/************************** Edit Department **********************/
if(isset($_GET['page']) and $_GET['page'] == "editShop") {

    $selectShop = easySelect(
        "shops",
        "*",
        array(),
        array(
            "shop_id" => $_GET['id']
        )
    );

    $shopData = $selectShop["data"][0];

    // Include the modal header
    modal_header("Edit Shop", full_website_address() . "/xhr/?module=settings&page=updateShop");
    
    ?>

            <div class="form-group">
                <label for="shopName"><?= __("Shop Name:"); ?></label>
                <input type="text" name="shopName" id="shopName" value = "<?php echo $shopData["shop_name"]; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="shopAddress"><?= __("Shop Address:"); ?></label>
                <textarea name="shopAddress" id="shopAddress" rows="3" class="form-control"> <?php echo $shopData["shop_address"]; ?> </textarea>
            </div>
            <div class="form-group">
                <label for="shopCity"><?= __("Shop City:"); ?></label>
                <input type="text" name="shopCity" id="shopCity" value = "<?php echo $shopData["shop_city"]; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="shopState"><?= __("Shop State:"); ?></label>
                <input type="text" name="shopState" id="shopState"value = "<?php echo $shopData["shop_state"]; ?>"  class="form-control">
            </div>
            <div class="form-group">
                <label for="shopPostalCode"><?= __("Shop Posal Code:"); ?></label>
                <input type="text" name="shopPostalCode" id="shopPostalCode" value = "<?php echo $shopData["shop_postal_code"]; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="shopCountry"><?= __("Shop Country:"); ?></label>
                <input type="text" name="shopCountry" id="shopCountry" value = "<?php echo $shopData["shop_country"]; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="shopPhone"><?= __("Shop Contact No:"); ?></label>
                <input type="text" name="shopPhone" id="shopPhone" value = "<?php echo $shopData["shop_phone"]; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="shopEmail"><?= __("Shop Email:"); ?></label>
                <input type="email" name="shopEmail" id="shopEmail" value = "<?php echo $shopData["shop_email"]; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="shopInvoiceFooter"><?= __("Shop Invoice Footer:"); ?></label>
                <textarea name="shopInvoiceFooter" id="shopInvoiceFooter" rows="3" class="form-control"><?php echo $shopData["shop_invoice_footer"]; ?> </textarea>
            </div>
            <div class="form-group">
                <label for=""><?= __("Shop Logo:"); ?> </label>
                <div id="image_preview" style="width: 60%; margin: auto;">
                    <?php echo empty($shopData['shop_logo']) ? "" : "<img id='previewing' height='80px' src='". full_website_address() . "/images/?for=shopLogo&id=". $_GET['id'] ."'>"; ?>
                </div>
                <br/>
                <div class="input-group">
                    <span class="input-group-btn">
                        <span class="btn btn-default btn-file">
                            <?= __("Select Logo"); ?> <input type="file" name = "shopLogo" id="imgInp">
                        </span>
                    </span>
                    <input type="text" class="form-control" id="imageNameShow" readonly>
                </div>
                <div id="message"></div>
            </div>

            <input type="hidden" name="shop_id" value="<?php echo htmlentities($_GET['id']); ?>">
        
        <script>

            $(document).ready(function (e) {
            /* Function to preview image */
            $(function() {
                $("#imgInp").change(function() {

                /* Show the filename in choose option */
                var imageNameShow = $("#imgInp").val().replace(/\\/g, '/').replace(/.*\//, '');
                $("#imageNameShow").val(imageNameShow);

                $("#message").empty();         /* To remove the previous error message */
                var file = this.files[0];
                var imagefile = file.type;
                var imagesize = file.size;
                var match= ["image/jpeg","image/png","image/jpg"];	
                if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])))
                {
                    $("#message").html("<br><div class='alert alert-danger'><p id='error'>Please Select A valid Image File</p>"+"<strong>Note: </strong>"+"<span id='error_message'>Only jpeg, jpg and png Images type allowed</span></div>");
                    return false;
                } else if (imagesize > 500000) {
                    $("#message").html("<br><div class='alert alert-danger'>Max image size 500 kb </div>");
                    return false;
                } else {
                    var reader = new FileReader();	
                    reader.onload = imageIsLoaded;
                    reader.readAsDataURL(this.files[0]);
                }		
                });
            });
            function imageIsLoaded(e) { 
                $("#imgInp").css("color","green");
                $('#image_preview').css("display", "block");
                $('#previewing').attr('src', e.target.result);
                $('#previewing').attr('width', '100%');
                $('#previewing').attr('height', 'auto');
            };
            });
        </script>

    <?php

    // Include the modal footer
    modal_footer();

}

//*********************************  Update Department ******************** */
if(isset($_GET['page']) and $_GET['page'] == "updateShop") {

    // Validate the Form
    if(empty($_POST["shopName"])) {
       return _e("Please enter shop name.");
    } else if(empty($_POST["shopAddress"])) {
       return _e("Please enter shop address.");
    } else if(empty($_POST["shopPhone"])) {
       return _e("Please enter shop contact number.");
    }

    // Upload the shop logo
    $shopLogo = NULL;
    if($_FILES["shopLogo"]["size"] > 0) {

        $shopLogo = easyUpload($_FILES["shopLogo"], "logos/shop", "shop_logo_".round(microtime(true)*1000));

        if(!isset($shopLogo["success"])) {
            return _e($shopLogo);
        } else {

            // Update shop logo
            easyUpdate(
                "shops",
                array(
                    "shop_logo" => $shopLogo["fileName"]
                ),
                array(
                    "shop_id" => $_POST["shop_id"]
                )
            );

        }
        
    }

    // Update shop without logo
    $updateShop = easyUpdate(
        "shops",
        array(
            "shop_name"             => $_POST["shopName"],
            "shop_address"          => $_POST["shopAddress"],
            "shop_city"             => $_POST["shopCity"],
            "shop_state"            => $_POST["shopState"],
            "shop_postal_code"      => $_POST["shopPostalCode"],
            "shop_country"          => $_POST["shopCountry"],
            "shop_phone"            => $_POST["shopPhone"],
            "shop_email"            => $_POST["shopEmail"],
            "shop_invoice_footer"   => $_POST["shopInvoiceFooter"]
        ),
        array(
            "shop_id" => $_POST["shop_id"]
        )
    );


    if($updateShop === true) {
        _s("Shop successfully updated.");
    } else {
       _e($updateShop);
    }

}

/************************** Add new Tariff and Charges **********************/
if(isset($_GET['page']) and $_GET['page'] == "newTariffCharges") {

    // Include the modal header
    modal_header("New  Tariff & Charges", full_website_address() . "/xhr/?module=settings&page=addNewTariffCharges");
    
    ?>
      <div class="box-body">
        
        <div class="form-group">
          <label for="tcName"><?= __("Tariff/Charges Name:"); ?></label>
          <input type="text" name="tcName" id="tcName" class="form-control" placeholder="Eg: VAT, TAX or SC" required>
        </div>
        <div class="form-group">
          <label for="tcValue"><?= __("Value:"); ?></label>
          <input type="text" name="tcValue" id="tcValue" class="form-control" placeholder="<?= __("Eg: 15% or 100 (Percentage or Fixed Amount)"); ?>" required>
        </div>
        <div class="form-group">
          <label for="tcDescription"><?= __("Brand Description"); ?></label>
          <textarea name="tcDescription" id="tcDescription" rows="3" class="form-control"></textarea>
        </div>
              
      </div>
      <!-- /Box body-->
  
    <?php
  
    // Include the modal footer
    modal_footer();
  
}

// Add new Tariff
if(isset($_GET['page']) and $_GET['page'] == "addNewTariffCharges") {
  
    if(empty($_POST["tcName"])) {
        return _e("Please enter Tariff/Charges name.");
    } else if(empty($_POST["tcValue"])) {
        return _e("Please enter Tariff/Charges value.");
    } else if(!preg_match("/(\d|\d%])/", $_POST["tcValue"])) {
        return _e("Only numbers and % are allowed in value field.");
    }
      
    $addTariff = easyInsert(
        "tariff_and_charges",
        array(
            "tc_name"         => $_POST["tcName"],
            "tc_value"        => preg_replace('/\s+/', '', $_POST["tcValue"]),
            "tc_description"  => $_POST["tcDescription"]
        ),
        array( // No duplicate allow.
            "tc_name"   => $_POST["tcName"]
        )
    );
  
    if($addTariff === true) {
        _s("New Tariff and Charges added successfully.");
    } else {
       _e($addTariff);
    }
  
}


/*************************** Tariff List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "tariffChargesList") {
      
    $requestData = $_REQUEST;
    $getData = [];
  
    // List of all columns name
    $columns = array(
        "tc_name",
        "tc_value",
        "tc_description"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
    "table" => "tariff_and_charges",
    "fields" => "count(*) as totalRow",
    "where" => array(
      "is_trash = 0"
    )
  ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
      $requestData['length'] = $totalRecords;
    }

    $getData = easySelect(
        "tariff_and_charges",
        "*",
        array(),
        array (
            "is_trash=0 and tc_name LIKE" => $requestData['search']['value'] . "%",
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
    if(isset($getData['count']) and $getData['count'] > 0) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = $value["tc_name"];
            $allNestedData[] = $value["tc_value"];
            $allNestedData[] = $value["tc_description"];
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=settings&page=editTariffCharges&id='. $value["tc_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=settings&page=deleteTariffCharges" data-to-be-deleted="'. $value["tc_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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
    
    // Encode in Json Format
    echo json_encode($jsonData); 
  
}


/***************** Delete Tariff ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteTariffCharges") {

    $deleteData = easyDelete(
        "tariff_and_charges",
        array(
            "tc_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {
        echo 1;
    } else {
        echo $deleteData;
    }

}


/************************** Edit Tariff and Charges **********************/
if(isset($_GET['page']) and $_GET['page'] == "editTariffCharges") {

    $tc = easySelect(
        "tariff_and_charges",
        "*",
        array(),
        array(
            "tc_id" => $_GET['id']
        )
    )["data"][0];

    // Include the modal header
    modal_header("Edit Tariff & Charges", full_website_address() . "/xhr/?module=settings&page=updateTariffCharges");
    
    ?>
      <div class="box-body">
        
        <div class="form-group">
          <label for="tcName"><?= __("Tariff/Charges Name:"); ?></label>
          <input type="text" name="tcName" id="tcName" class="form-control" value="<?php echo $tc["tc_name"]; ?>" placeholder="Eg: VAT, TAX or SC" required>
        </div>
        <div class="form-group">
          <label for="tcValue"><?= __("Value:"); ?></label>
          <input type="text" name="tcValue" id="tcValue" class="form-control" value="<?php echo $tc["tc_value"]; ?>" placeholder="Eg: 15% or 100 (Percentage or Fixed Amount)" required>
        </div>
        <div class="form-group">
          <label for="tcDescription"><?= __("Brand Description:"); ?></label>
          <textarea name="tcDescription" id="tcDescription" rows="3" class="form-control"><?php echo $tc["tc_description"]; ?></textarea>
        </div>
              
      </div>
      <!-- /Box body-->
  
    <?php
  
    // Include the modal footer
    modal_footer();
  
}

// Add new Tariff
if(isset($_GET['page']) and $_GET['page'] == "updateTariffCharges") {
  
    if(empty($_POST["tcName"])) {
        return _e("Please enter Tariff/Charges name.");
    } else if(empty($_POST["tcValue"])) {
        return _e("Please enter Tariff/Charges value.");
      }
    
    $addTariff = easyUpdate(
        "tariff_and_charges",
        array(
            "tc_name"         => $_POST["tcName"],
            "tc_value"        => preg_replace('/\s+/', '', $_POST["tcValue"]),
            "tc_description"  => $_POST["tcDescription"]
        ),
        array( // No duplicate allow.
            "tc_name"   => $_POST["tcName"]
        )
    );
  
    if($addTariff === true) {
        _s("Successfully updated");
    } else {
       _e($addTariff);
    }
  
}



// System Settings
if(isset($_GET['page']) and $_GET['page'] == "saveSystemSettings") {

    foreach($_POST as $option_name => $option_value) {
        
        // If curent option is not same with saved option value and isset the option, then update it
        if( $option_value !== get_options($option_name)) {

            easyUpdate(
                "options",
                array(
                    "option_value"  => strlen($option_value) === 0 ? "" : $option_value
                ),
                array(
                    "option_name"   => $option_name
                )
            );

        }

    }

    if( isset($_POST["timeZone"]) ) {

        // Update mysql timezone
        $timeZone = safe_input($_POST["timeZone"]);

        // Get the GMT value from time zone name
        $getGmtFormat = new DateTime('now', new DateTimeZone($timeZone));
        $getGmtFormat = $getGmtFormat->format("P");
        runQuery("SET GLOBAL time_zone = '{$getGmtFormat}'");
        
    }
    

    _s("Options have been successfully updated.");
  
}


//************************ Create new group permission ************************* */
if(isset($_GET['page']) and $_GET['page'] == "newFirewallRole") {


    // Include the modal header
    modal_header("New Firewall Role", full_website_address() . "/xhr/?module=settings&page=addNewFirewallRole");
    
    ?>
        <div class="box-body">

            <div class="form-group">
                <label for="firewallStatus"><?= __("Status:"); ?></label>
                <select name="firewallStatus" id="firewallStatus" class="form-control">
                    <option value="Active">Active</option>
                    <option value="Deactive">Deactive</option>
                </select>
            </div>
            <div class="form-group">
                <label for="firewallIpAddress"><?= __("IP Address:"); ?></label>
                <input type="text" name="firewallIpAddress" id="firewallIpAddress" class="form-control">
            </div>
            <div class="form-group">
                <label for="firewallAction"><?= __("Action:"); ?></label>
                <select name="firewallAction" id="firewallAction" class="form-control">
                    <option value="Blocked">Blocked</option>
                    <option value="Permitted">Permitted</option>
                </select>
            </div>
            <div class="form-group">
                <label for="firewallComments"><?= __("Comments:"); ?></label>
                <textarea name="firewallComments" id="firewallComments" cols="30" rows="3" class="form-control"></textarea>
            </div>
            
        </div>
            
    <?php

    // Include the modal footer
    modal_footer("Add Role");

    return;

}



// Add new user group page
if(isset($_GET['page']) and $_GET['page'] == "addNewFirewallRole") {


    if(empty($_POST["firewallStatus"])) {
       return _e("Please select status");
    } else if (empty($_POST["firewallIpAddress"])) {
       return _e("Please enter IP Address.");
    } else if (empty($_POST["firewallAction"])) {
        return _e("Please select action.");
    }
    
    $returnMsg = easyInsert(
        "firewall", // Table name
        array( // Fileds Name and its value
            "fw_status"     => $_POST["firewallStatus"],
            "fw_ip_address" => $_POST["firewallIpAddress"],
            "fw_action"     => $_POST["firewallAction"],
            "fw_comment"    => $_POST["firewallComments"],
            "fw_added_by"   => $_SESSION["uid"]
        ),
        array( // No duplicate allow.
            "fw_ip_address" => $_POST["firewallIpAddress"]
        )
    );

    if($returnMsg === true) {
        _s("Firewall role has been added successfully.");
    } else {
       _e($returnMsg);
    }

}


/*************************** Tariff List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "firewallList") {
      
    $requestData = $_REQUEST;
    $getData = [];
  
    // List of all columns name
    $columns = array(
        "fw_status",
        "fw_ip_address",
        "fw_action",
        "fw_comment",
        "fw_added_on",
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "firewall",
        "fields" => "count(*) as totalRow"
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
      $requestData['length'] = $totalRecords;
    }


    $getData = easySelectA(array(
        "table"     => "firewall",
        "where"     => array(
            "is_trash = 0 and fw_comment LIKE" => $requestData['search']['value'] . "%",
        )
    ));



    $totalFilteredRecords = $getData ? $getData["count"] : 0;


    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['count']) and $getData['count'] > 0) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["fw_added_on"];
            $allNestedData[] = $value["fw_status"];
            $allNestedData[] = $value["fw_ip_address"];
            $allNestedData[] = $value["fw_action"];
            $allNestedData[] = $value["fw_comment"];
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=settings&page=editFirwallRole&id='. $value["fw_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=settings&page=deleteFirewallRole" data-to-be-deleted="'. $value["fw_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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
    
    // Encode in Json Format
    echo json_encode($jsonData); 
  
}


// Delete FireWall
if(isset($_GET['page']) and $_GET['page'] == "deleteFirewallRole") {

    $deleteFirewall = easyPermDelete (
        "firewall",
        array(
            "fw_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteFirewall === true) {
        echo 1;
    } 

}



//************************ Create new group permission ************************* */
if(isset($_GET['page']) and $_GET['page'] == "editFirwallRole") {


    $fw = easySelectA(array(
        "table"     => "firewall",
        "where"     => array(
            "fw_id" => $_GET["id"]
        )
    ))["data"][0];

    // Include the modal header
    modal_header("Edit Firewall Role", full_website_address() . "/xhr/?module=settings&page=updateFirewallRole");
    
    ?>
        <div class="box-body">

            <div class="form-group">
                <label for="firewallStatus"><?= __("Status:"); ?></label>
                <select name="firewallStatus" id="firewallStatus" class="form-control">
                    <option <?php echo $fw["fw_status"] === "Active" ? "selected" : ""; ?> value="Active">Active</option>
                    <option <?php echo $fw["fw_status"] === "Deactive" ? "selected" : ""; ?> value="Deactive">Deactive</option>
                </select>
            </div>
            <div class="form-group">
                <label for="firewallIpAddress"><?= __("IP Address:"); ?></label>
                <input type="text" name="firewallIpAddress" id="firewallIpAddress" value="<?php echo $fw["fw_ip_address"]; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="firewallAction"><?= __("Action:"); ?></label>
                <select name="firewallAction" id="firewallAction" class="form-control">
                    <option <?php echo $fw["fw_action"] === "Blocked" ? "selected" : ""; ?> value="Blocked">Blocked</option>
                    <option <?php echo $fw["fw_action"] === "Permitted" ? "selected" : ""; ?> value="Permitted">Permitted</option>
                </select>
            </div>
            <div class="form-group">
                <label for="firewallComments"><?= __("Comments:"); ?></label>
                <textarea name="firewallComments" id="firewallComments" cols="30" rows="3" class="form-control"><?php echo $fw["fw_comment"]; ?></textarea>
            </div>
            <input type="hidden" name="fw_id" value="<?php echo htmlentities($_GET["id"]); ?>">
            
        </div>
            
    <?php

    // Include the modal footer
    modal_footer("Update Role");

}



// Update firewall role
if(isset($_GET['page']) and $_GET['page'] == "updateFirewallRole") {


    if(empty($_POST["firewallStatus"])) {
       return _e("Please select status");
    } else if (empty($_POST["firewallIpAddress"])) {
       return _e("Please enter IP Address.");
    } else if (empty($_POST["firewallAction"])) {
        return _e("Please select action.");
    }
    
    $returnMsg = easyUpdate(
        "firewall",
        array(
            "fw_status"     => $_POST["firewallStatus"],
            "fw_ip_address" => $_POST["firewallIpAddress"],
            "fw_action"     => $_POST["firewallAction"],
            "fw_comment"    => $_POST["firewallComments"]
        ),
        array( 
            "fw_id " => $_POST["fw_id"]
        )
    );

    if($returnMsg === true) {

        _s("Firewall role has been updated successfully.");

    } else {

       _e($returnMsg);
       
    }

}



// Update firewall role
if(isset($_GET['page']) and $_GET['page'] == "generateDatabaseBackup") {

    if(empty($_POST["selectedTable"])) {
        return;
    }

    $filename = "database-backup_".date("Y-m-d_H:i");
    $format = "sql";

    if( !empty($_POST["backupName"]) ) {
        $filename = $_POST["backupName"];
    }

    if( !empty($_POST["backupFormat"]) ) {
        $format = $_POST["backupFormat"];
    }


    $backupData = "";
    $backupData .= "-- \n";
    $backupData .= "-- Database Backup";
    $backupData .= "\n";
    $backupData .= "-- Export Created on ". date("Y-m-d H:i")."\n\n";
    $backupData .= "\n";
    $backupData .= "-- ************************************************\n";
    $backupData .= "-- Start exporting Data\n";
    $backupData .= "-- ************************************************\n";

    $backupData .= "\n";
    $backupData .= "\n";
    $backupData .= 'SET FOREIGN_KEY_CHECKS=0;' . "\n";
    $backupData .= "\n";
    $backupData .= "\n";
    $backupData .= "-- \n";


    // Generate the database/ tables backup
    foreach($_POST["selectedTable"] as $table) {

        $backupData .= "-- Data for {$table} table \n";
        $backupData .= "-- \n";

        $tableData = $conn->query("SELECT * FROM {$table}");
        $tableData = $tableData->fetch_all();

        // Create table chunk for more with 1000
        $tableDataChunk = array_chunk($tableData, 1000);

        foreach($tableDataChunk as $chunk) {

            // Insert into command
            $backupData .= "INSERT INTO {$table} VALUES \n";

            $rowData = "";
            foreach($chunk as $row) {

                $fieldData = "";
                foreach($row as $field) {

                    if(is_null($field)) {
                        
                        $fieldData .= " NULL,";

                    } elseif( is_numeric($field) ) {

                        $fieldData .= " ".$field.",";

                    } else {
                        
                        $fieldData .= " '". $conn->real_escape_string($field) ."',";

                    }

                }

                $rowData .= "(";
                $rowData .= substr(trim($fieldData), 0, -1);
                $rowData .= "),\n";

            }

            $backupData .= substr($rowData, 0, -2). ";";
            $backupData .= "\n";

            
        }


        $backupData .= "\n";
        $backupData .= "-- \n";

    }


    
    $backupDir = DIR_ASSETS . "/backup/";
    if(!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }

    // Create the sql file
    $filename = preg_replace("/[^a-z0-9\_\-\.]/i", '', $filename) . '.' . $format;
    $sqlFile = $backupDir . $filename;
    $createSQLFile = fopen($sqlFile, "a");
    
    // Write data into file
    fwrite($createSQLFile, $backupData);
    fclose($createSQLFile);


    echo $filename;


}



?>