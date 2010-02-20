<?php
include_once ('config_foodcoop.php');
include_once ('general_functions.php');

$date_today = date("F j, Y");
$display = '';

$sql = '
  SELECT
    '.TABLE_PRODUCT.'.* ,
    '.TABLE_PRODUCT_TYPES.'.*,
    '.TABLE_PRODUCER.'.producer_id,
    '.TABLE_PRODUCER.'.donotlist_producer
  FROM
    '.TABLE_PRODUCT.',
    '.TABLE_PRODUCER.',
    '.TABLE_PRODUCT_TYPES.'
  WHERE
    '.TABLE_PRODUCT.'.donotlist != 1
    AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
    AND '.TABLE_PRODUCER.'.pending = 0
    AND '.TABLE_PRODUCER.'.donotlist_producer = 0
    AND '.TABLE_PRODUCT.'.prodtype_id = '.TABLE_PRODUCT_TYPES.'.prodtype_id
  GROUP BY
    product_id
  ORDER BY
    product_id ASC';
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
      {
        $business_name = stripslashes ($row['business_name']);
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $producer_id = $row['producer_id'];
        $show_business_link = true;
        include("func/display_productinfo_public.php");

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
      <td align="left">

        <h3>Full Product List: Sorted by Product ID</h3>


        <table border=1 cellpadding=5 cellspacing=0 bordercolor=#DDDDDD>
          <tr>
            <th align="center" bgcolor="#DDDDDD"><?php echo $font;?>ID</font></th>
            <th align="center" bgcolor="#DDDDDD"><?php echo $font;?>Product Name</font></th>
            <th align="center" bgcolor="#DDDDDD"><?php echo $font;?>Producer</th>
            <th align="center" bgcolor="#DDDDDD"><?php echo $font;?>Type</th>
            <th align="center" bgcolor="#DDDDDD"><?php echo $font;?>Price</font></th>
          </tr>

          <?php echo $display;?>

        </table>
      </td>
    </tr>
  </table>
</div>
  <!-- CONTENT ENDS HERE -->

<?php include("template_footer.php");?>