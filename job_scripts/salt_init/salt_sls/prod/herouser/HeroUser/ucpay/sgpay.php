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
function callurl($endpoint,$postData) {
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
function _get($str){
	if (isset($_REQUEST[$str]))	{		
		$val = $_REQUEST[$str];	
	} else {
		$val = null;
	}
    return $val;
}
$ORDERID = _get('orderId');
//$userid = _get('userid');
$callurl = 'http://www.pay168.com.tw/purepay/online/CheckTransStatus.aspx';
$payname = _get('payname');
$paydesc = _get('paydesc');
$itemCode = _get('itemCode');
$money = _get('money');
$currency = _get('currency');
$isStage = _get('isStage');
$payStatus = 'FAIL';
//$gameid = _get('gameid');
$fwqInfo = explode('|',$paydesc);
$gameid = $fwqInfo[0];
$userid = $fwqInfo[1];
/*统一编码：001
Shop ID： 12121201
MD5交易金鑰/MD5 Key：pvRYK-r6DKMZVp*f$W
MD5交易金鑰(測試用)MD5 Key (Testing Stage)：emU-N8g6GNQ+Yez^vZ * 
CHECKCODE規則：MD5(TransactionKey+”*”+SHOPID+”*”+ORDERID+”*”+ TransactionKey)
 */
$SHOPID = 12121201;
$TransactionKey = 'pvRYK-r6DKMZVp*f$W';
$CHECKCODE = md5($TransactionKey.'*'.$SHOPID.'*'.$ORDERID.'*'.$TransactionKey);
$checkResult = callurl($callurl,"SHOPID=$SHOPID&ORDERID=$ORDERID&CHECKCODE=$CHECKCODE");
if (!empty($checkResult)) {
	$checkResultInfo = explode(',',$checkResult);
	$payStatusInfo = explode('=',$checkResultInfo[1]);
	$payStatus = $payStatusInfo[1];
    $checkSql = "SELECT * FROM ".$common->tname('sg_payinfo')." WHERE orderId = '".$ORDERID."' && payStatus = '".$payStatus."' LIMIT 1";    
    $res_check = $db->query($checkSql);
    $checknum = $db->num_rows($res_check);
    if ($checknum == 0) {
    	$common->inserttable('sg_payinfo',array('orderId'=>$ORDERID,'userid'=>$userid,'payname'=>$payname,'paydesc'=>$paydesc,'itemCode'=>$itemCode,'money'=>$money,'currency'=>$currency,'isStage'=>$isStage,'payStatus'=>$payStatus,'gameid'=>$gameid));
    } else {
    	echo '1|Success';
    	exit;
    }	
    $sql_user = "SELECT * FROM ".$common->tname('player')." WHERE userid = $userid LIMIT 1";
    $res_user = $db->query($sql_user);
    $rows_user = $db->fetch_array($res_user);
    $oldYB = 0;
    $newYB = 0;
    $sfcg = 'F';  
    if ($payStatus == 0) { 
    	if ($money == 60) {
    		$yb = 120;
    	} elseif ($money == 150) {
    		$yb = 310;
    	} elseif ($money == 450) {
    		$yb = 970;
    	} elseif ($money == 990) {
    		$yb = 2200;
    	} elseif ($money == 1490) {
    		$yb = 3600;
    	} elseif ($money == 2990) {
    		$yb = 7500;
    	} else {
    		$yb = 0;
    	}
    	//$yb = floor($money * 10);
        if (!empty($rows_user)) {
        	$sfcg = 'S'; 
    		$oldYB = $rows_user['ingot'];
    		$newYB = $rows_user['ingot'] + $yb;
    		$playersid = $rows_user['playersid'];
    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
    		$db->query($sql_p);	    		 
    		//writelog('app.php?task=ucchongzhi&option=pay',json_encode(array('orderId'=>$insert['orderId'],'ucid'=>$insert['ucid'],'payWay'=>$insert['payWay'],'amount'=>$insert['amount'],'orderStatus'=>$insert['orderStatus'],'failedDesc'=>$insert['failedDesc'],'createTime'=>$insert['createTime'],'status'=>0)),$rows_user['player_level'],$rows_user['userid']);		
    		vipChongzhi($playersid, $money, $yb, $ORDERID);    		
    	}
    } else {
		$common->inserttable('sg_payinfo_error',array('orderId'=>$ORDERID,'userid'=>$userid,'payname'=>$payname,'paydesc'=>$paydesc,'itemCode'=>$itemCode,'money'=>$money,'currency'=>$currency,'isStage'=>$isStage,'payStatus'=>$payStatus,'gameid'=>$gameid));    	
    }
    echo '1|Success';
    writelog('app.php?task=chongzhi&type=sg&option=pay',json_encode(array('orderId'=>$ORDERID,'ucid'=>$rows_user['userid'],'payWay'=>'wbcz','amount'=>$money,'orderStatus'=>$sfcg,'failedDesc'=>'','createTime'=>time(),'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);	
} else {
	$common->inserttable('sg_payinfo_error',array('orderId'=>$ORDERID,'userid'=>$userid,'payname'=>$payname,'paydesc'=>$paydesc,'itemCode'=>$itemCode,'money'=>$money,'currency'=>$currency,'isStage'=>$isStage,'payStatus'=>$payStatus,'gameid'=>$gameid));
    echo '1|Success';
    exit;
}

