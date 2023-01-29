
<?php 

    $date =  isset($_GET["date"]) ? safe_input($_GET["date"]) : date("Y-m-d");

   $selectSales = easySelectD("
     select customer_id, customer_name, product_id, product_code, product_name, sum(sale_item_quantity) as sale_item_quantity_sum, sum(sale_item_subtotal) as sale_item_subtotal_sum from {$table_prefeix}sale_items as sale_item
     inner join {$table_prefeix}customers on sale_item_customer_id = customer_id
     inner join {$table_prefeix}products on sale_item_product_id = product_id
     where sale_item.is_trash = 0 and date(sale_item_add_on) = '{$date}' group by sale_item_customer_id, sale_item_product_id order by sale_item_customer_id, sale_item_product_id ASC
   ");

  $customer = array();
  $book = array();

foreach($selectSales["data"] as $row){

  if(!isset($customer["{$row['customer_id']}"])){
    
    $customer["{$row['customer_id']}"] = array(
      "customer_name" => $row['customer_name'],
      "data" => array()
    );

  }

  if(!isset($book["{$row['product_id']}"])){
    
    $book["{$row['product_id']}"] = $row['product_name'];

  }

  $customer[$row['customer_id']]["data"]["{$row['product_id']}"] = $row["sale_item_quantity_sum"];

}

krsort($book);

?>

<table border = "1">

  <tr>
  <th>Customer</th>
    <?php

      foreach($book as $key => $row){
        echo "<th>$row</th>";
      }

    ?>

  </tr>

  <?php

  foreach($customer as $cus){
        echo "<tr>";

          echo "<td>{$cus["customer_name"]}</td>";

          foreach($book as $key => $row){
            
            if(isset($cus["data"][$key])){
              echo "<td>{$cus["data"][$key]}</td>";
            }else{
              echo "<td>0</td>";
            }

          }

        echo "</tr>";
      }
     
  ?>

</table>