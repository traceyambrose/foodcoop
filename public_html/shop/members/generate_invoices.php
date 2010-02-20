<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();

// If not auth_type = administrator then abort to main page
if (strpos ($_SESSION['auth_type'], 'administrator') === false)
  {
    header("Location: index.php");
  }

include("template_hdr_orders.php");

// Get the current value of the delivery_id
$query = "SELECT delivery_id FROM current_delivery";
$result= mysql_query("$query") or die("Error: " . mysql_error());
while ($row = mysql_fetch_array($result)) {
  $current_delivery_id = $row['delivery_id'];
  }

// Use any valid delivery_id that was passed or else use current value
if (round ($_GET['delivery_id']) && $_GET['delivery_id'] <= $current_delivery_id && $_GET['delivery_id'] > 0)
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

?>

<script type="text/javascript" src="/shop/ajax/jquery.js"></script>
<script type="text/javascript">


var c_arrElements;
var p_arrElements;
var i;

function getElementsByClass (needle) {
  var my_array = document.getElementsByTagName("li");
  var retvalue = new Array();
  var i;
  var j;

  for (i = 0, j = 0; i < my_array.length; i++) {
    var c = " " + my_array[i].className + " ";
    if (c.indexOf(" " + needle + " ") != -1)
      retvalue[j++] = my_array[i];
    }
  return retvalue;
  }

// CUSTOMER FUNCTIONS

function reset_cust_list() {
  c_arrElements = getElementsByClass("c_complete");
  for (i = 0; i < c_arrElements.length; i++) {
    if (c_arrElements[i].attributes["class"].value == 'c_complete') {
      // Change the class from "complete" to "c_incomplete"
      c_arrElements[i].attributes["class"].value = "c_incomplete";
      }
    }
  }

function cust_generate_start() {
  // Delete the old html file before continuing
  $.post("/shop/ajax/compile_customer_invoices.php", { query_data: "delete_html:"+"<?php echo $delivery_id; ?>" }, function(data) {
    if (data != "DELETED_HTML") {
      alert ("ERROR C1: "+data+" \r\nPlease try again");
      }
    })
  //get list of all span elements:
  c_arrElements = getElementsByClass("c_incomplete");
  // Set display elements
  document.getElementById("cust_generate_start").style.display = "none"; /* Make the button disappear */
  document.getElementById("load_customer_html").style.display = "none"; /* Hide the html link until regenerated */
  document.getElementById("cust_progress").style.display = "block"; /* Show the progress bar */
  document.getElementById("prod_generate_button").disabled = "true"; /* Disable the other button */
  i = 0;
  }

function compile_customer_invoices() {
  //iterate over the <li> array elements:
  if (i < c_arrElements.length) {
    //check that this is the proper class
    if (c_arrElements[i].attributes["class"].value == 'c_incomplete') {
      // Get the id of the element (that is the basket number, formatted like: basket_id2147
      var element_id = c_arrElements[i].attributes["id"].value;
      $.post("/shop/ajax/compile_customer_invoices.php", { query_data: ""+element_id+":<?php echo $delivery_id; ?>" }, function(data) {
        if(data == "GENERATED_INVOICE") {
          var oldHTML = document.getElementById('customerList').innerHTML;
          var c_progress_left = Math.floor (300 * i / c_arrElements.length);
          var c_progress_right = 300 - c_progress_left;
          document.getElementById("c_progress-left").style.width = c_progress_left+"px";
          document.getElementById("c_progress-left").innerHTML = Math.floor (c_progress_left / 3)+"%&nbsp;";
          document.getElementById("c_progress-right").style.width = c_progress_right+"px";
          document.getElementById(element_id).className = "c_complete";
          // If we're done with the list, then show the PDF button
          if (i == c_arrElements.length) {
            // And go generate the pdf
            document.getElementById("cust_progress").style.display = "none"; /* Hide the progress bar */
            document.getElementById("load_customer_html").style.display = ""; /* Make html link visible */
            <? if (USE_HTMLDOC) { echo 'cust_generate_pdf();'; } else { ?>
            document.getElementById("prod_generate_button").disabled = ""; /* Re-enable the other button */
            document.getElementById("cust_generate_start").style.display = ""; /* Bring back the button */
            document.getElementById("cust_html2pdf_message").style.display = ""; /* Hide the html2pdf conversion message */
            <?php } ?>
            };
          // Continue cycling through this loop
          compile_customer_invoices ();
          }
        else {
          alert ("ERROR C2: "+data+" \r\nPlease try again");
          }
        });
      }
    i++;
    }
  }

