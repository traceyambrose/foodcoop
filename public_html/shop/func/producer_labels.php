<?php
include_once ('config_foodcoop.php');
session_start();
// Figure out whether this is an admin or member session and validate accordingly (messy)
if ($_SESSION['valid_c'])
  {
    $user_type = 'valid_c';
  }
elseif ($_SESSION['valid_m'])
  {
    $user_type = 'valid_m';
  }
validate_user();

include_once ('general_functions.php');

// If we don't have a producer_id then get one from the arguments
if (! $producer_id) $producer_id = $_GET['producer_id'];
// If not administrator, then force producer to be the owner
if ( $producer_id_you && strpos ($_SESSION['auth_type'], 'administrator') === false )
  {
    $producer_id = $producer_id_you;
  }
// If no delivery id was passed, then use the current value
if ($_GET['delivery_id'])
  {
    $delivery_id = $_GET['delivery_id'];
  }
else
  {
    $delivery_id = $_SESSION['current_delivery_id'];
  }

$sqlp = '
  SELECT
    '.TABLE_PRODUCER.'.producer_id,
    '.TABLE_PRODUCER.'.member_id,
    '.TABLE_MEMBER.'.member_id,
    '.TABLE_MEMBER.'.business_name,
    '.TABLE_MEMBER.'.first_name,
    '.TABLE_MEMBER.'.last_name,
    '.TABLE_BASKET.'.product_id,
    '.TABLE_PRODUCT.'.producer_id,
    '.TABLE_PRODUCT.'.product_id
  FROM
    '.TABLE_PRODUCER.',
    '.TABLE_MEMBER.',
    '.TABLE_BASKET.',
    '.TABLE_PRODUCT.',
    '.TABLE_BASKET_ALL.'
  WHERE
    '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id
    AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
    AND '.TABLE_PRODUCER.'.producer_id = "'.$producer_id.'"
    AND '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
  GROUP BY
    '.TABLE_PRODUCER.'.producer_id
  ORDER BY
    business_name ASC,
    last_name ASC';
