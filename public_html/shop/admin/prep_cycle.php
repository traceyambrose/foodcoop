<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

$cycle_time = DAYS_PER_CYCLE * 24 * 3600; // Period in seconds for typical cycles

/////////////////////////////////////////////////////////////////////////////////////
///                                                                               ///
///                                  FUNCTIONS                                    ///
///                                                                               ///
/////////////////////////////////////////////////////////////////////////////////////

function getTableList () {
  $tables = mysql_list_tables(DB_NAME);
  $table_names = array ();
  for($i = 0; $i < mysql_num_rows($tables); $i++) {
    $table = mysql_tablename ($tables, $i);
    array_push ($table_names, $table);
    }
  return ($table_names);
  }

function doTextDiff ($string1, $string2, $diff_open, $diff_close) {
  // Note... this will not work for words or non-breaking combinations longer than
  // 61 characters.  Limitations: Uses embedded text that must not match any strings
  // being processed.  Using the shell diff... is there a good php way to do this???
  
  // Use /tmp directory
  // Use file1.tmp and file2.tmp
  
  $file1 = '/tmp/file1.tmp';
  if (!file_exists($file1)) { touch ($file1); }
  $handle1 = fopen ($file1, 'w');
//  $string1 = str_replace("<", "&lt;", $string1);
  $string1 = nl2br ($string1);
  $string1 = str_replace(" ", "\n", $string1);
  $string1 = strip_tags ($string1, '<br>');
  fwrite ($handle1, $string1);
  fclose ($handle1);
  
  $file2 = '/tmp/file2.tmp';
  if (!file_exists($file2)) { touch ($file2); }
  $handle2 = fopen ($file2, 'w');
//  $string2 = str_replace("<", "&lt;", $string2);
  $string2 = nl2br ($string2);
  $string2 = str_replace(" ", "\n", $string2);
  $string2 = strip_tags ($string2, '<br>');
  fwrite ($handle2, $string2);
  fclose ($handle2);
  
  // Get the diff output
  $raw_diff = explode ("\n", shell_exec("diff -ad --side-by-side $file1 $file2"));
  
  // Now figure out the results...
  $out1 = array (); $out2 = array ();
  
  // make sure diffs start out closed
  $diff1 = false;
  $diff2 = false;
  foreach ($raw_diff as $line) {
    $word1 = ""; $word2 = ""; $diff = "";
    $line_parts = explode ("\t", $line);
    $word1 = trim (array_shift ($line_parts));
    $word2 = trim (array_pop ($line_parts));
    $diff = trim (array_pop ($line_parts));
    if ($word2 == "<") {
      $word2 = "";
      $diff = "<";
      }
    if ($diff == "") {
      if ($diff1 == true) { array_push ($out1, "STOP_DIFF"); $diff1 = false; }
      if ($diff2 == true) { array_push ($out2, "STOP_DIFF"); $diff2 = false; }
      array_push ($out1, $word1);
      array_push ($out2, $word2);
      }
    if ($diff == "<") {
      if ($diff1 == false) { array_push ($out1, "START_DIFF"); $diff1 = true; }
      array_push ($out1, $word1);
      }
    if ($diff == ">") {
      if ($diff2 == false) { array_push ($out2, "START_DIFF"); $diff2 = true; }
      array_push ($out2, $word2);
      }
    if ($diff == "|") {
      if ($diff1 == false) { array_push ($out1, "START_DIFF"); $diff1 = true; }
      if ($diff2 == false) { array_push ($out2, "START_DIFF"); $diff2 = true; }
      array_push ($out1, $word1);
      array_push ($out2, $word2);
      }
    }
  // make sure we close any open diffs
  if ($diff1 == true) { array_push ($out1, "STOP_DIFF"); }
  if ($diff2 == true) { array_push ($out2, "STOP_DIFF"); }
  $output1 = implode (" ", $out1);
  $output1 = str_replace ("START_DIFF", $diff_open, $output1);
  $output1 = str_replace ("STOP_DIFF", $diff_close, $output1);
  $output1 = str_replace ("&lt;", "<", $output1);
  $output2 = implode (" ", $out2);
  $output2 = str_replace ("START_DIFF", $diff_open, $output2);
  $output2 = str_replace ("STOP_DIFF", $diff_close, $output2);
  $output2 = str_replace ("&lt;", "<", $output2);
  
  return (array($output1, $output2));
  }


