<?php
class cityModel {
	public static function getAwardInfo(){
		// 初始化奖励道具信息 toolsModel::getItemInfo($itemid);
		$awardItems = array(0=>array(array('id'=>20057, 'num'=>20),
									 array('id'=>20067, 'num'=>3),
									 array('id'=>18582, 'num'=>10),
									 array('id'=>'yp',  'num'=>200),
									 array('id'=>18609, 'num'=>2)),
							7=>array(array('id'=>20057, 'num'=>5),
									 array('id'=>20067, 'num'=>2),
									 array('id'=>18582, 'num'=>2),
									 array('id'=>'yp',  'num'=>40),
									 array('id'=>18609, 'num'=>1)),
							6=>array(array('id'=>20057, 'num'=>3),
									 array('id'=>20067, 'num'=>1),
									 array('id'=>18581, 'num'=>3),
									 array('id'=>'yp',  'num'=>35)),
							5=>array(array('id'=>10040, 'num'=>1),
									 array('id'=>18580, 'num'=>5),
									 array('id'=>'yp',  'num'=>30)),
							4=>array(array('id'=>20057, 'num'=>2),
									 array('id'=>10001, 'num'=>1),
									 array('id'=>'yp',  'num'=>20)),
							3=>array(array('id'=>20067, 'num'=>1),
									 array('id'=>18588, 'num'=>2)),
							2=>array(array('id'=>20057, 'num'=>1),
									 array('id'=>10001, 'num'=>1)),
							1=>array(array('id'=>20057, 'num'=>1)));
		return $awardItems;
	}

	//升级
	public static function upgradeBuilding($getInfo) {
		/*石材   10032
		木材   10033
		陶土   10034
		铁矿   10035
		绢布   10036*/
		global $mc, $common, $db, $_SGLOBAL, $G_PlayerMgr, $sys_lang, $city_lang;
		$playersid = $getInfo ['playersid'];
		$roleInfo ['playersid'] = $playersid;
		//$buildId 1市场2领地3铁匠铺4酒馆5点将台
		$buildId = $getInfo ['buildId'];
		//$userYb = $getInfo ['useYb']; //是否使用元宝
		$buildItem = buildingName ( $buildId );
		$roleRes = roleModel::getRoleInfo ( $roleInfo );
		if (empty ( $roleRes )) {
			$returnValue = array ('status' => 3, 'message' => $sys_lang[1] );
			return $returnValue;
		}
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$sys_lang[7]);
		//cityModel::resourceGrowth ( $roleInfo );
		$level = $roleInfo [$buildItem]; //当前等级
		if ($level >= 12) {
			return array ('status' => 25, 'message' => $city_lang['upgradeBuilding_1'] );
		}
		//$food = $roleInfo ['food']; //当前军粮
		$coins = $roleInfo ['coins']; //当前铜钱量		
		$woodAmoutArray = array ();
		$concreteAmoutArray = array ();
		$stoneAmoutArray = array ();
		$clothAmoutArray = array ();
		$ironAmoutArray = array ();
		$myitem = toolsModel::getMyItemInfo ( $playersid );
		if (! empty ( $myitem )) {
			foreach ( $myitem as $myitemValue ) {
				if ($myitemValue ['ItemID'] == 10032) {
					$stoneAmoutArray [] = $myitemValue ['EquipCount'];
				}
				if ($myitemValue ['ItemID'] == 10033) {
					$woodAmoutArray [] = $myitemValue ['EquipCount'];
				}
				if ($myitemValue ['ItemID'] == 10034) {
					$concreteAmoutArray [] = $myitemValue ['EquipCount'];
				}
				if ($myitemValue ['ItemID'] == 10035) {
					$ironAmoutArray [] = $myitemValue ['EquipCount'];
				}
				if ($myitemValue ['ItemID'] == 10036) {
					$clothAmoutArray [] = $myitemValue ['EquipCount'];
				}
			}
		}
		$wood = array_sum ( $woodAmoutArray ); //木材量
		$concrete = array_sum ( $concreteAmoutArray ); //陶土量
		$stone = array_sum ( $stoneAmoutArray ); //石材量
		$cloth = array_sum ( $clothAmoutArray ); //绢布量
		$iron = array_sum ( $ironAmoutArray ); //铁矿量
		

		$upgradeLevel = $level + 1; //要求达到的级别
		$collectNeedTime = collectNeedTime ();
		$last_update_time = $roleInfo ['last_collect_time'];
		$timeDiff = $_SGLOBAL ['timestamp'] - $last_update_time; //与上次征税时间间隔
		if ($timeDiff >= $collectNeedTime && $buildItem == 'sc_level') {
			$getCoins = round ( getCoinsAmount ( $roleInfo [$buildItem] ), 0 );			
			$coins = $roleInfo ['coins'] + $getCoins;
			if ($coins > COINSUPLIMIT) {
				$coins = COINSUPLIMIT;
				$getCoins = COINSUPLIMIT - $roleInfo ['coins'];
			}
			$returnValue ['hqtq'] = $getCoins;
			$zs = 1;
		} else {
			$zs = 0;
			$coins = $roleInfo ['coins'];
		}
		$needPlayerLevel = requestLevel ( $buildId, $upgradeLevel );
		if ($needPlayerLevel > $roleInfo ['player_level']) {
			$value ['status'] = 25;
			$value ['message'] = $city_lang['upgradeBuilding_2'] . $needPlayerLevel . $city_lang['upgradeBuilding_3'];
			return $value;
		}
		$needMoney = needMoney ( $buildId, $upgradeLevel );
		$needTq = $needMoney [0]; //所需铜钱
		$needCl = $needMoney [1]; //所需材料
		$coins = $coins - $needTq;
		$updateRole ['coins'] = $coins;
		if ($buildId == 1) {
			if ($coins < 0 || $cloth < $needCl) {
				$value ['status'] = 25;
				$value ['tq'] = $roleInfo ['coins'];
				$value ['message'] = $city_lang['upgradeBuilding_4'] . $needTq . $city_lang['upgradeBuilding_5'] . $needCl;
				return $value;
			} else {
				if ($zs == 1) {
					$updateRole ['last_collect_time'] = $_SGLOBAL ['timestamp'];
					$returnValue ['time'] = $collectNeedTime;
				} else {
					$leftTime = $collectNeedTime - (($_SGLOBAL ['timestamp'] - $roleInfo ['last_collect_time']) % $collectNeedTime);
					$returnValue ['time'] = $leftTime;
				}
				$returnValue ['xjzyid'] = 1;
				$returnValue ['djid'] = 10036;
				//toolsModel::deleteBags ( $playersid, array (10036 => $needCl ) );
				$player->DeleteItemByProto(array(10036 => $needCl));
				
				/*获得强征信息*/
				$collectNeedTime = collectNeedTime ();
				$vip = $roleInfo ['vip'];
				switch ($vip) {
					case 1:
						$rate = 1;
						break;						
					case 2 :
						$rate = 1.1;
						break;
					case 3 :
						$rate = 1.15;
						break;
					/*case 4 :
						$rate = 1.2;
						break;*/
					default :
						$rate = 1.2;
						break;
				}				
				$getCoinsTal = round ( getCoinsAmount ( $upgradeLevel ) * $rate, 0 );
				// 当前的银库容量加成
				$ykrljc = intval($roleInfo ['ykzjrl']);
				if($ykrljc > 0) {
					$getCoinsTal += $getCoinsTal * ($ykrljc/100);
				}
				$per_minute_coins = round(round ( $getCoinsTal, 0 ) / ($collectNeedTime/60), 3);
				
				$returnValue['ykjbrl'] = $getCoinsTal;
				$returnValue['dwsszl'] = $per_minute_coins;
			}
		} elseif ($buildId == 2) {
			if ($coins < 0 || $wood < $needCl) {
				$value ['status'] = 25;
				$value ['tq'] = $roleInfo ['coins'];
				$value ['message'] = $city_lang['upgradeBuilding_4'] . $needTq . $city_lang['upgradeBuilding_6'] . $needCl;
				return $value;
			} else {
				$returnValue ['xjzyid'] = 2;
				$returnValue ['djid'] = 10033;
				//toolsModel::deleteBags ( $playersid, array (10033 => $needCl ) );
				$player->DeleteItemByProto(array(10033 => $needCl));
			}
		} elseif ($buildId == 3) {
			if ($coins < 0 || $iron < $needCl) {
				$value ['status'] = 25;
				$value ['tq'] = $roleInfo ['coins'];
				$value ['message'] = $city_lang['upgradeBuilding_4'] . $needTq . $city_lang['upgradeBuilding_7'] . $needCl;
				return $value;
			} else {
				$returnValue ['xjzyid'] = 3;
				$returnValue ['djid'] = 10035;
				//toolsModel::deleteBags ( $playersid, array (10035 => $needCl ) );
				$player->DeleteItemByProto(array(10035 => $needCl));
			}
		} elseif ($buildId == 4) {
			if ($coins < 0 || $stone < $needCl) {
				$value ['status'] = 25;
				$value ['tq'] = floor ( $roleInfo ['coins'] );
				$value ['message'] = $city_lang['upgradeBuilding_4'] . $needTq . $city_lang['upgradeBuilding_8'] . $needCl;
				return $value;
			} else {
				$returnValue ['xjzyid'] = 4;
				$returnValue ['djid'] = 10032;
				//toolsModel::deleteBags ( $playersid, array (10032 => $needCl ) );
				$player->DeleteItemByProto(array(10032 => $needCl));
			}
		} elseif ($buildId == 5) {
			if ($coins < 0 || $concrete < $needCl) {
				$value ['status'] = 25;
				$value ['tq'] = floor ( $roleInfo ['coins'] );
				$value ['message'] = $city_lang['upgradeBuilding_4'] . $needTq . $city_lang['upgradeBuilding_9'] . $needCl;
				return $value;
			} else {
				$returnValue ['xjzyid'] = 5;
				$returnValue ['djid'] = 10034;
				//toolsModel::deleteBags ( $playersid, array (10034 => $needCl ) );
				$player->DeleteItemByProto(array(10034 => $needCl));
			}
		} else {
			$value ['status'] = 3;
			$value ['message'] = $sys_lang[7];
			return $value;
		}
		$returnValue ['status'] = 0;
		$returnValue ['xhtq'] = $needTq;
		$returnValue ['jzid'] = $buildId; //建筑ID
		$returnValue ['jzdj'] = $upgradeLevel; //建筑级别
		if ($upgradeLevel == 12) {
			$returnValue ['sfmj'] = 1;
		} else {
			$returnValue ['sfmj'] = 0;
			$needPlayerLevelNext = requestLevel ( $buildId, $upgradeLevel + 1 );
			$returnValue ['xjwjjb'] = $needPlayerLevelNext; //下级升级要求级别
			$needMoneyNext = needMoney ( $buildId, $upgradeLevel + 1 );
			$returnValue ['xjsjtq'] = $needMoneyNext [0]; //下级升级所需铜钱
			$returnValue ['xjzysl'] = $needMoneyNext [1]; //下级升级所需资源数量
		}
		//升级酒馆增加繁荣度
		if ($buildId == 4) {
			$xfrd = $roleInfo['frd'] + 80;
			if ( $xfrd < 10000) {
				$updateRole ['frd'] = $xfrd;
			} else {
				$updateRole ['frd'] = 10000; 
			}								
		}
		$updateRole [$buildItem] = $upgradeLevel;
		$roleInfo[$buildItem] = $upgradeLevel;		
		$updateRoleWhere ['playersid'] = $playersid;
		$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
		$common->updateMemCache ( MC . $playersid, $updateRole );
		$returnValue ['tq'] = floor ( $coins );
		//$allItems = toolsModel::getAllItems ( $playersid );
		//$returnValue ['list'] = $allItems ['list'];
		//$simBg = $playerBag = null;
		//$bagData = toolsModel::getBglist($simBg, $playersid, $playerBag);
		$bagData = $player->GetClientBag();
		$returnValue ['list'] = $bagData; 	// 修改背包列表返回
		$returnValue ['jzzt'] = cityModel::jzzt ( $playersid );
		//完成任务	
		$roleInfo['rwsl'] = $upgradeLevel;	 //建筑当前级别
		if ($buildId == 4) {
			$rwItme = ",'sjdjt'";
		} else {
			$rwItme = '';
		}
	
