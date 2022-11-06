<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= __("Reports"); ?>
  </h1>
</section>

<?php 

$date =  isset($_GET["salesDate"]) ? safe_entities($_GET["salesDate"]) : date("Y-m-d");

?>

<!-- Main content -->
<section class="content container-fluid">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title"><?= __("Sales Reports"); ?></h3>
          <div class="printButtonPosition">
          
            <form action="">
              <input style="width: 180px;" type="text" name="salesDate" id="salesDate" value="<?php echo $date; ?>" class="col-md-1 form-control datePicker" autoComplete="off">
              <button style="margin-left: 5px;" class="dt-button buttons-print btn btn-primary" tabindex="0" aria-controls="dataTableWithAjaxExtend" type="submit" title="Print"><span> Filter</span></button>
              <a target="_blank" href="<?php echo full_website_address(); ?>/print/?autoPrint=true&page=printSalesReport&date=<?php echo $date; ?>" style="margin-left: 15px;" class="dt-button buttons-print btn btn-default" tabindex="0" aria-controls="dataTableWithAjaxExtend" type="button" title="Print"><span><i class="fa fa-print"></i> Print</span></a>
              
            </form>

          </div>
        </div>
        <!-- Box header -->
        <div class="box-body">

         <?php
            $date = safe_input($date);
            $selectSales = easySelectD("
                select customer_id, customer_name, product_id, product_name, round(sum(stock_item_qty), 2) as sale_item_quantity_sum, sum(stock_item_subtotal) as sale_item_subtotal_sum 
                from {$table_prefeix}product_stock as sale_item
                inner join {$table_prefeix}products on stock_product_id = product_id
                inner join {$table_prefeix}sales on stock_sales_id  = sales_id
                inner join {$table_prefeix}customers on sales_customer_id = customer_id
                where stock_type = 'sale' and sale_item.is_trash = 0 and date(stock_item_add_on) = '{$date}' 
                group by sales_customer_id, stock_product_id 
                order by sales_customer_id, stock_product_id ASC
            ");

            $saleQnt = array();
            $salesTable = "";
            if($selectSales !== false) {
                
              $cid = "";

              foreach($selectSales["data"] as $key => $value ) {
                  $customerList[$value["customer_id"]] = $value["customer_name"];
                  $productList[$value["product_id"]] = array (
                                              "productName" => $value["product_name"],
                                              "totalProductQnt"  => (isset($productList[$value["product_id"]]["totalProductQnt"])) ? $productList[$value["product_id"]]["totalProductQnt"] + $value["sale_item_quantity_sum"] : $value["sale_item_quantity_sum"],
                                            ); 

                  if(isset( $saleQnt[$value["customer_id"]] ) ) {

                    array_push($saleQnt[$value["customer_id"]], array(
                        "productId" => $value["product_id"],
                        "productSoldQnt" => $value["sale_item_quantity_sum"],
                        "productSoldAmount" => $value["sale_item_subtotal_sum"]
                      )
                    );

                  } else {

                      $saleQnt[$value["customer_id"]] = array(
                          0 => array (
                              "productId" => $value["product_id"],
                              "productSoldQnt" => $value["sale_item_quantity_sum"],
                              "productSoldAmount" => $value["sale_item_subtotal_sum"]
                          )
                      );
                        
                  }
                    
              }

              krsort($productList);

              foreach($customerList as $customerId=> $customerName) {

                $salesTable .= "<tr><td>{$customerName}</td>";
                $totalQnt = 0;

                foreach($productList as $productId=> $productValue) {
                
                  $foundQnt = 0;

                  foreach($saleQnt[$customerId] as $saleQntKey => $saleQntValue ) {
                    
                    if($saleQntValue['productId'] == $productId ) {

                      $foundQnt = $saleQntValue['productSoldQnt'];
                      $totalQnt += (int)$saleQntValue['productSoldQnt'];

                    } 
                    
                  }

                  $salesTable .= "<td>{$foundQnt}</td>";
                
                }
                
                $salesTable .= "<td class='bg-primary'>{$totalQnt}</td>";

                $salesTable .= "</tr>";

              }

            } else {
              echo _e("No sales found");
              
            }
            

            ?>

              <table id="dataTableNormal" class="table table-bordered table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th><?= __("Customer"); ?></th>
                    <?php 
                      if($selectSales !== false) {
                        foreach($productList as $product_id=> $product_value) {
                          echo "<th>{$product_value['productName']}</th>";
                        }
                      }
                    ?>
                    <th><?= __("Total"); ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php 

                    echo $salesTable;
                  
                  ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th><?= __("Total:"); ?></th>
                    <?php 
                      if($selectSales !== false) {
                        foreach($productList as $productId2=> $productValue2) {
                          echo "<th>{$productValue2['totalProductQnt']}</th>";
                        }
                      }
                    ?>
                    <th><?= __("Total"); ?></th>
                  </tr>
                </tfoot>

              </table>

          
        </div>
        <!-- box body-->
      </div>
      <!-- box -->
    </div>
    <!-- col-xs-12-->
  </div>
  <!-- row-->

  <script>

    $(function() {
      
      $("#dataTableNormal").DataTable({
        "responsive": true,
        "scrollX": true,
        "scrollY": "50vh",
        "aLengthMenu": [  
          [15, 25, 50, 100, -1],
          [15, 25, 50, 100, "All"]
        ],
        "iDisplayLength": -1
      });

    });
      
  </script>

</section> <!-- Main content End tag -->
<!-- /.content -->
</div>
<!-- /.content-wrapper -->