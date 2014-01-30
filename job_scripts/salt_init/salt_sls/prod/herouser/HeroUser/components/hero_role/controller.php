<?php
class roleController {
	//创建角色
	function createRole(&$roleInfo) {
		global $common, $db, $G_PlayerMgr, $role_lang, $_SC;
		$nickname = $roleInfo ['nickname'];
		$inviteid = $roleInfo ['inviteid'];
		$userid = $roleInfo ['userid'];
		$ucid = $roleInfo ['ucid'];
		$jcmgc = _get ( 'jcmgc' );
		$phone = _get ( 'phone' );
		$roleInfo ['phone'] = is_null ( $phone ) ? 0 : $phone;
		if ($_SESSION ['client'] == 1) {
			$result = roleModel::is_regedit ( $roleInfo );
			if ($result == 2) {
				$returnValue ['status'] = 3;
				$returnValue ['message'] = $role_lang ['createRole_1'];
			} else {
				$returnValue ['status'] = 0;
			}
			if (empty ( $roleInfo ['sex'] )) {
				$roleInfo ['sex'] = _get ( 'sex' );
			}
			if (empty ( $roleInfo ['nickname'] )) {
				$roleInfo ['nickname'] = base64_decode ( _get ( 'xm' ) );
			}
			//print_r($roleInfo);
			$db->query ( "BEGIN" );
			$result = roleModel::saveRoleInfo ( $roleInfo, $jcmgc );
			questsController::OnAccept ( $roleInfo, "'player_level'" ); //接收任务
			$db->query ( "commit" );
			$G_PlayerMgr->dbcommit = 1;
			if ($result == 10) {
				$returnValue ['status'] = 21;
				$returnValue ['message'] = $role_lang ['createRole_2']; //系统错误，插入数据失败	
			} else {
				$returnValue = roleController::login ( $roleInfo, false );
			}
		
		} else {
			if (_get ( 'debug' ) == 1 && defined ( 'PRESSURE_TEST_DEBUG' )) {
				$_SESSION ['debug'] = true;
			} else {
				$_SESSION ['debug'] = false;
			}
			$db->query ( "BEGIN" );
			$result = roleModel::saveRoleInfo ( $roleInfo, $jcmgc );
			questsController::OnAccept ( $roleInfo, "'player_level'" ); //接收任务
			$db->query ( "commit" );
			$G_PlayerMgr->dbcommit = 1;
			//echo($result);
			if ($result == 2) {
				$returnValue ['status'] = 21; //角色名称已经存在
				$returnValue ['message'] = $role_lang ['createRole_3']; //角色名称已经存在
			} elseif ($result == 3) {
				$returnValue ['status'] = 24; //该用户已经拥有一个角色
				$returnValue ['message'] = $role_lang ['createRole_4'];
			} elseif ($result == 4) {
				$returnValue ['status'] = 21;
				$returnValue ['message'] = $role_lang ['createRole_5']; //角色信息不合法，比如有空值
			} elseif ($result == 5) {
				$returnValue ['status'] = 3;
				$returnValue ['message'] = $role_lang ['createRole_6']; //系统错误，插入数据失败	    	
			} elseif ($result == 10) {
				$returnValue ['status'] = 21;
				$returnValue ['message'] = $role_lang ['createRole_2']; //系统错误，插入数据失败	
			} else {
				//$returnValue ['status'] = 0;
				$roleInfo ['nickname'] = $nickname;
				$roleInfo ['inviteid'] = $inviteid;
				$roleInfo ['userid'] = $userid;
				$roleInfo ['scdl'] = 1;
				
				//$returnValue = roleController::login ( $roleInfo, false );
				if (SYPT == 0) {
					if (NEED_RESTITUTION == 1) {
						roleModel::restitution ( $roleInfo );
					}
				}
				$returnValue = roleModel::createRoleLogin ( $roleInfo );
				
				// 触发活动脚本
				hdProcess::run ( array ('role_login' ), $roleInfo );
				//questsController::OnAccept($roleInfo,"'player_level'");	 //接收任务	  
				

				// 测试用参数表示是否可以跳过战斗
				if (TGZD == 1) {
					$returnValue ['tgzd'] = 1;
				} else {
					$returnValue ['tgzd'] = 0;
				}
			}
			/*if ($inviteid > 0) { //奖励对方
				$roleInfo ['nickname'] = $nickname;
				$roleInfo ['inviteid'] = $inviteid;
				$roleInfo ['userid'] = $userid;
				$roleInfo ['ucid'] = $ucid;
				roleModel::setAward ( $roleInfo );
				roleModel::addFriends ( $roleInfo );
			}*/
		//$returnValue ['rsn'] = intval ( _get ( 'ssn' ) );
		//$returnValue['boss'] = roleModel::getBoss();
		}
		
		if (isset ( $_SC ['canReq'] ) && $_SC ['canReq'] && $returnValue ['status'] == 0) {
			$returnValue ['r_code'] = dechex ( $roleInfo ['playersid'] );
		}
		
		ClientView::show ( $returnValue );
	}
	
	//获取角色信息
	function getRoleInfo(&$roleInfo) {
		$result = roleModel::getRoleInfo ( $roleInfo );
		if (! empty ( $result )) {
			return true;
		} else {
			return false;
		}
	}
	
