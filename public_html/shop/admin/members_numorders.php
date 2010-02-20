<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

$delivery_id = $current_delivery_id;
$sql = '
  SELECT
    '.TABLE_MEMBER.'.*,
    '.TABLE_BASKET_ALL.'.member_id,
    '.TABLE_BASKET_ALL.'.basket_id,
    COUNT('.TABLE_BASKET_ALL.'.basket_id) AS count
  FROM
    '.TABLE_MEMBER.',
    '.TABLE_BASKET_ALL.'
  WHERE
    '.TABLE_MEMBER.'.member_id = '.TABLE_BASKET_ALL.'.member_id
  GROUP BY
    last_name ASC,
    first_name ASC';
$rs = @mysql_query($sql,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
$num = mysql_numrows($rs);
while ( $row = mysql_fetch_array($rs) )
  {
//    $numbaskets = mysql_result($rs, 0, "COUNT(basket_id)");
//    $numbaskets = $row['count'];
    $member_id = $row['member_id'];
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $first_name_2 = $row['first_name_2'];
    $last_name_2 = $row['last_name_2'];
    $business_name = stripslashes ($row['business_name']);
    $membership_date = $row['membership_date'];
    $city = $row['city'];
    $state = $row['state'];
    $zip = $row['zip'];
    include("../func/show_name.php");
    $basket_id = '';
    $sql2 = '
      SELECT
        '.TABLE_BASKET_ALL.'.member_id,
        '.TABLE_BASKET_ALL.'.delivery_id,
        '.TABLE_BASKET_ALL.'.basket_id
      FROM
        '.TABLE_BASKET_ALL.',
        '.TABLE_MEMBER.'
      WHERE
        '.TABLE_BASKET_ALL.'.member_id = '.TABLE_MEMBER.'.member_id
        AND '.TABLE_BASKET_ALL.'.member_id = "'.$member_id.'"';
//        AND '.TABLE_BASKET_ALL.'.delivery_id = "'.$current_delivery_id.'"';
    $rs2 = @mysql_query($sql2, $connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    $num2 = mysql_numrows($rs2);
    while ( $row = mysql_fetch_array($rs2) )
      {
        $basket_id = $row['basket_id'];
      }
    $display .= 'Member ID: '.$member_id.' - '.$num2.' baskets<br>';
    $display .= 'Membership Date: '.$membership_date.'<br>';
    $display .= '<b>'.$show_name.'</b><br>';
    $display .= $address_line1.'<br>';
    if ( $address_line2 )
      {
        $display .= $address_line2.'<br>';
      }
    $display .= "$city, $state $zip<br>";
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
    if ( $basket_id )
      {
        $display .= '
          <b><a href="customer_invoice.php?delivery_id='.$delivery_id.'&basket_id='.$basket_id.'&member_id='.$member_id.'">
          View their current invoice</a></b><br>';
      }
    $display .= '
      <a href="members_invoices.php?member_id='.$member_id.'">View all previous invoices</a><br><br>';
  }
include("template_hdr.php");
?>
<!-- CONTENT BEGINS HERE -->
<div align="center">
  <table width="80%">
    <tr>
      <td align="left">
        <div align="center">
          <h3>Member Contact Info: <?php echo $num;?> Members Listed Newest First</h3>
        </div>
        <?php echo $display;?>
      </td>
    </tr>
  </table>
</div>
<!-- CONTENT ENDS HERE -->
<?php include("template_footer.php");?>
