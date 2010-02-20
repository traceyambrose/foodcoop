<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();
$date_today = date("F j, Y");

$sql = '
  SELECT
    SUM('.TABLE_BASKET_ALL.'.grand_total_coop) AS grand_total_hub,
    '.TABLE_BASKET_ALL.'.delcode_id,
    '.TABLE_DELCODE.'.delcode_id,
    '.TABLE_DELCODE.'.hub
  FROM
    '.TABLE_BASKET_ALL.',
    '.TABLE_DELCODE.'
  WHERE
    '.TABLE_BASKET_ALL.'.delcode_id = '.TABLE_DELCODE.'.delcode_id
  GROUP BY
    '.TABLE_DELCODE.'.hub
  ORDER BY
    hub ASC';
$result = @mysql_query($sql,$connection) or die("".mysql_error()."");
while ( $row = mysql_fetch_array($result) )
  {
    $grand_total_hub = $row['grand_total_hub'];
    $hub = $row['hub'];

    $display .= "<tr bgcolor='#E5E5E5'><td><b>$hub</b></td><td align='right'><b>\$".number_format($grand_total_hub,2)."</b></td><td></td></tr>";

    $overall = $overall + $grand_total_hub + 0;

    $sql2 = '
      SELECT
        SUM('.TABLE_BASKET_ALL.'.grand_total_coop) AS grand_total_coop,
        '.TABLE_BASKET_ALL.'.delcode_id,
        '.TABLE_DELCODE.'.delcode_id,
        '.TABLE_DELCODE.'.hub,
        '.TABLE_DELCODE.'.delcode,
        '.TABLE_DELCODE.'.inactive
      FROM
        '.TABLE_BASKET_ALL.',
        '.TABLE_DELCODE.'
      WHERE
        '.TABLE_BASKET_ALL.'.delcode_id = '.TABLE_DELCODE.'.delcode_id
        AND '.TABLE_DELCODE.'.hub = "'.$hub.'"
      GROUP BY
        '.TABLE_DELCODE.'.hub, '.TABLE_BASKET_ALL.'.delcode_id
      ORDER BY
        delcode ASC';
    $result2 = @mysql_query($sql2,$connection) or die("".mysql_error()."");
    while ( $row2 = mysql_fetch_array($result2) )
      {
        $grand_total_coop = $row2['grand_total_coop'];
        $delcode_id = $row2['delcode_id'];
        $delcode = $row2['delcode'];
        if ( $row2[inactive] == 1 )
          {
            $active = "N";
          }
        else
          {
            $active = "Y";
          }

        $display .= "<tr bgcolor='#F5F5F5'><td>$delcode</td>
               <td align='right'>\$".number_format($grand_total_coop,2)."</td>
               <td align='center'>$active</td></tr>";
      }
  }
?>

<?php include("template_hdr_orders.php");?>


  <!-- CONTENT BEGINS HERE -->

<div align="center">
<table width="790">
  <tr><td align="left">

<h3>Totals by Location</h3>
Includes adjustments. Does not include sales tax. It is a total of what the coop keeps after taxes.
<br>Overall: $<?php echo number_format($overall,2);?>

<table cellpadding=4>
  <tr>
  <th>Delivery Code</th>
  <th align='right'>Total</th>
  <th align='center'>Active</th>
  </tr>

<?php echo $display;?>

</table>


  </td></tr>
</table>

  </td></tr>
</table>
</div>
  <!-- CONTENT ENDS HERE -->

<?php include("template_footer_orders.php");?>