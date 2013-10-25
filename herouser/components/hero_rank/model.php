<?php
class rankModel {
	// 增加战功
	static public function addZg($pid, $zg , &$sjzjzgz = '',$zglx,$pname,$tuid) {
		global $mc, $db, $common, $G_PlayerMgr, $rank_lang;
		
		$player = $G_PlayerMgr->GetPlayer($pid);
		if(!$player)	return array('status'=>21, 'message'=>$rank_lang['message_illegal']);	
		$userinfo = $player->GetBaseInfo();
		$curr_zg = $userinfo['ba'];
		if($userinfo['player_level'] < 18) {
			$sjzjzgz = 0;
			return array('ba'=>$curr_zg);
		}	
		
		$curr_rank = $userinfo['rank'];
		if($userinfo['today_ba_uplimit'] <= 0) { // 初始化
			$today_zglimit = getZgLimit($userinfo['player_level']);			
			$zglimit = needzg($curr_rank - 2); // 当前战功上限
			if (($userinfo['ba'] + $zg) > $zglimit) {
				if ($userinfo['ba'] > $zglimit) {
					$zglimit = 0;
				} else {
					$zglimit = $zglimit - $userinfo['ba'];
				}
			} else {
				$zglimit = $zglimit - $userinfo['ba'] - $zg;
			}
			if($today_zglimit[0] > $zglimit)  {
				$hdsx = $zglimit;
			} else {
				$hdsx = $today_zglimit[0];
			}
			if($hdsx < $userinfo['today_addba']) $hdsx += $userinfo['today_addba'];
			if($hdsx > $today_zglimit[0]) $hdsx = $today_zglimit[0];
			$userinfo['today_ba_uplimit'] = $updateRole['today_ba_uplimit'] = $hdsx;					
		}
		$today_zg_addlimit = $userinfo['today_ba_uplimit'];				
		
		if($userinfo['curr_ba_date'] == 0) $userinfo['curr_ba_date'] = $updateRole['curr_ba_date'] = time();
		if(date('Y-m-d') == date('Y-m-d', $userinfo['curr_ba_date'])) {
			if(($userinfo['today_addba'] + $zg) > $today_zg_addlimit) {
				if ($userinfo['today_addba'] > $today_zg_addlimit) {
					$sjzjzgz = 0;
				} else {
					$sjzjzgz = $today_zg_addlimit - $userinfo['today_addba'];
				}
				$updateRole['ba'] = $curr_zg + $sjzjzgz;
				$updateRole['today_addba'] = $today_zg_addlimit;
			} else {
				$sjzjzgz = $zg;
				$updateRole['ba'] = $curr_zg + $zg;
				$updateRole['today_addba'] = $userinfo['today_addba'] + $zg;
			} 
		} else {
			$today_zglimit = getZgLimit($userinfo['player_level']);		// 跨天时	
			$zglimit = needzg($curr_rank - 2); // 当前战功上限
			if (($userinfo['ba'] + $zg) > $zglimit) {
				if ($userinfo['ba'] > $zglimit) {
					$zglimit = 0;
				} else {
					$zglimit = $zglimit - $userinfo['ba'];
				}
			} else {
				$zglimit = $zglimit - $userinfo['ba'] - $zg;
			}
			if($today_zglimit[0] > $zglimit)  {
				$hdsx = $zglimit;
			} else {
				$hdsx = $today_zglimit[0];
			}
			if($hdsx > $today_zglimit[0]) $hdsx = $today_zglimit[0];
			$updateRole['today_ba_uplimit'] = $hdsx;			
			if ($zg > $hdsx) {
				$zg = $hdsx;				
			}
			$updateRole['today_addba'] = $zg;
			$updateRole['today_reduceba'] = 0;		
			$sjzjzgz = $zg;
			$updateRole['ba'] = $curr_zg + $zg;			
			$updateRole['curr_ba_date'] = time();
		}			
				
		/*$curr_rank = $userinfo['rank'];		
		$zglimit = needzg($curr_rank - 2); // 当前战功上限
		
		if($updateRole['ba'] > $zglimit) {
			$sjzjzgz = $zglimit - $curr_zg;
			$updateRole['ba'] = $zglimit;
			if($userinfo['today_addba'] + $sjzjzgz > $today_zg_addlimit) {
				$updateRole['today_addba'] = $today_zg_addlimit;
			} else {
				$updateRole['today_addba'] = $userinfo['today_addba'] + $sjzjzgz;	
			}			
		} */		
		if ($sjzjzgz > 0) {
			rankModel::zgLog($pid,$zglx,$sjzjzgz,$pname,$tuid);
		} else {
			$sjzjzgz = 0;
		}	
		return $updateRole;
	}
	
	// 减少战功
	static public function decreaseZg($pid, $zg, &$sjkczgz = '',$zglx,$pname,$tuid) {
		global $mc,$db,$common, $G_PlayerMgr, $rank_lang;
		
		$player = $G_PlayerMgr->GetPlayer($pid);
		if(!$player)	return array('status'=>21, 'message'=>$rank_lang['message_illegal']);	
		$userinfo = $player->GetBaseInfo();
		$curr_zg = $userinfo['ba'];
		if($userinfo['player_level'] < 18) {
			$sjkczgz = 0;
			return array('ba'=>$curr_zg);
		}	
				
		if($userinfo['today_ba_downlimit'] == 0) { // 初始化
			$today_zglimit = getZgLimit($userinfo['player_level']);
			$today_zg_reducelimit = $today_zglimit[1];	//2280
			$curr_rank = $userinfo['rank'];		// 108 0 1685 - 0  130
			$zg_lowlimit = $curr_rank >= 109 ? 0 : needzg($curr_rank + 1); // 当前战功下限
			if($userinfo['ba'] > 0) {
				$zg_lowlimit = $userinfo['ba'] - $zg_lowlimit - $zg;
			} else {
				$zg_lowlimit = 0;
			}
			if(abs($today_zglimit[1]) > $zg_lowlimit) {
				$sxsx = $zg_lowlimit;
			} else {
				$sxsx = abs($today_zglimit[1]);
			}
			if($sxsx < $userinfo['today_reduceba']) $sxsx += $userinfo['today_reduceba'];	
			if($sxsx > abs($today_zglimit[1])) $sxsx = abs($today_zglimit[1]);			
			$userinfo['today_ba_downlimit'] = $updateRole['today_ba_downlimit'] = $sxsx;
		}		
		$today_zg_reducelimit = $userinfo['today_ba_downlimit'];
		
		if($userinfo['curr_ba_date'] == 0) $userinfo['curr_ba_date'] = $updateRole['curr_ba_date'] = time();
		if(date('Y-m-d') == date('Y-m-d', $userinfo['curr_ba_date'])) {
			if(($userinfo['today_reduceba'] + $zg) > $today_zg_reducelimit) {				
				$sjkczgz = $today_zg_reducelimit - $userinfo['today_reduceba'];
				$updateRole['ba'] = $curr_zg - $sjkczgz;
				if ($updateRole['ba'] < 0) {
					$updateRole['ba'] = 0;
				}
				$updateRole['today_reduceba'] = $today_zg_reducelimit;
			} else {
				$sjkczgz = $zg;
				$updateRole['ba'] = $curr_zg - $zg;
				if ($updateRole['ba'] < 0) {
					$updateRole['ba'] = 0;
				}				
				$updateRole['today_reduceba'] = $userinfo['today_reduceba'] + $zg;
			} 
		} else {
			$today_zglimit = getZgLimit($userinfo['player_level']);
			//$today_zg_reducelimit = $today_zglimit[1];	
			$curr_rank = $userinfo['rank'];		
			$zg_lowlimit = $curr_rank >= 109 ? 0 : needzg($curr_rank + 1); // 当前战功下限
			if($userinfo['ba'] > 0) {
				$zg_lowlimit = $userinfo['ba'] - $zg_lowlimit - $zg;
			} else {
				$zg_lowlimit = 0;
			}
			if(abs($today_zglimit[1]) > $zg_lowlimit) {
				$sxsx = $zg_lowlimit;
			} else {
				$sxsx = abs($today_zglimit[1]);
			}
			if($sxsx > abs($today_zglimit[1])) $sxsx = abs($today_zglimit[1]);
			$updateRole['today_ba_downlimit'] = $sxsx;
		
			$updateRole['today_addba'] = 0;
			$updateRole['today_reduceba'] = abs(0 - $zg);
			$sjkczgz = $zg;
			$updateRole['ba'] = $curr_zg - $zg;	
			if ($updateRole['ba'] < 0) {
				$updateRole['ba'] = 0;
			}					
			$updateRole['curr_ba_date'] = time();
		}				

		/*if($updateRole['ba'] < $zg_lowlimit) {
			$sjkczgz = $curr_zg - $zg_lowlimit;
			$updateRole['ba'] = $zg_lowlimit;	
			if($userinfo['today_reduceba'] - $sjkczgz < $today_zg_reducelimit) {
				$updateRole['today_reduceba'] = $today_zg_reducelimit;
			} else {
				$updateRole['today_reduceba'] = $userinfo['today_reduceba'] - $sjkczgz;
			}
		}*/		
		if ($sjkczgz > 0) {
			rankModel::zgLog($pid,$zglx,$sjkczgz,$pname,$tuid);
		} else {
			$sjkczgz = 0;
		}
		return $updateRole;
	}
	
