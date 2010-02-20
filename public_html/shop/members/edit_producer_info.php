<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();
$date_today = date("F j, Y");
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

if ( $producer_id_you != $producer_id )
  {
    $fulllist_link = "<b> - <a href=\"edit_info_list.php#$producer_id\">Return to List</a></b>";
  }

if ( $producer_submit )
  {
    $sql = '
      UPDATE
        '.TABLE_PRODUCER.'
      SET
        producttypes = "'.$producttypes.'",
        about = "'.$about.'",
        general_practices = "'.$practices.'",
        ingredients = "'.$ingredients.'",
        additional = "'.$additional.'",
        highlights = "'.$highlights.'"
      WHERE
        producer_id = "'.$producer_id.'"';
    $result = mysql_query($sql, $connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());

    $message = "<font color=#3333FF><b>Your information has been updated</b></font><br>";
  }

$sqlr = '
  SELECT
    '.TABLE_PRODUCER.'.*,
    '.TABLE_MEMBER.'.*
  FROM
    '.TABLE_PRODUCER.',
    '.TABLE_MEMBER.'
  WHERE
    '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
    AND '.TABLE_PRODUCER.'.producer_id = "'.$producer_id.'"
  ORDER BY
    '.TABLE_MEMBER.'.business_name ASC';