$resultp = @mysql_query($sqlp,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
$message2 = "<b><font color=\"#3333FF\">The information has been updated.</font></b><br><br>";
while ( $row = mysql_fetch_array($resultp) )
  {
    $a_business_name = stripslashes ($row['business_name']);
    $a_first_name = stripslashes ($row['first_name']);
    $a_last_name = stripslashes ($row['last_name']);

    if ( ! $a_business_name )
      {
        $a_business_name = "$a_first_name $a_last_name";
      }
  }

$sql = '
  SELECT
    '.TABLE_BASKET.'.basket_id,
    '.TABLE_BASKET.'.product_id,
    '.TABLE_BASKET.'.item_price,
    '.TABLE_BASKET_ALL.'.basket_id,
    '.TABLE_BASKET_ALL.'.delivery_id,
    '.TABLE_PRODUCT.'.product_id,
    '.TABLE_PRODUCER.'.producer_id,
    '.TABLE_PRODUCT.'.product_name,
    '.TABLE_PRODUCT.'.pricing_unit,
    '.TABLE_PRODUCT.'.detailed_notes
  FROM
    '.TABLE_BASKET.',
    '.TABLE_BASKET_ALL.',
    '.TABLE_PRODUCT.',
    '.TABLE_PRODUCER.'
  WHERE
    '.TABLE_BASKET_ALL.'.delivery_id = '.$delivery_id.'
    AND '.TABLE_BASKET.'.basket_id = '.TABLE_BASKET_ALL.'.basket_id
    AND '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id
    AND '.TABLE_PRODUCT.'.producer_id = "'.$producer_id.'"
    AND '.TABLE_PRODUCT.'.hidefrominvoice = 0
  GROUP BY
    '.TABLE_BASKET.'.product_id
  ORDER BY
    product_name ASC,
    item_date ASC';
$rs = @mysql_query($sql,$connection) or die(mysql_error());
while ( $row = mysql_fetch_array($rs) )
  {
    $basket_id = $row['basket_id'];
    $product_id = $row['product_id'];
    $product_name = stripslashes ($row['product_name']);
    $item_price = $row['item_price'];
    $pricing_unit = $row['pricing_unit'];

    $display .= "<tr bgcolor=#DDDDDD><td colspan=\"8\"><a name=\"$product_id\">
    <font size=5>$product_name (Product ID# $product_id)</font><br>
    <b>\$".number_format($item_price, 2)."/$pricing_unit</b>
    </td></tr>";

    $total_pr = 0;
    $subtotal_pr = 0;
    //ORDER BY '.TABLE_BASKET.'.basket_id ASC
    $sql = '
      SELECT
        '.TABLE_BASKET.'.*,
        '.TABLE_BASKET_ALL.'.*,
        '.TABLE_MEMBER.'.*,
        '.TABLE_PRODUCT.'.product_name,
        '.TABLE_PRODUCT.'.ordering_unit,
        '.TABLE_DELCODE.'.hub,
        '.TABLE_DELCODE.'.delcode_id,
        '.TABLE_DELCODE.'.delcode,
        '.TABLE_DELCODE.'.deltype,
        '.TABLE_DELCODE.'.truck_code,
        pst.storage_code
      FROM
        '.TABLE_BASKET.',
        '.TABLE_PRODUCT.',
        '.TABLE_BASKET_ALL.',
        '.TABLE_MEMBER.',
        '.TABLE_ROUTE.',
        '.TABLE_DELCODE.',
        '.TABLE_PRODUCT_STORAGE_TYPES.' AS pst
      WHERE
        '.TABLE_BASKET.'.product_id = '.$product_id.'
        AND '.TABLE_BASKET_ALL.'.member_id = '.TABLE_MEMBER.'.member_id
        AND
          (
            '.TABLE_BASKET_ALL.'.delivery_id = '.$delivery_id.'
            OR '.TABLE_BASKET.'.future_delivery_id = '.$delivery_id.'
          )
        AND '.TABLE_BASKET_ALL.'.basket_id = '.TABLE_BASKET.'.basket_id
        AND '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id
        AND '.TABLE_PRODUCT.'.producer_id = "'.$producer_id.'"
        AND '.TABLE_BASKET_ALL.'.delcode_id = '.TABLE_DELCODE.'.delcode_id
        AND '.TABLE_DELCODE.'.route_id = '.TABLE_ROUTE.'.route_id
        AND '.TABLE_PRODUCT.'.storage_id = pst.storage_id
      GROUP BY
        '.TABLE_BASKET.'.basket_id
      ORDER BY
        pst.storage_code ASC,
        '.TABLE_DELCODE.'.delcode_id ASC,
        '.TABLE_BASKET_ALL.'.member_id ASC,
        '.TABLE_DELCODE.'.hub ASC';
    $resultpr = @mysql_query($sql,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    while ( $row = mysql_fetch_array($resultpr) )
      {
        $product_name = stripslashes ($row['product_name']);
        $product_id = $row['product_id'];
        $basket_id = $row['basket_id'];
        $member_id = $row['member_id'];
        $last_name = stripslashes ($row['last_name']);
        $first_name = stripslashes ($row['first_name']);
        $business_name = stripslashes ($row['business_name']);
        $hub = $row['hub'];
        $delcode_id = $row['delcode_id'];
        $delcode = $row['delcode'];
        $deltype = $row['deltype'];
        $truck_code = $row['truck_code'];
        $storage_code = $row['storage_code'];
        $quantity = $row['quantity'];
        $ordering_unit = $row['ordering_unit'];
        $item_price = $row['item_price'];

        if ( $last_name && $first_name )
          {
            $show_mem2 = $first_name.' '.$last_name;
            $show_mem = $last_name.', '.$first_name;
          }
        else
          {
            $show_mem2 = $business_name;
            $show_mem = $business_name;
          }

        $display_label .= "<font size=5 face=arial>";
        //$display_label .= $row['hub']."-$member_id-".$row['delcode_id']." ".$row['deltype']."-".$row['truck_code']."<br>";
        $display_label .= (convert_route_code($basket_id, $member_id, $last_name, $first_name, $business_name, $a_business_name, $hub, $delcode_id, $deltype, $truck_code, $storage_code, $show_mem, $show_mem2, $product_name, $quantity, $ordering_unit, $product_id, $item_price, $delcode)).'<br>';
        $display_label .= stripslashes($show_mem2).'<br>';
        $display_label .= stripslashes($a_business_name).'<br>';
        $display_label .= '#'.$product_id.' - '.$product_name.' ('.$quantity.' '.Inflect::pluralize_if ($quantity, $ordering_unit).')';
        $display_label .= '</font><br><br>';
      }
  }
?>

  <!-- CONTENT BEGINS HERE -->

<font face=arial>
<h3>Producer Labels: One Label Per Product for <?php echo $current_delivery_date;?> for <?php echo $a_business_name;?></h3><br>

<?php echo $display_label;?>

  <!-- CONTENT ENDS HERE -->
