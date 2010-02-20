<?php
//                                                                           //
// This script will create a table of producer orders, by product and        //
// handle forms for the updating of single products.  It requires that the   //
// following variables already be set:                                       //
//                                                                           //
//                 $current_delivery_id                                      //
//                 $producer_id_you                                          //
//                                                                           //

// Save session values in order to put them back before we're done (MESSY because of register_globals!)
$original_session_member_id = $_SESSION['member_id'];

// If we don't have a producer_id then get one from the arguments
if (! $producer_id) $producer_id = $_GET['producer_id'];
// If not administrator, then force producer to be the owner
if ( $producer_id_you && strpos ($_SESSION['auth_type'], 'administrator') === false )
  {
    $producer_id = $producer_id_you;
  }
// If no delivery id was passed, then use the current value
if ($_GET['delivery_id'])
  {
    $delivery_id = $_GET['delivery_id'];
  }
else
  {
    $delivery_id = $_SESSION['current_delivery_id'];
  }

$total_extra = 0;

    $query = '
      SELECT
        *
      FROM
        '.TABLE_DELDATE.'
      WHERE
        delivery_id = '.$delivery_id;
    $result= mysql_query("$query") or die("Error: " . mysql_error());
    while ($row = mysql_fetch_array($result))
      {
        $delivery_date = date ("M j, Y", strtotime ($row['delivery_date']));
      }

$sqlp = '
  SELECT
    '.TABLE_MEMBER.'.business_name,
    '.TABLE_MEMBER.'.email_address,
    '.TABLE_MEMBER.'.home_phone,
    '.TABLE_MEMBER.'.work_phone,
    '.TABLE_MEMBER.'.mobile_phone,
    '.TABLE_MEMBER.'.city,
    '.TABLE_MEMBER.'.state,
    '.TABLE_MEMBER.'.zip,
    '.TABLE_MEMBER.'.county,
    '.TABLE_MEMBER.'.address_line1,
    '.TABLE_MEMBER.'.address_line2,
    '.TABLE_MEMBER.'.first_name,
    '.TABLE_MEMBER.'.last_name
  FROM
    '.TABLE_PRODUCER.',
    '.TABLE_MEMBER.'
  WHERE
    '.TABLE_PRODUCER.'.producer_id = "'.$producer_id.'"
    AND '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
  GROUP BY
    '.TABLE_PRODUCER.'.producer_id
  ORDER BY
    business_name ASC,
    last_name ASC';
$resultp = @mysql_query($sqlp) or die("Couldn't execute query. ");
while ( $row = mysql_fetch_array($resultp) )
  {
    $a_business_name = stripslashes ($row['business_name']);
    $a_first_name = stripslashes ($row['first_name']);
    $a_last_name = stripslashes ($row['last_name']);
    $a_address_line1 = stripslashes($row['address_line1']);
    $a_address_line2 = stripslashes($row['address_line2']);
    $a_city = stripslashes($row['city']);
    $a_state = stripslashes($row['state']);
    $a_zip = stripslashes($row['zip']);
    $a_county = stripslashes($row['county']);
    $a_email_address = stripslashes($row['email_address']);
    $a_home_phone = stripslashes($row['home_phone']);
    $a_work_phone = stripslashes($row['work_phone']);
    $a_mobile_phone = stripslashes($row['cell_phone']);
    if (!$a_business_name)
      {
        $a_business_name = "$a_first_name $a_last_name";
      }
    if ($a_address_line1 && $a_address_line2)
      {
        $a_address = "$a_address_line1<br>\n$a_address_line2";
      }
    else
      {
        $a_address = $a_address_line1.$a_address_line2;
      }

  }
