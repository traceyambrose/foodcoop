<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();


// If we received posted information, then begin by making the updates
if ($_POST['action'] == 'Submit')
  {
    $counter = $_POST['counter'];
    $count = 0;
    while ( $count++ <= $counter )
      {
        $member_id = $_POST["member_id$count"];
        $status = $_POST["status$count"];
        $payment_method = $_POST["payment_method$count"];
        $payment_amount = $_POST["payment_amount$count"];
        $comments = $_POST["comments$count"];

        // Post any payments that were received
        if ($payment_amount != 0) // Changed from " > 0" to allow negative payments.
          {
            if ($payment_method != '')
              {
                $query = '
                  INSERT INTO
                    '.TABLE_TRANSACTIONS.'
                      (
                        transaction_type,
                        transaction_name,
                        transaction_amount,
                        transaction_user,
                        transaction_member_id,
                        transaction_delivery_id,
                        transaction_taxed,
                        transaction_method,
                        transaction_comments,
                        transaction_timestamp
                      )
                    VALUES
                      (
                        "25",
                        "Membership Payment Received",
                        "'.round (-$payment_amount, 2).'",
                        "'.$_SESSION['valid_c'].'",
                        "'.$member_id.'",
                        (SELECT delivery_id FROM '.TABLE_CURDEL.'),
                        "0",
                        "'.$payment_method.'",
                        "'.$comments.'",
                        now()
                      )';
                $sql = @mysql_query($query, $connection) or die("Couldn't execute query 1.");
              }
            else // No payment method was given, so flag the error
              {
                $error_message .= '<p class="error">ERROR: No payment method was entered for member # '.$member_id.'.</p>';
              }
          }
//         elseif ($payment_amount < 0) // Negative payments are not allowed
//           {
//             $error_message .= '<p class="error">ERROR: Negative payment was attempted for member # '.$member_id.'.</p>';
//           }
        // Change the pending status
        if ($status == 'Pending')
          {
            $query = '
              UPDATE
                '.TABLE_MEMBER.'
              SET
                pending = "1"
              WHERE
                member_id = "'.$member_id.'"';
            $sql = @mysql_query($query, $connection) or die("Couldn't execute query 2.");
          }
        if ($status == 'Approved')
          {
            $query = '
              SELECT
                first_name,
                last_name,
                email_address,
                pending
              FROM
                '.TABLE_MEMBER.'
              WHERE
                pending = "1"
                AND member_id = "'.$member_id.'"';
            $sql = mysql_query($query, $connection) or die("Couldn't execute query 3.");
            // If pending = 1 then we need to change it
            // This extra step is needed so we only set to *change* the value (Only send email on a newly "active" status)
            if ( $row = mysql_fetch_object ($sql) )
              {
                $query = '
                  UPDATE
                    '.TABLE_MEMBER.'
                  SET
                    pending = "0"
                  WHERE
                    member_id = "'.$member_id.'"';
                mysql_query($query, $connection) or die("Couldn't execute query 4.");

                // Now send the "Newly Activated" email notice
                $subject  = 'Account status: '.SITE_NAME;
                $email_to = preg_replace ('/SELF/', $row->email_address, MEMBER_FORM_EMAIL);
                $headers  = "From: ".MEMBERSHIP_EMAIL."\r\nReply-To: ".MEMBERSHIP_EMAIL."\r\n";
                $headers .= "Errors-To: ".GENERAL_EMAIL."\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/plain; charset=us-ascii\r\n";
                $headers .= "Message-ID: <".md5(uniqid(time()))."@".DOMAIN_NAME.">\r\n";
                $headers .= "X-Mailer: PHP ".phpversion()."\r\n";
                $headers .= "X-Priority: 3\r\n";
                $headers .= 'X-AntiAbuse: This is a user-submitted email through the '.SITE_NAME.' membership approval page.';
                $msg  = "Dear ".stripslashes($row->first_name)." ".stripslashes($row->last_name).",\n\n";
                $msg .= "Welcome to ".SITE_NAME.".  Your membership is now activated. ";
                $msg .= "Here is some information that may come in handy:\n\n";
                $msg .= "Beginning now, you may shop online using this link (during the regular ordering periods):\n";
                $msg .= BASE_URL.PATH."members/\n\n";
                $msg .= "Many of your questions can be answered at our web site: ".BASE_URL."\n";
                $msg .= "Producer help is available at: ".PRODUCER_CARE_EMAIL."\n";
                $msg .= "Other help is always available at: ".HELP_EMAIL."\n";
                $msg .= "Join in the fun, volunteer! ".VOLUNTEER_EMAIL."\n\n";
                $msg .= "If I can be of any help to you or you have any questions, please contact me. \n\n";
                $msg .= AUTHORIZED_PERSON."\n";
                $msg .= MEMBERSHIP_EMAIL;
                mail($email_to, $subject, $msg, $headers);
              }
          }
        if ($status == 'Remove')
          {
            $query = '
              UPDATE
                '.TABLE_MEMBER.'
              SET
                pending = "1",
                membership_discontinued = "1"
              WHERE
                member_id = "'.$member_id.'"';
            $sql = @mysql_query($query, $connection) or die("Couldn't execute query 5.");
          }
      }
  }


