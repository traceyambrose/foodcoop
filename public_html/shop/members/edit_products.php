<?php
$user_type = 'valid_m';
include_once ('config_foodcoop.php');
session_start();
validate_user();
$date_today = date("F j, Y");

// If not auth_type = administrator, then set producer_id to self
if ( $producer_id_you && strpos ($_SESSION['auth_type'], 'administrator') === false )
  {
    $producer_id = $producer_id_you;
  }
$action = "edit";
include("../func/edit_product_screen.php");

include("template_hdr_orders.php");

include("../func/javascript_popup.php");
?>

  <!-- CONTENT BEGINS HERE -->

<div align="center">
  <table width="80%">
    <tr>
      <td align="left">

        <font color="#770000" size=5><b><?php echo $business_name;?> Product # <?php echo $product_id;?></b></font><br><br>

        <?php
        echo $help;

if ($message2)
  {
    echo '<div style="border:1px solid red;background:#ffeeee;padding:3px;color:#ff0000;overflow:auto;"><h1 style="font-size:4em;float:left;margin:0px 12px 0px 0px;">!</h1><br>'.$message2.'</div>';
  };

        echo $display;

        ?>

        <br><br>


      </td>
    </tr>
  </table>
</div>
  <!-- CONTENT ENDS HERE -->

<?php include("template_footer_orders_edit.php");?>