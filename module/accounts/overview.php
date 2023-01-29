  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      <?= __("Overview"); ?>
      </h1>
    </section>

    <?php 
      
        $overview = easySelectD("
            SELECT 
                db_date, 

                if(sales_due_sum is null, 0, sales_due_sum) as receivables_amount, 
                (   ( if(salary_amount_sum is null, 0, salary_amount_sum) + if(bills_amount_sum is null, 0, bills_amount_sum) ) - 
                    if(emp_com_payment_amount_sum is null, 0, emp_com_payment_amount_sum) 
                ) as payables_amount,

                (   (if(sales_grand_sum is null, 0, sales_grand_sum) - if(sales_due_sum is null, 0, sales_due_sum) ) + 
                    if(received_payments_amount_sum is null, 0, received_payments_amount_sum) + 
                    if(incomes_amount_sum is null, 0, incomes_amount_sum) 
                ) as total_income, 

                if(payment_amount_sum is null, 0, payment_amount_sum) + if(loan_amount_sum is null, 0, loan_amount_sum) as total_expence 

            FROM time_dimension

            left join ( select 
                    sales_delivery_date, 
                    sum(sales_grand_total) as sales_grand_sum, 
                    sum(sales_due) as sales_due_sum 
                from {$table_prefix}sales 
                where is_trash = 0
                group by sales_delivery_date 
            ) as get_sales on sales_delivery_date = db_date
            left join ( select 
                    received_payments_datetime, 
                    sum(received_payments_amount) as received_payments_amount_sum 
                from {$table_prefix}received_payments 
                group by date(received_payments_datetime) 
            ) as get_received_payments on date(received_payments_datetime) = db_date
            left join ( select 
                    incomes_date, 
                    sum(incomes_amount) as incomes_amount_sum 
                from {$table_prefix}incomes 
                group by incomes_date 
            ) as get_income on incomes_date = db_date
            left join ( select 
                    payment_date, 
                    sum(payment_amount) as payment_amount_sum 
                from {$table_prefix}payments 
                group by payment_date 
            ) as get_payments on get_payments.payment_date = db_date
            left join ( select 
                    loan_pay_on, 
                    sum(loan_amount) as loan_amount_sum 
                from {$table_prefix}loan group 
                by date(loan_pay_on) 
            ) as get_loan on date(loan_pay_on) = db_date
            left join ( select 
                    salary_update_on, 
                    sum(salary_amount) as salary_amount_sum 
                from {$table_prefix}salaries 
                group by date(salary_update_on) 
                ) as get_salaries on date(salary_update_on) = db_date
            left join ( select 
                    payment_date, 
                    sum(payment_amount) as emp_com_payment_amount_sum 
                from {$table_prefix}payments 
                where payment_to_company is not null or payment_to_employee is not null 
                group by payment_date 
            ) as get_com_emp_payments on get_com_emp_payments.payment_date = db_date
            left join ( select 
                    bills_date, 
                    sum(bills_amount) as bills_amount_sum 
                from {$table_prefix}bills
                group by bills_date 
            ) as get_bills on bills_date = db_date
            where month(CURRENT_DATE) = month(db_date) and year(CURRENT_DATE) = year(db_date)
            order by db_date ASC
        ")["data"];

      $dates = array();
      $incomes = array();
      $receivable = array();
      $expence = array();
      $payable = array();
      $profit = array();
      $profitable = array();

      // loop all data and push data into relavent array
      foreach($overview as $key => $data) {

        array_push($dates, $data["db_date"]);
        array_push($incomes, $data["total_income"]);
        array_push($receivable, $data["receivables_amount"]);
        array_push($expence, $data["total_expence"]);
        array_push($payable, ($data["payables_amount"]) < 0 ? 0 : $data["payables_amount"] );

        // Calculate Profit
        $calculateProfit = $data["total_income"] - $data["total_expence"]; 
        
        array_push($profit,  $calculateProfit);

      }


    ?>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="info-box bg-aqua">
            <span class="info-box-icon">
              <i class="fa fa-money"></i>
            </span>
            <div class="info-box-content">
              <span class="info-box-text"><?= __("This Month Income"); ?></span>
              <span class="info-box-number"><?php echo __(number_format(array_sum($incomes), 2)); ?></span>
              <div class="progress-group">
                <div class="progress">
                  <div class="progress-bar" style="width: 100%;"></div>
                </div>
                <span class="progress-text"><?= __("Receivables"); ?></span>
                <span class="progress-number"><?php echo __(number_format(array_sum($receivable), 2)); ?></span>
              </div>
            </div>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="info-box bg-red">
            <span class="info-box-icon">
              <i class="fa fa-shopping-cart"></i>
            </span>
            <div class="info-box-content">
              <span class="info-box-text"><?= __("This Month Expense"); ?></span>
              <span class="info-box-number"><?php echo __(number_format(array_sum($expence), 2)); ?></span>
              <div class="progress-group">
                <div class="progress">
                  <div class="progress-bar" style="width: 100%;"></div>
                </div>
                <span class="progress-text"><?= __("Payables"); ?></span>
                <span class="progress-number"><?php echo __(number_format(array_sum($payable), 2)); ?></span>
              </div>
            </div>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="info-box bg-green">
            <span class="info-box-icon">
              <i class="fa fa-heart"></i>
            </span>
            <div class="info-box-content">
              <span class="info-box-text"><?= __("This Month Profit"); ?></span>
              <span class="info-box-number"><?php echo __(number_format(array_sum($profit), 2)); ?></span>
              <div class="progress-group">
                <div class="progress">
                  <div class="progress-bar" style="width: 100%;"></div>
                </div>
                <span class="progress-text"><?= __("Profitables"); ?></span>
                <span class="progress-number"><?php echo __(number_format( (array_sum($receivable) - array_sum($payable)) , 2)); ?></span>
              </div>
            </div>
          </div>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row-->

      <!-- Chart -->
      <div class="row">
        <div class="col-lg-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><?= __("Overview Chart"); ?></h3>
            </div>
            <div class="box-body">
              <div class="chart">
                <canvas id="overviewChart" style="height: 480px"></canvas>
              </div>
            </div>
            
          </div>
        </div>
      </div>

    </section> <!-- Main content End tag -->
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <script src="<?php echo full_website_address(); ?>/assets/3rd-party/chart.js/Chart.min.js"></script>

  <script>

    var ctx = document.getElementById('overviewChart');
    var myLineChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?php echo __(json_encode($dates)) ?>,
        datasets: [
          {
            label: "<?= __("Income"); ?>",
            borderColor: "#00c0ef",
            borderWidth: 2,
            data: <?php echo json_encode($incomes) ?>
          },
          {
            label: "<?= __("Expense"); ?>",
            borderColor: "red",
            borderWidth: 2,
            data: <?php echo json_encode($expence) ?>
          },
          {
            label: "<?= __("Profit"); ?>",
            borderColor: "green",
            borderWidth: 2,
            data: <?php echo json_encode($profit) ?>
          }
        ]
      },
      options: {
        responsive: true,
        tooltips: {
          mode: 'index',
          intersect: false
        }
      }
    });
    
    var scrollY = "";
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=accounts&page=capitalList";
  </script>
