<?php
// 玄铁的id
define('DALEI_XUNTIE_ID', 10040);
//define('DALEI_KAIFANGSHIJIAN', 30);

function cdt_law($pm){
	if(1 == $pm){
		return 200;
	}elseif(2 == $pm){
		return 180;
	}elseif(3 == $pm){
		return 165;
	}elseif(4 == $pm){
		return 155;
	}elseif(5 == $pm){
		return 145;
	}elseif(6 == $pm){
		return 140;
	}elseif(7 == $pm){
		return 135;
	}elseif(8 == $pm){
		return 130;
	}elseif(9 == $pm){
		return 125;
	}elseif(10 == $pm){
		return 120;
	}elseif($pm <= 12){
		return 118;
	}elseif($pm <= 14){
		return 116;
	}elseif($pm <= 16){
		return 114;
	}elseif($pm <= 18){
		return 112;
	}elseif($pm <=20){
		return 110;
	}elseif($pm <=22){
		return 108;
	}elseif($pm <=24){
		return 106;
	}elseif($pm <=26){
		return 104;
	}elseif($pm <= 28){
		return 102;
	}elseif($pm <= 30){
		return 100;
	}elseif($pm <= 35){
		return 98;
	}elseif($pm <= 40){
		return 96;
	}elseif($pm <= 45){
		return 94;
	}elseif($pm <= 50){
		return 92;
	}elseif($pm <= 60){
		return 90;
	}elseif($pm <= 70){
		return 88;
	}elseif($pm <= 80){
		return 86;
	}elseif($pm <= 90){
		return 84;
	}elseif($pm <= 100){
		return 82;
	}elseif($pm <= 120){
		return 80;
	}elseif($pm <= 140){
		return 78;
	}elseif($pm <= 160){
		return 76;
	}elseif($pm <= 180){
		return 74;
	}elseif($pm <= 200){
		return 72;
	}elseif($pm <= 220){
		return 71;
	}elseif($pm <= 240){
		return 70;
	}elseif($pm <= 260){
		return 69;
	}elseif($pm <= 280){
		return 68;
	}elseif($pm <= 300){
		return 67;
	}elseif($pm <= 350){
		return 66;
	}elseif($pm <= 400){
		return 65;
	}elseif($pm <= 450){
		return 64;
	}elseif($pm <= 500){
		return 63;
	}elseif($pm <= 550){
		return 62;
	}elseif($pm <= 600){
		return 61;
	}elseif($pm <= 650){
		return 60;
	}elseif($pm <= 700){
		return 59;
	}elseif($pm <= 750){
		return 58;
	}elseif($pm <= 800){
		return 57;
	}elseif($pm <= 850){
		return 56;
	}elseif($pm <= 900){
		return 55;
	}elseif($pm <= 950){
		return 54;
	}elseif($pm <= 1000){
		return 53;
	}elseif($pm <= 1100){
		return 52;
	}elseif($pm <= 1200){
		return 51;
	}elseif($pm <= 1300){
		return 50;
	}elseif($pm <= 1400){
		return 49;
	}elseif($pm <= 1500){
		return 48;
	}elseif($pm <= 1600){
		return 47;
	}elseif($pm <= 1700){
		return 46;
	}elseif($pm <= 1800){
		return 45;
	}elseif($pm <= 1900){
		return 44;
	}elseif($pm <= 2000){
		return 43;
	}elseif($pm <= 2200){
		return 42;
	}elseif($pm <= 2400){
		return 41;
	}elseif($pm <= 2600){
		return 40;
	}elseif($pm <= 2800){
		return 39;
	}elseif($pm <= 3000){
		return 38;
	}elseif($pm <= 3200){
		return 37;
	}elseif($pm <= 3400){
		return 36;
	}elseif($pm <= 3600){
		return 35;
	}elseif($pm <= 3800){
		return 34;
	}elseif($pm <= 4000){
		return 33;
	}elseif($pm <= 4200){
		return 32;
	}elseif($pm <= 4400){
		return 31;
	}elseif($pm <= 4600){
		return 30;
	}elseif($pm <= 4800){
		return 29;
	}elseif($pm <= 5000){
		return 28;
	}elseif($pm <= 5500){
		return 27;
	}elseif($pm <= 6000){
		return 26;
	}elseif($pm <= 6500){
		return 25;
	}elseif($pm <= 7000){
		return 24;
	}elseif($pm <= 7500){
		return 23;
	}elseif($pm <= 8000){
		return 22;
	}elseif($pm <= 8500){
		return 21;
	}
	return 20;
}

class daleiModel {
	// 获取下次自动刷新对手的时间
	private static function getRefTime($current_time = null){
		$current_time = is_null($current_time)?time():$current_time;
		$refTime = 0;
		if($current_time > strtotime(date("Y-m-d H:30:00", $current_time))){
			$refTime = mktime(date('H', $current_time)+1,
							  0,
							  0,
							  date('m', $current_time),
							  date('d', $current_time),
							  date('Y', $current_time));
		}else{
			$refTime = mktime(date('H', $current_time),
							  30,
							  0,
							  date('m', $current_time),
							  date('d', $current_time),
							  date('Y', $current_time));
		}

		return $refTime;
	}

	//获取擂台信息
	public static function hqltxx($playersid) {
		global $mc, $common, $db, $_SGLOBAL,$_g_lang;

		$current_time = intval($_SGLOBAL['timestamp']);

		$roleInfo['playersid'] =  $playersid;
		if(!roleModel::getRoleInfo($roleInfo)){
			return array('status'=>601,'message'=>$_g_lang['dalei']['err_pinfo']);
		}

		if($roleInfo['player_level'] < DALEI_START_LEVEL){
			return array('status'=>2,'message'=>$_g_lang['dalei']['need_play_level'].DALEI_START_LEVEL);
		}
		

		// 获取爵位信息
		$returnValue = array('status'=>0);
		$leitaiInfo = daleiModel::getMyLeitai($playersid);
		$myLadder   = daleiModel::findLadder($leitaiInfo);
		$myOpponent = daleiModel::refreshOppo($playersid);

		// 基础数据
		$returnValue = array('status'=>0,
							 'sxsj'=>$myOpponent['lastmatch'] - $current_time,
							 'fwqsj'=>$current_time,
							 'pm' => $leitaiInfo['pm'],
							 'jf' => $leitaiInfo['credits'],
							 'lscs'=>$leitaiInfo['winning'],
							 'sycs'=>$leitaiInfo['dl_c'],
							 'yzjcs'=>$leitaiInfo['buy_c'],
							 'xyyb'=>daleiModel::nextPrice($leitaiInfo['buy_c']),
							 'jfzz'=>cdt_law($leitaiInfo['pm']));

		// 爵位升级数据
		$jw = jwmc(intval($roleInfo['mg_level']));
		$next_jw = jwmc(intval($roleInfo['mg_level'])+1);

		$returnValue['sjdj'] = $next_jw['level'];
		$returnValue['sjsw'] = $next_jw['sw'];

		if($next_jw['jc'] != 0 ) {
			$value['sjlist']=array();

			foreach($next_jw['dj']  as $dj) {
				$item = toolsModel::getItemInfo($dj['id']);
				if($item) {
					$returnValue['sjlist'][]= array('djmc'=>$item['Name'],
													'djsl'=>$dj['sl'],
													'dqsl'=>toolsModel::getItemCount($playersid,$dj['id']),
													'djiid'=>$item['IconID']);
				}
			}
		}

		// 试炼场对手
		$returnValue['dslist'] = $myOpponent['oppos'];

		// 天梯对手
		$returnValue['ph'] = $myLadder;

		return $returnValue;
	}

