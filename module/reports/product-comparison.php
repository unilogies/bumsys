<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Product Comparison
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="row">
            <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-body">

                <form action="">
                    <div class="row">

                    <?php

                        $products = false;
                        if( isset($_GET["pid"]) ) {

                            $product_id = safe_input($_GET["pid"]);
                            $products = easySelectA(array(
                                "table"     => "products",
                                "fields"    => "product_id, product_name",
                                "where" => array(
                                    "is_trash = 0 and product_id in({$product_id})"
                                )
                            ));

                        }

                        
                    ?>

                    <div class="col-md-7 form-group">
                        <label for="productSelection">Select Products</label>
                        <select name="productSelection[]" id="productSelection" class="form-control select2Ajax" closeOnSelect="false" multiple select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=productList" style="width: 100%;"  required>
                            <option value="">Select Products....</option>
                            <?php
                                if($products !== false) {
                                    foreach($products["data"] as $product) {
                                        echo "<option selected value='{$product['product_id']}'>{$product['product_name']}</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2 form-group adjustFormGroupPadding">
                        <label for="DateRange">Date Range</label>
                        <input type="text" name="DateRange" id="DateRange" class="form-control dateRangePickerPreDefined" value="<?= date("Y-01-01") . " - " . date("Y-12-31"); ?>" autoComplete="off" required>
                    </div>
                    <div class="col-md-1 form-group adjustFormGroupPadding">
                        <label for="actProductAs">Act Product as</label>
                            <select name="actProductAs" id="actProductAs" class="form-control">
                            <option value="Different">Different</option>
                            <option selected value="Same">Same</option>
                        </select>
                    </div>
                    <div class="col-md-1 form-group adjustFormGroupPadding">
                        <label for="groupBy">Group By</label>
                        <select name="groupBy" id="groupBy" class="form-control">
                            <option value="Daily">Daily</option>
                            <option selected value="Monthly">Monthly</option>
                            <option value="Yearly">Yearly</option>
                        </select>
                    </div>
                    
                    <div style="margin-top: 5px;" class="col-md-1">
                        <label for=""></label>
                        <input type="submit" value="Submit" class="form-control">
                    </div>
                    </div>
                </form>

                </div>
            </div>
            </div>
        </div>

        <!-- Chart: Sales Overview -->
        <div class="row">
            <div class="col-lg-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                <h3 class="box-title">Sales Overview</h3>
                </div>
                <div class="box-body">
                <div class="chart">
                    <canvas id="OverviewChart" style="height: 560px"></canvas>
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

    /** On Submit */
    $(document).on("submit", "form", function(event) {
        
        event.preventDefault();
        showProductComparison();

    });

    /** On load */
    $(document).ready(function() {
        showProductComparison();
    });


    function showProductComparison() {
        $.post(
                "<?php echo full_website_address(); ?>/info/?module=data&page=getProductComparison",
                {
                    productsId: $("#productSelection").val(),
                    dateRange: $("#DateRange").val(),
                    groupBy: $("#groupBy").val(),
                    actProduct: $("#actProductAs").val()
                },
                function(data, status) {

                var datasetd = [];
                $.each(data.dataset, function(key, dataset){
                    datasetd.push(dataset);
                });

                
                $("#OverviewChart").remove();
                $(".chart").append('<canvas id="OverviewChart" style="height: 560px"></canvas>');

                var ctx = document.getElementById('OverviewChart');
                var chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                    labels: data.label,
                    datasets: datasetd
                    },
                    options: {
                    responsive: true,
                    tooltips: {
                        mode: 'index',
                        intersect: false
                    }
                    }
                });

                chart.update();
                    
                },
                'json'
        );
    }
    
</script>