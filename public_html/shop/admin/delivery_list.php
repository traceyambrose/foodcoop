<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

include_once ("general_functions.php");

if (($confirm_route == 'yes') && $member_id_passed && $basket_id_passed)
  {
    $sqlu = '
      UPDATE
        '.TABLE_BASKET_ALL.'
      SET
        rte_confirmed = "1",
        subtotal = "'.$subtotal.'"
      WHERE
        member_id = "'.$member_id_passed.'"
        AND basket_id = "'.$basket_id_passed.'"
        AND delivery_id = "'.$current_delivery_id.'"';
    $resultu = @mysql_query($sqlu,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
  }
$sqlr = '
  SELECT
    '.TABLE_ROUTE.'.*,
    '.TABLE_DELCODE.'.delcode_id,
    '.TABLE_DELCODE.'.delcode,
    '.TABLE_DELCODE.'.deldesc,
    '.TABLE_DELCODE.'.route_id,
    '.TABLE_DELCODE.'.truck_code
  FROM
    '.TABLE_ROUTE.',
    '.TABLE_DELCODE.'
  WHERE
    '.TABLE_ROUTE.'.route_id = "'.$route_id.'"
    AND '.TABLE_DELCODE.'.route_id = '.TABLE_ROUTE.'.route_id
    AND '.TABLE_DELCODE.'.delcode_id = "'.$delcode_id.'"
  ORDER BY
    route_name ASC';
$rsr = @mysql_query($sqlr,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
while ( $row = mysql_fetch_array($rsr) )
  {
    $route_name = $row['route_name'];
    $rtemgr_member_id = $row['rtemgr_member_id'];
    $rtemgr_namecd   = $row['rtemgr_namecd'];
    $route_desc  = $row['route_desc'];
    $delcode_id = $row['delcode_id'];
    $delcode = $row['delcode'];
    $deldesc = $row['deldesc'];
    $truck_code = $row['truck_code'];
    $hub = $row['hub_id'];
  }
$sql = '
  SELECT
    '.TABLE_BASKET_ALL.'.rte_confirmed,
    '.TABLE_BASKET_ALL.'.finalized,
    '.TABLE_BASKET_ALL.'.deltype as ddeltype,
    '.TABLE_BASKET_ALL.'.basket_id,
    '.TABLE_MEMBER.'.member_id,
    last_name,
    first_name,
    first_name_2,
    last_name_2,
    business_name,
    home_phone,
    work_phone,
    mobile_phone,
    fax,
    email_address,
    email_address_2,
    address_line1,
    address_line2,
    city,
    state,
    zip,
    work_address_line1,
    work_address_line2,
    work_city,
    work_state,
    work_zip
  FROM
    '.TABLE_BASKET_ALL.',
    '.TABLE_BASKET.',
    '.TABLE_MEMBER.'
  WHERE
    '.TABLE_BASKET_ALL.'.basket_id = '.TABLE_BASKET.'.basket_id
    AND
      (
        '.TABLE_BASKET_ALL.'.delivery_id = "'.$current_delivery_id.'"
        OR '.TABLE_BASKET.'.future_delivery_id ="'.$current_delivery_id.'"
      )
    AND '.TABLE_BASKET_ALL.'.member_id = '.TABLE_MEMBER.'.member_id
    AND '.TABLE_BASKET_ALL.'.delcode_id = "'.$delcode_id.'"
    AND '.TABLE_BASKET.'.out_of_stock != "1"
    AND '.TABLE_BASKET.'.product_id != "1279"
    AND '.TABLE_BASKET.'.product_id != "1696"
    AND '.TABLE_BASKET.'.product_id != "2823"
    AND '.TABLE_BASKET.'.product_id != "1403"
    AND '.TABLE_BASKET.'.product_id != "1363"
  GROUP BY
    '.TABLE_BASKET_ALL.'.basket_id
  ORDER BY
    last_name ASC';
$rs = @mysql_query($sql,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
$num_orders = mysql_numrows($rs);
while ( $row = mysql_fetch_array($rs) )
  {
    $basket_id = $row['basket_id'];
    $member_id = $row['member_id'];
    $last_name = $row['last_name'];
    $first_name = $row['first_name'];
    $business_name = stripslashes ($row['business_name']);
    $storage_code = 'ALL'; // Storage code isn't available so set to some value for convert_route_code function.

    $first_name_2 = $row['first_name_2'];
    $last_name_2 = $row['last_name_2'];
    //$hub = $row['hub'];
    //$deltype = $row['deltype'];
    $ddeltype = $row['ddeltype'];
    //$truck_code = $row['truck_code'];
    $rte_confirmed = $row['rte_confirmed'];
    $finalized = $row['finalized'];
    $home_phone = $row['home_phone'];
    $work_phone = $row['work_phone'];
    $mobile_phone = $row['mobile_phone'];
    $fax = $row['fax'];
    $email_address = $row['email_address'];
    $email_address_2 = $row['email_address_2'];
    //$product_quantity_of_member = $row['sum_pm'];
    $address_line1 = $row['address_line1'];
    $address_line2 = $row['address_line2'];
    $city = $row['city'];
    $state = $row['state'];
    $zip = $row['zip'];
    $work_address_line1 = $row['work_address_line1'];
    $work_address_line2 = $row['work_address_line2'];
    $work_city = $row['work_city'];
    $work_state = $row['work_state'];
    $work_zip = $row['work_zip'];
    include("../func/show_name.php");
    //include("../func/subtotal.php");
    $display .= '
      <table cellpadding=0 cellspacing=0 border=0><tr><td width="400" valign=top>
      <a name="'.$member_id.'">';
    $display .= '
      <li> <b>'.(convert_route_code($basket_id, $member_id, $last_name, $first_name, $business_name, $a_business_name, $hub, $delcode_id, $deltype, $truck_code, $storage_code, $show_mem, $show_mem2, $product_name, $quantity, $ordering_unit, $product_id, $item_price, $delcode)).'</b><br>
      <a href="customer_invoice.php?delivery_id='.$current_delivery_id.'&basket_id='.$basket_id.'&member_id='.$member_id.'">
      <b>'.$show_name.' (Mem#'.$member_id.')</b></a><!-- - Deliverable Products: '.$product_quantity_of_member.' -->';
    $display .= '   <ul>';
    if ( $ddeltype == 'W' )
      {
        $display .= 'Work address:<br>';
        if ( $work_address_line1 )
          {
            $display .= $work_address_line1.'<br>';
          }
        else
          {
            $display .= 'No work address available<br>';
          }
        if ( $work_address_line2 )
          {
            $display .= $work_address_line2.'<br>';
          }
        if ( $work_city || $work_state || $work_zip )
          {
            $display .= "$work_city, $work_state, $work_zip<br>";
          }
      }
    else
      {
        $display .= 'Home address:<br>';
        $display .= $address_line1.'<br>';
        if ( $address_line2 )
          {
            $display .= $address_line2.'<br>';
          }
        $display .= "$city, $state, $zip<br>";
      }
    $display .= "Email: $email_address <br>";
    if ( $email_address_2 )
      {
        $display .= "Email2: $email_address_2 <br>";
      }
    if ( $home_phone )
      {
        $display .= "Home: $home_phone <br>";
      }
    if ( $work_phone )
      {
        $display .= "Work: $work_phone <br>";
      }
    if ( $mobile_phone )
      {
        $display .= "Cell: $mobile_phone <br>";
      }
    if ( $fax )
      {
        $display .= "Fax: $fax<br>";
      }
    $display .= '   </ul><br>';
    $display .= '</td><td valign="middle">';
    if ( $rte_confirmed == 1 )
      {
        $display .= '<b>Route Confirmed</b>';
      }
    else
      {
        $display .= '
          <form action="'.$PHP_SELF.'#'.$member_id.'" method="post">
          <input type="hidden" name="member_id_passed" value="'.$member_id.'">
          <input type="hidden" name="basket_id_passed" value="'.$basket_id.'">
          <input type="hidden" name="subtotal" value="'.$total.'">
          <input type="hidden" name="route_id" value="'.$route_id.'">
          <input type="hidden" name="delcode_id" value="'.$delcode_id.'">
          <input type="hidden" name="confirm_route" value="yes">
          <input type="submit" name="where" value="Confirm '.$first_name.'&#146;s route info">
          </form>
          <br /><a href="delivery_change.php?member_id='.$member_id.'&basket_id='.$basket_id.'">Change this delivery/pick-up</a>';
      }
    $display .= '
          </td>
        </tr>
      </table>';
  }
$quantity_all = 0;
$sql_sum8 = '
  SELECT
    '.TABLE_BASKET_ALL.'.delivery_id,
    '.TABLE_BASKET_ALL.'.basket_id,
    '.TABLE_BASKET.'.basket_id,
    '.TABLE_BASKET_ALL.'.delcode_id,
    '.TABLE_BASKET.'.product_id,
    '.TABLE_PRODUCT.'.product_id,
    '.TABLE_PRODUCT.'.product_name,
    SUM('.TABLE_BASKET.'.quantity) AS sum_p,
    '.TABLE_PRODUCT.'.ordering_unit,
    '.TABLE_BASKET.'.out_of_stock,
    '.TABLE_BASKET.'.product_id,
    '.TABLE_BASKET.'.future_delivery_id
  FROM
    '.TABLE_BASKET_ALL.',
    '.TABLE_BASKET.',
    '.TABLE_PRODUCT.'
  WHERE
    '.TABLE_BASKET_ALL.'.basket_id = '.TABLE_BASKET.'.basket_id
    AND
      (
        '.TABLE_BASKET_ALL.'.delivery_id = "'.$current_delivery_id.'"
        OR '.TABLE_BASKET.'.future_delivery_id ="'.$current_delivery_id.'"
      )
    AND '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id
    AND '.TABLE_BASKET_ALL.'.delcode_id = "'.$delcode_id.'"
    AND '.TABLE_BASKET.'.product_id != "1279"
    AND '.TABLE_BASKET.'.product_id != "1696"
    AND '.TABLE_BASKET.'.product_id != "2823"
    AND '.TABLE_BASKET.'.product_id != "1403"
    AND '.TABLE_BASKET.'.product_id != "1363"
    AND '.TABLE_BASKET.'.out_of_stock != "1"
    AND
      (
        '.TABLE_BASKET.'.future_delivery_id ="'.$current_delivery_id.'"
        OR '.TABLE_BASKET.'.future_delivery_id ="0"
      )
  GROUP BY
    '.TABLE_PRODUCT.'.product_id
  ORDER BY
    sum_p DESC';
$result_sum8 = @mysql_query($sql_sum8,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
while ( $row = mysql_fetch_array($result_sum8) )
  {
    $product_id = $row['product_id'];
    $product_name = $row['product_name'];
    $product_quantity = $row['sum_p'];
    $ordering_unit = $row['ordering_unit'];
    $display_p .= "
      <tr><td align=\"right\">$product_quantity</td>
      <td align=\"left\">$ordering_unit </td><td>&nbsp; $product_name (# $product_id)</td></tr>";
    $quantity_all += $row['sum_p'];
  }

include("template_hdr.php");
?>
<!-- CONTENT BEGINS HERE -->
<div align="center">
<table width="80%" bgcolor="#FFFFFF" cellspacing="2" cellpadding="2" border="0">
  <tr>
    <td align="left">
      <h3>Route List: <?php  echo $current_delivery_date;?></h3>
    </td>
  </tr>
  <tr>
    <td align="left" bgcolor="#DDDDDD">
      <b>Route: <?php echo $route_name;?></b><br>
      <?php echo $route_desc;?><br><br></td>
  </tr>
  <tr>
    <td align="left" bgcolor="#DDDDDD">
      <b>Delivery Specifics: <?php echo $delcode;?> (Hub: <?php echo $hub;?>)</b><br>
      <?php echo $deldesc;?><br><br>
    </td>
  </tr>
  <tr>
    <td align="left">
      The following information is based on preliminary information since we are still waiting for producer confirmations of products.<br><br>
      <b>Members on this Route (<?php echo $num_orders;?> Orders)</b>
      <ul>
        <?php echo $display;?>
      </ul><br>
      <b><?php echo $quantity_all;?> Products on this Route</b>
      <blockquote>
        <table>
          <?php echo $display_p;?>
        </table>
      </blockquote><br>
    </td>
  </tr>
</table>
</div>
  <!-- CONTENT ENDS HERE -->
<br><br>

<?php include("template_footer.php"); ?>
