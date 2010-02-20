<div align="center">
<table width="70%">
  <tr><td align="left">

<?php 
if($_GET[action]=="edit")
{
  $action="member_interface.php?action=checkMemberForm&ID=$_GET[ID]";
  $title="Edit Membership Information";
}else{
  $action="member_interface.php?action=checkMemberForm";
  $title="Add A New Member";
}
echo "<form action='$action' method='post' name='addMember'>";
echo "<h3>$title</h3>";
?>
<font color=#3333FF><b>*</b></font> means it is a required field.
<table width="550" border="1" cellpadding="2" cellspacing="2" bordercolor="#333333">
  <tr bgcolor="#BB0000"> 
    <td colspan="3"><font face="arial"><b>Personal Info</b></font></td>
  </tr>
  <tr> 
    <td width="50" align="center">&nbsp;<?php if($fields["first_name"]==false){echo "<font color=#3333FF><b>*</b></font>";}?></td>
    <td width="150" bgcolor="#CCCCCC">First name</td>
    <td width="350" bgcolor="#CCCCCC"> <input name="first_name" type="text" id="first_name4" size="20" maxlength="25" <?php echo "value='".$result['first_name']."'"; ?> ></td>
  </tr>
  <tr> 
    <td align="center">&nbsp;<?php if($fields["last_name"]==false){echo "<font color=#3333FF><b>*</b></font>";}?></td>
    <td bgcolor="#CCCCCC">Last name </td>
    <td bgcolor="#CCCCCC"> <input name="last_name" type="text" id="last_name4" size="20" maxlength="25" <?php echo "value='".$result['last_name']."'"; ?> ></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td bgcolor="#CCCCCC">First name 2</td>
    <td bgcolor="#CCCCCC"> <input name="first_name_2" type="text" id="first_name_23" size="20" maxlength="25" <?php echo "value='".$result['first_name_2']."'"; ?> ></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td bgcolor="#CCCCCC">Last Name 2 </td>
    <td bgcolor="#CCCCCC"> <input name="last_name_2" type="text" id="last_name_23" size="20" maxlength="25" <?php echo "value='".$result['last_name_2']."'"; ?> ></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td bgcolor="#CCCCCC">Phone number (h) </td>
    <td bgcolor="#CCCCCC"> <input name="home_phone" type="text" id="home_phone3" size="15" maxlength="20" <?php echo "value='".$result['home_phone']."'"; ?> ></td>
  </tr>
   <tr> 
    <td>&nbsp;</td>
    <td bgcolor="#CCCCCC">Phone number (w) </td>
    <td bgcolor="#CCCCCC"> 
      <input name="work_phone" type="text" id="work_phone3" size="15" maxlength="20" <?php echo "value='".$result['work_phone']."'"; ?> ></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td bgcolor="#CCCCCC">Mobile phone </td>
    <td bgcolor="#CCCCCC"> <input name="mobile_phone" type="text" id="mobile_phone3" size="15" maxlength="20" <?php echo "value='".$result['mobile_phone']."'"; ?> ></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td bgcolor="#CCCCCC">Fax </td>
    <td bgcolor="#CCCCCC"> <input name="fax" type="text" id="fax4" size="15" maxlength="20" <?php echo "value='".$result['fax']."'"; ?> ></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td bgcolor="#CCCCCC">E-mail address </td>
    <td bgcolor="#CCCCCC"> <input name="email_address" type="text" id="email_address3" size="30" maxlength="100" <?php echo "value='".$result['email_address']."'"; ?> ></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td bgcolor="#CCCCCC">E-address (2) </td>
    <td bgcolor="#CCCCCC"> <input name="email_address_2" type="text" id="email_address_24" size="30" maxlength="100" <?php echo "value='".$result['email_address_2']."'"; ?>  ></td>
  </tr>
  <tr bgcolor="#BB0000"> 
    <td colspan="3"><font face="arial"><b>Home Address</td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td bgcolor="#CCCCCC">Address (line 1) </td>
    <td bgcolor="#CCCCCC"> <input name="address_line1" type="text" id="address_line13" size="25" maxlength="25" <?php echo "value='".$result['address_line1']."'"; ?> ></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td bgcolor="#CCCCCC">Address (line 2) </td>
    <td bgcolor="#CCCCCC"> 
      <input name="address_line2" type="text" id="address_line24" size="25" maxlength="25" <?php echo "value='".$result['address_line2']."'"; ?> ></td>
  </tr>
  <tr> 
    <td align="center">&nbsp;<?php if($fields["city"]==false){echo "<font color=#3333FF><b>*</b></font>";}?></td>
    <td colspan="2" bgcolor="#CCCCCC"> <p>City
        <input name="city" type="text" id="city3" size="15" maxlength="15" <?php echo "value='".$result['city']."'"; ?> >State

        <input name="state" type="text" id="state3" size="4" maxlength="2" <?php echo "value='".$result['state']."'"; ?> >Zip
         
        <input name="zip" type="text" id="zip3" size="12" maxlength="10" <?php echo "value='".$result['zip']."'"; ?> >
        <br>County (optional) 
        <input name="county" type="text" id="county" size="8" maxlength="20" <?php echo "value='".$result['county']."'"; ?> />
      </p></td>
  </tr>
  <tr bgcolor="#BB0000"> 
    <td colspan="3"><font face="arial"><b>Work Address (optional)</b></font></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td bgcolor="#CCCCCC" >Address (line 1) </td>
    <td bgcolor="#CCCCCC"> <input name="work_address_line1" type="text" id="address_line13" size="25" maxlength="25" <?php echo "value='".$result['work_address_line1']."'"; ?> ></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td bgcolor="#CCCCCC">Address (line 2) </td>
    <td bgcolor="#CCCCCC"> 
      <input name="work_address_line2" type="text" id="address_line24" size="25" maxlength="25" <?php echo "value='".$result['work_address_line2']."'"; ?> ></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td colspan="2" bgcolor="#CCCCCC"> <p>City 
        <input name="work_city" type="text" id="city3" size="15" maxlength="15" <?php echo "value='".$result['work_city']."'"; ?> >
        State 
        <input name="work_state" type="text" id="state3" size="4" maxlength="2" <?php echo "value='".$result['work_state']."'"; ?> >
        Zip 
        <input name="work_zip" type="text" id="zip3" size="12" maxlength="10" <?php echo "value='".$result['work_zip']."'"; ?> >
      </p></td>
  </tr>
  
  <tr bgcolor="#BB0000"> 
    <td colspan="3"><font face="arial"><b>User info</b></font></td>
  </tr>
  <tr> 
    <td width="50" align="center">&nbsp;<?php if($fields["username_m"]==false){echo "<font color=#3333FF><b>*</b></font>";}?></td>
    <td bgcolor="#CCCCCC">User name </td>
    <td bgcolor="#CCCCCC"> <input name="username_m" type="text" id="username_m3" size="20" maxlength="20" <?php echo "value='".$result['username_m']."'"; ?> ></td>
  </tr>
  <tr> 
    <td align="center">&nbsp;<?php if($fields["password"]==false){echo "<font color=#3333FF><b>*</b></font>";}?></td>
    <?php
   if($_GET[ID] && !$_GET[password])
   {
     echo "<td bgcolor='#CCCCCC' colspan='2'>Password stored. <a href='member_interface.php?action=edit&ID=$_GET[ID]&&password=edit'>Click here</a> to edit. <input type='hidden' name='password' value='".$result['password']."' /></td>";
  }else{
    echo "<td bgcolor='#CCCCCC'";
    if($fields["password"]==false);
    echo ">Password</td>";
    echo "<td bgcolor='#CCCCCC'> <input name='password' type='password' id='password3' size='15' maxlength='25'> (min 6 characters, no spaces)</td>";
  } ?>
  </tr>
  <?php 
  if(!$_GET[ID] || $_GET[password])
  {
    echo "<tr><td align=center>&nbsp"; if($fields["password_r"]==false){echo "<font color=#3333FF><b>*</b></font>";} echo "</td><td bgcolor='#CCCCCC'";
    if($fields["password_r"]==false);
    echo ">Repeat password </td>
      <td bgcolor='#CCCCCC'><input name='password_r' type='password' id='password_r4' size='15' maxlength='25'></td></tr>";
   }?>
  <tr bgcolor="#BB0000"> 
    <td colspan="3"><font face="arial"><b>Account Info</b></font></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td colspan="2" bgcolor="#CCCCCC"> <input name="no_postal_mail" type="checkbox" id="no_postal_mail2" value="1" <?php if($result['no_postal_mail']==1){ echo "checked";} ?> >
      Don&#146;t send postal mail</td>
  </tr>
  <!--<tr> 
    <td>&nbsp;</td>
    <td bgcolor="#CCCCCC">Amount owed </td>
    <td bgcolor="#CCCCCC">$<input name="membership_type_id" type="text" id="membership_type_id3" size="10" maxlength="10" <?php //echo "value='".$result['membership_type_id']."'"; ?> ><input name="membership_date" type="hidden" id="membership_date4" <?php //echo "value='".date("Y-m-d")."'"; ?>  >--><!-- the above tag places a hidden field in the form with the properly formatted date.  This offers the possibility of allowing the user to edit the date in future scripts -->
  <!--</td>
  </tr>-->
 
  <?php 
  if($_GET[ID])
  {
  echo "<tr><td>&nbsp;</td><td colspan='2' bgcolor='#CCCCCC'><input name='membership_discontinued' type='checkbox' id='membership_discontinued' value='1' ";
     if($result['membership_discontinued']==1)
   {
      echo "checked";
    } 
   echo " />Membership discontinued </td></tr>";
  }
   ?>
   <tr bgcolor="#BB0000"> 
    <td colspan="3"><font face="arial"><b>Producer Information (optional)</b></font></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td bgcolor="#CCCCCC">Producer ID<br>(5 letters)</td>
    <td bgcolor="#CCCCCC">
  <?php 
  if(!$p_result['producer_id'])
  {
    echo "<input name='new_producer_id' type='text' size='8' maxlength='5'>";
  }else{
    echo $p_result['producer_id']."<input name='producer_id' type='hidden' value='".$p_result['producer_id']."' />";
  }?>
  </td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td bgcolor="#CCCCCC">Business name </td>
    <td bgcolor="#CCCCCC"> <input name="business_name" type="text" id="business_name4" size="30" maxlength="50" <?php echo "value='".$result['business_name']."'"; ?> ></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td bgcolor="#CCCCCC">Active Status</td>
    <td bgcolor="#CCCCCC"> 
      <input name="donotlist_producer" type="radio" value="0" checked <?php if($p_result['donotlist_producer']!=1){ echo "checked"; } ?> />
      List <br>
      <input type="radio" name="donotlist_producer" value="1" <?php if($p_result['donotlist_producer']==1){ echo "checked"; } ?> >
      Do not list</td>
  </tr>
 
   
  
  <tr> 
    <td>&nbsp;</td>
    <td bgcolor="#CCCCCC">Homepage </td>
    <td bgcolor="#CCCCCC">http://
<input name="home_page" type="text" id="home_page3" size="30" <?php echo "value='".$result['home_page']."'"; ?> ></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td bgcolor="#CCCCCC">Toll free number </td>
    <td bgcolor="#CCCCCC"> <input name="toll_free" type="text" id="toll_free3" size="15" maxlength="20" <?php echo "value='".$result['toll_free']."'"; ?> ></td>
  </tr>
</table>
<div align="center">
  <input type="hidden" name="member_id" <?php echo "value='".$_GET[ID]."'"; ?> >
  <input name="reset" type="reset" id="reset" value="Clear form">
  <input type="submit" name="Submit" 
<?php
  if($_GET[ID])
  {
  echo "value='Update Entry'";
  }else{
    echo "value='Add Member'";
  }  
?> />
  </div>
</form>

  </td></tr>
</table>
<br>

</div>