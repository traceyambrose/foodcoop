<?php

/*******************************************************************************

NOTES ON USING THIS TEMPLATE FILE...

The heredoc convention is used to simplify quoting.
The noteworthy point to remember is to escape the '$' in
variable names.  But functions pass through as expected.

The short php if-else format is also useful in this context
for inline display (or not) of content elements:
([condition] ? [true] : [false])

All variables in this file are loaded at include-time and interpreted later
so there is no required ordering of the assignments.

All system constants from the configuration file are available to this template




********************************************************************************
Model for the overall invoice display page might look something like this:

 -- OVERALL INVOICE DISPLAY -------------
|                                        |
|     -- ROUTING DISPLAY SECTION ---     |
|    |                              |    |
|    |                              |    |
|     ------------------------------     |
|     -- PRODUCT DISPLAY SECTION ---     |
|    |                              |    |
|    |                              |    |
|    |                              |    |
|    |                              |    |
|     ------------------------------     |
|     -- ADJUSTMENT DISPLAY SECT ---     |
|    |                              |    |
|    |                              |    |
|     ------------------------------     |
|     -- MEMBERSHIP DISPLAY SECT ---     |
|    |                              |    |
|    |                              |    |
|     ------------------------------     |
|     -- TOTALS DISPLAY SECT -------     |
|    |                              |    |
|    |                              |    |
|     ------------------------------     |
|                                        |
 ----------------------------------------

|                                        |
|         ADMIN DISPLAY SECTION          |
|                                        |



The PRODUCT DISPLAY SECTION is composed of several subsections as shown here

 -- PRODUCT DISPLAY SECTION -------------
|                                        |
|       -- PRODUCER 1 ------------       |
|      |                          |      |
|      | PRODUCER DISPLAY (OPEN)  |      |
|      |                          |      |
|      |   PRODUCT DISPLAY 1      |      |
|      |   PRODUCT DISPLAY 2      |      |
|      |         ...              |      |
|      |   PRODUCT DISPLAY n      |      |
|      |                          |      |
|      | PRODUCER DISPLAY (CLOSE) |      |
|      |                          |      |
|       --------------------------       |
|                                        |
|       -- PRODUCER 2 ------------       |
|      |                          |      |
|      | PRODUCER DISPLAY (OPEN)  |      |
|      |                          |      |
|      |   PRODUCT DISPLAY n+1    |      |
|      |         ...              |      |
|      |                          |      |
|      | PRODUCER DISPLAY (CLOSE) |      |
|      |                          |      |
|       --------------------------       |
|                                        |
|                   ...                  |
|                                        |
|       -- PRODUCER M ------------       |
|      |                          |      |
|      | PRODUCER DISPLAY (OPEN)  |      |
|      |                          |      |
|      |   PRODUCT DISPLAY etc    |      |
|      |         ...              |      |
|      |                          |      |
|      | PRODUCER DISPLAY (CLOSE) |      |
|      |                          |      |
|       --------------------------       |
|                                        |
 ----------------------------------------


Finally, the PRODUCT DISPLAY SECTIONs include some markup that
is interpreted from the PRODUCT DISPLAY PRICE SECTION which can be used
to customize display of that column

In general, the OVERALL INVOICE DISPLAY ($overall_invoice_display_output) below
will contain the other sections with possible additional levels of embedding: 

$overall_invoice_display_output Markup for the entire customer invoice
$product_display_output         Markup for the entire product listing section, sans the header
$product_display_price_output   Markup for the pricing information for a particular product
$adjustment_display_output      Markup for the adjustments section
$membership_display_output      Markup for all historic membership accounting information
$totals_display_output          Markup for the totals section on the invoice page
$routing_display_output         Markup for the routing portion of the invoice (customer info and such)
$admin_display_output           Markup for the admin section (not part of the "official" invoice)


********************************************************************************

The following variables are available to the product listing section:

FROM THE DATABASE:

$a_business_name                Producer business name
$a_first_name                   Producer first name
$a_last_name                    Producer last name
$category_id                    Product category id
$taxable_cat                    0 or 1 if the category is taxable
$taxable_subcat                 0 or 1 if the subcategory is taxable
$product_id                     Numeric product id
$producer_id                    Five-char producer id
$product_name                   Short-textual product name
$item_price                     Price for each item (or for each pricing unit quantity)
$pricing_unit                   Units used for pricing of random-weight items
$quantity                       Quantity ordered
$ordering_unit                  Units used for ordering the item
$out_of_stock                   0 or 1 if this item is out of stock
$random_weight                  0 or 1 if this is a random-weight item
$min_weight                     Minimum number of pricing-units for random-weight items
$max_weight                     Maximum number of pricing-units for random-weight items
$total_weight                   Actual weight for random-weight items -- indeterminate until after ordering
$extra_charge                   Extra charges for each item ordered (no tax, no fee)
$notes                          Notes from the customer to the producer
$future_delivery_id             The delivery id in which this item is expected to be delivered (not used)
$storage_code                   Special storage coding (e.g. REF, FROZ, NON, EGGS)
$auth_type                      Invoice-owner auth_type for the owner of this invoice
$last_name                      Invoice-owner last name
$first_name                     Invoice-owner first name
$business_name                  Invoice-owner business name -- if available
$first_name_2                   Invoice-owner secondary first name
$last_name_2                    Invoice-owner secondary last name
$address_line1                  Invoice-owner home address -- line 1
$address_line2                  Invoice-owner home address -- line 2
$city                           Invoice-owner home address -- city
$county                         Invoice-owner home address -- county
$state                          Invoice-owner home address -- state
$zip                            Invoice-owner home address -- zip
$work_address_line1             Invoice-owner work address -- line 1
$work_address_line2             Invoice-owner work address -- line 2
$work_city                      Invoice-owner work address -- city
$work_state                     Invoice-owner work address -- state
$work_zip                       Invoice-owner work address -- zip
$email_address                  Invoice-owner primary email address
$email_address_2                Invoice-owner secondary email address
$home_phone                     Invoice-owner home phone number
$work_phone                     Invoice-owner work phone number
$mobile_phone                   Invoice-owner mobile/cell phone number
$fax                            Invoice-owner fax number
$mem_taxexempt                  0 or 1 if invoice owner is exempted from paying taxes
$mem_delch_discount             0 or 1 if invoice owner is exempted from paying delivery charges


CALCULATED AND OTHER VALUES:

$fontface                       Legacy value from the config file
$show_name                      Member modified name
$product_display_price_output   Markup for the pricing information for a particular product
$full_extra_charge              Total of extra charges for this item (because of multiples ordered)
$min_price                      Minimum price for random-weight item
$max_price                      Maximum price for random-weight item
$stock_image                    Url for out-of-stock image -- only defined when out_of_stock == 1
$weight_unit                    Weight units for random-weight item
$display_business_name          Producer modified business name
$display_price                  Formatted as e.g. $3.50/gallon and $4.00 / bucket *
                                (* indicates taxable item; $3.50 is regular part; 4.00 is extra-charge part)
$display_weight_actual          Either actual weight (after producer input) or show zero with $display_weight_actual_text comment preceding
$display_weight_pending         Either actual weight (after producer input) or show pending message from $display_weight_pending_text
$display_weight_both            Either actual weight (after producer input) or show min/max weights with $display_weight_both_text comment between
$display_weight_average         Either actual weight (after producer input) or display average weight with $display_weight_average_text preceding
$display_weight_minimum         Either actual weight (after producer input) or show min weight with $display_weight_minimum_text preceding
$display_weight_maximum         Either actual weight (after producer input) or show max weight with $display_weight_maximum_text preceding
$effective_price                Price that will be used for this item until actual weight is entered based upon RANDOM_CALC configuration
                                (e.g. if min price is $3.00 and max price is $5.00; if using AVG then totals will be calculated using $4.00;
                                If using MIN then totals will be calculated using $3.00; if using MAX then totals will be calculated using $5.00)
$message_incomplete             Message used if random weight is not yet entered
                                (taken from the appropriate of: $message_incomplete_zero, $message_incomplete_avg, $message_incomplete_min, $message_incomplete_max)
$extra_cost                     Running total of $full_extra_charge values (c.f.)
$exempt_product_cost            Running total of non-taxable product prices (not including extra-charges)
$number_of_products             Number of different products ordered

*******************************************************************************/



