<?php
  header("Content-type: text/html; charset=gb2312");
  $s=$_GET['s']; //�����ַ���
  $soid=$_GET['soid'];  //���ID�������ж���������߽��д������ɶ�Ӧ�����ע����
  $num=$_GET['num'];    //ע�����������ö����ڰ������������������Ӧ��Լ����ʽ���ض�Ӧ��Ŀ��ע���롣
  $name=$_GET['name'];  //ע������������KG����ע���롣
  //$machine=$_GET['machine']; //�����루�����룩������KG����ע���롣
  $password=$_GET['password']; //У�����룬������д�� Key Generator ���룬����У������ĺϷ���
  //$orid=$_GET['orid'];  //����ID�������߲ο������������ݱ��ݡ�
  //$time=$_GET['time'];  //�µ�ʱ�䣬�����߲ο������������ݱ��ݡ�
  //$email=$_GET['email'];  //�û�email��ַ�������߲ο������������ݱ��ݡ�
  //��������ע����Ϸ�IP 61.129.32.23 
  $productinfo = array(33726=>"DecompilerCnPer",33827=>"DecompilerCnEnt",33826=>"DecompilerCnPro"); //33726���˰棬33827��ҵ�棬33826רҵ��
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
