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


$sql="select id,userName as '用户名',userPswd as '密码',
	(case userType when 0 then '超级管理员' 
	when 1 then '高级管理员' else '普通帐号' end) as '用户权限',
	truth_name as '姓名',
	workId as '工号',
	dptMent as '部门' 
	from admin_userinfo where userType >= '".intval($_SESSION['userQx'])."'";
$link=$conn->getConn();
$tb=new TurnTable("Mysql","sothinkcn",$link,$sql,"修改-删除","alterUser.php,400,300,205,320-del,saveUser.php");
$tb->setShowNum(10);
$page=$_REQUEST["page"] ? $_REQUEST["page"] : $_POST["page"];
$tb->setPage($page);
$tb->setThisPage("showUser.php");
$tb->showTable();
$tb->showTurnLink();
?></td>
  </tr>
</table>
</body>
</html>