// Get the payment_method array
$query = '
  SELECT *
  FROM
    '.TABLE_PAY;
$sql = @mysql_query($query, $connection) or die("Couldn't execute query 6.");
$payment_method_array = array ();
while ( $row = mysql_fetch_object($sql) )
  {
    $payment_method_array[$row->payment_method] = $row->payment_desc;
  }


// This query will pull the members with a non-zero balance or who are pending
$query = '
  SELECT
    '.TABLE_MEMBER.'.member_id,
    '.TABLE_MEMBER.'.username_m,
    '.TABLE_MEMBER.'.first_name,
    '.TABLE_MEMBER.'.last_name,
    '.TABLE_MEMBER.'.first_name_2,
    '.TABLE_MEMBER.'.last_name_2,
    '.TABLE_MEMBER.'.business_name,
    '.TABLE_MEMBER.'.address_line1,
    '.TABLE_MEMBER.'.address_line2,
    '.TABLE_MEMBER.'.city,
    '.TABLE_MEMBER.'.state,
    '.TABLE_MEMBER.'.zip,
    '.TABLE_MEMBER.'.email_address,
    '.TABLE_MEMBER.'.email_address_2,
    '.TABLE_MEMBER.'.home_phone,
    '.TABLE_MEMBER.'.work_phone,
    '.TABLE_MEMBER.'.mobile_phone,
    '.TABLE_MEMBER.'.fax,
    '.TABLE_MEMBER.'.membership_date,
    '.TABLE_MEMBER.'.pending,
    '.TABLE_MEMBER.'.membership_discontinued,
    '.TABLE_MEMBERSHIP_TYPES.'.membership_class,
      (
        SELECT
          SUM('.TABLE_TRANSACTIONS.'.transaction_amount)
        FROM
          '.TABLE_TRANSACTIONS.'
        JOIN
          '.TABLE_TRANS_TYPES.' ON '.TABLE_TRANS_TYPES.'.ttype_id = '.TABLE_TRANSACTIONS.'.transaction_type
        WHERE
          ttype_parent = "40"
          AND '.TABLE_TRANSACTIONS.'.transaction_member_id = '.TABLE_MEMBER.'.member_id
      ) AS membership_dues
  FROM
    '.TABLE_MEMBER.'
  LEFT JOIN
    '.TABLE_MEMBERSHIP_TYPES.' ON '.TABLE_MEMBERSHIP_TYPES.'.membership_type_id = '.TABLE_MEMBER.'.membership_type_id
  WHERE
    '.TABLE_MEMBER.'.membership_discontinued != "1"
    AND
      (
        '.TABLE_MEMBER.'.pending != 0
        OR
          (
            SELECT
              SUM('.TABLE_TRANSACTIONS.'.transaction_amount)
            FROM
              '.TABLE_TRANSACTIONS.'
            JOIN
              '.TABLE_TRANS_TYPES.' ON '.TABLE_TRANS_TYPES.'.ttype_id = '.TABLE_TRANSACTIONS.'.transaction_type
            WHERE
              ttype_parent = "40"
              AND '.TABLE_TRANSACTIONS.'.transaction_member_id = '.TABLE_MEMBER.'.member_id
          ) != 0
      )
  ORDER BY
    '.TABLE_MEMBER.'.membership_date DESC';
