<?php
include_once ("config_foodcoop.php");
include_once ("general_functions.php");

// require_once("../func/order_summary_function.php");

// Get the arguments passed in the query_data variable
$argument_array = split (':', str_replace ('producer_id', 'producer_id:', $_POST['query_data']));

if ($argument_array[0] == 'producer_id')
  {
    $producer_id = $argument_array[1]; // Query is received as "basket_id:xxx:yy"
    $delivery_id = $argument_array[2];
  }
elseif ($argument_array[0] == 'html2pdf')
  {
    // Input like: html2pdf:14
    $delivery_id = $argument_array[1];
    $producer_output_html = INVOICE_FILE_PATH.'invoices_producers-'.$delivery_id.'.html';
    $producer_output_pdf = INVOICE_FILE_PATH.'invoices_producers-'.$delivery_id.'.pdf';
    exec("htmldoc --webpage --browserwidth 800 --left 36 --right 36 -t pdf $producer_output_html -f $producer_output_pdf");
    echo 'HTML2PDF';
    exit (1);
  }
elseif ($argument_array[0] == 'delete_html')
  {
    // Input like: delete_html:35
    $delivery_id = $argument_array[1];
    $producer_output_html = INVOICE_FILE_PATH.'invoices_producers-'.$delivery_id.'.html';
    if (file_exists($producer_output_html))
      {
        unlink($producer_output_html);
      }
    echo 'DELETED_HTML';
    exit (1);
  }
elseif ($argument_array[0] == 'delete_pdf')
  {
    // Input like: delete_pdf:27
    $delivery_id = $argument_array[1];
    $producer_output_pdf = INVOICE_FILE_PATH.'invoices_producers-'.$delivery_id.'.pdf';
    if (file_exists($producer_output_pdf))
      {
        unlink($producer_output_pdf);
      }
    echo 'DELETED_PDF';
    exit (1);
  }
else
  {
    exit (0); // Wrong query string, so abort.
  }

// Set up the values the invoice needs to operate (since it isn't implemented as a function)
$producer_id_you = $producer_id;
$_GET['delivery_id'] = $delivery_id;
$display_only = true;

include('../func/producer_orders_bycustomerstorage.php');
include ('../func/producer_orders_totals.php');

$producer_output_html = INVOICE_FILE_PATH.'invoices_producers-'.$delivery_id.'.html';
$fp = fopen($producer_output_html,a);

// $producer_invoice = '<div class="invoice-container">'.generate_producer_summary ($producer_id, $delivery_id, 'customer', 'batch').'</div><!-- MEDIA DUPLEX YES -->';
$producer_invoice = '<div class="invoice-container">'.$producer_orders_bycustomerstorage.$producer_orders_totals.'</div>'.HTMLDOC_PAGING;
fwrite($fp, $producer_invoice);

echo 'GENERATED_INVOICE';

?>