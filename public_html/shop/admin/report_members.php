<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

function getMembers($producer)
  {
    $wherestatement = '';
    if ( $producer > 0 )
      {
        $wherestatement = '
          WHERE
            auth_type
          LIKE
            "%producer%" ';
      }
    $sql = mysql_query('
          SELECT
            m.*,
            p.producer_id
          FROM
            '.TABLE_MEMBER.' m
          LEFT JOIN
            '.TABLE_PRODUCER.' p ON p.member_id = m.member_id
          '.$wherestatement.'
          ORDER BY
            member_id');
    while ( $row = mysql_fetch_array($sql) )
      {
        $members[] = $row;
      }
    return $members;
  }
if ( $_REQUEST['p'] == 1 )
  {
    $producers = 1;
  }
else
  {
    $producers = 0;
  }
$members = getMembers($producers);
if($_GET['export'] == "csv")
  {
    // Send output to spreadsheet
    $export = "member_id,member_pending,username_m,auth_type,business_name,producer_id,last_name,first_name,last_name_2,first_name_2,no_postal_mail,address_line1,address_line2,city,state,zip,county,work_address_line1,work_address_line2,work_city,work_state,work_zip,email_address,email_address_2,home_phone,work_phone,mobile_phone,fax,toll_free,home_page,membership_type_id,membership_date,membership_discontinued,mem_taxexempt,mem_delch_discount,how_heard_id\n";
    foreach( $members as $key=>$row )
      {
        $search = array('/\n/', '/\r/', '/"/', '/(.*),(.*)/');
        $replace = array(' ', ' ', '"""', '"\1,\2"');
        $export .=  preg_replace ($search, $replace, stripslashes($row['member_id'])).','.
                    preg_replace ($search, $replace, stripslashes($row['pending'])).','.
                    preg_replace ($search, $replace, stripslashes($row['username_m'])).','.
                    preg_replace ($search, $replace, stripslashes($row['auth_type'])).','.
                    preg_replace ($search, $replace, stripslashes($row['business_name'])).','.
                    preg_replace ($search, $replace, stripslashes($row['producer_id'])).','.
                    preg_replace ($search, $replace, stripslashes($row['last_name'])).','.
                    preg_replace ($search, $replace, stripslashes($row['first_name'])).','.
                    preg_replace ($search, $replace, stripslashes($row['last_name_2'])).','.
                    preg_replace ($search, $replace, stripslashes($row['first_name_2'])).','.
                    preg_replace ($search, $replace, stripslashes($row['no_postal_mail'])).','.
                    preg_replace ($search, $replace, stripslashes($row['address_line1'])).','.
                    preg_replace ($search, $replace, stripslashes($row['address_line2'])).','.
                    preg_replace ($search, $replace, stripslashes($row['city'])).','.
                    preg_replace ($search, $replace, stripslashes($row['state'])).','.
                    preg_replace ($search, $replace, stripslashes($row['zip'])).','.
                    preg_replace ($search, $replace, stripslashes($row['county'])).','.
                    preg_replace ($search, $replace, stripslashes($row['work_address_line1'])).','.
                    preg_replace ($search, $replace, stripslashes($row['work_address_line2'])).','.
                    preg_replace ($search, $replace, stripslashes($row['work_city'])).','.
                    preg_replace ($search, $replace, stripslashes($row['work_state'])).','.
                    preg_replace ($search, $replace, stripslashes($row['work_zip'])).','.
                    preg_replace ($search, $replace, stripslashes($row['email_address'])).','.
                    preg_replace ($search, $replace, stripslashes($row['email_address_2'])).','.
                    preg_replace ($search, $replace, stripslashes($row['home_phone'])).','.
                    preg_replace ($search, $replace, stripslashes($row['work_phone '])).','.
                    preg_replace ($search, $replace, stripslashes($row['mobile_phone '])).','.
                    preg_replace ($search, $replace, stripslashes($row['fax'])).','.
                    preg_replace ($search, $replace, stripslashes($row['toll_free'])).','.
                    preg_replace ($search, $replace, stripslashes($row['home_page'])).','.
                    preg_replace ($search, $replace, stripslashes($row['membership_type_id'])).','.
                    preg_replace ($search, $replace, stripslashes($row['membership_date'])).','.
                    preg_replace ($search, $replace, stripslashes($row['membership_discontinued'])).','.
                    preg_replace ($search, $replace, stripslashes($row['mem_taxexempt'])).','.
                    preg_replace ($search, $replace, stripslashes($row['mem_delch_discount'])).','.
                    preg_replace ($search, $replace, stripslashes($row['how_heard_id']))."\n";
      }
    header("Content-type: application/octet-stream"); 
    header("Content-Disposition: attachment; filename=foodcoop-".date('Y-m-d').".csv"); 
    header("Pragma: no-cache"); 
    header("Expires: 0"); 
    echo $export;
//    exit;
  }
else
  {
    // Send output to web page
    include("template_hdr.php");
    echo '<div align="center">';
    if ( $producers )
      {
        echo '<h2>Producer Members (Click to <a href="'.$_SERVER['PHP_SELF'].'?p=0">show all Members</a>)</h2>';
      }
    else
      {
        echo '<h2>Members (Click to <a href="'.$_SERVER['PHP_SELF'].'?p=1">show only Producers</a>)</h2>';
      }
    echo '
      <table style="border:1px solid;">
        <tr style="background-color:#CCCCCC;">
          <th>member_id</th>
          <th>member pending</th>
          <th>username_m</th>
          <th>auth_type</th>
          <th>business_name</th>
          <th>producer_id</th>
          <th>last_name</th>
          <th>first_name</th>
          <th>last_name_2</th>
          <th>first_name_2</th>
          <th>no_postal_mail</th>
          <th>address_line1</th>
          <th>address_line2</th>
          <th>city</th>
          <th>state</th>
          <th>zip</th>
          <th>county</th>
          <th>work_address_line1</th>
          <th>work_address_line2</th>
          <th>work_city</th>
          <th>work_state</th>
          <th>work_zip</th>
          <th>email_address</th>
          <th>email_address_2</th>
          <th>home_phone</th>
          <th>work_phone </th>
          <th>mobile_phone </th>
          <th>fax</th>
          <th>toll_free</th>
          <th>home_page</th>
          <th>membership_type_id</th>
          <th>membership_date</th>
          <th>membership_discontinued</th>
          <th>mem_taxexempt</th>
          <th>mem_delch_discount</th>
          <th>how_heard_id</th>
        </tr>';
    foreach( $members as $key=>$row )
      {
        $style = '';
        if ( is_int($key / 2) )
          {
            $style = ' style="background-color:#EEEEEE;" ';
          }
          echo '
        <tr '.$style.'>
          <td>'.stripslashes($row['member_id']).'</td>
          <td>'.stripslashes($row['pending']).'</td>
          <td>'.stripslashes($row['username_m']).'</td>
          <td>'.stripslashes($row['auth_type']).'</td>
          <td>'.stripslashes($row['business_name']).'</td>
          <td>'.stripslashes($row['producer_id']).'</td>
          <td>'.stripslashes($row['last_name']).'</td>
          <td>'.stripslashes($row['first_name']).'</td>
          <td>'.stripslashes($row['last_name_2']).'</td>
          <td>'.stripslashes($row['first_name_2']).'</td>
          <td>'.stripslashes($row['no_postal_mail']).'</td>
          <td>'.stripslashes($row['address_line1']).'</td>
          <td>'.stripslashes($row['address_line2']).'</td>
          <td>'.stripslashes($row['city']).'</td>
          <td>'.stripslashes($row['state']).'</td>
          <td>'.stripslashes($row['zip']).'</td>
          <td>'.stripslashes($row['county']).'</td>
          <td>'.stripslashes($row['work_address_line1']).'</td>
          <td>'.stripslashes($row['work_address_line2']).'</td>
          <td>'.stripslashes($row['work_city']).'</td>
          <td>'.stripslashes($row['work_state']).'</td>
          <td>'.stripslashes($row['work_zip']).'</td>
          <td>'.stripslashes($row['email_address']).'</td>
          <td>'.stripslashes($row['email_address_2']).'</td>
          <td>'.stripslashes($row['home_phone']).'</td>
          <td>'.stripslashes($row['work_phone']).'</td>
          <td>'.stripslashes($row['mobile_phone']).'</td>
          <td>'.stripslashes($row['fax']).'</td>
          <td>'.stripslashes($row['toll_free']).'</td>
          <td>'.stripslashes($row['home_page']).'</td>
          <td>'.stripslashes($row['membership_type_id']).'</td>
          <td>'.stripslashes($row['membership_date']).'</td>
          <td>'.stripslashes($row['membership_discontinued']).'</td>
          <td>'.stripslashes($row['mem_taxexempt']).'</td>
          <td>'.stripslashes($row['mem_delch_discount']).'</td>
          <td>'.stripslashes($row['how_heard_id']).'</td>
        </tr>';
      }
    echo '
      </table>
      <br/>
      <form action="'.$_SERVER['PHP_SELF'].'?p='.$_REQUEST['p'].'&export=csv" method="POST">
      <input type="submit" name="submit" value="Export Results to CSV File">
      </form>
      </div>';
    include("template_footer.php");
  }

?>