<br>
<?php

if (!$delivery_id)
  {
    $delivery_id = $current_delivery_id;
  }
?>
<div align="center"><b>
[ <a href="index.php">Main Page</a> |
<a href="orders_list.php?delivery_id=<?php echo $delivery_id;?>">Edit Member Orders</a> |
<a href="adjustments.php">Adjustments</a> |
<a href="orders_prdcr_list.php?delivery_id=<?php echo $current_delivery_id;?>">Producer Invoices</a> |
<a href="delivery.php">Route Lists</a> |
<a href="logout.php">Logout</a> ]
</b></div>
<br>
</body>
</html>