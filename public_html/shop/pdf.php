<?php
include_once ('config_foodcoop.php');
include_once ('general_functions.php');

$level = '';

echo '<title>Product Lists - '.SITE_NAME.'</title>
    </head>
    <body>';


echo '<div align="right"><a href="index.php">Back to Main Menu</a>&nbsp;&nbsp;</div>';

// Start with PDF processing stuff.

$filename = preg_replace('/[^a-z]/','',base64_decode($list));

if ( $filename == 'new' || $filename == 'changed' || $filename == 'all' || $filename == 'deleted' )
  {

    if ( file_exists(FILE_PATH . PATH . 'pdf/' . $filename . '.html') )
      {
        unlink(FILE_PATH . PATH . 'pdf/' . $filename . '.html');
      }

    $fp = fopen( FILE_PATH . PATH . 'pdf/' . $filename . '.html', a);
    include( FILE_PATH . PATH . 'pdfproductlist.php');
    //$newpdf .= "\n<HR BREAK>\n";
    fwrite($fp,$display);
    //unset($newpdf);

    if ( file_exists( FILE_PATH . PATH . 'pdf/' . $filename . '.pdf')) {
        unlink(FILE_PATH.PATH.'pdf/'.$filename.'.pdf');
    }

    function writepdf()
      {
        global $filename;

        exec('htmldoc --webpage --browserwidth 800 --left 36 --right 36 --top 24 -t pdf '.FILE_PATH.PATH.'pdf/'.$filename.'.html -f '.FILE_PATH.PATH.'pdf/'.$filename.'.pdf');
      }

    echo '
      <br><br><blockquote>
      <b>Printed '.$filename.'.pdf</b><br>
      This is an up to the minute copy of this product list, generated live from the currently posted information. <br>
      Opening the file below requires the <A HREF="http://www.adobe.com/products/acrobat/readstep.html" target="_new">Adobe Acrobat Reader</a>. <br>
      You can open the file by clicking it, or you can right click the link, select "Save As" and save a copy of it to your computer.
      <br><br><br>
      &nbsp;&nbsp;<a href="pdf/'.${filename}.'.pdf" target="_blank">Click to download your PDF</a><br><br>
      </blockquote><br><br><br>';


    writepdf();
  }
else
  {
    $filename = '';
    echo '<blockquote>Please return to the product list links to choose one to print to PDF.
      <br><br><br><br>
      </blockquote>';
  }

echo '</body>';
echo '</html>';
unset($_SESSION['donot_execute']);

ob_end_flush();
?>