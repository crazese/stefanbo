<?

// $detail = 0;

// seed with microseconds
function make_seed() {
    list($usec, $sec) = explode(' ', microtime());
    return (float) $sec + ((float) $usec * 100000);
}


class auth{
	// CHANGE THESE VALUES TO REFLECT YOUR SERVER'S SETTINGS
    var $HOST = "localhost";        // DB HOST
    var $DBUSERNAME = "sothink";              // USERNAME
    var $DBPASSWORD = "K2v3P494";      // USER PASSWORD
    var $DBNAME = "authuser";       // DB NAME
    var $downurl = "/reguser/download.php?fid=";    // Download URL
    var $tempdir = "/var/www/temp/";                                // Directory to create temp file for mail send
    var $reg_gen = "/usr/bin/regcode";
    var $reg_gen_v2 = "/usr/bin/keymaker-pp";
 	var $RESELLERID;
	
	// Log
	function addlog($resellerid, $detail)
	{
		$strIP = getenv ("REMOTE_ADDR"); 
		$strDate = date("Y-m-j H:i:s");
		$qLog = "insert into AdminLog(intResellerID, strDetails, datTime, strIP) values (".$resellerid.
			",'$detail','$strDate', '$strIP')";
		// print $qLog;
		$connection = mysql_connect($this->HOST, $this->DBUSERNAME, $this->DBPASSWORD);
		$res = mysql_db_query($this->DBNAME, $qLog);
		
	}

	
	// AUTHENTICATE
	function authenticate($username, $password) {
		$query = "SELECT Team.teamname as teamname, Reseller.intID as intID
			FROM Reseller,Team 
			WHERE Reseller.intTeamID=Team.intID AND Reseller.uname='$username' AND Reseller.passwd='$password' AND Reseller.strStatus <> 'inactive'";
		
		
		// print $query;
		$connection = mysql_connect($this->HOST, $this->DBUSERNAME, $this->DBPASSWORD);
		$result = mysql_db_query($this->DBNAME, $query);
		// print $result;
		$numrows = mysql_num_rows($result);
		$row = mysql_fetch_array($result);
		
		// CHECK IF THERE ARE RESULS
		// Logic: If the number of rows of the resulting recordset is 0, that means that no
		// match was found. Meaning, wrong username-password combination.
		if ($numrows == 0) {
			// Login failure
			$this->RESELLERID = -1;
			return 0;		
		}
		elseif ($row["teamname"]=="Admin") {  
			// ADMIN LOGIN
			setcookie("cookie[username]", $username, time()+3600);
			setcookie("cookie[password]", $password, time()+3600);
			$this->RESELLERID = $row["intID"];
			return 1;
		}
		else {
			// Other Login
			setcookie("cookie[username]", $username, time()+3600);
			setcookie("cookie[password]", $password, time()+3600);
			$this->RESELLERID = $row["intID"];
			return 2;
		}
	} // End: function authenticate
	
	// MODIFY USERS
	function modify_user($userid, $strName, $username, $password, $team, $level, $status) {
		$qUpdate = "UPDATE Reseller SET strName='$strName',uname='$username', passwd='$password', intTeamID=$team, level='$level', strStatus='$status'
					WHERE intID=$userid";	
		// print $qUpdate;
		if (trim($level)=="") {
			return "blank level";
		}
		elseif (($username=="sa" AND status=="inactive")) {
			return "sa cannot be inactivated";
		}
		elseif (($username=="admin" AND status=="inactive")) {
			return "admin cannot be inactivated";
		}
		else {
			$connection = mysql_connect($this->HOST, $this->DBUSERNAME, $this->DBPASSWORD);
			$result = mysql_db_query($this->DBNAME, $qUpdate);
			return 1;
		}
		
	} // End: function modify_user
	
	// DELETE USERS
	function delete_user($username) {
		$qDelete = "DELETE FROM  Reseller WHERE uname='$username'";	

		if ($username == "sa") {
			return "User sa cannot be deleted.";
		}
		elseif ($username == "admin") {
			return "User admin cannot be deleted.";
		}
		elseif ($username == "test") {
			return "User test cannot be deleted.";
		}

		$connection = mysql_connect($this->HOST, $this->DBUSERNAME, $this->DBPASSWORD);
		$result = mysql_db_query($this->DBNAME, $qDelete);
		return mysql_error();
		
	} // End: function delete_user
	
