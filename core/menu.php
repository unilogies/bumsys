<?php

$default_menu = array();


// Dashboard
$default_menu["Dashboard"] = array(
    "t_link"    => full_website_address() . "/home/",
    "title"     => "Dasheboard",
    "t_icon"    => "fa fa-dashboard",
    "__?"       => current_user_can("dashboard.View")
);

// Accounts default_menu
$default_menu["Accounts"] = array(
    "t_link"    => "#",
    "title"     => "",
    "t_icon"    => "fa fa-book"
);
// Accounts Sub default_menu
$default_menu["Accounts"]["Overview"] = array(
    "t_link"    => full_website_address() . "/accounts/overview/",
    "title"     => "Accounts Overview",
    "t_icon"    => "fa fa-dashboard",
    "__?"       => current_user_can("accounts_overview.View")
);
$default_menu["Accounts"]["Account List"] = array(
    "t_link"    => full_website_address() . "/accounts/account-list/",
    "title"     => "Accounts List",
    "t_icon"    => "fa fa-book",
    "__?"       => current_user_can("accounts.View || accounts.Add || accounts.Edit")
);
$default_menu["Accounts"]["New Account"] = array(
    "t_link"    => full_website_address() . "/xhr/?module=accounts&page=newAccount",
    "title"     => "",
    "t_icon"    => "fa fa-plus",
    "t_modal"   => "#modalDefault",
    "__?"       => current_user_can("accounts.Add")
);
$default_menu["Accounts"]["Capital"] = array(
    "t_link"    => full_website_address() . "/accounts/capital/",
    "title"     => "Accounts Capital",
    "t_icon"    => "fa fa-money",
    "__?"       => current_user_can("capital.View || capital.Add || capital.Edit || capital.Delete")
);
$default_menu["Accounts"]["Transfer Money"] = array(
    "t_link"    => full_website_address() . "/accounts/transfer-money/",
    "title"     => "Transfer Money",
    "t_icon"    => "fa fa-truck",
    "__?"       => current_user_can("transfer_money.View || transfer_money.Add || transfer_money.Edit || transfer_money.Delete")
);
$default_menu["Accounts"]["Receivable Report"] = array(
    "t_link"    => full_website_address() . "/accounts/total-receivable/",
    "title"     => "Receivable Amount",
    "t_icon"    => "fa fa-money",
    "__?"       => current_user_can("transfer_money.View || transfer_money.Add || transfer_money.Edit || transfer_money.Delete")
);
$default_menu["Accounts"]["Payble Report"] = array(
    "t_link"    => full_website_address() . "/accounts/total-payable/",
    "title"     => "Payable Amount",
    "t_icon"    => "fa fa-money",
    "__?"       => current_user_can("transfer_money.View || transfer_money.Add || transfer_money.Edit || transfer_money.Delete")
);
$default_menu["Accounts"]["Closings"] = array(
    "t_link"    => full_website_address() . "/accounts/closings/",
    "title"     => "Closings",
    "t_icon"    => "fa fa-calendar",
    "__?"       => current_user_can("closings.View || closings.Add || closings.Edit || closings.Delete")
);

// Ledger default_menu
$default_menu["Ledgers"] = array(
    "t_link"    => "#",
    "title"     => "",
    "t_icon"    => "fa fa-book"
);
$default_menu["Ledgers"]["Accounts Ledger"] = array(
    "t_link"    => full_website_address() . "/ledgers/accounts-ledger/",
    "title"     => "Accounts Ledger",
    "t_icon"    => "fa fa-book",
    "__?"       => current_user_can("accounts_ledger.View")
);
$default_menu["Ledgers"]["Employee Ledger"] = array(
    "t_link"    => full_website_address() . "/ledgers/employee-ledger/",
    "title"     => "Employee Ledger",
    "t_icon"    => "fa fa-book",
    "__?"       => current_user_can("employee_ledger.View")
);
$default_menu["Ledgers"]["Journal Ledger"] = array(
    "t_link"    => full_website_address() . "/ledgers/journal-ledger/",
    "title"     => "Journal Ledger",
    "t_icon"    => "fa fa-book",
    "__?"       => current_user_can("journal_ledger.View")
);
$default_menu["Ledgers"]["Customer Ledger"] = array(
    "t_link"    => full_website_address() . "/ledgers/customer-ledger/",
    "title"     => "Customer Ledger",
    "t_icon"    => "fa fa-book",
    "__?"       => current_user_can("customer_ledger.View")
);
$default_menu["Ledgers"]["Company Ledger"] = array(
    "t_link"    => full_website_address() . "/ledgers/company-ledger/",
    "title"     => "Company Ledger",
    "t_icon"    => "fa fa-book",
    "__?"       => current_user_can("company_ledger.View")
);
$default_menu["Ledgers"]["Advance Payment Ledger"] = array(
    "t_link"    => full_website_address() . "/ledgers/advance-payment-ledger/",
    "title"     => "Advance Payment Ledger",
    "t_icon"    => "fa fa-book",
    "__?"       => current_user_can("advance_payment_ledger.View")
);

