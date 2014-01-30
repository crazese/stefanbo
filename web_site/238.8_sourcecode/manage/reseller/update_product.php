<?php
  include ("class/reseller.php"); 
  include("islogin_admin.php");
  $class = new reseller();
  $class->dbconn();
  $intID = $_REQUEST['intID'];  
  if(!$act)
  {
     $rows = $class->GetProductlist($intID);
	 //print_r($rows);
  }
  if($_POST['act']=="add")
  {
     
      if(get_magic_quotes_gpc())
	  {   
	      $ProductName = trim($_POST['ProductName']);
		  $SoftwareName = trim($_POST['SoftwareName']);
		  $Version = trim($_POST['Version']);
		  $Website = trim($_POST['Website']);
		  $FileName = trim($_POST['FileName']);
		  $UnlockType = trim($_POST['UnlockType']);
		  $PriceUSD = trim($_POST['PriceUSD']);		  
	  } 
	  else
	  {
	      $ProductName = addslashes(trim($_POST['ProductName']));
		  $SoftwareName = addslashes(trim($_POST['SoftwareName']));
		  $Version = addslashes(trim($_POST['Version']));
		  $Website = addslashes(trim($_POST['Website']));
		  $FileName = addslashes(trim($_POST['FileName']));
		  $UnlockType = addslashes(trim($_POST['UnlockType']));
		  $PriceUSD = addslashes(trim($_POST['PriceUSD']));			      
	  }	 
	  $class->UpdateProduct($intID,$ProductName,$SoftwareName,$Version,$Website,$FileName,$UnlockType,$PriceUSD);
	  echo "<script>opener.location.reload();window.close();</script>";	 
  }
  
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Update product</title>
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

<body><form name="form1" method="post" action="update_product.php">

  <table width="500" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#999999">
    <tr>
      <td width="165" height="25" align="right" bgcolor="#FFFFFF"><strong>Product Name:</strong></td>
      <td width="432" height="25" bgcolor="#FFFFFF">&nbsp;
          <input name="ProductName" type="text" id="ProductName" value="<?php echo $rows[0]['ProductName'];?>" size="40"></td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>
      SoftwareName:</strong></td>
      <td height="25" bgcolor="#FFFFFF">&nbsp;
          <input name="SoftwareName" type="text" id="SoftwareName" value="<?php echo $rows[0]['SoftwareName'];?>" size="40"></td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>Version:</strong></td>
      <td height="25" bgcolor="#FFFFFF">&nbsp;
          <input name="Version" type="text" id="Version" value="<?php echo $rows[0]['Version'];?>" size="5"></td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>
      Website:</strong></td>
      <td height="25" bgcolor="#FFFFFF"> &nbsp;<input name="Website" type="radio" value="1" <?php if($rows[0]['Website']==1) echo "checked"; ?> >
      Sothink
        <input name="Website" type="radio" value="2" <?php if($rows[0]['Website']==2) echo "checked";?>>
      Sothinkmedia</td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>FileName:</strong></td>
      <td height="25" bgcolor="#FFFFFF">&nbsp;
          <input name="FileName" type="text" id="FileName" value="<?php echo $rows[0]['FileName'];?>" size="40"></td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>UnlockType:</strong></td>
      <td height="25" bgcolor="#FFFFFF">&nbsp; 
	          <select name="UnlockType" id="UnlockType">
                <option value="1" <?php if($rows[0]['UnlockType']==1) echo "selected";?>>Reg Code</option>
				<option value="2" <?php if($rows[0]['UnlockType']==2) echo "selected";?>>Full Version</option>
                <option value="3" <?php if($rows[0]['UnlockType']==3) echo "selected";?>>Reg Code + File</option>                
        </select></td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>Product Price:</strong></td>
      <td height="25" bgcolor="#FFFFFF">&nbsp;
      <input name="PriceUSD" type="text" id="PriceUSD" value="<?php echo $rows[0]['PriceUSD'];?>" size="5"></td>
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
