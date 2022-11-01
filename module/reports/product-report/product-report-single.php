<?php

$pid = safe_input($_GET["pid"]);

// Check the product is variable or not
$selectVariableProduct = easySelectA(array(
    "table"     => "products",
    "fields"    => "product_type, child_product as child_product_list",
    "join"      => array(
        "left join (
            SELECT 
                product_parent_id,
                group_concat(product_id) as child_product
            FROM {$table_prefeix}products
            where is_trash = 0
            group by product_parent_id
        ) as child_product on child_product.product_parent_id = product_id"
    ),
    "where"     => array(
        "product_id"    => $pid
    )
));

// Set the product filter with given product id
$product_filter = " = '{$pid}'";

// If the product is variable then change the filter
if( $selectVariableProduct !== false and $selectVariableProduct["data"][0]["product_type"] === "Variable" ) {

    $product_filter = " in({$selectVariableProduct['data'][0]['child_product_list']})";

}


$getProductDetails = easySelectA(array(
    "table"     => "products",
    "fields"    => "product_name, product_unit,
                    if(purchase_qnt is null, 0, purchase_qnt) as purchase_qnt_sum,
                    if(purchase_return_qnt is null, 0, purchase_return_qnt) as purchase_return_qnt_sum,
                    if(initial_stock_qnt is null, 0, initial_stock_qnt) as initial_stock_qnt_sum,
                    if(sale_production_stock_qnt is null, 0, sale_production_stock_qnt) as sale_production_stock_qnt_sum,
                    if(sales_qnt is null, 0, sales_qnt) as sales_qnt_sum,
                    if(wastage_sale_qnt is null, 0, wastage_sale_qnt) as wastage_sale_qnt_sum,
                    if(return_qnt is null, 0, return_qnt) as return_qnt_sum,
                    if(specimen_copy_qnt is null, 0, specimen_copy_qnt) as specimen_copy_qnt_sum,
                    if(specimen_copy_return_qnt is null, 0, specimen_copy_return_qnt) as specimen_copy_return_qnt_sum
                    ",
    "join"      => array(
        "left join (select
                    stock_product_id,
                    sum(case when stock_type = 'purchase' then stock_item_qty end ) as purchase_qnt,
                    sum(case when stock_type = 'purchase-return' then stock_item_qty end ) as purchase_return_qnt,
                    sum(case when stock_type = 'initial' then stock_item_qty end ) as initial_stock_qnt,
                    sum(case when stock_type = 'sale-production' then stock_item_qty end ) as sale_production_stock_qnt,
                    sum(case when stock_type = 'wastage-sale' then stock_item_qty end ) as wastage_sale_qnt,
                    sum(case when stock_type = 'sale' then stock_item_qty end ) as sales_qnt,
                    sum(case when stock_type = 'sale-return' then stock_item_qty end ) as return_qnt,
                    sum(case when stock_type = 'specimen-copy' then stock_item_qty end ) as specimen_copy_qnt,
                    sum(case when stock_type = 'specimen-copy-return' then stock_item_qty end ) as specimen_copy_return_qnt
                from {$table_prefeix}product_stock
                where is_trash = 0 and stock_product_id {$product_filter}
        ) as product_stock on stock_product_id {$product_filter}"
    ),
    "where"     => array(
        "product_id"    => $pid
    )
))["data"][0];


