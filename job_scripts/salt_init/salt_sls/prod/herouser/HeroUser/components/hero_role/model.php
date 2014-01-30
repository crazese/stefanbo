<?php
// 好友人数最大上限
define ( 'FRIEND_MAX_NUM', 200 );
define ( 'FCLB_FLAG', true );
define ( 'VIP_LEVEL_MAX', 12 );
define ( 'DALEI_START_LEVEL', 7);

// 根据vip等级返回好友上限
function vipFriendLimit($vip_level) {
	switch ($vip_level) {
	case 0 :
		return 20;
	case 1 :
		return 30;
	case 2 :
		return 40;
	case 3 :
		return 60;
	case 4:
		return 80;
	case 5:
		return 100;
	case 6:
		return 150;
	case 7:
		return 160;
	case 8:
		return 170;
	case 9:
		return 180;
	case 10:
		return 190;
	case 11:
	case 12:
		return FRIEND_MAX_NUM;
	default :
		return 0;
	}

}

class roleModel {
	//检查该角色是否存在
	public static function isRoleExists($nickname, $userid) {
		global $db, $common;
		$result_checkUser = $db->query ( "SELECT `userid` FROM " . $common->tname ( 'player' ) . " WHERE `userid` = '$userid' LIMIT 1" );
		$number_checkUser = $db->num_rows ( $result_checkUser ); //检测该用户是否已经创建角色 0 为未创建
		if (empty ( $number_checkUser )) {
			return 2;
		} else {
			return 3; //已经创建过角色
		}
	}
	
	//检查用户提交的信息是否合法，比如是否有空值等
	public static function checkRoleInfo($roleInfo) {
		if (empty ( $roleInfo ['nickname'] ) or empty ( $roleInfo ['userid'] )) {
			return false;
		}
		return true;		
	}
	
	//添加社交角色数据
	public static function saveSocialInfo($userid, $playersid) {
		global $common, $db;
		$insert_social ['playersid'] = $playersid;
		$insert_social ['userid'] = $userid;
		$common->inserttable ( 'social', $insert_social );
	}
	
	//初始化擂台积分数据
	public static function saveDaleiInfo($roleInfo) {
		global $common, $db;
		// 过滤7级以下的玩家
		if(DALEI_START_LEVEL!=$roleInfo['player_level']){
			return;
		}
		
		$intSql = "replace into " . $common->tname ( 'dalei_jf' );
		$intSql .= " set playersid={$roleInfo['playersid']}";
		$intSql .= ", pmsj=0";
		$db->query ($intSql);
		$db->query ( "update `ol_dalei_jf_count` set num=num+1,lsh=lsh+1 where jf=0" );
	}

	// 每十级送三天加一级VIP
	/*public static function rwVip($roleInfo){
		global $common, $db, $mc;
		if($roleInfo['vip']<4){
			$rwvip = $roleInfo['vip']+1;
			$rw_vip_end_time = time() + 3*24*3600;
			$db->query("update `ol_player` set rw_vip='{$rwvip}', rw_vip_end_time='{$rw_vip_end_time}' where playersid={$roleInfo['playersid']}");
			$mc->delete(MC . $roleInfo['playersid']);
		}
	}*/
	
	// 用户初始资源
	public static function initRoleRes($roleInfo, $player) {
		//global $common, $db, $mc, $_SGLOBAL;
		
		// 绢布
		//toolsModel::addPlayersItem ( $roleInfo, 10036, 7 );
		// 木材
		//toolsModel::addPlayersItem ( $roleInfo, 10033, 7 );
		// 铁矿
		//toolsModel::addPlayersItem ( $roleInfo, 10035, 7 );
		// 陶土
		//toolsModel::addPlayersItem ( $roleInfo, 10034, 7 );
		// 石材
		//toolsModel::addPlayersItem ( $roleInfo, 10032, 7 );
		// 金刚石
		//toolsModel::addPlayersItem ( $roleInfo, 10001, 10 );
		// 玄铁
		//toolsModel::addPlayersItem ( $roleInfo, 10040, 10 );
		if(SYDD_TQLB == 1) {
			$player->AddItems(array(10036=>7, 10033=>7, 10035=>7, 10034=>7, 10032=>7, 10001=>10, 10040=>10, 18522=>1, 18608=>1));
		} else {
			$player->AddItems(array(10036=>7, 10033=>7, 10035=>7, 10034=>7, 10032=>7, 10001=>10, 10040=>10, 18522=>1));
		}
	}
	
	//插入角色数据
	public static function saveRoleInfo(&$roleInfo, $jcmgc = false) {
		global $common, $db, $mc, $_SGLOBAL, $_SC, $G_PlayerMgr, $role_lang;
		//print_r($roleInfo);
		//$checkName = roleModel::is_checkNameEnglish($roleInfo['nickname']);
		$checkName = roleModel::is_checkNameAll ( $roleInfo ['nickname'], $jcmgc );
		if ($checkName == 1) {
			return 10;
		}
		$checkRoleInfo = roleModel::checkRoleInfo ( $roleInfo );
		if (! empty ( $checkRoleInfo )) {
			$isRoleExists = roleModel::isRoleExists ( $roleInfo ['nickname'], $roleInfo ['userid'] );
			if ($isRoleExists == 2) {
				$nowTime = $_SGLOBAL ['timestamp'];
				if (isset ( $_SESSION ['debug'] )) {
					if ($_SESSION ['debug']) {
						$roleInfo ['player_level'] = 7;
					} else {
						$roleInfo ['player_level'] = 1;
					}
				}
				$roleInfo ['last_update_food'] = $nowTime;
				$roleInfo ['last_update_general'] = $nowTime - 21601;
				$roleInfo ['last_collect_time'] = $nowTime - collectNeedTime ();
				$roleInfo ['last_zl_collect_time'] = 0;
				$roleInfo ['last_wk_time'] = $nowTime - 181;
				$roleInfo['last_twk_time'] = $nowTime - 7201;
				$roleInfo['last_dalei_time'] = 0;
				$roleInfo['dqzmbid'] = 5001001;
				$roleInfo['hyd'] = 0;
				$roleInfo['hyjl'] = '00000';
				$roleInfo['sgpf'] = '00000000';
				//$roleInfo['rcrws'] = 3;
				//$roleInfo ['food'] = foodUplimit ( 1 );
				if (isset ( $_SESSION ['debug'] )) {
					if ($_SESSION ['debug']) {
						$roleInfo ['coins'] = COINSUPLIMIT;
						$roleInfo ['silver'] = SILVERUPLIMIT;
						$roleInfo ['ingot'] = INGOTUPLIMIT;
						$roleInfo ['food'] = 999999;
						$general ['general_level'] = rand(30,70); //将领级别
						$roleInfo ['djt_level'] = 12; //点将台等级
						$roleInfo ['vip'] = 4;
						$roleInfo ['vip_end_time'] = $nowTime + 2592000;						
					} else {
						$roleInfo ['coins'] = 25000;
						$roleInfo ['silver'] = 100;
						$roleInfo ['ingot'] = 0;
						$roleInfo ['food'] = foodUplimit ( 1 );
						$general ['general_level'] = '1'; //将领级别
						$roleInfo ['djt_level'] = 1; //点将台等级
						$roleInfo ['vip'] = 0;
						$roleInfo ['vip_end_time'] = 0;						
					}
				}
				//$roleInfo['wood'] = 0;      //木材
				//$roleInfo['concrete'] = 0;  //陶土
				//$roleInfo['stone'] = 0;     //石材
				//$roleInfo['cloth'] = 0;     //绢布
				//$roleInfo['iron'] = 0;	  //铁矿	   	  
				$roleInfo ['jg_level'] = 1; //酒馆等级
				$roleInfo ['sc_level'] = 1; //市场等级
				$roleInfo ['ld_level'] = 1; //领地等级
				$roleInfo ['tjp_level'] = 1; //铁匠铺等级		 
				//$roleInfo['parliament'] = 1;
				$roleInfo ['completeQuests'] = '';				
				
				// 封测vip2
				/*  if(FCLB_FLAG) {
		   	  	$roleInfo['vip'] = 2;
		   	  	$roleInfo['vip_end_time'] = $nowTime + 2592000;
		   	  } else { */
				/*if (isset ( $_SESSION ['debug'] )) {
					if ($_SESSION ['debug']) {
						$roleInfo ['vip'] = 4;
						$roleInfo ['vip_end_time'] = $nowTime + 2592000;
					} else {
						$roleInfo ['vip'] = 0;
						$roleInfo ['vip_end_time'] = 0;
					}
				}*/
				//}		   	  
				$roleInfo ['boxOpen'] = "";
				$roleInfo ['boxTime'] = $nowTime;
				$roleInfo ['inviteid'] = 0;
				$roleInfo ['current_experience_value'] = 0;
				$roleInfo ['aggressor_playersid'] = 0;
				$roleInfo ['aggressor_general'] = 0;
				$roleInfo ['aggressor_nickname'] = '';
				$roleInfo ['aggressor_level'] = 0;
				$roleInfo['zf_aggressor_general'] = 0;
				$roleInfo ['is_defend'] = 0; //默认不设防
				$roleInfo ['end_defend_time'] = 0;
				if (isset ( $_SESSION ['debug'] )) {
					if ($_SESSION ['debug']) {
						$roleInfo ['bagpack'] = 300;
					} else {
						$roleInfo ['bagpack'] = 30; //初始背包格数
					}
				}
				$roleInfo ['wk_count'] = 0;
				$roleInfo ['strategy'] = 0;
				$roleInfo ['zf_strategy'] = 0;
				$roleInfo ['mg_level'] = 1;
				$roleInfo ['prestige'] = 0;
				$roleInfo ['jscs'] = 0;
				$roleInfo ['jssjrq'] = 0;
				$roleInfo ['is_reason'] = 0;
				$roleInfo ['createTime'] = $nowTime;
				$roleInfo ['tb_cs'] = 0;
				$roleInfo ['tb_gk'] = 1;
				$roleInfo ['tb_sjrq'] = 0;
				$roleInfo ['rw_vip'] = 0;
				$roleInfo ['rw_vip_end_time'] = 0;
				$roleInfo ['frd'] = 80;
				$roleInfo ['lssxcs'] = 0;
				$roleInfo ['zssxcs'] = 0;
				$roleInfo ['cssxcs'] = 0;
				$roleInfo ['xwzt'] = '00000000000000000000000000000000';
				$roleInfo ['ba'] = 0;
				$roleInfo ['rank'] = 109;
				$roleInfo ['rank_cd'] = 0;		
				$roleInfo ['qzcs'] = 0;
				$roleInfo ['qzsjrq'] = 0;	
				$roleInfo ['rwsj'] = '00000000000000000000000000000000';
				$roleInfo ['today_addba'] = 0;
				$roleInfo ['today_reduceba'] = 0;
				$roleInfo ['curr_ba_date'] = 0;				
				$roleInfo['today_ba_uplimit'] = 0;
				$roleInfo['today_ba_downlimit'] = 0;
				$roleInfo['ykzjrl'] = 0;
				$roleInfo['regionid'] = 0;
				//$roleInfo['area'] = 135;
				//玩家当前位置，根据势力区分，1 梁山 0 沧州
				/*if ($roleInfo['inviteid'] > 0) {
		   	  	$result = $db->query("SELECT fromplayersid FROM ".$common->tname('letters')." WHERE type = 2 and id = ".$roleInfo['inviteid']." and status = 0 LIMIT 1");
				$rows_pid = $db->fetch_array($result);
		   	  	$result = $db->query("SELECT regionid FROM ".$common->tname('player')." WHERE playersid = '".$rows_pid['fromplayersid']."' LIMIT 1");
				$rows_regionid = $db->fetch_array($result);
				$roleInfo['regionid'] = $rows_regionid['regionid'];
				 if ($roleInfo['regionid'] == 0) {
			   	  	 $roleInfo['current_pos']='4,5,山寨';
			   	 } else {
			   	  	 $roleInfo['current_pos']='5,4,山寨';    
			   	 }	
		   	  } else {
			   	 if ($roleInfo['regionid'] == 0) {
			   	  	 $roleInfo['current_pos']='4,5,山寨';       
			   	 } else {
			   	  	 $roleInfo['current_pos']='5,4,山寨';        
			   	 }	
		   	  }*/
				//$roleInfo['current_pos']='5,4,山寨';
				$insertRole = $roleInfo;
				$insertRole['nickname'] = mysql_escape_string($roleInfo['nickname']);
				$playersid = $common->inserttable ( 'player', $insertRole );
				if (empty ( $playersid )) {
					return 5;
				}					
				$mc->set ( MC . $playersid, $roleInfo, 0, 3600 );
				/*端午节活动*/
				/*if ($_SGLOBAL['timestamp'] > $_SC['dwhdkssj'] && $_SGLOBAL['timestamp'] < $_SC['dwhdjssj']) {
					$nowTime = date('Y-m-d',$_SGLOBAL['timestamp']);
					$checksql = "SELECT playersid FROM ".$common->tname('dwhd')." WHERE playersid = '$playersid' && createTime = '$nowTime' LIMIT 1";
					$checkres = $db->query($checksql);
					$amount = $db->num_rows($checkres);
					if ($amount == 0) {
						$message = array('playersid'=>$playersid,'toplayersid'=>$playersid,'subject'=>'七一狂欢，上线领惊喜！','genre'=>20,'tradeid'=>0,'interaction'=>0,'is_passive'=>0,'type'=>1,'request'=>addcslashes(json_encode(array('tq'=>0,'yp'=>0,'yb'=>0,'jl'=>0,'items'=>array(array('id'=>'20114','mc'=>'闯关卡','num'=>1)))), '\\'),'message'=>array('xjnr'=>'七一狂欢，上线领惊喜！活动奖励：闯关卡X1，祝各位玩家游戏愉快！'));
						lettersModel::addMessage($message);
						$common->inserttable('dwhd',array('playersid'=>$playersid,'createTime'=>$nowTime),0,1);
					}
				}*/				
				/*端午节活动结束*/					
				$_SESSION ['playersid'] = $playersid;
				// 初始化玩家闯关信息
				$stageInfo ['playersid'] = $playersid;
				$stageInfo ['difficulty'] = 1;
				$stageInfo ['unlock'] = 0;
				$stageInfo ['curr_difficulty'] = 0;
				$stageInfo ['curr_stage'] = 0;
				$stageInfo ['curr_subStage'] = 0;
				$stageInfo ['last_cg_date'] = time ();
				$stageInfo ['attackBossTimes'] = 0;
				$stageInfo ['buyTimes'] = 0;
				$stageInfo ['addTimes'] = 0;
				$stageInfo ['cgTimes'] = 0;
				if (isset ( $_SESSION ['debug'] )) {
					if ($_SESSION ['debug']) {
						$stageInfo ['timesLimt'] = 100000;
					} else {
						$stageInfo ['timesLimt'] = 6;
					}
				}
				$common->inserttable ( 'player_stage', $stageInfo );
				//roleModel::hqydrw($playersid);		      
				//$_SESSION['current_pos'] = $roleInfo['current_pos'];				
				//$_SESSION['regionid'] = $roleInfo['regionid'];
				$roleInfo ['playersid'] = $playersid;					
				$mc->set ( MC . $playersid, $roleInfo, 0, 3600 );
				//questsController::OnAccept ( $roleInfo, "'player_level'" ); //接收任务
				$player = $G_PlayerMgr->GetPlayer($playersid );								
				$playerBag = &$player->GetItems();
				
				// 添加封测礼包
				if (FCLB_FLAG) {
					//toolsModel::addPlayersItem ( $roleInfo, 20075, 1 );
					$player->AddItems(array(20075=>1));
				}
				
				// 根据用户的openid判断是否添加勇士礼包
				if(SYPT == 1) {
					if(!strpos(SYYSLB_WHITELIST, ',')) {
						$syopenid = array(SYYSLB_WHITELIST);
					} else {
						$syopenid = explode(',', SYYSLB_WHITELIST);
					}
					//$common->insertLog($roleInfo['ucid'] . '1');
					if(array_search($roleInfo['ucid'], $syopenid) !== false) {
						//toolsModel::addPlayersItem ( $roleInfo, 20001, 1 );
						$player->AddItems(array(20001=>1));
					}
				}
				
				// 用户初始资源
				roleModel::initRoleRes ( $roleInfo, $player );
				//questsController::OnAccept(array('playersid'=>$playersid,'player_level'=>1,'regionid'=>$roleInfo['regionid'],'completeQuests'=>''),"'player_level'");//检查是否有可接受任务
				roleModel::saveSocialInfo ( $roleInfo ['userid'], $playersid );
				//赠送三个主将
				$general_sex = 1;
				$avatar_1 = 'YX016';
				$avatar_2 = 'YX015';
				$avatar_3 = 'YX011';
				$general_name_1 = $role_lang['saveRoleInfo_1'];
				$general_name_2 = $role_lang['saveRoleInfo_2'];
				$general_name_3 = $role_lang['saveRoleInfo_3'];
				$understanding_value_1 = 20;
				$understanding_value_2 = 18;
				$understanding_value_3 = 16;
				$professional_1 = $arm_1 = 2;
				$professional_2 = $arm_2 = 3;
				$professional_3 = $arm_3 = 1;
				$jwInfo = jwmc ( 1 );
				$jwjc = 1 + $jwInfo ['jc'] / 100;
				$attributeValue_1 = genModel::sxxs ( $professional_1 );
				$attributeValue_2 = genModel::sxxs ( $professional_2 );	
				$attributeValue_3 = genModel::sxxs ( $professional_3 );	
				$general_life_1 = (genModel::hqwjsx ( 1, $understanding_value_1, 1, 0, $jwjc, 0, $attributeValue_1 ['tl'] )) * 10;
				$general_life_2 = (genModel::hqwjsx ( 1, $understanding_value_2, 1, 0, $jwjc, 0, $attributeValue_2 ['tl'] )) * 10;	
				$general_life_3 = (genModel::hqwjsx ( 1, $understanding_value_3, 1, 0, $jwjc, 0, $attributeValue_3 ['tl'] )) * 10;	
				$general_level = 1;					
				$general_sort_1 = 1;
				$general_sort_2 = 2;
				$general_sort_3 = 3;
				$professional_level = 1;
				$f_status = 1;
				$db->query("INSERT INTO ".$common->tname('playergeneral')."(general_sex,avatar,general_name,understanding_value,professional,general_life,general_level,playerid,general_sort,arm,professional_level,f_status) VALUES ('$general_sex','$avatar_1','$general_name_1','$understanding_value_1','$professional_1','$general_life_1','$general_level','$playersid','$general_sort_1','$arm_1','$professional_level','$f_status'),('$general_sex','$avatar_2','$general_name_2','$understanding_value_2','$professional_2','$general_life_2','$general_level','$playersid','$general_sort_2','$arm_2','$professional_level','$f_status'),('$general_sex','$avatar_3','$general_name_3','$understanding_value_3','$professional_3','$general_life_3','$general_level','$playersid','$general_sort_3','$arm_3','$professional_level','$f_status')");
				
				
				
				//添加武将训练位
				$db->query ( "INSERT INTO " . $common->tname ( 'playerxlw' ) . "(playersid,gid,g_end_time,end_time,is_open,px) VALUES ('$playersid','0','0','0','1','1'),('$playersid','0','0','0','1','2'),('$playersid','0','0','0','0','3'),('$playersid','0','0','0','0','4'),('$playersid','0','0','0','0','5'),('$playersid','0','0','0','0','6'),('$playersid','0','0','0','0','7'),('$playersid','0','0','0','0','8'),('$playersid','0','0','0','0','9')" );
				//添加默认留言
				$db->query ( "INSERT INTO " . $common->tname ( 'city_msg' ) . "(playersid,msg,newmsg) VALUES ('$playersid','','0')" );
				//添加默认留言数量
				//$db->query ( "INSERT INTO " . $common->tname ( 'player_ly_count' ) . "(playersid,lysl) VALUES ('$playersid','0')" );
				//添加默认任务状态数据
				$db->query ( "INSERT INTO " . $common->tname ( 'quests_new_status' ) . "(playersid,qstatusInfo) VALUES ('$playersid','')" );
				//添加默认引导数据
				//$db->query ( "INSERT INTO " . $common->tname ( 'guide_event' ) . "(playersid,guide_event) VALUES ('$playersid','')" );
				//生成资源点
				zydModel::sczyd($roleInfo['playersid'],mysql_escape_string($roleInfo['nickname']));
				return 1;
			} elseif ($isRoleExists == 3) {
				return 2;
			} else {
				return 3;
			}
		} else {
			return 4; //角色信息不合法
		}
	}
	
