<?php
include_once ('config_foodcoop.php');
include_once ('general_functions.php');

$date_today = date("F j, Y");

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
    AND '.TABLE_PRODUCT.'.donotlist = 0
    AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
    AND '.TABLE_PRODUCER.'.pending = 0
    AND '.TABLE_PRODUCER.'.donotlist_producer = 0
  ORDER BY
    '.TABLE_CATEGORY.'.category_name ASC,
    '.TABLE_SUBCATEGORY.'.subcategory_name ASC';
$rs = @mysql_query($sql,$connection) or die("Couldn't execute category query.");
while ( $row = mysql_fetch_array($rs) )
  {
    $category_id = $row['category_id'];
    $category_name = stripslashes($row['category_name']);
    $subcategory_id = stripslashes($row['subcategory_id']);
    $subcategory_name = stripslashes($row['subcategory_name']);

    if ( $current_subcategory_id < 0 )
      {
        $current_subcategory_id = $row['subcategory_id'];
      }
    while ( $current_subcategory_id != $subcategory_id )
      {
        $current_subcategory_id = $subcategory_id;
        $display .= "<h2><font color=\"#770000\">$category_name: $subcategory_name</font></h2>";

        $display .= "<table border=1 cellpadding=5 cellspacing=0 bordercolor=#DDDDDD width=\"95%\">";
        $display .= "<tr>";
        $display .= "<th align=center bgcolor=#DDDDDD width=\"60\">$font ID</th>";
        $display .= "<th align=center bgcolor=#DDDDDD>$font Product Name [<a href='producers/".strtolower($producer_id).".php'>About Producer</a>]</th>";
        $display .= "<th align=center bgcolor=#DDDDDD width=\"60\">$font Type</th>";
        $display .= "<th align=center bgcolor=#DDDDDD width=\"60\">$font Price</th>";
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
            AND '.TABLE_PRODUCT.'.donotlist = 0
            AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
            AND '.TABLE_PRODUCER.'.pending = 0
            AND '.TABLE_PRODUCER.'.donotlist_producer = 0
          ORDER BY
            product_name ASC,
            unit_price ASC';
        $result = @mysql_query($sql,$connection) or die("Couldn't execute search query.");

        $num = mysql_numrows($result);

        while ( $row = mysql_fetch_array($result) )
          {
            $product_id = $row['product_id'];
            $product_name = $row['product_name'];
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
                include("func/display_productinfo_public.php");
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
    AND '.TABLE_PRODUCER.'.donotlist_producer = 0
    ';
$resultp = @mysql_query($sqlp,$connection) or die("Couldn't execute search query 2.");
while ( $row = mysql_fetch_array($resultp) )
  {
    $business_name = stripslashes($row['business_name']);
    $first_name = stripslashes($row['first_name']);
    $last_name = stripslashes($row['last_name']);
    if ( !$business_name )
      {
        $business_name = "$first_name $last_name";
      }
  }
?>

<?php include("template_hdr.php");?>

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
      include("func/display_producer_page.php");
      echo prdcr_info($producer_id);?>

      <a name="products"></a>
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

<?php include("template_footer.php");?>