// Journal default_menu
$default_menu["journals"] = array(
    "t_link"    => "#",
    "title"     => "",
    "t_icon"    => "fa fa-book"
);
$default_menu["journals"]["Journal List"] = array(
    "t_link"    => full_website_address() . "/journals/journal-list/",
    "title"     => "Journal List",
    "t_icon"    => "fa fa-book",
    "__?"       => current_user_can("journal.View || journal.Add || journal.Edit || journal.Delete")
);
$default_menu["journals"]["Create Journal"] = array(
    "t_link"    => full_website_address() . "/xhr/?tooltip=true&select2=true&module=journals&page=newJournal",
    "title"     => "",
    "t_icon"    => "fa fa-plus",
    "t_modal"   => "#modalDefault",
    "__?"       => current_user_can("journal.Add")
);
$default_menu["journals"]["Journal Records"] = array(
    "t_link"    => full_website_address() . "/journals/journal-records/",
    "title"     => "Journal Records",
    "t_icon"    => "fa fa-book",
    "__?"       => current_user_can("journal_records.View || journal_records.Add || journal_records.Edit || journal_records.Delete")
);

// Income Default Menu
$default_menu["Incomes"] = array(
    "t_link"    => "#",
    "title"     => "",
    "t_icon"    => "fa fa-usd"
);
$default_menu["Incomes"]["Advance Collection"] = array(
    "t_link"    => full_website_address() . "/incomes/advance-collection/",
    "title"     => "Advance Collection",
    "t_icon"    => "fa fa-money",
    "__?"       => current_user_can("income_advance_collection.View || income_advance_collection.Add || income_advance_collection.Edit || income_advance_collection.Delete")
);
$default_menu["Incomes"]["Received Payments"] = array(
    "t_link"    => full_website_address() . "/incomes/received-payments/",
    "title"     => "Received Payments",
    "t_icon"    => "fa fa-money",
    "__?"       => current_user_can("income_received_payments.View || income_received_payments.Add || income_received_payments.Edit || income_received_payments.Delete")
);
$default_menu["Incomes"]["Other Incomes"] = array(
    "t_link"    => full_website_address() . "/incomes/other-incomes/",
    "title"     => "Other Incomes",
    "t_icon"    => "fa fa-money",
    "__?"       => current_user_can("incomes_other.View || incomes_other.Add || incomes_other.Edit || incomes_other.Delete")
);

// Expense default_menu
$default_menu["Expenses"] = array(
    "t_link"    => "#",
    "title"     => "",
    "t_icon"    => "fa fa-money"
);
$default_menu["Expenses"]["Payments"] = array(
    "t_link"    => full_website_address() . "/expenses/payments/",
    "title"     => "Expense Payments",
    "t_icon"    => "fa fa-money",
    "__?"       => current_user_can("payments.View || payments.Add || payments.Edit || payments.Delete")
);
$default_menu["Expenses"]["Payments Return"] = array(
    "t_link"    => full_website_address() . "/expenses/payments-return/",
    "title"     => "Payments Return",
    "t_icon"    => "fa fa-money",
    "__?"       => current_user_can("payments_return.View || payments_return.Add || payments_return.Edit || payments_return.Delete")
);
$default_menu["Expenses"]["Bills"] = array(
    "t_link"    => full_website_address() . "/expenses/bills/",
    "title"     => "Bills",
    "t_icon"    => "fa fa-book",
    "__?"       => current_user_can("bills.View || bills.Add || bills.Edit || bills.Delete")
);
$default_menu["Expenses"]["Advance Bill Payments"] = array(
    "t_link"    => "#",
    "title"     => "",
    "t_icon"    => "fa fa-book"
);
$default_menu["Expenses"]["Advance Bill Payments"]["Overview"] = array(
    "t_link"    => full_website_address() . "/expenses/advance-bill-payments/advance-payments-overview/",
    "title"     => "Advance Bill Payments Overview",
    "t_icon"    => "fa fa-dashboard",
    "__?"       => current_user_can("advance_bill_payments.View || advance_bill_payments.Add")
);
$default_menu["Expenses"]["Advance Bill Payments"]["Payment List"] = array(
    "t_link"    => full_website_address() . "/expenses/advance-bill-payments/advance-payments-list/",
    "title"     => "Advance Bill Payment List",
    "t_icon"    => "fa fa-money",
    "__?"       => current_user_can("advance_bill_payments.View || advance_bill_payments.Add")
);
$default_menu["Expenses"]["Advance Bill Payments"]["Return List"] = array(
    "t_link"    => full_website_address() . "/expenses/advance-bill-payments/advance-payments-return-list/",
    "title"     => "Advance Bill Payment Return List",
    "t_icon"    => "fa fa-undo",
    "__?"       => current_user_can("advance_bill_payments_return.View || advance_bill_payments_return.Add || advance_bill_payments_return.Edit || advance_bill_payments_return.Delete")
);
$default_menu["Expenses"]["Payment Adjustment"] = array(
    "t_link"    => full_website_address() . "/expenses/payment-adjustment/",
    "title"     => "Payment Adjustment",
    "t_icon"    => "fa fa-exchange",
    "__?"       => current_user_can("payment_adjustment.View || payment_adjustment.Add || payment_adjustment.Edit || payment_adjustment.Delete")
);
$default_menu["Expenses"]["Salaries"] = array(
    "t_link"    => full_website_address() . "/expenses/salaries/",
    "title"     => "Salaries",
    "t_icon"    => "fa fa-cubes",
    "__?"       => current_user_can("Salary.View || Salary.Add || Salary.Edit || Salary.Delete")
);
$default_menu["Expenses"]["Payment Categories"] = array(
    "t_link"    => full_website_address() . "/expenses/payment-categories/",
    "title"     => "Payment Categories",
    "t_icon"    => "fa fa-cubes",
    "__?"       => current_user_can("payment_categories.View || payment_categories.Add || payment_categories.Edit || payment_categories.Delete")
);