	//从数据库查询角色数据
	public static function getTableRoleInfo($playersid = '', $userid = '', $setMem = true, $IS_ROW = MYSQL_ASSOC) {
		global $common, $db, $mc;
		if (! empty ( $userid )) {
			$item = " && userid = '" . $userid . "'";
		} else {
			$item = '';
		}
		if (! empty ( $playersid )) {
			$item1 = " && playersid = '" . $playersid . "'";
		} else {
			$item1 = '';
		}
		if ((empty ( $userid ) && empty ( $playersid )) || (! empty ( $userid ) && ! empty ( $playersid ))) {
			return false;
		}
		
		$result = $db->query ( "SELECT * FROM " . $common->tname ( 'player' ) . " WHERE 1" . $item1 . $item . " LIMIT 1" );
		$rows = $db->fetch_array ( $result, $IS_ROW );
		if (empty ( $rows )) {
			return false;
		} else {
			//$db->free_result($result);                //释放查询内存		 
			$playersid = $rows ['playersid'];
			//$rows['nickname'] = stripcslashes($rows['nickname']);
			if ($setMem == true) {
				$mc->set ( MC . $playersid, $rows, 0, 3600 );
			}
			return $rows;
		}
	}
	
	/**
	 * 批量获得玩家信息
	 * @param array $playersids
	 * @return array
	 */
	public static function getAllRolesInfo($playidsList) {
		global $common, $db, $mc,$G_PlayerMgr;
    
		$list = array();
		$players= $G_PlayerMgr->GetPlayers($playidsList);
		foreach ( $players as $player ) {
			$list[] = &$player->baseinfo_;
		}
		return $list;
	}
	
	/**
	 * 获取玩家关系的完整数据 
	 * @param int $playersid
	 * @param int $type			默认1好友关系，4占领领地，3仇人关系
	 * @param boolean $onlyID	true仅仅只返回id列表数组，不包含其他的数据
	 * @return array			如果$onlyID为true好友或仇人的playersid为键组成的数组array(pid=>{feel,atime,level})
	 * 否则就返回对应的玩家信息组成的数组
	 */
	public static function getTableRoleFriendsInfo($playersid, $type = 1, $onlyID = false) {
		global $common, $db, $mc, $_SGLOBAL,$G_PlayerMgr;

		$friendIds = array();
		$player = $G_PlayerMgr->GetPlayer($playersid);
		if(empty($player))  return $friendIds;
    
		switch($type) {
		case 1:
			$friendIds = $player->GetFriends();
			break;
		case 3:
			$friendIds = $player->GetEnemys();
			break;
		case 4:
			$friendIds = $player->GetOccupys();
			break;
		default:
			return array ();
		}
		if ($onlyID)  return $friendIds;

		// 获得对应好友用户信息，如果没有好友直接返回空数组
		$tmpFriendsInfo = roleModel::getAllRolesInfo ( array_keys ( $friendIds ) );
		
		// 对每个玩家添加好感度
		foreach ( $tmpFriendsInfo as $key => $playerInfo ) {
			$playerInfo ['feel'] = $friendIds [$playerInfo ['playersid']] ['feel'];
			$tmpFriendsInfo [$key] = $playerInfo;
		}
		
		// 按照返回列表id进行排序，排序优先循序为好感度，等级。非好友列表不考虑好感度
		// 防止获取好友信息后发生数据排序错误
		$friendsLevelList = array ();
		$friendsFeelList = array ();
		foreach ( $tmpFriendsInfo as $key => $friendId ) {
			$friendsLevelList [$key] = $friendId ['player_level'];
			if (1 == $type) {
				$friendsFeelList [$key] = $friendId ['feel'];
			}
		}
		if (1 == $type)
			array_multisort ( $friendsFeelList, SORT_DESC, $friendsLevelList, SORT_DESC, $tmpFriendsInfo );
		else
			array_multisort ( $friendsLevelList, SORT_DESC, $tmpFriendsInfo );
		
		$friendsInfo = array_values ( $tmpFriendsInfo );
		
		// 返回好友信息
		return $friendsInfo;
	}
  
	//从数据库取得玩家社交信息
	public static function getSocialPlayersInfo($pid,$type) {
		global $db,$common;
		$where = " su.parent_playersid='{$pid}' and su.playersid=p.playersid";
		$sltSql = "select su.playersid, su.feel, su.add_time, p.player_level, p.is_reason from " ;
		$sltSql .= $common->tname ( 'social_user' ) . " su inner join ";
		$sltSql .= $common->tname ( 'player' ) . " p on su.type={$type} and " . $where;
		$sltSql .= " order by su.feel, p.player_level";
    
		$result = $db->query ( $sltSql );
    
		$friendIds = array ();
		while ( $rows = $db->fetch_array ( $result ) ) {
			$friendIds [] = array ('id' => $rows ['playersid'],
								   'feel' => $rows ['feel'], 
								   'atime' => $rows ['add_time'], 
								   'level' => $rows ['player_level'],
								   'deny'  => $rows['is_reason']);
		}
    
		// 对玩家列表id进行排序，排序优先循序为好感度，等级。如果不是好友列表不考虑好感度
		$friendsLevelList = array ();
		$friendsFeelList = array ();
		foreach ( $friendIds as $key => $friendId ) {
			$friendsLevelList [$key] = $friendId ['level'];
			// 仇人不需要按好感度排序
			if (1 == $type) {
				$friendsFeelList [$key] = $friendId ['feel'];
			}
		}
		if (1 == $type)
			array_multisort ( $friendsFeelList, SORT_DESC, $friendsLevelList, SORT_DESC, $friendIds );
		else
			array_multisort ( $friendsLevelList, SORT_DESC, $friendIds );
      
		// 重新格式化好友id列表
		$outFriendIds = array ();
		foreach ( $friendIds as $friendId ) {
			$outFriendIds [$friendId ['id']] = array ('feel' => $friendId ['feel'],
													  'atime' => $friendId ['atime'], 
													  'level' => $friendId ['level'],
													  'deny'  => $friendId ['deny']);
		}
		$friendIds = $outFriendIds;
		return $friendIds;
	}
	
	//从数据库查询角色UC好友数据
	public static function getTableRoleUcFriendsInfo($playersid = '', $ucid = '', $userid = '', $setMem = true, $IS_ROW = MYSQL_ASSOC) {
		global $common, $db, $mc;
		if (! empty ( $ucid )) {
			$item1 = " && ucid = '" . $ucid . "'";
		} else {
			$item1 = '';
		}
		if ((empty ( $userid ) && empty ( $ucid )) || (! empty ( $userid ) && ! empty ( $ucid ))) {
			return false;
		}
		
		$value = array ();
		$result = $db->query ( "SELECT * FROM " . $common->tname ( 'uc_friends' ) . " WHERE 1" . $item1 );
		while ( $rows = $db->fetch_array ( $result ) ) {
			$value [] = $rows;
		}
		if (! empty ( $value )) {
			$mc->set ( MC . $playersid . '_ucfriends', $value, 0, 3600 );
		}
		return $value;
	}

	/**
	 * 得到最近30天内实际缴费额
	 * @author KKND LI
	 * @param int $playersid
	 * @param int $checkTime unix时间戳，检查时间
	 * @return int 检查时间开始前30天充值元宝
	 */
	public static function getPlayerPayYaobao($playersid, $checkTime=null){
		global $common, $db;
		//$dateStr = is_null($checkTime)?date('Y-m-d H:i:s'):date('Y-m-d H:i:s', $checkTime);
		
		//$whereStr = " where insertTime>SUBDATE('{$dateStr}' , INTERVAL 30 DAY)";
		$whereStr = " where playersid={$playersid}";
		
		$sqlStr = "select sum(ingot) from " . $common->tname ( 'vip_money_log' ) . $whereStr;
		$result = $db->query ( $sqlStr );
		$money = mysql_result ( $result, 0 );
		return $money;
	}

	public static function getPayVipLevel($playersid, $ingot){
		$sumIngot = roleModel::getPlayerPayYaobao($playersid) + $ingot;

		$vipPrice = getVipPrice();
		$vip = 0;
		foreach($vipPrice as $level=>$price){
			if($sumIngot >= $price*RMB_TO_YUANBAO) $vip=$level;
		}
		
		return $vip;
	}

	/**
	 * 检查玩家信息根据任务vip和充值vip以及最近充值时间设定最终vip
	 * @author KKND LI
	 * 废弃的方法
	 */
	/*public static function setRoleVip($roleInfo){
		global $common, $db, $mc, $_SGLOBAL;
		$nowTime = $_SGLOBAL ['timestamp'];
		
		$rw_vip = $roleInfo ['rw_vip_end_time'] < $nowTime ? 0 : $roleInfo ['rw_vip'];

		// 如果vip过期就检查新的充值vip等级
		if($roleInfo ['vip_end_time'] < $nowTime && $roleInfo['vip'] != 0){
			// 如果结束时间超过30天以上以当前时间为检查时间，否则以过期时间为准
			$checkTime = ($roleInfo['vip_end_time'] + 30*24*3600) < $nowTime ? $nowTime : $roleInfo['vip_end_time'];
			$playersid = $roleInfo['playersid'];
			$vipPrice = getVipPrice ();
			$money = roleModel::getPlayerPayYaobao($roleInfo['playersid'], $checkTime
);
			$vip = 0;
			foreach($vipPrice as $level=>$price){
				if($money >= $price*RMB_TO_YUANBAO) $vip=$level;
			}

			// 更新vip信息
			$vip_end_time = $vip==0 ? 0 : $checkTime + 30*24*3600;
			$updateSql = 'update ' . $common->tname('player');
			$updateSql .= " set vip={$vip}, vip_end_time={$vip_end_time}";
			$updateSql .= " where playersid={$playersid}";

			$db->query($updateSql);

			// 将当前用户vip信息修正为实际vip
			$cur_vip = $vip;
			$roleInfo['vip_end_time'] = $vip_end_time;
			$roleInfo['vip']          = $cur_vip;
			$mc->delete(MC.$playersid);
		}
		else{
			$cur_vip = $roleInfo['vip'];
		}
		
		if($rw_vip > $cur_vip){
			$roleInfo ['vip'] = $roleInfo['rw_vip'];
			$roleInfo['vip_end_time'] = $roleInfo ['rw_vip_end_time'];
		}

		return $roleInfo;
	}
	*/
  
	//查询角色数据
	public static function getRoleInfo(&$pid) {
		global $G_PlayerMgr;
		if(!is_array($pid))
			return $G_PlayerMgr->GetPlayer($pid);
     
		// 以下是兼容性代码
		if (! empty ( $pid ['playersid'] )) {
			$player = $G_PlayerMgr->GetPlayer($pid ['playersid'] );
			if(empty($player)){
				$pid = null;
				return null;
			}
			$pid = $player->baseinfo_;
			return $pid;
		}
		if (!empty($pid ['userid'])) {
			$userid = $pid ['userid'];
		} else {
			$userid = 0;
		} 
    
		$pid = roleModel::getTableRoleInfo ( '', $userid );
		if (empty ( $pid ))  return null;
		//	$pid = roleModel::setRoleVip($pid);
		return $pid;
	}
	
	//从memcache查询角色uc好友数据
	public static function getRoleUcFriendsInfo($roleInfo, $isCheck = false) {
		global $mc;
		if (! empty ( $roleInfo ['playersid'] )) {
			$playersid = $roleInfo ['playersid'];
			$ucid = $roleInfo ['ucid'];
			if (! ($roleInfo = $mc->get ( MC . $playersid . '_ucfriends' )) || $isCheck) {
				$roleInfo = array ();
				$roleInfo = roleModel::getTableRoleUcFriendsInfo ( $playersid, $ucid );
				if (! empty ( $roleInfo )) {
					return $roleInfo;
				} else {
					return '';
				}
			}
			return $roleInfo;
		}
	}

	//处理从用户服务器请求的角色数据
	public static function getRoleInfoFromUserver($get_userid = null) {
		global $common, $db;
		//$userid = _get('userId');
		$userid = isset ( $get_userid ) ? $get_userid : _get ( 'userId' );
		$sql = "SELECT playersid,nickname FROM " . $common->tname ( 'player' ) . " WHERE userid = '$userid' LIMIT 1";
		$result = $db->query ( $sql );
		$rows = $db->fetch_array ( $result );
		$db->free_result ( $result );
		if (! empty ( $rows )) {
			$roleInfo = array ('playersid' => $rows ['playersid'], 'nickname' => $rows ['nickname'] );
			return $roleInfo;
		} else {
			return false;
		}
	}	
	
	//登录游戏服务器
	public static function login(&$roleInfo) {
		global $common, $db, $mc, $_SGLOBAL, $_SC;
		if (! empty ( $roleInfo ['userid'] )) {
			$wheresqlarr ['userid'] = $roleInfo ['userid'];
			$result = roleModel::getRoleInfo ( $roleInfo );
			if (! empty ( $result )) {
				/*if ($roleInfo['vip_end_time'] <= time()) {
					$roleInfo['vip'] = 0;
				} else {
					$roleInfo['vip'] = 1;
				}*/
				$_SESSION ['playersid'] = $roleInfo ['playersid'];
				//$_SESSION['current_pos'] = $roleInfo['current_pos'];
				//将是否城池保护装入内存
				/*$peaceResult = $db->query("SELECT * FROM ".$common->tname('peace')." WHERE `playersid` = '".$roleInfo['playersid']."' LIMIT 1");
    	        $peaceRows = $db->fetch_array($peaceResult);   	
    	        if (!empty($peaceRows)) {
    	        	$mc->set(MC.$roleInfo['playersid'].'_peace',$peaceRows,0,$peaceRows['peaceTime']);
    	        }*/
				//将该用户消息状态装入内存			
				$rows_message = $db->fetch_array ( $db->query ( "SELECT * FROM " . $common->tname ( 'message_status' ) . " WHERE playersid = '" . $roleInfo ['playersid'] . "' LIMIT 1" ) );
				if (! empty ( $rows_message )) {
					$mc->set ( MC . $roleInfo ['playersid'] . '_messageStatus', $rows_message, 0, 3600 );
				}
				/*端午节活动*/
				if ($_SGLOBAL['timestamp'] > $_SC['dwhdkssj'] && $_SGLOBAL['timestamp'] < $_SC['dwhdjssj']) {
					$nowTime = date('Y-m-d',$_SGLOBAL['timestamp']);
					$checksql = "SELECT playersid FROM ".$common->tname('dwhd')." WHERE playersid = '".$roleInfo['playersid']."' && createTime = '$nowTime' LIMIT 1";
					$checkres = $db->query($checksql);
					$amount = $db->num_rows($checkres);
					if ($amount == 0) {
						$message = array('playersid'=>$roleInfo['playersid'],'toplayersid'=>$roleInfo['playersid'],'subject'=>'七一狂欢，上线领惊喜！','genre'=>20,'tradeid'=>0,'interaction'=>0,'is_passive'=>0,'type'=>1,'request'=>addcslashes(json_encode(array('tq'=>0,'yp'=>0,'yb'=>0,'jl'=>0,'items'=>array(array('id'=>'20114','mc'=>'闯关卡','num'=>1)))), '\\'),'message'=>array('xjnr'=>'七一狂欢，上线领惊喜！活动奖励：闯关卡X1，祝各位玩家游戏愉快！'));
						lettersModel::addMessage($message);
						$common->inserttable('dwhd',array('playersid'=>$roleInfo['playersid'],'createTime'=>$nowTime),0,1);
					}
				}				
				/*端午节活动结束*/				
				//处理日常任务
				$uprwres = roleModel::hqrcrw ( $roleInfo );
				if ($uprwres == true) {
					//$roleInfo['rcrws'] = 3;
					$roleInfo['hyd'] = 0;
					$roleInfo['hyjl'] = '00000';
				}				
				questsController::OnAccept ( $roleInfo, "'player_level'" ); //接收任务
				//guideScript::jsydsj ( $roleInfo, 'player_level', 1, 1 ); //接收引导	
				//处理活动情况
				//huodongController::onFinishHD($roleInfo,'wjdj');
			} else {
				$_SESSION ['playersid'] = 0;
			}
		}
		if (! empty ( $roleInfo ['playersid'] )) {
			// 触发活动脚本
			hdProcess::run(array('role_login'), $roleInfo, 1);
			
			// 保存未使用的闯关抽奖信息
			toolsModel::lateCj($roleInfo['playersid']);
			
			// 更新绑定手机号
			if($phone = _get('sjhm')){
				if(isset($roleInfo['phone'])&&$phone != $roleInfo['phone']){
					$roleInfo['phone'] = $phone;
				}
			}			
			cityModel::resourceGrowth ( $roleInfo, true, true );
			roleModel::setbox ( $roleInfo );
			$showValue ['status'] = 0; //有角色		
		} else {
			$showValue ['status'] = 23; //无角色
		}
		if (_get ( 'client' ) == 1) {
			$_SESSION ['client'] = 1;
		}
		return $showValue;
	}
	
	//每天第一次登录时获取奖励
	public static function getAward($roleInfo) {
		global $common, $db, $rw_lang;
		$playersid = $roleInfo ['playersid'];
		$nowTime = date ( 'Y-m-d', time () );
		$nowTimeInfo = explode ( '-', $nowTime );
		$nowTimeLinuxTime = mktime ( 0, 0, 0, $nowTimeInfo [1], $nowTimeInfo [2], $nowTimeInfo [0] );
		$sql = "SELECT * FROM " . $common->tname ( 'award' ) . " WHERE playersid = '$playersid' LIMIT 1";
		$result = $db->query ( $sql );
		$rows = $db->fetch_array ( $result );
		if (! empty ( $rows )) {
			$lastLoginTime = date ( 'Y-m-d', $rows ['login_date'] );
			$lastLoginTimeInfo = explode ( '-', $lastLoginTime );
			$lastLoginTimeLinuxTime = mktime ( 0, 0, 0, $lastLoginTimeInfo [1], $lastLoginTimeInfo [2], $lastLoginTimeInfo [0] );
			$diffTime = $nowTimeLinuxTime - $lastLoginTimeLinuxTime;
			if ($nowTime == $lastLoginTime) {
				return false;
			} else {
				$value ['status'] = 0;
				//$value['amount'] = 1000;
				//$common->updatetable('player',$updateRole,$whereRole);   //更新获取的资源	        
				$whereAward ['playersid'] = $roleInfo ['playersid'];
				$updateAward ['login_date'] = time ();
				if ($diffTime > 86400 || $rows ['award_status'] == 5) {
					$updateAward ['award_status'] = 1;
				} else {
					$updateAward ['award_status'] = $rows ['award_status'] + 1;
				}
				if ($updateAward ['award_status'] <= 4) {
					$value ['type'] = 1; //获得铜钱
					$value ['amount'] = $updateAward ['award_status'] * 1000;
					$value ['jrjl'] = ($updateAward ['award_status'] * 1000) . $rw_lang['rw_getRWInfo_4'];
					if ($updateAward ['award_status'] == 4) {
						$value ['mrjl'] = '';
					} else {
						$value ['mrjl'] = (($updateAward ['award_status'] + 1) * 1000) . $rw_lang['rw_getRWInfo_4'];
					}
					$value ['day'] = $updateAward ['award_status'];
					$value ['award'] = 1;
				} else {
					//1元宝 2银两3军粮4铜钱
					$value ['type'] = 2;
					$prize = array (0 => array (4, 5000 ), 1 => array (3, 15 ), 2 => array (2, 10 ) );
					$_SESSION ['prize'] = $prize;
					shuffle ( $prize );
					//$prizeResult = array_slice($prize,0,3);
					$prizeShow = array ();
					for($i = 0; $i < count ( $prize ); $i ++) {
						$prizeShow [] = array ('itemId' => $prize [$i] [0], 'value' => $prize [$i] [1] );
					}
					$value ['prize'] = $prizeShow;
					//$value['type'] = 2;      //获得银两
					//$value['amount'] = 1;
					//$value['jrjl'] = '1张粮票';
					$value ['award'] = 1;
					//$value['mrjl'] = '1000铜钱';
					$value ['day'] = 5;
				}
				//$insertAward['award_status'] = 1;
				$common->updatetable ( 'award', $updateAward, $whereAward );
			}
		} else {
			$insert ['playersid'] = $roleInfo ['playersid'];
			$insert ['login_date'] = time ();
			$insert ['award_status'] = 1;
			$common->inserttable ( 'award', $insert );
			$value ['status'] = 0;
			$value ['type'] = 1; //获得铜钱
			$value ['amount'] = 1000;
			$value ['jrjl'] = '1000'.$rw_lang['rw_getRWInfo_4'];
			$value ['mrjl'] = '2000'.$rw_lang['rw_getRWInfo_4'];
			$value ['day'] = 1;
			$value ['award'] = 1;
		}
		$_SESSION ['day'] = $value ['day'];
		return $value;
	}
	
