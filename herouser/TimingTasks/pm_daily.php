<?php

// 擂台初始化玩家战力也会调用下面的calcPlayerPower方法
if(!isset($include_call)){
	// 三个小时吧,如果跑全服务器数据怎么搞法???
	//set_time_limit(3600*3);
	$time1 = microtime(true);
	date_default_timezone_set('PRC');

	define("ROOT_P", dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR );
	include(ROOT_P . 'config.php');
	require(ROOT_P . 'includes/class_memcacheAdapter.php');
	include(ROOT_P . 'components/hero_city/var.php');
	include(ROOT_P . 'configs' . DIRECTORY_SEPARATOR . 'ConfigLoader.php');
	include(ROOT_P . 'components/hero_tools/var.php');

	$current_time = time();
	mysql_connect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw']);
	mysql_select_db($_SC['dbname']);
	mysql_set_charset('utf8');
}
ConfigLoader::GetConfig($G_Items,'G_Items');

if(!isset($include_call)){
	// 找到当前最后处理时间,将该时间减半小时的时间点作为处理玩家数据的标准,如果没有一条数据则为零
	$lastUpdateTime = 0;
	$findTimeSql = "select max(`chkTime`) `chkTime` from ol_player_pm";
	$result = mysql_query($findTimeSql);
	if($result){
		$row = mysql_fetch_assoc($result);
		if(!is_null($row['chkTime'])){
			$lastUpdateTime = $row['chkTime'];
		}
	}else{
		echo "check last time fail\n";
		exit;
	}

	main($current_time);
}

// 调用入口函数
function main($current_time){
	global $lastUpdateTime;

	callTime("into main");

	// 这里每批更新一百个玩家,防止锁表
	$upPosition = 0;
	$lastPlayersid = 0;

	// 将所有玩家的战力增长值归零
	$cleanIncSql = "update ol_player_pm set `increase`=0 where `increase`<>0";
	mysql_query($cleanIncSql);

	do{
		$findPidSql = "select playersid, mg_level from ol_player where last_update_food >='{$lastUpdateTime}'";
		$findPidSql .= " and playersid > {$lastPlayersid}";
		$findPidSql .= " order by playersid limit 100";

		//echo $findPidSql." ; pos = $upPosition\n";
		$result = mysql_query($findPidSql);
		$pidList = array();
		while($row = mysql_fetch_assoc($result)){
			$pidList[$row['playersid']] = $row['mg_level'];
			$lastPlayersid = $row['playersid'];
		}

		updatePlayerInfo($pidList);
		if($upPosition%10000 == 0){
			callTime("process complete {$upPosition}");
		}
		$upPosition += 100;
	}while(!empty($pidList));
	callTime("end calc player value");

	updatePm();

	recordLog($current_time);
}

// 更新指定的玩家数据到排名表中
function updatePlayerInfo($playersidList){
	if(empty($playersidList))
		return;
	// 更新玩家武将战力并得到该玩家战力评分
	$pidList = array_keys($playersidList);
	$_delete_wj_sql = "delete p,j from ol_playergeneral_pm p, ol_pm_wj j";
	$_delete_wj_sql .= " where p.gid=j.gid and p.playersid in (" . implode(',', $pidList) . ")";

	mysql_query($_delete_wj_sql);
	$p_power = array();
	foreach($playersidList as $pid=>$mg){
		$zl = calcPlayerPower($pid, $mg);
		$p_power[$pid] = array('mc'=>'',
							   'zl'=>$zl,
							   'lv'=>0,
							   'exp'=>0,
							   'lv_last_time'=>0,
							   'jw'=>0,
							   'prestige'=>0,
							   'chkTime'=>0);
	}
	$playersidList = array_keys($playersidList);

	// 或得对应玩家上一次的战力评分
	$getPowerSql = "select * from ol_player_pm";
	$getPowerSql .= " where playersid in (". implode(',', $playersidList) .")";
	$result = mysql_query($getPowerSql);
	while($row = mysql_fetch_assoc($result)){
		$row['old_zl'] = $row['zl'];
		$row['zl'] = $p_power[$row['playersid']]['zl'];
		$p_power[$row['playersid']] = $row;
	}

	// 更新玩家战力到ol_player_pm表
	$upPowerSql = "replace into ol_player_pm(`playersid`, `mc`, `zl`, `increase`, ";
	$upPowerSql .= "`lv`, `exp`, `lv_last_time`, `jw`, `prestige`, `chkTime`) values";
	$insPowerSql = array();
	foreach($p_power as $pid=>$ppower){
		$increase_power = $ppower['zl'] - (isset($ppower['old_zl'])?$ppower['old_zl']:0);
		if(0 != $increase_power){
			$mc = mysql_real_escape_string($ppower['mc']);
			$_mdfValues = "('{$pid}', '{$mc}', '{$ppower['zl']}', '{$increase_power}'";
			$_mdfValues .= ", '{$ppower['lv']}', '{$ppower['exp']}', '{$ppower['lv_last_time']}'";
			$_mdfValues .= ", '{$ppower['jw']}', '{$ppower['prestige']}', '{$ppower['chkTime']}')";
			$insPowerSql[] = $_mdfValues;
		}
	}
	if(!empty($insPowerSql)){
		$upPowerSql .= implode(',', $insPowerSql);
		mysql_query($upPowerSql);
	}

	// 从玩家表中更新对应玩家数据
	$updatePlayer = "replace into ol_player_pm(`playersid`, `mc`, `lv`, `exp`, `lv_last_time`, `jw`, `prestige`, `zl`, `increase`)";
	$updatePlayer .= " select p.`playersid`, `nickname`, `player_level`, `current_experience_value`, `last_update_level`";
	$updatePlayer .= " ,`mg_level`, p.`prestige`, `zl`, `increase` from ol_player p left join ol_player_pm m";
	$updatePlayer .= "  on p.playersid=m.playersid where p.playersid in (". implode(',', $playersidList) .")";
	mysql_query($updatePlayer);
}

