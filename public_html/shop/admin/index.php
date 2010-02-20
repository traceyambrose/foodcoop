<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

$sqldd = '
  SELECT *
  FROM
    '.TABLE_CURDEL;
$rs = @mysql_query($sqldd,$connection) or die("Couldn't execute query.");
while ( $row = mysql_fetch_array($rs) )
  {
    $current_delivery_id = $row['delivery_id'];
    $delivery_date = $row['delivery_date'];
    $order_cycle_closed = $row['order_cycle_closed'];
  }
session_register("current_delivery_id");
include("../func/convert_delivery_date.php");
$current_delivery_date = $delivery_date;
session_register("current_delivery_date");
session_register("order_cycle_closed");
$sqlm = '
  SELECT auth_type,
    username_m,
    member_id,
    first_name,
    first_name_2,
    last_name,
    last_name_2,
    business_name
  FROM
    '.TABLE_MEMBER.'
  WHERE
    username_m = "'.$valid_c.'"';
$result = @mysql_query($sqlm, $connection) or die("Couldn't execute query -m.");
while ( $row = mysql_fetch_array($result) )
  {
    $admin_member_id = $row['member_id'];
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $first_name_2 = $row['first_name_2'];
    $last_name_2 = $row['last_name_2'];
    $business_name = stripslashes ($row['business_name']);
    $auth_type = $row['auth_type'];
  }
include("../func/show_name.php");

// Include messages from the localfoodcoop.org server about this version

$curl = curl_init();
curl_setopt ($curl, CURLOPT_URL,'www.localfoodcoop.org/updates/messages.php?version='.CURRENT_VERSION);
curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, 5);
$display_admin .= curl_exec($curl);
curl_close($curl);

session_register("show_name");
session_register("admin_member_id");
session_register("auth_type");
$display_admin .= '
  <span class="large">Administrators</span>
  <div class="menuBox2">
  <table width="100%" class="compact">
    <tr valign="top">
      <td align="left" width="50%">
        <img src="grfx/bottom.png" width="32" height="32" align="left" hspace="2" alt="Membership Information"><br />
        <b>Membership Information</b>
        <ul style="list-style-image: url(./grfx/nav_spacer_4dots.gif);">
          <li><a href="member_interface.php?action=find">Find/Edit Members</a><br /><br /></li>
          <li><a href="pending_members_list.php">Pending and Unpaid Members</a></li>
          <br /><br />
          <li><a href="members_list.php">Membership List (Full Info)</a></li>
          <br /><br />
          <li><a href="members_list_email.php">Member Email Addresses</a></li>
          <li> <a href="members_list_withemail.php">Members who have email</a></li>
          <li> <a href="members_list_noemail.php">Members without email</a></li>
          <br /><br />
          <li><a href="producers_pending.php">Pending Producers</a></li>
          <br /><br />
          <li> <a href="producers_labels.php">ALL Producers</a></li>
          <br /><br />
          <li><a href="prdcr_list_email.php">Producer Email Addresses</a></li>
          <li><a href="coopproducers.php">Producer Contact Info</a></li>
          <br /><br />
          <li> <a href="report_members.php?p=1">Download a Spreadsheet of All Members/Producers</a></li>
        </ul>
        <img src="grfx/acroread.png" width="32" height="32" align="left" hspace="2" alt="Generate PDFs"><br />
        <b>Generate PDFs</b>
        <ul style="list-style-image: url(./grfx/nav_spacer_4dots.gif);">
          <li> [see members area]</li>
        </ul>
        <img src="grfx/gnome2.png" width="32" height="32" align="left" hspace="2" alt="Generate PDFs"><br />
        <b>Hubs, Routes and Delivery Information</b>
        <ul style="list-style-image: url(./grfx/nav_spacer_4dots.gif);">
          <li><a href="delivery.php">Current Cycle&#146;s Route List: Deliveries and Pickups</a></li>
          <li><a href="delivery_editroute.php">Edit Route Info</a></li>
        </ul>
        <img src="grfx/admin.png" width="32" height="32" align="left" hspace="2" alt="Admin Maintenance"><br />
        <b>Admin Maintenance</b>
        <ul style="list-style-image: url(./grfx/nav_spacer_4dots.gif);">
          <!-- <li><a href="invoice_max.php">Update Invoice Max Table</a></li> -->
          <li><a href="orders_nomatch.php">Matching baskets</a></li>
          <li><a href="category_list_edit.php">Edit Categories and Subcategories</a></li>
          <li><a href="prep_cycle.php">Prep Cycle</a> (be very careful with this function!)</li>
        </ul>
      </td>
      <td align="left" width="50%">
        <img src="grfx/launch.png" width="32" height="32" align="left" hspace="2" alt="Current Delivery Cycle Functions"><br />
        <b>Current Delivery Cycle Functions</b>
        <ul style="list-style-image: url(./grfx/nav_spacer_4dots.gif);">
          <li><a href="orders_selectmember.php">Open an Order for a Customer</a></li>
          <li><a href="orders_list.php?delivery_id='.$current_delivery_id.'">Members with orders this cycle</a></li>
          <li><a href="orders_list_withtotals.php?delivery_id='.$current_delivery_id.'">Members with orders this cycle (with totals)</a></li>
          <li><a href="members_list_emailorders.php?delivery_id='.$current_delivery_id.'">Customer Email Addresses this cycle</a></li>
          <li><a href="orders_prdcr_list.php?delivery_id='.$current_delivery_id.'">Producers with Customers this Cycle</li>
          <li><a href="query_notes.php">Orders with Customer Notes</a></li>
          <br /><br />
          <li><a href="printprod_new.php">New Products</a></li>
          <li><a href="printprod_changed.php">Changed Products</a></li>
          <li><a href="printprod_deleted.php">Unlisted Products</a></li>
          <li><a href="printprod_list_all.php">Full Product List</a></li>
          <br /><br />
          <li><a href="invoice_edittext.php">Edit Invoice Messages</a></li>';
