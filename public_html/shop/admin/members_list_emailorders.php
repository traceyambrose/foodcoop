<?php
$user_type = 'valid_c';
include_once ('config_foodcoop.php');
session_start();
validate_user();

$delivery_id = $_GET['delivery_id'];
if (!$delivery_id) $delivery_id = $_SESSION['current_delivery_id'];

$sql = '
  SELECT
    '.TABLE_MEMBER.'.*,
    COUNT('.TABLE_BASKET.'.product_id) AS prod_qty
  FROM
    '.TABLE_MEMBER.'
  LEFT JOIN '.TABLE_BASKET_ALL.' ON '.TABLE_MEMBER.'.member_id = '.TABLE_BASKET_ALL.'.member_id
  LEFT JOIN '.TABLE_BASKET.' ON '.TABLE_BASKET.'.basket_id = '.TABLE_BASKET_ALL.'.basket_id
  WHERE
    '.TABLE_BASKET_ALL.'.member_id = '.TABLE_MEMBER.'.member_id
    AND '.TABLE_BASKET_ALL.'.delivery_id = "'.$delivery_id.'"
  GROUP BY
    '.TABLE_BASKET_ALL.'.member_id
  ORDER BY
    last_name ASC,
    first_name ASC';

$rs = @mysql_query($sql,$connection) or die("Couldn't execute query.");
$num = mysql_numrows($rs);
$mail_count = 0;
while ($row = mysql_fetch_array($rs))
  {
    $member_id = $row['member_id'];
    $first_name = stripslashes ($row['first_name']);
    $last_name = stripslashes ($row['last_name']);
    $first_name_2 = stripslashes ($row['first_name_2']);
    $last_name_2 = stripslashes ($row['last_name_2']);
    $business_name = stripslashes ($row['business_name']);
    $email_address = $row['email_address'];
    $email_address_2 = $row['email_address_2'];
    $prod_qty = $row['prod_qty'];

    if(!$last_name)
      {
        $show_mem = $business_name;
      }
    else
      {
        $show_mem = "$first_name $last_name";
      }

    if(!$last_name_2)
      {
        $show_mem_2 = $business_name;
      }
    else
      {
        $show_mem_2 = "$first_name_2 $last_name_2";
      }

    // Only show if there is an email address and the qty of items ordered is more than zero
    if ($email_address && $prod_qty)
      {
        $fancy_display .= '<a href="mailto:'.$email_address.'">'.$show_mem.' &lt;'.$email_address.'&gt;</a><br />';
        $plain_display .= '<span>'.$email_address.'</span><br />';
        $mail_count ++;
      }
//    if ($email_address_2) { $display .= "&nbsp; <a href=\"mailto:$email_address_2\">$show_mem_2 &lt;$email_address_2&gt;</a><br>"; }

  }

?>

<?include("template_hdr.php");?>


  <!-- CONTENT BEGINS HERE -->

<div align="center">
<table width="80%">
  <tr>
    <td align="left">
      <div align="center">
        <h3>Member Ordering for Delivery #<?php echo $delivery_id; ?>: <?echo $mail_count;?> Members</h3>
      </div>
<?php if ($delivery_id > 1) { ?>
      <div style="float:left;border:1px solid #440; background-color:#ffd;padding:3px 20px;">
        <a href="<?php echo $_SERVER['PHP_SELF']."?delivery_id=".($delivery_id - 1) ?>">Get list for prior order</a>
      </div>
<?php };
if ($delivery_id < $_SESSION['current_delivery_id']) { ?>
      <div style="float:right;border:1px solid #440; background-color:#ffd;padding:3px 20px;">
        <a href="<?php echo $_SERVER['PHP_SELF']."?delivery_id=".($delivery_id + 1) ?>">Get list for next order</a>
      </div>
<?php } ?>
      <br /><br />
      <input type="radio" name="display_type" onClick='{document.getElementById("fancy").style.display="none";document.getElementById("plain").style.display="";}'>Show plain addresses
      <br />
      <input type="radio" name="display_type" onClick='{document.getElementById("plain").style.display="none";document.getElementById("fancy").style.display="";}'>Show fancy addresses
      <br /><br />
    <div id="fancy"><?echo $fancy_display;?></div>
    <div id="plain" style="display:none"><?echo $plain_display;?></div>



  </td></tr>
</table>
</div>
  <!-- CONTENT ENDS HERE -->

<?include("template_footer.php");?>
 