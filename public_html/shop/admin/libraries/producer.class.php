<?php
class producer {

  function getProducerInvoice_CustomerStorage($producer_id, $delivery_id){
  global $db_host, $db_user, $db_pass, $db_name, $connection, $db;
  global $basket_id, $member_id, $last_name, $first_name, $business_name, $hub, $delcode_id, $deltype, $truck_code, $storage_code;

  $auth_mem = "auth_users_mem";
  $table_cat = "categories";
  $table_subcat = "subcategories";
  $table_plsubcat = "product_list_subcategories";
  $table_prod = "product_list";
  $table_prep = "product_list_prep";
  $table_prodtype = "production_types";
  $table_mem = "members";
  $table_prdcr = "producers";
  $table_basket = "customer_basket_items";
  $table_basket_all = "customer_basket_overall";
  $table_delcode = "delivery_codes";
  $table_deldate = "delivery_dates";
  $table_pay = "payment_method";
  $table_fdel = "future_deliveries";
  $table_curdate = "current_delivery";
  $table_rt = "routes";
  $table_adj = "adjustments";
  $table_trans = "transactions";
  define(TABLE_TRANS,"transactions");
  define(TABLE_TTYPES,"transactions_types");

  $domainname = DOMAIN_NAME;

$sqlp = "SELECT $table_mem.business_name, $table_mem.first_name, $table_mem.last_name
  FROM $table_prdcr, $table_mem
  WHERE  $table_prdcr.producer_id = \"$producer_id\"
  AND $table_prdcr.member_id = $table_mem.member_id
  GROUP BY $table_prdcr.producer_id
  ORDER BY business_name ASC, last_name ASC";
  $resultp = @mysql_query($sqlp) or die("Couldn't execute query. ");
  while ($row = mysql_fetch_array($resultp)) {
    $a_business_name = $row['business_name'];
      $a_first_name = $row['first_name'];
      $a_last_name = $row['last_name'];

    if (!$a_business_name) {
      $a_business_name = "$a_first_name $a_last_name";
    }
  }

    $total_pr = 0;
    $subtotal_pr = 0;

  //$table_basket_all,
  $sqlpr = "SELECT $table_basket.basket_id,$table_basket.product_id,$table_basket.item_price,$table_basket.quantity,
    $table_basket.random_weight,$table_basket.total_weight,$table_basket.extra_charge,$table_basket.out_of_stock,
  $table_basket.future_delivery_id,$table_basket.customer_notes_to_producer,
  $table_prod.product_name, $table_prod.random_weight, $table_prod.ordering_unit,
  $table_prod.extra_charge, $table_prod.pricing_unit,
  $table_subcat.subcategory_id, $table_subcat.category_id, $table_prod.subcategory_id,
  pst.storage_code,
  $table_delcode.truck_code,
  $table_basket_all.deltype as ddeltype,
  $table_mem.member_id,
  $table_mem.last_name, $table_mem.first_name, $table_mem.business_name, $table_mem.email_address,
  $table_mem.home_phone, $table_mem.mem_taxexempt, $table_delcode.hub,
  $table_delcode.delcode_id, $table_delcode.delcode, $table_delcode.deltype

  FROM ($table_basket, $table_prod, $table_subcat,
   $table_basket_all, $table_mem, $table_rt, $table_delcode)
  LEFT JOIN product_storage_types pst ON $table_prod.storage_id = pst.storage_id

  WHERE $table_basket.product_id = $table_prod.product_id
  AND $table_prod.producer_id = \"$producer_id\"
    AND $table_prod.subcategory_id = $table_subcat.subcategory_id
    AND $table_prod.hidefrominvoice ='0'

    AND $table_basket_all.member_id = $table_mem.member_id
  AND ($table_basket_all.delivery_id = \"$delivery_id\"
  OR $table_basket.future_delivery_id = \"$delivery_id\")
  AND $table_basket_all.basket_id = $table_basket.basket_id
  AND $table_basket_all.delcode_id = $table_delcode.delcode_id
  AND $table_delcode.route_id = $table_rt.route_id

  GROUP BY $table_basket_all.member_id, $table_basket.product_id
  ORDER BY pst.storage_code ASC, $table_delcode.delcode_id ASC, $table_basket_all.member_id ASC, $table_delcode.hub ASC, $table_basket.item_date ASC";
  $resultpr = @mysql_query($sqlpr) or die("Couldn't execute query 1.");
  while ($row = mysql_fetch_array($resultpr))
    {
    $product_name = $row['product_name'];
    $product_id = $row['product_id'];
    $basket_id = $row['basket_id'];
    $member_id = $row['member_id'];
    $last_name = $row['last_name'];
    $first_name = $row['first_name'];
    $business_name = $row['business_name'];
    $hub = $row['hub'];
    $delcode_id = $row['delcode_id'];
    $delcode = $row['delcode'];
    $deltype = $row['deltype'];
    $truck_code = $row['truck_code'];
    $storage_code = $row['storage_code'];
    $quantity = $row['quantity'];
    $ordering_unit = $row['ordering_unit'];
    $item_price = $row['item_price'];

    $email_address = $row['email_address'];
    $home_phone = $row['home_phone'];
    $ddeltype = $row['ddeltype'];
    $mem_taxexempt = $row['mem_taxexempt'];

    if ( $last_name && $first_name )
      {
        $show_mem2 = $first_name.' '.$last_name;
        $show_mem = $last_name.', '.$first_name;
      }
    else
      {
        $show_mem = $business_name;
      }

    $c_basket_id = $row['basket_id'];
    $product_id = $row['product_id'];
    $category_id = $row['category_id'];
    $product_name = $row['product_name'];
    $quantity = $row['quantity'];
    $random_weight = $row['random_weight'];
    $total_weight = $row['total_weight'];
    $out_of_stock = $row['out_of_stock'];
    $ordering_unit = $row['ordering_unit'];
    $extra_charge = $row['extra_charge'];
    $future_delivery_id = $row['future_delivery_id'];
    $detailed_notes = stripslashes($row['detailed_notes']);
    $notes = stripslashes($row['customer_notes_to_producer']);
    $item_price = $row['item_price'];
    $pricing_unit = $row['pricing_unit'];
    $truck_code = $row['truck_code'];
    $storage_code = $row['storage_code'];

    if($sc && $sc!=$row['storage_code']){
      $display .= '</table>
      <HR BREAK>
      <table width="100%" cellpadding="4" cellspacing="0" border="0">
  <tr>
    <th valign="bottom">PrdID</th>

    <th valign="bottom">Member</th>
    <th valign="bottom">Quantity</th>
    <th valign="bottom">(Add Later)<br>Weight</th>
    <th valign="bottom">Extra<br>Charge</th>
    <th valign="bottom">In/Out of Stock</th>

    <th valign="bottom">Total Item Price</th>
    <th valign="bottom">Edit Item</th>
  </tr>';
    }
    if($sc==$row['storage_code'] && $m==$member_id){
      //skip
    } else {
      $display .= "<tr bgcolor=#DDDDDD><td colspan=\"8\"><a name=\"$member_id\">
    <font size=4><font color=#770000>".(convert_route_code($basket_id, $member_id, $last_name, $first_name, $business_name, $a_business_name, $hub, $delcode_id, $deltype, $truck_code, $storage_code, $show_mem, $show_mem2, $product_name, $quantity, $ordering_unit, $product_id, $item_price, $delcode))." </font></font> /
      <b>Producer: ".stripslashes($a_business_name)."</b><br>
      <font size=4>Member: $show_mem Mem# $member_id:</font>
      $home_phone <a href=\"mailto:$email_address\">$email_address</a><br>
      </td></tr>";

      $sc = $row['storage_code'];
      $m = $member_id;
    }




    if($out_of_stock=="1"){
      $display_total_price = "\$".number_format(0, 2)."";
      }

if($future_delivery_id==$delivery_id){
      $display_weight = "";
      $item_total_price = "0";
      $display_total_price = "<font color=#FF0000>Invoiced in a previous order</font>";
} elseif($future_delivery_id>$delivery_id){
      $display_weight = "";
      $item_total_price = "0";
      $display_total_price = "<font color=#FF0000>Will be delivered in future order</font>";
}elseif($out_of_stock!="1"){
    if ($random_weight=="1") {
  if($total_weight=="0") {
      //$display_weight = "<input type=\"text\" name=\"total_weight\" value=\"$total_weight\" size=\"2\" maxlength=\"11\"> ".$pricing_unit."s";
      $display_weight = "<input type=\"text\" name=\"total_weight\" value=\"$total_weight\" size=\"2\" maxlength=\"11\"> ".$pricing_unit;
  $show_update_button="yes";
      $item_total_3dec = ($item_price*$total_weight) + 0.00000001;
      $item_total_price = round($item_total_3dec, 2);
      $display_total_price = "\$".number_format($item_total_price, 2)."";
      $message_incomplete = "<font color=\"#770000\">Order Incomplete<font>";
  } else {
      //$display_weight = "<input type=\"text\" name=\"total_weight\" value=\"$total_weight\" size=\"2\" maxlength=\"11\"> ".$pricing_unit."s";
      $display_weight = "<input type=\"text\" name=\"total_weight\" value=\"$total_weight\" size=\"2\" maxlength=\"11\"> ".$pricing_unit;
  $show_update_button="yes";
      $item_total_3dec = (($item_price*$total_weight)+($extra_charge*$quantity)) + 0.00000001;
      $item_total_price = round($item_total_3dec, 2);
      $display_total_price = "\$".number_format($item_total_price, 2)."";
  }
    } else {
      $display_weight = "";
  $show_update_button="no";
      $item_total_3dec = (($item_price*$quantity)+($extra_charge*$quantity)) + 0.00000001;
      $item_total_price = round($item_total_3dec, 2);
      $display_total_price = "\$".number_format($item_total_price, 2)."";
    }
} else {
      $display_weight = "";
  $show_update_button="no";
      $item_total_price = "0";
}

    if ($extra_charge) {
      $extra_charge_calc = $extra_charge*$quantity;
      $display_charge = "\$".number_format($extra_charge_calc, 2)."";
    } else {
      $display_charge = "";
    }
    if ($out_of_stock) {
      $display_outofstock = "<img src=\"grfx/checkmark_wht.gif\" align=left>";
      $chk1 = "";
      $chk2 = "checked";
    } else {
      $display_outofstock = "";
      $chk1 = "checked";
      $chk2 = "";
    }

      $display_stock = "<input type=\"radio\" name=\"out_of_stock\" value=\"0\" $chk1>In<br>
      <input type=\"radio\" name=\"out_of_stock\" value=\"1\" $chk2>Out";

    if($item_total_price) {
      $total = $item_total_price+$total;
    }

    $total_pr = $total_pr+$quantity;
    $subtotal_pr = $subtotal_pr+$item_total_price;

    if ($notes) {
      $display_notes = "<br><b>Customer note</b>: $notes";
    } else {
      $display_notes = "";
    }

    if ($quantity >"1") {
    //$display_ordering_unit = "".$ordering_unit."s";
    $display_ordering_unit = "".$ordering_unit;
    } else {
    $display_ordering_unit = "$ordering_unit";
    }



        $display .= "<tr align=\"center\">
          <td align=\"right\" valign=\"top\"><form action=\"$PHP_SELF#$member_id\" method=\"post\">
      <b>#$product_id</b>&nbsp;&nbsp;</td>
          <td align=\"left\" valign=\"top\">
      <b>$product_name</b>
      <br>\$".number_format($item_price, 2)."/$pricing_unit
      <br>$display_notes</td>
          <td align=\"center\" valign=\"top\">
            $quantity $display_ordering_unit</td>
          <td align=\"center\" valign=\"top\">
            $display_weight</td>
          <td align=\"center\" valign=\"top\">
            $display_charge</td>
          <td align=\"left\" valign=\"top\">
            $display_stock $display_outofstock</td>
          <td align=\"center\" valign=\"top\">
            $display_total_price</td>
               <td align=\"center\" valign=\"top\">";
        $display .= "
  <input type=\"hidden\" name=\"updatevalues\" value=\"ys\">
  <input type=\"hidden\" name=\"product_id\" value=\"$product_id\">
  <input type=\"hidden\" name=\"product_id_printed\" value=\"$product_id\">
  <input type=\"hidden\" name=\"producer_id\" value=\"$producer_id\">
  <input type=\"hidden\" name=\"delivery_id\" value=\"$delivery_id\">
  <input type=\"hidden\" name=\"member_id\" value=\"$member_id\">
  <input type=\"hidden\" name=\"c_member_id\" value=\"$member_id\">
  <input type=\"hidden\" name=\"c_basket_id\" value=\"$c_basket_id\">
  <input name=\"where\" type=\"submit\" value=\"Update\">
  </form>";

  if($member_id==$c_member_id){
    $display .= "$message2";
  } else {
    $display .= "";
  }
        $display .= "
      </td>
        </tr>";
// $display .= "<tr><td colspan=\"8\">Customer Quantity: $total_pr Customer subtotal: \$".number_format($subtotal_pr,2)."</td></tr>";
      }



    $sqla = mysql_query("select transaction_name,transaction_comments,transaction_amount from transactions t, transactions_types tt
      where transaction_delivery_id = '".$delivery_id."' AND transaction_producer_id = '".$producer_id."'
      and t.transaction_type = tt.ttype_id and tt.ttype_parent = '20' and t.transaction_taxed='1'");

    while($resulta = mysql_fetch_array($sqla)){
    $display .= "
      <tr><td colspan=8><strong>Adjustments</strong></td></tr>
      <tr align=\"center\">
          <td align='left' valign=\"top\" colspan='2'>
      ".$resulta['transaction_name']."</td>
          <td align=\"left\" valign=\"top\" colspan='4'>
            ".$resulta['transaction_comments']."</td>

          <td align=\"right\" valign=\"top\">
            \$".number_format($resulta['transaction_amount'], 2)."</td>
            <td align=\"center\" valign=\"top\"></td></tr>";

      $subtotal_pr = $subtotal_pr+$resulta['transaction_amount'];
      $total = $total+$resulta['transaction_amount'];
    }

  $producer_invoice = array(
    'a_business_name'=>$a_business_name,
    'total'=>$total,
    'message_incomplete'=>$message_incomplete,
    'display'=>$display);

  return $producer_invoice;
  }
}