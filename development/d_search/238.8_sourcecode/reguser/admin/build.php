<?
	include ("auth.php");
	include ("authconfig.php");	
	
	$user = new auth();

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

	if ($detail != 1)		// Admin
	{
		header("Location: $failure");
	}
	
?>

<?
// ADD Build
if ($action == "Add") 
{
	if (isset($emailneeded))
	{
		$intEmailNeeded = 1;
	}
	else
	{
		$intEmailNeeded = 0;
	}
	
	if (isset($upgradelimited))
	{
		$intUpgradeLimit = 1;
	}
	else
	{
		$intUpgradeLimit = 0;
	}
	
	$qAddBuild = "insert into Build (intProductID, strVer, strInternalVer, strBuild, strLang, 
		intSize, strCRC, strLicense, intEmailNeeded, intUpgradeLimit,intUpdateFileSize,strUpdateFilePath, strURLDesc) values ($intProductID,
		'$vername','$internalver','$build','$langstr', $size, '$crc', '$license', $intEmailNeeded
		, $intUpgradeLimit, $filesize, '$filepath', '$urldesc')";

	// print $qAddBuild;
	
	mysql_connect($dbhost, $dbusername, $dbpass);
	
	$result = mysql_db_query($dbname, $qAddBuild);
	
	$intBuildID = mysql_insert_id();

}

// DELETE Build
if ($action=="Delete") {
	$qDeleteBuild = "delete from Build where intID = $intBuildID";
	// print $qDeleteBuild;
	$result = mysql_db_query($dbname, $qDeleteBuild);
	
	$vername= "";
	$internalver= "";
	$build = "";
	$langstr = "";
	$size = 0;
	$crc = "";
	$license = "";
	$intEmailNeeded = 0;
	$intUpgradeLimit = 0;
	$filesize = 0;
	$filepath = "";
	$urldesc = "";
}

// MODIFY Build
if ($action == "Modify") {
	if (isset($emailneeded))
	{
		$intEmailNeeded = 1;
	}
	else
	{
		$intEmailNeeded = 0;
	}
	
	if (isset($upgradelimited))
	{
		$intUpgradeLimit = 1;
	}
	else
	{
		$intUpgradeLimit = 0;
	}

	$qUpdateBuild = "update Build set strVer='$vername', strInternalVer='$internalver', 
		strBuild='$build', strLang='$langstr', intSize=$size, strCRC='$crc', strLicense='$license',
		intEmailNeeded=$intEmailNeeded, intUpgradeLimit = $intUpgradeLimit, intUpdateFileSize = $filesize,
		strUpdateFilePath='$filepath', strURLDesc='$urldesc' where intID=$intBuildID";		
	// print $qUpdateBuild;
	$result = mysql_db_query($dbname, $qUpdateBuild);
}

// EDIT USER (accessed from clicking on username links)

if ($action == "Edit") 
{
	$message = "Edit Build details.";
	$qGetBuildDetails = "select * from Build where intID=$intBuildID";
	$resultbuild = mysql_db_query($dbname, $qGetBuildDetails);
	$rowbuild = mysql_fetch_array($resultbuild);
	$vername = $rowbuild["strVer"];
	$internalver = $rowbuild["strInternalVer"];
	$build = $rowbuild["strBuild"];
	$langstr = $rowbuild["strLang"];
	$size = $rowbuild["intSize"];
	$crc = $rowbuild["strCRC"];
	$license = $rowbuild["strLicense"];
	$intEmailNeeded = $rowbuild["intEmailNeeded"];
	$intUpgradeLimit = $rowbuild["intUpgradeLimit"];
	$filesize = $rowbuild["intUpdateFileSize"];
	$filepath = $rowbuild["strUpdateFilePath"];
	$urldesc = $rowbuild['strURLDesc'];
}

// CLEAR FIELDS

if ($action == "Add New") {
	//$vername= "";
	//$internalver= "";
	//$build = "";
	//$langstr = "";
	//$size = 0;
	//$crc = "";
	//$license = "";
	//$intEmailNeeded = 0;
	//$intUpgradeLimit = 0;
	//$filesize = 0;
	//$filepath = "";
	//$urldesc = "";
	$message = "New build detail entry.";
}

?>

<html>
<head>
<title>Reseller Control Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF" text="#000000">
<p><font face="Arial, Helvetica, sans-serif" size="5"><b>Reseller Control Panel 
  Admin - Build Management</b></font></p>
