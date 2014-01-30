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
<meta http-equiv="Content-Type" content="text/html; charset="<?=$langselect?>">
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
		<form method="post" action="newsletter-send.php" >
			
          <table width="90%" border="1" cellspacing="0" cellpadding="5" bordercolor="#999999" bgcolor="#DDDDDD">
            <tr> 
            <td>
                <p><b>Preview your newsletter(s)</b></p>
                <span><b>Recipients</b></span>
<hr noshade size="1">
                <p>Based on your selection, the newsletter will be sent to 
                <?
                	// Find out how many recipients there will be
					$from_date = "$YEAR_FROM-$MONTH_FROM-$DAY_FROM";
					$to_date = "$YEAR_TO-$MONTH_TO-$DAY_TO";
                	
                	$qRecipients = "select * from RegUser 
                		where intResellerID = $resellerid and
                		intProductID=$Product and 
                		dateReg >= '$from_date' and
                		dateReg < DATE_ADD(\"$to_date\", INTERVAL 1 DAY) and 
                		intStatus = 1";
                	// print $qRecipients;
                		
					$resultlist = mysql_db_query($dbname, $qRecipients);
					$totalrecnum = mysql_num_rows($resultlist);
                	
                	print $totalrecnum;
                ?>
                customer(s). 
                  These customers match the following criteria:</p>
                <p>They ordered 
                '
                <?
                	// Get Product Name
                	$qGetProName = "select strProductName from Product where intID= $Product";
					$resproname = mysql_db_query($dbname, $qGetProName);
					$pronamerow = mysql_fetch_array($resproname);
					print $pronamerow["strProductName"];
                ?>
                ' . <br>
                  They ordered these products between <?=$from_date?> and <?=$to_date?>. 
                </p>
                <p><a target="_blank" href="show-recipients.php?Product=<?=$Product?>&from_date=<?=$from_date?>&to_date=<?=$to_date?>"> Show the newsletter recipients</a><br>
                </p>
                <p>The following email will be sent to these customers:</p>
                <table width="95%" border="1" cellspacing="0" cellpadding="5" align="center" bordercolor="#999999">
                  <tr>
                    <td bgcolor="#FFFFFF">
                      <p><b>From:</b> <?=$emailfrom?><br>
                      <input type="hidden" name="emailfrom" value="<?=$emailfrom?>">
                      <b>Reply-To:</b> <?=$emailreplyto?></p>
                      <input type="hidden" name="emailreplyto" value="<?=$emailreplyto?>">
                      <p><b>Subject:</b> <?=$emailsubject?></p>
                      <input type="hidden" name="emailsubject" value="<?=$emailsubject?>">
                      <input type="hidden" name="productid" value="<?=$Product?>">
                      <input type="hidden" name="reseller" value="<?=$resellerid?>">
                      <input type="hidden" name="from_date" value="<?=$from_date?>">
                      <input type="hidden" name="to_date" value="<?=$to_date?>">
                      <p>
                        <textarea name="emailtext" cols="60" rows="10"><?=$emailtext?></textarea>
                      </p>
                    </td>
                  </tr>
                </table>
                <p></p>
                <hr noshade size="1">
                <p align="center"> 
                  <input type="submit" name="Submit" value="Send">
                </p>
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
