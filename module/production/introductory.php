<?php 
/**
 * Module Name: Production
 * Module URI: 
 * Description: Product and raw matirials requirements with order facilinites
 * Version: 0.1.0
 * Developer: Khadim Md. Khurshid Alam
 * Developer URI: https://kmk.alam.dev
 */


// Declaring the module menu
// Production production_menu
$production_menu["Production"] = array (
    "t_link"    => "#",
    "title"     => "",
    "t_icon"    => "fa fa-industry"
);
$production_menu["Production"]["New Order"] = array (
    "t_link"    => full_website_address() . "/production/new-order/",
    "title"     => "New Order",
    "t_icon"    => "fa fa-plus",
    "__?"       => current_user_can("production_order.View || production_order.Add || production_order.Edit || production_order.Delete")
);
$production_menu["Production"]["Order List"] = array (
    "t_link"    => full_website_address() . "/production/order-list/",
    "title"     => "Order List",
    "t_icon"    => "fa fa-plus",
    "__?"       => current_user_can("production_order.View || production_order.Add || production_order.Edit || production_order.Delete")
);
$production_menu["Production"]["Product Requirments"] = array (
    "t_link"    => full_website_address() . "/production/product-requirements/",
    "title"     => "Product Requirments",
    "t_icon"    => "fa fa-plus",
    "__?"       => current_user_can("production_product_requrement.View")
);


// Add menu in specific position
add_menu($production_menu, 10);


// Add Permissions
add_permission(
    array(
        "production_product_requrement" => array("View"),
        "production_order" => ""
    )
);


?>