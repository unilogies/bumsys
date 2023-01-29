<?php

$cid = safe_input($_GET["cid"]);

$getCustomerDetails = easySelectD(
  "select customer_id, customer_name, if(customer_opening_balance is null, 0, customer_opening_balance) as customer_opening_balance,
    if(sales_total_amount is null, 0, sum(sales_total_amount)) as sales_total_amount_sum, 
    if(sales_product_discount is null, 0, sum(sales_product_discount)) as sales_product_discount_sum, 
    if(sales_order_discount is null, 0, sum(sales_order_discount)) as sales_order_discount_sum, 
    if(sales_shipping is null, 0, sum(sales_shipping)) as sales_shipping_sum, 
    ( if(sales_grand_total is null, 0, sum(sales_grand_total)) ) as sales_grand_total, 
    ( if(sales_grand_total is null, 0, sum(sales_grand_total)) - if(sales_due is null, 0, sum(sales_due)) ) as sales_paid_amount_sum,
    if(returns_grand_total is null, 0, returns_grand_total) as returns_grand_total_sum,
    if(received_payments_amount is null, 0, received_payments_amount) as total_received_payments,
    if(received_payments_bonus is null, 0, received_payments_bonus) as total_given_bonus,
    if(discounts_amount is null, 0, discounts_amount) as special_discount
  from {$table_prefix}customers
  left join (
    select
        sales_customer_id,
        sum(sales_total_amount) as sales_total_amount,
        sum(sales_product_discount) as sales_product_discount,
        sum(sales_discount) as sales_order_discount,
        sum(sales_shipping) as sales_shipping,
        sum(sales_grand_total) as sales_grand_total,
        sum(sales_due) as sales_due
    from {$table_prefix}sales where is_trash = 0 and is_return = 0 group by sales_customer_id
) as sales on customer_id = sales.sales_customer_id
  left join ( 
        select sales_customer_id, 
        sum(sales_grand_total) as returns_grand_total 
  from {$table_prefix}sales where is_trash = 0 and is_return = 1 group by sales_customer_id) as product_returns on customer_id = product_returns.sales_customer_id
  left join 
      ( select received_payments_from, 
      sum(received_payments_amount) as received_payments_amount, 
      sum(received_payments_bonus) as received_payments_bonus 
  from {$table_prefix}received_payments where is_trash = 0 and received_payments_type != 'Discounts' group by received_payments_from) as received_payments on customer_id = received_payments.received_payments_from
  left join 
      ( select received_payments_from, 
      sum(received_payments_amount) as discounts_amount
  from {$table_prefix}received_payments where is_trash = 0 and received_payments_type = 'Discounts' group by received_payments_from) as given_discounts on customer_id = given_discounts.received_payments_from

  where customer_id = '{$cid}'"
)["data"][0];

