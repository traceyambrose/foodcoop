<?php

function new_changed_products($new_or_changed)
  {
    global $connection;
    include_once ("config_foodcoop.php");
    $display_type = "new_or_changed";
    if( $new_or_changed == 'new' )
      {
        $andstatement = 'AND '.TABLE_PRODUCT_PREP.'.new = "1"';
      }
    elseif ($new_or_changed=="changed")
      {
        $andstatement = 'AND '.TABLE_PRODUCT_PREP.'.changed = "1"';
      }
    else
      {
        $andstatement = '';
      }
    $sql = '
      SELECT
        '.TABLE_CATEGORY.'.*,
        '.TABLE_SUBCATEGORY.'.*,
        '.TABLE_PRODUCT_PREP.'.subcategory_id,
        '.TABLE_PRODUCT_PREP.'.donotlist,
        '.TABLE_PRODUCT_PREP.'.new,
        '.TABLE_PRODUCT_PREP.'.changed
      FROM
        '.TABLE_CATEGORY.',
        '.TABLE_SUBCATEGORY.',
        '.TABLE_PRODUCT_PREP.',
        '.TABLE_PRODUCER.'
      WHERE
        '.TABLE_CATEGORY.'.category_id = '.TABLE_SUBCATEGORY.'.category_id
        AND '.TABLE_SUBCATEGORY.'.subcategory_id = '.TABLE_PRODUCT_PREP.'.subcategory_id
        AND '.TABLE_PRODUCT_PREP.'.donotlist = "0"
        AND '.TABLE_PRODUCT_PREP.'.producer_id = '.TABLE_PRODUCER.'.producer_id
        AND '.TABLE_PRODUCER.'.pending = "0"
        AND '.TABLE_PRODUCER.'.donotlist_producer = "0"
        '.$andstatement.'
      GROUP BY
        '.TABLE_PRODUCT_PREP.'.subcategory_id
      ORDER BY
        sort_order ASC,
        subcategory_name ASC';
    $rs = @mysql_query($sql, $connection) or die(mysql_error());
    while ( $row = mysql_fetch_array($rs) )
      {
        $category_id = $row['category_id'];
        $category_name = stripslashes($row['category_name']);
        $subcategory_id = $row['subcategory_id'];
        $subcategory_name = stripslashes($row['subcategory_name']);
        if ( $current_category_id < 0 )
          {
            $current_category_id = $row['category_id'];
          }
        while ( $current_category_id != $category_id )
          {
            $current_category_id = $category_id;
            $display .= '<div align="right"><font size="-1">
              [ <a href="index.php">Return to main page</a> |
              <a href="logout.php">Logout</a> ]</font></div>';
            $display .= '<hr>';
            $display .= $current_subtotal;
            $display .= '<h2>'.$category_name.'</h2>';
          }
        $display .= '<a name="sub'.$subcategory_id.'"><h3>'.$subcategory_name.'</h3>';
        $sqlp = '
          SELECT
            '.TABLE_PRODUCT_PREP.'.*,
            '.TABLE_PRODUCER.'.producer_id,
            '.TABLE_PRODUCER.'.member_id,
            '.TABLE_MEMBER.'.member_id,
            '.TABLE_MEMBER.'.business_name,
            '.TABLE_MEMBER.'.first_name,
            '.TABLE_MEMBER.'.last_name
          FROM
            '.TABLE_PRODUCT_PREP.',
            '.TABLE_PRODUCER.',
            '.TABLE_MEMBER.'
          WHERE '.TABLE_PRODUCT_PREP.'.subcategory_id = "'.$subcategory_id.'"
            AND '.TABLE_PRODUCT_PREP.'.producer_id = '.TABLE_PRODUCER.'.producer_id
            AND '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
            AND '.TABLE_PRODUCT_PREP.'.donotlist = "0"
            AND '.TABLE_PRODUCER.'.pending = "0"
            AND '.TABLE_PRODUCER.'.donotlist_producer = "0"
            '.$andstatement.'
          GROUP BY
            '.TABLE_PRODUCT_PREP.'.producer_id
          ORDER BY
            '.TABLE_MEMBER.'.business_name';
        $resultp = @mysql_query($sqlp,$connection) or die(mysql_error());
        while ( $row = mysql_fetch_array($resultp) )
          {
            $producer_id = $row['producer_id'];
            $business_name = stripslashes($row['business_name']);
            $first_name = stripslashes($row['first_name']);
            $last_name = stripslashes($row['last_name']);
            if ( !$business_name )
              {
                $business_name = "$first_name $last_name";
              }
            if ( $current_producer_id < 0 )
              {
                $current_producer_id = $row['producer_id'];
              }
            while ( $current_producer_id != $producer_id )
              {
                $current_producer_id = $producer_id;
              }
            $display .= '<ul>';
            $display .= '<font color="#770000"><h3>'.$business_name.'</h3></font>';
            include("display_product_table_start.php");
            $sql = '
              SELECT *
              FROM
                '.TABLE_PRODUCT_PREP.',
                 '.TABLE_PRODUCT_TYPES.'
              WHERE
                '.TABLE_PRODUCT_PREP.'.subcategory_id = "'.$subcategory_id.'"
                AND '.TABLE_PRODUCT_PREP.'.producer_id = "'.$producer_id.'"
                AND '.TABLE_PRODUCT_PREP.'.prodtype_id = '.TABLE_PRODUCT_TYPES.'.prodtype_id
                AND '.TABLE_PRODUCT_PREP.'.donotlist != "1"
                '.$andstatement.'
              ORDER BY
                product_name ASC,
                unit_price ASC';

            $result = @mysql_query($sql,$connection) or die(mysql_error());
            while ( $row = mysql_fetch_array($result) )
              {
                include("display_productinfo.php");
                if( $new_or_changed == 'changed' )
                  {
                    $sqlo2 = '
                      SELECT *
                      FROM
                        '.TABLE_PRODUCT_PREV.',
                        '.TABLE_PRODUCT_TYPES.'
                      WHERE
                        '.TABLE_PRODUCT_PREV.'.product_id = "'.$product_id.'"
                        AND '.TABLE_PRODUCT_PREV.'.prodtype_id = '.TABLE_PRODUCT_TYPES.'.prodtype_id
                      ORDER BY
                        '.TABLE_PRODUCT_PREV.'.product_name ASC,
                        '.TABLE_PRODUCT_PREV.'.unit_price ASC
                      LIMIT 1';
                    $result2 = @mysql_query($sqlo2,$connection) or die(mysql_error());
                    while ( $row = mysql_fetch_array($result2) )
                      {
                        $product_name_old = $row['product_name'];
                        $unit_price_old = $row['unit_price'];
                        $pricing_unit_old = $row['pricing_unit'];
                        $ordering_unit_old = $row['ordering_unit'];
                        $prodtype_id_old = $row['prodtype_id'];
                        $prodtype_old = $row['prodtype'];
                        $random_weight_old = $row['random_weight'];
                        $meat_weight_type_old = $row['meat_weight_type_old'];
                        $minimum_weight_old = $row['minimum_weight'];
                        $maximum_weight_old = $row['maximum_weight'];
                        $extra_charge_old = $row['extra_charge'];
                        $donotlist_old = $row['donotlist'];
                        $detailed_notes_old = $row['detailed_notes'];
                        if( $donotlist_old == 1 )
                          {
                            $display_donot = '<br><font color="#FF0000">(Previously unlisted.)</font>';
                          }
                        else
                          {
                            $display_donot = '';
                          }

                        if( $minimum_weight_old == $maximum_weight_old )
                          {
                            $minmax_old = $minimum_weight_old.' '.Inflect::pluralize_if ($maximum_weight_old, $pricing_unit_old);
                          }
                        else
                          {
                            $minmax_old = $minimum_weight_old.' - '.$maximum_weight_old.' '.Inflect::pluralize_if ($maximum_weight_old, $pricing_unit_old);
                          }

                        if( $random_weight_old )
                          {
                            $show_weight_old = 'You will be billed for exact '.$meat_weight_type_old.' weight (approx. $minmax_old)';
                          }
                        else
                          {
                            $show_weight_old = '';
                          }

                        if( $extra_charge_old )
                          {
                            $extra_old = '<br>Extra charge: $'.number_format($extra_charge_old, 2).'/'.$ordering_unit_old;
                          }
                        else
                          {
                            $extra_old = '';
                          }

                        $display .= '
                            <tr>
                              <td valign="center"><a name="'.$product_id.'">OLD LISTING '.$display_donot.'</td>
                              <td><b># '.$product_id.'</b></td>
                              <td><b>'.stripslashes($product_name_old).'</b><br>
                                <font size="-1">Order number of '.Inflect::pluralize ($ordering_unit_old).' '.$show_weight_old.' '.stripslashes($detailed_notes_old).' '.$extra_old.'</font></td>';

                        if ($prodtype_id_old == 1)
                          {
                            $display .= '
                              <td><font size="-1">'.$prodtype_old.'</font></td>';
                          }
                         else
                          {
                            $display .= '
                              <td><font size="-1"></font></td>';
                          }

                        $display .= '
                              <td>$'.number_format($unit_price_old, 2).'/'.$pricing_unit_old.'</td>';
                        $display .= '
                            </tr>';
                        $display .= '
                            <tr>
                              <td colspan=5>
                                <form action="list_prodchanged.php#sub'.$subcategory_id.'" method="post">
                                <input type=hidden name="product_id_passed" value="'.$product_id.'">
                                <input type=hidden name="subcategory_id" value="'.$subcategory_id.'">
                                <input type=hidden name="updatelisting" value="yes">
                                <input type=submit name=where value="Click to take #'.$product_id.' off Changed List">
                                </form>
                              </td>
                            </tr>';
                      }
                  }
              }
            $display .= '
                          </table>';
            $display .= '
                        </ul>';
          }
      }
    return $display;
  }
