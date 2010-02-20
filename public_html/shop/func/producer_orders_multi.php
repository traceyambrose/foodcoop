<?php
////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// To add new sort methods or modify sort methods, search this file for     ///
/// "ADD NEW SORT ITEMS"                                                     ///
///                                                                          ///
/// You will need to:                                                        ///
///                                                                          ///
///    1.  Be sure to pull the needed information from the database          ///
///                                                                          ///
///    2.  Set up the search array (if it doesn't warrant headings, then     ///
///        place it in sort_array instead of sort_array                      ///
///                                                                          ///
///    3.  Create the appropriate header information for the sort method     ///
///                                                                          ///
///    4.  Add the sort header information to the headers                    ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

// Save session values in order to put them back before we're done (MESSY because of register_globals!)
$original_session_member_id = $_SESSION['member_id'];

$total_extra = 0;

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

if ( $_POST['Update'] == 'Update All' ) {
  // Get a list of all this producer's products so we can prevent any
  // code-hacking that would modify another producer's products
  $query_prodlist = '
    SELECT
      product_id
    FROM
      '.TABLE_PRODUCT.'
    WHERE
      producer_id = "'.$producer_id_you.'"
    ';
  $result_prodlist = @mysql_query($query_prodlist,$connection) or die(mysql_error());
  $prodlist = array ();
  while ( $row = mysql_fetch_array($result_prodlist) )
    {
    array_push ($prodlist, $row['product_id']);
    }
  };
$sqlp = '
  SELECT
    '.TABLE_MEMBER.'.business_name,
    '.TABLE_MEMBER.'.first_name,
    '.TABLE_MEMBER.'.last_name
  FROM
    '.TABLE_PRODUCER.',
    '.TABLE_MEMBER.'
  WHERE
    '.TABLE_PRODUCER.'.producer_id = "'.$producer_id_you.'"
    AND '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
  GROUP BY
    '.TABLE_PRODUCER.'.producer_id
  ORDER BY
    business_name ASC,
    last_name ASC';
$resultp = @mysql_query($sqlp,$connection) or die("Couldn't execute query.");
while ( $row = mysql_fetch_array($resultp) )
  {
    $a_business_name = stripslashes($row['business_name']);
    $a_first_name = stripslashes($row['first_name']);
    $a_last_name = stripslashes($row['last_name']);
    if ( !$a_business_name )
      {
        $a_business_name = $a_first_name.' '.$a_last_name;
      }
  }
// ADD NEW SORT ITEMS: be sure the sort item is in one of these two arrays.
// The array contains the textual sort description, the name of the related
// variable and the "SORT BY" code for mysql
// This data is also in orders_prdcr_invoice.php and should move to a config file
// Set up sort variables:
$sort_array = array (
  'Member ID' => array ('var_name' => 'member_id', 'db_sort' => ''.TABLE_BASKET_ALL.'.member_id ASC'),
  'Member Name' => array ('var_name' => 'show_mem', 'db_sort' => ''.TABLE_MEMBER.'.last_name ASC, '.TABLE_MEMBER.'.first_name ASC, '.TABLE_MEMBER.'.business_name ASC'),
  'Delivery Code' => array ('var_name' => 'delcode_id', 'db_sort' => ''.TABLE_DELCODE.'.delcode_id ASC'),
  'Hub' => array ('var_name' => 'hub', 'db_sort' => ''.TABLE_DELCODE.'.hub ASC'),
  'Storage Type' => array ('var_name' => 'storage_code', 'db_sort' => ''.TABLE_PRODUCT_STORAGE_TYPES.'.storage_code ASC'),
  'Product ID' => array ('var_name' => 'product_id', 'db_sort' => ''.TABLE_BASKET.'.product_id ASC'),
  'Product Name' => array ('var_name' => 'product_name', 'db_sort' => ''.TABLE_PRODUCT.'.product_name ASC'),
  'Subcategory' => array ('var_name' => 'subcategory_name', 'db_sort' => ''.TABLE_SUBCATEGORY.'.subcategory_name ASC'),
  );
$sort_array2 = array (
  'Order of Purchase' => array ('var_name' => 'bpid', 'db_sort' => ''.TABLE_BASKET.'.bpid ASC'),
  );
