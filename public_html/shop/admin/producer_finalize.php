<?php

class producer_finalize
  {
    function getTransactionName($ttype_id)
      {
        $sql = mysql_query('
          SELECT
            ttype_name
          FROM
            '.TABLE_TRANS_TYPES.'
          WHERE
            ttype_id = "'.$ttype_id.'" limit 1');
        $result = mysql_fetch_array($sql);
        return $result['ttype_name'];
      }
    function finalize($p)
      {
        foreach( $p['transaction_amount'] as $key=>$type )
          {
            $ttype_name = producer_finalize::getTransactionName($key);
            $sql = mysql_query('
              INSERT INTO
                '.TABLE_TRANSACTIONS.'
                  (
                    transaction_producer_id,
                    transaction_delivery_id,
                    transaction_type,
                    transaction_name,
                    transaction_amount,
                    transaction_user,
                    transaction_timestamp)
                VALUES
                  (
                    "'.$p["producer_id"].'",
                    "'.$p["delivery_id"].'",
                    "'.$key.'",
                    "'.$ttype_name.'",
                    "'.$type.'",
                    "'.$_SESSION["valid_c"].'",
                    now()
                  )');
          }
      }
    function finalizeAll($delivery_id,$producer_id)
      {
        include_once ("config_foodcoop.php");
        if ( $_SESSION['valid_c'] )
          {
            $user = $_SESSION['valid_c'];
          }
        else
          {
            $user = 'batch script';
          }
        // get list of producers with invoices this cycle
        $producers = array();
        if ( $producer_id )
          {
            $producers = array(strtoupper($producer_id));
          }
        elseif ( !$_SESSION['valid_c'] )
          {
            $sqlp = '
              SELECT
                '.TABLE_PRODUCER.'.producer_id
              FROM
                '.TABLE_PRODUCER.',
                '.TABLE_BASKET.',
                '.TABLE_PRODUCT.',
                '.TABLE_BASKET_ALL.'
              WHERE
                '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id
                AND '.TABLE_BASKET.'.basket_id = '.TABLE_BASKET_ALL.'.basket_id
                AND '.TABLE_BASKET_ALL.'.delivery_id = "'.$delivery_id.'"
                AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
              GROUP BY
                '.TABLE_PRODUCER.'.producer_id';
            $resultp = mysql_query($sqlp) or die("1) ".mysql_error());
            while ( $row = mysql_fetch_array($resultp) )
              {
                array_push($producers,$row['producer_id']);
              }
          }
        foreach( $producers as $key=>$producer_id )
          {
            $total = 0;
            // Reset "extra charge" totalizer
            $total_extra = 0;
            $sql = '
              SELECT
                '.TABLE_BASKET.'.basket_id,
                '.TABLE_BASKET.'.product_id,
                '.TABLE_BASKET_ALL.'.basket_id,
                '.TABLE_BASKET_ALL.'.delivery_id,
                '.TABLE_PRODUCT.'.product_id,
                '.TABLE_PRODUCER.'.producer_id
              FROM
                '.TABLE_BASKET.',
                '.TABLE_BASKET_ALL.',
                '.TABLE_PRODUCT.',
                '.TABLE_PRODUCER.'
              WHERE
                '.TABLE_BASKET.'.basket_id = '.TABLE_BASKET_ALL.'.basket_id
                AND
                  (
                    '.TABLE_BASKET_ALL.'.delivery_id = "'.$delivery_id.'"
                    OR '.TABLE_BASKET.'.future_delivery_id = "'.$delivery_id.'"
                  )
                AND '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id
                AND '.TABLE_PRODUCT.'.producer_id = "'.$producer_id.'"
              GROUP BY
                '.TABLE_BASKET.'.product_id';
            $rs = mysql_query($sql) or die("2) ".mysql_error());
            while ( $row = mysql_fetch_array($rs) )
              {
                $basket_id = $row['basket_id'];
                $product_id = $row['product_id'];
                $total_pr = 0;
                $subtotal_pr = 0;
                //ORDER BY $table_basket.basket_id ASC
                $sql = '
                  SELECT
                    '.TABLE_BASKET.'.*,
                    '.TABLE_BASKET_ALL.'.deltype as ddeltype,
                    '.TABLE_PRODUCT.'.random_weight,
                    '.TABLE_PRODUCT.'.extra_charge,
                    '.TABLE_BASKET.'.future_delivery_id,
                    '.TABLE_SUBCATEGORY.'.subcategory_id,
                    '.TABLE_SUBCATEGORY.'.category_id,
                    '.TABLE_PRODUCT.'.subcategory_id
                  FROM
                    '.TABLE_BASKET.',
                    '.TABLE_PRODUCT.',
                    '.TABLE_BASKET_ALL.',
                    '.TABLE_SUBCATEGORY.'
                  WHERE
                    '.TABLE_BASKET.'.product_id = "'.$product_id.'"
                    AND
                      (
                        '.TABLE_BASKET_ALL.'.delivery_id = "'.$delivery_id.'"
                        OR '.TABLE_BASKET.'.future_delivery_id = "'.$delivery_id.'"
                      )
                    AND '.TABLE_BASKET_ALL.'.basket_id = '.TABLE_BASKET.'.basket_id
                    AND '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id
                    AND '.TABLE_PRODUCT.'.producer_id = "'.$producer_id.'"
                    AND '.TABLE_PRODUCT.'.subcategory_id = '.TABLE_SUBCATEGORY.'.subcategory_id
                  GROUP BY
                    '.TABLE_BASKET.'.basket_id';
                $resultpr = mysql_query($sql) or die("3) ".mysql_error());
                while ( $row = mysql_fetch_array($resultpr) )
                  {
                    $basket_id = $row['basket_id'];
                    $product_id = $row['product_id'];
                    $category_id = $row['category_id'];
                    $quantity = $row['quantity'];
                    $random_weight = $row['random_weight'];
                    $total_weight = $row['total_weight'];
                    $item_price = $row['item_price'];
                    $out_of_stock = $row['out_of_stock'];
                    $ordering_unit = $row['ordering_unit'];
                    $extra_charge = $row['extra_charge'];
                    $delcode_id = $row['delcode_id'];
                    $ddeltype = $row['ddeltype'];
                    $future_delivery_id = $row['future_delivery_id'];
                    if ( $future_delivery_id == $delivery_id )
                      {
                        $total_pr = $total_pr + $quantity;
                        $item_total_price = 0;
                      }
                    elseif ( $future_delivery_id > $delivery_id )
                      {
                        $total_pr = $total_pr + $quantity;
                        $item_total_price = 0;
                      }
                    elseif ( $out_of_stock != 1 )
                      {
                        $total_pr = $total_pr+$quantity;
                        if ( $random_weight == 1 )
                          {
                            if ( $total_weight == 0 )
                              {
                                $item_total_3dec = ($item_price * $total_weight) + 0.00000001;
                                $item_total_price = round($item_total_3dec, 2);
                              }
                            else
                              {
                                $item_total_3dec = (($item_price * $total_weight) + ($extra_charge * $quantity)) + 0.00000001;
                                $item_total_price = round($item_total_3dec, 2);
                              }
                          }
                        else
                          {
                            $item_total_3dec = (($item_price * $quantity) + ($extra_charge * $quantity)) + 0.00000001;
                            $item_total_price = round($item_total_3dec, 2);
                          }
                      }
                    else
                      {
                        $total_pr = $total_pr + 0;
                        $item_total_price = 0;
                      }
                    if ( $extra_charge )
                      {
                        $extra_charge_calc = $extra_charge * $quantity;
                        // Accumulate all extra charges for this producer...
                        $total_extra = $total_extra + ($extra_charge * $quantity);
                      }
                    if ( $current_product_id < 0 )
                      {
                        $current_product_id = $row['product_id'];
                      }
                    while ( $current_product_id != $product_id )
                      {
                        $current_product_id = $product_id;
                      }
                    if ( $item_total_price )
                      {
                        $total = $item_total_price + $total;
                      }
                    $subtotal_pr = $subtotal_pr + $item_total_price;
                  }
                $prod_sum = $prod_sum + $total_pr;
              }
            $sqla = mysql_query('
              SELECT
                transaction_name,
                transaction_comments,
                transaction_amount
              FROM
                '.TABLE_TRANSACTIONS.' t,
                '.TABLE_TRANS_TYPES.' tt
              WHERE
                transaction_delivery_id = "'.$delivery_id.'"
                AND transaction_producer_id = "'.$producer_id.'"
                AND t.transaction_type = tt.ttype_id
                AND tt.ttype_parent = "20"');
            while ( $resulta = mysql_fetch_array($sqla) )
              {
                $subtotal_pr = $subtotal_pr+$resulta['transaction_amount'];
                $total = $total+$resulta['transaction_amount'];
              }
            $producer_fee = round((($total - $total_extra) * PRODUCER_MARKDOWN) + 0.00000001, 2);
            $final_total = round($total-$producer_fee+0.00000001,2);
            $p['transaction_amount'][28] = number_format($total, 2, '.', '');
            $p['transaction_amount'][31] = number_format($producer_fee, 2, '.', '');
            $p['transaction_amount'][35] = number_format($final_total, 2, '.', '');
            foreach( $p['transaction_amount'] as $key=>$type )
              {
                $ttype_name = producer_finalize::getTransactionName($key);
                $sql = mysql_query('
                  INSERT INTO
                    transactions
                      (
                        transaction_producer_id,
                        transaction_delivery_id,
                        transaction_type,
                        transaction_name,
                        transaction_amount,
                        transaction_user,
                        transaction_timestamp
                      )
                  VALUES
                    (
                      '.$producer_id.'",
                      "'.$delivery_id.'",
                      "'.$key.'",
                      "'.$ttype_name.'",
                      "'.$type.'",
                      "'.$user.'",
                      now()
                    )');
              }
          }
      }
  }
?>