if ($display_only) // Display-only orders get a producer header
  {
    $producer_orders_bycustomerstorage = '
      <table width="100%" cellpadding="4" cellspacing="0" border="0">
        <tr>
          <td colspan="7" align="left">
            <table cellspacing="5" width="100%" border="0">
              <tr>
                <td width="50%"><h3>'.$a_business_name.'</h3></td>
                <td colspan="2" width="50%" align="right"><font size="+1"><strong>Order #'.$delivery_id.' - '.$delivery_date.'</strong></font></td>
              </tr>
              <tr>
                <td rowspan="4" width="50%" valign="top">
                  '.$a_address.'<br>
                  '.$a_city.', '.$a_state.' '.$a_zip.'<br>
                  ('.$a_county.' County)
                </td>
                <td width="15%" align="right"> Email: </td>
                <td width="35%">'.$a_email_address.'</td>
              </tr>
              <tr>
                <td width="15%" align="right">Home: </td>
                <td width="35%">'.$a_home_phone.'</td>
              </tr>
              <tr>
                <td width="15%" align="right">Work: </td>
                <td width="35%">'.$a_work_phone.'</td>
              </tr>
              <tr>
                <td width="15%" align="right">Mobile: </td>
                <td width="35%">'.$a_mobile_phone.'</td>
              </tr>
            </table>
          </td>
        </tr>';
  }
else // Editable invoices get alternative invoice options
  {
    $producer_orders_bycustomerstorage = '
      <table width="100%" cellpadding="4" cellspacing="0" border="0">
        <tr>
          <td colspan="2" align="left">
            <font size=4>Sorted by Storage/Customer</font>
            </td><td colspan="6" align="right">
            Click for invoice sorted by <a href="orders_prdcr.php?delivery_id='.$delivery_id.'&producer_id='.$producer_id.'">product</a><br>
            Click for invoice sorted by <a href="orders_prdcr_cust.php?delivery_id='.$delivery_id.'&producer_id='.$producer_id.'">customer</a><br>
            Click for invoice with <a href="orders_prdcr_multi.php?delivery_id='.$delivery_id.'&producer_id='.$producer_id.'">multi-sort/mass-update</a><br>
            Click for <a href="../func/producer_labels.php?delivery_id='.$delivery_id.'&producer_id='.$producer_id.'">labels (one per product/customer)</a><br>
            Click for <a href="../func/producer_labelsc.php?delivery_id='.$delivery_id.'&producer_id='.$producer_id.'">labels (one per storage/customer)</a>
          </td>
        </tr>';
  }
$producer_orders_bycustomerstorage .= '
        <tr bgcolor="#9CA5B5">
          <th valign="bottom" align="center">PrdID</th>
          <th valign="bottom" align="center">Product</th>
          <th valign="bottom" align="center">Quantity</th>
          <th valign="bottom" align="center">Weight</th>
          <th valign="bottom" align="center">Extra<br>Charge</th>
          <th valign="bottom" align="center">In<br>Stock?</th>
          <th valign="bottom" align="center">Item<br>Total</th>';
// Only display the Edit column if this is an editable viewing of the page
if (!$display_only)
  {
    $producer_orders_bycustomerstorage .= '
          <th valign="bottom" align="center">Edit<br>Item</th>';
  }
$producer_orders_bycustomerstorage .= '
        </tr>';

