<?php
define("ENEMY_MAX_COUNT", 20);
define("COMPLETE_REQUEST_LEVEL", 12);

// 社交送礼相关道具信息
function get_social_tools(){
	global $_g_lang;
	return $social_tools = array(1=>array('id'=>10036, 'mc'=>$_g_lang['social']['juanbu'], 'iid'=>'Ico013'),
					2=>array('id'=>10032, 'mc'=>$_g_lang['social']['shicai'], 'iid'=>'Ico009'),
					3=>array('id'=>10035, 'mc'=>$_g_lang['social']['tiekuang'], 'iid'=>'Ico012'),
					4=>array('id'=>10034, 'mc'=>$_g_lang['social']['taotu'], 'iid'=>'Ico011'),
					5=>array('id'=>10033, 'mc'=>$_g_lang['social']['mucai'], 'iid'=>'Ico010'),
					6=>array('id'=>20018, 'mc'=>$_g_lang['social']['shicai_shuipian'], 'iid'=>'Ico018'),
					7=>array('id'=>20019, 'mc'=>$_g_lang['social']['mucai_shuipian'], 'iid'=>'Ico018'),
					8=>array('id'=>20020, 'mc'=>$_g_lang['social']['taotu_shuipian'], 'iid'=>'Ico018'),
					9=>array('id'=>20021, 'mc'=>$_g_lang['social']['tiekuang_shuipian'], 'iid'=>'Ico018'),
					10=>array('id'=>20022, 'mc'=>$_g_lang['social']['juanbu_shuipian'], 'iid'=>'Ico018'));
}

class socialModel {
	
	/**
	 * 检查用户是否向指定用户发送过某种信件
	 *
	 * @param int $playersid			检查玩家
	 * @param int $toplayersid			指定玩家
	 * @param int $type					现在所有信件都为1
	 * @param int $genre				信件类型
	 * @return int						0发过， 1未发
	 */
	static function is_oneDayCount($playersid,$toplayersid,$type,$genre) {
		global $common,$db,$_SGLOBAL;
		$dataInfo = date("Y-m-d H:i:s",$_SGLOBAL['timestamp']);
		jtsjc($dataInfo);
		$sql = "SELECT * FROM ".$common->tname('letters');
		$sql .= " WHERE fromplayersid = '{$playersid}' and playersid = '{$toplayersid}' and create_time >= '{$dataInfo[0]}'";
		$sql .= " and type = ".$type." and genre = ".$genre."  LIMIT 1";
		$result = $db->query($sql);
		$rows = $db->fetch_array($result);
		if ($rows['id'] > 0) {
			return 0;
		}else{
			return 1;
		}
	}
	
	//今天是否能能邀请
	static function is_TodayUCinvite($playersid,$toplayersid) {
		global $common,$db;
		$date_now=date("Y-m-d H:i:s",time());
		$year=((int)substr($date_now,0,4));//取得年份

		$month=((int)substr($date_now,5,2));//取得月份

		$day=((int)substr($date_now,8,2));//取得几号

		$date_now1 = mktime(0,0,0,$month,$day,$year);
		$date_now2 = mktime(23,59,59,$month,$day,$year);
		
		$result = $db->query("SELECT * FROM ".$common->tname('letters')." WHERE fromplayersid = '".$playersid."' and playersid = '".$toplayersid."' and create_time >= '".$date_now1."' and create_time <= '".$date_now2."' and type = 2 and genre = 2  LIMIT 1");
		$rows = $db->fetch_array($result);
		if ($rows['id'] > 0) {
			return 0;
		}else{
			return 1;
		}
	}
	
	//今天是否能送礼
	static function is_TodayUCgift($playersid,$toplayersid) {
		global $common,$db;
		$date_now=date("Y-m-d H:i:s",time());
		$year=((int)substr($date_now,0,4));//取得年份

		$month=((int)substr($date_now,5,2));//取得月份

		$day=((int)substr($date_now,8,2));//取得几号

		$date_now1 = mktime(0,0,0,$month,$day,$year);
		$date_now2 = mktime(23,59,59,$month,$day,$year);
		
		$result = $db->query("SELECT * FROM ".$common->tname('letters')." WHERE fromplayersid = '".$playersid."' and playersid = '".$toplayersid."' and create_time >= '".$date_now1."' and create_time <= '".$date_now2."' and type = 2 and genre = 3  LIMIT 1");
		//echo("SELECT * FROM ".$common->tname('letters')." WHERE fromplayersid = '".$playersid."' and playersid = '".$toplayersid."' and create_time >= '".$date_now1."' and create_time <= '".$date_now2."' and type = 2 and genre = 2  LIMIT 1");
		$rows = $db->fetch_array($result);
		if ($rows['id'] > 0) {
			return 0;
		}else{
			return 1;
		}
	}
	
	//uc好友列表
	/*
	static function getUcFriendlist($socialInfo) {
		global $common,$db;
		$value = array();
		$result = $db->query("SELECT ucid FROM ".$common->tname('player')." WHERE playersid = '".$socialInfo['playersid']."' LIMIT 1");
		$rowsucid = $db->fetch_array($result);
		
		//同势力的uc好友默认到好友列表中
		$result = $db->query("SELECT boxOpen FROM ".$common->tname('player')." WHERE playersid = ".$socialInfo['playersid']." LIMIT 1");
		$rows_box = $db->fetch_array($result);
		$selectItem = 'userid as yhid,ucid,playersid as jsid,nickname as mc,player_level as dj,regionid as sl,vip,vip_end_time as vt,prestige as dw,prestige_value as swz,coins as tq,silver as yl,boxOpen as box,bank as qz,resource1_level as zy1,resource2_level as zy2,resource3_level as zy3';
		$result = $db->query("SELECT ".$selectItem." FROM ".$common->tname('player')." WHERE playersid <> '".$socialInfo['playersid']."' and ucid in (select fucid from ".$common->tname('uc_friends')." where ucid = '".$_SESSION['ucid']."') and playersid not in (select playersid from ".$common->tname('social_user')." where parent_playersid = '".$socialInfo['playersid']."') and regionid = ".$_SESSION['regionid']." order by player_level desc LIMIT 200");
		//echo "SELECT ".$selectItem." FROM ".$common->tname('player')." WHERE playersid <> '".$socialInfo['playersid']."' and ucid in (select fucid from ".$common->tname('uc_friends')." where ucid = '".$_SESSION['ucid']."') and regionid = ".$_SESSION['regionid']." order by player_level desc LIMIT 200";
		while ($rows = $db->fetch_array($result)) {
			//$temp_dir = explode("|",socialModel::getWentao($rows['wt']));
  			//$rows['wt'] = $temp_dir[0];
  			$rows['yhid'] = (int)$rows['yhid'];
  			$rows['ucid'] = (int)$rows['ucid'];
  			$rows['jsid'] = (int)$rows['jsid'];
  			$rows['dj'] = (int)$rows['dj'];
  			$rows['sl'] = (int)$rows['sl'];
  			$rows['tq'] = (int)$rows['tq'];
  			$rows['yp'] = (int)$rows['yp'];
  			$rows['qz'] = (int)$rows['qz'];
  			$rows['zy1'] = (int)$rows['zy1'];
  			$rows['zy2'] = (int)$rows['zy2'];
  			$rows['zy3'] = (int)$rows['zy3'];
  			$rows['ftype'] = socialModel::is_UcFriend($rows['ucid']);
  			$value[] = $rows;
		}
		
		//不在游戏当中的uc好友
		$ufInfo = array();
		$ufInfo['playersid'] = $socialInfo['playersid'];
		$ufInfo['ucid'] = $_SESSION['ucid'];
		$ucfriendsInfo = roleModel::getRoleUcFriendsInfo($ufInfo);
		if($ucfriendsInfo == '') {
			return '';
		}
		//exit;
		for($i = 0; $i<count($ucfriendsInfo); $i++) {
			if(socialModel::is_UcGameFriend($ucfriendsInfo[$i]['fucid']) == 4) {
				continue;
			}
			$rows['yhid'] = 0;
			$rows['ucid'] = 0;
			$rows['uid'] = (int)$ucfriendsInfo[$i]['fucid'];
			$rows['mc'] = $ucfriendsInfo[$i]['realname'];
			$rows['dj'] = 1;
			$rows['sl'] = 0;
			$rows['vip'] = 0;
			$rows['vt'] = 0;
  			$rows['dw'] = "";
  			$rows['swz'] = 0;
  			$rows['tq'] = 0;
  			$rows['yp'] = 0;
  			$rows['box'] = 0;
  			$rows['qz'] = 0;
  			$rows['zy1'] = 1;
  			$rows['zy2'] = 1;
  			$rows['zy3'] = 1;
  			$rows['ftype'] = 1;
			$value[] = $rows;
		}
		return $value;

	}
	*/
	
	/**
	 * 更新好友的数据列表
	 * @author kknd li
	 *
	 * @param int $plaerysid				对应玩家
	 * @param array $updateFriendIDs		新更新的好友id列表
	 */
	static function updateMemCacheFriends($playersid, $updateFriendIDs = null) {
		global $mc,$G_PlayerMgr;
		$fkey = MC.$playersid.'_friends';
		$player = $G_PlayerMgr->GetPlayer($playersid );
		
		// 如果没有需要更新数据就直接删除好友id列
		if(is_null($updateFriendIDs)){
			$mc->delete($fkey);
			return ;
		}
		
		// 有更新的数据时更新
		$mc->set($fkey, $updateFriendIDs, 0, 3600);
		$player->friends_ = $updateFriendIDs;
	}
	
	/**
	 * 获取邻居列表或者可占领列表
	 *
	 * @param int $playersid		玩家id
	 * @param int $type				1邻居列表，2可占领列表
	 * @param int $sx				0不刷新，其他刷新
	 * @param boolean $onlyId		是否只返回用户id，true是只返回，false返回用户信息
	 * @return array				如果$onlyId是false则返回玩家信息，否则返回对应玩家id和等级
	 */
	public static function findRandFriend($playersid, $type=1, $sx=0, $onlyId=false){
		global $common, $db, $mc, $_SGLOBAL, $_g_lang;
		
		$playerIdList = array();
		
		$cacheTime = 3600;

		
		$type = intval($type);
		$memKey = '';
		switch($type){
			case 1:
				$memKey = '_neighbors';
			break;
			case 2:
				$memKey = '_canOccupys';
			break;
			default:
				return array('status'=> 27, 'message'=> $_g_lang['social']['err_req_type']);
			break;
		}
		//$memKey = MC.$playersid.$memKey;
		
		// 检查是否有缓存数据，如果有直接读取
		$playerIdList = array();
		if(0 == $sx ){
			$playerIdList = $mc->get(MC.$playersid.$memKey);
		}
		$roleInfo['playersid'] = $playersid;
		if(!roleModel::getRoleInfo($roleInfo)){
			$returnValue['status']  = 26;
			$returnValue['message'] = $_g_lang['social']['err_pinfo'];
			return $returnValue;
		}
		/*$xwzt_31 = substr($roleInfo['xwzt'],30,1);
		if ($xwzt_31 == 0) {
			$updateRole['xwzt'] = substr_replace($roleInfo['xwzt'],'1',30,1);
			$updateRoleWhere['playersid'] = $playersid;
			$common->updatetable('player',$updateRole,$updateRoleWhere);
			$common->updateMemCache(MC.$playersid,$updateRole);
		}*/
		// 如果没有读取缓存数据就再生成对应数据，查两次一次比自己高，一次比自己低
		if(empty($playerIdList)){
			

			$player_level = $roleInfo['player_level'];
			// 根据不同列表得到要过滤的用户id,1屏蔽好友id，2屏蔽已占领id，好友id，仇人id
			$maskIDList = array_keys(roleModel::getTableRoleFriendsInfo($playersid, 1, true));
			$maskIDList[] = $playersid;
			if(2 == $type){
				$maskIDList = array_merge($maskIDList, array_keys(roleModel::getTableRoleFriendsInfo($playersid, 4, true)));
				$maskIDList = array_merge($maskIDList, array_keys(roleModel::getTableRoleFriendsInfo($playersid, 3, true)));
			}
			
			// 查询对应类型的可查询玩家列表
			$allPlayerIdList = $mc->get(MC.$memKey.'_buffer_'.$player_level);
			if(empty($allPlayerIdList)){
				// 构建条件语句
				//$whereStr = " is_reason<>1";				
				//$whereStr .= " and playersid not in ({$maskStr})";
				// 1为封禁
				$whereStr1 = ' is_reason<>1';
				$whereStr2 = ' is_reason<>1';
				
				if(2 == $type){
					// 可占列表是在5级以内并且等级大于10级
					$level_diff = 5;
					$highPosition = $player_level + $level_diff;
					$lowPosition  = $player_level - $level_diff;
					$player_level_one = $player_level +1;
					//$whereStr .= " and player_level between {$lowPosition} and {$highPosition} and player_level>=5";
					$whereStr1 .= " and player_level between {$lowPosition} and {$player_level} and player_level>5";
					$whereStr2 .= " and player_level between {$player_level_one} and {$highPosition} and player_level>5";
				}
				elseif(1 == $type){
					$chktime = time() - 36*3600;
					$player_level_one = $player_level +1;
					$whereStr1 .= " and player_level <= {$player_level} and last_update_food>'{$chktime}'";
					$whereStr2 .= " and player_level > {$player_level} and last_update_food>'{$chktime}'";
				}
				
				// 优先取得与玩家等级接近并且未封禁的25个id
				$sltSql = "(select playersid, player_level from " . $common->tname('player');
				$sltSql .= " where".$whereStr1;
				$sltSql .= " order by player_level desc limit 200)\n";
				$sltSql .= " union all \n";
				$sltSql .= "(select playersid, player_level from " . $common->tname('player');
				$sltSql .= " where".$whereStr2;
				$sltSql .= " order by player_level limit 200)";
				
				$result = $db->query($sltSql);
				$allPlayerIdList = array();
				while($row = $db->fetch_array($result)){
					$allPlayerIdList[$row['playersid']] = array('pid'=>$row['playersid'], 'level'=>$row['player_level']);
				}
				
				$bufferTime = $player_level * 15 + 100;
				$mc->set(MC.$memKey.'_buffer_'.$player_level, $allPlayerIdList, 0, $bufferTime);
			}
			
			// 过滤不能显示的玩家
			foreach($maskIDList as $pid){
				unset($allPlayerIdList[$pid]);
			}
			
			// 如果是可占列表过滤
			
			// 得到随机玩家列表id
			if(count($allPlayerIdList)>6){
				$randIDKey = array_rand($allPlayerIdList, 6);
				foreach($randIDKey as $id){
					$playerIdList[] = $allPlayerIdList[$id];
				}
			}
			else{
				$playerIdList = $allPlayerIdList;
			}
			
			// 将得到的数据进行缓存
			$mc->set(MC.$playersid.$memKey, $playerIdList, 0, $cacheTime);
		}
		
		// 如果只要返回id就不进行后面的操作
		if($onlyId){
			//根据玩家等级排序
			$friendsLevelList = array();
			foreach($playerIdList as $key=>$friendInfo){
				$friendsLevelList[$key] = $friendInfo['level'];
			}
			array_multisort($friendsLevelList, SORT_DESC, $playerIdList);
			
			$tempIDList = array();
			foreach($playerIdList as $playerid){
				$tempIDList[$playerid['pid']] = $playerid;
			}
			return $tempIDList;
		}
		
		// 过滤掉不需要的等级信息
		$tempIDList = array();
		foreach($playerIdList as $playerid){
			$tempIDList[] = $playerid['pid'];
		}
		$playerIdList = $tempIDList;
		
		// 获得对应玩家id的信息
		$playerInfoList = roleModel::getAllRolesInfo($playerIdList);
		
		//根据玩家等级排序
		$friendsLevelList = array();
		foreach($playerInfoList as $key=>$friendInfo){
			$friendsLevelList[$key] = $friendInfo['player_level'];
		}
		
		array_multisort($friendsLevelList, SORT_DESC, $playerInfoList);
		
		// 格式化嵌套的玩家信息
		$list = array();
		foreach($playerInfoList as $playerInfo){
			$row = array();
			$row['jsid'] = $playerInfo['playersid'];
			$row['mc']   = $playerInfo['nickname'];
			$row['dj']   = $playerInfo['player_level'];
			$row['sex']  = $playerInfo['sex'];
			$row['vip']  = $playerInfo['vip'];
			
			$list[] = $row;
		}
		
		// 格式化返回值并返回
		if(empty($list)){
			$returnValue['status']  = 1001;
			$returnValue['message'] = $type==2?$_g_lang['social']['no_find_enemy']:$_g_lang['social']['no_find_neighbour'];
			$returnValue['list']    = array();
		}
		else{
			$returnValue['status'] = 0;   			
			$returnValue['list']   = $list;
		}
		
		return $returnValue;
	}
	
	public static function filterSy($playersid, $page){
		global $mc;
		$tmpPids = array();
		if($page != 1){
			$tmpPids = $mc->get(MC.$playersid.'_syFriendsPid');
		}
		
		if(empty($tmpPids)){
			$friendIds = roleModel::getTableRoleFriendsInfo($playersid, 1, true);
			$tmpPids = array();
			$userTradeInfo = socialModel::getMemTradeInfo($playersid);
			foreach($friendIds as $pid=>$fInfo){
				if($fInfo['deny']==1){
					continue;
				}
				if($fInfo['level']<10){
					continue;
				}
				if(date('Y-m-d',$friendIds[$pid]['atime']) == date('Y-m-d')){
					continue;
				}
				//socialModel::is_TodaySy($playersid,$friendsInfo[$i]['playersid']);
				$is_sy = true;
				foreach($userTradeInfo as $tradeInfo){
					// 缓存数据中frompid不等于自己的时候只会有别人对自己的送礼
					if($tradeInfo['topid']==$playersid
						&& $tradeInfo['frompid']==$pid
						&&($tradeInfo['type']==1||$tradeInfo['type']==2)){
						$is_sy = false;
						break;
					}
					if($tradeInfo['type']==3
					&& $tradeInfo['topid']==$pid){
						$is_sy = false;
						break;
					}
				}
				if(!$is_sy){
					continue;
				}
				
				$tmpPids[$pid] = $fInfo;
			}
			
			$mc->set(MC.$playersid.'_syFriendsPid', $tmpPids, 0, 3600);
		}
		return $tmpPids;
	}
	
	/**
	 * 获得好友列表所需的格式化后信息
	 *
	 * @param int $playersid			对应玩家的id
	 * @param int $page                 请求的好友列表对应页
	 * @param int $is_show_deny         是否显示封禁好友，0显示，1不显示
	 * @return array
	 */
	static function getFriendInfo($playersid, $page, $is_show_deny=0){
		global $common,$db,$_SGLOBAL,$_g_lang;
		$roleInfo['playersid'] = $playersid;
		if(!roleModel::getRoleInfo($roleInfo)){
			$returnValue['status']  = 26;
			$returnValue['message'] = $_g_lang['social']['err_pinfo'];
			return $returnValue;
		}		
		$isShowFj = $is_show_deny==0?true:false;
		if(!$isShowFj){
			$friendsInfo = socialModel::filterSy($playersid, $page);
		}else{
			$friendsInfo = roleModel::getTableRoleFriendsInfo($playersid, 1, true);
		}
		
		// 分页处理，$pageCount为页显示行数
		$pageCount     = 6;
		$allRowsCount  = count($friendsInfo);
		$totalPage     = ceil($allRowsCount/$pageCount);
		
		$curPage       = $page>1?$page:1;
		$curPage       = $curPage<$totalPage?$curPage:$totalPage;
		$curPage       = $curPage>1?$curPage:1;
		
		$_start  = ($curPage-1)*$pageCount;
		$_end    = $curPage*$pageCount;
		$_end    = $_end>$allRowsCount?$allRowsCount:$_end;
		
		// 过滤非当前页的玩家id
		$friList = array();
		$index = 0;
		foreach($friendsInfo as $fid=>$relation){
			if($index<$_start || $index >= $_end){ 
				$index++;
				continue;
			}
			$friList[$fid] = $relation;
			$index++;
		}
		
		// 获得具体信息并重设好感度
		$tmpFriendsInfo = roleModel::getAllRolesInfo ( array_keys ( $friList ) );
		foreach ( $tmpFriendsInfo as $key => $playerInfo ) {
			$playerInfo ['feel'] = $friList [$playerInfo ['playersid']] ['feel'];
			$tmpFriendsInfo [$key] = $playerInfo;
		}
		
		// 根据好感度和等级排序
		$friendsLevelList = array ();
		$friendsFeelList = array ();
		foreach ( $tmpFriendsInfo as $key => $friendId ) {
			$friendsLevelList [$key] = $friendId ['player_level'];
			$friendsFeelList [$key] = $friendId ['feel'];
		}
		array_multisort ( $friendsFeelList, SORT_DESC, $friendsLevelList, SORT_DESC, $tmpFriendsInfo );
		$friendsInfo = array_values ( $tmpFriendsInfo );
		
		// 格式化输出内容
		$value = array();
		for($i = 0; $i<count($friendsInfo); $i++) {
  			$rows['jsid'] = (int)$friendsInfo[$i]['playersid'];
  			$rows['mc'] = $friendsInfo[$i]['nickname'];
  			$rows['dj'] = (int)$friendsInfo[$i]['player_level'];
  			$rows['vip'] = (int)$friendsInfo[$i]['vip'];
			// 好感度
			$rows['feel'] = intval($friendsInfo[$i]['feel']);
			
			if($isShowFj){
				// 可否开宝箱
	  			$rows['box'] = socialModel::is_Box($playersid,$friendsInfo[$i]['playersid'],$roleInfo['boxOpen'],$roleInfo['boxTime']);
				
	  			// 是否需要救
	  			$occupyID = $friendsInfo[$i]['is_defend']==1?$friendsInfo[$i]['playersid']:$friendsInfo[$i]['aggressor_playersid'];
	  			if($occupyID == $friendsInfo[$i]['aggressor_playersid']
	  			&& $occupyID!=$playersid 
				&& $friendsInfo[$i]['end_defend_time'] > $_SGLOBAL['timestamp']) {
					$rows['jiu'] = 1;
				}
				$rows['is_sy'] = 0;
			}else{
				// 是否索要
	  			$rows['is_sy'] = socialModel::is_TodaySy($playersid,$friendsInfo[$i]['playersid']);
	  			$rows['box']   = 0;
			}
  			
  			$value[] = $rows;
  			unset($rows);
		}
		
		// 任务相关调用
		$roleInfo['playersid'] = $playersid;
		if (!empty($friendsInfo) && $curPage == 1) {
			$res = roleModel::getRoleInfo($roleInfo);
			if ($res == true) {
				$roleInfo['rwsl'] = $allRowsCount;  //任务所需数据
			  	$rwid = questsController::OnFinish ( $roleInfo, "'hysl'" );
				if (! empty ( $rwid )) {
					$returnValue ['rwid'] = $rwid;
				} 
			}
		}
		
        /*if ($allRowsCount > 0) {
       	     $ydts = guideScript::jsydsj($roleInfo,'ydkbx',8);
       	     if (!empty($ydts)) {
       	     	$returnValue['ydts'] = $ydts; 
       	     }
        }*/

		// 返回成功状态
		$returnValue['status'] = 0;
		$returnValue['list'] = $value;
		$returnValue['curPage'] = $curPage;
		$returnValue['totalPage'] = $totalPage;
		
		return $returnValue;
	}
	