// Miscellaneous markup elements for the product-list section

// This is the code used between the price/pricing_unit AND the extra_charge/ordering_unit
$pricing_ordering_separator = '<br>+&nbsp;';

// This is for display of the random weights when the weight is not yet known
$display_weight_actual_text  = 'Using ';                               // e.g. "Using 3.4 pounds"
$display_weight_pending_text = '<font color="#770000">Price updated when producer enters weight</font>';  // e.g. "Producer has not yet entered weight"
$display_weight_both_text    = '&ndash;';                              // e.g. "2.3-5.6 pounds"
$display_weight_average_text = 'Est. ';                              // e.g. "Approx 4.2 pounds"
$display_weight_minimum_text = 'More than ';                           // e.g. "More than 2.6 pounds"
$display_weight_maximum_text = 'Less than ';                           // e.g. "Less than 6.8 pounds"

$message_incomplete_zero = 'Totals do not include any cost for unfilled random-weight items';
$message_incomplete_avg = 'Totals are based upon the average weight for unfilled random-weight items';
$message_incomplete_min = 'Totals are based upon minimum weights for unfilled random-weight items';
$message_incomplete_max = 'Totals are based upon maximum weights for unfilled random-weight items';

$taxable_product_flag = ' * '; // Flag to be attached to taxable products
$out_of_stock_checkmark = '<img src="grfx/checkmark_wht.gif" alt="out of stock">';