	//加银子
	/*static function addCoins($nums,$playersid) {
		global $common,$db;
		$result = $db->query("SELECT food,silver,silver_warehouse,coins FROM ".$common->tname('player')." WHERE playersid = '".$playersid."'  LIMIT 1");
		$rows = $db->fetch_array($result);
		$CapacityValue = storageCapacityValue();      
 		$currentSilverCapacity = $CapacityValue[12][$rows['silver_warehouse']];         //当前银库容量
        $currentCurrencyTotal = $rows['coins'] + $rows['silver']*350;      //当前货币量，即所占银库量
        $currentRemainingSilverCapacity = $currentSilverCapacity - $currentCurrencyTotal;   //当前剩余银库空间量 
		
		if($currentRemainingSilverCapacity >= $nums*350)
		{	
         	$update['silver'] = $rows['silver'] + $nums;
			$wherearr['playersid'] = $playersid;
			$common->updatetable('player',$update,$wherearr);
		}else{
			$update['silver'] = $rows['silver'] + intval($currentRemainingSilverCapacity/350);
			$wherearr['playersid'] = $playersid;
			$common->updatetable('player',$update,$wherearr);
		}
	}*/
	
	//加同势力好友
	public static function addFriends($roleInfo) {
		global $common, $db;
		$value = array ();
		$result = $db->query ( "SELECT playersid,regionid FROM " . $common->tname ( 'player' ) . " WHERE ucid = '" . $roleInfo ['ucid'] . "'  LIMIT 1" );
		$rows_regionid = $db->fetch_array ( $result );
		$result = $db->query ( "SELECT * FROM " . $common->tname ( 'player' ) . " WHERE ucid in (SELECT fucid FROM " . $common->tname ( 'uc_friends' ) . " WHERE ucid = " . $roleInfo ['ucid'] . ") and regionid = " . $rows_regionid ['regionid'] . " " );
		//$rows_f = $db->fetch_array($result);
		//echo("SELECT * FROM ".$common->tname('player')." WHERE ucid in (SELECT fucid FROM ".$common->tname('uc_friends')." WHERE ucid = ".$roleInfo['ucid'].") and regionid = ".$rows_regionid['regionid']." ");
		while ( $rows = $db->fetch_array ( $result ) ) {
			$value [] = $rows;
		}
		
		if ($value == '') {
			return '';
		}
		for($i = 0; $i < count ( $value ); $i ++) {
			$social_user1 ['parent_playersid'] = $rows_regionid ['playersid'];
			$social_user1 ['type'] = 1;
			$social_user1 ['playersid'] = $value [$i] ['playersid'];
			$social_user1 ['add_time'] = time ();
			$common->inserttable ( 'social_user', $social_user1 );
			roleModel::updateFriendMemCache ( $rows_regionid ['playersid'] );
			
			$social_user2 ['parent_playersid'] = $value [$i] ['playersid'];
			$social_user2 ['type'] = 1;
			$social_user2 ['playersid'] = $rows_regionid ['playersid'];
			$social_user2 ['add_time'] = time ();
			$common->inserttable ( 'social_user', $social_user2 );
			roleModel::updateFriendMemCache ( $value [$i] ['playersid'] );
			
			$common->updatetable ( 'social', "friendsnum = friendsnum + 1", "playersid = '" . $rows_regionid ['playersid']."'" );
			$common->updatetable ( 'social', "friendsnum = friendsnum + 1", "playersid = '" . $value [$i] ['playersid']."'" );
		}
	}
	
	//修改好友内存
	public static function updateFriendMemCache($playersid) {
		//		global $common,$db,$mc;
		//		if (!empty($playersid))	{
		//			if (($roleInfo = $mc->get(MC.$playersid.'_friends'))) {
		//			   $roleInfo = array();
		//			   $roleInfo = roleModel::getTableRoleFriendsInfo($playersid);
		//		    }
		//		}
		

		$mc->delete ( MC . $playersid . '_friends' );
	}
	
	//奖励
	public static function setAward($roleInfo) {
		global $common, $db, $role_lang;
		$result = $db->query ( "SELECT * FROM " . $common->tname ( 'letters' ) . " WHERE type = 2 and id = " . $roleInfo ['inviteid'] . " and status = 0 LIMIT 1" );
		$rows = $db->fetch_array ( $result );
		//print_r($rows);
		if ($rows ['playersid'] == $roleInfo ['ucid']) {
			if ($rows ['genre'] == 2) {
				$result = $db->query ( "SELECT playersid FROM " . $common->tname ( 'player' ) . " WHERE ucid = " . $roleInfo ['ucid'] . " LIMIT 1" );
				$rows_p = $db->fetch_array ( $result );
				//roleModel::addCoins(30,$rows['fromplayersid']);
				//发送奖励消息
				$json ['playersid'] = 0;
				$json ['toplayersid'] = $rows ['fromplayersid'];
				$json ['message'] = '';
				$json ['type'] = '1';
				$json ['genre'] = '0'; //系统提示
				$json ['interaction'] = '0';
				$json ['message'] = $role_lang['setAward_1'] . $roleInfo ['nickname'] . $role_lang['setAward_2'];
				//$json = json_encode ( $json );
				
				lettersModel::addMessage ( $json );
				
				$common->deletetable ( 'letters', "playersid = '" . $roleInfo ['ucid']."'" );
				
				$social_user1 ['parent_playersid'] = $rows_p ['playersid'];
				$social_user1 ['type'] = 1;
				$social_user1 ['playersid'] = $rows ['fromplayersid'];
				$social_user1 ['add_time'] = time ();
				$common->inserttable ( 'social_user', $social_user1 );
				
				$social_user2 ['parent_playersid'] = $rows ['fromplayersid'];
				$social_user2 ['type'] = 1;
				$social_user2 ['playersid'] = $rows_p ['playersid'];
				$social_user2 ['add_time'] = time ();
				$common->inserttable ( 'social_user', $social_user2 );
				
				$common->updatetable ( 'social', 'friendsnum = friendsnum + 1 ', "playersid =" . $rows ['fromplayersid'] );
				$common->updatetable ( 'social', 'friendsnum = friendsnum + 1 ', "playersid =" . $rows_p ['playersid'] );
			} elseif ($rows ['genre'] == 3) {
				$result = $db->query ( "SELECT playersid FROM " . $common->tname ( 'player' ) . " WHERE ucid = " . $roleInfo ['ucid'] . " LIMIT 1" );
				$rows_p = $db->fetch_array ( $result );
				//roleModel::addCoins(30,$rows['fromplayersid']);
				

				//发送奖励消息
				//$json['playersid'] = 0;
				//$json['toplayersid'] = $rows['fromplayersid'];
				//$json['message'] = '';
				//$json['type'] = '1';
				//$json['genre'] = '0';           //系统提示
				//$json['interaction'] = '0';
				//$json['message'] = "您成功邀请好友".$roleInfo['nickname']." 奖励银两30。";
				//$json = json_encode($json);
				

				//lettersModel::addMessage($json);
				

				//发送礼物
				$social_trade ['fromplayersid'] = $rows ['fromplayersid'];
				$social_trade ['toplayersid'] = $rows_p ['playersid'];
				$social_trade ['interval_time'] = "0";
				$social_trade ['received_time'] = "";
				$social_trade ['return_count'] = "0";
				
				$social_trade ['gift_type'] = "1";
				$social_trade ['isrobbery'] = "";
				$social_trade ['history'] = $rows ['fromplayersid'] . "," . $rows_p ['playersid'];
				$social_trade ['create_time'] = time ();
				//$social_trade['status'] = 0;
				$social_trade ['sponsor_playersid'] = $rows ['fromplayersid'];
				$common->inserttable ( 'social_trade', $social_trade );
				
				//发送送礼消息
				$json_giff ['playersid'] = $rows ['fromplayersid'];
				$json_giff ['toplayersid'] = $rows_p ['playersid'];
				$json_giff ['message'] = '';
				$json_giff ['type'] = '1';
				$json_giff ['genre'] = '3'; //好友送礼
				$json_giff ['interaction'] = '1';
				
				//lettersModel::addMessage($json_giff);//送礼
				$result = $db->query ( "SELECT userid,nickname FROM " . $common->tname ( 'player' ) . " WHERE playersid = '" . $json_giff ['playersid'] . "'  LIMIT 1" );
				$rows_mc = $db->fetch_array ( $result );
				$letters_trade ['playersid'] = $json_giff ['toplayersid'];
				$letters_trade ['type'] = $json_giff ['type'];
				$letters_trade ['genre'] = $json_giff ['genre'];
				$letters_trade ['subject'] = $role_lang['setAward_3'];
				$letters_trade ['is_interaction'] = $json_giff ['interaction'];
				$letters_trade ['parameters'] = $rows_mc ['userid'] . "|" . $json_giff ['toplayersid'];
				$letters_trade ['fromplayersid'] = $json_giff ['playersid'];
				$letters_trade ['message'] = $rows_mc ['nickname'] . $role_lang['setAward_4'];
				$letters_trade ['status'] = "0";
				$letters_trade ['create_time'] = time ();
				$common->inserttable ( 'letters', $letters_trade );
				//
				$common->deletetable ( 'letters', "playersid = '" . $roleInfo ['ucid'] . "'" );
				
				$social_user1 ['parent_playersid'] = $rows_p ['playersid'];
				$social_user1 ['type'] = 1;
				$social_user1 ['playersid'] = $rows ['fromplayersid'];
				$social_user1 ['add_time'] = time ();
				$common->inserttable ( 'social_user', $social_user1 );
				
				$social_user2 ['parent_playersid'] = $rows ['fromplayersid'];
				$social_user2 ['type'] = 1;
				$social_user2 ['playersid'] = $rows_p ['playersid'];
				$social_user2 ['add_time'] = time ();
				$common->inserttable ( 'social_user', $social_user2 );
				
				$common->updatetable ( 'social', 'friendsnum = friendsnum + 1 ', "playersid =" . $rows ['fromplayersid'] );
				$common->updatetable ( 'social', 'friendsnum = friendsnum + 1 ', "playersid =" . $rows_p ['playersid'] );
			
			}
		}
	}
	
	//用户登出
	public static function logout() {
		global $mc, $common;
		@$playersid = $_SESSION ['playersid'];
		@$ucid = $_SESSION ['ucid'];
		if (! empty ( $playersid )) {
			$roleInfo ['playersid'] = $playersid;
			roleModel::getRoleInfo ( $roleInfo );
			//$updateRole['current_pos'] = $_SESSION['current_pos'];
			//$updateRole['food'] = $roleInfo['food'];
			//$updateRole['last_update_food'] = $roleInfo['last_update_food'];
			//$where['playersid'] = $playersid;
			//$common->updatetable('player',$updateRole,$where);
			$mc->delete ( MC . $playersid ); //清除角色信息
			$mc->delete ( MC . $playersid . '_general' ); //清除该角色将领信息
			$mc->delete ( MC . $playersid . '_messageStatus' ); //清除消息状态
			$mc->delete ( MC . $ucid . '_session' ); //清除登录后记录sessionid信息
			$mc->delete ( MC . 'items_' . $playersid ); //清除道具信息
			$mc->delete(MC.$playersid.'_xlw');            //清除训练位信息
			$mc->delete ( MC . $playersid . '_zllist' ); //清除占领列表信息
			$mc->delete ( MC . 'stageInfo_' . $playersid ); //清除玩家闯关表信息
			$mc->delete ( MC . "last_login_time_" . $roleInfo ['userid'] );
			$mc->delete ( MC . 'ld_' . $playersid ); //清除领地信息
			$mc->delete ( MC . $playersid . '_xsinfo' ); //清除显示按钮信息
			$mc->delete ( MC . $playersid . "_qstatus" ); //清除任务状态信息
			$mc->delete(MC.$playersid."_ydxx");
			$mc->delete(MC.$playersid."_zlydxx");	
			$mc->delete(MC.'ld_'.$playersid);        //清除此人领地数据		
			$mc->delete(MC.$playersid . '_chat_disble'); // 清除私聊信息
			$mc->delete(MC.'zglog_'.$playersid);        //清除战功记录信息
			$mc->delete(MC.$playersid.'_achieve_info'); //清除成就相关记录
		}
		session_destroy ();
	}
	
	// 废弃方法
	//用户抽奖
	public static function lottery($playersid, $id) {
		global $mc, $common, $db, $sys_lang;
		if (empty ( $_SESSION ['prize'] ) || $_SESSION ['day'] != 5) {
			$value ['status'] = 3;
			$value ['message'] = $sys_lang[7];
			$value ['rsn'] = intval ( _get ( 'ssn' ) );
			return $value;
		}
		$roleInfo ['playersid'] = $playersid;
		roleModel::getRoleInfo ( $roleInfo );
		$result = $_SESSION ['prize'];
		//$result = array_slice($prize,0,3);
		//0铜钱1军粮2银两
		$rand = rand ( 1, 100 );
		if ($rand <= 85 && $rand > 10) {
			$selected = $result [0];
			$num = 0;
		} elseif ($rand <= 10 && $rand > 5) {
			$selected = $result [1];
			$num = 1;
		} elseif ($rand <= 5) {
			$selected = $result [2];
			$num = 2;
		} else {
			$selected = $result [0];
			$num = 0;
		}
		//$selected = $result[$id-1];
		$chosed = $id - 1;
		$selectedItem = iDToRes ( $selected [0] ); //选中的资源
		$selectAmount = $selected [1]; //选中的数量
		$updateRole [$selectedItem] = $roleInfo [$selectedItem] + $selectAmount;
		$roleInfo [$selectedItem] = $updateRole [$selectedItem];
		$value ['yp'] = floor ( $roleInfo ['silver'] );
		$value ['tq'] = floor ( $roleInfo ['coins'] );
		$value ['jl'] = floor ( $roleInfo ['food'] );
		$updateRoleWhere ['playersid'] = $playersid;
		$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
		$common->updateMemCache ( MC . $playersid, $updateRole );
		$value ['status'] = 0;
		//$value['itemId'] = $selected[0];
		//$value['value'] = floor($updateRole[$selectedItem]);
		//shuffle($result);
		for($j = 0; $j < 3; $j ++) {
			if ($j != $num) {
				$res [] = $j;
			}
		}
		$k = 0;
		for($i = 0; $i < 3; $i ++) {
			if ($i != $chosed) {
				$result2 [] = array ($i => $result [$res [$k ++]] );
			}
		}
		//print_r($result2);
		$result1 = array ($chosed => $result [$num] );
		$result3 = $result2 [0] + $result2 [1] + $result1;
		$lorreryInfo = array ();
		for($i = 0; $i < count ( $result3 ); $i ++) {
			$lorreryInfo [] = array ('itemId' => $result3 [$i] [0], 'value' => $result3 [$i] [1] );
		}
		$value ['prize'] = $lorreryInfo;
		return $value;
	}
	
	public static function setbox($roleInfo) {
		global $mc, $common, $db;
		$date_now = date ( "Y-m-d H:i:s", time () );
		$year = (( int ) substr ( $date_now, 0, 4 )); //取得年份
		

		$month = (( int ) substr ( $date_now, 5, 2 )); //取得月份
		

		$day = (( int ) substr ( $date_now, 8, 2 )); //取得几号
		

		$date_now1 = mktime ( 0, 0, 0, $month, $day, $year );
		$date_now2 = mktime ( 23, 59, 59, $month, $day, $year );
		
		$playersid = $roleInfo ['playersid'];
		$memory_role = $mc->get ( MC . $playersid );
		if ($memory_role ['boxTime'] >= $date_now1 && $memory_role ['boxTime'] < $date_now2) {
			return "";
		} else {
			$updateMem ['boxOpen'] = "";
			$updateMem ['boxTime'] = $date_now1;
			$common->updateMemCache ( MC . $playersid, $updateMem );
			$common->updatetable ( 'player', "boxOpen = '',boxTime = '" . $date_now1 . "'", "playersid = '" . $playersid."'" );
			return "";
		}
	}
	
	//获取BOSS点信息
	public static function getBoss() {
		global $mc, $common, $db;
		if (! $rows = $mc->get ( '1_npc' )) {
			$sql = "SELECT * FROM " . $common->tname ( 'npc' );
			$result = $db->query ( $sql );
			while ( $rows = $db->fetch_array ( $result ) ) {
				$npcInfo [] = $rows;
				if ($rows ['npcType'] == 2) {
					$npcID [] = $rows ['npcid'];
				}
			}
			$mc->set ( '1_npc', $npcInfo, 0, 0 );
		} else {
			foreach ( $rows as $npcValue ) {
				if ($npcValue ['npcType'] == 2) {
					$npcID [] = $npcValue ['npcid'];
				}
			}
		}
		$mapData = mapData ();
		foreach ( $mapData as $mapKey => $mapValue ) {
			foreach ( $mapValue as $nodeKey => $nodeValue ) {
				if (in_array ( $nodeValue ['npcid'], $npcID )) {
					$nodeInfo [] = $mapKey . '_' . $nodeKey;
				}
			}
		}
		return implode ( ',', $nodeInfo );
	}
	