// 计算玩家战力值(最强五将之和)并更新该玩家所有武将战力到
// ol_playergeneral_pm
function calcPlayerPower($playersid, $mg){
	global $GLOBALS;
	$jwjc = jwjc($mg);
	// 查找玩家武将
	$findGSql = "select * from ol_playergeneral where `playerid`='$playersid'";
	$result = mysql_query($findGSql);

	$zbIdList = array();
	$wjInfos  = array();
	$replaceList = array();
	$wjScore  = array();
	while($row = mysql_fetch_assoc($result)){
		// 技能和等级评分
 		$levelScore = $row['general_level']*20;
		$jnScore =  ($row['jn1_level'] + $row['jn2_level']) * 43;
		$jnScore = $jnScore==0?43:$jnScore;
		
		// 基本属性
		$wjbronAttr = $row['general_level'] * ($row['understanding_value'] - $row['llcs']);
		$wjCombinAttr = $row['general_level'] * ($row['professional_level'] - 1) * 5;
		$wjtfAttr = ceil($row['general_level'] * ($row['llcs'] * 0.5));
		$pyAttr = $row['py_gj'] + $row['py_fy'] + $row['py_tl'] + $row['py_mj'];

		// 天赋和爵位评分
		$tfScore = ceil(80 + $wjbronAttr + $wjCombinAttr + $wjtfAttr + $pyAttr - $levelScore);
		$tfScore = $tfScore<0?0:$tfScore;
		$jwScore = ceil((80 + $wjbronAttr + $wjCombinAttr + $wjtfAttr) * ($jwjc / 100));

		$total = $levelScore + $jnScore + $tfScore;
		$wjScore[$row['intID']] = array('wj'=>$total,
										'level'=>$levelScore,
										'jn'=>$jnScore,
										'tf'=>$tfScore,
										'jw'=>$jwScore);

		// 求武将稀有度和星级
		$g_xyd = ceil(($row['understanding_value'] - $row['llcs'] -15)/5) + 1;
		$g_xj  = ($row['understanding_value'] - $row['llcs'] -15)%5;
		$g_xj  = $g_xj==0?5:$g_xj;
		$replaceList[] = array($playersid,
							   $row['intID'],
							   $total,
							   $row['avatar'],
							   $row['general_name'],
							   $row['general_level'],
							   $g_xyd,
							   $g_xj);
		// 记录该装备数据
		if($row['helmet'] != 0)
			$zbIdList[$row['intID']][] = $row['helmet'];
		if($row['carapace'] != 0)
			$zbIdList[$row['intID']][] = $row['carapace'];
		if($row['arms'] != 0)
			$zbIdList[$row['intID']][] = $row['arms'];
		if($row['shoes'] != 0)
			$zbIdList[$row['intID']][] = $row['shoes'];
	}
	
	// 更新武将排名数据
	$upWjSql = "replace into ol_playergeneral_pm values";
	if(!empty($replaceList)){
		foreach($replaceList as $wj_pm){
			$upWjSql .= "('" . implode("','", $wj_pm) . "'),";
		}
		$upWjSql = trim($upWjSql, ',');
		mysql_query($upWjSql);
	}

	// 找出最强五将ID和这些将使用的装备ID
	$_tmp_wjpf = array();
	foreach($wjScore as $gid=>$pf){
		$_tmp_wjpf[$gid] = $pf['wj'];
	}
	arsort($_tmp_wjpf);
	$_5_wjpf = array();
	$calc_zb_ids = array();
	foreach($_tmp_wjpf as $gid=>$pf){
		$_5_wjpf[$gid] = $wjScore[$gid];
		if(isset($zbIdList[$gid])){
			foreach($zbIdList[$gid] as $djid){
				$calc_zb_ids[] = $djid;
			}
		}
		if(count($_5_wjpf) >= 5){
			break;
		}
	}

	// 计算五将装备加成
	$zbScore = 0;
	if(!empty($calc_zb_ids)){
		$getZbSql = "select ItemID, QhLevel from ol_player_items where ID in (".implode(',', $calc_zb_ids) . ")";
		$result = mysql_query($getZbSql);
		$equipReinforceRule = getEquipReinforceRule();
		// 专属武器和圣诞套装 新年套装
		$zs_except_array = array(49000=>1, 49001=>1, 49002=>1, 49003=>1, 49004=>1, 41001=>1, 42001=>1, 43001=>1, 44001=>1, 41002=>1, 42002=>1, 43002=>1, 44002=>1);
		while($row = mysql_fetch_assoc($result)){
			$itemBaseInfo = $GLOBALS['G_Items'][$row['ItemID']];

			$qhValue = $equipReinforceRule[$row['QhLevel']]['effect'];
			$except_array = array(3=>10, 6=>20, 9=>30, 12=>40, 15=>50, 18=>60, 21=>70);
			$show_raw_value = false;
			if(array_key_exists($row['QhLevel'], $except_array)) {
				if($except_array[$row['QhLevel']] == $itemBaseInfo['LevelLimit'])
					$show_raw_value = true;
			}

			if(array_key_exists($row['ItemID'], $zs_except_array)) $show_raw_value = true;

			$Physical_value = $Defense_value = $Attack_value = $Agility_value = 0;
			if($itemBaseInfo['EquipType'] == 1){
				$Physical_value = $show_raw_value ? intval($itemBaseInfo['Physical_value']) : $qhValue;
			}else if($itemBaseInfo['EquipType'] == 2){
				$Defense_value = $show_raw_value ? intval($itemBaseInfo['Defense_value']) : $qhValue;
			}else if($itemBaseInfo['EquipType'] == 3){
				$Attack_value = $show_raw_value ? intval($itemBaseInfo['Attack_value']) : $qhValue;
			}else if($itemBaseInfo['EquipType'] == 4){
				$Agility_value  = $show_raw_value ? intval($itemBaseInfo['Agility_value']) : $qhValue;
			}
			$zbScore += $Physical_value + $Defense_value + $Attack_value + $Agility_value;
		}
	}

	// 计算玩家五虎将战力
	$player_power = $zbScore;
	foreach($_5_wjpf as $wjpf){
		$player_power += $wjpf['wj'];
		$player_power += $wjpf['jw'];
	}
	return $player_power;
}