	// 刷新对手
	public static function sxds($playersid, $type){
		global $common, $db, $mc, $_SGLOBAL, $_g_lang;
		$roleInfo['playersid'] = $playersid;
		
		if(!roleModel::getRoleInfo($roleInfo)){
			return array('status'=>601,'message'=>$_g_lang['dalei']['err_pinfo']);
		}

		if($type==1&&$roleInfo['ingot']<10){
			$message = sprintf($_g_lang['dalei']['need_buy_ingot'], 10, $roleInfo['ingot']);
			return array('status'=>88,'message'=>$message, 'xyxhyb'=>10);
		}
		
		$oppoInfo = daleiModel::refreshOppo($playersid, $type);
		$returnValue['dslist'] = $oppoInfo['oppos'];
		$returnValue['sxsj'] = daleiModel::getRefTime() - time();

		if($type == 1){
			$returnValue['yb'] = $roleInfo['ingot'] - 10;
			$returnValue['xhyb'] = 10;
			$common->updatetable('player', array('ingot'=>$returnValue['yb']), array('playersid'=>$playersid));
			$roleInfo['ingot'] = $returnValue['yb'];
			$mc->set(MC.$playersid, $roleInfo, 0, 3600);
		}
		$returnValue['status'] = 0;

		return $returnValue;
	}

	/*
	 * 获取当前打擂最大排名
	 */
	private static function getMaxPm(){
		global $common, $db, $mc, $_SGLOBAL;
		$_max_pm = $mc->get(MC.'_dalei_max_pm');

		if(!$_max_pm){
			$maxPmSql = "select max(pm) pm from ol_dalei_2";
			$result = $db->query($maxPmSql);

			$maxA = $db->fetch_array($result);

			$_max_pm = $maxA['pm'];
			$mc->set(MC.'_dalei_max_pm', $_max_pm, 0, 600);
		}
		return $_max_pm;
	}

	private static $_leitaiInfo = array();
	
	public static function setMyLeitai($leitaiInfo){
		if(!(isset($leitaiInfo['playersid'])
			 &&isset($leitaiInfo['pm'])
			 &&isset($leitaiInfo['credits'])
			 &&isset($leitaiInfo['winning'])
			 &&isset($leitaiInfo['dl_c'])
			 &&isset($leitaiInfo['buy_c'])
			 &&isset($leitaiInfo['last_buy_time'])
			 &&isset($leitaiInfo['last_dl_time'])
			 &&isset($leitaiInfo['last_cdt_time'])
			 &&isset($leitaiInfo['g_ids'])
			 &&isset($leitaiInfo['flush_times'])
			 &&isset($leitaiInfo['last_flush_time']))){
			return false;
		}

		daleiModel::$_leitaiInfo[$leitaiInfo['playersid']] = $leitaiInfo;
		return true;
	}

