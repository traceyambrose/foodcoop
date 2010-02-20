<?php
class Delivery
{
  /***
   * void Constructor(void)
   * This class was initially designed to be implemented statically and still 
   * could if a few functions had wrappers in the basket class.  But I'm lazy.
   * Everything works statically, its just a mixed bag now.  bad form. easy to use
   */
  function Delivery(){
    return;
  }

      
  /***
   * int getDeliveryId(void)
   * Checks to see if there's a sent delivery id and returns the current one
   * if there isn't.  Seems kind of silly but's used in so many functions it saves
   * significant time.  Could also be expanded.
   */
  
  function getDeliveryId(){
    if($_REQUEST["delivery_id"]){
      $delivery_id = $_REQUEST["delivery_id"];
    }else{
      $delivery_id = $_SESSION['current_delivery_id'];
    }
    
    return $delivery_id;
  }
  
  /***
   * boolean printChangeDeliveryInfoForm(int basket_id, int member_id)
   * Gets current delivery info for basket, prints a form showing it that allows
   * it to be changed. Returns true or false on success or failure.
   */
  
  function printChangeDeliveryInfoForm($basket_id, $member_id){
    //Extra security check.  
    /*if(Security::getRegisteredAuthType()!="rtemanager" && Security::getRegisteredAuthType()!="administrator" && Security::getRegisteredMemberId()!=$member_id){
      print "You don't have permission to view this page.<br /><a href='javascript: window.history.go(-1)'>Back</a>";
      return false;
    }*/
    
    //get the basket's delivery info
    $sql = "SELECT ".TABLE_BASKET_ALL.".deltype AS user_deltype,
            ".TABLE_BASKET_ALL.".*,
            ".TABLE_DELCODE.".*,
            ".TABLE_ROUTE.".*
             FROM ".TABLE_BASKET_ALL."
            LEFT JOIN ".TABLE_DELCODE." USING (delcode_id)
            LEFT JOIN ".TABLE_ROUTE." USING (route_id)
            WHERE basket_id = $basket_id";
    $result = mysql_query($sql) or die(mysql_error().$sql);
    $row = mysql_fetch_array($result);
    //delivery info
    $delcode_id = $row['delcode_id'];
    $delcode = $row['delcode'];
    $deltype = $row['user_deltype'];
        
    //get all the delivery options from the table    
    $sql = "SELECT * FROM ".TABLE_DELCODE." ORDER BY delcode";
    $result = mysql_query($sql) or die(mysql_error().$sql);
        
    //the form itself
    $display .= "<form action='".SELF."?&basket_id=$basket_id' method='post'>";
    $display .= "<table>
                <tr>
                  <td>
            <b>Pickup/Delivery Locations</b>
            </td>
            <td>";
    $display .= "<select name='delcode_id'>";
    //create all the options for SELECT, highlight the currently selected one
    while($row = mysql_fetch_array($result)){
      $selected=NULL;
      if($delcode_id == $row['delcode_id']){
        $selected="selected";
      }
      $display.="<option value='".trim($row['delcode_id'])."' $selected>".$row['delcode']."</option>\n\r";
    }
    
    $display .= "</select>";
    $display .= "</td>
                 </tr>";
    
    $display .= "<tr>
                  <td>
                  <b>Delivery Type</b
                  </td>";
    //check the current delivery type
    switch($deltype){
      case "H":
        $chk1 = "checked";
        break;
      case "W":
        $chk2 = "checked";
        break;
      case "P":
        $chk3 = "checked";
        break;
    }
    
    $display.="<td>
          <input type='radio' name='deltype' value='H' $chk1 >Home Delivery<br />
          <input type='radio' name='deltype' value='W' $chk2 >Work Delivery<br />
          <input type='radio' name='deltype' value='P' $chk3 >Pick-up
          </td>
          </tr>
          <tr>
          <td>
          </td>
          <td>
          <input type='submit' value='Change Delivery Information' />
          </td>
          </tr>";
    
    $display .= "</form>";
    $display .= "</table>";
    
    return $display;
  
  }

  
  function changeUserDeliveryInfo(){
    //get the environment variables
    $delcode_id = preg_replace("/[^a-zA-Z0-9]/","",$_REQUEST['delcode_id']);
    $deltype = preg_replace("/[^A-Z]/","",$_REQUEST['deltype']);
    $basket_id = preg_replace("/[^0-9]/","",$_REQUEST['basket_id']);
    
    //Get the delivery cost info
    $sql = "SELECT delcharge, transcharge
            FROM ".TABLE_DELCODE." 
            WHERE delcode_id = '".mysql_real_escape_string($delcode_id)."'";
      
    $result = mysql_query($sql) or die(mysql_error()."$sql");
    $row = mysql_fetch_array($result);
    
    $delcharge = $row['delcharge'];
    $transcharge = $row['transcharge'];
    
    //store new delivery information for the basket
    $sql = "UPDATE ".TABLE_BASKET_ALL." 
            SET transcharge=$transcharge,
            delivery_cost=$delcharge,
            delcode_id='".mysql_real_escape_string($delcode_id)."',
            deltype='".mysql_real_escape_string($deltype)."'
            WHERE basket_id = $basket_id";
    
    $result = mysql_query($sql) or die(mysql_error()."$sql");
    
    return;
  }
    
}