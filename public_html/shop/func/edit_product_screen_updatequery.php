<?php
if($where == '' && $submit_action == 'Update Product')
  {
    $product_name = mysql_real_escape_string ($product_name);
    $pricing_unit = mysql_real_escape_string ($pricing_unit);
    $ordering_unit = mysql_real_escape_string ($ordering_unit);
    $detailed_notes = mysql_real_escape_string ($detailed_notes);
    $unit_price     = preg_replace("/[^0-9\.\-]/","",$unit_price);
    $extra_charge   = preg_replace("/[^0-9\.\-]/","",$extra_charge);
    $minimum_weight = preg_replace("/[^0-9\.\/]/","",$minimum_weight);
    $maximum_weight = preg_replace("/[^0-9\.\/]/","",$maximum_weight);
    $inventory      = preg_replace("/[^0-9]/","",$inventory);
    if ( ! $product_name )
      {
        $message2 .= '<b><font color="#3333FF">You must enter a product name to continue.</font></b><br><br>';
        $alert2 = 1;
        $update = 'no';
      }
    if ( !$subcategory_id )
      {
        $message2 .= '<b><font color="#3333FF">Please choose a subcategory.</font></b><br><br>';
        $alert4 = 1;
        $update = 'no';
      }
    if ( ! $unit_price )
      {
        $message2 .= '<b><font color="#3333FF">Please enter a unit price.</font></b><br><br>';
        $alert5 = 1;
        $update = 'no';
      }
    if ( ! $pricing_unit )
      {
        $message2 .= '<b><font color="#3333FF">Please enter a pricing unit.</font></b><br><br>';
        $alert5a = 1;
        $update = 'no';
      }
    if ( ! $ordering_unit )
      {
        $message2 .= '<b><font color="#3333FF">Please enter an ordering unit, often the same as the pricing unit.</font></b><br><br>';
        $alert5b = 1;
        $update = 'no';
      }
    if ( $random_weight && ( ! $minimum_weight || ! $maximum_weight) )
      {
        $message2 .= '<b><font color="#3333FF">You have selected Yes for random weight product. If this is a random weight product you need to enter an approximate minimum and maximum weight. If, for example, a package is always approximately one pound, enter 1 in both the min. and max. fields and this will be reflected.</font></b><br><br>';
        $alert8 = 1;
        $update = 'no';
      }
    if ( $meat_weight_type && ! $random_weight )
      {
        $message2 .= '<b><font color="#3333FF">Meat weight type is only valid for random weight items.</font></b><br><br>';
        $alert12 = 1;
        $alert8 = 1;
        $update = 'no';
      }
    if ( ! $meat_weight_type && ! $random_weight )
      {
        $minimum_weight = '';
        $maximum_weight = '';
      }
    if ( $new == 1 )
      {
        $changed = 0;
      }
    else
      {
        $changed = 1;
      }
    if ( $update != 'no' )
      {
        $sqlu = '
          UPDATE
            '.TABLE_PRODUCT_PREP.'
          SET
            changed = "'.$changed.'",
            product_name = "'.$product_name.'",
            subcategory_id = "'.$subcategory_id.'",
            inventory_on = "'.$inventory_on.'",
            inventory = "'.$inventory.'",
            unit_price = "'.$unit_price.'",
            pricing_unit = "'.$pricing_unit.'",
            ordering_unit = "'.$ordering_unit.'",
            meat_weight_type = "'.$meat_weight_type.'",
            prodtype_id = "'.$prodtype_id.'",
            extra_charge = "'.$extra_charge.'",
            random_weight = "'.$random_weight.'",
            minimum_weight = "'.$minimum_weight.'",
            maximum_weight = "'.$maximum_weight.'",
            donotlist = "'.$donotlist.'",
            detailed_notes = "'.$detailed_notes.'",
            storage_id = "'.$storage_id.'"
          WHERE
            producer_id = "'.$producer_id.'"
            AND product_id = "'.$product_id.'"';
        $result = @mysql_query($sqlu,$connection) or die(mysql_error());
        $query = '
          SELECT
            product_id
          FROM
            '.TABLE_PRODUCT.'
          WHERE
            product_id = '.$product_id;
        $sql = mysql_query($query);
        if ( mysql_num_rows($sql) > 0 )
          {
            $sqlu2 = '
              UPDATE
                '.TABLE_PRODUCT.'
              SET
                storage_id = '.$storage_id.',
                inventory_on = '.$inventory_on.',
                inventory = "'.$inventory.'"
              WHERE
                producer_id = "'.$producer_id.'"
                AND product_id = '.$product_id;
            $resultu2 = @mysql_query($sqlu2,$connection) or die(mysql_error());
          }

        header ("refresh: 2; url='edit_product_list.php?producer_id=$producer_id&a={$_REQUEST['a']}#$product_id'");
        echo '<div style="width:40%;margin-left:auto;margin-right:auto;font-size:3em;padding:2em;text-align:center;color:fff;background-color:#008;">Product #'.$product_id.' has been updated.</div>';
        exit (0);

      }
    $action = 'edit';
  }
