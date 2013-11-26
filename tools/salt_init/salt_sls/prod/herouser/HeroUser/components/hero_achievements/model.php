<?php
class achievementsModel {	
	public static $player_achieve_info = array();
	public static $save_flag = 0;
	//public static $no_record = 0;
	//检测成就完成情况
	public static function check_achieve2($pid, $type, $subtype, $info, $cj_no = null) {
		global $db, $mc, $common;
		
		// 是否需要保存
		$save_flag = false;
		
		// 玩家是否有成就记录
		$no_record = true;
		
		// 获取成就列表
		$cjInfo = getCjInfo();
		
		// 加载玩家成就记录
		if (!($player_achieve_info = $mc->get(MC.$pid.'_achieve_info'))) {
			$achieveRet = $db->query("SELECT cj FROM ".$common->tname('player_achieve')." where pid = {$pid}");
			$num_rows = $db->num_rows($achieveRet);
			if($num_rows > 0) {
				$no_record = false;
				$rows = $db->fetch_array($achieveRet, MYSQL_ASSOC);
				$player_achieve_info = genArr4str($rows['cj']);
				$mc->set(MC.$pid.'_achieve_info', $rows['cj'], 0, 0);
			} else {
				$player_achieve_info = array();				
			}		   	
		}
		
		if(!is_array($player_achieve_info)) {			
			$player_achieve_info = genArr4str($player_achieve_info);
			$no_record = false;
		}
		
		$award_idx = array('level'=>0, 'mg_level'=>1, 'rank'=>2, 'wjpy'=>3, 'cg'=>4, 'qh'=>5, 'occupy'=>6, 'hd'=>7, 'other'=>8);
		$raw_type = $type;
		$type = $award_idx[$type];
		
		// 直接完类成成就
		if($cj_no != null) {
			if(!isset($player_achieve_info[$type][$cj_no]) || $player_achieve_info[$type][$cj_no]['result'] == 0) {
				$player_achieve_info[$type][$cj_no] = array('result'=>1, 'count'=>0);
				$save_flag = true;
			}
		} else { // 非直接完成类成就
			// 根据传入成就类型获取所有此类型的成就
			$curr_cj_info = $cjInfo[$raw_type];
			
			// 获取可自动检测成就取值范围(过滤掉直接完成类成就)
			$range = array(-1, -1);
			if($raw_type == 'wjpy') {
				if($subtype == 'll') $range = array(0, 3);
				if($subtype == 'py') $range = array(5, 10);
				if($subtype == 'hc') $range = array(11, 13);
			} else if($raw_type == 'qh') {
				if($subtype == 'qh') $range = array(0, 8);
				if($subtype == 'dz') $range = array(9, 15);
			} else if($raw_type == 'occupy') {
				if($subtype == 'gc') $range = array(0, 3);
				if($subtype == 'zyd') $range = array(4, 7);
			} else if($raw_type == 'hd') {				
				if($subtype == 'vip') $range = array(2, 5);
			} else if($raw_type == 'other') {				
				if($subtype == 'rc') $range = array(2, 2);
				if($subtype == 'rw') $range = array(3, 11);
				if($subtype == 'frd') $range = array(12, 16);
			} else {				
				$range = array(0, count($curr_cj_info));
			}
			
			// 检测成就完成情况
			foreach($curr_cj_info as $key=>$value) {
				if($key >= $range[0] && $key <= $range[1]) {
					if($raw_type == 'level') { // 等级相关成就 此处不加break那么当前等级之前的成就都会完成
						if($info['level'] >= intval($value['award'][5])) {
							if(!isset($player_achieve_info[$type][$key])) {
								$player_achieve_info[$type][$key] = array('result'=>1, 'count'=>0);
								$save_flag = true;
							} else if($player_achieve_info[$type][$key]['result'] == 0) {
								$player_achieve_info[$type][$key]['result'] = 1;
								$save_flag = true;
							}
						}
					} else if($raw_type == 'mg_level') {
						if($info['mg_level'] >= intval($value['award'][5])) {
							if(!isset($player_achieve_info[$type][$key])) {
								$player_achieve_info[$type][$key] = array('result'=>1, 'count'=>0);
								$save_flag = true;
							} else if($player_achieve_info[$type][$key]['result'] == 0) {
								$player_achieve_info[$type][$key]['result'] = 1;
								$save_flag = true;
							}
						}
					} else if($raw_type == 'rank') {
						if($info['mg_level'] <= intval($value['award'][5])) {
							if(!isset($player_achieve_info[$type][$key])) {
								$player_achieve_info[$type][$key] = array('result'=>1, 'count'=>0);
								$save_flag = true;
							} else if($player_achieve_info[$type][$key]['result'] == 0) {
								$player_achieve_info[$type][$key]['result'] = 1;
								$save_flag = true;
							}
						}
					} else if($raw_type == 'wjpy') {
						if($subtype == 'll') {
							if($info['ll'] >= intval($value['award'][5])) {
								if(!isset($player_achieve_info[$type][$key])) {
									$player_achieve_info[$type][$key] = array('result'=>1, 'count'=>0);
									$save_flag = true;
								} else if($player_achieve_info[$type][$key]['result'] == 0) {
									$player_achieve_info[$type][$key]['result'] = 1;
									$save_flag = true;
								}
							}
						} else if($subtype == 'py') {
							if($info['py'] >= intval($value['award'][5])) {
								if(!isset($player_achieve_info[$type][$key])) {
									$player_achieve_info[$type][$key] = array('result'=>1, 'count'=>0);
									$save_flag = true;
								} else if($player_achieve_info[$type][$key]['result'] == 0) {
									$player_achieve_info[$type][$key]['result'] = 1;
									$save_flag = true;
								}
							}	
						} else if($subtype == 'hc') {
							if($info['hc'] >= intval($value['award'][5])) {
								if(!isset($player_achieve_info[$type][$key])) {
									$player_achieve_info[$type][$key] = array('result'=>1, 'count'=>0);
									$save_flag = true;
								} else if($player_achieve_info[$type][$key]['result'] == 0) {
									$player_achieve_info[$type][$key]['result'] = 1;
									$save_flag = true;
								}
							}	
						}
					} else if($raw_type == 'cg') {
						if($info['cg'] >= intval($value['award'][5])) {
							if(!isset($player_achieve_info[$type][$key])) {
								$player_achieve_info[$type][$key] = array('result'=>1, 'count'=>0);
								$save_flag = true;
							} else if($player_achieve_info[$type][$key]['result'] == 0) {
								$player_achieve_info[$type][$key]['result'] = 1;
								$save_flag = true;
							}
						}
					} else if($raw_type == 'qh') {
						if($subtype == 'qh') {
							if($info['qh'] >= intval($value['award'][5])) {
								if(!isset($player_achieve_info[$type][$key])) {
									$player_achieve_info[$type][$key] = array('result'=>1, 'count'=>0);
									$save_flag = true;
								} else if($player_achieve_info[$type][$key]['result'] == 0) {
									$player_achieve_info[$type][$key]['result'] = 1;
									$save_flag = true;
								}
							}
						} else if($subtype == 'dz') {
							if(strpos($value['award'][5], (string)$info['dz']) !== false) {
								if(!isset($player_achieve_info[$type][$key])) {
									$player_achieve_info[$type][$key] = array('result'=>1, 'count'=>0);
									$save_flag = true;
								} else if($player_achieve_info[$type][$key]['result'] == 0) {
									$player_achieve_info[$type][$key]['result'] = 1;
									$save_flag = true;
								}
							}
						}
					} else if($raw_type == 'occupy') {
						$count = !isset($player_achieve_info[$type][$key]) ? 0 : $player_achieve_info[$type][$key]['count'];
						if($count >= intval($value['award'][5])) {
							if($player_achieve_info[$type][$key]['result'] == 0) {
								$player_achieve_info[$type][$key]['result'] = 1;								
								$player_achieve_info[$type][$key]['count'] = intval($value['award'][5]);
								$save_flag = true;
							}
						} else {
							if(!isset($player_achieve_info[$type][$key])) {
								$player_achieve_info[$type][$key] = array('result'=>0, 'count'=>1);
								$save_flag = true;
							} else {
								$player_achieve_info[$type][$key]['count'] = intval($player_achieve_info[$type][$key]['count']) + 1;
								$save_flag = true;
							}
						}						
					} else if($raw_type == 'hd') {
						if($info['vip'] >= intval($value['award'][5])) {
							if(!isset($player_achieve_info[$type][$key])) {
								$player_achieve_info[$type][$key] = array('result'=>1, 'count'=>0);
								$save_flag = true;
							} else if($player_achieve_info[$type][$key]['result'] == 0) {
								$player_achieve_info[$type][$key]['result'] = 1;
								$save_flag = true;
							}
						}
					} else if($raw_type == 'other') {
						if($subtype == 'rc') {
							$count = !isset($player_achieve_info[$type][$key]) ? 0 : $player_achieve_info[$type][$key]['count'];
							if($count >= intval($value['award'][5])) {
								if($player_achieve_info[$type][$key]['result'] == 0) {
									$player_achieve_info[$type][$key]['result'] = 1;								
									$player_achieve_info[$type][$key]['count'] = intval($value['award'][5]);
									$save_flag = true;
								}
							} else {
								if(!isset($player_achieve_info[$type][$key])) {
									$player_achieve_info[$type][$key] = array('result'=>0, 'count'=>1);
									$save_flag = true;
								} else {
									$player_achieve_info[$type][$key]['count'] = intval($player_achieve_info[$type][$key]['count']) + 1;
									$save_flag = true;
								}
							}
						} else if($subtype == 'rw') {
							if($info['rw'] == intval($value['award'][5])) {
								if(!isset($player_achieve_info[$type][$key])) {
									$player_achieve_info[$type][$key] = array('result'=>1, 'count'=>0);
									$save_flag = true;
								} else if($player_achieve_info[$type][$key]['result'] == 0) {
									$player_achieve_info[$type][$key]['result'] = 1;
									$save_flag = true;
								}
							}	
						} else if($subtype == 'frd') {
							if($info['frd'] >= intval($value['award'][5])) {
								if(!isset($player_achieve_info[$type][$key])) {
									$player_achieve_info[$type][$key] = array('result'=>1, 'count'=>0);
									$save_flag = true;
								} else if($player_achieve_info[$type][$key]['result'] == 0) {
									$player_achieve_info[$type][$key]['result'] = 1;
									$save_flag = true;
								}
							}	
						}
					} 
				}
			}
		}
		
		if($save_flag) {
			// 更新数据及内存
			$player_achieve_info = genStr4Arr($player_achieve_info);
			$mc->set(MC.$pid.'_achieve_info', $player_achieve_info, 0, 0);
			
			// 如果玩家没有记录
			if($no_record) {
				$db->query("INSERT INTO ol_player_achieve VALUES(null, {$pid}, '{$player_achieve_info}');");
			} else { // 有记录就更新
				$updateItem['cj'] = $player_achieve_info;
				$whereItem['pid'] = $pid;									
				$common->updatetable('player_achieve', $updateItem, $whereItem);
			}
		}
	}
	
