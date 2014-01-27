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
	
	$action = $_POST['action'];
	if ($action == "Update") {
		if ($expire == 0)
		{
			$strDateExp = "0";
		}
		else 
		{
			if ($expire == 1)
			{
				$days_to_expire = 14;
			}
			else
			{
				$days_to_expire = (int)$expiredays;
			}
			$strDateExp = date("Y-m-j H:i:s", mktime(date("H"), date("i"), date("s"), date("m"),  date("d")+ $days_to_expire, date("Y")));
			
		}
		
		$numupdate = 0;
		$numfail = 0;
		
		// Update the user info
		$qUpdate = "update RegUser set dateExpire='$strDateExp' where intResellerID = $resellerid and intProductID = $Product";
		// print $qUpdate;
		$res = mysql_db_query($dbname, $qUpdate);
		$numupdate = mysql_affected_rows();

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
        <?
        if ($action == "Update")
        {
        ?>
          <table width="90%" border="1" cellspacing="0" cellpadding="5" bordercolor="#000000">
          <tr>
            <td bgcolor="#FFFFCC" align="center"><b>Result</b></td>
          </tr>
          <tr>
            <td align=center> 
              <?=$numupdate?>
              registrations updated</td>
          </tr>
          </table>
        <?
        }
        ?>
        <br>
        <form name="form1" method="post" action="updateregexpire.php">
          <input type="hidden" name="resellerid" value="<?=$row["intID"]?>">
          <table width="90%" border="1" cellspacing="0" cellpadding="5" bgcolor="#CCCCCC" bordercolor="#000000">
            <tr>
              <td colspan="2" align="center"><b>Update Users' Download Expiration 
                Time</b></td>
            </tr>
            <tr> 
              <td width="25%">Product</td>
              <td bgcolor="#FFFFFF" width="75%"> 
                <?
              	// Get Product List
              	$qPro = "select * from Product";
				$presult = mysql_db_query($dbname, $qPro);
				// $numrows = mysql_num_rows($result);
				// print $numrows;
				$prow = mysql_fetch_array($presult);
              ?>
                <select name="Product">
                  <?
                while ($prow) {
                  	print "<option value=\"".$prow["intID"]."\" >".$prow["strProductName"]."</option>\n";
                	$prow = mysql_fetch_array($presult);
                }
                ?>
                </select>
              </td>
            </tr>
            <tr> 
              <td width="25%">Expire</td>
              <td bgcolor="#FFFFFF" width="75%"> 
                <select name="expire">
                  <option value="0" selected>Never expire</option>
                  <option value="1">Expire in 2 weeks</option>
                  <option value="2">Expire in ...</option>
                </select>
                <input type="text" name="expiredays" size="5" maxlength="4">
                Days </td>
            </tr>
            <tr> 
              <td colspan="2" align="center"> 
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
