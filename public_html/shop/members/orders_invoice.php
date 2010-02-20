<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();

include('general_functions.php');

$delivery_id = $current_delivery_id;

// If the auth_type is administrator, then we will allow viewing of invoices for other members and orders
// Note these are NOT finalized invoices... these are the dynamic ones.
if(strpos ($_SESSION['auth_type'], 'administrator') !== false && $_GET['member_id'] && $_GET['delivery_id'] && $_GET['basket_id'])
  {
    // Save session values in order to put them back before we're done (MESSY because of register_globals!)
    $original_session_member_id = $_SESSION['member_id'];
    $original_session_delivery_id = $_SESSION['delivery_id'];
    $original_session_basket_id = $_SESSION['basket_id'];

    $member_id = $_GET['member_id'];
    $delivery_id = $_GET['delivery_id'];
    $basket_id = $_GET['basket_id'];

    $put_it_back = true;
  }

include("../func/gen_invoice.php");

$display_page = geninvoice($member_id, $basket_id, $delivery_id, "members");

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <title><?php echo "Invoice for member #$member_id"; ?></title>
  </head>
<body>
<?php

    echo $display_page;
    echo "<br>";

include("template_footer_orders.php");

// Restore the session variables to their original settings
if ($put_it_back === true)
  {
  $member_id = $original_session_member_id;
  $delivery_id = $original_session_delivery_id;
  $basket_id = $original_session_basket_id;
  };

?>