	public static function checkGenData($playersid) {
		global $mc, $db, $common;
		$genRows = $db->fetch_array ( $db->query ( "SELECT COUNT(intID) FROM " . $common->tname ( 'playergeneral' ) . " WHERE `playerid` = '$playersid' LIMIT 1" ) );
		if (empty ( $genRows )) {
			//生成一个主将
			$general ['general_sex'] = rand ( 0, 1 ); //性别1男性0女性
			if ($general ['general_sex'] == 1) {
				$general ['avatar'] = rand ( 1, 5 );
			} else {
				$general ['avatar'] = rand ( 6, 10 );
			}
			$firset_name_id = rand ( 1, 76 );
			//根据性别获取名称ID
			if ($general ['general_sex'] == 1) {
				$last_name_id = rand ( 1, 20 );
			} else {
				$last_name_id = rand ( 21, 40 );
			}
			$firstNameInfo = firstName ();
			//$rows_first_name = $db->fetch_array($db->query("SELECT * FROM ".$common->tname('general_firstname')." WHERE ID = '$firset_name_id' LIMIT 1"));
			$firs_name = $firstNameInfo [$firset_name_id];
			//$rows_last_name = $db->fetch_array($db->query("SELECT * FROM ".$common->tname('general_lastname')." WHERE ID = '$last_name_id' LIMIT 1"));
			$lastNameInfo = lastName ();
			$last_name = $lastNameInfo [$last_name_id];
			$general ['general_name'] = $firs_name . $last_name; //姓名;        
			//$general['general_name'] = genModel::generateName($playerid,$general['sex']);  //姓名
			$general ['understanding_value'] = rand ( 10, 15 ); //战技
			//$general['professional'] = rand(1,5);                 //将领职业 ，1，重甲将领  2，长枪将领   3，弓箭将领，4，轻骑将领，5，连弩将领 
			$general ['professional'] = 3; //默认弓箭兵
			$attributeValue = cityModel::generateBaseAttribute ( $general ['professional'] ); //生成属性值
			$general ['attack_value'] = $attributeValue ['attack_value']; //攻击值 
			$general ['defense_value'] = $attributeValue ['defense_value']; //防御值
			$general ['physical_value'] = $attributeValue ['physical_value']; //体力值
			$general ['agility_value'] = $attributeValue ['agility_value']; //敏捷值     
			//$general['motorize'] = $attributeValue['jdl'];                  //机动力
			$general ['general_life'] = 100 + $general ['physical_value']; //生命值
			//$general['is_lord'] = '1';         //设置为主将
			$general ['general_level'] = '1'; //将领级别
			$general ['playerid'] = $playersid; //将领所属玩家
			$general ['general_sort'] = '1'; //将领 排序
			//$general['general_state'] = '1';   //将领状态，默认为出征
			$general ['arm'] = $general ['professional'];
			$general ['professional_level'] = 0; //内力
			$gid = $common->inserttable ( 'playergeneral', $general );
			//print_r($general);
			if (empty ( $gid )) {
				$value ['status'] = 10;
			} else {
				//$value['status'] = 0; 
				$getInfo ['playersid'] = $playersid;
				$value = cityModel::getGeneralList ( $getInfo );
			}
			return $value;
		}
	}
	
	//验证用户名
	public static function is_regedit($roleInfo) {
		global $mc, $common, $db;
		$nickname = $common->shtmlspecialchars ( $roleInfo ['nickname'] );
		$result = $db->query ( "SELECT count(*) as icount FROM " . $common->tname ( 'user' ) . " WHERE username = '" . $nickname . "'" );
		$rows_count = $db->fetch_array ( $result );
		if ($rows_count ['icount'] > 0) {
			return 2;
		} elseif ($nickname != $roleInfo ['nickname']) {
			return 2;
		} else {
			return 0;
		}
	}
	
	//更新挖苦次数，过一天后清0
	public static function cleanZero($roleInfo) {
		global $mc, $common, $db;
	}
	
	/**
	 * 废弃的方法
	 * 
	 * @deprecated 
	 * @author kknd li
	 * 查询指定用户在一段时间内的充值金额
	 * 目前这个接口还没有处理完成
	 * @param int $userID
	 * @param string $startTime
	 * @param string $endTime
	 * @return int RMB单位元
	 */
	public static function getUserPay($userID, $startTime, $endTime) {
		global $common, $db;
		
		// 查询uc_coin和uc_fill表来得到最近30天充值的金额
		$whereStr = " where trade_time>'{$startTime}' and trade_time<='{$endTime}'";
		$whereStr .= " and ucid={$userID}";
		$whereStr .= " and result=0 and yb!=0";
		
		$sqlStr = "select sum(amount) from " . $common->tname ( 'uc_coin' ) . $whereStr;
		$result = $db->query ( $sqlStr );
		$uc_coin_amont = mysql_result ( $result, 0 );
		
		$money = $uc_coin_amont / RMB_TO_YUANBAO;
		return $money;
	}
	
	//获取列表id  1好友列表，2邻居列表，3仇人列表 ，4领地，5可占领列表
	// 提供对左邻右舍，前敌后仇的支持
	public static function getNeedId($type) {
		global $common, $db;
		$rows = array ();
		if ($type == 1 || $type == 3 || $type == 4) { // 默认1好友关系，4占领领地，3仇人关系
			$rows = roleModel::getTableRoleFriendsInfo ( $_SESSION ['playersid'], $type, true);
		} elseif ($type == 2) { // 邻居id
			$rows = socialModel::findRandFriend ( $_SESSION ['playersid'], 1, 0, true);
			//$rows = $rows ['list'];
		} elseif ($type == 5) { // 可占领
			$rows = socialModel::findRandFriend ( $_SESSION ['playersid'], 2, 0, true);
			//$rows = $rows ['list'];
		}
		$idList = array();
		foreach($rows as $key=>$info){
			$idList[]['id'] = $key;
		}
		return $idList;
		
	}
	
	//获取引导任务
	public static function hqydrw($playersid) {
		global $_SGLOBAL, $db, $common;
		$sql = "SELECT * FROM " . $common->tname ( 'quests' ) . " WHERE Is_Guide = '1'";
		$result = $db->query ( $sql );
		while ( $rows = $db->fetch_array ( $result ) ) {
			$FinishScript = $rows ['FinishScript'];
			$OnFinishParameter = $rows ['OnFinishParameter'];
			$FinishVar1 = $rows ['FinishVar1'];
			$FinishVar2 = $rows ['FinishVar2'];
			$FinishVar3 = $rows ['FinishVar3'];
			$FinishVar4 = $rows ['FinishVar4'];
			$PlayersId = $playersid;
			$QuestID = $rows ['QuestID'];
			$RepeatInterval = $rows ['RepeatInterval'];
			$is_guide = '1';
			$common->inserttable ( 'accepted_quests', array ('FinishScript' => $FinishScript, 'OnFinishParameter' => $OnFinishParameter, 'FinishVar1' => $FinishVar1, 'FinishVar2' => $FinishVar2, 'FinishVar3' => $FinishVar3, 'FinishVar4' => $FinishVar4, 'playersid' => $PlayersId, 'QuestID' => $QuestID, 'Qstatus' => 0, 'AcceptTime' => $_SGLOBAL ['timestamp'], 'RepeatInterval' => $RepeatInterval, 'is_guide' => $is_guide ) );
			getDataAboutLevel::addNewQuestsStatus ( $playersid, $QuestID, 1, $is_guide );
		}
	}
	
	//添加日常任务
	public static function tjrcrw($playersid) {
		global $_SGLOBAL, $db, $common, $mc, $_SC, $MemcacheList, $Memport;
		$db = new dbstuff;
		$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname'],'utf8');
		$db->query("set names 'utf8'");	
		$mc = new MemcacheAdapter_Memcached;
		for ($m = 0; $m < 3; $m++) {
		   if ($mc->addServer($MemcacheList[0], $Memport) == true) {
		   	    $mcConnect = true;
		   		break;
		   }
		}
		$rwinfo = getDataAboutLevel::$rcrwsj;
		$qstatusInfo = array();
		if (empty($rwinfo)) {
			if (! ($qStatus = $mc->get ( MC . $playersid . "_qstatus" ))) {
				$sql = "SELECT * FROM " . $common->tname ( 'quests_new_status' ) . " WHERE playersid = '$playersid' LIMIT 1";
				$result = $db->query ( $sql );
				$rows = $db->fetch_array ( $result );
				$qStatus = $rows ['qstatusInfo'];
				$mc->set(MC.$playersid."_qstatus",$qStatus,0,3600);
			}
			if (! empty ( $qStatus )) {
				$qstatusInfo = unserialize ( $qStatus );
			}			
		} else {
			$qstatusInfo = $rwinfo;
		}	
		foreach ($qstatusInfo as $key => $value) {
			$rwxx = ConfigLoader::GetQuestProto($key);
			if ($rwxx['RepeatInterval'] == 1) {
				return false;
				break;
			}
		}						
		$nowTime = $_SGLOBAL ['timestamp'];
		$rwData = ConfigLoader::GetAllQuests();   //获取任务基表数据
		foreach ( $rwData as $rows_hqrw ) {
			if ($rows_hqrw ['RepeatInterval'] == 1) {
				$FinishScript = $rows_hqrw ['FinishScript'];
				$OnFinishParameter = $rows_hqrw ['OnFinishParameter'];
				$FinishVar1 = $rows_hqrw ['FinishVar1'];
				$FinishVar2 = $rows_hqrw ['FinishVar2'];
				$FinishVar3 = $rows_hqrw ['FinishVar3'];
				$FinishVar4 = $rows_hqrw ['FinishVar4'];
				$PlayersId = $playersid;
				$QuestID = $rows_hqrw ['QuestID'];
				$RepeatInterval = $rows_hqrw ['RepeatInterval'];
				$Level_Max = $rows_hqrw ['Level_Max'];
				$Level_Min = $rows_hqrw ['Level_Min'];	
				$qstatusInfo = array($QuestID=>6) + $qstatusInfo;
				$insertArray[] = "('$playersid','$QuestID','0','$nowTime','$RepeatInterval','0')";			
				$FinishScript = $OnFinishParameter = $FinishVar1 = $FinishVar2 = $FinishVar3 = $FinishVar4 = $PlayersId = $QuestID = $RepeatInterval = $Level_Max = $Level_Min = null;
			}
		}
		getDataAboutLevel::$rcrwsj = $qstatusInfo;
		$updateStatus['qstatusInfo'] = serialize($qstatusInfo);
		$updateStatusWhere['playersid'] = $playersid;		
		$mc->set(MC.$playersid."_qstatus",$updateStatus['qstatusInfo'],0,3600);
		$common->updatetable('quests_new_status',$updateStatus,$updateStatusWhere);					
		$db->query ( "INSERT INTO " . $common->tname ( 'accepted_quests' ) ."(playersid,QuestID,Qstatus,AcceptTime,RepeatInterval,published) VALUES" . implode(',',$insertArray));		
		$db->close();
		$mc->close();	
	}
	//升级时获取日常任务
	public static function sjhqrcrw() {
		register_shutdown_function(array('roleModel', "exechqrcrw"));
	}
	
	//执行关闭获取日常任务
	public static function exechqrcrw() {
		roleModel::tjrcrw($_SESSION['playersid']);
	}	
	//获取日常任务 $sjhq 升级获取日常任务
	public static function hqrcrw($roleInfo,$sjhq = false) {
		global $_SGLOBAL, $db, $common, $mc;		
		$nowTime = $_SGLOBAL ['timestamp'];
		$playersid = $PlayersId = $roleInfo ['playersid'];
		$player_level = $roleInfo ['player_level'];		
		//$checkLog = $db->fetch_array ( $db->query ( "SELECT * FROM " . $common->tname ( 'daily_quests_logs' ) . " WHERE playersid = '$playersid' LIMIT 1" ) );
		$qxyt = $_SESSION['qxyt'];  //是否全新一天
		$_SESSION['qxyt'] = 0;  //重置该值
		$rwData = ConfigLoader::GetAllQuests();   //获取任务基表数据
		if ($player_level > 7) {
			//获取任务状态信息
			if (! ($qStatus = $mc->get ( MC . $PlayersId . "_qstatus" ))) {
				$sql = "SELECT * FROM " . $common->tname ( 'quests_new_status' ) . " WHERE playersid = '$PlayersId' LIMIT 1";
				$result = $db->query ( $sql );
				$rows = $db->fetch_array ( $result );
				$qStatus = $rows ['qstatusInfo'];
				$mc->set(MC.$playersid."_qstatus",$qStatus,0,3600);
			}	
			$qstatusInfo = array();
			if (! empty ( $qStatus )) {
				$qstatusInfo = unserialize ( $qStatus );
				if (count ( $qstatusInfo ) == 0) {
					return false;
				}
				foreach ( $qstatusInfo as $key => $qValue ) {
					if (in_array ( $qValue, array (4, 5 ) ) && ! empty ( $key )) {
						$Qarray [] = $key;
					}
				}
			}			
			//$rwData = getDataAboutLevel::hqrwjbsj (); //获取任务基表数据			
			$rwDataChose = array ();
			if ($sjhq == true ) {
				foreach ( $rwData as $rwrDataValue ) {
					if ($rwrDataValue ['Level_Max'] >= $player_level && $rwrDataValue ['Level_Min'] <= $player_level && $rwrDataValue ['RepeatInterval'] == 1) {
						$rwDataChose [] = $rwrDataValue ['QuestID'];
					}
				}
				$sl = count ( $rwDataChose );
				if ($sl > 1) {
					$chosedKey = array_rand ( $rwDataChose, 2 );
					$q1 = $rwDataChose [$chosedKey [0]];
					$q2 = $rwDataChose [$chosedKey [1]];
				} else {
					if ($sl == 0) {
						$q1 = $q2 = 0;
					} else {
						$q1 = $q2 = $rwDataChose [0];
					}
				}
				foreach ( $rwData as $rows_hqrw ) {
					if ($rows_hqrw ['RepeatInterval'] == 1) {
						if ($roleInfo['sc_level'] >= 12 && $roleInfo['ld_level'] >= 12 && $roleInfo['tjp_level'] >= 12 && $roleInfo['jg_level'] >= 12 && $roleInfo['djt_level'] >= 12 && $rows_hqrw ['QuestID'] == 2003000) {
							continue;
						}
						$FinishScript = $rows_hqrw ['FinishScript'];
						$OnFinishParameter = $rows_hqrw ['OnFinishParameter'];
						$FinishVar1 = $rows_hqrw ['FinishVar1'];
						$FinishVar2 = $rows_hqrw ['FinishVar2'];
						$FinishVar3 = $rows_hqrw ['FinishVar3'];
						$FinishVar4 = $rows_hqrw ['FinishVar4'];
						$PlayersId = $playersid;
						$QuestID = $rows_hqrw ['QuestID'];
						$RepeatInterval = $rows_hqrw ['RepeatInterval'];
						$Level_Max = $rows_hqrw ['Level_Max'];
						$Level_Min = $rows_hqrw ['Level_Min'];
						if ($QuestID == $q1 || $QuestID == $q2) {
						    $qstatusInfo = array($QuestID=>5) + $qstatusInfo;
							$insertArray[] = "('$PlayersId','$QuestID','0','$nowTime','$RepeatInterval','1')";
						} else {
							$qstatusInfo = array($QuestID=>6) + $qstatusInfo;
							$insertArray[] = "('$PlayersId','$QuestID','0','$nowTime','$RepeatInterval','0')";
						}
						$FinishScript = $OnFinishParameter = $FinishVar1 = $FinishVar2 = $FinishVar3 = $FinishVar4 = $PlayersId = $QuestID = $RepeatInterval = $Level_Max = $Level_Min = null;
					}
				}
				getDataAboutLevel::$rcrwsj = $qstatusInfo;
				$updateStatus['qstatusInfo'] = serialize($qstatusInfo);
				$updateStatusWhere['playersid'] = $playersid;		
				$mc->set(MC.$playersid."_qstatus",$updateStatus['qstatusInfo'],0,3600);
				$common->updatetable('quests_new_status',$updateStatus,$updateStatusWhere);					
				$db->query ( "INSERT INTO " . $common->tname ( 'accepted_quests' ) ."(playersid,QuestID,Qstatus,AcceptTime,RepeatInterval,published) VALUES" . implode(',',$insertArray));
				//echo "INSERT INTO " . $common->tname ( 'accepted_quests' ) ."(FinishScript,OnFinishParameter,FinishVar1,FinishVar2,FinishVar3,FinishVar4,playersid,QuestID,Qstatus,AcceptTime,RepeatInterval,published,Level_Min,Level_Max) VALUES" . implode(',',$insertArray);
				return true;
			} else {
				if ($qxyt == 0 || isset ( $_SESSION ['debug'] ) ) {
					return false;
				} else {					
					$Qarray = array ();						
        			//查找目前已有的日常任务
					$sqlrw = "SELECT QuestID,Qstatus,published FROM ".$common->tname('accepted_quests')." WHERE playersid = '$playersid' && RepeatInterval = 1";							
					$resultrw = $db->query($sqlrw);
					$rcrw = array(); 
					$oldrw = array();
					while ($rowsRW = $db->fetch_array($resultrw)) {
						if ($rowsRW['Qstatus'] != 1 && $rowsRW['published'] != 1) {
							$qstatusInfo = array($rowsRW['QuestID']=>6) + $qstatusInfo;  //重置状态 
						} 
						if ($rowsRW['Qstatus'] == 1 && $rowsRW['published'] == 1){
							$oldrw[] = $rowsRW['QuestID'];
						}
						$rcrw[] = $rowsRW['QuestID'];						
					}
					/*if (empty($rcrw)) {
						return false;
					}*/
					//print_r($rcrw);	
					$easy = array(2002000,2003001,2003002,2004015,2005001,2004000,2004001,2004003,2004004,2001018,2004017,2004012,2001000,2003000);
					$insertArray = array();		
					//$yyrwsl = count($rcrw); //已有任务数量
					//$j = 0;
					$choseQ = array();		
					foreach ($rwData as $choseValue) {
						if ($choseValue['RepeatInterval'] == 1 && $choseValue['Level_Min'] <= $roleInfo['player_level'] && $choseValue['Level_Max'] >= $roleInfo['player_level'] && !in_array($choseValue['QuestID'],$oldrw) && in_array($choseValue['QuestID'],$easy)) {
							$choseQ[$choseValue['QuestID']] = $choseValue['Level_Min'];
						}
						if (empty($choseQ))	{
							if ($choseValue['RepeatInterval'] == 1 && $choseValue['Level_Min'] <= $roleInfo['player_level'] && $choseValue['Level_Max'] >= $roleInfo['player_level'] && !in_array($choseValue['QuestID'],$oldrw)) {
								$choseQ[$choseValue['QuestID']] = $choseValue['Level_Min'];
							}								
						}						
					}
					$rwsl = count($choseQ);
					$oldrwsl = count($oldrw);
					$chosedQuests = array();
					if ($oldrwsl < 3) {
						if ($rwsl > 3) {							
							$rwsl = 3 - $oldrwsl;
							if ($rwsl <= 0) {
								$chosedQuests = array();
							} else {
								$chosedQuests = array_rand($choseQ,$rwsl);
							}
						} else {	
							$chosedQuests = array_rand($choseQ,$rwsl);
						}										
					}
					//处理只有一个任务时数据格式不正确
					if (!empty($chosedQuests) && !is_array($chosedQuests)) {
						$chosedQuests = array($chosedQuests);
					}
					$chosedQuests = array_merge($chosedQuests,$oldrw);
					$chosedItem = implode(',',$chosedQuests);					
					foreach ( $rwData as $rows_hqrw ) {
						if ($rows_hqrw ['RepeatInterval'] == 1) {
							if ($roleInfo['sc_level'] >= 12 && $roleInfo['ld_level'] >= 12 && $roleInfo['tjp_level'] >= 12 && $roleInfo['jg_level'] >= 12 && $roleInfo['djt_level'] >= 12 && $rows_hqrw ['QuestID'] == 2003000) {
								continue;
							}							
							$QuestID = $rows_hqrw ['QuestID'];
							if (!in_array($rows_hqrw['QuestID'],$rcrw)) {								
								$FinishScript = $rows_hqrw ['FinishScript'];
								$OnFinishParameter = $rows_hqrw ['OnFinishParameter'];
								$FinishVar1 = $rows_hqrw ['FinishVar1'];
								$FinishVar2 = $rows_hqrw ['FinishVar2'];
								$FinishVar3 = $rows_hqrw ['FinishVar3'];
								$FinishVar4 = $rows_hqrw ['FinishVar4'];
								$PlayersId = $playersid;								
								$RepeatInterval = $rows_hqrw ['RepeatInterval'];
								$Level_Max = $rows_hqrw ['Level_Max'];
								$Level_Min = $rows_hqrw ['Level_Min'];
								if (in_array($rows_hqrw['QuestID'],$chosedQuests)) {
									$qstatusInfo = array($QuestID=>5) + $qstatusInfo;
									$insertArray[] = "('$PlayersId','$QuestID','0','$nowTime','$RepeatInterval','1')";
								} else {
									$qstatusInfo = array($QuestID=>6) + $qstatusInfo;
									$insertArray[] = "('$PlayersId','$QuestID','0','$nowTime','$RepeatInterval','0')";
								}							
							} else {
								if (in_array($rows_hqrw['QuestID'],$oldrw)) {
									$qstatusInfo = array($QuestID=>1) + $qstatusInfo;
								} elseif (in_array($rows_hqrw['QuestID'],$chosedQuests)) {
									$qstatusInfo = array($QuestID=>5) + $qstatusInfo;
								} else {
									$qstatusInfo = array($QuestID=>6) + $qstatusInfo;
								}																
							}
							$FinishScript = $OnFinishParameter = $FinishVar1 = $FinishVar2 = $FinishVar3 = $FinishVar4 = $PlayersId = $QuestID = $RepeatInterval = $Level_Max = $Level_Min = null;					
						}
					}
					//print_r($qstatusInfo);
					getDataAboutLevel::$rcrwsj = $qstatusInfo;
					$updateStatus['qstatusInfo'] = serialize($qstatusInfo);
					$updateStatusWhere['playersid'] = $playersid;		
					$mc->set(MC.$playersid."_qstatus",$updateStatus['qstatusInfo'],0,3600);
					if (!empty($insertArray)) {	
						$common->updatetable('quests_new_status',$updateStatus,$updateStatusWhere);												
						$db->query ( "INSERT INTO " . $common->tname ( 'accepted_quests' ) ."(playersid,QuestID,Qstatus,AcceptTime,RepeatInterval,published) VALUES" . implode(',',$insertArray));
					}
					//处理只有一个任务时数据格式不正确
					if (empty($chosedItem)) {
						$chosedItem = 0;
					}
					if (empty($oldrw)) {
						$oldrwItem = 0;
					} else {
						$oldrwItem = implode(',',$oldrw);
					}
					//清理任务进度
					$db->query("UPDATE ".$common->tname('accepted_quests')." SET Qstatus = (IF ( (QuestID IN ($oldrwItem)),1 , 0)), ExtraData = (IF ( (QuestID IN ($oldrwItem)),ExtraData , '')) ,readStatus = 0, published = (IF ( (QuestID IN ($chosedItem)),1 , 0)) WHERE playersid = '$playersid' && RepeatInterval = 1");					
					//echo "UPDATE ".$common->tname('accepted_quests')." SET Qstatus = (IF ( (QuestID IN ($oldrwItem)),1 , 0)), ExtraData = (IF ( (QuestID IN ($oldrwItem)),ExtraData , '')) ,readStatus = 0, published = (IF ( (QuestID IN ($chosedItem)),1 , 0)) WHERE playersid = '$playersid' && RepeatInterval = 1";
					zydModel::loginSzyd();  //新的一天更新资源点数据
					return true;
				}
			}
		} else {
			return false;
		}
	}
	
