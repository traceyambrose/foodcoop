<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();

$old_user = $valid_c;
$result_unreg = session_unregister("valid_c");
$result_dest = session_destroy();
if ( !empty($old_user) )
  {
    if ( $result_unreg && $result_dest )
      {
        $result =  'You are now logged out. <br><br><a href="show_login.php">Click here to return to log in again.</a>';
      }
    else
      {
        $result =  'Could not log you out.';
      }
  }
else
  {
    $result = 'You were not logged in, and so have not been logged out.';
  }

include("template_hdr.php");
?>
<div align="center">
  <h3>Admin</h3>
  <?php  echo "$result"; ?>
</div>
<?php include("template_footer.php");?>