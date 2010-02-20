<?php
include_once ('config_foodcoop.php');

$sqlr = '
  SELECT
    route_id,
    route_name,
    route_desc
  FROM
    '.TABLE_ROUTE.'
  ORDER BY
    route_name ASC';
$rsr = @mysql_query($sqlr,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
while ( $row = mysql_fetch_array($rsr) )
  {
    $route_id = $row['route_id'];
    $route_name = $row['route_name'];
    $route_desc = $row['route_desc'];


    $sqlr2 = '
      SELECT
        *
      FROM
        '.TABLE_DELCODE.'
      WHERE
        route_id = '.$route_id.'
        AND inactive != 1
      GROUP BY
        delcode_id
      ORDER BY
        deltype DESC,
        delcode ASC';

    $rsr2 = @mysql_query($sqlr2,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
    $num_del = mysql_numrows($rsr2);
    while ( $row = mysql_fetch_array($rsr2) )
      {
        $delcode_id = $row['delcode_id'];
        $delcode = $row['delcode'];
        $deldesc = $row['deldesc'];
        $delcharge = $row['delcharge'];
        $transcharge = $row['transcharge'];
        $hub = $row['hub'];

        $quicklinks .= '
          <li> '.$route_name.': <a href="#'.$delcode_id.'">'.$delcode.'</a></li>';

        if ($route_id != $route_id_prev )
          {
            $display .= '
              <tr>
                <td align="left" colspan="3" bgcolor="#EDF3FC">
                  <a name="'.$delcode_id.'"></a>
                  <font size="5"><b>Route: '.$route_name.'</b></font><br>(Hub: '.$hub.') '.$route_desc.'
                </td>
              </tr>';
          }

        //$display_charge .= "Transportation Charge: \$".number_format($transcharge, 2)."";
        if ( $delcharge )
          {
            $display_charge .= "Delivery Charge: \$".number_format($delcharge, 2)."";
          }

        $display .= '
              <tr>
                <td bgcolor=#FFFFFF><font color=#FFFFFF>.</font></td>
                <td align="left" valign=top><a name="'.$delcode_id.'"></a><font size=4><b>'.$delcode.'</b></font></td>
                <td>'.$display_charge.' </td>
              </tr>
              <tr>
                <td bgcolor=#FFFFFF><font color=#FFFFFF>.</font></td>
                <td align="left" valign=top colspan=2>'.nl2br ($deldesc).'<br><br></td>
              </tr>';
        $display_charge = '';
        $route_id_prev = $route_id;
      }
    $display .= '
              <tr>
                <td><br></td>
              </tr>';
  }
?>

  <!-- CONTENT BEGINS HERE -->
<?php
include("template_hdr.php");

$title = SITE_NAME." Delivery & Pickup Locations and Hours";

$display_block = "
<font size=5><b>".SITE_NAME." Delivery & Pickup Locations and Hours</b></font><br>
<table bgcolor='#FFFFFF' cellspacing='0' cellpadding='2' border='0' width='90%'>

Note: If you don't see your town listed here, please contact <a href='mailto:".GENERAL_EMAIL."'>".GENERAL_EMAIL."</a>.  We are adding new routes all the time and if there is interest in a particular location, we may be able to add it.
<br>
Quick Links:
<ul>
$quicklinks
</ul>
<br><br>

$display

</table>
";

echo $display_block;

include("template_footer.php");?>