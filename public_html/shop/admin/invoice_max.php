<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();
include("template_hdr.php");

if ( $_POST['submit'] )
  {
    $i = 0;
    $sql = mysql_query ('
      SELECT
        max( t.transaction_id ) AS transaction_id
      FROM
        '.TABLE_TRANSACTIONS.' t,
        transactions_types tt
      WHERE
        t.transaction_type = tt.ttype_id
        AND tt.ttype_parent = "22"
      GROUP BY t.transaction_type,
        t.transaction_member_id,
        t.transaction_basket_id,
        t.transaction_delivery_id
      ORDER BY
        transaction_id ASC');
    while ( $result = mysql_fetch_array($sql) )
      {
        $original_max_transactions[] = $result['transaction_id'];
      }
    $sql = mysql_query('
      SELECT
        transaction_id
      FROM
        '.TABLE_TRANSACTIONS_MAX);
    while ( $result = mysql_fetch_array($sql) )
      {
        $stored_max_transactions[] = $result['transaction_id'];
      }
    if ( is_array($original_max_transactions) && is_array($stored_max_transactions) )
      {
        $not_stored_yet = array_diff($original_max_transactions,$stored_max_transactions);
        if ( is_array($not_stored_yet) )
          {
            foreach ( $not_stored_yet as $transaction_id )
              {
                $sql = mysql_query('
                  INSERT INTO
                    '.TABLE_TRANSACTIONS_MAX.'
                  SET
                    transaction_id = "'.$transaction_id.'"');
                $i++;
              }
          }
      }
    else
      {
        foreach ( $original_max_transactions as $transaction_id )
          {
            $sql = mysql_query('
              INSERT INTO
                '.TABLE_TRANSACTIONS_MAX.'
              SET
                transaction_id = "'.$transaction_id.'"');
            $i++;
          }
      }
    $message = $i.' rows added<br /><br />';
  }
?>
<div align="center">
  <h2>Update the Invoice Max Table</h2>
  <?php  echo $message;?>
  <form action="<?php  echo $_SERVER['PHP_SELF'];?>" method="post">
    <input type="submit" name="submit" value="Update">
  </form>
</div>
<?php  include("template_footer.php"); ?>