$sqlpr = '
  SELECT
    '.TABLE_BASKET.'.basket_id,
    '.TABLE_BASKET.'.product_id,
    '.TABLE_BASKET.'.item_price,
    '.TABLE_BASKET.'.quantity,
    '.TABLE_BASKET.'.random_weight,
    '.TABLE_BASKET.'.total_weight,
    '.TABLE_BASKET.'.extra_charge,
    '.TABLE_BASKET.'.out_of_stock,
    '.TABLE_BASKET.'.future_delivery_id,
    '.TABLE_BASKET.'.customer_notes_to_producer,
    '.TABLE_PRODUCT.'.product_name,
    '.TABLE_PRODUCT.'.random_weight,
    '.TABLE_PRODUCT.'.ordering_unit,
    '.TABLE_PRODUCT.'.pricing_unit,
    '.TABLE_SUBCATEGORY.'.subcategory_id,
    '.TABLE_SUBCATEGORY.'.category_id,
    '.TABLE_PRODUCT.'.subcategory_id,
    pst.storage_code,
    '.TABLE_BASKET_ALL.'.deltype as ddeltype,
    '.TABLE_MEMBER.'.member_id,
    '.TABLE_MEMBER.'.last_name,
    '.TABLE_MEMBER.'.first_name,
    '.TABLE_MEMBER.'.business_name,
    '.TABLE_MEMBER.'.email_address,
    '.TABLE_MEMBER.'.home_phone,
    '.TABLE_MEMBER.'.mem_taxexempt,
    '.TABLE_DELCODE.'.hub,
    '.TABLE_DELCODE.'.truck_code,
    '.TABLE_DELCODE.'.delcode_id,
    '.TABLE_DELCODE.'.delcode,
    '.TABLE_DELCODE.'.deltype
  FROM
    ('.TABLE_BASKET.',
    '.TABLE_PRODUCT.',
    '.TABLE_SUBCATEGORY.',
    '.TABLE_BASKET_ALL.',
    '.TABLE_MEMBER.',
    '.TABLE_ROUTE.',
    '.TABLE_DELCODE.')
  LEFT JOIN
    '.TABLE_PRODUCT_STORAGE_TYPES.' pst ON '.TABLE_PRODUCT.'.storage_id = pst.storage_id
  WHERE
    '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id
    AND '.TABLE_PRODUCT.'.producer_id = "'.$producer_id.'"
    AND '.TABLE_PRODUCT.'.subcategory_id = '.TABLE_SUBCATEGORY.'.subcategory_id
    AND '.TABLE_PRODUCT.'.hidefrominvoice ="0"
    AND '.TABLE_BASKET_ALL.'.member_id = '.TABLE_MEMBER.'.member_id
    AND ('.TABLE_BASKET_ALL.'.delivery_id = "'.$delivery_id.'"
      OR '.TABLE_BASKET.'.future_delivery_id = "'.$delivery_id.'")
    AND '.TABLE_BASKET_ALL.'.basket_id = '.TABLE_BASKET.'.basket_id
    AND '.TABLE_BASKET_ALL.'.delcode_id = '.TABLE_DELCODE.'.delcode_id
    AND '.TABLE_DELCODE.'.route_id = '.TABLE_ROUTE.'.route_id
  GROUP BY
    '.TABLE_BASKET_ALL.'.member_id,
    '.TABLE_BASKET.'.product_id
  ORDER BY
    pst.storage_code ASC,
    '.TABLE_DELCODE.'.delcode_id ASC,
    '.TABLE_BASKET_ALL.'.member_id ASC,
    '.TABLE_DELCODE.'.hub ASC,
    '.TABLE_BASKET.'.item_date ASC';

