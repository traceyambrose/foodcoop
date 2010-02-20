<?php
$user_type = 'valid_c';
include_once ('config_foodcoop.php');
session_start();
validate_user();

include_once ('general_functions.php');

$producer_id = $producer_id_you;
if ( $_GET['delivery_id'] )
  {
    $delivery_id = $_GET['delivery_id'];
  }
else
  {
    $delivery_id = $current_delivery_id;
  }

if ( $updatevalues == "ys" && $_POST['product_id'] && $_POST['c_basket_id'])
  {
    $sqlu = '
      UPDATE
        '.TABLE_BASKET.'
      SET
        total_weight = "'.$total_weight.'",
        out_of_stock = "'.$out_of_stock.'"
      WHERE
        basket_id = '.$_POST['c_basket_id'].'
        AND product_id = '.$_POST['product_id'];
    $result = @mysql_query($sqlu,$connection) or die(mysql_error());
    $message2 = "<b><font color=\"#3333FF\">The information has been updated.</font></b><br><br>";
  }

// Get the target delivery date
$query = '
  SELECT
    delivery_date
  FROM
    '.TABLE_DELDATE.'
  WHERE
    delivery_id = "'.$delivery_id.'"';
$result = @mysql_query($query, $connection) or die(mysql_error());
if ( $row = mysql_fetch_array($result) )
  {
    $delivery_date = date ("F j, Y", strtotime ($row['delivery_date']));
  }

$total = 0;
$total_pr = 0;
$subtotal_pr = 0;

include('../func/producer_orders_bycustomer.php');
include ('../func/producer_orders_totals.php');

include("template_hdr.php");
?>

  <!-- CONTENT BEGINS HERE -->

<div align="center">
<table width="90%" border="1" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
  <tr>
    <td align="left">
      <div align="center">
      <h3>Producer List for <?php echo $delivery_date;?> for <?php echo stripslashes ($a_business_name); ?></h3>
      <?php echo $message;?>
      </div>
      <?php echo $producer_orders_bycustomer; ?>
    </td>
  </tr>
  <tr>
    <td>
      <?php echo $producer_orders_totals; ?>
    </td>
  </tr>
</table>

</div>
  <!-- CONTENT ENDS HERE -->
<br><br>
<?php include("template_footer.php"); ?>