<?php
require_once("formvalidator.class.php");
class memberInterface
{
  var $member;
  var $producer;

  function memberInterface()
  {
    $this->member="members";
    $this->producer="producers";
    return true;
  }

  function mainMenu()
  {
    echo "<div style='border:.5px black solid;width:300px;'>";
    //echo "<a href='member_interface.php?action=add'>Add A New Member</a><br />";
    echo "<a href='member_interface.php?action=find'>Find/Edit a Member</a><br />";
    echo "</div>";
    return true;  
  }
  
  function buildAddMember()
  {
    include "forms/addmember.form.php";
    return true;  
  }
  
  function checkMemberForm()//check the form. $add_edit is whether we're checking an add or edit form
  {
    $fields=array("ok");
    $cf=new formValidator;
    
    $fields["first_name"]=$cf->checkText($_POST[first_name], "first name");
    $fields["last_name"]=$cf->checkText($_POST[last_name], "last name");
    
    if($_POST[first_name_2] || $_POST[last_name_2])
    {
      $fields["first_name_2"]=$cf->checkText($_POST[first_name_2], "first name 2");
      $fields["last_name_2"]=$cf->checkText($_POST[last_name_2], "last name 2");
    }else{
      $fields["first_name_2"]=true;
      $fields["last_name_2"]=true;
    }
    
    if($_POST[email_address])
    {
      $fields["email_address"]=$cf->validateEmail($_POST[email_address]);
    }else{
      $fields["email_address"]=true;
    }
    
    if($_POST[email_address_2])
    {
      $fields["email_address_2"]=$cf->validateEmail($_POST[email_address_2]);
    }else{
      $fields["email_address_2"]=true;
    }
    
    $fields["home_phone"]=$cf->checkText($_POST[home_phone], "home phone");
    $fields["address_line1"]=$cf->checkText($_POST[address_line1], "address");
    $fields["city"]=$cf->checkText($_POST[city], "city");
    $fields["state"]=$cf->checkText($_POST[state], "state");
    $fields["zip"]=$cf->checkText($_POST[zip], "zip");    
    
  /*  if($_POST[business_name])
    {
      $fields["work_phone"]=$cf->checkText($_POST[work_phone], "work phone");
      $fields["work_address_line1"]=$cf->checkText($_POST[work_address_line1], "work address");
      $fields["work_city"]=$cf->checkText($_POST[work_city], "work city");
      $fields["work_state"]=$cf->checkText($_POST[work_state], "work state");
      $fields["work_zip"]=$cf->checkText($_POST[work_zip], "work zip");
    }else{
      $fields["work_phone"]=true;
      $fields["work_address_line1"]=true;
      $fields["work_city"]=true;
      $fields["work_state"]=true;
      $fields["zip"]=true;

    }*/
    $fields["username_m"]=$cf->checkText($_POST[username_m], "user name");
    if($fields["username_m"])
    {
      if(!$_GET[ID])
      {
        $query_string="SELECT * FROM ".$this->member." WHERE `username_m` = '".$_POST['username_m']."';";
        $query=mysql_query($query_string);
        $rows=@mysql_num_rows($query);
    
        if($rows>0)
        {
        
          $cf->_errors[$cf->_counter]="There is already a user with this username. Please choose a new one.";
          $cf->_counter++;
        }
      }
    }
    
    if(!$_GET[ID] || $_POST[password_r])
    {
      if($fields["password"]=$cf->checkText($_POST[password], "password", "min", 6, 25, "alphanumeric") && $fields["password_r"]=$cf->checkText($_POST[password_r], "password again", "min", 6, 25, "alphanumeric"))
      {
        if($_POST[password]!=$_POST[password_r])
        {
          $cf->_errors[$cf->_counter]="The second password does not match the first.";
          $cf->_counter++;
        }
      }
      
      
    }
    
    if(!$cf->showErrors())
    {
      
      $result['username_m']=$_POST[username_m];
      $result['business_name']=$_POST[business_name];
      $result['last_name']=$_POST[last_name];
      $result['first_name']=$_POST[first_name];
      $result['last_name_2']=$_POST[last_name_2];
      $result['first_name_2']=$_POST[first_name_2];
      $result['no_postal_mail']=$_POST[no_postal_mail];
      $result['address_line1']=$_POST[address_line1];
      $result['address_line2']=$_POST[address_line2];
      $result['city']=$_POST[city];
      $result['state']=$_POST[state];
      $result['zip']=$_POST[zip];
      $result['county']=$_POST[county];
      $result['work_address_line1']=$_POST[work_address_line1];
      $result['work_address_line2']=$_POST[work_address_line2];
      $result['work_city']=$_POST[work_city];
      $result['work_state']=$_POST[work_state];
      $result['work_zip']=$_POST[work_zip];
      $result['email_address']=$_POST[email_address];
      $result['email_address2']=$_POST[email_address2];
      $result['home_phone']=$_POST[home_phone];
      $result['work_phone']=$_POST[work_phone];
      $result['mobile_phone']=$_POST[mobile_phone];
      $result['fax']=$_POST[fax];
      $result['toll_free']=$_POST[toll_free];
      $result['home_page']=$_POST[home_page];
      $result['membership_type_id']=$_POST[membership_type_id];
      $result['membership_date']=$_POST[membership_date];
      $result['membership_discontinued']=$_POST[membership_discontinued];
      if($_POST[producer_id])
      {
        $p_result['producer_id']=$_POST[producer_id];
        $p_result['donotlist_producer']=$_POST[donotlist_producer];  
      }
      
    
      include "forms/addmember.form.php";
      return false;
    }
    
    $this->insertData();
        
    return true;
  }

