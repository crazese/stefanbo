<?php

class hdModel{
	/**
	 * 获取指定玩家的活动列表
	 *
	 * @param int $playersid     玩家id
	 */
	public static function hqhdlb($playersid){
		global $_SGLOBAL,$_g_lang;
		
		if(empty($playersid)){
			// 玩家信息错误
			return array('status'=>23, 'message'=>$_g_lang['hd']['p_inf_err']);
		}
		
		// 最终完成活动类活动的检查
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
		$myHdList = hdProcess::run(array('role_endLevel'), $roleInfo, null, false);
		
		$hdList   = hdProcess::getActList();
		
		// 格式化输出活动
		$hdOutList = array();
		foreach($myHdList as $myAct){
			// 已封禁的活动不显示
			if(isset($hdList[$myAct['hdid']])&&0 == $hdList[$myAct['hdid']]['pub']){
				continue;
			}

			// 所有状态已完成同时全部领取的活动不显示
			if($myAct['wczt'] >= $myAct['wccs']){
				continue;
			}
		
			// 已经有阶段完成并且未最终完成
			$getAble = 0;
			if(0 <= $myAct['jljd'] || $myAct['cptTimes'] > $myAct['wczt']){
				$getAble = 1;
			}
			// 如果是结束时领奖则检查当前可领奖阶段是否完成 jslj
			$secEndTime = isset($hdList[$myAct['hdid']]['secTime'][$myAct['wczt']])?$hdList[$myAct['hdid']]['secTime'][$myAct['wczt']]:0;
			if(1 == $myAct['jslj'] && $secEndTime>$_SGLOBAL['timestamp']){
				$getAble = 0;
			}
			
			$hdOutList[] = array('js'=>$myAct['title'],
								 'lq'=>$getAble,
								 'kssj'=>$myAct['startTime'],
								 'jssj'=>$myAct['endTime'],
								 'dx'=>$myAct['hddx'],
								 'nr'=>$myAct['desc'],
								 'id'=>$myAct['ID'],
								 'type'=>1);
		}
		
		$exInfo = exchangeEx::getExchangeInfo();
		foreach($exInfo as $usrEx){
			$hdOutList[] = array('js'=>$usrEx['title'],
								 'lq'=>1,
								 'kssj'=>$usrEx['startTime'],
								 'jssj'=>$usrEx['endTime'],
								 'dx'=>$usrEx['exObj'],
								 'nr'=>$usrEx['desc'],
								 'id'=>$usrEx['exid'],
								 'type'=>2);
		}
		
		
		$returnValue['status'] = 0;
		$returnValue['hdlist'] = $hdOutList;
		
		return $returnValue;
	}
	
