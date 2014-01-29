<?php    
 header   ("Expires:   Mon,   26   Jul   1997   05:00:00   GMT");    
  header   ("Last-Modified:   "   .   gmdate("D,d   M   YH:i:s")   .   "   GMT");    
  header   ("Cache-Control:   no-cache,   must-revalidate");            
  header   ("Pragma:   no-cache");            
  header   ("Content-type:   application/x-msexcel");    
  //header   ("Content-Disposition:filename=EmplList.xls"   );      
  header   ("Content-Description:   PHP/INTERBASE   Generated   Data"   );  
  /*header("Content-type:application/octet-stream");  */  
  header("Content-Disposition:filename=uninstall.xls");    
  //    
  //   the   next   lines   demonstrate   the   generation   of   the   Excel   stream    
  //  
  include("excel.inc.php");  
  xlsBOF();       //   begin   Excel   stream    
 
  xlsWriteLabel(0,0,"ID");  //   write   a   label   in   A1,   use   for   dates   too 
  xlsWriteLabel(0,1,"Name");       
  xlsWriteLabel(0,2,"Comment");   
  xlsWriteLabel(0,3,"SubmitDate");   
    $dbhost = "localhost";
	$dbusername = "sothink";
	$dbpass ="K2v3P494";
	$dbname = "uninstall";
	$connection = mysql_connect($dbhost,$dbusername,$dbpass);
	mysql_select_db($dbname,$connection);

	$query = "select comment.ID as ID, product.Name as Name, comment.Comment as Comment, comment.SubmitDate as SubmitDate							from `comment`,product where comment.ProductID=product.MajorNo && comment.Comment !='' && product.Name like '%$productName%' order by comment.ID DESC";
	$result=mysql_query($query);
	$r=1;
	$r1=1;
	$r2=1;
	$r3=1;
	while($rows=mysql_fetch_array($result))
	{
	    xlsWriteLabel($r++,0,$rows['ID']);
		xlsWriteLabel($r1++,1,$rows['Name']);
		xlsWriteLabel($r2++,2,$rows['Comment']);
		xlsWriteLabel($r3++,3,$rows['SubmitDate']);
		//echo ($i++)."<br>";
	} 
 // xlsWriteNumber(0,1,9999);     //   write   a   number   B1    
  xlsEOF();   //   close   the   stream  
  ?>   