	// 请求获取座次提示
	static public function qqzctsxx($getInfo) {
		global $mc,$db,$common, $G_PlayerMgr, $rank_lang;
		
		$playersid = $getInfo['playersid'];		
		$player = $G_PlayerMgr->GetPlayer($playersid);
		if(!$player)	return array('status'=>21, 'message'=>$rank_lang['message_illegal']);	
		$userinfo = $player->GetBaseInfo();
		
		//$curr_rank = $userinfo['rank'];	
		//$today_zglimit = getZgLimit($userinfo['player_level
		if($userinfo['today_ba_uplimit'] <= 0 || $userinfo['today_ba_downlimit'] <= 0) {
			$curr_rank = $userinfo['rank'];	
			$today_zglimit = getZgLimit($userinfo['player_level']);			
			$zglimit = needzg($curr_rank - 2); // 当前战功上限
			$zglimit = $zglimit - $userinfo['ba'];
			if ($zglimit < 0) {
				$zglimit = 0;
			}
			if($today_zglimit[0] > $zglimit)  {
				$hdsx = $zglimit;
			} else {
				$hdsx = $today_zglimit[0];
			}
			if($hdsx < $userinfo['today_addba']) $hdsx += $userinfo['today_addba'];
			if($hdsx > $today_zglimit[0]) $hdsx = $today_zglimit[0];
			$updateRole['today_ba_uplimit'] = $hdsx;
				
			$zg_lowlimit = $curr_rank >= 109 ? 0 : needzg($curr_rank + 1); // 当前战功下限
			$zg_lowlimit = $userinfo['ba'] - $zg_lowlimit;
			if(abs($today_zglimit[1]) > $zg_lowlimit) {
				$sxsx = $zg_lowlimit;
			} else {
				$sxsx = abs($today_zglimit[1]);
			}
			if($sxsx < $userinfo['today_reduceba']) $sxsx += $userinfo['today_reduceba'];
			if($sxsx > abs($today_zglimit[1])) $sxsx = abs($today_zglimit[1]);			
			$updateRole['today_ba_downlimit'] = $sxsx;
						
			$updateRoleWhere['playersid'] = $playersid;
			$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
			$common->updateMemCache ( MC . $playersid, $updateRole );
			$userinfo = $player->GetBaseInfo();
		}
		
		$return_value['status'] = 0;
		if(date('Y-m-d') == date('Y-m-d', $userinfo['curr_ba_date'])) {
			$return_value['hdzg'] = intval($userinfo['today_addba']);
			$return_value['sxzg'] = abs(intval($userinfo['today_reduceba']));
			$return_value['hdsx'] = $userinfo['today_ba_uplimit'];
			$return_value['sxsx'] = abs($userinfo['today_ba_downlimit']);
		} else {
			$curr_rank = $userinfo['rank'];	
			$today_zglimit = getZgLimit($userinfo['player_level']);			
			$zglimit = needzg($curr_rank - 2); // 当前战功上限
			$zglimit = $zglimit - $userinfo['ba'];
			if ($zglimit < 0) {
				$zglimit = 0;
			}
			if($today_zglimit[0] > $zglimit)  {
				$hdsx = $zglimit;
			} else {
				$hdsx = $today_zglimit[0];
			}
			//if($hdsx < $userinfo['today_addba']) $hdsx += $userinfo['today_addba'];
			if($hdsx > $today_zglimit[0]) $hdsx = $today_zglimit[0];
			$updateRole['today_ba_uplimit'] = $hdsx;
		//$common->insertLog("today_zglimit:$today_zglimit zglimit:$zglimit hdsx:$hdsx today_addba:{$userinfo['today_addba']}");
			$zg_lowlimit = $curr_rank >= 109 ? 0 : needzg($curr_rank + 1); // 当前战功下限
			$zg_lowlimit = $userinfo['ba'] - $zg_lowlimit;
			if(abs($today_zglimit[1]) > $zg_lowlimit) {
				$sxsx = $zg_lowlimit;
			} else {
				$sxsx = abs($today_zglimit[1]);
			}
			//if($sxsx < $userinfo['today_reduceba']) $sxsx += $userinfo['today_reduceba'];
			if($sxsx > abs($today_zglimit[1])) $sxsx = abs($today_zglimit[1]);
			$updateRole['today_ba_downlimit'] = $sxsx;
			$updateRole['today_addba'] = 0;
			$updateRole['today_reduceba'] = 0;
			$updateRole['curr_ba_date'] = time();
			
			$updateRoleWhere['playersid'] = $playersid;
			$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
			$common->updateMemCache ( MC . $playersid, $updateRole );	
			
			$return_value['hdsx'] = $updateRole['today_ba_uplimit'];
			$return_value['sxsx'] = abs($updateRole['today_ba_downlimit']);
			$return_value['hdzg'] = 0;
			$return_value['sxzg'] = 0;
		}
		
		/*$zglimit = needzg($curr_rank - 2); // 当前战功上限
		$zglimit = $zglimit - $userinfo['ba'];
		if($today_zglimit[0] > $zglimit)  {
			$return_value['hdsx'] = $zglimit;
		} else {
			$return_value['hdsx'] = $today_zglimit[0];
		}
		
		$zg_lowlimit = $curr_rank >= 109 ? 0 : needzg($curr_rank + 1); // 当前战功下限
		$zg_lowlimit = $userinfo['ba'] - $zg_lowlimit;
		if(abs($today_zglimit[1]) > $zg_lowlimit) {
			$return_value['sxsx'] = $zg_lowlimit;
		} else {
			$return_value['sxsx'] = abs($today_zglimit[1]);
		}*/		
		
		return $return_value;
	}
	
