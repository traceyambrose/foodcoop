<?php

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

function generate_producer_summary ($producer_id, $delivery_id, $detail_type, $use)
  {
    global $connection,
           $include_header,
           $include_footer;
    include_once ("general_functions.php");

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



    ///                 OBTAIN PRODUCER BUSINESS AND NAME INFO.                  ///
    $sqlp = '
      SELECT
        '.TABLE_MEMBER.'.business_name,
        '.TABLE_MEMBER.'.first_name,
        '.TABLE_MEMBER.'.last_name,
        '.TABLE_MEMBER.'.address_line1,
        '.TABLE_MEMBER.'.address_line2,
        '.TABLE_MEMBER.'.city,
        '.TABLE_MEMBER.'.state,
        '.TABLE_MEMBER.'.zip,
        '.TABLE_MEMBER.'.county,
        '.TABLE_MEMBER.'.email_address,
        '.TABLE_MEMBER.'.home_phone,
        '.TABLE_MEMBER.'.work_phone,
        '.TABLE_MEMBER.'.mobile_phone
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
    $resultp = @mysql_query($sqlp,$connection) or die("Couldn't execute query.");
    while ($row = mysql_fetch_array($resultp))
      {
        $a_business_name = stripslashes($row['business_name']);
        $a_first_name = stripslashes($row['first_name']);
        $a_last_name = stripslashes($row['last_name']);

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
      }

    $sqlpr = '
      SELECT
        '.TABLE_BASKET.'.product_id,
        '.TABLE_BASKET.'.quantity,
        '.TABLE_BASKET.'.total_weight,
        '.TABLE_BASKET.'.out_of_stock,
        '.TABLE_PRODUCT.'.ordering_unit,
        '.TABLE_PRODUCT.'.pricing_unit,
        '.TABLE_PRODUCT.'.product_name,
        '.TABLE_PRODUCT.'.unit_price,
        '.TABLE_PRODUCT.'.extra_charge,
        '.TABLE_PRODUCT_STORAGE_TYPES.'.storage_code,
        '.TABLE_DELCODE.'.delcode,
        '.TABLE_DELCODE.'.delcode_id,
        '.TABLE_MEMBER.'.first_name,
        '.TABLE_MEMBER.'.last_name,
        '.TABLE_MEMBER.'.business_name,
        '.TABLE_MEMBER.'.member_id,
        '.TABLE_BASKET_ALL.'.deltype
      FROM
        '.TABLE_BASKET.'
      LEFT JOIN '.TABLE_PRODUCT.'
        ON '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id
      LEFT JOIN '.TABLE_PRODUCT_STORAGE_TYPES.'
        ON '.TABLE_PRODUCT_STORAGE_TYPES.'.storage_id = '.TABLE_PRODUCT.'.storage_id
      LEFT JOIN '.TABLE_BASKET_ALL.'
        ON '.TABLE_BASKET_ALL.'.basket_id = '.TABLE_BASKET.'.basket_id
      LEFT JOIN '.TABLE_MEMBER.'
        ON '.TABLE_BASKET_ALL.'.member_id = '.TABLE_MEMBER.'.member_id
      LEFT JOIN '.TABLE_DELCODE.'
        ON '.TABLE_BASKET_ALL.'.delcode_id = '.TABLE_DELCODE.'.delcode_id
      WHERE 
        '.TABLE_PRODUCT.'.producer_id = "'.$producer_id.'"
        AND '.TABLE_PRODUCT.'.hidefrominvoice = 0
        AND ('.TABLE_BASKET_ALL.'.delivery_id = '.$delivery_id.'
          OR '.TABLE_BASKET.'.future_delivery_id = '.$delivery_id.')
      ORDER BY
        '.TABLE_DELCODE.'.delcode,
        '.TABLE_PRODUCT.'.product_name,
        '.TABLE_MEMBER.'.last_name, '.TABLE_MEMBER.'.business_name, '.TABLE_MEMBER.'.first_name';
    $resultpr = @mysql_query($sqlpr) or die("Couldn't execute query 1");
    while ($row = mysql_fetch_array($resultpr))
      {
        $product_id = $row['product_id'];
        $product_name = $row['product_name'];
        $delcode_id = $row['delcode_id'];
        $delcode = $row['delcode'];
        $member_id = $row['member_id'];
        $last_name = $row['last_name'];
        $first_name = $row['first_name'];
        $business_name = $row['business_name'];
        $deltype = $row['deltype'];
        $out_of_stock = $row['out_of_stock'];
        $unit_price = round ($row['unit_price'] * (1 + CUSTOMER_MARKUP), 2);
        $extra_charge = $row['extra_charge'];

        $quantity = $row['quantity'];
        $total_weight = $row['total_weight'];
        $ordering_unit = $row['ordering_unit'];
        $pricing_unit = $row['pricing_unit'];
        $storage_code = $row['storage_code'];

        // Figure out how to display the quantity
        $pricing_per_unit = '';

        if ($unit_price != 0)
          {
            $pricing_per_unit = '$'.number_format ($unit_price, 2).'/'.Inflect::singularize ($pricing_unit);
          }
        if ($unit_price != 0 && $extra_charge != 0)
          {
            $pricing_per_unit .= ' + ';
          }
        if ($extra_charge != 0)
          {
            $pricing_per_unit .= '$'.number_format ($extra_charge, 2).'/'.Inflect::singularize ($ordering_unit);
          }

        if ($out_of_stock == 1)
          {
            $show_quantity = $quantity;
            $show_unit = '<img src="http://www.idahosbounty.org/shop/grfx/checkmark_wht.gif">';
            $pricing_per_unit = 'N/A'; // Clobber the value
          }
        elseif ($quantity)
          {
            $show_quantity = $quantity;
            $show_unit = Inflect::pluralize_if ($show_quantity, $ordering_unit);
          }
        elseif ($total_weight)
          {
            $show_quantity = $total_weight;
            $show_unit = Inflect::pluralize_if ($show_quantity, $pricing_unit);
          }

        // If there's no last name, then use the business name
        if(!$last_name)
          {
            $show_mem = $business_name;
          }
        else
          {
            $show_mem = "$last_name, $first_name";
          }

        // Set up primary data structure
        $summary_qty[$delcode_id][$product_id][$member_id] = $show_quantity;
        // Configure deltype to only show when order is a delivery
        if ($deltype != 'P')
          {
            $summary_deltype[$member_id] = $deltype.'-'; // Will give something like D-BOISE-117
          }
        else
          {
            $summary_deltype[$member_id] = ''; // Will give something like BOISE-117
          }
        $summary_unit[$product_id] = $show_unit;
        $sordering_unit[$product_id] = $pricing_unit;
        $delcode_subtotal[$delcode_id][$product_id] += $show_quantity;
        $product_subtotal[$product_id] += $show_quantity;

        // Set up trivial data relationships
        $delcode_id_2_delcode[$delcode_id] = $delcode;
        $product_id_2_product_name[$product_id] = $product_name;
        $product_id_2_storage_code[$product_id] = $storage_code;
        $product_id_2_pricing_per_unit[$product_id] = $pricing_per_unit;
        $member_id_2_show_mem[$member_id] = $show_mem;
      }

    if ($a_address_line1 && $a_address_line2)
      {
        $a_address = "$a_address_line1<br>\n$a_address_line2";
      }
    else
      {
        $a_address = $a_address_line1.$a_address_line2;
      }

    $producer_header = '
      <table cellspacing="5">
        <tr>
          <td colspan="2" width="95%"><h3>'.$a_business_name.'</h3></td><td><font size="+1"><strong>Order #'.$delivery_id.' - '.$delivery_date.'</strong></font></td>
        </tr>
        <tr>
          <td rowspan="4" width="35%" valign="top">
            '.$a_address.'<br>
            '.$a_city.', '.$a_state.' '.$a_zip.'<br>
            ('.$a_county.' County)
          </td>
          <td width="15%" align="right"> Email address: </td><td width="45%">'.$a_email_address.'</td></tr>
        <tr><td width="15%" align="right">Home phone: </td><td width="45%">'.$a_home_phone.'</td></tr>
        <tr><td width="15%" align="right">Work phone: </td><td width="45%">'.$a_work_phone.'</td></tr>
        <tr><td width="15%" align="right">Mobile phone: </td><td width="45%">'.$a_mobile_phone.'</td></tr>
      </table><br>';

    if (is_array ($summary_qty))
      {
        $include_header = true;
        $include_footer = true;
        if ($detail_type == '' || $detail_type == 'customer')
          {
            $page_links = '
            <a href="'.$_SERVER['PHP_SELF'].'?detail_type=product">Summary by product</a><br>
            <a href="'.$_SERVER['PHP_SELF'].'?detail_type=labels">Labels for this order</a><br>
            <a href="configure_labels.php">Configure or select label format</a><br><br>
            <h2>Summary for<br>'.$a_business_name.'</h2>
            ';
            $display_page .= '
            <table border="0" cellspacing="0" width="95%">
            ';
            foreach (array_keys ($summary_qty) as $delcode_id)
              {
                $display_page .= '
                <tr><th colspan="4">&nbsp;</th></tr>
                <tr><th colspan="4" bgcolor="#444444"><font size="+1" color="#ffffff" align="center">'.$delcode_id_2_delcode[$delcode_id].' ('.$delcode_id.')</font></td></tr>
                <tr><th colspan="4">&nbsp;</th></tr>
                ';
                foreach (array_keys ($summary_qty[$delcode_id]) as $product_id)
                  {
                    $display_page .= '
                    <tr><td colspan="4"><br>'.$a_business_name.' &ndash; (#'.$product_id.') '.$product_id_2_product_name[$product_id].' ['.$product_id_2_storage_code[$product_id].'] &ndash; '.$product_id_2_pricing_per_unit[$product_id].'</td></tr>
                    ';
                    foreach (array_keys ($summary_qty[$delcode_id][$product_id]) as $member_id)
                      {
                        $quantity = $summary_qty[$delcode_id][$product_id][$member_id];
                        $display_page .= '
                          <tr><td width="5%">&nbsp;</td>
                          <td width="10%">#'.$member_id.'</td>
                          <td width="60%">'.$member_id_2_show_mem[$member_id].'</td>
                          <td width="20%">('.$quantity.') - '.Inflect::pluralize_if ($quantity, $summary_unit[$product_id]).'<br></td></tr>';
                      }
                    // Delivery Code summary
                    $subtotal = $delcode_subtotal[$delcode_id][$product_id];
                    $total = $product_subtotal[$product_id];
                    // Product summary
                    $display_page .= '
                    <tr><td width="5%">&nbsp;</td>
                    <td width="70%" colspan="2" bgcolor="#dddddd">Product quantity ('.$delcode_id_2_delcode[$delcode_id].'): </td>
                    <td width="20%" bgcolor="#dddddd">('.$subtotal.' of '.$total.') - '.Inflect::pluralize_if ($total, $summary_unit[$product_id]).'</td></tr>
                    ';
                  }
              }
            //       $display_page .= '<hr width="50%" style="text-align:left;margin:3em 0em 3em;">';
            $display_page .= '</table>';

            if ($use == 'batch')
              {
                $display_page = $producer_header.$display_page;
              }
            else
              {
                $display_page = '</font><div style="font-size:0.9em;">'.$page_links.$producer_header.$display_page;
              }
          }


        elseif ($detail_type == 'product')
          {
            $include_header = true;
            $include_footer = true;
            $page_links = '
            <a href="'.$_SERVER['PHP_SELF'].'?detail_type=customer">Summary by customer</a><br>
            <a href="'.$_SERVER['PHP_SELF'].'?detail_type=labels">Labels for this order</a><br>
            <a href="configure_labels.php">Configure or select label format</a><br><br>
            <h2>Overall Product Summary for<br>'.$a_business_name.'</h2>
            ';

            foreach (array_keys ($product_id_2_storage_code) as $product_id)
              {
                // Delivery Code summary
                $total = $product_subtotal[$product_id];
                $unit = $summary_unit[$product_id];
                // Product summary
                $display_page .= '<div style="width:40em;float:left;">'.$product_id_2_product_name[$product_id].' (#'.$product_id.') ['.$product_id_2_storage_code[$product_id].']</div><div style="width:10em;float:left;">('.$total.') - '.Inflect::pluralize_if ($total, $unit).'</div><br>';
              }

            if ($use == 'batch') {
                $display_page = $producer_header.$display_page;
              }
            else {
                $display_page = '</font><div style="font-size:0.9em;">'.$page_links.$producer_header.$display_page;
              }
          }


        elseif ($detail_type == 'labels')
          {
            require_once ("../func/label_config.class.php");

            // Choose the labels that were selected from configure_labels.php
            $label_name = $_SESSION['label_select'];

            // Set up the label based on stored cookie label values
            $current_label = output_Label::cookieToLabel ($label_name);


            if ($label_name)
              {
                // If a printer has been chosen, then include label styles
                $label_sheet_styles .= '
                  .container {
                    overflow:hidden;
                    width:100%;
                    height:100%
                    }
                  '.$current_label->getLabelCSS();
                // Set up font scaling
                $font_scaling = $current_label->font_scaling;
                if (! $font_scaling) { $font_scaling = 1.0; };
                $font_scaling_link = ''; // Scaling is automatic, so not controls are given
              }
            else
              {
                // Otherwise include a simple spacer style between labels
                $label_sheet_styles .= '
                  .container {
                    margin-bottom: 3em;
                    }
                  a {
                    text-decoration: none;
                    color:#880088;
                    }
                  a:hover {
                    text-decoration: underline;
                    color:#0000ff;
                    }
                  ';
                // Set up font scaling
                $font_scaling = $_GET['font_scaling'];
                if (! $font_scaling) $font_scaling = 1.0;
                if ($font_scaling < 0.3) $font_scaling = 0.3;
                if ($font_scaling > 4.0) $font_scaling = 4.0;
                // Controls for scaling the label
                $font_scaling_link = 'A custom label-sheet is NOT selected.<br>
                  Click <a href="configure_labels.php">here</a> to configure custom labels (i.e. Avery labels)<br>
                  Change label size: 
                  [<a href="'.$_SERVER['PHP_SELF'].'?detail_type=labels&font_scaling='.($font_scaling - 0.1).'">Smaller</a>]
                  [<a href="'.$_SERVER['PHP_SELF'].'?detail_type=labels&font_scaling='.($font_scaling + 0.1).'">Larger</a>]
                  <br><br><br>';
              }

            // Include the header and styles for this particular application
            $label_sheet .= '
              <head>
              <style>
              .counter {
                float:left;
                font-size:'.number_format (3 * $font_scaling, 2).'em;
                font-weight:bold;
                }
              .delcode {
                font-size:'.number_format (1.2 * $font_scaling, 2).'em;
                font-weight:bold;
                }
              .customer {
                font-size:'.number_format (1.0 * $font_scaling, 2).'em;
                }
              .producer {
                font-size:'.number_format (1.0 * $font_scaling, 2).'em;
                }
              .product {
                font-size:'.number_format (0.9 * $font_scaling, 2).'em;
                font-style:italic;
                line-height:100%;
                }
              '.$label_sheet_styles;

            // Close the header and open the body
            $label_sheet .= '
              </style>
              </head>
              <body>'.$font_scaling_link;

            // Begin the label sheet content
            $label_sheet .= $current_label->beginLabelSheet();

            foreach (array_keys ($product_id_2_product_name) as $product_id)
              {
                foreach (array_keys ($summary_qty) as $delcode_id)
                  {
                    if (is_array ($summary_qty[$delcode_id][$product_id]))
                      {
                        foreach (array_keys ($summary_qty[$delcode_id][$product_id]) as $member_id)
                          {
                            $quantity = $summary_qty[$delcode_id][$product_id][$member_id];
                            $deltype = $summary_deltype[$member_id];
                            $unit = $summary_unit[$product_id];
                            $label_sheet .= '
                              <div class="container">
                              <div class="delcode">'.$deltype.$delcode_id.'-'.$member_id.'['.$product_id_2_storage_code[$product_id].']</div>
                              <div class="customer">'.$member_id_2_show_mem[$member_id].'</div>
                              <div class="producer">'.$a_business_name.'</div>
                              <div class="product">('.$quantity.') - '.Inflect::pluralize_if ($quantity, $summary_unit[$product_id]).$product_id_2_product_name[$product_id].' (#'.$product_id.')</div>
                              </div>';
                            $label_sheet .= $current_label->advanceLabel();
                          }
                      }
                  }
              }
            $label_sheet .= $current_label->finishLabelSheet();
            // Finally, just before printing, clear the $_SESSION['label_select']
            // variable so the next use of this function will require choosing a
            // label type again.
            //    unset ($_SESSION['label_select']);
            $display_page .= $label_sheet;
          }
      }

    else
      {
        $include_header = true;
        $include_footer = true;
        $display_page .= "</font>";
        $display_page .= '<div style="font-size:0.9em;">';
        $display_page .= "<h2>No products to report</h2><br>\n";
        $display_page .= '<div>';
      }

    return $display_page;
  }