	//如果非法返回1  英文
	public static function is_checkNameEnglish($nickname) {
		global $_SGLOBAL, $db, $common;
		if (preg_match ( '/^[A-Za-z0-9]+$/', $nickname )) {
			//$value = roleModel::getChinaCount($nickname);
			$num = strlen ( $nickname );
			if ($num > 12) {
				return 1;
			}
			$value = '';
			$value = 'jofgame|JOFGAME|ADMIN|Admin|xtl|system|admin|Administrator|administrator|game|master|GAMEMASTER|GameMaster|GM|Gm|gm|gm|Client|Server|CS|Cs|cs|cS|KEFU|kefu|Kefu|KeFu|TEsT|tESt|test|test|TeSt|tEST|Test|sf|wg|&|dishun|Dishun|DISHUN|DiShun|16大|18摸|6-4tianwang|89-64cdjp|ADMIN|AIDS|Aiort墓地|ai滋|Arqus会议场|asshole|Atan的移动石|A片|Baichi|Baopi|Bao皮|bastard|Bc|biaozi|Biao子|bignews|bitch|Bi样|BLOWJOB|boxun|B样|caoB|caobi|cao你|cao你妈|cao你大爷|cha你|chinaliberal|chinamz|chinesenewsnet|Clockgemstone|cnd|creaders|Crestbone|dafa|dajiyuan|damn|dfdz|DICK|dpp|EVENT|falu|falun|falundafa|fa轮|Feelmistone|Fku|FLG|flg|freechina|freedom|freenet|Fuck|fuck|GAMEMASTER|gan你|GCD|gcd|GM|Gruepin|HACKING|hongzhi|hrichina|http|huanet|hypermart.net|incest|item|J8|JB|jiangdongriji|jian你|jiaochuang|jiaochun|jiba|jinv|Ji女|Kao|KISSMYASS|lihongzhi|Mai骚|making|minghui|minghuinews|nacb|naive|Neckromancer|nmis|paper64|peacehall|PENIS|playboy|pussy|qiangjian|Rape|renminbao|renmingbao|rfa|safeweb|saobi|sb|SEX|sex|sf|SHIT|shit|simple|SUCK|sucker|svdc|System|taip|TEST|The9|The9City|tibetalk|TMD|TNND|triangle|triangleboy|Tringel|UltraSurf|unixbox|ustibet|voa|voachinese|wangce|WEBZEN|WG|wstaiji|xinsheng|yuming|zhengjian|zhengjianwang|zhenshanren|zhuanfalunADMIN|AIDS|AIORT墓地|AI滋|ARQUS会议场|ASSHOLE|ATAN的移动石|A片|BAICHI|BAOPI|BAO皮|BASTARD|BC|BIAOZI|BIAO子|BIGNEWS|BITCH|BI样|BLOWJOB|BOXUN|B样|CAOB|CAOBI|CAO你|CC小雪|CHA你|CHINALIBERAL|CHINAMZ|CHINESENEWSNET|CLOCKGEMSTONE|CND|CREADERS|CRESTBONE|DAFA|DAJIYUAN|DAMN|DFDZ|DICK|DPP|EVENT|FALU|FALUN|FALUNDAFA|FA轮|FEELMISTONE|FKU|FLG|FREECHINA|FREEDOM|FREENET|FUCK|GAMEMASTER|GAN你|GCD|GM|GRUEPIN|HACKING|HONGZHI|HRICHINA|HTTP|HUANET|HYPERMART.NET|INCEST|ITEM|J8|JB|JIANGDONGRIJI|JIAN你|JIAOCHUANG|JIAOCHUN|JIBA|JINV|JI女|KAO|KISSMYASS|㎏|LIHONGZHI|MAI骚|MAKING|MINGHUI|MINGHUINEWS|㎎|㎜|NACB|NAIVE|NECKROMANCER|NMIS|PAPER64|PEACEHALL|PENIS|PLAYBOY|PUSSY|QIANGJIAN|RAPE|RENMINBAO|RENMINGBAO|RFA|SAFEWEB|SAOBI|SB|SEX|SF|SHIT|SIMPLE|SUCK|SUCKER|SVDC|SYSTEM|TAIP|TEST|THE9|THE9CITY|TIBETALK|TMD|TNND|TRIANGLE|TRIANGLEBOY|TRINGEL|ULTRASURF|UNIXBOX|USTIBET|VOA|VOACHINESE|WANGCE|WEBZEN|WG|WSTAIJI|WWW|WWW.|XINSHENG|YUMING|ZHENGJIAN|ZHENGJIANWANG|ZHENSHANREN|ZHUANFALUN|bcd.s.59764.com|kkk.xaoh.cn|www.xaoh.cn|zzz.xaoh.cn|aa.yazhousetu.hi.9705.net.cn|eee.xaoh.cn|lll.xaoh.cn|jj.pangfangwuyuetian.hi.9705.net.cn|rrr.xaoh.cn|ooo.xaoh.cn|www.zy528.com|aaad.s.59764.com|www.dy6789.cn|aaac.s.51524.com|208.43.198.56|166578.cn|www.wang567.com|www.bin5.cn|www.sanjidianying.com.cn|www.anule.cn|%77%77%77%2E%39%37%38%38%30%38%2E%63%6F%6D|www.976543.com|www.50spcombaidu1828adyou97sace.co.cc|chengrenmanhua.1242.net.cn|qingsewuyuetian.1174.net.cn|lunlidianyingxiazai.1174.net.cn|siwameitui.1274.net.cn|niuniujidi.1174.net.cn|xiao77.1243.net.cn|woyinwose.1243.net.cn|dingxiang.1249.net|cnaicheng.1174.net.cn|1234chengren.1249.net.cn|sewuyuetian.1174.net.cn|huangsexiaoshuo.1242.net.cn|lunlidianying.1274.net.cn|xingqingzhongren.1174.net.cn|chengrenwangzhi.1242.net.cn|xiao77luntan.1249.net.cn|dingxiang.1243.net.cn|11xp.1243.net.cn|baijie.1249.net.cn|sewuyuetian.1274.net.cn|meiguiqingren.1274.net.cn|tb.hi.4024.net.cn|www.91wangyou.com|www.wow366.cn|www.yxnpc.com|www.365jw.com|58.253.67.74|www.978808.com|www.sexwyt.com|7GG|www.567yx.com|131.com|bbs.7gg.cn|www.99game.net|ppt.cc|www.zsyxhd.cn|www.foyeye.com|www.23nice.com.cn|www.maituan.com|www.ylteam.cn|www.yhzt.org|vip886.com|www.neicehao.cn|bbs.butcn.com|www.gamelifeclub.cn|consignment5173|www.70yx.com|www.legu.com|ko180|bbs.pkmmo|whoyo.com|www.2q5q.com|www.zxkaku.cn|www.gw17173.cn|www.315ts.net|qgqm.org|17173dl.net|i9game.com|365gn|158le.com|1100y.com|bulaoge.com|17youle.com|reddidi.com.cn|icpcn.com|ul86.com|showka8.com|szlmgh.cn|bbs.766.com|www.766.com|91bysd.cn|jiayyou.cn|gigabyte.cn|duowan|wgxiaowu.com|youxiji888.cn|yz55.cn|Carrefour|51jiafen.cn|597ft.com|itnongzhuang.com2y7v.cnhwxvote.cn|92klgh.cn|xiaoqinzaixian.cn|661661.com|haosilu.com|dl.com|xl517.com|sjlike.com|tont.cn|xq-wl.cn|feitengdl.com|bz176.com|dadati.com|asgardcn.com|dolbbs.com|okaygood.cn|1t1t.com|jinpaopao.com|blacksee.com.cn|1qmsj.com|202333.com|luoxialu.cn|37447.cn|567567aa.cn|09city.com|71ka.com|fy371.com|365tttyx.com|host800.com|lybbs.info|ys168.com|88mysf.com|5d6d.com|id666.uqc.cn|stlmbbs.cn|pcikchina.com|lxsm888.com|wangyoudl.com|chinavfx.net|zxsj188.com|wg7766.cn|e7sw.cn|jooplay.com|gssmtt.com|likeko.com|tlyx-game.cn|wy33.com|zy666.net|newsmth.net|l2jsom.cn|13888wg.com|qtoy.com|1000scarf.com|digitallongking.com|zaixu.net|ncyh.cn|888895.com|ising99.com|pcikcatv.2om|parke888.com|01gh.com|gogo.net|uu1001.com|wy724.com|prettyirene.net|yaokong7.com|zzmysf.com|52sxhy.cn|92wydl.com|g365.net|pkmmo.com|52ppsa.cn|bl62.com|canyaa.com|lordren.com|xya3.cn|5m5m5m.com|www.gardcn.com|www.sf766.com.cn|ent365.com|18900.com|7mmo.com|cdream.com|wy3868.com|nbfib.cn|17173yxdl.cn|luosisa.cn|haouse.cn|54hero.com|ieboy.cn|geocities.com|xiuau.cn|cvceo.com|fxjsqc.com|thec.cn|c5c8.cn|a33.com|qqsg.org|my3q.com|51juezhan.com|kartt.cn|hexun.com|15wy.com|13ml.net|homexf.cn|xyxgh.com|jdyou.com|langyou.info|duowan.com|8188mu.com|tianlong4f.cn|yeswm.com|wgbobo.cn|haog8.cn|47513.cn|92ey.com|hao1788.co|mgjzybj.com|xdns.eu|shenycs.co|mpceggs.cn|kod920.cn|njgamecollege.org|51hdw.com|025game.cn|bibidu.com|bwowd.com|3kwow.com|zx002.com|bazhuwg.cn|991game.com|zuanshi1000.cn|10mb.cn|Huihuangtx.com|chongxianmu.cn|any2000.com|99sa.com|zhidian8.com|t9wg.cn|bobaoping|qixingnet.com|88kx.com|00sm.cn|moyi520.cn|id666.com|fisonet.com|0571qq.com|173at.com|pk200.com|2feiche.cn|jjdlw.com|xyq2sf.com|69nb.com|txwsWind|xxx|XXX|jiayyou.com';
			if (preg_match ( "/$value/i", $nickname )) {
				return 1;
			} else {
				return 0;
			}
			//echo($value);
			$value = explode ( "|", $value );
			for($i = 0; $i < count ( $value ); $i ++) {
				$badkey = $value [$i]; //"敏感词|毛泽东|敏感词C";
				$string = $nickname; //"毛泽东表";  
				similar_text ( $badkey, $string, $similarity_pst );
				echo ($similarity_pst);
				if ($similarity_pst > 30) {
					return 1;
				}
			}
			return 0;
		} else {
			return 1;
		}
	}
	