// $sort1_head_color = '#e0e0e0';
// $sort1_font_color = '#000066';
// $sort1_font_size = '1.5';
// $sort1_border_color = '#777777';
// $sort1_margin_color = '#e0e0e0';
//
// $sort2_head_color = '#e8e8e8';
// $sort2_font_color = '#000066';
// $sort2_font_size = '1.1';
// $sort2_border_color = '#888888';
// $sort2_margin_color = '#e8e8e8'; // This might be modified to obfuscate the margin
//
// $sort3_head_color = '#f0f0f0';
// $sort3_font_color = '#444400';
// $sort3_font_size = '1.1';
// $sort3_border_color = '#999999';
// $sort3_margin_color = '#f0f0f0'; // This might be modified to obfuscate the margin
//
// $sort4_head_color = '#f8f8f8';
// $sort4_border_color = '#bbbbbb';
// $sort4_margin_color = '#f8f8f8'; // This might be modified to obfuscate the margin
$sort1_head_color = '#9ca5b5';
$sort1_font_color = '#000000';
$sort1_font_size = '1.5';
$sort1_border_color = '#777777';
$sort1_margin_color = '#9ca5b5';
$sort2_head_color = '#dddddd';
$sort2_font_color = '#000000';
$sort2_font_size = '1';
$sort2_border_color = '#dddddd';
$sort2_margin_color = '#dddddd';
$sort3_head_color = '#ffffff';
$sort3_font_color = '#000000';
$sort3_font_size = '1';
$sort3_border_color = '#ffffff';
$sort3_margin_color = '#ffffff';
$sort4_head_color = '#ffffff';
$sort4_font_color = '#000000';
$sort4_font_size = '1';
$sort4_border_color = '#ffffff';
$sort4_margin_color = '#ffffff';
// Make sure the first three sort values are shifted left (except sort4, which stands alone)
if ( $_POST['sort2'] == '' )
  {
    $_POST['sort2'] = $_POST['sort3'];
    $_POST['sort3'] = '';
  }
if ( $_POST['sort1'] == '' )
  {
    $_POST['sort1'] = $_POST['sort2'];
    $_POST['sort2'] = $_POST['sort3'];
    $_POST['sort3'] = '';
  }
// Set up sort defaults
if ( ! $_POST['updatevalues'] )
  {
    $_POST['sort1'] = 'Storage Type';
    $_POST['sort2'] = 'Delivery Code';
    $_POST['sort3'] = '';
    $_POST['sort4'] = 'Member ID';
  }
// Set up first-level sort field
$sort1_display = '<select name="sort1">';
$sort1 = '';
foreach ( array_keys (array_merge (array ('' => ''), $sort_array)) as $sort_type )
  {
    $selected = '';
    if ( $sort_type == $_POST['sort1'] )
      {
        $sort1 = $sort_type;
        $selected = ' SELECTED';
      }
    $sort1_display .= '
    <option value="'.$sort_type.'"'.$selected.'>'.$sort_type.'</option>';
  }
$sort1_display .= '</select>';
// Set up second-level sort field
$sort2_display = '<select name="sort2">';
$sort2 = '';
foreach ( array_keys (array_merge (array ('' => ''), $sort_array)) as $sort_type )
  {
    $selected = '';
    if ( $sort_type == $_POST['sort2'] )
      {
        $sort2 = $sort_type;
        $selected = ' SELECTED';
      }
    $sort2_display .= '
    <option value="'.$sort_type.'"'.$selected.'>'.$sort_type.'</option>';
  }
$sort2_display .= '</select>';
// Set up third-level sort field
$sort3_display = '<select name="sort3">';
$sort3 = '';
foreach ( array_keys (array_merge (array ('' => ''), $sort_array)) as $sort_type )
  {
    $selected = '';
    if ( $sort_type == $_POST['sort3'] )
      {
        $sort3 = $sort_type;
        $selected = ' SELECTED';
      }
    $sort3_display .= '
    <option value="'.$sort_type.'"'.$selected.'>'.$sort_type.'</option>';
  }
$sort3_display .= '</select>';
// After the first three sort fields, we will allow some additional sort techniques
// that only make sense for sorting without headings
$sort_array = array_merge ($sort_array, $sort_array2);
// Set up third-level sort field
$sort4_display = '<select name="sort4">';
$sort4 = '';
foreach ( array_keys (array_merge (array ('' => ''), $sort_array)) as $sort_type )
  {
    $selected = '';
    if ( $sort_type == $_POST['sort4'] )
      {
        $sort4 = $sort_type;
        $selected = ' SELECTED';
      }
    $sort4_display .= '
    <option value="'.$sort_type.'"'.$selected.'>'.$sort_type.'</option>';
  }
$sort4_display .= '</select>';
// Figure out at which level to show the column headings
// We will show the column headings at the most detailed level EXCEPT level 4
if ( $sort3 )
  {
    $sort_level = 3;
  }
elseif ( $sort2 )
  {
    // There is no level-three sort
    $sort_level = 2;
    //  $sort4_border_color = $sort3_margin_color;
  }
else
  {
    // There is no level-two sort
    $sort_level = 1;
    $sort3_margin_color = $sort2_margin_color;
    $sort3_border_color = $sort2_margin_color;
  }
$producer_orders_multi = '
<style type="text/css">
.sort4_head_color
  {
  background:'.$sort4_head_color.';
  }
.sort4_left_color
  {
  border-left:1px solid '.$sort4_border_color.';
  }
.sort4_top_color
  {
  border-top:1px solid '.$sort4_border_color.';
  padding:0.5em;
  border-bottom:1px solid #aaa;
  }
.sort1_head_color
  {
  background:'.$sort1_head_color.';
  }
