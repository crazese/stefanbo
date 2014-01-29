<?php
class genModel {
	//解雇武将	
	public static function fireGerneral($playersid, $generalId) {
		global $db, $common, $mc, $_SGLOBAL, $G_PlayerMgr, $sys_lang, $city_lang;
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$sys_lang[7]);
		
		$gerneralRows = cityModel::getGeneralData ( $playersid, 0, $generalId );
		$gerneralRowsTatol = cityModel::getGeneralData ( $playersid, 0, '*' );
		$zb1 = $gerneralRows [0] ['helmet'];
		$zb2 = $gerneralRows [0] ['carapace'];
		$zb3 = $gerneralRows [0] ['arms'];
		$zb4 = $gerneralRows [0] ['shoes'];
		$f_status = $gerneralRows [0] ['f_status'];
		//$xljssj = $gerneralRows [0] ['xl_end_time'];  //训练结束时间
		$zbsl = 0;
		$amount = array ();
		if ($zb1 > 0) {
			$amount [] = 1;
		}
		if ($zb2 > 0) {
			$amount [] = 1;
		}
		if ($zb3 > 0) {
			$amount [] = 1;
		}
		if ($zb4 > 0) {
			$amount [] = 1;
		}
		if (! empty ( $amount )) {
			$zbsl = array_sum ( $amount );
			$value ['haveZb'] = 1;
		} else {
			$value ['haveZb'] = 0;
		}
		$bbxx = toolsModel::getAllItems ( $playersid ); //获取背包数据
		if ($bbxx ['status'] == 1021) {
			$yygs = 0;
		} else {
			$yygs = count ( $bbxx ['list'] );
		}
		$totalBlocks = toolsModel::getBagTotalBlocks ( $playersid ); //玩家背包最大格数
		$sygs = $totalBlocks - $yygs; //背包剩余格数  
		$roleInfo['playersid'] = $playersid;
		//如果是驻城武将则清空该武将信息
		roleModel::getRoleInfo($roleInfo);					  
	    if (empty ( $gerneralRows )) {
			$value ['status'] = 4;
			$value ['message'] = $sys_lang[3];
		} elseif ($gerneralRows [0] ['occupied_playersid'] != 0 && $gerneralRows [0] ['occupied_playersid'] == $playersid && $roleInfo['aggressor_general'] == $generalId) {
			$value ['status'] = 30;
			$value ['message'] = $city_lang['fireGerneral_1'];
		} elseif ($gerneralRows [0] ['occupied_playersid'] != 0 && $gerneralRows [0] ['occupied_playersid'] != $playersid && $gerneralRows [0] ['occupied_end_time'] > $_SGLOBAL['timestamp']) {
			$value ['status'] = 30;
			$value ['message'] = $city_lang['fireGerneral_2'];
		} elseif ($zbsl > $sygs) {
			$value ['status'] = 1030;
			$value ['message'] = $city_lang['fireGerneral_3'];
/*hk*/		} elseif ($gerneralRows [0] ['gohomeTime'] > $_SGLOBAL['timestamp'] && ($gerneralRows [0] ['act'] == 4 || $gerneralRows [0] ['act'] == 7)) {
			$value ['status'] = 30;
			$value ['message'] = $city_lang['fireGerneral_4'];
		} elseif ($gerneralRows [0] ['gohomeTime'] > $_SGLOBAL['timestamp'] && $gerneralRows [0] ['act'] == 3 ) {
			$value ['status'] = 30;
			$value ['message'] = $city_lang['fireGerneral_5'];
		} elseif ($gerneralRows [0] ['gohomeTime'] > $_SGLOBAL['timestamp'] && $gerneralRows [0] ['act'] == 2 ) {
			$value ['status'] = 30;
			$value ['message'] = $city_lang['fireGerneral_6'];
		} elseif ( $gerneralRows [0] ['act'] == 1 || $gerneralRows [0] ['act'] == 6) {
			$value ['status'] = 30;
			$value ['message'] = $city_lang['fireGerneral_7'];/*hk*///限制解雇武将的条件
		} else {
			$IDArray = array ();
			$playerBagRef = &$player->GetItems();
			foreach ( $playerBagRef as $itemInfo ) {
				if ($itemInfo ['ID'] == $zb1 || $itemInfo ['ID'] == $zb2 || $itemInfo ['ID'] == $zb3 || $itemInfo ['ID'] == $zb4) {
					$IDArray [] = $itemInfo ['ID'];
					$playerBagRef[$itemInfo ['ID']]['IsEquipped'] = 0;
				}
			}
			if (! empty ( $IDArray )) {
				$common->updateMemCache ( MC . 'items_' . $playersid, $playerBagRef );
				$common->updatetable ( 'player_items', "IsEquipped = '0'", "ID in (" . implode ( ',', $IDArray ) . ")" );					
			}				

			//print_r($gerneralRows);
			$glsl = count($gerneralRowsTatol);
			if ($glsl > 1) {
				$general_sort = $gerneralRows [0] ['general_sort'];
				$common->updatetable ( 'playergeneral', "general_sort = general_sort - 1", "playerid = '$playersid' && general_sort > '$general_sort' && f_status = '$f_status'" );
			}
			$whereDel ['intID'] = $generalId;
			$common->deletetable ( 'playergeneral', $whereDel );
			$value ['status'] = 0;
			$getInfo ['playersid'] = $playersid;
			if ($roleInfo['zf_aggressor_general'] == $generalId) {
				$updateRole['zf_aggressor_general'] = 0;
				$updateRole['zf_strategy'] = 0;
				$updateRoleWhere['playersid'] = $playersid;
				$common->updatetable('player',$updateRole,$updateRoleWhere);
				$common->updateMemCache(MC.$playersid,$updateRole);
			}
			//如果是驻城武将则清空该武将信息（结束）
			if ($glsl > 1) {				
				$generalInfo = cityModel::getGeneralList ( $getInfo, 1, true );
				if ($generalInfo['status'] == 0) {
					//$value ['generals'] = $generalInfo ['generals']; 
					foreach ($generalInfo ['generals'] as $generalInfoValue) {
						$garray[] = array('gid' => $generalInfoValue['gid'],'px' => $generalInfoValue['px']);
					}
					$value ['generals'] = $garray;
				}
			} else {
				$value ['generals'] = array();
				$mc->delete(MC . $playersid . '_general');
			}
			//$value ['hirableGeneral'] = $generalInfo ['hirableGeneral'];
			//$value ['nextUpdate'] = $generalInfo ['nextUpdate'];
			$common->updatetable('playerxlw',"g_end_time = '0',gid = '0'","playersid = '$playersid' && gid = '$generalId'");
			genModel::getxlw($playersid,true);
		}
		return $value;
	}
	
	//快速提取武魂
	public static function kstqwh($playersid, $tuid, $generalId) {
		global $db, $common, $mc, $_SGLOBAL, $G_PlayerMgr, $sys_lang, $city_lang;
		$whsl = 0;
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player) {	
			return array('status'=>21, 'message'=>$sys_lang[7]);
		}
		if ($playersid != $tuid) {
			$sftj = 1;
		} else {
			$sftj = 0;
		}
		$hireGeneralInfo = '';
		$hireGeneralInfoArray = cityModel::getHireGeneralInfo ( $tuid, $hireGeneralInfo );
		if ($sftj == 1 && $hireGeneralInfoArray ['tjbhsj'] != 0) {
			$value ['status'] = 1021;
			$value ['message'] = $city_lang['hireGeneral_2'];
			return $value;
		}	
		if (empty ( $hireGeneralInfo [$generalId] )) {
			$value ['status'] = 22;
			$value ['message'] = $city_lang['hireGeneral_3'];
			return $value;
		}
		$canhireinfo = $hireGeneralInfo [$generalId];
		$general_sort_fight = array ();
		if (! empty ( $canhireinfo )) {
			$generalInfo = cityModel::getGeneralData ( $playersid, '', '*' );
			$tf = $canhireinfo ['understanding_value'];
			$general_name = $canhireinfo ['general_name']; //将领姓名
			if (! empty ( $generalInfo )) {
				$general_total_num = count ( $generalInfo ); //将领总计数量
			} else {
				$general_total_num = 0;
			}	
			$roleInfo = $player->baseinfo_;		
			$command_upperlimit = guideValue ( $roleInfo ['djt_level'] ); //可招募将领上限       
			$allowhirenum = $command_upperlimit - $general_total_num; //允许招募将领个数  	
			$coins = $roleInfo ['coins'];
			if ($coins < $canhireinfo ['hftq']) {
				$value ['status'] = 58;
				$value ['message'] = $city_lang['hireGeneral_12'];
			} elseif ($allowhirenum > 0) {				
				$ys = actModel::hqwjpz ( $tf ) + 1;
				$value ['status'] = 0;							
				if ($sftj == 1 && $ys > 2) {
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
							$message = array('playersid'=>$roleInfo['playersid'],'toplayersid'=>$tuid,'subject'=>$city_lang['hireGeneral_15'],'genre'=>20,'tradeid'=>0,'interaction'=>0,'is_passive'=>0,'type'=>1,'request'=>addcslashes(json_encode(array('tq'=>0,'yp'=>$jlyp,'yb'=>0,'jl'=>0,'items'=>array())), '\\'),'message'=>array('xjnr'=>$roleInfo['nickname'].$city_lang['hireGeneral_25'].$general_name.$city_lang['hireGeneral_26'].$jlyp.$city_lang['hireGeneral_27']));
							lettersModel::addMessage($message);	
						}
						$value['xhyp'] = $hfyp;							
					}
				}
				if ($ys > 1) {
					$whsl = tqwhsl($ys);		
					$whiddata = array(2=>18580,3=>18581,4=>18582,5=>18583);
					$whid = $whiddata[$ys];	
					$ysInfo = $city_lang['tqwh_9'];
					$xsys = $ysInfo[$ys];
					$stat = $player->AddItems(array($whid=>$whsl));
					if ($stat === false) {  //背包已满	
						//$message = array('playersid'=>$roleInfo['playersid'],'toplayersid'=>$playersid,'subject'=>$city_lang['hireGeneral_15'],'genre'=>20,'tradeid'=>0,'interaction'=>0,'is_passive'=>0,'type'=>1,'request'=>addcslashes(json_encode(array('tq'=>0,'yp'=>0,'yb'=>0,'jl'=>0,'items'=>array($whid=>$whsl))), '\\'),'message'=>array('xjnr'=>$city_lang['kstqwh_2']));
						//$value['status'] = 1001;
						return array('status'=>30,'message' => $city_lang['tqwh_8']);
					} else {								
						$value ['list'] = $player->GetClientBag();		
						$value['message'] = $city_lang['tqwh_10'].$whsl.$city_lang['tqwh_11'].$xsys.$city_lang['tqwh_12'];	
						$roleInfo['rwsl'] = $whsl;
						if ($ys == 4) {
							$item = 'tqwhzwh';
						} elseif ($ys == 5) {
							$item = 'tqwhcwh';
						} else {
							$item = 'xxxxxxxxxx';
						}
						$rwid = questsController::OnFinish($roleInfo,"'tqwh','".$item."'");
					    if (!empty($rwid)) {
					         $value['rwid'] = $rwid;				             
					    }							
					}
				} else {
					return array('status' => 50,'message'=>$city_lang['kstqwh_1']);
				}				
				unset ( $hireGeneralInfo [$generalId] );
				//print_r($hireGeneralInfo);
				$mc->set ( MC . $tuid . '_HireGeneral', $hireGeneralInfo, 0, 3600 );
				$updateGinfo ['ginfo'] = serialize ( $hireGeneralInfo );
				$updateGinfoWhere ['playersid'] = $tuid;
				$common->updatetable ( 'save_ginfo', $updateGinfo, $updateGinfoWhere );				
	
				$updateRole ['coins'] = $coins - $canhireinfo ['hftq'];
				$updateRoleWhere ['playersid'] = $playersid;				
				$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
				$common->updateMemCache ( MC . $playersid, $updateRole );
				$value ['tq'] = $updateRole ['coins'];
				$value ['xhtq'] = $canhireinfo ['hftq'];
			} else {
				$value ['status'] = 28; //由于招募将领大到上限导致招募失败
				//$value['upperlimit'] = $command_upperlimit;                         //招募上限值
				$value ['message'] = $city_lang['hireGeneral_22'] . $command_upperlimit . $city_lang['hireGeneral_23'];
			}			
		} else {
			$value ['status'] = 30;
			$value ['message'] = $city_lang['hireGeneral_24'];
		}	
		return $value;						
	}
	
	//提取将魂	
	public static function tqwh($playersid, $generalId) {
		global $db, $common, $mc, $_SGLOBAL, $G_PlayerMgr, $sys_lang, $city_lang;
		$whsl = 0;
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$sys_lang[7]);
		
		$gerneralRows = cityModel::getGeneralData ( $playersid, 0, $generalId );
		if (empty($gerneralRows)) {
			return array('status'=>21, 'message'=>$sys_lang[3]);
		}
		$gerneralRowsTatol = cityModel::getGeneralData ( $playersid, 0, '*' );
		
		$zb1 = $gerneralRows [0] ['helmet'];
		$zb2 = $gerneralRows [0] ['carapace'];
		$zb3 = $gerneralRows [0] ['arms'];
		$zb4 = $gerneralRows [0] ['shoes'];
		$f_status = $gerneralRows [0] ['f_status'];
		$tf = $gerneralRows [0] ['understanding_value'] - $gerneralRows [0] ['llcs'];
		$ys = actModel::hqwjpz($tf) + 1;
		if ($ys > 1) {
			$whsl = tqwhsl($ys);		
			$whiddata = array(2=>18580,3=>18581,4=>18582,5=>18583);
			$whid = $whiddata[$ys];	
			$ysInfo = $city_lang['tqwh_9'];
			$xsys = $ysInfo[$ys];
		}
		//$xljssj = $gerneralRows [0] ['xl_end_time'];  //训练结束时间
		$zbsl = 0;
		$amount = array ();
		if ($zb1 > 0) {
			$amount [] = 1;
		}
		if ($zb2 > 0) {
			$amount [] = 1;
		}
		if ($zb3 > 0) {
			$amount [] = 1;
		}
		if ($zb4 > 0) {
			$amount [] = 1;
		}
		if (! empty ( $amount )) {
			$zbsl = array_sum ( $amount );
			$value ['haveZb'] = 1;
		} else {
			$value ['haveZb'] = 0;
		}
		$bbxx = toolsModel::getAllItems ( $playersid ); //获取背包数据
		if ($bbxx ['status'] == 1021) {
			$yygs = 0;
		} else {
			$yygs = count ( $bbxx ['list'] );
		}
		$totalBlocks = toolsModel::getBagTotalBlocks ( $playersid ); //玩家背包最大格数
		$sygs = $totalBlocks - $yygs; //背包剩余格数  
		$roleInfo['playersid'] = $playersid;
		//如果是驻城武将则清空该武将信息
		roleModel::getRoleInfo($roleInfo);					  
		if ($gerneralRows [0] ['occupied_playersid'] != 0 && $gerneralRows [0] ['occupied_playersid'] == $playersid && $roleInfo['aggressor_general'] == $generalId) {
			$value ['status'] = 30;
			if ($ys == 1) {
				$value ['message'] = $city_lang['fireGerneral_1'];
			} else {
				$value ['message'] = $city_lang['tqwh_1'];
			}
		} elseif ($gerneralRows [0] ['occupied_playersid'] != 0 && $gerneralRows [0] ['occupied_playersid'] != $playersid && $gerneralRows [0] ['occupied_end_time'] > $_SGLOBAL['timestamp']) {
			$value ['status'] = 30;
			if ($ys == 1) {
				$value ['message'] = $city_lang['fireGerneral_2'];
			} else {			
				$value ['message'] = $city_lang['tqwh_2'];
			}
		} elseif ($zbsl > $sygs) {
			$value ['status'] = 30;
			if ($ys == 1) {
				$value ['message'] = $city_lang['fireGerneral_3'];
			} else {			
				$value ['message'] = $city_lang['tqwh_3'];
			}
/*hk*/		} elseif ($gerneralRows [0] ['gohomeTime'] > $_SGLOBAL['timestamp'] && ($gerneralRows [0] ['act'] == 4 || $gerneralRows [0] ['act'] == 7)) {
			$value ['status'] = 30;
			if ($ys == 1) {
				$value ['message'] = $city_lang['fireGerneral_4'];
			} else {			
				$value ['message'] = $city_lang['tqwh_4'];
			}
		} elseif ($gerneralRows [0] ['gohomeTime'] > $_SGLOBAL['timestamp'] && $gerneralRows [0] ['act'] == 3 ) {
			$value ['status'] = 30;
			if ($ys == 1) {
				$value ['message'] = $city_lang['fireGerneral_5'];
			} else {
				$value ['message'] = $city_lang['tqwh_5'];
			}
		} elseif ($gerneralRows [0] ['gohomeTime'] > $_SGLOBAL['timestamp'] && $gerneralRows [0] ['act'] == 2 ) {
			$value ['status'] = 30;
			if ($ys == 1) {
				$value ['message'] = $city_lang['fireGerneral_6'];
			} else {			
				$value ['message'] = $city_lang['tqwh_6'];
			}
		} elseif ( $gerneralRows [0] ['act'] == 1 || $gerneralRows [0] ['act'] == 6) {
			$value ['status'] = 30;
			if ($ys == 1) {
				$value ['message'] = $city_lang['fireGerneral_7'];
			} else {			
				$value ['message'] = $city_lang['tqwh_7'];/*hk*///限制解雇武将的条件
			}
		} else {		
			$IDArray = array ();
			$playerBagRef = &$player->GetItems();
			foreach ( $playerBagRef as $itemInfo ) {
				if ($itemInfo ['ID'] == $zb1 || $itemInfo ['ID'] == $zb2 || $itemInfo ['ID'] == $zb3 || $itemInfo ['ID'] == $zb4) {
					$IDArray [] = $itemInfo ['ID'];
					$playerBagRef[$itemInfo ['ID']]['IsEquipped'] = 0;
				}
			}
			if ($ys > 1) {
				$stat = $player->AddItems(array($whid=>$whsl));
				if ($stat === false) {  //背包已满	
					return array('status'=>30,'message' => $city_lang['tqwh_8']);
				} else {
					//$value ['list'] = $player->GetClientBag();		
					$value['message'] = $city_lang['tqwh_10'].$whsl.$city_lang['tqwh_11'].$xsys.$city_lang['tqwh_12'];		
				}
			}
			$value ['list'] = $player->GetClientBag();				
			if (! empty ( $IDArray )) {
				$common->updateMemCache ( MC . 'items_' . $playersid, $playerBagRef );
				$common->updatetable ( 'player_items', "IsEquipped = '0'", "ID in (" . implode ( ',', $IDArray ) . ")" );					
			}
			//print_r($gerneralRows);
			$glsl = count($gerneralRowsTatol);
			if ($glsl > 1) {
				$general_sort = $gerneralRows [0] ['general_sort'];
				$common->updatetable ( 'playergeneral', "general_sort = general_sort - 1", "playerid = '$playersid' && general_sort > '$general_sort' && f_status = '$f_status'" );
			}
			$whereDel ['intID'] = $generalId;
			$common->deletetable ( 'playergeneral', $whereDel );
			$value ['status'] = 0;
			$getInfo ['playersid'] = $playersid;
			if ($roleInfo['zf_aggressor_general'] == $generalId) {
				$updateRole['zf_aggressor_general'] = 0;
				$updateRole['zf_strategy'] = 0;
				$updateRoleWhere['playersid'] = $playersid;
				$common->updatetable('player',$updateRole,$updateRoleWhere);
				$common->updateMemCache(MC.$playersid,$updateRole);
			}
			//如果是驻城武将则清空该武将信息（结束）
			if ($glsl > 1) {				
				$generalInfo = cityModel::getGeneralList ( $getInfo, 1, true );
				if ($generalInfo['status'] == 0) {
					//$value ['generals'] = $generalInfo ['generals']; 
					foreach ($generalInfo ['generals'] as $generalInfoValue) {
						$garray[] = array('gid' => $generalInfoValue['gid'],'px' => $generalInfoValue['px']);
					}
					$value ['generals'] = $garray;
				}
			} else {
				$value ['generals'] = array();
				$mc->delete(MC . $playersid . '_general');
			}
			//$value ['hirableGeneral'] = $generalInfo ['hirableGeneral'];
			//$value ['nextUpdate'] = $generalInfo ['nextUpdate'];
			$common->updatetable('playerxlw',"g_end_time = '0',gid = '0'","playersid = '$playersid' && gid = '$generalId'");
			genModel::getxlw($playersid,true);	
			if ($ys > 1) {
				//$roleInfo['wcsl'] = $whsl;	
				$roleInfo['rwsl'] = $whsl;
				if ($ys == 4) {
					$item = 'tqwhzwh';
				} elseif ($ys == 5) {
					$item = 'tqwhcwh';
				} else {
					$item = 'xxxxxxxxxx';
				}
				$rwid = questsController::OnFinish($roleInfo,"'tqwh','".$item."'");
			    if (!empty($rwid)) {
			         $value['rwid'] = $rwid;				             
			    }	
			}		
		}
		return $value;
	}
	
	//设置武将换防
	public static function setZf($playersid, $gid) {
		global $common, $db, $_SGLOBAL,$city_lang,$sys_lang;
		$generalInfo = cityModel::getGeneralData ( $playersid,'','*' );
		$stop = 0;
		$roleInfo ['playersid'] = $playersid;
		roleModel::getRoleInfo ( $roleInfo );
		$nowTime = $_SGLOBAL['timestamp'];
        if ($roleInfo ['aggressor_playersid'] != 0 && $roleInfo ['aggressor_playersid'] != $playersid && $roleInfo ['end_defend_time'] > $nowTime) {
			$value ['status'] = 1001;
			$value ['message'] = $city_lang['setZf_1'];
			$generalID = $roleInfo ['aggressor_general'];
			$incomeInfo = cityModel::occupied_income ( $generalID, $roleInfo ['aggressor_playersid'] );
			$value ['time'] = $incomeInfo ['time'];
			$value ['uid'] = $roleInfo ['aggressor_playersid'];
			$value ['un'] = $roleInfo ['aggressor_nickname'];
			$value ['un'] = $roleInfo ['aggressor_level'];
			$msgRes = $db->query ( "SELECT `aggressor_message` FROM " . $common->tname ( 'aggressor_message' ) . " WHERE `playersid` = '" . $playersid . "' && `aggressor_playersid` = '" . $roleInfo ['aggressor_playersid'] . "' ORDER BY id DESC LIMIT 1" );
			$msgInfo = $db->fetch_array ( $msgRes );
			if (! empty ( $msgInfo )) {
				$zlxy = $msgInfo ['aggressor_message'];
				if (empty($zlxy)) {
					$zlxy = $city_lang['setZf_2'];
				}
			} else {
				$zlxy = $city_lang['setZf_2'];
			}
			$value ['zlxy'] = $zlxy;
		} else {			
			$update ['occupied_playersid'] = $playersid;
			$update ['occupied_player_level'] = $roleInfo ['player_level'];
			$update ['occupied_player_nickname'] = mysql_escape_string($roleInfo ['nickname']);
			$updateWhere ['intID'] = $gid;
			$getInfo ['playersid'] = $playersid;
			//$generalInfo = cityModel::getGeneralList($getInfo,0,true);
			$allow = 0;
			$gid2 = 0;
			$allow2 = 0;
			$last_income_time = $nowTime;
			$occupied_end_time = $nowTime;
			foreach ( $generalInfo as $gvalue1 ) {
				if ($gvalue1 ['intID'] == $roleInfo['zf_aggressor_general']) {
					$last_income_time = $gvalue1 ['last_income_time'];
					if ($last_income_time == 0) {
						$last_income_time = $nowTime;
					}
					$occupied_end_time = $gvalue1 ['occupied_end_time'];
					break;
				}
			}
			if ($roleInfo['aggressor_playersid'] == 0) {
				$update ['last_income_time'] = $nowTime;
				$update ['occupied_end_time'] = $nowTime + 86400;
/*hk*/				$xjq = 1;//军情状态
			} else {
				$update ['last_income_time'] = $last_income_time;
				$update ['occupied_end_time'] = $occupied_end_time;						
/*hk*/				$xjq = 0;	//军情状态			
			}
			$newData = array();		
			$zxsj = $last_income_time;				
			foreach ( $generalInfo as $key => $gvalue ) {
				if ($gvalue ['intID'] == $gid) {
					if ($gvalue ['occupied_playersid'] != 0 && $gvalue ['occupied_end_time'] > $nowTime) {
/*hk*/						$value = array();
						$value ['status'] = 30;
						$value ['message'] = $city_lang['setZf_3'];
						//$showValue ['rsn'] = intval ( _get ( 'ssn' ) );
/*hk*/						return $value;
					} elseif (($gvalue['occupied_playersid'] == $playersid && $roleInfo['aggressor_general'] == $gid) || ($gvalue['occupied_playersid'] != 0 && $gvalue['occupied_playersid'] != $playersid && $gvalue['occupied_end_time'] > $_SGLOBAL['timestamp'])) {
						$value = array();
						$value['status'] = 1002;
						$value['message'] = $city_lang['setZf_4'];	
						return $value;		
					} elseif ($gvalue ['gohomeTime'] > $_SGLOBAL['timestamp'] && ($gvalue ['act'] == 4 || $gvalue ['act'] == 7) ) {
						$value = array();
						$value ['status'] = 30;
						$value ['message'] = $city_lang['setZf_5'];
						return $value;
					} elseif ($gvalue ['gohomeTime'] > $_SGLOBAL['timestamp'] && $gvalue ['act'] == 3 ) {
						$value = array();
						$value ['status'] = 30;
						$value ['message'] = $city_lang['setZf_6'];
						return $value;
					} elseif ($gvalue ['gohomeTime'] > $_SGLOBAL['timestamp'] && $gvalue ['act'] == 2 ) {
						$value = array();
						$value ['status'] = 30;
						$value ['message'] = $city_lang['setZf_7'];
						return $value;
					} elseif ( $gvalue ['act'] == 1 || $gvalue ['act'] == 6 ) {
						$value = array();
						$value ['status'] = 30;
						$value ['message'] = $city_lang['setZf_8'];/*hk*///设置武将换防条件限制
						return $value;
					}
					$gvalue ['occupied_playersid'] = $playersid;
					$gvalue ['occupied_player_level'] = $roleInfo ['player_level'];
					$gvalue ['occupied_player_nickname'] = mysql_escape_string($roleInfo ['nickname']);
					if ($roleInfo['aggressor_playersid'] == 0) {					
						$zxsj = $gvalue ['last_income_time'] = $nowTime;
						$gvalue ['occupied_end_time'] = $nowTime + 86400;
					} else {
						$zxsj = $gvalue ['last_income_time'] = $last_income_time;
						$gvalue ['occupied_end_time'] = $occupied_end_time;						
					}
					$allow = 1;
/*hk*/					$gname = $gvalue ['general_name'];//返回将领名称
				}
				if ($gvalue ['intID'] == $roleInfo['zf_aggressor_general'] && $gvalue ['intID'] != $gid) {
					$gvalue ['occupied_playersid'] = 0;
					$gvalue ['occupied_player_level'] = '';
					$gvalue ['occupied_player_nickname'] = '';
					$gvalue ['last_income_time'] = 0;
					$gvalue ['occupied_end_time'] = 0;		
					$allow2 = 1;
					$gid2 = $gvalue ['intID'];			
				}
				$newData [$gvalue ['sortid']] = $gvalue;
			}
			$common->updateMemCache ( MC . $playersid . '_general', $newData );	
			if ($allow == 1) {
				$value ['status'] = 0;
				$common->updatetable ( 'playergeneral', $update, $updateWhere );
				if ($allow2 == 1) {
					$updateGen ['occupied_playersid'] = 0;
					$updateGen ['occupied_player_level'] = '';
					$updateGen ['occupied_player_nickname'] = '';
					$updateGen ['last_income_time'] = 0;
					$updateGen ['occupied_end_time'] = 0;		
					$updateGenWhere['intID'] = $gid2;	
					$common->updatetable ( 'playergeneral', $updateGen, $updateGenWhere );	
				}				
				$value ['gid'] = $gid;
				$value ['time'] = 7200 - ($nowTime - $zxsj);
				if ($value ['time'] < 0) {
					$value ['time'] = 0;
				}
				$updateRole ['is_defend'] = 1;
				$updateRole ['end_defend_time'] = $nowTime;				
				$updateRole ['aggressor_general'] = $gid;
				$updateRole ['aggressor_level'] = $roleInfo ['player_level'];
				$updateRole ['aggressor_playersid'] = $playersid;
				$updateRole ['aggressor_nickname'] = mysql_escape_string($roleInfo ['nickname']);
				$updateRole ['zf_aggressor_general'] = $gid;
				$whereRole ['playersid'] = $playersid;
		    	/*$xwzt_15 = substr($roleInfo['xwzt'],14,1);  //完成 驻守行为
				if ($xwzt_15 == 0) {
					$updateRole['xwzt'] = substr_replace($roleInfo['xwzt'],'1',14,1);
					//$value['xwzt'] = $updateRole['xwzt'];
				}*/   				
				$common->updatetable ( 'player', $updateRole, $whereRole );
				$common->updateMemCache ( MC . $playersid, $updateRole );
				$roleInfo['is_defend'] = 1;
			    $rwid = questsController::OnFinish($roleInfo,"'wczs'");   //处理是否完成驻守任务     
			    if (!empty($rwid))	{
					$value['rwid'] = $rwid;
			    }		
		    	//完成引导脚本
				//$xyd = guideScript::wcjb($roleInfo,'ydzs',5,5);
				//接收新的引导
				/*$xyd = guideScript::xsjb ( $roleInfo, 'zbqh', 5);
				if ($xyd !== false) {
					$value['ydts'] = $xyd;
				}	*/						
			} else {
				$value ['status'] = 3;
				$value ['message'] = $sys_lang[7];
			}
		}
		return $value;
	}	
	
	//取消驻防
	/*public static function cancelZf($gid, $playersid) {
		global $common, $db, $_SGLOBAL;
		$generalInfo = cityModel::getGeneralData ( $playersid ,'','*');		
		$roleInfo ['playersid'] = $playersid;
		roleModel::getRoleInfo ( $roleInfo );
		$nowTime = $_SGLOBAL['timestamp'];
		if ($roleInfo ['aggressor_playersid'] != $playersid && $roleInfo ['aggressor_playersid'] != 0) {
			$value ['status'] = 1001;
			$value ['message'] = '您的山寨已被人占领，无法驻守！';
			$generalID = $roleInfo ['aggressor_general'];
			$incomeInfo = cityModel::occupied_income ( $generalID, $roleInfo ['aggressor_playersid'] );
			$msgRes = $db->query ( "SELECT `aggressor_message` FROM " . $common->tname ( 'aggressor_message' ) . " WHERE `playersid` = '" . $playersid . "' && `aggressor_playersid` = '" . $roleInfo ['aggressor_playersid'] . "' ORDER BY id DESC LIMIT 1" );
			$msgInfo = $db->fetch_array ( $msgRes );
			if (! empty ( $msgInfo )) {
				$zlxy = $msgInfo ['aggressor_message'];
			} else {
				$zlxy = '';
			}
			$value ['oi'] = array ('time' => $incomeInfo ['time'], 'uid' => $roleInfo ['aggressor_playersid'], 'ul' => $roleInfo ['aggressor_level'], 'un' => $roleInfo ['aggressor_nickname'], 'gid' => $generalID, 'zlxy' => $zlxy );
		} else {
			$updateRole ['is_defend'] = 0;
			$updateRole ['end_defend_time'] = 0;
			$updateRole ['aggressor_playersid'] = 0;
			$updateRole ['aggressor_nickname'] = '';
			$updateRole ['aggressor_level'] = 0;
			$updateRole ['aggressor_general'] = 0;
			//if ($roleInfo['end_defend_time'] <= time()) {
			// $updateRole['coins'] = $roleInfo['coins'] + 1000; //驻防收益，值暂未写入
			 //}
			$updateRole ['strategy'] = 0;
			$updateRoleWhere ['playersid'] = $playersid;
			$allow = 0;
			foreach ( $generalInfo as $key => $gvalue ) {
				if ($gvalue ['intID'] == $gid) {
					$gvalue ['occupied_playersid'] = $updateGen ['occupied_playersid'] = 0;
					$gvalue ['occupied_player_level'] = $updateGen ['occupied_player_level'] = 0;
					$gvalue ['occupied_player_nickname'] = $updateGen ['occupied_player_nickname'] = '';
					//$syjgsj = $nowTime - $gvalue ['last_income_time'];             //收益间隔时间
					//$sy = fightModel::zlsy($roleInfo['player_level'],1,$syjgsj);   
					//$hdsy = ceil($sy / 2);
					//$updateRole['coins'] = $roleInfo['coins'] + $hdsy;
					//$value['tq'] = intval($updateRole['coins']);
					//$value['hqtq'] = $hdsy;
					$gvalue ['last_income_time'] = $updateGen ['last_income_time'] = 0;
					$gvalue ['occupied_end_time'] = $updateGen ['occupied_end_time'] = 0;					
					$allow = 1;
				}
				$newData [$gvalue ['sortid']] = $gvalue;
				$common->updateMemCache ( MC . $playersid . '_general', $newData );
			}
			if ($allow == 1) {
				$where ['intID'] = $gid;
				$common->updatetable ( 'playergeneral', $updateGen, $where );
				$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
				$common->updateMemCache ( MC . $playersid, $updateRole );
				$value ['status'] = 0;
				$value ['gid'] = $gid;
				$rwid = questsController::OnFinish ( $roleInfo, "'wczs'" );
				if (! empty ( $rwid )) {
					$value ['rwid'] = intval ( $rwid );
				}				
			} else {
				$value ['status'] = 3;
				$value ['message'] = '非法请求！';
			}
		}
		return $value;
	}*/
	
	//取消占领
	public static function cancelZl($gid, $playersid, $toplayersid, $hyp) {
		global $common, $db, $mc, $_SGLOBAL, $sys_lang, $city_lang;
		if (empty($gid) || empty($toplayersid)) {
			return array('status'=>3,'message'=>$sys_lang[7]);
		}
		$general = cityModel::getGeneralData ( $playersid, '', $gid );
		$generalInfo = cityModel::getGeneralData ( $playersid,'','*' );
		//$common->updatetable('playergeneral',"`last_income_time` = '0',`occupied_playersid` = '0',`occupied_player_level` = '0',`occupied_player_nickname` = ''","`intID` = '$gid' && `playerid` = '$playersid'");
		//$toplayersid = $general[0]['occupied_playersid'];		
		if (! ($bzlInfo = $mc->get ( MC . $toplayersid ))) {
			$bzlInfo = $db->fetch_array ( $db->query ( "SELECT * FROM " . $common->tname ( 'player' ) . " WHERE `playersid` = '$toplayersid' LIMIT 1" ) );
		}
		//heroCommon::insertLog('zjpin+'.$playersid.'+dffff+'.$bzlInfo ['aggressor_playersid']);		
		if ($bzlInfo ['aggressor_playersid'] != $playersid) {
			$value ['status'] = 1001;
			$value ['message'] = $city_lang['cancelZl_1'];
			/*$generalID = $bzlInfo ['aggressor_general'];
			$incomeInfo = cityModel::occupied_income ( $generalID, $bzlInfo ['aggressor_playersid'] );
			$msgRes = $db->query ( "SELECT `aggressor_message` FROM " . $common->tname ( 'aggressor_message' ) . " WHERE `playersid` = '" . $playersid . "' && `aggressor_playersid` = '" . $bzlInfo ['aggressor_playersid'] . "' ORDER BY id DESC LIMIT 1" );
			$msgInfo = $db->fetch_array ( $msgRes );
			if (! empty ( $msgInfo )) {
				$zlxy = $msgInfo ['aggressor_message'];
			} else {
				$zlxy = '';
			}	*/			
			$zfInfo = cityModel::getZfInfo($toplayersid);	
			if ($zfInfo ['status'] == 0) {
				$value ['gid'] = $zfInfo ['gid'];
				$value ['giid'] = $zfInfo ['giid'];
				$value ['gname'] = $zfInfo ['gname']; //占领自己城池的将领名称
				$value ['glevel'] = $zfInfo ['glevel'];
				$value ['gxyd'] = $zfInfo ['gxyd'];
				$value ['ul'] = $zfInfo ['ul'];
				$value ['un'] = $zfInfo ['un'];
				$value['dzljtq'] = $zfInfo['sy'];
				//$value['mfzzz'] = $zfInfo['cl'];			
				$value['tqzzl'] = $zfInfo['tqzzl'];
				$value['time'] = $zfInfo['time'];
				$value['uid'] = $zfInfo['uid'];
				$value['gxj'] = $zfInfo['gxj'];
				$value['gzy'] = $zfInfo['gzy'];
				$value['zscl'] = $zfInfo['zscl'];
			} elseif ($zfInfo ['status'] == 1001) {
				$value ['uid'] = $zfInfo ['uid'];
				$value ['gid'] = $zfInfo ['gid'];
				$value ['giid'] = $zfInfo ['giid']; //占领自己城池的将领ICON id
				$value ['gname'] = $zfInfo ['gname']; //占领自己城池的将领名称
				$value ['glevel'] = $zfInfo ['glevel'];
				$value ['gxyd'] = $zfInfo ['gxyd'];
				$value ['un'] = $zfInfo ['un'];
				$value ['ul'] = $zfInfo ['ul'];
				$value ['time'] = $zfInfo ['time'];
				$value['dzljtq'] = $zfInfo['sy'];
				$value['gxj'] = $zfInfo['gxj'];
				$value['gzy'] = $zfInfo['gzy'];
				$value['zscl'] = $zfInfo['zscl'];
				//$value['mfzzz'] = $zfInfo['cl'];				
				if (! empty ( $zfInfo ['zlxy'] )) {
					$value ['zlxy'] = $zfInfo ['zlxy'];
				}
				$value['tqzzl'] = $zfInfo['tqzzl'];
				//$value ['uid'] = $zfInfo ['uid'];
			} else {
				$value ['status'] =1001;
			}
			$value['pid'] = $toplayersid;				
			//$value ['oi'] = array ('time' => $incomeInfo ['time'], 'uid' => $bzlInfo ['aggressor_playersid'], 'ul' => $bzlInfo ['aggressor_level'], 'un' => $bzlInfo ['aggressor_nickname'], 'gid' => $generalID, 'zlxy' => $zlxy );
		} else {
			$roleInfo ['playersid'] = $playersid;
			roleModel::getRoleInfo ( $roleInfo );			
			$nowTime = $_SGLOBAL['timestamp'];
			$yzsfsj =  86400 - ($bzlInfo['end_defend_time'] - $nowTime);  //已驻防时间
			if ($hyp == 1) {
				$silver = $roleInfo['silver'];
				if ($silver < 5) {
					$valueYp['status'] = 68;
					$valueYp['yp'] = $roleInfo['silver'];
					$valueYp['xyxhyp'] = 5;
					$arr1 = array('{xhyp}','{yp}');
					$arr2 = array('5',$valueYp ['yp']);
					$valueYp['message'] = str_replace($arr1,$arr2,$sys_lang[6]);	
					return $valueYp;				
				} else {
					$updatezl['silver'] = $roleInfo['silver'] - 5;
					$value['yp'] = intval($updatezl['silver']);
					$yzsfsj = 1800;
					$value['xhyp'] = 5;
				}
			}
			//$yzsfsj = 1800;			
			if ($yzsfsj < 1800) {
				$valueZf ['status'] = 1002;
				$valueZf ['message'] = $city_lang['cancelZl_2'];
				$valueZf ['xyyp'] = 5;
				return $valueZf;
			}						
			
			$updateRole ['aggressor_playersid'] = 0;
			$updateRole ['aggressor_nickname'] = '';
			$updateRole ['aggressor_level'] = 0;
			$updateRole ['aggressor_general'] = 0;
			$updateRole ['is_defend'] = 0;
			$updateRole ['end_defend_time'] = 0;
			$updateRole ['strategy'] = 0;
			$updateRoleWhere ['playersid'] = $toplayersid;
			/*设置回防*/
		    $bzlGinfo = cityModel::getGeneralData($bzlInfo['playersid'],false,'*');	    
		    $gen = 0;
		    if ($bzlInfo['zf_aggressor_general'] != 0) {		
		    	if (!empty($bzlGinfo)) {
		    		foreach ($bzlGinfo as $bzlGinfoValue) {
		    			if ($bzlGinfoValue['intID'] == $bzlInfo['zf_aggressor_general'] && $bzlGinfoValue['act'] == 0) {
				  			$updateRole['aggressor_playersid'] = $bzlInfo['playersid'];
				  			$updateRole['aggressor_nickname'] = mysql_escape_string($bzlInfo['nickname']);
				  			$updateRole['aggressor_level'] = $bzlGinfoValue['general_level']; 	  			 
							$updateRole['is_defend'] = 1;				
						    $updateRole['end_defend_time'] = $nowTime;		
						    $updateRole['zf_aggressor_general']	= $bzlGinfoValue['intID'];
						    $gen = 1;
							$bzlGinfoValue['occupied_playersid'] = $bzlupdateGen['occupied_playersid'] = $bzlInfo['playersid'];
							$bzlGinfoValue['occupied_player_level'] = $bzlupdateGen['occupied_player_level'] = $bzlInfo['player_level'];
							$bzlGinfoValue['occupied_player_nickname'] = $bzlupdateGen['occupied_player_nickname'] = mysql_escape_string($bzlInfo['nickname']);					 		 	
							$bzlGinfoValue['last_income_time'] = $bzlupdateGen['last_income_time'] = $nowTime; 
							$bzlGinfoValue['occupied_end_time'] = $bzlupdateGen['occupied_end_time'] = $nowTime;	
							$gzlnewdData[$bzlGinfoValue['sortid']] = $bzlGinfoValue;                 
						    $common->updateMemCache(MC.$bzlInfo['playersid'].'_general',$gzlnewdData);	
							$bzlwhere['intID'] = $bzlGinfoValue['intID'];
							$common->updatetable('playergeneral',$bzlupdateGen,$bzlwhere);  	
						    break;	    				
		    			}			    			
		    		}
		    	}	
		    	if ($gen == 1) {
    		    	$updateRole['strategy'] = $bzlInfo['zf_strategy'];
		    		$updateRole['aggressor_general'] = $bzlInfo['zf_aggressor_general'];		
		    	} else {
    		    	$updateRole['strategy'] = 0;
		    		$updateRole['aggressor_general'] = 0;					    		
		    	}		    	    	
		    } else {
		    	$updateRole['strategy'] = 0;
		    	//$updateBzl['aggressor_general'] = 0;			    	
		    	if (!empty($bzlGinfo)) {
		    		foreach ($bzlGinfo as $bzlGinfoValue) {
		    			if (($bzlGinfoValue['occupied_playersid'] == 0 || ($bzlGinfoValue['occupied_playersid'] != 0 && $bzlGinfoValue['occupied_playersid'] != $bzlInfo['playersid'] && $bzlGinfoValue['occupied_end_time'] < $nowTime)) && $bzlGinfoValue['act'] == 0) {
				  			$updateRole['aggressor_playersid'] = $bzlInfo['playersid'];
				  			$updateRole['aggressor_nickname'] = mysql_escape_string($bzlInfo['nickname']);
				  			$updateRole['aggressor_level'] = $bzlGinfoValue['general_level']; 	  			 
							$updateRole['is_defend'] = 1;				
						    $updateRole['end_defend_time'] = $nowTime;		
						    $updateRole['zf_aggressor_general']	= $bzlGinfoValue['intID'];
						    $updateRole['aggressor_general'] = $bzlGinfoValue['intID'];
						    $gen = 1;
							$bzlGinfoValue['occupied_playersid'] = $bzlupdateGen['occupied_playersid'] = $bzlInfo['playersid'];
							$bzlGinfoValue['occupied_player_level'] = $bzlupdateGen['occupied_player_level'] = $bzlInfo['player_level'];
							$bzlGinfoValue['occupied_player_nickname'] = $bzlupdateGen['occupied_player_nickname'] = mysql_escape_string($bzlInfo['nickname']);					 		 	
							$bzlGinfoValue['last_income_time'] = $bzlupdateGen['last_income_time'] = $nowTime; 
							$bzlGinfoValue['occupied_end_time'] = $bzlupdateGen['occupied_end_time'] = $nowTime;	
							$gzlnewdData[$bzlGinfoValue['sortid']] = $bzlGinfoValue;                 
						    $common->updateMemCache(MC.$bzlInfo['playersid'].'_general',$gzlnewdData);	
							$bzlwhere['intID'] = $bzlGinfoValue['intID'];
							$common->updatetable('playergeneral',$bzlupdateGen,$bzlwhere);  	
						    break;	    				
		    			}			    			
		    		}
		    	}
		    	if ($gen == 0) {
		    		$updateRole['aggressor_general'] = 0;
		    	}
		    }		  			
			//$updateBzlWhere['playersid'] = $toplayersid;
			//$common->updatetable('player',$updateBzl,$updateBzlWhere);
			//$common->updateMemCache(MC.$toplayersid,$updateRole);			
			/*设置回防结束*/
			$allow = 0;
			//print_r($generalInfo);
			//减少领地数量
			//fightModel::reduceLdsl($playersid,$toplayersid);
			foreach ( $generalInfo as $key => $gvalue ) {
				if ($gvalue ['intID'] == $gid) {
					$gvalue ['occupied_playersid'] = $updateGen ['occupied_playersid'] = 0;
					$gvalue ['occupied_player_level'] = $updateGen ['occupied_player_level'] = 0;
					$gvalue ['occupied_player_nickname'] = $updateGen ['occupied_player_nickname'] = '';
					//$incomeTimes = floor((time() - $gvalue['last_income_time']) / 14400) * 1000; //占领方收益,值未写入
					//$time = floor((time() - $gvalue['last_income_time']) / 14400);
					//$incomeTimes = fightModel::zlsy($roleInfo['player_level'],$roleInfo['prestige'],$time,2);
					$scsy = $gvalue ['last_income_time'];  //上次收益时间
					$gvalue ['last_income_time'] = $updateGen ['last_income_time'] = 0;
					$gvalue ['occupied_end_time'] = $updateGen ['occupied_end_time'] = 0;
					$allow = 1;					
				}
				$newData [$gvalue ['sortid']] = $gvalue;				
			}
			$common->updateMemCache ( MC . $playersid . '_general', $newData );
			if ($allow == 1) {				
				$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
				$common->updateMemCache ( MC . $toplayersid, $updateRole );
				$where ['intID'] = $gid;
				$common->updatetable ( 'playergeneral', $updateGen, $where );
				$syjg = $nowTime - $scsy;  //收益间隔时间
				$zlsy = fightModel::zlsy($bzlInfo['player_level'],2,$syjg);
				$hqtq = ceil($zlsy / 2);
				$updatezl['coins'] = $roleInfo['coins'] + $hqtq;
				if ($updatezl['coins'] > COINSUPLIMIT) {
					$updatezl['coins'] = COINSUPLIMIT;
					$hqtq = COINSUPLIMIT - $roleInfo['coins'];
				}
				$updatezlWhere['playersid'] = $playersid;
				$common->updatetable('player',$updatezl,$updatezlWhere);
				$common->updateMemCache(MC.$playersid,$updatezl);
				$value['tq'] = $updatezl['coins'];
				$value['hqtq'] = $hqtq; 
				$value ['status'] = 0;
				//$value ['gid'] = $gid;
				$zfInfo = cityModel::getZfInfo($toplayersid);	
				if ($zfInfo ['status'] == 0) {
					$value ['gid'] = $zfInfo ['gid'];
					$value ['giid'] = $zfInfo ['giid'];
					$value ['gname'] = $zfInfo ['gname']; //占领自己城池的将领名称
					$value ['glevel'] = $zfInfo ['glevel'];
					$value ['gxyd'] = $zfInfo ['gxyd'];
					$value ['ul'] = $zfInfo ['ul'];
					$value ['un'] = $zfInfo ['un'];
					$value['dzljtq'] = $zfInfo['sy'];
					//$value['mfzzz'] = $zfInfo['cl'];			
					$value['tqzzl'] = $zfInfo['tqzzl'];
					$value['time'] = $zfInfo['time'];
					$value['uid'] = $zfInfo['uid'];
					$value['gxj'] = $zfInfo['gxj'];
					$value['gzy'] = $zfInfo['gzy'];
					$value['zscl'] = $zfInfo['zscl'];
				} elseif ($zfInfo ['status'] == 1001) {
					$value ['uid'] = $zfInfo ['uid'];
					$value ['gid'] = $zfInfo ['gid'];
					$value ['giid'] = $zfInfo ['giid']; //占领自己城池的将领ICON id
					$value ['gname'] = $zfInfo ['gname']; //占领自己城池的将领名称
					$value ['glevel'] = $zfInfo ['glevel'];
					$value ['gxyd'] = $zfInfo ['gxyd'];
					$value ['un'] = $zfInfo ['un'];
					$value ['ul'] = $zfInfo ['ul'];
					$value ['time'] = $zfInfo ['time'];
					$value['dzljtq'] = $zfInfo['sy'];
					$value['gxj'] = $zfInfo['gxj'];
					$value['gzy'] = $zfInfo['gzy'];
					$value['zscl'] = $zfInfo['zscl'];
					//$value['mfzzz'] = $zfInfo['cl'];				
					if (! empty ( $zfInfo ['zlxy'] )) {
						$value ['zlxy'] = $zfInfo ['zlxy'];
					}
					$value['tqzzl'] = $zfInfo['tqzzl'];
					//$value ['uid'] = $zfInfo ['uid'];
				} else {
					$value ['status'] = 0;
				}
				$value['pid'] = $toplayersid;					
			} else {
				$value ['status'] = 3;
				$value ['message'] = $sys_lang[7];
			}
		}
		return $value;
	}
	//武将曲线
	public static function wjqx($level) {
		//return ceil ( 200 + 200 * 0.06 * $level );
		return 200;
	}
	
	//武将属性系数$bz：兵种
	public static function sxxs($bz) {
		switch ($bz) {
			//重甲
			case 1 :				
				$value ['gj'] = 0.25;
				$value ['fy'] = 0.25;
				$value ['tl'] = 0.3;
				$value ['mj'] = 0.2;
				break;
			//长枪
			case 2 :				
				$value ['gj'] = 0.3;
				$value ['fy'] = 0.2;
				$value ['tl'] = 0.3;
				$value ['mj'] = 0.2;
				break;
			//弓箭 
			case 3 :				
				$value ['gj'] = 0.25;
				$value ['fy'] = 0.15;
				$value ['tl'] = 0.25;
				$value ['mj'] = 0.35;
				break;
			//轻骑 
			case 4 :				
				$value ['gj'] = 0.3;
				$value ['fy'] = 0.15;
				$value ['tl'] = 0.3;
				$value ['mj'] = 0.25;
				break;
			//连弩
			default :				
				$value ['gj'] = 0.35;				
				$value ['fy'] = 0.2;
				$value ['tl'] = 0.2;
				$value ['mj'] = 0.25;
				break;
		}
		return $value;
	}
	//武将天赋
	public static function wjtf($tfdj, $wjdj) {
		//武将等级*武将天赋等级*职业属性系数
		//$xs = genModel::sxxs($zy);
		return $tfdj * $wjdj;
		//return round ( genModel::wjqx ( $wjdj ) * ($tfdj / 50 * 0.4), 0 );
	}
	
	//生成将领头标
	public static function generateAvatar($avatar_array, $sex) {
		$icon1 = array('BJ001','BJ002','BJ003');
		$icon2 = array('BJ004','BJ005','BJ006');
		if ($sex == 1) {
			$chosedKey = array_rand($icon1,1);
			$avatar = $icon1[$chosedKey];
		} else {
			$chosedKey = array_rand($icon2,1);
			$avatar = $icon2[$chosedKey];
		}
		return $avatar;
	}
	
	//生成将领职业
	public static function generateProfessional($professional_array) {
		$professional = rand ( 1, 5 );
		if (in_array ( $professional, $professional_array )) {
			$professional = genModel::generateProfessional ( $professional_array );
		}
		return $professional;
	}
	
	//生成招募将领
	public static function generateHireGeneral($playerid, $first_name_array, $last_name_array, $hired_general_array, $sortid, $updateType, $wjmc, $jgdj, $updateTime, $jwdj, $frd, $lssxcs, $zssxcs, $cssxcs, $num, &$ybys) {
		global $mc,$_SGLOBAL,$common,$city_lang,$city_var_lang;
		$HireGeneralInfo = $mc->get ( MC . $playerid . '_HireGeneral' );
		//print_r($HireGeneralInfo);
		//$mjarray = array ();
		if (empty ( $HireGeneralInfo )) {
			$sex_array = array ('***' );
			$avatar_array = array ('***' );
			$general_name_array = array ('***' );
			$professional_array = array ('***' );
			$mjarray = array ();			
		} else {
			//echo count($HireGeneralInfo);
			for($i = 0; $i < count ( $HireGeneralInfo ); $i ++) {
				//echo 'hello<br>';
				$sex_array [] = $HireGeneralInfo [$i] ['sex'];
				$avatar_array [] = $HireGeneralInfo [$i] ['avatar'];
				$general_name_array [] = $HireGeneralInfo [$i] ['general_name'];
				$professional_array [] = $HireGeneralInfo [$i] ['professional'];
				$mjarray [] = $HireGeneralInfo [$i] ['mj'];
				//echo $HireGeneralInfo[$i]['general_name'].'<br>';
			}
		}
		/*3初级名将卡：有较大的几率获得绿色名将、很大几率不获得名将；
		 4中级名将卡:有较小几率获得蓝色名将、较大几率获得绿色名将、很大几率不获得名将；
		 5高级名将卡:有较小几率获得紫色名将、稍大几率获得蓝色名将、较大几率获得绿色名将、很大几率不获得名将；
		 6英雄卡:有100%的几率获得蓝色名将；
		 7将军令:有100%的几率获得紫色名将
		 8武将卡:武将卡是针对具体名将，使用该道具可以100%的获得某个具体的名将
		 0刷将道具或正常刷新*/
		//$insert['understanding_value'] = rand(25,27);                                           //悟性
		$rand = rand ( 0, 99 );
		$tf = 0;
		$zd = 0;
		$sjtf = 0; 		
		$insert['jn1'] = 0;
		$insert['jn1_level'] = 0;
		$insert['general_level'] = 1;
		$bdxj = 0;
		if ($updateType == 3 && empty($mjarray)) { //3初级名将卡
			//$lsjl_rand = 100; //获得绿色将领几率
			/*$lsjv = 10;
			$ls5 = 28;
			$ls4 = 46;
			$ls3 = 54;
			$ls2 = 82;
			$ls1 = 100;
			if ($rand < $lsjv) {
				$mj = 1;
				$ys = 3;
				$sjtf = genModel::generateTf($jgdj,$mjarray,'',$ys,1);
			} elseif ($rand < $ls5) {
				$mj = 1;
				$ys = 2;
				$sjtf = genModel::generateTf($jgdj,$mjarray,'',$ys,5);
			} elseif ($rand < $ls4) {
				$mj = 1;
				$ys = 2;
				$sjtf = genModel::generateTf($jgdj,$mjarray,'',$ys,4);
			} elseif ($rand < $ls3) {
				$mj = 1;
				$ys = 2;
				$sjtf = genModel::generateTf($jgdj,$mjarray,'',$ys,2);
			} elseif ($rand < $ls2) {
				$mj = 1;
				$ys = 2;
				$sjtf = genModel::generateTf($jgdj,$mjarray,'',$ys,2);
			}  elseif ($rand < $ls1) {
				$mj = 1;
				$ys = 2;
				$sjtf = genModel::generateTf($jgdj,$mjarray,'',$ys,5);
			} else {
				$mj = 0;
				$ys = 1;
				$sjtf = genModel::generateTf($jgdj,$mjarray,'',$ys);
			}	*/
			if ($rand < 2) {
				$ys = 3;
			} else {
				$ys = 2;
			}
			$mj = 1;				
		} elseif ($updateType == 4 && empty($mjarray)) { //中级名将卡
			/*$zsjv = 10;
			$ls5 = 28;
			$ls4 = 46;
			$ls3 = 54;
			$ls2 = 82;
			$ls1 = 100;
			if ($rand < $zsjv) {
				$mj = 1;
				$ys = 4;
				$sjtf = genModel::generateTf($jgdj,$mjarray,'',$ys,1);
			} elseif ($rand < $ls5) {
				$mj = 1;
				$ys = 3;
				$sjtf = genModel::generateTf($jgdj,$mjarray,'',$ys,5);
			} elseif ($rand < $ls4) {
				$mj = 1;
				$ys = 3;
				$sjtf = genModel::generateTf($jgdj,$mjarray,'',$ys,4);
			} elseif ($rand < $ls3) {
				$mj = 1;
				$ys = 3;
				$sjtf = genModel::generateTf($jgdj,$mjarray,'',$ys,2);
			} elseif ($rand < $ls2) {
				$mj = 1;
				$ys = 3;
				$sjtf = genModel::generateTf($jgdj,$mjarray,'',$ys,2);
			}  elseif ($rand < $ls1) {
				$mj = 1;
				$ys = 3;
				$sjtf = genModel::generateTf($jgdj,$mjarray,'',$ys,5);
			} else {
				$mj = 0;
				$ys = 1;
				$sjtf = genModel::generateTf($jgdj,$mjarray,'',$ys);
			}	*/
			if ($rand < 2) {
				$ys = 4;
			} else {
				$ys = 3;
			}
			$mj = 1;
		} elseif ($updateType == 5 && empty($mjarray)) { //高级名将卡
			/*$zs5 = 20;
			$zs4 = 40;
			$zs3 = 60;
			$zs2 = 80;
			$zs1 = 100;
			if ($rand < $zs5) {
				$mj = 1;
				$ys = 4;
				$sjtf = genModel::generateTf($jgdj,$mjarray,'',$ys,5);
			} elseif ($rand < $zs4) {
				$mj = 1;
				$ys = 4;
				$sjtf = genModel::generateTf($jgdj,$mjarray,'',$ys,4);
			} elseif ($rand < $zs3) {
				$mj = 1;
				$ys = 4;
				$sjtf = genModel::generateTf($jgdj,$mjarray,'',$ys,3);
			} elseif ($rand < $zs2) {
				$mj = 1;
				$ys = 4;
				$sjtf = genModel::generateTf($jgdj,$mjarray,'',$ys,2);
			} elseif ($rand < $zs1) {
				$mj = 1;
				$ys = 4;
				$sjtf = genModel::generateTf($jgdj,$mjarray,'',$ys,1);
			} else {
				$mj = 0;
				$ys = 1;
				$sjtf = genModel::generateTf($jgdj,$mjarray,'',$ys);
			}*/
			$ys = 4;
			$mj = 1;		
		} elseif ($updateType == 10 && empty($mjarray)) {
			$mj = 1;
			$randnum = rand(0,99);
			if ($randnum < 20) {
				$nameInfo = yjlist ();
				$insert ['general_name'] = $city_var_lang['yjlist'][$nameInfo];
				if ($insert ['general_name'] == $city_lang['generateHireGeneral_3']) {
					$insert['jn1'] = 17;
				} else {
					$insert['jn1'] = 16;										
				}
				$insert['jn1_level'] = 1;
				$insert ['professional'] = substr ( $nameInfo, 1, 1 );
				$insert ['sex'] = substr ( $nameInfo, 2, 1 );
				$zdxj = $xj = substr ( $nameInfo, 3, 1 ); //星级
				$ys = substr ( $nameInfo, 0, 1 );
				$nameInfo = explode('_',$nameInfo);			
				$insert ['avatar'] = $nameInfo[1];	
				$tf = tfecsc($ys);
				$zd = 1;							
			} elseif ($randnum < 55) {
				$ys = 5;
			} else {				
				$ys = 4;
				$insert['general_level'] = 55;
				$sjtf = genModel::generateTf($jgdj,$mjarray,'',$ys,5);
				$bdxj = 1;
			}			
		} elseif (($updateType == 8 || $updateType == 9 || $updateType == 6) && empty ( $mjarray )) { //英雄卡
			//$nsjl_rand = 100;            //获得蓝色将领几率  
			$mj = 1;
			if ($updateType == 6) {
				$nameInfo = yjlist ();
				$insert ['general_name'] = $city_var_lang['yjlist'][$nameInfo];
				if ($insert ['general_name'] == $city_lang['generateHireGeneral_3']) {
					$insert['jn1'] = 17;
				} else {
					$insert['jn1'] = 16;										
				}
				$insert['jn1_level'] = 1;
			} else {
				$nameInfo = mjnamelist ( $wjmc );
				$insert ['general_name'] = $wjmc;
				if (strstr($insert ['general_name'],$city_lang['quickUpdateHireGeneral_4'])) {
					$insert ['general_name'] = str_replace($city_lang['quickUpdateHireGeneral_4'],'',$insert ['general_name']);
				}
			}		
			$insert ['professional'] = substr ( $nameInfo, 1, 1 );
			$insert ['sex'] = substr ( $nameInfo, 2, 1 );
			$zdxj = $xj = substr ( $nameInfo, 3, 1 ); //星级
			$ys = substr ( $nameInfo, 0, 1 );
			$nameInfo = explode('_',$nameInfo);			
			$insert ['avatar'] = $nameInfo[1];	
			/*if ($updateType == 8) {		
				$tf = genModel::generateTf($jgdj,$mjarray,'',$ys,'');
			} else {
				$tf = genModel::generateTf($jgdj,$mjarray,'',$ys,$zdxj);
			}*/
			if ($updateType == 9) {
				$tf = genModel::generateTf($jgdj,$mjarray,'',$ys,$zdxj);
			} else {
				$tf = tfecsc($ys);
			}					
			$zd = 1;
		} elseif ($updateType == 2) {  //使用元宝           
            $mjarray = array();
            if ($num == 0) {
            	$ybys = 0;
            	$sjtf = frdtf($frd, $lssxcs, $zssxcs, $cssxcs, $ybys); 
            } else {
            	$sjtf = genModel::generateTf($jgdj,$mjarray,1,'');
            }			
            $ys = actModel::hqwjpz($sjtf) + 1;
		    if ($ys > 1) {
            	$mj = 1;
            } else {
            	$mj = 0;
            }	
		} else {
			//默认刷将 
			$mjarray = array();  			                                       
			$sjtf = genModel::generateTf($jgdj,$mjarray,'','');
			$ys = actModel::hqwjpz($sjtf) + 1;
		    if ($ys > 1) {
            	$mj = 1;
            } else {
            	$mj = 0;
            }            	
            //$zd = 1;		
		}	
		$xj = 1;				    	
		if ($mj == 1 && $zd != 1) {
		//计算星级
			/*$xj = $sjtf % 5;
		    if ($xj == 0) {
			  $xj = 5;
		    }*/				
			if ($ys == 2) {
				$nameInfo = lsName ($xj ,$jwdj);			
			} elseif ($ys == 3) {
			    $nameInfo = nsName ($xj, $jwdj);			
			} elseif ($ys == 4) {
			    $nameInfo = zsName ($xj, $jwdj);				
			} else {
			    $nameInfo = csName ($xj, $jwdj);			
			} 
			
			$nameArray = explode ( '_', $nameInfo );
		    $insert ['professional'] = substr ( $nameInfo, 1, 1 );
		    $insert ['sex'] = substr ( $nameInfo, 2, 1 );
			$insert ['general_name'] = $nameArray [1];	
			if ($insert ['general_name'] == $city_lang['generateHireGeneral_1']) {
				$insert ['general_name'] = $city_lang['generateHireGeneral_2'];
			}		
			$insert ['avatar'] = $nameArray [2];
			/*$sjxj = substr ( $nameInfo, 3, 1 );
			if ($ys == 2) {
				$ksys = 15;
			} elseif ($ys == 3) {
				$ksys = 20;
			} elseif ($ys == 4) {
				$ksys = 25;
			} else {
				$ksys = 30;
			}*/
			if ($ys > 1 && $bdxj != 1) {
				$tf = tfecsc($ys);
			} else {
				$tf = $sjtf;
			}			
			//$zdxj = substr ( $nameInfo, 3, 1 );
			//$tf = genModel::generateTf($jgdj,$mjarray,'',$ys,$zdxj);
			//$tf = $sjtf;
		} else {
			if ($zd != 1) {
				$insert ['professional'] = genModel::generateProfessional ( $professional_array ); //将领职业 ，1，重甲将领  2，长枪将领  3，弓箭将领，4，轻骑将领，5，连弩将领 6，全能将领			
				$insert ['sex'] = genModel::generateSex ( $sex_array ); //性别1男性0女性
				$insert ['general_name'] = genModel::generateName ( $general_name_array, $insert ['sex'], $first_name_array, $last_name_array, $hired_general_array ); //姓名  			
			    $insert ['avatar'] = genModel::generateAvatar ( $avatar_array, $insert ['sex'] ); //头标			    
				if ($ys > 1) {
					$tf = tfecsc($ys);
				} else {
					$tf = $sjtf;
				}	
			    //$tf = genModel::generateTf($jgdj,$mjarray,'',$ys,$xj);			  
				/*$xj = $tf % 5;
			    if ($xj == 0) {
				  $xj = 5;
			    }*/				      
			}
		}
		$xj = $tf % 5;
	    if ($xj == 0) {
		  $xj = 5;
	    }		
		$insert ['understanding_value'] = $tf; //天赋	
		$attributeValue = genModel::sxxs ( $insert ['professional'] ); //生成属性值
		//$insert ['attack_value'] = round ( (genModel::wjqx ( 1 ) * 0.4 * $attributeValue ['gj']) + genModel::wjtf ( $insert ['understanding_value'], 1 ) * $attributeValue ['gj'], 0 ); //攻击值 
		$insert ['attack_value'] = genModel::hqwjsx(1,$insert ['understanding_value'],1,0,1,0,$attributeValue ['gj']);//攻击值
		//$insert ['defense_value'] = round ( (genModel::wjqx ( 1 ) * 0.4 * $attributeValue ['fy']) + genModel::wjtf ( $insert ['understanding_value'], 1 ) * $attributeValue ['fy'], 0 ); //防御值
		$insert ['defense_value'] = genModel::hqwjsx(1,$insert ['understanding_value'],1,0,1,0,$attributeValue ['fy']);//防御值
		//$insert ['physical_value'] = (genModel::wjqx ( 1 ) * 0.4 * $attributeValue ['tl']) + genModel::wjtf ( $insert ['understanding_value'], 1 ) * $attributeValue ['tl']; //体力值
		$insert ['physical_value'] = genModel::hqwjsx(1,$insert ['understanding_value'],1,0,1,0,$attributeValue ['tl']);//体力值		
		//$insert ['agility_value'] = round ( (genModel::wjqx ( 1 ) * 0.4 * $attributeValue ['mj']) + genModel::wjtf ( $insert ['understanding_value'], 1 ) * $attributeValue ['mj'], 0 ); //敏捷值
		$insert ['agility_value'] = genModel::hqwjsx(1,$insert ['understanding_value'],1,0,1,0,$attributeValue ['mj']);//敏捷值
		$insert ['mj'] = $mj;
		//CEILING(CEILING(((5+武将品质*5)+武将星级)*100*(武将品质*0.5)/100,1)*100,1)
		if ($mj == 0) {
			$pz = 1;
		} else {
			$pz = actModel::hqwjpz($tf) + 1;
		}
		/*$xj = $insert ['understanding_value'] % 5;
		if ($xj == 0) {
			$xj = 5;
		}*/
		$insert ['hftq'] = ceil ( ceil ( ((5 + $pz * 5) + $xj) * 100 * ($pz * 0.5) / 100 ) * 100 ); //需要花费的铜钱
		//$insert['motorize'] = $attributeValue['jdl'];                  //机动力
		//$insert['intID'] = $sortid;                                    //排序
		$array [$sortid] = $insert;
		if (is_array ( $HireGeneralInfo )) {
			$HireGeneralInfo = array_merge ( $HireGeneralInfo, $array );
			//print_r($array);
		} else {
			$HireGeneralInfo = $array;
		}
		
		$mc->set ( MC . $playerid . '_HireGeneral', $HireGeneralInfo, 0, 21600 );
		$saveData['playersid'] = $playerid;
		$saveData['ginfo'] = serialize($HireGeneralInfo);
		if ($updateTime == 0) {
			$updateTime = $_SGLOBAL['timestamp'];
		}
		$saveData['ctime'] = $updateTime + 21600;
		$common->inserttable('save_ginfo',$saveData,1,1);
		//print_r($saveData);
		//unset($HireGeneralInfo);
		unset ( $array );
		unset ( $sex_array );
		unset ( $avatar_array );
		unset ( $general_name_array );
		unset ( $professional_array );
		unset ( $insert );
	}
	
	//生成将领姓名
	public static function generateName($general_name_array, $sex, $first_name_array, $last_name_array, $hired_general_array) {
		$firset_name_id = rand ( 1, 76 );
		//根据性别获取名称ID
		if ($sex == 1) {
			$last_name_id = rand ( 1, 20 );
		} else {
			$last_name_id = rand ( 21, 40 );
		}
		$firs_name = $first_name_array [$firset_name_id];
		$last_name = $last_name_array [$last_name_id];
		$general_name = $firs_name . $last_name; //姓名;  
		//禁止姓名重复
		if (in_array ( $general_name, $general_name_array ) || in_array ( $general_name, $hired_general_array )) {
			$general_name = genModel::generateName ( $general_name_array, $sex, $first_name_array, $last_name_array, $hired_general_array );
		}
		return $general_name;
	}
	
	//生成将领性别
	public static function generateSex($sex_array) {
		$sex = rand ( 0, 1 ); //1为男性 0为女性
		$k = 0;
		for($i = 0; $i < count ( $sex_array ); $i ++) {
			if ($sex == $sex_array [$i]) {
				$k = $k + 1;
			} else {
				$k = $k + 0;
			}
		}
		if ($k > 4) {
			if ($sex == 1) {
				$sex = 0;
			} else {
				$sex = 1;
			}
		}
		return $sex;
	}
	
	//插入可招募将领数据$updateType支付方式1银两2元宝3初级名将卡4中级名将卡5高级名将卡6英雄卡7将军令8武将卡0刷将道具或正常刷新，$zy 职业 $ys颜色1绿色2蓝色3紫色 $jl几率$jwdj爵位等级
	public static function insertHireGeneralData($playerid, $updateType = 0, $wjmc = '', $jgdj, $updateTime = 0,$jwdj, $frd = 0, $lssxcs, $zssxcs, $cssxcs,&$ys = 0) {
		global $mc, $db, $common;
		$mc->delete ( MC . $playerid . '_HireGeneral' );
		$result_hired_general = cityModel::getGeneralData ( $playerid, 0, '*' );
		$hired_general_array = array ();
		for($i = 0; $i < count ( $result_hired_general ); $i ++) {
			$hired_general_array [] = $result_hired_general [$i] ['general_name'];
		}
		/*$result_hired_general = $db->query("SELECT intID FROM ".$common->tname('playergeneral')." WHERE playerid = '$playerid' LIMIT 6");
		 while ($rows_hired_general = $db->fetch_array($result_hired_general)) {
		 $hired_general_array[] = $rows_hired_general;
		 }*/
		if (empty ( $hired_general_array )) {
			$hired_general_array = array ('***' );
		}
		$first_name_array = firstName ();
		$last_name_array = lastName ();
		for($i = 0; $i < 3; $i ++) {
			genModel::generateHireGeneral ( $playerid, $first_name_array, $last_name_array, $hired_general_array, $i, $updateType, $wjmc, $jgdj, $updateTime, $jwdj, $frd, $lssxcs, $zssxcs, $cssxcs, $i, $ys );
		}
	}
	
	//设置将领空闲
	public static function setGeneralFree($playersid, $intID) {
		global $common, $mc, $city_lang;
		$ginfo = cityModel::getGeneralData ( $playersid,'','*' );
		$sortInfo = array ();
		$sortid = 0;
		foreach ( $ginfo as $key => $gvalue ) {
			/*if ($gvalue ['f_status'] == 1) {
				$sortidinfo [] = $gvalue ['general_sort'];
			}*/
			if ($gvalue ['f_status'] == 0) {
				$sortInfo [] = $gvalue ['general_sort'];
			}
			if ($gvalue ['intID'] == $intID) {
				$sortid = $gvalue ['general_sort']; //将领当前排序
				$f_status = $gvalue ['f_status']; //将领当前状态  	 		
			}
		}
		if ($f_status != 1) {
			$value ['status'] = 21;
			$value ['message'] = $city_lang['setGeneralFree_1'];
		} else {
			$value ['status'] = 0;
			$value ['gid'] = $intID;
			if (! empty ( $sortInfo )) {
				$maxID = max ( $sortInfo );
			} else {
				$maxID = 0;
			}
			$newID = $maxID + 1;
			$common->updatetable ( 'playergeneral', "`f_status` = '0',`general_sort` = '$newID'", "`intID` = '$intID'" );
			if ($sortid != 0) {
				$common->updatetable ( 'playergeneral', "`general_sort` = `general_sort` - 1", "`playerid` = '$playersid' && `general_sort` > '$sortid' && `f_status` = '1'" );
			}
			cityModel::getGeneralData ( $playersid, 1, '*', 1 );
		}
		return $value;
	}
	
	//设置将领出征
	public static function setGeneralFight($playersid, $intID) {
		global $common, $mc, $db, $city_lang, $sys_lang;
		$ginfo = cityModel::getGeneralData ( $playersid,'','*' );
		$sortidinfo = array();
		$oldsort = 0;
		foreach ( $ginfo as $key => $gvalue ) {
			if ($gvalue ['f_status'] == 1) {
				$sortidinfo [] = $gvalue ['general_sort'];
			}
			if ($gvalue ['intID'] == $intID) {
				$oldsort = $gvalue ['general_sort'];
			}
			$czztArray [] = array ('gid' => $gvalue ['intID'], 'zt' => $gvalue ['f_status'] );
		}
		$roleInfo['playersid'] = $playersid;
		$roleRes = roleModel::getRoleInfo($roleInfo);
		if ($roleRes == false) {
			return array('status'=>3,'message'=>$sys_lang[1]);
		}
		$player_level = $roleInfo['player_level'];
		$wjczsl = wjczsl($player_level);
		if (count ( $sortidinfo ) >= $wjczsl) {
			$value ['status'] = 1021;
			$value ['message'] = $city_lang['setGeneralFight_1'].$wjczsl.$city_lang['setGeneralFight_2'];
			$value ['czzt'] = $czztArray;
		} else {
			$value ['status'] = 0;
			$value ['gid'] = $intID;
			if (!empty($sortidinfo)) {			
				$maxID = max ( $sortidinfo );
			} else {
				$maxID = 0;
			}
			$newID = $maxID + 1;
			$common->updatetable ( 'playergeneral', "`general_sort` = '$newID',`f_status` = '1'", "intID = '$intID' && `f_status` = 0" );
			if ($oldsort != 0) {
				$common->updatetable ( 'playergeneral', "`general_sort` = `general_sort` - 1", "playerid = '$playersid' && `f_status` = 0 && `general_sort` > '$oldsort'" );
			}
		    //完成引导脚本
			//$xyd = guideScript::wcjb($roleInfo,'wjcz',10,10,'',false);
			//接收新的引导
			//$xyd = guideScript::jsydsj($roleInfo,'tsll',10,2); 
			//$xyd = guideScript::jsydsj($roleInfo,'cg');
			//if ($xyd !== false) {
				//$value['ydts'] = $xyd;
			//}	      			
			cityModel::getGeneralData ( $playersid, 1, '*', 1 );
			$roleInfo['rwsl'] = count ( $sortidinfo ) + 1;
			$rwid = questsController::OnFinish($roleInfo,"'wjczsl'");  //检查武将出征数量任务
		    if (!empty($rwid)) {
		         $value['rwid'] = $rwid;				             
		    }			
		}
		return $value;
	}
	
	//补血(当$id为空是为全体将领补血)
	public static function quickAddTroops($getInfo, $id) {
		global $common, $db, $mc, $G_PlayerMgr, $sys_lang, $city_lang;
		$playersid = $getInfo ['playersid'];
        $player = $G_PlayerMgr->GetPlayer($playersid);
		$jwdj = $player->baseinfo_['mg_level'];
		$jwInfo = jwmc($jwdj);
		$jwjc = 1 + $jwInfo['jc'] / 100;		
		$coins = $player->baseinfo_ ['coins'];
		$newData = array();
		if (! empty ( $id )) {
			//$gen_res = $db->query("SELECT physical_value,professional_level,general_life,understanding_value,general_level FROM ".$common->tname('playergeneral')." WHERE intID = '$id' && playerid = '$playersid' LIMIT 1");
			//$gen_rows = $db->fetch_array($gen_res);
			$gen_rows = cityModel::getGeneralData ( $playersid, false, $id );
			$zbtljcArray = array ();
			$zb1 = $gen_rows [0] ['helmet'];
			if ($zb1 != 0) {
				$zb1Info = $player->GetZBSX($zb1);
				$zbtljcArray [] = $zb1Info ['tl'];
			}
			$zb2 = $gen_rows [0] ['carapace'];
			if ($zb2 != 0) {
				$zb2Info = $player->GetZBSX($zb2 );
				$zbgjjcArray [] = $zb2Info ['gj'];
				$zbfyjcArray [] = $zb2Info ['fy'];
				$zbtljcArray [] = $zb2Info ['tl'];
				$zbmjjcArray [] = $zb2Info ['mj'];
			}
			$zb3 = $gen_rows [0] ['arms'];
			if ($zb3 != 0) {
				$zb3Info = $player->GetZBSX($zb3 );
				$zbtljcArray [] = $zb3Info ['tl'];
			}
			$zb4 = $gen_rows [0] ['shoes'];
			if ($zb4 != 0) {
				$zb4Info = $player->GetZBSX($zb4 );
				$zbtljcArray [] = $zb4Info ['tl'];
			}
			if (! empty ( $zbtljcArray )) {
				$zbtljc = array_sum ( $zbtljcArray );
			} else {
				$zbtljc = 0;
			}
			unset ( $zbtljcArray );
			if (empty ( $gen_rows )) {
				$value ['status'] = 21;
				$value ['message'] = $sys_lang[3];
				//$value ['rsn'] = intval ( _get ( 'ssn' ) );
				return $value;
			}
			//print_r($gen_rows);
			//$forcelv = $gen_rows [0] ['professional_level'];
			$currenLife = $gen_rows [0] ['general_life'];
			$dj = $gen_rows [0] ['general_level'];
			$tfz = $gen_rows [0] ['understanding_value'];
			$sxxs = genModel::sxxs ( $gen_rows [0] ['professional'] );
			//$tlz = ((genModel::wjqx ( $dj ) * 0.4 * $sxxs ['tl']) + genModel::wjtf ( $tfz, $dj ) * $sxxs ['tl']) * $jwjc  + $zbtljc;
			$jj = $gen_rows [0] ['professional_level'];
			$tlz = genModel::hqwjsx($dj,$tfz,$jj,$gen_rows [0] ['llcs'],$jwjc,$zbtljc,$sxxs ['tl'],$gen_rows [0] ['py_tl']);
			unset ( $sxxs );
			$lifeLimitUpValue = round ( $tlz, 0 ) * 10; //血量上限	   
			//echo '<br>'.$lifeLimitUpValue.'<br>';
			if ($currenLife >= $lifeLimitUpValue) {
				$value ['status'] = 21;
				$value ['message'] = $city_lang['quickAddTroops_1'];
				//$value ['rsn'] = intval ( _get ( 'ssn' ) );
				return $value;
			} else {
				$needLife = $lifeLimitUpValue - $currenLife; //需要的血量  	  
				//$lifePrice = addLifeCost($forcelv);
				$lifePrice = addLifeCost($player->baseinfo_['player_level']);
				$cost = $needLife * $lifePrice;
				if ($coins < $lifePrice) {
					$returnValueErr ['status'] = 1021;
					$returnValueErr ['yb'] = floor ( $player->baseinfo_ ['ingot'] );
					$returnValueErr ['yp'] = floor ( $player->baseinfo_ ['silver'] );
					$returnValueErr ['tq'] = floor ( $player->baseinfo_ ['coins'] );
					$returnValueErr ['jl'] = floor ( $player->baseinfo_ ['food'] );
					$returnValueErr ['message'] = $city_lang['quickAddTroops_2'];
					//$value ['rsn'] = intval ( _get ( 'ssn' ) );
					return $returnValueErr;
				} else {
					if ($coins >= $cost) {
						$value ['status'] = 0;
						$leftCoins = $coins - $cost;
						$updateGeneral ['general_life'] = round ( $lifeLimitUpValue, 0 );
					} else {
						//$value ['status'] = 1021;
						$value ['status'] = 0;
						$value ['yb'] = floor ( $player->baseinfo_ ['ingot'] );
						$value ['yp'] = floor ( $player->baseinfo_ ['silver'] );
						$value ['jl'] = floor ( $player->baseinfo_ ['food'] );
						//$value ['message'] = '您的铜钱不足，武将疗伤后生命值未满！';
						$addLife = floor ( $coins / $lifePrice );
						$leftCoins = floor ( $coins - ($addLife * $lifePrice) );
						if ($leftCoins < 0) {
							$leftCoins = 0;
						}
						//$value['tq'] = floor($leftCoins);
						$updateGeneral ['general_life'] = round ( $currenLife + $addLife, 0 );
					}
					$value ['smz'] = $updateGeneral ['general_life']; //当前生命值
					$value ['smsx'] = $lifeLimitUpValue; //生命值上限
					$gen_rows [0] ['general_life'] = $updateGeneral ['general_life'];
					$gen_rows [0] ['command_soldier'] = $updateGeneral ['general_life'];
					$newData [$gen_rows [0] ['sortid']] = $gen_rows [0];
					$common->updateMemCache ( MC . $playersid . '_general', $newData );
					$updateGeneralWhere ['intID'] = $id;
					$common->updatetable ( 'playergeneral', $updateGeneral, $updateGeneralWhere );
					$updateRoleWhere ['playersid'] = $playersid;
					$updateRole ['coins'] = floor ( $leftCoins );
					$value ['xhtq'] = floor ( $player->baseinfo_ ['coins'] - $updateRole ['coins'] );
					$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
					$common->updateMemCache ( MC . $playersid, $updateRole );
					$value ['tq'] = $updateRole ['coins'];
					$value ['gid'] = $id;
				}
				return $value;
			}
		} else {
			$generalData = cityModel::getGeneralData ( $playersid,'','*' );
			$generalInfo = array();
			foreach ($generalData as $generalDataValue) {
				if ($generalDataValue['f_status'] == 1) {
					$generalInfo[] = $generalDataValue;
				}
			}
			if (empty ( $generalInfo )) {
				$returnValueErr ['status'] = 21;
				$returnValueErr ['message'] = $sys_lang[3];
				//$value ['rsn'] = intval ( _get ( 'ssn' ) );
				return $returnValueErr;
			}
			//print_r($generalInfo);
			//$generalInfo = cityModel::getGeneralInfo($playersid,'intID,physical_value,professional_level,general_life,understanding_value,general_level');
			$value ['status'] = 0;
			$bxInfo = array ();
      static $zbslots = array('helmet','carapace','arms','shoes');
			for($i = 0; $i < count ( $generalInfo ); $i ++) {
				//print_r($generalInfo[0]);
				//$forcelv = $generalInfo [$i] ['professional_level'];
				$currenLife = $generalInfo [$i] ['general_life'];
				$sxxs = genModel::sxxs ( $generalInfo [$i] ['professional'] );
				$dj = $generalInfo [$i] ['general_level'];
				$tfz = $generalInfo [$i] ['understanding_value'];
				$zbtljcArray = array ();
        foreach ($zbslots as $slot) {
          $zbid = $generalInfo [$i] [$slot];
          if ($zbid != 0) {
            $zbInfo = $player->GetZBSX($zbid );
            $zbtljcArray [] = $zbInfo ['tl'];
          }
        }
        $zbtljc = array_sum ( $zbtljcArray );

				$jj = $generalInfo [$i] ['professional_level'];
				$tlz = genModel::hqwjsx($dj,$tfz,$jj,$generalInfo [$i] ['llcs'],$jwjc,$zbtljc,$sxxs ['tl'],$generalInfo [$i] ['py_tl']);	
				$jj = null;						
				unset ( $sxxs );
				$lifeLimitUpValue = round ( $tlz, 0 ) * 10;
				//$lifeLimitUpValue = round($generalInfo[$i]['physical_value']*((1+$generalInfo[$i]['understanding_value']*($generalInfo[$i]['general_level']-1)/100) + $forcelv / 2) + 100,0); //血量上限
				$needLife = $lifeLimitUpValue - $currenLife; //需要的血量 
				if ($needLife < 0) {
					$needLife = 0;
				} 	  
				//$lifePrice = addLifeCost($forcelv);
				$lifePrice = addLifeCost($player->baseinfo_['player_level']);
				$cost = 0;
				$cost = $needLife * $lifePrice;
				//$coins = $coins - $cost;
				if ($coins <= 0 && $i == 0) {
					$value ['status'] = 1021;
					$value ['yb'] = floor ( $player->baseinfo_ ['ingot'] );
					$value ['yp'] = floor ( $player->baseinfo_ ['silver'] );
					$value ['tq'] = floor ( $player->baseinfo_ ['coins'] );
					$value ['jl'] = floor ( $player->baseinfo_ ['food'] );
					$value ['message'] = $city_lang['quickAddTroops_2'];
					break;
				} elseif ($coins <= 0 && $i > 0) {
					//$value ['status'] = 1022;
					$value ['status'] = 0;
					$value ['yb'] = floor ( $player->baseinfo_ ['ingot'] );
					$value ['yp'] = floor ( $player->baseinfo_ ['silver'] );
					//$value['tq'] = 0;
					$value ['jl'] = floor ( $player->baseinfo_ ['food'] );
					//$value ['message'] = '您的铜钱不足，武将疗伤后生命值未满！';
					break;
				} else {
					$updateGeneralWhere ['intID'] = $generalInfo [$i] ['intID'];
					if ($coins >= $cost) {
						$updateGeneral ['general_life'] = $lifeLimitUpValue;
					} else {
						$updateGeneral ['general_life'] = floor ( $coins / $lifePrice );
						$coins = 0;
						//$value ['status'] = 1022;
						$value ['status'] = 0;
						//$value ['message'] = '您的铜钱不足，武将疗伤后生命值未满！';
					}
					$common->updatetable ( 'playergeneral', $updateGeneral, $updateGeneralWhere );
					$generalInfo [$i] ['general_life'] = $updateGeneral ['general_life'];
					$generalInfo [$i] ['command_soldier'] = $updateGeneral ['general_life'];
					$newData [$generalInfo [$i] ['sortid']] = $generalInfo [$i];					
					$bxInfo [] = array ('gid' => intval ( $generalInfo [$i] ['intID'] ), 'smz' => intval ( $updateGeneral ['general_life'] ) );
					unset ( $updateGeneralWhere );
					unset ( $updateGeneral );
					//unset ( $newData );
					if ($coins < $cost) {
						break;
					}
				}
				$coins = $coins - $cost;
				if (!empty($newData)) {
					$common->updateMemCache ( MC . $playersid . '_general', $newData );
				}
			}
			if (! empty ( $bxInfo )) {
				$value ['ginfo'] = $bxInfo;
			}
			if ($coins != $player->baseinfo_ ['coins']) {
				if ($coins < 0) {
					$coins = 0;
				}
				$updateRole ['coins'] = floor ( $coins );
				$updateRoleWhere ['playersid'] = $playersid;
				$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
				$common->updateMemCache ( MC . $playersid, $updateRole );
				$value ['tq'] = $updateRole ['coins'];
				$value ['xhtq'] = floor ( $player->baseinfo_ ['coins'] - $updateRole ['coins'] );
			}	
			return $value;
		}
	}
	//装备武将位置
	public static function zbpos($zbid) {
		switch ($zbid) {
			case 1 :
				$item = 'helmet';
				break;
			case 2 :
				$item = 'carapace';
				break;
			case 3 :
				$item = 'arms';
				break;
			case 4 :
				$item = 'shoes';
				break;
			default :
				$item = '';
				break;
		}
		return $item;
	}
	
	//设置武将装备
	public static function wjzb($playersid, $gid, $zb1, $zb2, $zb3, $zb4, $mzsl = '') {
		global $db, $mc, $common, $G_PlayerMgr, $sys_lang, $city_lang;
		$updateRole = array();
    	$player = $G_PlayerMgr->GetPlayer($playersid);
    	if(!$player)  return array ('status' => 3, 'message' => $sys_lang[1] );
    
		$ginfo = cityModel::getGeneralData ( $playersid, '', $gid );
		if (empty ( $ginfo )) 
			return array ('status' => 3, 'message' => $sys_lang[3] );
    
	    $oldzb = array_unique ( array ($ginfo [0] ['helmet'], $ginfo [0] ['carapace'], $ginfo [0] ['arms'], $ginfo [0] ['shoes'] ) );
	    $dqzy = $ginfo [0] ['professional']; //当前武将职业
	    $wjjb = $ginfo [0] ['general_level']; //武将级别
	    $zbsj = array ();
	    $zb1xx = explode ( '_', $zb1 );
	    $zb2xx = explode ( '_', $zb2 );
	    $zb3xx = explode ( '_', $zb3 );
	    $zb4xx = explode ( '_', $zb4 );
	    $zbarray = array($zb1xx, $zb2xx, $zb3xx, $zb4xx);
	    foreach($zbarray as $zbxx) {
	      if ($zbxx [1] != 0) {
	        $zbsj [] = $zbxx [1];
	        $zbInfo = $player->GetItem($zbxx [1]);
	        $zbProto = toolsModel::getItemInfo($zbInfo['ItemID']);
	        $zbpos = genModel::zbpos ( $zbProto ['EquipType'] );
			
			if($zbProto['ItemType'] == 4 && $zbProto['EquipType'] == 3) {
				if(!canEquipExclusiveWeapon($ginfo[0]['general_name'], $zbInfo['ItemID'])) {
					$value = array ('status' => 998, 'message' => $city_lang['wjzb_1'] . $zbProto['Description'] . $city_lang['wjzb_2'], 'rsn' => intval ( _get ( 'rsn' ) ) );
					return $value;
				}	
			}
			
	        if ($zbProto ['Zhiye'] != $dqzy && $zbProto ['Zhiye'] != 6) 
	          return array ('status' => 1021, 'message' => $city_lang['wjzb_3'], 'rsn' => intval ( _get ( 'rsn' ) ) );
	        if ($zbProto ['EquipType'] != $zbxx [0]) 
	          return array ('status' => 3, 'message' => $city_lang['wjzb_4'], 'rsn' => intval ( _get ( 'rsn' ) ) );
	        if ($zbProto ['ItemType'] != 4) 
	          return array ('status' => 3, 'message' => $sys_lang[7], 'rsn' => intval ( _get ( 'rsn' ) ) );
	        if ($zbProto ['LevelLimit'] > $wjjb) 
	          return array ('status' => 1021, 'message' => $city_lang['wjzb_5'], 'rsn' => intval ( _get ( 'rsn' ) ) );
	        if ($zbpos == "") 
	          return array ('status' => 3, 'message' => $sys_lang[7], 'rsn' => intval ( _get ( 'rsn' ) ) );
	        if ($zbInfo ['IsEquipped'] && $ginfo [0] [$zbpos] != $zbxx [1]) 
	          return array ('status' => 3, 'message' => $city_lang['wjzb_6'], 'rsn' => intval ( _get ( 'rsn' ) ) );
	
	        $updateGen [$zbpos] = $ginfo [0] [$zbpos] = $zbxx [1];
	      } else {
	        $zbpos = genModel::zbpos ( $zbxx [0] );
	        if ($zbpos == "") 
	          return array ('status' => 3, 'message' => $sys_lang[7], 'rsn' => intval ( _get ( 'rsn' ) ) );
	
	        $updateGen [$zbpos] = $ginfo [0] [$zbpos] = 0;
	      }
	      if ($zbpos == 'arms' && in_array($zbInfo['ItemID'],array(LGBOW_ITEMID, HALFMOON_ITEMID, RUNWIND_ITEMID, HITTIGER_ITEMID, ARCHLORD_ITEMID))) {
	      	$oldzbs = $_SESSION['zswq'];	
	      	$zswq = $_SESSION['zswq'] = $oldzbs + 1;
	      }
	      $zbInfo = null;
	      $zbProto = null;
	      $zbpos = null;
	    }
	
	    static $zbslots = array('helmet','carapace','arms','shoes');
	    $zbtljcArray = array ();
	    foreach ($zbslots as $slot) {
	      $zbid = $ginfo [0][$slot];
	      if ($zbid != 0) {
	        $zbInfo = $player->GetZBSX($zbid );
	        $zbtljcArray [] = $zbInfo ['tl'];
	      }
	    }
	    $zbtljc = array_sum ( $zbtljcArray );
        
		$roleInfo = $player->baseinfo_;
		//roleModel::getRoleInfo($roleInfo);
		$jwdj = $roleInfo['mg_level'];
		$jwInfo = jwmc($jwdj);
		$jwjc = 1 + $jwInfo['jc'] / 100;		
		$sxxs = genModel::sxxs ( $ginfo [0] ['professional'] );
		
		$jj = $ginfo [0] ['professional_level'];
		$tlz = genModel::hqwjsx($ginfo [0] ['general_level'],$ginfo [0] ['understanding_value'],$jj,$ginfo [0] ['llcs'],$jwjc,$zbtljc,$sxxs ['tl'],$ginfo [0] ['py_tl']);		
		
		$smsx = round ( $tlz, 0 ) * 10;
		if ($ginfo [0] ['general_life'] > $smsx) {
			$updateGen ['general_life'] = $ginfo [0] ['general_life'] = $smsx;
		}
		$value ['status'] = 0;
		$value ['gid'] = $gid;
		$value ['hp'] = $ginfo [0] ['general_life'];
		$value ['hpmax'] = $smsx;
		//$newzb = array($zb1,$zb2,$zb3,$zb4);
		$zbgx = 0;
		$playerBag = &$player->GetItems();
		$diffzbnew = array_diff ( $zbsj, $oldzb );
		if (! empty ( $zbsj )) {
			$value ['zbid'] = implode ( '_', $zbsj );
		}		
		$unitem = implode ( ',', $diffzbnew );
		if (! empty ( $diffzbnew ) && $unitem != 0) {
			foreach ($diffzbnew as $diffzbnewValue) {
				if ($diffzbnewValue != 0) {
					$playerBag[$diffzbnewValue]['IsEquipped'] = 1;	
					$zbgx = 1;
				}			
			}
			$common->updatetable ( 'player_items', "IsEquipped = '1'", "ID in ($unitem)" );
		}
		$common->updatetable ( 'playergeneral', $updateGen, "intID = '$gid'" );
		$newData [$ginfo [0] ['sortid']] = $ginfo [0];
		$common->updateMemCache ( MC . $playersid . '_general', $newData );
		$diffzbold = array_diff ( $oldzb, $zbsj );
		if (! empty ( $diffzbold )) {
			$uitem = implode ( ',', $diffzbold );
			$stopUPOld = 0;
		} else {
			$stopUPOld = 1;
		}
		if ($stopUPOld == 0 && $uitem != '0') {
			foreach ($diffzbold as $diffzboldValue) {
				if ($diffzboldValue != 0) {
					$playerBag[$diffzboldValue]['IsEquipped'] = 0;		
					$zbgx = 1;
				}		
			}					
			$common->updatetable ( 'player_items', "IsEquipped = '0'", "ID in (" . $uitem . ")" );
		}	
		if ($zbgx == 1) {
			$mc->set(MC.'items_'.$playersid,$playerBag,0,3600);	
		}
		//toolsModel::getMyItemInfo ( $playersid, '', true , true);
		$rwid = '';
		if ($mzsl == 2) {
			$getginfo = cityModel::getGeneralData($playersid,false,'*');
			$mz = 0;
			foreach ($getginfo as $getginfoValue) {
				if ($getginfoValue['helmet'] != 0 && $getginfoValue['carapace'] != 0 && $getginfoValue['arms'] != 0 && $getginfoValue['shoes'] != 0) {
					$mz++;
					if ($mz == 2) {
						break;
					}
				}
			}
			if($mz >= 2) {
		        $rwid = questsController::OnFinish ( $roleInfo, "'wjmz'" );	
		        if (substr($roleInfo['rwsj'],1,1) != 1) {
		        	$updateRole['rwsj'] = substr_replace($roleInfo['rwsj'],'1',1,1);
		        }
        		if (! empty ( $rwid )) {
					$value ['rwid'] =  $rwid;
				}		        
      		}
		}
		if (isset($zswq)) {
			$roleInfo['zswq'] = $zswq;
			$rwid = questsController::OnFinish ( $roleInfo, "'wjcdzb','zswq'" );
		} else {
			$rwid = questsController::OnFinish ( $roleInfo, "'wjcdzb'" );
		}
		if (! empty ( $rwid )) {
			$value ['rwid'] =  $rwid;
		}	
		/*$xwzt_11 = substr($roleInfo['xwzt'],10,1);
		if ($xwzt_11 == 0) {			
			$xwzt = substr_replace($roleInfo['xwzt'],'1',10,1);
			$updateRole['xwzt'] = $xwzt;
			//$value['xwzt'] = $xwzt;
		} */	
		if (!empty($updateRole)) {
			$common->updatetable('player',$updateRole,"playersid = '$playersid'");
			$common->updateMemCache(MC.$playersid,$updateRole);			
		}	
		/*if ($roleInfo['player_level'] == 1) {
			$xyd = guideScript::wcjb($roleInfo,'czb',1,$_SESSION['player_level']);	
			//接收新的引导
			$xyd = guideScript::xsjb($roleInfo,'ckrw',$_SESSION['player_level']);
			if ($xyd !== false) {
				$value['ydts'] = $xyd;
			}	
		}*/			
		return $value;
	}
	
	//生成玩家天赋$jgdj酒馆等级$mj名将信息$yb是否使用元宝$zdys指定颜色$zdxj指定星级
	public static function generateTf($jgdj, $mjarray, $yb, $zdys = '', $zdxj = '') {		
		$rand = rand ( 0, 999 ) / 1000;
        if (!empty($zdys) && empty ( $mjarray )) {
			if (empty($zdxj)) {
				if ($zdys == 1) {
					$tf = rand(11,15);
				} elseif ($zdys == 2) {
					$tf = rand(16,20);
				} elseif ($zdys == 3) {
					$tf = rand(21,25);
				} elseif ($zdys == 4) {
					$tf = rand(26,30);
				} elseif ($zdys == 5) {
					$tf = rand(31,35);
				} else {
					$tf = 0;
				}
			} else {
				if ($zdys == 1) {
					$tf = 10 + $zdxj;
				} elseif ($zdys == 2) {
					$tf = 15 + $zdxj;
				} elseif ($zdys == 3) {
					$tf = 20 + $zdxj;
				} elseif ($zdys == 4) {
					$tf = 25 + $zdxj;
				} elseif ($zdys == 5) {
					$tf = 30 + $zdxj;
				} else {
					$tf = 0;
				}				
			}
			
		} else {
			switch ($jgdj) {
				case 1 :
					if ($rand < 0.2/* && empty ( $mjarray )*/) {
						$tf = 16;
					} elseif ($rand < 0.6) {
						$tf = 15;
					} elseif ($rand < 0.7) {
						$tf = 14;
					} elseif ($rand < 0.8) {
						$tf = 13;
					} elseif ($rand < 0.9) {
						$tf = 12;
					} else {
						$tf = 11;
					}					
					break;
				case 2 :
					if ($rand < 0.2/* && empty ( $mjarray )*/) {
						$tf = 17;
					} elseif ($rand < 0.253 /*&& empty ( $mjarray )*/) {
						$tf = 16;
					} elseif ($rand < 0.6) {
						$tf = 15;
					} elseif ($rand < 0.7) {
						$tf = 14;
					} elseif ($rand < 0.8) {
						$tf = 13;
					} elseif ($rand < 0.9) {
						$tf = 12;
					} else {
						$tf = 11;
					} 							
					break;
				case 3 :
					if ($rand < 0.2 /*&& empty ( $mjarray )*/) {
						$tf = 18;
					} elseif ($rand < 0.54 /*&& empty ( $mjarray )*/) {
						$tf = 17;
					} elseif ($rand < 0.307 /*&& empty ( $mjarray )*/) {
						$tf = 16;
					} elseif ($rand < 0.6) {
						$tf = 15;
					} elseif ($rand < 0.7) {
						$tf = 14;
					} elseif($rand < 0.8) {
						$tf = 13;
					} elseif($rand < 0.9) {
						$tf = 12;
					} else {
						$tf = 11;
					}
					break;
				case 4 :
					if ($rand < 0.2 /*&& empty ( $mjarray )*/) {
						$tf = 19;
					} elseif ($rand < 0.254 /*&& empty ( $mjarray )*/) {
						$tf = 18;
					} elseif ($rand < 0.307 /*&& empty ( $mjarray )*/) {
						$tf = 17;
					} elseif ($rand < 0.36 /*&& empty ( $mjarray )*/) {
						$tf = 16;
					} elseif ($rand < 0.7) {
						$tf = 15;
					} elseif ($rand < 0.8) {
						$tf = 14;
					} elseif ($rand < 0.9) {
						$tf = 13;
					} else {
						$tf = 12;
					}
					break;
				case 5 :
					if ($rand < 0.1 /*&& empty ( $mjarray )*/) {
						$tf = 21;
					} elseif ($rand < 0.2 /*&& empty ( $mjarray )*/) {
						$tf = 20;
					} elseif ($rand < 0.254 /*&& empty ( $mjarray )*/) {
						$tf = 19;
					} elseif ($rand < 0.308 /*&& empty ( $mjarray )*/) {
						$tf = 18;
					} elseif ($rand < 0.361 /*&& empty ( $mjarray )*/) {
						$tf = 17;
					} elseif ($rand < 0.413 /*&& empty ( $mjarray )*/) {
						$tf = 16;
					} elseif ($rand < 0.7) {
						$tf = 15;
					} elseif ($rand < 0.8) {
						$tf = 14;
					} elseif ($rand < 0.9) {
						$tf = 13;
					} else {
						$tf = 12;
					}
					break;
				case 6 :
					if ($rand < 0.05 /*&& empty ( $mjarray )*/) {
						$tf = 22;
					} elseif ($rand < 0.155 /*&& empty ( $mjarray )*/) {
						$tf = 21;
					} elseif ($rand < 0.255 /*&& empty ( $mjarray )*/) {
						$tf = 20;
					} elseif ($rand < 0.308 /*&& empty ( $mjarray )*/) {
						$tf = 19;
					} elseif ($rand < 0.362 /*&& empty ( $mjarray )*/) {
						$tf = 18;
					} elseif ($rand < 0.414 /*&& empty ( $mjarray )*/) {
						$tf = 17;
					} elseif ($rand < 0.467 /*&& empty ( $mjarray )*/) {
						$tf = 16;
					} elseif ($rand < 0.7) {
						$tf = 15;
					} elseif ($rand < 0.8) {
						$tf = 14;
					} elseif ($rand < 0.9) {
						$tf = 13;
					} else {
						$tf = 12;
					}
					break;
				case 7 :
					if ($rand < 0.108 /*&& empty ( $mjarray )*/) {
						$tf = 22;
					} elseif ($rand < 0.209 /*&& empty ( $mjarray )*/) {
						$tf = 21;
					} elseif ($rand < 0.309 /*&& empty ( $mjarray )*/) {
						$tf = 20;
					} elseif ($rand < 0.363 /*&& empty ( $mjarray )*/) {
						$tf = 19;
					} elseif ($rand < 0.415 /*&& empty ( $mjarray )*/) {
						$tf = 18;
					} elseif ($rand < 0.468 /*&& empty ( $mjarray )*/) {
						$tf = 17;
					} elseif ($rand < 0.52 /*&& empty ( $mjarray )*/) {
						$tf = 16;
					} elseif ($rand < 0.8) {
						$tf = 15;
					} elseif ($rand < 0.9) {
						$tf = 14;
					} else {
						$tf = 13;
					}
					break;
				case 8 :
					if ($rand < 0.05 /*&& empty ( $mjarray )*/) {
						$tf = 23;
					} elseif ($rand < 0.165 /*&& empty ( $mjarray )*/) {
						$tf = 22;
					} elseif ($rand < 0.264 /*&& empty ( $mjarray )*/) {
						$tf = 21;
					} elseif ($rand < 0.364 /*&& empty ( $mjarray )*/) {
						$tf = 20;
					} elseif ($rand < 0.417 /*&& empty ( $mjarray )*/) {
						$tf = 19;
					} elseif ($rand < 0.469 /*&& empty ( $mjarray )*/) {
						$tf = 18;
					} elseif ($rand < 0.521 /*&& empty ( $mjarray )*/) {
						$tf = 17;
					} elseif ($rand < 0.537 /*&& empty ( $mjarray )*/) {
						$tf = 16;
					} elseif ($rand < 0.8) {
						$tf = 15;
					} elseif ($rand < 0.9) {
						$tf = 14;
					} else {
						$tf = 13;
					}
					break;
				case 9 :
					if ($rand < 0.113 /*&& empty ( $mjarray )*/) {
						$tf = 23;
					} elseif ($rand < 0.223 /*&& empty ( $mjarray )*/) {
						$tf = 22;
					} elseif ($rand < 0.318 /*&& empty ( $mjarray )*/) {
						$tf = 21;
					} elseif ($rand < 0.418 /*&& empty ( $mjarray )*/) {
						$tf = 20;
					} elseif ($rand < 0.471 /*&& empty ( $mjarray )*/) {
						$tf = 19;
					} elseif ($rand < 0.523 /*&& empty ( $mjarray )*/) {
						$tf = 18;
					} elseif ($rand < 0.575 /*&& empty ( $mjarray )*/) {
						$tf = 17;
					} elseif ($rand < 0.627 /*&& empty ( $mjarray )*/) {
						$tf = 16;
					} elseif ($rand < 0.8) {
						$tf = 15;
					} elseif ($rand < 0.9) {
						$tf = 14;
					} else {
						$tf = 13;
					}
					break;
				case 10 :
					if ($rand < 0.05 /*&& empty ( $mjarray )*/) {
						$tf = 24;
					} elseif ($rand < 0.175 /*&& empty ( $mjarray )*/) {
						$tf = 23;
					} elseif ($rand < 0.28 /*&& empty ( $mjarray )*/) {
						$tf = 22;
					} elseif ($rand < 0.373 /*&& empty ( $mjarray )*/) {
						$tf = 21;
					} elseif ($rand < 0.473 /*&& empty ( $mjarray )*/) {
						$tf = 20;
					} elseif ($rand < 0.525 /*&& empty ( $mjarray )*/) {
						$tf = 19;
					} elseif ($rand < 0.577 /*&& empty ( $mjarray )*/) {
						$tf = 18;
					} elseif ($rand < 0.629 /*&& empty ( $mjarray )*/) {
						$tf = 17;
					} elseif ($rand < 0.68 /*&& empty ( $mjarray )*/) {
						$tf = 16;
					} elseif ($rand < 0.9) {
						$tf = 15;
					} else {
						$tf = 14;
					}
					break;
				case 11 :
					if ($rand < 0.121 /*&& empty ( $mjarray )*/) {
						$tf = 25;
					} elseif ($rand < 0.238 /*&& empty ( $mjarray )*/) {
						$tf = 24;
					} elseif ($rand < 0.338 /*&& empty ( $mjarray )*/) {
						$tf = 23;
					} elseif ($rand < 0.427 /*&& empty ( $mjarray )*/) {
						$tf = 22;
					} elseif ($rand < 0.527 /*&& empty ( $mjarray )*/) {
						$tf = 21;
					} elseif ($rand < 0.579 /*&& empty ( $mjarray )*/) {
						$tf = 20;
					} elseif ($rand < 0.631 /*&& empty ( $mjarray )*/) {
						$tf = 19;
					} elseif ($rand < 0.682 /*&& empty ( $mjarray )*/) {
						$tf = 18;
					} elseif ($rand < 0.733 /*&& empty ( $mjarray )*/) {
						$tf = 17;
					} elseif ($rand < 0.9) {
						$tf = 16;
					} else {
						$tf = 15;
					}
					break;
				case 12 :
					if ($rand < 0.01 /*&& empty ( $mjarray )*/) {
						$tf = 26;
					} elseif ($rand < 0.15 /*&& empty ( $mjarray )*/) {
						$tf = 25;
					} elseif ($rand < 0.192 /*&& empty ( $mjarray )*/) {
						$tf = 24;
					} elseif ($rand < 0.3 /*&& empty ( $mjarray )*/) {
						$tf = 23;
					} elseif ($rand < 0.395 /*&& empty ( $mjarray )*/) {
						$tf = 22;
					} elseif ($rand < 0.482 /*&& empty ( $mjarray )*/) {
						$tf = 21;
					} elseif ($rand < 0.582 /*&& empty ( $mjarray )*/) {
						$tf = 20;
					} elseif ($rand < 0.633 /*&& empty ( $mjarray )*/) {
						$tf = 19;
					} elseif ($rand < 0.685 /*&& empty ( $mjarray )*/) {
						$tf = 18;
					} elseif ($rand < 0.736 /*&& empty ( $mjarray )*/) {
						$tf = 17;
					} elseif ($rand < 0.787) {
						$tf = 16;
					} else {
						$tf = 15;
					}
					break;
				case 13 :
					if ($rand < 0.083 /*&& empty ( $mjarray )*/) {
						$tf = 26;
					} elseif ($rand < 0.213 /*&& empty ( $mjarray )*/) {
						$tf = 25;
					} elseif ($rand < 0.263 /*&& empty ( $mjarray )*/) {
						$tf = 24;
					} elseif ($rand < 0.363 /*&& empty ( $mjarray )*/) {
						$tf = 23;
					} elseif ($rand < 0.453 /*&& empty ( $mjarray )*/) {
						$tf = 22;
					} elseif ($rand < 0.536 /*&& empty ( $mjarray )*/) {
						$tf = 21;
					} elseif ($rand < 0.636 /*&& empty ( $mjarray )*/) {
						$tf = 20;
					} elseif ($rand < 0.688 /*&& empty ( $mjarray )*/) {
						$tf = 19;
					} elseif ($rand < 0.738 /*&& empty ( $mjarray )*/) {
						$tf = 18;
					} elseif ($rand < 0.789 /*&& empty ( $mjarray )*/) {
						$tf = 17;
					} elseif ($rand < 0.84 /*&& empty ( $mjarray )*/) {
						$tf = 16;
					} else {
						$tf = 15;
					} 
					break;	
				case 14 :
					if ($rand < 0.155 /*&& empty ( $mjarray )*/) {
						$tf = 27;
					} elseif ($rand < 0.275 /*&& empty ( $mjarray )*/) {
						$tf = 26;
					} elseif ($rand < 0.333 /*&& empty ( $mjarray )*/) {
						$tf = 25;
					} elseif ($rand < 0.425 /*&& empty ( $mjarray )*/) {
						$tf = 24;
					} elseif ($rand < 0.51 /*&& empty ( $mjarray )*/) {
						$tf = 23;
					} elseif ($rand < 0.591 /*&& empty ( $mjarray )*/) {
						$tf = 22;
					} elseif ($rand < 0.691 /*&& empty ( $mjarray )*/) {
						$tf = 21;
					} elseif ($rand < 0.742 /*&& empty ( $mjarray )*/) {
						$tf = 20;
					} elseif ($rand < 0.792 /*&& empty ( $mjarray )*/) {
						$tf = 19;
					} elseif ($rand < 0.843 /*&& empty ( $mjarray )*/) {
						$tf = 18;
					} elseif ($rand < 0.893 /*&& empty ( $mjarray )*/) {
						$tf = 17;
					} else {
						$tf = 16;
					} 
					break;	
				case 15 :
					if ($rand < 0.01 /*&& empty ( $mjarray )*/) {
						$tf = 29;
					} elseif ($rand < 0.083 /*&& empty ( $mjarray )*/) {
						$tf = 28;
					} elseif ($rand < 0.155 /*&& empty ( $mjarray )*/) {
						$tf = 27;
					} elseif ($rand < 0.228 /*&& empty ( $mjarray )*/) {
						$tf = 26;
					} elseif ($rand < 0.338 /*&& empty ( $mjarray )*/) {
						$tf = 25;
					} elseif ($rand < 0.404 /*&& empty ( $mjarray )*/) {
						$tf = 24;
					} elseif ($rand < 0.488 /*&& empty ( $mjarray )*/) {
						$tf = 23;
					} elseif ($rand < 0.568 /*&& empty ( $mjarray )*/) {
						$tf = 22;
					} elseif ($rand < 0.645 /*&& empty ( $mjarray )*/) {
						$tf = 21;
					} elseif ($rand < 0.745 /*&& empty ( $mjarray )*/) {
						$tf = 20;
					} elseif ($rand < 0.796 /*&& empty ( $mjarray )*/) {
						$tf = 19;
					} elseif ($rand < 0.846 /*&& empty ( $mjarray )*/) {
						$tf = 18;
					} elseif ($rand < 0.896 /*&& empty ( $mjarray )*/) {
						$tf = 17;
					} elseif ($rand < 0.947 /*&& empty ( $mjarray )*/) {
						$tf = 16;
					} else {
						$tf = 15;
					} 
					break;
				case 16 :
					if ($rand < 0.01 /*&& empty ( $mjarray )*/) {
						$tf = 30;
					} elseif ($rand < 0.083 /*&& empty ( $mjarray )*/) {
						$tf = 29;
					} elseif ($rand < 0.155 /*&& empty ( $mjarray )*/) {
						$tf = 28;
					} elseif ($rand < 0.228 /*&& empty ( $mjarray )*/) {
						$tf = 27;
					} elseif ($rand < 0.3 /*&& empty ( $mjarray )*/) {
						$tf = 26;
					} elseif ($rand < 0.4 /*&& empty ( $mjarray )*/) {
						$tf = 25;
					} elseif ($rand < 0.475 /*&& empty ( $mjarray )*/) {
						$tf = 24;
					} elseif ($rand < 0.55 /*&& empty ( $mjarray )*/) {
						$tf = 23;
					} elseif ($rand < 0.625 /*&& empty ( $mjarray )*/) {
						$tf = 22;
					} elseif ($rand < 0.7 /*&& empty ( $mjarray )*/) {
						$tf = 21;
					} elseif ($rand < 0.8 /*&& empty ( $mjarray )*/) {
						$tf = 20;
					} elseif ($rand < 0.85 /*&& empty ( $mjarray )*/) {
						$tf = 19;
					} elseif ($rand < 0.9 /*&& empty ( $mjarray )*/) {
						$tf = 18;
					} elseif ($rand < 0.95 /*&& empty ( $mjarray )*/) {
						$tf = 17;
					} else {
						$tf = 16;
					} 
					break;																				
				default :
					$tf = 0;
					break;
			}
		}
		return $tf;		
	}
	
	//获取训练位信息
	public static function getxlw($playersid,$resetMac = false, $resetMc = true) {
		global $mc,$db,$common;
		if (!($xlwInfo = $mc->get(MC.$playersid.'_xlw')) || $resetMac == true) {
			$sql = "SELECT * FROM ".$common->tname('playerxlw')." WHERE playersid = '$playersid' LIMIT 9";
			$result = $db->query($sql);
			while ($rows = $db->fetch_array($result)) {
				$xlwInfo[$rows['intID']] = $rows;
			}
			if (!empty($resetMc)) {
				$mc->set(MC.$playersid.'_xlw',$xlwInfo,0,3600);
			}
		}
		return $xlwInfo;
	}
	
	//开始武将训练
	public static function wjxl($playersid,$gid,$yb,$xlsc,$px) {
		global $mc,$db,$common,$_SGLOBAL,$city_lang,$sys_lang;
		$value['status'] = 0;
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
		$nowTime = $_SGLOBAL['timestamp'];
		$xlwInfo = genModel::getxlw($playersid);
		$xx = 0;
		$new_level = 1;  //初始化武将级别
		//CEILING(POWER(A30,$M$26)*$N$26+A30*$O$26+$P$26,1)		
		$xlwid = array();
		foreach ($xlwInfo as $xlwValue) {
			//($xlwValue['gid'] == 0 && $xlwValue['end_time'] > $nowTime) || $xlwValue['px'] == 1 || $xlwValue['px'] == 2 || ($xlwValue['px'] == 4 && $xlwValue['is_open'] == 1)
			if ($xlwValue['gid'] == 0 && $xlwValue['is_open'] == 1 && $xlwValue['px'] == $px) {			
				$xlwid[] = $xlwValue;					
			}
		}
		$ginfo = cityModel::getGeneralData($playersid,false,$gid);
		$current_general_level = $ginfo[0]['general_level'];
		$current_experience = $ginfo[0]['current_experience'];
	    $tf = $ginfo[0]['understanding_value'] - $ginfo[0]['llcs']; //武将天赋
	    $jbsx = wjsjsx($tf);   //该武将可升级的最高上限
		if (empty($ginfo)) {
			$value['status'] = 21;
			$value['message'] = $sys_lang[3];
		} elseif ($ginfo[0]['xl_end_time'] > $nowTime) {
			$value['status'] = 1021;
			$value['message'] = $city_lang['wjxl_1'];			
		} else {
			if ($xlsc == 22) {
				$xltq = 600;
			} else {
				$xltq = 200;
			}
			if ($ginfo[0]['xl_end_time'] != 0 && $ginfo[0]['xl_end_time'] <= $nowTime) {
				genModel::wcxl($playersid,$gid,0); //容错处理,如果发现此将更新训练位失败，则先完成再训练（低概率事件）
			}			
			if ($roleInfo['coins'] < $xltq) {
				$returnValueErr['status'] = 1021;
				$returnValueErr['tq'] = $roleInfo['coins'];
				$returnValueErr['message'] = $city_lang['wjxl_2'];
				return $returnValueErr;
			} else {
				//(1-70级武将经验总和/20/100,1)*100
				$baseIncomeExp24 = ceil(2213095 / 32 /100) * 100;
				$baseIncomeExp8 = ceil($baseIncomeExp24 / 3);
				if (!empty($yb)) {
					if ($roleInfo['ingot'] < 15) {
						$returnValueErr['status'] = 88;
						$arr1 = array('{xhyb}','{yb}');
						$arr2 = array('15',$roleInfo['ingot']);
						$returnValueErr['message'] = str_replace($arr1,$arr2,$sys_lang['2']);	
						$returnValueErr['yb'] = $roleInfo['ingot'];
						return $returnValueErr;					
					} else {
						$updateRole['ingot'] = $roleInfo['ingot'] - 15;						
						if ($xlsc == 22 && $roleInfo['vip'] != 0) {
							$incomeExp = $baseIncomeExp24 * (1 + 0.3);
						} else {
							if ($xlsc == 22) {
								$value['status'] = 1022;
								$value['message'] = $city_lang['wjxl_3'];
								//$value['vip'] = 0;
								//return $value;
							}
							//$incomeExp = ceil((pow($current_general_level,2.6)*6.6+$current_general_level*9000+8000) * (1 + 0.3));
							$incomeExp = $baseIncomeExp8 * (1 + 0.3);
							
						}
						$value['yb'] = $updateRole['ingot'];
						$value['xhyb'] = 15;
					}
				} else {
					//CEILING(POWER(A32,$H$26)*$I$26+A32*$J$26+$K$26,1)
					if ($xlsc == 22 && $roleInfo['vip'] != 0 ) {
						$incomeExp = $baseIncomeExp24;
					} else {
						if ($xlsc == 22) {
							$value['status'] = 1022;
							$value['message'] = $city_lang['wjxl_3'];
							//$value['vip'] = 0;
							//return $value;
						}						
						$incomeExp = $baseIncomeExp8;
					}
				}
			}
	
			if (!empty($xlwid)) {
				if ($roleInfo['vip'] != 0 ) {
					if ($xlsc == 22) {
						$endTime = $nowTime + 79200;
						$value['mcdyp'] = 22 * 5;
					} else {						
						$endTime = $nowTime + 28800;
						$value['mcdyp'] = 8 * 5;
					}
				} else {
					if ($xlsc == 22) {
						$value['status'] = 1022;
						$value['message'] = $city_lang['wjxl_3'];
						//$value['vip'] = 0;
						//return $value;
					}						
					$endTime = $nowTime + 28800;
					$value['mcdyp'] = 8 * 5;
				}
				$xlw = $xlwid[0]['intID'];
				$updateXlw['gid'] = $xlwid[0]['gid'] = $gid;
				$updateXlw['g_end_time'] = $xlwid[0]['g_end_time'] = $endTime;
				$updateXlwWhere['intID'] = $xlw;
				$common->updatetable('playerxlw',$updateXlw,$updateXlwWhere);
				$xlwInfo[$xlwid[0]['intID']] = $xlwid[0];
				$common->updateMemCache(MC.$playersid.'_xlw',$xlwInfo);				
				//武将升级	
				$needJy = cityModel::getGeneralUpgradeExp($current_general_level);                //下级所需经验
				$value['hqjy'] = 0;
				if ($current_general_level < $jbsx) {
					 $newExp = ceil($current_experience + $incomeExp);
					 $g_hqjy = ceil($incomeExp);
					 $upLevel = $roleInfo['player_level'] + 8;
					 if ($upLevel > $jbsx) {
					 	$upLevel = $jbsx;
					 }
					 if ($current_general_level >= $upLevel) {
						if ($current_experience < $needJy) {							
							if ($newExp <= $needJy) {
								$updateGen['current_experience'] = $left_experience = $newExp;
								$value['hqjy'] = $g_hqjy;
								//$value['wjjy'] = ceil($incomeExp);
							} else {
								$updateGen['current_experience'] = $left_experience = $needJy;
								$value['hqjy'] = $newExp - $needJy;
								//$value['wjjy'] = $needJy - $current_experience;
							}							
						} else {
							$left_experience = $current_experience;
							$value['hqjy'] = 0;
							$value['nexp'] = 1;
						}				 	 	
					 } else {				 	 	
						$generalUpgrade = fightModel::upgradeRole($current_general_level,$newExp,2);
						$left_experience = $generalUpgrade['left'];
						$new_level = $generalUpgrade['level'];
						if ($new_level > $upLevel) {
							$new_level = $upLevel;
							$left_experience = 0;
						}
						/*$newJy = cityModel::getGeneralUpgradeExp($new_level);
						if ($newJy < $left_experience) {
							$left_experience = $newJy;
						}*/
						$updateGen['current_experience'] = $left_experience;		                     
						$upgrade = 0;
						if (intval($new_level) > intval($current_general_level) && intval($current_general_level) > 0) {
							$updateGen['general_level'] = $new_level;
						}
						$xhjy = array();
						for ($k = 0; $k < ($new_level - $current_general_level); $k++) {
							$xhjy[] = cityModel::getGeneralUpgradeExp($current_general_level + $k);
						}	
						//$wjhqjy[] = $g_hqjy;	
						if (!empty($xhjy)) {
							$totalExp =  array_sum($xhjy);        //所需要的总经验
						    $value['hqjy'] = $totalExp - $current_experience + $left_experience;
						} else {
							$value['hqjy'] = $g_hqjy;
						}									 	 			 	 	
					 }
					 unset($generalUpgrade);	//清空数值	
					 //$new_level = '';
					 $current_general_level = '';
					 $get_experience = ''; 
					 $current_experience = '';		                                       		                 	
				} else {
					$valueErr['status'] = 1021;
					$valueErr['message'] = $city_lang['wjxl_4'];
					return $valueErr; 
				}
				//武将升级结束	
				if ($_SESSION['player_level'] < 8) {
					$jnArray = array(1,2,4,6,7,13);
					$chosed = array_rand($jnArray);
					$jn = $jnArray[$chosed];
				} else {
					$jn = rand(1,15);
				}				
				$jn1 = $ginfo[0]['jn1'];
				$jn2 = $ginfo[0]['jn2'];			
				if ($jn1 == 0 && $jn != $jn2) {
					$updateGen['jn1'] = $jn;
					$updateGen['jn1_level'] = 1;
					$jnInfo = jnk($jn);
					$value['hqjn'] = $jnInfo['n'];
					$value['hqjniid'] = $jnInfo['iconid'];
					$xx = 1;
				} elseif ($jn2 == 0 && $jn != $jn1) {
					$updateGen['jn2'] = $jn;
					$updateGen['jn2_level'] = 1;	
					$jnInfo = jnk($jn);
					$value['hqjn'] = $jnInfo['n'];
					$value['hqjniid'] = $jnInfo['iconid'];	
					$xx = 1;
				}
				               
				$updateGen['xl_end_time'] = $endTime;
				$updateGenWhere['intID'] = $gid;
				$common->updatetable('playergeneral',$updateGen,$updateGenWhere);				
				$gList = cityModel::getGeneralList(array('playersid'=>$playersid), 0, true,$gid);				
				$value['gid'] = $gid;
				$value['bh'] = $xlwid[0]['px'];
				$value['xlsysj'] = $endTime - $nowTime;
				$value['ginfo'] = $gList['generals'];
				$roleInfo['wcsl'] = 1;
				if ($xx == 1) {				
	        		$rwid = questsController::OnFinish ( $roleInfo, "'xxjn','wjxl'" );
	        	} else {
	        		$rwid = questsController::OnFinish ( $roleInfo, "'wjxl'" );
	        	}
				if (! empty ( $rwid )) {
					$value ['rwid'] = $rwid;
				}
				$updateRole['coins'] = $roleInfo['coins'] - $xltq;
				$value['tq'] = $updateRole['coins'];
				$value['xhtq'] = $xltq;				 
				$updateRoleWhere['playersid'] = $playersid;
				$common->updatetable('player',$updateRole,$updateRoleWhere);
				$common->updateMemCache(MC.$playersid,$updateRole);				
		    	//完成引导脚本
				/*$xyd = guideScript::wcjb($roleInfo,'wcxl',4,$_SESSION['player_level']);
				//接收新的引导
				if ($roleInfo['player_level'] < 32) {
					$xyd = guideScript::xsjb($roleInfo,'sjdjt');
				} else {
					if ($new_level > 39) {						
						$xyd = guideScript::jsydsj($roleInfo,'hcwj',32,2);
					} else {
						$xyd = false;
					}
				}
				if ($xyd !== false) {
					$value['ydts'] = $xyd;
				}	*/				
				return $value;						
				//$common->updateMemCache(MC.$playersid.'_general',$ginfo);
			} else {
				$returnValue['status'] = 1021;
				$returnValue['message'] = $city_lang['wjxl_5'];
				return $returnValue;
			}
		}
		return $value;
	}
	
	//训练位开通$type(1元宝开通2道具开通3vip开通)
	/*public static function openxlw($playersid,$type,$djid = '',$vip_end_time = 0,$vip_level = 1) {
		global $common,$db,$mc,$_SGLOBAL;
		$nowTime = $_SGLOBAL['timestamp'];
		if ($type == 1) {
			$value['status'] = 0;
			$roleInfo['playersid'] = $playersid;
			roleModel::getRoleInfo($roleInfo);
			$ingot = $roleInfo['ingot'];
			if ($ingot < 1) {
				$value['status'] = 88;
				$value['message'] = '您需要消耗1元宝，目前您有'.$ingot.'元宝，请先充值？';
			} else {
				$update['ingot'] = $ingot - 1;
				$common->updatetable('player',$update,"playersid = '$playersid'");
				$common->updateMemCache(MC.$playersid,$update);
				$value['yb'] = $update['ingot'];
				$value['hfyb'] = 1;
				$common->updatetable('playerxlw',"is_open = '1'","playersid = '$playersid' && px = '3' LIMIT 1");
				$value['xlwbh'] = 4;
			}
		} elseif ($type == 2) {	
			//$jyl_amount = $itemInfo['EquipCount'];
			if ($jylsl === false) {
				$value['status'] = 1021;
				$value['message'] = '没有聚义令';
			} else {
				$value['status'] = 0;
				$end_time = $nowTime + 259200;
				//toolsModel::deleteBags($playersid, array(10015=>1));
				//$leftDj = $jyl_amount - 1;
				toolsModel::deleteBags($playersid, array(10015=>1),DELETE_BAG_YES_DO);
				$common->updatetable('playerxlw',"is_open = '1',end_time = '$end_time'","playersid = '$playersid' && px = '3' LIMIT 1");
			    $bg = toolsModel::getAllItems($playersid);
				$value['list'] = $bg['list'];
				$value['xlwbh'] = 3;
				$value['dqsj'] = intval($end_time);
			}			
		} elseif ($type == 3) {
			$value['status'] = 0;			
			for ($i = 1; $i < $vip_level + 1; $i++) {
				$pxarray[] = $i + 4;
			}
			$pxid = implode(',',$pxarray);
			//$common->updatetable('playerxlw',"is_open = '0',end_time = '0'","playersid = '$playersid' && end_time < '$nowTime' && gid = 0 && px in (5,6,7,8) LIMIT 4");
			$common->updatetable('playerxlw',"is_open = '1',end_time = '$vip_end_time'","playersid = '$playersid' && px in ($pxid) LIMIT 4");
		} else {
			$value['status'] = 3;
			$value['message'] = '非法操作';			
		}
		$xlwInfo = genModel::getxlw($playersid,true);
		return $value;
	}*/
	public static function openxlw($playersid,$bh) {
		global $common,$db,$mc,$_SGLOBAL,$city_lang,$sys_lang;
		$nowTime = $_SGLOBAL['timestamp'];		
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
		$vip = $roleInfo['vip'];		
		if ($vip == 0) {
			$value = array('status'=>21,'message'=>$city_lang['openxlw_1']);
			return $value;
		}
        switch ($bh) {
        	case 3:        		
        		$needYb = 1;
        	break;
         	case 4:
         		if ($vip < 2) {
					$value = array('status'=>3,'message'=>$city_lang['openxlw_2']);
					return $value;         			
         		}
        		$needYb = 100;
        	break;
        	case 5:
                if ($vip < 3) {
					$value = array('status'=>3,'message'=>$city_lang['openxlw_2']);
					return $value;         			
         		}        		
        		$needYb = 200;
        	break;
        	case 6:
                if ($vip < 3) {
					$value = array('status'=>3,'message'=>$city_lang['openxlw_2']);
					return $value;         			
         		}           		
        		$needYb = 300;
        	break;
        	case 7:
                if ($vip < 4) {
					$value = array('status'=>3,'message'=>$city_lang['openxlw_2']);
					return $value;         			
         		}            		
        		$needYb = 400;
        	break;
         	case 8:
         		if ($vip < 4) {
					$value = array('status'=>3,'message'=>$city_lang['openxlw_2']);
					return $value;         			
         		}    
        		$needYb = 500;
        	break;
         	case 9:
                if ($vip < 4) {
					$value = array('status'=>3,'message'=>$city_lang['openxlw_2']);
					return $value;         			
         		}    
        		$needYb = 600;
        	break;
        	default: 
        		$value = array('status'=>3,'message'=>$city_lang['openxlw_3']);
				return $value;   
        	break;      	       	        	        	        	       	
        }		
		$ingot = $roleInfo['ingot'];
		if ($ingot < $needYb) {
			$value['status'] = 88;
			$arr1 = array('{xhyb}','{yb}');
			$arr2 = array($needYb,$ingot);
			$value['message'] = str_replace($arr1,$arr2,$sys_lang[2]);
		} else {
			$value['status'] = 0;
			$update['ingot'] = $ingot - $needYb;
			$common->updatetable('player',$update,"playersid = '$playersid'");
			$common->updateMemCache(MC.$playersid,$update);
			$value['yb'] = $update['ingot'];
			$value['xhyb'] = $needYb;
			$common->updatetable('playerxlw',"is_open = '1'","playersid = '$playersid' && px = '$bh' LIMIT 1");
			$value['xlwbh'] = $bh;
		}
		$xlwInfo = genModel::getxlw($playersid,true);
		return $value;
	}	
	
	public static function wjhcFaild($playersid, $generalId) {
		global $db, $common, $mc, $_SGLOBAL, $sys_lang;
		
		$gerneralRows = cityModel::getGeneralData ( $playersid, 0, $generalId );
		$gerneralRowsTatol = cityModel::getGeneralData ( $playersid, 0, '*' );
		$zb1 = $gerneralRows [0] ['helmet'];
		$zb2 = $gerneralRows [0] ['carapace'];
		$zb3 = $gerneralRows [0] ['arms'];
		$zb4 = $gerneralRows [0] ['shoes'];
		$f_status = $gerneralRows [0] ['f_status'];
		//$xljssj = $gerneralRows [0] ['xl_end_time'];  //训练结束时间
		$zbsl = 0;
		$amount = array ();
		if ($zb1 > 0) {
			$amount [] = 1;
		}
		if ($zb2 > 0) {
			$amount [] = 1;
		}
		if ($zb3 > 0) {
			$amount [] = 1;
		}
		if ($zb4 > 0) {
			$amount [] = 1;
		}
		if (! empty ( $amount )) {
			$zbsl = array_sum ( $amount );
			$value ['haveZb'] = 1;
		} else {
			$value ['haveZb'] = 0;
		}
		$bbxx = toolsModel::getAllItems ( $playersid ); //获取背包数据
		if ($bbxx ['status'] == 1021) {
			$yygs = 0;
		} else {
			$yygs = count ( $bbxx ['list'] );
		}
		$totalBlocks = toolsModel::getBagTotalBlocks ( $playersid ); //玩家背包最大格数
		$sygs = $totalBlocks - $yygs; //背包剩余格数
		if (empty ( $gerneralRows )) {
			$value ['status'] = 4;
			$value ['message'] = $sys_lang[3];
		} elseif ($gerneralRows [0] ['occupied_playersid'] != 0 && $gerneralRows [0] ['occupied_playersid'] == $playersid) {
			$value ['status'] = 30;
//			$value ['message'] = '此将领正在驻守本城，不能解雇，请先撤防再解雇！';
		} elseif ($gerneralRows [0] ['occupied_playersid'] != 0 && $gerneralRows [0] ['occupied_playersid'] != $playersid && $gerneralRows [0] ['occupied_end_time'] > $_SGLOBAL['timestamp']) {
			$value ['status'] = 30;
//			$value ['message'] = '此将领正占领其它城池，不能解雇，请先撤防再解雇！';
		} elseif ($zbsl > $sygs) {
			$value ['status'] = 1030;
//			$value ['message'] = '背包格数已满，无法卸除将领装备，不能解雇！';
/*hk*/		} elseif ($gerneralRows [0] ['gohomeTime'] > $_SGLOBAL['timestamp'] && ($gerneralRows [0] ['act'] == 4 || $gerneralRows [0] ['act'] == 7) ) {
			$value ['status'] = 30;
//			$value ['message'] = '此将领正在回城途中，无法解雇！';
		} elseif ($gerneralRows [0] ['gohomeTime'] > $_SGLOBAL['timestamp'] && $gerneralRows [0] ['act'] == 3 ) {
			$value ['status'] = 30;
//			$value ['message'] = '此将领正在掠夺资源点途中，无法解雇！';
		} elseif ($gerneralRows [0] ['gohomeTime'] > $_SGLOBAL['timestamp'] && $gerneralRows [0] ['act'] == 2 ) {
			$value ['status'] = 30;
//			$value ['message'] = '此将领正在占领资源点途中，无法解雇！';
		} elseif ( $gerneralRows [0] ['act'] == 1 || $gerneralRows [0] ['act'] == 6 ) {
			$value ['status'] = 30;
//			$value ['message'] = '此将领正在占领资源点中，无法解雇！';/*hk*///
		} else {		
			$value['status'] = 0;
		}
		
		return $value;
	}
	
	//武将合成
	public static function wjhc($playersid,$mid,$sid,$hyb) {
		global $db, $mc, $common,$city_lang,$sys_lang;
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);		
		$isCheck = false;
		$update = array();
		if($playersid == '' || $mid == '' || $sid == '') {
			$value ['status'] = 2;
			return $value;
		}
		if($mid == $sid) {
			$value ['status'] = 2;
			return $value;
		}
		
		// 检查传入的正、副武将是否是该玩家的
		$midInfo = cityModel::getGeneralData($playersid,0,$mid);
		if (empty($midInfo)) {
			$value ['status'] = 2;      
			return $value;                  
		}
		/*if ($roleInfo['zf_aggressor_general'] == $sid) {
			$value ['status'] = 1021;    
			$value ['message'] = '副将为守备将领，无法合成！';  
			return $value; 					
		}*/
		/*if ($midInfo[0]['xl_end_time'] <> 0) {
			$value ['status'] = 2;    
			$value ['message'] = '主武将在训练，无法合成';    
			return $value;    
		}*/
		$tf = $midInfo[0]['understanding_value'] - $midInfo[0]['llcs'];
		if ($tf < 21) {
			$value ['status'] = 998;    
			$value ['message'] = $city_lang['wjhc_1'];  
			return $value; 			
		}
		/*if ($midInfo[0]['professional_level'] == hcsx()) {
			$value ['status'] = 2;    
			$value ['message'] = '主武将军阶达到上限';  
			return $value;    
		}*/
		if ($midInfo[0]['llcs'] <> $midInfo[0]['professional_level'] * 10) {
			$value ['status'] = 998;    
			$value ['message'] = $city_lang['wjhc_2'];    
			return $value;    
		}
		if ($tf >= 21 && $tf <= 25) {
			if ($midInfo[0]['professional_level'] > 1) {
				$value ['status'] = 998;    
				$value ['message'] = $city_lang['wjhc_3'];    
				return $value;    				
			}
		} elseif ($tf >= 26 && $tf <= 30) {
			if ($midInfo[0]['professional_level'] > 2) {
				$value ['status'] = 998;    
				$value ['message'] = $city_lang['wjhc_4'];    
				return $value;    				
			}			
		} else {
			if ($midInfo[0]['professional_level'] > 3) {
				$value ['status'] = 998;    
				$value ['message'] = $city_lang['wjhc_5'];    
				return $value;    				
			}					
		}
		
		/* if (intval($midInfo[0]['general_level']) < ((intval($midInfo[0]['professional_level']) * 10) + 10)) {
			$value ['status'] = 2;    
			$value ['message'] = '主武将未达到合成所需等级';    
			return $value;    
		} */
		
		// 主武将40级才可以合
		if($midInfo[0]['general_level'] < 40) {
			$value ['status'] = 998;
			$value ['message'] = str_replace('{dj}','40',$city_lang['wjhc_6']);
			return $value;
		}
		
		// 对主武将的级别限制
		if($midInfo[0]['professional_level'] == 2 && $midInfo[0]['general_level'] < 50) {
			$value ['status'] = 998;
			$value ['message'] = str_replace('{dj}','50',$city_lang['wjhc_6']);
			return $value;
		} else if ($midInfo[0]['professional_level'] == 3 && $midInfo[0]['general_level'] < 60) {
			$value ['status'] = 998;
			$value ['message'] = str_replace('{dj}','60',$city_lang['wjhc_6']);
			return $value;
		}
		
		$midPZ = actModel::hqwjpz($midInfo[0]['understanding_value'] - $midInfo[0]['llcs']);
		if($midPZ < 1) {
			$value ['status'] = 2;
			return $value;
		}
		$sidInfo = cityModel::getGeneralData($playersid,0,$sid);
		if (empty($sidInfo)) {
			$value ['status'] = 2;      
			return $value;                  
		}
		/*if ($sidInfo[0]['xl_end_time'] <> 0) {
			$value ['status'] = 2;    
			$value ['message'] = '副武将在训练，无法合成';    
			return $value;    
		}*/
		$sidPZ = actModel::hqwjpz($sidInfo[0]['understanding_value'] - $sidInfo[0]['llcs']);
		if ($midInfo[0]['professional_level'] == 1) {
			if ($sidPZ != 1 || ($sidInfo[0]['general_level'] < 40)) {
				$value ['status'] = 998;    
				//$value ['message'] = '此次合成需要1名40级的绿色武将做副将';    
				$value ['message'] = $city_lang['wjhc_7'];
				return $value;    				
			}
		} elseif ($midInfo[0]['professional_level'] == 2) {
			if ($sidPZ != 2 || ($sidInfo[0]['general_level'] < 50)) {
				$value ['status'] = 998;    
				//$value ['message'] = '此次合成需要1名50级的蓝色武将做副将';
				$value ['message'] = $city_lang['wjhc_7'];
				return $value;    				
			}			
		} elseif ($midInfo[0]['professional_level'] == 3) {
			if ($sidPZ != 3 || ($sidInfo[0]['general_level'] < 50)) {
				$value ['status'] = 998;    
				//$value ['message'] = '此次合成需要1名60级的紫色武将做副将';
				$value ['message'] = $city_lang['wjhc_7'];
				return $value;    				
			}					
		}

		$xh = hctq($midInfo[0]['professional_level']);
		if($xh == '') {
			$value ['status'] = 2;
			return $value;
		}
		if($roleInfo['coins'] < $xh[0]) {
			$returnValueErr ['status'] = 58;
			$returnValueErr ['message'] = $city_lang['wjhc_8'];
			$returnValueErr['xyxhtq'] = $xh[0];
			return $returnValueErr;
		}
				
		$update['coins'] = $roleInfo['coins'] - $xh[0];
		//echo($update['coins'].'</br>');
		if($hyb == 1) {
			if($roleInfo['ingot'] < $xh[1]) {
				$returnValueErr ['status'] = 88;
				$arr1 = array('{xhyb}','{yb}');
				$arr2 = array($xh[1],$roleInfo['ingot']);
				$returnValueErr ['message'] = str_replace($arr1,$arr2,$sys_lang[2]);//'您需要消耗'.$xh[1].'元宝，目前您有'.$roleInfo['ingot'].'元宝，请先充值？';
				return $returnValueErr;
			}
			$update['ingot'] = $roleInfo['ingot'] - $xh[1];
			$isCheck = true;
			$value ['xhyb'] = $xh[1];
			//echo($update['ingot'].'</br>');
		}else{
			$update['ingot'] = $roleInfo['ingot'];
			//professional_level
			$rand_jl = $xh[2];
			$rand = rand(0,99);
			if ($rand <= $rand_jl) {
				$isCheck = true;
			}
		}
		if($isCheck == false) {
			$value ['status'] = 2200;
			$value ['message'] = $city_lang['wjhc_9'];
			$value ['gid'] = $mid;
			//$showValue = genModel::fireGerneral($playersid,$sid);
			$showValue = genModel::wjhcFaild($playersid,$sid);
			
			if($showValue ['status'] <> 0) {
				if ($showValue['status'] == 30) {
					$showValue['message'] = $city_lang['wjhc_10'];
				}
				if ($showValue['status'] == 1030) {
					$showValue['message'] = $city_lang['wjhc_11'];
				}
				return $showValue;
			}
			
			$getInfo['playersid'] = $playersid;
	  		$showValue = cityModel::getGeneralList($getInfo,1,true);
	  		$garray = array();
	  		foreach ($showValue ['generals'] as $gValue) {
	  			if ($gValue['gid'] == $mid) {
	  				$garray[] = $gValue;
	  			} else {
	  				$garray[] = array('gid'=>$gValue['gid'],'px'=>$gValue['px']);
	  			}
	  		}
	  		$value ['generals'] = $garray;
	  		//$value ['generals'] = $showValue ['generals'];
	  		
	  		$where['playersid'] = $playersid;		
	  		$common->updatetable('player',$update,$where);
	  		$common->updateMemCache(MC.$playersid,$update);
	  		
	  		$value ['tq'] = $update['coins'];
	  		$value ['xhtq'] = $xh[0];
	  		
			return $value;
		}
		$showValue = genModel::fireGerneral($playersid,$sid);
		if($showValue ['status'] <> 0) {
			return $showValue;
		}
		/*$xwzt_19 = substr($roleInfo['xwzt'],18,1); 
		if ($xwzt_19 == 0) {
			$update['xwzt'] = substr_replace($roleInfo['xwzt'],'1',18,1);
		}*/	  		
		$where['playersid'] = $playersid;
		$common->updatetable('player',$update,$where);
		$common->updateMemCache(MC.$playersid,$update);
		
		$common->updatetable('playergeneral'," professional_level = professional_level + 1","intID = ".$mid." and playerid = '".$playersid."'");
		$midInfo[0]['professional_level'] = $midInfo[0]['professional_level'] + 1;
	  	$newData[$midInfo[0]['sortid']] = $midInfo[0];
	  	$common->updateMemCache(MC.$playersid.'_general',$newData);
	  	$value ['status'] = 0;
	  	$mc->delete(MC.$playersid.'_friendNotRepeat'); 
	  	$value ['yb'] = intval($update['ingot']);
	  	//$value ['xhyb'] = $xh[1];
	  	$value ['tq'] = $update['coins'];
	  	$value ['xhtq'] = $xh[0];
	  	$value ['gid'] = $mid;
	  	$value['cs'] = $midInfo[0]['professional_level'] - 1;
	  	$bagDataInfo = toolsModel::getAllItems($playersid, true);
	  	$value ['list'] = $bagDataInfo['list'];
	  	$getInfo['playersid'] = $playersid;
	  	$showValue = cityModel::getGeneralList($getInfo,1,true);
	  	$value ['generals'] = $showValue['generals'];
	  	
		// 合成任务控制
		if ($midInfo[0]['professional_level'] == 3) {
			$_SESSION['jj'] = $_SESSION['jj'] + 1;
			$roleInfo['jjsl'] = $_SESSION['jj'];
		}
		$roleInfo['rwsl'] = $cjInfo['hc'] = $midInfo[0]['professional_level'];
		achievementsModel::check_achieve($playersid,$cjInfo,array('hc'));		
		$rwid = questsController::OnFinish($roleInfo, "'hccs','sjjw','hc'");
		if(!empty($rwid)){
			$value['rwid'] = $rwid;
		}
		
		// 强化（军阶）成就相关					
		//achievementsModel::check_achieve($playersid, 'wjpy', 'hc', array('hc'=>intval($midInfo[0]['professional_level'])));

		$midPZ = actModel::hqwjpz($midInfo[0]['understanding_value'] - $midInfo[0]['llcs']);
		if($midPZ == 2) {  	
	  		lettersModel::setPublicNotice('<FONT COLOR="#ab7afe">['.$roleInfo['nickname'].']</FONT>'.$city_lang['wjhc_12'].'<FONT COLOR="#60acfe">['.$midInfo[0]['general_name'].']</FONT>'.$city_lang['wjhc_13'].'<FONT COLOR="#ffff00">[' . jjmc($midInfo[0]['professional_level']) . ']</FONT>');
		} else if($midPZ == 3) {
			lettersModel::setPublicNotice('<FONT COLOR="#ab7afe">['.$roleInfo['nickname'].']</FONT>'.$city_lang['wjhc_12'].'<FONT COLOR="#ff65f8">['.$midInfo[0]['general_name'].']</FONT>'.$city_lang['wjhc_13'].'<FONT COLOR="#ffff00">[' . jjmc($midInfo[0]['professional_level']) . ']</FONT>');
		} else if($midPZ == 4) {
			lettersModel::setPublicNotice('<FONT COLOR="#ab7afe">['.$roleInfo['nickname'].']</FONT>'.$city_lang['wjhc_12'].'<FONT COLOR="#ff8929">['.$midInfo[0]['general_name'].']</FONT>'.$city_lang['wjhc_13'].'<FONT COLOR="#ffff00">[' . jjmc($midInfo[0]['professional_level']) . ']</FONT>');
		}    		
		return $value;
	}
	//返回训练位信息
	public static function wjxlxx($playersid) {
		global $_SGLOBAL;
		$nowTime = $_SGLOBAL['timestamp'];
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
	 	$vip = $roleInfo['vip'];	 	
		$xlwInfo = genModel::getxlw($playersid);
		$value['status'] = 0;
		foreach ($xlwInfo as $xlwValue) {
			$bh = intval($xlwValue['px']);
            if ($xlwValue['is_open'] == 1) {
            	$sfky = 0;
            } elseif ($bh == 3) {
            	$sfky = 1;        
            } elseif ($bh == 4) {
            	$sfky = 100;          
            } elseif ($bh == 5 || $bh == 6) {
            	if ($bh == 5) {
            		$sfky = 200;
            	} else {
            		$sfky = 300;
            	}            	
            } else {     
            	if ($bh == 7) {
            		$sfky = 400;
            	} elseif ($bh == 8) {
            		$sfky = 500;
            	} else {
            		$sfky = 600;
            	}     
            }
			$gid = intval($xlwValue['gid']);
			$xlsysj = ($xlwValue['g_end_time'] - $nowTime > 0) ? ($xlwValue['g_end_time'] - $nowTime) : 0;
			$mcdyp = ceil($xlsysj / 3600) * 5;
			$list[] = array('bh'=>$bh,'sfky'=>$sfky,'gid'=>$gid,'xlsysj'=>$xlsysj,'mcdyp'=>$mcdyp);
		    $bh = NULL;
		    $sfky = NULL;
		    $gid = NULL;
		    $mcdyp = NULL;
		}
		$value['list'] = $list;
		return $value;
	}
	//完成训练
	public static function wcxl($playersid,$gid,$yp) {
		global $_SGLOBAL, $common, $db, $mc, $city_lang, $sys_lang;
		$nowTime = $_SGLOBAL['timestamp'];
		$xlwInfo = genModel::getxlw($playersid);
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
		$wbjs = 0;
		$xlwid = array();
		foreach ($xlwInfo as $xlwValue) {
			if ($xlwValue['gid'] == $gid) {
				$xlwid[] = $xlwValue;
			}
		}
		$ginfo = cityModel::getGeneralData($playersid,false,$gid);
		if (empty($ginfo) || empty($xlwid)) {
			$value['status'] = 21;
			$value['message'] = $sys_lang[3];
		} else {
			$lt = $xlwid[0]['g_end_time'] - $nowTime; //训练剩余时间
			if ($lt < 0) {
				$lt = 0;
			}
			$xyyp = ceil($lt / 3600) * 5;
			if (!empty($yp)) {
				if ($roleInfo['silver'] < $xyyp) {
					$valueYp['status'] = 68;						
					$valueYp['yp'] = $roleInfo['silver'];
					$valueYp['xyxhyp'] = $xyyp;
					$arr1 = array('{xhyp}','{yp}');
					$arr2 = array($xyyp,$valueYp ['yp']);
					$valueYp['message'] = str_replace($arr1,$arr2,$sys_lang[6]);
					return $valueYp;						
				} else {
					$updateRole['silver'] = $roleInfo['silver'] - $xyyp;
					$updateRoleWhere['playersid'] = $playersid;
					$common->updatetable('player',$updateRole,$updateRoleWhere);
					$common->updateMemCache(MC.$playersid,$updateRole);
					$value['yp'] = $updateRole['silver'];
					$value['xhyp'] = $xyyp;
					$wbjs = 1;
				} 
			} else {
				if ($xlwid[0]['g_end_time'] > $nowTime) {
					$valueLt['status'] = 1021;
					$valueLt['lt'] = $lt;
					$valueLt['message'] = $city_lang['wcxl_1'];			
					return $valueLt;	
				} 				
			}	
			//$value['sfky'] = 0;
			//$value['bh'] = $xlwid[0]['px'];
			//学习技能（需要取消）
			/*$jn = rand(1,14);
			$jn1 = $ginfo[0]['jn1'];
			$jn2 = $ginfo[0]['jn2'];			
			if ($jn1 == 0 && $jn != $jn2) {
				$value['status'] = 0;
				$updateGen['jn1'] = $ginfo[0]['jn1'] = $jn;
				$updateGen['jn1_level'] = $ginfo[0]['jn1_level'] = 1;
				$jnInfo = jnk($jn);
				$value['hqjn'] = $jnInfo['n'];
				$value['hqjniid'] = $jnInfo['iconid'];
				$xx = 1;
			} elseif ($jn2 == 0 && $jn != $jn1) {
				$value['status'] = 0;
				$updateGen['jn2'] = $ginfo[0]['jn2'] = $jn;
				$updateGen['jn2_level'] = $ginfo[0]['jn2_level'] = 1;	
				$jnInfo = jnk($jn);
				$value['hqjn'] = $jnInfo['n'];
				$value['hqjniid'] = $jnInfo['iconid'];	
			} else {
				$value['status'] = 1001;
			}	
         	*/		
			//学习技能结束（需要取消）	
			$updateGen['xl_end_time'] = $ginfo[0]['xl_end_time'] = 0;
			$common->updatetable('playergeneral',$updateGen,"intID = '$gid'");
			$common->updateMemCache(MC.$playersid."_general",array($ginfo[0]['sortid'] => $ginfo[0]));				
			$value['status'] = 0;	
			$updateXlw['gid'] = $xlwid[0]['gid'] = 0;
			$updateXlw['g_end_time'] = $xlwid[0]['g_end_time'] = 0;
			$updateXlwWhere['intID'] = $xlwid[0]['intID'];
			$common->updatetable('playerxlw',$updateXlw,$updateXlwWhere);	
			$common->updateMemCache(MC.$playersid.'_xlw',array($xlwid[0]['intID'] => $xlwid[0]));					
		}
		if ($wbjs == 1) {
			$rwid = questsController::OnFinish ( $roleInfo, "'jsxl'" );
		    if (! empty ( $rwid )) {
				$value ['rwid'] =  $rwid;
		    }				
		}
		return $value;		
	}	
	
	//获取武将合成信息
	public static function wjhcxx($playersid,$gid) {
		global $_SGLOBAL, $common, $db, $mc;
		$ginfo = cityModel::getGeneralData($playersid,false,$gid);
		$hctq = hctq($ginfo[0]['professional_level']);
		$value['status'] = 0;
		$value['cgl'] = $hctq[2];
		$value['xyyb'] = $hctq[1];
		$value['hctq'] = $hctq[0];
		return $value;
	}
	
    //秒历练CD
	public static function mllcd($playersid,$gid) {
		global $_SGLOBAL, $common, $db, $mc, $sys_lang;
		$ginfo = cityModel::getGeneralData($playersid,false,$gid);
		
		// 检查武将是否是该玩家的
		if(empty($ginfo)) {		
			$value['status'] = 21;
			$value['message'] = $sys_lang[3];
			return $value;
		}
		
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
		//
		$cd_xs = llcdxs($ginfo[0]['llcs']);
		$cd_second = $cd_xs * 3600;
		$difftime = ($ginfo[0]['last_end_ll'] + $cd_second) - $_SGLOBAL['timestamp'];
		$k_yp = llcd($difftime);
		if ($k_yp < 0) {
			$k_yp = 0;
		}
		//
		//$k_yp = llcd($ginfo[0]['llcs']);
		if($roleInfo['silver'] < $k_yp) {              //扣银票
			$valueYp['status'] = 68;
			$valueYp ['yp'] = $roleInfo['silver'];
			$valueYp['xyxhyp'] = $k_yp;
			$arr1 = array('{xhyp}','{yp}');
			$arr2 = array($k_yp,$valueYp ['yp']);
			$valueYp['message'] = str_replace($arr1,$arr2,$sys_lang[6]);
			return $valueYp;
		}
		$yp = $roleInfo['silver'] - $k_yp;
		$updateRole['silver'] = $roleInfo['silver'] = $yp;
		$common->updatetable('player',"silver = ".$yp,"playersid = '".$playersid."'");
		$common->updateMemCache(MC.$playersid,$updateRole);
		
		$update['last_end_ll'] = 0;
		$where['intID'] = $gid;
		$common->updatetable('playergeneral',$update,$where);
		
		$ginfo[0]['last_end_ll'] = "0";
	  	$newData[$ginfo[0]['sortid']] = $ginfo[0];
		$common->updateMemCache(MC.$playersid.'_general',$newData);	
		$value['status'] = 0;
		$value['gid'] = $gid;
		$value['yp'] = $yp;
		$value['xhyp'] = $k_yp;
		$rwid = questsController::OnFinish($roleInfo,"'jsll'");  //加速历练任务
	    if (!empty($rwid)) {
	         $value['rwid'] = $rwid;				             
	    }		
		return $value;
	}
	
	//获取武将历练冷却时间
	public static function lllqsh($playersid,$gid) {
		global $_SGLOBAL, $common, $db, $mc;
		$ginfo = cityModel::getGeneralData($playersid,false,$gid);
		
		$cd_xs = llcdxs($ginfo[0]['llcs']);
		$cd_second = $cd_xs * 3600;
		//echo $cd_xs . " " . $cd_second;
		if(($ginfo[0]['last_end_ll'] + $cd_second) < $_SGLOBAL['timestamp']) {
			$value['status'] = 0;
			$value['lt'] = 0;
		} else {
			$value['status'] = 0;
			$difftime = ($ginfo[0]['last_end_ll'] + $cd_second) - $_SGLOBAL['timestamp'];
			$cd_yp = llcd($difftime);
			$value['lt'] = $difftime;
			$value['xyyp'] =  $cd_yp;
		}
		return $value;
	}
	
	//获取武将训练信息
	public static function hqwjxlxx($playersid,$gid) {
		global $common, $sys_lang;
		$gInfo = cityModel::getGeneralData($playersid,false,$gid);
        if (empty($gInfo)) {
        	$value = array('status' => 3, 'message' => $sys_lang[3]);
        } else {
        	$value['status'] = 0;
        	$current_general_level = $gInfo[0]['general_level'];
			$baseIncomeExp24 = ceil(2213095 / 32 /100) * 100;
			$baseIncomeExp8 = ceil($baseIncomeExp24 / 3);
        	//$xltq = ceil(pow($current_general_level,2.08)*1.6+$current_general_level*18+180);       //花费铜钱
        	$value['gid'] = $gid;
        	$value['xlhf'] = array(array('sc'=>8,'tq'=>200,'jy'=>$baseIncomeExp8),array('sc'=>22,'tq'=>600,'jy'=>$baseIncomeExp24));
        	$value['ybjjy'] = array('yb'=>15,'jy'=>130);        	
        }
        return $value;
	}
	
	//设置防守策略
	public static function szfscl($playersid,$tuid,$st) {
		global $common, $sys_lang, $city_lang;
		if (empty($tuid)) {
			$roleInfo['playersid'] = $playersid;
			$roleRes = roleModel::getRoleInfo($roleInfo);
			if (empty($roleRes)) {
			   $value = array('status'=>3,'message'=>$sys_lang[1]);
			   return $value;
			}				
			$updateRoleWhere['playersid'] = $playersid;
			$tuid = $playersid;
		} else {
			$roleInfo['playersid'] = $tuid;
			$roleRes = roleModel::getRoleInfo($roleInfo,false);	
			if (empty($roleRes)) {
			   $value = array('status'=>3,'message'=>$sys_lang[1]);
			   return $value;
			}				
			$updateRoleWhere['playersid'] = $tuid;		
		}
		if ($roleInfo['aggressor_playersid'] != $playersid) {
			$value['status'] = 1021;
			$value['message'] = $city_lang['szfscl_1'];
			//当发现被其它人占领或驻守时返回当前城池信息
			//$dfInfo = cityModel::qqwjccxx($tuid);  //获取设置策略城池信息
			$dfInfo = cityModel::getZfInfo($tuid);   //获取设置策略城池信息
			$status1 = $dfInfo['status'];
			//$value['status1'] = $dfInfo['status'];
			if ($status1 == 1001) {
				$value ['uid'] = $dfInfo ['uid'];
				$value ['gid'] = $dfInfo ['gid'];
				$value ['giid'] = $dfInfo ['giid']; //占领自己城池的将领ICON id
				$value ['gname'] = $dfInfo ['gname']; //占领自己城池的将领名称
				$value ['glevel'] = $dfInfo ['glevel'];
				$value ['gxyd'] = $dfInfo ['gxyd'];
				$value ['un'] = $dfInfo ['un'];
				$value ['ul'] = $dfInfo ['ul'];
				$value ['time'] = $dfInfo ['time'];
				$value['dzljtq'] = $dfInfo['sy'];
				$value['gxj'] = $dfInfo['gxj'];
				$value['gzy'] = $dfInfo['gzy'];
				$value['zscl'] = $dfInfo['zscl'];
				//$value['mfzzz'] = $dfInfo['mfzzz'];				
				if (! empty ( $dfInfo ['zlxy'] )) {
					$value ['zlxy'] = $dfInfo ['zlxy'];
				}				
			} elseif ($status1 == 0) {
				$value ['gid'] = $dfInfo ['gid'];
				$value ['giid'] = $dfInfo ['giid'];
				$value ['gname'] = $dfInfo ['gname']; //占领自己城池的将领名称
				$value ['glevel'] = $dfInfo ['glevel'];
				$value ['gxyd'] = $dfInfo ['gxyd'];
				$value ['ul'] = $dfInfo ['ul'];
				$value ['un'] = $dfInfo ['un'];
				$value['dzljtq'] = $dfInfo['sy'];
				$value['time'] = $dfInfo['time'];
				$value['uid'] = $dfInfo['uid'];
				$value['gxj'] = $dfInfo['gxj'];
				$value['gzy'] = $dfInfo['gzy'];
				$value['zscl'] = $dfInfo['zscl'];
				//$value['mfzzz'] = $dfInfo['mfzzz'];					
			}
			$value['pid'] = $tuid;				
		} else {
			$value['status'] = 0;
			$updateRole['strategy'] = $st;			
			$common->updateMemCache(MC.$updateRoleWhere['playersid'],$updateRole);
			$common->updatetable('player',$updateRole,$updateRoleWhere);
			//$xyd = guideScript::xsjb($roleInfo,'ydjhy',6);
			/*$xyd = guideScript::xsjb ( $roleInfo, 'zbqh', 5 );
			if ($xyd !== false) {
				$value['ydts'] = $xyd;
			}*/			
		}		
		return $value;
	}	
	
	//武将属性计算
	/* $dj   	武将等级 
	 * $tf  	武将天赋
	 * $jj  	武将军阶
	 * $ll   	历练次数
	 * $jwjc   	爵位加成
	 * $zbsx    装备属性
	 * $zyxs 	职业系数	 
	 * $py_sx   培养属性
	 * */
	public static function hqwjsx($dj,$tf,$jj,$ll,$jwjc,$zbsx,$zyxs,$py_sx = 0) {	    
	    $cstf = $tf - $ll;           //出生天赋
	    $hcsx = $dj * ($jj - 1) * 5; //合成属性
	    $llsx = $dj * $ll * 0.5;     //历练属性
	    $cssx = $cstf * $dj;         //天生属性	    
		return ceil( ((80 + $cssx + $hcsx + $llsx) * $jwjc) * $zyxs ) + $zbsx + $py_sx; 
	}
	
	//武将的培养
	/* $gid 武将ID
	 * $pyfs培养方式
	 * */
	public static function pywj($playersid,$gid,$pyfs) {
		global $common, $G_PlayerMgr, $sys_lang, $city_lang;
		$player = $G_PlayerMgr->GetPlayer($playersid);
    	if(!$player) {
    		return array ('status' => 3, 'message' => $sys_lang[1] );	
    	} 
    	$roleInfo = $player->baseinfo_;	
    	$ginfo = cityModel::getGeneralData($playersid,false,$gid);
    	if (empty($ginfo)) {
    		return array ('status' => 3, 'message' => $sys_lang[3] );
    	}
    	if ($roleInfo['player_level'] < 18) {
    		return array ('status' => 30, 'message' => $city_lang['pywj_2'] );
    	}
    	$zc = $roleInfo['rank'];  //座次
    	if ($pyfs < 5) {
    		$whlx = $pyfs;
			$whiddata = array(1=>18580,2=>18581,3=>18582,4=>18583);
			$whid = $whiddata[$whlx];
			//toolsModel::getItemCount(玩家ID, itemid)/
			$djsl = toolsModel::getItemCount($playersid, $whid);
			if ($djsl > 0) {
				$player->DeleteItemByProto(array($whid => 1));	
				$value ['list'] = $player->GetClientBag();	
			} else {
				return array('status'=>30,'message'=>$city_lang['pywj_1']);
			}	    		
    	} else {
    		if ($pyfs == 5) {
    			$whlx = 1;    			
    		} elseif ($pyfs == 6) {
    			$whlx = 2;
    		} elseif ($pyfs == 7) {
    			$whlx = 3;
    		} else {
    			$whlx = 4;
    		}
    		$needYb = whyb($whlx);
    		if ($needYb > $roleInfo['ingot']) {
 				$returnValueErr ['status'] = 88;
 				$returnValueErr['yb'] = $roleInfo['ingot'];
				$arr1 = array('{xhyb}','{yb}');
				$arr2 = array($needYb,$roleInfo['ingot']);				
				$returnValueErr ['message'] = str_replace($arr1,$arr2,$sys_lang[2]);
				return $returnValueErr;  			
    		} else {
    			$newYb = $roleInfo['ingot'] - $needYb;
    			$updateRole['ingot'] = $newYb;
    			$updateRoleWhere['playersid'] = $playersid;
    			$common->updatetable('player',$updateRole,$updateRoleWhere);
    			$common->updateMemCache(MC.$playersid,$updateRole);
    			$value['yb'] = $newYb;
				$value['xhyb'] = $needYb;
    		}
    	}    	
    	$py_gj = $ginfo[0]['py_gj'];
    	$py_fy = $ginfo[0]['py_fy'];
    	$py_tl = $ginfo[0]['py_tl'];
    	$py_mj = $ginfo[0]['py_mj'];
    	$sjsxsx = zcpysjsx($zc,$whlx);  //随机最大上限
    	$pysxdata = array(zcpysx($zc),wjdjpysx($ginfo[0]['general_level']));
    	$kpysx = min($pysxdata);  //可培养单项属性的上限
    	if ($whlx < 4) {
    		$rand1 = rand(0,$sjsxsx);
    		$rand2 = rand(0,$sjsxsx);
    		$rand3 = rand(0,$sjsxsx);
    		$rand4 = rand(0,$sjsxsx);
    	} else {
    		$rand1 = 0;
    		$rand2 = 0;
    		$rand3 = 0;
    		$rand4 = 0;
    	}
    	//攻击增减
    	if ($rand1 >= $py_gj || $whlx == 4) {
    		if ($whlx == 4) {
    			$add_gj = 5;
    		} else {
    			$add_gj = rand(0,5);
    		}
    		$new_py_gj = $py_gj + $add_gj;
    		if ($new_py_gj > $kpysx) {
    			$new_py_gj = $kpysx;
        		$sj_add_gy = $kpysx - $py_gj;
    		} else {
    			$sj_add_gy = $add_gj;
    		}
    	} else {
    		$add_gj = rand(-5,-1);
        	$new_py_gj = $py_gj + $add_gj;
    		if ($new_py_gj < 0) {
    			$new_py_gj = 0;
    			$sj_add_gj = 0 - $py_gj;
    		} else {
    			$sj_add_gj = $add_gj;
    		}   	    		
    	}
    	$_SESSION[$gid.'_pygj'] = $new_py_gj;
    	//防御增减
    	if ($rand2 >= $py_fy || $whlx == 4) {
    		if ($whlx == 4) {
    			$add_fy = 5;
    		} else {
    			$add_fy = rand(0,5);
    		}
    		$new_py_fy = $py_fy + $add_fy;
    		if ($new_py_fy > $kpysx) {
    			$new_py_fy = $kpysx;
    			$sj_add_fy = $kpysx - $py_fy;
    		} else {
    			$sj_add_fy = $add_fy;
    		}
    	} else {
    		$add_fy = rand(-5,-1);
        	$new_py_fy = $py_fy + $add_fy;
    		if ($new_py_fy < 0) {
    			$new_py_fy = 0;
    			$sj_add_fy = 0 - $py_fy;
    		} else {
    			$sj_add_fy = $add_fy;
    		}   		
    	}
    	$_SESSION[$gid.'_pyfy'] = $new_py_fy;
		//体力增减
    	if ($rand3 >= $py_tl || $whlx == 4) {
    		if ($whlx == 4) {
    			$add_tl = 5;
    		} else {
    			$add_tl = rand(0,5);
    		}
    		$new_py_tl = $py_tl + $add_tl;
    		if ($new_py_tl > $kpysx) {
    			$new_py_tl = $kpysx;
        		$sj_add_tl = $kpysx - $py_tl;
    		} else {
    			$sj_add_tl = $add_tl;
    		}
    	} else {
    		$add_tl = rand(-5,-1);
        	$new_py_tl = $py_tl + $add_tl;
    		if ($new_py_tl < 0) {
    			$new_py_tl = 0;
    			$sj_add_tl = 0 - $py_tl;
    		} else {
    			$sj_add_tl = $add_tl;
    		}      		
    	}
    	$_SESSION[$gid.'_pytl'] = $new_py_tl;
		//敏捷增减
    	if ($rand4 >= $py_mj || $whlx == 4) {
    		if ($whlx == 4) {
    			$add_mj = 5;
    		} else {
    			$add_mj = rand(0,5);
    		}    		
    		$new_py_mj = $py_mj + $add_mj;
    		if ($new_py_mj > $kpysx) {
    			$new_py_mj = $kpysx;
            	$sj_add_mj = $kpysx - $py_mj;
    		} else {
    			$sj_add_mj = $add_mj;
    		}
    	} else {
    		$add_mj = rand(-5,-1);
        	$new_py_mj = $py_mj + $add_mj;
    		if ($new_py_mj < 0) {
    			$new_py_mj = 0;
        		$sj_add_mj = 0 - $py_mj;
    		} else {
    			$sj_add_mj = $add_mj;
    		}    		
    	} 
    	$_SESSION[$gid.'_pymj'] = $new_py_mj;  	
    	$value['status'] = 0;
    	$_SESSION[$gid.'_wjpy'] = 1;
    	$value['gj'] = $new_py_gj;
    	$value['fy'] = $new_py_fy;
    	$value['tl'] = $new_py_tl;
    	$value['mj'] = $new_py_mj; 
    	$rwid = questsController::OnFinish($roleInfo,"'wjpy'");    	
	    if (!empty($rwid)) {
	         $value['rwid'] = $rwid;				             
	    }       		
    	return $value;    	 
	}
	
	//确定培养
	public static function bcpy($playersid,$gid) {
		global $common, $G_PlayerMgr, $sys_lang, $city_lang;
    	$ginfo = cityModel::getGeneralData($playersid,false,$gid);
    	$player = $G_PlayerMgr->GetPlayer($playersid);
    	$roleInfo = $player->baseinfo_;
    	if (empty($ginfo)) {
    		return array ('status' => 3, 'message' => $sys_lang[3] );
    	}
    	if ($_SESSION[$gid.'_wjpy'] == 1) {
    		unset($_SESSION[$gid.'_wjpy']);
    		$new_py_gj = $_SESSION[$gid.'_pygj'];
    		$new_py_fy = $_SESSION[$gid.'_pyfy'];
    		$new_py_tl = $_SESSION[$gid.'_pytl'];
    		$new_py_mj = $_SESSION[$gid.'_pymj'];
    		unset($_SESSION[$gid.'_pygj']);
    		unset($_SESSION[$gid.'_pyfy']);
    		unset($_SESSION[$gid.'_pytl']);
    		unset($_SESSION[$gid.'_pymj']);
    		$updateGinfo['py_gj'] = $ginfo[0]['py_gj'] = $new_py_gj;
    		$updateGinfo['py_fy'] = $ginfo[0]['py_fy'] = $new_py_fy;
    		$updateGinfo['py_tl'] = $ginfo[0]['py_tl'] = $new_py_tl;
    		$updateGinfo['py_mj'] = $ginfo[0]['py_mj'] = $new_py_mj;
    		$updateGinfoWhere['intID'] = $ginfo[0]['intID'];
    		$common->updatetable('playergeneral',$updateGinfo,$updateGinfoWhere);
	  		$newData[$ginfo[0]['sortid']] = $ginfo[0];
			$common->updateMemCache(MC.$playersid.'_general',$newData);	
			$value['status'] = 0;
	    	$value['gj'] = intval($updateGinfo['py_gj']);
	    	$value['fy'] = intval($updateGinfo['py_fy']);
	    	$value['tl'] = intval($updateGinfo['py_tl']);
	    	$value['mj'] = intval($updateGinfo['py_mj']);
	    	$pysx = $cjInfo['py'] = $updateGinfo['py_gj'] + $updateGinfo['py_fy'] + $updateGinfo['py_tl'] + $updateGinfo['py_mj'];
        	achievementsModel::check_achieve($playersid,$cjInfo,array('py'));
	    	if ($pysx >= 200) {
	    		$roleInfo['py_200'] = $_SESSION['py_200'] = $_SESSION['py_200'] + 1;
	    	}	    	
	    	if ($pysx >= 400) {
	    		$roleInfo['py_400'] = $_SESSION['py_400'] = $_SESSION['py_400'] + 1;
	    	}
        	if ($pysx >= 600) {
	    		$roleInfo['py_600'] = $_SESSION['py_600'] = $_SESSION['py_600'] + 1;
	    	}
        	if ($pysx >= 800) {
	    		$roleInfo['py_800'] = $_SESSION['py_800'] = $_SESSION['py_800'] + 1;
	    	}
        	if ($pysx >= 1000) {
	    		$roleInfo['py_1000'] = $_SESSION['py_1000'] = $_SESSION['py_1000'] + 1;
	    	}	    	
        	if ($pysx >= 1200) {
	    		$roleInfo['py_1800'] = $_SESSION['py_1800'] = $_SESSION['py_1800'] + 1;
	    	}
        	if ($pysx >= 1400) {
	    		$roleInfo['py_1400'] = $_SESSION['py_1400'] = $_SESSION['py_1400'] + 1;
	    	}
        	if ($pysx >= 1600) {
	    		$roleInfo['py_1600'] = $_SESSION['py_1600'] = $_SESSION['py_1600'] + 1;
	    	}
        	if ($pysx >= 1800) {
	    		$roleInfo['py_1800'] = $_SESSION['py_1800'] = $_SESSION['py_1800'] + 1;
	    	}	    		    		    	
	    	$rwid = questsController::OnFinish($roleInfo,"'pysx'");    	
		    if (!empty($rwid)) {
		         $value['rwid'] = $rwid;				             
		    }   	    				
    	} else {
    		$value = array('status' => 30,'message'=>$city_lang['bcpy_1']);
    	}
    	return $value;	
	}
	
	//提升继承比率
	public static function tsjcbl($playersid,$yjts = false) {
		global $common, $G_PlayerMgr, $sys_lang, $city_lang;
		$player = $G_PlayerMgr->GetPlayer($playersid);
    	if(!$player) {
    		return array ('status' => 3, 'message' => $sys_lang[1] );	
    	} 
    	$roleInfo = $player->baseinfo_;
    	$yb = $roleInfo['ingot'];
    	$dqtsbl = $roleInfo['jcb'];    	
    	if ($dqtsbl == 100) {
    		return array('status'=>30,'message'=>$city_lang['tsjcbl_1']);
    	}
    	if (!empty($yjts)) {
    		$ybhf = jcyjts($dqtsbl);
    		$updateRole['jcb'] = 100;
    		$value['yjyb'] = 0;
    	} else {
    		$ybhf = jcybhf($dqtsbl);
    		$updateRole['jcb'] = $dqtsbl + 2;
    		$value['yjyb'] = jcyjts($updateRole['jcb']);
    	}		
    	if ($yb < $ybhf) {
 			$returnValueErr ['status'] = 88;
 			$returnValueErr['yb'] = $roleInfo['ingot'];
			$arr1 = array('{xhyb}','{yb}');
			$arr2 = array($ybhf,$yb);				
			$returnValueErr ['message'] = str_replace($arr1,$arr2,$sys_lang[2]);    
			return $returnValueErr;		
    	}
    	$updateRole['ingot'] = $yb - $ybhf;    	
    	$updateRoleWhere['playersid'] = $playersid;
    	$value['yb'] = $updateRole['ingot'];
    	$value['xhyb'] = intval($ybhf);
    	$common->updatetable('player',$updateRole,$updateRoleWhere);
    	$common->updateMemCache(MC.$playersid,$updateRole);
    	$value['xcyb'] = intval(jcybhf($updateRole['jcb']));
    	$value['jcbl'] = $updateRole['jcb'];
    	$value['status'] = 0;
    	return $value;
	}
	
	//武将属性传承$ccgid 传承武将ID $jcgid 继承武将ID
	public static function sxcc($playersid,$ccgid,$jcgid) {
		global $common, $G_PlayerMgr, $sys_lang, $city_lang, $mc;
		$player = $G_PlayerMgr->GetPlayer($playersid);
		if (!$player) {
			return array('status'=>30,'message' => $sys_lang[1]);
		} 
		$roleInfo = $player->baseinfo_;
		$tq = intval($roleInfo['coins']);
		if ($tq < 30000) {
			$value['status'] = 58;
			$value['xyxhtq'] = 30000;
			$value['tq'] = $tq;
			//您需要消耗{xhtq}银票，目前您有{tq}银票
			$arr1 = array('{xhtq}','{tq}');
			$arr2 = array('30000',$tq);
			$value['message'] = str_replace($arr1,$arr2,$sys_lang[15]);
			return ;
		}
		$wjInfo = cityModel::getGeneralData($playersid,false,'*');		
		if (empty($wjInfo)) {
			return array('status' => 30,'message'=>$sys_lang[3]);			
		}
		$ccwjinfo = $jcwjinfo = array();
		foreach ($wjInfo as $wjInfoValue) {
			if ($wjInfoValue['intID'] == $ccgid) {
				$ccwjinfo = $wjInfoValue;
			}
			if ($wjInfoValue['intID'] == $jcgid) {
				$jcwjinfo = $wjInfoValue;
			}
		}
		if (empty($ccwjinfo) || empty($jcwjinfo)) {
			return array('status' => 30,'message'=>$sys_lang[3]);			
		}	
		if ($jcwjinfo['general_level'] < $ccwjinfo['general_level']) {
			return array('status'=>30,'message'=>$city_lang['sxcc_2']);
		}	
		$cc_py_gj = floor($ccwjinfo['py_gj'] * $roleInfo['jcb'] / 100);
		$cc_py_fy = floor($ccwjinfo['py_fy'] * $roleInfo['jcb'] / 100);
		$cc_py_tl = floor($ccwjinfo['py_tl'] * $roleInfo['jcb'] / 100);
		$cc_py_mj = floor($ccwjinfo['py_mj'] * $roleInfo['jcb'] / 100);
		if ($cc_py_gj == 0 && $cc_py_fy == 0 && $cc_py_tl == 0 && $cc_py_mj == 0) {
			return array('status'=>30,'message'=>$city_lang['sxcc_1']);
		}
		$jc_py_gj = $jcwjinfo['py_gj'];
		$jc_py_fy = $jcwjinfo['py_fy'];
		$jc_py_tl = $jcwjinfo['py_tl'];
		$jc_py_mj = $jcwjinfo['py_mj'];		
		
		$new_py_gj = max(array($cc_py_gj,$jc_py_gj));
		$new_py_fy = max(array($cc_py_fy,$jc_py_fy));
		$new_py_tl = max(array($cc_py_tl,$jc_py_tl));
		$new_py_mj = max(array($cc_py_mj,$jc_py_mj));
		$common->updatetable('playergeneral',array('py_gj'=>$new_py_gj,'py_fy'=>$new_py_fy,'py_tl'=>$new_py_tl,'py_mj'=>$new_py_mj),"intID=$jcgid");
		$common->updatetable('playergeneral',array('py_gj'=>0,'py_fy'=>0,'py_tl'=>0,'py_mj'=>0),"intID=$ccgid");
		$mc->delete(MC.$playersid.'_general');
		$updateRole['coins'] = $value['tq'] = $tq - 30000;
		$updateRole['jcb'] = $value['jcbl'] = 50;
		$common->updatetable('player',$updateRole,"playersid = $playersid");
		$common->updateMemCache(MC.$playersid,$updateRole);
		$value['status'] = 0;
		$value['xhtq'] = 30000;
		$value['xcyb'] = jcybhf(50);
		$value['yjyb'] = jcyjts(50);
		return $value;
	}
}