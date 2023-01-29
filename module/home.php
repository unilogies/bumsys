	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				<?=__("Dashboard");?>
				<small></small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">Dashboard</li>
			</ol>
		</section>

		<!-- Main content -->
		<section class="content">
			<!-- Small boxes (Stat box) -->
			<div class="row">
				<div class="col-md-4 col-sm-6 col-xs-12">
					<!-- small box -->
					<div class="small-box bg-green">
						<div class="inner">
							<h3><?php 
							$selectSoldQnt =  easySelectD("SELECT if(sum(sales_quantity) is null, 0, sum(sales_quantity)) as totalSales FROM {$table_prefix}sales where YEAR(sales_delivery_date) = YEAR(CURRENT_DATE) and is_trash = 0 group by YEAR(sales_delivery_date)");
							
							$soldQnt = 0;
							if($selectSoldQnt !== false) {
								$soldQnt = number_format($selectSoldQnt["data"][0]["totalSales"], 2);
							}
							
							echo __($soldQnt);
							
							?></h3>

							<p><?= __("This Year Product Sales"); ?></p>
						</div>
						<div class="icon">
							<i class="fa fa-shopping-bag"></i>
						</div>
						<a href="<?php echo full_website_address(); ?>/sales/pos-sale/" class="small-box-footer"><?= __("Sales List"); ?> <i class="fa fa-arrow-circle-right"></i></a>
					</div>
				</div>
				<!-- ./col -->
				<div class="col-md-4 col-sm-6 col-xs-12">
					<!-- small box -->
					<div class="small-box bg-aqua">
						<div class="inner">
						<h3><?php echo __(easySelectD("SELECT count(DISTINCT sales_customer_id) as totalCustomer FROM {$table_prefix}sales")["data"][0]["totalCustomer"]); ?></h3>

							<p><?= __("Total Customers"); ?></p>
						</div>
						<div class="icon">
							<i class="fa fa-user"></i>
						</div>
						<a href="<?php echo full_website_address(); ?>/peoples/customer-list/" class="small-box-footer"><?= __("Customers List"); ?> <i class="fa fa-arrow-circle-right"></i></a>
					</div>
				</div>
				<!-- ./col -->
				<div class="col-md-4 col-sm-6 col-xs-12">
					<!-- small box -->
					<div class="small-box bg-yellow">
						<div class="inner">
							<h3><?php echo __(easySelectD("SELECT count(product_id) as totalProduct FROM {$table_prefix}products")["data"][0]["totalProduct"]); ?></h3>

							<p><?= __("Total Products"); ?></p>
						</div>
						<div class="icon">
							<i class="fa fa-cube"></i>
						</div>
						<a href="<?php echo full_website_address(); ?>/products/product-list/" class="small-box-footer"><?= __("Products List"); ?> <i class="fa fa-arrow-circle-right"></i></a>
					</div>
				</div>
				<!-- ./col -->
			</div>
			<!-- /.row -->

			<!-- Chart -->
			<div class="row">
				<div class="col-lg-12">
					<div class="box box-primary">
						<div class="box-header with-border">
							<h3 class="box-title"><?= __("Sales Overview"); ?></h3>

                            <select style="width: 200px; display: inline; float: right;" id="salesOverviewType">
                                <option value="daily">Daily</option>
                                <option selected value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>

						</div>
						<div class="box-body">
							<div class="chart">
								<canvas id="salesOverviewChart" style="height: 320px"></canvas>
							</div>
						</div>
						
					</div>
				</div>
			</div>

			<div class="row">

                <!-- Customers Need to get Attention -->
				<div class="col-sm-12 col-lg-6">
					<div class="box box-success">
						<div style="height: 40px;" class="box-header">
							<h3 class="box-title"><?= __("Customers Need to get attention"); ?></h3>
                            <select style="width: 120px; display: inline; float: right;" id="customerPurchaseIncreaseRate">
                                <option selected value="fullYear">Full Year</option>
                                <option value="tillToday">Till Today</option>
                            </select>
						</div>
						<div class="box-body">
							<table class="table table-bordered table-striped table-hover" id="customerIncreasedRateList" style="width: 100%;">
								<thead>
									<tr>
										<th class="no-sort"><?= __("Customer Name"); ?></th>
										<th class="text-right"><?= __("Previous Year"); ?></th>
										<th class="text-right"><?= __("Running Year"); ?></th>
										<th class="text-right"><?= __("Increase Rate"); ?></th>
									</tr>
								</thead>
								
								<tbody>
            
								</tbody>

							</table>
						</div>
					</div>
				</div> <!-- /.Col -->

                <!-- Alerming Customer -->
				<div class="col-sm-12 col-lg-6">
					<div class="box box-danger">
						<div style="height: 40px;" class="box-header">
							<h3 class="box-title"><?= __("Alerming Customer"); ?></h3>
                            <select style="width: 120px; display: inline; float: right;" id="customerPurchaseDecreasedRate">
                                <option selected value="fullYear">Full Year</option>
                                <option value="tillToday">Till Today</option>
                            </select>
						</div>
						<div class="box-body">
							<table class="table table-bordered table-striped table-hover" id="customerDecreasedRateList" style="width: 100%;">
								<thead>
									<tr>
										<th class="no-sort"><?= __("Customer Name"); ?></th>
										<th class="text-right"><?= __("Previous Year"); ?></th>
										<th class="text-right"><?= __("Running Year"); ?></th>
										<th class="text-right"><?= __("Descrease Rate"); ?></th>
									</tr>
								</thead>
								
								<tbody>
								</tbody>

							</table>
						</div>
					</div>
				</div> <!-- /.Col -->
            
				<!-- Top Customer of this product -->
				<div class="col-sm-12 col-lg-6">
					<div class="box box-info">
						<div class="box-header">
							<h3 class="box-title"><?= __("Top Sold Products of %d", date("Y")); ?></h3>
						</div>
						<div class="box-body">
							<table class="table table-bordered table-striped table-hover" style="width: 100%;">
								<thead>
									<tr>
										<th class="no-sort"><?= __("Product Name"); ?></th>
										<th class="text-right"><?= __("Sold Qnt"); ?></th>
									</tr>
								</thead>
								
								<tbody>
									<?php
										$topProduct = easySelectA(array(
											"table"   => "product_stock as product_stock",
											"fields"  => "product_name, product_unit, sum(stock_item_qty) as totalItemQnt",
											"join"    => array(
												"left join {$table_prefix}products on stock_product_id = product_id"
											),
											"where"   => array(
													"product_stock.is_trash = 0 and stock_type"  => 'sale',
													""
											),
											"having" => array(
												"max(stock_entry_date) >= CONCAT(YEAR(CURRENT_DATE),'-',01,'-',01) AND max(stock_entry_date) <= CONCAT(YEAR(CURRENT_DATE),'-',12,'-',31)"
											),
											"groupby" => "stock_product_id",
											"orderby" => array(
												"sum(stock_item_qty)" => "DESC"
											),
											"limit" => array(
												"start"   => 0,
												"length"  => 10
											)
										));
										$num = 1;
										if($topProduct) {
											foreach($topProduct["data"] as $key => $product) {
												echo "<tr>";
												echo  "<td>$num. {$product['product_name']}</td>";
                                                echo  "<td class='text-right'>". __(number_format($product['totalItemQnt'], 2)) . " " . __($product['product_unit']) ."</td>";
												echo "</tr>";
												$num++;
											}
										} else {
											echo "
												<tr>
													<td class='text-center' colspan='2'>No Data Found!</td>
												</tr>
											";
										}

									?>
								</tbody>

							</table>
						</div>
					</div>
				</div> <!-- /.Col -->

				<!-- Top Customer of this product -->
				<div class="col-sm-12 col-lg-6">
					<div class="box box-info">
						<div class="box-header">
							<h3 class="box-title"><?= __("Low Product Stock"); ?></h3>
						</div>
						<div class="box-body">
							<table class="table table-bordered table-striped table-hover" style="width: 100%;">
								<thead>
									<tr>
										<th class="no-sort"><?= __("Product Name"); ?></th>
										<th class="text-right"><?= __("Left Stock"); ?></th>
									</tr>
								</thead>
								
								<tbody>
								<?php

									$select_low_product = easySelectD("
                                                                    SELECT 
                                                                        pbs.vp_id as pid, 
                                                                        product_name, product_unit, 
                                                                        round(sum(base_stock_in), 2) as base_stock_in,
                                                                        base_qty
                                                                    FROM product_base_stock as pbs -- Product Base Stock
                                                                    left join {$table_prefix}products as product on product.product_id = pbs.vp_id
                                                                    where batch_id is null or date(batch_expiry_date) > curdate()
                                                                    GROUP BY pbs.vp_id
                                                                    order by base_stock_in ASC
                                                                    limit 0,10
                                                            ");

									$num = 1;
									if($select_low_product) {
										foreach($select_low_product["data"] as $key => $product) {
											echo "<tr>";
											echo  "<td>$num. {$product['product_name']}</td>";
											echo  "<td class='text-right'>". __(number_format($product['base_stock_in']/$product['base_qty'], 2)) . " " . __($product['product_unit']) ."</td>";
											echo "</tr>";
											$num++;
										}
									} else {
										echo "
											<tr>
												<td class='text-center' colspan='2'>No Data Found!</td>
											</tr>
										";
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


/** Weekly Sales */
var ctx = document.getElementById('salesOverviewChart');
var salesOverviewCharts = new Chart(ctx, {
	type: 'line',
	data: {},
	options: {
		responsive: true,
		tooltips: {
			mode: 'index',
			intersect: false,
            callbacks: {
                label: function(tooltipItem, data) {
                    return format_number(tooltipItem.yLabel)
                }
            }
		}
	}
});


/** When Document Ready Then load the chart */
$(function() {

    BMS.fn.get("salesOverviewChartData&type=weekly", function(data) {

        salesOverviewCharts.data = data;

        salesOverviewCharts.update();

    });


    /** Customer Increased Rate */
    BMS.fn.get("customerPurchaseIncreasedList&type=fullYear", function(data) {

        var dataHtml = "";
        data.forEach((item, index) => {
            dataHtml += `<tr>
                <td>${index+1}. <a title='Show More Details' href='<?php echo full_website_address(); ?>/reports/customer-report/?cid=${item['customer_id']}'>${item['customer_name']}, ${item['upazila_name']}, ${item['district_name']}</a> </td>
                <td class='text-right'>${ to_money(item["previous_year_total_purchase"]) }</td>
                <td class='text-right'>${ to_money(item["current_year_total_purchase"]) }</td>
                <td class='text-right'> <span class='description-percentage text-green'><i class='fa fa-caret-up'></i> ${ format_number(item["increased_rate"]) }%</span></td></td>
                
            </tr>`;
        });

        $("#customerIncreasedRateList > tbody").hide().html(dataHtml).show("slow");

    });

    /** Customer Decreased Rate */
    BMS.fn.get("customerPurchaseDecreasedList&type=fullYear", function(data) {

        var dataHtml = "";
        data.forEach((item, index) => {
            dataHtml += `<tr>
                <td>${index+1}. <a title='Show More Details' href='<?php echo full_website_address(); ?>/reports/customer-report/?cid=${item['customer_id']}'>${item['customer_name']}, ${item['upazila_name']}, ${item['district_name']}</a> </td>
                <td class='text-right'>${ to_money(item["previous_year_total_purchase"]) }</td>
                <td class='text-right'>${ to_money(item["current_year_total_purchase"]) }</td>
                <td class='text-right'> <span class='description-percentage text-red'><i class='fa fa-caret-down'></i> ${ format_number(item["decreased_rate"]) }%</span></td></td>
                
            </tr>`;
        });

        $("#customerDecreasedRateList > tbody").hide().html(dataHtml).show("slow");

    });


});

/** Update Sales  */
$(document).on("change", "#salesOverviewType", function() {

    BMS.fn.get("salesOverviewChartData&type="+  $(this).val() , function(data) {

        salesOverviewCharts.data = data;

        salesOverviewCharts.update();

    });

});

$(document).on("change", "#customerPurchaseIncreaseRate", function() {

    /** Customer Increased Rate */
    BMS.fn.get("customerPurchaseIncreasedList&type="+ $(this).val() , function(data) {

        var dataHtml = "";
        data.forEach((item, index) => {
            dataHtml += `<tr>
                <td>${index+1}. <a title='Show More Details' href='<?php echo full_website_address(); ?>/reports/customer-report/?cid=${item['customer_id']}'>${item['customer_name']}, ${item['upazila_name']}, ${item['district_name']}</a> </td>
                <td class='text-right'>${ to_money(item["previous_year_total_purchase"]) }</td>
                <td class='text-right'>${ to_money(item["current_year_total_purchase"]) }</td>
                <td class='text-right'> <span class='description-percentage text-green'><i class='fa fa-caret-up'></i> ${ format_number(item["increased_rate"]) }%</span></td></td>
                
            </tr>`;
        });

        $("#customerIncreasedRateList > tbody").hide().html(dataHtml).show("slow");

    });

});


$(document).on("change", "#customerPurchaseDecreasedRate", function() {

    /** Customer Decreased Rate */
    BMS.fn.get("customerPurchaseDecreasedList&type="+ $(this).val() , function(data) {

        var dataHtml = "";
        data.forEach((item, index) => {
            dataHtml += `<tr>
                <td>${index+1}. <a title='Show More Details' href='<?php echo full_website_address(); ?>/reports/customer-report/?cid=${item['customer_id']}'>${item['customer_name']}, ${item['upazila_name']}, ${item['district_name']}</a> </td>
                <td class='text-right'>${ to_money(item["previous_year_total_purchase"]) }</td>
                <td class='text-right'>${ to_money(item["current_year_total_purchase"]) }</td>
                <td class='text-right'> <span class='description-percentage text-red'><i class='fa fa-caret-down'></i> ${ format_number(item["decreased_rate"]) }%</span></td></td>
                
            </tr>`;
        });

        $("#customerDecreasedRateList > tbody").hide().html(dataHtml).show("slow");

    });

});


</script>