	//创建本次角色登录身份session标识 
	function login(&$roleInfo) {
		global $common, $db, $mc, $_SC, $_SGLOBAL, $payServer, $role_lang;
		$nowTime = $_SGLOBAL ['timestamp'];
		$nickname = $roleInfo ['nickname'];
		$inviteid = $roleInfo ['inviteid'];
		$userid = $roleInfo ['userid'];
		//$scdl = isset ( $roleInfo ['scdl'] ) ? $roleInfo ['scdl'] : '';
		$result = roleModel::login ( $roleInfo );
		//print_r($result);
		//$returnValue['serverState'] = $result;    
		//服务器状态 22满员 23成功登录但无角色 1成功登录有角色
		//print_r($roleInfo);
		//$loginKey = $userid.uniqid(rand(15,20));
		//echo $userid."ww";
		if ($result ['status'] == 22) {
			$returnValue ['status'] = 22;
			$returnValue ['message'] = $role_lang ['login_1'];
		} elseif ($result ['status'] == 23) {
			//$returnValue['status'] = 1023;
			//$returnValue['roleName'] = $nickname;
			if ($_SESSION ['client'] == 1) {
				$returnValue ['status'] = 1023;
				$returnValue ['roleName'] = $nickname;
			} else {
				//创建新角色
				$returnValue ['status'] = 0;
				$returnValue ['loginKey'] = _get ( 'loginKey' );
				$returnValue ['userid'] = $userid;
				$returnValue ['scdl'] = 1;
				ClientView::show ( $returnValue );
				return true;
				/*$roleInfo['nickname'] = $_SESSION['realname'];
			 	$roleInfo['inviteid'] = $inviteid;
			 	$roleInfo['userid'] = $userid;
			 	$roleInfo['ucid'] = $_SESSION['ucid'];
			 	
			 	roleController::createRole($roleInfo);
			 	return true;*/
			}
		} else {
			//$mc->set(MC.$userid.'_login',$loginKey,0,14400);
			//socialModel::updateSocial ( $roleInfo ); //优化屏蔽
			/*if($roleInfo['vip'] > 0) {
		 		genModel::openxlw($roleInfo['playersid'],3,'',$roleInfo['vip_end_time'],$roleInfo['vip']);
		 	}*/
			if ($roleInfo ['vip'] >= 4) {
				$qzcs = 4;
			} elseif ($roleInfo ['vip'] == 3) {
				$qzcs = 3;
			} elseif ($roleInfo ['vip'] == 2) {
				$qzcs = 2;
			} elseif ($roleInfo ['vip'] == 1) {
				$qzcs = 1;
			} else {
				$qzcs = 0;
			}
			$jscs = $roleInfo ['jscs'];
			$returnValue ['jssycs'] = ($qzcs - $jscs) > 0 ? ($qzcs - $jscs) : 0;
			$returnValue ['status'] = 0;
			$returnValue ['loginKey'] = _get ( 'loginKey' );
			$_SESSION ['playersid'] = $roleInfo ['playersid'];
			$_SESSION ['player_level'] = $roleInfo ['player_level'];
			//$_SESSION['regionid'] = $roleInfo['regionid'];
			if (SYPT == 1) {
				$returnValue ['roleName'] = stripcslashes ( $roleInfo ['nickname'] );
			} else {
				$returnValue ['roleName'] = stripcslashes ( str_replace ( '_', '-', $roleInfo ['nickname'] ) );
			}
			
			if (SYPT == 1) {
				if ($roleInfo ['nickname'] == $role_lang ['login_2']) {
					$appid = 9;
					
					$seqnum = 'QJSH' . date ( 'ymdHis' ) . rand ( 1, 9999999 );
					$qqopenid = $roleInfo ['ucid'];
					$token = $mc->get ( MC . $qqopenid . '_token' );
					$pro = "{'seqnum':'$seqnum', 'func':'gnn'}";
					
					$servernum = rand ( 0, 1 );
					$serverIp = $payServer [$servernum] ['server'];
					$serverPort = $payServer [$servernum] ['port'];
					
					$fp = fsockopen ( $serverIp, $serverPort, $errno, $errstr, 1000 );
					$header = "GET /appid=$appid HTTP/1.1\r\n";
					$header .= "Host:localhost \r\n";
					$header .= "Content-Length: " . strlen ( $pro ) . "\r\n";
					$header .= "Content-Type: text/html \r\n";
					$header .= "X-TOKEN: $token\r\n";
					$header .= "Connection: Close\r\n\r\n";
					$header .= $pro . "\r\n";
					socket_set_timeout ( $fp, 1 );
					fwrite ( $fp, $header );
					
					$str = null;
					$startTime = microtime ( true );
					while ( ! feof ( $fp ) ) {
						$str .= fgets ( $fp, 1024 );
						
						$endTime = microtime ( true );
						if (($endTime - $startTime) * 1000 > 200) {
							$startPos = strrpos ( $str, "Content-Length" );
							$len = strlen ( 'Content-Length: ' );
							$endPos = strrpos ( $str, "Content-Type" );
							$len = substr ( $str, $startPos + $len, $endPos - ($startPos + $len) );
							$cStartPos = strrpos ( $str, "{" );
							$cEndPos = $endPos = strrpos ( $str, "}" );
							$finalLen = strlen ( substr ( $str, $cStartPos, $cEndPos - $cStartPos + 1 ) );
							if ($len == $finalLen)
								break;
						}
					}
					fclose ( $fp );
					$str = str_replace ( '\r\n\r\n', '', stristr ( $str, "{ " ) );
					$result = json_decode ( $str );
					
					$qqnickname = $role_lang ['login_2'];
					if (is_object ( $result )) {
						if ($result->code == 1) {
							$qqnickname = $result->nickname;
							$returnValue ['roleName'] = $qqnickname;
							$updateRole ['nickname'] = mysql_escape_string ( $qqnickname );
							$whereRole ['playersid'] = $roleInfo ['playersid'];
							$common->updatetable ( 'player', $updateRole, $whereRole );
							$common->updateMemCache ( MC . $roleInfo ['playersid'], $updateRole );
							// 同步修改资源点的用户昵称
							$common->updatetable ( 'zyd', $updateRole, $whereRole );
							$mc->delete ( MC . $roleInfo ['playersid'] . "_ydxx" );
						}
					}
				}
			}
			
			// 用户充值返还
			/* 	if(SYPT == 0) {
				if(NEED_RESTITUTION == 1) {
					roleModel::restitution($roleInfo);
				}
			} */
			
			$returnValue ['sexId'] = intval ( $roleInfo ['sex'] );
			//$returnValue['regionId'] = intval($roleInfo['regionid']);
			$returnValue ['jy'] = intval ( $roleInfo ['current_experience_value'] ); //经验
			$returnValue ['jl'] = floor ( $roleInfo ['food'] );
			$returnValue ['level'] = intval ( $roleInfo ['player_level'] );
			$returnValue ['xjjy'] = cityModel::getPlayerUpgradeExp ( $roleInfo ['player_level'] ); //下级经验
			$returnValue ['bbgs'] = intval ( $roleInfo ['bagpack'] );
			$returnValue ['xlwkqyb'] = 1; //开启训练位所需元宝
			$returnValue ['yb'] = intval ( $roleInfo ['ingot'] ); //元宝
			$returnValue ['tq'] = floor ( $roleInfo ['coins'] ); //铜钱
			$returnValue ['yp'] = intval ( $roleInfo ['silver'] ); //银两
			$returnValue ['lsdj'] = addLifeCost ( $roleInfo ['player_level'] );
			
			//$returnValue ['vc'] = array (array ('d' => 30, 'c' => '1_19' ), array ('d' => 30, 'c' => '20_99' ), array ('d' => 30, 'c' => '100_499' ), array ('d' => 30, 'c' => '500_100000' ) ); //vip数值
			$returnValue ['jlbhyb'] = 20; //将领编号需要的元宝数
			//$returnValue['ybhyp'] = array(array('yb'=>15,'yp'=>10),array('yb'=>30,'yp'=>30),array('yb'=>45,'yp'=>50));     //1个元宝换银票数
			//$returnValue['yphjl'] = array(array('yp'=>1,'jl'=>1),array('yp'=>5,'jl'=>5),array('yp'=>8,'jl'=>8));     //1银两换军粮数（数值待定）
			$returnValue ['jltqhyp'] = array (array ('jl' => 1, 'tq' => 100, 'yp' => 1 ), array ('jl' => 5, 'tq' => 500, 'yp' => 5 ), array ('jl' => 8, 'tq' => 800, 'yp' => 8 ), array ('jl' => 15, 'tq' => 1500, 'yp' => 15 ) ); //军粮铜钱换银两数
			$returnValue ['zbqhsx'] = 6; //装备强化上限
			//$returnValue['szbh'] = 5;      //山寨保护需要的元宝数
			//$returnValue['hc'] = 1;       //回城需要的银两数
			$returnValue ['jlhf'] = 5; //军粮恢复时间分为单位		
			//生成vip
			

			$returnValue ['zc'] = intval ( $roleInfo ['rank'] );
			$returnValue ['zg'] = intval ( $roleInfo ['ba'] );
			$returnValue ['frd'] = intval ( $roleInfo ['frd'] );
			$returnValue ['vip'] = intval ( $roleInfo ['vip'] );
			$cjInfo ['vip'] = $roleInfo ['vip'];
			achievementsModel::check_achieve ( $roleInfo ['playersid'], $cjInfo, array ('vip' ) );
			$returnValue ['pid'] = $roleInfo ['playersid'];
			$returnValue ['kzwjsx'] = guideValue ( $roleInfo ['djt_level'] ); //可招武将上限
			$returnValue ['zljbxz'] = 5; //占领级别限制 
			$returnValue ['zcyp'] = 5; //侦查所需银票个数
			//$returnValue ['bxlist'] = cityModel::getGemBoxInfo ( $roleInfo );
			$loginAward = cityModel::getLoginAwardInfo ( $roleInfo );
			$returnValue ['qrlist'] = $loginAward ['qrlist'];
			$returnValue ['qrjlts2'] = $loginAward ['qrjlts2'];
			$returnValue ['qrjllq2'] = $loginAward ['qrjllq2'];
			$jlsx = foodUplimit ( $roleInfo ['player_level'] );
			if ($returnValue ['vip'] > 0) {
				//	$returnValue ['vTime'] = $roleInfo ['vip_end_time'] - $nowTime;
				$returnValue ['jlsx'] = $jlsx;
				//	$returnValue ['vdl'] = intval ( $roleInfo ['vip_end_time'] );
			} else {
				$returnValue ['jlsx'] = $jlsx;
			}
			$returnValue ['jlzf'] = 2;
			$mg = jwmc ( intval ( $roleInfo ['mg_level'] ) );
			$next_mg = jwmc ( intval ( $roleInfo ['mg_level'] ) + 1 );
			$returnValue ['dqjw'] = $mg ['mc']; //当前爵位
			$returnValue ['jwid'] = intval ( $roleInfo ['mg_level'] );
			$returnValue ['xjjw'] = $next_mg ['mc']; //下级爵位
			$returnValue ['dqjc'] = $mg ['jc']; //当前加成，返回一个整数，比如40%，就返回40
			$returnValue ['xjjc'] = $next_mg ['jc']; //下级加成
			$returnValue ['swz'] = $roleInfo ['prestige']; //声望值
			$returnValue ['swsx'] = $next_mg ['sw']; //声望上限
			//$daleiInfo = daleiModel::getdaleiInfo($roleInfo['playersid']);
			//print_r($daleiInfo[0]['lxsl']);
			$returnValue ['jsyp'] = 5;
			//$returnValue['type'] = $result['type'];
			/*$returnValue['award'] = $result['award'];
		   	if ($returnValue['award'] == 1) {
		   	   if ($result['day'] != 5) {
			   	   $returnValue['jrjl'] = $result['jrjl'];
			   	   $returnValue['mrjl'] = $result['mrjl'];		   	   	
		   	   } else {
		   	   	   $returnValue['prize'] = $result['prize'];
		   	   }
		   	   $returnValue['day'] = $result['day'];		   	     		
		   	}*/
			//$generalArray = cityModel::getGeneralData($roleInfo['playersid']);
			$getInfo ['playersid'] = $roleInfo ['playersid'];
			$ufInfo = array ();
			$ufInfo ['playersid'] = $_SESSION ['playersid'];
			$ufInfo ['ucid'] = $_SESSION ['ucid'];
			//$mc->flush();
			//$ucfriendsInfo = roleModel::getRoleUcFriendsInfo($ufInfo);
			//$friendsInfo = roleModel::getTableRoleFriendsInfo($_SESSION['playersid']);
			

			//print_r($ucfriendsInfo);
			//$aa = $mc->get(MC.$_SESSION['playersid'].'_friends');
			//print_r($aa);
			$generalInfo = cityModel::getGeneralList ( $getInfo, 1, 1 );
			if ($generalInfo ['status'] == 0) {
				$returnValue ['generals'] = $generalInfo ['generals'];
				//$returnValue['hirableGeneral'] = $generalInfo['hirableGeneral'];
				$returnValue ['nextUpdate'] = $generalInfo ['nextUpdate'];
				$returnValue ['jlsl'] = count ( $returnValue ['generals'] );
				$roleInfo ['ldsl'] = $generalInfo ['ldsl'];
			} else {
				$returnValue ['generals'] = array ();
				$returnValue ['jlsl'] = 0;
				$roleInfo ['ldsl'] = 0;
			}
			$_SESSION ['wjsl'] = $roleInfo ['rwsl'] = $returnValue ['jlsl'];
			$roleInfo ['jjsl'] = $_SESSION ['jj'];
			$roleInfo ['lles'] = $_SESSION ['lles'];
			$roleInfo ['llss'] = $_SESSION ['llss'];
			$roleInfo ['llmax'] = $_SESSION ['llmax'];
			$roleInfo ['py_200'] = $_SESSION ['py_200'];
			$roleInfo ['py_400'] = $_SESSION ['py_400'];
			$roleInfo ['py_600'] = $_SESSION ['py_600'];
			$roleInfo ['py_800'] = $_SESSION ['py_800'];
			$roleInfo ['py_1000'] = $_SESSION ['py_1000'];
			$roleInfo ['py_1200'] = $_SESSION ['py_1200'];
			$roleInfo ['py_1400'] = $_SESSION ['py_1400'];
			$roleInfo ['py_1600'] = $_SESSION ['py_1600'];
			$roleInfo ['py_1800'] = $_SESSION ['py_1800'];
			$roleInfo ['qbjn'] = $_SESSION ['qbjn'];
			$roleInfo ['zswq'] = $_SESSION ['zswq'];
			$roleInfo ['gtzs'] = $_SESSION ['gtzs'];
			$roleInfo ['jn_3'] = $_SESSION ['jn_3'];
			$roleInfo ['jn_5'] = $_SESSION ['jn_5'];
			$roleInfo ['jn_7'] = $_SESSION ['jn_7'];
			$rwid = questsController::OnFinish ( $roleInfo, "'zmwj','zlldsl','hc','llwj','pysx','qbjn','zswq','gtzs'" );
			if (! empty ( $rwid )) {
				$returnValue ['rwid'] = $rwid;
			}
			
			//$rwValue = clientList::getQuestsList($roleInfo['playersid']);
			//$returnValue['rw'] = $rwValue;
			//$returnValue['boss'] = roleModel::getBoss();
			//$returnValue['loginKey'] = $loginKey;		
			//socialModel::waitingMessage();
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
			
			// 重置闯关(如果当前没有闯关状态/进度)
			if (! ($cgRecord = $mc->get ( MC . 'stageInfo_' . $roleInfo ['playersid'] ))) {
				$cgRecord = $db->fetch_array ( $db->query ( "SELECT * FROM " . $common->tname ( 'player_stage' ) . " WHERE `playersid` = '{$roleInfo['playersid']}'" ) );
				$mc->set ( MC . 'stageInfo_' . $roleInfo ['playersid'], $cgRecord, 0, 3600 );
			}
			if ($cgRecord ['curr_stage'] == 0) {
				$curr_date = $nowTime;
				// 判断闯关信息是否过期
				$year = date ( 'Y' );
				$month = date ( 'm' );
				$day = date ( 'd' );
				if ($cgRecord ['last_cg_date'] != 0) {
					$players_year = date ( 'Y', $cgRecord ['last_cg_date'] );
					$players_month = date ( 'm', $cgRecord ['last_cg_date'] );
					$players_day = date ( 'd', $cgRecord ['last_cg_date'] );
					if (($players_year < $year || $players_month < $month) || ($players_year == $year && $players_month == $month && $players_day < $day)) {
						// 如果过期就重置
						$whereStage ['playersid'] = $roleInfo ['playersid'];
						$common->deletetable ( 'player_boss', $whereStage );
						$curr_date = $nowTime;
						$updateStageInfo ['last_cg_date'] = $curr_date;
						$updateStageInfo ['curr_difficulty'] = 0;
						$updateStageInfo ['attackBossTimes'] = 0;
						$updateStageInfo ['curr_stage'] = 0;
						$updateStageInfo ['curr_subStage'] = 0;
						$cgRecord ['cgTimes'] = $cgRecord ['cgTimes'] < 6 ? 6 : $cgRecord ['cgTimes'];
						$updateStageInfo ['addTimes'] = ($cgRecord ['timesLimt'] + $cgRecord ['addTimes']) - $cgRecord ['cgTimes'];
						$updateStageInfo ['buyTimes'] = 0;
						$updateStageInfo ['cgTimes'] = 0;
						$common->updatetable ( 'player_stage', $updateStageInfo, $whereStage );
						$mc->delete ( MC . 'stageInfo_' . $roleInfo ['playersid'] );
						$mc->delete ( MC . $roleInfo ['playersid'] . '_subStage' );
						
						// 更新上次闯关操作时间
						$updateStageInfo ['last_cg_date'] = $curr_date;
						$whereStage ['playersid'] = $roleInfo ['playersid'];
						$common->updatetable ( 'player_stage', $updateStageInfo, $whereStage );
						$common->updateMemCache ( MC . 'stageInfo_' . $roleInfo ['playersid'], $updateStageInfo );
					}
				}
			}
			
			// 游戏公告
			// 判断是否是今天第一次登陆
			//date_default_timezone_set ( 'PRC' );
			// $result = $db->query("SELECT last_login_time FROM ".$common->tname('user')." WHERE userid = ". $roleInfo['userid'] ." LIMIT 1");
			//$rows = $db->fetch_array($result);
			

			//$rows = $mc->get ( MC . "last_login_time_" . $roleInfo ['userid'] );
			//$mc->set ( MC . "last_login_time_" . $roleInfo ['userid'], $nowTime, 0, 0 );
			//list ( $_year, $_month, $_day ) = explode ( '-', date ( 'Y-m-d', $rows ) );
			//list ( $now_year, $now_month, $now_day, $now_hour, $now_minute, $now_second ) = explode ( '-', date ( 'Y-m-d-H-i-s' ) );
			//if ($_year < $now_year || $_month < $now_month || ($_year == $now_year && $_month == $now_month && $_day < $now_day)) {
			

			if ($loginAward ['hasAward']) {
				// 显示登陆奖励
				$returnValue ['gg'] = 1;
			} else {
				// 显示公告
				if (! ($serverNotice = $mc->get ( MC . 'serverNotice' ))) {
					$serverNotice = $db->fetch_array ( $db->query ( "SELECT * FROM " . $common->tname ( 'notice' ) ) );
					if (! empty ( $serverNotice )) {
						$mc->set ( MC . 'serverNotice', $serverNotice, 0, 3600 );
					}
				}
				if (! empty ( $serverNotice )) {
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
						//if ($NoticeFlag == 0 || $NoticeExpFlag == 1) {
						//$mc->set ( MC . 'NoticeFlag' . $roleInfo ['playersid'], 1, 0, 0 );
						//$mc->set ( MC . 'NoticeExpFlag', 0, 0, 0 );
						$returnValue ['gg'] = 2;
						$returnValue ['ggnr'] = $serverNotice ['notice'];
						$returnValue ['links'] = findUrlLink ( $serverNotice ['notice'] );
						//}
					}
				}
			}
			
		// 闪游充值检测，发起一个异步检测，如果有充值信息那么就增加元宝并写日志
		/*if (SYPT == 1) {
				$pid = $roleInfo ['playersid'];
				$ucid = $roleInfo['ucid'];				
				
				$fp = fsockopen("127.0.0.1", 17000, $errno, $errstr, 1000);
				$url = "/includes/syProcess.php?pid=$pid&ucid=$ucid";
				$out = "GET $url HTTP/1.1\r\n";
				$out .= "Host: 127.0.0.1\r\n";
				$out .= "Connection: Close\r\n\r\n";
				fwrite($fp, $out);
				fclose($fp);
			}*/
		}
		
