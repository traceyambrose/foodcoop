<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();

include_once ('general_functions.php');

//register_globals: $subcategory_id

if ( $add == "tocart" )
  {
    include("../func/addtocart.php");
    $current_subtotal = '
      <div align="right">
        <font size="-1"><b>Current Subtotal: $'.number_format($total, 2).'</b></font>
      </div>';
  }

$date_today = date("F j, Y");

$sql = '
  SELECT
    subcategory_name
  FROM
    '.TABLE_SUBCATEGORY.'
  WHERE
    subcategory_id = "'.$subcategory_id.'"';
$rs = @mysql_query($sql,$connection) or die("Couldn't execute category query.");
while ( $row = mysql_fetch_array($rs) )
  {
    $subcategory_name = $row['subcategory_name'];
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

$sqlp = '
  SELECT
    '.TABLE_PRODUCER.'.producer_id,
    '.TABLE_PRODUCER.'.member_id,
    '.TABLE_MEMBER.'.member_id,
    '.TABLE_PRODUCER.'.donotlist_producer,
    '.TABLE_MEMBER.'.business_name,
    '.TABLE_MEMBER.'.first_name,
    '.TABLE_MEMBER.'.last_name
  FROM
    '.TABLE_PRODUCT.',
    '.TABLE_PRODUCER.',
    '.TABLE_MEMBER.'
  WHERE
    '.TABLE_PRODUCT.'.subcategory_id = "'.$subcategory_id.'"
    AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
    AND '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
    '.$donotlist_condition.'
    AND '.TABLE_PRODUCER.'.pending = 0
    AND '.TABLE_PRODUCER.'.donotlist_producer = 0
  GROUP BY
    '.TABLE_PRODUCT.'.producer_id
  ORDER BY
    '.TABLE_MEMBER.'.business_name';
$resultp = @mysql_query($sqlp,$connection) or die("Couldn't execute search query 2.");
while ( $row = mysql_fetch_array($resultp) )
  {
    $producer_id = $row['producer_id'];
    $business_name = stripslashes($row['business_name']);
    $first_name = stripslashes($row['first_name']);
    $last_name = stripslashes($row['last_name']);

    if ( !$business_name ) {
      $business_name = "$first_name $last_name";
    }

    if ( $current_business_name < 0 )
      {
        $current_business_name = stripslashes ( $row['business_name'] );
      }

    $display .= '
      <div align="right">
        <font size="-1">
        [ <a href="index.php">Return to main page</a> |
        <a href="orders_current.php">View Shopping Cart</a> |
        <a href="logout.php">Logout</a> ]</font>
      </div>';

    while ( $current_business_name != $business_name )
      {
        $current_business_name = $business_name;
        $display .= "<font color=\"#770000\"><h2>$business_name</h2></font>";

        $display .= '<table border="1" cellpadding="5" cellspacing="0" bordercolor="#DDDDDD" bgcolor="#ffffff" width="100%">
        <tr>
          <th align=center bgcolor=#DDDDDD width="10%">'.$font.' Order</font></th>
          <th align=center bgcolor=#DDDDDD width="10%">'.$font.' ID</font></th>
          <th align=center bgcolor=#DDDDDD width="55%">'.$font.' Product Name [<a href="'.BASE_URL.PATH.'producers/'.strtolower($producer_id).'.php">About Producer</a>]</font></th>
          <th align=center bgcolor=#DDDDDD width="10%">'.$font.' Type</font></th>
          <th align=center bgcolor=#DDDDDD width="15%">'.$font.' Price</font></th>
        </tr>';

        $sql = '
          SELECT
            *
          FROM
            '.TABLE_PRODUCT.',
            '.TABLE_PRODUCT_TYPES.'
          WHERE
            '.TABLE_PRODUCT.'.subcategory_id = "'.$subcategory_id.'"
            AND '.TABLE_PRODUCT.'.producer_id = "'.$producer_id.'"
            AND '.TABLE_PRODUCT.'.prodtype_id = '.TABLE_PRODUCT_TYPES.'.prodtype_id
            '.$donotlist_condition.'
          ORDER BY
            product_name ASC,
            unit_price ASC';
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


            while ( $current_product_id != $product_id )
              {
                $current_product_id = $product_id;
                include("../func/show_product_info_members.php");
              }
          }
        $display .= "</table>";
        $display .= "$current_subtotal";
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
<table width="80%">
  <tr><td align="left">

<font color="#770000"><h2><u>Producers and Products in the <?php echo $subcategory_name;?> Section</u></h2></font>


<?php echo $display;?>

<?php
if (!$num) {
echo '<br>No products found for this Producer at this time.';
}
?>

  </td></tr>
</table>
</div>
  <!-- CONTENT ENDS HERE -->

<?php include("template_footer_orders.php");?>