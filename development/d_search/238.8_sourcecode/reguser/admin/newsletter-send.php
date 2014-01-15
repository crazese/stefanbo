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
	
	
	if ($Submit == "Send")
	{
		// Write the newsletter to be sent into a file
		mt_srand(make_seed());
		$randval = mt_rand();
		
		$filename = $Auth->tempdir.$randval.".txt";
		$fp = fopen ($filename, "w");
		fputs($fp, "http://".$HTTP_SERVER_VARS['HTTP_HOST'].$Auth->downurl."\r\n");
		fputs($fp, $reseller."\r\n");
		fputs($fp, $productid."\r\n");
		fputs($fp, $from_date."\r\n");
		fputs($fp, $to_date."\r\n");
		fputs($fp, $emailfrom."\r\n");
		fputs($fp, $emailreplyto."\r\n");
		fputs($fp, $emailsubject."\r\n");
		fputs($fp, $emailtext."\r\n");
		fclose($fp);

		// Start to send
		exec("$mail_sender $filename > $temp_file&");
		
	}
	
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
          <tr bgcolor="#EEEEEE"> 
            <td colspan="5"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Hello, 
              <?=$row["strName"]?>
              </font></td>
          </tr>
          <tr bgcolor="#FFFFEE"> 
            <td width="20%"><a href="register.php">Register Software</a></td>
            <td width="20%"><a href="query.php">Query Registration</a></td>
            <td width="20%"><a href="newsletter.php">Send Newsletter</a></td>
            <td width="20%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><a href="profile.php">Profile</a></font></td>
            <td><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><a href="logout.php">Logout</a></font></td>
          </tr>
        </table>
        <br>
          <table width="90%" border="1" cellspacing="0" cellpadding="5" bordercolor="#999999" bgcolor="#DDDDDD">
            <tr> 
            <td>
                <p><b>Your newsletter has been queued to send</b></p>
                <p align="center">You will be notified when it is done.</p>
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
