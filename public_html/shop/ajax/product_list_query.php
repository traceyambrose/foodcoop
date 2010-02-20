<?php

include_once ("config_foodcoop.php");

$response = '';

$sort_order = $_GET["q"];

/*function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");

try {
  */
$step='setting session';

$user_type = 'valid_m';
include_once('config_foodcoop.php');
session_start();
validate_user();
$date_today = date("F j, Y");

include_once('general_functions.php');
/* initialize some variables to get ride of some errors */
$display='';
$message="";


$step='if add==tocart';
if(isset($add))
  {
    if ( $add == "tocart" )
      {
        include("../func/addtocart.php");
        $current_subtotal = "<div align=\"right\"><font size=\"-1\">
        <b>Current Subtotal: \$".number_format($total, 2)."</b></font></div>";
      }
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


/* some variables with default values to handle sorting the table */
$order_by = 'product_id';
$sorted_by = 'id';
$order_direction = 'ASC';
$reverse_direction = 'DESC';

$step='determining sort order';
if (isset($_GET['order']) && $_GET['order'] != "")
  {
    $order_by = htmlentities($_GET['order']);
  }
if (isset($_GET['sort']) && $_GET['sort'] != "")
  {
    $order_direction = htmlentities($_GET['sort']);
  }
/* determine the sort order
if (isset($_GET['order']) && $_GET['order'] != "") {
  /* if the same order_by, switch the direction

    if (isset($_GET['dir']) && $_GET['dir'] != "") {
      $order_direction = htmlentities($_GET['dir']);
      if($order_direction == 'ASC') {
        $reverse_direction = 'DESC';
      } else if ($order_direction == 'DESC') {
        $reverse_direction = 'ASC';
      } else {
        $reverse_direction = 'ASC';
      }
    }
  $order_by = htmlentities($_GET['order']);
  $sorted_by = htmlentities($_GET['order']);
  switch ($order_by){
    case 'id':
      $order_by = 'product_id';
      break;
    case 'producer':
      $order_by = 'business_name';
      break;
    case 'product':
      $order_by = 'product_name';
      break;
    case 'price':
      $order_by = 'unit_price';
      break;
    case 'type':
      $order_by = 'prodtype';
      break;
  }
}
*/
$step='querying';
$sql = '
  SELECT
    '.TABLE_PRODUCT.'.* ,
    '.TABLE_PRODUCT_TYPES.'.*,
    '.TABLE_PRODUCER.'.*,
    '.TABLE_MEMBER.'.*,
    '.TABLE_SUBCATEGORY.'.*
  FROM
    '.TABLE_PRODUCT.',
    '.TABLE_PRODUCER.',
    '.TABLE_PRODUCT_TYPES.',
    '.TABLE_MEMBER.',
    '.TABLE_SUBCATEGORY.'
  WHERE
    '.TABLE_PRODUCT.'.donotlist = 3
    AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
    AND '.TABLE_PRODUCER.'.pending = 0
    AND '.TABLE_PRODUCER.'.donotlist_producer = 0
    AND '.TABLE_PRODUCT.'.prodtype_id = '.TABLE_PRODUCT_TYPES.'.prodtype_id
    AND '.TABLE_PRODUCT.'.subcategory_id = '.TABLE_SUBCATEGORY.'.subcategory_id
    AND '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
  GROUP BY
    product_id
  ORDER BY
    '.$order_by.' '.$order_direction.'';
$result = @mysql_query($sql,$connection) or die("Couldn't execute search query.");

$num = mysql_numrows($result);
$step='looping on query results';
if($order_by=="subcategory_name")
  {
    $set=array();
    while ( $record = mysql_fetch_object($result) )
      {
        $set[$record->subcategory_name][] = $record;
      }
    foreach ($set as $category => $records)
      {
        $display = $display . '<tr><td colspan=6><h3>' . ${category} . '</h3></td></tr>';
        foreach ($records as $record)
          {
            $product_id = $record->product_id;
            $product_name = $record->product_name;
            $inventory_on = $record->inventory_on;
            $inventory = $record->inventory;
            $unit_price = $record->unit_price;
            $pricing_unit = $record->pricing_unit;
            $ordering_unit = $record->ordering_unit;
            $prodtype_id = $record->prodtype_id;
            $prodtype = $record->prodtype;
            $random_weight = $record->random_weight;
            $minimum_weight = $record->minimum_weight;
            $maximum_weight = $record->maximum_weight;
            $meat_weight_type = $record->meat_weight_type;
            $extra_charge = $record->extra_charge;
            $image_id = $record->image_id;
            $donotlist = $record->donotlist;
            $detailed_notes = $record->detailed_notes;
            $subcategory_id = $record->subcategory_id;
            $subcategory_name = $record->subcategory_name;
            $business_name = stripslashes ($record->business_name);
            $first_name = $record->first_name;
            $last_name = $record->last_name;
        //    $prodtype = $record->prodtype;
            $producer_id = $record->producer_id;
            $show_business_link = true;
            // The next line is a **BAD** workaround and means this ajax script can only be used
            // for /shop/members/listall_wholesale.php successfully.  But without this piece
            // the /shop/func/show_product_info_members.php is confused about where to return
            // control on adding products.
            $PHP_SELF = PATH.'members/listall_wholesale.php';
            include("../func/show_product_info_members.php");
          }
      }
  }
else
  {
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
        $subcategory_id = $row['subcategory_id'];
        $subcategory_name = $row['subcategory_name'];
        $business_name = stripslashes ($row['business_name']);
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
    //    $prodtype = $row['prodtype'];
        $producer_id = $row['producer_id'];
        $show_business_link = true;
        include("../func/show_product_info_members.php");
      }
  }

$response .=  '
        <table border=1 cellpadding=5 cellspacing=0 bordercolor=#DDDDDD>
          <tr>
            <th align="center" bgcolor="#DDDDDD" width="10%"><?php echo $font;?>Order</font></th>
            <th align="center" bgcolor="#DDDDDD"><?php echo $font;?>ID</font></th>
            <th align="center" bgcolor="#DDDDDD"><?php echo $font;?>Product Name</font></th>
            <th align="center" bgcolor="#DDDDDD"><?php echo $font;?>Producer</th>
            <th align="center" bgcolor="#DDDDDD"><?php echo $font;?>Type</th>
            <th align="center" bgcolor="#DDDDDD"><?php echo $font;?>Price</font></th>
          </tr>';

$response .= $display;
$response .= '
        </table>';
echo $response;
?>
 