	// 请求座次信息
	static public function qqzcxx($getInfo) {
		global $mc,$db,$common, $G_PlayerMgr, $rank_lang;
		
		$playersid = $getInfo['playersid'];		
		$player = $G_PlayerMgr->GetPlayer($playersid);
		if(!$player)	return array('status'=>21, 'message'=>$rank_lang['message_illegal']);	
		$userinfo = $player->GetBaseInfo();
		
		$return_value['status'] = 0;
		$return_value['zc'] = $userinfo['rank'];
		$return_value['zg'] = $userinfo['ba'];
		
		return $return_value;
	}
	
	// 请求座次详情
	static public function qqzcxq($getInfo) {
		global $mc,$db,$common, $G_PlayerMgr, $rank_lang;
		
		if(intval($getInfo['rank']) >= 109 || intval($getInfo['rank']) < 1 )	return array('status'=>21, 'message'=>$rank_lang['message_illegal']);	
		
		$playersid = $getInfo['playersid'];		
		$player = $G_PlayerMgr->GetPlayer($playersid);
		if(!$player)	return array('status'=>21, 'message'=>$rank_lang['message_illegal']);	
		$userinfo = $player->GetBaseInfo();
		$challenge_rank = $getInfo['rank'];
		$nextrank_zg = needzg($challenge_rank);
		$curr_zg = $userinfo['ba'];
		$curr_rank = $userinfo['rank'];
		$curr_rankcd = $userinfo['rank_cd'] == 0 ? false : $userinfo['rank_cd'];
    	$xwzt_23 = substr($userinfo['xwzt'],22,1); 
		if ($xwzt_23 == 0) {
			$updateRole['xwzt'] = substr_replace($userinfo['xwzt'],'1',22,1);
			$updateRoleWhere['playersid'] = $playersid;
			$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
			$common->updateMemCache ( MC . $playersid, $updateRole );			
			//$value['xwzt'] = $updateRole['xwzt'];
		} 				
		if($userinfo['player_level'] < 18) return array('status'=>998, 'message'=>$rank_lang['message_1'], 'zc'=>$curr_rank, 'zg'=>$curr_zg);
		if($challenge_rank <= 72) return array('status'=>998, 'message'=>$rank_lang['message_2'], 'zc'=>$curr_rank, 'zg'=>$curr_zg);	
		if($challenge_rank >= $curr_rank) return array('status'=>1004, 'zc'=>$curr_rank, 'zg'=>$curr_zg);
		if(($curr_rank - $challenge_rank) > 1) return array('status'=>1001, 'zc'=>$curr_rank, 'zg'=>$curr_zg);		
		if($curr_zg < $nextrank_zg) return array('status'=>1002, 'zc'=>$curr_rank, 'zg'=>$curr_zg);
		if($curr_rankcd !== false) {
			date_default_timezone_set('PRC');
			$cdseconds = time() - $curr_rankcd;
			if($cdseconds < 60 * 15) {
				$cdseconds = 60 * 15 - $cdseconds;
				return array('status'=>1003, 'zc'=>$curr_rank, 'zg'=>$curr_zg, 'lqsj'=>$cdseconds);
			}
		}			
		$return_value['status'] = 0;
		$return_value['zc'] = $curr_rank;
		$return_value['zg'] = $curr_zg;
		
		return $return_value;
	}
	
	// 请求座次排行榜
	static public function qqzcyxb($getInfo) {
		global $mc,$db,$common, $G_PlayerMgr, $rank_lang;
		
		if(intval($getInfo['rank']) >= 109 || intval($getInfo['rank']) < 1 || intval($getInfo['lx']) < 0 || intval($getInfo['lx']) > 2)	return array('status'=>21, 'message'=>$rank_lang['message_illegal']);	
		
		$playersid = $getInfo['playersid'];		
		$player = $G_PlayerMgr->GetPlayer($playersid);
		if(!$player)	return array('status'=>21, 'message'=>$rank_lang['message_illegal']);	
		$userinfo = $player->GetBaseInfo();
		
		$challenge_rank = $getInfo['rank'];
		$memkey = '';
		if($getInfo['lx'] == 0) $memkey = MC.'kill_ranklist_' . $challenge_rank;
		if($getInfo['lx'] == 1) $memkey = MC.'speed_ranklist_' . $challenge_rank;
		if($getInfo['lx'] == 2) $memkey = MC.'tech_ranklist_' . $challenge_rank;
		
		$result = null;		
		if(!($result[$memkey] = $mc->get($memkey))) {
			$res = $db->query("SELECT playersid,`kill`,speed,technique,record FROM ".$common->tname('ranklist') . " WHERE `rank` = $challenge_rank ORDER BY id ASC");				
			if(mysql_num_rows($res) > 0) {
				while($ranklist[] = $db->fetch_array($res));
				array_pop($ranklist);
				for($i = 0; $i < count($ranklist); $i++) {
					if($ranklist[$i]['kill'] != 0) {
						$result[MC.'kill_ranklist_' . $challenge_rank][] = $ranklist[$i];
					} else if($ranklist[$i]['speed'] != 0) {
						$result[MC.'speed_ranklist_' . $challenge_rank][] = $ranklist[$i];
					} else if($ranklist[$i]['technique'] != 0) {
						$result[MC.'tech_ranklist_' . $challenge_rank][] = $ranklist[$i];
					}
				}									
				$mc->set(MC.'kill_ranklist_' . $challenge_rank, $result[MC.'kill_ranklist_' . $challenge_rank], 0, 3600);
				$mc->set(MC.'speed_ranklist_' . $challenge_rank, $result[MC.'speed_ranklist_' . $challenge_rank], 0, 3600);
				$mc->set(MC.'tech_ranklist_' . $challenge_rank, $result[MC.'tech_ranklist_' . $challenge_rank], 0, 3600);
			}
		}
		
		$list = array();
		if($result != null) {
			$ranklist = $result[$memkey];	
			foreach($ranklist as $key=>$value) {
				$other_roleInfo['playersid'] = $value['playersid'];			
				roleModel::getRoleInfo($other_roleInfo);
				if($value['playersid'] != 0) {
					$tzid = explode('|', $value['record']);
					$jwmc = jwmc($other_roleInfo['mg_level']);
					if($getInfo['lx'] == 2) {
						$list[] = array('mc'=>$other_roleInfo['nickname'], 'id'=>$value['playersid'], 'level'=>$other_roleInfo['player_level'], 'jw'=>$jwmc['mc'], 'tzid'=>$tzid[0], 'pf'=>$value['technique']);
					} else {
						$list[] = array('mc'=>$other_roleInfo['nickname'], 'id'=>$value['playersid'], 'level'=>$other_roleInfo['player_level'], 'jw'=>$jwmc['mc'], 'tzid'=>$tzid[0]);
					}			
				}
			}		
		}
				
		$return_value['list'] = array_values($list);
		$return_value['status'] = 0;
		$return_value['zc'] = $getInfo['rank'];
		$return_value['lx'] = $getInfo['lx'];
		
		return $return_value;
	}
	
