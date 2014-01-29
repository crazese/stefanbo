<?php
header("Content-Type:text/html; charset=utf-8");
include(dirname(__FILE__) . '/config.php');
include(dirname(__FILE__) . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "common.php");
include(dirname(__FILE__) . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "function.php");
$link = mysql_connect($_SC['dbhost'], $_SC['dbuser'], $_SC['dbpw']);
mysql_selectdb($_SC['dbname'], $link);
mysql_query("set names 'utf8'", $link);
$Key = '6xmua6$%^&fds5po';
$GameServerID = _get('GameServerID');
$AccountID = $userid91 =  _get('AccountID');
$Timestamp = _get('Timestamp');
$Sign = _get('Sign');
//$_REQUEST['vn'] = $_GET['vn'] = 1.34;
if ($GameServerID == 1) {
	$_REQUEST['vn'] = $_GET['vn'] = 6.1;
} else {
	$_REQUEST['vn'] = $_GET['vn'] = 6.2;
}
//$_REQUEST['vn'] = $_GET['vn'] = 1.34;
$_REQUEST['ly'] = 10;
//Sign = MD5({AccountID}_{GameServerID}_{Timestamp}_{Key})
if (md5($AccountID.'_'.$GameServerID.'_'.$Timestamp.'_'.$Key) == $Sign) {
	$userName = '91_h5_1_'.$AccountID;
	$nickName = '';
	$returnValue = sdKlogin::regist($userName,$nickName,$link);
	//print_r($returnValue);
	$userId = $returnValue['userId'];
	$token = $returnValue['token'];
	$slist = urlencode(json_encode($returnValue['slist']));
	$error = 0;
} else {
	$error = 1;
}
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width = device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>用户登录</title>
</head>
<style type="text/css">
body,td,th {
	font-family:"Microsoft YaHei", sans-serif;
	font-size:13px;
	color:#555555;
}
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	line-height:20px;
}
p{padding:0px; margin:0px; line-height:30px; padding:5px 0 0 5px;}
table{line-height:27px;}
table tr td{padding-left:5px;}
.box{width:130px; height:20px; line-height:20px; color:#000; border:1px solid #cccccc;}
.boxbtn{height:28px; width:60px; margin-top:5px;}
</style>
<?php if ($error == 0) {?>
<script language="javascript">
function submitsj() {
	document.tjsj.submit();	
}
</script>
<?php }?>
<body onload = submitsj();>
<?php if ($error == 0) {?>
<form action="http://h5.jofgame.com/herool_91/herool.php" id="tjsj" name="tjsj" method="post">
	<input type="hidden" value=0 name="status" />
	<input type="hidden" value="<?php echo $userId;?>" name="userId" />
	<input type="hidden" value="<?php echo $token;?>" name="token" />
	<input type="hidden" value="<?php echo $slist;?>" name="slist" />
	<input type="hidden" value="<?php echo $userid91;?>" name="userid91" />
</form>
<?php } else {?>
<div>登录失败，请重新登录</div>
<?php }?>
</body>
</html>
