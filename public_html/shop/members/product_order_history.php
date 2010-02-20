<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();

// If not the current producer or the administrator then abort to main page
if($producer_id != $producer_id_you && strpos ($_SESSION['auth_type'], 'administrator') === false)
  {
    header( "Location:index.php");
    exit;
  }

include("template_hdr_orders.php");

$content_body = '';

$query = '
  SELECT
    '.TABLE_BASKET_ALL.'.delivery_id,
    '.TABLE_MEMBER.'.member_id,
    '.TABLE_MEMBER.'.first_name,
    '.TABLE_MEMBER.'.last_name,
    '.TABLE_MEMBER.'.email_address,
    '.TABLE_MEMBER.'.home_phone,
    '.TABLE_BASKET_ALL.'.basket_id,
    '.TABLE_BASKET.'.quantity,
    '.TABLE_BASKET.'.customer_notes_to_producer,
    '.TABLE_BASKET.'.product_id,
    '.TABLE_PRODUCER.'.producer_id,
    '.TABLE_PRODUCT.'.product_name,
    '.TABLE_PRODUCT.'.detailed_notes,
    '.TABLE_DELDATE.'.delivery_date
  FROM
    '.TABLE_BASKET.'
  LEFT JOIN '.TABLE_BASKET_ALL.' ON '.TABLE_BASKET_ALL.'.basket_id = '.TABLE_BASKET.'.basket_id
  LEFT JOIN '.TABLE_MEMBER.' ON '.TABLE_MEMBER.'.member_id = '.TABLE_BASKET_ALL.'.member_id
  LEFT JOIN '.TABLE_PRODUCT.' ON '.TABLE_PRODUCT.'.product_id='.TABLE_BASKET.'.product_id
  LEFT JOIN '.TABLE_PRODUCER.' ON '.TABLE_PRODUCER.'.producer_id='.TABLE_PRODUCT.'.producer_id
  LEFT JOIN '.TABLE_DELDATE.' ON '.TABLE_DELDATE.'.delivery_id = '.TABLE_BASKET_ALL.'.delivery_id
  WHERE
    '.TABLE_BASKET.'.product_id ="'.$_GET['product_id'].'"
    AND '.TABLE_PRODUCER.'.producer_id="'.$_GET['producer_id'].'"
  ORDER BY
    '.TABLE_BASKET_ALL.'.delivery_id';
$sql = @mysql_query($query,$connection) or die("Error: ".mysql_error()."<br>Error No: ".mysql_errno());
while ($row = mysql_fetch_array($sql))
  {
    $delivery_id = $row['delivery_id'];
    $member_id = $row['member_id'];
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $email_address = $row['email_address'];
    $home_phone = $row['home_phone'];
    $quantity = $row['quantity'];
    $notes_to_producer = stripslashes ($row['customer_notes_to_producer']);
    $product_name = stripslashes ($row['product_name']);
    $detailed_notes = stripslashes ($row['detailed_notes']);
    $delivery_date = $row['delivery_date'];

    if ($delivery_date && $delivery_date != $delivery_date_prior)
      {
        $new_section = ' class="new_section"';
      }
    else
      {
        $new_section = '';
      }

    $content_body .= '
      <tr class="proddata">
      <td '.$new_section.'>'.$delivery_date.'</td>
      <td '.$new_section.'>'.$first_name.' '.$last_name.'</td>
      <td '.$new_section.'>'.$email_address.'</td>
      <td '.$new_section.'>'.$home_phone.'</td>
      <td '.$new_section.'>'.$quantity.'</td>
      <td '.$new_section.'>'.$notes_to_producer.'</td>
      </tr>';

    $delivery_date_prior = $delivery_date;
  }

$content_head = '
  <style>
  tr.prodhead {background:#efd}
  tr.prodhead th {text-align:left;font-size:1.1em;font-weight:bolder;border-bottom:1px solid #000;}
  tr.proddata {background:#ffe;color:#000;}
  tr.proddata:hover {background:#ffffcc;color:#008;}
  tr.proddata td.new_section {border-top:1px solid #ccc;}
  </style>
  <table align="center" border="0" cellspacing="0" cellpadding="2" width="90%">
  <tr class="prodhead">
  <td colspan="6"><h3>'.$product_name.'</h3>'.$detailed_notes.'<br><br></td>
  </tr>
  <tr class="prodhead">
  <th>Delivery<br>Date</th>
  <th>Customer Name</th>
  <th>Email Address</th>
  <th>Home Phone</th>
  <th>Qty</th>
  <th>Notes from Customer</th>
  </tr>';

echo $content_head.$content_body.'
  </table>';

include("template_footer_orders.php");
