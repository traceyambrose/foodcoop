<?php
/* ========= Determine if order cycle is open =============*/
$order_is_open = "";
$date_today = date("F j, Y");
$sqldd1 = '
  SELECT
    *
  FROM
    '.TABLE_CURDEL.'';
$rs1 = @mysql_query($sqldd1,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
while ( $row1 = mysql_fetch_array($rs1) )
  {
    $current_delivery_id = $row1['delivery_id'];
    $delivery_date = $row1['delivery_date'];
    $closing_timestamp = $row1['closing_timestamp'];
    $date_open = strtotime ($row1['date_open']);
    $order_cycle_closed = $row1['order_cycle_closed'];
    //$open = $row['open'];
    $date_closed = strtotime ($row1['date_closed']);
  }

// Time_now is used to determine whether the order cycle is in session
// One of the following two should be uncommented for (automatic vs. manual cycling)

$time_now = time ();
// if($open==1){
//  $time_now = 0;
// } else {
//  $time_now = 99999999999999;
// }

if ($time_now > $date_open && $time_now < $date_closed) $order_is_open = true;

/* END DETERMINATION OF ORDER CYCLE */

if (SHOW_ACTUAL_PRICE)
  {
    if (strpos ($_SESSION['auth_type'], 'institution') !== false)
      {
      $coop_markup = 1 + INSTITUTION_MARKUP;
      }
    else
      {
      $coop_markup = 1 + CUSTOMER_MARKUP;
      }
  }
else
  {
    $coop_markup = 1;
  }

if ($donotlist == 3)
  {
  $wholesale_rowcolor = ' bgcolor="#eeffdd"';
  $wholesale_text = '<br><br><center style="color:#f00;letter-spacing:5px;">WHOLESALE DISCOUNTED ITEM</center>';
  }
else
  {
  $wholesale_rowcolor = '';
  $wholesale_text = '';
  }

if ( $minimum_weight == $maximum_weight )
  {
    $minmax = $minimum_weight.' '.Inflect::pluralize_if ($minimum_weight, $pricing_unit);
  }
else
  {
    $minmax = 'between '.$minimum_weight.' and '.$maximum_weight.' '.Inflect::pluralize ($pricing_unit);
  }
if ( $random_weight )
  {
    $show_weight = 'You will be billed for exact '.$meat_weight_type.' weight ('.$minmax.')';
  }
else
  {
    $show_weight = '';
  }
if ( $ordering_unit == 'unknown' )
  {
    $ordering_unit = '';
  }
else
  {
    $ordering_unit = Inflect::pluralize ($ordering_unit);
  }
if ( $extra_charge )
  {
    $extra = '<br>Extra charge: $'.number_format($extra_charge, 2).'/'.Inflect::singularize ($ordering_unit);
  }
else
  {
    $extra = '';
  }
if ( $inventory_on )
  {
    $inventory_info = $inventory.' available.';
  }
else
  {
    $inventory_info = '';
  }
$display .= '
  <tr'.$wholesale_rowcolor.'>
    <td valign="center"><a name="'.$product_id.'">';
if($order_is_open)
  {
    if ( $basket_id )
      {
        $display .= '
          <form action="'.$PHP_SELF.'#'.$product_id.'" method="post">
            <input type="hidden" name="add" value="tocart">
            <input type="hidden" name="product_id" value="'.$product_id.'">
            <input type="hidden" name="producer_id" value="'.$producer_id.'">
            <input type="hidden" name="product_id_printed" value="'.$product_id.'">
            <input type="hidden" name="product_name" value="'.$product_name.'">
            <input type="hidden" name="subcategory_id" value="'.$subcategory_id.'">
            <input type="image" name="submit" src="../grfx/addtocart.gif" width="60" height="70" border="0" alt="Submit">
          </form>';
      }
    else
      {
        $display .= $font.' <a href="index.php">Begin an order</a>.</font>';
      }
  }
else
  {
    $display .= $font.'Order is currently Closed.</font>';
  }
$display .= '</td>
    <td>'.$font .'<b>#'.$product_id.'</font></b></td>';
if ( $image_id )
  {
    $display_image = '
      <img src="getimage.php?image_id='.$image_id.'" width="100" name="img'.$image_id.'"
      onclick="javascript:img'.$image_id.'.width=300"
      onMouseOut="javascript:img'.$image_id.'.width=100" hspace="5" border="1" align="left" alt="Click to enlarge '.$product_name.'">';
  }
else
  {
    $display_image = '';
  }

$display .= '
    <td>'.$display_image.' '.$font.' <b>'.stripslashes($product_name).'</b><br>
    '.$inventory_info.' Order number of '.stripslashes(Inflect::pluralize ($ordering_unit)).'. '.$show_weight.' '.stripslashes($detailed_notes).' '.stripslashes($extra).'</font>';
if ( ($message) && ($product_id == $product_id_printed) )
  {
    $display .= '<br><br><font color="#770000">'.$message.'</font>';
  }
$display .= $wholesale_text.'</td>';

if ($show_business_link == true)
  {
    if ($business_name)
      {
        $display.= '<td>'.$font.' <a href="'.PATH.'producers/'.strtolower($producer_id).'.php">'.stripslashes($business_name).'</a></td>';
      }
    else
      {
        $display .= '<td>'.$font.' <a href="'.PATH.'producers/'.strtolower($producer_id).'.php">'.stripslashes($first_name).' '.stripslashes($last_name).'</a></td>';
      }
  }

if ( $prodtype_id != 5 )
  {
    $display .= '
    <td>'.$font.' '.$prodtype.'</font></td>';
  }
else
  {
    $display .= '
    <td>'.$font.' </font></td>';
  }

// Figure out how much markup to show
if (SHOW_ACTUAL_PRICE)
  {
    $display_unit_price = $unit_price * $coop_markup;
  }
else
  {
    $display_unit_price = $unit_price;
  }

$display .= '<td align="center">';
if ( $display_unit_price != 0 )
  {
    $display .= $font.' $'.number_format($display_unit_price, 2).'/'.$pricing_unit.'';
  }
if ( $display_unit_price != 0 && $extra_charge != 0 ) $display .= '<br>and<br>';
if ( $extra_charge != 0 )
  {
    $display .= '$'.number_format($extra_charge, 2).'/'.Inflect::singularize ($ordering_unit);
  }
$display .= '</td>
    </tr>';
