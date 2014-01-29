<?php
session_start();
/*	include ("auth.php"); 
	include ("authconfig.php");  	
	$Auth = new auth();
	// check cookie 
	
	if (isset($_COOKIE['cookie'])) 
	{
		// print "Cookie Set";
		// get password and username and check in database
		$detail = $Auth->authenticate($cookie[username], $cookie[password]);
	}
	else
	{
		// print "Cookie Not Set";
		$detail = 0;
		header("Location: $failure");
	}*/
/*	if (!isset($_COOKIE['cookie']['sothinkusername']) && !isset($_COOKIE['cookie']['sothinkpassword']))
	{
	   header("Location:/manage/main/Admin/login.php");	   
	}
*/
if (!isset($_SESSION['userName']))
{
  header("Location:/manage/main/Admin/login.php");
}
?>