		//echo $loginKey;
		//$speedInfo = $db->fetch_array($db->query("SELECT * FROM ".$common->tname('speed')));
		//$speed = $speedInfo['speed'];	
		//$speed = 1;
		//$returnValue['speed'] = $speedInfo['speed'];
		//$returnValue['speed'] = $speed;    
		

		/*if (! empty ( $inviteid )) {
			$result = $db->query ( "SELECT fromplayersid FROM " . $common->tname ( 'letters' ) . " WHERE type = 2 and id = " . $inviteid . " and status = 0 LIMIT 1" );
			$rows_pid = $db->fetch_array ( $result );
			$result = $db->query ( "SELECT nickname,regionid FROM " . $common->tname ( 'player' ) . " WHERE playersid = '" . $rows_pid ['fromplayersid'] . "' LIMIT 1" );
			$rows_regionid = $db->fetch_array ( $result );
			$returnValue ['yqfsl'] = $rows_regionid ['regionid'];
			$returnValue ['yqfmc'] = $rows_regionid ['nickname'];
		}*/
		//$returnValue['rsn'] = intval(_get('ssn'));
		if ($returnValue ['status'] == 0) {
			$temp = array ();
			$temp = fightModel::getMsg ();
			$returnValue ['msglist'] = $temp [0] . ';' . $temp [1] . ';' . $temp [2] . ';' . $temp [3] . ';' . $temp [4] . ';' . $temp [5];
		}
		/*获得强征信息*/
		$collectNeedTime = collectNeedTime ();
		$vip = $roleInfo ['vip'];
		switch ($vip) {
			case 1 :
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
		$ykrljc = intval ( $roleInfo ['ykzjrl'] );
		$getCoinsTal = round ( getCoinsAmount ( $roleInfo ['sc_level'] ) * $rate, 0 );
		$returnValue ['ykjbrl'] = $getCoinsTal;
		if ($ykrljc > 0) {
			$getCoinsTal += $getCoinsTal * ($ykrljc / 100);
		}
		$per_minute_coins = round ( round ( $getCoinsTal, 0 ) / ($collectNeedTime / 60), 3 );
		if ($ykrljc == 0) {
			$returnValue ['ykjc'] = $ykrljc;
			$returnValue ['xjykjc'] = 10;
			$returnValue ['tstj'] = array (0, 10 );
			$returnValue ['dwsszl'] = $per_minute_coins;
		} else if ($ykrljc == 10) {
			$returnValue ['ykjc'] = $ykrljc;
			$returnValue ['xjykjc'] = 25;
			$returnValue ['tstj'] = array (1, 25 );
			$returnValue ['dwsszl'] = $per_minute_coins;
		} else if ($ykrljc == 25) {
			$returnValue ['ykjc'] = $ykrljc;
			$returnValue ['xjykjc'] = 50;
			$returnValue ['tstj'] = array (2, 50 );
			$returnValue ['dwsszl'] = $per_minute_coins;
		} else if ($ykrljc == 50) {
			$returnValue ['ykjc'] = $ykrljc;
			$returnValue ['xjykjc'] = 75;
			$returnValue ['tstj'] = array (3, 75 );
			$returnValue ['dwsszl'] = $per_minute_coins;
		} else if ($ykrljc == 75) {
			$returnValue ['ykjc'] = $ykrljc;
			$returnValue ['xjykjc'] = 100;
			$returnValue ['tstj'] = array (4, 100 );
			$returnValue ['dwsszl'] = $per_minute_coins;
		} else if ($ykrljc == 100) {
			$returnValue ['ykjc'] = $ykrljc;
			$returnValue ['dwsszl'] = $per_minute_coins;
		}
		/*if ($vip == 4) {
			$qztq = 6500;
		} elseif ($vip == 3) {
			$qztq = 6000;
		} elseif ($vip == 2) {
			$qztq = 5500;
		} elseif ($vip == 1) {
			$qztq = 5000;
		} else {
			$qztq = 5000;
		}*/
		$ybqzcs = $roleInfo ['qzcs'];
		if (date ( 'Ymd', $nowTime ) == date ( 'Ymd', $roleInfo ['qzsjrq'] )) {
			if ($ybqzcs == 1) {
				$needyb = 20;
				$qzsycs = 4;
				$tqcl = 20000;
			} elseif ($ybqzcs == 2) {
				$needyb = 40;
				$qzsycs = 3;
				$tqcl = 30000;
			} elseif ($ybqzcs == 3) {
				$needyb = 80;
				$qzsycs = 2;
				$tqcl = 40000;
			} elseif ($ybqzcs == 4) {
				$needyb = 160;
				$qzsycs = 1;
				$tqcl = 50000;
			} else {
				$needyb = 0;
				$qzsycs = 0;
				$tqcl = 0;
			}
		} else {
			$needyb = 10;
			$qzsycs = 5;
			$tqcl = 10000;
		}
		$returnValue ['qzcs'] = $qzsycs;
		$returnValue ['qzyb'] = $needyb;
		$returnValue ['qztq'] = $tqcl;
		/*获得强征信息结束*/
		
		// 返回今日强掠次数
		if (! ($qianglv_cs = $mc->get ( MC . $roleInfo ['playersid'] . '_qianglv_cs' ))) {
			$qianglv_cs = array ('cs' => 1, 'sj' => time () );
			$mc->set ( MC . $roleInfo ['playersid'] . '_qianglv_cs', json_encode ( $qianglv_cs ), 0, 3600 * 24 );
			$qianglv_info = $qianglv_cs;
		} else {
			$qianglv_info = json_decode ( $qianglv_cs, true );
		}
		$qianglv_sj = $qianglv_info ['sj'];
		date_default_timezone_set ( 'PRC' );
		if (date ( 'Ymd', time () ) != date ( 'Ymd', $qianglv_sj )) {
			$qianglv_cs = array ('cs' => 1, 'sj' => time () );
			$mc->set ( MC . $roleInfo ['playersid'] . '_qianglv_cs', json_encode ( $qianglv_cs ), 0, 3600 * 24 );
			$qianglv_info = $qianglv_cs;
			$qianglv_sj = $qianglv_info ['sj'];
		}
		
		$returnValue ['qlcs'] = intval ( $qianglv_info ['cs'] ) - 1;
		
		$returnValue ['tqzzl'] = roleModel::tqzzl ( $roleInfo ['player_level'] ); //铜钱增长率	
		//偷将花费
		$ljhf = tjhf ( 3 );
		$ljhfxx = $ljhf ['hs'] . ',' . $ljhf ['jl'];
		$zshf = tjhf ( 4 );
		$zshfxx = $zshf ['hs'] . ',' . $zshf ['jl'];
		$cshf = tjhf ( 5 );
		$cshfxx = $cshf ['hs'] . ',' . $cshf ['jl'];
		$returnValue ['tjhf'] = $ljhfxx . ',' . $zshfxx . ',' . $cshfxx;
		$returnValue ['ct'] = 30000;
		$returnValue ['xwzt'] = $roleInfo ['xwzt'];
		$zlzydInfo = zydModel::zlzydzt ();
		$returnValue ['zllist'] = $zlzydInfo ['zllist'];
		$returnValue ['srvsj'] = $nowTime;
		$returnValue ['jcbl'] = intval ( $roleInfo ['jcb'] );
		$returnValue ['jcblyb'] = jcybhf ( $roleInfo ['jcb'] );
		$returnValue ['yjblyb'] = jcyjts ( $roleInfo ['jcb'] );
		$returnValue ['pyybhf'] = array (whyb ( 1 ), whyb ( 2 ), whyb ( 3 ), whyb ( 4 ) );
		$returnValue ['jctqhf'] = 30000;
		$returnValue ['bxzt'] = $roleInfo ['hyjl'];
		$returnValue ['dqhyd'] = intval ( $roleInfo ['hyd'] );
		$returnValue ['djhyd'] = array (5, 30, 60, 100, 150 );
		$zbmInfo = ConfigLoader::GetQuestProto ( $roleInfo ['dqzmbid'] );
		$returnValue ['mbTitle'] = $zbmInfo ['QTitle'];
		$returnValue ['mbiid'] = zmbiid ( $roleInfo ['dqzmbid'] );
		// 测试用参数表示是否可以跳过战斗
		if (TGZD == 1) {
			$returnValue ['tgzd'] = 1;
		} else {
			$returnValue ['tgzd'] = 0;
		}
		
		// 轻松探宝次数
		if (date ( "Ymd" ) == date ( "Ymd", $roleInfo ['tb_sjrq'] )) {
			if ($roleInfo ['tb_cs'] < 5) {
				$returnValue ['tbycs'] = 5 - $roleInfo ['tb_cs'];
			} else {
				$returnValue ['tbycs'] = 0;
			}
		} else {
			$returnValue ['tbycs'] = 5;
		}
		$jcid = _get ( 'jcid' );
		$_SESSION ['jcid'] = $jcid;
		$pfcs = roleModel::pfcs ( $jcid );
		if (! empty ( $pfcs )) {
			$sgpf = $roleInfo ['sgpf'];
			if (substr ( $sgpf, $pfcs ['ztpos'], 1 ) == 0) {
				$returnValue ['pfdz'] = $pfcs ['url'];
				//$returnValue['pfjl'] = $pfcs['pfjl'];
			}
		}
		
		if (isset ( $_SC ['canReq'] ) && $_SC ['canReq']) {
			$returnValue ['r_code'] = dechex ( $roleInfo ['playersid'] );
			if (empty ( $roleInfo ['r_code'] )) {
				$returnValue ['xybdyqm'] = 1;
			}
		}
		if ($jcid == 57) {
			roleModel::bntz($roleInfo['ucid'],$roleInfo['playersid'],$roleInfo['nickname'],$roleInfo['player_level']);
		}
		ClientView::loginShow ( $returnValue );
	}
	