	/**
	 * 今天是否能开宝箱
	 *
	 * @param int $playersid			检测玩家id
	 * @param int $tuid					目标玩家id
	 * @param string $box				,分割的玩家id，表示已开箱pid
	 * @param int $boxtime				最后开的宝箱时间
	 * @return int						0 不能开宝箱，1可开宝箱
	 */
	static function is_Box($playersid, $tuid, $box, $boxtime) {
		global $common,$db,$_SGLOBAL,$mc;
		//检查最后开宝箱时间是否过期，如果过期更新检测玩家时间
		if(date("Y-m-d",$boxtime) <> date("Y-m-d",$_SGLOBAL['timestamp'])) {
			$update['boxOpen'] = '';
			$update['boxTime'] = $_SGLOBAL['timestamp'];
			$wherearr['playersid'] = $playersid;
			$common->updatetable('player', $update, $wherearr);
			
			//修改内存
			$memory_Resource = $mc->get(MC.$playersid);
			if($memory_Resource <> "") {
				$updateMem['boxOpen'] = '';
				$updateMem['boxTime'] = $_SGLOBAL['timestamp'];
				$common->updateMemCache(MC.$playersid,$updateMem);
			}
		}
		
		// 检查玩家是否在列表中
		$temp_dir = explode(",",$box);
		
		$friendList = array_keys(roleModel::getTableRoleFriendsInfo($playersid, 1, true));
		
		if (in_array($tuid,$temp_dir)
		|| !in_array($tuid, $friendList)) {
  			return 0;
  		}else{
  			return 1;
  		}
	}
	
	//文韬
	static function getWentao($military_expertise_level) {
		if($military_expertise_level == "1") {
  			return "儒生|0|0|";
  		}elseif($military_expertise_level == "2") {
  			return "秀才|0.06|0.01|";
  		}elseif($military_expertise_level == "3") {
  			return "案首|0.12|0.02|";
  		}elseif($military_expertise_level == "4") {
  			return "举人|0.18|0.03|";
  		}elseif($military_expertise_level == "5") {
 			return "解元|0.24|0.04|";
 		}elseif($military_expertise_level == "6") {
 			return "贡士|0.3|0.05|";
  		}elseif($military_expertise_level == "7") {
  			return "会元|0.36|0.06|";
  		}elseif($military_expertise_level == "8") {
  			return "进士|0.42|0.07|";
  		}elseif($military_expertise_level == "9") {
  			return "探花|0.48|0.08|";
  		}elseif($military_expertise_level == "10") {
  			return "榜眼|0.54|0.09|";
  		}else{
  			return "状元|0.6|0.1|";
  		}
	}
	
	//地位
	static function getDiwei($value) {			//名称|最小声望|征税加成|天赋上线|历练积分
		if($value == "1") {
			return "平民|0-20|0|17|1";
		}elseif($value == "2") {
			return "士卒|20-60|0.02|19|3";
		}elseif($value == "3") {
			return "伍长|60-120|0.06|21|5";
		}elseif($value == "4") {
			return "什长|120-200|0.12|23|7";
		}elseif($value == "5") {
			return "百夫长|200-300|0.2|25|9";
		}elseif($value == "6") {
			return "小都统|300-420|0.3|27|11";
		}elseif($value == "7") {
			return "大都统|420-560|0.42|29|13";
		}elseif($value == "8") {
			return "偏将|560-720|0.56|31|15";
		}elseif($value == "9") {
			return "正将|720-900|0.72|33|17";
		}elseif($value == "10") {
			return "偏牙将|900-1100|0.9|35|19";
		}elseif($value == "11") {
			return "牙将|1100-1320|1.1|37|21";
		}elseif($value == "12") {
			return "副将军|1320-1560|1.32|39|23";
		}elseif($value == "13") {
			return "将军|1560-1820|1.56|41|25";
		}elseif($value == "14") {
			return "大将军|1820-2100|1.82|43|27";
		}elseif($value == "15") {
			return "上将军|2100-10000|2.1|50|29";
		}
	}
	
	//天赋
	static function getTianfu($understanding_level) {		//参加历练人数|花费银两|需要得到的历练积分|目标地位
		if($understanding_level <= "15") {
			return "1|2|1|1";
		}elseif($understanding_level <= "17") {
			return "1|4|3|2";
		}elseif($understanding_level <= "19") {
			return "2|6|6|2";
		}elseif($understanding_level <= "21") {
			return "2|8|10|3";
		}elseif($understanding_level <= "23") {
			return "3|10|15|3";
		}elseif($understanding_level <= "25") {
			return "3|12|21|4";
		}elseif($understanding_level <= "27") {
			return "4|14|28|4";
		}elseif($understanding_level <= "29") {
			return "4|16|36|5";
		}elseif($understanding_level <= "31") {
			return "5|18|55|6";
		}elseif($understanding_level <= "33") {
			return "5|20|65|7";
		}elseif($understanding_level <= "35") {
			return "5|22|75|8";
		}elseif($understanding_level <= "37") {
			return "5|24|85|9";
		}elseif($understanding_level <= "39") {
			return "5|26|95|10";
		}elseif($understanding_level <= "50") {
			return "5|28|125|13";
		}
	}
	
	//武略
	static function getWulue($militaries_strategies_level) {
		if($militaries_strategies_level == "1") {
			return "布衣|0|0|0|1|1";
		}elseif($militaries_strategies_level == "2") {
			return "从九品|0|0|2|3|2";
		}elseif($militaries_strategies_level == "3") {
			return "正九品|0.06|0.03|4|5|3";
		}elseif($militaries_strategies_level == "4") {
			return "从八品|0.12|0.06|6|7|4";
		}elseif($militaries_strategies_level == "5") {
			return "正八品|0.18|0.09|8|9|5";
		}elseif($militaries_strategies_level == "6") {
			return "从七品|0.24|0.12|10|11|6";
		}elseif($militaries_strategies_level == "7") {
			return "正七品|0.3|0.15|12|13|7";
		}elseif($militaries_strategies_level == "8") {
			return "从六品|0.36|0.18|14|15|8";
		}elseif($militaries_strategies_level == "9") {
			return "正六品|0.42|0.21|16|17|9";
		}elseif($militaries_strategies_level == "10") {
			return "从五品|0.48|0.24|18|19|10";
		}elseif($militaries_strategies_level == "11") {
			return "正五品|0.54|0.27|20|21|11";
		}elseif($militaries_strategies_level == "12") {
			return "从四品|0.6|0.3|22|23|12";
		}elseif($militaries_strategies_level == "13") {
			return "正四品|0.66|0.33|24|25|13";
		}elseif($militaries_strategies_level == "14") {
			return "从三品|0.72|0.36|26|27|14";
		}elseif($militaries_strategies_level == "15") {
			return "正三品|0.78|0.39|28|29|15";
		}elseif($militaries_strategies_level == "16") {
			return "从二品|0.84|0.42|30|31|16";
		}elseif($militaries_strategies_level == "17") {
			return "正二品|0.9|0.45|32|33|17";
		}elseif($militaries_strategies_level == "18") {
			return "从一品|0.96|0.48|34|35|18";
		}else{
			return "正一品|1.02|0.51|36|37|19";
		}
	}
	
	/**
	 * 添加双方好友社交关系
	 *
	 * @param int $playersid			邀请响应方
	 * @param int $toplayersid			邀请发起方
	 * @return boolean
	 */
	public static function addFriendSocial($playersid, $toplayersid){
		global $common, $db, $mc;
		// 检查是否已经存在社交关系
		$selfFriendIDs = roleModel::getTableRoleFriendsInfo($playersid, 1, true);
		if(key_exists($toplayersid, $selfFriendIDs)){
			return false;
		}
		
		// 更新请求方数据
		$social['parent_playersid'] = $playersid;
    	$social['type'] = '1';
    	$social['playersid'] = $toplayersid;
    	$social['add_time'] = time();
    	$common->inserttable('social_user',$social);
    	
    	// 更新发起方数据
		$social['parent_playersid'] = $toplayersid;
  		$social['type'] = '1';
  		$social['playersid'] = $playersid;
  		$social['add_time'] = time();
  		$common->inserttable('social_user',$social);
  		
  		//获取自己的好友id数据并添加新好友id
  		//$selfFriendIDs = roleModel::getTableRoleFriendsInfo($playersid, 1, true);
  		
		$otherInfo = array('playersid'=>$toplayersid);
		$roleRes = roleModel::getRoleInfo($otherInfo);
		$level = $roleRes?$otherInfo['player_level']:0;
		$deny  = $roleRes?$otherInfo['is_reason']:0;
  		$selfFriendIDs[$toplayersid] = array('feel'=>0, 'atime'=>time(), 'deny'=>$deny, 'level'=>$level);
  		
		// 更新双方好友缓存
		socialModel::updateMemCacheFriends($playersid, $selfFriendIDs);
		socialModel::updateMemCacheFriends($toplayersid);
		
		// 从响应方邻居列表中删除对方的数据
		$resNeighbors = $mc->get(MC.$playersid.'_neighbors');
		if(is_array($resNeighbors)){
			foreach($resNeighbors as $key=>$cityID){
				if($cityID['pid'] == $toplayersid){
					unset($resNeighbors[$key]);
					$mc->set(MC.$playersid.'_neighbors', $resNeighbors, 0, 3000);
					break;
				}
			}
		}
		
		// 从发起方邻居列表中删除对方的数据
		$reqNeighbors = $mc->get(MC.$toplayersid.'_neighbors');
		if(is_array($reqNeighbors)){
			foreach($reqNeighbors as $key=>$cityID){
				if($cityID['pid'] == $playersid){
					unset($reqNeighbors[$key]);
					$mc->set(MC.$toplayersid.'_neighbors', $reqNeighbors, 0, 3000);
					break;
				}
			}
		}
		
		// zzz的接口要更新的可历练的列表
		$mc->delete(MC.$playersid.'_friendNotRepeat');
		$mc->delete(MC.$toplayersid.'_friendNotRepeat');
		
		return true;
	}
	
	/**
	 * 删除好友
	 * 2012-01-18重写
	 * @author kknd li
	 *
	 * @param int $playersid		要删除好友的玩家id
	 * @param int $tuid				从好友列表中删除的id
	 * @param int $page				返回的好友列表页数
	 * @return array
	 */
	static function deleteFriends($playersid, $tuid, $page) {
		global $common,$db,$mc,$_g_lang;
		// 获取最新好友id列表
		$friendsIDList = array_keys(roleModel::getTableRoleFriendsInfo($playersid, 1, true));
		if(!key_exists($tuid, $friendsIDList)){
			$returnValue['status'] = 1110;
			$returnValue['message'] = $_g_lang['social']['y_no_in_fri_list'];
		}
		
		// 删除双方的好友关系
		$delSql = "delete from ".$common->tname('social_user')." where type=1 and ";
		$delSql .= "((parent_playersid={$playersid} and playersid={$tuid}) ";
		$delSql .= "or (parent_playersid={$tuid} and playersid={$playersid})) limit 2";
		$db->query($delSql);
		
		// 移除好友后更新好友列表
		$selfFriendIDs = roleModel::getTableRoleFriendsInfo($playersid, 1, true);
		foreach($selfFriendIDs as $key=>$fid){
			if($key == $tuid){
				unset($selfFriendIDs[$key]);
				break;
			}
		}
		
		// 更新双方的memcache值
		socialModel::updateMemCacheFriends($playersid, $selfFriendIDs);
		socialModel::updateMemCacheFriends($tuid);
		
		// 获得最新好友列表
		$returnValue = socialModel::getFriendInfo($playersid, $page, 1);
		$returnValue['jsid'] = (int)$tuid;

		// zzz的接口要更新的可历练的列表
		$mc->delete(MC.$playersid.'_friendNotRepeat');
		$mc->delete(MC.$tuid.'_friendNotRepeat');
		
		return $returnValue;
	}
	
	/**
	 * 送礼接口
	 *
	 * @param array $socialInfo
	 * @param int $sltype				1主动送礼，2收到索取请求后送礼
	 * @param int $tradeid				为sltype为2准备
	 * @param int $xxlx					为sltype为2准备
	 * @param int $page					为sltype为2准备
	 * @param int $xxid					为sltype为2准备
	 * @return array
	 */
	static function setGift($socialInfo,$sltype=1,$tradeid,$xxlx,$page,$xxid) {
		global $common,$db,$_SGLOBAL,$mc,$_g_lang;
		$social_tools = get_social_tools();
		//$item = array(10036,10032,10035,10034,10033,20018,20019,20020,20021,20022);
		$create_time = $_SGLOBAL['timestamp'];
		
		if (empty($socialInfo['playersid']) || empty($socialInfo['toplayersid'])) {
			$value['status'] = 2;   //非法的客户端请求
			$value['message'] = $_g_lang['social']['err_request'];
			return $value;
		}
		
		// 检测对象是否封禁
		$tuInfo['playersid'] = $socialInfo['toplayersid'];
		if(!roleModel::getRoleInfo($tuInfo)){
			$returnValue['status']  = 25;
			$returnValue['message'] = $_g_lang['social']['no_find_account'];
			return $returnValue;
		}
		if(1==$tuInfo['is_reason']){
			$returnValue['status']  = 21;
			$returnValue['message'] = $_g_lang['social']['account_deny'];
			return $returnValue;
		}
		
		$friendIDList = roleModel::getTableRoleFriendsInfo($socialInfo['playersid'], 1, true);
		
		$selfInfo['playersid'] = $socialInfo['playersid'];
		if(!roleModel::getRoleInfo($selfInfo)){
			$returnValue['status']  = 26;
			$returnValue['message'] = $_g_lang['social']['err_pinfo'];
			return $returnValue;
		}
		if(!(defined('PRESSURE_TEST_DEBUG') && isset($_GET['debug']))){
			// 双方等级十级以上
			if($tuInfo['player_level']<10
			||$selfInfo['player_level']<10){
					$value['status'] = 23;   //非法的客户端请求
					$value['message'] = $_g_lang['social']['err_req_level'];
					return $value;
			}
			
			// 判断是否是好友关系
			if(!key_exists($socialInfo['toplayersid'], $friendIDList)){
				if(2 == $sltype){
					$value        = lettersModel::deleteLetters($xxid,$socialInfo['playersid'],$page,$xxlx);
					$value['status'] = 1003;
				}
				else{
					$value['status']  = 24;
				}
				$value['message'] = $_g_lang['social']['no_friend_to_gift'];
				
				return $value;
			}
		
			// 如果双方关系是当天添加就不能送礼不能索要
			if(date('Y-m-d',$friendIDList[$socialInfo['toplayersid']]['atime']) == date('Y-m-d')){
				$value['status'] = 639;
				$value['message'] = $_g_lang['social']['new_friend_to_gift'];
				return $value;
			}
		
			// 如果已送礼则提示并删除信件
			if(socialModel::is_TodayGift($socialInfo['playersid'],$socialInfo['toplayersid'])!=1) {
				// 今天您已送礼给该用户
				if(2 == $sltype){
					$value = lettersModel::deleteLetters($xxid,$socialInfo['playersid'],$page,$xxlx);
				}
				$value['status'] = 1001;
				return $value;
			}
		}
		
		$dataInfo = date("Y-m-d H:i:s",$_SGLOBAL['timestamp']);
		jtsjc($dataInfo);
		
		// 不同的送礼途径物品ID获得途径不同
		if(2==$sltype){
			$sql = "select gift_type as itemId from ".$common->tname('social_trade');
			$sql .= " where id={$tradeid} and type=3 and toplayersid={$socialInfo['playersid']}";
			$tradeResult = $db->query($sql);
			$tradeInfo   = $db->fetch_array($tradeResult);
			$itemId = $tradeInfo['itemId'];
		}
		else{
			$itemId = $social_tools[intval(_get('clid'))]['id'];
		}
		// 写入送礼信息，$sltype=1时直接送礼，$sltype=2时转入回赠方式
		$social_trade['type'] = $sltype;
		$social_trade['fromplayersid'] = $socialInfo['playersid'];
	    $social_trade['toplayersid'] = $socialInfo['toplayersid'];

	    $social_trade['gift_type'] = $itemId;//赠送的材料id
	    $social_trade['create_time'] = $create_time;
	    $tradeId = $common->inserttable('social_trade',$social_trade);
	    
	    // 更新双方玩家送礼缓冲数据
	    $tradeInfo = array('id'=>$tradeId,
	    					'frompid'=> $social_trade['fromplayersid'],
	    					'topid'  => $social_trade['toplayersid'],
	    					'type'   => $social_trade['type'],
	    					'itemid' => $social_trade['gift_type'],
	    					'status' => 0,
	    					'ctime'  => $social_trade['create_time']);
	    					
	    socialModel::modifyMemTradeInfo($socialInfo['playersid'], $tradeInfo);
	    socialModel::modifyMemTradeInfo($socialInfo['toplayersid'], $tradeInfo);

	    $itemInfo = array();
	    foreach($social_tools as $s_tool){
	    	if($s_tool['id']==$itemId){
	    		$itemInfo = $s_tool;
	    		break;
	    	}
	    }
	    if(empty($itemInfo)){
			$returnValue['status']  = 27;
			$returnValue['message'] = $_g_lang['social']['no_gift_id'];
			return $returnValue;
		}
	    
	    // 增加好友度
	    socialModel::addFriendFeel($socialInfo['toplayersid'], $socialInfo['playersid'], 1);
		//发送消息
//		$roleInfo['playersid'] = $socialInfo['playersid'];
//		roleModel::getRoleInfo($roleInfo);
		$json = array();
		$json['playersid'] = $socialInfo['playersid'];
		$json['toplayersid'] = $socialInfo['toplayersid'];
		$json['message'] = array('wjmc1'=>$selfInfo['nickname'],'wjid1'=>$selfInfo['playersid'],'lwmc'=>$itemInfo['mc'], 'lwsl'=>1);		// 送礼名称
		// 用来区分回赠，这里为1，letterModel::agreeGift为2
		$json['type'] = '1';
		//直接送礼类型为3，索要后回礼是4
		$json['genre'] = $sltype==1?3:4;
		$json['uc'] = '0';
		$json['create_time'] = $create_time;
		
		$json['tradeid'] = $tradeId;
		//$json = json_encode($json);
		
		$result = lettersModel::addMessage($json);//送礼
		$value['status'] = 0;
		
		// 任务部分的调用
		$rwid = questsController::OnFinish($selfInfo,"'slrs'");
		if (!empty($rwid)) {
			$value['rwid'] = $rwid;
		} 
		
		// 索要送礼时需要删除对应信件
		if(2==$sltype){
			$value = lettersModel::deleteLetters($xxid,$socialInfo['playersid'],$page,$xxlx);
		}
		return $value;
	}
	
