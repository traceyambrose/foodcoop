<?php
include_once ('config_foodcoop.php');
include_once ('general_functions.php');

$producer_id = substr($_SERVER['PHP_SELF'],strrpos($_SERVER['PHP_SELF'],'/')+1,-4);

$link = "<b><a href=\"".PATH."\">Click here to log in and shop or find out more</a></b>";

include(FILE_PATH.PATH.'func/display_producer_page.php');

include(FILE_PATH.PATH.'func/show_businessname.php');

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
    AND '.TABLE_PRODUCT.'.donotlist = "0"
    AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
    AND '.TABLE_PRODUCER.'.pending = "0"
    AND '.TABLE_PRODUCER.'.donotlist_producer = "0"
  ORDER BY
    '.TABLE_CATEGORY.'.category_name ASC,
    '.TABLE_SUBCATEGORY.'.subcategory_name ASC';
$rs = @mysql_query($sql,$connection) or die("Couldn't execute category query.");
$nums = mysql_numrows($rs);
while ( $row = mysql_fetch_array($rs) )
  {
    $category_id = $row['category_id'];
    $category_name = $row['category_name'];
    $subcategory_id = $row['subcategory_id'];
    $subcategory_name = $row['subcategory_name'];
    if ( $current_subcategory_id<0 )
      {
        $current_subcategory_id = $row['subcategory_id'];
      }
    while ($current_subcategory_id != $subcategory_id)
      {
        $current_subcategory_id = $subcategory_id;
        $display .= '<h2 style="width:95%;margin:auto;"><font color="#770000">'.$category_name.': '.$subcategory_name.'</font></h2>';
        include(FILE_PATH.PATH.'func/display_product_table_start.php');
        $sql = '
          SELECT
            '.TABLE_PRODUCT.'.*,
            '.TABLE_PRODUCT_TYPES.'.*
          FROM
            '.TABLE_PRODUCT.',
            '.TABLE_PRODUCT_TYPES.',
            '.TABLE_PRODUCER.'
          WHERE
            '.TABLE_PRODUCT.'.subcategory_id = "'.$subcategory_id.'"
            AND '.TABLE_PRODUCT.'.producer_id = "'.$producer_id.'"
            AND '.TABLE_PRODUCT.'.prodtype_id = '.TABLE_PRODUCT_TYPES.'.prodtype_id
            AND '.TABLE_PRODUCT.'.donotlist = "0"
            AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
            AND '.TABLE_PRODUCER.'.pending = "0"
            AND '.TABLE_PRODUCER.'.donotlist_producer = "0"
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
            $donotlist = $row['donotlist'];
            $detailed_notes = $row['detailed_notes'];
            $image_id = $row['image_id'];
            while ( $current_product_id != $product_id )
              {
                $current_product_id = $product_id;
                include(FILE_PATH.PATH.'func/display_productinfo_public.php');
              }
          }
        $display .= '</table>';
        $display .= '<div style="width:95%;margin:auto;text-align:right">'.$link.'</div>';
      }
  }
if(!$num)
  {
    $display .= 'No products available at this time.';
  }

?>
<?php include("../template_hdr.php");?>
<?php echo prdcr_info($producer_id);?>

<a name="products">
<br>
<div style="width:95%;margin:auto;">
<font size="5"><b><?php echo stripslashes($business_name);?> Products for Sale through the <?php echo ORGANIZATION_TYPE ?></b></font><br>
<?php echo $link;?>
</div>


<?php echo "$display";?><br>
<div align="right"><a href="<?php echo PATH;?>coopproducers.php">Back to producers list</a></div>


<?php include("../template_footer.php");?>
