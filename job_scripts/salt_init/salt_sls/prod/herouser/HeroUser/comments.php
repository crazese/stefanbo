<?php
require dirname(__FILE__) . '/ucapi/libs/UzoneRestApi.php';
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);

//程序目录
define('OP_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR);

//基本文件
include_once(S_ROOT.'./config.php');
include_once(S_ROOT.'./includes/class_mysql.php');
include_once(S_ROOT.'./includes/class_common.php');
//include_once(S_ROOT.'./includes/session.php');
$db = new dbstuff;
$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname']);
$db->query("set names 'utf8'");
//时间
$mtime = explode(' ', microtime());
$_SGLOBAL['timestamp'] = $mtime[1];
$_SGLOBAL['supe_starttime'] = $_SGLOBAL['timestamp'] + $mtime[0];
//$session = new MysqlSession($db->link);
$commond = new heroCommon;


include_once(OP_ROOT.'./hero_user'.DIRECTORY_SEPARATOR.'controller.php');
include_once(OP_ROOT.'./hero_user'.DIRECTORY_SEPARATOR.'model.php');
include_once(OP_ROOT.'./ClientView.php');
		
$uzone_token  = isset($_GET['uzone_token']) ? str_replace(' ','+',urldecode($_GET['uzone_token'])) : '';
$inviteid = isset($_GET['inviteid']) ? $_GET['inviteid'] : '';
$message = '';
if($uzone_token == '') {
	$uzone_token  = isset($_POST['uzone_token']) ? str_replace(' ','+',urldecode($_POST['uzone_token'])) : '';
}
$UzoneRestApi = new UzoneRestApi($uzone_token);
$uid = $UzoneRestApi->getAuthUid();
$is_check = false;
$action = isset($_POST['action']) ? $_POST['action'] : '';
$bit = 0;
if ($action == "comments") {
	$message = isset($_POST['message']) ? $_POST['message'] : '';
	$uid = isset($_POST['ucid']) ? $_POST['ucid'] : '';
	$message = $commond->shtmlspecialchars($message);
	$i = strlen($message);
    if($i >300 || $i == 0){
       $is_check = true;
     }else{
	   $db->query("insert into hero_log.ol_comments (ucid,message,create_time) value ('".$uid."','".$message."','".time()."')");
	   $bit = 1;
     }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>水浒OL之铁血英雄</title>
</head>

<body>
<form action="comments.php" method="post">
<input type="hidden" id="action" name="action" value="comments" />
<input type="hidden" id="ucid" name="ucid" value="<?php echo($uid);?>" />
<input type="hidden" id=uzone_token name="uzone_token" value="<?php echo($uzone_token);?>" />
<?php
if($bit == 1 && $is_check == false) {
?>
&nbsp;&nbsp;&nbsp;发送成功，非常感谢!
<p></p>
<br/><a href="index.php<?php echo("?uzone_token=".$uzone_token."&inviteid=".$inviteid);?>">&nbsp;&nbsp;&nbsp;返回游戏首页</a>
<br/><a href="http://uzonetest.uc.cn:8084/ucsns4/">&nbsp;&nbsp;&nbsp;返回乐园</a>
<br/>
<?php
}elseif($uzone_token == ''){
?>
<a href='http://uzonetest.uc.cn:8084/ucsns4/'>请先登录UC乐园</a>
<?php
}else{
?>
&nbsp;&nbsp;&nbsp;欢迎您提出宝贵意见，请在下框内输入内容（限100字内）！
<br/><input name="message" type="text" id="message" maxlength="200" value="<?php echo($message);?>"/>
<br/><input name="" type="submit" id="" value="确定" />
<?php
if($is_check == true) {
	echo('	内容不能为空或不能超过上限');
}
?>
<p></p>
<br/><a href="index.php<?php echo("?uzone_token=".$uzone_token."&inviteid=".$inviteid);?>" >&nbsp;&nbsp;&nbsp;返回游戏首页</a>
<br/>
<?php }?>
</form>
</body>
</html>