?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("Report of"); ?> <?php echo $getProductDetails["product_name"] ?>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-3 col-xs-3">
                <!-- small box -->
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3><?php echo __(number_format($getProductDetails["purchase_qnt_sum"] - $getProductDetails["purchase_return_qnt_sum"], 0)); ?> +
                            <?php echo __(number_format($getProductDetails["initial_stock_qnt_sum"], 0)); ?>
                        </h3>

                        <p><?= $getProductDetails["product_unit"]; ?> <?= __("Purchased + Initial Stock"); ?></p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-shopping-bag"></i>
                    </div>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-xs-3">
                <!-- small box -->
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3><?php echo __(number_format($getProductDetails["sales_qnt_sum"], 0)) . "+" . __(number_format($getProductDetails["specimen_copy_qnt_sum"] - $getProductDetails["specimen_copy_return_qnt_sum"], 0)); ?></h3>

                        <p><?= $getProductDetails["product_unit"]; ?> <?= __("Sold + Specimen Copy"); ?></p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-xs-3">
                <!-- small box -->
                <div class="small-box bg-orange">
                    <div class="inner">
                        <h3><?php echo __(number_format($getProductDetails["return_qnt_sum"], 0)); ?></h3>

                        <p><?= $getProductDetails["product_unit"]; ?> <?= __("Return"); ?></p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-undo"></i>
                    </div>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-xs-3">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3><?php echo __(
                                number_format(
                                    ($getProductDetails["initial_stock_qnt_sum"] + $getProductDetails["sale_production_stock_qnt_sum"] + $getProductDetails["purchase_qnt_sum"] + $getProductDetails["return_qnt_sum"] +  $getProductDetails["specimen_copy_return_qnt_sum"])
                                        - ($getProductDetails["sales_qnt_sum"] + $getProductDetails["specimen_copy_qnt_sum"] + $getProductDetails["wastage_sale_qnt_sum"] + $getProductDetails["purchase_return_qnt_sum"]),
                                    0
                                )
                            );
                            ?>
                        </h3>

                        <p><?= $getProductDetails["product_unit"]; ?> <?= __("In Stock"); ?></p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-cube"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->

        <!-- Chart: Last 30 days sales report -->
        <div class="row">
            <div class="col-lg-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= __("Last 30 days Sales Overview"); ?></h3>
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
                        <h3 class="box-title"><?= __("Top Customer"); ?></h3>
                        <div class="printButtonPosition"><a class="" target="_blank" href='<?php echo full_website_address(); ?>/print/?page=allCustomerOfThisProduct&pid=<?php echo htmlentities($_GET["pid"]); ?>'><?= __("View All"); ?></a></div>
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
                                $getTopCustomer = easySelectD(" select 
                                                                    customer_id,
                                                                    customer_name, 
                                                                    sum(stock_item_qty) as purchased_qnt 
                                                                from {$table_prefeix}customers
                                                                left join {$table_prefeix}sales on sales_customer_id = customer_id
                                                                left join {$table_prefeix}product_stock as product_stock on stock_sales_id = sales_id
                                                                where product_stock.is_trash = 0 and product_stock.stock_product_id {$product_filter} 
                                                                group by sales_customer_id 
                                                                order by purchased_qnt DESC 
                                                                LIMIT 0,5
                                                            ");

                                if ($getTopCustomer !== false) {
                                    foreach ($getTopCustomer["data"] as $key => $tc) {
                                        echo "<tr>";
                                        echo  "<td>{$tc['customer_name']}</td>";
                                        echo  "<td class='text-right'>{$tc['purchased_qnt']}</td>";
                                        echo "</tr>";
                                    }
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
                        <h3 class="box-title"><?= __("Sales By Month"); ?></h3>
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

$last30DaysProductDetails = easySelectD("
    select db_date, if(sales_quantity is null, 0, sales_quantity) as sales_quantity_sum from time_dimension 
    left join ( select 
                stock_product_id, 
                stock_entry_date, 
                sum(stock_item_qty) as sales_quantity 
            from {$table_prefeix}product_stock where is_trash = 0 and stock_type = 'sale' and stock_product_id {$product_filter} 
            group by stock_entry_date 
        ) as get_sales_data on stock_entry_date = db_date
    where db_date BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) and DATE(NOW())  
    ORDER BY db_date ASC
");

$salesData = array();
$salesQnt = array();

if ($last30DaysProductDetails !== false) {
    foreach ($last30DaysProductDetails["data"] as $key => $pd) {
        array_push($salesData, $pd["db_date"]);
        array_push($salesQnt, $pd["sales_quantity_sum"]);
    }
}

// Sales Data By Month
$salesByMonth = easySelectD("
    select 
        MONTHNAME(db_date) as sales_month, 
        if(stock_item_qty is null, 0, sum(stock_item_qty)) as sold_by_month 
    from time_dimension
    left join {$table_prefeix}product_stock as product_stock on stock_entry_date = db_date
    where product_stock.is_trash = 0 and 
    year(db_date) = year(CURRENT_DATE) and 
    stock_type = 'sale' and 
    stock_product_id {$product_filter} group by month(db_date)
");

$salesMonth = array();
$salesQntByMonth = array();

if ($salesByMonth !== false) {
    foreach ($salesByMonth["data"] as $keys => $sbm) {
        array_push($salesMonth, $sbm["sales_month"]);
        array_push($salesQntByMonth, $sbm["sold_by_month"]);
    }
}

?>

<script>
    var ctx = document.getElementById('30daysOverviewChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo __(json_encode($salesData)); ?>,
            datasets: [{
                label: "Sales",
                borderColor: "green",
                borderWidth: 2,
                data: <?php echo json_encode($salesQnt); ?>
            }]
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
            labels: <?php echo json_encode($salesMonth); ?>,
            datasets: [{
                data: <?php echo json_encode($salesQntByMonth); ?>,
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