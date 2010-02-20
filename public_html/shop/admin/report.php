<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

if ( !$_REQUEST['year'] )
  {
    $year = date('Y');
    $next_year = date('Y') + 1;
  }
else
  {
    $year = $_REQUEST['year'];
    $next_year = $_REQUEST['year'] + 1;
  }
$report = '
  Grand Total = Products Subtotal + Sales tax + Coop Charges + Home Delivery + (-)Missing Ticket Items + (-)Producer discounts<br >
  Coop Charges = The Shipping & Handling line on a customer invoice<br />
  <table border="1" cellpadding="5">
    <tr>
      <th>Delivery Date</th>
      <th>Total Orders</th>
      <th>Grand Total</th>
      <th><a href="salestax.php">Total Taxes</a></th>
      <th>Total Coop Charges</th>
      <th>Total Work Credits</th>
      <th>Donations</th>
    </tr>';
$sql = '
  SELECT
    ba.delivery_id,
    DATE_FORMAT(delivery_date, "%M %d, %Y") AS delivery_date,
    SUM(ROUND(stax.collected_statetax, 2)) AS sum1,
    SUM(ROUND(stax.collected_citytax, 2)) AS sum2,
    SUM(ROUND(stax.collected_countytax, 2)) AS sum3,
    count(ba.basket_id) AS total_orders
  FROM
    (
      '.TABLE_BASKET_ALL.' ba,
      '.TABLE_DELDATE.' dd
    )
  LEFT JOIN '.TABLE_CUSTOMER_SALESTAX.' stax ON  stax.basket_id = ba.basket_id
  WHERE
    ba.delivery_id = dd.delivery_id
    AND dd.delivery_date >= "'.$year.'" AND dd.delivery_date <= "'.$next_year.'"
  GROUP BY
    ba.delivery_id
  ORDER BY
    ba.delivery_id DESC';