	/**
	 * 打劫请求处理
	 *
	 * @param array $socialInfo
	 * @param int $rwid				任务id
	 * @return array
	 */
	public static function setRobbery($socialInfo,&$rwid = ''){
		global $common,$db,$mc,$_SGLOBAL, $G_PlayerMgr,$_g_lang;
		$social_tools = get_social_tools();
		if (empty($socialInfo['toplayersid'])) {
			return 4;   //非法的客户端请求
		}
		
		$player = $G_PlayerMgr->GetPlayer($socialInfo['playersid'] );
		if(!$player)	return array('status'=>21, 'message'=>$_g_lang['social']['err_request']);
		
		// 检测对象是否封禁
		$tuInfo['playersid'] = $socialInfo['toplayersid'];
		if(!roleModel::getRoleInfo($tuInfo)){
			$returnValue['status']  = 26;
			$returnValue['message'] = $_g_lang['social']['err_pinfo'];
			return $returnValue;
		}
		elseif(1==$tuInfo['is_reason']){
			$returnValue['status']  = 21;
			$returnValue['message'] = $_g_lang['social']['account_deny'];
			return $returnValue;
		}
		
		$roleInfo['playersid'] = $socialInfo['playersid'];
		if(!roleModel::getRoleInfo($roleInfo)){
			$returnValue['status']  = 26;
			$returnValue['message'] = $_g_lang['social']['err_pinfo'];
			return $returnValue;
		}
		
		// 开启互斥锁
		$common->userMutex('_social_trade_'.$socialInfo['toplayersid']);
		
		//判断今天是否打劫
		if(socialModel::is_TodayRobbery($socialInfo['playersid'],$socialInfo['toplayersid'])!=1) {
			// 无法完成条件，关闭互斥锁
			$common->unLockMutex('_social_trade_'.$socialInfo['toplayersid']);
			$value['status'] = 21;
			$value['message'] = $_g_lang['social']['too_slow_no_gift'];
			return $value;
		}
		
		// 只能打劫超过保护时间的直接送礼并且没有被领取
		$diffTime = slbhsj();
		$safeTime = $_SGLOBAL['timestamp'] - $diffTime;
		if(!(defined('PRESSURE_TEST_DEBUG') && isset($_GET['debug']))){
			$safeTime = time();
		}
		$sql = "SELECT * FROM ".$common->tname('social_trade');
		$sql .= " WHERE toplayersid = '".$socialInfo['toplayersid'];
		$sql .= "' and (type=1 or type=2) and status=0";
		$sql .= " and create_time <= {$safeTime} LIMIT 1";
		
		$result = $db->query($sql);
		$rows = $db->fetch_array($result);
		$itemId = $rows['gift_type'];
		
		$slf['playersid'] = $rows['fromplayersid'];
		if(!roleModel::getRoleInfo($slf)){
			$common->unLockMutex('_social_trade_'.$socialInfo['toplayersid']);
			$returnValue['status']  = 26;
			$returnValue['message'] = $_g_lang['social']['err_pinfo'];
			return $returnValue;
		}
		
		if (empty($rows)) {
			// 无法完成条件，关闭互斥锁
			$common->unLockMutex('_social_trade_'.$socialInfo['toplayersid']);
			$value['status'] = 21;
			$value['message'] = $_g_lang['social']['too_slow_no_gift'];
			return $value;
		}

		// 添加打劫到的物品
		//$result_add = toolsModel::addPlayersItem($roleInfo, $rows['gift_type']);
		//$addInfo = toolsModel::addItems($roleInfo['playersid'], array($rows['gift_type']=>1), $djIdList, $oldDjList);
		$addInfo = $player->AddItems(array($rows['gift_type']=>1));
		if($addInfo === false) {
			// 无法完成条件，关闭互斥锁
			$common->unLockMutex('_social_trade_'.$socialInfo['toplayersid']);
			$value['status'] = 1002;
			$value['message'] = $_g_lang['social']['pack_full_no_robbery'];
			return $value;
		}
		
		// 将物品状态更新为已收
		// 这里不用缓存别的玩家的改变
		$update['status'] = 1;
        
		$wherearr['id'] = $rows['id'];
		$wherearr['toplayersid'] = $socialInfo['toplayersid'];
		$wherearr['status'] = 0;
		$common->updatetable('social_trade', $update, $wherearr);
		
		// 打劫可能出现冲突的流程完成，关闭互斥锁
		$common->unLockMutex('_social_trade_'.$socialInfo['toplayersid']);

		// 添加打劫信息
		$social_trade['type'] = 4;
		$social_trade['fromplayersid'] = $socialInfo['playersid'];
	    $social_trade['toplayersid'] = $socialInfo['toplayersid'];
	    $social_trade['gift_type'] = $itemId;//打劫的材料id
	    $social_trade['create_time'] = time();
	    $tradeId = $common->inserttable('social_trade',$social_trade);
	    
	    // 添加打劫数据到缓冲
	    $tradeInfo = array('id'=>$tradeId,
	    					'frompid'=> $social_trade['fromplayersid'],
	    					'topid'  => $social_trade['toplayersid'],
	    					'type'   => $social_trade['type'],
	    					'itemid' => $social_trade['gift_type'],
	    					'status' => 0,
	    					'ctime'  => $social_trade['create_time']);
	    socialModel::modifyMemTradeInfo($socialInfo['playersid'], $tradeInfo);
		
		// 修改好感度
		socialModel::addFriendFeel($socialInfo['toplayersid'], $socialInfo['playersid'], -1);
		
		// 删除对应的信件
		$letterWhere['playersid']      = $rows['toplayersid'];
		$letterWhere['fromplayersid']  = $rows['fromplayersid'];
		$letterWhere['tradeid']        = $rows['id'];
		$common->deletetable('letters', $letterWhere);
		
		//发送打劫消息
		$json['playersid'] = $rows['fromplayersid'];
		$json['toplayersid'] = $rows['toplayersid'];  //打劫人 
		
		//$itemInfo = toolsModel::getItemInfo($itemId);
		
	    $itemInfo = array();
	    foreach($social_tools as $s_tool){
	    	if($s_tool['id']==$itemId){
	    		$itemInfo = $s_tool;
	    		break;
	    	}
	    }
		$json['message'] = array('wjmc2'=>$slf['nickname'],'wjmc1'=>$roleInfo['nickname'],'wjid1'=>$roleInfo['playersid'],'lwmc'=>$itemInfo['mc'], 'lwsl'=>1);
		$json['type'] = '1';
		$json['genre'] = '5';           //打劫消息
		//$json = json_encode($json);
	
		$result = lettersModel::addMessage($json);
		
		// 打劫任务处理
		$rwid = questsController::OnFinish($roleInfo,"'dj'");
		if (!empty($rwid)) {
   			if (!empty($rwid)) {
    			$value['rwid'] = $rwid;
    		} 
		}
		
		// 返回数据
		$value['status'] = 0;
		$value['mc'] = idtomc($rows['gift_type']);
		$value['iid'] = $itemInfo['iid'];				//材料图标id
		$value['sl'] = 1;               //打劫数量
		//$bgDataInfo = toolsModel::getAllItems($socialInfo['playersid']);
		//$bagData = toolsModel::getBglist($djIdList, $socialInfo['playersid'], $oldDjList);
		$bagData = $player->GetClientBag();
		$value['list'] = $bagData;            //背包所有数据
		
		return $value;
	}
	
	//是否uc好友
	static function is_UcFriend($ucid) {
		global $common,$db;
		$result = $db->query("SELECT count(*) as icount FROM ".$common->tname('uc_friends')." WHERE ucid = '".$_SESSION['ucid']."' and fucid = '".$ucid."' LIMIT 1");
		$rows = $db->fetch_array($result);
		if ($rows['icount']>0) {
			return 4;
		}else{
			return 0;
		}
	}

	//
	static function is_UcGameFriend($ucid) {
		global $common,$db;	
		$result = $db->query("SELECT count(*) as icount FROM ".$common->tname('player')." WHERE ucid = '".$ucid."' LIMIT 1");
		$rows = $db->fetch_array($result);
		if ($rows['icount']>0) {
			return 4;
		}else{
			return 0;
		}
	}
	
	/**
	 * 检查是否对玩家进行过索要
	 *
	 * @param int $playersid			索要玩家
	 * @param int $toplayersid			被索要玩家
	 * @return int						1可以索要 0不能，2当天新好友，明天才能索要
	 */
	public static function is_TodaySy($playersid,$toplayersid) {
		global $common,$db,$_SGLOBAL;
		
		// 检查送礼双方等级和等级差
		$playerInfo['playersid'] = $playersid;
		$roleRes = roleModel::getRoleInfo($playerInfo);
		if (empty($roleRes)) {
		   return 0;
		}
		$toplayerInfo['playersid'] = $toplayersid;
		$roleRes = roleModel::getRoleInfo($toplayerInfo);
		if (empty($roleRes)) {
		   return 0;
		}
		
		// 双方等级十级以上
		if($playerInfo['player_level']<10
		||$toplayerInfo['player_level']<10){
			return 0;
		}
		
		$dataInfo = date("Y-m-d H:i:s",$_SGLOBAL['timestamp']);
		jtsjc($dataInfo);
		
		$friendIdList = roleModel::getTableRoleFriendsInfo($playersid, 1, true);
		if(!key_exists($toplayersid, $friendIdList)){
			return 0;
		}
		
		if(date('Y-m-d',$friendIdList[$toplayersid]['atime']) == date('Y-m-d')){
			return 2;
		}

		// 当天即没有索要（type=3）也没有玩家回礼（type=2）或送礼（type=1）
		$userTradeInfo = socialModel::getMemTradeInfo($playersid);
		foreach($userTradeInfo as $tradeInfo){
			// 缓存数据中frompid不等于自己的时候只会有别人对自己的送礼
			if($tradeInfo['topid']==$playersid
				&& $tradeInfo['frompid']==$toplayersid
				&&($tradeInfo['type']==1||$tradeInfo['type']==2)){
				return 0;
			}
			if($tradeInfo['type']==3
			&& $tradeInfo['topid']==$toplayersid){
				return 0;
			}
		}
		
		return 1;
	}
	
	/**
	 * 检查今天能否再次送礼
	 *
	 * @param int $playersid				送礼人
	 * @param int $toplayersid				收礼人
	 * @return int							0表示不能送礼，1表示可以送礼
	 */
	public static function is_TodayGift($playersid,$toplayersid) {
		global $common,$db,$_SGLOBAL;
		$dataInfo = date("Y-m-d H:i:s",$_SGLOBAL['timestamp']);
		jtsjc($dataInfo);
		
		// 检查送礼双方等级和等级差
		$playerInfo['playersid'] = $playersid;
		$roleRes = roleModel::getRoleInfo($playerInfo);
		if (empty($roleRes)) {
		   return 0;
		}		
		$toplayerInfo['playersid'] = $toplayersid;
		$roleRes2 = roleModel::getRoleInfo($toplayerInfo);
		if (empty($roleRes2)) {
		   return 0;
		}			
		// 双方等级十级以上才能送礼
		if($playerInfo['player_level']<10
		||$toplayerInfo['player_level']<10){
			return 0;
		}

		$friendIdList = roleModel::getTableRoleFriendsInfo($playersid, 1, true);
		if(!key_exists($toplayersid, $friendIdList)){
			return 0;
		}
		
		if(date('Y-m-d',$friendIdList[$toplayersid]['atime']) == date('Y-m-d')){
			return 2;
		}
		
		// 检查当天是否有过对$toplayersid送礼或回礼type=1送礼，type=2回礼
		$userTradeInfo = socialModel::getMemTradeInfo($playersid);
		foreach($userTradeInfo as $tradeInfo){
			// 只检查对$toplayersid的状态
			if($tradeInfo['topid']!=$toplayersid){
				continue;
			}
			if($tradeInfo['type']==1
				|| $tradeInfo['type']==2){
				return 0;
			}
		}
		
		return 1;
	}
	
	/**
	 * 判断能否对指定玩家打劫
	 *
	 * @param int $playersid			来打劫的玩家
	 * @param int $toplayersid			被检查的目标玩家
	 * @return int						1可以打劫，0不能打劫
	 */
	static function is_TodayRobbery($playersid,$toplayersid) {
		global $common,$db,$_SGLOBAL;
		
		// 得到当天第一秒
		$date_now=date("Y-m-d H:i:s",$_SGLOBAL['timestamp']);
		$year=((int)substr($date_now,0,4));//取得年份
		$month=((int)substr($date_now,5,2));//取得月份
		$day=((int)substr($date_now,8,2));//取得几号

		$todayZero = mktime(0,0,0,$month,$day,$year);
		
		$userTradeInfo = socialModel::getMemTradeInfo($playersid);
		foreach($userTradeInfo as $tradeInfo){
			// 判断当天是否已打劫
			if($tradeInfo['topid'] == $toplayersid
			&& 4 == $tradeInfo['type']){
				return 0;
			}
		}
		
		// 检查目标玩家是否有未收送礼(type 1,2 status 0)可以打劫
		$checkTime = time() - slbhsj();
		$giftSql = "select count(1) `count` from ".$common->tname('social_trade');
		$giftSql .= " where (type=1 or type=2) and status=0 and toplayersid={$toplayersid}";
		$giftSql .= " and create_time<'{$checkTime}' limit 1";
		$result = $db->query($giftSql);
		$row = $db->fetch_array($result);
		if($row['count']<=0){
			return 0;
		}
		
		return 1;
	}
    
	
	//仇人列表
	static function getBlacklist($playersid, $is_zl = '') {
		global $common,$db,$_SGLOBAL;
		$value = array();
		
		// 获得对应玩家基础信息
		$relationList = array();
		if(empty($is_zl)) {
			// 仇人流程
			$relationList = roleModel::getTableRoleFriendsInfo($playersid, 3);
		} else {
			// 占领流程
			$relationList = roleModel::getTableRoleFriendsInfo($playersid, 4);
		}
		
		// 格式化返回值
		foreach($relationList as $playerInfo) {
			$rows['jsid'] = $playerInfo['playersid'];
			$rows['mc']   = $playerInfo['nickname'];
			$rows['sex']  = $playerInfo['sex'];
			$rows['dj']   = $playerInfo['player_level'];
			$rows['is_dj'] = socialModel::is_TodayRobbery($playersid,$rows['jsid']);
	  		$rows['vip']  = $playerInfo['vip'];
	  		if(!empty($is_zl)){
	  			// 能否偷将
  				$rows['tj'] = (cityModel::is_onetj($playersid,$playerInfo) == 1) ? 1 : 0;
  				// 征收占领收益
	  			$rows['zs'] = (cityModel::is_onezs($playersid,$rows['jsid']) == 1) ? 1 : 0; // 这个操作是否还需要吗？？
	  		}
	  		
	  		$value[] = $rows;
  		}
		return $value;
	}
	
	/**
	 * 是否驻防
	 *
	 * @param int			$playersid		表示pid
	 * @return array
	 */
	static function is_defend($playersid) {
		global $db,$common,$mc,$_SGLOBAL,$_g_lang;
		$roleInfo = array();
		
		$roleInfo['playersid'] = $playersid;
		$roleRes = roleModel::getRoleInfo($roleInfo);
		if (empty($roleRes)) {
		   $value = array('status'=>3,'message'=>$_g_lang['social']['no_find_account']);
		   return $value;
		}		
		if($roleInfo['is_defend'] == 1) {
			$rows['zluid'] = $roleInfo['playersid'];
			$rows['zlun'] = $roleInfo['nickname'];
			$rows['zlul'] = $roleInfo['player_level'];
			$end_defend_time = $roleInfo['end_defend_time'] - time();
			if($end_defend_time > 0) {
			// 这里保证zlut是一个正整数，为撒不知道，问王勇
				$rows['zlut'] = 14400 - ($end_defend_time%14400);
			}else{
				$rows['zlut'] = 0;
			}
		}elseif($roleInfo['aggressor_playersid'] <> $playersid && $roleInfo['aggressor_playersid'] > 0 && $roleInfo['end_defend_time'] > $_SGLOBAL['timestamp']) {
			//print_r($roleInfo);
			$rows['zluid'] = $roleInfo['aggressor_playersid'];
			$rows['zlun'] = $roleInfo['aggressor_nickname'];
			$rows['zlul'] = $roleInfo['aggressor_level'];
			//echo($roleInfo['aggressor_playersid'].'|');
			//echo($roleInfo['aggressor_general']);
			$general = cityModel::getGeneralData($roleInfo['aggressor_playersid'],'',$roleInfo['aggressor_general'],0);
			//print_r($general);
			$end_defend_time = time() - $general[0]['last_income_time'];
			if($end_defend_time > 0 ) {
				$rows['zlut'] = 14400 - ($end_defend_time%14400);
			}else{
				$rows['zlut'] = 0;
			}
			$result = $db->query("SELECT aggressor_message FROM ".$common->tname('aggressor_message')." WHERE playersid = '".$playersid."' order by create_time desc  LIMIT 1");
			$rows_xx = $db->fetch_array($result);
			if(!empty($rows_xx)) {
				$rows['zlxx'] = $rows_xx['aggressor_message'];
			}
		}else{
			return '';
		}
		return $rows;
	}
	

	/**
	 * 添加仇人关系
	 *
	 * @param int $playersid		攻城方
	 * @param int $toplayersid		被打方
	 * @return int
	 */
	static function addEnemy($playersid, $toplayersid) {
		global $common,$db,$mc;
		// 获取对方仇人数量
		$relationList = roleModel::getTableRoleFriendsInfo($toplayersid, 3, true);
		
		// 如果数量过大删除多出来的玩家
		$enemy_space_num = count($relationList)+1-ENEMY_MAX_COUNT;
		if(0 < $enemy_space_num){
			$delSql = "delete from " . $common->tname('social_user') ;
			$delSql .= " where parent_playersid={$toplayersid} and type=3 order by add_time limit {$enemy_space_num}";
			$db->query($delSql);
			$mc->delete(MC.$toplayersid.'_enemys');
		}
		
		// 判断是否有仇人关系
		if(key_exists($playersid, $relationList)){
			return 0;
		}
		
		// 如果没有仇人关系就添加对应关系
		$insertArr['parent_playersid'] = $toplayersid;
		$insertArr['type']             = 3;
		$insertArr['playersid']        = $playersid;
		$insertArr['add_time']         = time();
		$common->inserttable('social_user', $insertArr);
		
		$mc->delete(MC.$toplayersid.'_enemys');
		
		// zzz的接口要更新的可历练的列表
		$mc->delete(MC.$playersid.'_friendNotRepeat');
		$mc->delete(MC.$toplayersid.'_friendNotRepeat');
	}
	
	/**
	 * 处理可占领列表的缓存数据
	 * 
	 * @param int $playersid			用来指定列表的玩家id
	 * @param int $removepid			要移除的玩家id
	 */
	public static function removeOccupyList($playersid, $removepid){
		global $mc;
		$canOccupyList = socialModel::findRandFriend($playersid, 2, 0, true);//$mc->get(MC.$playersid.'_canOccupys');
		if(is_array($canOccupyList)&&isset($canOccupyList[$removepid])){
			unset($canOccupyList[$removepid]);
			$mc->set(MC.$playersid.'_canOccupys', $canOccupyList, 0, 3000);
//			foreach($canOccupyList as $key=>$cityID){
//				if($cityID == $removepid){
//					unset($canOccupyList[$key]);
//					$mc->set(MC.$playersid.'_canOccupys', $canOccupyList, 0, 3000);
//					break;
//				}
//			}
			return true;
		}
		
		return false;
	}
	
	//减 银两，开始历练 
	static function lessSilver($socialInfo) {
		global $common,$db,$mc,$_SGLOBAL,$_g_lang;
		$rows_ingot = $mc->get(MC.$socialInfo['playersid']);
		if($rows_ingot == "") {
			$result = $db->query("SELECT silver FROM ".$common->tname('player')." WHERE playersid = '".$socialInfo['playersid']."' LIMIT 1");
			$rows_ingot = $db->fetch_array($result);
		}
		$rows_tf = cityModel::getGeneralData($socialInfo['playersid'],0,$socialInfo['generalid']);
		if (empty($rows_tf)) {
			return 2;                                 
		} else {
			$cd_xs = llcdxs($rows_tf[0]['llcs']);
			$cd_second = $cd_xs * 3600;
			$last_end_ll = $rows_tf[0]['last_end_ll'];
			if(($last_end_ll + $cd_second) > $_SGLOBAL ['timestamp']) {
				$cz = ($last_end_ll + $cd_second) - $_SGLOBAL ['timestamp'];
				return '10|'.$cz; 
			}
		}
		
		$result = $db->query("SELECT generalid FROM ".$common->tname('practice')." WHERE playersid = '".$socialInfo['playersid']."' and generalid = '".$socialInfo['generalid']."' and status = 0 LIMIT 1");
		$rows = $db->fetch_array($result);
		if(!empty($rows)) {
			if($rows['generalid'] <> "") {
				if($rows['generalid'] > 0) {
					if($rows['generalid'] <> $socialInfo['generalid']) {
						$returnValue['status'] = 1121;                                 
						$returnValue['message'] = $_g_lang['social']['y_gen_at_exp_when_exp_new'];
						return $returnValue;					//存在其他历练将领
					}else{
						return 0;								//该武将正在历练
					}
				}
			}
		}else{		//没有武将历练
			if ($rows_tf[0]['llcs'] >= ($rows_tf[0]['professional_level'] * 10) ) {
				return 4;
			}
			$tf = $rows_tf[0]['llcs'] + 1;
			$needYl = llyl($tf);
			/*if($rows_ingot['silver'] < $needYl) {
				echo '5';
				return '5|'.$rows_ingot['silver'].'|'.$needYl;//银两不足
			}*/
			$needRS = llrs($tf);
			$practice['invite_num'] = $needRS;
			$practice['playersid'] = $socialInfo['playersid'];
        	$practice['generalid'] = $socialInfo['generalid'];
        	$practice['create_time'] = time();
        	$common->inserttable('practice',$practice);
			//return 2;
		}
		
		$temp_silver = 0;/*$rows_ingot['silver'] - $needYl;
		$db->query("update ".$common->tname('player')." set silver = silver - ".$needYl." WHERE playersid=".$socialInfo['playersid']." LIMIT 1");
		//休息内存数据  player 银两
		$mm_ingot = $mc->get(MC.$socialInfo['playersid']);
		if($mm_ingot <> "") {
			$updateMem['silver'] = $temp_silver;
			$common->updateMemCache(MC.$socialInfo['playersid'],$updateMem);
		}*/
		
		$update['is_practice'] = $socialInfo['generalid'];
		$wherearr['playersid'] = $socialInfo['playersid'];
		$common->updatetable('social',$update,$wherearr);
		
		$common->updatetable('playergeneral',"llzt = 1","intID = ".$socialInfo['generalid']);
		//修改内存
		$gen_rows = cityModel::getGeneralData($socialInfo['playersid'],0,$socialInfo['generalid']);
		if($gen_rows <> "") {
			$gen_rows[0]['llzt'] = "1";
  	     	$newData[$gen_rows[0]['sortid']] = $gen_rows[0];
  	     	$common->updateMemCache(MC.$socialInfo['playersid'].'_general',$newData);
		}
		return "0"."|".$temp_silver."|".$needYl;
	}
	
