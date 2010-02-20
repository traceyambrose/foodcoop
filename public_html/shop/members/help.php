<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();
?>

<html>
<head>
<title>Editing Product Information</title>
<style>
strong
  {
  font-size:1.4em;
  }
</style>
</head>
<body bgcolor="#FFFFFF">
<font face="arial" size="-1">

<!-- CONTENT BEGINS HERE -->

<a name="availability">
<h3>Editing Product Information</h3>

<a name="pid">
<br><br>
<strong>Product #</strong><br>
The Product # is assigned arbitrarily at the time the product is added to the database. This ID number does not change.

<strong>Availability</strong><br>
If marked Yes, the product will be listed as available for purchasing in the upcoming ordering cycle. If marked No, it
will be hidden from public view, but can be marked yes later for a future ordering cycle.

<a name="foodstamps">
<br><br>
<strong>Food Stamp Retail and Staple Definitions</strong><br>
<em><u>Retail Food</u></em>: Anything that&#146;s not fabric arts, no health and beauty, no soaps, no crafts, no
firewood, no charitable donations or CD sales, no household supplies or laundry detergent, nothing in the "Non-Food
Item" category, no pet supplies. No live plants or worm castings. It would however include our prepared foods since
they are cold and packaged for take out.
<br>
<em><u>Retail Staple Food</u></em>:Retail foods covered above but without prepared or hot foods, candy, condiments,
spices, coffee, tea, cocoa, carbonated or uncarbonated drinks. Bread probably qualifies for a staple food sale, but
probably not cake or cookies.

<a name="pname">
<br><br>
<strong>Product Name</strong><br>
A brief descriptive name. There should be some consistency in the product number/name so that changes to a product
should be fairly small things such as change of price, category listing, descriptive information.  But if it is
really very different you should probably add it as a new product  This should be better for your sales in the
long run.  Similarly, if you have an existing unlisted product, it should improve your sales to keep the product
number rather than create a new product.

<a name="notes">
<br><br>
<strong>Product Details</strong> (not required)<br>
If the approximate size, weight or contents are not clear from the name of the product, list those details here.
If it is a package of several items, the approximate (or exact, whichever the case may be) number of items in the
package should be listed.

<a name="cat">
<br><br>
<strong>Category</strong><br>
Category that it should be listed under. If we do not have an existing category and/or subcategory, please give us
some guidance on how the product would be categorized. To suggest an additional category, email
<a href="mailto:<?php echo PRICELIST_EMAIL; ?>"><?php echo PRICELIST_EMAIL; ?></a>.

<a name="subcat">
<br><br>
<strong>Subcategory</strong><br>
Subcategory that it should be listed under. If we do not have an existing category and/or subcategory, please give us
some guidance on how the product would be categorized. To suggest an additional category, email
<a href="mailto:<?php echo PRICELIST_EMAIL; ?>"><?php echo PRICELIST_EMAIL; ?></a>.

<a name="inventory">
<br><br>
<strong>Inventory</strong><br>
By selecting Yes for inventory, the number you enter will be displayed on the product list. If you select No,
there will be no mention of how many of the items you have in stock. If you use the inventory and enter an amount,
whenever a customer adds that item to their basket, the quantity they selected will be deducted from your inventory
number. You can always add to the number if you get more in stock. This way, when the number reaches &ldquo;0&rdquo;,
no more can be ordered unless you add to that number or if you click no on the inventory option.