function cust_generate_pdf() {
  document.getElementById("load_customer_pdf").style.display = "none"; /* Hide the pdf link until regenerated */
  document.getElementById("cust_html2pdf_message").style.display = "block"; /* Show the html2pdf conversion message */
  // Delete the old pdf file before continuing
  $.post("/shop/ajax/compile_customer_invoices.php", { query_data: "delete_pdf:"+"<?php echo $delivery_id; ?>" }, function(data) {
    if (data != "DELETED_PDF") {
      alert ("ERROR C3: "+data+" \r\nPlease try again");
      }
    })
  $.post("/shop/ajax/compile_customer_invoices.php", { query_data: "html2pdf:"+"<?php echo $delivery_id; ?>" }, function(data) {
    if(data != "HTML2PDF") {
      alert ("ERROR C4: "+data+" \r\nPlease try again");
      }
    document.getElementById("load_customer_pdf").style.display = ""; /* Make pdf link visible */
    document.getElementById("prod_generate_button").disabled = ""; /* Re-enable the other button */
    document.getElementById("cust_generate_start").style.display = ""; /* Bring back the button */
    document.getElementById("cust_html2pdf_message").style.display = ""; /* Hide the html2pdf conversion message */
    })
  }


// PRODUCER FUNCTIONS

function reset_prod_list() {
  p_arrElements = getElementsByClass("p_complete");
  for (i = 0; i < p_arrElements.length; i++) {
    if (p_arrElements[i].attributes["class"].value == 'p_complete') {
      // Change the class from "complete" to "p_incomplete"
      p_arrElements[i].attributes["class"].value = "p_incomplete";
      }
    }
  }

function prod_generate_start() {
  // Delete the old html file before continuing
  $.post("/shop/ajax/compile_producer_invoices.php", { query_data: "delete_html:"+"<?php echo $delivery_id; ?>" }, function(data) {
    if (data != "DELETED_HTML") {
      alert ("ERROR P1: "+data+" \r\nPlease try again");
      }
    })
  //get list of all span elements:
  p_arrElements = getElementsByClass("p_incomplete");
  // Set display elements
  document.getElementById("prod_generate_start").style.display = "none"; /* Make the button disappear */
  document.getElementById("load_producer_html").style.display = "none"; /* Hide the html link until regenerated */
  document.getElementById("prod_progress").style.display = "block"; /* Show the progress bar */
  document.getElementById("cust_generate_button").disabled = "true"; /* Disable the other button */
  i = 0;
  }

function compile_producer_invoices() {
  //iterate over the <li> array elements:
  if (i < p_arrElements.length) {
    //check that this is the proper class
    if (p_arrElements[i].attributes["class"].value == 'p_incomplete') {
      // Get the id of the element (that is the basket number, formatted like: basket_id2147
//      alert ("DATA: "+data);
      var element_id = p_arrElements[i].attributes["id"].value;
      $.post("/shop/ajax/compile_producer_invoices.php", { query_data: ""+element_id+":<?php echo $delivery_id; ?>" }, function(data) {
        if(data == "GENERATED_INVOICE") {
          var oldHTML = document.getElementById('producerList').innerHTML;
          var p_progress_left = Math.floor (300 * i / p_arrElements.length);
          var p_progress_right = 300 - p_progress_left;
          document.getElementById("p_progress-left").style.width = p_progress_left+"px";
          document.getElementById("p_progress-left").innerHTML = Math.floor (p_progress_left / 3)+"%&nbsp;";
          document.getElementById("p_progress-right").style.width = p_progress_right+"px";
          document.getElementById(element_id).className = "p_complete";
          // If we're done with the list, then show the PDF button
          if (i == p_arrElements.length) {
            // And go generate the pdf
            document.getElementById("prod_progress").style.display = "none"; /* Hide the progress bar */
            document.getElementById("load_producer_html").style.display = ""; /* Make html link visible */
            <? if (USE_HTMLDOC) { echo 'prod_generate_pdf();'; } else { ?>
            document.getElementById("cust_generate_button").disabled = ""; /* Re-enable the other button */
            document.getElementById("prod_generate_start").style.display = ""; /* Bring back the button */
            document.getElementById("prod_html2pdf_message").style.display = ""; /* Hide the html2pdf conversion message */
            <?php } ?>
            };
          // Continue cycling through this loop
          compile_producer_invoices ();
          }
        else {
          alert ("ERROR P2: "+data+" \r\nPlease try again");
          }
        });
      }
    i++;
    }
  }

