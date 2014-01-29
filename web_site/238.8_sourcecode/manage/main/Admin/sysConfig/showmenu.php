<?php
session_start();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>show admin users</title>
<link href="../../CSS/turn.css" rel="stylesheet" type="text/css">
<link href="../CSS/admin.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="javascript" src="../../JS/turnTable.js"></script>
</head>

<body>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="2"><?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$oc_dbStr="../../";
require_once "../../Inc/Turn.inc";


$sql="select id,higherMenu as '上级菜单',
	menuName as '菜单名',
	(case masterQx when 0 then '超级管理员' 
	when 1 then '高级管理员' else '普通帐号' end) as '访问权限',
	(case menuLevel when 0 then '一级菜单'
	when 1 then '二级菜单' when 2 then '三级菜单' end) as '菜单级别',
	orderNum as '排序' 
	from sys_menu order by higherMenu";

$link=$conn->getConn();
$tb=new TurnTable("Mysql","sothinkcn",$link,$sql,"修改-删除","alterMenu.php,400,320,205,320-del,saveMenu.php");
$tb->setShowNum(20);
$page=$_REQUEST["page"] ? $_REQUEST["page"] : $_POST["page"];
$tb->setPage($page);
$tb->setThisPage("showMenu.php");
$tb->showTable();
$tb->showTurnLink();
?></td>
  </tr>
</table>
</body>
</html>