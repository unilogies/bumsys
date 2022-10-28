<?php

// Select the Accounts
$selectAccounts = easySelect("accounts", "accounts_id, accounts_name", array(), array("is_trash" => 0));

/************************** Add New Accounts **********************/
if(isset($_GET['page']) and $_GET['page'] == "newAccount") {

    // Include the modal header
    modal_header("Create New Accounts", full_website_address() . "/xhr/?module=accounts&page=addNewAccount");

    if(current_user_can("accounts.Add") !== true) {
        return _e("Sorry! you do not have permission to add new Account.");
    }
    
    ?>
      <div class="box-body">
        
        <div class="form-group">
            <label for="accountName"><?= __("Account Name:"); ?></label>
            <input type="text" name="accountName" id="accountName" value="" class="form-control">
        </div>
        <div class="form-group">
            <label for="accountType"><?= __("Account Type:"); ?></label>
            <select name="accountType" id="accountType" class="form-control">
            <?php
                $accountType = array("Local (Cash)", "Bank (Savings)", "Bank (Current)", "Card (Credit)", "Card (Debit)",);
                foreach($accountType as $accType) {
                    echo "<option value='{$accType}'>". __($accType) ."</option>";
                }
            ?>
            </select>
        </div>
        <div class="form-group">
            <label for="accountCurrency"><?= __("Currency:"); ?></label>
            <select name="accountCurrency" id="accountCurrency" class="form-control">
                <option value="BDT"><?= __("BDT"); ?></option>
            </select>
        </div>
        <div class="form-group">
            <label for="openingBalance"><?= __("Opening Balance:"); ?></label>
            <input type="number" name="openingBalance" id="openingBalance" value="" class="form-control">
        </div>
        <div class="form-group">
            <label for="bankName"><?= __("Bank Name:"); ?></label>
            <input type="text" name="bankName" id="bankName" value="" class="form-control">
        </div>
        <div class="form-group">
            <label for="bankAccNumber"><?= __("Bank Account Number:"); ?></label>
            <input type="number" name="bankAccNumber" id="bankAccNumber" value="" class="form-control">
        </div>
        <div class="form-group">
            <label for="bankAccDetails"><?= __("Bank Account Details:"); ?></label>
            <textarea name="bankAccDetails" id="bankAccDetails" rows="3" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <input value="1" type="checkbox" name="negativeValueIsAllowed" id="negativeValueIsAllowed">
            <label for="negativeValueIsAllowed"><?= __("Negative value is allowed"); ?></label>
        </div>
              
      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer();
  
  }
  

// Add new account
if(isset($_GET['page']) and $_GET['page'] == "addNewAccount") {
    
    if(current_user_can("accounts.Add") !== true) {
        return _e("Sorry! you do not have permission to add new Account.");
    }

    if(empty($_POST["accountName"])) {
       return _e("Please enter account name");
    } else if(strlen($_POST["openingBalance"]) < 1) {
        return _e("Please enter account opening balance");
    }
    
    $returnMsg = easyInsert(
        "accounts", // Table name
        array( // Fileds Name and value
            "accounts_name"             => $_POST["accountName"],
            "accounts_type"             => $_POST["accountType"],
            "accounts_currency"         => $_POST["accountCurrency"],
            "accounts_opening_balance"  => $_POST["openingBalance"],
            "accounts_balance"          => $_POST["openingBalance"],
            "accounts_bank_name"        => $_POST["bankName"],
            "accounts_bank_acc_number"  => empty($_POST["bankAccNumber"]) ? NULL : $_POST["bankAccNumber"],
            "accounts_bank_acc_details" => $_POST["bankAccDetails"],
            "accounts_status"           => "Active",
            "accounts_add_by"           => $_SESSION["uid"],
            "negative_value_is_allow"   => isset($_POST["negativeValueIsAllowed"]) ? $_POST["negativeValueIsAllowed"] : 0
        ),
        array( // No duplicate allow.
            "accounts_name"                  => $_POST["accountName"],
            " AND accounts_bank_acc_number"  => $_POST["bankAccNumber"],
            " AND accounts_type"             => $_POST["accountType"]
        )
    );

    if($returnMsg === true) {
        _s("New accounts added successfully.");
    } else {
        _e($returnMsg);
    }

}


