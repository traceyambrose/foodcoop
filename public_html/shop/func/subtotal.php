<?php

$total = 0;

$sql = '
  SELECT
    '.TABLE_BASKET.'.*,
    '.TABLE_PRODUCT.'.random_weight,
    '.TABLE_PRODUCT.'.product_id,
    '.TABLE_ROUTE.'.route_id,
    '.TABLE_ROUTE.'.route_desc,
    '.TABLE_PRODUCER.'.*,
    '.TABLE_MEMBER.'.member_id,
    '.TABLE_DELCODE.'.*,
    '.TABLE_PAY.'.*,
    '.TABLE_BASKET_ALL.'.deltype as ddeltype
  FROM
    '.TABLE_BASKET.',
    '.TABLE_BASKET_ALL.',
    '.TABLE_PRODUCT.',
    '.TABLE_PRODUCER.',
    '.TABLE_MEMBER.',
    '.TABLE_DELCODE.',
    '.TABLE_PAY.',
    '.TABLE_ROUTE.'
  WHERE
    '.TABLE_BASKET.'.basket_id = '.TABLE_BASKET_ALL.'.basket_id
    AND
      (
        '.TABLE_BASKET_ALL.'.basket_id = "'.$basket_id.'"
        OR '.TABLE_BASKET.'.future_delivery_id = "'.$current_delivery_id.'"
      )
    AND '.TABLE_BASKET_ALL.'.member_id = "'.$member_id.'"
    AND
      (
        '.TABLE_BASKET_ALL.'.delivery_id = "'.$current_delivery_id.'"
        OR '.TABLE_BASKET.'.future_delivery_id = "'.$current_delivery_id.'"
      )
    AND '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id
    AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
    AND '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
    AND '.TABLE_BASKET_ALL.'.delcode_id = '.TABLE_DELCODE.'.delcode_id
    AND '.TABLE_DELCODE.'.route_id = '.TABLE_ROUTE.'.route_id
    AND '.TABLE_BASKET_ALL.'.payment_method = '.TABLE_PAY.'.payment_method
    AND '.TABLE_ROUTE.'.route_id = '.TABLE_DELCODE.'.route_id
  GROUP BY '.TABLE_BASKET.'.product_id
  ORDER BY business_name ASC, last_name ASC';
$result = @mysql_query($sql,$connection) or die(mysql_error());
while ($row = mysql_fetch_array($result))
  {
    $product_id = $row['product_id'];
    $item_price = $row['item_price'];
    $pricing_unit = $row['pricing_unit'];
    $detailed_notes = stripslashes($row['detailed_notes']);
    $quantity = $row['quantity'];
    $ordering_unit = $row['ordering_unit'];
    $out_of_stock = $row['out_of_stock'];
    $random_weight = $row['random_weight'];
    $total_weight = $row['total_weight'];
    $extra_charge = $row['extra_charge'];
    $deltype = $row['deltype'];
    $ddeltype = $row['ddeltype'];
    $delcode_id = $row['delcode_id'];
    $delcode = $row['delcode'];
    $delcharge = $row['delcharge'];
    $transcharge = $row['transcharge'];
    $future_delivery_id = $row['future_delivery_id'];
    $delivery_date = $row['delivery_date'];
    $payment_method = $row['payment_method'];
    $payment_desc = $row['payment_desc'];
    if($future_delivery_id == "$delivery_id")
      {
        $display_weight = '';
        $item_total_price = 0;
        $display_total_price = '<font color="#FF0000">Invoiced in a previous order</font>';
      }
    elseif($out_of_stock != 1)
      {
        if ($random_weight == 1)
          {
            if($total_weight == 0)
              {
                $display_weight = '<font color="#770000">price updated when producer enters weight</font>';
                $message_incomplete = '<font color="#770000">Order Incomplete</font>';
              }
            else
              {
                $display_weight = $total_weight;
              }
            $item_total_3dec = number_format ((($item_price * $total_weight) + ($quantity * $extra_charge)), 3) + 0.00000001;
            $item_total_price = round($item_total_3dec, 2);
            $display_total_price = '<b>$'.number_format ($item_total_price, 2).'</b>';
          }
        else
          {
            $display_weight = '';
            $item_total_3dec = number_format((($item_price*$quantity)+($quantity*$extra_charge)),3) + 0.00000001;
            $item_total_price = round($item_total_3dec, 2);
            $display_total_price = '<b>$'.number_format ($item_total_price, 2).'</b>';
          }
      }
    else
      {
        $display_weight = '';
        $item_total_price = 0;
        $display_total_price = '<b>$'.number_format ($item_total_price, 2).'</b>';
      }
    if($item_total_price)
      {
        $total = $item_total_price + $total;
      }
    $total_pr = $total_pr + $quantity;
    $subtotal_pr = $subtotal_pr + $item_total_price;
  }

$sqladj = '
  SELECT *
  FROM
    '.TABLE_ADJ.'
  WHERE
    basket_id = "'.$basket_id.'"
  GROUP BY
    adjid
  ORDER BY
    adj_name ASC';
$result_adj = @mysql_query($sqladj,$connection) or die(mysql_error());
while ($row = mysql_fetch_array($result_adj))
  {
    $adjid = $row['adjid'];
    $adj_name = $row['adj_name'];
    $adj_amount = $row['adj_amount'];
    $adj_desc = $row['adj_desc'];
    $adj_total = $adj_amount+$adj_total;
  }
$total = $total + $adj_total;
?>
