<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>save user</title>
</head>

<body>
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$oc_dbStr="../../";
require_once("../../Inc/Conn.inc");

$linkid = intval($_GET['id'] ? $_GET['id'] : $_POST['id']);
$action = $_GET['action'] ? $_GET['action'] : $_POST['action'];
$username = addslashes($_GET['username'] ? $_GET['username'] : $_POST['username']);
$password = addslashes($_GET['password'] ? $_GET['password'] : $_POST['password']);
$ifalterpswd = $_GET['ifalterpswd'] ? $_GET['ifalterpswd'] : $_POST['ifalterpswd'];
$masterQx = intval($_GET['masterQx'] ? $_GET['masterQx'] : $_POST['masterQx']);
$truthname = addslashes($_GET['truthname'] ? $_GET['truthname'] : $_POST['truthname']);
$workId = addslashes($_GET['workId'] ? $_GET['workId'] : $_POST['workId']);
$dptment = addslashes($_GET['dptment'] ? $_GET['dptment'] : $_POST['dptment']);
$page = intval($_GET['page'] ? $_GET['page'] : $_POST['page']);

if($action == "saveadd"){
	$sql="insert into admin_userinfo(userName,userPswd,userType,truth_name,workId,dptMent) 
		values('".$username."','".$password."','".$masterQx."','".$truthname."','".$workId."','".$dptment."')";
	mysql_query($sql) or die("Invalid query : ". mysql_error() . "<br/>");
	
	echo "<script language=javascript>
		<!--
		alert(\"添加成功！\");
		history.back(0);
		-->
		</script>";
}elseif($action == "savealter"){
	session_start();
	$sql="update admin_userinfo 
		set userName='".$username."',
		truth_name='".$truthname."',
		workId='".$workId."',
		dptMent='".$dptment."' ";
	if($masterQx >= intval($_SESSION['userQx'])){	//security setting
		$sql=$sql.",userType='".$masterQx."' ";
	}	
	
	if($ifalterpswd == "1"){
		$sql = $sql.",userPswd='".$password."' ";
	}
	
	$sql = $sql." where id='".$linkid."' and userType >= '".intval($_SESSION['userQx'])."'";
	
	//echo $sql;
	mysql_query($sql) or die("Invalid query : ". mysql_error() . "<br/>");
	echo "<script language=javascript>
		<!--
		alert(\"修改成功！\");
		window.opener.location.reload();
		self.close();
		-->
		</script>";
}elseif($action == "del"){
	$sql = "delete from admin_userinfo where id = '".$linkid."'";
	mysql_query($sql) or die("Invalid query : ". mysql_error() . "<br/>");
	echo "<script language=javascript>
		<!--
		alert(\"删除成功！\");
		location.href='showUser.php?page=$page';
		-->
		</script>";
}
?>
</body>
</html>