<?php

// Select the Accounts
$selectAccounts = easySelect("accounts", "accounts_id, accounts_name", array(), array("is_trash" => 0));

/************************** Add New Journal **********************/
if(isset($_GET['page']) and $_GET['page'] == "newJournal") {

    // Include the modal header
    modal_header("Create New Journal", full_website_address() . "/xhr/?module=journals&page=addNewJournal");
    
    ?>

      <div class="box-body">
        <div class="form-group required">
            <label for="journalDate"><?= __("Date:"); ?></label>
            <input type="text" name="journalDate" id="journalDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker" required>
        </div>
        <div class="form-group required">
            <label for="journalName"><?= __("Journal Name:"); ?></label>
            <input type="text" name="journalName" id="journalName" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="journalOpeningBalance"><?= __("Opening Balance:"); ?></label>
            <input type="number" name="journalOpeningBalance" id="journalOpeningBalance" class="form-control" value="0" required>
        </div>
        <div id="ajaxSubmitMsg"></div>

      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer();
  
}


/************************** Add new Journal **********************/
if(isset($_GET['page']) and $_GET['page'] == "addNewJournal") {

    if(current_user_can("journal.Add") !== true) {
        return _e("Sorry! you do not have permission to add new Journal.");
    }

    if(empty($_POST["journalDate"])) {
        return _e("Please select date.");
    } else if(empty($_POST["journalName"])) {
        return _e("Please journal name.");
    }

    $addJournal = easyInsert(
        "journals",
        array (
            "journals_date"             => $_POST["journalDate"],
            "journals_name"             => $_POST["journalName"],
            "journals_opening_balance"  => $_POST["journalOpeningBalance"],
            "journals_add_by"           => $_SESSION["uid"]
        ), 
        array (
            "journals_date"         => $_POST["journalDate"],
            " AND journals_name"    => $_POST["journalName"],
        )
    );

    if($addJournal === true) {
        _s("Journal Successfully Added.");
    } else {
        _e($addJournal);
    }

}