function prod_generate_pdf() {
  document.getElementById("load_producer_pdf").style.display = "none"; /* Hide the pdf link until regenerated */
  document.getElementById("prod_html2pdf_message").style.display = "block"; /* Show the html2pdf conversion message */
  // Delete the old pdf file before continuing
  $.post("/shop/ajax/compile_producer_invoices.php", { query_data: "delete_pdf:"+"<?php echo $delivery_id; ?>" }, function(data) {
    if (data != "DELETED_PDF") {
      alert ("ERROR P3: "+data+" \r\nPlease try again");
      }
    })
  $.post("/shop/ajax/compile_producer_invoices.php", { query_data: "html2pdf:"+"<?php echo $delivery_id; ?>" }, function(data) {
    if(data != "HTML2PDF") {
      alert ("ERROR P4: "+data+" \r\nPlease try again");
      }
    document.getElementById("load_producer_pdf").style.display = ""; /* Make pdf link visible */
    document.getElementById("cust_generate_button").disabled = ""; /* Re-enable the other button */
    document.getElementById("prod_generate_start").style.display = ""; /* Bring back the button */
    document.getElementById("prod_html2pdf_message").style.display = ""; /* Hide the html2pdf conversion message */
    })
  }

</script>

<style type="text/css">
body {
  font-family: Helvetica;
  font-size: 11px;
  color: #000;
  }

h3 {
  margin: 0px;
  padding: 0px;
  }

li.c_complete a, li.p_complete a {
  color: #000;
  margin: 0;
  padding: 0;
  border: 0;
  }

li.c_incomplete a, li.p_incomplete a {
  color: #ddd;
/*  height: 0; */
  margin: 0;
  padding: 0;
  border: 0;
  }

#left-column {
  float:left;
  margin: 0px;
  width: 49%;
  }

#right-column {
  float:right;
  margin: 0px;
  width: 49%;
  }

#customerBox {
  clear:both;
/*  position: relative; */
  margin: auto;
  margin-top:10px;
  width: 80%;
  height:500px;
  overflow:auto;
  background-color: #fff;
  -moz-border-radius: 7px;
/*  -webkit-border-radius: 7px; */
  border: 2px solid #000;
  }

#producerBox {
  clear:both;
  position: relative;
  margin: auto;
  margin-top:10px;
  width: 80%;
  height:500px;
  overflow:auto;
  background-color: #fff;
  -moz-border-radius: 7px;
/*  -webkit-border-radius: 7px; */
  border: 2px solid #000;
  }

input {
  display:block;
  margin: auto;
  margin-top:10px;
  }

.customerList {
  margin: 0px;
  padding: 0px;
  }

ul {
  list-style-type:none;
  padding-left:5px;
  }

/*
.customerList li {
  margin: 0px 0px 3px 0px;
  cursor: pointer;
  } */

.customerList li:hover {
  background-color: #ccc;
  color:#000;
  }

.producerList {
  margin: 0px;
  padding: 0px;
  }

/*
.producerList li {
  margin: 0px 0px 3px 0px;
  cursor: pointer;
  } */

.producerList li:hover {
  background-color: #ccc;
  color:#000;
  }

#cust_progress, #prod_progress {
  display:none;
  position: relative;
  margin: auto;
  margin-top:10px;
  height:20px;
  border: 2px solid #000;
  width: 301px;
  }

#p_progress-left, #c_progress-left {
  float:left;
  border-right: 1px solid #000;
  width: 0px;
  height:20px;
  background: #ff0;
  text-align:left;
  }

#p_progress-right #c_progress-right {
  float:right;
  border: 0;
  width: 300px;
  height:20px;
  background: #aaa;
  text-align:left;
  }

a:link, a:visited {
  text-decoration:none;
  color:#228;
  }

