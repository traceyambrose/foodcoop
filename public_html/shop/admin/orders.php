<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
include_once ('general_functions.php');
session_start();
validate_user();

// Need to set this so that admins can add "wholesale" products, if desired
if (strpos ($_SESSION['auth_type'], 'administrator') !== false)
  {
    $_SESSION['auth_type'] .= ',institution';
  }

$fontface="arial";
include("../func/add_prod.php");
if ( $sub == "mit" )
  {
    $sqlo = '
      UPDATE
        '.TABLE_BASKET_ALL.'
      SET
        subtotal = "'.$subtotal.'",
        coopfee = "'.$coopfee.'",
        member_submitted = "1",
        order_date = now()
      WHERE
        basket_id = "'.$basket_id.'"
        AND member_id = "'.$member_id.'"';
    $resulto = @mysql_query($sqlo,$connection) or die(mysql_error());
    header( "Location: index.php?saved=yes");
    exit;
  }
include("../func/mem_edit_invoice_admin.php");
include("template_hdr.php");?>
<div align="center">
<table cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" border="0" width="695">
  <tr>
    <td bgcolor="#FFFFFF" colspan="2" align="left">
      <font color="#770000"><?php echo $message;?></font>
    </td>
  </tr>
  <tr>
    <th valign="bottom" align="left" bgcolor="#AEDE86"><font face="<?php echo $fontface;?>">
      <form name="order" action="orders.php" method="post">
      <b>Entering Orders: Basket # <?php echo $basket_id;?>, Member # <?php echo $member_id;?></b>
    </th>
    <th></th>
    <th valign="bottom" align="right" bgcolor="#AEDE86"><font face="<?php echo $fontface;?>">
      [ <a href="orders_list.php?delivery_id=<?php echo$delivery_id;?>#<?php echo$basket_id;?>">Edit Other Orders</a>
      | <a href="logout.php">Logout</a> ]
    </th>
  </tr>
  <tr>
    <td valign="top" bgcolor="#DDDDDD">
      <table cellspacing="0" cellpadding="0" border="0">
        <tr align="center">
          <td align="right"><?php echo$font;?># <input type="text" name="product_id" size=5 maxlength="6">&nbsp;&nbsp;</td>
          <td align="left"><?php echo$font;?><b>Product ID</td>
          <td align="center"><?php echo$font;?><input type="text" name="quantity" value="1" size=3 maxlength="4"> <b>Quantity</b></td>
        </tr>
        <tr bgcolor="#DDDDDD">
          <td colspan="3" align="left" valign="top">
            <table cellpadding=2 cellspacing=0 border=0>
              <tr>
                <td valign="top" align="right"><?php echo$font;?><b>Notes to Producer about this product:</b></td>
                <td align="left">
                  <?php echo$font;?>
                  <textarea name="customer_notes_to_producer" cols="32" rows="2"></textarea>
                  <input type="hidden" name="yp" value="ds">
                  <input type="hidden" name="delivery_id" value="<?php echo $delivery_id;?>">
                  <input type="hidden" name="member_id" value="<?php echo $member_id;?>">
                  <input type="hidden" name="basket_id" value="<?php echo $basket_id;?>">
                  <input name="where" type="submit" value="Add this Product to the Order">
                  </form>
                  <script language=javascript> document.order.product_id.focus(); </script>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
    <td> </td>
    <td align="center" bgcolor="#ADB6C6" valign="top">
      <?php echo$font;?><br>
      <b>Current SubTotal = $<?php echo "".number_format($total, 2)."";?></b>*<br>
      <a href="customer_invoice.php?delivery_id=<?php echo$delivery_id; ?>&basket_id=<?php echo$basket_id; ?>&member_id=<?php echo$member_id; ?>">View Invoice</a>
    </td>
  </tr>
  <tr>
    <td colspan="3"><?php echo$font;?><?php echo $display_page;?><br>
      <?php echo $message3;?>
  <tr>
    <td colspan="2" valign="top" align="left">
      <?php echo$font;?>
      [ To remove an item from your shopping cart,<br>
      &nbsp;&nbsp;enter the number 0 as the quantity. ]
      <br><br>
      * Subtotal doesn&#146;t include fees or items needing weights to calculate price.
    </td>
    <td bgcolor="#ADB6C6" valign="top" align="center">
      <?php echo$font;?>
      <br>
      <b>Current SubTotal = $<?php echo "".number_format($total, 2)."";?></b>*<br>
      <a href="customer_invoice.php?delivery_id=<?php echo$delivery_id; ?>&basket_id=<?php echo$basket_id; ?>&member_id=<?php echo$member_id; ?>">View Invoice</a>
      ____________________________
    </td>
  </tr>
</table>
<div align="center">
  [ <a href="orders_list.php?delivery_id=<?php echo$delivery_id;?>#<?php echo$basket_id;?>">Edit Other Member Orders</a> ]
</div>
<?php include("template_footer.php");?>
