<?php
  include ("class/reseller.php"); 
  include("islogin_admin.php");
  $class = new reseller();
  $class->dbconn();
  $intID = $_REQUEST['intID'];  
  if(!$act)
  {
     $rows = $class->ResellerList($intID);
	 //print_r($rows);
  }
  
  if($_POST['act']=="add")
  {
      if(get_magic_quotes_gpc())
	  {  
	    $strName = trim($_POST['strName']);
		$passwd = trim($_POST['passwd']);
		$strStatus = trim($_POST['strStatus']);
		$country = trim($_POST['country']);
		$email = trim($_POST['email']);
		$Discount = trim($_POST['Discount']);
	  }		
	  else
	  {
	    $strName = addslashes(trim($_POST['strName']));
		$passwd = addslashes(trim($_POST['passwd']));
		$strStatus = addslashes(trim($_POST['strStatus']));
		$country = addslashes(trim($_POST['country']));
		$email = addslashes(trim($_POST['email']));
		$Discount = addslashes(trim($_POST['Discount']));
	  }
     $class->UpdateReseller($intID,$strName,$passwd,$strStatus,$country,$email,$Discount);
	 echo "<script>opener.location.reload();window.close();</script>";
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Update Reseller</title>
<style type="text/css">
<!--
body,td,th {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
}
body {
	margin-left: 0px;
	margin-top: 10px;
	margin-right: 0px;
	margin-bottom: 10px;
}
-->
</style></head>

<body>
<form name="form1" method="post" action="update_reseller.php">
  <table width="500" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#999999">
    <tr>
      <td width="165" height="25" align="right" bgcolor="#FFFFFF"><strong>Reseller 
        Name :</strong></td>
      <td width="432" height="25" bgcolor="#FFFFFF">&nbsp;
          <input name="strName" type="text" id="strName" value="<?php echo $rows[0]['strName'];?>" size="40"></td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>Username:</strong></td>
      <td height="25" bgcolor="#FFFFFF">&nbsp;&nbsp;<?php echo $rows[0]['uname'];?></td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>Password:</strong></td>
      <td height="25" bgcolor="#FFFFFF">&nbsp;
          <input name="passwd" type="text" id="passwd" value="<?php echo $rows[0]['passwd'];?>" size="40"></td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>Status:</strong></td>
      <td height="25" bgcolor="#FFFFFF">&nbsp;
          <select name="strStatus" id="strStatus">
		     <?php 
			 if($rows[0]['strStatus']=="Active")
			   echo "<option value=\"active\" selected>Active</option>";			 
			 else
			   echo "<option value=\"active\" >Active</option>";
			 
			 if($rows[0]['strStatus']=="Inactive")
			   echo "<option value=\"Inactive\" selected>Inactive</option>";
			 else
			   echo "<option value=\"Inactive\">Inactive</option>";
			 ?>
        </select></td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>Email:</strong></td>
      <td height="25" bgcolor="#FFFFFF">&nbsp;
          <input name="email" type="text" id="email" value="<?php echo $rows[0]['email'];?>" size="40"></td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>Country:</strong></td>
      <td height="25" bgcolor="#FFFFFF">&nbsp;
          <input name="country" type="text" id="country" value="<?php echo $rows[0]['country'];?>"></td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>Discount:</strong></td>
      <td height="25" bgcolor="#FFFFFF">&nbsp;
          <input name="Discount" type="text" id="Discount" value="<?php echo $rows[0]['Discount'];?>" size="5" /></td>
    </tr>
  </table>
  <table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td height="30" align="center"><input type="submit" name="Submit" value="Submit">
      <input name="act" type="hidden" id="act" value="add">&nbsp;
      <input name="intID" type="hidden" id="intID" value="<?php echo $intID;?>" />
      &nbsp;
      <input type="reset" name="Submit2" value="Reset"></td>
    </tr>
  </table>
</form>
</body>
</html>
