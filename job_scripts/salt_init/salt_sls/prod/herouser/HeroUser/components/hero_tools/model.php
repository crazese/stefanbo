<?php
/**
 * 只得到背包模拟删除后的数据结构而不真正删除
 * @var boolean
 */
define('DELETE_BAG_NOT_DO', false);

/**
 * 
 * 删除背包数据并返回删除后的数据结构
 * @var boolean
 */
define('DELETE_BAG_YES_DO', true);

// 定义橙色，紫色道具值
define('TOOL_VALUE_ORANGE', 5);
define('TOOL_VALUE_PURPLE', 4);

function getDjsyArr(){
	global $G_ItemUse;
	ConfigLoader::GetConfig($G_ItemUse, 'G_ItemUse');
	return $G_ItemUse;
}

class toolsModel {

	private static $_itemUseInfo = array();
	/**
	 * 道具使用时获取道具限制配置
	 */
	public static function getUseInfo($playersid, $itemId){
		global $mc, $db, $common, $tools_lang;

		if(!isset(toolsModel::$_itemUseInfo[$playersid][$itemId])){
			$sql = "select * from ol_use_limit where ";
			$sql .= "playersid='{$playersid}' and use_id='{$itemId}'";
			$result = $db->query($sql);
			while($row = $db->fetch_array($result)){
				toolsModel::$_itemUseInfo[$playersid][$itemId][$row['get_id']] = $row;
			}

			if(empty(toolsModel::$_itemUseInfo)){
				toolsModel::$_itemUseInfo[$playersid][$itemId] = array();
			}
		}

		$returnValue = toolsModel::$_itemUseInfo[$playersid][$itemId];
		unset($returnValue['mdf']);
		return $returnValue;
	}

	/**
	 * 修改当前道具限制值
	 */
	public static function setUseInfo($info){
		global $mc, $db, $common, $tools_lang;

		if(empty($info))
			return ;

		// 获得更新循环使用关键数据
		$itemExchg = ConfigLoader::GetUseItemCfg($info['use_id']);
		$lowLimit = array();
		$topLimit = array();
		$startLimit = 0;
		foreach($itemExchg['limit'] as $limit){
			if(empty($lowLimit)&&$limit['cyc']){
				$lowLimit = $limit;
			}else if($limit['cyc']){
				$lowLimit = $limit['low']<$lowLimit['low']?$limit:$lowLimit;
			}

			if(empty($topLimit)&&$limit['cyc']){
				$topLimit = $limit;
			}else if($limit['cyc']){
				$topLimit = $limit['top']>$topLimit['top']?$limit:$topLimit;
			}

			// 找到最大的不循环道具限制值
			if(empty($startLimit)&&!$limit['cyc']){
				$startLimit = $startLimit < $limit['limit']?$limit['limit']:$startLimit;
			}
		}

		$playersid = $info['playersid'];
		$itemId    = $info['use_id'];
		// 检查道具是否需要循环,并作对应更新
		if($info['use_num']>$topLimit['top']){
			$info['use_num'] = $lowLimit['low'];
			$info['get_num'] = $startLimit;
		}

		toolsModel::$_itemUseInfo[$playersid][$itemId][$info['get_id']] = $info;

		toolsModel::$_itemUseInfo[$playersid][$itemId]['mdf'] = true;
	}

	/**
	 * 更新使用限制值
	 */
	public static function mdfUseInfo(){
		global $mc, $db, $common, $tools_lang;

		$mdfList = array();
		foreach(toolsModel::$_itemUseInfo as $playersid=>$item_use_limit){
			foreach($item_use_limit as $itemId=>$use_limit){
				if(!isset($use_limit['mdf'])){
					continue;
				}

				unset($use_limit['mdf']);
				foreach($use_limit as $use_id=>$info){
					$mdfList[] = $info;
				}
			}
		}
		if(!empty($mdfList)){
			$replaceSql = "replace into " . $common->tname('use_limit');
			$replaceSql .= "(playersid,use_id,get_id,get_num,use_num) values";
			$valueStr = array();
			foreach($mdfList as $mdfInfo){
				$_sql = "('{$mdfInfo['playersid']}','{$mdfInfo['use_id']}',";
				$_sql .= "'{$mdfInfo['get_id']}','{$mdfInfo['get_num']}',";
				$_sql .= "'{$mdfInfo['use_num']}')";
				$valueStr[] = $_sql;
			}
			$replaceSql .= implode(",", $valueStr);
			$db->query($replaceSql);
		}
	}

	/**
	 * @author kknd li
	 * 获取对应类型的评级
	 * @param array $itemproto
	 * @return 如果有要替换的值就返回对应品级，没有返回原有品级
	 */
	public static function getRealRarity($itemproto) {
		// 道具大类为1时的品级值
		if($itemproto['ItemType']==1){			
			$baseRarity = array(1=>5, 122=>5,
								121=>4, 18=>4,
								120=>3, 17=>3,
								119=>2, 16=>2);

			if(18654 == $itemproto['ItemID']){
				return 5;
			}
			if(18571 == $itemproto['ItemID']){
				return 4;
			}
			
			if(isset($baseRarity[$itemproto['EquipType']])){
				return $baseRarity[$itemproto['EquipType']];
			}
		}
		return intval($itemproto['Rarity']);
	}
	
	/**
	 * 添加物品时的广播
	 *
	 * @param array $itemInfo		添加成功后背包格内的数据
	 */
	public static function addItemBroadcast($pid, $itemid) {
		global $tools_lang;
		$item = toolsModel::getItemInfo($itemid);

		// 修正道具品级，部分道具级别和Rarity字段描述有出入
		$realRarity = toolsModel::getRealRarity($item);

		// 判断抽到的物品是否是紫色装备，如果是加到广播数据中
		if(!($realRarity == TOOL_VALUE_PURPLE || $realRarity == TOOL_VALUE_ORANGE)){
			return;
		}
		$roleInfo['playersid'] = $pid;
		roleModel::getRoleInfo($roleInfo);
		if(empty($roleInfo)){
			return;
		}
		$rolename = $roleInfo['nickname'];
  		$color = 'C62FDC';
		switch($realRarity){
			case TOOL_VALUE_PURPLE:
				$color = 'C62FDC';
				break;
			case TOOL_VALUE_ORANGE:
				$color = 'DC962F';
				break;
		}

		$rep_arr1 = array('{rolename}', '{$color}', '{item_Name]}');
		$rep_arr2 = array($rolename, $color, $item['Name']);
		$notice = str_replace($rep_arr1, $rep_arr2, $tools_lang['model_notice_1']);
		lettersModel::setPublicNotice($notice);
	}
	
	/**
	 * 批量添加物品接口
	 *
	 * @param int $players			玩家id
	 * @param array $addItemList	array('id'=>'amount',...)要添加的道具id和数量组合的数组
	 * @param array $djIdList		返回新添加道具之后的模拟背包结构
	 * @param array $oldDjList		返回添加道具之前背包结构
	 * @return boolean				true成功添加，false失败
	 */
	public static function addItems($playersid, $addItemList, &$djIdList=null, &$oldDjList=null){
		global $common, $mc;
		if(is_null($oldDjList)){
			$simulateBag = toolsModel::getMyItemInfo($playersid);
			$oldDjList = $simulateBag;
		}else{
			$simulateBag = $oldDjList;
		}
		$simulateBag = toolsModel::simulateAddBag($playersid, $addItemList, $simulateBag);
		$djIdList    = $simulateBag;
		if($simulateBag === false){
			return false;
		}

		$djList = array();
		$player = roleModel::getRoleInfo($playersid);
		$roleInfo = &$player->baseInfo_;
		
		// $djIdList = array();
		foreach($simulateBag as $djid=>$item){
			if(isset($item['update'])){
				$whereArray['ID']          = $djid;
				$updateArray['EquipCount'] = $item['update'];
				$common->updatetable('player_items', $updateArray, $whereArray);
				toolsModel::addItemBroadcast($playersid, $simulateBag[$djid]['ItemID']);
				
				// $djIdList[$djid] = $item['ItemID'];
				unset($item['update']);
				$djList[$djid] = $item;
			}
			else if(isset($item['insert'])){
				$itemInfo = toolsModel::getItemInfo($item['ItemID']);
				$itemInfo['IsEquipped'] = 0;
				$itemInfo['Playersid']  = $playersid;
				$itemInfo['SortNo']     = 0;
				$itemInfo['EquipCount'] = $item['insert'];
				
				$newID = $common->inserttable('player_items', $itemInfo);
				$itemInfo['ID'] = $newID;
				$djList[$newID] = $itemInfo;
				// $djIdList[$newID] = $item['ItemID'];
				toolsModel::addItemBroadcast($playersid, $itemInfo);
			}
			else{
				$djList[$djid] = $item;
			}
		}
		
		$mc->set(MC.'items_'.$playersid, $djList, 0, 3600);
		return true;
	}
	
	// 逐鹿奖励专用批量添加
	public static function addItems_zl($bgInfo, $roleInfo, $playersid, &$djIdList=null){
		global $common, $mc;
		$simulateBag = $bgInfo;
	
		$djList = array();
		
		$random_weapon_arr = array(
				6=>array(41202,42202,43202,44202),
				9=>array(41303,42303,43303,44303),
				12=>array(41304,42304,43304,44304),
				15=>array(41405,42405,43405,44405),
				18=>array(41406,42406,43406,44406),
				21=>array(41507,42507,43507,44507)
		);
	
		$djIdList = array();
		foreach($simulateBag as $djid=>$item){
			if(isset($item['update'])){
				$whereArray['ID']          = $djid;
				$updateArray['EquipCount'] = $item['update'];
				$common->updatetable('player_items', $updateArray, $whereArray);
				toolsModel::addItemBroadcast($roleInfo['playersid'], $simulateBag[$djid]);
	
				$djIdList[$djid] = $item['ItemID'];
				unset($item['update']);
				$djList[$djid] = $item;
			}
			else if(isset($item['insert'])){
				$itemInfo = toolsModel::getItemInfo($item['ItemID']);
				$zswq_arr = array(LGBOW_ITEMID=>0,
								  HALFMOON_ITEMID=>1,
								  RUNWIND_ITEMID=>2,
								  HITTIGER_ITEMID=>2,
								  ARCHLORD_ITEMID=>0);
				
				$qh_level = 0;
				if($itemInfo['ItemType'] == 4 && !array_key_exists($item['ItemID'], $zswq_arr)) {
					foreach ($random_weapon_arr as $key=>$value) {
						if(array_search($item['ItemID'], $value) !== false) {
							$qh_level = $key;
							break;
						}
					}
				}
				
				$itemInfo['IsEquipped'] = 0;
				$itemInfo['Playersid']  = $playersid;
				$itemInfo['SortNo']     = 0;
				$itemInfo['EquipCount'] = $item['insert'];
				if($qh_level > 0) $itemInfo['QhLevel'] = $qh_level;
	
				$newID = $common->inserttable('player_items', $itemInfo);
				$itemInfo['ID'] = $newID;
				$djList[$newID] = $itemInfo;
				$djIdList[$newID] = $item['ItemID'];
				toolsModel::addItemBroadcast($roleInfo['playersid'], $item['ItemID']);
			}
			else{
				$djList[$djid] = $item;
			}
		}
		
		$mc->set(MC.'items_'.$playersid, $djList, 0, 3600);
		return true;
	}
	
	// 增加一个道具到玩家物品表$playersid(玩家ID),$itemid(道具ID),$amount(增加道具数量)
	public static function addPlayersItem($roleInfo,$itemid,$amount = 1) {
		global $mc, $db, $common, $tools_lang;
		//$roleInfo['playersid'] = $playersid;
		//roleModel::getRoleInfo($roleInfo);
		$playersid = $roleInfo['playersid'];
		$bagpack = $roleInfo['bagpack'];        //该玩家背包格数
		$itemInfo = toolsModel::getItemInfo($itemid);		
    	$bagDataInfo = toolsModel::getMyItemInfo($playersid);
		$oldItemarray = array();
		$toolsDataInfo = array();
		if (!empty($bagDataInfo)) {
			foreach ($bagDataInfo as $bagDataInfoValue) {
				if ($bagDataInfoValue['IsEquipped'] == 0) {
					$toolsDataInfo[] = $bagDataInfoValue;	
				}
			}
		}		
		$saveUP = $itemInfo['DiejiaLimit'];    //堆叠上限	
		$insert = 0;
		$now_bagpack_num = 0;
		if (empty($itemInfo)) {
			$value['status'] = 3;
			$value['message'] = $tools_lang['model_msg_1'];				
		} elseif (!empty($toolsDataInfo)) {			
			foreach ($toolsDataInfo as $bagValue) {
				if ($bagValue['ItemID'] == $itemid) {
					$oldItemarray[] = $bagValue['EquipCount'];										
				}
			}
			$oldItemGs = count($oldItemarray);  //老道具所占格数
			$xzdjzs = array_sum($oldItemarray) + $amount;  //添加后道具总数
			$zygs = ceil($xzdjzs / $saveUP);    //所占用格数
			$zjgs = $zygs - $oldItemGs;         //添加道具后增加格数
			$now_bagpack_num = count($toolsDataInfo) + $zjgs;  //添加道具后的总格数
		} else {
			$zjgs = ceil($amount / $saveUP);             //所占用格数
			$now_bagpack_num = count($toolsDataInfo) + $zjgs;  //添加道具后的总格数
		}		                   
		if ($now_bagpack_num > $bagpack) {			
			//$common->insertLog("toolsgs:$test oldItemGs:$oldItemGs zygs:$zygs zjgs:$zjgs now_bagpack_num:$now_bagpack_num bagpack:$bagpack");
			$value['status'] = 1021;
			$value['message'] = $tools_lang['model_msg_2'];
		} else {
		$value['status'] = 0;
		$process = 0;
  		    
		toolsModel::addItemBroadcast($roleInfo['playersid'], $itemInfo['ItemID']);
  		    
			// 开始添加物品
		if (!empty($toolsDataInfo)) {
				$EquipCount = array();	
				foreach ($toolsDataInfo as $bagValue) {
					$itemproto = toolsModel::getItemInfo($bagValue['ItemID'] );
					if ($bagValue['ItemID'] == $itemid && $itemproto['DiejiaLimit'] > $bagValue['EquipCount']) {
						$ID = $bagValue['ID'];
						$amount = $amount - ($itemproto['DiejiaLimit'] - $bagValue['EquipCount']);
						if ($amount < 0) {
							$adddjsl = $itemproto['DiejiaLimit'] + $amount;  //格子内道具数量
						} else {
							$adddjsl = $itemproto['DiejiaLimit'];          //格子内道具数量
						}	
						$update['EquipCount'] = $bagValue['EquipCount'] = $adddjsl;					
						$newdData[$ID] = $bagValue;								
						$common->updatetable('player_items',$update,"ID = '$ID'");
						$common->updateMemCache(MC.'items_'.$playersid,$newdData);
						$ID = NULL; 
						unset($update);						
						if ($amount <= 0) {
							$process = 1;
							break;
						} else {
							$process = 0;
						} 
					}					
				}
			}	
			if ($process == 0) {
				$addgs = ceil($amount / $itemInfo['DiejiaLimit']);
				for ($j = 0;$j < $addgs; $j++) {
					$amount = $amount - $itemInfo['DiejiaLimit'];
					if ($amount < 0) {
						$adddjsl = $itemInfo['DiejiaLimit'] + $amount;  //格子内道具数量
					} else {
						$adddjsl = $itemInfo['DiejiaLimit'];          //格子内道具数量
					}
					$insertInfo = array(
					   'ItemID'=>$itemInfo['ItemID'],
					   'EquipCount'=>$adddjsl,
					   'QhLevel'=>0,
					   'IsEquipped'=>0,
					   'Playersid'=>$playersid,
					   'SortNo'=>0
					);
					$newID = $common->inserttable('player_items',$insertInfo);	
					$value['newid'] = $newID;
					$insertInfo['ID'] = $newID;	
					if (empty($bagDataInfo) && $j == 0) {
						$mc->set(MC.'items_'.$playersid,array($newID => $insertInfo),0,3600);
					} else {
						$common->updateMemCache(MC.'items_'.$playersid,array($newID => $insertInfo));
					}
					unset($insertInfo);							
				}	
			}
		}
		return $value;
	}
	
	// 获取玩家背包总格数
	public static function getBagTotalBlocks($playersid) {
		global $mc, $db, $common, $tools_lang;
		$TotalBlocks = null;
		
		// 获取player信息
		$roleInfo = array('playersid'=>$playersid);		
		$ret = roleModel::getRoleInfo($roleInfo);
		
		if($ret) {
			$TotalBlocks = intval($roleInfo['bagpack']);
		} else {
		    $value = array('status'=>3,'message'=>$tools_lang['model_msg_3']);
		    return $value;	
		}
		if(is_numeric($TotalBlocks))
			return $TotalBlocks;
		else
			return false;
	}
	
	// 获取玩家背包剩余格数 
	public static function getBgSyGs($playersid) {
		global $mc, $db, $common;
		$tmp = toolsModel::getAllItems($playersid);
		$total = $tmp['gs'];
		$curr = $tmp['list'];
		return $total - count($curr);
	}

	public static function chkCombinInfo($itemInfo){
		global $G_CombinRule;
		if(2 != $itemInfo['ItemType']){
			return array('hclx'=>0);
		}

		$combinRule = ConfigLoader::GetCombinRule($itemInfo['EquipType']);

		if(empty($combinRule)){
			return array('hclx'=>0);
		}

		$returnValue = array('hclx'=>0);

		$spbw = 0;
		$spsl = 0;
		foreach($combinRule['items'] as $itemId=>$need){
			$spbw ++;
			$spsl += $need;
			if($itemId == $itemInfo['ItemID']){
				$returnValue['hclx'] = 1;
				$returnValue['spbw'] = $spbw;
			}
		}

		if(0 != $returnValue['hclx']){
			$returnValue['spsl'] = $spsl;
			$returnValue['hclx'] = count($combinRule['items'])>1?2:1;
		}
		if($itemInfo['EquipType'] == 32){
			//	var_dump($returnValue);
		}
		return $returnValue;
	}
	
	// 获取合成道具的信息
	public static function getCombinInfo($playersid, $djid) {
		global $mc, $db, $common;
		
		//$bagInfo = toolsModel::getMyItemInfo($playersid, $djid);
		$bagInfo = $djid;
		$ret = null;
		$equipType = $bagInfo['EquipType'];
		$itemID = $bagInfo['ItemID'];
		
		return toolsModel::chkCombinInfo($bagInfo);
	}
	
	public static function getCombinInfoByID($itemId) {
		global $mc, $db, $common;
		
		$ret = null;
		$itemInfo = $itemId;
		$equipType = $itemInfo['EquipType'];
		$itemID = $itemInfo['ItemID'];
				
		return toolsModel::chkCombinInfo($itemInfo);
	}
  
