<?php
$sql = '
  SELECT
    *
  FROM
    '.TABLE_PRODUCT_PREP.',
    '.TABLE_PRODUCT_TYPES.',
    '.TABLE_CATEGORY.',
    '.TABLE_SUBCATEGORY.'
  WHERE
    '.TABLE_PRODUCT_PREP.'.product_id = '.$product_id.'
    AND '.TABLE_PRODUCT_PREP.'.prodtype_id = '.TABLE_PRODUCT_TYPES.'.prodtype_id
    AND '.TABLE_CATEGORY.'.category_id = '.TABLE_SUBCATEGORY.'.category_id
    AND '.TABLE_SUBCATEGORY.'.subcategory_id = '.TABLE_PRODUCT_PREP.'.subcategory_id';
$result = @mysql_query($sql,$connection) or die(mysql_error());

$num = mysql_numrows($result);

while ( $row = mysql_fetch_array($result) )
  {
    $product_name = stripslashes($row['product_name']);
    $new = $row['new'];
    $category_id = $row['category_id'];
    $category_name = $row['category_name'];
    $subcategory_id = $row['subcategory_id'];
    $subcategory_name = $row['subcategory_name'];
    $inventory_on = $row['inventory_on'];
    $inventory = $row['inventory'];
    $unit_price = $row['unit_price'];
    $pricing_unit = $row['pricing_unit'];
    $ordering_unit = $row['ordering_unit'];
    $prodtype_id = $row['prodtype_id'];
    $prodtype = $row['prodtype'];
    $meat_weight_type = $row['meat_weight_type'];
    $extra_charge = $row['extra_charge'];
    $future_delivery = $row['future_delivery'];
    $random_weight = $row['random_weight'];
    $maximum_weight = $row['maximum_weight'];
    $minimum_weight = $row['minimum_weight'];
    $donotlist = $row['donotlist'];
    $detailed_notes = stripslashes($row['detailed_notes']);
    $retail_staple = $row['retail_staple'];
    $storage_id = $row['storage_id'];
  }
?>