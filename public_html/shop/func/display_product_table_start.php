<?php
$display .= '
  <table border="1" cellpadding="5" cellspacing="0" bordercolor="#DDDDDD" bgcolor="#ffffff" width="95%" align="center">
    <tr>';
if ( $display_type == 'shop' )
  {
    $display .= '
      <th align="center" bgcolor="#DDDDDD" width="60">Order</th>';
  }
elseif ( $display_type == 'new_or_changed' )
  {
    $display .= '
      <th align="center" bgcolor="#DDDDDD" width="60"></th>';
  }
elseif ( $display_type == 'edit' )
  {
    $display .= '
      <th align="center" bgcolor="#DDDDDD" width="60">Edit</th>';
  }
$display .= '
      <th align="center" bgcolor="#DDDDDD" width="60">Prod.ID</th>
      <th align="center" bgcolor="#DDDDDD">Product Name [<a href="'.BASE_URL.PATH.'producers/'.strtolower($producer_id).'.php">About Producer</a>]</th>
      <th align="center" bgcolor="#DDDDDD" width="60">Prod.Type</th>
      <th align="center" bgcolor="#DDDDDD" width="60">Price</th>
    </tr>';
?>