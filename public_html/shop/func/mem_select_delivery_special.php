<?php
if ( $qy == 'jk' )
  {
    if ( !$delcode_id )
      {
        $message2 = '<b>Please choose a Pickup or Delivery Location.</b>';
      }
    elseif ( !$payment_method )
      {
        $message2 = '<b>Please choose a Payment Method.</b>';
      }
    if ( $payment_method )
      {
        $sql4 = '
          SELECT
            delivery_id,
            member_id,
            basket_id
          FROM
            '.TABLE_BASKET_ALL.'
          WHERE
            delivery_id = "'.$current_delivery_id.'"
            AND member_id = "'.$member_id.'"';
        $result4 = @mysql_query($sql4,$connection) or die(mysql_error());
        $num4 = mysql_numrows($result4);
        while ( $row = mysql_fetch_array($result4) )
          {
            $basket_id = $row['basket_id'];
          }
        session_register("basket_id");
        if ( $num4 == 1 )
          {
            $message2 = '<b>This order has already been submitted. Click here to <a href="orders_current.php">edit the order</a></b>';
          }
        else
          {
            $show_page = 'no';
            $sql3 = '
              SELECT
                basket_id
              FROM
                '.$table_basket_all.'
              WHERE
                basket_id > "1"
              ORDER BY
                basket_id DESC
              LIMIT 1';
            $result3 = @mysql_query($sql3,$connection) or die("Couldn't execute query 3.");
            while ( $row = mysql_fetch_array($result3) )
              {
                $basket_id = $row['basket_id'];
                $basket_new = $basket_id + 1;
                $basket_id = "$basket_new";
              }
            $sql2 = '
              SELECT
                delcharge
              FROM
                '.TABLE_DELCODE.'
              WHERE delcode_id = "'.$delcode_id.'"';
            $result2 = @mysql_query($sql2,$connection) or die("Couldn't execute query 2.");
            while ( $row = mysql_fetch_array($result2) )
              {
                $delcharge = $row['delcharge'];
              }
            $message3 = '<font color="#770000"><h3>You can begin shopping!<br>
              You can add items from the Product list or add <br>
              items by Product ID. Select from your options below.</font></h3>';
            $deltype = 'P';
            session_register("basket_id");
            $order_started = 'yes';
            session_register("order_started");
            $sqlo = '
              INSERT INTO
                '.TABLE_BASKET_ALL.'
                  (
                    basket_id,
                    member_id,
                    delivery_id,
                    deltype,
                    delcode_id,
                    delivery_cost,
                    payment_method,
                    order_date
                  )
              VALUES
                (
                  "'.$basket_id.'",
                  "'.$member_id.'",
                  "'.$current_delivery_id.'",
                  "'.$deltype.'",
                  "'.$delcode_id.'",
                  "'.$delcharge.'",
                  "'.$payment_method.'",
                  now()
                )';
            $resulto = @mysql_query($sqlo,$connection) or die(mysql_error());
          }
      }
  }
$display_deltype = 'Pick-up';
$sqldc = '
  SELECT
    *
  FROM
    '.TABLE_DELCODE.'
  WHERE special_loc = "1"
  ORDER BY delcode ASC';
$rs = @mysql_query($sqldc,$connection) or die("Couldn't execute query.");
while ( $row = mysql_fetch_array($rs) )
  {
    $delcode_id = $row['delcode_id'];
    $delcode = $row['delcode'];
    $delcharge = $row['delcharge'];
    $delcode_first = '
      <option value="">Choose a pickup or delivery option</option>';
    $display_delcode .= '
      <option value="'.$delcode_id.'">'.$delcode.'</option>';
  }
$display_pay = '
  <input type="radio" name="payment_method" value="C">Check
  <input type="radio" name="payment_method" value="P">PayPal online';
$display .= '
<form action="" method="post">
<div align="center">
<table cellpadding="7" cellspacing="2" border="0">
  <tr>
    <td colspan="2" bgcolor="#AEDE86" valign="bottom" align="left"><b>Select from these options to begin an order</b></td>
  </tr>';
if( $message2 )
  {
    $display .= '<tr bgcolor="#DDDDDD"><td colspan="2"><font color="#990000">'.$message2.'</font></td></tr>';
  }
else
  {
    $display .= '';
  }
$display .= '
  <tr bgcolor="#DDDDDD">
    <td align="left"><b>Delivery Type:</b>: </td>
    <td align="left">'.$display_deltype.'</td>
  </tr>
    <tr bgcolor="#DDDDDD">
    <td align="left"><b>Pickup/Delivery Locations:</b></td>
    <td align="left">
      <select name="delcode_id">
        '.$delcode_first.'
        '.$display_delcode.'
      </select>
    </td>
  </tr>
  <tr bgcolor="#DDDDDD">
    <td align="left"><b>Payment Method:</b></td>
    <td align="left">'.$display_pay.'</td>
  </tr>
  <tr bgcolor="#DDDDDD">
    <td colspan="2" align="right">
      <input type="hidden" name="qy" value="jk">
';
if( $basket_id )
  {
    $display .= '<input type="hidden" name="basket_id" value="'.$basket_id.'">';
  }
$display .= '<input name="where" type="submit" value="Click to Start An Order">
    </td>
  </tr>
</table>
</form>
';
