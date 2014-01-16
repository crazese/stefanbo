<?
	setcookie("cookie[password]", "", time()-3600);
	setcookie("cookie[username]", "", time()-3600);
	setcookie("cookie[level]", "", time()-3600);
	header("Location: index.php");
?>