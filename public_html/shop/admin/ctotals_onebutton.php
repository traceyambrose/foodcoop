<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();
$date_today = date("F j, Y");

if ( $updatevalues == "ys" && !empty($payment_method) )
  {
  $sql77 = '
    SELECT
      delivery_id,
      basket_id
    FROM
      '.TABLE_BASKET_ALL.'
    WHERE
      '.TABLE_BASKET_ALL.'.delivery_id = "'.$delivery_id.'"';
  $result77 = @mysql_query($sql77,$connection) or die("".mysql_error()."");
  while ( $row = mysql_fetch_array($result77) )
    {
      $basket_id = $row['basket_id'];
      $sql_pay = '
        SELECT
          payment_method
        FROM
          '.TABLE_BASKET_ALL.'
        WHERE
          basket_id = "'.$basket_id.'"';
      $result_pay = @mysql_query($sql_pay,$connection) or die("".mysql_error()."");
      while ( $row = mysql_fetch_array($result_pay) )
        {
          $payment_method_previous = $row['payment_method'];
          if ( $payment_method_previous != $payment_method[$basket_id] )
            {
              $finalized2 = "0";
              $payment_method2 = $payment_method[$basket_id];
            }
          else
            {
              $finalized2 = $finalized[$basket_id];
              $payment_method2 = $payment_method_previous;
            }
          $amount_paid_update = preg_replace("/[^0-9\.\-]/","",$amount_paid[$basket_id]);
          $membership_amount_paid_update = preg_replace("/[^0-9\.\-]/","",$membership_amount_paid[$basket_id]);
          $payment_method2 = preg_replace("/[^a-zA-Z]/","",$payment_method2);
          $finalized2 = preg_replace("/[^0-9]/","",$finalized2);
          if ( $amount_paid_update != 0 )
            {
              $sqlu = '
                UPDATE
                  '.TABLE_BASKET_ALL.'
                SET
                  payment_method = "'.$payment_method2.'",
                  amount_paid = amount_paid + "'.$amount_paid_update.'",
                  order_date = now()
                WHERE
                  basket_id = "'.$basket_id.'"
                  AND delivery_id = "'.$delivery_id.'"';
              $resultu = @mysql_query($sqlu,$connection) or die(mysql_error());
              $message = "<H3>The information has been updated.</h3>";
            }
          elseif ( $payment_method_previous != $payment_method[$basket_id] )
            {
              // only change payment method if no amount chosen
              $sqlu = '
                UPDATE '.TABLE_BASKET_ALL.'
                SET
                  payment_method = "'.$payment_method2.'",
                  finalized = "'.$finalized2.'",
                  order_date = now()
                WHERE
                  basket_id = "'.$basket_id.'"
                  AND delivery_id = "'.$delivery_id.'"';
              $resultu = @mysql_query($sqlu,$connection) or die(mysql_error());
              $message = "<H3>The information has been updated.</h3>";
            }
          $member_id = preg_replace("/[^0-9]/","",$_POST['member_id'][$basket_id]);
          $batchno = preg_replace("/[^0-9]/","",$_POST['transaction_batchno'][$basket_id]);
          $memo = stripslashes(strip_tags($_POST['transaction_memo'][$basket_id]));
          $comments = stripslashes(strip_tags($_POST['transaction_comments'][$basket_id]));
          if ( $member_id && ($amount_paid_update != 0 || ($payment_method_previous != $payment_method[$basket_id])) || $membership_amount_paid_update != 0)
            {
              $sql = mysql_query('
                SELECT
                  transaction_id
                FROM
                  '.TABLE_TRANSACTIONS.' t
                WHERE
                  t.transaction_type="23"
                  AND t.transaction_member_id = "'.$member_id.'"
                  AND t.transaction_basket_id = "'.$basket_id.'"
                  AND t.transaction_delivery_id = "'.$delivery_id.'"
                  AND t.transaction_name = "Invoice Payment"
                  AND t.transaction_amount = "'.$amount_paid_update.'"
                  AND t.transaction_user = "'.$_SESSION['valid_c'].'"
                  AND t.transaction_batchno = "'.$batchno.'"
                  AND t.transaction_memo = "'.$memo.'"
                  AND t.transaction_comments = "'.$comments.'"');
              // This is a check to be sure not to post the same transaction twice
              if ( mysql_num_rows($sql) < 1 && $amount_paid_update != 0)
                {
                  $query = '
                    INSERT INTO
                      '.TABLE_TRANSACTIONS.'
                        (
                          transaction_type,
                          transaction_name,
                          transaction_amount,
                          transaction_user,
                          transaction_member_id,
                          transaction_basket_id,
                          transaction_delivery_id,
                          transaction_timestamp,
                          transaction_batchno,
                          transaction_memo,
                          transaction_comments,
                          transaction_method
                        )
                    VALUES
                      (
                        "23",
                        "Invoice Payment",
                        "'.$amount_paid_update.'",
                        "'.$_SESSION['valid_c'].'",
                        "'.$member_id.'",
                        "'.$basket_id.'",
                        "'.$delivery_id.'",
                        now(),
                        "'.$batchno.'",
                        "'.$memo.'",
                        "'.$comments.'",
                        "'.$payment_method2.'")';
                  $sql = mysql_query($query);
                  }
              else
                {
                  $message = "<h3>There was a duplicate entry that was not entered.</h3>";
                }
              if ( $member_id && ($membership_amount_paid_update != 0) )
                {
                  // In this query, we change the sign of the membership amount paid
                  // because it must add in a positive sense with membership receivables
                  $query = '
                    INSERT INTO
                      '.TABLE_TRANSACTIONS.'
                        (
                          transaction_type,
                          transaction_name,
                          transaction_amount,
                          transaction_user,
                          transaction_member_id,
                          transaction_basket_id,
                          transaction_delivery_id,
                          transaction_timestamp,
                          transaction_batchno,
                          transaction_memo,
                          transaction_comments,
                          transaction_method
                        )
                    VALUES
                      (
                        "25",
                        "Membership Payment Received",
                        "'.($membership_amount_paid_update * -1).'",
                        "'.$_SESSION['valid_c'].'",
                        "'.$member_id.'",
                        "'.$basket_id.'",
                        "'.$delivery_id.'",
                        now(),
                        "'.$batchno.'",
                        "'.$memo.'",
                        "'.$comments.'",
                        "'.$payment_method2.'")';
                  $sql = mysql_query($query);
                }
            }
        }
    }
}
// End of update loop.
$sql_sum = '
  SELECT
    delivery_id,
    SUM(subtotal) AS sub_sum
  FROM
    '.TABLE_BASKET_ALL.'
  WHERE
    delivery_id = "'.$delivery_id.'"
  GROUP BY delivery_id';
$result_sum = @mysql_query($sql_sum,$connection) or die("Couldn't execute query 1b.");
while ( $row = mysql_fetch_array($result_sum) )
  {
    $subtotal_all = $row['sub_sum'];
  }
$sql_sum2 = '
  SELECT
    delivery_id,
    SUM(coopfee) AS coop_sum,
    SUM(surcharge_for_paypal) AS total_paypal
  FROM
    '.TABLE_BASKET_ALL.'
  WHERE
    delivery_id = "'.$delivery_id.'"
  GROUP BY '.TABLE_BASKET_ALL.'.delivery_id';
$result_sum2 = @mysql_query($sql_sum2,$connection) or die("Couldn't execute query 2.");
while ( $row = mysql_fetch_array($result_sum2) )
  {
    $coopfee_all = $row['coop_sum'];
    $total_paypal = $row['total_paypal'];
  }
$sql_sum3 = '
  SELECT
    delivery_id,
    SUM(delivery_cost) AS delivery_sum,
    SUM(transcharge) AS transcharge_sum
  FROM
    '.TABLE_BASKET_ALL.'
  WHERE
    '.TABLE_BASKET_ALL.'.delivery_id = "'.$delivery_id.'"
  GROUP BY '.TABLE_BASKET_ALL.'.delivery_id';
$result_sum3 = @mysql_query($sql_sum3,$connection) or die("Couldn't execute query 3.");
while ( $row = mysql_fetch_array($result_sum3) )
  {
    $delivery_cost_all = $row['delivery_sum'];
    $transcharge_all = $row['transcharge_sum'];
  }
$sql_sum4 = '
  SELECT
    delivery_id,
    SUM(grand_total) AS grandcust_sum
  FROM
    '.TABLE_BASKET_ALL.'
  WHERE
    delivery_id = "'.$delivery_id.'"
  GROUP BY delivery_id';
$result_sum4 = @mysql_query($sql_sum4,$connection) or die("Couldn't execute query 4.");
while ( $row = mysql_fetch_array($result_sum4) )
  {
    $grand_total_all = $row['grandcust_sum'];
  }
$sql_sum7 = '
  SELECT
    delivery_id,
    SUM(grand_total_coop) AS grandcoop_sum
  FROM
    '.TABLE_BASKET_ALL.'
  WHERE
    delivery_id = "'.$delivery_id.'"
  GROUP BY delivery_id';
$result_sum7 = @mysql_query($sql_sum7,$connection) or die("Couldn't execute query 5.");
while ( $row = mysql_fetch_array($result_sum7) )
  {
    $grand_total_all_coop = $row['grandcoop_sum'];
  }
$sql_sum6 = '
  SELECT
    '.TABLE_BASKET_ALL.'.delivery_id,
    '.TABLE_BASKET_ALL.'.basket_id,
    '.TABLE_BASKET.'.basket_id,
    '.TABLE_BASKET.'.out_of_stock,
    sum(quantity) AS sumq
  FROM
    '.TABLE_BASKET_ALL.',
    '.TABLE_BASKET.'
  WHERE
    '.TABLE_BASKET_ALL.'.delivery_id = "'.$delivery_id.'"
    AND '.TABLE_BASKET_ALL.'.basket_id = '.TABLE_BASKET.'.basket_id
    AND '.TABLE_BASKET.'.out_of_stock != "1"
  GROUP BY '.TABLE_BASKET_ALL.'.delivery_id';
$result_sum6 = @mysql_query($sql_sum6,$connection) or die("Couldn't execute query 6.");
while ( $row = mysql_fetch_array($result_sum6) )
  {
    $quantity_all = $row['sumq'];
  }
$surcharge = "";
$sql = '
  SELECT
    '.TABLE_BASKET_ALL.'.*,
    '.TABLE_MEMBER.'.member_id,
    '.TABLE_MEMBER.'.business_name,
    '.TABLE_MEMBER.'.first_name,
    '.TABLE_MEMBER.'.first_name_2,
    '.TABLE_MEMBER.'.last_name_2,
    '.TABLE_MEMBER.'.last_name,
    '.TABLE_DELDATE.'.delivery_id,
    '.TABLE_DELDATE.'.delivery_date,
    '.TABLE_PAY.'.*,
    DATE_FORMAT(order_date, "%b %d, %Y") AS last_modified,
    DATE_FORMAT(delivery_date, "%M %d, %Y") AS delivery_date
  FROM
    '.TABLE_BASKET_ALL.',
    '.TABLE_MEMBER.',
    '.TABLE_DELDATE.',
    '.TABLE_PAY.'
  WHERE
    '.TABLE_BASKET_ALL.'.delivery_id = "'.$delivery_id.'"
    AND '.TABLE_DELDATE.'.delivery_id = "'.$delivery_id.'"
    AND '.TABLE_BASKET_ALL.'.payment_method = '.TABLE_PAY.'.payment_method
    AND '.TABLE_BASKET_ALL.'.member_id = '.TABLE_MEMBER.'.member_id
  GROUP BY
    '.TABLE_BASKET_ALL.'.basket_id
  ORDER BY
    last_name ASC,
    business_name ASC';

$result = @mysql_query($sql,$connection) or die("Couldn't execute query 1.");
$numtotal = mysql_numrows($result);
while ( $row = mysql_fetch_array($result) )
  {
    $basket_id = $row['basket_id'];
    $member_id = $row['member_id'];
    $business_name = stripslashes ($row['business_name']);
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $first_name_2 = $row['first_name_2'];
    $last_name_2 = $row['last_name_2'];
    $delcode = $row['delcode'];
    $delivery_cost = $row['delivery_cost'];
    $transcharge = $row['transcharge'];
    $delivery_date = $row['delivery_date'];
    $payment_method = $row['payment_method'];
    $payment_desc = $row['payment_desc'];
    $surcharge_for_paypal = $row['surcharge_for_paypal'];
    $subtotal = $row['subtotal'];
    $coopfee = $row['coopfee'];
    $grand_total_cust = $row['grand_total'];
    $grand_total_coop = $row['grand_total_coop'];
    $last_modified = $row['last_modified'];
    $prev_balance = $row['prev_balance'];
    $amount_paid = $row['amount_paid'];
    $draft_emailed = $row['draft_emailed'];
    $finalized = $row['finalized'];

    // Get membership dues separately
    $query = '
      SELECT
        SUM('.TABLE_TRANSACTIONS.'.transaction_amount) AS total,
        '.TABLE_TRANS_TYPES.'.ttype_parent
      FROM
        '.TABLE_TRANSACTIONS.'
      LEFT JOIN
        '.TABLE_TRANS_TYPES.' ON '.TABLE_TRANS_TYPES.'.ttype_id = '.TABLE_TRANSACTIONS.'.transaction_type
      WHERE
        transaction_delivery_id <= "'.$delivery_id.'"
        AND ttype_parent = "40"
        AND transaction_member_id = "'.$member_id.'"
      GROUP BY transaction_member_id';
    $sql = @mysql_query($query) or die(mysql_error());
    if ($row = mysql_fetch_array($sql))
      {
        $membership_dues = $row['total'];
      }

    if ( $current_basket_id < 0 )
      {
        $current_basket_id = $row['basket_id'];
      }
    while ( $current_basket_id != $basket_id )
      {
        $current_basket_id = $basket_id;
        $cust_salestax = "";
        $sql_sums = '
          SELECT
            collected_statetax,
            collected_citytax,
            collected_countytax
          FROM
            '.TABLE_CUSTOMER_SALESTAX.'
          WHERE
            customer_salestax.basket_id = "'.$basket_id.'"';
        $result_sums = @mysql_query($sql_sums,$connection) or die("Couldn't execute query sales tax.");
        while ( $row = mysql_fetch_array($result_sums) )
          {
            $collected_statetax = $row['collected_statetax'];
            $collected_citytax = $row['collected_citytax'];
            $collected_countytax = $row['collected_countytax'];
            $cust_salestax = $collected_statetax + $collected_citytax + $collected_countytax;
            $total_salestax = $cust_salestax + $total_salestax + 0;
          }
        $draft_emailed = '';
        if ( $draft_emailed )
          {
            $draft_emailed = 'Y';
          }
        $final_invoice = '';
        if ( $finalized )
          {
            $final_invoice = 'Y';
          }
        if ( $payment_method == 'P')
          {
            $p_chk = "checked";
            $c_chk = "";
            if ( $delivery_id > DELIVERY_NO_PAYPAL )
              {
                $subtotal_1 = $subtotal + $coopfee + $transcharge + $delivery_cost + $cust_salestax;
                $total_sent_to_paypal = ($subtotal_1 + .30) / .971;
                //$surcharge = number_format((($total_sent_to_paypal*.029) + .30),2);
                $surcharge = number_format($surcharge_for_paypal, 2);
                if ($surcharge_for_paypal) $minus_paypal = "<br>-$surcharge for paying by check/cash";
              }
          }
        elseif ( $payment_method == 'C' )
          {
            $c_chk = 'checked';
            $p_chk = '';
            $surcharge = '';
            $minus_paypal = '';
          }
        else
          {
            $c_chk = '';
            $p_chk = '';
            $surcharge = '';
            $minus_paypal = '';
          }
        $quantity_mem = '';
        $sql_sum8 = '
          SELECT
            '.TABLE_BASKET_ALL.'.delivery_id,
            '.TABLE_BASKET_ALL.'.basket_id,
            '.TABLE_BASKET.'.basket_id,
            '.TABLE_BASKET.'.out_of_stock,
            sum(quantity) AS sum_mem
          FROM
            '.TABLE_BASKET_ALL.',
            '.TABLE_BASKET.'
          WHERE
            '.TABLE_BASKET_ALL.'.delivery_id = "'.$delivery_id.'"
            AND '.TABLE_BASKET_ALL.'.basket_id = '.TABLE_BASKET.'.basket_id
            AND '.TABLE_BASKET.'.out_of_stock != "1"
            AND '.TABLE_BASKET_ALL.'.member_id = "'.$member_id.'"
          GROUP BY
            '.TABLE_BASKET_ALL.'.delivery_id';
        $result_sum8 = @mysql_query($sql_sum8,$connection) or die("Couldn't execute query 8.");
        while ( $row = mysql_fetch_array($result_sum8) )
          {
            $quantity_mem = $row['sum_mem'];
          }
        // to override the amount paid from the customer_basket_overall
        $sql_t = mysql_query('
          SELECT
            SUM(transaction_amount) as amount_paid FROM
            '.TABLE_TRANSACTIONS.'
          WHERE
            transaction_type = "23"
            AND transaction_member_id = "'.$member_id.'"
            AND transaction_delivery_id = "'.$delivery_id.'"');
        $result_t = mysql_fetch_array($sql_t);
        if ( $result_t['amount_paid'] > 0 )
          {
            $amount_paid = $result_t['amount_paid'];
          }
        $amount_paid = number_format($amount_paid, 2, '.', '');
        $discrepancy = 0;
        $discrepancy = $grand_total_cust - $surcharge - $amount_paid;
        if ( $delivery_id == 10 )
          {
            $delivery_id_previous = $delivery_id - 2;
          }
        else
          {
            $delivery_id_previous = $delivery_id - 1;
          }
        $discrepancy_previous = '';
        $grand_total_cust_previous = '';
        $surcharge_previous = '';
        $amount_paid_previous = '';
        $sqldp = '
          SELECT
            '.TABLE_BASKET_ALL.'.delivery_id,
            '.TABLE_BASKET_ALL.'.grand_total,
            '.TABLE_BASKET_ALL.'.amount_paid,
            '.TABLE_BASKET_ALL.'.surcharge_for_paypal
          FROM
            '.TABLE_BASKET_ALL.'
          WHERE
            '.TABLE_BASKET_ALL.'.delivery_id = "'.$delivery_id_previous.'"
            AND '.TABLE_BASKET_ALL.'.member_id = "'.$member_id.'"';
        $resultdp = @mysql_query($sqldp,$connection) or die("Couldn't execute query for previous discrepancy.");
        while ( $row = mysql_fetch_array($resultdp) )
          {
            $grand_total_cust_previous = $row['grand_total'];
            $surcharge_previous = number_format($row['surcharge_for_paypal'], 2);
            $amount_paid_previous = $row['amount_paid'];
          }
        $transaction_batchno = "";
        $transaction_memo = "";
        $transaction_comments = "";
        $sqlt = mysql_query('
          SELECT
            transaction_batchno,
            transaction_memo,
            transaction_comments 
          FROM
            '.TABLE_TRANSACTIONS.' t
          WHERE
            t.transaction_type ="23"
            AND t.transaction_member_id = "'.$member_id.'"
            AND t.transaction_basket_id ="'.$basket_id.'"
          ORDER BY
            transaction_id DESC LIMIT 1');
        $trans = mysql_fetch_array($sqlt);
        $discrepancy_previous = $grand_total_cust_previous - $surcharge_previous - $amount_paid_previous;
        if ( $discrepancy_previous )
          {
            $discrep_color = 'bgcolor="#FFCC66"';
          }
        else
          {
            $discrep_color = '';
          }
        include("../func/show_name_last.php");
        if ( $discrepancy == 0 || $finalized != 1 )
          {
            $mismatch_color = 'bgcolor="#DDDDDD"';
          }
        else
          {
            $mismatch_color = 'bgcolor="#FFCC33"';
          }
        if ( $finalized != 1 )
          {
            $unfinalized = '<font size="-2" color="#880000"><br>Unfinalized</font>';
          }
        else
          {
            $unfinalized = '';
          }
        $display_month .= '
          <tr>
            <td align="right" valign="top"><font face="arial" size="-1"><b># '.$member_id.'</b></td>
            <td align="left" valign="top"><font face="arial" size="-1">
              <b><a href="customer_invoice.php?member_id='.$member_id.'&basket_id='.$basket_id.'&delivery_id='.$delivery_id.'" target="_blank">'.$show_name.'</a></b>&nbsp;&nbsp;</td>
            <td align="right" valign="top"><font face="arial" size="-1">
              <input type=radio name="payment_method['.$basket_id.']" value="P" '.$p_chk.'>P
              <input type=radio name="payment_method['.$basket_id.']" value="C" '.$c_chk.'>C</td>
            <td align="right" valign="top" '.$discrep_color.'><font face="arial" size="-1">$'.number_format($discrepancy_previous, 2).'</td>
            <td align="right" valign="top" '.$mismatch_color.'><font face="arial" size="-1">$'.$amount_paid.'</td>
            <td align="right" valign="top" '.$mismatch_color.'><nobr><font face="arial" size="-1"><b>$'.number_format($grand_total_cust, 2).'</b> '.$minus_paypal.'<br>
              $<input type="text" name="amount_paid['.$basket_id.']" size="5" maxlength="10"> '.$unfinalized.'</nobr></td>
            <td align="right" valign="top" '.$mismatch_color.'><nobr><font face="arial" size="-1">$'.number_format($membership_dues, 2).'<br>
              $<input type="text" name="membership_amount_paid['.$basket_id.']" size="5" maxlength="10"> </nobr></td>
            <td align="right" valign="top"><font face="arial" size="-1">$'.number_format($discrepancy + $membership_dues, 2).'
              <input type="hidden" name="member_id['.$basket_id.']" value="'.$member_id.'"></td>
            <td><input type="input" name="transaction_batchno['.$basket_id.']" value="'.$trans['transaction_batchno'].'" maxlength="8" size="4"></td>
            <td><input type="input" name="transaction_memo['.$basket_id.']" value="'.$trans['transaction_memo'].'" maxlength="20" size="10"></td>
            <td><input type="input" name="transaction_comments['.$basket_id.']" value="'.$trans['transaction_comments'].'" maxlength="200"></td>
            <td align="right" valign="top"><font face="arial" size="-2"><i>'.$last_modified.'</i></td>
          </tr>';
        $amount_paid_total = $amount_paid_total + $amount_paid + 0;
      }
  }
$fontface='arial';
?>
<html>
<body bgcolor="#FFFFFF">
<font face="<?php echo $fontface;?>">
<h2>Monthly Breakdown by Customer: Delivery Date <?php echo $delivery_date;?></h2>
<b>Total Products Sold: <?php echo $quantity_all;?> Products &nbsp;&nbsp;&nbsp;
Total Orders: <?php echo $numtotal;?></b> &nbsp;&nbsp;&nbsp;<font size="-1">(Print Landscape for best results.)</font>
<br>Click here for <a href="ctotals_reports.php?delivery_id=<?php echo$delivery_id;?>">Customer Totals Report</a> |
Click here for <a href="totals_saved.php">Previous Reports</a>
<br><font color="#CC9900"><?php echo $message;?></font>
<hr>
<form action='<?php echo $_SERVER['PHP_SELF'];?>?delivery_id=<?php echo$delivery_id;?>&updatevalues=ys' method='post'>
<table cellpadding="2" cellspacing="0" border="1">
  <tr>
    <th valign="bottom" bgcolor="#DDDDDD"><font face="<?php echo $fontface;?>" size="-2">Mem. ID</th>
    <th valign="bottom" bgcolor="#DDDDDD"><font face="<?php echo $fontface;?>" size="-2">Member Name</th>
    <th valign="bottom" bgcolor="#DDDDDD"><font face="<?php echo $fontface;?>" size="-2">Payment Method</th>
    <th valign="bottom" bgcolor="#DDDDDD"><font face="<?php echo $fontface;?>" size="-2">Previous<br>Discrepency</th>
    <th valign="bottom" bgcolor="#DDDDDD"><font face="<?php echo $fontface;?>" size="-2">Amount Paid So Far</th>
    <th valign="bottom" bgcolor="#DDDDDD"><font face="<?php echo $fontface;?>" size="-2">Shopping <br>Due / Pmt.</th>
    <th valign="bottom" bgcolor="#DDDDDD"><font face="<?php echo $fontface;?>" size="-2">Membership <br>Due / Pmt.</th>
    <th valign="bottom" bgcolor="#DDDDDD"><font face="<?php echo $fontface;?>" size="-2">Total Amount Owed</th>
    <th valign="bottom" bgcolor="#DDDDDD"><font face="<?php echo $fontface;?>" size="-2">Batch No.</th>
    <th valign="bottom" bgcolor="#DDDDDD"><font face="<?php echo $fontface;?>" size="-2">Memo</th>
    <th valign="bottom" bgcolor="#DDDDDD"><font face="<?php echo $fontface;?>" size="-2">Comments</th>
    <th valign="bottom" bgcolor="#DDDDDD"><font face="<?php echo $fontface;?>" size="-2">Last Modified</th>
  </tr>
<?php echo $display_month;?>
  <tr><td bgcolor="#AEDE86" colspan="5" align="right">
  </td><td bgcolor="#AEDE86" align="right">
  <b><?php echo '$'.number_format($amount_paid_total,2); ?></b>
  </td><td bgcolor="#AEDE86" colspan="6" align="right">
    <input name="where" type="submit" value="SAVE CHANGES">
  </td></tr>
</table>
  </form>
<?php include("template_footer.php");?>
