<?php
require dirname(__FILE__) . '/ucapi/libs/UzoneRestApi.php';

$uzone_token  = isset($_GET['uzone_token']) ? str_replace(' ','+',urldecode($_GET['uzone_token'])) : '';
$inviteid = isset($_GET['inviteid']) ? $_GET['inviteid'] : '';
$uid = isset($_GET['uid']) ? $_GET['uid'] : '';
$UzoneRestApi = new UzoneRestApi($uzone_token);

if (!$UzoneRestApi->checkIsAuth()){
	echo("token error");
   //$backUrl      = 'http://hero.iguodong.com:9000/index.php?inviteid='.$inviteid;
   //$UzoneRestApi->redirect2SsoServer($backUrl);
}
echo($uzone_token);
$header = $UzoneRestApi->callMethod('layout.get', array('type' => 'header'));


$res = $UzoneRestApi->callMethod('friends.getFriends', array('page' => 1, 'pageSize' => 200));
if ($UzoneRestApi->checkIsCallSuccess()){ 
	$arr_friends = $res;
	echo(count($arr_friends));
}

//程序目录
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('OP_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR);

//基本文件
include_once(S_ROOT.'./config.php');
include_once(S_ROOT.'./includes/class_mysql.php');
include_once(S_ROOT.'./includes/class_common.php');
$db = new dbstuff;
$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname']);
$db->query("set names 'utf8'");
//时间
$mtime = explode(' ', microtime());
$_SGLOBAL['timestamp'] = $mtime[1];
$_SGLOBAL['supe_starttime'] = $_SGLOBAL['timestamp'] + $mtime[0];
//$session = new MysqlSession($db->link);
$common = new heroCommon;

include_once(OP_ROOT.'./hero_user'.DIRECTORY_SEPARATOR.'controller.php');
include_once(OP_ROOT.'./hero_user'.DIRECTORY_SEPARATOR.'model.php');
include_once(OP_ROOT.'./ClientView.php');

if(!empty($arr_friends)) {
	for($i = 0 ; $i < count($arr_friends); $i++ ) {
		if($i == 0) {
			$db->query("delete from hero.ol_uc_friends where ucid = '".$uid."'");
		}
		$db->query("insert into hero.ol_uc_friends (ucid,fucid,realname) value ('".$uid."','".$arr_friends[$i]['uid']."','".$arr_friends[$i]['real_name']."') ");
	}	
}

?>