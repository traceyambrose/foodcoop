<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();

$message = '';

// Rather than use the check_valid_user function, we need to trap the result
if ( ! $_SESSION['valid_m'] )
  // The user is not valid, so provide a form to reset and send a new password by email
  {
    if ( $_POST['form_data'] == 'true' )
      // Validate the information and take appropriate action
      {
        $username_m = $_POST['username_m'];
        $email_address = mysql_real_escape_string($_POST['email_address']);
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $full_name = $_POST['first_name'].' '.$_POST['last_name'];
        // Check consistency between username_m and email_address
        $query_check = '
          SELECT
            username_m,
            email_address,
            first_name,
            last_name,
            first_name_2,
            last_name_2
          FROM
            '.TABLE_MEMBER.'
          WHERE
            username_m = "'.mysql_real_escape_string($username_m).'"
            OR email_address = "'.mysql_real_escape_string($email_address).'"
            OR
              (
                first_name = "'.mysql_real_escape_string($first_name).'"
                AND last_name = "'.mysql_real_escape_string($last_name).'"
              )
            OR
              (
                first_name_2 = "'.mysql_real_escape_string($first_name).'"
                AND last_name_2 = "'.mysql_real_escape_string($last_name).'"
              )';
        $result = @mysql_query($query_check, $connection) or die(mysql_error());
        $valid_info = false;
        while ( $row = mysql_fetch_array($result) )
          {
            $row['full_name'] = $row['first_name'].' '.$row['last_name'];
            $row['full_name_2'] = $row['first_name_2'].' '.$row['last_name_2'];
            if ($row['username_m'] == $username_m && $row['email_address'] == $email_address)
              {
                $valid_info = true;
                $valid_email = $row['email_address'];
                $valid_username = $row['username_m'];
              }
            if ($row['username_m'] == $username_m && ($row['full_name'] == $full_name || $row['full_name_2'] == $full_name))
              {
                $valid_info = true;
                $valid_email = $row['email_address'];
                $valid_username = $row['username_m'];
              }
            if ($row['email_address'] == $email_address && ($row['full_name'] == $full_name || $row['full_name_2'] == $full_name))
              {
                $valid_info = true;
                $valid_email = $row['email_address'];
                $valid_username = $row['username_m'];
              }
          }
        if ( $valid_info == true )
        // Everything looks good, send the new password to the validated email address.
          {
            // Generate new password
            $chars = "ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789";
            $password = '' ;
            while (strlen ($password) <= rand(5,8))
              {
                $password .= substr($chars, rand(0,57), 1);
              }
            $query_update = '
              UPDATE
                '.TABLE_MEMBER.'
                SET
                  password = MD5("'.mysql_real_escape_string($password).'")
                WHERE
                  email_address = "'.mysql_real_escape_string($valid_email).'"';
            $result = mysql_query($query_update, $connection) or die(mysql_errno());
            $message =
              'Account security notice:

                The password for an account registered with this email address
                has been reset from the website at '.DOMAIN_NAME.'
                Username: '.$valid_username.'
                The new password is: '.$password;
            mail ( $valid_email, 'Updated account info for '.DOMAIN_NAME, $message, "from: ".MEMBERSHIP_EMAIL);
            header( 'refresh: 7; url=../index.php' );
            include("template_hdr_orders.php");
            echo
              '<table width="50%" align="center" cellspacing="5">
                <tr>
                  <td><p style="font-size:1.2em">An email has been sent to the validated address.
                    If you do not receive it, contact '.MEMBERSHIP_EMAIL.'</p>
                  <p style="font-size:1.2em">In a few seconds, you will be redirected to the main page.</p></td>
                </tr>
              </table>';
            include("template_footer_orders_notloggedin.php");
            exit;
          }
        else
          // Information did not validate, so return to the form
          {
          $_POST['form_data'] = 'false';
          $message = '<p style="font-size:1.2em;color:#700;">Sorry... the information you submitted did not validate.</p>';
          }
      }
    if ( $_POST['form_data'] != 'true' )
      // Form data was not posted or was invalid, so show the form for input
      {
        include("template_hdr_orders.php");
        echo
          '<form method="post" action="'.$_SERVER['PHP_SELF'].'" name="change_password">
          <table width="50%" align="center" cellspacing="5">
            <tr>
              <td colspan="3">'.$message.'<p style="color:#462">In order to reset your password, you must correctly
                enter two of the three pieces of information below.  Then a new password will be
                e-mailed to you.</p><p style="color:#462">For security purposes, you will not be told which information
                is incorrect.</p></td>
            </tr>
            <tr>
              <td align="right" style="padding-bottom:1em;"><b>Username</b>:</td>
              <td align="left" colspan="2" style="padding-bottom:1em;"><input type="input" name="username_m" size="25" maxlength="20"></td>
            </tr>
            <tr>
              <td align="right" style="padding-bottom:1em;"><b>Email Address</b>:</td>
              <td align="left" colspan="2" style="padding-bottom:1em;"><input type="text" name="email_address" size="25" maxlength="50"></td>
            </tr>
            <tr>
              <td align="right" rowspan="2" style="padding-bottom:1em;"><b>Full Name</b>:</td>
              <td align="left" width="10"><input type="input" name="first_name" size="25" maxlength="25" value=" F I R S T" onClick="javascript:this.focus();this.select();"></td>
              <td valign="middle" rowspan="2" align="left" style="padding-bottom:1em;"> Both required</td>
            </tr>
            <tr>
              <td align="left" width="10" style="padding-bottom:1em;"><input type="input" name="last_name" size="25" maxlength="25" value=" L A S T" onClick="javascript:this.focus();this.select();"></td>
           </tr>
            <tr>
              <td colspan="3" align="center"><input type="hidden" name="form_data" value="true">
                <input type="submit" name="submit" value="Send New Password"></td>
            </tr>
          </table>
          </form>';
        include("template_footer_orders_notloggedin.php");
      }
  }
