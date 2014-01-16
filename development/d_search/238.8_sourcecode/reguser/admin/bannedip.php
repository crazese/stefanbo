<?
	include ("auth.php");
	include ("authconfig.php");	
	$user = new auth();
	// check cookie 
	if (isset ($_COOKIE['cookie']['username'])) 
	{
		// print "Cookie Set";
		// get password and username and check in database		
		$detail = $user->authenticate($_COOKIE['cookie']['username'], $_COOKIE['cookie']['password']);		
	}
	else
	{
		$detail = 0;
	}
	if ($detail != 1)		// Admin
	{
		header("Location: $failure");
	}
	// $connection = mysql_connect($dbhost, $dbusername, $dbpass);
	// $listteams = mysql_db_query($dbname, "SELECT * from authteam");
?>

<?
$action = $_REQUEST['action'];
$strIP = $_REQUEST['strIP'];
if ($action == "Edit") {
	$qShowIp = "select * from BannedIP where strIP = '$strIP'";
	$result = mysql_db_query($dbname, $qShowIp);
	$row = mysql_fetch_array($result);
	$strIP = $row["strIP"];
	$Count = $row["intCount"];
	$LastAccess = $row["dateLastAccess"];
}

// DELETE Build
if ($action=="Delete") {
	$qDeleteIP = "delete from BannedIP where strIP = '$strIP'";
	// print $qDeleteBuild;
	$result = mysql_db_query($dbname, $qDeleteIP);
	$strIP = "&nbsp;";
	$Count = "&nbsp;";
	$LastAccess = "&nbsp;";
	
}



?>

<html>
<head>
<title>Reseller Control Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF" text="#000000">
<p><font face="Arial, Helvetica, sans-serif" size="5"><b>Reseller Control Panel 
  Admin - Banned IP Address</b></font></p>
<table width="95%" border="0" cellspacing="0" cellpadding="0" align="left">
  <tr valign="top"> 
    <td width="50%"> 
      
	  <form name="BannedIP" method="Get" action="bannedip.php">
	    <table width="95%" border="1" cellspacing="0" cellpadding="2" align="center" bordercolor="#000000">
          <tr bgcolor="#000000"> 
            <td colspan="2"> <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="3" color="#FFFFCC"><b>Banned 
                IP DETAILS</b></font></div></td>
          </tr>
          <tr valign="middle"> 
            <td width="45%" bgcolor="#33CCFF"><strong><font face="Verdana, Arial, Helvetica, sans-serif" size="2">IP</font></strong></td>
            <td width="55%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><input name="strIP" type="hidden" value="<?=$strIP?>"><?=$strIP?> 
              </font></td>
          </tr>
          <tr valign="middle"> 
            <td width="45%" bgcolor="#33CCFF"><strong>Illegal Access</strong></td>
            <td width="55%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><?=$Count?>
              </font></td>
          </tr>
          <tr valign="middle"> 
            <td width="45%" bgcolor="#33CCFF"><strong>Last Access Time</strong></td>
            <td width="55%"><?=$LastAccess?></td>
          </tr>
          <tr bgcolor="#CCCCCC" valign="middle"> 
            <td colspan="2"> <div align="center"><font size="2"><font size="2"><font size="2"><font face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="action" type="submit" value="Delete">
                </font></font></font></font></div></td>
          </tr>
        </table>
	  </form>
	  

      <p>&nbsp;</p>
      <p>&nbsp;</p>
      </td>
    <td width="50%"> 
      
	  
	  <table width="95%" border="1" cellspacing="0" cellpadding="2" align="center" bordercolor="#000000">
        <tr bgcolor="#000000"> 
          <td> <div align="center"><font size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFCC"><b>List 
              of Banned IPs</b></font></div></td>
        </tr>
        <tr bgcolor="#CCCCCC"> 
          <td > <font size="1"><b><font face="Verdana, Arial, Helvetica, sans-serif">IP</font></b></font></td>
        </tr>
        <?
	// Fetch rows from AuthUser table and display ALL users
	$result = mysql_db_query($dbname, "SELECT * FROM BannedIP");
	$row = mysql_fetch_array($result);
	while ($row) { 
?>
        <tr> 
          <td><a href="bannedip.php?strIP=<?=$row["strIP"]?>&action=Edit"> 
            <?=$row["strIP"]?>
            </a> </td>
        </tr>
        <?		
		$row = mysql_fetch_array($result);
	}
?>
      </table>
	  
      
    </td>
  </tr>
</table>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b><a href="admin.php">&lt; 
  Back</a> <a href="logout.php">Logout</a></b></font></p>
</body>
</html>
