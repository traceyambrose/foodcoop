<?php
require_once ("config_foodcoop.php");

if ( strpos($_SERVER['PHP_SELF'], "index") === false )
  {
    $fontface = 'arial';
    $fontsize = '-1';
    $font = '<font face"arial" size="-1">';
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo SITE_NAME; ?> - Shop</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta http-equiv="Content-Language" content="en-us">
<?php if (FAVICON != '') { echo '<link rel="shortcut icon" href="'.FAVICON.'" type="image/x-icon" />';} 
if ( strpos($_SERVER['PHP_SELF'], "index") !== false )
  {
    echo '
      <link href="libraries/stylesheet.css" rel="stylesheet" type="text/css">
      <link href="libraries/pngHack.css" rel="stylesheet" type="text/css">';
  }
?>
</head>

<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table cellpadding="0" cellspacing="0" border="0" style="width:100%;border-bottom:1px solid #000000;background-color:#FFFFFF;">
  <tr>
    <td align="center">
      <div style="width:500px;">
        <?php if (SHOW_HEADER_LOGO === true) { ?>
        <a href="<?php echo BASE_URL.PATH;?>">
        <img src="<?php echo PATH;?>grfx/logo.jpg" border="0" alt="Food <?php echo ORGANIZATION_TYPE; ?>" align="center"></a>
        <br />
        <?php }
        if (SHOW_HEADER_SITENAME === true) { ?>
        <h2><?php echo SITE_NAME;?></h2>
        <?php } ?>
      </div>
    </td>
  </tr>
</table>
<br>
<?php echo $_GLOBALS['font'] ?>
