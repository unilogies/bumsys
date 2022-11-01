<?php

/*************************** Employee Ledger ***********************/
if(isset($_GET['page']) and $_GET['page'] == "employeeLedger") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // Count Total recrods
    $totalFilteredRecords = $totalRecords = 0;
    $allData = [];

    if(isset($_GET["emp_id"])) {

        $dateRange = explode(" - ", safe_input($requestData["dateRange"]));
        $emp_id = safe_input($_GET["emp_id"]);
        	
		$previous_balance = easySelectD("
			SELECT 
				@balance := (
						if(emp_opening_salary is null, 0, emp_opening_salary) +
						if(emp_opening_overtime is null, 0, emp_opening_overtime) + 
						if(emp_opening_bonus is null, 0, emp_opening_bonus) +
						if(total_salary_added_before_filtered_date is null, 0, total_salary_added_before_filtered_date)
						
				) - (
						if(total_salary_paid_before_filtered_date is null, 0, total_salary_paid_before_filtered_date) +
						if(loan_installment_paying_amount_before_filtered_date is null, 0, loan_installment_paying_amount_before_filtered_date)
				)
			FROM {$table_prefeix}employees as employees
			left join ( select
					salary_emp_id,
					sum(salary_amount) as total_salary_added_before_filtered_date
				from {$table_prefeix}salaries where is_trash = 0 and date(salary_add_on) < '{$dateRange[0]}' group by salary_emp_id
			) as added_salaries on salary_emp_id = emp_id
			left join ( select
					loan_installment_provider,
					sum(loan_installment_paying_amount) as loan_installment_paying_amount_before_filtered_date
				from {$table_prefeix}loan_installment where is_trash = 0 and loan_installment_paying_date < '{$dateRange[0]}' group by loan_installment_provider
			) as loan_installment on loan_installment.loan_installment_provider = emp_id
			left join ( select
					payment_items_payments_id,
					payment_items_employee,
					sum(payment_items_amount) as total_salary_paid_before_filtered_date
				from {$table_prefeix}payment_items where is_trash = 0 and payment_items_type != 'Bill' and payment_items_date < '{$dateRange[0]}' group by payment_items_employee 
			) as payments on payment_items_employee = emp_id
			where emp_id = '{$emp_id}'
		");

		
		$getData = easySelectD("
			SELECT empl_id, ledger_date, description, debit, credit, @balance := ( @balance + credit ) - debit as balance from
            (
                select 
                    1 as sortby,
                    '{$emp_id}' as empl_id,
                    '' as ledger_date,
                    'Opening/Previous Balance' as description,
                    0 as debit,
                    0 as credit
                UNION ALL
                SELECT
                    2 as sortby,
                    salary_emp_id as empl_id,
                    date(salary_add_on) as ledger_date,
                    combine_description( group_concat(salary_type, ' for ', date_format(salary_month, '%b, &apos;%y') ), salary_description) as description,
                    0 as debit,
                    salary_amount as credit
                from {$table_prefeix}salaries
                where is_trash = 0 and date(salary_add_on) between '{$dateRange[0]}' and '{$dateRange[1]}' group by salary_id
				UNION ALL
				SELECT
					3 as sortby,
					loan_installment_provider as empl_id,
					date(loan_installment_paying_date) as ledger_date,
					concat('Loan Installment for ', monthname(loan_installment_date), ', ', year(loan_installment_date) ) as description,
					loan_installment_paying_amount as debit,
					0 as credit
				from {$table_prefeix}loan_installment as loan_installment_received
				where loan_installment_received.is_trash = 0 and date(loan_installment_received.loan_installment_paying_date) between '{$dateRange[0]}' and '{$dateRange[1]}' group by loan_installment_id
				UNION ALL
                SELECT
                    3 as sortby, 
                    payment_items_employee as empl_id,
                    payment_items_date as ledger_date,
                    combine_description( concat(payment_items_type, ' payment'), payment_items_description) as description,
                    payment_items_amount as debit,
                    0 as credit
                from {$table_prefeix}payment_items
				where is_trash = 0 and payment_items_type != 'Bill' and payment_items_date between '{$dateRange[0]}' and '{$dateRange[1]}' group by payment_items_id
			) as get_data
			where empl_id = '{$emp_id}'
            order by ledger_date, sortby
		");
			

        $totalFilteredRecords = $totalRecords = $getData["count"];

        // Check if there have more then zero data
        if(isset($getData['data'])) {
            
            foreach($getData['data'] as $key => $value) {
                $allNestedData = [];
				$allNestedData[] = "";
                $allNestedData[] = $value["ledger_date"];
                $allNestedData[] = $value["description"];
				$allNestedData[] = number_format($value["debit"], get_options("decimalPlaces"), ".", "");
                $allNestedData[] = number_format($value["credit"], get_options("decimalPlaces"), ".", "");
                $allNestedData[] = number_format($value["balance"], get_options("decimalPlaces"));
                
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



/*************************** Accounts Ledger ***********************/
if(isset($_GET['page']) and $_GET['page'] == "accountsLedger") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // Count Total recrods
    $totalFilteredRecords = $totalRecords = 0;
    $allData = [];

    if(isset($_GET["account_id"])) {

        $dateRange = explode(" - ", safe_input($requestData["dateRange"]));
        $account_id = safe_input($_GET["account_id"]);
        	
		$previous_balance = easySelectD("
			SELECT 
				@balance := (
						if(accounts_opening_balance is null, 0, accounts_opening_balance) +
						if(incomes_amount_sum is null, 0, incomes_amount_sum) + 
						if(journal_records_payment_amount_incoming is null, 0, journal_records_payment_amount_incoming) +
						if(received_payments_amount_sum is null, 0, received_payments_amount_sum) +
						if(transfer_money_amount_received is null, 0, transfer_money_amount_received) +
                        if(capital_amount_sum is null, 0, capital_amount_sum) +
                        if(payment_incoming_return_amount_sum is null, 0, payment_incoming_return_amount_sum)
				) - (
						if(advance_payment_amount_sum is null, 0, advance_payment_amount_sum) +
						if(journal_records_payment_amount_outgoing is null, 0, journal_records_payment_amount_outgoing) +
						if(loan_amount_sum is null, 0, loan_amount_sum) +
						if(payment_amount_sum is null, 0, payment_amount_sum) +
						if(transfer_money_amount_sent is null, 0, transfer_money_amount_sent) +
                        if(payment_outgoing_return_amount_sum is null, 0, payment_outgoing_return_amount_sum)
				)
			FROM {$table_prefeix}accounts accounts
			left join (
					SELECT 
							advance_payment_pay_from, 
							sum(advance_payment_amount) as advance_payment_amount_sum
					FROM {$table_prefeix}advance_payments as advance_payments
					WHERE advance_payments.is_trash = 0 and advance_payment_date < '{$dateRange[0]}'
					group by advance_payment_pay_from
			) as advance_payments on advance_payments.advance_payment_pay_from = accounts.accounts_id
			left join (
					SELECT
							incomes_accounts_id,
							sum(incomes_amount) as incomes_amount_sum
					FROM {$table_prefeix}incomes
					WHERE is_trash = 0 and incomes_date < '{$dateRange[0]}'
					group by incomes_accounts_id
			) as incomes on incomes.incomes_accounts_id = accounts.accounts_id
			left join (
					SELECT
							journal_records_accounts,
							sum(journal_records_payment_amount)  as journal_records_payment_amount_outgoing
					FROM {$table_prefeix}journal_records
					WHERE is_trash = 0 and date(journal_records_datetime) < '{$dateRange[0]}' and journal_records_payments_type = 'Outgoing'
					group by journal_records_accounts
			) as journal_records_outgoing on journal_records_outgoing.journal_records_accounts = accounts.accounts_id
			left join (
					SELECT
							journal_records_accounts,
							sum(journal_records_payment_amount)  as journal_records_payment_amount_incoming
					FROM {$table_prefeix}journal_records
					WHERE is_trash = 0 and date(journal_records_datetime) < '{$dateRange[0]}' and journal_records_payments_type = 'Incoming'
					group by journal_records_accounts
			) as journal_records_incoming on journal_records_incoming.journal_records_accounts = accounts.accounts_id
			left join (
					SELECT
							loan_paying_from,
							sum(loan_amount) as loan_amount_sum
					FROM {$table_prefeix}loan
					WHERE is_trash = 0 and date(loan_pay_on) < '{$dateRange[0]}'
					group by loan_paying_from
			) as loan on loan.loan_paying_from = accounts.accounts_id
			left join (
					SELECT
							payment_from,
							sum(payment_amount) as payment_amount_sum
					FROM {$table_prefeix}payments
					where is_trash = 0 and payment_status != 'Cancel' and ( payment_type != 'Advance Adjustment' or payment_type is null)
					and payment_date < '{$dateRange[0]}'
					group by payment_from
			) as payments on payments.payment_from = accounts.accounts_id
			left join (
				SELECT
						payments_return_accounts,
                        sum( case when payments_return_type = 'Incoming' then payments_return_amount end ) as payment_incoming_return_amount_sum,
                        sum( case when payments_return_type = 'Outgoing' then payments_return_amount end ) as payment_outgoing_return_amount_sum
				FROM {$table_prefeix}payments_return
				WHERE is_trash = 0 and date(payments_return_date) < '{$dateRange[0]}'
				group by payments_return_accounts
			) as payments_return on payments_return.payments_return_accounts = accounts.accounts_id
			left join (
					SELECT
							received_payments_accounts,
							SUM(received_payments_amount) as received_payments_amount_sum
					FROM {$table_prefeix}received_payments
					where is_trash = 0 and received_payments_type != 'Discounts'
					and date(received_payments_datetime) < '{$dateRange[0]}'
					group by received_payments_accounts
			) as received_payments on received_payments.received_payments_accounts = accounts.accounts_id
			left join (
					SELECT
							transfer_money_to,
							sum(transfer_money_amount) as transfer_money_amount_received
					FROM {$table_prefeix}transfer_money
					WHERE is_trash = 0 and transfer_money_date < '{$dateRange[0]}'
					group by transfer_money_to
			) as transfer_money_received on transfer_money_received.transfer_money_to = accounts.accounts_id
			left join (
					SELECT
							transfer_money_from,
							sum(transfer_money_amount) as transfer_money_amount_sent
					FROM {$table_prefeix}transfer_money
					WHERE is_trash = 0 and transfer_money_date < '{$dateRange[0]}'
					group by transfer_money_from
			) as transfer_money_sent on transfer_money_sent.transfer_money_from = accounts.accounts_id
            left join (
                SELECT
                        capital_accounts,
                        sum(capital_amounts) as capital_amount_sum
                FROM {$table_prefeix}capital
                WHERE is_trash = 0 and capital_received_date < '{$dateRange[0]}'
                group by capital_accounts
            ) as capital on capital.capital_accounts = accounts.accounts_id
			where accounts_id = '{$account_id}'
		");

		
		$getData = easySelectD("
			SELECT account_id, ledger_date_time, sql_join_id, sql_join_id_two, description, debit, credit, @balance := ( @balance + debit ) - credit as balance from
			(
				SELECT 
					'{$account_id}' as account_id,
					'' as ledger_date_time,
					'' as sql_join_id,
					'' as sql_join_id_two,
					'Opening/Previous Balance' as description,
					0 as debit,
					0 as credit
				UNION ALL
				SELECT 
					advance_payment_pay_from as account_id,
					concat(advance_payment_date, ' ', date_format(advance_payment_pay_on, '%H:%i:%s') ) as ledger_date_time,
					advance_payment_pay_to as sql_join_id,
					'' as sql_join_id_two,
                    combine_description( concat('Advance Payment to ', emp_firstname, ' ', emp_lastname, ' (', emp_PIN, ')' ), advance_payment_description ),
					0 as debit,
					advance_payment_amount as credit
				from {$table_prefeix}advance_payments as advance_payments
				left join {$table_prefeix}employees on advance_payment_pay_to = emp_id
				where advance_payments.is_trash = 0 and advance_payment_date between '{$dateRange[0]}' and '{$dateRange[1]}'
				UNION ALL
				SELECT
					incomes_accounts_id,
					concat(incomes_date, ' ', date_format(incomes_add_on, '%H:%i:%s') ),
					'' as sql_join_id,
					'' as sql_join_id_two,
					combine_description('Income', incomes_description),
					incomes_amount,
					0
				from {$table_prefeix}incomes
				where is_trash = 0 and incomes_date between '{$dateRange[0]}' and '{$dateRange[1]}'
				UNION ALL
				SELECT
					journal_records_accounts,
					journal_records_datetime,
					journal_records_journal_id,
					'',
					combine_description( concat('Journal payment to ', journals_name), journal_records_narration),
					0,
					journal_records_payment_amount
				from {$table_prefeix}journal_records as outgoing_journal_records
				left join {$table_prefeix}journals on journal_records_journal_id = journals_id
				where outgoing_journal_records.is_trash = 0 and journal_records_payments_type = 'Outgoing' 
				and date(journal_records_datetime) between '{$dateRange[0]}' and '{$dateRange[1]}'
				UNION ALL
				SELECT
					journal_records_accounts,
					journal_records_datetime,
					journal_records_journal_id,
					'',
					combine_description( concat('Journal payment received from ', journals_name), journal_records_narration),
					journal_records_payment_amount,
					0
				from {$table_prefeix}journal_records as incoming_journal_records
				left join {$table_prefeix}journals on journal_records_journal_id = journals_id
				where incoming_journal_records.is_trash = 0 and journal_records_payments_type = 'Incoming'
				and date(journal_records_datetime) between '{$dateRange[0]}' and '{$dateRange[1]}'
				UNION ALL
				SELECT
					loan_paying_from,
					loan_pay_on,
					loan_borrower,
					'',
					combine_description( concat('Loan pay to ', emp_firstname, ' ', emp_lastname, ' (', emp_PIN, ')' ), loan_details),
					0,
					loan_amount
				from {$table_prefeix}loan as loan
				left join {$table_prefeix}employees on loan_borrower = emp_id
				where loan.is_trash = 0 and date(loan_pay_on) between '{$dateRange[0]}' and '{$dateRange[1]}'
				UNION ALL
				SELECT
					payment_items_accounts,
					concat(payment_items_date, ' ', date_format(payment_items_add_on, '%H:%i:%s') ),
					payment_items_payments_id,
					payment_items_category_id,
					combine_description( concat(payment_items_type,  if(payment_belongs_to is null, concat(' pay of ', payment_category_name), concat(' pay to ', payment_belongs_to) ) ), payment_items_description),
					0,
					payment_items_amount
				from {$table_prefeix}payment_items as payment_items
				left join {$table_prefeix}payments_categories on payment_items_category_id = payment_category_id
				inner join (
					SELECT
						payment_id,
						if(payment_to_employee is null or payment_to_employee = 0, company_name, concat(emp_firstname, ' ', emp_lastname, ' (', emp_PIN, ')' ) ) as payment_belongs_to,
						payment_type,
						payment_to_company,
						payment_to_employee
					from {$table_prefeix}payments as payments
					left join {$table_prefeix}employees on payment_to_employee = emp_id
					left join {$table_prefeix}companies on payment_to_company = company_id
					where payments.is_trash = 0 and payment_status != 'Cancel' and ( payment_type != 'Advance Adjustment' or payment_type is null)
					and payment_date between '{$dateRange[0]}' and '{$dateRange[1]}'
				) as get_payment_info on payment_items_payments_id = payment_id
				where payment_items.is_trash = 0
				UNION ALL
				SELECT
					payments_return_accounts,
					payments_return_date,
					payments_return_emp_id,
					payments_return_company_id,
					combine_description( concat('Payment return from ', if(company_name is null,  concat(emp_firstname, '', 'emp_lastname', ' (', emp_PIN, ')' ), company_name ) ), payments_return_description),
					payments_return_amount,
					0
				from {$table_prefeix}payments_return as payments_return
				left join {$table_prefeix}employees on payments_return_emp_id = emp_id
                left join {$table_prefeix}companies on payments_return_company_id = company_id
				where payments_return.is_trash = 0 and payments_return_type = 'Incoming' and date(payments_return_date) between '{$dateRange[0]}' and '{$dateRange[1]}'
				UNION ALL
                SELECT
					payments_return_accounts,
					payments_return_date,
					payments_return_customer_id,
					'',
					combine_description( concat('Payment return to ', customer_name ), payments_return_description),
					0,
					payments_return_amount
				from {$table_prefeix}payments_return as payments_return
				left join {$table_prefeix}customers on payments_return_customer_id = customer_id
				where payments_return.is_trash = 0 and payments_return_type = 'Outgoing' and date(payments_return_date) between '{$dateRange[0]}' and '{$dateRange[1]}'
				UNION ALL
				SELECT
					received_payments_accounts,
					received_payments_datetime,
					received_payments_from,
					'',
					combine_description( concat(received_payments_type, ' from ', customer_name, ', ', upazila_name, ', ', district_name), received_payments_details),
					received_payments_amount,
					0
				from {$table_prefeix}received_payments as received_payments
				left join {$table_prefeix}customers on received_payments_from = customer_id
				left join {$table_prefeix}upazilas on upazila_id = customer_upazila
				left join {$table_prefeix}districts on district_id = customer_district
				where received_payments.is_trash = 0 and received_payments_type != 'Discounts' and date(received_payments_datetime) between '{$dateRange[0]}' and '{$dateRange[1]}'
                UNION ALL
				SELECT
					transfer_money_to,
					concat(transfer_money_date, ' ', date_format(transfer_money_made_on, '%H:%i:%s') ),
					transfer_money_from,
					'',
					combine_description( concat('Transfer money from ', accounts_name), transfer_money_description),
					transfer_money_amount,
					0
				from {$table_prefeix}transfer_money as transfer_money_outgoing
				left join {$table_prefeix}accounts on transfer_money_from = accounts_id
				where transfer_money_outgoing.is_trash = 0 and transfer_money_date between '{$dateRange[0]}' and '{$dateRange[1]}'
				UNION ALL
				SELECT
					transfer_money_from,
					concat(transfer_money_date, ' ', date_format(transfer_money_made_on, '%H:%i:%s') ),
					transfer_money_to,
					'',
					combine_description( concat('Transfer money to ', accounts_name), transfer_money_description),
					0,
					transfer_money_amount
				from {$table_prefeix}transfer_money as transfer_money_incoming
				left join {$table_prefeix}accounts on transfer_money_to = accounts_id
				where transfer_money_incoming.is_trash = 0 and transfer_money_date between '{$dateRange[0]}' and '{$dateRange[1]}'
                UNION ALL
				SELECT
                    capital_accounts,
					concat(capital_received_date, ' ', date_format(capital_add_on, '%H:%i:%s') ),
					'',
					'',
					combine_description('Capital', capital_description),
					capital_amounts,
                    0
				from {$table_prefeix}capital as capital
				where capital.is_trash = 0 and capital_received_date between '{$dateRange[0]}' and '{$dateRange[1]}'
			) as getData
			where account_id = '{$account_id}'
			order by ledger_date_time ASC
		");
			

        //echo $getData;

        $totalFilteredRecords = $totalRecords = $getData["count"];

        // Check if there have more then zero data
        if(isset($getData['data'])) {
			
			// create date format
			$dateTimeFormat = get_options("dateFormat") . " " . get_options("timeFormat");

            foreach($getData['data'] as $key => $value) {
				
                $allNestedData = [];
				$allNestedData[] = "";
                $allNestedData[] = empty($value["ledger_date_time"]) ? "" : date($dateTimeFormat, strtotime($value["ledger_date_time"]));
                $allNestedData[] = $value["description"];
                $allNestedData[] = number_format($value["debit"], get_options("decimalPlaces"), ".", "");
                $allNestedData[] = number_format($value["credit"], get_options("decimalPlaces"), ".", "");
                $allNestedData[] = number_format($value["balance"], get_options("decimalPlaces")) ;
                
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


/*************************** Journal Ledger ***********************/
if(isset($_GET['page']) and $_GET['page'] == "journalLedger") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // Count Total recrods
    $totalFilteredRecords = $totalRecords = 0;
    $allData = [];

    if(isset($_GET["journal_id"])) {

        $dateRange = explode(" - ", safe_input($requestData["dateRange"]));
        $journal_id = safe_input($_GET["journal_id"]);
        	
		$previous_balance = easySelectD("
			SELECT 
				@balance := (
						if(journals_opening_balance is null, 0, journals_opening_balance) +
						if(journal_records_incoming_payment_amount_before_filtered_date is null, 0, journal_records_incoming_payment_amount_before_filtered_date)
				) - (
						if(journal_records_outgoing_payment_amount_before_filtered_date is null, 0, journal_records_outgoing_payment_amount_before_filtered_date)
				)
			FROM {$table_prefeix}journals as journals
			left join ( select
					journal_records_journal_id,
					sum(journal_records_payment_amount) as journal_records_outgoing_payment_amount_before_filtered_date
				from {$table_prefeix}journal_records where is_trash = 0 and journal_records_payments_type = 'Outgoing' and date(journal_records_datetime) < '{$dateRange[0]}' group by journal_records_journal_id
			) as journal_records_Outgoing on journal_records_Outgoing.journal_records_journal_id = journals_id
			left join ( select
					journal_records_journal_id,
					sum(journal_records_payment_amount) as journal_records_incoming_payment_amount_before_filtered_date
				from {$table_prefeix}journal_records where is_trash = 0 and journal_records_payments_type = 'Incoming' and date(journal_records_datetime) < '{$dateRange[0]}' group by journal_records_journal_id
			) as journal_records_Incoming on journal_records_Incoming.journal_records_journal_id = journals_id
			where journals_id  = '{$journal_id}'
		");

		
		$getData = easySelectD("
			SELECT journals_id, ledger_date, description, debit, credit, @balance := ( @balance + credit ) - debit as balance from
            (
                select 
                    1 as sortby,
                    '{$journal_id}' as journals_id,
                    '' as ledger_date,
                    'Opening/Previous Balance' as description,
                    0 as debit,
                    0 as credit
                UNION ALL
                SELECT
                    2 as sortby,
                    journal_records_journal_id as journals_id,
                    date(journal_records_datetime) as ledger_date,
                    combine_description('Incoming Payment' , journal_records_narration) as description,
                    0 as debit,
                    journal_records_payment_amount as credit
                from {$table_prefeix}journal_records as journal_records_incoming
				where journal_records_incoming.is_trash = 0 and journal_records_incoming.journal_records_payments_type = 'Incoming' and date(journal_records_incoming.journal_records_datetime) between '{$dateRange[0]}' and '{$dateRange[1]}' group by journal_records_incoming.journal_records_id
				UNION ALL
                SELECT
                    2 as sortby,
                    journal_records_journal_id as journals_id,
                    date(journal_records_datetime) as ledger_date,
                    combine_description('Outgoing Payment' , journal_records_narration) as description,
                    journal_records_payment_amount as debit,
                    0 as credit
                from {$table_prefeix}journal_records as journal_records_outgoing
                where journal_records_outgoing.is_trash = 0 and journal_records_outgoing.journal_records_payments_type = 'Outgoing' and date(journal_records_outgoing.journal_records_datetime) between '{$dateRange[0]}' and '{$dateRange[1]}' group by journal_records_outgoing.journal_records_id
			) as get_data
			where journals_id = '{$journal_id}'
            order by ledger_date, sortby
		");
			

        $totalFilteredRecords = $totalRecords = $getData["count"];

        // Check if there have more then zero data
        if(isset($getData['data'])) {
            
            foreach($getData['data'] as $key => $value) {
                $allNestedData = [];
				$allNestedData[] = "";
                $allNestedData[] = $value["ledger_date"];
                $allNestedData[] = $value["description"];
				$allNestedData[] = number_format($value["debit"], get_options("decimalPlaces"), ".", "");
                $allNestedData[] = number_format($value["credit"], get_options("decimalPlaces"), ".", "");
                $allNestedData[] = number_format($value["balance"], get_options("decimalPlaces")) ;
                
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



/*************************** Customer Ledger ***********************/
if(isset($_GET['page']) and $_GET['page'] == "customerLedger") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // Count Total recrods
    $totalFilteredRecords = $totalRecords = 0;
    $allData = [];

    if(isset($_GET["customer_id"])) {

        $dateRange = explode(" - ", safe_input($requestData["dateRange"]));
        $customer_id = safe_input($_GET["customer_id"]);
        	
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
                    sum(case when is_return = 1 then sales_grand_total end) as total_returned_before_filtered_date
				from {$table_prefeix}sales where is_trash = 0 and sales_status = 'Delivered' and sales_delivery_date < '{$dateRange[0]}' group by sales_customer_id
			) as sales on sales_customer_id = customer_id
			left join ( select
                    wastage_sale_customer,
                    sum(wastage_sale_grand_total) as total_wastage_purched_before_filtered_date
                from {$table_prefeix}wastage_sale where is_trash = 0 and wastage_sale_date < '{$dateRange[0]}' group by wastage_sale_customer
            ) as wastage_sale on customer_id = wastage_sale_customer
			left join ( select 
					received_payments_from,
					sum(received_payments_amount) + sum(received_payments_bonus) as total_payment_before_filtered_date
				from {$table_prefeix}received_payments where is_trash = 0 and date(received_payments_datetime) < '{$dateRange[0]}' group by received_payments_from
			) as payments on received_payments_from = customer_id
            left join ( select 
                    payments_return_customer_id,
                    sum(payments_return_amount) as total_payment_return_before_filtered_date
                from {$table_prefeix}payments_return where is_trash = 0 and payments_return_type = 'Outgoing' and date(payments_return_date) < '{$dateRange[0]}' group by payments_return_customer_id
            ) as payment_return on payments_return_customer_id = customer_id
			where customer_id = '{$customer_id}'
		");
		
		$getData = easySelectD("
			SELECT customer_id, ledger_date, description, debit, credit, @balance := ( @balance + credit ) - debit as balance from
            (
                select 
                    1 as sortby,
                    '{$customer_id}' as customer_id,
                    '' as ledger_date,
                    'Opening/Previous Balance' as description,
                    0 as debit,
                    0 as credit
                UNION ALL
                SELECT
                    2 as sortby,
                    sales_customer_id as customer_id,
                    concat(sales_delivery_date, ' ', TIME(sales_add_on)) as ledger_date,
					combine_description( concat( if(is_exchange = 1, 'Product Exchange/ Return' , 'Product Purchase'), ' (', sales_reference, ')' ), sales_note) as description,
                    if(sales_grand_total > 0, sales_grand_total, 0) as debit,
                    if(sales_grand_total < 0, abs(sales_grand_total), 0) as credit
                from {$table_prefeix}sales
				where is_trash = 0 and is_return = 0 and sales_status = 'Delivered' and sales_delivery_date between '{$dateRange[0]}' and '{$dateRange[1]}' group by sales_id
				UNION ALL
				SELECT
					3 as sortby,
					wastage_sale_customer as customer_id,
                    concat(wastage_sale_date, ' ', TIME(wastage_sale_add_on)) as ledger_date,
					combine_description('Wastage Purchase', wastage_sale_note) as description,
					wastage_sale_grand_total as debit,
					0 as credit
				from {$table_prefeix}wastage_sale
				where is_trash = 0 and wastage_sale_date between '{$dateRange[0]}' and '{$dateRange[1]}' group by wastage_sale_id
                UNION ALL
                SELECT
                    4 as sortby,
                    sales_customer_id as customer_id,
                    concat(sales_delivery_date, ' ', TIME(sales_add_on)) as ledger_date,
					combine_description( concat('Product Return', ' (', sales_reference, ')' ), sales_note) as description,
                    0 as debit,
                    sales_grand_total as credit
                from {$table_prefeix}sales
				where is_trash = 0 and is_return = 1 and sales_status = 'Delivered' and sales_delivery_date between '{$dateRange[0]}' and '{$dateRange[1]}' group by sales_id
                UNION ALL
				SELECT
					5 as sortby,
					payments_return_customer_id as customer_id,
					payments_return_date as ledger_date,
					combine_description('Payment return ', payments_return_description) as description,
					payments_return_amount as debit,
					0 as credit
				from {$table_prefeix}payments_return where is_trash = 0 and payments_return_type = 'Outgoing' and date(payments_return_date) between '{$dateRange[0]}' and '{$dateRange[1]}' group by payments_return_id
				UNION ALL
				SELECT
					6 as sortby,
					received_payments_from as customer_id,
					received_payments_datetime as ledger_date,
					combine_description( concat(received_payments_type, if(accounts_name is null, '', concat(' in ', accounts_name) ) ), received_payments_details) as description,
					0 as debit,
					received_payments_amount as credit
				from {$table_prefeix}received_payments as received_payments
                left join {$table_prefeix}accounts on received_payments_accounts = accounts_id
                where received_payments.is_trash = 0 and date(received_payments_datetime) between '{$dateRange[0]}' and '{$dateRange[1]}' group by received_payments_id
				UNION ALL
				SELECT
					7 as sortby,
					received_payments_from as customer_id,
					received_payments_datetime as ledger_date,
					concat('Provide Bonus on ', received_payments_type) as description,
					0 as debit,
					received_payments_bonus as credit
				from {$table_prefeix}received_payments where is_trash = 0 and received_payments_bonus > 0 and date(received_payments_datetime) between '{$dateRange[0]}' and '{$dateRange[1]}' group by received_payments_id
				UNION ALL
				SELECT
					8 as sortby,
					incomes_from as customer_id,
                    concat(incomes_date, ' ', TIME(incomes_add_on)) as ledger_date,
					combine_description('Received Payments ', incomes_description) as description,
					0 as debit,
					incomes_amount as credit
				from {$table_prefeix}incomes where is_trash = 0 and incomes_date between '{$dateRange[0]}' and '{$dateRange[1]}' group by incomes_id
			) as get_data
			where customer_id = '{$customer_id}'
            order by ledger_date, sortby
		");
			

        $totalFilteredRecords = $totalRecords = $getData["count"];

        // Check if there have more then zero data
        if(isset($getData['data'])) {
            
            foreach($getData['data'] as $key => $value) {
                $allNestedData = [];
				$allNestedData[] = "";
                $allNestedData[] = empty($value["ledger_date"]) ? "" : date("Y-m-d", strtotime($value["ledger_date"]) );
                $allNestedData[] = $value["description"];
				$allNestedData[] = number_format($value["debit"], get_options("decimalPlaces"), ".", "");
                $allNestedData[] = number_format($value["credit"], get_options("decimalPlaces"), ".", "");
                $allNestedData[] = number_format($value["balance"], get_options("decimalPlaces")) ;
                
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


/*************************** Company Ledger ***********************/
if(isset($_GET['page']) and $_GET['page'] == "companyLedger") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // Count Total recrods
    $totalFilteredRecords = $totalRecords = 0;
    $allData = [];

    if(isset($_GET["company_id"])) {

        $dateRange = explode(" - ", safe_input($requestData["dateRange"]));
        $company_id = safe_input($_GET["company_id"]);
        	
		$previous_balance = easySelectD("
			SELECT 
				@balance := (
						if(company_opening_balance is null, 0, company_opening_balance) +						
						if(total_billed_before_filtered_date is null, 0, total_billed_before_filtered_date) +
						if(total_direct_payment_before_filtered_date is null, 0, total_direct_payment_before_filtered_date)
				) - ( 
						if(total_payment_before_filtered_date is null, 0, total_payment_before_filtered_date) +
						if(total_pa_before_filtered_date is null, 0, total_pa_before_filtered_date) +
                        if(total_payment_return_before_filtered_date is null, 0, total_payment_return_before_filtered_date)
				)
			FROM {$table_prefeix}companies as company
			left join (select
					bills_company_id,
					sum(bills_amount) as total_billed_before_filtered_date
				from {$table_prefeix}bills where is_trash = 0 and bills_date < '{$dateRange[0]}' group by bills_company_id
			) as bills on bills_company_id = company_id
			left join (select
					payment_to_company,
					sum(payment_amount) as total_payment_before_filtered_date
				from {$table_prefeix}payments where is_trash = 0 and payment_date < '{$dateRange[0]}' group by payment_to_company
			) as payments on payments.payment_to_company = company_id
			left join (select
					payment_to_company,
					sum(payment_amount) as total_direct_payment_before_filtered_date
				from {$table_prefeix}payments where is_trash = 0 and payment_type is null and payment_date < '{$dateRange[0]}' group by payment_to_company
			) as direct_payments on direct_payments.payment_to_company = company_id
			left join (select
					pa_company,
					sum(pa_amount) as total_pa_before_filtered_date
				from {$table_prefeix}payment_adjustment where is_trash = 0 and pa_date < '{$dateRange[0]}' group by pa_company
			) as pa on pa_company = company_id
            left join (select
                    payments_return_company_id,
                    sum(payments_return_amount) as total_payment_return_before_filtered_date
                from {$table_prefeix}payments_return where is_trash = 0 and payments_return_type = 'Incoming' and date(payments_return_date) < '{$dateRange[0]}' group by payments_return_company_id
            ) as payment_return on payments_return_company_id = company_id
			where company_id = '{$company_id}'
		");

		//var_dump($previous_balance);
		
		$getData = easySelectD("
			SELECT company_id, ledger_date, description, debit, credit, @balance := ( @balance + credit ) - debit as balance from
            (
                select 
                    1 as sortby,
                    '{$company_id}' as company_id,
                    '' as ledger_date,
                    'Opening/Previous Balance' as description,
                    0 as debit,
                    0 as credit
				UNION ALL
				SELECT
					2 as sortby,
					bills_company_id as company_id,
					concat(bills_date, ' ', TIME(bills_add_on) ) as ledger_date,
					combine_description('Bill', all_description) as description,
					0 as debit,
					bills_amount as credit
				FROM {$table_prefeix}bills
                left join ( select
                            bill_items_bill_id, 
                            group_concat(bill_items_note SEPARATOR ', ') as all_description 
                        from {$table_prefeix}bill_items 
                        group by bill_items_bill_id 
                    ) as bill_items on bill_items_bill_id = bills_id
                where is_trash = 0 and bills_date between '{$dateRange[0]}' and '{$dateRange[1]}' group by bills_id
                UNION ALL
				SELECT
					3 as sortby,
					purchase_company_id as company_id,
					concat(purchase_date, ' ', TIME(purchase_add_on)) as ledger_date,
					combine_description('Product purchase bill', purchase_note) as description,
					0 as debit,
					purchase_grand_total as credit
				from {$table_prefeix}purchases where is_trash = 0 and is_return = 0 and purchase_date between '{$dateRange[0]}' and '{$dateRange[1]}' group by purchase_id
				UNION ALL
				SELECT
					4 as sortby,
					purchase_company_id as company_id,
					concat(purchase_date, ' ', TIME(purchase_add_on)) as ledger_date,
					combine_description('Product purchase return', purchase_note) as description,
					purchase_grand_total as debit,
					0 as credit
				from {$table_prefeix}purchases where is_trash = 0 and is_return = 1 and purchase_date between '{$dateRange[0]}' and '{$dateRange[1]}' group by purchase_id
				UNION ALL
				SELECT
					5 as sortby,
					payment_to_company as company_id,
					concat(payment_date, ' ', TIME(payment_update_on)) as ledger_date,
					combine_description('Bills', payment_item_description) as description,
					0 as debit,
					payment_amount as credit
				from {$table_prefeix}payments 
				left join (select
						payment_items_payments_id,
						group_concat(payment_items_description) as payment_item_description
					from {$table_prefeix}payment_items group by payment_items_payments_id
				) as payment_items_credit on payment_items_credit.payment_items_payments_id = payment_id
				where is_trash = 0 and (payment_type is null or payment_type = 'Advance Adjustment') and payment_date between '{$dateRange[0]}' and '{$dateRange[1]}' group by payment_id
				UNION ALL
				SELECT
					6 as sortby,
					payment_to_company as company_id,
					concat(payment_date, ' ', TIME(payment_update_on)) as ledger_date,
					combine_description( concat('Payments', ' (', payment_reference, ')'), payment_item_description) as description,
					payment_amount as debit,
					0 as credit
				from {$table_prefeix}payments 
				left join (select
						payment_items_payments_id,
						group_concat(payment_items_description) as payment_item_description
					from {$table_prefeix}payment_items group by payment_items_payments_id
				) as payment_items_debit on payment_items_debit.payment_items_payments_id = payment_id
				where is_trash = 0 and payment_date between '{$dateRange[0]}' and '{$dateRange[1]}' group by payment_id
				UNION ALL
				SELECT
					7 as sortby,
					pa_company as company_id,
					concat(pa_date, ' ', TIME(pa_add_on)) as ledger_date,
					combine_description('Payment Adjustment', pa_description) as description,
					pa_amount as debit,
					0 as credit
				from {$table_prefeix}payment_adjustment where is_trash = 0 and pa_date between '{$dateRange[0]}' and '{$dateRange[1]}' group by pa_id
                UNION ALL
                SELECT
                    8 as sortby,
                    payments_return_company_id as company_id,
                    payments_return_date as ledger_date,
                    combine_description('Purchase Payment Return', payments_return_description) as description,
                    0 as debit,
                    payments_return_amount as credit
                from {$table_prefeix}payments_return where is_trash = 0 and payments_return_type = 'Incoming' and date(payments_return_date) between '{$dateRange[0]}' and '{$dateRange[1]}' group by company_id
            ) as get_data
			where company_id = '{$company_id}'
            order by ledger_date, sortby
		");
			
		//var_dump($getData);

        $totalFilteredRecords = $totalRecords = $getData["count"];

        // Check if there have more then zero data
        if(isset($getData['data'])) {

            //print_r($getData['data']);
            
            foreach($getData['data'] as $key => $value) {
                $allNestedData = [];
				$allNestedData[] = "";
                $allNestedData[] = empty($value["ledger_date"]) ? "" : date("Y-m-d", strtotime($value["ledger_date"]) );
                $allNestedData[] = $value["description"];
				$allNestedData[] = number_format($value["debit"], get_options("decimalPlaces"), ".", "");
                $allNestedData[] = number_format($value["credit"], get_options("decimalPlaces"), ".", "");
                $allNestedData[] = number_format($value["balance"], get_options("decimalPlaces")) ;
                
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


/*************************** Employee Ledger ***********************/
if(isset($_GET['page']) and $_GET['page'] == "advancePaymentLedger") {
    
    $requestData = $_REQUEST;
    $getData = [];

    // Count Total recrods
    $totalFilteredRecords = $totalRecords = 0;
    $allData = [];

    if(isset($_GET["emp_id"])) {

        $dateRange = explode(" - ", safe_input($requestData["dateRange"]));
        $emp_id = safe_input($_GET["emp_id"]);
        	
		$previous_balance = easySelectD("
			SELECT 
				@balance := (
						if(total_advance_payment_before_filtered_date is null, 0, total_advance_payment_before_filtered_date)
						
				) - (
						if(total_payment_before_filtered_date is null, 0, total_payment_before_filtered_date) +
						if(total_return_before_filtered_date is null, 0, total_return_before_filtered_date)
				)
			FROM {$table_prefeix}employees as employees
			left join (select
					advance_payment_pay_to,
					sum(advance_payment_amount) as total_advance_payment_before_filtered_date
				from {$table_prefeix}advance_payments where is_trash = 0 and advance_payment_date < '{$dateRange[0]}' group by advance_payment_pay_to
			) as advance_payment on advance_payment_pay_to = emp_id
			left join (select
					payment_to_employee,
					sum(payment_amount) as total_payment_before_filtered_date
				from {$table_prefeix}payments where is_trash = 0 and payment_type = 'Advance Adjustment' and payment_date < '{$dateRange[0]}' group by payment_to_employee
			) as payment_adjustment on payment_to_employee = emp_id
			left join (select
					payments_return_emp_id,
					sum(payments_return_amount) as total_return_before_filtered_date
				from {$table_prefeix}payments_return where is_trash = 0 and date(payments_return_date) < '{$dateRange[0]}' group by payments_return_emp_id
			) as payment_return on payments_return_emp_id = emp_id
			where emp_id = '{$emp_id}'
		");

		
		$getData = easySelectD("
			SELECT empl_id, ledger_date, description, debit, credit, @balance := ( @balance + credit ) - debit as balance from
            (
                select 
                    1 as sortby,
                    '{$emp_id}' as empl_id,
                    '' as ledger_date,
                    'Opening/Previous Balance' as description,
                    0 as debit,
                    0 as credit
                UNION ALL
                SELECT
                    2 as sortby,
                    advance_payment_pay_to as empl_id,
                    advance_payment_date as ledger_date,
                    combine_description( 'Advance Payment', advance_payment_description) as description,
                    0 as debit,
                    advance_payment_amount as credit
                from {$table_prefeix}advance_payments
                where is_trash = 0 and advance_payment_date between '{$dateRange[0]}' and '{$dateRange[1]}' group by advance_payment_id
				UNION ALL
				SELECT
					3 as sortby,
					payment_to_employee as empl_id,
					payment_date as ledger_date,
					concat('Adjusted on ', item_description),
					payment_amount as debit,
					0 as credit
				from {$table_prefeix}payments as payment
				left join ( select
						payment_items_category_id,
						payment_items_payments_id,
						payment_items_description,
						group_concat(combine_description(payment_category_name, payment_items_description) SEPARATOR ', ') as item_description
					from {$table_prefeix}payment_items
					left join {$table_prefeix}payments_categories on payment_items_category_id = payment_category_id
					group by payment_items_payments_id
				) as payment_item on payment_items_payments_id = payment_id
				where payment.is_trash = 0 and payment.payment_type = 'Advance Adjustment' and payment.payment_date between '{$dateRange[0]}' and '{$dateRange[1]}' group by payment_id
				UNION ALL
                SELECT
                    3 as sortby,
                    payments_return_emp_id as empl_id,
                    payments_return_date as ledger_date,
                    combine_description( 'Payment Return', payments_return_description) as description,
                    payments_return_amount as debit,
                    0 as credit
                from {$table_prefeix}payments_return
                where is_trash = 0 and date(payments_return_date) between '{$dateRange[0]}' and '{$dateRange[1]}' group by payments_return_id
			) as get_data
			where empl_id = '{$emp_id}'
            order by ledger_date, sortby
		");
			

        $totalFilteredRecords = $totalRecords = $getData["count"];

        // Check if there have more then zero data
        if(isset($getData['data'])) {
            
            foreach($getData['data'] as $key => $value) {
                $allNestedData = [];
				$allNestedData[] = "";
                $allNestedData[] = $value["ledger_date"];
                $allNestedData[] = $value["description"];
				$allNestedData[] = number_format($value["debit"], get_options("decimalPlaces"), ".", "");
                $allNestedData[] = number_format($value["credit"], get_options("decimalPlaces"), ".", "");
                $allNestedData[] = number_format($value["balance"], get_options("decimalPlaces")) ;
                
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

