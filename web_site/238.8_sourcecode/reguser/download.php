<?
	include ("admin/authconfig.php");
	$connection = mysql_connect($dbhost, $dbusername, $dbpass);
	
	// Check if the User IP had been banned
	$strIP = getenv ("REMOTE_ADDR"); 
	$strDate = date("Y-m-j H:i:s");
	$qFindBannedIP = "select * from BannedIP where strIP='$strIP'";
	$result = mysql_db_query($dbname, $qFindBannedIP);
	$numrows = mysql_num_rows($result);
	if ($numrows >= 1)
	{
		// find user ip in banned list
		$row = mysql_fetch_array($result);
		if ($row["intCount"] >= 5)
		{
			// Banned when the count is up to 5, for 1 hour
			$dateCur = $row["dateLastAccess"];
			// print $dateCur;
			$tsCur = strtotime($dateCur);
			// print $tsCur;
			if (time()-$tsCur > 3600)
			{
				// Unlock the banned ip
				$result = mysql_db_query($dbname, "update BannedIP set intCount=0 where strIP='$strIP'");
			} 
			else
			{
				include("banned.php");
				exit();
			}
				
		}
	}	

	// Check if it is a valid fid number
	// print $fid;
	$fid = addslashes($fid);
	$fid = $_GET['fid'];
	$qSearch = "select RegUser.intID as intID, 
		intStatus,strFilePath,intDownloadCount, strName, strRegName, intResellerID, strProductName, strURL, dateExpire 
		from RegUser, Product where Product.intID=RegUser.intProductID and strURL='$fid'";

	$result = mysql_db_query($dbname, $qSearch);
	$numrows = mysql_num_rows($result);
	if ($numrows < 1)
	{
		// Cannot find the download fid number
		$retcode = 0;
		
		// Record the ip address to database
		// print $qFindBannedIP ;
		$result = mysql_db_query($dbname, $qFindBannedIP);
		$numrows = mysql_num_rows($result);
		
		if ($numrows >= 1)
		{
			// The address already in list
			$qBanIP = "update BannedIP set dateLastAccess='".$strDate."', intCount=intCount + 1 where strIP='".$strIP."'";
			$result = mysql_db_query($dbname, $qBanIP);
		}
		else
		{
			// the IP not in the list, add it
			if ($strIP != "218.104.102.18" && $strIP !="218.246.32.119")
			{
				$qBanIP = "insert into BannedIP(strIP, intCount, dateLastAccess) values ('$strIP', 1, '$strDate')";
				$result = mysql_db_query($dbname, $qBanIP);
			}

		}
		
		include ("failed.php?strIP=$strIP");
	}
	else
	{
		$row = mysql_fetch_array($result);
		
		// Check User Status and expire time
		
		if ($row["intStatus"] == 1 && ($row["dateExpire"] == 0 || time() < strtotime($row["dateExpire"])) && $row["intDownloadCount"] < 3)
		{
			$path = $row["strFilePath"];
			//print $path;
			//die();			
			$retcode = 1;
			
			// Log it
			$qLog = "insert into UserLog(intUserID, datTime, strIP) values (".$row["intID"].
				",'$strDate', '$strIP')";
			$res = mysql_db_query($dbname, $qLog);
	
			// Get download start position
			$SizeOfFile = filesize($path);
			$sRangeInfo = getenv("HTTP_RANGE");
			if ($sRangeInfo != "" && $sRangeInfo != NULL)
			{
				list($rangeunit, $iStartPos) = explode("=", $sRangeInfo);
				// print $sRangeInfo;
				if ($rangeunit == "bytes")
				{
					settype($iStartPos, "integer");
				}
				else
				{
					$iStartPos = 0;
				}
			}
			else
			{
				$iStartPos = 0;
			}
			
			$DownLength = $SizeOfFile - $iStartPos;
			
			if ($iStartPos != 0)
			{
				Header("HTTP/1.1 206 Partial Content");
				Header("Accept-Range: bytes");
			}
			Header("Content-Type: application/octet-stream");
			Header("Content-Length: ".$DownLength);
			if ($iStartPos != 0)
			{
				Header("Content-Range: bytes ".$iStartPos."-".($SizeOfFile-1)."/".$SizeOfFile);
			}
			else
			{
				// Download from scratch, add the download counter by 1 and log it
				// Update the download counter
				$qUpdate = "update RegUser set intDownloadCount = intDownloadCount + 1 where strURL='$fid'";
				$res = mysql_db_query($dbname, $qUpdate);
				
				// Send warning email if the download counter can be divided by 10
				//if ($row["intDownloadCount"] % 10 == 0)
				//{
				//	$message = "User ID: ".$row["intID"];
				//	$message = $message."\nUser Name: ".$row["strName"];
				//	$message = $message."\nUser Email: ".$row["strRegName"];
				//	$message = $message."\nDownload Times: ".$row["intDownloadCount"];
				//	$message = $message."\nReseller ID: ".$row["intResellerID"];
				//	$message = $message."\nProduct Name: ".$row["strProductName"];
				//	$message = $message."\nFile ID: ".$row["strURL"]."\n";
				//	mail("$admin_email", "Reg User Download Report", $message);
				//}
			}
			Header("Content-Disposition: attachment; filename=".basename($path));
			
			$fp = fopen($path, "rb");
			fseek($fp, $iStartPos);
			if ($DownLength > 1000000)	// In case the file is too large
			{
				$segnum = $DownLength / 1000000;
				$remain = $DownLength % 1000000;
				for ($x = 0; $x < $segnum; $x++)
				{
					$content = fread($fp, 1000000);
					print $content;
				}
				$content = fread($fp, $remain);
				print $content;
				
			}
			else
			{
				$content = fread($fp, $DownLength);
				print $content;
			}
			fclose($fp);
			
		}
		else if ($row["intStatus"] != 1)
		{
			// This User had been disabled
			
			include("disabled.php");
		}
		else
		{
			// This User Expired
			
			include("expire.php");
		}
	}	
?>
