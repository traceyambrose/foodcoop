<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();

include("template_hdr_orders.php");
echo $font;?>
  <!-- CONTENT BEGINS HERE -->
<div align="center">
  <h3>Contacting the <?php echo SITE_NAME;?></h3>

  <b>Please first check the "<a href="faq.php">How to Order & FAQ</a>" page to see if your question has already been addressed.<br>Thank you for your involvement in the food <?php echo ORGANIZATION_TYPE; ?>.</b>
  <br><br>
  <table>
    <tr>
      <td align="left">
        Questions about ordering online?
      </td>
      <td align="left">
        <a href="mailto:<?php echo HELP_EMAIL;?>"><?php echo HELP_EMAIL;?></a>
      </td>
    </tr>
    <tr>
      <td align="left">
        Questions about ordering in general?
      </td>
      <td align="left">
        <a href="mailto:<?php echo ORDER_EMAIL;?>"><?php echo ORDER_EMAIL;?></a>
      </td>
    </tr>
    <tr>
      <td align="left">
        Questions about your payment?
      </td>
      <td align="left">
        <a href="mailto:<?php echo TREASURER_EMAIL;?>"><?php echo TREASURER_EMAIL;?></a>
      </td>
    </tr>
    <tr>
      <td align="left">
        Questions about your Membership?
      </td>
      <td align="left">
        <a href="mailto:<?php echo MEMBERSHIP_EMAIL;?>"><?php echo MEMBERSHIP_EMAIL;?></a>
      </td>
    </tr>
    <tr>
      <td align="left">
        If you are a producer, send product updates to:
      </td>
      <td align="left">
        <a href="mailto:<?php echo PRICELIST_EMAIL;?>"><?php echo PRICELIST_EMAIL;?></a>
      </td>
    </tr>
    <tr>
      <td align="left">
        Questions about the website?
      </td>
      <td align="left">
        <a href="mailto:<?php echo WEBMASTER_EMAIL;?>"><?php echo WEBMASTER_EMAIL;?></a>
      </td>
    </tr>
    <tr>
      <td align="left">
        General Information
      </td>
      <td align="left">
        <a href="mailto:<?php echo GENERAL_EMAIL;?>"><?php echo GENERAL_EMAIL;?></a>
      </td>
    </tr>
  </table>
</div>

  <!-- CONTENT ENDS HERE -->

<?php include("template_footer_orders.php");?>