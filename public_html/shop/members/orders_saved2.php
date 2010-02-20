<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();

$query = '
  SELECT
    delivery_id,
    delivery_date
  FROM
    '.TABLE_DELDATE.'
  ORDER BY
    delivery_id DESC';
$sql = mysql_query($query,$connection);
while ( $row = mysql_fetch_array($sql) )
  {
    $delivery_id = $row['delivery_id'];
    $delivery_date = $row['delivery_date'];

    include("../func/convert_delivery_date.php");
    $display2 .= "<li><a href=\"orders_prdcr_cust.php?delivery_id=$delivery_id\">$delivery_date</a>";
  }
?>

<?php include("template_hdr_orders.php");?>


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
<?php include("template_footer_orders.php");?>
