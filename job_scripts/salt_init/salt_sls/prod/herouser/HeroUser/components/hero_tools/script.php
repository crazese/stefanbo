<?php
class useItemByParam {
	public static function getItemUseMethod($param, $playersid, $djid, $playerBag) {
		$method = 'itemUse_' . $param;
		return useItemByParam::$method($playersid, $djid, $playerBag);
	}
	
	// 扩包道具
	public static function itemUse_1($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
			
		$playerBag = &$player->GetItems();
		
		$subtract = 0;
		
		// 获取玩家背包格数
		$currBlocks = toolsModel::getBagTotalBlocks($playersid);
		
		if($currBlocks >= 120) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['model_msg_5']);
			return $returnValue;	
		}
		
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;	
		}
		
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>1021, 'message'=>$tools_lang['model_msg_7']);					
			return $returnValue;
		} 	
		//echo "ec:" .intval($playerBag[$djid]['EquipCount']);
		if( intval($playerBag[$djid]['EquipCount']) >= 1 ) {
			// 处理玩家道具数量信息
			$whereItem['playersid'] = $playersid;
			$whereItem['ID'] = $djid;
			$subtract = intval($playerBag[$djid]['EquipCount']) - 1;
			if( $subtract > 0 ) {
				// 更新玩家当前扩包道具数量
				$updateItem['EquipCount'] = $subtract;					
				$common->updatetable('player_items',$updateItem,$whereItem);
				// 更新mc中玩家道具信息
				$playerBag[$djid]['EquipCount'] = $subtract;
			} else {
				// 删除此道具
				$common->deletetable('player_items', $whereItem);
				unset($playerBag[$djid]);
			}
			// 更新mc
			$mc->set(MC.'items_'.$playersid, $playerBag, 0, 3600);
			
			// 增加背包格数
			$currBlocks = $currBlocks + 1;					
			$updateRole['bagpack'] =  $currBlocks;
			$whereRole['playersid'] = $playersid;
			$common->updatetable('player', $updateRole, $whereRole);  
			$common->updateMemCache(MC.$playersid, $updateRole);
			
 			$roleInfo = array('playersid'=>$playersid);		
			roleModel::getRoleInfo($roleInfo);	
						
			// 使用道具任务相关
			$roleInfo['itemId'] = KBDJ;
		   	$rwid = questsController::OnFinish($roleInfo, "'sydj'");
	        if (!empty($rwid)) {
	            if (!empty($rwid)) {
	            	$returnValue['rwid'] = $rwid;
	            } 
	        }
			
			// 获取背包数据
			//$simBg = $playerBag = null;
	        //$bagData = toolsModel::getBglist($simBg, $playersid, $playerBag);
			$bagData = $player->GetClientBag();
			
			$returnValue = array('status'=>0, 'message'=>$tools_lang['script_msg_80'],'gs'=>$currBlocks, 'type'=>2, 'list'=>$bagData);
		}
		
		//if(_get('all') == 1)
			//$returnValue['hqdj'] = array(0=>array($newitemName, 1));

		return $returnValue;
	}
	
	// 勇士礼包
	public static function itemUse_6($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
					
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;	
		}
				
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_83']);
			return $returnValue;
		} 	
			
		// 获取道具信息				
		$myItemInfo = $playerBag[$djid];		
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		
		$ypbId = 0;
		$ypsl = 0;
		$itemName = null;
		$itemDescr = null;
		if($myItemInfo['ItemID'] == YSLB1_ITEMID) {
			$ypsl = 30;
			$ypbId = YSLB1_ITEMID;
			$itemName = $itemproto['Name'];
			$itemDescr = $itemproto['Description'];
		} else if($myItemInfo['ItemID'] == YSLB15_ITEMID) {
			$ypsl = 50;
			$ypbId = YSLB15_ITEMID;
			$itemName = $itemproto['Name'];
			$itemDescr = $itemproto['Description'];
		} else if($myItemInfo['ItemID'] == YSLB30_ITEMID) {
			$ypsl = 75;
			$ypbId = YSLB30_ITEMID;
			$itemName = $itemproto['Name'];
			$itemDescr = $itemproto['Description'];
		} else if($myItemInfo['ItemID'] == YSLB45_ITEMID) {
			$ypsl = 100;
			$ypbId = YSLB45_ITEMID;
			$itemName = $itemproto['Name'];
			$itemDescr = $itemproto['Description'];
		} else {
			$ypsl = 200;
			$ypbId = YSLB60_ITEMID;
			$itemName = $itemproto['Name'];
			$itemDescr = $itemproto['Description'];
		}
		
		$newYsbId = null;
		$levelLimit = null;
		$newitemName = null;		
		if($ypbId == YSLB1_ITEMID) {
			$newYsbId = YSLB15_ITEMID;			
			$levelLimit = 1;
		} else if($ypbId == YSLB15_ITEMID) {
			$newYsbId = YSLB30_ITEMID;			
			$levelLimit = 15;
		} else if($ypbId == YSLB30_ITEMID) {
			$levelLimit = 30;
			$newYsbId = YSLB45_ITEMID;			
		}  else if($ypbId == YSLB45_ITEMID) {
			$levelLimit = 45;
			$newYsbId = YSLB60_ITEMID;			
		} else {
			$levelLimit = 60;
		}
		if($newYsbId != null) {
			$newitemproto = ConfigLoader::GetItemProto($newYsbId);
			$newitemName = $newitemproto['Name'];
		}
		
		// 获取玩家信息
		$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);
		if($roleInfo['player_level'] < $levelLimit) {
			$returnValue['status'] = 998;
			$returnValue['message'] = $itemDescr;
			return $returnValue;
		}

		// 玩家背包剩余格数
		$syBlocks = toolsModel::getBgSyGs($playersid);
		if($syBlocks < 1) {
			$returnValue['status'] = 998;
			$returnValue['message'] = $tools_lang['controller_msg_65'];
			return $returnValue;
		}
		
		if($levelLimit < 60) {
			// 加入玩家背包删除老道具
			$addRet = $player->AddItems(array($newYsbId=>1), false, array($ypbId=>1));			
		} else {
			$player->DeleteItemByProto(array($ypbId=>1));
		}
		
		if($levelLimit == 60 || $addRet !== false) {
			$returnValue['status'] = 0;	
			if($levelLimit < 60) {
				$returnValue['message'] = "{$tools_lang['script_msg_84']}{$itemName}，{$tools_lang['controller_msg_67']}{$ypsl}{$tools_lang['script_msg_85']}{$newitemName}。";
			} else {
				$returnValue['message'] = "{$tools_lang['script_msg_84']}{$itemName}，{$tools_lang['controller_msg_67']}{$ypsl}{$tools_lang['script_msg_86']}";
			}
						
			$questItemID = $ypbId;
			$roleInfo['itemId'] = $questItemID;
		   	$rwid = questsController::OnFinish($roleInfo, "'sydj'");
		    if (!empty($rwid)) {
			    if (!empty($rwid)) {
		    	   	$returnValue['rwid'] = $rwid;
		        } 
		    }		
				
			// 获取玩家{$tools_lang['controller_msg_69']}数量
			$currYP = $roleInfo['silver'];		
			
			// 更新玩家当前{$tools_lang['controller_msg_69']}数量
			$updateRole['silver'] = $currYP + $ypsl;		
			$whereRole['playersid'] = $playersid;
			$common->updatetable('player', $updateRole, $whereRole); 
			$common->updateMemCache(MC.$playersid, $updateRole);
			
			$returnValue['yp'] = $updateRole['silver'];
			$returnValue['hqyp'] = $ypsl;
			
			// 删除勇士礼包
			//$delRet = toolsModel::deleteBags($playersid, array($ypbId=>1));
			
			// 获取背包数据
			$bagData = $player->GetClientBag();
			$returnValue['list'] = $bagData;
			
			if(_get('all') == 1 && $newitemName != null)
				$returnValue['hqdj'] = array(0=>array($newitemName, 1));
		}
		return $returnValue;
	}
	
	// 大中小铜钱包
	public static function itemUse_7($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;	
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);

		// 获取道具信息		
		$myItemInfo = $playerBag[$djid];		
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);

		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>"您没有{$itemproto['Name']}！");
			return $returnValue;
		} 	
			
		// 获取铜钱包规则
		$tqbRule = getTqbRule();
		$tqbId = 0;
		$tqsl = 0;
		$itemName = null;
		if($myItemInfo['ItemID'] == BIGTQB_ITEMID) {
			$tqsl = rand($tqbRule[BIGTQB_ITEMID]['min'], $tqbRule[BIGTQB_ITEMID]['max']);
			$tqbId = BIGTQB_ITEMID;
			$itemName = $itemproto['Name'];
		} else if($myItemInfo['ItemID'] == MIDTQB_ITEMID) {
			$tqsl = rand($tqbRule[MIDTQB_ITEMID]['min'], $tqbRule[MIDTQB_ITEMID]['max']);
			$tqbId = MIDTQB_ITEMID;
			$itemName = $itemproto['Name'];
		} else {
			$tqsl = rand($tqbRule[SMALLTQB_ITEMID]['min'], $tqbRule[SMALLTQB_ITEMID]['max']);
			$tqbId = SMALLTQB_ITEMID;
			$itemName = $itemproto['Name'];
		}
		
		// 获取玩家铜钱、元宝数量
		$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);		
		$currTQ = $roleInfo['coins'];		
			
		// 更新玩家当前铜钱数量		
		$updateRole['coins'] = intval($currTQ) + intval($tqsl);
		if($updateRole['coins'] > COINSUPLIMIT) {
			$updateRole['coins'] = COINSUPLIMIT;
			$tqsl = COINSUPLIMIT - intval($currTQ);
		}		
		$whereRole['playersid'] = $playersid;
		$common->updatetable('player', $updateRole, $whereRole); 
		$common->updateMemCache(MC.$playersid, $updateRole);
		
		$returnValue['tq'] = $updateRole['coins'];
		$returnValue['hqtq'] = $tqsl;
			
		// 删除铜钱包		
		$player->DeleteItemByProto(array($tqbId=>1));
			
		$returnValue['status'] = 0;	
		$rep_arr1 = array('{itemName}', '{tqsl}');
		$rep_arr2 = array($itemName, $tqsl);
		$returnValue['message'] = str_replace($rep_arr1, $rep_arr2, $tools_lang['script_msg_87']);
		
		$roleInfo['itemId'] = $tqbId;
	   	$rwid = questsController::OnFinish($roleInfo, "'sydj'");
        if (!empty($rwid)) {
            if (!empty($rwid)) {
            	$returnValue['rwid'] = $rwid;
            } 
        }	
		
		// 获取背包数据        
        $bagData = $player->GetClientBag();
		$returnValue['list'] = $bagData;
		
		return $returnValue;
	}
	
	// 大中小军粮包
	public static function itemUse_8($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $_SGLOBAL, $G_PlayerMgr, $tools_lang, $sys_lang;
		
		$nowTime = $_SGLOBAL['timestamp'];
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;	
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
		
		// 获取道具信息		
		$myItemInfo = $playerBag[$djid];		
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>"您没有{$itemproto['Name']}！");
			return $returnValue;
		} 	
		
		// 获取军粮包规则
		$jlbRule = getJlbRule();
		$jlbId = 0;
		$jlsl = 0;
		$itemName = null;
		if($myItemInfo['ItemID'] == BIGJLB_ITEMID) {
			$jlsl = rand($jlbRule[BIGJLB_ITEMID]['min'], $jlbRule[BIGJLB_ITEMID]['max']);
			$jlbId = BIGJLB_ITEMID;
			$itemName = $itemproto['Name'];
		} else if($myItemInfo['ItemID'] == MIDJLB_ITEMID) {
			$jlsl = rand($jlbRule[MIDJLB_ITEMID]['min'], $jlbRule[MIDJLB_ITEMID]['max']);
			$jlbId = MIDJLB_ITEMID;
			$itemName = $itemproto['Name'];
		} else {
			$jlsl = rand($jlbRule[SMALLJLB_ITEMID]['min'], $jlbRule[SMALLJLB_ITEMID]['max']);
			$jlbId = SMALLJLB_ITEMID;
			$itemName = $itemproto['Name'];
		}
		
		// 获取玩家军粮数量
		$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);		
		cityModel::resourceGrowth($roleInfo);
		$currJL = $roleInfo['food'];		
			
		// 更新玩家当前军粮数量
		$updateRole['food'] = intval($currJL) + intval($jlsl);	
		$updateRole['last_update_food'] = $nowTime; 	
		$whereRole['playersid'] = $playersid;
		$common->updatetable('player', $updateRole, $whereRole); 
		$common->updateMemCache(MC.$playersid, $updateRole);
		
		$returnValue['jl'] = floor($updateRole['food']);
		$returnValue['hqjl'] = $jlsl;
			
		// 删除军粮包		
		$player->DeleteItemByProto(array($jlbId=>1));
		
		$returnValue['status'] = 0;
		$rep_arr1 = array('{itemName}', '{jlsl}');
		$rep_arr2 = array($itemName, $jlsl);
		$returnValue['message'] = str_replace($rep_arr1, $rep_arr2, $tools_lang['script_msg_88']);
		
		$roleInfo['itemId'] = $jlbId;
		$rwid = questsController::OnFinish($roleInfo, "'sydj'");
        if (!empty($rwid)) {
            if (!empty($rwid)) {
            	$returnValue['rwid'] = $rwid;
            } 
        }	
			
		// 获取背包数据		        
        $bagData = $player->GetClientBag();
		$returnValue['list'] = $bagData;
		
		return $returnValue;
	}
	
	
	// 强化材料包
	public static function itemUse_9($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;	
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
		
		// 获取道具信息		
		$myItemInfo = $playerBag[$djid];		
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);

		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>"您没有{$itemproto['Name']}！");
			return $returnValue;
		}

		// 获取道具信息
		$myItemInfo = $playerBag[$djid];
				
		// 获取强化材料包规则		
		$qhbRule = getQhbRule();
		$qhbId = 0;
		$qhclId = 0;
		$qhclsl = 0;
		$itemName = null;
		if($myItemInfo['ItemID'] == BIGQHB_ITEMID) {
			// 计算出现金刚石和矿工锄的概率
			$fodds = 1 - 0.98;
			$ps = array('jgs'=>0.98, 'kgc'=>$fodds);										
			$spResult = toolsModel::random($ps);
			
			$qhbId = BIGQHB_ITEMID;
			$itemName = $itemproto['Name'];
			
			if($spResult == 'jgs') {				
				$qhclsl = rand($qhbRule[BIGQHB_ITEMID]['number']['min'], $qhbRule[BIGQHB_ITEMID]['number']['max']);
				$qhclId = toolsModel::random($qhbRule[BIGQHB_ITEMID]['info']);				
			} else {				
				$qhclsl = 5;
				$qhclId = KGC_ITEMID;				
			}
		} else if($myItemInfo['ItemID'] == MIDQHB_ITEMID) {
			$fodds = 1 - 0.9;
			$ps = array('jgs'=>0.9, 'kgc'=>$fodds);										
			$spResult = toolsModel::random($ps);
			
			$qhbId = MIDQHB_ITEMID;
			$itemName = $itemproto['Name'];
			
			if($spResult == 'jgs') {			
				$qhclsl = rand($qhbRule[MIDQHB_ITEMID]['number']['min'], $qhbRule[MIDQHB_ITEMID]['number']['max']);			
				$qhclId = toolsModel::random($qhbRule[MIDQHB_ITEMID]['info']);
			} else {
				$qhclsl = 3;
				$qhclId = KGC_ITEMID;			
			}
		} else {
			$fodds = 1 - 0.8;
			$ps = array('jgs'=>0.8, 'kgc'=>$fodds);										
			$spResult = toolsModel::random($ps);
			
			$qhbId = SMALLQHB_ITEMID;
			$itemName = $itemproto['Name'];
						
			if($spResult == 'jgs') {
				$qhclsl = rand($qhbRule[SMALLQHB_ITEMID]['number']['min'], $qhbRule[SMALLQHB_ITEMID]['number']['max']);
				$qhclId = toolsModel::random($qhbRule[SMALLQHB_ITEMID]['info']);
			} else {
				$qhclsl = 1;
				$qhclId = KGC_ITEMID;				
			}
		}
		
		$qhclInfo = toolsModel::getItemInfo($qhclId);
		
		// 加入玩家背包
		$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);
	
		$addRet = $player->AddItems(array($qhclId=>$qhclsl), false, array($myItemInfo['ItemID']=>1));

		if($addRet === false) {
			$returnValue['status'] = 998;
			$returnValue['message'] = $tools_lang['controller_msg_65'];
			return $returnValue;
		} 
						
		$returnValue['status'] = 0;	
		$rep_arr1 = array('{itemName}', '{qhclsl}', '{Name}');
		$rep_arr2 = array($itemName, $qhclsl, $qhclInfo['Name']);
		$returnValue['message'] = str_replace($rep_arr1, $rep_arr2, $tools_lang['script_msg_89']);
		
		$roleInfo['itemId'] = $qhbId;
		$rwid = questsController::OnFinish($roleInfo, "'sydj'");
		if (!empty($rwid)) {
			if (!empty($rwid)) {
				$returnValue['rwid'] = $rwid;
			} 
		}	

		$returnValue['list'] = $player->GetClientBag();
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = array(0=>array($qhclInfo['Name'], $qhclsl));		
		
		return $returnValue;
	}

	// 建筑材料包
	public static function itemUse_10($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;	
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
		
		// 获取道具信息
		$myItemInfo = $playerBag[$djid];
		$itemproto = ConfigLoader::GetItemProto($myItemInfo['ItemID']);
		
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>"您没有{$itemproto['Name']}！");
			return $returnValue;
		}
		
		if(toolsModel::getBgSyGs($playersid) == 0) {
			$returnValue['status'] = 998;
			$returnValue['message'] = $tools_lang['controller_msg_65'];
			return $returnValue;		
		}
		
		// 获取建筑材料包规则		
		$jzbRule = getJzbRule();
		$jzbId = 0;
		$jzclId = 0;
		$jzclsl = 0;
		$itemName = null;
		if($myItemInfo['ItemID'] == BIGJZB_ITEMID) {
			$jzbId = BIGJZB_ITEMID;
			$jzclsl = rand($jzbRule[BIGJZB_ITEMID]['number']['min'], $jzbRule[BIGJZB_ITEMID]['number']['max']);
			$jzclId = toolsModel::random($jzbRule[BIGJZB_ITEMID]['info']);
			$itemName = $itemproto['Name'];
		} else if($myItemInfo['ItemID'] == MIDJZB_ITEMID) {
			$jzbId = MIDJZB_ITEMID;
			$jzclsl = rand($jzbRule[MIDJZB_ITEMID]['number']['min'], $jzbRule[MIDJZB_ITEMID]['number']['max']);
			$jzclId = toolsModel::random($jzbRule[MIDJZB_ITEMID]['info']);
			$itemName = $itemproto['Name'];
		} else {
			$jzbId = SMALLJZB_ITEMID;
			$jzclsl = rand($jzbRule[SMALLJZB_ITEMID]['number']['min'], $jzbRule[SMALLJZB_ITEMID]['number']['max']);
			$jzclId = toolsModel::random($jzbRule[SMALLJZB_ITEMID]['info']);
			$itemName = $itemproto['Name'];
		}
		
		$jzclInfo = toolsModel::getItemInfo($jzclId);
		
		// 加入玩家背包
		$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);					
		$addRet = $player->AddItems(array($jzclId=>$jzclsl), false, array($jzbId=>1));
		if($addRet === false) {
			$returnValue['status'] = 998;
			$returnValue['message'] = $tools_lang['controller_msg_65'];
			return $returnValue;
		} 
		
		if($addRet !== false) {
			$returnValue['status'] = 0;
			$rep_arr1 = array('{itemName}', '{qhclsl}', '{Name}');
			$rep_arr2 = array($itemName, $jzclsl, $jzclInfo['Name']);			
			$returnValue['message'] = str_replace($rep_arr1, $rep_arr2, $tools_lang['script_msg_89']);;
			$roleInfo['itemId'] = $jzbId;
			$rwid = questsController::OnFinish($roleInfo, "'sydj'");
	        if (!empty($rwid)) {
	            if (!empty($rwid)) {
	            	$returnValue['rwid'] = $rwid;
	            } 
	        }	        
							
			// 获取背包数据						
			$bagData = $player->GetClientBag();
			$returnValue['list'] = $bagData;
			
			if(_get('all') == 1)
				$returnValue['hqdj'] = array(0=>array($jzclInfo['Name'], $jzclsl));
		}	
		
		return $returnValue;
	}
	
	// 大中小银票包
	public static function itemUse_12($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;	
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
		
		// 获取道具信息
		$myItemInfo = $playerBag[$djid];
		$itemproto = ConfigLoader::GetItemProto($myItemInfo['ItemID']);
		
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>"您没有{$itemproto['Name']}！");
			return $returnValue;
		} 	
		
		// 获取银票包规则
		$ypbRule = getYpbRule();
		$ypbId = 0;
		$ypsl = 0;
		$itemName = null;
		if($myItemInfo['ItemID'] == BIGYPB_ITEMID) {
			$ypsl = $ypbRule[BIGYPB_ITEMID];//rand($ypbRule[BIGYPB_ITEMID]['min'], $ypbRule[BIGYPB_ITEMID]['max']);
			$ypbId = BIGYPB_ITEMID;
			$itemName = $itemproto['Name'];
		} else if($myItemInfo['ItemID'] == MIDYPB_ITEMID) {
			$ypsl = $ypbRule[MIDYPB_ITEMID];//rand($ypbRule[MIDYPB_ITEMID]['min'], $ypbRule[MIDYPB_ITEMID]['max']);
			$ypbId = MIDYPB_ITEMID;
			$itemName = $itemproto['Name'];
		} else if($myItemInfo['ItemID'] == SUPERYPB_ITEMID) {
			$ypsl = $ypbRule[SUPERYPB_ITEMID];
			$ypbId = SUPERYPB_ITEMID;
			$itemName = $itemproto['Name'];
		} else {
			$ypsl = $ypbRule[SMALLYPB_ITEMID];//rand($ypbRule[SMALLYPB_ITEMID]['min'], $ypbRule[SMALLYPB_ITEMID]['max']);
			$ypbId = SMALLYPB_ITEMID;
			$itemName = $itemproto['Name'];
		}
		
		// 获取玩家银票数量
		$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);		
		$currYP = $roleInfo['silver'];		
			
		// 更新玩家当前银票数量
		$updateRole['silver'] = intval($currYP) + intval($ypsl);	
		if($updateRole['silver'] > SILVERUPLIMIT) {
			$updateRole['silver'] = SILVERUPLIMIT;
			$ypsl = SILVERUPLIMIT - intval($currYP);
		}	
		$whereRole['playersid'] = $playersid;
		$common->updatetable('player', $updateRole, $whereRole); 
		$common->updateMemCache(MC.$playersid, $updateRole);
		
		$returnValue['yp'] = $updateRole['silver'];
		$returnValue['hqyp'] = $ypsl;
			
		// 删除银票包		
		$player->DeleteItemByProto(array($ypbId=>1));
			
		$returnValue['status'] = 0;	
		$rep_arr1 = array('{itemName}', '{ypsl}');
		$rep_arr2 = array($itemName, $ypsl);
		$returnValue['message'] = str_replace($rep_arr1, $rep_arr2, $tools_lang['script_msg_90']);
		
		$roleInfo['itemId'] = $ypbId;
	   	$rwid = questsController::OnFinish($roleInfo, "'sydj'");
        if (!empty($rwid)) {
            if (!empty($rwid)) {
            	$returnValue['rwid'] = $rwid;
            } 
        }	
		
		$returnValue['list'] = $player->GetClientBag();
		
		return $returnValue;
	}
	
	// 成长礼包
	public static function itemUse_14($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);

		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_91']);
			return $returnValue;
		}		
		
		$ypbId = 0;
		$ypsl = 0;
		$tqsl = 0;
		$ybsl = 0;
		$getitemArr = null;
		$czlbDescr = null;
		
		// 获取道具信息		
		$myItemInfo = $playerBag[$djid];
		$itemproto = ConfigLoader::GetItemProto($myItemInfo['ItemID']);
		$levelLimit = $itemproto['LevelLimit'];
		$czlbDescr = $itemproto['Name'];
		$ypbId = $myItemInfo['ItemID'];
				
		// 获取礼包规则
		$tmpstr = explode('|', $itemproto['Description']);
		$czlbRule = json_decode($tmpstr[1], true);
		
		$newYsbId = null;
		foreach($czlbRule as $key=>$value) {
			if(intval($key) == 1 ) { // 下级礼包ID
				$newYsbId = $value;
			} else if(intval($key) == 2 ) { // 铜钱				
				$tqsl = intval($value);
			} else if(intval($key) == 3 ) { // 银票
				$ypsl = intval($value);
			} else if(intval($key) == 4 ) { // 元宝
				$ybsl = intval($value);
			} else if(intval($key) == 5 ) { // 增加的道具数组
				$getitemArr = $value;		
			} 	
		}
		
		// 合并下级成长礼包和要添加的道具数组
		if($newYsbId != null) {
			$getitemArr[$newYsbId] = 1;
		}
		
		//$common->insertLog(json_encode($getitemArr));
			
		// 获取玩家信息
		$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);
		
		// 删除此等级成长礼包
		//unset($playerBag[$djid]);		
		// 等级是否足够
		if($roleInfo['player_level'] < $levelLimit) {
			$returnValue['status'] = 998;		
			$returnValue['message'] = $tools_lang['script_msg_92'];
			return $returnValue;
		}
		
		// 模拟添加道具到背包		
		$addRet = $player->AddItems($getitemArr, false, array($ypbId=>1));
				
		if($addRet === false) {
			$returnValue['status'] = 998;
			$returnValue['message'] = $tools_lang['controller_msg_65'];			
			return $returnValue;
		}
		
		// 获取玩家银票数量
		$currYP = $roleInfo['silver'];
		$currYB = $roleInfo['ingot'];
		$currTQ = $roleInfo['coins'];
		
		$msg = "{$tools_lang['script_msg_84']}{$czlbDescr}，{$tools_lang['controller_msg_67']}";
		
		// 加入玩家背包
		if($ypsl > 0) {
			$msg .= "{$ypsl}{$tools_lang['model_msg_17']}{$tools_lang['controller_msg_69']}，";
			$updateRole['silver'] = $currYP + $ypsl;
							
			$returnValue['yp'] = $updateRole['silver'];
			$returnValue['hqyp'] = $ypsl;
		}
		
		if($tqsl > 0) {
			$msg .= "{$tqsl}{$tools_lang['model_msg_17']}{$tools_lang['controller_msg_68']}，";
			$updateRole['coins'] = $currTQ + $tqsl;
			
			$returnValue['tq'] = $updateRole['coins'];
			$returnValue['hqtq'] = $tqsl;
		}
		
		if($ybsl > 0) {
			$msg .= "{$ybsl}{$tools_lang['model_msg_17']}{$tools_lang['controller_msg_71']}";
			$updateRole['ingot'] = $currYB + $ybsl;
			
			$returnValue['yb'] = $updateRole['ingot'];
			$returnValue['hqyb'] = $ybsl;
		}		
		
		// 更新玩家当前银票数量		
		$whereRole['playersid'] = $playersid;
		$common->updatetable('player', $updateRole, $whereRole);
		$common->updateMemCache(MC.$playersid, $updateRole);
		
		$returnValue['status'] = 0;
		
		$hqdjArr = array();
		$first = true;
		foreach($getitemArr as $k=>$v) {
			$newItemInfo = toolsModel::getItemInfo($k);
			$newItemName = $newItemInfo['Name'];
			
			$hqdjArr[] = array($newItemName, $v);
			
			if($ybsl == 0 && $first) {				
				$msg .= "{$v}{$tools_lang['model_msg_17']}{$newItemName}";
				$first = false;
			} else {
				$msg .= "，{$v}{$tools_lang['model_msg_17']}{$newItemName}";
			}	
		}

		$questItemID = $ypbId;
		$roleInfo['itemId'] = $questItemID;
		$rwid = questsController::OnFinish($roleInfo, "'sydj'");
		if (!empty($rwid)) {
			if (!empty($rwid)) {
				$returnValue['rwid'] = $rwid;
			}
		}	

		$returnValue['message'] = $msg;
			
		// 获取背包数据
		$bagData = $player->GetClientBag();
		$returnValue['list'] = $bagData;
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = $hqdjArr;

		return $returnValue;
	}
	
	// 大中小玄铁包
	public static function itemUse_15($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);

		// 获取道具信息
		$myItemInfo = $playerBag[$djid];
		$itemproto = ConfigLoader::GetItemProto($myItemInfo['ItemID']);
		
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>"您没有{$itemproto['Name']}！");
			return $returnValue;
		}
	
		// 获取玄铁包规则
		$xtbRule = getXtbRule();
		$xtbId = 0;
		$xtsl = 0;
		$itemName = null;
		if($myItemInfo['ItemID'] == BIGXTB_ITEMID) {
			$xtsl = $xtbRule[BIGXTB_ITEMID];//rand($ypbRule[BIGYPB_ITEMID]['min'], $ypbRule[BIGYPB_ITEMID]['max']);
			$xtbId = BIGXTB_ITEMID;
			$itemName = $itemproto['Name'];
		} else if($myItemInfo['ItemID'] == MIDXTB_ITEMID) {
			$xtsl = $xtbRule[MIDXTB_ITEMID];//rand($ypbRule[MIDYPB_ITEMID]['min'], $ypbRule[MIDYPB_ITEMID]['max']);
			$xtbId = MIDXTB_ITEMID;
			$itemName = $itemproto['Name'];
		} else {
			$xtsl = $xtbRule[SMALLXTB_ITEMID];//rand($ypbRule[SMALLYPB_ITEMID]['min'], $ypbRule[SMALLYPB_ITEMID]['max']);
			$xtbId = SMALLXTB_ITEMID;
			$itemName = $itemproto['Name'];
		}
	
		// 检查玩家背包是否足够		
		$addRet = $player->AddItems(array(XT_ITEMID=>$xtsl), false, array($xtbId=>1));
		
		if($addRet === false) {
			$returnValue['status'] = 998;
			$returnValue['message'] = $tools_lang['controller_msg_65'];
			return $returnValue;
		}
				
		// 更新玩家当前玄铁数量
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);		
					
		$returnValue['status'] = 0;
		$rep_arr1 = array('{itemName}', '{xtsl}');
		$rep_arr2 = array($itemName, $xtsl);
		$returnValue['message'] = str_replace($rep_arr1, $rep_arr2, $tools_lang['script_msg_93']);
	
		$roleInfo['itemId'] = $xtbId;
		$rwid = questsController::OnFinish($roleInfo, "'sydj'");
		if (!empty($rwid)) {
			if (!empty($rwid)) {
				$returnValue['rwid'] = $rwid;
			}
		}
	
		// 获取背包数据
		$bagData = $player->GetClientBag();		
		$returnValue['list'] = $bagData;
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = array(0=>array($tools_lang['script_msg_94'], $xtsl));
	
		return $returnValue;
	}
	
	// 粽子
	public static function itemUse_16($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);			

		$itemInfo = $playerBag[$djid];
		$itemproto = ConfigLoader::GetItemProto($itemInfo['ItemID']);
		$itemName = $itemproto['Name'];
		
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>"您没有{$itemName}！");
			return $returnValue;
		}
	
		// 生成概率数组
		$ps = array(CGCARD_ITEMID=>0.4,
					BLUECARD_ITEMID=>0.3,
					KBDJ=>0.2,
					ZISECARD_ITEMID=>0.1);
	
		// 获取概率结果
		$spResult = toolsModel::random($ps);

		// 获取道具信息
		$newItmeInfo = toolsModel::getItemInfo($spResult);
		$newItemName = $newItmeInfo['Name'];	
		$itemCnt = $spResult == KBDJ ? 3 : 1;

		// 模拟删除一个粽子
		$addRet = $player->AddItems(array($spResult=>$itemCnt), false, array(ZONGZI_ITEMID=>1));
				
		// 检查玩家背包是否足够	
		if($addRet === false) {
			$returnValue['status'] = 998;
			$returnValue['message'] = $tools_lang['controller_msg_65'];
			return $returnValue;
		}		
			
		// 更新玩家当前获得道具数量
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);				
			
		$returnValue['status'] = 0;
		$rep_arr1 = array('{itemName}', 'qhclsl', 'Name');
		$rep_arr2 = array($itemName, $itemCnt, $newItemName);		
		$returnValue['message'] = str_replace($rep_arr1, $rep_arr2, $tools_lang['script_msg_89']);
	
		$roleInfo['itemId'] = ZONGZI_ITEMID;
		$rwid = questsController::OnFinish($roleInfo, "'sydj'");
		if (!empty($rwid)) {
			if (!empty($rwid)) {
				$returnValue['rwid'] = $rwid;
			}
		}
	
		// 获取背包数据
		$bagData = $player->GetClientBag();
		$returnValue['list'] = $bagData;
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = array(0=>array($newItemName, $itemCnt));
	
		return $returnValue;
	}
	
	// 闯关卡
	public static function itemUse_17($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
		
		$itemInfo = $playerBag[$djid];
		$itemproto = ConfigLoader::GetItemProto($itemInfo['ItemID']);
		$itemName = $itemproto['Name'];
	
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>"您没有{$itemName}！");
			return $returnValue;
		}
		
		// 获取玩家闯关进度
		if (!($cgRecord = $mc->get(MC.'stageInfo_'.$playersid))) {
			$cgRecord = $db->fetch_array($db->query("SELECT * FROM ".$common->tname('player_stage')." WHERE `playersid` = '$playersid' limit 1"));
			$mc->set(MC.'stageInfo_'.$playersid, $cgRecord, 0, 3600);
		}
		
		// 闯关次数
		$totalCgTimes = 0;
		$totalCgTimes = $cgRecord['timesLimt'];
		
		// 闯关次数用完
		if($cgRecord['cgTimes'] >= ($totalCgTimes + $cgRecord['addTimes'])) {
			$itemInfo = $playerBag[$djid];
			$itemproto = ConfigLoader::GetItemProto($itemInfo['ItemID']);
			$itemName = $itemproto['Name'];
			
			// 删除闯关卡			
			$player->DeleteItemByProto(array(CGCARD_ITEMID=>1));
			
			// 增加闯关次数
			$update['cgTimes'] = $cgRecord['cgTimes'] - 4;
			$common->updatetable('player_stage', $update, "playersid = '$playersid'");
			$common->updateMemCache(MC.'stageInfo_'.$playersid, $update);
				
			$returnValue['status'] = 0;
			$returnValue['message'] = "{$tools_lang['script_msg_84']}{$itemName}，{$tools_lang['script_msg_95']}";
			$returnValue['cgcs'] = 4;
			
			$roleInfo['itemId'] = CGCARD_ITEMID;
			$rwid = questsController::OnFinish($roleInfo, "'sydj'");
			if (!empty($rwid)) {
				if (!empty($rwid)) {
					$returnValue['rwid'] = $rwid;
				}
			}
			
			// 获取背包数据			
			$bagData = $player->GetClientBag();
			$returnValue['list'] = $bagData;	

			if(_get('all') == 1)
				$returnValue['hqdj'] = array(0=>array('闯关机会', 4));
			
		} else {		
			$returnValue['status'] = 998;
			$returnValue['message'] = $tools_lang['script_msg_96'];
		}				
			
		return $returnValue;
	}
	
	// 逐鹿活动奖励
	public static function itemUse_18($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
		
		// 获取使用道具的ID
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];	
		$item_name = $itemproto['Name'];
	
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_97']);
			return $returnValue;
		}
		
		$ingot = 0;  // 获取的元宝数量
		$coins = 0;  // 获取的铜钱数量
		$silver = 0; // 获取的银票数量
		$weapon_qh_level = 0;
		$add_item_arr = array(); // 获取的道具
		$message = null;
		$got_msg = null;
		
		// 随机装备数组
		$random_weapon_arr = array(
				6=>array(41202,42202,43202,44202),
				9=>array(41303,42303,43303,44303),
				12=>array(41304,42304,43304,44304),
				15=>array(41405,42405,43405,44405),
				18=>array(41406,42406,43406,44406),
				21=>array(41507,42507,43507,44507)
				);
		// 专属道具数组
		$zswq_arr = array(LGBOW_ITEMID,
						  HALFMOON_ITEMID,
						  RUNWIND_ITEMID,
						  HITTIGER_ITEMID,
						  ARCHLORD_ITEMID);
		// 橙将卡碎片数组
		$cjk_arr = array(20023,20024,20025,20026,20027,20028,20031,20032,20033,20034,20035,20036,20090,20091,20092,20093);
		
		// 逐鹿大奖级别
		$zldj_level = 0;
		$hqjlArr = array();
		
		if($itemid == 18508) { // 李鬼家财
			$zldj_level = 10;
			
			// 蓝将卡
			$add_item_arr[BLUECARD_ITEMID] = 1;
			
			// 获取+6随机装备
			$weapon_itemid = $random_weapon_arr[6][rand(0, count($random_weapon_arr[6]) - 1)];
			$add_item_arr[$weapon_itemid] = 1;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
			
			// 金刚石
			$add_item_arr[JGS_ITEMID] = 10;
			
			// 银票
			$silver = 50;
			
			$got_msg = "{$tools_lang['script_msg_99'][0]}*1、{$newItemName}+6*1、{$tools_lang['script_msg_99'][1]}*10、{$tools_lang['controller_msg_69']}50";
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}" . $got_msg;
			
			$hqjlArr[] = array($tools_lang['script_msg_99'][0], 1);
			$hqjlArr[] = array($newItemName. '+6', 1);
			$hqjlArr[] = array($tools_lang['script_msg_99'][1], 10);
		} else if($itemid == 18509) { // 小霸王彩礼
			$zldj_level = 20;
			
			// 蓝将卡
			$add_item_arr[BLUECARD_ITEMID] = 2;
				
			// 获取+9随机装备
			$weapon_itemid = $random_weapon_arr[9][rand(0, count($random_weapon_arr[9]) - 1)];
			$add_item_arr[$weapon_itemid] = 1;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
				
			// 金刚石
			$add_item_arr[JGS_ITEMID] = 20;
				
			// 银票
			$silver = 100;
			
			$got_msg = "{$tools_lang['script_msg_99'][0]}*2、{$newItemName}+9*1、{$tools_lang['script_msg_99'][1]}*20、{$tools_lang['controller_msg_69']}100";
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}" . $got_msg;
			
			$hqjlArr[] = array($tools_lang['script_msg_99'][0], 2);
			$hqjlArr[] = array($newItemName. '+9', 1);
			$hqjlArr[] = array($tools_lang['script_msg_99'][1], 20);
		} else if($itemid == 18510) { // 豹子头包裹
			$zldj_level = 30;
			
			// 紫将卡
			$add_item_arr[ZISECARD_ITEMID] = 1;
				
			// 获取+12随机装备
			$weapon_itemid = $random_weapon_arr[12][rand(0, count($random_weapon_arr[12]) - 1)];
			$add_item_arr[$weapon_itemid] = 1;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
				
			// 玛瑙
			$add_item_arr[MANAO_ITEMID] = 5;
				
			// 银票
			$silver = 200;
			
			$got_msg = "{$tools_lang['script_msg_99'][2]}*1、{$newItemName}+12*1、{$tools_lang['script_msg_99'][3]}*5、{$tools_lang['controller_msg_69']}200";
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}" . $got_msg;
			
			$hqjlArr[] = array($tools_lang['script_msg_99'][2], 1);
			$hqjlArr[] = array($newItemName. '+12', 1);
			$hqjlArr[] = array($tools_lang['script_msg_99'][3], 5);
		} else if($itemid == 18511) { // 生辰纲
			$zldj_level = 40;
			
			// 紫将卡
			$add_item_arr[ZISECARD_ITEMID] = 2;
			
			// 获取+15随机装备
			$weapon_itemid = $random_weapon_arr[15][rand(0, count($random_weapon_arr[15]) - 1)];
			$add_item_arr[$weapon_itemid] = 1;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
			
			// 玄铁
			$add_item_arr[XT_ITEMID] = 10;
			
			// 铜钱
			$coins = 50000;
			
			$got_msg = "{$tools_lang['script_msg_99'][2]}*2、{$newItemName}+15*1、{$tools_lang['script_msg_94']}*10、{$tools_lang['controller_msg_68']}50000";
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}" . $got_msg;
			
			$hqjlArr[] = array($tools_lang['script_msg_99'][2], 2);
			$hqjlArr[] = array($newItemName. '+15', 1);
			$hqjlArr[] = array($tools_lang['script_msg_94'], 10);
		} else if($itemid == 18512) { // 曾氏财宝
			$zldj_level = 50;
			
			// 橙将卡碎片
			$yellow_itemid = $cjk_arr[rand(0, count($cjk_arr) - 1)];
			$add_item_arr[$yellow_itemid] = 1;
			$newCardInfo = toolsModel::getItemInfo($yellow_itemid);
			$newCardName = $newCardInfo['Name'];
				
			// 获取+18随机装备
			$weapon_itemid = $random_weapon_arr[18][rand(0, count($random_weapon_arr[18]) - 1)];
			$add_item_arr[$weapon_itemid] = 1;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
				
			// 玛瑙
			$add_item_arr[MANAO_ITEMID] = 20;
				
			// 铜钱
			$coins = 100000;	

			$got_msg = "{$newCardName}*1、{$newItemName}+18*1、{$tools_lang['script_msg_99'][3]}*20、{$tools_lang['controller_msg_68']}100000";
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}" . $got_msg;
			
			$hqjlArr[] = array($newCardName, 1);
			$hqjlArr[] = array($newItemName. '+18', 1);
			$hqjlArr[] = array($tools_lang['script_msg_99'][3], 20);
		} else if($itemid == 18513) { // 江州军需
			$zldj_level = 60;
			
			// 橙将卡碎片
			$yellow_itemid = $cjk_arr[rand(0, count($cjk_arr) - 1)];
			$add_item_arr[$yellow_itemid] = 1;
			$newCardInfo = toolsModel::getItemInfo($yellow_itemid);
			$newCardName = $newCardInfo['Name'];
			
			// 获取+21随机装备
			$weapon_itemid = $random_weapon_arr[21][rand(0, count($random_weapon_arr[21]) - 1)];
			$add_item_arr[$weapon_itemid] = 1;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
			
			// 翡翠
			$add_item_arr[FEICUI_ITEMID] = 10;
			
			// 铜钱
			$coins = 150000;
			
			$got_msg = "{$newCardName}*1、{$newItemName}+21*1、{$tools_lang['script_msg_99'][4]}*10、{$tools_lang['controller_msg_68']}150000";
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}" . $got_msg;
			
			$hqjlArr[] = array($newCardName, 1);
			$hqjlArr[] = array($newItemName. '+21', 1);
			$hqjlArr[] = array($tools_lang['script_msg_99'][4], 10);
		} else if($itemid == 18514) { // 太尉宝箱
			$zldj_level = 70;
			
			// 专属武器
			$zsweapon_itemid = $zswq_arr[rand(0, count($zswq_arr) - 1)];
			$add_item_arr[$zsweapon_itemid] = 1;
			$newItmeInfo = toolsModel::getItemInfo($zsweapon_itemid);
			$newItemName = $newItmeInfo['Name'];
				
			// 白玉
			$add_item_arr[BAIYU_ITEMID] = 10;
			
			// 元宝
			$ingot = 200;
				
			// 铜钱
			$coins = 200000;
			
			$got_msg = "{$newItemName}*1、{$tools_lang['script_msg_99'][5]}*10、{$tools_lang['controller_msg_71']}200、{$tools_lang['controller_msg_68']}200000";
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}" . $got_msg;

			$hqjlArr[] = array($newItemName, 1);
			$hqjlArr[] = array($tools_lang['script_msg_99'][5], 10);			
		}
	
		// 检查玩家背包是否足够		
		$addRet = $player->AddItems($add_item_arr, false, array($itemid=>1));		
		if($addRet === false) {
			$returnValue['status'] = 998;
			$returnValue['message'] = $tools_lang['controller_msg_65'];
			return $returnValue;
		}		
		
		// 修改强化属性
		$playerBag = $player->GetItems();
		foreach($addRet as $idkey => $wid) {
			$itemproto = ConfigLoader::GetItemProto($playerBag[$wid]['ItemID']);			
			if($itemproto['ItemType'] == 4 && array_search($itemproto['ItemID'], $zswq_arr) === false) {
				$qh_level = 0;
				foreach($random_weapon_arr as $need_qhLevel => $itemids) {
					if(array_search($itemproto['ItemID'], $itemids) !== false) {
						$qh_level = $need_qhLevel;
						break;
					}
				}				
				$player->ModifyItem($wid, array('ItemID'=>$playerBag[$wid]['ItemID'], 'QhLevel'=>$qh_level));
			}
		}
		
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
						
		$currYP = $roleInfo['silver'];
		$currYB = $roleInfo['ingot'];
		$currTQ = $roleInfo['coins'];
		
		// 加入玩家背包
		if($silver > 0) {
			$updateRole['silver'] = $currYP + $silver;
			
			$returnValue['yp'] = $updateRole['silver'];
			$returnValue['hqyp'] = $silver;
		}
		
		if($coins > 0) {
			$updateRole['coins'] = $currTQ + $coins;
			
			$returnValue['tq'] = $updateRole['coins'];
			$returnValue['hqtq'] = $coins;
		}
		
		if($ingot > 0) {
			$updateRole['ingot'] = $currYB + $ingot;
				
			$returnValue['yb'] = $updateRole['ingot'];
			$returnValue['hqyb'] = $ingot;
		}
			
		// 更新玩家当前银票、铜钱、元宝数量
		$whereRole['playersid'] = $playersid;
		$common->updatetable('player', $updateRole, $whereRole);
		$common->updateMemCache(MC.$playersid, $updateRole);
		
		$returnValue['status'] = 0;
		$returnValue['message'] = $message;
		
		$nickName = $roleInfo['nickname'];
		$notice_msg1 = array($nickName, $zldj_level);
		$notice_msg2 = array($zldj_level, $got_msg);
		lettersModel::setSysPublicNoice($notice_msg1, 3);
		lettersModel::setSysPublicNoice($notice_msg2, 4);
		
		// 获取背包数据
		$bagData = $player->GetClientBag();
		$returnValue['list'] = $bagData;
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = $hqjlArr;
		
		return $returnValue;
	}
	
	// 七夕礼包
	/*
	public static function itemUse_19($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
	
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_97']);
			return $returnValue;
		}
		
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];	
		$item_name = $itemproto['Name'];
		
		$lb_info = array(
			18516=>$tools_lang['script_msg_98'][0],
			18517=>$tools_lang['script_msg_98'][1],
			18518=>$tools_lang['script_msg_98'][2],
			18519=>$tools_lang['script_msg_98'][3],
			18520=>$tools_lang['script_msg_98'][4]);
		
		// 礼包内容
		$lb_contents = array(
			18516=>0.20,
			18517=>0.20,
			18518=>0.20,
			18519=>0.20,
			18520=>0.20
		);
		$get_item_id = toolsModel::random($lb_contents);
				
		// 检查玩家背包是否足够
		$addRet = $player->AddItems(array($get_item_id=>1), false, array(18515=>1));
		if($addRet === false) {
			$returnValue['status'] = 998;
			$returnValue['message'] = $tools_lang['controller_msg_65'];
			return $returnValue;
		}
		
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
				
		$returnValue['status'] = 0;
		$returnValue['message'] = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$lb_info[$get_item_id]}";
				
		// 获取背包数据		
		$bagData = $player->GetClientBag();
		$returnValue['list'] = $bagData;
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = array(0=>array($lb_info[$get_item_id], 1));
		
		return $returnValue;
	}
	*/
	
	// 牵牛绳、鹊桥仙...
	public static function itemUse_20($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $_SGLOBAL, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
	
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_97']);
			return $returnValue;
		}
		
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
	
		$exp = 0; 	 // 获取经验数量
		$coins = 0;  // 获取的{$tools_lang['controller_msg_68']}数量
		$silver = 0; // 获取的{$tools_lang['controller_msg_69']}数量
		$add_item_arr = array(); // 获取的道具
		$message = null;
		$hqjlArr = array();
	
		// 获取使用道具的ID
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];		
		$item_name = $itemproto['Name'];
		if($itemid == 18516) { // 牵牛绳
			// 铜钱
			$coins = 500;
			
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}500{$tools_lang['controller_msg_68']}";
		} else if($itemid == 18517) { // 鹊桥仙
			// 银票
			$silver = 10;

			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}10{$tools_lang['controller_msg_69']}";
		} else if($itemid == 18518) { // 乞巧灯							
			// 中军粮包
			$add_item_arr[MIDJLB_ITEMID] = 1;
			$newitemproto = ConfigLoader::GetItemProto(MIDJLB_ITEMID);
			
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newitemproto['Name']}*1";
			$hqjlArr[] = array($newitemproto['Name'], 1);
		} else if($itemid == 18519) { // 织云梭
			// 小强化材料包
			$add_item_arr[SMALLQHB_ITEMID] = 1;
			$newitemproto = ConfigLoader::GetItemProto(SMALLQHB_ITEMID);
			
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newitemproto['Name']}*1";
			$hqjlArr[] = array($newitemproto['Name'], 1);
		} else if($itemid == 18520) { // 喜鹊魂
			$exp = 200;
			if($roleInfo['player_level'] < 70) {
				$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}200{$tools_lang['controller_msg_73']}";
			} else {
				$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['script_msg_100']}";
			}
		}
			
		if(count($add_item_arr) > 0) {		
			$addRet = $player->AddItems($add_item_arr, false, array($itemid=>1));
			if($addRet === false) {
				$returnValue['status'] = 998;
				$returnValue['message'] = $tools_lang['controller_msg_65'];
				return $returnValue;
			}			
		} else {		
			$player->DeleteItemByProto(array($itemid=>1));
		}
					
		$currYP = $roleInfo['silver'];		
		$currTQ = $roleInfo['coins'];
	
		// 加入玩家背包
		if($silver > 0) {
			$updateRole['silver'] = $currYP + $silver;
				
			$returnValue['yp'] = $updateRole['silver'];
			$returnValue['hqyp'] = $silver;
		}
	
		if($coins > 0) {
			$updateRole['coins'] = $currTQ + $coins;
				
			$returnValue['tq'] = $updateRole['coins'];
			$returnValue['hqtq'] = $coins;
		}
	
		if($exp > 0) {
			$nowTime = $_SGLOBAL['timestamp'];
			// 判断玩家是否升级
    		$upRet = fightModel::upgradeRole($roleInfo['player_level'], $exp + intval($roleInfo['current_experience_value']), 1);
    		// 备注：等级上限变更70时修改此处
    		if($roleInfo['player_level'] < 70) {
    			if($upRet['level'] > $roleInfo['player_level']) {
	    			$returnValue['upgrade']  = 1;
    			} else {
    				$returnValue['upgrade']  = 0;
    			}
	    		if($returnValue['upgrade'] == 1) { // 升级
	    			$returnValue['xjjy'] =  intval(cityModel::getPlayerUpgradeExp(intval($roleInfo['player_level']) + 1)); 
	    			$returnValue['jlsx'] =  foodUplimit($roleInfo['player_level']);
	    			$returnValue['level'] = $updateRole['player_level'] = intval($upRet['level']);	    			 
	    			$updateRole['current_experience_value'] = $upRet['left'];
					$updateRole['last_update_level'] = time();
	    			$roleInfo['player_level'] = intval($upRet['level']);
	    			$returnValue['lsdj'] = addLifeCost($roleInfo ['player_level']);
	    			$_SESSION['player_level'] = $roleInfo['player_level'];
					// 添加到擂台
	    			if($roleInfo['player_level'] == 7) {
	    				roleModel::saveDaleiInfo($roleInfo);
	    			}
					// 每十级送vip+1三天
					/*if($roleInfo['player_level']%10 == 0){
						roleModel::rwVip($roleInfo);
					}*/
					// 达到完成邀请条件
					if($roleInfo['player_level']==COMPLETE_REQUEST_LEVEL){
						socialModel::completeRequst($roleInfo, 1);
					}
					// 大于十级检查玩家手机绑定
					if($roleInfo['player_level']>=10){
						// 检查是否已绑定手机号,如果没有绑定就将手机号标识为999999
						if(isset($roleInfo['phone'])&&$roleInfo['phone']=='0'){
							$updateArray  = array('phone'=>'999999');
							$whereArray   = array('playersid'=>$playersid);
							$common->updatetable('player', $updateArray, $whereArray);
						}
					}
	    			// 获取日常任务
	    			if ($roleInfo['player_level'] == 8) {
	    				//roleModel::hqrcrw($roleInfo,false);
	    				getAward::sxrcrw($playersid,2);
	    			}
	    			// 升级补满军粮
	    			if ($roleInfo['food'] < $returnValue['jlsx']) {
		            	$updateRole['food'] = $returnValue['jlsx'];
						$updateRole['last_update_food'] = $nowTime;
		            	$roleInfo['food'] = $returnValue['jlsx'];
		            	$returnValue['jl'] = $returnValue['jlsx'];
		            }	 
		            $returnValue['tqzzl'] = roleModel::tqzzl($roleInfo['player_level']); // 铜钱增长率
                    if ($roleInfo['player_level'] == 7 || $roleInfo['player_level'] == 2) {
                    	$zt = 2;
                    } else {
                    	$zt = 1;
                    }					
		  	
    				//更新一日常任务
    	        	//获取任务状态信息
					if (! ($qStatus = $mc->get ( MC . $roleInfo['playersid'] . "_qstatus" ))) {
						$sql = "SELECT * FROM " . $common->tname ( 'quests_new_status' ) . " WHERE playersid = '".$roleInfo['playersid']."' LIMIT 1";
						$result = $db->query ( $sql );
						$rows = $db->fetch_array ( $result );
						$qStatus = $rows ['qstatusInfo'];
						$mc->set(MC.$playersid."_qstatus",$qStatus,0,3600);
					}	
					$qstatusInfo = array();
					$Qarray = array();
					$QarrayChose = array();
					if (! empty ( $qStatus )) {
						$qstatusInfo = unserialize ( $qStatus );
			            if (!empty($qstatusInfo)) {
							foreach ( $qstatusInfo as $key => $qValue ) {
								if (in_array ( $qValue, array (4, 5 ) ) && ! empty ( $key )) {
									$Qarray [] = $key;
								}
								if ($qValue == 6 && ! empty ( $key )) {
									$QarrayChose [] = $key;
								}									
							}
			            }
					}  
					$rcrwsl = count($Qarray);
					if ($rcrwsl < 3 && !empty($QarrayChose)) {  
						$canUseID = array();
						foreach ($QarrayChose as $QarrayChoseValue) {
							$rwinfo = ConfigLoader::GetQuestProto($QarrayChoseValue);
			        		$Level_Min = $rwinfo['Level_Min'];
			        		$Level_Max = $rwinfo['Level_Max'];							
							if ($returnValue['level'] >= $Level_Min && $returnValue['level'] <= $Level_Max) {
								$canUseID[] = $QarrayChoseValue;
							}
							$rwinfo = null;
							$Level_Min = null;
							$Level_Max = null;
						} 	
						if (!empty($canUseID)) {
			        		$checked = array_rand($canUseID,1);
			        		$QuestIDUpdate = $canUseID[$checked];			        		
				        	$rwsql = "SELECT * FROM ".$common->tname('accepted_quests')." WHERE playersid = $playersid && && QuestID = $QuestIDUpdate LIMIT 1";
				        	$rwsqlRows = $db->fetch_array($db->query($rwsql));
				        	if (!empty($rwsqlRows)) {
				        		$db->query("UPDATE ".$common->tname('accepted_quests')." SET published = 1 WHERE playersid = $playersid && QuestID = '$QuestIDUpdate' LIMIT 1");
				        		if ($rwsqlRows['Qstatus'] == 1) {
				        			getDataAboutLevel::addCompleteQuestsStatus($playersid,$QuestIDUpdate,0);
				        		} else {
				        			getDataAboutLevel::addNewQuestsStatus($playersid,$QuestIDUpdate,5);
				        		}
	                            //$updateRole['rcrws'] = $roleInfo['rcrws'] + 1;
	        			       // $roleInfo['rcrws'] = $updateRole['rcrws'];
				        	}
						}
					}							             				    			
	    		} else {
	    			$updateRole['current_experience_value'] = $roleInfo['current_experience_value'] + $exp;
	    		}
	    		$returnValue['hqjy'] = intval($exp);
	    		$returnValue['jy'] =  $updateRole['current_experience_value'];
    			$updateRoleWhere['playersid'] = $playersid;
				$common->updatetable('player',$updateRole,$updateRoleWhere);
				$common->updateMemCache(MC.$playersid,$updateRole);	
				
				// 任务相关(升级)
				if($returnValue['upgrade'] == 1) {
			     	$acc_res = questsController::OnAccept($roleInfo,"'player_level'");		
				}
    		}
		}
			
		if(isset($updateRole)) {
			// 更新玩家当前铜钱、元宝、银票数量
			$whereRole['playersid'] = $playersid;
			$common->updatetable('player', $updateRole, $whereRole);
			$common->updateMemCache(MC.$playersid, $updateRole);
		}
	
		$returnValue['status'] = 0;
		$returnValue['message'] = $message;
	
		$bagData = $player->GetClientBag();
		$returnValue['list'] = $bagData;
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = $hqjlArr;
			
		return $returnValue;
	}
	
	// 月老的祝福
	/*
	public static function itemUse_21($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}

		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
	
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_97']);
			return $returnValue;
		}
			
		$ingot = 0;  // 获取的元宝数量
		$coins = 0;  // 获取的铜钱数量
		$silver = 0; // 获取的银票数量
		$sw = 0;	 // 获取的声望
		$weapon_qh_level = 0;
		$add_item_arr = array(); // 获取的道具
		$message = null;
	
		// 随机装备数组
		$random_weapon_arr = array(
				12=>array(41304,42304,43304,44304),
				15=>array(41405,42405,43405,44405),
				18=>array(41406,42406,43406,44406),
				21=>array(41507,42507,43507,44507)
		);		
		// 橙将卡数组
		$cjk_arr = array('lc'=>10038);
		
		$lb_contents = array(
			'tq1'=>5000,
			'tq2'=>10000,
			'tq3'=>100000,
			'yp'=>50,
			'sw'=>100,
			MIDQHB_ITEMID=>1,
			BIGQHB_ITEMID=>1,
			SMALLXTB_ITEMID=>1,
			MIDXTB_ITEMID=>1,
			12=>1,
			15=>1,
			18=>1,
			21=>1,
			'ingot'=>1000,
			'lc'=>1				
		);
		
		$ps = array(			
			'tq1'=>0.38068,
			'tq2'=>0.19034,
			'tq3'=>0.01903,
			'yp'=>0.09517,
			'sw'=>0.09517,
			MIDQHB_ITEMID=>0.06345,
			BIGQHB_ITEMID=>0.03807,
			SMALLXTB_ITEMID=>0.05076,
			MIDXTB_ITEMID=>0.03807,
			12=>0.01903,
			15=>0.00761,
			18=>0.00127,
			21=>0.00060,
			'ingot'=>0.00038,			
			'lc'=>0.00038			
		);
		
		$none_dj_ps = array(			
			'tq1'=>0.38068,
			'tq2'=>0.19034,
			'tq3'=>0.01903,
			'yp'=>0.09517,
			'sw'=>0.09517,
			MIDQHB_ITEMID=>0.06345,
			BIGQHB_ITEMID=>0.03807,
			SMALLXTB_ITEMID=>0.05076,
			MIDXTB_ITEMID=>0.03807,
			12=>0.01903,
			15=>0.00761,
			18=>0.00127	
		);
			
		$lb_key = toolsModel::random($ps);
		$got_num = $lb_contents[$lb_key];
		
		date_default_timezone_set('PRC');
		
		// 控制爆率
		if($lb_key == 21) {			
			if(!($ylzf_qh21 = $mc->get(MC.'ylzf_qh21'))) { // 内存无数据
				$ylzf_qh21_arr = array('cs'=>1, 'date'=>time());
				$mc->set(MC.'ylzf_qh21', $ylzf_qh21_arr, 0, 3600 * 24);
			} else {
				$day = date('d', $ylzf_qh21['date']);
				$cur_day = date('d');
				if($cur_day != $day) { // 当天第一次
					$ylzf_qh21_arr = array('cs'=>1, 'date'=>time());
					$mc->set(MC.'ylzf_qh21', $ylzf_qh21_arr, 0, 3600 * 24);
				} else {
					if($ylzf_qh21['cs'] < 4) { // 只出现3次
						$ylzf_qh21['cs'] = $ylzf_qh21['cs'] + 1;
						$ylzf_qh21['date'] = time(); 
						$mc->set(MC.'ylzf_qh21', $ylzf_qh21, 0, 3600 * 24);					
					}
				}
			}
		} else if($lb_key == 'ingot')  {
			if(!($ylzf_ingot = $mc->get(MC.'ylzf_ingot'))) { // 内存无数据
				$ylzf_ingot_arr = array('cs'=>1, 'date'=>time());
				$mc->set(MC.'ylzf_ingot', $ylzf_ingot_arr, 0, 3600 * 24);
			} else {
				$day = date('d', $ylzf_ingot['date']);
				$cur_day = date('d');
				if($cur_day != $day) { // 当天第一次
					$ylzf_ingot_arr = array('cs'=>1, 'date'=>time());
					$mc->set(MC.'ylzf_ingot', $ylzf_ingot_arr, 0, 3600 * 24);
				} else {
					if($ylzf_ingot['cs'] < 2) {
						$ylzf_ingot['cs'] = $ylzf_ingot['cs'] + 1;
						$ylzf_ingot['date'] = time();
						$mc->set(MC.'ylzf_ingot', $ylzf_ingot, 0, 3600 * 24);
					}
				}
			}
		} else if($lb_key == 'lc') {
			if(!($ylzf_lc = $mc->get(MC.'ylzf_lc'))) { // 内存无数据
				$ylzf_lc_arr = array('cs'=>1, 'date'=>time());
				$mc->set(MC.'ylzf_lc', $ylzf_lc_arr, 0, 3600 * 24);		
			} else {
				$day = date('d', $ylzf_lc['date']);
				$cur_day = date('d');
				if($cur_day != $day) { // 当天第一次
					$ylzf_lc_arr = array('cs'=>1, 'date'=>time());
					$mc->set(MC.'ylzf_lc', $ylzf_lc_arr, 0, 3600 * 24);					
				} else {
					if($ylzf_lc['cs'] < 2) {
						$ylzf_lc['cs'] = $ylzf_lc['cs'] + 1;
						$ylzf_lc['date'] = time();
						$mc->set(MC.'ylzf_lc', $ylzf_lc, 0, 3600 * 24);
					}
				}
			}
		}
				
		if($lb_key == 'lc' || $lb_key == 'ingot' || $lb_key == 21) {
			$ylzf_qh21 = $mc->get(MC.'ylzf_qh21');
			$ylzf_ingot = $mc->get(MC.'ylzf_ingot');
			$ylzf_lc = $mc->get(MC.'ylzf_lc');
			if(is_array($ylzf_qh21)) {
				$day = date('d', $ylzf_qh21['date']);
				$cur_day = date('d');
				if($cur_day == $day && $ylzf_qh21['cs'] > 3) {
					unset($ps[21]);
				}
			}
			
			if(is_array($ylzf_ingot)) {
				$day = date('d', $ylzf_ingot['date']);
				$cur_day = date('d');
				if($cur_day == $day && $ylzf_ingot['cs'] > 1) {
					unset($ps['ingot']);
				}
			}
			
			if(is_array($ylzf_lc)) {
				$day = date('d', $ylzf_lc['date']);
				$cur_day = date('d');
				if($cur_day == $day && $ylzf_lc['cs'] > 1) {
					unset($ps['lc']);
				}
			}
			
			if(!array_key_exists($lb_key, $ps)) {							
				$lb_key = toolsModel::random($none_dj_ps);
				$got_num = $lb_contents[$lb_key];
			}
		}
	
		// 获取使用道具的ID
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];
		$item_name = $itemproto['Name'];
		$rarity = 1;
		$hqjlArr = array();
		if($lb_key == 'tq1' || $lb_key == 'tq2' || $lb_key == 'tq3') {
			$coins = $got_num;		
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$got_num}{$tools_lang['controller_msg_68']}";
		} else if($lb_key == 'yp') {			
			$silver = $got_num;		
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$got_num}{$tools_lang['controller_msg_69']}";
		} else if($lb_key == 'sw') {			
			$sw = $got_num;		
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$got_num}{$tools_lang['controller_msg_72']}";
		} else if($lb_key == 'ingot') {			
			$ingot = $got_num;		
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$got_num}{$tools_lang['controller_msg_71']}";
		} else if($lb_key == 12) {				
			// 获取+12随机装备
			$weapon_itemid = $random_weapon_arr[12][rand(0, count($random_weapon_arr[12]) - 1)];
			$add_item_arr[$weapon_itemid] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
			$rarity = $newItmeInfo['Rarity'];
						
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}+12*{$got_num}";
			
			$hqjlArr[] = array($newItemName . '+12', $got_num);
		} else if($lb_key == 15) {				
			// 获取+15随机装备
			$weapon_itemid = $random_weapon_arr[15][rand(0, count($random_weapon_arr[15]) - 1)];
			$add_item_arr[$weapon_itemid] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
			$rarity = $newItmeInfo['Rarity'];
						
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}+15*{$got_num}";
			
			$hqjlArr[] = array($newItemName . '+15', $got_num);
		} else if($lb_key == 18) {				
			// 获取+18随机装备
			$weapon_itemid = $random_weapon_arr[18][rand(0, count($random_weapon_arr[18]) - 1)];
			$add_item_arr[$weapon_itemid] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
			$rarity = $newItmeInfo['Rarity'];
						
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}+18*{$got_num}";
			
			$hqjlArr[] = array($newItemName . '+18', $got_num);
		} else if($lb_key == 21) {				
			// 获取+21随机装备
			$weapon_itemid = $random_weapon_arr[21][rand(0, count($random_weapon_arr[21]) - 1)];
			$add_item_arr[$weapon_itemid] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
			$rarity = $newItmeInfo['Rarity'];
						
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}+21*{$got_num}";
			
			$hqjlArr[] = array($newItemName . '+21', $got_num);
		} else if($lb_key == 'lc') {		
			$add_item_arr[$cjk_arr[$lb_key]] = $got_num;	
			$newItmeInfo = toolsModel::getItemInfo($cjk_arr[$lb_key]);
			$newItemName = $newItmeInfo['Name'];			
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItmeInfo['Name']}*{$got_num}";
			
			$hqjlArr[] = array($newItmeInfo['Name'], $got_num);
		} else if($lb_key == MIDQHB_ITEMID) {
			$add_item_arr[$lb_key] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($lb_key);
			$newItemName = $newItmeInfo['Name'];
				
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}*{$got_num}";
			
			$hqjlArr[] = array($newItemName, $got_num);
		} else if($lb_key == BIGQHB_ITEMID) {
			$add_item_arr[$lb_key] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($lb_key);
			$newItemName = $newItmeInfo['Name'];
				
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}*{$got_num}";
			
			$hqjlArr[] = array($newItemName, $got_num);
		} else if($lb_key == SMALLXTB_ITEMID) {
			$add_item_arr[$lb_key] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($lb_key);
			$newItemName = $newItmeInfo['Name'];
				
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}*{$got_num}";
			
			$hqjlArr[] = array($newItemName, $got_num);
		} else if($lb_key == MIDXTB_ITEMID) {
			$add_item_arr[$lb_key] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($lb_key);
			$newItemName = $newItmeInfo['Name'];
				
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}*{$got_num}";
			
			$hqjlArr[] = array($newItemName, $got_num);
		} 
	
		if(count($add_item_arr) > 0) {
			// 模拟删除奖励礼包	
			$addRet = $player->AddItems($add_item_arr, false, array($itemid=>1));
			if($addRet === false) {
				$returnValue['status'] = 998;
				$returnValue['message'] = $tools_lang['controller_msg_65'];
				return $returnValue;
			}
		} else {
			$player->DeleteItemByProto(array($itemid=>1));
		}		
		
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
		if(count($add_item_arr) > 0) {		
			// 修改强化属性
			$playerBag = $player->GetItems();
			foreach($addRet as $idkey => $wid) {
				$itemproto = ConfigLoader::GetItemProto($playerBag[$wid]['ItemID']);			
				if($itemproto['ItemType'] == 4) {
					$qh_level = 0;
					foreach($random_weapon_arr as $need_qhLevel => $itemids) {
						if(array_search($itemproto['ItemID'], $itemids) !== false) {
							$qh_level = $need_qhLevel;
							break;
						}
					}				
					$player->ModifyItem($wid, array('ItemID'=>$playerBag[$wid]['ItemID'], 'QhLevel'=>$qh_level));
				}
			}
		}		
	
		$currYP = $roleInfo['silver'];
		$currYB = $roleInfo['ingot'];
		$currTQ = $roleInfo['coins'];
		$currSW = $roleInfo['prestige'];
	
		// 加入玩家背包
		if($silver > 0) {
			$updateRole['silver'] = $currYP + $silver;
				
			$returnValue['yp'] = $updateRole['silver'];
			$returnValue['hqyp'] = $silver;
		}
	
		if($coins > 0) {
			$updateRole['coins'] = $currTQ + $coins;
				
			$returnValue['tq'] = $updateRole['coins'];
			$returnValue['hqtq'] = $coins;
		}
	
		if($ingot > 0) {
			$updateRole['ingot'] = $currYB + $ingot;
	
			$returnValue['yb'] = $updateRole['ingot'];
			$returnValue['hqyb'] = $ingot;
		}
		
		if($sw > 0) {
			$updateRole['prestige'] = $currSW + $sw;
		
			$returnValue['sw'] = $updateRole['prestige'];
			$returnValue['hqsw'] = $sw;
		}
			
		if(isset($updateRole)) {
			// 更新玩家当前银票、铜钱、元宝数量
			$whereRole['playersid'] = $playersid;
			$common->updatetable('player', $updateRole, $whereRole);
			$common->updateMemCache(MC.$playersid, $updateRole);
		}
	
		$returnValue['status'] = 0;
		$returnValue['message'] = $message;

		$bagData = $player->GetClientBag();
		
		// 发公告		
		$nickName = $roleInfo['nickname'];
		if($lb_key == 12 || $lb_key == 15 || $lb_key == 18 || $lb_key == 21) {
			if($rarity == 1) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_101'][1]}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#FFFFFF\">[{$newItemName}+{$lb_key}*1]</FONT>");
			} else if($rarity == 2) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_101'][1]}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#66ff00\">[{$newItemName}+{$lb_key}*1]</FONT>");
			}  else if($rarity == 3) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_101'][1]}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#60acfe\">[{$newItemName}+{$lb_key}*1]</FONT>");
			}  else if($rarity == 4) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_101'][1]}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#ff65f8\">[{$newItemName}+{$lb_key}*1]</FONT>");
			}  else if($rarity == 5) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_101'][1]}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#ff8929\">[{$newItemName}+{$lb_key}*1]</FONT>");
			}
		} else if($lb_key == 'tq3') {
			lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_101'][1]}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#60acfe\">[{$coins}{$tools_lang['controller_msg_68']}]</FONT>");
		} else if($lb_key == 'ingot') {
			lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_101'][1]}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#ff8929\">[{$ingot}{$tools_lang['controller_msg_71']}]</FONT>");
		} else if($lb_key == 'lc') {
			lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_101'][1]}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#ff8929\">[{$newItemName}*1]</FONT>");
		}
		
		$returnValue['list'] = $bagData;
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = $hqjlArr;
		
		return $returnValue;
	}
	*/

		// 中秋礼包
	public static function itemUse_22($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
		
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];
		$item_name = $itemproto['Name'];
	
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_97']);
			return $returnValue;
		}
		
		$lb_info = array(
			20130=>$tools_lang['script_msg_102'][0],
			20131=>$tools_lang['script_msg_102'][1],
			20132=>$tools_lang['script_msg_102'][2],
			20133=>$tools_lang['script_msg_102'][3]);
		
		// 礼包内容
		$lb_contents = array(
			20130=>0.25,
			20131=>0.25,
			20132=>0.25,
			20133=>0.25
		);
		$get_item_id = toolsModel::random($lb_contents);
				
		// 检查玩家背包是否足够
		$addRet = $player->AddItems(array($get_item_id=>1), false, array(20128=>1));
		if($addRet === false) {
			$returnValue['status'] = 998;
			$returnValue['message'] = $tools_lang['controller_msg_65'];
			return $returnValue;
		}
		
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
				
		$returnValue['status'] = 0;
		$returnValue['message'] = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$lb_info[$get_item_id]}*1";
				
		// 获取背包数据		
		$bagData = $player->GetClientBag();
		$returnValue['list'] = $bagData;
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = array(0=>array($lb_info[$get_item_id], 1));
		
		return $returnValue;
	} 
	
	// 金樽月饼
	public static function itemUse_23($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}

		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
	
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_97']);
			return $returnValue;
		}
			
		$ingot = 0;  // 获取的元宝数量
		$coins = 0;  // 获取的铜钱数量
		$silver = 0; // 获取的银票数量
		$sw = 0;	 // 获取的声望
		$weapon_qh_level = 0;
		$add_item_arr = array(); // 获取的道具
		$message = null;
	
		// 随机装备数组
		$random_weapon_arr = array(
				12=>array(41304,42304,43304,44304),
				15=>array(41405,42405,43405,44405),
				18=>array(41406,42406,43406,44406),
				21=>array(41507,42507,43507,44507)
		);		
		// 橙将卡数组
		$cjk_arr = array('lc'=>10038);
		
		$lb_contents = array(
			'tq1'=>5000,
			'tq2'=>10000,
			'tq3'=>100000,
			'yp'=>50,
			'sw'=>100,
			MIDQHB_ITEMID=>1,
			BIGQHB_ITEMID=>1,
			SMALLXTB_ITEMID=>1,
			MIDXTB_ITEMID=>1,
			12=>1,
			15=>1,
			18=>1,
			21=>1,
			'ingot'=>1000,
			'lc'=>1				
		);
		
		date_default_timezone_set('PRC');
		
		// 控制上午11点以后才会出大奖	
		$curr_hour = date('H');
		if($curr_hour >= 11) {
			$ps = array(			
				'tq1'=>0.38,
				'tq2'=>0.19,
				'tq3'=>0.019,
				'yp'=>0.095,
				'sw'=>0.095,
				MIDQHB_ITEMID=>0.063,
				BIGQHB_ITEMID=>0.038,
				SMALLXTB_ITEMID=>0.050,
				MIDXTB_ITEMID=>0.038,
				12=>0.019,
				15=>0.0076,
				18=>0.0012,
				21=>0.00060,
				'ingot'=>0.00038,			
				'lc'=>0.00038			
			);
		} else {
			$ps = array(			
				'tq1'=>0.38,
				'tq2'=>0.19,
				'tq3'=>0.019,
				'yp'=>0.095,
				'sw'=>0.095,
				MIDQHB_ITEMID=>0.063,
				BIGQHB_ITEMID=>0.038,
				SMALLXTB_ITEMID=>0.050,
				MIDXTB_ITEMID=>0.038,
				12=>0.019,
				15=>0.0076,
				18=>0.0012
			);
		}
		
		$none_dj_ps = array(			
			'tq1'=>0.38,
			'tq2'=>0.19,
			'tq3'=>0.019,
			'yp'=>0.095,
			'sw'=>0.095,
			MIDQHB_ITEMID=>0.063,
			BIGQHB_ITEMID=>0.038,
			SMALLXTB_ITEMID=>0.050,
			MIDXTB_ITEMID=>0.038,
			12=>0.019,
			15=>0.0076,
			18=>0.0012
		);
			
		$lb_key = toolsModel::random($ps);
		$got_num = $lb_contents[$lb_key];
		
		// 控制爆率
		if($lb_key == 21) {			
			if(!($ylzf_qh21 = $mc->get(MC.'ylzf_qh21'))) { // 内存无数据
				$ylzf_qh21_arr = array('cs'=>1, 'date'=>time());
				$mc->set(MC.'ylzf_qh21', $ylzf_qh21_arr, 0, 3600 * 24);
			} else {
				$day = date('d', $ylzf_qh21['date']);
				$cur_day = date('d');
				if($cur_day != $day) { // 当天第一次
					$ylzf_qh21_arr = array('cs'=>1, 'date'=>time());
					$mc->set(MC.'ylzf_qh21', $ylzf_qh21_arr, 0, 3600 * 24);
				} else {
					if($ylzf_qh21['cs'] < 4) { // 只出现3次
						$ylzf_qh21['cs'] = $ylzf_qh21['cs'] + 1;
						$ylzf_qh21['date'] = time(); 
						$mc->set(MC.'ylzf_qh21', $ylzf_qh21, 0, 3600 * 24);					
					}
				}
			}
		} else if($lb_key == 'ingot')  {
			if(!($ylzf_ingot = $mc->get(MC.'ylzf_ingot'))) { // 内存无数据
				$ylzf_ingot_arr = array('cs'=>1, 'date'=>time());
				$mc->set(MC.'ylzf_ingot', $ylzf_ingot_arr, 0, 3600 * 24);
			} else {
				$day = date('d', $ylzf_ingot['date']);
				$cur_day = date('d');
				if($cur_day != $day) { // 当天第一次
					$ylzf_ingot_arr = array('cs'=>1, 'date'=>time());
					$mc->set(MC.'ylzf_ingot', $ylzf_ingot_arr, 0, 3600 * 24);
				} else {
					if($ylzf_ingot['cs'] < 2) {
						$ylzf_ingot['cs'] = $ylzf_ingot['cs'] + 1;
						$ylzf_ingot['date'] = time();
						$mc->set(MC.'ylzf_ingot', $ylzf_ingot, 0, 3600 * 24);
					}
				}
			}
		} else if($lb_key == 'lc') {
			if(!($ylzf_lc = $mc->get(MC.'ylzf_lc'))) { // 内存无数据
				$ylzf_lc_arr = array('cs'=>1, 'date'=>time());
				$mc->set(MC.'ylzf_lc', $ylzf_lc_arr, 0, 3600 * 24);		
			} else {
				$day = date('d', $ylzf_lc['date']);
				$cur_day = date('d');
				if($cur_day != $day) { // 当天第一次
					$ylzf_lc_arr = array('cs'=>1, 'date'=>time());
					$mc->set(MC.'ylzf_lc', $ylzf_lc_arr, 0, 3600 * 24);					
				} else {
					if($ylzf_lc['cs'] < 2) {
						$ylzf_lc['cs'] = $ylzf_lc['cs'] + 1;
						$ylzf_lc['date'] = time();
						$mc->set(MC.'ylzf_lc', $ylzf_lc, 0, 3600 * 24);
					}
				}
			}
		}
				
		if($lb_key == 'lc' || $lb_key == 'ingot' || $lb_key == 21) {
			$ylzf_qh21 = $mc->get(MC.'ylzf_qh21');
			$ylzf_ingot = $mc->get(MC.'ylzf_ingot');
			$ylzf_lc = $mc->get(MC.'ylzf_lc');
			if(is_array($ylzf_qh21)) {
				$day = date('d', $ylzf_qh21['date']);
				$cur_day = date('d');
				if($cur_day == $day && $ylzf_qh21['cs'] > 3) {
					unset($ps[21]);
				}
			}
			
			if(is_array($ylzf_ingot)) {
				$day = date('d', $ylzf_ingot['date']);
				$cur_day = date('d');
				if($cur_day == $day && $ylzf_ingot['cs'] > 1) {
					unset($ps['ingot']);
				}
			}
			
			if(is_array($ylzf_lc)) {
				$day = date('d', $ylzf_lc['date']);
				$cur_day = date('d');
				if($cur_day == $day && $ylzf_lc['cs'] > 1) {
					unset($ps['lc']);
				}
			}
			
			if(!array_key_exists($lb_key, $ps)) {							
				$lb_key = toolsModel::random($none_dj_ps);
				$got_num = $lb_contents[$lb_key];
			}
		}
	
		// 获取使用道具的ID
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];
		$item_name = $itemproto['Name'];
		$rarity = 1;
		$hqjlArr = array();
		if($lb_key == 'tq1' || $lb_key == 'tq2' || $lb_key == 'tq3') {
			$coins = $got_num;		
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$got_num}{$tools_lang['controller_msg_68']}";
		} else if($lb_key == 'yp') {			
			$silver = $got_num;		
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$got_num}{$tools_lang['controller_msg_69']}";
		} else if($lb_key == 'sw') {			
			$sw = $got_num;		
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$got_num}{$tools_lang['controller_msg_72']}";
		} else if($lb_key == 'ingot') {			
			$ingot = $got_num;		
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$got_num}{$tools_lang['controller_msg_71']}";
		} else if($lb_key == 12) {				
			// 获取+12随机装备
			$weapon_itemid = $random_weapon_arr[12][rand(0, count($random_weapon_arr[12]) - 1)];
			$add_item_arr[$weapon_itemid] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
			$rarity = $newItmeInfo['Rarity'];
						
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}+12*{$got_num}";
			
			$hqjlArr[] = array($newItemName . '+12', $got_num);
		} else if($lb_key == 15) {				
			// 获取+15随机装备
			$weapon_itemid = $random_weapon_arr[15][rand(0, count($random_weapon_arr[15]) - 1)];
			$add_item_arr[$weapon_itemid] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
			$rarity = $newItmeInfo['Rarity'];
						
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}+15*{$got_num}";
			
			$hqjlArr[] = array($newItemName . '+15', $got_num);
		} else if($lb_key == 18) {				
			// 获取+18随机装备
			$weapon_itemid = $random_weapon_arr[18][rand(0, count($random_weapon_arr[18]) - 1)];
			$add_item_arr[$weapon_itemid] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
			$rarity = $newItmeInfo['Rarity'];
						
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}+18*{$got_num}";
			
			$hqjlArr[] = array($newItemName . '+18', $got_num);
		} else if($lb_key == 21) {				
			// 获取+21随机装备
			$weapon_itemid = $random_weapon_arr[21][rand(0, count($random_weapon_arr[21]) - 1)];
			$add_item_arr[$weapon_itemid] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
			$rarity = $newItmeInfo['Rarity'];
						
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}+21*{$got_num}";
			
			$hqjlArr[] = array($newItemName . '+21', $got_num);
		} else if($lb_key == 'lc') {		
			$add_item_arr[$cjk_arr[$lb_key]] = $got_num;	
			$newItmeInfo = toolsModel::getItemInfo($cjk_arr[$lb_key]);
			$newItemName = $newItmeInfo['Name'];			
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItmeInfo['Name']}*{$got_num}";
			
			$hqjlArr[] = array($newItmeInfo['Name'], $got_num);
		} else if($lb_key == MIDQHB_ITEMID) {
			$add_item_arr[$lb_key] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($lb_key);
			$newItemName = $newItmeInfo['Name'];
				
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}*{$got_num}";
			
			$hqjlArr[] = array($newItemName, $got_num);
		} else if($lb_key == BIGQHB_ITEMID) {
			$add_item_arr[$lb_key] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($lb_key);
			$newItemName = $newItmeInfo['Name'];
				
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}*{$got_num}";
			
			$hqjlArr[] = array($newItemName, $got_num);
		} else if($lb_key == SMALLXTB_ITEMID) {
			$add_item_arr[$lb_key] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($lb_key);
			$newItemName = $newItmeInfo['Name'];
				
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}*{$got_num}";
			
			$hqjlArr[] = array($newItemName, $got_num);
		} else if($lb_key == MIDXTB_ITEMID) {
			$add_item_arr[$lb_key] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($lb_key);
			$newItemName = $newItmeInfo['Name'];
				
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}*{$got_num}";
			
			$hqjlArr[] = array($newItemName, $got_num);
		} 
	
		if(count($add_item_arr) > 0) {
			// 模拟删除奖励礼包	
			$addRet = $player->AddItems($add_item_arr, false, array($itemid=>1));
			if($addRet === false) {
				$returnValue['status'] = 998;
				$returnValue['message'] = $tools_lang['controller_msg_65'];
				return $returnValue;
			}
		} else {
			$player->DeleteItemByProto(array($itemid=>1));
		}		
		
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
		if(count($add_item_arr) > 0) {		
			// 修改强化属性
			$playerBag = $player->GetItems();
			foreach($addRet as $idkey => $wid) {
				$itemproto = ConfigLoader::GetItemProto($playerBag[$wid]['ItemID']);			
				if($itemproto['ItemType'] == 4) {
					$qh_level = 0;
					foreach($random_weapon_arr as $need_qhLevel => $itemids) {
						if(array_search($itemproto['ItemID'], $itemids) !== false) {
							$qh_level = $need_qhLevel;
							break;
						}
					}				
					$player->ModifyItem($wid, array('ItemID'=>$playerBag[$wid]['ItemID'], 'QhLevel'=>$qh_level));
				}
			}
		}		
	
		$currYP = $roleInfo['silver'];
		$currYB = $roleInfo['ingot'];
		$currTQ = $roleInfo['coins'];
		$currSW = $roleInfo['prestige'];
	
		// 加入玩家背包
		if($silver > 0) {
			$updateRole['silver'] = $currYP + $silver;
				
			$returnValue['yp'] = $updateRole['silver'];
			$returnValue['hqyp'] = $silver;
		}
	
		if($coins > 0) {
			$updateRole['coins'] = $currTQ + $coins;
				
			$returnValue['tq'] = $updateRole['coins'];
			$returnValue['hqtq'] = $coins;
		}
	
		if($ingot > 0) {
			$updateRole['ingot'] = $currYB + $ingot;
	
			$returnValue['yb'] = $updateRole['ingot'];
			$returnValue['hqyb'] = $ingot;
		}
		
		if($sw > 0) {
			$updateRole['prestige'] = $currSW + $sw;
		
			$returnValue['sw'] = $updateRole['prestige'];
			$returnValue['hqsw'] = $sw;
		}
			
		if(isset($updateRole)) {
			// 更新玩家当前银票、铜钱、元宝数量
			$whereRole['playersid'] = $playersid;
			$common->updatetable('player', $updateRole, $whereRole);
			$common->updateMemCache(MC.$playersid, $updateRole);
		}
	
		$returnValue['status'] = 0;
		$returnValue['message'] = $message;

		$bagData = $player->GetClientBag();
		
		// 发公告		
		$nickName = $roleInfo['nickname'];
		if($lb_key == 12 || $lb_key == 15 || $lb_key == 18 || $lb_key == 21) {
			if($rarity == 1) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#FFFFFF\">[{$newItemName}+{$lb_key}*1]</FONT>");
			} else if($rarity == 2) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#66ff00\">[{$newItemName}+{$lb_key}*1]</FONT>");
			}  else if($rarity == 3) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#60acfe\">[{$newItemName}+{$lb_key}*1]</FONT>");
			}  else if($rarity == 4) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#ff65f8\">[{$newItemName}+{$lb_key}*1]</FONT>");
			}  else if($rarity == 5) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#ff8929\">[{$newItemName}+{$lb_key}*1]</FONT>");
			}
		} else if($lb_key == 'tq3') {
			lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#60acfe\">[{$coins}{$tools_lang['controller_msg_68']}]</FONT>");
		} else if($lb_key == 'ingot') {
			lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#ff8929\">[{$ingot}{$tools_lang['controller_msg_71']}]</FONT>");
		} else if($lb_key == 'lc') {
			lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#ff8929\">[{$newItemName}*1]</FONT>");
		}
		
		$returnValue['list'] = $bagData;
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = $hqjlArr;
		
		return $returnValue;
	}
	
	// 面粉、豆沙...
	public static function itemUse_24($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $_SGLOBAL, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
	
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_97']);
			return $returnValue;
		}
		
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
	
		$exp = 0; 	 // 获取经验数量
		$coins = 0;  // 获取的铜钱数量
		$silver = 0; // 获取的银票数量
		$add_item_arr = array(); // 获取的道具
		$message = null;
		$hqjlArr = array();
	
		// 获取使用道具的ID
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];		
		$item_name = $itemproto['Name'];
		if($itemid == 20130) { // 面粉			
			// 小强化材料包
			$add_item_arr[SMALLQHB_ITEMID] = 1;
			$newitemproto = ConfigLoader::GetItemProto(SMALLQHB_ITEMID);
			
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newitemproto['Name']}*1";
			$hqjlArr[] = array($newitemproto['Name'], 1);
		} else if($itemid == 20131) { // 豆沙
			// 大军粮包
			$add_item_arr[BIGJLB_ITEMID] = 1;
			$newitemproto = ConfigLoader::GetItemProto(BIGJLB_ITEMID);

			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newitemproto['Name']}*1";
			$hqjlArr[] = array($newitemproto['Name'], 1);
		} else if($itemid == 20132) { // 莲蓉							
			// 银票
			$silver = 20;
			
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}20{$tools_lang['controller_msg_69']}";			
		} else if($itemid == 20133) { // 奶油
			// 铜钱
			$coins = 1000;

			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}1000{$tools_lang['controller_msg_68']}";			
		} 
			
		if(count($add_item_arr) > 0) {		
			$addRet = $player->AddItems($add_item_arr, false, array($itemid=>1));
			if($addRet === false) {
				$returnValue['status'] = 998;
				$returnValue['message'] = $tools_lang['controller_msg_65'];
				return $returnValue;
			}			
		} else {		
			$player->DeleteItemByProto(array($itemid=>1));
		}
					
		$currYP = $roleInfo['silver'];		
		$currTQ = $roleInfo['coins'];
	
		// 加入玩家背包
		if($silver > 0) {
			$updateRole['silver'] = $currYP + $silver;
				
			$returnValue['yp'] = $updateRole['silver'];
			$returnValue['hqyp'] = $silver;
		}
	
		if($coins > 0) {
			$updateRole['coins'] = $currTQ + $coins;
				
			$returnValue['tq'] = $updateRole['coins'];
			$returnValue['hqtq'] = $coins;
		}
	
		if(isset($updateRole)) {
			// 更新玩家当前银票、铜钱、元宝数量
			$whereRole['playersid'] = $playersid;
			$common->updatetable('player', $updateRole, $whereRole);
			$common->updateMemCache(MC.$playersid, $updateRole);
		}
	
		$returnValue['status'] = 0;
		$returnValue['message'] = $message;
	
		// 获取背包数据
		$bagData = $player->GetClientBag();
		$returnValue['list'] = $bagData;
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = $hqjlArr;
			
		return $returnValue;
	}

	
	// 闯关解锁道具
	public static function itemUse_25($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
	
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_104']);
			return $returnValue;
		}
		
		// 获取玩家闯关进度
		if (!($cgRecord = $mc->get(MC.'stageInfo_'.$playersid))) {
			$cgRecord = $db->fetch_array($db->query("SELECT * FROM ".$common->tname('player_stage')." WHERE `playersid` = '$playersid' limit 1"));
			$mc->set(MC.'stageInfo_'.$playersid, $cgRecord, 0, 3600);
		}
		
		$unlock_itemid = $playerBag[$djid]['ItemID'];
		$unlock_proto = toolsModel::getItemInfo($unlock_itemid);
		$unlock_item_name = $unlock_proto['Name'];		
		$need_unlock_itemid = '';		
		$need_unlock_difficuity = 0;
		$need_unlock_stage = 0;
		if($cgRecord['unlock'] == 9) $cgRecord['unlock'] = 8;
		if($cgRecord['difficulty'] <= 6 && $cgRecord['unlock'] < 8) {
			$need_unlock_itemid = getUnlockItem($cgRecord['difficulty'], $cgRecord['unlock'] + 1);
			$need_unlock_difficuity = $cgRecord['difficulty'];
			$need_unlock_stage = $cgRecord['unlock'] + 1;
			
			// 解锁大关+1
			$updateStageInfo['unlock'] = $cgRecord['unlock'] + 1;			      		
		} else if($cgRecord['difficulty'] < 6 && $cgRecord['unlock'] == 8) { // 开启下一难度  备注：增加地狱难度时修改此处的$nd
			$need_unlock_itemid = getUnlockItem($cgRecord['difficulty'] + 1, 1);
			$need_unlock_difficuity = $cgRecord['difficulty'] + 1;
			$need_unlock_stage = 1;
			
			$updateStageInfo['difficulty'] = $cgRecord['difficulty'] + 1;
			$updateStageInfo['unlock'] = 1;
			$updateStageInfo['curr_difficulty'] = 0;									
		}

		if($unlock_itemid == $need_unlock_itemid) {
			// 删除解锁道具			
			$player->DeleteItemByProto(array($need_unlock_itemid=>1));
			
			// 修改闯关解锁信息			
			$common->updatetable('player_stage', $updateStageInfo, "playersid = '$playersid'");
			$common->updateMemCache(MC.'stageInfo_'.$playersid, $updateStageInfo);
				
			$returnValue['status'] = 0;
			$gk_difficuity_names = array(1=>$tools_lang['script_msg_107'][0],2=>$tools_lang['script_msg_107'][1], 3=>$tools_lang['script_msg_107'][2],4=>$tools_lang['script_msg_107'][3],5=>$tools_lang['script_msg_107'][4],6=>$tools_lang['script_msg_107'][5]);
			$gk_stage_names = array(1=>$tools_lang['script_msg_109'][0],2=>$tools_lang['script_msg_109'][1], 3=>$tools_lang['script_msg_109'][2],4=>$tools_lang['script_msg_109'][3],5=>$tools_lang['script_msg_109'][4],6=>$tools_lang['script_msg_109'][5],7=>$tools_lang['script_msg_109'][6],8=>$tools_lang['script_msg_109'][7]);
			$returnValue['message'] = "{$tools_lang['script_msg_84']}{$unlock_item_name}，{$gk_difficuity_names[$need_unlock_difficuity]}{$tools_lang['script_msg_108'][0]}{$gk_stage_names[$need_unlock_stage]}{$tools_lang['script_msg_108'][1]}";
			
			$returnValue['kqnd'] = isset($updateStageInfo['difficulty']) ? $updateStageInfo['difficulty'] : intval($cgRecord['difficulty']);
			$returnValue['kqdgk'] = $updateStageInfo['unlock'];
						
			// 获取背包数据			
			$bagData = $player->GetClientBag();
			$returnValue['list'] = $bagData;	
			if ($unlock_itemid == 18522) {		
			    $rwid = questsController::OnFinish($player->baseinfo_,"'ckbb'");
	            if (!empty($rwid)) {
	              $returnValue['rwid'] = $rwid;
	            }	
		    	$xwzt_9 = substr($player->baseinfo_['xwzt'],8,1);  //完成 开启普通阳谷县行为
				if ($xwzt_9 == 0) {
					$xwzt = substr_replace($player->baseinfo_['xwzt'],'1',8,1);
					$returnValue['xwzt'] = $xwzt;
					$common->updatetable('player',"xwzt = '$xwzt'","playersid = '$playersid' LIMIT 1");
					$common->updateMemCache(MC.$playersid,array('xwzt'=>$xwzt));			
				} 		            			
			}
			
			$mc->delete(MC.$playersid.'_killBoss_wmjs');
			
			if(_get('all') == 1)
				$returnValue['hqdj'] = array(0=>array("{$unlock_item_name}，{$gk_difficuity_names[$need_unlock_difficuity]}{$tools_lang['script_msg_108'][0]}{$gk_stage_names[$need_unlock_stage]}{$tools_lang['script_msg_108'][1]}", 1));			
				
		} else if($unlock_itemid < $need_unlock_itemid) { // 针对已经解锁的玩家将道具消耗掉
			$returnValue['status'] = 0;
			// 删除解锁道具			
			$player->DeleteItemByProto(array($unlock_itemid=>1));
			// 获取背包数据			
			$bagData = $player->GetClientBag();
			$returnValue['message'] = $tools_lang['script_msg_105'];
			$returnValue['list'] = $bagData;
		} else {		
			$returnValue['status'] = 998;
			$returnValue['message'] = $tools_lang['script_msg_106'];
		}				
			
		return $returnValue;
	}
	
	// 神兵利器
	public static function itemUse_27($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
	
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_97']);
			return $returnValue;
		}
		
		$add_item_arr = array(); // 获取的道具
		$message = null;
		$got_msg = null;
		
		// 专属道具数组
		$zswq_arr = array(LGBOW_ITEMID, HALFMOON_ITEMID, RUNWIND_ITEMID, HITTIGER_ITEMID, ARCHLORD_ITEMID);
		$hqjlArr = array();
		
		// 获取使用道具的ID
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];	
		$item_name = $itemproto['Name'];
		
		// 专属武器
		$zsweapon_itemid = $zswq_arr[rand(0, count($zswq_arr) - 1)];
		$add_item_arr[$zsweapon_itemid] = 1;
		$newItmeInfo = toolsModel::getItemInfo($zsweapon_itemid);
		$newItemName = $newItmeInfo['Name'];
		
		$got_msg = "{$newItemName}*1";
		$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}" . $got_msg;	
		$hqjlArr[] = array($newItemName, 1);

		// 检查玩家背包是否足够		
		$addRet = $player->AddItems($add_item_arr, false, array($itemid=>1));		
		if($addRet === false) {
			$returnValue['status'] = 998;
			$returnValue['message'] = $tools_lang['controller_msg_65'];
			return $returnValue;
		}		
		
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
		
		$returnValue['status'] = 0;
		$returnValue['message'] = $message;
		
		/*$nickName = $roleInfo['nickname'];
		$notice_msg1 = array($nickName, $zldj_level);
		$notice_msg2 = array($zldj_level, $got_msg);
		lettersModel::setSysPublicNoice($notice_msg1, 3);
		lettersModel::setSysPublicNoice($notice_msg2, 4);*/
		
		// 获取背包数据
		$bagData = $player->GetClientBag();
		$returnValue['list'] = $bagData;
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = $hqjlArr;
		
		return $returnValue;
	}
	
	// 名将符碎片
	public static function itemUse_28($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $_SGLOBAL, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
	
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_97']);
			return $returnValue;
		}
		
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
	
		$add_item_arr = array(); // 获取的道具
		$message = null;
		$hqjlArr = array();
	
		// 获取使用道具的ID
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];		
		$item_name = $itemproto['Name'];
		if($itemid == 18569) { 
			// 小强化材料包
			$add_item_arr[SMALLQHB_ITEMID] = 1;
			$newitemproto = ConfigLoader::GetItemProto(SMALLQHB_ITEMID);
			
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newitemproto['Name']}*1";
			$hqjlArr[] = array($newitemproto['Name'], 1);
		} 
			
		if(count($add_item_arr) > 0) {		
			$addRet = $player->AddItems($add_item_arr, false, array($itemid=>1));
			if($addRet === false) {
				$returnValue['status'] = 998;
				$returnValue['message'] = $tools_lang['controller_msg_65'];
				return $returnValue;
			}			
		} else {		
			$player->DeleteItemByProto(array($itemid=>1));
		}

		$returnValue['status'] = 0;
		$returnValue['message'] = $message;
	
		// 获取背包数据
		$bagData = $player->GetClientBag();
		$returnValue['list'] = $bagData;
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = $hqjlArr;
			
		return $returnValue;
	}
	
	// 名将符
	public static function itemUse_29($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}

		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
	
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_97']);
			return $returnValue;
		}
			
		$ingot = 0;  // 获取的元宝数量
		$coins = 0;  // 获取的铜钱数量
		$silver = 0; // 获取的银票数量
		$sw = 0;	 // 获取的声望
		$weapon_qh_level = 0;
		$add_item_arr = array(); // 获取的道具
		$message = null;
	
		// 随机装备数组
		$random_weapon_arr = array(
				12=>array(41304,42304,43304,44304),
				15=>array(41405,42405,43405,44405),
				18=>array(41406,42406,43406,44406),
				21=>array(41507,42507,43507,44507)
		);		
		// 橙将卡数组
		$cjk_arr = array('lc'=>10038);
		
		$lb_contents = array(
			'tq1'=>5000,
			'tq2'=>10000,
			'tq3'=>100000,
			'yp'=>50,
			'sw'=>100,
			MIDQHB_ITEMID=>1,
			BIGQHB_ITEMID=>1,
			SMALLXTB_ITEMID=>1,
			MIDXTB_ITEMID=>1,
			12=>1,
			15=>1,
			18=>1,
			21=>1,
			'ingot'=>1000
			//'lc'=>1				
		);
		
		date_default_timezone_set('PRC');
		
		// 控制上午12点以后才会出大奖	
		$curr_hour = date('H');
		if($curr_hour >= 12) {
			$ps = array(			
				'tq1'=>0.38,
				'tq2'=>0.19,
				'tq3'=>0.019,
				'yp'=>0.095,
				'sw'=>0.095,
				MIDQHB_ITEMID=>0.063,
				BIGQHB_ITEMID=>0.038,
				SMALLXTB_ITEMID=>0.050,
				MIDXTB_ITEMID=>0.038,
				12=>0.019,
				15=>0.0076,
				18=>0.0012,
				21=>0.00060,
				'ingot'=>0.00038
				//'lc'=>0.00038			
			);
		} else {
			$ps = array(			
				'tq1'=>0.38,
				'tq2'=>0.19,
				'tq3'=>0.019,
				'yp'=>0.095,
				'sw'=>0.095,
				MIDQHB_ITEMID=>0.063,
				BIGQHB_ITEMID=>0.038,
				SMALLXTB_ITEMID=>0.050,
				MIDXTB_ITEMID=>0.038,
				12=>0.019,
				15=>0.0076,
				18=>0.0012
			);
		}
		
		$none_dj_ps = array(			
			'tq1'=>0.38,
			'tq2'=>0.19,
			'tq3'=>0.019,
			'yp'=>0.095,
			'sw'=>0.095,
			MIDQHB_ITEMID=>0.063,
			BIGQHB_ITEMID=>0.038,
			SMALLXTB_ITEMID=>0.050,
			MIDXTB_ITEMID=>0.038,
			12=>0.019,
			15=>0.0076,
			18=>0.0012
		);
			
		$lb_key = toolsModel::random($ps);
		$got_num = $lb_contents[$lb_key];
		
		// 控制爆率
		if($lb_key == 21) {			
			if(!($ylzf_qh21 = $mc->get(MC.'ylzf_qh21'))) { // 内存无数据
				$ylzf_qh21_arr = array('cs'=>1, 'date'=>time());
				$mc->set(MC.'ylzf_qh21', $ylzf_qh21_arr, 0, 3600 * 24);
			} else {
				$day = date('d', $ylzf_qh21['date']);
				$cur_day = date('d');
				if($cur_day != $day) { // 当天第一次
					$ylzf_qh21_arr = array('cs'=>1, 'date'=>time());
					$mc->set(MC.'ylzf_qh21', $ylzf_qh21_arr, 0, 3600 * 24);
				} else {
					if($ylzf_qh21['cs'] < 4) { // 只出现3次
						$ylzf_qh21['cs'] = $ylzf_qh21['cs'] + 1;
						$ylzf_qh21['date'] = time(); 
						$mc->set(MC.'ylzf_qh21', $ylzf_qh21, 0, 3600 * 24);					
					}
				}
			}
		} else if($lb_key == 'ingot')  {
			if(!($ylzf_ingot = $mc->get(MC.'ylzf_ingot'))) { // 内存无数据
				$ylzf_ingot_arr = array('cs'=>1, 'date'=>time());
				$mc->set(MC.'ylzf_ingot', $ylzf_ingot_arr, 0, 3600 * 24);
			} else {
				$day = date('d', $ylzf_ingot['date']);
				$cur_day = date('d');
				if($cur_day != $day) { // 当天第一次
					$ylzf_ingot_arr = array('cs'=>1, 'date'=>time());
					$mc->set(MC.'ylzf_ingot', $ylzf_ingot_arr, 0, 3600 * 24);
				} else {
					if($ylzf_ingot['cs'] < 2) {
						$ylzf_ingot['cs'] = $ylzf_ingot['cs'] + 1;
						$ylzf_ingot['date'] = time();
						$mc->set(MC.'ylzf_ingot', $ylzf_ingot, 0, 3600 * 24);
					}
				}
			}
		} else if($lb_key == 'lc') {
			if(!($ylzf_lc = $mc->get(MC.'ylzf_lc'))) { // 内存无数据
				$ylzf_lc_arr = array('cs'=>1, 'date'=>time());
				$mc->set(MC.'ylzf_lc', $ylzf_lc_arr, 0, 3600 * 24);		
			} else {
				$day = date('d', $ylzf_lc['date']);
				$cur_day = date('d');
				if($cur_day != $day) { // 当天第一次
					$ylzf_lc_arr = array('cs'=>1, 'date'=>time());
					$mc->set(MC.'ylzf_lc', $ylzf_lc_arr, 0, 3600 * 24);					
				} else {
					if($ylzf_lc['cs'] < 2) {
						$ylzf_lc['cs'] = $ylzf_lc['cs'] + 1;
						$ylzf_lc['date'] = time();
						$mc->set(MC.'ylzf_lc', $ylzf_lc, 0, 3600 * 24);
					}
				}
			}
		}
				
		if($lb_key == 'lc' || $lb_key == 'ingot' || $lb_key == 21) {
			$ylzf_qh21 = $mc->get(MC.'ylzf_qh21');
			$ylzf_ingot = $mc->get(MC.'ylzf_ingot');
			$ylzf_lc = $mc->get(MC.'ylzf_lc');
			if(is_array($ylzf_qh21)) {
				$day = date('d', $ylzf_qh21['date']);
				$cur_day = date('d');
				if($cur_day == $day && $ylzf_qh21['cs'] > 3) {
					unset($ps[21]);
				}
			}
			
			if(is_array($ylzf_ingot)) {
				$day = date('d', $ylzf_ingot['date']);
				$cur_day = date('d');
				if($cur_day == $day && $ylzf_ingot['cs'] > 1) {
					unset($ps['ingot']);
				}
			}
			
			if(is_array($ylzf_lc)) {
				$day = date('d', $ylzf_lc['date']);
				$cur_day = date('d');
				if($cur_day == $day && $ylzf_lc['cs'] > 1) {
					unset($ps['lc']);
				}
			}
			
			if(!array_key_exists($lb_key, $ps)) {							
				$lb_key = toolsModel::random($none_dj_ps);
				$got_num = $lb_contents[$lb_key];
			}
		}
	
		// 获取使用道具的ID
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];
		$item_name = $itemproto['Name'];
		$rarity = 1;
		$hqjlArr = array();
		if($lb_key == 'tq1' || $lb_key == 'tq2' || $lb_key == 'tq3') {
			$coins = $got_num;		
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$got_num}{$tools_lang['controller_msg_68']}";
		} else if($lb_key == 'yp') {			
			$silver = $got_num;		
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$got_num}{$tools_lang['controller_msg_69']}";
		} else if($lb_key == 'sw') {			
			$sw = $got_num;		
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$got_num}{$tools_lang['controller_msg_72']}";
		} else if($lb_key == 'ingot') {			
			$ingot = $got_num;		
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$got_num}{$tools_lang['controller_msg_71']}";
		} else if($lb_key == 12) {				
			// 获取+12随机装备
			$weapon_itemid = $random_weapon_arr[12][rand(0, count($random_weapon_arr[12]) - 1)];
			$add_item_arr[$weapon_itemid] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
			$rarity = $newItmeInfo['Rarity'];
						
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}+12*{$got_num}";
			
			$hqjlArr[] = array($newItemName . '+12', $got_num);
		} else if($lb_key == 15) {				
			// 获取+15随机装备
			$weapon_itemid = $random_weapon_arr[15][rand(0, count($random_weapon_arr[15]) - 1)];
			$add_item_arr[$weapon_itemid] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
			$rarity = $newItmeInfo['Rarity'];
						
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}+15*{$got_num}";
			
			$hqjlArr[] = array($newItemName . '+15', $got_num);
		} else if($lb_key == 18) {				
			// 获取+18随机装备
			$weapon_itemid = $random_weapon_arr[18][rand(0, count($random_weapon_arr[18]) - 1)];
			$add_item_arr[$weapon_itemid] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
			$rarity = $newItmeInfo['Rarity'];
						
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}+18*{$got_num}";
			
			$hqjlArr[] = array($newItemName . '+18', $got_num);
		} else if($lb_key == 21) {				
			// 获取+21随机装备
			$weapon_itemid = $random_weapon_arr[21][rand(0, count($random_weapon_arr[21]) - 1)];
			$add_item_arr[$weapon_itemid] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
			$rarity = $newItmeInfo['Rarity'];
						
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}+21*{$got_num}";
			
			$hqjlArr[] = array($newItemName . '+21', $got_num);
		} else if($lb_key == 'lc') {		
			$add_item_arr[$cjk_arr[$lb_key]] = $got_num;	
			$newItmeInfo = toolsModel::getItemInfo($cjk_arr[$lb_key]);
			$newItemName = $newItmeInfo['Name'];			
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItmeInfo['Name']}*{$got_num}";
			
			$hqjlArr[] = array($newItmeInfo['Name'], $got_num);
		} else if($lb_key == MIDQHB_ITEMID) {
			$add_item_arr[$lb_key] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($lb_key);
			$newItemName = $newItmeInfo['Name'];
				
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}*{$got_num}";
			
			$hqjlArr[] = array($newItemName, $got_num);
		} else if($lb_key == BIGQHB_ITEMID) {
			$add_item_arr[$lb_key] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($lb_key);
			$newItemName = $newItmeInfo['Name'];
				
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}*{$got_num}";
			
			$hqjlArr[] = array($newItemName, $got_num);
		} else if($lb_key == SMALLXTB_ITEMID) {
			$add_item_arr[$lb_key] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($lb_key);
			$newItemName = $newItmeInfo['Name'];
				
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}*{$got_num}";
			
			$hqjlArr[] = array($newItemName, $got_num);
		} else if($lb_key == MIDXTB_ITEMID) {
			$add_item_arr[$lb_key] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($lb_key);
			$newItemName = $newItmeInfo['Name'];
				
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}*{$got_num}";
			
			$hqjlArr[] = array($newItemName, $got_num);
		} 
	
		if(count($add_item_arr) > 0) {
			// 模拟删除奖励礼包	
			$addRet = $player->AddItems($add_item_arr, false, array($itemid=>1));
			if($addRet === false) {
				$returnValue['status'] = 998;
				$returnValue['message'] = $tools_lang['controller_msg_65'];
				return $returnValue;
			}
		} else {
			$player->DeleteItemByProto(array($itemid=>1));
		}		
		
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
		if(count($add_item_arr) > 0) {		
			// 修改强化属性
			$playerBag = $player->GetItems();
			foreach($addRet as $idkey => $wid) {
				$itemproto = ConfigLoader::GetItemProto($playerBag[$wid]['ItemID']);			
				if($itemproto['ItemType'] == 4) {
					$qh_level = 0;
					foreach($random_weapon_arr as $need_qhLevel => $itemids) {
						if(array_search($itemproto['ItemID'], $itemids) !== false) {
							$qh_level = $need_qhLevel;
							break;
						}
					}				
					$player->ModifyItem($wid, array('ItemID'=>$playerBag[$wid]['ItemID'], 'QhLevel'=>$qh_level));
				}
			}
		}		
	
		$currYP = $roleInfo['silver'];
		$currYB = $roleInfo['ingot'];
		$currTQ = $roleInfo['coins'];
		$currSW = $roleInfo['prestige'];
	
		// 加入玩家背包
		if($silver > 0) {
			$updateRole['silver'] = $currYP + $silver;
				
			$returnValue['yp'] = $updateRole['silver'];
			$returnValue['hqyp'] = $silver;
		}
	
		if($coins > 0) {
			$updateRole['coins'] = $currTQ + $coins;
				
			$returnValue['tq'] = $updateRole['coins'];
			$returnValue['hqtq'] = $coins;
		}
	
		if($ingot > 0) {
			$updateRole['ingot'] = $currYB + $ingot;
	
			$returnValue['yb'] = $updateRole['ingot'];
			$returnValue['hqyb'] = $ingot;
		}
		
		if($sw > 0) {
			$updateRole['prestige'] = $currSW + $sw;
		
			$returnValue['sw'] = $updateRole['prestige'];
			$returnValue['hqsw'] = $sw;
		}
			
		if(isset($updateRole)) {
			// 更新玩家当前银票、铜钱、元宝数量
			$whereRole['playersid'] = $playersid;
			$common->updatetable('player', $updateRole, $whereRole);
			$common->updateMemCache(MC.$playersid, $updateRole);
		}
	
		$returnValue['status'] = 0;
		$returnValue['message'] = $message;

		$bagData = $player->GetClientBag();
		
		// 发公告		
		$nickName = $roleInfo['nickname'];
		if($lb_key == 12 || $lb_key == 15 || $lb_key == 18 || $lb_key == 21) {
			if($rarity == 1) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#FFFFFF\">[{$newItemName}+{$lb_key}*1]</FONT>");
			} else if($rarity == 2) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#66ff00\">[{$newItemName}+{$lb_key}*1]</FONT>");
			}  else if($rarity == 3) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#60acfe\">[{$newItemName}+{$lb_key}*1]</FONT>");
			}  else if($rarity == 4) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#ff65f8\">[{$newItemName}+{$lb_key}*1]</FONT>");
			}  else if($rarity == 5) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#ff8929\">[{$newItemName}+{$lb_key}*1]</FONT>");
			}
		} else if($lb_key == 'tq3') {
			lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#60acfe\">[{$coins}{$tools_lang['controller_msg_68']}]</FONT>");
		} else if($lb_key == 'ingot') {
			lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#ff8929\">[{$ingot}{$tools_lang['controller_msg_71']}]</FONT>");
		} else if($lb_key == 'lc') {
			lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#ff8929\">[{$newItemName}*1]</FONT>");
		}
		
		$returnValue['list'] = $bagData;
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = $hqjlArr;
		
		return $returnValue;
	}
	
	// 小背囊包
	public static function itemUse_30($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;	
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);

		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];
		$item_name = $itemproto['Name'];
		
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>"您没有{$item_name}！");
			return $returnValue;
		}

		// 获取道具信息
		$myItemInfo = $playerBag[$djid];
				
		$qhclInfo = toolsModel::getItemInfo(20000);
		
		// 加入玩家背包
		$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);
	
		$addRet = $player->AddItems(array(20000=>5), false, array($myItemInfo['ItemID']=>1));

		if($addRet === false) {
			$returnValue['status'] = 998;
			$returnValue['message'] = $tools_lang['controller_msg_65'];
			return $returnValue;
		} 
						
		$returnValue['status'] = 0;	
		$returnValue['message'] = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}5{$tools_lang['model_msg_17']}{$qhclInfo['Name']}";
		
		$returnValue['list'] = $player->GetClientBag();
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = array(0=>array($qhclInfo['Name'], 5));		
		
		return $returnValue;
	}
	
	// 强化技能包
	public static function itemUse_31($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;	
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
		
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];
		$item_name = $itemproto['Name'];

		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>"您没有{$item_name}！");
			return $returnValue;
		}

		// 获取道具信息
		$myItemInfo = $playerBag[$djid];
		
		// 技能书数组
		$jns_arr = array(0=>10016,1=>10017,2=>10018,3=>10019,4=>10020,5=>10021,6=>10022,7=>10023,8=>10024,9=>10025,10=>10026,11=>10027,12=>10028,13=>10029,14=>10030);
		$jns_idx = array_rand($jns_arr, 2);
		$jns_itemid1 = $jns_arr[$jns_idx[0]];
		$jns_itemid2 = $jns_arr[$jns_idx[1]];
		
		$qhclInfo1 = toolsModel::getItemInfo($jns_itemid1);
		$qhclInfo2 = toolsModel::getItemInfo($jns_itemid2);
		
		// 加入玩家背包
		$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);
	
		$addRet = $player->AddItems(array(10001=>10, $jns_itemid1=>1, $jns_itemid2=>1), false, array($myItemInfo['ItemID']=>1));

		if($addRet === false) {
			$returnValue['status'] = 998;
			$returnValue['message'] = $tools_lang['controller_msg_65'];
			return $returnValue;
		}
						
		$returnValue['status'] = 0;	
		$returnValue['message'] = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}10{$tools_lang['model_msg_17']}{$tools_lang['script_msg_99'][1]}，{$qhclInfo1['Name']}*1，{$qhclInfo2['Name']}*1";
		
		$returnValue['list'] = $player->GetClientBag();
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = array(0=>array($tools_lang['script_msg_99'][1], 10), 1=>array($qhclInfo1['Name'], 1), 2=>array($qhclInfo2['Name'], 1));		
		
		return $returnValue;
	}
	
	// 圣诞卡片
	public static function itemUse_32($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;	
		}
		
		if(toolsModel::getBgSyGs($playersid) == 0)
			return array ('status' => 998, 'message' => $tools_lang['model_msg_13']);
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
		
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];
		$item_name = $itemproto['Name'];

		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>"您没有{$item_name}！");
			return $returnValue;
		}

		// 获取道具信息
		$myItemInfo = $playerBag[$djid];
		
		// 套装碎片数组
		$tz_arr = array(0=>18576, 1=>18577, 2=>18578, 3=>18579);
		$tz_idx = rand(0, 3);
		$tz_itemid = $tz_arr[$tz_idx];		
		
		$tzspInfo = toolsModel::getItemInfo($tz_itemid);	
		
		// 加入玩家背包
		$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);
	
		$addRet = $player->AddItems(array($tz_itemid=>1), false, array($myItemInfo['ItemID']=>1));

		if($addRet === false) {
			$returnValue['status'] = 998;
			$returnValue['message'] = $tools_lang['controller_msg_65'];
			return $returnValue;
		}
						
		$returnValue['status'] = 0;	
		$returnValue['message'] = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}1{$tools_lang['model_msg_17']}{$tzspInfo['Name']}";
		
		$nickName = $roleInfo['nickname'];
		lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_101'][1]}“{$item_name}”，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#66ff00\">[{$tzspInfo['Name']}]</FONT>");
		
		$returnValue['list'] = $player->GetClientBag();
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = array(0=>array($tzspInfo['Name'], 1));		
		
		return $returnValue;
	}
	
	// 套装【风】 套装【林】 套装【火】 套装【山】
	public static function itemUse_33($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;	
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
		
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];
		$item_name = $itemproto['Name'];

		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>"您没有{$item_name}！");
			return $returnValue;
		}

		// 获取道具信息
		$myItemInfo = $playerBag[$djid];
		
		$jgssp_itemid = JGSSP_ITEMID;				
		$jgsspInfo = toolsModel::getItemInfo($jgssp_itemid);	
		
		// 加入玩家背包
		$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);
	
		$addRet = $player->AddItems(array($jgssp_itemid=>5), false, array($myItemInfo['ItemID']=>1));

		if($addRet === false) {
			$returnValue['status'] = 998;
			$returnValue['message'] = $tools_lang['controller_msg_65'];
			return $returnValue;
		}
						
		$returnValue['status'] = 0;	
		$returnValue['message'] = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}5{$tools_lang['model_msg_17']}{$jgsspInfo['Name']}";
		
		$returnValue['list'] = $player->GetClientBag();
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = array(0=>array($jgsspInfo['Name'], 5));		
		
		return $returnValue;
	}
	
	// 武魂包
	public static function itemUse_34($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;	
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
		
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];
		$item_name = $itemproto['Name'];

		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>"您没有{$item_name}！");
			return $returnValue;
		}

		// 获取道具信息
		$myItemInfo = $playerBag[$djid];
		
		if($myItemInfo['ItemID'] == 18584) {// 绿武魂包		
			$whInfo = toolsModel::getItemInfo(18580);			
		} else if($myItemInfo['ItemID'] == 18585) {// 蓝武魂包		
			$whInfo = toolsModel::getItemInfo(18581);
		} else if($myItemInfo['ItemID'] == 18586) {// 紫武魂包
			$whInfo = toolsModel::getItemInfo(18582);
		} else if($myItemInfo['ItemID'] == 18587) {// 橙武魂包
			$whInfo = toolsModel::getItemInfo(18583);
		}
		
		// 加入玩家背包
		$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);
	
		$addRet = $player->AddItems(array($whInfo['ItemID']=>10), false, array($myItemInfo['ItemID']=>1));

		if($addRet === false) {
			$returnValue['status'] = 998;
			$returnValue['message'] = $tools_lang['controller_msg_65'];
			return $returnValue;
		}
		
		$returnValue['status'] = 0;	
		$returnValue['message'] = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}10{$tools_lang['model_msg_17']}{$whInfo['Name']}";
		
		$returnValue['list'] = $player->GetClientBag();
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = array(0=>array($whInfo['Name'], 10));		
		
		return $returnValue;
	}
	
	// 元宵花灯
	public static function itemUse_35($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}

		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
	
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_97']);
			return $returnValue;
		}
			
		$ingot = 0;  // 获取的元宝数量
		$coins = 0;  // 获取的铜钱数量
		$silver = 0; // 获取的银票数量
		$sw = 0;	 // 获取的声望
		$weapon_qh_level = 0;
		$add_item_arr = array(); // 获取的道具
		$message = null;
	
		// 随机装备数组
		$random_weapon_arr = array(
				12=>array(41304,42304,43304,44304),
				15=>array(41405,42405,43405,44405),
				18=>array(41406,42406,43406,44406),
				21=>array(41507,42507,43507,44507)
		);		
				
		$lb_contents = array(
			18580=>3,	// 绿武魂
			18581=>2,
			18582=>1,
			18583=>1,	// 橙武魂
			'sw1'=>20,
			'sw2'=>50,
			'sw3'=>100,
			18588=>array(2,5), // 矿工锄					
			BIGQHB_ITEMID=>1,			
			MIDXTB_ITEMID=>1,
			12=>1,
			15=>1,
			18=>1,
			21=>1
		);
		
		$ps = array(			
			18580=>0.3542,	// 绿武魂
			18581=>0.1771,
			18582=>0.0885,
			18583=>0.0035,	// 橙武魂
			'sw1'=>0.0885,
			'sw2'=>0.0354,
			'sw3'=>0.0177,
			'18588_1'=>0.1771,
			'18588_2'=>0.0177, // 矿工锄					
			BIGQHB_ITEMID=>0.0177,			
			MIDXTB_ITEMID=>0.0177,
			12=>0.0354,
			15=>0.0005,
			18=>0.0002,
			21=>0.0001			
		);
	
			
		$lb_key = toolsModel::random($ps);
		if($lb_key == '18588_1') {
			$lb_key = 18588;
			$got_num = $lb_contents[$lb_key][0];
		} else if($lb_key == '18588_2') {
			$lb_key = 18588;
			$got_num = $lb_contents[$lb_key][1];	
		}else {
			$got_num = $lb_contents[$lb_key];
		}
			
		// 获取使用道具的ID
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];
		$item_name = $itemproto['Name'];
		$rarity = 1;
		$hqjlArr = array();
		if($lb_key == 'sw1' || $lb_key == 'sw2' || $lb_key == 'sw3') {			
			$sw = $got_num;		
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$got_num}{$tools_lang['controller_msg_72']}";
		} else if($lb_key == 12) {				
			// 获取+12随机装备
			$weapon_itemid = $random_weapon_arr[12][rand(0, count($random_weapon_arr[12]) - 1)];
			$add_item_arr[$weapon_itemid] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
			$rarity = $newItmeInfo['Rarity'];
						
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}+12*{$got_num}";
			
			$hqjlArr[] = array($newItemName . '+12', $got_num);
		} else if($lb_key == 15) {				
			// 获取+15随机装备
			$weapon_itemid = $random_weapon_arr[15][rand(0, count($random_weapon_arr[15]) - 1)];
			$add_item_arr[$weapon_itemid] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
			$rarity = $newItmeInfo['Rarity'];
						
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}+15*{$got_num}";
			
			$hqjlArr[] = array($newItemName . '+15', $got_num);
		} else if($lb_key == 18) {				
			// 获取+18随机装备
			$weapon_itemid = $random_weapon_arr[18][rand(0, count($random_weapon_arr[18]) - 1)];
			$add_item_arr[$weapon_itemid] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
			$rarity = $newItmeInfo['Rarity'];
						
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}+18*{$got_num}";
			
			$hqjlArr[] = array($newItemName . '+18', $got_num);
		} else if($lb_key == 21) {				
			// 获取+21随机装备
			$weapon_itemid = $random_weapon_arr[21][rand(0, count($random_weapon_arr[21]) - 1)];
			$add_item_arr[$weapon_itemid] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
			$rarity = $newItmeInfo['Rarity'];
						
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}+21*{$got_num}";
			
			$hqjlArr[] = array($newItemName . '+21', $got_num);
		} else if($lb_key == BIGQHB_ITEMID) {
			$add_item_arr[$lb_key] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($lb_key);
			$newItemName = $newItmeInfo['Name'];
				
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}*{$got_num}";
			
			$hqjlArr[] = array($newItemName, $got_num);
		} else if($lb_key == MIDXTB_ITEMID) {
			$add_item_arr[$lb_key] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($lb_key);
			$newItemName = $newItmeInfo['Name'];
				
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}*{$got_num}";
			
			$hqjlArr[] = array($newItemName, $got_num);
		} else if($lb_key == 18580 || $lb_key == 18581 || $lb_key == 18582 || $lb_key == 18583 || $lb_key == 18588) {
			$add_item_arr[$lb_key] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($lb_key);
			$newItemName = $newItmeInfo['Name'];
			$rarity = $newItmeInfo['Rarity'];
						
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}*{$got_num}";
			
			$hqjlArr[] = array($newItemName, $got_num);
		}
	
		if(count($add_item_arr) > 0) {
			// 模拟删除奖励礼包	
			$addRet = $player->AddItems($add_item_arr, false, array($itemid=>1));
			if($addRet === false) {
				$returnValue['status'] = 998;
				$returnValue['message'] = $tools_lang['controller_msg_65'];
				return $returnValue;
			}
		} else {
			$player->DeleteItemByProto(array($itemid=>1));
		}		
		
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
		if(count($add_item_arr) > 0) {		
			// 修改强化属性
			$playerBag = $player->GetItems();
			foreach($addRet as $idkey => $wid) {
				$itemproto = ConfigLoader::GetItemProto($playerBag[$wid]['ItemID']);			
				if($itemproto['ItemType'] == 4) {
					$qh_level = 0;
					foreach($random_weapon_arr as $need_qhLevel => $itemids) {
						if(array_search($itemproto['ItemID'], $itemids) !== false) {
							$qh_level = $need_qhLevel;
							break;
						}
					}				
					$player->ModifyItem($wid, array('ItemID'=>$playerBag[$wid]['ItemID'], 'QhLevel'=>$qh_level));
				}
			}
		}		
	
		$currSW = $roleInfo['prestige'];
	
		// 加入玩家背包
		if($sw > 0) {
			$updateRole['prestige'] = $currSW + $sw;
		
			$returnValue['sw'] = $updateRole['prestige'];
			$returnValue['hqsw'] = $sw;
		}
			
		if(isset($updateRole)) {
			// 更新玩家当前银票、铜钱、元宝数量
			$whereRole['playersid'] = $playersid;
			$common->updatetable('player', $updateRole, $whereRole);
			$common->updateMemCache(MC.$playersid, $updateRole);
		}
	
		$returnValue['status'] = 0;
		$returnValue['message'] = $message;

		$bagData = $player->GetClientBag();
		
		// 发公告		
		$nickName = $roleInfo['nickname'];
		if($lb_key == 12 || $lb_key == 15 || $lb_key == 18 || $lb_key == 21 ) {
			if($rarity == 1) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#FFFFFF\">[{$newItemName}+{$lb_key}*1]</FONT>");
			} else if($rarity == 2) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#66ff00\">[{$newItemName}+{$lb_key}*1]</FONT>");
			}  else if($rarity == 3) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#60acfe\">[{$newItemName}+{$lb_key}*1]</FONT>");
			}  else if($rarity == 4) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#ff65f8\">[{$newItemName}+{$lb_key}*1]</FONT>");
			}  else if($rarity == 5) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#ff8929\">[{$newItemName}+{$lb_key}*1]</FONT>");
			}
		} else if($lb_key == 'sw1' || $lb_key == 'sw2' || $lb_key == 'sw3') {
			lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#60acfe\">[{$sw}{$tools_lang['controller_msg_72']}]</FONT>");
		} else if($lb_key == BIGQHB_ITEMID || $lb_key == MIDXTB_ITEMID || $lb_key == 18588) {
			lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#60acfe\">[{$newItemName}*{$got_num}]</FONT>");
		} else if($lb_key == 18580 || $lb_key == 18581 || $lb_key == 18582 || $lb_key == 18583) {
			if($rarity == 1) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#FFFFFF\">[{$newItemName}*{$got_num}]</FONT>");
			} else if($rarity == 2) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#66ff00\">[{$newItemName}*{$got_num}]</FONT>");
			}  else if($rarity == 3) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#60acfe\">[{$newItemName}*{$got_num}]</FONT>");
			}  else if($rarity == 4) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#ff65f8\">[{$newItemName}*{$got_num}]</FONT>");
			}  else if($rarity == 5) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#ff8929\">[{$newItemName}*{$got_num}]</FONT>");
			}
		}
		
		$returnValue['list'] = $bagData;
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = $hqjlArr;
		
		return $returnValue;
	}
	
	// 新年红包、吉祥礼包、如意礼包
	public static function itemUse_36($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}

		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
	
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_97']);
			return $returnValue;
		}
			
		$ingot = 0;  // 获取的元宝数量
		$coins = 0;  // 获取的铜钱数量
		$silver = 0; // 获取的银票数量
		$sw = $got_num = 0;	 // 获取的声望		
		$add_item_arr = array(); // 获取的道具
		$message = null;
	
		// 随机对联、爆竹、饺子、年糕
		$random_newyear_arr = array(18602,18603,18604,18605);		
			
		// 获取使用道具的ID
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];
		$item_name = $itemproto['Name'];
		$rarity = 1;
		$hqjlArr = array();
		if($itemid == 18600) { // 吉祥礼包		
			$sw = $got_num = 300;		
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$got_num}{$tools_lang['controller_msg_72']}";
		} else if($itemid == 18599 || $itemid == 18601) { // 新年红包、如意礼包
			if($itemid == 18599) {
				$got_num = 1;
				$lb_key = $random_newyear_arr[rand(0, count($random_newyear_arr) - 1)];
			} else {
				$got_num = 10;
				$lb_key = 18599;
			}
			$add_item_arr[$lb_key] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($lb_key);
			$newItemName = $newItmeInfo['Name'];
			$rarity = $newItmeInfo['Rarity'];
			
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}*{$got_num}";
			
			$hqjlArr[] = array($newItemName, $got_num);
		}
	
		if(count($add_item_arr) > 0) {
			// 模拟删除奖励礼包	
			$addRet = $player->AddItems($add_item_arr, false, array($itemid=>1));
			if($addRet === false) {
				$returnValue['status'] = 998;
				$returnValue['message'] = $tools_lang['controller_msg_65'];
				return $returnValue;
			}
		} else {
			$player->DeleteItemByProto(array($itemid=>1));
		}		
		
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
		
		$currSW = $roleInfo['prestige'];
	
		// 加入玩家背包
		if($sw > 0) {
			$updateRole['prestige'] = $currSW + $sw;
		
			$returnValue['sw'] = $updateRole['prestige'];
			$returnValue['hqsw'] = $sw;
		}
			
		if(isset($updateRole)) {
			// 更新玩家当前银票、铜钱、元宝数量
			$whereRole['playersid'] = $playersid;
			$common->updatetable('player', $updateRole, $whereRole);
			$common->updateMemCache(MC.$playersid, $updateRole);
		}
	
		$returnValue['status'] = 0;
		$returnValue['message'] = $message;

		$bagData = $player->GetClientBag();
		
		// 发公告		
		$nickName = $roleInfo['nickname'];
		if($itemid == 18599) {
			if($rarity == 1) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#FFFFFF\">[{$newItemName}*{$got_num}]</FONT>");
			} else if($rarity == 2) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#66ff00\">[{$newItemName}*{$got_num}]</FONT>");
			}  else if($rarity == 3) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#60acfe\">[{$newItemName}*{$got_num}]</FONT>");
			}  else if($rarity == 4) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#ff65f8\">[{$newItemName}*{$got_num}]</FONT>");
			}  else if($rarity == 5) {
				lettersModel::setPublicNotice("{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$nickName}]</FONT>{$tools_lang['script_msg_103']}{$item_name}，{$tools_lang['controller_msg_67']}<FONT COLOR=\"#ff8929\">[{$newItemName}*{$got_num}]</FONT>");
			}
		}
		
		$returnValue['list'] = $bagData;
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = $hqjlArr;
		
		return $returnValue;
	}
	
	// 新年饺子、新年爆竹、新年对联、新年年糕、心想事成
	public static function itemUse_37($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}

		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
	
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_97']);
			return $returnValue;
		}
			
		$ingot = 0;  // 获取的元宝数量
		$coins = 0;  // 获取的铜钱数量
		$silver = 0; // 获取的银票数量
		$sw = $got_num = 0;	 // 获取的声望		
		$add_item_arr = array(); // 获取的道具
		$message = null;
	
		// 获取使用道具的ID
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];
		$item_name = $itemproto['Name'];
		$rarity = 1;
		$hqjlArr = array();
		if($itemid == 18602 || $itemid == 18603 || $itemid == 18604 || $itemid == 18605) { // 新年饺子、新年爆竹、新年对联、新年年糕
			$got_num = 5;
			$lb_key = JGSSP_ITEMID;
			
			$add_item_arr[$lb_key] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($lb_key);
			$newItemName = $newItmeInfo['Name'];
			$rarity = $newItmeInfo['Rarity'];
			
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}*{$got_num}";
			
			$hqjlArr[] = array($newItemName, $got_num);
		} else if($itemid == 18607) { // 心想事成
			$got_num = 2;
			$lb_key = JGS_ITEMID;
			
			$add_item_arr[$lb_key] = $got_num;
			$newItmeInfo = toolsModel::getItemInfo($lb_key);
			$newItemName = $newItmeInfo['Name'];
			$rarity = $newItmeInfo['Rarity'];
			
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}*{$got_num}";
			
			$hqjlArr[] = array($newItemName, $got_num);
		}
	
		if(count($add_item_arr) > 0) {
			// 模拟删除奖励礼包	
			$addRet = $player->AddItems($add_item_arr, false, array($itemid=>1));
			if($addRet === false) {
				$returnValue['status'] = 998;
				$returnValue['message'] = $tools_lang['controller_msg_65'];
				return $returnValue;
			}
		} else {
			$player->DeleteItemByProto(array($itemid=>1));
		}		
		
		$returnValue['status'] = 0;
		$returnValue['message'] = $message;

		$bagData = $player->GetClientBag();		
		
		$returnValue['list'] = $bagData;
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = $hqjlArr;
		
		return $returnValue;
	}
	
	
	// 特权礼包
	public static function itemUse_38($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}

		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
	
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_97']);
			return $returnValue;
		}
			
		$ingot = 0;  // 获取的元宝数量
		$coins = 0;  // 获取的铜钱数量
		$silver = 0; // 获取的银票数量
		$sw = $got_num = 0;	 // 获取的声望		
		$add_item_arr = array(); // 获取的道具
		$message = null;
				
		// 获取使用道具的ID
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];
		$item_name = $itemproto['Name'];
		$rarity = 1;
		$hqjlArr = array();
	
		$add_item_arr[JGS_ITEMID] = $got_num = 10;
		$newItmeInfo = toolsModel::getItemInfo(JGS_ITEMID);
		$newItemName = $newItmeInfo['Name'];
		$rarity = $newItmeInfo['Rarity'];
			
		$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}*{$got_num},{$tools_lang['controller_msg_68']}*20000,{$tools_lang['controller_msg_69']}*50";
			
		$hqjlArr[] = array($newItemName, $got_num);
		$silver = 50;
		$coins = 20000;
	
		if(count($add_item_arr) > 0) {
			// 模拟删除奖励礼包	
			$addRet = $player->AddItems($add_item_arr, false, array($itemid=>1));
			if($addRet === false) {
				$returnValue['status'] = 998;
				$returnValue['message'] = $tools_lang['controller_msg_65'];
				return $returnValue;
			}
		} else {
			$player->DeleteItemByProto(array($itemid=>1));
		}		
		
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
		
		$currYP = $roleInfo['silver'];
		$currYB = $roleInfo['ingot'];
		$currTQ = $roleInfo['coins'];
		$currSW = $roleInfo['prestige'];
	
		// 加入玩家背包
		if($silver > 0) {
			$updateRole['silver'] = $currYP + $silver;
				
			$returnValue['yp'] = $updateRole['silver'];
			$returnValue['hqyp'] = $silver;
		}
	
		if($coins > 0) {
			$updateRole['coins'] = $currTQ + $coins;
				
			$returnValue['tq'] = $updateRole['coins'];
			$returnValue['hqtq'] = $coins;
		}
	
		if($ingot > 0) {
			$updateRole['ingot'] = $currYB + $ingot;
	
			$returnValue['yb'] = $updateRole['ingot'];
			$returnValue['hqyb'] = $ingot;
		}
		
		if($sw > 0) {
			$updateRole['prestige'] = $currSW + $sw;
		
			$returnValue['sw'] = $updateRole['prestige'];
			$returnValue['hqsw'] = $sw;
		}
			
		if(isset($updateRole)) {
			// 更新玩家当前银票、铜钱、元宝数量
			$whereRole['playersid'] = $playersid;
			$common->updatetable('player', $updateRole, $whereRole);
			$common->updateMemCache(MC.$playersid, $updateRole);
		}
	
		$returnValue['status'] = 0;
		$returnValue['message'] = $message;

		$bagData = $player->GetClientBag();
		
		$returnValue['list'] = $bagData;
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = $hqjlArr;
		
		return $returnValue;
	}
	
	// 声望卡
	public static function itemUse_39($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}

		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
	
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_97']);
			return $returnValue;
		}
			
		$ingot = 0;  // 获取的元宝数量
		$coins = 0;  // 获取的铜钱数量
		$silver = 0; // 获取的银票数量
		$sw = $got_num = 0;	 // 获取的声望		
		$add_item_arr = array(); // 获取的道具
		$message = null;
				
		// 获取使用道具的ID
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];
		$item_name = $itemproto['Name'];
		$rarity = 1;
		$hqjlArr = array();
	
		$sw = $got_num = 100;		
		$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$got_num}{$tools_lang['controller_msg_72']}";
			
		if(count($add_item_arr) > 0) {
			// 模拟删除奖励礼包	
			$addRet = $player->AddItems($add_item_arr, false, array($itemid=>1));
			if($addRet === false) {
				$returnValue['status'] = 998;
				$returnValue['message'] = $tools_lang['controller_msg_65'];
				return $returnValue;
			}
		} else {
			$player->DeleteItemByProto(array($itemid=>1));
		}		
		
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
		
		$currYP = $roleInfo['silver'];
		$currYB = $roleInfo['ingot'];
		$currTQ = $roleInfo['coins'];
		$currSW = $roleInfo['prestige'];
	
		// 加入玩家背包
		if($silver > 0) {
			$updateRole['silver'] = $currYP + $silver;
				
			$returnValue['yp'] = $updateRole['silver'];
			$returnValue['hqyp'] = $silver;
		}
	
		if($coins > 0) {
			$updateRole['coins'] = $currTQ + $coins;
				
			$returnValue['tq'] = $updateRole['coins'];
			$returnValue['hqtq'] = $coins;
		}
	
		if($ingot > 0) {
			$updateRole['ingot'] = $currYB + $ingot;
	
			$returnValue['yb'] = $updateRole['ingot'];
			$returnValue['hqyb'] = $ingot;
		}
		
		if($sw > 0) {
			$updateRole['prestige'] = $currSW + $sw;
		
			$returnValue['sw'] = $updateRole['prestige'];
			$returnValue['hqsw'] = $sw;
		}
			
		if(isset($updateRole)) {
			// 更新玩家当前银票、铜钱、元宝数量
			$whereRole['playersid'] = $playersid;
			$common->updatetable('player', $updateRole, $whereRole);
			$common->updateMemCache(MC.$playersid, $updateRole);
		}
	
		$returnValue['status'] = 0;
		$returnValue['message'] = $message;

		$bagData = $player->GetClientBag();
		
		$returnValue['list'] = $bagData;
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = $hqjlArr;
		
		return $returnValue;
	}
	
	// 强掠令包
	public static function itemUse_40($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}

		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
	
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_97']);
			return $returnValue;
		}
			
		$ingot = 0;  // 获取的元宝数量
		$coins = 0;  // 获取的铜钱数量
		$silver = 0; // 获取的银票数量
		$sw = $got_num = 0;	 // 获取的声望		
		$add_item_arr = array(); // 获取的道具
		$message = null;
				
		// 获取使用道具的ID
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];
		$item_name = $itemproto['Name'];
		$rarity = 1;
		$hqjlArr = array();
	
		$add_item_arr[18611] = $got_num = 10;
		$newItmeInfo = toolsModel::getItemInfo(18611);
		$newItemName = $newItmeInfo['Name'];
		$rarity = $newItmeInfo['Rarity'];		
		$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}*{$got_num}";
		$hqjlArr[] = array($newItemName, $got_num);
			
		if(count($add_item_arr) > 0) {
			// 模拟删除奖励礼包	
			$addRet = $player->AddItems($add_item_arr, false, array($itemid=>1));
			if($addRet === false) {
				$returnValue['status'] = 998;
				$returnValue['message'] = $tools_lang['controller_msg_65'];
				return $returnValue;
			}
		} else {
			$player->DeleteItemByProto(array($itemid=>1));
		}		
	
		$returnValue['status'] = 0;
		$returnValue['message'] = $message;

		$bagData = $player->GetClientBag();
		
		$returnValue['list'] = $bagData;
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = $hqjlArr;
		
		return $returnValue;
	}
	
	// 评分礼包
	public static function itemUse_41($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}

		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
	
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_97']);
			return $returnValue;
		}
			
		$ingot = 0;  // 获取的元宝数量
		$coins = 0;  // 获取的铜钱数量
		$silver = 0; // 获取的银票数量
		$sw = $got_num = 0;	 // 获取的声望		
		$add_item_arr = array(); // 获取的道具
		$message = null;
				
		// 获取使用道具的ID
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];
		$item_name = $itemproto['Name'];
		$rarity = 1;
		$hqjlArr = array();
	
		$add_item_arr[BLUECARD_ITEMID] = $got_num = 1;
		$newItmeInfo = toolsModel::getItemInfo(BLUECARD_ITEMID);
		$newItemName = $newItmeInfo['Name'];
		$rarity = $newItmeInfo['Rarity'];
			
		$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}{$newItemName}*{$got_num},{$tools_lang['controller_msg_68']}*20000,{$tools_lang['controller_msg_69']}*50";
			
		$hqjlArr[] = array($newItemName, $got_num);
		$silver = 10;
		$coins = 20000;
	
		if(count($add_item_arr) > 0) {
			// 模拟删除奖励礼包	
			$addRet = $player->AddItems($add_item_arr, false, array($itemid=>1));
			if($addRet === false) {
				$returnValue['status'] = 998;
				$returnValue['message'] = $tools_lang['controller_msg_65'];
				return $returnValue;
			}
		} else {
			$player->DeleteItemByProto(array($itemid=>1));
		}		
		
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
		
		$currYP = $roleInfo['silver'];
		$currYB = $roleInfo['ingot'];
		$currTQ = $roleInfo['coins'];
		$currSW = $roleInfo['prestige'];
	
		// 加入玩家背包
		if($silver > 0) {
			$updateRole['silver'] = $currYP + $silver;
				
			$returnValue['yp'] = $updateRole['silver'];
			$returnValue['hqyp'] = $silver;
		}
	
		if($coins > 0) {
			$updateRole['coins'] = $currTQ + $coins;
				
			$returnValue['tq'] = $updateRole['coins'];
			$returnValue['hqtq'] = $coins;
		}
	
		if($ingot > 0) {
			$updateRole['ingot'] = $currYB + $ingot;
	
			$returnValue['yb'] = $updateRole['ingot'];
			$returnValue['hqyb'] = $ingot;
		}
		
		if($sw > 0) {
			$updateRole['prestige'] = $currSW + $sw;
		
			$returnValue['sw'] = $updateRole['prestige'];
			$returnValue['hqsw'] = $sw;
		}
			
		if(isset($updateRole)) {
			// 更新玩家当前银票、铜钱、元宝数量
			$whereRole['playersid'] = $playersid;
			$common->updatetable('player', $updateRole, $whereRole);
			$common->updateMemCache(MC.$playersid, $updateRole);
		}
	
		$returnValue['status'] = 0;
		$returnValue['message'] = $message;

		$bagData = $player->GetClientBag();
		
		$returnValue['list'] = $bagData;
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = $hqjlArr;
		
		return $returnValue;
	}
	
	// 基本通用使用道具脚本
	public static function itemUse_42($playersid, $djid, $playerBag){
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;

		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}

		$player = $G_PlayerMgr->GetPlayer($playersid );
		$roleInfo = $player->baseinfo_;

		if(!$player){
			return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
		}
	
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_97']);
			return $returnValue;
		}

		// 获取道具信息
		$myItemInfo = $playerBag[$djid];
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemExchg = ConfigLoader::GetUseItemCfg($playerBag[$djid]['ItemID']);

		// 检查执行条件
		if(isset($itemExchg['cond'])){
			foreach($itemExchg['cond'] as $key=>$condValue){
				switch($key){
				case 'plevel':
					if($roleInfo['player_level']<$condValue){
						$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_92']);
						return $returnValue;
					}
					break;
				}
			}
		}

		// 检查特殊道具限制信息
		$exchgs = array();
		$mdfUseInfo = array();
		if(isset($itemExchg['limit'])){
			$limitInfo = array();
			$useHistory = toolsModel::getUseInfo($playersid, $myItemInfo['ItemID']);
			foreach($itemExchg['limit'] as $key=>$_limit){
				foreach($useHistory as $get_id=>$history){
					// 这里先加1表示当前使用的第几个
					$history['use_num'] ++;
					if($get_id != $_limit['itemId']){
						continue;
					}
					// 过虑不符合条件
					if($history['use_num']<$_limit['low']
					   ||$history['use_num']>$_limit['top']
					   ||$history['get_num']>=$_limit['limit']){
						continue;
					}

					// 必需要得到物品
					if($history['use_num'] == $_limit['top']){
						$exchgs = array('items'=>array($_limit['itemId']=>1));
						$_use_his = toolsModel::getUseInfo($playersid, $myItemInfo['ItemID']);
						$mdfUseInfo = $_use_his[$_limit['itemId']];
						$mdfUseInfo['use_num']++;
						$mdfUseInfo['get_num']++;
						break;
					}

					// 有一定概率出的物品
					$limitInfo[] = $_limit;
				}
				if(!empty($exchgs)){
					break;
				}

			}

			// 对于第一次使用道具没有使用历史
			if(empty($useHistory)){
				foreach($itemExchg['limit'] as $key=>$_limit){
					if($_limit['low']==1){
						$limitInfo[] = $_limit;
					}
				}

				// 初始化历史道具使用数据
				foreach($limitInfo as $_limit){
					$_history = array('playersid'=>$playersid,
									  'use_id'=>$myItemInfo['ItemID'],
									  'get_id'=>$_limit['itemId'],
									  'use_num'=>0,
									  'get_num'=>0);
					toolsModel::setUseInfo($_history);
				}
			}

			// 计算道具出现概率
			if(empty($exchgs)){
				$pdList = array();
				$other = 100;
				foreach($limitInfo as $key=>$_limit){
					$other -= $_limit['pd'];
					$pdList[$key] = $_limit['pd'];
				}
				$pdList['other'] = $other;

				$choice = toolsModel::random($pdList);
				if('other' !== $choice){
					$exchgs = array('items'=>array($limitInfo[$choice]['itemId']=>1));
					// 准备更新成功后的更新数据
					$_use_his = toolsModel::getUseInfo($playersid, $myItemInfo['ItemID']);
					$mdfUseInfo = $_use_his[$limitInfo[$choice]['itemId']];
					$mdfUseInfo['use_num']++;
					$mdfUseInfo['get_num']++;
				}else{
					// 只使用道具而没有得到限制物品
					if(!empty($useHistory)){
						foreach($useHistory as $_history){
							$mdfUseInfo = $_history;
							$mdfUseInfo['use_num']++;
							break;
						}
					}else{
						$mdfUseInfo = array('playersid'=>$playersid,
											'use_id'=>$myItemInfo['ItemID'],
											'get_id'=>$itemExchg['limit'][0]['itemId'],
											'use_num'=>1,
											'get_num'=>0);
					}
				}
			}
		}

		$useNum = 0;
		if(empty($exchgs)){
			$pdList = array();
			foreach($itemExchg['list'] as $key=>$config){
				$pdList[$key] = $config['pd'];
			}
			$choice = toolsModel::random($pdList);
			$exchgs = $itemExchg['list'][$choice]['exchg'];
		}else{
			// 标记当前是特殊限制物品
			$getNum = 1;
		}

		// 格式化数据
		$addList = array();
		$addItems = array();
		$jf = 0;
		foreach($exchgs as $key=>$addInfo){
			switch($key){
			case 'tq':
			case 'yp':
			case 'yb':
			case 'sw':
			case 'jl':
			case 'jf':
				$addList[$key] = $addInfo;
				break;
			case 'jf':
				$jf = $addInfo;
				break;
			case 'items':
				foreach($addInfo as $itemId=>$addNum){
					$_itemId = intval($itemId);
					$addItems[$_itemId] = $addNum;
				}
				break;
			}
		}

		$getMsgList = array();
		// 道具虚拟填加
		$is_del_item = false;
		$hqjlArr = array();
		if(!empty($addItems)){
			$addRet = $player->AddItems($addItems, false, array($itemproto['ItemID']=>1));
			if($addRet === false) {
				$returnValue['status'] = 998;
				$returnValue['message'] = $tools_lang['controller_msg_65'];
				return $returnValue;
			}else{
				foreach($addItems as $_itemId=>$sl){
					$addItemInfo = ConfigLoader::GetItemProto($_itemId);
					$hqjlArr[] = array($addItemInfo['Name'], $sl);
					$getMsgList[] = $sl . $tools_lang['model_msg_17'] . $addItemInfo['Name'];
					$is_del_item = true;
				}
				// 发跑马灯消息
				useItemByParam::setPublicNotice($playersid, $itemproto['ItemID'], $hqjlArr);
			}
		}

		// 修改金钱,声望,军粮
		if(!empty($addList)){
			$roleInfo['playersid'] = $playersid;
			roleModel::getRoleInfo($roleInfo);
			if(!$is_del_item){
				$delRet = $player->DeleteItemByProto(array($itemproto['ItemID']=>1));
				$is_del_item = true;
			}

			$updateRole = array();
			if(isset($addList['tq'])){
				$updateRole['coins']    = $roleInfo['coins'] + $addList['tq'];
				$returnValue['tq']      = $updateRole['coins'];
				$returnValue['hqtq']    = $addList['tq'];

				$getMsgList[] = $addList['tq'] . $tools_lang['model_msg_17'] . $tools_lang['controller_msg_68'];
				//$hqjlArr[] = array($tools_lang['controller_msg_68'], $addList['tq']);
			}else if(isset($addList['yp'])){
				$updateRole['silver']   = $roleInfo['silver'] + $addList['yp'];
				$returnValue['yp']      = $updateRole['silver'];
				$returnValue['hqyp']    = $addList['yp'];

				$getMsgList[] = $addList['yp'] . $tools_lang['model_msg_17'] . $tools_lang['controller_msg_69'];
				//$hqjlArr[] = array($tools_lang['controller_msg_69'], $addList['yp']);
			}else if(isset($addList['yb'])){
				$updateRole['ingot']    = $roleInfo['ingot'] + $addList['yb'];
				$returnValue['yb']      = $updateRole['ingot'];
				$returnValue['hqyb']    = $addList['yb'];

				$getMsgList[] = $addList['yb'] . $tools_lang['model_msg_17'] . $tools_lang['controller_msg_71'];
				//$hqjlArr[] = array($tools_lang['controller_msg_71'], $addList['yb']);
			}else if(isset($addList['sw'])){
				$updateRole['prestige'] = $roleInfo['prestige'] + $addList['sw'];
				$returnValue['sw']      = $updateRole['prestige'];
				$returnValue['hqsw']    = $addList['sw'];
				
				$getMsgList[] = $addList['sw'] . $tools_lang['model_msg_17'] . $tools_lang['controller_msg_72'];
				//$hqjlArr[] = array($tools_lang['controller_msg_72'], $addList['sw']);
			}else if(isset($addList['jl'])){
				$updateRole['food']     = $roleInfo['food'] + $addList['jl'];
				$returnValue['jl']      = $updateRole['food'];
				$returnValue['hqjl']    = $addList['jl'];

				$getMsgList[] = $addList['jl'] . $tools_lang['model_msg_17'] . $tools_lang['controller_msg_70'];
				//$hqjlArr[] = array($tools_lang['controller_msg_70'], $addList['jl']);
			}else if(isset($addList['jf'])){
				$leitaiInfo = daleiModel::getMyLeitai($playersid);
				$leitaiInfo['credits'] += $addList['jf'];
				$leitaiInfo['hqjf'] += $addList['jf'];
				daleiModel::setMyLeitai($leitaiInfo);

				$sql = "update ".$common->tname('dalei_2');
				$sql .= " set credits='{$leitaiInfo['credits']}', last_cdt_time='{$leitaiInfo['last_cdt_time']}'";
				$sql .= " where playersid='{$playersid}'";

				$db->query($sql);
				$returnValue['ltjf'] = $leitaiInfo['credits'];
				$returnValue['hqjf'] = $leitaiInfo['hqjf'];

				$getMsgList[] = $addList['jf'] . $tools_lang['model_msg_17'] . $tools_lang['add_script_jf'];
				$hqjlArr[] = array($tools_lang['add_script_jf'], $addList['jf']);
			}

			// 更新玩家数据
			if(!empty($updateRole)){
				$whereRole['playersid'] = $playersid;
				$common->updatetable('player', $updateRole, $whereRole);
				$common->updateMemCache(MC.$playersid, $updateRole);
			}
		}

		$message = $tools_lang['script_msg_84'] . $itemproto['Name'] . ',';
		$message .= $tools_lang['controller_msg_67'] . implode(',', $getMsgList);
	
		$returnValue['status'] = 0;
		$returnValue['message'] = $message;

		$bagData = $player->GetClientBag();
		$returnValue['list'] = $bagData;

		if(_get('all') == 1)
			$returnValue['hqdj'] = $hqjlArr;

		// 设置使用限制数据
		toolsModel::setUseInfo($mdfUseInfo);

		return $returnValue;
	}

	// 逐鹿道具非正常使用
	public static function itemUse_43($playersid, $djid, $playerBag) {
		global $mc, $db, $common, $G_PlayerMgr, $tools_lang, $sys_lang;
			
		// 无道具ID参数
		if($djid == null) {
			$returnValue = array('status'=>3, 'message'=>$tools_lang['model_msg_1']);
			return $returnValue;
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
		
		// 获取使用道具的ID
		$itemproto = ConfigLoader::GetItemProto($playerBag[$djid]['ItemID']);
		$itemid = $itemproto['ItemID'];	
		$item_name = $itemproto['Name'];
	
		// 如果玩家无道具信息或者道具不足
		if($playerBag == null || !array_key_exists($djid, $playerBag)) {
			$returnValue = array('status'=>998, 'message'=>$tools_lang['script_msg_97']);
			return $returnValue;
		}
		
		$ingot = 0;  // 获取的元宝数量
		$coins = 0;  // 获取的铜钱数量
		$silver = 0; // 获取的银票数量
		$weapon_qh_level = 0;
		$add_item_arr = array(); // 获取的道具
		$message = null;
		$got_msg = null;
		
		// 随机装备数组
		$random_weapon_arr = array(
				6=>array(41202,42202,43202,44202),
				9=>array(41303,42303,43303,44303),
				12=>array(41304,42304,43304,44304),
				15=>array(41405,42405,43405,44405),
				18=>array(41406,42406,43406,44406),
				21=>array(41507,42507,43507,44507)
				);
		// 专属道具数组
		$zswq_arr = array(LGBOW_ITEMID,
						  HALFMOON_ITEMID,
						  RUNWIND_ITEMID,
						  HITTIGER_ITEMID,
						  ARCHLORD_ITEMID);
		// 橙将卡碎片数组
		$cjk_arr = array(20023,20024,20025,20026,20027,20028,20031,20032,20033,20034,20035,20036,20090,20091,20092,20093);
		
		// 逐鹿大奖级别
		$zldj_level = 0;
		$hqjlArr = array();
		
		if($itemid == 18833) { // 小霸王彩礼
			$zldj_level = 20;
			
			// 蓝将卡
			$add_item_arr[BLUECARD_ITEMID] = 2;
				
			// 获取+9随机装备
			$weapon_itemid = $random_weapon_arr[9][rand(0, count($random_weapon_arr[9]) - 1)];
			$add_item_arr[$weapon_itemid] = 1;
			$newItmeInfo = toolsModel::getItemInfo($weapon_itemid);
			$newItemName = $newItmeInfo['Name'];
				
			// 金刚石
			$add_item_arr[JGS_ITEMID] = 20;
				
			// 银票
			$silver = 100;
			
			$got_msg = "{$tools_lang['script_msg_99'][0]}*2、{$newItemName}+9*1、{$tools_lang['script_msg_99'][1]}*20、{$tools_lang['controller_msg_69']}100";
			$message = "{$tools_lang['script_msg_84']}{$item_name}，{$tools_lang['controller_msg_67']}" . $got_msg;
			
			$hqjlArr[] = array($tools_lang['script_msg_99'][0], 2);
			$hqjlArr[] = array($newItemName. '+9', 1);
			$hqjlArr[] = array($tools_lang['script_msg_99'][1], 20);
		}
	
		// 检查玩家背包是否足够		
		$addRet = $player->AddItems($add_item_arr, false, array($itemid=>1));		
		if($addRet === false) {
			$returnValue['status'] = 998;
			$returnValue['message'] = $tools_lang['controller_msg_65'];
			return $returnValue;
		}		
		
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
						
		$currYP = $roleInfo['silver'];
		$currYB = $roleInfo['ingot'];
		$currTQ = $roleInfo['coins'];
		
		// 加入玩家背包
		if($silver > 0) {
			$updateRole['silver'] = $currYP + $silver;
			
			$returnValue['yp'] = $updateRole['silver'];
			$returnValue['hqyp'] = $silver;
		}
		
		if($coins > 0) {
			$updateRole['coins'] = $currTQ + $coins;
			
			$returnValue['tq'] = $updateRole['coins'];
			$returnValue['hqtq'] = $coins;
		}
		
		if($ingot > 0) {
			$updateRole['ingot'] = $currYB + $ingot;
				
			$returnValue['yb'] = $updateRole['ingot'];
			$returnValue['hqyb'] = $ingot;
		}
			
		// 更新玩家当前银票、铜钱、元宝数量
		$whereRole['playersid'] = $playersid;
		$common->updatetable('player', $updateRole, $whereRole);
		$common->updateMemCache(MC.$playersid, $updateRole);
		
		$returnValue['status'] = 0;
		$returnValue['message'] = $message;
		
		$nickName = $roleInfo['nickname'];
		$notice_msg1 = array($nickName, $zldj_level);
		$notice_msg2 = array($zldj_level, $got_msg);
		
		// 获取背包数据
		$bagData = $player->GetClientBag();
		$returnValue['list'] = $bagData;
		
		if(_get('all') == 1)
			$returnValue['hqdj'] = $hqjlArr;
		
		return $returnValue;
	}

	// 发布消息
	public static function setPublicNotice($playersid, $useItemId, $getList){
		global $tools_lang, $G_PlayerMgr;
		$message = '';

		switch($useItemId){
		case 18614:// 树种
			$roleInfo['playersid'] = $playersid;
			roleModel::getRoleInfo($roleInfo);
			$useItemInfo = ConfigLoader::GetItemProto($useItemId);
			$addItemName = $getList[0][0];
			$sl = $getList[0][1];

			$message = "{$tools_lang['script_msg_101'][0]}，<FONT COLOR=\"#ab7afe\">[{$roleInfo['nickname']}]</FONT>";
			$message .= "{$tools_lang['script_msg_101'][1]}\"{$useItemInfo['Name']}\"，{$tools_lang['controller_msg_67']}";
			$message .= "<FONT COLOR=\"#ff65f8\">[{$addItemName}*{$sl}]</FONT>";
			break;
		}

		if(!empty($message)){
			lettersModel::setPublicNotice($message);
		}
	}
}