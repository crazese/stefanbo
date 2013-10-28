<?php
define('SZF_PRIVATEKEY',"123456");
$_REQUEST['client'] = 0;
define('S_ROOT', dirname(dirname(dirname(__FILE__))) . '/');
include(dirname(dirname(dirname(__FILE__))) . '/includes/class_mysql.php');
include(dirname(dirname(dirname(__FILE__))) . '/includes/class_common.php');	
include(dirname(dirname(dirname(__FILE__))) . '/config.php');
require(dirname(dirname(dirname(__FILE__))) .'/configs/ConfigLoader.php');
require(dirname(dirname(dirname(__FILE__))) .'/model/PlayerMgr.php');
require(dirname(dirname(dirname(__FILE__))) .'/includes/class_memcacheAdapter.php');
require(dirname(dirname(dirname(__FILE__))) .'/components/hero_letters/var.php');
require(dirname(dirname(dirname(__FILE__))) .'/components/hero_letters/model.php');
require(dirname(dirname(dirname(__FILE__))) .'/components/hero_letters/controller.php');
require(dirname(dirname(dirname(__FILE__))) .'/components/hero_role/model.php');

include(dirname(__FILE__) . '/YeePayCommon.php');

$common = new heroCommon;	
$db = new dbstuff;
$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname'],'utf8');
$db->query("set names 'utf8'");

$mc = new MemcacheAdapter_Memcached;	
$mc->addServer($MemcacheList[0], $Memport);

$G_PlayerMgr = new PlayerMgr($db,$mc);
		
//$common->insertLog(json_encode($_REQUEST));
#	只有支付成功时易宝支付才会通知商户.
##支付成功回调有两次，都会通知到在线支付请求参数中的p8_Url上：浏览器重定向;服务器点对点通讯.
include(dirname(__FILE__).'/ShenZhouCommon.php');

#	解析返回参数.
$return = getszfCallBackValue($version,$merId,$payMoney,$orderId,$payResult,$privateField,$md5String,$errcode);

#	判断返回签名是否正确（True/False）
$payDetails="";
$bRet = CheckszfHmac($version,$merId,$payMoney,$orderId,$payResult,$privateField,$payDetails,$md5String);
#	以上代码和变量不需要修改.

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
$curr_month = date('m');

