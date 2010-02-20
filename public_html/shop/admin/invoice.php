<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

$sql = '
  SELECT basket_id,
    member_id,
    invoice_content
  FROM
    '.TABLE_BASKET_ALL.'
  WHERE
    member_id = "'.$member_id.'"
    AND basket_id = "'.$basket_id.'"';
$rs = @mysql_query($sql,$connection) or die("Couldn't execute query.");
$num = mysql_numrows($rs);
while ( $row = mysql_fetch_array($rs) )
  {
    $basket_id = $row['basket_id'];
    $member_id = $row['member_id'];
    $invoice_content = $row['invoice_content'];
  }
?>
<html>
<body bgcolor="#FFFFFF">
  <!-- CONTENT BEGINS HERE -->
<?php echo $invoice_content;?>
  <!-- CONTENT ENDS HERE -->
<?php include("template_footer.php");?>