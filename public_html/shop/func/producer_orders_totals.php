<?php

//                                                                           //
// This script will add a table of producer totals to be used at the bottom  //
// of producer invoices.  It requires values for these variables to already  //
// be set:                                                                   //
//                 $total                                                    //
//                 $message_incomplete                                       //
//                 $current_delivery_id                                      //
//                 $producer_id_you                                          //
//                                                                           //

// If we were sent finalization information, then post it to the database...
if ( $_POST['submit'] == 'Store Producer Totals' )
  {
    include("../admin/producer_finalize.php");
    producer_finalize::finalize($_POST);
    $message = '<h3>The information has been saved.</h3>';
  }

$subtotal = round($total + 0.00000001,2);
if ( $message_incomplete ) 
  {
    $subtotal_display = $message_incomplete;
  }
else
  {
    $subtotal_display = '$'.number_format($subtotal, 2);
  }

$producer_fee = round((($subtotal - $total_extra) * PRODUCER_MARKDOWN) + 0.00000001,2);
$fee = (100 * PRODUCER_MARKDOWN).'%';

if ( $message_incomplete )
  {
    $producer_fee_display = '';
  }
else
  {
    $producer_fee_display = '$'.number_format($producer_fee, 2);
  }

$query = '
  SELECT
    transaction_name,
    transaction_comments,
    transaction_amount
  FROM
    '.TABLE_TRANSACTIONS.' AS t,
    '.TABLE_TRANS_TYPES.' AS tt  
  WHERE
    transaction_delivery_id = '.$delivery_id.'
    AND transaction_producer_id = "'.$producer_id.'"
    AND t.transaction_type = tt.ttype_id 
    AND tt.ttype_parent = 20
    AND t.transaction_taxed = 0';
$sqla = mysql_query($query);
$adjustment_display = '';
$adjustment_header = '
      <tr>
        <td colspan="4" bgcolor="#dddddd" align="center"><font size=4>Adjustments</font></td>
      </tr>';
while ( $resulta = mysql_fetch_array($sqla) )
  {
    $adjustment_display .= '
      <tr>
        <td style="border-bottom:1px solid #666;" align="left" valign="top" width="25%">'.$resulta['transaction_name'].'</td>
        <td style="border-bottom:1px solid #666;" align="left" valign="top" colspan="2" width="50%">'.$resulta['transaction_comments'].'</td>
        <td style="border-bottom:1px solid #666;" align="right" valign="top" width="25%">$'.number_format($resulta['transaction_amount'], 2).'</td>
      </tr>';
    $subtotal_pr = $subtotal_pr + $resulta['transaction_amount'];
    $total2 = $total2 + $resulta['transaction_amount'];
  }

$final_total = $subtotal + $total2 - $producer_fee;

if ( $message_incomplete )
  {
    $final_total_display = $message_incomplete;
  }
else
  {
    $final_total_display = '$'.number_format($final_total, 2);
  }

// Only allow administrators to access the finalization button
if (strpos ($_SESSION['auth_type'], 'administrator') !== false )
  {
    $finalize_button_code = '
            <div align="center">
              <form method="POST" action="'.$_SERVER['PHP_SELF'].'?delivery_id='.$delivery_id.'&producer_id='.$producer_id.'">
              <input type="hidden" name="producer_id" value="'.$producer_id.'">
              <input type="hidden" name="delivery_id" value="'.$delivery_id.'">
              <input type="hidden" name="transaction_amount[28]" value="'.number_format($subtotal, 2, '.', '').'">
              <input type="hidden" name="transaction_amount[31]" value="'.number_format($producer_fee, 2, '.', '').'">
              <input type="hidden" name="transaction_amount[35]" value="'.number_format($final_total, 2, '.', '').'">
              <input type="submit" name="submit" value="Store Producer Totals">
              </form>
            </div>
          ';
  }
else
  {
    $finalize_button_code = '';
  }

$producer_orders_totals = '
      <table border="0" cellpadding="2" cellspacing="0" width="100%">
        '.($adjustment_display? $adjustment_header.$adjustment_display : '').'
        <tr>
          <td colspan="2" width="50%">&nbsp;</td>
          <td align="right" width="25%">Total</td>
          <td align="right" width="25%">'.$subtotal_display.'</td>
        </tr>

        <tr>
          <td colspan="2" width="50%">&nbsp;</td>
          <td align="right" width="25%"><b>'.$fee.' Coop Fee</b></td>
          <td align="right" width="25%">'.$producer_fee_display.'</td>
        </tr>
        <tr>
          <td colspan="2" width="50%">&nbsp;</td>
          <td align="right" width="25%"><b>TOTAL with Coop Fee</b></td>
          <td align="right" width="25%"><b>'.$final_total_display.'</b></td>
        </tr>
        <tr>
          <td colspan="4" width="100%">&nbsp;'.$finalize_button_code.'</td>
        </tr>
      </table>';
?>