.sort1_font_color
  {
  color:'.$sort1_font_color.';
  }
.sort1_font_size
  {
  font-size:'.$sort1_font_size.'em;
  }
.sort1_margin_color
  {
  background:'.$sort1_margin_color.';
  }
.sort1_left_color
  {
  border-left:1px solid '.$sort1_border_color.';
  }
.sort1_right_color
  {
  border-right:1px solid '.$sort1_border_color.';
  }
.sort1_top_color
  {
  border-top:1px solid '.$sort1_border_color.';
  }
.sort2_head_color
  {
  background:'.$sort2_head_color.';
  }
.sort2_font_color
  {
  color:'.$sort2_font_color.';
  }
.sort2_font_size
  {
  font-size:'.$sort2_font_size.'em;
  }
.sort2_margin_color
  {
  background:'.$sort2_margin_color.';
  }
.sort2_top_color
  {
  border-top:1px solid '.$sort2_border_color.';
  }
.sort2_left_color
  {
  border-left:1px solid '.$sort2_border_color.';
  }
.sort3_head_color
  {
  background:'.$sort3_head_color.';
  }
.sort3_font_color
  {
  color:'.$sort3_font_color.';
  }
.sort3_font_size
  {
  font-size:'.$sort3_font_size.'em;
  }
.sort3_margin_color
  {
  background:'.$sort3_margin_color.';
  }
.sort3_left_color
  {
  border-left:1px solid '.$sort3_border_color.';
  }
.sort3_top_color
  {
  border-top:1px solid '.$sort3_border_color.';
  }
</style>';
// Open one form for all product changes
$producer_orders_multi .= '
  <form action="'.$PHP_SELF.'?delivery_id='.$delivery_id.'&producer_id='.$producer_id.'#updated" method="post">
  <table width="100%" cellspacing="0" cellpadding="0" border="0">';
////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
///            BEGIN FUNCTION TO CALCULATE DISPLAY OF INVOICE                ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

// If not administrator, then force producer to be the owner
if ( $producer_id_you && strpos ($_SESSION['auth_type'], 'administrator') === false )
  {
    $producer_id = $producer_id_you;
  }
// If no delivery id was passed, then use the current value
if ($_GET['delivery_id'])
  {
    $delivery_id = $_GET['delivery_id'];
//    $show_form = false;
  }
else
  {
    $delivery_id = $_SESSION['current_delivery_id'];
  }
$show_form = true;

$sqlp = '
  SELECT
    '.TABLE_MEMBER.'.business_name,
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
    $a_business_name = $row['business_name'];
    $a_first_name = $row['first_name'];
    $a_last_name = $row['last_name'];
    if (!$a_business_name)
      {
        $a_business_name = "$a_first_name $a_last_name";
      }
  }
$total_pr = 0;
$subtotal_pr = 0;
//TABLE_BASKET_ALL,
// ADD NEW SORT ITEMS: be sure the search item is being pulled from the database here
$sqlpr = '
  SELECT
    '.TABLE_BASKET.'.bpid,
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
    '.TABLE_SUBCATEGORY.'.subcategory_name,
    '.TABLE_PRODUCT.'.subcategory_id,
    '.TABLE_PRODUCT.'.detailed_notes,
    '.TABLE_PRODUCT_STORAGE_TYPES.'.storage_code,
    '.TABLE_BASKET_ALL.'.deltype AS ddeltype,
    '.TABLE_MEMBER.'.member_id,
    '.TABLE_MEMBER.'.last_name,
    '.TABLE_MEMBER.'.first_name,
    '.TABLE_MEMBER.'.business_name,
    '.TABLE_MEMBER.'.email_address,
    '.TABLE_MEMBER.'.home_phone,
    '.TABLE_MEMBER.'.mem_taxexempt,
    '.TABLE_DELCODE.'.hub,
    '.TABLE_DELCODE.'.delcode_id,
    '.TABLE_DELCODE.'.delcode,
    '.TABLE_DELCODE.'.deltype,
    '.TABLE_DELCODE.'.truck_code,
    hubs.hub_name
  FROM
    '.TABLE_BASKET.'
  LEFT JOIN '.TABLE_PRODUCT.'
    ON '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id
  LEFT JOIN '.TABLE_PRODUCT_STORAGE_TYPES.'
    ON '.TABLE_PRODUCT_STORAGE_TYPES.'.storage_id = '.TABLE_PRODUCT.'.storage_id
  LEFT JOIN '.TABLE_SUBCATEGORY.'
    ON '.TABLE_PRODUCT.'.subcategory_id = '.TABLE_SUBCATEGORY.'.subcategory_id
  LEFT JOIN '.TABLE_BASKET_ALL.'
    ON '.TABLE_BASKET_ALL.'.basket_id = '.TABLE_BASKET.'.basket_id
  LEFT JOIN '.TABLE_MEMBER.'
    ON '.TABLE_BASKET_ALL.'.member_id = '.TABLE_MEMBER.'.member_id
  LEFT JOIN '.TABLE_DELCODE.'
    ON '.TABLE_BASKET_ALL.'.delcode_id = '.TABLE_DELCODE.'.delcode_id
  LEFT JOIN '.$table_rt.'
    ON '.TABLE_DELCODE.'.route_id = '.$table_rt.'.route_id
  LEFT JOIN hubs
    ON hubs.hub_id = '.TABLE_DELCODE.'.hub
  WHERE
    '.TABLE_PRODUCT.'.producer_id = "'.$producer_id.'"
    AND '.TABLE_PRODUCT.'.hidefrominvoice = 0
    AND ('.TABLE_BASKET_ALL.'.delivery_id = '.$delivery_id.'
      OR '.TABLE_BASKET.'.future_delivery_id = '.$delivery_id.')
  ORDER BY';
