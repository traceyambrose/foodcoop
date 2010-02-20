<?php

include_once ("config_foodcoop.php");

if ($_GET['queryString'])
  {
  $_POST['queryString'] = $_GET['queryString'];
  }

// Requests will be passed in the form: ##|String where ## is the delivery_id

list ($action, $delivery_id, $search_string) = explode ('|', $_POST['queryString']);

if(strlen($search_string) > 0 && $action == "get_cust_prod")
  {
    // Query to look for producers
    $query = '
      SELECT
        '.TABLE_PRODUCER.'.producer_id,
        '.TABLE_MEMBER.'.business_name
      FROM
        '.TABLE_PRODUCER.'
      LEFT JOIN '.TABLE_MEMBER.' ON '.TABLE_MEMBER.'.member_id = '.TABLE_PRODUCER.'.member_id
      WHERE
        producer_id LIKE "'.$search_string.'%"
        OR business_name LIKE "%'.$search_string.'%"
      ORDER BY
        business_name,
        producer_id
      LIMIT 0,10';
    $result = @mysql_query($query, $connection) or die(mysql_error());
    while ($row = mysql_fetch_object($result))
      {
        $row->business_name = htmlentities (stripslashes ($row->business_name), ENT_QUOTES);
        echo '<li class="producer_select" onClick="fill_producer_id(\''.$row->producer_id.'\');fill_producer_name(\''.$row->business_name.'\');">'.$row->producer_id.': '.$row->business_name.'</li>';
      }

    // Query to look for customers
    $query = '
      SELECT
        '.TABLE_MEMBER.'.first_name,
        '.TABLE_MEMBER.'.last_name,
        '.TABLE_MEMBER.'.first_name_2,
        '.TABLE_MEMBER.'.last_name_2,
        '.TABLE_MEMBER.'.business_name,
        '.TABLE_MEMBER.'.member_id
      FROM
        '.TABLE_MEMBER.'
      WHERE
        member_id = "'.$search_string.'"
        OR first_name LIKE "'.$search_string.'%"
        OR last_name LIKE "'.$search_string.'%"
        OR first_name_2 LIKE "'.$search_string.'%"
        OR last_name_2 LIKE "'.$search_string.'%"
        OR business_name LIKE "%'.$search_string.'%"
      ORDER BY
        last_name,
        last_name_2,
        business_name
      LIMIT 0,10';

// echo "<pre>$query</pre>";

    $result = @mysql_query($query, $connection) or die(mysql_error());
    while ($row = mysql_fetch_object($result))
      {
        $member_name = '';
        // Set up to translate Andy Johnson & Betty Johnson to Andy & Betty Johnson
        if ($row->last_name && $row->last_name == $row->last_name_2)
          {
            $row->last_name = '';
          }
        // Set up to translate Andy Johnson & Betty to Andy & Betty Johnson
        if ($row->last_name && $row->first_name_2 && ! $row->last_name_2)
          {
            $row->last_name_2 = $row->last_name;
            $row->last_name = '';
          }
        // Join first and last name with a space (or not)
        if ($row->first_name && $row->last_name)
          {
            $member_name1 = $row->first_name.' '.$row->last_name;
          }
        else
          {
            $member_name1 = $row->first_name.$row->last_name;
          }
        // Join first_2 and last_2 name with a space (or not)
        if ($row->first_name_2 && $row->last_name_2)
          {
            $member_name2 = $row->first_name_2.' '.$row->last_name_2;
          }
        else
          {
            $member_name2 = $row->first_name_2.$row->last_name_2;
          }
        // Join name and name_2 with & (or not)
        if ($member_name1 && $member_name2)
          {
            $member_name = $member_name1.' & '.$member_name2;
          }
        else
          {
            $member_name = $member_name1.$member_name2;
          }
        // Add the business name if there is one
        if ($row->business_name)
          {
            $member_name .= ' ('.$row->business_name.')';
          }
        $member_name = htmlentities (stripslashes ($member_name), ENT_QUOTES);
        echo '<li class="customer_select" onClick="fill_customer_id(\''.$row->member_id.'\');fill_customer_name(\''.$member_name.'\');">'.$row->member_id.': '.$member_name.'</li>';
      }
  }
?>
 
 