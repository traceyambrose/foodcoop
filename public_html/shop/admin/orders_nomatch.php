<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

$hub = '';
$sql2 = '
  SELECT
    '.TABLE_BASKET_ALL.'.basket_id AS basket_id_big
  FROM
    '.TABLE_BASKET_ALL.'
  GROUP BY
    '.TABLE_BASKET_ALL.'.basket_id
  ORDER BY
    basket_id ASC';
$rs2 = @mysql_query($sql2,$connection) or die("<br><br>You found a bug. If there is an error listed below, please copy and paste the error into an email to <a href=\"mailto:".WEBMASTER_EMAIL."\">".WEBMASTER_EMAIL."</a><br><br><b>Error:</b> Listing customer orders " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
$num_orders2 = mysql_numrows($rs2);
while ( $row = mysql_fetch_array($rs2) )
  {
    $basket_id_big = $row['basket_id_big'];
    $basket_id_big_list .= "#$basket_id_big";
  }
$sql = '
  SELECT
    '.TABLE_BASKET.'.basket_id AS basket_id_small
  FROM
    '.TABLE_BASKET.'
  GROUP BY
    '.TABLE_BASKET.'.basket_id
  ORDER BY
    basket_id ASC';
$rs = @mysql_query($sql,$connection) or die("<br><br>You found a bug. If there is an error listed below, please copy and paste the error into an email to <a href=\"mailto:".WEBMASTER_EMAIL."\">".WEBMASTER_EMAIL."</a><br><br><b>Error:</b> Listing customer orders " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
$num_orders1 = mysql_numrows($rs);
while ( $row = mysql_fetch_array($rs) )
  {
    $basket_id_small = $row['basket_id_small'];
    $basket_id_small = "#$basket_id_small";
    $pos = strpos($basket_id_big_list,$basket_id_small);
    if ( $pos === false )
      {
        $display .= '<b>no match for '.$basket_id_small.' in basket small list</b><br> ';
      }
    else
      {
        $display .= '';
      }
  }
include("template_hdr.php");
?>
<div align="right">
  [ <a href="index.php">Main Page</a>
  | <a href="ctotals_onebutton.php?delivery_id=<?php echo $delivery_id; ?>">Customer Totals</a>
  | <a href="logout.php">Logout</a> ]
</div>
<!-- CONTENT BEGINS HERE -->
<div align="center">
<table width="90%">
  <tr>
    <td align="left">
      <h3>An admin check for an basket items with a non-matching overall basket</h3>
<?php
if ( $display )
  {
    echo $display;
  }
else
  {
    echo 'All&#146;s good, everything has a match across basket tables from items to overall.';
  }
?>
    </td>
  </tr>
</table>
</div>
<!-- CONTENT ENDS HERE -->
<?php include("template_footer.php");?>
</body>
</html>
