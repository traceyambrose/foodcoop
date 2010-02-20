<?php
$sql = '
  SELECT
    delivery_id,
    delivery_date
  FROM
    '.TABLE_DELDATE.'
  WHERE
    delivery_id = "'.$delivery_id.'"';
$rs = @mysql_query($sql,$connection) or die("Couldn't execute query.");
while ( $row = mysql_fetch_array($rs) )
  {
    $delivery_id = $row['delivery_id'];
    $delivery_date = $row['delivery_date'];
  }