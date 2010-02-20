<?php
$product_id = $row['product_id'];
$product_name = $row['product_name'];
$unit_price = $row['unit_price'];
$pricing_unit = $row['pricing_unit'];
$ordering_unit = $row['ordering_unit'];
$prodtype_id = $row['prodtype_id'];
$prodtype = $row['prodtype'];
$random_weight = $row['random_weight'];
$minimum_weight = $row['minimum_weight'];
$maximum_weight = $row['maximum_weight'];
$meat_weight_type = $row['meat_weight_type'];
$extra_charge = $row['extra_charge'];
$donotlist = $row['donotlist'];
$detailed_notes = $row['detailed_notes'];

if ( $current_product_id < 0 )
  {
    $current_product_id = $row['product_id'];
  }
while ( $current_product_id != $product_id )
  {
    $current_product_id = $product_id;
  }

if ( $random_weight )
  {
    $show_weight = "You will be billed for exact $meat_weight_type weight
    (approx. $minimum_weight - $maximum_weight ".$pricing_unit."s).";
    $blue_weight = "<font color=#3333ff>(Producer will need to enter weight.)</font>";
  }
else
  {
    $show_weight = '';
    $blue_weight = '';
  }
if ( $ordering_unit )
  {
    $ordering_unit = $ordering_unit;
  }
else
  {
    $ordering_unit = "<font color=#3333ff>ERROR</font>";
  }
if ( $extra_charge )
  {
    $extra = "<br>Extra charge: \$".number_format($extra_charge, 2)."/$ordering_unit";
  }
else
  {
    $extra = '';
  }

if ( $display_type == "new_or_changed" )
  {
    $first_table_cell = "
      <td valign=\"center\"><a name=\"$product_id\">
      <font color=#3333ff>NEW LISTING</font>
      </td>";
  }

$display .= "<tr>";
$display .= "$first_table_cell";
$display .= "<td><b># $product_id</b></td>";
$display .= "<td><b>".stripslashes($product_name)."</b><br>";
$display .= "<font size=\"-1\">Order number of ".$ordering_unit."s. $show_weight      ".stripslashes($detailed_notes)." $extra $blue_weight";
$display .= "</font>";
$display .= "</td>";

if ( $prodtype_id  == "1" )
  {
    $display .= "<td><font size=\"-1\">$prodtype</font></td>";
  }
else
  {
    $display .= "<td><font size=\"-1\"></font></td>";
  }

$display .= "<td>\$".number_format($unit_price, 2)."/$pricing_unit</td>";
$display .= "</tr>";
?>