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
	
	// print $query;
	$action = $_GET['action'];
	$intID = $_GET['intID'];
	//echo $action;
	if ($action == "Disable" || $action == "Enable")
	{
		$qStatus = "update RegUser set intStatus = not intStatus where intID = ".$intID;
		$res = mysql_db_query($dbname, $qStatus);
		
		// Log it
		$Auth->addlog($Auth->RESELLERID, "$action Reg Entry, ID: $intID");
	}
	
	if ($action == "Update")
	{
		// Check the product type
		$productid = $_GET['productid'];
		$Name = $_GET['Name'];
		$Email = $_GET['Email'];
		
		$qType = "select * from Product where intID =". $productid;
		echo $qType;
		$restype = mysql_db_query($dbname, $qType);
		$numrowstype = mysql_num_rows($restype);
		$rowtype = mysql_fetch_array($restype);
		
		if ($rowtype["intUnlockType"] == 1)			
		// Download Full Version
		{
			$qUpdate = "update RegUser set strName = '".addslashes($Name)
				."', strRegName = '".addslashes($Email)
				."', strRegCode = '', intProductID = $productid where intID = ".$intID;
		}
		else 										
		// Reg Code + File or Reg Code
		{
			// Generate Reg Code
			$codeseed = $rowtype["strCodeSeed"];
			$retcode = 0;
			exec("$reg_gen $Email $codeseed", $regcode, $retcode);
			if ($retcode == -1)
			{
				print "Cannot generate register code";
			}
			
			// print "Reg Code:".$regcode[0];
			
			$qUpdate = "update RegUser set strName = '".addslashes($Name)
				."', strRegName = '".addslashes($Email)
				."', strRegCode = '$regcode[0]', intProductID = $productid where intID = ".$intID;
		}
		
		
		// print $qUpdate;
		$res = mysql_db_query($dbname, $qUpdate);

		// Log it
		$Auth->addlog($Auth->RESELLERID, "Update Reg Entry Info, ID: $intID");
	}
	
	if ($action == "Set")
	{
		// Set expiration time
		$expire = $_GET['expire'];
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
		
		$qSetExp = "update RegUser set dateExpire='$strDateExp' where intID= ".$intID;
		//echo $expire;
		$res = mysql_db_query($dbname, $qSetExp);
		// Log it
		$Auth->addlog($Auth->RESELLERID, "Set Entry Expire, ID: $intID, Date: $strDateExp");
	}

	if ($action == "Reset Counter")
	{
		$qSetExp = "update RegUser set intDownloadCount=0 where intID= ".$intID;
		
		$res = mysql_db_query($dbname, $qSetExp);
		// Log it
		$Auth->addlog($Auth->RESELLERID, "Set Entry Expire, ID: $intID, Date: $strDateExp");
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
      <p align="center"><font face="Arial, Helvetica, sans-serif" size="5"><b>Reseller Control Panel</b></font></p>
      <div align="center"> 
        <table width="90%" border="0" cellspacing="0" cellpadding="4">
          <tr> 
            <td>
            <?
            	$query = "select * from RegUser, Product where RegUser.intProductID = Product.intID and RegUser.intID =".$intID;
				$result = mysql_db_query($dbname, $query);
				$numrows = mysql_num_rows($result);
				if ($numrows < 1) 
				{
					print "Cannot find registration record, please contact info@sothink.com to report";
				}
				else
				{
					$row = mysql_fetch_array($result);
			?>
              <form action="updatereg.php">
                <table width="90%" align="center" cellpadding="5" cellspacing="0" border="1" bordercolor="#CCCCCC">
                  <tr> 
                    <td width="20%" bgcolor="#EEEEEE" valign="top">ID</td>
                    <td width="80%"> 
                      <?=$intID?>
                    </td>
                  </tr>
                  <tr> 
                    <td bgcolor="#EEEEEE" valign="top" width="20%">User Name</td>
                    <td width="80%"> 
                      <input type="text" name="Name" value="<?=$row["strName"]?>">
                    </td>
                  </tr>
                  <tr> 
                    <td bgcolor="#EEEEEE" valign="top" width="20%">User Email</td>
                    <td width="80%"> 
                      <input type="text" name="Email" value="<?=$row["strRegName"]?>">

                    </td>
                  </tr>
                  <tr> 
                    <td bgcolor="#EEEEEE" valign="top" width="20%">Product Name</td>
                    <td width="80%"> 
		                <select name="productid">
		                <?
			              	// Get Product List
			              	$qPro = "select * from Product";
							$presult = mysql_db_query($dbname, $qPro);
							// $numrows = mysql_num_rows($result);
							// print $numrows;
							$prow = mysql_fetch_array($presult);
			                while ($prow) 
			                {
			                  	print "<option value=\"".$prow["intID"]."\" ";
			                  	if ($prow["intID"] == $row["intProductID"])
			                  	{
			                  		print "selected";
			                  	}
			                  	print " >".$prow["strProductName"]."</option>\n";
			                	$prow = mysql_fetch_array($presult);
			                }
		                ?>
						</select>
                    </td>
                  </tr>
                  <tr> 
                    <td bgcolor="#EEEEEE" valign="top" width="20%">Unlock Type</td>
                    <td width="80%"> 
                      <?
						if ($row["intUnlockType"]==1)
						{
							print "Full Version";
						}
						else if ($row["intUnlockType"]==2)
						{
							print "Reg Code + File";
						}
						else if ($row["intUnlockType"]==3)
						{
							print "Reg Code";
						}
                    	?>
                    </td>
                  </tr>
                  <tr> 
                    <td bgcolor="#EEEEEE" valign="top" width="20%">Download URL</td>
                    <td width="80%"> 
                      <?="http://".$_SERVER['HTTP_HOST'].$Auth->downurl.$row["strURL"];?>
                    </td>
                  </tr>
                  <tr> 
                    <td bgcolor="#EEEEEE" valign="top" width="20%">Access Counter</td>
                    <td width="80%"> 
                      <?=$row["intDownloadCount"]?>&nbsp;<input type="submit" name="action" value="Reset Counter">

                    </td>
                  </tr>
                  <tr> 
                    <td bgcolor="#EEEEEE" valign="top" width="20%">Reg Code</td>
                    <td width="80%"> 
                      <?
					  	if (trim($row["strRegCode"]) != "")
						{
							print $row["strRegCode"];
						}
						else
						{
							print "&nbsp;";
						}
						?>
						</td>
                  </tr>
                  <tr> 
                    <td bgcolor="#EEEEEE" valign="top" width="20%">Date</td>
                    <td width="80%"> 
                      <?=$row["dateReg"]?>
                    </td>
                  </tr>
                  <tr> 
                    <td bgcolor="#EEEEEE" valign="top" width="20%">Exipre</td>
                    <td width="80%">
                      <?=$row["dateExpire"]?>
                      <br>
                      <br>
                      Set to
                      <select name="expire">
                        <option value="0" selected>Never expire</option>
                        <option value="1">Expire in 2 weeks from now on</option>
                        <option value="2">Expire in ...</option>
                      </select>
                <input type="text" name="expiredays" size="5" maxlength="4">
                      Days 
                      <input type="submit" name="action" value="Set">
                    </td>
                  </tr>
                  <tr> 
                    <td bgcolor="#EEEEEE" valign="top" width="20%">Status</td>
                    <td width="80%"> 
                      <?
                    		if ($row["intStatus"] == 1)
                    		{
                    			print "Active &nbsp;";
                      			print "<input type=\"submit\" name=\"action\" value=\"Disable\">";
                      		}
                      		else
                      		{
                    			print "Inactive &nbsp;";

                      			print "<input type=\"submit\" name=\"action\" value=\"Enable\">";
                      		}
                      	?>
                    </td>
                  </tr>
                  <tr> 
                    <td colspan="2" valign="top"> 
                      <div align="center"> 
                        <input type="submit" name="action" value="Update">
                        <input type="hidden" name="intID" value="<?=$intID?>">
                      </div>
                    </td>
                  </tr>
                </table>
			  </form>
              <br>
			<?
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
</body>
</html>