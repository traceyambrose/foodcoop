<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();

$filepath = FILE_PATH.PATH.'admin/taxrates/';
$file = $_REQUEST[file];
$filename = $_REQUEST[filename];
$reupload = $_REQUEST[reupload];
if ( $_REQUEST[step] == 'preview' )
  {
    $title = 'Previewing Tax Rates to be Imported';
  }
elseif ( $_REQUEST[step] == 'insert' )
  {
    $title = 'Importing Tax Rates';
  }
else
  {
    $title = 'Importing Tax Rates';
  }
$content = '
  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td class="headerCell">'.$title.'</td>
    </tr>
  </table>';
if ( $_REQUEST[step] == 'preview' )
  {
    $step = 'preview';
    if ( $_REQUEST[headerrow] == 'Y' )
      {
        $headerrow = 1;
      }
    elseif ( $_REQUEST[headerrow] == 'N' )
      {
        $headerrow = 0;
      }
    else
      {
        $step = '';
        $msg_headerrow = '
          Please select Yes or No for if there is a header row in the file<br>
          (headings like Copo, City/County, Current Rate in the first row of your spreadsheet).<br>';
      }
  }
elseif ( $_REQUEST[step] == 'insert' )
  {
    $step = 'insert';
  }
if ( ($step == 'preview' || $step == 'insert') && ($file || $filename) )
  {
    if ( $file )
      {
        $file = array_slice(explode("\\",$file),-1);
        foreach($file as $v)
          {
            $filename = $v;
          }
      }
    if ( file_exists($filepath.$filename) )
      {
        // Separate into lines of the spreadsheet
        $imported = explode("\n",file_get_contents($filepath.$filename));
        $fileexists = 1;
      }
    else
      {
        $msg_error = '
          There was an error reading the spreadsheet data.<br>
          Please check that the file exists in the directory.<br>
          Click here to <b><a href="files.php">re-upload the revised file</a>.</b><br><br>';
        $error_sbj = 'Food Coop may need to chmod upload script';
        $notifyadmin = mail($error_to,$error_sbj,"$error_link",$error_hdrs,"-f $error_from");
      }
  }
elseif ( ($step == 'preview' || $step == 'insert') && (!$file || !$filename) )
  {
    $step = '';
    $msg_file = 'Please select a file to import.<br>';
  }
if ( $step == 'preview' )
  {
    $content .= '
      <table  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>Copo</td>
          <td>City/County</td>
          <td>Current Rate</td>
        </tr>';
    $previewarray = array();
  }
elseif ( $step == 'insert' )
  {
    $insertarray = array();
  }
if ( ($step == 'preview' || $step == 'insert') && $fileexists )
  {
    if ( $_REQUEST[headerrow] == 'Y' )
      {
        $headerrow = '1';
      }
    else
      {
        $headerrow = '0';
      }
    $imported = array_slice($imported,$headerrow); // If there is a header row in the spreadsheet, offset and start at the next row
    $r = $headerrow + 1; // Count row numbers for reference in error messages
    foreach( $imported as $value )
      {
        $imported2 = explode("\t",$value); // Separate into each column of the spreadsheet for each row
        if ( $step == 'insert' )
          {
            $field1 = array_slice($imported2, 0,1);
            foreach( $field1 as $copo )
              {
                $copo = addslashes(addslashes($copo));
              }
            $field2 = array_slice($imported2, 1,1);
            foreach( $field2 as $city_county )
              {
                $city_county = addslashes($city_county);
              }
            $field3 = array_slice($imported2, 2,1);
            foreach( $field3 as $current_rate )
              {
                $current_rate = str_replace(",",".",$current_rate);
                $current_rate = preg_replace("/[^0-9]/","",$current_rate);
                $current_rate = "0.0".$current_rate;
              }
          }
        if ( $step == 'preview' )
          {
            $preview .= '<tr>';
            foreach( $imported2 as $value2 )
              {
                $preview .= '<td class="t">'.$value2.'</td>'; // Show data in preview table
              }
            $preview .= "</tr>\n";
            array_push($previewarray,$preview); // Build the preview array
            unset($preview);
          }
        if ( $step == 'insert' )
          {
            //Empty the backup table created last time
            $sql = '
              TRUNCATE TABLE
                sales_tax_backup';
            //Back up the current sales_tax table
            $sql2 = '
              INSERT INTO
                sales_tax_backup
              SELECT
                *
              FROM
                sales_tax';
            //Empty the current sales_tax table
            $sql3 = 'TRUNCATE TABLE sales_tax_test';
            //Insert the new data
            $sql4 = '
              INSERT INTO
                '.TABLE_SALES_TAX.'_test
                  (
                    copo,
                    city_county,
                    current_rate
                  )
                VALUES
                  (
                    "'.$copo.'",
                    "'.$city_county.'",
                    "'.$current_rate.'"
                  )';
            array_push($insertarray,$sql);
          }
        $r++;
      }
  }