	//检测成就完成情况
	public static function check_achieve($pid, $info = '', $cfzd) {
		global $db, $mc, $common;
		
		// 是否需要保存
		$save_flag = false;
		
		// 玩家是否有成就记录
		$no_record = true;
		
		// 获取成就列表
		$cjInfo = getCjInfo();
		
		// 加载玩家成就记录
		if (!($player_achieve_info = $mc->get(MC.$pid.'_achieve_info'))) {
			$achieveRet = $db->query("SELECT cj FROM ".$common->tname('player_achieve')." where pid = {$pid}  LIMIT 1");
			$num_rows = $db->num_rows($achieveRet);
			if($num_rows > 0) {
				$no_record = false;
				$rows = $db->fetch_array($achieveRet, MYSQL_ASSOC);
				$player_achieve_info = achievementsModel::$player_achieve_info = genArr4str($rows['cj']);
				$mc->set(MC.$pid.'_achieve_info', $rows['cj'], 0, 0);
			} else {
				$player_achieve_info = array();				
			}		   	
		}
		
		if(!is_array($player_achieve_info)) {			
			$player_achieve_info = achievementsModel::$player_achieve_info = genArr4str($player_achieve_info);
			$no_record = false;
		}
		
		foreach ($cjInfo as $key => $value) {
			$script = $value['award'][7];
			if (isset($player_achieve_info[$key])) {
				if ($player_achieve_info[$key]['result'] == 1 || $player_achieve_info[$key]['result'] == 2) {
					continue;
				} else {
					if (in_array($value['award'][6],$cfzd)) {									
						call_user_func_array('achievementsModel::'.$script,array($pid,$info,$value,$key));
					} else {
						continue;
					}
				}
			} else {
				if (in_array($value['award'][6],$cfzd)) {
					call_user_func_array('achievementsModel::'.$script,array($pid,$info,$value,$key));
				} else {
					continue;
				}
			}
			$script = null;
		}
		$save_flag = achievementsModel::$save_flag;		
		if($save_flag) {
			// 更新数据及内存
			$player_achieve_info = genStr4Arr(achievementsModel::$player_achieve_info);
			$mc->set(MC.$pid.'_achieve_info', $player_achieve_info, 0, 0);			
			// 如果玩家没有记录
			if($no_record) {
				$db->query("INSERT INTO ol_player_achieve VALUES({$pid}, '{$player_achieve_info}');");
			} else { // 有记录就更新
				$updateItem['cj'] = $player_achieve_info;
				$whereItem['pid'] = $pid;									
				$common->updatetable('player_achieve', $updateItem, $whereItem);
			}
		}
	}	
	
