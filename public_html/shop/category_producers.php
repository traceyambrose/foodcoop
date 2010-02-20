<?php
include_once ('config_foodcoop.php');
include_once ('general_functions.php');


$date_today = date("F j, Y");

// register_globals: $subcategory_id

$sql = '
  SELECT
    subcategory_name
  FROM
    '.TABLE_SUBCATEGORY.'
  WHERE
    subcategory_id = '.$subcategory_id;
$rs = @mysql_query($sql,$connection) or die("Couldn't execute category query.");
while ( $row = mysql_fetch_array($rs) )
  {
    $subcategory_name = $row['subcategory_name'];
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
    '.TABLE_PRODUCT.'.subcategory_id = '.$subcategory_id.'
    AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
    AND '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
    AND '.TABLE_PRODUCT.'.donotlist = 0
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

    if ( !$business_name )
      {
        $business_name = "$first_name $last_name";
      }

    if ( $current_business_name < 0 )
      {
        $current_business_name = $row['business_name'];
      }

    while ( $current_business_name != $business_name )
      {
        $current_business_name = $business_name;
        $display .= "<font color=\"#770000\"><h2>$business_name</h2></font>";

        $display .= "<table border=1 cellpadding=5 cellspacing=0 bordercolor=#DDDDDD>";
        $display .= "<tr>";
        $display .= "<th align=center bgcolor=#DDDDDD>$font ID</th>";
        $display .= "<th align=center bgcolor=#DDDDDD>$font Product Name [<a href='".BASE_URL.PATH.'producers/'.strtolower($producer_id).".php'>About Producer</a>]</th>";
        $display .= "<th align=center bgcolor=#DDDDDD>$font Type</th>";
        $display .= "<th align=center bgcolor=#DDDDDD>$font Price</th>";
        $display .= "</tr>";

        $sql = '
          SELECT
            *
          FROM
            '.TABLE_PRODUCT.',
            '.TABLE_PRODUCT_TYPES.'
          WHERE
            '.TABLE_PRODUCT.'.subcategory_id = '.$subcategory_id.'
            AND '.TABLE_PRODUCT.'.producer_id = "'.$producer_id.'"
            AND '.TABLE_PRODUCT.'.prodtype_id = '.TABLE_PRODUCT_TYPES.'.prodtype_id
            AND '.TABLE_PRODUCT.'.donotlist = 0
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

?>

<?php include('template_hdr.php');?>

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

        <font color="#770000"><h2><u>Producers and Products in the <?php echo $subcategory_name;?> Section</u></h2></font>

        <?php echo $display;?>

        <?php
        if ( !$num )
          {
            echo "<br>No products found for this Producer at this time.";
          }
        ?>
      </td>
    </tr>
  </table>
</div>
  <!-- CONTENT ENDS HERE -->

<?php include('template_footer.php');?>