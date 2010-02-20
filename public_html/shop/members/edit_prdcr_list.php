<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();

if( $prep == 'live' )
  {
    // Make sure only confirmed products are copied to the product_list table
    if (REQ_PRDCR_CONFIRM)
      {
        $where_confirmed = 'WHERE t1.confirmed = "1"';
      }
    else
      {
        $where_confirmed = '';
      }
    $sqlprep = '
      CREATE TABLE '.TABLE_PRODUCT_TEMP.'
      SELECT t1.*
      FROM '.TABLE_PRODUCT_PREP.' AS t1
      '.$where_confirmed;
    $resultprep = @mysql_query($sqlprep,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    if($resultprep)
      {
        $message .= "New product list has been copied.<br>";
      }
    else
      {
        $message .= "New product list not copied. Notify the administrator of this error.";
      }
    $sqldrop = '
      DROP TABLE '.TABLE_PRODUCT;
    $resultdrop = @mysql_query($sqldrop,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    if($resultdrop)
      {
        $message .= "Old product list has been dropped.<br>";
      }
    else
      {
        $message .= "Old product list was not dropped. Notify the administrator of this error.<br>";
      }
    $sqlrename = '
      ALTER TABLE '.TABLE_PRODUCT_TEMP.'
      RENAME TO '.TABLE_PRODUCT;
    $resultrename = @mysql_query($sqlrename,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    if($resultrename)
      {
        $message .= "New Product list has been renamed and the CHANGES ARE LIVE.";
      }
    else
      {
        $message .= "Product list was not rename, product list NOT UPDATED. Notify the administrator of this error.";
      }
  }
if( $confirm == 'yes' )
  {
    $sqlu = '
      UPDATE
        '.TABLE_PRODUCT_PREP.'
      SET
        confirmed = "1"
      WHERE producer_id = "'.$producer_id.'"';
    $result = @mysql_query($sqlu,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    $message = 'The Product List for '.$producer_id.' has been confirmed.<br>';
  }
if( $relist == 'yes' || $unlist == 'yes' )
  {
    if($unlist == "yes")
      {
        $donotlist_prdcr = 1;
      }
    elseif ( $relist == 'yes' )
      {
        $donotlist_prdcr = 0;
      }
    $sqlr = '
      UPDATE
        '.TABLE_PRODUCER.'
      SET
        donotlist_producer = "'.$donotlist_prdcr.'"
      WHERE producer_id = "'.$producer_id.'"';
    $resultr = @mysql_query($sqlr,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    $message = "$producer_id has been updated.<br>";
  }
$display .= '
  <table cellpadding="4" cellspacing="2" border="1" bgcolor="#DDDDDD">
    <tr bgcolor="#AEDE86">
      <td><b>New</b></td>
      <td><b>Edit</b></td>
      <td><b>Business Name</b></td>';
if ( REQ_PRDCR_CONFIRM ) $display .= '
      <td><b>Confirm Product Listing</b></td>
      <td><b>Producer Approved</b></td>';
$display .= '
      <td><b>List or Unlist</b></td>
    </tr>';

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
      GROUP BY
        producer_id';
    $result_count = @mysql_query($sql_count,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    while ( $row = mysql_fetch_array($result_count) )
      {
        $count_prod = $row['count_prod'];
        $count_confirmed = $row['count_confirmed'];
        if($count_prod == $count_confirmed)
          {
            $confirmed = "1";
          }
        else
          {
            $confirmed = "";
          }
      }
    if ( !$business_name )
      {
        $business_name = "$first_name $last_name";
      }

    if ( $donotlist_producer=="1" )
      {
        $display .= '
          <tr bgcolor="#FFFFFF">
            <td></td>
            <td></td>
            <td align="left"><b>'.stripslashes($business_name).'</b></td>';
        if ( REQ_PRDCR_CONFIRM ) $display .= '
            <td>Currently Unlisted</td>
            <td valign="top" align="center" bgcolor="#dddddd">&mdash;</td>';
        $display .= '
            <td bgcolor="#ffdddd" align="center">[<a href="'.$PHP_SELF.'?producer_id='.$producer_id.'&relist=yes"><strong>Relist</strong></a>]</td>
          </tr>';
      }
    else
      {
        if ( ($current_business_name < 0) && !$business_name )
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
            $display_confirmed = "";
            if( $confirmed == "" )
              {
                $display_confirmed = '
                <td><a href="'.$PHP_SELF.'?producer_id='.$producer_id.'&confirm=yes#'.$producer_id.'">Confirm Listing</a></td>
                <td valign="top" align="center" bgcolor="#ffdddd"><strong>No</strong></td>';
              }
            else
              {
                $display_confirmed .= '
                <td>Confirmed</td>
                <td valign="top" align="center" bgcolor="#ddeedd">Yes</td>';
              }
            $display .= '
              <tr bgcolor="#FFFFFF">
                <td align="center"><a name="'.$producer_id.'"> <a href="add_products.php?producer_id='.$producer_id.'">Add</a></td>
                <td align="center"> [<a href="edit_product_list.php?a=listed&producer_id='.$producer_id.'">Listed</a>]<br>
                  [<a href="edit_product_list.php?a=wholesale&producer_id='.$producer_id.'">Wholesale</a>]<br>
                  [<a href="edit_product_list.php?a=unlisted&producer_id='.$producer_id.'">Unlisted</a>]<br></td>
                <td align="left"><b>'.stripslashes ($business_name).'</b></td>';
            if ( REQ_PRDCR_CONFIRM ) $display .= $display_confirmed;
            $display .= '
                <td bgcolor="#ddeedd" align="center">[<a href="'.$PHP_SELF.'?producer_id='.$producer_id.'&unlist=yes">Unlist</a>]</td>
              </tr>';
          }
      }
  }
      $display .= "</table>";
?>
<?php include("template_hdr_orders.php"); ?>

<!-- CONTENT BEGINS HERE -->

<div align="center">
<table width="80%">
  <tr>
    <td align="center">
      <div align="center">
        <h3>Producer Product Lists (<?php echo $prdcr_count;?> Producers):<br>Editing and Adding New Products Prior to Order Day</h3>
        <font color=#3333ff><b><?php echo $message;?></b></font>
      </div>
      Click here to make the product list changes <a href="<?php echo"$PHP_SELF?prep=live";?>">live</a>.
      <br>
      <?php echo $display;?>
    </td>
  </tr>
</table>
</div>

<!-- CONTENT ENDS HERE -->

<?php include("template_footer_orders_edit.php");?>
