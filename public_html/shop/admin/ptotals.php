<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

$date_today = date("F j, Y");
if ( $updatevalues == 'ys' )
  {
    $sqlu = '
      UPDATE
        '.TABLE_PRODUCER_TOTALS.'
        SET
          amount_paid = "'.$amount_paid.'"
      WHERE
        producer_id = "'.$producer_id.'"
        AND delivery_id = "'.$delivery_id.'"';
    $resultu = @mysql_query($sqlu,$connection) or die(mysql_error());
    $message = '<H3>The information has been updated.</h3>';
  }
$sql_sum3 = '
  SELECT
    delivery_id,
    SUM(prod_total) AS total_prod_sum
  FROM
    '.TABLE_PRODUCER_TOTALS.'
  WHERE
    delivery_id = "'.$delivery_id.'"
  GROUP BY
    delivery_id';
$result_sum3 = @mysql_query($sql_sum3,$connection) or die("Couldn't execute query 3.");
while ( $row = mysql_fetch_array($result_sum3) )
  {
    $total_prod_sum = $row['total_prod_sum'];
  }
$sql_sum4 = '
  SELECT
    delivery_id,
    SUM(total_earned) AS total_earned_sum
  FROM
    '.TABLE_PRODUCER_TOTALS.'
  WHERE
    delivery_id = "'.$delivery_id.'"
  GROUP BY
    delivery_id';
$result_sum4 = @mysql_query($sql_sum4,$connection) or die("Couldn't execute query 4.");
while ( $row = mysql_fetch_array($result_sum4) )
  {
    $total_earned_sum = $row['total_earned_sum'];
  }
$sqlp = '
  SELECT
    '.TABLE_PRODUCER.'.producer_id,
    '.TABLE_PRODUCER.'.member_id,
    '.TABLE_MEMBER.'.member_id,
    '.TABLE_MEMBER.'.business_name,
    '.TABLE_MEMBER.'.first_name,
    '.TABLE_MEMBER.'.last_name,
    '.TABLE_PRODUCER_TOTALS.'.*,
    DATE_FORMAT('.TABLE_PRODUCER_TOTALS.'.last_modified, "%b %d, %Y") AS last_modified
  FROM
    '.TABLE_PRODUCER.',
    '.TABLE_MEMBER.',
    '.TABLE_PRODUCER_TOTALS.'
  WHERE
    '.TABLE_PRODUCER_TOTALS.'.producer_id = '.TABLE_PRODUCER.'.producer_id
    AND '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
    AND '.TABLE_PRODUCER_TOTALS.'.delivery_id = "'.$delivery_id.'"
  GROUP BY
    '.TABLE_PRODUCER_TOTALS.'.producer_id
  ORDER BY
    business_name ASC,
    last_name ASC';