  public static function PackItem2Array($playersid, $item, &$array, $bFullinfo=true, $bPackEquiped=false) {	
    $id= $item['ID'];
    $itemprotoid = $item['ItemID'];
    $itemproto = ConfigLoader::GetItemProto($itemprotoid);
    if ((!$bPackEquiped && $item['IsEquipped']) || $itemproto['ItemType'] == 5)   return;
	
    $array[$id]['wid']  = intval($id);
    $array[$id]['djid'] = intval($itemprotoid);
    $array[$id]['num'] = intval($item['EquipCount']);	 
    $array[$id]['qhcs']  = intval($item['QhLevel']);
    
	$equipType = $itemproto['EquipType'];
	if($itemproto['ItemType'] == 4) { // 装备必返属性		
		$equipReinforceRule = getEquipReinforceRule();
		$equipReinforceRule = $equipReinforceRule[$array[$id]['qhcs']];			
		$qhValue = $equipReinforceRule['effect'];	
		$except_array = array(3=>10, 6=>20, 9=>30, 12=>40, 15=>50, 18=>60, 21=>70);						
		$zs_except_array = array(49000=>1, 49001=>1, 49002=>1, 49003=>1, 49004=>1, 41001=>1, 42001=>1, 43001=>1, 44001=>1, 41002=>1, 42002=>1, 43002=>1, 44002=>1);// 专属武器和圣诞套装 新年套装
		$show_raw_value = false;
		if(array_key_exists($array[$id]['qhcs'], $except_array)) {
			if($except_array[$array[$id]['qhcs']] == $itemproto['LevelLimit']) 
				$show_raw_value = true;
		}
		if(array_key_exists($array[$id]['djid'], $zs_except_array)) $show_raw_value = true;
		if($equipType == 1) {
			$array[$id]['tl'] = $show_raw_value ? intval($itemproto['Physical_value']) : $qhValue;		
		} else if($equipType == 2) {
			$array[$id]['fy'] = $show_raw_value ? intval($itemproto['Defense_value']) : $qhValue;
		} else if($equipType == 3) {
			$array[$id]['gj'] = $show_raw_value ? intval($itemproto['Attack_value']) : $qhValue;	
		} else if($equipType == 4) {
			$array[$id]['mj'] = $show_raw_value ? intval($itemproto['Agility_value']) : $qhValue;
		}
	}
	
    if(!$bFullinfo) return;
    
    $array[$id]['lx'] = intval($itemproto['ItemType']);
    if($itemproto['EquipType'] != 0)
      $array[$id]['st'] = intval($itemproto['EquipType']);
    $array[$id]['iconid'] = $itemproto['IconID'];	
    $array[$id]['jg']  = intval(substr($itemproto['SellPirce'], 1));
    
    $array[$id]['mc']  = $itemproto['Name'];
	if(intval($itemproto['ItemType']) != 4) {
		if((intval($itemproto['ItemID']) >= 20075 && intval($itemproto['ItemID']) <= 20089) 
		   || (intval($itemproto['ItemID']) >= 20098 && intval($itemproto['ItemID']) <= 20112)) { // 成长礼包
		  $desc_tmp = explode('|', $itemproto['Description']);
		  $array[$id]['sm']  = $desc_tmp[0];
		} else {
		  $array[$id]['sm']  = $itemproto['Description'];
		}
	}

	
    if($itemproto['ItemType'] == 1 && $equipType == 15) {
      $array[$id]['sm'] .= "||".$itemproto['ItemScript_Parameter'];
    }
    
    $array[$id]['zy'] = intval($itemproto['Zhiye']);		
              
    $array[$id]['jb']  = intval($itemproto['LevelLimit']);
    
    // 判断名将卡和初中高武将卡 
    if($itemproto['Rarity'] !== null) {
		$array[$id]['xyd'] = toolsModel::getRealRarity($itemproto);
    }
    
    // 是否是背包中直接可用道具
    if($itemproto['ItemType'] == 2) {
		if(array_key_exists($itemproto['EquipType'], getDjsyArr())) {
        $array[$id]['kysy'] = 1;
      } else {
        $array[$id]['kysy'] = 0;
      }
    }
	
	$array[$id]['nfdz'] = intval($itemproto['can_Dz']);
	$array[$id]['nfqh'] = intval($itemproto['can_Qh']);
            
    // 材料碎片
    $combinBagInfo = null;
    $combinBagInfo['EquipType'] = $itemproto['EquipType'];
    $combinBagInfo['ItemID'] = $itemproto['ItemID'];
    $combinBagInfo['ItemType'] = $itemproto['ItemType'];
    $combinInfo = toolsModel::getCombinInfo($playersid, $combinBagInfo);

    if($combinInfo['hclx'] == 1) {
      $array[$id]['hclx'] = 1; 
      $array[$id]['spsl'] = $combinInfo['spsl'];				    
    } else if($combinInfo['hclx'] == 2) { // 武将卡碎片
      $array[$id]['hclx'] = 2;
      $array[$id]['spsl'] = $combinInfo['spsl'];				    	
      $array[$id]['spbw'] = $combinInfo['spbw'];
    } else { // 非碎片
      $array[$id]['hclx'] = 0;
    }
  }
	
	// 获取背包中所有道具
	public static function getAllItems($playersid,$sendfull=false) {
		global $mc, $db, $common,$G_PlayerMgr, $tools_lang;
		$ret_val = array();
		$array = array();
		$player = $G_PlayerMgr->GetPlayer($playersid);
		if(!$player)
			return false;
		
		// 获取玩家背包格数
		$totalBlocks = $player->GetBagBlocksMax();
		$array = $player->GetClientBag($sendfull);
		if(empty($array))
			return array('status'=>998, 'message'=>$tools_lang['model_msg_4'], 'gs'=>$totalBlocks, 'list'=>$array);

		return array('status'=>0, 'gs'=>$totalBlocks, 'list'=>$array);
	}
	
	// 背包内存排序
	public static function bgMemSort(&$playerItmes) {		
		foreach ($playerItmes as $key=>$value) {
			$ItemID[] = $value['ItemID'];
			$ItemType[] = $value['ItemType'];
			$EquipType[] = $value['EquipType'];
		}
		
		array_multisort($ItemID, $ItemType, $EquipType, $playerItmes);
		
		for($i = 0; $i < count($playerItmes); $i++) {
			$playerItmes[$playerItmes[$i]['ID']] = $playerItmes[$i];
			unset($playerItmes[$i]);
		}
	}

	// 获取背包中一个道具的信息，不判断是否已装备
	public static function getBgItem($playersid, $djid) {
		global $common,$db,$mc;
		
		$rows = toolsModel::getMyItemInfo($playersid, $djid);

		// 获取玩家背包格数
		$totalBlocks = toolsModel::getBagTotalBlocks($playersid);
		$itemproto = toolsModel::getItemInfo($rows['ItemID']);
		
		$array['lx'] = intval($itemproto['ItemType']);
		if($itemproto['EquipType'] != 0)
			$array['st'] = intval($itemproto['EquipType']);
 		$array['wid']  = intval($rows['ID']);
 		$array['djid'] = intval($rows['ItemID']);
 		$array['qhcs']  = intval($rows['QhLevel']);
 		$array['iconid'] = $itemproto['IconID'];
 		$array['num'] = intval($rows['EquipCount']);
 		$jg = substr($itemproto['SellPirce'], 1);						 					
 		$array['jg']  = intval($jg);
 		$array['mc']  = $itemproto['Name'];
		
 		if((intval($rows['ItemID']) >= 20075 && intval($rows['ItemID']) <= 20089) 
		   || (intval($rows['ItemID']) >= 20098 && intval($rows['ItemID']) <= 20112)) { // 成长礼包
 			$desc_tmp = explode('|', $itemproto['Description']);
 			$array['sm']  = $desc_tmp[0];
 		} else {
			$array['sm']  = $itemproto['Description'];
 		}				
		
		$array['zy'] = intval($itemproto['Zhiye']);
		
		$equipReinforceRule = getEquipReinforceRule();
		$equipReinforceRule = $equipReinforceRule[$array['qhcs']];			
		$qhValue = $equipReinforceRule['effect'];	
		$except_array = array(3=>10, 6=>20, 9=>30, 12=>40, 15=>50, 18=>60, 21=>70);				
		$show_raw_value = false;
		if(array_key_exists($array['qhcs'], $except_array)) {
			if($except_array[$array['qhcs']] == $itemproto['LevelLimit']) 
				$show_raw_value = true;
		}		
		if($array['st'] == 1) {
			$array['tl'] = $show_raw_value ? intval($itemproto['Physical_value']) : $qhValue;			
		} else if($array['st'] == 2) {
			$array['fy'] = $show_raw_value ? intval($itemproto['Defense_value']) : $qhValue;;
		} else if($array['st'] == 3) {
			$array['gj'] = $show_raw_value ? intval($itemproto['Attack_value']) : $qhValue;;	
		} else if($array['st'] == 4) {
			$array['mj']  = $show_raw_value ? intval($itemproto['Agility_value']) : $qhValue;;
		}	

		$array['jb']  = intval($itemproto['LevelLimit']);
				
		// 判断名将卡和初中高武将卡 
		if($itemproto['Rarity'] !== null) {
			$array['xyd'] = toolsModel::getRealRarity($itemproto);
		}
				
		// 是否是背包中直接可用道具
		if($itemproto['ItemType'] == 2) {
			if(array_key_exists($itemproto['EquipType'], getDjsyArr())) {
				$array['kysy'] = 1;
			} else {
				$array['kysy'] = 0;
			}
		}
			
		// 材料碎片
		$combinInfo = toolsModel::getCombinInfo($playersid, $djid);
	    if($combinInfo['hclx'] == 1) {
	    	$array['hclx'] = 1; 
	    	$array['spsl'] = $combinInfo['spsl'];				    
	    } else if($combinInfo['hclx'] == 2) { // 武将卡碎片
	    	$array['hclx'] = 2;
	    	$array['spsl'] = $combinInfo['spsl'];				    	
	    	$array['spbw'] = $combinInfo['spbw'];
	    } else { // 非碎片
	    	$array['hclx'] = 0;
	    }  
	    
	    // 判断是否是已装备道具，如果已装备返回gid
	    if($rows['IsEquipped'] == 1){	    
	    	$gid = null;	
	    	$genData = cityModel::getGeneralData($playersid,'','*');	    	
	    	foreach ($genData as $k=>$v) {
	    		if($itemproto['EquipType'] == 1) {
	    			// 如果背包的wid等于装备位置记录的id
	    			if($v['helmet'] == $djid) {	    				
	    				$gid = intval($v['intID']);
	    				break;
	    			}
	    		} else if($itemproto['EquipType'] == 2) {
	    			if($v['carapace'] == $djid) {
	    				$gid = intval($v['intID']);
	    				break;
	    			}
	    		} else if($itemproto['EquipType'] == 3) {
	    			if($v['arms'] == $djid) {
	    				$gid = intval($v['intID']);
	    				break;
	    			}	    			
	    		} else if($itemproto['EquipType'] == 4) {
	    			if($v['shoes'] == $djid) {
	    				$gid = intval($v['intID']);
	    				break;
	    			}
	    		}
	    	}
	    	
	    	if($gid != null) {
	    		$array['gid'] = $gid;
	    	}
	    }
	
	    return $array;
	}
	
	//获取物品信息
	public static function getMyItemInfo($playersid,$id = '') {
		global $common,$db,$mc,$G_PlayerMgr;
		$player = $G_PlayerMgr->GetPlayer($playersid);

		if(empty($player))
			return null;
		if($id != '')
			return $player->GetItem($id);

		$items = $player->GetItems();
		foreach($items as $itemkey=>$item) {
			$itemproto = toolsModel::getItemInfo($item['ItemID']);
			foreach($itemproto as $key=>$value)
				$items[$itemkey][$key] = $value;
		}
		return $items;
	}
	
	// 获取背包中一个道具的总数量
	public static function getItemCount($playersid, $djid) {
		$myItems = toolsModel::getMyItemInfo($playersid);
		$item_count = 0;
		foreach ($myItems as $key=>$value) {
			if($value['ItemID'] == $djid) {
				$item_count += intval($value['EquipCount']); 
			}
		}
		
		return $item_count;
	}
	
	// 背包扩容
	public static function kuorong($playersid) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
		$returnValue = array();
		
		// 获取玩家背包格数
		$currBlocks = toolsModel::getBagTotalBlocks($playersid);
		
