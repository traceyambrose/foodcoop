<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();

if (! isset ($basket_id))
  {
    $basket_id = 0;
  };

$sql = '
  SELECT
    '.TABLE_BASKET_ALL.'.delivery_id,
    '.TABLE_DELDATE.'.*
  FROM
    '.TABLE_BASKET_ALL.',
    '.TABLE_DELDATE.'
  WHERE
    '.TABLE_BASKET_ALL.'.member_id = '.$member_id.'
    AND '.TABLE_BASKET_ALL.'.delivery_id = '.TABLE_DELDATE.'.delivery_id
    AND '.TABLE_BASKET_ALL.'.basket_id != '.$basket_id.'
  ORDER BY
    '.TABLE_BASKET_ALL.'.delivery_id DESC';
$rs = @mysql_query($sql,$connection) or die("Couldn't execute query -d.");
while ( $row = mysql_fetch_array($rs) )
  {
    $delivery_id = $row['delivery_id'];
    $delivery_date = $row['delivery_date'];

    include("../func/convert_delivery_date.php");

    $display .="<li> <a href=\"orders_invoice_done.php?delivery_id=$delivery_id\">$delivery_date</a><br>";
  }
include("template_hdr_orders.php");
?>
<div align="center">
  <table width="60%">
    <tr>
      <td align="left">

        <h3>Previous Orders for <?php echo $show_name;?></h3>

        <ul>
        <?php echo $display;?>
        </ul>

      </td>
    </tr>
  </table>
</div>

<?php include("template_footer_orders.php");?>
</body>
</html>