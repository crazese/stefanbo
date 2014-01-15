<?php
  	include ("auth.php"); 
	$username=$_POST['username'];
	$password=$_POST['password'];
	$Auth = new auth();
	$detail = $Auth->authenticate($username, $password);

	if ($detail==1)
	{
		setcookie("cookie[username]", $username, time()+3600);
		setcookie("cookie[password]", $password, time()+3600);
		header("Location: product_manage.php");
	}
	else 
	{
		header("Location:failed.php ");
	}
?>