<?php

function prdcr_contact_info($start, $half)
  {
    global $connection;
    $sqlp = '
      SELECT
        '.TABLE_PRODUCER.'.producer_id,
        '.TABLE_PRODUCER.'.member_id,
        '.TABLE_MEMBER.'.*,
        '.TABLE_PRODUCER.'.donotlist_producer
      FROM
        '.TABLE_PRODUCER.',
        '.TABLE_MEMBER.'
      WHERE
        '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
        AND '.TABLE_PRODUCER.'.donotlist_producer != "1"
        AND '.TABLE_MEMBER.'.membership_discontinued != "1"
      ORDER BY
        business_name ASC,
        last_name ASC
      LIMIT '.$start.', '.$half.'';
    $resultp = @mysql_query($sqlp, $connection) or die("Error: Could not get producer contact information");
    while ( $row = mysql_fetch_array($resultp) )
      {
        $producer_id = $row['producer_id'];
        $business_name = stripslashes ($row['business_name']);
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $address_line1 = $row['address_line1'];
        $address_line2 = $row['address_line2'];
        $city = $row['city'];
        $state = $row['state'];
        $zip = $row['zip'];
        include("../func/show_name.php");
        $display .= $show_name.'</b><br>';
        $display .= $address_line1.'<br>';
        if($address_line2)
          {
            $display .= $address_line2.'<br>';
          }
        $display .= $city.', '.$state.' '.$zip.'<br>';
        $display .= '<br>';
      }
    return $display;
  }