a:hover {
  text-decoration:underline;
  color:#161;
  }

h1 {
  text-align:center;
  }

.navigation {
  text-align:center;
  background-color:#edc;
  color:#000;
  width: 25em;
  margin:auto;
  padding: 5px;
  margin-bottom:3em;
  border:1px solid #420;
  }

#cust_html_link,
#cust_pdf_link,
#cust_generate_start,
#cust_html2pdf_message,
#prod_html_link,
#prod_pdf_link,
#prod_generate_start,
#prod_html2pdf_message {
  clear:both;
  width:80%;
  height:30px;
  margin:auto;
  font-size:1.3em;
  text-align:center;
  }

#prod_html2pdf_message,
#cust_html2pdf_message {
  display:none;
  color: #a00;
  font-size:1.3em;
  }

#cust_pdf_link,
#prod_pdf_link {
  display:<?php if (USE_HTMLDOC) {echo 'block'; } else { echo 'none'; } ?>;
  }

.p_list_pid {
  width:5em;
  float:left;
  text-align:center;
  padding-right:1em;
  font-family:verdana;
  }

.c_list_cid strong,
.p_list_pid strong {
  color:#a22;
  }

.p_list_name {
  padding-left:6em;
  font-family:verdana;
  }

.p_list_header {
  position:relative;
  font-weight:bold;
  text-decoration:underline;
  color:#008;
  }

.c_list_cid {
  width:3em;
  float:left;
  text-align:right;
  padding-right:1em;
  font-family:verdana;
  }

.c_list_name {
  padding-left:4em;
  font-family:verdana;
  }

.c_list_header {
  position:relative;
  font-weight:bold;
  text-decoration:underline;
  color:#008;
  }

</style>
</head>
<body>
<?php


$prior_delivery_link = ' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ';
$next_delivery_link = ' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ';
if ($delivery_id > 1)
  {
    $prior_delivery_link = '<a href="'.$_SERVER['PHP_SELF'].'?delivery_id='.number_format($delivery_id - 1, 0).'">&larr; PRIOR &#151;</a>';
  }
if ($delivery_id < $current_delivery_id)
  {
    $next_delivery_link = '<a href="'.$_SERVER['PHP_SELF'].'?delivery_id='.number_format($delivery_id + 1, 0).'">&#151; NEXT &rarr;</a>';
  }


echo '
<h1>Generate Invoices for Delivery #'.$delivery_id.'<br>'.$delivery_date.'</h1>
<p class="navigation">'.$prior_delivery_link.' &nbsp; &nbsp; OTHER ORDERS &nbsp; &nbsp; '.$next_delivery_link.'</p>';


$customer_output_html = INVOICE_WEB_PATH.'invoices_customers-'.$delivery_id.'.html';
$customer_output_pdf = INVOICE_WEB_PATH.'invoices_customers-'.$delivery_id.'.pdf';


if (! file_exists(INVOICE_FILE_PATH.'invoices_customers-'.$delivery_id.'.html'))
  {
    $cust_view_html = ' style="display:none;"';
  }
if (! file_exists(INVOICE_FILE_PATH.'invoices_customers-'.$delivery_id.'.pdf'))
  {
    $cust_view_pdf = ' style="display:none;"';
  }

