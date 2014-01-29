<?php
  include ("class/reseller.php"); 
  include("islogin.php");
  $class = new reseller();
  $class->dbconn(); 
  $uname = $_COOKIE['cookie']['username'];
  $psw = $_COOKIE['cookie']['password'];
  if($_POST['act']=="update") 
  {
      if(get_magic_quotes_gpc())
	  {
         $strName = trim($_POST['companyname']);
		 $password = trim($_POST['password']);	     
	  }
	  else
	  {
         $strName = addslashes(trim($_POST['companyname']));
		 $password = addslashes(trim($_POST['password']));		     
	  }
	  
	 $class->Updateprofile($uname,$psw,$strName,$password);
  }
  $userinfo = $class->GetUserinfo($uname,$psw);
  //print_r($userinfo);
  include("header.php");
?>
<script language="JavaScript">
<!--
function validateform()
{
	if (document.updateform.password.value != document.updateform.password1.value)
	{
		alert("Password not match.");
		document.updateform.password.focus();
		return false;
	}
	else
	{
		return true;
	}
}
//-->
</script>
<table width="800" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
    <td> 
      <div align="center">
        <form method="post" name="updateform" action="profile.php" onSubmit="return validateform();">
          <table width="96%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
            </tr>
          </table>
          <table width="90%" border="0" cellpadding="0" cellspacing="1" bgcolor="#999999">
          <tr> 
            <td width="29%" height="25" bgcolor="#FFFFFF">Company</td>
            <td width="71%" height="25" bgcolor="#FFFFFF">
                <input type="text" name="companyname" maxlength="128" size="64" value="<?=$userinfo["strName"]?>">
            </td>
          </tr>
          <tr> 
            <td width="29%" height="25" bgcolor="#FFFFFF">Reseller's Login Name</td>
            <td width="71%" height="25" bgcolor="#FFFFFF"><?=$userinfo["uname"]?></td>
          </tr>
          <tr> 
            <td width="29%" height="25" bgcolor="#FFFFFF">Password</td>
            <td width="71%" height="25" bgcolor="#FFFFFF">
              <input type="password" name="password" size="32" maxlength="32" value="<?=$userinfo["passwd"]?>">
            </td>
          </tr>
          <tr> 
            <td height="25" bgcolor="#FFFFFF">Passwrod Confirm</td>
            <td height="25" bgcolor="#FFFFFF">
              <input type="password" name="password1" size="32" maxlength="32" value="<?=$userinfo["passwd"]?>">
            </td>
          </tr>
          <tr bgcolor="#CCCCCC" align="center"> 
            <td colspan="2" bgcolor="#FFFFFF">
              <input type="submit" name="action" value="Update">
              <input name="act" type="hidden" id="act" value="update">            </td>
          </tr>
        </table>
		  <table width="96%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td height="25">&nbsp;</td>
            </tr>
            <tr>
              <td align="center">Contact <a href="mailto:reseller@sothink.com">reseller@sothink.com</a> if you have any 
          questions.</td>
            </tr>
          </table>
        </form>
        <p>&nbsp;</p>
      </div>
    </td>
  </tr>
</table>
<?php include("bottom.php");?>