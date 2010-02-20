<?php
$date_cycle = date("F Y");
$date_today = date("F d, Y");

if ( $filename == "new" )
  {
    $listtype = "AND ".TABLE_PRODUCT.".new = '1'";
  }
elseif ( $filename == "changed" )
  {
    $listtype = "AND ".TABLE_PRODUCT.".changed = '1'";
  }
else
  {
    $listtype = '';
  }

$display = "<html>
<head>
<title></title>
</head>
<body>
<hr color=#000000 noshade size=2 width=100%>
<h2>".SITE_NAME." $date_cycle List of ".ucfirst($filename)." Products</h2>
<hr color=#000000 noshade size=2 width=100%>";

$font = "<font face=arial size=-1>";
$font2 = "<font face=arial size=-2>";

$display .= "<table border='0' cellpadding='0' cellspacing='5' width='100%'>";

if ( $filename == "deleted" )
  {
    $sql = '
      SELECT
        '.TABLE_CATEGORY.'.*,
        '.TABLE_SUBCATEGORY.'.*,
        '.TABLE_PRODUCT_PREP.'.product_id,
        '.TABLE_PRODUCT_PREP.'.subcategory_id,
        '.TABLE_PRODUCT_PREP.'.donotlist,
        '.TABLE_PRODUCT_PREV.'.donotlist,
        '.TABLE_PRODUCT_PREV.'.product_id
      FROM
        '.TABLE_CATEGORY.',
        '.TABLE_SUBCATEGORY.',
        '.TABLE_PRODUCT_PREP.',
        '.TABLE_PRODUCT_PREV.'
      WHERE
        '.TABLE_CATEGORY.'.category_id = '.TABLE_SUBCATEGORY.'.category_id
        AND '.TABLE_SUBCATEGORY.'.subcategory_id = '.TABLE_PRODUCT_PREP.'.subcategory_id
        AND '.TABLE_PRODUCT_PREP.'.product_id = '.TABLE_PRODUCT_PREV.'.product_id
        AND '.TABLE_PRODUCT_PREP.'.donotlist != 0
        AND '.TABLE_PRODUCT_PREV.'.donotlist = 0
      GROUP BY
        '.TABLE_PRODUCT_PREP.'.subcategory_id
      ORDER BY
        sort_order ASC,
        subcategory_name ASC';
  }
else
  {
    $sql = '
      SELECT
        '.TABLE_CATEGORY.'.*,
        '.TABLE_SUBCATEGORY.'.*,
        '.TABLE_PRODUCT.'.subcategory_id,
        '.TABLE_PRODUCT.'.donotlist,
        '.TABLE_PRODUCT.'.product_id,
        '.TABLE_PRODUCT.'.producer_id,
        '.TABLE_PRODUCER.'.producer_id,
        '.TABLE_PRODUCER.'.donotlist_producer,
        '.TABLE_PRODUCT.'.new,
        '.TABLE_PRODUCT.'.changed
      FROM
        '.TABLE_CATEGORY.',
        '.TABLE_SUBCATEGORY.',
        '.TABLE_PRODUCT.',
        '.TABLE_PRODUCER.'
      WHERE '.TABLE_CATEGORY.'.category_id = '.TABLE_SUBCATEGORY.'.category_id
        AND '.TABLE_SUBCATEGORY.'.subcategory_id = '.TABLE_PRODUCT.'.subcategory_id
        AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
        AND '.TABLE_PRODUCER.'.donotlist_producer != 1
        AND '.TABLE_PRODUCT.'.donotlist = 0
        '.$listtype.'
      GROUP BY
        '.TABLE_PRODUCT.'.subcategory_id
      ORDER BY
        sort_order ASC,
        subcategory_name ASC';
  }
$query  = mysql_query($sql, $connection) or die(STANDARD_ERROR.mysql_error());
while( $row = mysql_fetch_array($query) )
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
        $display .= "<tr><td colspan='4'><font face=arial size='+2'><b>$category_name</b>     </font></td></tr>";
      }
    $display .= "<tr><td colspan='4'><b><font size='+1'>$subcategory_name</b> </font></td></tr>";

    $display .= "<tr>
                <th align='center' width='30'>$font
<div style='border: solid 0 #000; border-top-width:2px; border-bottom-width:2px; padding-left:0.0ex'>
#</div></th>
                <th align='left'>$font
