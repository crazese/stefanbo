<?php
  include "islogin_admin.php";
  header   ("Expires:   Mon,   26   Jul   1997   05:00:00   GMT");    
  header   ("Last-Modified:   "   .   gmdate("D,d   M   YH:i:s")   .   "   GMT");    
  header   ("Cache-Control:   no-cache,   must-revalidate");            
  header   ("Pragma:   no-cache");            
  header   ("Content-type:   application/x-msexcel");    
  //header   ("Content-Disposition:filename=EmplList.xls"   );      
  header   ("Content-Description:   PHP/INTERBASE   Generated   Data"   );  
  //header("Content-type:application/octet-stream");    
  header("Content-Disposition:filename=".$_POST['uname']."_order.xls");   

  include ("class/reseller.php"); 
  include("excel.inc.php");
  $class = new reseller();
  $class->dbconn();   
  //$userinfo = $class->GetUserinfo($uname,$psw);
  $ResellerID =$_REQUEST['ResellerID'];
  $ProductID = $_REQUEST['ProductName'];
  $email = trim($_REQUEST['email']);
  $from = trim($_REQUEST['from']);
  $from2 = $from;
  $end = trim($_REQUEST['end']);
  $end2=$end;
  if($from=="" && $end!="")
    $from = $end;
  if($from!="" && $end=="")
    $end = $from;
  if ($end > $from)
     $end = $end ." 23:59:59";  
  if ($end<$from)
  {
     $end = $from." 23:59:59";
	 $from = $end2;
  }  
  $rows = $class->GetPurchased($ResellerID,"","",$ProductID,$email,$from,$end); 
/*  for($i=0;$i<count($rows);$i++)
  {
     echo $rows[$i]['ProductName']."<br>".$rows[$i]['email']."<br>".$rows[$i]['Regcode']."<br>".$rows[$i]['Regdate']."<br>".$rows[$i]['PriceUSD'];
  }*/
  //print_r($rows);
   xlsBOF(); 
   xlsWriteLabel(0,0,"Product Name"); 
   xlsWriteLabel(0,1,"Register Name");
   xlsWriteLabel(0,2,"Regcode");
   xlsWriteLabel(0,3,"RegDate");
   xlsWriteLabel(0,4,"Sales");
   
   $k=1;
   $k1=1;
   $k2=1;
   $k3=1;
   $k4=1;
   for($i=0;$i<count($rows);$i++)
   {
      xlsWriteLabel($k++,0,$rows[$i]['ProductName']);
	  xlsWriteLabel($k1++,1,$rows[$i]['email']);
	  xlsWriteLabel($k2++,2,$rows[$i]['Regcode']);
	  xlsWriteLabel($k3++,3,$rows[$i]['Regdate']);
	  xlsWriteLabel($k4++,4,$rows[$i]['PriceUSD']);
   }
   xlsEOF();
?>
