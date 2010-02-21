<?php
$user_type = 'valid_m';
include_once ("config_foodcoop.php");
session_start();
require_once ('securimage.php');
// validate_user(); Do not validate because non-members must access this form


// SPECIAL NOTES ABOUT THIS PAGE: //////////////////////////////////////////////
//                                                                            //
// This page MAY be accessed by visitors without logging in.  If not          //
// logged-in, Information will need to be added to the form.  If properly     //
// logged in already then the form will be prefilled with the appropriate     //
// information and can be used to update that information.                    //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////


// Set up the default action for this form (for the submit button)
if (! $_POST['action']) $action = 'Submit';

////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//                           PROCESS POSTED DATA                              //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////

// Get data from the $_POST variable that pertain to BOTH Submit (new members) and Update (existing members)
if ($_POST['action'] == 'Submit' || $_POST['action'] == 'Update')
  {
    $business_name = stripslashes ($_POST['business_name']);
    $last_name = stripslashes ($_POST['last_name']);
    $first_name = stripslashes ($_POST['first_name']);
    $last_name_2 = stripslashes ($_POST['last_name_2']);
    $first_name_2 = stripslashes ($_POST['first_name_2']);
    $no_postal_mail = $_POST['no_postal_mail'];
    $address_line1 = stripslashes ($_POST['address_line1']);
    $address_line2 = stripslashes ($_POST['address_line2']);
    $city = stripslashes ($_POST['city']);
    $state = stripslashes ($_POST['state']);
    $zip = stripslashes ($_POST['zip']);
    $county = stripslashes ($_POST['county']);
    $work_address_line1 = stripslashes ($_POST['work_address_line1']);
    $work_address_line2 = stripslashes ($_POST['work_address_line2']);
    $work_city = stripslashes ($_POST['work_city']);
    $work_state = stripslashes ($_POST['work_state']);
    $work_zip = stripslashes ($_POST['work_zip']);
    $email_address = stripslashes ($_POST['email_address']);
    $email_address_2 = stripslashes ($_POST['email_address_2']);
    $home_phone = stripslashes ($_POST['home_phone']);
    $work_phone = stripslashes ($_POST['work_phone']);
    $mobile_phone = stripslashes ($_POST['mobile_phone']);
    $fax = stripslashes ($_POST['fax']);
    $toll_free = stripslashes ($_POST['toll_free']);
    $home_page = stripslashes ($_POST['home_page']);

    // VALIDATE THE DATA
    $error_array = array ();

    if ( !$first_name || !$last_name ) array_push ($error_array, 'First and last name are required');

    if ( !$county ) array_push ($error_array, 'County of residence is required');

    if ( !$home_phone && !$mobile_phone ) array_push ($error_array, 'Either home or mobile phone number is required');

    if (!$email_address && ! $email_address_2) array_push ($error_array, 'Please enter at least one valid email address');
    if (! eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email_address) &&
        ! eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email_address_2))
        array_push ($error_array, 'Enter at least one valid email address');
  }

// Get data from the $_POST variable that pertain ONLY to Submit (new members)
if ($_POST['action'] == 'Submit')
  {
    $password1 = stripslashes ($_POST['password1']);
    $password2 = stripslashes ($_POST['password2']);
    $username_m = stripslashes ($_POST['username_m']);
    $how_heard = $_POST['how_heard'];
    $membership_type_id = $_POST['membership_type_id'];

    if ( strlen ($password1) < 6 )
      {
        array_push ($error_array, 'Passwords must be at least six characters long');
        $clear_password = true;
      }

    if ( $password1 != $password2 )
      {
        array_push ($error_array, 'Passwords do not match');
        $clear_password = true;
      }

    if ($clear_password === true)
      {
        $password1 = '';
        $password2 = '';
      }

    $query = '
      SELECT
        *
      FROM
        '.TABLE_MEMBER.'
      WHERE username_m = "'.mysql_real_escape_string ($username_m).'"';
    $sql =  @mysql_query($query, $connection) or die("You found a bug. <b>Error:</b> Check for existing member query " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());

    if ($row = mysql_fetch_object($sql)) array_push ($error_array, 'The username "'.$username_m.'" is already in use');

    if (!$username_m) array_push ($error_array, 'Choose a unique username');

    if ( !$membership_type_id ) array_push ($error_array, 'Choose a membership option');

    if ( !$affirmation ) array_push ($error_array, 'You must accept the affirmation before becoming a member');

  }


