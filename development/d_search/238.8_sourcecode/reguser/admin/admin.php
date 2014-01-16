<?
  	include ("auth.php"); 
	include ("authconfig.php");  	

	$Auth = new auth();
	$nname = $_COOKIE['cookie']['username'];
	$passwd = $_COOKIE['cookie']['password'];
    //print_r($_COOKIE['cookie']['username']);
	//exit;
	// check cookie 
	if ($_COOKIE['cookie']['username']!="") 
	{
		// print "Cookie Set";
		// get password and username and check in database		
		$detail = $Auth->authenticate($_COOKIE['cookie']['username'], $_COOKIE['cookie']['password']);
		$Auth->addlog($Auth->RESELLERID, "Cookie Set, Check Failed: $nname,$passwd, res=".$detail);
	}
	else
	{
		// print "Cookie Not Set";
		$detail = 0;
		$Auth->addlog($Auth->RESELLERID, "Cookie Not Set");
	}

	 //print $detail;
	
	if ($detail != 1)
	{
		header("Location: $failure");
	}
?>
<html>
<head>
<title>Reseller Control Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF" text="#000000">
<p><font face="Arial, Helvetica, sans-serif" size="5"><b>Reseller Control Panel Admin </b></font></p>
<table width="400" border="1" cellspacing="0" cellpadding="5" bordercolor="#000000">
  <tr> 
    <td bgcolor="#0099CC" colspan="2"> 
	<div align="center">
	<b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFCC">Admin Panel </font></b>
	 </div></td>
  </tr>
  <tr> 
    <td bgcolor="#0099CC" valign="top" rowspan="2">
	<font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFCC">
	<b>Reseller Management</b></font></td>
    <td bgcolor="#FFFFCC" height="8"> 
	<font face="Verdana, Arial, Helvetica, sans-serif" size="2">
	<a href="authgroup.php">Groups</a></font></td>
  </tr>
  <tr> 
    <td bgcolor="#FFFFCC" height="8">
	 <font face="Verdana, Arial, Helvetica, sans-serif" size="2">
	 <a href="authuser.php">Reseller</a></font></td>
  </tr>
  <tr> 
    <td bgcolor="#0099CC">
	<font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFCC">
	<b>Product Management</b></font>
	  </td>
    <td bgcolor="#FFFFCC"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">
	<a href="products.php">Products</a></font></td>
  </tr>
  <tr> 
    <td bgcolor="#0099CC"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFCC">
	<strong>Misc Management </strong></font></td>
    <td bgcolor="#FFFFCC"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
	<a href="bannedip.php">Banned IPs</a></font></td>
  </tr>
  <tr> 
    <td bgcolor="#0099CC" colspan="2"> <div align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">
	<a href="logout.php">Logout</a></font></div></td>
  </tr>
</table>
</body>
</html>