<a name="price"><a name="order">
<br><br>
<strong>Price and Pricing Unit</strong> and <strong>Ordering Unit</strong><br>
The price, the pricing unit (e.g. whatever comes after the &ldquo;per&rdquo; in $ per ____), and the ordering unit
(when the customer orders, they will order number of ______).  Also, we need to know if the item has a random weight
&ndash; the customer will not know the price until you provide a weight for it  after the item is ordered.
<br><br>
EXAMPLE 1:  You are selling a 5 pound bag of wheat for $10.00.  The price is $10.00.  The pricing unit is &ldquo;5
pound bag&rdquo; (because you are selling at $10.00 per 5 pound bag).  The ordering unit is also &ldquo;5 pound
bag&rdquo; because the customer orders by the number of 5 pound bags that they wish to buy.  This item is not
considered random weight because the bags always weigh the same and the customer knows the final price when it
is ordered.  Notice in this example that even though the flour ends up costing $2 per pound, you would not list
as $2 per pound because you are only selling 5 pound bags that cost $10.
<br><br>
EXAMPLE 2:  You are selling a bag of ground beef.  The bag weights range between .75 and 1.25 pounds and you sell
the meat at $4 per pound.  The price depends on the weight but you want the customer orders the number of bags, not
the number of pounds because you do not package it in exactly 1 pound bags.  In this case, your price would be $4,
your pricing unit would be &ldquo;pound&rdquo;, and your ordering unit would be &ldquo;bag&rdquo;.  This is a random
weight product because the price cannot be pre-determined by the customer.  It can only be determined after you (the
producer) enter the weight.
<br><br>
EXAMPLE 3:  You are selling packages of chicken breasts, the package varies in weight from a little under 2 pounds
to a little over 2 pounds.  However, you always charge the same price per package ($6.00).  In this case, price is
$6.00, the pricing unit is &ldquo;package&rdquo;, and the ordering unit is package.  This is not a random weight
product because the customer knows the price in advance.
<br><br>
EXAMPLE 4.  You are selling tomatoes at $3.00 per pound.  The customer can order by the pound.  If the customer
orders 3 pounds, you have decided that you will always provide a minimum of 3 pounds but will not charge for exact
weight but instead charge for the weight ordered.  So if the customer orders 3 pounds and you end up giving them
3.1 pounds, you still only charge $9.00.  In this case, the price is $3.00.  The pricing unit is &ldquo;pound&rdquo;
and the ordering unit is &ldquo;pound&rdquo;.   This is not a random weight product because the customer can determine
what the price will be in advance.  Modifying this example slightly, if you did decide that you want to charge for
exact weight (e.g. charge $9.30 for the 3.1 pound bag) then all of the other information would be the same, but now
this would be a random weight product because when the customer orders 3 pounds, he/she has no way of determining
the final price which depends upon your weighing the item).
<br><br>
In general, you should use descriptive terms (though not too long) for the ordering and pricing units. Some standard
terms will be "pound", "bag", "package" but in many cases it will be worthwhile to be even more descriptive. For
instance, if you are selling T-bone steaks 1 to a package at $8/pound, then instead of package you could put steak
as the ordering unit. In this case the pricing unit would be pound. However, if the package had two steaks, you
would either put "package" or "package of 2 steaks" as the ordering unit. Any product that the customer orders by
the item can also get descriptive pricing and ordering units. For instance, if you are selling by the individual
tomato, ear of corn, squash, jar or jelly, etc. then you could list "tomato", "ear", "squash", or "jar" as the
ordering unit. The pricing units could also be listed as "tomato", "ear", "squash" or "jar", or you could just
use the generic "each" in the pricing unit. It may be helpful when you choose these units to think of the way this
information will appear on your product listing and on invoices. Your ordering unit will be displayed on your
product/price list as follows "Order number of ___________s." So if you choose "steak" as your pricing unit, your
listing will say "Order number of steaks". On the customer invoice, the ordering unit will show up under the quantity
heading with the # ordered and the ordering unit (e.g., 1 steak, or 2 steaks). For pricing unit, the unit you choose
will show up on the product list and on the invoice as price/pricing unit. So for the T-bone above this would be
$8/pound because pound was the pricing unit.

<a name="extra">
<br><br>
<strong>Extra Charge</strong><br>
(coming soon.)

