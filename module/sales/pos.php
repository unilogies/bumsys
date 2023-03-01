<script>
/* Collapse the sidebar on POS screen */
$("body").addClass("sidebar-collapse");
</script>

<link rel="stylesheet" href="<?php echo full_website_address(); ?>/assets/css/pos.min.css">

<?php 

    if( isset($_GET["edit"]) and !empty($_GET["edit"]) ) { 

        $selectSale = easySelectA(array(
            "table"     => "sales",
            "fields"    => "sales_delivery_date, sales_status, sales_customer_id, customer_name, sales_warehouse_id, round(sales_total_amount, 2) as sales_total_amount, sales_total_packets,
                            round(sales_product_discount, 2) as sales_product_discount, round(sales_discount, 2) as sales_discount, round(sales_tariff_charges, 2) as sales_tariff_charges, sales_tariff_charges_details, 
                            round(sales_shipping, 2) as sales_shipping, round(sales_adjustment, 2) as sales_adjustment, round(sales_grand_total, 2) as sales_grand_total,
                            round(sales_paid_amount, 2) as sales_paid_amount, round(sales_change, 2) as sales_change, round(sales_due, 2) as sales_due, sales_note, sales_shop_id",
            "join"      => array(
                "left join {$table_prefix}customers on customer_id = sales_customer_id"
            ),
            "where"     => array(
                "sales_id"  => $_GET["edit"]
            )
        ));

        if($selectSale !== false) {
            
            $sale = $selectSale["data"][0];
            require "edit-sale.php";

        } else {

            require "add-sale.php";

        }

    } else {

        require "add-sale.php";

    }

?>