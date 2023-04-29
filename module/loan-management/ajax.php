<?php

// Select the Accounts
$selectAccounts = easySelect("accounts", "accounts_id, accounts_name", array(), array("is_trash" => 0));

/************************** Pay Loan **********************/
if(isset($_GET['page']) and $_GET['page'] == "payLoan") {
  
    // Include the modal header
    modal_header("Pay Loan", full_website_address() . "/xhr/?module=loan-management&page=payNewLoan");
    
    ?>
    
      <div class="box-body">
        
        <div class="form-group required">
            <label for="loanBorrower"><?= __("Loan Borrower"); ?></label>
            <select name="loanBorrower" id="loanBorrower" select2-minimum-input-length="1" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=employeeList" style="width: 100%;" required>
                <option value=""><?= __("Select Employee"); ?>....</option>
            </select>
        </div>
        <div class="form-group required">
            <label for="loanPayingFromAccounts"><?= __("Accounts"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="Which accounts the loan is paying from." class="fa fa-question-circle"></i>
            <select name="loanPayingFromAccounts" id="loanPayingFromAccounts" class="form-control select2" style="width: 100%;" required>
                <?php
                    
                    foreach($selectAccounts["data"] as $accounts) {
                        $selected = (isset($_SESSION['aid']) and $_SESSION['aid'] === $accounts['accounts_id']) ? "selected" : "";
                        echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group required">
            <label for="loanAmount"><?= __("Loan Amount"); ?></label>
            <input type="number" name="loanAmount" id="loanAmount" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="installmentStartingDate"><?= __("Installment Starting From"); ?></label>
            <input type="text" name="installmentStartingDate" id="installmentStartingDate" class="form-control" required autoComplete="off">
        </div>
<!--         <div class="form-group required">
            <label for="installmentInterval">Installment Interval</label>
            <div class="input-group data">
                <input type="number" value="1" max="12" name="installmentInterval" id="installmentInterval" class="form-control">            
                <span class="bg-info input-group-addon">Month(s)</span>
            </div>
        </div> -->
        <div class="form-group required">
            <label for="installmentAmount"><?= __("Installment Amount"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="How much amount will be deducted per interval." class="fa fa-question-circle"></i>
            <input type="number" name="installmentAmount" id="installmentAmount" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="loanGranter"><?= __("Loan Granter"); ?></label>
            <input type="text" name="loanGranter" id="loanGranter" class="form-control">
        </div>
        <div class="form-group">
            <label for="loanDetails"><?= __("Loan Details"); ?></label>
            <textarea name="loanDetails" id="loanDetails" rows="3" class="form-control"></textarea>
        </div>

        <div id="ajaxSubmitMsg"></div>

      </div>
      <!-- /Box body-->

      <script>
        $("#installmentStartingDate").datepicker({
            format: 'MM, yyyy',
            autoclose: true,
            viewMode: "months",
            minViewMode: "months"
        });
      </script>

    <?php
  
    // Include the modal footer
    modal_footer("Pay Loan");
  
}


/************************** Pay New Loan **********************/
if(isset($_GET['page']) and $_GET['page'] == "payNewLoan") {

    if( !current_user_can("loan.Add") ) {
        return _e("Sorry! you do not have permission to pay new loan");
    }

    $accounts_balance = accounts_balance($_POST["loanPayingFromAccounts"]);

    if(empty($_POST["loanBorrower"]))  {
        return _e("Please select borrower");
    } elseif(empty($_POST["loanAmount"]))  {
        return _e("Please enter loan amount");
    } elseif(empty($_POST["installmentStartingDate"]))  {
        return _e("Please enter installment starting date");
    } elseif(empty($_POST["installmentAmount"]))  {
        return _e("Please enter installment amount");
    } else if( !negative_value_is_allowed($_POST["loanPayingFromAccounts"]) and $accounts_balance < $_POST["loanAmount"] ) {
        return _e("Payment amount is exceeded of account balance (%.2f)", number_format($accounts_balance, 2));
    }

    // Insert Loan and installment Details
    $insertLoan = easyInsert(
        "loan",
        array (
            "loan_borrower"                 => $_POST["loanBorrower"],
            "loan_paying_from"              => $_POST["loanPayingFromAccounts"],
            "loan_amount"                   => $_POST["loanAmount"],
            "loan_installment_interval"     => 1,
            "loan_installment_starting_from"=> !empty($_POST["installmentStartingDate"]) ? DateTime::createFromFormat('d F, Y', '01 '.$_POST["installmentStartingDate"])->format('Y-m-d') : "",
            "loan_installment_amount"       => $_POST["installmentAmount"],
            "loan_granter"                  => $_POST["loanGranter"],
            "loan_details"                  => $_POST["loanDetails"],
            "loan_pay_by"                   => $_SESSION["sid"]   
        ),
        array(),
        true
    );


    if($insertLoan["status"] === "success") {
        // Update Accounts Balance
        updateAccountBalance($_POST["loanPayingFromAccounts"]);

        echo _s("Loan successfully created. Please <a %s>click here to print</a> the receipt.", "onClick='BMS.MAIN.printPage(this.href, event);' href='".full_website_address()."/invoice-print/?autoPrint=true&invoiceType=loanPaymentReceipt&id={$insertLoan['last_insert_id']}'");
        
        // _s("oan successfully created.");

    } else {
        _e($insertLoan);
    }

}



/*************************** Loan List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "loanList") {

    if( !current_user_can("loan.View") ) {
        return _e("Sorry! you do not have permission to view loan list");
    }
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "loan_pay_on",
        "emp_firstname",
        "accounts_name",
        "loan_amount",
        "loan_installment_amount",
        "paid_loan",
        "due_loan"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "loan",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
    
    $search = safe_input($requestData['search']['value']);
    $orderBy = safe_input($requestData['order'][0]['dir']);
  
    $getData = easySelectD(
        "SELECT
            loan_id,
            emp_firstname, 
            emp_lastname, 
            emp_PIN,
            accounts_name,
            loan_amount,
            loan_installment_amount,
            if(paid_loan is null, 0, paid_loan) as paid_loan,
            (loan_amount - if(paid_loan is null, 0, paid_loan)) as due_loan,
            loan_pay_on
        from {$table_prefix}loan as loan
        left join {$table_prefix}accounts on loan_paying_from = accounts_id
        left join (select 
                loan_ids, 
                sum(loan_installment_paying_amount) as paid_loan 
            from {$table_prefix}loan_installment where is_trash = 0 group by loan_ids
        ) as loan_installment on loan_id = loan_ids
        left join {$table_prefix}employees on loan_borrower = emp_id
        where loan.is_trash = 0 and emp_firstname like '{$search}%' or emp_PIN = '{$search}'
        order by 
            {$columns[$requestData['order'][0]['column']]} {$orderBy}
        "
    );

    $totalFilteredRecords = $getData ? $getData["count"] : 0;

    $allData = [];
    // Check if there have more then zero data
    if($getData) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = date("d M, Y", strtotime($value["loan_pay_on"]));
            $allNestedData[] = $value["emp_firstname"] . ' ' . $value["emp_lastname"] . ' (' . $value["emp_PIN"] . ')';
            $allNestedData[] = $value["accounts_name"];
            $allNestedData[] = $value["loan_amount"];
            $allNestedData[] = $value["loan_installment_amount"];
            $allNestedData[] = $value["paid_loan"];
            $allNestedData[] = $value["due_loan"];
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a <a onClick=\'BMS.MAIN.printPage(this.href, event);\' target="_blank" href="'. full_website_address() .'/invoice-print/?autoPrint=true&invoiceType=loanPaymentReceipt&id='. $value["loan_id"] .'"><i class="fa fa-print"></i> Print Receipt</a></li>
                                    <li><a class="'. ( current_user_can("Loan.Edit") ? "" : "restricted" ) .'" data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=loan-management&page=editLoan&id='. $value["loan_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                    <li><a class="'. ( current_user_can("Loan.Delete") ? "" : "restricted " ) .'deleteEntry" href="'. full_website_address() . '/xhr/?module=loan-management&page=deleteLoan" data-to-be-deleted="'. $value["loan_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
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


/************************** Pay Loan **********************/
if(isset($_GET['page']) and $_GET['page'] == "editLoan") {

    if( !current_user_can("loan.Edit") ) {
        return _e("Sorry! you do not have permission to edit loan");
    }
  
    // Include the modal header
    modal_header("Edit Loan", full_website_address() . "/xhr/?module=loan-management&page=updateLoan");

    $loan_id = safe_input($_GET["id"]);

    $selectLoan = easySelectD(
        "SELECT
            *
        from {$table_prefix}loan as loan
        left join {$table_prefix}employees on loan_borrower = emp_id
        where loan.is_trash = 0 and loan_id = '{$loan_id}'
        "
    )["data"][0];
    
    ?>

      <div class="box-body">
        
        <div class="form-group required">
            <label for="loanBorrower"><?= __("Loan Borrower"); ?></label>
            <select name="loanBorrower" id="loanBorrower" select2-minimum-input-length="1" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=employeeList" style="width: 100%;" required>
                <option value="<?php echo $selectLoan["loan_borrower"]; ?>"><?php echo $selectLoan["emp_firstname"] . ' ' . $selectLoan["emp_lastname"] ?></option>
            </select>
        </div>
        <div class="form-group required">
            <label for="loanPayingFromAccounts"><?= __("Accounts"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="Which accounts the loan is paying from." class="fa fa-question-circle"></i>
            <select name="loanPayingFromAccounts" id="loanPayingFromAccounts" class="form-control select2" style="width: 100%;" required>
                <?php
                    foreach($selectAccounts["data"] as $accounts) {
                        $selected = $selectLoan["loan_paying_from"] === $accounts['accounts_id'] ? "selected" : "";
                        echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="form-group required">
            <label for="loanAmount"><?= __("Loan Amount"); ?></label>
            <input type="number" name="loanAmount" id="loanAmount" value="<?php echo number_format($selectLoan["loan_amount"], 2, ".", ""); ?>" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="installmentStartingDate"><?= __("Installment Starting From"); ?></label>
            <input type="text" name="installmentStartingDate" id="installmentStartingDate" value = "<?php echo date("M, Y", strtotime($selectLoan["loan_installment_starting_from"]) ); ?>" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="installmentAmount"><?= __("Installment Amount"); ?></label>
            <i data-toggle="tooltip" data-placement="right" title="How much amount will be deducted per interval." class="fa fa-question-circle"></i>
            <input type="number" name="installmentAmount" id="installmentAmount" value="<?php echo number_format($selectLoan["loan_installment_amount"], 2, ".", ""); ?>"  class="form-control" required>
        </div>
        <div class="form-group">
            <label for="loanGranter"><?= __("Loan Granter"); ?></label>
            <input type="text" name="loanGranter" id="loanGranter" value="<?php echo $selectLoan["loan_granter"]; ?>" class="form-control">
        </div>
        <div class="form-group">
            <label for="loanDetails"><?= __("Loan Details"); ?></label>
            <textarea name="loanDetails" id="loanDetails" rows="3" class="form-control"><?php echo $selectLoan["loan_details"]; ?></textarea>
        </div>
        <input type="hidden" name="loan_id" value="<?php echo safe_entities($_GET["id"]); ?>">

        <div id="ajaxSubmitMsg"></div>

      </div>
      <!-- /Box body-->

      <script>
        $("#installmentStartingDate").datepicker({
            format: 'MM, yyyy',
            autoclose: true,
            viewMode: "months",
            minViewMode: "months"
        });
      </script>

    <?php
  
    // Include the modal footer
    modal_footer();
  
}



/***************** Delete Loan ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteLoan") {

    if(current_user_can("loan.Delete") !== true) {
        echo '{
            "title": "Sorry!",
            "text": "'. __("you do not have permission to delete loan.") .'",
            "showConfirmButton": true,
            "showCloseButton": true,
            "toast": false,
            "icon": "error"
        }';
        return;
    }

    $deleteData = easyDelete(
        "loan",
        array(
            "loan_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {
        echo '{
            "title": "'. __("The loan has been deleted successfully.") .'"
        }';
    } 
}


/************************** Pay New Loan **********************/
if(isset($_GET['page']) and $_GET['page'] == "updateLoan") {

    if( !current_user_can("loan.Edit") ) {
        return _e("Sorry! you do not have permission to edit loan");
    }

    $accounts_balance = accounts_balance($_POST["loanPayingFromAccounts"]);

    // Update account balance with current loan ammount
    $selectLoanDetails = easySelect(
        "loan",
        "loan_amount, loan_paying_from",
        array(),
        array(
            "loan_id" => $_POST["loan_id"]
        )
    )["data"][0];

    $accounts_balance += $selectLoanDetails["loan_amount"];

    if(empty($_POST["loanBorrower"]))  {
        return _e("Please select borrower");
    } elseif(empty($_POST["loanAmount"]))  {
        return _e("Please enter loan amount");
    } elseif(empty($_POST["installmentStartingDate"]))  {
        return _e("Please enter installment starting date");
    } elseif(empty($_POST["installmentAmount"]))  {
        return _e("Please enter installment amount");
    } else if( !negative_value_is_allowed($_POST["loanPayingFromAccounts"]) and $accounts_balance < $_POST["loanAmount"] ) {
        return _e("Payment amount is exceeded of account balance (%.2f)", number_format($accounts_balance, 2));
    }

    // Insert Loan and installment Details
    $insertLoan = easyUpdate(
        "loan",
        array (
            "loan_borrower"                 => $_POST["loanBorrower"],
            "loan_paying_from"              => $_POST["loanPayingFromAccounts"],
            "loan_amount"                   => $_POST["loanAmount"],
            "loan_installment_interval"     => 1,
            "loan_installment_starting_from"=> !empty($_POST["installmentStartingDate"]) ? DateTime::createFromFormat('d F, Y', '01 '.$_POST["installmentStartingDate"])->format('Y-m-d') : "",
            "loan_installment_amount"       => $_POST["installmentAmount"],
            "loan_granter"                  => $_POST["loanGranter"],
            "loan_details"                  => $_POST["loanDetails"],
            "loan_pay_by"                   => $_SESSION["sid"]   
        ),
        array(
            "loan_id" => $_POST["loan_id"]
        )
    );


    if($insertLoan === true) {

        // Update paying Accounts Balance
        updateAccountBalance($_POST["loanPayingFromAccounts"]);

        // Update edited account balance
        updateAccountBalance($selectLoanDetails["loan_paying_from"]);

        _s("Loan successfully updated.");

    } else {
        _e($insertLoan);
    }

}


/*************************** Loans Installment List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "loanInstallmentList") {

    if( !current_user_can("loan_installment.View") ) {
        return _e("Sorry! you do not have permission to view loan installment list");
    }
    
    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name
    $columns = array(
        "",
        "loan_installment_paying_date",
        "loan_ids",
        "loan_installment_date",
        "emp_firstname",
        "",
        "loan_installment_paying_amount"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "loan_installment",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];

    if($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }
    
    $search = safe_input($requestData['search']['value']);
    $orderBy = safe_input($requestData['order'][0]['dir']);
  
    $getData = easySelectD(
        "SELECT
            loan_installment_id,
            loan_installment_date,
            loan_ids,
            loan_installment_provider,
            emp_firstname,
            emp_lastname,
            emp_PIN,
            loan_installment_receiving_accounts,
            accounts_name,
            loan_installment_paying_amount,
            loan_installment_description,
            loan_installment_paying_date
        from {$table_prefix}loan_installment as loan_installment
        left join {$table_prefix}accounts on loan_installment_receiving_accounts = accounts_id
        left join {$table_prefix}employees on loan_installment_provider = emp_id
        where loan_installment.is_trash = 0 and ( emp_firstname like '{$search}%' or emp_PIN = '{$search}')
        order by 
            {$columns[$requestData['order'][0]['column']]} {$orderBy}
        "
    );

    $totalFilteredRecords = $getData ? $getData["count"] : 0;

    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['count']) and $getData['count'] > 0) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = date("d M, Y", strtotime($value["loan_installment_paying_date"]));
            $allNestedData[] = $value["loan_ids"];
            $allNestedData[] = date("M, Y", strtotime($value["loan_installment_date"]));
            $allNestedData[] = $value["emp_firstname"] . ' ' . $value["emp_lastname"] . ' (' . $value["emp_PIN"] . ')';
            $allNestedData[] = $value["accounts_name"];
            $allNestedData[] = $value["loan_installment_paying_amount"];
            $allNestedData[] = '<a class="'. ( current_user_can("loan_installment.Delete") ? "" : "restricted " ) .'deleteEntry" href="'. full_website_address() . '/xhr/?module=loan-management&page=deleteLoanInstallment" data-to-be-deleted="'. $value["loan_installment_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>';
            
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


/***************** Delete Loan ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteLoanInstallment") {

    if(current_user_can("loan_installment.Delete") !== true) {
        echo '{
            "title": "Sorry!",
            "text": "'. __("you do not have permission to delete loan installment.") .'",
            "showConfirmButton": true,
            "showCloseButton": true,
            "toast": false,
            "icon": "error"
        }';
        return;
    }

    $deleteData = easyDelete(
        "loan_installment",
        array(
            "loan_installment_id" => $_POST["datatoDelete"]
        )
    );

    if($deleteData === true) {
        echo '{
            "title": "'. __("The loan installment has been deleted successfully.") .'"
        }';
    } 

}



/************************** Pay Loan **********************/
if(isset($_GET['page']) and $_GET['page'] == "addInstallment") {
  
    // Include the modal header
    modal_header("Add Loan Installment", full_website_address() . "/xhr/?module=loan-management&page=addNewInstallment");
    
    ?>
    
      <div class="box-body">
        
        <div class="form-group required">
            <label for="loanBorrower"><?= __("Installment Provider"); ?></label>
            <select name="loanBorrower" id="loanBorrower" select2-minimum-input-length="1" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=employeeList" style="width: 100%;" required>
                <option value=""><?= __("Select Employee"); ?>....</option>
            </select>
        </div>
        <div class="form-group required">
            <label for="loanInstallmentMonth"><?= __("Installment Month"); ?></label>
            <input type="text" name="loanInstallmentMonth" id="loanInstallmentMonth" value="<?php echo date("m, Y"); ?>" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="installmentAmount"><?= __("Installment Details"); ?></label>
            <table id="loan_installment_details" class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center"><?= __("Loan Amount"); ?></th>
                        <th class="text-center"><?= __("Paid Amount"); ?></th>
                        <th class="text-center"><?= __("Due Amount"); ?></th>
                        <th class="text-center"><?= __("Installment"); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-right">0.00</td>
                        <td class="text-right">0.00</td>
                        <td class="text-right">0.00</td>
                        <td class="text-right">0.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="form-group">
            <label for="receiveAllDueLoan">
                <input type="checkbox" name="receiveAllDueLoan" id="receiveAllDueLoan" class="square"> <?= __("Receive all due loan"); ?>
            </label>
        </div>
        <div class="form-group">
            <label for="installmentReceivingAccounts"><?= __("Accounts"); ?></label>
            <select name="installmentReceivingAccounts" id="installmentReceivingAccounts" class="form-control select2" style="width: 100%;">
                <option value=""><?= __("Select Accounts"); ?></option>
                <?php
                    foreach($selectAccounts["data"] as $accounts) {
                        echo "<option value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                    }
                ?>
            </select>
            <small class="alert-danger"><?= __("Please do not select any accounts if the installment need to be paid from employee's salary."); ?></small>
        </div>
        <div class="form-group">
            <label for="loanInstallmentDetails"><?= __("Installment Details"); ?></label>
            <textarea name="loanInstallmentDetails" id="loanInstallmentDetails" rows="3" class="form-control"></textarea>
        </div>

        <div id="ajaxSubmitMsg"></div>

      </div>
      <!-- /Box body-->

      <script>
        $("#loanInstallmentMonth").datepicker({
            format: 'mm, yyyy',
            autoclose: true,
            viewMode: "months",
            minViewMode: "months"
        });

        $(document).on("change ifChanged", "#loanBorrower, #receiveAllDueLoan, #loanInstallmentMonth", function() {
            
            if($("#loanBorrower").val() === "") {
                alert("Please select installment provider");
                return;
            } 

            var installmentDate = $("#loanInstallmentMonth").val().split(", ");

            $.post(
                "<?php echo full_website_address(); ?>/info/?module=data&page=getEmpLoanLoanData",
                {
                    
                    empId: $("#loanBorrower").val(),
                    month: installmentDate[0],
                    year: installmentDate[1]
                },

                function (data, status) {

                    if(data == 0) {
                        return;
                    }

                    var empLoanData = JSON.parse(data);


                    if ( $("#receiveAllDueLoan").is(":checked") ) {
                        
                        /* View Total Loan Amount */
                        $("#loan_installment_details  tbody td:nth-child(1)").html(empLoanData.totalLoan);
                        /* View Total Paid Amount */
                        $("#loan_installment_details  tbody td:nth-child(2)").html(empLoanData.totalLoanPaid);
                        /* View Total Due Amount */
                        $("#loan_installment_details  tbody td:nth-child(3)").html(empLoanData.totalLoan - empLoanData.totalLoanPaid);
                        /* View Total Installment Amount */
                        $("#loan_installment_details  tbody td:nth-child(4)").html(empLoanData.totalLoan - empLoanData.totalLoanPaid);

                    } else {

                        /* View Total Loan Amount */
                        $("#loan_installment_details  tbody td:nth-child(1)").html(empLoanData.totalLoan);
                        /* View Total Paid Amount */
                        $("#loan_installment_details  tbody td:nth-child(2)").html(empLoanData.totalLoanPaid);
                        /* View Total Due Amount */
                        $("#loan_installment_details  tbody td:nth-child(3)").html(empLoanData.totalLoan - empLoanData.totalLoanPaid);
                        /* View Total Installment Amount */
                        $("#loan_installment_details  tbody td:nth-child(4)").html(empLoanData.totalInstallmentAmount);
                       

                    }                  

                }
            );
        })

      </script>

    <?php
  
    // Include the modal footer
    modal_footer("Pay Loan");
  
}


/************************** Add Loan installment **********************/
if(isset($_GET['page']) and $_GET['page'] == "addNewInstallment") {

    if( !current_user_can("loan_installment.Add") ) {
        return _e("Sorry! you do not have permission to add loan installment");
    }

    if(empty($_POST["loanBorrower"]))  {
        return _e("Please select installment provider");
    } elseif(empty($_POST["loanInstallmentMonth"]))  {
        return _e("Please select installment month");
    }

    $installment_is_added = false;

    $installmentDate = explode(", ", safe_input($_POST["loanInstallmentMonth"]));
    $emp_id = safe_input($_POST["loanBorrower"]);
    $month = $installmentDate[0];
    $year = $installmentDate[1];

    $getLoanDetails = easySelectD(
        "select 
            loan_id, loan_amount, loan_installment_amount, 
            if(thisMonthInstallmentPayingStatus is null, 0, 1) as thisMonthInstallmentPayingStatus,
            if(loan_paid_amount is null, 0, loan_paid_amount) as loan_paid_amount
        from {$table_prefix}loan as loan
        left join (select 
                loan_ids, 
                sum(loan_installment_paying_amount) as loan_paid_amount 
            from {$table_prefix}loan_installment where is_trash = 0 group by loan_ids
        ) as totalPaidAmount on loan_id = totalPaidAmount.loan_ids
        left join (select 
                loan_ids, 1 as thisMonthInstallmentPayingStatus
            from {$table_prefix}loan_installment where is_trash = 0 and MONTH(loan_installment_date) = '{$month}' and year(loan_installment_date) = '{$year}' group by loan_ids 
        ) as thisMonthStatus on loan_id = thisMonthStatus.loan_ids
        where loan.is_trash = 0 and loan_borrower = '{$emp_id}' and loan_installment_starting_from <= '{$year}-{$month}-01'
        and ( loan_paid_amount is null or loan_paid_amount < loan_amount)" 
        // loan_paid_amount can be NULL on left join if there is no records, for that the is null check.
        // We can also use HAVING clause without using is null check. But it will raise a error with full_group_by mode.
    );

    // Check if there any Loan Data Exists
    if(isset($getLoanDetails["data"])) {

        foreach($getLoanDetails["data"] as $key => $loan) {

            // Total cut loan will be stored in this variable
            $totalDeductedLoanFromSalary = 0;

            $unpaidLoan = $loan["loan_amount"] - $loan["loan_paid_amount"];

            // Check if the loan installment already paid
            // and installmentOption is not 3. 
            // 3 means cut off All loan
            if($loan["thisMonthInstallmentPayingStatus"] == 1 and !isset($_POST["receiveAllDueLoan"])) {
                continue;
            }

            // Calculate unpaid loan
            if(isset($_POST["receiveAllDueLoan"])) {
                $totalDeductedLoanFromSalary = $unpaidLoan;
            } else {
                $totalDeductedLoanFromSalary = ($unpaidLoan >= $loan["loan_installment_amount"]) ? $loan["loan_installment_amount"] : $unpaidLoan;
            }

            // Add loan installment
            easyInsert(
                "loan_installment",
                array (
                    "loan_ids"                              => $loan["loan_id"],
                    "loan_installment_provider"             => $_POST["loanBorrower"],
                    "loan_installment_receiving_accounts"   => empty($_POST["installmentReceivingAccounts"]) ? NULL : $_POST["installmentReceivingAccounts"],
                    "loan_installment_paying_amount"        => $totalDeductedLoanFromSalary,
                    "loan_installment_date"                 => $year .'-'. $month . '-01',
                    "loan_installment_paying_date"          => date("Y-m-d H:i:s"),
                    "loan_installment_description"          => $_POST["loanInstallmentDetails"],
                    "loan_installment_receive_by"           => $_SESSION["uid"]
                )
            );

            $installment_is_added = true;

        }
    }


    if($installment_is_added === true) {

        // Update receiving Accounts Balance
        if(!empty($_POST["installmentReceivingAccounts"])) {
            updateAccountBalance($_POST["installmentReceivingAccounts"]);
        }

        _s("Installment(s) successfully added.");

    } else {
        _e("There is no loan installment to be added");
    }

}


?>