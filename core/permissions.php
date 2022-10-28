<?php

// View, Add, Edit, Delete

$default_role = array("View", "Add", "Edit", "Delete"); 

$defaultPermission["dashboard"] = array("View");
$defaultPermission["accounts_overview"] = array("View");
$defaultPermission["accounts"] = "";
$defaultPermission["incomes"] = "";
$defaultPermission["capital"] = "";
$defaultPermission["transfer_money"] = "";
$defaultPermission["closings"] = "";
$defaultPermission["accounts_ledger"] = array("View");
$defaultPermission["employee_ledger"] = array("View");
$defaultPermission["journal_ledger"] = array("View");
$defaultPermission["customer_ledger"] = array("View");
$defaultPermission["company_ledger"] = array("View");
$defaultPermission["advance_payment_ledger"] = array("View");
$defaultPermission["journal"] = "";
$defaultPermission["journal_records"] = "";
$defaultPermission["payments"] = "";
$defaultPermission["payments_return"] = "";
$defaultPermission["advance_bill_payments"] = "";
$defaultPermission["advance_bill_payments_return"] = "";
$defaultPermission["bills"] = "";
$defaultPermission["salary"] = "";
$defaultPermission["loan"] = "";
$defaultPermission["loan_installment"] = "";
$defaultPermission["payment_adjustment"] = "";
$defaultPermission["payment_categories"] = "";
$defaultPermission["products"] = "";
$defaultPermission["product_purchases"] = "";
$defaultPermission["stock_entry"] = "";
$defaultPermission["product_category"] = "";
$defaultPermission["product_authors"] = "";
$defaultPermission["product_generics"] = "";
$defaultPermission["product_brands"] = "";
$defaultPermission["product_attributes"] = "";
$defaultPermission["product_variations"] = "";
$defaultPermission["product_units"] = "";
$defaultPermission["warehouse"] = "";

$defaultPermission["income_advance_collection"] = "";
$defaultPermission["income_received_payments"] = "";
$defaultPermission["incomes_other"] = "";

$defaultPermission["myshop_overview"] = array("View");
$defaultPermission["myshop_transfer_balance"] = "";
$defaultPermission["myshop_expenses"] = "";
$defaultPermission["myshop_advance_collection"] = "";
$defaultPermission["myshop_received_payments"] = "";
$defaultPermission["myshop_pos_sales"] = "";
$defaultPermission["myshop_discount"] = "";

$defaultPermission["stock_management_order_list"] = "";
$defaultPermission["stock_transfer"] = "";

$defaultPermission["product_return"] = "";
$defaultPermission["sale_pos_sale"] = array("View");
$defaultPermission["sale_return"] = array("View");
$defaultPermission["wastage_sales"] = "";
$defaultPermission["peoples_user"] = "";
$defaultPermission["peoples_employee"] = "";
$defaultPermission["peoples_biller"] = "";
$defaultPermission["peoples_customer"] = "";
$defaultPermission["peoples_company"] = "";
$defaultPermission["settings_system"] = array("View", "Edit");
$defaultPermission["settings_security"] = array("View", "Edit");
$defaultPermission["settings_pos"] = array("View", "Edit");
$defaultPermission["settings_tariff_charges"] = "";
$defaultPermission["settings_group_permissions"] = "";
$defaultPermission["settings_department"] = "";
$defaultPermission["settings_shops"] = "";
$defaultPermission["settings_backup"] = array("view");
$defaultPermission["reports_sales"] = array("View");
$defaultPermission["reports_product"] = array("View");
$defaultPermission["product_comparison"] = array("View");
$defaultPermission["reports_customer"] = array("View");
$defaultPermission["reports_expense"] = array("View");
$defaultPermission["reports_expired_products"] = array("View");


?>