else
  // The user is already logged in, so provide a form to change the password
  {
    if ( $_POST['form_data'] == 'true' )
      // Validate the password information and take appropriate action
      {
        $username_m = $_SESSION['username_m'];
        $old_password = $_POST['old_password'];
        $new_password1 = $_POST['new_password1'];
        $new_password2 = $_POST['new_password2'];
        // Make sure everything is filled in
        if($_SESSION['username_m'] && $old_password && $new_password1 && $new_password2)
          {
            // Check that the new passwords match
            if ( $new_password1 != $new_password2 )
              {
                $message .= '<p style="font-size:1.2em;color:#700;">New passwords do not match.</p>';
              }
            // Check that the old password is correct
            $query_pw = '
              SELECT
                "true" AS valid_password
              FROM
                '.TABLE_MEMBER.'
              WHERE
                username_m = "'.mysql_real_escape_string($username_m).'"
                AND password = MD5("'.mysql_real_escape_string($old_password).'")';
            $result = @mysql_query($query_pw, $connection) or die(mysql_error());
            $row = mysql_fetch_array($result);
            if ( $row['valid_password'] != 'true' )
              {
                $message .= '<p style="font-size:1.2em;color:#700;">Incorrect old password was provided.</p>';
              }
            if ($message == '')
              // Everything looks good, so go ahead and update the password
              {
                $query_update = '
                  UPDATE
                    '.TABLE_MEMBER.'
                  SET
                    password = MD5("'.mysql_real_escape_string($new_password1).'")
                  WHERE
                    username_m = "'.mysql_real_escape_string($username_m).'"';
                $result = mysql_query($query_update, $connection) or die(mysql_errno());

                header( 'refresh: 7; url=index.php' );
                include("template_hdr_orders.php");
                echo
                '<table width="50%" align="center" cellspacing="5">
                  <tr>
                    <td><p style="font-size:1.2em">Your password has been updated. </p>
                    <p style="font-size:1.2em">In a few seconds, you will be redirected to the login page.</p></td>
                  </tr>
                </table>';
                include("template_footer_orders_notloggedin.php");
                exit;
              }
            else
              // There was an error, so return to the form
              {
                $_POST['form_data'] = 'false';
              }
          }
        else
          {
            $_POST['form_data'] = 'false';
          }
      }
    if ( $_POST['form_data'] != 'true' )
      // Form data was not posted or was invalid, so show the form for input
      {
        include("template_hdr_orders.php");
        echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'" name="change_password">';
        echo '
          <table width="50%" align="center" cellspacing="5">
            <tr>
              <td colspan="2">';
        if ($message)
          {
            echo $message.'<p style="font-size:1.2em;color:#700;">Please re-enter your information.</p>';
          }
        else
          {
            echo '<p style="font-size:1.2em">In order to change your password, please enter your old password and
              enter your new password twice for confirmation.</p>';
          }
        echo '
              </td>
            </tr>
            <tr>
              <td align="right"><b>Old Password</b>:</td>
              <td align="left"><input type="password" name="old_password" size="17" maxlength="20"></td>
            </tr>
            <tr>
              <td align="right"><b>New Password</b>:</td>
              <td align="left"><input type="password" name="new_password1" size="17" maxlength="25"></td>
            </tr>
            <tr>
              <td align="right"><b>New Password (confirm)</b>:</td>
              <td align="left"><input type="password" name="new_password2" size="17" maxlength="25"></td>
            </tr>
            <tr>
              <td colspan="2" align="right"><input type="hidden" name="form_data" value="true">
                <input type="submit" name="submit" value="Update"></td>
            </tr>
          </table>
          </form>';
        include("template_footer_orders.php");
      }
  }
?>