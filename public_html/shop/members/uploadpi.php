<?php

// store.php3 - by Florian Dittmer <dittmer@gmx.net>
// Example php script to demonstrate the storing of binary files into
// an sql database. More information can be found at http://www.phpbuilder.com/

$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();

// Check if auth_type = administrator and there is a producer_id provided
if( strpos ($_SESSION['auth_type'], 'administrator') !== false && $_GET['producer_id'])
  {
    // Keep the same producer_id value
    $producer_id = $_GET['producer_id'];
  }
elseif ($_SESSION['producer_id_you'])
  {
    $producer_id = $_SESSION['producer_id_you'];
  }

include ( "template_hdr_orders.php" );


$sqll = '
  SELECT
    '.TABLE_PRODUCT_PREP.'.product_id,
    '.TABLE_PRODUCT_PREP.'.image_id,
    '.TABLE_PRODUCT_PREP.'.product_name
  FROM
    '.TABLE_PRODUCT_PREP.'
  LEFT JOIN
    '.TABLE_PRODUCT_IMAGES.'
      ON '.TABLE_PRODUCT_PREP.'.image_id = '.TABLE_PRODUCT_IMAGES.'.image_id
  WHERE
    '.TABLE_PRODUCT_PREP.'.product_id = "'.$product_id.'"';
$rsrl = @mysql_query($sqll,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
$num = mysql_numrows($rsrl);
while ( $row = mysql_fetch_array($rsrl) )
  {
    $image_id = $row['image_id'];
    $product_name = stripslashes($row['product_name']);
  }
if ( $image_id )
  {
    $display_image = '
      <td align="center" bgcolor="#FFFFFF">
        <img src="getimage.php?image_id='.$image_id.'" width="100" hspace="5" border="1">
      </td>';
  }
else
  {
    $display_image = "<td align=center bgcolor=#DDDDDD>No image uploaded</td>";
  }

// code that will be executed if the form has been submitted:

if ( $submit )
  {

    if ( ! $form_data )
      {
        echo '
          <div align=center>
            <font color=#3333FF><b>To add an image, you"ll need to select an image to upload from your computer<br>
            by clicking the "Browse" button.<br></font></b>
            Return to your <a href="edit_product_list.php?producer_id='.$producer_id.'">product list</a><br>
            Questions?: <a href="mailto:'.HELP_EMAIL.'">'.HELP_EMAIL.'</a><br><br>
          </div>';
      }
    else
      {

        $data = addslashes(fread(fopen($form_data, "r"), filesize($form_data)));

        if ( $form_data_size > 200000 )
          {
            echo '
              <div align=center><font color=#3333FF><b>
                Your image is too large. The file size must be less than 200K to <br>
                ensure that webpages load at a reasonable speed for all users.<br></font></b>
                Return to your <a href="edit_product_list.php?producer_id='.$producer_id.'">product list</a><br>
                Questions?: <a href="mailto:'.HELP_EMAIL.'">'.HELP_EMAIL.'</a><br><br>
              </div>';
          }
        else
          {
            if ( $image_id )
              {
                $sql = '
                  UPDATE
                    product_images
                  SET
                    image_desc = "'.$form_description.'",
                    bin_data = "'.$data.'",
                    filename = "'.$form_data_name.'",
                    filesize = "'.$form_data_size.'",
                    filetype = "'.$form_data_type.'"
                   WHERE
                    image_id = "'.$image_id.'"';
                $result = mysql_query($sql, $connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());

                $display_results .= '
                  <div align="center">
                    <font color="#3333FF"><b>Your image has been updated</b></font><br><br>
                    '.$display_image.'
                    <br>Click here to <a href="edit_product_list.php?producer_id='.$producer_id.'">return to your product list</a>.
                  </div>';
                echo $display_results;
              }
            else
              {
                $query = '
                  INSERT INTO
                    '.TABLE_PRODUCT_IMAGES.'
                  (
                    image_desc,
                    bin_data,
                    filename,
                    filesize,
                    filetype
                  ) ' .
                  "VALUES
                    (
                      '$form_description',
                      '$data',
                      '$form_data_name',
                      '$form_data_size',
                      '$form_data_type'
                    )";
                $result=MYSQL_QUERY($query,$connection);

                $image_id= mysql_insert_id();

                $sqlu = '
                  UPDATE
                    '.TABLE_PRODUCT_PREP.'
                  SET
                    image_id = "'.$image_id.'"
                  WHERE
                    product_id = "'.$product_id.'"';
                $resultu = mysql_query($sqlu, $connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());


                $display_results .= '
                  <div align="center">
                    <font color="#3333FF"><b>Your image has been uploaded.</b></font><br><br>
                    <br>Click here to <a href="edit_product_list.php?producer_id='.$producer_id.'">return to your product list</a>.
                  </div>';
                echo $display_results;

                MYSQL_CLOSE();
              }
          }
      }
  }
else
  {

    // else show the form to submit new data:
?>
<div align="center">
<table width="70%">
  <tr><td align="left">
    <h3>Upload an Image for <?php echo $product_name;?></h3>

    <table cellpadding="3" border="0">
      <tr><td valign="top">
      <b>Current Product Image:</b><br>
      To replace or upload an image,<br>use the form below.<br><br>
      Must be .jpg or .swf or .gif format and<br>no larger than 200K.
      </td>
        <?php echo $display_image;?>
        </tr>
    </table>

    <form method="post" action="<?php echo $PHP_SELF; ?>?producer_id=<?php echo $producer_id; ?>" enctype="multipart/form-data">

    <input type="hidden" name="product_id" value="<?php echo $product_id;?>">
    <input type="hidden" name="producer_id" value="<?php echo $producer_id;?>">
    File to upload/store in database:<br>
    <input type="file" name="form_data"  size="40">
    <p><input type="submit" name="submit" value="Upload">
    </form>
    Questions?: <a href="mailto:<?php echo HELP_EMAIL;?>"><?php echo HELP_EMAIL;?></a>
  </td></tr>
</table>
</div>

<?php
}
include("template_footer_orders.php");
?>