if ( $sort1 ) $sqlpr .= ' '.$sort_array[$sort1]['db_sort'].",\n";
if ( $sort2 ) $sqlpr .= ' '.$sort_array[$sort2]['db_sort'].",\n";
if ( $sort3 ) $sqlpr .= ' '.$sort_array[$sort3]['db_sort'].",\n";
if ( $sort4 ) $sqlpr .= ' '.$sort_array[$sort4]['db_sort'].",\n";
$sqlpr .= ' 1'; // We include the "ORDER BY 1" clause to make mysql happy
                // in case nothing is chosen to order by.
$resultpr = @mysql_query($sqlpr) or die("Couldn't execute query 1");
while ( $row = mysql_fetch_array($resultpr) )
  {
    // ADD NEW SORT ITEMS: be sure the search item is assigned to a variable here
    $product_name = stripslashes ($row['product_name']);
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

    $bpid = $row['bpid'];
    $email_address = $row['email_address'];
    $home_phone = $row['home_phone'];
    $ddeltype = $row['ddeltype'];
    $mem_taxexempt = $row['mem_taxexempt'];
    // If there's no last name, then use the business name
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
    $subcategory_name = $row['subcategory_name'];
    $random_weight = $row['random_weight'];
    $total_weight = $row['total_weight'];
    $out_of_stock = $row['out_of_stock'];
    $extra_charge = $row['extra_charge'];
    $future_delivery_id = $row['future_delivery_id'];
    $detailed_notes = stripslashes($row['detailed_notes']);
    $notes = stripslashes($row['customer_notes_to_producer']);
    $pricing_unit = $row['pricing_unit'];
    $hub_name = $row['hub_name'];
    $update_id = "-$c_basket_id-$product_id"; // This is used to uniquely tag each update field
    $total_weight_updated = '';
    $out_of_stock_updated = '';
    ////////////////////////////////////////////////////////////////////////////////
    ///                                                                          ///
    ///             CALCULATE INFORMATION FOR THIS ORDER LINE-ITEM               ///
    ///                                                                          ///
    ////////////////////////////////////////////////////////////////////////////////
    if( $out_of_stock == 1 )
      {
        $display_total_price = '$'.number_format(0, 2);
        // This next bit sets the amount to add back in the case of an "un-outed" item
        if ( $random_weight == 1)
          {
            $out_restore_value = round(($item_price * $total_weight) + ($extra_charge * $quantity), 2);
          }
        else
          {
            $out_restore_value = round((($item_price * $quantity) + ($extra_charge * $quantity)), 2);
          }
      }
    if ( $future_delivery_id == $delivery_id )
      {
        $display_weight = '';
        $item_total_price = 0;
        $display_total_price = '<font color="#ff0000">Invoiced in a previous order</font>';
      }
    elseif ( $future_delivery_id > $delivery_id )
      {
        $display_weight = '';
        $item_total_price = 0;
        $display_total_price = '<font color=#ff0000>Future<br>delivery</font>';
      }
    elseif( $out_of_stock != 1)
      {
        if ( $random_weight == 1)
          {
            if ( $show_form )
              {
                // Display weight information form since it is the current delivery
                if ( $_POST['Update'] == 'Update All' && $_POST['total_weight'.$update_id] != $total_weight )
                  {
                    // Check whether we just updated this value
                    $total_weight = $_POST['total_weight'.$update_id];
                    $query_update = '
                      UPDATE
                        '.TABLE_BASKET.'
                      SET
                        total_weight = "'.$total_weight.'"
                      WHERE
                        basket_id = "'.$c_basket_id.'"
                        AND product_id = "'.$product_id.'"';
                    $result_update = @mysql_query($query_update,$connection) or die(mysql_error());
                    $total_weight_updated = '<br><font color="#ff0000">UPDATED</font>';
                  }
                $display_weight = '<input type="text" name="total_weight'.$update_id.'" value="'.$total_weight.'" size="2" maxlength="11"><br>'.Inflect::pluralize ($pricing_unit).$total_weight_updated;
              }
            else
              {
                // Do not display form; just the information because it is historic data
                $display_weight = $total_weight.'<br>'.$pricing_unit."s";
              }
            if ( $total_weight == 0 )
              {
                $message_incomplete = '<font color="#770000">Order Incomplete<font>';
              }
            $item_total_3dec = ($item_price * $total_weight) + ($extra_charge * $quantity);
            $item_total_price = round($item_total_3dec, 2);
            $display_unit_price = $item_total_price;
            $display_total_price = '$'.number_format($item_total_price, 2);
          }
        else
          {
            $display_weight = '';
            $item_total_3dec = (($item_price * $quantity) + ($extra_charge * $quantity));
            $item_total_price = round($item_total_3dec, 2);
            $display_unit_price = $item_total_price;
            $display_total_price = '$'.number_format($item_total_price, 2);
          }
      }
    else
      {
        // Out of stock condition
        $display_weight = '';
        $item_total_price = 0;
        $extra_charge = 0; // If not sold, then no extra charge
      }
    if ( $extra_charge )
      {
        $extra_charge_calc = $extra_charge*$quantity;
        $total_extra = $total_extra + round ($extra_charge_calc, 2);
        $display_charge = '$'.number_format($extra_charge_calc, 2);
      }
    else
      {
        $display_charge = '';
      }
    if ( $show_form )
      {
        // Display IN/OUT information form since it is the current delivery
        if ( $_POST['Update'] == 'Update All' && $_POST['out_of_stock'.$update_id] != $out_of_stock )
          {
            // Check whether we just updated this value
            $out_of_stock = $_POST['out_of_stock'.$update_id];
            $query_update ='
              UPDATE
                '.TABLE_BASKET.'
              SET
                out_of_stock = "'.$out_of_stock.'"
              WHERE
                basket_id = "'.$c_basket_id.'"
                AND product_id = "'.$product_id.'"';
            $result_update = @mysql_query($query_update,$connection) or die(mysql_error());
            $out_of_stock_updated = '<br><font color="#ff0000">UPDATED</font>';
            // Be sure to subtract total for items that have been marked out-of-stock
            if ($out_of_stock == 1)
              {
                $total = $total - $item_total_price;
              }
            elseif ($out_of_stock == 0)
              {
                $total = $total + $out_restore_value;
              }
          }
        if ( $out_of_stock == 1 )
          {
            $display_outofstock = '<img src="'.BASE_URL.DIR_GRAPHICS.'checkmark_wht.gif">';
            $chk1 = '';
            $chk2 = 'checked';
          }
        else
          {
            $display_outofstock = '';
            $chk1 = 'checked';
            $chk2 = '';
          }
        $display_stock = '<input type="radio" name="out_of_stock'.$update_id.'" value="0" '.$chk1.'>In<br>
          <input type="radio" name="out_of_stock'.$update_id.'" value="1" '.$chk2.'>Out'.$out_of_stock_updated;
      }
    else
      {
        // Do not display form; just the information because it is historic data
        if ( $out_of_stock == 1 )
          {
            $display_outofstock = '<img src="'.BASE_URL.DIR_GRAPHICS.'checkmark_wht.gif">';
          }
        else
          {
            $display_outofstock = "";
          }
        $display_stock = "";
      }
    if( $item_total_price )
      {
        $total = $item_total_price+$total;
      }
    $total_pr = $total_pr + $quantity;
    $subtotal_pr = $subtotal_pr + $item_total_price;

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
    $display_price .= $font.' $'.number_format($display_unit_price, 2).'/'.$pricing_unit.'</font>';
  }
