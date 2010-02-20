<?php

if ( ($first_name || $last_name) && ($first_name_2 || $last_name_2) )
  {
    if( $last_name == $last_name_2 )
      {
        if ( $business_name )
          {
            $show_name = stripslashes($business_name).'<br>'.stripslashes($last_name_2).', '.stripslashes($first_name).' & '.stripslashes($first_name_2);
          }
        else
          {
            $show_name = stripslashes($last_name_2).', '.stripslashes($first_name).' & '.stripslashes($first_name_2);
          }
      }
    else
      {
        if ( $business_name )
          {
            $show_name = stripslashes($business_name).'<br>'.stripslashes($last_name).', '.stripslashes($first_name).' & '.stripslashes($first_name_2).' '.stripslashes($last_name_2);
          }
        else
          {
            $show_name = stripslashes($last_name).', '.stripslashes($first_name).' & '.stripslashes($first_name_2).' '.stripslashes($last_name_2);
          }
      }
  }
elseif ( ($first_name || $last_name) && (!$first_name_2 && !$last_name_2) )
  {
    if ( $business_name )
      {
        $show_name =  stripslashes($business_name).'<br>'.stripslashes($last_name).', '.stripslashes($first_name);
      }
    else
      {
        $show_name =  stripslashes($last_name).', '.stripslashes($first_name);
      }
  }
else
  {
    $show_name = stripslashes($business_name);
  }
