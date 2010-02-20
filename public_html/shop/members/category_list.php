<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();

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
    '.TABLE_PRODUCT.'.subcategory_id,
    '.TABLE_PRODUCT.'.donotlist
  FROM
    '.TABLE_CATEGORY.',
    '.TABLE_SUBCATEGORY.',
    '.TABLE_PRODUCT.',
    '.TABLE_PRODUCER.'
  WHERE
    '.TABLE_CATEGORY.'.category_id = '.TABLE_SUBCATEGORY.'.category_id
    AND '.TABLE_SUBCATEGORY.'.subcategory_id = '.TABLE_PRODUCT.'.subcategory_id
    '.$donotlist_condition.'
    AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
    AND '.TABLE_PRODUCER.'.pending = 0
    AND '.TABLE_PRODUCER.'.donotlist_producer = 0
  GROUP BY
    '.TABLE_PRODUCT.'.subcategory_id
  ORDER BY
    sort_order ASC,
    subcategory_name ASC';
$rs = @mysql_query($sql,$connection) or die("Couldn't execute category query.");
while ( $row = mysql_fetch_array($rs) )
  {
    $category_id = $row['category_id'];
    $category_name = stripslashes($row['category_name']);
    $subcategory_id = $row['subcategory_id'];
    $subcategory_name = stripslashes($row['subcategory_name']);

    if ( $current_category_id < 0)
      {
        $current_category_id = $row['category_id'];
      }

    while ( $current_category_id != $category_id )
      {
        $current_category_id = $category_id;
        $display .= "<h3>$category_name</h3>";
      }
    $display .= "<ul>";
    $display .= "<li><a href=\"category_producers.php?subcategory_id=$subcategory_id\">$subcategory_name</a>";
    $display .= "</ul>";
  }

?>

<?php include("template_hdr_orders.php");?>


  <!-- CONTENT BEGINS HERE -->

<div align="center">
<table width="80%">
  <tr><td align="left">

<div align="center">
<h3>Product List: Sorted by Category</h3>
</div>

<?php echo $display;?>



  </td></tr>
</table>
</div>
  <!-- CONTENT ENDS HERE -->

<?php include("template_footer_orders.php");?>