$resultpr = @mysql_query($sqlpr) or die("Couldn't execute query 1.");
while ( $row = mysql_fetch_array($resultpr) )
  {
    $display_weight = '';
    $product_name = $row['product_name'];
    $product_id = $row['product_id'];
    $basket_id = $row['basket_id'];
    $member_id = $row['member_id'];
    $last_name = $row['last_name'];
    $first_name = $row['first_name'];
    $business_name = $row['business_name'];
    $hub = $row['hub'];
    $delcode_id = $row['delcode_id'];
    $delcode = $row['delcode'];
    $deltype = $row['deltype'];
    $truck_code = $row['truck_code'];
    $storage_code = $row['storage_code'];
    $quantity = $row['quantity'];
    $ordering_unit = $row['ordering_unit'];
    $item_price = $row['item_price'];
    $email_address = $row['email_address'];
    $home_phone = $row['home_phone'];
    $ddeltype = $row['ddeltype'];
    $mem_taxexempt = $row['mem_taxexempt'];
    if ( $last_name && $first_name )
      {
        $show_mem2 = $first_name.' '.$last_name;
        $show_mem = $last_name.', '.$first_name;
      }
    else
      {
        $show_mem = $business_name;
      }
    $c_basket_id = $row['basket_id'];
    $category_id = $row['category_id'];
    $random_weight = $row['random_weight'];
    $total_weight = $row['total_weight'];
    $out_of_stock = $row['out_of_stock'];
    $extra_charge = $row['extra_charge'];
    $future_delivery_id = $row['future_delivery_id'];
    $detailed_notes = stripslashes($row['detailed_notes']);
    $notes = stripslashes($row['customer_notes_to_producer']);
    $pricing_unit = $row['pricing_unit'];

    // Throw up a header if necessary...
    if ($sc && $sc!=$row['storage_code'])
      {
        $producer_orders_bycustomerstorage .= '</table>
          <hr BREAK>
          <table width="100%" cellpadding="4" cellspacing="0" border="0">
            <tr>
              <th valign="bottom" align="center">PrdID</th>
              <th valign="bottom" align="center">Product</th>
              <th valign="bottom" align="center">Quantity</th>
              <th valign="bottom" align="center">Weight</th>
              <th valign="bottom" align="center">Extra<br>Charge</th>
              <th valign="bottom" align="center">In<br>Stock?</th>
              <th valign="bottom" align="center">Item<br>Total</th>';
        // Only display the Edit column if this is an editable viewing of the page
        if (!$display_only)
          {
            $producer_orders_bycustomerstorage .= '
              <th valign="bottom" align="center">Edit<br>Item</th>';
          }
        $producer_orders_bycustomerstorage .= '
            </tr>';
      }
    if ( $sc == $row['storage_code'] && $m == $member_id )
      {
        // Same storage code and same member so don't do anything
      }
    else
      {
        $producer_orders_bycustomerstorage .= '
          <tr bgcolor="#DDDDDD">
            <td colspan="8">
              <a name="'.$member_id.'">
              <font size="4"><font color="#770000">'.(convert_route_code($basket_id, $member_id, $last_name, $first_name, $business_name, $a_business_name, $hub, $delcode_id, $deltype, $truck_code, $storage_code, $show_mem, $show_mem2, $product_name, $quantity, $ordering_unit, $product_id, $item_price, $delcode)).'</font></font> /
              <b>Producer: '.stripslashes($a_business_name).'</b><br>
              <font size="4">Member: '.$show_mem.' Mem# '.$member_id.':</font>
              '.$home_phone.' <a href="mailto:'.$email_address.'">'.$email_address.'</a><br>
          </td>
          </tr>';
        $sc = $row['storage_code'];
        $m = $member_id;
      }
    if ( $out_of_stock == 1 )
      {
        $display_total_price = '$'.number_format(0, 2);
      }
    if ($future_delivery_id == $delivery_id)
      {
        $display_weight = '';
        $item_total_price = 0;
        $display_total_price = '<font color="#FF0000">Invoiced in a previous order</font>';
      }
    elseif ($future_delivery_id > $delivery_id)
      {
        $display_weight = '';
        $item_total_price = 0;
        $display_total_price = '<font color="#FF0000">Will be delivered in future order</font>';
      }
    elseif ( $out_of_stock != 1 )
      {
        if ( $random_weight == 1 )
          {
            if( $total_weight == 0 )
              {
                //$display_weight = "<input type=\"text\" name=\"total_weight\" value=\"$total_weight\" size=\"2\" maxlength=\"11\"> ".$pricing_unit."s";
                if (!$display_only)
                  {
                    $display_weight = '<input type="text" name="total_weight" value="0.00" size="2" maxlength="11"> '.$pricing_unit;
                  }
                else
                  {
                    $display_weight = '0.00 '.Inflect::pluralize ($pricing_unit);
                  }
                $show_update_button = 'yes';
                $item_total_3dec = ($item_price * $total_weight) + 0.00000001;
                $item_total_price = round($item_total_3dec, 2);
                $display_total_price = '$'.number_format($item_total_price, 2)."";
                $display_unit_price = $item_total_price;
                $message_incomplete = '<font color="#770000">Order Incomplete<font>';
              }
            else
              {
                //$display_weight = "<input type=\"text\" name=\"total_weight\" value=\"$total_weight\" size=\"2\" maxlength=\"11\"> ".$pricing_unit."s";
                if (!$display_only)
                  {
                    $display_weight = '<input type="text" name="total_weight" value="'.$total_weight.'" size="2" maxlength="11"> '.Inflect::pluralize ($pricing_unit);
                  }
                else
                  {
                    $display_weight = $total_weight.' '.Inflect::pluralize_if ($total_weight, $pricing_unit);
                  }
                $show_update_button = 'yes';
                $item_total_3dec = (($item_price * $total_weight) + ($extra_charge * $quantity)) + 0.00000001;
                $item_total_price = round($item_total_3dec, 2);
                $display_total_price = '$'.number_format($item_total_price, 2);
                $display_unit_price = $item_total_price;
              }
          }
        else
          {
            $display_weight = '';
            $show_update_button = 'no';
            $item_total_3dec = (($item_price * $quantity) + ($extra_charge * $quantity)) + 0.00000001;
            $item_total_price = round($item_total_3dec, 2);
            $display_total_price = '$'.number_format($item_total_price, 2);
            $display_unit_price = $item_total_price;
          }
      }
    else
      {
        $display_weight = '';
        $show_update_button = 'no';
        $item_total_price = 0;
      }
    if ( $out_of_stock )
      {
        $display_outofstock = '<img src="'.BASE_URL.DIR_GRAPHICS.'checkmark_wht.gif" align="centert">';
        $extra_charge = 0; // If not sold, then no extra charge
        $chk1 = '';
        $chk2 = 'checked';
      }
    else
      {
        $display_outofstock = '';
        $chk1 = 'checked';
        $chk2 = '';
      }
    if ( $extra_charge )
      {
        $extra_charge_calc = $extra_charge * $quantity;
        $total_extra = $total_extra + round ($extra_charge_calc, 2);
        $display_charge = '$'.number_format($extra_charge_calc, 2);
      }
    else
      {
        $display_charge = '';
      }
    if (!$display_only)
      {
        $display_stock = '
          <input type="radio" name="out_of_stock" value="0" '.$chk1.'>In<br>
          <input type="radio" name="out_of_stock" value="1" '.$chk2.'>Out';
      }
    else
      {
        $display_stock = '';
      }

    if( $item_total_price )
      {
        $total = $item_total_price + $total;
      }
    $total_pr = $total_pr + $quantity;
    $subtotal_pr = $subtotal_pr + $item_total_price;
    if ( $notes )
      {
        $display_notes = '<br><b>Customer note</b>: '.$notes;
      }
    else
      {
        $display_notes = '';
      }
    if ( $quantity > 1 )
      {
        //$display_ordering_unit = "".$ordering_unit."s";
        $display_ordering_unit = $ordering_unit;
      }
    else
      {
        $display_ordering_unit = $ordering_unit;
      }

    // Figure out the form open/close elements to be used only when the page is "editable" (display !== true)
    if (!$display_only)
      {
        $display_form_open = '<form action="'.$PHP_SELF.'?delivery_id='.$delivery_id.'&producer_id='.$producer_id.'#'.$member_id.'" method="post">';
        $display_form_close = '
        <td align="center" valign="top">
          <input type="hidden" name="updatevalues" value="ys">
          <input type="hidden" name="product_id" value="'.$product_id.'">
          <input type="hidden" name="product_id_printed" value="'.$product_id.'">
          <input type="hidden" name="producer_id" value="'.$producer_id.'">
          <input type="hidden" name="delivery_id" value="'.$delivery_id.'">
          <input type="hidden" name="member_id" value="'.$member_id.'">
          <input type="hidden" name="c_member_id" value="'.$member_id.'">
          <input type="hidden" name="c_basket_id" value="'.$c_basket_id.'">
          <input name="where" type="submit" value="Update">
          </form>';
        if( $member_id == $c_member_id )
          {
            $producer_orders_bycustomerstorage .= $message2;
          }
        else
          {
            $producer_orders_bycustomerstorage .= '';
          }
      }
    else
      {
        $display_form_open = '';
        $display_form_close = '';
      }



// adjust the unit price to what we actually want to display.
$display_price = '';
if (SHOW_ACTUAL_PRICE)
  {
    // Show customer markup as default -- not wholesale
    $display_unit_price = $item_price * (1 + CUSTOMER_MARKUP);
  }
else
  {
    $display_unit_price = $item_price;
  }

if ( $display_unit_price != 0 )
  {
    $display_price .= $font.' $'.number_format($display_unit_price, 2).'/'.$pricing_unit.'';
  }
if ( $display_unit_price != 0 && $extra_charge != 0 ) $display_price .= ' and ';
if ( $extra_charge != 0 )
  {
    $display_price .= '$'.number_format($extra_charge, 2).'/'.Inflect::singularize ($ordering_unit);
  }


    $producer_orders_bycustomerstorage .= '
      <tr align="center">
        <td align="right" valign="top">'.$display_form_open.'<b>#'.$product_id.'</b>&nbsp;&nbsp;</td>
        <td align="left" valign="top"><b>'.stripslashes ($product_name).'</b><br>'.$display_price.'<br>'.$display_notes.'</td>
        <td align="center" valign="top">'.$quantity.' '.Inflect::pluralize_if ($quantity, $display_ordering_unit).'</td>
        <td align="center" valign="top">'.$display_weight.'</td>
        <td align="center" valign="top">'.$display_charge.'</td>
        <td align="center" valign="top">'.$display_stock.' '.$display_outofstock.'</td>
        <td align="center" valign="top">'.$display_total_price.'</td>'.$display_form_close;
    $producer_orders_bycustomerstorage .= '
      </td>
      </tr>';
      // $producer_orders_bycustomerstorage .= "<tr><td colspan=\"8\">Customer Quantity: $total_pr Customer subtotal: \$".number_format($subtotal_pr,2)."</td></tr>";
  }
