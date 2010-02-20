<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

if($_POST['export'])
  {
    header("Content-type: application/octet-stream"); 
    header("Content-Disposition: attachment; filename=foodcoop-".date('Y-m-d').".csv"); 
    header("Pragma: no-cache"); 
    header("Expires: 0"); 
    print $_POST['export'];
    exit;  
  }


?>