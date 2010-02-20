<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

include("member_balance_function.php");
include("template_hdr.php");
echo '
  <style type="text/css">
  <!--
  table, td, th {
   border: 1px solid #CCCCCC;
  }
  </style>';
if ( $_POST['member_id'] )
  {
    $member_id = preg_replace("/[^0-9]/","",$_POST['member_id']);
  }
elseif ( $_REQUEST['m'] )
  {
    $member_id = preg_replace("/[^0-9]/","",$_REQUEST['m']);
  }
$display = getMemberBalance($member_id, $current_delivery_id, 'display');
echo $display;
include("template_footer.php");
?>
<script language="javascript">
  location.href = "#bottom";
  document.lookup_member.member_id.focus();
</script>