// 更新所有排名
function updatePm(){
	global $MemcacheList, $Memport;
	callTime("start sort PM");

	$mc = new MemcacheAdapter_Memcached;
	$mc->addServer($MemcacheList[0], $Memport);

	// 导入数据到排序表
	mysql_query("Truncate ol_pm_zl");
	mysql_query("ALTER TABLE ol_pm_zl AUTO_INCREMENT = 1");
	$inserTmpSql = "insert into ol_pm_zl(`playersid`)";
	$inserTmpSql .= " select playersid from ol_player_pm";
	$inserTmpSql .= " order by `zl` desc, playersid";
	mysql_query($inserTmpSql);
	$mc->delete(MC."pm_top100_1");
	callTime("end insert zl table");

	mysql_query("Truncate ol_pm_wj");
	mysql_query("ALTER TABLE ol_pm_wj AUTO_INCREMENT = 1");
	$inserTmpSql = "insert into ol_pm_wj(`playersid`, `gid`)";
	$inserTmpSql .= " select playersid, `gid` from ol_playergeneral_pm";
	$inserTmpSql .= " order by `zl` desc, playersid";
	mysql_query($inserTmpSql);
	$mc->delete(MC."pm_top100_2");
	callTime("end insert wj table");
	
	mysql_query("Truncate ol_pm_plevel");
	mysql_query("ALTER TABLE ol_pm_plevel AUTO_INCREMENT = 1");
	$inserTmpSql = "insert into ol_pm_plevel(`playersid`)";
	$inserTmpSql .= " select playersid from ol_player_pm";
	$inserTmpSql .= " order by `lv` desc, `exp` desc, `lv_last_time` desc, playersid";
	mysql_query($inserTmpSql);
	$mc->delete(MC."pm_top100_3");
	callTime("end insert plevel table");

	mysql_query("Truncate ol_pm_jw");
	mysql_query("ALTER TABLE ol_pm_jw AUTO_INCREMENT = 1");
	$inserTmpSql = "insert into ol_pm_jw(`playersid`)";
	$inserTmpSql .= " select playersid from ol_player_pm";
	$inserTmpSql .= " order by `jw` desc, prestige desc, playersid";
	mysql_query($inserTmpSql);
	$mc->delete(MC."pm_top100_4");
	callTime("end insert jw table");

	mysql_query("Truncate ol_pm_inc");
	mysql_query("ALTER TABLE ol_pm_inc AUTO_INCREMENT = 1");
	$inserTmpSql = "insert into ol_pm_inc(`playersid`)";
	$inserTmpSql .= " select playersid from ol_player_pm";
	$inserTmpSql .= " order by increase desc, playersid";
	mysql_query($inserTmpSql);
	$mc->delete(MC."pm_top100_5");
	callTime("end insert inc table");
}