if ( $display_unit_price != 0 && $extra_charge != 0 ) $display_price .= '<br>and<br>';
if ( $extra_charge != 0 )
  {
    $display_price .= '$'.number_format($extra_charge, 2).'/'.Inflect::singularize ($ordering_unit);
  }

    ////////////////////////////////////////////////////////////////////////////////
    ///                                                                          ///
    ///                 CALCULATE INDIVIDUAL DISPLAY ELEMENTS                    ///
    ///                                                                          ///
    ////////////////////////////////////////////////////////////////////////////////
    // ADD NEW SORT ITEMS: if the search is NOT in search_array2, then create
    // a new variable for header content here
    $header_member = '
      <b>Member:</b> '.$show_mem.' (#'.$member_id.')
      <br>'.$home_phone.' <a href="mailto:'.$email_address.'">'.$email_address.'</a>
      <br>'.(convert_route_code($basket_id, $member_id, $last_name, $first_name, $business_name, $a_business_name, $hub, $delcode_id, $deltype, $truck_code, $storage_code, $show_mem, $show_mem2, $product_name, $quantity, $ordering_unit, $product_id, $item_price, $delcode));
    $header_member_name = '
      <b>Member:</b> '.$show_mem.' (#'.$member_id.')
      <br>'.$home_phone.' <a href="mailto:'.$email_address.'">'.$email_address.'</a>
      <br>'.(convert_route_code($basket_id, $member_id, $last_name, $first_name, $business_name, $a_business_name, $hub, $delcode_id, $deltype, $truck_code, $storage_code, $show_mem, $show_mem2, $product_name, $quantity, $ordering_unit, $product_id, $item_price, $delcode));
    $header_delivery_code = '
      <b>Delivery Code:</b> '.$delcode_id.'
      <br>'.$delcode;
    $header_hub = '
      <b>Hub:</b> '.$hub_name.'</font> ('.$hub.')';
    $header_storage_type = '
      <b>Storage Code:</b> ['.$storage_code.']';
    $header_product = '
      <b>Product #'.$product_id.':</b> '.$product_name.'
      <br><font size="-1">'.$display_price.'
      <br>'.$detailed_notes.'</font>';
    $header_product_name = '
      <b>Product Name:</b> '.$product_name.' ('.$product_id.')
      <br><font size="-1">'.$display_price.'
      <br>'.$detailed_notes.'</font>';
    $header_subcategory_name = '
      <b>Subcategory:</b> '.$subcategory_name;
    // Now, just in case they haven't been used in a header, we add line-item member and product information
    // Line items are for information the really SHOULD be represented to each line of the invoice, somehow
    $line_item_member = $header_member;
    $line_item_product = '
      <b>Product #'.$product_id.':</b> '.$product_name.'
      <br>'.$display_price;
    if ( $notes )
      {
        // These aren't appropriate in a header (above) since they are from a particular customer
        $line_item_product .= '<br><b>Customer note:</b>'.$notes;
      }
    // ADD NEW SEARCH ITEMS: if the search is NOT in search_array2, then check
    // conditions for adding it to the various headers sort1, sort2, or sort3 below
    // Get the header for sort1
    if ( $sort_array[$sort1]['var_name'] == 'member_id' )
      {
        $sort1_header = $header_member;
        $line_item_member = ''; // We no longer need member info in the line items
      }
    elseif ( $sort_array[$sort1]['var_name'] == 'show_mem' )
      {
        $sort1_header = $header_member_name;
        $line_item_member = ''; // We no longer need member info in the line items
      }
    elseif ( $sort_array[$sort1]['var_name'] == 'delcode_id' )
      {
        $sort1_header = $header_delivery_code;
      }
    elseif ( $sort_array[$sort1]['var_name'] == 'product_id' )
      {
        $sort1_header = $header_product;
        $line_item_product = ''; // We no longer need product info in the line items
      }
    elseif ( $sort_array[$sort1]['var_name'] == 'product_name' )
      {
        $sort1_header = $header_product_name;
        $line_item_product = ''; // We no longer need product info in the line items
      }
    elseif ( $sort_array[$sort1]['var_name'] == 'hub' )
      {
        $sort1_header = $header_hub;
      }
    elseif ( $sort_array[$sort1]['var_name'] == 'storage_code' )
      {
        $sort1_header = $header_storage_type;
      }
    elseif ( $sort_array[$sort1]['var_name'] == 'subcategory_name' )
      {
        $sort1_header = $header_subcategory_name;
      }
    // Get the header for sort2
    if ( $sort_array[$sort2]['var_name'] == 'member_id' )
      {
        $sort2_header = $header_member;
        $line_item_member = '';
      }
    elseif ( $sort_array[$sort2]['var_name'] == 'show_mem' )
      {
        $sort2_header = $header_member_name;
        $line_item_member = ''; // We no longer need member info in the line items
      }
    elseif ( $sort_array[$sort2]['var_name'] == 'delcode_id' )
      {
        $sort2_header = $header_delivery_code;
      }
    elseif ( $sort_array[$sort2]['var_name'] == 'product_id' )
      {
        $sort2_header = $header_product;
        $line_item_product = '';
      }
    elseif ( $sort_array[$sort2]['var_name'] == 'product_name' )
      {
        $sort2_header = $header_product_name;
        $line_item_product = ''; // We no longer need product info in the line items
      }
    elseif ( $sort_array[$sort2]['var_name'] == 'hub' )
      {
        $sort2_header = $header_hub;
      }
    elseif ( $sort_array[$sort2]['var_name'] == 'storage_code' )
      {
        $sort2_header = $header_storage_type;
      }
    elseif ( $sort_array[$sort2]['var_name'] == 'subcategory_name' )
      {
        $sort2_header = $header_subcategory_name;
      }
    // Get the header for sort3
    if ( $sort_array[$sort3]['var_name'] == 'member_id' )
      {
        $sort3_header = $header_member;
        $line_item_member = '';
      }
    elseif ( $sort_array[$sort3]['var_name'] == 'show_mem' )
      {
        $sort3_header = $header_member_name;
        $line_item_member = ''; // We no longer need member info in the line items
      }
    elseif ( $sort_array[$sort3]['var_name'] == 'delcode_id' )
      {
        $sort3_header = $header_delivery_code;
      }
    elseif ( $sort_array[$sort3]['var_name'] == 'product_id' )
      {
        $sort3_header = $header_product;
        $line_item_product = '';
      }
    elseif ( $sort_array[$sort3]['var_name'] == 'product_name' )
      {
        $sort3_header = $header_product_name;
        $line_item_product = ''; // We no longer need product info in the line items
      }
    elseif ( $sort_array[$sort3]['var_name'] == 'hub' )
      {
        $sort3_header = $header_hub;
      }
    elseif ( $sort_array[$sort3]['var_name'] == 'storage_code' )
      {
        $sort3_header = $header_storage_type;
      }
    elseif ( $sort_array[$sort3]['var_name'] == 'subcategory_name' )
      {
        $sort3_header = $header_subcategory_name;
      }
    $line_item = $line_item_product;
    if ( $line_item_member && $line_item_product )
      {
        $line_item .='<br>';
      }
    $line_item .= $line_item_member;
    if( $bpid == $_POST['bpid'] )
      {
        $line_item .= '<br>'.$message2;
      }
    $line_markup = '
      <tr>
        <td class="sort1_left_color sort1_margin_color">&nbsp;</td>
        <td class="sort2_left_color sort2_margin_color">&nbsp;</td>
        <td class="sort3_left_color sort3_margin_color">&nbsp;</td>
        <td align="left" valign="top" class="sort4_top_color sort4_left_color">'.$line_item.'</td>
        <td align="center" valign="top" class="sort4_top_color">&nbsp;'.$quantity.' '.Inflect::pluralize_if ($quantity, $ordering_unit).'</td>
        <td align="center" valign="top" class="sort4_top_color">&nbsp;'.$display_weight.'</td>
        <td align="left" valign="top" class="sort4_top_color"><table border="0" cellpadding="0" cellspacing="0"><tr><td>'.$display_outofstock.'</td><td>'.$display_stock.'</td></tr></table></td>
        <td align="center" valign="top" class="sort1_right_color sort4_top_color">&nbsp;'.$display_total_price.'
          <input type="hidden" name="product_id'.$update_id.'" value="'.$product_id.'">
          <input type="hidden" name="bpid'.$update_id.'" value="'.$bpid.'">
          <input type="hidden" name="member_id'.$update_id.'" value="'.$member_id.'">
        </td>';
    ////////////////////////////////////////////////////////////////////////////////
    ///                                                                          ///
    ///                         SEND PRIMARY SORT HEADER                         ///
    ///                                                                          ///
    ////////////////////////////////////////////////////////////////////////////////
    // This compares i.e. $sort_array[$sort1]['value'] with $member_id
    // when $sort_array[$sort1]['var_name'] happens to be 'member_id'
    // And if they're different, then we need a new major section...
    if ( $sort_array[$sort1]['value'] != $$sort_array[$sort1]['var_name'] )
      {
        // Assign the new value to compare against
        $sort_array[$sort1]['value'] = $$sort_array[$sort1]['var_name'];
        // We will also want to force a second-level sort subsection
        $sort_array[$sort2]['value'] = '';
        // Now add the first-level sort section header
        $producer_orders_multi .= '
          <tr class="sort1_head_color">
            <td colspan="9" class="sort1_top_color sort1_left_color sort1_right_color">
              <table width="100%">
                <tr class="sort1_head_color">
                  <td align="left" class="sort1_font_color sort1_font_size">'.$sort1_header.'</td>
                </tr>
              </table>
            </td>
          </tr>';
        if ( $sort_level == 1 )
          {
            $producer_orders_multi .= '
              <tr class="sort2_head_color">
                <th valign="bottom" class="sort1_head_color sort1_left_color">&nbsp;</th>
                <th valign="bottom" class="sort2_top_color sort2_left_color">&nbsp;</th>
                <th valign="bottom" class="sort2_top_color sort3_left_color">&nbsp;</th>
                <th valign="bottom" class="sort2_top_color" style="font-size:0.8em;">&nbsp;</th>
                <th valign="bottom" class="sort2_top_color" style="font-size:0.8em;">Quantity</th>
                <th valign="bottom" class="sort2_top_color" style="font-size:0.8em;">Weight</th>
                <th valign="bottom" class="sort2_top_color" style="font-size:0.8em;">In<br>Stock?</th>
                <th valign="bottom" class="sort1_right_color sort2_top_color" style="font-size:0.8em;">Item<br>Total</th>
              </tr>';
          }
      }
    //echo "SORT_ARRAY[SORT1][VALUE]: ".$sort_array[$sort1]['value']."<br>\n";
    //echo "SORT_ARRAY[SORT1][".$sort_array[$sort1]['var_name']."]: ".$$sort_array[$sort1]['var_name']."<br>\n";
    ////////////////////////////////////////////////////////////////////////////////
    ///                                                                          ///
    ///                        SEND SECONDARY SORT HEADER                        ///
    ///                                                                          ///
    ////////////////////////////////////////////////////////////////////////////////
    // This compares i.e. $sort_array[$sort2]['value'] with $member_id
    // when $sort_array[$sort2]['var_name'] happens to be 'member_id'
    // And if they're different, then we need a new secondary section...
    if ( $sort_array[$sort2]['value'] != $$sort_array[$sort2]['var_name'] )
      {
        // Assign the new value to compare against
        $sort_array[$sort2]['value'] = $$sort_array[$sort2]['var_name'];
        // We will also want to force a third-level sort subsection
        $sort_array[$sort3]['value'] = '';
        // Now add the second-level sort section header
        $producer_orders_multi .= '
          <tr class="sort2_head_color">
            <td class="sort1_head_color sort1_left_color">&nbsp;</td>
            <td colspan="8" class="sort1_right_color sort2_top_color sort2_left_color">
              <table width="100%">
                <tr class="sort2_head_color">
                  <td align="left" class="sort2_font_color sort2_font_size">'.$sort2_header.'</td>
                </tr>
              </table>
            </td>
          </tr>';
        if ( $sort_level == 2 )
          {
            $producer_orders_multi .= '
              <tr class="sort3_head_color">
                <th valign="bottom" class="sort1_head_color sort1_left_color">&nbsp;</th>
                <th valign="bottom" class="sort2_head_color sort2_left_color">&nbsp;</th>
                <th class="sort3_left_color sort3_top_color sort3_head_color" valign="bottom">&nbsp;</th>
                <th class="sort3_top_color" valign="bottom" style="font-size:0.8em;">&nbsp;</th>
                <th class="sort3_top_color" valign="bottom" style="font-size:0.8em;">Quantity</th>
                <th class="sort3_top_color" valign="bottom" style="font-size:0.8em;">Weight</th>
                <th class="sort3_top_color" valign="bottom" style="font-size:0.8em;">In<br>Stock?</th>
                <th valign="bottom" class="sort1_right_color sort3_top_color" style="font-size:0.8em;">Item<br>Total</th>
              </tr>';
          }
      }
    ////////////////////////////////////////////////////////////////////////////////
    ///                                                                          ///
    ///                        SEND TERTIARY SORT HEADER                         ///
    ///                                                                          ///
    ////////////////////////////////////////////////////////////////////////////////
    // This compares i.e. $sort_array[$sort3]['value'] with $member_id
    // when $sort_array[$sort3]['var_name'] happens to be 'member_id'
    // And if they're different, then we need a new secondary section...
    if ( $sort_array[$sort3]['value'] != $$sort_array[$sort3]['var_name'] )
      {
        // Assign the new value to compare against
        $sort_array[$sort3]['value'] = $$sort_array[$sort3]['var_name'];
        // There is no fourth-level subsection
        // Now add the third-level sort section header
        $producer_orders_multi .= '
          <tr class="sort3_head_color">
            <td class="sort1_head_color sort1_left_color">&nbsp;</td>
            <td class="sort2_head_color sort2_left_color">&nbsp;</td>
            <td colspan="7" class="sort1_right_color sort3_left_color sort3_top_color sort3_head_color">
              <table width="100%">
                <tr class="sort3_head_color">
                  <td align="left" class="sort3_font_color sort3_font_size">'.$sort3_header.'</td>
                </tr>
              </table>
            </td>
          </tr>';
        $producer_orders_multi .= '
          <tr class="sort4_head_color">
            <th valign="bottom" class="sort1_head_color sort1_left_color">&nbsp;</th>
            <th valign="bottom" class="sort2_head_color sort2_left_color">&nbsp;</th>
            <th valign="bottom" class="sort3_head_color sort3_left_color">&nbsp;</th>
            <th valign="bottom" class="sort4_top_color" style="border-left:1px solid '.$sort4_border_color.';font-size:0.8em;">&nbsp;</th>
            <th valign="bottom" class="sort4_top_color" style="font-size:0.8em;">Quantity</th>
            <th valign="bottom" class="sort4_top_color" style="font-size:0.8em;">Weight</th>
            <th valign="bottom" class="sort4_top_color" style="font-size:0.8em;">In<br>Stock?</th>
            <th valign="bottom" class="sort1_right_color sort4_top_color" style="font-size:0.8em;">Item<br>Total</th>
          </tr>';
      }
    $producer_orders_multi .= $line_markup;
  }
$producer_orders_multi .= '
  <tr>
    <td colspan="9" align="center" bgcolor="#eeeeee">
      <br>
      <input type="hidden" name="sort1" value="'.$sort1.'">
      <input type="hidden" name="sort2" value="'.$sort2.'">
      <input type="hidden" name="sort3" value="'.$sort3.'">
      <input type="hidden" name="sort4" value="'.$sort4.'">
      <input type="hidden" name="producer_id" value="'.$producer_id.'">
      <input type="hidden" name="delivery_id" value="'.$delivery_id.'">
      <input name="Update" type="submit" value="Update All">
      <br><br>
    </td>
  </tr>
</table>
</form>';

// Restore the session variables to their original settings
$member_id = $original_session_member_id;
?>
