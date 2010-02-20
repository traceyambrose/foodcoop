<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

include("../func/show_delivery_date_all.php");
include("template_hdr.php");
?>
<!-- CONTENT BEGINS HERE -->
<div align="center">
<table width="80%">
  <tr>
    <td align="left">
      <h3>All Previous and Current Producer Invoices</h3>
      <ul>
        <?php echo $display2;?>
      </ul>
    </td>
  </tr>
</table>
</div>
<!-- CONTENT ENDS HERE -->
<?php include("template_footer.php");?>
