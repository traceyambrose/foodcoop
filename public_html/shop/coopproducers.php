<?php
include_once ('config_foodcoop.php');


$color1 = "#DDDDDD";
$color2 = "#CCCCCC";
$row_count = 0;

$sqlr = '
  SELECT
    '.TABLE_PRODUCER.'.*,
    '.TABLE_MEMBER.'.*
  FROM
    '.TABLE_PRODUCER.',
    '.TABLE_MEMBER.'
  WHERE
    '.TABLE_PRODUCER.'.member_id = '.TABLE_MEMBER.'.member_id
    AND '.TABLE_PRODUCER.'.donotlist_producer != "1"
    AND '.TABLE_PRODUCER.'.pending = "0"
  ORDER BY
    '.TABLE_MEMBER.'.business_name ASC';
$rsr = @mysql_query($sqlr,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
while ($row = mysql_fetch_array($rsr) )
  {
    $producer_id = $row['producer_id'];
    $business_name = stripslashes($row['business_name']);
    $producttypes = stripslashes($row['producttypes']);

    $first_name = stripslashes($row['first_name']);
    $last_name = stripslashes($row['last_name']);
    $first_name_2 = stripslashes($row['first_name_2']);
    $last_name_2 = stripslashes($row['last_name_2']);

    $show_name = '';

    include("func/show_name.php");

    $producer_id_lower = strtolower($producer_id);

    $row_color = ($row_count % 2) ? $color1 : $color2;

    $display_top .= '
      <tr bgcolor="'.$row_color.'">
        <td width="25%"><font face="arial" size="3"><b><a href="'.PATH."producers/".$producer_id_lower.'.php">'.$business_name.'</a></b></td>
        <td width="75%">'.$producttypes.'</font></td>
      </tr>';

    $row_count++;
  }

?>

  <!-- CONTENT BEGINS HERE -->
<?php include("template_hdr.php");?>

<a name="top">
<font face="arial">

<font size=5><b>Food <? echo ucfirst (ORGANIZATION_TYPE); ?> Producer Members</b></font><br>

For prices for the products sold by these producers through our <? echo ORGANIZATION_TYPE; ?>, visit <a href="<?php echo BASE_URL;?>"><?php echo BASE_URL;?></a>.
<br><br>
Not from this region? Don&#146;t despair. Many of these producers are ready and able to ship their products to you, including frozen meats! Please contact the producers directly about the shipping policies.
<br><br>

<table cellpadding="2" cellspacing="2" border="0">
  <tr bgcolor="#AEDE86">
    <td><b>Producer Name</b> (Click on name)</td><td><b>Types of Products Available for Sale</b></td>
  </tr>
  <?php echo $display_top;?>
</table>


  <!-- CONTENT ENDS HERE -->

<?php include("template_footer.php");?>