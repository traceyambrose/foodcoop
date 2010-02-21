<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title><?php echo SITE_NAME;?>  - Shop</title>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
	<meta http-equiv="Content-Language" content="en-us">
	<?php if (FAVICON != '') { echo '<link rel="shortcut icon" href="'.FAVICON.'" type="image/x-icon" />';} ?>
	<style>
	body, p, td {
	  font-family: Verdana;
	  font-size: 9pt;
	  color: #000000;
	  padding-right: 5px;
	  }
	em {
	  color: #758954;
	  }
	table.proddata {
	  font-size: 1.2em;
	  border: 1px solid black;
	  empty-cells:show;
	  }
	tr.d0 td {
	  background-color: #eeeeee;
	  color: black;
	  border-top:1px solid black;
	  }
	tr.d00 td {
	  background-color: #eeeeee;
	  color: black;
	  }
	tr.d1 td {
	  background-color: #f8f8f8;
	  color: black;
	  border-top:1px solid black;
	  }
	td.b {
	  border-left: 2px solid #dddddd;
	  border-right: 2px solid #dddddd;
	  }
	td.memform {
	  border: 1px solid #ccc;
	  }
	</style>
	<link href="<?php echo BASE_URL.PATH;?>css/template.css" rel="stylesheet" type="text/css" media="screen" />
	<link href="<?php echo BASE_URL.PATH;?>css/style_1.css" rel="stylesheet" type="text/css" media="screen" />
</head>

<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table cellpadding="0" cellspacing="0" border="0" style="width:100%;border-bottom:1px solid #000000;background-color:#FFFFFF;">
  <tr>
    <td align="center">
      <div id="header-wrap">
        <?php if (SHOW_HEADER_LOGO === true) { ?>
        <a href="<?php echo BASE_URL.PATH;?>">
        <img src="<?php echo DIR_GRAPHICS; ?>logo.jpg" border="0" alt="Food <?php echo ORGANIZATION_TYPE; ?>" align="center"></a>
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
