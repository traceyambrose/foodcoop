<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();

$old_user = $valid_m;

$result_unreg = session_unregister("valid_m");
$result_dest = session_destroy();

if ( ! empty($old_user) )
  {
    if ( $result_unreg && $result_dest )
      {
        $result =  "You are now logged out. <br><br><a href=\"orders_login.php\">Click here to return to log in again.</a>";
      }
    else
      {
        $result =  "Could not log you out.";
      }
  }
else
  {
    $result = "You were not logged in, and so have not been logged out.";
  }

?>

<?php include("template_hdr_orders.php");?>
<div align="center">
  <h3>Thank you.</h3>

  <?php echo "$result"; ?><br><br>
</div>

<?php include("template_footer_orders_notloggedin.php");?>
</body>
</html>