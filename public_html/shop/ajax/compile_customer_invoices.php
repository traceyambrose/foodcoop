<?php
include_once ("config_foodcoop.php");
include_once ("general_functions.php");


// Get the arguments passed in the query_data variable
$argument_array = split (':', str_replace ('basket_id', 'basket_id:', $_POST['query_data']));

if ($argument_array[0] == 'basket_id')
  {
    $basket_id = $argument_array[1]; // Query is received as "basket_id:xxx:yy"
    $delivery_id = $argument_array[2];
  }
elseif ($argument_array[0] == 'html2pdf')
  {
    // Input like: html2pdf:14
    $delivery_id = $argument_array[1];
    $customer_output_html = INVOICE_FILE_PATH.'invoices_customers-'.$delivery_id.'.html';
    $customer_output_pdf = INVOICE_FILE_PATH.'invoices_customers-'.$delivery_id.'.pdf';
    exec("htmldoc --webpage --browserwidth 800 --left 36 --right 36 -t pdf $customer_output_html -f $customer_output_pdf");
    echo 'HTML2PDF';
    exit (1);
  }
elseif ($argument_array[0] == 'delete_html')
  {
    // Input like: delete_html:35
    $delivery_id = $argument_array[1];
    $customer_output_html = INVOICE_FILE_PATH.'invoices_customers-'.$delivery_id.'.html';
    if (file_exists($customer_output_html))
      {
        unlink($customer_output_html);
      }
    echo 'DELETED_HTML';
    exit (1);
  }
elseif ($argument_array[0] == 'delete_pdf')
  {
    // Input like: delete_pdf:27
    $delivery_id = $argument_array[1];
    $customer_output_pdf = INVOICE_FILE_PATH.'invoices_customers-'.$delivery_id.'.pdf';
    if (file_exists($customer_output_pdf))
      {
        unlink($customer_output_pdf);
      }
    echo 'DELETED_PDF';
    exit (1);
  }
else
  {
    exit (0); // Wrong query string, so abort.
  }


$query = '
  SELECT
    '.TABLE_BASKET_ALL.'.member_id,
    '.TABLE_BASKET_ALL.'.basket_id,
    '.TABLE_MEMBER.'.last_name,
    '.TABLE_MEMBER.'.first_name,
    '.TABLE_BASKET_ALL.'.delivery_id,
    '.TABLE_BASKET_ALL.'.delcode_id,
    '.TABLE_DELCODE.'.delcode_id,
    '.TABLE_DELCODE.'.hub
  FROM ('.TABLE_BASKET_ALL.', '.TABLE_DELCODE.')
  LEFT JOIN '.TABLE_MEMBER.' ON '.TABLE_BASKET_ALL.'.member_id = '.TABLE_MEMBER.'.member_id
  WHERE
    '.TABLE_BASKET_ALL.'.member_id IS NOT NULL
    AND '.TABLE_BASKET_ALL.'.delivery_id = "'.$delivery_id.'"
    AND '.TABLE_BASKET_ALL.'.delcode_id = '.TABLE_DELCODE.'.delcode_id
    AND '.TABLE_BASKET_ALL.'.basket_id = "'.$basket_id.'"
  GROUP BY '.TABLE_BASKET_ALL.'.member_id
  ORDER BY
    '.TABLE_DELCODE.'.hub ASC,
    '.TABLE_BASKET_ALL.'.delcode_id ASC,
    '.TABLE_MEMBER.'.last_name ASC,
    '.TABLE_MEMBER.'.first_name ASC';

$result= mysql_query($query) or die("Error: " . mysql_error());

$customer_output_html = INVOICE_FILE_PATH.'invoices_customers-'.$delivery_id.'.html';
$fp = fopen($customer_output_html,a);

while ($row = mysql_fetch_array($result))
  {
    $hub = $row['hub'];
//    $member_id = $row['member_id'];
//    $basket_id = $row['basket_id'];
    require_once("../func/gen_invoice.php");
    $customer_invoice = '<div class="invoice-container">'.geninvoice($row['member_id'], $row['basket_id'], $delivery_id, '').'</div>'.HTMLDOC_PAGING;
    if ( strpos($customer_invoice, 'EMPTY INVOICE') === false )
      {
        fwrite($fp, $customer_invoice);
        $length = strlen ($customer_invoice);
      }
  }

echo "GENERATED_INVOICE";

?>