$resultp = @mysql_query($sqlp,$connection) or die("Couldn't execute query.");
while ( $row = mysql_fetch_array($resultp) )
  {
    $producer_id = $row['producer_id'];
    $business_name = stripslashes ($row['business_name']);
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $delivery_id = $row['delivery_id'];
    $prod_total = $row['prod_total'];
    $total_earned = $row['total_earned'];
    $amount_paid = $row['amount_paid'];
    $sent_to_prdcr = $row['sent_to_prdcr'];
    $sent_back = $row['sent_back'];
    $finalized = $row['finalized'];
    $last_modified = $row['last_modified'];
    if ( !$business_name )
      {
        $business_name = $first_name.' '.$last_name;
      }
    if (( $current_business_name < 0 ) && !$business_name )
      {
        $current_business_name = stripslashes ($row['business_name']);
      }
    else
      {
        $current_business_name = $row['last_name'];
      }
    while ( $current_business_name != $business_name )
      {
        $current_business_name = $business_name;
        if ( $sent_to_prdcr == 1 )
          {
            $sent_to_prdcr = 'Y';
          }
        else
          {
            $sent_to_prdcr='<font color="#FFFFFF">.</font>';
          }
        if ( $sent_back == 1 )
          {
            $sent_back = 'Y';
          }
        else
          {
            $sent_back='<font color="#FFFFFF">.</font>';
          }
        if ( $finalized == 1 )
          {
            $finalized = 'Y';
          }
        else
          {
            $finalized='<font color="#FFFFFF">.</font>';
          }
        $discrepancy = 0;
        $discrepancy = $total_earned - $amount_paid;
        $display_month .= '
          <tr>
            <td align="right" valign="top"><font face="arial" size="-1">
              <form action="" method="post"><b>'.$producer_id.'</b>
            </td>
            <td align="left" valign="top"><font face="arial" size="-1"><b>'.$business_name.'</b>&nbsp;&nbsp;</td>
            <td align="right" valign="top"><font face="arial" size="-1">'.$prod_total.'</td>
            <td align="right" valign="top"><font face="arial" size="-1">$'.number_format($total_earned, 2).'</td>
            <td align="right" valign="top"><font face="arial" size="-1">'.$sent_to_prdcr.'</td>
            <td align="right" valign="top"><font face="arial" size="-1">'.$sent_back.'</td>
            <td align="right" valign="top"><font face="arial" size="-1">'.$finalized.'</td>
            <td align="right" valign="top"><font face="arial" size="-1">$<input type="text" name="amount_paid" value="'.$amount_paid.'" size="5" maxlength="7"></td>
            <td align="right" valign="top"><font face="arial" size="-1">$'.number_format($discrepancy, 2).'</td>
            <td>
              <input type="hidden" name="updatevalues" value="ys">
              <input type="hidden" name="producer_id" value="'.$producer_id.'">
              <input type="hidden" name="delivery_id" value="3">
              <input name="where" type="submit" value="Update">
              </form></td>
            <td align="right" valign="top"><font face="arial" size="-2"><i>'.$last_modified.'</i></td>
          </tr>';
      }
  }
$display_totals = '
  <tr>
    <td colspan="2" align="center" bgcolor="#AEDE86"><br><b>T O T A L S</b><br><br></td>
    <td align="right" valign="top"><font face="arial" size="-1"><br>
    <b>'.$total_prod_sum.'</b></td>
    <td align="right" valign="top"><font face="arial" size="-1"><br>
    <b>$'.number_format($total_earned_sum, 2).'</b></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>';
$fontface='arial';
?>
<html>
<body bgcolor="#FFFFFF">
<font face="<?php echo $fontface;?>">
<h2>Monthly Breakdown by Producer: Delivery Date <?php echo $delivery_date;?></h2>
<b>Total Products Sold: <?php echo $num_prod;?> Products</b> &nbsp;&nbsp;&nbsp;<font size="-1">(Print Landscape for best results.)</font>
<br>
<font color="#CC9900"><?php echo $message;?></font>
<table cellpadding="2" cellspacing="0" border="1">
<?php echo $display_totals;?>
  <tr>
    <th valign="bottom" bgcolor="#DDDDDD"><font face="<?php echo $fontface;?>" size="-1">Prd. ID</th>
    <th valign="bottom" bgcolor="#DDDDDD"><font face="<?php echo $fontface;?>" size="-1">Producer Name</th>
    <th valign="bottom" bgcolor="#DDDDDD"><font face="<?php echo $fontface;?>" size="-1"># Prod. Sold</th>
    <th valign="bottom" bgcolor="#DDDDDD"><font face="<?php echo $fontface;?>" size="-1">Total Earned</th>
    <th valign="bottom" bgcolor="#CC9900"><font face="<?php echo $fontface;?>" size="-2">Sent to<br>Prdcr</th>
    <th valign="bottom" bgcolor="#ADB6C6"><font face="<?php echo $fontface;?>" size="-2">Sent back<br>to Co-op</th>
    <th valign="bottom" bgcolor="#AEDE86"><font face="<?php echo $fontface;?>" size="-2">Final</th>
    <th valign="bottom" bgcolor="#DDDDDD"><font face="<?php echo $fontface;?>" size="-1">Amount Co-op<br>Paid Prdcr</th>
    <th valign="bottom" bgcolor="#DDDDDD"><font face="<?php echo $fontface;?>" size="-1">Discrepancy</th>
    <th valign="bottom" bgcolor="#DDDDDD"><font face="<?php echo $fontface;?>" size="-1">Update Info</th>
    <th valign="bottom" bgcolor="#DDDDDD"><font face="<?php echo $fontface;?>" size="-2">Last Modified</th>
  </tr>
<?php echo $display_month;?>
<?php echo $display_totals;?>
</table>
<?php include("template_footer.php");?>