<table width="95%" border="0" cellspacing="0" cellpadding="0" align="left">
  <tr valign="top"> 
    <td width="50%"> 
      
	  <form name="Builds" method="Post" action="build.php">
	    <table width="95%" border="1" cellspacing="0" cellpadding="2" align="center" bordercolor="#000000">
          <tr bgcolor="#000000"> 
            <td colspan="2"> <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="3" color="#FFFFCC"><b>Build 
                DETAILS</b></font></div></td>
          </tr>
          <tr valign="middle"> 
            <td width="45%" bgcolor="#33CCFF"><strong><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Version 
              String </font></strong></td>
            <td width="55%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"> 
              <input name="intProductID" type="hidden" id="intProductID" value="<?=$intProductID?>">
              <input name="intBuildID" type="hidden" id="intBuildID" value="<?=$intBuildID?>">
              <input name="vername" type="text" id="vername" value="<?=$vername?>" size="20">
              </font></td>
          </tr>
          <tr valign="middle"> 
            <td width="45%" bgcolor="#33CCFF"><strong><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Internal 
              Version</font></strong></td>
            <td width="55%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"> 
              <input name="internalver" type="text" id="internalver" value="<?=$internalver?>">
              </font></td>
          </tr>
          <tr valign="middle"> 
            <td width="45%" bgcolor="#33CCFF"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Build</font></strong></td>
            <td width="55%"> <input name="build" type="text" id="build" value="<?=$build?>"> 
            </td>
          </tr>
          <tr valign="middle"> 
            <td width="45%" bgcolor="#33CCFF"><strong><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Language</font></strong></td>
            <td width="55%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"> 
              <input name="langstr" type="text"  value="<?=$langstr?>">
              </font></td>
          </tr>
          <tr valign="middle"> 
            <td bgcolor="#33CCFF"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">License</font></strong></td>
            <td><input name="license" type="text" id="license" value="<?=$license?>"></td>
          </tr>
          <tr valign="middle"> 
            <td bgcolor="#33CCFF"><strong>Description</strong></td>
            <td><input name="urldesc" type="text" id="urldesc" value="<?=$urldesc?>"></td>
          </tr>
          <tr valign="middle"> 
            <td bgcolor="#33CCFF"><strong><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Size</font></strong></td>
            <td><input name="size" type="text" id="size" value="<?=$size?>"></td>
          </tr>
          <tr valign="middle"> 
            <td bgcolor="#33CCFF"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">CRC</font></strong></td>
            <td><input name="crc" type="text" id="crc" value="<?=$crc?>"></td>
          </tr>
          <tr valign="middle"> 
            <td bgcolor="#33CCFF"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Email 
              Needed</font></strong></td>
            <td><input name="emailneeded" type="checkbox" id="emailneeded" value="emailneeded" 
				<? if ($intEmailNeeded == 1) print "checked"; ?>
				></td>
          </tr>
          <tr valign="middle"> 
            <td bgcolor="#33CCFF"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Upgrade 
              Limited</font></strong></td>
            <td><font face="Verdana, Arial, Helvetica, sans-serif" size="2"> 
              <input name="upgradelimited" type="checkbox" id="upgradelimited2" value="upgradelimit"
			  <? if ($intUpgradeLimit == 1) print "checked"; ?>
			  >
              </font></td>
          </tr>
          <tr valign="middle"> 
            <td bgcolor="#33CCFF"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Update 
              File Size</font></strong></td>
            <td><input name="filesize" type="text" id="filesize" value="<?=$filesize?>"></td>
          </tr>
          <tr valign="middle"> 
            <td width="45%" bgcolor="#33CCFF"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Update 
              File Path</font></strong></td>
            <td width="55%"><input name="filepath" type="text" id="filepath" value="<?=$filepath?>"></td>
          </tr>
          <tr bgcolor="#CCCCCC" valign="middle"> 
            <td colspan="2"> <div align="center"><font size="2"><font size="2"><font size="2"><font face="Verdana, Arial, Helvetica, sans-serif"> 
                <?
					
				if (($action=="Add") || ($action == "Modify") || ($action=="Edit")) {
					print "<input type=\"submit\" name=\"action\" value=\"Add New\"> ";
					print "<input type=\"submit\" name=\"action\" value=\"Modify\"> ";
					print "<input type=\"submit\" name=\"action\" value=\"Delete\"> ";
				}
				else {
					print "<input type=\"submit\" name=\"action\" value=\"Add\"> ";
                }
				
				?>
                <input type="reset" name="Reset" value="Clear">
                </font></font></font></font></div></td>
          </tr>
        </table>
	  </form>
	  

      <p>&nbsp;</p>
      <table width="95%" border="1" cellspacing="0" cellpadding="0" align="center" bordercolor="#000000">
        <tr> 
          <td bgcolor="#990000"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFCC">Message:</font></b></td>
        </tr>
        <tr> 
          <td><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#0000FF">
		  <?
		  	if ($message) {
			 	print $message;
		  	}
			else {
				print "<BR>&nbsp;";
			}
		  ?>
		  </font></td>
        </tr>
      </table>
      <p>&nbsp;</p>
      </td>
    <td width="50%"> 
      
	  
	  <table width="95%" border="1" cellspacing="0" cellpadding="2" align="center" bordercolor="#000000">
        <tr bgcolor="#000000"> 
          <td colspan="4"> <div align="center"><font size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFCC"><b>Build 
              List Of [ 
              <?=$intProductID?>
              ]</b></font></div></td>
        </tr>
        <tr bgcolor="#CCCCCC"> 
          <td > <div align="center"><font size="1"><b><font face="Verdana, Arial, Helvetica, sans-serif">Version 
              Name</font></b></font></div></td>
          <td ><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>Build</strong></font></td>
          <td> 
            <div align="center"><font size="1"><b>Language</b></font></div></td>
          <td> 
            <div align="center"><font size="1"><b>License</b></font></div></td>
        </tr>
        <?
	// Fetch rows from AuthUser table and display ALL users
	$result = mysql_db_query($dbname, "SELECT * FROM Build where intProductID=$intProductID ORDER BY intID");
	$row = mysql_fetch_array($result);
	while ($row) { 
?>
        <tr>
          <td><a href="build.php?intProductID=<?=$intProductID?>&intBuildID=<?=$row["intID"]?>&action=Edit"><?=$row["strVer"]?></a></td>
		  <td><?=$row["strBuild"]?></td>
		  <td><?=$row["strLang"]?></td>
		  <td><?=$row["strLicense"]?></td>
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
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b><a href="admin.php">&lt; 
  Back</a> <a href="logout.php">Logout</a></b></font></p>
</body>
</html>
