<?php
//echo 'OK';exit;
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
include(dirname(dirname(__FILE__)).'/includes/class_common.php');
$mc = new MemcacheAdapter_Memcached;	
$mc->addServer($MemcacheList[0], $Memport);
$common = new heroCommon;
$db = new dbstuff;
$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname'],'utf8');
$db->query("set names 'utf8'");
include(dirname(dirname(__FILE__)).'/includes/vip_control.php');
//$raw_post_data = file_get_contents('php://input', 'r');
$raw_post_data = $_REQUEST;
//$raw_post_data = json_decode('{"sign":"73ad292dba83232187490163c804beea","tradeStatus":"TRADE_SUCCESS","appId":"1357","tradeId":"201302210002","reqFee":"1.00","invoice":"7a15972e950140728cc7811cacd00a10","tradeName":"10yuanbao","payerId":"8","paidFee":"1.00","notifyDatetime":"2013-02-21 15:53:02"}', true);
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
//$common->insertLog(json_encode($_REQUEST));

if (!empty($raw_post_data)) {
	$sign = $raw_post_data['sign'];
	$trade_no = $raw_post_data['invoice'];
	$ord_no = $raw_post_data['tradeId'];
	$notify_time = $raw_post_data['notifyDatetime'];
	$paidFee = $raw_post_data['paidFee'];
	$tradeStatus =$raw_post_data['tradeStatus'];
	$payerId = $raw_post_data['payerId'];
	$appId = $raw_post_data['appId'];
	$reqFee = $raw_post_data['reqFee'];
	$tradeName = $raw_post_data['tradeName'];
	$makesign = md5("appId={$appId}&invoice={$trade_no}&notifyDatetime={$notify_time}&paidFee={$paidFee}&payerId={$payerId}&reqFee={$reqFee}&tradeId={$ord_no}&tradeName={$tradeName}&tradeStatus={$tradeStatus}7DA95D6C9E5EFCEDBE83AF7B8D9FEC8B");
	//$common->insertLog(json_encode($insert) . ' | ' . $makesign . ' | ' . $sign);
    if ($makesign == $sign) {
    	$checkSql = "SELECT * FROM ".$common->tname('easoupay_ord')." WHERE ord_no = '".$ord_no."' LIMIT 1";
    	$res_check = $db->query($checkSql);
    	$res_rows = $db->fetch_array($res_check);
    	$checknum = $db->num_rows($res_check);
    	$sql_user = "SELECT userid,playersid,ingot,player_level FROM ". $common->tname('player')." WHERE playersid = '".$res_rows['playersid']."' LIMIT 1";
    	$res_user = $db->query($sql_user);
    	$rows_user = $db->fetch_array($res_user);
    	if ($tradeStatus == 'TRADE_SUCCESS' && $res_rows['verify'] == 0) {
	    	$yb = floor($paidFee * 10);
	    	$notify_time = strtotime($notify_time);
	    	if (!empty($rows_user)) {
	    		$sql_o = "UPDATE ".$common->tname('easoupay_ord')." SET trade_no = '{$trade_no}', notify_time={$notify_time}, total_fee = {$paidFee}, verify = 1 WHERE ord_no = '".$ord_no."'";
	    		$db->query($sql_o);
	    		
	    		$playersid = $rows_user['playersid'];
	    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + $yb WHERE playersid = '$playersid' LIMIT 1";
	    		$db->query($sql_p);	    		 
	    		//writelog('app.php?task=ucchongzhi&option=pay',json_encode(array('orderId'=>$insert['orderId'],'ucid'=>$insert['ucid'],'payWay'=>$insert['payWay'],'amount'=>$insert['amount'],'orderStatus'=>$insert['orderStatus'],'failedDesc'=>$insert['failedDesc'],'createTime'=>$insert['createTime'],'status'=>0)),$rows_user['player_level'],$rows_user['userid']);		
	    		vipChongzhi($playersid, $paidFee, $yb, $ord_no);
	    	}
	    	$newIngo = $rows_user['ingot'] + $yb;
	    	writelog('app.php?task=chongzhi&type=easoupay&option=pay',json_encode(array('orderId'=>$ord_no,'ucid'=>'','payWay'=>'easou','amount'=>$paidFee,'orderStatus'=>1,'failedDesc'=>'','createTime'=>$notify_time,'status'=>0,'oldYB'=>$rows_user['ingot'], 'newYB'=>$newIngo)),$rows_user['player_level'],$rows_user['userid']);
    	}
        	   	
    } else {
    	echo 'ILLEGAL_SIGN';
    }
    echo 'OK';
} else {
	echo 'NOT_OK';
}
