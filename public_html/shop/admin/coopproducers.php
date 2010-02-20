<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
include ("../func/prdcr_contact_info_admin.php");

$sql_count = '
  SELECT
    count(producer_id)
  FROM
    '.TABLE_PRODUCER.' WHERE
    donotlist_producer != "1"';
$result_count = @mysql_query($sql_count,$connection) or die("Couldn't execute query.");
$pid_count = mysql_result($result_count,0,"count(producer_id)");
$pid_half = ceil ($pid_count / 2);
include("template_hdr.php");
?>
  <!-- CONTENT BEGINS HERE -->
<div align="center">
<table width="70%">
  <tr>
    <td colspan="2" align="center">
      <h3>Producer Contact Info: <?php echo $pid_count;?> Producers</h3>
      Click here for <a href="<?php echo BASE_URL.PATH;?>coopproducers.php"><b>Further Details about each producer</b></a>
      <br>Contact us at <a href="mailto:<?php echo MEMBERSHIP_EMAIL; ?>"><?php echo MEMBERSHIP_EMAIL;?></a> if your contact information needs to be updated.
      <br><br>
    </td>
  </tr>
  <tr>
    <td valign="top" align="left">
      <?php echo prdcr_contact_info (0, $pid_half); ?>
    </td>
    <td valign="top" align="left">
      <?php echo prdcr_contact_info ($pid_half, $pid_count); ?>
    </td>
  </tr>
</table>
</div>
  <!-- CONTENT ENDS HERE -->
<?php include("template_footer.php");?>
