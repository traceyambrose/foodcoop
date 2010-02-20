<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();

$show_form = "yes";

// If needing to login, keep track of the page that was initially requested and redirect to it
$success_redirect = 'Location: index.php';
if ($_GET['call'])
  {
    $redirect_call = '?call='.$_GET['call'];
    $success_redirect = 'Location: '.$_GET['call'];
  }

if ( $_POST['gp'] == "ds" && $_POST['username_m'] && $_POST['password'] )
  {
    $query = '
      SELECT
        auth_type,
        username_m
      FROM
        '.TABLE_MEMBER.'
      WHERE
        username_m = "'.mysql_real_escape_string($_POST['username_m']).'"
        AND pending = "0"
        AND
          (password = md5("'.mysql_real_escape_string($_POST['password']).'")
          OR "'.MD5_MASTER_PASSWORD.'" = md5("'.mysql_real_escape_string($_POST['password']).'"))';
    $sql = mysql_query($query);

    if ( mysql_numrows ($sql) != 0)
      {
        $row = mysql_fetch_array($sql);
        $_SESSION["username_m"] = $row['username_m'];
        $_SESSION["valid_m"] = $row['username_m'];
        $_SESSION["auth_type"] = $row['auth_type'];

        header($success_redirect);
        exit;
      }
    else
      {
        $msg = "Login incorrect. Please re-enter your login information.";
      }
  }

$form_block = '
  <form method="post" action="'.$_SERVER['PHP_SELF'].$redirect_call.'" name="login">
    '.$msg.'

    <table>
        <tr><td>'.$font.'<b>Username</b>:</td><td>
        <input type="text" name="username_m" size="17" maxlength="20">
        </td></tr>

        <tr><td>'.$font.'<b>Password</b>:</td><td>
        <input type="password" name="password" size="17" maxlength="25">
         </td></tr>

      <tr><td colspan="2" align="right">
        <input type="hidden" name="gp" value="ds">
        <input type="submit" name="submit" value="Login">
        </td></tr>
    </table>
  </form>

  <div style="text-align:left;font-size:11px;">
    <a href="reset_password.php">Forgot your password?</a>
  </div>
  ';

if ( $show_form == "yes" )
  {
    $display_block = $form_block;
  }

include("template_hdr_orders.php");?>

<div align="center">
  <table cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td align="center">
        <table width="100%" cellpadding="10" cellspacing="2" border="1" bordercolor="#000000">
          <tr>
            <td bgcolor="#DDDDDD" align="center" colspan="2">
              <?php echo $font ?>
              <font size="3"><b>Welcome to the <?php  echo SITE_NAME; ?></b></font>
            </td>
          </tr>
          <tr>
            <td bgcolor="#DDDDDD" align="center">
              <?php echo $font ?>
              <b>M<br>E<br>M<br>B<br>E<br>R<br>S<br></b>
            </td>
            <td valign="center" align="center">
              <br>

              <?php  echo $display_block; ?>

            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr bgcolor="#000000">
      <td align="center">
        <img src="../grfx/shop-welcome.jpg" width="373" height="90" border="1" alt="Welcome"><br>
      </td>
    </tr>
  </table>
  <br><br>

  <table width=475>
    <tr>
      <td align=left>
        <?php echo $font ?>
        If you are member and have lost your user name and password, send an e-mail to <a href="mailto:<?php echo MEMBERSHIP_EMAIL;?>"><?php echo MEMBERSHIP_EMAIL;?></a>.
        If you have your user name and password, but are having difficulty logging in, make sure cookies are enabled on your internet browser.  If you need assistance with how to do this, or are still unable to log in, please send an e-mail to <a href="mailto:<?php echo HELP_EMAIL;?>"><?php echo HELP_EMAIL;?></a>.
      </td>
    </tr>
  </table>
</div>

<?php include("template_footer_orders_notloggedin.php");?>

<script language="javascript">
  document.login.username_m.focus();
</script>
