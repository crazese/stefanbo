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
$raw_post_data = file_get_contents('php://input', 'r');
$raw_post_data = json_decode($raw_post_data,true);
function callucpay($endpoint,$postData) {
	$ch = curl_init($endpoint);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

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
if (!empty($raw_post_data)) {
	$nowTime = time();
	$payInfo = $raw_post_data['data'];
	$sign = $raw_post_data['sign'];
	$insert['orderId'] = $payInfo['orderId'];
	$insert['gameId'] = $payInfo['gameId'];
	$insert['serverId'] = $payInfo['serverId'];
	$insert['ucid'] = $payInfo['ucid'];
	$insert['payWay'] = $payInfo['payWay'];
	$insert['amount'] = $payInfo['amount'];
	$insert['callbackInfo'] = $payInfo['callbackInfo'];
	$insert['orderStatus'] = $payInfo['orderStatus'];
	$insert['failedDesc'] = mysql_escape_string($payInfo['failedDesc']);
	$insert['createTime'] = $nowTime;
	$insert['processed'] = 1;
	$backInfo = explode('|',$insert['callbackInfo']);
	$userid = 0;
	if (count($backInfo) > 1) {
		$fwqdm = $backInfo[1];
		$userid = $backInfo[0];
	} else {
		$fwqdm = $insert['callbackInfo']; 
	}
	if ($fwqdm == '01_002' && $_SC['fwqdm'] != '01_002') {  //端游2服
		$callRes = callucpay('http://117.135.139.165/ucpay/ucpay.php',json_encode($raw_post_data));
		if (empty($callRes)) {
			$insert['toserver'] = 2;
			$common->inserttable('uc_payinfo_error',$insert);
		} else {
			echo $callRes;
		}
	} elseif ($fwqdm == '01_003' && $_SC['fwqdm'] != '01_003') { //端游3服
		$callRes = callucpay('http://117.135.139.165:8080/ucpay/ucpay.php',json_encode($raw_post_data));
		if (empty($callRes)) {
			$insert['toserver'] = 2;
			$common->inserttable('uc_payinfo_error',$insert);
		} else {
			echo $callRes;
		}
	} else {	
		$makesign = md5($_SC['cpId'].'amount='.$insert['amount'].'callbackInfo='.$insert['callbackInfo'].'failedDesc='.$insert['failedDesc'].'gameId='.$_SC['gameId'].'orderId='.$insert['orderId'].'orderStatus='.$insert['orderStatus'].'payWay='.$insert['payWay'].'serverId='.$_SC['serverId'].'ucid='.$insert['ucid'].$_SC['apiKey']);
	    if ($makesign == $sign) {
	    	$checkSql = "SELECT * FROM ".$common->tname('uc_payinfo')." WHERE orderId = '".$insert['orderId']."' && orderStatus = '".$insert['orderStatus']."' LIMIT 1";
	    	$res_check = $db->query($checkSql);
	    	$checknum = $db->num_rows($res_check);
	    	if ($checknum == 0) {
	    		$common->inserttable('uc_payinfo',$insert);
	    	} else {
	    		echo 'SUCCESS';
	    		exit;
	    	}
	    	if ($userid > 0) {
	    		$sql_user = "SELECT * FROM ".$common->tname('player')." WHERE userid = $userid LIMIT 1";
	    	} else {
	    		$sql_user = "SELECT a.userid,b.playersid,b.ingot,b.player_level FROM ".$common->tname('user')." a,".$common->tname('player')." b WHERE a.username = '".$insert['ucid']."' && a.userid = b.userid LIMIT 1";
	    	}	        
	    	$res_user = $db->query($sql_user);
	    	$rows_user = $db->fetch_array($res_user);
	    	$oldYB = 0;
	    	$newYB = 0;
	    	if ($insert['orderStatus'] == 'S') {
		    	$yb = floor($insert['amount'] * 10);    		
		    	if (!empty($rows_user)) {
		    		$oldYB = $rows_user['ingot'];
		    		$newYB = $rows_user['ingot'] + $yb;
		    		$playersid = $rows_user['playersid'];
		    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
		    		$db->query($sql_p);	    		 
		    		//writelog('app.php?task=ucchongzhi&option=pay',json_encode(array('orderId'=>$insert['orderId'],'ucid'=>$insert['ucid'],'payWay'=>$insert['payWay'],'amount'=>$insert['amount'],'orderStatus'=>$insert['orderStatus'],'failedDesc'=>$insert['failedDesc'],'createTime'=>$insert['createTime'],'status'=>0)),$rows_user['player_level'],$rows_user['userid']);		
		    		vipChongzhi($playersid, $insert['amount'], $yb, $insert['orderId']);
		    	}
	    	}
	        writelog('app.php?task=chongzhi&type=uc&option=pay',json_encode(array('orderId'=>$insert['orderId'],'ucid'=>$insert['ucid'],'payWay'=>$insert['payWay'],'amount'=>$insert['amount'],'orderStatus'=>$insert['orderStatus'],'failedDesc'=>$insert['failedDesc'],'createTime'=>$insert['createTime'],'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);	   	
	    } else {
	    	$common->inserttable('uc_payinfo_error',$insert);
	    }
	    echo 'SUCCESS';
	}
} else {
	echo 'FAILURE';
}