// Loan Management default_menu
$default_menu["Loan Management"] = array(
    "t_link"    => "#",
    "title"     => "",
    "t_icon"    => "fa fa-credit-card"
);
$default_menu["Loan Management"]["Pay Loan"] = array(
    "t_link"    => full_website_address() . "/xhr/?select2=true&tooltip=true&module=loan-management&page=payLoan",
    "title"     => "",
    "t_icon"    => "fa fa-money",
    "t_modal"   => "#modalDefault",
    "__?"       => current_user_can("loan.Add")
);
$default_menu["Loan Management"]["Loan List"] = array(
    "t_link"    => full_website_address() . "/loan-management/loan-list/",
    "title"     => "Loan List",
    "t_icon"    => "fa fa-credit-card",
    "__?"       => current_user_can("loan.View || loan.Add || loan.Edit || loan.Delete")
);
$default_menu["Loan Management"]["Add Installment"] = array(
    "t_link"    => full_website_address() . "/xhr/?icheck=true&module=loan-management&page=addInstallment",
    "title"     => "",
    "t_icon"    => "fa fa-plus",
    "t_modal"   => "#modalDefault",
    "__?"       => current_user_can("loan_installment.Add")
);
$default_menu["Loan Management"]["Loans Installment"] = array(
    "t_link"    => full_website_address() . "/loan-management/loans-installment/",
    "title"     => "Loan Installment List",
    "t_icon"    => "fa fa-money",
    "__?"       => current_user_can("loan_installment.View || loan_installment.Add || loan_installment.Edit || loan_installment.Delete")
);

// Sales default_menu
$default_menu["Sales"] = array(
    "t_link"    => "#",
    "title"     => "",
    "t_icon"    => "fa fa-shopping-cart"
);
$default_menu["Sales"]["POS Sales"] = array(
    "t_link"    => full_website_address() . "/sales/pos-sale/",
    "title"     => "POS Sale List",
    "t_icon"    => "fa fa-cubes",
    "__?"       => current_user_can("sale_pos_sale.View")
);
$default_menu["Sales"]["Returns"] = array(
    "t_link"    => full_website_address() . "/sales/sales-return/",
    "title"     => "Sale Return",
    "t_icon"    => "fa fa-cubes",
    "__?"       => current_user_can("sale_return.View")
);
$default_menu["Sales"]["Wastage Sales"] = array(
    "t_link"    => full_website_address() . "/sales/wastage-sale-list/",
    "title"     => "Wastage Sale List",
    "t_icon"    => "fa fa-recycle",
    "__?"       => current_user_can("wastage_sales.View || wastage_sales.Add || wastage_sales.Edit || wastage_sales.Delete")
);
$default_menu["Sales"]["New Wastage Sale"] = array(
    "t_link"    => full_website_address() . "/sales/new-wastage-sale/",
    "title"     => "New Wastage Sale",
    "t_icon"    => "fa fa-plus",
    "__?"       => current_user_can("wastage_sales.Add")
);

