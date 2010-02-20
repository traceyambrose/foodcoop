<?php
if ( $yp == "ds" )
  {

    $sqlp = '
      SELECT
        product_id
      FROM
        '.TABLE_PRODUCT.'
      WHERE
        product_id = "'.$product_id.'"';
    $resultp = @mysql_query($sqlp, $connection) or die(mysql_error());
    $nump = mysql_numrows($resultp);
    if ( $nump != 1 )
      {
        $message = "<H3>Product ID # $product_id does not exist in the system.</h3>";
      }

    // Get the time until the order closes
    $seconds_until_close = strtotime ($_SESSION['closing_timestamp']) - time();
    // Set up the "donotlist" field condition based on whether the member is an "institution" or not
    // Only institutions are allowed to see donotlist=3 (wholesale products)
    if (strpos ($_SESSION['auth_type'], 'institution') !== false && $seconds_until_close < INSTITUTION_WINDOW)
      {
        $donotlist_condition = 'AND ('.TABLE_PRODUCT.'.donotlist = "0" OR '.TABLE_PRODUCT.'.donotlist = "3")';
      }
    else
      {
        $donotlist_condition = 'AND '.TABLE_PRODUCT.'.donotlist = "0"';
      }

    // Check to see if this product is available
    $sqldn = '
      SELECT
        product_id
      FROM
        '.TABLE_PRODUCT.'
      LEFT JOIN '.TABLE_PRODUCER.' ON '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
      WHERE
        product_id = "'.$product_id.'"
        AND '.TABLE_PRODUCER.'.pending = 0
        AND '.TABLE_PRODUCER.'.donotlist_producer = 0
        '.$donotlist_condition;
    $resultdn = @mysql_query($sqldn, $connection) or die(mysql_error());
    $numdn = mysql_numrows($resultdn);
    // No results means we can't add that product
    if ( $numdn == "0" )
      {
        $message = "<H3>Product ID # $product_id is currently unavailable.</h3>";
      }

    $sqli = '
      SELECT
        inventory_on,
        inventory
      FROM
        '.TABLE_PRODUCT.'
      WHERE
        product_id = '.$product_id;
    $resulti = @mysql_query($sqli, $connection) or die(mysql_error());
    while ( $row = mysql_fetch_array($resulti) )
      {
        $inventory_on = $row['inventory_on'];
        $inventory = $row['inventory'];
      }

    $sqlb = '
      SELECT
        product_id
      FROM
        '.TABLE_BASKET.'
      WHERE
        product_id = '.$product_id.'
        AND basket_id = "'.$basket_id.'"';
    $resultb = @mysql_query($sqlb, $connection) or die(mysql_error());
    $numb = mysql_numrows($resultb);
    if ( $numb == "1" )
      {
        $message = "<H3>Product ID # $product_id is already in the basket.<br>Please edit the quantity of the item already listed if you need to add more.</h3>";
      }

    if ( ! $product_id )
      {
        $message = '<h3>Please enter a Product ID.</h3>';
      }
    elseif ( ! ereg("[0-9]+$", $product_id) )
      {
        $message = '<h3>Please review the Product ID: The id must only be a number.</h3>';
      }
    elseif ( ! $quantity )
      {
        $message = '<h3>Please review the quantity: Please enter a quantity for the product.</h3>';
      }
    elseif ( ! ereg("[0-9]+$", $quantity) )
      {
        $message = '<h3>Please review the quantity: The quantity must be a number.</h3>';
      }
    elseif ( $inventory_on && ($inventory == '' || $inventory == 0) )
      {
        $message = '<h3>Product ID # '.$product_id.' is currently out of stock.</h3>';
      }
    elseif ($inventory_on && $inventory < $quantity)
      {
        $message = '<h3>There are only '.$inventory.' of Product ID # '.$product_id.' available. Please add that quantity or less.</h3>';
      }
    elseif (($basket_id) && ($numdn != 0) && ($nump == 1) && ($numb != 1))
      {
        if ( $inventory_on && $inventory >= $quantity )
          {
            $inventory = $inventory - $quantity;

            $sqlus = '
              UPDATE
                '.TABLE_PRODUCT.'
              SET
                inventory = "'.$inventory.'"
              WHERE
                product_id = '.$product_id;
            $resultus = @mysql_query($sqlus, $connection) or die("Couldn't execute query updating stock in public product list.");

            $sqlus2 = '
              UPDATE
                '.TABLE_PRODUCT_PREP.'
              SET
                inventory = "'.$inventory.'"
              WHERE
                product_id = '.$product_id;
            $resultus2 = @mysql_query($sqlus2, $connection) or die("Couldn't execute query updating stock in prep list.");
          }

        $sql3 = '
          SELECT
            unit_price,
            extra_charge,
            future_delivery_id
          FROM
            '.TABLE_PRODUCT.'
          WHERE
            product_id = '.$product_id;
        $result3 = @mysql_query($sql3, $connection) or die("Couldn't execute query 3.");
        while ( $row = mysql_fetch_array($result3) )
          {
            $unit_price = $row['unit_price'];
            $extra_charge = $row['extra_charge'];
            $future_delivery_id = $row['future_delivery_id'];
          }

        $customer_notes_to_producer = mysql_real_escape_string ($customer_notes_to_producer);

        $sql = '
          INSERT INTO
            '.TABLE_BASKET.'
              (
                basket_id,
                product_id,
                item_price, quantity,
                total_weight,
                extra_charge,
                customer_notes_to_producer,
                future_delivery_id,
                item_date
              )
          VALUES
            (
              "'.$basket_id.'",
              "'.$product_id.'",
              "'.$unit_price.'",
              "'.$quantity.'",
              "'.$total_weight.'",
              "'.$extra_charge.'",
              "'.$customer_notes_to_producer.'",
              "'.$future_delivery_id.'",
              now()
            )';
        $result = @mysql_query($sql, $connection) or die(mysql_error());
        mysql_free_result($result3);
      }
  }
?>