/*************************** Journal List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "journalList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "journals_id",
        "journals_name"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "journals",
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
            "journals",
            "journals_id, journals_date, journals_name, journals_opening_balance, if(journal_incoming_payment is null, 0, journal_incoming_payment) as journal_incoming_payment_sum, if(journal_outgoing_payment is null, 0, journal_outgoing_payment) as journal_outgoing_payment_sum",
            array (
                "left join ( select journal_records_journal_id, sum(journal_records_payment_amount) as journal_incoming_payment from {$table_prefix}journal_records where journal_records_payments_type = 'Incoming' group by journal_records_journal_id ) as journal_incoming_records on journal_incoming_records.journal_records_journal_id = journals_id",
                "left join ( select journal_records_journal_id, sum(journal_records_payment_amount) as journal_outgoing_payment from {$table_prefix}journal_records where journal_records_payments_type = 'Outgoing' group by journal_records_journal_id ) as journal_outgoing_records on journal_outgoing_records.journal_records_journal_id = journals_id"
            ),
            array (
                "journals_name LIKE" => $requestData['search']['value'] . "%"
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
            "journals",
            "journals_id, journals_date, journals_name, journals_opening_balance, if(journal_incoming_payment is null, 0, journal_incoming_payment) as journal_incoming_payment_sum, if(journal_outgoing_payment is null, 0, journal_outgoing_payment) as journal_outgoing_payment_sum",
            array (
                "left join ( select journal_records_journal_id, sum(journal_records_payment_amount) as journal_incoming_payment from {$table_prefix}journal_records where journal_records_payments_type = 'Incoming' group by journal_records_journal_id ) as journal_incoming_records on journal_incoming_records.journal_records_journal_id = journals_id",
                "left join ( select journal_records_journal_id, sum(journal_records_payment_amount) as journal_outgoing_payment from {$table_prefix}journal_records where journal_records_payments_type = 'Outgoing' group by journal_records_journal_id ) as journal_outgoing_records on journal_outgoing_records.journal_records_journal_id = journals_id"
            ),
            array(),
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
            $journalStatus = "<span class='btn-sm btn-danger'>Unbalanced</span>";
            
            if( ( $value["journals_opening_balance"] + $value["journal_incoming_payment_sum"] ) == $value["journal_outgoing_payment_sum"] ) {
                $journalStatus = "<span class='btn-sm btn-success'>Balanced</span>";
            }

            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value["journals_date"];
            $allNestedData[] = $value["journals_name"];
            $allNestedData[] = to_money($value["journals_opening_balance"]);
            $allNestedData[] = to_money( ( $value["journals_opening_balance"] + $value["journal_incoming_payment_sum"] ) - $value["journal_outgoing_payment_sum"] );
            $allNestedData[] = $journalStatus;
            $allNestedData[] = '<a class="'. ( current_user_can("journal.Edit") ? "" : "restricted " ) .'btn-sm btn-primary" data-toggle="modal" data-target="#modalDefault" href="'. full_website_address() . '/xhr/?tooltip=true&select2=true&module=journals&page=editJournal&journal_id='. $value["journals_id"] .'"><i class="fa fa-edit"></i> Edit</a>';
            
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


/************************** Add New Journal **********************/
if(isset($_GET['page']) and $_GET['page'] == "editJournal") {

    $selectJournal = easySelect(
        "journals",
        "*",
        array(),
        array (
            "journals_id"   => $_GET["journal_id"]
        )
    )["data"][0];

    // Include the modal header
    modal_header("Edit Journal", full_website_address() . "/xhr/?module=journals&page=UpdateJournal");
    
    ?>

      <div class="box-body">
        <div class="form-group required">
            <label for="journalDate"><?= __("Date:"); ?></label>
            <input type="text" name="journalDate" id="journalDate" value="<?php echo $selectJournal['journals_date']; ?>" class="form-control datePicker" required>
        </div>
        <div class="form-group required">
            <label for="journalName"><?= __("Journal Name:"); ?></label>
            <input type="text" name="journalName" id="journalName" class="form-control" value="<?php echo $selectJournal['journals_name']; ?>" required>
        </div>
        <div class="form-group required">
            <label for="journalOpeningBalance"><?= __("Opening Balance:"); ?></label>
            <input type="number" name="journalOpeningBalance" id="journalOpeningBalance" class="form-control" value="<?php echo $selectJournal['journals_opening_balance']; ?>" required>
        </div>
        <input type="hidden" name="journal_id" value="<?php echo safe_entities($_GET["journal_id"]); ?>">

        <div id="ajaxSubmitMsg"></div>

      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer("Update");
  
}


/************************** Add new Journal **********************/
if(isset($_GET['page']) and $_GET['page'] == "UpdateJournal") {

    if(!current_user_can("journal.Edit")) {
        return _e("You have no permission to edit journal.");
    }

    if(empty($_POST["journalDate"])) {
        return _e("Please select date");
    } else if(empty($_POST["journalName"])) {
        return _e("Please journal name.");
    }

    $updateJournal = easyUpdate(
        "journals",
        array (
            "journals_date"             => $_POST["journalDate"],
            "journals_name"             => $_POST["journalName"],
            "journals_opening_balance"  => $_POST["journalOpeningBalance"]
        ),
        array (
            "journals_id"   => $_POST["journal_id"]
        )
    );

    if($updateJournal === true) {
        _s("Journal successfully updated.");
    } else {
        _e($updateJournal);
    }

}


