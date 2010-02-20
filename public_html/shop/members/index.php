<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();
if ( $action == "open" )
  {
    $sqlop = '
      UPDATE
        '.TABLE_CURDEL.'
      SET
        open = "1"';
    $resultop = @mysql_query($sqlop,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
 }
elseif ( $action == "close" )
  {
    $sqlop = '
      UPDATE
        '.TABLE_CURDEL.'
      SET
        open = "0"';
    $resultop = @mysql_query($sqlop,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
  }
$date_today = date("F j, Y");
$sqldd = '
  SELECT
    *
  FROM
    '.TABLE_CURDEL.'';
$rs = @mysql_query($sqldd,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
while ( $row = mysql_fetch_array($rs) )
  {
    $current_delivery_id = $row['delivery_id'];
    $delivery_date = $row['delivery_date'];
    $closing_timestamp = $row['closing_timestamp'];
    $date_open = strtotime ($row['date_open']);
    $order_cycle_closed = $row['order_cycle_closed'];
    //$open = $row['open'];
    $date_closed = strtotime ($row['date_closed']);
  }

// Time_now is used to determine whether the order cycle is in session
// One of the following two should be uncommented for (automatic vs. manual cycling)

$time_now = time ();
// if($open==1){
//  $time_now = 0;
// } else {
//  $time_now = 99999999999999;
// }

session_register("current_delivery_id");
include("../func/convert_delivery_date.php");
$current_delivery_date = $delivery_date;
session_register("current_delivery_date");
session_register("closing_timestamp");
session_register("order_cycle_closed");
session_register("date_open");
session_register("date_closed");

$sqlm = '
  SELECT
    username_m,
    auth_type,
    member_id,
    first_name,
    first_name_2,
    last_name,
   last_name_2,
    business_name,
    pending
  FROM
    '.TABLE_MEMBER.'
 WHERE
    username_m = "'.$valid_m.'"';
$result = @mysql_query($sqlm, $connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
while ( $row = mysql_fetch_array($result) )
  {
    $member_id = $row['member_id'];
    $auth_type = $row['auth_type'];
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $first_name_2 = $row['first_name_2'];
    $last_name_2 = $row['last_name_2'];
    $business_name = stripslashes ($row['business_name']);
    $pending = $row['pending'];
  }

/* ======= Determines authorization levels for this member ======= */
$all_auth_types = explode(',',$auth_type);
$is_institutionion = false;
$is_sysadmin = false;
for($row=0;$row<=count($all_auth_types);$row+=1)
{	
	if($all_auth_types[$row]=='institution') $is_institution = true;
	if($all_auth_types[$row]=='sysadmin') $is_sysadmin = true;
}

session_register("member_id");
include("../func/show_name.php");
session_register("show_name");

$sqlp = '
  SELECT
    member_id,
    producer_id,
    pending as pending_producer
  FROM
    '.TABLE_PRODUCER.'
  WHERE
    member_id = "'.$member_id.'"';
$result = @mysql_query($sqlp, $connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
while ( $row = mysql_fetch_array($result) )
  {
    $pending_producer = $row['pending_producer'];
    $producer_id_you = $row['producer_id'];
    session_register("producer_id_you");
    if ( $result )
      {
        $qhy=ujo;
      }
  }

$sql4 = '
  SELECT
    member_id,
    delivery_id,
    basket_id,
    finalized
  FROM
    '.TABLE_BASKET_ALL.'
  WHERE
    delivery_id = "'.$current_delivery_id.'"
    AND member_id = "'.$member_id.'"';
$result4 = @mysql_query($sql4,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
$num4 = mysql_numrows($result4);
while ( $row = mysql_fetch_array($result4) )
  {
    $basket_id = $row['basket_id'];
    $finalized = $row['finalized'];
  }
if ( $num4 == "1" )
  {
    $order_started = "yes";
    session_register("basket_id");
  }
else
  {
   $order_started = "";
  }


$sql = '
  SELECT
    '.TABLE_PRODUCT.'.product_id,
    '.TABLE_PRODUCT.'.donotlist,
    '.TABLE_PRODUCT.'.producer_id,
   '.TABLE_PRODUCER.'.producer_id,
    '.TABLE_PRODUCER.'.donotlist_producer
  FROM
    '.TABLE_PRODUCT.',
    '.TABLE_PRODUCER.'
  WHERE
    '.TABLE_PRODUCT.'.donotlist = 0
    AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
    AND '.TABLE_PRODUCER.'.donotlist_producer != 1
  GROUP BY
    product_id';
$result = @mysql_query($sql,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
$prod_count = mysql_numrows($result);
?>

<?php include("template_hdr_orders.php");?>
<?php echo $font;?>
<!-- CONTENT BEGINS HERE -->
<div align="center">
<b>Welcome <?php echo $show_name;?>!</b><br><br>

<b>Ordering Closes: <font color="#770000"><?php echo $order_cycle_closed;?></font></b> /
<b>Delivery Date: <font color="#770000"><?php echo $current_delivery_date;?></b></font>
<br>

<?php
if ( ( ! $order_started)
  && ( $time_now > $date_open && $time_now < $date_closed )
  && $pending != 1)
  {
    include("../func/mem_select_delivery.php");
    //include("../func/mem_select_delivery_special.php");
    if ( $show_page == "no" )
      {
      }
    else
      {
        echo $display;
      }
  }
elseif ( $pending == 1 )
  {
    echo '<br/><font color="#770000"><b>Your membership is pending, please contact <a href="mailto:'.MEMBERSHIP_EMAIL.'">'.MEMBERSHIP_EMAIL.'</a> with any questions.</b></font><br/>';
  }
if ( $saved == "yes" )
  {
    $message3 = "<font color=\"#770000\"><b>Thank you - Your order has been saved.<br>
      You can come back and edit it at any time until $order_cycle_closed.</b></font><br><br>";
  }
?>
<br><?php echo $message3;?>
<table width="700" cellpadding="7" cellspacing="2" border="0">

<?php

@include ('message.php');
if ( $time_now > $date_open && $time_now < $date_closed )
  {
    echo '
      <tr>
        <td colspan="3" valign="middle" align="center">
          '.$notification_message.'
        </td>
      </tr>
      <tr>
      <tr>
        <td colspan="3" bgcolor="#AEDE86" valign="bottom" align="left">'.$font.'<b>Shopping Info: Available Products</b></td>
      </tr>
      <tr>
        <td bgcolor="#DDDDDD" valign="top"><b><a href=category_list_full.php>Products Sorted by Category<br>(Full List)</a></b></td>
        <td bgcolor="#DDDDDD" valign="top">'.$font.'<b><a href="prdcr_list.php">Products Sorted by<br>Producer</a></b></td>
        <td bgcolor="#DDDDDD" valign="top">'.$font.'<b><a href="listall.php">Products Sorted by<br>Product ID#</a></b></td>
      </tr>
      <tr>
        <td bgcolor="#DDDDDD" valign="top">'.$font.'<b><a href=category_list_full_new.php>New Products</a></b></td>
        <td bgcolor="#DDDDDD" valign="top">'.$font.'<b><a href="category_list_full_changed.php">Changed Products</a></b></td>
        <td bgcolor="#DDDDDD" valign="top">'.$font.'<b><a href="category_list.php">Products Sorted by<br>Category</a></b></td>
      </tr>
      <tr>
        <td colspan="3"><br></td></tr>';
  }

echo '
  <tr>
    <td colspan="3" bgcolor="#AEDE86" valign="bottom" align="left">'.$font.'<b>Customer Info: Product and Order Info</b></td>
  </tr>
  <tr>
    <td bgcolor="#DDDDDD" valign="top">'.$font;

if ( ( $order_started ) && ( $time_now < $date_closed ) )
  {
    echo '<b><a href="orders_current.php">View Your Cart</a></b><br>
      <a href="orders_current.php?open#prior">Previously Ordered Items</a>';
  }
// elseif ( ($order_started ) && ( $time_now > $date_closed ) )
//   {
//     echo 'Order now closed';
//   }
elseif ( $time_now > $date_closed )
  {
    echo '<b>Order now closed</b>';
  }
elseif ( (! $order_started ) && $time_now < $date_closed )
  {
    echo '<b>No cart is open</b>';
  }
else
  {
    echo '';
  }

echo '</td>
  <td bgcolor="#DDDDDD" valign="top">'.$font;

if ( ( $order_started) && ( ! $finalized ) )
  {
    echo '<b><a href="orders_invoice.php">View In-Process Invoice</a></b>';
  }
elseif ( ( $order_started ) && ( $finalized ) )
  {
    echo '<b><a href="orders_invoice.php">View Final Invoice</a></b>';
  }
elseif ( ( !$order_started ) )
  {
    echo '<b>No invoice</b>';
  }

echo '</td>
    <td bgcolor="#DDDDDD" valign="top"><?php echo $font;?><b><a href="orders_past.php">Past Customer Invoices</a></b></td>
  </tr>';

if ( ( $producer_id_you ) && ( $qhy == ujo ) && $pending_producer == 0 )
  {
    echo '
      <tr>
        <td colspan="3"><br></td>
      </tr>
      <tr>
        <td colspan="3" bgcolor="#ADB6C6" valign="bottom" align="left">'.$font.'<b>Producer Info: Product and Order Info</b></td>
      </tr>
      <tr>
        <td bgcolor="#DDDDDD" valign="top">'.$font.'<b>Delivery Day Labels:</b><br>
          <a href="../func/producer_labelsc.php?delivery_id='.$_SESSION['current_delivery_id'].'&producer_id='.$_SESSION['producer_id_you'].'">One Label per Customer</a><br>
          <a href="../func/producer_labels.php?delivery_id='.$_SESSION['current_delivery_id'].'&producer_id='.$_SESSION['producer_id_you'].'">One Label per Product</a></td>
        <td bgcolor="#DDDDDD" valign="top">'.$font.'<b>Producer Invoices:</b><br>
          <a href="orders_prdcr_cust.php">by Customer</a><br>
          <a href="orders_prdcr_cust_storage.php">by Storage/Customer</a><br>
          <a href="orders_prdcr.php">by Product</a></b><br>
          <a href="orders_prdcr_multi.php">Multi-sort / Mass-update</a><br><br>
          <a href="order_summary.php">Order Summary</a><br><br>
          <b><a href="orders_saved2.php">Past Producer Invoices</a></b></td>
        <td bgcolor="#DDDDDD" valign="top">'.$font.'<b>Edit Your Products:</b><br>
          [<a href="edit_product_list.php?a=unlisted">Unlisted</a>]
          [<a href="edit_product_list.php?a=retail">Listed&nbsp;Retail</a>]
          [<a href="edit_product_list.php?a=wholesale">Listed&nbsp;Wholesale</a>]
          [<a href="edit_product_list.php?a=archived">Archived</a>]<br>
          [<a href="add_products.php?producer_id='.$producer_id_you.'">Add New Product</a>]<br><br>
          <b><a href="edit_producer_info.php">Edit Your Public Info</a></b></td>
      </tr>';
  }
//<b><a href=\"edit_product_list.php\">
//Producer/Product List<br>(Sept I)</a></b>

if ( ( $producer_id_you ) && ( $qhy==ujo ) && ( $fjdkslfj == "jkfldjsf" ) )
  {
    echo '
      <tr>
        <td colspan="3"><br></td>
      </tr>
      <tr>
        <td colspan="3" bgcolor="#ADB6C6" valign="bottom" align="left">'.$font.'<b>Producer Info: Product and Order Info</b></td>
      </tr>
      <tr>
        <td bgcolor="#DDDDDD" valign="top">'.$font.'Labels coming after order</td>
        <td bgcolor="#DDDDDD" valign="top">'.$font.'<b>Sneak Peek:<br><a href="orders_prdcr_cust_prep.php">by Customer</a><br>by Product - coming back soon<!--<a href="orders_prdcr_prep.php">by Product</a>--></b></td>
        <td bgcolor="#DDDDDD" valign="top">'.$font.'<b><a href="edit_product_list.php">Edit Your Products</b><br><a href="edit_producer_info.php">Edit Your Public Info</a></b></td>
      </tr>';
  }
// Check if auth_type = administrator
if ( strpos ($_SESSION['auth_type'], 'administrator') !== false )
  {
    echo '
      <tr>
        <td colspan=\"3\"><br></td>
      </tr>
      <tr bgcolor="#ADB6C6">
        <td colspan="2" valign="bottom" align="left">'.$font.'<b>Admin Producer Info</b></td>
        <td>';


// if($open==1)
//   {
//     echo "<b>Currently Open</b> &nbsp;&nbsp; <b><a href='$PHP_SELF?action=close'>Close Order</a></b>";
//   }
// else
//   {
//     echo "<b><a href='$PHP_SELF?action=open'>Open Order</a></b> &nbsp;&nbsp; <b>Currently Closed</b>";
//   }

    echo '
        </td>
      </tr>
      <tr>
        <td bgcolor="#DDDDDD" valign="top">'.$font.'<b><a href="generate_invoices.php">Customer and Producer Invoices</a></font></td>
        <td bgcolor="#DDDDDD" valign="top"></td>
        <td bgcolor="#DDDDDD" valign="top">'.$font.'<b><a href="edit_info_list.php">Edit Producer Info</a></b><br><br><b><a href="totalsbylocation.php">Totals by Location</a></b><br><b><a href="foodstamps_bylocation.php">Food Types by Location</a></b></td>
      </tr>
      <tr>
        <td bgcolor="#DDDDDD" valign="top">'.$font.'<b><a href="foodstamps.php?fs=3">Food Stamp Designations</a><br><a href="foodstamps_updatepast.php">Staple/Retail/Nonfood Totals</a></b></td>
        <td bgcolor="#DDDDDD" valign="top">'.$font.'<b><a href="list_prodnew.php">New Products ('.date('F').')</a><br><a href="list_prodchanged.php">Changed Products ('.date('F').')</a></b></td>
        <td bgcolor="#DDDDDD" valign="top">'.$font.'<b><a href="edit_prdcr_list.php">Producer/Product List<br>('.date('F').')</a></b></td>
      </tr>';
  }
?>

<tr>
  <td colspan="3"><br></td>
</tr>
<?php 

/* ================== DISPLAY INFORMATION FOR WHOLESALE MEMBERS ============== */
if ($is_institution || $is_sysadmin) {
	echo '
	  <tr>
    <td colspan="3" bgcolor="#AEDE86" valign="bottom" align="left">'.$font.'<b>Institutional Member Info</b></td>
  </tr>
  <tr>
    <td bgcolor="#DDDDDD" valign="top">'.$font.'<b><a href="listall_wholesale.php">Wholesale Product List</a></b></td>
    <tr>
  <td colspan="3"><br></td>
</tr>';
	
}
?>
<tr>
  <td colspan="3" bgcolor="#AEDE86" valign="bottom" align="left"><?php echo $font;?><b>Contact Information</b></td>
</tr>
<tr>
  <td bgcolor="#DDDDDD">
    <?php echo $font;?><b><a href="contact.php">How to Contact Us<br>with Questions</a></b>
  </td>
  <td bgcolor="#DDDDDD">
    <?php echo $font;?><b><a href="member_form.php">Update Your<br>Contact Info</a></b><br />
    <strong><a href="reset_password.php">Change Password</a></strong>
  </td>
  <td bgcolor="#DDDDDD">
    <?php echo $font;?><b><a href="faq.php">How to Order FAQ</a><br>
    <?php echo $font;?><b><a href="producer_form.php">Producer applicaton form</a><br>
    <a href='<?php echo BASE_URL.PATH;?>' target='_blank'>Other Info</a></b>
  </td>
</tr>
<tr>
  <td colspan="3" bgcolor="#DDDDDD" valign="bottom" align="center">
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="paypal">
      <input type="hidden" name="cmd" value="_xclick">
      <input type="hidden" name="business" value="<?php echo PAYPAL_EMAIL;?>">
      <input type="hidden" name="item_name" value="Food Coop: <? echo "$first_name $last_name"; if ($first_name_2 || $last_name_2) echo " &amp; $first_name_2 $last_name_2" ?> (#<?= $member_id ?>) Delivery Date: <?= $delivery_date ?>">
      <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="Make payment with PayPal">
    </form>
  </td>
</tr>
</table>
</div>

<br><b>Questions?:</b> <a href='mailto:<?php echo HELP_EMAIL;?>'><?php echo HELP_EMAIL;?></a>
<br><br>
<table border=2 cellpadding=5 cellspacing=0 bgcolor=#EEEEEE>
<tr>
<td>

<b>Acting as the agent of producer members</b>, the <?php echo SITE_NAME; ?> posts and publicizes the products the producers have for sale, receive orders, provides a way for products to be delivered to other members of the <?php echo ORGANIZATION_TYPE; ?>, collects from the customers and forwards the payments to the producers.  <b>Acting as the agent for customer members</b>, we provide them a catalog of available local food products that includes information about how and where the product was grown or processed. We receive their orders and notify the appropriate producers, arrange for the food to be delivered, receive and process their payments.  For both producer and customer members, we provide a basic screening of products and producers based on our published parameters, and education and training regarding the use and the advantages of local foods.

<br><br>

For some of our producer members, we are agents that facilitate farm gate sales of their products.  For other producer members, we facilitate off-farm sales or sales of processed products.

<br><br>

<b>The essential business of the <? echo ORGANIZATION_TYPE; ?> is to provide a marketplace where willing buyers and sellers can meet.</b>  At no time does the <? echo ORGANIZATION_TYPE; ?> ever have title to any of the products.  We have no inventory.  The products that go through our distribution system are owned either by the producer, or by the customer who purchases "title" to the product from the producer.  All complaints should first be brought to the attention of the producer, unless it is a situation where the <? echo ORGANIZATION_TYPE; ?> itself is at fault (such as broken eggs due to poor packing). If a successful resolution can not be found by the affected producer and customer members, the <? echo ORGANIZATION_TYPE; ?>&#146;s arbitration procedure can be invoked.
</td>
</tr>
</table>

<!-- CONTENT ENDS HERE -->

<?php include("template_footer_orders.php");?>
