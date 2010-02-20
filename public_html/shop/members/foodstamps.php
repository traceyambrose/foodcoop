<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();
$date_today = date("F j, Y");

// JPF: register_globals $fs

if ( $update == "yes" && $product_id )
  {
    $sqlu = '
      UPDATE
        '.TABLE_PRODUCT_PREP.'
      SET
        retail_staple = "'.$retail_staple.'",
        staple_type = "'.$staple_type.'"
      WHERE
        product_id = "'.$product_id.'"';
      $result = @mysql_query($sqlu,$connection) or die("<br><br>You found a bug. If there is an error listed below, please copy and paste the error into an email to <a href=\"mailto:".WEBMASTER_EMAIL."\">".WEBMASTER_EMAIL."</a><br><br><b>Error:</b> Updating " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
  }

$sql = '
  SELECT
    '.TABLE_PRODUCT_PREP.'.product_id,
    '.TABLE_PRODUCT_PREP.'.product_name,
    '.TABLE_PRODUCT_PREP.'.retail_staple,
    '.TABLE_PRODUCT_PREP.'.staple_type,
    '.TABLE_PRODUCT_PREP.'.subcategory_id,
    '.TABLE_SUBCATEGORY.'.subcategory_id,
    '.TABLE_SUBCATEGORY.'.subcategory_name
  FROM
    '.TABLE_PRODUCT_PREP.',
    '.TABLE_SUBCATEGORY.'
  WHERE
    '.TABLE_PRODUCT_PREP.'.subcategory_id = '.TABLE_SUBCATEGORY.'.subcategory_id
    AND '.TABLE_PRODUCT_PREP.'.retail_staple = "'.$fs.'"
  GROUP BY
    product_id
  ORDER BY
    retail_staple DESC,
    subcategory_name ASC,
    product_name ASC';
$result = @mysql_query($sql,$connection) or die("".mysql_error()."");

$num = mysql_numrows($result);

while ( $row = mysql_fetch_array($result) )
  {
    $subcategory_name = $row['subcategory_name'];
    $product_id = $row['product_id'];
    $product_name = $row['product_name'];
    $retail_staple = $row['retail_staple'];
    $staple_type = $row['staple_type'];

    if ( $retail_staple == "1" )
      {
        $chkf1 = "checked";
        $chkf2 = "";
        $chkf3 = "";
      }
    elseif ( $retail_staple == "2" )
      {
        $chkf1 = "";
        $chkf2 = "checked";
        $chkf3 = "";
      }
    elseif ( $retail_staple == "3" )
      {
        $chkf1 = "";
        $chkf2 = "";
        $chkf3 = "checked";
      }
    else
      {
        $chkf1 = "";
        $chkf2 = "";
        $chkf3 = "";
      }


    $display .= "<tr>";
    $display .= "<td>$font <a name=\"$product_id\"><b>#$product_id</b></td>";

    $display .= "<td>$font <b>$subcategory_name: ".stripslashes($product_name)."</b><br>";
    $display .= "</td>";

    $display .= "<td>$font
    <form action=\"$PHP_SELF\" method=\"post\">
    <input type=\"radio\" name=\"retail_staple\" value=\"2\" $chkf2>RFnoS
    <input type=\"radio\" name=\"retail_staple\" value=\"3\" $chkf3>Staple
    <input type=\"radio\" name=\"retail_staple\" value=\"1\" $chkf1>NF ";

    if ( $retail_staple == "3" )
      {
        $display .= "<select name=staple_type>
          <option value='$staple_type'>$staple_type</option>
          <option value=''>Select Type</option>
          <option value='m'>(m) Meat</option>
          <option value='p'>(p) Produce</option>
          <option value='e'>(e) Eggs</option>
          <option value=''>none</option>
          </select> <b>$staple_type</b>
          ";
      }

    $display .= "</td>";

    $display .= "<td>
      <input type=\"hidden\" name=\"product_id\" value=\"$product_id\">
      <input type=\"hidden\" name=\"update\" value=\"yes\">
      <input type=\"hidden\" name=\"fs\" value=\"$fs\">
      <input name=\"where\" type=\"submit\" value=\"Update\">
      </form></td></tr>";
  }

?>

<?php include("template_hdr_orders.php");?>


  <!-- CONTENT BEGINS HERE -->

<div align="center">
<table width="680">
  <tr><td align="left">

<h3>Food Stamp Designations</h3>

<?php echo $num;?> Entries Found
<br><br>
Go to these pages:
<a href="<?php echo$PHP_SELF;?>?fs=3">Staples</a> |
<a href="<?php echo$PHP_SELF;?>?fs=2">Retail Food but not Staples</a> |
<a href="<?php echo$PHP_SELF;?>?fs=1">Non-food</a> |
<a href="<?php echo$PHP_SELF;?>?fs=0">Unassigned</a>
<br>

<?php
echo "<div align=\"right\"><font size=\"-1\">
    [ <a href=\"index.php\">Return to main page</a> |
    <a href=\"logout.php\">Logout</a> ]</font></div>";
?>

<table border="1" cellpadding="5" cellspacing="0">
  <tr>
    <th align="center" bgcolor="#770000"><?php echo $font;?><font color="#FFFFFF">ID</font></th>
    <th align="center" bgcolor="#770000" width="200"><?php echo $font;?><font color="#FFFFFF">Product Name</font></th>
    <th align="center" bgcolor="#770000"><?php echo $font;?><font color="#FFFFFF">Foodstamps</font></th>
    <th align="center" bgcolor="#770000"><?php echo $font;?><font color="#FFFFFF">Update</font></th>
  </tr>

  <?php echo $display;?>

</table>


  </td></tr>
</table>
</div>
  <!-- CONTENT ENDS HERE -->

<?php include("template_footer_orders.php");?>