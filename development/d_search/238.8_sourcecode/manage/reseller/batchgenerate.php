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
		 $email = str_replace("\n","",$email);
	     //$username = trim($_POST['username']);
	  }
	  else
	  {
	     $intID = addslashes($_POST['ProductName']);
	     $email = addslashes(trim($_POST['email']));
		 $email = str_replace("\n","",$email);
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
		 {
		    $email_key = explode(",",$email);
			for($k=0;$k<count($email_key);$k++)
			{
			   $class->GenerateCode($email_key[$k],$strSoftWareName,$strVer,$ProductName,$PriceUSD,$Discount,$Regdate,$OrderNum,$ResellerID,$ProductID,$uname);
			}
		 }
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

<form name="form1" method="post" action="batchgenerate.php">
  <table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td align="right">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
  </table>
  <table width="630" border="0" align="center" cellpadding="0" cellspacing="1" bordercolor="#999999" bgcolor="#CCCCCC">
    
    <tr>
      <td width="31%" height="25" align="right" valign="top" bgcolor="#FFFFFF"><br>
      User Email<span class="style1">(Register name)</span>:<br></td>
      <td width="69%" height="25" bgcolor="#FFFFFF" style="padding-left:10px;"><br>
        test@test.com,test2@sothink.com<br><br><label>
        <textarea name="email" cols="60" rows="20" id="email" style="font-size:11px;"><?php echo $email;?></textarea>
        </label>
      <label></label></td>
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
      <td height="25" colspan="2" bgcolor="#FFFFFF"><?php echo "<strong>Product Name: ".$ProductName."</strong><br><br>"; for($j=0;$j<count($rows_purchase);$j++) { echo "Registration Name: ".$rows_purchase[$j]['email']."<br>Registration Key: ".$rows_purchase[$j]['Regcode']."<br>Download Path:<a href=http://www2.sothink.com/download/".$FileName.">http://www2.sothink.com/download/".$FileName."</a><hr><br>"; }?></td>
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