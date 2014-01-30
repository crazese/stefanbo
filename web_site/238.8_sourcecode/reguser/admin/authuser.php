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

	if ($detail != 1)
	{
		header("Location: $failure");
	}

	$connection = mysql_connect($dbhost, $dbusername, $dbpass);
	$listteams = mysql_db_query($dbname, "SELECT * from Team");
	
?>

<?
// ADD USER
$action = $_REQUEST['action'];
//echo $action;
$strName = $_REQUEST['strName'];
$username = $_REQUEST['username'];
$password = $_REQUEST['password'];
$team = $_REQUEST['team'];;
$level = $_REQUEST['level'];
$status = $_REQUEST['status'];
$userid = $_REQUEST['userid'];

if ($action == "Add") {
	$situation = $user->add_user($strName, $username, $password, $team, $level, $status);
	
	if ($situation == "blank username") {
		$message = "Username field cannot be blank.";
		$action = "";
	}
	elseif ($situation == "username exists") {
		$message = "Username already exists in the database. Please enter a new one.";
		$action = "";
	}
	elseif ($situation == "blank level") {
		$message = "Level field cannot be blank.";
		$action = "";
	}
	elseif ($situation >= 1) {
		$message = "New user added successfully.";
		// Log it
		$user->addlog($user->RESELLERID, "Add Reseller, ID:$situation, Name:$username(team:$team)");
	}
	else {
		$message = "Add User Failure";
	}
}

// DELETE USER
if ($action=="Delete") {
	$delete = $user->delete_user($username);
	
	if ($delete) {
		$message = $delete;
	}
	else {
		// Log it
		$user->addlog($user->RESELLERID, "Delete Reseller, Name:$username");

		$strName = "";
		$username = "";
		$password = "";
		$team = 1;
		$level = "";
		$status = "active";
		$message = "The user has been deleted.";
	}
}

// MODIFY USER
if ($action == "Modify") {
	$update = $user->modify_user($userid, $strName, $username, $password, $team, $level, $status);

	if ($update) {
		// Log it
		$user->addlog($user->RESELLERID, "Modify Reseller, ID:$userid, Name:$username, Status:$status");
		
		$message = "User detail updated successfully.";
	}
	elseif ($update == "blank level") {
		$message = "Level field cannot be blank.";
		$action = "";
	}
	else {
		$message = "";
	}
}

// EDIT USER (accessed from clicking on username links)
if ($action == "Edit") {
	$message = "Modify user details.";
}

// CLEAR FIELDS
if ($action == "Add New") {
	$strName = "";
	$username = "";
	$password = "";
	$team = "1";
	$level = "";
	$status = "active";
	$message = "New user detail entry.";
}

?>

<html>
<head>
<title>Reseller Control Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF" text="#000000">
<p><font face="Arial, Helvetica, sans-serif" size="5"><b>Reseller Control Panel 
  Admin - Resellers</b></font></p>