	//处理与玩家基本属性相关的成就
	public static function pinfo($pid,$info,$cjInfo,$key) {
		if ($info[$cjInfo['award'][6]] >= intval($cjInfo['award'][5])) {
			//$player_achieve_info = achievementsModel::$player_achieve_info;
			achievementsModel::$player_achieve_info[$key] = array('result'=>1, 'count'=>$cjInfo['award'][5]);
			achievementsModel::$save_flag = true;			
		}
	}
	//处理座次相关成就
	public static function rank($pid,$info,$cjInfo,$key) {
		if ($info[$cjInfo['award'][6]] <= intval($cjInfo['award'][5])) {
			//$player_achieve_info = achievementsModel::$player_achieve_info;
			achievementsModel::$player_achieve_info[$key] = array('result'=>1, 'count'=>$cjInfo['award'][5]);
			achievementsModel::$save_flag = true;			
		}
	}
	//锻造相关成就
	public static function dz($pid,$info,$cjInfo,$key) {
		//if ($info[$cjInfo['award'][6]] <= intval($cjInfo['award'][5])) {
		if (strstr($cjInfo['award'][5],$info['djid'])) {
			//$player_achieve_info = achievementsModel::$player_achieve_info;
			achievementsModel::$player_achieve_info[$key] = array('result'=>1, 'count'=>0);		
			achievementsModel::$save_flag = true;
		}
	}
	