/*************************** Accounts List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "accountList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "accounts_name",
        "accounts_type",
        "accounts_balance",
        "accounts_bank_name",
        "accounts_bank_acc_number",
        "accounts_bank_acc_details"
    );
    
    // Count Total recrods
    // $selectAccounts declared on the top of the page
    $totalFilteredRecords = $totalRecords = $selectAccounts ? $selectAccounts["count"] : 0;

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if(!empty($requestData["search"]["value"])) {  // get data with search
        
        $getData = easySelect(
            "accounts",
            "accounts_id, accounts_name, accounts_type, accounts_balance, accounts_bank_name, accounts_bank_acc_number, accounts_bank_acc_details",
            array(),
            array (
                "is_trash"  => 0,
                " and accounts_name LIKE" => $requestData['search']['value'] . "%",
                " OR accounts_type LIKE" => $requestData['search']['value'] . "%",
                " OR accounts_bank_name LIKE" => $requestData['search']['value'] . "%",
                " OR accounts_bank_acc_number LIKE" => $requestData['search']['value'] . "%"
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
            "accounts",
            "accounts_id, accounts_name, accounts_type, accounts_balance, accounts_bank_name, accounts_bank_acc_number, accounts_bank_acc_details",
            array(),
            array(
                "is_trash"  => 0
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
    if($getData) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["accounts_name"];
            $allNestedData[] = $value["accounts_type"];
            $allNestedData[] = $value["accounts_balance"];
            $allNestedData[] = $value["accounts_bank_name"];
            $allNestedData[] = $value["accounts_bank_acc_number"];
            $allNestedData[] = $value["accounts_bank_acc_details"];
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a class="'. ( current_user_can("accounts.Edit") ? "" : "restricted " ) .'" data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=accounts&page=editAccount&id='. $value["accounts_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                    <li><a class="updateEntry" href="'. full_website_address() . '/xhr/?module=accounts&page=updateAccountBalance" data-to-be-updated="'. $value["accounts_id"] .'"><i class="fa fa-refresh"></i> Update Balance</a></li>
                                    <li><a class="'. ( current_user_can("accounts.Delete") ? "" : "restricted " ) .'deleteEntry" href="'. full_website_address() . '/xhr/?module=accounts&page=deleteAccount" data-to-be-deleted="'. $value["accounts_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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
if(isset($_GET['page']) and $_GET['page'] == "deleteAccount") {

    if(current_user_can("accounts.Delete") !== true) {
        echo '{
            "title": "Sorry!",
            "text": "'. __("you do not have permission to delete Account.") .'",
            "showConfirmButton": true,
            "showCloseButton": true,
            "toast": false,
            "icon": "error"
        }';
        return;
    }

    $deleteData = easyDelete(
        "accounts",
        array(
            "accounts_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {
        echo '{
            "title": "The account has been deleted successfully."
        }';
    } 
}



/***************** Update Accounts Balance ****************/
if(isset($_GET['page']) and $_GET['page'] == "updateAccountBalance") {

    updateAccountBalance($_POST["datatoUpdate"]);

    echo '{
        "title": "The account balance has been updated successfully."
    }';
}



/************************** Edit Accounts **********************/
if(isset($_GET['page']) and $_GET['page'] == "editAccount") {

    $selectAccount = easySelect(
        "accounts",
        "*",
        array(),
        array(
            "accounts_id" => $_GET['id']
        )
    );
  
    $accounts = $selectAccount["data"][0];
    // Include the modal header
    modal_header("Edit Accounts", full_website_address() . "/xhr/?module=accounts&page=updateAccount");

    if(current_user_can("accounts.Edit") !== true) {
        return _e("Sorry! you do not have permission to edit Account.");
    }
    
    ?>
      <div class="box-body">
        
        <div class="form-group">
            <label for="accountName"><?= __("Account Name:"); ?></label>
            <input type="text" name="accountName" id="accountName" value="<?php echo $accounts["accounts_name"] ?>" class="form-control">
        </div>
        <div class="form-group">
            <label for="accountType"><?= __("Account Type:"); ?></label>
            <select name="accountType" id="accountType" class="form-control">
            <?php
                $accountType = array("Local (Cash)", "Bank (Savings)", "Bank (Current)", "Card (Credit)", "Card (Debit)",);
                foreach($accountType as $accType) {
                    $selected = ($accounts["accounts_type"] == $accType) ? "selected" : "";
                    echo "<option {$selected} value='{$accType}'>{$accType}</option>";
                }
            ?>
            </select>
        </div>
        <div class="form-group">
            <label for="accountCurrency"><?= __("Currency:"); ?></label>
            <select name="accountCurrency" id="accountCurrency" class="form-control">
                <option value="BDT"><?= __("BDT"); ?></option>
            </select>
        </div>
        <div class="form-group">
            <label for="openingBalance"><?= __("Opening Balance:"); ?></label>
            <input type="number" name="openingBalance" onclick="this.select();" id="openingBalance" value="<?php echo number_format($accounts["accounts_opening_balance"], 0, "", ""); ?>" class="form-control">
        </div>
        <div class="form-group">
            <label for="bankName"><?= __("Bank Name:"); ?></label>
            <input type="text" name="bankName" id="bankName" value="<?php echo $accounts["accounts_bank_name"] ?>" class="form-control">
        </div>
        <div class="form-group">
            <label for="bankAccNumber"><?= __("Bank Account Number:"); ?></label>
            <input type="number" name="bankAccNumber" id="bankAccNumber" value="<?php echo $accounts["accounts_bank_acc_number"] ?>" class="form-control">
        </div>
        <div class="form-group">
            <label for="bankAccDetails"><?= __("Bank Account Details:"); ?></label>
            <textarea name="bankAccDetails" id="bankAccDetails" rows="3" class="form-control"> <?php echo $accounts["accounts_bank_acc_details"] ?> </textarea>
        </div>
        <div class="form-group">
            <input <?php echo ($accounts["negative_value_is_allow"] == 1) ? "checked" : ""; ?> value="1" type="checkbox" name="negativeValueIsAllowed" id="negativeValueIsAllowed">
            <label for="negativeValueIsAllowed"><?= __("Negative value is allowed"); ?></label>
        </div>

        <input type="hidden" name="accounts_id" value="<?php echo $_GET['id']; ?>">
              
      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer();
  
  }
  
  
  //*******************************  Update User ******************** */
  if(isset($_GET['page']) and $_GET['page'] == "updateAccount") {

    if(current_user_can("accounts.Edit") !== true) {
        return _e("Sorry! you do not have permission to add new Account.");
    }
  
    if(empty($_POST["accountName"])) {
        return _e("Please enter account name");
    }
    
    $updateAccounts = easyUpdate(
        "accounts", 
        array( 
            "accounts_name"             => $_POST["accountName"],
            "accounts_type"             => $_POST["accountType"],
            "accounts_currency"         => $_POST["accountCurrency"],
            "accounts_bank_name"        => $_POST["bankName"],
            "accounts_bank_acc_number"  => $_POST["bankAccNumber"] ? $_POST["bankAccNumber"] : NULL,
            "accounts_opening_balance"  => $_POST["openingBalance"],
            "accounts_bank_acc_details" => $_POST["bankAccDetails"],
            "accounts_status"           => "Active",
            "accounts_update_by"        => $_SESSION["uid"],
            "negative_value_is_allow"   => isset($_POST["negativeValueIsAllowed"]) ? $_POST["negativeValueIsAllowed"] : 0
        ),
        array( 
            "accounts_id"  => $_POST["accounts_id"]
        )
    );
  
        
    if($updateAccounts === true) {
        // Update Accounts Balance
        updateAccountBalance($_POST["accounts_id"]);

        _s("Accounts successfully updated.");

    } else {
        _e($updateAccounts);
    }
  
  }