	/**
	 * 领取活动奖励
	 *
	 * @param int $hdid
	 * @param int $playersid
	 * @return array
	 */
	public static function lqhdjl($hdid, $playersid){
		global $common, $mc, $_SGLOBAL, $G_PlayerMgr, $_g_lang;
		if(empty($playersid)){
			// 玩家信息错误
			return array('status'=>23, 'message'=>$_g_lang['hd']['p_inf_err']);
		}
		
		$myHdList = hdProcess::getMyActList($playersid);
		
		if(!array_key_exists($hdid, $myHdList)){
			// 活动信息错误
			return array('status'=>24, 'message'=>$_g_lang['hd']['a_inf_err']);
		}
		
		if(!(0 <= $myHdList[$hdid]['jljd'] || $myHdList[$hdid]['cptTimes'] > $myHdList[$hdid]['wczt'])){
			// 该活动没有可领取的奖品
			return array('status'=>26, 'message'=>$_g_lang['hd']['no_good']);
		}
		
		$hdList   = hdProcess::getActList();
		//  如果是结束时领奖则检查当前阶段是否完成 jslj
		$sltActInfo = $myHdList[$hdid];
		$secEndTime = isset($hdList[$sltActInfo['hdid']]['secTime'][$sltActInfo['wczt']])?$hdList[$sltActInfo['hdid']]['secTime'][$sltActInfo['wczt']]:0;
		if(1 == $myHdList[$hdid]['jslj'] && $secEndTime > $_SGLOBAL['timestamp']){
			// 活动未到领奖时间
			return array('status'=>28, 'message'=>$_g_lang['hd']['time_limit']);
		}
		
		// 检测是否是封禁的活动
		if(isset($hdList[$myHdList[$hdid]['hdid']]) 
		&& 0 == $hdList[$myHdList[$hdid]['hdid']]['pub']){
			// 请求的是已封禁活动
			return array('status'=>25, 'message'=>$_g_lang['hd']['a_deny']);
		}
		
		// 获取玩家活动奖励内容，这里是先领大奖再领小奖
		$sltMyActInfo = $myHdList[$hdid];
		if($sltMyActInfo['wczt']<$sltMyActInfo['cptTimes']){
			$jpjl = explode(',', $sltMyActInfo['jpjl']);
			if(count($jpjl)>0){
				$giftId = array_shift($jpjl);
				$giftInfo = $sltMyActInfo['jpsj'][$giftId];
			}else{
				$giftInfo = $sltMyActInfo['jpsj'][count($sltMyActInfo['jpsj'])-1];
			}
			
			$sltMyActInfo['jpjl'] = implode(',', $jpjl);
		}
		else{
			
			$giftId   = $sltMyActInfo['jljd'];
			$giftInfo = $sltMyActInfo['jpsj'][$giftId];
		}
		
		$roleInfo['playersid'] = $playersid;
		$res = roleModel::getRoleInfo($roleInfo);
		if (empty($roleInfo)||empty($res)) {
			// 玩家数据有误！
			return array('status'=>27,'message'=>$_g_lang['hd']['p_inf_err']);
		}
		
		$updateArray = array();
		$returnValue = array();
		$addItemList = array();
		foreach($giftInfo as $gName=>$giftV){
			switch($gName){
				case 'tq':
					$updateArray['coins'] = $roleInfo['coins'] + $giftV;
					$roleInfo['coins']    = $updateArray['coins'];
					$returnValue['tq']    = $updateArray['coins'];
					$returnValue['hqtq']  = $giftV;

					$jpsm[]   = array('mc'=>'tq',
									  'iid'=>'tq',
									  'sl'=>$giftV);
				break;
				case 'yb':
					$updateArray['ingot'] = $roleInfo['ingot'] + $giftV;
					$roleInfo['ingot']    = $updateArray['ingot'];
					$returnValue['yb']    = $updateArray['ingot'];
					$returnValue['hqyb']  = $giftV;

					$jpsm[]   = array('mc'=>'yb',
									  'iid'=>'yb',
									  'sl'=>$giftV);
				break;
				case 'yp':
					$updateArray['silver'] = $roleInfo['silver'] + $giftV;
					$roleInfo['silver']    = $updateArray['silver'];
					$returnValue['yp']     = $updateArray['silver'];
					$returnValue['hqyp']   = $giftV;

					$jpsm[]   = array('mc'=>'yp',
									  'iid'=>'yp',
									  'sl'=>$giftV);
				break;
				case 'jl':
					$updateArray['food'] = $roleInfo['food'] + $giftV;
					$roleInfo['food']    = $updateArray['food'];
					$returnValue['jl']   = $updateArray['food'];
					$returnValue['hqjl'] = $giftV;
					
					$jpsm[]   = array('mc'=>'jl',
									  'iid'=>'jl',
									  'sl'=>$giftV);
				break;
				case 'sw':
					$updateArray['prestige'] = $roleInfo['prestige'] + $giftV;
					$roleInfo['prestige']    = $updateArray['prestige'];
					$returnValue['sw']       = $updateArray['prestige'];

					$jpsm[]   = array('mc'=>'sw',
									  'iid'=>'sw',
									  'sl'=>$giftV);
				break;
				default:
					$itemId = intval($gName);
					if(empty($itemId)){
						return array('status'=>29,'message'=>$_g_lang['hd']['good_err']);
					}
					$addItemList[$itemId] = $giftV;
				break;
			}
		}
		
		// 添加道具
		if(!empty($addItemList)){
			//$isOk = toolsModel::addItems($playersid, $addItemList, $djIdList, $oldDjList);
			$player = $G_PlayerMgr->GetPlayer($playersid);
			$isOk = $player->AddItems($addItemList);
			if(false === $isOk){
				// 背包已满
				return array('status'=>1001);
			}
			
			//$bagList = toolsModel::getBglist($djIdList, $playersid, $oldDjList);
			$bagList = $player->GetClientBag();
			$returnValue['list'] = $bagList;
			
			// 奖品说明数据
			$jpsm = array();
			foreach($addItemList as $itemId=>$num){
				$itemInfo = toolsModel::getItemInfo($itemId);
				$jpsm[]   = array('mc'=>$itemInfo['Name'],
								  'iid'=>$itemInfo['IconID'],
								  'sl'=>$num);
			}
			$returnValue['jllist'] = $jpsm;
		}
		
		// 修改玩家信息
		if(isset($returnValue['tq'])
		||isset($returnValue['yb'])
		||isset($returnValue['yp'])
		||isset($returnValue['jl'])
		||isset($returnValue['sw'])){
			$updateRoleWhere['playersid'] = $playersid;
			$common->updatetable('player', $updateArray, $updateRoleWhere);
			$mc->set(MC.$playersid, $roleInfo, 0, 3600);
		}
		
		/**
		 * 如果能领奖最终修改玩家活动状态
		 * 1.检查活动是否是最终完成  ++wczt == wccs
		 * 2.如果不是通过wczt来得到奖励则初始化jljd和procData为-1和array(),并且cptTimes字段要加一
		 *   而如果是通过wczt未领取的次数来领取就不用做以上的修改，因为状态在活动完成时之前已经改变
		 */
		if($sltMyActInfo['wczt'] < $sltMyActInfo['cptTimes']){
			$sltMyActInfo['wczt']++;
		}
		else{
			$sltMyActInfo['jljd'] = -1;
			$sltMyActInfo['procData'] = array();
			$sltMyActInfo['cptTimes'] ++;
			$sltMyActInfo['wczt']++;
		}
		
		// 检查是否可以继续领奖，对于可以领奖的活动肯定是活动领奖时间内所以不用检查时间
		$returnValue['lq'] = 0;
		if($sltMyActInfo['wczt'] < $sltMyActInfo['cptTimes']
		||$sltMyActInfo['jljd']>=0){
			$returnValue['lq'] = 1;
		}
		
		$secEndTime = isset($hdList[$sltActInfo['hdid']]['secTime'][$sltActInfo['wczt']])?$hdList[$sltActInfo['hdid']]['secTime'][$sltActInfo['wczt']]:0;
		if(1 == $myHdList[$hdid]['jslj'] && $secEndTime > $_SGLOBAL['timestamp']){
			$returnValue['lq'] = 0;
		}
		
		$returnValue['sc'] = 0;
		// 活动没有可领取道具并且活动时间过期时通知前端删除活动
		if(0 > $sltMyActInfo['jljd'] 
		&& $sltMyActInfo['cptTimes'] <= $sltMyActInfo['wczt'] 
		&& $sltMyActInfo['endTime'] < $_SGLOBAL['timestamp']){
			$returnValue['sc'] = 1;
		}
		
		$myHdList[$sltMyActInfo['ID']] = $sltMyActInfo;
		$mc->set(MC.$playersid.'_actlist', array('list'=>$myHdList), 0, 3600);
		
		$myUpdateArray['cptTimes'] = $sltMyActInfo['cptTimes'];
		$myUpdateArray['jljd']     = $sltMyActInfo['jljd'];
		$myUpdateArray['procData'] = serialize($sltMyActInfo['procData']);
		$myUpdateArray['wczt']     = $sltMyActInfo['wczt'];
		$myUpdateArray['jslj']     = $sltMyActInfo['jslj'];
		$myUpdateArray['jpjl']     = $sltMyActInfo['jpjl'];
		
		$whereArray = array('ID'=>$sltMyActInfo['ID']);
		$common->updatetable('my_huodong', $myUpdateArray, $whereArray);
		
		$returnValue['status'] = 0;
		return $returnValue;
	}


