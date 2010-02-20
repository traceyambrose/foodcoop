<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

$sql = '
  SELECT
    '.TABLE_BASKET_ALL.'.delivery_id,
    '.TABLE_DELDATE.'.*,
    '.TABLE_BASKET_ALL.'.finalized,
    '.TABLE_BASKET_ALL.'.basket_id,
    '.TABLE_MEMBER.'.member_id,
    '.TABLE_MEMBER.'.first_name,
    '.TABLE_MEMBER.'.last_name,
    '.TABLE_MEMBER.'.first_name_2,
    '.TABLE_MEMBER.'.last_name_2,
    '.TABLE_MEMBER.'.business_name
  FROM
    '.TABLE_BASKET_ALL.',
    '.TABLE_DELDATE.',
    '.TABLE_MEMBER.'
    WHERE
      '.TABLE_BASKET_ALL.'.member_id = "'.$member_id.'"
      AND '.TABLE_MEMBER.'.member_id = "'.$member_id.'"
      AND '.TABLE_BASKET_ALL.'.delivery_id = '.TABLE_DELDATE.'.delivery_id
    ORDER BY '.TABLE_BASKET_ALL.'.delivery_id DESC';
$rs = @mysql_query($sql,$connection) or die("Couldn't execute query -d.");
$num = mysql_numrows($rs);
while ( $row = mysql_fetch_array($rs) )
  {
    $delivery_id = $row['delivery_id'];
    $delivery_date = $row['delivery_date'];
    $basket_id = $row['basket_id'];
    $finalized = $row['finalized'];
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $first_name_2 = $row['first_name_2'];
    $last_name_2 = $row['last_name_2'];
    $business_name = stripslashes ($row['business_name']);
    include("../func/convert_delivery_date.php");
    if ( $finalized )
      {
        $display .="<li> <a href=\"invoice.php?delivery_id=$delivery_id&basket_id=$basket_id&member_id=$member_id\">$delivery_date</a><br>";
      }
    else
      {
        $display .="<li> <a href='customer_invoice.php?delivery_id=$delivery_id&basket_id=$basket_id&member_id=$member_id'>$delivery_date</a> (unfinalized)<br>";
      }
  }
include("../func/show_name.php");
?>
<?php include("template_hdr.php");?>
<div align="center">
  <table width="60%">
    <tr>
      <td align="left">
<?php
if ( $num )
  {
    echo '
      <h3>Previous Orders for '.$show_name.'</h3>
      <ul>
        '.$display.'
      </ul>
      ';
  }
else
  {
    echo "<b>No previous orders on record.</b>";
  }
?>
      </td>
    </tr>
  </table>
</div>
<?php include("template_footer.php");?>
</body>
</html>