	//如果非法返回1 中文
	public static function is_checkName($nickname) {
		global $_SGLOBAL, $db, $common;
		//echo($nickname);
		if (preg_match ( '/^[\x{4e00}-\x{9fa5}]+$/u', $nickname )) {
			$value = roleModel::getChinaCount ( $nickname );
			$num = count ( $value );
			if ($num > 6) {
				//echo('长度');
				return 1;
			}
			$value = '';
			/*$sql = "SELECT username FROM ".$common->tname('glb'); 
			$res = $db->query($sql);
			while ($rows = $db->fetch_array($res)) {
				$value .= $rows['username'].'|';
			}
			echo($value);*/
			$value = '毛泽东|周恩来|刘少奇|朱德|彭德怀|林彪|刘伯承|陈毅|贺龙|聂荣臻|徐向前|罗荣桓|叶剑英|李大钊|陈独秀|孙中山|孙文|孙逸仙|邓小平|陈云|江泽民|李鹏|朱镕基|李瑞环|尉健行|李岚清|胡锦涛|罗干|温家宝|吴邦国|曾庆红|贾庆林|黄菊|吴官正|李长春|吴仪|回良玉|曾培炎|周永康|曹刚川|唐家璇|华建敏|陈至立|陈良宇|张德江|张立昌|俞正声|王乐泉|刘云山|王刚|王兆国|刘淇|贺国强|郭伯雄|胡耀邦|王乐泉|王兆国|周永康|李登辉|连战|陈水扁|宋楚瑜|吕秀莲|郁慕明|蒋介石|蒋中正|蒋经国|马英九|习近平|李克强|吴帮国|无帮国|无邦国|无帮过|瘟家宝|假庆林|甲庆林|假青林|离长春|习远平|袭近平|李磕墙|贺过墙|和锅枪|粥永康|轴永康|肘永康|周健康|粥健康|周小康|布什|布莱尔|小泉|纯一郎|萨马兰奇|安南|阿拉法特|普京|默克尔|克林顿|里根|尼克松|林肯|杜鲁门|赫鲁晓夫|列宁|斯大林|马克思|恩格斯|金正日|金日成|萨达姆|胡志明|西哈努克|希拉克|撒切尔|阿罗约|曼德拉|卡斯特罗|富兰克林|华盛顿|艾森豪威尔|拿破仑|亚历山大|路易|拉姆斯菲尔德|劳拉|鲍威尔|奥巴马|本拉登|奥马尔|柴玲|达赖喇嘛|江青|张春桥|姚文元|王洪文|东条英机|希特勒|墨索里尼|冈村秀树|冈村宁次|高丽朴|赵紫阳|王丹|沃尔开西|李洪志|李大师|赖昌星|马加爵|班禅|额尔德尼|山本五十六|阿扁|阿扁万岁|热那亚|六四|六四运动|美国之音|密宗|民国|民进党|民运|民主|民主潮|摩门教|纳粹|南华早报|南蛮|明慧网|起义|亲民党|瘸腿帮|人民报|法轮功|法轮大法|打倒共产党|台独万岁|圣战|示威|台独|台独分子|台联|台湾民国|台湾岛国|台湾国|台湾独立|太子党|天安门事件|屠杀|小泉|新党|新疆独立|新疆分裂|新疆国|疆独|西藏独立|西藏分裂|西藏国|藏独|藏青会|藏妇会|学潮|学运|一党专政|一中一台|两个中国|一贯道|游行|圆满|造反|真善忍|镇压|政变|政治|政治反对派|政治犯|中共|共产党|反党|反共|政府|民主党|中国之春|转法轮|自焚|共党|共匪|苏家屯|基地组织|塔利班|东亚病夫|支那|高治联|高自联|专政|专制|核工业基地|核武器|铀|原子弹|氢弹|导弹|核潜艇|大参考|小参考|国内动态清样|道教|多维|佛教|佛祖|释迦牟尼|如来|阿弥陀佛|观世音|普贤|文殊|地藏|河殇|回教|密宗|摩门教|穆罕默德|穆斯林|升天|圣母|圣战|耶和华|耶稣|伊斯兰|真主安拉|白莲教|天主教|基督教|东正教|大法|法轮|法轮功|瘸腿帮|真理教|真善忍|转法轮|自焚|走向圆满|黄大仙|风水|跳大神|神汉|神婆|真理教|大卫教|阎王|黑白无常|牛头马面|藏独|高丽棒子|回回|疆独|蒙古鞑子|台独|台独分子|台联|台湾民国|西藏独立|新疆独立|南蛮|老毛子|回民吃猪肉|谋杀|杀人|吸毒|贩毒|赌博|拐卖|走私|卖淫|造反|监狱|强奸|轮奸|抢劫|先奸后杀|下注|押大|押小|抽头|坐庄|赌马|赌球|筹码|老虎机|轮盘赌|安非他命|大麻|可卡因|海洛因|冰毒|摇头丸|杜冷丁|鸦片|罂粟|迷幻药|白粉|嗑药|吸毒|屙|爱滋|淋病|梅毒|爱液|屄|逼|臭机八|臭鸡巴|吹喇叭|吹箫|催情药|屌|肛交|肛门|龟头|黄色|机八|机巴|鸡八|鸡巴|机掰|机巴|鸡叭|鸡鸡|鸡掰|鸡奸|妓女|奸|茎|精液|精子|尻|口交|滥交|乱交|轮奸|卖淫|屁眼|嫖娼|强奸|强奸犯|情色|肉棒|乳房|乳峰|乳交|乳头|乳晕|三陪|色情|射精|手淫|威而钢|威而柔|伟哥|性高潮|性交|性虐|性欲|穴|颜射|阳物|一夜情|阴部|阴唇|阴道|阴蒂|阴核|阴户|阴茎|阴门|淫|淫秽|淫乱|淫水|淫娃|淫液|淫汁|淫穴|淫洞|援交妹|做爱|梦遗|阳痿|早泄|奸淫|性欲|性交|白痴|笨蛋|屄|逼|变态|婊子|操|操她妈|操妳妈|操你|操你妈|操他妈|草你|肏|册那|侧那|测拿|插|蠢猪|荡妇|发骚|废物|干她妈|干妳|干妳娘|干你|干你妈|干你妈B|干你妈b|干你妈逼|干你娘|干他妈|狗娘养的|滚|鸡奸|贱货|贱人|靠|烂人|老母|老土|妈比|妈的|马的|妳老母的|妳娘的|你妈逼|破鞋|仆街|去她妈|去妳的|去妳妈|去你的|去你妈|去死|去他妈|日|日你|赛她娘|赛妳娘|赛你娘|赛他娘|骚货|傻B|傻比|傻子|上妳|上你|神经病|屎|屎妳娘|屎你娘|他妈的|王八蛋|我操|我日|乡巴佬|猪猡|屙|干|尿|掯|屌|操|骑你|湿了|操你|操他|操她|骑你|骑他|骑她|欠骑|欠人骑|来爽我|来插我|干你|干他|干她|干死|干爆|干机|机叭|臭鸡|臭机|烂鸟|览叫|阳具|肉棒|肉壶|奶子|摸咪咪|干鸡|干入|小穴|强奸|插你|插你|爽你|爽你|干干|干X|我操|他干|干它|干牠|干您|干汝|干林|操林|干尼|操尼|我咧干|干勒|干我|干到|干啦|干爽|欠干|狗干|我干|来干|轮干|轮流干|干一干|援交|骑你|我操|轮奸|鸡奸|奸暴|再奸|我奸|奸你|奸你|奸他|奸她|奸一奸|淫水|淫湿|鸡歪|仆街|臭西|尻|吊|遗精|烂逼|大血比|叼你妈|靠你妈|草你|干你|日你|插你|奸你|戳你|逼你老母|挨球|我日你|草拟妈|卖逼|狗操卖逼|奸淫|日死|奶子|阴茎|奶娘|他娘|她娘|你妈了妹|逼毛|插你妈|叼你|渣波波|缔顺|帝顺|蒂顺|系统消息|午夜|看下|草泥马|法克鱿|雅蠛蝶|潜烈蟹|菊花蚕|尾申鲸|吉跋猫|搞栗棒|吟稻雁|达菲鸡|群P|马勒戈壁|双飞|史进|朱武|陈达|杨春|鲁智深|李忠|周通|林冲|柴进|朱贵|杜迁|宋万|杨志|索超|朱仝|雷横|刘唐|吴用|阮小二|阮小五|阮小七|公孙胜|白胜|曹正|宋江|宋清|武松|张青|孙二娘|施恩|孔明|孔亮|燕顺|王英|郑天寿|花荣|黄信|秦明|吕方|郭盛|石勇|李立|李俊|童威|童猛|薛永|张横|穆弘|穆春|戴宗|李逵|张顺|萧让|金大坚|侯健|欧鹏|蒋敬|马麟|陶宗旺|朱富|李云|杨林|邓飞|孟康|裴宣|杨雄|石秀|时迁|杜兴|李应|扈三娘|解珍|解宝|乐和|顾大嫂|孙新|邹渊|邹润|孙立|汤隆|呼延灼|韩滔|彭圯|凌振|徐宁|樊瑞|项充|李兖|段景住|卢俊义|燕青|蔡福|蔡庆|宣赞|关胜|郝思文|王定六|安道全|焦挺|魏定国|鲍旭|郁保四|董平|张清|龚旺|丁得孙|皇甫端|管理|管里|管理员|服务管理|服务器|活动管理员|官方|维护|系统|系统公告|审查|巡查|监督|监管|游戏管理员|助理|客户服务|客服|服务天使|测试|辅助程序|运营|运营者|运营组|运营商|运营长|运营官|运营人|私服|私人服务器|外挂|开房|鞴|鐾|瘭|镳|黪|瘥|觇|孱|廛|蒇|冁|羼|螭|傺|瘛|舂|艟|瘳|雠|搋|嘬|辏|殂|汆|爨|榱|毳|皴|蹉|鹾|纛|髑屙民|莪|苊|鲕|鲼|瀵|酆|幞|黻|呒|黼|阝|阝月|彀|觏|毂|汩|罟|嘏|鲴|宄|庋|刿|虢|馘|撖|夯|薅|曷|翮|曷|翮|觳|冱|怙|戽|祜|瓠|鹱|溷|耠|锪|劐|蠖|丌|乩|赍|殛|蕺|掎|彐|芰|跽|鲚|葭|恝|湔|搛|鲣|鞯|囝|趼|醮|疖|苣|屦|醵|蠲|桊|鄄|谲|爝|麇|贶|悝|喟|仂|泐|鳓|诔|酹|嫠|黧|蠡|醴|鳢|轹|詈|跞|奁|臁|蚍|埤|罴|鼙|庀|仳|圮綦|屺|綮|汔|碛|葜|佥|岍|愆|搴|钤|掮|凵|肷|椠|戕|锖|檠|苘|謦|庆红|跫|銎|邛|筇|蛩鼽|诎|麴|黢|劬|朐|璩|蘧|衢|蠼毵|糁|21世纪中国基金会|墓地|周恩來|碡|籀|朱駿|朱狨基|朱容基|朱溶剂|朱熔基|朱镕基|邾|猪操|猪聋畸|猪毛|猪毛1|舳|瘃|躅|翥|專政|颛|丬|隹|窀|卓伯源|倬|斫|诼|髭|鲻|子宫|秭|訾|自焚|自民党|自慰|自已的故事|自由民主论坛|总理|偬|诹|陬|鄹|鲰|躜|缵|作爱|作秀|阼|祚|做爱|阿扁萬歲|阿萊娜|啊無卵|埃裏克蘇特勤|埃斯萬|艾麗絲|愛滋|愛滋病|垵|暗黑法師|嶴|奧克拉|奧拉德|奧利弗|奧魯奇|奧倫|奧特蘭|巴倫侍從|巴倫坦|白立樸|白夢|白皮書|班禪|寶石商人|保釣|鮑戈|鮑彤|鮑伊|暴風亡靈|暴亂|暴熱的戰士|暴躁的城塔野獸|暴躁的警衛兵靈魂|暴躁的馬杜克|北大三角地論壇|北韓|北京當局|北美自由論壇|貝尤爾|韝|逼樣|比樣|蹕|颮|鑣|婊子養的|賓周|冰後|博訊|不滅帝王|不爽不要錢|布萊爾|布雷爾|蔡崇國|蔡啓芳|黲|操鶏|操那嗎B|操那嗎逼|操那嗎比|操你媽|操你爺爺|曹長青|曹剛川|草|草你媽|草擬媽|册那娘餓比|插那嗎B|插那嗎逼|插那嗎比|插你媽|插你爺爺|覘|蕆|囅|閶|長官沙塔特|常勁|朝鮮|車侖|車侖女幹|沉睡圖騰|陳炳基|陳博志|陳定南|陳建銘|陳景俊|陳菊|陳軍|陳良宇|陳蒙|陳破空|陳水扁|陳唐山|陳希同|陳小同|陳宣良|陳學聖|陳一諮|陳總統|諶|齔|櫬|讖|程凱|程鐵軍|鴟|痴鳩|痴拈|遲鈍的圖騰|持不同政見|赤色騎士|赤色戰士|處女膜|傳染性病|吹簫|春夏自由論壇|戳那嗎B|戳那嗎逼|戳那嗎比|輳|鹺|錯B|錯逼|錯比|錯那嗎B|錯那嗎逼|錯那嗎比|達夫警衛兵|達夫侍從|達癩|打飛機|大參考|大東亞|大東亞共榮|大鶏巴|大紀元|大紀元新聞網|大紀園|大家論壇|大奶媽|大史記|大史紀|大衛教|大中國論壇|大中華論壇|大衆真人真事|紿|戴維教|戴相龍|彈劾|氹|蕩婦|導師|盜竊犯|德維爾|登輝|鄧笑貧|糴|迪裏夏提|覿|地下教會|帝國主義|電視流氓|叼你媽|釣魚島|丁關根|東北獨立|東部地下水路|東方紅時空|東方時空|東南西北論談|東社|東升|東條|東條英機|東突暴動|東突獨立|東土耳其斯坦|東西南北論壇|東亞|東院看守|動亂|鬥士哈夫拉蘇|鬥士霍克|獨裁|獨裁政治|獨夫|獨立臺灣會|俄國|鮞|發楞|發掄|發掄功|發倫|發倫功|發輪|發論|發論公|發論功|發騷|發正念|法~倫|法~淪|法~綸|法~輪|法~論|法爾卡|法掄|法掄功|法侖|法淪|法綸|法輪大法|法輪功|法十輪十功|法謫|法謫功|反封鎖|反封鎖技術|反腐敗論壇|反人類|反社會|方勵之|防衛指揮官|放蕩|飛揚論壇|廢墟守護者|費鴻泰|費良勇|分隊長施蒂文|粉飾太平|糞便|鱝|豐饒的果實|風雨神州|風雨神州論壇|封從德|封殺|封印的靈魂騎士|馮東海|馮素英|紱|襆|嘸|傅作義|幹bi|幹逼|幹比|幹的你|幹幹幹|幹她|幹你|幹你老比|幹你老母|幹你娘|幹全家|幹死|幹死你|幹他|幹一家|趕你娘|岡巒|剛比|剛比樣子|崗哨士兵|肛門|高麗棒子|高文謙|高薪養廉|高自聯|膏藥旗|戈瑞爾德|戈揚|鴿派|歌功頌德|格雷關卡排名管理者|格魯|格魯城鎮移動|鯁|工自聯|弓雖|共産|共産黨|共産主義|共黨|共軍|共榮圈|緱|狗誠|狗狼養的|狗娘養的|狗養|狗雜種|覯|轂|古龍祭壇|骨獅|鯝|鴰|詿|關卓中|貫通兩極法|廣聞|嬀|龜兒子|龜公|龜孫子|龜頭|龜投|劌|緄|滾那嗎|滾那嗎B|滾那嗎錯比|滾那嗎老比|滾那嗎瘟比|鯀|咼|郭俊銘|郭羅基|郭岩華|國家安全|國家機密|國軍|國賊|哈爾羅尼|頇|韓東方|韓聯潮|韓正|漢奸|顥|灝|河殤|賀國强|賀龍|黑社會|黑手黨|紅燈區|紅色恐怖|紅炎猛獸|洪傳|洪興|洪哲勝|黌|鱟|胡緊掏|胡錦滔|胡錦淘|胡景濤|胡喬木|胡總書記|湖岸護衛兵|湖岸警衛兵|湖岸哨兵隊長|護法|鸌|華建敏|華通時事論壇|華夏文摘|華語世界論壇|華岳時事論壇|懷特|鍰|皇軍|黃伯源|黃慈萍|黃禍|黃劍輝|黃金幼龍|黃菊|黃片|黃翔|黃義交|黃仲生|回民暴動|噦|繢|毀滅步兵|毀滅騎士|毀滅射手|昏迷圖騰|混亂的圖騰|鍃|活動|擊倒圖騰|擊傷的圖騰|鶏8|鶏八|鶏巴|鶏吧|鶏鶏|鶏奸|鶏毛信文匯|鶏女|鶏院|姬勝德|積克館|賫|鱭|賈廷安|賈育台|戔|監視塔|監視塔哨兵|監視塔哨兵隊長|鰹|韉|簡肇棟|建國黨|賤B|賤bi|賤逼|賤比|賤貨|賤人|賤種|江八點|江羅|江綿恒|江戲子|江則民|江澤慧|江賊|江賊民|薑春雲|將則民|僵賊|僵賊民|講法|蔣介石|蔣中正|降低命中的圖騰|醬猪媳|撟|狡猾的達夫|矯健的馬努爾|嶠|教養院|癤|揭批書|訐|她媽|届中央政治局委員|金槍不倒|金堯如|金澤辰|巹|錦濤|經文|經血|莖候佳陰|荊棘護衛兵|靖國神社|舊斗篷哨兵|齟|巨槌騎兵|巨鐵角哈克|鋸齒通道被遺弃的骷髏|鋸齒通道骷髏|屨|棬|絕望之地|譎|軍妓|開苞|開放雜志|凱奧勒尼什|凱爾本|凱爾雷斯|凱特切爾|砍翻一條街|看中國|闞|靠你媽|柯賜海|柯建銘|科萊爾|克萊恩|克萊特|克勞森|客戶服務|緙|空氣精靈|空虛的伊坤|空虛之地|恐怖主義|瞘|嚳|鄺錦文|貺|昆圖|拉姆斯菲爾德|拉皮條|萊特|賴士葆|蘭迪|爛B|爛逼|爛比|爛袋|爛貨|濫B|濫逼|濫比|濫貨|濫交|勞動教養所|勞改|勞教|鰳|雷尼亞|誄|李紅痔|李洪寬|李繼耐|李蘭菊|李老師|李錄|李祿|李慶安|李慶華|李淑嫻|李鐵映|李旺陽|李小鵬|李月月鳥|李志綏|李總理|李總統|裏菲斯|鱧|轢|躒|奩|連方瑀|連惠心|連勝德|連勝文|連戰|聯總|廉政大論壇|煉功|兩岸關係|兩岸三地論壇|兩個中國|兩會|兩會報道|兩會新聞|廖錫龍|林保華|林長盛|林佳龍|林信義|林正勝|林重謨|躪|淩鋒|劉賓深|劉賓雁|劉剛|劉國凱|劉華清|劉俊國|劉凱中|劉千石|劉青|劉山青|劉士賢|劉文勝|劉文雄|劉曉波|劉曉竹|劉永川|鷚|龍虎豹|龍火之心|盧卡|盧西德|陸委會|輅|呂京花|呂秀蓮|亂交|亂倫|亂輪|鋝|掄功|倫功|輪大|輪功|輪奸|論壇管理員|羅福助|羅幹|羅禮詩|羅文嘉|羅志明|腡|濼|洛克菲爾特|媽B|媽比|媽的|媽批|馬大維|馬克思|馬良駿|馬三家|馬時敏|馬特斯|馬英九|馬永成|瑪麗亞|瑪雅|嗎的|嗎啡|勱|麥克斯|賣逼|賣比|賣國|賣騷|賣淫|瞞報|毛厠洞|毛賊|毛賊東|美國|美國參考|美國佬|美國之音|蒙獨|蒙古達子|蒙古獨|蒙古獨立|禰|羋|綿恒|黽|民國|民進黨|民聯|民意論壇|民陣|民主墻|緡|湣|鰵|摸你鶏巴|莫偉强|木子論壇|內褲|內衣|那嗎B|那嗎逼|那嗎錯比|那嗎老比|那嗎瘟比|那娘錯比|納粹|奶頭|南大自由論壇|南蠻子|鬧事|能樣|尼奧夫|倪育賢|鯢|你媽|你媽逼|你媽比|你媽的|你媽了妹|你說我說論壇|你爺|娘餓比|捏你鶏巴|儂著岡巒|儂著卵拋|奴隸魔族士兵|女幹|女主人羅姬馬莉|儺|諾姆|潘國平|蹣|龐建國|泡沫經濟|轡|噴你|皮條客|羆|諞|潑婦|齊墨|齊諾|騎你|磧|僉|鈐|錢達|錢國梁|錢其琛|膁|槧|錆|繰|喬石|喬伊|橋侵襲兵|譙|鞽|篋|親美|親民黨|親日|欽本立|禽獸|唚|輕舟快訊|情婦|情獸|檾|慶紅|丘垂貞|詘|去你媽的|闃|全國兩會|全國人大|犬|綣|瘸腿幫|愨|讓你操|熱比婭|熱站政論網|人民報|人民大會堂|人民內情真相|人民真實|人民之聲論壇|人權|日本帝國|日軍|日內瓦金融|日你媽|日你爺爺|日朱駿|顬|乳頭|乳暈|瑞士金融大學|薩達姆|三K黨|三個代表|三級片|三去車侖工力|毿|糝|騷B|騷棒|騷包|騷逼|騷棍|騷貨|騷鶏|騷卵|殺你全家|殺你一家|殺人犯|傻鳥|煞筆|山口組|善惡有報|上訪|上海幫|上海孤兒院|厙|社會主義|射了還說要|灄|詵|神經病|諗|生孩子沒屁眼|生命分流的圖騰|澠|聖射手|聖戰|盛華仁|濕了還說不要|濕了還說要|釃|鯴|石化圖騰|石拳戰鬥兵|時代論壇|時事論壇|鰣|史萊姆|史萊姆王|士兵管理員瓦爾臣|世界經濟導報|事實獨立|侍從貝赫爾特|侍從倫斯韋|貰|攄|數據中國|雙十節|氵去車侖工力|稅力|司馬晋|司馬璐|司徒華|私處|思科羅|斯諾|斯皮爾德|四川獨|四川獨立|四人幫|宋書元|藪|蘇菲爾|蘇拉|蘇南成|蘇紹智|蘇特勒守護兵|蘇特勤|蘇特勤護衛兵|蘇特勤魔法師|蘇曉康|蘇盈貴|蘇貞昌|誶|碎片製造商人馬克|碎片製造商人蘇克|孫大千|孫中山|他媽|他媽的|他嗎的|他母親|塔內|塔烏|鰨|闥|臺盟|臺灣帝國|臺灣獨立|臺灣獨|臺灣共産黨|臺灣狗|臺灣建國運動組織|臺灣民國|臺灣青年獨立聯盟|臺灣政論區|臺灣自由聯盟|鮐|太監|泰奴橋警衛兵|泰奴橋掠奪者|湯光中|唐柏橋|鞀|謄|天安門|天安門錄影帶|天安門事件|天安門屠殺|天安門一代|天閹|田紀雲|齠|鰷|銚|庭院警衛兵|統獨|統獨論壇|統戰|頭領奧馬|頭領墳墓管理員|圖書管理員卡特|屠殺|團長戈登|團員馬爾汀|摶|鼉|籜|膃|外交論壇|外交與方略|晚年周恩來|綰|萬里|萬潤南|萬維讀者論壇|萬曉東|王寶森|王超華|王輔臣|王剛|王涵萬|王滬寧|王軍濤|王樂泉|王潤生|王世堅|王世勛|王秀麗|王兆國|網禪|網特|猥褻|鮪|溫B|溫逼|溫比|溫家寶|溫元凱|閿|無界瀏覽器|吳百益|吳敦義|吳方城|吳弘達|吳宏達|吳仁華|吳淑珍|吳學燦|吳學璨|吳育升|吳志芳|西藏獨|吸收的圖騰|吸血獸|覡|洗腦|系統|系統公告|餼|郤|下賤|下體|薟|躚|鮮族|獫|蜆|峴|現金|現金交易|獻祭的圖騰|鯗|項懷誠|項小吉|嘵|小B樣|小比樣|小參考|小鶏鶏|小靈通|小泉純一郎|謝長廷|謝深山|謝選駿|謝中之|辛灝年|新觀察論壇|新華舉報|新華內情|新華通論壇|新疆獨|新生網|新手訓練營|新聞出版總署|新聞封鎖|新義安|新語絲|信用危機|邢錚|性愛|性無能|修煉|頊|虛弱圖騰|虛無的飽食者|徐國舅|許財利|許家屯|許信良|諼|薛偉|學潮|學聯|學運|學自聯|澩|閹狗|訁|嚴家其|嚴家祺|閻明複|顔清標|顔慶章|顔射|讞|央視內部晚會|陽具|陽痿|陽物|楊懷安|楊建利|楊巍|楊月清|楊周|姚羅|姚月謙|軺|搖頭丸|藥材商人蘇耐得|藥水|耶穌|野鶏|葉菊蘭|夜話紫禁城|一陀糞|伊莎貝爾|伊斯蘭|伊斯蘭亞格林尼斯|遺精|議長阿茵斯塔|議員斯格文德|异見人士|异型叛軍|异議人士|易丹軒|意志不堅的圖騰|瘞|陰部|陰唇|陰道|陰蒂|陰戶|陰莖|陰精|陰毛|陰門|陰囊|陰水|淫蕩|淫穢|淫貨|淫賤|尹慶民|引導|隱者之路|鷹眼派氏族|硬直圖騰|憂鬱的艾拉|尤比亞|由喜貴|游蕩的僵尸|游蕩的士兵|游蕩爪牙|游錫坤|游戲管理員|友好的魯德|幼齒|幼龍|于幼軍|余英時|漁夫菲斯曼|輿論|輿論反制|傴|宇明網|齬|飫|鵒|元老蘭提沃德|圓滿|緣圈圈|遠志明|月經|韞|雜種|鏨|造愛|則民|擇民|澤夫|澤民|賾|賊民|譖|扎卡維是英雄|驏|張伯笠|張博雅|張鋼|張健|張林|張清芳|張偉國|張溫鷹|張昭富|張志清|章孝嚴|帳號|賬號|招鶏|趙海青|趙建銘|趙南|趙品潞|趙曉微|趙紫陽|貞操|鎮壓|爭鳴論壇|正見網|正義黨論壇|鄭寶清|鄭麗文|鄭義|鄭餘鎮|鄭源|鄭運鵬|政權|政治反對派|縶|躑|指點江山論壇|騭|觶|躓|中毒的圖騰|中毒圖騰|中俄邊界|中國復興論壇|中國共産黨|中國孤兒院|中國和平|中國論壇|中國社會進步黨|中國社會論壇|中國威脅論|中國問題論壇|中國移動通信|中國真實內容|中國之春|中國猪|中華大地|中華大衆|中華講清|中華民國|中華人民實話實說|中華人民正邪|中華時事|中華養生益智功|中華真實報道|中央電視臺|鐘山風雨論壇|周鋒鎖|周守訓|朱鳳芝|朱立倫|朱溶劑|猪聾畸|主攻指揮官|主義|助手威爾特|專制|顓|轉化|諑|資本主義|鯔|子宮|自民黨|自由民主論壇|總理|諏|鯫|躦|纘|作愛|做愛';
			if (preg_match ( "/$value/i", $nickname )) {
				return 1;
			} else {
				return 0;
			}
			//echo($value);
			$value = explode ( "|", $value );
			for($i = 0; $i < count ( $value ); $i ++) {
				$badkey = $value [$i]; //"敏感词|毛泽东|敏感词C";     
				$string = $nickname; //"毛泽东表";  
				similar_text ( $badkey, $string, $similarity_pst );
				if ($similarity_pst > 60) {
					return 1;
				}
			}
			return 0;
			/*if(preg_match("/$badkey/i",$string)){     
		    	return 1;//echo "对不起，含有含有敏感字符，不允许发表";
		    }else{     
		    	return 0;
		    }*/
		} else {
			//echo('不是汉字');
			return 1;
		}
	}
	