echo '
<div id="left-column">
  <div id="cust_control">
    <div id="cust_html_link"><a id="load_customer_html" href="'.$customer_output_html.'" target="_blank"'.$cust_view_html.'>View Customer Invoices (HTML)</a></div>
    <div id="cust_pdf_link"><a id="load_customer_pdf" href="'.$customer_output_pdf.'" target="_blank"'.$cust_view_pdf.'>View Customer Invoices (PDF)</a></div>
    <div id="cust_generate_start"><input id="cust_generate_button" type="submit" onClick="reset_cust_list(); cust_generate_start(); compile_customer_invoices();" value="Generate Customer Invoices"></div>
    <div id="cust_html2pdf_message">Converting HTML to PDF... <blink>please wait</blink></div>
    <div id="cust_progress"><div id="c_progress-left"></div><div id="c_progress-right"></div></div>
  </div>
  <div id="customerBox">
    <div class="customerList" id="customerList">
      <ul>
        <li><div class="c_list_header c_list_cid">ID</div><div class="c_list_header c_list_name">Destination Hub: Delcode [Name]</div></a></li>';

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
  FROM
    (
      '.TABLE_BASKET_ALL.',
      '.TABLE_DELCODE.'
    )
  LEFT JOIN '.TABLE_MEMBER.' ON '.TABLE_BASKET_ALL.'.member_id = '.TABLE_MEMBER.'.member_id
  WHERE
    '.TABLE_BASKET_ALL.'.member_id IS NOT NULL
    AND '.TABLE_BASKET_ALL.'.delivery_id = "'.$delivery_id.'"
    AND '.TABLE_BASKET_ALL.'.delcode_id = '.TABLE_DELCODE.'.delcode_id
  GROUP BY
    '.TABLE_BASKET_ALL.'.member_id
  ORDER BY
    '.TABLE_DELCODE.'.hub ASC,
    '.TABLE_BASKET_ALL.'.delcode_id ASC,
    '.TABLE_MEMBER.'.last_name ASC,
    '.TABLE_MEMBER.'.first_name ASC'; ///////// LIMIT 0,5//////////////////////////////////////////////////////             CHANGE THIS VALUE !!!!

$result= mysql_query("$query") or die("Error: " . mysql_error());
while($row = mysql_fetch_array($result))
  {
    $hub = $row['hub'];
    $basket_id = $row['basket_id'];
    $delcode_id = $row['delcode_id'];
    $member_id = $row['member_id'];
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];



    $sql = '
      SELECT '.TABLE_BASKET_ALL.'.basket_id
      FROM
        '.TABLE_BASKET.',
        '.TABLE_BASKET_ALL.',
        '.TABLE_PRODUCT.'
      WHERE
        '.TABLE_BASKET_ALL.'.delivery_id = "'.$delivery_id.'"
        AND '.TABLE_BASKET.'.basket_id = '.TABLE_BASKET_ALL.'.basket_id
        AND '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id
        AND '.TABLE_BASKET_ALL.'.member_id = "'.$member_id.'"
        AND '.TABLE_BASKET.'.out_of_stock != "1"
        AND '.TABLE_PRODUCT.'.random_weight = "1"
        AND '.TABLE_BASKET.'.total_weight <= "0"';
    $rs = @mysql_query($sql,$connection) or die("Couldn't execute query.");
    $qty_need_weight = mysql_num_rows ($rs);
    if ($qty_need_weight == 0)
      {
        $ready_begin = '';
        $ready_end = '';
      }
    else
      {
        $ready_begin = '<strong>';
        $ready_end = '</strong>';
      }


    echo stripslashes ('          <li id="basket_id'.$basket_id.'" class="c_complete"><a href="orders_invoice.php?delivery_id='.$delivery_id.'&basket_id='.$basket_id.'&member_id='.$member_id.' " target="_blank"><div class="c_list_cid">'.$ready_begin.$row['member_id'].$ready_end.'</div><div class="c_list_name">'.$hub.': '.$delcode_id.' ['.$last_name.', '.$first_name.']</div></a></li>');
  }

?>

      </ul>
    </div>
  </div>
</div>

<?php
////////////////////////////////////  END CUSTOMER SECTION AND BEGIN PRODUCER SECTION ////////////////////////////////

$producer_output_html = INVOICE_WEB_PATH.'invoices_producers-'.$delivery_id.'.html';
$producer_output_pdf = INVOICE_WEB_PATH.'invoices_producers-'.$delivery_id.'.pdf';

if (! file_exists(INVOICE_FILE_PATH.'invoices_producers-'.$delivery_id.'.html'))
  {
    $prod_view_html = ' style="display:none;"';
  }
if (! file_exists(INVOICE_FILE_PATH.'invoices_producers-'.$delivery_id.'.pdf'))
  {
    $prod_view_pdf = ' style="display:none;"';
  }

