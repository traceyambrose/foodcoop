<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

if ( $fin == 'unfinalize' )
  {
    $sqlf = '
      UPDATE
        '.TABLE_BASKET_ALL.'
      SET
        finalized = "0"
      WHERE
        basket_id = "'.$basket_id.'"
        AND member_id = "'.$member_id.'"';
    $resultf = @mysql_query($sqlf, $connection) or die("<br><br>You found a bug. If there is an error listed below, please copy and paste the error into an email to <a href=\"mailto:".WEBMASTER_EMAIL."\">".WEBMASTER_EMAIL."</a><br><br><b>Error:</b> Unfinalizing invoice " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
  }
$hub = '';
$sql = '
  SELECT
    '.TABLE_BASKET_ALL.'.basket_id AS big_basket_id,
    '.TABLE_BASKET_ALL.'.basket_id,
    '.TABLE_BASKET_ALL.'.member_id,
    '.TABLE_BASKET_ALL.'.delivery_id,
    '.TABLE_BASKET_ALL.'.delivery_id AS basket_delivery_id,
    '.TABLE_MEMBER.'.member_id,
    '.TABLE_BASKET_ALL.'.finalized,
    '.TABLE_MEMBER.'.last_name,
    '.TABLE_MEMBER.'.first_name,
    '.TABLE_MEMBER.'.business_name,
    '.TABLE_BASKET_ALL.'.rte_confirmed,
    '.TABLE_DELCODE.'.delcode_id,
    '.TABLE_BASKET_ALL.'.delcode_id,
    '.TABLE_DELCODE.'.hub
  FROM
    '.TABLE_BASKET_ALL.',
    '.TABLE_MEMBER.',
    '.TABLE_DELCODE.'
  WHERE
    '.TABLE_BASKET_ALL.'.delivery_id = "'.$delivery_id.'"
    AND '.TABLE_BASKET_ALL.'.member_id = '.TABLE_MEMBER.'.member_id
    AND '.TABLE_BASKET_ALL.'.delcode_id = '.TABLE_DELCODE.'.delcode_id
  GROUP BY
    '.TABLE_BASKET_ALL.'.member_id
  ORDER BY
    last_name ASC,
    '.TABLE_BASKET_ALL.'.basket_id DESC';
$rs = @mysql_query($sql,$connection) or die("<br><br>You found a bug. If there is an error listed below, please copy and paste the error into an email to <a href=\"mailto:".WEBMASTER_EMAIL."\">".WEBMASTER_EMAIL."</a><br><br><b>Error:</b> Listing customer orders " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
$num_orders = mysql_numrows($rs);
while ( $row = mysql_fetch_array($rs) )
  {
    $hub = $row['hub'];
    $basket_id = $row['big_basket_id'];
    $basket_delivery_id = $row['basket_delivery_id'];
    $member_id = $row['member_id'];
    $last_name = $row['last_name'];
    $first_name = $row['first_name'];
    $business_name = stripslashes ($row['business_name']);
    $rte_confirmed = $row['rte_confirmed'];
    $finalized = $row['finalized'];
    include("../func/show_name_last.php");
    $subtotal = '';
    $sql2 = '
      SELECT
        '.TABLE_BASKET.'.*,
        '.TABLE_PRODUCT.'.product_id,
        '.TABLE_PRODUCT.'.random_weight
      FROM
        '.TABLE_PRODUCT.',
        '.TABLE_BASKET.'
      WHERE
        '.TABLE_BASKET.'.basket_id = "'.$basket_id.'"
        AND '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id';
    $result2 = @mysql_query($sql2,$connection) or die("<br><br>You found a bug. If there is an error listed below, please copy and paste the error into an email to <a href=\"mailto:".WEBMASTER_EMAIL."\">".WEBMASTER_EMAIL."</a><br><br><b>Error:</b> Selecting product information " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    while ( $row = mysql_fetch_array($result2) )
      {
        //$basket_id = $row['basket_id'];
        $item_price = $row['item_price'];
        $quantity = $row['quantity'];
        $out_of_stock = $row['out_of_stock'];
        $random_weight = $row['random_weight'];
        $total_weight = $row['total_weight'];
        $extra_charge = $row['extra_charge'];
        $future_delivery_id = $row['future_delivery_id'];
        $item_total_price = "";
        if (( $future_delivery_id == $delivery_id ))
          {
            $item_total_3dec = number_format(($item_price*$quantity),3) + 0.00000001;
            $item_total_price = round($item_total_3dec, 2);
          }
        elseif ( $out_of_stock != 1 )
          {
            if ( $random_weight == 1 )
              {
                if ( $total_weight == 0 )
                  {
                    $message_incomplete = '<font color="#770000">Order Incomplete</font>';
                  }
                else
                  {
                    //$display_weight = "$total_weight";
                  }
                $item_total_3dec = number_format((($item_price * $total_weight) + ($quantity * $extra_charge)), 3) + 0.00000001;
                $item_total_price = round($item_total_3dec, 2);
              }
            else
              {
                $item_total_3dec = number_format((($item_price * $quantity) + ($quantity * $extra_charge)), 3) + 0.00000001;
                $item_total_price = round($item_total_3dec, 2);
              }
          }
        else
          {
            $item_total_price = 0;
          }
        $subtotal = $subtotal + $item_total_price;
      }
    $adj_total = '';
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
    $result_adj = @mysql_query($sqladj,$connection) or die("<br><br>You found a bug. If there is an error listed below, please copy and paste the error into an email to <a href=\"mailto:".WEBMASTER_EMAIL."\">".WEBMASTER_EMAIL."</a><br><br><b>Error:</b> Selecting customer information " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    while ( $row = mysql_fetch_array($result_adj) )
      {
        $adjid = $row['adjid'];
        $adj_name = $row['adj_name'];
        $adj_amount = $row['adj_amount'];
        $adj_desc = $row['adj_desc'];
        $adj_taxed = $row['adj_taxed'];
        $adj_total = $adj_amount+$adj_total;
      }
    $total = $subtotal + $adj_total;
    $bigtotal = $total + $bigtotal;
    $bigsubtotal = $bigsubtotal + $subtotal;
    $bigadj = $bigadj + $adj_total;
    $display .= '<tr bgcolor="#FFFFFF">';
    if ( $total < 0 )
      {
        $display .= '<td valign="top" align="right" bgcolor="#CC9900"><a name="'.$basket_id.'">$'.number_format($total,2).'</td>';
      }
    else
      {
        $display .= '<td valign="top" align="right" bgcolor="#AEDE86"><a name="'.$basket_id.'">$'.number_format($total,2).'</td>';
      }
    $display .= '<td valign="top" align="right"># '.$member_id.'</td>';
    if ( $finalized != 1 )
      {
        $display .= '<td valign="top"><a href="orders.php?delivery_id='.$delivery_id.'&basket_id='.$basket_id.'&member_id='.$member_id.'">'.$show_name.'</a></td>';
      }
    else
      {
        $display .= '<td valign="top">'.$show_name.'</td>';
      }
    $display .= '<td>';
    $sqlp = '
      SELECT '.TABLE_PRODUCT.'.product_id,
        '.TABLE_PRODUCT.'.random_weight,
        '.TABLE_BASKET.'.basket_id,
        '.TABLE_BASKET.'.out_of_stock,
        '.TABLE_BASKET.'.total_weight,
        '.TABLE_BASKET.'.product_id,
        '.TABLE_BASKET_ALL.'.basket_id,
        '.TABLE_BASKET_ALL.'.member_id,
        '.TABLE_PRODUCT.'.producer_id
      FROM
        '.TABLE_BASKET.',
        '.TABLE_PRODUCT.',
        '.TABLE_BASKET_ALL.'
        WHERE
        '.TABLE_BASKET_ALL.'.member_id = "'.$member_id.'"
        AND '.TABLE_BASKET_ALL.'.basket_id = "'.$basket_id.'"
        AND '.TABLE_BASKET_ALL.'.basket_id = '.TABLE_BASKET.'.basket_id
        AND '.TABLE_PRODUCT.'.product_id = '.TABLE_BASKET.'.product_id
        AND '.TABLE_BASKET.'.out_of_stock != "1"
        AND '.TABLE_PRODUCT.'.random_weight = "1"
        AND '.TABLE_BASKET.'.total_weight = "0"
        ORDER BY producer_id ASC';
    $resultprp = @mysql_query($sqlp,$connection) or die("Couldn't execute query 1.");
    $num = mysql_numrows($resultprp);
    while ( $row = mysql_fetch_array($resultprp) )
      {
        $product_id = $row['product_id'];
        $display .= '<a href="orders.php?delivery_id='.$delivery_id.'&basket_id='.$basket_id.'&member_id='.$member_id.'#'.$product_id.'">Weight needed for Product #'.$product_id.'</a><br>';
      }
    $display .= '</td>';
    if ( $basket_delivery_id != $delivery_id )
      {
        $display .= '<td>*Need to <a href="orders_selectmember.php">create an invoice</a> for this cycle*</td>';
      }
    else
      {
        if ( $finalized != 1 )
          {
            $display .= '<td valign="top"><font size="2"><a href="customer_invoice.php?delivery_id='.$delivery_id.'&basket_id='.$basket_id.'&member_id='.$member_id.'">View Temp. Inv.</a></font></td>';
          }
        else
          {
            $display .= '<td></td>';
          }
      }
    if ( $basket_delivery_id != $delivery_id )
      {
        $display .= '<td valign="top" align="center" bgcolor="'.$hubcolor.'"><font size="-2">no / '.$hub.'</font></td>';
      }
    else
      {
        if ( $rte_confirmed == 1 )
          {
            $display .= '<td valign="top" align="center" bgcolor="'.$hubcolor.'">Yes</td>';
          }
        else
          {
            $display .= '<td valign="top" align="center" bgcolor="'.$hubcolor.'"><font size="-2">no / '.$hub.'</font></td>';
          }
      }
    if ( $basket_delivery_id != $delivery_id )
      {
        $display .= '<td valign="top" colspan="2">*Has a pre-ordered item in <a href="customer_invoice.php?delivery_id='.$basket_delivery_id.'&basket_id='.$basket_id.'&member_id='.$member_id.'">Basket '.$basket_id.'</a> for delivery this month*</td>';
      }
    else
      {
        if ( $finalized != 1 )
          {
            $display .= '<td valign="top" bgcolor="#AEDE86">Not saved as final version</td>';
            $display .= '<td valign="top"></td>';
          }
        else
          {
            $display .='<td valign="top"><a href="invoice.php?basket_id='.$basket_id.'&member_id='.$member_id.'">Final Invoice</a> (Mem# '.$member_id.') </td>';
            $display .= '<td valign="top"><font size="-2"><a href="'.$PHP_SELF.'?fin=unfinalize&delivery_id='.$delivery_id.'&basket_id='.$basket_id.'&member_id='.$member_id.'#'.$basket_id.'">Unfinalize to Edit</a></font></td>';
          }
      }
    $display .= '</tr>';
    $member_id_list .= '#'.$member_id;
  }
$sqlfd = '
  SELECT '.TABLE_BASKET_ALL.'.basket_id,
    '.TABLE_BASKET_ALL.'.member_id,
    '.TABLE_BASKET_ALL.'.delivery_id,
    '.TABLE_BASKET_ALL.'.finalized,
    '.TABLE_BASKET.'.future_delivery_id,
    '.TABLE_BASKET.'.basket_id
  FROM
    '.TABLE_BASKET_ALL.',
    '.TABLE_DELCODE.',
    '.TABLE_BASKET.'
    WHERE
    '.TABLE_BASKET.'.future_delivery_id = "'.$delivery_id.'"
    AND '.TABLE_BASKET_ALL.'.basket_id = '.TABLE_BASKET.'.basket_id
    AND '.TABLE_BASKET_ALL.'.delivery_id != "'.$delivery_id.'"
    AND '.TABLE_BASKET_ALL.'.member_id != "'.$member_id.'"
    GROUP BY '.TABLE_BASKET_ALL.'.member_id
    ORDER BY '.TABLE_BASKET_ALL.'.basket_id DESC';
$rsf = @mysql_query($sqlfd,$connection) or die("<br><br>You found a bug. If there is an error listed below, please copy and paste the error into an email to <a href=\"mailto:".WEBMASTER_EMAIL."\">".WEBMASTER_EMAIL."</a><br><br><b>Error:</b> Listing customer orders " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
while ( $row = mysql_fetch_array($rsf) )
  {
    $basket_id2 = $row['basket_id'];
    $member_id2 = $row['member_id'];
    $future_delivery_id2 = $row['future_delivery_id'];
    $member_id2 = "#$member_id2";
    $pos = strpos($member_id_list,$member_id2);
    if ( $pos === false )
      {
        $memneedinvoice .= ' <font color="#FF0000"><b> '.$member_id2.'</b></font> &nbsp;';
      }
    else
      {
        $memneedinvoice .= '';
      }
  }
include("template_hdr.php");
?>
<div align="right">
  [ <a href="index.php">Main Page</a>
  | <a href="ctotals_onebutton.php?delivery_id=<?php echo $delivery_id;?>">Customer Totals</a>
  | <a href="logout.php">Logout</a> ]
</div>
<!-- CONTENT BEGINS HERE -->
<div align="center">
<table width="90%">
  <tr>
    <td align="left">
<?
include("../func/show_delivery_date.php");
include("../func/convert_delivery_date.php");
if ( $delivery_id == $current_delivery_id )
  {
    $delivery_date = $current_delivery_date;
  }
?>
      <h3>Saved Orders: <?php echo $delivery_date;?> (<?php echo $num_orders;?> Orders)</h3>
      Current Combined SUBTOTAL: <b>$<?php echo number_format($bigtotal,2);?></b> (Subtotal $<?php echo number_format($bigsubtotal,2);?> + Adjustments $<?php echo number_format($bigadj,2);?>)<br>
      (includes adjustments, doesn&#146;t include taxes and delivery charges)<br/>
      Click to finalize invoices for members id #:
      <a href="finalize.php?delivery_id=<?php echo $delivery_id;?>&ms=1&mf=100">1-100</a> |
      <a href="finalize.php?delivery_id=<?php echo $delivery_id;?>&ms=101&mf=200">101-200</a> |
      <a href="finalize.php?delivery_id=<?php echo $delivery_id;?>&ms=201&mf=300">201-300</a> |
      <a href="finalize.php?delivery_id=<?php echo $delivery_id;?>&ms=301&mf=400">301-400</a> |
      <a href="finalize.php?delivery_id=<?php echo $delivery_id;?>&ms=401&mf=500">401-500</a> |
      <a href="finalize.php?delivery_id=<?php echo $delivery_id;?>&ms=501&mf=600">501-600</a> |
      <a href="finalize.php?delivery_id=<?php echo $delivery_id;?>&ms=601&mf=700">601-700</a> |
      <a href="finalize.php?delivery_id=<?php echo $delivery_id;?>&ms=701&mf=800">701-800</a> |
      <a href="finalize.php?delivery_id=<?php echo $delivery_id;?>&ms=801&mf=900">801-900</a> |
      <a href="finalize.php?delivery_id=<?php echo $delivery_id;?>&ms=901&mf=1000">901-1000</a> |
      <a href="finalize.php?delivery_id=<?php echo $delivery_id;?>&ms=1001&mf=1100">1101-1200</a> |
      <a href="finalize.php?delivery_id=<?php echo $delivery_id;?>&ms=1101&mf=1200">1101-1200</a> |
      <a href="finalize.php?delivery_id=<?php echo $delivery_id;?>&ms=1201&mf=1300">1201-1300</a> |
      <a href="finalize.php?delivery_id=<?php echo $delivery_id;?>&ms=1301&mf=1400">1301-1400</a> |
      <a href="finalize.php?delivery_id=<?php echo $delivery_id;?>&ms=1401&mf=1500">1401-1500</a><br><br>
<?php
if ( $memneedinvoice )
  {
    echo "*Need to <a href='orders_selectmember.php'>create an invoice</a> for this cycle for these members:";
    echo "$memneedinvoice<br><br>";
  }
?>
<table bgcolor="#DDDDDD" cellpadding="2" cellspacing="2" border="0">
  <tr bgcolor="#AEDE86">
    <th valign="bottom" bgcolor="#CC9900"><font face="arial" size="-2">Subtotal</th>
    <th>Mem. ID</th>
    <th>Member (Click to Edit Order)</th>
    <th>Order Completion</th>
    <th>Temp. Invoice</th>
    <th valign="bottom" bgcolor="#ADB6C6"><font face="arial" size="-2">Rte. Mgr<br>Confirmed</th>
    <th>Finalized After Delivery</th>
    <th>UnFinalize</th>
  </tr>
  <?php echo $display;?>
</table>
</td></tr>
</table>
</div>
<!-- CONTENT ENDS HERE -->
<?php include("template_footer.php");?>
</body>
</html>