	//中文英文数字
	public static function is_checkNameAll($nickname,$jcmgc) {
		global $_SGLOBAL, $db, $common, $mc;
		//echo($nickname);
		$num = roleModel::getChinaCount ( $nickname );
		//echo($num);
		//heroCommon::insertLog('num:'.$num.'+++++++++++++++++');
		if ($num > 36) {
			//echo('长度');
			return 1;
		}
		$value = '';
		if ($jcmgc == true && $num > 1) {
			$checkstring = roleModel::removeTag($nickname);
			include (S_ROOT . 'lang_' . LANG_FLAG . DIRECTORY_SEPARATOR . 'mgc.php');

			$glarray = mgc ();
				//$mc->set ( MC . 'mgc', $glarray, 0, 0 );
			//}
			foreach ( $glarray as $glarrayValue ) {
				if (preg_match ( "/$glarrayValue/i", $checkstring )) {
					return 1;
					break;
				}
			}
		}
		return 0;
	}
	
	//长度
	public static function getChinaCount($str) {
		$len = strlen ( $str );
		$i = 0;
		$j = 0;
		while ( $i < $len ) {
			if (preg_match ( '/^[\x{4e00}-\x{9fa5}]+$/u', $str [$i] )) {
				$i += 3;
			} else {
				$i += 1;
			}
			$j ++;
		}
		return $j;
	}
	
