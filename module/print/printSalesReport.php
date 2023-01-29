<div class="box-body">

<?php 
    $date =  isset($_GET["date"]) ? safe_input($_GET["date"]) : date("Y-m-d");
    $selectSales = easySelectD("
            select 
                customer_id, 
                customer_name,
                product_id,
                product_name,
                round(sum(stock_item_qty), 2) as sale_item_quantity_sum, 
                sum(stock_item_subtotal) as sale_item_subtotal_sum 
        from {$table_prefix}product_stock as sale_item
        inner join {$table_prefix}products on stock_product_id = product_id
        inner join {$table_prefix}sales on stock_sales_id  = sales_id
        inner join {$table_prefix}customers on sales_customer_id = customer_id
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
                                              "productCode" => $value["product_code"],
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

       $salesTable .= "<tr><td><strong> {$customerName} </strong></td>";
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
     echo "<div class='alert alert-danger'>No sales found</div>";
   }
   

   ?>
    <h1 style="text-align: center; margin: 0;">Product Wise Sales Report</h1>
    <p style="text-align: center; margin: 0;">Date: <?php echo date("d M, Y", strtotime($date)); ?></p>
    <br/>

     <table class="table table-striped table-bordered table-header-rotated">
       <thead>
         <tr>
           <th>Customer</th>
           <?php 
            if($selectSales !== false) {
               foreach($productList as $product_id=> $product_value) {
                 echo "<th class='rotate'> <div> <span>{$product_value['productName']}<br/>{$product_value['productCode']} </span></div> </th>";
              }
            }
           ?>
           <th class="rotate"><div><span>Total</span></div></th>
         </tr>
       </thead>
       <tbody>
         <?php 

           echo $salesTable;
         
         ?>
       </tbody>
       <tfoot>
         <tr>
           <th>Total :</th>
           <?php 
            if($selectSales !== false) {
              foreach($productList as $productId2=> $productValue2) {
                echo "<th>{$productValue2['totalProductQnt']}</th>";
              }
            }
           ?>
         </tr>
       </tfoot>

     </table>
 
</div>
<!-- box body-->

<div class="no-print">
  <hr/>
  <div class="form-group">
    <button type="button" onclick="print();" class="btn btn-primary">Print</button>
  </div>
</div>