$querya = '
  SELECT
    transaction_name,
    transaction_comments,
    transaction_amount
  FROM
    '.TABLE_TRANS.' t,
    '.TABLE_TRANS_TYPES.' tt
  WHERE
    transaction_delivery_id = "'.$delivery_id.'"
    AND transaction_producer_id = "'.$producer_id.'"
    AND t.transaction_type = tt.ttype_id
    AND tt.ttype_parent = "20"
    AND t.transaction_taxed="1"';
$sqla = mysql_query($querya);
while( $resulta = mysql_fetch_array($sqla) )
  {
    $producer_orders_bycustomerstorage .= '
      <tr>
        <td colspan=8><strong>Adjustments</strong></td>
      </tr>
      <tr align="center">
        <td align="left" valign="top" colspan="2">'.$resulta['transaction_name'].'</td>
        <td align="left" valign="top" colspan="4">'.$resulta['transaction_comments'].'</td>
        <td align="right" valign="top">$'.number_format($resulta['transaction_amount'], 2).'</td>
        <td align="center" valign="top"></td>
      </tr>';
    $subtotal_pr = $subtotal_pr + $resulta['transaction_amount'];
    $total = $total + $resulta['transaction_amount'];
  }
$producer_orders_bycustomerstorage .= '
      </table>';

// Restore the session variables to their original settings
$member_id = $original_session_member_id;
?>