$sql = @mysql_query($query,$connection) or die("Couldn't execute query 7.");
$num = mysql_numrows($sql);
$counter = 1;
$display = '<input type="hidden" name="counter" value="'.$num.'">';
while ( $row = mysql_fetch_array($sql) )
  {
    $member_id = $row['member_id'];
    $username_m = $row['username_m'];
    $first_name = stripslashes ($row['first_name']);
    $last_name = stripslashes ($row['last_name']);
    $first_name_2 = stripslashes ($row['first_name_2']);
    $last_name_2 = stripslashes ($row['last_name_2']);
    $business_name = stripslashes ($row['business_name']);
    $address_line1 = stripslashes ($row['address_line1']);
    $address_line2 = stripslashes ($row['address_line2']);
    $city = stripslashes ($row['city']);
    $state = stripslashes ($row['state']);
    $zip = $row['zip'];
    $email_address = $row['email_address'];
    $email_address_2 = $row['email_address_2'];
    $home_phone = $row['home_phone'];
    $work_phone = $row['work_phone'];
    $mobile_phone = $row['mobile_phone'];
    $fax = $row['fax'];
    $membership_date = $row['membership_date'];
    $membership_discontinued = $row['membership_discontinued'];
    $pending = $row['pending'];
    $membership_class = $row['membership_class'];
    $membership_dues = $row['membership_dues'];

    // Get the most recent delivery date for this member
    $query2 = '
      SELECT
        MAX('.TABLE_DELDATE.'.delivery_date) AS recent_delivery_date
      FROM
        '.TABLE_DELDATE.'
      JOIN
        '.TABLE_BASKET_ALL.' ON '.TABLE_DELDATE.'.delivery_id = '.TABLE_BASKET_ALL.'.delivery_id
      WHERE
        '.TABLE_BASKET_ALL.'.member_id = "'.$member_id.'"';
    $sql2 = @mysql_query($query2,$connection) or die("Couldn't execute query 8.");
    $row2 = mysql_fetch_array($sql2);
    $recent_delivery_date = $row2['recent_delivery_date'];
    if ($recent_delivery_date == '') $recent_delivery_date = "NEVER";

    include("../func/show_name.php");
    //This will change the background color on alternating lines

    if ( $pending == 1 )
      {
        $bg = '#ddddff';
      }
    else
      {
        if ( $membership_dues < 0 )
          {
            $bg = '#ddffdd';
          }
        else
          {
            $bg = '#ffdddd';
          }
      }
    //This will insert a header row every 8 rows
    if  ( ! fmod($counter, 6) )
      {
        $display .= '<tr align="center" bgcolor="#ffff88">';
        $display .= '<th>Status</th>';
        $display .= '<th>Amt. Due</th>';
        $display .= '<th colspan="3">Contact Information</th></tr>';
      }
    //The member is either not yet active or active and owes dues
    //not yet approved
        $checked['pending'] = '';
        $checked['approved'] = '';
        if ( $pending == 0 )
          {
            $checked['approved'] = ' checked';
          }
        else
          {
            $checked['pending'] = ' checked';
          }
        $display .= '
          <tr bgcolor = "'.$bg.'">
            <td>
              <strong>Member #'.$member_id.'</strong> ['.$username_m.']<br /><br />
              Applied on: '.$membership_date.'<br />'.$membership_class.'<br />
              Last ordered: '.$recent_delivery_date.'<br /><br />
              <input type="radio" name="status'.$counter.'" value="Pending"'.$checked["pending"].'>Pending</input><br />
              <input name ="status'.$counter.'" type="radio" value="Approved"'.$checked["approved"].'>Approved</input><br />
              <input name ="status'.$counter.'" type="radio" value="Remove">Discontinue membership
            </td>';
    //memberships are either paid or unpaid
    //not yet paid
    $display_dues = '';
    if ( $membership_dues < 0) $display_dues = '<span class="overpaid">$ '.number_format (-$membership_dues, 2).' (credit)</span>';
    if ($membership_dues > 0) $display_dues = '<span class="due">$ '.number_format ($membership_dues, 2).' due</span>';
    $payment_display = '';
    foreach (array_keys ($payment_method_array) as $payment_method)
      {
        $payment_display .= '
          <input name="payment_method'.$counter.'" type="radio" value="'.$payment_method.'">'.$payment_method_array[$payment_method].'<br />';
      }
    $display .= '
      <td>'.$display_dues.'<br /><br />'.$payment_display.'<br />
      Paid: $ <input type="text" name ="payment_amount'.$counter.'" size="6" maxlength="8"><br /><br />
        Comments:<br><input name="comments'.$counter.'" type="text" size="20" value=""/>
        <input type="hidden" name="member_id'.$counter.'" value="'.$member_id.'"></td>
      <td colspan="3">'.$show_name;
    //address
    $display .= '<br />'.$address_line1;
    if ( $address_line2 )
      {
        $display .= '<br />'.$address_line2;
      }
    $display .= '<br />'.$city.', '.$state.' '.$zip.'';
    //phone
    if ( $home_phone )
      {
        $display .= '<br />'.$home_phone.' (home)';
      }
    if ( $work_phone )
      {
        $display .= '<br />'.$work_phone.' (work)';
      }
    if ( $mobile_phone )
      {
        $display .= '<br />'.$mobile_phone.' (cell)';
      }
    if ( $fax )
      {
        $display .= '<br />'.$fax.' (fax)';
      }
    //email
    if ( $email_address )
      {
        $display .= '<br /><a href="mailto:'.$email_address.'">'.$email_address.'</a>';
      }
    if ( $email_address_2 )
      {
        $display .= '<br /><a href="mailto:'.$email_address_2.'">'.$email_address_2.'</a>';
      }
    $display .= '
        </td>
      </tr>';
    $counter = $counter + 1;
  }