		if($currBlocks >= 120) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['model_msg_5']);
			return $returnValue;	
		}
		
		// 扩容类型
		$type = intval(_get('type'));
		if(_get('type') != 1 && _get('type') != 2) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;					
		}
		
		// 元宝扩容
		if($type == 1) {
			/*$yb = intval(_get('yb'));			
			
			// 元宝参数异常
			if ($yb == null || $yb == 0 || $yb < 0 || $yb != 3) {
				$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
				return $returnValue;
			}*/
			// 每次消耗3个元宝
			$yb = 3;
			
			$roleInfo = array('playersid'=>$playersid);
			$ret = roleModel::getRoleInfo($roleInfo);

			if($ret) {
			   $currYB = $roleInfo['ingot'];
			} else {
			   $value = array('status'=>3,'message'=>$tools_lang['model_msg_6']);
			   return $value;		
			}
				
			// 当前元宝不足
			$subtract = intval($currYB) - $yb;

			if( intval($currYB) == 0 || $subtract < 0 ) {
				$rep_arr1 = array('{xhyb}', '{yb}');
				$rep_arr2 = array($yb, $currYB);
				$msg = str_replace($rep_arr1, $rep_arr2, $sys_lang[2]);
				$returnValue = array('status'=>88,'message'=>$msg);				
			} else {
				$currBlocks = $currBlocks + 1;
				// 更新玩家当前元宝数量,增加背包格数
				$updateRole['ingot'] = $subtract;
				$updateRole['bagpack'] =  $currBlocks;
				$whereRole['playersid'] = $playersid;
				$common->updatetable('player', $updateRole, $whereRole); 
				$common->updateMemCache(MC.$playersid, $updateRole);
				
				$returnValue = array('status'=>0, 'gs'=>$currBlocks, 'type'=>1, 'yb'=>$subtract, 'xhyb'=>$yb);
			}
		} else if($type == 2) { // 道具扩容
			//$djid = intval(_get('djid'));
						
			// 获取道具对应脚本
			$script = null;
			$param = null;
			$playerBag = null;
			
			$player = $G_PlayerMgr->GetPlayer($playersid );
			if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
			
			$playerBag = $player->GetItems();
			
			$djid = null;
			foreach($playerBag as $key=>$value) {
				if($value['ItemID'] == KBDJ) {
					$djid = $value['ID'];
					break;
				}
			}			
		
			if($djid != null) {		
				$itemid = $playerBag[$djid]['ItemID'];
				$itemproto = ConfigLoader::GetItemProto($itemid);
				$script = $itemproto['ItemScript'];
				$param  = $itemproto['ItemScript_Parameter'];
				if($script == 0 && $param == 0) {
					$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);	
					return $returnValue;	
				} else {
					//$returnValue = $script::getItemUseMethod($param, $playersid, $djid, $playerBag);
					$returnValue = call_user_func_array($script.'::getItemUseMethod', array($param, $playersid, $djid, $playerBag));
				}
			} else {
				$returnValue = array('status'=>998, 'message'=>$tools_lang['model_msg_7']);
				return $returnValue;
			}

		}
		
		return $returnValue;
	}
	
	//读取道具信息
	public static function getItemInfo($itemId) {
		return ConfigLoader::GetItemProto($itemId);
	}
	
	// 出售道具
	public static function sellItem($playersid) {
		global $mc,$db,$common, $G_PlayerMgr, $tools_lang, $sys_lang;
		$returnValue = array();

		$djid = intval(_get('djid'));
		// 出售的道具数量
		$djsl = intval(_get('djsl'));
		
		if(intval($djsl) < 0 || intval($djsl) == 0) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}	
					
		if(intval($djsl) > 100) {
			$returnValue = array('status'=>21, 'message'=>$tools_lang['model_msg_8']);
			return $returnValue;
		}

		// 获取玩家背包中道具信息
		//$itemInfo = toolsModel::getMyItemInfo($playersid);
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);		
		
		$itemInfo  = &$player->GetItems();
		if(!isset($itemInfo[$djid]))  return array('status'=>21, 'message'=>$tools_lang['model_msg_9']);
		$itemproto  = ConfigLoader::GetItemProto($itemInfo[$djid]['ItemID']);			
		
		// fix notice warrning
		if(!array_key_exists($djid, $itemInfo)) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}
		
		if($itemInfo[$djid]['IsEquipped'] == 1) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;		
		}
		
		$tmp_jg = $itemproto['SellPirce'];
		if($tmp_jg == 0) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['model_msg_10']);
			return $returnValue;		
		}
			
		if(!array_key_exists($djid, $itemInfo)) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		} else {
			// 剩余道具数量
			$sydjsl = 0;
			// 背包中此道具数量			
			//$itemCnt = $itemInfo[$djid]['EquipCount'];	
			$itemCnt = toolsModel::getItemCount($playersid, $itemInfo[$djid]['ItemID']);
			$itemMc = $itemproto['Name'];		
			$sydjsl = intval($itemCnt) - intval($djsl);		
			
			// 当前道具数量小于出售数量
			if ( $sydjsl < 0) {
				$returnValue = array('status'=>998, 'message'=>$tools_lang['model_msg_11']);
				return $returnValue;
			} else {
				$sellInfo = $itemproto['SellPirce'];				
				// 获取出售价格的单位 1为铜钱，2为银票，3为元宝
				$sellUnit = substr($sellInfo, 0, 1);
				$sellpr = substr($sellInfo, 1);
				
				if($sellUnit != 1) {
					$returnValue = array('status'=>998, 'message'=>$tools_lang['model_msg_10']);
					return $returnValue;
				} else {
					// 获取玩家信息
					$roleInfo = array('playersid'=>$playersid);		
					$ret = roleModel::getRoleInfo($roleInfo);
					if($ret) {
					   $currCoins = $roleInfo['coins'];
					} else {
					   $value = array('status'=>3,'message'=>$tools_lang['model_msg_3']);
					   return $value;
					}
					// 所得铜钱
					$mdtq = intval($sellpr) * intval($djsl);
					// 增加当前铜钱					
					$currCoins = intval($currCoins) + $mdtq;
					if($currCoins > COINSUPLIMIT) {
						$currCoins = COINSUPLIMIT;
						$mdtq = COINSUPLIMIT - intval($currCoins);
					}
					
					// 更新玩家信息数据库和MC
					$updateRole['coins'] = $currCoins;			
					$whereRole['playersid'] = $playersid;
					$common->updatetable('player', $updateRole, $whereRole); 
					$common->updateMemCache(MC.$playersid, $updateRole);
					
					if(!$player->DeleteItemByProto(array($itemInfo[$djid]['ItemID']=>$djsl))){
						$returnValue = array('status'=>998, 'message'=>$tools_lang['model_msg_11']);
						return $returnValue;						
					}
					
					// 获取背包数据
					$bagData = $player->GetClientBag();
						
					// 出售成功
					$returnValue = array(
										 'status'=>0,
										 'tq'=>$currCoins,										
										 'mc'=>$itemMc, 
										 'mcdjsl'=>$djsl,
										 'mdtq'=>$mdtq,
										 'hqtq'=>$mdtq,
										 'list'=>$bagData
					);
					return $returnValue;
				}
			}
		}
	}
	
	//获取装备属性
	public static function getZbSx($playersid,$zbid) {
		global $G_PlayerMgr;
		$player = $G_PlayerMgr->GetPlayer($playersid);
		if(empty($player))  return null;
		return $player->GetZBSX($zbid);
	}
	
	// 获取装备属性
	public static function getZbOneSx($playersid,$zbid) {
		$itemInfo = toolsModel::getMyItemInfo($playersid,$zbid);
		$sx = 0;
		if($itemInfo['Attack_value'] != 0)
			$sx  = intval($itemInfo['Attack_value']);
		if($itemInfo['Defense_value'] != 0)
			$sx  = intval($itemInfo['Defense_value']);
		if($itemInfo['Physical_value'] != 0)
			$sx  = intval($itemInfo['Physical_value']);
		if($itemInfo['Agility_value'] != 0)
			$sx  = intval($itemInfo['Agility_value']);
				
		return $sx;
	}
	
	// 获取指定道具的总数量
	public static function getZbSl($playersid, $itemId) {
		$itemInfo = toolsModel::getMyItemInfo($playersid);
		
		$equipCnt = 0;
		foreach($itemInfo as $key=>$value) {
			if($value['ItemID'] == $itemId) {
				$equipCnt += $value['EquipCount'];
			}
		}
		
		return $equipCnt;
	}
	
	// 获取商城物品列表
	public static function sclb($playersid) {
		global $mc, $db, $common, $G_Items;
		
		$returnValue = array();
		$mallInfo = null;
		$list = null;
		
		// 获取玩家信息
    	$roleInfo = array('playersid'=>$playersid);		
		roleModel::getRoleInfo($roleInfo);

		$discount = array(1=>0.98,
						  2=>0.95,
						  3=>0.92,
						  4=>0.9,
						  5=>0.88,
						  6=>0.85,
						  7=>0.82,
						  8=>0.8,
						  9=>0.78,
						  10=>0.75,
						  11=>0.72,
						  12=>0.7);
			
		// 加载活动商城道具
		$hd_mallItem = $mc->get(MC.'hd_mallItem');
		if (empty($hd_mallItem)) {
			$mallItemRet = $db->query("SELECT * FROM ".$common->tname('shop'));
		   	while($rows = $db->fetch_array($mallItemRet)){
				$hd_mallItem[$rows['ItemID']] = $rows;
			}

		   	if(!empty($hd_mallItem) > 0){
				$mc->set(MC.'hd_mallItem', $hd_mallItem, 0, 0);
			}
		}

		ConfigLoader::GetConfig($G_Items,'G_Items');
		$mallInfo = $equipType = $itemType = array();

		if(empty($hd_mallItem)){
			$hd_mallItem = array();
		}
		foreach($hd_mallItem as $hd_key=>$hd_value) {
			// 活动新增道具
			$hd_timestart = strtotime($hd_value['TimeStart']);
			$hd_timeend   = strtotime($hd_value['TimeEnd']);
			$curr_time    = time();
			if(($curr_time >= $hd_timestart && $curr_time <= $hd_timeend) 
				|| $G_Items[$hd_key]['IsMallItem'] != 0){
				$mallInfo[] = $G_Items[$hd_key];
				$itemType[] = $G_Items[$hd_key]['ItemType'];
				$equipType[] = $G_Items[$hd_key]['EquipType'];	
			}
		}

		foreach($G_Items as $key=>$value) {
			// 商城基本道具
			if($value['IsMallItem'] != 0) {
				$mallInfo[] = $value;
				$itemType[] = $value['ItemType'];
				$equipType[] = $value['EquipType'];								
			}
		}

		array_multisort($itemType, $equipType, SORT_ASC, SORT_NUMERIC, $mallInfo);
		for($idx = 0; $idx < count($mallInfo); $idx++) {
			if($mallInfo[$idx]['IsMallItem'] == 0) {
				$mallInfo[$idx]['IsMallItem'] = '11';
			}
		}

		$returnValue['status'] = 0;
		$vipjg = 0;
		$list = array();
		for($i = 0; $i < count($mallInfo); $i++) {
			$scInfo = $mallInfo[$i]['IsMallItem'];
			$sclx = substr($scInfo, 1);			
			$BuyPrice = intval($mallInfo[$i]['BuyPrice']);
			$BuyUnit  = substr($BuyPrice, 0, 1);
			$Buypr    = intval(substr($BuyPrice, 1));
			if($roleInfo['vip'] == 0) {
				$vipjg1 = round($Buypr * $discount[1]);
			} else if($roleInfo['vip'] == 12) {
				$vipjg1 = round($Buypr * $discount[12]);
			} else {
				$vipjg1 = round($Buypr * $discount[$roleInfo['vip']]);
				$vipjg2 = round($Buypr * $discount[$roleInfo['vip']+1]);
			}
			$field_name = null;
			if($BuyUnit == 1) {
				$field_name = 'tqjg';
			} else if($BuyUnit == 3){
				$field_name = 'ybjg';
			}
			
			$attrField = null;
			$attrValue = null;
			if(intval($mallInfo[$i]['Attack_value']) != 0) {
				$attrField = 'gj';
				$attrValue = intval($mallInfo[$i]['Attack_value']);
			} else if(intval($mallInfo[$i]['Defense_value']) != 0) {
				$attrField = 'fy';
				$attrValue = intval($mallInfo[$i]['Defense_value']);
			} else if(intval($mallInfo[$i]['Physical_value']) != 0) {
				$attrField = 'tl';
				$attrValue = intval($mallInfo[$i]['Physical_value']);
			} else if(intval($mallInfo[$i]['Agility_value']) != 0) {
				$attrField = 'mj';
				$attrValue = intval($mallInfo[$i]['Agility_value']);
			}
			
			// 材料碎片EquipTypeItemType
			$combinInfo = toolsModel::getCombinInfoByID(array('ItemID'=>$mallInfo[$i]['ItemID'],
															  'ItemType'=>$mallInfo[$i]['ItemType'],
															  'EquipType'=>$mallInfo[$i]['EquipType']));
		    if($combinInfo['hclx'] == 1) {
		    	$hclx = 1;
		    	$spsl = $combinInfo['spsl'];
		    } else if($combinInfo['hclx'] == 2) { // 武将卡碎片
		    	$hclx = 2;
		    	$spsl = $combinInfo['spsl'];
		    	$spbw = $combinInfo['spbw'];
		    } else { // 非碎片
		    	$hclx = 0;
		    }  
			
		    if($hclx == 0) {
		    	if($BuyUnit == 3 && ($roleInfo['vip'] == 0 || $roleInfo['vip'] ==12)) {
					$list[] = array('sclx'=>intval($sclx),
									'wid'=>intval($mallInfo[$i]['ItemID']),
									'iconid'=>$mallInfo[$i]['IconID'],
									$field_name=>$Buypr,
									'vipjg1'=>$vipjg1,
									'mc'=>$mallInfo[$i]['Name'],
									'sm'=>$mallInfo[$i]['Description'],
									'jb'=>intval($mallInfo[$i]['LevelLimit']),
									'xyd'=>intval($mallInfo[$i]['Rarity']),
									'hclx'=>$hclx);
		    	} else if($BuyUnit == 3 && $roleInfo['vip'] < 12) {
		    		$list[] = array('sclx'=>intval($sclx),
									'wid'=>intval($mallInfo[$i]['ItemID']),
									'iconid'=>$mallInfo[$i]['IconID'],
									$field_name=>$Buypr,
									'vipjg1'=>$vipjg1,
									'vipjg2'=>$vipjg2,
									'mc'=>$mallInfo[$i]['Name'],
									'sm'=>$mallInfo[$i]['Description'],
									'jb'=>intval($mallInfo[$i]['LevelLimit']),
									'xyd'=>intval($mallInfo[$i]['Rarity']),
									'hclx'=>$hclx);
		    	} else {
		    		$list[] = array('sclx'=>intval($sclx),
									'wid'=>intval($mallInfo[$i]['ItemID']),
									'iconid'=>$mallInfo[$i]['IconID'],
									$field_name=>$Buypr,
									'mc'=>$mallInfo[$i]['Name'],
									'sm'=>$mallInfo[$i]['Description'],
									'jb'=>intval($mallInfo[$i]['LevelLimit']),
									'xyd'=>intval($mallInfo[$i]['Rarity']),
									$attrField=>$attrValue,'hclx'=>$hclx);
		    	}
		    } else if($hclx == 1) {
		    	if($BuyUnit == 3 && ($roleInfo['vip'] == 0 || $roleInfo['vip'] ==12)) {
					$list[] = array('sclx'=>intval($sclx),
									'wid'=>intval($mallInfo[$i]['ItemID']),
									'iconid'=>$mallInfo[$i]['IconID'],
									$field_name=>$Buypr,
									'vipjg1'=>$vipjg1,
									'mc'=>$mallInfo[$i]['Name'],
									'sm'=>$mallInfo[$i]['Description'],
									'jb'=>intval($mallInfo[$i]['LevelLimit']),
									'xyd'=>intval($mallInfo[$i]['Rarity']),
									'hclx'=>$hclx,
									'spsl'=>$spsl);
		    	} else if($BuyUnit == 3 && $roleInfo['vip'] < 12) {
					$list[] = array('sclx'=>intval($sclx),
									'wid'=>intval($mallInfo[$i]['ItemID']),
									'iconid'=>$mallInfo[$i]['IconID'],
									$field_name=>$Buypr,
									'vipjg1'=>$vipjg1,
									'vipjg2'=>$vipjg2,
									'mc'=>$mallInfo[$i]['Name'],
									'sm'=>$mallInfo[$i]['Description'],
									'jb'=>intval($mallInfo[$i]['LevelLimit']),
									'xyd'=>intval($mallInfo[$i]['Rarity']),
									'hclx'=>$hclx,
									'spsl'=>$spsl);		    		
		    	} else {
		    		$list[] = array('sclx'=>intval($sclx),
									'wid'=>intval($mallInfo[$i]['ItemID']),
									'iconid'=>$mallInfo[$i]['IconID'],
									$field_name=>$Buypr,
									'mc'=>$mallInfo[$i]['Name'],
									'sm'=>$mallInfo[$i]['Description'],
									'jb'=>intval($mallInfo[$i]['LevelLimit']),
									'xyd'=>intval($mallInfo[$i]['Rarity']),
									$attrField=>$attrValue,
									'hclx'=>$hclx,
									'spsl'=>$spsl);
		    	}		    	
		    } else if($hclx == 2) {
		    	if($BuyUnit == 3 && ($roleInfo['vip'] == 0 || $roleInfo['vip'] ==12)) {
		    		$list[] = array('sclx'=>intval($sclx),
									'wid'=>intval($mallInfo[$i]['ItemID']),
									'iconid'=>$mallInfo[$i]['IconID'],
									$field_name=>$Buypr,
									'vipjg1'=>$vipjg1,
									'mc'=>$mallInfo[$i]['Name'],
									'sm'=>$mallInfo[$i]['Description'],
									'jb'=>intval($mallInfo[$i]['LevelLimit']),
									'xyd'=>intval($mallInfo[$i]['Rarity']),
									'hclx'=>$hclx,
									'spsl'=>$spsl,
									'spbw'=>$spbw);
		    	} else if($BuyUnit == 3 && $roleInfo['vip'] <12) {
		    		$list[] = array('sclx'=>intval($sclx),
									'wid'=>intval($mallInfo[$i]['ItemID']),
									'iconid'=>$mallInfo[$i]['IconID'],
									$field_name=>$Buypr,
									'vipjg1'=>$vipjg1,
									'vipjg2'=>$vipjg2,
									'mc'=>$mallInfo[$i]['Name'],
									'sm'=>$mallInfo[$i]['Description'],
									'jb'=>intval($mallInfo[$i]['LevelLimit']),
									'xyd'=>intval($mallInfo[$i]['Rarity']),
									'hclx'=>$hclx,
									'spsl'=>$spsl,
									'spbw'=>$spbw);
		    	} else {
					$list[] = array('sclx'=>intval($sclx),
									'wid'=>intval($mallInfo[$i]['ItemID']),
									'iconid'=>$mallInfo[$i]['IconID'],
									$field_name=>$Buypr,
									'mc'=>$mallInfo[$i]['Name'],
									'sm'=>$mallInfo[$i]['Description'],
									'jb'=>intval($mallInfo[$i]['LevelLimit']),
									'xyd'=>intval($mallInfo[$i]['Rarity']),
									$attrField=>$attrValue,
									'hclx'=>$hclx,
									'spsl'=>$spsl,
									'spbw'=>$spbw);		    		
		    	}
		    }
		}
		
		// 根据管理后台设置的排序规则对商城的道具排序
		if (!($shop_sortrule = $mc->get(MC.'shop_sortrule'))) {
			$shop_sortrule_Ret = $db->query("SELECT * FROM ".$common->tname('shop_sortrule'));
			$shop_sortrule = $db->fetch_array($shop_sortrule_Ret, MYSQL_ASSOC);
			$shop_sortrule = $shop_sortrule['sort_rule'];
			$mc->set(MC.'shop_sortrule', $shop_sortrule, 0, 0);
		}

		if($shop_sortrule != '0') {
			$sort_rule = json_decode($shop_sortrule, true);
			
			$no_sort_arr = array();
			$search_arr = array();
			foreach($sort_rule as $k2=>$v2) {
				$search_arr[$v2['id']] = 0;
			}
			//print_r($list);
			foreach($list as $k=>$v) {
				if($v['sclx'] == 1) {
					if(!isset($search_arr[$v['wid']])) {
						$no_sort_tmp = $v;
						$no_sort_tmp['rx'] = 0;
						$no_sort_arr[] = $no_sort_tmp;						
					}
					$tmp_list_dj[$v['wid']] = $v;					
				} else {
					$tmp_list_zb[] = $v;
				}
			}
	
			$list = array();			
			foreach($sort_rule as $k1=>$v1) {
				if(!isset($tmp_list_dj[$v1['id']])) {
					continue;
				} else {				
					$tmp_list1 = $tmp_list_dj[$v1['id']];
					$tmp_list1['rx'] = $v1['hot'];
					$list[] = $tmp_list1;
				}
			}
			
			if(count($no_sort_arr) > 0) {
				$list = array_merge($list, $no_sort_arr);
				$list = array_merge($list, $tmp_list_zb);
			} else {
				$list = array_merge($list, $tmp_list_zb);
			}
		} else {
			foreach($list as $k2=>$v2) {
				$tmp_list2 = $v2;			
				$tmp_list2['rx'] = 0;				
				$ret_tmp_list[] = $tmp_list2;
			}
			$list = $ret_tmp_list;
		}
		
		$returnValue['list'] = $list;

		return $returnValue;
	}
	
	public static function virtualAdd($playersid, $djid, $djsl) {
		global $mc,$db,$common;
				
		$testBag = toolsModel::getAllItems($playersid);
		$testBag = $testBag['list'];
		if(is_array($testBag)) {
			$totalBlock = toolsModel::getBagTotalBlocks($playersid); 
			$syBlock = toolsModel::getBgSyGs($playersid);					
			$testItemInfo = toolsModel::getItemInfo($djid);	
			$diejaLimit = $testItemInfo['DiejiaLimit']; 
			$totalNum = array();
			$usedGs = 0;
			foreach ($testBag as $k=>$v) {
				if($v['djid'] == $djid) {
					$totalNum[] = $v['num'];					
				}
			}
			
			$usedGs = count($totalNum);
			$newGs = array_sum($totalNum) + $djsl;			
			$needGs = ceil($newGs / $diejaLimit);    
			$addGs = $needGs - $usedGs;        
			$newBgBlocks = count($testBag) + $addGs;
			//echo $addGs . "<br>" . $totalBlock . "<br>";
			if($newBgBlocks > $totalBlock) {
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}
	
	// 购买商城道具
	public static function gmdj($playersid, $djid, $djsl) {
		global $mc,$db,$common, $G_PlayerMgr, $tools_lang, $sys_lang;
		
		if(intval($djsl) <= 0 
		   || strlen($djid) < 5  
		   || $djid == 10014 
		   || $djid == 10031
		   || $djid == 10014
		   || ($djid >= 20061 && $djid <= 20064)  ) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}
		
		if(intval($djsl) > 100) {
			$returnValue = array('status'=>21, 'message'=>$tools_lang['model_msg_12']);
			return $returnValue;
		}
		
		// 获取道具信息
		$djInfo = toolsModel::getItemInfo($djid);
		
		$returnValue = array();
		
		// 获取玩家铜钱、元宝数量
		$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);		
		$currTQ = $roleInfo['coins'];
		$currYB = $roleInfo['ingot'];
		
		$discount = array(0=>1,
						  1=>0.98,
						  2=>0.95,
						  3=>0.92,
						  4=>0.9,
						  5=>0.88,
						  6=>0.85,
						  7=>0.82,
						  8=>0.8,
						  9=>0.78,
						  10=>0.75,
						  11=>0.72,
						  12=>0.7);
				
		// 道具购买价格
		$BuyPrice = intval($djInfo['BuyPrice']);
		$BuyUnit = substr($BuyPrice, 0, 1);
		$Buypr = substr($BuyPrice, 1);
		
		$subtract = 0;		
		if($BuyUnit == 1) {
			$subtract = intval($currTQ) - $Buypr*$djsl;
			if( intval($currTQ) == 0 || $subtract < 0 ) {
				$returnValue['status'] = 58;		
				$returnValue['xyxhtq'] = $Buypr*$djsl;
				$returnValue['coins'] = $currTQ;
				return $returnValue;
			}
		} else if($BuyUnit == 3) {
			$discount = $discount[$roleInfo['vip']];

			$xyyb = round($Buypr*$discount)*$djsl;			
			$subtract = intval($currYB) - $xyyb;

			if( $subtract < 0 ) {
				$rep_arr1 = array('{xhyb}', '{yb}');
				$rep_arr2 = array($xyyb, $currYB);
				$msg = str_replace($rep_arr1, $rep_arr2, $sys_lang[2]);
				$returnValue = array('status'=>88, 'yb'=>$currYB, 'message'=>$msg); // 元宝不足
				return $returnValue;
			}
		}
				
		// 玩家购买道具总数量记录
		$player_buyItemRecRet = $db->query("SELECT * FROM ".$common->tname('player_shop')." WHERE pid = $playersid");
		$player_buyItemRec = $db->fetch_array($player_buyItemRecRet);
		if($player_buyItemRec){
			$player_buyRec = unserialize($player_buyItemRec['data']);
		}else{
			$player_buyRec = array();
		}
		
		// 活动商城道具		
		$hd_itemArr = $mc->get(MC.'hd_mallItem');
		if (empty($hd_itemArr)) {
			$mallItemRet = $db->query("SELECT * FROM ".$common->tname('shop'));
		   	while($goods = $db->fetch_array($mallItemRet)){
				$hd_itemArr[$goods['ItemID']] = $goods;
			}
		   	$mc->set(MC.'hd_mallItem', $hd_itemArr, 0, 0);
		}
		
		if(isset($hd_itemArr[$djid])){
			$total_buyLimit = $hd_itemArr[$djid]['Total'];
			$day_buyLimit = $hd_itemArr[$djid]['BuyOfDay'];
			$buy_levelLimit = $hd_itemArr[$djid]['Condition'];
			$buy_star_time = strtotime($hd_itemArr[$djid]['TimeStart']);
			$buy_end_time = strtotime($hd_itemArr[$djid]['TimeEnd']);
			
			// 检查购买等级
			if($buy_levelLimit > $roleInfo['player_level']){
				$returnValue = array('status'=>998, 'message'=>"{$buy_levelLimit}{$tools_lang['model_msg_21']}");
				return $returnValue;
			}

			// 检查购买日期
			if($buy_end_time < time()) {
				$msg = "{$tools_lang['model_msg_14']}{$hd_itemArr[$djid]['TimeStart']}~{$hd_itemArr[$djid]['TimeEnd']})";
				$returnValue = array('status'=>998,
									 'message'=>$msg);
				return $returnValue;
			}

			// 对购买数据初始化
			if(isset($player_buyRec['today'][$djid])&&$player_buyRec['today'][$djid]['day']<$buy_star_time){
				unset($player_buyRec['today'][$djid]);
				unset($player_buyRec[$djid]);
			}
			
			// 获得当日和历史购买数量
			$dayNum = isset($player_buyRec['today'][$djid]['buynum'])?$player_buyRec['today'][$djid]['buynum']:0;
			$day = isset($player_buyRec['today'][$djid]['day'])?date('Ymd', $player_buyRec['today'][$djid]['day']):'';
			$need_day_num = date('Ymd')==$day?$dayNum + $djsl:$djsl;
			if($need_day_num > $day_buyLimit){
				$can_buyCut = $day_buyLimit - $dayNum;
				$msg = "{$tools_lang['model_msg_18']}{$day_buyLimit}";
				$msg .= $can_buyCut>0?"{$tools_lang['model_msg_19']}{$dayNum}{$tools_lang['model_msg_20']}".$can_buyCut:'';
				$msg .= $tools_lang['model_msg_17'];
				return array('status'=>998 ,'message'=>$msg);
			}

			// 检查最大购买限制
			$allNum = isset($player_buyRec[$djid])?$player_buyRec[$djid]:0;
			$need_num = $allNum + $djsl;
			if($need_num > $total_buyLimit){
				$can_buyCut = $total_buyLimit - $allNum;
				$msg = "{$tools_lang['model_msg_15']}{$total_buyLimit}";
				$msg .= $can_buyCut>0?"{$tools_lang['model_msg_16']}{$allNum}{$tools_lang['model_msg_20']}".$can_buyCut:'';
				$msg .= $tools_lang['model_msg_17'];
				return array('status'=>998, 'message'=>$msg);
			}
		}else if(0 == $djInfo['IsMallItem']){
			// 非默认商城道具同时未配制到商城
			$returnValue = array('status'=>998, 'message'=>$tools_lang['model_msg_22']);
			return $returnValue;
		}

		// 增加道具到玩家背包		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)
			return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
		$bagInfo = $player->GetItems();
		
		$ret = $player->AddItems(array($djid=>$djsl));	
				
		// 添加成功,更新玩家元宝数量
		if($ret !== false) {
			$bagData = $player->GetClientBag();
			$updateRole = null;
			$returnValue = array('status'=>0,
								 'list'=>$bagData,
								 'mc'=>$djInfo['Name'],
								 'gmsl'=>$djsl);
			if($BuyUnit == 1) {
				$returnValue['tq'] = $subtract;
				$returnValue['xhtq'] = $Buypr*$djsl;

				$updateRole['coins'] = $subtract;
			} else if($BuyUnit == 3) {
				$returnValue['yb'] = $subtract;
				$returnValue['xhyb']= round($Buypr*$discount)*$djsl;

				$updateRole['ingot'] = $subtract;
			}

			$whereRole['playersid'] = $playersid;
			$common->updatetable('player', $updateRole, $whereRole); 
			$common->updateMemCache(MC.$playersid, $updateRole);

			// 更新限制数量
			if(array_key_exists($djid, $hd_itemArr)){
				$player_buyRec[$djid] = $need_num;
				$player_buyRec['today'] = isset($player_buyRec['today'])?$player_buyRec['today']:array();
				$player_buyRec['today'][$djid] = array('day'=>time(),
													   'buynum'=>$need_day_num);
				$updateLimit = serialize($player_buyRec);
				$replaceSql = "replace into ol_player_shop(`pid`, `data`) values('{$playersid}', '$updateLimit')";
				$db->query($replaceSql);
			}
		    
			if (in_array($djid,array(41100,42100,44100,43100))) {
				if ($djid == 41100) {
					$rwid = questsController::OnFinish($roleInfo,"'gmzm','zbgm'"); 
				} else {
					$rwid = questsController::OnFinish($roleInfo,"'zbgm'"); //购买毡帽任务
				}
			    if (!empty($rwid)) {
			         $returnValue['rwid'] = $rwid;				             
			    }				
			}								
		} else {
			// 添加道具失败
			$returnValue['status'] = 998;
			$returnValue['message'] = $tools_lang['model_msg_23'];
		}	
     
		return $returnValue;
	}
	
	public static function getBglist($simBg, $playersid, $oldBg) {
		global $G_PlayerMgr;
		$player = $G_PlayerMgr->GetPlayer($playersid);
		if(!$player)  return array();
		return $player->GetClientBag();
	}
	
	// 根据成功率获取100%成功所需元宝数(强化道具)
	public static function getQhCgYb($odds) {
		$percent = $odds * 100;
		$needYb = 100 - $percent;
		
		return $needYb;
	}
	
	// 获取道具强化后的属性,$baseAttr为装备初始属性
	public static function getQhSx($qhdj, $baseAttr) {
		for($n = 1; $n < 13; $n++) {
			if($n == 1) {
				$preBaseAttr = $baseAttr;
				$ret = $preBaseAttr = $preBaseAttr + $baseAttr * (2 + 2 * QHXS * ($n-1)) / 15;						
			} else {
				$ret = $preBaseAttr = $preBaseAttr + $baseAttr * (2 + 2 * QHXS * ($n-1)) / 15;			
				
			}
			if($n == $qhdj) {
				break;
			}
		}	
		return round($ret);
	}
	
	// 强化次数限制
	public static function qhstrict($equip_level, $qh_level) {
		$rule = array(1=>3, 10=>6, 20=>9, 30=>12, 40=>15, 50=>18, 60=>21, 70=>24);
		if(intval($qh_level) < intval($rule[$equip_level])) {
			return true;
		} else {
			return false;
		}
	}

	public static function qhxx($playersid, $djid) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;

		// 获取玩家铜钱、元宝数量
		$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);		
		$currTQ = $roleInfo['coins'];
		$currYB = $roleInfo['ingot'];
		$tjpLevel = $roleInfo['tjp_level'];

		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
		$bagInfo = &$player->GetItems();
		
		// 获取玩家背包中须强化道具信息
		$itemInfo  = $bagInfo[$djid];	
		
		$itemIproto  = ConfigLoader::GetItemProto($itemInfo['ItemID']);	
		
		// 装备名称
		$itemName  = $itemIproto['Name'];
		
		// 获取道具的强化等级
		$qhLevel   = $itemInfo['QhLevel'];
		
		// 判断所选道具的类型
		$itemType  = $itemIproto['ItemType'];
		$equipType = $itemIproto['EquipType'];
		$itemID    = $itemIproto['ItemID'];
		$equipLevel = $itemIproto['LevelLimit'];
		
		// 非装备
		if($itemType != 4) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;	
		}
		
		// 非刀类武器
		if($equipLevel < 70 && $itemType == 4 && $equipType == 3) {
			if(substr($itemID, 0, 2) != '44') {
				$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_24']);
				return $returnValue;
			}
		}
		
		// 判断已强化等级
		if($qhLevel == 24) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['model_msg_25']);
			return $returnValue;	
		}
		
		// 判断是否是专属武器
		$zswq_arr = array(LGBOW_ITEMID=>1,
						  HALFMOON_ITEMID=>1,
						  RUNWIND_ITEMID=>1,
						  HITTIGER_ITEMID=>1,
						  ARCHLORD_ITEMID=>1);
		if(array_key_exists($itemID, $zswq_arr)) {
			$returnValue = array('status'=>998,
								 'message'=>$tools_lang['model_msg_26']);
			return $returnValue;	
		}
		
		// 是否是圣诞套装、金蛇套装
		$sdtz_arr = array(41001=>1,
						  42001=>1,
						  43001=>1,
						  44001=>1,
						  41002=>1,
						  42002=>1,
						  43002=>1,
						  44002=>1);
		if(array_key_exists($itemID, $sdtz_arr)) {
			$returnValue = array('status'=>998,
								 'message'=>$tools_lang['model_msg_64']);
			return $returnValue;	
		}
		
		$xjsx = 0;
		
		$comBagId = $spBagId = $qhValue = $baseAttr = $needItems = $odds = $needIngot = $commonId = $commonName = null;	
		$odds = $needCommonCnt = $commonCnt = $coins = 0;		

		// 获取强化所需道具和强化数据
		$needItems = getEquipReinforceNeed();
		$equipReinforceRule = getEquipReinforceRule();
		
		// 获取道具下级强化后属性		
		$xjsx = $equipReinforceRule[$qhLevel + 1]['effect'];

		// 获取当前强化需求信息
		$equipReinforceRule = $equipReinforceRule[$qhLevel];
		
		// 每次失败增加幸运值，幸运值3后必然成功，成功后清除幸运值
		$player_qh_luck = 0;		
		if (!($player_qh_luck = $mc->get(MC.$playersid.'_qh_luck'))) {	
			if(!is_numeric($player_qh_luck)) $player_qh_luck = 0;		
		   	$mc->set(MC.$playersid.'_qh_luck', $player_qh_luck, 0, 3600 * 24);
		}
		
		if($player_qh_luck >= 3) {
			$odds = 100;
		} else {
			// 成功率
			$odds = $equipReinforceRule['odds'] * 100;		
		}

		// 提升成功率所需元宝
		$needIngot= $equipReinforceRule['ingot'];
				
		// 所需强化材料个数		
		$needCommonCnt   = $equipReinforceRule['common'];		
			
		// 消耗铜钱数
		$coins = $equipReinforceRule['coins'];	
		
		// 玩家拥有强化材料的个数
		$commonId   = $needItems['commonItemid'];
		$commonCnt = toolsModel::getItemCount($playersid, $commonId);
		
		// 获取强化材料属性
		$itemRawInfo  = toolsModel::getItemInfo($commonId);
		$commonName   = $itemRawInfo['Name'];
		$commonIcon   = $itemRawInfo['IconID'];
		$commSubtype  = $itemRawInfo['EquipType'];
				
		//if($commonCnt == 0) {
		//	$returnValue = array('status'=>0,'qhdj'=>$qhLevel,'cgl'=>$odds,'xyyb'=>$needIngot,'xytq'=>$coins,'clmc1'=>$commonName,'tbid1'=>$commonIcon,'clsl1'=>$needCommonCnt,'xjsx'=>$xjsx);
		//} else if($commonCnt > 0) {
			$returnValue = array('status'=>0,'qhdj'=>$qhLevel,'cgl'=>$odds,'xyyb'=>$needIngot,'xytq'=>$coins,'clmc1'=>$commonName,'clzlbid1'=>$commSubtype,'tbid1'=>$commonIcon,'clsl1'=>$needCommonCnt,'xjsx'=>$xjsx);
		//}
		
		return $returnValue;
	}
	
	
	/**
	 * @author kknd li
	 * 碎片合成道具接口，合成规则定义在combin_rule表中
	 * @param 玩家id $playerid
	 * @param 背包中的物品id $djid
	 * @param 是否合成所有对应道具 $all
	 */
	public static function combinTool($playersid, $djid, $all=0){
		global $common, $mc, $G_PlayerMgr, $tools_lang;
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)
			return array('status'=>21,
						 'message'=>$tools_lang['model_msg_1']);
		
		$items = toolsModel::getMyItemInfo($playersid);
		if(!isset($items[$djid])){
			$returnValue['status'] = 692;
			$returnValue['message'] = $tools_lang['model_msg_28'];
			return $returnValue;
		}
		
		// 合成数量控制
		$combin_limit_array = array(18576=>41001,
									18577=>42001,
									18578=>43001,
									18579=>44001);
		$_itemId = $items[$djid]['ItemID'];
		if(key_exists($_itemId, $combin_limit_array)){
			$_combinId = $combin_limit_array[$_itemId];
			$amount = toolsModel::getItemCount($playersid, $_combinId);

			if($amount >= 2){
				$returnValue['status'] = 698;
				$returnValue['message'] = $tools_lang['model_msg_66'];
				return $returnValue;
			}
		}
		
		// 通过对应道具的combinId找到可以合成的物品
		//
		//	$whereArray = array('itemId'=>$items[$djid]['ItemID']);
		//	$itemCombinRule = $common->sltTableOnlyOneWithMem('combin_rule', $whereArray);
		//	$combin_id = intval($itemCombinRule['combinId']);

		$chkR = toolsModel::chkCombinInfo($items[$djid]);
		if(0 == $chkR['hclx']){
			$returnValue['status'] = 691;
			$returnValue['message'] = $tools_lang['model_msg_29'];
			
			return $returnValue;
		}
		
		// 通过目标合成物品的combinRule找到合成物品需要的道具
		// 目标合成物品的combinRule被定义为：$itemId[$needNum][,...]
		//$whereArray = array('combinId'=>$combin_id);
		//$combinRule = $common->sltTableByWhereWithMem('combin_rule', $whereArray);
		$combinRule = ConfigLoader::GetCombinRule($items[$djid]['EquipType']);
		$combin_id = intval($combinRule['target']);
		$combinArray = array();
		foreach($combinRule['items'] as $itemId=>$need){
			$combinArray[$itemId] = intval($need);
		}

		if(count($combinArray) != 0){	//至少有一次可合成
			$addRet = $player->AddItems(array($combin_id=>1), true, $combinArray, true, $status);
			if(1 == $status){
				$returnValue['status'] = 695;
				$returnValue['message'] = $tools_lang['model_msg_29'];
				
				return $returnValue;
			}else if(2 == $status){
				$returnValue['status'] = 601;
				$returnValue['message'] = $tools_lang['model_msg_30'];
				return $returnValue;
			}
			
			// 如果需要全部合成就合成到不能合成为止
			$multValue = 1;
			if(1 == $all && false !== $addRet){
				do{
					$itemcount = 
					$addRet = $player->AddItems(array($combin_id=>1), true, $combinArray, true);
					if(false !== $addRet) {//成功
						++$multValue;
					}
					else{
						break;
					}
				}while(true);
				
				// 重新初始化合成具体物品
				$tmpCombinArray = array();
				foreach($combinArray as $itemId=>$count){
					$tmpCombinArray[$itemId] = $multValue * $count;
				}
				$combinArray = $tmpCombinArray;
			}
			
			// 减少用户对于道具，如果成功就返回修改后的结果
			$player->SaveItems();
			
			// 获取铜钱	
			$currTQ = $player->baseinfo_['coins'];
			
			// 获取合成道具名
			$toolInfo   = toolsModel::getItemInfo($combin_id);
			$combinName = $toolInfo['Name'];
			
			// 获取背包数据
			$bagData = $player->GetClientBag();
			
			$returnValue['status'] = 0;
			$returnValue['tq']     = $currTQ;
			$returnValue['xhtq']   = 0;
			$returnValue['list']   = $bagData;
			$returnValue['message'] = "{$tools_lang['model_msg_31']}{$multValue}{$tools_lang['model_msg_17']}{$combinName}";
						
			return $returnValue;
		}
		else{
			$returnValue['status'] = 692;
			$returnValue['message'] = $tools_lang['model_msg_32'];
			
			return $returnValue;
		}

	}
	
	/**
	 * @author kknd li
	 * 模拟添加背包数据，用来测试玩家背包是否有足够空间来添加新道具
	 * 
	 * @param int   $playersid 玩家id
	 * @param int   $itemID    要添加的道具id，或者以itemId为键，数量为值的键值对数组
	 * @param array $bagInfo   结构和player_items一样的数组用来模拟玩家背包数据
	 * @param int   $amount    要添加的数量
	 * 
	 * @return Array|boolean 如果成功返回模拟添加后的背包结构
	 * 新添加的item只会包含ItemID,EquipCount,DiejiaLimit三个item基表字段
	 * 修改过的会包含update字段，添加的将包含insert字段，这两个都表示最终添加后格子中的堆叠数量
	 * 失败返回false
	 */
	public static function simulateAddBag($playersid, $itemIDs, $bagInfo, $amount=1){
		$heapLimit     = array();
		// 已装备道具数量
		$equippedCount = 0;
		
		// 格式化初始数据
		if(!is_array($itemIDs)){
			$itemIDs = array($itemIDs=>$amount);
		}
		
		// 遍历背包看是否还有可以堆叠的位置
		foreach($bagInfo as $djid=>$item){
			// 不计算模拟数据中已装备道具
			if(isset($item['IsEquipped'])&&$item['IsEquipped']==1){
				$equippedCount ++;
				continue;
			}
			
      $itemproto = toolsModel::getItemInfo($item['ItemID']);
			// 不计算废弃的道具种类
			if(isset($itemproto['ItemType'])&&$itemproto['ItemType']==5){
				$equippedCount ++;
				continue;
			}
			
			if(key_exists($item['ItemID'], $itemIDs)){
				$heapLimit[$item['ItemID']] = array('DiejiaLimit' => $itemproto['DiejiaLimit'],
													'Name'        => $itemproto['Name'],
													'IconID'      => $itemproto['IconID']);
				$itemFreeSpace = $item['DiejiaLimit'] - $item['EquipCount'];
				//如果这个格子是模拟添加的那么就继续标识为insert，否则就为update
				$addState = isset($item['insert'])?'insert':'update';
				if($itemIDs[$item['ItemID']] > $itemFreeSpace){
					$bagInfo[$djid]['EquipCount'] = $itemproto['DiejiaLimit'];
					$bagInfo[$djid][$addState]    = $bagInfo[$djid]['EquipCount'];
					$bagInfo[$djid]['Name']       = $itemproto['Name'];
					$bagInfo[$djid]['IconID']     = $itemproto['IconID'];
					
					$itemIDs[$item['ItemID']]     -= $itemFreeSpace;
				}
				// 可以直接堆叠到现有道具中时计算堆叠值后返回数据
				else{
					$bagInfo[$djid]['EquipCount'] += $itemIDs[$item['ItemID']];
					$bagInfo[$djid][$addState]     = $bagInfo[$djid]['EquipCount'];
					$bagInfo[$djid]['Name']        = $itemproto['Name'];
					$bagInfo[$djid]['IconID']      = $itemproto['IconID'];
					
					unset($itemIDs[$item['ItemID']]);
					if(empty($itemIDs)){
						return $bagInfo;
					}
				}
			}
		}
		
		// 检查最大堆叠上限是否存在不存在就直接获取数据
		foreach($itemIDs as $itemID=>$amount){
			if(!isset($heapLimit[$itemID])){
				$itemInfo           = toolsModel::getItemInfo($itemID);
				$heapLimit[$itemID] =  array('DiejiaLimit' => $itemInfo['DiejiaLimit'],
											'Name'        => $itemInfo['Name'],
											'IconID'      => $itemInfo['IconID']);
			}
		}
		
//		 如果没有可以堆叠的现成位置或者不够就检查背包是否还有空格
//		$maxBagCount   = toolsModel::getBagTotalBlocks($playersid);
//		$freeItemCount = $maxBagCount - count($bagInfo) + $equippedCount;
//		$maxSpace      = $freeItemCount*$heapLimit;
//		
//		if($maxSpace < $amount){
//			return false;
//		}

		//总共格子数减当前模拟背包中占用的数量
		$freeItemCount = intval(toolsModel::getBagTotalBlocks($playersid)) - (count($bagInfo) - $equippedCount);
		
		// 如果有空格就添加到新的格子
		foreach($itemIDs as $itemID=>$amount){
			while($amount>0){
				// 新加的物品都是没装备的道具
				$isEquiped = 0;
				if($amount>$heapLimit[$itemID]['DiejiaLimit']){
					$bagInfo[] = array('ItemID'       => $itemID, 
										'EquipCount'  => $heapLimit[$itemID]['DiejiaLimit'],
										'DiejiaLimit' => $heapLimit[$itemID]['DiejiaLimit'],
										'IsEquipped'  => $isEquiped,
										'insert'      => $heapLimit[$itemID]['DiejiaLimit'],
										'Name'        => $heapLimit[$itemID]['Name'],
										'IconID'      => $heapLimit[$itemID]['IconID']);
					$amount -= $heapLimit[$itemID]['DiejiaLimit'];
				}
				else{
					$bagInfo[] = array('ItemID'       => $itemID, 
										'EquipCount'  => $amount, 
										'DiejiaLimit' => $heapLimit[$itemID]['DiejiaLimit'],
										'IsEquipped'  => $isEquiped,
										'insert'      => $amount,
										'Name'        => $heapLimit[$itemID]['Name'],
										'IconID'      => $heapLimit[$itemID]['IconID']);
					$amount = 0;
				}
				$freeItemCount --;
				if($freeItemCount<0){
					return false;
				}
			}
		}
		return $bagInfo;
	}
	
	/**
	 * @author kknd li
	 * 减少玩家背包内对应道具数量
	 * @param int        $playersid
	 * @param array      $items  array(itemid=>amount, ...);
	 * @param boolean    $is_del 是否真正删除背包DELETE_BAG_YES_DO|DELETE_BAG_NOT_DO
	 * @param array      $simulateBag 模拟背包数据，用来在删除道具测试时使用
	 * 
	 * @return array|boolean 是否成功删除道具，如果成功删除道具则返回背包数据否则返回false
	 * 						   如果返回空包裹将直接返回true方便检查
	 */
	public static function deleteBags($playersid, $items, $is_del=DELETE_BAG_YES_DO, $simulateBag=null){
		global $common, $mc, $db;
		if(is_null($simulateBag)){
			$player_items = toolsModel::getMyItemInfo($playersid);
		}
		else{
			$player_items = $simulateBag;
		}
		
		// 找出需要的道具数量对应背包位置和每个位置要删除数量，由于需要从最后删除所以先倒置数据
		$needs = array();//array(itemid=>array(0=>array(djid=>amount), ...), ...)
		$player_items = array_reverse($player_items, true);
		foreach($player_items as $djid=>$item){
			// 将已装备物品剔除出计算范围
			if($item['IsEquipped']!=0){
				continue;
			}
			$item_id = $item['ItemID'];
			if(isset($items[$item_id])){
				// 删除数量小于背包格堆叠物品数量
				if($items[$item_id]<=$item['EquipCount']){
					$needs[$item_id][] = array($djid=>$items[$item_id]);
					$items[$item_id] = 0;
				}
				// 删除数量大于背包格堆叠物品数量
				else{
					$needs[$item_id][] = array($djid=>$item['EquipCount']); 
					$items[$item_id] -= $item['EquipCount'];
				}
			}
		}
		
		// 检测背包内是否有足够删除所有请求道具的数量，如果没有足够数量就返回false
		foreach($items as $subAmount){
			if($subAmount != 0){
				return false;
			}
		}
		
		// 消耗掉对应位置的对应数量道具
		foreach($needs as $itemID=>$itemNeeds){
			foreach($itemNeeds as $cells){
				foreach($cells as $djid=>$amount){
					$toolInfo = $player_items[$djid];
					$toolInfo['EquipCount'] -= $amount;
					
					if($toolInfo['EquipCount'] <=0){
						unset($player_items[$djid]);
						if($is_del == DELETE_BAG_YES_DO){
							$whereStr = ' ID='.$djid.' limit 1;';
							$common->deletetable('player_items', $whereStr);
						}
					}
					else if($is_del == DELETE_BAG_YES_DO){
						$updateItem['EquipCount'] = $toolInfo['EquipCount'];
						$whereStr = ' ID='.$djid.' limit 1;';
						$common->updatetable('player_items', $updateItem, $whereStr);
					}
					
					if(isset($player_items[$djid])){
						$player_items[$djid]['EquipCount'] = $toolInfo['EquipCount'];
					}
				}
			}
		}
		
		// 如果需要更新数据，在更新前倒置数据保证之前倒置数据结构不会对结果产生影响
		if($is_del == DELETE_BAG_YES_DO){
			$player_items = array_reverse($player_items, true);
			$mc->set(MC.'items_'.$playersid, $player_items, 0, 3600);
		}
		
		// 这里避免出现返回空数组，避免外部检查时出现if($return(array())==false)为真的问题
		$player_items = empty($player_items)?true:$player_items;
		
		return $player_items;
	}
	
	/**
	 * @author kknd li
	 * 通过道具id来删除对应格子中对应数量的道具
	 * 如果要删除的道具不存在或者是已装备的道具将调用失败
	 * @param int $playersid 
	 * @param array $djArray array($djid=>$amount, ...);
	 * 
	 * @return boolean 是否成功删除
	 */
	public static function deleteBagsByDjid($playersid, $djArray){
		global $common, $mc;
		$player_items = toolsModel::getMyItemInfo($playersid);
		
		// 检测用户是否存在对应道具并且数量足够
		foreach($djArray as $djid=>$amount){
			if(!(isset($player_items[$djid])
				&&$player_items[$djid]['EquipCount'] >= $amount)){
				return false;
			}
			
			// 不能删除已装备道具
			if($player_items[$djid]['IsEquipped'] == 0){
				return false;
			}
		}
		
		// 删除对应道具
		foreach($djArray as $djid=>$amount){
			
			$player_items[$djid]['EquipCount'] = $player_items[$djid]['EquipCount'] - $amount;
			// 对应格子的数量大于需求，直接修改数量
			if($player_items[$djid]['EquipCount']>0){
				$updateItem['EquipCount'] = $player_items[$djid]['EquipCount'];
				$whereItem['playersid'] = $playersid;
				$whereItem['ID'] = $djid;
				$common->updatetable('player_items', $updateItem, $whereItem);
			}
			// 格子中没有多余的道具，删除道具
			else{
				$whereItem['playersid'] = $playersid;
				$whereItem['ID'] = $djid;
				$common->deletetable('player_items', $whereItem);
				unset($player_items[$djid]);
			}
		}
		$mc->set(MC.'items_'.$playersid, $player_items, 0, 3600);
		
		return true;
	}
	
	/**
	 * @author kknd li
	 * 借助道具删除玩家技能
	 * 新需求不需要删除道具
	 * @param int $playersid
	 * @param int $generalID
	 * @param int $djid        道具id
	 * @param int $jnbh        武将技能格编号，取值范围[1,2]
	 */
	public static function deleteSkillByTools($playersid, $generalID, $jnbh){
		global $common, $tools_lang, $sys_lang;
		// 检测是否有技能可以遗忘
		$gerneralRows = cityModel::getGeneralData ( $playersid, 0, $generalID );
		$gerneral = $gerneralRows[0];
		$jnKey = 'jn'.$jnbh;
		
		if(!(isset($gerneral[$jnKey])
			&&$gerneral[$jnKey]!=0)
			){
				
			$returnValue['status'] = 21;
			$returnValue['message'] = $tools_lang['model_msg_33'];
			return $returnValue;
		}

		// 扣除洗技能所需银票
		$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);
		$yp = $roleInfo['silver'];
		
		if(DELETE_SKILL_YINPAO > $yp){
			$returnValue['status']  = 68;
			$returnValue['xyxhyp']  = DELETE_SKILL_YINPAO;
			$rep_arr1 = array('xhyp', 'yp');
			$rep_arr2 = array(DELETE_SKILL_YINPAO, $yp);		
			$returnValue['message'] = str_replace($rep_arr1, $rep_arr2, $sys_lang[6]);
			return $returnValue;
		}
		
		// 更新玩家银票金额
		$updateRoleArray['silver'] = $roleInfo['silver'] - DELETE_SKILL_YINPAO;
		$returnValue['xhyp'] = DELETE_SKILL_YINPAO;
		$returnValue['yp']   = $updateRoleArray['silver'];
		
		$updateRoleWhere['playersid'] = $playersid;
		$common->updatetable('player', $updateRoleArray, $updateRoleWhere);
		$roleInfo['silver'] = $updateRoleArray['silver'];
		$common->updateMemCache(MC.$playersid,$roleInfo);
		
		
		// 删除技能，如果删除的是第一技能则将第二技能移动到第一的位置
		if($jnbh==2){
			$updateArray['jn2']       = 0;
			$updateArray['jn2_level'] = 0;
		}
		else{
			$updateArray['jn1']       = $gerneral['jn2'];
			$updateArray['jn1_level'] = $gerneral['jn2_level'];
			
			$updateArray['jn2']       = 0;
			$updateArray['jn2_level'] = 0;
		}
		// 更新数据库并利用getGeneralData 函数的自动更新来更新memcache
		$whereStr = "intID={$generalID}";
		$common->updatetable('playergeneral', $updateArray, $whereStr);
		$gerneralRows = cityModel::getGeneralData($playersid, true, $generalID);
		
		// 获得技能iconID
		$returnValue['status'] = 0;
		$gerneral = $gerneralRows[0];
		if(isset($gerneral['jn1'])&&$gerneral['jn1']!=0){
			$jnCardInfo = jnk($gerneral['jn1']);
			$returnValue['jnid1']  = $gerneral['jn1'];
			$returnValue['jniid1'] = $jnCardInfo['iconid'];
			$returnValue['jndj1']  = $gerneral['jn1_level'];
			$returnValue['jnmc1']  = $jnCardInfo['n'];
		}
		if(isset($gerneral['jn2'])&&$gerneral['jn2']!=0){
			$jnCardInfo = jnk($gerneral['jn2']);
			$returnValue['jnid2']  = $gerneral['jn2'];
			$returnValue['jniid2'] = $jnCardInfo['iconid'];
			$returnValue['jndj2']  = $gerneral['jn2_level'];
			$returnValue['jnmc2']  = $jnCardInfo['n'];
		}
		
		return $returnValue;
	}
	
	public static function hqcjxx($playersid, $xxid=0) {
		global $mc, $db, $common, $tools_lang, $sys_lang;
		
		// 根据xxid的存在与否来决定是立即抽奖还是稍后抽奖
		if(0 == $xxid){
			$cjMemInfo = $mc->get(MC.$playersid.'_memCjInfo');
			if(empty($cjMemInfo)){
				$returnValue['status']  = 1211;
				$returnValue['message'] = $tools_lang['model_msg_34'];
				return $returnValue;
			}
			
			$cjInfo = $cjMemInfo['request'];
		}else{
			while(true){
				// 兼容以前的抽奖方式
				$tname = $common->tname('player_cj');
				$slt_sql = "SELECT * from {$tname} where letterID={$xxid} and playersid={$playersid}";
				$result = $db->query($slt_sql);
				$row_num = $db->num_rows($result);
				
				if($row_num == 1){
					$cjInfo = $db->fetch_array($result);
					$db->free_result ( $result );
					$cjInfo['item_pack']= unserialize($cjInfo['item_pack']);
					break;
				}
				
				// 根据抽奖id得到信件抽奖信息
				$letterReqeust = lettersModel::qqzdhf($playersid, $xxid);
				if(0 == $letterReqeust['status']){
					$cjInfo = $letterReqeust['request'];
					break;
				}
				
				// 没有发现奖励信息的时候
				$returnValue['status']  = 21;
				$returnValue['message'] = $tools_lang['model_msg_34'];
				return $returnValue;
			}
		}
		
//		$sltSql = "SELECT * FROM ".$common->tname('player_cj')." WHERE letterID='$xxid'";
//		$res = $db->query($sltSql);
//		$cjInfo = $db->fetch_array($res);
//		
//		if(!$cjInfo){
//			$returnValue['status'] = 201;
//			$returnValue['message'] = '没有对应抽奖信息';
//			return $returnValue;
//		}
//		
//		$cqInfo = $cjInfo['item_pack'];
//		$cqInfo = unserialize($cqInfo);
		
		// 获取弃牌消耗规则
		$giveUpRule = array();
		for($i=1; $i<=5; $i++){
			$whereArray = array('difficulty'=> $cjInfo['difficulty'],
								'stageID'	=> $cjInfo['stageID'],
								//'subStageID'=> $cjInfo['subStageID'],	目前小关弃牌规则都是一致的
								'type'		=> 1,
								'giveUpNum' => $i);
			$giveUpRule[$i] = $common->sltTableOnlyOneWithMem('game_use', $whereArray);
		}
		
		// 格式化一个消耗规则的字符串
		$xhjhlRule = '';
		foreach($giveUpRule as $i=>$singleRule) {
			if(isset($giveUpRule[$i-1])){
				$yp = $singleRule['yp'] - $giveUpRule[$i-1]['yp'];
				$yb = $singleRule['yb'] - $giveUpRule[$i-1]['yb'];
				$jhl = $singleRule['jhl'] - $giveUpRule[$i-1]['jhl'];
			}else{
				$yp = $singleRule['yp'];
				$yb = $singleRule['yb'];
				$jhl = $singleRule['jhl'];
			}
			
			if($yp != 0) {
				$xhjhlRule = $yp . ',';
			} else if($jhl!=0) {
				$xhjhlRule .= $jhl . ',';
			} else {
				$xhjhlRule .= $yb . ',';
			}
		}
		
		$xhjhlRule = rtrim($xhjhlRule, ',');
		$cqInfo = $cjInfo['item_pack'];
		$jpxx = array();		
		foreach ($cqInfo as $itemodd) {
			$jpxx[] = array( 'mc'=>$itemodd['Name'],
							 'iid'=>$itemodd['IconID'],
							 'xyd'=>toolsModel::getRealRarity(toolsModel::getItemInfo($itemodd['ItemID'])),
							 'sm'=>$itemodd['Description'] );
		}

		$returnValue['cgid'] = "{$cjInfo['difficulty']}_{$cjInfo['stageID']}_{$cjInfo['subStageID']}";
		$returnValue['status'] = 0;
		$returnValue['jpxx'] = $jpxx;
		$returnValue['qpjhl'] = $xhjhlRule;
		return $returnValue;
	}

	/**
	* 全概率计算
	* @param array $ps array('a'=>0.5,'b'=>0.2,'c'=>0.3)
	* @return string 返回上面数组的key
	*/
	public static function random( $ps ){  
		$markV = 0;
	    $vessel = array();
	    foreach($ps as $key=>$value){
	    	$vessel[$key]['min'] = $markV;
	    	
	    	$markV += $value * 100000;
	    	$vessel[$key]['max'] = $markV;
	    }
	    
	    $speed = mt_rand(0, $markV-1);
	    
	    foreach($vessel as $key=>$range){
	    	if($speed >= $range['min'] && $speed < $range['max']){
	    		return $key;
	    	}
	    }  
	    return false;
	}
  
	/**
	 * 创建一个对应特定关卡闯关成功的抽奖信息
	 * @param int $playersid
	 * @param int $difficulty		关卡难度
	 * @param int $stageId			大关id
	 * @param int $subStageId		小关id
	 * @param int $letterId			对应信件id
	 * @return array			抽奖信息，如果不存在对应关卡抽奖信息返回false
	 */
	public static function buildCJInfo($playersid, $difficulty, $stageId, $subStageId){
		global $common,$G_StageCardOdds,$G_ItemOdds;
    	if(empty($G_StageCardOdds)) require(S_ROOT.'configs'.DIRECTORY_SEPARATOR.'G_StageCardOdds.php');
    	
    	// 检查是否有未抽奖的memcache信件并处理
    	toolsModel::lateCj($playersid);
		
		$cjOddsList = array();
		// 获得抽奖卡信息
		$cardsInfo = $G_StageCardOdds[$difficulty.'_'.$stageId.'_'.$subStageId];
		if($cardsInfo) {
			if(empty($G_ItemOdds)) require(S_ROOT.'configs'.DIRECTORY_SEPARATOR.'G_ItemOdds.php');
			// 初始化每张卡的生成概率数据		
			foreach($cardsInfo as $cardid=>$odds){
				$toolID = toolsModel::random($G_ItemOdds[$cardid]);
				$cjOddsList[] = array('itemId'=>$toolID, 'odds'=>$odds);
			}
		}
		else{ // 不存在对应关卡信息
			$cjOddsList = array(20006=>10, 
								20058=>10, 
								20059=>10, 
								20067=>10, 
								20066=>10, 
								20070=>10);
		}
		
		// 格式化道具详细信息
		$itemOddsList = array();
		foreach($cjOddsList as $key=>$itemOdds){
			$toolInfo = toolsModel::getItemInfo($itemOdds['itemId']);
			
			// 修正道具品级，部分道具级别和Rarity字段描述有出入
			$realRarity = toolsModel::getRealRarity($toolInfo['ItemType'], $toolInfo['EquipType']);
			
			// 得到抽奖数据并格式化为如下数据结构
			// array(array('ItemID'=>$id, 'Name'=>$name, 'IconID'=>$icid, 'odds'=>$odds), 'Rarity'=>$Rarity ...)
			$itemOddsList[$key] = array('ItemID'  => $toolInfo['ItemID'],
									'Name'      => $toolInfo['Name'],
									'IconID'    => $toolInfo['IconID'],
									'Rarity'	=> $realRarity,
									'odds'      => $itemOdds['odds'],
									'Description'=> $toolInfo['Description']);
		}
		
		$returnArray = array('playersid'   => $playersid,
							'item_pack'   => $itemOddsList,
							'stageID'     => $stageId,
							 'subStageID'  => $subStageId,
							 'difficulty'  => $difficulty,
							'create_time' => date('Y-m-d H:i:s'));
		
		return $returnArray;
	}
	
	
	/**
	 * 稍后抽奖
	 *
	 * @param int $playersid
	 * @return unknown
	 */
	public static function lateCj($playersid){
		global $mc;
		$cjInfo = $mc->get(MC.$playersid.'_memCjInfo');
		
		if(!empty($cjInfo)){
			$cjInfo['request'] = json_encode($cjInfo['request']);
			lettersModel::addMessage($cjInfo);
			$mc->delete(MC.$playersid.'_memCjInfo');
		}
			
		return array('status'=>0);
	}
	
	/**
	 * @author kknd li
	 * 获得抽奖结果信息，背包最少要有一个格子来保证放抽奖物品
	 * @param int $playersid
	 * @param string $qpbh		由","分割的弃牌数据，没有弃牌时为空字符串
	 * @param int $cjid      player_cj
	 * @param int $ljcj      1立即抽奖，0信件抽奖
	 */
	public static function getCjResult($playersid, $qpbh, $xxid=0){
		global $common, $db, $mc, $G_PlayerMgr, $tools_lang, $sys_lang;
		//$mc->delete(MC.'_player'.$playersid);
		
		// 检查背包是否有一个空格
		$bagSyBlocks = toolsModel::getBgSyGs($playersid);
		if(1 > $bagSyBlocks){
			if(0 == $xxid){
				toolsModel::lateCj($playersid);
			}
			$returnValue['status']  = 24;
			$returnValue['message'] = $tools_lang['model_msg_35'];
			return $returnValue;
		}
		
		// 根据xxid的存在与否来决定是立即抽奖还是稍后抽奖
		if(0 == $xxid){
			$cjInfo = $mc->get(MC.$playersid.'_memCjInfo');
			if(empty($cjInfo)){
				$returnValue['status']  = 21;
				$returnValue['message'] = $tools_lang['model_msg_36'];
				return $returnValue;
			}
			
			$mc->delete(MC.$playersid.'_memCjInfo');
			$oddsList = $cjInfo['request'];
			$itemOddsList = $oddsList['item_pack'];
		}else{
			while(true){
				// 兼容以前的抽奖方式
				$tname = $common->tname('player_cj');
				$slt_sql = "SELECT * from {$tname} where letterID={$xxid} and playersid={$playersid}";
				$result = $db->query($slt_sql);
				$row_num = $db->num_rows($result);
				
				if($row_num == 1){
					$oddsList = $db->fetch_array($result);
					$db->free_result ( $result );
					$itemOddsList = unserialize($oddsList['item_pack']);
					break;
				}
				
				// 根据抽奖id得到信件抽奖信息
				$letterReqeust = lettersModel::qqzdhf($playersid, $xxid);
				if(0 == $letterReqeust['status']){
					$oddsList = $letterReqeust['request'];
					$itemOddsList = $oddsList['item_pack'];
					break;
				}
				
				// 没有发现奖励信息的时候
				$returnValue['status']  = 21;
				$returnValue['message'] = $tools_lang['model_msg_37'];
				return $returnValue;
			}
		}
		// 获得序列化的抽奖数据并反序列化为如下数据结构
		// array(array('ItemID'=>$id, 'Name'=>$name, 'IconID'=>$icid, 'odds'=>$odds, 'Rarity'=>$Rarity), ...)
		
		// 根据弃牌信息得到弃牌所需的花费
		if($qpbh == ''){
			$giveUpList = array();
		}
		else{
			$giveUpList = explode(',', $qpbh);
		}
		$whereArray = array('type'       => 1,
							'difficulty' => $oddsList['difficulty'],
							'stageID'    => $oddsList['stageID'],
							//'subStageID' => $oddsList['subStageID'], //目前所有的小关弃牌规则都是一致的
							'giveUpNum'  => count($giveUpList));
		$giveUpNeeds = $common->sltTableOnlyOneWithMem('game_use', $whereArray);
		
		// 检查是否有足够的银票，元宝来弃牌
		$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);
		$yp = $roleInfo['silver'];
		$yb = $roleInfo['ingot'];
		
		if($giveUpNeeds['yp'] > $yp){
			$returnValueErr['status']  = 68;
			$returnValueErr['xyxhyp']  = $giveUpNeeds['yp'];
			$rep_arr1 = array('{xhyp}', '{yp}');
			$rep_arr2 = array($giveUpNeeds['yp'], $yp);			
			$returnValueErr['message'] = str_replace($rep_arr1, $rep_arr2, $sys_lang[6]);
			return $returnValueErr;
		}
		
		if($giveUpNeeds['yb'] > $yb){
			$returnValueErr['status']  = 88;
			$rep_arr1 = array('{xhyb}', '{yb}');
			$rep_arr2 = array($giveUpNeeds['yb'], $yb);
			$returnValueErr['message'] = str_replace($rep_arr1, $rep_arr2, $sys_lang[2]);
			return $returnValueErr;
		}
		
		// 检查是否有足够的江湖令来弃牌
		/*
		if($giveUpNeeds['jhl']>0){
			$jhlNum = 0;
			$playerItems = toolsModel::getMyItemInfo($playersid);
			foreach($playerItems as $djid=>$item){
				if($item['ItemID'] == JHL_ITEMID){
					$jhlNum += $item['EquipCount'];
				}
			}
			
			if($giveUpNeeds['jhl'] > $jhlNum){
				$returnValueErr['status']  = 23;
				$returnValueErr['message'] = $tools_lang['model_msg_38'];
				return $returnValueErr;
			}
		}
		*/
		
		// 格式化出未弃牌数据并根据该信息计算出最终所得物品
		foreach($giveUpList as $cardID){
			unset($itemOddsList[$cardID - 1]);//传入的cardid起始是1，存储的oddsList起始是0
		}
		
		$oddsParams = array();
		foreach($itemOddsList as $key=>$oddsInfo){
			$oddsParams[$key] = $oddsInfo['odds'];
		}
		
		// 最终结果
		$resultKey = toolsModel::random($oddsParams);
		$resultID = $itemOddsList[$resultKey]['ItemID'];
		
		$resultKey++;
		
		// 添加物品
		$player = $G_PlayerMgr->GetPlayer($playersid);
		$addCan = $player->AddItems(array($resultID=>1));
		if(false === $addCan){
			return array('status'=>'67', 'message'=>$tools_lang['model_msg_39']);
		}
		
		// 更新玩家道具栏以及银票数量
		$returnValue['status'] = 0;
		$returnValue['bh']     = $resultKey;
		/*
		if($giveUpNeeds['jhl']>0){
			//toolsModel::deleteBags($playersid, array(1214=>$giveUpNeeds['jhl']));
			$player->DeleteItemByProto(array(1214=>$giveUpNeeds['jhl']), false);
		}
		*/
		if($giveUpNeeds['yb'] > 0 || $giveUpNeeds['yp'] > 0){
			$updateArray['silver'] = $roleInfo['silver'] - $giveUpNeeds['yp'];
			$updateArray['ingot']  = $roleInfo['ingot'] - $giveUpNeeds['yb'];
			
			$updateRoleWhere['playersid'] = $playersid;
			$common->updatetable('player',$updateArray,$updateRoleWhere);
			$roleInfo['silver'] = $updateArray['silver'];
			$roleInfo['ingot']  = $updateArray['ingot'];
			$common->updateMemCache(MC.$playersid,$roleInfo);
		}
			
		$returnValue['yp']   = $roleInfo['silver'];
		$returnValue['xhyp'] = isset($giveUpNeeds['yp'])?$giveUpNeeds['yp']:0;
		$returnValue['yb']   = $roleInfo['ingot'];
		$returnValue['xhyb'] = isset($giveUpNeeds['yb'])?$giveUpNeeds['yb']:0;
		
		// 删除抽奖信息
