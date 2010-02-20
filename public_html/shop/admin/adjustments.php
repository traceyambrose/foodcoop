<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

$message = '';

if ( $delete_adjustment == 'yes' )
  {

    if ( $_POST['transaction_id_passed'] )
      {
        $sql = mysql_query('
          SELECT
            transaction_type,
            transaction_name,
            transaction_amount,
            transaction_producer_id,
            transaction_member_id,
            transaction_basket_id,
            transaction_taxed
          FROM
            transactions
          WHERE
            transaction_id = "'.$_POST['transaction_id_passed'].'"
          LIMIT 1');
        $row = mysql_fetch_array($sql);
        if ( strpos($row['transaction_amount'],"-") !== false )
          {
            $amount = preg_replace("/[^0-9\.]/","",$row['transaction_amount']);
          }
        else
          {
            $amount = '-'.$row['transaction_amount'];
          }
        $sqldelete = '
          INSERT INTO
            transactions
              (
                transaction_type,
                transaction_name,
                transaction_amount,
                transaction_user,
                transaction_producer_id,
                transaction_member_id,
                transaction_basket_id,
                transaction_delivery_id,
                transaction_taxed,
                transaction_comments,
                transaction_timestamp
              )
          VALUES
            (
              "'.$row['transaction_type'].'",
              "'.$row['transaction_name'].'",
              "'.$amount.'",
              "'.$_SESSION['valid_c'].'",
              "'.$row['transaction_producer_id'].'",
              "'.$row['transaction_member_id'].'",
              "'.$row['transaction_basket_id'].'",
              "'.$delivery_id.'",
              "'.$row['transaction_taxed'].'",
              "Adjustment Zeroed Out",
              now()
            )';
        //echo $sqldelete."<br/>";
        $resultdelete = @mysql_query($sqldelete,$connection) or die(mysql_error());
        $message = ": <font color=\"#FFFFFF\">Adjustment Zeroed Out</font>";
      }
  }
elseif ( $adjustment_submitted == "yep" && $adjt_id && (($basket_id && $adj_type=="customer") || ($_POST['producer_id'] && $adj_type=="producer")) )
  {
    $sql_select = "SELECT ttype_creditdebit, ttype_name, ttype_taxed FROM transactions_types WHERE ttype_id = '$adjt_id' limit 1";
    $result_select = @mysql_query($sql_select, $connection) or die("".mysql_error()."");
    $row = mysql_fetch_array($result_select);
    if ( ($row['ttype_creditdebit'] == 'credit' && $adj_type == 'customer') || ($row['ttype_creditdebit'] == 'debit' && $adj_type== 'producer') )
      {
        $adj_amount = preg_replace("/[^0-9\.\-]/","",$adj_amount);
        $adj_amount = $adj_amount * (-1);
      }
    else
      {
        $adj_amount = preg_replace("/[^0-9\.\-]/","",$adj_amount);
      }
    if ( $adj_type == 'customer' )
      {
        $sql2 = mysql_query('
          SELECT
            member_id
          FROM
            '.TABLE_BASKET_ALL.'
          WHERE
            basket_id = "'.$basket_id.'"
            AND delivery_id = "'.$delivery_id.'"');
        $row2 = mysql_fetch_array($sql2);
      }
    elseif ( $adj_type=="producer" && $_POST['producer_id'] )
      {
        $sql2 = mysql_query('
          SELECT
            member_id
          FROM
            producers
              WHERE producer_id = "'.$_POST['producer_id'].'"');
        $row2 = mysql_fetch_array($sql2);
      }
    // check for duplicates
    $sql = mysql_query('
      SELECT
        transaction_id FROM
        transactions t
      WHERE
        t.transaction_type="'.$adjt_id.'"
          AND t.transaction_member_id = "'.$row2['member_id'].'"
          AND t.transaction_basket_id = "'.$basket_id.'"
          AND t.transaction_delivery_id = "'.$_POST['delivery_id'].'"
          AND t.transaction_name = "'.$row['ttype_name'].'"
          AND t.transaction_amount = "'.$adj_amount.'"
          AND t.transaction_user = "'.$_SESSION['valid_c'].'"
          AND t.transaction_comments = "'.$adj_desc.'"');
    if ( mysql_num_rows($sql) < 1 )
      {
        $sql_insert = '
          INSERT INTO transactions
            (
              transaction_type,
              transaction_name,
              transaction_amount,
              transaction_user,
              transaction_producer_id,
              transaction_member_id,
              transaction_basket_id,
              transaction_delivery_id,
              transaction_taxed,
              transaction_timestamp,
              transaction_comments,
              transaction_method)
          VALUES
            (
              "'.$adjt_id.'",
              "'.$row["ttype_name"].'",
              "'.$adj_amount.'",
              "'.$_SESSION['valid_c'].'",
              "'.$_POST['producer_id'].'",
              "'.$row2['member_id'].'",
              "'.$basket_id.'",
              "'.$_POST['delivery_id'].'",
              "'.$row['ttype_taxed'].'",
              now(),
              "'.$adj_desc.'",
              "'.$_POST['payment_method'].'"
            )';
        //echo $sql_insert."<br/>";
        $result_insert = @mysql_query($sql_insert,$connection) or die(mysql_error());
        $message = ": <font color=\"#FFFFFF\">Adjustment Added</font>";
      }
  }
elseif ( $adjustment_submitted=="yep" && (!$adjt_id || (!$basket_id && $adj_type=="customer") || ($adj_type=="producer" && !$_POST['producer_id'])) )
  {
    $message = ": <font color=\"#FFFFFF\">Please select an adjustment type and basket.</font>";
  }
if ( $basket_id &&($delete_adjustment == 'yes' || ($adjustment_submitted == 'yep' && $adjt_id)) )
  {
    $sqlo = '
      UPDATE
        '.TABLE_BASKET_ALL.'
      SET
        finalized = "0"
      WHERE
        basket_id = "'.$basket_id.'"';
    //echo $sqlo."<br/>";
    $resulto = @mysql_query($sqlo,$connection) or die(mysql_error());
  }
// END UPDATING QUERY
//Show baskets depending on the delivery date.
$delivery_id = $_GET['delivery_id'];
if ( $_POST['adj_type'] )
  {
    $adj_type = $_POST['adj_type'];
  }
else
  {
    $adj_type = $_GET['adj_type'];
  }
$q = mysql_query('
  SELECT
    delivery_id,
    delivery_date
  FROM
    '.TABLE_DELDATE.'
  ORDER BY
    delivery_id DESC');
while ( $row = mysql_fetch_array($q) )
  {
    $delivery_date_formated = date('Y: F j',mktime(0,0,0, substr($row['delivery_date'], 5, 2), substr($row['delivery_date'], 8), substr($row['delivery_date'], 0,4)));
    $selected = ($row["delivery_id"] == $delivery_id)? "SELECTED":"";
    $display_dates .= "<option value=\"".$row['delivery_id']."\"". $selected." >$delivery_date_formated</option>";
  }
$q3 = mysql_query('
  SELECT
    delivery_id,
    delivery_date
  FROM
    '.TABLE_DELDATE.'
  WHERE
    delivery_id = "'.$delivery_id.'"');
while ( $row = mysql_fetch_array($q3) )
  {
    $display = '
      <tr>
        <td colspan="2" bgcolor="#AEDE86" align="left"><b>Delivery Date: '.date('Y: F j',mktime(0,0,0, substr($row['delivery_date'], 5, 2), substr($row['delivery_date'], 8), substr($row['delivery_date'], 0, 4))).'</b></b></td>
      </tr>';
  }
if ( $adj_type == 'customer' )
  {
    $q2 = mysql_query('
      SELECT
        '.TABLE_BASKET_ALL.'.basket_id,
        '.TABLE_BASKET_ALL.'.member_id,
        '.TABLE_MEMBER.'.member_id,
        '.TABLE_BASKET_ALL.'.finalized,
        '.TABLE_MEMBER.'.last_name,
        '.TABLE_MEMBER.'.first_name,
        '.TABLE_MEMBER.'.business_name
      FROM
        '.TABLE_BASKET_ALL.',
        '.TABLE_MEMBER.'
      WHERE
        '.TABLE_BASKET_ALL.'.delivery_id = "'.$delivery_id.'"
        AND '.TABLE_BASKET_ALL.'.member_id = '.TABLE_MEMBER.'.member_id
      GROUP BY
        basket_id
      ORDER BY
        last_name ASC');
    while ( $row = mysql_fetch_array($q2) )
      {
        $member_id = $row['member_id'];
        $business_name = stripslashes ($row['business_name']);
        $last_name = $row['last_name'];
        $first_name = $row['first_name'];
        $last_name_2 = $row['last_name_2'];
        $first_name_2 = $row['first_name_2'];
        include("../func/show_name_last.php");
        $display_baskets .= '<option value="'.$row['basket_id'].'">'.stripslashes($show_name).' #'.$member_id.'</option>';
        $sql = mysql_query('
          SELECT
            t.transaction_id,
            t.transaction_amount,
            t.transaction_basket_id,
            t.transaction_comments,
            tt.ttype_name
          FROM
            '.TABLE_TRANSACTIONS.' t,
            '.TABLE_TRANS_TYPES.' tt
          WHERE
            transaction_basket_id = "'.$row['basket_id'].'"
            AND t.transaction_type = tt.ttype_id
            AND
              (
                tt.ttype_parent = "20"
                OR tt.ttype_parent = "40"
              )');
        if ( mysql_num_rows($sql) > 0 )
          {
            while ( $row = mysql_fetch_array($sql) )
              {
                $display .= '<tr bgcolor="#CCCCCC"><td align="left">';
                if (!$row['finalized'])
                  {
                    $display .= '<a href="customer_invoice.php?delivery_id='.$delivery_id.'&basket_id='.$row['transaction_basket_id'].'&member_id='.$member_id.'">';
                  }
                else
                  {
                    $display .= '(finalized) <a href="invoice.php?basket_id='.$row['transaction_basket_id'].'&member_id='.$member_id.'">';
                  }
                $display .= stripslashes($show_name).' (Mem # '.$member_id.')</a>,  Basket id: '.$row['transaction_basket_id'].'<br>
                  '.$row['ttype_name'].': $'.number_format($row['transaction_amount'],2).'<br>
                  '.stripslashes($row['transaction_comments']).'</td>';
                $display .= '<td><form action="'.$PHP_SELF.'?delivery_id='.$delivery_id.'&adj_type='.$_GET['adj_type'].'" method="post">
                  <input type="hidden" name="transaction_id_passed" value="'.$row['transaction_id'].'">
                  <input type="hidden" name="delete_adjustment" value="yes">
                  <input type="hidden" name="basket_id" value="'.$row['transaction_basket_id'].'">
                  <input type="submit" name="where" value="Delete">
                  </form>
                  </td></tr>';
              }
          }
      }
  }
elseif ( $adj_type == 'producer' )
  {
    $q4 = mysql_query('
      SELECT
        p.producer_id,
        business_name
      FROM
        '.TABLE_PRODUCER.' pr,
        '.TABLE_MEMBER.' m,
        '.TABLE_BASKET_ALL.' ba,
        '.TABLE_BASKET.' b,
        '.TABLE_PRODUCT.' p
      WHERE
        pr.member_id = m.member_id
        AND ba.delivery_id = "'.$delivery_id.'"
        AND ba.basket_id = b.basket_id
        AND b.product_id = p.product_id
        AND p.producer_id = pr.producer_id
      GROUP BY
        business_name,
        p.producer_id
      ORDER BY
        business_name ASC,
        p.producer_id');
    while ( $r4 = mysql_fetch_array($q4) )
      {
        $display_p .= '<option value="'.$r4['producer_id'].'">'.$r4['producer_id'].' : '.stripslashes($r4['business_name']).'</option>';
      }
    $q2 = mysql_query('
      SELECT
        '.TABLE_BASKET_ALL.'.basket_id,
        '.TABLE_BASKET_ALL.'.member_id,
        '.TABLE_MEMBER.'.member_id,
        '.TABLE_BASKET_ALL.'.finalized,
        '.TABLE_MEMBER.'.last_name,
        '.TABLE_MEMBER.'.first_name,
        '.TABLE_MEMBER.'.business_name,
        t.transaction_id,
        t.transaction_amount,
        t.transaction_comments,
        tt.ttype_name,
        t.transaction_producer_id as producer_id
      FROM
        '.TABLE_BASKET_ALL.',
        '.TABLE_MEMBER.',
        '.TABLE_TRANSACTIONS.' t,
        '.TABLE_TRANS_TYPES.' tt,
        producers pr
      WHERE
        t.transaction_delivery_id = "'.$delivery_id.'"
        AND t.transaction_type = tt.ttype_id
        AND 
          (
            tt.ttype_parent = "20"
            OR tt.ttype_parent = "40"
          )
        AND t.transaction_producer_id = pr.producer_id
        AND pr.member_id = '.TABLE_MEMBER.'.member_id
        AND t.transaction_producer_id != ""
        AND t.transaction_producer_id is not null
      GROUP BY
        t.transaction_id
      ORDER BY
        t.transaction_producer_id ASC ');
    while ( $row = mysql_fetch_array($q2) )
      {
        $member_id = $row['member_id'];
        $business_name = stripslashes ($row['business_name']);
        $last_name = $row['last_name'];
        $first_name = $row['first_name'];
        $last_name_2 = $row['last_name_2'];
        $first_name_2 = $row['first_name_2'];
        include("../func/show_name_last.php");
        $display .= '<tr bgcolor="#CCCCCC"><td align="left">';
        $display .= '<a href="orders_prdcr_cust.php?delivery_id='.$delivery_id.'&producer_id='.$row['producer_id'].'">';
        $display .= $row['producer_id'].' : '.stripslashes($row['business_name']).' (Mem # '.$member_id.')</a><br>
          '.$row['ttype_name'].': $'.number_format($row['transaction_amount'],2).'<br>
          '.stripslashes($row['transaction_comments']).'</td>';
        $display .= '<td><form action="'.$PHP_SELF.'?delivery_id='.$delivery_id.'" method="post">
          <input type="hidden" name="transaction_id_passed" value="'.$row['transaction_id'].'">
          <input type="hidden" name="delete_adjustment" value="yes">
          <input type="hidden" name="producer_id" value="'.$row['producer_id'].'">
          <input type="submit" name="where" value="Delete">
          </form>
          </td></tr>';
      }
  }

$sql_adjt = '
  SELECT
    *
  FROM
    '.TABLE_TRANS_TYPES.'
  WHERE
    ttype_status = "1"
    AND
      (
        ttype_parent="20"
        OR ttype_parent="40"
      )
  ORDER BY ttype_whereshow ASC,
    ttype_creditdebit ASC,
    ttype_name ASC';
$result_adjt = @mysql_query($sql_adjt, $connection) or die("".mysql_error()."");
while ( $row = mysql_fetch_array($result_adjt) )
  {
    if ( $row['ttype_taxed'] == 1 )
      {
        $taxed = 'Y';
      }
    else
      {
        $taxed = 'N';
      }
    $display_adjt .= '
      <tr bgcolor="#DDDDDD" style="font-size:9pt;font-family:Arial;">
        <td align="center">'.ucfirst($row['ttype_whereshow']).'</td>
        <td align=center>'.$taxed.'</td>
        <td>'.stripslashes($row['ttype_name']).'</td>
        <td>'.stripslashes($row['ttype_desc']).'</td>
        <td align="center">'.$row['ttype_creditdebit'].'</td>
      </tr>';
  }
$sql_adjt = '
  SELECT
    *
  FROM
    '.TABLE_TRANS_TYPES.'
  WHERE
    ttype_status = "1"
    AND
      (
        ttype_parent="20"
        OR ttype_parent="40"
      )
    AND ttype_whereshow = "'.$adj_type.'"
  ORDER BY
    ttype_name ASC';
$result_adjt = @mysql_query($sql_adjt, $connection) or die("".mysql_error()."");
while ( $row = mysql_fetch_array($result_adjt) )
  {
    $display_adjt_dropdownbox .= '
      <option value="'.$row['ttype_id'].'">'.stripslashes($row['ttype_name']).'</option>';
  }

include("template_hdr.php");?>

<script type="text/javascript">
<!--
function Load_id()
  {
    var delivery_id = document.adjustments.delivery_id.options[document.adjustments.delivery_id.selectedIndex].value
    var id_txt = "?delivery_id="
    var adj_type = document.adjustments.adj_type.options[document.adjustments.adj_type.selectedIndex].value
    var adj_txt = "&adj_type="
    location = id_txt + delivery_id + adj_txt + adj_type
  }
-->
</script>

<?php echo $font;?>

<!-- CONTENT BEGINS HERE -->

<div align="center">
<h3>Invoice Adjustments</h3>

<table cellpadding="7" cellspacing="2" border="0">
  <tr>
    <td colspan="2" bgcolor="#AE58DA" align="left"><b>Add an Adjustment</b> <?php echo $message;?></td>
  </tr>
  <tr>
    <td colspan="2" align="left" bgcolor="#CCCCCC">
      <form action="<?php echo $PHP_SELF;?>?delivery_id=<?php echo $delivery_id;?>" method="post" name="adjustments">
      <table cellpadding="1" cellspacing="1" border="0">
        <tr>
          <td>Type of invoice to apply it to:</td>
          <td>
            <select name="adj_type">
              <option value='0'>Please select a type</option>
<?php
function listEnum($fieldname, $table_name)
  {
    $mysql_datatype_field = 1;
    if (!$result = mysql_query ("SHOW COLUMNS FROM $table_name LIKE '".$fieldname."'") )
      {
        $output=0;
        echo mysql_error();
      }
    else
      {
        $mysql_column_data = mysql_fetch_row( $result );
        if ( !$enum_data= $mysql_column_data[$mysql_datatype_field] )
          {
            $output=0;
          }
        elseif ( !$buffer_array=explode("'", $enum_data) )
          {
            $output = 0;
          }
        else
          {
            $i = 0;
            reset ($buffer_array);
            while (list(, $value) = each ($buffer_array))
              {
                if ( $i % 2 ) $output[stripslashes($value)] = stripslashes($value);
                ++$i;
              }
          }
      }
    return $output;
  }
$types = listEnum('ttype_whereshow','transactions_types');
foreach ( $types as $key => $type )
  {
    if ( $type!='')
      {
        $selected_type = ($type == $adj_type)? "SELECTED":"";
        echo '<option value="'.$key.'" '.$selected_type.'>'.ucfirst($type).'</option>';
      }
  }

?>
            </select>
          </td>
          <td rowspan="6">
            <table cellspacing=1 cellpadding=0 border=0>
              <tr bgcolor='#DDDDDD'>
                <td align=center><b>Which invoice to apply to?</b></td>
                <td align=center><b>Sales Tax Calculated</b></td>
                <td align=center><b>Type</b></td>
                <td align=center><b>Example</b></td>
                <td align=center><b>Credit or Debit</b></td></tr>
                <?php echo $display_adjt;?>
            </table>
          </td>
        </tr>
        <tr>
          <td>Select Delivery Date: </td>
          <td>
            <select name="delivery_id" onChange="Load_id()">
              <option value='0'>Please select a date</option>
              <?php echo $display_dates;?>
            </select>
          </td>
        </tr>
        <tr>
          <td>Select <?php  echo ucfirst($adj_type);?> Invoice: </td>
          <td>
<?php
if ( $adj_type == 'customer' )
  {
?>
            <input type="hidden" name="adj_type" value="customer">
            <select name="basket_id">
              <option value='0'>Please select a basket</option>
              <?php echo $display_baskets;?>
            </select>
<?php
  }
elseif ( $adj_type=='producer' )
  {
?>
            <input type="hidden" name="adj_type" value="producer">
            <select name="producer_id">
              <option value='0'>Please select a producer invoice</option>
              <?php echo $display_p;?>
            </select>
<?php
  }
?>
          </td>
        </tr>
        <tr>
          <td>Type of Adjustment: </td>
          <td>
            <select name="adjt_id">
              <option value="">Select Type of Adjustment</option>
              <?php echo $display_adjt_dropdownbox;?>
            </select>
          </td>
        </tr>
        <tr>
          <td>Payment Method: </td>
          <td>(Only needed for Membership Payments)<br/>
<?php
$sql = mysql_query('
  SELECT
    payment_method
  FROM
    '.TABLE_PAY);
while ( $row = mysql_fetch_array($sql) )
  {
    echo '<input type="radio" name="payment_method" value="'.$row['payment_method'].'"> '.$row['payment_method'].' ';
  }
?>
          </td>
        </tr>
        <tr>
          <td valign=top>Amount: </td>
          <td valign=top>$
            <input type="text" name="adj_amount" size="5" maxlength="6"><br>
            <font size="-2">(No need to add a plus or minus, it will be added for you.)</font>
          </td>
        </tr>
        <tr>
          <td valign=top>Description:</td>
          <td valign=top>
            <textarea name="adj_desc" rows="2" cols="30"></textarea>
            <input type="hidden" name="adjustment_submitted" value="yep">
            <input type="submit" name="where" value="Submit Adjustment">
          </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
<?php echo $display;?>
</table>
</div>
<br><br>
  <!-- CONTENT ENDS HERE -->
<?php include("template_footer.php");?>
