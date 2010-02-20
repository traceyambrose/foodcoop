<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
include_once ('general_functions.php');
session_start();
validate_user();
$date_today = date("F j, Y");

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
    '.TABLE_CATEGORY.'.*,
    '.TABLE_SUBCATEGORY.'.*,
    '.TABLE_PRODUCT.'.*
  FROM
    '.TABLE_CATEGORY.',
    '.TABLE_SUBCATEGORY.',
    '.TABLE_PRODUCT.',
    '.TABLE_PRODUCER.'
  WHERE
    '.TABLE_CATEGORY.'.category_id = '.TABLE_SUBCATEGORY.'.category_id
    AND '.TABLE_SUBCATEGORY.'.subcategory_id = '.TABLE_PRODUCT.'.subcategory_id
    AND '.TABLE_PRODUCT.'.producer_id = "'.$producer_id.'"
    '.$donotlist_condition.'
    AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
    AND '.TABLE_PRODUCER.'.pending = 0
    AND '.TABLE_PRODUCER.'.donotlist_producer = 0
  ORDER BY
    '.TABLE_CATEGORY.'.category_name ASC,
    '.TABLE_SUBCATEGORY.'.subcategory_name ASC';
$rs = @mysql_query($sql,$connection) or die("Couldn't execute category query.".$sql);
while ( $row = mysql_fetch_array($rs) )
  {
    $category_id = $row['category_id'];
    $category_name = stripslashes($row['category_name']);
    $subcategory_id = $row['subcategory_id'];
    $subcategory_name = stripslashes($row['subcategory_name']);

    if ( $current_subcategory_id < 0 )
      {
        $current_subcategory_id = $row['subcategory_id'];
      }
    while ( $current_subcategory_id != $subcategory_id )
      {
        $current_subcategory_id = $subcategory_id;

        $display .= "<div align=\"right\"><font size=\"-1\">
          [ <a href=\"index.php\">Return to main page</a> |
          <a href=\"orders_current.php\">View Shopping Cart</a> |
          <a href=\"logout.php\">Logout</a> ]</font></div>";
        $display .= "$current_subtotal";

        $display .= "<h2><font color=\"#770000\">$category_name: $subcategory_name</font></h2>";

        $display .= "<table border=1 cellpadding=5 cellspacing=0 bordercolor=#DDDDDD>";
        $display .= "<tr>";
        $display .= "<th align=center bgcolor=#DDDDDD>$font Order</font></th>";
        $display .= "<th align=center bgcolor=#DDDDDD>$font ID</font></th>";
        $display .= "<th align=center bgcolor=#DDDDDD>$font Product Name [<a href='".BASE_URL.PATH.'producers/'.strtolower($producer_id).".php'>About Producer</a>]</font></th>";
        $display .= "<th align=center bgcolor=#DDDDDD>$font Type</font></th>";
        $display .= "<th align=center bgcolor=#DDDDDD>$font Price</font></th>";
        $display .= "</tr>";

        $sql = '
          SELECT
            '.TABLE_PRODUCT.'.*,
            '.TABLE_PRODUCT_TYPES.'.*
          FROM
            '.TABLE_PRODUCT.',
            '.TABLE_PRODUCT_TYPES.',
            '.TABLE_PRODUCER.'
          WHERE
            '.TABLE_PRODUCT.'.subcategory_id = '.$subcategory_id.'
            AND '.TABLE_PRODUCT.'.producer_id = "'.$producer_id.'"
            AND '.TABLE_PRODUCT.'.prodtype_id = '.TABLE_PRODUCT_TYPES.'.prodtype_id
            '.$donotlist_condition.'
            AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
            AND '.TABLE_PRODUCER.'.pending = 0
            AND '.TABLE_PRODUCER.'.donotlist_producer = 0
          ORDER BY
            product_name ASC,
            unit_price ASC';
        $result = @mysql_query($sql,$connection) or die("<pre>Couldn't execute search query.".$sql);

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
      }
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
    '.TABLE_PRODUCER.',
    '.TABLE_MEMBER.'
  WHERE
    '.TABLE_PRODUCER.'.producer_id = "'.$producer_id.'"
    AND '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
    AND '.TABLE_PRODUCER.'.pending = 0
    AND '.TABLE_PRODUCER.'.donotlist_producer = 0';
$resultp = @mysql_query($sqlp,$connection) or die("Couldn't execute search query 2.");
while ( $row = mysql_fetch_array($resultp) )
  {
    $business_name = stripslashes($row['business_name']);
    $first_name = stripslashes($row['first_name']);
    $last_name = stripslashes($row['last_name']);
    if ( ! $business_name )
      {
        $business_name = "$first_name $last_name";
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
  <tr>
    <td align="left"><?php echo $font;?>

      <h2><font color="#770000"><?php echo stripslashes($business_name);?> Product List</font></h2>

      <?php
      include("../func/display_producer_page.php");
      echo prdcr_info($producer_id);?>

      <a name="products"></a>
      <?php echo $display;?>

      <?php
        if ( ! $num )
          {
          echo "<br>No products found for this Producer at this time.";
          }
      ?>
    </td>
  </tr>
</table>
</div>
  <!-- CONTENT ENDS HERE -->

<?php include("template_footer_orders.php");?>