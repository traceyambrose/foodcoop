<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();
$date_today = date("F j, Y");


$sql1 = '
  SELECT
    product_id,
    retail_staple
  FROM
    '.TABLE_PRODUCT_PREP.'';
$result1 = @mysql_query($sql1,$connection) or die("".mysql_error()."");
while ( $row = mysql_fetch_array($result1) )
  {
    $product_id = $row['product_id'];
    $retail_staple = $row['retail_staple'];

    $sql2 = '
      UPDATE
        '.TABLE_BASKET.'
      SET
        retail_staple = "'.$retail_staple.'"
      WHERE
        product_id = "'.$product_id.'"';
    $result2 = @mysql_query($sql2,$connection) or die("".mysql_error()."");
  }


$sqld = '
  SELECT
    delivery_id
  FROM
    '.TABLE_DELDATE.'
  ORDER BY
    delivery_id DESC';
$rsd = @mysql_query($sqld,$connection) or die("Couldn't execute query.");
while ( $row = mysql_fetch_array($rsd) )
  {
    $delivery_id = $row['delivery_id'];
    $cycle_total_m = "";
    $cycle_total_p = "";
    $cycle_total_e = "";
    $cycle_total1 = "";
    $cycle_total2 = "";
    $cycle_total3 = "";
    $display_total1 = "";
    $display_total2 = "";
    $display_total3 = "";
    $display_total_m = "";
    $display_total_p = "";
    $display_total_e = "";
    $display_bigtotal = "";

    $sql = '
      SELECT
        '.TABLE_BASKET.'.retail_staple,
        '.TABLE_BASKET.'.item_price,
        '.TABLE_BASKET.'.quantity,
        '.TABLE_BASKET.'.basket_id,
        '.TABLE_BASKET_ALL.'.basket_id,
        '.TABLE_BASKET_ALL.'.delivery_id,
        '.TABLE_BASKET.'.future_delivery_id,
        '.TABLE_DELDATE.'.delivery_id,
        '.TABLE_DELDATE.'.delivery_date,
        '.TABLE_BASKET.'.total_weight,
        '.TABLE_BASKET.'.out_of_stock,
        '.TABLE_BASKET.'.extra_charge,
        '.TABLE_PRODUCT_PREP.'.staple_type,
        '.TABLE_BASKET.'.product_id,
        '.TABLE_PRODUCT.'.product_id,
        '.TABLE_PRODUCT.'.random_weight
      FROM
        '.TABLE_BASKET.',
        '.TABLE_BASKET_ALL.',
        '.TABLE_DELDATE.',
        '.TABLE_PRODUCT_PREP.',
        '.TABLE_PRODUCT.'
      WHERE
        '.TABLE_BASKET.'.basket_id = '.TABLE_BASKET_ALL.'.basket_id
        AND '.TABLE_BASKET_ALL.'.delivery_id = '.TABLE_DELDATE.'.delivery_id
        AND '.TABLE_BASKET_ALL.'.delivery_id = "'.$delivery_id.'"
        AND '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT_PREP.'.product_id
        AND '.TABLE_BASKET.'.future_delivery_id != "'.$delivery_id.'"
        AND '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id
      ORDER BY
        '.TABLE_BASKET_ALL.'.delivery_id DESC';
    $result = @mysql_query($sql,$connection) or die("".mysql_error()."");

    while ( $row = mysql_fetch_array($result) )
      {
        $delivery_id = $row['delivery_id'];
        $delivery_date = $row['delivery_date'];
        $item_price = $row['item_price'];
        $quantity = $row['quantity'];
        $out_of_stock = $row['out_of_stock'];
        $total_weight = $row['total_weight'];
        $extra_charge = $row['extra_charge'];
        $retail_staple = $row['retail_staple'];
        $staple_type = $row['staple_type'];
        $future_delivery_id = $row['future_delivery_id'];
        $random_weight = $row['random_weight'];

        if ( $future_delivery_id == $delivery_id )
          {
            //$item_total_3dec = number_format(($item_price*$quantity),3) + 0.00000001;
            //$item_total_price = round($item_total_3dec, 2);
          }
        elseif ( $out_of_stock != "1" )
          {
            if ( $total_weight )
              {
                $item_total_3dec = number_format((($item_price*$total_weight)+($quantity*$extra_charge)),3) + 0.00000001;
                $item_total = round($item_total_3dec, 2);
              }
            elseif ( $total_weight <= 0 && $random_weight == 1)
              {
                $item_total = "0";
              }
            else
              {
                $item_total_3dec = number_format((($item_price*$quantity)+($quantity*$extra_charge)),3) + 0.00000001;
                $item_total = round($item_total_3dec, 2);
              }
          } else {
            $item_total = "0";
          }

        if ( $retail_staple == "3" )
          {
            $cycle_total3 = $cycle_total3+$item_total+0;
            $display_total3 = number_format($cycle_total3,2);
            if ( $staple_type == "m" )
              {
                $cycle_total_m = $cycle_total_m+$item_total+0;
                $display_total_m = number_format($cycle_total_m,2);
              }
            elseif ( $staple_type == "p" )
              {
                $cycle_total_p = $cycle_total_p+$item_total+0;
                $display_total_p = number_format($cycle_total_p,2);
              }
            elseif ( $staple_type == "e" )
              {
                $cycle_total_e = $cycle_total_e+$item_total+0;
                $display_total_e = number_format($cycle_total_e,2);
              }
          }
        elseif ( $retail_staple == "2" )
          {
            $cycle_total2 = $cycle_total2+$item_total+0;
            $display_total2 = number_format($cycle_total2,2);
          }
        elseif ( $retail_staple == "1" )
          {
            $cycle_total1 = $cycle_total1+$item_total+0;
            $display_total1 = number_format($cycle_total1,2);
          }
        $bigtotal = $cycle_total3+$cycle_total2+$cycle_total1+0;
        $display_bigtotal = number_format($bigtotal,2);
      }

    $total_staple = $total_staple+$cycle_total3+0;
    $total_rf = $total_rf+$cycle_total2+0;
    $total_nf = $total_nf+$cycle_total1+0;

    $total_m = $total_m+$cycle_total_m+0;
    $show_total_m = number_format($total_m,2);
    $total_p = $total_p+$cycle_total_p+0;
    $show_total_p = number_format($total_p,2);
    $total_e = $total_e+$cycle_total_e+0;
    $show_total_e = number_format($total_e,2);

    $total_todate = $bigtotal+$total_todate+0;

    include("../func/convert_delivery_date.php");

    $display_totalb = number_format($cycle_total3+$cycle_total2,2);

    $display3 .= "<tr><td bgcolor=#dddddd>$delivery_date</td>
      <td align=right bgcolor=#eeeeee> \$$display_total3</td>
      <td align=right bgcolor=#ffffff> \$$display_total_m</td>
      <td align=right bgcolor=#ffffff> \$$display_total_p</td>
      <td align=right bgcolor=#ffffff> \$$display_total_e</td>
      <td align=right bgcolor=#eeeeee> \$$display_total2</td>
      <td align=right bgcolor=#eeeeee> \$$display_totalb</td>
      <td align=right bgcolor=#eeeeee> \$$display_total1</td>
      <td align=right bgcolor=#dddddd> \$$display_bigtotal</td></tr>";
  }

