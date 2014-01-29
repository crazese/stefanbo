<?
	include ("auth.php");
	include ("authconfig.php");	
	$group = new auth();
    //echo $_COOKIE['cookie']['username'];
	//exit;
	// check cookie 
	if (isset ($_COOKIE['cookie']['username'])) 
	{
		// print "Cookie Set";
		// get password and username and check in database		
		$detail = $group->authenticate($_COOKIE['cookie']['username'], $_COOKIE['cookie']['password']);		
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
	$listusers = mysql_db_query($dbname, "SELECT * from Reseller");
	
?>

<?
// ADD GROUP
$action = $_POST['action'];
if (!$action)
$action = $_GET['action'];
//echo $action;
$teamname = trim($_POST['teamname']);
if (!$teamname)
$teamname = $_GET['teamname'];
$teamid = trim($_POST['teamid']);
if (!$teamid )
$teamid = $_GET['teamid'];
$teamlead = trim($_POST['teamlead']);
if (!$teamlead)
$teamlead = $_GET['teamlead'];
$status = trim($_POST['status']);
if (!$status)
$status = $_GET['$status'];
$teamid = $_REQUEST['teamid'];
if ($action == "Add") {
	$situation = $group->add_team($teamname, $teamlead, $status);
	
	if ($situation == "blank team name") {
		$message = "Team Name field cannot be blank.";
		$action = "";
	}
	elseif ($situation == "group exists") {
		$message = "Team Name already exists in the database. Please enter a new one.";
		$action = "";
	}
	elseif ($situation == "Add team failure") {
		$message = "Add Team Failure.";
		$action = "";
	}
	elseif ($situation == 0) {
		$message = "No team was added.";
		$action = "";
	}
	elseif ($situation > 1) {
		$teamid = $situation;
		$message = "New team added successfully.($teamid)";
		// Log it
		$group->addlog($group->RESELLERID, "Add Team, ID: $teamid, Name: $teamname");
	}
	else {
		$message = "";
	}
}

// DELETE GROUP
if ($action=="Delete") {
	$delete = $group->delete_team($teamname);
	
	if ($delete) {
		$message = $delete;
		$action = "";
	}
	else {
		// Log it
		$group->addlog($group->RESELLERID, "Delete Team, Name: $teamname");
		
		$teamname = "";
		$teamlead = "sa";
		$status = "active";
		$message = "The group has been deleted.<br>All users associated with the group are moved to the Ungrouped team";
	}
}

// MODIFY TEAM
if ($action == "Modify") {
	$update = $group->modify_team($teamid, $teamname, $teamlead, $status);

	if ($update==1) {
		$message = "Team detail updated successfully.";
		// Log it
		$group->addlog($group->RESELLERID, "Modify Team, ID: $teamid, Name: $teamname");
	}
	elseif ($update == "Admin team cannot be inactivated.") {
		$message = $update;
		$action = "";
	}
	elseif ($update == "Ungrouped team cannot be inactivated.") {
		$message = $update;
		$action = "";
	}
	elseif ($update == "Team Lead field cannot be blank.") {
		$message = $update;
		$action = "";
	}
	else {
		$message = "";
	}
}

// EDIT TEAM (accessed from clicking on username links)
if ($action == "Edit") {
	$message = "Modify team details.";
}

// CLEAR FIELDS
if ($action == "Add New") {
	$teamname = "";
	$teamlead = "sa";
	$status = "active";
	$message = "New team detail entry.";
}

?>

<html>
<head>
<title>Reseller Control Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF" text="#000000">
<p><font face="Arial, Helvetica, sans-serif" size="5"><b>Reseller Control Panel 
  Admin - Teams</b></font></p>
<table width="95%" border="0" cellspacing="0" cellpadding="0" align="left">
  <tr valign="top"> 
    <td width="50%"> 
      
	  <form name="AddTeam" method="Post" action="authgroup.php">
	    <table width="95%" border="1" cellspacing="0" cellpadding="0" align="center" bordercolor="#000000">
          <tr bgcolor="#000000"> 
            <td colspan="2"> 
              <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="3" color="#FFFFCC">
			  <b>TEAM DETAILS</b></font></div>
            </td>
          </tr>
          <tr valign="middle"> 
            <td width="27%" bgcolor="#33CCFF">
			<b><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Team Name </font></b></td>
            <td width="73%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp; 
              <?   
				print "<input type=\"text\" name=\"teamname\" size=\"15\" maxlength=\"15\" value=\"$teamname\">"; 
				print "<input type=\"hidden\" name=\"teamid\" value=\"$teamid\">"; 
			  ?>
              </font></td>
          </tr>
          <tr valign="middle"> 
            <td width="27%" bgcolor="#33CCFF"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2">
			Team Lead </font></b></td>
            <td width="73%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp; 
              <select name="teamlead">
                <?
			  	// DISPLAY MEMBERS
			  	$row = mysql_fetch_array($listusers);
			  	while ($row) {
					$memberlist = $row["uname"];
					
					if ($teamlead == $memberlist) {
						print "<option value=\"$memberlist\" SELECTED>" . $row["uname"] . "</option>";
					}
					else {
						print "<option value=\"$memberlist\">" . $row["uname"] . "</option>";
					}
					$row = mysql_fetch_array($listusers);
				}
			  ?>
              </select>
              <a href="authuser.php">Add</a></font></td>
          </tr>
          <tr valign="middle"> 
            <td width="27%" bgcolor="#33CCFF">
			<b><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Status</font></b></td>
            <td width="73%">
			<font face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp; 
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
              <div align="center"><font  size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?
					
				if (($action=="Add") || ($action == "Modify") || ($action=="Edit")) {
					print "<input type=\"submit\" name=\"action\" value=\"Add New\" onClick=\"document.all.AddTeam.action[1].value='Add New'\"> ";
					print "<input type=\"submit\" name=\"action\" value=\"Modify\" onClick=\"document.all.AddTeam.action[1].value='Modify'\"> ";
					print "<input type=\"submit\" name=\"action\" value=\"Delete\" onClick=\"document.all.AddTeam.action[1].value='Delete'\"> ";
				}
				else {
					print "<input type=\"submit\" name=\"action\" value=\"Add\" onClick=\"document.all.AddTeam.action[1].value='Add'\"> ";
                }
				
				?>
                <input type="reset" name="Reset" value="Clear">
                </font>
                <input name="teamid" type="hidden" id="teamid" value="<?php echo $teamid;?>">
              </div>
            </td>
          </tr>
        </table>
	  </form>
	  

      <p>&nbsp;</p>
      <table width="95%" border="1" cellspacing="0" cellpadding="0" align="center" bordercolor="#000000">
        <tr> 
          <td bgcolor="#990000">
		  <b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFCC">Message:</font></b></td>
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
      
	  
	  <table width="95%" border="1" cellspacing="0" cellpadding="0" align="center" bordercolor="#000000">
        <tr bgcolor="#000000"> 
          <td colspan="3"> 
            <div align="center"><font size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFCC">
			<b>TEAM LIST</b></font></div>
          </td>
        </tr>
        <tr bgcolor="#CCCCCC"> 
          <td width="35%"> 
            <div align="center"><font size="1"><b><font face="Verdana, Arial, Helvetica, sans-serif">Team 
              Name </font></b></font></div>
          </td>
          <td width="34%"> 
            <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><b>Team 
              Lead </b></font></div>
          </td>
          <td width="31%"> 
            <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><b>Status</b></font></div>
          </td>
        </tr>

<?
	// Fetch rows from AuthUser table and display ALL users
	$qQuery = "SELECT * FROM Team ORDER BY intID";
	
	$result = mysql_db_query($dbname, $qQuery);
	$row = mysql_fetch_array($result);
	while ($row) {  		
		print "<tr>"; 
        print "  <td width=\"35%\">";
        print "    <div align=\"left\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">";
		print "		<a href=\"authgroup.php?action=Edit&teamid=".$row["intID"]."&teamname=".$row["teamname"]."&teamlead=".$row["teamlead"]."&status=".$row["status"]."\">";
		print 		$row["teamname"];
		print "		</a>";
		print "	   </font></div>";
        print "  </td>";
        print "  <td width=\"34%\">";
        print "    <div align=\"center\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">".$row["teamlead"]."</font></div>";
        print "  </td>";
        print "  <td width=\"31%\">";
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
<p><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b><a href="admin.php">&lt; Back</a> 
  <a href="logout.php">Logout</a></b></font></p>
</body>
</html>
