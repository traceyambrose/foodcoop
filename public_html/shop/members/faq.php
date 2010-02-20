<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();

include("../func/show_name.php");
include("../func/mem_contact_info_single.php");
include("template_hdr_orders.php");
?>

<div align="center">
  <h3>Frequently Asked Questions</h3>
  <table width="80%">
    <tr>
      <td align="left"><?php echo $font;?>

        <b>Click on the question to see an answer:</b>
        <ul>
          <li> <a href="#order1">How do I order ONLINE with this shopping cart?</a>
          <li> <a href="#order2">How do I order NOT using this shopping cart?</a>
          <li> <a href="#order3">Can I change my order?</a>
          <li> <a href="#order4">When does ordering end for this month?</a>
          <br><br>
          <li> <a href="#pay1">How do I pay?</a>
          <li> <a href="#pay2">How do I change my payment method?</a>
          <br><br>
          <li> <a href="#del1">When can I pick up or have my items delivered?</a>
          <li> <a href="#del2">Can I change my delivery method once I have chosen it?</a>
          <br><br>
          <li> <a href="#prdcr1">I am a producer, where do I send my product updates?</a>
          <br><br>
          <li> <a href="#web1">I am getting an error on a page, what do I do?</a>
          <li> <a href="#web2">I have a suggestion on how to make this website easier to use.</a>
          <br><br>
          <li> <a href="edit_contact.php">How do I update my contact information?</a>
          <br><br>
          <li> <a href="#q1">What if I have questions that are not covered in this list?</a>
        </ul>

        <a name="order1">
        <br>
        <b>Q: How do I order online with this shopping cart?</b>
        <br>
        <b>A:</b> The member log-in page is <?php echo '<a href="'.BASE_URL.PATH.'members/">'.BASE_URL.PATH.'members/</a>' ?> . When you log in, if you have not started an order, it will ask you to choose a delivery method (home or work delivery, or pick up) and a payment method. Then click the button to start your order. There are two methods of selecting products you want to buy.
        <ol>
        <LI>You can browse through the product lists and click "Add to Shopping Cart". When you do this, the system adds one of the items you have selected to your cart. If you want to buy several packages of an individual product, click on the "View Your Cart" button. Place the cursor in the Quantity box for the item you wish to order, and change the number to however many you plan to buy. Then click on the "Update" button to the right of that product entry. If you need to add notes to the producer, such as "red, medium sized tomatoes" or "make this a small pig", click on View Your Cart, place the cursor in the box for notes for that product, type in the notes, and then press the UPDATE button to the right of that product entry. When you are done, there is no need to submit your order - whatever remains in your basket when the order closes will be considered your order. <br>
        <br>

        <LI>You can browse the product lists or printed catalog and make a note of the product ID numbers of the items you want to buy. To enter those items, click 'View Your Cart' and you will find a box at the top of the page where you can add items, one at a time, by entering the product ID and how much or how many items you want to buy and any relevant notes. Then hit the "add this product to the order" button. <br>
        <br>

        <LI>To remove a product from your shopping cart, change its Quantity to ZERO and press the UPDATE button. <br>
        <br>

        <LI>You can edit your order up until the time that the Order Desk closes at the end of Delivery Day. The time of closing is announced at the beginning of Order Week. To edit your order (add or subtract items, change quantities, add notes), log in at <A href="members/"><?php echo BASE_URL.PATH;?>/members/</A> . If you want to add items, you can use methods (1) or (2) above to add items, to edit quantities, or remove items, click on "View Your Cart" and make your changes. There is no need to submit your order - whatever remains in your basket when the order closes will be considered your order.
        </ol><br>
        Note: the shopping cart will show a subtotal, but it will not necessarily subtotal everything you have ordered, as items with random weights (such as packages of meat or cheese) will not be totaled until that information is updated from the producers.


        <a name="order2">
        <br><br><br>

        <b>Q: How do I order NOT using this shopping cart?</b>
        <br>
        <b>A:</b> For ordering NOT using this online shopping cart,
        click here for instructions on <a href="../howtoorder.php" target="_blank">How to Order Offline</a>

        <a name="order3">
        <br><br><br>

        <b>Q: Can I change my order?</b>
        <br>
        <b>A:</b> You can log in and change your order until <?php echo $order_cycle_closed;?>. Between then and the delivery day, we will be entering weights on any items that need it and putting your order together. You can view your temporary invoice in progress during that time by logging in.

        <a name="order4">
        <br><br><br>

        <b>Q: When does ordering end for this month?</b>
        <br>
        <b>A:</b> You can log in and change your order until <?php echo $order_cycle_closed;?>.

        <a name="pay1">
        <br><br><br>

        <b>Q: How do I pay?</b>
        <br>
        <b>A:</b> You will receive a paper copy of your invoice with your order on delivery day with the final total owed.
        Then you can write a check and send it to the address on the invoice, or log in and pay by PayPal online.
        You will also be able to view your finalized invoice online after delivery day. The mailing address is on the invoice. If paying by PayPal, email payment to <a href="mailto:<?php echo PAYPAL_EMAIL;?>"><?php echo PAYPAL_EMAIL;?></a>

        <a name="pay2">
        <br><br><br>

        <b>Q: How do I change my payment method?</b>
        <br>
        <b>A:</b> To change how you will pay, once your invoice is finalized after delivery day, you will be shown totals for different methods of payment. You can then decide to write a check, or log on and pay by PayPal online. You will also be able to change your method of payment at that time. If you have questions about this at that time, contact us at <a href="mailto:<?php echo HELP_EMAIL;?>"><?php echo HELP_EMAIL;?></a>

        <a name="del1">
        <br><br><br>

        <b>Q: When can I pick up or have my items delivered?</b>
        <br>
        <b>A:</b> Delivery Day is <?php echo $current_delivery_date;?>. Your temporary invoice (viewable after ordering is closed) will have the information on pick up location or delivery. If you chose delivery, a route manager will be in touch to coordinate delivery with you.

        <a name="del2">
        <br><br><br>

        <b>Q: Can I change my delivery method once I have chosen it?</b>
        <br>
        <b>A:</b> Contact us at <a href="mailto:<?php echo ORDER_EMAIL;?>"><?php echo ORDER_EMAIL;?></a> to change your delivery method.

        <a name="prdcr1">
        <br><br><br>

        <b>Q: I am a producer, where do I send my product updates?</b>
        <br>
        <b>A:</b> Send them to <a href="mailto:<?php echo PRICELIST_EMAIL;?>"><?php echo PRICELIST_EMAIL;?></a>.

        <a name="web1">
        <br><br><br>

        <b>Q: I am getting an error on a page, what do I do?</b>
        <br>
        <b>A:</b> Please copy and paste the text of the error into an email along with what page it is and send it to <a href="mailto:<?php echo WEBMASTER_EMAIL;?>"><?php echo WEBMASTER_EMAIL;?></a>. Please also explain what happened before that error occurred. Thank you for your help in keeping this website working smoothly.

        <a name="web2">
        <br><br><br>

        <b>Q: I have a suggestion on how to make this website easier to use.</b>
        <br>
        <b>A:</b> Please send your suggestions to <a href="mailto:<?php echo WEBMASTER_EMAIL;?>"><?php echo WEBMASTER_EMAIL;?></a>. Thank you for your help in keeping this website working smoothly.

        <a name="q1">
        <br><br><br>

        <b>Q: What if I have questions that are not covered in this list?</b>
        <br>
        <b>A:</b> You can contact the appropriate person by looking at this <a href="contact.php">Contact List</a>.
        <br><br>

      </td>
    </tr>
  </table>
</div>

<?php include("template_footer_orders.php");?>