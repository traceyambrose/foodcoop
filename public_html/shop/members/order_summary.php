<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
include_once ('general_functions.php');
session_start();
validate_user();

$detail_type = $_GET['detail_type'];

include ('../func/order_summary_function.php');
if (! preg_match ('/.*compile_producer_invoices.*/' , $_SERVER['HTTP_REFERER']))
  {
    $web_display = true;
  };
////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
///                       OBTAIN CURRENT DELIVERY ID                         ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////


if ($_GET['delivery_id'])
  { // If we were passed a delivery_id, use  it
  $delivery_id = $_GET['delivery_id'];
  }
else
  { // Otherwise, use the current delivery_id
    $sqlp = '
      SELECT
        delivery_id,
        delivery_date
      FROM
        '.TABLE_CURDEL.'
      WHERE 1';
    $resultp = @mysql_query($sqlp,$connection) or die("Couldn't execute query.");
    while ($row = mysql_fetch_array($resultp))
      {
        $delivery_id = $row['delivery_id'];
        $delivery_date = $row['delivery_date'];
      }
  }

$producer_id = $producer_id_you;
$display_page = generate_producer_summary ($producer_id, $delivery_id, $detail_type, '');

if ($include_header)
  {
    include("template_hdr_orders.php");
  };
echo $display_page;
if ($include_footer)
  {
    include("template_footer_orders.php");
  };
