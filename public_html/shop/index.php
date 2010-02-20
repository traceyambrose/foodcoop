<?php
include_once ('config_foodcoop.php');

$sql3 = '
  SELECT
    DATE_FORMAT(date_closed, "%M")          AS month,
    DATE_FORMAT(date_open, "%b %d, %Y")     AS date_open,
    DATE_FORMAT(date_closed, "%b %d, %Y")   AS date_closed,
    DATE_FORMAT(delivery_date, "%b %d, %Y") AS delivery_date
  FROM
    '.TABLE_CURDEL;

$result3 = @mysql_query($sql3, $connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
while ( $row = mysql_fetch_array($result3) )
  {
    $month = $row['month'];
    $date_open = str_replace (' ', '&nbsp;', $row['date_open']);
    $date_closed = str_replace (' ', '&nbsp;', $row['date_closed']);
    $delivery_date = str_replace (' ', '&nbsp;', $row['delivery_date']);
  }

$cycle = '<font color="#770000"><i>'. $month .'</i></font>';

$sql = '
  SELECT
    '.TABLE_PRODUCT.'.product_id,
    '.TABLE_PRODUCT.'.donotlist,
    '.TABLE_PRODUCT.'.producer_id,
    '.TABLE_PRODUCER.'.producer_id,
    '.TABLE_PRODUCER.'.donotlist_producer
  FROM
    '.TABLE_PRODUCT.',
    '.TABLE_PRODUCER.'
  WHERE
    '.TABLE_PRODUCT.'.donotlist = "0"
    AND '.TABLE_PRODUCT.'.producer_id = '.TABLE_PRODUCER.'.producer_id
    AND '.TABLE_PRODUCER.'.donotlist_producer != "1"
  GROUP BY
    product_id';
$result = @mysql_query($sql,$connection) or die(mysql_error() . "<br><b>Error No: </b>" . mysql_errno());
$prod_count = mysql_numrows($result);
?>

<?php
include("template_hdr.php");
echo $font;
?>

  <!-- CONTENT BEGINS HERE -->
<div align="center">
  <table width="685" cellpadding="3" cellspacing="0" border="0">
    <tr>
      <td align="left"><?php echo $font;?>
        Welcome to the <?php echo SITE_NAME; ?>&#146;s food co-operative website. We will endeavor to provide you with quality local and organic produce on a weekly basis. You can find a list of available product <a href="category_list.php">here</a>.
        <br /><br />
        <font size="+1"><?php @include ('message.php'); echo $notification_message; ?></font>
        Feel free to browse through our producers&#146; offerings below &ndash; everyone can take a tour of what is available.
        To actually start ordering, if you are already a registered member, just login below.  If you are not a registered
        member yet, click <a href="../join.php">here</a> to see more information on joining the <?php echo ORGANIZATION_TYPE; ?> &ndash; producers and shoppers alike make up both the
        voting and nonvoting membership of the <?php echo ORGANIZATION_TYPE; ?>.
      </td>
    </tr>
  </table>

  <table width="685" cellpadding="10" cellspacing="2" border="1" bordercolor="#000000">
    <tr>
      <td width="10" bgcolor="#DDDDDD" valign="center" align="center"><?php echo $font;?>
        <b>M<br>E<br>M<br>B<br>E<br>R<br>S<br></b>
      </td>
      <td align="left"><?php echo $font;?>
        <a href="<?php echo LOCATIONS_PAGE ?>">
        <b>Pickup&nbsp;&amp;&nbsp;Delivery&nbsp;Locations</b></a><br>
        <a href="<?php echo MEMBERSHIP_PAGE ?>">
        <b>How to Join</b></a><br>
        <a href="<?php echo COOP_PRODUCERS_PAGE ?>">
        <b>Read about Producers</b></a><br>
        <a href="<?php echo BASE_URL ?>">
        <b>Return to main website</b></a>
      </td>
      <td align="center"><?php echo $font;?>
        <b><?php echo $prod_count;?> Product Listed for the<br>
        <font color="#770000"><i><?php echo $month; ?></i></font> Order Cycle</b><br>
        <br>
        <table cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td align="left">
              <b>Order&nbsp;Opens:&nbsp</b></td><td align="right"><b><font color="#770000"><?php echo $date_open; ?></font></b>
            </td>
          </tr>
          <tr>
            <td align="left">
              <b>Order&nbsp;Closes:&nbsp</b></td><td align="right"><b><font color="#770000"><?php echo $date_closed; ?></font></b>
            </td>
          </tr>
          <tr>
            <td align="left">
              <b>Delivery&nbsp;Day:&nbsp</b></td><td align="right"><b><font color="#770000"><?php echo $delivery_date; ?></font></b>
           </td>
          </tr>
        </table>
      </td>
      <td bgcolor="#DDDDDD"><?php echo $font;?><br>
        <form method="post" action="members/orders_login.php" name="login">
        <table bgcolor="#DDDDDD">
          <tr>
            <td><b>Username</b>:</td>
            <td>
              <input type="text" name="username_m" value="<?php echo $username_m;?>" size="17" maxlength="20">
            </td>
          </tr>
          <tr>
            <td><b>Password</b>:</td>
            <td>
              <input type="password" name="password" size="17" maxlength="25">
            </td>
          </tr>
          <tr>
            <td colspan="2" align="right">
              <input type="hidden" name="gp" value="ds">
              <input type="submit" name="submit" value="Login to Order">
            </td>
          </tr>
        </table>
        </form>

      </td>
    </tr>
  </table>

  <table width="685" cellpadding="0" cellspacing="0" border="0">
    <tr bgcolor="#000000">
      <td align="center">
        <img src="grfx/shop-photos.jpg" width="100%" height="92" border="1" alt="<?php echo SITE_NAME; ?>"><br>
      </td>
    </tr>
  </table>

  <table width="685" cellpadding="10" cellspacing="2" border="1" bordercolor="#000000">
    <tr>
      <td rowspan="2" width="10" bgcolor="#DDDDDD" valign="center" align="center"><?php echo $font;?>
        <b>P<br>R<br>O<br>D<br>U<br>C<br>T<br>S<br></b>
      </td>

<?php if (USE_HTMLDOC ) {  // Don't show pdf options if htmldoc is not available ?>
      <td width="370" bgcolor="#DDDDDD" align="center"><?php echo $font;?>
        <b>Download PDFs of the <font color="#770000"><i><?php echo $cycle;?></i></font> Product List</b>
      </td>
<?php } ?>

      <td width="300" bgcolor="#DDDDDD" align="center" <?php if ( ! USE_HTMLDOC ) { echo ' colspan="2"';} ?>><?php echo $font;?>
        <b>Browse the <?php echo $cycle;?> Product List Webpages</b>
      </td>
    </tr>
    <tr>

<?php if ( USE_HTMLDOC ) {  // Don't show pdf options if htmldoc is not available ?>
      <td align="left" width="50%"><?php echo $font;?>

        <a href="pdf/all.pdf">
        <img src="<?php echo DIR_GRAPHICS ?>icon_pdf.gif" width="12" height="13" hspace="3" alt="PDF" border="0" align="left"></a>
        <a href="pdf.php?list=<?php echo base64_encode("all");?>">
        <b>Full Product List</b></a><br><br>

        <a href="pdf/new.pdf">
        <img src="<?php echo DIR_GRAPHICS ?>icon_pdf.gif" width="12" height="13" hspace="3" alt="PDF" border="0" align="left"></a>
        <a href="pdf.php?list=<?php echo base64_encode("new");?>">
        <b>New Products Since Last Order Cycle</b></a><br><br>

        <a href="pdf/changed.pdf">
        <img src="<?php echo DIR_GRAPHICS ?>icon_pdf.gif" width="12" height="13" hspace="3" alt="PDF" border="0" align="left"></a>
        <a href="pdf.php?list=<?php echo base64_encode("changed");?>">
        <b>Changed Products Since Last Order Cycle</b></a><br><br>

        <a href="pdf/deleted.pdf">
        <img src="<?php echo DIR_GRAPHICS ?>icon_pdf.gif" width="12" height="13" hspace="3" alt="PDF" border="0" align="left"></a>
        <a href="pdf.php?list=<?php echo base64_encode("deleted");?>">
        <b>Items Listed last cycle that are not available this order cycle</b></a><br><br>

        <a href="http://www.adobe.com/prodindex/acrobat/readstep.html" target="_blank">
        <img src="<?php echo DIR_GRAPHICS ?>acrobat.gif" width="88" height="31" hspace="3" alt="Get Acrobat Reader" border="0" align="left"></a>
        PDF files can be viewed using Adobe Acrobat Reader.

      </td>
<?php } ?>

      <td align="left" width="50%"><?php echo $font;?>

        <a href="category_list_full_new.php">
        <b>New Products Since Last Order Cycle</b></a><br><br>

        <a href="category_list_full_changed.php">
        <b>Changed Products Since Last Order Cycle</b></a><br><br>

        <a href="products_organic.php">
        <b>All Organic Products</b></a><br><br>

<?php if ( ! USE_HTMLDOC ) { echo '</td><td align="left" width="50%">';} ?>

        <a href="category_list.php">
        <b>Products by Category</b></a><br><br>

        <a href="prdcr_list.php">
        <b>Products by Producer</b></a><br><br>

<?php if (INSTITUTION_WINDOW > 0) { /* Only display wholesale if the wholesale system is being used */ echo '
        <a href="listall_wholesale.php">
        <b>Wholesale Products</b></a><br><br>';
} ?>
        <a href="category_list_full.php">
        <b>The Entire <?php echo $cycle;?> Product List</b></a><br><br>

        <a href="listall.php">
        <b>All Products by Product ID#</b></a><br><br>

      </td>
    </tr>
  </table>

  <table width="685" cellpadding="3" cellspacing="0" border="0">
    <tr>
      <td align="left"><?php echo $font;?>
        <b>Note:</b> You must be a member of the <?php echo SITE_NAME; ?> to purchase food through the <? echo ORGANIZATION_TYPE; ?>. Click here for
        the <A href="../join.php">membership page</a>. If you don&#146;t live in the region, we suggest that you contact
        our producers directly about ordering their products.  Their contact information is listed on their
        <a href="coopproducers.php">producer pages</a>.
      </td>
    </tr>
  </table>

</div>

  <!-- CONTENT ENDS HERE -->

<?php include("template_footer.php");?>

<script language="javascript">
  document.login.username_m.focus();
</script>