	// 请求英雄榜战斗数据回放
	static public function qqyxbzd($getInfo) {
		global $mc,$db,$common, $G_PlayerMgr, $rank_lang;
		
		if(intval($getInfo['rank']) >= 109 || intval($getInfo['rank']) < 1 || intval($getInfo['lx']) < 0 || intval($getInfo['lx']) > 2 || intval($getInfo['mc']) < 0 || intval($getInfo['mc']) > 5)	return array('status'=>21, 'message'=>$rank_lang['message_illegal']);	
		
		$playersid = $getInfo['playersid'];		
		$player = $G_PlayerMgr->GetPlayer($playersid);
		if(!$player)	return array('status'=>21, 'message'=>$rank_lang['message_illegal']);
		$userinfo = $player->GetBaseInfo();
		
		$challenge_rank = $getInfo['rank'];
		$memkey = '';
		if($getInfo['lx'] == 0) $memkey = MC.'kill_ranklist_' . $challenge_rank;
		if($getInfo['lx'] == 1) $memkey = MC.'speed_ranklist_' . $challenge_rank;
		if($getInfo['lx'] == 2) $memkey = MC.'tech_ranklist_' . $challenge_rank;
		
		$result = null;
		if(!($result[$memkey] = $mc->get($memkey))) {
			$res = $db->query("SELECT playersid,`kill`,speed,technique,record FROM ".$common->tname('ranklist') . " WHERE `rank` = $challenge_rank ORDER BY id ASC");				
			if(mysql_num_rows($res) > 0) {
				while($ranklist[] = $db->fetch_array($res));
				array_pop($ranklist);
				for($i = 0; $i < count($ranklist); $i++) {
					if($ranklist[$i]['kill'] != 0) {
						$result[MC.'kill_ranklist_' . $challenge_rank][] = $ranklist[$i];
					} else if($ranklist[$i]['speed'] != 0) {
						$result[MC.'speed_ranklist_' . $challenge_rank][] = $ranklist[$i];
					} else if($ranklist[$i]['technique'] != 0) {
						$result[MC.'tech_ranklist_' . $challenge_rank][] = $ranklist[$i];
					}
				}									
				$mc->set(MC.'kill_ranklist_' . $challenge_rank, $result[MC.'kill_ranklist_' . $challenge_rank], 0, 3600);
				$mc->set(MC.'speed_ranklist_' . $challenge_rank, $result[MC.'speed_ranklist_' . $challenge_rank], 0, 3600);
				$mc->set(MC.'tech_ranklist_' . $challenge_rank, $result[MC.'tech_ranklist_' . $challenge_rank], 0, 3600);
			}
		}
		
		$list = array();
		if($result != null) {
			$ranklist = $result[$memkey];				
			if($ranklist[$getInfo['mc']-1]['playersid'] != 0) {
				$tzid = explode('|', $ranklist[$getInfo['mc']-1]['record']);
				$return_value['request'] = json_decode(gzuncompress(base64_decode($tzid[1])), true);	
				foreach($ranklist as $key=>$value) {
					$other_roleInfo['playersid'] = $value['playersid'];			
					roleModel::getRoleInfo($other_roleInfo);
					if($value['playersid'] != 0) {
						if($getInfo['lx'] == 2) {
							$list[] = array('mc'=>$other_roleInfo['nickname'], 'id'=>$value['playersid'], 'level'=>$other_roleInfo['player_level'], 'jw'=>$other_roleInfo['mg_level'], 'tzid'=>$tzid[0], 'pf'=>$value['technique']);
						} else {
							$list[] = array('mc'=>$other_roleInfo['nickname'], 'id'=>$value['playersid'], 'level'=>$other_roleInfo['player_level'], 'jw'=>$other_roleInfo['mg_level'], 'tzid'=>$tzid[0]);
						}
					}
				}
				if($tzid[0] != $getInfo['tzid']) return array('status'=>998, 'message'=>$rank_lang['message_3'],'list'=>$list);
			}
			
		}
				
		$return_value['status'] = 0;			
		
		return $return_value;
	}
	
