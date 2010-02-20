<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();
$sqlp = '
  SELECT
    '.TABLE_PRODUCER.'.producer_id,
    '.TABLE_PRODUCER.'.member_id,
    '.TABLE_MEMBER.'.member_id,
    '.TABLE_PRODUCER.'.donotlist_producer,
    '.TABLE_MEMBER.'.business_name,
    '.TABLE_MEMBER.'.first_name,
    '.TABLE_MEMBER.'.last_name,
    '.TABLE_MEMBER.'.email_address
  FROM
    '.TABLE_PRODUCER.',
    '.TABLE_MEMBER.'
  WHERE
    '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
    AND '.TABLE_PRODUCER.'.donotlist_producer != "1"
    AND '.TABLE_MEMBER.'.membership_discontinued != "1"';
$resultp = @mysql_query($sqlp,$connection) or die("Couldn't execute search query 2.");
while ( $row = mysql_fetch_array($resultp) )
  {
    $producer_id = $row['producer_id'];
    $business_name = stripslashes ($row['business_name']);
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $email_address = $row['email_address'];
    $email_address_2 = $row['email_address_2'];
    if ( !$business_name )
      {
        $business_name = $first_name.' '.$last_name;
      }
    if ( ($current_business_name < 0) &&! $business_name )
      {
        $current_business_name = $row['business_name'];
      }
    else
      {
        $current_business_name = $row['last_name'];
      }
    while ( $current_business_name != $business_name )
      {
        $current_business_name = $business_name;
        if ( $email_address )
          {
            $display .= '<a href="mailto:'.$email_address.'">'.$email_address.'</a><br>';
          }
        if ( $email_address_2 )
          {
            $display .= '<a href="mailto:'.$email_address_2.'">'.$email_address_2.'</a><br>';
          }
      }
  }
?>
<?php include("template_hdr.php");?>
<!-- CONTENT BEGINS HERE -->
<div align="center">
<table width="80%">
  <tr>
    <td align="left">
      <div align="center">
        <h3>Producer Email List</h3>
      </div>
      <?php echo $display;?>
    </td>
  </tr>
</table>
</div>
<!-- CONTENT ENDS HERE -->
<?php include("template_footer.php");?>