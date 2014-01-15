<?php
  header("Content-type: text/html; charset=gb2312");
  $s=$_GET['s']; //代理字符串
  $soid=$_GET['soid'];  //软件ID，便于有多软件的作者进行处理，生成对应软件的注册码
  $num=$_GET['num'];    //注册套数（即该订单内包含软件套数），作者应按约定格式返回对应数目的注册码。
  $name=$_GET['name'];  //注册姓名，用于KG生成注册码。
  //$machine=$_GET['machine']; //机器码（附加码），用于KG生成注册码。
  $password=$_GET['password']; //校验密码，即您填写的 Key Generator 密码，用于校验请求的合法。
  //$orid=$_GET['orid'];  //订单ID，供作者参考，可用于数据备份。
  //$time=$_GET['time'];  //下单时间，供作者参考，可用于数据备份。
  //$email=$_GET['email'];  //用户email地址，供作者参考，可用于数据备份。
  //请求生成注册码合法IP 61.129.32.23 
  $productinfo = array(33726=>"DecompilerCnPer",33827=>"DecompilerCnEnt",33826=>"DecompilerCnPro"); //33726个人版，33827企业版，33826专业版
  function GenerateCode($strSoftWareName,$regname)
  {
   	   $intKeyType=1;
	   $fingerprint="0";
	   $strVer = "5.0";
	   exec("/usr/bin/keymaker-pp-reseller $strSoftWareName $strVer $intKeyType $regname $fingerprint",$arycode,$ret);
	   $regcode1 = $arycode[0];
	   unset($arycode);
       sleep(1);
	   return $regcode1;			     
  }
  
  //if($password == "zzbpio4w" && $_SERVER['REMOTE_ADDR'] == "61.129.32.23")
  if($s == md5("zzbpio4w".$soid.$num.$name))
  {  
     for ($i=0;$i<$num;$i++)
     {
        $regcode .= "|_|".GenerateCode($productinfo[$soid],$name);
     }
	 echo "|_|OK".$regcode;
  }
  else
  {
     echo "error";
  }
?>
