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
	if ($action == "Register") {
	
		$expire = $_POST["expire"];
		$UserName = $_POST["UserName"];
		$Email = $_POST["Email"];
		$Product = $_POST["Product"];
		$expiredays = $_POST["expiredays"];
		$resellerid = $_POST['resellerid'];
        //print_r($_POST);
		if ($expire == 0)
		{
			$days_to_expire = 0;
		}
		else if ($expire == 1)
		{
			$days_to_expire = 14;
		}
		else
		{
			$days_to_expire = (int)$expiredays;
		}
		
		$reguserid = $Auth->add_reguser($UserName, $Email, $Product, $resellerid, $days_to_expire);
		
		// Log it
		if ($reguserid != -1 && $reguserid != -2)
		{
			$Auth->addlog($Auth->RESELLERID, "Add Reg Entry, ID: $reguserid, Product: $Product");
			//print "Register: $reguserid";
		}
	}
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
        <p><a href="register-batch.php">Click here to add a list of users.</a></p>
        <p><a href="updateregexpire.php">Click here to update users' download 
          expiration time.<br>
          </a> </p>
        <form name="form1" method="post" action="register.php">
        	<input type="hidden" name="resellerid" value="<?=$row["intID"]?>">
          <table width="90%" border="1" cellspacing="0" cellpadding="5" bgcolor="#CCCCCC" bordercolor="#000000">
            <tr> 
              <td width="25%">User Name</td>
              <td bgcolor="#CCCCCC"> 
                <input type="text" name="UserName" maxlength="32" size="32" value="<?=$UserName?>">
              </td>
            </tr>
            <tr> 
              <td height="36">User Email</td>
              <td bgcolor="#CCCCCC" height="36"> 
                <input type="text" name="Email" size="32" maxlength="128" value="<?=$Email?>">
              </td>
            </tr>
            <tr> 
              <td>Product</td>
              <td bgcolor="#CCCCCC"> 
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
              <td>Expire</td>
              <td bgcolor="#CCCCCC"> 
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
                <input type="submit" name="action" value="Register">
              </td>
            </tr>
          </table>
        </form>
        <?
        	if ($action == "Register") {
		?>
        <br>
        <table width="90%" border="1" cellspacing="0" cellpadding="5" bordercolor="#000000">
          <tr>
            <td bgcolor="#FFFFCC" align="center"><b>Result</b></td>
          </tr>
          <tr>
            <td>
            <?
            	// Get Reg Info
            	if (isset($reguserid))
            	{
            		if ($reguserid != -1)
            		{
	            		$qGetRegInfo = "select * from Product, RegUser where Product.intID=RegUser.intProductID and RegUser.intID=$reguserid";
	            		// print $qGetRegInfo;
						$result = mysql_db_query($dbname, $qGetRegInfo);
						$row = mysql_fetch_array($result);
						print "User Name: ".$row["strName"]."<br>";
						print "User Email: ".$row["strRegName"]."<br><br>";
						print "Product Name: ".$row["strProductName"]."<br>";
						if ($row["intUnlockType"]==1)
						{
							print "Unlock Type: Full Version<br>";
							$url = "http://".$_SERVER['HTTP_HOST'].$Auth->downurl.$row["strURL"];
							print "Donwload Link: <a href=\"$url\" target=\"_blank\">$url</a><br>";
							print "Expire Date: ".$row["dateExpire"];
						}
						else if ($row["intUnlockType"]==2)
						{
							print "Unlock Type: Reg Code + File<br><br>";
							print "User Email: ".$row["strRegName"]."<br>";
							print "Register Code: ".$row["strRegCode"]."<br><br>";
							print "Also Download this file to unlock the trial version:<br>";
							$url = "http://".$_SERVER['HTTP_HOST'].$Auth->downurl.$row["strURL"];
							print "Donwload Link: <a href=\"$url\" target=\"_blank\">$url</a><br>";
							print "Expire Date: ".$row["dateExpire"];
							
						}
						else if ($row["intUnlockType"]==3)
						{
							print "Unlock Type: Reg Code Ver ".$row["intCodeVersion"]." <br>";
							print "User Email: ".$row["strRegName"]."<br>";
							print "Register Code: ".$row["strRegCode"]."<br><br>";							
						}
					}
					else
					{
						// Add Reg User Failure
						print "Add Reg User Failure";
					}
            	}
            	else
            	{
            		// No valid reg info
            		print "No valid reg user information";
            	}
            ?>
            </td>
          </tr>
        </table>
        <?
        }
        ?>
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
