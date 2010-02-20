<?php
$user_type = 'valid_c';
include_once ('config_foodcoop.php');
session_start();
validate_user();

include_once ('general_functions.php');

$producer_id = $producer_id_you;

if ($_GET['display_only'] == "true")
  {
    $display_only = true;
  }
else
  {
    $display_only = false;
  }

if(strpos ($_SESSION['auth_type'], 'administrator') !== false && $_GET['producer_id'])
  {
    // Save session values in order to put them back before we're done (MESSY because of register_globals!)
    $original_session_producer_id = $_SESSION['producer_id'];
    $producer_id = $_GET['producer_id'];
//     if ($_GET['display_only'] = "true")
//       {
//         $display_only = true;
//       }
    $put_it_back = true;
  }

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

include('../func/producer_orders_bycustomerstorage.php');
include ('../func/producer_orders_totals.php');

if (!$display_only)
  {
    include("template_hdr.php");
    echo '
      <!-- CONTENT BEGINS HERE -->';
  }

if (!$display_only)
  {
    echo '
  <div align="center">
  <table width="90%" border="1" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
    <tr>
      <td align="left">
        <div align="center">
        <h3>Producer List for '.$delivery_date.' for '.$a_business_name.'</h3>
        '.$message.'
        </div>';
  }

// Conditional for generating PDF invoices
if ($_GET['output'] == 'pdf')
  {
    $fp = fopen( FILE_PATH.PATH.'admin/temp/producer_invoice_temp.html', a);
    fwrite($fp, $producer_orders_bycustomerstorage);
  }
else
  {
    echo $producer_orders_bycustomerstorage;
  }

if (!$display_only)
  {
    echo '
      </td>
    </tr>
    <tr>
      <td>';
  }

// Conditional for generating PDF invoices
if ($_GET['output'] == 'pdf')
  {
    $fp = fopen( FILE_PATH.PATH.'admin/temp/producer_invoice_temp.html', a);
    fwrite($fp, $producer_orders_totals);
    // Now convert to PDF and send to browser
    putenv("HTMLDOC_NOCGI=1");
    header("Content-Type: application/pdf");
    flush();
    passthru('htmldoc -t pdf --webpage '.FILE_PATH.PATH.'admin/temp/producer_invoice_temp.html');
    unlink(FILE_PATH.PATH.'admin/temp/producer_invoice_temp.html');
  }
else
  {
    echo $producer_orders_totals;
  }

if (!$display_only)
  {
    echo '
      </td>
    </tr>
  </table>

  </div>
    <!-- CONTENT ENDS HERE -->
  <br><br>';
  }

if (!$display_only)
  {
    include("template_footer.php");
  }

// Restore the session variables to their original settings
if ($put_it_back)
  {
  $producer_id = $original_session_producer_id;
  };

