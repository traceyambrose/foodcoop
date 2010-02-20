<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

$sql = '
  SELECT
    '.TABLE_MEMBER.'.*
  FROM
    '.TABLE_MEMBER.'
  WHERE
    email_address != ""
    AND no_postal_mail != "1"
    AND membership_discontinued != "1"
  ORDER BY
    last_name ASC,
    first_name ASC';
$rs = @mysql_query($sql,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
$num = mysql_numrows($rs);
while ( $row = mysql_fetch_array($rs) )
  {
    $member_id = $row['member_id'];
    $first_name = stripslashes ($row['first_name']);
    $last_name = stripslashes ($row['last_name']);
    $first_name_2 = stripslashes ($row['first_name_2']);
    $last_name_2 = stripslashes ($row['last_name_2']);
    $business_name = stripslashes ($row['business_name']);
    $address_line1 = stripslashes ($row['address_line1']);
    $address_line2 = stripslashes ($row['address_line2']);
    $city = stripslashes ($row['city']);
    $state = stripslashes ($row['state']);
    $zip = $row['zip'];
    $email_address = $row['email_address'];
    $home_phone = $row['home_phone'];
    $work_phone = $row['work_phone'];
    $mobile_phone = $row['mobile_phone'];
    $fax = $row['fax'];
    $membership_date = $row['membership_date'];
    include("../func/show_name.php");
    $display .= "$show_name</b><br>";
    $display .= "$address_line1<br>";
    if ( $address_line2 )
      {
        $display .= "$address_line2<br>";
      }
    $display .= "$city, $state $zip<br>";
    $display .= "<br>";
  }
include("template_hdr.php");
?>
<!-- CONTENT BEGINS HERE -->
<div align="center">
  <table width="80%">
    <tr>
      <td align="left">
        <div align="center">
          <h3>Label Info for Members WITH Email: <?php echo $num;?> Members</h3>
        </div>
        <?php echo $display;?>
      </td>
    </tr>
  </table>
</div>
  <!-- CONTENT ENDS HERE -->
<?php include("template_footer.php");?>