$total_rfs = $total_staple+$total_rf;

$display_total_staple = number_format($total_staple,2);
$display_total_rf = number_format($total_rf,2);
$display_total_rfs = number_format($total_rfs,2);
$display_total_nf = number_format($total_nf,2);

$display_total_todate = number_format($total_todate,2);
?>

<?php include("template_hdr_orders.php");?>


  <!-- CONTENT BEGINS HERE -->

<div align="center">
<table width="790">
  <tr><td align="left">

<h3>Food Stamp Designation Totals</h3>
Does not include adjustments made to invoices, these are totals for actual items purchased. Current version does not count wholesale purchases separately.


<table cellpadding=6>
  <tr>
  <th>Date</th>
  <th>Staple</th>
  <th>(Meat)</th>
  <th>(Produce)</th>
  <th>(Eggs)</th>
  <th>Retail Food<br><font size=-2>(Not including Staple)</font></th>
  <th>Retail Food<br><font size=-2>(including Staple)</font></th>
  <th>Non-food</th>
  <th>Total</th>
  </tr>

<?php echo $display3;?>

  <tr>
  <td bgcolor=#dddddd><b>Total to Date</b></td>
  <td align=right bgcolor=#dddddd>$<?php echo $display_total_staple;?></td>
  <td align=right bgcolor=#eeeeee>$<?php echo $show_total_m;?></td>
  <td align=right bgcolor=#eeeeee>$<?php echo $show_total_p;?></td>
  <td align=right bgcolor=#eeeeee>$<?php echo $show_total_e;?></td>
  <td align=right bgcolor=#dddddd>$<?php echo $display_total_rf;?></td>
  <td align=right bgcolor=#dddddd>$<?php echo $display_total_rfs;?></td>
  <td align=right bgcolor=#dddddd>$<?php echo $display_total_nf;?></td>
  <td align=right bgcolor=#dddddd><b>$<?php echo $display_total_todate;?></b>
  </td></tr>
  </td></tr>
</table>


  </td></tr>
</table>

  </td></tr>
</table>
</div>
  <!-- CONTENT ENDS HERE -->

<?php include("template_footer_orders.php");?>