<?php

echo "<ul class='sidebar-menu' data-widget='tree'>";
generateMenu($menu);
echo '</ul>';

?>

    <ul class="sidebar-menu" data-widget="tree">

      <!-- DashBoard Menu  -->
      <li>
        <a href="<?php echo full_website_address(); ?>/">
          <i class="fa fa-dashboard"></i> <span>Dashboard</span> 
        </a>
      </li>
      <!-- End Accounts Menu  -->
      
      <?php if(current_user_can_visit("accounts")) { ?>
      <!-- Accounts Menu  -->
      <li class="treeview">
        <a href="#">
          <i class="fa fa-cubes"></i> <span>Accounts</span> 
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <?php if(current_user_can_visit("accounts/overview")) { ?>
            <li> <a href="<?php echo full_website_address(); ?>/accounts/overview/"> <i class="fa fa-cubes"></i> Overview</a> </li>
          <?php } ?>

          <?php if(current_user_can_visit("accounts/account-list")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/accounts/account-list/"> <i class="fa fa-cubes"></i> Account List</a> </li>
          <?php } ?>
          
          <?php if(current_user_can_visit("accounts/new-account")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/accounts/new-account/"> <i class="fa fa-cubes"></i> New Account</a> </li>
          <?php } ?>
          
          <?php if(current_user_can_visit("accounts/incomes")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/accounts/incomes/"> <i class="fa fa-cubes"></i> Incomes</a> </li>
          <?php } ?>
          
          <?php if(current_user_can_visit("accounts/capital")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/accounts/capital/"> <i class="fa fa-cubes"></i> Capital</a> </li>
          <?php } ?>
          
          <?php if(current_user_can_visit("accounts/transfer")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/accounts/transfer/"> <i class="fa fa-cubes"></i> Transfer</a> </li>
          <?php } ?>
        </ul>
      </li>
      <!-- End Accounts Menu  -->
      <?php } ?>

      <?php if(current_user_can_visit("ledgers")) { ?>
      <!-- Ledgers Menu  -->
      <li class="treeview">
        <a href="#">
          <i class="fa fa-book"></i> <span>Ledgers</span> 
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <?php if(current_user_can_visit("ledgers/accounts")) { ?>
           <li> <a href="<?php echo full_website_address(); ?>/ledgers/accounts-ledger/"> <i class="fa fa-cubes"></i> Accounts Ledger</a> </li>
          <?php } 
          if(current_user_can_visit("ledgers/employee")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/ledgers/employee-ledger/"> <i class="fa fa-cubes"></i> Employee Ledger</a> </li>
          <?php } 
          if(current_user_can_visit("ledgers/customer")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/ledgers/customer-ledger/"> <i class="fa fa-cubes"></i> Customer Ledger</a> </li>
          <?php } 
          if(current_user_can_visit("ledgers/company")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/ledgers/company-ledger/"> <i class="fa fa-cubes"></i> Company Ledger</a> </li>
          <?php } ?>
        </ul>
      </li>
      <!-- End Ledgers Menu  -->
      <?php } ?>

      <?php if(current_user_can_visit("journals")) { ?>
      <!-- Ledgers Menu  -->
      <li class="treeview">
        <a href="#">
          <i class="fa fa-book"></i> <span>journals</span> 
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <?php if(current_user_can_visit("journals/journal-list")) { ?>
           <li> <a href="<?php echo full_website_address(); ?>/journals/journal-list/"> <i class="fa fa-cubes"></i> Journal List</a> </li>
          <?php } 
          if(current_user_can_visit("journals/create-journal")) { ?>
          <li> <a data-toggle="modal" data-target="#modalDefault" href="<?php echo full_website_address(); ?>/xhr/?tooltip=true&select2=true&module=journals&page=newJournal"> <i class="fa fa-plus"></i> Create Journal</a> </li>
          <?php } 
          if(current_user_can_visit("journals/journal-records")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/journals/journal-records/"> <i class="fa fa-book"></i> Journal Records</a> </li>
          <?php } ?>
        </ul>
      </li>
      <!-- End Ledgers Menu  -->
      <?php } ?>

      <?php if(current_user_can_visit("expenses")) { ?>
      <!-- Expenses Menu  -->
      <li class="treeview">
        <a href="#">
          <i class="fa fa-money"></i> <span>Expenses</span> 
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <?php if(current_user_can_visit("expenses/payments")) { ?>
            <li> <a href="<?php echo full_website_address(); ?>/expenses/payments/"> <i class="fa fa-cubes"></i> Payments</a> </li>
          <?php } 
          if(current_user_can_visit("expenses/advance-payments")) { ?>
            <li> <a href="<?php echo full_website_address(); ?>/expenses/advance-payments/"> <i class="fa fa-cubes"></i> Advance Payments</a> </li>
          <?php } 
          if(current_user_can_visit("expenses/bills")) { ?>
            <li> <a href="<?php echo full_website_address(); ?>/expenses/bills/"> <i class="fa fa-cubes"></i> Bills</a> </li>
          <?php } 
          if(current_user_can_visit("expenses/salaries")) { ?>
            <li> <a href="<?php echo full_website_address(); ?>/expenses/salaries/"> <i class="fa fa-cubes"></i> Salaries</a> </li>
          <?php } 
          if(current_user_can_visit("expenses/loan")) { ?>
            <li> <a href="<?php echo full_website_address(); ?>/expenses/loan/"> <i class="fa fa-cubes"></i> Loan</a> </li>
          <?php } 
          if(current_user_can_visit("expenses/payment-categories")) { ?>
            <li> <a href="<?php echo full_website_address(); ?>/expenses/payment-categories/"> <i class="fa fa-cubes"></i> Payment Categories</a> </li>
          <?php } ?>
        </ul>
      </li>
      <!-- End Expenses Menu  -->
      <?php } ?>
      
      <?php if(current_user_can_visit("assets")) { ?>
      <!-- Assets Menu  -->
      <li class="treeview">
        <a href="#">
          <i class="fa fa-cubes"></i> <span> Assets</span> 
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li> <a href="<?php echo full_website_address(); ?>/assets/"> <i class="fa fa-cubes"></i> Asset List</a> </li>
          <li> <a href="<?php echo full_website_address(); ?>/assets/"> <i class="fa fa-cubes"></i> New Asset</a> </li>
          <li> <a href="<?php echo full_website_address(); ?>/assets/"> <i class="fa fa-cubes"></i> Purchases</a> </li>
          <li> <a href="<?php echo full_website_address(); ?>/assets/"> <i class="fa fa-cubes"></i> Vendor</a> </li>
        </ul>
      </li>
      <!-- End Assets Menu  -->
      <?php } ?>

      <?php if(current_user_can_visit("products")) { ?>
      <!-- Product Menu -->
      <li class="treeview">
        <a href="#"><i class="fa fa-cubes"></i> <span>Products</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">          
          <?php if(current_user_can_visit("products/product-list")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/products/product-list/"> <i class="fa fa-cubes"></i> Product List</a> </li>
          <?php } ?>
          
          <?php if(current_user_can_visit("products/new-product")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/products/new-product"> <i class="fa fa-plus-circle"></i> Add Product</a> </li>
          <?php } ?>
          
          <?php if(current_user_can_visit("products/product-purchases")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/products/product-purchases/"> <i class="fa fa-shopping-bag"></i> Purchase List</a> </li>
          <?php } ?>
          
          <?php if(current_user_can_visit("products/new-purchase") and isset($_SESSION["aid"]) ) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/products/new-purchase/"> <i class="fa fa-shopping-bag"></i> Add Purchases</a> </li>
          <?php } ?>
          
          <?php if(current_user_can_visit("products/product-categories")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/products/product-categories/"> <i class="fa fa-folder-open"></i> Product Categories</a> </li>
          <?php } ?>
          
          <?php if(current_user_can_visit("products/product-warehouse")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/products/product-warehouse/"> <i class="fa fa-building-o"></i> Warehouse</a> </li>
          <?php } ?>
        </ul>
      </li>
      <!-- End Product Menu -->
      <?php } ?>

      <?php if(current_user_can_visit("my-shop") and isset($_SESSION["sid"]) ) { ?>
      <!-- My Shop Menu  -->
      <li class="treeview">
        <a href="#">
          <i class="fa fa-shopping-cart"></i> <span>My Shop</span> 
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          
          <?php if(current_user_can_visit("my-shop/shop-overview")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/my-shop/shop-overview/"> <i class="fa fa-cubes"></i> Overview</a> </li>
          <?php } ?>

          <?php if(current_user_can_visit("my-shop/transfer-balance")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/my-shop/transfer-balance/"> <i class="fa fa-cubes"></i> Transfer Balance</a> </li>
          <?php } ?>

          <?php if(current_user_can_visit("my-shop/shop-expenses")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/my-shop/shop-expenses/"> <i class="fa fa-cubes"></i> Expenses</a> </li>
          <?php } ?>
          
          <?php if(current_user_can_visit("my-shop/shop-advance-collection")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/my-shop/shop-advance-collection/"> <i class="fa fa-cubes"></i> Advance Collection</a> </li>
          <?php } ?>
          
          <?php if(current_user_can_visit("my-shop/received-payments")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/my-shop/received-payments/"> <i class="fa fa-cubes"></i> Received Payments</a> </li>
          <?php } ?>
          
          <?php if(current_user_can_visit("my-shop/pos-sale")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/my-shop/pos-sale/"> <i class="fa fa-cubes"></i> POS Sales</a> </li>
          <?php } ?>
          
          <?php if(current_user_can_visit("my-shop/shop-product-returns")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/my-shop/shop-product-returns/"> <i class="fa fa-cubes"></i> Return List</a> </li>
          <?php } ?>
          
          <?php if(current_user_can_visit("my-shop/new-return")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/my-shop/new-return/"> <i class="fa fa-cubes"></i> Add Return</a> </li>
          <?php } ?>
          
          <?php if(current_user_can_visit("my-shop/discounts")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/my-shop/discounts/"> <i class="fa fa-cubes"></i> Discounts</a> </li>
          <?php } ?>
        </ul>
      </li>
      <!-- End My Shop menu  -->
      <?php } ?>
      
      <?php if(current_user_can_visit("sales")) { ?>
      <!-- Sales Menu  -->
      <li class="treeview">
        <a href="#">
          <i class="fa fa-shopping-cart"></i> <span>Sales</span> 
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <?php if(current_user_can_visit("sales/sales-advance-collection")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/sales/sales-advance-collection/"> <i class="fa fa-cubes"></i> Advance Collection</a> </li>
          <?php } ?>
          
          <?php if(current_user_can_visit("sales/sales-received-payments")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/sales/sales-received-payments/"> <i class="fa fa-cubes"></i> Received Payments</a> </li>
          <?php } ?>
          
          <?php if(current_user_can_visit("sales/pos-sale")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/sales/pos-sale/"> <i class="fa fa-cubes"></i> POS Sales</a> </li>
          <?php } ?>
          
          <?php if(current_user_can_visit("sales/sales-return")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/sales/sales-return/"> <i class="fa fa-cubes"></i> Returns</a> </li>
          <?php } ?>
        </ul>
      </li>
      <!-- End Sales Menu  -->
      <?php } ?>
      
      <?php if(current_user_can_visit("peoples")) { ?>
      <!-- Peoples Menu -->
      <li class="treeview">
        <a href="#"><i class="fa fa-users"></i> <span>Peoples</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        
        <ul class="treeview-menu">
          <?php if(current_user_can_visit("users")) { ?>
          <!-- Users Menu -->
          <li class="treeview">
              <a href="#"><i class="fa fa-users"></i> Users
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <?php if(current_user_can_visit("peoples/users/new-user")) { ?>
                  <li><a href="<?php echo full_website_address(); ?>/peoples/users/new-user/"><i class="fa fa-user-plus"></i> New User</a></li>
                <?php } ?>
          
                <?php if(current_user_can_visit("peoples/users/user-list")) { ?>
                <li><a href="<?php echo full_website_address(); ?>/peoples/users/user-list/"><i class="fa fa-users"></i> User List</a></li>
                <?php } ?>
              </ul>
          </li>
          <!-- Users Menu -->
          <?php } ?>

          <?php if(current_user_can_visit("employees")) { ?>
          <!-- Employees Menu -->
          <li class="treeview">
            <a href="#"><i class="fa fa-users"></i> Employees
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <?php if(current_user_can_visit("peoples/employees/new-employee")) { ?>
              <li><a href="<?php echo full_website_address(); ?>/peoples/employees/new-employee/"><i class="fa fa-user-plus"></i> New Employee</a></li>
              <?php } ?>
              <?php if(current_user_can_visit("peoples/employees/employee-list")) { ?>
              <li><a href="<?php echo full_website_address(); ?>/peoples/employees/employee-list/"><i class="fa fa-users"></i> Employee List</a></li>
              <?php } ?>
            </ul>
          </li>
          <!-- Employees Menu -->
          <?php } ?>

          <?php if(current_user_can_visit("billers")) { ?>
          <!-- Billers Menu -->
          <li class="treeview">
            <a href="#"><i class="fa fa-users"></i> Billers
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <?php if(current_user_can_visit("peoples/billers/new-biller")) { ?>
              <li><a href="<?php echo full_website_address(); ?>/peoples/billers/new-biller/"><i class="fa fa-plus-circle"></i> New Biller</a></li>
              <?php } ?>

              <?php if(current_user_can_visit("peoples/billers/biller-list")) { ?>
              <li><a href="<?php echo full_website_address(); ?>/peoples/billers/biller-list/"><i class="fa fa-users"></i> Biller List</a></li>
              <?php } ?>
            </ul>
          </li>
          <!-- Billers Menu -->
          <?php } ?>

          <?php if(current_user_can_visit("customers")) { ?>
          <!-- Customers Menu -->
          <li class="treeview">
            <a href="#"><i class="fa fa-users"></i> Customers
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <?php if(current_user_can_visit("peoples/customers/new-customer")) { ?>
              <li><a href="<?php echo full_website_address(); ?>/peoples/customers/new-customer/"><i class="fa fa-plus-circle"></i> New Customer</a></li>
              <?php } ?>

              <?php if(current_user_can_visit("peoples/customers/customer-list")) { ?>
              <li><a href="<?php echo full_website_address(); ?>/peoples/customers/customer-list/"><i class="fa fa-users"></i> Customer List</a></li>
              <?php } ?>
            </ul>
          </li>
          <!-- Customers Menu -->
          <?php } ?>

          <?php if(current_user_can_visit("companies")) { ?>
          <!-- Companies Menu -->
          <li class="treeview">
            <a href="#"><i class="fa fa-users"></i> Companies
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <?php if(current_user_can_visit("peoples/companies/new-company")) { ?>
              <li><a href="<?php echo full_website_address(); ?>/peoples/companies/new-company/"><i class="fa fa-plus-circle"></i> New Company</a></li>
              <?php } ?>

              <?php if(current_user_can_visit("peoples/companies/company-list")) { ?>
              <li><a href="<?php echo full_website_address(); ?>/peoples/companies/company-list/"><i class="fa fa-users"></i> Company List</a></li>
              <?php } ?>
            </ul>
          </li>
          <!-- Companies Menu -->
          <?php } ?>

        </ul>
        
      </li>
      <!-- Peoples Menu -->
      <?php } ?>

      <?php if(current_user_can_visit("settings")) { ?>
      <!-- Settings Menu -->
      <li class="treeview">
        <a href="#"><i class="fa fa-gears"></i> <span>Settings</span>
          <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>

        <ul class="treeview-menu">
          <?php if(current_user_can_visit("#")) { ?>
          <li><a href="#"><i class="fa fa-gear"></i> System Settings</a></li>
          <?php } ?>

          <?php if(current_user_can_visit("#")) { ?>
          <li><a href="#"><i class="fa fa-th-large"></i> POS Settings</a></li>
          <?php } ?>

          <?php if(current_user_can_visit("settings/item-units")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/settings/item-units"> <i class="fa fa-balance-scale"></i> Item Units</a> </li>
          <?php } ?>

          <?php if(current_user_can_visit("groups")) { ?>
          <!-- Groups Menu -->
          <li class="treeview">
            <a href="#"><i class="fa fa-key"></i> Group Permissions
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <?php if(current_user_can_visit("settings/groups/new")) { ?>
              <li><a href="<?php echo full_website_address(); ?>/settings/groups/new/"><i class="fa fa-plus-circle"></i> New Group</a></li>
              <?php } ?>

              <?php if(current_user_can_visit("settings/groups/list")) { ?>
              <li><a href="<?php echo full_website_address(); ?>/settings/groups/list/"><i class="fa fa-key"></i> Groups List</a></li>
              <?php } ?>
            </ul>
          </li>
          <!-- Groups Menu -->
          <?php } ?>

          <?php if(current_user_can_visit("departments")) { ?>
          <!-- Department Menu -->
          <li class="treeview">
            <a href="#"><i class="fa fa-building"></i> Department
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <?php if(current_user_can_visit("settings/departments/new-department")) { ?>
              <li><a href="<?php echo full_website_address(); ?>/settings/departments/new-department/"><i class="fa fa-plus-circle"></i> New Department</a></li>
              <?php } ?>

              <?php if(current_user_can_visit("settings/departments/department-list")) { ?>
              <li><a href="<?php echo full_website_address(); ?>/settings/departments/department-list/"><i class="fa fa-building"></i> Department List</a></li>
              <?php } ?>
            </ul>
          </li>
          <!-- Department Menu -->
          <?php } ?>

          <?php if(current_user_can_visit("shop")) { ?>
          <!-- Shop Menu -->
          <li class="treeview">
            <a href="#"><i class="fa fa-shopping-cart"></i> Shop
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <?php if(current_user_can_visit("settings/shop/new-shop")) { ?>
              <li><a href="<?php echo full_website_address(); ?>/settings/shop/new-shop/"><i class="fa fa-plus-circle"></i> New Shop</a></li>
              <?php } ?>

              <?php if(current_user_can_visit("settings/shop/shop-list")) { ?>
              <li><a href="<?php echo full_website_address(); ?>/settings/shop/shop-list/"><i class="fa fa-shopping-cart"></i> Shop List</a></li>
              <?php } ?>
            </ul>
          </li>
          <!-- Shop Menu -->
          <?php } ?>

        </ul>
      </li>
      <!-- Settings Menu -->
      <?php } ?>

      <?php if(current_user_can_visit("reports")) { ?>
      <!-- Accounts Menu  -->
      <li class="treeview">
        <a href="#">
          <i class="fa fa-bar-chart"></i> <span>Reports</span> 
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">

          <?php if(current_user_can_visit("reports/sales-report")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/reports/sales-report/"> <i class="fa fa-cubes"></i> Sales Report</a> </li>
          <?php } ?>

          <?php if(current_user_can_visit("reports/product-report")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/reports/product-report/"> <i class="fa fa-cubes"></i> Product Report</a> </li>
          <?php } ?>

          <?php if(current_user_can_visit("reports/customer-report")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/reports/customer-report/"> <i class="fa fa-cubes"></i> Customer Report</a> </li>
          <?php } ?>

          <?php if(current_user_can_visit("reports/customer-statement")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/reports/customer-statement/"> <i class="fa fa-cubes"></i> Customer Statement</a> </li>
          <?php } ?>

          <?php if(current_user_can_visit("reports/expense-report")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/reports/expense-report/"> <i class="fa fa-cubes"></i> Expense Report</a> </li>
          <?php } ?>

          <?php if(current_user_can_visit("reports/company-report")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/reports/company-report/"> <i class="fa fa-cubes"></i> Company Report</a> </li>
          <?php } ?>

          <?php if(current_user_can_visit("reports/employee-report")) { ?>
          <li> <a href="<?php echo full_website_address(); ?>/reports/employee-report/"> <i class="fa fa-cubes"></i> Employee Report</a> </li>
          <?php } ?>
        </ul>
      </li>
      <!-- End Accounts Menu  -->
      <?php } ?>

    </ul>
    <!-- /.sidebar-menu -->