	// 历练限制检查
	static function practiceStrict($wjdj, $tf, $jj, $llcs) {
		global $common,$db,$_SGLOBAL,$mc,$_g_lang;
		
		$wjjb_strict = array(1,4,7,10,13,16,19,22,25,28);
		$wjjb_block = array(1=>1,2=>1,3=>1,4=>2,5=>2,6=>2,7=>3,8=>3,9=>3,10=>4,11=>4,12=>4,13=>5,14=>5,15=>5,16=>6,17=>6,18=>6,19=>7,20=>7,21=>7,22=>8,23=>8,24=>8,	25=>9,26=>9,27=>9,28=>10,29=>10,30=>10);
		
		// 初始天赋=当前天赋值-已历练总次数
		$tf = $tf - $llcs;	
					
		// 武将等级小于30级时，如果不在每3级等级列表中
		// 如果此级别下历练次数未用完不进入此判断		
		if($wjdj < 30 && ($llcs >= $wjjb_block[$wjdj])) {
			if(!in_array($wjdj, $wjjb_strict)) {
				$idx = 0;
				foreach ($wjjb_strict as $k=>$v) {
					if($v > $wjdj) {
						$idx = $k;
						break;
					}
				}
				return array('status'=>998, 'message'=>$_g_lang['social']['bf_30_level_exp_1_per_3']);
			} else { // 防止用户重复刷历练
				return array('status'=>998, 'message'=>$_g_lang['social']['general_expr_max_at_lvl']);
			}
		}

		if($llcs >= ($jj * 10)) {
			return array('status'=>998, 'message'=>$_g_lang['social']['general_expr_max']);
		}
		
		return array('status'=>0);	    
	}
	
	//武将历练
	static function getPracticeInfo($socialInfo) {
		global $common,$db,$_SGLOBAL,$mc,$_g_lang;
		$return_message = array();
		$return_data1 = array();
		$return_data2 = array();
		$roleInfo['playersid'] = $socialInfo['playersid'];
		$roleRes = roleModel::getRoleInfo($roleInfo);
		if (empty($roleRes)) {
		   $value = array('status'=>21,'message'=>$_g_lang['social']['no_find_account']);
		   return $value;
		}		
		$jwdj = $roleInfo['mg_level'];
		$jwInfo = jwmc($jwdj);
		$jwjc = 1 + $jwInfo['jc'] / 100;
		//返回加成													
		$ginfo = cityModel::getGeneralData($socialInfo['playersid'],0,$socialInfo['generalid']);
		$rows['wjid'] = $ginfo[0]['intID'];
		$rows['tx'] = $ginfo[0]['avatar'];	
		$rows['mc'] = $ginfo[0]['general_name'];	
		$rows['tf'] = $ginfo[0]['understanding_value'];
		$rows['llcs'] = $ginfo[0]['llcs'];		
		$rows['jj'] = $ginfo[0]['professional_level'];
		$rows['zy'] = $ginfo[0]['professional'];
		$zbgjjcArray = array ();
		$zbfyjcArray = array ();
		$zbtljcArray = array ();
		$zbmjjcArray = array ();
		$zb1 = $ginfo[0]['helmet'];
		if ($zb1 != 0) {
			$zb1Info = toolsModel::getZbSx ( $ginfo[0]['playerid'], $zb1 );
			$zbgjjcArray [] = $zb1Info ['gj'];
			$zbfyjcArray [] = $zb1Info ['fy'];
			$zbtljcArray [] = $zb1Info ['tl'];
			$zbmjjcArray [] = $zb1Info ['mj'];
		}
		$zb2 = $ginfo[0]['carapace'];
		if ($zb2 != 0) {
			$zb2Info = toolsModel::getZbSx ( $ginfo[0]['playerid'], $zb2 );
			$zbgjjcArray [] = $zb2Info ['gj'];
			$zbfyjcArray [] = $zb2Info ['fy'];
			$zbtljcArray [] = $zb2Info ['tl'];
			$zbmjjcArray [] = $zb2Info ['mj'];
		}
		$zb3 = $ginfo[0]['arms'];
		if ($zb3 != 0) {
			$zb3Info = toolsModel::getZbSx ( $ginfo[0]['playerid'], $zb3 );
			$zbgjjcArray [] = $zb3Info ['gj'];
			$zbfyjcArray [] = $zb3Info ['fy'];
			$zbtljcArray [] = $zb3Info ['tl'];
			$zbmjjcArray [] = $zb3Info ['mj'];
		}
		$zb4 = $ginfo[0]['shoes'];
		if ($zb4 != 0) {
			$zb4Info = toolsModel::getZbSx ( $ginfo[0]['playerid'], $zb4 );
			$zbgjjcArray [] = $zb4Info ['gj'];
			$zbfyjcArray [] = $zb4Info ['fy'];
			$zbtljcArray [] = $zb4Info ['tl'];
			$zbmjjcArray [] = $zb4Info ['mj'];
		}
		$zbgjjc = array_sum ( $zbgjjcArray );
		$zbfyjc = array_sum ( $zbfyjcArray );
		$zbtljc = array_sum ( $zbtljcArray );
		$zbmjjc = array_sum ( $zbmjjcArray );
		
		$g_zy = $ginfo[0]['professional']; //职业		
		$sxxs = genModel::sxxs ( $g_zy );
		$g_dj = $ginfo[0]['general_level'];
		$g_tf_up = $ginfo[0]['understanding_value'];
		$g_tf_down = $ginfo[0]['understanding_value']+1;
			
		//$rows ['gjl'] = round ( ((genModel::wjqx ( $g_dj ) * 0.4 * $sxxs ['gj']) + genModel::wjtf ( $g_tf_up, $g_dj ) * $sxxs ['gj']) * $jwjc + $zbgjjc, 0 ); //攻击值
		$rows ['gjl'] = genModel::hqwjsx($g_dj,$g_tf_up,$ginfo[0]['professional_level'],$ginfo[0] ['llcs'],$jwjc,$zbgjjc,$sxxs ['gj'],$ginfo[0]['py_gj']);//攻击值
		//$rows ['fyl'] = round ( ((genModel::wjqx ( $g_dj ) * 0.4 * $sxxs ['fy']) + genModel::wjtf ( $g_tf_up, $g_dj ) * $sxxs ['fy']) * $jwjc + $zbfyjc, 0 ); //防御值
		$rows ['fyl'] = genModel::hqwjsx($g_dj,$g_tf_up,$ginfo[0]['professional_level'],$ginfo[0] ['llcs'],$jwjc,$zbfyjc,$sxxs ['fy'],$ginfo[0]['py_fy']);//防御值
		//$rows ['tl'] = round ( ((genModel::wjqx ( $g_dj ) * 0.4 * $sxxs ['tl']) + genModel::wjtf ( $g_tf_up, $g_dj ) * $sxxs ['tl']) * $jwjc + $zbtljc, 0 ); //体力值
		$rows ['tl'] = genModel::hqwjsx($g_dj,$g_tf_up,$ginfo[0]['professional_level'],$ginfo[0] ['llcs'],$jwjc,$zbtljc,$sxxs ['tl'],$ginfo[0]['py_tl']); //体力值
		//$rows ['mj'] = round ( ((genModel::wjqx ( $g_dj ) * 0.4 * $sxxs ['mj']) + genModel::wjtf ( $g_tf_up, $g_dj ) * $sxxs ['mj']) * $jwjc + $zbmjjc, 0 ); //敏捷值
		$rows ['mj'] = genModel::hqwjsx($g_dj,$g_tf_up,$ginfo[0]['professional_level'],$ginfo[0] ['llcs'],$jwjc,$zbmjjc,$sxxs ['mj'],$ginfo[0]['py_mj']); //敏捷值
		//$rows ['llgjl'] = round ( ((genModel::wjqx ( $g_dj ) * 0.4 * $sxxs ['gj']) + genModel::wjtf ( $g_tf_down, $g_dj ) * $sxxs ['gj']) * $jwjc+ $zbgjjc, 0 ); //攻击值
		$rows ['llgjl'] = genModel::hqwjsx($g_dj,$g_tf_down,$ginfo[0]['professional_level'],$ginfo[0] ['llcs'] + 1,$jwjc,$zbgjjc,$sxxs ['gj'],$ginfo[0]['py_gj']); //攻击值
		//$rows ['llfyl'] = round ( ((genModel::wjqx ( $g_dj ) * 0.4 * $sxxs ['fy']) + genModel::wjtf ( $g_tf_down, $g_dj ) * $sxxs ['fy']) * $jwjc + $zbfyjc, 0 ); //防御值
		$rows ['llfyl'] = genModel::hqwjsx($g_dj,$g_tf_down,$ginfo[0]['professional_level'],$ginfo[0] ['llcs'] + 1,$jwjc,$zbfyjc,$sxxs ['fy'],$ginfo[0]['py_fy']);//防御值
		//$rows ['lltl'] = round ( ((genModel::wjqx ( $g_dj ) * 0.4 * $sxxs ['tl']) + genModel::wjtf ( $g_tf_down, $g_dj ) * $sxxs ['tl']) * $jwjc + $zbtljc, 0 ); //体力值
		$rows ['lltl'] = genModel::hqwjsx($g_dj,$g_tf_down,$ginfo[0]['professional_level'],$ginfo[0] ['llcs'] + 1,$jwjc,$zbtljc,$sxxs ['tl'],$ginfo[0]['py_tl']);//体力值
		//$rows ['llmj'] = round ( ((genModel::wjqx ( $g_dj ) * 0.4 * $sxxs ['mj']) + genModel::wjtf ( $g_tf_down, $g_dj ) * $sxxs ['mj']) * $jwjc + $zbmjjc, 0 ); //敏捷值
		$rows ['llmj'] = genModel::hqwjsx($g_dj,$g_tf_down,$ginfo[0]['professional_level'],$ginfo[0] ['llcs'] + 1,$jwjc,$zbmjjc,$sxxs ['mj'],$ginfo[0]['py_mj']);//体力值
	
		$tf = $rows['llcs'] + 1;
		$rows['llrs'] = llrs($tf);   //历练所需人数
		$rows['xxcs'] = intval($tf);   //武将已经历练次数
		
		$llyb = llyb($tf);
		$rows['llyb'] = intval($llyb);
		$value[] = $rows;
		$return_message[] = $value;
		unset($rows);
  	  	  		
  		$result = $db->query("select * from ".$common->tname('practice')." where generalid = '".$socialInfo['generalid']."' and playersid = '".$socialInfo['playersid']."' and status = 0 LIMIT 1");
  		$rows = $db->fetch_array($result);
		$temp_practiveid = $rows['id'];
		$temp_jhl_num = $rows['tool_num'];	
		if(!empty($rows)) {			
			if($rows['yq_playersid'] != '') {
				$temp_yq_playersid_dir = explode(",",$rows['yq_playersid']);
				$temp_is_agree_dir = unserialize($rows['is_agree']);
        global $G_PlayerMgr;
        //$arryagreed = array_keys($temp_is_agree_dir,1);
        //array_intersect($arryagreed, $temp_yq_playersid_dir);
        $arryagreed = array();
        for($i = 0,$l=count($temp_yq_playersid_dir); $i < $l; ++$i ) {
					if($temp_is_agree_dir[$temp_yq_playersid_dir[$i]] != 0) 
            $arryagreed[] = $temp_yq_playersid_dir[$i];
				}
        if(!empty($arryagreed)) {
          $players = $G_PlayerMgr->GetPlayers($arryagreed);
          foreach($players as $player) {
            $roleInfo = $player->baseinfo_;
            $return_data1[] = $temp_null = array(
              'jsid' => $roleInfo['playersid'],
              'mc'=>$roleInfo['nickname'],
              's' => intval($temp_is_agree_dir[$player->GetId()]),
              'sex'=> $roleInfo['sex']);
          }
        }
			}
			$return_message[] = $return_data1;
		}
		$return_message['jhl'] = $temp_jhl_num;
		return $return_message;
	}

	static function updateArrData(&$temp,&$delStr,$str1,$str2,$str3,$socialInfo,$practiceid) {   //yq_playersid,is_agree,invite_time
		global $common,$db;
		
		for($i = 0 ; $i < count($temp); $i ++ ){
			if($temp['temp_yq_playersid_dir'][$i] == $str1) {
				unset($temp['temp_yq_playersid_dir'][$i]);
				unset($temp['temp_is_agree_dir'][$i]);
				unset($temp['temp_invite_time_dir'][$i]);
				break;
			}
		}
		$temp['temp_yq_playersid_dir'] = array_values($temp['temp_yq_playersid_dir']);
		$temp['temp_is_agree_dir'] = array_values($temp['temp_is_agree_dir']);
		$temp['temp_invite_time_dir'] = array_values($temp['temp_invite_time_dir']);
		
		$temp['temp_yq_playersid_dir'] = empty($temp['temp_yq_playersid_dir'])?'':$temp['temp_yq_playersid_dir'];
		$temp['temp_is_agree_dir'] = empty($temp['temp_is_agree_dir'])?'':$temp['temp_is_agree_dir'];
		$temp['temp_invite_time_dir'] = empty($temp['temp_invite_time_dir'])?'':$temp['temp_invite_time_dir']; 
		//echo "DELETE FROM " . $common->tname('letters') . " WHERE playersid = {$str1} and fromplayersid = {$socialInfo['playersid']} and genre='7' and (status='0' or status='1') and practiceid = {$practiceid}";
		if($str1 != '')
			$delStr .= $str1 . ',';
			//$db->query("DELETE FROM " . $common->tname('letters') . " WHERE playersid = {$str1} and fromplayersid = {$socialInfo['playersid']} and genre='7' and (status='0' or status='1') and practiceid = {$practiceid}");
		
			
		//return $temp;
	}	
	
	//判断今天是否已接受历练邀请
	static function is_TodayRepeat($playersid,$tuid,$type = false) {
		global $common,$db,$_SGLOBAL;
		$dataInfo = date("Y-m-d H:i:s",$_SGLOBAL['timestamp']);
		jtsjc($dataInfo);   //当天开始结束时间戳
		if($type == false) {
			$result = $db->query("select id,yq_playersid,is_agree,status from ".$common->tname('practice')." where playersid = '".$tuid."' and create_time > '".$dataInfo[0]."' and create_time < '".$dataInfo[1]."'");  //and status = 0 LIMIT 1
			//heroCommon::insertLog("判断今天是否已接受历练邀请select id,yq_playersid,is_agree,status from ".$common->tname('practice')." where playersid = '".$tuid."' and create_time > '".$dataInfo[0]."' and create_time < '".$dataInfo[1]."'");
			$jsid = '';
			$friends = '';
			$is_agree = '';
			while ($rows = $db->fetch_array($result)) {
				$friends .= $rows['yq_playersid'].',';
				$is_agree .= $rows['is_agree'].',';
				$is_status = $rows['status'];
			}
			//heroCommon::insertLog('=======---------========'.$friends.'-=-=-=-='.$is_agree);
			$friends_arr = explode(",",$friends);
			$is_agree_arr = explode(",",$is_agree);
			for($i = 0; $i < count($friends_arr); $i++ ) {
				if($friends_arr[$i] <> '') {
					//heroCommon::insertLog('=======---------========'.$friends_arr[$i]);
					if($is_agree_arr[$i] == '1' ) {
						//heroCommon::insertLog('=======-----playersid----========'.$playersid);
						if($friends_arr[$i] == $playersid) {
							//heroCommon::insertLog('=======-----playersid----========111111111');
							return 1;
						}
					}
				}
			}
			return 0;
		} elseif($type == true) {
			$result = $db->query("select id,yq_playersid,is_agree,status from ".$common->tname('practice')." where playersid = '".$tuid."' and create_time > '".$dataInfo[0]."' and create_time < '".$dataInfo[1]."'");
			$jsid = '';
			$friends = '';
			$is_agree = '';
			while ($rows = $db->fetch_array($result)) {
				$friends .= $rows['yq_playersid'].',';
				$is_agree .= $rows['is_agree'].',';
				$is_status = $rows['status'];
			}
			
			$value = array();
			$friends_arr = explode(",",$friends);
			$is_agree_arr = explode(",",$is_agree);
			for($i = 0; $i < count($friends_arr); $i++ ) {
				if($friends_arr[$i] <> '') {
					if($is_agree_arr[$i] == '1' ) {
						$value[] = $friends_arr[$i];
					}
					//if(socialModel::selLetters(7,$friends_arr[$i],$dataInfo) == 1) {
						
					//}
				}
			}
			return $value;
		}
	}
	
	//历练好友列表总数
	static function selLetters($type,$playersid,$dataInfo) {
		global $common,$db;
		if($type == 7) {
			$result = $db->query("select count(*) as icount from ".$common->tname('letters')." where playersid = '".$playersid."' and genre = '".$type."' and status = 0 and create_time > '".$dataInfo[0]."' and create_time < '".$dataInfo[1]."' LIMIT 1"); 
			$rows = $db->fetch_array($result);
			if($rows['icount'] > 0) {
				return 1;
			} else {
				return 0;
			}
		}
	}
	
	//历练好友列表总数
	static function getFriendsNotRepeatCount($socialInfo) {
		global $common,$db,$_SGLOBAL,$mc;
		if (!empty($socialInfo['userid'])) {
			$item = " && userid = '".$socialInfo['userid']."'";
		} else {
			$item = '';
		}
		$item1 = " type = '1' and ";
		
		if (empty($socialInfo['userid']) && empty($socialInfo['playersid'])) {
			return false;
		}

		date_default_timezone_set('PRC');
		$year = date('Y');
		$month = date('m');
		$day = date('d');
		
		$result = null;
		// 获取今天0时0分的时间戳
		// 当天加的好友不可出现在历练邀请列表中	
		// 已封禁用户不出现在历练邀请列表中(ol_player.is_reason=1)
		//$time_limit = strtotime("{$year}-{$month}-{$day} 0:0:0");
		if (!($result = $mc->get(MC.$socialInfo['playersid'] . '_friendNotRepeat'))) {
			$friendNotRepeat = $db->query("SELECT t1.playersid as jsid,t1.nickname as mc,t1.sex,t1.player_level as jb,IF(0,-1,-1) as 'type' FROM ".$common->tname('player') ." t1, ".$common->tname('social_user') ." t2  WHERE  t1.playersid=t2.playersid and t2.type = '1' and t2.parent_playersid = ".$socialInfo['playersid'] . " and t1.is_reason <> 1 order by t1.player_level desc LIMIT 200");
			while($result[] = $db->fetch_array($friendNotRepeat));
		   	array_pop($result);
		   	$mc->set(MC.$socialInfo['playersid'].'_friendNotRepeat', $result, 0, 3600); 
		}
		
		// 根据日期设置可邀请好友状态
		if(!($block = $mc->get(MC.$socialInfo['playersid'] . '_friendBlock'))) {
			$block = null;
			$blockRet = $db->query("SELECT * FROM ".$common->tname('practice_block') . " WHERE playersid = {$socialInfo['playersid']}");
			if(mysql_num_rows($blockRet) != 0) { 
			   	while($tmp = $db->fetch_array($blockRet)) {
			   		$block[$tmp['toplayersid']] = $tmp['blocktime'];
			   	}
			   	$mc->set(MC.$socialInfo['playersid'] . '_friendBlock', $block, 0, 0);
			}
		}
		
		for($i = 0; $i < count($result); $i++) {
			if(isset($block[$result[$i]['jsid']])) {
				$yq_year = date('Y', $block[$result[$i]['jsid']]);
				$yq_month = date('m', $block[$result[$i]['jsid']]);
				$yq_day = date('d', $block[$result[$i]['jsid']]);
				if(($yq_year < $year || $yq_month < $month) || ($yq_year == $year && $yq_month == $month && $yq_day < $day)) {
					// 第二天修改为可以邀请状态
					$result[$i]['type'] = 1;
				} else {
					$result[$i]['type'] = 0;
				}

			} else {
				$result[$i]['type'] = 1;
			}
		}
		// 预留:后续如需要根据是否已邀请对历练邀请列表排序,可对$result进行操作按type和级别进行排序,排序后再写入内存
		$mc->set(MC.$socialInfo['playersid'].'_friendNotRepeat', $result, 0, 3600);
		
		// 根据军阶来过滤可邀请好友列表
		$general = cityModel::getGeneralData($socialInfo['playersid'], 0, $socialInfo['generalid']);
		$level_limit = null;
		if($general[0]['professional_level'] == 1) {
			$level_limit = 1;
		} else if($general[0]['professional_level'] == 2) {
			$level_limit = 10;
		} else if($general[0]['professional_level'] == 3) {
			$level_limit = 20;
		} else if($general[0]['professional_level'] == 4) {
			$level_limit = 30;
		} else if($general[0]['professional_level'] == 5) {
			$level_limit = 40;
		}

		$cnt = 0;
		if($level_limit != null) {
			for($j = 0; $j < count($result); $j++) {
				if($result[$j]['jb'] >= $level_limit) {
					$cnt++;
				}
			}
		}
		
		if($cnt != 0) return $cnt;
		
		return count($result);		
	}
	
	//历练好友列表
	static function getFriendsNotRepeat($socialInfo) {
		global $common,$db,$_SGLOBAL,$mc;
		$page = _get('page');
		if($page == 0 || $page == '') {
			$page = 1;
		}
		
		$zs = $socialInfo['zs'];		
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
			$_end = $_start + $pageRowNum;
		}else{
			$_end = $zs;
		}	
		