// 记录所有TOP100日志
function recordLog($updateTime){
	global $_LOG_INFO,$current_time;

	callTime("write log");
	$topZlSql = "select pm.playersid, zl, z.pm from ol_player_pm pm, ol_pm_zl z";
	$topZlSql .= " where pm.playersid=z.playersid order by zl desc limit 100";

	$topIncSql = "select pm.playersid, increase, i.pm from ol_player_pm pm, ol_pm_inc i";
	$topIncSql .= " where pm.playersid=i.playersid order by increase desc limit 100";

	$topLvSql = "select pm.playersid, lv, exp, lv_last_time, l.pm from ol_player_pm pm, ol_pm_plevel l";
	$topLvSql .= " where pm.playersid=l.playersid order by lv desc, exp desc, lv_last_time desc limit 100";

	$topJwSql = "select pm.playersid, jw, prestige, j.pm from ol_player_pm pm, ol_pm_jw j";
	$topJwSql .= " where pm.playersid=j.playersid order by jw desc, prestige desc limit 100";

	$topWjSql = "select pm.playersid, pm.gid, pm.zl, w.pm from ol_playergeneral_pm pm, ol_pm_wj w";
	$topWjSql .= " where pm.gid=w.gid order by w.pm limit 100";
	
	$topSqlArr = array('Power'=>$topZlSql,
					   'Incease'=>$topIncSql,
					   'Level'=>$topLvSql,
					   'Jw'=>$topJwSql,
					   'General'=>$topWjSql);

	$log_path = $_LOG_INFO['path'] . $_LOG_INFO['prefix'] . 'ranking_' . date('Y_m_d_H_i_s', $updateTime);
	$logHandle = fopen($log_path, 'a');
	foreach($topSqlArr as $topName=>$topSql){
		fwrite($logHandle, "\n\t{$topName} TOP 100\n");
		$result = mysql_query($topSql);
		while($row = mysql_fetch_assoc($result)){
			$_tmp_log = '';
			foreach($row as $key=>$value){
				$_tmp_log .= "{$key}:{$value}\t";
			}
			$_tmp_log .= "\n";
			fwrite($logHandle, $_tmp_log);
		}
		fwrite($logHandle, "\n");
	}

	fwrite($logHandle, "\n\t start:{$current_time}; end:".time()."\n");
	fclose($logHandle);

	// 更新操作时间
	$updateTimeSql = "update ol_player_pm set chkTime='{$updateTime}' limit 1";
	mysql_query($updateTimeSql);
	
	callTime("end TOP 100 log");
}

function callTime($name){
	$micT = microtime(true);
	//echo "call time {$name} {$micT}\n";
}