// Product default_menu
$default_menu["Products"] = array(
    "t_link"    => "#",
    "title"     => "",
    "t_icon"    => "fa fa-cubes"
);
$default_menu["Products"]["Product List"] = array(
    "t_link"    => full_website_address() . "/products/product-list/",
    "title"     => "Product List",
    "t_icon"    => "fa fa-cubes",
    "__?"       => current_user_can("products.View || products.Add || products.Edit || products.Delete")
);
$default_menu["Products"]["Add Product"] = array(
    "t_link"    => full_website_address() . "/products/new-product/",
    "title"     => "Add New Product",
    "t_icon"    => "fa fa-plus-circle",
    "dload"     => true,
    "__?"       => current_user_can("products.Add")
);
$default_menu["Products"]["Categories"] = array(
    "t_link"    => full_website_address() . "/products/product-categories/",
    "title"     => "Product Categories",
    "t_icon"    => "fa fa-folder-open",
    "__?"       => current_user_can("product_category.View || product_category.Add || product_category.Edit || product_category.Delete")
);
$default_menu["Products"]["Attributes"] = array(
    "t_link"    => full_website_address() . "/products/product-attributes/",
    "title"     => "Product Attributes",
    "t_icon"    => "fa fa-cubes",
    "__?"       => get_options("enableProductVariations") and current_user_can("product_attributes.View || product_attributes.Add || product_attributes.Edit || product_attributes.Delete")
);
$default_menu["Products"]["Variations"] = array(
    "t_link"    => full_website_address() . "/products/product-variations/",
    "title"     => "Product Variations",
    "t_icon"    => "fa fa-cubes",
    "__?"       => get_options("enableProductVariations") and current_user_can("product_variations.View || product_variations.Add || product_variations.Edit || product_variations.Delete")
);
$default_menu["Products"]["Units"] = array(
    "t_link"    => full_website_address() . "/products/product-units/",
    "title"     => "Product Unit",
    "t_icon"    => "fa fa-balance-scale",
    "__?"       => current_user_can("product_units.View || product_units.Add || product_units.Edit || product_units.Delete")
);
$default_menu["Products"]["Editions"] = array(
    "t_link"    => full_website_address() . "/products/product-editions/",
    "title"     => "Product Editions",
    "t_icon"    => "fa fa-filter",
    "__?"       => current_user_can("product_units.View || product_units.Add || product_units.Edit || product_units.Delete")
);
$default_menu["Products"]["Brands"] = array(
    "t_link"    => full_website_address() . "/products/product-brands/",
    "title"     => "Product Brands",
    "t_icon"    => "fa fa-copyright",
    "__?"       => current_user_can("product_brands.View || product_brands.Add || product_brands.Edit || product_brands.Delete")
);
$default_menu["Products"]["Generics"] = array(
    "t_link"    => full_website_address() . "/products/product-generics/",
    "title"     => "Product Generic",
    "t_icon"    => "fa fa-copyright",
    "__?"       => current_user_can("product_generics.View || product_generics.Add || product_generics.Edit || product_generics.Delete")
);
$default_menu["Products"]["Authors"] = array(
    "t_link"    => full_website_address() . "/products/authors/",
    "title"     => "Product Authors",
    "t_icon"    => "fa fa-user",
    "__?"       => current_user_can("product_authors.View || product_authors.Add || product_authors.Edit || product_authors.Delete")
);
$default_menu["Products"]["Settings"] = array(
    "t_link"    => full_website_address() . "/products/product-settings/",
    "title"     => "Product Settings",
    "t_icon"    => "fa fa-gear",
    "__?"       => current_user_can("product_settings.View || product_settings.Edit")
);

