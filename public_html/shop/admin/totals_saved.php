<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

include("../func/show_delivery_date_all.php");
include("template_hdr.php");
?>
<!-- CONTENT BEGINS HERE -->
<ul>
  <table cellspacing="0" cellpadding="3" border="0">
    <tr>
      <td align="left" colspan="3">
        <h3>Previous Customer Totals</h3>
      </td>
    </tr>
    <?php echo $display_totals;?>
  </table>
</ul>
  <!-- CONTENT ENDS HERE -->
<?php include("template_footer.php"); ?>
