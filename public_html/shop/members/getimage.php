<?php

// getdata.php3 - by Florian Dittmer <dittmer@gmx.net>
// Example php script to demonstrate the direct passing of binary data
// to the user. More infos at http://www.phpbuilder.com
// Syntax: getdata.php3?id=<id>
// You would call this script as an img src tag, e.g.
// you can do <img src="getdata.php?id=3">
//
include_once ('config_foodcoop.php');

if ( $image_id )
  {

    // you may have to modify login information for your database server:

    $query = '
      SELECT
        bin_data,
        filetype
      FROM
        '.TABLE_PRODUCT_IMAGES.'
      WHERE
        image_id = '.$image_id;
    $result = @MYSQL_QUERY($query,$connection);

    $data = @MYSQL_RESULT($result,0,"bin_data");
    $type = @MYSQL_RESULT($result,0,"filetype");

    Header( "Content-type: $type");
    echo $data;

    echo "Click here to return to <a href=\"edit_product_list.php?producer_id=$producer_id\">your product list</a>";
  };
?>