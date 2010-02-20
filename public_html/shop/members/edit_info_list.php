<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();

$display .= "<table cellpadding=4 cellspacing=2 border=0 bgcolor=\"#DDDDDD\">
  <tr bgcolor=\"#AEDE86\"><td><b>Edit Producer Info.</b></td>
  <td><b>Business Name</b></td></tr>";

$sqlp = '
  SELECT
    '.TABLE_PRODUCER.'.producer_id,
    '.TABLE_PRODUCER.'.member_id,
    '.TABLE_MEMBER.'.member_id,
    '.TABLE_PRODUCER.'.donotlist_producer,
    '.TABLE_MEMBER.'.business_name,
    '.TABLE_MEMBER.'.first_name,
    '.TABLE_MEMBER.'.last_name
  FROM
    '.TABLE_PRODUCER.',
    '.TABLE_MEMBER.'
  WHERE
    '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
  GROUP BY
    producer_id
  ORDER BY
    business_name ASC,
    last_name ASC';
$resultp = @mysql_query($sqlp,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
$prdcr_count = mysql_numrows($resultp);
while ( $row = mysql_fetch_array($resultp) )
  {
    $producer_id = $row['producer_id'];
    $business_name = stripslashes ($row['business_name']);
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $donotlist_producer = $row['donotlist_producer'];

    if ( !$business_name )
      {
      $business_name = "$first_name $last_name";
      }

    if ( $donotlist_producer == "1" )
      {
        $display .= "<tr bgcolor=\"#FFFFFF\">
        <td><a href=\"edit_producer_info.php?producer_id=$producer_id\">Edit</a></td>
        <td align=\"left\"><b>$business_name</b> (Currently Unlisted)</td>
        </tr>";
      } else {

        if ( ($current_business_name < 0) && ! $business_name )
          {
            $current_business_name = stripslashes ($row['business_name']);
          }
        else
          {
            $current_business_name = $row['last_name'];
          }


        while ( $current_business_name != $business_name )
          {
            $current_business_name = $business_name;

            $display .= "<tr bgcolor=\"#FFFFFF\">";
            $display .= "<td align=\"center\"><a name=\"$producer_id\">
            <a href=\"edit_producer_info.php?producer_id=$producer_id\">Edit</a></td>
            <td align=\"left\"><b>$business_name</b></td>";
            $display .= "</tr>";
          }
      }
  }
$display .= "</table>";
?>


<?php include("template_hdr_orders.php");?>


  <!-- CONTENT BEGINS HERE -->

<div align="center">
<table width="80%">
  <tr><td align="center">

<div align="center">
<h3>Producer Information (<?php echo $prdcr_count;?> Producers)</h3>
</div>

<?php echo $display;?>



  </td></tr>
</table>
</div>
  <!-- CONTENT ENDS HERE -->

<?php include("template_footer_orders_edit.php");?>
