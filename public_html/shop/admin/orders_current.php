<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
include_once ('general_functions.php');
session_start();
validate_user();

$fontface="arial";
include("../func/add_prod.php");
if ( $sub == 'mit' )
  {
    $sqlo = '
      UPDATE
        '.TABLE_BASKET_ALL.'
      SET
        subtotal = '.$subtotal.'",
        coopfee = '.$coopfee.'",
        member_submitted = "1",
        order_date = now()
      WHERE
        basket_id = '.$basket_id.'"
        AND member_id = '.$member_id.'"';
    $resulto = @mysql_query($sqlo,$connection) or die(mysql_error());
    header( "Location: index.php?saved=yes");
    exit;
  }
else
  {
//    include("../func/mem_edit_invoice.php");
  }
include("template_hdr_orders.php");?>
<div align="center">
<table cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" border="0" width="695">
  <tr>
    <td bgcolor="#FFFFFF" colspan="2" align="left"><font color="#770000"><?php echo $message;?></font></td>
  </tr>
  <tr>
    <th valign="bottom" align="left" bgcolor="#AEDE86">
      <font face="<?php echo $fontface;?>">
      <form name="order" action="orders_current.php" method="post">
      <b>Entering Orders: Customer Basket # <?php echo $basket_id;?></b>
    </th>
    <th>
    </th>
    <th valign="bottom" align="right" bgcolor="#AEDE86">
      <font face="<?php echo $fontface;?>">
      [ <a href="index.php">Go to Product Lists</a> | <a href="logout.php">Logout</a> ]
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
                <td align="left"><?php echo$font;?>
                  <textarea name="customer_notes_to_producer" cols="32" rows="2"></textarea>
                  <input type="hidden" name="yp" value="ds">
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
    <td></td>
    <td align="center" bgcolor="#ADB6C6" valign="top"><?php echo$font;?><br>
      <b>Current SubTotal = $<?php echo "".number_format($total, 2)."";?></b>*<br>
      <form action="" method="post">
        <input type="hidden" name="sub" value="mit">
        <input type="hidden" name="subtotal" value="<?php echo $total;?>">
        <input name="where" type="submit" value="Click to Save Your Order"><br>
        <font size="-2">You can change your order until<br><?php echo $order_cycle_closed;?>.</font>
      </form>
    </td>
  </tr>
  <tr>
    <td colspan="3">
      <?php echo$font;?>
      <?php echo $display_page;?>
      <br>
      <?php echo $message3;?>
  <tr>
    <td colspan="2" valign="top" align="left"><?php echo$font;?>
      [ To remove an item from your shopping cart,<br>
      &nbsp;&nbsp;enter the number 0 as the quantity. ]<br><br>
      * Subtotal doesn&#146;t include fees or items needing weights to calculate price.
    </td>
    <td bgcolor="#ADB6C6" valign="top" align="center">
      <?php echo$font;?><br>
      <b>Current SubTotal = $<?php echo "".number_format($total, 2)."";?></b>*<br>
      <form action="" method="post">
        <input type="hidden" name="sub" value="mit">
        <input type="hidden" name="subtotal" value="<?php echo $total;?>">
        <input name="where" type="submit" value="Click to Save Your Order"><br>
        <font size="-2">You can change your order until<br><?php echo $order_cycle_closed;?>.</font>
      </form>
    </td>
  </tr>
</table>
<?php include("template_footer_orders.php");?>