if($where == 'Save as a New Product' || $submit_action == 'Add Product')
  {
    $product_name = mysql_real_escape_string ($product_name);
    $pricing_unit = mysql_real_escape_string ($pricing_unit);
    $ordering_unit = mysql_real_escape_string ($ordering_unit);
    $detailed_notes = mysql_real_escape_string ($detailed_notes);
    $unit_price     = preg_replace("/[^0-9\.\-]/","",$unit_price);
    $extra_charge   = preg_replace("/[^0-9\.\-]/","",$extra_charge);
    $minimum_weight = preg_replace("/[^0-9\.\/]/","",$minimum_weight);
    $maximum_weight = preg_replace("/[^0-9\.\/]/","",$maximum_weight);
    $inventory      = preg_replace("/[^0-9]/","",$inventory);
    if ( ! $product_name )
      {
        $message2 .= '<b><font color="#3333FF">You must enter a product name to continue.</font></b><br><br>';
        $alert2 = 1;
        $update = 'no';
      }
    if ( ! $subcategory_id )
      {
        $message2 .= '<b><font color="#3333FF">Please choose a subcategory.</font></b><br><br>';
        $alert4 = 1;
        $update = 'no';
      }
    if ( ! $unit_price )
      {
        $message2 .= '<b><font color="#3333FF">Please enter a unit price.</font></b><br><br>';
        $alert5 = 1;
        $update = 'no';
      }
    if ( ! $pricing_unit )
      {
        $message2 .= '<b><font color="#3333FF">Please enter a pricing unit.</font></b><br><br>';
        $alert5a = 1;
        $update = 'no';
      }
    if ( ! $ordering_unit )
      {
        $message2 .= '<b><font color="#3333FF">Please enter an ordering unit, often the same as the pricing unit.</font></b><br><br>';
        $alert5b = 1;
        $update = 'no';
      }
    if ( $random_weight && ( ! $minimum_weight || ! $maximum_weight ) )
      {
        $message2 .= '<b><font color="#3333FF">You have selected Yes for random weight product. If this is a random weight product you need to enter an approximate minimum and maximum weight. If, for example, a package is always approximately one pound, enter 1 in both the min. and max. fields and this will be reflected.</font></b><br><br>';
        $alert8 = 1;
        $update = 'no';
      }
    if ( $meat_weight_type && ! $random_weight )
      {
        $message2 .= '<b><font color="#3333FF">Meat weight type is only valid for random weight items.</font></b><br><br>';
        $alert12 = 1;
        $alert8 = 1;
        $update = 'no';
      }
    if ( ! $meat_weight_type && ! $random_weight )
      {
        $minimum_weight = '';
        $maximum_weight = '';
      }
    if ( ! $prodtype_id )
      {
        $message2 .= '<b><font color="#3333FF">Please select a product type.</font></b><br><br>';
        $alert6 = "1";
        $update = 'no';
      }
    if ( $update != 'no')
      {
        $sqlu = '
          INSERT INTO
            '.TABLE_PRODUCT_PREP.'
            (
              producer_id,
              subcategory_id,
              inventory_on,
              inventory,
              new,
              product_name,
              unit_price,
              pricing_unit,
              ordering_unit,
              prodtype_id,
              extra_charge,
              random_weight,
              minimum_weight,
              maximum_weight,
              meat_weight_type,
              donotlist,
              detailed_notes,
              storage_id
            )
          VALUES
            (
              "'.$producer_id.'",
              "'.$subcategory_id.'",
              "'.$inventory_on.'",
              "'.$inventory.'",
              "1",
              "'.$product_name.'",
              "'.$unit_price.'",
              "'.$pricing_unit.'",
              "'.$ordering_unit.'",
              "'.$prodtype_id.'",
              "'.$extra_charge.'",
              "'.$random_weight.'",
              "'.$minimum_weight.'",
              "'.$maximum_weight.'",
              "'.$meat_weight_type.'",
              "'.$donotlist.'",
              "'.$detailed_notes.'",
              "'.$storage_id.'"
            )';
        $result3 = @mysql_query($sqlu,$connection) or die(mysql_error());
        $new_product_id = mysql_insert_id ();
        header("refresh: 2; url='edit_product_list.php?producer_id=$producer_id&a={$_REQUEST['a']}#$new_product_id'");
        echo '<div style="width:40%;margin-left:auto;margin-right:auto;font-size:3em;padding:2em;text-align:center;color:fff;background-color:#080;">New product #'.$new_product_id.' has been created.</div>';
        exit (0);
      }
  }
if($submit_action == 'Cancel')
  {
    header('refresh: 2; url="edit_product_list.php?producer_id='.$producer_id.'&a='.$_REQUEST['a'].'#'.$product_id.'"');
    echo '<div style="width:40%;margin-left:auto;margin-right:auto;font-size:3em;padding:2em;text-align:center;color:fff;background-color:#800;">Editing<br>was<br>CANCELLED.</div>';
    exit (0);
  }