$totalPaid = $getCustomerDetails["total_received_payments"] + $getCustomerDetails["total_given_bonus"];

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <?= __("Report of"); ?> <?php echo $getCustomerDetails["customer_name"] ?>
    </h1>
  </section>
  <!-- Main content -->
  <section class="content container-fluid">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div style="cursor: pointer;" data-html="true" data-toggle="tooltip" 
        title="<p class='text-left'> 
        <?= __("Net Purchased:"); ?> <?php echo __(number_format($getCustomerDetails["sales_total_amount_sum"], 2)); ?> <br/> 
        <?= __("Product Discount:"); ?> <?php echo __(number_format( $getCustomerDetails["sales_product_discount_sum"], 2)); ?> <br/> 
        <?= __("Order Discount:"); ?> <?php echo __(number_format($getCustomerDetails["sales_order_discount_sum"], 2)); ?> <br/> 
        <?= __("Shipping:"); ?> <?php echo __(number_format($getCustomerDetails["sales_shipping_sum"], 2)); ?> </p>" 
      class="col-lg-4 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-aqua">
          <div class="inner">
            <h3><?php echo __(number_format($getCustomerDetails["sales_grand_total"], 2)) ; ?></h3>

            <p><?= __("Grand Total"); ?></p>
          </div>
          <div class="icon">
            <i class="fa fa-shopping-bag"></i>
          </div>
        </div>
      </div>
      <!-- ./col -->

      <div style="cursor: pointer;" data-html="true" data-toggle="tooltip" 
        title="<p class='text-left'> 
        <?= __("Sales Paid:"); ?> <?php echo __(number_format($getCustomerDetails["sales_paid_amount_sum"], 2)); ?> <br/> 
        <?= __("Received Payments:"); ?> <?php echo __(number_format($getCustomerDetails["total_received_payments"], 2)); ?> <br/> 
        <?= __("Given Bonus:"); ?> <?php echo __(number_format($getCustomerDetails["total_given_bonus"], 2)); ?> </p>" 
      class="col-lg-4 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-red">
          <div class="inner">
            <h3><?php echo __(number_format($totalPaid, 2)); ?></h3>

            <p><?= __("Total Paid"); ?></p>
          </div>
          <div class="icon">
            <i class="fa fa-shopping-cart"></i>
          </div>
        </div>
      </div>
      <!-- ./col -->

      <div style="cursor: pointer;" data-html="true" data-toggle="tooltip" 
        title="<p class='text-left'> 
        <?= __("Opening Balance:"); ?> <?php echo __(number_format($getCustomerDetails["customer_opening_balance"], 2)); ?> <br/> 
        <?= __("Special Discount:"); ?> <?php echo __(number_format($getCustomerDetails["special_discount"], 2)); ?> <br/> 
        <?= __("Returns:"); ?> <?php echo __(number_format($getCustomerDetails["returns_grand_total_sum"], 2)); ?> </p>" 
      class="col-lg-4 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-green">
          <div class="inner">
            <h3><?php echo __(number_format( (($getCustomerDetails["customer_opening_balance"]) + $totalPaid + $getCustomerDetails["returns_grand_total_sum"] + $getCustomerDetails["special_discount"] ) - $getCustomerDetails["sales_grand_total"] , 2)) ; ?></h3>

            <p><?= __("Balance"); ?></p>
          </div>
          <div class="icon">
            <i class="fa fa-money"></i>
          </div>
        </div>
      </div>
      <!-- ./col -->

    </div>
    <!-- /.row -->

     <!-- Chart: Last 30 days Purcahse report -->
     <div class="row">
      <div class="col-lg-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><?= __("Last %d days Purchase Overview", 30); ?></h3>
          </div>
          <div class="box-body">
            <div class="chart">
              <canvas id="30daysOverviewChart" style="height: 280px"></canvas>
            </div>
          </div>
          
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Top Customer of this product -->
      <div class="col-xs-6">
        <div class="box box-info">
          <div class="box-header">
            <h3 class="box-title"><?= __("Top Products"); ?></h3>
            <div class="printButtonPosition"><a class="" target="_blank" href='<?php echo full_website_address(); ?>/print/?page=allProductOfThisCustomer&cid=<?php echo safe_entities($_GET["cid"]); ?>'>View All</a></div>
          </div>
          <div class="box-body">
            <table class="table table-bordered table-striped table-hover" style="width: 100%;">
              <thead>
                <tr>
                  <th class="no-sort"><?= __("Product Name"); ?></th>
                  <th class="text-right"><?= __("Purchased Qnt"); ?></th>
                </tr>
              </thead>
              
              <tbody>
              <?php

                $getTopCustomer = easySelectD("
                        SELECT
                            product_name, product_unit,
                            (
                                if(purchased_qnt is null, 0, sum(purchased_qnt)) -
                                if(return_qnt is null, 0, sum(return_qnt)) 
                            ) as purchase_qty
                        FROM {$table_prefix}sales as sales
                        left join( SELECT
                                    stock_product_id,
                                    stock_sales_id,
                                    sum(CASE WHEN stock_type = 'sale' then stock_item_qty end) as purchased_qnt,
                                    sum(CASE WHEN stock_type = 'sale-return' then stock_item_qty end) as return_qnt
                                from {$table_prefix}product_stock
                                where is_trash = 0 and stock_type in ('sale', 'sale-return')
                                group by stock_sales_id, stock_product_id
                        ) as product_stock on stock_sales_id = sales_id
                        left join {$table_prefix}products on product_id = stock_product_id
                        where sales_customer_id = '{$cid}' and sales.is_trash = 0 and sales.is_wastage = 0
                        group by stock_product_id
                        order by purchase_qty DESC limit 0,5

                ");


                if($getTopCustomer != false) {

                  foreach($getTopCustomer["data"] as $key => $tc) {
                    echo "<tr>";
                    echo  "<td>{$tc['product_name']}</td>";
                    echo  "<td class='text-right'>". $tc['purchase_qty'] ."</td>";
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
            <h3 class="box-title"><?= __("Purchase By Month"); ?></h3>
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


  </section> <!-- Main content End tag -->
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->


<script src="<?php echo full_website_address(); ?>/assets/3rd-party/chart.js/Chart.min.js"></script>

<?php

$last30DaysPurchaseDetails = easySelectD("
    select 
        db_date, 
        if(stock_item_qty is null, 0, stock_item_qty) as sales_quantity_sum 
    from time_dimension 
    left join ( 
        select 
            stock_sales_id,
            stock_entry_date,
            sum(stock_item_qty) as stock_item_qty 
        from {$table_prefix}product_stock as product_stock
        left join {$table_prefix}sales on stock_sales_id = sales_id
        where product_stock.is_trash = 0 and stock_type = 'sale' and sales_customer_id = '{$cid}'
        group by stock_sales_id, stock_entry_date 
    ) as get_sales_data on stock_entry_date = db_date
    where db_date BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) and DATE(NOW())  
    ORDER BY db_date ASC
");
$purchaseDate = array();
$purchaseQnt = array();

if($last30DaysPurchaseDetails != false) {
  foreach($last30DaysPurchaseDetails["data"] as $key => $pd) {
    array_push($purchaseDate, $pd["db_date"]);
    array_push($purchaseQnt, $pd["sales_quantity_sum"]);
  }
}


// Purchase Data By Month
$purchaseByMonth = easySelectD("
    select 
            MONTHNAME(db_date) as sales_month, 
            if(stock_item_qty is null, 0, sum(stock_item_qty)) as sold_by_month 
        from time_dimension
    left join {$table_prefix}product_stock as product_stock on stock_entry_date = db_date
    left join {$table_prefix}sales as sales on stock_sales_id = sales_id
    where year(db_date) = year(CURRENT_DATE) and sales.is_trash = 0 and sales.is_return = 0 and sales.sales_customer_id = '{$cid}' and product_stock.is_trash = 0 
    group by month(db_date)
");

$purchaseMonth = array();
$purchaseQntByMonth = array();

if($purchaseByMonth != false) {
  foreach($purchaseByMonth["data"] as $keys => $pbm) {
    array_push($purchaseMonth, $pbm["sales_month"]);
    array_push($purchaseQntByMonth, $pbm["sold_by_month"]);
  }
}


?>

<script>

  /* ToolTip */
  $(document).ready(function() {
   $('[data-toggle="tooltip"]').tooltip();
  });

  var ctx = document.getElementById('30daysOverviewChart');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: <?php echo json_encode($purchaseDate); ?>,
      datasets: [
        {
          label: "Purchase",
          borderColor: "green",
          borderWidth: 2,
          data: <?php echo json_encode($purchaseQnt); ?>
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

  var ctx = document.getElementById('soldByMonth');
  new Chart(ctx, {
    type: 'pie',
    data: {
      labels: <?php echo json_encode($purchaseMonth); ?>,
      datasets: [{
        data: <?php echo json_encode($purchaseQntByMonth); ?>,
        backgroundColor: [
            "#8b0000",
            "purple",
            "#add8e6",
            "#dacbcb",
            "green",
            "#b19cd9",
            "red",
            "#90ee90",
            "#00008b",
            "pink",
            "yellow",
            "blue"
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