// Stock Management default_menu
$default_menu["Stock Management"] = array(
    "t_link"    => "#",
    "title"     => "",
    "t_icon"    => "fa fa-cubes"
);
$default_menu["Stock Management"]["Stock Entry List"] = array(
    "t_link"    => full_website_address() . "/stock-management/stock-entry-list/",
    "title"     => "Stock Entry List",
    "t_icon"    => "fa fa-shopping-bag",
    "__?"       => current_user_can("stock_entry.View || stock_entry.Add || stock_entry.Edit || stock_entry.Delete")
);
$default_menu["Stock Management"]["New Stock Entry"] = array(
    "t_link"    => full_website_address() . "/stock-management/new-stock-entry/",
    "title"     => "New Stock Entry",
    "t_icon"    => "fa fa-plus",
    "dload"     => true,
    "__?"       => is_biller() and current_user_can("stock_entry.Add")
);
$default_menu["Stock Management"]["Purchase List"] = array(
    "t_link"    => full_website_address() . "/stock-management/product-purchases-list/",
    "title"     => "Product Purchase List",
    "t_icon"    => "fa fa-shopping-bag",
    "__?"       => current_user_can("product_purchases.View || product_purchases.Add || product_purchases.Edit || product_purchases.Delete")
);
$default_menu["Stock Management"]["New Purchases"] = array(
    "t_link"    => full_website_address() . "/stock-management/new-purchase/",
    "title"     => "New Product Purchase",
    "t_icon"    => "fa fa-plus",
    "dload"     => true,
    "__?"       => is_biller() and current_user_can("product_purchases.Add")
);
$default_menu["Stock Management"]["Purchase Return List"] = array(
    "t_link"    => full_website_address() . "/stock-management/purchase-return-list/",
    "title"     => "Purchase Return List",
    "t_icon"    => "fa fa-undo",
    "__?"       => current_user_can("product_return.View || product_return.Add || product_return.Edit || product_return.Delete")
);
$default_menu["Stock Management"]["New Purchase Return"] = array(
    "t_link"    => full_website_address() . "/stock-management/new-purchase-return/",
    "title"     => "New Purchase Return",
    "t_icon"    => "fa fa-plus",
    "dload"     => true,
    "__?"       => is_biller() and current_user_can("product_return.Add")
);
$default_menu["Stock Management"]["Stock Transfer List"] = array(
    "t_link"    => full_website_address() . "/stock-management/stock-transfer-list/",
    "title"     => "Stock Transfer List",
    "t_icon"    => "fa fa-exchange",
    "__?"       => current_user_can("stock_transfer.View || stock_transfer.Add || stock_transfer.Edit || stock_transfer.Delete")
);
$default_menu["Stock Management"]["New Stock Transfer"] = array(
    "t_link"    => full_website_address() . "/stock-management/new-stock-transfer/",
    "title"     => "New Stock Transfer",
    "t_icon"    => "fa fa-plus",
    "dload"     => true,
    "__?"       => is_biller() and current_user_can("stock_transfer.Add")
);
$default_menu["Stock Management"]["Sales Return List"] = array(
    "t_link"    => full_website_address() . "/stock-management/sales-return-list/",
    "title"     => "Sale Return List",
    "t_icon"    => "fa fa-undo",
    "__?"       => current_user_can("product_return.View || product_return.Add || product_return.Edit || product_return.Delete")
);
$default_menu["Stock Management"]["New Sales Return"] = array(
    "t_link"    => full_website_address() . "/stock-management/new-sales-return/",
    "title"     => "New Sale Return",
    "t_icon"    => "fa fa-plus",
    "dload"     => true, // This
    "__?"       => is_biller() and current_user_can("product_return.Add")
);
$default_menu["Stock Management"]["Batches"] = array(
    "t_link"    => full_website_address() . "/stock-management/batch-list/",
    "title"     => "Batch List",
    "t_icon"    => "fa fa-hourglass-end",
    "__?"       => current_user_can("batches.View || batches.Add || batches.Edit || batches.Delete")
);
$default_menu["Stock Management"]["Warehouse"] = array(
    "t_link"    => full_website_address() . "/stock-management/warehouse-list/",
    "title"     => "Warehouse List",
    "t_icon"    => "fa fa-building-o",
    "__?"       => current_user_can("warehouse.View || warehouse.Add || warehouse.Edit || warehouse.Delete")
);


