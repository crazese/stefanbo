<?php
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
		
	$r0_Cmd=$r1_Code=$p1_MerId=$p2_Order=$p3_Amt=$p4_FrpId=$p5_CardNo=$p6_confirmAmount=$p7_realAmount=$p8_cardStatus=$p9_MP=$pb_BalanceAmt=$pc_BalanceAct=$hmac=null;
	#	解析返回参数.
	$return = getCallBackValue($r0_Cmd,$r1_Code,$p1_MerId,$p2_Order,$p3_Amt,$p4_FrpId,$p5_CardNo,$p6_confirmAmount,$p7_realAmount,$p8_cardStatus,$p9_MP,$pb_BalanceAmt,$pc_BalanceAct,$hmac);

	#	判断返回签名是否正确（True/False）
	$bRet = CheckHmac($r0_Cmd,$r1_Code,$p1_MerId,$p2_Order,$p3_Amt,$p4_FrpId,$p5_CardNo,$p6_confirmAmount,$p7_realAmount,$p8_cardStatus,$p9_MP,$pb_BalanceAmt,$pc_BalanceAct,$hmac);
	#	以上代码和变量不需要修改.
		 	
	#	校验码正确.
	$ret_str = "$r0_Cmd,$r1_Code,$p1_MerId,$p2_Order,$p3_Amt,$p4_FrpId,$p5_CardNo,$p6_confirmAmount,$p7_realAmount,$p8_cardStatus,$p9_MP,$pb_BalanceAmt,$pc_BalanceAct,$hmac";
	$ret_str = sprintf("%s\r\n", $ret_str);
	
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
			
	if($bRet){
		echo "success";
		
		// 分割playersid和闪游平台标志
		$tmp_flag = explode('|', $p9_MP);
		$p9_MP = $tmp_flag[0];
		$sypt = $tmp_flag[1];
		
		#在接收到支付结果通知后，判断是否进行过业务逻辑处理，不要重复进行业务逻辑处理
		if ($r1_Code == "1") { // 成功
			$result = $db->query ( "SELECT * FROM " . $common->tname ( 'yeepay_ord' ) . "  WHERE orderNo = '" . $p2_Order . "' LIMIT 1" );
			$rows = $db->fetch_array ( $result );
			
			if(mysql_num_rows($result) == 0) { // 订单丢失补单
				$addOrdDate = time();
				$db->query("insert into " . $common->tname ( 'yeepay_ord' ) . "(playersid, type, memberId,orderNo,orderAmt,currency,descr, extInfo,cardAmt,cardNo,cardPwd,frpId,hmac,verify,errcode,trxId,ordDate) values({$p9_MP},  'ChargeCardDirect', '10011285454', '{$p2_Order}',{$p6_confirmAmount}, '', 'YuanBao', '{$p9_MP}|{$sypt}',  {$p6_confirmAmount}, '$p5_CardNo','', '{$p4_FrpId}', '{$hmac}', 0, '',  '',   '{$addOrdDate}')");
				
				$result = $db->query ( "SELECT * FROM " . $common->tname ( 'yeepay_ord' ) . "  WHERE orderNo = '" . $p2_Order . "' LIMIT 1" );
				$rows = $db->fetch_array ( $result );
			}
			
			// 检查是否已验证
			if ($rows ['verify'] == 0) {
				if(isset($tmp_flag[2])) {
					$from_pid = $tmp_flag[2];
					// 对双方发探报
					$toplayer = $G_PlayerMgr->GetPlayer($p9_MP);
					$topalyer_info = $toplayer->baseinfo_;
					$player = $G_PlayerMgr->GetPlayer($from_pid);
					$player_info = $player->baseinfo_;
					$give_ingot = intval($p3_Amt)*10;
					
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
				// 支付金额是否与订单金额一致
				//if($rows['orderAmt'] == intval($p3_Amt)) {
				//if($rows['orderAmt'] == $p3_Amt) {
					// 获取玩家现有元宝数量
					$result = $db->query ( "SELECT * FROM " . $common->tname ( 'player' ) . "  WHERE playersid = '" . $p9_MP . "' LIMIT 1" );
					$rowsp = $db->fetch_array ( $result );
					
					// 根据支付金额为玩家增加元宝
					if($sypt == 1) {
						$curr = time();
						$endDate = strtotime("+7 day", KFSJ);
						if( $curr <= $endDate ) {
							$addIngot = intval($p3_Amt*12);
						} else {
							$addIngot = intval($p3_Amt*10);
						}
					} else {
						$addIngot = intval($p3_Amt)*10;
					}
					$updateRole['ingot'] = intval($rowsp['ingot']) + $addIngot;			
					$whereRole['playersid'] = $p9_MP;
					$common->updatetable('player', $updateRole, $whereRole); 
					$common->updateMemCache(MC.$p9_MP, $updateRole);
					
					// 设置为已验证处理
					$updateOrd['verify'] = 1;
					$updateOrd['ordDate'] = time();
					$updateOrd['orderAmt'] = $p3_Amt;
					$whereOrd['orderNo'] = $p2_Order;
					$common->updatetable('yeepay_ord', $updateOrd, $whereOrd);
										
					// vip
					vipChongzhi($p9_MP, $p3_Amt, $addIngot, $p2_Order);		
					
					if($sypt == 1) {
						$log_date  =  date('Ymd');
						$log_date_hour = date('H');
						$ord_date = $rows['orderNo'];
						$ord_date = substr(str_replace('jofgame_qjsh_', '', $ord_date), 0, 14);
						$ord_date = strtotime($ord_date);
						$recharge_type = 1;
						if((time() - $ord_date) > 1200) $recharge_type = 2;
						$log_content = time() . '|' . $ord_date . '|fgc|10000016|786' . '|' . $rowsp['ucid'] . '|'. $recharge_type . '|' . $rows['id'] . '|0|success|' . $p3_Amt * 100 . '|1|1|' . $p3_Amt * 100 . '|yuanbao';
						$log_content = sprintf("%s \r\n", $log_content);
						
						//$log_fileName = "/usr/local/app/logs/786/fgc_10000016_{$log_date}_10.144.133.54_0.log";
						$log_fileName = str_replace('{log_date}', $log_date, $_SC['yp_log']);
						file_put_contents($log_fileName, $log_content, FILE_APPEND);
					}
				//}
				writelog('app.php?task=chongzhi&type=sy&option=pay',json_encode(array('orderId'=>$p2_Order,'ucid'=>$rowsp['ucid'],'payWay'=>$p4_FrpId,'amount'=>$p3_Amt,'orderStatus'=>'S','failedDesc'=>'','createTime'=>$updateOrd['ordDate'],'status'=>0, 'oldYB'=>$rowsp['ingot'], 'newYB'=>$updateRole['ingot'])), $rowsp['player_level'], $rowsp['userid']);
				
			}
		} else { // 失败
				$result = $db->query ( "SELECT * FROM " . $common->tname ( 'yeepay_ord' ) . "  WHERE orderNo = '" . $p2_Order . "' LIMIT 1" );
				$rows = $db->fetch_array ( $result );
					
				if(mysql_num_rows($result) == 0) { // 订单丢失补单
					$addOrdDate = time();
					$db->query("insert into " . $common->tname ( 'yeepay_ord' ) . "(playersid, type, memberId,orderNo,orderAmt,currency,descr, extInfo,cardAmt,cardNo,cardPwd,frpId,hmac,verify,errcode,trxId,ordDate) values({$p9_MP},  'ChargeCardDirect', '10011285454', '{$p2_Order}',{$p6_confirmAmount}, '', 'YuanBao', '{$p9_MP}|{$sypt}',  {$p6_confirmAmount}, '$p5_CardNo','', '{$p4_FrpId}', '{$hmac}', 0, '',  '',   '{$addOrdDate}')");
					
					$result = $db->query ( "SELECT * FROM " . $common->tname ( 'yeepay_ord' ) . "  WHERE orderNo = '" . $p2_Order . "' LIMIT 1" );
					$rows = $db->fetch_array ( $result );
				}
				
				$updateOrd['verify'] = 2;
				$updateOrd['errcode'] = $p8_cardStatus;
				$updateOrd['ordDate'] = time();
				$whereOrd['orderNo'] = $p2_Order;
				$common->updatetable('yeepay_ord', $updateOrd, $whereOrd);				
				
				$result = $db->query ( "SELECT * FROM " . $common->tname ( 'player' ) . "  WHERE playersid = '" . $p9_MP . "' LIMIT 1" );
				$rowsp = $db->fetch_array ( $result );
				
				if($sypt == 1) {
					$log_date  =  date('Ymd');
					$log_date_hour = date('H');
					
					$ord_date = $rows['orderNo'];
					$ord_date = substr(str_replace('jofgame_qjsh_', '', $ord_date), 0, 14);
					$ord_date = strtotime($ord_date);
					$recharge_type = 1;
					if((time() - $ord_date) > 1200) $recharge_type = 2;
					$log_content = time() . '|' . $ord_date . '|fgc|10000016|786' . '|' . $rowsp['ucid'] . '|' . $recharge_type . '|' . $rows['id'] . '|2|failure|' . $p3_Amt * 100 . '|1|1|' . $p3_Amt * 100 . '|yuanbao';
					$log_content = sprintf("%s \r\n", $log_content);
					
					//$log_fileName = "/usr/local/app/logs/786/fgc_10000016_{$log_date}_10.144.133.54_0.log";
					$log_fileName = str_replace('{log_date}', $log_date, $_SC['yp_log']);
					file_put_contents($log_fileName, $log_content, FILE_APPEND);
				}
				
				writelog('app.php?task=chongzhi&type=sy&option=pay', json_encode(array('orderId'=>$p2_Order,'ucid'=>$rowsp['ucid'],'payWay'=>$p4_FrpId,'amount'=>$p3_Amt,'orderStatus'=>'F','failedDesc'=>$p8_cardStatus,'createTime'=>$updateOrd['ordDate'],'status'=>0,'oldYB'=>0, 'newYB'=>0)), $rowsp['player_level'], $rowsp['userid']);
		}
	} else {
		// 交易签名无效
		$updateOrd['verify'] = 2;
		$updateOrd['errcode'] = 3;
		$updateOrd['ordDate'] = time();
		$whereOrd['orderNo'] = $p2_Order;
		$common->updatetable('yeepay_ord', $updateOrd, $whereOrd);
		header("HTTP/1.0 404 Not Found");
	}
?> 