	// ADD USERS
	function add_user($strName, $username, $password, $intTeamID, $level, $strStatus) {
		$qUserExists = "SELECT * FROM Reseller WHERE uname='$username'";
		$qInsertUser = "INSERT INTO Reseller(strName, uname, passwd, intTeamID, level, strStatus) 
				  			   VALUES ('$strName','$username', '$password', '$intTeamID', '$level', '$strStatus')";
		
		$connection = mysql_connect($this->HOST, $this->DBUSERNAME, $this->DBPASSWORD);
		
		// Check if all fields are filled up
		if (trim($username) == "") { 
			return "blank username";
		}
		elseif (trim($level) == "") {
			return "blank level";
		}
		
		// Check if user exists
		$user_exists = mysql_db_query($this->DBNAME, $qUserExists);
		if (mysql_num_rows($user_exists) > 0) {
			return "username exists";
		}
		else {
			// Add user to DB
			$result = mysql_db_query($this->DBNAME, $qInsertUser);
			
			return mysql_insert_id();
		}
	} // End: function add_user


	// *****************************************************************************************
	// ************************************** G R O U P S ************************************** 
	// *****************************************************************************************

	// ADD TEAM
	function add_team($teamname, $teamlead, $status="active") {
		$qGroupExists = "SELECT * FROM Team WHERE teamname='$teamname'";
		$qInsertGroup = "INSERT INTO Team(teamname, teamlead, strStatus) 
				  			   VALUES ('$teamname', '$teamlead', '$status')";
		
		$connection = mysql_connect($this->HOST, $this->DBUSERNAME, $this->DBPASSWORD);
		
		// Check if all fields are filled up
		if (trim($teamname) == "") { 
			return "blank team name";
		}
		
		// Check if group exists
		$group_exists = mysql_db_query($this->DBNAME, $qGroupExists);
		if (mysql_num_rows($group_exists) > 0) {
			return "group exists";
		}
		else {
			// Add user to DB
			$result = mysql_db_query($this->DBNAME, $qInsertGroup);
			
			if (mysql_affected_rows() <> 1)
			{
				return "Add team failure";
			}
			
			$result = mysql_db_query($this->DBNAME, $qGroupExists);
			if (mysql_num_rows($result) > 0 )
			{
				// print "Number of Row:".mysql_num_rows($result);
				$row = mysql_fetch_array($result);
				return $row["intID"];
			}
			else
				return 0;
		}
	} // End: function add_group
	
	// MODIFY TEAM
	function modify_team($teamid, $teamname, $teamlead, $status) {
		$qUpdate = "UPDATE Team SET teamlead='$teamlead', strStatus='$status', teamname='$teamname' WHERE intId=$teamid";
		// print $qUpdate;
		$qUserStatus = "UPDATE Reseller SET strStatus='$status' WHERE intTeamID = $teamid";

		if ($teamname == "Admin" AND $status=="inactive") {
			return "Admin team cannot be inactivated.";
		}
		elseif ($teamname == "Ungrouped" AND $status=="inactive") {
			return "Ungrouped team cannot be inactivated.";
		}
		else {		
			$connection = mysql_connect($this->HOST, $this->DBUSERNAME, $this->DBPASSWORD);
			
			// UPDATE STATUS IF STATUS OF TEAM IS INACTIVATED
			$userresult = mysql_db_query($this->DBNAME, $qUserStatus);
			
			$result = mysql_db_query($this->DBNAME, $qUpdate);
			return 1;
		}
		
	} // End: function modify_team

	// DELETE TEAM
	function delete_team($teamname) {
		$qDelete = "DELETE FROM Team WHERE teamname='$teamname'";
		$qUpdateUser = "UPDATE Reseller SET team='Ungrouped' WHERE team='$teamname'";	
		
		if ($teamname == "Admin") {
			return "Admin team cannot be deleted.";
		}
		elseif ($teamname == "Ungrouped") {
			return "Ungrouped team cannot be deleted.";
		}

		$connection = mysql_connect($this->HOST, $this->DBUSERNAME, $this->DBPASSWORD);
		$result = mysql_db_query($this->DBNAME, $qUpdateUser);
		$result = mysql_db_query($this->DBNAME, $qDelete);
		return mysql_error();
		
	} // End: function delete_team
	
	
	
