<?
  	include ("auth.php"); 
	include ("authconfig.php");  	

	$Auth = new auth();
	$nname = $_COOKIE['cookie']['username'];
	$passwd = $_COOKIE['cookie']['password'];
	// check cookie 
	if (isset ($_COOKIE['cookie']['username'])) 
	{
		// print "Cookie Set";
		// get password and username and check in database		
		$detail = $Auth->authenticate($_COOKIE['cookie']['username'], $_COOKIE['cookie']['password']);		
	}
	else
	{
		$detail = 0;
	}

	if ($detail != 2)
	{
		header("Location: $failure");
	}

	// Get reseller information
	$query = "SELECT * FROM Reseller WHERE uname='$nname' AND passwd='$passwd'";
	
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
-->
</style>
</head>

<body bgcolor="#FFFFFF" text="#000000">
<table width="800" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
    <td> 
      <p align="center"><font face="Arial, Helvetica, sans-serif" size="5"><b>Reseller 
        Control Panel</b></font></p>
      <div align="center"> 
        <table width="90%" border="0" cellspacing="0" cellpadding="4">
          <tr> 
            <td>
            <?
            	$query = "select strName, strRegName from RegUser
            		where intResellerID = $resellerid and
            		intProductID=$Product and 
            		dateReg >= '$from_date' and
            		dateReg < DATE_ADD(\"$to_date\", INTERVAL 1 DAY) and
            		intStatus = 1";
            		
            	// print $query;
            		
				$result = mysql_db_query($dbname, $query);
				$numrows = mysql_num_rows($result);
				if ($numrows < 1) 
				{
					print "Cannot find recipients.";
				}
				else
				{
					for ($i=0; $i<$numrows; $i++)
					{
						// 
						$row = mysql_fetch_array($result);
						print $row["strName"].",".$row["strRegName"]."<br>\n";
					}
				}
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
