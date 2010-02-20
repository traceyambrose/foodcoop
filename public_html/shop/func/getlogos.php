<?php
include_once ('config_foodcoop.php');
// getdata.php3 - by Florian Dittmer <dittmer@gmx.net>
// Example php script to demonstrate the direct passing of binary data
// to the user. More infos at http://www.phpbuilder.com
// Syntax: getdata.php3?id=<id>
// You would call this script as an img src tag, e.g.
// you can do <img src="getdata.php?id=3">
//
if( $logo_id )
  {
    // you may have to modify login information for your database server:
    $query = '
      SELECT
        bin_data,
        filetype
      FROM
        '.TABLE_PRODUCER_LOGOS.'
      WHERE
        logo_id='.$logo_id;
    $result = @MYSQL_QUERY($query);
    $data = @MYSQL_RESULT($result,0,"bin_data");
    $type = @MYSQL_RESULT($result,0,"filetype");
    Header( "Content-type: $type");
    echo $data;
    echo 'Click here to return to <a href="coopproducers.php">'.ORGANIZATION_TYPE.' producers</a>';
  };
?>
