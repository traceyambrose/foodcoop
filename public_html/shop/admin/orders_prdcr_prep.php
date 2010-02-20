<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

$total = 0;
$prod_sum = 0;
$sqlp = '
  SELECT
    '.TABLE_PRODUCER.'.producer_id,
    '.TABLE_PRODUCER.'.member_id,
    '.TABLE_MEMBER.'.member_id,
    '.TABLE_MEMBER.'.business_name,
    '.TABLE_MEMBER.'.first_name,
    '.TABLE_MEMBER.'.last_name,
    '.TABLE_BASKET.'.product_id,
    '.TABLE_PRODUCT.'.producer_id,
    '.TABLE_PRODUCT.'.product_id
  FROM
    '.TABLE_PRODUCER.',
    '.TABLE_MEMBER.',
    '.TABLE_BASKET.',
    '.TABLE_PRODUCT.'
  WHERE
    '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id
    AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
    AND '.TABLE_PRODUCER.'.producer_id = "'.$producer_id.'"
    AND '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
  GROUP BY
    '.TABLE_PRODUCER.'.producer_id
  ORDER BY
    business_name ASC,
    last_name ASC';
$resultp = @mysql_query($sqlp,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
while ( $row = mysql_fetch_array($resultp) )
  {
    $a_business_name = stripslashes ($row['business_name']);
    $a_first_name = $row['first_name'];
    $a_last_name = $row['last_name'];
    if ( !$a_business_name )
      {
        $a_business_name = $a_first_name.' '.$a_last_name;
      }
  }
$sql = '
  SELECT
    '.TABLE_BASKET.'.basket_id,
    '.TABLE_BASKET.'.product_id,
    '.TABLE_BASKET.'.item_price,
    '.TABLE_BASKET_ALL.'.basket_id,
    '.TABLE_BASKET_ALL.'.delivery_id,
    '.TABLE_PRODUCT.'.product_id,
    '.TABLE_PRODUCER.'.producer_id,
    '.TABLE_PRODUCT.'.product_name,
    '.TABLE_PRODUCT.'.pricing_unit
  FROM
    '.TABLE_BASKET.',
    '.TABLE_BASKET_ALL.',
    '.TABLE_PRODUCT.',
    '.TABLE_PRODUCER.'
  WHERE
    '.TABLE_BASKET.'.basket_id = '.TABLE_BASKET_ALL.'.basket_id
    AND ('.TABLE_BASKET_ALL.'.delivery_id = "'.$current_delivery_id.'"
    OR '.TABLE_BASKET.'.future_delivery_id = "'.$current_delivery_id.'")
    AND '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id
    AND '.TABLE_PRODUCT.'.producer_id = "'.$producer_id.'"
  GROUP BY
    '.TABLE_BASKET.'.product_id
  ORDER BY
    product_name ASC';
$rs = @mysql_query($sql,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
while ( $row = mysql_fetch_array($rs) )
  {
    $basket_id = $row['basket_id'];
    $product_id = $row['product_id'];
    $product_name = $row['product_name'];
    $item_price = $row['item_price'];
    $pricing_unit = $row['pricing_unit'];
    $display .= '
      <tr bgcolor="#DDDDDD">
        <td colspan="9"><a name="'.$product_id.'"><font size=4>'.$product_name.' (Product ID# '.$product_id.') </font><b>$'.number_format($item_price, 2).'/'.$pricing_unit.'</b>.</td>
      </tr>';
    $total_pr = 0;
    $subtotal_pr = 0;
    $sql = '
      SELECT
        '.TABLE_BASKET.'.*,
        '.TABLE_BASKET_ALL.'.*,
        '.TABLE_BASKET_ALL.'.deltype as ddeltype,
        '.TABLE_MEMBER.'.*,
        '.TABLE_PRODUCT.'.product_name,
        '.TABLE_PRODUCT.'.random_weight,
        '.TABLE_PRODUCT.'.ordering_unit,
        '.TABLE_PRODUCT.'.extra_charge,
        '.TABLE_PRODUCT.'.pricing_unit,
        '.TABLE_BASKET.'.future_delivery_id,
        '.TABLE_SUBCATEGORY.'.subcategory_id,
        '.TABLE_SUBCATEGORY.'.category_id,
        '.TABLE_PRODUCT.'.subcategory_id
      FROM
        '.TABLE_BASKET.',
        '.TABLE_PRODUCT.',
        '.TABLE_BASKET_ALL.',
        '.TABLE_MEMBER.',
        '.TABLE_SUBCATEGORY.'
      WHERE
        '.TABLE_BASKET.'.product_id = "'.$product_id.'"
        AND '.TABLE_BASKET_ALL.'.member_id = '.TABLE_MEMBER.'.member_id
        AND
          (
            '.TABLE_BASKET_ALL.'.delivery_id = "'.$current_delivery_id.'"
            OR '.TABLE_BASKET.'.future_delivery_id = "'.$current_delivery_id.'"
          )
        AND '.TABLE_BASKET_ALL.'.basket_id = '.TABLE_BASKET.'.basket_id
        AND '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id
        AND '.TABLE_PRODUCT.'.producer_id = "'.$producer_id.'"
        AND '.TABLE_PRODUCT.'.subcategory_id = '.TABLE_SUBCATEGORY.'.subcategory_id
      GROUP BY
        '.TABLE_BASKET.'.basket_id
      ORDER BY
        '.TABLE_BASKET.'.basket_id ASC';
    $resultpr = @mysql_query($sql,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    while ( $row = mysql_fetch_array($resultpr) )
      {
        $basket_id = $row['basket_id'];
        $product_id = $row['product_id'];
        $category_id = $row['category_id'];
        $product_name = $row['product_name'];
        $quantity = $row['quantity'];
        $random_weight = $row['random_weight'];
        $total_weight = $row['total_weight'];
        $out_of_stock = $row['out_of_stock'];
        $ordering_unit = $row['ordering_unit'];
        $extra_charge = $row['extra_charge'];
        $delcode_id = $row['delcode_id'];
        $ddeltype = $row['ddeltype'];
        $future_delivery_id = $row['future_delivery_id'];
        $notes = $row['customer_notes_to_producer'];
        $member_id = $row['member_id'];
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $business_name = stripslashes ($row['business_name']);
        $mem_taxexempt = $row['mem_taxexempt'];
        if ( (!$last_name) || (!$first_name) )
          {
            $show_mem = $business_name;
          }
        else
          {
            $show_mem = $first_name.' '.$last_name;
          }
        if ( $out_of_stock == 1 )
          {
            $display_total_price = '$'.number_format(0, 2);
          }
        if ( $future_delivery_id == $current_delivery_id )
          {
            $display_weight = '';
            $total_pr = $total_pr + $quantity;
            $item_total_price = 0;
            $display_total_price = '<font color="#FF0000">Invoiced in a previous order</font>';
          }
        elseif ( $out_of_stock != 1 )
          {
            $total_pr = $total_pr + $quantity;
            if ( $random_weight == 1 )
              {
                if ( $total_weight == 0 )
                  {
                    //$display_weight = "$total_weight ".$pricing_unit."s";
                    $display_weight = "$total_weight ".$pricing_unit;
                    $item_total_3dec = number_format(($item_price * $total_weight), 3) + 0.00000001;
                    $item_total_price = round($item_total_3dec, 2);
                    $display_total_price = '$'.number_format($item_total_price, 2);
                    $message_incomplete = '<font color="#770000">Order Incomplete<font>';
                  }
                else
                  {
                    //$display_weight = "$total_weight ".$pricing_unit."s";
                    $display_weight = "$total_weight ".$pricing_unit;
                    $item_total_3dec = number_format((($item_price * $total_weight) + $extra_charge),3) + 0.00000001;
                    $item_total_price = round($item_total_3dec, 2);
                    $display_total_price = '$'.number_format($item_total_price, 2);
                  }
              }
            else
              {
                $display_weight = '';
                $item_total_3dec = number_format((($item_price * $quantity) + $extra_charge), 3) + 0.00000001;
                $item_total_price = round($item_total_3dec, 2);
                $display_total_price = '$'.number_format($item_total_price, 2);
              }
          }
        else
          {
            $total_pr = $total_pr + 0;
            $display_weight = '';
            $show_update_button = 'no';
            $item_total_price = 0;
          }
        if ( $extra_charge )
          {
            $display_charge = '$'.number_format($extra_charge, 2);
          }
        else
          {
            $display_charge = '';
          }
        if ( $out_of_stock )
          {
            $display_outofstock = '<img src="grfx/checkmark_wht.gif"><br>';
          }
        else
          {
            $display_outofstock = '';
          }
        if ( $item_total_price )
          {
            $total = $item_total_price + $total;
          }
        $subtotal_pr = $subtotal_pr + $item_total_price;
        if ( $notes )
          {
            $display_notes = '<br>Customer note: '.$notes;
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
        if ( $current_product_id < 0 )
          {
            $current_product_id = $row['product_id'];
          }
        while ( $current_product_id != $product_id )
          {
            $current_product_id = $product_id;
          }
        $taxcalcs = '';
        $copo_city = '';
        $copo_county = '';
        if ( $ddeltype == 'P' )
          {
            $sqlz = '
              SELECT
                '.TABLE_DELCODE.'.delcode_id,
                '.TABLE_DELCODE.'.copo_city,
                '.TABLE_SALES_TAX.'.*
              FROM
                '.TABLE_DELCODE.',
                '.TABLE_SALES_TAX.'
              WHERE
                '.TABLE_DELCODE.'.copo_city = '.TABLE_SALES_TAX.'.copo
                AND '.TABLE_DELCODE.'.delcode_id = "'.$delcode_id.'"';
            $resultz = @mysql_query($sqlz,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
            while ( $row = mysql_fetch_array($resultz) )
              {
                $city_county = $row['city_county'];
                $city_tax = $row['current_rate'];
                $copo_city = $row['copo_city'];
              }
          }
        if ( $ddeltype == 'W' )
          {
            $sqlz = '
              SELECT
                '.TABLE_MEMBER.'.work_zip,
                '.TABLE_ZIP_CITYTAXNO.'.zip,
                '.TABLE_ZIP_CITYTAXNO.'.copo,
                '.TABLE_SALES_TAX.'.copo,
                 '.TABLE_SALES_TAX.'.current_rate,
                '.TABLE_SALES_TAX.'.city_county
              FROM
                '.TABLE_MEMBER.',
                '.TABLE_ZIP_CITYTAXNO.',
                '.TABLE_SALES_TAX.'
              WHERE
                member_id = "'.$member_id.'"
                AND '.TABLE_MEMBER.'.work_zip = '.TABLE_ZIP_CITYTAXNO.'.zip
                AND '.TABLE_ZIP_CITYTAXNO.'.copo = '.TABLE_SALES_TAX.'.copo';
            $resultz = @mysql_query($sqlz,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
            $numresultz = mysql_numrows($resultz);
            while ( $row = mysql_fetch_array($resultz) )
              {
                $city_county = $row['city_county'];
                $city_tax = $row['current_rate'];
                $copo_city = $row['copo'];
                $zipwh = $row['work_zip'];
              }
            $zipwh = $work_zip;
          }
        else
          {
            if ( $ddeltype == 'H' )
              {
                $sqlz = '
                  SELECT
                    '.TABLE_MEMBER.'.zip,
                    '.TABLE_ZIP_CITYTAXNO.'.zip,
                    '.TABLE_ZIP_CITYTAXNO.'.copo,
                    '.TABLE_SALES_TAX.'.copo,
                     '.TABLE_SALES_TAX.'.current_rate,
                    '.TABLE_SALES_TAX.'.city_county
                  FROM
                    '.TABLE_MEMBER.',
                    '.TABLE_ZIP_CITYTAXNO.',
                    '.TABLE_SALES_TAX.'
                  WHERE
                    member_id = "'.$member_id.'"
                    AND '.TABLE_MEMBER.'.zip = '.TABLE_ZIP_CITYTAXNO.'.zip
                    AND '.TABLE_ZIP_CITYTAXNO.'.copo = '.TABLE_SALES_TAX.'.copo';
                $resultz = @mysql_query($sqlz,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
                $numresultz = mysql_numrows($resultz);
                while ( $row = mysql_fetch_array($resultz) )
                  {
                    $city_county = $row['city_county'];
                    $city_tax = $row['current_rate'];
                    $copo_city = $row['copo'];
                  }
                $zipwh = $zip;
              }
          }
        if ( $copo_city > 0)
          {
            if ( $copo_city < 1000 )
              {
                $copo_county .= substr($copo_city, -3, 1);
              }
            else
              {
                $copo_county .= substr($copo_city, -4, 2);
              }
            $copo_county .= '88';
            $sqlcounty = '
              SELECT
                city_county,
                current_rate
              FROM
                '.TABLE_SALES_TAX.'
              WHERE
                copo = "'.$copo_county.'"';
            $resultcounty = @mysql_query($sqlcounty,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
            while ( $row = mysql_fetch_array($resultcounty) )
              {
                $county_tax = $row['current_rate'];
                $county = $row['city_county'];
              }
          }
        else
          {
            $sqlcounty = '
              SELECT
                '.TABLE_ZIP_COUNTYTAXNO.'.zip,
                '.TABLE_ZIP_COUNTYTAXNO.'.copo,
                '.TABLE_SALES_TAX.'.copo,
                 '.TABLE_SALES_TAX.'.current_rate,
                '.TABLE_SALES_TAX.'.city_county
              FROM
                '.TABLE_MEMBER.',
                '.TABLE_ZIP_COUNTYTAXNO.',
                '.TABLE_SALES_TAX.'
              WHERE
                '.TABLE_ZIP_COUNTYTAXNO.'.zip = "'.$zipwh.'"
                AND '.TABLE_ZIP_COUNTYTAXNO.'.copo = '.TABLE_SALES_TAX.'.copo';
            $resultcounty = @mysql_query($sqlcounty,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
            while ( $row = mysql_fetch_array($resultcounty) )
              {
                $county_tax = $row['current_rate'];
                $county = $row['city_county'];
                $copo_county = $row['copo'];
              }
          }
        if ( $item_total_price )
          {
            if ( $category_id == 25 || $category_id == 26 )
              {
                $total_exempt = $item_total_price;
              }
          }
        $taxable_total = $item_total_price;
        if ( $mem_taxexempt == 1 )
          {
            $total_exempt = $taxable_total;
          }
        $total_exempt = number_format($total_exempt, 2);
        $taxcalcs .= 'Member id: '.$member_id.'<br>';
        $taxcalcs .= 'COPO CITY: '.$copo_city.'<br>';
        $taxcalcs .= 'COPO County: '.$copo_county.'<br>';
        $taxcalcs .= 'Current city rate ('.$city_county.'): '.$city_tax.'<br>';
        $taxcalcs .= 'Current county rate ('.$county.'): '.$county_tax.'<br>';
        $taxcalcs .= 'Tax Exempt total: $'.$total_exempt.'<br>';
        $collected_statetax_4dec = number_format((STATE_TAX * $taxable_total), 4) + 0.00000001;
        $collected_statetax = number_format(round($collected_statetax_4dec, 2), 2);
        $collected_citytax_4dec = number_format(($city_tax * $taxable_total), 4) + 0.00000001;
        $collected_citytax = number_format(round($collected_citytax_4dec, 2), 2);
        $collected_countytax_4dec = number_format(($county_tax * $taxable_total), 4) + 0.00000001;
        $collected_countytax = number_format(round($collected_countytax_4dec, 2), 2);
        if ( (($item_total_price) <= 0) || ($mem_taxexempt == 1) || ($category_id == 25 || $category_id == 26) || $product_id == 1363 )
          {
            $taxable_total = '';
            $sales_tax = '';
            $collected_statetax = '';
            $collected_citytax = '';
            $collected_countytax = '';
          }
        else
          {
            $sales_tax = number_format($collected_statetax + $collected_citytax + $collected_countytax, 2);
          }
        $taxcalcs .= 'Item tax: \$'.$taxable_total.'<br>';
        $taxcalcs .= 'Collected State Tax: $'.$collected_statetax.'<br>';
        $taxcalcs .= 'Collected City Tax: $'.$collected_citytax.'<br>';
        $taxcalcs .= 'Collected County Tax: $'.$collected_countytax.'<br>';
        $taxcalcs .= 'Total Sales Tax: $'.$sales_tax.'<br>';
        if ( $mem_taxexempt == 1 || $category_id == 25 || $category_id == 26 || $product_id == 1363 )
          {
            $sales_tax = '<font size="-2"><br>Tax exempt</font>';
          }
        else
          {
            $sales_tax = '$'.number_format($sales_tax, 2);
          }
        $display .= '
          <tr align="center">
            <td align="right" valign="top"><b># '.$member_id.'</b>&nbsp;&nbsp;</td>
            <td align="left"><b>'.$show_mem.'</b>'.$display_notes.'</td>
            <td align="center">'.$quantity.' '.$display_ordering_unit.'</td>
            <td align="center">'.$display_weight.'</td>
            <td align="center">'.$display_charge.'</td>
            <td align="center">'.$display_outofstock.'</td>
            <td align="center">'.$display_total_price.'</td>
            <td align="center">'.$sales_tax.'</td>
          </tr>';
      }
    $display .= '
          <tr>
            <td colspan="8">Product Quantity: '.$total_pr.' &nbsp;&nbsp;&nbsp;Product subtotal:  $'.number_format($subtotal_pr, 2).'<br><br></td>
          </tr>';
    $prod_sum = $prod_sum + $total_pr;
  }
?>
<html>
<!-- CONTENT BEGINS HERE -->
<table width="100%">
  <tr>
    <td align="left">
      <font size="5"><b><?php echo $a_business_name;?></b> for <?php echo $current_delivery_date;?>: Sorted by Product</font>
      <table cellpadding="4" cellspacing="0" border="0">
        <tr bgcolor="#9CA5B5">
          <th valign="bottom">Member ID</th>
          <th valign="bottom">Member</th>
          <th valign="bottom">Quantity</th>
          <th valign="bottom">Weight</th>
          <th valign="bottom">Extra<br>Charge</th>
          <th valign="bottom">In/Out of Stock</th>
          <th valign="bottom">Total Item Price</th>
          <th valign="bottom">Sales Tax</th>
        </tr>
        <?php echo $display;?>
        <tr>
          <td colspan="6" align="right" valign="top"><b>TOTAL</b></td>
          <td align="center"><b>
<?php
if ( $message_incomplete )
  {
    echo $message_incomplete;
  }
else
  {
    echo "<form action=\"\" method=\"post\">";
    echo "\$".number_format($total, 2)."<br>";
  }
?></b>
          </td>
          <td></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<!-- CONTENT ENDS HERE -->