#	校验码正确.
if($bRet){
	echo $orderId;
	if( $payResult ) {
		$sql = "SELECT * FROM ol_shenzhoupay_record WHERE orderId = '" . $orderId . "' LIMIT 1;";
		$result = $db->query( $sql );
		$rows = $db->fetch_array( $result );

		// 检查是否已验证
		if ($rows ['verify'] == 1) {
			echo $orderId;
			return;
		}
		else if ($rows ['verify'] == 0) {
			$tmp_flag = explode('|', $privateField);
			//$p9_MP = $tmp_flag[0];
			$sypt = $tmp_flag[1];
			
			// 支付金额是否与订单金额一致
			if( $payMoney == $rows['payMoney'] ) {
				if(isset($tmp_flag[2])) {
					$from_pid = $tmp_flag[2];
					$p9_MP = $tmp_flag[0];
					// 对双方发探报
					$toplayer = $G_PlayerMgr->GetPlayer($p9_MP);
					$topalyer_info = $toplayer->baseinfo_;
					$player = $G_PlayerMgr->GetPlayer($from_pid);
					$player_info = $player->baseinfo_;
					$give_ingot = intval($payMoney/100)*10;
					
					$other_recharge = array();
					$other_recharge['playersid'] = $from_pid;
					$other_recharge['toplayersid'] = $from_pid;
					$other_recharge['message'] = array('xjnr'=>"您已经为您的好友 {$topalyer_info['nickname']}（ID：{$p9_MP}）充值{$give_ingot}元宝，请稍后在充值记录界面查看充值结果");
					$other_recharge['genre'] = 32;
					$other_recharge['request'] ='';
					$other_recharge['type'] = 1;
					$other_recharge['uc'] = '0';
					$other_recharge['is_passive'] = 0;
					$other_recharge['interaction'] = 0;
					$other_recharge['tradeid'] = 0;							
					lettersModel::addMessage($other_recharge);
					
					$other_recharge = array();
					$other_recharge['playersid'] = $from_pid;
					$other_recharge['toplayersid'] = $p9_MP;
					$other_recharge['message'] = array('xjnr'=>"您的好友 {$player_info['nickname']}（ID：{$from_pid}）给您充了{$give_ingot}元宝，请注意核对！");
					$other_recharge['genre'] = 32;
					$other_recharge['request'] ='';
					$other_recharge['type'] = 1;
					$other_recharge['uc'] = '0';
					$other_recharge['is_passive'] = 0;
					$other_recharge['interaction'] = 0;
					$other_recharge['tradeid'] = 0;							
					lettersModel::addMessage($other_recharge);
				}
				// 获取玩家现有元宝数量
				$result = $db->query ( "SELECT * FROM " . $common->tname ( 'player' ) . "  WHERE playersid = '" . $privateField . "' LIMIT 1" );
				$rowsp = $db->fetch_array ( $result );
					
				// 根据支付金额为玩家增加元宝
				$addIngot = intval($payMoney/100*10);
				$updateRole['ingot'] = intval($rowsp['ingot']) + $addIngot;
				$whereRole['playersid'] = $privateField;
				$common->updatetable('player', $updateRole, $whereRole);
				$common->updateMemCache(MC.$privateField, $updateRole);
				
				// 设置为已验证处理
				$db->query("update ol_shenzhoupay_record set verify = '1', errcode = '". $errcode ."',
						ordDate = '". time() ."' where orderId = '" . $orderId . "';");
									
				// vip
				vipChongzhi($privateField, $payMoney/100, $addIngot, $orderId);
				
				if($sypt == 1) {
					$log_date  =  date('Ymd');
					$log_date_hour = date('H');
					$ord_date = $rows['orderId'];
					$ord_date = substr(str_replace('jofgame_szf_', '', $ord_date), 0, 14);
					$ord_date = strtotime($ord_date);
					$recharge_type = 1;
					if((time() - $ord_date) > 1200) $recharge_type = 2;
					$log_content = time() . '|' . $ord_date . '|fgc|10000016|786' . '|' . $rowsp['ucid'] . '|'. $recharge_type . '|' . $rows['id'] . '|0|success|' . $p3_Amt * 100 . '|1|1|' . $p3_Amt * 100 . '|yuanbao';
					$log_content = sprintf("%s \r\n", $log_content);
					
					//$log_fileName = "/usr/local/app/logs/786/fgc_10000016_{$log_date}_10.144.133.54_0.log";
					$log_fileName = str_replace('{log_date}', $log_date, $_SC['yp_log']);
					//$log_fileName = "/tmp/fgc_10000016_{$log_date}_10.144.133.54_0.log";
					file_put_contents($log_fileName, $log_content, FILE_APPEND);
				}
				
				writelog('app.php?task=chongzhi&type=sy&option=pay',json_encode(array('orderId'=>$orderId,'ucid'=>$rowsp['ucid'],'payWay'=>'szf','amount'=>$payMoney/100,'orderStatus'=>'S','failedDesc'=>'','createTime'=>time(),'status'=>0, 'oldYB'=>$rowsp['ingot'], 'newYB'=>$updateRole['ingot'])), $rowsp['player_level'], $rowsp['userid']);
			}
			else {
				$db->query("update ol_shenzhoupay_record set verify = '2', errcode = '1001', ordDate = '". 
						time() ."' where orderId = '" . $orderId . "';");
			}
		}
	}
	else {
		$result = $db->query ( "SELECT ingot FROM " . $common->tname ( 'player' ) . "  WHERE playersid = '" . $privateField . "' LIMIT 1" );
		$rowsp = $db->fetch_array ( $result );
		$db->query("update ol_shenzhoupay_record set verify = '2', errcode = '". $errcode ."', ordDate = 
				'". time() ."' where orderId = '" . $orderId . "';");
		writelog('app.php?task=chongzhi&type=sy&option=pay', json_encode(array('orderId'=>$orderId,'ucid'=>$rowsp['ucid'],'payWay'=>'szf','amount'=>$payMoney/100,'orderStatus'=>'F','failedDesc'=>'支付失败','createTime'=>time(),'status'=>0,'oldYB'=>0, 'newYB'=>0)), $rowsp['player_level'], $rowsp['userid']);
	}
}else{
	$db->query("update ol_shenzhoupay_record set verify = '2', errcode = '3', ordDate = '". time() ."' 
			where orderId = '" . $orderId . "';");

	header("HTTP/1.0 404 Not Found");
}
   
?>
