<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();

$show_form = 'yes';

// If needing to login, keep track of the page that was initially requested and redirect to it
$success_redirect = 'Location: index.php';
if ($_GET['call'])
  {
    $redirect_call = '?call='.$_GET['call'];
    $success_redirect = 'Location: '.$_GET['call'];
  }

if ( $_POST['op'] == "ds" && $_POST['username_c'] && $_POST['password'] )
  {
    $query = '
      SELECT
        username_c
      FROM
        '.TABLE_AUTH.'
      WHERE
        username_c = "'.mysql_real_escape_string($_POST["username_c"]).'"
        AND password = md5("'.mysql_real_escape_string($_POST["password"]).'")';
    $sql = mysql_query($query);
    if ( mysql_numrows($sql) != 0 )
      {
        $row = mysql_fetch_array($sql);
        $_SESSION["username_c"] = $row['username_c'];
        $_SESSION["valid_c"] = $row['username_c'];
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
    <tr>
      <td><b>Username</b>:</td>
      <td><input type="text" name="username_c" size="17" maxlength="20"></td>
    </tr>
    <tr>
      <td><b>Password</b>:</td>
      <td><input type="password" name="password" size="17" maxlength="25"></td>
    </tr>
    <tr>
      <td colspan="2" align="right"><input type="hidden" name="op" value="ds"><input type="submit" name="submit" value="Login"></td>
    </tr>
  </table>
  </form>';
if ( $show_form == 'yes' )
  {
    $display_block = $form_block;
  }
include("template_hdr.php");?>
<div align="center">
<table cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td align="center">
      <table width="100%" cellpadding="10" cellspacing="2" border="1" bordercolor="#000000">
        <tr>
          <td bgcolor="#DDDDDD" align="center" colspan="2"><font size="3"><b>Welcome to the <?php echo SITE_NAME; ?></b></font></td>
        </tr>
        <tr>
          <td bgcolor="#DDDDDD" align="center"><b>A<br>D<br>M<br>I<br>N<br></b></td>
          <td valign="center" align="center"><br><?php echo $display_block; ?></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr bgcolor="#000000">
    <td align="center"><img src="../grfx/shop-welcome.jpg" width="373" height="90" border="1" alt="Welcome"><br></td>
  </tr>
</table>
</div>
<script language="javascript">
  document.login.username_c.focus();
</script>
</body>
</html>