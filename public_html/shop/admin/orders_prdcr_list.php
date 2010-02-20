<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

$i=0;
$sqlp = '
  SELECT
    '.TABLE_PRODUCER.'.producer_id,
    '.TABLE_MEMBER.'.business_name,
    '.TABLE_MEMBER.'.first_name,
    '.TABLE_MEMBER.'.last_name,
    '.TABLE_MEMBER.'.address_line1,
    '.TABLE_MEMBER.'.city,
    '.TABLE_MEMBER.'.state,
    '.TABLE_MEMBER.'.zip,
    '.TABLE_MEMBER.'.county,
    '.TABLE_MEMBER.'.home_phone,
    '.TABLE_MEMBER.'.work_phone,
    '.TABLE_MEMBER.'.mobile_phone,
    '.TABLE_MEMBER.'.email_address
  FROM
    '.TABLE_PRODUCER.',
    '.TABLE_MEMBER.',
    '.TABLE_BASKET.',
    '.TABLE_PRODUCT.',
    '.TABLE_BASKET_ALL.'
    WHERE
    '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id
    AND '.TABLE_BASKET.'.basket_id = '.TABLE_BASKET_ALL.'.basket_id
    AND '.TABLE_BASKET_ALL.'.delivery_id = "'.$delivery_id.'"
    AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
    AND '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
  GROUP BY '.TABLE_PRODUCER.'.producer_id
  ORDER BY
    business_name ASC,
    last_name ASC';
$resultp = @mysql_query($sqlp,$connection) or die("Couldn't execute query.");
while ( $row = mysql_fetch_array($resultp) )
  {
    $business_name = '';
    $phone_number = '';
    $county = '';
    if ( $row['business_name'] )
      {
        $business_name = $row['business_name'];
      }
    else
      {
        $business_name = $row['first_name'].' '.$row['last_name'];
      }
    if ($row['home_phone'])
      {
        $phone_number = $row['home_phone'];
      }
    elseif ($row['work_phone'])
      {
        $phone_number = $row['work_phone'];
      }
    elseif ($row['mobile_phone'])
      {
        $phone_number = $row['mobile_phone'];
      }
    if ($row['county'] != '')
      {
        $county = strtoupper ($row['county'].' Co.');
      }
    $display .= '
        <tr>
          <td style="border-bottom:1px solid gray;" valign="top">
            <b>'.stripslashes($business_name).'</b><br>
            <font style="font-size:75%;">'.$row['address_line1'].'<br>
            '.$row['city'].', '.$row['state'].' '.$row['zip'].'<br>
            '.$county.'<br>
            <strong>'.$phone_number.'</strong> &ndash; <a href="mailto:'.$business_name.' &lt;'.$row['email_address'].'&gt;">'.$row['email_address'].'</a></font>
          </td>
          <td style="border-bottom:1px solid gray;font-size:70%;">
            <a href="orders_prdcr.php?delivery_id='.$delivery_id.'&producer_id='.$row['producer_id'].'">Product</a><br/>
            <a href="orders_prdcr_cust.php?delivery_id='.$delivery_id.'&producer_id='.$row['producer_id'].'">Customer</a><br/>
            <a href="orders_prdcr_cust_storage.php?delivery_id='.$delivery_id.'&producer_id='.$row['producer_id'].'">Customer storage</a><br/>
            <a href="orders_prdcr_multi.php?delivery_id='.$delivery_id.'&producer_id='.$row['producer_id'].'">Multi</a><br>
            <a href="orders_prdcr_cust_storage.php?delivery_id='.$delivery_id.'&producer_id='.$row['producer_id'].'&display_only=true&output=pdf" target="_blank">PDF invoice</a>
          </td>
          <td style="border-bottom:1px solid gray;font-size:70%;">';
    $sql = '
      SELECT '.TABLE_BASKET_ALL.'.basket_id
      FROM
        '.TABLE_BASKET.'
      LEFT JOIN '.TABLE_BASKET_ALL.' ON '.TABLE_BASKET.'.basket_id = '.TABLE_BASKET_ALL.'.basket_id
      LEFT JOIN '.TABLE_PRODUCT.' ON '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id
      WHERE
        '.TABLE_BASKET_ALL.'.delivery_id = "'.$delivery_id.'"
        AND '.TABLE_PRODUCT.'.producer_id = "'.$row["producer_id"].'"
        AND '.TABLE_BASKET.'.out_of_stock != "1"
        AND '.TABLE_PRODUCT.'.random_weight = "1"
        AND '.TABLE_BASKET.'.total_weight <= "0"';
    $rs = @mysql_query($sql,$connection) or die("Couldn't execute query.");
    $unfilled_random_weights = 0;
    while ( $row = mysql_fetch_array($rs) )
      {
        $unfilled_random_weights++;
      }
    if ($unfilled_random_weights) $display .= '
          [waiting on '.$unfilled_random_weights.' weights]';
    $display .= '
          </td>
        </tr>';
    $i++;
  }
include("template_hdr.php");?>
<!-- CONTENT BEGINS HERE -->
<div align="center">
<table width="70%">
  <tr>
    <td align="left">
<?php include("../func/show_delivery_date.php");?>
<?php include("../func/convert_delivery_date.php");?>
      <div align="center">
        <h3>Producer List for <?php echo $delivery_date;?></h3>
      </div>
      <table cellpadding="4" cellspacing="0" border="0" style="border:1px solid gray;">
        <tr bgcolor="#AEDE86">
          <td align="center" valign="bottom" style="border-bottom:1px solid gray;"><b>Business Name</b></td>
          <td align="center" valign="bottom" style="border-bottom:1px solid gray;"><b>View Invoice</b></td>
          <td align="center" valign="bottom" style="border-bottom:1px solid gray;"><b>Status</b></td>
        </tr>
        <?php echo $display;?>
      </table>
    </td>
  </tr>
</table>
<br><br>
</div>
<!-- CONTENT ENDS HERE -->
<br><br>
<?php
include("template_footer.php");?>