	// 请求座次战斗信息
	static public function qqzczd($getInfo) {
		global $mc,$db,$common, $G_PlayerMgr, $_SGLOBAL, $zbslots, $rank_lang;	
		
		$nowTime = $_SGLOBAL['timestamp'];
    	
 		if(intval($getInfo['rank']) >= 109 || intval($getInfo['rank']) < 1 )	return array('status'=>21, 'message'=>$rank_lang['message_illegal']);	
		
		$playersid = $getInfo['playersid'];		
		$player = $G_PlayerMgr->GetPlayer($playersid);
		if(!$player)	return array('status'=>21, 'message'=>$rank_lang['message_illegal']);	
		$userinfo = $player->GetBaseInfo();
		
		// 战斗所用消费类型
    	$type = $getInfo['hyp'];
     	if($type != 0 && $type != 1) 
        return array('status'=>3, 'message'=>$rank_lang['message_illegal']);
		
		$challenge_rank = $getInfo['rank'];
		$nextrank_zg = needzg($challenge_rank);

		$curr_zg = $userinfo['ba'];
		$curr_rank = $userinfo['rank'];
		$curr_rankcd = $userinfo['rank_cd'] == 0 ? false : $userinfo['rank_cd'];
		
		if($userinfo['player_level'] < 18) return array('status'=>998, 'message'=>$rank_lang['message_1'], 'zc'=>$curr_rank, 'zg'=>$curr_zg);
		if($challenge_rank <= 72) return array('status'=>998, 'message'=>$rank_lang['message_2'], 'zc'=>$curr_rank, 'zg'=>$curr_zg);		
		if($challenge_rank >= $curr_rank) return array('status'=>1004, 'zc'=>$curr_rank, 'zg'=>$curr_zg);
		if(($curr_rank - $challenge_rank) > 1) return array('status'=>1001, 'zc'=>$curr_rank, 'zg'=>$curr_zg);
		if($curr_zg < $nextrank_zg) return array('status'=>1002, 'zc'=>$curr_rank, 'zg'=>$curr_zg);
		if($curr_rankcd !== false) {			
			date_default_timezone_set('PRC');
			if($type == 1) {
				$updateRole['rank_cd'] = 0;
			} else {
				$cdseconds = time() - $curr_rankcd;
				if($cdseconds < 60 * 15) {
					$cdseconds = 60 * 15 - $cdseconds;
					return array('status'=>1003, 'zc'=>$curr_rank, 'zg'=>$curr_zg, 'lqsj'=>$cdseconds);
				}
			}
		}
      
		$returnValue = null;
		$roleInfo = $userinfo;
		$jwdj = $roleInfo['mg_level'];
		$jwInfo = jwmc($jwdj);
		$jwjc = 1 + $jwInfo['jc'] / 100;
		
		// 玩家将领信息
		$general = cityModel::getGeneralData($playersid,'','*');
		if($general == false) {
			$returnValue['status'] = 21;
			$returnValue['message'] = $rank_lang['message_4'] ;
			
			return $returnValue;				
		}
		
		$cz_ginfo = 0;		
		for($z = 0; $z < count($general); $z++) {
			if($general[$z]['f_status'] == 1) {
				$cz_ginfo++;				 
			}
		}
		
    	// 玩家出征将领信息		
		if($cz_ginfo == 0) 
			return array('status'=>998, 'message'=>$rank_lang['message_4'] );
		
		$avalue = array();
		$j = 0;		
		for($i = 0; $i < count($general); $i++) {
			if($general[$i]['general_life'] > 0 && $general[$i]['f_status'] == 1) {
				if ($general[$i]['act'] == 1 || $general[$i]['gohomeTime'] > $nowTime || $general[$i]['act'] == 6) {
					continue;
				}
				$j++;
				$general[$i]['general_sort_data'] = $general[$i]['general_sort'];
				$general[$i]['general_sort'] = $j;							
				$general[$i]['command_soldier'] = $general[$i]['general_life'];
				$avalue[] = $general[$i];										
			}
		}		
		
		// 战斗用战斗数据
		$attack_data = $avalue;	
		
		// 玩家武将生命值都为0
    	if (empty($avalue)) {
    		// 构造疗伤用武将数据
    		$avalue = null;
    		$j = 0;		
			for($i = 0; $i < count($general); $i++) {
				if($general[$i]['general_life'] == 0 && $general[$i]['f_status'] == 1) {
					$j++;
					$general[$i]['general_sort_data'] = $general[$i]['general_sort'];
					$general[$i]['general_sort'] = $j;							
					$general[$i]['command_soldier'] = $general[$i]['general_life'];
					$avalue[] = $general[$i];										
				}
			} 
			$attack_data = $avalue;	
			
			for ($k = 0; $k < count($attack_data); $k++) {
				// 计算疗伤所需铜钱
				$zbtljcArray = array ();
				foreach ($zbslots as $slot) {
				  $zbid = $attack_data[$k] [$slot];
				  if ($zbid != 0) {
					$zbInfo = $player->GetZBSX($zbid);
					$zbtljcArray [] = $zbInfo ['tl'];
				  }
				}
				$zbtljc = array_sum ( $zbtljcArray );

				$sxxs = genModel::sxxs ( $attack_data[$k]['professional'] );
				$tl = genModel::hqwjsx($attack_data[$k]['general_level'],$attack_data[$k] ['understanding_value'],$attack_data[$k] ['professional_level'],$attack_data[$k] ['llcs'],$jwjc,$zbtljc,$sxxs ['tl'],$attack_data[$k] ['py_tl']);				  
				$lshf_value[] = ceil(($tl * 10 - 0) * addLifeCost($roleInfo['player_level']));				
			}
    		
			$returnValue['lshf'] = array_sum($lshf_value);
			$returnValue['tq'] = $roleInfo['coins'];
			$returnValue['status'] = 1048;
			$returnValue['message'] = $rank_lang['message_6'];		
			return $returnValue;
		}
		
		// 使用银票消除冷却时间
		$xhyp = 5;		
		$currFood = $currYb = 0;
    	if($type == 1) { // 消耗银票
    		$currYp = $roleInfo['silver']; 
			$syyp = $currYp - $xhyp;	
			if($syyp < 0){
				$returnValueYp['status'] = 68;
				$returnValueYp['yp'] = intval($currYp);
				$returnValueYp['xyxhyp'] = intval($xhyp);				
				return $returnValueYp;
			}		 					
			$updateRole['silver'] = $syyp;	
			$updateRole['rank_cd'] = 0;	
			$returnValue['yp'] = intval($syyp);
    		$returnValue['xhyp'] = intval($xhyp);
    	} 
			
		// 获取关卡怪物信息		
		$monsters = null;		
		if (!($monsters = $mc->get(MC.'zcmonsters_' . $challenge_rank))) {
			$res = $db->query("SELECT * FROM ".$common->tname('seatingrank') . " WHERE npcid = '$challenge_rank'");
			while($monsters[] = $db->fetch_array($res));
			array_pop($monsters);
			$mc->set(MC.'zcmonsters_' . $challenge_rank, $monsters, 0, 3600);
		}	
		
    	// 如果因意外状况未取到怪物数据，那么重新读取
		if(empty($monsters)) {			
			$result = $db->query("SELECT * FROM ".$common->tname('seatingrank')." WHERE npcid = '$challenge_rank'");			
			while ($monsters[] = $db->fetch_array($result));
			array_pop($monsters);
		}		
		
		// 构造战斗流程相关数据
		$zcch = getZcCh($challenge_rank);			
		$returnValue['request']['wjxm'] = stripcslashes($roleInfo['nickname']);
		$returnValue['request']['gwxm'] = $zcch;
		$returnValue['request']['a_level'] = intval($roleInfo['player_level']);
		$returnValue['request']['a_sex'] = intval($roleInfo['sex']);
		$returnValue['request']['d_level'] = intval($monsters[0]['general_level']);
		$returnValue['request']['d_sex'] = intval($monsters[0]['sex']);
	
		// 如果怪物数据为空
		if(empty($monsters) || empty($attack_data)) {
			$returnValueData = array('status'=>3, 'message'=>$rank_lang['message_7']);
			return $returnValueData;			
		}
		$gf_jwdj = $roleInfo['mg_level'];
		$fightResult = actModel::fight($attack_data, $monsters, 1, 0, 0, 0,$gf_jwdj);
    	
		$roleInfo['boss'] = $challenge_rank;
      	    	
    	// 根据返回的数据来判断输赢
		$soldierNum = 0;                                                                      //初始化防守士兵数量
		$attack_result = $fightResult['result'];
		$attackLeft = array(0);
		for ($i = 0; $i < count($attack_result); $i++) {
			$role = $attack_result[$i]['role'];  //攻击方还是防守方
			if ($role == 1) {
				$attack_soldier[] = $attack_result[$i]['command_soldier'];
				$attackGeneralLeftSoldier[$attack_result[$i]['id']] = $attack_result[$i]['command_soldier'];  //攻击方每个将领所剩兵力
				if ($attack_result[$i]['command_soldier'] > 0) {
					$attackLeft[] = $attack_result[$i]['command_soldier'];
				}	
			} else {
				$defend_soldier[] = $attack_result[$i]['command_soldier'];
				$defendGeneralLeftSoldier[$attack_result[$i]['id']] = $attack_result[$i]['command_soldier']; 				
			}
		}
		$attack_soldier_left = array_sum($attack_soldier);   // 攻击方所剩人数
		$defend_soldier_left = array_sum($defend_soldier);	
		$returnValue['request']['begin'] = $fightResult['begin'];                                               // 开始士兵情形		
		if (!empty($fast)) {
			$returnValue['wjsx'] = $attack_soldier_left;
			$returnValue['gwsx'] = $defend_soldier_left;
			foreach ($returnValue['begin'] as $beginValue) {
				if (substr($beginValue['hid'],0,1) == 1) {
					$wjzx[] = $beginValue['sac'];
				} else {
					$gwzx[] = $beginValue['sac'];
				}
			}
			$returnValue['wjzx'] = array_sum($wjzx);
			$returnValue['gwzx'] = array_sum($gwzx);
		}
		
		// 获取当前玩家实力评分信息
		//$pfIndex = $nd . $dgbh . $cgRecord['curr_subStage'] + 1;
		//$cgScores = cgpf($pfIndex);
		$levelScore = 0;
		$tfScore = 0;
		$zbScore = 0;
		$jwScore = 0;
		$jnScore = 0;
		$jwjcpf = jwmc($roleInfo['mg_level']);
		//战斗后自动补血
		$zdbx = fightModel::zdbx($attack_data,$playersid,$attackGeneralLeftSoldier);
		if (!empty($zdbx)) {
			if (!empty($zdbx['ginfo'])) {
				$attackGeneralLeftSoldier = $zdbx['ginfo'];
			}
			$updateRole['coins'] = $zdbx['tq'];
			$returnValue['tq'] = $updateRole['coins'];
		}
    	// 更新玩家信息
    	$whereRole['playersid'] = $playersid;
    	/*$xwzt_10 = substr($roleInfo['xwzt'],9,1);  //完成 闯关行为
		if ($xwzt_10 == 0) {
			$updateRole['xwzt'] = substr_replace($roleInfo['xwzt'],'1',9,1);		
		} */   	
			
		if ($attack_soldier_left <= 0 && $defend_soldier_left > 0) {
      		$success = 0;
		} else {
			$success = 1;
		}
		//不论输赢都做的
		$newData = array();
		for ($k = 0; $k < count($attack_data); $k++) {
		  $id = $attack_data[$k]['intID'];
		  $left_command_soldier = $attackGeneralLeftSoldier[$id];                             //剩余士兵数量
		  $sssmz = $attack_data[$k]['general_life'] - $left_command_soldier;                  //损失的生命值
		  $wjmc = $attack_data[$k]['general_name']; 
		  $updateGeneral['general_life'] = $left_command_soldier;    
		  $attack_data[$k]['general_life'] = $left_command_soldier;
		  $attack_data[$k]['command_soldier'] = $left_command_soldier;	
		  $attack_data[$k]['general_sort'] =  $attack_data[$k]['general_sort_data']; 
		  $value['ginfo'][] = array('gid'=>$id, 'smz'=>intval($left_command_soldier));	
		  // 计算疗伤所需铜钱
		  $zbtljcArray = array ();
		  $zbTotalAttr = array();
		  foreach ($zbslots as $slot) {
			$zbid = $attack_data[$k] [$slot];
			if ($zbid != 0) {
			  $zbInfo = $player->GetZBSX($zbid );
			  $zbtljcArray [] = $zbInfo ['tl'];
			  $zbTotalAttr[] = $zbInfo['gj'] > 0 ? $zbInfo['gj'] : ($zbInfo['fy'] > 0 ? $zbInfo['fy'] : ($zbInfo['tl'] > 0 ? $zbInfo['tl'] : $zbInfo['mj']));
			}
		  }
		  $zbtljc = array_sum ( $zbtljcArray );
		  // 计算子项评分
		  $levelScore += $attack_data[$k]['general_level'] * 20;
		  $zbScore += array_sum($zbTotalAttr);
		  
		  $wjbronAttr = $attack_data[$k]['general_level'] * ($attack_data[$k]['understanding_value'] - $attack_data[$k]['llcs']);
		  $wjCombinAttr = $attack_data[$k]['general_level'] * ($attack_data[$k]['professional_level'] - 1) * 5;
		  $wjtfAttr = $attack_data[$k]['general_level'] * ($attack_data[$k]['llcs'] * 0.5);
		  $jwScore += ceil(ceil(80 + $wjbronAttr + $wjCombinAttr + $wjtfAttr) * ($jwjcpf['jc'] / 100));

		  $pyAttr = $attack_data[$k]['py_gj'] + $attack_data[$k]['py_fy'] + $attack_data[$k]['py_tl'] + $attack_data[$k]['py_mj'];
		  
		  $tftmpScore = 80 + $wjbronAttr + $wjCombinAttr + $wjtfAttr + $pyAttr - ($attack_data[$k]['general_level'] * 20);
		  $tftmpScore = $tftmpScore < 0 ? 0 : $tftmpScore;
		  $tfScore += $tftmpScore;
		  
		  $jnScore +=  ($attack_data[$k]['jn1_level'] + $attack_data[$k]['jn2_level']) * 43;
		  
		  unset($zbTotalAttr);
		  $tftmpScore = 0;
		  
		  $sxxs = genModel::sxxs ( $attack_data[$k]['professional'] );		 
		  $tl = genModel::hqwjsx($attack_data[$k]['general_level'],$attack_data[$k] ['understanding_value'],$attack_data[$k] ['professional_level'],$attack_data[$k] ['llcs'],$jwjc,$zbtljc,$sxxs ['tl'],$attack_data[$k] ['py_tl']);				 
		  $lshf_value[] = ceil(($tl * 10 - $left_command_soldier) * addLifeCost($roleInfo['player_level']));	
		  
		  $whereGeneral['intID'] = $id;
		  $common->updatetable('playergeneral',$updateGeneral,$whereGeneral);               //更新将领数据				  				
		  $newData[$attack_data[$k]['sortid']] = $attack_data[$k];    				
		  unset($updateGeneral);
		  unset($whereGeneral);
		 }
		 if(!empty($newData)) $common->updateMemCache(MC.$playersid.'_general',$newData);  
		 $wjScore = $levelScore + $tfScore + $zbScore + $jwScore + $jnScore;
		 /*$returnValue['gkpf'] = $cgScores['stageScore'];
		 $returnValue['tgsxpf'] = $cgScores['needScore'];
		 $returnValue['wjzdlpf'] = $wjScore;
		 $returnValue['djpf'] = $levelScore;
		 $returnValue['tfpf'] = $tfScore;
		 $returnValue['zbpf'] = $zbScore;
		 $returnValue['jwpf'] = $jwScore;
		 $returnValue['jnpf'] = $jnScore;
	  	 $returnValue['djpfzb'] = $cgScores['djScore'];
	 	 $returnValue['tfpfzb'] = $cgScores['llScore'];
		 $returnValue['zbpfzb'] = $cgScores['zbScore'];
		 $returnValue['jwpfzb'] = $cgScores['jwScore'];
		 $returnValue['jnpfzb'] = $cgScores['jnScore'];*/
		
		 $returnValue['lshf'] = array_sum($lshf_value);					
		 $round = $fightResult['round']; 
		 $newround = heroCommon::arr_foreach($round);
		 $returnValue['request']['round'] = $fightResult['round']= $newround;
		 $round = null;	
		 $newround = null;				
		 $returnValue['request']['ginfo'] = array_values($value['ginfo']);
		 unset($value);
		 // 战斗信息判断结束
		
		 $updateStageInfo = null;
		 $currSubstage = 0;
		 if($success == 1) { // 战斗胜利			
			// 判断 杀 速 巧			
			$result = $mc->getMulti(array(MC.'kill_ranklist_' . $challenge_rank, MC.'speed_ranklist_' . $challenge_rank, MC.'tech_ranklist_' . $challenge_rank));
			if(!isset($result[MC.'kill_ranklist_' . $challenge_rank]) || !isset($result[MC.'speed_ranklist_' . $challenge_rank]) || !isset($result[MC.'tech_ranklist_' . $challenge_rank])) {
				$res = $db->query("SELECT playersid,`kill`,speed,technique,record FROM ".$common->tname('ranklist') . " WHERE `rank` = $challenge_rank ORDER BY id ASC");				
				if(mysql_num_rows($res) > 0) {
					while($ranklist[] = $db->fetch_array($res));
					array_pop($ranklist);
					$result = array();
					for($i = 0; $i < count($ranklist); $i++) {
						if($ranklist[$i]['kill'] != 0) {
							$result[MC.'kill_ranklist_' . $challenge_rank][] = $ranklist[$i];
						} else if($ranklist[$i]['speed'] != 0) {
							$result[MC.'speed_ranklist_' . $challenge_rank][] = $ranklist[$i];
						} else if($ranklist[$i]['technique'] != 0) {
							$result[MC.'tech_ranklist_' . $challenge_rank][] = $ranklist[$i];
						}
					}									
					$mc->set(MC.'kill_ranklist_' . $challenge_rank, $result[MC.'kill_ranklist_' . $challenge_rank], 0, 3600);
					$mc->set(MC.'speed_ranklist_' . $challenge_rank, $result[MC.'speed_ranklist_' . $challenge_rank], 0, 3600);
					$mc->set(MC.'tech_ranklist_' . $challenge_rank, $result[MC.'tech_ranklist_' . $challenge_rank], 0, 3600);
				}
			}
			
			$kill_cnt = $speed_cnt = $tech_cnt = 0;
			foreach($result as $key=>$value) {
				foreach($value as $innerkey=>$innervalue) {
					if($key == MC.'kill_ranklist_' . $challenge_rank) {
						if($innervalue['playersid'] != 0 && $innervalue['kill'] == 1) $kill_cnt++;
					} else if($key == MC.'speed_ranklist_' . $challenge_rank) {
						if($innervalue['playersid'] != 0 && $innervalue['speed'] == 1) $speed_cnt++;
					} else if($key == MC.'tech_ranklist_' . $challenge_rank) {
						if($innervalue['playersid'] != 0 && $innervalue['technique'] > 1) $tech_cnt++;
					}
				}
			}
			
			$need_update_kill = $need_update_speed = $need_update_tech = $kill_first = false;
			if(isset($result[MC.'kill_ranklist_' . $challenge_rank])) {
				if($kill_cnt == 0) $kill_first = true;
				if($kill_cnt >= 5) {
					array_shift($result[MC.'kill_ranklist_' . $challenge_rank]);
					$kill_cnt--;
				}
				$result[MC.'kill_ranklist_' . $challenge_rank][$kill_cnt]['playersid'] = $playersid;
				$result[MC.'kill_ranklist_' . $challenge_rank][$kill_cnt]['kill'] = 1;
				$result[MC.'kill_ranklist_' . $challenge_rank][$kill_cnt]['speed'] = 0;
				$result[MC.'kill_ranklist_' . $challenge_rank][$kill_cnt]['technique'] = 0;
				$result[MC.'kill_ranklist_' . $challenge_rank][$kill_cnt]['record'] = $playersid . '|' . base64_encode(gzcompress(json_encode($returnValue['request']),1));		
				$rand_num = rand(0, 100);
				//if($rand_num > 90) 
					$need_update_kill = true;
			}
			
			if(isset($result[MC.'speed_ranklist_' . $challenge_rank])) {
				if($speed_cnt < 5) {
					$result[MC.'speed_ranklist_' . $challenge_rank][$speed_cnt]['playersid'] = $playersid;
					$result[MC.'speed_ranklist_' . $challenge_rank][$speed_cnt]['kill'] = 0;
					$result[MC.'speed_ranklist_' . $challenge_rank][$speed_cnt]['speed'] = 1;
					$result[MC.'speed_ranklist_' . $challenge_rank][$speed_cnt]['technique'] = 0;
					$result[MC.'speed_ranklist_' . $challenge_rank][$speed_cnt]['record'] = $playersid . '|' . base64_encode(gzcompress(json_encode($returnValue['request']),1));
					$need_update_speed = true;
				}
			}
			
			if(isset($result[MC.'tech_ranklist_' . $challenge_rank])) {
				if($tech_cnt < 5) {				
					$result[MC.'tech_ranklist_' . $challenge_rank][$tech_cnt]['playersid'] = $playersid;
					$result[MC.'tech_ranklist_' . $challenge_rank][$tech_cnt]['kill'] = 0;
					$result[MC.'tech_ranklist_' . $challenge_rank][$tech_cnt]['speed'] = 0;
					$result[MC.'tech_ranklist_' . $challenge_rank][$tech_cnt]['technique'] = $wjScore;
					$result[MC.'tech_ranklist_' . $challenge_rank][$tech_cnt]['record'] = $playersid . '|' . base64_encode(gzcompress(json_encode($returnValue['request']),1));
					$need_update_tech = true;
				} else {
					if($wjScore < $result[MC.'tech_ranklist_' . $challenge_rank][4]['technique']) {
						$result[MC.'tech_ranklist_' . $challenge_rank][4]['playersid'] = $playersid;
						$result[MC.'tech_ranklist_' . $challenge_rank][4]['kill'] = 0;
						$result[MC.'tech_ranklist_' . $challenge_rank][4]['speed'] = 0;
						$result[MC.'tech_ranklist_' . $challenge_rank][4]['technique'] = $wjScore;
						$result[MC.'tech_ranklist_' . $challenge_rank][4]['record'] = $playersid . '|' . base64_encode(gzcompress(json_encode($returnValue['request']),1));
						$need_update_tech = true;
					}						
				}
				if($need_update_tech) {
					$wait_sort_arr = array();
					foreach($result[MC.'tech_ranklist_' . $challenge_rank] as $key=>$value) {
						if($value['technique'] != 1)
							$wait_sort_arr[$key] = intval($value['technique']);
					}
					arsort($wait_sort_arr);
					$tmparr = $result[MC.'tech_ranklist_' . $challenge_rank];
					$idx = count($wait_sort_arr) - 1;
					foreach($wait_sort_arr as $key=>$value) {
						$result[MC.'tech_ranklist_' . $challenge_rank][$idx] = $tmparr[$key];
						$idx--;
					}
					unset($tmparr);
				}
			}								
			$mc->set(MC.'kill_ranklist_' . $challenge_rank, $result[MC.'kill_ranklist_' . $challenge_rank], 0, 3600);
			if($need_update_speed)
				$mc->set(MC.'speed_ranklist_' . $challenge_rank, $result[MC.'speed_ranklist_' . $challenge_rank], 0, 3600);
			if($need_update_tech)
				$mc->set(MC.'tech_ranklist_' . $challenge_rank, $result[MC.'tech_ranklist_' . $challenge_rank], 0, 3600);
				
			// 数据落地
			if($need_update_tech || $need_update_speed || $need_update_kill) {
				$sql = 'REPLACE INTO ol_ranklist values';
				if($need_update_kill) { 
					foreach($result[MC.'kill_ranklist_' . $challenge_rank] as $key=>$value) {
						if($value['playersid'] != 0) {
							$tb_zc_id = (108 - $challenge_rank)*15 + $key + 1;
							$sql .= " ({$tb_zc_id}, {$challenge_rank}, {$value['playersid']}, 1, 0, 0, '{$value['record']}'),";
						}
					}
				}
				if($need_update_speed) { 
					foreach($result[MC.'speed_ranklist_' . $challenge_rank] as $key=>$value) {
						if($value['playersid'] != 0) {
							$tb_zc_id = (108 - $challenge_rank)*15 + 5 + $key + 1;
							$sql .= " ({$tb_zc_id}, {$challenge_rank}, {$value['playersid']}, 0, 1, 0, '{$value['record']}'),";
						}
					}
				}
				if($need_update_tech) { 
					foreach($result[MC.'tech_ranklist_' . $challenge_rank] as $key=>$value) {
						if($value['playersid'] != 0) {
							$tb_zc_id = (108 - $challenge_rank)*15 + 10 + $key + 1;
							$sql .= " ({$tb_zc_id},{$challenge_rank},{$value['playersid']},0, 0, {$value['technique']},'{$value['record']}'),";
						}
					}
				}
				$sql=rtrim($sql, ',');
				$sql.=';';//$common->insertLog($sql);
				if($sql != '')
					$db->query($sql);
			}
			
			$returnValue['status'] = 0;
			$updateRole['rank'] = $challenge_rank;		
			
			if($kill_first) {
				$rep_arr1 = array('{nickname}', '{zcch}');
				$rep_arr2 = array($roleInfo['nickname'], $zcch);				
				lettersModel::setPublicNotice(str_replace($rep_arr1, $rep_arr2, $rank_lang['notice_1']));				
			} else {
				$rep_arr1 = array('{nickname}', '{zcch}', '{challenge_rank}');
				$rep_arr2 = array($roleInfo['nickname'], $zcch, $challenge_rank);
				lettersModel::setPublicNotice(str_replace($rep_arr1, $rep_arr2, $rank_lang['notice_2']));
			}				
			foreach ($general as $generalValue) {
				$pysxdata = array(zcpysx($challenge_rank),wjdjpysx($generalValue['general_level']));
	    		$kpysx = min($pysxdata);  //可培养单项属性的上限			
				$data['gid'] = intval($generalValue['intID']);
	    		$data['pysx'] = $kpysx;
				$array [] = $data;
				unset($data);
				$pysxdata = NULL;
				$kpysx = NULL;				
			}
			$returnValue['ginfo'] = $array;
			
			// 提升座次时计算每日战功的上下限
			$curr_rank = $updateRole['rank'];	
			$today_zglimit = getZgLimit($userinfo['player_level']);			
			$zglimit = needzg($curr_rank - 2); // 当前战功上限
			$zglimit = $zglimit - $userinfo['ba'];
			if($today_zglimit[0] > $zglimit)  {
				$hdsx = $zglimit;
			} else {
				$hdsx = $today_zglimit[0];
			}
			if(date('Y-m-d') == date('Y-m-d', $userinfo['curr_ba_date'])) {
				if($hdsx < $userinfo['today_addba']) $hdsx += $userinfo['today_addba'];
			}
			if($hdsx > $today_zglimit[0]) $hdsx = $today_zglimit[0];
			$updateRole['today_ba_uplimit'] = $hdsx;
				
			$zg_lowlimit = $curr_rank >= 109 ? 0 : needzg($curr_rank + 1); // 当前战功下限
			$zg_lowlimit = $userinfo['ba'] - $zg_lowlimit;
			if(abs($today_zglimit[1]) > $zg_lowlimit) {
				$sxsx = $zg_lowlimit;
			} else {
				$sxsx = abs($today_zglimit[1]);
			}
			if(date('Y-m-d') == date('Y-m-d', $userinfo['curr_ba_date'])) {
				if($sxsx < $userinfo['today_reduceba']) $sxsx += $userinfo['today_reduceba'];
			}
			if($sxsx > abs($today_zglimit[1])) $sxsx = abs($today_zglimit[1]);
			$updateRole['today_ba_downlimit'] = $sxsx;		
		 } else { // 战斗失败    	
			$returnValue['status'] = 1001;
			$returnValue['message'] = $rank_lang['message_8'];
			date_default_timezone_set('PRC');
			$updateRole['rank_cd'] = time();
		 }
		
		 // 更新玩家表
		 if(!empty($updateRole)) {	
		 	if (isset($updateRole['rank'])) {
			 	$roleInfo['rank'] = $cjInfo['rank'] = $updateRole['rank'];
	  			$rwid = questsController::OnFinish($roleInfo,"'tszc'");  
			    if (!empty($rwid)) {
			         $value['rwid'] = $rwid;				             
			    }
			    achievementsModel::check_achieve($playersid,$cjInfo, array('rank'));
		 	}		 				
			$common->updatetable('player', $updateRole, $whereRole); 
			$common->updateMemCache(MC.$playersid, $updateRole);	
		 }
		 
		 $userinfo = $player->GetBaseInfo();
		 $returnValue['zc'] = $userinfo['rank'];
		 $returnValue['zg'] = $userinfo['ba'];
		
		 unset($fightResult);					
		 return $returnValue;    
	}
	
