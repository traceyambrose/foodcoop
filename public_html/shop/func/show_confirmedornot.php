<?php
if ( REQ_PRDCR_CONFIRM )
  {
    $sql_count = '
      SELECT
        producer_id,
        product_id,
        COUNT(product_id) AS count_prod,
        SUM(confirmed) AS count_confirmed
      FROM
        '.TABLE_PRODUCT_PREP.'
      WHERE
        producer_id = "'.$producer_id.'"
      GROUP BY producer_id';
    $result_count = @mysql_query($sql_count,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    while ( $row = mysql_fetch_array($result_count) )
      {
        $count_prod = $row['count_prod'];
        $count_confirmed = $row['count_confirmed'];
        if( $count_prod == $count_confirmed )
          {
            $confirmed = '<b>You have confirmed your product listing. Thank you!</b><br>
              You are still able to make changes to it as long as they are done before the deadline.</b>';
          }
        else
          {
            $confirmed = '<b>Currently, you have not confirmed your product list.
              Click here to <a href="edit_product_list.php?producer_id='.$producer_id.'&confirm=yes">
              Confirm this Listing</a>.</b>
              <br>Please confirm before the deadline. This is a requirement for your product list
              to be included in the coming order cycle.<br>
              If not confirmed by the time ordering opens, we will be unable to list your products.';
          }
      }
  }