  function insertData()
  {
    $query=mysql_query("SELECT MD5('".$_POST[password]."')");
    $pass=mysql_fetch_row($query);
    $password=$pass[0];
  
    $member_id = preg_replace("/[^0-9]/","",$_POST['member_id']);
    
    if($_POST[producer_id])
    {
      $query_string="UPDATE ".$this->producer." SET `donotlist_producer`=".$_POST[donotlist_producer]." WHERE `member_id`='".$member_id."';";
      $query=mysql_query($query_string) or die(mysql_error()." ".$sql);
      
    }
    
     if(!$_POST[password_r])
     {
       $password=$_POST[password];
     }
        
    if($_POST[no_postal_mail]!=1)
    {
      $_POST[no_postal_mail]=0;
    }
    
    if($_POST[membership_discontinued]!=1)
    {
    
      $_POST[membership_discontinued]=0;
    }
    
    
    if($member_id>0){
      $query_type = "UPDATE";
    } else {
      $query_type = " INSERT INTO";
    }
    
    $query_string="".$query_type." ".$this->member." SET 
      username_m = '".mysql_real_escape_string($_POST[username_m])."',
      password = '".mysql_real_escape_string($password)."',
      business_name = '".mysql_real_escape_string($_POST[business_name])."',
      last_name = '".mysql_real_escape_string($_POST[last_name])."',
      first_name = '".mysql_real_escape_string($_POST[first_name])."',
      last_name_2 = '".mysql_real_escape_string($_POST[last_name_2])."', 
      first_name_2 = '".mysql_real_escape_string($_POST[first_name_2])."', 
      no_postal_mail = '$_POST[no_postal_mail]',
      address_line1 = '".mysql_real_escape_string($_POST[address_line1])."',
      address_line2 = '".mysql_real_escape_string($_POST[address_line2])."',
      city = '".mysql_real_escape_string($_POST[city])."',
      state = '".mysql_real_escape_string($_POST[state])."',
      zip = '".mysql_real_escape_string($_POST[zip])."',
      county = '".mysql_real_escape_string($_POST[county])."',
      work_address_line1 = '".mysql_real_escape_string($_POST[work_address_line1])."',
      work_address_line2 = '".mysql_real_escape_string($_POST[work_address_line2])."',
      work_city = '".mysql_real_escape_string($_POST[work_city])."',
      work_state = '".mysql_real_escape_string($_POST[work_state])."',
      work_zip = '".mysql_real_escape_string($_POST[work_zip])."',
      email_address = '".mysql_real_escape_string($_POST[email_address])."',
      email_address_2 = '".mysql_real_escape_string($_POST[email_address_2])."',
      home_phone = '".mysql_real_escape_string($_POST[home_phone])."',
      work_phone = '".mysql_real_escape_string($_POST[work_phone])."',
      mobile_phone = '".mysql_real_escape_string($_POST[mobile_phone])."',
      fax = '".mysql_real_escape_string($_POST[fax])."',
      toll_free = '".mysql_real_escape_string($_POST[toll_free])."', 
      home_page = '".mysql_real_escape_string($_POST[home_page])."',
      membership_discontinued = '$_POST[membership_discontinued]'"; 
    if($member_id>0){
      $query_string .= " WHERE member_id = '".$member_id."' ";
    }
    $query=mysql_query($query_string) or die(mysql_error()." ".$sql);
    
    
    if($_POST[new_producer_id])
    {
      $query_string="SELECT `member_id` FROM ".$this->member." WHERE  `username_m`='$_POST[username_m]';";
      $query=mysql_query($query_string);
      $result=mysql_fetch_row($query);
      $member_id=$result[0];
      $query_string="INSERT INTO ".$this->producer." (producer_id, member_id,  donotlist_producer) values('$_POST[new_producer_id]','$member_id', '$_POST[donotlist_producer]');";
      $query=mysql_query($query_string) or die(mysql_error()." ".$sql);
      $query_string="INSERT INTO ".TABLE_PRODUCER_REG." (producer_id, member_id,  business_name) values('$_POST[new_producer_id]','$member_id', '$_POST[business_name]');";
      $query=mysql_query($query_string) or die(mysql_error()." ".$sql);
    }
    
    if($query && $member_id)
    {
      echo "Member updated!<br />";
    }else{
      echo "Member added!<br />";
    }
    
    $this->mainMenu();
    return true;
  
  }