	//完成任务相关成就
	public static function rw($pid,$info,$cjInfo,$key) {
		if ($info['rwid'] == intval($cjInfo['award'][5])) {		
			//$player_achieve_info = achievementsModel::$player_achieve_info;
			achievementsModel::$player_achieve_info[$key] = array('result'=>1, 'count'=>0);		
			achievementsModel::$save_flag = true;
		}
	}	
	//完成一个日常任务成就
	public static function cflx($pid,$info,$cjInfo,$key) {
		//$player_achieve_info = achievementsModel::$player_achieve_info;
		achievementsModel::$player_achieve_info[$key] = array('result'=>1, 'count'=>0);	
		achievementsModel::$save_flag = true;
	}
	//按次数处理的一些成就
	public static function cscl($pid,$info,$cjInfo,$key) {
		$player_achieve_info = achievementsModel::$player_achieve_info;
		if (isset($player_achieve_info[$key])) {
			$currCount = $player_achieve_info[$key]['count'];
		} else {
			$currCount = 0;
		}
		$newCount = $currCount + 1;
		if ($newCount >= intval($cjInfo['award'][5])) {
			achievementsModel::$player_achieve_info[$key] = array('result'=>1, 'count'=>$cjInfo['award'][5]);
		} else {
			achievementsModel::$player_achieve_info[$key] = array('result'=>0, 'count'=>$newCount);
		}
		achievementsModel::$save_flag = true;
	}
	//获取成就大类信息
	public static function hqcjdlxx($playersid) {
		global $db, $mc, $common;
		$cjInfo = getCjInfo();
		if (!($player_achieve_info = $mc->get(MC.$playersid.'_achieve_info'))) {
			$achieveRet = $db->query("SELECT cj FROM ".$common->tname('player_achieve')." where pid = {$playersid} LIMIT 1");
			$num_rows = $db->num_rows($achieveRet);
			if($num_rows > 0) {
				$rows = $db->fetch_array($achieveRet, MYSQL_ASSOC);
				$player_achieve_info = genArr4str($rows['cj']);
				$mc->set(MC.$playersid.'_achieve_info', $rows['cj'], 0, 0);
			} else {
				$player_achieve_info = array();				
			}		   	
		}
		if(!is_array($player_achieve_info)) {			
			$player_achieve_info = genArr4str($player_achieve_info);
		}
		$cjLxInfo = array();
		//确定每种类型成就总数
		foreach ($cjInfo as $cjKey => $cjValue) {
			$type = $cjValue['type'];	
			$cjLxInfo[$type][] = $cjKey;				
		}
		for ($i = 1; $i < 10; $i++) {
			$sl[$i] = count($cjLxInfo[$i]);
			$ywc[$i] = array();
			$yfx[$i] = array();			
		}	
		if (!empty($player_achieve_info)) {
			foreach ($player_achieve_info as $key => $value) {
				for ($k = 1; $k < 10; $k++) {
					if (in_array($key,$cjLxInfo[$k])) {
						if ($value['result'] == 1) {
							$ywc[$k][] = $key;
						} elseif ($value['result'] == 2) {
							$yfx[$k][] = $key;
						}
					}
					//$key = null;
					//$value = null;
				}				
			}
		}
		$cjlist = array();
		for ($j = 1; $j < 10; $j++) {
			$cjlist[] = array('cjlx' => $j - 1,'cjzsl' => intval($sl[$j]),'cjywcsl' => (count($ywc[$j]) + count($yfx[$j])));
		}
		return array('status'=>0,'cjlist'=>$cjlist);
		 		
	}
	//获取小类成就信息	
	public static function hqcjxlxx($playersid,$cjdlbh) {
		global $db, $mc, $common;	
		$cjInfo = getCjInfo();
		if (!($player_achieve_info = $mc->get(MC.$playersid.'_achieve_info'))) {
			$achieveRet = $db->query("SELECT cj FROM ".$common->tname('player_achieve')." where pid = {$playersid} LIMIT 1");
			$num_rows = $db->num_rows($achieveRet);
			if($num_rows > 0) {
				$rows = $db->fetch_array($achieveRet, MYSQL_ASSOC);
				$player_achieve_info = genArr4str($rows['cj']);
				$mc->set(MC.$playersid.'_achieve_info', $rows['cj'], 0, 0);
			} else {
				$player_achieve_info = array();				
			}		   	
		}
		if(!is_array($player_achieve_info)) {			
			$player_achieve_info = genArr4str($player_achieve_info);
		}
		$xcjlist = array();
		foreach ($cjInfo as $key => $value) {
			if ($value['type'] == $cjdlbh) {
				if (isset($player_achieve_info[$key])) {
					if ($player_achieve_info[$key]['result'] == 1) {
						$cjxldc = 1;
					} elseif ($player_achieve_info[$key]['result'] == 2) {
						$cjxldc = 2;
					} else {
						$cjxldc = 0;
					}
				} else {
					$cjxldc = 0;
				}
				$cjxlhb = intval($key);
				$cjxlmc = $value['info'][0];
				$cjxlms = $value['info'][1];
				$cjxljl = $value['info'][3];
				$cjxljlsl = intval($value['award'][2]);
				$xcjlist[] = array('cjxldc' => $cjxldc,'cjxlhb'=>$cjxlhb,'cjxlmc'=>$cjxlmc,'cjxlms'=>$cjxlms,'cjxljl'=>$cjxljl,'cjxljlsl'=>$cjxljlsl);
			}
		}
		return array('status'=>0,'xcjlist'=>$xcjlist);		
	}	
	//分享成就
	public static function hqcjxlfx($playersid,$cjxlbh) {
		global $db, $mc, $common, $G_PlayerMgr, $cj_lang, $sys_lang;	
		$cjData = getCjInfo();
		$cjInfo = $cjData[$cjxlbh];
		if (empty($cjInfo)) {
			return array('status'=>30,'message'=>$cj_lang['hqcjxlfx_1']);
		} else {
			if (!($player_achieve_info = $mc->get(MC.$playersid.'_achieve_info'))) {
				$achieveRet = $db->query("SELECT cj FROM ".$common->tname('player_achieve')." where pid = {$playersid} LIMIT 1");
				$num_rows = $db->num_rows($achieveRet);
				if($num_rows > 0) {
					$rows = $db->fetch_array($achieveRet, MYSQL_ASSOC);
					$player_achieve_info = achievementsModel::$player_achieve_info = genArr4str($rows['cj']);
					$mc->set(MC.$playersid.'_achieve_info', $rows['cj'], 0, 0);
				} else {
					$player_achieve_info = array();				
				}		   	
			}
			if(!is_array($player_achieve_info)) {			
				$player_achieve_info = achievementsModel::$player_achieve_info = genArr4str($player_achieve_info);
			}
			if ($player_achieve_info[$cjxlbh]['result'] != 1) {
				return array('status'=>30,'message'=>$cj_lang['hqcjxlfx_2']);
			} else {				
				$jllx = $cjInfo['award'][1];
				$jlsl = $cjInfo['award'][2];
				$player = $G_PlayerMgr->GetPlayer($playersid);
		    	if (empty ( $player )) {
					return array('status' => 1021, 'message' => $sys_lang[1]);	
		    	}
		    	$token = _get('token');
		    	$message = $cj_lang['hqcjxlfx_6'].$cjInfo['info'][0].','.$cjInfo['info'][2].$cj_lang['hqcjxlfx_7'];
		    	//太厲害了！我完成了成就XXXX,XXXXXX！立即加入【逆轉風雲】和我一起打造屬於我們的水滸世界！
		    	$shareRes = SocialHub::shareMessage($token, $message);
		    	if ($shareRes === false) {
		    		return array('status'=>30,'message'=>$cj_lang['hqcjxlfx_5']);
		    	}
		    	$roleInfo=$player->baseinfo_;			
				$value['status'] = 0;
		    	if ($jllx == 'coins') {  //奖励铜钱
					$updateRole ['coins'] = $roleInfo ['coins'] + $jlsl;
					if ($updateRole ['coins'] > COINSUPLIMIT) {
						$updateRole ['coins'] = COINSUPLIMIT;
						$jlsl = COINSUPLIMIT - $roleInfo ['coins'];
					}
					$value ['tq'] = $updateRole ['coins'];
					$value ['hqtq'] = $jlsl;
					$common->updatetable('player',$updateRole,"playersid = '$playersid'");
					$common->updateMemCache(MC.$playersid,$updateRole);										
				} elseif ($jllx == 'silver') {  //奖励银票
					$updateRole ['silver'] = $roleInfo ['silver'] + $jlsl;
					$value ['yp'] = $updateRole ['silver'];
					$value ['hqyp'] = $jlsl;
					$common->updatetable('player',$updateRole,"playersid = '$playersid'");
					$common->updateMemCache(MC.$playersid,$updateRole);							
				} else { //奖励道具
					$addItem = $player->AddItems(array($jllx=>$jlsl));
					if ($addItem === false) {
						$value ['status'] = 1020;
						$value['message'] = $cj_lang['hqcjxlfx_3'];
						$jlmc = $cjInfo['info'][3];
						$message = array('playersid'=>$playersid,'toplayersid'=>$playersid,'subject'=>$cj_lang['hqcjxlfx_4'],'genre'=>20,'tradeid'=>0,'interaction'=>0,'is_passive'=>0,'type'=>1,'request'=>addcslashes(json_encode(array('tq'=>0,'yp'=>0,'yb'=>0,'jl'=>0,'items'=>array(array('id'=>$jllx,'mc'=>$jlmc,'num'=>$jlsl)))), '\\'),'message'=>array('xjnr'=>'send message'));
						lettersModel::addMessage($message);							
					} else {
						$bagData = $player->GetClientBag();
						$value ['list'] = $bagData;
					}					
				}
				achievementsModel::$player_achieve_info[$cjxlbh] = array('result'=>2, 'count'=>$cjInfo['award'][5]);
				// 更新数据及内存
				$player_achieve_info = genStr4Arr(achievementsModel::$player_achieve_info);
				$mc->set(MC.$playersid.'_achieve_info', $player_achieve_info, 0, 0);			
				// 如果玩家没有记录				
				$updateItem['cj'] = $player_achieve_info;
				$whereItem['pid'] = $playersid;									
				$common->updatetable('player_achieve', $updateItem, $whereItem);							
				return $value;
			}		
		}		
	}			
}