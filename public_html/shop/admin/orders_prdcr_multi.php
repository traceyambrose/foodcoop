<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
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

include('../func/producer_orders_multi.php');
include ('../func/producer_orders_totals.php');

include("template_hdr.php");

?>
<div align="center">
<table width="90%" border="1" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
  <tr>
    <td align="left" colspan="2">
      <table width="100%">
        <tr>
          <td align="left" colspan="11">
            <div align="center">
            <h3>Producer List for <?php echo $delivery_date;?> for <?php echo stripslashes ($a_business_name); ?></h3>
            <?php echo $message;?>
            </div>
          </td>
        </tr>
        <tr>
          <td align="left">
            <form action="<? echo $PHP_SELF.'?delivery_id='.$delivery_id.'&producer_id='.$producer_id ?>" method="post">
            <font size=4>Multi Sort (as shown below)</font>&nbsp;&nbsp;
            <input type="hidden" name="updatevalues" value="sort">
            <input type="submit" name="action" value="Change Sorting"><br><br>
            <table cellspacing="3" border="0" cellpadding="0">
              <tr>
                <td align="right">Sorted by
                <td><? echo "$sort1_display" ?></td>
                <td>(with headers)</td>
              </tr>
              <tr>
                <td align="right">then by</td>
                <td><? echo "$sort2_display" ?>
                <td>(with headers)</td>
              </tr>
              <tr>
                <td align="right">then by</td>
                <td><? echo "$sort3_display" ?>
                <td>(with headers)</td>
              </tr>
              <tr>
                <td align="right">then by</td>
                <td><? echo "$sort4_display" ?>
                <td>(no headers)</td>
              </tr>
              <tr>
                <td colspan="3" align="center">
                </td>
              </tr>
            </table>
            </form>


          </td>
          <td align="right" valign="bottom">
            Click for invoice sorted by <a href="orders_prdcr.php?delivery_id=<?php echo $delivery_id; ?>&producer_id=<?php echo $producer_id; ?>">product</a><br>
            Click for invoice sorted by <a href="orders_prdcr_cust.php?delivery_id=<?php echo $delivery_id; ?>&producer_id=<?php echo $producer_id; ?>">customer</a><br>
            Click for invoice sorted by <a href="orders_prdcr_cust_storage.php?delivery_id=<?php echo $delivery_id; ?>&producer_id=<?php echo $producer_id; ?>">storage/customer</a><br>
            Click for <a href="../func/producer_labels.php?delivery_id=<?php echo $delivery_id; ?>&producer_id=<?php echo $producer_id; ?>">labels (one per product/customer)</a><br>
            Click for <a href="../func/producer_labelsc.php?delivery_id=<?php echo $delivery_id; ?>&producer_id=<?php echo $producer_id; ?>">labels (one per storage/customer)</a>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <?php echo $producer_orders_multi; ?>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <?php echo $producer_orders_totals; ?>
    </td>
  </tr>
</table>

</div>
  <!-- CONTENT ENDS HERE -->
<br><br>
<?php include("template_footer.php"); ?>