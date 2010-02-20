<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

$sqlr = '
  SELECT
    '.TABLE_ROUTE.'.route_id,
    '.TABLE_ROUTE.'.route_name
  FROM
    '.TABLE_ROUTE.'
  GROUP BY
    '.TABLE_ROUTE.'.route_id
  ORDER BY
    '.TABLE_ROUTE.'.route_name ASC';
$rsr = @mysql_query($sqlr,$connection) or die(mysql_error());
while ( $row = mysql_fetch_array($rsr) )
  {
    $route_id = $row['route_id'];
    $route_name = $row['route_name'];
    $display .= '<tr><td colspan="4" bgcolor="#AEDE86">'.$font.'<b>'.$route_name.'</b></td></tr>';
    $sql = '
      SELECT
        delcode_id,
        delcode,
        route_id,
        hub
      FROM
        '.TABLE_DELCODE.'
      WHERE
        route_id = "'.$route_id.'"
      ORDER BY delcode ASC';
    $rs = @mysql_query($sql,$connection) or die(mysql_error());
    while ( $row = mysql_fetch_array($rs) )
      {
        $delcode_id = $row['delcode_id'];
        $delcode = $row['delcode'];
        $hub = $row['hub'];
        if ( $current_delcode_id < 0 )
          {
            $current_delcode_id = $row['delcode_id'];
          }
        while ( $current_delcode_id != $delcode_id )
          {
            $current_delcode_id = $delcode_id;
            $rte_confirmed_total = "";
            //$table_basket_all.*, $table_mem.member_id, $table_basket.product_id,
            //$table_basket.basket_id, $table_basket.out_of_stock, $table_basket.future_delivery_id
            $sqlo = '
              SELECT
                '.TABLE_BASKET_ALL.'.rte_confirmed,
                '.TABLE_BASKET_ALL.'.basket_id
              FROM
                '.TABLE_BASKET_ALL.',
                '.TABLE_MEMBER.',
                '.TABLE_BASKET.'
              WHERE
                '.TABLE_BASKET_ALL.'.delcode_id = "'.$delcode_id.'"
                AND '.TABLE_BASKET_ALL.'.delivery_id = '.$current_delivery_id.'
                AND '.TABLE_BASKET_ALL.'.member_id = '.TABLE_MEMBER.'.member_id
                AND '.TABLE_BASKET_ALL.'.basket_id = '.TABLE_BASKET.'.basket_id
                AND '.TABLE_BASKET.'.product_id != "1279"
                AND '.TABLE_BASKET.'.out_of_stock != "1"
                AND
                  (
                    '.TABLE_BASKET.'.future_delivery_id ="0"
                    OR '.TABLE_BASKET.'.future_delivery_id ='.$current_delivery_id.'
                  )
              GROUP BY '.TABLE_BASKET_ALL.'.basket_id';
            $rs_o = @mysql_query($sqlo,$connection) or die("Couldn't execute query -o.");
            while ( $row = mysql_fetch_array($rs_o) )
              {
                $basket_id = $row['basket_id'];
                $rte_confirmed = $row['rte_confirmed'];
                $rte_confirmed_total = $rte_confirmed_total+$rte_confirmed+0;
              }
            $num_orders = mysql_numrows($rs_o);
            if ( !$num_orders )
              {
                $num_orders = 0;
              }
            $remaining_to_confirm = $num_orders - $rte_confirmed_total;
            if ( ($num_orders == $rte_confirmed_total) && $num_orders != 0 )
              {
                $display_confirmed = '<td>All confirmed</td>';
              }
            elseif ( $num_orders == 0 )
              {
              $display_confirmed = '<td></td>';
              }
            else
              {
                $display_confirmed = '<td bgcolor="#ADB6C6">'.$remaining_to_confirm.' awaiting confirmation by route manager</td>';
              }
          }
        $display .='
          <tr>
            <td>[<a href="delivery_list.php?route_id='.$route_id.'&delcode_id='.$delcode_id.'">By Member</a>]</td>
            <td>[Hub: '.$hub.']</td>
            <td><b>'.$delcode.'</b> ('.$num_orders.' orders)</td>
            '.$display_confirmed.'
          </tr>';
        $total_orders = $total_orders + $num_orders;
      }
    $display .= '<tr><td colspan="3"><br></td></tr>';
  }
?>
<?php include("template_hdr.php");?>
<!-- CONTENT BEGINS HERE -->
<div align="center">
<table width="80%">
  <tr>
    <td align="left">
      <h3>Route Lists: Deliveries and Pickups</h3>
      <b><?php echo $total_orders;?> Total Orders for this Ordering Cycle</b><br>
      <ul>
        <li> <a href="delivery_list_all.php">List of ALL members with orders on each route</a>
      </ul>
      <b>List by Route</b><br>
      <table cellpadding="3" cellspacing="0" border="0">
        <?php echo $display;?>
      </table>
    </td>
  </tr>
</table>
</div>
  <!-- CONTENT ENDS HERE -->
<br><br>
<?php include("template_footer.php");?>
