<?php
echo json_encode(array('status'=>'ok'));
/*header("Pragma: no-cache");
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);

include(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
//设置时区
date_default_timezone_set('Asia/Shanghai');

$uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : null;
$vcode = isset($_REQUEST['vcode']) ? $_REQUEST['vcode'] : null;
$config = require dirname(dirname(dirname(dirname(__FILE__)))) . '/ucapi/conf/Api.inc.php'; 
$myvcode = substr(md5($config['secret'] . '2_'.$uid), 0, 10);
if($vcode == $myvcode) {
	$mc = new Memcache;
	for ($m = 0; $m < 3; $m++) {
		if ($mc->addServer($MemcacheList[0], $Memport) == true) {
			$mcConnect = true;
			break;
		}
	}
	session_id($mc->get(MC.'2_'.$uid.'_session'));
	session_start();
	unset($_SESSION['playersid']);
	echo json_encode(array('status'=>'ok'));
} else {
	echo json_encode(array('status'=>'error'));
}*/

?>