$display_admin .= '
          </ul>
          <img src="grfx/kcron.png" width="32" height="32" align="left" hspace="2" alt="Previous Delivery Cycle Functions"><br />
          <b>Previous Delivery Cycle Functions</b>
          <ul style="list-style-image: url(./grfx/nav_spacer_4dots.gif);">
            <li><a href="orders_saved.php">Past Customer Invoices</a></li>
            <li><a href="orders_saved2.php">Past Producer Invoices</a></li>
          </ul>
          <img src="grfx/kspread.png" width="32" height="32" align="left" hspace="2" alt="Treasurer Functions"><br />
          <b>Treasurer Functions</b>
          <ul style="list-style-image: url(./grfx/nav_spacer_4dots.gif);">
            <li><a href="finalizep.php?delivery_id='.$current_delivery_id.'">Finalize Producer Invoices</a></li>
            <li><a href="unfinalized.php">All Previous Unfinalized Invoices</a></li>
          </ul>
          <img src="grfx/kchart.png" width="32" height="32" align="left" hspace="2" alt="Reports"><br />
          <b>Reports</b>
          <ul style="list-style-image: url(./grfx/nav_spacer_4dots.gif);">
            <li><a href="report_financial.php">Financial Report</a></li>
            <li><a href="transaction_report.php">Transaction Report</a></li>
            <br /><br />
            <!--<li><a href="members_history.php">Member Account Balances</a></li>-->
            <li><a href="member_balances_lookup.php">Member Balances Look-up</a></li>
            <li><a href="member_balances_outstanding.php">Member Balances Outstanding</a> (slow)</li>
            <!--<li> [revamping] Aging (30/60/90)</li>-->
            <br /><br />
            <li><a href="salestax.php">Sales Tax Breakdown</a></li>
            <li><a href="orders_perhub.php">Orders and Sales per Hub</a></li>
            <br /><br />
            <li><a href="report.php">Sales Reports</a></li>
            <li><a href="totals_saved.php?delivery_id='.$current_delivery_id.'">Customer Totals Report</a></li>
          </ul>
        </td>
      </tr>
    </table>
    </div>';
