<?php

$sql = '
  SELECT
    delivery_id,
    delivery_date
  FROM
    '.TABLE_DELDATE.'
  ORDER BY
    delivery_id DESC';
$rs = @mysql_query($sql,$connection) or die("Couldn't execute query.");
while ( $row = mysql_fetch_array($rs) )
  {
    $delivery_id = $row['delivery_id'];
    $delivery_date = $row['delivery_date'];
    include("convert_delivery_date.php");
    $display .= '<li><a href="orders_list.php?delivery_id='.$delivery_id.'">'.$delivery_date.'</a>';
    $display2 .= '<li><a href="orders_prdcr_list.php?delivery_id='.$delivery_id.'">'.$delivery_date.'</a>';
    $display_totals .= '
    <tr>
      <td>[<a href="ctotals_onebutton.php?delivery_id='.$delivery_id.'">Update Payments</a>]</td>
      <td><b>'.$delivery_date.'</b></td>
      <td>[<a href="ctotals_reports.php?delivery_id='.$delivery_id.'">Reports</a>]</td>
      <td>[<a href="ctotals_reports.php?delivery_id='.$delivery_id.'&spreadsheet=1">Spreadsheet</a>]</td>
    </tr>';
  }