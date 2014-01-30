<?php 
session_start();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link href="CSS/menu.css" rel="stylesheet" type="text/css">
<title></title>
<script type="text/javascript" src="JS/oc_sysMenu.js"></script>
<style type="text/css">
<!--
body,td,th {
	font-family: 宋体;
	font-size: 12px;
	color: #000000;
}
a:link {
	color: #000066;
}
a {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
}
-->
</style></head>

<body>
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
if($_SESSION['userName'] != ""){
	$oc_dbStr="../";
	require_once "../Inc/Conn.inc";
	require_once "../Config/Config.php";
	require_once "./getMenu.inc";

	$menu=new oc_sysMenu($sysMenu_level,$_SESSION['userQx']);
	$menu->showTopMenus();
	//$menu->echoItem();
}
?>
</body>
</html>