		$result = $mc->get(MC.$socialInfo['playersid'].'_friendNotRepeat');
		// 请求第一页时排序		
		if($page == 1) {			
			$kyq_arr = $bkyq_arr = array();			
			foreach($result as $key=>$value) {
				if($value['type'] == 1) 
					$kyq_arr[] = $value;
				else
					$bkyq_arr[] = $value;
			}
			
			$result = array_merge($kyq_arr, $bkyq_arr);
			$mc->set(MC.$socialInfo['playersid'].'_friendNotRepeat', $result, 0, 3600);
		}
		
		// 根据军阶来过滤可邀请好友列表
		$general = cityModel::getGeneralData($socialInfo['playersid'], 0, $socialInfo['generalid']);
		$level_limit = null;
		if($general[0]['professional_level'] == 1) {
			$level_limit = 1;
		} else if($general[0]['professional_level'] == 2) {
			$level_limit = 10;
		} else if($general[0]['professional_level'] == 3) {
			$level_limit = 20;
		} else if($general[0]['professional_level'] == 4) {
			$level_limit = 30;
		} else if($general[0]['professional_level'] == 5) {
			$level_limit = 40;
		}
		
		// 获取内存好友信息
		$friendIDList = roleModel::getTableRoleFriendsInfo($socialInfo['playersid'], 1, true);
			
		$temp_ret = $temp_ret1 = null;
		if($level_limit != null) {
			for($j = 0; $j < count($result); $j++) {
				if($result[$j]['jb'] >= $level_limit) {
					// fix notice warrning
					if(!array_key_exists($result[$j]['jsid'], $friendIDList)) {
						continue;
					}
					
					// 如果添加好友时间为当天那么就设置type
					if(date('Y-m-d', $friendIDList[$result[$j]['jsid']]['atime']) == date('Y-m-d')) {
						$result[$j]['type'] = 2;
					} 
					$temp_ret[] = $result[$j]; 					
				}
			}
		}		
		
		if($temp_ret != null) {
			unset($result);
			$result = $temp_ret;
		
			$kyq_arr = $bkyq_arr = array();			
			foreach($result as $key=>$value) {
				if($value['type'] == 2) 
					$bkyq_arr[] = $value;
				else
					$kyq_arr[] = $value;
			}
			$result = array_merge($kyq_arr, $bkyq_arr);
		
		}
		
		$value = array();
		for($i = $_start; $i < count($result); $i++) {
			if($i >= $_start) {
				if($i < $_end) {
					$value[] = $result[$i];					
				} else {
					break;
				}
			}
		}
		
