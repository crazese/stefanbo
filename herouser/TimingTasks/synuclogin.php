<?php
define('D_BUG', '1');
@define('IN_HERO', TRUE);
//程序目录
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR.'../');
define('OP_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR.'../components'.DIRECTORY_SEPARATOR);
$mtime = explode(' ', microtime());
$_SGLOBAL['timestamp'] = $mtime[1];
$_SGLOBAL['supe_starttime'] = $_SGLOBAL['timestamp'] + $mtime[0];
//设置时区
date_default_timezone_set('Asia/Shanghai');
include(S_ROOT.'config.php');
include(S_ROOT.'includes/class_mysql.php');
include(S_ROOT.'includes/class_common.php');
//基本文件
include(OP_ROOT.'hero_fight'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'hero_fight'.DIRECTORY_SEPARATOR.'model_fight.php');
include(OP_ROOT.'hero_fight'.DIRECTORY_SEPARATOR.'model_attack.php');
include(OP_ROOT.'hero_fight'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'hero_city'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'hero_city'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'hero_city'.DIRECTORY_SEPARATOR.'model_java.php');
include(OP_ROOT.'hero_city'.DIRECTORY_SEPARATOR.'model_general.php');
include(OP_ROOT.'ClientView.php');
include(OP_ROOT.'hero_role'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'hero_tools'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'hero_tools'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'hero_zyd'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'hero_zyd'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'hero_zyd'.DIRECTORY_SEPARATOR.'controller.php');
include(OP_ROOT.'hero_letters'.DIRECTORY_SEPARATOR.'model.php'); 
require(S_ROOT.'configs/ConfigLoader.php');
require(S_ROOT.'model/PlayerMgr.php');
require(S_ROOT.'includes/class_memcacheAdapter.php');
include(OP_ROOT.'./hero_social'.DIRECTORY_SEPARATOR.'controller.php');
include(OP_ROOT.'./hero_social'.DIRECTORY_SEPARATOR.'model.php');
$db = new dbstuff;
$mc = new MemcacheAdapter_Memcached;
$G_PlayerMgr = new PlayerMgr($db,$mc);
$common = new heroCommon;
for ($m = 0; $m < 3; $m++) {
   if ($mc->addServer($MemcacheList[0], $Memport) == true) {
   	    $mcConnect = true;
   		break;
   }
}
$user = 'admin';
$pass = 'admin';
//$ip = $_SERVER['SERVER_ADDR'];
$ip = $_SC['domain'];
$token = file_get_contents('php://input', 'r');
$auth = new authentication_header($user, $pass, $ip);
$authvalues = new SoapVar($auth, SOAP_ENC_OBJECT, 'authenticate');
$header = new SoapHeader(WS_SERVER, 'authenticate', $authvalues);

$client = new SoapClient(null,array('location' =>USER_SERVER . "authserver/server.php", 'uri' => "http://127.0.0.1/"));
$client->__setSoapHeaders(array($header));
try {
	$ret = $client->getuidinfo($token);	
} catch(Exception $e) {
	echo false;
}
if($ret === 3) {
	echo false;
} else {
	$retInfo = explode('-',$ret);
	$uid = $retInfo[0];
	$olduid = $retInfo[3];
	$common->updatetable('user',"username = '$uid'","username = '$olduid' limit 1");
	$common->updatetable('player',"ucid = '$uid'","ucid = '$olduid' limit 1");
	echo 1;
}