	// 获取玩家擂台信息, 考虑到加锁的复杂性这部分的数据不进行缓存
	public static function getMyLeitai($playersid){
		global $common, $db, $mc, $_SGLOBAL;
		$current_time = intval($_SGLOBAL['timestamp']);
		
		if(isset(daleiModel::$_leitaiInfo[$playersid])){
			return daleiModel::$_leitaiInfo[$playersid];
		}
		$leitaiSql = "select * from " . $common->tname('dalei_2');
		$leitaiSql .= " where playersid = '{$playersid}'";
		$result = $db->query($leitaiSql);
		$self_leitai = $db->fetch_array($result);
		
		// 如果在擂台中没有发现就添加玩家擂台信息
		if(!$self_leitai){
			$_def_dl_c = 20;
			$insertSql = "insert into ol_dalei_2(`playersid`, `last_cdt_time`, `dl_c`)";
			$insertSql .= " values($playersid, '{$current_time}', '{$_def_dl_c}')";
			$db->query($insertSql);
			if($db->affected_rows()>0){
				$pm = $db->insert_id();

				// 更新玩家排名表
				$roleInfo['playersid'] = $playersid;
		
				if(!roleModel::getRoleInfo($roleInfo)){
					return array('status'=>601,'message'=>$_g_lang['dalei']['err_pinfo']);
				}
				$include_call = true;
				include(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TimingTasks' . DIRECTORY_SEPARATOR . 'pm_daily.php');
				$zl = calcPlayerPower($playersid, 1);
				$insertZlSql = "insert into ol_player_pm(`playersid`, `mc`, `zl`, `lv`)";
				$insertZlSql .= " values('{$playersid}', '{$roleInfo['nickname']}', '{$zl}', '{$roleInfo['player_level']}')";
				$insertZlSql .= "  ON DUPLICATE KEY UPDATE `zl` = '{$zl}'";
				$db->query($insertZlSql);

				daleiModel::$_leitaiInfo[$playersid] = array('playersid'=>$playersid,
												 'pm'=>$pm,
												 'credits'=>0,
												 'winning'=>0,
												 'dl_c'=>$_def_dl_c,
												 'buy_c'=>0,
												 'last_buy_time'=>0,
												 'last_dl_time'=>date('Y-m-d H:i:s'),
												 'last_cdt_time'=>$current_time,
												 'g_ids'=>'',
												 'flush_times'=>0,
												 'last_flush_time'=>0);
				return daleiModel::$_leitaiInfo[$playersid];
			}else{
				return false;
			}
		}

		$curr_day = date('Ymd', $current_time);
		// 根据最后打擂时间决定初始化打擂次数
		if(date('Ymd', strtotime($self_leitai['last_dl_time'])) != $curr_day){
			$self_leitai['dl_c'] = 20;
		}


		// 试炼场刷新当天有效
		if(date('Ymd',$self_leitai['last_flush_time']) != $curr_day){
			$self_leitai['flush_times'] = 0;
			$self_leitai['last_flush_time'] = $current_time;
		}

		// 跟据最后领取时间记算积分
		$c_num = floor(($current_time - $self_leitai['last_cdt_time'])/600);
		if($c_num > 0){
			$self_leitai['hqjf'] = $c_num * cdt_law($self_leitai['pm']);
			$self_leitai['credits'] += $self_leitai['hqjf'];
			$self_leitai['last_cdt_time'] += $c_num*600;
		}else{
			$self_leitai['hqjf'] = 0;
		}

		// 购买挑战次数的初始化
		if(date('Ymd', $self_leitai['last_buy_time']) != $curr_day){
			$self_leitai['buy_c'] = 0;
		}
		
		daleiModel::$_leitaiInfo[$playersid] = $self_leitai;
		return $self_leitai;
	}

	/*
	 * 刷新试炼场对手,反回四名对手的playersid
	 *
	 * return [opponent_pleyrsid_list]
	 */
	private static function refreshOppo($playersid, $is_ref=0){
		global $common, $db, $mc, $_SGLOBAL;
		$current_time = $_SGLOBAL['timestamp'];
		$leitai_oppo_key = MC.$playersid.'_dalei_oppo';

		if(0 == $is_ref){
			$leitaiInfo = $mc->get($leitai_oppo_key);
			if(!(isset($leitaiInfo['lastmatch'])&&$leitaiInfo['lastmatch'] > $current_time)){
				$leitaiInfo = false;
			}
		}else{
			$leitaiInfo = false;
		}

		// 没有对手信息或主动刷新
		if( !$leitaiInfo || $is_ref){
			$_tmp_oppo_list = array();

			// 真对非主动刷新规则
			while(!$is_ref){
				// 首先找到玩家战力排名,如果首日未进排名就按擂台排名
				$zlsql = "select * from ol_pm_zl where playersid='$playersid'";
				$result = $db->query($zlsql);
				$row = $db->fetch_array($result);
				if(empty($row)){
					break;
				}
				$myPowerRank = $row['pm'];

				// 从低100名玩家范围中找到匹配4个对手的战力排名
				$start = $myPowerRank + 1;
				$step  = 24;
				$zl_oppo_list = array();
				for($i=0; $i<4; $i++){
					$end = $start + $step;
					$tmp_pm = mt_rand($start, $end);
					$zl_oppo_list[] = $tmp_pm;
					$start = $end + 1;
				}

				// 找到数据
				$zlOppoSql = "select d.playersid pid, d.pm pm, p.nickname mc, p.mg_level jw, p.sex xb, p.player_level dj";
				$zlOppoSql .= " from ol_dalei_2 d, ol_pm_zl z, ol_player p";
				$zlOppoSql .= " where d.playersid=z.playersid and d.playersid=p.playersid";
				$zlOppoSql .= " and z.pm in (".implode(',', $zl_oppo_list).")";
				$result = $db->query($zlOppoSql);

				while($row = $db->fetch_array($result)){
					$row['jw'] = jwmc(intval($row['jw']));
					$row['jw'] = $row['jw']['mc'];
					$row['cd'] = 0;
					$row['hyp'] = 0;
					$_tmp_oppo_list[$row['pid']] = $row;
				}
				break;
			}

			// 没有找到4个匹配玩家时按擂台排名找对手
			if(4 > count($_tmp_oppo_list)){
				$need_oppo = 4 - count($_tmp_oppo_list);
				// 获取玩家擂台信息
				$self_leitai = daleiModel::getMyLeitai($playersid);

				// 按照主动刷新次数和当前排名得到区间和步长
				$_star_pm = $self_leitai['pm'];
				$_end_pm  = $_star_pm + 4;

				$_max_pm = daleiModel::getMaxPm();
				if($is_ref != 0){
					$_end_pm = $_max_pm;
				}

				$_end_pm = $_end_pm > $_max_pm ? $_max_pm : $_end_pm;
				if($_end_pm - 4 < $_star_pm){
					$_end_pm = $_star_pm>$_max_pm?$_star_pm:$_max_pm;
					$_star_pm = $_end_pm - 4;
				}

				// 找到当前已匹配对手
				$pmList = array($playersid);
				foreach($_tmp_oppo_list as $_tmp_info){
					$pmList[] = $_tmp_info['pm'];
				}

				// 在区间内随机出需要的对手
				$step_l = floor(($_end_pm - $_star_pm)/4) - 1;
				$_oppo_ids = array();
				if($step_l<=0){
					for($i=0; count($_oppo_ids)<$need_oppo; $i++){
						// 选中的对手已被选中
						if(in_array($tmp_pm, $pmList)){
							$need_oppo++;
							continue;
						}
						$tmp_pm = $_star_pm++;
						if($tmp_pm != $self_leitai['pm']){
							$_oppo_ids[] = $tmp_pm;
						}else{
							$i--;
						}
					}
				}else{
					for($i=0; $i<$need_oppo; $i++){
						$_star_pm++;
						$tmp_pm = mt_rand($_star_pm, $_star_pm + $step_l);
						if(in_array($tmp_pm, $pmList)){
							$need_oppo++;
							continue;
						}
						if($tmp_pm != $self_leitai['pm']){
							$_oppo_ids[] = $tmp_pm;
							$_star_pm += $step_l;
						}else{
							$i--;
						}
					}
				}


				// 获取对手信息并添加到缓冲
				$oppoSql = "select d.playersid pid, pm, nickname mc, mg_level jw, sex xb, p.player_level dj";
				$oppoSql .= " from ol_dalei_2 d, ol_player p";
				$oppoSql .= " where d.playersid = p.playersid and d.pm in (".implode(',', $_oppo_ids).")";
				$oppoSql .= " and d.playersid<>'{$playersid}' order by pm";
				$result = $db->query($oppoSql);

				while($row = $db->fetch_array($result)){
					$row['jw'] = jwmc(intval($row['jw']));
					$row['jw'] = $row['jw']['mc'];
					$row['cd'] = 0;
					$row['hyp'] = 0;
					$_tmp_oppo_list[$row['pid']] = $row;
				}
			}

			// 对手需要按对手擂台进行排序
			$sort_array = array();
			foreach($_tmp_oppo_list as $key=>$_oppoInfo){
				$sort_array[] = $_oppoInfo['pm'];
			}
			array_multisort($sort_array ,SORT_ASC ,$_tmp_oppo_list);

			foreach($_tmp_oppo_list as $final_oppo){
				$leitaiInfo['oppos'][$final_oppo['pid']] = $final_oppo;
			}
			$leitaiInfo['lastmatch'] = daleiModel::getRefTime($current_time);
			$mc->set($leitai_oppo_key, $leitaiInfo, 0, $leitaiInfo['lastmatch']-$current_time);
		}

		foreach($leitaiInfo['oppos'] as $pid=>$oppoer){
			if($oppoer['cd']>$current_time){
				$leitaiInfo['oppos'][$pid]['cd'] = $oppoer['cd'] - $current_time;
			}else{
				$leitaiInfo['oppos'][$pid]['cd'] = 0;
			}
		}

		$leitaiInfo['oppos'] = array_values($leitaiInfo['oppos']);
		return $leitaiInfo;
	}

	/*
	 * 打擂台
	 * $type 1 top10, 2 天梯, 3 试炼场
	 */
	public static function dlt($playersid, $targetpid, $type) {
		global $common,$mc,$db, $_SGLOBAL,$_g_lang;
		$current_time = $_SGLOBAL['timestamp'];

		if($playersid == $targetpid){
			return array('status'=>1003);
		}

		$roleInfo['playersid'] =  $playersid;
		roleModel::getRoleInfo($roleInfo);
		if($roleInfo['player_level']<DALEI_START_LEVEL)
			return array('status'=>2,'message'=>$_g_lang['dalei']['need_play_level'].DALEI_START_LEVEL);

		$leitaiInfo = daleiModel::getMyLeitai($playersid);

		if(0 >= $leitaiInfo['dl_c'] && $type!=1){
			return array('status'=>1002,'message'=>$_g_lang['dalei']['duel_times_out']);
		}
		// top10, 不检查对手,不交换排名,不扣场次,不加积分

		// 试炼场, 检查对手,不交换排名,扣场次,加积分
		if(3 == $type){
			$targetsinfo = $mc->get(MC.$playersid.'_dalei_oppo');
			if(!$targetsinfo)
				return array('status'=>1002,'message'=>$_g_lang['dalei']['no_find_oppo']);

			if(!isset($targetsinfo['oppos']) || !isset($targetsinfo['oppos'][$targetpid]))
				return array('status'=>1002,'message'=>$_g_lang['dalei']['no_find_oppo']);

			if($targetsinfo['lastmatch'] < $current_time)
				return array('status'=>1002,'message'=>$_g_lang['dalei']['oppo_info_timeout']);

			$ds = $targetsinfo['oppos'][$targetpid];
			if($current_time < intval($ds['cd'])){
				return array('status'=>1002,'message'=>$_g_lang['dalei']['need_cool']);
			}
		}

		// 检查对手信息
		$dRoleInfo['playersid'] = $targetpid;
		$roleRes = roleModel::getRoleInfo($dRoleInfo,false);
		if (empty($roleRes))
			return array('status'=>601,'message'=>$_g_lang['dalei']['err_pinfo']);

		if(1 == $dRoleInfo['is_reason'])
			return array('status'=>21,'message'=>$_g_lang['dalei']['deny_user']);
		if($dRoleInfo['player_level']<DALEI_START_LEVEL){
			return array('status'=>21, 'message'=>$_g_lang['dalei']['no_find_oppo']);
		}

		$leitai_T_info = daleiModel::getMyLeitai($targetpid);

		// 天梯, 检查对手是否是范围内对手,交换排名,扣场次,加积分
		// 并且开始时需要锁住战斗双方
		if(2 == $type){
			$minPm = 0;
			// 获得排名下限
			if($leitaiInfo['pm']<=60){
				$minPm = $leitaiInfo['pm'] - 4;
			}else{
				// 这里适当放宽验证条件
				$minPm = ceil($leitaiInfo['pm']*8.9/10)-4;
			}
			if($minPm > $leitai_T_info['pm'] 
			   || $leitaiInfo['pm'] < $leitai_T_info['pm']){
				if($leitaiInfo['pm']>4 && $leitai_T_info['pm']>5){
					$newOpponent = daleiModel::findLadder($leitaiInfo);
					return array('status'=>1002,
								 'message'=>$_g_lang['dalei']['opponent_leaved'],
								 'ph' => $newOpponent,
								 'pm' => $leitaiInfo['pm'],
								 'jfzz' => cdt_law($leitaiInfo['pm']));
				}
			}

			// 如果攻击方排名低则需要交换排名对双方加锁30秒,这里按高位向低位加锁
			if($leitai_T_info['pm'] < $leitaiInfo['pm']){
				$lock_num = 0;
				while(!$mc->add(MC.'_dalei_lock_'.$playersid, 1, 0, 30)){
					// sleep 1% second, max 3 times
					if(3 <= $lock_num){
						return array('status'=>1002,
									 'message'=>$_g_lang['dalei']['wait_server_try_late']);
					}
					usleep(1000);
					$lock_num ++;
				}
				$lock_num = 0;
				while(!$mc->add(MC.'_dalei_lock_'.$targetpid, 1, 0, 30)){
					// sleep 1% second, max 3 times
					if(3 <= $lock_num){
						$mc->delete(MC.'_dalei_lock_'.$playersid);
						return array('status'=>1002,
									 'message'=>$_g_lang['dalei']['wait_server_try_late']);
					}
					usleep(1000);
					$lock_num ++;
				}
			}
		}

		// 获取双方武将基础信息
		$aGinfo = cityModel::getGeneralInfo($playersid);
		if (empty($aGinfo)) {
			//当前您没有可以出战的武将，请招募一名武将！
			return array('status'=>1004,'message'=>$_g_lang['dalei']['please_set_general']);
		}
		$dGinfo = cityModel::getGeneralInfo($targetpid);

		//找出攻击方可出征的将领
		$a_ginfo = array();
		$a_gid_array = array();
		$j = 0;
		foreach ($aGinfo as $aGinfoValue) {
			if ($aGinfoValue['f_status'] == 1) {
				$j++;
				$aGinfoValue['general_sort_data'] = $aGinfoValue['general_sort'];
				$aGinfoValue['general_sort'] = $j;
				$a_ginfo[] = $aGinfoValue;
				$a_gid_array[] = $aGinfoValue['intID'];
			}
		}
		if (empty($a_ginfo)) {
			return array('status'=>1004,'message'=>$_g_lang['dalei']['please_set_general']);
		}
		
		// 检查对手上次打擂数据
		$d_gids = trim($leitai_T_info['g_ids']);
		if(!empty($d_gids)){
			$d_gid_array = explode('_', $d_gids);
		}
		$d_ginfo = array();
		if (!empty($dGinfo)) {
			$k = 0;
			// 优先查找上次打擂对手
			if(!empty($d_gid_array)){
				foreach ($dGinfo as $dGinfoValue) {
					if (in_array($dGinfoValue['intID'], $d_gid_array)) {
						$k++;
						$dGinfoValue['general_sort_data'] = $dGinfoValue['general_sort'];
						$dGinfoValue['general_sort'] = $k;
						$d_ginfo[] = $dGinfoValue;
					} 
				}
			}
			
			// 如果上次打擂为空就按出征阵容
			if(empty($d_ginfo)){
				foreach ($dGinfo as $dGinfoValue) {
					if ($dGinfoValue['f_status'] == 1) {
						$k++;
						$dGinfoValue['general_sort_data'] = $dGinfoValue['general_sort'];
						$dGinfoValue['general_sort'] = $k;
						$d_ginfo[] = $dGinfoValue;
					}
				}
			}

			// 如果没有设置出征则按最大可出征武将出征
			if(empty($d_ginfo)){
				$maxGoTo = wjczsl($dRoleInfo['player_level']);
				for($i=0; $i<$maxGoTo ;$i++){
					if(empty($dGinfo)){
						break;
					}
					$k++;
					$dGinfoValue = array_shift($dGinfo);
					$dGinfoValue['general_sort_data'] = $dGinfoValue['general_sort'];
					$dGinfoValue['general_sort'] = $k;
					$d_ginfo[] = $dGinfoValue;
				}
			}
		}
		
		//找出防守方可出征将领
		if (!empty($d_ginfo)) {
			$gf_jwdj = $roleInfo['mg_level'];
			$sf_jwdj = $dRoleInfo['mg_level'];
			$fightResult = actModel::fight($a_ginfo, $d_ginfo, 0,0,0,1,$gf_jwdj,$sf_jwdj);
			
			// 评分
			$pfArr = fightModel::getPf($a_ginfo, $roleInfo);
			$value['wjzdlpf'] = $pfArr['wjzdlpf'];
			$value['djpf'] = $pfArr['djpf'];
			$value['tfpf'] = $pfArr['tfpf'];
			$value['zbpf'] = $pfArr['zbpf'];
			$value['jwpf'] = $pfArr['jwpf'];
			$value['jnpf'] = $pfArr['jnpf'];

			$dfpfArr = fightModel::getPf($d_ginfo, $dRoleInfo);
			$value['gkpf'] = $dfpfArr['wjzdlpf'];
			$value['djpfzb'] = $dfpfArr['djpf'];
			$value['tfpfzb'] = $dfpfArr['tfpf'];
			$value['zbpfzb'] = $dfpfArr['zbpf'];
			$value['jwpfzb'] = $dfpfArr['jwpf'];
			$value['jnpfzb'] = $dfpfArr['jnpf'];
			$value['tgsxpf'] = $dfpfArr['wjzdlpf'];
			
			// 构造战斗流程相关数据
			$fightdata['wjxm']  = $value['wjxm'] = stripcslashes($roleInfo['nickname']);
			$fightdata['gwxm']  = $value['gwxm'] = stripcslashes($dRoleInfo['nickname']);
			$fightdata['a_level']  = $value['a_level'] = intval($roleInfo['player_level']);
			$fightdata['a_sex']  = $value['a_sex'] = intval($roleInfo['sex']);
			$fightdata['d_level']  = $value['d_level'] = intval($dRoleInfo['player_level']);
			$fightdata['d_sex']  = $value['d_sex'] = intval($dRoleInfo['sex']);
			$fightdata['begin']  = $value['begin'] = $fightResult['begin'];    //开始士兵情形

			$round = $fightResult['round'];     //战斗回合
			$newround = heroCommon::arr_foreach($round);
			$fightdata['round']  = $value['round'] = $newround;
			$round = null;	
			$newround = null;	
			// 根据返回的数据来判断输赢
			$soldierNum = 0;  //初始化防守士兵数量
			$attack_result = $fightResult['result'];
			$attackLeft = array(0);
			for ($i = 0; $i < count($attack_result); $i++) {
				$role = $attack_result[$i]['role'];  //攻击方还是防守方
				if ($role == 1) {
					$attack_soldier[] = $attack_result[$i]['command_soldier'];
					//攻击方每个将领所剩兵力
					$attackGeneralLeftSoldier[$attack_result[$i]['id']] = $attack_result[$i]['command_soldier'];  
					if ($attack_result[$i]['command_soldier'] > 0) {
						$attackLeft[] = $attack_result[$i]['command_soldier'];
					}
				} else {
					$defend_soldier[] = $attack_result[$i]['command_soldier'];
					$defendGeneralLeftSoldier[$attack_result[$i]['id']] = $attack_result[$i]['command_soldier'];
				}
			}
			$attack_soldier_left = array_sum($attack_soldier);   // 攻击方所剩人数
		} else {
			$attack_soldier_left = 1;
		}


		$win = $attack_soldier_left >0?true:false;

		// 连胜和连胜场次修改和反回积分数据
		// top10 不能触发任务和活动,不修改玩家数据
		$updateArr = array();
		$value['status'] = 0;
		if(1 != $type){
			$ewjf = 0;
			if ($win) {
				if (empty($d_ginfo)) {
					$value['status'] = 1003;
					$value['message'] = $_g_lang['dalei']['oppo_no_general'];
				}
				$winning = $leitaiInfo['winning'] + 1;

				if ($winning % 3 == 0)
					$ewjf += 150;
				if ($winning % 10 == 0)
					$ewjf += 400;
				if ($winning % 20 == 0)
					$ewjf += 1000;

				$value['ewjf'] = $ewjf;
				$value['jcjf'] = 150;  //基本得分
				$value['lscc'] = $winning;
			} else {
				$value['status'] = 1001;
				$value['jcjf'] = 50;
				$winning = 0;
			}

			$value['hqjf'] = $leitaiInfo['hqjf'] + $value['jcjf'] + $ewjf;
			$value['ltjf'] = $value['jcjf'] + $ewjf + $leitaiInfo['credits'];

			// 修改连胜场次
			$updateArr['winning'] = $winning;
			$updateArr['credits'] = $value['ltjf'];
			$updateArr['last_cdt_time'] = $leitaiInfo['last_cdt_time'];

			// 更新最后打擂时间
			$updateArr['last_dl_time'] = date('Y-m-d H:i:s', $current_time);
			$updateArr['dl_c'] = $leitaiInfo['dl_c'] - 1;

			$value['sycs'] = $updateArr['dl_c'];

			//如果是天梯,删除锁,如果胜利交换双方排名
			if(2 == $type){
				if($win){
					//更新攻击方武将
					$updateArr['g_ids'] = implode('_', $a_gid_array);
					$updateArr['winning'] = $winning;

					// 如果攻击方排名低则更新双方排名
					if($leitai_T_info['pm'] < $leitaiInfo['pm']){
						$_tmp_pm = $leitaiInfo['pm'];
						$leitaiInfo['pm'] = $leitai_T_info['pm'];
						$leitai_T_info['pm'] = $_tmp_pm;

						// 更新top10数据
						if($leitaiInfo['pm'] <= 10){
							$mc->delete(MC.'_dalei_top10');
						}

						$leitaiInfo = array_merge($leitaiInfo, $updateArr);
						// 擂台表中没有hqjf字段要清理
						$_leitaiInfo = $leitaiInfo;
						unset($_leitaiInfo['hqjf']);
						$_leitai_T_info = $leitai_T_info;
						unset($_leitai_T_info['hqjf']);
						$tableKeys = array_keys($_leitaiInfo);
						$replacePmSql = "replace into ol_dalei_2(".implode(',', $tableKeys).")";
						$replacePmSql .= " values('".implode("','", array_values($_leitaiInfo))."')";
						$replacePmSql .= " ,('".implode("','", array_values($_leitai_T_info))."')";
						$db->query($replacePmSql);
					
						// 如果战胜,就更新天梯对手
						$value['ph'] = daleiModel::findLadder($leitaiInfo);
						$value['pm'] = $leitaiInfo['pm'];
						
						$value['jfzz'] = cdt_law($leitaiInfo['pm']);
					}else{
						$common->updatetable('dalei_2',
											 $updateArr,
											 array('playersid'=>$roleInfo['playersid']));
					}
				}else{
					$common->updatetable('dalei_2',
										 $updateArr,
										 array('playersid'=>$roleInfo['playersid']));
				}
				$mc->delete(MC.'_dalei_lock_'.$playersid);
				$mc->delete(MC.'_dalei_lock_'.$targetpid);
			}

			// 如果是试炼场,更新玩家对手数据和积分
			if(3 == $type){
				$targetsinfo['oppos'][$targetpid]['cd'] = $current_time + 180;
				$mc->set(MC.$playersid.'_dalei_oppo',$targetsinfo,0,3660);

				// 更新方式与refreshOppo中的一致
				foreach($targetsinfo['oppos'] as $pid=>$oppoer){
					if($oppoer['cd']>$current_time){
						$targetsinfo['oppos'][$pid]['cd'] = $oppoer['cd'] - $current_time;
					}else{
						$targetsinfo['oppos'][$pid]['cd'] = 0;
					}
				}
				$value['dslist'] = array_values($targetsinfo['oppos']);

				$common->updatetable('dalei_2',
									 $updateArr,
									 array('playersid'=>$roleInfo['playersid']));
			}

			// 任务和活动
			$rwid = questsController::OnFinish($roleInfo,"'dlt'");  //打擂台任务
			if (!empty($rwid)) {
				$value['rwid'] = $rwid;
			}
			hdProcess::run(array('fight_countDalei', 'fight_dailyDalei'), $roleInfo, 1);
		}else{
			if ($win) {
				if (empty($d_ginfo)) {
					$value['status'] = 1003;
					$value['message'] = $_g_lang['dalei']['oppo_no_general'];
				}
			} else {
				$value['status'] = 1001;
			}
		}
		return $value;
	}

	//加速cd
	public static function jscd($playersid, $targetpid) {
		global $common,$mc,$db, $_SGLOBAL, $_g_lang;
		$xhyp = 5;
		$current_time = $_SGLOBAL['timestamp'];

		$targetsinfo = $mc->get(MC.$playersid.'_dalei_oppo');

		if(!$targetsinfo){
			return array('status'=>602,'message'=>$_g_lang['dalei']['no_find_oppo']);
		}
		if(!isset($targetsinfo['oppos']) || !$targetsinfo['oppos'][$targetpid]){
			return array('status'=>603,'message'=>$_g_lang['dalei']['no_find_oppo']);
		}
		if($targetsinfo['lastmatch'] < $current_time){
			return array('status'=>604,'message'=>$_g_lang['dalei']['oppo_info_timeout']);
		}

		$roleInfo['playersid'] =  $playersid;
		if(!roleModel::getRoleInfo($roleInfo)){
			return array('status'=>605,'message'=>$_g_lang['dalei']['err_pinfo']);
		}

		$ds = $targetsinfo['oppos'][$targetpid];

		if($ds['cd'] > $current_time)  {
			if($roleInfo['silver'] < $xhyp){
				return array('status'=>606,
							 'message'=>$_g_lang['dalei']['need_silver'],
							 'yp'=>$roleInfo['silver'],'xyyp'=>$xhyp);
			}
			$targetsinfo['oppos'][$targetpid]['cd']= 0;
			$updateRole['silver'] = $roleInfo['silver'] - $xhyp;

			$updateRoleWhere ['playersid'] = $playersid;
			$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
			$common->updateMemCache ( MC . $playersid, $updateRole );

			$mc->set(MC.$playersid.'_dalei_oppo',$targetsinfo,0,3660);
			return array('status'=>0,'yp' =>$updateRole['silver'],'xhyp'=>$xhyp);
		}
		else
			return array('status'=>0);
	}

	/**
	 * 增加擂台次数
	*/
	public static function zjcs($playersid) {
		global $mc, $common, $db,$_SGLOBAL,$_g_lang;

		$current_time = $_SGLOBAL['timestamp'];

		$roleInfo['playersid'] =  $playersid;
		roleModel::getRoleInfo($roleInfo);
		if($roleInfo['player_level']<DALEI_START_LEVEL){
			return array('status'=>2,'message'=>$_g_lang['dalei']['need_play_level'].DALEI_START_LEVEL);
		}

		$leitaiInfo = daleiModel::getMyLeitai($playersid);

		// 购买次数与购买价格
		$buy_c = $leitaiInfo['buy_c'];
		$curr_day = date('Ymd');
		if($curr_day != date('Ymd', $leitaiInfo['last_buy_time'])){
			$buy_c = 0;
		}
		$price = daleiModel::nextPrice($buy_c);
		
		if($roleInfo['ingot'] < $price){
			$_message = sprintf($_g_lang['dalei']['need_buy_ingot'], $price, $roleInfo['ingot']);
			return array('status'=>88,'message'=>$_message,'yb'=>$roleInfo['ingot'], 'xyxhyb'=>$price);
		}
		$leitaiInfo['credits'] += 1000;

		$buy_c++;
		$leitaiInfo['dl_c'] += 10;
		$leitaiInfo['last_dl_time'] = $current_time;
		$sql = "update ".$common->tname('dalei_2');
		$sql .= " set dl_c='{$leitaiInfo['dl_c']}',last_dl_time=from_unixtime('{$current_time}', '%Y%m%d'),";
		$sql .= "  buy_c={$buy_c},last_buy_time='{$current_time}',";
		$sql .= " credits={$leitaiInfo['credits']}, last_cdt_time='{$leitaiInfo['last_cdt_time']}'";
		$sql .= " where playersid='$playersid'";
		$db->query($sql);

		$updateRole['ingot'] = $roleInfo['ingot'] - $price;
		$updateRoleWhere ['playersid'] = $playersid;
		$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
		$common->updateMemCache ( MC . $playersid, $updateRole );

		// 计算下次加速元宝消耗
		$xyybnew = daleiModel::nextPrice($buy_c);
		
		// 计算今日剩余场次
		$dl_c = $leitaiInfo['dl_c'];

		return array('status'=>0,
					 'xhyb'=>$price,
					 'yb'=>$updateRole['ingot'],
					 'xyyb'=>$xyybnew,
					 'yzjcs'=>$buy_c,
					 'sycs'=>$dl_c,
					 'ltjf'=>$leitaiInfo['credits'],
					 'hqjf'=>1000+$leitaiInfo['hqjf']);
	}

	private static function nextPrice($buy_c){
		$price = 0;
		switch($buy_c){
		case 0:
			$price = 50;
			break;
		case 1:
			$price = 100;
			break;
		case 2:
			$price = 200;
			break;
		case 3:
			$price = 500;
			break;
		default:
			$price = 1000;
		}
		return $price;
	}

	//提升爵位
	public static function tsjw($playersid) {
		global $mc, $common, $db, $_SGLOBAL,$G_PlayerMgr,$_g_lang;
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
		$next_jw = jwmc(intval($roleInfo['mg_level'])+1);
		if($next_jw['jc'] == 0)
			return array('status'=> 1003);//爵位已达到最高

		if($next_jw['level'] > $roleInfo['player_level']){
			$_message = sprintf($_g_lang['dalei']['next_need_p_level'], $next_jw['level']);
			return array('status'=> 1102,'message'=>$_message);
		}

		$updateRole = array();
		if(!(defined('PRESSURE_TEST_DEBUG') && isset($_GET['debug']))){
			if($next_jw['sw']) {
				if($next_jw['sw']> $roleInfo['prestige'])
					return array('status'=> 1102,'message'=>$_g_lang['dalei']['need_shengwang']);
				else
					$updateRole['prestige'] = $roleInfo['prestige'] - $next_jw['sw'];
			}
		}
		else{
			$updateRole['prestige'] = $roleInfo['prestige'] - $next_jw['sw'];
		}

		if($next_jw['tq']) {
			if($next_jw['tq']> $roleInfo['coins'])
				return array('status'=> 1102,'message'=>$_g_lang['dalei']['need_coin']);
			else {
				$updateRole['coins'] = $roleInfo['coins'] - $next_jw['tq'];
				$value['tq'] = $updateRole ['coins'];
				$value['xhtq'] = $next_jw['tq'];
			}
		}

		if($next_jw['yp']) {
			if($next_jw['yp'] > $roleInfo['silver'])
				return array('status'=> 1102,'message'=>$_g_lang['dalei']['need_silver']);
			else {
				$updateRole['silver'] = $roleInfo['silver'] - $next_jw['yp'];
				$value['yp'] = $updateRole ['silver'];
				$value['xhyp'] = $next_jw['yp'];
			}
		}

		if($next_jw['yb']) {
			if($next_jw['yb']> $roleInfo['ingot'])
				return array('status'=> 1102,'message'=>$_g_lang['dalei']['need_ingot']);
			else {
				$updateRole['ingot'] = $roleInfo['ingot'] - $next_jw['yb'];
				$value['yb'] = $updateRole ['ingot'];
				$value['xhyb'] = $next_jw['yb'];
			}
		}

		$delItems = array();
		foreach($next_jw['dj'] as $dj) {
			$delItems[$dj['id']] = $dj['sl'];
			$itemNum = toolsModel::getItemCount($playersid, $dj['id']);
			if($itemNum < $dj['sl']) {
				$item = toolsModel::getItemInfo($dj['id']);
				$name = $item['Name'];
				$_message = sprintf($_g_lang['dalei']['need_anything'], $name);
				return array('status'=> 1102,'message'=>$_message);
			}
		}

		//完全验证通过,删除物品
		$player = $G_PlayerMgr->GetPlayer($playersid);
		$player->DeleteItemByProto($delItems, false);

		$updateRole ['mg_level'] = $cjInfo ['mg_level'] = $value['jwid'] = $roleInfo ['mg_level'] + 1;
		achievementsModel::check_achieve($playersid,$cjInfo,array('mg_level'));

		$updateRoleWhere ['playersid'] = $playersid;
		$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
		$common->updateMemCache ( MC . $playersid, $updateRole );

		$value['status'] = 0;
		$value['dqjw'] = $next_jw['mc'];
		$value['dqjc'] = $next_jw['jc'];
		$value['swz'] = $updateRole ['prestige']; //声望值
		if($next_jw['dj']) {
			$bagData = $player->GetClientBag();
			$value['list'] = $bagData;
		}
		$next_jw = jwmc($updateRole ['mg_level']+1);
		$value['xjjw'] = $next_jw['mc'];
		$value['xjjc'] = $next_jw['jc'];

		$value['sjdj'] = $next_jw['level'];
		$value['sjsw'] = $next_jw['sw'];
		if($next_jw['jc'] != 0 ) {
			$value['sjlist']=array();
			foreach($next_jw['dj']  as $dj) {
				$item = toolsModel::getItemInfo($dj['id']);
				if($item) {
					$value['sjlist'][]= array('djmc'=>$item['Name'],
											  'djsl'=>$dj['sl'],
											  'djiid'=>$item['IconID'],
											  'dqsl'=>toolsModel::getItemCount($playersid,$dj['id']));
				}
			}
		}

		$getInfo['playersid'] = $playersid;
		$showValue = cityModel::getGeneralList($getInfo,1,true);
		$value ['generals'] = $showValue ['generals'];
		$roleInfo['rwsl'] = $updateRole ['mg_level'];
		$rwid = questsController::OnFinish($roleInfo,"'wjjw'");
		if (!empty($rwid)) {
			$value ['rwid'] = $rwid;
		}
		return $value;
	}

	private static function findLadder($leitaiInfo){
		global $db;
		$_ladderList = array();
		$_pm = $leitaiInfo['pm'];
		if($_pm > 60){
			$star   = $_pm;
			$step_l = ceil($_pm/50);
			for($i=0; $i<4; $i++){
				$next = $star - $step_l;
				// 这里防止最近一个对手匹配到自己
				$_ladderList[] = mt_rand($next-1, $star-1);
				$star = $next-1;
			}
		}else{
			$reverse = 0;
			for($i=1; $i<=4; $i++){
				if(($_pm-$i)>=1){
					$_ladderList[] = $_pm - $i;
				}else{
					$reverse ++;
				}
			}

			for($i=1; $i<=$reverse; $i++){
				$_ladderList[] = $_pm +$i;
			}
		}
		
		// 查询对手信息
		$ladderSql = "select d.playersid id, d.pm, p.nickname mc, p.mg_level jw, m.zl, p.player_level dj, p.sex xb";
		$ladderSql .= " from ol_dalei_2 d, ol_player p, ol_player_pm m";
		$ladderSql .= " where d.playersid=p.playersid and p.playersid=m.playersid";
		$ladderSql .= ' and (d.pm in ('.implode(',', $_ladderList).") or d.playersid='{$leitaiInfo['playersid']}')";
		$ladderSql .= ' order by d.pm limit 5';

		$result = $db->query($ladderSql);
		$ladderInfo= array();
		while($row = $db->fetch_array($result)){
			$row['jw'] = jwmc($row['jw']);
			$row['jw'] = $row['jw']['mc'];
			$ladderInfo[] = $row;
		}
		return $ladderInfo;
	}

	/**
	 * 获取天梯对手
	 */
	public static function getLadder($playersid) {
		global $common, $db, $_g_lang;
		$roleInfo['playersid'] = $playersid;
		if(!roleModel::getRoleInfo($roleInfo)){
			return array('status'=>601,'message'=>$_g_lang['dalei']['err_pinfo']);
		}
		if($roleInfo['player_level']<DALEI_START_LEVEL){
			return array('status'=>2,'message'=>$_g_lang['dalei']['need_play_level']);
		}

		$leitaiInfo = daleiModel::getMyLeitai($playersid);
		if(!$leitaiInfo){
			return array('status'=>1002, 'message'=>$_g_lang['dalei']['no_paiming']);
		}

		// 找到天梯对手排名
		$ladderInfo = daleiModel::findLadder($leitaiInfo);
		
		$leitaiInfo = daleiModel::getMyLeitai($playersid);
		return array('status'=>0, 'dslist'=>$ladderInfo, 'jf'=>$leitaiInfo['credits'], 'pm'=>$leitaiInfo['pm']);
	}

	/*
	 * top10 
	 */
	public static function top10() {
		global $common, $db, $mc, $_g_lang;

		$top10_key = MC.'_dalei_top10';
		$top10 = $mc->get($top10_key);

		if($top10){
			return array('status'=>0, 'ph'=>$top10);
		}

		$top10Sql = "select d.playersid id, p.nickname mc, p.player_level dj, p.mg_level jw, m.zl, d.pm, p.sex xb";
		$top10Sql .= " from ol_dalei_2 d, ol_player_pm m, ol_player p";
		$top10Sql .= " where d.playersid=m.playersid and m.playersid=p.playersid";
		$top10Sql .= " order by d.pm asc limit 10";

		$result = $db->query($top10Sql);
		$top10Arr = array();
		while($row = $db->fetch_array($result)){
			$row['jw'] = jwmc($row['jw']);
			$row['jw'] = $row['jw']['mc'];
			$top10Arr[] = $row;
		}
		$mc->set($top10_key, $top10Arr, 0, 600);

		return array('status'=>0, 'ph'=>$top10Arr);
	}

	// 获取对手阵容
	public static function hqdlzr($playersid, $tuid){
		global $common, $db, $mc, $_SGLOBAL, $_g_lang;

		$current_time = $_SGLOBAL['timestamp'];
		$mcKey = MC.$playersid.'_hqdlzr';
		
		$rc = $mc->get($mcKey);
		$rcList = array();
		if((!empty($rc))&&$rc['expire_t']>$current_time){
			$rcList = explode('_', $rc['value']);
		}else{
			$rcList = array();
		}

		$returnValue = array();
		// 如果在一个自然刷新时段内(一个小时内)查询过对手阵容,那么就可以直接查询
		if(!in_array($tuid, $rcList)){
			$roleInfo['playersid'] = $playersid;
			roleModel::getRoleInfo($roleInfo);

			if(empty($roleInfo)){
				return array('status'=>601, 'message'=>$_g_lang['dalei']['err_pinfo']);
			}

			if($roleInfo['silver']<5){
				return array('status'=>68, 'xyxhyp'=>5);
			}
			$returnValue['yp'] = $roleInfo['silver'] - 5;
			$returnValue['xhyp'] = 5;
			$common->updatetable('player', array('silver'=>$roleInfo['silver']), array('playersid'=>$playersid));
			$roleInfo['silver'] = $returnValue['yp'];
			$mc->set(MC.$playersid, $roleInfo, 0, 3600);

			$rcList[] = $tuid;
			$rcValue = implode('_', $rcList);
			$exp_t = mktime(date('H')+1, 0, 0, date('m'), date('d'), date('Y'));
			$mc->set($mcKey, array('value'=>$rcValue, 'expire_t'=>$exp_t), 0, 3600);
		}
		
		$dGinfo = cityModel::getGeneralList(array('playersid'=>$tuid));

		$sltSqt = "select g_ids from ".$common->tname('dalei_2');
		$sltSqt .= " where playersid = '{$tuid}'";
		$result = $db->query($sltSqt);
		$rows = $db->fetch_array($result);
		$d_gids = trim($rows['g_ids']);
		
		if(!empty($d_gids)){
			$d_gid_array = explode('_', $d_gids);
		}
		$d_ginfo = array();
		if (isset($dGinfo['generals'])&&!empty($dGinfo['generals'])) {
			if(!empty($d_gid_array)){
				foreach ($dGinfo['generals'] as $dGinfoValue) {
					if (in_array($dGinfoValue['gid'], $d_gid_array)) {
						$d_ginfo[] = $dGinfoValue;
					} 
				}
			}
			
			if(empty($d_ginfo)){
				foreach ($dGinfo['generals'] as $dGinfoValue) {
					if ($dGinfoValue['czzt2'] == 1) {
						$d_ginfo[] = $dGinfoValue;
					}
				}
			}

			if(empty($d_ginfo)){
				$dRoleInfo['playersid'] = $playersid;
				roleModel::getRoleInfo($dRoleInfo);
				$maxGoTo = wjczsl($dRoleInfo['player_level']);
				for($i=0; $i<$maxGoTo ;$i++){
					if(empty($dGinfo)){
						break;
					}
					$dGinfoValue = array_shift($dGinfo['generals']);
					$d_ginfo[] = $dGinfoValue;
				}
			}
		}

		$returnValue['status'] = 0;
		$returnValue['generals'] = $d_ginfo;

		return $returnValue;
	}
	
	//兑换列表
	public static function dhlb() {
		global $city_var_lang;
		$data = jfdhsj();
		foreach ($data as $dataValue) {
			$id = intval($dataValue['intID']);
			$dhlx = intval($dataValue['dhlx']);
			$dhsl = intval($dataValue['dhsl']);
			$xhjf = intval($dataValue['xhjf']);
			$djid = $dataValue['djid'];
			$xzlx = intval($dataValue['xzlx']);
			$dhsx = intval($dataValue['dhsx']);
			if ($dhlx == 1) {
				$list[] = array('id'=>$id,'iid'=>'icon_sw','jf'=>$xhjf,'sl'=>$dhsl);
			} else {
				$djInfo = ConfigLoader::GetItemProto($djid);
				$mc = $djInfo['Name'];
				$iid = $djInfo['IconID'];
				$pj  = toolsModel::getRealRarity($djInfo);
				if ($xzlx == 2) {
					$list[] = array('id'=>$id,'iid'=>$iid,'jf'=>$xhjf,'sl'=>$dhsl,'jwid'=>$dhsx,'jw'=>$city_var_lang['jwmc'][$dhsx], 'pj'=>$pj);
				} elseif ($xzlx == 1) {
					$list[] = array('id'=>$id,'iid'=>$iid,'jf'=>$xhjf,'sl'=>$dhsl,'dj'=>$dhsx, 'pj'=>$pj);
				} else {
					$list[] = array('id'=>$id,'iid'=>$iid,'jf'=>$xhjf,'sl'=>$dhsl, 'pj'=>$pj);
				}
			}
			$intID = $dhlx = $dhsl = $xhjf = $djid = $xzlx = $dhsx = $mc = $iid = null;
		}
		return array('status'=>0,'list'=>$list);
	}
	
	//积分兑换
	public static function jfdh($id,$playersid) {
		global $mc, $common, $db, $_SGLOBAL, $G_PlayerMgr, $sys_lang, $_g_lang;
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player) {
			return array('status'=>21, 'message'=>$sys_lang[7]);
		}
		$jfData = jfdhsj();
		if (isset($jfData[$id])) {
			$dhInfo = $jfData[$id];
		} else {
			return array('status'=>21, 'message'=>$sys_lang[7]);
		}
		$roleInfo = $player->baseinfo_;
		$dqldInfo = daleiModel::getMyLeitai($playersid);
		$dqjf = $dqldInfo['credits'];
		$dhlx = $dhInfo['dhlx'];
		$xhjf = $dhInfo['xhjf'];
		$xzlx = $dhInfo['xzlx'];
		$dhsl = $dhInfo['dhsl'];
		$dhsx = $dhInfo['dhsx'];
		$djid = $dhInfo['djid'];
		if ($dqjf < $xhjf) {
			return array('status'=>21,'message'=>$_g_lang['dalei']['no_credits']);
		}
		$value ['status'] = 0;
		$value ['zjs'] = $dhsl;
		$value['xhjf'] = $xhjf;
		$value['hqjf'] = $dqldInfo['hqjf'];
		if ($dhlx == 1) {
			$updateRole['prestige'] = $roleInfo['prestige'] + $dhsl;
			$common->updateMemCache(MC.$playersid,$updateRole);
			$common->updatetable('player',$updateRole,"playersid = $playersid");
			$value['swz'] = $updateRole['prestige'];
			$value ['mc'] = $_g_lang['dalei']['sw'];
			$value ['jf'] = $dqjf - $xhjf;
			$updateJf ['credits'] = $value ['jf'];
			$updateJf ['last_cdt_time'] = time();
			$common->updatetable('dalei_2',$updateJf,"playersid = ".$dqldInfo['playersid']);
		} else {
			if ($xzlx == 1) {
				$player_level = $roleInfo['player_level'];
				if ($player_level < $dhsx) {
					return array('status'=>21,'message'=>$_g_lang['dalei']['no_level']);
				}
			} elseif ($xzlx == 2) {
				$mg_level = $roleInfo['mg_level'];
				if ($mg_level < $dhsx) {
					return array('status'=>21,'message'=>$_g_lang['dalei']['no_jw']);
				}
			}
			$addRes = $player->AddItems(array($djid=>$dhsl));
			if ($addRes !== false) {				
				$itemInfo = toolsModel::getItemInfo ( $djid );
				$value ['mc'] = $itemInfo ['Name'];				
				$bagData = $player->GetClientBag();
				$value['list'] = $bagData;
				$value ['jf'] = $dqjf - $xhjf;
				$updateJf ['credits'] = $value ['jf'];
				$updateJf ['last_cdt_time'] = time();
				$common->updatetable('dalei_2',$updateJf,"playersid = ".$dqldInfo['playersid']);
			} else { //背包已满
				$value ['status'] = 1001;
				$value ['message'] = $_g_lang['dalei']['pack_full'];
			}
		}
		return $value;			
	}
}