/************************** Transfer Money **********************/
if(isset($_GET['page']) and $_GET['page'] == "newTransfer") {

    // Include the modal header
    modal_header("Transfer Money", full_website_address() . "/xhr/?module=accounts&page=addNewTransfer");
    
    ?>

      <div class="box-body">
        
        <div class="form-group required">
            <label for="transferDate"><?= __("Date:"); ?></label>
            <input type="text" name="transferDate" id="transferDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker" required>
        </div>

        <div class="form-group required">
            <label for="transferAcountsFrom"><?= __("From Accounts:"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="Which account the money is transfer from" class="fa fa-question-circle"></i>
            <select name="transferAcountsFrom" id="transferAcountsFrom" class="form-control select2" style="width: 100%;" required>
                <option value=""><?= __("Select Accounts..."); ?></option>
                <?php
                    
                    foreach($selectAccounts["data"] as $accounts) {
                        $selected = (isset($_SESSION['aid']) and $_SESSION['aid'] === $accounts['accounts_id']) ? "selected" : "";
                        echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>

        <div class="form-group required">
            <label for="transferAcountsTO"><?= __("To Accounts:"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="In which account the money is transfer to" class="fa fa-question-circle"></i>
            <select name="transferAcountsTO" id="transferAcountsTO" class="form-control select2" style="width: 100%;" required>
                <option value=""><?= __("Select Accounts..."); ?></option>
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
if(isset($_GET['page']) and $_GET['page'] == "addNewTransfer") {


    if(current_user_can("transfer_money.Add") !== true) {
        echo '{
            "title": "Sorry!",
            "text": "' . __("You do not have permission to transfer money.") . '",
            "showConfirmButton": true,
            "showCloseButton": true,
            "toast": false,
            "icon": "error"
        }';
        return;
    }

    $accounts_balance = accounts_balance($_POST["transferAcountsFrom"]);

    if(empty($_POST["transferDate"])) {
        return _e("Please select transfer date");
    } elseif(empty($_POST["transferAcountsFrom"])) {
        return _e("Please select from accounts");
    } elseif(empty($_POST["transferAcountsTO"])) {
        return _e("Please select to accounts");
    } elseif(empty($_POST["transferAmount"])) {
        return _e("Please enter transfer amount");
    } else if( !negative_value_is_allowed($_POST["transferAcountsFrom"]) and $accounts_balance < $_POST["transferAmount"] ) {
        return _e('Transfer amount is exceeded of account balance (%s)', number_format($accounts_balance, 2) );
    }

    $insertTransfer = easyInsert(
        "transfer_money",
        array (
            "transfer_money_date"       => $_POST["transferDate"],
            "transfer_money_from"       => $_POST["transferAcountsFrom"],
            "transfer_money_to"         => $_POST["transferAcountsTO"],
            "transfer_money_amount"     => $_POST["transferAmount"],
            "transfer_money_description"=> $_POST["transferDescription"],
            "transfer_money_made_by"    => $_SESSION["uid"]
        ),
        array (
            "transfer_money_date"               => $_POST["transferDate"],
            " AND transfer_money_from"          => $_POST["transferAcountsFrom"],
            " AND transfer_money_to"            => $_POST["transferAcountsTO"],
            " AND transfer_money_amount"        => $_POST["transferAmount"],
            " AND transfer_money_description"   => $_POST["transferDescription"],
            " AND transfer_money_made_by"       => $_SESSION["uid"]
        )
    );

    if($insertTransfer === true) {
        // Update Accounts Balance
        updateAccountBalance($_POST["transferAcountsFrom"]);
        updateAccountBalance($_POST["transferAcountsTO"]);

        echo _s("Transfer sucessfully completed");
    } else {
        echo _e($insertTransfer);
    }

}

/*************************** Transfer List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "transferList") {
    
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
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
 
    if(!empty($requestData["search"]["value"])) {  // get data with search
        
        $getData = easySelect(
            "transfer_money as transfer_money",
            "transfer_money_id, transfer_money_date, from_accounts.accounts_name as from_accounts_name, to_accounts.accounts_name as to_accounts_name, transfer_money_amount, transfer_money_description",
            array (
                "inner join {$table_prefeix}accounts as from_accounts on transfer_money_from = from_accounts.accounts_id",
                "inner join {$table_prefeix}accounts as to_accounts on transfer_money_to = to_accounts.accounts_id"
            ),
            array (
                "transfer_money.is_trash"  => 0,
                " AND from_accounts.accounts_name LIKE" => $requestData['search']['value'] . "%",
                " OR to_accounts.accounts_name LIKE" => $requestData['search']['value'] . "%"
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
            "transfer_money as transfer_money",
            "transfer_money_id, transfer_money_date, from_accounts.accounts_name as from_accounts_name, to_accounts.accounts_name as to_accounts_name, transfer_money_amount, transfer_money_description",
            array (
                "inner join {$table_prefeix}accounts as from_accounts on transfer_money_from = from_accounts.accounts_id",
                "inner join {$table_prefeix}accounts as to_accounts on transfer_money_to = to_accounts.accounts_id"
            ),
            array(
                "transfer_money.is_trash"  => 0
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
            $allNestedData[] = date( get_options("dateFormat"), strtotime($value["transfer_money_date"]));
            $allNestedData[] = $value["from_accounts_name"];
            $allNestedData[] = $value["to_accounts_name"];
            $allNestedData[] = $value["transfer_money_amount"];
            $allNestedData[] = $value["transfer_money_description"];
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    '.__("action").'
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=accounts&page=editTransferMoney&id='. $value["transfer_money_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i>'.__("Edit").'</a></li>
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=accounts&page=deleteTransferMoney" data-to-be-deleted="'. $value["transfer_money_id"] .'"><i class="fa fa-minus-circle"></i>'.__("Delete").'</a></li>
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


/************************** Transfer Money **********************/
if(isset($_GET['page']) and $_GET['page'] == "editTransferMoney") {

    // Include the modal header
    modal_header("Edit Transfer Money", full_website_address() . "/xhr/?module=accounts&page=updateTransferMoney");

    $selectTransfer = easySelect(
        "transfer_money",
        "*",
        array(),
        array(
            "transfer_money_id" => $_GET["id"],
            " and is_trash" => 0
        )
    )["data"][0];
    
    ?>

      <div class="box-body">
        
        <div class="form-group required">
            <label for="transferDate"><?= __("Date:");?></label>
            <input type="text" name="transferDate" id="transferDate" value="<?php echo $selectTransfer["transfer_money_date"]; ?>" class="form-control datePicker" required>
        </div>
        <div class="form-group required">
            <label for="transferAcountsFrom"><?= __("From Accounts:");?></label>
            <i data-toggle="tooltip" data-placement="right" title="Which account the money is transfer from" class="fa fa-question-circle"></i>
            <select name="transferAcountsFrom" id="transferAcountsFrom" class="form-control select2" style="width: 100%;" required>
                <option value=""><?= __("Select Accounts...");?></option>
                <?php
    
                    foreach($selectAccounts["data"] as $accounts) {
                        $selected = ( $selectTransfer["transfer_money_from"] === $accounts['accounts_id']) ? "selected" : "";
                        echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group required">
            <label for="transferAcountsTO"><?= __("To Accounts:");?></label>
            <i data-toggle="tooltip" data-placement="right" title="In which account the money is transfer to" class="fa fa-question-circle"></i>
            <select name="transferAcountsTO" id="transferAcountsTO" class="form-control select2" style="width: 100%;" required>
                <option value=""><?= __("Select Accounts...");?></option>
                <?php 
                    foreach($selectAccounts["data"] as $accounts) {
                        $selected = ( $selectTransfer["transfer_money_to"] === $accounts['accounts_id']) ? "selected" : "";
                        echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group required">
            <label for="transferAmount"><?= __("Amount:");?></label>
            <input type="number" name="transferAmount" id="transferAmount" value="<?php echo number_format($selectTransfer["transfer_money_amount"], 0, "", ""); ?>" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="transferDescription"><?= __("Description:");?></label>
            <textarea name="transferDescription" id="transferDescription" rows="3" class="form-control"><?php echo $selectTransfer["transfer_money_description"]; ?></textarea>
        </div>
        <input type="hidden" name="transfer_money_id" value="<?php echo $_GET["id"] ?>">

      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Update Transfer");
  
}


/************************** New Transfer Money **********************/
if(isset($_GET['page']) and $_GET['page'] == "updateTransferMoney") {


    if(current_user_can("transfer_money.Edit") !== true) {
        echo '{
            "title": "Sorry!",
            "text": "'. __('you do not have permission to edit transfer money.') . '",
            "showConfirmButton": true,
            "showCloseButton": true,
            "toast": false,
            "icon": "error"
        }';
        return;
    }

    $transferedAmount = easySelect(
        "transfer_money",
        "transfer_money_amount",
        array(),
        array(
            "transfer_money_id" => $_POST["transfer_money_id"]
        )
    )["data"][0]["transfer_money_amount"];

    $accounts_balance = $transferedAmount + accounts_balance($_POST["transferAcountsFrom"]);

    if(empty($_POST["transferDate"])) {
        return _e("Please select transfer date");
    } elseif(empty($_POST["transferAcountsFrom"])) {
        return _e("Please select from accounts");
    } elseif(empty($_POST["transferAcountsTO"])) {
        return _e("Please select to accounts>");
    } elseif(empty($_POST["transferAmount"])) {
        return _e("Please enter transfer amount");
    } else if( !negative_value_is_allowed($_POST["transferAcountsFrom"]) and $accounts_balance < $_POST["transferAmount"] ) {
        return _e("Transfer amount is exceeded of account balance (%d)", number_format($accounts_balance, 2) );
    }

    $insertTransfer = easyUpdate(
        "transfer_money",
        array (
            "transfer_money_date"       => $_POST["transferDate"],
            "transfer_money_from"       => $_POST["transferAcountsFrom"],
            "transfer_money_to"         => $_POST["transferAcountsTO"],
            "transfer_money_amount"     => $_POST["transferAmount"],
            "transfer_money_description"=> $_POST["transferDescription"],
            "transfer_money_made_by"    => $_SESSION["uid"]
        ),
        array (
            "transfer_money_id "        => $_POST["transfer_money_id"]
        )
    );

    if($insertTransfer === true) {
        // Update Accounts Balance
        updateAccountBalance($_POST["transferAcountsFrom"]);
        updateAccountBalance($_POST["transferAcountsTO"]);

        _s("Transfer sucessfully completed");

    } else {
        _e($insertTransfer);
    }

}


/***************** Delete Income ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteTransferMoney") {

    if(current_user_can("transfer_money.Delete") !== true) {
        echo '{
            "title": "Sorry!",
            "text": "'. __("you do not have permission to delete transfer money.") .'",
            "showConfirmButton": true,
            "showCloseButton": true,
            "toast": false,
            "icon": "error"
        }';
        return;
    }

    $deleteData = easyDelete(
        "transfer_money",
        array(
            "transfer_money_id " => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {
        echo '{
            "title": "'. __('The transfer money has been deleted successfully.') .'"
        }';
    } 
}



/************************** New Capital **********************/
if(isset($_GET['page']) and $_GET['page'] == "newCapital") {

    // Include the modal header
    modal_header("Add Capital", full_website_address() . "/xhr/?module=accounts&page=addNewCapital");
    
    ?>

      <div class="box-body">
        
        <div class="form-group required">
            <label for="capitalReceivedDate"><?= __("Date:"); ?></label>
            <input type="text" name="capitalReceivedDate" id="capitalReceivedDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker" required>
        </div>
        <div class="form-group required">
            <label for="capitalAccounts"><?= __("Accounts:"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="In which accounts the capital will be added." class="fa fa-question-circle"></i>
            <select name="capitalAccounts" id="capitalAccounts" class="form-control select2" style="width: 100%;" required>
                <option value=""><?= __("Select Accounts"); ?>...</option>
                <?php                    
                    foreach($selectAccounts["data"] as $accounts) {
                        echo "<option value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>     
        <div class="form-group required">
            <label for="capitalAmount"><?= __("Amount:"); ?></label>
            <input type="number" name="capitalAmount" id="capitalAmount" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="capitalDescription"><?= __("Description:"); ?></label>
            <textarea name="capitalDescription" id="capitalDescription" rows="3" class="form-control"></textarea>
        </div>

      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Add Capital");
  
}

/************************** Add New Capital **********************/
if(isset($_GET['page']) and $_GET['page'] == "addNewCapital") {

    if(empty($_POST["capitalReceivedDate"])) {
        return _e("Please select capital received date.");
    } elseif(empty($_POST["capitalAccounts"])) {
        return _e("Please select accounts");
    } elseif(empty($_POST["capitalAmount"])) {
        return _e("Please enter amount");
    }

    $insertCapital = easyInsert(
        "capital",
        array (
            "capital_received_date" => $_POST["capitalReceivedDate"],
            "capital_accounts"      => $_POST["capitalAccounts"],
            "capital_amounts"       => $_POST["capitalAmount"],
            "capital_description"   => $_POST["capitalDescription"],
            "capital_add_by"        => $_SESSION["uid"]
        ),
        array (
            "capital_received_date"     => $_POST["capitalReceivedDate"],
            " AND capital_accounts"     => $_POST["capitalAccounts"],
            " AND capital_amounts"      => $_POST["capitalAmount"]
        )
    );

    if($insertCapital === true) {
        // Update Accounts Balance
        updateAccountBalance($_POST["capitalAccounts"]);
        _s("Capital Successfully added");

    } else {
        _e($insertCapital);
    }

}


/*************************** Transfer List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "capitalList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "capital_received_date",
        "accounts_name",
        "capital_amounts",
        "capital_description"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "capital",
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
            "capital as capital",
            "capital_received_date, accounts_name, capital_amounts, capital_description",
            array (
                "inner join {$table_prefeix}accounts on capital_accounts = accounts_id"
            ),
            array (
                "capital.is_trash"  => 0,
                " AND accounts_name LIKE" => $requestData['search']['value'] . "%"
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
            "capital as capital",
            "capital_received_date, accounts_name, capital_amounts, capital_description",
            array (
                "inner join {$table_prefeix}accounts on capital_accounts = accounts_id"
            ),
            array(
                "capital.is_trash"  => 0
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
            $allNestedData[] = $value["capital_received_date"];
            $allNestedData[] = $value["accounts_name"];
            $allNestedData[] = $value["capital_amounts"];
            $allNestedData[] = $value["capital_description"];
            
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



/************************** New Closings **********************/
if(isset($_GET['page']) and $_GET['page'] == "newClosings") {

    // Include the modal header
    modal_header("New Closings", full_website_address() . "/xhr/?module=accounts&page=addNewClosing");
    
    ?>

    <div class="box-body">
        
        <div class="form-group required">
            <label for="closingCustomer"><?= __("Customer"); ?></label>
            <select name="closingCustomer" id="closingCustomer" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=customerList" style="width: 100%;" required>
                <option value="">Select Customer....</option>
            </select>
        </div>
        <div class="form-group required">
            <label for="closingTitle"><?= __("Title/ Label:"); ?></label>
            <input type="text" name="closingTitle" id="closingTitle" value="<?php echo date("Y"); ?>" class="form-control" maxlength="15" required>
        </div>
        <div class="form-group required">
            <label for="closingDate"><?= __("Closing Date:"); ?></label>
            <input type="text" name="closingDate" id="closingDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker" required>
        </div>
        
    
    </div>
    <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Add Capital");
  
}

/************************** Add New Capital **********************/
if(isset($_GET['page']) and $_GET['page'] == "addNewClosing") {

    if(empty($_POST["closingCustomer"])) {
        return _e("Please select the customer.");
    } elseif(empty($_POST["closingTitle"])) {
        return _e("Please enter title/ label");
    } elseif(strlen($_POST["closingTitle"]) > 15) {
        return _e("Closing title/ label can not be more then 15 character");
    } elseif(empty($_POST["closingDate"])) {
        return _e("Please select closing date");
    }

    $insertClosings = easyInsert(
        "closings",
        array (
            "closings_customer"     => $_POST["closingCustomer"],
            "closings_title"        => $_POST["closingTitle"],
            "closings_date"         => $_POST["closingDate"],
            "closings_add_by"       => $_SESSION["uid"]
        ),
        array (
            "closings_customer"     => $_POST["closingCustomer"],
            " AND closings_title"   => $_POST["closingTitle"],
        )
    );

    if($insertClosings === true) {
        
        _s("Closings has been successfully added");

    } else {

        _e($insertClosings);

    }

}


/*************************** Transfer List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "closingList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "customer_name",
        "closings_title",
        "closings_date"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "closings",
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
            "table"     => "closings as closings",
            "fields"    => "closings_id, closings_customer, customer_name, closings_title, closings_date",
            "join"      => array(
                "left join {$table_prefeix}customers on customer_id = closings_customer"
            ),
            "where"     => array(
                "closings.is_trash = 0",
                " AND customer_name LIKE"   => $requestData['search']['value'] . "%"
            ),
            "orderby"   => array(
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit"     => array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));

        $totalFilteredRecords = $getData ? $getData["count"] : 0;

    } else { // Get data withouth search

        $getData = easySelectA(array(
            "table"     => "closings as closings",
            "fields"    => "closings_id, closings_customer, customer_name, closings_title, closings_date",
            "join"      => array(
                "left join {$table_prefeix}customers on customer_id = closings_customer"
            ),
            "where"     => array(
                "closings.is_trash = 0"
            ),
            "orderby"   => array(
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit"     => array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));

    }

    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['data'])) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["customer_name"];
            $allNestedData[] = $value["closings_title"];
            $allNestedData[] = $value["closings_date"];
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a class="'. ( current_user_can("closings.Edit") ? "" : "restricted " ) .'" data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=accounts&page=editClosings&id='. $value["closings_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                    <li><a class="'. ( current_user_can("closings.Delete") ? "" : "restricted " ) .'deleteEntry" href="'. full_website_address() . '/xhr/?module=accounts&page=deleteClosings" data-to-be-deleted="'. $value["closings_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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
if(isset($_GET['page']) and $_GET['page'] == "deleteClosings") {

    if(current_user_can("closings.Delete") !== true) {
        echo '{
            "title": "Sorry!",
            "text": "'. __("you do not have permission to delete closings.") .'",
            "showConfirmButton": true,
            "showCloseButton": true,
            "toast": false,
            "icon": "error"
        }';
        return;
    }

    $deleteData = easyDelete(
        "closings",
        array(
            "closings_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {
        echo '{
            "title": "The closings has been deleted successfully."
        }';
    } 
}


/************************** Edit Closings **********************/
if(isset($_GET['page']) and $_GET['page'] == "editClosings") {

    // Include the modal header
    modal_header("Edit Closings", full_website_address() . "/xhr/?module=accounts&page=updateClosing");
    
    $closings = easySelectA(array(
        "table"     => "closings as closings",
        "fields"    => "closings_id, closings_customer, customer_name, closings_title, closings_date",
        "join"      => array(
            "left join {$table_prefeix}customers on customer_id = closings_customer"
        ),
        "where"     => array(
            "closings.is_trash = 0 and closings_id" => $_GET["id"]
        )
    ))["data"][0];
    
    ?>

    <div class="box-body">
        
        <div class="form-group required">
            <label for="closingCustomer"><?= __("Customer"); ?></label>
            <select name="closingCustomer" id="closingCustomer" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=customerList" style="width: 100%;" required>
                <option value="">Select Customer....</option>
                <option selected value="<?php echo $closings["closings_customer"]; ?>"><?php echo $closings["customer_name"]; ?></option>
            </select>
        </div>
        <div class="form-group required">
            <label for="closingTitle"><?= __("Title/ Label:"); ?></label>
            <input type="text" name="closingTitle" id="closingTitle" value="<?php echo $closings["closings_title"]; ?>" class="form-control" maxlength="15" required>
        </div>
        <div class="form-group required">
            <label for="closingDate"><?= __("Closing Date:"); ?></label>
            <input type="text" name="closingDate" id="closingDate" value="<?php echo $closings["closings_date"]; ?>" class="form-control datePicker" required>
        </div>
        <input type="hidden" name="closingsId" value="<?php echo $_GET["id"]; ?>">
    
    </div>
    <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Add Capital");
  
}

/************************** Add New Capital **********************/
if(isset($_GET['page']) and $_GET['page'] == "updateClosing") {

    if(empty($_POST["closingCustomer"])) {
        return _e("Please select the customer.");
    } elseif(empty($_POST["closingTitle"])) {
        return _e("Please enter title/ label");
    } elseif(strlen($_POST["closingTitle"]) > 15) {
        return _e("Closing title/ label can not be more then 15 character");
    } elseif(empty($_POST["closingDate"])) {
        return _e("Please select closing date");
    }

    $updateClosings = easyUpdate(
        "closings",
        array (
            "closings_customer"     => $_POST["closingCustomer"],
            "closings_title"        => $_POST["closingTitle"],
            "closings_date"         => $_POST["closingDate"],
            "closings_add_by"       => $_SESSION["uid"]
        ),
        array (
            "closings_id"     => $_POST["closingsId"]
        )
    );

    if($updateClosings === true) {
        
        _s("Closings has been updated added");

    } else {

        _e($updateClosings);

    }

}


/*************************** Amount receivable report ***********************/
if(isset($_GET['page']) and $_GET['page'] == "receivableReport") {
    
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

    //print_r($dateRange);

         
    $getData = easySelectD(
        "select customer_id, customer_name,
            if(sales_grand_total_in_filtered_date is null, 0, round(sales_grand_total_in_filtered_date, 2)) as sales_grand_total_in_filtered_date, 
            if(wastage_sale_grand_total_in_filtered_date is null, 0, round(wastage_sale_grand_total_in_filtered_date, 2)) as wastage_sale_grand_total_in_filtered_date,
            if(sales_shipping_in_filtered_date is null, 0, round(sales_shipping_in_filtered_date, 2)) as sales_shipping_in_filtered_date,
            if(product_returns_grand_total_in_filtered_date is null, 0, round(product_returns_grand_total_in_filtered_date, 2)) as product_returns_grand_total_in_filtered_date,
            if(received_payments_amount_in_filtered_date is null, 0, round(received_payments_amount_in_filtered_date, 2)) as received_payments_amount_in_filtered_date,
            if(received_payments_bonus_in_filtered_date is null, 0, round(received_payments_bonus_in_filtered_date, 2)) as received_payments_bonus_in_filtered_date,
            if(discounts_amount_in_filtered_date is null, 0, round(discounts_amount_in_filtered_date, 2)) as discounts_amount_in_filtered_date,
            if(payment_return_amount_in_filtered_date is null, 0, payment_return_amount_in_filtered_date) as payment_return_amount_in_filtered_date,
            round((
                    if(customer_opening_balance is null, 0, customer_opening_balance) +						
                    if(total_return_before_filtered_date is null, 0, total_return_before_filtered_date) +
                    if(received_payments_amount_before_filtered_date is null, 0, received_payments_amount_before_filtered_date) +
                    if(received_payments_bonus_before_filtered_date is null, 0, received_payments_bonus_before_filtered_date) +
                    if(discounts_amount_before_filtered_date is null, 0, discounts_amount_before_filtered_date)
            ) - ( 
                    if(sales_grand_total_before_filtered_date is null, 0, sales_grand_total_before_filtered_date) +
                    if(wastage_sale_grand_total_before_filtered_date is null, 0, wastage_sale_grand_total_before_filtered_date) +
                    if(payment_return_amount_before_filtered_date is null, 0, payment_return_amount_before_filtered_date)
            ), 2) as previous_balance,
            customer_phone, customer_address, upazila_name, district_name
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
            from {$table_prefeix}sales where is_trash = 0 and sales_status = 'Delivered' group by sales_customer_id
        ) as sales on sales_customer_id = customer_id
        left join ( select
                wastage_sale_customer,
                sum( case when wastage_sale_date between '{$dateRange[0]}' and '{$dateRange[1]}' then wastage_sale_grand_total end ) as wastage_sale_grand_total_in_filtered_date,
                sum( case when wastage_sale_date < '{$dateRange[0]}' then wastage_sale_grand_total end ) as wastage_sale_grand_total_before_filtered_date
            from {$table_prefeix}wastage_sale where is_trash = 0 group by wastage_sale_customer
        ) as wastage_sale on wastage_sale_customer = customer_id
        left join ( select 
                received_payments_from, 
                sum( case when date(received_payments_datetime) between '{$dateRange[0]}' and '{$dateRange[1]}' then received_payments_amount end ) as received_payments_amount_in_filtered_date,
                sum( case when date(received_payments_datetime) < '{$dateRange[0]}' then received_payments_amount end ) as received_payments_amount_before_filtered_date,
                sum( case when date(received_payments_datetime) between '{$dateRange[0]}' and '{$dateRange[1]}' then received_payments_bonus end ) as received_payments_bonus_in_filtered_date,
                sum( case when date(received_payments_datetime) < '{$dateRange[0]}' then received_payments_bonus end ) as received_payments_bonus_before_filtered_date
            from {$table_prefeix}received_payments where is_trash = 0 and received_payments_type != 'Discounts' group by received_payments_from
        ) as received_payments on received_payments.received_payments_from = customer_id
        left join ( select 
                received_payments_from,
                sum( case when date(received_payments_datetime) between '{$dateRange[0]}' and '{$dateRange[1]}' then received_payments_amount end ) as discounts_amount_in_filtered_date,
                sum( case when date(received_payments_datetime) < '{$dateRange[0]}' then received_payments_amount end ) as discounts_amount_before_filtered_date
            from {$table_prefeix}received_payments where is_trash = 0 and received_payments_type = 'Discounts' group by received_payments_from
        ) as given_discounts on given_discounts.received_payments_from = customer_id
        left join (select
                payments_return_customer_id,
                sum( case when date(payments_return_date) between '{$dateRange[0]}' and '{$dateRange[1]}' then payments_return_amount end ) as payment_return_amount_in_filtered_date,
                sum( case when date(payments_return_date) < '{$dateRange[0]}' then payments_return_amount end ) as payment_return_amount_before_filtered_date
            from {$table_prefeix}payments_return where is_trash = 0 and payments_return_type = 'Outgoing' group by payments_return_customer_id
        ) as payment_return on payments_return_customer_id = customer_id

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
            $allNestedData[] = $value['customer_name'];
            $allNestedData[] = $value['customer_phone'];
            $allNestedData[] = "{$value['customer_address']}, {$value['upazila_name']}, {$value['district_name']}";
            $allNestedData[] = round((
                                    $value["sales_grand_total_in_filtered_date"] + 
                                    $value["wastage_sale_grand_total_in_filtered_date"] +
                                    $value["payment_return_amount_in_filtered_date"]
                                ) - (
                                    $value["previous_balance"] + 
                                    $value["received_payments_amount_in_filtered_date"] + 
                                    $value["received_payments_bonus_in_filtered_date"] + 
                                    $value["product_returns_grand_total_in_filtered_date"] +
                                    $value["discounts_amount_in_filtered_date"]
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



/*************************** Amount payable report ***********************/
if(isset($_GET['page']) and $_GET['page'] == "payableReport") {
    
    $requestData = $_REQUEST;
    $getData = [];
    $search = safe_input($requestData['search']['value']);

    // List of all columns name
    $columns = array(
        "",
        "company_name"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "companies",
        "fields" => "count(*) as totalRow",
        "where" => array(
        "is_trash = 0"
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }


         
    $getData = easySelectD(
        "select 
            company_id,
            company_name,
            company_phone,
            company_address,
            company_city,
            company_state,
            company_opening_balance,
            if(totalBill is null, 0, totalBill) as totalBill,
            if(totalPaymentAmount is null, 0, totalPaymentAmount) as totalPaymentAmount,
            if(totalPaymentAdjustment is null, 0, totalPaymentAdjustment) as totalPaymentAdjustment
        from {$table_prefeix}companies as company
        left join (select 
                bills_company_id,
                sum(bills_amount) as totalBill
            from {$table_prefeix}bills
            where is_trash = 0 group by bills_company_id
        ) as bill on bills_company_id = company_id
        left join (select 
                payment_to_company,
                sum(payment_amount) as totalPaymentAmount
            from {$table_prefeix}payments
            where is_trash = 0 and payment_type is not null group by payment_to_company
        ) as payment on payment_to_company = company_id
        left join (SELECT
                pa_company,
                sum(pa_amount) as totalPaymentAdjustment
            from {$table_prefeix}payment_adjustment where is_trash = 0 group by pa_company
        ) as payment_adjustment on pa_company = company_id

        where company.is_trash = 0 and company_name like '{$search}%'
        group by company_id order by company_name {$requestData['order'][0]['dir']}
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
            $allNestedData[] = $value['company_name'];
            $allNestedData[] = $value['company_phone'];
            $allNestedData[] = "{$value['company_address']}, {$value['company_state']}, {$value['company_city']}";
            $allNestedData[] = round( ($value["company_opening_balance"] + $value["totalBill"] ) - ( $value["totalPaymentAmount"] + $value["totalPaymentAdjustment"]) );
            
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
