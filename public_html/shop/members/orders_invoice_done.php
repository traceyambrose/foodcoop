<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();

$sql = '
  SELECT
    member_id,
    delivery_id,
    invoice_content
  FROM
    '.TABLE_BASKET_ALL.'
  WHERE
    member_id = '.$member_id.'
    AND delivery_id = '.$delivery_id;
$rs = @mysql_query($sql,$connection) or die("Couldn't execute query.");
$num = mysql_numrows($rs);
while ( $row = mysql_fetch_array($rs) )
  {
    $invoice_content = $row['invoice_content'];
  }
?>

<html>
<body bgcolor="#FFFFFF">
<font face="arial" size="-1">


  <!-- CONTENT BEGINS HERE -->

<?php echo $invoice_content;?>

  <!-- CONTENT ENDS HERE -->

<?php include("template_footer_orders.php");?>