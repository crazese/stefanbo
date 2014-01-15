<?php
  include ("class/reseller.php"); 
  include("islogin_admin.php");
  $class = new reseller();
  $class->dbconn();
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
	  $Regdate = date('Y-m-d H:i:s',time());
	  $class->AddProdcut($ProductName,$SoftwareName,$Version,$Website,$FileName,$UnlockType,$PriceUSD,$Regdate);
	  header("location:product_list.php");
  }
  include("header.php");  
?>  
<table width="98%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td align="right"><a href="product_list.php">Product List</a> </td>
    </tr>
    <tr>
      <td align="right">&nbsp;</td>
    </tr>
 </table>
<form name="form1" method="post" action="add_product.php">

  <table width="500" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#999999">
    <tr>
      <td width="165" height="25" align="right" bgcolor="#FFFFFF"><strong>Product Name:</strong></td>
      <td width="432" height="25" bgcolor="#FFFFFF">&nbsp;
          <input name="ProductName" type="text" id="ProductName" size="40"></td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>
      SoftwareName:</strong></td>
      <td height="25" bgcolor="#FFFFFF">&nbsp;
          <input name="SoftwareName" type="text" id="SoftwareName" size="40"></td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>Version:</strong></td>
      <td height="25" bgcolor="#FFFFFF">&nbsp;
          <input name="Version" type="text" id="Version" size="5"></td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>
      Website:</strong></td>
      <td height="25" bgcolor="#FFFFFF"> &nbsp;<input name="Website" type="radio" value="1" checked>
      Sothink
        <input name="Website" type="radio" value="2">
      Sothinkmedia</td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>FileName:</strong></td>
      <td height="25" bgcolor="#FFFFFF">&nbsp;
          <input name="FileName" type="text" id="FileName" size="40"></td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>UnlockType:</strong></td>
      <td height="25" bgcolor="#FFFFFF">&nbsp; 
	          <select name="UnlockType" id="UnlockType">
                <option value="1" selected>Reg Code</option>
				<option value="2">Full Version</option>
                <option value="3">Reg Code + File</option>                
          </select></td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF"><strong>Product Price:</strong></td>
      <td height="25" bgcolor="#FFFFFF">&nbsp;
      <input name="PriceUSD" type="text" id="PriceUSD" size="5"></td>
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
<?php include("bottom.php");?>
