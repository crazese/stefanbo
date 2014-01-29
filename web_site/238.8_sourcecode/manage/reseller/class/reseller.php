<?php
   class reseller
   {
	   var $dbuser = "sothink";
	   var $dbpassword = "K2v3P494";
	   var $database = "reseller";
	   var $dbserver = "localhost";
	   function dbconn()
	   {
	   	   @$dbconn = mysql_connect($this->dbserver,$this->dbuser,$this->dbpassword);
	   	   @$db = mysql_select_db($this->database,$dbconn);	   	   	
	   	   if (!$dbconn)
		   {
		      $string = array('error'=>'Connect server false');
		   }
		   if (!$db)
		   {
		      $string = array('error'=>'Select db false');
		   }
		   return $string;
	   }
	   
	   function login($username,$password)
	   {
	   	   $sql = "select * from Reseller where uname = '$username' && passwd = '$password'";
	   	   $result = mysql_query($sql);
	   	   $err=mysql_error();
	   	   if($err)
	   	     return "failed";
	   	   $rows = mysql_fetch_array($result);
           if($rows['uname']=="$username" && $rows['strStatus']=="active")	
             return $rows['level'];
           else 
             return "failed";     	      	
	   }
	   
	   function ResellerList($intID="",$order="",$itemname="")
	   {
	   	  if($intID != "")
	   	    $item = " && intID='$intID'";
	   	  if($order!="" && $itemname!="")
	   	    $item1 = "order by $itemname $order";
	   	  else 
	   	    $item1 = "order by regdate DESC";
	   	  $sql = "select * from Reseller where 1=1 $item $item1";
	   	  //echo $sql;	   	  
	   	  $result = mysql_query($sql);
	   	  while($array=mysql_fetch_array($result))
	   	  {
	   	  	$rows[] = $array;
	   	  }
	   	  return $rows;
	   }
	   
	   function UpdateSales($ResellerID)
	   {
	   	  	$rows_sales = $this->TotalSales($ResellerID,"","","","");
	   	  	$sales = $rows_sales[0];
	   	  	$sql = "update Reseller set sales='$sales' where intID = '$ResellerID'";
	   	  	mysql_query($sql);		
	   }
	   
	   function AddReseller($strName,$uname,$passwd,$strStatus,$country,$regdate,$email,$level,$Discount)
	   {
	   	  $sql = "select * from Reseller where uname = '$uname'";
	   	  $result = mysql_query($sql);
	   	  $check = mysql_num_rows($result);
	   	  if($check>=1)
	   	  {
              return "failed";
	   	  }
	   	  else 
	   	  {
	   	     $sql = "insert into Reseller(strName,uname,passwd,strStatus,country,regdate,email,level,Discount) values ('$strName','$uname','$passwd','$strStatus','$country','$regdate','$email','$level','$Discount')";
	   	      mysql_query($sql);
	   	  }	   	   
	   }
	   
	   function DeleteReseller($intID)
	   {
	   	  $sql = "delete from Reseller where intID = '$intID'";
	   	  mysql_query($sql);
	   }
	   
	   function UpdateReseller($intID,$strName,$passwd,$strStatus,$country,$email,$Discount)
	   {
	   	  $sql = "update Reseller set strName='$strName',passwd='$passwd',strStatus='$strStatus',country='$country',email='$email',Discount='$Discount' where intID='$intID'";
	   	  mysql_query($sql);
	   }
	   
	   function AddProdcut($ProductName,$SoftwareName,$Version,$Website,$FileName,$UnlockType,$PriceUSD,$Regdate)
	   {
	   	  $sql = "insert into Product_reseller(ProductName,SoftwareName,Version,Website,FileName,UnlockType,PriceUSD,Regdate) values ('$ProductName','$SoftwareName','$Version','$Website','$FileName','$UnlockType','$PriceUSD','$Regdate')";
	   	  mysql_query($sql);
	   }
	   
	   function GetProductlist($intID="")
	   {
	   	  if($intID!="")
	   	    $item = " && intID = '$intID'";
	   	  $sql = "select * from Product_reseller where 1=1 $item order by Regdate DESC";
	   	  $result = mysql_query($sql);
	   	  while ($array = mysql_fetch_array($result))
	   	  {
	   	  	$rows[] = $array;
	   	  }
	   	  return $rows;
	   }
	   
	   function GetWebsite($ID)
	   {
	   	  if ($ID==1)
	   	    return "Sothink";
	   	  if ($ID==2)
	   	    return "SothinkMedia";
	   }
	   
	   function GetUnlockTpye($ID)
	   {
	   	 if($ID==1)
	   	   return "Reg Code";
	   	 if($ID==2)
	   	   return "Full Version";
	   	 if($ID==3)
	   	   return "Reg Code + File";	   	   	   	  
	   }
	   
	   function UpdateProduct($intID,$ProductName,$SoftwareName,$Version,$Website,$FileName,$UnlockType,$PriceUSD)
	   {
	   	  $sql = "update Product_reseller set ProductName='$ProductName',SoftwareName='$SoftwareName',Version='$Version',Website='$Website',FileName='$FileName',UnlockType='$UnlockType',PriceUSD='$PriceUSD' where intID='$intID'";
	   	  mysql_query($sql);
	   }
	   
	   function DeleteProduct($intID)
	   {
	   	  $sql = "delete from Product_reseller where intID = '$intID'";
	   	  mysql_query($sql);
	   }
	   
	   function GenerateCode($email,$strSoftWareName,$strVer,$ProductName,$PriceUSD,$Discount,$Regdate,$OrderNum,$ResellerID,$ProductID,$username)
	   {
	   		$intKeyType=1;
			$fingerprint="0";
			exec("/var/www/keymaker-pp $strSoftWareName $strVer $intKeyType $email $fingerprint",$arycode,$ret);
			$regcode1 = $arycode[0];
			unset($arycode);
			$sql="insert into reseller_purchase(ProductName,PriceUSD,Discount,Regcode,Regdate,OrderNum,ResellerID,ProductID,username,email) values('$ProductName','$PriceUSD','$Discount','$regcode1','$Regdate','$OrderNum','$ResellerID','$ProductID','$username','$email')";
			//echo $strSoftWareName."<br>".$strVer."<br>".$email."<br>".$sql;
			$result=mysql_query($sql);
			sleep(1);			
	   }
	   
	   function encode()
       {
           $ordernum = date("Y-m-dH-i-s");
           $ordernum = str_replace("-","",$ordernum);
           $ordernum .= rand(1000,2000);
           return $ordernum;
       }
       
       function GetUserinfo($uname,$psw)
       {
       	  $sql = "select * from Reseller where uname = '$uname' && passwd = '$psw'";
       	  $result = mysql_query($sql);
       	  $rows = mysql_fetch_array($result);
       	  return $rows;
       }
       
       function Getorder($OrderNum)
       {
       	  $sql = "select * from reseller_purchase where OrderNum = '$OrderNum'";
       	  //echo $sql;
       	  $result = mysql_query($sql);
       	  while($array = mysql_fetch_array($result))
       	  {
       	  	$rows[] = $array;
       	  }
       	  return $rows;
       }
       
       function Updateprofile($uname,$psw,$strName,$password)
       {
       	  $sql = "update Reseller set strName = '$strName',passwd = '$password' where uname='$uname' && passwd = '$psw'";
       	  mysql_query($sql);
       }
       
       function GetPurchased($ResellerID,$start,$perpage,$ProductID,$email,$from,$end)
       {
       	  if($perpage!="")
       	    $item = "limit $start,$perpage";
       	  if($ProductID!="")
       	    $item1 = " && ProductID = '$ProductID'";
       	  if($email!="")
       	    $item2 = " && email like '%$email%'";
       	  if($from!="" && $from!=$end)
       	    $item3 = " && Regdate>'$from' && Regdate<'$end'";
       	  if($from==$end)
       	    $item3 = " && Regdate like '%$from%'";
       	  $sql = "select * from  reseller_purchase where ResellerID='$ResellerID' $item1 $item2 $item3 order by Regdate DESC $item";
       	  $result = mysql_query($sql);
       	  while($array = mysql_fetch_array($result))
       	  {
       	  	 $rows[] = $array;
       	  }
       	  return $rows;
       }
       
       function TotalSales($ResellerID,$ProductID,$email,$from,$end)
       {
       	  if($ProductID!="")
       	    $item1 = " && ProductID = '$ProductID'";
       	  if($email!="")
       	    $item2 = " && email like '%$email%'";
       	  if($from!="" && $from!=$end)
       	    $item3 = " && Regdate>'$from' && Regdate<'$end'";
       	  if($from==$end)
       	    $item3 = " && Regdate like '%$from%'";
       	  $sql = "select sum(PriceUSD) as `total`,count(PriceUSD) as `number` from  reseller_purchase where ResellerID='$ResellerID' $item1 $item2 $item3";
       	  $result = mysql_query($sql);
       	  $rows = mysql_fetch_row($result);
       	  return $rows;      	  
       }
   }
?>
