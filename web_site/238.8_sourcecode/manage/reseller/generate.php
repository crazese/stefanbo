<?php
  include ("class/reseller.php"); 
  include("islogin.php");
  $class = new reseller();
  $class->dbconn();
  $rows_product = $class->GetProductlist();
  $uname = $_COOKIE['cookie']['username'];
  $psw = $_COOKIE['cookie']['password'];
  if($_POST['act']=="add")
  {
     if(get_magic_quotes_gpc())
	 {
	     $intID = $_POST['ProductName'];
	     $email = trim($_POST['email']);
	     //$username = trim($_POST['username']);
	  }
	  else
	  {
	     $intID = addslashes($_POST['ProductName']);
	     $email = addslashes(trim($_POST['email']));
	     //$username = addslashes(trim($_POST['username']));
	  }
	  $userinfo = $class->GetUserinfo($uname,$psw);
	  $ResellerID = $userinfo['intID'];
	   //echo $ResellerID; 
	  $Regdate = date("Y-m-d H;i:s",time());
	  $OrderNum = $class->encode();
	  if($ResellerID)
	  {
	     $Productinfo = $class->GetProductlist($intID);
		 $strSoftWareName = $Productinfo[0]['SoftwareName'];
		 $ProductName = $Productinfo[0]['ProductName'];
		 $strVer = $Productinfo[0]['Version'];
		 $FileName = $Productinfo[0]['FileName'];
		 $PriceUSD = round($userinfo['Discount']/100*$Productinfo[0]['PriceUSD'],2);
		 $Discount = $userinfo['Discount'];
		 $ProductID = $intID;
		 if($Productinfo[0]['UnlockType'] == 1 && $uname!="" && $email!="")
		    $class->GenerateCode($email,$strSoftWareName,$strVer,$ProductName,$PriceUSD,$Discount,$Regdate,$OrderNum,$ResellerID,$ProductID,$uname);
	     $rows_purchase= $class->Getorder($OrderNum);
		 //print_r($rows_purchase);
	  }
  }
  include("header.php");
?>
<style type="text/css">
<!--
.style1 {color: #FF0000}
-->
</style>

<form name="form1" method="post" action="generate.php">
  <table width="630" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td height="25" align="left"><a href="batchgenerate.php" class="navigation"></a></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
  </table>
  <table width="630" border="0" align="center" cellpadding="0" cellspacing="1" bordercolor="#999999" bgcolor="#CCCCCC">
    
    <tr>
      <td width="25%" height="25" align="right" bgcolor="#FFFFFF">User Email:</td>
      <td height="25" bgcolor="#FFFFFF" style="padding-left:10px;"><input name="email" type="text" id="email" value="<?=$email?>" size="32" maxlength="128"> 
        <span class="style1">Register name</span>       </td>
    </tr>
    <tr>
      <td height="25" align="right" bgcolor="#FFFFFF">Product:</td>
      <td height="25" bgcolor="#FFFFFF" style="padding-left:10px;">
	  <select name="ProductName" id="ProductName">
	   <?php 
	   for($i=0;$i<count($rows_product);$i++)
	   {
	      if($rows_product[$i]['intID']==$intID)
		  {
		      echo "<option value=\"".$rows_product[$i]['intID']."\" selected>".$rows_product[$i]['ProductName']."</option>";
		  }
		  else
		  {
		      echo "<option value=\"".$rows_product[$i]['intID']."\">".$rows_product[$i]['ProductName']."</option>"; 
		  }
	   }
	   ?>
      </select>      </td>
    </tr>
    
    <tr>
      <td height="25" colspan="2" align="center" bgcolor="#FFFFFF"><input type="submit" name="action" value="Generate Code">
        <input name="act" type="hidden" id="act" value="add"></td>
    </tr>
    <?php 
	if($_POST['act'] == "add" && $ResellerID)
	{
	?>
	<tr>
      <td height="25" colspan="2" bgcolor="#FFFFFF"><?php for($j=0;$j<count($rows_purchase);$j++) { echo "<br><strong>Product Name: ".$ProductName."</strong><br><br>Registration Name: ".$rows_purchase[$j]['email']."<br>Registration Key: ".$rows_purchase[$j]['Regcode']."<br>Download Path:<a href=http://www2.sothink.com/download/".$FileName.">http://www2.sothink.com/download/".$FileName."</a><br><br>"; }?></td>
    </tr>
	<?php
	}
	?>
  </table>
  <table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td align="center">Contact <a href="mailto:reseller@sothink.com">reseller@sothink.com</a> if you have any             questions.</td>
    </tr>
  </table>
</form>
<?php include("bottom.php");?>