if ( $step == 'preview' && $filename )
  {
    $previewrow = array_slice($previewarray, 0, 5); // Show first 5 rows for preview
    foreach( $previewrow as $taxrate )
      {
        $content .= $taxrate;
      }
    $content .= '</table>';
    $content .= '
      <font color="red">Warning: Double-check that the first names are in the first name column<br>
      and all data is in the right location before importing. Importing is final.</font>';
  }
if ( $step == 'insert' )
  {
    array_pop($insertarray);
    foreach($insertarray as $sql4)
      {
        // loop through and make each insert
        $result2 = @mysql_query($sql4,$connection) or die("".$errormsg." ".mysql_error()." ".$notifyadmin."");
      }
    if ( $result2 )
      {
        $n = $r - $headerrow - 2; // -2 because of the $r++ at end of foreach loop above
        $msg_done = '
          Import completed.<br>'.$n.' tax rates added. <br><br>
          Click to <a href="taxrates.php">import another file</a>.<br><br>';
      }
  }
$content .= '
  <table border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td><font color="red"><b>
        '.$msg_error.'
        '.$msg_done.'</b></font>
      </td>
    </tr>
  </table>';
if ( !$_REQUEST[step] )
  {
    $content .= '<table width="70%" border="0" cellspacing="0" cellpadding="0"><tr><td width="55">&nbsp;</td><td>';
    if ( !$filename )
      {
        $content .= '
          To download your file:
          <ol>
            <li>Click here: <a href="http://www.tax.ok.gov/csvexl.html" target="_blank">http://www.tax.ok.gov/csvexl.html</a>
            <li>Right-click on the <b>Excel File</b>, choose "Save File As" and save it to your computer.
            <li>Open the file, and delete rows 1 & 2. Then delete columns A and E through L. (Or D through K, after  you have deleted column A.)
            <li>Then save the file as a <b>comma separated</b> file (.csv format). Excel may say that your file "may contain features that are not compatible with CSV." If you see this warning, click on the "yes" button.<br>Fields should be in this order:
            <ul>
              <img src="grfx/tab.jpg" width="388" height="137" hspace="4" align="right" border="0" alt="Save as a tab-delimited file">
              <li> Copo
              <li> City/Country
              <li> Current Tax Rate
            </ul>
          <br>';
        $mydir = dir($filepath);
        while (($file1 = $mydir->read()) !== false)
          {
            if ( ($file1 != '.') && ($file1 != '..') && ($file1 != 'index.php') )
              {
                $options .= '<option value="'.$file1.'">'.$file1.'</option>'."\n";
              }
          }
        $content .= '
            <li> <b>Please choose a file to upload:</b><BR>
              <form enctype="multipart/form-data" action="'.BASE_URL.'/cgi-bin/upload.pl" method="post">
                <input type="file" name="upload">
                <input type="submit" name="submit" value="Upload">
              </form>';
        if ( $options && $reupload != 1 )
          {
            $content .= '
              <form action="'.$_SERVER['PHP_SELF'].'" method="post">
                <b>Or if the file is already uploaded:</b><br>
                <select name="filename" class="form">
                  <option value="">-- Select the file to import--</option>\n
                  '.$options.'
                </select>
                <input type="submit" name="submit" value="Select File">
              </form>';
          }
      }
    else
      {
        $content .= '
          <b>Filename: '.$filename.'</b><br>
          <span class="red">'.$msg_file.'</span>
          <br>
          <form action="'.$_SERVER["PHP_SELF"].'" method="post">
            <b>Is there a header row?</b>
            <input type="radio" name="headerrow" value="Y">Yes
            <input type="radio" name="headerrow" value="N">No<br>
            <font color="red"><b>'.$msg_headerrow.'</b></font>
            <br>
            <b>Preview the contents</b>:
            <input type="hidden" name="step" value="preview">
            <input type="hidden" name="filename" value="'.$filename.'">
            <input type="submit" name="submit" value="Preview Data">
          </form>';
      }
    $content .= '
            </ol>
          </td>
        </tr>
      </table>';
  }
if ( $msg_error )
  {
    $errors = 1;
  }
if ( $step == "preview" && !$errors && $_REQUEST[headerrow] )
  {
    $content .= '
      <br><br>
      <div align="center">
      <table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>If this preview is not correct: </td>
          <td>&nbsp;</td>
          <td>If this preview is correct: </td>
        </tr>
        <tr>
          <td align="center">
            <form action="'.$_SERVER["PHP_SELF"].'" method="post"><br>
              <input type="hidden" name="reupload" value="1">
              <input type="submit" name="submit" value="Re-upload File">
            </form>
          </td>
          <td>&nbsp;</td>
          <td align="center">
            <form action="'.$_SERVER["PHP_SELF"].'" method="post"><br>
              <input type="hidden" name="step" value="insert">
              <input type="hidden" name="headerrow" value="'.$_REQUEST[headerrow].'">
              <input type="hidden" name="filename" value="'.$_REQUEST[filename].'">
              <input type="submit" name="submit" value="Insert Tax Rate Data">
            </form>
          </td>
        </tr>
      </table>
      </div>';
  }
include("template_hdr.php");?>
<?php  echo $content; ?>
<?php  include("template_footer.php"); ?>