?>
<?php include ("template_hdr.php");?>
<!-- CONTENT BEGINS HERE -->
<style>
table {
  font-size:90%;
  }
table tr td {
  padding:0.5em;
  vertical-align:top;
  }
.due {
  color:#a00;
  }
.overpaid {
  color:#060;
  }
.error {
  color:#a00;
  font-weight:bold;
  }
</style>
<div align="center">
<table width="100%">
  <tr>
    <td align="left">
      <div align="center" style="margin-bottom:8px;">
        <h3><?php echo $num;?> Pending or Unpaid Members</h3><h5>Listed Newest First</h5>
        <span style="padding:2px;background-color:#ddddff;border:1px solid #aaa;">BLUE: Pending</span> &nbsp; <span style="padding:2px;background-color:#ddffdd;border:1px solid #aaa;">GREEN: Credit</span> &nbsp; <span style="padding:2px;background-color:#ffdddd;border:1px solid #aaa;">RED: Debit</span>
      </div>
      <?php echo $error_message; ?>
      <form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      <table border="3" align="center" width="90%">
        <tr align="center" style="background-color:#ffff88;">
          <th>Status</th>
          <th>Amt. Due</th>
          <th colspan="3">Contact Information</th>
        </tr>
        <?php echo $display;?>
      </table>
      <div align="center"><br />
        <input type="submit" name="action" value="Submit" />
      </div>
      </form>
    </td>
  </tr>
</table>
</div>
<!-- CONTENT ENDS HERE -->
<?php include ("template_footer.php");?>
