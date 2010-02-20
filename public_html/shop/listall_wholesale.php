<?php
include_once ('config_foodcoop.php');
include_once ('general_functions.php');

$date_today = date("F j, Y");
$display = '';

/* some variables with default values to handle sorting the table */
$order_by = 'product_id';
$sorted_by = 'id';
$order_direction = 'ASC';
$reverse_direction = 'DESC';


/* determine the sort order */
if (isset($_GET['order']) && $_GET['order'] != "") {
	/* if the same order_by, switch the direction */
	//if ($_GET['order'] == $order_by) {
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
	//} else echo 'orders do not match <br/>';
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
//    '.TABLE_PRODUCER.'.producer_id,
//    '.TABLE_PRODUCER.'.donotlist_producer,

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
        $producer_id = $row['producer_id'];
        $show_business_link = true;
        include("func/display_productinfo_wholesale.php");

 //     }
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

        <h3>Wholesale Product List: <? echo $num; ?> products found and sorted by <?=$sorted_by?></h3>


        <table border=1 cellpadding=5 cellspacing=0 bordercolor=#DDDDDD>
          <tr>
            <th align="center" bgcolor="#DDDDDD"><?php echo $font;?><a href="listall_wholesale.php?order=id&dir=<?php echo $reverse_direction?>">ID</a></font></th>
            <th align="center" bgcolor="#DDDDDD"><?php echo $font;?><a href="listall_wholesale.php?order=product&dir=<?php echo $reverse_direction?>">Product Name</a></font></th>
            <th align="center" bgcolor="#DDDDDD"><?php echo $font;?><a href="listall_wholesale.php?order=producer&dir=<?php echo $reverse_direction?>">Producer</a></th>
            <th align="center" bgcolor="#DDDDDD"><?php echo $font;?><a href="listall_wholesale.php?order=type&dir=<?php echo $reverse_direction?>">Type</a></th>
            <th align="center" bgcolor="#DDDDDD"><?php echo $font;?><a href="listall_wholesale.php?order=price&dir=<?php echo $reverse_direction?>">Price</a></font></th>
          </tr>

          <?php echo $display;?>

        </table>
      </td>
    </tr>
  </table>
</div>
  <!-- CONTENT ENDS HERE -->

<?php include("template_footer.php");?>