<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

include ("general_functions.php");
include("../func/gen_invoice.php");


// DEBUG... is this the correct default?
$use = 'admin';

if ($_POST['use'] == 'adminfinalize')
  {
    $use = 'adminfinalize';
  }

$display_page = geninvoice($member_id, $basket_id, $delivery_id, $use);
if ( $message != '' )
  {
    // Display that the invoice was finalized
    include("template_hdr.php");
    echo '<br><center><b>'.$message.'</b></center><br><br>';
  }
else
  {
    echo '<html><body bgcolor="#FFFFFF">';
    echo $display_page;
    include("template_footer.php");
  }
?>
