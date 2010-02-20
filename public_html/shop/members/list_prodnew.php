<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
include_once ('general_functions.php');
session_start();
validate_user();
$new_or_changed = "new";
include("../func/new_changed_products.php");

include("template_hdr_orders.php");
?>

  <!-- CONTENT BEGINS HERE -->

<div align="center">
  <table width="80%">
    <tr><td align="left">

  <div align="center">
  <h3>New Products for this Month</h3>
  <b><font color="#770000"><?php echo $message;?></font></b>
  </div>

  <?php echo new_changed_products($new_or_changed);?>


    </td></tr>
  </table>
</div>
  <!-- CONTENT ENDS HERE -->

<?php include("template_footer_orders_edit.php");?>