	//Java创建角色
	function setRegionId(&$roleInfo) {
		global $common, $db, $G_PlayerMgr, $role_lang;
		$nickname = $roleInfo ['nickname'];
		$db->query ( "BEGIN" );
		$result = roleModel::saveRoleInfo ( $roleInfo );
		questsController::OnAccept ( $roleInfo, "'player_level'" ); //接收任务
		$db->query ( "commit" );
		$G_PlayerMgr->dbcommit = 1;
		//echo($result);
		if ($result == 2) {
			$returnVale ['status'] = 21; //角色名称已经存在
			$returnVale ['message'] = $role_lang ['createRole_3']; //角色名称已经存在
		} elseif ($result == 3) {
			$returnVale ['status'] = 24; //该用户已经拥有一个角色
			$returnVale ['message'] = $role_lang ['createRole_4'];
		} elseif ($result == 4) {
			$returnVale ['status'] = 21;
			$returnVale ['message'] = $role_lang ['createRole_5']; //角色信息不合法，比如有空值
		} elseif ($result == 5) {
			$returnVale ['status'] = 3;
			$returnVale ['message'] = $role_lang ['createRole_6']; //系统错误，插入数据失败	    	
		} else {
			$returnVale ['status'] = 0;
			$returnVale ['roleName'] = $nickname;
			$returnVale ['sexId'] = intval ( $roleInfo ['sex'] );
			$returnVale ['regionId'] = intval ( $roleInfo ['regionid'] );
			$returnVale ['level'] = intval ( $roleInfo ['player_level'] );
			//$returnVale['current_pos'] = str_replace(',','&',$roleInfo['current_pos']);
			$nodeInfo = explode ( ',', $roleInfo ['current_pos'] );
			$returnVale ['mapId'] = intval ( $nodeInfo [0] );
			$returnVale ['nodeId'] = intval ( $nodeInfo [1] );
			$returnVale ['nodeName'] = $nodeInfo [2];
			$returnVale ['llybjf'] = 1; //1个元宝换多少积分，整数
			$returnVale ['jl'] = floor ( $roleInfo ['food'] );
			//$returnVale ['dw'] = '平民'; //地位
			$returnVale ['sw'] = 0; //声望值
			$returnVale ['tq'] = 0; //铜钱
			$returnVale ['yp'] = 0; //银两
			$returnVale ['yb'] = 0; //元宝
			$returnVale ['jy'] = 0; //经验
			$returnVale ['xjjy'] = cityModel::getPlayerUpgradeExp ( $roleInfo ['player_level'] + 1 ); //下级经验
			//$returnVale ['vc'] = array (array ('d' => 3, 'c' => 45 ), array ('d' => 7, 'c' => 70 ), array ('d' => 15, 'c' => 125 ) ); //vip数值
			$returnVale ['jlbhyb'] = 20; //将领编号需要的元宝数
			$returnVale ['ybhjl'] = array (array ('yb' => 1, 'jl' => 1 ), array ('yb' => 5, 'jl' => 5 ), array ('yb' => 8, 'jl' => 10 ), array ('yb' => 15, 'jl' => 20 ) ); //1个元宝换军粮数（数值待定）
			$returnVale ['ylhjl'] = 1; //1银两换军粮数
			$returnVale ['jltqhyl'] = array ('jl' => 1, 'tq' => 100 ); //军粮铜钱换银两数		   	
			$returnVale ['szbh'] = 5; //山寨保护需要的元宝数
			$returnVale ['hc'] = 1; //回城需要的银两数
			$returnVale ['jlhf'] = 5; //军粮恢复时间分为单位
			$returnVale ['sxjl'] = 5; //刷新将领花费
			$returnVale ['gchf'] = 1; //攻城花费军粮
			$jlsx = foodUplimit ( $roleInfo ['player_level'] );
			$returnVale ['jlsx'] = $jlsx;
			$returnVale ['jlzf'] = 2;
			$returnVale ['hot'] = 0;
			$returnVale ['jlsl'] = 1; //将领数量              //下级经验
			$returnVale ['pid'] = $roleInfo ['playersid'];
			//print_r($returnVale);
		}
		if ($roleInfo ['inviteid'] > 0) { //奖励对方
			roleModel::setAward ( $roleInfo );
			roleModel::addFriends ( $roleInfo );
		}
		ClientView::show ( $returnVale );
		//heroCommon::writelog($_SERVER['REQUEST_URI'],json_encode($returnVale));
	}
	