// My Shop default_menu
$default_menu["My Shop"] = array(
    "t_link"    => "#",
    "title"     => "",
    "t_icon"    => "fa fa-shopping-cart",
    "__?"       => is_biller()
);
$default_menu["My Shop"]["Overview"] = array(
    "t_link"    => full_website_address() . "/my-shop/shop-overview/",
    "title"     => "My Shop Overview",
    "t_icon"    => "fa fa-dashboard",
    "__?"       => is_biller() and current_user_can("myshop_overview.View")
);
$default_menu["My Shop"]["Transfer Balance"] = array(
    "t_link"    => full_website_address() . "/my-shop/transfer-balance/",
    "title"     => "Transfer Balance",
    "t_icon"    => "fa fa-cubes",
    "__?"       => is_biller() and current_user_can("myshop_transfer_balance.View || myshop_transfer_balance.Add || myshop_transfer_balance.Edit || myshop_transfer_balance.Delete")
);
$default_menu["My Shop"]["Expenses"] = array(
    "t_link"    => full_website_address() . "/my-shop/shop-expenses/",
    "title"     => "Expense List",
    "t_icon"    => "fa fa-money",
    "__?"       => is_biller() and current_user_can("myshop_expenses.View || myshop_expenses.Add || myshop_expenses.Edit || myshop_expenses.Delete")
);
$default_menu["My Shop"]["Advance Collection"] = array(
    "t_link"    => full_website_address() . "/my-shop/shop-advance-collection/",
    "title"     => "Advance Collection",
    "t_icon"    => "fa fa-cubes",
    "__?"       => is_biller() and current_user_can("myshop_advance_collection.View || myshop_advance_collection.Add || myshop_advance_collection.Edit || myshop_advance_collection.Delete")
);
$default_menu["My Shop"]["Received Payments"] = array(
    "t_link"    => full_website_address() . "/my-shop/received-payments/",
    "title"     => "Received Payments",
    "t_icon"    => "fa fa-cubes",
    "__?"       => is_biller() and current_user_can("myshop_received_payments.View || myshop_received_payments.Add || myshop_received_payments.Edit || myshop_received_payments.Delete")
);
$default_menu["My Shop"]["POS Sales"] = array(
    "t_link"    => full_website_address() . "/my-shop/pos-sale/",
    "title"     => "POS Sale List",
    "t_icon"    => "fa fa-cubes",
    "__?"       => is_biller() and current_user_can("myshop_pos_sales.View || myshop_pos_sales.Add || myshop_pos_sales.Edit || myshop_pos_sales.Delete")
);
$default_menu["My Shop"]["Discounts"] = array(
    "t_link"    => full_website_address() . "/my-shop/discounts/",
    "title"     => "Discount",
    "t_icon"    => "fa fa-cubes",
    "__?"       => is_biller() and current_user_can("myshop_discount.View || myshop_discount.Add || myshop_discount.Edit || myshop_discount.Delete")
);

// Peoples default_menu
$default_menu["Peoples"] = array(
    "t_link"    => "#",
    "title"     => "",
    "t_icon"    => "fa fa-users"
);
$default_menu["Peoples"]["New User"] = array(
    "t_link"    => full_website_address() . "/xhr/?module=peoples&page=newUser", // This will jquery popup
    "title"     => "New User",
    "t_icon"    => "fa fa-plus",
    "t_modal"   => "#modalDefault",
    "__?"       => current_user_can("peoples_user.Add")
);
$default_menu["Peoples"]["User List"] = array(
    "t_link"    => full_website_address() . "/peoples/user-list/",
    "title"     => "User List",
    "t_icon"    => "fa fa-users",
    "__?"       => current_user_can("peoples_user.View || peoples_user.Add || peoples_user.Edit || peoples_user.Delete")
);
$default_menu["Peoples"]["New Employee"] = array(
    "t_link"    => full_website_address() . "/peoples/new-employee/",
    "title"     => "New Employee",
    "t_icon"    => "fa fa-plus",
    "__?"       => current_user_can("peoples_employee.Add")
);
$default_menu["Peoples"]["Employee List"] = array(
    "t_link"    => full_website_address() . "/peoples/employee-list/",
    "title"     => "Employee List",
    "t_icon"    => "fa fa-users",
    "__?"       => current_user_can("peoples_employee.View || peoples_employee.Add || peoples_employee.Edit || peoples_employee.Delete")
);
$default_menu["Peoples"]["New Biller"] = array(
    "t_link"    => full_website_address() . "/xhr/?module=peoples&page=newBiller",
    "title"     => "",
    "t_icon"    => "fa fa-plus",
    "t_modal"   => "#modalDefault",
    "__?"       => current_user_can("peoples_biller.Add")
);
$default_menu["Peoples"]["Biller List"] = array(
    "t_link"    => full_website_address() . "/peoples/biller-list/",
    "title"     => "Biller List",
    "t_icon"    => "fa fa-users",
    "__?"       => current_user_can("peoples_biller.View || peoples_biller.Add || peoples_biller.Edit || peoples_biller.Delete")
);
$default_menu["Peoples"]["New Customer"] = array(
    "t_link"    => full_website_address() . "/xhr/?module=peoples&page=newCustomer",
    "title"     => "",
    "t_icon"    => "fa fa-plus",
    "t_modal"   => "#modalDefault",
    "__?"       => current_user_can("peoples_customer.Add")
);
$default_menu["Peoples"]["Customer List"] = array(
    "t_link"    => full_website_address() . "/peoples/customer-list/",
    "title"     => "Customer List",
    "t_icon"    => "fa fa-users",
    "__?"       => current_user_can("peoples_customer.View || peoples_customer.Add || peoples_customer.Edit || peoples_customer.Delete")
);
$default_menu["Peoples"]["New Company"] = array(
    "t_link"    => full_website_address() . "/xhr/?module=peoples&page=newCompany",
    "title"     => "",
    "t_icon"    => "fa fa-plus",
    "t_modal"   => "#modalDefault",
    "__?"       => current_user_can("peoples_company.Add")
);
$default_menu["Peoples"]["Company List"] = array(
    "t_link"    => full_website_address() . "/peoples/company-list/",
    "title"     => "Company List",
    "t_icon"    => "fa fa-users",
    "__?"       => current_user_can("peoples_company.View || peoples_company.Add || peoples_company.Edit || peoples_company.Delete")
);

