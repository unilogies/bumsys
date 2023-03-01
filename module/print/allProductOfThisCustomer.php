<div class="box-body">
  <?php 

  $cid = safe_input($_GET["cid"]);

  $customerName = easySelectA(array(
      "table"  => "customers",
      "fields" => "customer_name",
      "where"  => array( "customer_id={$cid}"
      )
    )
  )["data"][0]["customer_name"];


  $getTopProduct = easySelectD("
                    select 
                        product_id, 
                        product_name, 
                        (
                            if(purchased_qnt is null, 0, sum(purchased_qnt)) -
                            if(return_qnt is null, 0, sum(return_qnt)) 
                        ) as purchased_qnt
                    from {$table_prefix}products
                    left join( SELECT
                                stock_product_id,
                                stock_sales_id,
                                sum(CASE WHEN stock_type = 'sale' then stock_item_qty end) as purchased_qnt,
                                sum(CASE WHEN stock_type = 'sale-return' then stock_item_qty end) as return_qnt
                            from {$table_prefix}product_stock
                            where is_trash = 0 and stock_type in ('sale', 'sale-return')
                            group by stock_sales_id, stock_product_id
                    ) as product_stock on stock_product_id = product_id
                    left join {$table_prefix}sales as sales on stock_sales_id = sales_id
                    where sales.is_trash = 0 and sales.is_wastage = 0 and sales.sales_customer_id = '{$cid}'
                    group by stock_product_id
                    order by purchased_qnt DESC
                ");      

  ?>

  <h2 class="text-center">Product-wise sales report of <?php echo $customerName; ?></h2>
  <p class="text-center">As on: <?php echo date("d-m-Y") ?></p>

  <table class="table table-bordered table-striped table-hover">

    <thead>
      
      <tr>
        <th class="no-sort">Product Name</th>
        <th class="text-right">Purchased Qnt</th>
      </tr>
    </thead>
    <tbody>
        <?php 
            if($getTopProduct != false) {

              foreach($getTopProduct["data"] as $key => $tc) {
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