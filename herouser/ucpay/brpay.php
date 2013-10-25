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
	//$magic_quote = get_magic_quotes_gpc();
	if (isset($_REQUEST[$str]))	{		
		$val = $_REQUEST[$str];	
	} else {
		$val = null;
	}
    return $val;
}
function encpw($src) {
	$spw=$src;
	  //$spw=base64_decode($src);
	$Xbox1 = array( chr(75), chr(99), chr(200), chr(24),  chr(64),  chr(10), chr(23), chr(52) );
	$Xbox2 = array( chr(12), chr(28), chr(21),  chr(100), chr(29),  chr(44), chr(87), chr(23) );
	$Xbox3 = array( chr(23), chr(11), chr(33),  chr(134), chr(123), chr(29), chr(12), chr(12));
	$Xbox4 = array( chr(39), chr(22), chr(19),  chr(103), chr(145), chr(199),chr(20), chr(77) );
	$len = strlen( $spw);
	for ( $c = 0; $c <$len; $c++ ) {
	  $index = $c % 8;
	  $spw[$c] =$spw[$c]^ $Xbox1[$index];
	  $spw[$c] =$spw[$c]^ $Xbox2[$index];
	  $spw[$c] =$spw[$c]^ $Xbox3[$index];
	  $spw[$c] =$spw[$c]^ $Xbox4[$index];
	}
	for (  $pos=0; $pos < $len;  $pos++ ) {  
	  if (ord($spw[$pos])==0) {
	  	$spw[$pos] = $src[$pos];
	  }
	}
	return base64_encode($spw);  
}

$insert['result'] = _get('result');
$insert['order_id'] = _get('order_id');
$insert['amount'] = _get('amount');
$insert['pay_amount'] = _get('pay_amount');
$insert['game_id'] = _get('game_id');
$insert['msg'] = _get('msg');
$insert['card_code1'] = _get('card_code1');
$insert['verifystr'] = _get('verifystr');
$insert['userId'] = _get('game_userid');
$nowTime = $insert['systime'] = time();
$mkjm = encpw("key=26582147c68e02cb58&result=".$insert['result']."&order_id=".$insert['order_id']."&amount=".$insert['amount']."&pay_amount=".$insert['pay_amount']."&game_id=".$insert['game_id']);
if ($insert['verifystr'] == $mkjm) {
    $checkSql = "SELECT * FROM ".$common->tname('br_payinfo')." WHERE order_id = '".$insert['order_id']."' && result = '". $insert['result']."' LIMIT 1";
    $res_check = $db->query($checkSql);
    $checknum = $db->num_rows($res_check);
    if ($checknum == 0) {
    	$common->inserttable('br_payinfo',$insert);
    } else {
    	echo 'success';
    	exit;
    }
    $sql_user = "SELECT a.userid,b.playersid,b.ingot,b.player_level FROM ".$common->tname('user')." a,".$common->tname('player')." b WHERE a.userid = '".$insert['userId']."' && a.userid = b.userid LIMIT 1";
    $res_user = $db->query($sql_user);
    $rows_user = $db->fetch_array($res_user);
    $oldYB = 0;
    $newYB = 0;  
    $orderStatus = "F"; 
    if ($insert['result'] == 0) {
    	$yb = floor($insert['pay_amount'] * 10);    		
    	if (!empty($rows_user)) {
    		$oldYB = $rows_user['ingot'];
    		$newYB = $rows_user['ingot'] + $yb;
    		$playersid = $rows_user['playersid'];
    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
    		$db->query($sql_p);	    		 
    		//writelog('app.php?task=ucchongzhi&option=pay',json_encode(array('orderId'=>$insert['orderId'],'ucid'=>$insert['ucid'],'payWay'=>$insert['payWay'],'amount'=>$insert['amount'],'orderStatus'=>$insert['orderStatus'],'failedDesc'=>$insert['failedDesc'],'createTime'=>$insert['createTime'],'status'=>0)),$rows_user['player_level'],$rows_user['userid']);		
    		vipChongzhi($playersid, $insert['pay_amount'], $yb, $insert['order_id']);
    		$orderStatus = 'S';
    	}	    	
    }
    writelog('app.php?task=chongzhi&type=br&option=pay',json_encode(array('orderId'=>$insert['order_id'],'ucid'=>$insert['userId'],'payWay'=>1,'amount'=>$insert['pay_amount'],'orderStatus'=>$orderStatus,'failedDesc'=>'','createTime'=>time(),'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);	    	
} else {
	$common->inserttable('br_payinfo_error',$insert);
}
echo 'success';
