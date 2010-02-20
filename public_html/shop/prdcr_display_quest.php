<?php
include_once ('config_foodcoop.php');

$page_name = $PHP_SELF;
$producer_id = $_GET['pid'];

include("template_hdr.php");

$query = '
  SELECT
    producers_registration.*
    ,pending, donotlist_producer
  FROM
    producers_registration,
    producers
  WHERE producers_registration.producer_id = "'.$producer_id.'"
    AND producers.producer_id = "'.$producer_id.'"
    AND pending = "0"
    AND donotlist_producer = "0"';
$result = @mysql_query($query,$connection) or die("<br><br>Whoops! You found a bug. If there is an error listed below, please copy and paste the error into an email to <a href=\"mailto:web@$domainname\">web@$domainname</a><br><br><b>Error:</b> Route Query " . mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
while ($row = mysql_fetch_array($result))
  {
    $pid = $row['pid'];
    $producer_id = $row['producer_id'];
    $member_id = $row['member_id'];
    $business_name = $row['business_name'];
    $website = $row['website'];

    $products = $row['products'];
    $practices = $row['practices'];
    $pest_management = $row['pest_management'];
    $productivity_management = $row['productivity_management'];
    $feeding_practices = $row['feeding_practices'];
    $soil_management = $row['soil_management'];
    $water_management = $row['water_management'];
    $land_practices = $row['land_practices'];
    $additional_information = $row['additional_information'];
    $licenses_insurance = $row['licenses_insurance'];
    $organic_products = $row['organic_products'];
    $certifying_agency = $row['certifying_agency'];
    $agency_phone = $row['agency_phone'];
    $agency_fax = $row['agency_fax'];
    $organic_cert = $row['organic_cert'];
    if ($organic_cert == "1")
      {
        $organic_cert_yes = " CHECKED";
        $organic_cert_no = "";
      }
    elseif ($organic_cert == "0")
      {
        $organic_cert_yes = "";
        $organic_cert_no = " CHECKED";
      }
    $date_added = $row['date_added'];
  }
  echo '
  <table class="proddata" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <th colspan="2" align="center" bgcolor="#CFCFA1"><b>PRODUCTS & PRACTICES</b></th>
  </tr>
  <tr>
    <th width="40%" align="center" bgcolor="#CFCFA1">Question</th>
    <th width="60%" align="center" bgcolor="#CFCFA1">Answer</th>
  </tr>

  <tr class="d0">
    <td width="40%"><b>List what you intend to sell.</b> (if applicable)<br>
    (e.g. meats, grains, jellies, crafts; also note if you have any heritage breeds)</td>
    <td width="60%" class="d0">'.stripslashes ($products).'</td>
  </tr>

  <tr class="d1">
    <td width="40%"><b>Describe your farming, processing and/or crafting practices.</b> (if applicable)</td>
    <td width="60%" class="d1">'.stripslashes ($practices).'</td>
  </tr>

  <tr class="d0">
    <td width="40%"><b>Describe your pest and disease management system.</b> (if applicable)</td>
    <td width="60%" class="d0">'.stripslashes ($pest_management).'</td>
  </tr>

  <tr class="d1">
    <td width="40%"><b>Describe your herd health and productivity management.</b> (if applicable)<br>
    (i.e. do you use any hormones, antibiotics, and/or steroids)</td>
    <td width="60%" class="d1">'.stripslashes ($productivity_management).'</td>
  </tr>

  <tr class="d0">
    <td width="40%"><b>Describe your feeding practices.</b> (if applicable)<br>
    (grass-fed only, free-range, feed-lot, etc.)</td>
    <td width="60%" class="d0">'.stripslashes ($feeding_practices).'</td>
  </tr>

  <tr class="d1">
    <td width="40%"><b>Describe your soil and nutrient management.</b> (if applicable)<br>
    (Do you compost, use fertilizers, green manures or animal manures?)</td>
    <td width="60%" class="d1">'.stripslashes ($soil_management).'</td>
  </tr>

  <tr class="d0">
    <td width="40%"><b>Describe your water usage practices.</b> (if applicable)<br>
    (If you irrigate, describe how - e.g. deep well, surface water, etc., and explain how you conserve water or use
    best management practices.  Describe how you are protecting your water source from contamination/erosion).</td>
    <td width="60%" class="d0">'.stripslashes ($water_management).'</td>
  </tr>

  <tr class="d1">
    <td width="40%"><b>Describe your conservation/land stewardship practices.</b> (if applicable)<br>
    (e.g., do you plant windbreaks, maintain grass waterways, riparian buffers, use green manures for wind erosion,
    plant habitats for birds, improve soil quality, etc.)</td>
    <td width="60%" class="d1">'.stripslashes ($land_practices).'</td>
  </tr>

  <tr class="d0">
    <td width="40%"><b>Describe any additional information and/or sustainable practices about your operation that
    would be helpful to a potential customer in understanding your farm or operation better.</b><br>
    (e.g. if you are raising any heritage animals you might list breeds or list varieties of heirloom seeds. List the
    percentage of local ingredients in your processed items).</td>
    <td width="60%" class="d0">'.stripslashes ($additional_information).'</td>
  </tr>

  <tr class="d1">
    <td width="40%"><b>List your food liability insurance coverage, both general and product-related, as well as any
    licenses and tests that you have available.</b> (if applicable)</td>
    <td width="60%" class="d1">'.stripslashes ($licenses_insurance).'</td>
  </tr>
  </table>


  <table class="proddata" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td colspan="4" width="50%" align="center" bgcolor="#CFCFA1"><b>ORGANIC PRODUCERS</b></td>
  </tr>

  <tr class="d0">
    <td colspan="2" width="50%"><b>List which products you are selling as organic.</b> (if applicable)</td>
    <td colspan="2" width="50%" class="d0">'.stripslashes ($organic_products).'</td>
  </tr>

  <tr class="d1">
    <td valign="top" align="right" width="25%"><b>List certifying agency&#39;s name and address:</b><br> (if applicable)</td>
    <td colspan="3" width="75%" class="d1">'.stripslashes ($certifying_agency).'</td>
  </tr>

  <tr class="d0">
    <td align="right" width="25%"><b>Certifying Agency&#39;s Phone:</b><br>(if applicable)</td>
    <td width="25%" class="d0">&nbsp;'.stripslashes ($agency_phone).'&nbsp;</td>
    <td align="right" width="25%"><b>Certifying Agency&#39;s Fax:</b><br>(if applicable)</td>
    <td width="25%" class="d0">&nbsp;'.stripslashes ($agency_fax).'&nbsp;</td>
  </tr>

  <tr class="d1">
    <td colspan="3" align="right" width="75%"><b>Do you have available for inspection a copy of your current organic certificate?</b> (if applicable)</td>
    <td width="25%" class="d1">
      <input type="radio" name="organic_cert"'.$organic_cert_yes.' DISABLED> Yes &nbsp;&nbsp;
      <input type="radio" name="organic_cert"'.$organic_cert_no.' DISABLED> No
    </td>
  </tr>

  <tr class="d0">
    <td colspan="4" width="50%">
      I affirm that all statements made about my farm and products in this application are true, correct and complete
      and I have given a truthful representation of my operation, practices, and origin of products.  I understand
      that if questions arise about my operation I may be inspected (unannounced).  If I stated my operation
      is organic, then I am complying with the National Organic Program and will provide upon request a copy of my
      certification.  I have read all of the producer standards and fully understand and am willing to comply
      with them.</td>
  </tr>

  <tr class="d00">
    <td colspan="2" class="d0">
      <div align="center">
        <input type="checkbox" name="affirmation1" value="1" CHECKED DISABLED> <b>I agree</b>
      </div
    </td>
    <td align="center" colspan="2" class="d0">'.date ('l \t\h\e jS \d\a\y \o\f F, Y', strtotime ($date_added)).'</td>
  </tr>
  </table>'
?>
<div align="right"><a href="<?echo $main_url;?>/shop/coopproducers.php">Back to producers list</a></div>
</blockquote>

<?include("template_footer.php");?>