	public static function utf8Substr($str, $from, $len) {
		return preg_replace ( '#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $from . '}' . '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $len . '}).*#s', '$1', $str );
	}
	
	
	//废弃的方法
	/*
	public static function getvip($playersid) {
		global $_SGLOBAL, $db, $common;
		$level = 0;
		$vip = 0;
		$vip_end_time = 0;
		$month = date ( "m", $_SGLOBAL ['timestamp'] );
		$sql = "select player_level,vip,vip_end_time from ol_player where playersid = '" . $playersid . "' limit 1";
		$arr = $db->query ( $sql );
		$tmp_level = $db->fetch_array ( $arr );
		if (! empty ( $tmp_level )) {
			$level = $tmp_level ['player_level'];
			$vip = $tmp_level ['vip'];
			if ($tmp_level ['vip_end_time'] == 0) {
				$vip_end_time = time () + 600;
			} else {
				$vip_end_time = $tmp_level ['vip_end_time'];
			}
		}
		
		if ($vip_end_time > $_SGLOBAL ['timestamp']) { //有效期
			$vip_start_time = $vip_end_time - 2592000;
			$rq1 = date ( "Y-m-d H:i:s", $vip_start_time );
			$rq2 = date ( "Y-m-d H:i:s", $vip_end_time );
			$month1 = date ( "m", $vip_start_time );
			$month2 = date ( "m", $vip_end_time );
			if ($playersid != '') {
				$sql = "select sum(yb) as yb from ol_uc_coin where ucid = " . $ucid . " and trade_time >= '" . $rq1 . "' and trade_time <= '" . $rq2 . "'";
				$arr = mysql_query ( $sql );
				$total_info = mysql_fetch_array ( $arr );
				$total_1 = $total_info ['yb'];
				
				$sql = "select sum(yb) as yb from ol_uc_fill where user_id = " . $ucid . " and trade_time >= '" . $rq1 . "' and trade_time <= '" . $rq2 . "'";
				$arr = mysql_query ( $sql );
				$total_info = mysql_fetch_array ( $arr );
				$total_2 = $total_info ['yb'];
				
				$total = $total_1 + $total_2;
				$rmb = intval ( $total / 10 );
				$new_vip_time = time () + 2592000;
				if ($rmb >= 1 && $rmb < 20) {
					$vip_level = 1;
				} elseif ($rmb >= 20 && $rmb < 100) {
					$vip_level = 2;
				} elseif ($rmb >= 100 && $rmb < 500) {
					$vip_level = 3;
				} elseif ($rmb > 500) {
					$vip_level = 4;
				}
				if ($vip_level > $vip) {
					mysql_query ( "update ol_player set vip = " . $vip_level . ", vip_end_time = '" . $new_vip_time . "' where ucid = " . $ucid );
				}
			}
		} else { //无效期
			$vip_start_time = $vip_end_time;
			$rq1 = date ( "Y-m-d H:i:s", $vip_start_time );
			$rq2 = date ( "Y-m-d H:i:s", time () );
			if ($ucid != '') {
				$sql = "select sum(yb) as yb from ol_uc_coin where ucid = " . $ucid . " and trade_time >= '" . $rq1 . "' and trade_time <= '" . $rq2 . "'";
				$arr = mysql_query ( $sql );
				$total_info = mysql_fetch_array ( $arr );
				$total_1 = $total_info ['yb'];
				
				$sql = "select sum(yb) as yb from ol_uc_fill where user_id = " . $ucid . " and trade_time >= '" . $rq1 . "' and trade_time <= '" . $rq2 . "'";
				$arr = mysql_query ( $sql );
				$total_info = mysql_fetch_array ( $arr );
				$total_2 = $total_info ['yb'];
				
				$total = $total_1 + $total_2;
				$rmb = intval ( $total / 10 );
				$new_vip_time = time () + 2592000;
				if ($rmb >= 1 && $rmb < 20) {
					$vip_level = 1;
				} elseif ($rmb >= 20 && $rmb < 100) {
					$vip_level = 2;
				} elseif ($rmb >= 100 && $rmb < 500) {
					$vip_level = 3;
				} elseif ($rmb > 500) {
					$vip_level = 4;
				}
				mysql_query ( "update ol_player set vip = " . $vip_level . ", vip_end_time = '" . $new_vip_time . "' where ucid = " . $ucid );
			}
		}
		}*/

	//获取铜钱增长率
	public static function tqzzl($dj) {
    	if ($dj > 0 && $dj < 31) {
    		$rate = 0.1;
    	} elseif ($dj > 30 && $dj < 61) {
    		$rate = 0.2;
    	} elseif ($dj > 60 && $dj < 71) {
    		$rate = 0.3;
    	} else {
    		$rate = 0;
    	}
		return $rate;
	}
	
	//获取显示按钮信息
	/*public static function hqxsanid($playersid, $level) {
		global $db, $common, $mc;
		if ($level < 6) {
			if (! ($xsinfo = $mc->get ( MC . $playersid . '_xsinfo' ))) {
				$sql = "SELECT * FROM " . $common->tname ( 'button_id' ) . " WHERE playersid = '$playersid' LIMIT 1";
				$result = $db->query ( $sql );
				$rows = $db->fetch_array ( $result );
				if (! empty ( $rows )) {
					$xs = unserialize ( $rows ['openID'] );
					$mc->set ( MC . $playersid . '_xsinfo', $rows ['openID'], 0, 3600 );
					return $xs;
				} else {
					return false;
				}
			} else {
				$xs = unserialize ( $xsinfo );
				return $xs;
			}
		} else {
			return false;
		}
	}*/
	/*public static function hqxsanid($level) {
        if ($level < 6) {
        	if (isset($_REQUEST['ospt'])) {
 	       		$anArray = array(
	        		1=>array('userHome'=>array(0,1,2,3,5,7,10,11,12,14,15,16,17,18,19,20,21),'userHome2'=>array(array(0,1,2,3,7,10),array(3,5,5,5,6,0))),
	        		2=>array('userHome'=>array(0,2,3,5,7,12,14,15,16,17,18,19,20,21),'userHome2'=>array(array(0,1,2,3,18,7),array(3,5,5,5,5,6))),
	        		3=>array('userHome'=>array(0,2,3,5,7,12,14,15,16,17,18,19,20,21),'userHome2'=>array(array(1,2,3,18,7),array(5,5,5,5,6))),
	        		4=>array('userHome'=>array(0,3,5,7,12,14,15,16,17,18,19,20,21),'userHome2'=>array(array(1,2,3,18,7),array(5,5,5,5,6))),
	        		5=>array('userHome'=>array(),'userHome2'=>array(array(7),array(6)))
	        		);          		
        	} else {
	       		$anArray = array(
	        		1=>array('userHome'=>array(0,1,2,3,4,6,7,9,10,12,13,14,16,17,18,21),'userHome2'=>array(array(0,1,2,3,7,10),array(3,5,5,5,6,0))),
	        		2=>array('userHome'=>array(0,1,2,3,4,7,9,12,13,16,17,18,21),'userHome2'=>array(array(0,1,2,3,18,7),array(3,5,5,5,5,6))),
	        		3=>array('userHome'=>array(0,1,2,3,4,7,9,12,13,16,17,18,21),'userHome2'=>array(array(1,2,3,18,7),array(5,5,5,5,6))),
	        		4=>array('userHome'=>array(0,1,2,3,4,7,9,12,16,17,18,21),'userHome2'=>array(array(1,2,3,18,7),array(5,5,5,5,6))),
	        		5=>array('userHome'=>array(),'userHome2'=>array(array(7),array(6)))
	        		);       		
        	}
        	return $anArray[$level];
        } else {
        	return false;
        }
	}*/ 	
	
	//更新按钮ID信息
	public static function gxanid($playersid, $newID) {
		global $db, $common, $mc;
		if (! empty ( $newID )) {
			$common->updatetable ( 'button_id', array ('openID' => serialize ( $newID ) ), "playersid = '$playersid' LIMIT 1" );
			//$_SESSION['xs'] = $newID;
			$mc->set ( MC . $playersid . '_xsinfo', serialize ( $newID ), 0, 3600 );
		}
	}
	
	//创建角色返回信息
	public static function createRoleLogin($roleInfo) {
		global $common, $db, $mc, $_SC, $_SGLOBAL;
		$nowTime = $_SGLOBAL ['timestamp'];
		$nickname = $roleInfo ['nickname'];
		$inviteid = $roleInfo ['inviteid'];
		$userid = $roleInfo ['userid'];
		//$result = roleModel::login ( $roleInfo );		
        $returnValue['status'] = 0;
		$returnValue ['jssycs'] = 0;		
		$returnValue ['loginKey'] = _get ( 'loginKey' );
		$_SESSION ['player_level'] = 1;
		$returnValue ['roleName'] = stripcslashes($roleInfo ['nickname']);
		$returnValue ['sexId'] = intval ( $roleInfo ['sex'] );
		$returnValue ['jy'] = intval ( $roleInfo ['current_experience_value'] ); //经验
		$returnValue ['jl'] = floor ( $roleInfo ['food'] );
		$returnValue ['level'] = intval ( $roleInfo ['player_level'] );
		$returnValue['lsdj'] = addLifeCost(1);
		$returnValue ['xjjy'] = cityModel::getPlayerUpgradeExp ( $roleInfo ['player_level'] ); //下级经验
		$returnValue ['bbgs'] = intval ( $roleInfo ['bagpack'] );
		$returnValue ['xlwkqyb'] = 1; //开启训练位所需元宝
		$returnValue ['yb'] = intval ( $roleInfo ['ingot'] ); //元宝
		$returnValue ['tq'] = floor ( $roleInfo ['coins'] ); //铜钱
		$returnValue ['yp'] = intval ( $roleInfo ['silver'] ); //银两
		

		//		$returnValue ['vc'] = array (array ('d' => 30, 'c' => '1_19' ), array ('d' => 30, 'c' => '20_99' ), array ('d' => 30, 'c' => '100_499' ), array ('d' => 30, 'c' => '500_100000' ) ); //vip数值
		$returnValue ['jlbhyb'] = 20; //将领编号需要的元宝数
		$returnValue ['jltqhyp'] = array (array ('jl' => 1, 'tq' => 100, 'yp' => 1 ), array ('jl' => 5, 'tq' => 500, 'yp' => 5 ), array ('jl' => 8, 'tq' => 800, 'yp' => 8 ), array ('jl' => 15, 'tq' => 1500, 'yp' => 15 ) ); //军粮铜钱换银两数
		$returnValue ['zbqhsx'] = 6; //装备强化上限
		$returnValue ['jlhf'] = 5; //军粮恢复时间分为单位		
		//生成vip
		$returnValue ['vip'] = $roleInfo['vip'] > 0 ? $roleInfo['vip'] : 0;
		/* if(isset($roleInfo['vip_end_time'])) { */
		/* 	$returnValue ['vdl'] = $roleInfo['vip_end_time']; */
		/* } */
		$returnValue ['pid'] = $roleInfo ['playersid'];
		$returnValue ['kzwjsx'] = guideValue ( $roleInfo ['djt_level'] ); //可招武将上限
		$returnValue ['zljbxz'] = 5; //占领级别限制 
		$returnValue ['zcyp'] = 5; //侦查所需银票个数
		//$returnValue ['bxlist'] = cityModel::getGemBoxInfo ( $roleInfo );
		$loginAward = cityModel::getLoginAwardInfo($roleInfo);
		$returnValue['qrlist'] = $loginAward['qrlist'];
		$returnValue['qrjlts2'] = $loginAward['qrjlts2'];
		$returnValue['qrjllq2'] = $loginAward['qrjllq2'];
		$jlsx = foodUplimit ( $roleInfo ['player_level'] );
		$returnValue ['jlsx'] = $jlsx;		
		$returnValue ['jlzf'] = 2;
		$mg = jwmc ( intval ( $roleInfo ['mg_level'] ) );
		$next_mg = jwmc ( intval ( $roleInfo ['mg_level'] ) + 1 );		
		$returnValue ['dqjw'] = $mg ['mc']; //当前爵位
		$returnValue ['jwid'] = 1;
		$returnValue ['xjjw'] = $next_mg ['mc']; //下级爵位
		$returnValue ['dqjc'] = intval(jwjc($roleInfo ['mg_level'])); //当前加成，返回一个整数，比如40%，就返回40
		$returnValue ['xjjc'] = intval(jwjc($roleInfo ['mg_level'] + 1)); //下级加成
		$returnValue ['swz'] = $roleInfo ['prestige']; //声望值
		$returnValue ['swsx'] = $next_mg ['sw']; //声望上限
		$returnValue ['jsyp'] = 5;
		$getInfo ['playersid'] = $roleInfo ['playersid'];
		$ufInfo = array ();
		$ufInfo ['playersid'] = $_SESSION ['playersid'];
		$ufInfo ['ucid'] = $_SESSION ['ucid'];
		$generalInfo = cityModel::getGeneralList ( $getInfo, 1, 1 );
		if ($generalInfo ['status'] == 0) {
			$returnValue ['generals'] = $generalInfo ['generals'];
			//$returnValue['hirableGeneral'] = $generalInfo['hirableGeneral'];
			$returnValue ['nextUpdate'] = $generalInfo ['nextUpdate'];
			$returnValue ['jlsl'] = 1;
		} else {
			$returnValue ['generals'] = array ();
			$returnValue ['jlsl'] = 0;
		}
		$_SESSION ['wjsl'] = $returnValue ['jlsl'];
		$jcclhl = array ();
		$hlyb = jcclhl ();
		$jcclhl ['jgs'] = $hlyb;
		$jcclhl ['jgt'] = $hlyb;
		$jcclhl ['llp'] = $hlyb;
		$jcclhl ['jlx'] = $hlyb;
		$jcclhl ['fyr'] = $hlyb;
		$hl [] = $jcclhl;
		$returnValue ['jcclhl'] = $hl;
		
		$hl = array ();
		$sjclhl = array ();
		$hlyb = sjclhl ();
		$sjclhl ['jb'] = $hlyb;
		$sjclhl ['sc'] = $hlyb;
		$sjclhl ['tk'] = $hlyb;
		$sjclhl ['tt'] = $hlyb;
		$sjclhl ['mc'] = $hlyb;
		$hl [] = $sjclhl;
		$returnValue ['sjclhl'] = $hl;
		$returnValue ['sjyp'] = 5; //刷将所需银票个数(待定)
		//$returnValue['sjjhl'] = 1;						//刷将所需江湖令个数(待定)
		$returnValue ['hydyp'] = 5; //历练补一个好友所需银票
		

		$returnValue ['scdjsx'] = 12; //市场等级上限
		$returnValue ['djtdjsx'] = 12; //点将台等级上限
		$returnValue ['jgdjsx'] = 12; //酒馆等级上限
		$returnValue ['lddjsx'] = 12; //领地等级上限
		$returnValue ['tjpdjsx'] = 12; //铁匠铺等级上限
		$bgDataInfo = toolsModel::getAllItems ( $roleInfo ['playersid'], true );
		$returnValue ['list'] = $bgDataInfo ['list'];
		$returnValue ['gv'] = $_SC ['swf_version'];
		$sql_lscs = "SELECT lxsl,jf FROM " . $common->tname ( 'dalei_jf' ) . " WHERE playersid = '" . $roleInfo ['playersid'] . "' LIMIT 1";
		$rows_lscs = $db->fetch_array ( $db->query ( $sql_lscs ) );
		$returnValue ['lscc'] = $rows_lscs ['lxsl'];
		$returnValue ['ltjf'] = $rows_lscs ['jf'];		
		
		// 判断是否是今天第一次登陆
		$mc->set ( MC . "last_login_time_" . $roleInfo ['userid'], $nowTime, 0, 0 );
		// 显示公告
		$serverNotice = $db->fetch_array ( $db->query ( "SELECT * FROM " . $common->tname ( 'notice' ) ) );
		if (! empty ( $serverNotice )) {
			$mc->set ( MC . 'serverNotice', $serverNotice, 0, 3600 );
			// 获取公告有效时间
			$curr_ts = $nowTime;
			//$common->insertLog("$curr_ts  {$serverNotice['start_date']}  {$serverNotice['end_date']}");
			if ($curr_ts >= intval ( $serverNotice ['start_date'] ) && $curr_ts <= intval ( $serverNotice ['end_date'] )) {
				if (! ($NoticeFlag = $mc->get ( MC . 'NoticeFlag' . $roleInfo ['playersid'] ))) {
					$NoticeFlag = 0;
				}
				if (! ($NoticeExpFlag = $mc->get ( MC . 'NoticeExpFlag' ))) {
					$NoticeExpFlag = 0;
				}
				if ($NoticeFlag == 0 || $NoticeExpFlag == 1) {
					$mc->set ( MC . 'NoticeFlag' . $roleInfo ['playersid'], 1, 0, 0 );
					$mc->set ( MC . 'NoticeExpFlag', 0, 0, 0 );
					$returnValue ['gg'] = 2;
					$returnValue ['ggnr'] = $serverNotice ['notice'];
				}
			}
		}				
		$temp = array ();
		$temp = fightModel::getMsg ();
		$returnValue ['msglist'] = $temp [0] . ';' . $temp [1] . ';' . $temp [2] . ';' . $temp [3] . ';' . $temp [4] . ';' . $temp [5];
		
		/*获得强征信息*/
		/*$collectNeedTime = collectNeedTime ();
		switch ($roleInfo ['vip']) {
			case 2 :
				$rate = 1.1;
				break;
			case 3 :
				$rate = 1.15;
				break;
			case 4 :
				$rate = 1.2;
				break;
			default :
				$rate = 1;
				break;
		}*/
		

		//$qzcs = 3;
		//$vip = 0;
		//$cdcs = 0;
		//$yzcs = 0;
		$returnValue ['qzcs'] = 3;               //返回可强征次数		
		/*$last_update_time = $roleInfo ['last_collect_time'];
		$timeDiff = $nowTime - $last_update_time; //与上次征税时间间隔
		if ($timeDiff > 0) {
			$leftTime = 7200 - $timeDiff;
			$needYp = ceil ( $leftTime / 3600 ) * 5;
			if ($needYp < 0) {
				$needYp = 0;
			}
		} else {
			$needYp = 0;
		}*/
		$returnValue ['qzyp'] = 0;
		
		/*获得强征信息结束*/
		$returnValue ['tqzzl'] = roleModel::tqzzl ( $roleInfo ['player_level'] ); //铜钱增长率			
		//guideScript::jsydsj ( $roleInfo, 'player_level', 1, 1 ); //接收引导	
		$returnValue['frd'] = 80;
		//偷将花费
		$ljhf = tjhf(3);
		$ljhfxx = $ljhf['hs'].','.$ljhf['jl'];
		$zshf = tjhf(4);
		$zshfxx = $zshf['hs'].','.$zshf['jl'];
		$cshf = tjhf(5);
		$cshfxx = $cshf['hs'].','.$cshf['jl'];
		$returnValue['tjhf'] = $ljhfxx.','.$zshfxx.','.$cshfxx;	
		$returnValue['ct'] = 30000;	
		$returnValue['xwzt'] = '00000000000000000000000000000000';
		$returnValue['srvsj'] = $nowTime;
		$zlzydInfo = zydModel::zlzydzt();
		$returnValue['zllist'] = $zlzydInfo['zllist'];	
		$returnValue ['qzcs'] = 5;		
		$returnValue ['qzyb'] = 10;			
		$returnValue ['qztq'] = 10000;		
		$returnValue['jcbl'] = 50;
		$returnValue['jcblyb'] = jcybhf(50);
		$returnValue['yjblyb'] = jcyjts(50);
		$returnValue['pyybhf'] = array(whyb(1),whyb(2),whyb(3),whyb(4));
		$returnValue['jctqhf'] = 30000;		
		$returnValue['bxzt'] = '00000';	
		$returnValue['dqhyd'] = 0;
		$returnValue['djhyd'] = array(5,30,60,100,150);
		$zbmInfo = ConfigLoader::GetQuestProto(5001001);
		$returnValue['mbTitle'] = $zbmInfo['QTitle'];
		$returnValue['mbiid'] = zmbiid(5001001);
		
		/*获得强征信息*/
		$collectNeedTime = collectNeedTime ();
		$vip = $roleInfo ['vip'];
		switch ($vip) {
			case 1 :
				$rate = 1;
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
		$ykrljc = intval($roleInfo ['ykzjrl']);
		$getCoinsTal = round ( getCoinsAmount ( $roleInfo ['sc_level'] ) * $rate, 0 );
		$returnValue['ykjbrl'] = $getCoinsTal;
		if($ykrljc > 0) {
			$getCoinsTal += $getCoinsTal * ($ykrljc/100);
		}
		$per_minute_coins = round(round ( $getCoinsTal, 0 ) / ($collectNeedTime/60), 3);
		if($ykrljc == 0) {
			$returnValue['ykjc'] = $ykrljc;
			$returnValue['xjykjc'] = 10;
			$returnValue['tstj'] = array(0,10);
			$returnValue['dwsszl'] = $per_minute_coins;
		} else if($ykrljc == 10) {
			$returnValue['ykjc'] = $ykrljc;
			$returnValue['xjykjc'] = 25;
			$returnValue['tstj'] = array(1,25);
			$returnValue['dwsszl'] = $per_minute_coins;
		} else if($ykrljc == 25) {
			$returnValue['ykjc'] = $ykrljc;
			$returnValue['xjykjc'] = 50;
			$returnValue['tstj'] = array(2,50);
			$returnValue['dwsszl'] = $per_minute_coins;
		} else if($ykrljc == 50) {
			$returnValue['ykjc'] = $ykrljc;
			$returnValue['xjykjc'] = 75;
			$returnValue['tstj'] = array(3,75);
			$returnValue['dwsszl'] = $per_minute_coins;
		} else if($ykrljc == 75) {
			$returnValue['ykjc'] = $ykrljc;
			$returnValue['xjykjc'] = 100;
			$returnValue['tstj'] = array(4,100);
			$returnValue['dwsszl'] = $per_minute_coins;
		} else if($ykrljc == 100) {
			$returnValue['ykjc'] = $ykrljc;
			$returnValue['dwsszl'] = $per_minute_coins;
		}
		$jcid = _get('jcid');
		$_SESSION['jcid'] = $jcid;
		$pfcs = roleModel::pfcs($jcid);
		if (!empty($pfcs)) {
			$sgpf = $roleInfo['sgpf'];
			$returnValue['pfdz'] = $pfcs['url'];
			$returnValue['pfjl'] = $pfcs['pfjl'];			
		}
		//百纳接口
		if ($jcid == 57) {
			roleModel::bntz($roleInfo['ucid'],$roleInfo['playersid'],$roleInfo['nickname'],$roleInfo['player_level']);
		}					
		return $returnValue;		
	}
	
	//去除标点符号
	public static function removeTag($string) {		
		return preg_replace('/\s/','',preg_replace("/[[:punct:]]/",'',strip_tags(html_entity_decode(str_replace(array('？','！','￥','（','）','：','‘','’','“','”','《','》','，','…','。','、','nbsp'),'',$string),ENT_QUOTES,'UTF-8'))));
	}
	
	// 充值返还
	public static function restitution(&$roleInfo) {
		global $common, $db, $mc;
		
		if (!($restitutionList = $mc->get(MC.'restitution'))) {
			$res = mysql_query('select * from ol_restitution');
			if(mysql_num_rows($res) > 0) {
				while($restitutionList[] = mysql_fetch_array($res, MYSQL_ASSOC));
				array_pop($restitutionList);
				$mc->set(MC.'restitution', $restitutionList, 0, 3600);
			} else {
				$restitutionList = null;
			}
		}
		
		if($restitutionList != null) {
			if(strlen($restitutionList[0]['ucids']) > 1) {		
				// 用户是否在返还名单中
				$ucidArr = $restitutionList[0]['ucids'];
				$ucidArr = explode(',', $ucidArr); 
			} else {
				$ucidArr = array();
			}
		} else {
			$ucidArr = array();
		}
		
		if(in_array($roleInfo['ucid'], $ucidArr)) {
			$rule1 = null;
			$rule2 = null;
			$rule3 = null;
			
			$rule = json_decode($restitutionList[0]['rule'], true);	
			foreach($rule as $k=>$v) {
				if($k == 1) {
					// 获取元宝返还规则					
					$rule1['rate'] = $v[$roleInfo['ucid']]['rate'];
					$rule1['yb'] = $v[$roleInfo['ucid']]['yb'];
				} else if($k == 2) {
					$rule2['amount'] = $v;
				} else if($k == 3) {
					$rule3['items'] = $v;
				}
			}
			
			// 添加返还到用户，更新roleInfo及player相关
			if($rule1 != null) {
				$roleInfo['ingot'] = $roleInfo['ingot'] + intval($rule1['rate']) * intval($rule1['yb']);
				//$common->insertLog("roleInfo['ingot'] = {$roleInfo['ingot']} + {$rule1['rate']} * {$rule1['yb']};");
				include(dirname(dirname(dirname(__FILE__))) . '/includes/vip_control.php');
				$vipRet = vipChongzhi($roleInfo['playersid'], 0, $roleInfo['ingot'], 0, 0, false);
				if(empty($vipRet)){
					$roleInfo['vip'] = $vipRet['vip'];
					$roleInfo['vip_end_time'] = $vipRet['vip_end_time'];
					$mc->delete(MC.$roleInfo['playersid']);
					$roleInfoTmp['playersid'] = $roleInfo['playersid'];
					roleModel::getRoleInfo($roleInfoTmp, false);
				}
			}			
			
			if($rule2 != null) {
				$roleInfo['silver'] = $roleInfo['silver'] + intval($rule2['amount']);
			}
			
			if($rule1 != null || $rule2 != null) {
				$updateRole['ingot'] = $roleInfo['ingot'];
				$updateRole['silver'] = $roleInfo['silver'];
				$whereRole['playersid'] = $roleInfo['playersid'];
				$common->updatetable('player', $updateRole, $whereRole);
				$common->updateMemCache(MC.$roleInfo['playersid'], $updateRole);
			}
			
			if($rule3 != null) {				
				toolsModel::addItems($roleInfo['playersid'], $rule3['items']);
			}
			
			if($rule1 != null) { // 删除对应用户
				unset($rule[1][$roleInfo['ucid']]);
				$restitutionList[0]['rule'] = json_encode($rule);
			}
			
			$findKey = array_search($roleInfo['ucid'], $ucidArr);
			unset($ucidArr[$findKey]);
			$restitutionList[0]['ucids'] = implode(',', $ucidArr); // 删除对应用户
			
			$updateRestitution['rule'] = $restitutionList[0]['rule'];
			$updateRestitution['ucids'] = $restitutionList[0]['ucids'];
			$whereRestitution['id'] = $restitutionList[0]['id'];
			$common->updatetable('restitution', $updateRestitution, $whereRestitution);
			
			$mc->delete(MC.'restitution');
			if (!($restitutionList = $mc->get(MC.'restitution'))) {
				$res = mysql_query('select * from ol_restitution');
				while($restitutionList[] = mysql_fetch_array($res, MYSQL_ASSOC));
				array_pop($restitutionList);
				$mc->set(MC.'restitution', $restitutionList, 0, 3600);
			}			
		}
	}
	
	//更新引导事件
	public static function sstt($playersid,$keysStr) {
		global $mc, $db, $common, $G_PlayerMgr;
		$player = $G_PlayerMgr->GetPlayer($playersid);
		$xwzt = $player->baseinfo_['xwzt'];
		$updateStr = explode(',',$keysStr);
		if (is_array($updateStr)) {
			foreach ($updateStr as $updateStrValue) {
				$xwzt = substr_replace($xwzt,'1',$updateStrValue - 1,1);
			}
		} else {
			$xwzt = substr_replace($xwzt,'1',$updateStr - 1,1);
		}
		$updateRole['xwzt'] = $xwzt;
		$updateRoleWhere['playersid'] = $playersid;
		$common->updatetable('player',$updateRole,$updateRoleWhere);
		$common->updateMemCache(MC.$playersid,$updateRole);
		return array('status'=>0);
	}
	
	//评分参数配置
	public static function pfcs($jcid) {
		global $role_lang;
		$pfcs = array(
			1 => array('url'=>'','ztpos'=>1000,'pfjl'=>array()),
			2 => array('url'=>'','ztpos'=>1000,'pfjl'=>array()),
			3 => array('url'=>'','ztpos'=>1000,'pfjl'=>array()),
			4 => array('url'=>'','ztpos'=>1000,'pfjl'=>array()),
			5 => array('url'=>'','ztpos'=>1000,'pfjl'=>array()),
			6 => array('url'=>'https://play.google.com/store/apps/details?id=air.com.jofgame.qjsh.SG','ztpos'=>1,'pfjl'=>array(array('mc'=>$role_lang['login_3'],'sl'=>10000),array('mc'=>$role_lang['login_4'],'sl'=>$role_lang['login_5']))),
			7 => array('url'=>'','ztpos'=>1000,'pfjl'=>array()),
			8 => array('url'=>'https://itunes.apple.com/us/app/ni-zhuan-feng-yun/id645436918?ls=1&mt=8','ztpos'=>1,'pfjl'=>array(array('mc'=>$role_lang['login_3'],'sl'=>10000),array('mc'=>$role_lang['login_4'],'sl'=>$role_lang['login_5']))),
			9 => array('url'=>'','ztpos'=>1000,'pfjl'=>array()),
			10 => array('url'=>'','ztpos'=>1000,'pfjl'=>array()),
			11 => array('url'=>'','ztpos'=>1000,'pfjl'=>array()),
			12 => array('url'=>'','ztpos'=>1000,'pfjl'=>array()),
			13 => array('url'=>'https://play.google.com/store/apps/details?id=air.com.jofgame.qjsh.SG','ztpos'=>0,'pfjl'=>array(array('mc'=>$role_lang['login_3'],'sl'=>10000),array('mc'=>$role_lang['login_4'],'sl'=>$role_lang['login_5']))), 
			14 => array('url'=>'','ztpos'=>1000,'pfjl'=>array()),
			15 => array('url'=>'','ztpos'=>1000,'pfjl'=>array())			
		);
		if (isset($pfcs[$jcid])) {
			return $pfcs[$jcid];
		} else {
			return false;
		}
	}

	//修改昵称
	public static function xgnc ( $playersid, $nickname, $sex ) {
		global $mc, $db, $common, $user_lang;
		$checkName = roleModel::is_checkNameAll ( $nickname, 1 );
		if ($checkName == 1 || empty($nickname)) {
			return array('status'=>30,'message'=>$user_lang['xgksdl_5']);
		}
		$updateRole['nickname'] = $nickname;
		$updateRole['sex'] = $sex;
		$common->updatetable('player',$updateRole,"playersid = '$playersid'");
		$common->updateMemCache(MC.$playersid,$updateRole);
		//修改资源点信息
		$common->updatetable('zyd',array('nickname'=>$nickname),"playersid = '$playersid'");
		$sql_zyd = "select * from ".$common->tname('zyd')." where zlpid = '$playersid'";
		$res_zyd = $db->query($sql_zyd);
		$mc->delete(MC.$playersid."_ydxx");
		$mc->delete(MC.$playersid."_zlydxx");
		$zyd = array();
		while ($rows_zyd = $db->fetch_array($res_zyd)) {
			$zyd[] = $rows_zyd;
		}
		if (!empty($zyd)) {
			$common->updatetable('zyd',array('zlpname'=>$nickname),"zlpid = '$playersid'");
			foreach ($zyd as $zydValue) {
				$mc->delete(MC.$zydValue['playersid']."_ydxx");
				$mc->delete(MC.$zydValue['playersid']."_zlydxx");				
			}
		}
		//修改占领城池相关
		$sql_city = "select * from ".$common->tname('player')." where aggressor_playersid = '$playersid'";
		$res_city = $db->query($sql_city);
		$zl = array();
		while ($rows_zl = $db->fetch_array($res_city)) {
			$zl[] = $rows_zl;
		}
		if (!empty($zl)) {
			$common->updatetable('player',array('aggressor_nickname'=>$nickname),"aggressor_playersid = '$playersid'");
			foreach ($zl as $zlValue) {
				$mc->delete(MC.$zlValue['playersid']);
			}			
		}
		//修改武将相关
		$sql_wj = "select * from ".$common->tname('playergeneral')." where occupied_playersid = '$playersid'";
		$res_wj = $db->query($sql_wj);
		$wj = array();
		while ($rows_wj = $db->fetch_array($res_wj)) {
			$wj[] = $rows_wj;
		}
		if (!empty($wj)) {
			$common->updatetable('playergeneral',array('occupied_player_nickname'=>$nickname),"occupied_playersid = '$playersid'");
			foreach ($wj as $wjValue) {
				$mc->delete(MC . $wjValue['playerid'] . '_general');
			}
		}
		//更新军情
		$common->updatetable('jq',array('wfmc'=>$nickname),"wfpid='$playersid'");
		$common->updatetable('jq',array('dfmc'=>$nickname),"dfpid='$playersid'");
		return array('status'=>0);
	}
	
	//礼品码验证
	public static function lpm($lpm,$userid) {
		global $mc, $db, $common, $G_PlayerMgr, $sys_lang, $role_lang, $city_lang;
		$playersid = $_SESSION['playersid'];
		if (strlen($lpm) < 8) {
			$lpm = '0'.$lpm;
		}		
		$player = $G_PlayerMgr->GetPlayer($playersid);
		$nowTime = time();
		if(!$player) {
			return array('status'=>21, 'message'=>$sys_lang[7]);
		}
		$lpmlx = substr($lpm,0,2);		
		$ck_sql = "select count(lpm) as sl from ".$common->tname('lpm')." where playersid = $playersid && lpmlx = '$lpmlx' limit 1";
		$ck_res = $db->query($ck_sql);
		$ck_rows = $db->fetch_array($ck_res);
		if ($ck_rows['sl'] > 0) {
			return array('status'=>21,'message'=>$role_lang['lpm_3']);
		}		
		$sql = "SELECT * FROM ".$common->tname('lpm')." WHERE lpm = '$lpm' LIMIT 1";
		$result = $db->query($sql);
		$rows = $db->fetch_array($result);
		if (empty($rows)) {
			return array('status'=>21, 'message'=>$role_lang['lpm_1']);
		} else {
			if ($rows['playersid'] > 0 || ($nowTime > $rows['endTime'] && $rows['endTime'] != 0)) {
				return array('status'=>21,'message'=>$role_lang['lpm_1']);
			}
		}		
		switch ($lpmlx) {
			case '01':
				$value['status'] = 0;
				//18635
				$addRes = $player->AddItems(array(18673=>1));
				if ($addRes !== false) {
					$value ['status'] = 0;
					$itemInfo = toolsModel::getItemInfo ( 18673 );
					$value ['mc'] = $itemInfo ['Name'];
					$value ['sl'] = 1;
					$value ['iid'] = $itemInfo ['IconID'];
					$bagData = $player->GetClientBag();
					$value['list'] = $bagData;
					$db->query("UPDATE ".$common->tname('lpm')." SET playersid = $playersid WHERE lpm = '$lpm' LIMIT 1");
				} else { //背包已满
					$value ['status'] = 1001;
					//$value ['message'] = $city_lang['ksck_2'];
				}				
			break;
			default:
				$value['status'] = 21;
				$value['message'] = $role_lang['lpm_2'];
			break;
		}
		return $value;
	}
	
	//百纳角色信息通知
	public static function bntz($UserID,$CharacterID,$CharacterName,$CharacterLevl) {
		global $_SC;
		$serverInfo = array(
			'hud' => '座次',
			'test' => '测试',
			'zjsh_zyy_001' => '天地豪情',
			'zjsh_zyy_002' => '天地豪情',
			'1' => '天地豪情',
			'2' => '独霸天下',
			'zyy02' => '独霸天下',
			'zj3' => '天下无双'
		);
		$fwqdm = $_SC['fwqdm'];
		if (isset($serverInfo[$fwqdm])) {
			$GameServer = $serverInfo[$fwqdm];
		} else {
			$GameServer = '独霸天下';
		}
		$endpoint = 'http://baina.com:8010/bainasdk/characterInfo.jsp';
		$ch = curl_init($endpoint);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "UserID=$UserID&CharacterID=$CharacterID&CharacterName=$CharacterName&CharacterLevl=$CharacterLevl&CharacterJobInfo=&GameServer=$GameServer&ComeFrom=whyzl001");
		$response = curl_exec($ch);
		$errno    = curl_errno($ch);
		$errmsg   = curl_error($ch);
		curl_close($ch);		
	}
}