		if (!empty($value)) {
			return $value;
		} else {
			return $value;
		}
	}
	
	//历练 挑选申请好友
	static function selPractice($socialInfo) {
		global $common,$db;
		
		/*$result = $db->query("select a.general_name as jm,a.understanding_value as tf from ".$common->tname('playergeneral')." a LEFT JOIN ".$common->tname('player')." b ON a.playerid = b.playersid where 1 = 1 and a.intID = '".$socialInfo['generalid']."' and a.playerid = '".$socialInfo['playersid']."' LIMIT 1");
		while ($rows = $db->fetch_array($result)) {			
			$temp_dir2 = explode("|",socialModel::getTianfu($rows['tf']));//根据天赋得到历练所需积分与人数
			$practice['need_points'] = $temp_dir2[2]; //所需修行积分
			$practice['invite_num'] = $temp_dir2[0];  //所需连接人数 
  		}*/
		$practice['invite_num'] = 3;
  		$result = $db->query("SELECT nickname FROM ".$common->tname('player')." WHERE playersid = '".$socialInfo['toplayersid']."' LIMIT 1");
		$rows_toplayer = $db->fetch_array($result);
		
  		$result = $db->query("SELECT * FROM ".$common->tname('practice')." WHERE playersid = '".$socialInfo['playersid']."' and generalid = '".$socialInfo['generalid']."'  and status = 0 LIMIT 1");
		$rows = $db->fetch_array($result);
		$temp_npc_count = ($rows['npc'] == "") ? 0 : count(explode(",",$rows['npc']))-1;
		if(!empty($rows) && $temp_npc_count < $practice['invite_num']) {
			$old_str = $rows['yq_playersid'];
			$temp_yq_playersid_dir = explode(",",$rows['yq_playersid']);
			$temp_is_agree_dir = explode(",",$rows['is_agree']);
			$temp_invite_time_dir = explode(",",$rows['invite_time']);
			$news_yq_playersid = "";
			$news_is_agree = "";
			$news_invite_time = "";
			for($i = 0;$i<count($temp_yq_playersid_dir);$i++) {
				if($temp_yq_playersid_dir[$i] == $socialInfo['toplayersid']) {
					continue;
				}else{
					$news_yq_playersid = $news_yq_playersid.",".$temp_yq_playersid_dir[$i];
					$news_is_agree = $news_is_agree.",".$temp_is_agree_dir[$i];
					$news_invite_time = $news_invite_time.",".$temp_invite_time_dir[$i];
				}
			}
			//echo("dfdf");
			//print_r($news_yq_playersid);
			$news_yq_playersid = substr($news_yq_playersid,1,strlen($news_yq_playersid));
			$news_is_agree = substr($news_is_agree,1,strlen($news_is_agree));
			$news_invite_time = substr($news_invite_time,1,strlen($news_invite_time));
			if($old_str == $news_yq_playersid) {
				return 2;
			}
			//echo($news_yq_playersid."/n/t");
			//echo($news_is_agree."/n/t");
			//echo($news_invite_time."/n/t");
			
			//exit;
			
			$result = $db->query("SELECT prestige FROM ".$common->tname('player')." WHERE playersid = '".$socialInfo['toplayersid']."' LIMIT 1");
			$rows_prestige = $db->fetch_array($result);
			$temp_dw = explode("|",socialModel::getDiwei($rows_prestige['prestige']));
			
			$update['yq_playersid'] = $news_yq_playersid;
			$update['is_agree'] = $news_is_agree;
			$update['invite_time'] = $news_invite_time;
	        $update['npc'] = $rows['npc'].",".$socialInfo['toplayersid'];
	        $update['npc_name'] = $rows['npc_name'].",".$rows_toplayer['nickname'];
	        $update['npc_points'] = $rows['npc_points'].",".$temp_dw[4];
	        $update['current_points'] = $rows['current_points']+$temp_dw[4];
	        $wherearr['id'] = $rows['id'];
			$common->updatetable('practice',$update,$wherearr);
			
		}else{
			//print_r($rows);
			return 2;
		}
  		
	}
	
	//历练 邀请所有
	static function setPracticeAll($socialInfo) {
		global $common,$db,$mc,$_SGLOBAL,$_g_lang;
		
		// 获取需邀请的玩家ID数组
		$can_invite_array = explode(',', $socialInfo['toplayersid']);
		$can_invite_count = count($can_invite_array);
		
		// 分别邀请数组中的玩家
		for($i = 0; $i < $can_invite_count; $i++) {
			$practiceid = "";
				
			// 判断邀请的玩家id是否是玩家好友
			$friendIDList = roleModel::getTableRoleFriendsInfo($socialInfo['playersid'], 1, true);
			if(!key_exists($can_invite_array[$i], $friendIDList)){
				return 21;
			}
				
			// 判断是否是当天添加的好友
			$friendIDList = roleModel::getTableRoleFriendsInfo($socialInfo['playersid'], 1, true);
			if(key_exists($can_invite_array[$i], $friendIDList)
					&&date('Y-m-d', $friendIDList[$can_invite_array[$i]]['atime']) == date('Y-m-d')){
				return 22;
			}
				
			// 判断不可邀请玩家被传入
			if(!($block = $mc->get(MC.$socialInfo['playersid'] . '_friendBlock'))) {
				$block = null;
				$blockRet = $db->query("SELECT * FROM ".$common->tname('practice_block') . " WHERE playersid = {$socialInfo['playersid']}");
				if(mysql_num_rows($blockRet) != 0) {
					while($tmp = $db->fetch_array($blockRet)) {
						$block[$tmp['toplayersid']] = $tmp['blocktime'];
					}
					$mc->set(MC.$socialInfo['playersid'] . '_friendBlock', $block, 0, 3600);
				}
			}
			if($block != null) {
				$year = date('Y');
				$month = date('m');
				$day = date('d');
				if(isset($block[$can_invite_array[$i]])) {
					$yq_year = date('Y', $block[$can_invite_array[$i]]);
					$yq_month = date('m',$block[$can_invite_array[$i]]);
					$yq_day = date('d', $block[$can_invite_array[$i]]);
					if($yq_year == $year && $yq_month == $month) {
						if($yq_day == $day) {
							return 23;
						}
					}
				}
			}
				
			// 判断历练信息是否合法
			$general = cityModel::getGeneralData($socialInfo['playersid'], 0, $socialInfo['generalid']);
			$pStrict_ret = socialModel::practiceStrict($general[0]['general_level'], $general[0]['understanding_value'], $general[0]['professional_level'], $general[0]['llcs']);
			if($pStrict_ret['status'] != 0) {
				$returnValue['status'] = $pStrict_ret['status'];
				$returnValue['message'] = $pStrict_ret['message'];
				return $returnValue;
			}
				
			$result = $db->query("select * from ".$common->tname('practice')." where generalid = '".$socialInfo['generalid']."' and playersid = '".$socialInfo['playersid']."' and status = 0 LIMIT 1");
			$rows = $db->fetch_array($result);
			$temp_practiveid = $rows['id'];
			if(empty($rows)) {
				$practice['playersid'] = $socialInfo['playersid'];
				$practice['generalid'] = $socialInfo['generalid'];
				$practice['yq_playersid'] = $can_invite_array[$i];
				$agree_arr = serialize(array($can_invite_array[$i]=>0));
				$practice['is_agree'] = $agree_arr;
				$practice['invite_time'] = time();
				$practice['create_time'] = time();
					
				$common->inserttable('practice',$practice);
			}else{
				$yq_arr = explode(',', trim($rows['yq_playersid']));
				if(trim($rows['yq_playersid'] == "")) {
					$yq_arr = array();
				}
					
				// 如果邀请的玩家不在邀请列表中
				if(in_array($can_invite_array[$i], $yq_arr) == false) {
					if(count($yq_arr) == 0) {
						$update['yq_playersid'] = $can_invite_array[$i];
						$agree_arr = serialize(array($can_invite_array[$i]=>0));
						$update['is_agree'] = $agree_arr;
						$update['invite_time'] = $_SGLOBAL['timestamp'];
					}else{
						$update['yq_playersid'] = $rows['yq_playersid'].",".$can_invite_array[$i];
						$agree_arr = unserialize($rows['is_agree']);
						$agree_arr[$can_invite_array[$i]] = 0;
						$update['is_agree'] = serialize($agree_arr);
						$update['invite_time'] = $rows['invite_time'].",".$_SGLOBAL['timestamp'];
					}
					$update['create_time'] = $_SGLOBAL['timestamp'];
					$wherearr['id'] = $temp_practiveid;
					$common->updatetable('practice',$update,$wherearr);
					$practiceid = $temp_practiveid;
						
				}else{ // 邀请的玩家在邀请列表中(不同天的邀请情况)
					$idx = array_search($can_invite_array[$i], $yq_arr);
					$invite_arr = explode(',', $rows['invite_time']);
					$invite_arr[$idx] = $_SGLOBAL['timestamp'];
					$update['invite_time'] = implode(',', $invite_arr);
					$update['create_time'] = $_SGLOBAL['timestamp'];
					$wherearr['id'] = $temp_practiveid;
					$common->updatetable('practice',$update,$wherearr);
					$practiceid = $temp_practiveid;
				}
					
				if(!($block = $mc->get(MC.$socialInfo['playersid'] . '_friendBlock'))) {
					$block = null;
					$blockRet = $db->query("SELECT * FROM ".$common->tname('practice_block') . " WHERE playersid = {$socialInfo['playersid']}");
					if(mysql_num_rows($blockRet) != 0) {
						while($tmp = $db->fetch_array($blockRet)) {
							$block[$tmp['toplayersid']] = $tmp['blocktime'];
						}
						$mc->set(MC.$socialInfo['playersid'] . '_friendBlock', $block, 0, 0);;
					}
				}
				$updatePraBlock['blocktime'] = $_SGLOBAL['timestamp'];
				$wherePraBlock['toplayersid'] = $can_invite_array[$i];
				$wherePraBlock['playersid'] = $socialInfo['playersid'];
				$pra_ret = $db->query("SELECT * FROM ".$common->tname('practice_block') . " WHERE playersid = {$socialInfo['playersid']} and toplayersid = {$can_invite_array[$i]}");
				if(mysql_num_rows($pra_ret) > 0) {
					$common->updatetable('practice_block', $updatePraBlock, $wherePraBlock);
				} else {
					$common->inserttable('practice_block', array('playersid'=>$socialInfo['playersid'], 'toplayersid'=>$can_invite_array[$i], 'blocktime'=>$_SGLOBAL['timestamp']));
				}
				$block[$can_invite_array[$i]] = $_SGLOBAL['timestamp'];
				$mc->set(MC.$socialInfo['playersid'] . '_friendBlock', $block, 0, 0);
			}
			//发送消息
			$roleInfo['playersid'] = $socialInfo['playersid'];
			$roleRes = roleModel::getRoleInfo($roleInfo);
			if (empty($roleRes)) {
				$value = array('status'=>20,'message'=>$_g_lang['social']['no_find_account']);
				return $value;
			}
			$json['playersid'] = $socialInfo['playersid'];
			$json['toplayersid'] = $can_invite_array[$i];
			$json['message'] = array('wjmc1'=>$roleInfo['nickname'],'wjid1'=>$roleInfo['playersid']);
			$json['type'] = 1;
			$json['genre'] = 7;           //历练
			$json['practiceid'] = $practiceid;
			$json['interaction'] = 1;
			$json['tradeid'] = 0;
		
			$result = lettersModel::addMessage($json);
		}
		return 0;		
	}
	
	//历练 邀请好友
	static function setPractice($socialInfo) {
		global $common,$db,$mc,$_SGLOBAL,$_g_lang;
		$practiceid = "";
		
		// 判断邀请的玩家id是否是玩家好友
		$friendIDList = roleModel::getTableRoleFriendsInfo($socialInfo['playersid'], 1, true);
		if(!key_exists($socialInfo['toplayersid'], $friendIDList)){
			return 21;
		}
		
		// 判断是否是当天添加的好友
		$friendIDList = roleModel::getTableRoleFriendsInfo($socialInfo['playersid'], 1, true);
		if(key_exists($socialInfo['toplayersid'], $friendIDList)
				&&date('Y-m-d',$friendIDList[$socialInfo['toplayersid']]['atime']) == date('Y-m-d')){			
			return 22;
		}
				
		// 判断不可邀请玩家被传入
		if(!($block = $mc->get(MC.$socialInfo['playersid'] . '_friendBlock'))) {
			$block = null;
			$blockRet = $db->query("SELECT * FROM ".$common->tname('practice_block') . " WHERE playersid = {$socialInfo['playersid']}");
			if(mysql_num_rows($blockRet) != 0) { 
			   	while($tmp = $db->fetch_array($blockRet)) {
			   		$block[$tmp['toplayersid']] = $tmp['blocktime'];
			   	}
			   	$mc->set(MC.$socialInfo['playersid'] . '_friendBlock', $block, 0, 3600);	
			}
		}
		if($block != null) {
			$year = date('Y');
			$month = date('m');
			$day = date('d');
			if(isset($block[$socialInfo['toplayersid']])) {
				$yq_year = date('Y', $block[$socialInfo['toplayersid']]);
				$yq_month = date('m',$block[$socialInfo['toplayersid']]);
				$yq_day = date('d', $block[$socialInfo['toplayersid']]);
				if($yq_year == $year && $yq_month == $month) {						
					if($yq_day == $day) {
						return 23;
					}
				}
			}
		}
		
		// 判断历练信息是否合法
		$general = cityModel::getGeneralData($socialInfo['playersid'], 0, $socialInfo['generalid']);
		$pStrict_ret = socialModel::practiceStrict($general[0]['general_level'], $general[0]['understanding_value'], $general[0]['professional_level'], $general[0]['llcs']);
		if($pStrict_ret['status'] != 0) {			
			$returnValue['status'] = $pStrict_ret['status'];                                 
			$returnValue['message'] = $pStrict_ret['message'];
			return $returnValue;			
		}
				
		$result = $db->query("select * from ".$common->tname('practice')." where generalid = '".$socialInfo['generalid']."' and playersid = '".$socialInfo['playersid']."' and status = 0 LIMIT 1");
  		$rows = $db->fetch_array($result);
		$temp_practiveid = $rows['id'];
		if(empty($rows)) {
			$practice['playersid'] = $socialInfo['playersid'];
        	$practice['generalid'] = $socialInfo['generalid'];
        	$practice['yq_playersid'] = $socialInfo['toplayersid'];
        	$agree_arr = serialize(array($socialInfo['toplayersid']=>0));
        	$practice['is_agree'] = $agree_arr;
        	$practice['invite_time'] = time();
        	$practice['create_time'] = time();
        	
        	$common->inserttable('practice',$practice);
		}else{
			$yq_arr = explode(',', trim($rows['yq_playersid']));
			if(trim($rows['yq_playersid'] == "")) {
				$yq_arr = array();
			}						
	
			// 如果邀请的玩家不在邀请列表中
			if(in_array($socialInfo['toplayersid'], $yq_arr) == false) {
				if(count($yq_arr) == 0) {
					$update['yq_playersid'] = $socialInfo['toplayersid'];
					$agree_arr = serialize(array($socialInfo['toplayersid']=>0));
	        		$update['is_agree'] = $agree_arr;
	        		$update['invite_time'] = $_SGLOBAL['timestamp'];
				}else{
	        		$update['yq_playersid'] = $rows['yq_playersid'].",".$socialInfo['toplayersid'];
	        		$agree_arr = unserialize($rows['is_agree']);
	        		$agree_arr[$socialInfo['toplayersid']] = 0;	        		
	        		$update['is_agree'] = serialize($agree_arr);
	        		$update['invite_time'] = $rows['invite_time'].",".$_SGLOBAL['timestamp'];	        	
				}
	        	$update['create_time'] = $_SGLOBAL['timestamp'];
	        	$wherearr['id'] = $temp_practiveid;
	        	$common->updatetable('practice',$update,$wherearr);
	        	$practiceid = $temp_practiveid;				
				
			}else{ // 邀请的玩家在邀请列表中(不同天的邀请情况)
				$idx = array_search($socialInfo['toplayersid'], $yq_arr);
				$invite_arr = explode(',', $rows['invite_time']);
				$invite_arr[$idx] = $_SGLOBAL['timestamp'];
				$update['invite_time'] = implode(',', $invite_arr);
				$update['create_time'] = $_SGLOBAL['timestamp'];
				$wherearr['id'] = $temp_practiveid;				
				$common->updatetable('practice',$update,$wherearr);
				$practiceid = $temp_practiveid;				
			}
			
			if(!($block = $mc->get(MC.$socialInfo['playersid'] . '_friendBlock'))) {
				$block = null;
				$blockRet = $db->query("SELECT * FROM ".$common->tname('practice_block') . " WHERE playersid = {$socialInfo['playersid']}");
				if(mysql_num_rows($blockRet) != 0) { 
				   	while($tmp = $db->fetch_array($blockRet)) {
				   		$block[$tmp['toplayersid']] = $tmp['blocktime'];
				   	}
				   	$mc->set(MC.$socialInfo['playersid'] . '_friendBlock', $block, 0, 0);;	
				}
			}
			$updatePraBlock['blocktime'] = $_SGLOBAL['timestamp'];
			$wherePraBlock['toplayersid'] = $socialInfo['toplayersid'];			
			$wherePraBlock['playersid'] = $socialInfo['playersid'];
			$pra_ret = $db->query("SELECT * FROM ".$common->tname('practice_block') . " WHERE playersid = {$socialInfo['playersid']} and toplayersid = {$socialInfo['toplayersid']}");
			if(mysql_num_rows($pra_ret) > 0) {
				$common->updatetable('practice_block', $updatePraBlock, $wherePraBlock);	
			} else {
				$common->inserttable('practice_block', array('playersid'=>$socialInfo['playersid'], 'toplayersid'=>$socialInfo['toplayersid'], 'blocktime'=>$_SGLOBAL['timestamp']));	
			}
			$block[$socialInfo['toplayersid']] = $_SGLOBAL['timestamp'];
			$mc->set(MC.$socialInfo['playersid'] . '_friendBlock', $block, 0, 0);
		}
		//发送消息		
		$roleInfo['playersid'] = $socialInfo['playersid'];
		$roleRes = roleModel::getRoleInfo($roleInfo);
		if (empty($roleRes)) {
		   $value = array('status'=>20,'message'=>$_g_lang['social']['no_find_account']);
		   return $value;
		}			
		$json['playersid'] = $socialInfo['playersid'];
		$json['toplayersid'] = $socialInfo['toplayersid'];
		$json['message'] = array('wjmc1'=>$roleInfo['nickname'],'wjid1'=>$roleInfo['playersid']);
		$json['type'] = 1;
		$json['genre'] = 7;           //历练
		$json['practiceid'] = $practiceid;
		$json['interaction'] = 1;
		$json['tradeid'] = 0;
		//$json = json_encode($json);
			
		$result = lettersModel::addMessage($json);		
		return 0;
	}
	
	//删除修行要求
	static function delPractice($socialInfo) {
		global $common,$db;
		$result = $db->query("SELECT * FROM ".$common->tname('practice')." WHERE playersid = '".$socialInfo['playersid']."' and generalid = '".$socialInfo['generalid']."' and status = 0 LIMIT 1");
		$rows = $db->fetch_array($result);
		$temp_practiveid = $rows['id'];
		if(!empty($rows)) {
			$temp_yq_playersid_dir = explode(",",$rows['yq_playersid']);
			$temp_is_agree_dir = explode(",",$rows['is_agree']);
			$temp_invite_time_dir = explode(",",$rows['invite_time']);
			for($i = 0; $i < count($temp_yq_playersid_dir); $i++) {
				if($temp_yq_playersid_dir[$i] == $socialInfo['toplayersid']) {
					if($temp_is_agree_dir[$i] == 0) {
						socialModel::updateArrData($temp_yq_playersid_dir,$temp_is_agree_dir,$temp_invite_time_dir,count($temp_yq_playersid_dir),$i,$temp_practiveid,$socialInfo);
						return 0;   //正常
					}
				}
			}
			return 3;	//用户已同意
		}else{
			return 2;	//非法请求
		}
	}

	
	//取消历练
	static function cancelPractice($socialInfo) {
		global $common,$db,$mc;
		
		$result = $db->query("SELECT * FROM ".$common->tname('practice')." WHERE playersid = '".$socialInfo['playersid']."' and generalid = '".$socialInfo['generalid']."' and status = 0 LIMIT 1");
		$rows = $db->fetch_array($result);
		if(!empty($rows)) {
			$update_practice['status'] = 2;
			$wherearr_practice['id'] = $rows['id'];
			$common->updatetable('practice',$update_practice,$wherearr_practice);
	
			$update_general['llzt'] = "0";
			$wherearr_general['intID'] = $socialInfo['generalid'];
			$common->updatetable('playergeneral',$update_general,$wherearr_general);
				
			$gen_rows = cityModel::getGeneralData($socialInfo['playersid'],0,$socialInfo['generalid']);
			if($gen_rows <> "") {
				$gen_rows[0]['llzt'] = "0";
     			$newData[$gen_rows[0]['sortid']] = $gen_rows[0];
     			$common->updateMemCache(MC.$socialInfo['playersid'].'_general',$newData);
			}
				
			$update_social['is_practice'] = "0";
			$wherearr_social['playersid'] = $socialInfo['playersid'];
			$common->updatetable('social',$update_social,$wherearr_social);		
		}else{
			$update1['llzt'] = "0";
			$wherearr1['intID'] = $socialInfo['generalid'];
			$common->updatetable('playergeneral',$update1,$wherearr1);
			
			$gen_rows = cityModel::getGeneralData($socialInfo['playersid'],0,$socialInfo['generalid']);
			if($gen_rows <> "") {
				$gen_rows[0]['llzt'] = "0";
  	     		$newData[$gen_rows[0]['sortid']] = $gen_rows[0];
  	     		$common->updateMemCache(MC.$socialInfo['playersid'].'_general',$newData);
			}
			
			$update2['is_practice'] = "0";
			$wherearr2['playersid'] = $socialInfo['playersid'];
			$common->updatetable('social',$update2,$wherearr2);
		}
		
		// 修改历练好友内存
		$friendNotRepeat = $db->query("SELECT t1.playersid as jsid,t1.nickname as mc,t1.sex,t1.player_level as jb FROM ".$common->tname('player') ." t1, ".$common->tname('social_user') ." t2 WHERE   t1.playersid=t2.playersid and t2.type = '1' and t2.parent_playersid = ".$socialInfo['playersid']." order by t1.player_level desc LIMIT 200");
	   	while($fri_result[] = $db->fetch_array($friendNotRepeat));
	   	array_pop($fri_result);
	   	$mc->set(MC.$socialInfo['playersid'].'_'.$socialInfo['generalid'].'_friendNotRepeat', $fri_result, 0, 3600);
	   	
	   	return 0;
	}
	
	//历练减元宝成功
	static function submitLessPractice($socialInfo,&$rwid = '') {
		global $common,$db,$mc,$_SGLOBAL,$_g_lang;
		
		$result = $db->query("SELECT * FROM ".$common->tname('practice')." WHERE playersid = '".$socialInfo['playersid']."' and generalid = '".$socialInfo['generalid']."' and status = 0 LIMIT 1");
		$rows = $db->fetch_array($result);
		$gen_rows = cityModel::getGeneralData($socialInfo['playersid'],0,$socialInfo['generalid']);
		if (empty($gen_rows)) {
			return 20;
		}
		if(!empty($rows)) {
			$agree = $rows['is_agree'];
			$agreeNum = 0;
			if (!empty($agree)) {
				$agreeInfo = unserialize($agree);
				foreach ($agreeInfo as $k=>$v) {
					$agreeNum += intval($v);
				}
			}
			$roleInfo['playersid'] = $socialInfo['playersid'];
			$roleRes = roleModel::getRoleInfo($roleInfo);
			if (empty($roleRes)) {
			   $value = array('status'=>21,'message'=>$_g_lang['social']['no_find_account']);
			   return $value;
			}				
			//$ce = $rows['need_points'] - $rows['current_points'];
			//$xs = $ce;
			//$kf = $ce;
			$tf = $gen_rows[0]['llcs'] + 1;
			$kf = llrs($tf);//llrs($llcs)
			$diffNum = $kf - $agreeNum;
			if($diffNum < 0) {
				return 2;
			}
			/*
			if(toolsModel::deleteBags($socialInfo['playersid'], array(JHL_ITEMID=>$diffNum), DELETE_BAG_NOT_DO) == false) { 
				// 数量不足
				$myItemInfo = toolsModel::getMyItemInfo($socialInfo['playersid']);
				$myJhlGs = 0;
				//print_r($myItemInfo);
				foreach($myItemInfo as $k=>$v) {
					if($v['ItemID'] == JHL_ITEMID) {
						$myJhlGs += $v['EquipCount'];
					}
				}
				return '5|'.$myJhlGs.'|'.$diffNum;
			}*/
			if(intval($roleInfo['silver']) < ($diffNum * bllyp())) {
				//heroCommon::insertLog('5|'.$roleInfo['silver'].'|'.($diffNum * bllyp()));
				return '5|'.$roleInfo['silver'].'|'.$diffNum * bllyp();
			}
			
			/*if ($gen_rows[0]['llcs'] >= ($gen_rows[0]['professional_level'] * 10)) {
				return 3;
			}*/
			// 判断历练信息是否合法
			//$general = cityModel::getGeneralData($socialInfo['playersid'], '', $socialInfo['generalid']);
			$pStrict_ret = socialModel::practiceStrict($gen_rows[0]['general_level'], $gen_rows[0]['understanding_value'], $gen_rows[0]['professional_level'], $gen_rows[0]['llcs']);
			if($pStrict_ret['status'] == 998 && $pStrict_ret['message'] == $_g_lang['social']['general_expr_max']) {
				return 23;
			} else if($pStrict_ret['status'] == 998 && $pStrict_ret['message'] == $_g_lang['social']['general_expr_max_at_lvl']) {
				return 24;
			} else if($pStrict_ret['status'] == 998 && $pStrict_ret['message'] == $_g_lang['social']['bf_30_level_exp_1_per_3']) {
				return 25;
			} else if($pStrict_ret['status'] != 0) {
				return 2;			
			} 
			
			//toolsModel::deleteBags($socialInfo['playersid'], array(JHL_ITEMID=>$diffNum), DELETE_BAG_YES_DO);
			
			$yp = $roleInfo['silver'] - ($diffNum * bllyp());
			$updateRole['silver'] = $roleInfo['silver'] = $yp;
			$common->updatetable('player',"silver = ".$yp,"playersid = '".$socialInfo['playersid']."'");
			$common->updateMemCache(MC.$socialInfo['playersid'],$updateRole);
			
			$update_practice['tool_num'] = $diffNum;
			$wherearr_practice['id'] = $rows['id'];
			$common->updatetable('practice',$update_practice,$wherearr_practice);
			
			/*$yb = $roleInfo['ingot'] - $kf;
			$updateRole['ingot'] = $roleInfo['ingot'] = $yb;
			$common->updatetable('player',"ingot = ".$yb,"playersid = '".$socialInfo['playersid']."'");
			$common->updateMemCache(MC.$socialInfo['playersid'],$updateRole);
	
			$common->updatetable('playergeneral'," understanding_value = understanding_value + 1,llzt = 0,llcs = llcs + 1 ,last_end_ll = ".$_SGLOBAL['timestamp'],"intID = ".$socialInfo['generalid']." and playerid = '".$socialInfo['playersid']."'");
			$gen_rows[0]['llzt'] = "0";
			$gen_rows[0]['last_end_ll'] = $_SGLOBAL['timestamp'];
			$gen_rows[0]['understanding_value'] = $gen_rows[0]['understanding_value'] + 1;
			$gen_rows[0]['llcs'] = $gen_rows[0]['llcs'] + 1;
	  	    $newData[$gen_rows[0]['sortid']] = $gen_rows[0];
	  	    $common->updateMemCache(MC.$socialInfo['playersid'].'_general',$newData);	
				
			$update_practice['status'] = 1;
			$wherearr_practice['id'] = $rows['id'];
			$common->updatetable('practice',$update_practice,$wherearr_practice);
				
			$common->updatetable('social',"is_practice = 0","playersid = ".$socialInfo['playersid']);*/		
							
			return '0|'.$yp.'|'.$diffNum * bllyp();
		}else{
			return 22;
		}
	}
	
	//历练成功
	static function submitPractice($socialInfo,&$rwid = '') {
		global $common,$db,$mc,$_SGLOBAL,$_g_lang;
		$result = $db->query("SELECT * FROM ".$common->tname('practice')." WHERE playersid = '".$socialInfo['playersid']."' and generalid = '".$socialInfo['generalid']."' and status = 0 LIMIT 1");
		$rows = $db->fetch_array($result);
		if(!empty($rows)) {
			$agree = $rows['is_agree'];
			$amount = 0;
			if (!empty($agree)) {
				$agreeInfo = unserialize($agree);
				foreach ($agreeInfo as $k=>$v) {
					$amount += intval($v);
				}
			} else {
				$amount = 0;
			}
			if(($amount + $rows['tool_num'])  >= $rows['invite_num']) {
				
				//$result = $db->query("SELECT * FROM ".$common->tname('playergeneral')." WHERE playerid = '".$socialInfo['playersid']."' and intID = '".$socialInfo['generalid']."' LIMIT 1");
				//$rows_general = $db->fetch_array($result);
				$ginfo = cityModel::getGeneralData($socialInfo['playersid'],0,$socialInfo['generalid']);
				if (empty($ginfo)) {
					return 20;
				}
				
				// 判断历练信息是否合法
				$general = cityModel::getGeneralData($socialInfo['playersid'], '', $socialInfo['generalid']);
				$pStrict_ret = socialModel::practiceStrict($general[0]['general_level'], $general[0]['understanding_value'], $general[0]['professional_level'], $general[0]['llcs']);
				if($pStrict_ret['status'] == 998 && $pStrict_ret['message'] == $_g_lang['social']['general_expr_max']){
					return 23;
				} else if($pStrict_ret['status'] == 998 && $pStrict_ret['message'] == $_g_lang['social']['general_expr_max_at_lvl']) {
					return 24;
				} else if($pStrict_ret['status'] == 998 && $pStrict_ret['message'] == $_g_lang['social']['bf_30_level_exp_1_per_3']) {
					return 25;
				} else if($pStrict_ret['status'] != 0) {
					return 2;			
				} 
				
				//加属性
				//socialModel::addGenProperty($ginfo);
				
				$common->updatetable('playergeneral',"understanding_value = understanding_value + 1,llzt = 0,llcs = llcs + 1 ,last_end_ll = ".$_SGLOBAL['timestamp'],"intID = ".$socialInfo['generalid']." and playerid = '".$socialInfo['playersid']."'");
				$ginfo[0]['llzt'] = "0";
				$ginfo[0]['last_end_ll'] = $_SGLOBAL['timestamp'];
				$ginfo[0]['understanding_value'] = $ginfo[0]['understanding_value'] + 1;
				$ginfo[0]['llcs'] = $ginfo[0]['llcs'] + 1;
	  	     	$newData[$ginfo[0]['sortid']] = $ginfo[0];
	  	     	$common->updateMemCache(MC.$socialInfo['playersid'].'_general',$newData);				
					
				$update_practice['status'] = 1;
				$wherearr_practice['id'] = $rows['id'];
				$common->updatetable('practice',$update_practice,$wherearr_practice);				
				$common->updatetable('social',"is_practice = 0","playersid = ".$socialInfo['playersid']);
				$roleInfo['playersid'] = $socialInfo['playersid'];
				$roleRes = roleModel::getRoleInfo($roleInfo);
				if (empty($roleRes)) {
				   $value = array('status'=>21,'message'=>$_g_lang['social']['no_find_account']);
				   return $value;
				}
				//$roleInfo['rwsl'] = $ginfo[0]['llcs'];	
				//$rwid = questsController::OnFinish($roleInfo,"'llcs'");				
				return 0;
			}else{
				return 26;
			}
		}else{
			return 22;
		}
	}
	
	
	public static function getPracticeInfo1($socialInfo) {
		global $common,$_g_lang;
		$temp_dir = array();
		$yl = "";
		$delsocialInfo = array('playersid'=>$socialInfo['playersid'],'userid'=>$socialInfo['userid'],'generalid'=>$socialInfo['generalid'],'wxdj'=>$socialInfo['wxdj']);
		$result = socialModel::lessSilver($delsocialInfo);
		$temp_dir = explode("|",$result);
		//print_r($temp_dir);
		//exit;
		if(count($temp_dir)>1) {
			$result = $temp_dir[0];
			$yl = $temp_dir[1];
		}		
		/*if ($result == 5) {
			$returnValue['status'] = 68;
			$returnValue['xyxhyp'] = $temp_dir[2];
			$returnValue['message'] = '您需要消耗'.$temp_dir[2].'银票，目前您有'.$temp_dir[1].'银票，市场里可兑换银票';
			$roleInfo['playersid'] = (int)$socialInfo['playersid'];
			roleModel::getRoleInfo($roleInfo);
			$returnValue['yp'] = (int)$roleInfo['silver'];
			return $returnValue;
			//exit;
		} else*/
		if ($result == 4) {
			$returnValue['status'] = 1002;      //understanding_value <= 15
			$returnValue['message'] = $_g_lang['social']['expr_at_times_max'];
			return $returnValue;
			//exit;
		} elseif ($result == 2) {
			$returnValue['status'] = 2;                                 
			$returnValue['message'] = $_g_lang['social']['err_request'];
			return $returnValue;
		} elseif ($result == 10) {
			$returnValue['status'] = 1001;    
			$returnValue['message'] = $_g_lang['social']['general_cd_time_out'];
			$returnValue['sysj'] = $yl;		//冷却时间
			return $returnValue;
		}
		if (!is_numeric($result)) {
			$resultInfo = explode("|",$result);
        	$xhyp =  $resultInfo[2];
		} else {
			$xhyp = 0;
		}
		if(/*$result <> 5 && */$result <> 2) {
			// 判断历练信息是否合法
			$general = cityModel::getGeneralData($socialInfo['playersid'], 0, $socialInfo['generalid']);
			$pStrict_ret = socialModel::practiceStrict($general[0]['general_level'], $general[0]['understanding_value'], $general[0]['professional_level'], $general[0]['llcs']);
			if($pStrict_ret['status'] != 0) {			
				$returnValue['status'] = $pStrict_ret['status'];                                 
				$returnValue['message'] = $pStrict_ret['message'];
				return $returnValue;			
			}

			$result = socialModel::getPracticeInfo($socialInfo);
	
			if ($result == "2") {
				$returnValue['status'] = 2;                                 
				$returnValue['message'] = $_g_lang['social']['err_request'];
			} else {
				$returnValue['status'] = 0;   			                            
				$returnValue['xxinfo'] = $result[0];
				$returnValue['yyqhy'] = $result[1];
				$returnValue['jhl'] = (int)$result['jhl'];
				//print_r($result);
				if($yl > 0) {
					//$returnValue['yp'] = $temp_dir[1];
					//$returnValue['xhyp'] = $temp_dir[2];
					$returnValue['yp'] = (int)$yl;
				}
			}			
		}
 		/*$ydts = guideScript::jsydsj(array('playersid'=>$_SESSION['playersid']),'ypjy',10);
		if (!empty($ydts)) {
			$returnValue['ydts'] = $ydts;
		}*/			
		return $returnValue;
	}
	
	public static function setPractice1($socialInfo) {
		global $common,$db, $_g_lang;
		
		if($socialInfo['toplayersid'] == '') {
			/*socialModel::setPracticeAll($socialInfo);
			$returnValue['status'] = 0;
			$returnValue['jsid'] = "all";*/
			$returnValue['status'] = 2; 
			$returnValue['message'] = $_g_lang['social']['err_request'];
		}else{
			$lllq = genModel::lllqsh($socialInfo['playersid'], $socialInfo['generalid']);
			
			if($lllq['lt'] > 0) {
				$returnValue['status'] = 21; 
				$returnValue['message'] = $_g_lang['social']['expr_cool_wait'];
				return $returnValue;
			}
			$result = socialModel::setPractice($socialInfo);
			if ($result == 2) {
				$returnValue['status'] = 2;
				$returnValue['message'] = $_g_lang['social']['err_request'];
			} elseif ($result == 115) {
				$returnValue['status'] = 1115;
				$returnValue['message'] = $_g_lang['social']['no_to_invert_fri'];
			} elseif($result == 20) {
				$returnValue['status'] = 21;
				$returnValue['message'] = $_g_lang['social']['no_find_account'];
			}  elseif($result == 21) {
				$returnValue['status'] = 21;
				$returnValue['message'] = $_g_lang['social']['invert_no_friend'];
			}  elseif($result == 22) {
				$returnValue['status'] = 21;
				$returnValue['message'] = $_g_lang['social']['today_friend_no_help'];
			} elseif($result == 23) {
				$returnValue['status'] = 21;
				$returnValue['message'] = $_g_lang['social']['repeat_friend_no_help'];
			}else {
				$returnValue['status'] = 0;
				//$returnValue['jsid'] = $socialInfo['toplayersid'];   
				//$socialInfo['zs'] = socialModel::getFriendsNotRepeatCount($socialInfo);
				//$result = socialModel::getFriendsNotRepeat($socialInfo);
				//$returnValue['list'] = $result;	  
				//$returnValue['zs'] = (int)$socialInfo['zs'];
			}
		}
	 	return $returnValue;		
	}
	
	// 全部邀请历练
	public static function setPractice2($socialInfo) {
		global $common,$db,$_g_lang;
	
		if($socialInfo['toplayersid'] == '') {
			/*socialModel::setPracticeAll($socialInfo);
				$returnValue['status'] = 0;
			$returnValue['jsid'] = "all";*/
			$returnValue['status'] = 2;
			$returnValue['message'] = $_g_lang['social']['err_request'];
		}else{
			$lllq = genModel::lllqsh($socialInfo['playersid'], $socialInfo['generalid']);
				
			if($lllq['lt'] > 0) {
				$returnValue['status'] = 21;
				$returnValue['message'] = $_g_lang['social']['expr_cool_wait'];
				return $returnValue;
			}
			$result = socialModel::setPracticeAll($socialInfo);
			if ($result == 2) {
				$returnValue['status'] = 2;
				$returnValue['message'] = $_g_lang['social']['err_request'];
			} elseif ($result == 115) {
				$returnValue['status'] = 1115;
				$returnValue['message'] = $_g_lang['social']['no_to_invert_fri'];
			} elseif($result == 20) {
				$returnValue['status'] = 21;
				$returnValue['message'] = $_g_lang['social']['no_find_account'];
			}  elseif($result == 21) {
				$returnValue['status'] = 21;
				$returnValue['message'] = $_g_lang['social']['invert_no_friend'];
			}  elseif($result == 22) {
				$returnValue['status'] = 21;
				$returnValue['message'] = $_g_lang['social']['today_friend_no_help'];
			} elseif($result == 23) {
				$returnValue['status'] = 21;
				$returnValue['message'] = $_g_lang['social']['repeat_friend_no_help'];
			}else {
				$returnValue['status'] = 0;
				//$returnValue['jsid'] = $socialInfo['toplayersid'];
				//$socialInfo['zs'] = socialModel::getFriendsNotRepeatCount($socialInfo);
				//$result = socialModel::getFriendsNotRepeat($socialInfo);
				//$returnValue['list'] = $result;
				//$returnValue['zs'] = (int)$socialInfo['zs'];
			}
		}
		return $returnValue;
	}
	
	//银票补齐好友
	public static function submitLessPractice1($socialInfo) {
		global $G_PlayerMgr,$common,$_g_lang;
		$rwid = '';
		$lllq = genModel::lllqsh($socialInfo['playersid'], $socialInfo['generalid']);
		if($lllq['lt'] > 0) {
			$returnValue['status'] = 21; 
			$returnValue['message'] = $_g_lang['social']['expr_cool_wait'];
			return $returnValue;
		}
	
		$result = socialModel::submitLessPractice($socialInfo,$rwid);
		$temp_dir = explode("|",$result);
		if(count($temp_dir)>1) {
			$result = $temp_dir[0];
		}
		if ($result == 2) {
			$returnValue['status'] = 2; 
			$returnValue['message'] = $_g_lang['social']['err_request'];
		} elseif ($result == 5) {
			$returnValue['status'] = 68;
			$_message = sprintf($_g_lang['social']['exchange_silver_prompt'], $temp_dir[2], $temp_dir[1]);
			$returnValue['message'] = $_message;
			$returnValue['xyxhyp'] = $temp_dir[2];
			/*$returnValue['message'] = '您需要消耗'.$kyb.'元宝，目前您有'.$yb.'元宝，请先充值？';
			$roleInfo['playersid'] = $socialInfo['playersid'];
			roleModel::getRoleInfo($roleInfo);
			$returnValue['yb'] = (int)$roleInfo['ingot'];*/
		} elseif ($result == 3) {
			$returnValue['status'] = 21; 
			$returnValue['message'] = $_g_lang['social']['expr_at_times_max'];
		} elseif ($result == 20) {
			$returnValue['status'] = 21;
			$returnValue['message'] = $_g_lang['social']['expr_general_no_find'];
		} elseif ($result == 22) {
			$returnValue['status'] = 21;
			$returnValue['message'] = $_g_lang['social']['expr_info_no_find'];
		}  elseif ($result == 23) {
			$returnValue['status'] = 21;
			$returnValue['message'] = $_g_lang['social']['general_expr_max'];
		} elseif ($result == 24) {
			$returnValue['status'] = 21;
			$returnValue['message'] = $_g_lang['social']['general_expr_max_at_lvl'];
		}  elseif ($result == 25) {
			$returnValue['status'] = 21;
			$returnValue['message'] = $_g_lang['social']['bf_30_level_exp_1_per_3'];
		} else {
			/*$getInfo = array();
			$getInfo['playersid'] = $socialInfo['playersid'];
			$getInfo['userid'] = $socialInfo['userid'];
			$ginfo = cityModel::getGeneralList($getInfo, 0, true ,$socialInfo['generalid']);  //cityModel::getGeneralList ( $getInfo, 0, true ,$ngid);  
			*/
			$returnValue['status'] = 0;
			$returnValue['xhyp'] = $temp_dir[2];
			$returnValue['yp'] = $temp_dir[1];
			
			//完成历练
			$rwid = '';
			$ret = socialModel::submitPractice($socialInfo, $rwid);
			$getInfo = array();
			$getInfo['playersid'] = $socialInfo['playersid'];
			$getInfo['userid'] = $socialInfo['userid'];
			$ginfo = cityModel::getGeneralList($getInfo, 0, true ,$socialInfo['generalid']);
			$returnValue['ginfo'] = $ginfo['generals'];   
			$returnValue['gid'] = $socialInfo['generalid']; 
			$returnValue['cs'] = $cjInfo['ll'] = $returnValue['ginfo'][0]['llcs'];  			
			$roleInfo['playersid'] = $socialInfo['playersid'];
			achievementsModel::check_achieve($roleInfo['playersid'],$cjInfo,array('ll'));
			$roleRes = roleModel::getRoleInfo($roleInfo);
			if (empty($roleRes)) {
			   $value = array('status'=>21,'message'=>$_g_lang['social']['no_find_account']);
			   return $value;
			}
			$roleInfo['rwsl'] = $returnValue['cs']; 
			if ($returnValue['cs'] >= 20) {
				$roleInfo['lles'] = $_SESSION['lles'] = $_SESSION['lles'] + 1;				
			}
			if ($returnValue['cs'] >= 30) {
				$roleInfo['llss'] = $_SESSION['llss'] = $_SESSION['llss'] + 1;				
			}
			if ($returnValue['cs'] >= 40) {
				$roleInfo['llmax'] = $_SESSION['llmax'] = $_SESSION['llmax'] + 1;				
			}						
			$rwid = questsController::OnFinish($roleInfo,"'llcs','ypll','llwj'");   //处理是否完成武将历练任务    							
			if (!empty($rwid)) {
				$returnValue['rwid'] = $rwid;
			}    
			$player = $G_PlayerMgr->GetPlayer($socialInfo['playersid']);
			$roleInfo = $player->baseinfo_;            	 
			/*$xwzt_18 = substr($roleInfo['xwzt'],17,1); 
			if ($xwzt_18 == 0) {
				$updateRole['xwzt'] = substr_replace($roleInfo['xwzt'],'1',17,1);
				$common->updatetable('player',$updateRole,array('playersid'=>$roleInfo['playersid']));
				$common->updateMemCache(MC.$roleInfo['playersid'],$updateRole);
			}	*/		
			//$returnValue['ginfo'] = $ginfo['generals'];
			//$bagDataInfo = toolsModel::getAllItems($socialInfo['playersid']);
			//$returnValue['list'] = $bagDataInfo['list'];
			/*$returnValue['yb'] = (int)$yb;
			$returnValue['kyb'] = (int)$kyb;
			if (!empty($rwid)) {
				$returnValue['rwid'] = intval($rwid);
			}
			$returnValue['gid'] = $socialInfo['generalid'];     
			*/	
			/*$roleInfo['playersid'] = $socialInfo['playersid'];
			roleModel::getRoleInfo($roleInfo);
			$rwid = questsController::OnFinish($roleInfo,"'llcs'");
			if (!empty($rwid)) {
				if (!empty($rwid)) {
					$returnValue['rwid'] = $rwid;
				} 
			}	*/
		}
		return $returnValue;		
	}
	
	/**
	 * 
	 * 获得仇人列表
	 * @param array $socialInfo
	 * @param int $zl   如果有就说明是为了查询领地不查仇人
	 * @param int $tuid 如果查看别人的领地就有此值
	 */
	public static function getBlacklist1($playersid, $zl=null, $tuid=null, $page=null) {
		global $_g_lang;
		$return_result = array();
		// 如果查看别人的领地就更换用户id
		$roleInfo['playersid'] = $playersid;
		if(isset($tuid)) {
			$roleInfo['playersid'] = $tuid;	
		} 
		$roleRes = roleModel::getRoleInfo($roleInfo);
		if (empty($roleRes)) {
		   $value = array('status'=>3,'message'=>$_g_lang['social']['no_find_account']);
		   return $value;
		}		
		// 获得查询页值和是否查询占领
		$page = isset($page)?$page:0;
		$zl = isset($zl)?$zl:'';
		
		$result = socialModel::getBlacklist($roleInfo['playersid'], $zl);
		
		$zs = count($result);
		$curPage = $page;
		$totalPage = ($zs%6) >0 ? (int)($zs/6)+1 : (int)($zs/6);
		if($curPage > $totalPage) {
			$curPage += 1;
		}
		$_start = ($page-1) * 6;
		$_end = 0;
		if($_start + 5 < $zs) {
			$_end = $_start + 6;
		}else{
			$_start = ($page == 1) ? 0 : ($page-1) * 6;
			$_end = $zs;
		}			
		if($zl == 1) {
			$_start = 0;
			$_end = $zs;
		}
		if($zs == 0) {
			$curPage = 1;
		}
		for($i = $_start; $i < $_end; $i ++) {
			$return_result[] = $result[$i];
		}
		if($zl == 1) {
			if($zs < 12) {
				for($j = 0 ; $j < (12-$zs); $j++ ) {
					$return_result[] = array('zt'=>2);
				}
			}
			for($n = 0 ; $n < count($return_result); $n++ ) {
				if($n < $roleInfo['ld_level']) {
					if(!empty($return_result[$n]['jsid'])) {
						$return_result[$n]['zt'] = 0;
					} else {
						$return_result[$n]['zt'] = 1;
					}
				}
			}
		}
		
		$returnValue['status'] = 0;                          //返回成功状态投其所好啊 
		$returnValue['list'] = $return_result;
		$returnValue['curPage'] = $curPage;
		$returnValue['totalPage'] = $totalPage;
		
		return $returnValue;
	}
	
	
	//偷将
	public static function jgtj($playersid,$gid,$tuid) {
		global $common,$db,$mc,$_SGLOBAL,$_g_lang;
		$isCheck = false;
		$newGid = 0;
		$myRoleInfo['playersid'] = $playersid;
		$roleRes = roleModel::getRoleInfo($myRoleInfo);
		if (empty($roleRes)) {
		   $value = array('status'=>3,'message'=>$_g_lang['social']['no_find_account']);
		   return $value;
		}		
		$heRoleInfo['playersid'] = $tuid;
		$roleRes2 = roleModel::getRoleInfo($heRoleInfo,false);
		if (empty($roleRes2)) {
		   $value = array('status'=>3,'message'=>$_g_lang['social']['no_find_account']);
		   return $value;
		}	
		if ($heRoleInfo['is_reason'] == 1) {
			$value = array('status'=>21,'message'=>$_g_lang['social']['account_deny']);
			return $value;
		}		
		if($heRoleInfo['player_level'] < 10 || $myRoleInfo['player_level'] < 10) {
			$value['status'] = 21;
			$value['message'] = $_g_lang['social']['lvl_less_10_dnt_steal'];
			return $value;
		}
		$is_hy = socialModel::is_hy($playersid,$tuid);
		if($heRoleInfo['aggressor_playersid'] <> $playersid && $heRoleInfo['end_defend_time'] > $_SGLOBAL['timestamp'] && $is_hy == false) {
			$value['status'] = 21;
			$value['message'] = $_g_lang['social']['at_occupy_no_to_recruit'];
			return $value;
		}
		if ($heRoleInfo['aggressor_playersid'] == $playersid && $heRoleInfo['end_defend_time'] < $_SGLOBAL['timestamp']) {
			$value['status'] = 100;
			$value['message'] = $_g_lang['social']['need_occupy_to_recruit'];
			return $value;			
		}
		
		if ($heRoleInfo['aggressor_playersid'] <> $playersid  && $heRoleInfo['end_defend_time'] > $_SGLOBAL['timestamp'] && $is_hy == true) {
			$value['status'] = 100;
			$value['message'] = $_g_lang['social']['need_save_to_see'];
			return $value;			
		}
		
	    //并发控制
    	$bfkz = heroCommon::bfkz(MC.$tuid.'_zltj',3,$_g_lang['social']['other_at_steal']);
    	if ($bfkz != 'go') {
    		$value['status'] = 22;
    		$value['message'] = $bfkz;
    		return $value;
    	}				
		$getInfo = array();
		$getInfo['playersid'] = $playersid;
		$getInfo['tuid'] = $tuid;
		$getInfo['gid'] = $gid;
		$showValue = cityModel::hireGeneral($getInfo,true);		
		if($showValue['status'] ==0) {
			$generalValue = cityModel::getGeneralList($getInfo);
			$rwid = questsController::OnFinish ( $myRoleInfo, "'tqjl'" );
			if (! empty ( $rwid )) {
				if (! empty ( $rwid )) {
					$value ['rwid'] = $rwid;
				}
			}			
			$tjValue = $showValue ['generals'];
			//$value['generals'] = $showValue['generals'];
			$garray = array();
			foreach ($generalValue['generals'] as $gValue) {
				if ($gValue['gid'] == $tjValue[0]['gid']) {
					$garray[] = $gValue;
				} else {
					$garray[] = array('gid'=>$gValue['gid'],'px'=>$gValue['px']);
				}
			}
			//$value ['generals'] = $generalValue['generals'];
			$value ['generals'] = $garray;
			//$value ['hirableGeneral'] = $showValue ['hirableGeneral'];
			$value ['nextUpdate'] = $showValue ['nextUpdate'];
			$value ['tjbhsj'] = $showValue ['tjbhsj'];
			$value ['tq'] = $showValue ['tq'];
			if (isset($showValue ['yp'])) {			
				$value ['yp'] = $showValue ['yp'];
				$value ['xhyp'] = $showValue ['xhyp'];
			}
			$value ['id'] = $showValue['id'];
			$value ['xm'] = $showValue['xm'];
			$value ['tf'] = $showValue['tf'];
			$value ['xhtq'] = $showValue ['xhtq'];
			//发信件
			$json['playersid'] = $playersid;
			$json['toplayersid'] = $tuid;
			$json['message'] = array('wjmc1'=>$myRoleInfo['nickname'],'wjid1'=>$myRoleInfo['playersid'],'wjwjmc1'=>$tjValue[0]['xm'],'wjwjid1'=>$tjValue[0]['gid']);
			$json['type'] = 1;
			$json['genre'] = 6;
			$json['interaction'] = 1;
			$json['is_passive'] = '';
			$json['tradeid'] = 0;
			//$json = json_encode($json);    
			$result = lettersModel::addMessage($json);
			$value['status'] = 0;
		} elseif ($showValue['status'] == 68) {
			$value = $showValue;
		} else {
			$value['status'] = $showValue['status'];
			$value['message'] = $showValue['message'];
		}
		return $value;
	}
	
	//发送加好友申请
	public static function jhysq($playersid,$tuid) {
		global $common,$_SC,$mc,$_g_lang;
		$return_nickname = "";
		$abc = 0;
		if (empty($playersid) || empty($tuid)) {
			$value['status'] = 100;
			$value['message'] = $_g_lang['social']['err_request'];
			return $value;
		}
		
		// 检测对象是否封禁
		$tuInfo['playersid'] = $tuid;
		roleModel::getRoleInfo($tuInfo);
		if(empty($tuInfo)){
			$returnValue['status']  = 25;
			$returnValue['message'] = $_g_lang['social']['err_pinfo'];
			return $returnValue;
		}
		if(1==$tuInfo['is_reason']){
			$returnValue['status']  = 21;
			$returnValue['message'] = $_g_lang['social']['account_deny'];
			return $returnValue;
		}
		
		// 检查好友数量是否超上限
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
		if(empty($roleInfo)){
			$returnValue['status']  = 26;
			$returnValue['message'] = $_g_lang['social']['err_pinfo'];
			return $returnValue;
		}		
		if(!socialModel::checkFriendLimit($roleInfo)){
			$vip_next = $roleInfo['vip'] + 1;
			$_message = sprintf($_g_lang['social']['friend_num_limit'][0], $vip_next);
			$message = $vip_next>VIP_LEVEL_MAX?$_g_lang['social']['friend_num_limit'][1]:$_message;
			
			$value['status'] = 1021;
			$value['message'] = $message;
			return $value;
		}
		
		// 检查是否发起过邀请
		if(!socialModel::is_oneDayCount($playersid,$tuid,1,2)) {
			$value['status'] = 21;
			$value['message'] = $_g_lang['social']['friend_invert_again'];
			return $value;
		}
				
		//发送消息
		$json = array();
		$json['playersid'] = $playersid;
		$json['toplayersid'] = $tuid;
		$json['message'] = array('wjmc1'=>$roleInfo['nickname'],'wjid1'=>$playersid);
		$json['type'] = 1;
		$json['genre'] = 2;
		$json['uc'] = 0;
		$json['interaction'] = 1;
		//$json = json_encode($json);
		$result = lettersModel::addMessage($json);
		$value['status'] = 0;
		
		$rwid = questsController::OnFinish ( $roleInfo, "'jhy'" );
	        	
		if (! empty ( $rwid )) {
			$value ['rwid'] = $rwid;
		} 
    	/*$xwzt_22 = substr($roleInfo['xwzt'],21,1);  //完成招募武将行为
		if ($xwzt_22 == 0) {
			$updateRoleWhere['playersid'] = $playersid;
			$updateRole['xwzt'] = substr_replace($roleInfo['xwzt'],'1',21,1);
			$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
			$common->updateMemCache ( MC . $playersid, $updateRole );				
		} */				
	
    	//完成引导脚本
		/*$xyd = guideScript::wcjb($roleInfo,'ydjhy',7,6);	
		if ($xyd !== false) {
			$value['ydts'] = $xyd;
		}*/	
		$tInfo['playersid'] = $tuid;
		//guideScript::jsydsj($tInfo,'tyhy',6,1);		
		return $value;
	}
	
	/**
	 * 判能否继续加好友
	 *
	 * @param array $playerInfo			要验证的玩家信息
	 * @return boolean					true表示能够加好友
	 */
	static function checkFriendLimit($playerInfo) {
		global $db,$common;
		
		$friendIdList = roleModel::getTableRoleFriendsInfo($playerInfo['playersid']);
		
		$friendLimit = vipFriendLimit($playerInfo['vip']);

		return count($friendIdList)<$friendLimit?true:false;
	}
	
	//处理好友请求
	public static function cljhysq($playersid,$sqid,$page,$xxlx,$tuid) {
		global $common,$_SC,$db,$mc,$_g_lang;
		
		$selfInfo['playersid'] = $playersid;
		if(!$roleRes = roleModel::getRoleInfo($selfInfo)){
			$returnValue['status']  = 26;
			$returnValue['message'] = $_g_lang['social']['err_pinfo'];
			return $returnValue;
		}
		if (empty($roleRes)) {
		   $value = array('status'=>3,'message'=>$_g_lang['social']['no_find_account']);
		   return $value;
		}
		// 检查自己的好友上限
		if(!socialModel::checkFriendLimit($selfInfo)) {
			$vip_next = $selfInfo['vip'] + 1;
			$value['status'] = 21;

			$next_fri_num = vipFriendLimit($vip_next);
			$now_fri_num = vipFriendLimit($selfInfo['vip']);
			if($now_fri_num < $next_fri_num){
				$value['message'] = sprintf($_g_lang['social']['friend_num_limit'][0], $vip_next);
			}else{
				$value['message'] = $_g_lang['social']['friend_num_limit'][1];
			}

			return $value;
		}
		
		// 检查对方好友上限
		$roleInfo['playersid'] = $tuid;
		if(!$roleRes2 = roleModel::getRoleInfo($roleInfo)){
			$returnValue['status']  = 26;
			$returnValue['message'] = $_g_lang['social']['err_pinfo'];
			return $returnValue;
		}
		if (empty($roleRes2)) {
			$value = array('status'=>1001,'message'=>$_g_lang['social']['no_find_account']);
			$letterList       = lettersModel::deleteLetters($sqid,$playersid,$page,$xxlx);
			$value['xxlist']  = $letterList['xxlist'];
			return $value;
		}	
		if(!socialModel::checkFriendLimit($roleInfo)) {
			$value['status']  = 1001;
			$value['message'] = $_g_lang['social']['oppo_friend_limit'];
			$letterList       = lettersModel::deleteLetters($sqid,$playersid,$page,$xxlx);
			$value['xxlist']  = $letterList['xxlist'];
			return $value;
		}
		
		// 如果能够成功删除好友信件那么就可以添加好友
		lettersModel::deleteLetters($sqid,$playersid,$page,$xxlx);
		$delRow = $db->affected_rows();
//		if($delRow <= 0 ) {
//			$value['status'] = 100;
//			$value['message'] = '邀请不存在';
//			return $value;
//		}
		
		// 互相加为好友
		socialModel::addFriendSocial($playersid, $tuid);
		
		$returnValue = lettersModel::getMessageList($playersid, $xxlx, $page);
		$showValue['xxlist'] = $returnValue['xxlist'];
		
		
		$showValue['status'] = 0;
		$showValue['dfmc']   = $roleInfo['nickname'];
		$showValue['zys']    = $returnValue['zys'];
		$showValue['page']   = $returnValue['page'];
		return $showValue;
	}
	
	/**
	 * 开好友宝箱
	 *
	 * @param int $playersid		开宝箱玩家id
	 * @param int $tuid				被开玩家id
	 * @return unknown
	 */
	public static function openBox($playersid,$tuid) {
		global $common, $db, $mc, $_SGLOBAL, $G_PlayerMgr,$_g_lang;
		$roleInfo['playersid'] = $playersid;
		if(!roleModel::getRoleInfo($roleInfo)){
			$returnValue['status']  = 26;
			$returnValue['message'] = $_g_lang['social']['err_pinfo'];
			return $returnValue;
		}
		cityModel::resourceGrowth($roleInfo);
		
		$vipLevel = $roleInfo['vip'];
		$vip_cs = vipcs($vipLevel);
		
		// 检测对象是否封禁
		$tuInfo['playersid'] = $tuid;
		if(!roleModel::getRoleInfo($tuInfo)){
			$returnValue['status']  = 26;
			$returnValue['message'] = $_g_lang['social']['err_pinfo'];
			return $returnValue;
		}
		if(1==$tuInfo['is_reason']){
			$returnValue['status']  = 21;
			$returnValue['message'] = $_g_lang['social']['account_deny'];
			return $returnValue;
		}
				
		$result = socialModel::is_Box($playersid,$tuid,$roleInfo['boxOpen'],$roleInfo['boxTime']);
		
		// 获得开宝箱次数
		$openTimes = count(explode(',', trim($roleInfo['boxOpen'], ',')));
		
		// 开宝箱次数到了后提示下级可增加次数和到下级需要充值金额
		if(!(defined('PRESSURE_TEST_DEBUG') && isset($_GET['debug']))){
			if($result == 0 || $openTimes>=$vip_cs) {
				$value['status'] = 1025;
				$vip_next_cs = vipcs($vipLevel + 1);
				if($vip_next_cs !== false){
					$value['xjsx'] = $vip_next_cs - $vip_cs;
					$vipxx = cityModel::vipxx($playersid, $roleInfo['userid']);
					$value['xjcz'] = $vipxx['xjcz'];
				}
				return $value;
			}
		}
		$rand_tq = bxsz(1);   //铜钱 0.7
		$rand_jl = bxsz(2);   //军粮 0.2
		$rand_cl = bxsz(3);   //材料 0.1
		$rand = rand(0,99);
		// 开宝箱内容
		$itemList = array(0=>array('id'=>20009, 'mc'=>$_g_lang['social']['diamond_fragment']));
		$getIndex = 0;
		if ($rand <= $rand_cl[1]) {
			$gifts = 'item';
			$amount = $rand_cl[0];
		} elseif($rand <= $rand_jl[1]) {
			$gifts = 'food';
			$amount = $rand_jl[0];
		} elseif($rand > $rand_jl[1]) {
			$gifts = 'coins';
			$amount = $rand_tq[0];
		}
		//roleModel::getRoleInfo ( $roleInfo );
		if ($gifts == 'coins' || $gifts == 'food') {
			$updateMem1 = $roleInfo [$gifts] + $amount;
			$updateMem [$gifts] = $updateMem1;
			if ($gifts == 'food') {
				$value['jl'] = floor($roleInfo ['food'] + $amount);
				$value['hqjl'] = $amount;
			}
			elseif($gifts == 'coins') {
				if(($upLimit = $updateMem1 - COINSUPLIMIT) > 0){
					$updateMem[$gifts] = COINSUPLIMIT;
					$amount -= $upLimit;
				}
				$value['tq'] = $updateMem[$gifts];
				$value['hqtq'] = $amount;
			}  
			$updateMem ['boxOpen'] = $roleInfo ['boxOpen'] . "," . $tuid;
			$common->updateMemCache ( MC . $playersid, $updateMem );
			$common->updatetable ( 'player', $gifts . " = " . $updateMem1 . ", boxOpen = '" . $updateMem ['boxOpen'] . "'", "playersid = " . $playersid );
			
		} elseif($gifts == 'item') {
			// 20009金刚石碎片
			$player = $G_PlayerMgr->GetPlayer($playersid);
			$status = $player->AddItems(array($itemList[$getIndex]['id']=>1));
			if(false === $status) {
				$value['status']  = 1021;
				$value['message'] = $_g_lang['social']['pack_full_openbox'];
				return $value;
			}
			$updateMem ['boxOpen'] = $roleInfo ['boxOpen'] . "," . $tuid;
			$common->updateMemCache ( MC . $playersid, $updateMem );
			$common->updatetable ( 'player', $updateMem, "playersid = " . $playersid );
			
			$value['mc'] = $itemList[$getIndex]['mc'];
			$value['sl'] = $amount;
			$value['list'] = $player->GetClientBag();
		}
		$value['status'] = 0;
		return $value;
	}
	
	/**
	 * 索要礼物
	 *
	 * @param int $playersid				玩家id
	 * @param id $clid						索要界面中的序号
	 * @param int $tuid						被索要的玩家id
	 * @return array
	 */
	public static function sylw($playersid,$clid,$tuid) {
		global $common,$db,$_SGLOBAL,$_SC,$_g_lang;
		$social_tools = get_social_tools();
		
		if (empty($playersid) || empty($tuid)) {
			$value['status'] = 100;
			$value['message'] = $_g_lang['social']['err_request'];
			return $value;
		}

		$selfInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($selfInfo);
		if(empty($selfInfo)){
			$returnValue['status']  = 26;
			$returnValue['message'] = $_g_lang['social']['err_pinfo'];
			return $returnValue;
		}
		
		$friendIDList = roleModel::getTableRoleFriendsInfo($playersid, 1, true);
		$rIdList = explode(',', $tuid);
		foreach($rIdList as $tuPid){
			$tuPid = intval($tuPid);
			// 检测对象是否封禁
			$tuInfo['playersid'] = $tuPid;
			roleModel::getRoleInfo($tuInfo);
			if(empty($tuInfo)){
//				$returnValue['status']  = 26;
//				$returnValue['message'] = '帐号数据出错';
				continue;
			}
			if(1==$tuInfo['is_reason']){
//				$returnValue['status']  = 21;
//				$returnValue['message'] = '此帐号已被暂时冻结';
				continue;
			}
			
			// 双方等级十级以上
			if(!(defined('PRESSURE_TEST_DEBUG') && isset($_GET['debug']))){
				if($tuInfo['player_level']<10
				||$selfInfo['player_level']<10){
//					$value['status'] = 23;   //非法的客户端请求
//					$value['message'] = '非法请求的等级';
						continue;
				}
			}
			
			// 如果双方关系是当天添加就不能送礼不能索要
			if(!(defined('PRESSURE_TEST_DEBUG') && isset($_GET['debug']))){
				if(key_exists($tuPid, $friendIDList)
				&&date('Y-m-d',$friendIDList[$tuPid]['atime']) == date('Y-m-d')){
//					$value['status'] = 639;   //非法的客户端请求
//					$value['message'] = '新添加的好友当天无法互相送礼!';
						continue;
				}
				
				if(!key_exists($tuPid, $friendIDList)){
//					$value['status'] = 24;
//					$value['message'] = '用户已解除好友关系，不能索礼';
					continue;
				}
				
				if(socialModel::is_TodaySy($playersid, $tuPid) != 1){
//					$value['status'] = 1100;
//					$value['message'] = '今天您已向该用户索要过礼物';
					continue;
				}
			}
			
			if(intval($clid) > 10) {
//				$value['status'] = 100;
//				$value['message'] = '非法请求';
				continue;
			}
			$sciItemInfo = $social_tools[intval($clid)];
			
			$roleInfo = $selfInfo;
//			$roleInfo['playersid'] = $playersid;
//			if(!roleModel::getRoleInfo($roleInfo)){
//				$returnValue['status']  = 26;
//				$returnValue['message'] = '帐号数据出错';
//				return $returnValue;
//			}
			
			$social_trade['type'] = 3;
			$social_trade['fromplayersid'] = $playersid;
			$social_trade['toplayersid'] = $tuPid;
			$social_trade['gift_type'] = $sciItemInfo['id'];//赠送的材料id
			$social_trade['create_time'] = time();
			
			$tradeid = $common->inserttable('social_trade',$social_trade);
			
		    // 更新缓冲数据
		    $tradeInfo = array('id'=>$tradeid,
		    					'frompid'=> $social_trade['fromplayersid'],
		    					'topid'  => $social_trade['toplayersid'],
		    					'type'   => $social_trade['type'],
		    					'itemid' => $social_trade['gift_type'],
		    					'status' => 0,
		    					'ctime'  => $social_trade['create_time']);
		    					
		    socialModel::modifyMemTradeInfo($playersid, $tradeInfo);
			
			// 构建消息信息
			//$itemInfo = toolsModel::getItemInfo($itemId);
			$syInfo = array('lwmc'=>$sciItemInfo['mc'], 'lwsl'=>1, 'wjmc1'=>$roleInfo['nickname'], 'wjid1'=>$playersid);
			
			//发送消息
			$json['playersid'] = $playersid;
			$json['toplayersid'] = $tuPid;
			$json['message'] = $syInfo;
			$json['type'] = 1;
			$json['genre'] = 11;           //索要送礼
			$json['uc'] = 0;
			$json['tradeid'] = $tradeid;
			$result = lettersModel::addMessage($json);//送礼
		}
		
		$value['status'] = 0;
		return $value;
	}
	
	//今天是否已发送历练邀请
	public static function is_oneDayPractice() {
		global $common,$db,$_SGLOBAL;
		$dataInfo = date("Y-m-d H:i:s",$_SGLOBAL['timestamp']);
		jtsjc($dataInfo);
		$result = $db->query("SELECT * FROM ".$common->tname('letters')." WHERE practiceid <> 0 and playersid = '".$toplayersid."' and create_time >= '".$date_now1."' and create_time <= '".$date_now2."' and type = ".$type." and genre = ".$genre."  LIMIT 1");
		$rows = $db->fetch_array($result);
		if ($rows['id'] > 0) {
			return 0;
		}else{
			return 1;
		}
	}
	
	/**
	 * 判断是不是好友关系
	 *
	 * @param int $playersid		目标玩家id
	 * @param int $tuid				对比玩家id
	 * @return boolean
	 */
	public static function is_hy($playersid,$tuid) {
		$friendIdList = array_keys(roleModel::getTableRoleFriendsInfo($playersid, 1, true));
		if(key_exists($tuid, $friendIdList)){
			return true;
		}
		else{
			return false;
		}
	}
	
	/**
	 * 修改玩家好感度
	 *
	 * @param int $playersid		好感度受影响的玩家id
	 * @param int $tuid				做出影响行为的好友id
	 * @param int $feel				累加的好感度
	 */
	public static function addFriendFeel($playersid, $tuid, $feel){
		global $common, $mc;
		$mutexKey = 'friendIdlist_'.$playersid;
		// 对玩家好友id列表的操作加锁
		$common->userMutex($mutexKey);
		
		// 获取好感度并修改
		$friendIdList = roleModel::getTableRoleFriendsInfo($playersid, 1, true);
		if(key_exists($tuid, $friendIdList)){
			$friendIdList[$tuid]['feel'] += $feel;
			socialModel::updateMemCacheFriends($playersid, $friendIdList);
			
			// 修改对应数据库
			$updateArr['feel']            = $friendIdList[$tuid]['feel'];
			$whereArr['parent_playersid'] = $playersid;
			$whereArr['playersid']        = $tuid;
			$whereArr['type']             = 1;
			$common->updatetable('social_user', $updateArr, $whereArr);
		}
		
		// 关闭锁
		$common->unLockMutex($mutexKey);
	}
	
	/**
	 * 按昵称或playersid查找用户
	 * @author kknd li
	 *
	 * @param int $playersid
	 * @param int|string $tun		对方昵称/pid（君主id）
	 * @param int $type				搜索类型  0按名字  1按pid
	 * @param int $page				页数  从1开始  type=0时需要
	 */
	public static function sslj($playersid, $tun, $type, $page=0){
		global $common, $db;
		$findPlayerList = array();
		// 按照playersid查找流程
		if(1==$type){
			$tun = intval($tun);
			
			// 不能查找到自己
			if($tun == $playersid){
				$returnValue['status']  = 1001;
				return $returnValue;
			}
			
			$roleInfo['playersid'] = $tun;
			$getInfoOk = rolemodel::getRoleInfo($roleInfo);
			
			// 判断是否找到对应玩家
			if(!$getInfoOk){
				$returnValue['status']  = 1001;
				return $returnValue;
			}
			$findPlayerList[] = $roleInfo;
		}
		
		$totalPage = 0;
		$curpage   = 0;
		// 按照昵称来查找
		if(0 == $type){
			$tun = urldecode($tun);
			$whereStr = " nickname='".mysql_escape_string($tun)."' and playersid <> {$playersid}";
			$countSql = "select count(1) a_count from ".$common->tname('player')." where {$whereStr}";
			$result = $db->query($countSql);
			$row = $db->fetch_array($result);
			$count = $row['a_count'];
			
			// 判断是否能查找到玩家
			if(0 >= $count){
				$returnValue['status']  = 1001;
				return $returnValue;
			}
			
			// 分页处理
			$pageRCount = 6;
			$totalPage  = ceil($count/$pageRCount);
			$page       = abs($page);
			$page       = 0 == $page?1:$page;
			$curpage    = $page>$totalPage?$totalPage:$page;
			$starRow    = ($page-1)*$pageRCount;
			
			// 获得玩家信息
			$sltSql = "select * from ".$common->tname('player')." where {$whereStr}";
			$sltSql .= " limit {$starRow}, {$pageRCount}";
			$result = $db->query($sltSql);
			while($row=$db->fetch_array($result)){
				$findPlayerList[] = $row;
			}
		}
		
		// 格式化最后结果并输出
		$plist = array();
		$friendList = roleModel::getTableRoleFriendsInfo($playersid, 1, true);
		foreach($findPlayerList as $playerInfo){
			$row = array();
			$row['mc']    = $playerInfo['nickname'];
			$row['jsid']  = $playerInfo['playersid'];
			$row['dj']    = $playerInfo['player_level'];
			$row['sex']   = $playerInfo['sex'];
			$row['vip']   = $playerInfo['vip'];
			if(key_exists($playerInfo['playersid'], $friendList)){
				$row['hy'] = 1;
			}else{
				$row['hy'] = 0;
			}
			
			$plist[] = $row;
		}
		
		$returnValue['status']    = 0;
		$returnValue['curpage']   = $curpage;
		$returnValue['totalPage'] = $totalPage;
		$returnValue['lj']        = $plist;
		return $returnValue;
	}
	
	/**
	 * 单次请求中玩家交易数据
	 *
	 * @var array
	 */
	private static $userTradeInfo = array();
	
	/**
	 * 获得自己的交易，索要，打劫和其他玩家对自己送礼的数据
	 * 对自己送礼的数据是为了方便
	 * 所有数据只取当天的交易情况
	 * 数据只缓冲一个小时
	 *
	 * @param int $playersid			要查询的玩家id
	 * @return array					array(tid=>array(id, frompid, topid, type, itemid, status, ctime).....)
	 */
	public static function getMemTradeInfo($playersid){
		global $common, $db, $mc;
		$key = MC.$playersid.'_tradeInfo';
		
		// 先检查以前是否取得过数据
		if(isset(self::$userTradeInfo[$key])){
			return self::$userTradeInfo[$key];
		}
		
		// 获取玩家当天的交易数据
		$_userTradeInfo = array();
		$_userTradeInfo = $mc->get($key);
		if(empty($_userTradeInfo)){
			$_userTradeInfo = array();
			$year  = date('Y');
			$month = date('n');
			$day   = date('j');
			$todayZero = mktime(0,0,1,$month,$day,$year);
			$tradeSql = 'select * from ';
			$tradeSql .= "(select id, fromplayersid frompid, toplayersid topid, type, gift_type itemid, status, create_time ctime from ".$common->tname("social_trade");
			$tradeSql .= " where fromplayersid={$playersid} and create_time>{$todayZero}";
			$tradeSql .= "  UNION \n";
			$tradeSql .= "select id, fromplayersid frompid, toplayersid topid, type, gift_type itemid, status, create_time ctime from ".$common->tname("social_trade");
			$tradeSql .= " where toplayersid={$playersid} and create_time>{$todayZero} and (type=1 or type=2)) t";
			$tradeSql .= " order by ctime";
			
			$result = $db->query($tradeSql);
			
			while(($row=$db->fetch_array($result))!==false){
				$_userTradeInfo[$row['id']] = $row;
			}
			
			// 更新memcache和本地中的数据
			$mc->set($key, $_userTradeInfo, 0, 3600);
		}
		
		self::$userTradeInfo[$key] = $_userTradeInfo;
		
		// 针对凌晨玩家判断缓冲中数据是不是当天的，如果不是再重新取数据
		if(count($_userTradeInfo)>0){
			foreach($_userTradeInfo as $tradeSql){
				if(date('Y-m-d')!=date('Y-m-d', $tradeSql['ctime'])){
					unset(self::$userTradeInfo[$key]);
					$mc->delete($key);
					$_userTradeInfo = self::getMemTradeInfo($playersid);
					break;
				}
			}
		}
		
		return $_userTradeInfo;
	}
	
	/**
	 * 修改玩家缓存的交易数据
	 *
	 * @param int $playersid				要修改的玩家id
	 * @param array $userTradeInfo			array(id, frompid, topid, type, itemid, status, ctime)
	 */
	public static function modifyMemTradeInfo($playersid, $tradeInfo){
		global $mc;
		
		// 如果没有缓存数据就直接跳出不进行处理
		$key=MC.$playersid.'_tradeInfo';
		$_userTradeInfo = null;
		if(!isset(self::$userTradeInfo[$key])
			&& ($_userTradeInfo = $mc->get($key))===false){
			return;
		}
		
		// 更新缓冲数据，对当天的数据只会出现更新或添加。所以不用考虑删除
		$_userTradeInfo = is_null($_userTradeInfo)?self::$userTradeInfo[$key]:$_userTradeInfo;
		$_userTradeInfo[$tradeInfo['id']] = $tradeInfo;
		
		$mc->set($key, $_userTradeInfo, 0, 3600);
		self::$userTradeInfo[$key] = $_userTradeInfo;
	}

	// 达到完成邀请条件时调用
	// $type=1 等级完成, 2 充值完成
	public static function completeRequst($roleInfo, $type=0){
		global $common, $db, $mc, $_SC, $_g_lang;

		//检查配置中是否支持邀请码功能
		if(!(isset($_SC['canReq'])&&$_SC['canReq'])){
			return false;
		}

		if(!isset($roleInfo['r_code'])){
			return false;
		}

		if('' == $roleInfo['r_code']){
			return false;
		}

		if($type==0){
			return false;
		}

		// 检查是否完成过对应条件
		$feature = explode('_', $roleInfo['r_code']);
		if($type&$feature[1]){
			return false;
		}

		// 根据完成类型发送不同内容和奖励
		if(1 == $type){
			$message = $_g_lang['social']['request_code_3'];
			$sendItems = array('tq'=>1000,'yp'=>10,'yb'=>0,'jl'=>0);
		}else{
			$message = $_g_lang['social']['request_code_4'];
			$sendItems = array('tq'=>0,'yp'=>20,'yb'=>0,'jl'=>0,'items'=>array(array('id'=>'10001', 'num'=>'5', 'mc'=>'')));
		}
		$message = str_replace('{pid}', $roleInfo['playersid'], $message);
		$sendItems = addcslashes(json_encode($sendItems), '\\');
		$user = 'admin';
		$pass = 'admin';

		$ip = $_SC['domain'];
		//$token = file_get_contents('php://input', 'r');
		$auth = new authentication_header($user, $pass, $ip);
		$authvalues = new SoapVar($auth, SOAP_ENC_OBJECT, 'authenticate');
		$header = new SoapHeader(WS_SERVER, 'authenticate', $authvalues);
		$url = USER_SERVER . "authserver/server.php";

		$client = new SoapClient(null,array('location'=>$url, 'uri' => "http://127.0.0.1/"));
		$client->__setSoapHeaders(array($header));
		$r_code = $feature[0];
		try {
			$ret = $client->sendReqCode($r_code, $message, $sendItems);
		} catch(Exception $ex) {
			return "err:".$ex->getMessage();
		}

		// 如果正常完成修改用户邀请完成状态
		if(0 == $ret){
			$r_code = $feature[0].'_'.($type|$feature[1]);
			$updateArray['r_code'] = $r_code;
			$whereArray['playersid'] = $roleInfo['playersid'];
			$common->updatetable('player', $updateArray, $whereArray);
			$mc->delete(MC.$roleInfo['playersid']);
		}

		return $ret;
	}

	/**
	 * 邀请码绑定接口
	 *
	 *
	 */
	public static function bindReqCode($playersid, $r_code){
		global $common, $db, $mc, $_SC, $_g_lang;

		//检查配置中是否支持邀请码功能
		if(!(isset($_SC['canReq'])&&$_SC['canReq'])){
			return array('status'=>21, 'message'=>$_g_lang['social']['req_code_no_support']);
		}

		// 用户不能输入自己的邀请码,这里只验证本地环境
		if(substr($r_code, 3) == dechex($playersid)){
			return array('status'=>22, 'message'=>$_g_lang['social']['req_code_no_self']);
		}

		$roleInfo = array('playersid'=>$playersid);
		if(!rolemodel::getRoleInfo($roleInfo)){
			return array('status'=>23, 'message'=>$_g_lang['social']['no_find_account']);
		}

		// 检查用户是否已绑定邀请码
		if('' != $roleInfo['r_code']){
			return array('status'=>24, 'message'=>$_g_lang['social']['req_code_bind_yet']);
		}

		// 通过用户发送邀请奖励
		$user = 'admin';
		$pass = 'admin';
		$ip = $_SC['domain'];

		$auth = new authentication_header($user, $pass, $ip);
		$authvalues = new SoapVar($auth, SOAP_ENC_OBJECT, 'authenticate');
		$header = new SoapHeader(WS_SERVER, 'authenticate', $authvalues);
		$url = USER_SERVER . "authserver/server.php";

		$client = new SoapClient(null,array('location'=>$url, 'uri' => "http://127.0.0.1/"));
		$client->__setSoapHeaders(array($header));

		// 如果发送成功用户服务器返回0,否则可能该用户不存在或网络异常
		try{
			$message = $_g_lang['social']['request_code_1'];
			$message = str_replace('{pid}', $playersid, $message);
			$ret = $client->sendBindCodeInfo($playersid, $r_code, $message);
			if(3 == $ret || 4 == $ret || 5 == $ret){
				return array('status'=>25, 'message'=>$_g_lang['social']['req_code_no_find'],'v'=>$ret);
			}
		}catch(Exception $ex){
			return array('status'=>26, 'message'=>$_g_lang['social']['req_code_net_err'],'s'=>$ex->getMessage());
		}

		$otherPid = hexdec(substr($r_code, 3));

		// 如果发送成功更新用户邀请码
		$r_code = $r_code.'_0';
		$updateArray['r_code'] = $r_code;
		$whereArray['playersid'] = $roleInfo['playersid'];
		$common->updatetable('player', $updateArray, $whereArray);
		$mc->delete(MC.$roleInfo['playersid']);

		// 发送领取奖励信件
		$json = array();
		$json['playersid'] = $playersid;
		$json['toplayersid'] = $playersid;
		$message = $_g_lang['social']['request_code_2'];
		$message = str_replace('{server}', $ret, $message);
		$message = str_replace('{pid}', $otherPid, $message);
		$json['message'] = array('xjnr'=>$message);
		$json['genre'] = 20;
		$request = array('tq'=>0,'yp'=>20,'yb'=>0,'jl'=>0,'items'=>array(array('id'=>'10001', 'num'=>'5', 'mc'=>'')));
		$request = addcslashes(json_encode($request), '\\');
		$json['request'] = $request;
		$json['type'] = 1;
		$json['uc'] = '0';
		$json['is_passive'] = 0;
		$json['interaction'] = 0;
		$json['tradeid'] = 0;
		$ret = lettersModel::addMessage($json);

		// 如果该玩家当前等级已达到或超过COMPLETE_REQUEST_LEVEL 则发送对应奖品
		if(COMPLETE_REQUEST_LEVEL <= $roleInfo['player_level']){
			$roleInfo['r_code'] = $r_code;
			$sendResult = socialModel::completeRequst($roleInfo, 1);
		}

		return array('status'=>0, 'otherID'=>$otherPid);
	}
}