$action = $_POST['action'];

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
///                     BEGIN NEW PAGE - NO SUBMITTED DATA                   ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

if (!$action || $action == "Change setup") {
  // This is a new page visit so start out with the forms
  $query = '
    SELECT
      * 
    FROM
      '.TABLE_CURDEL;
  $result = @mysql_query($query, $connection) or die("<br><br>Whoops! You found a bug. If there is an error listed below, please copy and paste the error into an email to <a href=\"mailto:webmaster@$domainname\">webmaster@$domainname</a><br><br><b>Error:</b> Current Delivery Cycle " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
  $row = mysql_fetch_array($result); // Only need the first row
  $prior_current_delivery_delivery_id = $row['delivery_id'];
  $prior_current_delivery_open = $row['open'];
  $prior_current_delivery_date_open = $row['date_open'];
  $prior_current_delivery_delivery_date = $row['delivery_date'];
  $prior_current_delivery_order_cycle_closed = $row['order_cycle_closed'];
  $prior_current_delivery_closing_timestamp = $row['closing_timestamp'];
  $prior_current_delivery_date_closed = $row['date_closed'];
  $prior_current_delivery_msg_all = $row['msg_all'];
  $prior_current_delivery_msg_bottom = $row['msg_bottom'];
  $prior_current_delivery_special_order = $row['special_order'];

  // Get the delivery date for the prior cycle
  $query = '
    SELECT
      * 
    FROM
      '.TABLE_DELDATE.'
    ORDER BY
      delivery_date DESC
    LIMIT
      1';
  $result = @mysql_query($query, $connection) or die("<br><br>Whoops! You found a bug. If there is an error listed below, please copy and paste the error into an email to <a href=\"mailto:webmaster@$domainname\">webmaster@$domainname</a><br><br><b>Error:</b> Current Delivery Cycle " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
  $row = mysql_fetch_array($result); // Only need the first row
  $prior_delivery_dates_delivery_id = $row['delivery_id'];
  $prior_delivery_dates_delivery_date = $row['delivery_date'];
  $prior_delivery_dates_special_order = $row['special_order'];
  $prior_delivery_dates_coopfee = $row['coopfee'];

  $prior_delivery = date ("Y-m-d", strtotime($prior_delivery_dates_delivery_date));

  // Find out if there is a database table named for that prior delivery date...
  if ($prior_delivery) {
    $target_table = "product_list_".$prior_delivery;
    }
  elseif ($_POST['target_table']) {
    $target_table = $_POST['target_table'];
    }


  echo '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">'."\n";
  echo 'The following processes will take place when the form is submitted:'."\n";
  echo '<ul>'."\n";

  echo '<li><p>The <font color="navy">product_list_previous</font> table will be overwritten with the copied table from last month: ';
  $table_list = getTableList();

  if (array_search  ($target_table, $table_list)) {
    echo '<font color="navy">'.$target_table.'</font>, which <em><u>does</u></em> exist.'."\n"; // The table exists in the database
    echo '<input type="hidden" name="prior_product_list" value="'.$target_table.'">'."\n";
    }
  else {
    echo 'The expected table does not exist. Please <select name="prior_product_list">'."\n";
    $no_target_table = true;
    echo '<select name="target_table">'."\n";
    echo '<option>-- select the table --</option>'."\n";
    foreach ($table_list as $table) {
      echo '<option value="'.$table.'">'.$table.'</option>'."\n";
      }
    echo '</select>'."\n";
    }
  echo '</p></li>'."\n";
  echo '
  <li><p>The delivery_dates table will be updated for the new order cycle:<br>
  <table border="1">
    <tr>
      <th valign="top">Field</th>
      <th valign="top">Prior Value</th>
      <th valign="top">New Value</th>
      <th valign="top">Notes</th>
    </tr>
    <tr>
      <td valign="top">delivery_id</td>
      <td valign="top">'.$prior_delivery_dates_delivery_id.'</td>
      <td valign="top"><input size="5" maxlength="4" value="'.($prior_delivery_dates_delivery_id + 1).'" disabled>
                       <input type="hidden" name="delivery_id" value="'.($prior_delivery_dates_delivery_id + 1).'"></td>
      <td valign="top">This may not be changed</td>
    </tr>
    <tr>
      <td valign="top">delivery_date</td>
      <td valign="top">'.$prior_delivery_dates_delivery_date.'</td>
      <td valign="top"><input name="delivery_date" size="10" maxlength="10" value="'.date ('Y-m-d', (strtotime ($prior_delivery_dates_delivery_date) + $cycle_time)).'"></td>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td valign="top">special_order</td>
      <td valign="top">';
      if ($prior_delivery_dates_special_order == 1) { echo "Yes"; };
      if ($prior_delivery_dates_special_order == 0) { echo "No"; };
      echo '</td>
      <td valign="top"><select name="special_order"><option value="0" selected>No</option><option value="1">Yes</option></select></td>
      <td valign="top">Select only if this is a special order</td>
    </tr>
    <tr>
      <td valign="top">coopfee</td>
      <td valign="top">$ '.number_format ($prior_delivery_dates_coopfee, 2).'</td>
      <td valign="top">$ <input name="coopfee" size="10" maxlength="5" value="'.number_format ($prior_delivery_dates_coopfee, 2).'"></td>
      <td valign="top">Fee charged to each order</td>
    </tr>
  </table>
  </p></li>';
  echo '
  <li><p>The current_delivery table will be updated for the new order cycle:<br>
  <table border="1">
    <tr>
      <th valign="top">Field</th>
      <th valign="top">Prior Value</th>
      <th valign="top">New Value</th>
      <th valign="top">Notes</th>
    </tr>
    <tr>
      <td valign="top">delivery_id</td>
      <td valign="top">'.$prior_current_delivery_delivery_id.'</td>
      <td valign="top"><input name="delivery_id" size="5" maxlength="4" value="'.($prior_current_delivery_delivery_id + 1).'" disabled></td>
      <td valign="top">This may not be changed</td>
    </tr>
    <tr>
      <td valign="top">open</td>
      <td valign="top">';
  if ($prior_current_delivery_delivery_open == 1) { echo "Yes"; };
  if ($prior_current_delivery_delivery_open == 0) { echo "No"; };
      echo '</td>
      <td valign="top"><select name="open"><option value="0" selected>No</option><option value="1">Yes</option></select></td>
      <td valign="top">Should be "No" unless the order is already opened</td>
    </tr>
    <tr>
      <td valign="top">date_open</td>
      <td valign="top">'.$prior_current_delivery_date_open.'</td>
      <td valign="top"><input name="date_open" size="25" maxlength="25" value="'.date ('Y-m-d H:i:s', (strtotime ($prior_current_delivery_date_open) + $cycle_time)).'"></td>
      <td valign="top">Date-time the order will automatically open.</td>
    </tr>
    <tr>
      <td valign="top">delivery_date</td>
      <td valign="top">'.$prior_current_delivery_delivery_date.'</td>
      <td valign="top"><input name="delivery_date" size="10" maxlength="10" value="'.date ('Y-m-d', (strtotime ($prior_current_delivery_delivery_date) + $cycle_time)).'"></td>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td valign="top">order_cycle_closed</td>
      <td valign="top">'.$prior_current_delivery_order_cycle_closed.'</td>
      <td valign="top"><input name="order_cycle_closed" size="25" maxlength="25" value="'.$prior_current_delivery_order_cycle_closed.'"></td>
      <td valign="top">Date-time the order will close (human-readable text for display on members page).</td>
    </tr>
    <tr>
      <td valign="top">closing_timestamp</td>
      <td valign="top">'.$prior_current_delivery_closing_timestamp.'</td>
      <td valign="top"><input name="closing_timestamp" size="12" maxlength="12" value="'.date ('YmdHi', (strtotime (substr ($prior_current_delivery_closing_timestamp, 0, 8).' '.substr ($prior_current_delivery_closing_timestamp, 8, 4)) + $cycle_time)).'"></td>
      <td valign="top">Closing timestamp (YYYYMMDDhhmm).</td>
    </tr>
    <tr>
      <td valign="top">date_closed</td>
      <td valign="top">'.$prior_current_delivery_date_closed.'</td>
      <td valign="top"><input name="date_closed" size="25" maxlength="25" value="'.date ('Y-m-d H:i:s', (strtotime ($prior_current_delivery_date_closed) + $cycle_time)).'"></td>
      <td valign="top">Date-time the order will automatically close.</td>
    </tr>
    <tr>
      <td valign="top">msg_all</td>
      <td valign="top" colspan="2"><textarea name="msg_all" rows="8" cols="60">'.preg_replace ('/\<br\>/', "\n", $prior_current_delivery_msg_all).'</textarea></td>
      <td valign="top">This message is usually configured to appear near the top of the invoice</td>
    </tr>
    <tr>
      <td valign="top">msg_bottom</td>
      <td valign="top" colspan="2"><textarea name="msg_bottom" rows="8" cols="60">'.preg_replace ('/\<br\>/', "\n", $prior_current_delivery_msg_bottom).'</textarea></td>
      <td valign="top">This message is usually configured to appear near the bottom of the invoice</td>
    </tr>
    <tr>
      <td valign="top">special_order</td>
      <td valign="top">';
  if ($prior_current_delivery_special_order == 1) { echo "Yes"; };
  if ($prior_current_delivery_special_order == 0) { echo "No"; };
      echo '</td>
      <td valign="top"><select name="special_order"><option value="0" selected>No</option><option value="1">Yes</option></select></td>
      <td valign="top">Select only if this is a special order</td>
    </tr>
  </table>
  </p></li>';

  // If we don't have a target_table, we can't do the next step
  if ($no_target_table == true) {
    echo '<input type="submit" name="action" value="Change setup"> &nbsp; &nbsp; &nbsp; <input type="reset" value="Reset values">';
    exit (1);
    }

  $query = '
    SELECT
      MAX(product_id) AS max_product_id 
    FROM
      `'.$target_table.'`
    WHERE
      1;';
  $result = @mysql_query($query, $connection) or die("<br><br>Whoops! You found a bug. If there is an error listed below, please copy and paste the error into an email to <a href=\"mailto:webmaster@$domainname\">webmaster@$domainname</a><br><br><b>Error:</b> Current Delivery Cycle " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
  $row = mysql_fetch_array($result); // Only need the first row
  $max_product_id = $row['max_product_id'];

  $query = '
    SELECT
      COUNT(product_id) AS count_new_products 
    FROM
      '.TABLE_PRODUCT.'
    WHERE
      product_id > "'.$max_product_id.'"';
  $result = @mysql_query($query, $connection) or die("<br><br>Whoops! You found a bug. If there is an error listed below, please copy and paste the error into an email to <a href=\"mailto:webmaster@$domainname\">webmaster@$domainname</a><br><br><b>Error:</b> Current Delivery Cycle " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
  $row = mysql_fetch_array($result); // Only need the first row
  $count_new_products = $row['count_new_products'];
  
  echo '<li><p>Products newer than product_id '.$max_product_id.' from the [newly copied] <font color="navy">product_list_previous</font> table ('.$count_new_products.' products) will be marked as NEW in the <font color="navy">product_list</font> and <font color="navy">product_list_prep</font> tables for this order cycle.</p>'."\n";

  echo '<input type="hidden" name="max_product_id_" value="'.$max_product_id.'">'."\n";
  echo '<input type="hidden" name="count_new_products" value="'.$count_new_products.'">'."\n";

  echo '<li><p>The following CHECKED products will be marked UNCHANGED in the database.  Please modify the selections as needed, even though they may have been changed only slightly.  Differences are shown in <font color="#A00000">red</font> and unchanged productes are shown in <font color="#c0c0c0">grey</font>).  Make no changes for default behavior.</p>'."\n";

  // Run the query to view changed/unchanged products
  $query = '
    SELECT
      p.product_id,
      p.product_name AS product_name_old,
      pp.product_name AS product_name_new,
      p.unit_price AS unit_price_old,
      pp.unit_price AS unit_price_new,
      p.detailed_notes AS detailed_notes_old,
      pp.detailed_notes AS detailed_notes_new,
      p.pricing_unit AS pricing_unit_old,
      pp.pricing_unit AS pricing_unit_new,
      p.ordering_unit AS ordering_unit_old,
      pp.ordering_unit AS ordering_unit_new,
      p.meat_weight_type AS meat_weight_type_old,
      pp.meat_weight_type AS meat_weight_type_new 
    FROM
      '.TABLE_PRODUCT_PREP.' p,
      '.TABLE_PRODUCT_PREV.' pp 
    WHERE
      p.product_id = pp.product_id 
      AND p.changed = "1"
      AND pp.donotlist = "0"
      AND p.donotlist = "0" 
    GROUP BY
      product_id;';
  $result = mysql_query($query,$connection) or die("<br><br>Whoops! You found a bug. If there is an error listed below, please copy and paste the error into an email to <a href=\"mailto:webmaster@$domainname\">webmaster@$domainname</a><br><br><b>Error:</b> Current Delivery Cycle " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
  echo "<table border='1'>".
       "<tr><th>[Count]<br>(Prod&nbsp;ID)</th><th>Unchanged?</th>".
         "<th>Product Name</th>".
         "<th>Unit Price</th>".
         "<th>Pricing Unit</th>".
         "<th>Ordering Unit</th>".
         "<th>Meat Weight Type</th>".
         "<th>Detailed Notes</th></tr>";

  while ($row = mysql_fetch_array($result)) {
    $product_id = $row['product_id'];
    $product_name_old = $row['product_name_old'];
    $product_name_new = $row['product_name_new'];
    $unit_price_old = $row['unit_price_old'];
    $unit_price_new = $row['unit_price_new'];
    $pricing_unit_old = $row['pricing_unit_old'];
    $pricing_unit_new = $row['pricing_unit_new'];
    $ordering_unit_old = $row['ordering_unit_old'];
    $ordering_unit_new = $row['ordering_unit_new'];
    $meat_weight_type_old = $row['meat_weight_type_old'];
    $meat_weight_type_new = $row['meat_weight_type_new'];
    $detailed_notes_old = $row['detailed_notes_old'];
    $detailed_notes_new = $row['detailed_notes_new'];

    if (($product_name_old == $product_name_new) &&
        ($unit_price_old == $unit_price_new) &&
        ($pricing_unit_old == $pricing_unit_new) &&
        ($ordering_unit_old == $ordering_unit_new) &&
        ($meat_weight_type_old == $meat_weight_type_new) &&
        ($detailed_notes_old == $detailed_notes_new)) { // Case where there is no change
      $changed = "false";
      $product_name_celltype = " style='color:#c0c0c0;'";
      $unit_price_celltype = " style='color:#c0c0c0;'";
      $pricing_unit_celltype = " style='color:#c0c0c0;'";
      $ordering_unit_celltype = " style='color:#c0c0c0;'";
      $meat_weight_type_celltype = " style='color:#c0c0c0;'";
      $detailed_notes_celltype = " style='color:#c0c0c0;'";
      }
    else { // Case where *something* changed
      $changed = "true";
      if ($product_name_old != $product_name_new) {
        list ($product_name_old, $product_name_new) = doTextDiff ($product_name_old, $product_name_new, '<font style="color:#A00000;">', '</font>');
        }
      if ($unit_price_old == $unit_price_new) {
        $unit_price_celltype = "";
        }
      else {
        $unit_price_celltype = " style='color:#A00000;'";
        }
      if ($pricing_unit_old == $pricing_unit_new) {
        $pricing_unit_celltype = "";
        }
      else {
        $pricing_unit_celltype = " style='color:#A00000;'";
        }
      if ($ordering_unit_old == $ordering_unit_new) {
        $ordering_unit_celltype = "";
        }
      else {
        $ordering_unit_celltype = " style='color:#A00000;'";
        }
      if ($meat_weight_type_old == $meat_weight_type_new) {
        $meat_weight_type_celltype = "";
        }
      else {
        $meat_weight_type_celltype = " style='color:#A00000;'";
        }
      if ($detailed_notes_old != $detailed_notes_new) {
        list ($detailed_notes_old, $detailed_notes_new) = doTextDiff ($detailed_notes_old, $detailed_notes_new, '<font style="color:#A00000;">', '</font>');
        }
      }
    if ($changed == "true") {
      $count_data = ++$count."<br>";
      }
    else {
      $count_data = "";
      }
    echo "<tr><td align='center'>$count_data($product_id)</td>".
         '<td align="center"><input type="checkbox" name="product_'.$product_id.'" value="unchanged"';
    if ($changed != "true") { echo ' CHECKED'; };
    echo '></td>'.
         "<td>$product_name_old<hr>$product_name_new</td>".
         "<td$unit_price_celltype>\$$unit_price_old<hr>\$$unit_price_new</td>".
         "<td$pricing_unit_celltype>$pricing_unit_old<hr>$pricing_unit_new</td>".
         "<td$ordering_unit_celltype>$ordering_unit_old<hr>$ordering_unit_new</td>".
         "<td$meat_weight_type_celltype>$meat_weight_type_old<hr>$meat_weight_type_new</td>".
         "<td>$detailed_notes_old<hr>$detailed_notes_new</td></tr>";
    }
  echo "</table>\n";
  echo '</li>'."\n";
  echo '</ul>'."\n";
  echo '<table border="0" width="100%"><tr>'."\n";
  echo '<td width="50%" align="center"><input type="submit" name="action" value="Process"></td>'."\n";
  echo '<td width="50%" align="center"><input type="reset"></td>'."\n";
  echo '</tr></table>'."\n";
  echo '</form>'."\n";

  echo '<hr>'."\n";
  }


////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
///                        BEGIN PROCESSING SUBMITTED PAGE                   ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

elseif ($action == "Process") {

  // Make sure all submitted data looks good...
  // Assume everything will go well
  unset ($error);

  // We should have all values and they should agree.
  $prior_product_list = $_POST['prior_product_list'];
  $coopfee = $_POST['coopfee'];
  $delivery_id = $_POST['delivery_id'];
  $open = $_POST['open'];
  $date_open = date ('Y-m-d H:i:s', strtotime ($_POST['date_open']));
  $delivery_date = date ('Y-m-d', strtotime ($_POST['delivery_date']));
  $order_cycle_closed = $_POST['order_cycle_closed'];
  $closing_timestamp = date ('YmdHis', strtotime ($_POST['closing_timestamp']));
  $date_closed = date ('Y-m-d H:i:s', strtotime ($_POST['date_closed']));
  $msg_all = addslashes (nl2br ($_POST['msg_all']));
  $msg_bottom = addslashes (nl2br ($_POST['msg_bottom']));
  $special_order = $_POST['special_order'];

///                                                                          ///
///                                  STEP ONE                                ///
///                                                                          ///

  $product_list = 'product_list_'.trim ($delivery_date);
if (strtotime ($delivery_date) < 1000) {
  $error_array[0] .= "Invalid delivery date!<br>\n";
    }
  $query_array[0] = '
    CREATE TABLE
      `'.DB_NAME.'`.`'.$product_list.'`
    SELECT
      *
    FROM
      `'.DB_NAME.'`.`'.TABLE_PRODUCT.'`';

///                                                                          ///
///                                  STEP TWO                                ///
///                                                                          ///

  $table_list = getTableList();
  if (! array_search ($prior_product_list, $table_list)) {
    $error_array[1] .= "Prior product table: $prior_product_list does not exist!<br>\n";
    }
  $query_array[1] = '
    DROP TABLE
      `'.TABLE_PRODUCT_PREV.'`';
  $query_array[2] = '
    ALTER TABLE
      `'.$prior_product_list.'`
    RENAME `'.TABLE_PRODUCT_PREV.'`';

///                                                                          ///
///                                 STEP THREE                               ///
///                                                                          ///

if ($special_order != 1 && $special_order != 0) {
  $error_array[3] .= "Special order is not 0 or 1!<br>\n";
    }
if (strtotime ($delivery_date) < 1000) {
  $error_array[3] .= "Invalid delivery date!<br>\n";
    }
if (! is_numeric ($coopfee)) {
  $error_array[3] .= "Coop fee is not a number!<br>\n";
    }

  $query_array[3] = '
    INSERT INTO
      `'.TABLE_DELDATE.'`
        (
          `delivery_id`,
          `delivery_date`,
          `special_order`,
          `coopfee`
        )
      VALUES
        (
          "'.$delivery_id.'",
          "'.$delivery_date.'",
          "'.$special_order.'",
          "'.$coopfee.'"
        )';

///                                                                          ///
///                                 STEP FOUR                                ///
///                                                                          ///

  $query = '
    SELECT
      MAX(delivery_id) AS last_delivery_id
    FROM
      `'.TABLE_DELDATE.'`';
  $result = @mysql_query($query, $connection) or $error_array[4] = "SQL Error while retrieving next delivery id from $table_deldate!\n";
  $row = mysql_fetch_array($result); // Only need the first row
  if ($row['last_delivery_id'] + 1 != $delivery_id) {
    $error_array[4] .= "Incorrect delivery_id!<br>\n";
    }
  if (strtotime ($date_open) < 1000) {
    $error_array[4] .= "Invalid date_open date!<br>\n";
    }
  if (strtotime ($closing_timestamp) < 1000) {
    $error_array[4] .= "Invalid closing_timestamp date!<br>\n";
    }
  if (strtotime ($date_closed) < 1000) {
    $error_array[4] .= "Invalid date_closed date!<br>\n";
    }
  if (strtotime ($delivery_date) < 1000) {
    $error_array[4] .= "Invalid delivery_date date!<br>\n";
    }

  $query_array[4] = '
    UPDATE
      `'.TABLE_CURDEL.'`
    SET
      delivery_id = "'.$delivery_id.'",
      open = "0",
      date_open = "'.$date_open.'",
      closing_timestamp = "'.$closing_timestamp.'",
      date_closed = "'.$date_closed.'",
      delivery_date = "'.$delivery_date.'",
      order_cycle_closed = "'.$order_cycle_closed.'",
      msg_all = "'.$msg_all.'",
      msg_bottom = "'.$msg_bottom.'",
      special_order = "0"';

///                                                                          ///
///                                 STEP FIVE                                ///
///                                                                          ///

  // We do all the queries together so this table hasn't been renamed yet,
  // Otherwise we would be querying product_list_previous.
  $query = '
    SELECT
      MAX( product_id ) AS max_id
    FROM
      `'.$prior_product_list.'`';
  $result = @mysql_query($query, $connection) or $error_array[5] = "SQL Error while retrieving maximum former product id!\n";
  $row = mysql_fetch_array($result); // Only need the first row
  $max_id = $row['max_id'];

  $query_array[5] = '
    UPDATE
      '.TABLE_PRODUCT.'
    SET
      new="0"
    WHERE
      product_id <= "'.$max_id.'"';
  $query_array[6] = '
    UPDATE
      '.TABLE_PRODUCT_PREP.'
    SET
      new="0"
    WHERE
      product_id <= "'.$max_id.'"';

///                                                                          ///
///                                 STEP SIX                                ///
///                                                                          ///

  // Get the products that *might* have changed (a shorter list than was shown earlier)
  $change_product_array = array ();
  $query = '
    SELECT
      p.product_id,
      p.product_name,
      p.unit_price,
      p.detailed_notes,
      pp.detailed_notes
    FROM
      '.TABLE_PRODUCT_PREP.' p,
      '.TABLE_PRODUCT_PREV.' pp
    WHERE
      p.product_id = pp.product_id
      AND p.changed = "1"
      AND p.product_name = pp.product_name
      AND p.unit_price = pp.unit_price
      AND p.pricing_unit = pp.pricing_unit
      AND p.ordering_unit = pp.ordering_unit
      AND p.detailed_notes = pp.detailed_notes
      AND p.meat_weight_type = pp.meat_weight_type
      AND pp.donotlist = "0"
      AND p.donotlist = "0"
    GROUP BY
      product_id';
  $result = @mysql_query($query, $connection) or $error_array[7] = "SQL Error while retrieving potentially changed products!\n";
  while ($row = mysql_fetch_array($result)) { // Only need the first row
    // Compare this list to see which were asked to be marked as "unchanged"
    $product_id = $row['product_id'];
    if ($_POST['product_'.$product_id] == "unchanged") {
      array_push ($change_product_array, "`p`.`product_id` = $product_id");
      }
    }
  $change_product_list = implode (" OR ", $change_product_array);
  if ($change_product_list == '')
    {
      // If there are no values, then make the "WHEN" condition to "1 = 0" so it is never true
      $change_product_list = '1 = 0';
    }
  $query_array[7] = '
    UPDATE
      '.TABLE_PRODUCT_PREP.' p,
      '.TABLE_PRODUCT_PREV.' pp
    SET
      p.changed = "0"
    WHERE
      '.$change_product_list;

///                                                                          ///
///                                   FINISH                                 ///
///                                                                          ///


  for ($step = 0; $step < 8; $step++ ) {
    if ($error_array[$step] == '')
      {
        $error_array[$step] = '-- NO ERRORS --';
      }
    echo '<font style="color:#a00000;">ERROR ['.$step.']: '.$error_array[$step].'</font><br>'."\n";
    echo 'QUERY ['.$step.']: '.$query_array[$step].'<br><hr>'."\n";
    }

  if (count ($error_list) > 0) {
    die ("ERRORS WERE FOUND... EXECUTION STOPPED BEFORE IT BEGINS");
    }
  else {
    for ($step = 0; $step < 8; $step++ ) {
      $result = @mysql_query($query_array[$step], $connection) or die ("PROCESS FAILED TO COMPLETE STEP $step!\n");
      // Give mysql a moment to catch it's breath
      echo "STEP $step COMPLETED SUCCESSFULLY<br>\n";
      sleep (1);
      }
    }

  }