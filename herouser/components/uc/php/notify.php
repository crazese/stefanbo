<?php
@define('IN_HERO', TRUE);
define('D_BUG', '0');
$_REQUEST['client'] = 0;
define('S_ROOT', dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR);
include(dirname(dirname(dirname(dirname(__FILE__)))) . '/includes/class_mysql.php');
include(dirname(dirname(dirname(dirname(__FILE__)))) . '/includes/class_common.php');	
include(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require(dirname(dirname(dirname(dirname(__FILE__)))) .'/configs/ConfigLoader.php');
require(dirname(dirname(dirname(dirname(__FILE__)))) .'/model/PlayerMgr.php');
require(dirname(dirname(dirname(dirname(__FILE__)))) .'/includes/class_memcacheAdapter.php');
require dirname(__FILE__).'/ucpro.php';

$mc = new MemcacheAdapter_Memcached;	
$mc->addServer($MemcacheList[0], $Memport);

$common = new heroCommon;
$db = new dbstuff;
$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname'],'utf8');
$db->query("set names 'utf8'");

include(dirname(dirname(dirname(dirname(__FILE__)))) . '/includes/vip_control.php');

$amount=isset($_POST['amount']) ? $_POST['amount'] : '';
$result=isset($_POST['result']) ? $_POST['result'] : '';
$ucid=isset($_POST['ucid']) ? $_POST['ucid'] : ''; //正式
$ucid = '2_' . $ucid;
//$ucid = '2_55604910'; //测试
$token=isset($_POST['token']) ? $_POST['token'] : '';
$trade_id=isset($_POST['trade_id']) ? $_POST['trade_id'] : '';
$add_info=isset($_POST['add_info']) ? $_POST['add_info'] : '';
$prod_nbr=isset($_POST['prod_nbr']) ? $_POST['prod_nbr'] : '';
$trade_time=isset($_POST['trade_time']) ? $_POST['trade_time'] : '';
$order_id=isset($_POST['order_id']) ? $_POST['order_id'] : '';
$prod_name=isset($_POST['prod_name']) ? $_POST['prod_name'] : '';
$msg = isset($_POST['msg']) ? $_POST['msg'] : '';
$sign=isset($_POST['sign']) ? $_POST['sign'] : '';

$_POST['trade_time'] = urldecode($_POST['trade_time']);
$_POST['prod_name'] = urldecode($_POST['prod_name']);

$sql = "insert into ol_uc_coin (amount,result,errmsg,ucid,token,trade_id,add_info,prod_nbr,trade_time,prder_id,prod_name,sign,type) values ('".$amount."','".$result."','".$msg."','".$ucid."','".$token."','".
$trade_id."','".$add_info."','".$prod_nbr."','".$trade_time."','" . $order_id . "','".$prod_name."','".$sign."','1')";/*hk*///UC U点支付，回调时记录订单号

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
		$result = $db->query ( "SELECT * FROM " . $common->tname ( 'uc_order_notify' ) . "  WHERE order_id = '" . $order_id . "' LIMIT 1" );
		$rows = $db->fetch_array ( $result );
		// 检查是否已验证
		if ($rows ['status'] == 0) {
			// 支付金额是否与订单金额一致
			if($rows['amount'] == $amount) {
				// 获取玩家现有元宝数量
				$result = $db->query ( "SELECT * FROM " . $common->tname ( 'player' ) . "  WHERE ucid = '" . $ucid . "' LIMIT 1" );
				$rows = $db->fetch_array ( $result );
					
				// 根据支付金额为玩家增加元宝
				$addIngot = intval($amount);
				
				$updateRole['ingot'] = intval($rows['ingot']) + $addIngot;
				$whereRole['playersid'] = $rows['playersid'];
				$common->updatetable('player', $updateRole, $whereRole);
				$common->updateMemCache(MC.$rows['playersid'], $updateRole);
					
				// 设置为已验证处理
				$updateOrd['status'] = 1;
				$updateOrd['yb'] = $updateRole['ingot'];
				$updateOrd['level'] = $rows['player_level'];
				$whereOrd['trade_id'] = $trade_id;
				$common->updatetable('uc_coin', $updateOrd, $whereOrd);
				
				$updateNotify['status'] = 1;			
				$whereNotify['order_id'] = $order_id;
				$common->updatetable('uc_order_notify', $updateNotify, $whereNotify);
		
				// vip
				$real_amt = $amount/10;
				vipChongzhi($rows['playersid'], $real_amt, $addIngot, $order_id);
				
			}
			writelog('app.php?task=chongzhi&type=ucly&option=pay',json_encode(array('orderId'=>$order_id,'ucid'=>$ucid,'payWay'=>'U点','amount'=>$real_amt,'orderStatus'=>'S','failedDesc'=>'','createTime'=>$ordDate,'status'=>0,'oldYB'=>$rows['ingot'], 'newYB'=>$updateRole['ingot'])), $rows['player_level'], $rows['userid']);
		}
	}
} else { // 支付失败
	$result = $db->query ( "SELECT * FROM " . $common->tname ( 'player' ) . "  WHERE ucid = '" . $ucid . "' LIMIT 1" );
	$rows = $db->fetch_array ( $result );
/*hk*/	$updateNotify['status'] = 2;
	$whereNotify['order_id'] = $order_id;
	$common->updatetable('uc_order_notify', $updateNotify, $whereNotify);/*hk*///支付失败时，修改订单状态为2
	writelog('app.php?task=chongzhi&type=ucly&option=pay',json_encode(array('orderId'=>$order_id,'ucid'=>$ucid,'payWay'=>'U点','amount'=>$amount,'orderStatus'=>'F','failedDesc'=>$msg,'createTime'=>$ordDate,'status'=>0,'oldYB'=>0, 'newYB'=>0)), $rows['player_level'], $rows['userid']);
}
echo 'success';