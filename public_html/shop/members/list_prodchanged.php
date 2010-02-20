<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
include_once ('general_functions.php');
session_start();
validate_user();

if ( $updatelisting == "yes" )
  {
    $sqlu = '
      UPDATE
        '.TABLE_PRODUCT_PREP.'
      SET
        changed = "0"
      WHERE
        product_id = "'.$product_id_passed.'"';

    $resultu = @mysql_query($sqlu,$connection) or die("<br><br>You found a bug. If there is an error listed below,
    please copy and paste the error into an email to <a href=\"mailto:".WEBMASTER_EMAIL."\">".WEBMASTER_EMAIL."</a><br><br><br>
    <b>Error:</b> Updating " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
  }
if ( $resultu )
  {
    $message2 = "<b><font color=\"#3333FF\">SUCCESS! The information has been updated</font></b>.";
  }

include("../func/new_changed_products.php");
$new_or_changed = "changed";

include("template_hdr_orders.php");
?>
  <!-- CONTENT BEGINS HERE -->

<div align="center">
  <table width="80%">
    <tr>
      <td align="left">

        <div align="center">
        <h3>Changed Products for this Month</h3>
        <b><font color="#770000"><?php echo $message;?></font><?php echo $message2;?></b>
        </div>

        <?php echo new_changed_products($new_or_changed);?>


      </td>
    </tr>
  </table>
</div>
  <!-- CONTENT ENDS HERE -->

<?php include("template_footer_orders_edit.php");?>