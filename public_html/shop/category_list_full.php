<?php
include_once ('config_foodcoop.php');
include_once ('general_functions.php');


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
    AND '.TABLE_PRODUCT.'.donotlist = "0"
    AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
    AND '.TABLE_PRODUCER.'.pending = "0"
    AND '.TABLE_PRODUCER.'.donotlist_producer = "0"
  GROUP BY
    '.TABLE_PRODUCT.'.subcategory_id
  ORDER BY
    sort_order ASC,
    subcategory_name ASC';
$rs = @mysql_query($sql,$connection) or die("Couldn't execute category query.");
while ($row = mysql_fetch_array($rs))
  {
    $category_id = $row['category_id'];
    $category_name = stripslashes($row['category_name']);
    $subcategory_id = $row['subcategory_id'];
    $subcategory_name = stripslashes($row['subcategory_name']);

    if ($current_category_id<0)
      {
        $current_category_id = $row['category_id'];
      }

    while ($current_category_id != $category_id)
      {
        $current_category_id = $category_id;
        $display .= "<hr>";
        $display .= "<h2>$category_name</h2>";
      }

    $display .= "<h3>$subcategory_name</h3>";

    $sqlp = '
      SELECT
        '.TABLE_PRODUCT.'.subcategory_id,
        '.TABLE_PRODUCT.'.producer_id,
        '.TABLE_PRODUCER.'.producer_id,
        '.TABLE_PRODUCER.'.member_id,
        '.TABLE_MEMBER.'.member_id,
        '.TABLE_MEMBER.'.business_name,
        '.TABLE_MEMBER.'.first_name,
        '.TABLE_MEMBER.'.last_name,
        '.TABLE_PRODUCT.'.donotlist
      FROM
        '.TABLE_PRODUCT.',
        '.TABLE_PRODUCER.',
        '.TABLE_MEMBER.'
      WHERE '.TABLE_PRODUCT.'.subcategory_id = "'.$subcategory_id.'"
        AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
        AND '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
        AND '.TABLE_PRODUCT.'.donotlist = "0"
        AND '.TABLE_PRODUCER.'.pending = "0"
        AND '.TABLE_PRODUCER.'.donotlist_producer = "0"
      GROUP BY
        '.TABLE_PRODUCT.'.producer_id
      ORDER BY
        '.TABLE_MEMBER.'.business_name';
    $resultp = @mysql_query($sqlp,$connection) or die("Couldn't execute query 2.");
    while ($row = mysql_fetch_array($resultp))
      {
        $producer_id = $row['producer_id'];
        $business_name = stripslashes($row['business_name']);
        $first_name = stripslashes($row['first_name']);
        $last_name = stripslashes($row['last_name']);

        if (!$business_name)
          {
            $business_name = "$first_name $last_name";
          }

        if ($current_producer_id<0)
          {
            $current_producer_id = $row['producer_id'];
          }
        while ($current_producer_id != $producer_id)
          {
            $current_producer_id = $producer_id;
          }
        $display .= "<ul>";
        $display .= "<font color=\"#770000\"><h3>".stripslashes($business_name)."</h3></font>";
        $display .= "<table border=1 cellpadding=5 cellspacing=0 bordercolor=#DDDDDD width=\"95%\">";
        $display .= "<tr>";
        $display .= "<th align=center bgcolor=#DDDDDD width=\"60\">$font ID</font></th>";
        $display .= "<th align=center bgcolor=#DDDDDD>$font Product Name [<a href='producers/".strtolower($producer_id).".php'>About Producer</a>]</font></th>";
        $display .= "<th align=center bgcolor=#DDDDDD width=\"60\">$font Type</font></th>";
        $display .= "<th align=center bgcolor=#DDDDDD width=\"60\">$font Price</font></th>";
        $display .= "</tr>";
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
            AND '.TABLE_PRODUCT.'.donotlist = "0"
          ORDER BY
            product_name ASC,
            unit_price ASC';
        $result = @mysql_query($sql,$connection) or die("Couldn't execute search query.");
        while ($row = mysql_fetch_array($result))
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

            if ($current_product_id<0)
              {
                $current_product_id = $row['product_id'];
              }
            while ($current_product_id != $product_id)
              {
                $current_product_id = $product_id;
                include("func/display_productinfo_public.php");
              }
          }
        $display .= "</table>";
        $display .= "</ul>";
      }
  }

?>
<?php include("template_hdr.php");?>

<!-- CONTENT BEGINS HERE -->

<div align="center">
  <table width="80%">
    <tr>
      <td align="left"><?php echo $font;?>
        <div align="center">
          <h3>Product List: Sorted by Category</h3>
        </div>
      <?php echo $display;?>
      </td>
    </tr>
  </table>
</div>

<!-- CONTENT ENDS HERE -->

<?php include("template_footer.php");?>