/************************** Add New Journal **********************/
if(isset($_GET['page']) and $_GET['page'] == "newJournalRecords") {

    // Include the modal header
    modal_header("New Journal Record", full_website_address() . "/xhr/?module=journals&page=addNewJournalRecord");
    
    ?>

      <div class="box-body">
        <div class="form-group required">
            <label for="journalID"><?= __("Journal"); ?></label>
            <select name="journalID" id="journalID" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=journalList" style="width: 100%;" required>
                <option value=""><?= __("Select Journal"); ?>....</option>
            </select>
        </div>
        <div class="form-group required">
            <label for="journalRecordsPaymentType"><?= __("Payment Type"); ?></label>
            <div style="margin-top: 0;" class="radio">
                <label for="journalRecrodPaymentType1">
                    <input type="radio" name="journalRecrodPaymentType" value="Incoming" id="journalRecrodPaymentType1" required>
                    Incoming
                </label>
                <label style="margin-left: 50px; margin-top: 0;" for="journalRecrodPaymentType2">
                    <input type="radio" name="journalRecrodPaymentType" value="Outgoing" id="journalRecrodPaymentType2" required>
                    Outgoing
                </label>
            </div>
        </div>
        <div class="form-group required">
            <label for="journalRecordsDate"><?= __("Date:"); ?></label>
            <input type="text" name="journalRecordsDate" id="journalRecordsDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker" required>
        </div>
        <div class="form-group required">
            <label for="journalRecordPaymentFromAccount"><?= __("Accounts"); ?></label>
            <select name="journalRecordPaymentFromAccount" id="journalRecordPaymentFromAccount" class="form-control select2" style="width: 100%;" required>
                <option value=""><?= __("Select Accounts"); ?>...</option>
                <?php
                    foreach($selectAccounts["data"] as $accounts) {
                        echo "<option value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group required">
            <label for="journalRecordsAmount"><?= __("Amount:"); ?></label>
            <input type="number" name="journalRecordsAmount" id="journalRecordsAmount" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="journalRecordsNarration"><?= __("Narration:"); ?></label>
            <textarea name="journalRecordsNarration" id="journalRecordsNarration" rows="3" class="form-control"></textarea>
        </div>

        <div id="ajaxSubmitMsg"></div>

      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer();
  
}


/************************** Add new Journal Records **********************/
if(isset($_GET['page']) and $_GET['page'] == "addNewJournalRecord") {

    if( !current_user_can("journal_records.Add") ) {
        return _e("Sorry! you have no permission to entry journal records.");
    }

    $accounts_balance = accounts_balance($_POST["journalRecordPaymentFromAccount"]);

    if(empty($_POST["journalID"])) {
        return _e("Please select journal.");
    } else if(empty($_POST["journalRecrodPaymentType"])) {
        return _e("Please select payment type.");
    } else if(empty($_POST["journalRecordsDate"])) {
        return _e("Please select date.");
    } else if(empty($_POST["journalRecordPaymentFromAccount"])) {
        return _e("Please select account.");
    } else if(empty($_POST["journalRecordsAmount"])) {
        return _e("Please enter amount.");
    } else if(!negative_value_is_allowed($_POST["journalRecordPaymentFromAccount"]) and $_POST["journalRecrodPaymentType"] === "Outgoing" and $accounts_balance < $_POST["journalRecordsAmount"] ) {
        return _e("Transfer amount is exceeded of account balance (%.2f)", number_format($accounts_balance, 2));
    }

    // Select last payment references
    $selectJournalRecordReference = easySelect(
        "journal_records",
        "journal_records_reference",
        array(),
        array (
            "journal_records_add_by"   => $_SESSION['uid'],
            " AND journal_records_reference is not null"
        ),
        array (
            "journal_records_id" => "DESC"
        ),
        array (
            "start" => 0,
            "length" => 1
        )
    );

    // Referense Format: SALE/POS/n
    $journalRecordReferences = "JR/{$_SESSION['uid']}/";

    // check if there is minimum one records
    if($selectJournalRecordReference !== false) {
        $getLastReferenceNo = (int)explode($journalRecordReferences, $selectJournalRecordReference["data"][0]["journal_records_reference"])[1];
        $journalRecordReferences = $journalRecordReferences . ($getLastReferenceNo+1);
    } else {
        $journalRecordReferences = "JR/{$_SESSION['uid']}/1";
    }


    $addJournal = easyInsert(
        "journal_records",
        array (
            "journal_records_datetime"      => $_POST["journalRecordsDate"] .' '. date('H:i:s'),
            "journal_records_reference"     => $journalRecordReferences,
            "journal_records_journal_id"    => $_POST["journalID"],
            "journal_records_accounts"      => $_POST["journalRecordPaymentFromAccount"],
            "journal_records_payments_type" => $_POST["journalRecrodPaymentType"],
            "journal_records_payment_amount" => $_POST["journalRecordsAmount"],
            "journal_records_narration"     => $_POST["journalRecordsNarration"],
            "journal_records_add_by"        => $_SESSION["uid"]
        ), 
        array (
            "journal_records_journal_id"            => $_POST["journalID"],
            " AND journal_records_accounts"         => $_POST["journalRecordPaymentFromAccount"],
            " AND journal_records_payment_amount"   => $_POST["journalRecordsAmount"],
            " AND journal_records_narration"        => $_POST["journalRecordsNarration"],
            " AND journal_records_payments_type"    => $_POST["journalRecrodPaymentType"],
            " AND date(journal_records_datetime)"   => $_POST["journalRecordsDate"],
            " AND journal_records_add_by"           => $_SESSION["uid"]
        ),
        true
    );

    if( isset($addJournal["status"]) and $addJournal["status"] === "success" ) {

        // Update Accounts Balance
       updateAccountBalance($_POST["journalRecordPaymentFromAccount"]);

       $successMsg = sprintf(__("Journal record successfully added. The refernece is %s. <a %s>Click Here</a> to print the receipt."), $journalRecordReferences, " onClick='BMS.MAIN.printPage(this.href, event);' href='". full_website_address() ."/invoice-print/?invoiceType=moneyReceiptJournalRecords&autoPrint=true&id=". $addJournal["last_insert_id"] ."'");

        echo "<div class='alert alert-success'>{$successMsg}</div>";
    
    } else {
        _e($addJournal);
    }

}


/*************************** Journal List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "journalRecordList") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "journal_records_datetime",
        "journal_records_reference",
        "journals_name",
        "accounts_name",
        "category_name",
        "journal_records_payments_type",
        "journal_records_payment_amount",
        "journal_records_narration"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "journal_records",
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
            "journal_records as journal_records",
            "journal_records_id, journal_records_datetime, journal_records_reference, journal_records_journal_id, journals_name, journal_records_accounts, accounts_name, journal_records_payments_type, journal_records_payment_amount, journal_records_narration",
            array (
                "left join {$table_prefix}journals on journals_id = journal_records_journal_id",
                "left join {$table_prefix}accounts on accounts_id = journal_records_accounts"
            ),
            array (
                "journal_records.is_trash = 0", 
                " and journals_name LIKE" => $requestData['search']['value'] . "%",
                " OR journal_records_reference LIKE" => $requestData['search']['value'] . "%",
                " OR journal_records_narration LIKE" => $requestData['search']['value'] . "%"
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
            "journal_records as journal_records ",
            "journal_records_id, journal_records_datetime, journal_records_reference, journal_records_journal_id, journals_name, journal_records_accounts, accounts_name, journal_records_payments_type, journal_records_payment_amount, journal_records_narration",
            array (
                "left join {$table_prefix}journals on journals_id = journal_records_journal_id",
                "left join {$table_prefix}accounts on accounts_id = journal_records_accounts"
            ),
            array("journal_records.is_trash = 0"),
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
            $allNestedData[] = $value["journal_records_datetime"];
            $allNestedData[] = $value["journal_records_reference"];
            $allNestedData[] = $value["journals_name"];
            $allNestedData[] = $value["accounts_name"];
            $allNestedData[] = $value["journal_records_payments_type"];
            $allNestedData[] = $value["journal_records_payment_amount"];
            $allNestedData[] = $value["journal_records_narration"];
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        <li><a onClick=\'BMS.MAIN.printPage(this.href, event);\' href="'. full_website_address() .'/invoice-print/?invoiceType=moneyReceiptJournalRecords&autoPrint=true&id='. $value["journal_records_id"] .'"><i class="fa fa-print"></i> Print Receipt</a></li>
                                        <li><a class="'. ( current_user_can("journal_records.Edit") ? "" : "restricted " ) .'" data-toggle="modal" href="'. full_website_address() .'/xhr/?module=journals&page=editJournalRecord&id='. $value["journal_records_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                        <li><a class="'. ( current_user_can("journal_records.Delete") ? "" : "restricted " ) .'deleteEntry" href="'. full_website_address() . '/xhr/?module=journals&page=deleteJournalRecords" data-to-be-deleted="'. $value["journal_records_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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


/************************** Add New Journal **********************/
if(isset($_GET['page']) and $_GET['page'] == "editJournalRecord") {

    // Include the modal header
    modal_header("Edit Journal Record", full_website_address() . "/xhr/?module=journals&page=updateJournalRecord");

    $journalRecord = easySelect(
        "journal_records as journal_records",
        "*",
        array(
            "left join {$table_prefix}journals on journal_records_journal_id  = journals_id "
        ),
        array(
            "journal_records_id" => $_GET['id'],
            " and journal_records.is_trash" => 0
        )
    )["data"][0];

    ?>

      <div class="box-body">
        <div class="form-group required">
            <label for="journalID"><?= __("Journal"); ?></label>
            <select name="journalID" id="journalID" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=journalList" style="width: 100%;" required>
                <option value="<?php echo $journalRecord["journal_records_journal_id"]; ?>"><?php echo $journalRecord["journals_name"]; ?></option>
            </select>
        </div>
        <div class="form-group required">
            <label for="journalRecordsPaymentType"><?= __("Payment Type"); ?></label>
            <div style="margin-top: 0;" class="radio">
                <label for="journalRecrodPaymentType1">
                    <input <?php echo ($journalRecord["journal_records_payments_type"] === "Incoming") ? "checked" : ""; ?> type="radio" name="journalRecrodPaymentType" value="Incoming" id="journalRecrodPaymentType1" required>
                    <?= __("Incoming"); ?>
                </label>
                <label style="margin-left: 50px; margin-top: 0;" for="journalRecrodPaymentType2">
                    <input <?php echo ($journalRecord["journal_records_payments_type"] === "Outgoing") ? "checked" : ""; ?> type="radio" name="journalRecrodPaymentType" value="Outgoing" id="journalRecrodPaymentType2" required>
                    <?= __("Outgoing"); ?>
                </label>
            </div>
        </div>
        <div class="form-group required">
            <label for="journalRecordsDate"><?= __("Date:"); ?></label>
            <input type="text" name="journalRecordsDate" id="journalRecordsDate" value="<?php echo date("Y-m-d"); ?>" class="form-control datePicker" required>
        </div>
        <div class="form-group required">
            <label for="journalRecordPaymentFromAccount"><?= __("Accounts"); ?></label>
            <select name="journalRecordPaymentFromAccount" id="journalRecordPaymentFromAccount" class="form-control select2" style="width: 100%;" required>
                <option value=""><?= __("Select Accounts"); ?>...</option>
                <?php
                    
                    foreach($selectAccounts["data"] as $accounts) {
                        $selected = ($journalRecord["journal_records_accounts"] == $accounts['accounts_id']) ? "selected" : "";
                        echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group required">
            <label for="journalRecordsAmount"><?= __("Amount:"); ?></label>
            <input type="number" name="journalRecordsAmount" id="journalRecordsAmount" class="form-control" value="<?php echo number_format($journalRecord["journal_records_payment_amount"], 0, '.', ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="journalRecordsNarration"><?= __("Narration:"); ?></label>
            <textarea name="journalRecordsNarration" id="journalRecordsNarration" rows="3" class="form-control"> <?php echo $journalRecord["journal_records_narration"]; ?> </textarea>
        </div>
        <input type="hidden" name="journal_record_id" value="<?php echo safe_entities($_GET["id"]); ?>">

        <div id="ajaxSubmitMsg"></div>

      </div>
      <!-- /Box body-->

    <?php
  
    // Include the modal footer
    modal_footer();
  
}


/************************** Add new Journal Records **********************/
if(isset($_GET['page']) and $_GET['page'] == "updateJournalRecord") {

    if( !current_user_can("journal_records.Edit") ) {
        return _e("Sorry! you have no permission to edit journal records.");
    }

    // Current Account ballance must be add with the current journal record payment amount, Because
    // If current account balance is 30
    // And current journal records amount payment is 20
    // And now if update the journal records payment amount with 45. Then it raise an error if we dont add(+) the amount.
    // Bacause 45 is grater then of account balance (30). So If add(+) the amount, then the current account balance will (30 + 20) = 50 which is grater then update amount. So this will not raise any error.
    $accounts_balance = easySelect(
        "journal_records as journal_records",
        "*",
        array(
            "left join {$table_prefix}journals on journal_records_journal_id  = journals_id "
        ),
        array(
            "journal_records_id" => $_POST['journal_record_id'],
            " and journal_records.is_trash" => 0
        )
    )["data"][0]["journal_records_payment_amount"];
        
    $accounts_balance += accounts_balance($_POST["journalRecordPaymentFromAccount"]);

    if(empty($_POST["journalID"])) {
        return _e("Please select journal.");
    } else if(empty($_POST["journalRecrodPaymentType"])) {
        return _e("Please select payment type.");
    } else if(empty($_POST["journalRecordsDate"])) {
        return _e("Please select date.");
    } else if(empty($_POST["journalRecordPaymentFromAccount"])) {
        return _e("Please select account.");
    } else if(empty($_POST["journalRecordsAmount"])) {
        return _e("Please enter amount.");
    } else if(!negative_value_is_allowed($_POST["journalRecordPaymentFromAccount"]) and $_POST["journalRecrodPaymentType"] === "Outgoing" and $accounts_balance < $_POST["journalRecordsAmount"] ) {
        return _e("Transfer amount is exceeded of account balance (%.2f)", number_format($accounts_balance, 2));
    }

    $updateJournalRecord = easyUpdate(
        "journal_records",
        array (
            "journal_records_datetime"      => $_POST["journalRecordsDate"] .' '. date('H:m:s'),
            "journal_records_journal_id"    => $_POST["journalID"],
            "journal_records_accounts"      => $_POST["journalRecordPaymentFromAccount"],
            "journal_records_payments_type" => $_POST["journalRecrodPaymentType"],
            "journal_records_payment_amount" => $_POST["journalRecordsAmount"],
            "journal_records_narration"     => $_POST["journalRecordsNarration"]
        ),
        array (
            "journal_records_id"   => $_POST["journal_record_id"]
        )
    );

    if($updateJournalRecord === true) {
        // Update Accounts Balance
       updateAccountBalance($_POST["journalRecordPaymentFromAccount"]);

        echo _s("Journal record successfully updated.");

    } else {
        _e($updateJournalRecord);
    }

}


/***************** Delete Journal Records ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteJournalRecords") {

    // Select accounts id of delected journal records
    $selectAccountId = easySelect(
        "journal_records",
        "journal_records_accounts",
        array(),
        array (
            "journal_records_id" => $_POST["datatoDelete"]
        )
    )["data"][0]["journal_records_accounts"];

    // Delect the journal records
    $deleteData = easyDelete(
        "journal_records",
        array(
            "journal_records_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {
        // Update accounts Balance
        updateAccountBalance($selectAccountId);
        echo 1;
    } 
}


?>