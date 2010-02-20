<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();

include("../func/show_name.php");
include("../func/mem_contact_info_single.php");
include("template_hdr_orders.php");
?>

<div align="center">
  <h3>Your Contact Information</h3>
  <table width="60%">
    <tr>
      <td align="left"><?php echo $font;?>

        <?php echo $show_name;?> (Member ID: <?php echo $member_id;?>)<br>
        <?php echo $display;?>

        <?php echo "If any of this information needs to be updated or added to,<br>
        please email <a href=\"mailto:".MEMBERSHIP_EMAIL."?subject=CoopMemberUpdate_#$member_id\">".MEMBERSHIP_EMAIL."</a><br>
        with the new information and your Member ID ($member_id).";?>

      </td>
    </tr>
  </table>
</div>

<?php include("template_footer_orders.php");?>