/************************* PRODUCER DISPLAY (OPEN) ****************************/

$producer_display_section_open = <<<EOT
(\$producer_id_prior != '' ? '
          <tr align="left">
            <td colspan="6" height="4"></td>
          </tr>'
: '').'
          <tr align="left">
            <td colspan="6" bgcolor="#aaaaaa" height="1"></td>
          </tr>
          <tr align="left">
            <td colspan="6" height="4"></td>
          </tr>
          <tr>
            <td colspan="6" ><font face="arial" color="#770000" size="-1"><b>'.\$display_business_name.'</b></font></td>
          </tr>'
EOT;

/************************* PRODUCER DISPLAY (CLOSE) ***************************/

$producer_display_section_close = <<<EOT
'
'
EOT;

/************************* PRODUCT DISPLAY SECTION ****************************/

// This is used to interpret each product line-item
$product_display_section = <<<EOT
'
          <tr align="center">
            <td align="right" valign="top"><font face="arial" size="-1">'.\$stock_image.'</td>
            <td align="left" valign="top"><font face="arial" size="-1">('.\$quantity.') '.Inflect::pluralize_if (\$quantity, \$ordering_unit).'</td>
            <td align="left" valign="top"><font face="arial" size="-1">('.\$product_id.') '.\$product_name.'</td>
            <td align="center" valign="top"><font face="arial" size="-1">'.\$product_display_price_output.'</td>
            <td align="center" valign="top"><font face="arial" size="-1">'.\$display_weight_pending.'</td>
            <td align="right" valign="top"><font face="arial" size="-1"><b>$'.number_format (round (\$effective_price + \$full_extra_charge, 2), 2).'</b></td>
          </tr>'.
(\$notes != '' ? '
          <tr align="center">
            <td colspan="2"></td>
            <td align="left" valign="top" bgcolor="#ffeebb"><font face="arial" size="-1">'.\$future.' <strong>Customer note:</strong> '.\$notes.'</td>
            <td>&nbsp;</td>
          </tr>'
: '')
EOT;

/************************** PRODUCT DISPLAY PRICE SECTION *********************/