<a name="ran">
<br><br>
<strong>Random Weight</strong><br>
If it is a random weight product (the price depends on the weight), we need to know the approximate range of weights.
Example: roast, sold by a package of one roast, price is $4/lb, the roasts weigh between 2 and 4 pounds. If it is a
variable weight product which is sold for a single standard price rather than a price based on a random weight, you
should have listed the range of weights in the basic description so the customers know what they are getting.  The
customer needs this information to know how much to order.

<a name="minmax">
<br><br>
<strong>Minimum/ Maximum Weights</strong><br>
This applies only to products that require weighing at the time of purchase. A minimum and maximum weight provides the
customer with a general idea of what to expect.  It will also allow the customer to let you know if they would like a
smaller or larger item.  These can be approximate, but the more accurate the range, the more informed the customer will
be.  Note that the minimum and maximum weight must be in the same unit as the pricing unit.  So if your product is
$5/pound, enter minimum and maximum weights in pounds (or decimal/fractions thereof).  For example: a whole chicken
might min. weight = 3 pounds / max. weight = 5 pounds.   A steak might range 8 to 10 ounces but if it is sold by the
pound, you would list min. weight = .5 pounds / max. weight = .625 pounds. If a package is always approximately one
pound, enter 1 in both the min. and max. fields and this will be reflected.

<a name="meat">
<br><br>
<strong>Meat Weight/Type</strong><br>
Selecting one of these will automatically insert the following text with the option in caps into your product
description: "You will be billed for exact LIVE weight." Use this if your product is a random weight item and
it is not clear from the name what type it is.

<a name="type">
<br><br>
<strong>Product Type</strong><br>
If a product is certified organic, all natural, or otherwise designated, this can be chosen from the drop down box.
If marking a product as certified in some way, please be sure the certification is officially registered.

<a name="storage">
<br><br>
<strong>Storage Type</strong><br>
Choose the storage requirements for this product.  It is important to classify how the product must be transported
and stored on delivery day.

<a name="future">
<br><br>
<strong>Future Deliveries</strong><br>
If the product is one that is being sold in advance but will not actually be delivered until a future order cycle,
let us know the date that it will be delivered.  This must be the date of an existing co-op delivery.  If you are not
sure about the future delivery date, please contact us to discuss this.  If this is an item where you will be setting
up more than one payment, you will need to contact us to discuss this.  Also, contact us if the item will be delivered
directly to the customer by you and not through the co-op so that we can help you work out the details of listing the
item.  Contact us at <a href="mailto:<?php echo HELP_EMAIL; ?>"><?php echo HELP_EMAIL; ?></a>.

<a name="new">
<br><br>
<strong>Save as a New Product</strong><br>
Select this checkbox to save your changes as a NEW product while leaving the original product unchanged.  This is an
easy way to clone a product with only minor changes.  The new product will not include any picture that is attached
to the current product. Be aware that products can not be deleted once they have been created.

<a name="click">
<br><br>
<strong>Update Existing Product</strong><br>
Click this button to save any changes you&#146;ve made to the product information. It will return you to the product
list where you can see the updated listing. You can then again click the link to edit the information and submit more
changes.
<br><br>
<strong>Save as a New Product</strong><br>
Click this button to save your changes as a NEW product while leaving the original product unchanged.  This is an easy
way to clone a product with minor changes.  Be aware that products can not be deleted once they have been created.
<br><br>
<strong>Add This Product</strong><br>
Click this button to add the information as a new product.  Be aware that products can not be deleted once they have
been created.
<br><br>
<strong>Cancel</strong><br>
If you made changes you don&#146;t want to keep, you can click the Cancel button to return to the product list.  You
can also navigate away from this page without clicking the Update/Save/Add buttons and no changes will be made.

<br>Once changes are submitted, they will be reviewed by the coop admins to ensure everything is ok and will be released
to the public once the ordering cycle is open.

<br><br>

<!-- CONTENT ENDS HERE -->
<br>
<div align="right">
<a href="#" onClick="window.close()">Close Window</a>
</div>
</body>
</html>
