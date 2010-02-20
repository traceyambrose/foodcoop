<?php
$user_type = 'valid_c';
include_once ('config_foodcoop.php');

if( $minimum_weight == $maximum_weight )
  {
    $minmax = $minimum_weight.' '.Inflect::pluralize_if ($minimum_weight, $pricing_unit);
  }
else
  {
    $minmax = $minimum_weight.' - '.$maximum_weight.' '.Inflect::pluralize ($pricing_unit);
  }
if( $random_weight )
  {
    $show_weight = 'You will be billed for exact '.$meat_weight_type.' weight (approx. '.$minmax.')';
  }
else
  {
    $show_weight = '';
  }
if( $ordering_unit == 'unknown' )
  {
    $ordering_unit = '';
  }
else
  {
    $ordering_unit = Inflect::pluralize ($ordering_unit);
  }
if( $extra_charge )
  {
    $extra = '<br>Extra charge: $'.number_format($extra_charge, 2).'/'.Inflect::singularize ($ordering_unit);
  }
else
  {
    $extra = '';
  }
if( $inventory_on )
  {
    $inventory_info = $inventory.' available.';
  }
else
  {
    $inventory_info = '';
  }
if ( $image_id )
  {
    $display_image = '
      <img src="'.PATH.'members/getimage.php?image_id='.$image_id.'" width="100" name="img'.$image_id.'"
      onClick="javascript:img'.$image_id.'.width=300"
      onMouseOut="javascript:img'.$image_id.'.width=100" hspace="5" border="1" align="left" alt="Click to enlarge '.$product_name.'">';
  }
else
  {
    $display_image = '';
  }
if ( $show_business_link == true )
  {
    if ( $business_name )
      {
        $display_producer = $font.' <a href="'.BASE_URL.PATH.'producers/'.strtolower($producer_id).'.php">'.stripslashes($business_name).'</a><br>';
      }
    else
      {
        $display_producer = $font.' <a href="'.BASE_URL.PATH.'producers/'.strtolower($producer_id).'.php">'.stripslashes($first_name).' '.stripslashes($last_name).'</a><br>';
      }
  }
else
  {
    $display_producer = '';
  }
$display .= '<tr>';
$display .= '<td valign="center"><a name="'.$product_id.'">'.$font.' <b>#'.$product_id.'</font></b></td>';
$display .= '<td>'.$display_image.' '.$font.' <b>'.stripslashes($product_name).'</b><br>';
$display .= $inventory_info.' Order number of '.stripslashes(Inflect::pluralize ($ordering_unit)).'. '.$show_weight.' '.stripslashes($detailed_notes).' '.stripslashes($extra).'</font>';
if( ($message) && ($product_id == $product_id_printed) )
  {
    $display .= '<br><br><font color="#770000">'.$message.'</font>';
  }
$display .= '</td>';
if ( $show_business_link == true )
  {
  $display .= '<td>'.$display_producer;
  }
if ( $prodtype_id != 5 )
  {
    $display .= '<td align="center"><font size="-1" color="#000000">'.$prodtype.'</font></td>';
  }
else
  {
  $display .= '<td><font size="-1" color="#FFFFFF">-</font></td>';
  }
// adjust the unit price to what we actually want to display.
if (SHOW_ACTUAL_PRICE)
  {
    $display_unit_price = $unit_price * (1 + CUSTOMER_MARKUP);
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
