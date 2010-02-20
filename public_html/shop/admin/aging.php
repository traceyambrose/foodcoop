<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();
include("member_balance_function.php");

$balance = array();
$balances = array();
$sql = mysql_query("SELECT member_id FROM members where member_id<'10' ORDER BY member_id ASC");
while ( $result = mysql_fetch_array($sql) )
  {
    $balance = getMemberBalance($result['member_id'],$current_delivery_id,"");
    if ( is_array($balance) )
      {
        $balance = array_slice($balance,-3,3);
        $balances[$result['member_id']] = $balance;
      }
  }

// sort by overall balance
if ( is_array($balances) )
  {
    foreach ( $balances as $member_id=>$balance )
      {
        $end = array_pop($balance);
        $overall[$member_id]  = $end['balance'];
      }
  }
if ( is_array($overall) )
  {
    $total_all = array_sum($overall);
  }
// Sort the data with volume descending, edition ascending
// Add $data as the last parameter, to sort by the common key
if ( is_array($overall) && is_array($balances) )
  {
    array_multisort($overall, SORT_NUMERIC, SORT_DESC, $balances);
  }
$end = array();
$aging = '';
foreach ( $balances as $key => $balance )
  {
    $end = array_pop($balance);
    $thirty = $end['total']-$end['amount_paid'];
    $total =  $end['balance'];
    $end = array_pop($balance);
    $sixty = $end['total']-$end['amount_paid'];
    $end = array_pop($balance);
    $ninety = $end['balance'];
    if ( $total != 0 )
      {
        $sql = mysql_query('
          SELECT
            first_name,
            last_name,
            first_name_2,
            last_name_2,
            business_name,
            address_line1,
            address_line2,
            city,
            state,
            zip
          FROM
            '.TABLE_MEMBER.'
          WHERE
            member_id = "'.$end['member_id'].'"
          LIMIT 1');
        while ( $row = mysql_fetch_array($sql) )
          {
            $first_name = $row['first_name'];
            $last_name = $row['last_name'];
            $first_name_2 = $row['first_name_2'];
            $last_name_2 = $row['last_name_2'];
            $business_name = stripslashes ($row['business_name']);
            $address = $row['address_line1'];
            if ( $row['address_line2'] )
              {
                $address .= ', '.$row['address_line2'];
              }
            include("../func/show_name.php");
            $aging .= '
              <tr style="background-color:#FFFFFF;">
              <td># '.$end['member_id'].'</td>
              <td><a href="member-balances-lookup-test.php?m='.$end['member_id'].'" target="_blank">'.$show_name.'</a></td>
              <td>'.$address.'</td>
              <td>'.$row['city'].', '.$row['state'].' '.$row['zip'].'</td>
              <td style="text-align:right;">$'.number_format($thirty,2).'</td>
              <td style="text-align:right;">$'.number_format($sixty,2).'</td>
              <td style="text-align:right;">$'.number_format($ninety,2).'</td>
              <td style="text-align:right;font-weight:bold;">$'.number_format($total,2).'</td>
              </tr>
              ';
          }
      }
  }

include("template_hdr.php"); ?>

<!-- CONTENT BEGINS HERE -->

<div align="center">
<table width="80%">
  <tr>
    <td align="left">
      <div align="center">
        <h3> Members with Credits or Outstanding Balances</h3>
        Click on the member&#146;s name to see more detail.<br><br>
      </div>
      <table cellspacing='1' style="background-color:#000000;font-family:Verdana;font-size:12px;">
        <tr bgcolor='#AEDE86'>
          <th>Mem #</th>
          <th>Name and Link to Account Detail</th>
          <th>Address</th>
          <th>City, State, Zip</th>
          <th> (Delivery: <?php echo $current_delivery_id;?>)<br /><=30 Days</th>
          <th> (Delivery: <?php echo $current_delivery_id-1;?>)<br /><=60 Days</th>
          <th> (Delivery: <?php echo $current_delivery_id-2;?>)<br />>90 Days</th>
          <th style="text-align:center;">Total</th>
        </tr>
        <?php echo $aging;?>
        <tr bgcolor="#AEDE86">
          <td colspan=8 align=right><b>Total: &nbsp;&nbsp;<?php echo '$'.number_format($total_all,2); ?></b></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</div>
  <!-- CONTENT ENDS HERE -->

<?php include("template_footer.php");?>
