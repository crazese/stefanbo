<?php
@define('IN_HERO', TRUE);
define('D_BUG', '0');
$_REQUEST['client'] = 0;
define('S_ROOT', dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR);
include(dirname(dirname(dirname(dirname(__FILE__)))) . '/includes/class_mysql.php');
include(dirname(dirname(dirname(dirname(__FILE__)))) . '/includes/class_common.php');	
include(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require(dirname(dirname(dirname(dirname(__FILE__))))  .'/configs/ConfigLoader.php');
require(dirname(dirname(dirname(dirname(__FILE__))))  .'/model/PlayerMgr.php');
require(dirname(dirname(dirname(dirname(__FILE__))))  .'/includes/class_memcacheAdapter.php');
require dirname(__FILE__).'/ucpro.php';

$mc = new MemcacheAdapter_Memcached;	
$mc->addServer($MemcacheList[0], $Memport);

$common = new heroCommon;
$db = new dbstuff;
$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname'],'utf8');
$db->query("set names 'utf8'");

include(dirname(dirname(dirname(dirname(__FILE__)))) . '/includes/vip_control.php');

$result=isset($_POST['result']) ? $_POST['result'] : '';
$error_code=isset($_POST['error_code']) ? $_POST['error_code'] : '';
$order_id=isset($_POST['order_id']) ? $_POST['order_id'] : '';
$user_id=isset($_POST['user_id']) ? $_POST['user_id'] : ''; //正式
$user_id = '2_' . $user_id;
$recharge_amt=isset($_POST['recharge_amt']) ? $_POST['recharge_amt'] : '';
$pay_amt=isset($_POST['pay_amt']) ? $_POST['pay_amt'] : '';
$trade_id=isset($_POST['trade_id']) ? $_POST['trade_id'] : '';
$trade_time=isset($_POST['trade_time']) ? $_POST['trade_time'] : '';
$sign=isset($_POST['sign']) ? $_POST['sign'] : '';

$_POST['trade_time'] = urldecode($_POST['trade_time']);

$sql = "insert into ol_uc_fill (result,error_code,order_id,user_id,recharge_amt,pay_amt,trade_id,
trade_time,sign) values ('".$result."','".$error_code."','".$order_id."','".$user_id."',
'".$recharge_amt."','".$pay_amt."','".$trade_id."','".$trade_time."','".$sign."')";

$db->query($sql);

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

date_default_timezone_set('PRC');
$ordDate = time();

if($result == '00') { // 支付成功
	require dirname(__FILE__).'/UcPayAccessAPI.php';
	$myvcode = HanderReq::signGenerate($_POST, PAYKEY);
	if(strtolower($sign) == strtolower($myvcode)) { // 验证交易签名
		$result = $db->query ( "SELECT * FROM " . $common->tname ( 'uc_order' ) . "  WHERE order_id = '" . $order_id . "' LIMIT 1" );
		$rows = $db->fetch_array ( $result );
		// 检查是否已验证
		if ($rows ['status'] == 0) {
			// 支付金额是否与订单金额一致
			if(intval($rows['card_amt'])*10 == $pay_amt) {
				// 获取玩家现有元宝数量
				$result = $db->query ( "SELECT * FROM " . $common->tname ( 'player' ) . "  WHERE ucid = '" . $user_id . "' LIMIT 1" );
				$rows = $db->fetch_array ( $result );
					
				// 根据支付金额为玩家增加元宝
				$addIngot = intval($pay_amt);
				
				$updateRole['ingot'] = intval($rows['ingot']) + $addIngot;
				$whereRole['playersid'] = $rows['playersid'];
				$common->updatetable('player', $updateRole, $whereRole);
				$common->updateMemCache(MC.$rows['playersid'], $updateRole);
					
				// 设置为已验证处理
				$updateOrd['status'] = 1;
				$updateOrd['yb'] = $updateRole['ingot'];
				$updateOrd['level'] = $rows['player_level'];
				$whereOrd['trade_id'] = $trade_id;
				$common->updatetable('uc_fill', $updateOrd, $whereOrd);
				
				$updateNotify['status'] = 1;			
				$whereNotify['order_id'] = $order_id;
				$common->updatetable('uc_order', $updateNotify, $whereNotify);
		
				// vip
				$pay_amt = $pay_amt/10;
				vipChongzhi($rows['playersid'], $pay_amt, $addIngot, $order_id);
				
				writelog('app.php?task=chongzhi&type=ucly_card_yd&option=pay',json_encode(array('orderId'=>$order_id,'ucid'=>$user_id,'payWay'=>'U点充值卡支付','amount'=>$pay_amt,'orderStatus'=>'S','failedDesc'=>'','createTime'=>$ordDate,'status'=>0,'oldYB'=>$rows['ingot'], 'newYB'=>$updateRole['ingot'])), $rows['player_level'], $rows['userid']);
			}			
		}
	}
} else { // 支付失败
	$result = $db->query ( "SELECT * FROM " . $common->tname ( 'player' ) . "  WHERE ucid = '" . $user_id . "' LIMIT 1" );
	$rows = $db->fetch_array ( $result );
	writelog('app.php?task=chongzhi&type=ucly_card_yd&option=pay',json_encode(array('orderId'=>$order_id,'ucid'=>$user_id,'payWay'=>'U点充值卡支付','amount'=>$pay_amt,'orderStatus'=>'F','failedDesc'=>$error_code,'createTime'=>$ordDate,'status'=>0,'oldYB'=>0, 'newYB'=>0)), $rows['player_level'], $rows['userid']);
}
echo 'success';