<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();
$date_today = date("F j, Y");

include('general_functions.php');

if ( $add == "tocart" )
  {
    include("../func/addtocart.php");
    $current_subtotal = "<div align=\"right\"><font size=\"-1\">
    <b>Current Subtotal: \$".number_format($total, 2)."</b></font></div>";
  }

// Get the time until the order closes
$seconds_until_close = strtotime ($_SESSION['closing_timestamp']) - time();
// Set up the "donotlist" field condition based on whether the member is an "institution" or not
// Only institutions are allowed to see donotlist=3 (wholesale products)
if (strpos ($_SESSION['auth_type'], 'institution') !== false && $seconds_until_close < INSTITUTION_WINDOW)
  {
    $donotlist_condition = 'AND ('.TABLE_PRODUCT.'.donotlist = "0" OR '.TABLE_PRODUCT.'.donotlist = "3")';
  }
else
  {
    $donotlist_condition = 'AND '.TABLE_PRODUCT.'.donotlist = "0"';
  }

$sql = '
  SELECT
    '.TABLE_PRODUCT.'.* ,
    '.TABLE_PRODUCER.'.producer_id,
    '.TABLE_PRODUCER.'.donotlist_producer
  FROM
    '.TABLE_PRODUCT.',
    '.TABLE_PRODUCER.'
  WHERE
    '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
    '.$donotlist_condition.'
    AND '.TABLE_PRODUCER.'.pending = 0
    AND '.TABLE_PRODUCER.'.donotlist_producer = 0
  GROUP BY
    product_id
  ORDER BY
    product_id ASC';

$result = @mysql_query($sql,$connection) or die("Couldn't execute search query.");

$num = mysql_numrows($result);

while ( $row = mysql_fetch_array($result) )
  {
    $product_id = $row['product_id'];
    $product_name = $row['product_name'];
    $inventory_on = $row['inventory_on'];
    $inventory = $row['inventory'];
    $unit_price = $row['unit_price'];
    $pricing_unit = $row['pricing_unit'];
    $ordering_unit = $row['ordering_unit'];
    $prodtype_id = $row['prodtype_id'];
    $prodtype = $row['prodtype'];
    $random_weight = $row['random_weight'];
    $minimum_weight = $row['minimum_weight'];
    $maximum_weight = $row['maximum_weight'];
    $meat_weight_type = $row['meat_weight_type'];
    $extra_charge = $row['extra_charge'];
    $image_id = $row['image_id'];
    $donotlist = $row['donotlist'];
    $detailed_notes = $row['detailed_notes'];


    $sqlp = '
      SELECT
        '.TABLE_PRODUCT.'.producer_id,
        '.TABLE_PRODUCER.'.producer_id,
        '.TABLE_PRODUCT_TYPES.'.prodtype,
        '.TABLE_PRODUCER.'.member_id,
        '.TABLE_MEMBER.'.member_id,
        '.TABLE_MEMBER.'.business_name,
        '.TABLE_MEMBER.'.first_name,
        '.TABLE_MEMBER.'.last_name
      FROM
        '.TABLE_PRODUCT.',
        '.TABLE_PRODUCER.',
        '.TABLE_MEMBER.',
        '.TABLE_PRODUCT_TYPES.'
      WHERE
        '.TABLE_PRODUCT.'.product_id = "'.$product_id.'"
        AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
        AND '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
        AND '.TABLE_PRODUCT_TYPES.'.prodtype_id = "'.$prodtype_id.'"';
    $resultp = @mysql_query($sqlp,$connection) or die("Couldn't execute search query 2.");
    while ( $row = mysql_fetch_array($resultp) )
      {
        $business_name = stripslashes($row['business_name']);
        $first_name = stripslashes($row['first_name']);
        $last_name = stripslashes($row['last_name']);
        $prodtype = $row['prodtype'];
        $producer_id = $row['producer_id'];
        $show_business_link = true; // Set this to display the column with the producer name
        include("../func/show_product_info_members.php");
      }
  }

?>

<?php include("template_hdr_orders.php");?>

<script type="text/javascript" language="javascript">
var new_window = null; function create_window(w,h,url) {
var options = "width=" + w + ",height=" + h + ",status=no";
new_window = window.open(url, "new_window", options); return false; }
</script>

  <!-- CONTENT BEGINS HERE -->

<div align="center">
<table width="90%">
  <tr><td align="left">

<h3>Full Product List: Sorted by Product ID</h3>

<?php echo $num;?> Entries Found
<br><br>

<?php
      echo "<div align=\"right\"><font size=\"-1\">
    [ <a href=\"index.php\">Return to main page</a> |
    <a href=\"orders_current.php\">View Shopping Cart</a> |
    <a href=\"logout.php\">Logout</a> ]</font></div>";
      echo "$current_subtotal";
?>

<table border="1" cellpadding="5" cellspacing="0" bordercolor="#DDDDDD" width="100%">
  <tr>
    <th align="center" bgcolor="#DDDDDD" width="10%"><?php echo $font;?>Order</font></th>
    <th align="center" bgcolor="#DDDDDD" width="10%"><?php echo $font;?>ID</font></th>
    <th align="center" bgcolor="#DDDDDD" width="45%"><?php echo $font;?>Product Name</font></th>
    <th align="center" bgcolor="#DDDDDD" width="10%"><?php echo $font;?>Producer</th>
    <th align="center" bgcolor="#DDDDDD" width="10%"><?php echo $font;?>Type</th>
    <th align="center" bgcolor="#DDDDDD" width="15%"><?php echo $font;?>Price</font></th>
  </tr>

<?php echo $display;?>

</table>


  </td></tr>
</table>
</div>
  <!-- CONTENT ENDS HERE -->

<?php include("template_footer_orders.php");?>