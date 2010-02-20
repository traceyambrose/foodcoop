<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();
$date_today = date("F j, Y");
$sqlm = '
  SELECT
    '.TABLE_BASKET_ALL.'.member_id,
    '.TABLE_BASKET_ALL.'.delivery_id,
    '.TABLE_BASKET.'.customer_notes_to_producer,
    '.TABLE_BASKET.'.basket_id,
    '.TABLE_BASKET_ALL.'.basket_id,
    '.TABLE_MEMBER.'.*
  FROM
    '.TABLE_BASKET_ALL.',
    '.TABLE_MEMBER.',
    '.TABLE_BASKET.'
  WHERE
    '.TABLE_BASKET_ALL.'.member_id = '.TABLE_MEMBER.'.member_id
    AND '.TABLE_BASKET_ALL.'.basket_id = '.TABLE_BASKET.'.basket_id
    AND '.TABLE_BASKET_ALL.'.delivery_id = "'.$current_delivery_id.'"
    AND '.TABLE_BASKET.'.customer_notes_to_producer != ""
  GROUP BY
    '.TABLE_BASKET_ALL.'.member_id
  ORDER BY
    last_name ASC';
$resultm = @mysql_query($sqlm,$connection) or die("Couldn't execute query -m.");
while ( $row = mysql_fetch_array($resultm) )
  {
    $basket_id = $row['basket_id'];
    $member_id = $row['member_id'];
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $first_name_2 = $row['first_name_2'];
    $last_name_2 = $row['last_name_2'];
    $business_name = stripslashes ($row['business_name']);
    if ( $current_member_id < 0 )
      {
        $current_member_id = $row['member_id'];
      }
      while ( $current_member_id != $member_id )
        {
          $current_member_id = $member_id;
          include("../func/show_name_last.php");
          $display_basket .= '
            <tr align="left" bgcolor="#AEDE86"><td colspan="8">'.$show_name.' (Mem # '.$member_id.')</td></tr>';
          $sql = '
            SELECT
              '.TABLE_BASKET_ALL.'.*,
              '.TABLE_BASKET.'.*,
              '.TABLE_PRODUCT.'.product_name,
              '.TABLE_PRODUCT.'.pricing_unit,
              '.TABLE_PRODUCT.'.detailed_notes,
              '.TABLE_PRODUCT.'.ordering_unit,
              '.TABLE_PRODUCT.'.random_weight,
              '.TABLE_PRODUCT.'.producer_id,
              '.TABLE_PRODUCT.'.product_id,
              '.TABLE_PRODUCER.'.*,
              '.TABLE_MEMBER.'.member_id,
              '.TABLE_MEMBER.'.business_name,
              '.TABLE_MEMBER.'.first_name,
              '.TABLE_MEMBER.'.last_name,
              '.TABLE_PAY.'.*
            FROM
              '.TABLE_BASKET.',
              '.TABLE_BASKET_ALL.',
              '.TABLE_PRODUCT.',
              '.TABLE_PRODUCER.',
              '.TABLE_MEMBER.',
              '.TABLE_PAY.'
            WHERE
              '.TABLE_BASKET_ALL.'.basket_id = "'.$basket_id.'"
              AND '.TABLE_BASKET.'.basket_id = "'.$basket_id.'"
              AND '.TABLE_BASKET_ALL.'.member_id = "'.$member_id.'"
              AND '.TABLE_BASKET_ALL.'.delivery_id = "'.$current_delivery_id.'"
              AND '.TABLE_BASKET.'.customer_notes_to_producer != ""
              AND '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id
              AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
              AND '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
              AND '.TABLE_BASKET_ALL.'.payment_method = '.TABLE_PAY.'.payment_method
            ORDER BY
              business_name ASC,
              last_name ASC';
          $result = @mysql_query($sql,$connection) or die("Couldn't execute query 1.");
          while ( $row = mysql_fetch_array($result) )
            {
              $product_id = $row['product_id'];
              $producer_id = $row['producer_id'];
              $member_id_product = $row['member_id'];
              $a_business_name = stripslashes ($row['business_name']);
              $a_first_name = $row['first_name'];
              $a_last_name = $row['last_name'];
              $product_name = $row['product_name'];
              $item_price = $row['item_price'];
              $pricing_unit = $row['pricing_unit'];
              $detailed_notes = stripslashes ($row['detailed_notes']);
              $quantity = $row['quantity'];
              $ordering_unit = $row['ordering_unit'];
              $out_of_stock = $row['out_of_stock'];
              $random_weight = $row['random_weight'];
              $total_weight = $row['total_weight'];
              $extra_charge = $row['extra_charge'];
              $notes = stripslashes($row['customer_notes_to_producer']);
              $transcharge = $row['transcharge'];
              $delivery_id = $row['delivery_id'];
              $delivery_date = $row['delivery_date'];
              $payment_method = $row['payment_method'];
              $payment_desc = $row['payment_desc'];
              if ( $out_of_stock != 1 )
                {
                  if ( $random_weight == 1 )
                    {
                      if ( $total_weight == 0 )
                        {
                          $display_weight = '<font color="#770000">weight to be added</font>';
                          $message_incomplete = '<font color="#770000">Order Incomplete</font>';
                        }
                      else
                        {
                          $display_weight = $total_weight;
                        }
                      $item_total_3dec = number_format((($item_price * $total_weight) + ($quantity * $extra_charge)), 3) + 0.00000001;
                      $item_total_price = round($item_total_3dec, 2);
                    }
                  else
                    {
                      $display_weight = '';
                      $item_total_3dec = number_format((($item_price * $quantity) + ($quantity * $extra_charge)), 3) + 0.00000001;
                      $item_total_price = round($item_total_3dec, 2);
                    }
                }
              else
                {
                  $display_weight = '';
                  $item_total_price = 0;
                }
              if ( $out_of_stock )
                {
                  $display_outofstock = '<img src="grfx/checkmark_wht.gif"><br>';
                }
              else
                {
                  $display_outofstock = '';
                }
              if ( $quantity > 1 )
                {
                  //$display_ordering_unit = $ordering_unit.'s';
                  $display_ordering_unit = $ordering_unit;
                }
              else
                {
                  $display_ordering_unit = $ordering_unit;
                }
              if ( $total_weight > 1 )
                {
                  //$display_pricing_unit = $pricing_unit.'s';
                  $display_pricing_unit = $pricing_unit;
                }
              elseif ( $total_weight == 1 )
                {
                  $display_pricing_unit = $pricing_unit;
                }
              else
                {
                  $display_pricing_unit = '';
                }
              if ( $extra_charge )
                {
                  $display_charge = '$'.number_format($extra_charge, 2);
                }
              else
                {
                  $display_charge = '';
                }
              if ( $item_total_price )
                {
              $total = $item_total_price + $total;
            }
          $total_pr = $total_pr + $quantity;
          $subtotal_pr = $subtotal_pr + $item_total_price;
          if ( $notes )
            {
              $display_notes = '<b>Customer note:</b> '.$notes.'';
            }
          else
            {
              $display_notes = '';
            }
          if ( $a_business_name )
            {
              $display_business_name = $a_business_name;
            }
          else
            {
              $display_business_name = $a_first_name.' '.$a_last_name;
            }
          if ( $current_producer_id < 0 )
            {
              $current_producer_id = $row['producer_id'];
            }
          while ( $current_producer_id != $producer_id )
            {
              $current_producer_id = $producer_id;
            }
          $display_basket .= '
            <tr align="left"><td></td><td>____</td><td colspan="6"><br>
            <font face="arial" color="#770000" size="-1"><b>'.$display_business_name.'</b></font></td></tr>';
          if ( $current_product_id < 0 )
            {
              $current_product_id = $row['product_id'];
            }
          while ( $current_product_id != $product_id )
            {
              $current_product_id = $product_id;
            }
          $future_delivery_id = '';
          $sqlfd = '
            SELECT
              '.TABLE_BASKET.'.basket_id,
              '.TABLE_BASKET.'.product_id,
              '.TABLE_BASKET.'.future_delivery_id,
              '.TABLE_FUTURE_DELIVERY.'.*
            FROM
              '.TABLE_BASKET.',
              '.TABLE_FUTURE_DELIVERY.'
            WHERE
              '.TABLE_BASKET.'.basket_id = "'.$basket_id.'"
              AND '.TABLE_BASKET.'.product_id = "'.$product_id.'"
              AND '.TABLE_FUTURE_DELIVERY.'.future_delivery_id = '.TABLE_BASKET.'.future_delivery_id';
          $rs = @mysql_query($sqlfd,$connection) or die("Couldn't execute query.");
          while ( $row = mysql_fetch_array($rs) )
            {
              $future_delivery_id = $row['future_delivery_id'];
              $future_delivery_dates = $row['future_delivery_dates'];
            }
          if ( $future_delivery_id )
            {
              $future = 'Delivery date: '.$future_delivery_dates.' <br>';
            }
          else
            {
              $future = '';
            }
          $display_basket .= '
            <tr align="center">
              <td align="center" valign="top"><font face="arial" size="-1">'.$display_outofstock.'</td>
              <td align="right" valign="top"><font face="arial" size="-1"><b>'.$product_id.'</b>&nbsp;&nbsp;</td>
              <td width="275" align="left" valign="top"><font face="arial" size="-1"><b>'.$product_name.'</b><br>'.$size_measurements_contents.'<br>'.$future.''.$display_notes.'</td>
              <td align="center" valign="top"><font face="arial" size="-1">$'.number_format($item_price, 2).'/'.$pricing_unit.'</td>
              <td align="center" valign="top"><font face="arial" size="-1">'.$quantity.' '.$display_ordering_unit.'</td>
              <td align="center" valign="top"><font face="arial" size="-1">'.$display_weight.' '.$display_pricing_unit.'</td>
              <td align="center" valign="top"><font face="arial" size="-1">'.$display_charge.'</td>
              <td align="right" valign="top"><font face="arial" size="-1">$'.number_format($item_total_price, 2).'</td>
            </tr>';
          }
      $display_basket .= '
        <tr align="left" bgcolor="#FFFFFF"><td colspan="8"><br><br></td></tr>';
      }
  }
$fontface='arial';
$display_page .= '
<table width="670" cellpadding="2" cellspacing="0" border="0">
  <tr>
    <td colspan="8"><hr></td>
  </tr>
  <tr>
    <th valign="bottom"><font face="'.$fontface.'" size="-1"></th>
    <th valign="bottom"><font face="'.$fontface.'" size="-1">#</th>
    <th valign="bottom" align="left"><font face="'.$fontface.'" size="-1">Product Name</th>
    <th valign="bottom"><font face="'.$fontface.'" size="-1">Price</th>
    <th valign="bottom"><font face="'.$fontface.'" size="-1">Quantity</th>
    <th valign="bottom"><font face="'.$fontface.'" size="-1">Total<br>Weight</th>
    <th valign="bottom"><font face="'.$fontface.'" size="-1">Extra<br>Charge</th>
    <th valign="bottom"><font face="'.$fontface.'" size="-1">Amount</th>
  </tr>
  <tr>
    <td colspan="8"><hr></td>
  </tr>';
$display_page .= $display_basket;
$display_page .= '
  </table>';
?>
<?php include("template_hdr.php");?>
<?php echo $display_page;?>
<?php include("template_footer.php");?>