echo '
<div id="right-column">
  <div id="cust_control">
    <div id="prod_html_link"><a id="load_producer_html" href="'.$producer_output_html.'" target="_blank"'.$prod_view_html.'>View Producer Invoices (HTML)</a></div>
    <div id="prod_pdf_link"><a id="load_producer_pdf" href="'.$producer_output_pdf.'" target="_blank"'.$prod_view_pdf.'>View Producer Invoices (PDF)</a></div>
    <div id="prod_generate_start"><input id="prod_generate_button" type="submit" onClick="reset_prod_list(); prod_generate_start(); compile_producer_invoices();" value="Generate Producer Invoices"></div>
    <div id="prod_html2pdf_message">Converting HTML to PDF... <blink>please wait</blink></div>
    <div id="prod_progress"><div id="p_progress-left"></div><div id="p_progress-right"></div></div>
  </div>
  <div id="producerBox">
    <div class="producerList" id="producerList">
      <ul>
        <li><div class="p_list_header p_list_pid">ID</div><div class="p_list_header p_list_name">Business Name</div></a></li>';

  $sqlp2 = '
    SELECT
      '.TABLE_PRODUCER.'.producer_id,
      '.TABLE_PRODUCER.'.member_id,
      '.TABLE_MEMBER.'.member_id,
      '.TABLE_MEMBER.'.business_name,
      '.TABLE_MEMBER.'.last_name,
      '.TABLE_BASKET.'.product_id,
      '.TABLE_PRODUCT.'.producer_id,
      '.TABLE_PRODUCT.'.product_id,
      '.TABLE_BASKET_ALL.'.delivery_id,
      '.TABLE_BASKET_ALL.'.basket_id,
      '.TABLE_BASKET.'.basket_id
    FROM
      '.TABLE_PRODUCER.'
    LEFT JOIN '.TABLE_MEMBER.' ON '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
    LEFT JOIN '.TABLE_PRODUCT.' ON '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
    LEFT JOIN '.TABLE_BASKET.' ON '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id
    LEFT JOIN '.TABLE_BASKET_ALL.' ON '.TABLE_BASKET.'.basket_id = '.TABLE_BASKET_ALL.'.basket_id
    WHERE
      '.TABLE_BASKET_ALL.'.delivery_id = "'.$delivery_id.'"
    GROUP BY
      '.TABLE_PRODUCER.'.producer_id
    ORDER BY
      business_name ASC, last_name ASC'; /// LIMIT 0,5//////////////////////////////////////////////////////////             CHANGE THIS VALUE !!!!

$resultp= mysql_query("$sqlp2") or die("Error: " . mysql_error());
while($row = mysql_fetch_array($resultp))
  {
    $producer_id = $row['producer_id'];
    $business_name = $row['business_name'];
    $sql = '
      SELECT '.TABLE_BASKET_ALL.'.basket_id
      FROM
        '.TABLE_BASKET.',
        '.TABLE_BASKET_ALL.',
        '.TABLE_PRODUCT.'
      WHERE
        '.TABLE_BASKET_ALL.'.delivery_id = "'.$delivery_id.'"
        AND '.TABLE_BASKET.'.basket_id = '.TABLE_BASKET_ALL.'.basket_id
        AND '.TABLE_BASKET.'.product_id = '.TABLE_PRODUCT.'.product_id
        AND '.TABLE_PRODUCT.'.producer_id = "'.$producer_id.'"
        AND '.TABLE_BASKET.'.out_of_stock != "1"
        AND '.TABLE_PRODUCT.'.random_weight = "1"
        AND '.TABLE_BASKET.'.total_weight <= "0"';
    $rs = @mysql_query($sql,$connection) or die("Couldn't execute query.");
    $qty_need_weight = mysql_num_rows ($rs);
    if ($qty_need_weight == 0)
      {
        $ready_begin = '';
        $ready_end = '';
      }
    else
      {
        $ready_begin = '<strong>';
        $ready_end = '</strong>';
      }
    echo stripslashes ('          <li id="producer_id'.$producer_id.'" class="p_complete"><a href="orders_prdcr_cust_storage.php?producer_id='.$producer_id.'&delivery_id='.$delivery_id.'&display_only=true" target="_blank"><div class="p_list_pid">'.$ready_begin.$producer_id.$ready_end.'</div><div class="p_list_name">'.$business_name.'</div></a></li>');
    //  $invoicep .= prdcrinvoice($producer_id, $delivery_id);
    //  $invoicep .= "\n<HR BREAK>\n";
  }
?>

      </ul>
    </div>
  </div>
</div>


<div style="clear:both;">&nbsp;</div>
<?php

include("template_footer_orders_edit.php");

?>
 