<?php

$sqlb = '
  SELECT
    product_id
  FROM
    '.TABLE_BASKET.'
  WHERE
    product_id = "'.$product_id.'"
    AND basket_id = "'.$basket_id.'"';
$resultb = @mysql_query($sqlb, $connection) or die(mysql_error());
$numb = mysql_numrows($resultb);
if ( $numb == 1 )
  {
    $message = '<b><u>Product ID # '.$product_id.' is already in your basket</u>.</b><br>Please edit the quantity in <a href="orders_current.php">your shopping cart</a> of the item already listed if you want to add more.';
  }
else
  {
    $sqlis = '
      SELECT
        '.TABLE_PRODUCT.'.inventory_on,
        '.TABLE_PRODUCT.'.inventory
      FROM
        '.TABLE_PRODUCT.'
      WHERE
        product_id = "'.$product_id.'"';
    $resultis = @mysql_query($sqlis, $connection) or die("Couldn't execute query s.");
    while ( $row = mysql_fetch_array($resultis) )
      {
        $inventory_on = $row['inventory_on'];
        $inventory = $row['inventory'];
      }
    if( $inventory_on && ($inventory == '' || $inventory == 0) )
      {
        $message = '<b>This product is sold out!</b>';
      }
    else
      {
        if( $inventory_on && $inventory >= 1 )
          {
            $inventory = $inventory - 1;
            $sqlus = '
              UPDATE
                '.TABLE_PRODUCT.'
              SET
                inventory = "'.$inventory.'"
              WHERE
                product_id = "'.$product_id.'"';
            $resultus = @mysql_query($sqlus, $connection) or die("Couldn't execute query updating stock in public product list.");
            $sqlus2 = '
              UPDATE '.TABLE_PRODUCT_PREP.'
              SET
                inventory = "'.$inventory.'"
              WHERE
                product_id = "'.$product_id.'"';
            $resultus2 = @mysql_query($sqlus2, $connection) or die("Couldn't execute query updating stock in prep list.");
          }
        $sql3 = '
          SELECT
            unit_price,
            extra_charge
          FROM
            '.TABLE_PRODUCT.'
          WHERE
            product_id = "'.$product_id.'"';
        $result3 = @mysql_query($sql3, $connection) or die("Couldn't execute query 3.");
        while ( $row = mysql_fetch_array($result3) )
          {
            $unit_price = $row['unit_price'];
            $extra_charge = $row['extra_charge'];
          }
        $sqlc = '
          INSERT INTO
            '.TABLE_BASKET.'
              (
                basket_id,
                product_id,
                item_price,
                quantity,
                extra_charge,
                item_date
              )
          VALUES
            (
              "'.$basket_id.'",
              "'.$product_id.'",
              "'.$unit_price.'",
              "1",
              "'.$extra_charge.'",
              now()
            )';
        $result = @mysql_query($sqlc, $connection) or die(mysql_error());
        $product_name = stripslashes($product_name);
        $message = '<b># '.$product_id.' : '.$product_name.' was added to your cart.<br>
          <a href="orders_current.php">View your Cart</a> to increase the quantity.</b>';
        $unit_price = 0;
        $extra_charge = 0;
      }
  }
$sqls = '
  SELECT
    '.TABLE_BASKET_ALL.'.basket_id,
    '.TABLE_BASKET_ALL.'.delivery_id,
    '.TABLE_BASKET.'.basket_id,
    '.TABLE_BASKET.'.product_id,
    '.TABLE_BASKET.'.quantity,
    '.TABLE_BASKET.'.item_price,
    '.TABLE_BASKET.'.out_of_stock,
    '.TABLE_BASKET.'.total_weight,
    '.TABLE_BASKET.'.extra_charge,
    '.TABLE_PRODUCT.'.random_weight,
    '.TABLE_PRODUCT.'.product_id
  FROM
    '.TABLE_BASKET.',
    '.TABLE_BASKET_ALL.',
    '.TABLE_PRODUCT.'
  WHERE
    '.TABLE_BASKET_ALL.'.basket_id = "'.$basket_id.'"
    AND '.TABLE_BASKET.'.basket_id = "'.$basket_id.'"
    AND '.TABLE_BASKET_ALL.'.delivery_id = "'.$current_delivery_id.'"
    AND '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id
  GROUP BY
    '.TABLE_BASKET.'.product_id';
$results = @mysql_query($sqls, $connection) or die("Couldn't execute query 1.");
while ( $row = mysql_fetch_array($results) )
  {
    $product_id = $row['product_id'];
    $item_price = $row['item_price'];
    $quantity = $row['quantity'];
    $out_of_stock = $row['out_of_stock'];
    $random_weight = $row['random_weight'];
    $total_weight = $row['total_weight'];
    $extra_charge = $row['extra_charge'];
    if( $out_of_stock != 1 )
      {
        if ( $random_weight == 1 )
          {
            if( $total_weight == 0 )
              {
              }
            else
              {
                $display_weight = $total_weight;
              }
            $item_total_3dec = number_format ((($item_price * $total_weight) + ($quantity * $extra_charge)), 3) + 0.00000001;
            $item_total_price = round ( $item_total_3dec, 2 );
          }
        else
          {
            $display_weight = "";
            $item_total_3dec = number_format ((($item_price * $quantity) + ($quantity * $extra_charge)), 3) + 0.00000001;
            $item_total_price = round ($item_total_3dec, 2);
          }
      }
    else
      {
        $display_weight = '';
        $item_total_price = '0';
      }
    if( $item_total_price )
      {
        $total = $item_total_price + $total;
      }
    $total_pr = $total_pr + $quantity;
    $subtotal_pr = $subtotal_pr + $item_total_price;
  }
  mysql_free_result($results);