<?php

// store.php3 - by Florian Dittmer <dittmer@gmx.net>
// Example php script to demonstrate the storing of binary files into
// an sql database. More information can be found at http://www.phpbuilder.com/

$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();
include("template_hdr_orders.php");

// Check if auth_type = administrator and there is a producer_id provided
if(strpos ($_SESSION['auth_type'], 'administrator') !== false && $_GET['producer_id'])
  {
    // Keep the same producer_id value
    $producer_id = $_GET['producer_id'];
  }
elseif ($_SESSION['producer_id_you'])
  {
    $producer_id = $_SESSION['producer_id_you'];
  }

$sqll = '
  SELECT
    '.TABLE_PRODUCER_LOGOS.'.logo_id
  FROM
    '.TABLE_PRODUCER_LOGOS.'
  WHERE
    '.TABLE_PRODUCER_LOGOS.'.producer_id = "'.$producer_id.'"';
$rsrl = @mysql_query($sqll,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
$num = mysql_numrows($rsrl);
while ($row = mysql_fetch_array($rsrl))
  {
    $logo_id = $row['logo_id'];
  }
if ( $logo_id )
  {
    $display_logo = "
      <img src=\"getlogos.php?logo_id=$logo_id\" width=\"150\" hspace=\"5\" alt=\"$logo_desc\">";
  }
else
  {
    $display_logo = "No logo uploaded";
  }

// code that will be executed if the form has been submitted:

if ( $submit )
  {

    // connect to the database
    // (you may have to adjust the hostname,username or password)

    $data = addslashes(fread(fopen($form_data, "r"), filesize($form_data)));


    if ( $logo_id )
      {
        $sql = '
          UPDATE
            '.TABLE_PRODUCER_LOGOS.'
          SET
            logo_desc = "'.$form_description.'",
            bin_data = "'.$data.'",
            filename = "'.$form_data_name.'",
            filesize = "'.$form_data_size.'",
            filetype = "'.$form_data_type.'"
         WHERE
          producer_id = "'.$producer_id.'"';
        $result = mysql_query($sql, $connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());

        $display_results .= "<div align=\"center\">";
        $display_results .= "<font color=#3333FF><b>Your logo has been updated</b></font><br><br>";
        $display_results .= $display_logo;
        $display_results .= "<br>Click here to <a href=\"edit_producer_info.php?producer_id=$producer_id\">return to your editing page</a>.";
        $display_results .= "</div>";
        echo $display_results;
      }
    else
      {

        $query = '
          INSERT INTO
            '.TABLE_PRODUCER_LOGOS.'
            (
              logo_desc,
              producer_id,
              bin_data,
              filename,
              filesize,
              filetype
            )
          VALUES
            (
              "'.$form_description.'",
              "'.$producer_id.'",
              "'.$data.'",
              "'.$form_data_name.'",
              "'.$form_data_size.'",
              "'.$form_data_type.'"
            )';
        $result=mysql_query($query,$connection);

        $logo_id= mysql_insert_id();
        $display_results .= "<div align=\"center\">";
        $display_results .= "<font color=#3333FF><b>Your logo has been uploaded.</b></font><br><br>";
        $display_results .= "<br>Click here to <a href=\"edit_producer_info.php?producer_id=$producer_id\">return to your editing page to view the logo</a>.";
        $display_results .= "</div>";
        echo $display_results;

        mysql_close();
      }

  } else {

    // else show the form to submit new data:
?>
<div align="center">
<table width="70%">
  <tr>
    <td align="left">
      <h3><?php echo SITE_NAME; ?>: Upload Images</h3>

      <table cellpadding="3" border="0">
        <tr>
          <td valign="top">
            <b>Current Logo:</b><br>
            Use the form below to upload a new logo or replace an old logo.<br><br>
            <font size="-2">(All logos are displayed at a width of 150 pixels.  For best results, you should scale the image before uploading it.)
            Images must be no larger than 100Kb and may be .jpg, .gif, .png, or .swf format.</font>
          </td>
          <td align="center" bgcolor="#DDDDDD">
            <?php  echo $display_logo;?>
          </td>
        </tr>
      </table>

      <form method="post" action="<?php echo $PHP_SELF; ?>?producer_id=<?php echo $producer_id; ?>" enctype="multipart/form-data">
      File Description:<br>
      <input type="text" name="form_description"  size="40"> (For example, Bob&#146;s Logo)<br><small>Alternative text that will be shown if the logo is not dislpayed</small>
      <input type="hidden" name="MAX_FILE_SIZE" value="102400">
      <input type="hidden" name="producer_id" value="<?php echo $producer_id;?>">
      <br><br>
      File to upload/store in database:<br>
      <input type="file" name="form_data"  size="40">
      <p><input type="submit" name="submit" value="Upload">
      </form>
    </td>
  </tr>
</table>
</div>
<?php
}
include("template_footer_orders.php");
?>