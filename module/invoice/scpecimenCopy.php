<?php 

// Select Specimen Copy
$selectSC = easySelectA(array(
    "table"     => "specimen_copies",
    "fields"    => "sc_id, sc_type, sc_date, emp_firstname, emp_lastname, emp_working_area, emp_contact_number",
    "join"      => array(
        "left join {$table_prefix}employees on sc_employee_id = emp_id",
    ),
    "where"     => array(
        "sc_id"     => empty($_GET["id"]) ? 0 : $_GET["id"]
    )
));

// Print error msg if there has no sales
if($selectSC["count"] < 1) {
  echo "<div class='no-print'>
          <div class='alert alert-danger'>Sorry! No specimen copy found.</div>
        </div>";
  exit();
}

$selectSCItem = easySelectA(array(
    "table"   => "product_stock",
    "fields"  => "stock_item_qty, product_name",
    "join"    => array(
      "left join {$table_prefix}products on stock_product_id = product_id"
    ),
    "where" => array(
      "is_bundle_item = 0 and stock_sc_id"  => $_GET["id"],
    )
));


$selectSC = $selectSC["data"][0];

?>

<div class="shopLogo text-center">
    <h3 style="font-weight: bold; font-size: 30px;"><?php echo get_options("companyName"); ?></h3>
    <p class="text-center">32, Bangla Bazar, Dhaka</p>
    <p></p>
</div>

<table> 
    <tbody>
    <tr>
        <td style="padding: 0" class="col-md-3">Reference No: SC/<?php echo $selectSC["sc_type"] . "/" . $selectSC["sc_id"]; ?></td>
        <td style="padding: 0" class="col-md-3 text-right">Date: <?php echo date("d/m/Y", strtotime($selectSC["sc_date"])) ?></td>
    </tr>
    <tr>
        <td colspan="2"><strong>Representative: <?php echo $selectSC["emp_firstname"] . " " . $selectSC["emp_lastname"] . ", ". $selectSC["emp_working_area"] ." (" . $selectSC["emp_contact_number"] ?>)  </strong> </td>
    </tr>
    </tbody>
</table>

<br/>

<table class="table table-striped table-condensed">
    <tbody>
        <tr>
            <td style="width: 50px;">সংখ্যা</td>
            <td>বিবরণ</td>
        </tr>
        <?php 

            foreach($selectSCItem["data"] as $key => $scItems) {

            echo "<tr>";
            echo " <td style='vertical-align: middle;'>". number_format($scItems['stock_item_qty'],0) ."</td>";
            echo " <td style='vertical-align: middle;'>{$scItems['product_name']}</td>";
            echo "</tr>";

            }

        ?>      

    </tbody>

</table>

<div style="padding: 9px; border: 1px solid #ddd; border-radius: 3px; background-color: #ddd;">

</div>

<div class="no-print">
    <hr/>
    <div class="form-group">
        <a class="btn btn-primary" href="<?php echo full_website_address(); ?>/marketing/specimen-copies/">Back</a>
        <button type="button" onclick="print();" class="btn btn-primary">Print</button>
    </div>
</div>