	//退出游戏
	function logout() {
		//heroCommon::writelog_login('logout');
		roleModel::logout ();
		$returnValue ['rsn'] = intval ( _get ( 'ssn' ) );
		$returnValue ['status'] = 0;
		ClientView::show ( $returnValue );
	}
	
	//加uc好友为游戏好友
	function addFriends($roleInfo) {
		global $common;
		//$roleInfo['ucid'] = "1005386";
		$result = roleModel::addfriends ( $roleInfo );
		$returnValue ['status'] = 0;
		$returnValue ['rsn'] = intval ( _get ( 'ssn' ) );
		ClientView::show ( $returnValue );
	}
	
	// 废弃接口
	//抽奖接口
	function lottery() {
		$playersid = $_SESSION ['playersid'];
		$id = _get ( 'prizeId' );
		$returnValue = roleModel::lottery ( $playersid, $id );
		$returnValue ['rsn'] = intval ( _get ( 'ssn' ) );
		ClientView::show ( $returnValue );
	}
	
	//检查将领是否存在,如不存在则重新添加将领并返回数据
	function reGetGeneralInfo($roleInfo) {
		$playersid = $roleInfo ['playersid'];
		$returnValue = roleModel::checkGenData ( $playersid );
		$returnValue ['rsn'] = intval ( _get ( 'ssn' ) );
		ClientView::show ( $returnValue );
	}
	
	//返回用户服务器请求角色数据
	function returnRoleDataToUser() {
		ClientView::show ( roleModel::getRoleInfoFromUserver () );
	}
	
	function sstt() {
		$playersid = $_SESSION ['playersid'];
		$keysStr = _get ( 'keys' );
		$returnValue = roleModel::sstt ( $playersid, $keysStr );
		ClientView::show ( $returnValue );
	}
	//修改昵称
	function xgnc($roleInfo) {
		$playersid = $_SESSION ['playersid'];
		$nickname = _get ( 'nickname' );
		$sex = _get ( 'sex' );
		$returnValue = roleModel::xgnc ( $playersid, $nickname, $sex );
		ClientView::show ( $returnValue );
	}
	//礼品码验证
	function lpm($getInfo) {
		$lpm = _get ( 'lpm' );
		$userid = $getInfo ['userid'];
		$returnValue = roleModel::lpm ( $lpm, $userid );
		ClientView::show ( $returnValue );
	}
}
