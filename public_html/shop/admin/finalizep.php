<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

if ( $_REQUEST['delivery_id'] )
  {
    $delivery_id = preg_replace("/[^0-9]/","",$_REQUEST['delivery_id']);
  }
else
  {
    $delivery_id = $_POST['delivery_id'];
  }
include("template_hdr.php");
if ( $_POST && strlen($_POST['producer_id']) == 5 )
  {
    //print_r($_POST);
    include("producer_finalize.php");
    producer_finalize::finalizeAll($_POST['delivery_id'],$_POST['producer_id']);
    $message = "<H3>The information has been saved.</h3>";
  }
if ( $message )
  {
    echo '<div align="center">'.$message.'</div>';
  }
?>

<div align="center">
  <form action="<?php  echo $_SERVER['PHP_SELF'];?>" method="POST">
    <input type="hidden" name="delivery_id" value="<?php  echo $delivery_id;?>">
    <input type="hidden" name="set" value="1">
    Producer ID: <input type="text" name="producer_id" maxlength="5">
    <input type="submit" name="submit" value="Finalize producer invoice">
  </form>
</div>
<br/><br/>
<?php include("template_footer.php"); ?>
</body>
</html>
