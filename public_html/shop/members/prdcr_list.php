<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();

$color1 = "#DDDDDD";
$color2 = "#CCCCCC";
$row_count = 0;

$sqlr = '
  SELECT
    '.TABLE_PRODUCER.'.*,
    '.TABLE_MEMBER.'.*
  FROM
    '.TABLE_PRODUCER.',
    '.TABLE_MEMBER.'
  WHERE
    '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
    AND '.TABLE_PRODUCER.'.pending = 0
    AND '.TABLE_PRODUCER.'.donotlist_producer = 0
  ORDER BY
    '.TABLE_MEMBER.'.business_name ASC';
$rsr = @mysql_query($sqlr,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
while ( $row = mysql_fetch_array($rsr) )
  {
    $producer_id = $row['producer_id'];
    $business_name = stripslashes ($row['business_name']);
    $producttypes = $row['producttypes'];

    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $first_name_2 = $row['first_name_2'];
    $last_name_2 = $row['last_name_2'];
    $total_product_id = '';

    $sql = '
      SELECT
        count('.TABLE_PRODUCT.'.product_id) as total_product_id,
        '.TABLE_PRODUCT.'.producer_id
      FROM
        '.TABLE_PRODUCT.'
      WHERE
        '.TABLE_PRODUCT.'.producer_id = "'.$producer_id.'"
        AND '.TABLE_PRODUCT.'.donotlist = "0"
      GROUP BY
        '.TABLE_PRODUCT.'.producer_id';
    $result = @mysql_query($sql,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    while ( $row = mysql_fetch_array($result) )
      {
        $total_product_id = $row['total_product_id'];
        if($total_product_id > 0){
            $show_name = "";
            include("../func/show_name.php");
            $row_color = ($row_count % 2) ? $color1 : $color2;
            $display_top .= "<tr bgcolor=\"$row_color\"><td width=\"25%\">
              <font face=\"arial\" size=\"3\"><b><a href=\"prdcr_categories.php?producer_id=$producer_id\">".stripslashes($business_name)."</a></b></td>
              <td width=\"75%\">$producttypes</font></td></tr>";
            $row_count++;
          }
      }
  }
?>

<?php include("template_hdr_orders.php");?>

  <!-- CONTENT BEGINS HERE -->

<a name="top">
<font face="arial">

<div align="center">
<h3>Producer List</h3>
</div>
Only coop producer members with products to sell this month are listed on this page.  For a complete listing of the producer members, irrespective of the current status of their product offerings, click here for a <a href="<?php echo BASE_URL.PATH;?>coopproducers.php">complete listing of producer members</a>.<br><br>

<table cellpadding="2" cellspacing="2" border="0">
  <tr bgcolor="#AEDE86">
    <td><b>Producer Name</b> (Click on name)</td><td><b>Types of Products Available for Sale</b>
    </td>
  </tr>
  <?php echo $display_top;?>
</table>


  <!-- CONTENT ENDS HERE -->

<?php include("template_footer_orders.php");?>