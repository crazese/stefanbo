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

	// print $detail;
	
	if ($detail != 2)
	{
		header("Location: $failure");
	}

	// Get reseller information
	$query = "SELECT * FROM Reseller WHERE uname='$nname' AND passwd='$passwd'";
	//echo $query;
	$connection = mysql_connect($dbhost, $dbusername, $dbpass);
	$result = mysql_db_query($dbname, $query);
	$numrows = mysql_num_rows($result);
	$row = mysql_fetch_array($result);
	$resellerid = $row["intID"];
	// print $query;
	
?>
<html>
<head>
<title>Reseller Control Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
td {  font-family: "Verdana", "Arial", "Helvetica", "sans-serif"; font-size: 12px}
body {
	background-color: #BDCBE7;
}
-->
</style>
</head>

<body text="#000000">
<table width="800" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
    <td> 
      <div align="center"> 
        <table width="90%" border="0" cellspacing="0" cellpadding="4">
          <tr bgcolor="#EEEEEE"> 
            <td colspan="5" bgcolor="#BDCBE7"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Hello, 
              <?=$row["strName"]?>
              </font></td>
          </tr>
          <tr bgcolor="#FFFFEE"> 
            <td width="20%" bgcolor="#BDCBE7"><a href="register.php">Register Software</a></td>
            <td width="20%" bgcolor="#BDCBE7"><a href="query.php">Query Registration</a></td>
            <td width="20%" bgcolor="#BDCBE7"><a href="newsletter.php">Send Newsletter</a></td>
            <td width="20%" bgcolor="#BDCBE7"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><a href="profile.php">Profile</a></font></td>
            <td bgcolor="#BDCBE7"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><a href="logout.php">Logout</a></font></td>
          </tr>
        </table>
        <br>
        <table width="90%" border="0" cellspacing="0" cellpadding="4">
          <tr> 
            <td>
            <?
            	// Find out how many registration had been issued.
				$qReg = "select count(*) as Num, Product.strProductName as ProductName
					from RegUser, Product       
					where RegUser.intProductID = Product.intID and
    				RegUser.intResellerID = $resellerid
					group by RegUser.intProductID";
					
				$resReg = mysql_db_query($dbname, $qReg);
				$numrowsReg = mysql_num_rows($resReg);
								
				print "<table border=\"1\" cellpadding=4 cellspacing=0 align=center width=80%> \n";
				print "<tr bgcolor=#cccccc><td with=50%>Product Name</td><td width=50%>Number</td></tr>";
				for ($i = 0 ; $i < $numrowsReg; $i++)
				{
					$rowReg = mysql_fetch_array($resReg);
					print "<tr>\n";
					print "<td>".$rowReg["ProductName"]."</td>";
					print "<td>".$rowReg["Num"]."</td>";
					print "</tr>\n";
				}
				
				$qReg = "select count(*) as Num from RegUser where RegUser.intResellerID = $resellerid";
				$resReg = mysql_db_query($dbname, $qReg);
				print "<tr>\n";
				print "<td bgcolor=#cccccc>Total</td>";
				$rowReg = mysql_fetch_array($resReg);
				print "<td bgcolor=#cccccc>".$rowReg["Num"]."</td>";
            	print "</table>\n";
				print "</tr>\n";
            ?>
            </td>
          </tr>
        </table>
        <p><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Contact 
          <a href="mailto:info@sothink.com">info@sothink.com</a> if you have any 
          questions.</font></p>
      </div>
    </td>
  </tr>
</table>
<p>&nbsp; </p>
</body>
</html>