// Assemble any errors encountered so far
if (count ($error_array) > 0) $error_message = '
  <p class="error_message">The information was not accepted. Please correct the following problems and
  resubmit.<ul class="error_list"><li>'.implode ("</li>\n<li>", $error_array).'</li></ul></p>';



////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//                    GET MEMBER'S INFO FROM THE DATABASE                     //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////

// Get member information from the database to pre-fill the form (only if first time through -- $_POST is unset)


if (!$_POST['action'] && $_SESSION['valid_m'])
  {
    $query = '
      SELECT
        *
      FROM
        '.TABLE_MEMBER.'
      WHERE
        username_m = "'.$_SESSION['valid_m'].'"';
    $sql =  @mysql_query($query, $connection) or die("You found a bug. <b>Error:</b> Get member information query " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    while ($row = mysql_fetch_object($sql))
      {
        $last_name = stripslashes ($row->last_name);
        $first_name = stripslashes ($row->first_name);
        $last_name_2 = stripslashes ($row->last_name_2);
        $first_name_2 = stripslashes ($row->first_name_2);
        $business_name = stripslashes ($row->business_name);
        $address_line1 = stripslashes ($row->address_line1);
        $address_line2 = stripslashes ($row->address_line2);
        $city = stripslashes ($row->city);
        $state = $row->state;
        $zip = $row->zip;
        $county = $row->county;
        $work_address_line1 = $row->work_address_line1;
        $work_address_line2 = $row->work_address_line2;
        $work_city = $row->work_city;
        $work_state = $row->work_state;
        $work_zip = $row->work_zip;
        $home_phone = $row->home_phone;
        $work_phone = $row->work_phone;
        $mobile_phone = $row->mobile_phone;
        $fax = $row->fax;
        $toll_free = $row->toll_free;

        $username_m = $row->username_m;
        $no_postal_mail = $row->no_postal_mail;
        $email_address = $row->email_address;
        $email_address_2 = $row->email_address_2;
        $home_page = $row->home_page;
        $how_heard_id = $row->how_heard_id;
        $username_m = $_SESSION['valid_m'];
        $action = 'Update';
      }
  }


////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//  SET UP THE SELECT AND CHECKBOX FORMS FOR DISPLAY BASED UPON PRIOR VALUES  //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////


// Generate the membership_types_display and membership_types_options
$membership_types_options = '
      <option value="">Choose One</option>';
$query = '
  SELECT
    *
  FROM
    '.TABLE_MEMBERSHIP_TYPES.'
  WHERE 1';
$sql =  @mysql_query($query, $connection) or die("You found a bug. <b>Error:</b> Select Delivery Types Query " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
while ($row = mysql_fetch_object($sql))
  {
    $selected = '';
    if ($membership_type_id == $row->membership_type_id)
      {
        $selected = ' selected';
        $membership_type_text = $row->membership_description;
      }
    $membership_types_display .= '
      <dt>'.$row->membership_class.'</dt>
      <dd>'.$row->membership_description.'</dd>';
    $membership_types_options .= '
      <option value="'.$row->membership_type_id.'"'.$selected.'>'.$row->membership_class.'</option>';
  }

// Build how-heard select options
$how_heard_options = '
      <option value="">Choose One</option>';
$query = '
  SELECT
    *
  FROM
    '.TABLE_HOW_HEARD.'
  WHERE 1';
$sql =  @mysql_query($query, $connection) or die("You found a bug. <b>Error:</b> Select Delivery Types Query " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
while ($row = mysql_fetch_object($sql))
  {
    $selected = '';
    if ($how_heard_id == $row->how_heard_id)
      {
        $selected = ' selected';
        $how_heard_text = $row->how_heard_name;
      }
    $how_heard_options .= '
      <option value="'.$row->how_heard_id.'"'.$selected.'>'.$row->how_heard_name.'</option>';
  }

if ($affirmation == 'yes') $affirmation_check = ' checked';
if ($volunteer == 'yes') $volunteer_check = ' checked';
if ($producer == 'yes') $producer_check = ' checked';
if ($no_postal_mail == '1') $no_postal_mail_check = ' checked';


////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//                          DISPLAY THE INPUT FORM                            //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////

$display_form_title .= '
  <h1>'.date('Y').' '.SITE_NAME.' Registration</h1><br>
  <div style="margin:auto;width:90%;padding:1em;">';

$welcome_message = '
  <p><em>Thank you for your interest in becoming a member of '.SITE_NAME.'.
  '.SITE_NAME.' customers and producers are interested in local foods and products
  produced with sustainable practices that demonstrate good stewardship of the environment.';

$display_form_top .= $welcome_message.'
  To become a member, please read the <a href="'.TERMS_OF_SERVICE.'" target="_blank">
  Terms of Service</a>, and then complete the following information and click submit.</em></p>';

if (! $_SESSION['valid_m'])
  {
    $display_form_top .= '
      <p>If you are already a member, please <a href="orders_login.php?call='.$_SERVER['PHP_SELF'].'">sign in here</a>.  Otherwise fill out the form below to become a member.</p>';
  }
else
  {
    $display_form_top .= '
      <p>As an existing member, you can use the form below to update your membership information.</p>';
  }

$display_form_text .= '
  First name:    '.$first_name.'
  Last name:     '.$last_name.'
  First name 2:  '.$first_name_2.'
  Last name 2:   '.$last_name_2.'
  Business name: '.$business_name.'

  Address: 
      '.$address_line1.'
      '.$address_line2.'
      '.$city.', '.$state.' '.$zip.'
      '.$county.' County

  Do not send postal mail? '.$no_postal_mail_check.'

  Work Address:
      '.$work_address_line1.'
      '.$work_address_line2.'
      '.$work_city.', '.$work_state.' '.$work_zip.'

  Home phone: '.$home_phone.'
  Work phone: '.$work_phone.'
  Cell phone: '.$mobile_phone.'
  FAX:        '.$fax.'
  Toll-free: '.$toll_free.'

  E-mail address:   '.$email_address.'
  E-mail address 2: '.$email_address_2.'
  Home Page:        '.$home_page.'

  Username: '.$username_m.'
  Password: '.$password1.'

  Membership type: '.$membership_type_text.'

  Interested in volunteering? '.$volunteer_check.'

  Read the membership documents? '.$affirmation_check.'
  ';

$display_form_html .= $error_message.'
  <form action="'.$_SERVER['PHP_SELF'].'" name="delivery" method="post">

    <table cellspacing="15" cellpadding="2" width="100%" border="0" align="center">
      <tbody>

      <tr>
        <th class="memberform">Section 1: General Information</th>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>First&nbsp;Name:</strong></td>
              <td><input maxlength="20" size="25" name="first_name" value="'.$first_name.'"></td>
              <td class="form_key"><strong>Last&nbsp;Name:</strong></td>
              <td><input maxlength="20" size="25" name="last_name" value="'.$last_name.'"></td>
            </tr>
            <tr>
              <td class="form_key"><strong>First&nbsp;Name&nbsp;2:</strong></td>
              <td><input maxlength="20" size="25" name="first_name_2" value="'.$first_name_2.'"></td>
              <td class="form_key"><strong>Last&nbsp;Name&nbsp;2:</strong></td>
              <td><input maxlength="20" size="25" name="last_name_2" value="'.$last_name_2.'"></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Business&nbsp;Name:</strong></td>
              <td><input maxlength="50" size="45" name="business_name" value="'.$business_name.'"></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <th class="memberform">Section 2: Contact Information</th>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Address:</strong></td>
              <td colspan="6"><input maxlength="75" size="50" name="address_line1" value="'.$address_line1.'"></td>
            </tr>
            <tr>
              <td class="form_key"><strong>Address&nbsp;2:</strong></td>
              <td colspan="6"><input maxlength="75" size="50" name="address_line2" value="'.$address_line2.'"></td>
            </tr>
            <tr>
              <td class="form_key"><strong>City/State/Zip:</strong></td>
              <td><input maxlength="50" size="25" name="city" value="'.$city.'"></td>
              <td><input maxlength="2" size="2" name="state" value="'.$state.'"></td>
              <td><input maxlength="10" size="10" name="zip" value="'.$zip.'"></td>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td class="form_key"><strong>County:</strong></td>
              <td colspan="3"><input maxlength="75" size="25" name="county" value="'.$county.'"></td>
              <td class="form_key" colspan="2"><strong>Do Not Send Postal Mail:</strong></td>
              <td><input type="checkbox" name="no_postal_mail" value="1"'.$no_postal_mail_check.'></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Work&nbsp;Address:</strong></td>
              <td colspan="3"><input maxlength="75" size="50" name="work_address_line1" value="'.$work_address_line1.'"></td>
            </tr>
            <tr>
              <td class="form_key"><strong>Work&nbsp;Address&nbsp;2:</strong></td>
              <td colspan="3"><input maxlength="75" size="50" name="work_address_line2" value="'.$work_address_line2.'"></td>
            </tr>
            <tr>
              <td class="form_key"><strong>City/State/Zip:</strong></td>
              <td><input maxlength="50" size="25" name="work_city" value="'.$work_city.'"></td>
              <td><input maxlength="2" size="2" name="work_state" value="'.$work_state.'"></td>
              <td><input maxlength="10" size="10" name="work_zip" value="'.$work_zip.'"></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Home&nbsp;Phone:</strong></td>
              <td><input maxlength="20" size="25" name="home_phone" value="'.$home_phone.'"></td>
              <td class="form_key"><strong>Work&nbsp;Phone:</strong></td>
              <td><input maxlength="20" size="25" name="work_phone" value="'.$work_phone.'"></td>
            </tr>
            <tr>
              <td class="form_key"><strong>Mobile&nbsp;Phone:</strong></td>
              <td><input maxlength="20" size="25" name="mobile_phone" value="'.$mobile_phone.'"></td>
              <td class="form_key"><strong>FAX:</strong></td>
              <td><input maxlength="20" size="25" name="fax" value="'.$fax.'"></td>
            </tr>
            <tr>
              <td class="form_key"><strong>Toll&nbsp;Free:</strong></td>
              <td><input maxlength="20" size="25" name="toll_free" value="'.$toll_free.'"></td>
              <td class="form_key"><strong>&nbsp;</strong></td>
              <td>&nbsp;</td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Email&nbsp;Address:</strong></td>
              <td><input maxlength="80" size="45" name="email_address" value="'.$email_address.'"></td>
            </tr>
            <tr>
              <td class="form_key"><strong>Email&nbsp;Address&nbsp;2:</strong></td>
              <td><input maxlength="80" size="45" name="email_address_2" value="'.$email_address_2.'"></td>
            </tr>
            <tr>
              <td class="form_key"><strong>Home&nbsp;Page:</strong></td>
              <td>http://<input maxlength="80" size="40" name="home_page" value="'.$home_page.'"></td>
            </tr>
          </table>
        </td>
      </tr>';

if (! $_SESSION['valid_m']) // Do not show the following part to existing members....
  {
$display_form_html .= '
      <tr>
        <th class="memberform">Section 3: Access Credentials</th>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Username:</strong></td>
              <td><input maxlength="20" size="25" name="username_m" value="'.$username_m.'"></td>
            </tr>
            <tr>
              <td class="form_key"><strong>Password:</strong></td>
              <td><input type="password" maxlength="20" size="25" name="password1" value="'.$password1.'"></td>
            </tr>
            <tr>
              <td class="form_key"><strong>Confirm:</strong></td>
              <td><input type="password" maxlength="20" size="25" name="password2" value="'.$password2.'"></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <th class="memberform">Section 4: Membership Type</th>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key" rowspan="2" valign="top"><strong>Membership&nbsp;Type:</strong></td>
              <td><select name="membership_type_id">'.$membership_types_options.'</select></td>
            </tr>
            <tr>
              <td>
                <dl>
                  '.$membership_types_display.'
                </dl>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <th class="memberform">Section 5: Additional Information</th>
      </tr>


      <tr>
        <td><input type="checkbox" name="volunteer" value="yes"'.$volunteer_check.'> YES! I&lsquo;m interested in volunteering to help '.SITE_NAME.'.</td>
      </tr>

      <tr>
        <td><input type="checkbox" name="affirmation" value="yes"'.$affirmation_check.'>
          I acknowledge that I have read and understand the '.SITE_NAME.' <a href="'.TERMS_OF_SERVICE.'" target="_blank">
          Terms of Service</a> statement.
        </td>
      </tr>';
  }

$display_form_html .= '
      <tr>
        <td><input type="checkbox" name="producer" value="yes"'.$producer_check.'> I am interested in becoming a producer member for '.SITE_NAME.'.</td>
      </tr>

      <tr>
        <td align="center">
          <input type="submit" name="action" value="'.$action.'">
        </td>
      </tr>
      </tbody>
    </table>
  </form></div>';


////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//         ADD OR CHANGE INFORMATION IN THE DATABASE FOR THIS MEMBER          //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////

// If everything validates, then we can post to the database...
if (count ($error_array) == 0 && $_POST['action'] == 'Submit') // For new members
  {
    $set_member_id = '';
    if (FILL_IN_MEMBER_ID)
      {
        // This query will find blanks in the members table and fill in the gaps with the new member_id
        $query = '
          SELECT l.member_id + 1 AS empty_member_id
          FROM members AS l
          LEFT OUTER JOIN members AS r ON l.member_id + 1 = r.member_id
          WHERE r.member_id IS NULL';
        $sql = @mysql_query($query, $connection) or die("You found a bug. <b>Error:</b>
          Member insert Query " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
        if ($row = mysql_fetch_object($sql))
          {
            $set_member_id = 'member_id = "'.$row->empty_member_id.'",';
          }
      }
    // Everything validates correctly so do the INSERT and send the EMAIL

    // Begin by getting this member's pending status based upon the membership_type_id
    $query = '
      SELECT
          pending,
          initial_cost
        FROM
          membership_types
        WHERE membership_type_id = "'.$membership_type_id.'"';
    $result = @mysql_query($query, $connection) or die("You found a bug. <b>Error:</b>
        Member insert Query " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    if ($row = mysql_fetch_object($result))
      {
        $pending = $row->pending;
        $initial_cost = $row->initial_cost;
      }

    // Then do the database insert with the relevant membership data
    $query = '
      INSERT INTO
        '.TABLE_MEMBER.'
      SET
        '.$set_member_id.'
        pending = "'.mysql_escape_string ($pending).'",
        auth_type = "member",
        membership_type_id = "'.mysql_escape_string ($membership_type_id).'",
        membership_date = now(),
        username_m = "'.mysql_escape_string ($username_m).'",
        password = md5("'.mysql_escape_string ($password1).'"),
        last_name = "'.mysql_escape_string ($last_name).'",
        first_name = "'.mysql_escape_string ($first_name).'",
        last_name_2 = "'.mysql_escape_string ($last_name_2).'",
        first_name_2 = "'.mysql_escape_string ($first_name_2).'",
        business_name = "'.mysql_escape_string ($business_name).'",
        address_line1 = "'.mysql_escape_string ($address_line1).'",
        address_line2 = "'.mysql_escape_string ($address_line2).'",
        city = "'.mysql_escape_string ($city).'",
        state = "'.mysql_escape_string ($state).'",
        zip = "'.mysql_escape_string ($zip).'",
        county = "'.mysql_escape_string ($county).'",
        work_address_line1 = "'.mysql_escape_string ($work_address_line1).'",
        work_address_line2 = "'.mysql_escape_string ($work_address_line2).'",
        work_city = "'.mysql_escape_string ($work_city).'",
        work_state = "'.mysql_escape_string ($work_state).'",
        work_zip = "'.mysql_escape_string ($work_zip).'",
        home_phone = "'.mysql_escape_string ($home_phone).'",
        work_phone = "'.mysql_escape_string ($work_phone).'",
        mobile_phone = "'.mysql_escape_string ($mobile_phone).'",
        fax = "'.mysql_escape_string ($fax).'",
        toll_free = "'.mysql_escape_string ($toll_free).'",
        email_address = "'.mysql_escape_string ($email_address).'",
        email_address_2 = "'.mysql_escape_string ($email_address_2).'",
        home_page = "'.mysql_escape_string ($home_page).'",
        how_heard_id = "'.mysql_escape_string ($how_heard_id).'",
        no_postal_mail = "'.mysql_escape_string ($no_postal_mail).'"';

    $result = @mysql_query($query,$connection) or die("You found a bug. <b>Error:</b>
        Member insert Query " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());

    $member_id= mysql_insert_id();

    // Then do the database insert with the transaction information (membership receivables)
    $query = '
      INSERT INTO
        '.TABLE_TRANSACTIONS.'
          (
            transaction_type,
            transaction_name,
            transaction_amount,
            transaction_user,
            transaction_member_id,
            transaction_delivery_id,
            transaction_taxed,
            transaction_comments,
            transaction_timestamp
          )
        VALUES
          (
            "24",
            "Membership Receivables",
            "'.$initial_cost.'",
            "member_form",
            "'.$member_id.'",
            (SELECT delivery_id FROM '.TABLE_CURDEL.'),
            "0",
            "'.$comments.'",
            now()
          )';

    $result = @mysql_query($query,$connection) or die("You found a bug. <b>Error:</b>
        Member insert Query " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());



    // Figure out what sort of "welcome" to give the new member...
    if ($pending == 1)
      {
        $membership_disposition = '
          <p class="error_message">Your membership number will be #'.$member_id.'.  Your membership
          application will be reviewed by an administrator and you will be notified when it becomes
          active.  Until then, you will not be able to log in.</p>';
      }
    else // Pending = 0
      {
        $membership_disposition = '
          <p class="error_message">Your membership number is #'.$member_id.'.  Your membership has
          been automatically activated and you may <a href="'.BASE_URL.PATH.'members/orders_login.php">
          sign in</a> immediately.</p>';
      }

    if ($initial_cost > 0) $membership_disposition .= '
      <p class="error_message">Please deposit your membership payment of $'.number_format ($initial_cost, 2).' to:<br><br>
     Atamai Foods Inc <br>00-0000-000000-000</p>';
    if ( PAYPAL_EMAIL && $initial_cost > 0 ) $membership_disposition .= '
      <p class="error_message">Or make a payment online through PayPal (opens in a new window)
      <form target="paypal" method="post" action="https://www.paypal.com/cgi-bin/webscr">
      <input type="hidden" value="_xclick" name="cmd">
      <input type="hidden" value="'.PAYPAL_EMAIL.'" name="business">
      <input type="hidden" name="amount" value="'.number_format ($initial_cost, 2).'">
      <input type="hidden" value="Membership payment for #'.$member_id.' '.$first_name.' '.$last_name.' : '.$business_name.'" name="item_name">
      <input type="image" border="0" alt="Make payment with PayPal" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_paynowCC_LG.gif">
      </form></p>';

    $display_form_message .= $membership_disposition;

    // Now send email notification(s)
    $email_to = preg_replace ('/SELF/', $email_address, MEMBER_FORM_EMAIL);
    $email_subject = 'Welcome to '.SITE_NAME.' - '.$first_name.' '.$last_name.' (#'.$member_id.')';
    $boundary = uniqid();
    // Set up the email preamble...
    $email_preamble = '
      <p>Following is a copy of the membership information you submitted to '.SITE_NAME.'.</p>';
    $email_preamble .= $membership_disposition.$welcome_message;

    // Disable all form elements for emailing
    $html_version = $email_preamble.preg_replace ('/<(input|select|textarea)/', '<\1 disabled', $display_form_html);

    $email_headers  = "From: ".MEMBERSHIP_EMAIL."\r\n";
    $email_headers .= "Reply-To: ".MEMBERSHIP_EMAIL."\r\n";
    $email_headers .= "Errors-To: web@".DOMAIN_NAME."\r\n";
    $email_headers .= "MIME-Version: 1.0\r\n";
    $email_headers .= "Content-type: multipart/alternative; boundary=\"$boundary\"\r\n";
    $email_headers .= "Message-ID: <".md5(uniqid(time()))."@".DOMAIN_NAME.">\r\n";
    $email_headers .= "X-Mailer: PHP ".phpversion()."\r\n";
    $email_headers .= "X-Priority: 3\r\n";
    $email_headers .= "X-AntiAbuse: This is a machine-generated response to a user-submitted form at ".SITE_NAME.".\r\n";

    $email_body .= "\r\n--".$boundary;
    $email_body .= "\r\nContent-Type: text/plain; charset=us-ascii";
    $email_body .= "\r\n\r\n".strip_tags ($email_preamble).$display_form_text;
    $email_body .= "\r\n--".$boundary;
    $email_body .= "\r\nContent-Type: text/html; charset=us-ascii";
    $email_body .= "\r\n\r\n".$html_version;
    $email_body .= "\r\n--".$boundary.'--';

    mail ($email_to, $email_subject, $email_body, $email_headers);
    $email_sent = true;
  }

elseif (count ($error_array) == 0 && $_POST['action'] == 'Update') // For existing members
  {
    // Everything validates correctly so do the INSERT and send the EMAIL
    $query = '
      UPDATE
        '.TABLE_MEMBER.'
      SET
        last_name = "'.mysql_escape_string ($last_name).'",
        first_name = "'.mysql_escape_string ($first_name).'",
        last_name_2 = "'.mysql_escape_string ($last_name_2).'",
        first_name_2 = "'.mysql_escape_string ($first_name_2).'",
        business_name = "'.mysql_escape_string ($business_name).'",
        address_line1 = "'.mysql_escape_string ($address_line1).'",
        address_line2 = "'.mysql_escape_string ($address_line2).'",
        city = "'.mysql_escape_string ($city).'",
        state = "'.mysql_escape_string ($state).'",
        zip = "'.mysql_escape_string ($zip).'",
        county = "'.mysql_escape_string ($county).'",
        work_address_line1 = "'.mysql_escape_string ($work_address_line1).'",
        work_address_line2 = "'.mysql_escape_string ($work_address_line2).'",
        work_city = "'.mysql_escape_string ($work_city).'",
        work_state = "'.mysql_escape_string ($work_state).'",
        work_zip = "'.mysql_escape_string ($work_zip).'",
        home_phone = "'.mysql_escape_string ($home_phone).'",
        work_phone = "'.mysql_escape_string ($work_phone).'",
        mobile_phone = "'.mysql_escape_string ($mobile_phone).'",
        fax = "'.mysql_escape_string ($fax).'",
        toll_free = "'.mysql_escape_string ($toll_free).'",
        email_address = "'.mysql_escape_string ($email_address).'",
        email_address_2 = "'.mysql_escape_string ($email_address_2).'",
        home_page = "'.mysql_escape_string ($home_page).'",
        no_postal_mail = "'.mysql_escape_string ($no_postal_mail).'"
      WHERE
        username_m = "'.mysql_escape_string ($username_m).'"';

    $result = @mysql_query($query,$connection) or die("You found a bug. <b>Error:</b>
        Member insert Query " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    $display_form_message = '
      <p class="error_message">Your membership information has been successfully updated.<br><br></p>';
  }

if ($producer == 'yes' && count ($error_array) == 0)
  {
    // Get the member_id from members.username_m
    $query = '
      SELECT
        member_id
      FROM
        '.TABLE_MEMBER.'
      WHERE
        username_m = "'.$username_m.'"';
    $sql = @mysql_query($query, $connection) or die("You found a bug. <b>Error:</b>
      Member insert Query " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    if ($row = mysql_fetch_object($sql)) $member_id = $row->member_id;
    $_SESSION['member_id'] = $member_id;
    $_SESSION['business_name'] = $business_name;
    $_SESSION['website'] = $home_page;
    $display_form_message .= '
      <p class="error_message">You also expressed interest in becoming a producer member.</p>
      <p class="error_message">You can access the <a href="producer_form.php">producer
      registration form</a> immediately or you can return later to complete the form.
      It is a lengthy form  and you may wish to print it out prior to filling it out online.<br><br></p>';
//     header( "Location: producer_form.php?action=from_member_form");
  }
include ("template_hdr_orders.php");
echo $display_form_title;
echo $display_form_message;
if ( !$email_sent )
  {
    echo $display_form_top;
    echo $display_form_html;
  }
include("template_footer_orders.php");