	// ADD PRODUCT
	function add_product($productname, $unlocktype, $filepath, $codeversion, $codeseed)
	{
		$qProExists = "SELECT * FROM Product WHERE strProductName='$productname'";
		$qInsertPro = "INSERT INTO Product(strProductName, intUnlockType, strFilePath, intCodeVersion, strCodeSeed) 
				  			   VALUES ('$productname',$unlocktype, '$filepath', $codeversion, '$codeseed')";
		
		// print $qInsertPro;
		
		$connection = mysql_connect($this->HOST, $this->DBUSERNAME, $this->DBPASSWORD);
		
		// Check if all fields are filled up
		if (trim($productname) == "") { 
			return "blank product name";
		}
		
		// Check if Product exists
		$pro_exists = mysql_db_query($this->DBNAME, $qProExists);
		if (mysql_num_rows($pro_exists) > 0) {
			return "product name exists";
		}
		else {
			// Add Product to DB
			$result = mysql_db_query($this->DBNAME, $qInsertPro);

			return mysql_insert_id();
		}
	} // End: function add_product
	
	function modify_product($productid, $productname, $unlocktype, $filepath, $codeversion, $codeseed) {
		$qUpdate = "UPDATE Product SET strProductName='$productname', intUnlockType=$unlocktype, 
			strFilePath='$filepath', intCodeVersion =$codeversion, strCodeSeed='$codeseed' 
			WHERE intId=$productid";
		// Check if all fields are filled up
		if (trim($productname) == "") { 
			return "blank product name";
		}
		else
		{
			$connection = mysql_connect($this->HOST, $this->DBUSERNAME, $this->DBPASSWORD);
			
			// UPDATE STATUS IF STATUS OF TEAM IS INACTIVATED
			// print $qUpdate;
			$result = mysql_db_query($this->DBNAME, $qUpdate);
			return 1;
			
		}
		
	} // End of function modify_product
	
	// DELETE PRODUCT
	function delete_product($productname) {
		$qDelete = "DELETE FROM  Product WHERE strProductName='$productname'";	

		$connection = mysql_connect($this->HOST, $this->DBUSERNAME, $this->DBPASSWORD);
		$result = mysql_db_query($this->DBNAME, $qDelete);
		return mysql_error();
		
	} // End: function delete_user