//		$whereArray = array('letterID'=>$xxid, 'playersid'=>$playersid);
//		$common->deletetable('player_cj', $whereArray);
		
		// 如果需要就删信件
		if(0 != $xxid){
			// $oddsList该次抽奖的具体信息，之后删除对应信件
			lettersModel::deleteLetters($xxid, $playersid);
			// 将未读消息数量减一
			lettersModel::decreaseMessageStatus($playersid, 0);
		}
		
		// 获取背包数据
		$bgData = $player->GetClientBag();
		
		$returnValue['list'] = $bgData;
		/*if ($roleInfo['player_level'] == 1) {
			$xyd = guideScript::wcjb($roleInfo,'cj',1,1);	
			//接收新的引导
			$xyd = guideScript::xsjb($roleInfo,'czb');
			if ($xyd !== false) {
				$returnValue['ydts'] = $xyd;
			}	
		}*/		
		return $returnValue;
	}
	
	public static function qhdj($playersid, $djid, $yyb) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
		
		if($yyb != 1 && $yyb != 0) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;			
		}				
		
		// 获取玩家铜钱、元宝数量、铁匠铺等级
		$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);		
		$currTQ = $roleInfo['coins'];
		$currYB = $roleInfo['ingot'];
		$tjpLevel = $roleInfo['tjp_level'];

		// 获取玩家背包所有道具
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
		$bagInfo = &$player->GetItems();
		
		// 获取玩家背包中须强化道具信息
		$itemInfo  = $bagInfo[$djid];	
		$itemIproto  = ConfigLoader::GetItemProto($itemInfo['ItemID']);	

		// 判断所选道具的类型
		$itemType  = $itemIproto['ItemType'];
		$equipType = $itemIproto['EquipType'];
		$itemID    = $itemInfo['ItemID'];
		$equipLevel = $itemIproto['LevelLimit'];
				
		// 非装备
		if($itemType != 4) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;	
		}
		
		// 非刀类武器
		if($equipLevel < 70 && $itemType == 4 && $equipType == 3) {
			if(substr($itemID, 0, 2) != '44') {
				$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_24']);
				return $returnValue;
			}
		}
		
		// 判断是否是专属武器
		$zswq_arr = array(LGBOW_ITEMID=>1,
						  HALFMOON_ITEMID=>1,
						  RUNWIND_ITEMID=>1,
						  HITTIGER_ITEMID=>1,
						  ARCHLORD_ITEMID=>1);
		if(array_key_exists($itemID, $zswq_arr)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['model_msg_26']);
			return $returnValue;
		}

		// 是否是圣诞套装、金蛇套装
		$sdtz_arr = array(41001=>1, 42001=>1, 43001=>1, 44001=>1, 44001=>1, 41002=>1, 42002=>1, 43002=>1, 44002=>1);
		if(array_key_exists($itemID, $sdtz_arr)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['model_msg_64']);
			return $returnValue;	
		}
	
		// 装备名称
		$itemName  = $itemIproto['Name'];
		// 获取道具的强化等级
		$qhLevel   = $itemInfo['QhLevel'];
		// 获取道具的稀有度
		$rarity   = $itemIproto['Rarity'];
		// 装备等级
		$equipLevel = $itemIproto['LevelLimit'];
		
		// 强化次数限制
		if(!toolsModel::qhstrict($equipLevel, $qhLevel)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['model_msg_40']);
			return $returnValue;
		}
		
		// 判断已强化等级
		if($qhLevel == 24) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['model_msg_25']);
			return $returnValue;	
		}
						
		// 如果要当前铁匠铺的等级小于需要的铁匠铺等级		
		$equipReinforceRule = getEquipReinforceRule();		
		if($tjpLevel < $equipReinforceRule[$qhLevel]['tjp']) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['model_msg_41']);
			return $returnValue;				
		}		
		
		$spBagId = $comBagId = $qhValue = $baseAttr = $needItems = $odds = $needIngot = $specificId = $commonId = $specificName = $commonName = null;	
		$needSpecificCnt = $needCommonCnt = $specificCnt = $commonCnt = 0;		
		// 根据不同类型获取强化所需道具和强化数据
		$needItems = getEquipReinforceNeed();
		$commonId  = $needItems['commonItemid'];
		
		// 获取当前玩家拥有的强化材料个数
		// 可能会有多个堆叠数最大的相同强化材料
		foreach($bagInfo as $key=>$value) {
			if($value['ItemID'] == $commonId) {
				$commonCnt += $value['EquipCount'];
			}
		}
			
		// 获取规则		
		$equipReinforceRule = $equipReinforceRule[$qhLevel];
	
		// 所需强化材料个数		
		$needCommonCnt = $equipReinforceRule['common'];
					
		// 消耗铜钱数
		$coins = $equipReinforceRule['coins'];
		
		// 成功率
		$odds = $equipReinforceRule['odds'];
		
		// 提升成功率所需元宝
		$needIngot = $equipReinforceRule['ingot'];		
		
		// 每次失败增加幸运值，幸运值3后必然成功，成功后清除幸运值		
		/*$player_qh_luck = 0;		
		if (!($player_qh_luck = $mc->get(MC.$playersid.'_qh_luck'))) {	
			if(!is_numeric($player_qh_luck)) $player_qh_luck = 0;
		   	$mc->set(MC.$playersid.'_qh_luck', $player_qh_luck, 0, 3600 * 24);
		}*/
		//$common->insertLog(json_encode($itemInfo));
		if($itemInfo['luck'] >= 3) {			
			$spResult = 'success';
		} else {
			// 失败几率
			$fodds = 1 - $odds;			
			// 生成概率数组
			$ps = array('success'=>$odds, 'faild'=>$fodds);				
			// 获取概率结果
			$spResult = toolsModel::random($ps);
		}
		
		if(($spResult == 'success') || ($yyb == 1)) {
			// 计算当前强化等级+1的效果
			$equipReinforceRule = getEquipReinforceRule();
			$equipReinforceRule = $equipReinforceRule[$qhLevel + 1];			
			$qhValue = $equipReinforceRule['effect'];			
			$returnValue['status'] = 0;		

			// 清除强化幸运值
			/*$player_qh_luck = 0;
			if(($player_qh_luck = $mc->get(MC.$playersid.'_qh_luck')))
				$mc->delete(MC.$playersid.'_qh_luck');*/
			$updateItem['luck'] = 0;
			$bagInfo[$djid]['luck'] = 0;
		} else {			
			$returnValue['status'] = 1001;
			$returnValue['message']= $tools_lang['model_msg_42'];
			
			// 强化幸运值加+1
			/*if($player_qh_luck < 3) {
				$player_qh_luck++;
				$mc->set(MC.$playersid.'_qh_luck', $player_qh_luck, 0, 3600 * 24);
			}*/
			$updateItem['luck'] = intval($itemInfo['luck']) + 1;
			$whereItem['playersid'] = $playersid;
			$whereItem['ID'] = $djid;						
			$common->updatetable('player_items', $updateItem, $whereItem);
			$bagInfo[$djid]['luck'] = $updateItem['luck'];
		}

		// 非法状况(前端判断足够情况下才可点强化按钮) 所需之物不足 就是前后端数据不一致		
		// 材料是否足够		
		$syCommCnt = $commonCnt - $needCommonCnt;

		if($syCommCnt < 0) {
			$returnValue['status'] = 1002;
			$returnValue['message']= $tools_lang['model_msg_43'];	
			$bagDataInfo = toolsModel::getAllItems($playersid);			
			$returnValue['bgkinfo'] = $bagDataInfo['list'];

			return $returnValue;
		}
		
		// 铜钱是否足够
		$syTq = $currTQ - $coins;
		if($syTq < 0) {
			$returnValue['status'] = 58;		
			$returnValue['xyxhtq'] = $coins;
			$returnValue['coins'] = $currTQ;

			return $returnValue;			
		}
		
		// 元宝是否足够
		if($yyb == 1) {
			$syYb = $currYB - $needIngot;
			if($syYb < 0) {
				$returnValueErr['status'] = 88;										
				$returnValueErr['ingot'] = $currYB;
				$rep_arr1 = array('{xhyb}', '{yb}');
				$rep_arr2 = array($needIngot, $currYB);
				$returnValueErr['message'] = str_replace($rep_arr1, $rep_arr2, $sys_lang[2]);
	
				return $returnValueErr;				
			}
		}
		
		// 材料足够开始更新强化信息
		// 减花费的铜钱、元宝(如果花费元宝)、材料
		if($yyb == 1){
			// 更新玩家当前元宝数量
			$updateRole['ingot'] = $syYb;				
		}

		// 更新玩家当前铜钱数量
		$updateRole['coins'] = $syTq;
		$whereRole['playersid'] = $playersid;
		$common->updatetable('player', $updateRole, $whereRole); 
		$common->updateMemCache(MC.$playersid, $updateRole);		
		
		// 更新装备属性信息
		if($returnValue['status'] == 0) {	
			$bagInfo[$djid]['QhLevel'] = $qhLevel + 1;			
			$updateItem['QhLevel'] = $qhLevel + 1;		
			$whereItem['playersid'] = $playersid;
			$whereItem['ID'] = $djid;						
			$common->updatetable('player_items', $updateItem, $whereItem);
				
			// 装备升级任务相关
			$roleInfo['rwsl'] = $cjInfo['qh'] = $updateItem['QhLevel'];
			achievementsModel::check_achieve($playersid,$cjInfo,array('qh'));
			$rwid = questsController::OnFinish($roleInfo,"'zbsj'");
			if (!empty($rwid)) {
				if (!empty($rwid)) {
					$returnValue['rwid'] = $rwid;
				}
			}
		}
				
		// 更新材料信息		
		$player->DeleteItemByProto(array($commonId=>$needCommonCnt));

		// 获取背包数据		
		$bagData = $player->GetClientBag();
		
		if($yyb == 1) {
			$returnValue['yb'] = $syYb;
			$returnValue['xhyb'] = $needIngot; 
			$returnValue['tq'] = $syTq; 
			$returnValue['xhtq'] = $coins; 
			$returnValue['djid'] = $djid; 
			$returnValue['list'] = $bagData;
		} else {
			$returnValue['tq'] = $syTq; 
			$returnValue['xhtq'] = $coins; 
			$returnValue['djid'] = $djid; 
			$returnValue['list'] = $bagData;
		}
		
		// 强化成功时
		// 根据强化等级发广播消息
		if($returnValue['status'] == 0) {
			$currQhLevel = $qhLevel + 1;
			$nickName = $roleInfo['nickname'];
			if($currQhLevel == 6 || $currQhLevel == 12 || $currQhLevel == 18 || $currQhLevel == 24) {
				if($rarity == 1) {
					lettersModel::setPublicNotice("{$tools_lang['model_msg_44']}<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['model_msg_45']}<FONT COLOR=\"#FFFFFF\">[{$itemName}]</FONT>{$tools_lang['model_msg_46']}<FONT COLOR=\"#fe5656\">+{$currQhLevel}</FONT>！");
				} else if($rarity == 2) {
					lettersModel::setPublicNotice("{$tools_lang['model_msg_44']}<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['model_msg_45']}<FONT COLOR=\"#66ff00\">[{$itemName}]</FONT>{$tools_lang['model_msg_46']}<FONT COLOR=\"#fe5656\">+{$currQhLevel}</FONT>！");
				}  else if($rarity == 3) {
					lettersModel::setPublicNotice("{$tools_lang['model_msg_44']}<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['model_msg_45']}<FONT COLOR=\"#60acfe\">[{$itemName}]</FONT>{$tools_lang['model_msg_46']}<FONT COLOR=\"#fe5656\">+{$currQhLevel}</FONT>！");
				}  else if($rarity == 4) {
					lettersModel::setPublicNotice("{$tools_lang['model_msg_44']}<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['model_msg_45']}<FONT COLOR=\"#ff65f8\">[{$itemName}]</FONT>{$tools_lang['model_msg_46']}<FONT COLOR=\"#fe5656\">+{$currQhLevel}</FONT>！");
				}  else if($rarity == 5) {
					lettersModel::setPublicNotice("{$tools_lang['model_msg_44']}<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['model_msg_45']}<FONT COLOR=\"#ff8929\">[{$itemName}]</FONT>{$tools_lang['model_msg_46']}<FONT COLOR=\"#fe5656\">+{$currQhLevel}</FONT>！");
				}			
			}				
		}
		
		// 返回cdata数据
		$cdata = toolsModel::getBgItem($playersid, $djid);
		if(isset($cdata['gid'])) {
			$returnValue['gid'] = $cdata['gid'];
			// 强化成功时
			if($returnValue['status'] == 0) {
				// 返回将领信息
				$getInfo['playersid'] = $playersid;
				$ginfo = cityModel::getGeneralList($getInfo, 0, false ,$cdata['gid']);   
				$returnValue['ginfo'] = $ginfo['generals'];
			}
			unset($cdata['gid']);			
		}
		$returnValue['cdata'] = $cdata;
		
		// 返回下级强化信息
		$ret = toolsModel::qhxx($playersid, $djid);
		unset($ret['status']);
		unset($ret['message']);
		$returnValue = array_merge($returnValue, $ret);
		
		return $returnValue;
	}
	
	public static function getFjRule($playersid, $itemInfo) {
		// EquipType 1为头盔,2胸甲,3武器,4战靴
		$position = null;
		if($itemInfo['EquipType'] == 1) {
			$position = "helmet";
		} else if($itemInfo['EquipType'] == 2){
			$position = "clothes";
		} else if($itemInfo['EquipType'] == 3){
			$position = "weapon";
		} else if($itemInfo['EquipType'] == 4){
			$position = "shoes";
		}
		
		// 是否是强化装备 
		$returnValue['fj_get_itmeid'] = 0;
		$returnValue['fj_get_itmeid1'] = 0;
		$returnValue['fj_get_itemNum'] = 0;
		$returnValue['fj_get_itemNum1'] = 0;
		$qhLevel = $itemInfo['QhLevel'];
		if($qhLevel > 0) {
			// 获取强化到此强化级别所消耗材料
			$qh_fj_need = null;
			$qh_fj_rule = null;
			if($position != 'weapon') {
				$qh_fj_rule = getEquipReinforceRule();
				$qh_fj_need = getEquipFjNeed();
				$returnValue['fj_get_itmeid'] = $qh_fj_need[$position]['commonItemid'];
				$returnValue['fj_get_itmeid1'] = $qh_fj_need[$position]['specificItemid'];
			} else {
				$qh_fj_rule = getWeaponReinforceRule();
				$qh_fj_need = getWeaponFjNeed();
				$returnValue['fj_get_itmeid'] = $qh_fj_need['commonItemid'];
				$returnValue['fj_get_itmeid1'] = $qh_fj_need['specificItemid'];
			}
			for($i = 0; $i < $qhLevel; $i++) {
				$returnValue['fj_get_itemNum'] += intval($qh_fj_rule[$i]['common']);
				$returnValue['fj_get_itemNum1'] += intval($qh_fj_rule[$i]['specific']);						
			}

		} 
		
		// 10%几率获取金刚石碎片			
		$ps = array('success'=>0.1, 'faild'=>0.9);
		// 获取概率结果
		$spResult = toolsModel::random($ps);
		if($spResult == 'success'  && $itemInfo['Rarity'] > 1) {				
			$equipLevel = $itemInfo['LevelLimit'];
			$fj_additon_rule = getFjRule();
			$fj_jgs_id = $fj_additon_rule['addition'][$equipLevel]['itemid'];
			$fj_jgs_num = rand($fj_additon_rule['addition'][$equipLevel]['num']['min'], $fj_additon_rule['addition'][$equipLevel]['num']['max']);
	
			$returnValue['fj_get_itmeid'] = JGSSP_ITEMID;
			$returnValue['fj_get_itemNum'] += $fj_jgs_num;				
		} else {
			// 根据装备的级别获取碎片数量
			$fj_fqh_rule = getFjRule();
			$equipLevel = $itemInfo['LevelLimit'];
			if($equipLevel == 1 && $itemInfo['Rarity'] == 1) {
				if($returnValue['fj_get_itmeid'] != 0 && $returnValue['fj_get_itemNum'] != 0) {
					$returnValue['fj_get_itemNum1'] += $fj_fqh_rule[$position][$equipLevel]['rarity'][$itemInfo['Rarity']]['num'];
				} else {
					$returnValue['fj_get_itmeid'] = $fj_fqh_rule[$position][$equipLevel]['rarity'][$itemInfo['Rarity']]['itemid'];
					$returnValue['fj_get_itemNum'] = $fj_fqh_rule[$position][$equipLevel]['rarity'][$itemInfo['Rarity']]['num'];
				}
			} else if($equipLevel == 1 && $itemInfo['Rarity'] == 2) {
				if($returnValue['fj_get_itmeid'] != 0 && $returnValue['fj_get_itemNum'] != 0) {					
					$returnValue['fj_get_itemNum1'] += rand($fj_fqh_rule[$position][$equipLevel]['rarity'][$itemInfo['Rarity']]['num']['min'], $fj_fqh_rule[$position][$equipLevel]['rarity'][$itemInfo['Rarity']]['num']['max']);
				} else {
					$returnValue['fj_get_itmeid'] = $fj_fqh_rule[$position][$equipLevel]['rarity'][$itemInfo['Rarity']]['itemid'];
					$returnValue['fj_get_itemNum'] = rand($fj_fqh_rule[$position][$equipLevel]['rarity'][$itemInfo['Rarity']]['num']['min'], $fj_fqh_rule[$position][$equipLevel]['rarity'][$itemInfo['Rarity']]['num']['max']);
				}
			} else {
				if($returnValue['fj_get_itmeid'] != 0 && $returnValue['fj_get_itemNum'] != 0) {					
					$returnValue['fj_get_itemNum1'] += rand($fj_fqh_rule[$position][$equipLevel]['num']['min'], $fj_fqh_rule[$position][$equipLevel]['num']['max']);
				} else {
					$returnValue['fj_get_itmeid'] = $fj_fqh_rule[$position][$equipLevel]['itemid'];
					$returnValue['fj_get_itemNum'] = rand($fj_fqh_rule[$position][$equipLevel]['num']['min'], $fj_fqh_rule[$position][$equipLevel]['num']['max']);
				}
			}
		}	
		
		$returnValue['qhLevel'] = $qhLevel;

		return $returnValue;
	}
	
	// 装备分解
	public static function zbfj($playersid, $wid) {
		global $_SGLOBAL, $common, $db, $mc, $tools_lang, $sys_lang;
		
		// 获取背包中道具
		$playerBag = null;			
		if( !($playerBag = $mc->get(MC.'items_'.$playersid) ) ) {
			$playerBag = toolsModel::getMyItemInfo($playersid);	
		}
		
		if($playerBag[$wid]['IsEquipped'] == 1) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;		
		}
		
		// 玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($wid, $playerBag)) {
			$returnValue = array('status'=>22, 'message'=>$tools_lang['model_msg_47']);					
			return $returnValue;
		} 		
		
		// 不可分解(非装备)
		if($playerBag[$wid]['ItemType'] != 4) {
			$returnValue = array('status'=>23, 'message'=>$tools_lang['model_msg_48']);					
			return $returnValue;		
		} 
		
		// 获取分解信息
		$fjInfo = toolsModel::getFjRule($playersid, $playerBag[$wid]);
		$fj_get_itmeid = $fjInfo['fj_get_itmeid'];
		$fj_get_itemNum = $fjInfo['fj_get_itemNum'];
		$fj_get_itmeid1 = $fjInfo['fj_get_itmeid1'];
		$fj_get_itemNum1 = $fjInfo['fj_get_itemNum1'];
		
		// 判断背包是否足够
		$flag = false;
		if($fj_get_itmeid1 != 0 && $fj_get_itemNum1 != 0) {
			if(toolsModel::virtualAdd($playersid, $fj_get_itmeid, $fj_get_itemNum) == true && toolsModel::virtualAdd($playersid, $fj_get_itmeid1, $fj_get_itemNum1) == true) {
				$flag = true;
			}
		} else {
			if(toolsModel::virtualAdd($playersid, $fj_get_itmeid, $fj_get_itemNum) == true) {
				$flag = true;
			}		
		}
		
		if($flag) {		
			// 添加分解碎片到背包
			$roleInfo['playersid'] = $playersid;
			roleModel::getRoleInfo($roleInfo);
			toolsModel::addPlayersItem($roleInfo, $fj_get_itmeid, $fj_get_itemNum);
			if($fj_get_itmeid1 != 0 && $fj_get_itemNum1 != 0) {				
				toolsModel::addPlayersItem($roleInfo, $fj_get_itmeid1, $fj_get_itemNum1);
			}
			
			// 删除被分解道具			
			$whereItem['ID'] = $wid;
			$common->deletetable('player_items', $whereItem);
			
			// 重新获取背包中道具
			$playerBag = null;			
			if( !($playerBag = $mc->get(MC.'items_'.$playersid) ) ) {
				$playerBag = toolsModel::getMyItemInfo($playersid);	
			}
			
			// 获取分解获得材料名称
			$itemInfo = toolsModel::getItemInfo($fj_get_itmeid);
			$fj_get_itemName = $itemInfo['Name'];
			$itemInfo1 = toolsModel::getItemInfo($fj_get_itmeid1);
			$fj_get_itemName1 = $itemInfo1['Name'];		
			
			// 分解成功
			$returnValue['status'] = 0;
			if($fj_get_itmeid1 != 0 && $fj_get_itemNum1 != 0) {
				$returnValue['message'] = $playerBag[$wid]['Name'] . "{$tools_lang['model_msg_49']}{$fj_get_itemNum}{$tools_lang['model_msg_17']}{$fj_get_itemName}{$tools_lang['model_msg_50']}{$fj_get_itemNum1}{$tools_lang['model_msg_17']}{$fj_get_itemName1}！";
			} else {
				$returnValue['message'] = "{$playerBag[$wid]['Name']}{$tools_lang['model_msg_49']}{$fj_get_itemNum}{$tools_lang['model_msg_17']}{$fj_get_itemName}！";
			}
			
			// 从背包内存删除
			unset($playerBag[$wid]);
			$mc->set(MC.'items_'.$playersid, $playerBag, 0, 3600);
			
			// 获取背包数据
			$bagDataInfo = toolsModel::getAllItems($playersid);
			$bagData = $bagDataInfo['list'];
			$returnValue['list'] = $bagData;
			
			return $returnValue;
		} else {
			$returnValue = array('status'=>21, 'message'=>$tools_lang['model_msg_13']);					
			return $returnValue;							
		}			
	}
	
	//学习武将技能
	public static function xxjn($playersid,$gid,$jnid,$yb = false, $djid) {
		global $_SGLOBAL, $common, $db, $mc, $G_PlayerMgr, $tools_lang, $sys_lang, $city_lang;
		$ginfo = cityModel::getGeneralData($playersid,'',$gid);
		if (!empty($djid)) {
			$itemId = $djid;
			$jnid = iddyjn($itemId);
			if ($jnid == 0) {
				$value = array('status'=>3,'message'=>$tools_lang['model_msg_1']);
				return $value;
			}
		} else {
			$itemId = jndyid($jnid);
			if ($itemId == 0) {
				$value = array('status'=>3,'message'=>$tools_lang['model_msg_1']);
				return $value;
			}			
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
		
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
		$coins = $roleInfo['coins'];  //玩家当前铜钱数量
		$ingot = $roleInfo['ingot'];  //玩家当前元宝数量
		$rand = rand(0,99);
		$xhtq = 0;
		$xhyb = 0;
		$updateRole = array();		
        if (empty($ginfo) || $itemId == 0) {
        	$value['status'] = 21;
        	$value['message'] = '未找到相关数据！';
        } else {
        	if ($jnid == 17 && $ginfo[0]['general_name'] != $city_lang['generateHireGeneral_3']) {
        		return array('status'=>30,'message'=>'该技能为'.$city_lang['generateHireGeneral_3'].'专属技能，其它武将不能学习！');
        	}
            if ($jnid == 16 && $ginfo[0]['general_name'] != $city_lang['generateHireGeneral_4']) {
        		return array('status'=>30,'message'=>'该技能为'.$city_lang['generateHireGeneral_4'].'专属技能，其它武将不能学习！');
        	}       	
        	$jn1 = $ginfo[0]['jn1'];
        	$jn1_level = $ginfo[0]['jn1_level'];
        	$jn2 = $ginfo[0]['jn2'];
        	$jn2_level = $ginfo[0]['jn2_level'];
        	if ($jn1 != 0) {
         		$jnInfo = jnk($jn1);
         		if ($jnid == $jn1 && $jn1_level >= 8) {
         			return array('status'=>600,'message'=>$tools_lang['model_msg_51']);
         		}
        		$value['jniid1'] = $jnInfo['iconid'];
        		$value['jnid1'] = intval($jn1);
        		$value['jndj1'] = intval($jn1_level);   
        		$value['mc1'] = $jnInfo['n'];
        		$value['sm1'] = $jnInfo['ms'];        		
        	}
        	if ($jn2 != 0) {
        		$jnInfo = jnk($jn2);
        	    if ($jnid == $jn2 && $jn2_level >= 7) {
         			return array('status'=>600,'message'=>$tools_lang['model_msg_51']);
         		}        		
        		$value['jniid2'] = $jnInfo['iconid'];
        		$value['jnid2'] = intval($jn2);
        		$value['jndj2'] = intval($jn2_level);         
         		$value['mc2'] = $jnInfo['n'];
        		$value['sm2'] = $jnInfo['ms'];           				
        	}
        	if (!empty($djid)) {
        		if ($jnid == $jn1 || $jnid == $jn2) {
		        	$value['status'] = 1021;
		        	$value['message'] = $tools_lang['model_msg_52']; 
		        	return $value;       			
        		}
        	}
        	if ($jnid == $jn1) {
         	    $xh = jnsj($jn1_level + 1);     //升级技能所需消耗
        	    $xhtq = $xh['tq'];      //需要的铜钱        	    
        	    if ($yb == true) {
        	    	$xhyb = $xh['yb'];  //需要元宝
        	    	$cgjl = 100;        //成功几率
        	    } else {
        	    	$xhyb = 0;
        	    	$cgjl = $xh['jl'] * 100;  //成功几率
        	    }        	    
        	    $jns = $xh['jns'];      //消耗技能书数量      
        		//$jcdj = toolsModel::deleteBags($playersid,array($itemId=>$jns),DELETE_BAG_NOT_DO); //检查技能书数量是否充足   
				//$jcdj = $player->DeleteItemByProto(array($itemId=>$jns));
                if ($xhtq > $coins) {
        			$value['status'] = 1021;
        			$value['message'] = $tools_lang['model_msg_53'];
        		} elseif ($xhyb > $ingot) {
         			$value['status'] = 88;
         			$value['yb'] = intval($ingot);
					$rep_arr1 = array('{xhyb}', '{yb}');
					$rep_arr2 = array($xhyb, $ingot);
        			$value['message'] = str_replace($rep_arr1, $rep_arr2, $sys_lang[2]);       			
        		} else {
        			if ($cgjl > $rand) {
        				$jnsjc = $player->DeleteItemByProto(array($itemId=>$jns),false,true);
						if ($jnsjc === false) {
        					return array('status' => 1021,'message' => $tools_lang['model_msg_54']);						
						}        				
	        		 	$value['status'] = 0;
	        			$updateGen['jn1'] = $ginfo[0]['jn1'] = $jnid;
	        			$updateGen['jn1_level'] = $ginfo[0]['jn1_level'] = $ginfo[0]['jn1_level'] + 1;
	        			$updateGenWhere['intID'] = $gid;
	        			$common->updatetable('playergeneral',$updateGen,$updateGenWhere);
	        			$common->updateMemCache(MC . $playersid . '_general',array($ginfo[0]['sortid']=>$ginfo[0]));    
	        			//toolsModel::deleteBags($playersid,array($itemId=>$jns),DELETE_BAG_YES_DO); //消耗技能书 
	        			$jnInfo = jnk($jnid);
	        			$value['jniid1'] = $jnInfo['iconid'];
	        			$value['jnid1'] = $updateGen['jn1'];
	        			$value['jndj1'] = $updateGen['jn1_level']; 
		        		$value['mc1'] = $jnInfo['n'];		        		 
		        		if (!empty($djid)) {
		        			$value['newjn'] = 1;
		        	        $rwid = questsController::OnFinish($roleInfo,"'jnxx'");
					        if (!empty($rwid)) {
					            if (!empty($rwid)) {
					            	   $value['rwid'] = $rwid;
					            } 
					        }
					        $value['sm1'] = $jnInfo['xxms'];
		        		} else {
        		        	if ($jn2_level >= 7 && $updateGen['jn1_level'] >= 7) {
			        			$roleInfo['qbjn'] = $_SESSION['qbjn'] = $_SESSION['qbjn'] + 1;
			        		} else {
			        			$roleInfo['qbjn'] = $_SESSION['qbjn'];
			        		}
                            if ($jn2_level >= 3 || $updateGen['jn1_level'] >= 3) {
			        			$roleInfo['jn_3'] = $_SESSION['jn_3'] = $_SESSION['jn_3'] + 1;
			        		} else {
			        			$roleInfo['jn_3'] = $_SESSION['jn_3'];
			        		}
                            if ($jn2_level >= 5 || $updateGen['jn1_level'] >= 5) {
			        			$roleInfo['jn_5'] = $_SESSION['jn_5'] = $_SESSION['jn_5'] + 1;
			        		} else {
			        			$roleInfo['jn_5'] = $_SESSION['jn_5'];
			        		}
                            if ($jn2_level >= 7 || $updateGen['jn1_level'] >= 7) {
			        			$roleInfo['jn_7'] = $_SESSION['jn_7'] = $_SESSION['jn_7'] + 1;
			        		} else {
			        			$roleInfo['jn_7'] = $_SESSION['jn_7'];
			        		}			        		
			        		$roleInfo['rwsl'] = $cjInfo['jn'] = $updateGen['jn1_level'];
			        		achievementsModel::check_achieve($playersid,$cjInfo,array('jn'));
			        		$rwid = questsController::OnFinish($roleInfo,"'jnsj','jnxx','qbjn'");
					        if (!empty($rwid)) {
					            if (!empty($rwid)) {
					            	   $value['rwid'] = $rwid;
					            } 
					        }	
					        $value['sm1'] = $jnInfo['ms'];		        			
			        	}	  	        			
        			} else {
        				//toolsModel::deleteBags($playersid,array($itemId=>$jns),DELETE_BAG_YES_DO); //消耗技能书     
						$jnsjc = $player->DeleteItemByProto(array($itemId=>$jns),false,true);
						if ($jnsjc === false) {
        					return array('status' => 1021,'message' => $tools_lang['model_msg_54']);						
						} 						
        				$value['status'] = 1001;
        			}  				
        		}        		 		
        	} elseif ($jnid == $jn2) {
         	    $xh = jnsj($jn2_level + 1);     //升级技能所需消耗
        	    $xhtq = $xh['tq'];     //需要的铜钱        	    
        	    if ($yb == true) {
        	    	$xhyb = $xh['yb']; //需要元宝
        	    	$cgjl = 100;       //成功几率
        	    } else {
        	    	$xhyb = 0;
        	    	$cgjl = $xh['jl'] * 100; //成功几率
        	    }       
        	    $jns = $xh['jns'];     //消耗技能书数量      
        		//$jcdj = toolsModel::deleteBags($playersid,array($itemId=>$jns),DELETE_BAG_NOT_DO); //检查技能书数量是否充足        	    
				//$jcdj = $player->DeleteItemByProto(array($itemId=>$jns));
                if ($xhtq > $coins) {
        			$value['status'] = 1021;
        			$value['message'] = $tools_lang['model_msg_53'];
        		} elseif ($xhyb > $ingot) {
         			$value['status'] = 88;
         			$value['yb'] = intval($ingot);
					$rep_arr1 = array('{xhyb}', '{yb}');
					$rep_arr2 = array($xhyb, $value['yb']);					
        			$value['message'] = str_replace($rep_arr1, $rep_arr2, $sys_lang[2]);       			
        		} else {
        			if ($cgjl > $rand) {
        				$jnsjc = $player->DeleteItemByProto(array($itemId=>$jns),false,true);
						if ($jnsjc === false) {
        					return array('status' => 1021,'message' => $tools_lang['model_msg_54']);						
						}             				
	        		 	$value['status'] = 0;
	        			$updateGen['jn2'] = $ginfo[0]['jn2'] = $jnid;
	        			$updateGen['jn2_level'] = $ginfo[0]['jn2_level'] = $ginfo[0]['jn2_level'] + 1;
	        			$updateGenWhere['intID'] = $gid;
	        			$common->updatetable('playergeneral',$updateGen,$updateGenWhere);
	        			$common->updateMemCache(MC . $playersid . '_general',array($ginfo[0]['sortid']=>$ginfo[0])); 
	        			//toolsModel::deleteBags($playersid,array($itemId=>$jns),DELETE_BAG_YES_DO); //消耗技能书       		
						//$player->DeleteItemByProto(array($itemId=>$jns));
	         			$jnInfo = jnk($jnid);
	        			$value['jniid2'] = $jnInfo['iconid'];
	        			$value['jnid2'] = $jnid;
	        			$value['jndj2'] = $updateGen['jn2_level'];   
		         		$value['mc2'] = $jnInfo['n'];		        		   
		        		if (!empty($djid)) {
		        			$value['newjn'] = 2;
		        		    $rwid = questsController::OnFinish($roleInfo,"'jnxx'");
					        if (!empty($rwid)) {
					            if (!empty($rwid)) {
					            	   $value['rwid'] = $rwid;
					            } 
					        }	
					        $value['sm2'] = $jnInfo['xxms'];	        			
		        		} else {
                        	if ($jn1_level >= 7 && $updateGen['jn2_level'] >= 7) {
			        			$roleInfo['qbjn'] = $_SESSION['qbjn'] = $_SESSION['qbjn'] + 1;
			        		} else {
			        			$roleInfo['qbjn'] = $_SESSION['qbjn'];
			        		}
                            if ($jn1_level >= 3 || $updateGen['jn2_level'] >= 3) {
			        			$roleInfo['jn_3'] = $_SESSION['jn_3'] = $_SESSION['jn_3'] + 1;
			        		} else {
			        			$roleInfo['jn_3'] = $_SESSION['jn_3'];
			        		}
                            if ($jn1_level >= 5 || $updateGen['jn2_level'] >= 5) {
			        			$roleInfo['jn_5'] = $_SESSION['jn_5'] = $_SESSION['jn_5'] + 1;
			        		} else {
			        			$roleInfo['jn_5'] = $_SESSION['jn_5'];
			        		}
                            if ($jn1_level >= 7 || $updateGen['jn2_level'] >= 7) {
			        			$roleInfo['jn_7'] = $_SESSION['jn_7'] = $_SESSION['jn_7'] + 1;
			        		} else {
			        			$roleInfo['jn_7'] = $_SESSION['jn_7'];
			        		}			        						        					        		
			        		$roleInfo['rwsl'] = $cjInfo['jn'] = $updateGen['jn2_level'];	
			        		achievementsModel::check_achieve($playersid,$cjInfo,array('jn'));	        			
			        		$rwid = questsController::OnFinish($roleInfo,"'jnsj','jnxx','qbjn'");
					        if (!empty($rwid)) {
					            if (!empty($rwid)) {
					            	   $value['rwid'] = $rwid;
					            } 
					        }	
					        $value['sm2'] = $jnInfo['ms'];		        			
			        	}		 	        			   
        			} else {
        				//toolsModel::deleteBags($playersid,array($itemId=>$jns),DELETE_BAG_YES_DO); //消耗技能书     
						$jnsjc = $player->DeleteItemByProto(array($itemId=>$jns),false,true);
						if ($jnsjc === false) {
        					return array('status' => 1021,'message' => $tools_lang['model_msg_54']);						
						} 						
        				$value['status'] = 1001;
        			}   					
        		}          		
        	} else {
        		$xh = jnsj(1);     //升级技能所需消耗
        		$xhtq = $xh['tq']; //需要的铜钱        		
        	    if ($yb == true) {
        	    	$xhyb = $xh['yb']; //需要元宝
        	    	$cgjl = 100;       //成功几率
        	    } else {
        	    	$xhyb = 0;
        	    	$cgjl = $xh['jl'] * 100; //成功几率
        	    }    
        		$jns = $xh['jns']; //消耗技能书数量
        		//$jcdj = toolsModel::deleteBags($playersid,array($itemId=>$jns),DELETE_BAG_NOT_DO); //检查技能书数量是否充足
				//$jcdj = $player->DeleteItemByProto(array($itemId=>$jns));
        		if ($jn1 == 0) {
        			if ($xhtq > $coins) {
        				$value['status'] = 1021;
        				$value['message'] = $tools_lang['model_msg_53'];
        			} elseif ($xhyb > $ingot) {
         				$value['status'] = 88;
         				$value['yb'] = intval($ingot);
						$rep_arr1 = array('{xhyb}', '{yb}');
						$rep_arr2 = array($xhyb, $value['yb']);					
        				$value['message'] = str_replace($rep_arr1, $rep_arr2, $sys_lang[2]);       				
        			} else {
        				if ($cgjl > $rand) {
        					$jnsjc = $player->DeleteItemByProto(array($itemId=>$jns),false,true);
							if ($jnsjc === false) {
	        					return array('status' => 1021,'message' => $tools_lang['model_msg_54']);						
							}         					
	        				$value['status'] = 0;
	        				$updateGen['jn1'] = $ginfo[0]['jn1'] = $jnid;
	        				$updateGen['jn1_level'] = $ginfo[0]['jn1_level'] = 1;
	        				$updateGenWhere['intID'] = $gid;
	        				$common->updatetable('playergeneral',$updateGen,$updateGenWhere);
	        				$common->updateMemCache(MC . $playersid . '_general',array($ginfo[0]['sortid']=>$ginfo[0]));  
	        				//toolsModel::deleteBags($playersid,array($itemId=>$jns),DELETE_BAG_YES_DO); //消耗技能书          	
							
		         			$jnInfo = jnk($jnid);
		        			$value['jniid1'] = $jnInfo['iconid'];
		        			$value['jnid1'] = $jnid;
		        			$value['jndj1'] = $updateGen['jn1_level'];   
			         		$value['mc1'] = $jnInfo['n'];			        		   		
			        		if (!empty($djid)) {
			        			$value['newjn'] = 1;
				        		$rwid = questsController::OnFinish($roleInfo,"'jnxx'");
						        if (!empty($rwid)) {
						            if (!empty($rwid)) {
						            	   $value['rwid'] = $rwid;
						            } 
						        }		
						        $value['sm1'] = $jnInfo['xxms'];	        			
			        		} else {
				        		$rwid = questsController::OnFinish($roleInfo,"'jnsj','jnxx'");
						        if (!empty($rwid)) {
						            if (!empty($rwid)) {
						            	   $value['rwid'] = $rwid;
						            } 
						        }
						        $value['sm1'] = $jnInfo['ms'];			        			
			        		}		 			        		        			 
        				} else {
        					//toolsModel::deleteBags($playersid,array($itemId=>$jns),DELETE_BAG_YES_DO); //消耗技能书     
							$jnsjc = $player->DeleteItemByProto(array($itemId=>$jns),false,true);
							if ($jnsjc === false) {
	        					return array('status' => 1021,'message' => $tools_lang['model_msg_54']);						
							}  
        					$value['status'] = 1001;
        				}      							
        			}
        		} elseif ($jn2 == 0) {
        		    if ($xhtq > $coins) {
        				$value['status'] = 1021;
        				$value['message'] = $tools_lang['model_msg_53'];
        			} elseif ($xhyb > $ingot) {
         				$value['status'] = 88;
         				$value['yb'] = intval($ingot);
        				$rep_arr1 = array('{xhyb}', '{yb}');
						$rep_arr2 = array($xhyb, $value['yb']);					
        				$value['message'] = str_replace($rep_arr1, $rep_arr2, $sys_lang[2]);      				
        			} else {        				
        				if ($cgjl > $rand) {
        					$jnsjc = $player->DeleteItemByProto(array($itemId=>$jns),false,true);
        					if ($jnsjc === false) {
	        					return array('status' => 1021,'message' => $tools_lang['model_msg_54']);						
							}       					
	        				$value['status'] = 0;
	        				$updateGen['jn2'] = $ginfo[0]['jn2'] = $jnid;
	        				$updateGen['jn2_level'] = $ginfo[0]['jn2_level'] = 1;
	        				$updateGenWhere['intID'] = $gid;
	        				$common->updatetable('playergeneral',$updateGen,$updateGenWhere);
	        				$common->updateMemCache(MC . $playersid . '_general',array($ginfo[0]['sortid']=>$ginfo[0]));  
	        				//toolsModel::deleteBags($playersid,array($itemId=>$jns),DELETE_BAG_YES_DO); //消耗技能书         							
		         			$jnInfo = jnk($jnid);
		        			$value['jniid2'] = $jnInfo['iconid'];
		        			$value['jnid2'] = $jnid;
		        			$value['jndj2'] = $updateGen['jn2_level']; 
			         		$value['mc2'] = $jnInfo['n'];			        		
			        		if (!empty($djid)) {
			        			$value['newjn'] = 2;
				        		$rwid = questsController::OnFinish($roleInfo,"'jnxx'");
						        if (!empty($rwid)) {
						            if (!empty($rwid)) {
						            	   $value['rwid'] = $rwid;
						            } 
						        }	
						        $value['sm2'] = $jnInfo['xxms']; 		        			
			        		} else {
				        		$rwid = questsController::OnFinish($roleInfo,"'jnsj','jnxx'");
						        if (!empty($rwid)) {
						            if (!empty($rwid)) {
						            	   $value['rwid'] = $rwid;
						            } 
						        }			        			
			        		}
			        		$value['sm2'] = $jnInfo['ms']; 			        		  		        			 
        				} else {
        					//toolsModel::deleteBags($playersid,array($itemId=>$jns),DELETE_BAG_YES_DO); //消耗技能书     
							$jnsjc = $player->DeleteItemByProto(array($itemId=>$jns),false,true);
        					if ($jnsjc === false) {
	        					return array('status' => 1021,'message' => $tools_lang['model_msg_54']);						
							} 							
        					$value['status'] = 1001;
        				}        								
        			}       			
        		} else {
        			$value['status'] = 1021;
        			$value['message'] = $tools_lang['model_msg_55'];
        		}
        	}
        }
        if ($value['status'] == 0 || $value['status'] == 1001) {
        	$value['gid'] = $gid;
        	//$djInfo = toolsModel::getAllItems($playersid);
        	//$value['list'] = $djInfo['list'];
        	//$simBg = $playerBag = null;
        	//$value['list'] = toolsModel::getBglist($simBg, $playersid, $playerBag);
			$value['list'] = $player->GetClientBag();
            if ($xhyb > 0) {
	        	$value['xhyb'] = $xhyb;
	        	$value['yb'] = $ingot - $xhyb;
	        	$updateRole['ingot'] = $value['yb'];
            }  
            if ($xhtq > 0) {
	        	$value['xhtq'] = $xhtq;
	        	$value['tq'] = $coins - $xhtq;
	        	$updateRole['coins'] = $value['tq'];
            }    
            if (!empty($updateRole)) {             	
	         	$updateRoleWhere['playersid'] = $playersid;
	        	$common->updatetable('player',$updateRole,$updateRoleWhere);
            }
        	$common->updateMemCache(MC.$playersid,$updateRole);       	
        }
        return $value;
	}	
	
	// 返回装备锻造所需强化次数
	public static function getDzStrict($equipLevel) {
		$strict = array(1=>3, 10=>6, 20=>9, 30=>12, 40=>15, 50=>18, 60=>21);
		return $strict[$equipLevel];
	}
	
	// 返回锻造所需消耗信息
	public static function getDzNeed($itemid, $equipLevel) {
		$need = array(
				1=>array('xt'=>1, 'odds'=>1, 'tq'=>100, 'attr'=>25, 'yb'=>0),
				10=>array('xt'=>2, 'odds'=>0.95, 'tq'=>500, 'attr'=>47, 'yb'=>5),
				20=>array('xt'=>3, 'odds'=>0.9, 'tq'=>1000, 'attr'=>80, 'yb'=>10),
				30=>array('xt'=>4, 'odds'=>0.85, 'tq'=>2000, 'attr'=>123, 'yb'=>15),
				40=>array('xt'=>5, 'odds'=>0.8, 'tq'=>4000, 'attr'=>176, 'yb'=>20),
				50=>array('xt'=>7, 'odds'=>0.75, 'tq'=>8000, 'attr'=>240, 'yb'=>25),
				60=>array('xt'=>10, 'odds'=>0.7, 'tq'=>15000, 'attr'=>313, 'yb'=>30)
		);
		
		// 获取所需图纸itemid
		$rule = array(
				100=>201,
				201=>202,
				202=>303,
				303=>304,
				304=>405,
				405=>406,			
				406=>507
		);
		
		$idx = substr($itemid, 2, 3);
		$prefix = substr($itemid, 0, 2);
		
		$returnValue['xt'] = $need[$equipLevel]['xt'];
		$returnValue['odds'] = $need[$equipLevel]['odds'];
		$returnValue['tq'] = $need[$equipLevel]['tq'];
		$returnValue['newEquip'] = $prefix . $rule[$idx];
		$returnValue['graph'] = '1' . $prefix[1] . $rule[$idx];
		$returnValue['nweAttr'] = $need[$equipLevel]['attr'];
		$returnValue['yb'] = $need[$equipLevel]['yb'];
		
		return $returnValue;
	}
	
	public static function qqdzxx($playersid, $djid) {
		global $_SGLOBAL, $common, $db, $mc, $tools_lang, $sys_lang;
	
		// 获取玩家背包所有道具
		$bagInfo = toolsModel::getMyItemInfo($playersid);
		
		// 如果玩家无道具信息或者道具不足
		if(empty($bagInfo) || !array_key_exists($djid, $bagInfo)) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}
		
		// 获取玩家背包中须锻造道具信息
		$itemInfo  = $bagInfo[$djid];
		
		// 获取道具的强化等级
		$qhLevel   = $itemInfo['QhLevel'];
		
		// 判断所选道具的类型
		$itemType  = $itemInfo['ItemType'];
		$equipType = $itemInfo['EquipType'];
		$itemID    = $itemInfo['ItemID'];
		$equipLevel = $itemInfo['LevelLimit'];
				
		if($equipLevel == 70) {
			$returnValue = array('status'=>1002, 'message'=>$tools_lang['model_msg_56']);
			return $returnValue;
		}
		
		// 非装备
		if($itemType != 4) {
			$returnValue = array('status'=>3, $tools_lang['model_msg_1']);
			return $returnValue;
		}
		
		// 非刀类武器
		if($equipLevel < 70 && $itemType == 4 && $equipType == 3) {
			if(substr($itemID, 0, 2) != '44') {
				$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_24']);
				return $returnValue;
			}
		}
		
		// 判断是否是专属武器
		$zswq_arr = array(LGBOW_ITEMID=>1, HALFMOON_ITEMID=>1, RUNWIND_ITEMID=>1, HITTIGER_ITEMID=>1, ARCHLORD_ITEMID=>1);
		if(array_key_exists($itemID, $zswq_arr)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['model_msg_57']);
			return $returnValue;
		}
		
		// 是否是圣诞套装、金蛇套装
		$sdtz_arr = array(41001=>1, 42001=>1, 43001=>1, 44001=>1, 44001=>1, 41002=>1, 42002=>1, 43002=>1, 44002=>1);
		if(array_key_exists($itemID, $sdtz_arr)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['model_msg_65']);
			return $returnValue;	
		}

		// 获取玄铁信息
		$itemRawInfo  = toolsModel::getItemInfo(XT_ITEMID);
		$commonName   = $itemRawInfo['Name'];
		$commonIcon   = $itemRawInfo['IconID'];
		$commSubtype  = $itemRawInfo['EquipType'];
		$commXyd 	  = intval($itemRawInfo['Rarity']);
		
		// 获取消耗信息
		$need = toolsModel::getDzNeed($itemID, $equipLevel);
		
		// 获取图纸信息
		$itemRawInfo  = toolsModel::getItemInfo($need['graph']);
		$tzName 	  = $itemRawInfo['Name'];
		$tzIcon  	  = $itemRawInfo['IconID'];
		$tzSubtype    = $itemRawInfo['EquipType'];
		$tzXyd 	  	  = intval($itemRawInfo['Rarity']);
		
		// 获取背包中玄铁的数量
		$xt_count = toolsModel::getItemCount($playersid, XT_ITEMID);
		
		// 获取背包中图纸的数量
		$tz_count = toolsModel::getItemCount($playersid, $need['graph']);
		
		// 获取所需强化次数
		$returnValue['status'] = 0;
		$returnValue['xz'] = toolsModel::getDzStrict($equipLevel);
		$returnValue['xytq'] = $need['tq'];
		$returnValue['clmc1'] = $commonName;
		//if($xt_count > 0) {
			$returnValue['clzlbid1'] = intval($commSubtype);
		//}
		$returnValue['tbid1'] = $commonIcon;
		$returnValue['clsl1'] = $need['xt'];
		
		$returnValue['clmc2'] = $tzName;
		//if($tz_count > 0) {
			$returnValue['clzlbid2'] = intval($tzSubtype);
		//}
		$returnValue['tbid2'] = $tzIcon;
		$returnValue['clsl2'] = 1;
		
		$returnValue['xyd1'] = $commXyd;
		$returnValue['xyd2'] = $tzXyd;
		
		// 获取锻造后装备的信息
		$itemRawInfo = toolsModel::getItemInfo($need['newEquip']);
		$newZbName = $itemRawInfo['Name'];		
		$newZbLevel = $itemRawInfo['LevelLimit'];
		$newZbIcon = $itemRawInfo['IconID'];
		$newZbRarity = $itemRawInfo['Rarity'];
		
		//$returnValue['xjsx'] = $need['nweAttr'];
		//$returnValue['xjjb'] = intval($newZbLevel);
		//$returnValue['xmc'] = $newZbName;
		//$returnValue['xtbid'] = $newZbIcon;
		//$returnValue['xxyd'] = intval($newZbRarity);
		$returnValue['cgl'] = $need['odds'] * 100;
		$returnValue['hyb'] = $need['yb'];
		
		return $returnValue;
	}
	
	// 过滤道具信息只保留基本道具背包信息
	public static function getBgItemBase(&$iteminfo) {
		unset($iteminfo['ItemType']);
		unset($iteminfo['EquipType']);
		unset($iteminfo['Name']);
		unset($iteminfo['Description']);
		unset($iteminfo['Rarity']);
		unset($iteminfo['LevelLimit']);
		unset($iteminfo['SellPirce']);
		unset($iteminfo['BuyPrice']);
		unset($iteminfo['DiejiaLimit']);
		unset($iteminfo['Attack_value']);
		unset($iteminfo['Defense_value']);
		unset($iteminfo['Physical_value']);
		unset($iteminfo['Agility_value']);
		unset($iteminfo['Addition_attack_value']);
		unset($iteminfo['Addition_defense_value']);
		unset($iteminfo['Addition_physical_value']);
		unset($iteminfo['Addition_agility_value']);
		unset($iteminfo['IsSuit']);
		unset($iteminfo['SuitID']);
		unset($iteminfo['ItemScript']);
		unset($iteminfo['ItemScript_Parameter']);
		unset($iteminfo['SortNo']);
		unset($iteminfo['Zhiye']);
		unset($iteminfo['IconID']);
		unset($iteminfo['IsMallItem']);
		unset($iteminfo['can_Qh']);
		unset($iteminfo['can_Dz']);
		unset($iteminfo['zbpz']);
		unset($iteminfo['fmsx']);
	}
	
	public static function dzzb($playersid, $djid, $hyb) {
		global $_SGLOBAL, $common, $db, $mc, $G_PlayerMgr, $tools_lang, $sys_lang;
		
		// 获取玩家背包所有道具		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);		
		
		// 获取玩家背包中须锻造道具信息
		$bagInfo = &$player->GetItems();
		$itemInfo = $bagInfo[$djid];
		
		$itemIproto  = ConfigLoader::GetItemProto($itemInfo['ItemID']);	
		
		// 如果玩家无道具信息或者道具不足
		if(empty($bagInfo) || !array_key_exists($djid, $bagInfo)) {
			$returnValue = array('status'=>3, $tools_lang['model_msg_1']);
			return $returnValue;
		}
		
		// 获取玩家背包中须锻造道具信息
		//$itemInfo  = $bagInfo[$djid];
		
		// 获取道具的强化等级
		$qhLevel   = $itemInfo['QhLevel'];
		
		// 判断所选道具的类型
		$itemType = $itemIproto['ItemType'];
		$equipType = $itemIproto['EquipType'];
		$itemID = $itemInfo['ItemID'];
		$equipLevel = $itemIproto['LevelLimit'];
		$isEquipeed = $itemInfo['IsEquipped'];
		
		if($equipLevel == 70) {
			$returnValue = array('status'=>1002, 'message'=>$tools_lang['model_msg_56']);
			return $returnValue;
		}
		
		// 非装备
		if($itemType != 4) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}
		
		// 非刀类武器
		if($equipLevel < 70 && $itemType == 4 && $equipType == 3) {
			if(substr($itemID, 0, 2) != '44') {
				$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_24']);
				return $returnValue;
			}
		}
		
		// 判断是否是专属武器
		$zswq_arr = array(LGBOW_ITEMID=>1, HALFMOON_ITEMID=>1, RUNWIND_ITEMID=>1, HITTIGER_ITEMID=>1, ARCHLORD_ITEMID=>1);
		if(array_key_exists($itemID, $zswq_arr)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['model_msg_57']);
			return $returnValue;
		}

		// 是否是圣诞套装、金蛇套装
		$sdtz_arr = array(41001=>1, 42001=>1, 43001=>1, 44001=>1, 44001=>1, 41002=>1, 42002=>1, 43002=>1, 44002=>1);
		if(array_key_exists($itemID, $sdtz_arr)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['model_msg_65']);
			return $returnValue;	
		}

		// 判断强化次数是否已足够
		$need_qh_level = toolsModel::getDzStrict($equipLevel);
		if($qhLevel < $need_qh_level) {
			$returnValue = array('status'=>998, 'message'=>"{$tools_lang['model_msg_58']}{$need_qh_level}{$tools_lang['model_msg_59']}");
			return $returnValue;
		}
		
		// 获取玄铁信息
		$itemRawInfo  = toolsModel::getItemInfo(XT_ITEMID);
		$commonName   = $itemRawInfo['Name'];
		$commonIcon   = $itemRawInfo['IconID'];
		$commSubtype  = $itemRawInfo['EquipType'];
		
		// 获取消耗信息
		$need = toolsModel::getDzNeed($itemID, $equipLevel);
		
		// 获取图纸信息
		$itemRawInfo = toolsModel::getItemInfo($need['graph']);
		$tzName = $itemRawInfo['Name'];
		$tzIcon = $itemRawInfo['IconID'];
		$tzSubtype = $itemRawInfo['EquipType'];
		
		// 获取背包中玄铁的数量
		$xt_count = toolsModel::getItemCount($playersid, XT_ITEMID);
		
		// 获取背包中图纸的数量
		$tz_count = toolsModel::getItemCount($playersid, $need['graph']);
		
		if($xt_count == 0 || ($xt_count < $need['xt'])) {
			$returnValue = array('status'=>1002, 'message'=>$tools_lang['model_msg_60']);
			return $returnValue;
		}
		
		if($tz_count == 0 || ($tz_count < 1)) {
			$returnValue = array('status'=>1002, 'message'=>"{$tzName}{$tools_lang['model_msg_61']}");
			return $returnValue;
		}
		
		// 获取锻造后装备的属性
		$itemRawInfo  = toolsModel::getItemInfo($need['newEquip']);
		
		// 如果此装备已装备，须判断锻造后的装备等级是否大于此武将的级别		
		if($isEquipeed) {
			$glevel = null;
			$genData = cityModel::getGeneralData($playersid,'','*');
			foreach ($genData as $k=>$v) {
				if($equipType == 1) {
					// 如果背包的wid等于装备位置记录的id
					if($v['helmet'] == $djid) {
						$glevel = intval($v['general_level']);
						break;
					}
				} else if($equipType == 2) {
					if($v['carapace'] == $djid) {
						$glevel = intval($v['general_level']);
						break;
					}
				} else if($equipType == 3) {
					if($v['arms'] == $djid) {
						$glevel = intval($v['general_level']);
						break;
					}
				} else if($equipType == 4) {
					if($v['shoes'] == $djid) {
						$glevel = intval($v['general_level']);
						break;
					}
				}
			}
	
			if($itemRawInfo['LevelLimit'] > $glevel) {
				$returnValue = array('status'=>1003, 'message'=>$tools_lang['model_msg_62']);
				return $returnValue;
			}
		}
				
		// 获取玩家铜钱、元宝数量
		$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);
		$currTQ = $roleInfo['coins'];
		$currYB = intval($roleInfo['ingot']);

		// 铜钱是否足够
		$syTq = $currTQ - $need['tq'];
		if($syTq < 0) {
			$returnValue['status'] = 58;
			$returnValue['xyxhtq'] = $need['tq'];
			$returnValue['message'] = $tools_lang['model_msg_53'];
		
			return $returnValue;
		}
		
		// 元宝是否足够
		if($hyb == 1) {
			$syYb = $currYB - $need['yb'];
			if($syYb < 0) {
				$returnValue['status'] = 88;
				$returnValue['yb'] = $currYB;
				$rep_arr1 = array('{xhyb}', '{yb}');
				$rep_arr2 = array($need['yb'], $currYB);		
				$returnValue['message'] = str_replace($rep_arr1, $rep_arr2, $sys_lang[2]);
		
				return $returnValue;
			} else {				
				$returnValue['yb'] = $syYb;
				$returnValue['xhyb'] = $need['yb'];
				$updateRole['ingot'] = $syYb;
			}
		}
		
		// 更新玩家当前铜钱数量
		$updateRole['coins'] = $syTq;
		/*$xwzt_20 = substr($roleInfo['xwzt'],19,1); 
		if ($xwzt_20 == 0) {
			$updateRole['xwzt'] = substr_replace($roleInfo['xwzt'],'1',19,1);
		}*/		
		$whereRole['playersid'] = $playersid;
		$common->updatetable('player', $updateRole, $whereRole);
		$common->updateMemCache(MC.$playersid, $updateRole);
		
		// 扣除玄铁和图纸	
		$player->DeleteItemByProto(array(XT_ITEMID=>$need['xt'], $need['graph']=>1));

		// 开始锻造
		// 失败几率
		$fodds = 1 - $need['odds'];
		
		// 生成概率数组
		$ps = array('success'=>$need['odds'], 'faild'=>$fodds);					
		
		// 获取概率结果
		$spResult = toolsModel::random($ps);
		if(($spResult == 'success') || ($hyb == 1)) {
			$returnValue['status'] = 0;
		} else {
			$returnValue['status'] = 1001;
			$returnValue['message']= $tools_lang['model_msg_63'];
		}
		
		// 更新装备属性信息
		if($returnValue['status'] == 0) {				
			$roleInfo['rwsl'] = $itemRawInfo['Rarity'];
			$cjInfo['djid'] = $itemRawInfo['ItemID'];
			achievementsModel::check_achieve($playersid,$cjInfo,array('dz'));
			toolsModel::getBgItemBase($itemRawInfo);
			
			// 更新新装备的强化等级					
			$itemRawInfo['QhLevel'] = $need_qh_level;		
			$player->ModifyItem($djid, $itemRawInfo);
			 
			$rwid = questsController::OnFinish($roleInfo,"'zbdz'");  //锻造任务
		    if (!empty($rwid)) {
		         $returnValue['rwid'] = $rwid;				             
		    }	    

			$bagData = $player->GetClientBag();			
		} else {		

			$bagData = $player->GetClientBag();
		}
		
		// 设置返回值
		$returnValue['tq'] = $syTq;
		$returnValue['xhtq'] = $need['tq'];
		$returnValue['djid'] = $djid;
		$returnValue['list'] = $bagData;
				
		// 返回cdata数据		
		$cdata = toolsModel::getBgItem($playersid, $djid);
		if(isset($cdata['gid'])) {
			//$returnValue['gid'] = $cdata['gid'];
			// 锻造成功时
			if($returnValue['status'] == 0) {
				// 返回将领信息
				$getInfo['playersid'] = $playersid;
				$ginfo = cityModel::getGeneralList($getInfo, 0, false ,$cdata['gid']);
				$returnValue['ginfo'] = $ginfo['generals'];
			}
			unset($cdata['gid']);
		}
		$returnValue['cdata'] = $cdata;		
		// 返回下次锻造信息
		//$ret = toolsModel::qqdzxx($playersid, $djid);
		//unset($ret['status']);
		//unset($ret['message']);
		//$returnValue = array_merge($returnValue, $ret);
				
		return $returnValue;
	}
}