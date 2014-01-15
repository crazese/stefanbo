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
	if ($detail != 1)		// Admin
	{
		header("Location: $failure");
	}
	// $connection = mysql_connect($dbhost, $dbusername, $dbpass);
	// $listteams = mysql_db_query($dbname, "SELECT * from authteam");
?>
<?
// ADD Product
$action = $_REQUEST['action'];
//echo $action;
//exit;
$productname = $_REQUEST['productname']; 
$unlocktype = $_REQUEST['unlocktype'];
$filepath = $_REQUEST['filepath'];
$codeversion = $_REQUEST['codeversion'];
$codeseed = $_REQUEST['codeseed'];
$productid = $_REQUEST['productid'];

if ($action == "Add") {
	$situation = $user->add_product($productname, $unlocktype, $filepath, $codeversion, $codeseed);
	
	if ($situation == "blank product name") {
		$message = "Product Name field cannot be blank.";
		$action = "";
	}
	elseif ($situation == "product name exists") {
		$message = "Product already exists in the database. Please enter a new one.";
		$action = "";
	}
	elseif ($situation >= 1) {
		// Log it
		$user->addlog($user->RESELLERID, "Add Product, ID:$situation, Name:$productname");
		$productid = $situation;
		$message = "New Product added successfully.".$situation;
	}
	else {
		$message = $situation;
	}
}

// DELETE Product
if ($action=="Delete") {
	$delete = $user->delete_product($productname);
	
	if ($delete) {
		$message = $delete;
	}
	else {
		// Log it
		$user->addlog($user->RESELLERID, "Delete Product, Name:$productname");
		$productname = "";
		$codeseed = "";
		$filepath = "";
		$message = "The product has been deleted.";
	}
}