// Settings default_menu
$default_menu["Settings"] = array(
    "t_link"    => "#",
    "title"     => "",
    "t_icon"    => "fa fa-gears"
);
$default_menu["Settings"]["Backup"] = array(
    "t_link"    => full_website_address() . "/settings/backup/",
    "title"     => "Database and File Backup",
    "t_icon"    => "fa fa-database", // fa fa-download
    "__?"       => current_user_can("module.View || module.Edit || module.Delete || module.Add")
);
$default_menu["Settings"]["Modules"] = array(
    "t_link"    => full_website_address() . "/settings/modules/",
    "title"     => "Modules",
    "t_icon"    => "fa fa-cube",
    "__?"       => current_user_can("module.View || module.Edit || module.Delete || module.Add")
);
$default_menu["Settings"]["System Settings"] = array(
    "t_link"    => full_website_address() . "/settings/system-settings/",
    "title"     => "System Settings",
    "t_icon"    => "fa fa-gear",
    "__?"       => current_user_can("settings_system.View || settings_system.Edit")
);
$default_menu["Settings"]["Security Settings"] = array(
    "t_link"    => full_website_address() . "/settings/security-settings/",
    "title"     => "Security Settings",
    "t_icon"    => "fa fa-lock",
    "__?"       => current_user_can("settings_security.View || settings_security.Edit")
);
$default_menu["Settings"]["POS Settings"] = array(
    "t_link"    => full_website_address() . "/settings/pos-settings/",
    "title"     => "POS Settings",
    "t_icon"    => "fa fa-th-large",
    "__?"       => current_user_can("settings_pos.View || settings_pos.Edit")
);
$default_menu["Settings"]["Invoice Settings"] = array(
    "t_link"    => full_website_address() . "/settings/invoice-settings/",
    "title"     => "Invoice Settings",
    "t_icon"    => "fa fa-gears",
    "__?"       => current_user_can("settings_system.View || settings_pos.Edit")
);
$default_menu["Settings"]["Others Settings"] = array(
    "t_link"    => full_website_address() . "/settings/others-settings/",
    "title"     => "Other Settings",
    "t_icon"    => "fa fa-gears",
    "__?"       => current_user_can("settings_system.View || settings_pos.Edit")
);
$default_menu["Settings"]["Tariff and Charges"] = array(
    "t_link"    => full_website_address() . "/settings/tariff-charges/",
    "title"     => "Tariff and Charges",
    "t_icon"    => "fa fa-percent",
    "__?"       => current_user_can("settings_tariff_charges.View || settings_tariff_charges.Add || settings_tariff_charges.Edit || settings_tariff_charges.Delete")
);
$default_menu["Settings"]["Group Permissions"] = array(
    "t_link"    => full_website_address() . "/settings/groups-permission-list/",
    "title"     => "Group Permission List",
    "t_icon"    => "fa fa-key",
    "__?"       => current_user_can("settings_group_permissions.View || settings_group_permissions.Add || settings_group_permissions.Edit || settings_group_permissions.Delete")
);
$default_menu["Settings"]["Department"] = array(
    "t_link"    => full_website_address() . "/settings/department-list/",
    "title"     => "Department List",
    "t_icon"    => "fa fa-building",
    "__?"       => current_user_can("settings_department.View || settings_department.Add || settings_department.Edit || settings_department.Delete")
);
$default_menu["Settings"]["Shops"] = array(
    "t_link"    => full_website_address() . "/settings/shop-list/",
    "title"     => "Shop List",
    "t_icon"    => "fa fa-shopping-cart",
    "__?"       => current_user_can("settings_shops.View || settings_shops.Add || settings_shops.Edit || settings_shops.Delete")
);


