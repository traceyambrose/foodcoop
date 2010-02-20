<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();
function valid_email($address)
  {
    if ( ereg('^[a-zA-Z0-9\.\_\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$', $address) )
      return true;
    else
      return false;
  }
function getTransactions_TypeName($ttype_id)
  {
    $sql = mysql_query('
      SELECT
        ttype_name
      FROM
        '.TABLE_TRANS_TYPES.'
      WHERE
        ttype_id="'.$ttype_id.'" limit 1');
    $row = mysql_fetch_array($sql);
    return $row['ttype_name'];
  }
function getTransactions_TypeValue($ttype_id)
  {
    $sql = mysql_query('
      SELECT
        ttype_value
      FROM
        '.TABLE_TRANS_TYPES.'
      WHERE
        ttype_id = "'.$ttype_id.'"');
    $value = mysql_fetch_array($sql);
    return $value['ttype_value'];
  }
function getMemberContactInfo($member_id)
  {
    $sql = mysql_query('
      SELECT
        first_name,
        last_name,
        email_address
      FROM
        '.TABLE_MEMBER.'
      WHERE
        member_id = "'.$member_id.'" LIMIT 1');
    $result = mysql_fetch_array($sql);
    return $result;
  }
function sendApprovalEmail($member_id)
  {
    $memberinfo = getMemberContactInfo($member_id);
    if ( valid_email($memberinfo['email_address']) )
      {
        //$to = MEMBERSHIP_EMAIL;
        $to = $memberinfo['email_address'];
        $subject = 'Welcome to the '.SITE_NAME;
      }
    else
      {
        $to = MEMBERSHIP_EMAIL;
        $subject = 'Approval email could not be sent - invalid email address';
      }
    $headers = "From: ".MEMBERSHIP_EMAIL."\r\nReply-To: ".MEMBERSHIP_EMAIL."\r\n";
    $headers .= "Errors-To: ".GENERAL_EMAIL."\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/plain; charset=us-ascii\r\n";
    $headers .= "Message-ID: <".md5(uniqid(time()))."@".DOMAIN_NAME.">\r\n";
    $headers .= "X-Mailer: PHP ".phpversion()."\r\n";
    $headers .= "X-Priority: 3\r\n";
    $headers .= "X-AntiAbuse: This is a user-submitted email through the coop membership approval page.";
    $msg = "Dear ".stripslashes($memberinfo['first_name'])." ".stripslashes($memberinfo['last_name']).",\n\n";
    $msg .= "Your membership is now activated. Here's some info that will come in handy:\n\n";
    $msg .= "Welcome to the food ".ORGANIZATION_TYPE.".   You have been entered as a member. \n";
    $msg .= "You may now shop online using this link (when the order period is open). \n";
    $msg .= BASE_URL.PATH."members/\n\n";
    $msg .= "Many of your questions can be answered at our web site- \n";
    $msg .= BASE_URL."\n";
    $msg .= "Producer help is available at-\n";
    $msg .= PRODUCER_CARE_EMAIL."\n";
    $msg .= "Help is always available at-\n";
    $msg .= HELP_EMAIL."\n";
    $msg .= "Join in the fun, volunteer!\n";
    $msg .= VOLUNTEER_EMAIL."\n\n";
    $msg .= "If I can be of any help to you or you have any questions, please contact me. \n\n";
    $msg .= "Membership Chair,\n\n";
    $msg .= MEMBERSHIP_EMAIL;
    mail($to, $subject, $msg, $headers);
  }
$case1 = '';
$case2 = '';
$case3 = '';
$case4 = '';
$case5 = '';
$case6 = '';
$case7 = '';
//Pending = 1 not approved, not paid
//Pending = 2   approved, not paid
//Pending = 3 not approved, paid
$counter = $_POST['counter'];
$count = 1;
while ( $count <= $counter )
  {
    $member_id = $_POST["member_id$count"];
    $status = $_POST["status$count"];
    $pdType = $_POST["pdType$count"];
    $comments = $_POST["comments$count"];
    switch ( $status )
      {
        case ( 'Pending' ):
          if ( $pdType == '' )
            {
              // case 1
              // no change
              $case1 .= '<br><u>CASE 1: MemberID = '.$member_id.' and Status = '.$status.' and Paid = '.$pdType.'</u>';
            }
          elseif ( $pdType == 'Done' )
            {
              //case 3
              $case3 .= '<br><u>CASE 3: MemberID = '.$member_id.' and Status = '.$status.' and Paid = '.$pdType.'</u>';
              // Member table action - If the member is not already marked as pending=3, update
              $sql = '
                UPDATE
                  '.TABLE_MEMBER.'
                SET
                  pending="3"
                WHERE
                  member_id="'.$member_id.'"';
              //$case3 .= '<br />'.$sql.'<br />';
              $result = mysql_query($sql);
              // Transaction table action  - update comments
              if ( $comments )
                {
                  $sql = '
                    UPDATE
                      '.TABLE_TRANSACTIONS.'
                    SET
                      transactions_comments="'.addslashes($comments).'"
                    WHERE
                      member_id="'.$member_id.'"';
                  //$case3 .= '<br />'.$sql.'<br />';
                  $result = mysql_query($sql);
                }
            }
          else
            {
              // case 2
              $case2 .= '<br><u>CASE 2: MemberID = '.$member_id.' and Status = '.$status.' and Paid = '.$pdType.'</u>';
              // Member table action - pending=3
              $sql = '
                UPDATE
                  '.TABLE_MEMBER.'
                SET
                  pending="3"
                WHERE
                  member_id="'.$member_id.'"';
              //$case2 .= '<br />'.$sql.'<br />';
              $result = mysql_query($sql);
              // Transaction table action
              // see if the membership payment transaction has been recorded yet (no receiveables because not approved)
              $sql3 = mysql_query('
                SELECT
                  transaction_basket_id
                FROM
                  '.TABLE_TRANSACTIONS.'
                WHERE
                  transaction_type="25"
                  AND transaction_member_id="'.$member_id.'"
                LIMIT 1');
              if ( mysql_num_rows($sql3) < 1 )
                {
                  // if the membership payment transaction hasn't been recorded yet, add it
                  // add basket later
                  $sql = '
                    INSERT INTO
                      '.TABLE_TRANSACTIONS.'
                    SET
                      transaction_type= "25",
                      transaction_name= "'.getTransactions_TypeName(25).'",
                      transaction_amount= "'.getTransactions_TypeValue(25).'",
                      transaction_user= "'.$_SESSION["valid_c"].'",
                      transaction_producer_id= "",
                      transaction_member_id= "'.$member_id.'",
                      transaction_delivery_id= "'.$current_delivery_id.'",
                      transaction_taxed= "0",
                      transaction_timestamp= now(),
                      transaction_batchno= null,
                      transaction_memo= null,
                      transaction_comments= "'.addslashes($comments).'",
                      transaction_method= "'.preg_replace('/[^A-Z]/', '', $pdType).'"';
                  //$case2 .= '<br />'.$sql.'<br />';
                  $result = mysql_query($sql);
                }
            }
          break;
        case ( 'Approved' ):
          if ( $pdType == '' )
            {
              //case 4
              $case4 .= '<br><u>CASE 4: MemberID = '.$member_id.' and Status = '.$status.' and Paid = '.$pdType.'</u>';
              // Member table action - pending=2, update
              $sql = '
                UPDATE
                  '.TABLE_MEMBER.'
                SET
                  pending="2"
                WHERE
                  member_id="'.$member_id.'"';
              //$case4 .= '<br />'.$sql.'<br />';
              $result = mysql_query($sql);
              // Transaction table action
              // see if the membership receivables transaction has been recorded yet
              $sql3 = mysql_query('
                SELECT
                  transaction_basket_id
                FROM
                  '.TABLE_TRANSACTIONS.'
                WHERE
                  transaction_type="24"
                  AND transaction_member_id="'.$member_id.'"
                LIMIT 1');
              if ( mysql_num_rows($sql3) < 1 )
                {
                  // get the value of this transaction type
                  $sql = mysql_query('
                    SELECT
                      ttype_value
                    FROM
                      '.TABLE_TRANS_TYPES.'
                    WHERE
                      ttype_id = "24"');
                  $value = mysql_fetch_array($sql);
                  // if the membership payment transaction hasn't been recorded yet, add it
                  // add basket id later
                  $sql = '
                    INSERT INTO
                      '.TABLE_TRANSACTIONS.'
                    SET
                      transaction_type= "24",
                      transaction_name= "'.getTransactions_TypeName(24).'",
                      transaction_amount= "'.getTransactions_TypeValue(24).'",
                      transaction_user= "'.$_SESSION["valid_c"].'",
                      transaction_producer_id= "",
                      transaction_member_id= "'.$member_id.'",
                      transaction_delivery_id= "'.$current_delivery_id.'",
                      transaction_taxed= "0",
                      transaction_timestamp= now(),
                      transaction_batchno= null,
                      transaction_memo= null,
                      transaction_comments= "'.addslashes($comments).'",
                      transaction_method= null';
                  //$case4 .= '<br />'.$sql.'<br />';
                  $result = mysql_query($sql);
                }
            }
          elseif ( $pdType == 'Done' )
            {
              //case 6
              $case6 .= '<br><u>CASE 6: MemberID = '.$member_id.' and Status = '.$status.' and Paid = '.$pdType.'</u>';
              // Member table action - pending=0, update
              $sql = '
                UPDATE
                  '.TABLE_MEMBER.'
                SET
                  pending="0"
                WHERE
                  member_id="'.$member_id.'"';
              //$case6 .= '<br />'.$sql.'<br />';
              $result = mysql_query($sql);
              // Customer basket overall action - add new basket if none exists
              $basket_id = 0;
              // See if a basket has been created yet to house their payment
              $sql2 = mysql_query('
                SELECT
                  basket_id
                FROM
                  customer_basket_overall
                WHERE
                  member_id="'.$member_id.'"
                  AND delivery_id="'.$current_delivery_id.'"
                LIMIT 1');
              if ( mysql_num_rows($sql2) < 1 )
                {
                  // Create a new basket
                  // select member preferences to build new basket
                  $sql = mysql_query('
                    SELECT
                      mp.delcode_id,
                      mp.deltype,
                      mp.payment_method,
                      dc.delcharge
                    FROM
                      '.TABLE_MEMBER_PREF.' mp,
                      '.TABLE_DELCODE.' dc
                    WHERE
                      mp.member_id = "'.$member_id.'"
                      AND mp.delcode_id = dc.delcode_id
                    LIMIT 1');
                  if ( mysql_num_rows($sql) > 0 )
                    {
                      $prefs = mysql_fetch_array($sql);
                    }
                  // if no preferences, default it to the main sorting site, pick up, pay by check
                  if ( !$prefs['delcode_id'] )
                    {
                      $prefs['delcode_id'] = 'HLY';
                    }
                  if ( !$prefs['deltype'] )
                    {
                      $prefs['deltype'] = 'P';
                    }
                  if ( !$payment_type )
                    {
                      if ( $prefs['payment_method'] )
                        {
                          $payment_type = $prefs['payment_method'];
                        }
                      else
                        {
                          $payment_type = 'C';
                        }
                    }
                  if ( !$prefs['delcharge'] )
                    {
                      $prefs['delcharge'] = 0;
                    }
                  $sql = '
                    INSERT INTO
                      customer_basket_overall
                        (
                          member_id,
                          delivery_id,
                          delcode_id,
                          deltype,
                          delivery_cost,
                          payment_method
                        )
                      VALUES
                        (
                          "'.$member_id.'",
                          "'.$current_delivery_id.'",
                          "'.$prefs["delcode_id"].'",
                          "'.$prefs["deltype"].'",
                          "'.$prefs["delcharge"].'",
                          "'.$payment_type.'"
                        )';
                  $case6 .= '<br />'.$sql.'<br />';
                  $result = mysql_query($sql);
                  $basket_id = mysql_insert_id();
                }
              else
                {
                  // Else, return the basket id to use in the transaction entry
                  $row2 = mysql_fetch_array($sql2);
                  $basket_id = $row2['basket_id'];
                  $case6 .= '<br />Retrieved basket id: '.$basket_id.'<br />';
                }
              // Transaction table action
              // see if the membership receivables transaction has been recorded yet
              $sql3 = mysql_query('
                SELECT
                  transaction_basket_id
                FROM
                  '.TABLE_TRANSACTIONS.'
                WHERE
                  transaction_type="24"
                  AND transaction_member_id="'.$member_id.'"
                LIMIT 1');
              if ( mysql_num_rows($sql3) < 1 )
                {
                  // get the value of this transaction type
                  $sql = mysql_query('
                    SELECT
                      ttype_value
                    FROM
                      '.TABLE_TRANS_TYPES.'
                    WHERE
                      ttype_id = "24"');
                  $value = mysql_fetch_array($sql);
                  // if the membership payment transaction hasn't been recorded yet, add it
                  // add basket id later
                  $sql = '
                    INSERT INTO
                      '.TABLE_TRANSACTIONS.'
                    SET
                      transaction_type= "24",
                      transaction_name= "'.getTransactions_TypeName(24).'",
                      transaction_amount= "'.getTransactions_TypeValue(24).'",
                      transaction_user= "'.$_SESSION["valid_c"].'",
                      transaction_producer_id= "",
                      transaction_member_id= "'.$member_id.'",
                      transaction_basket_id= "'.$basket_id.'",
                      transaction_delivery_id= "'.$current_delivery_id.'",
                      transaction_taxed= "0",
                      transaction_timestamp= now(),
                      transaction_batchno= null,
                      transaction_memo= null,
                      transaction_comments= "'.addslashes($comments).'",
                      transaction_method= null';
                  //$case4 .= '<br />'.$sql.'<br />';
                  $result = mysql_query($sql);
                }
              // see if the existing membership transaction has a basket id, update if not
              $sql3 = mysql_query('
                SELECT
                  transaction_id,
                  transaction_basket_id
                FROM
                  '.TABLE_TRANSACTIONS.'
                WHERE
                  (
                    transaction_type="24"
                    OR transaction_type="25"
                  )
                  AND transaction_member_id="'.$member_id.'"
                LIMIT 1');
              if ( mysql_num_rows($sql3) > 0 )
                {
                  while ( $result = mysql_fetch_array($sql3) )
                    {
                      if ( $result['transaction_basket_id'] < 1 && $result['transaction_id'] && $basket_id > 0)
                        {
                          $sql = mysql_query('
                            UPDATE
                              '.TABLE_TRANSACTIONS.'
                            SET
                              transaction_basket_id = "'.$basket_id.'"
                            WHERE
                              transaction_id = "'.$result["transaction_id"].'"
                              AND transaction_member_id= "'.$member_id.'"');
                          //$case6 .= '<br />'.$sql.'<br />';
                          $result = mysql_query($sql);
                        }
                    }
                }
              // Transaction table action  - update comments
              if ( $comments )
                {
                  $sql = '
                    UPDATE
                      '.TABLE_TRANSACTIONS.'
                    SET
                      transactions_comments="'.addslashes($comments).'"
                    WHERE
                      member_id="'.$member_id.'"';
                  //$case6 .= '<br />'.$sql.'<br />';
                  $result = mysql_query($sql);
                }
              // Send approval email
              sendApprovalEmail($member_id);
            }
          else
            {
              // case 5
              $case5 .= '<br><u>CASE 5: MemberID = '.$member_id.' and Status = '.$status.' and Paid = '.$pdType.'</u>';
              // Member table action - pending=0, update
              $sql = '
                UPDATE
                  '.TABLE_MEMBER.'
                SET
                  pending="0"
                WHERE
                  member_id="'.$member_id.'"';
              //$case5 .= '<br />'.$sql.'<br />';
              $result = mysql_query($sql);
              // Customer basket overall action - add new basket if none exists
              $basket_id = 0;
              // See if a basket has been created yet to house their payment
              $sql2 = mysql_query('
                SELECT
                  basket_id
                FROM
                  customer_basket_overall
                WHERE
                  member_id="'.$member_id.'"
                  AND delivery_id="'.$current_delivery_id.'"
                LIMIT 1');
              if ( mysql_num_rows($sql2) < 1 )
                {
                  // Create a new basket
                  // select member preferences to build new basket
                  $sql = mysql_query('
                    SELECT
                      mp.delcode_id,
                      mp.deltype,
                      mp.payment_method,
                      dc.delcharge
                    FROM
                      '.TABLE_MEMBER_PREF.' mp,
                      '.TABLE_DELCODE.' dc
                    WHERE
                      mp.member_id = "'.$member_id.'"
                      AND mp.delcode_id = dc.delcode_id
                    LIMIT 1');
                  if ( mysql_num_rows($sql) > 0 )
                    {
                      $prefs = mysql_fetch_array($sql);
                    }
                  // if no preferences, default it to the main sorting site, pick up, pay by check
                  if ( !$prefs['delcode_id'] )
                    {
                      $prefs['delcode_id'] = 'HLY';
                    }
                  if ( !$prefs['deltype'] )
                    {
                      $prefs['deltype'] = 'P';
                    }
                  $pdType = preg_replace('/[^A-Z]/','',$pdType); // use the payment method used to pay their membership dues
                  if ( !$pdType && $pdType != 'Done' )
                    {
                      if ( $prefs['payment_method'] )
                        {
                          $pdType = $prefs['payment_method'];
                        }
                      else
                        {
                          $pdType = 'C';
                        }
                    }
                  if ( !$prefs['delcharge'] )
                    {
                      $prefs['delcharge'] = 0;
                    }
                  $sql = '
                    INSERT INTO
                      customer_basket_overall
                        (
                          member_id,
                          delivery_id,
                          delcode_id,
                          deltype,
                          delivery_cost,
                          payment_method
                        )
                      VALUES
                        (
                          "'.$member_id.'",
                          "'.$current_delivery_id.'",
                          "'.$prefs["delcode_id"].'",
                          "'.$prefs["deltype"].'",
                          "'.$prefs["delcharge"].'",
                          "'.$pdType.'"
                        )';
                  $case5 .= '<br />'.$sql.'<br />';
                  $result = mysql_query($sql);
                  $basket_id = mysql_insert_id();
                }
              else
                {
                  // Else, return the basket id to use in the transaction entry
                  $row2 = mysql_fetch_array($sql2);
                  $basket_id = $row2['basket_id'];
                  $case5 .= '<br />Retrieved basket id: '.$basket_id.'<br />';
                }
              // Transaction table action
              // see if the membership receivables transaction has been recorded yet
              $sql3 = mysql_query('
                SELECT
                  transaction_id,
                  transaction_basket_id
                FROM
                  '.TABLE_TRANSACTIONS.'
                WHERE
                  transaction_type="24"
                  AND transaction_member_id="'.$member_id.'"
                LIMIT 1');
              if ( mysql_num_rows($sql3) < 1 )
                {
                  // if the membership receivables transaction hasn't been recorded yet, add it
                  $sql = '
                    INSERT INTO
                      '.TABLE_TRANSACTIONS.'
                    SET
                      transaction_type= "24",
                      transaction_name= "'.getTransactions_TypeName(24).'",
                      transaction_amount= "'.getTransactions_TypeValue(24).'",
                      transaction_user= "'.$_SESSION["valid_c"].'",
                      transaction_producer_id= "",
                      transaction_member_id= "'.$member_id.'",
                      transaction_basket_id= "'.$basket_id.'",
                      transaction_delivery_id= "'.$current_delivery_id.'",
                      transaction_taxed= "0",
                      transaction_timestamp= now(),
                      transaction_batchno= null,
                      transaction_memo= null,
                      transaction_comments= "'.addslashes($comments).'",
                      transaction_method= null';
                  $case5 .= '<br />'.$sql.'<br />';
                  $result = mysql_query($sql);
                }
              elseif ( mysql_num_rows($sql3) > 0 )
                {
                  // see if the existing transaction has a basket id, update if not
                  $result = mysql_fetch_array($sql3);
                  if ( $result['transaction_basket_id'] < 1 && $result['transaction_id'] && $basket_id > 0 )
                    {
                      $sql = mysql_query('
                        UPDATE
                          '.TABLE_TRANSACTIONS.'
                        SET
                          transaction_basket_id = "'.$basket_id.'"
                        WHERE
                          transaction_id = "'.$result["transaction_id"].'"
                          AND transaction_member_id= "'.$member_id.'"');
                      //$case5 .= '<br />'.$sql.'<br />';
                      $result = mysql_query($sql);
                    }
                }
              // see if the membership payment transaction has been recorded yet
              $sql3 = mysql_query('
                SELECT
                  transaction_id,
                  transaction_basket_id
                FROM
                  '.TABLE_TRANSACTIONS.'
                WHERE
                  transaction_type="25"
                  AND transaction_member_id="'.$member_id.'"
                LIMIT 1');
              if ( mysql_num_rows($sql3) < 1 )
                {
                  // if the membership payment transaction hasn't been recorded yet, add it
                  $sql = '
                    INSERT INTO
                      '.TABLE_TRANSACTIONS.'
                    SET
                      transaction_type= "25",
                      transaction_name= "'.getTransactions_TypeName(25).'",
                      transaction_amount= "'.getTransactions_TypeValue(25).'",
                      transaction_user= "'.$_SESSION["valid_c"].'",
                      transaction_producer_id= "",
                      transaction_member_id= "'.$member_id.'",
                      transaction_basket_id= "'.$basket_id.'",
                      transaction_delivery_id= "'.$current_delivery_id.'",
                      transaction_taxed= "0",
                      transaction_timestamp= now(),
                      transaction_batchno= null,
                      transaction_memo= null,
                      transaction_comments= "'.addslashes($comments).'",
                      transaction_method= "'.preg_replace('/[^A-Z]/', '', $pdType).'"';
                  $case5 .= '<br />'.$sql.'<br />';
                  $result = mysql_query($sql);
                }
              elseif ( mysql_num_rows($sql3) > 0 )
                {
                  // see if the existing transaction has a basket id, update if not
                  $result = mysql_fetch_array($sql3);
                  if ( $result['transaction_basket_id'] < 1 && $result['transaction_id'] && $basket_id > 0 )
                    {
                      $sql = mysql_query('
                        UPDATE
                          '.TABLE_TRANSACTIONS.'
                        SET
                          transaction_basket_id = "'.$basket_id.'"
                        WHERE
                          transaction_id = "'.$result["transaction_id"].'"
                          AND transaction_member_id= "'.$member_id.'"');
                      //$case5 .= '<br />'.$sql.'<br />';
                      $result = mysql_query($sql);
                    }
                }
              // Send approval email
              sendApprovalEmail($member_id);
            }
          break;
        case ( "Remove" && ('' || 'C' || 'P' || 'Done') ):
          $case7 .= '<br><u>CASE 7: MemberID = '.$member_id.' and Status = '.$status.' and Paid = '.$pdType.'</u>';
          // Member table action - pending=0, update
          $sql = '
            UPDATE
              '.TABLE_MEMBER.'
            SET
              membership_discontinued="1"
            WHERE
              member_id="'.$member_id.'"';
          //$case7 .= '<br />'.$sql.'<br />';
          $result = mysql_query($sql);
          break;
        default:
          $display .= 'Error with Member id = '.$member_id;
      }
    $count = $count + 1;
  }
include ("template_hdr.php");?>
<!-- CONTENT BEGINS HERE -->
<!--//Pending = 1 not approved, not paid
//Pending = 2   approved, not paid
//Pending = 3 not approved, paid-->
<div align="center">
<table width="80%">
  <tr>
    <td align="left">
      <div align="center">
        <h3>Hi! You&#146;ve reached the Process Pending Website</h3>
      </div>
      <div align="left">
        There are <?php echo $counter;?> Records to be Processed <br><br />
        <?php echo $display;?>
        <h4>CASE 1: Pending &amp; Unpaid (Pending=1)</h4>
        Do Nothing<br />
        <?php echo $case1;?>
        <h4>CASE 2: Pending&amp; Paid (Pending=3)</h4>
        Record in table the transaction information, but hold for approval of member <br />
        <?php echo $case2;?>
        <h4>CASE 3: Pending&amp; Already Paid Before (Pending=3)</h4>
        Check comments
        <?php echo $case3;?>
        <h4>CASE 4: Approved&amp; Unpaid (Pending=2)</h4>
        <?php echo $case4;?>
        <h4>CASE 5: Approved&amp; Paid (Pending=0)</h4>
        <?php echo $case5;?>
        <h4>CASE 6: Approved&amp; Already Paid Before (Pending=0)</h4>
        <?php echo $case6;?>
        <h4>CASE 7: Remove&amp; Unpaid/Paid</h4>
        Set Disabled Membership = 1<br />
        <?php echo $case7;?><br />
        <br></br>
        <a href="pending_members_list.php">Return</a>
      </div>
    </td>
  </tr>
</table>
</div>
  <!-- CONTENT ENDS HERE -->
<?php include ("template_footer.php");?>