		if ($buildId == 4) {
			$returnValue['frd'] = $cjInfo['frd'] = $roleInfo['frd'] = $updateRole ['frd'];
			$roleInfo['hdyp'] = 80;		
			achievementsModel::check_achieve($playersid,$cjInfo,array('frd'));
			$rwid = questsController::OnFinish ( $roleInfo, "'jzdj','sjsy','frd','" . $buildItem . "'".$rwItme );
		} else {
			$rwid = questsController::OnFinish ( $roleInfo, "'jzdj','sjsy','" . $buildItem . "'".$rwItme );
		}
		if (! empty ( $rwid )) {
			$returnValue ['rwid'] = $rwid;
		}		
		/*if ($buildItem == 'djt_level') {
			//完成引导脚本
			$xyd = guideScript::wcjb ( $roleInfo, 'sjdjt', $roleInfo ['player_level'], $roleInfo['player_level'], 'djt_level' );
			//接收新的引导
			if (empty ( $rwid )) {
				$xyd = guideScript::xsjb ( $roleInfo, 'zmwj', 8 );
			}
			if ($xyd !== false) {
				$returnValue ['ydts'] = $xyd;
			}
		}*/
		return $returnValue;
	}
	
	//采矿$playersid玩家ID
	public static function ksck($playersid) {
		global $common, $mc, $_SGLOBAL, $G_PlayerMgr, $city_lang, $sys_lang, $zyd_lang;
		$roleInfo ['playersid'] = $playersid;
		$roleRes = roleModel::getRoleInfo ( $roleInfo );
		if (empty ( $roleRes )) {
			$value = array ('status' => 3, 'message' => $sys_lang[1] );
			return $value;
		}
		
		if(toolsModel::getBgSyGs($playersid) == 0)
			return array ('status' => 998, 'message' => $city_lang['ksck_2']);
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$sys_lang[7]);
		
		$nowTime = $_SGLOBAL ['timestamp'];
		$last_wk_time = $roleInfo ['last_wk_time']; //上次挖矿时间
		$wk_count = cityModel::checkMineTimes ( $roleInfo ); //挖矿次数
		$timeInfo = wksjxz ();
		$diffTime = $timeInfo [0]; //要求的时间间隔
		if ($nowTime - $last_wk_time < $diffTime) {
			$value ['status'] = 1021;
			$value ['lt'] = $diffTime - ($nowTime - $last_wk_time);
		} else {
			if ($wk_count >= $timeInfo [1]) {
				$value ['status'] = 1003;
				$value ['jzzt'] = cityModel::jzzt ( $playersid );
				$value ['message'] = $city_lang['ksck_1'];
				return $value;
			}
			
			$rand_tq = 90;
			$rand_jgs = 45; //
			//$rand_jl = 20;
			//$rand_qh = 60;  //获取金刚石的几率
			$rand = rand ( 0, 99 );
			$updateRole ['playersid'] = $playersid;
			//$playerBag = toolsModel::getMyItemInfo($playersid); // 背包列表返回协议优化
			if ($rand <= $rand_jgs) {
				//$addRes = toolsModel::addPlayersItem ( $roleInfo, 20009 ); //放入背包
				$addRes = $player->AddItems(array(20009=>1));
				if ($addRes !== false) {
					$value ['status'] = 0;
					$itemInfo = toolsModel::getItemInfo ( 20009 );
					$value ['mc'] = $itemInfo ['Name'];
					$value ['sl'] = 1;
					$value ['iid'] = $itemInfo ['IconID'];
					$value['jllx'] = 3;
					//$allItem = toolsModel::getAllItems ( $playersid );
					//$value ['list'] = $allItem ['list'];
					//$simBg[] = array('insert'=>1, 'ItemID'=>$itemInfo ['ItemID']); // 背包列表返回协议优化
					//$bagData = toolsModel::getBglist($simBg, $playersid, $playerBag);// 背包列表返回协议优化
					$bagData = $player->GetClientBag();
					$value['list'] = $bagData;
				} elseif ($addRes === false) { //背包已满
					$value ['status'] = 1001;
					$value ['message'] = $city_lang['ksck_2'];
				} else {
					$value ['status'] = 1001;
					$value ['message'] = $city_lang['ksck_2'];
				}
			} elseif ($rand <= $rand_tq) {
				$value ['status'] = 0;				
				$updateRole ['coins'] = $roleInfo ['coins'] + 500;
				if ($updateRole ['coins'] > COINSUPLIMIT) {
					$updateRole ['coins'] = COINSUPLIMIT;
					$hqtq = COINSUPLIMIT - $roleInfo ['coins'];
				} else {
					$hqtq = 500;
				}
				$value['jllx'] = 2;
				$value ['hqtq'] = $hqtq;
				$value ['tq'] = $updateRole ['coins'];
				$value ['mc'] = $zyd_lang['scyd_1'];
			} else {
				//10001金刚石
				//$djInfo = array(10002,10003,10004,10005);
				//$amount = rand(2,3);
				//$key = array_rand($djInfo,1);
				$itemid = 10001;
				//$addRes = toolsModel::addPlayersItem ( $roleInfo, $itemid, 1 ); //放入背包
				$addRes = $player->AddItems(array($itemid=>1));
				if ($addRes !== false) {
					$value ['status'] = 0;
					$itemInfo = toolsModel::getItemInfo ( $itemid );
					$value ['mc'] = $itemInfo ['Name'];
					$value ['sl'] = 1;
					$value ['iid'] = $itemInfo ['IconID'];
					$value['jllx'] = 1;
					//$allItem = toolsModel::getAllItems ( $playersid );
					//$value ['list'] = $allItem ['list'];
					//$simBg[] = array('insert'=>1, 'ItemID'=>$itemid); // 背包列表返回协议优化
					//$bagData = toolsModel::getBglist($simBg, $playersid, $playerBag);// 背包列表返回协议优化
					$bagData = $player->GetClientBag();
					$value['list'] = $bagData;
				} elseif ($addRes === false) { //背包已满
					$value ['status'] = 1001;
					$value ['message'] = '';
				} else {
					$value ['status'] = 1001;
					$value ['message'] = $city_lang['ksck_2'];
				}
			}
			$rwid = questsController::OnFinish ( $roleInfo, "'ck'" );
			if (! empty ( $rwid )) {
				if (! empty ( $rwid )) {
					$value ['rwid'] = $rwid;
				}
			}
			if ($value ['status'] == 0) {
				/*$xwzt_26 = substr($roleInfo['xwzt'],25,1);
				if ($xwzt_26 == 0) {
					$updateRole['xwzt'] = $xwzt = substr_replace($roleInfo['xwzt'],'1',25,1);
				}*/				
				$updateRole ['last_wk_time'] = $nowTime;
				$updateRole ['wk_count'] = $wk_count + 1;
				$updateRoleWhere ['playersid'] = $playersid;
				$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
				$common->updateMemCache ( MC . $playersid, $updateRole );
				$wk_count = $updateRole ['wk_count'];
				//完成引导脚本
				/*guideScript::wcjb ( $roleInfo, 'ydwk', 5, 5 );
				//接收新的引导
				$xyd = guideScript::xsjb ( $roleInfo, 'ydtb', 5 );
				if ($xyd !== false) {
					$value ['ydts'] = $xyd;
				}*/
			}
			if ($wk_count < 10) {
				if ($value ['status'] == 0) {
					$value ['kscd'] = $diffTime;
				} else {
					$value ['kscd'] = 0;
				}
			}
			$value ['jzzt'] = cityModel::jzzt ( $playersid );
		}
		return $value;
	}
	
	/**
	 * 检查当前采矿次数
	 *
	 * @param array $roleInfo		要检查的用户信息
	 * @return int					采矿次数
	 */
	
	public static function checkMineTimes($roleInfo) {
		$mx_wk_time = $roleInfo ['last_twk_time'] > $roleInfo ['last_wk_time'] ? $roleInfo ['last_twk_time'] : $roleInfo ['last_wk_time'];
		if (date ( 'Y-m-d' ) == date ( 'Y-m-d', $mx_wk_time )) {
			return $roleInfo ['wk_count'];
		} else {
			return 0;
		}
	}
	
	//偷占领者矿
	public static function zlck($playersid, $tuid) {
		global $common, $mc, $_SGLOBAL, $G_PlayerMgr, $city_lang, $sys_lang, $zyd_lang; //last_twk_time
		$roleInfo ['playersid'] = $playersid;
		$roleRes = roleModel::getRoleInfo ( $roleInfo );
		if (empty ( $roleRes )) {
			$value = array ('status' => 3, 'message' => $sys_lang[1] );
			return $value;
		}
		
		if(toolsModel::getBgSyGs($playersid) == 0)
			return array ('status' => 998, 'message' => $city_lang['tqtrks_full']);
		
		$occupyRoleInfo ['playersid'] = $tuid;
		$roleRes2 = roleModel::getRoleInfo ( $occupyRoleInfo );
		if (empty ( $roleRes2 )) {
			$value = array ('status' => 3, 'message' => $sys_lang[1] );
			return $value;
		}
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$sys_lang[7]);
		
		if ($occupyRoleInfo ['is_reason'] == 1) {
			$value = array ('status' => 21, 'message' => $city_lang['zlck_1'] );
			return $value;
		}
		$value = array ();
		//print_r('playersid:'.$occupyRoleInfo['aggressor_playersid']);
		// 检查是否能够偷矿
		if ($roleInfo ['aggressor_playersid'] != $tuid || $roleInfo ['end_defend_time'] <= $_SGLOBAL ['timestamp']) {
			$value ['status'] = 2;
		} else {
			$nowTime = $_SGLOBAL ['timestamp'];
			$last_twk_time = $occupyRoleInfo ['last_twk_time']; //上次偷挖时间
			$wk_count = cityModel::checkMineTimes ( $occupyRoleInfo ); //挖矿次数
			$timeInfo = wksjxz ();
			
			// 检查偷矿时间是否过期
			$diffTime = $timeInfo [2]; //要求的时间间隔
			if ($nowTime - $last_twk_time < $diffTime) {
				$value ['status'] = 1021;
				$value ['lt'] = $diffTime - ($nowTime - $last_twk_time);
			} else {
				//并发控制
				$bfkz = heroCommon::bfkz ( MC . $tuid . '_zltk', 3, $city_lang['zlck_2'] );
				if ($bfkz != 'go') {
					$value ['status'] = 1003;
					$value ['message'] = $bfkz;
					return $value;
				}
				// 如果是当天偷过矿或者从来没有偷过的话就检查偷矿次数
				if ($wk_count >= $timeInfo [1]) {
					$value ['status'] = 1003;
					$value ['message'] = $city_lang['zlck_3'];
					return $value;
				}
				
				$rand_tq = 90;
				$rand_jgs = 45;
				//$rand_jl = 30;
				//$rand_qh = 40;
				$rand = rand ( 0, 99 );
				//$updateRole['playersid'] = $playersid;
				$tqsl = 0; //偷到资源的数值
				//echo('rand:'.$rand.'----rand_tq:'.$rand_tq);
				//$playerBag = toolsModel::getMyItemInfo($playersid); // 背包列表返回协议优化
				if ($rand <= $rand_jgs) {
					//$addRes = toolsModel::addPlayersItem ( $roleInfo, 20009 ); //放入背包
					$addRes = $player->AddItems(array(20009=>1));
					if ($addRes !== false) {
						$value ['status'] = 0;
						$itemInfo = toolsModel::getItemInfo ( 20009 );
						$value ['mc'] = $itemInfo ['Name'];
						$value ['sl'] = 1;
						$value ['iid'] = $itemInfo ['IconID'];
						$value ['jllx'] = 3;
						//$allItem = toolsModel::getAllItems ( $playersid );
						//$value ['list'] = $allItem ['list'];
						//$simBg[] = array('insert'=>1, 'ItemID'=>$itemInfo ['ItemID']); // 背包列表返回协议优化
						//$bagData = toolsModel::getBglist($simBg, $playersid, $playerBag);// 背包列表返回协议优化
						$bagData = $player->GetClientBag();
						$value['list'] = $bagData;
						$tqsl = 1;
					} elseif ($addRes === false) { //背包已满
						$value ['status'] = 1001;
						$value ['message'] = $city_lang['ksck_2'];
						return $value;
					} else {
						$value ['status'] = 1001;
						$value ['message'] = $city_lang['ksck_2'];
						return $value;
					}
				} elseif ($rand <= $rand_tq) {
					$value ['status'] = 0;
					$updateRoleSelf ['coins'] = $roleInfo ['coins'] + 500;
					if ($updateRoleSelf ['coins'] > COINSUPLIMIT) {
						$updateRoleSelf ['coins'] = COINSUPLIMIT;
						$hqtq = COINSUPLIMIT - $roleInfo ['coins'];
					} else {
						$hqtq = 500;
					}
					$value ['jllx'] = 2;
					$value ['hqtq'] = $hqtq;										
					$value ['tq'] = $updateRoleSelf ['coins'];
					$value ['mc'] = $zyd_lang['scyd_1'];
					$tqsl = $hqtq;
					$updateRoleWhereSelf ['playersid'] = $playersid;
					$common->updatetable ( 'player', $updateRoleSelf, $updateRoleWhereSelf );
					$common->updateMemCache ( MC . $playersid, $updateRoleSelf );
				} else {
					//10001金刚石
					//$djInfo = array(10002,10003,10004,10005);
					//$amount = rand(2,3);
					//$key = array_rand($djInfo,1);
					$itemid = 10001;
					//$addRes = toolsModel::addPlayersItem ( $roleInfo, $itemid, 1 ); //放入背包
					$addRes = $player->AddItems(array($itemid=>1));
					if ($addRes !== false) {
						$value ['status'] = 0;
						$itemInfo = toolsModel::getItemInfo ( $itemid );
						$value ['mc'] = $itemInfo ['Name'];
						$value ['sl'] = 1;
						$value ['iid'] = $itemInfo ['IconID'];
						$value ['jllx'] = 1;
						//$allItem = toolsModel::getAllItems ( $playersid );
						//$value ['list'] = $allItem ['list'];
						//$simBg[] = array('insert'=>1, 'ItemID'=>$itemid); // 背包列表返回协议优化
						//$bagData = toolsModel::getBglist($simBg, $playersid, $playerBag);// 背包列表返回协议优化
						$bagData = $player->GetClientBag();
						$value['list'] = $bagData;
						$tqsl = 1;
					} elseif ($addRes ['status'] == 1021) { //背包已满
						$value ['status'] = 1001;
						$value ['message'] = $city_lang['ksck_2'];
						return $value;
					} else {
						$value ['status'] = 1001;
						$value ['message'] = $city_lang['ksck_2'];
					}
				}
				if ($value ['status'] == 0) {
					//写消息 偷矿
					$json ['playersid'] = $playersid;
					$json ['toplayersid'] = $tuid;
					$json ['message'] = array ('wjmc1' => $roleInfo ['nickname'], 'wjid1' => $roleInfo ['playersid'], 'tqmc' => $value ['mc'], 'tqsl' => $tqsl );
					$json ['type'] = 1;
					if ($value ['mc'] == $zyd_lang['scyd_1']) {
						$json ['genre'] = 22;
					} elseif ($value ['mc'] == $zyd_lang['scyd_2']) {
						$json ['genre'] = 21;
					} else {
						$json ['genre'] = 23;
					}
					$json ['interaction'] = 1;
					$json ['is_passive'] = '';
					$json ['tradeid'] = 0;
					//$json = json_encode ( $json );
					$result = lettersModel::addMessage ( $json );
					$rwid = questsController::OnFinish ( $roleInfo, "'tk'" );
					if (! empty ( $rwid )) {
						if (! empty ( $rwid )) {
							$value ['rwid'] = $rwid;
						}
					}
					
					// 更新被挖矿玩家数据
					$updateRole ['wk_count'] = $wk_count + 1;
					$updateRole ['last_twk_time'] = $nowTime;
					$updateRoleWhere ['playersid'] = $tuid;
					$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
					//$common->updateMemCache(MC.$tuid,$updateRole);
					$mc->delete ( MC . $tuid );
					$wk_count = $updateRole ['wk_count'];
				}
				
				if ($wk_count < 10) {
					if ($value ['status'] == 0) {
						$value ['kscd'] = $diffTime;
					} else {
						$value ['kscd'] = 0;
					}
				}
			}
		}
		return $value;
	}
	
	// 提升银库容量
	public static function tsykrl($getInfo) {
		global $common, $_SGLOBAL, $sys_lang, $city_lang;
		
		$nowTime = $_SGLOBAL ['timestamp'];
		$playersid = $getInfo['playersid'];
		$roleInfo ['playersid'] = $playersid;
		$roleRes = roleModel::getRoleInfo ( $roleInfo );
		if (empty ( $roleRes )) {
			$value = array ('status' => 3, 'message' => $sys_lang[1] );
			return $value;
		}

		$ykrljc = intval($roleInfo ['ykzjrl']);//echo $ykrljc;
		$ts_need_arr = array(0=>array(0, 10), 10=>array(1, 25), 25=>array(2, 50), 50=>array(3, 75), 75=>array(4, 100));
		if(isset($ts_need_arr[$ykrljc])) {
			$need_vip = $ts_need_arr[$ykrljc][0];
			$need_yp = $ts_need_arr[$ykrljc][1];
		} else {
			return array('status'=>998, 'message'=>$city_lang['tssx']);
		}
		
		$silver = $roleInfo['silver'];	
		if ($silver < $need_yp) {
			$value ['status'] = 68;
			$value ['yp'] = $silver;
			$value ['xyxhyp'] = $need_yp;
			$rep_arr1 = array('{xhyp}', '{yp}');
			$rep_arr2 = array($need_yp, $silver);
			$msg = str_replace($rep_arr1, $rep_arr2, $sys_lang[6]);			
			$value ['message'] = $msg;
			$value ['xyxhyp'] = $need_yp;
			return $value;
		} else if($roleInfo['vip'] < $need_vip) {			
			return array('status'=>998, 'message'=>str_replace('{vip}', $need_vip, $city_lang['tsvipbz']));
		} else {			
			$returnValue['status'] = 0;
			$updateRole['silver'] = $silver - $need_yp;
			$returnValue ['yp'] = intval($updateRole['silver']);
			$returnValue ['xhyp'] = $need_yp;
			$updateRole['ykzjrl'] = $need_yp; // 所需银票与加成数值一致
			$updateRoleWhere['playersid'] = $playersid;
			$common->updatetable('player',$updateRole,$updateRoleWhere);
			$common->updateMemCache(MC.$playersid,$updateRole);		
			
			roleModel::getRoleInfo ( $roleInfo );
			$collectNeedTime = collectNeedTime ();
			$vip = $roleInfo ['vip'];
			switch ($vip) {
				case 1:
					$rate = 1;
					break;					
				case 2 :
					$rate = 1.1;
					break;
				case 3 :
					$rate = 1.15;
					break;
				/*case 4 :
					$rate = 1.2;
					break;*/
				default :
					$rate = 1.2;
					break;
			}
			// 当前的银库容量加成
			$ykrljc = intval($roleInfo ['ykzjrl']); //echo $ykrljc . '   ' . $collectNeedTime;
			$jc_arr = array(0=>10, 10=>25, 25=>50, 50=>75, 75=>100);
			
			$getCoinsTal = round ( getCoinsAmount ( $roleInfo ['sc_level'] ) * $rate, 0 );
			//echo $ykrljc/100 . '    ' . $getCoinsTal;
			if($ykrljc > 0) {
				$getCoinsTal += $getCoinsTal * ($ykrljc/100);
			}//echo $getCoinsTal . '    ' . $collectNeedTime/60;
			$per_minute_coins = round(round ( $getCoinsTal, 0 ) / ($collectNeedTime/60), 3);
						
			/*if(!isset($jc_arr[$ykrljc])) {
				return array('status'=>998, 'message'=>$city_lang['tssx']);
			}*/
									
			if($ykrljc == 0) {
				$returnValue['ykjc'] = $ykrljc;
				$returnValue['xjykjc'] = 10;
				$returnValue['tstj'] = array(0,10);
				//$returnValue['dwsszl'] = $per_minute_coins;
			} else if($ykrljc == 10) {
				$returnValue['ykjc'] = $ykrljc;
				$returnValue['xjykjc'] = 25;
				$returnValue['tstj'] = array(1,25);
				//$returnValue['dwsszl'] = $per_minute_coins;
			} else if($ykrljc == 25) {
				$returnValue['ykjc'] = $ykrljc;
				$returnValue['xjykjc'] = 50;
				$returnValue['tstj'] = array(2,50);
				//$returnValue['dwsszl'] = $per_minute_coins;
			} else if($ykrljc == 50) {
				$returnValue['ykjc'] = $ykrljc;
				$returnValue['xjykjc'] = 75;
				$returnValue['tstj'] = array(3,75);
				//$returnValue['dwsszl'] = $per_minute_coins;
			} else if($ykrljc == 75) {
				$returnValue['ykjc'] = $ykrljc;
				$returnValue['xjykjc'] = 100;
				$returnValue['tstj'] = array(4,100);
				//$returnValue['dwsszl'] = $per_minute_coins;
			} else if($ykrljc == 100) {
				$returnValue['ykjc'] = $ykrljc;		
				//$returnValue['dwsszl'] = $per_minute_coins;
			}
		}

		return $returnValue;
	}
	
	//秒征税ID
	public static function mzscd($playersid) {
		global $common, $_SGLOBAL, $sys_lang, $city_lang;
		$nowTime = $_SGLOBAL ['timestamp'];
		$roleInfo ['playersid'] = $playersid;
		$roleRes = roleModel::getRoleInfo ( $roleInfo );
		if (empty ( $roleRes )) {
			$value = array ('status' => 3, 'message' => $sys_lang[1] );
			return $value;
		}
		$vip = $roleInfo ['vip'];
		if ($vip >= 4) {
			$qzcs = 4;
		} elseif ($vip == 3) {
			$qzcs = 3;
		} elseif ($vip == 2) {
			$qzcs = 2;
		} elseif ($vip == 1) {
			$qzcs = 1;
		} else {
			$qzcs = 0;
		}
		$jscs = $roleInfo ['jscs'];
		if ($qzcs != 0) {
			if ($jscs >= $qzcs) {
				$value ['status'] = 1025;
				$value ['message'] = $city_lang['mzscd_1'];
				$vipNext = $vip + 1;
				$vipInfo = cityModel::vipxx ( $playersid, $roleInfo ['userid'] );
				if ($vipNext >= 4) {
					$xjqzcs = 4;
				} elseif ($vipNext == 3) {
					$xjqzcs = 3;
				} elseif ($vipNext == 2) {
					$xjqzcs = 2;
				} else {
					$xjqzcs = 1;
				}
				if ($vipNext < 13) {
					$value ['zjcs'] = $xjqzcs - $jscs;
					if ($value ['zjcs'] <= 0) {
						$value ['zjcs'] = 1;
						$updateRole['jscs'] = $xjqzcs - 1;
						$updateRoleWhere ['playersid'] = $playersid;
						$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
						$common->updateMemCache ( MC . $playersid, $updateRole );
					}
					$value ['xycz'] = intval ( $vipInfo ['xjcz'] );
				}
				return $value;
			} else {
				$value ['status'] = 0;
				$updateRole ['last_collect_time'] = $roleInfo ['last_collect_time'] - 300;
				$updateRole ['jscs'] = $jscs + 1;
				//$updateRole['jssjrq'] = $nowTime;
				$updateRoleWhere ['playersid'] = $playersid;
				$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
				$common->updateMemCache ( MC . $playersid, $updateRole );
				$value ['jzzt'] = cityModel::jzzt ( $playersid );
				$value ['jssycs'] = $qzcs - $updateRole ['jscs'];
				$value ['lt'] = 7200 - ($nowTime - $updateRole ['last_collect_time']);
				if ($value ['lt'] < 0) {
					$value ['lt'] = 0;
				}
				$rwid = questsController::OnFinish($roleInfo,"'ssjs'");  //征税加速
			    if (!empty($rwid)) {
			         $value['rwid'] = $rwid;				             
			    }				
			}
		} else {
			$value ['status'] = 1025;
			$value ['message'] = $city_lang['mzscd_2'];
			$vipInfo = cityModel::vipxx ( $playersid, $roleInfo ['userid'] );
			$value ['zjcs'] = 1;
			$value ['xycz'] = intval ( $vipInfo ['xjcz'] );
		}
		/*$silver = $roleInfo['silver'];
		$cdTime = $_SGLOBAL['timestamp'] - $roleInfo['last_collect_time'];
		if ($cdTime > 0) {
			$leftTime = 7200 - $cdTime;
			$needYp = ceil($leftTime / 3600) * 5;
		} else {
			$needYp = 0;
		}		
		if ($silver < $needYp) {
			$value ['status'] = 68;
			$value ['yp'] = $silver;
			$value ['xyxhyp'] = $needYp;
			$value ['message'] = '您需要消耗'.$needYp.'银票，目前您有'.$value ['yp'].'银票，市场里可兑换银票';
			$value ['xyxhyp'] = $needYp;
		} else {
			$value['status'] = 0;
			$updateRole['silver'] = $silver - $needYp;
			$value ['yp'] = intval($updateRole['silver']);
			$value ['xhyp'] = $needYp;
			$updateRole['last_collect_time'] = $_SGLOBAL['timestamp'] - 7200;
			$updateRoleWhere['playersid'] = $playersid;
			$common->updatetable('player',$updateRole,$updateRoleWhere);
			$common->updateMemCache(MC.$playersid,$updateRole);
			$value['jzzt'] =  cityModel::jzzt($playersid);
		}*/
		return $value;
	}
	
	//征税
	public static function resourceCollection($getInfo) {
		global $common, $_SGLOBAL, $mc, $sys_lang, $city_lang;
		$nowTime = $_SGLOBAL ['timestamp'];
		$playersid = $getInfo ['playersid'];
		$hyb = $getInfo ['hyb'];
		$roleInfo ['playersid'] = $playersid;
		$roleRes = roleModel::getRoleInfo ( $roleInfo );
		if (empty ( $roleRes )) {
			$value = array ('status' => 3, 'message' => $sys_lang[1] );
			return $value;
		}
		$qzcs = intval($roleInfo['qzcs']);
		$qzsjrq = $roleInfo['qzsjrq'];
		if ($hyb == 1) {
			if (date('Ymd',$nowTime) == date('Ymd',$qzsjrq)) {
				if ($qzcs == 1) {
					$needyb = 20;
					$tqcl = 20000;
					$qzsycs = 3;
					$qzyb = 40;
					$qztqsl = 30000;
				} elseif ($qzcs == 2) {
					$needyb = 40;
					$tqcl = 30000;
					$qzsycs = 2;
					$qzyb = 80;
					$qztqsl = 40000;
				} elseif ($qzcs == 3) {
					$needyb = 80;
					$qzsycs = 1;
					$tqcl = 40000;
					$qzyb = 160;
					$qztqsl = 50000;
				} elseif ($qzcs == 4) {
					$needyb = 160;
					$qzsycs = 0;
					$tqcl = 50000;
					$qztqsl = 0;
					$qzyb = 0;
				} else {
					return array('status'=>21,'message'=>$city_lang['resourceCollection_3']);
				}
			} else {
				$needyb = 10;
				$tqcl = 10000;
				$qzsycs = 4;
				$qzyb = 20;	
				$qztqsl = 20000;		
			}
		}
		//cityModel::resourceGrowth($roleInfo);
		$buildItem = 'sc_level';
		$collectNeedTime = collectNeedTime ();
		switch ($roleInfo ['vip']) {
			case 1:
				$rate = 1;
				break;				
			case 2 :
				$rate = 1.1;
				break;
			case 3 :
				$rate = 1.15;
				break;
			/*case 4 :
				$rate = 1.2;
				break;*/
			default :
				$rate = 1.2;
				break;
		}
		$value ['status'] = 0;
		$last_update_time = $roleInfo ['last_collect_time'];
		$timeDiff = $nowTime - $last_update_time; //与上次征税时间间隔
		$vip = $roleInfo ['vip'];
		if ($vip >= 4) {
			$cdcs = 4;
		} elseif ($vip == 3) {
			$cdcs = 3;
		} elseif ($vip == 2) {
			$cdcs = 2;
		} elseif ($vip == 1) {
			$cdcs = 1;
		} else {
			$vip = 0;
			$cdcs = 0;
		}
		if (isset($needyb)) {
			$getCoinsTal = $tqcl;
		} else {
			$getCoinsTal = round ( getCoinsAmount ( $roleInfo [$buildItem] ) * $rate, 0 );
			//$common->insertLog("$getCoinsTal");
			// 当前的银库容量加成
			$ykrljc = $roleInfo ['ykzjrl'] / 100;
			if($ykrljc > 0) {
				$getCoinsTal += $getCoinsTal * $ykrljc;
			}
		}		
		if (isset($needyb)) {
			$ingot = $roleInfo ['ingot'];
			if ($ingot < $needyb) {
				$valueYp ['status'] = 88;
				$valueYp ['yb'] = floor($ingot);
				$valueYp ['xyxhyb'] = $needyb;
				$arr1 = array('{xhyb}','{yb}');
				$arr2 = array($needyb,$ingot);
				$valueYp ['message'] = str_replace($arr1,$arr2,$sys_lang[2]);
				return $valueYp;
			} else {
				//$value['status'] = 0;
				$updateRole ['ingot'] = $ingot - $needyb;
				$value ['yb'] = intval ( $updateRole ['ingot'] );
				//$updateRole ['last_collect_time'] = $_SGLOBAL ['timestamp'] - 7200;
				//$updateRoleWhere ['playersid'] = $playersid;
				$updateRole ['qzsjrq'] = $nowTime;
				$updateRole ['qzcs'] = 5 - $qzsycs;
				$timeDiff = $collectNeedTime;
				$value ['qzcs'] = $qzsycs;	
				$value['xhyb'] = $needyb;
				$value['qzyb'] = $qzyb;		
				$value['qztq'] = $qztqsl;
				$rwid = questsController::OnFinish ( $roleInfo, "'qzzs','zs'" );
				if (! empty ( $rwid )) {
					$value ['rwid'] =  $rwid;
				}				
			}
		}
		//if ($timeDiff < $collectNeedTime) {
		if ($timeDiff < 7200) {
			$valueLt ['status'] = 1021;
			$valueLt ['message'] = $city_lang['resourceCollection_1'];
			$valueLt ['lt'] = $collectNeedTime - $timeDiff; //还剩时间
			return $valueLt;
		} else {
			if ($hyb != 1) {
				if($ykrljc > 0) {
					// 计算当前可征收铜钱数量
					if(($collectNeedTime - $timeDiff) <= 0) $timeDiff = $collectNeedTime;
					$per_minute_coins = round(round ( $getCoinsTal, 0 ) / ($collectNeedTime/60), 3);
					//$common->insertLog("$getCoinsTal");
					$getCoinsTal = round($timeDiff / 60, 0) * $per_minute_coins;
					//$common->insertLog("$getCoinsTal, $collectNeedTime, $per_minute_coins, $getCoinsTal");
				}
			}
			
			$zfInfo = cityModel::getZfInfo ( '', $roleInfo ); //驻防信息
			if ($zfInfo ['status'] == 0 || $hyb == 1) {
				$zf = 1;
				$getCoins = round ( $getCoinsTal, 0 );
			} elseif ($zfInfo ['status'] == 1001) {
				$zlid = $zfInfo ['uid'];
				$getCoins = round ( $getCoinsTal * 0.9, 0 );
				$tozlCoins = round ( $getCoinsTal * 0.1, 0 );
				$value ['sj_tuid'] = $zlid;
				$value ['sj_tq'] = $tozlCoins;
				$zlInfo['playersid'] = $zlid;
				$zlRes = roleModel::getRoleInfo($zlInfo);
				$updatezl ['coins'] = $zlInfo ['coins'] + $tozlCoins;
				if ($updatezl ['coins'] > COINSUPLIMIT) {
					$updatezl ['coins'] = COINSUPLIMIT;
					$tozlCoins = COINSUPLIMIT - $zlInfo ['coins'];
				}
				$common->updatetable ( 'player', $updatezl, "playersid = '$zlid'" );			
				$common->updateMemCache ( MC . $zlid, $updatezl );
				zydModel::zydlog('app.php?task=zlss&option=city',array('hqtq'=>$tozlCoins),$zlInfo['player_level'],$zlInfo['userid']);				
				//发消息
				$mesArra = array ('xllx' => 31, 'wjmc1' => $roleInfo ['nickname'], 'wjid1' => intval ( $playersid ), 'ss' => intval ( $tozlCoins ), 'interaction' => 1 );
				//$mesageDen = addcslashes ( json_encode ( $mesArra ), '\\' );
				$mesageDen = $mesArra;
				$messageInfo = array ('playersid' => 0, 'jsid' => $roleInfo ['playersid'], 'toplayersid' => $zlid, 'subject' => $city_lang['resourceCollection_2'], 'message' => $mesageDen, 'type' => 1, 'genre' => 31, 'tradeid' => 0, 'is_passive' => 0, 'interaction' => 1, 'request' => '' );
				lettersModel::addMessage ( $messageInfo );
			} else {
				$getCoins = round ( $getCoinsTal, 0 );
			}
			$updateRole ['jscs'] = 0;
			$updateRole ['coins'] = $roleInfo ['coins'] + $getCoins;
			if ($updateRole ['coins'] > COINSUPLIMIT) {
				$updateRole ['coins'] = COINSUPLIMIT;
				$getCoins = COINSUPLIMIT - $roleInfo ['coins'];
			}
			$updateRole ['last_collect_time'] = $_SGLOBAL ['timestamp'];
			$updateRoleWhere ['playersid'] = $playersid;
			$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
			$common->updateMemCache ( MC . $playersid, $updateRole );
			if (!isset($needyb)) {
				$rwid = questsController::OnFinish ( $roleInfo, "'zs'" );
				if (! empty ( $rwid )) {
					$value ['rwid'] = $rwid;
				}
			}			
			$value ['tq'] = $updateRole ['coins'];
			$value ['hqtq'] = $getCoins;
			//$value ['sccd'] = $collectNeedTime;
			$value ['sccd'] = 7200;
			$value ['scljtq'] = 0;
			$value ['jssycs'] = $cdcs; //加速剩余次数
			$value ['jzzt'] = cityModel::jzzt ( $playersid );
			/*$xyd = guideScript::wcjb ( $roleInfo, 'wczs', 3, 2 );
			//接收新的引导
			//$xyd = guideScript::jsydsj($roleInfo,'cg');
			if ($xyd !== false) {
				$value ['ydts'] = $xyd;
			}*/
		}
		return $value;
	}
	
	//资源成长
	public static function resourceGrowth(&$roleInfo, $saveData = false, $updateLoginTime = false) {
		global $common, $db, $_SGLOBAL, $mc;
		//$roleInfo['playersid'] = $playersid;
		//roleModel::getRoleInfo($roleInfo);
		$playersid = $roleInfo ['playersid'];
		$speed = 1; //资源成长速度，测试用，实际不需要
		$duration = $_SGLOBAL ['timestamp'] - $roleInfo ['last_update_food']; //上次更新与本次间隔时长 
		if ($duration < 0) {
			$duration = 0;
		}
		$food = $roleInfo ['food']; //当前军粮量
		$food_efficiency = round ( 1 / 300, 3 ) * $speed; //军粮效率，单位同上，每5分钟一点
		$foodoutput = round ( $duration * $food_efficiency, 3 ); //军粮最终产出量
		//$food_level = $roleInfo['food_level'];                    //军粮上限级别
		$uplimit = foodUplimit ( $roleInfo ['player_level'] ); //理论军粮上限
		//$vip = $roleInfo ['vip'];
		//$vipfood = 0;
		//$foodUplimitVip = 0;
		//$vipgrowth = 0;
		/*if ($vip == 1) {
			$end_vip = $roleInfo ['vip_end_time'];
			$now = $_SGLOBAL['timestamp'];
			if ($end_vip >= $now) {
				$addUp = 2;
			} else {
				$addUp = 0;
			}
			if ($end_vip > $roleInfo ['last_update_food'] && $end_vip < $now) {
				$vipgrowth = 1;
				$vipfood = round ( ($end_vip - $roleInfo ['last_update_food']) * $food_efficiency, 3 );
				$foodUplimitVip = $uplimit + $addUp;
			}
		} else {
			$addUp = 0;
		}*/
		//$addUp = 0;
		//$foodUplimit = $uplimit + $addUp; //军粮上限
		if ($food + $foodoutput >= $uplimit) {
			if ($food < $uplimit) {
				$update ['food'] = $uplimit;
			}
		} else {
			$update ['food'] = $food + $foodoutput;
		}
		$update ['last_update_food'] = $_SGLOBAL ['timestamp'];
		$update ['coins'] = $roleInfo ['coins'];
		if ($updateLoginTime == true) {
			//$update ['last_login_time'] = $_SGLOBAL ['timestamp'];
			//$update ['rcrws'] = $roleInfo['rcrws'];
			//$mc->set(MC."last_login_time_".$roleInfo['userid'], $roleInfo['last_login_time'], 0, 0);	
			$update ['phone'] = $roleInfo['phone'];
			$update ['hyd'] = $roleInfo['hyd'];
			$update ['hyjl'] = $roleInfo['hyjl'];		
		}	
		//$update['silver'] = $roleInfo['silver'];
		if ($saveData == true) {
			$wherearrsql ['playersid'] = $roleInfo ['playersid'];
			$common->updatetable ( 'player', $update, $wherearrsql );
		}
		if (! empty ( $update ['food'] )) {
			$roleInfo ['food'] = $update ['food'];
			$roleInfo ['last_update_food'] = $update ['last_update_food'];
		}
		heroCommon::updateMemCache ( MC . $playersid, $update );
		//print_r($update);
	}
	
	//获取城池基本信息
	public static function getCityBaseInfo($getInfo, $showState = 1) {
		global $db, $common, $mc, $_SGLOBAL, $_SC, $city_lang, $sys_lang;
		$nowTime = $_SGLOBAL ['timestamp'];
		$roleInfo ['playersid'] = $getInfo ['playersid'];
		$more = _get ( 'more' );
		$result = roleModel::getRoleInfo ( $roleInfo );
		//cityModel::resourceGrowth($roleInfo,false);
		if (! empty ( $result )) {
			cityModel::saveOnlieData($roleInfo ['playersid']);
			if ($showState == 1) {
				$value ['status'] = 0; //返回数据是否查询成功状态,1为成功
			}
			if ($roleInfo ['is_reason'] == 1) {
				$value = array ('status' => 21, 'message' => $city_lang['zlck_1'] );
				return $value;
			}
			/*设置回防*/
			if ($roleInfo ['aggressor_playersid'] != $roleInfo ['playersid'] && $roleInfo ['aggressor_playersid'] != 0 && $roleInfo ['end_defend_time'] < $nowTime) {
				$bzlGinfo = cityModel::getGeneralData ( $roleInfo ['playersid'], false, '*' );
				$gen = 0;
				if ($roleInfo ['zf_aggressor_general'] != 0) {
					if (! empty ( $bzlGinfo )) {
						foreach ( $bzlGinfo as $bzlGinfoValue ) {
							if ($bzlGinfoValue ['intID'] == $roleInfo ['zf_aggressor_general'] && $bzlGinfoValue ['act'] == 0) {
								$updateRole ['aggressor_playersid'] = $roleInfo ['playersid'];
								$updateRole ['aggressor_nickname'] = mysql_escape_string($roleInfo ['nickname']);
								$updateRole ['aggressor_level'] = $bzlGinfoValue ['general_level'];
								$updateRole ['is_defend'] = 1;
								$updateRole ['end_defend_time'] = $roleInfo['end_defend_time'];
								$updateRole ['zf_aggressor_general'] = $bzlGinfoValue ['intID'];
								$gen = 1;
								$bzlGinfoValue ['occupied_playersid'] = $bzlupdateGen ['occupied_playersid'] = $roleInfo ['playersid'];
								$bzlGinfoValue ['occupied_player_level'] = $bzlupdateGen ['occupied_player_level'] = $roleInfo ['player_level'];
								$bzlGinfoValue ['occupied_player_nickname'] = $bzlupdateGen ['occupied_player_nickname'] = mysql_escape_string($roleInfo ['nickname']);
								$bzlGinfoValue ['last_income_time'] = $bzlupdateGen ['last_income_time'] = $roleInfo['end_defend_time'];
								$bzlGinfoValue ['occupied_end_time'] = $bzlupdateGen ['occupied_end_time'] = $roleInfo['end_defend_time'];
								$gzlnewdData [$bzlGinfoValue ['sortid']] = $bzlGinfoValue;
								$common->updateMemCache ( MC . $roleInfo ['playersid'] . '_general', $gzlnewdData );
								$bzlwhere ['intID'] = $bzlGinfoValue ['intID'];
								$common->updatetable ( 'playergeneral', $bzlupdateGen, $bzlwhere );
								break;
							}
						}
					}
					if ($gen == 1) {
						$updateRole ['strategy'] = $roleInfo ['zf_strategy'];
						$updateRole ['aggressor_general'] = $roleInfo ['zf_aggressor_general'];
					} else {
						$updateRole ['strategy'] = 0;
						$updateRole ['aggressor_general'] = 0;
					}
				} else {
					$updateRole ['strategy'] = 0;
					//$updateBzl['aggressor_general'] = 0;			    	
					if (! empty ( $bzlGinfo )) {
						foreach ( $bzlGinfo as $bzlGinfoValue ) {
							if (($bzlGinfoValue ['occupied_playersid'] == 0 || ($bzlGinfoValue ['occupied_playersid'] != 0 && $bzlGinfoValue ['occupied_playersid'] != $roleInfo ['playersid'] && $bzlGinfoValue ['occupied_end_time'] < $nowTime)) && $bzlGinfoValue['act'] == 0) {
								$updateRole ['aggressor_playersid'] = $roleInfo ['playersid'];
								$updateRole ['aggressor_nickname'] = mysql_escape_string($roleInfo ['nickname']);
								$updateRole ['aggressor_level'] = $bzlGinfoValue ['general_level'];
								$updateRole ['is_defend'] = 1;
								$updateRole ['end_defend_time'] = $roleInfo['end_defend_time'];
								$updateRole ['zf_aggressor_general'] = $bzlGinfoValue ['intID'];
								$updateRole ['aggressor_general'] = $bzlGinfoValue ['intID'];
								$gen = 1;
								$bzlGinfoValue ['occupied_playersid'] = $bzlupdateGen ['occupied_playersid'] = $roleInfo ['playersid'];
								$bzlGinfoValue ['occupied_player_level'] = $bzlupdateGen ['occupied_player_level'] = $roleInfo ['player_level'];
								$bzlGinfoValue ['occupied_player_nickname'] = $bzlupdateGen ['occupied_player_nickname'] = mysql_escape_string($roleInfo ['nickname']);
								$bzlGinfoValue ['last_income_time'] = $bzlupdateGen ['last_income_time'] = $roleInfo['end_defend_time'];
								$bzlGinfoValue ['occupied_end_time'] = $bzlupdateGen ['occupied_end_time'] = $roleInfo['end_defend_time'];
								$gzlnewdData [$bzlGinfoValue ['sortid']] = $bzlGinfoValue;
								$common->updateMemCache ( MC . $roleInfo ['playersid'] . '_general', $gzlnewdData );
								$bzlwhere ['intID'] = $bzlGinfoValue ['intID'];
								$common->updatetable ( 'playergeneral', $bzlupdateGen, $bzlwhere );
								break;
							}
						}
					}
					if ($gen == 0) {
						$updateRole ['aggressor_general'] = 0;
					}
				}
				$updateBzlWhere ['playersid'] = $roleInfo ['playersid'];
				$common->updatetable ( 'player', $updateRole, $updateBzlWhere );
				$common->updateMemCache ( MC . $roleInfo ['playersid'], $updateRole );
			}
			/*设置回防结束*/
			$zfInfo = cityModel::getZfInfo ( $roleInfo['playersid'] ); //驻防信息
			if ($zfInfo ['status'] == 0) {				
				$value ['gid'] = $zfInfo ['gid'];
				if ($value ['gid'] > 0) {
					$value ['oct'] = 0;
				} else {
					$value ['oct'] = 2;
				}
				$value ['giid'] = $zfInfo ['giid']; //占领自己城池的将领ICON id
				$value ['gname'] = $zfInfo ['gname']; //占领自己城池的将领名称
				$value ['glevel'] = $zfInfo ['glevel'];
				$value ['gxyd'] = $zfInfo ['gxyd'];
				$value ['time'] = $zfInfo ['time'];
				$value ['dzljtq'] = $zfInfo ['sy'];
				//$value['mfzzz'] = $zfInfo['cl'];
				$value ['zscl'] = $zfInfo ['zscl']; //驻守策略
				$value ['gxj'] = $zfInfo ['gxj'];
				$value['gzy'] = $zfInfo['gzy'];
				$value['zscl'] = $zfInfo['zscl'];
			} elseif ($zfInfo ['status'] == 1001) {				
				$value ['tk'] = $zfInfo ['tk'];
				$value ['uid'] = $zfInfo ['uid'];
				$value ['gid'] = $zfInfo ['gid'];
				if ($value ['gid'] > 0) {
					$value ['oct'] = 1;
				} else {
					$value ['oct'] = 2;
				}
				$value ['giid'] = $zfInfo ['giid']; //占领自己城池的将领ICON id
				$value ['gname'] = $zfInfo ['gname']; //占领自己城池的将领名称
				$value ['glevel'] = $zfInfo ['glevel'];
				$value ['gxyd'] = $zfInfo ['gxyd'];
				$value ['un'] = $zfInfo ['un'];
				$value ['ul'] = $zfInfo ['ul'];
				$value ['time'] = $zfInfo ['time'];
				$value ['dzljtq'] = $zfInfo ['sy'];
				$value ['gxj'] = $zfInfo ['gxj'];
				$value['gzy'] = $zfInfo['gzy'];
				$value['zscl'] = $zfInfo['zscl'];
				//$value['mfzzz'] = $zfInfo['cl'];
				if (! empty ( $zfInfo ['zlxy'] )) {
					$value ['zlxy'] = $zfInfo ['zlxy'];
				}
			} else {
				$value ['oct'] = 2;
			}
			$value ['gbnr'] = lettersModel::getPublicNotice (); //广播内容(有则反)(暂无)
			$showValue = lettersModel::publicParameters ( $roleInfo ['playersid'] );
			$value ['xrzsl'] = $showValue ['sl']; //新日志数量，没有新日志则返0(暂无)
/*hk*/			$value ['tq'] = intval($roleInfo ['coins']);//铜钱取整
/*hk*/			$value ['jl'] = intval($roleInfo ['food']);//返回军粮
			$value ['jzzt'] = cityModel::jzzt ( $roleInfo ['playersid'] );
			// 获取开矿次数
			$value ['ykccs'] = cityModel::checkMineTimes ( $roleInfo );
			$wk_sz = wksjxz ();
			$wk_sy_cd = ($roleInfo ['last_wk_time'] + intval ( $wk_sz [0] )) - $_SGLOBAL ['timestamp'];
			if ($value ['ykccs'] < 10) {
				$value ['kscd'] = $wk_sy_cd < 0 ? 0 : $wk_sy_cd; //矿山cd剩余时间
			}
			
			$value ['kczcs'] = 10;
			$collectNeedTime = collectNeedTime (); //间隔时间
			$timeDiff = $_SGLOBAL ['timestamp'] - $roleInfo ['last_collect_time']; //与上次征税时间间隔
			//$sc_sy_cd = $collectNeedTime - $timeDiff; //还剩时间
			$sc_sy_cd = 7200 - $timeDiff; //还剩时间
			$value ['sccd'] = $sc_sy_cd > 0 ? $sc_sy_cd : 0; //市场cd剩余时间  						

			$player_level = $roleInfo ['player_level'];
			if (! empty ( $more )) {
				$value ['binfo'] = cityModel::getBuildInfo ( $roleInfo );
			}
			$value['yb'] = intval($roleInfo['ingot']);
			$value['yp'] = intval($roleInfo['silver']);
			$value['vip'] = intval($roleInfo['vip']);
			$lysl = cityModel::hqlysl ( $roleInfo ['playersid'] );
			$value ['xlysl'] = intval ( $lysl ); //留言数量
			/*获得强征信息*/
			$collectNeedTime = collectNeedTime ();
			$vip = $roleInfo ['vip'];

			//返回当前的市场累计铜钱
			$buildItem = 'sc_level';			
			switch ($roleInfo ['vip']) {
				case 1:
					$rate = 1;
					break;					
				case 2 :
					$rate = 1.1;
					break;
				case 3 :
					$rate = 1.15;
					break;
				/*case 4 :
					$rate = 1.2;
					break;*/
				default :
					$rate = 1.2;
					break;
			}			
						
			$getCoinsTal = round ( getCoinsAmount ( $roleInfo [$buildItem] ) * $rate, 0 );
			
			// 当前的银库容量加成
			$ykrljc = $roleInfo ['ykzjrl'] / 100;
			if($ykrljc > 0) {
				$getCoinsTal += $getCoinsTal * $ykrljc;
			}
		
			// 计算当前可征收铜钱数量
			if(($collectNeedTime - $timeDiff) <= 0) $timeDiff = $collectNeedTime;
			$per_minute_coins = round(round ( $getCoinsTal, 0 ) / ($collectNeedTime/60), 3);			
			$getCoinsTal = round(round($timeDiff / 60, 0) * $per_minute_coins, 0);
			$value['scljtq'] = $getCoinsTal;
			
			if ($vip >= 4) {
				$cdcs = 4;
			} elseif ($vip == 3) {
				$cdcs = 3;
			} elseif ($vip == 2) {
				$cdcs = 2;
			} elseif ($vip == 1) {
				$cdcs = 1;
			} else {
				$vip = 0;
				$cdcs = 0;
			}	
			//$value ['qzcs'] = - 1;
			$value['zg'] = intval($roleInfo['ba']);
			//$value ['qzyb'] = 10;			
			//获取任务状态信息
			/*$rwzt = getDataAboutLevel::hqrwzt ( $roleInfo ['playersid'] );
			$value ['rw'] = intval($rwzt['rw']);
			$value['xrwsl'] = intval($rwzt['xrwsl']);*/
			if (empty ( $_SESSION ['xsrw'] )) {
				//返回已完成任务ID
				if (! ($qStatus = $mc->get ( MC . $roleInfo ['playersid'] . "_qstatus" ))) {
					$sql = "SELECT * FROM " . $common->tname ( 'quests_new_status' ) . " WHERE playersid = '" . $roleInfo ['playersid'] . "' LIMIT 1";
					$result = $db->query ( $sql );
					$rows = $db->fetch_array ( $result );
					$qStatus = $rows ['qstatusInfo'];
					$mc->set ( MC . $roleInfo ['playersid'] . "_qstatus", $qStatus, 0, 3600 );
				}
				if (! empty ( $qStatus )) {
					$qstatusInfo = unserialize ( $qStatus );
					$completeQuests = $roleInfo['completeQuests'];
					if (empty($completeQuests)) {
						$completeQuestsArray = array();
					} else {
						$completeQuestsArray = explode(',',$completeQuests);
					}
					if (count ( $qstatusInfo ) > 0) {
						foreach ( $qstatusInfo as $qkey => $qstatusInfoValue ) {
							if ($qstatusInfoValue == 1) {
								if (in_array($qkey,array(1001301,1001302,1001303,1001304,1001307,3000000,3000001,3000002,3001000,3002000,3003000,1001401,1001402,1001403,1001409,1001410,1001508,1001439))) {
									continue;
								} elseif (in_array($qkey,array(1001308,1001309,1001310,1001311,1001312,1001313,1001314)) && $roleInfo['vip'] > 3) {
									continue;
								} else {
									$rwInfo = ConfigLoader::GetQuestProto($qkey);
									if ($rwInfo['RepeatInterval'] == 2 || $rwInfo['RepeatInterval'] == 0) {
										$value ['rwid'] = $rwInfo['RepeatInterval'].'_'.$qkey;
										break;
									}									
								}
							}
						}
					}
				}
				$_SESSION ['xsrw'] = 1;
			}
			
			/*if (empty ( $_SESSION ['ydts'] )) {
				//显示引导脚本	
				$xsydjb = guideScript::xsjb ( $roleInfo, 'player_level' );
				if ($xsydjb !== false) {
					$value ['ydts'] = $xsydjb;
				}
				$_SESSION ['ydts'] = 1;
			}*/
			//显示按钮ID
			/*$buttonID = roleModel::hqxsanid ( $player_level );
			$debug = _get('debug');
			if (defined("PRESSURE_TEST_DEBUG") && !empty($debug) ) {
				$value ['xs'] = '';
			} else {
				if ($buttonID != false) {
					$value ['xs'] = $buttonID;
				}
			}*/
/*hk*/			//检查是否返回背包列表
			$xsbb = $mc->get(MC.$roleInfo['playersid'].'_xsbb');
			if ($xsbb == 1) {
				$bbInfo = toolsModel::getAllItems($roleInfo['playersid'], true);
				$value['list'] = $bbInfo['list'];	
				$mc->delete(MC.$roleInfo['playersid'].'_xsbb');			
			}	/*hk*/		
			// 返回聊天信息
			// 0世界频道 1工会频道 2私聊频道
			if (!($disable = $mc->get(MC.$roleInfo['playersid'] . '_chat_disble'))) {
				$disable_ret = $db->query("SELECT to_pid FROM ".$common->tname('chat_blacklist')." WHERE from_pid = {$roleInfo['playersid']}");
				$disable = $db->fetch_array($disable_ret, MYSQL_NUM);
				$disable = $disable[0];
				$mc->set(MC.$roleInfo['playersid'] . '_chat_disble', $disable, 0, 3600);
			}
			
			if($disable != false) {
				if(strpos($disable, ',') !== false) {
					$disable_arr = explode(',', $disable);
				} else {
					$disable_arr[] = $disable;
				} 
			} else {
				$disable_arr = array();
			}
		
			$ltlx = 0;
			if($ltlx == 0) {
				$mem_key = 'Chat_World-channel';
			} else if($ltlx == 2) {
				$mem_key = 'Private-channel';
			} else {
				$mem_key = 'Guild-channel'; 
			}
			if (!($worldChatInfo = $mc->get(MC.$mem_key))) {
				$worldChatInfo = '';
				$mc->set(MC.$mem_key, $worldChatInfo, 0, 3600);
			}
			if($worldChatInfo != '') {
				$worldChatInfo = array_reverse($worldChatInfo);
				date_default_timezone_set('PRC');
				$jl = array();
				//$common->insertLog(json_encode($worldChatInfo));
				foreach ($worldChatInfo as $k=>$v) {
					if($k < 10) {
						if(in_array($v['pid'], $disable_arr) === false) {
							$wjmc = $v['pid'] == $roleInfo ['playersid'] ? $city_lang['getCityBaseInfo_1'] : $v['mc'];
							$diff_time = time() - $v['dt'] < 0 ? 1 : time() - $v['dt'];
							if($diff_time > 60)	
								$dt = ceil($diff_time/60) . $city_lang['ltmsg1'][1];
							else
								$dt = $diff_time . $city_lang['ltmsg1'][0];
							$jl[] = array(
									'mc'=>$wjmc,
									'nr'=>json_decode($v['nr']),
									'pid'=>intval($v['pid']),
									'dt'=>$dt
							);
						}
					} else {
						break;
					}
				}
				
				$value['ltjl'] = $jl;
			} else {
				$value['ltjl'] = '';
			}
			$value['ysl'] = ($mc->get(MC.$roleInfo['playersid'].'_private-channel_newflag')) ? 1 : 0;
			// 检查玩家绑定手机号状态,999999表示需要提醒玩家绑定手机号
			if(isset($roleInfo['phone'])&&$roleInfo['phone']=='999999'){
				$value['bdsj'] = 1;
				$bdsjWhere ['playersid'] = $roleInfo ['playersid'];
				$updateRole = array('phone'=>'0');
				$roleInfo['phone'] = 0;
				$common->updatetable ( 'player', $updateRole, $bdsjWhere );
				$common->updateMemCache ( MC . $roleInfo ['playersid'], $roleInfo );
			}
		
		} else {
			$value ['status'] = 4; //返回数据是否查询成功状态,0为失败
			$value ['message'] = $sys_lang[1];
		}
		
		// 触发活动脚本
		hdProcess::run(array('role_endLevel', 'role_endAddFriend', 'role_endGrade'), $roleInfo);
		$jqzt = $mc->get(MC.$roleInfo['playersid'].'_jq');
		if (!empty($jqzt)) {
			$value['jq'] = 1;
		} else {
			$value['jq'] = 0;
		}
		$qdbb = qdbb($_SESSION['qdxx']);
		if (isset($qdbb['swf_version'])) {
			$_SC['swf_version'] = $qdbb['swf_version'];
		}			
		$value ['gv'] = $_SC ['swf_version'];
		return $value;
	}
	
	//请求其它玩家城池信息  type 1好友列表，2邻居列表，3仇人列表 ，4领地，5可占领，6信件，
	public static function qqwjccxx($tuid, $type = 0) {
		global $mc, $common, $_SGLOBAL, $db, $city_lang, $sys_lang;
		$nowTime = $_SGLOBAL ['timestamp'];
		$needid = roleModel::getNeedId ( $type );
		//print_r($needid);
		if (count ( $needid ) > 0) {
			$is_value = false;
			$index = 0;
			foreach ( $needid as $key => $needidValue ) {
				if ($needidValue ['id'] == $tuid) {
					$is_value = true;
					$index = $key;
					break;
				}
			}
			if ($is_value == true) {
				$value ['list'] = $needid;
				$value ['index'] = $index;
			} else {
				$value ['tuid'] = $tuid;
			}
		}
		
		$roleInfo ['playersid'] = $tuid;
		$result = roleModel::getRoleInfo ( $roleInfo, false );
		if (! empty ( $result )) {
			cityModel::resourceGrowth ( $roleInfo );			
			/*设置回防*/
			if ($roleInfo ['aggressor_playersid'] != $roleInfo ['playersid'] && $roleInfo ['aggressor_playersid'] != 0 && $roleInfo ['end_defend_time'] < $nowTime) {
				$bzlGinfo = cityModel::getGeneralData ( $roleInfo ['playersid'], false, '*' );
				$gen = 0;
				if ($roleInfo ['zf_aggressor_general'] != 0) {
					if (! empty ( $bzlGinfo )) {
						foreach ( $bzlGinfo as $bzlGinfoValue ) {
							if ($bzlGinfoValue ['intID'] == $roleInfo ['zf_aggressor_general'] && $bzlGinfoValue ['act'] == 0) {
								$updateRole ['aggressor_playersid'] = $roleInfo ['playersid'];
								$updateRole ['aggressor_nickname'] = mysql_escape_string($roleInfo ['nickname']);
								$updateRole ['aggressor_level'] = $bzlGinfoValue ['general_level'];
								$updateRole ['is_defend'] = 1;
								$updateRole ['end_defend_time'] = $roleInfo['end_defend_time'];
								$updateRole ['zf_aggressor_general'] = $bzlGinfoValue ['intID'];
								$gen = 1;
								$bzlGinfoValue ['occupied_playersid'] = $bzlupdateGen ['occupied_playersid'] = $roleInfo ['playersid'];
								$bzlGinfoValue ['occupied_player_level'] = $bzlupdateGen ['occupied_player_level'] = $roleInfo ['player_level'];
								$bzlGinfoValue ['occupied_player_nickname'] = $bzlupdateGen ['occupied_player_nickname'] = mysql_escape_string($roleInfo ['nickname']);
								$bzlGinfoValue ['last_income_time'] = $bzlupdateGen ['last_income_time'] = $roleInfo['end_defend_time'];
								$bzlGinfoValue ['occupied_end_time'] = $bzlupdateGen ['occupied_end_time'] = $roleInfo['end_defend_time'];
								$gzlnewdData [$bzlGinfoValue ['sortid']] = $bzlGinfoValue;
								$common->updateMemCache ( MC . $roleInfo ['playersid'] . '_general', $gzlnewdData );
								$bzlwhere ['intID'] = $bzlGinfoValue ['intID'];
								$common->updatetable ( 'playergeneral', $bzlupdateGen, $bzlwhere );
								break;
							}
						}
					}
					if ($gen == 1) {
						$updateRole ['strategy'] = $roleInfo ['zf_strategy'];
						$updateRole ['aggressor_general'] = $roleInfo ['zf_aggressor_general'];
					} else {
						$updateRole ['strategy'] = 0;
						$updateRole ['aggressor_general'] = 0;
					}
				} else {
					$updateRole ['strategy'] = 0;
					//$updateBzl['aggressor_general'] = 0;			    	
					if (! empty ( $bzlGinfo )) {
						foreach ( $bzlGinfo as $bzlGinfoValue ) {
							if (($bzlGinfoValue ['occupied_playersid'] == 0 || ($bzlGinfoValue ['occupied_playersid'] != 0 && $bzlGinfoValue ['occupied_playersid'] != $roleInfo ['playersid'] && $bzlGinfoValue ['occupied_end_time'] < $nowTime)) && $bzlGinfoValue['act'] == 0) {
								$updateRole ['aggressor_playersid'] = $roleInfo ['playersid'];
								$updateRole ['aggressor_nickname'] = mysql_escape_string($roleInfo ['nickname']);
								$updateRole ['aggressor_level'] = $bzlGinfoValue ['general_level'];
								$updateRole ['is_defend'] = 1;
								$updateRole ['end_defend_time'] = $roleInfo['end_defend_time'];
								$updateRole ['zf_aggressor_general'] = $bzlGinfoValue ['intID'];
								$updateRole ['aggressor_general'] = $bzlGinfoValue ['intID'];
								$gen = 1;
								$bzlGinfoValue ['occupied_playersid'] = $bzlupdateGen ['occupied_playersid'] = $roleInfo ['playersid'];
								$bzlGinfoValue ['occupied_player_level'] = $bzlupdateGen ['occupied_player_level'] = $roleInfo ['player_level'];
								$bzlGinfoValue ['occupied_player_nickname'] = $bzlupdateGen ['occupied_player_nickname'] = mysql_escape_string($roleInfo ['nickname']);
								$bzlGinfoValue ['last_income_time'] = $bzlupdateGen ['last_income_time'] = $roleInfo['end_defend_time'];
								$bzlGinfoValue ['occupied_end_time'] = $bzlupdateGen ['occupied_end_time'] = $roleInfo['end_defend_time'];
								$gzlnewdData [$bzlGinfoValue ['sortid']] = $bzlGinfoValue;
								$common->updateMemCache ( MC . $roleInfo ['playersid'] . '_general', $gzlnewdData );
								$bzlwhere ['intID'] = $bzlGinfoValue ['intID'];
								$common->updatetable ( 'playergeneral', $bzlupdateGen, $bzlwhere );
								break;
							}
						}
					}
					if ($gen == 0) {
						$updateRole ['aggressor_general'] = 0;
					}
				}
				$updateBzlWhere ['playersid'] = $roleInfo ['playersid'];
				$common->updatetable ( 'player', $updateRole, $updateBzlWhere );
				$common->updateMemCache ( MC . $roleInfo ['playersid'], $updateRole );
			}
			/*设置回防结束*/					
			$zfInfo = cityModel::getZfInfo ( $roleInfo ['playersid'] ); //驻防信息
			if ($zfInfo ['status'] == 1001 || $zfInfo ['status'] == 0) {
				$value ['status'] = $zfInfo ['status'];
				$value ['gid'] = $zfInfo ['gid'];
				$value ['giid'] = $zfInfo ['giid']; //占领自己城池的将领ICON id
				$value ['gname'] = $zfInfo ['gname']; //占领自己城池的将领名称
				$value ['glevel'] = $zfInfo ['glevel'];
				$value ['gxyd'] = $zfInfo ['gxyd'];
				$value ['ul'] = $zfInfo ['ul'];
				$value ['un'] = $zfInfo ['un'];
				$value ['dzljtq'] = $zfInfo ['sy'];
				$value ['tqzzl'] = $zfInfo ['tqzzl'];
				$value ['time'] = $zfInfo ['time'];
				$value ['gxj'] = $zfInfo ['gxj'];
				$value['gzy'] = $zfInfo['gzy'];
				$value['zscl'] = $zfInfo['zscl'];
				if ($zfInfo ['status'] == 1001) {
					$value ['uid'] = $zfInfo ['uid'];
					if (! empty ( $zfInfo ['zlxy'] )) {
						$value ['zlxy'] = $zfInfo ['zlxy'];
					}
				}
			} else {
				$value ['status'] = 1002;
			}
			
			$value ['zscl'] = intval ( $roleInfo ['strategy'] );
			$value ['vip'] = $roleInfo['vip'];
			
			$value ['scdj'] = intval ( $roleInfo ['sc_level'] ); //市场等级
			$value ['jgdj'] = intval ( $roleInfo ['jg_level'] ); //酒馆等级
			if ($roleInfo ['player_level'] < 3) {
				$value ['tjpdj'] = 's';
			} else {
				$value ['tjpdj'] = intval ( $roleInfo ['tjp_level'] ); //铁匠铺等级
			}
			if ($roleInfo ['player_level'] < 5) {
				$value ['lddj'] = 's';
			} else {
				$value ['lddj'] = intval ( $roleInfo ['ld_level'] ); //领地等级
			}
			$value ['djtdj'] = intval ( $roleInfo ['djt_level'] ); //点将台等级
			

			$value ['name'] = $roleInfo ['nickname'];
			$value ['level'] = intval ( $roleInfo ['player_level'] );
			$value ['sex'] = intval ( $roleInfo ['sex'] );
			$value ['gz'] = intval ( $roleInfo ['vip'] );
			$jw = jwmc ( $roleInfo ['mg_level'] );
			$value ['jw'] = $jw ['mc'];
			$value ['gx'] = cityModel::getUserRelations ( $_SESSION ['playersid'], $tuid );
			$value['zc'] = $roleInfo['rank'];
			
			// 获取自己的信息
			$selfInfo ['playersid'] = $_SESSION ['playersid'];
			$roleRes = roleModel::getRoleInfo ( $selfInfo );
			if (empty ( $roleRes )) {
				$value = array ('status' => 1021, 'message' => $sys_lang[1] );
				return $value;
			}
			
			$value ['box'] = socialModel::is_Box ( $selfInfo ['playersid'], $roleInfo ['playersid'], $selfInfo ['boxOpen'], $selfInfo ['boxTime'] );
			
			$value ['is_sl'] = socialModel::is_TodayGift ( $_SESSION ['playersid'], $roleInfo ['playersid'] ); //是否能送礼
			$value ['is_dj'] = socialModel::is_TodayRobbery ( $_SESSION ['playersid'], $roleInfo ['playersid'] );
			$value ['is_sy'] = socialModel::is_TodaySy ( $_SESSION ['playersid'], $roleInfo ['playersid'] );
			// 是否显示加好友
			if ($value ['gx'] != 1 && $value ['gx'] != 3) {
				$value ['is_jhy'] = socialModel::is_oneDayCount ( $_SESSION ['playersid'], $tuid, 1, 2 );
				if(1 == $value['is_jhy']){
					$value['is_jhy'] = socialModel::checkFriendLimit($roleInfo)?1:0;
				}
			}
			
			// 获取每个城池状态，比如偷矿和偷将
			$tempSbAPI = cityModel::jzzt ( $roleInfo ['playersid'], true );
			$tempSbAPI = explode ( ',', $tempSbAPI );
			$tempSbAPI [4] = in_array ( $type, array (3 ) ) ? 0 : $tempSbAPI [4];
			$value ['jzzt'] = implode ( ',', $tempSbAPI );
			$lySql = "SELECT COUNT(intID) as sl FROM ".$common->tname('city_msg_blacklist')." WHERE from_pid = $tuid && to_pid = ".$_SESSION['playersid']." LIMIT 1";
			$lyRes = $db->query($lySql);
			$lyRows = $db->fetch_array($lyRes);
			if ($lyRows['sl'] > 0){
				$value['isly'] = 0;
			} else {
				$value['isly'] = 1;
			}
		} else {
			$value ['status'] = 1021;
			$value ['message'] = $sys_lang[1];
		}
		return $value;
	}
	
	//获取玩家物品信息
	public static function getArticlesInfo($articleid = '', $playerid = '', $propsid = '', $article_type = '') {
		global $common, $db;
		if (! empty ( $articleid )) {
			$item1 = " && articleid = '$articleid'";
		} else {
			$item1 = "";
		}
		if (! empty ( $playerid )) {
			$item2 = " && playerid = '$playerid'";
		} else {
			$item2 = "";
		}
		if (! empty ( $propsid )) {
			$item3 = " && propsid = '$propsid'";
		} else {
			$item3 = "";
		}
		if (! empty ( $article_type )) {
			$item4 = " && article_type = '$article_type'";
		} else {
			$item4 = "";
		}
		if (empty ( $articleid ) && empty ( $playerid ) && empty ( $propsid ) && empty ( $article_type )) {
			return false;
		} else {
			$result = $db->query ( "SELECT * FROM " . $common->tname ( 'articles' ) . " WHERE 1=1 $item1 $item2 $item3 $item4" );
			$rows = $db->fetch_array ( $result );
			$db->free_result ( $result );
			return $rows;
		}
	}
	
	//获取将领信息
	public static function getGeneralInfo($playerid, $selectItem = '*', $general_state = '', $orderby = 1) {
		global $common, $db;
		if (! empty ( $orderby )) {
			$orderby = "ORDER BY f_status DESC,general_sort ASC";
		}
		if (! empty ( $general_state )) {
			$item_general_state = "&& general_state = '$general_state' && command_soldier > 0";
		} else {
			$item_general_state = '';
		}
		$result = $db->query ( "SELECT $selectItem FROM " . $common->tname ( 'playergeneral' ) . " WHERE `playerid` = '$playerid' $item_general_state $orderby LIMIT 24" );
		$value = array ();
		$i = 0;
		while ( $rows = $db->fetch_array ( $result ) ) {
			$rows ['sortid'] = $i ++;
			$value [] = $rows;
		}
		$db->free_result ( $result );
		if (! empty ( $value )) {
			return $value;
		} else {
			return false;
		}
	}
	
	//获取将领数据到内存
	public static function getGeneralData($playersid, $resetMca = false, $intID, $resetMc = true) {
		global $db, $common, $mc;
		if (! ($generalArray = $mc->get ( MC . $playersid . '_general' )) || ! empty ( $resetMca )) {
			$generalArray = cityModel::getGeneralInfo ( $playersid );
			if (empty ( $generalArray )) {
				return false;
			}
			if ($resetMc == true) {
				$mc->set ( MC . $playersid . '_general', $generalArray, 0, 3600 );
			}
		}
		if ($intID === 0) {
			return false;
		}
		if ($intID == '*') {
			return $generalArray;
		} else {
			$value = array ();
			for($i = 0; $i < count ( $generalArray ); $i ++) {
				if ($generalArray [$i] ['intID'] == $intID) {
					$generalArray [$i] ['sortid'] = $i;
					$value [] = $generalArray [$i];
					break;
				}
			}
			return $value;
		}
	}
  
	private static function GetZBData(&$zbInfo) {
	    $zbProto = toolsModel::getItemInfo($zbInfo ['ItemID'] );
	    return array ('lx' => intval ( $zbProto ['ItemType'] ), 'st' => intval ( $zbProto ['EquipType'] ), 'wid' => intval ( $zbInfo ['ID'] ), 'qhcs' => intval ( $zbInfo ['QhLevel'] ), 'iconid' => $zbProto ['IconID'], 'num' => intval ( $zbInfo ['EquipCount'] ), 'jg' => intval ( substr ( $zbProto ['SellPirce'], 1 ) ), 'mc' => $zbProto ['Name'], 'sm' => $zbProto ['Description'], 'gj' => intval ( $zbProto ['Attack_value'] ), 'fy' => intval ( $zbProto ['Defense_value'] ), 'tl' => intval ( $zbProto ['Physical_value'] ), 'mj' => intval ( $zbProto ['Agility_value'] ), 'gjt' => intval ( $zbProto ['Addition_attack_value'] ), 'fyt' => intval ( $zbProto ['Addition_defense_value'] ), 'tlt' => intval ( $zbProto ['Addition_physical_value'] ), 'mjt' => intval ( $zbProto ['Addition_agility_value'] ), 'tzid' => intval ( $zbProto ['SuitID'] ), 'gqh' => 0, 'fqh' => 0, 'tqh' => 0, 'mqh' => 0, 'jb' => intval ( $zbProto ['LevelLimit'] ), 'xyd' => intval ( $zbProto ['Rarity'] ), 'zy' => intval ( $zbProto ['Zhiye'] ) );
	}
	
	//获取将领tab列表数据  
	public static function getGeneralList($getInfo, $showstate = 1, $resetMca = false, $gid = '*', $resetMc = true) {
		global $common, $mc, $db, $_SGLOBAL, $G_PlayerMgr, $sys_lang, $city_lang;
		$playerid = $getInfo ['playersid']; //玩家ID
    	$player = $G_PlayerMgr->GetPlayer($playerid);
    	if (empty ( $player )) 
			return array('status' => 1021, 'message' => $sys_lang[1]);
		$generalArray = cityModel::getGeneralData ( $playerid, $resetMca, $gid, $resetMc );
		$nowTime = $_SGLOBAL ['timestamp'];
		if (empty ( $generalArray )) 
			return array('status' => 1021, 'message' => $sys_lang[3]);

    	$roleInfo=$player->baseinfo_;
		$scjl = $roleInfo ['zf_aggressor_general']; //守备将领编号
		$jwdj = $roleInfo ['mg_level'];
		$jwInfo = jwmc ( $jwdj );
		$jwjc = 1 + $jwInfo ['jc'] / 100;
		$array = array ();
		$zbidArray = array ();
		$zc = $roleInfo['rank'];
		$zllist = array();
		$jjlist = array();
		$lles = $llss = $llmax = array();
		$py_200 = $py_400 = $py_600 = $py_800 = $py_1000 = $py_1200 = $py_1400 = $py_1600 = $py_1800 = $jn_3 = $jn_5 = $jn_7 = array();
		$jnlist = array();
		$zswq = array();
		$gtzs = array();
    	static $zbslots = array('helmet','carapace','arms','shoes');
		for($i = 0, $gl = count ( $generalArray ); $i < $gl; ++$i) {
			$zbgjjcArray = array ();
			$zbfyjcArray = array ();
			$zbtljcArray = array ();
			$zbmjjcArray = array ();
      		foreach ($zbslots as $slot) {
	        $zbid = $generalArray [$i] [$slot];
		        if ($zbid != 0) {
		          $zbInfo = $player->GetZBSX ($zbid );
		          $zbgjjcArray [] = $zbInfo['gj'];
		          $zbfyjcArray [] = $zbInfo['fy'];
		          $zbtljcArray [] = $zbInfo['tl'];
		          $zbmjjcArray [] = $zbInfo['mj'];
		        }
	        }
			$zbgjjc = array_sum ( $zbgjjcArray );
			$zbfyjc = array_sum ( $zbfyjcArray );
      	    $zbtljc = array_sum ( $zbtljcArray );
      		$zbmjjc = array_sum ( $zbmjjcArray );
  			if ($generalArray [$i]['occupied_playersid'] != 0 && $generalArray [$i]['occupied_playersid'] != $playerid && $generalArray [$i]['occupied_end_time'] > time()) {
  				$zllist[] = $generalArray [$i]['occupied_playersid'];
  			} 
  			if ($generalArray [$i]['professional_level'] == 3) {
  				$jjlist[] = $generalArray [$i]['intID'];
  			}
  			if ($generalArray [$i]['llcs'] >= 20) {
  				$lles[] = $jjlist[] = $generalArray [$i]['intID'];
  			} 
   			if ($generalArray [$i]['llcs'] >= 30) {
  				$llss[] = $jjlist[] = $generalArray [$i]['intID'];
  			}
    		if ($generalArray [$i]['llcs'] >= 40) {
  				$llmax[] = $jjlist[] = $generalArray [$i]['intID'];
  			}
  			$pysx = $generalArray [$i] ['py_gj'] + $generalArray [$i] ['py_fy'] + $generalArray [$i] ['py_tl'] + $generalArray [$i] ['py_mj'];
  			if ($pysx >= 200) {
  				$py_200[] = $generalArray [$i] ['intID'];
  			} 			
  			if ($pysx >= 400) {
  				$py_400[] = $generalArray [$i] ['intID'];
  			}
  			if ($pysx >= 600) {
  				$py_600[] = $generalArray [$i] ['intID'];
  			}  			
  			if ($pysx >= 800) {
  				$py_800[] = $generalArray [$i] ['intID'];
  			}
  			if ($pysx >= 1000) {
  				$py_1000[] = $generalArray [$i] ['intID'];
  			} 			 
  			if ($pysx >= 1200) {
  				$py_1200[] = $generalArray [$i] ['intID'];
  			}
  			if ($pysx >= 1400) {
  				$py_1400[] = $generalArray [$i] ['intID'];
  			}
  			if ($pysx >= 1600) {
  				$py_1600[] = $generalArray [$i] ['intID'];
  			}  			  			 
  			if ($pysx >= 1800) {
  				$py_1800[] = $generalArray [$i] ['intID'];
  			}
  			if ($generalArray [$i] ['jn1_level'] >= 7 && $generalArray [$i] ['jn2_level'] >= 7) {
  				$jnlist[] = $generalArray [$i] ['intID'];
  			}
  			if ($generalArray [$i] ['jn1_level'] >= 3 || $generalArray [$i] ['jn2_level'] >= 3) {
  				$jn_3[] = $generalArray [$i] ['intID'];
  			} 
   			if ($generalArray [$i] ['jn1_level'] >= 5 || $generalArray [$i] ['jn2_level'] >= 5) {
  				$jn_5[] = $generalArray [$i] ['intID'];
  			} 
   			if ($generalArray [$i] ['jn1_level'] >= 7 || $generalArray [$i] ['jn2_level'] >= 5) {
  				$jn_7[] = $generalArray [$i] ['intID'];
  			}    			 			 			
  			if ($generalArray [$i] ['arms'] > 0 ) {
  				$zbInfo = $player->GetItem($generalArray [$i] ['arms']);
  				if (in_array($zbInfo['ItemID'],array(LGBOW_ITEMID, HALFMOON_ITEMID, RUNWIND_ITEMID, HITTIGER_ITEMID, ARCHLORD_ITEMID))) {
  					$zswq[] = $generalArray [$i] ['intID'];
  				}
  			}
  			if ($generalArray [$i] ['carapace'] > 0) {
  				$xjInfo = $player->GetItem($generalArray [$i] ['carapace']);
  				if ($xjInfo['QhLevel'] == 24) {
  					$gtzs[] = $generalArray [$i] ['intID'];
  				}
  			}	        
  			//$zbInfo = $player->GetItem($zbxx [1]);
	        //$zbProto = toolsModel::getItemInfo($zbInfo['ItemID']);;
  			$pysx = null;    			 			 			  			  			 
			$g_dj = $generalArray [$i] ['general_level']; //将领等级
			$g_zy = $generalArray [$i] ['professional']; //职业		
			$sxxs = genModel::sxxs ( $g_zy );
			$data ['gid'] = intval ( $generalArray [$i] ['intID'] ); //主键ID
			if ($data ['gid'] == $scjl) {
				$data ['scg'] = 1; //是否守城官
			} else {
				$data ['scg'] = 0;
			}
			$data ['px'] = intval ( $generalArray [$i] ['general_sort'] ); //将领排序
			$data ['tx'] = $generalArray [$i] ['avatar']; //将领头标
			$data ['xm'] = $generalArray [$i] ['general_name']; //将领姓名
			if ($data ['xm'] == $city_lang['generateHireGeneral_1']) {
				$data ['xm'] = $city_lang['generateHireGeneral_2'];
			}
			$data ['jb'] = intval ( $generalArray [$i] ['general_level'] ); //将领级别
			$data ['zy'] = intval ( $generalArray [$i] ['professional'] ); //将领职业
			$data ['wjxb'] = intval ( $generalArray [$i] ['general_sex'] ); //将领性别
			$data ['tf'] = round ( $generalArray [$i] ['understanding_value'], 2 ); //天赋
			$data ['jj'] = intval ( $generalArray [$i] ['professional_level'] ); //军阶
			
			$data['zysx'] = implode(',',$sxxs).',80';
			//武将各项当前属性=initvalue[leadertype,atrname]*(1+gift*(lv-1)/100)*(1+$forcelv*0.25)		
			$data ['gj'] = genModel::hqwjsx ( $g_dj, $data ['tf'], $data ['jj'], $generalArray [$i] ['llcs'], $jwjc, $zbgjjc, $sxxs ['gj'], $generalArray [$i] ['py_gj'] );
			$data ['fy'] = genModel::hqwjsx ( $g_dj, $data ['tf'], $data ['jj'], $generalArray [$i] ['llcs'], $jwjc, $zbfyjc, $sxxs ['fy'], $generalArray [$i] ['py_fy'] );
			$data ['tl'] = genModel::hqwjsx ( $g_dj, $data ['tf'], $data ['jj'], $generalArray [$i] ['llcs'], $jwjc, $zbtljc, $sxxs ['tl'], $generalArray [$i] ['py_tl'] );
			$data ['mj'] = genModel::hqwjsx ( $g_dj, $data ['tf'], $data ['jj'], $generalArray [$i] ['llcs'], $jwjc, $zbmjjc, $sxxs ['mj'], $generalArray [$i] ['py_mj'] );
			$data ['jy'] = intval ( $generalArray [$i] ['current_experience'] ); //当前经验
			$data ['xjjy'] = cityModel::getGeneralUpgradeExp ( $generalArray [$i] ['general_level'] ); //下级所需经验
			
			$data['gjp'] = intval($generalArray [$i] ['py_gj']);
			$data['fyp'] = intval($generalArray [$i] ['py_fy']);
			$data['tlp'] = intval($generalArray [$i] ['py_tl']);
			$data['mjp'] = intval($generalArray [$i] ['py_mj']);
	    	$pysxdata = array(zcpysx($zc),wjdjpysx($generalArray[$i]['general_level']));
	    	$kpysx = min($pysxdata);  //可培养单项属性的上限			
			$data['pysx'] = $kpysx;			
			$pysxdata = $kpysx = NULL;
			$data ['smsx'] = $data ['tl'] * 10; //生命上限
			//echo $forcelv.'<br>';
			$data ['smz'] = round ( $generalArray [$i] ['general_life'], 0 ); //生命值
			if ($data ['smz'] > $data ['smsx']) {
				$data ['smz'] = $data ['smsx'];
			}
			$data ['sfll'] = intval ( $generalArray [$i] ['llzt'] ); //是否正在历练
			if ($generalArray [$i] ['xl_end_time'] != 0) {
				$data ['sfzxl'] = 1;
			} else {
				$data ['sfzxl'] = 0;
			}
      
	        for($si=1,$l=count($zbslots)+1; $si<$l; ++$si) {
		        $slot = $zbslots[$si-1];
		        if ($generalArray [$i] [$slot] != 0) {
		          $zbInfo =  $player->GetItem($generalArray [$i] [$slot] );
				  $zbArray = array();
				  toolsModel::PackItem2Array($playerid, $zbInfo, $zbArray, true, true);//$common->insertLog(json_encode($zbArray));
		          $data ['zb'.$si] = $zbArray[$zbInfo['ID']];//cityModel::GetZBData($zbInfo);
		        } else {
		          $data ['zb'.$si] = 0;
		        }
	        }
/*hk*/		if ($generalArray [$i] ['gohomeTime'] > $nowTime && ($generalArray [$i] ['act'] == 4 || $generalArray [$i] ['act'] == 7)) {//修改武将状态
				$data ['czzt'] = 7;
			} elseif ($generalArray [$i] ['gohomeTime'] > $nowTime && $generalArray [$i] ['act'] == 3) {
				$data ['czzt'] = 6;
			} elseif ($generalArray [$i] ['gohomeTime'] > $nowTime && $generalArray [$i] ['act'] == 2) {
				$data ['czzt'] = 5;
			} elseif ($generalArray [$i] ['act'] == 1 || $generalArray [$i] ['act'] == 6) {
				$data ['czzt'] = 4;
			} elseif ($generalArray [$i] ['occupied_playersid'] == $playerid && $roleInfo['aggressor_general'] != $generalArray [$i] ['intID']) {
				$data ['czzt'] = intval ( $generalArray [$i] ['f_status'] );
			} elseif ($generalArray [$i] ['occupied_end_time'] > $nowTime || $generalArray [$i] ['occupied_playersid'] == $playerid) {
				$data ['czzt'] = 3;
			} else {
				$data ['czzt'] = intval ( $generalArray [$i] ['f_status'] );
			}
			$data ['czzt2'] = intval ( $generalArray [$i] ['f_status'] );
			//$data ['czzt'] = intval ( $generalArray [$i] ['f_status'] ); //武将的战斗状态/*hk*/
			//$temp_dir = explode("|",socialModel::getTianfu($data['zj']));
			if (! empty ( $generalArray [$i] ['understanding_value'] )) {
				$data ['llyl'] = intval ( llyl ( $generalArray [$i] ['llcs'] + 1 ) ); //历练银两(待定)
			}
			$data ['llcs'] = intval ( $generalArray [$i] ['llcs'] ); //练练次数
			//$data['lshf'] =  addLifeCost($data['nl']);
			$data ['lshf'] = addLifeCost ($roleInfo['player_level']); //疗伤单价
			$data ['tuid'] = intval ( $generalArray [$i] ['occupied_playersid'] ); //占领玩家ID
			if (($data ['tuid'] != 0 && $data ['tuid'] != $playerid && $generalArray [$i] ['occupied_end_time'] < $nowTime) || ($data ['tuid'] == $playerid && $roleInfo['aggressor_general'] != $generalArray [$i] ['intID'])) {
				$data ['tuid'] = 0;
			}
			$issj = 0;
			if ($roleInfo ['zf_aggressor_general'] == $generalArray [$i] ['intID'] && $roleInfo ['aggressor_general'] != $generalArray [$i] ['intID']) {
				$data ['tuid'] = $playerid;
				$issj = 1;
			}
			$jn1 = $generalArray [$i] ['jn1'];
			if ($jn1 != 0) {
				$jnInfo = jnk ( $jn1 );
				$data ['jniid1'] = $jnInfo ['iconid'];
				$data ['jndj1'] = intval ( $generalArray [$i] ['jn1_level'] );
				$data ['jnid1'] = intval ( $jn1 );
				$data ['jnmc1'] = $jnInfo ['n'];
			}
			$jn2 = $generalArray [$i] ['jn2'];
			if ($jn2 != 0) {
				$jnInfo2 = jnk ( $jn2 );
				$data ['jniid2'] = $jnInfo2 ['iconid'];
				$data ['jndj2'] = intval ( $generalArray [$i] ['jn2_level'] );
				$data ['jnid2'] = intval ( $jn2 );
				$data ['jnmc2'] = $jnInfo2 ['n'];
			}
			$array [] = $data;
			unset ( $data );
			unset ( $sxxs );
		}
		$_SESSION['jj'] = count($jjlist);
		$_SESSION['lles'] = count($lles);
		$_SESSION['llss'] = count($llss);
		$_SESSION['llmax'] = count($llmax);	
		$_SESSION['py_200'] = count($py_200);
		$_SESSION['py_400'] = count($py_400);	
		$_SESSION['py_600'] = count($py_600);
		$_SESSION['py_800'] = count($py_800);
		$_SESSION['py_1000'] = count($py_1000);
		$_SESSION['py_1200'] = count($py_1200);
		$_SESSION['py_1400'] = count($py_1400);
		$_SESSION['py_1600'] = count($py_1600);
		$_SESSION['py_1800'] = count($py_1800);
		$_SESSION['qbjn'] = count($jnlist);
		$_SESSION['zswq'] = count($zswq);
		$_SESSION['gtzs'] = count($gtzs);
		$_SESSION['jn_3'] = count($jn_3);
		$_SESSION['jn_5'] = count($jn_5);
		$_SESSION['jn_7'] = count($jn_7);
		$hiregeneral = cityModel::getHireGeneralInfo ( $playerid );
		//print_r($hiregeneral);
		if (! empty ( $array )) {
			if ($showstate == 1) {
				$value ['status'] = 0;
			}
			$value ['generals'] = $array;
			$value ['hirableGeneral'] = $hiregeneral ['hirableGeneral'];
			$value ['nextUpdate'] = $hiregeneral ['nextUpdate'];
			$value ['tjbhsj'] = $hiregeneral ['tjbhsj'];
			$value ['ldsl'] = count($zllist);
		} else {
			if ($showstate == 1) {
				$value ['status'] = 1021;
				$value ['message'] = $sys_lang[3];
			}
		}
		return $value;
	}
	
	//判断武将是否能历练
	public static function is_Practice($playerid) {
		global $common, $db, $mc;
		$memory_Resource = $mc->get ( MC . $playerid );
		if ($memory_Resource != "") {
			$prestige = $memory_Resource ['prestige'];
			$temp_dir = explode ( "|", socialModel::getDiwei ( $prestige ) ); //3
			$sx = $temp_dir [3];
			
			$result = $db->query ( "SELECT * FROM " . $common->tname ( 'playergeneral' ) . " WHERE playerid = '" . $playerid . "'" );
			while ( $rows = $db->fetch_array ( $result ) ) {
				if ($rows ['understanding_value'] < $sx) {
					return 1;
				}
			}
			return 0;
		}
	}
	
	//招募将领
	public static function hireGeneral($getInfo, $ngid = false) {
		global $common, $db, $mc, $_SGLOBAL, $sys_lang, $city_lang;
		$nowTime = $_SGLOBAL ['timestamp'];
		$playerid = $getInfo ['playersid']; //玩家ID
		$roleInfo ['playersid'] = $playerid;
		roleModel::getRoleInfo ( $roleInfo );
		$tjjwdj = 0;
		$jwdj = $tjjwdj = $roleInfo ['mg_level'];
		$jwInfo = jwmc ( $jwdj );
		$jwjc = 1 + $jwInfo ['jc'] / 100;
		if (! empty ( $getInfo ['tuid'] )) {
			$listid = $getInfo ['gid']; //可招募将领ID
			$playerid = $getInfo ['playersid']; //玩家ID
			$gid = $getInfo ['tuid'];
			if (! ($heRoleInfo = $mc->get ( MC . $getInfo ['tuid'] ))) {
				$heRoleInfo = roleModel::getTableRoleInfo ( $getInfo ['tuid'], '', false );
			}
			if (empty ( $heRoleInfo )) {
				return array ('status' => 3, 'message' => $sys_lang[1] );
			}
			if ($heRoleInfo ['player_level'] < 10 || $roleInfo ['player_level'] < 10) {
				$value ['status'] = 21;
				$value ['message'] = $city_lang['hireGeneral_1'];
				return $value;
			}
			/*$updiff = $roleInfo ['player_level'] + 10;
			$downdiff = ($roleInfo ['player_level'] - 10) < 0 ? 0 : $roleInfo ['player_level'] - 10;
			if ($heRoleInfo ['player_level'] < $downdiff && $heRoleInfo ['player_level'] > $updiff) {
				$value ['status'] = 21;
				$value ['message'] = '级别差大于10级，不得偷取';
				return $value;
			}*/
			//$tjjwdj = $heRoleInfo['mg_level'];
		} else {
			$listid = _get ( 'generalId' ); //可招募将领ID			
			$gid = $getInfo ['playersid'];
		}
		//if(!($hireGeneralInfo = $mc->get(MC . $gid . '_HireGeneral'))) {
		$hireGeneralInfo = '';
		$hireGeneralInfoArray = cityModel::getHireGeneralInfo ( $gid, $hireGeneralInfo );
		//$hireGeneralInfo = $hireGeneralInfoArray['hirableGeneral'];
		//}
		if (! empty ( $getInfo ['tuid'] ) && $hireGeneralInfoArray ['tjbhsj'] != 0) {
			$value ['status'] = 1021;
			$value ['message'] = $city_lang['hireGeneral_2'];
			return $value;
		}
		//print_r($hireGeneralInfo);
		if (empty ( $hireGeneralInfo [$listid] )) {
			$value ['status'] = 22;
			$value ['message'] = $city_lang['hireGeneral_3'];
			return $value;
		}
		$canhireinfo = $hireGeneralInfo [$listid];
		$general_sort_fight = array ();
		if (! empty ( $canhireinfo )) {
			//$generalInfo = cityModel::getGeneralInfo($playerid,'general_state,general_sort');
			$generalInfo = cityModel::getGeneralData ( $playerid, '', '*' );
			//print_r($generalInfo);
			//$general_sort = array();
			$max_general_sort = '';
			if (! empty ( $generalInfo )) {
				$general_total_num = count ( $generalInfo ); //将领总计数量
			} else {
				$general_total_num = 0;
			}
			$general_sort_free = array ();
			for($i = 0; $i < $general_total_num; $i ++) {
				if ($generalInfo [$i] ['f_status'] == 1) {
					$general_sort_fight [] = $generalInfo [$i] ['general_sort'];
				} else {
					$general_sort_free [] = $generalInfo [$i] ['general_sort'];
				}
			}
			$wjczsl = wjczsl ( $roleInfo ['player_level'] );
			if (count ( $general_sort_fight ) < $wjczsl) {
				$f_status = 1;
				if (! empty ( $general_sort_fight )) {
					$newID = max ( $general_sort_fight ) + 1;
				} else {
					$newID = 1;
				}
			} else {
				$f_status = 0;
				if (! empty ( $general_sort_free )) {
					$newID = max ( $general_sort_free ) + 1;
				} else {
					$newID = 1;
				}
			}
			
			/*if ($general_total_num < 6) {
			 if (!empty($general_sort)) {
			 $max_general_sort = max($general_sort);
			 }
			 }
			 if (empty($max_general_sort)) {
			 $max_general_sort = 0;
			 }*/
			//echo $generalnum.'<br>'.$max_general_sort;
			$manage_command_ability = $roleInfo ['djt_level']; //统帅能力等级
			$command_upperlimit = guideValue ( $manage_command_ability ); //可招募将领上限       
			$allowhirenum = $command_upperlimit - $general_total_num; //允许招募将领个数  	
			$insert ['general_sort'] = $newID; //将领排序
			$insert ['f_status'] = $f_status; //将领状态
			$insert ['playerid'] = $playerid; //玩家ID
			$insert ['general_name'] = $canhireinfo ['general_name']; //将领姓名
			$insert ['general_sex'] = $canhireinfo ['sex']; //将领性别
			$insert ['avatar'] = $canhireinfo ['avatar']; //将领头像标识
			$insert ['professional'] = $canhireinfo ['professional']; //将领职业
			$insert ['arm'] = $canhireinfo ['professional'];
			$insert ['jn1'] = $canhireinfo ['jn1'];
			$insert ['jn1_level'] = $canhireinfo ['jn1_level'];
			//$insert['attack_value'] = $canhireinfo['attack_value'];                //将领攻击值
			//$insert['defense_value'] = $canhireinfo['defense_value'];              //将领防御值
			//$insert['physical_value'] = $canhireinfo['physical_value'];            //将领体力值
			//$insert['agility_value'] = $canhireinfo['agility_value'];              //将领敏捷值
			$insert ['understanding_value'] = $canhireinfo ['understanding_value']; //将领悟性
			$insert ['mj'] = $canhireinfo ['mj']; //是否名将
			if (isset ( $_SESSION ['debug'] )) {
				if ($_SESSION ['debug']) {
					$insert ['general_level'] = rand(30,70); //将领级别	
				} else {			
					$insert ['general_level'] = '1'; //将领级别					
				}
			} else {
				if (isset($canhireinfo ['general_level'])) {
					$insert ['general_level'] = $canhireinfo ['general_level']; 
				} else {
					$insert ['general_level'] = '1'; 
				}
			}
			$diffLev = $insert ['general_level'] - 8;
			if ($roleInfo['player_level'] < $diffLev) {
				return array ('status' => 22, 'message' => $city_lang['hireGeneral_28'] );
			}			
			//$insert['motorize'] = $canhireinfo['motorize'];                        //将领机动力 
			$insert ['general_life'] = $canhireinfo ['physical_value'] * 10; //将领生命值
			$insert ['professional_level'] = 1; //军阶默认1级
			if ($tjjwdj != 0) {
				if ($tjjwdj < 2 && $insert ['general_name'] == $city_lang['hireGeneral_4']) {
					return array ('status' => 22, 'message' => $city_lang['hireGeneral_5'] );
				} elseif ($tjjwdj < 7 && $insert ['general_name'] == $city_lang['hireGeneral_6']) {
					return array ('status' => 22, 'message' => $city_lang['hireGeneral_7'] );
				} elseif ($tjjwdj < 12 && $insert ['general_name'] == $city_lang['hireGeneral_8']) {
					return array ('status' => 22, 'message' => $city_lang['hireGeneral_9'] );
				} elseif ($tjjwdj < 16 && $insert ['general_name'] == $city_lang['hireGeneral_10']) {
					return array ('status' => 22, 'message' => $city_lang['hireGeneral_11'] );
				}
			}
			//设置默认驻守
			/*if ($general_total_num == 0) {
				$insert ['occupied_playersid'] = $playerid;
				$insert ['occupied_player_level'] = $roleInfo ['player_level'];
				$insert ['occupied_player_nickname'] = $roleInfo ['nickname'];
				$insert ['last_income_time'] = $nowTime;
				$insert ['occupied_end_time'] = $nowTime + 86400;			
		    }*/
			$coins = $roleInfo ['coins'];
			if ($coins < $canhireinfo ['hftq']) {
				$value ['status'] = 1021;
				$value ['message'] = $city_lang['hireGeneral_12'];
			} elseif ($allowhirenum > 0) {
				$value ['status'] = 0;
				$ys = actModel::hqwjpz ( $insert ['understanding_value'] ) + 1;
				if (! empty ( $getInfo ['tuid'] ) && $ys > 2) {
					if ($ys == 4) {
						if ($roleInfo['player_level'] < 20) {
							return array('status'=>22,'message'=>$city_lang['hireGeneral_13']);
						}
					} elseif ($ys == 5) {
						if ($roleInfo['player_level'] < 40) {
							return array('status'=>22,'message'=>$city_lang['hireGeneral_14']);
						}				
					}					
					$zmhfxx = tjhf($ys);
					$hfyp = $zmhfxx['hf'];
					$hsyp = $zmhfxx['hs'];
					$jlyp = $zmhfxx['jl'];
					$yp = $roleInfo['silver'];
					if ($yp < $hfyp) {
						$arr1 = array('{xhyp}','{yp}');
						$arr2 = array($hfyp,$yp);
						return array('status'=>68,'yp'=>floor ( $roleInfo ['silver'] ),'message'=>str_replace($arr1,$arr2,$sys_lang[6]),'xyxhyp' => $hfyp);                                                                                                                                                      
					} else {
						$updateRole ['silver'] = $yp - $hfyp;
						$value['yp'] = $updateRole ['silver'];
						if ($ys > 3) {
							$message = array('playersid'=>$roleInfo['playersid'],'toplayersid'=>$heRoleInfo['playersid'],'subject'=>$city_lang['hireGeneral_15'],'genre'=>20,'tradeid'=>0,'interaction'=>0,'is_passive'=>0,'type'=>1,'request'=>addcslashes(json_encode(array('tq'=>0,'yp'=>$jlyp,'yb'=>0,'jl'=>0,'items'=>array())), '\\'),'message'=>array('xjnr'=>$roleInfo['nickname'].$city_lang['hireGeneral_25'].$insert ['general_name'].$city_lang['hireGeneral_26'].$jlyp.$city_lang['hireGeneral_27']));
							lettersModel::addMessage($message);	
						}
						$value['xhyp'] = $hfyp;							
					}
				}
				$newGid = $common->inserttable ( 'playergeneral', $insert );
				if (empty($newGid)) {
					return array('status'=>28,'message'=>$city_lang['hireGeneral_16']);
				}
				$value['id'] = $newGid;
				$value['xm'] = $insert ['general_name'];
				$value['tf'] = $insert ['understanding_value'];
				unset ( $hireGeneralInfo [$listid] );
				//设置默认驻守
				/*if ($general_total_num == 0) {
					$updateRole ['is_defend'] = 1;
					$updateRole ['end_defend_time'] = $nowTime;
					$updateRole ['aggressor_general'] = $newGid;
					$updateRole ['zf_aggressor_general'] = $newGid;	
					$updateRole ['aggressor_level'] = $roleInfo ['player_level'];
					$updateRole ['aggressor_playersid'] = $playerid;
					$updateRole ['aggressor_nickname'] = $roleInfo ['nickname'];							
				}*/				
				//恭喜，玩家[某某某]成功的招募了紫（橙）色武将XX	
				if ($ys == 4 || $ys == 5) {
					if ($ys == 4) {
						$ysmc = $city_lang['hireGeneral_17'];
						$color = '#FF65F8';
					} else {
						$ysmc = $city_lang['hireGeneral_18'];
						$color = '#FF8929';
					}
					lettersModel::setPublicNotice ( $city_lang['hireGeneral_19'].'<FONT COLOR="#AB7AFE">[' . $roleInfo ['nickname'] . ']</FONT>'.$city_lang['hireGeneral_20'] . $ysmc . $city_lang['hireGeneral_21'].'<FONT COLOR="' . $color . '">[' . $insert ['general_name'] . ']</FONT>' );
				}
				//print_r($hireGeneralInfo);
				$mc->set ( MC . $gid . '_HireGeneral', $hireGeneralInfo, 0, 3600 );
				$updateGinfo ['ginfo'] = serialize ( $hireGeneralInfo );
				$updateGinfoWhere ['playersid'] = $gid;
				$common->updatetable ( 'save_ginfo', $updateGinfo, $updateGinfoWhere );
				
				if ($ngid == false) {
					$ngid = '*';
				} else {
					$ngid = $newGid;
				}
				$generalInfo = cityModel::getGeneralList ( $getInfo, 0, true, $ngid );
				$arrayginfo = array();
				foreach ($generalInfo ['generals'] as $generalInfoVale) {
					if ($generalInfoVale['gid'] == $newGid) {
						$arrayginfo[] = $generalInfoVale;
					} else {
						$arrayginfo[] = array('gid'=>$generalInfoVale['gid'],'px'=>$generalInfoVale['px']);
					}
				}
				//$value ['generals'] = $generalInfo ['generals'];
				$value ['generals'] = $arrayginfo;
				$array = array ();
				if (! empty ( $getInfo ['tuid'] )) {
					foreach ( $hireGeneralInfo as $i => $garray ) {
						$array [] = array ('gid' => $i, 'xm' => $hireGeneralInfo [$i] ['general_name'], 'tx' => $hireGeneralInfo [$i] ['avatar'], 'zy' => $hireGeneralInfo [$i] ['professional'], 'gj' => $hireGeneralInfo [$i] ['attack_value'], 'fy' => $hireGeneralInfo [$i] ['defense_value'], 'tl' => $hireGeneralInfo [$i] ['physical_value'], 'mj' => $hireGeneralInfo [$i] ['agility_value'], 'tf' => round ( $hireGeneralInfo [$i] ['understanding_value'], 2 ), 'hf' => $hireGeneralInfo [$i] ['hftq'] );
					}
					//$value ['hirableGeneral'] = $array;
					$value ['nextUpdate'] = $hireGeneralInfoArray ['nextUpdate'];
					$value ['tjbhsj'] = $hireGeneralInfoArray ['tjbhsj'];
				} else {
					//$value ['hirableGeneral'] = $generalInfo ['hirableGeneral'];
					$value ['nextUpdate'] = $generalInfo ['nextUpdate'];
					$value ['tjbhsj'] = $generalInfo ['tjbhsj'];
				}
				
				$updateRole ['coins'] = $coins - $canhireinfo ['hftq'];
				$updateRoleWhere ['playersid'] = $playerid;				
				$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
				$common->updateMemCache ( MC . $playerid, $updateRole );
				$value ['tq'] = $updateRole ['coins'];
				//完成引导脚本
				/*$xyd = guideScript::wcjb ( $roleInfo, 'zmwj', $roleInfo ['player_level'], $roleInfo ['player_level'] );
				//接收新的引导
				$xyd = guideScript::xsjb ( $roleInfo, 'wjxl' );
				if ($xyd !== false) {
					$value ['ydts'] = $xyd;
				}*/
				/*完成任务*/
				$roleInfo['wjys'] = $ys;
				$roleInfo['rwsl'] = count($generalInfo ['generals']);
				$rwid = questsController::OnFinish ( $roleInfo, "'zmwj','zmgjwj'" );
				if (! empty ( $rwid )) {
					$value ['rwid'] =  $rwid;
				}				
				$value ['xhtq'] = $canhireinfo ['hftq'];
				//$value ['newgid'] = $newGid;
				/*end完成任务*/
				if ($roleInfo ['player_level'] == 4) {
					$_SESSION ['wjsl'] = $general_total_num + 1;
				}
			} else {
				$value ['status'] = 28; //由于招募将领大到上限导致招募失败
				//$value['upperlimit'] = $command_upperlimit;                         //招募上限值
				$value ['message'] = $city_lang['hireGeneral_22'] . $command_upperlimit . $city_lang['hireGeneral_23'];
			}
		} else {
			$value ['status'] = 4; //由于找不到可招募将领数据导致招募失败,可能是数据不存在，或者该将领已经被招募
			$value ['message'] = $city_lang['hireGeneral_24'];
		}
		return $value;
	}
	
	//获取可招募将领数据
	public static function getHireGeneralInfo($playerid, &$rows = '', $gx = '', $sid = '') {
		global $mc, $common, $_SGLOBAL, $db, $G_PlayerMgr, $city_lang, $sys_lang;
		//$HireGeneralInfo = $mc->get ( MC . $playerid . '_HireGeneral' );
		$nowTime = isset($_SGLOBAL ['timestamp'])?$_SGLOBAL ['timestamp']:time();
		/*if (!$HireGeneralInfo = $mc->get ( MC . $playerid . '_HireGeneral' )) {
			$sqlGinfo = "SELECT ginfo from ".$common->tname('save_ginfo')." WHERE playersid = $playerid && ctime > $nowTime LIMIT 1";
			$grows = $db->fetch_array($db->query($sqlGinfo));
			if (!empty($grows)) {
				$HireGeneralInfo = unserialize($grows['ginfo']);
			} else {
				$HireGeneralInfo = array();
			}
		}	*/
		//$roleInfo ['playersid'] = $playerid;
		//roleModel::getRoleInfo ( $roleInfo );
		/*if (! ($roleInfo = $mc->get ( MC . $playerid ))) {
			$roleInfo = roleModel::getTableRoleInfo ( $playerid, '', false );
		}*/
		$player = $G_PlayerMgr->GetPlayer($playerid);
		if (empty($player)) {
			$valueErr ['status'] = 1;
			$valueErr ['message'] = $sys_lang[1];
			return $valueErr;			
		}
		if (! empty ( $_SESSION ['wjsl'] ) && $player->baseinfo_['player_level'] == 4 && empty ( $_SESSION ['update'] )) {
			if ($_SESSION ['wjsl'] == 1) {
				//$itemInfo = toolsModel::addPlayersItem ( $roleInfo, 10058, 1 );
				$itemInfo = $player->AddItems(array(10058=>1));				
				if (! empty ( $itemInfo [0] )) {
					$getInfo ['playersid'] = $playerid;
					cityModel::quickUpdateHireGeneral ( $getInfo, 3, $itemInfo [0] );
					/*if (! ($roleInfo = $mc->get ( MC . $playerid ))) {
						$roleInfo = roleModel::getTableRoleInfo ( $playerid, '', false );
					}*/
					$_SESSION ['update'] = 1;
				}
			}
		}
		if (! empty ( $gx )) {
			if ($player->baseinfo_ ['aggressor_playersid'] != $player->baseinfo_ ['playersid'] && $player->baseinfo_ ['aggressor_playersid'] != $sid && $player->baseinfo_ ['aggressor_playersid'] != 0 && $player->baseinfo_ ['end_defend_time'] > $nowTime && $gx === 1) {
				$valueErr ['status'] = 1002;
				$valueErr ['message'] = $city_lang['getHireGeneralInfo_1'];
				return $valueErr;
			}
			if ($player->baseinfo_ ['aggressor_playersid'] != $sid && $player->baseinfo_ ['aggressor_playersid'] != 0 && $player->baseinfo_ ['end_defend_time'] > $nowTime) {
				$valueErr ['status'] = 1001;
				$valueErr ['message'] = $city_lang['getHireGeneralInfo_2'];
				return $valueErr;
			}
		}
		$last_updatetime = $player->baseinfo_ ['last_update_general']; //上次更新将领的时间		
		$diffTime = 21600;
		$timeDiff = $nowTime - $last_updatetime; //计算与上次将领更新时差
		$updateRole = array();
    	/*$xwzt_13 = substr($player->baseinfo_['xwzt'],12,1);  //完成招募武将行为
		if ($xwzt_13 == 0 && $_SESSION['playersid'] == $playerid) {
			$updateRole['xwzt'] = substr_replace($player->baseinfo_['xwzt'],'1',12,1);
			//$value['xwzt'] = $updateRole['xwzt'];
		}*/ 	
		
		//当时间超过了一小时,计算剩余时间和上次更新时间
		if ($timeDiff >= $diffTime/* || (empty ( $HireGeneralInfo ) && $timeDiff >= $diffTime)*/) { // && $timeDiff >= 21600
			$usedtime = $timeDiff % $diffTime; //余数即为用去的时间        
			$timeleft = $diffTime - $usedtime;
			//$updateTime['last_updatetime'] = $nowTime - $usedtime;   //计算上次更新数据时间	
			//更新招募将领表更新时间
			$updateRole ['last_update_general'] = $nowTime - $usedtime;
			$jwdj = $player->baseinfo_ ['mg_level']; //爵位等级
			genModel::insertHireGeneralData ( $playerid, 0, '', $player->baseinfo_ ['jg_level'], $updateRole ['last_update_general'], $jwdj, 0, 0, 0, 0 ); //重新生成可招募将领	 
			//$mc->set ( MC . $playerid . '_newGeneral', 21600 );
			if ($nowTime - $updateRole ['last_update_general'] >= 1800) {
				$general ['tjbhsj'] = 0;
			} else {
				$general ['tjbhsj'] = 1800 - ($nowTime - $updateRole ['last_update_general']);
			}
			//$general['tjbhsj'] = 1800;
		} else {
			$timeleft = $diffTime - $timeDiff;
			if ($nowTime - $last_updatetime >= 1800) {
				$general ['tjbhsj'] = 0;
			} else {
				$general ['tjbhsj'] = 1800 - ($nowTime - $last_updatetime);
			}
			//$mc->set ( MC . $playerid . '_newGeneral', 2, 0, 21600 );
		}
		if (! $rows = $mc->get ( MC . $playerid . '_HireGeneral' )) {
			$sqlGinfo = "SELECT ginfo from " . $common->tname ( 'save_ginfo' ) . " WHERE playersid = $playerid && ctime > $nowTime LIMIT 1";
			$grows = $db->fetch_array ( $db->query ( $sqlGinfo ) );
			if (! empty ( $grows )) {
				$rows = unserialize ( $grows ['ginfo'] );
			} else {
				$rows = array ();
			}
		}
		//for ($i = 0; $i < count($rows); $i++) {
		$array = array ();
		if (! empty ( $rows )) {
			foreach ( $rows as $i => $value ) {
				//print_r($value);
				$array [] = array ('gid' => $i, 'xm' => $rows [$i] ['general_name'], 'tx' => $rows [$i] ['avatar'], 'zy' => $rows [$i] ['professional'], 'gj' => $rows [$i] ['attack_value'], 'fy' => $rows [$i] ['defense_value'], 'tl' => round ( $rows [$i] ['physical_value'], 0 ), 'mj' => $rows [$i] ['agility_value'], 'tf' => round ( $rows [$i] ['understanding_value'], 2 ), 'hf' => $rows [$i] ['hftq'] );
				//$array[] = array('id'=>$i,'xm'=>$value['general_name'],'tx'=>$value['avatar'],'zy'=>$value['professional'],'gj'=>$value['attack_value'],'fy'=>$value['defense_value'],'tl'=>$value['physical_value'],'mj'=>$value['agility_value'],'zj'=>round($value['understanding_value'],2));
			}
		}
		//print_r($rows);
		$general ['hirableGeneral'] = $array;
		$general ['nextUpdate'] = $timeleft;
		$general ['frd'] = intval($player->baseinfo_['frd']);
		if (!empty($updateRole)) {
			$updateRoleWhere ['playersid'] = $playerid;
			$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
			$common->updateMemCache ( MC . $playerid, $updateRole );			
		}
		if (! empty ( $general )) {
			$general ['status'] = 0;
			return $general;
		} else {
			return false;
		}
	}
	
	//快速更新可招募将领
	public static function quickUpdateHireGeneral($getInfo, $updateType, $djid) {
		global $common, $db, $mc, $_SGLOBAL, $G_PlayerMgr, $city_lang, $sys_lang;
		$roleInfo ['playersid'] = $getInfo ['playersid'];
		$playerid = $getInfo ['playersid'];
		
		$player = $G_PlayerMgr->GetPlayer($playerid );
		if(!$player)	return array('status'=>21, 'message'=>$sys_lang[7]);
		
		$HireGeneralInfo = $mc->get ( MC . $playerid . '_HireGeneral' );
		roleModel::getRoleInfo ( $roleInfo );
		$jwdj = $roleInfo ['mg_level']; //爵位等级
		$ingot = $roleInfo ['ingot']; //玩家拥有的元宝量
		$silver = $roleInfo ['silver']; //玩家拥有的银两数量
		$lssxcs = $roleInfo['lssxcs'];
		$zssxcs = $roleInfo['zssxcs'];
		$cssxcs = $roleInfo['cssxcs'];
		$player_level = $roleInfo['player_level'];
		$updateTime = 21600;
		$frd = $roleInfo['frd'];
		if ($player_level < 35 && $frd > 1000) {
			if ($frd > 5000) {
				$frd = 5000;
			}			
			if ($player_level < 20) {
				$frd = 1000;
			}						
		}
		if ($updateType == 2) {
			if ($ingot >= 10) {
				$last_updatetime = $roleInfo ['last_update_general']; //上次更新将领的时间
				$nowTime = $_SGLOBAL ['timestamp'];
				$ybys = 0;
				genModel::insertHireGeneralData ( $roleInfo ['playersid'], $updateType, '', $roleInfo ['jg_level'], '', $jwdj, $frd, $lssxcs, $zssxcs, $cssxcs, $ybys ); //生成可招募将领
				$updateRole ['ingot'] = $roleInfo ['ingot'] - 10;
				//$updateRole ['last_update_general'] = $nowTime;
				//$whereupdatePlayer ['playersid'] = $roleInfo ['playersid'];
				//$common->updatetable ( 'player', $updateRole, $whereupdatePlayer ); //更新玩家表内的元宝数据
				//$common->updateMemCache ( MC . $playerid, $updateRole );
				$value ['status'] = 0; //更新成功
				$value ['yb'] = $updateRole ['ingot'];
				$rows = $mc->get ( MC . $getInfo ['playersid'] . '_HireGeneral' );
				for($i = 0; $i < count ( $rows ); $i ++) {
					$array [] = array ('gid' => $i, 'xm' => $rows [$i] ['general_name'], 'tx' => $rows [$i] ['avatar'], 'zy' => $rows [$i] ['professional'], 'gj' => $rows [$i] ['attack_value'], 'fy' => $rows [$i] ['defense_value'], 'tl' => $rows [$i] ['physical_value'], 'mj' => $rows [$i] ['agility_value'], 'tf' => round ( $rows [$i] ['understanding_value'], 2 ), 'hf' => $rows [$i] ['hftq'] );
				}
				$value ['type'] = $updateType - 1;
				$value ['hirableGeneral'] = $array;
				$value ['nextUpdate'] = $updateTime;
				//
				$last_updatetime = $nowTime; //上次更新将领的时间
				if ($nowTime - $last_updatetime >= 1800) {
					$value ['tjbhsj'] = 0;
				} else {
					$value ['tjbhsj'] = 1800 - ($nowTime - $last_updatetime);
				}
				//
				$value ['xhyb'] = 10;
				$xfrd = $roleInfo['frd'] + 5;
				if ( $xfrd < 10000) {
					$updateRole ['frd'] = $xfrd;
				} else {
					$updateRole ['frd'] = 10000; 
				}
				$roleInfo['hdyp'] = $updateRole ['frd'] - $roleInfo['frd'];
				if ($updateRole ['frd'] > 200 && $updateRole ['frd'] < 1001) {
					$updateRole ['lssxcs'] = $roleInfo['lssxcs'] + 1;
				} elseif ($updateRole ['frd'] > 1000 && $updateRole ['frd'] < 5001) {
					$updateRole ['lssxcs'] = $roleInfo['lssxcs'] + 1;
					$updateRole ['zssxcs'] = $roleInfo['zssxcs'] + 1;
				} elseif ($updateRole ['frd'] > 5000 && $updateRole ['frd'] < 10001) {
					$updateRole ['lssxcs'] = $roleInfo['lssxcs'] + 1;
					$updateRole ['zssxcs'] = $roleInfo['zssxcs'] + 1;					
					$updateRole ['cssxcs'] = $roleInfo['cssxcs'] + 1;
				}				
				if ($ybys != 0 && $ybys != 2) {
					if ($ybys == 3) {
						$updateRole['lssxcs'] = 0;
					} elseif ($ybys == 4) {
						$updateRole['zssxcs'] = 0;
					} else {
						$updateRole['cssxcs'] = 0;
					}
				}				
				$value['frd'] = $cjInfo['frd'] = $roleInfo['frd'] = $updateRole ['frd'];				
				$updateRole ['last_update_general'] = $nowTime;
				$whereupdatePlayer ['playersid'] = $roleInfo ['playersid'];
		    	/*$xwzt_13 = substr($roleInfo['xwzt'],12,1);  //完成 刷将行为
				if ($xwzt_13 == 0) {
					$updateRole['xwzt'] = substr_replace($roleInfo['xwzt'],'1',12,1);
					//$value['xwzt'] = $updateRole['xwzt'];
				} */ 
				achievementsModel::check_achieve($playerid,$cjInfo,array('frd'));
				$rwid = questsController::OnFinish($roleInfo,"'frd','ybsxjg'");  //银票刷酒馆任务
			    if (!empty($rwid)) {
			         $value['rwid'] = $rwid;				             
			    }
				hdProcess::run(array('pay_inviteGen'), $roleInfo, 1);
				$common->updatetable ( 'player', $updateRole, $whereupdatePlayer ); //更新玩家表内的元宝数据
				$common->updateMemCache ( MC . $playerid, $updateRole );			
				//$value['bzxx'] = $generalInfo['bzxx'];
			} else {
				$value ['status'] = 88; //更新失败
				$value ['yb'] = floor ( $roleInfo ['ingot'] );
				$arr1 = array('{xhyb}','{yb}');
				$arr2 = array('10',$value ['yb']);
				$value ['message'] = str_replace($arr1,$arr2,$sys_lang[2]);
				$value ['xyxhyb'] = 5;
			}
		} elseif ($updateType == 1) {
			if ($silver >= 5) {
				$last_updatetime = $roleInfo ['last_update_general']; //上次更新将领的时间
				$nowTime = $_SGLOBAL ['timestamp'];
				genModel::insertHireGeneralData ( $roleInfo ['playersid'], $updateType, '', $roleInfo ['jg_level'], '', $jwdj, $frd, $lssxcs, $zssxcs, $cssxcs ); //生成可招募将领
				$updateRole ['silver'] = $roleInfo ['silver'] - 5;
				//$updateRole ['last_update_general'] = $nowTime;
				//$whereupdatePlayer ['playersid'] = $roleInfo ['playersid'];
				//$common->updatetable ( 'player', $updateRole, $whereupdatePlayer ); //更新玩家表内的元宝数据
				//$common->updateMemCache ( MC . $playerid, $updateRole );
				$value ['status'] = 0; //更新成功
				$value ['yp'] = $updateRole ['silver'];
				//$value['info'] = implode('&',cityModel::getGeneralList($getInfo,0)); //获取可招募将领更新后列表数据
				$rows = $mc->get ( MC . $getInfo ['playersid'] . '_HireGeneral' );
				//print_r($rows);
				for($i = 0; $i < count ( $rows ); $i ++) {
					$array [] = array ('gid' => $i, 'xm' => $rows [$i] ['general_name'], 'tx' => $rows [$i] ['avatar'], 'zy' => $rows [$i] ['professional'], 'gj' => $rows [$i] ['attack_value'], 'fy' => $rows [$i] ['defense_value'], 'tl' => $rows [$i] ['physical_value'], 'mj' => $rows [$i] ['agility_value'], 'tf' => round ( $rows [$i] ['understanding_value'], 2 ), 'hf' => $rows [$i] ['hftq'] );
				}
				$value ['type'] = $updateType - 1;
				$value ['hirableGeneral'] = $array;
				$value ['nextUpdate'] = $updateTime;
				//
				$last_updatetime = $nowTime; //上次更新将领的时间
				if ($nowTime - $last_updatetime >= 1800) {
					$value ['tjbhsj'] = 0;
				} else {
					$value ['tjbhsj'] = 1800 - ($nowTime - $last_updatetime);
				}
				//
				$value ['xhyp'] = 5;
				$xfrd = $roleInfo['frd'] + 1;
				if ( $xfrd < 10000) {
					$updateRole ['frd'] = $xfrd;
				} else {
					$updateRole ['frd'] = 10000; 
				}
				$roleInfo['hdyp'] = $updateRole ['frd'] - $roleInfo['frd'];
				$value['frd'] = $cjInfo['frd'] = $roleInfo['frd'] = $updateRole ['frd'];					
				$updateRole ['last_update_general'] = $nowTime;
				$whereupdatePlayer ['playersid'] = $roleInfo ['playersid'];
		    	/*$xwzt_13 = substr($roleInfo['xwzt'],12,1);  //完成 刷将行为
				if ($xwzt_13 == 0) {
					$updateRole['xwzt'] = substr_replace($roleInfo['xwzt'],'1',12,1);
					//$value['xwzt'] = $updateRole['xwzt'];
				} */
				achievementsModel::check_achieve($playerid,$cjInfo,array('frd'));
				$rwid = questsController::OnFinish($roleInfo,"'ypsjg','frd'");  //银票刷酒馆任务
			    if (!empty($rwid)) {
			         $value['rwid'] = $rwid;				             
			    }
				$common->updatetable ( 'player', $updateRole, $whereupdatePlayer ); //更新玩家表内的元宝数据
				$common->updateMemCache ( MC . $playerid, $updateRole );			
			} else {
				$value ['status'] = 68; //更新失败
				$value ['yp'] = floor ( $roleInfo ['silver'] );
				$arr_1 = array('{xhyp}','{yp}');
				$arr_2 = array('5',$value ['yp']);
				$value ['message'] = str_replace($arr_1,$arr_2,$sys_lang[6]);
				$value ['xyxhyp'] = 5;
			}
		} else {
			//检查武将卡是否存在
			//$myItem = toolsModel::getMyItemInfo ( $playerid, $djid );
			$myItem = &$player->GetItems();
			$itemproto = ConfigLoader::GetItemProto($myItem[$djid]['ItemID']);
			if (empty ( $myItem )) {
				$value ['status'] = 21;
				$value ['message'] = $city_lang['quickUpdateHireGeneral_1'];
			} elseif (!in_array($itemproto ['ItemID'],array(10011,10012,10013,10037,10038,10039,10053,10054,10055,10056,10057,10058,18571,18572,20041,18606,18654,18670,18671,18672))) {
				$value ['status'] = 21;
				$value ['message'] = $city_lang['quickUpdateHireGeneral_2'];
			} elseif ($myItem[$djid]['EquipCount'] <= 0) {
				$value ['status'] = 21;
				$value ['message'] = $city_lang['quickUpdateHireGeneral_3'];
			} else {
				/*3初级名将卡：有较大的几率获得绿色名将、很大几率不获得名将；
				 4中级名将卡:有较小几率获得蓝色名将、较大几率获得绿色名将、很大几率不获得名将；
				 5高级名将卡:有较小几率获得紫色名将、稍大几率获得蓝色名将、较大几率获得绿色名将、很大几率不获得名将；
				 6英雄卡:武将卡是针对具体名将，使用该道具可以100%的获得某个具体的名将；
				 7将军令:有100%的几率获得紫色名将(无)
				 8武将卡:武将卡是针对具体名将，使用该道具可以100%的获得某个具体的名将(无)
				 0刷将道具或正常刷新*/
				$value ['status'] = 0;
				if ($itemproto ['Rarity'] == 8 || $itemproto ['Rarity'] == 9) {
					$wjmc = $itemproto ['ItemScript'];
				} else {
					$wjmc = '';
				}
				$last_updatetime = $roleInfo ['last_update_general']; //上次更新将领的时间
				$nowTime = $_SGLOBAL ['timestamp'];
				if ($myItem[$djid]['EquipCount'] > 1) {
					$updateItem ['EquipCount'] = $myItem[$djid]['EquipCount'] - 1;
					$updateItemWhere ['ID'] = $djid;
					$common->updatetable ( 'player_items', $updateItem, $updateItemWhere );
					$myItem[$djid]['EquipCount'] = $updateItem ['EquipCount'];
					$common->updateMemCache ( MC . 'items_' . $playerid, array ($djid => $myItem[$djid] ) );
				} else {
					$delItemWhere ['ID'] = $djid;
					$common->deletetable ( 'player_items', $delItemWhere );
					//$allItem = $mc->get ( MC . 'items_' . $playerid );
					//unset ( $allItem [$djid] );
					unset ( $myItem [$djid] );
					$mc->set ( MC . 'items_' . $playerid, $myItem, 0, 3600 );
				}
				if ($itemproto ['Rarity'] == 3) {
					$addfrd = 5;
				} elseif ($itemproto ['Rarity'] == 4) {
					$addfrd = 10;
				} elseif ($itemproto ['Rarity'] == 5) {
					$addfrd = 15;
				} elseif ($itemproto ['Rarity'] == 8) {
					if ($itemproto ['EquipType'] == 119) {
						$addfrd = 5;
					} elseif ($itemproto ['EquipType'] == 120) {
						$addfrd = 10;
					} elseif ($itemproto ['EquipType'] == 121) {
						$addfrd = 15;
					} else {
						$addfrd = 30;
					}
				} else {
					$addfrd = 30;
				}
				$xfrd = $roleInfo['frd'] + $addfrd;
				if ( $xfrd < 10000) {
					$updateRole ['frd'] = $xfrd;
				} else {
					$updateRole ['frd'] = 10000; 
				}
				$roleInfo['hdyp'] = $updateRole ['frd'] - $roleInfo['frd'];
				$value['frd'] = $cjInfo['frd'] = $updateRole ['frd'];				
				$updateRole ['last_update_general'] = $nowTime;
				$whereupdatePlayer ['playersid'] = $roleInfo ['playersid'];
				achievementsModel::check_achieve($playerid,$cjInfo,array('frd'));
		    	/*$xwzt_13 = substr($roleInfo['xwzt'],12,1);  //完成 刷将行为
				if ($xwzt_13 == 0) {
					$updateRole['xwzt'] = substr_replace($roleInfo['xwzt'],'1',12,1);
					//$value['xwzt'] = $updateRole['xwzt'];
				} */				
				$common->updatetable ( 'player', $updateRole, $whereupdatePlayer ); //更新玩家表内的元宝数据
				$common->updateMemCache ( MC . $playerid, $updateRole );
				genModel::insertHireGeneralData ( $roleInfo ['playersid'], $itemproto ['Rarity'], $wjmc, $roleInfo ['jg_level'], '', $jwdj, $frd, $lssxcs, $zssxcs, $cssxcs ); //生成可招募将领
				//$mc->set ( MC . $playerid . '_newGeneral', 1, 0, 21600 );
				$rows = $mc->get ( MC . $getInfo ['playersid'] . '_HireGeneral' );
				for($i = 0; $i < count ( $rows ); $i ++) {
					$array [] = array ('gid' => $i, 'xm' => $rows [$i] ['general_name'], 'tx' => $rows [$i] ['avatar'], 'zy' => $rows [$i] ['professional'], 'gj' => $rows [$i] ['attack_value'], 'fy' => $rows [$i] ['defense_value'], 'tl' => $rows [$i] ['physical_value'], 'mj' => $rows [$i] ['agility_value'], 'tf' => round ( $rows [$i] ['understanding_value'], 2 ), 'hf' => $rows [$i] ['hftq'] );
				}
				//$itemInfo = toolsModel::getAllItems ( $playerid );
				//$value ['list'] = $itemInfo ['list'];
				//$simBg = $playerBag = null;  // 背包列表返回协议优化
				//$bagData = toolsModel::getBglist($simBg, $roleInfo ['playersid'], $playerBag);  // 背包列表返回协议优化
				$bagData = $player->GetClientBag();
				$value ['list'] = $bagData;
				$value ['type'] = $updateType - 1;
				$value ['djid'] = $djid;
				$value ['hirableGeneral'] = $array;
				$value ['nextUpdate'] = $updateTime;
				
				$last_updatetime = $nowTime; //上次更新将领的时间
				if ($nowTime - $last_updatetime >= 1800) {
					$value ['tjbhsj'] = 0;
				} else {
					$value ['tjbhsj'] = 1800 - ($nowTime - $last_updatetime);
				}
				$roleInfo['frd'] = $updateRole ['frd'];
				$rwid = questsController::OnFinish ( $roleInfo, "'wjksj','frd'" );
				if (! empty ( $rwid )) {
					$value ['rwid'] =  $rwid;
				}
			}
		}
		return $value;
	}
	
	//设置将领排序编号
	public static function setGeneralSort($getInfo) {
		global $common, $db;
		$playerid = $getInfo ['playersid']; //玩家ID
		//$groupid = _get('state');         //将领所属将领组
		$needYb = 20; //花费元宝
		$roleInfo ['playersid'] = $playerid;
		roleModel::getRoleInfo ( $roleInfo );
		$generalId = _get ( 'generalId' );
		$intidarr = explode ( ',', $generalId ); //分割将领编号列  	 
		for($i = 0; $i < count ( $intidarr ); $i ++) {
			$intID = $intidarr [$i];
			//$sortid = $sortidarr[$i];
			$sortid = $i + 1;
			$update = "general_sort = '$sortid'";
			$where = "intID = '$intID' && playerid = '$playerid'";
			$common->updatetable ( 'playergeneral', $update, $where );
		}
		$value ['status'] = 0;
		//$value['info'] = implode('&',cityModel::getGeneralList($getInfo,0));
		$generalInfo = cityModel::getGeneralList ( $getInfo, 0, true );
		//$value ['generals'] = $generalInfo ['generals'];
		foreach ($generalInfo['generals'] as $generalInfoValue) {
			$array[] = array('gid' => $generalInfoValue['gid'],'px'=>$generalInfoValue['px']);
		}
		$value ['generals'] = $array;	
		//$value ['hirableGeneral'] = $generalInfo ['hirableGeneral'];
		$value ['nextUpdate'] = $generalInfo ['nextUpdate'];
    	/*$xwzt_14 = substr($roleInfo['xwzt'],13,1);  //完成 刷将行为
		if ($xwzt_14 == 0) {
			$xwzt = substr_replace($roleInfo['xwzt'],'1',13,1);
			//$value['xwzt'] = $xwzt;
			$common->updatetable('player',"xwzt = '$xwzt'","playersid = '$playerid' LIMIT 1");
			$common->updateMemCache(MC.$playerid,array('xwzt'=>$xwzt));			
		} */		
		return $value;
	}
	
	//升级将领所需经验值获取
	public static function getGeneralUpgradeExp($level) {
		$explvup = cityModel::getPlayerUpgradeExp ( $level );
		return ceil ( $explvup * 0.5 );
	}
	
	//升级玩家所需经验值获取
	public static function getPlayerUpgradeExp($level) {
		if ($level > 70) {
			$level = 70; //暂时设定升级上限为60级
		}
		//CEILING(POWER(角色等级,2.68)*2.58+角色等级*58.58+1088.,1)
		//return ceil ( pow ( $level, 2.6 ) * 16.88 + $level * 18.88 + 8 ); // old
		return ceil ( pow ( $level, 2.68 ) * 2.58 + $level * 58.58 + 1088 ); // new	
	}
	
	//货币兑换
	public static function exchange($playersid, $rId, $rAm, $tId) {
		global $common, $db, $mc, $_SGLOBAL, $city_lang, $sys_lang;
		$nowTime = $_SGLOBAL ['timestamp'];
		$UCU = 0; //U币数量待定
		//$tId 1：元宝2：银两3：军粮   (可兑换)  $rId 1：U点 2：元宝 3：铜钱+军粮(兑换源) 4:银两	
		$rAm = intval ( $rAm );
		if ($rAm < 0 || $rAm > 100) {
			$value ['status'] = 3;
			$value ['message'] = $sys_lang[7];
			return $value;
		}
		if (($tId != 1 && $tId != 2 && $tId != 3) || ($rId != 1 && $rId != 2 && $rId != 3 && $rId != 4)) {
			$value ['status'] = 3;
			$value ['message'] = $sys_lang[7];
			//$value ['rsn'] = intval ( _get ( 'ssn' ) ); 
			return $value;
		}
		$roleInfo ['playersid'] = $playersid;
		roleModel::getRoleInfo ( $roleInfo );
		cityModel::resourceGrowth ( $roleInfo );
		$updateRoleWhere ['playersid'] = $playersid;
		if (($roleInfo['silver'] + $rAm) > SILVERUPLIMIT) {
			return array('status'=>22,'message'=>$city_lang['exchange_1']);
		}
		$value ['status'] = 0;
		if ($tId == 2) {
			if ($rId == 3) { //军粮铜钱换银两
				$needJl = ceil ( $rAm * 1 ); //需要军粮量
				$needTq = ceil ( $rAm * 100 ); //需要铜钱量
				if ($roleInfo ['food'] < $needJl) {
					$returnValueErr ['status'] = 888;
					$returnValueErr ['yb'] = floor ( $roleInfo ['ingot'] );
					$returnValueErr ['yp'] = floor ( $roleInfo ['silver'] );
					$returnValueErr ['tq'] = floor ( $roleInfo ['coins'] );
					$returnValueErr ['jl'] = floor ( $roleInfo ['food'] );
					$returnValueErr ['message'] = $city_lang['exchange_2'];
					//$value ['rsn'] = intval ( _get ( 'ssn' ) );
					return $returnValueErr;
				} elseif ($roleInfo ['coins'] < $needTq) {
					$returnValueErr ['status'] = 1045;
					$returnValueErr ['yb'] = floor ( $roleInfo ['ingot'] );
					$returnValueErr ['yp'] = floor ( $roleInfo ['silver'] );
					$returnValueErr ['tq'] = floor ( $roleInfo ['coins'] );
					$returnValueErr ['jl'] = floor ( $roleInfo ['food'] );
					$returnValueErr ['message'] = $city_lang['exchange_3'];
					//$value ['rsn'] = intval ( _get ( 'ssn' ) );
					return $returnValueErr;
				} else {
					$updateRole ['silver'] = $roleInfo ['silver'] + $rAm;
					$updateRole ['coins'] = floor ( $roleInfo ['coins'] - $needTq );
					$updateRole ['food'] = $roleInfo ['food'] - $needJl;
					$updateRole ['last_update_food'] = $nowTime;
					$value ['yp'] = $updateRole ['silver'];
					$value ['tq'] = $updateRole ['coins'];
					$value ['jl'] = floor ( $updateRole ['food'] );
					$questsInfo = $roleInfo;
					$questsInfo ['hdyp'] = $value ['hqyp'] = $rAm;
					$questsInfo['wcsl'] = $rAm;	
					$rwid = questsController::OnFinish ( $questsInfo, "'dhyp'" );//兑换银票任务
					if (! empty ( $rwid )) {
						$value ['rwid'] = $rwid;
					}
					$value ['xhtq'] = $needTq;
				}
			}
		}
		/*$xwzt_16 = substr($roleInfo['xwzt'],15,1); //玩家兑换
		if ($xwzt_16 == 0) {
			$updateRole['xwzt'] = substr_replace($roleInfo['xwzt'],'1',15,1);
		}*/		
		$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
		$common->updateMemCache ( MC . $playersid, $updateRole );			
		return $value;
	}
	
	/* 应该是废弃的方法
	public static function vip($playersid, $days) {
		global $common, $db, $mc;
		$ingot = 0;
		$level = 0;
		$end_time = 0;
		$now_time = time ();
		if ($days == 3) {
			$ingot = 45;
			$level = 1;
		} elseif ($days == 7) {
			$ingot = 70;
			$level = 2;
		} elseif ($days == 15) {
			$ingot = 125;
			$level = 3;
		}
		if ($ingot == 0) {
			return 88;
		}
		$end_time = $now_time + ($days * 86400);
		$memory_Resource = $mc->get ( MC . $playersid );
		if ($memory_Resource != "") {
			if ($memory_Resource ['ingot'] >= $ingot) {
				$updateMem1 = $memory_Resource ['ingot'] - $ingot;
				$updateMem ['ingot'] = $updateMem1;
				$updateMem ['vip'] = $level;
				$updateMem ['vip_end_time'] = $end_time;
				$common->updateMemCache ( MC . $playersid, $updateMem );
				$common->updatetable ( 'player', "ingot = " . $updateMem1 . ",vip = " . $level . ",vip_end_time = " . $end_time, "playersid = " . $playersid );
				return 0;
			} else {
				return 88;
			}
		}
		}*/

	//计算占领收益剩余时间
	public static function occupied_income($gid, $playersid) {
		global $common, $db, $_SGLOBAL;
		$generalInfo = cityModel::getGeneralData ( $playersid, '', $gid, 0 );
		$last_income_time = $generalInfo [0] ['last_income_time'];
		$diffTime = $_SGLOBAL ['timestamp'] - $last_income_time;
		//$incomeTimes = floor ( $diffTime / 7200 );
		if ($diffTime >= 7200) {
			$value ['time'] = 0;
		} else {
			$time = 7200 - $diffTime;
			$value ['time'] = $time > 0 ? $time : 0;
		}
		//$value['income'] = fightModel::zlsy($dj,$dwdj,$time,$type); //占领收益，数值未写入
		return $value;
	}
	
	//获取驻防信息
	public static function getZfInfo($playersid,$roleInfo = array()) {
		global $db, $common, $mc, $_SGLOBAL, $G_PlayerMgr, $sys_lang, $city_lang;
		$nowTime = $_SGLOBAL ['timestamp'];
		if (!empty($playersid)) {
			$player = $G_PlayerMgr->GetPlayer($playersid);
			//$roleInfo ['playersid'] = $playersid;
			//$roleRes = roleModel::getRoleInfo ( $roleInfo, false );		
			if (empty ( $player )) {
				$value = array ('status' => 1003, 'message' => $sys_lang[1] );
				return $value;
			}
			$roleInfo = $player->baseinfo_;
		} else {
			$playersid = $roleInfo['playersid'];
		}		
		if ($roleInfo ['is_defend'] == 1) {
			$value ['status'] = 0;
			/*$diff = $roleInfo ['end_defend_time'] - $nowTime;
			if ($diff < 0) {
				$diff = 0;
			}
			$value ['time'] = $diff;*/
			$value ['gid'] = $roleInfo ['aggressor_general'];
			//$generalInfo = $db->fetch_array ( $db->query ( "SELECT * FROM " . $common->tname ( 'playergeneral' ) . " WHERE intID = '" . $roleInfo ['aggressor_general'] . "' LIMIT 1" ) );
			$generalInfo = cityModel::getGeneralData ( $playersid, 0, $roleInfo ['aggressor_general'] );
			$value ['giid'] = $generalInfo [0] ['avatar']; //占领自己城池的将领ICON id
			$value ['gname'] = $generalInfo [0] ['general_name'];
			$value ['glevel'] = $generalInfo [0] ['general_level'];
			$tf = $generalInfo [0] ['understanding_value'] - $generalInfo [0] ['llcs'];
			$gxyd = actModel::hqwjpz ($tf);
			$value ['gxyd'] = $gxyd + 1;
			$value ['gxj'] = actModel::hqwjxj($tf);
			$value ['un'] = stripcslashes($roleInfo ['aggressor_nickname']);
			$value ['ul'] = $roleInfo ['aggressor_level'];
			$value ['gzy'] = $generalInfo [0] ['professional'];
			$last_income_time = $generalInfo [0] ['last_income_time']; //上次收取收益时间
			$syjg = $nowTime - $last_income_time;
			if ($syjg > 7200) {
				$leftTime = 0;
			} else {
				$leftTime = 7200 - $syjg;
			}
			if ($leftTime < 0) {
				$leftTime = 0;
			}
			$value ['time'] = $leftTime;
			$value ['sy'] = fightModel::zlsy ( $roleInfo ['player_level'], 1, $syjg ); //收益
			//$value['cl'] = ceil($roleInfo['player_level']*0.7+8); //单位时间铜钱增长量
			$value ['zscl'] = $roleInfo ['strategy'];
			$value ['uid'] = $roleInfo ['playersid'];
			$value ['plevel'] = $roleInfo ['player_level'];
			$value ['tqzzl'] = roleModel::tqzzl ( $roleInfo ['player_level'] ); //铜钱增长率	
			$value ['uid'] = $roleInfo ['playersid'];
			$value ['zscl'] = $roleInfo ['strategy'];
		} elseif ($roleInfo ['is_defend'] == 0 && $roleInfo ['aggressor_playersid'] != 0 && $roleInfo ['end_defend_time'] > $nowTime) {
			$value ['status'] = 1001;
			//判断是否能偷矿			
			if (! ($roleInfo_zl = $mc->get ( MC . $roleInfo ['aggressor_playersid'] ))) {
				//print_r($roleInfo);
				//echo("SELECT * FROM " . $common->tname ( 'player' ) . " WHERE playersid = '".$roleInfo ['aggressor_playersid']."' LIMIT 1");
				$roleInfo_zl = $db->fetch_array ( $db->query ( "SELECT * FROM " . $common->tname ( 'player' ) . " WHERE playersid = '" . $roleInfo ['aggressor_playersid'] . "' LIMIT 1" ) );
			}
			$last_twk_time = $roleInfo_zl ['last_twk_time']; //上次偷挖时间
			//$wk_count = $roleInfo_zl['wk_count'];            //挖矿次数
			$wk_count = cityModel::checkMineTimes ( $roleInfo_zl );
			$timeInfo = wksjxz ();
			$diffTime = $timeInfo [2]; //要求的时间间隔
			if (($nowTime - $last_twk_time) > $diffTime && $wk_count < $timeInfo [1]) {
				$value ['tk'] = 1;
			} else {
				$value ['tk'] = 0;
			}
			$value ['uid'] = $roleInfo ['playersid'];
			$value ['gid'] = $roleInfo ['aggressor_general'];
			$generalInfo = $db->fetch_array ( $db->query ( "SELECT * FROM " . $common->tname ( 'playergeneral' ) . " WHERE intID = '" . $roleInfo ['aggressor_general'] . "' LIMIT 1" ) );
			$value ['giid'] = $generalInfo ['avatar']; //占领自己城池的将领ICON id
			$value ['gname'] = $generalInfo ['general_name']; //占领自己城池的将领名称
			$value ['glevel'] = $generalInfo ['general_level'];
			$value ['glevel'] = $generalInfo ['general_level']; //占领自己城池的将领等级
			$tf = $generalInfo ['understanding_value'] - $generalInfo ['llcs'];
			$gxyd = actModel::hqwjpz ($tf);
			$value ['gxj'] = actModel::hqwjxj($tf);
			$value ['gzy'] = $generalInfo['professional'];
			$value ['gxyd'] = $gxyd + 1;
			$zlRoleInfo ['playersid'] = $roleInfo ['aggressor_playersid'];
			$zlres = roleModel::getRoleInfo ( $zlRoleInfo, false );
			if ($zlres == false) {
				return array ('status' => 1003, 'message' => $sys_lang[1] );
			}
			$value ['un'] = stripcslashes($zlRoleInfo ['nickname']);
			$value ['ul'] = $zlRoleInfo ['player_level'];
			$incomeInfo = cityModel::occupied_income ( $roleInfo ['aggressor_general'], $roleInfo ['aggressor_playersid'] );
			$value ['time'] = $incomeInfo ['time'];
			$value ['gid'] = $roleInfo ['aggressor_general'];
			$msgRes = $db->query ( "SELECT `aggressor_message` FROM " . $common->tname ( 'aggressor_message' ) . " WHERE `playersid` = '" . $roleInfo ['playersid'] . "' ORDER BY id DESC LIMIT 1" );
			$msgInfo = $db->fetch_array ( $msgRes );
			if (! empty ( $msgInfo )) {
				if ($msgInfo ['aggressor_message'] == '') {
					$value ['zlxy'] = $city_lang['setZf_2'];
				} else {
					$value ['zlxy'] = $msgInfo ['aggressor_message'];
				}
			} else {
				$value ['zlxy'] = $city_lang['setZf_2'];
			}
			$value ['uid'] = $roleInfo ['aggressor_playersid'];
			$last_income_time = $generalInfo ['last_income_time']; //上次收取收益时间
			$syjg = $nowTime - $last_income_time;
			$value ['sy'] = fightModel::zlsy ( $roleInfo ['player_level'], 1, $syjg );
			//$value['cl'] = ceil($roleInfo_zl['player_level']*0.7+8);	
			$value ['zscl'] = $roleInfo ['strategy'];
			$value ['plevel'] = $roleInfo ['player_level'];
			$value ['tqzzl'] = roleModel::tqzzl ( $roleInfo ['player_level'] ); //铜钱增长率	
		} else {
			$value ['status'] = 1002;
		}
		//$sc_total_cdtime = collectNeedTime();
		//$value ['zscd'] = ($nowTime - $roleInfo ['last_collect_time'] > $sc_total_cdtime) ? 0 : ($nowTime - $roleInfo ['last_collect_time']);
		$wkcs = cityModel::checkMineTimes ( $roleInfo );
		if ($wkcs < 10) {
			$value ['kscd'] = ($nowTime - $roleInfo ['last_wk_time'] > 180) ? 0 : ($nowTime - $roleInfo ['last_wk_time']);
		}
		
		return $value;
	}
	
	//收取驻守或者占领收益
	public static function sqsy($playersid, $gid, $pid) {
		global $common, $db, $mc, $_SGLOBAL, $G_PlayerMgr, $city_lang, $sys_lang;
		$roleInfo ['playersid'] = $playersid;
		roleModel::getRoleInfo ( $roleInfo );
		$nowTime = $_SGLOBAL ['timestamp'];
		$gInfo = cityModel::getGeneralData ( $playersid, '', $gid );
		if (empty ( $gInfo )) {
			$value = array ('status' => 3, 'message' => $sys_lang[3] );
			return $value;
		}
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$sys_lang[7]);
		
		$occupied_playersid = $gInfo [0] ['occupied_playersid']; //占领玩家ID
		$occupied_player_level = $gInfo [0] ['occupied_player_level']; //占领的玩家级别
		$occupied_player_nickname = $gInfo [0] ['occupied_player_nickname']; //占领的玩家名称
		$last_income_time = $gInfo [0] ['last_income_time']; //上次收益时间
		$occGid = $roleInfo ['aggressor_general'];
		$diffTime = $nowTime - $last_income_time;
		$occupied_end_time = $gInfo [0] ['occupied_end_time']; //占领结束时间
		if ($occupied_playersid == 0) {
			$value ['status'] = 1021;
			$value ['message'] = $city_lang['sqsy_1'];
			if (! empty ( $pid )) {
				$value ['pid'] = $pid;
				$showPid = $pid;
			} else {
				$showPid = $playersid;
			}
			$bzlInfo = cityModel::getZfInfo ( $showPid );
			if ($bzlInfo ['status'] != 1002) {
				$value ['uid'] = $bzlInfo ['uid'];
				$value ['gid'] = $bzlInfo ['gid'];
				$value ['un'] = $bzlInfo ['un'];
				$value ['ul'] = $bzlInfo ['ul'];
				$value ['giid'] = $bzlInfo ['giid'];
				if ($bzlInfo ['status'] == 1001 && ! empty ( $bzlInfo ['zlxy'] )) {
					$value ['zlxy'] = $bzlInfo ['zlxy'];
				}
				$value ['glevel'] = $bzlInfo ['glevel'];
				//$hqtq = fightModel::zlsy ( $roleInfo ['player_level'],2,$diffTime); 
				$value ['dzljtq'] = $bzlInfo ['sy'];
				$value ['gname'] = $bzlInfo ['gname'];
				$value ['gxyd'] = $bzlInfo ['gxyd'];
				$value ['time'] = $bzlInfo ['time'];
				$value['gxj'] = $bzlInfo['gxj'];
				$value['gzy'] = $bzlInfo['gzy'];
				$value['zscl'] = $bzlInfo['zscl'];
			}
		} elseif (empty ( $gInfo )) {
			$value ['status'] = 21;
			$value ['message'] = $sys_lang[3];
		} elseif (empty ( $gid )) {
			$value ['status'] = 3;
			$value ['message'] = $sys_lang[7];
		} elseif ($diffTime >= 7200) {
			$value ['status'] = 0;
			$value ['tt'] = $diffTime;
			$value ['gid'] = $gid;
			if ($occupied_playersid == $playersid) {
				//$updateRole ['end_defend_time'] = $nowTime + 7200;
				$hqtq = fightModel::zlsy ( $roleInfo ['player_level'], 1, $diffTime ); //获取铜钱收益
				$updateRole ['coins'] = $roleInfo ['coins'] + $hqtq;
				if ($updateRole ['coins'] > COINSUPLIMIT) {
					$updateRole ['coins'] = COINSUPLIMIT;
					$hqtq = COINSUPLIMIT - $roleInfo ['coins'];
				}
				$updateRoleWhere ['playersid'] = $playersid;
				$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
				$common->updateMemCache ( MC . $playersid, $updateRole );
				$gInfo [0] ['last_income_time'] = $updateGen ['last_income_time'] = $nowTime;
				$updateGenWhere ['intID'] = $gid;
				$common->updatetable ( 'playergeneral', $updateGen, $updateGenWhere );
				$newData [$gInfo [0] ['sortid']] = $gInfo [0];
				$common->updateMemCache ( MC . $playersid . '_general', $newData );
				$value ['tq'] = $updateRole ['coins'];
				$value ['hqtq'] = $hqtq;
				$value ['time'] = 7200;
				$value ['dzljtq'] = 50;
			} else {
				if ($occupied_end_time > $nowTime && $occupied_playersid == $pid) {
					$bzlrInfo ['playersid'] = $occupied_playersid;
					$roleRes = roleModel::getRoleInfo ( $bzlrInfo, false );
					if (empty ( $roleRes )) {
						$value = array ('status' => 3, 'message' => $sys_lang[1] );
						return $value;
					}
					$hqtq = fightModel::zlsy ( $bzlrInfo ['player_level'], 2, $diffTime ); //获取铜钱收益
					$updateRole ['coins'] = $roleInfo ['coins'] + $hqtq;
					if ($updateRole ['coins'] > COINSUPLIMIT) {
						$updateRole ['coins'] = COINSUPLIMIT;
						$hqtq = COINSUPLIMIT - $roleInfo ['coins'];
					}
					$updateRoleWhere ['playersid'] = $playersid;
					$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
					$common->updateMemCache ( MC . $playersid, $updateRole );
					$value ['time'] = 7200;
					$value ['tq'] = $updateRole ['coins'];
					$value ['hqtq'] = $hqtq;
					$value ['dzljtq'] = 50;
					$updateGen ['last_income_time'] = $nowTime;
					$gInfo [0] ['last_income_time'] = $updateGen ['last_income_time'];
					$updateGenWhere ['intID'] = $gid;
					$common->updatetable ( 'playergeneral', $updateGen, $updateGenWhere );
					$newData [$gInfo [0] ['sortid']] = $gInfo [0];
					$common->updateMemCache ( MC . $playersid . '_general', $newData );
					$sjrq = date('Ymd',time());
					$pvpcs = $mc->get(MC.$playersid."_pvp_".$occupied_playersid.'_'.$sjrq);	
			        if (empty($pvpcs)) {
			        	$pvpcs = 1;
			        }
					if ($pvpcs < 4) {
						//根据级别差来得到获取技能书概率
						$jbc = $roleInfo ['player_level'] - $bzlrInfo ['player_level'];	
						if ($jbc < 10) {
							$jjgl = $jbc + 10;
						} else {
							$jjgl = 20;
						}
						if (rand ( 0, 99 ) < $jjgl) {
							$jnid = rand ( 1, 15 );
							$djid = jndyid ( $jnid );
							//$playerBag = toolsModel::getMyItemInfo($playersid); // 背包列表返回协议优化
							//$addItem = toolsModel::addPlayersItem ( $roleInfo, $djid );
							$addItem = $player->AddItems(array($djid=>1));
							if ($addItem === false) {
								$value ['status'] = 1004;
							} else {
								$jnInfo = jnk ( $jnid );
								$value ['dlmc'] = $jnInfo ['n'];
								$value ['dlsl'] = 1;
								$value ['dliid'] = $jnInfo ['iconid'];
								//$bbInfo = toolsModel::getAllItems ( $playersid );
								//$value ['list'] = $bbInfo ['list'];								
								//$simBg[] = array('insert'=>1, 'ItemID'=>$djid);// 背包列表返回协议优化
								//$bagData = toolsModel::getBglist($simBg, $playersid, $playerBag);// 背包列表返回协议优化
								$bagData = $player->GetClientBag();
								$value ['list'] = $bagData;
							}
						}						
					}
					$mc->set(MC.$playersid."_pvp_".$occupied_playersid.'_'.$sjrq,$pvpcs + 1,0,86400);
					$roleInfo['wcsl'] = 1;
					$rwid = questsController::OnFinish ( $roleInfo, "'sqsy'" );
					if (! empty ( $rwid )) {
						if (! empty ( $rwid )) {
							$value ['rwid'] = $rwid;
						}
					}					
				} else {
					$value ['status'] = 1021;
					$value ['pid'] = $pid;
					$bzlInfo = cityModel::getZfInfo ( $occupied_playersid );
					if ($bzlInfo ['status'] != 1002) {
						$value ['uid'] = $bzlInfo ['uid'];
						$value ['gid'] = $bzlInfo ['gid'];
						$value ['un'] = $bzlInfo ['un'];
						$value ['ul'] = $bzlInfo ['ul'];
						$value ['giid'] = $bzlInfo ['giid'];
						if ($bzlInfo ['status'] == 1001 && ! empty ( $bzlInfo ['zlxy'] )) {
							$value ['zlxy'] = $bzlInfo ['zlxy'];
						}
						$value ['glevel'] = $bzlInfo ['glevel'];
						//$hqtq = fightModel::zlsy ( $bzlInfo ['plevel'],2,$diffTime);
						$value ['dzljtq'] = $bzlInfo ['sy'];
						$value ['gname'] = $bzlInfo ['gname'];
						$value ['gxyd'] = $bzlInfo ['gxyd'];
						$value ['time'] = $bzlInfo ['time'];
						$value['gxj'] = $bzlInfo['gxj'];
						$value['gzy'] = $bzlInfo['gzy'];
						$value['zscl'] = $bzlInfo['zscl'];
					}
				}
			}
		} else {
			$value ['status'] = 1002;
			$value ['gid'] = $gid;
			$value ['ltt'] = 7200 - $diffTime;
		}
		return $value;
	}
	
	//获取自己所有武将驻守占领数据
	public static function hqjlzlsj($playersid) {
		global $db, $common, $_SGLOBAL, $sys_lang;
		$nowTime = $_SGLOBAL ['timestamp'];
		$generalInfo = cityModel::getGeneralData ( $playersid, '', '*' );
		$roleInfo ['playersid'] = $playersid;
		roleModel::getRoleInfo ( $roleInfo );
		if (empty ( $generalInfo )) {
			$value ['status'] = 1021;
			$value ['message'] = $sys_lang[3];
		} else {
			$jwdj = $roleInfo ['mg_level'];
			$jwInfo = jwmc ( $jwdj );
			$jwjc = 1 + $jwInfo ['jc'] / 100;	
			$zbidArray = array ();		
			foreach ( $generalInfo as $gValue ) {				
				$zbtljcArray = array ();
				$zb1 = $gValue ['helmet'];
				if ($zb1 != 0) {
					$zb1Info = toolsModel::getZbSx ( $gValue ['playerid'], $zb1 );
					$zbtljcArray [] = $zb1Info ['tl'];
				}			
				if (! empty ( $zbtljcArray )) {
					$zbtljc = array_sum ( $zbtljcArray );
				} else {
					$zbtljc = 0;
				}	
				unset ( $zbtljcArray );
				$g_zy = $gValue ['professional']; //职业		
				$sxxs = genModel::sxxs ( $g_zy );
				$data ['tf'] = round ( $gValue ['understanding_value'], 2 ); //天赋
				$data ['jj'] = intval ( $gValue ['professional_level'] ); //军阶
				$data ['tl'] = genModel::hqwjsx ( $gValue ['general_level'], $data ['tf'], $data ['jj'], $gValue ['llcs'], $jwjc, $zbtljc, $sxxs ['tl'], $gValue ['py_tl'] );
				$data ['jy'] = intval ( $gValue ['current_experience'] ); //当前经验
				$data ['xjjy'] = cityModel::getGeneralUpgradeExp ( $gValue ['general_level'] ); //下级所需经验	
				$data ['smsx'] = $data ['tl'] * 10; //生命上限			
				$data ['smz'] = round ( $gValue ['general_life'], 0 ); //生命值
				if ($data ['smz'] > $data ['smsx']) {
					$data ['smz'] = $data ['smsx'];
				}				
				if ($gValue ['occupied_playersid'] != 0 && $gValue ['occupied_playersid'] != $playersid && $gValue ['occupied_end_time'] < $nowTime) {
					$tuid = 0;
				} else {
					if ($gValue ['occupied_playersid'] == $playersid && $gValue ['intID'] != $roleInfo ['aggressor_general']) {
						$tuid = 0;
					} else {
						$tuid = $gValue ['occupied_playersid'];
					}
				}
				
				if ($roleInfo ['zf_aggressor_general'] == $gValue ['intID']) {
					$scg = 1;
				} else {
					$scg = 0;
				}
/*hk*///修改武将状态				
				if ($gValue ['gohomeTime'] > $nowTime && ($gValue ['act'] == 4 || $gValue ['act'] == 7)) {
					$czzt = 7;
				} elseif ($gValue ['gohomeTime'] > $nowTime && $gValue ['act'] == 3) {
					$czzt = 6;
				} elseif ($gValue ['gohomeTime'] > $nowTime && $gValue ['act'] == 2) {
					$czzt = 5;
				} elseif ($gValue ['act'] == 1 || $gValue ['act'] == 6) {
					$czzt = 4;
				} elseif ($gValue ['occupied_playersid'] == $playersid && $gValue ['intID'] != $roleInfo ['aggressor_general']) {
					$czzt = intval ( $gValue ['f_status'] );
				} elseif ($gValue ['occupied_end_time'] > $nowTime || $gValue ['occupied_playersid'] == $playersid) {
					$czzt = 3;
				} else {
					$czzt = intval ( $gValue ['f_status'] );
				}				
				$czzt2 = intval ( $gValue ['f_status'] );
				$gInfo [] = array ('czzt2'=>$czzt2,'czzt'=>$czzt,'gid' => $gValue ['intID'], 'tuid' => intval ( $tuid ), 'scg' => $scg, 'smz' => $data ['smz'], 'smsx' => $data ['smsx'], 'jy' => $data ['jy'], 'xjjy' => $data ['xjjy'], 'jb' => intval($gValue ['general_level']) );/*hk*/
				$scg = null;
				$tuid = null;
				$zb1 = null; 
/*hk*/				$czzt = null;
				unset ( $data );
				unset ( $sxxs );
			}
			if ($_SESSION['playersid'] == $playersid) {
			    $rwid = questsController::OnFinish($roleInfo,"'ckwj'");
	            if (!empty($rwid)) {
	              $value['rwid'] = $rwid;
	            }
		    	/*$xwzt_7 = substr($roleInfo['xwzt'],6,1);  //完成 查看武将行为
				if ($xwzt_7 == 0) {
					$xwzt = substr_replace($roleInfo['xwzt'],'1',6,1);
					//$value['xwzt'] = $xwzt;
					$common->updatetable('player',"xwzt = '$xwzt'","playersid = '$playersid' LIMIT 1");
					$common->updateMemCache(MC.$playersid,array('xwzt'=>$xwzt));			
				} */	            
			} 			
			$value ['status'] = 0;
			$value ['ginfo'] = $gInfo;
			/*$xyd = guideScript::jsydsj($roleInfo,'ckdjt',1);	
			//接收新的引导
			//$xyd = guideScript::jsydsj($roleInfo,'cg');
			if ($xyd !== false) {
				$value['ydts'] = $xyd;
			}*/
		}
		return $value;
	}
	
	//能否挖矿一个玩家一天只能挖矿10次，挖自己矿180秒，挖别人要1800秒
	public static function is_Excavation($last_wk_time, $wk_count, $is_T = false) {
		//print_r ( $roleInfo );
		$tmp_time = time ();
		$date_now = date ( "Y-m-d", $tmp_time );
		$sjInfo = wksjxz ();
		if ($is_T == false) {
			$diffTime = $sjInfo [0];
			$diffCount = $sjInfo [1];
		} else {
			$diffTime = $sjInfo [2];
			$diffCount = $sjInfo [1];
		}
		if ($wk_count >= $diffCount) {
			return 0;
		}
		
		if ((date ( "Y-m-d", $last_wk_time ) == $date_now 
		|| $last_wk_time == 0 
		|| $last_wk_time + $diffTime < $tmp_time) && $wk_count < $diffCount && $last_wk_time + $diffTime < $tmp_time) {
			return 1;
		}
		return 0;
	}
	
	//能否征收
	public static function is_Collection($last_collect_time) {
		//if (($last_collect_time + collectNeedTime ()) <= time ()) {
		if (($last_collect_time + 7200) <= time ()) {
			return 1;
		}
		return 0;
	}
	
	//能否占领征收
	public static function is_ZCollection($playersid) {
		global $mc, $common, $_SGLOBAL, $db, $sys_lang;
		$nowTime = $_SGLOBAL ['timestamp'];
		$roleInfo ['playersid'] = $playersid;
		$roleRes = roleModel::getRoleInfo ( $roleInfo, false );
		if (empty ( $roleRes )) {
			$value = array ('status' => 3, 'message' => $sys_lang[1]);
			return $value;
		}
		$incomeInfo = cityModel::occupied_income ( $roleInfo ['aggressor_general'], $roleInfo ['aggressor_playersid'] );
		$time = $incomeInfo ['time'];
		if ($time == 0) {
			//$leftTime = 0;
			return 1;
		} else {
			//$leftTime = 7200 - $syjg;
			return 0;
		}
	
	}
	
	//检查能否升级建筑
	public static function is_BuildUpgradeOne($roleInfo, $itemId, $myitem, $buildId, $i) { 
		global $city_lang;
		//$roleInfo['playersid'],'10036','sc_level' 
		//$playersid = $roleInfo ['playersid'];
		$buildAmoutArray = array ();
		//$myitem = toolsModel::getMyItemInfo ( $playersid );
		if (! empty ( $myitem )) {
			foreach ( $myitem as $myitemValue ) {
				if ($myitemValue ['ItemID'] == $itemId) {
					$buildAmoutArray [] = $myitemValue ['EquipCount'];
				}
			}
		} else {
			return 0;
		}
		$zy = array_sum ( $buildAmoutArray );
		$value = 0;
		$level_yq = requestLevel ( $i, $roleInfo [$buildId] + 1 );
		if ($level_yq != $city_lang['is_BuildUpgradeOne_1']) {
			if ($roleInfo ['player_level'] >= $level_yq) {
				/*$money = needMoney($i,$roleInfo['sc_level']+1);
				if($roleInfo['coins'] >= $money[0] && $zy >= $money[1]) {
					$value = 1;
				}*/
				$value = 1;
			}
		}
		return $value;
	}
	
	//检查能否升级建筑
	public static function is_BuildUpgrade($roleInfo) {
		global $city_lang;
		//市场 1 sc_level
		$playersid = $roleInfo ['playersid'];
		$woodAmoutArray = array ();
		$concreteAmoutArray = array ();
		$stoneAmoutArray = array ();
		$clothAmoutArray = array ();
		$ironAmoutArray = array ();
		$myitem = toolsModel::getMyItemInfo ( $playersid );
		if (! empty ( $myitem )) {
			foreach ( $myitem as $myitemValue ) {
				if ($myitemValue ['ItemID'] == 10032) {
					$stoneAmoutArray [] = $myitemValue ['EquipCount'];
				}
				if ($myitemValue ['ItemID'] == 10033) {
					$woodAmoutArray [] = $myitemValue ['EquipCount'];
				}
				if ($myitemValue ['ItemID'] == 10034) {
					$concreteAmoutArray [] = $myitemValue ['EquipCount'];
				}
				if ($myitemValue ['ItemID'] == 10035) {
					$ironAmoutArray [] = $myitemValue ['EquipCount'];
				}
				if ($myitemValue ['ItemID'] == 10036) {
					$clothAmoutArray [] = $myitemValue ['EquipCount'];
				}
			}
		}
		$wood = array_sum ( $woodAmoutArray ); //木材量
		$concrete = array_sum ( $concreteAmoutArray ); //陶土量
		$stone = array_sum ( $stoneAmoutArray ); //石材量
		$cloth = array_sum ( $clothAmoutArray ); //绢布量
		$iron = array_sum ( $ironAmoutArray ); //铁矿量
		$value = '';
		$level_yq = requestLevel ( 1, $roleInfo ['sc_level'] + 1 );
		if ($level_yq != $city_lang['is_BuildUpgradeOne_1']) {
			if ($roleInfo ['player_level'] >= $level_yq) {
				$money = needMoney ( 1, $roleInfo ['sc_level'] + 1 );
				if ($roleInfo ['coins'] >= $money [0] && $cloth >= $money [1]) {
					$value .= '1' . ',';
				}
			}
		}
		
		//领地 2 ld_level
		$level_yq = requestLevel ( 1, $roleInfo ['ld_level'] + 1 );
		if ($level_yq != $city_lang['is_BuildUpgradeOne_1']) {
			if ($roleInfo ['player_level'] >= $level_yq) {
				$money = needMoney ( 1, $roleInfo ['ld_level'] + 1 );
				if ($roleInfo ['coins'] >= $money [0] && $wood >= $money [1]) {
					$value .= '2' . ',';
				}
			}
		}
		
		//铁矿 3 tjp_level
		$level_yq = requestLevel ( 1, $roleInfo ['tjp_level'] + 1 );
		if ($level_yq != $city_lang['is_BuildUpgradeOne_1']) {
			if ($roleInfo ['player_level'] >= $level_yq) {
				$money = needMoney ( 1, $roleInfo ['tjp_level'] + 1 );
				if ($roleInfo ['coins'] >= $money [0] && $iron >= $money [1]) {
					$value .= '3' . ',';
				}
			}
		}
		
		//酒馆 4 jg_level
		$level_yq = requestLevel ( 1, $roleInfo ['jg_level'] + 1 );
		if ($level_yq != $city_lang['is_BuildUpgradeOne_1']) {
			if ($roleInfo ['player_level'] >= $level_yq) {
				$money = needMoney ( 1, $roleInfo ['jg_level'] + 1 );
				if ($roleInfo ['coins'] >= $money [0] && $stone >= $money [1]) {
					$value .= '4' . ',';
				}
			}
		}
		
		//点将台 5 djt_level
		$level_yq = requestLevel ( 1, $roleInfo ['djt_level'] + 1 );
		if ($level_yq != $city_lang['is_BuildUpgradeOne_1']) {
			if ($roleInfo ['player_level'] >= $level_yq) {
				$money = needMoney ( 1, $roleInfo ['djt_level'] + 1 );
				if ($roleInfo ['coins'] >= $money [0] && $concrete >= $money [1]) {
					$value .= '5' . ',';
				}
			}
		}
		$count = strlen ( $value );
		if ($count > 0) {
			return substr ( $value, 0, strlen ( $value ) - 1 );
		} else {
			return '';
		}
	
	}
	
	//获取建筑升级信息	
	public static function getBuildInfo($roleInfo) {
		$value = array ();
		$value [] = cityModel::getUpgradeMoney ( 1, $roleInfo ['sc_level'] + 1, 10036 ); //绢布
		$value [] = cityModel::getUpgradeMoney ( 2, $roleInfo ['ld_level'] + 1, 10033, 5, $roleInfo ['player_level'] ); //木材
		$value [] = cityModel::getUpgradeMoney ( 3, $roleInfo ['tjp_level'] + 1, 10035, 3, $roleInfo ['player_level'] ); //铁矿
		$value [] = cityModel::getUpgradeMoney ( 4, $roleInfo ['jg_level'] + 1, 10032 ); //石材
		$value [] = cityModel::getUpgradeMoney ( 5, $roleInfo ['djt_level'] + 1, 10034 ); //陶土
		return $value;
	}
	
	//获取建筑升级信息
	public static function getUpgradeMoney($upgradeId, $requestLevel, $djid, $level = 0, $plevel = 0) {
		global $city_lang;
		$level_yq = requestLevel ( $upgradeId, $requestLevel );
		$value = array ();
		if ($level_yq !== $city_lang['is_BuildUpgradeOne_1']) {
			if ($level > 0) {
				if ($plevel < $level) {
					$value ['jzdj'] = 's';
				} else {
					$value ['jzdj'] = $requestLevel - 1;
				}
			} else {
				$value ['jzdj'] = $requestLevel - 1;
			}
			
			$money = needMoney ( $upgradeId, $requestLevel );
			$value ['jzid'] = $upgradeId;
			$value ['sfmj'] = 0;
			$value ['wjjb'] = $level_yq;
			$value ['sjtq'] = $money [0];
			$value ['zyid'] = $upgradeId;
			$value ['djid'] = $djid;
			$value ['zysl'] = $money [1];
		} else {
			$value ['jzid'] = $upgradeId;
			$value ['jzdj'] = $requestLevel - 1;
			$value ['sfmj'] = 1;
			$value ['wjjb'] = '';
			$value ['sjtq'] = '';
			$value ['zyid'] = '';
			$value ['djid'] = $djid;
			$value ['zysl'] = '';
		}
		return $value;
	}
	
	//获取可占领列表
	public static function zllist($playersid, $resetData = false) {
		global $_SGLOBAL, $common, $db, $mc;
		$roleInfo ['playersid'] = $playersid;
		roleModel::getRoleInfo ( $roleInfo );
		$player_level = $roleInfo ['player_level'];
		$player_level_up = $player_level + 5;
		$player_level_dw = $player_level - 5;
		$listArray = array ();
		if (! ($zllist = $mc->get ( MC . $playersid . '_zllist' )) || $resetData == true) {
			$sql_1 = "SELECT * FROM " . $common->tname ( 'playersid' ) . " WHERE player_level = '$player_level' && aggressor_playersid != '$playersid' LIMIT 6";
			$res_1 = $db->query ( $sql_1 );
			while ( $rows_1 = $db->fetch_array ( $res_1 ) ) {
				$listArray [] = $rows_1;
			}
			$amount_1 = count ( $listArray );
			if ($amount_1 < 6) {
				$sql_2 = "SELECT * FROM " . $common->tname ( 'playersid' ) . " WHERE player_level > '$player_level' && player_level <= '$player_level_up' && aggressor_playersid != '$playersid' ORDER BY player_level ASC LIMIT " . (6 - $amount_1);
				$res_2 = $db->query ( $sql_2 );
				while ( $rows_2 = $db->fetch_array ( $res_2 ) ) {
					$listArray [] = $rows_2;
				}
			}
			$amount_2 = count ( $listArray );
			if ($amount_2 < 6) {
				$sql_3 = "SELECT * FROM " . $common->tname ( 'playersid' ) . " WHERE player_level < '$player_level' && player_level >= '$player_level_dw' && aggressor_playersid != '$playersid' ORDER BY player_level DESC LIMIT " . (6 - $amount_1);
				$res_3 = $db->query ( $sql_3 );
				while ( $rows_3 = $db->fetch_array ( $res_3 ) ) {
					$listArray [] = $rows_3;
				}
			}
			$mc->set ( MC . $playersid . '_zllist', $listArray, 0, 3600 );
		}
		if (! empty ( $listArray )) {
			return $listArray;
		} else {
			return false;
		}
	}
	
	//补齐升级所需材料
	public static function bqsjcl($playersid, $buildId) {
		global $common, $G_PlayerMgr, $city_lang, $sys_lang;
		$roleInfo ['playersid'] = $playersid;
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$sys_lang[1]);
		
		//$buildId 1市场2领地3铁匠铺4酒馆5点将台
		$buildItem = buildingName ( $buildId );
		roleModel::getRoleInfo ( $roleInfo );
		$level = $roleInfo [$buildItem]; //当前等级
		$upgradeLevel = $level + 1;
		$ingot = $roleInfo ['ingot'];
		//$coins = $roleInfo['coins'];
		$woodAmoutArray = array ();
		$concreteAmoutArray = array ();
		$stoneAmoutArray = array ();
		$clothAmoutArray = array ();
		$ironAmoutArray = array ();
		$myitem = toolsModel::getMyItemInfo ( $playersid );
		if (! empty ( $myitem )) {
			foreach ( $myitem as $myitemValue ) {
				if ($myitemValue ['ItemID'] == 10032) {
					$stoneAmoutArray [] = $myitemValue ['EquipCount'];
				}
				if ($myitemValue ['ItemID'] == 10033) {
					$woodAmoutArray [] = $myitemValue ['EquipCount'];
				}
				if ($myitemValue ['ItemID'] == 10034) {
					$concreteAmoutArray [] = $myitemValue ['EquipCount'];
				}
				if ($myitemValue ['ItemID'] == 10035) {
					$ironAmoutArray [] = $myitemValue ['EquipCount'];
				}
				if ($myitemValue ['ItemID'] == 10036) {
					$clothAmoutArray [] = $myitemValue ['EquipCount'];
				}
			}
		}
		$wood = array_sum ( $woodAmoutArray ); //木材量
		$concrete = array_sum ( $concreteAmoutArray ); //陶土量
		$stone = array_sum ( $stoneAmoutArray ); //石材量
		$cloth = array_sum ( $clothAmoutArray ); //绢布量
		$iron = array_sum ( $ironAmoutArray ); //铁矿量		
		$needMoney = needMoney ( $buildId, $upgradeLevel );
		$needTq = $needMoney [0]; //所需铜钱
		$needCl = $needMoney [1]; //所需材料
		//$coins = $coins - $needTq;
		//$updateRole ['coins'] = $coins;
		if ($buildId == 1) {
			if ($cloth < $needCl) {
				$diff_cl = $needCl - $cloth;
				$clid = 10036;
				$full = 0;
			} else {
				$full = 1;
			}
		} elseif ($buildId == 2) {
			if ($wood < $needCl) {
				$diff_cl = $needCl - $wood;
				$clid = 10033;
				$full = 0;
			} else {
				$full = 1;
			}
		} elseif ($buildId == 3) {
			if ($iron < $needCl) {
				$diff_cl = $needCl - $iron;
				$clid = 10035;
				$full = 0;
			} else {
				$full = 1;
			}
		} elseif ($buildId == 4) {
			if ($stone < $needCl) {
				$diff_cl = $needCl - $stone;
				$clid = 10032;
				$full = 0;
			} else {
				$full = 1;
			}
		} elseif ($buildId == 5) {
			if ($concrete < $needCl) {
				$diff_cl = $needCl - $concrete;
				$clid = 10034;
				$full = 0;
			} else {
				$full = 1;
			}
		} else {
			$value ['status'] = 3;
			$value ['message'] = $sys_lang[7];
			return $value;
		}
		if ($full == 0) {
			$needYb = ybtocl ( $clid, $diff_cl );
			if ($needYb > $ingot) {
				$value ['status'] = 88;
				$arr1 = array('{xhyb}','{yb}');
				$arr2 = array($needYb,$ingot);
				$value ['message'] = str_replace($arr1,$arr2,$sys_lang[2]);
				$value ['yb'] = intval ( $ingot );
			} else {
				//$result = toolsModel::addPlayersItem ( $roleInfo, $clid, $diff_cl );
				$result = $player->AddItems(array($clid=>$diff_cl));
				if ($result === false) {
					$value ['status'] = 1021;
					$value ['message'] = $city_lang['bqcl'];
				} else {
					$value ['status'] = 0;
					$updateRole ['ingot'] = $ingot - $needYb;
					$common->updatetable ( 'player', $updateRole, "playersid = '$playersid'" );
					$common->updateMemCache ( MC . $playersid, $updateRole );
					$value ['yb'] = intval ( $updateRole ['ingot'] );
					$value ['xhyb'] = $needYb;
					//$newItem = toolsModel::getAllItems ( $playersid );
					//$value ['list'] = $newItem ['list'];
					//$simBg[] = array('insert'=>1, 'ItemID'=>$clid);  // 背包列表返回协议优化
					//$bagData = toolsModel::getBglist($simBg, $playersid, $myitem);  // 背包列表返回协议优化
					$bagData = $player->GetClientBag();
					$value ['list'] = $bagData;
				}
			}
		} else {
			$value ['status'] = 1021;
			$value ['message'] = $city_lang['bqsjcl_1'];
			return $value;
		}
		return $value;
	}
	
	/**
	 * @author kknd li
	 * vip 信息接口实现
	 * @param int $playersid
	 */
	public static function vipxx($playersid) {
		global $db, $common, $mc, $sys_lang, $city_lang;
		
		$roleInfo = array ('playersid' => $playersid );
		if (roleModel::getRoleInfo ( $roleInfo )) {
			$returnValue ['status'] = 0;
			$returnValue ['vip'] = intval($roleInfo ['vip']);
			
			// 如果VIP满级就将下一级要充值元宝设为零
			$vipPrice = getVipPrice ();

			if ($returnValue ['vip'] != 0) {
				$returnValue ['yxq'] = date ( $city_lang['vipxx_1'], $roleInfo ['vip_end_time'] );
			}

			if (! isset ( $vipPrice [$returnValue ['vip'] + 1] )) {
				$returnValue ['xjcz'] = 0;
				return $returnValue;
			}
			
			// 得到需要检查的时间来计算实际缴费多少
			$money = roleModel::getPlayerPayYaobao($roleInfo['playersid']);
			
			// 计算下级需要的元宝并返回
			$needMoney = $vipPrice [$returnValue['vip'] + 1] * RMB_TO_YUANBAO - $money;

			$returnValue ['xjcz'] = $needMoney <= 0?0:$needMoney;
			return $returnValue;
		} else {
			$returnValue ['status'] = 1102;
			$returnValue ['message'] = $sys_lang[1];
		}
	
	}
	
	/**
	 * 
	 * 矿山,市场,领地,铁匠铺,酒馆,点将台 这几个建筑的状态
	 *
	 * @param int $playersid			目标玩家id
	 * @param boolean $is_T				请求true其它玩家信息，false自己的信息
	 * @return string					市场,领地,铁匠铺,酒馆,点将台:12345  0无状态，1可升级，,2可采矿，3可征收，
	 */
	public static function jzzt($playersid, $is_T = false) {
		global $_SGLOBAL,$G_PlayerMgr;
		
		// 获得玩家信息
		//$roleInfo ['playersid'] = $playersid;		 
		$player = $G_PlayerMgr->GetPlayer($playersid);
		$roleInfo = &$player->GetBaseInfo();//roleModel::getRoleInfo ( $playersid );
		if($playersid == $_SESSION['playersid']){
			$myRoleInfo = $roleInfo;
			//$roleRes2 = $roleRes;
		}
		else{
			//$myRoleInfo ['playersid'] = $_SESSION ['playersid'];
			//$roleRes2 = roleModel::getRoleInfo ( $myRoleInfo );
			$player2 = $G_PlayerMgr->GetPlayer($_SESSION ['playersid']);
			$myRoleInfo = &$player2->GetBaseInfo();//roleModel::getRoleInfo ( $playersid );			
		}
		/*if (empty ( $roleRes ) || empty ( $roleRes2 )) {
			$value = array ('status' => 3, 'message' => '没有找到玩家数据' );
			return $value;
		}*/
		
		if ($is_T == true) {
			// 可否挖矿 1可以 0 不可以
			if ($myRoleInfo ['aggressor_playersid'] == $playersid && $myRoleInfo ['end_defend_time'] > $_SGLOBAL ['timestamp']) {
				$wk_count = cityModel::checkMineTimes($roleInfo);
				$v1_wk = cityModel::is_Excavation ( $roleInfo ['last_twk_time'], $wk_count, $is_T );
			} else {
				$v1_wk = 0;
			}
			
			// 市场可否征收 1可以 0 不可以 目前在被占领情况下都不可以
			$v2_sc_zs = 0;
			
			// 酒馆偷将
			if (cityModel::is_onetj ( $_SESSION ['playersid'], $roleInfo ) == 1) {
				$v5_jg_up = 4;
			} else {
				$v5_jg_up = 0; //别人的酒馆
			}
			
			$v2_sc_up = 0;
			$v3_ld_up = 0; //领地
			$v4_tjp_up = 0;
			$v6_djt_up = 0;
		} else {
			$wk_count = cityModel::checkMineTimes($roleInfo);
			$v1_wk = cityModel::is_Excavation ( $roleInfo ['last_wk_time'], $wk_count, $is_T ); //能否挖矿 1可以 0 不可以
			$v2_sc_zs = cityModel::is_Collection ( $roleInfo ['last_collect_time'] );
			$myitem = $player->GetItems();//toolsModel::getMyItemInfo ( $playersid );
			$v2_sc_up = cityModel::is_BuildUpgradeOne ( $roleInfo, '10036',$myitem, 'sc_level', 1 );
			//判断是否能偷将
			if (cityModel::is_tj ( $playersid ) == 1) {
				$v3_ld_up = 4;
			} elseif (cityModel::is_zlsy ( $playersid ) == 1) { //is_zs($playersid)
				$v3_ld_up = 5;
			} else {
				$v3_ld_up = cityModel::is_BuildUpgradeOne ( $roleInfo, '10033', $myitem,'ld_level', 2 );
			}
			$v4_tjp_up = cityModel::is_BuildUpgradeOne ( $roleInfo, '10035', $myitem,'tjp_level', 3 );
			$v5_jg_up = cityModel::is_BuildUpgradeOne ( $roleInfo, '10032', $myitem,'jg_level', 4 );
			$v6_djt_up = cityModel::is_BuildUpgradeOne ( $roleInfo, '10034',$myitem, 'djt_level', 5 );
		}
		
		$v1 = ($v1_wk == 1) ? 2 : 0;
		$v2 = ($v2_sc_zs == 1) ? 3 : (($v2_sc_up == 1) ? 1 : 0);
		$v3 = $v3_ld_up;
		$v4 = $v4_tjp_up;
		$v5 = $v5_jg_up;
		$v6 = $v6_djt_up;
		return $v1 . "," . $v2 . "," . $v3 . "," . $v4 . "," . $v5 . "," . $v6;
	}

	/**
	 *
	 * 每日登录奖励
	 *
	 **/
	public static function getLoginAwardInfo($roleInfo){
		global $common, $city_lang, $db;

		$whereArray = array('playersid'=>$roleInfo['playersid']);
		$awardInfo  = $common->sltTableOnlyOneWithMem('login_award', $whereArray);
		
		// 初始化检查奖励
		$awardArray = array();
		if(isset($awardInfo['award'])){
			$awardArray['award'] = json_decode($awardInfo['award'], true);
			$awardArray['lastDay']  = $awardInfo['lastDay'];
			$awardArray['mday']     = $awardInfo['mday'];
			$awardArray['wday']     = $awardInfo['wday'];
		}else{
			// 20天对应索引为0
			$awardArray['award'] = array(0,2,0,0,0,0,0,0);
			$awardArray['lastDay']  = date('Y-m-d');
			$awardArray['mday']     = 1;
			$awardArray['wday']     = 1;
		}

		// 上次登陆不在当天发生时
		if($awardArray['lastDay'] != date('Y-m-d')){
			// 连续登录时更新奖励数据
			if($awardArray['lastDay'] == date('Y-m-d', time()-24*3600)){
				$awardArray['mday']++;
				if($awardArray['mday'] == 20){
					$awardArray['award'][0] = 2;
				}

				$awardArray['wday']++;
				if(7 >= $awardArray['wday']&&isset($awardArray['award'][$awardArray['wday']])){
					$awardArray['award'][$awardArray['wday']] = 2;
				}else if(7 < $awardArray['wday']){
					$awardArray['wday'] = 1;
					$awardArray['award'][1] = 2;
				}
				$awardArray['lastDay'] = date('Y-m-d');
			}// 未连续登陆,新的开始
			else{
				$awardArray['lastDay'] = date('Y-m-d');
				$awardArray['wday'] = 1;
				$awardArray['mday'] = 1;
				$awardArray['award'][1] = 2;
				for($i=2; $i<=7; $i++){
					$awardArray['award'][$i] = $awardArray['award'][$i]==1?0:$awardArray['award'][$i];
				}
			}

			// 每月第一天清理20天奖励
			if(1 == date('d')){
				$awardArray['award'][0] = 0;
				$awardArray['mday'] = 1;
			}

			// 更新数据
			$update = $awardArray;
			$update['award'] = json_encode(array_values($update['award']));

			$common->updateTableOnlyOneWithMem('login_award', $update, $whereArray);
		}// 没有用户记录时的第一次写入
		else if(!isset($awardInfo['award'])){
			$insert = $awardArray;
			$insert['award'] = json_encode($insert['award']);
			$insert['playersid'] = $whereArray['playersid'];
			$common->intTableOnlyOneWithMem('login_award', $insert, $whereArray);
		}

		// 构造反回数据
		$returnValue['qrjlts2'] = $awardArray['mday'];
		$returnValue['qrjllq2'] = $awardArray['award'][0];
		$qrlist = array();
		$awardItems = cityModel::getAwardInfo();
		for($i=1; $i<=7; $i++){
			$_awardInfo = array();
			$_awardInfo['day'] = $i;
			$_awardInfo['zt']  = $awardArray['award'][$i];
			$awardList = $awardItems[$i];
			foreach($awardList as $item){
				$awardValue = array();
				if($item['id'] != 'yp'){
					$itemInfo = toolsModel::getItemInfo($item['id']);
					$awardValue['mc'] = $itemInfo['Name'];
					$awardValue['sl'] = $item['num'];
					$awardValue['iid'] = $itemInfo['IconID'];
					$awardValue['xyd'] = toolsModel::getRealRarity($itemInfo);
				}else{
					$awardValue['mc'] = $city_lang['yingpiao'];
					$awardValue['sl'] = $item['num'];
					$awardValue['iid'] = 'icon_yp';
					$awardValue['xyd'] = 0;
				}
				$_awardInfo['dljl'][] = $awardValue;
			}
			$qrlist[] = $_awardInfo;
		}
		$returnValue['qrlist'] = $qrlist;

		// 检查当前是否有可领奖励
		$returnValue['hasAward'] = false;
		foreach($awardArray['award'] as $value){
			if(2 == $value){
				$returnValue['hasAward'] = true;
			}
		}

		return $returnValue;
	}

	/**
	 * 领取登陆奖励
	 **/
	public static function getLoginAward($playersid, $dayId){
		global $common, $city_lang, $db, $G_PlayerMgr;

		$whereArray = array('playersid'=>$playersid);
		$awardInfo  = $common->sltTableOnlyOneWithMem('login_award', $whereArray);

		$awardList = json_decode($awardInfo['award'], true);
		if(!isset($awardList[$dayId])
		   ||$awardList[$dayId] != 2){
			return array('status'=>625,
						 'message'=>$city_lang['loginaward'][0]);
		}

		$awardItems = cityModel::getAwardInfo();
		$awards = $awardItems[$dayId];

		$addItemList = array();
		$addyp = 0;
		foreach($awards as $awardInfo){
			if($awardInfo['id'] == 'yp'){
				$addyp += $awardInfo['num'];
				continue;
			}
			$addItemList[$awardInfo['id']] = $awardInfo['num'];
		}
		
		$returnValue['lx'] = $dayId;
		$player = $G_PlayerMgr->GetPlayer($playersid);
		$canAdd = $player->AddItems($addItemList);
		if($canAdd === false){
			return array('status'=>1001,
						 'message'=>$city_lang['loginaward'][1]);
		}

		if(0 < $addyp){
			$roleInfo = $player->GetBaseInfo();
			$returnValue['hqyp'] = $addyp;
			$updateRole['silver'] = $roleInfo['silver'] + $addyp;
			$whereRole['playersid'] = $playersid;
			$common->updatetable('player', $updateRole, $whereRole); 
			$common->updateMemCache(MC.$playersid, $updateRole);
		}

		if(0 == $dayId){
			$awardValue = array();
			foreach($awards as $item){
				if($item['id'] != 'yp'){
					$itemInfo = toolsModel::getItemInfo($item['id']);
					$awardValue[] = array('mc' => $itemInfo['Name'],
										  'sl' => $item['num'],
										  'iid' => $itemInfo['IconID'],
										  'xyd' => toolsModel::getRealRarity($itemInfo));
				}else{
					$awardValue[] = array('mc' => $city_lang['yingpiao'],
										  'sl' => $item['num'],
										  'iid' => 'icon_yp',
										  'xyd' => 0);
				}
			}
			$returnValue['jidj'] = $awardValue;
		}

		// 领取后更新奖励新息
		$awardList[$dayId] = 1;
		$awardInfo[$dayId]['award'] = json_encode($awardList);
		$common->updateTableOnlyOneWithMem('login_award', $awardInfo[$dayId], array('playersid'=>$playersid));

		$returnValue['list'] = $player->GetClientBag();
		$returnValue['status'] = 0;
		return $returnValue;
	}
	
	/**
	 * @author kknd li
	 * 获得宝箱信息
	 * @param int $playersid		玩家id
	 * @return array				返回当前vip等级和下两级的宝箱信息
	 * 废弃
	 */
	public static function getGemBoxInfo($roleInfo) {
		global $common, $rw_lang;
		// 获得玩家VIP等级
		//$roleInfo = array ('playersid' => $playersid );
		//roleModel::getRoleInfo ( $roleInfo );
		$vip = $roleInfo ['vip'];
		
		// 获得玩家宝箱信息
		$whereArray = array ('playersid' => $roleInfo['playersid'] );
		$gemboxInfo = $common->sltTableOnlyOneWithMem ( 'gembox_info', $whereArray );
		
		//如果不存或者过期在就构造新宝箱并更新数据库
		$boxday = isset ( $gemboxInfo ['validay'] ) ? strtotime ( $gemboxInfo ['validay'] ) : 0;
		$newBoxInfo = isset ( $gemboxInfo ['gemBoxInfo'] ) ? json_decode ( $gemboxInfo ['gemBoxInfo'] , true) : null;
		
		// 检查宝箱信息是否和vip信息一致，如果不一致就更新数据
		if (! empty ( $newBoxInfo ) && 0 == $newBoxInfo [$vip]) {
			for($i = 0; $i <= 4; $i ++) {
				$newBoxInfo [$i] = 0;
			}
			$newBoxInfo [$vip] = 2;
			$updateArray = array ('gemBoxInfo' => json_encode ( $newBoxInfo ) );
			$common->updateTableOnlyOneWithMem ( 'gembox_info', $updateArray, $whereArray );
		}
		
		if (date ( 'Ymd', time () ) != date ( 'Ymd', $boxday )) {
			$newBoxInfo = array ();
			for($i = 0; $i < 5; $i ++) {
				if ($vip == $i) { // 未打开状态
					$newBoxInfo [] = 2;
				} else { // 不能打开的状态
					$newBoxInfo [] = 0;
				}
			}
			
			// 不存在宝箱信息
			if (empty ( $gemboxInfo )) {
				$insertArray = array ('playersid' => $roleInfo['playersid'], 'gemBoxInfo' => json_encode ( $newBoxInfo ), 'validay' => date ( 'Y-m-d' ) );
				$common->intTableOnlyOneWithMem ( 'gembox_info', $insertArray, $whereArray );
			} // 存在宝箱信息
			else {
				
				$updateArray = array ('gemBoxInfo' => json_encode ( $newBoxInfo ), 'validay' => date ( 'Y-m-d' ) );
				$common->updateTableOnlyOneWithMem ( 'gembox_info', $updateArray, $whereArray );
			}
		}
		
		// 格式化返回的宝箱信息， 返回当前vip等级和下两级的宝箱信息
		$gemboxRule = getVipGemBoxInfo ();
		for($i = 0; $i < 3; $i ++) {
			$boxId = $vip + $i;
			
			if (! isset ( $gemboxRule [$boxId] ))
				break;
			
			$vipBoxInfo = $gemboxRule [$boxId];
			$returnValue [$i] ['bh'] = $boxId;
			$returnValue [$i] ['zt'] = $newBoxInfo [$boxId];
			
			// 宝箱内容，铜钱，银票和物品
			$boxContent = array ();
			if (isset ( $vipBoxInfo ['tq'] ) && $vipBoxInfo ['tq'] > 0) {
				$boxContent [] = array ('mc' => $rw_lang['rw_getRWInfo_4'], 'sl' => $vipBoxInfo ['tq'], 'tb' => 1 );
			}
			if (isset ( $vipBoxInfo ['yp'] ) && $vipBoxInfo ['yp'] > 0) {
				$boxContent [] = array ('mc' => $rw_lang['rw_getRWInfo_3'], 'sl' => $vipBoxInfo ['yp'], 'tb' => 2 );
			}
			foreach ( $vipBoxInfo ['tools'] as $itemid => $num ) {
				$itemInfo = toolsModel::getItemInfo ( $itemid );
				$boxContent [] = array ('mc' => $itemInfo ['Name'], 'sl' => $num, 'tb' => $itemInfo ['IconID'] );
			}
			$returnValue [$i] ['jl'] = $boxContent;
		}
		
		// 返回最后的宝箱信息
		return $returnValue;
	}
	
	/**
	 * @author kknd li
	 * 处理kzjlb开宝箱接口的调用
	 * @param int $playersid
	 * @param int $boxid      0普通，1vip1,...,4vip4
	 * 废弃
	 */
	public static function openGemBox($playersid, $boxid) {
		global $common, $_SGLOBAL, $G_PlayerMgr, $city_lang, $sys_lang, $zyd_lang, $rw_lang;
		$nowTime = $_SGLOBAL ['timestamp'];
		
		$roleInfo = array ('playersid' => $playersid );
		roleModel::getRoleInfo ( $roleInfo );
		//cityModel::resourceGrowth ( $roleInfo );
		$vip = $roleInfo ['vip'];
		
		// 获得宝箱信息
		$whereArray = array ('playersid' => $playersid );
		$gemboxInfo = $common->sltTableOnlyOneWithMem ( 'gembox_info', $whereArray );
		
		if (! $gemboxInfo) {
			$returnValue ['status'] = 1010;
			$returnValue ['message'] = $city_lang['openGemBox_1'];
			return $returnValue;
		}
		
		$gemboxInfo ['gemBoxInfo'] = json_decode ( $gemboxInfo ['gemBoxInfo'] );
		// 检查能否开启宝箱如果可以得到请求宝箱的内容
		if ($boxid != $vip) {
			// 获得对应宝箱vip级别所需充值金额并返回
			$vipxx = cityModel::vipxx ( $playersid, $roleInfo ['userid'] );
			$_vipPrice = getVipPrice ();
			
			$returnValue ['status'] = 1002;
			// 请求级别费用-(下级费用-下级需要) = 到指定级别所需费用
			$returnValue ['je'] = $_vipPrice [$boxid] * RMB_TO_YUANBAO - ($_vipPrice [$vipxx ['vip'] + 1] * RMB_TO_YUANBAO - $vipxx ['xjcz']);
			
			return $returnValue;
		}
		
		// 1表示已开启过
		$gembox = $gemboxInfo['gemBoxInfo'];
		if($gembox[$boxid] == 1){
			$returnValue ['status'] = 21;
			$returnValue ['message'] = $city_lang['openGemBox_2'];
			return $returnValue;
		}
		
		$gemboxRule = getVipGemBoxInfo ();
		$requestBox = $gemboxRule [$boxid];
		
		$returnValue ['wlist'] = array ();
		
		if (count ( $requestBox ['tools'] ) > 0) {
			// 检查背包是否有足够空间，如果有更新数据
			$player = $G_PlayerMgr->GetPlayer($playersid);
			$addOK = $player->AddItems($requestBox['tools']);
			
			if(false === $addOK){
				$returnValue['status']  = 1001;
				$returnValue['message'] = $city_lang['openGemBox_3'];
				return $returnValue;
			}
			
			// 背包足够更新数据并构造返回数据
			foreach($requestBox['tools'] as $itemId=>$amount){
				$itemInfo = toolsModel::getItemInfo($itemId);
				$returnValue ['wlist'] [] = array ('mc' => $itemInfo ['Name'], 'sl' => $amount );
			}
			
			//$bagData = toolsModel::getBglist($djIdList, $playersid, $oldDjList);
			$bagData = $player->GetClientBag();
			$returnValue ['list'] = $bagData;
		}
		
		// 返回前端名称，数量的值
		$wlist = array ();
		
		// 更新获取后的玩家金融数据
		if ($requestBox ['tq'] > 0 || $requestBox ['jl'] > 0 || $requestBox ['yp'] > 0) {
			$updateRoleArray = array();
			// 更新铜钱
			if ($requestBox ['tq'] > 0) {
				$updateRoleArray['coins'] = $roleInfo ['coins'] + $requestBox ['tq'];
				if(($upLimit = $updateRoleArray['coins'] - COINSUPLIMIT) > 0){
					$updateRoleArray['coins'] = COINSUPLIMIT;
					$requestBox ['tq'] -= $upLimit;
				}
				$returnValue ['tq'] = $updateRoleArray['coins'];
				$returnValue ['hqtq'] = $requestBox ['tq'];
				$wlist [] = array ('mc' => $rw_lang['rw_getRWInfo_4'], 'sl' => $requestBox ['tq'] );
			}
			
			// 更新军粮
			if ($requestBox ['jl'] > 0) {
				$updateRoleArray['food'] = $roleInfo ['food'] + $requestBox ['jl'];
				$returnValue ['jl'] = floor ( $updateRoleArray['food'] );
				$returnValue ['hqjl'] = $requestBox ['jl'];
				$wlist [] = array ('mc' => $zyd_lang['scyd_2'], 'sl' => $requestBox ['jl'] );
			}
			
			// 更新银票
			if ($requestBox ['yp'] > 0) {
				$updateRoleArray['silver'] = $roleInfo ['silver'] += $requestBox ['yp'];
				if(($upLimit = $updateRoleArray['silver'] - SILVERUPLIMIT) > 0){
					$updateRoleArray['silver'] = SILVERUPLIMIT;
					$requestBox ['yp'] -= $upLimit;
				}
				$returnValue ['yp'] = floor ( $updateRoleArray ['silver'] );
				$returnValue ['hqyp'] = $requestBox ['yp'];
				$wlist [] = array ('mc' => $rw_lang['rw_getRWInfo_3'], 'sl' => $requestBox ['yp'] );
			}
			
			$updateRoleWhere ['playersid'] = $playersid;
			$common->updatetable ( 'player', $updateRoleArray, $updateRoleWhere );
			$roleInfo = array_merge($roleInfo, $updateRoleArray);
			$common->updateMemCache ( MC . $playersid, $roleInfo );
		}
		
		$returnValue ['status'] = 0;
		
		// 更新宝箱状态信息并返回开启内容的数据
		$gemboxInfo ['gemBoxInfo'] [$boxid] = 1;
		$whereArray = array ('playersid' => $playersid );
		$updateArray = array ('gemBoxInfo' => json_encode ( $gemboxInfo ['gemBoxInfo'] ), 'validay' => date ( 'Y-m-d' ) );
		$common->updateTableOnlyOneWithMem ( 'gembox_info', $updateArray, $whereArray );
		
		$returnValue ['lblx'] = intval ( $boxid );
		//完成引导脚本
		/*$xyd = guideScript::wcjb ( $roleInfo, 'kbx', 6, 6 );
		//接收新的引导
		$xyd = guideScript::xsjb ( $roleInfo, 'ydzs', 6 );
		if ($xyd !== false) {
			$returnValue ['ydts'] = $xyd;
		}*/
		return $returnValue;
	}
	
	//获取指定将领信息
	public static function hqdgwj($tuid, $gid = '*') {
		$getInfo ['playersid'] = $tuid;
		$ginfo = cityModel::getGeneralList ( $getInfo, 1, false, $gid, false );
		$value ['status'] = $ginfo ['status'];
		if ($value ['status'] == 0) {
			$value ['generals'] = $ginfo ['generals'];
		} else {
			$value['message'] = $ginfo['message'];
		}
		return $value;
	}
	
	/**
	 * 获取用户关系
	 *
	 * @param int $playersid
	 * @param int $tuid
	 * @return 0陌生人 1好友 2仇人 4邻居 其他复杂关系采用或操作获得：例如 好友+仇人3，邻居+仇人还是反2
	 */
	public static function getUserRelations($playersid, $tuid) {
		$neighborsId = socialModel::findRandFriend ( $playersid, 1, 0, true );
		$tempRelation = key_exists ( $tuid, $neighborsId ) ? 4 : 0;
		
		$friendsId = roleModel::getTableRoleFriendsInfo ( $playersid, 1, true );
		$tempRelation = key_exists ( $tuid, $friendsId ) ? $tempRelation | 1 : $tempRelation;
		
		$enemysId = roleModel::getTableRoleFriendsInfo ( $playersid, 3, true );
		$tempRelation = key_exists ( $tuid, $enemysId ) ? $tempRelation | 2 : $tempRelation;

		// 邻居+仇人还是反2
		if($tempRelation == 6){
			return 2;
		}
		
		return $tempRelation;
	}
	
	//获取技能升级信息
	public static function hqjnsjxx($playersid, $gid, $jnid) {
		global $city_lang, $sys_lang;
		$gInfo = cityModel::getGeneralData ( $playersid, false, $gid );
		if (! empty ( $gInfo )) {
			$jn1 = $gInfo [0] ['jn1'];
			$jn1_level = $gInfo [0] ['jn1_level'];
			$jn2 = $gInfo [0] ['jn2'];
			$jn2_level = $gInfo [0] ['jn2_level'];
			if ($jnid == $jn1) {
				$xh = jnsj ( $jn1_level + 1 ); //升级技能所需消耗		
				$nextLV = $jn1_level + 1;
				$LV = $jn1_level;
			} elseif ($jnid == $jn2) {
				$xh = jnsj ( $jn2_level + 1 ); //升级技能所需消耗	
				$nextLV = $jn2_level + 1;
				$LV = $jn2_level;
			} else {
				$value = array ('status' => 3, 'message' => $city_lang['hqjnsjxx_1'] );
				return $value;
			}
			$value ['status'] = 0;
			$value ['yqtq'] = $xh ['tq']; //需要的铜钱
			$value ['cgl'] = $xh ['jl']; //成功几率
			$value ['yqyb'] = $xh ['yb']; //需要元宝
			$value ['yqsl'] = $xh ['jns']; //消耗技能书数量
			$itemID = jndyid ( $jnid );
			$myItem = toolsModel::getMyItemInfo ( $playersid );
			$jnAmArray = array ();
			if (! empty ( $myItem )) {
				foreach ( $myItem as $itemValue ) {
					if ($itemValue ['ItemID'] == $itemID) {
						$jnAmArray [] = $itemValue ['EquipCount'];
					}
				}
			}
			$value ['dqsl'] = array_sum ( $jnAmArray );
			$jnkInfo = jnk ( $jnid );
			$value ['sm'] = $jnkInfo ['ms'];
			$value ['jnmc'] = $jnkInfo ['n'];
			$txcf = actModel::getJnJl ( $jnid, $LV );
			$txcf_next = actModel::getJnJl ( $jnid, $nextLV );
			$value ['dqjl'] = intval ( $txcf );
			$value ['xjjl'] = intval ( $txcf_next );
		} else {
			$value ['status'] = 21;
			$value ['message'] = $sys_lang[3];
		}
		return $value;
	}
	
	//判断对应玩家领地中是否有偷将可偷
	public static function is_tj($playersid) {
		$_occupysId = roleModel::getTableRoleFriendsInfo ( $playersid, 4, true );
		foreach ( $_occupysId as $pid => $level ) {
			$showValue = cityModel::getHireGeneralInfo ( $pid );
			$genValue = $showValue ['hirableGeneral'];
			if ($showValue ['status'] == 0 && count ( $showValue ['hirableGeneral'] ) > 0) {
				for($i = 0; $i < count ( $genValue ); $i ++) {
					$ys = actModel::hqwjpz ( $genValue [$i] ['tf'] ); //$genValue[$i]['tf']
					if ($ys > 0) {
						return 1;
					}
				}
			}
		}
		return 0;
	}
	
	/**
	 * 此人是否能被当前用户偷将，这个只能用于占领关系的检查
	 * 2012-1-5 修改
	 * 
	 * @param int $playersid		要偷将玩家id
	 * @param int $tuid				目标玩家id，如果是array就表示玩家的详细信息
	 * @return boolean              1 可偷将，0没将可偷
	 */
	public static function is_onetj($playersid, $tuid) {
		global $common, $db, $_SGLOBAL;
		// 初始化玩家信息和目标玩家ID
		$roleInfo = $tuid;
		if (! is_array ( $tuid )) {
			$roleInfo ['playersid'] = $tuid;
			roleModel::getRoleInfo ( $roleInfo );
		}
		$tuid = $roleInfo ['playersid'];
		// 判断是否可以偷将，占领者可以偷将，好友可以偷将
		$is_occupier = $roleInfo ['is_defend'] == 0 && $roleInfo ['aggressor_playersid'] == $playersid && $roleInfo ['end_defend_time'] > $_SGLOBAL ['timestamp'];
		$is_friend = key_exists ( $playersid, roleModel::getTableRoleFriendsInfo ( $tuid, 1, true ) );
		if ($is_occupier || $is_friend) {
			$showValue = cityModel::getHireGeneralInfo ( $tuid );
			$genValue = $showValue ['hirableGeneral'];
			// 是否有将可偷
			if ($showValue ['status'] == 0 && count ( $showValue ['hirableGeneral'] ) > 0) {
				for($i = 0; $i < count ( $genValue ); $i ++) {
					// 是否有大于白将的可偷
					$ys = actModel::hqwjpz ( $genValue [$i] ['tf'] ); //$genValue[$i]['tf']
					if ($ys > 0) {
						return 1;
					}
				}
			}
		} else {
			return 0;
		}
	}
	
	//占领地是否有收益
	public static function is_zlsy($playersid) {
		global $common, $db, $_SGLOBAL;
		$ginfo = cityModel::getGeneralData($playersid,false,'*',true);
		$value = 0;
		if (!empty($ginfo)) {
			foreach ($ginfo as $ginfoValue) {
				if ($ginfoValue['occupied_playersid'] != $playersid && $ginfoValue['occupied_end_time'] > $_SGLOBAL ['timestamp'] && ($_SGLOBAL ['timestamp'] - $ginfoValue ['last_income_time']) >= 7200) {
					$value = 1;
					break;
				}
			}
		}
		/*$result = $db->query ( "SELECT * FROM " . $common->tname ( 'playergeneral' ) . " WHERE playerid = '" . $playersid . "' and playerid <> occupied_playersid and occupied_end_time > '" . $_SGLOBAL ['timestamp'] . "'" );
		while ( $rows = $db->fetch_array ( $result ) ) {
			if (($_SGLOBAL ['timestamp'] - $rows ['last_income_time']) >= 7200) {
				return 1;
			}
		}*/
		if ($value == 1) {
			return 1;
		} else {
			return 0;
		}
	}
	
	//是否能征别人的税
	public static function is_zs($playersid) {
		global $common, $db, $_SGLOBAL;
		$result = $db->query ( "SELECT * FROM " . $common->tname ( 'player' ) . " WHERE is_defend = 0 and aggressor_playersid = '" . $playersid . "' and end_defend_time > '" . $_SGLOBAL ['timestamp'] . "'" );
		while ( $rows = $db->fetch_array ( $result ) ) {
			if (cityModel::is_Collection ( $rows ['last_zl_collect_time'] ) == 1) {
				return 1;
			}
		}
		return 0;
	}
	
	//此人是否能被当前用户征税
	public static function is_onezs($playersid, $tuid) {
		global $common, $db, $_SGLOBAL;
		if (cityModel::is_ZCollection ( $tuid ) == 1) {
			return 1;
		} else {
			return 0;
		}
	}
	
	// 使用侦查功能
	public static function zc($playersid, $tuid) {
		global $common, $db, $_SGLOBAL, $city_lang, $sys_lang;
		
		// 消耗银票
		$xyyp = 5;
		
		// 获取玩家信息
		$roleInfo = array ('playersid' => $playersid );
		$roleRes = roleModel::getRoleInfo ( $roleInfo, false );
		if (empty ( $roleRes )) {
			$value = array ('status' => 3, 'message' => $sys_lang[1] );
			return $value;
		}
		$currYp = $roleInfo ['silver'];
		$syyp = $currYp - $xyyp;
		
		// 银票不足
		if ($syyp < 0) {
			$returnValue ['status'] = 68;
			$returnValue ['xyxhyp'] = $xyyp;
			$arr1 = array('{xhyp}','{yp}');
			$arr2 = array($xyyp,$currYp);
			$returnValue ['message'] = str_replace($arr1,$arr2,$sys_lang[6]);
			//$returnValue['sl'] = abs($syyp);			
			return $returnValue;
		} else {
			// 获取对方玩家防守策略
			$opposite_roleInfo = array ('playersid' => $tuid );
			$result = roleModel::getRoleInfo ( $opposite_roleInfo, false );
			if (! empty ( $result )) {
				// 0 未设置防守策略 1固守城池 2偷营劫寨 3空城之计			
				$fscl = $opposite_roleInfo ['strategy'];
				
				// 更新玩家信息
				$updateRole ['silver'] = $syyp;
				$whereRole ['playersid'] = $playersid;
				$common->updatetable ( 'player', $updateRole, $whereRole );
				$common->updateMemCache ( MC . $playersid, $updateRole );
				
				// 返回值
				$returnValue ['status'] = 0;
				$returnValue ['fscl'] = $fscl;
				$returnValue ['yp'] = intval ( $syyp );
				$returnValue ['xhyp'] = intval ( $xyyp );
				
				return $returnValue;
			} else {
				return array ('status' => 3, 'message' => $sys_lang[1] );
			}
		}
	}
	
	//查看技能信息
	public static function ckjnxx($jndj, $jnid) {
		$jnInfo = jnk ( $jnid, $jndj );
		$value ['status'] = 0;
		$value ['jntz'] = 0;
		$value ['jnms'] = $jnInfo ['xxms'];
		$value ['dqxg'] = $jnInfo ['djxx'];
		$jnInfoNext = jnk ( $jnid, $jndj + 1 );
		$value ['xjxg'] = $jnInfoNext ['djxx'];
		return $value;
	}
	
	//写留言
	public static function wjly($msg, $tuid, $playersid, $lylx = 0) {
		global $mc, $db, $common, $_SGLOBAL, $city_lang;
		//$ysmsg = $msg;
		//$mcfh = "@|#|\\$|%|\\*|^|&|(|)|_|\\+|~|￥|……| |\\|";
		//$checkmsg = preg_replace ( "/$mcfh/i", "", $msg );	
		/*$msg = ereg_replace("\\r\\n","",$msg);
		$msg = ereg_replace("\\r","",$msg);
		$msg = ereg_replace("\\n","",$msg);
		$msg = ereg_replace("\\t","",$msg);*/					
		$checkmsg = roleModel::removeTag($msg);
		$msgLen = cityModel::sstrlen ( $msg );
		if ($msgLen > 180 || $msgLen == 0) {
			$value = array ('status' => 1002, 'nr' => $msg );
		} else {
			if ($playersid != $tuid) {
				$blistSql = "SELECT COUNT(intID) as sl FROM ".$common->tname('city_msg_blacklist')." WHERE from_pid = $tuid && to_pid = $playersid LIMIT 1";				
				$bres = $db->query($blistSql);
				$brows = $db->fetch_array($bres);
				if ($brows['sl'] > 0) {
					if ($lylx == 1) {
						$msgErr = $city_lang['wjly_1'];
					} else {
						$msgErr = $city_lang['wjly_2'];
					}
					return array('status'=>30,'message'=>$msgErr);
				}
			}
			$roleInfo ['playersid'] = $playersid;
			roleModel::getRoleInfo ( $roleInfo );
			$nc = $roleInfo ['nickname'];
			$lysl = 0;
			$oldMsg = cityModel::hqlyxx ( $tuid,$lysl );
			///if (! ($glarray = $mc->get ( MC . 'mgc' ))) {
			include (S_ROOT . 'lang_' . LANG_FLAG . DIRECTORY_SEPARATOR . 'mgc.php');

			$glarray = mgc ();
				//$mc->set ( MC . 'mgc', $glarray, 0, 0 );
			//}
			//$glarrayValue = implode('|',$glarray);
			$stop = 0;
			if ($msgLen > 1) {
				foreach ( $glarray as $glarrayValue ) {
					if (preg_match ( "/$glarrayValue/i", $checkmsg )) {
						$stop = 1;
						break;
					}
				}
			}
			if ($stop == 1) {
				/*foreach ( $glarray as $glarrayV ) {
					$msg = preg_replace ( "/$glarrayV/i", "***", $msg );
				}*/
				$value ['status'] = 1001;
				$value ['nr'] = $msg;
				$glarray = NULL;
				$glarrayValue = NULL;
				$glarrayV = NULL;
				return $value;
			}
			$glarray = NULL;
			//$msg  = mysql_escape_string($msg);	
			if (empty ( $oldMsg )) {
				$msgInfo = array (array ('msg' => $msg, 't' => intval ( $_SGLOBAL ['timestamp'] ), 'pid' => $playersid, 'nc' => $nc ) );
			} else {
				if (count ( $oldMsg ) > 49) {
					array_pop ( $oldMsg );
				}
				$msgInfo = array_merge ( array (array ('msg' => $msg, 't' => intval ( $_SGLOBAL ['timestamp'] ), 'pid' => $playersid, 'nc' => $nc ) ), $oldMsg );
			}
			$updateMsg = $updateMemMsg = serialize ( $msgInfo );
			$updateMsg = mysql_escape_string ( $updateMsg );
			$mc->set ( MC . $tuid . '_cityMsg', $updateMemMsg, 0, 604800 );
			//$db->query ( "UPDATE " . $common->tname ( 'city_msg' ) . " SET `msg`='$updateMsg' WHERE playersid = '$tuid' LIMIT 1" );
			
			$value ['status'] = 0;
			$value ['ym'] = 1;
			$value ['ys'] = ceil ( count ( $msgInfo ) / 2 ); //总页数
			$todayZero = strtotime ( date ( 'Y-m-d 0:0:0' ) );
			$showMsg = array ();
			for($i = 0; $i < count ( $msgInfo ); $i ++) {
				if ($i > 1) {
					break;
				}
				$rq = $msgInfo [$i] ['t'] > $todayZero ? 0 : ceil ( ($todayZero - $msgInfo [$i] ['t']) / (3600 * 24) );
				$time = date ( 'H:i', $msgInfo [$i] ['t'] );
				$showMsg [] = array ('pid' => intval ( $msgInfo [$i] ['pid'] ), 'nc' => $msgInfo [$i] ['nc'], 'nr' => stripcslashes ( $msgInfo [$i] ['msg'] ), 'rq' => $rq, 'time' => $time );
				$rq = NULL;
				$time = NULL;
			}
			$value ['ly'] = $showMsg;
			//$mc->set(MC.$tuid.'_lyzt',1,0,604800);
			//$lysl = cityModel::hqlysl ( $tuid );
			if ($lysl > 49) {
				$lysl = 49;
			}
			$xsl = $lysl + 1;
			$mc->set ( MC . $tuid . '_wjlysl', $xsl, 0, 3600 );
			$common->updatetable('city_msg',"msg='$updateMsg',newmsg = '$xsl'","playersid = '$tuid' LIMIT 1");
			if ($tuid != $playersid) {
				$rwid = questsController::OnFinish($roleInfo,"'wjlycs'");  //玩家留言任务
			    if (!empty($rwid)) {
			         $value['rwid'] = $rwid;				             
			    }
			}			
		}
		return $value;
	}
	
	//获取留言板信息
	public static function hqlyxx($tuid,&$newmsg = 0) {
		global $mc, $db, $common, $_SGLOBAL;
		$msg = $mc->get ( MC . $tuid . '_cityMsg' );
		if (empty ( $msg )) {
			$sql = "SELECT * FROM " . $common->tname ( 'city_msg' ) . " WHERE playersid = '$tuid' LIMIT 1";
			$result = $db->query ( $sql );
			$msgReturn = array ();
			$rows = $db->fetch_array ( $result );
			$newmsg = $rows['newmsg'];
			if (! empty ( $rows ['msg'] )) {
				$msgInfoArray = unserialize ( $rows ['msg'] );
				foreach ( $msgInfoArray as $msgKey => $msgInfo ) {
					if (($msgInfo ['t'] + 604800) > $_SGLOBAL ['timestamp']) {
						$msgReturn [] = array ('msg' => $msgInfo ['msg'], 't' => $msgInfo ['t'], 'pid' => $msgInfo ['pid'], 'nc' => $msgInfo ['nc'], 'lyid' => $msgKey);
					}
				}				
			}			
			if (! empty ( $msgReturn )) {
				$updateMemMsg = serialize ( $msgReturn );
				$mc->set ( MC . $tuid . '_cityMsg', $updateMemMsg, 0, 604800 );
			}
			return $msgReturn;
		} else {
			$msgInfoArray = unserialize ( $msg );
			$msgReturn = array ();
			foreach ( $msgInfoArray as $msgKey => $msgInfo ) {
				if (($msgInfo ['t'] + 604800) > $_SGLOBAL ['timestamp']) {
					$msgReturn [] = array ('msg' => $msgInfo ['msg'], 't' => $msgInfo ['t'], 'pid' => $msgInfo ['pid'], 'nc' => $msgInfo ['nc'], 'lyid' => $msgKey );
				}
			}
			$newmsg = cityModel::hqlysl($tuid);
			return $msgReturn;
		}
	}
	
	//显示留言板信息
	public static function hqly($page, $tuid) {
		global $mc, $common, $db;	
		if ($_SESSION['playersid'] == $tuid) {
			$allow = 1;
		} else {			
			$allow = 0;
		}	
		$msgInfo = cityModel::hqlyxx ( $tuid );
		$total = count ( $msgInfo );
		$value ['status'] = 0;
		$value ['ys'] = ceil ( $total / 2 );
		if ($page > $value ['ys']) {
			$page = $value ['ys'];
		}
		if (empty ( $page ) || $page < 0) {
			$page = 1;
		}
		$value ['ym'] = $page;
		$start = ($page - 1) * 2;
		$end = $start + 1;
		$todayZero = strtotime ( date ( 'Y-m-d 0:0:0' ) );
		$showMsg = array ();
		$pidArray = array();
		$blackList = array();
		if ($allow == 1) {
			for($i = $start; $i < $total; $i ++) {
				if ($i > $end) {
					break;
				}
				$pidArray[] = $msgInfo [$i] ['pid'];
			}			
			if (!empty($pidArray)) {
				$topids = implode(',',$pidArray);
				$sql = "SELECT * FROM ".$common->tname('city_msg_blacklist')." WHERE from_pid in ($topids) && to_pid = $tuid";
				$result = $db->query($sql);
				while ($rows = $db->fetch_array($result)) {
					$blackList[] = $rows['from_pid'];
				}
			}
		}		
		for($i = $start; $i < $total; $i ++) {
			if ($i > $end) {
				break;
			}
			if (!empty($blackList)) {
				if (in_array($msgInfo [$i] ['pid'],$blackList)) {
					$pblx = 1;
				} else {
					$pblx = 0;
				}
			} else {
				$pblx = 0;
			}
			$rq = $msgInfo [$i] ['t'] > $todayZero ? 0 : ceil ( ($todayZero - $msgInfo [$i] ['t']) / (3600 * 24) );
			$time = date ( 'H:i', $msgInfo [$i] ['t'] );
			$showMsg [] = array ('pid' => intval ( $msgInfo [$i] ['pid'] ), 'nc' => $msgInfo [$i] ['nc'], 'nr' => stripcslashes ( $msgInfo [$i] ['msg'] ), 'rq' => $rq, 'time' => $time, 'lyid' => intval ( $msgInfo [$i] ['lyid'] ), 'pblx' => $pblx  );
			$rq = NULL;
			$time = NULL;
		}
		$value ['ly'] = $showMsg;
		//$mc->delete(MC.$tuid.'_lyzt');
		$lysl = $mc->get(MC . $tuid . '_wjlysl');
		if ($_SESSION['playersid'] == $tuid && $lysl > 0) {
			$xsl = 0;
			$mc->set ( MC . $tuid . '_wjlysl', $xsl, 0, 3600 );
			$updateLysl ['newmsg'] = $xsl;
			$updateLyslWhere ['playersid'] = $tuid;
			$common->updatetable ( 'city_msg', $updateLysl, $updateLyslWhere );
		}
		return $value;
	}
	
	//删除留言
	public static function scly($playersid, $lx, $tuid, $page) {
		global $mc, $common, $db, $G_PlayerMgr, $city_lang;
		$msg = $mc->get ( MC . $playersid . '_cityMsg' );
		if (empty ( $msg )) {
			$sql = "SELECT * FROM " . $common->tname ( 'city_msg' ) . " WHERE playersid = '$playersid' LIMIT 1";
			$result = $db->query ( $sql );
			$msgReturn = array ();
			$rows = $db->fetch_array ( $result );
			$newmsg = $rows['newmsg'];
			if (! empty ( $rows ['msg'] )) {
				$msgInfo = unserialize ( $rows ['msg'] );				
			}	
		} else {
			$msgInfo = unserialize ( $msg );
		}
		if (empty($msgInfo)) {
			return array('status' => 30, 'message' => $city_lang['scly_1']);
		} 
		if ($lx == 0) {
			unset($msgInfo[$tuid]);			
		} elseif ($lx == 1 || $lx == 3 ) {
			foreach ($msgInfo as $key => $msgInfoArrayValue) {
				if ($msgInfoArrayValue['pid'] == $tuid) {
					unset($msgInfo[$key]);
				}
			}
			if ($lx == 3) {
				$common->inserttable('city_msg_blacklist',array('from_pid'=>$playersid,'to_pid'=>$tuid));
			}
		} else {
			$mc->delete ( MC . $playersid . '_cityMsg' );
			$common->updatetable('city_msg',"msg=''","playersid = '$playersid' LIMIT 1");
			return array('status'=>0,'ly'=>array());
		}
		$updateMsg = $updateMemMsg = serialize ( $msgInfo );
		$updateMsg = mysql_escape_string ( $updateMsg );
		$mc->set ( MC . $playersid . '_cityMsg', $updateMemMsg, 0, 604800 );
		$common->updatetable('city_msg',"msg='$updateMsg'","playersid = '$playersid' LIMIT 1");	
		$lyInfo = cityModel::hqly($page, $playersid);
		$value ['ly'] = $lyInfo['ly'];
		$value ['ym'] = $lyInfo['ym'];	
		$value ['ys'] = $lyInfo['ys'];			
		$value['status'] = 0;
		return $value;
	}
	
	//获取屏蔽留言玩家列表
	public static function hqlypb($playersid, $page) {
		global $mc, $common, $db, $city_lang;
		$sql = "SELECT count(intID) as sl FROM ".$common->tname('city_msg_blacklist')." WHERE from_pid = $playersid";
		$result = $db->query($sql);
		$rows = $db->fetch_array($result);
		$sl = $rows['sl'];
		if ($sl > 0) {
			if (empty ( $page ) || $page < 0) {
				$page = 1;
			}
			$perPage = 6;
			$value ['ym'] = $page;
			if ($page > $sl && $page > 1) {
				$page = $sl;
			}
			$start = ($page - 1) * $perPage;	
			$dataSql = "SELECT * FROM ".$common->tname('city_msg_blacklist')." WHERE from_pid = $playersid ORDER BY intID DESC LIMIT $start,$perPage";
			$dataRes = $db->query($dataSql); 
			while ($dataRows = $db->fetch_array($dataRes)) {
				//$data[] = array('pid' => intval($dataRows['to_pid']),'nc'=>$dataRows['to_nickname'],'vip'=>1,'level'=>1);
				$data[] = $dataRows['to_pid']; 
			}
			$pInfo = roleModel::getAllRolesInfo($data);
			foreach ($pInfo as $pInfoValue) {
				$rData[] = array('pid' => intval($pInfoValue['playersid']),'nc'=>$pInfoValue['nickname'],'vip'=>intval($pInfoValue['vip']),'level'=>intval($pInfoValue['player_level']),);
			}
			$value['ly'] = $rData;
			$value['status'] = 0;
			$value['ys'] = ceil($sl/$perPage);
			
		/*$list = array();
		$players= $G_PlayerMgr->GetPlayers($playidsList);
		foreach ( $players as $player ) {
			$list[] = &$player->baseinfo_;
		}
		
		return $list;	*/		
			return $value;		
		} else {
			return array('status'=>0,'ly'=>array(),'ys'=>1,'ym'=>1);
		}
	}
	
	//取消屏蔽
	public static function qxlypb($playersid,$lx,$tuid,$page) {
		global $mc, $common, $db, $city_lang;
		if ($lx == 1) {
			$db->query("DELETE FROM ".$common->tname('city_msg_blacklist')." WHERE from_pid = $playersid");
			return array('status'=>0,'ly'=>array(),'ys'=>1,'ym'=>1);
		} else {
			$db->query("DELETE FROM ".$common->tname('city_msg_blacklist')." WHERE from_pid = $playersid && to_pid = $tuid");
			$listInfo = cityModel::hqlypb($playersid, $page);
			return $listInfo;			
			//return array('status'=>0,'ly'=>array(),'ys'=>1,'ym'=>1);
		}
		
	}
	
	//检测字符长度
	public static function sstrlen($str, $charset = 'utf-8') {
		$n = 0;
		$p = 0;
		$c = '';
		$len = strlen ( $str );
		if ($charset == 'utf-8') {
			for($i = 0; $i < $len; $i ++) {
				$c = ord ( $str {$i} );
				if ($c > 252) {
					$p = 5;
				} elseif ($c > 248) {
					$p = 4;
				} elseif ($c > 240) {
					$p = 3;
				} elseif ($c > 224) {
					$p = 2;
				} elseif ($c > 192) {
					$p = 1;
				} else {
					$p = 0;
				}
				$i += $p;
				$n ++;
			}
		} else {
			for($i = 0; $i < $len; $i ++) {
				$c = ord ( $str {$i} );
				if ($c > 127) {
					$p = 1;
				} else {
					$p = 0;
				}
				$i += $p;
				$n ++;
			}
		}
		return $n;
	}
	//获取新留言数量
	public static function hqlysl($tuid) {
		global $mc, $db, $common;
		if (! ($lysl = $mc->get ( MC . $tuid . '_wjlysl' ))) {
			$sql = "SELECT newmsg FROM " . $common->tname ( 'city_msg' ) . " WHERE playersid = '$tuid' LIMIT 1";
			$result = $db->query ( $sql );
			$rows = $db->fetch_array ( $result );
			$lysl = $rows ['newmsg'];
			$mc->set ( MC . $tuid . '_wjlysl', $lysl, 0, 3600 );
		}
		return $lysl;
	}
	
	//探宝信息
	public static function tbxx($playersid) {
		global $common, $_SGLOBAL, $sys_lang;
		$roleInfo ['playersid'] = $playersid;
		if (! roleModel::getRoleInfo ( $roleInfo ))
			return array ('status' => 3, 'message' => $sys_lang[1] );
		
		$current_time = $_SGLOBAL ['timestamp'];
		$last_time = $roleInfo ['tb_sjrq']; //上次探宝时间
		$tb_cs = intval($roleInfo ['tb_cs']); //探宝次数
		$tb_gk = intval($roleInfo ['tb_gk']); //探宝关卡
				
		$updateRole = array ();
		if ($tb_cs > 0 && date ( "Ymd", $current_time ) != date ( "Ymd", $last_time )) {
			$tb_cs = $updateRole ['tb_cs'] = 0;
			$tb_gk = $updateRole ['tb_gk'] = 1;
			$updateRole ['tb_sjrq'] = $current_time;
		}
		
		if (! empty ( $updateRole )) {
			$updateRoleWhere ['playersid'] = $playersid;
			$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
			$common->updateMemCache ( MC . $playersid, $updateRole );
		}
		
		$kgc_count = toolsModel::getItemCount($roleInfo ['playersid'], KGC_ITEMID); // 矿工锄的数量
		$kgc_cost = $tb_cs < 5 ? 1 : 2;
		
		$num = array (1=>1, 2=>2, 3=>5, 4=>10, 5=>15, 6=>25);		
		
		return array ('status' => 0, 'gk' => $tb_gk, 'gczs' => $kgc_count, 'gchf' => $kgc_cost, 'dsl' => $num[$tb_gk], 'xsl' => $num[$tb_gk + 1]);
	}
	
	//探宝
	public static function tb($playersid) {
		global $common, $_SGLOBAL, $G_PlayerMgr, $sys_lang, $city_lang;
		$roleInfo ['playersid'] = $playersid;
    	$player = $G_PlayerMgr->GetPlayer($playersid);
		if (! $player)
			return array ('status' => 3, 'message' => $sys_lang[1] );
			
		if(toolsModel::getItemCount($roleInfo ['playersid'], KGC_ITEMID) <= 0) 
			return array ('status' => 998, 'message' => $city_lang['tbgkmc_8']);

		if(toolsModel::getBgSyGs($playersid) == 0)
			return array ('status' => 998, 'message' => $city_lang['tb_full']);
      
    	$roleInfo = $player->GetBaseInfo();
		
		$current_time = $_SGLOBAL ['timestamp'];
		$last_time = $roleInfo ['tb_sjrq']; //上次探宝时间
		$tb_cs = $roleInfo ['tb_cs']; //探宝次数
		$tb_gk = $roleInfo ['tb_gk']; //探宝关卡
		$updateRole = array ();
		if ($tb_cs > 0 && date ( "Ymd", $current_time ) != date ( "Ymd", $last_time )) {
			$tb_cs = $updateRole ['tb_cs'] = 0;
			$tb_gk = $updateRole ['tb_gk'] = 1;
		}
		
		if ($tb_gk > 5 || $tb_gk < 1)
			$tb_gk = $updateRole ['tb_gk'] = 1;
				
		/*$ybhf = $tb_gk * 10;

		if ($roleInfo ['ingot'] < $ybhf) {
			$arr1 = array('{xhyb}','{yb}');
			$arr2 = array($ybhf,$roleInfo ['ingot']);
			return array ('status' => 88, 'message' => str_replace($arr1,$arr2,$sys_lang[2]), 'yb' => $roleInfo ['ingot'] );
		}*/		
		$kgc_cost = $tb_cs < 5 ? 1 : 2;
		$qstbcs = $kgc_cost == 2 ? 0 : 5 - ($tb_cs + 1);

		$jg = 1;
		$num = 0;
		$num1 = array (1=>1, 2=>2, 3=>5, 4=>10, 5=>15, 6=>25);		
		$rnd = array (1=>50, 2=>50, 3=>40, 4=>35, 5=>20);
		
		if (mt_rand ( 0, 99 ) > $rnd [$tb_gk]) {
			$num = $num1 [$tb_gk];
			if($tb_gk > 1)
				$tb_gk = $updateRole ['tb_gk'] = 1;
		} else {
			if($tb_gk >= 5) {
				$num = $num1[6];
				$tb_gk = $updateRole ['tb_gk'] = 1;
			} else {
				$tb_gk = $updateRole ['tb_gk'] = $tb_gk + 1;
			}
			$jg = 2;
		}
				
		//10001金刚石
    	$addRes = $player->AddItems(array(10001=>$num), false, array(KGC_ITEMID=>$kgc_cost));
		if (false === $addRes)  return array ('status' => 1001); // 背包已满

		$updateRole ['tb_cs'] = $tb_cs + 1;
		//$updateRole ['ingot'] = $roleInfo ['ingot'] - $ybhf;
		$updateRole ['tb_sjrq'] = $current_time;
		
		$updateRoleWhere ['playersid'] = $playersid;
		$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
		$common->updateMemCache ( MC . $playersid, $updateRole );
		if (!empty($updateRole ['tb_gk'])) {
			$roleInfo['hdyp'] = $updateRole ['tb_gk'];
			$rwid = questsController::OnFinish ( $roleInfo, "'tb'" );				
			if (!empty($rwid)) {
				$value['rwid'] = $rwid;
			} else {
				$value = array();
			}
		} else {
			$value = array();
		}
		
		$bagData = $player->GetClientBag();
		$kgc_count = toolsModel::getItemCount($roleInfo ['playersid'], KGC_ITEMID); // 矿工锄的数量
		
		return array ('status' => 0, 'jg' => $jg, 'hd' => $num, 'xhgc' => $kgc_cost, 'kgcs' => $kgc_count, 'cs'=> $qstbcs, 'list' => $bagData) + $value;
	}
	
	// 探宝见好就收
	public static function tbjhjs($playersid) {
		global $common, $_SGLOBAL, $G_PlayerMgr, $sys_lang, $city_lang;
		$roleInfo ['playersid'] = $playersid;
    	$player = $G_PlayerMgr->GetPlayer($playersid);
		if (! $player)
			return array ('status' => 3, 'message' => $sys_lang[1] );
		
		if(toolsModel::getBgSyGs($playersid) == 0)
			return array ('status' => 998, 'message' => $city_lang['tb_jhjs_full']);
		      
    	$roleInfo = $player->GetBaseInfo();
		
		$current_time = $_SGLOBAL ['timestamp'];
		$last_time = $roleInfo ['tb_sjrq']; //上次探宝时间
		$tb_cs = $roleInfo ['tb_cs']; //探宝次数
		$tb_gk = $roleInfo ['tb_gk']; //探宝关卡
		$updateRole = array ();
		$updateRole['tb_gk'] = 1;
		if ($tb_cs > 0 && date ( "Ymd", $current_time ) != date ( "Ymd", $last_time )) {
			$tb_cs = $updateRole['tb_cs'] = 0;
			$tb_gk = $updateRole['tb_gk'] = 1;
		}
		
		if ($tb_gk > 5 || $tb_gk < 1)
			$tb_gk = $updateRole['tb_gk'] = 1;
				
		if($tb_gk <= 1) return array ('status' => 998, 'message' => $city_lang['tbgkmc_9']);
			
		$num = 0;
		$num1 = array(1=>1, 2=>2, 3=>5, 4=>10, 5=>15, 6=>25);		
		$num = $num1[$tb_gk];
						
		//10001金刚石
    	$addRes = $player->AddItems(array(10001=>$num));
		if (false === $addRes)  return array ('status' => 1001); // 背包已满
				
		$updateRoleWhere ['playersid'] = $playersid;
		$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
		$common->updateMemCache ( MC . $playersid, $updateRole );
		
		$bagData = $player->GetClientBag();
				
		return array ('status' => 0, 'hd' => $num, 'list' => $bagData);
	}
	
	//探宝关卡名称
	private static function tbgkmc($i) {
		global $city_lang;
		switch ($i) {
			case 1 :
				return $city_lang['tbgkmc_1'];
				break;
			case 2 :
				return $city_lang['tbgkmc_2'];
				break;
			case 3 :
				return $city_lang['tbgkmc_3'];
				break;
			case 4 :
				return $city_lang['tbgkmc_4'];
				break;
			case 5 :
				return $city_lang['tbgkmc_5'];
				break;
			case 6 :
				return $city_lang['tbgkmc_6'];
				break;
			default :
				return $city_lang['tbgkmc_7'];
		}
	}
	
	//添加在线玩家数据
	public static function saveOnlieData($pid) {
		global $common,$_SGLOBAL;
		$common->inserttable('online',array('playersid'=>$pid,'updateTime'=>$_SGLOBAL['timestamp']),0,1);
	}
	
	// 闪游用户充值扣费请求
	public static function czcgjyb($playersid) {
		global $mc, $common, $db;
		
		$payServer = array(0=>array('server'=>'10.144.130.154', 'port'=>'12004'), 1=>array('server'=>'10.144.131.26', 'port'=>'12004'));
		$appid = 9;
		/* $payServer = array(0=>array('server'=>'221.130.15.242', 'port'=>'16000'), 1=>array('server'=>'221.130.15.242', 'port'=>'16000'));
		$appid = 6; */
		
		$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);
		
		$username = $roleInfo['ucid'];
		
		$seqnum = 'QJSH' . date('ymdHis') . rand(1, 10000);
		$token = $mc->get(MC.$username.'_token');
		$openid = $mc->get(MC.$username.'_openid');
		$pro = "{'seqnum':'$seqnum', 'func':'qcoins', 'token':'$token'}";
		
		$servernum = rand(0, 1);
		$serverIp = $payServer[$servernum]['server'];
		$serverPort = $payServer[$servernum]['port'];
		
		$fp = fsockopen($serverIp, $serverPort, $errno, $errstr, 1000);
		$header = "GET /appid=$appid HTTP/1.1\r\n";
		$header .= "Host:localhost \r\n";
		$header .= "Content-Length: ".strlen($pro)."\r\n";
		$header .= "Content-Type: text/html \r\n";
		$header .= "X-TOKEN: $token\r\n";
		$header .= "Connection: Close\r\n\r\n";
		$header .= $pro."\r\n";
		socket_set_timeout($fp, 1);
		fwrite($fp, $header);
		
		$str = null;
		$startTime = microtime(true);
		while(!feof($fp))
		{
			$str .= fgets($fp, 1024);
		
			$endTime = microtime(true);
			if(($endTime - $startTime) * 1000 > 800) {
				$startPos = strrpos($str, "Content-Length");
				$len = strlen('Content-Length: ');
				$endPos = strrpos($str, "Content-Type");
				$len = substr($str, $startPos + $len, $endPos - ($startPos + $len));
				$cStartPos = strrpos($str, "{");
				$cEndPos = $endPos = strrpos($str, "}");
				$finalLen = strlen(substr($str, $cStartPos, $cEndPos - $cStartPos + 1));
				if($len == $finalLen) break;
			}
		}
		fclose($fp);
		$str = str_replace('\r\n\r\n', '', stristr($str, "{ "));
		$result = json_decode($str);
		if(is_object($result)) {
			if($result->desc == 'success') { // 查询余额成功
				$coins = $result->balance;
				if($coins > 0) { // 余额大于0			
					$pro = "{'seqnum':'$seqnum', 'func':'ppay', 'token':'$token','propid':'1', 'price':'$coins', 'num':'1' }";
					$servernum = rand(0, 1);
					$serverIp = $payServer[$servernum]['server'];
					$serverPort = $payServer[$servernum]['port'];
					
					$fp = fsockopen($serverIp, $serverPort, $errno, $errstr, 1000);
					$header = "GET /appid=$appid HTTP/1.1\r\n";
					$header .= "Host:localhost \r\n";
					$header .= "Content-Length: ".strlen($pro)."\r\n";
					$header .= "Content-Type: text/html \r\n";
					$header .= "X-TOKEN: $token\r\n";
					$header .= "Connection: Close\r\n\r\n";
					$header .= $pro."\r\n";
					socket_set_timeout($fp, 1);
					fwrite($fp, $header);
					
					$str = null;
					$startTime = microtime(true);
					while(!feof($fp))
					{
						$str .= fgets($fp, 1024);
					
						$endTime = microtime(true);
						if(($endTime - $startTime) * 1000 > 800) {
							$startPos = strrpos($str, "Content-Length");
							$len = strlen('Content-Length: ');
							$endPos = strrpos($str, "Content-Type");
							$len = substr($str, $startPos + $len, $endPos - ($startPos + $len));
							$cStartPos = strrpos($str, "{");
							$cEndPos = $endPos = strrpos($str, "}");
							$finalLen = strlen(substr($str, $cStartPos, $cEndPos - $cStartPos + 1));
							if($len == $finalLen) break;
						}
					}
					fclose($fp);
					$str = str_replace('\r\n\r\n', '', stristr($str, "{ "));
					$result1 = json_decode($str);
					if(is_object($result1)) {
						if($result1->desc == 'success') {
							// 记录闪游返回的订单信息 { "code": "1", "desc": "success", "orderid": "ZK2012041215164008130000001", "seqnum": "QJSH1204121516407191" }
							$log_content = date('Y-m-d H:i:s') . '|' . $seqnum . '|' . '9' . '|' . '786' . '|' . $openid . '|' . '1' . '|' . $coins . '|' . '1' . '|' . $serverIp;
							$log_content = sprintf("%s \r\n", $log_content);
							file_put_contents(dirname(dirname(dirname(dirname(__FILE__)))) . '/' .SYPAYLOG. '/QJSH'. date('Y') . '_' . date('m') . '_' . date('d') . 'ppay.log', $log_content, FILE_APPEND);
						
							// 获取玩家现有元宝数量
							$res = $db->query ( "SELECT ingot FROM " . $common->tname ( 'player' ) . "  WHERE playersid = '" . $playersid . "' LIMIT 1" );
							$rows = $db->fetch_array ( $res );
						
							$oderNo = $result1->orderid;
							$addIngot = $coins;
							$updateRole['ingot'] = intval($rows['ingot']) + $addIngot;
							$whereRole['playersid'] = $playersid;
							$common->updatetable('player', $updateRole, $whereRole);
							$common->updateMemCache(MC.$playersid, $updateRole);
								
							// vip
							vipChongzhi($playersid, intval($coins)/10, $addIngot, $oderNo);   // 添加最后的参数
						
							$log_content1 = date('Y-m-d H:i:s') . '|' . $seqnum . '|' . '9' . '|' . '786' . '|' . $openid . '|' . '1' . '|' . $coins . '|' . '1' . '|' . $oderNo;
							$log_content1 = sprintf("%s \r\n", $log_content1);
							file_put_contents(dirname(dirname(dirname(dirname(__FILE__)))) . '/' .SYPAYLOG. '/QJSH'. date('Y') . '_' . date('m') . '_' . date('d') . 'dgoods.log', $log_content1, FILE_APPEND);
							
							$returnValue['status'] = 0;
							$returnValue['yb'] = $updateRole['ingot'];
						} else { // 扣费失败
							$returnValue['status'] = 1003;
						}
					} else {
						$returnValue['status'] = 1003;
					}
				} else { // 余额不足
					$returnValue['status'] = 1001;
				}
			} else { // 查询余额失败
				$returnValue['status'] = 1002;
			}
		} else { // 查询余额失败
				$returnValue['status'] = 1002;
		}
		
		return $returnValue;
	}
		
	// 发送聊天消息
	public static function fsltxx($nr, $playersid, $ltlx, $ym) {
		global $mc, $common, $db, $sys_lang, $city_lang;
		
		$nr = urldecode($nr);
		$checkmsg = roleModel::removeTag($nr);
		$checkmsg = $nr;
		$msgLen = cityModel::sstrlen($nr);
		
		include (S_ROOT . 'lang_' . LANG_FLAG . DIRECTORY_SEPARATOR . 'mgc.php');

		$glarray = mgc ();

		$stop = 0;
		if ($msgLen > 1) {
			foreach ( $glarray as $glarrayValue ) {
				if (preg_match ( "/$glarrayValue/i", $checkmsg )) {
					$stop = 1;
					break;
				}
			}
		}
		if ($stop == 1) {
			$value ['status'] = 1001;
			$value ['nr'] = $nr;
			$glarray = NULL;
			$glarrayValue = NULL;
			$glarrayV = NULL;
			return $value;
		}
		$glarray = NULL; 
		
		if($ltlx == 3) {
			$name = urldecode(_get('mc'));
			
			if(is_numeric($name)) {
				$name_find_ret = $db->query("SELECT count(nickname) as cnt, playersid as pid FROM ".$common->tname('player')." WHERE playersid = $name");
			} else {
				$name_find_ret = $db->query("SELECT count(nickname) as cnt, playersid as pid FROM ".$common->tname('player')." WHERE nickname = '$name'");
			}			
			$name_find_arr = $db->fetch_array($name_find_ret, MYSQL_ASSOC);
			
			if(intval($name_find_arr['cnt']) == 0)
				return array('lx'=>$ltlx, 'mc'=>$name, 'status'=>998, 'message'=>$city_lang['ltmsg1'][3]);
			
			// 不能与自己私聊
			if(intval($name_find_arr['cnt']) == 1 && $name_find_arr['pid'] == $playersid)
				return array('status'=>998, 'message'=>$city_lang['ltmsg1'][5]);			
				
			if(intval($name_find_arr['cnt']) > 1)
				return array('lx'=>$ltlx, 'mc'=>$name, 'status'=>1003);
									
			$ltlx = 2;
			$pid = $name_find_arr['pid'];
		} else if($ltlx == 2) {
			$pid = _get('pid');
		}
		
		// 修改私聊信息提示的标志
		if($ltlx == 2) {
			// 判断聊天对方是否屏蔽了自己
			if (!($disable = $mc->get(MC.$pid . '_chat_disble'))) {
				$disable_ret = $db->query("SELECT to_pid FROM ".$common->tname('chat_blacklist')." WHERE from_pid = $pid");
				$disable = $db->fetch_array($disable_ret, MYSQL_NUM);
				$disable = $disable[0];
				$mc->set(MC.$pid . '_chat_disble', $disable, 0, 3600);
			}
			
			if($disable != false) {
				if(strpos($disable, ',') !== false) {
					$disable_arr = explode(',', $disable);
				} else {
					$disable_arr[] = $disable;
				} 
			} else {
				$disable_arr = array();
			}
			
			if(in_array($playersid, $disable_arr) !== false) {
				return array('status'=>998, 'message'=>$city_lang['ltmsg1'][6]);
			}
									
			// 判断自己是否屏蔽了对方
			if (!($disable = $mc->get(MC.$playersid . '_chat_disble'))) {
				$disable_ret = $db->query("SELECT to_pid FROM ".$common->tname('chat_blacklist')." WHERE from_pid = $playersid");
				$disable = $db->fetch_array($disable_ret, MYSQL_NUM);
				$disable = $disable[0];
				$mc->set(MC.$playersid . '_chat_disble', $disable, 0, 3600);
			}
			
			if($disable != false) {
				if(strpos($disable, ',') !== false) {
					$disable_arr = explode(',', $disable);
				} else {
					$disable_arr[] = $disable;
				} 
			} else {
				$disable_arr = array();
			}
			
			if(in_array($pid, $disable_arr) !== false) {
				return array('status'=>998, 'message'=>$city_lang['ltmsg1'][2]);
			}
			
			$mc->set(MC.$pid.'_private-channel_newflag', 1, 0, 3600);
		}
		
		// 0世界频道 1工会频道 2私聊频道
		if($ltlx == 0) {
			$mem_key = 'Chat_World-channel';
		} else if($ltlx == 2) {
			$other_mem_key = $pid . '_Private-channel';
			$mem_key = $playersid . '_Private-channel';
		} else {
			$mem_key = 'Guild-channel'; 
		}
		
		if (!($worldChatInfo = $mc->get(MC.$mem_key))) {
			$worldChatInfo = '';
			$mc->set(MC.$mem_key, $worldChatInfo, 0, 3600);
		}
		
		// 获取玩家信息
		$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);
		$wjmc = $roleInfo['nickname'];
		
		if($worldChatInfo != '') {			
			$currCount = count($worldChatInfo);			
			if($currCount == TOTAL_WORLD_CHAT_RECORD) { // 超过最大聊天条数
				array_splice($worldChatInfo, 0, 1);		
			}
		}
		
		//$worldChatInfo1 = $worldChatInfo;
		$worldChatInfo1 = array();		
		$nr = json_encode($nr);
		if($ltlx == 2) {
			$worldChatInfo[] = array('mc'=>$wjmc, 'dt'=>time(), 'pid'=>$playersid, 'nr'=>$nr, 'other'=>$pid);			
		} else {
			$worldChatInfo[] = array('mc'=>$wjmc, 'dt'=>time(), 'pid'=>$playersid, 'nr'=>$nr);			
		}
		$mc->set(MC.$mem_key, $worldChatInfo, 0, 3600);
		
		if($ltlx == 2 || $ltlx == 1) {
			$worldChatInfo1 = $mc->get(MC.$other_mem_key);
			$worldChatInfo1[] = array('mc'=>$wjmc, 'dt'=>time(), 'pid'=>$playersid, 'nr'=>$nr, 'other'=>$pid);			
			$mc->set(MC.$other_mem_key, $worldChatInfo1, 0, 3600);
		}
		
		$returnValue = cityModel::ltjl(1, $playersid, $ltlx);
		
		return $returnValue;
	}
	
	// 获取重名玩家信息列表
	public static function hqltcmxx($name, $playersid, $page) {
		global $mc, $common, $db, $sys_lang, $city_lang;
	
		$name_find_ret = $db->query("SELECT playersid FROM ".$common->tname('player')." WHERE nickname = '{$name}' AND playersid <> {$playersid}");
		if(mysql_num_rows($name_find_ret) > 1) {
			while($tmp = $db->fetch_array($name_find_ret, MYSQL_ASSOC)) {
				$name_find_arr[] = $tmp['playersid'];
			}			
		} else {
			/*f(mysql_num_rows($name_find_ret) == 0) {
				return array('status'=>998, 'message'=>$city_lang['ltmsg1'][5]);
			}*/
			$tmp = $db->fetch_array($name_find_ret, MYSQL_ASSOC);
			$name_find_arr[] = $tmp['playersid'];
		}

		if (!($disable = $mc->get(MC.$playersid . '_chat_disble'))) {
			$disable_ret = $db->query("SELECT to_pid FROM ".$common->tname('chat_blacklist')." WHERE from_pid = $playersid");
			$disable = $db->fetch_array($disable_ret, MYSQL_NUM);
			$disable = $disable[0];
			$mc->set(MC.$playersid . '_chat_disble', $disable, 0, 3600);
		}
		
		if($disable != false) {
			if(strpos($disable, ',') !== false) {
				$disable_arr = explode(',', $disable);
			} else {
				$disable_arr[] = $disable;
			} 
		} else {
			$disable_arr = array();
		}
		
		if(empty($page)) {
			$page = 1;
		}		
			
		$zs = count($name_find_arr);
		$pageRowNum = 6;
		
		$page_z = 0;
		if($zs > 0) {
			$page_z = ceil($zs / $pageRowNum);
			if($page > $page_z) {
				$page = $page_z;
			}
		}else{
			$page = 1;
		}
		$_start = ($page-1) * $pageRowNum;
		$_end = 0;
		if($_start + $pageRowNum < $zs) {
			$_end = $pageRowNum;
		}else{
			$_end = $zs;
		}
				
		$returnMsgInfo = array();
		foreach($name_find_arr as $key=>$value) {
			if($key > ($_start - 1) && $key < ($_start + $_end)) {
				if(in_array($value, $disable_arr) === false)
					$returnMsgInfo[] = $value;
			}
		}
	
		$find_roleinof_arr = roleModel::getAllRolesInfo($name_find_arr);
		foreach($find_roleinof_arr as $key=>$value) {
			$my_find_arr[$value['playersid']] = $value;
		}
		$lj = array();
		foreach ($returnMsgInfo as $k=>$v) {		
			$vip = $my_find_arr[$v]['vip'] != 0 ? $my_find_arr[$v]['vip'] : 0;
			$level = intval($my_find_arr[$v]['player_level']);
			$sex = intval($my_find_arr[$v]['sex']);
			$lj[] = array(
				'mc'=>$name,				
				'pid'=>intval($v),
				'dj'=>$level,
				'sex'=>$sex,
				'vip'=>$vip
			);
		}
				
		$returnValue['status'] = 0;
		$returnValue['zys'] = $page_z;
		$returnValue['dqys'] = $page;
		//$returnValue['dqys'] = $page; // 当前玩家的工会id
		$returnValue['lj'] = $lj;		
		
		return $returnValue;		
	}
	
	// 获取好友聊天列表
	public static function hqlthyxx( $playersid, $page ) {
		global $mc, $common, $db, $sys_lang, $city_lang;
	
		$my_friend_arr = roleModel::getTableRoleFriendsInfo($playersid, 1, false);

		//		if (!($disable = $mc->get(MC.$playersid . '_chat_disble'))) {
			$disable_ret = $db->query("SELECT to_pid FROM ".$common->tname('chat_blacklist')." WHERE from_pid = $playersid");
			$disable = $db->fetch_array($disable_ret, MYSQL_NUM);
			$disable = $disable[0];
			$mc->set(MC.$playersid . '_chat_disble', $disable, 0, 3600);
			//		}
		
		if($disable != false) {
			if(strpos($disable, ',') !== false) {
				$disable_arr = explode(',', $disable);
			} else {
				$disable_arr[] = $disable;
			} 
		} else {
			$disable_arr = array();
		}
		
		if(empty($page)) {
			$page = 1;
		}	

		for($i = 0; $i < count($my_friend_arr); $i++) {
			if(in_array($my_friend_arr[$i]['playersid'], $disable_arr) !== false) {
				array_splice($my_friend_arr, $i, 1);
			}
		}
		
		$zs = count($my_friend_arr);
		$pageRowNum = 6;
		
		$page_z = 0;
		if($zs > 0) {
			$page_z = ceil($zs / $pageRowNum);
			if($page > $page_z) {
				$page = $page_z;
			}
		}else{
			$page = 1;
		}
		$_start = ($page-1) * $pageRowNum;
		$_end = 0;
		if($_start + $pageRowNum < $zs) {
			$_end = $pageRowNum;
		}else{
			$_end = $zs;
		}
				
		$returnMsgInfo = array();
		foreach($my_friend_arr as $key=>$value) {
			if($key > ($_start - 1) && $key < ($_start + $_end)) {				
				$returnMsgInfo[] = $value;
			}
		}
	
		$lj = array();//$common->insertLog(json_encode($returnMsgInfo));
		foreach ($returnMsgInfo as $k=>$v) {		
			$vip = intval($v['vip']) != 0 ? $v['vip'] : 0;
			$level = intval($v['player_level']);
			$sex = intval($v['sex']);
			$name = $v['nickname'];
			$lj[] = array(
				'mc'=>$name,		
				'pid'=>intval($v['playersid']),
				'dj'=>$level,
				'sex'=>$sex,
				'vip'=>$vip
			);
		}
				
		$returnValue['status'] = 0;
		$returnValue['zys'] = $page_z;
		$returnValue['dqys'] = $page;
		//$returnValue['dqys'] = $page; // 当前玩家的工会id
		$returnValue['lj'] = $lj;		
		
		return $returnValue;			
	}
	
	// 获取聊天记录 2 私聊
	public static function ltjl($page, $playersid, $ltlx) {
		global $mc, $common, $db, $sys_lang, $city_lang;
				
		if (empty($page)) {
			$returnValue['status'] = 3;
			$returnValue['message'] = $sys_lang[7];
			return $returnValue;
		}
		
		if (!($disable = $mc->get(MC.$playersid . '_chat_disble'))) {
			$disable_ret = $db->query("SELECT to_pid FROM ".$common->tname('chat_blacklist')." WHERE from_pid = $playersid");
			$disable = $db->fetch_array($disable_ret, MYSQL_NUM);
			$disable = $disable[0];
			$mc->set(MC.$playersid . '_chat_disble', $disable, 0, 3600);
		}
		
		if($disable != false) {
			if(strpos($disable, ',') !== false) {
				$disable_arr = explode(',', $disable);
			} else {
				$disable_arr[] = $disable;
			} 
		} else {
			$disable_arr = array();
		}

		if(empty($page)) {
			$page = 1;
		}
		
		// 0世界频道 1工会频道 2私聊频道
		if($ltlx == 0) {
			$mem_key = 'Chat_World-channel';
			// 获取私聊信息提示的标志
			if (!($private_channel_newflag = $mc->get(MC.$playersid.'_private-channel_newflag'))) {
				$private_channel_newflag = 0;
			}			
			if($private_channel_newflag == 1) $returnValue['xslxx'] = 1;
		} else if($ltlx == 2) {
			$mem_key = $playersid . '_Private-channel';
			// 修改私聊信息提示的标志
			if($ltlx == 2) {			
				$mc->set(MC.$playersid.'_private-channel_newflag', 0, 0, 3600);
			}
		} else {
			$mem_key = 'Guild-channel'; 
		}

		if (!($worldChatInfo = $mc->get(MC.$mem_key))) {
			$worldChatInfo = '';
			$mc->set(MC.$mem_key, $worldChatInfo, 0, 3600);
		}
		
		// 过滤屏蔽的发言		
		if($worldChatInfo != '') {
			$worldChatInfo_tmp = $worldChatInfo;//print_r($disable_arr);print_r($worldChatInfo_tmp);
			foreach($worldChatInfo_tmp as $key=>$value) {
				if(isset($value['other'])) { // 屏蔽私聊的发言
					if(in_array($value['other'], $disable_arr) !== false) {
						$worldChatInfo[$key]['pb'] = 1;// 添加已屏蔽标识
					}
				}
				if(in_array($value['pid'], $disable_arr) !== false) {
					unset($worldChatInfo[$key]);
				}
			}
			if($ltlx == 2) {
				$mc->set(MC.$mem_key, $worldChatInfo, 0, 3600); // 删除被屏蔽玩家的发言
			}
		}
		
		$zs = count($worldChatInfo);
		$pageRowNum = SHOW_WORLD_CHAT_MESSAGE_NUM;
		
		$page_z = 0;
		if($zs > 0) {
			$page_z = ceil($zs / $pageRowNum);
			if($page > $page_z) {
				$page = $page_z;
			}
		}else{
			$page = 1;
		}
		$_start = ($page-1) * $pageRowNum;
		$_end = 0;
		if($_start + $pageRowNum < $zs) {
			$_end = $pageRowNum;
		}else{
			$_end = $zs;
		}
		
		if($worldChatInfo != '') {
			$worldChatInfo = array_reverse($worldChatInfo);

			$other_plist = array();			
			$returnMsgInfo = array();
			foreach($worldChatInfo as $key=>$value) {
				if($key > ($_start - 1) && $key < ($_start + $_end)) {					
					if($value['pid'] != $playersid) {
						if(in_array($value['pid'], $other_plist) == false)
							$other_plist[] = $value['pid'];							
					} 
					if(isset($value['other'])) {
						if(in_array($value['other'], $other_plist) == false)
							$other_plist[] = $value['other'];
					}
					$returnMsgInfo[] = $value;			
				}
			}
		
			if($ltlx == 2) {
				$other_pinfo_tmp = roleModel::getAllRolesInfo($other_plist);
				foreach($other_pinfo_tmp as $k1=>$v1) {
					$other_pinfo_tmp[$v1['playersid']] = $v1;
				}
				//print_r($other_plist);
				// 获取对方的屏蔽列表
				$other_disable_arr = array();
				foreach($other_plist as $value) {
					if (!($disable = $mc->get(MC.$value . '_chat_disble'))) {
						$disable_ret = $db->query("SELECT to_pid FROM ".$common->tname('chat_blacklist')." WHERE from_pid = $value");
						$disable = $db->fetch_array($disable_ret, MYSQL_NUM);
						$disable = $disable[0];
						$mc->set(MC.$value . '_chat_disble', $disable, 0, 3600);
					}

					if($disable != false) {
						if(strpos($disable, ',') !== false) {
							$disable_arr = explode(',', $disable);
						} else {
							$disable_arr[] = $disable;
						} 
					} else {
						$disable_arr = array();
					}
					
					$other_disable_arr[$value] = $disable_arr;
				}
			}
			
			$my_friend_arr = roleModel::getTableRoleFriendsInfo($playersid, 1, true);
			//print_r($my_friend_arr);
			//print_r($returnMsgInfo);
			//print_r($other_disable_arr);
			$jl = array();
			foreach ($returnMsgInfo as $k=>$v) {
				if($ltlx == 2) {
					$to = null;
					$wjmc = $v['pid'] == $playersid ? $city_lang['getCityBaseInfo_1'] : $v['mc'];
					// 判断是否被屏蔽(双方）
					$ret_pb = 0;
					if(isset($other_disable_arr[$v['pid']])) {
						$pb_arr = $other_disable_arr[$v['pid']];
						if($wjmc == $city_lang['getCityBaseInfo_1']) {					
							if(in_array($v['other'], $pb_arr) != false) $ret_pb = 1;
							$pb_arr = $other_disable_arr[$v['other']];
							if(in_array($v['pid'], $pb_arr) != false) $ret_pb = 2;
						} else {
							if(in_array($v['other'], $pb_arr) != false) $ret_pb = 2;
						}
					} else {
						$ret_pb = isset($v['pb']) ? intval($v['pb']) : 0;
					}
					if($wjmc == $city_lang['getCityBaseInfo_1']) { 
						$pid = $v['other'];
						$to = $other_pinfo_tmp[$v['other']]['nickname'];						
					}
					$diff_time = time() - $v['dt'] < 0 ? 1 : time() - $v['dt'];
					if($diff_time > 60)	
						$dt = ceil($diff_time/60) . $city_lang['ltmsg1'][1];
					else
						$dt = $diff_time . $city_lang['ltmsg1'][0];
						
					// 判断发言双方是否为好友，other,pid都需要判断
					$hy = isset($my_friend_arr[$v['other']]) ? 1 : 0;
					if($hy == 0) $hy = isset($my_friend_arr[$v['pid']]) ? 1 : 0;
					
					$jl[] = array(
						'mc'=>$wjmc,						
						'nr'=>json_decode($v['nr']),
						'pid'=>intval($v['pid']),
						'dt'=>$dt,
						'hy'=>$hy,
						'pb'=>$ret_pb
					);
					if($to != null) {
						$jl[count($jl) - 1]['to'] = $to;
						$jl[count($jl) - 1]['pid'] = intval($pid);
					}

				} else {
					$wjmc = $v['pid'] == $playersid ? $city_lang['getCityBaseInfo_1'] : $v['mc'];				
					$diff_time = time() - $v['dt'] < 0 ? 1 : time() - $v['dt'];
					if($diff_time > 60)	
						$dt = ceil($diff_time/60) . $city_lang['ltmsg1'][1];
					else
						$dt = $diff_time . $city_lang['ltmsg1'][0];
					$hy = isset($my_friend_arr[$v['pid']]) ? 1 : 0;
					$jl[] = array(
						'mc'=>$wjmc,						
						'nr'=>json_decode($v['nr']),
						'pid'=>intval($v['pid']),
						'dt'=>$dt,
						'hy'=>$hy
					);
				}
			}
		} else {
			$jl = '';
		}
		
		$returnValue['status'] = 0;
		$returnValue['zys'] = $page_z;
		$returnValue['dqys'] = $page;
		//$returnValue['dqys'] = $page; // 当前玩家的工会id
		$returnValue['ltlist'] = $jl;
		$returnValue['lx'] = $ltlx;
		
		return $returnValue;
	}
	
	//屏蔽聊天玩家
	static public function pbltwj( $page, $playersid, $ltlx, $tuid ) {
		global $mc, $common, $db, $sys_lang, $city_lang;
		
		$disable = null;
		if (!($disable = $mc->get(MC.$playersid . '_chat_disble'))) {
			$disable_ret = $db->query("SELECT to_pid FROM ".$common->tname('chat_blacklist')." WHERE from_pid = $playersid");
			$disable = $db->fetch_array($disable_ret, MYSQL_NUM);
			$disable = $disable[0];
			$mc->set(MC.$playersid . '_chat_disble', $disable, 0, 3600);
		}
		
		if($disable != false) {
			if(strpos($disable, ',') !== false) {
				$disable_arr = explode(',', $disable);
			} else {
				$disable_arr[] = $disable;
			}
			
			if(count($disable_arr) >= 50) return array('status'=>998,'message'=>$city_lang['ltmsg1'][4]);
				
			if(in_array($tuid, $disable_arr) === false) {
				$disable_arr[] = $tuid;
				$disable = implode(',', $disable_arr);
				$mc->set(MC.$playersid . '_chat_disble', $disable, 0, 3600);
				
				$update['to_pid'] = $disable;
				$where['from_pid'] = $playersid;
				$common->updatetable('chat_blacklist', $update, $where); 		
			}
		} else {
			$disable = $tuid;
			$mc->set(MC.$playersid . '_chat_disble', $disable, 0, 3600);
			$db->query("INSERT INTO ol_chat_blacklist values(null, {$playersid}, '{$disable}')");
		}	
		
		$returnValue = cityModel::ltjl($page, $playersid, $ltlx);
		
		return $returnValue;
	}
	
	// 聊天举报
	public static function jbltxx($playersid, $pid, $nr, $lx) {
		global $mc, $common, $db, $sys_lang, $city_lang, $_SC;
		//return array('status'=>0);
		$nr = urldecode($nr);
		date_default_timezone_set('PRC');
		$date = time();
		
		$res = $db->query("SELECT id,`date` FROM ol_chat_report WHERE jbr_pid ={$playersid}");
		if($db->num_rows($res) >= 3) {
			while($row = $db->fetch_array($res, MYSQL_NUM)){
				$jbr_ids[$row[1]] = $row[0];
				$jbr_wait_sort[] = $row[1];
			}
			sort($jbr_wait_sort);
			$id = $jbr_ids[$jbr_wait_sort[0]];
			//print_r($jbr_ids);
			
			$db->query("UPDATE ol_chat_report SET bjb_pid={$pid}, chat_type={$lx}, chat_content='{$nr}', `date`='{$date}' WHERE id={$id}");	
		} else {	
			$db->query("INSERT INTO ol_chat_report values(null, '{$_SC['fwqdm']}', {$pid}, {$lx}, '{$nr}', {$playersid}, '{$date}')");	
		}
		return array('status'=>0);
	}
	
	// 开启私聊
	public static function slkqxx($playersid, $pid) {
		global $mc, $common, $db, $sys_lang, $city_lang;
		
		$disable = null;
		if (!($disable = $mc->get(MC.$playersid . '_chat_disble'))) {
			$disable_ret = $db->query("SELECT to_pid FROM ".$common->tname('chat_blacklist')." WHERE from_pid = $playersid");
			$disable = $db->fetch_array($disable_ret, MYSQL_NUM);
			$disable = $disable[0];
			$mc->set(MC.$playersid . '_chat_disble', $disable, 0, 3600);
		}
		
		if($disable != false) {
			if(strpos($disable, ',') !== false) {
				$disable_arr = explode(',', $disable);
			} else {
				$disable_arr[] = $disable;
			}
		} else {
			$disable_arr = array();
		}
		
		if(in_array($pid, $disable_arr) === false) {
			$returnValue = array('status'=>0);
		} else {
			$returnValue = array('status'=>998,'message'=>$city_lang['ltmsg1'][2]);
		}
		
		return $returnValue;
	}
	
	// 获取聊天屏蔽列表每页5个
	public static function hqltpb($playersid, $page) {
		global $mc, $common, $db, $sys_lang, $city_lang, $G_PlayerMgr;
		
		if (empty($page)) {
			$returnValue['status'] = 3;
			$returnValue['message'] = $sys_lang[7];
			return $returnValue;
		}
		
		// 获取私聊信息提示的标志
		if (!($private_channel_newflag = $mc->get(MC.$playersid.'_private-channel_newflag'))) {
			$private_channel_newflag = 0;
		}			
		if($private_channel_newflag == 1) $returnValue['xslxx'] = 1;
		
		if (!($disable = $mc->get(MC.$playersid . '_chat_disble'))) {
			$disable_ret = $db->query("SELECT to_pid FROM ".$common->tname('chat_blacklist')." WHERE from_pid = $playersid");
			$disable = $db->fetch_array($disable_ret, MYSQL_NUM);
			$disable = $disable[0];
			$mc->set(MC.$playersid . '_chat_disble', $disable, 0, 3600);
		}
		
		if($disable != false) {
			if(strpos($disable, ',') !== false) {
				$disable_arr = explode(',', $disable);
			} else {
				$disable_arr[] = $disable;
			} 
		} else {
			$disable_arr = array();
		}
		
		if(count($disable_arr) > 0) {
			if(empty($page)) {
				$page = 1;
			}
			
			$zs = count($disable_arr);
			$pageRowNum = 5;
			
			$page_z = 0;
			if($zs > 0) {
				$page_z = ceil($zs / $pageRowNum);
				if($page > $page_z) {
					$page = $page_z;
				}
			}else{
				$page = 1;
			}
			$_start = ($page-1) * $pageRowNum;
			$_end = 0;
			if($_start + $pageRowNum < $zs) {
				$_end = $pageRowNum;
			}else{
				$_end = $zs;
			}
			
			if($disable_arr != '') {
				$disable_arr = array_reverse($disable_arr);			
				$returnMsgInfo = array();
				foreach($disable_arr as $key=>$value) {
					if($key > ($_start - 1) && $key < ($_start + $_end)) {
						$returnMsgInfo[] = $value;
					}
				}
			
				date_default_timezone_set('PRC');
				$jl = array();
				$backlist_pinfo_tmp = roleModel::getAllRolesInfo($disable_arr);
				foreach($backlist_pinfo_tmp as $key=>$value) {
					$backlist_pinfo[$value['playersid']] = $value;
				}
				
				foreach ($returnMsgInfo as $k=>$v) {								
					$jl[] = array(
						'pid'=>$backlist_pinfo[$v]['playersid'],
						'vip'=>$backlist_pinfo[$v]['vip'],
						'nc'=>$backlist_pinfo[$v]['nickname'],
						'level'=>$backlist_pinfo[$v]['player_level']
					);
				}
			} else {
				$jl = '';
			}
			
			$returnValue['status'] = 0;
			$returnValue['zys'] = $page_z;
			$returnValue['dqys'] = $page;
			$returnValue['ly'] = $jl;
		} else {
			$returnValue['status'] = 0;
			$returnValue['zys'] = 0;
			$returnValue['dqys'] = 0;
			$returnValue['ly'] = array();
		}
		
		return $returnValue;	
		
	}
	
	// 取消聊天屏蔽
	public static function qxltpb($playersid, $lx, $tuid, $ym) {
		global $mc, $common, $db, $sys_lang, $city_lang, $G_PlayerMgr;
		
		if (!($disable = $mc->get(MC.$playersid . '_chat_disble'))) {
			$disable_ret = $db->query("SELECT to_pid FROM ".$common->tname('chat_blacklist')." WHERE from_pid = $playersid");
			$disable = $db->fetch_array($disable_ret, MYSQL_NUM);
			$disable = $disable[0];
			$mc->set(MC.$playersid . '_chat_disble', $disable, 0, 3600);
		}
		
		if($disable != false) {
			if(strpos($disable, ',') !== false) {
				$disable_arr = explode(',', $disable);
			} else {
				$disable_arr[] = $disable;
			} 
		} else {
			$disable_arr = array();
		}
		
		if($lx == 1) {			
			$mc->delete(MC.$playersid . '_chat_disble');
			$db->query("DELETE FROM ".$common->tname('chat_blacklist')." WHERE from_pid = $playersid");			
		} else {
			if(count($disable_arr) > 1) {
				unset($disable_arr[array_search($tuid, $disable_arr)]);
				$disable = implode(',', $disable_arr);
				$mc->set(MC.$playersid . '_chat_disble', $disable, 0, 3600);
				
				$update['to_pid'] = $disable;
				$where['from_pid'] = $playersid;
				$common->updatetable('chat_blacklist', $update, $where);	
			} else {
				$mc->delete(MC.$playersid . '_chat_disble');
				$db->query("DELETE FROM ".$common->tname('chat_blacklist')." WHERE from_pid = $playersid");		
			}
		}
		
		if($lx == 1 || count($disable_arr) == 0) {
			$returnValue['status'] = 0;
			$returnValue['zys'] = 0;
			$returnValue['dqys'] = 0;
			$returnValue['ly'] = array();
		} else {
			$returnValue = cityModel::hqltpb($playersid, $ym);
		}		
		return $returnValue;		
	}
	//评分奖励
	public static function sdkpf($playersid,$jcid) {
		global $mc, $common, $db, $sys_lang, $city_lang, $G_PlayerMgr;
		$player = $G_PlayerMgr->GetPlayer($playersid);
		if (! $player) {
			return array ('status' => 3, 'message' => $sys_lang[1] );
		}  
    	$roleInfo = $player->GetBaseInfo();
    	$pfjl = $roleInfo['sgpf'];
    	if (substr($pfjl,0,1) == 1) {
    		return array('status'=>30,'message'=>$city_lang['sgsdkpf_1']);
    	}
    	$coins = $roleInfo['coins'];
		$updateRole ['coins'] = $roleInfo ['coins'] + 10000;
		if ($updateRole ['coins'] > COINSUPLIMIT) {
			$updateRole ['coins'] = COINSUPLIMIT;
		}
		$updateRole['sgpf'] = substr_replace($pfjl,1,0,1);
		$value['tq'] = intval($updateRole['coins']);
		$addItem = $player->AddItems(array(10012=>1));
		if ($addItem === false) {
			$value['status'] = 1001;
			$unlock_proto = toolsModel::getItemInfo(10012);
			$unlock_item_name = $unlock_proto['Name'];			
			$unlock_json = array();
			$unlock_json['playersid'] = $playersid;
			$unlock_json['toplayersid'] = $playersid;
			$unlock_json['message'] = array('xjnr'=>$city_lang['sgsdkpf_2']);
			$unlock_json['genre'] = 20;
			$unlock_json['request'] = json_encode(array('tq'=>0,'yp'=>0,'yb'=>0,'jl'=>0,'items'=>array(array('id'=>10012, 'mc'=>$unlock_item_name, 'num'=>1))));
			$unlock_json['type'] = 1;
			$unlock_json['uc'] = '0';
			$unlock_json['is_passive'] = 0;
			$unlock_json['interaction'] = 0;
			$unlock_json['tradeid'] = 0;							
			lettersModel::addMessage($unlock_json);			
		} else {
			$value['status'] = 0;
			$value['list'] = $player->GetClientBag();
		}
		$common->updatetable('player',$updateRole,"playersid = $playersid");
		$common->updateMemCache(MC.$playersid,$updateRole);
		return $value;
	}

	/**
	 * 显示排行榜
	 * $type 1,战力 2,武将 3,等级 4,爵位 5,进步最快
	 */
	public static function showPm($playersid, $type, $page=1, $me = null){
		global $mc, $common, $db, $sys_lang, $city_lang, $G_PlayerMgr;

		$totalPage = 0;
		if(!is_null($me)&&$me==1){
			$pmList = cityModel::showMyPm($playersid, $type);
			if(isset($pmList['status']) && $pmList['status'] == 1001){
				return $pmList;
			}
		}else {
			$mc_pmList = array();

			// 构建排名数据
			$mem_key = MC.'pm_top100_'.$type;
			if(!($mc_pmList = $mc->get($mem_key))){
				$pmSql = '';

				switch($type){
				case 1:
					$pmSql = "select `mc`, `zl`, playersid from " . $common->tname('player_pm');
					$pmSql .= " order by `zl` desc";
					break;
				case 2:
					$pmSql = "select pm.*, p.nickname mc from " . $common->tname('playergeneral_pm') . " pm, ";
					$pmSql .= $common->tname('player');
					$pmSql .= " p where p.playersid=pm.playersid";
					$pmSql .= " order by `zl` desc";
					break;
				case 3:
					$pmSql = "select `mc`, `lv`, playersid from " . $common->tname('player_pm');
					$pmSql .= " order by `lv` desc, `exp` desc, `lv_last_time` desc";
					break;
				case 4:
					$pmSql = "select `mc`, `jw`, playersid from ".$common->tname('player_pm');
					$pmSql .= " order by `jw` desc, prestige desc";
					break;
				case 5:
					$pmSql = "select `mc`, `zl`, playersid from " . $common->tname('player_pm');
					$pmSql .= " order by increase desc";
					break;
				default:
					return array('status'=>566, 'message'=>'');
				}
			
				$pmSql .= " limit 100";
				$result = $db->query($pmSql);
			
				// 格式化存储数据
				$_player_pm = 1;
				while($row = $db->fetch_array($result)){
					switch($type){
					case 1:
					case 3:
					case 5:
						$mc_pm = $row;
						break;
					case 2:
						unset($row['gid']);
						$mc_pm = $row;
						break;
					case 4:
						$jwmc = jwmc($row['jw']);
						$mc_pm = array('mc'=>$row['mc'],
									   'jw'=>$jwmc['mc'],
									   'playersid'=>$row['playersid']);
						break;
					}
					$mc_pm['pm'] = $_player_pm;
					$_player_pm++;
					$mc_pmList[] = $mc_pm;
				}

				$mc->set($mem_key, $mc_pmList, 0, 3600);
			}

			$pageNum = 5;
			// 根据请求页号反回请求内容(20条)
			$pmList = array();
			$start = ($page-1)*$pageNum;
			for($i=0; $i<20; $i++){
				if(isset($mc_pmList[$start + $i])){
					if($mc_pmList[$start + $i]['playersid'] == $playersid){
						$mc_pmList[$start + $i]['me'] = 1;
					}
					unset($mc_pmList[$start + $i]['playersid']);
					$pmList[] = $mc_pmList[$start + $i];
				}else{
					break;
				}
			}

			$totalPage = ceil(count($mc_pmList)/$pageNum);
		}


		$returnValue = array('status' => 0,
							 'ys'     => $totalPage,
							 'pmlist' => $pmList);

		return $returnValue;
	}

	/**
	 * $type 1,战力 2,武将 3,等级 4,爵位 5,进步最快
	 */
	public static function showMyPm($playersid, $type){
		global $mc, $common, $db, $sys_lang, $city_lang, $G_PlayerMgr;
		
		// 构建排名查询
		switch($type){
		case 1:
			$pmSql = "select pm from ol_pm_zl";
			break;
		case 2:
			$pmSql = "select pm from ol_pm_wj";
			break;
		case 3:
			$pmSql = "select pm from ol_pm_plevel";
			break;
		case 4:
			$pmSql = "select pm from ol_pm_jw";
			break;
		case 5:
			$pmSql = "select pm from ol_pm_inc";
			break;
		default:
			return array();
		}
		$pmSql .= " where playersid='$playersid' order by pm limit 1";
		$result = $db->query($pmSql);

		$pm = 0;
		if($row = $db->fetch_array($result)){
			$pm = $row['pm'];
		}

		// 没找到请求玩家排名或者正在更新排名
		if($pm == 0){
			return array('status'=>1001);
		}

		// 构建查询语句
		$startPm = $pm - 2;
		$startPm = 0 >= $startPm ? 1 : $startPm;
		$endPm   = $startPm + 4;
		switch($type){
		case 1:
			$listSql = "select p.`playersid`, `mc`, `zl`, pm.pm from " . $common->tname('player_pm');
			$listSql .= " p inner join " . $common->tname('pm_zl') . ' pm on p.playersid=pm.playersid';
			$listSql .= " where pm.`pm`>='{$startPm}' and pm.`pm`<='{$endPm}' order by pm.`pm` limit 5";
			break;
		case 2:
			$listSql = "select pm.*, p.nickname mc, wj.pm from " . $common->tname('playergeneral_pm') . " pm, ";
			$listSql .= $common->tname('player') . " p, " . $common->tname('pm_wj') . " wj";
			$listSql .= " where p.playersid=pm.playersid and pm.playersid=wj.playersid and pm.gid=wj.gid";
			$listSql .= " and wj.`pm`>='{$startPm}' and wj.`pm`<='{$endPm}' order by wj.`pm` limit 5";
			break;
		case 3:
			$listSql = "select `mc`, `lv`, l.pm, pm.playersid from " . $common->tname('player_pm'). " pm ";
			$listSql .= " inner join " . $common->tname('pm_plevel') . " l on pm.playersid=l.playersid";
			$listSql .= " where l.`pm`>={$startPm} and l.`pm`<='{$endPm}' order by l.`pm` limit 5";
			break;
		case 4:
			$listSql = "select `mc`, `jw`, j.pm, pm.playersid from " . $common->tname('player_pm');
			$listSql .= " pm inner join " . $common->tname('pm_jw') . " j on pm.playersid=j.playersid";
			$listSql .= " where `pm`>='{$startPm}' and `pm`<='{$endPm}' order by j.`pm` limit 5";
			break;
		case 5:
			$listSql = "select `mc`, `zl`, pm.playersid, i.pm from " . $common->tname('player_pm');
			$listSql .= " pm inner join " . $common->tname('pm_inc') . ' i on pm.playersid=i.playersid';
			$listSql .= " where i.`pm`>='{$startPm}' and i.`pm`<='{$endPm}' order by i.`pm` limit 5";
			break;
		}
		// 查询结果,找出自己的位置并标识me
		$result = $db->query($listSql);
		$pmList = array();
		while($row = $db->fetch_array($result)){
			if($playersid == $row['playersid']){
				$row['me'] = 1;
			}
			if($type==4){
				$jwmc = jwmc($row['jw']);
				$row['jw'] = $jwmc['mc'];
			}
			unset($row['playersid']);
			$pmList[] = $row;
		}

		return $pmList;
	}
}
