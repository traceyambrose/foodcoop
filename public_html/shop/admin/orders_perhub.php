<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

$display = '
  <tr bgcolor=#EEEEEE><th>HUB</th><th>Total Orders</th><th>Total Sales</th></tr>';
$sql = mysql_query('
  SELECT
    SUM(co.grand_total) AS grand_total,
    dc.hub,
    count(co.basket_id) as total_baskets
  FROM
    '.TABLE_BASKET_ALL.' co,
    '.TABLE_DELCODE.' dc
  WHERE
    co.delcode_id = dc.delcode_id
  GROUP BY
    dc.hub
  ORDER BY
    dc.hub ASC');
$num_orders = mysql_numrows($sql);
while ( $row = mysql_fetch_array($sql) )
  {
    $display .= '
      <tr><td><b>'.$row['hub'].'</b></td>
      <td align=right>'.$row['total_baskets'].'</td><td align=right>$'.number_format($row['grand_total'],2).'</td></tr>
      ';
  }
include("template_hdr.php");?>
<div align="right">
  [ <a href="index.php">Main Page</a>
  | <a href="ctotals_onebutton.php?delivery_id=<?php echo $delivery_id;?>">Customer Totals</a>
  | <a href="logout.php">Logout</a> ]
</div>
<!-- CONTENT BEGINS HERE -->
<div align="center">
<table width="90%">
  <tr>
    <td align="left">
      <h3>Total Orders and Sales per Hub</h3>
      <table cellpadding="2" cellspacing="2" border="0">
        <?php echo $display;?>
      </table>
    </td>
  </tr>
</table>
</div>
<!-- CONTENT ENDS HERE -->
<?php include("template_footer.php");?>
</body>
</html>