<table width="95%" border="0" cellspacing="0" cellpadding="0" align="left">
  <tr valign="top"> 
    <td width="50%"> 
      
	  <form name="AddUser" method="Post" action="authuser.php">
	    <table width="95%" border="1" cellspacing="0" cellpadding="0" align="center" bordercolor="#000000">
          <tr bgcolor="#000000"> 
            <td colspan="2"> 
              <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="3" color="#FFFFCC"><b>Reseller 
                DETAILS</b></font></div>
            </td>
          </tr>
          <tr valign="middle"> 
            <td width="27%" bgcolor="#33CCFF"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Reseller 
              Name </font></b></td>
            <td width="73%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp; 
              <?   
					print "<input type=\"text\" name=\"strName\" size=\"15\" maxlength=\"15\" value=\"$strName\">"; 
				
			  ?>
              </font></td>
          </tr>
           <tr valign="middle"> 
            <td width="27%" bgcolor="#33CCFF"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Username</font></b></td>
            <td width="73%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp; 
              <?   
					print "<input type=\"hidden\" name=\"userid\" value=\"$userid\">"; 
					print "<input type=\"text\" name=\"username\" size=\"15\" maxlength=\"15\" value=\"$username\">"; 
				
			  ?>
              </font></td>
          </tr>
          <tr valign="middle"> 
            <td width="27%" bgcolor="#33CCFF"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Password</font></b></td>
            <td width="73%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp; 
              <? print "<input type=\"password\" name=\"password\" size=\"15\" maxlength=\"15\" value=\"$password\">"; ?>
              </font></td>
          </tr>
          <tr valign="middle"> 
            <td width="27%" bgcolor="#33CCFF"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Group</font></b></td>
            <td width="73%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp; 
              <select name="team">
                <?
			  	// DISPLAY TEAMS
			  	$row = mysql_fetch_array($listteams);
			  	while ($row) {
					$teamlist = $row["teamname"];
					$teamid = $row["intID"];
					
					if ($team == $teamid) {
						print "<option value=\"$teamid\" SELECTED>" . $row["teamname"] . "</option>";
					}
					else {
						print "<option value=\"$teamid\">" . $row["teamname"] . "</option>";
					}
					$row = mysql_fetch_array($listteams);
				}
			  ?>
              </select>
              <a href="authgroup.php">Add</a></font></td>
          </tr>
          <tr valign="middle"> 
            <td width="27%" bgcolor="#33CCFF"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Level</font></b></td>
            <td width="73%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp; 
              <? print "<input type=\"text\" name=\"level\" size=\"4\" maxlength=\"4\" value=\"$level\">"; ?>
              This field is not used, set it to "2"</font></td>
          </tr>
          <tr valign="middle"> 
            <td width="27%" bgcolor="#33CCFF"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Status</font></b></td>
            <td width="73%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp; 
              <select name="status">
                <?
			  	// ACTIVE / INACTIVE
				if ($status == "inactive") {
					print "<option value=\"active\">Active</option>";
                	print "<option value=\"inactive\" selected>Inactive</option>";
				}
				else {
					print "<option value=\"active\" selected>Active</option>";
                	print "<option value=\"inactive\">Inactive</option>";
				}
              
			  ?>
              </select>
              </font></td>
          </tr>
          <tr bgcolor="#CCCCCC" valign="middle"> 
            <td colspan="2"> 
              <div align="center"><font size="2"><font size="2"><font size="2"><font face="Verdana, Arial, Helvetica, sans-serif"> 
                <?
					
				if (($action=="Add") || ($action == "Modify") || ($action=="Edit")) {
					print "<input type=\"submit\" name=\"action\" value=\"Add New\" onclick=\"AddUser.action.value='Add New'\" onClick=\"document.all.AddUser.action[1].value='Add New'\"> ";
					print "<input type=\"submit\" name=\"action\" value=\"Modify\" onclick=\"AddUser.action.value='Add New'\" onClick=\"document.all.AddUser.action[1].value='Modify'\"> ";
					print "<input type=\"submit\" name=\"action\" value=\"Delete\" onClick=\"document.all.AddUser.action[1].value='Delete'\"> ";
				}
				else {
					print "<input type=\"submit\" name=\"action\" value=\"Add\" onClick=\"document.all.AddUser.action[1].value='Add'\"> ";
                }
				
				?>
                <input type="reset" name="Reset" value="Clear">
                </font></font></font></font>
                <input name="userid" type="hidden" id="userid" value="<?php echo $userid;?>">
              </div>
            </td>
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
          <td colspan="4"> 
            <div align="center"><font size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFCC"><b>Reseller 
              List</b></font></div>
          </td>
        </tr>
        <tr bgcolor="#CCCCCC"> 
          <td width="30%"> 
            <div align="center"><font size="1"><b><font face="Verdana, Arial, Helvetica, sans-serif">Reseller Name</font></b></font></div>
          </td>
          <td width="25%"> 
            <div align="center"><font size="1"><b><font face="Verdana, Arial, Helvetica, sans-serif">Username</font></b></font></div>
          </td>
          <td width="24%"> 
            <div align="center"><font size="1"><b><font face="Verdana, Arial, Helvetica, sans-serif">Group</font></b></font></div>
          </td>
          <td width="21%"> 
            <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><b>Status</b></font></div>
          </td>
        </tr>

<?
	// Fetch rows from AuthUser table and display ALL users
	$result = mysql_db_query($dbname, "SELECT Reseller.intID as intID, Reseller.strName as strName, Reseller.uname as uname, Reseller.passwd as passwd, Reseller.intTeamID as team, Team.teamname as teamname, Reseller.level as level, Reseller.strStatus as strStatus FROM Reseller,Team where Reseller.intTeamID=Team.intID ORDER BY Reseller.intID");
	$row = mysql_fetch_array($result);
	while ($row) {  		
		print "<tr>"; 
		print "  <td width=\"30%\">";
        print "    <div align=\"left\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">";
		print "		<a href=\"authuser.php?action=Edit&userid=".$row["intID"]."&strName=".$row["strName"]."&username=".$row["uname"]."&password=".$row["passwd"]."&team=".$row["team"]."&level=".$row["level"]."&status=".$row["strStatus"]."\">";
		print 		$row["strName"];
		print "		</a>";
		print "	   </font></div>";
        print "  </td>";
        print "  <td width=\"25%\">";
        print "    <div align=\"left\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">";
		print 		$row["uname"];
		print "	   </font></div>";
        print "  </td>";
        print "  <td width=\"24%\">";
        print "    <div align=\"center\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">".$row["teamname"]."</font></div>";
        print "  </td>";
        print "  <td width=\"21%\">";
        print "    <div align=\"right\"><font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">".($row["strStatus"])."</font></div>";
        print "  </td>";
        print "</tr>";
		
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
<p><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b><a href="admin.php">&lt; 
  Back</a> <a href="logout.php">Logout</a></b></font></p>
</body>
</html>