$display_route = '
  <span class="large">Route Managers</span>
  <div class="menuBox2 compact" align="left">
  <table width="100%" class="compact">
   <tr valign="top">
      <td align="left" width="50%">
        <img src="grfx/gnome2.png" width="32" height="32" align="left" hspace="2" alt="Route Information"><br />
        <b>Route Information</b>
        <ul style="list-style-image: url(./grfx/nav_spacer_4dots.gif);">
          <li><a href="delivery.php">Current Cycle&#146;s Route List: Deliveries and Pickups</a></li>
          <li><a href="delivery_editroute.php">Edit Route Info</a></li>
          <li><a href="delcode_activation.php">Activate Delivery Locations</a></li>
        </ul>
      </td>
      <td align="left" width="50%">
        <img src="grfx/bottom.png" width="32" height="32" align="left" hspace="2" alt="Members on your Route"><br />
        <b>Members on your Route</b>
        <ul style="list-style-image: url(./grfx/nav_spacer_4dots.gif);">
          <li><a href="delivery_zip.php">Members on Your Route</a></li>
        </ul>
      </td>
    </tr>
  </table>
  </div>';
$display_admin2 = '
  <span class="large">Cashiers</span>
  <div class="menuBox2 compact" align="left">
  <table width="100%" class="compact">
    <tr valign="top">
      <td align="left" width="50%">
        <img src="grfx/ksirc.png" width="32" height="32" align="left" hspace="2" alt="Helpful PDF Forms for Download"><br />
        <b>Helpful PDF Forms for Download</b>
        <ul style="list-style-image: url(./grfx/nav_spacer_4dots.gif);">
          <li><a href="pdf/payments_received.pdf" target="_blank">Payments Received Form</a></li>
          <li><a href="pdf/invoice_adjustments.pdf" target="_blank">Invoice Adjustments Chart</a></li>
        </ul>
      </td>
      <td align="left" width="50%">
        <img src="grfx/kspread.png" width="32" height="32" align="left" hspace="2" alt="Cashier and Adjustment Information"><br />
        <b>Cashier and Adjustment Information</b>
        <ul style="list-style-image: url(./grfx/nav_spacer_4dots.gif);">
          <li><a href="adjustments.php">Invoice Adjustments</a></li>
          <li><a href="ctotals_onebutton.php?delivery_id='.$current_delivery_id.'">Receive Payments</a>
          </li>
        </ul>
      </td>
    </tr>
  </table>
  </div>';
  $sqla = '
    SELECT
      rtemgr_member_id,
      admin
    FROM
      '.TABLE_ROUTE.'
    WHERE
      rtemgr_member_id = "'.$admin_member_id.'"
      AND admin != "1"';
$resulta = @mysql_query($sqla, $connection) or die("Couldn't execute query -a.");
$rt_num = mysql_numrows($resulta);
// If auth_type is not the administrator and there is a route number, then display route info only
if ( $rt_num && strpos ($_SESSION['auth_type'], 'administrator') === false )
  {
    $display = $display_route;
  }
// Otherwise display all info.
else
  {
    $display = $display_admin;
    $display .= $display_route;
    $display .= $display_admin2;
  }
$date_today = date("F j, Y");
$sql = '
  SELECT '.TABLE_PRODUCT.'.product_id,
    '.TABLE_PRODUCT.'.donotlist,
    '.TABLE_PRODUCT.'.producer_id,
    '.TABLE_PRODUCER.'.producer_id,
    '.TABLE_PRODUCER.'.donotlist_producer
  FROM
    '.TABLE_PRODUCT.',
    '.TABLE_PRODUCER.'
    WHERE
    '.TABLE_PRODUCT.'.donotlist != "1"
    AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
    AND '.TABLE_PRODUCER.'.donotlist_producer != "1"
    GROUP BY product_id';
$result = @mysql_query($sql,$connection) or die("Couldn't execute count query.");
$prod_count = mysql_numrows($result);
?>
<?php include("template_hdr.php");?>
<div align="center">
  <b>Welcome <?php echo $show_name;?> to the Administrative Area!</b><br><br>
  <b>As of <?php echo $date_today;?>, there are <?php echo $prod_count;?> Products Available through the <?php echo ORGANIZATION_TYPE; ?></b><br>
  <b>Ordering Closes: <font color="#770000"><?php echo $order_cycle_closed;?></font></b>
  <br><br>
  <div id="yellowWrapper" align="center">
    <div style="width: 98%;">
      <?php echo $display;?>
    </div>
  </div>
</div>
<br><br>
  <!-- CONTENT ENDS HERE -->
<?php include("template_footer.php"); ?>