	// ADD REG USER
	function add_reguser($username, $email, $productid, $resellerid, $days_to_expire) {
		// Get Product Info
		$qPro = "select * from Product where intID=".$productid;
		$connection = mysql_connect($this->HOST, $this->DBUSERNAME, $this->DBPASSWORD);
		$result = mysql_db_query($this->DBNAME, $qPro);
		//print $qPro."<br>";
		//print mysql_num_rows($result)."<br>";
		
		if (mysql_num_rows($result) > 0 )
		{
			// print "Number of Row:".mysql_num_rows($result);
			$row = mysql_fetch_array($result);
			$unlocktype = $row["intUnlockType"];
			$filepath = $row["strFilePath"];
			$codeversion = $row["intCodeVersion"];
			$codeseed = $row["strCodeSeed"];
			$productname = $row["strProductName"];
			//print $unlocktype.$filepath.$codeversion.$codeseed.$productname."<br>";
		}
		else
		{
			print "Cannot find product information";
			return -1;		// Cannot find product information;
		}

		if ($days_to_expire == 0)	// Never expire
		{
			$strDateExp = "0";
		}
		else
		{
			// print $days_to_expire;
			$strDateExp = date("Y-m-j H:i:s", mktime(date("H"), date("i"), date("s"), date("m"),  date("d")+ $days_to_expire, date("Y")));
			// print $strDateExp;
		}

		$strDate = date("Y-m-j H:i:s");

		if ($unlocktype == 1 || $unlocktype == 2) 
		{
			// Genrate random download link
			do 
			{
				mt_srand(make_seed());
				$randval = mt_rand();
				$randval1 = mt_rand();
				$randval2 = mt_rand();
				$path = $randval.$randval1.$randval2;
				
				// Make sure that this URL path is distinct
				$qFindSame = "select strURL from RegUser where strURL='".$path."'";
				// print $qFindSame;
				$ressame = mysql_db_query($this->DBNAME, $qFindSame);
				$recfound = mysql_num_rows($ressame);
			}
			while ($recfound > 0);

			if ($unlocktype == 2)
			{
				// Reg Code + File
				
				// Genreate Reg Code
				$retcode = 0;
				exec("$this->reg_gen $email $codeseed", $regcode, $retcode);
				if ($retcode == -1)
				{
					print "Cannot generate register code";
					return -1;
				}
				else
				{
					$theregcode = $regcode[0];
				}
			}
			else
			{
				// Full Version Download
				$theregcode = "";
			}
		}
		else if ($unlocktype == 3)
		{
			if ($codeversion == 1)
			{
				// Reg Code Ver 1, TODO
				print "Reg Code Ver 1, TODO";
				return -1;
			}
			else if ($codeversion == 2)
			{
				if ($productname == "Sothink DHTMLMenu 5")
				{
					$retcode = 0;
					exec("$this->reg_gen_v2 DHTMLMenu 5.0 1 $email 0000-0000", $regcode, $retcode);
					if ($retcode == 0)
					{
						$theregcode = $regcode[0];						
					}
					else
					{
						print "Cannot generate register code";
						return -1;
					}
				}
				else if ($productname == "Sothink SWF Quicker")
				{
					$retcode = 0;
					exec("$this->reg_gen_v2 Quicker 1.6 1 $email 0000-0000", $regcode, $retcode);
					if ($retcode == 0)
					{
						$theregcode = $regcode[0];						
					}
					else
					{
						print "Cannot generate register code";
						return -1;
					}
					
				}
				else if ($productname == "Sothink SWF Decompiler MX 2005")
				{
					$retcode = 0;
					exec("$this->reg_gen_v2 Decompiler MX2005A 1 $email 0000-0000", $regcode, $retcode);
					if ($retcode == 0)
					{
						$theregcode = $regcode[0];						
					}
					else
					{
						print "Cannot generate register code";
						return -1;
					}
					
				}
				else if ($productname == "Sothink Glanda 2005")
				{
					$retcode = 0;
					exec("$this->reg_gen_v2 Glanda 2005 1 $email 0000-0000", $regcode, $retcode);
					if ($retcode == 0)
					{
						$theregcode = $regcode[0];						
					}
					else
					{
						print "Cannot generate register code";
						return -1;
					}
					
				}
				else
				{
					print "cannot generate reg code for $productname";
					return -1;
				}
			}
			else
			{
				print "Unknown Code Version";
				return -1;
			}
		
		}
		else
		{
			print "Cannot find unlock type";
			return -1;
		}

		// Check if this reg entry exists
		$qRegExist = "select * from RegUser where intProductID=".$productid." and strRegName='$email' and strName='$username'";
		$resExist = mysql_db_query($this->DBNAME, $qRegExist);
		if (mysql_num_rows($resExist) > 0)
		{
			// Exist, update it and return
			$rowexist = mysql_fetch_array($resExist);
			$qRegUpdate = "update RegUser set 
				strName = '$username',
				intProductID = $productid,
				intResellerID = $resellerid,
				strURL = '$path',
				strRegCode = '$theregcode',
				strRegName = '$email',
				dateExpire = '$strDateExp'
				where intID=".$rowexist["intID"];

			$res = mysql_db_query($this->DBNAME, $qRegUpdate);
			if ('' != mysql_error())
			{
				print "Update Reg Entry Failure: $qRegUpdate";
				return -1;
			}
			return -2;
		}
		else
		{
			$qaddreguser = "insert into RegUser(strName, intProductID, intResellerID, strURL, intDownloadCount, intUpdateCount,strRegCode, strRegName, intStatus, dateReg, dateExpire)
				Values('$username', $productid, $resellerid, '$path', 0, 0, '$theregcode', '$email', 1 , '$strDate', '$strDateExp')";
			//  print $qaddreguser;
			$result = mysql_db_query($this->DBNAME, $qaddreguser);
			if ('' != mysql_error())
			{
				print "Insert Reg Entry Failure: $qaddreguser";
				return -1;
			}
	
			// Get Reg User ID
			return mysql_insert_id();
		}

	}	// End: add_reguser

} // End: class auth

?>
