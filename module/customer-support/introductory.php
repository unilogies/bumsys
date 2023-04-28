<?php 
/**
 * Module Name: Customer Support
 * Module URI: https://bumsys.net
 * Description: A free and open source call center software.
 * Version: 1.1.0
 * Developer: Khurshid Alam
 * Developer URI: https://kmk.alam.dev
 */


// Declaring the module menu
$menu["Customer Support"] = array (
    "t_link"    => "#",
    "title"     => "",
    "t_icon"    => "fa fa-assistive-listening-systems"
);
$menu["Customer Support"]["Dashboard"] = array (
    "t_link"    => full_website_address() . "/customer-support/dashboard/",
    "title"     => "Customer Support Dashboard",
    "t_icon"    => "fa fa-dashboard",
    "dload"     => true, // This will disable ajax loading
    "__?"       => current_user_can("customer_support_dashboard.View")
);
$menu["Customer Support"]["Call Center"] = array (
    "t_link"    => full_website_address() . "/customer-support/call-center/",
    "title"     => "Call Center Dashboard",
    "t_icon"    => "fa fa-headphones",
    "dload"     => true,
    "__?"       => current_user_can("customer_support_call_center.View")
);
$menu["Customer Support"]["Call Center Old"] = array (
    "t_link"    => full_website_address() . "/customer-support/call-center-old/",
    "title"     => "Call Center Dashboard",
    "t_icon"    => "fa fa-headphones",
    "dload"     => true,
    "__?"       => current_user_can("customer_support_call_center.View")
);
$menu["Customer Support"]["Voice Message"] = array (
    "t_link"    => full_website_address() . "/customer-support/voice-message/",
    "title"     => "Voice Brodcasting System",
    "t_icon"    => "fa fa-volume-up",
    "dload"     => true,
    "__?"       => current_user_can("customer_support_voice_message.View || customer_support_voice_message.Add || customer_support_voice_message.Edit || customer_support_voice_message.Delete")
);
$menu["Customer Support"]["Case List"] = array (
    "t_link"    => full_website_address() . "/customer-support/case-list/",
    "title"     => "Case/ Ticket List",
    "t_icon"    => "fa fa-exclamation-circle",
    "__?"       => current_user_can("customer_support_cases.View || customer_support_cases.Add || customer_support_cases.Edit || customer_support_cases.Delete")
);
$menu["Customer Support"]["My Call History"] = array (
    "t_link"    => full_website_address() . "/customer-support/my-call-history/",
    "title"     => "My Call History",
    "t_icon"    => "fa fa-headphones",
    "__?"       => current_user_can("customer_support_my_call_history.View || customer_support_my_call_history.Add || customer_support_my_call_history.Edit || customer_support_my_call_history.Delete")
);
$menu["Customer Support"]["All Call History"] = array (
    "t_link"    => full_website_address() . "/customer-support/all-call-history/",
    "title"     => "All Call History",
    "t_icon"    => "fa fa-phone",
    "__?"       => current_user_can("customer_support_all_call_history.View || customer_support_all_call_history.Add || customer_support_all_call_history.Edit || customer_support_all_call_history.Delete")
);
$menu["Customer Support"]["SMS"] = array (
    "t_link"    => full_website_address() . "/customer-support/sms-list/",
    "title"     => "SMS List",
    "t_icon"    => "fa fa-comment",
    "__?"       => current_user_can("customer_support_sms.View || customer_support_sms.Add || customer_support_sms.Edit || customer_support_sms.Delete")
);
$menu["Customer Support"]["Notes/ Feedback"] = array (
    "t_link"    => full_website_address() . "/customer-support/note-list/",
    "title"     => "Notes/ Feedback List",
    "t_icon"    => "fa fa-sticky-note",
    "__?"       => current_user_can("customer_support_note.View || customer_support_note.Add || customer_support_note.Edit || customer_support_note.Delete")
);
$menu["Customer Support"]["Representative"] = array (
    "t_link"    => full_website_address() . "/customer-support/representative-list/",
    "title"     => "Representative List",
    "t_icon"    => "fa fa-user",
    "__?"       => current_user_can("customer_support_representative.View || customer_support_representative.Add || customer_support_representative.Edit || customer_support_representative.Delete")
);


// Add menu in specific position
add_menu($menu, 7);


// Permissions
add_permission(
    array(
        "customer_support_dashboard" => array("View"),
        "customer_support_call_center" => array("View"),
        "customer_support_cases" => "",
        "customer_support_my_call_history" => "",
        "customer_support_all_call_history" => "",
        "customer_support_sms" => "",
        "customer_support_representative" => "",
        "customer_support_note" => ""
    )
)



?>