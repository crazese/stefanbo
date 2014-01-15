<?
  	include ("auth.php"); 
	include ("authconfig.php");
    $username = trim($_REQUEST['username']);
    $password = trim($_REQUEST['password']);
	$Auth = new auth();
	$detail = $Auth->authenticate($username, $password);

	// print $detail;	// For debug
	
	if ($detail==0)
	{
		include ($failure);
	}
	elseif ($detail == 1) 
	{
		setcookie("cookie[username]", $username, time()+3600);
		setcookie("cookie[password]", $password, time()+3600);

		// Log it
		$Auth->addlog($Auth->RESELLERID, "Login, User ID:".$Auth->RESELLERID);
		header("Location: admin.php");
	}
	else 
	{
		setcookie("cookie[username]", $username, time()+3600);
		setcookie("cookie[password]", $password, time()+3600);
		
		// Log it		
		$Auth->addlog($Auth->RESELLERID, "Login, User ID:".$Auth->RESELLERID);
		header("Location: reseller.php");
	}
?>
