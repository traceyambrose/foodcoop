<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
include_once ('general_functions.php');
session_start();
validate_user();

// This is the script for editing the shopping basket

$time_now = time ();
if ( $time_now < $date_open || $time_now > $date_closed )
  {
    header( "Location: index.php");
    exit;
  }

$fontface="arial";
include("../func/add_prod.php");
include("../func/mem_edit_invoice.php");
?>

<?php include("template_hdr_orders.php");?>

<div align="center">
<table cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" border="0" width="695">
  <tr>
    <td bgcolor="#FFFFFF" colspan="2" align="left">
      <font color="#770000"><?php echo $message;?></font>
    </td>
  </tr>
  <tr>
    <th valign="bottom" align="left" bgcolor="#AEDE86" width="60%"><font face="<?php echo $fontface;?>">
      <form name="order" action="orders_current.php" method="post">
      <b>Entering Orders: Customer Basket # <?php echo $basket_id;?></b></th>
    <th></th>
    <th valign="bottom" align="right" bgcolor="#AEDE86" width="40%"><font face="<?php echo $fontface;?>">
      [ <a href="index.php">Product Lists</a> | <a href="orders_invoice.php">View Invoice</a> | <a href="logout.php">Logout</a> ]</th>
  </tr>
  <tr>
    <td valign="top" bgcolor="#DDDDDD">

      <table cellspacing="0" cellpadding="0" border="0">
        <tr align="center">
          <td align="right"><?php echo$font;?>
            # <input type="text" name="product_id" size=5 maxlength="6">&nbsp;&nbsp;</td>
              <td align="left"><?php echo$font;?>
            <b>Product ID</td>
          <td align="center"><?php echo$font;?>
            <input type="text" name="quantity" value="1" size=3 maxlength="4"> <b>Quantity</b></td>
        </tr>
        <tr bgcolor="#DDDDDD">
          <td colspan="3" align="left" valign="top">
            <table cellpadding=2 cellspacing=0 border=0>
              <tr>
                <td valign="top" align="right"><?php echo$font;?>
                  <b>Notes to Producer about this product:</b>
                </td>
                <td align="left"><?php echo$font;?>
                  <textarea name="customer_notes_to_producer" cols="32" rows="2"></textarea>
                  <input type="hidden" name="yp" value="ds">
                  <input name="where" type="submit" value="Add this Product to the Order">
                  </form>
                  <script language=javascript>
                    document.order.product_id.focus();
                  </script>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
    </td>
    <td> </td>
    <td align="center" bgcolor="#EFEFEF" valign="top"><span   style="font-family:Arial;font-size:10pt;">
      <br>
      <b>Current SubTotal = $<?php echo "".number_format($total, 2)."";?></b>*
      <br><a href="orders_invoice.php"><b>Click to View Invoice</b></a>
    </td>
<?php if ($qty_in_basket > 0) { ?>
  <tr>
    <td bgcolor="#ffeedd" colspan="3" align="left" style="padding:1em 2em 1em; border:1px solid #f00;color:#f00;">
      <h2 style="text-align:center;">You have an ACTIVE order</h2>If you do not want to order the items below, you must manually remove them from your shopping cart.  Do this by entering a quantity of zero &quot;0&quot; and clicking &quot;Update&quot; for each item listed.
      <br><br>Orders may be changed until <?echo $order_cycle_closed;?>.  At that time, anything in your shopping cart will be considered an order.
      <br><br>When finished shopping, you can just leave the website.  There is no additional &quot;checkout&quot; process.  Please do not forget to pick up your order.
    </td>
  </tr>
<?php } ?>
  <tr>
    <td colspan="3">
      <?php echo $font; ?>
      <?php echo $display_page; ?>
      <br>
      <?php echo $message3; ?>
    </td>
  </tr>
    <td colspan="3">
      <?php include ("../func/prior_cart_list.php"); ?>
    </td>
  <tr>
    <td colspan="3" valign="top" align="left"><?php echo$font;?>
      * Subtotal does not include fees or items needing weights to calculate price.
    </td>
  </tr>
</table>

<?php include("template_footer_orders.php");?>