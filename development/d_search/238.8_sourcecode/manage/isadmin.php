<?php
if(!isset($_SESSION['s_user']['isAdmin']))
{
	header("Location: ../error.php?msg=notAdmin"); 
	exit;
}
?>