$rsr = @mysql_query($sqlr,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
while ( $row = mysql_fetch_array($rsr) )
  {
    $business_name = stripslashes ($row['business_name']);
    $producttypes = stripslashes ($row['producttypes']);
    $about = stripslashes ($row['about']);
    $general_practices = stripslashes ($row['general_practices']);
    $ingredients = stripslashes ($row['ingredients']);
    $highlights = stripslashes ($row['highlights']);
    $additional = stripslashes ($row['additional']);

    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $first_name_2 = $row['first_name_2'];
    $last_name_2 = $row['last_name_2'];
    $address_line1 = $row['address_line1'];
    $address_line2 = $row['address_line2'];
    $city = $row['city'];
    $state = $row['state'];
    $zip = $row['zip'];
    $email_address = $row['email_address'];
    $email_address_2 = $row['email_address_2'];
    $home_phone = $row['home_phone'];
    $work_phone = $row['work_phone'];
    $mobile_phone = $row['mobile_phone'];
    $fax = $row['fax'];
    $toll_free = $row['toll_free'];
    $home_page = $row['home_page'];
    $membership_date = $row['membership_date'];
    $pub_address = $row['pub_address'];
    $pub_email = $row['pub_email'];
    $pub_email2 = $row['pub_email2'];
    $pub_phoneh = $row['pub_phoneh'];
    $pub_phonew = $row['pub_phonew'];
    $pub_phonec = $row['pub_phonec'];
    $pub_phonet = $row['pub_phonet'];
    $pub_fax = $row['pub_fax'];
    $pub_web = $row['pub_web'];

    $display_logo = "";

    $sqll = '
      SELECT
        '.TABLE_PRODUCER_LOGOS.'.*
      FROM
        '.TABLE_PRODUCER_LOGOS.'
      WHERE
        '.TABLE_PRODUCER_LOGOS.'.producer_id = "'.$producer_id.'"';
    $rsrl = @mysql_query($sqll,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    while ($row = mysql_fetch_array($rsrl))
      {
        $logo_id = $row['logo_id'];
        $logo_desc = $row['logo_desc'];

        if ( $logo_id )
          {
            $display_logo = "<td width=\"150\">
              <img src=\"getlogos.php?logo_id=$logo_id\" width=\"150\" hspace=\"5\" alt=\"$logo_desc\"></td>";
          }
      }

    $show_name = '';
    include("../func/show_name.php");

    $display .= "
      <table width=\"100%\" cellpadding=\"10\" cellspacing=\"2\" border=\"1\" bordercolor=\"#000000\">
        <tr><td bgcolor=\"#DDDDDD\">
        <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#DDDDDD\">
          <tr>
            $display_logo
          <td>
        <font face=\"arial\" size=\"4\">
        $show_name<br><br>$city, $state
        </font><br>
          </td><td>
        <font face=\"arial\" size=\"-1\">
        Click here to <a href=\"upload.php?producer_id=$producer_id\">upload a logo</a>.
        </font><br>
          </td><td align=\"right\"><font face=\"arial\" size=\"-1\">";

    if ($address_line1 && !$pub_address) { $display .= "$address_line1<br>"; }
    if ($address_line2 && !$pub_address) { $display .= "$address_line2<br>"; }
    if ($address_line1 && !$pub_address) { $display .= "$city, $state $zip<br>"; }

    if ($email_address && !$pub_email)
      {
        $display .= "<a href=\"mailto:$email_address\">$email_address</a><br>";
      }
    if ($email_address_2 && !$pub_email2)
      {
        $display .= "<a href=\"mailto:$email_address_2\">$email_address_2</a><br>";
       }
    if ($home_phone && !$pub_phoneh) { $display .= "$home_phone (home)<br>"; }
    if ($work_phone && !$pub_phonew) { $display .= "$work_phone (work)<br>"; }
    if ($mobile_phone && !$pub_phonec) { $display .= "$mobile_phone (cell)<br>"; }
    if ($fax && !$pub_fax) { $display .= "$fax (fax)<br>"; }
    if ($toll_free && !$pub_phonet) { $display .= "$toll_free (toll free)<br>"; }
    if ($home_page && !$pub_web) { $display .= "<a href=\"http://$home_page\" target=\"_blank\">$home_page</a><br>"; }

    $display .= '</td></tr></table>
      </td></tr>
      </table>';
  }

$producer_id_lower = strtolower($producer_id);
?>

  <!-- CONTENT BEGINS HERE -->

<h3>Editing Producer Information</h3>

<?php echo $message;?>

<?php echo $display;?>

<div align="right">
  <b><a href="<?php echo BASE_URL.PATH.'producers/'.$producer_id_lower;?>.php" target="_blank">Click here to see the live webpage</a></b>
  <?php echo $fulllist_link;?>
</div>

<blockquote>
  <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">

    <font size="3"><b>Product Types:</b></font> (max. 150 characters, use keywords like lettuce, berries, buffalo, soap, etc.)<br>
    <input type="text" name="producttypes" value="<?php echo $producttypes;?>" size="75" maxlength="150">
    <br><br>

    For the sections below, <b>to separate your paragraphs inside of the editing
    boxes</b>, type: &lt;br&gt;&lt;br&gt; in between each paragraph. This will serve as
    two line breaks (&lt;br&gt;) to space text out for easier reading.
    <br><br>

    <font size="3"><b>About Us:</b></font> (Use this space to describe your business, you, how you got started, etc.)<br>
    <textarea name="about" cols="75" rows="17"><?php echo $about;?></textarea>
    <br><br>

    <font size="3"><b>Ingredients:</b></font> (Use this space to outline ingredients if relevant.)<br>
    <textarea name="ingredients" cols="75" rows="17"><?php echo $ingredients;?></textarea>
    <br><br>

    <font size="3"><b>Practices:</b></font> (Use this space to describe your standards and practices. For example, if you use all natural products, etc.)<br>
    <textarea name="practices" cols="75" rows="17"><?php echo $general_practices;?></textarea>
    <br><br>

    <font size="3"><b>Additional Information:</b></font> (Use this space for anything that isn't covered in these other sections.)<br>
    <textarea name="additional" cols="75" rows="17"><?php echo $additional;?></textarea>
    <br><br>

    <font size="3"><b>Highlights this Month:</b></font><br>
    <textarea name="highlights" cols="75" rows="17"><?php echo $highlights;?></textarea>
    <br><br>
</blockquote>

<div align="center">
  <input type="hidden" name="producer_id" value="<?php echo $producer_id ?>">
  <input type="submit" name="producer_submit" value="Click here to save your info">
</div>

<br><br>

  <!-- CONTENT ENDS HERE -->

<?php include("template_footer_orders_edit.php");?>
