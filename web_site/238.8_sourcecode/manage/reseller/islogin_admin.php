<?php
	if($_COOKIE['cookie']=="" || $_COOKIE['cookie']['level']!=1)
	{
	   header("Location:/manage/reseller/index.php");
	}
?>