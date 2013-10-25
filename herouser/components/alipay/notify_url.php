<?php
@define('IN_HERO', TRUE);
define('D_BUG', '0');
$_REQUEST['client'] = 0;
define('S_ROOT', dirname(dirname(dirname(__FILE__))) . '/');
include(dirname(dirname(dirname(__FILE__))) . '/includes/class_mysql.php');
include(dirname(dirname(dirname(__FILE__))) . '/includes/class_common.php');	
include(dirname(dirname(dirname(__FILE__))) . '/config.php');
require(dirname(dirname(dirname(__FILE__))) .'/configs/ConfigLoader.php');
require(dirname(dirname(dirname(__FILE__))) .'/model/PlayerMgr.php');
require(dirname(dirname(dirname(__FILE__))) .'/includes/class_memcacheAdapter.php');

ini_set('memcached.compression_type', 'fastlz');
ini_set('memcached.compression_factor', 1.3);
ini_set('memcached.compression_threshold', 8000);
ini_set('memcached.serializer', 'igbinary');

ini_set('session.cookie_domain', '127.0.0.1');
ini_set('session.save_handler', 'memcached');
ini_set('memcached.sess_binary', 'On');
ini_set('session.save_path', 'PERSISTENT=ms1 127.0.0.1:11211'); 

$common = new heroCommon;	
$db = new dbstuff;
$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname'],'utf8');
$db->query("set names 'utf8'");

$mc = new MemcacheAdapter_Memcached;	
$mc->addServer($MemcacheList[0], $Memport);

$G_PlayerMgr = new PlayerMgr($db,$mc);

include(dirname(dirname(dirname(__FILE__))) . '/includes/vip_control.php');

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

require_once("class/alipay_notify.php");
if($_SC['domain'] == '10.144.132.230') {
	require_once("alipay_config2.php");
} else {
	require_once("alipay_config.php");
}

//构造通知函数信息
$alipay = new alipay_notify($partner,$sec_id,$_input_charset);

//计算得出通知验证结果
$verify_result = $alipay->notify_verify();

//判断验签是否成功
if($verify_result) {
	
	//解密notify_data数据，并获得该xml节点的状态
	$notify_data = decrypt($_POST['notify_data']);
	$status = getDataForXML($notify_data, '/notify/trade_status');
	//log_result('decrypted notfiy data:' .$notify_data);

	//判断交易是否完成
	if($status == 'TRADE_FINISHED' || $status == 'TRADE_SUCCESS') {

		//在判断交易完成后，必须在页面输出success
		echo "success";
		
        //记录日志
		//log_result('notify success');

		/**********************************这里配置商户的业务逻辑*************************************/
		$out_trade_no = getDataForXML($notify_data, '/notify/out_trade_no');
		$total_fee = getDataForXML($notify_data, '/notify/total_fee');
		$trade_no = getDataForXML($notify_data, '/notify/trade_no');
		$buyer_email = getDataForXML($notify_data, '/notify/buyer_email');
		$gmt_create = getDataForXML($notify_data, '/notify/gmt_create');
		$notify_time = getDataForXML($notify_data, '/notify/notify_time');
		$seller_id = getDataForXML($notify_data, '/notify/seller_id');
		$total_fee = getDataForXML($notify_data, '/notify/total_fee');
		$gmt_payment = getDataForXML($notify_data, '/notify/gmt_payment');
		$notify_id = getDataForXML($notify_data, '/notify/notify_id');
		$use_coupon = getDataForXML($notify_data, '/notify/use_coupon');

		$result = $db->query ( "SELECT * FROM " . $common->tname ( 'alipay_ord' ) . "  WHERE ord_no = '" . $out_trade_no . "' LIMIT 1" );
		$rows = $db->fetch_array ( $result );			

		if ($rows ['verify'] == 0) { // 检查是否已验证
			// 支付金额是否与订单金额一致
			if($rows['ord_amt'] == $total_fee) {
				// 设置为已验证处理
				$alipay_update['trade_no'] = $trade_no;
				$alipay_update['buyer_email'] = $buyer_email;
				$alipay_update['gmt_create'] = $gmt_create;
				$alipay_update['notify_time'] = strtotime($notify_time);
				$alipay_update['seller_id'] = $seller_id;
				$alipay_update['total_fee'] = $total_fee;
				$alipay_update['gmt_payment'] = $gmt_payment;
				$alipay_update['notify_id'] = $notify_id;
				$alipay_update['use_coupon'] = $use_coupon;
				$alipay_update['verify'] = 1;
				$alipay_where['ord_no'] = $out_trade_no;
				$common->updatetable('alipay_ord', $alipay_update, $alipay_where);

				// 获取玩家现有元宝数量
				$result = $db->query ( "SELECT * FROM " . $common->tname ( 'player' ) . "  WHERE playersid = '" . $rows['playersid'] . "' LIMIT 1" );
				$rowsp = $db->fetch_array ( $result );
				
				// 根据支付金额为玩家增加元宝
				$addIngot = intval($total_fee)*10;
				$updateRole['ingot'] = intval($rowsp['ingot']) + $addIngot;			
				$whereRole['playersid'] = $rows['playersid'];
				$common->updatetable('player', $updateRole, $whereRole); 
				$common->updateMemCache(MC.$rows['playersid'], $updateRole);
												
				// vip
				vipChongzhi($rows['playersid'], intval($total_fee), $addIngot, (string)$out_trade_no);		
				
				$log_date  =  date('Ymd');
				$log_date_hour = date('H');
				$ord_date = $out_trade_no;
				$ord_date = substr(str_replace('jofgame_qjsh_', '', $ord_date), 0, 14);
				$ord_date = strtotime($ord_date);
				$recharge_type = 1;
				if((time() - $ord_date) > 1200) $recharge_type = 2;
				$log_content = time() . '|' . $ord_date . '|fgc|10000016|786' . '|' . $rowsp['ucid'] . '|'. $recharge_type . '|' . $rows['id'] . '|0|success|' . $total_fee * 100 . '|1|1|' . $total_fee * 100 . '|yuanbao';
				$log_content = sprintf("%s \r\n", $log_content);
				
				//$log_fileName = "/usr/local/app/logs/786/fgc_10000016_{$log_date}_10.144.133.54_0.log";
				$log_fileName = str_replace('{log_date}', $log_date, $_SC['yp_log']);
				file_put_contents($log_fileName, $log_content, FILE_APPEND);
		
				writelog('app.php?task=chongzhi&type=sy_alipay&option=pay',json_encode(array('orderId'=>$out_trade_no,'ucid'=>$rowsp['ucid'],'payWay'=>$rows['cashier_code'],'amount'=>$total_fee,'orderStatus'=>'S','failedDesc'=>'','createTime'=>$ord_date ,'status'=>0, 'oldYB'=>$rowsp['ingot'], 'newYB'=>$updateRole['ingot'])), $rowsp['player_level'], $rowsp['userid']);
			}
		}
    } else {

		//交易未完成
		echo "fail";

        //记录日志
       //log_result('notify fail' );
    }
}
else {
    //验签失败，输出fail，支付宝会24小时根据策略重发总共7次
    echo "fail";
	//$notify_data = decrypt($_POST['notify_data']);
	//log_result('decrypted notfiy data:' .$notify_data);
    //记录日志
    //log_result('notify 验签失败');
}
?>