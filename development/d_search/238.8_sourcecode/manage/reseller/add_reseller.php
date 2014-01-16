<?php
  include ("class/reseller.php"); 
  include("islogin_admin.php");
  $class = new reseller();
  $class->dbconn();
  if($_POST['act']=="add")
  {
      if(get_magic_quotes_gpc())
	  {  
	    $strName = trim($_POST['strName']);
		$uname = trim($_POST['uname']);
		$passwd = trim($_POST['passwd']);
		$strStatus = trim($_POST['strStatus']);
		$country = trim($_POST['country']);
		$email = trim($_POST['email']);
		$Discount = trim($_POST['Discount']);
	  }		
	  else
	  {
	    $strName = addslashes(trim($_POST['strName']));
		$uname = addslashes(trim($_POST['uname']));
		$passwd = addslashes(trim($_POST['passwd']));
		$strStatus = addslashes(trim($_POST['strStatus']));
		$country = addslashes(trim($_POST['country']));
		$email = addslashes(trim($_POST['email']));
		$Discount = addslashes(trim($_POST['Discount']));
	  }
	  $regdate = date("Y-m-d H:i:s",time());
	  $level=2;
	  $result = $class->AddReseller($strName,$uname,$passwd,$strStatus,$country,$regdate,$email,$level,$Discount);
	  if($result!="failed")
	    header("location:admin.php"); 
  }
  include("header.php");
?>
<table width="98%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="right"><a href="admin.php">Reseller List</a> </td>
  </tr>
</table>
<?php
   if(!$result)
   {
?>
<form name="form1" method="post" action="add_reseller.php">
  <table width="500" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#999999">
    <tr>
      <td width="165" height="25" align="right" bgcolor="#FFFFFF"><strong>Reseller Name:</strong></td>
      <td width="432" height="25" bgcolor="#FFFFFF">&nbsp;
          <input name="strName" type="text" id="strName" size="40"></td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>Username:</strong></td>
      <td height="25" bgcolor="#FFFFFF">&nbsp;
          <input name="uname" type="text" id="uname" size="40"></td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>Password:</strong></td>
      <td height="25" bgcolor="#FFFFFF">&nbsp;
          <input name="passwd" type="text" id="passwd" size="40"></td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>Status:</strong></td>
      <td height="25" bgcolor="#FFFFFF">&nbsp;
          <select name="strStatus" id="strStatus">
            <option value="active" selected>Active</option>
            <option value="Inactive">Inactive</option>
        </select></td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>Email:</strong></td>
      <td height="25" bgcolor="#FFFFFF">&nbsp;
          <input name="email" type="text" id="email" size="40"></td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>Country:</strong></td>
      <td height="25" bgcolor="#FFFFFF">&nbsp;
          <input name="country" type="text" id="country"></td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>Discount:</strong></td>
      <td height="25" bgcolor="#FFFFFF"> &nbsp;
      <input name="Discount" type="text" id="Discount" size="5" /></td>
    </tr>
  </table>
  <table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td height="30" align="center"><input type="submit" name="Submit" value="Submit">
      <input name="act" type="hidden" id="act" value="add">&nbsp;&nbsp;
      <input type="reset" name="Submit2" value="Reset"></td>
    </tr>
  </table>
</form>
<?php
}
else
{
?>
<table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td height="200" align="center"><strong>UserName already exist!</strong></td>
    </tr>
</table>
<?php }?>
 <?php include("bottom.php");?>
