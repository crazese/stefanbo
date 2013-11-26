<?php
$mtime = explode(' ', microtime());
$_SGLOBAL['timestamp'] = $mtime[1];
$_SGLOBAL['supe_starttime'] = $_SGLOBAL['timestamp'] + $mtime[0];
define('S_ROOT', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
header("content-type:text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
//基本文件
include(dirname(dirname(__FILE__)) .'/config.php');
include(dirname(dirname(__FILE__)) .'/includes/class_mysql.php');
require(dirname(dirname(__FILE__)).'/configs/ConfigLoader.php');
require(dirname(dirname(__FILE__)).'/model/PlayerMgr.php');
require(dirname(dirname(__FILE__)).'/includes/class_memcacheAdapter.php');
$mc = new MemcacheAdapter_Memcached;	
$mc->addServer($MemcacheList[0], $Memport);
include(dirname(dirname(__FILE__)).'/includes/class_common.php');

$common = new heroCommon;
$db = new dbstuff;
$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname'],'utf8');
$db->query("set names 'utf8'");
include(dirname(dirname(__FILE__)).'/includes/vip_control.php');
function callUrl($endpoint,$postData,$auth_header = '') {
	$ch = curl_init($endpoint);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
	if (!empty($auth_header)) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($auth_header));			
	}	
	$response = curl_exec($ch);
	$errno    = curl_errno($ch);
	$errmsg   = curl_error($ch);
	curl_close($ch);
	if ($errno != 0) {
		return false;
	} else {
		return $response;
	}
}
function writelog($request,$return,$level=0,$userid=0) {
	global $common,$mc, $_LOG_INFO;	
	$nowTime = time();	
	$letters_trade['request_url'] = $request;
    $letters_trade['create_time'] = $nowTime;	
    $letters_trade['level'] = $level;
	$letters_trade['userid'] = $userid;
	$letters_trade['result'] = $return;
	$log_path = $_LOG_INFO['path'] . $_LOG_INFO['prefix'] . date('Y_m_d_H',$nowTime).'_'.intval($nowTime/$_LOG_INFO['split_t']);
	$log_json_value = json_encode($letters_trade);
	$flag = false;
	for($i=1; $i<=5; $i++){
		$logHandle = fopen($log_path."_{$i}", 'a');
		if(flock($logHandle, LOCK_EX|LOCK_NB)){
			$w_long = fwrite($logHandle, $log_json_value."\n");
			if(0 < $w_long){
				$flag = true;
				fclose($logHandle);
				break;
			}
		}
		fclose($logHandle);
	}
	if(!$flag){
		$log['logValue'] = $log_json_value;
		$common->inserttable('bck_log', $log);
	} else{
		syslog(LOG_ALERT, 'game log write log need userid');
	}
}

function _get($str){
	if (isset($_REQUEST[$str]))	{		
		$val = trim($_REQUEST[$str]);	
	} else {
		$val = null;
	}
    return $val;
}

function zfsj($url,$data) {
	if (!empty($data)) {
		foreach ($data as $key => $value) {
			$str[] = $key.'='.$value;
		}
		$sendData = implode('&',$str);
		$result = callUrl($url,$sendData);
		echo $result;
	}
}