<div class="box-body">
  <?php 

  $pid = safe_input($_GET["pid"]);

  $productName = easySelectA(array(
      "table"  => "products",
      "fields" => "product_name",
      "where"  => array("product_id={$pid}"
      )
    )
  )["data"][0]["product_name"];

    $getCustomer = easySelectD("
                  select 
                    customer_id,
                    customer_name, 
                    sum(stock_item_qty) as purchased_qnt 
                  from {$table_prefeix}customers
                  left join {$table_prefeix}sales on sales_customer_id = customer_id
                  left join {$table_prefeix}product_stock as product_stock on stock_sales_id = sales_id
                  where product_stock.is_trash = 0 and product_stock.stock_product_id = '{$pid}' group by sales_customer_id order by purchased_qnt DESC
    ");

  ?>

  <h2 class="text-center">Customer-wise sales report of <?php echo $productName; ?></h2>
  <p class="text-center">As on: <?php echo date("d-m-Y") ?></p>

  <table class="table table-bordered table-striped table-hover">

    <thead>
      
      <tr>
        <th class="no-sort">Customer Name</th>
        <th class="text-right">Purchased Qnt</th>
      </tr>
    </thead>
    <tbody>
        <?php 
            if($getCustomer !== false) {
              foreach($getCustomer["data"] as $key => $tc) {
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