// MODIFY USER
if ($action == "Modify") {	
	$update = $user->modify_product($productid, $productname, $unlocktype, $filepath, $codeversion, $codeseed);

	if ($update) {
		// Log it
		$user->addlog($user->RESELLERID, "Modify Product, ID:$productid, Name:$productname");
		$message = "User detail updated successfully.";
	}
	elseif ($update == "blank product name") {
		$message = "product Name field cannot be blank.";
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
	$productname= "";
	$unlocktype = 0;
	$filepath = "";
	$codeversion = 1;
	$codeseed = "";
	$message = "New product detail entry.";
}

?>
<html>
<head>
<title>Reseller Control Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#FFFFFF" text="#000000">
<p><font face="Arial, Helvetica, sans-serif" size="5"><b>Reseller Control Panel 
  Admin - Product</b></font></p>
<table width="95%" border="0" cellspacing="0" cellpadding="0" align="left">
  <tr valign="top"> 
    <td width="50%"> 
	  <form id="AddUser" name="AddUser" method="Post" action="products.php">
	    <table width="95%" border="1" cellspacing="0" cellpadding="2" align="center" bordercolor="#000000">
          <tr bgcolor="#000000"> 
            <td colspan="2"> 
              <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="3" color="#FFFFCC"><b>Product 
                DETAILS</b></font></div>
            </td>
          </tr>
          <tr valign="middle"> 
            <td width="45%" bgcolor="#33CCFF"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Product 
              Name </font></b></td>
            <td width="55%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp; 
              <?   
					print "<input type=\"hidden\" name=\"productid\" value=\"$productid\">"; 
					print "<input type=\"text\" name=\"productname\" size=\"20\" value=\"$productname\">"; 
			  ?>
              </font></td>
          </tr>
          <tr valign="middle"> 
            <td width="45%" bgcolor="#33CCFF"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Unlock 
              Type </font></b></td>
            <td width="55%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp; 
              <select name="unlocktype">
                <option value="1" <?if ($unlocktype=='1') print "selected";?>>Full Version</option>
                <option value="2" <?if ($unlocktype=='2') print "selected";?>>Reg Code + File</option>
                <option value="3" <?if ($unlocktype=='3') print "selected";?>>Reg Code</option>
              </select>
              </font></td>
          </tr>
          <tr valign="middle"> 
            <td width="45%" bgcolor="#33CCFF"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2">File 
              Path </font></b></td>
            <td width="55%"> &nbsp; 
              <input type="text" name="filepath" value=<?=$filepath?>>
            </td>
          </tr>
          <tr valign="middle"> 
            <td width="45%" bgcolor="#33CCFF"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Code 
              Generator Version</font></b></td>
            <td width="55%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp; 
              <select name="codeversion">
                <option value="1" <?if ($codeversion=='1') print "selected";?>>Ver 1.0</option>
                <option value="2" <?if ($codeversion=='2') print "selected";?>>Ver 2.0</option>                
              </select>
              </font></td>
          </tr>
          <tr valign="middle"> 
            <td width="45%" bgcolor="#33CCFF"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Code 
              Seed </font></b></td>
            <td width="55%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">
            &nbsp; <input type="text" name="codeseed" value="<?=$codeseed?>">
              </font></td>
          </tr>
          <tr bgcolor="#CCCCCC" valign="middle"> 
            <td colspan="2"> 
              <div align="center"><font size="2"><font size="2"><font size="2"><font face="Verdana, Arial, Helvetica, sans-serif"> 
                <?
					
				if (($action=="Add") || ($action == "Modify") || ($action=="Edit")) {
					print "<input type=\"submit\" name=\"action\" value=\"Add New\"  onClick=\"document.all.AddUser.action[1].value='Add New'\"> ";
					print "<input type=\"submit\" name=\"action\" value=\"Modify\" onClick=\"document.all.AddUser.action[1].value='Modify'\"> ";
					print "<input type=\"submit\" name=\"action\" value=\"Delete\" onClick=\"document.all.AddUser.action[1].value='Delete'\"> ";
				}
				else {
					print "<input type=\"submit\" name=\"action\" value=\"Add\" onClick=\"document.all.AddUser.action[1].value='Add'\"> ";
                }
				
				?>
				
                <input type="reset" name="Reset" value="Clear">
				</font></font></font></font>
                <input name="productid" type="hidden" id="productid" value="<?php echo $productid;?>">
              </div>
            </td>
          </tr>
          <?
				if (($action=="Add") || ($action == "Modify") || ($action=="Edit")) {
		  ?>          
          <tr bgcolor="#CCCCCC" valign="middle">
          	<td colspan=2 align=center>
          	<font face="Verdana, Arial, Helvetica, sans-serif" size="2">
          		<a href=build.php?intProductID=<?=$productid?> target=_blank>Build Management</a>
          	</font>
          	</td>
          </tr>
          <?
          		}
          ?>
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
          <td colspan="3"> 
            <div align="center"><font size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFCC"><b>Product 
              List</b></font></div>
          </td>
        </tr>
        <tr bgcolor="#CCCCCC"> 
          <td > 
            <div align="center"><font size="1"><b><font face="Verdana, Arial, Helvetica, sans-serif">Name</font></b></font></div>
          </td>
          <td width="25%"> 
            <div align="center"><font size="1"><b><font face="Verdana, Arial, Helvetica, sans-serif">ProductID</font></b></font></div>
          </td>
          <td width="25%"> 
            <div align="center"><font size="1"><b><font face="Verdana, Arial, Helvetica, sans-serif">Unlock 
              Type</font></b></font></div>
          </td>
        </tr>

<?
	// Fetch rows from AuthUser table and display ALL users
	$result = mysql_db_query($dbname, "SELECT * FROM Product ORDER BY intID");
	$row = mysql_fetch_array($result);
	while ($row) {  		
		print "<tr>"; 
        print "  <td>";
        print "    <div align=\"left\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">";
		print "		<a href=\"products.php?action=Edit&productid=".$row["intID"]."&productname=".$row["strProductName"]."&unlocktype=".$row["intUnlockType"]."&filepath=".$row["strFilePath"]."&codeversion=".$row["intCodeVersion"]."&codeseed=".$row["strCodeSeed"]."\">";
		print 		$row["strProductName"];
		print "		</a>";
		print "	   </font></div>";
        print "  </td>";
        print "  <td width=\"25%\">";
		print $row["intID"];			
        print "  </td>";
        print "  <td width=\"25%\">";
		if ($row["intUnlockType"] == "1") 
	        print "    <div align=\"center\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Full Version</font></div>";
		else if ($row["intUnlockType"] == "2") 
			print "    <div align=\"center\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Reg Code + File</font></div>";
		else if ($row["intUnlockType"] == "3") 
			print "    <div align=\"center\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Reg Code</font></div>";
				
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
<p>&nbsp;</p>
<p>&nbsp;</p>
<p><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b><a href="admin.php">&lt; 
  Back</a> <a href="logout.php">Logout</a></b></font></p>
</body>
</html>