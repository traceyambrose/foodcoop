<?php
$user_type = 'valid_m';
include_once ("config_foodcoop.php");
session_start();
// validate_user(); Do not validate because non-members must access this form

if ( $_SESSION['member_id'])
  {
    $member_id = $_SESSION['member_id'];
  }
elseif ( $_POST['member_id'])
  {
    $member_id = $_SESSION['member_id'];
  }
else
  {
    header( "Location: index.php");
    exit;
  }

if ($_GET['action'] == 'from_member_form')
  {
    $error_message = 'Membership information has been accepted. In order to be considered as a producer
    for the '.ORGANIZATION_TYPE.', please fill out the form below.  If you do not have time to fill out
    the form now, you may want to print it and return to the membership form at a later time.';
    $website = $_SESSION['website'];
    $business_name = $_SESSION['business_name'];
    unset ($_SESSION['website']);
    unset ($_SESSION['business_name']);
  }

// SPECIAL NOTES ABOUT THIS PAGE: //////////////////////////////////////////////
//                                                                            //
// This page MAY be accessed by visitors without logging in.  However, it     //
// will only be accessed by visitors who either are already accepted members  //
// or who have already supplied an application for membership.                //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////


////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//                           PROCESS POSTED DATA                              //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////

// Get data from the $_POST variable that pertain to BOTH Submit (new members) and Update (existing members)
if ($_POST['action'] == 'Submit')
  {
    $member_id = $_POST['member_id'];
    $producer_id = preg_replace ('/[^0-9A-Za-z\-\._]/','', $_POST['producer_id']); // Only: A-Z a-z 0-9 - . _
    $business_name = stripslashes ($_POST['business_name']);

    $products = stripslashes ($_POST['products']);
    $practices = stripslashes ($_POST['practices']);
    $general_practices = stripslashes ($_POST['general_practices']);
    $pest_management = stripslashes ($_POST['pest_management']);
    $productivity_management = stripslashes ($_POST['productivity_management']);
    $feeding_practices = stripslashes ($_POST['feeding_practices']);
    $soil_management = stripslashes ($_POST['soil_management']);
    $water_management = stripslashes ($_POST['water_management']);
    $land_practices = stripslashes ($_POST['land_practices']);
    $additional_information = stripslashes ($_POST['additional_information']);
    $licenses_insurance = stripslashes ($_POST['licenses_insurance']);
    $organic_products = stripslashes ($_POST['organic_products']);
    $certifying_agency = stripslashes ($_POST['certifying_agency']);
    $agency_phone = stripslashes ($_POST['agency_phone']);
    $agency_fax = stripslashes ($_POST['agency_fax']);
    $organic_cert = stripslashes ($_POST['organic_cert']);
    $producttypes = stripslashes ($_POST['producttypes']);
    $about = stripslashes ($_POST['about']);
    $ingredients = stripslashes ($_POST['ingredients']);
    $practices = stripslashes ($_POST['practices']);
    $highlights = stripslashes ($_POST['highlights']);
    $additional = stripslashes ($_POST['additional']);
    $pub_address = stripslashes ($_POST['pub_address']);
    $pub_email = stripslashes ($_POST['pub_email']);
    $pub_email2 = stripslashes ($_POST['pub_email2']);
    $pub_phoneh = stripslashes ($_POST['pub_phoneh']);
    $pub_phonew = stripslashes ($_POST['pub_phonew']);
    $pub_phonec = stripslashes ($_POST['pub_phonec']);
    $pub_phonet = stripslashes ($_POST['pub_phonet']);
    $pub_fax = stripslashes ($_POST['pub_fax']);
    $pub_web = stripslashes ($_POST['pub_web']);
    $liability_statement = stripslashes ($_POST['liability_statement']);

    // VALIDATE THE DATA
    $error_array = array ();

    if ( !$member_id ) array_push ($error_array, 'Member ID is unknown.  You must access this form after logging in and/or submitting a <a href="member_form.php">membership form</a>');

    if ( !$producer_id ) array_push ($error_array, 'You must enter a unique producer ID for your business');

    if ( $producer_id && strlen ($producer_id) != 5  ) array_push ($error_array, 'Producer ID must be five unique alphanumeric characters');

    $query = '
      SELECT
        member_id
      FROM
        '.TABLE_PRODUCER.'
      WHERE producer_id = "'.mysql_real_escape_string ($producer_id).'"';
    $sql =  @mysql_query($query, $connection) or die("You found a bug. <b>Error:</b> Check for existing member query " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    if ($row = mysql_fetch_object($sql))
      {
        if ($member_id == $row->member_id) array_push ($error_array, 'You are already registered with this Producer ID');
        if ($member_id != $row->member_id) array_push ($error_array, 'The Producer ID you have chosen is already in use, please select another value');
      }

    $query = '
      SELECT
        member_id
      FROM
        '.TABLE_PRODUCER.'
      WHERE member_id = "'.mysql_real_escape_string ($member_id).'"';
    $sql =  @mysql_query($query, $connection) or die("You found a bug. <b>Error:</b> Check for existing member query " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    if ($row = mysql_fetch_object($sql)) array_push ($error_array, 'You are already registered as a Producer');

    if ( !$business_name ) array_push ($error_array, 'A business name is required in order to register as a producer');

    if ( $liability_statement != 1 ) array_push ($error_array, 'In order to be accepted as a producer, you must agree with the stated terms');
  }

// Assemble any errors encountered so far
if (count ($error_array) > 0) $error_message = '
  <p class="message">The information was not accepted. Please correct the following problems and
  resubmit.<ul class="error_list"><li>'.implode ("</li>\n<li>", $error_array).'</li></ul></p>';


////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//  SET UP THE SELECT AND CHECKBOX FORMS FOR DISPLAY BASED UPON PRIOR VALUES  //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////

if ($liability_statement == 1) $liability_statement_check = ' checked';
if ($organic_cert == '1') $organic_cert_yes = ' checked';
if ($organic_cert == '0') $organic_cert_no = ' checked';
if ($pub_address == '1') $pub_address_check = ' checked';
if ($pub_email == '1') $pub_email_check = ' checked';
if ($pub_email2 == '1') $pub_email2_check = ' checked';
if ($pub_phoneh == '1') $pub_phoneh_check = ' checked';
if ($pub_phonew == '1') $pub_phonew_check = ' checked';
if ($pub_phonec == '1') $pub_phonec_check = ' checked';
if ($pub_phonet == '1') $pub_phonet_check = ' checked';
if ($pub_fax == '1') $pub_fax_check = ' checked';
if ($pub_web == '1') $pub_web_check = ' checked';

if ($liability_statement != 1) $liability_statement = '0';
if ($organic_cert != '1') $organic_cert = '0';
if ($pub_address != '1') $pub_address = '0';
if ($pub_email != '1') $pub_email = '0';
if ($pub_email2 != '1') $pub_email2 = '0';
if ($pub_phoneh != '1') $pub_phoneh = '0';
if ($pub_phonew != '1') $pub_phonew = '0';
if ($pub_phonec != '1') $pub_phonec = '0';
if ($pub_phonet != '1') $pub_phonet = '0';
if ($pub_fax != '1') $pub_fax = '0';
if ($pub_web != '1') $pub_web = '0';



////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//                          DISPLAY THE INPUT FORM                            //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////

$display_form_top .= '
  <h1>'.date('Y').' '.SITE_NAME.'<br>Producer Registration</h1/><br/>
  <div style="margin:auto;width:90%;padding:1em;">';

$display_form_text .= '
  Member ID:     '.$member_id.'
  Producer ID:   '.$producer_id.'
  Business Name: '.$business_name.'
  Website:       '.$website.'

  The following checked items will be displayed on the site
    ['.strtr ($pub_address, " 01", "  X").'] Publish Home Address
    ['.strtr ($pub_email, " 01", "  X").'] Publish Email Address
    ['.strtr ($pub_email2, " 01", "  X").'] Publish Email Address 2
    ['.strtr ($pub_phoneh, " 01", "  X").'] Publish Home Phone No.
    ['.strtr ($pub_phonew, " 01", "  X").'] Publish Work Phone No.
    ['.strtr ($pub_phonec, " 01", "  X").'] Publish Mobile Phone No.
    ['.strtr ($pub_phonet, " 01", "  X").'] Publish Toll-free Phone No.
    ['.strtr ($pub_fax, " 01", "  X").'] Publish FAX No.
    ['.strtr ($pub_web, " 01", "  X").'] Publish Web Page

  Product Types:
    '.str_replace ("\n", "\n    ", wordwrap($producttypes, 71, "\n", true)).'

  About Us:
    '.str_replace ("\n", "\n    ", wordwrap($about, 71, "\n", true)).'

  Ingredients:
    '.str_replace ("\n", "\n    ", wordwrap($ingredients, 71, "\n", true)).'

  Practices:
    '.str_replace ("\n", "\n    ", wordwrap($general_practices, 71, "\n", true)).'

  Additional Information:
    '.str_replace ("\n", "\n    ", wordwrap($additional, 71, "\n", true)).'

  Highlights This Month:
    '.str_replace ("\n", "\n    ", wordwrap($highlights, 71, "\n", true)).'

  Products:
    '.str_replace ("\n", "\n    ", wordwrap($products, 71, "\n", true)).'

  Practices:
    '.str_replace ("\n", "\n    ", wordwrap($practices, 71, "\n", true)).'

  Pest Management:
    '.str_replace ("\n", "\n    ", wordwrap($pest_management, 71, "\n", true)).'

  Productivity Management:
    '.str_replace ("\n", "\n    ", wordwrap($productivity_management, 71, "\n", true)).'

  Feeding Practices:
    '.str_replace ("\n", "\n    ", wordwrap($feeding_practices, 71, "\n", true)).'

  Soil Management:
    '.str_replace ("\n", "\n    ", wordwrap($soil_management, 71, "\n", true)).'

  Water Management:
    '.str_replace ("\n", "\n    ", wordwrap($water_management, 71, "\n", true)).'

  Land Practices:
    '.str_replace ("\n", "\n    ", wordwrap($land_practices, 71, "\n", true)).'

  Additional Information:
    '.str_replace ("\n", "\n    ", wordwrap($additional_information, 71, "\n", true)).'

  Insurance, Licenses, and Tests:
    '.str_replace ("\n", "\n    ", wordwrap($licenses_insurance, 71, "\n", true)).'

  Organic Products:
    '.str_replace ("\n", "\n    ", wordwrap($organic_products, 71, "\n", true)).'

  Organic Certifying Agency:
    '.str_replace ("\n", "\n    ", wordwrap($certifying_agency, 71, "\n", true)).'

  Certifying Agency Phone:
    '.str_replace ("\n", "\n    ", wordwrap($agency_phone, 71, "\n", true)).'

  Certifying Agency FAX:
    '.str_replace ("\n", "\n    ", wordwrap($agency_fax, 71, "\n", true)).'

  ['.strtr ($organic_cert, " 01", "  X").'] I have available for inspection a copy of your current organic
      certificate.

  ['.strtr ($liability_statement, " 01", "  X").'] '.str_replace ("\n", "\n      ", wordwrap ('I affirm that all statements made about my farm and products in this application are true, correct and complete and I have given a truthful representation of my operation, practices, and origin of products. I understand that if questions arise about my operation I may be inspected (unannounced) by '.SITE_NAME.'. If I stated my operation is organic, then I am complying with the National Organic Program and will provide upon request a copy of my certification.  I have read all of '.SITE_NAME."'".'s terms of service and fully understand and am willing to comply with them.', 75, "\n", true));

$welcome_message .= '<p><em>Thank you for your interest in becoming a producer member of '.SITE_NAME.'. '.SITE_NAME.' customers and producers are interested in local foods and products produced with sustainable practices that demonstrate good stewardship of the environment. Upon approval this form will register you to sell products within '.SITE_NAME.'.  Please read the <a href="'.TERMS_OF_SERVICE.'" target="_blank">Terms of Service</a>, and then complete the following information and click submit.</em></p>';

$display_form_html .= '<p class="error_message">'.$error_message.'</p>
  <form action="'.$_SERVER['PHP_SELF'].'" name="delivery" method="post">

    <table cellspacing="15" cellpadding="2" width="100%" border="1" align="center">
      <tbody>

      <tr>
        <th class="memberform">Section 1: Credentials and Privacy</th>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>* Member&nbsp;ID:</strong></td>
              <td><input maxlength="6" size="10" name="disabled" value="'.$member_id.'" disabled/><input type="hidden" name="member_id" value="'.$member_id.'"/></td>
              <td class="form_key"><strong>** Producer&nbsp;ID:</strong></td>
              <td><input maxlength="6" size="10" name="producer_id" value="'.$producer_id.'"/></td>
            </tr>
            <tr>
              <td colspan="4">
                * Your member ID should already be filled in.  It may not be changed.<br>
                ** Choose a unique 5-character Producer ID to represent your operation.  May contain letters,
                numbers, dash, dot, or underline but it must be exactly five characters long.</td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Business&nbsp;Name:</strong></td>
              <td><input maxlength="50" size="45" name="business_name" value="'.htmlentities ($business_name, ENT_QUOTES).'"/></td>
            </tr>
            <tr>
              <td class="form_key"><strong>Website:</strong></td>
              <td><input maxlength="50" size="45" name="website" value="'.htmlentities ($website, ENT_QUOTES).'"/></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key" colspan="4">The following privacy settings affect which of your
                membership information will be displayed publicly on your producer page.</td>
            </tr>
            <tr>
              <td align="right"><input type="checkbox" name="pub_address" value="1" '.$pub_address_check.'/></td>
              <td colspan="3" class="form_key"><strong>Publish Home Address</strong></td>
            </tr>
              <td align="right"><input type="checkbox" name="pub_email" value="1" '.$pub_email_check.'/></td>
              <td class="form_key"><strong>Publish Email Address</strong></td>
              <td align="right"><input type="checkbox" name="pub_email2" value="1" '.$pub_email2_check.'/></td>
              <td class="form_key"><strong>Publish Email Address 2</strong></td>
            <tr>
            </tr>
              <td align="right"><input type="checkbox" name="pub_phoneh" value="1" '.$pub_phoneh_check.'/></td>
              <td class="form_key"><strong>Publish Home Phone No.</strong></td>
              <td align="right"><input type="checkbox" name="pub_phonew" value="1" '.$pub_phonew_check.'/></td>
              <td class="form_key"><strong>Publish Work Phone No.</strong></td>
            <tr>
            </tr>
              <td align="right"><input type="checkbox" name="pub_phonec" value="1" '.$pub_phonec_check.'/></td>
              <td class="form_key"><strong>Publish Mobile Phone No.</strong></td>
              <td align="right"><input type="checkbox" name="pub_phonet" value="1" '.$pub_phonet_check.'/></td>
              <td class="form_key"><strong>Publish Toll-free Phone No.</strong></td>
            <tr>
            </tr>
              <td align="right"><input type="checkbox" name="pub_fax" value="1" '.$pub_fax_check.'/></td>
              <td class="form_key"><strong>Publish FAX No.</strong></td>
              <td align="right"><input type="checkbox" name="pub_web" value="1" '.$pub_web_check.'/></td>
              <td class="form_key"><strong>Publish Web Page</strong></td>
            <tr>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <th class="memberform">Section 2: General Producer Information</th>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Product Types:</strong><br>
                List keywords like lettuce, berries, buffalo, soap, etc.</td>
            </tr>
            <tr>
              <td><textarea cols="80" rows="5" name="producttypes">'.htmlentities ($producttypes, ENT_QUOTES).'</textarea></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>About Us:</strong><br>
                Use this space to describe your business, you, how you got started, etc.</td>
            </tr>
            <tr>
              <td><textarea cols="80" rows="5" name="about">'.htmlentities ($about, ENT_QUOTES).'</textarea></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Ingredients:</strong><br>
                Use this space to outline ingredients if relevant.</td>
            </tr>
            <tr>
              <td><textarea cols="80" rows="5" name="ingredients">'.htmlentities ($ingredients, ENT_QUOTES).'</textarea></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Practices:</strong><br>
                Use this space to describe your standards and practices. For example, if you use all
                natural products, etc.</td>
            </tr>
            <tr>
              <td><textarea cols="80" rows="5" name="general_practices">'.htmlentities ($general_practices, ENT_QUOTES).'</textarea></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Additional Information:</strong><br>
              Use this space for anything that is not covered in these other sections.</td>
            </tr>
            <tr>
              <td><textarea cols="80" rows="5" name="additional">'.htmlentities ($additional, ENT_QUOTES).'</textarea></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Highlights This Month:</strong><br>
                Use this section for notes that are relevant to the current month.</td>
            </tr>
            <tr>
              <td><textarea cols="80" rows="5" name="highlights">'.htmlentities ($highlights, ENT_QUOTES).'</textarea></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <th class="memberform">Section 3: Production Specifics (Producer Questionnaire)</th>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Products:</strong><br>
                List the types of products you intend to sell through '.SITE_NAME.'
                (e.g. meats, grains, jellies, crafts; also note if you have any heritage breeds).</td>
            </tr>
            <tr>
              <td><textarea cols="80" rows="5" name="products">'.htmlentities ($products, ENT_QUOTES).'</textarea></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Practices:</strong><br>
                Describe your farming, processing and/or crafting practices.</td>
            </tr>
            <tr>
              <td><textarea cols="80" rows="5" name="practices">'.htmlentities ($practices, ENT_QUOTES).'</textarea></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Pest Management:</strong><br>
                Describe your pest and disease management system.</td>
            </tr>
            <tr>
              <td><textarea cols="80" rows="5" name="pest_management">'.htmlentities ($pest_management, ENT_QUOTES).'</textarea></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Productivity Management:</strong><br>
                Describe your herd health and productivity management (i.e. do you use any hormones,
                antibiotics, and/or steroids).</td>
            </tr>
            <tr>
              <td><textarea cols="80" rows="5" name="productivity_management">'.htmlentities ($productivity_management, ENT_QUOTES).'</textarea></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Feeding Practices:</strong><br>
                Describe your feeding practices &ndash; grass-fed only, free-range, feed-lot, etc.</td>
            </tr>
            <tr>
              <td><textarea cols="80" rows="5" name="feeding_practices">'.htmlentities ($feeding_practices, ENT_QUOTES).'</textarea></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Soil Management:</strong><br>
                Describe your soil and nutrient management. Do you compost, use fertilizers, green
                manures or animal manures?</td>
            </tr>
            <tr>
              <td><textarea cols="80" rows="5" name="soil_management">'.htmlentities ($soil_management, ENT_QUOTES).'</textarea></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Water Management:</strong><br>
                Describe your water usage practices. If you irrigate, describe how (e.g. deep well,
                surface water, etc.), and explain how you conserve water or use best management practices.
                Describe how you are protecting your water source from contamination/erosion.</td>
            </tr>
            <tr>
              <td><textarea cols="80" rows="5" name="water_management">'.htmlentities ($water_management, ENT_QUOTES).'</textarea></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Land Practices:</strong><br>
                Describe your conservation/land stewardship practices.  E.g. do you plant
                windbreaks, maintain grass waterways, riparian buffers, use green manures
                for wind erosion, plant habitats for birds, improve soil quality, etc.</td>
            </tr>
            <tr>
              <td><textarea cols="80" rows="5" name="land_practices">'.htmlentities ($land_practices, ENT_QUOTES).'</textarea></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Additional Information:</strong><br>
                Describe any additional information and/or sustainable practices about your operation
                that would be helpful to a potential customer in understanding your farm or operation better
                (e.g. if you are raising any heritage animals you might list breeds or list varieties of
                heirloom seeds. List the percentage of local ingredients in your processed items).</td>
            </tr>
            <tr>
              <td><textarea cols="80" rows="5" name="additional_information">'.htmlentities ($additional_information, ENT_QUOTES).'</textarea></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <th class="memberform">Section 4: Certifications</th>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Insurance, Licenses, and Tests:</strong><br>
                List your food liability insurance coverage, both general and product-related, as well as
                any licenses and tests that you have available. (if applicable).  As this is required to
                market products through the Co-op, you will be required to provide copies of the above
                when you receive confirmation of approvale by '.SITE_NAME.'.</td>
            </tr>
            <tr>
              <td><textarea cols="80" rows="5" name="licenses_insurance">'.htmlentities ($licenses_insurance, ENT_QUOTES).'</textarea></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Organic Products:</strong><br>
              List which products you are selling as organic.</td>
            </tr>
            <tr>
              <td><textarea cols="80" rows="5" name="organic_products">'.htmlentities ($organic_products, ENT_QUOTES).'</textarea></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Organic Certifying Agency:</strong><br>
              List orgainic certifying agency&#146;s name and address.</td>
            </tr>
            <tr>
              <td><textarea cols="80" rows="5" name="certifying_agency">'.htmlentities ($certifying_agency, ENT_QUOTES).'</textarea></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Certifying&nbsp;Agency&#146;s&nbsp;Phone:</strong></td>
              <td><input maxlength="20" size="15" name="agency_phone" value="'.htmlentities ($agency_phone, ENT_QUOTES).'"/></td>
              <td class="form_key"><strong>Certifying&nbsp;Agency&#146;s&nbsp;FAX:</strong></td>
              <td><input maxlength="20" size="15" name="agency_fax" value="'.htmlentities ($agency_fax, ENT_QUOTES).'"/></td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td class="form_key"><strong>Do you have available for inspection a copy of your current organic certificate?</strong></td>
              <td><input type="radio" name="organic_cert" value="1"'.$organic_cert_yes.'/> Yes
              &nbsp; &nbsp; &nbsp; <input type="radio" name="organic_cert" value="0"'.$organic_cert_no.'/> No</td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <th class="memberform">Section 5: Terms and Agreement</th>
      </tr>

      <tr>
        <td>
          <table>
            <tr>
              <td>
                I affirm that all statements made about my farm and products in this application are true,
                correct and complete and I have given a truthful representation of my operation, practices,
                and origin of products. I understand that if questions arise about my operation I may be
                inspected (unannounced) by '.SITE_NAME.'. If I stated my operation is organic, then I am
                complying with the National Organic Program and will provide upon request a copy of my
                certification.  I have read all of '.SITE_NAME.'&#146;s <a href="'.TERMS_OF_SERVICE.'" target="_blank">
                terms of service</a> and fully understand and am willing to comply with them.
              </td>
            </tr>
            <tr>
              <td align="center"><input type="checkbox" name="liability_statement" value="1"'.$liability_statement_check.'/> I agree</td>
            </tr>
          </table>
        </td>
      </tr>
      ';

$display_form_html .= '
      <tr>
        <td align="center">
          <input type="submit" name="action" value="Submit"/>
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
    // Everything validates correctly so do the INSERT and send the EMAIL

    // Do the database insert with the relevant data (producers table)
    $query = '
      UPDATE
        '.TABLE_MEMBER.'
      SET
        auth_type = CONCAT_WS(",", auth_type, "producer"),
        business_name = "'.mysql_escape_string ($business_name).'"
      WHERE
        member_id = "'.mysql_escape_string ($member_id).'"';

    $result = @mysql_query($query,$connection) or die("You found a bug. <b>Error:</b>
        Member insert Query " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    
    // Do the database insert with the relevant data (producers table)
    $query = '
      INSERT INTO
        '.TABLE_PRODUCER.'
      SET
        member_id = '.mysql_escape_string ($member_id).',
        producer_id = "'.mysql_escape_string ($producer_id).'",
        pending = "'.NEW_PRODUCER_PENDING.'",
        donotlist_producer = "1",
        producttypes = "'.mysql_escape_string ($producttypes).'",
        about = "'.mysql_escape_string ($about).'",
        ingredients = "'.mysql_escape_string ($ingredients).'",
        general_practices = "'.mysql_escape_string ($general_practices).'",
        additional = "'.mysql_escape_string ($additional).'",
        highlights = "'.mysql_escape_string ($highlights).'",
        liability_statement = "'.mysql_escape_string ($liability_statement).'",
        pub_address = "'.mysql_escape_string ($pub_address).'",
        pub_email = "'.mysql_escape_string ($pub_email).'",
        pub_email2 = "'.mysql_escape_string ($pub_email2).'",
        pub_phoneh = "'.mysql_escape_string ($pub_phoneh).'",
        pub_phonew = "'.mysql_escape_string ($pub_phonew).'",
        pub_phonec = "'.mysql_escape_string ($pub_phonec).'",
        pub_phonet = "'.mysql_escape_string ($pub_phonet).'",
        pub_fax = "'.mysql_escape_string ($pub_fax).'",
        pub_web = "'.mysql_escape_string ($pub_web).'"';

    $result = @mysql_query($query,$connection) or die("You found a bug. <b>Error:</b>
        Member insert Query " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());

    // Do the database insert with the relevant data (producers_registration table)
    $query = '
      INSERT INTO
        '.TABLE_PRODUCER_REG.'
      SET
        member_id = '.mysql_escape_string ($member_id).',
        producer_id = "'.mysql_escape_string ($producer_id).'",
        business_name = "'.mysql_escape_string ($business_name).'",
        website = "'.mysql_escape_string ($website).'",
        date_added = now(),
        products = "'.mysql_escape_string ($products).'",
        practices = "'.mysql_escape_string ($practices).'",
        pest_management = "'.mysql_escape_string ($pest_management).'",
        productivity_management = "'.mysql_escape_string ($productivity_management).'",
        feeding_practices = "'.mysql_escape_string ($feeding_practices).'",
        soil_management = "'.mysql_escape_string ($soil_management).'",
        water_management = "'.mysql_escape_string ($water_management).'",
        land_practices = "'.mysql_escape_string ($land_practices).'",
        additional_information = "'.mysql_escape_string ($additional_information).'",
        licenses_insurance = "'.mysql_escape_string ($licenses_insurance).'",
        organic_products = "'.mysql_escape_string ($organic_products).'",
        certifying_agency = "'.mysql_escape_string ($certifying_agency).'",
        agency_phone = "'.mysql_escape_string ($agency_phone).'",
        agency_fax = "'.mysql_escape_string ($agency_fax).'",
        organic_cert = "'.mysql_escape_string ($organic_cert).'"';

    $result = @mysql_query($query,$connection) or die("You found a bug. <b>Error:</b>
        Member insert Query " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());

    // Set the session variable so the member has immediate access to producer functions
    $_SESSION['producer_id_you'] = mysql_escape_string ($producer_id);

    // Get the producer's email address so we can send them a notification
    $query = '
      SELECT
        email_address
      FROM
        '.TABLE_MEMBER.'
      WHERE member_id = "'.mysql_real_escape_string ($member_id).'"';
    $sql =  @mysql_query($query, $connection) or die("You found a bug. <b>Error:</b> Check for existing member query " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    if ($row = mysql_fetch_object($sql))
      {
        $email_address = $row->email_address;
      }

    // Now send email notification(s)
    $email_to = preg_replace ('/SELF/', $email_address, PRODUCER_FORM_EMAIL);
    $email_subject = 'New producer: Welcome to '.SITE_NAME;
    $boundary = uniqid();
    // Set up the email preamble...
    $email_preamble = '<p>Following is a copy of the producer information you submitted to '.SITE_NAME.'.</p>';
    $email_preamble .= $welcome_message;

    // Disable all form elements for emailing
    $html_version = preg_replace ('/<(input|select|textarea)/', '<\1 disabled', $welcome_message.$display_form_html);

    $email_headers  = "From: ".STANDARDS_EMAIL."\r\n";
    $email_headers .= "Reply-To: ".STANDARDS_EMAIL."\r\n";
    $email_headers .= "Errors-To: web@".DOMAIN_NAME."\r\n";
    $email_headers .= "MIME-Version: 1.0\r\n";
    $email_headers .= "Content-type: multipart/alternative; boundary=\"$boundary\"\r\n";
    $email_headers .= "Message-ID: <".md5(uniqid(time()))."@".DOMAIN_NAME.">\r\n";
    $email_headers .= "X-Mailer: PHP ".phpversion()."\r\n";
    $email_headers .= "X-Priority: 3\r\n";
    $email_headers .= "X-AntiAbuse: This is a machine-generated response to a user-submitted form at ".SITE_NAME.".\r\n";

    $email_body .= "\r\n--".$boundary;
    $email_body .= "\r\nContent-Type: text/plain; charset=us-ascii";
    $email_body .= "\r\n\r\n".wordwrap (strip_tags ($welcome_message), 75, "\n", true)."\n".$display_form_text;
    $email_body .= "\r\n--".$boundary;
    $email_body .= "\r\nContent-Type: text/html; charset=us-ascii";
    $email_body .= "\r\n\r\n--".$html_version;
    $email_body .= "\r\n--".$boundary.'--';

    mail ($email_to, $email_subject, $email_body, $email_headers);

    $file = fopen (FILE_PATH.'/public_html/shop/producers/'.strtolower($producer_id).".php", "w");

    if($file)
      {
        $filetext = "<?php include('../template_prdcr.php'); ?>";
        fwrite ($file, $filetext);
        $display_form_html = '<p class="error_message">Producer information has been accepted.</p>';
      }
    else
      {
        $display_form_html = '<p class="error_message">Producer information was accepted but there
          was an error creating the producer file</p>';
      }


     $display_form_message .= '
      <p class="message">Your producer application will be reviewed by an administrator and you will be notified when
      it becomes active.  Until then, you will not have producer access or be able to enter products into the system.</p>';
  }



include ("template_hdr_orders.php");
echo $display_form_top;
echo $display_form_message;
echo $display_form_html;
include("template_footer_orders.php");



