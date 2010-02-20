<?php
include_once ("config_foodcoop.php");

// Call with: $_POST['subcategory_id']
// Return: [category_id]|[coop_fee]

// Is there a posted query string?
if(isset($_POST['subcategory_id']))
  {
  $subcategory_id = mysql_real_escape_string($_POST['subcategory_id']);
  // Is the string length greater than 0?
  $query = '
    SELECT
      category_name,
      0.15 as coop_fee
    FROM subcategories
    LEFT JOIN categories ON subcategories.category_id = categories.category_id
    WHERE subcategory_id = '.$subcategory_id.'
    LIMIT 15
    ';
  $sql = @mysql_query($query, $connection) or die('FAILED:'.mysql_error().'<br>$query');
  if($sql)
    {
    // While there are results loop through them - fetching an Object (i like PHP5 btw!).
    while ($row = mysql_fetch_array($sql))
      {
      // Format the results, im using <li> for the list, you can change it.
      // The onClick function fills the textbox with the result.

      // YOU MUST CHANGE: $result->value to $result->your_column
      echo $row['category_name'].'|'.$row['coop_fee'];
      }
    }
  }
?>