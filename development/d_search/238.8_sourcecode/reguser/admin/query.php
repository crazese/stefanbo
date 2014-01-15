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
        <td colspan="5" bgcolor="#DDDDDD"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Hello,
		 <?=$row["strName"]?></font></td>
          </tr>
          <tr bgcolor="#FFFFEE"> 
            <td width="20%" bgcolor="#DDDDDD"><a href="register.php">Register Software</a></td>
            <td width="20%" bgcolor="#DDDDDD"><a href="query.php">Query Registration</a></td>
            <td width="20%" bgcolor="#DDDDDD"><a href="newsletter.php">Send Newsletter</a></td>
            <td width="20%" bgcolor="#DDDDDD"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><a href="profile.php">Profile</a></font></td>
            <td bgcolor="#DDDDDD"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><a href="logout.php">Logout</a></font></td>
          </tr>
        </table>
        <br>
              <form name="form1" method="get" action="query.php">
          <table width="90%" border="0" cellspacing="0" cellpadding="4" bgcolor="#eeeeee">
            <tr> 
              <td colspan="3" bgcolor="#DDDDDD"><b>Please Select Range</b></td>
            </tr>
            <tr> 
              <td bgcolor="#DDDDDD"> From: </td>
              <td bgcolor="#DDDDDD"> 
                <select name="from_month" >
                  <option value="1" <? if (date("m")==1) print selected;?>>January</option>
                  <option value="2" <? if (date("m")==2) print selected;?>>February</option>
                  <option value="3" <? if (date("m")==3) print selected;?>>March</option>
                  <option value="4" <? if (date("m")==4) print selected;?>>April</option>
                  <option value="5" <? if (date("m")==5) print selected;?>>May</option>
                  <option value="6" <? if (date("m")==6) print selected;?>>June</option>
                  <option value="7" <? if (date("m")==7) print selected;?>>July</option>
                  <option value="8" <? if (date("m")==8) print selected;?>>August</option>
                  <option value="9" <? if (date("m")==9) print selected;?>>September</option>
                  <option value="10" <? if (date("m")==10) print selected;?>>October</option>
                  <option value="11" <? if (date("m")==11) print selected;?>>November</option>
                  <option value="12" <? if (date("m")==12) print selected;?>>December</option>
                </select>
                &nbsp; 
                <select name="from_year" >
                  <option value="2002" <? if (date("Y")==2002) print selected;?>>2002</option>
                  <option value="2003" <? if (date("Y")==2003) print selected;?>>2003</option>
                  <option value="2004" <? if (date("Y")==2004) print selected;?>>2004</option>
                  <option value="2005" <? if (date("Y")==2005) print selected;?>>2005</option>
                  <option value="2006" <? if (date("Y")==2006) print selected;?>>2006</option>
                  <option value="2007" <? if (date("Y")==2007) print selected;?>>2007</option>
                  <option value="2008" <? if (date("Y")==2008) print selected;?>>2008</option>
                  <option value="2009" <? if (date("Y")==2009) print selected;?>>2009</option>
				  <option value="2010" <? if (date("Y")==2010) print selected;?>>2010</option>
				  <option value="2011" <? if (date("Y")==2011) print selected;?>>2011</option>
				  <option value="2012" <? if (date("Y")==2012) print selected;?>>2012</option>
			    </select>
              </td>
              <td bgcolor="#DDDDDD">Page Size: 
                <select name="recnumperpage">
                  <option value="10" selected>10 Entries Per Page</option>
                  <option value="25">25 Entries Per Page</option>
                  <option value="50">50 Entries Per Page</option>
                </select>
              </td>
            </tr>
            <tr> 
              <td bgcolor="#DDDDDD"> To: </td>
              <td bgcolor="#DDDDDD"> 
                <select name="to_month" >
                  <option value="1" <? if (date("m")==1) print selected;?>>January</option>
                  <option value="2" <? if (date("m")==2) print selected;?>>February</option>
                  <option value="3" <? if (date("m")==3) print selected;?>>March</option>
                  <option value="4" <? if (date("m")==4) print selected;?>>April</option>
                  <option value="5" <? if (date("m")==5) print selected;?>>May</option>
                  <option value="6" <? if (date("m")==6) print selected;?>>June</option>
                  <option value="7" <? if (date("m")==7) print selected;?>>July</option>
                  <option value="8" <? if (date("m")==8) print selected;?>>August</option>
                  <option value="9" <? if (date("m")==9) print selected;?>>September</option>
                  <option value="10" <? if (date("m")==10) print selected;?>>October</option>
                  <option value="11" <? if (date("m")==11) print selected;?>>November</option>
                  <option value="12" <? if (date("m")==12) print selected;?>>December</option>
                </select>
                &nbsp; 
                <select name="to_year" >
                  <option value="2002" <? if (date("Y")==2002) print selected;?>>2002</option>
                  <option value="2003" <? if (date("Y")==2003) print selected;?>>2003</option>
                  <option value="2004" <? if (date("Y")==2004) print selected;?>>2004</option>
                  <option value="2005" <? if (date("Y")==2005) print selected;?>>2005</option>
                  <option value="2006" <? if (date("Y")==2006) print selected;?>>2006</option>
                  <option value="2007" <? if (date("Y")==2007) print selected;?>>2007</option>
                  <option value="2008" <? if (date("Y")==2008) print selected;?>>2008</option>
                  <option value="2009" <? if (date("Y")==2009) print selected;?>>2009</option>
				  <option value="2010" <? if (date("Y")==2010) print selected;?>>2010</option>
				  <option value="2011" <? if (date("Y")==2011) print selected;?>>2011</option>
				  <option value="2012" <? if (date("Y")==2012) print selected;?>>2012</option>               
				</select>
              </td>
              <td bgcolor="#DDDDDD">&nbsp;</td>
            </tr>
            <tr> 
              <td colspan="3" bgcolor="#DDDDDD">
              <?
              	if (!isset($curpage))
              	{
              		$curpage = 1;
              	}
              ?> 
                <input type="submit" name="action" value="Display">

              </td>
            </tr>
          </table>
              </form>
        <br>
		<form action="query.php" method="get" name="form2">
          <table width="90%" cellpadding="4" cellspacing="0" border="0" bgcolor="#eeeeee">
            <tr> 
              <td colspan="2" bgcolor="#DDDDDD"><b>Find a registered user</b></td>
            </tr>
            <tr> 
              <td bgcolor="#DDDDDD">Name: 
                <input type="text" name="username">
              </td>
              <td bgcolor="#DDDDDD">Email: 
                <input type="text" name="useremail">
              </td>
            </tr>
            <tr> 
              <td colspan="2" bgcolor="#DDDDDD"> 
                <input type="submit" name="action" value="Find">
              </td>
            </tr>
          </table>
		</form>
		
		<? 
		$action = $_GET['action'];
		if (isset($action))
		{
		
		$from_month = $_GET['from_month'];
		$from_year = $_GET['from_year'];
		$to_month = $_GET['to_month'];
		$to_year = $_GET['to_year'];
		$recnumperpage = $_GET['recnumperpage'];
		$username = $_GET['username'];
		$useremail = $_GET['useremail'];
		
			if ($action == "Display")
			{
				$from_date = "$from_year-$from_month-1";
				$to_date = "$to_year-$to_month-1";
				$querygetrecnum = "select * from RegUser 
						where intResellerID = $resellerid 
						and dateReg >= '$from_date' 
						and dateReg < DATE_ADD(\"$to_date\", INTERVAL 1 MONTH)";
			}
			else
			{
				if ($username != "")
				{
					$qCreteria = "strName='".$username."'";
					if ($useremail != "")
					{
						$qCreteria = $qCreteria . " and strRegName='".$useremail."'";
					}
				}
				else
				{
					if ($useremail != "")
					{
						$qCreteria = "strRegName='".$useremail."'";
					}
					else
					{
						$qCreteria = "strName='' and strRegName=''";
					}
				}
				// print $qCreteria;
				$querygetrecnum = "select * from RegUser where intResellerID = $resellerid and $qCreteria";
			}
			
			// print $querygetrecnum;
			$resultlist = mysql_db_query($dbname, $querygetrecnum);
			$totalrecnum = mysql_num_rows($resultlist);

			if ($action == "Display")
			{
				$querylist = " Select RegUser.intID as intID, 
					RegUser.strName as strName, 
					RegUser.strRegName as strRegName, 
					Product.strProductName as strProductName, 
					RegUser.intDownloadCount as intDownloadCount,
					RegUser.dateReg as dateReg, 
					RegUser.intStatus as intStatus, 
					RegUser.strURL as strURL,
					Product.intUnlockType as intUnlockType,
					RegUser.strRegCode as strRegCode
					from RegUser,Product 
					where intResellerID = $resellerid and RegUser.intProductID = Product.intID
					and dateReg >= '$from_date' and dateReg < DATE_ADD(\"$to_date\", INTERVAL 1 MONTH)
					order by RegUser.intID limit ".(($curpage-1)*$recnumperpage).",".$recnumperpage;
			}
			else
			{
				$querylist = " Select RegUser.intID as intID, 
					RegUser.strName as strName, 
					RegUser.strRegName as strRegName, 
					Product.strProductName as strProductName, 
					RegUser.intDownloadCount as intDownloadCount,
					RegUser.dateReg as dateReg, 
					RegUser.intStatus as intStatus, 
					RegUser.strURL as strURL,
					Product.intUnlockType as intUnlockType,
					RegUser.strRegCode as strRegCode
					from RegUser,Product 
					where intResellerID = $resellerid and RegUser.intProductID = Product.intID and $qCreteria order by RegUser.intID";
			}
			
			$resultlist = mysql_db_query($dbname, $querylist);
			$numrowslist = mysql_num_rows($resultlist);
			$rowlist = mysql_fetch_array($resultlist);
			if ($totalrecnum > $recnumperpage && $action == "Display")
			{
				// print page selector
				$pagenum = (int) ($totalrecnum / $recnumperpage);
				if ($totalrecnum % $recnumperpage != 0)
				{
					$pagenum = $pagenum + 1;
				}
		?>
		        <table width="90%" border="0" cellspacing="0" cellpadding="4" bordercolor="#CCCCCC">
		        <tr>
		        <td align="center" bgcolor="#EEEEEE">
		        Page <?=$curpage?> of <?=$pagenum?>, Page: 
		        <?
		        for ($i=0; $i<$pagenum; $i++)
		        {
		        	if ($i + 1 == $curpage)
		        	{
		        		print $i+1;
		        	}
		        	else
		        	{
		        		print "<a href=\"query.php?action=Display&curpage=".($i+1)."&recnumperpage=$recnumperpage&from_year=$from_year&from_month=$from_month&to_year=$to_year&to_month=$to_month\">".($i+1)."</a>";
		        	}
		        	print "&nbsp;";
		        }
		        ?>
		        </td>
		        </tr>
				</table>
				<br>
		<?		
			}
		?>
		
		
        <table width="90%" border="1" cellspacing="0" cellpadding="4" bordercolor="#CCCCCC">
          <tr bgcolor="#EEEEEE"> 
            <td>ID</td>
            <td>Name</td>
            <td>Email</td>
            <td>Product</td>
            <td>Access Count</td>
            <td>Date</td>
            <td>Status</td>
          </tr>
        <?
        	for ($i=0; $i<$numrowslist; $i++)
        	{
        ?>
          <tr> 
            <td><a target="updatereg" href=updatereg.php?action=Display&intID=<?=$rowlist["intID"]?>><?=$rowlist["intID"]?></a></td>
            <td><?=$rowlist["strName"]?></td>
            <td><?=$rowlist["strRegName"]?></td>
            <td><?=$rowlist["strProductName"]?></td>
            <td><?=$rowlist["intDownloadCount"]?></td>
            <td><?=$rowlist["dateReg"]?></td>
            <td>
            <?
            	if ($rowlist["intStatus"] == 1)
            	{
            		print "Active";
            	}
            	else
            	{
            		print "Inactive";
            	}
            ?>
            </td>
          </tr>
        <?
				$rowlist = mysql_fetch_array($resultlist);
    		}
    	?>
        </table>
        <br>
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