// Reports default_menu
$default_menu["Reports"] = array(
    "t_link"    => "#",
    "title"     => "",
    "t_icon"    => "fa fa-bar-chart"
);
$default_menu["Reports"]["Sales Report"] = array(
    "t_link"    => full_website_address() . "/reports/sales-report/",
    "title"     => "Sales Report",
    "t_icon"    => "fa fa-cubes",
    "__?"       => current_user_can("reports_sales.View")
);
$default_menu["Reports"]["Product Report"] = array(
    "t_link"    => full_website_address() . "/reports/product-report/",
    "title"     => "Product Report",
    "t_icon"    => "fa fa-cubes",
    "__?"       => current_user_can("reports_product.View")
);
$default_menu["Reports"]["Product Visual Report"] = array(
    "t_link"    => full_website_address() . "/reports/product-visual-report/",
    "title"     => "Product Visual Report",
    "t_icon"    => "fa fa-cubes",
    "__?"       => current_user_can("reports_product.View")
);
$default_menu["Reports"]["Product Stock Ledger"] = array(
    "t_link"    => full_website_address() . "/reports/product-ledger/",
    "title"     => "Product Stock Ledger",
    "t_icon"    => "fa fa-cubes",
    "__?"       => current_user_can("reports_product.View")
);
$default_menu["Reports"]["Locationwise Report"] = array(
    "t_link"    => full_website_address() . "/reports/locationwise-report/",
    "title"     => "Location Wise Report",
    "t_icon"    => "fa fa-location-arrow",
    "__?"       => current_user_can("reports_product.View")
);
$default_menu["Reports"]["Expired Products"] = array(
    "t_link"    => full_website_address() . "/reports/expired-products/",
    "title"     => "Expired Product",
    "t_icon"    => "fa fa-hourglass-end",
    "__?"       => current_user_can("reports_expired_products.View")
);
$default_menu["Reports"]["Employee Report"] = array(
    "t_link"    => full_website_address() . "/reports/employee-report/",
    "title"     => "Employee Report",
    "t_icon"    => "fa fa-cubes",
    "__?"       => current_user_can("reports_employee.View")
);
$default_menu["Reports"]["Product Comparison"] = array(
    "t_link"    => full_website_address() . "/reports/product-comparison/",
    "title"     => "Product Comparison",
    "t_icon"    => "fa fa-line-chart",
    "__?"       => current_user_can("product_comparison.View")
);
$default_menu["Reports"]["Sale Comparison"] = array(
    "t_link"    => full_website_address() . "/reports/product-comparison/",
    "title"     => "Sale Comparison",
    "t_icon"    => "fa fa-line-chart",
    "__?"       => current_user_can("product_comparison.View")
);
$default_menu["Reports"]["Customer Report"] = array(
    "t_link"    => full_website_address() . "/reports/customer-report/",
    "title"     => "Customer Report",
    "t_icon"    => "fa fa-cubes",
    "__?"       => current_user_can("reports_customer.View")
);
$default_menu["Reports"]["Customer Statement"] = array(
    "t_link"    => full_website_address() . "/reports/customer-statement/",
    "title"     => "Customer Statement",
    "t_icon"    => "fa fa-cubes",
    "__?"       => current_user_can("reports_customer.View")
);
$default_menu["Reports"]["Transaction Report"] = array(
    "t_link"    => full_website_address() . "/reports/customer-statement/",
    "title"     => "Transaction Report",
    "t_icon"    => "fa fa-cubes",
    "__?"       => current_user_can("reports_customer.View")
);
$default_menu["Reports"]["Expense Report"] = array(
    "t_link"    => full_website_address() . "/reports/expense-report/",
    "title"     => "Expense Report",
    "t_icon"    => "fa fa-cubes",
    "__?"       => current_user_can("reports_expense.View")
);

// The following menu will be not shown
$default_menu["Hidden"]["pos"] = array(
    "t_link"    => full_website_address() . "/sales/pos/",
    "title"     => "Point of Sale",
    "__?"       => current_user_can("myshop_pos_sales.Add") and is_biller()
);
$default_menu["Hidden"]["return"] = array(
    "t_link"    => full_website_address() . "/sales/return/",
    "title"     => "Point of Sale",
    "__?"       => current_user_can("myshop_return.Add")
);
$default_menu["Hidden"]["Edit Product"] = array(
    "t_link"    => full_website_address() . "/products/edit-product/",
    "title"     => "Edit Product",
    "t_icon"    => "fa fa-cubes",
    "__?"       => current_user_can("products.Edit")
);
$default_menu["Hidden"]["Edit Purchase"] = array(
    "t_link"    => full_website_address() . "/stock-management/edit-purchase/",
    "title"     => "Edit Product",
    "t_icon"    => "fa fa-cubes",
    "__?"       => current_user_can("product_purchases.Edit")
);
$default_menu["Hidden"]["Attach Sub Product"] = array(
    "t_link"    => full_website_address() . "/products/attach-sub-product/",
    "title"     => "Attach Sub Product",
    "t_icon"    => "fa fa-link",
    "dload"     => true,
    "__?"       => current_user_can("products.Add")
);