<?php    
 header   ("Expires:   Mon,   26   Jul   1997   05:00:00   GMT");    
  header   ("Last-Modified:   "   .   gmdate("D,d   M   YH:i:s")   .   "   GMT");    
  header   ("Cache-Control:   no-cache,   must-revalidate");            
  header   ("Pragma:   no-cache");            
  header   ("Content-type:   application/x-msexcel");    
  //header   ("Content-Disposition:filename=EmplList.xls"   );      
  header   ("Content-Description:   PHP/INTERBASE   Generated   Data"   );  
  /*header("Content-type:application/octet-stream");  */  
  header("Content-Disposition:filename=freeUser.xls");    
  //    
  //   the   next   lines   demonstrate   the   generation   of   the   Excel   stream    
  //  
  include("excel.inc.php");  
  xlsBOF();       //   begin   Excel   stream    
 
  xlsWriteLabel(0,0,"Id");  //   write   a   label   in   A1,   use   for   dates   too 
  xlsWriteLabel(0,1,"Name");       
  xlsWriteLabel(0,2,"ProductId");   
  xlsWriteLabel(0,3,"Email");   
  xlsWriteLabel(0,4,"Country");   
  xlsWriteLabel(0,5,"Time");   
  xlsWriteLabel(0,6,"The purpose you download this template is");  
  xlsWriteLabel(0,7,"How did you find us?");  
  xlsWriteLabel(0,8,"What's your major tool to make Flash movies?");  
  xlsWriteLabel(0,9,"Degree of familiarity with the production environment of Adobe Flash");  
  xlsWriteLabel(0,10,"AS version you are conversant with");  
  xlsWriteLabel(0,11,"Degree of familiarity with AS"); 
  xlsWriteLabel(0,12,"How many templates should a bundle contain in your"); 
  xlsWriteLabel(0,13,"The key factor that effects your decision is"); 
  xlsWriteLabel(0,14,"What is the most important feature of Flash Pioneer templates?"); 
  xlsWriteLabel(0,15,"Your occupation is"); 
  xlsWriteLabel(0,16,"What theme template you would like us to create?");
  xlsWriteLabel(0,17,"password");
    $dbhost = "localhost";
	$dbusername = "sothink";
	$dbpass ="K2v3P494";
	$dbname = "template";
	$connection = mysql_connect($dbhost,$dbusername,$dbpass);
	mysql_select_db($dbname,$connection);
	$query="select * from free where registed='y' order by id desc";
	$result=mysql_query($query);
    $i=1;
	$j=1;
	$k=1;
	$l=1;
	$m=1;
	$n=1;
	$r=1;
	$r1=1;
	$r2=1;
	$r3=1;
	$r4=1;
	$r5=1;
	$r6=1;
	$r7=1;
	$r8=1;
	$r9=1;
	$r10=1;
	$r11=1;
	while($rows=mysql_fetch_array($result))
	{
	    xlsWriteLabel($i++,0,$rows['id']);
		xlsWriteLabel($j++,1,$rows['name']);
		xlsWriteLabel($k++,2,$rows['productId']);
		xlsWriteLabel($l++,3,$rows['email']);
		xlsWriteLabel($m++,4,$rows['country']);
		xlsWriteLabel($n++,5,$rows['time']);
		xlsWriteLabel($r++,6,$rows['purpose']);
		xlsWriteLabel($r1++,7,$rows['findus']);
		xlsWriteLabel($r2++,8,$rows['majortool']);
		xlsWriteLabel($r3++,9,$rows['Degree']);
		xlsWriteLabel($r4++,10,$rows['version']);
		xlsWriteLabel($r5++,11,$rows['Degree_10']);
		xlsWriteLabel($r6++,12,$rows['templates']);
		xlsWriteLabel($r7++,13,$rows['keyfactor']);
		xlsWriteLabel($r8++,14,$rows['feature']);
		xlsWriteLabel($r9++,15,$rows['occupation']);
		xlsWriteLabel($r10++,16,$rows['theme']);
		xlsWriteLabel($r11++,17,$rows['password']);
		//echo ($i++)."<br>";
	} 
 // xlsWriteNumber(0,1,9999);     //   write   a   number   B1    
  xlsEOF();   //   close   the   stream  
  ?>   
