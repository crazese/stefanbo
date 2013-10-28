<?php
$mtime = explode(' ', microtime());
$_SGLOBAL['timestamp'] = $mtime[1];
$_SGLOBAL['supe_starttime'] = $_SGLOBAL['timestamp'] + $mtime[0];
define('S_ROOT', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
header("content-type:text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
$cpId = '690';
$gameId9y = '506135';
$serverId9y = '1980';
$appkey9y = 'f23e3bdea56048a71c5100ee824ca699';
//基本文件
include(dirname(dirname(__FILE__)) .'/config.php');
include(dirname(dirname(__FILE__)) .'/includes/class_mysql.php');
require(dirname(dirname(__FILE__)).'/configs/ConfigLoader.php');
require(dirname(dirname(__FILE__)).'/model/PlayerMgr.php');
require(dirname(dirname(__FILE__)).'/includes/class_memcacheAdapter.php');
include(dirname(dirname(__FILE__)).'/includes/class_common.php');
$mc = new MemcacheAdapter_Memcached;	
$mc->addServer($MemcacheList[0], $Memport);
$common = new heroCommon;
$db = new dbstuff;
$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname'],'utf8');
$db->query("set names 'utf8'");
include(dirname(dirname(__FILE__)).'/includes/vip_control.php');
$raw_post_data = file_get_contents('php://input', 'r');
$raw_post_data = json_decode($raw_post_data,true);
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
	$insert['createTime'] = time();
	$insert['processed'] = 1;
	$makesign = md5($cpId.'amount='.$insert['amount'].'callbackInfo='.$insert['callbackInfo'].'failedDesc='.$insert['failedDesc'].'gameId='.$gameId9y.'orderId='.$insert['orderId'].'orderStatus='.$insert['orderStatus'].'payWay='.$insert['payWay'].'serverId='.$serverId9y.'ucid='.$payInfo['ucid'].$appkey9y);
	//$common->insertLog(json_encode($insert) . ' | ' . $makesign . ' | ' . $sign);
    if ($makesign == $sign) {
    	$checkSql = "SELECT * FROM ".$common->tname('uc_payinfo')." WHERE orderId = '".$insert['orderId']."' LIMIT 1";
    	$res_check = $db->query($checkSql);
    	$res_rows = $db->fetch_array($res_check);
    	$checknum = $db->num_rows($res_check);
    	if ($checknum == 0) {
    		$common->inserttable('uc_payinfo',$insert);
    	} else if($res_rows['orderStatus'] != 'F') {    		
    		echo 'SUCCESS';
    		exit;
    	}
        //$sql_user = "SELECT a.userid,b.playersid,b.ingot,b.player_level FROM ".$common->tname('user')." a,".$common->tname('player')." b WHERE a.username = '".$insert['ucid']."' && a.userid = b.userid LIMIT 1";
    	$sql_user = "SELECT userid,playersid,ingot,player_level FROM ". $common->tname('player')." WHERE ucid = '".$insert['ucid']."' LIMIT 1";
    	$res_user = $db->query($sql_user);
    	$rows_user = $db->fetch_array($res_user);
    	if ($insert['orderStatus'] == 'S') {
	    	$yb = floor($insert['amount'] * 10);
	    	if (!empty($rows_user)) {
	    		if($res_rows['orderStatus'] != 'F') {
	    			$sql_o = "UPDATE ".$common->tname('uc_payinfo')." SET orderStatus = 'S' WHERE orderId = '".$insert['orderId']."'";
	    			$db->query($sql_o);
	    		}
	    		$playersid = $rows_user['playersid'];
	    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
	    		$db->query($sql_p);	    		 
	    		//writelog('app.php?task=ucchongzhi&option=pay',json_encode(array('orderId'=>$insert['orderId'],'ucid'=>$insert['ucid'],'payWay'=>$insert['payWay'],'amount'=>$insert['amount'],'orderStatus'=>$insert['orderStatus'],'failedDesc'=>$insert['failedDesc'],'createTime'=>$insert['createTime'],'status'=>0)),$rows_user['player_level'],$rows_user['userid']);		
	    		vipChongzhi($playersid, $insert['amount'], $yb, $insert['orderId']);
	    	}
	    	$newIngo = $rows_user['ingot'] + $yb;
	    	writelog('app.php?task=chongzhi&type=9ypage&option=pay',json_encode(array('orderId'=>$insert['orderId'],'ucid'=>$insert['ucid'],'payWay'=>$insert['payWay'],'amount'=>$insert['amount'],'orderStatus'=>$insert['orderStatus'],'failedDesc'=>$insert['failedDesc'],'createTime'=>$insert['createTime'],'status'=>0,'oldYB'=>$rows_user['ingot'], 'newYB'=>$newIngo)),$rows_user['player_level'],$rows_user['userid']);
    	}
        	   	
    } else {
    	$common->inserttable('uc_payinfo_error',$insert);
    }
    echo 'SUCCESS';
} else {
	echo 'FAILURE';
}
