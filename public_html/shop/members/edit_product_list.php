<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
include_once ('general_functions.php');
session_start();
validate_user();
$date_today = date("F j, Y");
$display_type = "edit";

// Check if auth_type = administrator and there is a producer_id provided
if(strpos ($_SESSION['auth_type'], 'administrator') !== false && $_GET['producer_id'])
  {
    // Keep the same producer_id value
    $producer_id = $_GET['producer_id'];
  }
elseif ($_SESSION['producer_id_you'])
  {
    $producer_id = $_SESSION['producer_id_you'];
  }

if ( $confirm == "yes" )
  {
    $sqlu = '
      UPDATE
        '.TABLE_PRODUCT_PREP.'
      SET
        confirmed = 1
      WHERE
        producer_id = "'.$producer_id.'"';
    $result = @mysql_query($sqlu,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
  }
include("../func/show_confirmedornot.php");

if ( $_REQUEST['a']=="retail" )
  {
    $wherestatement = ' AND '.$table_prep.'.donotlist = "0" ';
  }
elseif ( $_REQUEST['a']=="unlisted" )
  {
    $wherestatement = ' AND '.$table_prep.'.donotlist = "1" ';
  }
elseif ( $_REQUEST['a']=="archived" )
  {
    $wherestatement = ' AND '.$table_prep.'.donotlist = "2" ';
  }
elseif($_REQUEST['a']=="wholesale")
  {
    $wherestatement = " AND ".$table_prep.".donotlist = '3' ";
  }
else
  {
    $wherestatement = "";
  }

$sql = '
  SELECT
    '.TABLE_CATEGORY.'.*,
    '.TABLE_SUBCATEGORY.'.*,
    '.TABLE_PRODUCT_PREP.'.subcategory_id,
    '.TABLE_PRODUCT_PREP.'.producer_id
  FROM
    '.TABLE_CATEGORY.',
    '.TABLE_SUBCATEGORY.',
    '.TABLE_PRODUCT_PREP.'
  WHERE
    '.TABLE_CATEGORY.'.category_id = '.TABLE_SUBCATEGORY.'.category_id
    AND '.TABLE_SUBCATEGORY.'.subcategory_id = '.TABLE_PRODUCT_PREP.'.subcategory_id
    AND '.TABLE_PRODUCT_PREP.'.producer_id = "'.$producer_id.'"
    '.$wherestatement.'
  ORDER BY
    '.TABLE_CATEGORY.'.category_name ASC,
    '.TABLE_SUBCATEGORY.'.subcategory_name ASC';
$rs = @mysql_query($sql,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
while ( $row = mysql_fetch_array($rs) )
  {
    $category_id = $row['category_id'];
    $category_name = $row['category_name'];
    $subcategory_id = $row['subcategory_id'];
    $subcategory_name = $row['subcategory_name'];

    if ( $current_subcategory_id < 0 )
      {
      $current_subcategory_id = $row['subcategory_id'];
      }
    while ( $current_subcategory_id != $subcategory_id )
      {
        $current_subcategory_id = $subcategory_id;

        $display .= "<div align=\"right\"><font size=\"-1\">
        [ <a href=\"index.php\">Return to main page</a> |
        <a href=\"logout.php\">Logout</a> ]</font></div>";

        $display .= "<h2><font color=\"#770000\">$category_name: $subcategory_name</font></h2>";

        include("../func/display_product_table_start.php");

        $sql2 = '
          SELECT
            *
          FROM
            '.TABLE_PRODUCT_PREP.',
            '.TABLE_PRODUCT_TYPES.'
          WHERE
            '.TABLE_PRODUCT_PREP.'.subcategory_id = '.$subcategory_id.'
            AND '.TABLE_PRODUCT_PREP.'.producer_id = "'.$producer_id.'"
            AND '.TABLE_PRODUCT_PREP.'.prodtype_id = '.TABLE_PRODUCT_TYPES.'.prodtype_id
            '.$wherestatement.'
          ORDER BY
            product_name ASC,
            unit_price ASC';
        $result = @mysql_query($sql2,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());

        $num = mysql_numrows($result);

        while ( $row = mysql_fetch_array($result) )
          {
            include("../func/display_productinfo.php");
          }

        $display .= "</table>";
       }
  }
include("../func/show_businessname.php");

include("template_hdr_orders.php");

?>
  <!-- CONTENT BEGINS HERE -->

<div align="center">
  <table width="80%">
    <tr>
      <td align="left">
        <h2><font color="#770000"><?php echo $business_name;?> Product List</font></h2>
        <h3><?php
foreach (array ("retail"=>"Listed Retail", "wholesale"=>"Listed Wholesale", "unlisted"=>"Unlisted", "archived"=>"Archived") as $key=>$value)
  {
    if ($_REQUEST['a'] != $key)
      {
        echo '[<a href="edit_product_list.php?producer_id='.$producer_id.'&a='.$key.'">'.$value.'</a>] ';
      }
    else
      {
        echo $value.' ';
      }
  }
        ?></h3>
        <table border=2 cellpadding=5 cellspacing=0 bgcolor=#EEEEEE>
          <tr>
            <td>
              &quot;Unlisted&quot; and &quot;Archived&quot; will not be shown in the public product list for ordering unless you change them. &quot;Listed Wholesale&quot; will only be available to wholesale buyers.  Notes in <font color=#3333FF>blue</font> are for you only and will not be seen on the public list.
              Click here to <b><a href="add_products.php?producer_id=<?php echo $producer_id; ?>&a=<?php echo $_REQUEST['a']; ?>">Add a new Product</a></b> (but if this is a product you previously listed, first check below to see if it is there).
              <br><br>
              Once changes are submitted, they will be reviewed by the coop admins to ensure everything is ok and will be released to the public once the ordering cycle is open. They will not be public prior to the opening of the order cycle.
              <br><br>
              <?php if (REQ_PRDCR_CONFIRM) echo $confirmed; ?>
            </td>
          </tr>
        </table>
        <br>

        <?php echo $display;?>

        <?php
        if (!$num) {
        echo "<br>No products found for this Producer at this time.";
        }
        ?>
        </td>
    </tr>
  </table>
</div>
  <!-- CONTENT ENDS HERE -->

<?php include("template_footer_orders_edit.php");?>