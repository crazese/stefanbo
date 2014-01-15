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

	$connection = mysql_connect($dbhost, $dbusername, $dbpass);
	
	// print "action".$action;

	if ($action == "Update")
	{
		// Update the user profile
		$qUpdate = "update Reseller set strName='$companyname', 
			uname='$loginname', 
			passwd='$password' 
			where uname='$cookie[username]' AND passwd='$cookie[password]'";
			
		// print "query is:".$qUpdate;
			
		$result = mysql_db_query($dbname, $qUpdate);
		
		// Re-login
		
		setcookie("cookie[username]", $loginname, time()+3600);
		setcookie("cookie[password]", $password, time()+3600);

		$query = "SELECT * FROM Reseller WHERE uname='$loginname' AND passwd='$password'";
	}
	else
	{
		$query = "SELECT * FROM Reseller WHERE uname='$nname' AND passwd='$passwd'";
	}
	
	
	// Get reseller information
	
	$result = mysql_db_query($dbname, $query);
	$numrows = mysql_num_rows($result);
	$row = mysql_fetch_array($result);
	
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

<script language="JavaScript">
<!--
function validateform()
{
	if (document.updateform.password.value != document.updateform.password1.value)
	{
		alert("Password not match.");
		document.updateform.password.focus();
		return false;
	}
	else
	{
		return true;
	}
}
//-->
</script>

</head>

<body text="#000000">
<table width="800" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
    <td> 
      <div align="center"> 
        <table width="90%" border="0" cellspacing="0" cellpadding="4">
          <tr bgcolor="#EEEEEE"> 
            <td colspan="5" bgcolor="#CCCCCC"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Hello, 
              <?=$row["strName"]?>
              </font></td>
          </tr>
          <tr bgcolor="#FFFFEE"> 
            <td width="20%" bgcolor="#CCCCCC"><a href="register.php">Register Software</a></td>
            <td width="20%" bgcolor="#CCCCCC"><a href="query.php">Query Registration</a></td>
            <td width="20%" bgcolor="#CCCCCC"><a href="newsletter.php">Send Newsletter</a></td>
            <td width="20%" bgcolor="#CCCCCC"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><a href="profile.php">Profile</a></font></td>
            <td bgcolor="#CCCCCC"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><a href="logout.php">Logout</a></font></td>
          </tr>
        </table>
        <br>
		<form method="post" name="updateform" action="profile.php" onSubmit="return validateform();">
        <table width="90%" border="1" cellspacing="0" cellpadding="5" bordercolor="#333333">
          <tr> 
            <td width="29%" bgcolor="#CCCCCC">Company</td>
            <td width="71%">
                <input type="text" name="companyname" maxlength="128" size="64" value="<?=$row["strName"]?>">
            </td>
          </tr>
          <tr> 
            <td width="29%" bgcolor="#CCCCCC">Reseller's Login Name</td>
            <td width="71%">
              <input type="text" name="loginname" size="32" maxlength="32" value="<?=$row["uname"]?>">
            </td>
          </tr>
          <tr> 
            <td width="29%" bgcolor="#CCCCCC">Password</td>
            <td width="71%">
              <input type="password" name="password" size="32" maxlength="32" value="<?=$row["passwd"]?>">
            </td>
          </tr>
          <tr> 
            <td bgcolor="#CCCCCC">Passwrod Confirm</td>
            <td>
              <input type="password" name="password1" size="32" maxlength="32" value="<?=$row["passwd"]?>">
            </td>
          </tr>
          <tr bgcolor="#CCCCCC" align="center"> 
            <td colspan="2">
              <input type="submit" name="action" value="Update">
            </td>
          </tr>
        </table>
		</form>
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