$product_display_price_section = <<<EOT
(\$item_price != 0 ? \$taxable_product.'$'.number_format (round (\$item_price, 2), 2).'/'.\$pricing_unit.'
' : '').
(\$item_price != 0 && \$extra_charge != 0 ? \$pricing_ordering_separator : '').
(\$extra_charge != 0 ? '$'.number_format (round (\$extra_charge, 2), 2).'/'.Inflect::singularize (\$ordering_unit).'
' : '')
EOT;


/*******************************************************************************
The following variables are available to the ADJUSTMENT DISPLAY SECTION and MEMBERSHIP DISPLAY SECTIONs:

FROM THE DATABASE:

$transaction_id                      Unique numeric transaction ID
$transaction_type                    Numeric value of transaction type
$transaction_name                    Human-readable transaction name
$transaction_amount                  Dollar amount of transaction (may be negative)
$transaction_user                    Username of person who posted transaction
$transaction_taxed                   0 or 1 if the transaction is taxed
$transaction_timestamp               Date-time the transaction was posted
$transaction_batchno                 Integer field
$transaction_memo                    20-character field
$transaction_comments                200-character field
$transaction_method                  Single character matching the payment method


CALCULATED VALUES:

$taxable_product                     Asterisk (* ) for display if product is taxable
$adjustments_exist                   0 or 1 if there are adjustments to display
$taxed_adjustment_cost               Running total of taxable adjustments on this invoice
$exempt_adjustment_cost              Running total of non-taxable adjustments on this invoice
$membership_this_exist               0 or 1 if there is membership accounting on THIS order
$membership_cost                     Running total of membership fees/payments

*************************** ADJUSTMENT DISPLAY SECTION ************************/

$adjustment_display_section = <<<EOT
'
              <tr align="center">
                <td></td>
                <td align="right" valign="top"><font face="arial" size="-1"><b> </b>&nbsp;&nbsp;</td>
                <td width="275" align="left" valign="top" colspan="4"><font face="arial" size="-1"><b>'.\$transaction_name.\$taxable_product.'</b><br>'.\$transaction_comments.'</td>
                <td align="right" valign="top"><font face="arial" size="-1">$'.number_format(\$transaction_amount, 2).'</td>
              </tr>'
EOT;

/************************** MEMBERSHIP DISPLAY SECTION ************************/

$membership_display_section = <<<EOT
'
              <tr align="center">
                <td></td>
                <td align="right" valign="top"><font face="arial" size="-1"><b> </b>&nbsp;&nbsp;</td>
                <td width="275" align="left" valign="top" colspan="4"><font face="arial" size="-1"><b>'.\$transaction_name.'</b><br>'.\$transaction_comments.'</td>
                <td align="right" valign="top"><font face="arial" size="-1">$'.number_format(\$transaction_amount, 2).'</td>
              </tr>'
EOT;




/*******************************************************************************
The following variables are available to the TOTALS DISPLAY SECTION, ROUTING DISPLAY SECTION,
OVERALL INVOICE, and ADMIN DISPLAY SECTIONs:

FROM THE DATABASE:

$msg_all                             Message from database to display on invoices
$msg_bottom                          Message from database to display on invoices
$deltype                             D or P for delivery or pickup respectively
$delcode_id                          Delcode ID where this order should be routed
$delcode                             Delcode (description)
$delcharge                           Charges for delivering to this delcode from delivery_codes table
$transcharge                         Transportation charges from delivery_codes table
$delivery_date                       Delivery date for this order cycle
$payment_method                      C or P for check or paypal respectively from payment_method table
$payment_desc                        Textual description of payment method
$msg_unique                          Message to specific customer for this invoice
$route_name                          Route name from the routes table corresponding to route_id in the delivery_codes table
$route_desc                          Route description from the routes table corresponding to route_id in the delivery_codes table
$deldesc                             Verbose description of delivery code -- often with address and contact inforamtion -- from delivery_codes table
$hub                                 Hub where this order should be sorted
$truck_code                          Truck code that will transport this order
$special_order                       0 or 1 for special orders (not currently implemented)
$current_delivery_id                 Delivery ID for this order
$auth_type                           Authorization type for the owner of this invoice
$membership_type_id                  Membership type ID from the membership_types table
$order_cost                          Per-order cost -- from membership_types table


CALCULATED VALUES:

$previous_balance                    Unpaid balance due prior to this order
$city_name                           Name of city for tax purposes
$copo_city                           Tax code for city
$city_tax_rate                       Tax rate in this city
$county_name                         Name of county for tax purposes
$copo_county                         Tax code for county
$county_tax_rate                     Tax rate in this county
$state_id                            Tax ID for this state
$state_tax_rate                      Tax rate for this state
$coop_fee_basis                      Amount upon which co-op fee will be assessed
                                     (taxed products + taxed adjustments + exempt products)
$coop_fee                            Amount of the co-op fee that is assessed
$total_taxable_cost                  Total taxable costs (products + adjustments + membership?)
$total_exempt_cost                   Total non-taxable costs (exempt products + extra-charges + membership?)
$total_basket_cost                   Basket total (taxed_product_cost + exempt_product_cost + extra-charges)
$total_current_cost                  (taxable costs + exempt costs)
$total_tax_rate                      Overall tax rate (city, county, state)
$city_sales_tax                      Amount of tax needed by city
$county_sales_tax                    Amount of tax needed by county
$state_sales_tax                     Amount of tax needed by state
$total_tax                           Total of taxes needed by city, county, state
$subtotal_1                          $total_current_cost + $total_tax + $delcharge + $coop_fee
$potential_paypal_fee                The paypal fee that WOULD BE CHARGED if the person had chosen to pay by paypal
$paypal_fee                          The actual paypal fee -- based upon how the member chose to pay
$subtotal_2                          $total_current_cost + $total_tax + $delcharge + $coop_fee + $potential_paypal_fee - $membership_cost;
$grand_total                         $total_current_cost + $total_tax + $delcharge + $coop_fee + $exempt_adjustment_cost + $paypal_fee;
$grand_total_coop                    $grand_total - $total_tax - $paypal_fee;
$pay_this_amount                     $subtotal_2 + $order_cost - $potential_paypal_fee + $paypal_fee + $previous_balance + $membership_cost
$pay_this_amount_zero_min            Either the same as $pay_this_amount or ZERO, whichever is greater
$most_recent_payment_amount          Amount of the members most recent payment applied to a shopping cart (could be partial)
$most_recent_payment_date            Timestamp of most recent payment entered
$most_recent_payment_order           Delivery_id to which most recent payment was applied
$taxable_membership_cost             Membership costs (if taxable)
$exempt_membership_cost              Membership costs (if exempt)
$taxable_coop_fee                    Amount of the coop fee that is applied to taxable items
$exempt_coop_fee                     Amount of the coop fee that is applied to exempt items

***************************** TOTALS DISPLAY SECTION **************************/

$totals_display_section = <<<EOT
(\$previous_balance != 0 && strpos (\$auth_type, 'institution') === false ? '
          <tr align="left">
            <td></td>
            <td>____</td>
            <td colspan="4"><br>
              <font face="arial" color="#770000" size="-1"><b>Previous Credits or Unpaid Balances</b>:</font>
              <font face="arial" size="-1"> $&nbsp;'.number_format(\$previous_balance, 2).' '.(\$previous_balance < 0 ? 'Credit' : 'Owed').'</font>
            </td>
          </tr>'
: '').
(\$adjustments_exist != '' ? '
          <tr align="left">
            <td></td>
            <td>____</td>
            <td colspan="4"><br><font face="arial" color="#770000" size="-1"><b>Adjustments</b></font></td>
          </tr>
          '.\$adjustment_display_output
: '').'
          <tr align="center">
            <td align="right" valign="top"><font face="arial" size="-1"></td>
            <td align="left" valign="top" colspan="6"><font face="arial" size="-1"><br><b>Note:</b> E-mail any problems with your order to '.PROBLEMS_EMAIL.' It may be that our records are incomplete or inaccurate. Amounts paid or credits may still be unrecorded. If you have questions about this, email '.TREASURER_EMAIL.'.</td>
          </tr>
<!-- NEED 7 -->
          <tr>
            <td colspan="5" align="right"><br>'.\$font.'<b>SUBTOTAL</b></td>
            <td align="right">'.\$font.'<br><b>$ '.number_format(round (\$total_basket_cost, 2), 2).'</b></td>
          </tr>'.
(\$delivery_id >= DELIVERY_NO_PAYPAL && SHOW_ACTUAL_PRICE != true ? '
          <tr>
            <td colspan="5" align="right">'.\$font.'<b>* Co-op Fee on $'.number_format(\$coop_fee_basis, 2).' at '.(\$coop_markup * 100).'%</b></font></td>
            <td align="right">'.\$font.'<b>$ '.number_format(\$coop_fee, 2).'</b></font></td>
          </tr>'
: '').
(\$total_tax != 0 ? '
          <tr>
            <td colspan="5" align="right">'.\$font.'<b>Sales tax on taxable sales'.\$taxable_product_flag.'</b></font></td>
            <td align="right">'.\$font.'<b>$ '.number_format(\$total_tax, 2).'</b></font></td>
          </tr>'
: '').
(\$delivery_id < DELIVERY_NO_PAYPAL && SHOW_ACTUAL_PRICE != true ? '
          <<tr>
            <td colspan="5" align="right">'.\$font.'<b>'.(\$delivery_id < DELIVERY_NO_PAYPAL ? 'Shipping and Handling' : '').(SHOW_ACTUAL_PRICE != true ? '+ '.number_format(\$coop_markup * 100, 0).'% Co-op Fee' : '').'</font></b></td>
            <td  align="right">'.\$font.'<b>$&nbsp;'.number_format(\$coop_fee + \$potential_paypal_fee, 2).'</b></font></td>
          </tr>'
: '').
(\$special_order != "1" && \$delcharge != 0? '
          <tr>
            <td colspan="5" align="right">'.\$font.'<b>Extra Charge for Home or Work Delivery </b></font></td>
            <td  align="right">'.\$font.'<b>$&nbsp;'.number_format(\$delcharge, 2).'</b></font></td>
          </tr>'
: '').'
          <tr>
            <td colspan="5"></td>
            <td bgcolor="#000000" height="1"></td>
          </tr>
          <tr>
            <td colspan="5" align="right">'.\$font.'<b>Invoice&nbsp;Total </b></font></td>
            <td align="right">'.\$font.'<b>$&nbsp;'.number_format(\$subtotal_2, 2).'</b></font></td>
          </tr>
          <tr>'.
(\$membership_cost != 0 || \$order_cost != 0 ? '
            <td colspan="5" align="right">'.\$font.'<b>Membership/Fees</b></font></td>
            <td  align="right">'.\$font.'<b>$ '.number_format(\$membership_cost + \$order_cost, 2).'</b></font></td>
          </tr>'
: '').
(\$exempt_adjustment_cost != 0 ? '
          <tr>
            <td colspan="5" align="right">'.\$font.'<b>Non-taxed Adjustments</b></font></td>
            <td  align="right">'.\$font.'<b>$&nbsp;'.number_format(\$exempt_adjustment_cost, 2).'</b></font></td>
          </tr>'
: '').'
          <!--<tr>
            <td colspan="5" align="right">'.\$font.'<font size="+1"><b>'.(\$payment_method == 'P' ? '(Current choice)' : '').' Total I: </td>
            <td  align="right">'.\$font.'$&nbsp;'.number_format(\$subtotal_2, 2).'</b></td>
          </tr>
          <tr>-->'.
(\$delivery_id < DELIVERY_NO_PAYPAL ? '
            <td colspan="6" align="right">'.\$font.'<font size="+1"><b><u>LESS Cash discount</u>: </td>
            <td align="right">'.\$font.'$&nbsp;-'.number_format(\$potential_paypal_fee, 2).'</td>
          </tr>'
: '').'
            <!--<tr>
              <td colspan="5" align="right">'.\$font.'<font size="+1"><b><u>'.(\$payment_method == 'C' ? '(Current choice)' : '').' Total II with discount for payment by check or cash</u>: </td>
              <td align="right">'.\$font.'<u>$&nbsp;'.number_format(\$subtotal_2 - \$potential_paypal_fee, 2).'</b></u></td>
          </tr>-->'.
((\$previous_balance != 0 && strpos (\$auth_type, 'institution') !== false) ? '
          <tr>
            <td colspan="5"></td>
            <td bgcolor="#000000" height="1"></td>
          </tr>
          <tr>
            <td colspan="5" align="right"><font size="+2">REMIT THIS AMOUNT:</font></td>
            <td align="right"><font size="+2">'.(\$unfilled_random_weight ? '<font size="-1">'.\$display_weight_pending_text.'</font>' : '$&nbsp;'.number_format(round (\$total_basket_cost + \$exempt_adjustment_cost + \$membership_cost + \$order_cost, 2), 2)).'</font></font></td>
          </tr>'
: '').
(strpos (\$auth_type, 'institution') === false ? '
          <tr>
            <td colspan="5" align="right">'.\$font.'<b>Previous '.(\$previous_balance < 0 ? 'Credit' : 'Balance Due').'</b></font></td>
            <td align="right">'.\$font.'<b>$&nbsp;'.number_format(\$previous_balance, 2).'</b></font></td>
          </tr>
          <tr>
            <td colspan="5"></td>
            <td bgcolor="#000000" height="1"></td>
          </tr>
          <tr>
            <td colspan="5" align="right"><font size="+2">PAY THIS AMOUNT:</font></td>
            <td align="right"><font size="+2">'.(\$unfilled_random_weight ? '<font size="-1">'.\$display_weight_pending_text.'</font>' : '$&nbsp;'.number_format (\$pay_this_amount_zero_min, 2)).'</font></font></td>
          </tr>'
: '').
(\$invoice_payment[\$delivery_id - 1] != 0 ? '
          <tr>
            <td colspan="5" align="right">Thank you for your payment of $&nbsp;'.number_format (\$invoice_payment[\$delivery_id - 1], 2).'</font></td>
            <td>&nbsp;</td>
          </tr>'
: '').
(\$pay_this_amount < 0 ? '
          <tr>
            <td colspan="5" align="right">($&nbsp;'.(-1 * \$pay_this_amount).' CREDIT) Nothing is due at this time</td>
            <td></td>
          </tr>'
: '')
EOT;

/*************************** ROUTING DISPLAY SECTION **************************/

$routing_display_section = <<<EOT
'
          <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
              <td colspan="2" align="center">
                <img src="'.BASE_URL.PATH.'/grfx/logo.jpg" width="70" height="70"><br><br>
              </td>
            </tr>
            <tr>
              <td valign="top" width="50%">
                <font face="arial" size="+1"><b>'.\$show_name.'<br>
                '.(convert_route_code(\$basket_id, \$member_id, \$last_name, \$first_name, \$business_name, \$a_business_name, \$hub, \$delcode_id, \$deltype, \$truck_code, "", \$show_mem, \$show_mem2, \$product_name, \$quantity, \$ordering_unit, \$product_id, \$item_price, \$delcode)).'</b></font>
              </td>
              <td valign="top" align="right" rowspan="3" width="50%">
                <font face="arial">
                <font size="+1"><b>'.\$route_name.' &ndash; '.\$delcode.'</b></font><br><br>
                <font color="#990000" size="-1">'.\$msg_all.'</font>
                </font>
              </td>
            </tr>
            <tr>
              <td><strong>Invoice No. '.\$basket_id.'</strong><br>
              <strong>'.date ('F j, Y', strtotime (\$delivery_date)).'</strong></td>
            </tr>
            <tr>
              <td>
                <table width="100%" border="0" cellpadding="5" bgcolor="#eeeeee">
                  <tr>
                    <td width="50%" valign="top">
                      <font size="-1">'.
(\$deltype == 'H' || \$deltype == 'P' ? '
                      <strong>Home address:</strong><br>'.str_replace (' ', '&nbsp;', \$address_line1).'<br>'.
(\$address_line2 != '' ? '
                '.str_replace (' ', '&nbsp;', \$address_line2).'<br>'
: '').'
                '.str_replace (' ', '&nbsp;', \$city.', '.\$state.', '.\$zip).'<br>' :
'').
(\$deltype == 'W' ? '
                      <strong>Work address:</strong><br>'.str_replace (' ', '&nbsp;', \$work_address_line1).'<br>'.
(\$work_address_line2 != '' ? '
                '.str_replace (' ', '&nbsp;', \$work_address_line2).'<br>'
: '').'
                '.str_replace (' ', '&nbsp;', \$work_city.', '.\$work_state.', '.\$work_zip)
: '').'</font>
                    </td>
                    <td width="50%" valign="top">
                      <font size="-1">'.
(\$email_address != '' ? '
                      <a href="mailto:'.\$email_address.'">'.\$email_address.'</a><br>'
: '').
(\$email_address_2 != '' ? '
                      <a href="mailto:'.\$email_address_2.'">'.\$email_address_2.'</a><br>'
: '').
(\$home_phone != '' ? '
                '.\$home_phone .' (home)<br>'
: '').
(\$work_phone != '' ? '
                '.\$work_phone .' (work)<br>'
: '').
(\$mobile_phone != '' ? '
                '.\$mobile_phone .' (mobile)<br>'
: '').
(\$fax != '' ? '
                '.\$fax .' (fax)<br>'
: '').'</font>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td colspan="2" align="left">
                <pre><font face="Arial,Helvetica">'.str_replace ('<br>', '', \$deldesc).'</font></pre>'.
(\$msg_unique != '' ? '<br><br>
                <font color="#990000" size="-1">'.\$msg_unique.'</font>'
: '').'
              </td>
            </tr>
          </table>

'
EOT;


/************************** OVERALL INVOICE DISPLAY ***************************/

$overall_invoice_display_section = <<<EOT

\$routing_display_output.'
        <table width="100%" cellpadding="0" cellspacing="0" border="0">'.
(\$number_of_products > 0 ? '
          <tr>
            <td colspan="6"><br></td>
          </tr>
          <tr bgcolor="#dddddd">
            <th></th>
            <th valign="bottom" align="left"><font face="'.$fontface.'" size="-1">Quantity</th>
            <th valign="bottom" align="left"><font face="'.$fontface.'" size="-1">Product Name</th>
            <th valign="bottom"><font face="'.$fontface.'" size="-1">Price</th>
            <th valign="bottom"><font face="'.$fontface.'" size="-1">Weight</th>
            <th valign="bottom" align=right><font face="'.$fontface.'" size="-1">Amount</th>
          </tr>'.
\$product_display_output
: '
          <tr>
            <td colspan="6" align="center"><br><br><br><br>EMPTY INVOICE<br>Nothing ordered<br><br><br></td>
          </tr>').'
          '.\$totals_display_output.
(\$pay_this_amount > 0 && strlen (\$message_incomplete) == 0 && \$use == "members" ? '
          <tr>
            <td colspan="6">
              <table width="100%" border="0" cellspacing="10" align="center" style="border:1px solid black;">
                <tr>
                  <td colspan="2" valign="top" align="center">
                    <b>P A Y M E N T &nbsp; &nbsp; O P T I O N S</b>
                  </td>
                </tr>
                <tr>
                  <td valign="top">
                    <b>Pay $'.\$pay_this_amount.' now with PayPal</b><br>
                    <font size="-1">Click the button at the right and be sure to print/bring your<br>PayPal receipt with you to order pickup as proof of payment.</font>
                  </td>
                  <td valign="top">
                    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="paypal">
                    <input type="hidden" name="cmd" value="_xclick">
                    <input type="hidden" name="business" value="'.PAYPAL_EMAIL.'">
                    <input type="hidden" name="item_name" value="Food Coop: '.\$show_name.' #$member_id Delivery Date: '.\$delivery_date.'">
                    <input type="hidden" name="amount" value="'.\$pay_this_amount.'">
                    <input type="image" src="https://www.paypal.com/images/x-click-butcc.gif" border="0" name="submit" alt="Make payments with PayPal - fast, free and secure!"><br>
                    </form>
                  </td>
                </tr>
                <tr>
                  <td colspan="2">OR</td>
                </tr>
                <tr>
                  <td colspan="2" valign="top">
                    <b>Pay $'.\$pay_this_amount.' by cash or check at order pickup</b>
                  </td>
                </tr>
              </table>
            </td>
          </tr>'
: '').'
          <tr>
            <td align="left"><font face="'.\$fontface.'"><br>'.\$font.\$msg_bottom.'</td>
          </tr>
        </table>'
EOT;


/**************************** ADMIN DISPLAY SECTION ***************************/

$admin_display_section = <<<EOT
(\$unfilled_random_weight != 1 && \$use == 'admin' ? '
<table align="center" style="margin-top:2em;background-color:#ddd;border:2px solid black;">
  <tr>
    <th colspan="2">Invoice Administration</th>
  </tr>
  <tr bgcolor="#f8f8f8">
    <td width="60%" align="center">
      Tax information<br>
      <table style="padding:1em;font-family:courier new,courier,monospace">
        <tr><td>Exempt Purchases:</td><td align="right">$'.number_format (round (\$exempt_product_cost, 2), 2).'</td></tr>'.
(round (\$exempt_product_cost * \$coop_markup, 2) != 0 ? '
        <tr><td>Markup on Exempt Total:</td><td align="right">$'.number_format (round (\$exempt_product_cost * \$coop_markup, 2), 2).'</td></tr>'
: '').
(\$extra_cost != 0 ? '
        <tr><td>Total Extra:</td><td align="right">$'.number_format (round (\$extra_cost, 2), 2).'</td></tr>'
: '').
(\$exempt_membership_cost != 0 ? '
        <tr><td>Exempt Membership Fees:</td><td align="right">$'.number_format (round (\$exempt_membership_cost, 2), 2).'</td></tr>'
: '').
(round (\$exempt_adjustment_cost, 2) != 0 ? '
        <tr><td>Exempt Adjustments:</td><td align="right">$'.number_format (round (\$exempt_adjustment_cost, 2), 2).'</td></tr>'
: '').
(\$taxable_membership_cost != 0 ? '
        <tr><td>Exempt Membership Fees:</td><td align="right">$'.number_format (round (\$taxable_membership_cost, 2), 2).'</td></tr>'
: '').'
        <tr><td align="right">EXEMPT TOTAL:</td><td align="right">$'.number_format (\$total_exempt_cost, 2).'</td></tr>
        <tr><td>Taxable Purchases:</td><td align="right">$'.number_format (\$taxed_product_cost, 2).'</td></tr>'.
(\$taxed_adjustment_cost != 0 ? '
        <tr><td>Taxable Adjustments:</td><td align="right">$'.number_format (\$taxed_adjustment_cost, 2).'</td></tr>'
: '').
(round ((\$taxed_product_cost + \$taxed_adjustment_cost) * \$coop_markup, 2) != 0 ? '
        <tr><td>Markup on Taxable Total:</td><td align="right">$'.number_format (round ((\$taxed_product_cost + \$taxed_adjustment_cost) * \$coop_markup, 2), 2).'</td></tr>'
: '').'
        <tr><td align="right">TAXABLE TOTAL:</td><td align="right">$'.number_format (\$total_taxable_cost, 2).'</td></tr>
        <tr><td>COPO City:</td><td>'.\$copo_city.'</td></tr>
        <tr><td>COPO County:</td><td>'.\$copo_county.'</td></tr>
        <tr><td>Current city rate ('.\$city_name.'):</td><td align="center">'.number_format ((\$city_tax_rate * 100), 2).'%</td></tr>
        <tr><td>Current county rate ('.\$county_name.'):</td><td align="center">'.number_format ((\$county_tax_rate * 100), 2).'%</td></tr>
        <tr><td>Current state rate ('.\$state_id.'):</td><td align="center">'.number_format ((\$state_tax_rate * 100), 2).'%</td></tr>
        <tr><td>Collected State Tax:</td><td align="right">$'.number_format(round(\$state_sales_tax, 2), 2).'</td></tr>
        <tr><td>Collected City Tax:</td><td align="right">$'.number_format(round(\$city_sales_tax, 2), 2).'</td></tr>
        <tr><td>Collected County Tax:</td><td align="right">$'.number_format( round(\$county_sales_tax, 2), 2).'</td></tr>
        <tr><td>Total Sales Tax:</td><td align="right">$'.number_format( round(\$total_tax, 2), 2).'</td></tr>
      </table>
    </td>
    <td width="40%" align="center">
      <form action="'.\$PHP_SELF.'" method="post">
        <input name="use" type="hidden" value="adminfinalize">
        <input name="action" type="submit" value="Store copy of final invoice">
      </form>
    </td>
  </tr>
</table>'
: '')
EOT;


/******************************************************************************/