	//插入战功值
	public static function zgLog($playersid,$zglx,$zgz,$pname,$tuid) {
		global $mc, $db, $common;
		$nowTime = time();
		$zglogInfo = $mc->get(MC.'zglog_'.$playersid);
		$id = $db->query("INSERT INTO ".$common->tname('zg_log')." SET playersid = $playersid, zgz = $zgz, zglx = $zglx, zgtime = $nowTime, pname = '$pname',tuid = $tuid");
		$mc->delete(MC.'zglog_'.$playersid);		
	}
	
	//获取战功信息
	public static function hqzgxx($playersid,$page) {
		global $mc, $db, $common, $G_PlayerMgr;
		
		$player = $G_PlayerMgr->GetPlayer($playersid);
		if(!$player)	return array('status'=>21, 'message'=>$rank_lang['message_illegal']);
		$userinfo = $player->GetBaseInfo();
		
		if (!$zglogInfo = $mc->get(MC.'zglog_'.$playersid)) {
			$i = 0;
			$sql = "SELECT * FROM ".$common->tname('zg_log')." WHERE playersid = $playersid ORDER BY intID DESC";
			$result = $db->query($sql);
			$zglogInfo = array();
			$delID = array();
			while ($rows = $db->fetch_array($result)) {
				$i++;
				if ($i < 31) {
					$zglogInfo[] = array('pid'=>$playersid,'zgz'=>$rows['zgz'],'zglx'=>$rows['zglx'],'t'=>$rows['zgtime'],'pname'=>$rows['pname'],'tuid'=>$rows['tuid']);
				} else {
					$delID[] = $rows['intID'];
				}
			}
			if (!empty($zglogInfo)) {
				$mc->set(MC.'zglog_'.$playersid,$zglogInfo,0,3600);
			}
			if (!empty($delID)) {
				$db->query("DELETE FROM ".$common->tname('zg_log')." WHERE intID IN (".implode(',',$delID).")");
			}
		}
		$value ['status'] = 0;
		if (!empty($zglogInfo)) {
  			$todayZero = strtotime(date('Y-m-d 0:0:0'));
	 		$total = count ( $zglogInfo );			
			$value ['zys'] = ceil ( $total / 3 );
			if ($page > $value ['zys']) {
				$page = $value ['zys'];
			}
			if (empty ( $page ) || $page < 0) {
				$page = 1;
			}
			$value ['dqys'] = $page;
			$start = ($page - 1) * 3;
			$end = $start + 2;
			for($i = $start; $i < $total; $i ++) {
				if ($i > $end) {
					break;
				}				
				$rq = $zglogInfo [$i] ['t'] > $todayZero ? 0 : ceil ( ($todayZero - $zglogInfo [$i] ['t']) / (3600 * 24) );
				$time = date ( 'H:i', $zglogInfo [$i] ['t'] );
				// 判断是否是掠夺别人占领自己的资源	，此时应显示对方的昵称			
				if(($zglogInfo [$i] ['zglx'] == 7 || $zglogInfo [$i] ['zglx'] == 8) &&($userinfo['nickname'] == $zglogInfo [$i] ['pname'])) {
					$other = $G_PlayerMgr->GetPlayer($zglogInfo [$i] ['tuid']);				
					$other_userinfo = $other->GetBaseInfo();
					$zglogInfo [$i] ['pname'] = $other_userinfo['nickname'];
				}
				$showMsg [] = array ('rq' => $rq, 'time' => $time, 'xllx' => intval ( $zglogInfo [$i] ['zglx'] ), 'pid' => intval($zglogInfo [$i] ['tuid']), 'X' => $zglogInfo [$i] ['pname'], 'G'=>intval($zglogInfo [$i] ['zgz']));
				$rq = NULL;
				$time = NULL;
			}
			$value['xxlist'] = $showMsg;								
		} else {
			$value['xxlist'] = array();
		}
		return $value;
	}
}