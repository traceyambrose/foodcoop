<?php
$sqlm =
  'SELECT
    *
  FROM
    '.TABLE_MEMBER.'
  WHERE
    member_id = "'.$member_id.'"';
  $resultm = @mysql_query($sqlm,$connection) or die("Error: Could not get producer contact information");
while ( $row = mysql_fetch_array($resultm) )
  {
    $username_m = $row['username_m'];
    $address_line1 = $row['address_line1'];
    $address_line2 = $row['address_line2'];
    $city = $row['city'];
    $state = $row['state'];
    $zip = $row['zip'];
    $email_address = $row['email_address'];
    $email_address_2 = $row['email_address_2'];
    $home_phone = $row['home_phone'];
    $work_phone = $row['work_phone'];
    $mobile_phone = $row['mobile_phone'];
    $fax = $row['fax'];
    $toll_free = $row['toll_free'];
    $home_page = $row['home_page'];
    $membership_date = $row['membership_date'];
    $display .= "$address_line1<br>";
    if( $address_line2 )
      {
        $display .= $address_line2.'<br>';
      }
    $display .= $city.', '.$state.' '.$zip.'<br>';
    if ( $email_address )
      {
        $display .= '<a href="mailto:'.$email_address.'">'.$email_address.'</a><br>';
      }
    if ( $email_address_2 )
      {
        $display .= '<a href="mailto:'.$email_address_2.'">'.$email_address_2.'</a><br>';
      }
    if ( $home_phone )
      {
        $display .= $home_phone.' (home)<br>';
      }
    if ( $work_phone )
      {
        $display .= $work_phone.' (work)<br>';
      }
    if ( $mobile_phone )
      {
        $display .= $mobile_phone.' (cell)<br>';
      }
    if ( $fax )
      {
        $display .= $fax.' (fax)<br>';
      }
    if ( $toll_free )
      {
        $display .= $toll_free.' (toll free)<br>';
      }
    if ( $home_page )
      {
        $display .= $home_page.'<br>';
      }
    $year = substr($membership_date, 0, 4);
    $month = substr($membership_date, 5, 2);
    $day = substr($membership_date, 8);
    $member_since = date('F j, Y',mktime(0, 0, 0, $month, $day, $year));
    $display .= '
      Member since '.$member_since.'<br><br>
      Username for online shopping: '.$username_m.'<br><br>';
  }