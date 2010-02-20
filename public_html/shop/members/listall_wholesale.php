<?php
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
/* determine the sort order */
if (isset($_GET['order']) && $_GET['order'] != "")
  {
    /* if the same order_by, switch the direction */
    //if ($_GET['order'] == $order_by) {
    if (isset($_GET['dir']) && $_GET['dir'] != "")
      {
        $order_direction = htmlentities($_GET['dir']);
        if($order_direction == 'ASC')
          {
            $reverse_direction = 'DESC';
          }
        else if ($order_direction == 'DESC')
          {
            $reverse_direction = 'ASC';
          }
        else
          {
            $reverse_direction = 'ASC';
          }
      }
    //} else echo 'orders do not match <br/>';
    $order_by = htmlentities($_GET['order']);
    $sorted_by = htmlentities($_GET['order']);
    switch ($order_by)
      {
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
//    '.TABLE_PRODUCER.'.producer_id,
//    '.TABLE_PRODUCER.'.donotlist_producer,
$step='querying';
$sql = '
  SELECT
    '.TABLE_PRODUCT.'.* ,
    '.TABLE_PRODUCT_TYPES.'.*,
    '.TABLE_PRODUCER.'.*,
    '.TABLE_MEMBER.'.*
  FROM
    '.TABLE_PRODUCT.',
    '.TABLE_PRODUCER.',
    '.TABLE_PRODUCT_TYPES.',
    '.TABLE_MEMBER.'
  WHERE
    '.TABLE_PRODUCT.'.donotlist = 3
    AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
    AND '.TABLE_PRODUCER.'.pending = 0
    AND '.TABLE_PRODUCER.'.donotlist_producer = 0
    AND '.TABLE_PRODUCT.'.prodtype_id = '.TABLE_PRODUCT_TYPES.'.prodtype_id
    AND '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
  GROUP BY
    product_id
  ORDER BY
    '.$order_by.' '.$order_direction.'';
$result = @mysql_query($sql,$connection) or die("Couldn't execute search query.");

$num = mysql_numrows($result);
$step='looping on query results';
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
/*
    $sqlp = '
      SELECT
        *
      FROM
        '.TABLE_PRODUCT.',
        '.TABLE_PRODUCER.',
        '.TABLE_MEMBER.',
        '.TABLE_PRODUCT_TYPES.'
      WHERE
        '.TABLE_PRODUCT.'.product_id = '.$product_id.'
        AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
        AND '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
      GROUP BY
        product_id';
    $resultp = @mysql_query($sqlp,$connection) or die("Couldn't execute search query 2.");
    while ( $row = mysql_fetch_array($resultp) )
      {*/
        $business_name = stripslashes ($row['business_name']);
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $prodtype = $row['prodtype'];
        $producer_id = $row['producer_id'];
        $show_business_link = true;
        include("../func/show_product_info_members.php");

    //     }
  }

?>

<?php include("template_hdr_orders.php");?>

<script type="text/javascript" src="/shop/ajax/jquery.js"></script>
<script type="text/javascript" language="javascript">
var new_window = null; function create_window(w,h,url)
  {
    var options = "width=" + w + ",height=" + h + ",status=no";
    new_window = window.open(url, "new_window", options); return false;
  }
</script>

<script type="text/javascript" language="javascript">

var xmlhttp;

function updateProductList()
  {
    xmlhttp=GetXmlHttpObject();
    if (xmlhttp==null)
      {
        alert ("Browser does not support HTTP Request");
        return;
      }

    var url="../ajax/product_list_query.php";
    $order_by = document.getElementById("orderBy").value;
    //get radio button values
    var radioNames = [ 'sortASC', 'sortDESC' ];
    for (var i = 0; i <= 1; i++)
      {
        var radioValue = document.getElementById(radioNames[i]);
        if ( radioValue.checked )
          {
            $sort = radioValue.value;
          }
      }
    url=url+"?order="+$order_by+"&sort="+$sort;
    url=url+"&sid="+Math.random();
    xmlhttp.onreadystatechange=stateChanged;
    xmlhttp.open("GET",url,true);
    xmlhttp.send(null);
  }

function stateChanged()
  {
    if (xmlhttp.readyState==4)
      {
        document.getElementById("show_products").innerHTML=xmlhttp.responseText;
      }
  }

function GetXmlHttpObject()
  {
    if (window.XMLHttpRequest)
      {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        return new XMLHttpRequest();
      }
    if (window.ActiveXObject)
      {
        // code for IE6, IE5
        return new ActiveXObject("Microsoft.XMLHTTP");
      }
    return null;
  }
//Auto load the list
$(document).ready(updateProductList);
</script>
  <!-- CONTENT BEGINS HERE -->

<div align="center">
  <table width="90%">
    <tr>
      <td align="left">
        <h3>Wholesale Product List: <? echo $num; ?> Products Found</h3>
<?php
try
  {
    $step='get shopping cart info';
    echo "<div align=\"right\"><font size=\"-1\">
    [ <a href=\"index.php\">Return to main page</a> |
    <a href=\"orders_current.php\">View Shopping Cart</a> |
    <a href=\"logout.php\">Logout</a> ]</font></div>";
    echo "$current_subtotal";
  }
catch (Exception $e)
  {
    //echo 'Caught exception: ',  $e->getMessage(), "\n", "while ", $step;
  }

?>
        <hr/>
        <form onsubmit="return false;">
          <table>
            <tr>
              <td>
                Sort Wholesale Products By:
                <select name="orderBy" id="orderBy" onchange="updateProductList()">
                  <option value="product_id">Product ID</option>
                  <option value="business_name">Producer</option>
                  <option value="product_name">Product Name</option>
                  <option value="prodtype">Product Type</option>
                  <option value="subcategory_name" selected>Product Category</option>
                  <option value="unit_price">Price</option>
                </select>
              </td>
              <td>
                <input type="radio" name="sort" id="sortASC" value="ASC" checked onclick="updateProductList()" />Ascending Order (A-Z, 1-10)<br/>
                <input type="radio" name="sort" id="sortDESC" value="DESC" onclick="updateProductList()"  />Descending Order (Z-A, 10-1)
              </td>
              <td>
                <input type="button" value="Refresh" onclick="updateProductList()" />
              </td>
            </tr>
          </table>
        </form>
        <hr/>
        <div id="show_products">Select a sort order above</div>

<?php
/*?>
        <table border=1 cellpadding=5 cellspacing=0 bordercolor=#DDDDDD>
          <tr>
            <th align="center" bgcolor="#DDDDDD" width="10%"><?php echo $font;?>Order</font></th>
        <th align="center" bgcolor="#DDDDDD"><?php echo $font;?><a href="listall_wholesale.php?order=id&dir=<?php echo $reverse_direction?>">ID</a></font></th>
            <th align="center" bgcolor="#DDDDDD"><?php echo $font;?><a href="listall_wholesale.php?order=product&dir=<?php echo $reverse_direction?>">Product Name</a></font></th>
            <th align="center" bgcolor="#DDDDDD"><?php echo $font;?><a href="listall_wholesale.php?order=producer&dir=<?php echo $reverse_direction?>">Producer</a></th>
            <th align="center" bgcolor="#DDDDDD"><?php echo $font;?><a href="listall_wholesale.php?order=type&dir=<?php echo $reverse_direction?>">Type</a></th>
            <th align="center" bgcolor="#DDDDDD"><?php echo $font;?><a href="listall_wholesale.php?order=price&dir=<?php echo $reverse_direction?>">Price</a></font></th>
          </tr>

          <?php echo $display;?>

        </table>
        <?php */
?>
      </td>
    </tr>
  </table>
</div>
  <!-- CONTENT ENDS HERE -->

<?php
include("template_footer_orders.php");
/*
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n", "while ", $step;

}*/
?>
 