  function findForm()
  {
    include "forms/findmembers.form.php";
    return;
  }
  
  function findUsers()
  {
    if(!$_POST[query])
    {
      $query_string = "SELECT * FROM ".$this->member." ORDER BY `last_name`;";
    }
    if($_POST[type]=="name")
    {
      //the following code block splits up separate names and searches for and deletes any commas the user mayhave entered
      $names=explode(" ", $_POST[query]);//split search string
    
      $len=count($names);//how many words?
      for($i=0;$i<$len;$i++)
      {
        $comma=strchr($names[$i], ord(","));//find commas
        if($comma)//delete commas
        {
          $names[$i]=str_replace(","," ",$names[$i]);
          $names[$i]=trim($names[$i]);
        }
      
      }
      
      $query_string="SELECT * FROM ".$this->member." WHERE ";
      
      for($i=0;$i<$len;$i++)
      {
        if($i>0)
        {
          $query_string.=" OR "; 
        }
          
          $query_string.="`last_name` = '".$names[$i]."' OR `last_name_2` = '".$names[$i]."' OR `first_name` = '".$names[$i]."' OR `first_name_2` = '".$names[$i]."' ";
      }
      
      $query_string.=" ORDER BY `last_name`, `last_name_2`;";
      //die("$query_string");
    }else{
      $query_string="SELECT * FROM ".$this->member." WHERE `".$_POST[type]."` = '".$_POST[query]."' ORDER BY `".$_POST[type]."`;";
    }
  
    $query=mysql_query($query_string) or die(mysql_error());
    $rows=@mysql_num_rows($query);
    if($rows>0)
    {
      $this->displayUsers($query, $rows);
      return true;
    }else{
      echo "No users found.  Please search again.";
      $this->findForm();
      return false;
    }
  }  

  function displayUsers($query, $rows)//entirely a subset of the findUsers()
  {
      echo "<table style='border:.5px black solid;width:80%;' cellspacing='0'>
        <tr bgcolor='#BB0000'>
          <td>Member #</td><td>Name</td><td>Business Name</td><td>Username</td><td>Action</td>
        </tr>";
      while($result=mysql_fetch_array($query)){
        echo "
        <tr>
          <td>".$result['member_id']."</td><td>".$result['first_name']." ".$result['last_name']."</td><td>".$result['business_name']."</td><td>".$result['username_m']."</td><td><a href='member_interface.php?action=edit&ID=".$result['member_id']."'>Edit</a></td>
        </tr>"; 
      }
    echo "</table>";
    return true;
  }
    
  function editUser()
  {
    $query_string="SELECT * FROM ".$this->member." WHERE `member_id`=".$_GET[ID].";";
    $query=mysql_query($query_string);  
    $result=mysql_fetch_array($query);
    
    $query_string="SELECT * FROM ".$this->producer." WHERE `member_id`=".$_GET[ID].";";
    $query=mysql_query($query_string);
    $p_result=@mysql_fetch_array($query);
    
    include "forms/addmember.form.php";
    return true;
  }
  


}


?>
