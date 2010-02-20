<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();
$message = "";
if ( $deliveryupdate_submitted == "yep" )
  {
    // If auth_type is rtemanager and not administrator then do the update
    if ( strpos ($_SESSION['auth_type'], 'rtemanager') !== false || strpos ($_SESSION['auth_type'], 'administrator') !== false )
      {
        $sqlu = '
          UPDATE
            '.TABLE_DELCODE.'
          SET
            deldesc = "'.$deldesc.'"
          WHERE
            delcode_id = "'.$delcode_id.'"';
        $resultu = @mysql_query($sqlu,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
        $message = ": <font color=\"#FFFFFF\">Delivery Information Updated</font>";
      }
    else
      {
        $message = ': <font color="#FFFFFF">You can only update the route you manage</font>';
      }
  }
elseif ( $routeupdate_submitted == 'yes' )
  {
    if ( $admin_member_id == $rtemgr_member_id || strpos ($_SESSION['auth_type'], 'administrator') !== false )
      {
        $sqlu2 = '
          UPDATE
            '.TABLE_ROUTE.'
          SET
            route_desc = "'.$route_desc.'"
          WHERE
            route_id = "'.$route_id.'"';
        $resultu2 = @mysql_query($sqlu2,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
        $message = ': <font color="#FFFFFF">Route Information Updated</font>';
      }
    else
      {
        $message = ': <font color="#FFFFFF">You can only update the route you manage</font>';
      }
  }
$sqlr = '
  SELECT
    '.TABLE_ROUTE.'.route_id,
    '.TABLE_ROUTE.'.route_name,
    '.TABLE_ROUTE.'.route_desc,
    '.TABLE_ROUTE.'.rtemgr_member_id,
    '.TABLE_ROUTE.'.rtemgr_namecd,
    '.TABLE_DELCODE.'.hub,
    '.TABLE_MEMBER.'.member_id,
    '.TABLE_MEMBER.'.first_name,
    '.TABLE_MEMBER.'.last_name,
    '.TABLE_MEMBER.'.first_name_2,
    '.TABLE_MEMBER.'.last_name_2,
    '.TABLE_MEMBER.'.email_address,
    '.TABLE_MEMBER.'.email_address_2
  FROM
    '.TABLE_ROUTE.',
    '.TABLE_DELCODE.',
    '.TABLE_MEMBER.'
  WHERE
    '.TABLE_ROUTE.'.route_id = '.TABLE_DELCODE.'.route_id
    AND '.TABLE_ROUTE.'.rtemgr_member_id = '.TABLE_MEMBER.'.member_id
  GROUP BY
    '.TABLE_ROUTE.'.route_id
  ORDER BY
    '.TABLE_DELCODE.'.hub ASC,
    '.TABLE_ROUTE.'.route_name ASC';
$rsr = @mysql_query($sqlr,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
while ( $row = mysql_fetch_array($rsr) )
  {
    $route_id = $row['route_id'];
    $route_name = $row['route_name'];
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $first_name_2 = $row['first_name_2'];
    $last_name_2 = $row['last_name_2'];
    $email_address = $row['email_address'];
    $email_address_2 = $row['email_address_2'];
    $rtemgr_member_id = $row['rtemgr_member_id'];
    $rtemgr_namecd = $row['rtemgr_namecd'];
    $route_desc = $row['route_desc'];
    if ( $rtemgr_namecd == 'F' )
      {
        $route_manager = '<b>'.$first_name.' '.$last_name.'</b><br><a href="mailto:'.$email_address.'">'.$email_address.'</a>';
      }
    elseif ( $rtemgr_namecd == 'S' )
      {
        if ( $email_address_2 )
          {
            $route_manager = '<b>'.$first_name_2.' '.$last_name_2.'</b><br><a href="mailto:'.$email_address_2.'">'.$email_address_2.'</a>';
          }
        else
          {
            $route_manager = '<b>'.$first_name_2.' '.$last_name_2.'</b><br><a href="mailto:'.$email_address.'">'.$email_address.'</a>';
          }
      }
    elseif ( $rtemgr_namecd == 'B' )
      {
        if ( $email_address_2 )
          {
            $route_manager = '
              <b>'.$first_name.' '.$last_name.'</b><br><a href="mailto:'.$email_address.'">'.$email_address.'</a><br>
              <b>'.$first_name_2.' '.$last_name_2.'</b><br><a href="mailto:'.$email_address_2.'">'.$email_address_2.'</a>';
          }
        else
          {
            $route_manager = '
              <b>'.$first_name.' '.$last_name.'</b><br>
              <b>'.$first_name_2.' '.$last_name_2.'</b><br>
              <a href="mailto:'.$email_address.'">'.$email_address.'</a><br>';
          }
      }
    $quick_links .= '<a href="#'.$route_id.'">'.$route_name.'</a> &nbsp;&nbsp;&nbsp;';
    $display .= '
      <tr bgcolor="#AEDE86"><td colspan="2" align="left">
      <a name="'.$route_id.'">
      <font size="3"><b>Route: '.$route_name.'</b></font></td></tr>';
    $display .= '
      <tr bgcolor="#DDEECC"><td align="left" valign="top">
      <b>Route Manager:</b><br>'.$route_manager.'
      </td><td align="left">
      <form action="'.$PHP_SELF.'" method="post">
      <textarea name="route_desc" cols="53" rows="3">'.$route_desc.'</textarea><br>
      <input type="hidden" name="route_id" value="'.$route_id.'">
      <input type="hidden" name="rtemgr_member_id" value="'.$rtemgr_member_id.'">
      <input type="hidden" name="routeupdate_submitted" value="yes">
      <div align="right">
      <input type="submit" name="where" value="Save changes to this description">
      </div>
      </form>
      </td></tr>';
    $sqlr2 = '
      SELECT
        '.TABLE_DELCODE.'.delcode_id,
        '.TABLE_DELCODE.'.delcode,
        '.TABLE_DELCODE.'.deldesc,
        '.TABLE_DELCODE.'.route_id,
        '.TABLE_DELCODE.'.hub,
        '.TABLE_DELCODE.'.deltype,
        '.TABLE_DELCODE.'.truck_code,
        '.TABLE_DELCODE.'.delcharge,
        '.TABLE_DELCODE.'.transcharge
        FROM
        '.TABLE_DELCODE.'
        WHERE
        route_id = "'.$route_id.'"
        GROUP BY delcode_id
        ORDER BY deltype DESC,
        delcode ASC';
    $rsr2 = @mysql_query($sqlr2,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    $num_del = mysql_numrows($rsr2);
    while ( $row = mysql_fetch_array($rsr2) )
      {
        $delcode_id = $row['delcode_id'];
        $delcode = $row['delcode'];
        $deltype = $row['deltype'];
        $truck_code = $row['truck_code'];
        $delcharge = number_format($row['delcharge'], 2);
        $transcharge = number_format($row['transcharge'],2);
        $deldesc = $row['deldesc'];
        $hub = $row['hub'];
        if ( $deltype == 'P' )
          {
            $deltype_long = '(Pickup)';
          }
        elseif ( $deltype == 'D' )
          {
            $deltype_long = '(Home or Work Delivery)';
          }
        $display .= '
          <tr bgcolor="#CCCCCC"><td colspan="2" align="left">
          <b>Delivery Specifics: '.$delcode.' (Hub: '.$hub.')</b></td></tr>';
        $display .= '
          <tr bgcolor="#EEEEEE"><td align="left" valign="top">
          <table>
          <tr><td>Delivery Code ID:</td><td><b>'.$delcode_id.'</b></td></tr>
          <tr><td>Delivery Type:</td><td><b> '.$deltype.'</b> '.$deltype_long.'</td></tr>
          <tr><td>Truck Code:</td><td><b> '.$truck_code.'</b></td></tr>
          <tr><td>Delivery Charge:</td><td><b> $ '.$delcharge.'</b></td></tr>
          <tr><td>Transportation Charge:</td><td><b>$ '.$transcharge.'</b></td></tr>
          </table>
          </td><td align="left">
          <form action="'.$PHP_SELF.'" method="post">
          <textarea name="deldesc" cols="53" rows="8">'.$deldesc.'</textarea><br>
          <input type="hidden" name="delcode_id" value="'.$delcode_id.'">
          <input type="hidden" name="rtemgr_member_id" value="'.$rtemgr_member_id.'">
          <input type="hidden" name="deliveryupdate_submitted" value="yep">
          <div align="right">
          <input type="submit" name="where" value="Save changes to this description">
          </div>
          </form>
          </td></tr>';
      }
    $display .= '
      <tr>
        <td colspan="2"><hr></td>
      </tr>';
  }

include("template_hdr.php");
echo $font;
?>
  <!-- CONTENT BEGINS HERE -->
<div align="center">
<h3>Editing Delivery and Pick up Route Information</h3>
<table width="685" cellpadding="7" cellspacing="2" border="0">
  <tr bgcolor="#AE58DA">
    <td colspan="2" align="left"><b>Delivery and Pick up Route Information</b> <?php echo $message;?></td>
  </tr>
  <tr>
    <td colspan="2" align="left" bgcolor="#EEEEEE">
      <ul>
        Click to view the full public <a href="<?php echo LOCATIONS_PAGE;?>" target="_blank">list of locations</a>.<br><br>
        View <a href="delivery.php">route information for this delivery cycle</a>.<br><br>
        Email <?php echo WEBMASTER_EMAIL;?> if you need an additional delivery or pickup code.
      </ul>
      <b>Quick Links to Routes:</b><br>
      <div align="center"><?php echo $quick_links;?></div>
    </td>
  </tr>
  <tr>
    <td colspan="2"><hr></td>
  </tr>
  <?php echo $display;?>
</table>
</div>
<br><br>
  <!-- CONTENT ENDS HERE -->
<?php include("template_footer.php");?>