	public function dhdj($exid, $playersid){
		global $common, $db, $mc, $_SGLOBAL, $G_PlayerMgr, $_g_lang;
		$curr_time = $_SGLOBAL['timestamp'];
		if(empty($playersid)){
			// 玩家信息错误
			return array('status'=>23, 'message'=>$_g_lang['hd']['p_inf_err']);
		}
		
		//	$myHdList = hdProcess::getMyActList($playersid);
		$exList = exchangeEx::getExchangeInfo();
		if(!array_key_exists($exid, $exList)){
			// 兑换信息错误
			return array('status'=>24, 'message'=>$_g_lang['hd']['ex_inf_err']);
		}

		$exInfo = $exList[$exid];
		//  如果是结束时领奖则检查当前阶段是否完成 jslj
		if($curr_time < $exInfo['startTime'] || $curr_time > $exInfo['endTime']){
			// 兑换未到领奖时间
			return array('status'=>28, 'message'=>$_g_lang['hd']['ex_time_limit']);
		}

		// 检查阶段兑换次数
		$exSql = "select * from ".$common->tname('my_exchange');
		$exSql .= " where playersid='{$playersid}' and exc_id='{$exid}'";
		$result = $db->query($exSql);
		$exc_count = 0;
		$expire_time = 0;
		while($row = mysql_fetch_assoc($result)){
			if($exInfo['exCount'] <= $row['exc_count']&&$row['expire_time']>$curr_time){
				return array('status'=>29, 'message'=>$_g_lang['hd']['exc_count_limit']);
			}
			$exc_count = $row['expire_time']>$curr_time?$row['exc_count']:0;
			$expire_time = $row['expire_time'];
		}

		// 找到最近的兑换过期时间
		if($expire_time<$curr_time){
			$tmp_time = $exInfo['endTime'];
			foreach($exInfo['secTimes'] as $_secTime){
				if($_secTime>$curr_time&&$_secTime<$tmp_time){
					$tmp_time = $_secTime;
				}
			}

			$expire_time = $tmp_time;
		}
		
		// 获取兑换内容
		$exc_cond = $exInfo['cond'];
		$exc_result = $exInfo['result'];
		
		$roleInfo['playersid'] = $playersid;
		$res = roleModel::getRoleInfo($roleInfo);
		if (empty($roleInfo)||empty($res)) {
			// 玩家数据有误！
			return array('status'=>27,'message'=>$_g_lang['hd']['p_inf_err']);
		}
		
		$updateArray = array();
		$returnValue = array();
		$delItemList = array();
		foreach($exc_cond as $cName=>$cInfo){
			switch($cName){
				case 'tq':
					$updateArray['coins'] = $roleInfo['coins'] - $cInfo['count'];
					if($updateArray['coins']<0){
						return array('status'=>1002);
					}
					$roleInfo['coins']    = $updateArray['coins'];
					$returnValue['tq']    = $updateArray['coins'];
					$returnValue['xhtq']  = $cInfo['count'];
				break;
				case 'yb':
					$updateArray['ingot'] = $roleInfo['ingot'] - $cInfo['count'];
					if($updateArray['ingot']<0){
						return array('status'=>1002);
					}
					$roleInfo['ingot']    = $updateArray['ingot'];
					$returnValue['yb']    = $updateArray['ingot'];
					$returnValue['xhyb']  = $cInfo['count'];
				break;
				case 'yp':
					$updateArray['silver'] = $roleInfo['silver'] - $cInfo['count'];
					if($updateArray['silver']<0){
						return array('status'=>1002);
					}
					$roleInfo['silver']    = $updateArray['silver'];
					$returnValue['yp']     = $updateArray['silver'];
					$returnValue['xhyp']   = $cInfo['count'];
				break;
				case 'jl':
					$updateArray['food'] = $roleInfo['food'] - $cInfo['count'];
					if($updateArray['food']<0){
						return array('status'=>1002);
					}
					$roleInfo['food']    = $updateArray['food'];
					$returnValue['jl']   = $updateArray['food'];
					$returnValue['xhjl'] = $cInfo['count'];
				break;
				case 'sw':
					$updateArray['prestige'] = $roleInfo['prestige'] - $cInfo['count'];
					if($updateArray['prestige']<0){
						return array('status'=>1002);
					}				   
					$roleInfo['prestige']    = $updateArray['prestige'];
					$returnValue['sw']       = $updateArray['prestige'];
				break;
				default:
					$itemId = intval($cName);
					$delItemList[$itemId] = $cInfo['count'];
				break;
			}
		}

		$addItemList = array();
		foreach($exc_result as $excName=>$excInfo){
			$excNum = $excInfo['count'];
			switch($excName){
				case 'tq':
					$updateArray['coins'] = $roleInfo['coins'] + $excNum;
					$roleInfo['coins']    = $updateArray['coins'];
					$returnValue['tq']    = $updateArray['coins'];
					$returnValue['hqtq']  = $excNum;
				break;
				case 'yb':
					$updateArray['ingot'] = $roleInfo['ingot'] + $excNum;
					$roleInfo['ingot']    = $updateArray['ingot'];
					$returnValue['yb']    = $updateArray['ingot'];
					$returnValue['hqyb']  = $excNum;
				break;
				case 'yp':
					$updateArray['silver'] = $roleInfo['silver'] + $excNum;
					$roleInfo['silver']    = $updateArray['silver'];
					$returnValue['yp']     = $updateArray['silver'];
					$returnValue['hqyp']   = $excNum;
				break;
				case 'jl':
					$updateArray['food'] = $roleInfo['food'] + $excNum;
					$roleInfo['food']    = $updateArray['food'];
					$returnValue['jl']   = $updateArray['food'];
					$returnValue['hqjl'] = $excNum;
				break;
				case 'sw':
					$updateArray['prestige'] = $roleInfo['prestige'] + $excNum;
					$roleInfo['prestige']    = $updateArray['prestige'];
					$returnValue['sw']       = $updateArray['prestige'];
				break;
				default:
					$itemId = intval($excName);
					$addItemList[$itemId] = $excNum;
				break;
			}
		}
		
		// 添加道具
		if(!empty($addItemList) || !empty($delItemList)){
			$player = $G_PlayerMgr->GetPlayer($playersid);
			$isOk = $player->AddItems($addItemList, false, $delItemList, true, $status);
			if(false === $isOk){
				if($status==2){
					return array('status'=>1001);
				}
				// 背包已满
				return array('status'=>1002);
			}
			
			$bagList = $player->GetClientBag();
			$returnValue['list'] = $bagList;
			
			// 奖品说明数据
			$jpsm = array();
			foreach($addItemList as $itemId=>$num){
				$itemInfo = toolsModel::getItemInfo($itemId);
				$jpsm[]   = array('mc'=>$itemInfo['Name'],
								  'iid'=>$itemInfo['IconID'],
								  'sl'=>$num);
			}
			$returnValue['jllist'] = $jpsm;
		}
		
		// 修改玩家信息
		if(isset($returnValue['tq'])
		||isset($returnValue['yb'])
		||isset($returnValue['yp'])
		||isset($returnValue['jl'])
		||isset($returnValue['sw'])){
			$updateRoleWhere['playersid'] = $playersid;
			$common->updatetable('player', $updateArray, $updateRoleWhere);
			$mc->set(MC.$playersid, $roleInfo, 0, 3600);
		}
		
		// 更新玩家兑换数据
		$exc_count++;
		$insertSql = "insert into ".$common->tname('my_exchange');
		$insertSql .= " values('{$playersid}', '{$exid}', '{$exc_count}', '{$expire_time}')";
		$insertSql .= " ON DUPLICATE KEY UPDATE exc_count='{$exc_count}', expire_time='{$expire_time}'";
		$db->query($insertSql);
		
		$returnValue['status'] = 0;
		return $returnValue;

	}
	
	public static function lqpfjl($playersid){
		global $common, $db, $mc, $_SGLOBAL, $G_PlayerMgr, $_g_lang;

		$pflb_proto = toolsModel::getItemInfo(18613);
		$pflb_item_name = $pflb_proto['Name'];
		
		$pflb_json = array();
		$pflb_json['playersid'] = $playersid;
		$pflb_json['toplayersid'] = $playersid;
		$pflb_json['message'] = array('xjnr'=>$_g_lang['hd']['lqpflb']);
		$pflb_json['genre'] = 20;
		$pflb_json['request'] = json_encode(array('tq'=>0,'yp'=>0,'yb'=>0,'jl'=>0,'items'=>array(array('id'=>18613, 'mc'=>$pflb_item_name, 'num'=>1))));
		$pflb_json['type'] = 1;
		$pflb_json['uc'] = '0';
		$pflb_json['is_passive'] = 0;
		$pflb_json['interaction'] = 0;
		$pflb_json['tradeid'] = 0;							
		lettersModel::addMessage($pflb_json);
		
		return array('status'=>0);
	}
}