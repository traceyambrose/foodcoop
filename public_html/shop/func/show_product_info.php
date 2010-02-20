<?php
if ( $random_weight )
  {
    $show_weight = '<br><font color="#3333ff">(Producer will need to enter weight.)</font>';
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
    $ordering_unit = 'Order number of '.Inflect::pluralize ($ordering_unit).'.';
  }
if ( $extra_charge )
  {
    $extra = '<br>Extra charge: $'.number_format($extra_charge, 2).'/'.$ordering_unit;
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
  <tr>
    <td>'.$font.' <b>#'.$product_id.'</b></td>
    <td>'.$font.' <b>'.stripslashes($product_name).'</b><br>'.$inventory_info.' '.$ordering_unit.' '.stripslashes($detailed_notes).' '.$extra.'</font></td>';
if ( $prodtype_id != 5 )
  {
    $display .= '
    <td>'.$font.' '.$prodtype.'</font></td>';
  }
else
  {
    $display .= '
    <td>'.$font.' <font size="-1"></font></td>';
  }
$display .= '
    <td>'.$font.' $'.number_format($unit_price, 2).'/'.$pricing_unit.'</td>
  </tr>';