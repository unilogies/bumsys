<?php 
/**
 * Module Name: Marketing
 * Module URI: 
 * Description: A free and open source software for doing marketing tasks.
 * Version: 0.1.0
 * Developer: Khurshid Alam
 * Developer URI: https://kmk.alam.dev
 */


// Declaring the module menu
$marketing_menu["Marketing"] = array (
    "t_link"    => "#",
    "title"     => "",
    "t_icon"    => "fa fa-bullhorn"
);
$marketing_menu["Marketing"]["Lead Statistics"] = array (
    "t_link"    => full_website_address() . "/marketing/lead-statistics/",
    "title"     => "Lead Statistics",
    "t_icon"    => "fa fa-bar-chart",
    "__?"       => current_user_can("specimen_copy_overview.View")
);
$marketing_menu["Marketing"]["Overview"] = array (
    "t_link"    => full_website_address() . "/marketing/overview/",
    "title"     => "Marketing Overview",
    "t_icon"    => "fa fa-dashboard",
    "__?"       => current_user_can("specimen_copy_overview.View")
);
$marketing_menu["Marketing"]["Specimen Copies"] = array (
    "t_link"    => full_website_address() . "/marketing/specimen-copies/",
    "title"     => "Specimen Copies",
    "t_icon"    => "fa fa-gift",
    "__?"       => current_user_can("specimen_copy.View || specimen_copy.Add || specimen_copy.Edit || specimen_copy.Delete")
);
$marketing_menu["Marketing"]["Add Specimen Copy"] = array (
    "t_link"    => full_website_address() . "/marketing/add-specimen-copy/",
    "title"     => "Add Specimen Copy",
    "t_icon"    => "fa fa-plus",
    "__?"       => current_user_can("specimen_copy.Add") and is_biller()
);
$marketing_menu["Marketing"]["Specimen Copy Distributions"] = array (
    "t_link"    => full_website_address() . "/marketing/specimen-copy-distributions/",
    "title"     => "Specimen Copy Distributions",
    "t_icon"    => "fa fa-truck",
    "__?"       => current_user_can("specimen_copy_distribution.View || specimen_copy_distribution.Add || specimen_copy_distribution.Edit || specimen_copy_distribution.Delete")
);
$marketing_menu["Marketing"]["New Distribution"] = array (
    "t_link"    => full_website_address() . "/marketing/new-sc-distribution/",
    "title"     => "Specimen Copy Distribution",
    "t_icon"    => "fa fa-plus",
    "__?"       => current_user_can("specimen_copy_distribution.Add") and is_biller()
);
$marketing_menu["Marketing"]["Persons List"] = array (
    "t_link"    => full_website_address() . "/marketing/person-list/",
    "title"     => "Person List",
    "t_icon"    => "fa fa-user",
    "__?"       => current_user_can("persons.View || persons.Add || persons.Edit || persons.Delete")
);
$marketing_menu["Marketing"]["New Person"] = array (
    "t_link"    => full_website_address() . "/xhr/?module=marketing&page=newPerson", // This will jquery popup
    "title"     => "Add New Person",
    "t_icon"    => "fa fa-user-plus",
    "t_modal"   => "#modalDefault",
    "__?"       => current_user_can("persons.Add")
);
$marketing_menu["Marketing"]["Institutes"] = array (
    "t_link"    => full_website_address() . "/marketing/institute-list/",
    "title"     => "Institute List",
    "t_icon"    => "fa fa-university",
    "__?"       => current_user_can("institutes.View || institutes.Add || institutes.Edit || institutes.Delete")
);
$marketing_menu["Hidden"]["Edit Specimen Copy"] = array (
    "t_link"    => full_website_address() . "/marketing/edit-specimen-copy/",
    "title"     => "Edit Specimen Copy",
    "t_icon"    => "fa fa-university",
    "__?"       => current_user_can("specimen_copy.Edit")
);


// Add menu in specific position
add_menu($marketing_menu, 8);


// Add Permissions
add_permission(
    array(
        "specimen_copy_overview" => array("View"),
        "specimen_copy" => "",
        "specimen_copy_distribution" => "",
        "customer_support_all_call_history" => "",
        "persons" => "",
        "institutes" => ""
    )
);


?>