<?php
include_once ('config_foodcoop.php');


include("template_hdr.php");
echo $font;
?>

<!-- CONTENT BEGINS HERE -->
<div align="center">
  <h3>Contacting the food <? echo ORGANIZATION_TYPE; ?></h3>
  <b>Please first check the "<a href="faq.php">How to Order & FAQ</a>" page to see if your question has already been addressed.<br>Thank you for your involvement in the food <? echo ORGANIZATION_TYPE; ?>.</b>
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
        Problems with your order from delivery day?
      </td>
      <td align="left">
        <a href="mailto:<?php echo PROBLEMS_EMAIL;?>"><?php echo PROBLEMS_EMAIL;?></a>
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
        If you&#146;re a producer with limited online access, send product updates to:
      </td>
      <td align="left">
        <a href="mailto:<?php echo ORDER_EMAIL;?>"><?php echo ORDER_EMAIL;?></a>
      </td>
    </tr>
    <tr>
      <td align="left">
        If you&#146;re a producer with a new product:
      </td>
      <td align="left">
        <a href="mailto:<?php echo STANDARDS_EMAIL;?>"><?php echo STANDARDS_EMAIL;?></a>
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
        <a href="mailto:<?php echo CUSTOMER_EMAIL;?>"><?php echo CUSTOMER_EMAIL;?></a>
      </td>
    </tr>
  </table>
</div>

<!-- CONTENT ENDS HERE -->

<?php include("template_footer.php");?>
