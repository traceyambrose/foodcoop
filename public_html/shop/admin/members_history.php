<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();
include("member_balance_function.php");

$delivery_id = $current_delivery_id;
$prev_grand_total_total = '';
$prev_paypal_total = '';
$amount_paid_total = '';
$previous_balance = '';
$balances = array();
$sql = mysql_query('
  SELECT
    member_id 
  FROM
    '.TABLE_MEMBER.'
  ORDER BY member_id ASC');
while ( $result = mysql_fetch_array($sql) )
  {
    $balances[$result['member_id']] = getMemberBalance($result['member_id'], $current_delivery_id, '');
  }
?>
<?php include("template_hdr.php");?>
  <!-- CONTENT BEGINS HERE -->
<div align="center">
  <table width="80%">
    <tr>
      <td align="left">
        <div align="center">
          <h3> Members with Credits or Outstanding Balances</h3>
          <img src='grfx/icon_arrow_right.gif' width=13 height=13 border=0>Click the blue arrow for a detailed breakdown of the member's payment history.<br>
          Click on the member's name for a list of all previous invoices.<br><br>
        </div>
        <table border=0 cellpadding="0" cellspacing="0">
          <tr>
            <td valign=top>
              <table>
                <tr bgcolor=#DDDDDD>
                  <th>Mem#</th>
                  <th>Customer Name and Link to Current Invoice</th>
                  <th>Grand Total</th>
                  <th>Paypal</th>
                  <th>Amount Paid</th>
                  <th>Balance</th>
                  <th bgcolor=#FFFFFF></th>
                </tr>
                <?php echo $display;?>
                <tr bgcolor="#AEDE86">
                  <td colspan=6 align=right><b>Total: &nbsp;&nbsp;<?php echo "\$".number_format($balance_total,2)."";?></b></td>
                  <td bgcolor=#FFFFFF></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</div>
  <!-- CONTENT ENDS HERE -->
<?php include("template_footer.php");?>
