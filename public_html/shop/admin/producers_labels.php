<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();
include("../func/prdcr_labels_admin.php");

$sql = '
  SELECT
    COUNT(producer_id) AS count
  FROM
    '.TABLE_PRODUCER.'
  WHERE
    donotlist_producer != "1"';
$result = mysql_query($sql) or die("Couldn't execute query.");
$row = mysql_fetch_array($result);
$pid_count = $row['count'];
$pid_half = ceil($pid_count/2);
?>
<?php include("template_hdr.php");?>
<!-- CONTENT BEGINS HERE -->
<div align="center">
<table width="70%" cellspacing="15" cellpadding="1">
  <tr>
    <td colspan="3" align="center">
      <h3>Producer Contact Info for Mailing Labels: <?php echo $pid_count;?> Producers</h3>
    </td>
  </tr>
  <tr>
    <td valign="top" align="left" width="50%"><?php echo prdcr_contact_info(0, $pid_half); ?></td>
    <td bgcolor="#000000" width="2"></td>
    <td valign="top" align="left" width="50%"><?php echo prdcr_contact_info($pid_half, $pid_count); ?></td>
  </tr>
</table>
</div>
  <!-- CONTENT ENDS HERE -->
<?php include("template_footer.php");?>