$rs = @mysql_query($sql,$connection) or die(mysql_error());
while ( $row2 = mysql_fetch_array($rs) )
  {
    $total_taxes = $row2['sum1'] + $row2['sum2'] + $row2['sum3'];
    $coop_charges = 0;
    $grand_total = 0;
    $overall_total = 0;
    $totalsql = mysql_query('
      SELECT
        ROUND(collected_statetax, 2) AS collected_statetax,
        ROUND(collected_citytax, 2) AS collected_citytax,
        ROUND(collected_countytax, 2) AS collected_countytax,
        ba.subtotal,
        ba.coopfee,
        ba.transcharge,
        ba.delivery_cost,
        ba.sh,
        ba.basket_id
      FROM
        '.TABLE_BASKET_ALL.' ba
      LEFT JOIN customer_salestax stax ON  stax.basket_id = ba.basket_id
      WHERE
        ba.delivery_id = "'.$row2["delivery_id"].'"');
    while ( $totals = mysql_fetch_array($totalsql) )
      {
        // Non-taxed adjustments
        $sqladjn = mysql_query('
          SELECT
            SUM(adj_amount) as adjustment_sum
          FROM
            '.TABLE_ADJ.' adj,
            '.TABLE_BASKET_ALL.' ba
          WHERE
            adj_taxed = "0"
            AND adj.basket_id = ba.basket_id
            AND ba.delivery_id = "'.$row2["delivery_id"].'"
            AND ba.basket_id = "'.$totals["basket_id"].'"');
        $adjnont = mysql_fetch_array($sqladjn);
        $subtotal_1 = 0;
        $subtotal_1 = $totals['subtotal'] + $totals['coopfee'] + $totals['transcharge'] + $totals['delivery_cost'] + $totals['sh'] + $totals['collected_statetax'] + $totals['collected_citytax'] + $totals['collected_countytax'] + $adjnont['adjustment_sum'];
        if ( $totals['subtotal'] <= 0 )
          {
            $cash_discount = .31;
          }
        else
          {
            //$total_sent_to_paypal = (($subtotal_1 + .30)/ .971);
            $cash_discount = number_format((((($subtotal_1 + .30)/ .971)*.029) + .30),4);
          }
        if ( $subtotal_1 <= 0 )
          {
            $cash_discount = 0;
          }
        $coop_charges = $coop_charges + $totals['sh'] + number_format($cash_discount, 2) + 0;
        $overall_total = $overall_total + $totals['subtotal'] + $totals['collected_statetax'] + $totals['collected_citytax'] + $totals['collected_countytax'] + ($totals['sh'] + number_format($cash_discount,2)) + $totals['delivery_cost'];
      }
    // Work Credit adjustments
    $sqladjw = mysql_query('
      SELECT
        SUM(adj_amount) as adjustment_sum
      FROM
        '.TABLE_ADJ.' adj,
        '.TABLE_BASKET_ALL.' ba
      WHERE
        (adjt_id = "3" or adjt_id = "11" or adjt_id = "12" or adjt_id = "15")
        AND adj.basket_id = ba.basket_id
        AND ba.delivery_id = "'.$row2["delivery_id"].'"
      GROUP BY
        ba.delivery_id');
    $adjw = mysql_fetch_array($sqladjw);
    // Donations - for OKFood Coop, it's subcategory 56
    $sql_donations = mysql_query('
      SELECT
        SUM(item_price*quantity) as product_sum
      FROM
        '.TABLE_BASKET.' b,
        '.TABLE_BASKET_ALL.' ba,
        '.TABLE_PRODUCT.' p
      WHERE
        ba.delivery_id = "'.$row2["delivery_id"].'"
        AND b.basket_id = ba.basket_id
        AND b.product_id = p.product_id
        AND p.subcategory_id = "56"
      GROUP BY ba.delivery_id');
    $donations = mysql_fetch_array($sql_donations);
    // Missing ticket item + Producer Discount adjustments
    $queryadj = '
      SELECT
        SUM(adj_amount) as adjustment_sum
      FROM
        '.TABLE_ADJ.' a,
        '.TABLE_BASKET_ALL.' ba
      WHERE
        (
          adjt_id = "8"
          OR adjt_id = "14"
        )
        AND a.basket_id = ba.basket_id
        AND ba.delivery_id = "'.$row2["delivery_id"].'"';
    $sqladj = mysql_query($queryadj);
    $adj = mysql_fetch_array($sqladj);
    $grand_total = $overall_total + $adj['adjustment_sum'];
    $report .= '
      <tr align="right">
        <td><a href="ctotals_reports.php?delivery_id='.$row2['delivery_id'].'">'.$row2['delivery_date'].'</a></td>
        <td>'.$row2['total_orders'].'</td>
        <td>$'.number_format($grand_total, 2).'</td>
        <td>$'.number_format($total_taxes, 2).'</td>
        <td>$'.number_format($coop_charges, 2).'</td>
        <td>$'.number_format($adjw['adjustment_sum'], 2).'</td>
        <td>$'.number_format($donations['product_sum'], 2).'</td>
      </tr>';
  }
$report .= '
    </table>';
?>
<html>
<body bgcolor="#FFFFFF">
<font face="arial">
<table cellpadding="15">
  <tr>
    <td valign="top">
      <h2><?php  echo $year;?> Totals</h2>
<?php
$sql = mysql_query('
  SELECT
    delivery_date
  FROM
    '.TABLE_DELDATE.'
  ORDER BY
    delivery_date ASC
  LIMIT 1');
$result = mysql_fetch_array($sql);
$first_year = substr($result['delivery_date'],0,4);
for( $yr = $first_year; $yr <= date('Y'); $yr++ )
  {
    if ( $year == $yr )
      {
        echo $yr;
      }
    else
      {
        echo ' <a href="'.$_SERVER['PHP_SELF'].'?year='.$yr.'">'.$yr.'</a> ';
      }
  }
?>
      <br/>
      <?php echo $report;?>
    </td>
  </tr>
</table>
<?php include("template_footer.php");?>
