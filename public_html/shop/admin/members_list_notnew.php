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
    member_id < "170"
    AND email_address != ""
    AND no_postal_mail != "1"
  ORDER BY
    last_name ASC,
  first_name ASC';
$rs = @mysql_query($sql,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
$num = mysql_numrows($rs);
while ( $row = mysql_fetch_array($rs) )
  {
    $member_id = $row['member_id'];
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $first_name_2 = $row['first_name_2'];
    $last_name_2 = $row['last_name_2'];
    $business_name = stripslashes ($row['business_name']);
    $address_line1 = $row['address_line1'];
    $address_line2 = $row['address_line2'];
    $city = $row['city'];
    $state = $row['state'];
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
          <h3>Member Contact Info for Existing Members (Not New): <?php echo $num;?> Members</h3>
        </div>
        <?php echo $display;?>
      </td>
    </tr>
  </table>
</div>
<!-- CONTENT ENDS HERE -->
<?php include("template_footer.php");?>