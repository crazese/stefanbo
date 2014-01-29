<?php
@define('IN_HERO', TRUE);
define('D_BUG', '1');
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
function _get($str){
	//$magic_quote = get_magic_quotes_gpc();
	if (isset($_REQUEST[$str]))	{		
		$val = urldecode($_REQUEST[$str]);	
	} else {
		$val = null;
	}
    return $val;
}
$common = new heroCommon;
$db = new dbstuff;
$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname'],'utf8');
$db->query("set names 'utf8'");
include(dirname(dirname(__FILE__)).'/includes/vip_control.php');
function writelog($request,$return,$level=0,$userid=0) {
	global $common,$mc,$_HTTPSQS, $_LOG_INFO;	
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
		//$logHandle = fopen($log_path, 'a');
		$logHandle = fopen($log_path. "_$i", 'a');
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
//$raw_post_data = file_get_contents('php://input', 'r');
//$payInfo = json_decode($raw_post_data,true);
if (!empty($_REQUEST)) {
	$insert['returnCode'] = _get('returnCode');
	$insert['rechargeId'] = _get('rechargeId');
	$insert['rechargeMoney'] = _get('rechargeMoney');
	$insert['rechargeType'] = _get('rechargeType');
	$insert['userId'] = _get('game_userid');
	$insert['TomUserId'] = _get('userId');
	$insert['orderId'] = _get('orderId');
	$insert['orderStatus'] = _get('orderStatus');
	$insert['payId'] = _get('payId');
	$insert['payType'] = _get('payType');
	$insert['payMoney'] = _get('payMoney');
	$insert['time'] = _get('time');
	$insert['Balance'] = _get('Balance');
	$insert['ssoId'] = _get('ssoId');
	$insert['versionId'] = _get('versionId');
	//$insert['versionId'] = '';
	$insert['payKey'] = _get('payKey');
	$makesign = md5('businessId='.$_SC['TOM_BID'].'&returnCode='.$insert['returnCode'].'&rechargeId='.$insert['rechargeId'].'&rechargeMoney='.$insert['rechargeMoney'].'&rechargeType='.$insert['rechargeType'].'&userId='.$insert['TomUserId'].'&orderId='.$insert['orderId'].'&orderStatus='.$insert['orderStatus'].'&payId='.$insert['payId'].'&payMoney='.$insert['payMoney'].'&payType='.$insert['payType'].'&time='.$insert['time'].'&Balance='.$insert['Balance'].'&ssoId='.$insert['ssoId'].'&versionId='.$insert['versionId'].'&'.$_SC['TOM_KEY']);
	if ($makesign == $insert['payKey']) {
    	$checkSql = "SELECT * FROM ".$common->tname('tom_payinfo')." WHERE orderId = '".$insert['orderId']."' && orderStatus = '". $insert['orderStatus']."' LIMIT 1";
    	$res_check = $db->query($checkSql);
    	$checknum = $db->num_rows($res_check);
    	if ($checknum == 0) {
    		$common->inserttable('tom_payinfo',$insert);
    	} else {
    		echo 'SUCC';
    		exit;
    	}
        $sql_user = "SELECT a.userid,b.playersid,b.ingot,b.player_level FROM ".$common->tname('user')." a,".$common->tname('player')." b WHERE a.userid = '".$insert['userId']."' && a.userid = b.userid LIMIT 1";
    	$res_user = $db->query($sql_user);
    	$rows_user = $db->fetch_array($res_user);
    	$oldYB = 0;
    	$newYB = 0;  
    	$orderStatus = "F"; 
        if ($insert['orderStatus'] == 1) {
	    	$yb = floor($insert['rechargeMoney'] / 100 * 10);    		
	    	if (!empty($rows_user)) {
	    		$oldYB = $rows_user['ingot'];
	    		$newYB = $rows_user['ingot'] + $yb;
	    		$playersid = $rows_user['playersid'];
	    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
	    		$db->query($sql_p);	    		 
	    		//writelog('app.php?task=ucchongzhi&option=pay',json_encode(array('orderId'=>$insert['orderId'],'ucid'=>$insert['ucid'],'payWay'=>$insert['payWay'],'amount'=>$insert['amount'],'orderStatus'=>$insert['orderStatus'],'failedDesc'=>$insert['failedDesc'],'createTime'=>$insert['createTime'],'status'=>0)),$rows_user['player_level'],$rows_user['userid']);		
	    		vipChongzhi($playersid, round($insert['rechargeMoney'] / 100,3), $yb, $insert['orderId'], null, true, true, 'tom');
	    		$orderStatus = 'S';
	    	}	    	
    	} 
        writelog('app.php?task=chongzhi&type=tom&option=pay',json_encode(array('orderId'=>$insert['orderId'],'ucid'=>$insert['userId'],'payWay'=>$insert['rechargeType'],'amount'=>$insert['rechargeMoney'] / 100,'orderStatus'=>$orderStatus,'failedDesc'=>$insert['returnCode'],'createTime'=>time(),'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);	
    } else {
    	$common->inserttable('tom_payinfo_error',$insert);
    }
    echo 'SUCC';
} else {
	echo 'SUCC';
}