<div style='border: solid 0 #000; border-top-width:2px; border-bottom-width:2px; padding-left:0.0ex'> Product</div></th>
                <th align='center'>$font
<div style='border: solid 0 #000; border-top-width:2px; border-bottom-width:2px; padding-left:0.0ex'>
Type</div></th>
                <th align='center'>$font
<div style='border: solid 0 #000; border-top-width:2px; border-bottom-width:2px; padding-left:0.0ex'>
Price</div></th>
    </tr>";

    if ( $filename == "deleted" )
      {
        $sqlp = '
          SELECT
            '.TABLE_PRODUCT_PREP.'.subcategory_id,
            '.TABLE_PRODUCT_PREP.'.producer_id,
            '.TABLE_PRODUCER.'.producer_id,
            '.TABLE_PRODUCER.'.member_id,
            '.TABLE_MEMBER.'.member_id,
            '.TABLE_MEMBER.'.business_name,
            '.TABLE_MEMBER.'.first_name,
            '.TABLE_MEMBER.'.last_name,
            '.TABLE_PRODUCT_PREP.'.donotlist,
            '.TABLE_PRODUCT_PREV.'.donotlist,
            '.TABLE_PRODUCT_PREP.'.product_id,
            '.TABLE_PRODUCT_PREV.'.product_id
          FROM
            '.TABLE_PRODUCT_PREP.',
            '.TABLE_PRODUCER.',
            '.TABLE_MEMBER.',
            '.TABLE_PRODUCT_PREV.'
          WHERE
            '.TABLE_PRODUCT_PREP.'.subcategory_id = '.$subcategory_id.'
            AND '.TABLE_PRODUCT_PREP.'.producer_id = '.TABLE_PRODUCER.'.producer_id
            AND '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
            AND '.TABLE_PRODUCT_PREP.'.product_id = '.TABLE_PRODUCT_PREV.'.product_id
            AND '.TABLE_PRODUCT_PREP.'.donotlist != 0
            AND '.TABLE_PRODUCT_PREV.'.donotlist = 0
            AND '.TABLE_PRODUCER.'.donotlist_producer != 1
          GROUP BY
            '.TABLE_PRODUCT_PREP.'.producer_id
          ORDER BY
            '.TABLE_MEMBER.'.business_name';
      }
    else
      {
        $sqlp = '
          SELECT
            '.TABLE_PRODUCT.'.product_id,
            '.TABLE_PRODUCT.'.subcategory_id,
            '.TABLE_PRODUCT.'.producer_id,
            '.TABLE_PRODUCER.'.producer_id,
            '.TABLE_PRODUCER.'.member_id,
            '.TABLE_MEMBER.'.member_id,
            '.TABLE_MEMBER.'.business_name,
            '.TABLE_MEMBER.'.first_name,
            '.TABLE_MEMBER.'.last_name,
            '.TABLE_PRODUCT.'.donotlist,
            '.TABLE_PRODUCT.'.new,
            '.TABLE_PRODUCT.'.changed,
            '.TABLE_PRODUCER.'.donotlist_producer
          FROM
            '.TABLE_PRODUCT.',
            '.TABLE_PRODUCER.',
            '.TABLE_MEMBER.'
          WHERE
            '.TABLE_PRODUCT.'.subcategory_id = '.$subcategory_id.'
            AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
            AND '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
            AND '.TABLE_PRODUCER.'.donotlist_producer != 1
            AND '.TABLE_PRODUCT.'.donotlist = 0
            '.$listtype.'
          GROUP BY
            '.TABLE_PRODUCT.'.producer_id
          ORDER BY
            '.TABLE_MEMBER.'.business_name';
      }

    $queryp  = mysql_query($sqlp, $connection) or die(STANDARD_ERROR.mysql_error());
    while ( $row = mysql_fetch_array($queryp) )
      {
        $producer_id = $row['producer_id'];
        $business_name =  stripslashes ($row['business_name']);
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];

        if ( !$business_name ) {
          $business_name = "$first_name $last_name";
        }
        $display .= "<tr><td colspan='4'><font size='+1'><b>$business_name</b></td></tr>";

        if ( $filename == "deleted" )
          {
            $sql3 = '
              SELECT
                '.TABLE_PRODUCT_PREP.'.product_id,
                '.TABLE_PRODUCT_PREP.'.donotlist,
                '.TABLE_PRODUCT_TYPES.'.*,
                '.TABLE_PRODUCT_PREV.'.subcategory_id,
                '.TABLE_PRODUCT_PREV.'.prodtype_id,
                '.TABLE_PRODUCT_PREV.'.donotlist,
                '.TABLE_PRODUCT_PREV.'.product_name,
                '.TABLE_PRODUCT_PREP.'.producer_id,
                '.TABLE_PRODUCT_PREV.'.product_id,
                '.TABLE_PRODUCT_PREP.'.producer_id,
                '.TABLE_PRODUCT_PREV.'.unit_price,
                '.TABLE_PRODUCT_PREV.'.pricing_unit,
                '.TABLE_PRODUCT_PREV.'.ordering_unit,
                '.TABLE_PRODUCT_PREV.'.extra_charge,
                '.TABLE_PRODUCT_PREV.'.detailed_notes
              FROM
                '.TABLE_PRODUCT_PREP.',
                '.TABLE_PRODUCT_TYPES.',
                '.TABLE_PRODUCT_PREV.'
              WHERE
                '.TABLE_PRODUCT_PREP.'.subcategory_id = '.$subcategory_id.'
                AND '.TABLE_PRODUCT_PREP.'.producer_id = "'.$producer_id.'"
                AND '.TABLE_PRODUCT_PREP.'.product_id = '.TABLE_PRODUCT_PREV.'.product_id
                AND '.TABLE_PRODUCT_PREV.'.prodtype_id = '.TABLE_PRODUCT_TYPES.'.prodtype_id
                AND '.TABLE_PRODUCT_PREP.'.donotlist != 0
                AND '.TABLE_PRODUCT_PREV.'.donotlist = 0
              ORDER BY
                '.TABLE_PRODUCT_PREV.'.product_name ASC';
          }
        else
          {
            $sql3 = '
              SELECT
                subcategory_id,
                product_id,
                product_name,
                unit_price,
                donotlist,
                producer_id,
                pricing_unit,
                ordering_unit,
                '.TABLE_PRODUCT.'.prodtype_id,
                '.TABLE_PRODUCT_TYPES.'.prodtype_id,
                prodtype,
                extra_charge,
                detailed_notes,
                random_weight,
                minimum_weight,
                maximum_weight,
                meat_weight_type,
                image_id
              FROM
                '.TABLE_PRODUCT.',
                '.TABLE_PRODUCT_TYPES.'
              WHERE
                '.TABLE_PRODUCT.'.subcategory_id = '.$subcategory_id.'
                AND '.TABLE_PRODUCT.'.producer_id = "'.$producer_id.'"
                AND '.TABLE_PRODUCT.'.prodtype_id = '.TABLE_PRODUCT_TYPES.'.prodtype_id
                AND '.TABLE_PRODUCT.'.donotlist = 0
                '.$listtype.'
              ORDER BY
                product_name ASC,
                unit_price ASC';
          }
        $result = @mysql_query($sql3,$connection) or die("Couldn't execute search query.");
        while ($row3 = mysql_fetch_object($result))
          {
            $subcategory_id = $row3->subcategory_id;
            $product_id = $row3->product_id;
            $product_name = $row3->product_name;
            $unit_price = $row3->unit_price;
            $donotlist = $row3->donotlist;
            $producer_id = $row3->producer_id;
            $pricing_unit = $row3->pricing_unit;
            $ordering_unit = $row3->ordering_unit;
            $prodtype_id = $row3->prodtype_id;
            $prodtype = $row3->prodtype;
            $extra_charge = $row3->extra_charge;
            $detailed_notes = $row3->detailed_notes;
            $random_weight = $row3->random_weight;
            $minimum_weight = $row3->minimum_weight;
            $maximum_weight = $row3->maximum_weight;
            $meat_weight_type = $row3->meat_weight_type;
            $image_id = $row3->image_id;
            if ($current_product_id < 0)
              {
                $current_product_id = $row3->product_id;
              }
            while ($current_product_id != $product_id)
              {
                $current_product_id = $product_id;
                include("func/display_productinfo_public.php");
              }
          }
      }
  }
$display .= '</table>';

$display .= '</body></html>';
?>