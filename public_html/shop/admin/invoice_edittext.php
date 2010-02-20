<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

$message = "";
if ( $update )
  {
    $sqlu = '
      UPDATE
        '.TABLE_CURDEL.'
      SET
        msg_all = "'.$msg_all.'",
        msg_bottom = "'.$msg_bottom.'"
      WHERE
      delivery_id = "'.$current_delivery_id.'"';
    $resultu = @mysql_query($sqlu,$connection) or die("<br><br>You found a bug. If there is an error listed below, please copy and paste the error into an email to <a href=\"mailto:".WEBMASTER_EMAIL."\">".WEBMASTER_EMAIL."</a><br><br><b>Error:</b> Updating " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    $message = ": <font color=\"#FFFFFF\">Messages have been updated</font>";
  }
$sqlmsg = '
  SELECT msg_all,
    delivery_id,
    msg_bottom
  FROM
    '.TABLE_CURDEL.'
  WHERE
    delivery_id = "'.$current_delivery_id.'"';
$resultmsg = @mysql_query($sqlmsg,$connection) or die("<br><br>You found a bug. If there is an error listed below, please copy and paste the error into an email to <a href=\"mailto:".WEBMASTER_EMAIL."\">".WEBMASTER_EMAIL."</a><br><br><b>Error:</b> Selecting message " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
while ( $row = mysql_fetch_array($resultmsg) )
  {
    $msg_all = $row['msg_all'];
    $msg_bottom = $row['msg_bottom'];
  }
include("template_hdr.php");
?>
<?php echo $font;?>
<!-- CONTENT BEGINS HERE -->
<div align="center">
  <h3>Editing Text on the Invoices</h3>
  <table width="685" cellpadding="7" cellspacing="2" border="0">
    <tr bgcolor="#AE58DA">
      <td align="left"><b>Message to all Members <?php echo $message;?></b></td>
    </tr>
    <tr>
      <td align="left" bgcolor="#EEEEEE">
        <form action="<?php echo $PHP_SELF;?>" method="POST">
          <b>Appears at the top of all Customer Invoices</b><br>
          <textarea name="msg_all" cols=75 rows=7><?php echo $msg_all;?></textarea><br><br>
          <b>Appears at the bottom of all Customer Invoices</b><br>
          <textarea name="msg_bottom" cols=75 rows=7><?php echo $msg_bottom;?></textarea><br>
          <input type="hidden" name=update value=yes>
          <div align=center><input type="submit" name="submit" value="Submit"></div>
        </form>
      </td>
    </tr>
  </table>
</div>
<br><br>
  <!-- CONTENT ENDS HERE -->
<?php include("template_footer.php");?>
