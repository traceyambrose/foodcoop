<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo SITE_NAME;?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Language" content="en-us">
<?php if (FAVICON != '') { echo '<link rel="shortcut icon" href="'.FAVICON.'" type="image/x-icon">';} ?>
<style type="text/css">
body, p, td {
  font-family: Verdana;
  font-size: 9pt;
  color: #000000;
  padding-right: 5px;
  }

body {
  margin-left:0;
  margin-top:0;
  }

h1 {
  text-align:center;
  }

em {
  color: #758954;
  }
th.memberform {
  background:#cca;
  }

dl {
  color:#230;
  font-weight:normal;
  }

dt {
  text-decoration:underline;
  font-style:italic;
  margin-top:0.5em;
  font-size:1.1em;
  }

dd {
  font-size:0.9em;
  margin-top: 0.3em;
  }

td.form_key strong {
  color:#230;
  font-weight:bold;
  }

.error_message, .error_list {
  font-weight:normal;
  font-style:italic;
  font-size: 1.1em;
  color: #a22;
  }

</style>

</head>

<body bgcolor="#FFFFFF">
<table cellpadding="0" cellspacing="0" border="0" style="width:100%;border-bottom:1px solid #000000;background-color:#FFFFFF;">
  <tr>
    <td align="center">
      <div style="width:500px;">
        <?php if (SHOW_HEADER_LOGO === true) { ?>
        <a href="<?php echo BASE_URL.PATH;?>">
        <img src="<?php echo PATH;?>grfx/logo.jpg" border="0" alt="Food <?php echo ORGANIZATION_TYPE; ?>" align="middle"></a>
        <br>
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
