  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Overview"); ?>
      </h1>
    </section>

    <?php 
      $shopId = safe_input($_SESSION['sid']);
      $accountsId = safe_input($_SESSION['aid']);

      $shopOverview = easySelectD("
        select db_date,
        if(received_payments_amount_sum is null, 0, round(received_payments_amount_sum, 2) ) as received_payments_amount_sum,
        if(payment_amount_sum is null, 0, payment_amount_sum) as payment_amount_sum
        from time_dimension
        left join ( select received_payments_add_on, sum(received_payments_amount) as received_payments_amount_sum from {$table_prefeix}received_payments where received_payments_type != 'Discounts' AND received_payments_shop = '{$shopId}' group by date(received_payments_add_on) ) as get_received_payments on date(received_payments_add_on) = db_date
        left join ( select payment_date, sum(payment_amount) as payment_amount_sum from {$table_prefeix}payments where payment_from = {$accountsId} group by payment_date) as get_payments on payment_date = db_date
        where db_date = CURRENT_DATE
      ")["data"][0];

      $todayEarns = $shopOverview["received_payments_amount_sum"];
      $todayCashIn = $todayEarns - $shopOverview["payment_amount_sum"];
	    $todayCashIn = $todayCashIn < 0 ? 0 : $todayCashIn;
	  
    ?>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
	  <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon">
              <i class="fa fa-money"></i>
            </span>
            <div class="info-box-content">
              <span class="info-box-text"><?= __("Accounts Balance"); ?></span>
              <span class="info-box-number"><?php echo __(number_format(accounts_balance($_SESSION['aid']), 2)); ?></span>
            </div>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-aqua">
            <span class="info-box-icon">
              <i class="fa fa-money"></i>
            </span>
            <div class="info-box-content">
              <span class="info-box-text"><?= __("Today's Earns"); ?></span>
              <span class="info-box-number"><?php echo __(number_format($todayEarns, 2)); ?></span>
            </div>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-red">
            <span class="info-box-icon">
              <i class="fa fa-shopping-cart"></i>
            </span>
            <div class="info-box-content">
              <span class="info-box-text"><?= __("Today's Expense"); ?></span>
              <span class="info-box-number"><?php echo __(number_format($shopOverview["payment_amount_sum"], 2)); ?></span>
            </div>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-green">
            <span class="info-box-icon">
              <i class="fa fa-heart"></i>
            </span>
            <div class="info-box-content">
              <span class="info-box-text"><?= __("Today Cash In"); ?></span>
              <span class="info-box-number"><?php echo __(number_format($todayCashIn, 2)); ?></span>
            </div>
          </div>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row-->

      <br/>

      <div class="row">
        <!-- Best Seller Today -->
        <div class="col-xs-6">
          <div class="box box-info">
            <div class="box-header">
              <h3 class="box-title"><?= __("Today's Best Selling"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>
            <div class="box-body">
              <table class="table table-bordered table-striped table-hover" style="width: 100%;">
                <thead>
                  <tr>
                    <th class="no-sort"><?= __("Product Name"); ?></th>
                    <th class="text-right"><?= __("Sold Qty"); ?></th>
                  </tr>
                </thead>
                
                <tbody>
                <?php
                  $getTopSeller = easySelectD("
                    select 
                        stock_product_id, 
                        product_name, 
                        sum(stock_item_qty) as purchased_qnt 
                    from {$table_prefeix}product_stock
                    inner join {$table_prefeix}products on product_id = stock_product_id
                    where stock_type = 'sale' and date(stock_entry_date) = current_date group by stock_product_id order by purchased_qnt DESC LIMIT 0,5
                ");


                  if($getTopSeller) {

                    foreach($getTopSeller["data"] as $key => $tc) {
                      echo "<tr>";
                      echo  "<td>{$tc['product_name']}</td>";
                      echo  "<td class='text-right'>{$tc['purchased_qnt']}</td>";
                      echo "</tr>";
                    } 

                  } else {
                    echo "<tr><td class='text-center' colspan='2'>No Records Found</td></tr>";
                  }

                ?>
                </tbody>
 
              </table>
            </div>
          </div>
        </div> <!-- /.Col -->

        <!-- Pie Chart of Current year by Month -->
        <div class="col-xs-6">
          <div class="box box-info">
            <div class="box-header">
              <h3 class="box-title"><?= __("Pie Chart"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>
            <div class="box-body">
              <div class="chart">
                <canvas id="soldByMonth" style="height: 225px"></canvas>
              </div>
            </div>
          </div>
        </div> <!-- /.Col -->

      </div> <!-- /.Row -->

      <div class="row">

            <!-- Top Customer Today -->
            <div class="col-xs-6">
                <div class="box box-info">
                    <div class="box-header">
                        <h3 class="box-title"><?= __("Today's Top Customer"); ?></h3>
                        <div class="printButtonPosition"></div>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="no-sort"><?= __("Customer Name"); ?></th>
                                    <th class="text-right"><?= __("Purchased Qnt"); ?></th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                <?php
                                
                                $getTopCustomer = easySelectD("
                                    select sales_delivery_date, sales_customer_id, sum(sales_quantity) as total_purchased_qnt_today, customer_name from {$table_prefeix}sales 
                                    left join {$table_prefeix}customers on sales_customer_id = customer_id
                                    where sales_delivery_date = CURRENT_DATE group by sales_customer_id order by total_purchased_qnt_today DESC LIMIT 0,5
                                ");


                                if($getTopCustomer) {

                                    foreach($getTopCustomer["data"] as $key => $tc) {
                                    echo "<tr>";
                                    echo  "<td>{$tc['customer_name']}</td>";
                                    echo  "<td class='text-right'>{$tc['total_purchased_qnt_today']}</td>";
                                    echo "</tr>";
                                    } 

                                } else {
                                    echo "<tr><td class='text-center' colspan='2'>No Records Found</td></tr>";
                                }

                                ?>
                            </tbody>
            
                        </table>
                    </div>
                </div>
            </div> <!-- /.Col -->

            <!-- Top Customer Today -->
            <div class="col-xs-6">
                <div class="box box-danger">
                    <div class="box-header bg-red">
                        <h3 class="box-title"><?= __("Expired in 30 days"); ?></h3>
                        <div class="printButtonPosition"></div>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="no-sort"><?= __("Product Name"); ?></th>
                                    <th><?= __("Qty"); ?></th>
                                    <th><?= __("Batch No."); ?></th>
                                    <th><?= __("Expired In"); ?></th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                <?php
                                
                                $expireySoonProduct = easySelectD("
                                    SELECT
                                        product_name,
                                        round(base_stock_in / base_qty, 2) as expired_qty,
                                        pbs.batch_expiry_date as expiry_date,
                                        batch_number
                                    FROM product_base_stock as pbs
                                    left join {$table_prefeix}products as product on product.product_id = pbs.vp_id
                                    left join {$table_prefeix}product_batches as product_batches on product_batches.batch_id = pbs.batch_id
                                    WHERE pbs.batch_expiry_date < DATE_ADD(NOW(), INTERVAL 30 DAY)
                                    order by pbs.batch_expiry_date DESC
                                ");


                                if($expireySoonProduct) {

                                    foreach($expireySoonProduct["data"] as $ep) {
                                        echo "<tr>";
                                        echo  "<td>{$ep['product_name']}</td>";
                                        echo  "<td>{$ep['expired_qty']}</td>";
                                        echo  "<td>{$ep['batch_number']}</td>";
                                        echo  "<td>".  number_format( (strtotime($ep['expiry_date']) -  time() ) / 86400, 0) ." Day(s)</td>";
                                        echo "</tr>";
                                    } 

                                } else {
                                    echo "<tr><td class='text-center' colspan='2'>No Records Found</td></tr>";
                                }

                                ?>
                            </tbody>
            
                        </table>
                    </div>
                </div>
            </div> <!-- /.Col -->

      </div> <!-- /.Row -->

    </section> <!-- Main content End tag -->
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <script src="<?php echo full_website_address(); ?>/assets/3rd-party/chart.js/Chart.min.js"></script>

  <script>

    var ctx = document.getElementById('soldByMonth');
    new Chart(ctx, {
      type: 'pie',
      data: {
        labels: ["<?= __("Earn's"); ?>", "<?= __("Expense"); ?>", "<?= __("Cash In"); ?>"],
        datasets: [{
          data: [<?php echo "'{$todayEarns}', '". ($shopOverview['payment_amount_sum'] - 0) ."', '{$todayCashIn}'" ?>],
          backgroundColor: [
              "#00c0ef",
              "red",
              "green"
            ],
        }],
        
      },
      options: {
        responsive: true,
        legend: {
          display: true,
          position: "left"
        }
      }
    });

  </script>
