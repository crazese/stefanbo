<?php
class zydModel {
	//检索我的野地信息
	/* $playersid 玩家ID
	 * $qqtype 查看类型 1、查看自家资源点 2、查看占领资源点
	 * $setMc 是否更新缓存1是，其他否
	 * $gxzyd 是否更新资源点 1是其他否
	 * */
	public static function jszyd($playersid,$qqtype,$setMc = 2,$gxzyd = 1, $close = 0) {
		global $db, $common, $mc, $_SGLOBAL, $_SC, $MemcacheList, $Memport;
		if ($close == 1) {
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
		}		
		$nowTime = date('Y-m-d',$_SGLOBAL['timestamp']);
		$zydlist = array();
		if ($qqtype == 1) {
			$ydInfo = $mc->get(MC.$playersid."_ydxx");
		} else {
			$ydInfo = $mc->get(MC.$playersid."_zlydxx");
		}
		if ($close == 1) {
			$ydInfo = null;
			$mc->delete(MC.$playersid."_ydxx");
			$mc->delete(MC.$playersid."_zlydxx");
		}		
		$update = 0;
		if (!$ydInfo) {
			if ($close == 1) {
				$sql = "SELECT * FROM ".$common->tname('zyd')." WHERE  playersid = '$playersid' || zlpid = '$playersid'";
			} else {
				if ($qqtype == 1) {
					$sql = "SELECT * FROM ".$common->tname('zyd')." WHERE playersid = '$playersid' LIMIT 9";
				} else {
					$sql = "SELECT * FROM ".$common->tname('zyd')." WHERE zlpid = '$playersid'";
				}
			}
			$mc->delete(MC.$playersid.'_jq');
			$result = $db->query($sql);
			$ydInfo = array();
			while ($rows = $db->fetch_array($result)) {
				if ($gxzyd == 1) {
					$jgts = zydModel::dateDiff($rows['ydscsj'], $nowTime);
					$yd_id = $rows['yd_id'];
					if ($jgts > 0) {
						$nowjb = $rows['yd_level'] - $jgts;
						if ($nowjb > 0) {
							$rows['yd_level'] = $nowjb;
							$rows['ldzyid'] = 0;
							$rows['ydscsj'] = date('Y-m-d',$_SGLOBAL['timestamp']);
							$sql = "UPDATE ".$common->tname('zyd')." SET yd_level = '$nowjb',ldzyid = 0,ydscsj = '".$rows['ydscsj']."' WHERE yd_id = '$yd_id' LIMIT 1";
							$db->query($sql);
							$sql = null;
						} else {
							//需要结算资源						
							$scyd = zydModel::scyd($rows['yd_id'],$rows);
							$rows['yd_type'] = $scyd['lx'];
							$rows['yd_level'] = $scyd['jb'];
							$rows['zydiid'] = $scyd['iid'];
							$rows['ydscsj'] = date('Y-m-d',$_SGLOBAL['timestamp']);
							$rows['zlpid'] = 0;
							$rows['zlwjid'] = 0;
							$rows['scsysj'] = 0;
							$rows['zlsj'] = 0;							
							$rows['ldbhcs'] = 0;
							$rows['zlwjmc'] = '';
							$rows['zlpname'] = '';
							$rows['ldzyid'] = 0;
							$rows['ldsl'] = 0;
							$rows['wjmc'] = '';							
						}
						$nowjb = null;	
						$yd_id = null;								
					}
					$jgts = null;
				}
				$ydInfo[$rows['yd_id']] = $rows;					
			}
			$update = 1;
			if ($setMc == 1 && $update == 1 && $close == 0) {
				if ($qqtype == 1) {
					$mc->set(MC.$playersid."_ydxx",$ydInfo,0,3600);
				} else {
					$mc->set(MC.$playersid."_zlydxx",$ydInfo,0,3600);
				}
			}
		} else {
			if ($gxzyd == 1) {
				foreach ($ydInfo as $ydInfoKey => $rows) {
					$jgts = zydModel::dateDiff($rows['ydscsj'], $nowTime);
					$yd_id = $rows['yd_id'];				
					if ($jgts > 0) {
						$nowjb = $rows['yd_level'] - $jgts;
						if ($nowjb > 0) {
							$rows['yd_level'] = $nowjb;
							$rows['ydscsj'] = date('Y-m-d',$_SGLOBAL['timestamp']);
							$rows['ldzyid'] = 0;
							$sql = "UPDATE ".$common->tname('zyd')." SET yd_level = '$nowjb',ldzyid = 0,ydscsj = '".$rows['ydscsj']."' WHERE yd_id = '$yd_id' LIMIT 1";
							$db->query($sql);
							$sql = null;
						} else {	
							//需要结算资源					
							$scyd = zydModel::scyd($rows['yd_id'],$rows);
							$rows['yd_type'] = $scyd['lx'];
							$rows['yd_level'] = $scyd['jb'];
							$rows['zydiid'] = $scyd['iid'];
							$rows['ydscsj'] = date('Y-m-d',$_SGLOBAL['timestamp']);
							$rows['zlpid'] = 0;
							$rows['zlwjid'] = 0;
							$rows['scsysj'] = 0;
							$rows['zlsj'] = 0;
							$rows['ldbhcs'] = 0;
							$rows['zlwjmc'] = '';
							$rows['zlpname'] = '';
							$rows['ldzyid'] = 0;
							$rows['ldsl'] = 0;
							$rows['wjmc'] = '';
						}
						$nowjb = null;	
						$yd_id = null;		
						$update = 1;		
					}
					$ydInfo[$rows['yd_id']] = $rows;
					$jgts = null;							
				}
				if ($setMc == 1 && $update == 1) {
					if ($qqtype == 1) {
						$mc->set(MC.$playersid."_ydxx",$ydInfo,0,3600);
					} else {
						$mc->set(MC.$playersid."_zlydxx",$ydInfo,0,3600);
					}
				}
			}			
		}
		if ($close == 1) {
			$db->close();
			$mc->close();
		}		
		if (empty($ydInfo)) {
			return false;
		} else {
			return $ydInfo;
		}
	}
	
	//获取单武将信息
	public static function hqdwjxx($gInfo,$gid) {
		$value = null;
		for($i = 0; $i < count ( $gInfo ); $i ++) {
			if ($gInfo [$i] ['intID'] == $gid) {
				$value = $gInfo [$i];
				break;
			}
		}
		return $value;
	}
	
	//玩家资源点列表
	public static function zydlb($pid,$qqtype,$gxxwzt = false) {
		global $_SGLOBAL,$common,$db,$zyd_lang,$G_PlayerMgr;
		if ($qqtype == 1) {
			$setMc = 1;
		} else {
			$setMc = 2;
		}
		if (!empty($gxxwzt) && $_SESSION['playersid'] == $pid) {
			$player = $G_PlayerMgr->GetPlayer($pid );
			$xwzt_30 = substr($player->baseinfo_['xwzt'],29,1);
			if ($xwzt_30 == 0) {
				$updateRole['xwzt'] = substr_replace($player->baseinfo_['xwzt'],'1',29,1);
				$updateRoleWhere['playersid'] = $pid;
				$common->updatetable('player',$updateRole,$updateRoleWhere);
				$common->updateMemCache(MC.$pid,$updateRole);
			}				
		}
		$zydInfo = zydModel::jszyd($pid,$qqtype,$setMc);
		/*$gInfo = cityModel::getGeneralData($pid,false,'*',false);		
		if (empty($gInfo)) {
			return array('status'=>1021,'message'=>'该资源点武将数据有误');
		}*/
		if (empty($zydInfo)) {
			return array('status'=>1021,'message'=>$zyd_lang['zydlb_1021']);
		} else {
			/*$zlarray = $zsarray = $ldarray = array();
			foreach ($gInfo as $gInfoValue) {
				if ($gInfoValue['act'] == 2) {
					$zlarray[] = $gInfoValue['zydid'];
				}
				if ($gInfoValue['act'] == 3) {
					$ldarray[] = $gInfoValue['zydid'];
				}					
			}*/			
			$zydlist = array();
			/*foreach ($zydInfo as $zydInfoValue) {
				$zydArray[] = $zydInfoValue['yd_id'];
			}
			$sql = "SELECT * FROM ".$common->tname('jq')." WHERE zyid IN (".implode(',',$zydArray).") && jqlx = 3 || jqlx = 2 GROUP BY zyid ORDER BY ddsj ASC";
			$result = $db->query($sql);
			$qtldzyd = array();
			$qtzlzyd = array();
			while ($jqRows = $db->fetch_array($result)) {
				if ($jqRows['jqlx'] == 3) {
					$qtldzyd[] = $jqRows['zyid'];
				}
				if ($jqRows['jqlx'] == 2) {
					$qtldzyd[] = $jqRows['zyid'];
				}				
			}*/
			$zlzydsl = array();
			foreach ($zydInfo as $rows) {
				if ($rows['playersid'] == $rows['zlpid']) {
					$zlzydsl[] = 1;
					$zllx = 1;
				} elseif ($rows['zlpid'] == 0) {
					$zllx = 2;
				} else {
					$zllx = 3;
				}	
				/*$gidInfo = zydModel::hqdwjxx($gInfo,$rows['yd_id']);
				if ($gidInfo['zydid'] == $rows['yd_id']) {
					$ddsj = $gidInfo['gohomeTime'] - $_SGLOBAL['timestamp'];
				}*/
				$shsh = $_SGLOBAL['timestamp'] - $rows['scsysj'];
				if ($shsh >= 3600) {
					$sh = 1;
				} else {
					$sh = 0;
				}
				if ($rows['zlpid'] != 0) {
					$zlsj = $_SGLOBAL['timestamp'] - $rows['zlsj'];
				} else {
					$zlsj = 0;
				}
				/*if (in_array($rows['yd_id'],$zlarray)) {
					$zt = 1;
				} elseif (in_array($rows['yd_id'],$ldarray)) {
					$zt = 2;
				} elseif (in_array($rows['yd_id'],$qtldzyd)) {
					$zt = 3;
				} else {
					$zt = 0;
				}*/
				if (!empty($rows['zlwjmc'])) {
					$xm = $rows['zlwjmc'];
				} else {
					$npcInfo = ydsj($rows['yd_level'],$rows['yd_type']);
					$xm = $npcInfo['xm'];
				}
				$zydlist[] = array('lx'=>$zllx,'zydid'=>intval($rows['yd_id']),'zydlx'=>intval($rows['yd_type']),'zydj'=>intval($rows['yd_level']),'wjmc1'=>$rows['zlpname'],'wjid1'=>intval($rows['zlpid']),'zssj'=>$zlsj,'sh'=>$sh,'gid'=>intval($rows['zlwjid']),'xm'=>$xm,'tuid'=>intval($rows['playersid']));	
				$zllx = null;
				$shsh = null;	
				$zlsj = null;
				$sh = null;	
				$zt = null;		
				$npcInfo = null;
				$xm = null;	
			}
			if ($_SESSION['playersid'] == $pid) {
				$player = $G_PlayerMgr->GetPlayer($pid);
				$roleInfo = $player->baseinfo_;
				$roleInfo['rwsl'] = count($zlzydsl);
				$rwid = questsController::OnFinish ( $roleInfo, "'zyd'" );
				if (!empty($rwid)) {
					$value['rwid'] = $rwid;
				}
			}
		}
		if (empty($zydlist)) {
			if ($qqtype == 1) {
				$msg = $zyd_lang['zydlb_1021_2'];
			} else {
				$msg = $zyd_lang['zydlb_1021_3'];
			}
			return array('status'=>1021,'message'=>$msg);
		} else {
			return array('status'=>0,'zydlist'=>$zydlist);
		}
	}
	
	//重新生成资源点$yd_id资源点ID，$oldzydInfo老资源点信息，$zrsx是否自然刷新 2自然刷新1强行刷行
	public static function scyd($yd_id,$oldzydInfo,$zrsx = 2) {
		global $db, $common, $mc, $_SGLOBAL, $zyd_lang;
		$mc->delete(MC.$oldzydInfo['playersid']."_ydxx");
		$mc->delete(MC.$oldzydInfo['playersid']."_zlydxx");	
		if ($oldzydInfo['zlpid'] != 0) {
			$mc->delete(MC.$oldzydInfo['zlpid']."_ydxx");
			$mc->delete(MC.$oldzydInfo['zlpid']."_zlydxx");	
		}		
		//$mc->delete(MC.$pid."_ydxx");
		//$mc->delete(MC.$pid."_zlydxx");	
		if ($zrsx == 2) {
			$nowTime = strtotime($oldzydInfo['ydscsj']) + 86400;
		} else {				
			$nowTime = $_SGLOBAL['timestamp'];
		}
		//结算该资源点
		if ($oldzydInfo['zlpid'] != 0) {
			$roleInfo['playersid'] = $oldzydInfo['zlpid'];
			$res = roleModel::getRoleInfo($roleInfo);
			$jjsj = $nowTime - $oldzydInfo['scsysj'];
			if ($jjsj >= 3600) {
				$zyzsInfo = zydzy($oldzydInfo['yd_type'],$oldzydInfo['yd_level'],$jjsj,$oldzydInfo['ldzyid']);
				$value['status'] = 0;
				/*结算获取的资源*/
				if (isset($zyzsInfo['tq'])) {
					$ldzyAmount = floor(($zyzsInfo['tq'] - $oldzydInfo['ldsl']) / 2); //该次掠夺资源数量
					if ($ldzyAmount > 0) {
						//$updateRole['coins'] = $roleInfo['coins'] + $ldzyAmount;
						//$common->updatetable('player',$updateRole,"playersid = '".$oldzydInfo['zlpid']."'");
						//$common->updateMemCache(MC.$oldzydInfo['zlpid'],$updateRole);
						$jlsj = json_encode(array('tq'=>$ldzyAmount));
						$jlmc = $zyd_lang['scyd_1'];
						$wpsl = $ldzyAmount;
					} else {
						$jlsj = '';
					}
				} elseif (isset($zyzsInfo['jl'])) {					
					$ldzyAmount = floor(($zyzsInfo['jl'] - $oldzydInfo['ldsl']) / 2); //该次掠夺资源数量
					if ($ldzyAmount > 0) {
						//$updateRole['food'] = $roleInfo['food'] + $ldzyAmount;
						//$common->updatetable('player',$updateRole,"playersid = '".$oldzydInfo['zlpid']."'");
						//$common->updateMemCache(MC.$oldzydInfo['zlpid'],$updateRole);
						$jlsj = json_encode(array('jl'=>$ldzyAmount));
						$jlmc = $zyd_lang['scyd_2'];
						$wpsl = $ldzyAmount;						
					} else {
						$jlsj = '';
					} 				
				} else {
					if ($zyzsInfo != 0) {
						foreach ($zyzsInfo['dj'] as $key => $zyzsInfovalue) {
							$ldzyAmount = floor(($zyzsInfovalue - $oldzydInfo['ldsl']) / 2); //该次掠夺资源数量
							if ($key == 20009 && $ldzyAmount > 4) {  //当金刚石碎片大于4时，有50%几率转换为玄铁碎片，数量为金刚石产量1/4
								if (rand(1,2) == 1) {
									$key = 20127;
									$ldzyAmount = round($ldzyAmount / 4 ,0);
								}
							}							
							/*if ($ldzyAmount > 0) {
								$toolsRes = toolsModel::addPlayersItem($roleInfo,$key,$ldzyAmount);												
							} */
							$djInfo = toolsModel::getItemInfo($key);
							$jlmc = $djInfo['Name'];
							$jlsj = json_encode(array('dj'=>array($key=>$ldzyAmount))); 
							$wpsl = $ldzyAmount;								
						}
					} else {
						$jlsj = '';
					}				
				}		
				/*结算结束*/	
			} else {
				$jlsj = '';
			}			
		}
		//所有与该资源点的情况全部返回
		$sql = "SELECT * FROM ".$common->tname('jq')." WHERE zyid = '$yd_id' && jqlx != 2 && jqlx != 4";
		$result = $db->query($sql);		
		while ($rows = $db->fetch_array($result)) {					
			if ($nowTime - $rows['createTime'] > 2 * $rows['xhsj'] && $rows['jqlx'] != 1 && $zrsx != 1) {
				$updateGen = "UPDATE ".$common->tname('playergeneral')." SET gohomeTime = 0,act=0,zydid='0' WHERE intID = '".$rows['wfgid']."' LIMIT 1";
				$db->query($updateGen);  
				$mc->delete(MC.$rows['wfpid'].'_general');	
				$updateSql = "DELETE FROM ".$common->tname('jq')." WHERE jq_id = '".$rows['jq_id']."'";
				$db->query($updateSql);		
				$msg['X'] = $rows['wfgname'];
				//$msg['Y'] = $oldzydInfo['nickname'];	
				$msg['L'] = $oldzydInfo['yd_level'];	
				$msg['R'] = zydmc($oldzydInfo['yd_type']);	
				$msg['xllx'] = 54;	
				//				$mesageDen = addcslashes(json_encode($msg),'\\');				
				$mesageDen = $msg;
				$messageInfoDen = array('playersid'=>0,'jsid'=>$rows['wfpid'],'toplayersid'=>$rows['wfpid'],'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
				//$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
				$messageInfoDen['genre'] = $msg['xllx'];
				lettersModel::addMessage($messageInfoDen);		
				$messageInfoDen = null;
				$mesageDen = null;
				$msg = null;											
			} else {
				if ($rows['wfpid'] == $oldzydInfo['zlpid']) {
					if ($nowTime - $rows['createTime'] > 2 * $rows['xhsj'] && $zrsx != 1) {
						$updateGen = "UPDATE ".$common->tname('playergeneral')." SET gohomeTime = 0,act=0,zydid='0' WHERE intID = '".$rows['wfgid']."' LIMIT 1";
						//$updateSql = "DELETE FROM ".$common->tname('jq')." WHERE jq_id = '".$rows['jq_id']."'";						
						$msg['X'] = $rows['wfgname'];
						//$msg['Y'] = $oldzydInfo['nickname'];	
						$msg['L'] = $oldzydInfo['yd_level'];	
						$msg['R'] = zydmc($oldzydInfo['yd_type']);							
						if (empty($jlsj) || $wpsl <= 0) {
							$msg['xllx'] = 49;
						} else {
							$msg['xllx'] = 48;
							$msg['B'] = $jlmc.' X '.$wpsl;
							//zydModel::jlzy($roleInfo,$jlsj);
							if (!empty($jlsj)) {
								$wp_jlInfo = json_decode($jlsj,true);
								$hqtq = (isset($wp_jlInfo['tq']))?$wp_jlInfo['tq']:0;								
								zydModel::zydlog('app.php?task=sx&option=zyd&dj='.$oldzydInfo['yd_level'].'&lx='.$oldzydInfo['yd_type'].'&sc='.$jjsj,array('wp'=>$wp_jlInfo,'status'=>0,'hqtq'=>$hqtq),$roleInfo['player_level'],$roleInfo['userid']);
							}
						}	
						//$mesageDen = addcslashes(json_encode($msg),'\\');				
						$mesageDen = $msg;
						$messageInfoDen = array('playersid'=>0,'jsid'=>$rows['wfpid'],'toplayersid'=>$rows['wfpid'],'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
						//$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
						$messageInfoDen['genre'] = $msg['xllx'];
						lettersModel::addMessage($messageInfoDen);		
						$messageInfoDen = null;
						$mesageDen = null;
						$msg = null;			
						$updateSql = "UPDATE ".$common->tname('jq')." SET ddsj = '$nowTime',dfpid = 0,dfmc = '',jlsj = '$jlsj',jqlx = '4',createTime='".$_SGLOBAL['timestamp']."' WHERE jq_id = '".$rows['jq_id']."' LIMIT 1";		
					} else {
						$fhsj = $rows['xhsj'] + $nowTime;
						$updateSql = "UPDATE ".$common->tname('jq')." SET jlsj = '$jlsj',ddsj = '$fhsj',dfpid = 0,dfmc = '',jqlx = '4',createTime='".$_SGLOBAL['timestamp']."' WHERE jq_id = '".$rows['jq_id']."' LIMIT 1";						
						$syjg = $nowTime - $oldzydInfo['scsysj']; //上次收益间隔时间
						$msg['X'] = $rows['wfgname'];
						//$msg['Y'] = $oldzydInfo['nickname'];	
						$msg['L'] = $oldzydInfo['yd_level'];	
						$msg['R'] = zydmc($oldzydInfo['yd_type']);						
						if ($syjg < 3600) {
							$msg['xllx'] = 47;						
						} else {
							if (empty($jlsj) || $wpsl <= 0) {
								$msg['xllx'] = 49;
							} else {
								$msg['xllx'] = 48;
								$msg['B'] = $jlmc.' X '.$wpsl;	
								if (!empty($jlsj)) {
									$wp_jlInfo = json_decode($jlsj,true);
									$hqtq = (isset($wp_jlInfo['tq']))?$wp_jlInfo['tq']:0;
									zydModel::zydlog('app.php?task=sx&option=zyd&dj='.$oldzydInfo['yd_level'].'&lx='.$oldzydInfo['yd_type'].'&sc='.$jjsj,array('wp'=>$wp_jlInfo,'status'=>0,'hqtq'=>$hqtq),$roleInfo['player_level'],$roleInfo['userid']);
								}															
							}
						}
						//	$mesageDen = addcslashes(json_encode($msg),'\\');				
						$mesageDen = $msg;
						$messageInfoDen = array('playersid'=>0,'jsid'=>$rows['wfpid'],'toplayersid'=>$rows['wfpid'],'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
						//$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
						$messageInfoDen['genre'] = $msg['xllx'];
						lettersModel::addMessage($messageInfoDen);		
						$messageInfoDen = null;
						$mesageDen = null;
						$msg = null;					
						$syjg = null;
						$updateGen = "UPDATE ".$common->tname('playergeneral')." SET gohomeTime = '$fhsj',act='4',zydid='0' WHERE intID = '".$rows['wfgid']."' LIMIT 1";
					}
				} else {
					$sysj = $rows['ddsj'] - $nowTime;
					if ($sysj < 0) {
						$sysj = 0;
					}
					$fhsj = $nowTime + ($rows['xhsj'] - $sysj);
					$updateSql = "UPDATE ".$common->tname('jq')." SET ddsj = '$fhsj',dfpid = 0,dfmc = '',jqlx = '4',createTime='".$_SGLOBAL['timestamp']."' WHERE jq_id = '".$rows['jq_id']."' LIMIT 1";
					$updateGen = "UPDATE ".$common->tname('playergeneral')." SET gohomeTime = '$fhsj',act='4',zydid='0' WHERE intID = '".$rows['wfgid']."' LIMIT 1";
				}
				$mc->set(MC.$rows['wfpid'].'_jq',1,0,1800); //设置军情状态					
				$db->query($updateGen);  
				$mc->delete(MC.$rows['wfpid'].'_general');	
				$db->query($updateSql);	
			}							
		} 	
		//所有与该资源点的情况全部返回END			
		$nowTime = date('Y-m-d',$_SGLOBAL['timestamp']);
		$lx = rand(1,5);
		$jb = rand(1,10);
		switch ($lx) {
			case 1:
				$iid = 'a';
				break;
			case 2:
				$iid = 'b';
				break;
			case 3:
				$iid = 'c';
				break;
			case 4:
				$iid = 'd';
				break;
			default:
				$iid = 'e';
				break;												
		}		
		$sql = "UPDATE ".$common->tname('zyd')." SET yd_type = '$lx',yd_level = '$jb',zlpid = 0,zlpname='',zlwjid = 0,zlwjmc = '',ydscsj = '$nowTime',scsysj = 0,zlsj = 0,zydiid = '$iid',ldsl=0,ldzyid=0,ldbhcs=0 WHERE yd_id = '$yd_id' LIMIT 1";
		$db->query($sql);
		return array('lx'=>$lx,'jb'=>$jb,'iid'=>$iid);
	}
	
	//生成资源点
	public static function sczyd($playersid,$nickname) {
		global $db, $common, $mc, $_SGLOBAL;
		$nickname = mysql_escape_string($nickname);
		$nowTime = date('Y-m-d',$_SGLOBAL['timestamp']);
		for ($i=0;$i<9;$i++) {
			$lx = rand(1,5);
			$jb = rand(1,10);
			switch ($lx) {
				case 1:
					$iid = 'a';
					break;
				case 2:
					$iid = 'b';
					break;
				case 3:
					$iid = 'c';
					break;
				case 4:
					$iid = 'd';
					break;
				default:
					$iid = 'e';
					break;												
			}
			$sql = "INSERT INTO ".$common->tname('zyd')."(yd_type,yd_level,ydscsj,zydiid,playersid,nickname) VALUES ('$lx','$jb','$nowTime','$iid','$playersid','$nickname')";				
			$db->query($sql);
			$lx = $jb = $iid = $sql = null;
		}	
	}
	
    //计算两个日期所间隔天数
	public static function dateDiff($start, $end) {	
		$start_ts = strtotime($start);		
		$end_ts = strtotime($end);		
		$diff = $end_ts - $start_ts;		
		return intval($diff / 86400);	
	}

	//元宝刷新资源点
	public static function sxzyd($playersid) {
		global $db, $common, $mc, $_SGLOBAL, $sys_lang;
		$sxzydyb = 5;		
		$roleInfo['playersid'] = $playersid;
		$result = roleModel::getRoleInfo($roleInfo);
		if (empty($result)) {
			return array('status'=>1021,'message'=>$sys_lang[1]);
		} else {
			$value['status'] = 0;
			if ($roleInfo['ingot'] >= $sxzydyb) {
				$zydInfo = zydModel::jszyd($playersid,1,2,2);
				$ydarray = array();
				foreach ($zydInfo as $zydInfoKey => $zydInfoValue) {
					$scyd = zydModel::scyd($zydInfoValue['yd_id'],$zydInfoValue,1);
					$zydInfoValue['yd_type'] = $scyd['lx'];
					$zydInfoValue['yd_level'] = $scyd['jb'];
					$zydInfoValue['zydiid'] = $scyd['iid'];
					$zydInfoValue['ydscsj'] = $_SGLOBAL['timestamp'];
					$zydInfoValue['zlpid'] = 0;
					$zydInfoValue['zlwjid'] = 0;
					$zydInfoValue['scsysj'] = 0;
					$zydInfoValue['zlsj'] = 0;
					$zydInfoValue['ldbhcs'] = 0;	
					//$ydInfo[$zydInfoValue['yd_id']] = $zydInfoValue;	
					$npcInfo = ydsj($zydInfoValue['yd_level'],$zydInfoValue['yd_type']);
					$ydarray[] = array('zydid'=>intval($zydInfoValue['yd_id']),'zydlx'=>intval($zydInfoValue['yd_type']),'zydj'=>intval($zydInfoValue['yd_level']),'xm'=>$npcInfo['xm']);			
					$npcInfo = null;
				}
				$value['zydlist'] = $ydarray;
				$updateRole['ingot'] = $roleInfo['ingot'] - $sxzydyb;
				$value['yb'] = $updateRole['ingot'];
				$value['xhyb'] = $sxzydyb;
				$updateRoleWhere['playersid'] = $playersid;
				$common->updatetable('player',$updateRole,$updateRoleWhere);
				$common->updateMemCache(MC.$playersid,$updateRole);				
				//$mc->set(MC.$playersid."_ydxx",$ydInfo,0,3600);	
				//$mc->delete(MC.$playersid."_ydxx");			
				//$mc->delete(MC.$playersid."_zlydxx");
				$rwid = questsController::OnFinish($roleInfo,"'sxzyd'");  
			    if (!empty($rwid)) {
			         $value['rwid'] = $rwid;				             
			    }				
			} else {
				$arr1 = array('{xhyb}','{yb}');
				$arr2 = array($sxzydyb,$roleInfo['ingot']);
				$value = array('status'=>88,'yb'=>floor ( $roleInfo ['ingot'] ),'message'=>str_replace($arr1, $arr2, $sys_lang[2]));
			}
		}		
		return $value;
	}
	
	//获取单个资源点信息
	public static function hqdzydxx($pid,$zyid) {
		global $db, $common, $mc, $_SGLOBAL, $zyd_lang;
		$zydInfo = $mc->get(MC.$pid."_ydxx");
		if (!($zydInfo = $mc->get(MC.$pid."_ydxx"))) {
			$sql = "SELECT * FROM ".$common->tname('zyd')." WHERE yd_id = '$zyid' && playersid = '$pid' LIMIT 1";
			$result = $db->query($sql);
			$rows = $db->fetch_array($result);
			if (empty($rows)) {
				return array('status'=>21,'message'=>$zyd_lang['zydlb_1021_2']);
			}
		} else {
			if (empty($zydInfo[$zyid])) {
				return array('status'=>21,'message'=>$zyd_lang['zydlb_1021_2']);
			} else {
				$rows = $zydInfo[$zyid];
			}
		}
		$nowTime = date('Y-m-d',$_SGLOBAL['timestamp']);
		$jgts = zydModel::dateDiff($rows['ydscsj'], $nowTime);
		if ($jgts > 0) {
			$nowjb = $rows['yd_level'] - $jgts;
			if ($nowjb > 0) {
				$rows['yd_level'] = $nowjb;
				$rows['ldzyid'] = 0;
				$rows['ydscsj'] = date('Y-m-d',$_SGLOBAL['timestamp']);
				$sql = "UPDATE ".$common->tname('zyd')." SET yd_level = '$nowjb',ldzyid = 0,ydscsj = '".$rows['ydscsj']."'  WHERE yd_id = '$zyid' LIMIT 1";
				$db->query($sql);
				$sql = null;
			} else {						
				$scyd = zydModel::scyd($rows['yd_id'],$rows);
				$rows['yd_type'] = $scyd['lx'];
				$rows['yd_level'] = $scyd['jb'];
				$rows['zydiid'] = $scyd['iid'];
				$rows['ydscsj'] = date('Y-m-d',$_SGLOBAL['timestamp']);
				$rows['zlpid'] = 0;
				$rows['zlwjid'] = 0;
				$rows['scsysj'] = 0;
				$rows['zlsj'] = 0;
				$rows['ldbhcs'] = 0;
			}
			$nowjb = null;	
			$yd_id = null;		
			$update = 1;		
		}
		return $rows;	
	}
	
	//我的军情
	/* $playersid 玩家ID
	 * $jqlx 1、我的军情 2、敌方军情
	 * */
	public static function hqjq($playersid,$jqlx) {
		global $sxzydyb, $db, $common, $_SGLOBAL,$mc,$zyd_lang,$G_PlayerMgr;
		if ($jqlx == 1) {
			$sql = "SELECT * FROM ".$common->tname('jq')." WHERE wfpid = '$playersid' ORDER BY createTime DESC LIMIT 40";
		} else {
			$sql = "SELECT * FROM ".$common->tname('jq')." WHERE dfpid = '$playersid' && dfpid != wfpid ORDER BY createTime DESC LIMIT 40";
		}
		$result = $db->query($sql);
		$mc->delete(MC.$playersid.'_jq');
		$jqArray = array();
		$zymc = '';
		$zumc = '';
		$zlzydsl = array();
		while ($rows = $db->fetch_array($result)) {
			$zydInfo = zydModel::hqdzydxx($rows['zydowner'],$rows['zyid']);
			if ($jqlx == 1 && $rows['jqlx'] == 1) {
				$zlzydsl[] = 1;
			}
			if ($rows['jqlx'] == 7) {
				$rows['jqlx'] = 4;
			}			
			if ($rows['jqlx'] == 4) {
				$zydInfo = array('yd_id'=>'','yd_type'=>'','yd_level'=>'','playersid'=>'','nickname'=>'','zlpid'=>'','zlpname'=>'','zlwjid'=>'','zlwjmc'=>'','ydscsj'=>'','scsysj'=>'','zlsj'=>'','zydiid'=>'','wjmc'=>'','ldsl'=>'','ldzyid'=>'');
			} elseif ($rows['jqlx'] == 5 || $rows['jqlx'] == 6) {
				$zydInfo = zydModel::hqzlzyd($rows['zyid']);
				$zymc = $zydInfo['zldd'];
				$zumc = $zydInfo['zlmc'];
			} else {
				$zydInfo = zydModel::hqdzydxx($rows['zydowner'],$rows['zyid']);
			}
			if (empty($zydInfo)) {
				continue;
			}		
			$lx = intval($rows['jqlx']);
			//$wjmc1 = $rows['dfmc'];
			//$wjid1 = intval($rows['dfpid']);
			$wjmc2 = $rows['wfmc'];
			$wjid2 = intval($rows['wfpid']);				
					
			$jlmc1 = $zydInfo['zlpname'];
			
			$jlmc2 = $rows['wfgname'];
			$jlid2 = intval($rows['wfgid']);
			$zydlx = $zydInfo['yd_type'];					
			$zydj = $zydInfo['yd_level'];
			if ($zydInfo['zlsj'] > 0) {
				$zssj = $_SGLOBAL['timestamp'] - $zydInfo['zlsj'];
			} else {
				$zssj = 0;
			}
			//$ddsj = intval($rows['ddsj']);
			$ddsj = $rows['ddsj'] - $_SGLOBAL['timestamp'];	
			if ($ddsj < 0) {
				$ddsj = 0;
			}		
			if ($ddsj <= 0 && $rows['jqlx'] != 1 && $rows['jqlx'] != 5 && $rows['jqlx'] != 6) {
				continue;
			}
			if ($rows['jqlx'] == 2) {
				if ($zydInfo['zlpid'] == 0) {
					$gInfo = ydsj($zydInfo['yd_level'],$zydInfo['yd_type']);
					//$wjmc1 = 'NPC';
					$wjmc1 = $zydInfo['nickname'];
					$wjid1 = '';
					$jlid1 = '';
					$jlmc1 = $gInfo['xm'];
					$gInfo = null;
				} else {
					$wjmc1 = $zydInfo['zlpname'];
					$wjid1 = $zydInfo['zlpid'];
					$jlid1 = $zydInfo['zlwjid'];
					$jlmc1 = $zydInfo['zlwjmc'];					
				}
			} elseif ($rows['jqlx'] == 3) {
					$wjmc1 = $zydInfo['zlpname'];
					$wjid1 = $zydInfo['zlpid'];
					$jlid1 = $zydInfo['zlwjid'];
					$jlmc1 = $zydInfo['zlwjmc'];				
			} else {
				$wjmc1 = '';
				$wjid1 = '';
				$jlid1 = '';
				$jlmc1 = '';
			}
			$rq = $rows['createTime'] * 1000;
			$jqArray[] = array('rq'=>$rq,'lx'=>$lx,'wjmc1'=>$wjmc1,'wjid1'=>$wjid1,'wjmc2'=>$wjmc2,'wjid2'=>$wjid2,'jlmc1'=>$jlmc1,'jlid1'=>$jlid1,'jlmc2'=>$jlmc2,'jlid2'=>$jlid2,'zydlx'=>$zydlx,'zydj'=>$zydj,'zssj'=>$zssj,'ddsj'=>$ddsj,'jqid'=>intval($rows['jq_id']),'tuid'=>intval($rows['zydowner']),'zydid'=>intval($rows['zyid']),'zymc'=>$zymc,'zumc'=>$zumc);
			$rq = $lx = $wjmc1 = $wjid1 = $wjmc2 = $wjid2 = $jlmc1 = $jlid1 = $jlmc2 = $jlid2 = $zydlx = $zydj = $zssj = $ddsj = $zydInfo = null;
		}
		if ($jqlx == 1) {
			$player = $G_PlayerMgr->GetPlayer($playersid);
			$roleInfo = $player->baseinfo_;
			$roleInfo['rwsl'] = count($zlzydsl);
			$rwid = questsController::OnFinish ( $roleInfo, "'zyd'" );
			if (!empty($rwid)) {
				$value['rwid'] = $rwid;
			}
		}
		if (!empty($jqArray)) {
			$value['status'] = 0;
			$value['wfjqlist'] = $jqArray;
			return $value;
			//return array('status'=>0,'wfjqlist'=>$jqArray);
		} else {
			return array('status'=>1021,'message'=>$zyd_lang['hqjq_1021']);
		}
	}
	
	//资源点操作
	/* $playersid 玩家ID 
	 * $option 操作  1、占领 2、掠夺 3、中途召回 5、逐鹿战占领请求  7、逐鹿战中途召回请求 8 元宝强掠 9 道具强掠

	 * $zydid 资源ID
	 * $gid 武将ID
	 * $pid 资源点所有者玩家ID
	 * */
	public static function zydzd($playersid,$option,$zydid,$gid,$pid) {
		global $mc, $db, $common, $_SGLOBAL, $zyd_lang, $sys_lang,$G_PlayerMgr;
		$nowTime = $_SGLOBAL['timestamp'];
		$H = date('H',$nowTime);
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
		cityModel::resourceGrowth($roleInfo);       //结算军粮
		if ($option != 5 && $option != 7) {
			$zydInfo = zydModel::hqdzydxx($pid,$zydid);
			if (!empty($zydInfo['status'])) {
				return array('status'=>21,'message'=>$zyd_lang['zydzd_21_1']);
			}			
		} else {
			if ($option == 5) {
				$m = zyzkssj($zydid);
				if ($H < $m || $H >= $m + 1) {
					return array('status'=>21,'message'=>$zyd_lang['zydzd_21_2']);
				}				
				if ($zydid == 1) {
					if ($roleInfo['player_level'] > 10) {
						return array('status'=>21,'message'=>str_replace('{jb}','10',$zyd_lang['zydzd_21_3']));
					}
				} elseif ($zydid == 2) {
					/*if ($H < 13 || $H >= 14) {
						return array('status'=>21,'message'=>'该逐鹿点正处于关闭状态！');
					}*/
					if ($roleInfo['player_level'] > 20) {
						return array('status'=>21,'message'=>str_replace('{jb}','20',$zyd_lang['zydzd_21_3']));
					}					
				} elseif ($zydid == 3) {
					/*if ($H < 17 || $H >= 18) {
						return array('status'=>21,'message'=>'该逐鹿点正处于关闭状态！');
					}*/
					if ($roleInfo['player_level'] > 30) {
						return array('status'=>21,'message'=>str_replace('{jb}','30',$zyd_lang['zydzd_21_3']));
					}
				} elseif ($zydid == 4) {
					/*if ($H < 18 || $H >= 19) {
						return array('status'=>21,'message'=>'该逐鹿点正处于关闭状态！');
					}*/
					if ($roleInfo['player_level'] > 40) {
						return array('status'=>21,'message'=>str_replace('{jb}','40',$zyd_lang['zydzd_21_3']));
					}					
				} elseif ($zydid == 5) {
					/*if ($H < 19 || $H >= 20) {
						return array('status'=>21,'message'=>'该逐鹿点正处于关闭状态！');
					}*/
					if ($roleInfo['player_level'] > 50) {
						return array('status'=>21,'message'=>str_replace('{jb}','50',$zyd_lang['zydzd_21_3']));
					}					
				} elseif ($zydid == 6) {
					/*if ($H < 20 || $H >= 21) {
						return array('status'=>21,'message'=>'该逐鹿点正处于关闭状态！');
					}*/
					if ($roleInfo['player_level'] > 60) {
						return array('status'=>21,'message'=>str_replace('{jb}','60',$zyd_lang['zydzd_21_3']));
					}
				} else {
					/*if ($H < 21 || $H >= 22) {
						return array('status'=>21,'message'=>'该逐鹿点正处于关闭状态！');
					}*/
					if ($roleInfo['player_level'] > 70) {
						return array('status'=>21,'message'=>str_replace('{jb}','70',$zyd_lang['zydzd_21_3']));
					}					
				}
			}
			$zydInfo = zydModel::hqzlzyd($zydid);
			$zydInfo['playersid'] = 0;
		}
		if ($roleInfo['zf_aggressor_general'] == $gid) {
			return array('status'=>21,'message'=>$zyd_lang['zydzd_21_4']);
		}	

		$gInfo = cityModel::getGeneralData($playersid,false,$gid);		
		if (empty($gInfo)) {
			return array('status'=>21,'message'=>$sys_lang[3]);
		}
		/*$zydArray = array();
		foreach ($gInfo as $gInfoValue) {
			if (($gInfoValue['zydid'] != 0 && $gInfoValue['act'] == 2 && $gInfoValue['gohomeTime'] > $nowTime) || $gInfoValue['act'] == 1) {
				$zydArray[] = $gInfoValue['zydid'];
			}
		}
		if (in_array($zydid,$zydArray) && $option != 3) {
			return array('status'=>21,'message'=>'您已对该资源点进行了占领操作');
		}*/
		$wjInfo = $gInfo[0];
		if (empty($wjInfo)) {
			return array('status'=>21,'message'=>$sys_lang[3]);
		} elseif ($wjInfo['general_life'] <= 0) {
			return array('status'=>21,'message'=>$sys_lang[4]);
		} elseif ($wjInfo['f_status'] == 1) {
			if ($option == 1) {
				$msg = $zyd_lang['zydzd_21_5'];
			} else {
				$msg = $zyd_lang['zydzd_21_6'];
			}
			return array('status'=>21,'message'=>$msg);
		} elseif ($wjInfo['occupied_playersid'] == $playersid && $roleInfo['aggressor_general'] == $wjInfo['intID']) {
			if ($option == 1) {
				$msg = $zyd_lang['zydzd_21_7'];
			} else {
				$msg = $zyd_lang['zydzd_21_8'];
			}	
			return array('status'=>21,'message'=>$msg);		
		} elseif ($wjInfo['occupied_end_time'] > $nowTime && $wjInfo['occupied_playersid'] != $playersid && $wjInfo['occupied_playersid'] != 0) {
			if ($option == 1) {
				$msg = $zyd_lang['zydzd_21_9'];
			} else {
				$msg = $zyd_lang['zydzd_21_10'];
			}			
			return array('status'=>21,'message'=>$msg);
		} elseif ($wjInfo['gohomeTime'] < $nowTime && $option == 3) {
			return array('status'=>21,'message'=>$zyd_lang['zydzd_21_11']);
		} 
		if ($option != 3 && $option != 7) {
			if ($wjInfo ['act'] == 4 || $wjInfo ['act'] == 7) {
				$value ['status'] = 21;
				$value ['message'] = $zyd_lang['zydzd_21_12'];
				return $value;
			} elseif ($wjInfo ['act'] == 3 ) {
				$value ['status'] = 21;
				$value ['message'] = $zyd_lang['zydzd_21_13'];
				return $value;
			} elseif ($wjInfo ['act'] == 2 ) {
				$value ['status'] = 21;
				$value ['message'] = $zyd_lang['zydzd_21_14'];
				return $value;
			} elseif ( $wjInfo ['act'] == 1 || $wjInfo ['act'] == 6 ) {
				$value ['status'] = 21;
				$value ['message'] = $zyd_lang['zydzd_21_15'];
				return $value;
			}
		}
		if ($playersid == $zydInfo['playersid']) {
			$needTime = 300;    //需要花费时间
		} else {
			$needTime = 1800;   //需要花费时间
		}	
		if ($option == 1) {  //占领			
			if ($roleInfo['food'] < 1) {
				return array('status'=>888,'message'=>$sys_lang[5]);
			} else {
				$mc->set(MC.$playersid.'_jq',1,0,1800); //设置军情状态
				if (!empty($zydInfo['zlpid'])) {
					$mc->set(MC.$zydInfo['zlpid'].'_jq',1,0,1800); //设置军情状态
				}
				$insert['wfpid'] = $playersid;
				$insert['wfmc'] = mysql_escape_string($roleInfo['nickname']);
				$insert['wfgid'] = $gid;
				$insert['wfgname'] = $wjInfo['general_name'];
				$insert['zyid'] = $zydid;
				$insert['ddsj'] = $nowTime + $needTime;
				$insert['jqlx'] = 2;	
				$insert['dfpid'] = $zydInfo['zlpid'];
				//$insert['dfmc'] = mysql_escape_string($zydInfo['zlpname']);		
				$insert['zydowner'] = $zydInfo['playersid'];	
				$insert['createTime'] = $nowTime;	
				$insert['xhsj'] = $needTime;
				if ($zydInfo['zlpid'] != 0) {
					$insert['dfpid'] = $zydInfo['zlpid'];
					$insert['dfmc'] = mysql_escape_string($zydInfo['zlpname']);
				} else {
					$insert['dfpid'] = $zydInfo['playersid'];
					$insert['dfmc'] = mysql_escape_string($zydInfo['nickname']);					
				}
				$jqid = $common->inserttable('jq',$insert);		
				$updateRole['food'] = $roleInfo['food'] - 1;
				$updateRole['last_update_food'] = $nowTime;
	    		$value['jl'] = floor($updateRole['food']);    			
				$updateGen = "UPDATE ".$common->tname('playergeneral')." SET gohomeTime = '".$insert['ddsj']."',jqid='$jqid',zydid = '$zydid',act='2' WHERE intID = '$gid' LIMIT 1";
				$db->query($updateGen);  
				$rwid = questsController::OnFinish($roleInfo,"'zlzyd'");  
			    if (!empty($rwid)) {
			         $value['rwid'] = $rwid;				             
			    }						
				//$wjInfo['gohomeTime'] = $insert['ddsj'];
				//$wjInfo['jqid'] = $jqid;
				//$wjInfo['zydid'] = $zydid;
				//$wjInfo['act'] = 2;
				//$newValue[$wjInfo['sortid']] = $wjInfo;
				//$common->updateMemCache(MC.$playersid.'_general',$newValue);
			}
		} elseif ($option == 2 || $option ==8 || $option ==9) { // 掠夺(2) 强掠(8花元宝 9用道具)
			if ($zydInfo['zlpid'] == 0) {
				return array('status'=>21,'message'=>$zyd_lang['zydzd_21_16']);
			} elseif ($roleInfo['food'] < 1) {
				return array('status'=>888,'message'=>$sys_lang[5]);
			} else {
				// 如果在保护期内或者掠夺次数超出保护次数
				if($option == 2) {
					$zl_total_sj = ($_SGLOBAL['timestamp'] -  $zydInfo['zlsj']);
					if($zl_total_sj < 28800) {
						return array('status'=>1001,'message'=>$zyd_lang['zydzd_21_19']);
					} else if($zydInfo['ldbhcs'] >= 2){
						return array('status'=>1002,'message'=>$zyd_lang['zydzd_21_18']);
					}
				}
				
				if($zydInfo['ldbhcs'] < 2) {//$common->insertLog($zydInfo['ldbhcs']);
					$updateZy['ldbhcs'] = $zydInfo['ldbhcs'] + 1; // 不管强掠还是掠夺只要派出将就算一次
					$common->updatetable('zyd',$updateZy,"yd_id = '".$zydInfo['yd_id']."'");
					$mc->delete(MC.$zydInfo['playersid']."_ydxx");
				}
				
				// 如果花元宝或者道具强掠
				if(!($qianglv_cs = $mc->get(MC.$playersid.'_qianglv_cs'))) {
					$qianglv_cs = array('cs'=>1, 'sj'=>time());
					$mc->set(MC.$playersid.'_qianglv_cs', json_encode($qianglv_cs), 0, 3600*24);
					$qianglv_info = $qianglv_cs;
				} else {
					$qianglv_info = json_decode($qianglv_cs, true);
				}
				$qianglv_sj = $qianglv_info['sj'];
				date_default_timezone_set('PRC');
				if(date('Ymd', time()) != date('Ymd', $qianglv_sj)) {
					$qianglv_cs = array('cs'=>1, 'sj'=>time());
					//$mc->set(MC.$playersid.'_qianglv_cs', json_encode($qianglv_cs), 0, 3600*24);
					$qianglv_info = $qianglv_cs;
					$qianglv_sj = $qianglv_info['sj'];
				}
				
				$qianglv_cs = $qianglv_info['cs'];
				if($qianglv_cs > 4) {
					$qianglv_cs = 8;
				} else if($qianglv_cs == 3) {
					$qianglv_cs = 4;
				} else if($qianglv_cs == 4) {
					$qianglv_cs = 8;
				}
				
				if($option == 8) {
					if ($roleInfo['ingot'] < $qianglv_cs*10) {
						$arr1 = array('{xhyb}','{yb}');
						$arr2 = array($qianglv_cs*10, $roleInfo['ingot']);					
						return array('status'=>88,'message'=>str_replace($arr1, $arr2, $sys_lang[2]),'yb'=>floor($roleInfo['ingot']));
					}//$common->insertLog($qianglv_cs*10);
					// 使用了强掠后攻击力+100%
					$mc->set(MC.$playersid.'_'.$zydid.'_attack100', $zydid, 0, 1800);
					
					$qianglv_info1 = array('cs'=>$qianglv_info['cs']+1, 'sj'=>$qianglv_sj);
					$mc->set(MC.$playersid.'_qianglv_cs', json_encode($qianglv_info1), 0, 3600*24);
					
					$updateRole['ingot'] = intval($roleInfo['ingot']) - $qianglv_cs*10;
					$value['xhyb'] = $qianglv_cs*10;
					$value['yb'] = $updateRole['ingot'];
				} else if($option == 9) {
					$player = $G_PlayerMgr->GetPlayer($playersid );
					$delRet = $player->DeleteItemByProto(array(18611=>$qianglv_cs));
					if($delRet === false) {				
						return array('status'=>1003, 'message'=>$zyd_lang['zydzd_21_20']);
					}
					//$common->insertLog($qianglv_cs);
					// 使用了强掠后攻击力+100%
					$mc->set(MC.$playersid.'_'.$zydid.'_attack100', $zydid, 0, 1800);
					
					$qianglv_info1 = array('cs'=>$qianglv_info['cs']+1, 'sj'=>$qianglv_sj);
					$mc->set(MC.$playersid.'_qianglv_cs', json_encode($qianglv_info1), 0, 3600*24);
					
					$bagData = $player->GetClientBag();		
					$value['list'] = $bagData;
				}
				$mc->set(MC.$playersid.'_jq',1,0,1800); //设置军情状态
				$mc->set(MC.$zydInfo['zlpid'].'_jq',1,0,1800); //设置军情状态							
				$insert['wfpid'] = $playersid;
				$insert['wfmc'] = mysql_escape_string($roleInfo['nickname']);
				$insert['wfgid'] = $gid;
				$insert['wfgname'] = $wjInfo['general_name'];
				$insert['zyid'] = $zydid;
				$insert['ddsj'] = $nowTime + $needTime;
				$insert['jqlx'] = 3;	
				$insert['dfpid'] = $zydInfo['zlpid'];
				$insert['dfmc'] = mysql_escape_string($zydInfo['zlpname']);
				$insert['zydowner'] = $zydInfo['playersid'];
				$insert['createTime'] = $nowTime;		
				$insert['xhsj'] = $needTime;
				$jqid = $common->inserttable('jq',$insert);	
				$updateRole['food'] = $roleInfo['food'] - 1;
				$updateRole['last_update_food'] = $nowTime;
	    		$value['jl'] = floor($updateRole['food']); 	    	
				$updateGen = "UPDATE ".$common->tname('playergeneral')." SET gohomeTime = '".$insert['ddsj']."',jqid='$jqid',zydid = '$zydid',act='3' WHERE intID = '$gid' LIMIT 1";
				$db->query($updateGen);  
				$rwid = questsController::OnFinish($roleInfo,"'ldzyd'");  
			    if (!empty($rwid)) {
			         $value['rwid'] = $rwid;				             
			    }		
				//$wjInfo['gohomeTime'] = $insert['ddsj'];
				//$wjInfo['jqid'] = $jqid;
				//$wjInfo['act'] = 3;
				//$newValue[$wjInfo['sortid']] = $wjInfo;
				//$common->updateMemCache(MC.$playersid.'_general',$newValue);	   
			}
		} elseif ($option == 5) {
			if ($roleInfo['food'] < 1) {
				return array('status'=>888,'message'=>$sys_lang[5]);
			} else {
				$zlsl = 0;
				$zlsl = $mc->get(MC.$zydid."_zlsl"); //获取多少人逐鹿
				$mc->set(MC.$zydid."_zlsl",$zlsl + 1,0,1800);				
				$mc->set(MC.$playersid.'_jq',1,0,1800); //设置军情状态
				$insert['wfpid'] = $playersid;
				$insert['wfmc'] = mysql_escape_string($roleInfo['nickname']);
				$insert['wfgid'] = $gid;
				$insert['wfgname'] = $wjInfo['general_name'];
				$insert['zyid'] = $zydid;
				$insert['ddsj'] = $nowTime + 1800;
				$insert['jqlx'] = 5;	
				$insert['dfpid'] = 0;
				$insert['zydowner'] = $zyd_lang['zydzd_21_17'];	 //待定
				$insert['createTime'] = $nowTime;	
				$insert['xhsj'] = 1800;
				$insert['dfpid'] = 0;
				$insert['dfmc'] = '';				
				$jqid = $common->inserttable('jq',$insert);		
				$updateRole['food'] = $roleInfo['food'] - 1;
				$updateRole['last_update_food'] = $nowTime;				
	    		$value['jl'] = floor($updateRole['food']); 	   	
				$updateGen = "UPDATE ".$common->tname('playergeneral')." SET gohomeTime = '".$insert['ddsj']."',jqid='$jqid',zydid = '$zydid',act='2' WHERE intID = '$gid' LIMIT 1";
				$db->query($updateGen);	
				$cjInfo['qxzl'] = 1;
				achievementsModel::check_achieve($playersid,$cjInfo,array('qxzl'));
				$rwid = questsController::OnFinish($roleInfo,"'qxzl'");  
			    if (!empty($rwid)) {
			         $value['rwid'] = $rwid;				             
			    }								
				/*$wjInfo['gohomeTime'] = $insert['ddsj'];
				$wjInfo['jqid'] = $jqid;
				$wjInfo['act'] = 2;
				$newValue[$wjInfo['sortid']] = $wjInfo;
				$common->updateMemCache(MC.$playersid.'_general',$newValue);	*/ 				 
			}			
		} else {
			if ($option == 7) {
				$jqlx = 7;
				$zlsl = $mc->get(MC.$zydid."_zlsl"); //获取多少人逐鹿
				$newsl = $zlsl - 1;
				if($newsl < 0) {
					$newsl = 0;
				}
				$mc->set(MC.$zydid."_zlsl",$newsl,0,1800);						
				$msg['L'] = $zydInfo['dj'];	
			} else {
				$jqlx = 4;
				$msg['L'] = $zydInfo['yd_level'];	
			}
			$mc->set(MC.$playersid.'_jq',1,0,1800); //设置军情状态			
			$updateSql = "UPDATE ".$common->tname('jq')." SET ddsj = $nowTime + $needTime - ddsj + $nowTime,dfpid = 0,dfmc = '',jqlx = '$jqlx',createTime = '$nowTime' WHERE zyid = '$zydid' && wfgid = '$gid' LIMIT 1";
			$db->query($updateSql);
			$updateGen = "UPDATE ".$common->tname('playergeneral')." SET gohomeTime = $nowTime + $needTime - gohomeTime + $nowTime ,act='4',zydid ='0' WHERE intID = '$gid' LIMIT 1";
			$db->query($updateGen);
			/*$wjInfo['gohomeTime'] = $nowTime + $needTime - $wjInfo['gohomeTime'] + $nowTime;
			$wjInfo['act'] = 4;
			$wjInfo['zydid'] = 0;
			$newValue[$wjInfo['sortid']] = $wjInfo;
			$common->updateMemCache(MC.$playersid.'_general',$newValue);*/			
			$msg['xllx'] = 52;
			$msg['X'] = $wjInfo['general_name'];			
			$msg['R'] = zydmc($zydInfo['yd_type']);					
			//	$mesageDen = addcslashes(json_encode($msg),'\\');				
			$mesageDen = $msg;
			$messageInfoDen = array('playersid'=>0,'jsid'=>$roleInfo['playersid'],'toplayersid'=>$playersid,'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
			//$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
			$messageInfoDen['genre'] = 52;
			lettersModel::addMessage($messageInfoDen);		
			$messageInfoDen = null;
			$mesageDen = null;
			$msg = null;			
		}
		if (isset($updateRole)) {
		    $updateRoleWhere['playersid'] = $playersid;
    		$common->updatetable('player',$updateRole,$updateRoleWhere);
    		$common->updateMemCache(MC.$playersid,$updateRole);	   
		}		
		$mc->delete(MC.$playersid.'_general');
		$value['status'] = 0;
		$value['lx'] = intval($zydInfo['yd_type']);   //资源点类型 日志需要
		$value['dj'] = intval($zydInfo['yd_level']);  //资源点等级 日志需要
		return $value;
	}
	
	//从资源点撤退
	/*  $playersid  玩家ID
	 *  $zydid      资源点ID
	 *  $gid        武将ID
	 *  $pid        资源点所属玩家ID 
	 * */
	public static function zydcf($playersid,$zydid,$gid,$pid) {
		global $mc, $db, $common, $_SGLOBAL, $zyd_lang, $sys_lang;
		$nowTime = $_SGLOBAL['timestamp'];
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
		$zydInfo = zydModel::hqdzydxx($pid,$zydid);
		if (!empty($zydInfo['status'])) {
			return array('status'=>21,'message'=>$zyd_lang['zydlb_1021_2']);
		} elseif ($zydInfo['zlpid'] != $playersid) {
			$value['un'] = $zydInfo['zlpname'];
			$value['uid'] = $zydInfo['zlpid'];
			$value['status'] = 1001;
			return $value;
		}
		$gInfo = cityModel::getGeneralData($playersid,false,$gid);
		if (empty($gInfo)) {
			return array('status'=>21,'message'=>$sys_lang[3]);
		}
		$value['gid'] = $gid; 
		if ($playersid == $zydInfo['playersid']) {
			$needTime = 300;    //需要花费时间
		} else {
			$needTime = 1800;   //需要花费时间
		}	
		/*$insert['wfpid'] = $playersid;
		$insert['wfmc'] = $roleInfo['nickname'];
		$insert['wfgid'] = $gid;
		$insert['wfgname'] = $gInfo[0]['general_name'];
		$insert['zyid'] = $zydid;
		$insert['ddsj'] = $nowTime + $needTime;
		$insert['jqlx'] = 3;	
		$insert['dfpid'] = $zydInfo['zlpid'];
		$insert['dfmc'] = $zydInfo['zlpname'];
		$insert['createTime'] = $nowTime;
		$insert['xhsj'] = $needTime;
		$jqid = $common->inserttable('jq',$insert);	*/
		$mc->set(MC.$playersid.'_jq',1,0,1800); //设置军情状态	
		$mc->delete(MC.$playersid."_ydxx");
		$mc->delete(MC.$playersid."_zlydxx");	
		$mc->delete(MC.$pid."_ydxx");
		$mc->delete(MC.$pid."_zlydxx");				
		$jjsj = $nowTime - $zydInfo['scsysj'];
		$ddsj = $nowTime + $needTime;
		$updateJq = "UPDATE ".$common->tname('jq')." SET jqlx = '4',xhsj = '$needTime',ddsj = '$ddsj',createTime = '$nowTime',dfpid = 0,dfmc = '',jlsj = '' WHERE wfgid = '$gid' && zyid = '$zydid' LIMIT 1";
		$db->query($updateJq); 
		$updateGen = "UPDATE ".$common->tname('playergeneral')." SET gohomeTime = '$ddsj',act = '4' WHERE intID = '$gid' LIMIT 1";
		$db->query($updateGen);  	
		$updateZyd = "UPDATE ".$common->tname('zyd')." SET zlpid = 0,zlpname='',zlwjid=0,zlwjmc='',scsysj=0,zlsj=0,wjmc='',ldsl=0,ldzyid= 0,ldbhcs=0 WHERE yd_id = '$zydid'";
		$db->query($updateZyd);
		$updatewfJq = "UPDATE ".$common->tname('jq')." SET dfpid=0,dfmc='' WHERE dfpid = '$playersid' && zyid = '$zydid'";
		$db->query($updatewfJq); 		
		$gInfo[0]['gohomeTime'] = $ddsj;
		$gInfo[0]['act'] = 4;
		//$gInfo[0]['jqid'] = $jqid;
		//$gInfo[0]['zydid'] = $zydid;		
		$newValue[$gInfo[0]['sortid']] = $gInfo[0];
		$common->updateMemCache(MC.$playersid.'_general',$newValue);	 
		//发布探报
		$msg['xllx'] = 44;
		$msg['X'] = $gInfo[0]['general_name'];
		//$msg['Y'] = $zydInfo['nickname'];	
		$msg['L'] = $zydInfo['yd_level'];	
		$msg['R'] = zydmc($zydInfo['yd_type']);					
		//  $mesageDen = addcslashes(json_encode($msg),'\\');				
		$mesageDen = $msg;
        $messageInfoDen = array('playersid'=>0,'jsid'=>$roleInfo['playersid'],'toplayersid'=>$playersid,'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
		$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
		$messageInfoDen['genre'] = 44;
		lettersModel::addMessage($messageInfoDen);		
		$messageInfoDen = null;
		$mesageDen = null;
		$msg = null;
		
		$sql = "SELECT * FROM ".$common->tname('jq')." WHERE zyid = '$zydid' && jqlx = '3'";
		$result = $db->query($sql);
		while ($rows = $db->fetch_array($result)) {
			$ql_flag = false;
			if(($is_ql = $mc->get(MC.$rows['wfpid'].'_'.$zydid.'_attack100'))) {// 判断是否是强掠
				$ql_flag = true;
				$mc->delete(MC.$rows['wfpid'].'_'.$zydid.'_attack100');
			}
			
			//发放探报
			if($ql_flag) {
				$msg['xllx'] = 67;
			} else {
				$msg['xllx'] = 41;
			}
			$msg['X'] = $rows['wfgname'];
			//$msg['Y'] = $zydInfo['nickname'];	
			$msg['L'] = $zydInfo['yd_level'];	
			$msg['R'] = zydmc($zydInfo['yd_type']);								
			//$mesageDen = addcslashes(json_encode($msg),'\\');				
			$mesageDen = $msg;
			$messageInfoDen = array('playersid'=>0,'jsid'=>$rows['wfpid'],'toplayersid'=>$rows['wfpid'],'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
			$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
			if($ql_flag) {
				$messageInfoDen['genre'] = 67;
			} else {
				$messageInfoDen['genre'] = 41;
			}
			lettersModel::addMessage($messageInfoDen);		
			$messageInfoDen = null;
			$mesageDen = null;
			$msg = null;	
			//更新回城状态		
			$sysj = $rows['ddsj'] - $nowTime;
			if ($sysj < 0) {
				$sysj = 0;
			}
			$fhsj = $nowTime + ($rows['xhsj'] - $sysj);
			$updateGen = "UPDATE ".$common->tname('playergeneral')." SET gohomeTime = '$fhsj',act='4',zydid='0' WHERE intID = '".$rows['wfgid']."' LIMIT 1";
			$db->query($updateGen);  
			$mc->delete(MC.$rows['wfpid'].'_general');	
			$updateSql = "UPDATE ".$common->tname('jq')." SET ddsj = '$fhsj',dfpid = 0,dfmc = '',jqlx = '4',createTime='$nowTime' WHERE jq_id = '".$rows['jq_id']."' LIMIT 1";
			$db->query($updateSql);
			$mc->set(MC.$rows['wfpid'].'_jq',1,0,1800); //设置军情状态									
		}  
		$value['status'] = 0;
		return $value; 										
	}
	
	//资源点结算
	public static function zydjs() {
		global $mc, $db, $common, $_SGLOBAL,$G_PlayerMgr;
		$nowTime = $_SGLOBAL['timestamp'];
		$maxTime = $_SGLOBAL['timestamp'] + 1800;
		$sql = "SELECT * FROM ".$common->tname('jq')." WHERE (ddsj <= $nowTime || ddsj > $maxTime) && jqlx != 1 && jqlx != 5 && jqlx != 6";
		$result = $db->query($sql);
		while ($rows = $db->fetch_array($result)) {
			$G_PlayerMgr->removeplayer($rows['wfpid']);
			if ($rows['ddsj'] > $maxTime) {
				$db->query("update ".$common->tname('jq')." set ddsj = $maxTime where jq_id = ".$rows['jq_id']);
				continue;
			}
			if ($rows['jqlx'] == 2) {
				//进行占领相关操作
			    zydModel::zxzl($rows['wfpid'],$rows['zyid'],$rows['wfgid'],$rows['zydowner'],$rows['jq_id'],$rows['xhsj'],$rows);
			} elseif ($rows['jqlx'] == 3) {
				//进行掠夺相关操作
				zydModel::zxld($rows['wfpid'],$rows['zyid'],$rows['wfgid'],$rows['zydowner'],$rows['jq_id'],$rows['xhsj'],$rows);
			} elseif ($rows['jqlx'] == 5) {				
				$db->query("DELETE FROM ".$common->tname('jq')." WHERE ($nowTime - createTime) > 86400 && jqlx = 5");
			} else {
				//进行召回相关操作
				zydModel::hccz($rows['wfpid'],$rows['zyid'],$rows['wfgid'],$rows['zydowner'],$rows['jq_id'],$rows['xhsj'],$rows);
			}
			$G_PlayerMgr->removeplayer($rows['wfpid']);
		}
	}
	
	//回城操作
	public static function hccz($playersid,$zydid,$gid,$zypid,$jq_id,$xhsj,$jqInfo) {
		global $mc, $db, $common, $_SGLOBAL, $zyd_lang;
		$nowTime = $_SGLOBAL['timestamp'];
		$updatewj = "UPDATE ".$common->tname('playergeneral')." SET gohomeTime = '0',jqid = '0',zydid=0,act=0 WHERE intID = '".$jqInfo['wfgid']."' LIMIT 1";
		$db->query($updatewj);
		$mc->delete(MC.$jqInfo['wfpid'].'_general');
		$sql = "SELECT * FROM ".$common->tname('jq')." WHERE jq_id = '".$jqInfo['jq_id']."' LIMIT 1";
		$result = $db->query($sql);
		$rows = $db->fetch_array($result);
		if (empty($rows)) {
			return false;
		} else {
			if ($rows['jlsj'] != '') {
				$amount = 0;
				$roleInfo['playersid'] = $rows['wfpid'];
				roleModel::getRoleInfo($roleInfo,false);
				$jlsj = json_decode($rows['jlsj'],true);
				zydModel::jlzy($roleInfo,$jlsj);
				if (isset($jlsj['tq'])) {
					$jlmc = $zyd_lang['scyd_1'];
					$amount = $jlsj['tq'];
				} elseif (isset($jlsj['jl'])) {
					$jlmc = $zyd_lang['scyd_2'];
					$amount = $jlsj['jl'];
				} else {
					$jlInfo = $jlsj['dj'];
					foreach ($jlInfo as $key => $amount) {
						$toolsInfo = toolsModel::getItemInfo($key);
						$jlmc = $toolsInfo['Name'];
						if ($amount == 0) {
							$jlsj = '';
						}
					}
				}
			} else {
				$jlsj = '';
			}
			if (empty($jlsj) || $amount <= 0) {
				$msg['xllx'] = 54;
			} else {
				$msg['xllx'] = 53;
				$msg['B'] = $jlmc.' X '.$amount;
			}
			if ($rows['jqlx'] == 7) {
				$zydInfo = zydModel::hqzlzyd($rows['zyid']);
				$msg['L'] = $zydInfo['dj'];
			} else {
				$zydInfo = zydModel::hqdzydxx($rows['zydowner'],$rows['zyid']);		
				$msg['L'] = $zydInfo['yd_level'];
			}	
			$msg['X'] = $rows['wfgname'];
			//$msg['Y'] = $zydInfo['nickname'];					
			$msg['R'] = zydmc($zydInfo['yd_type']);					
			//$mesageDen = addcslashes(json_encode($msg),'\\');				
			$mesageDen = $msg;
			$messageInfoDen = array('playersid'=>0,'jsid'=>$playersid,'toplayersid'=>$playersid,'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
			//$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
			$messageInfoDen['genre'] = $msg['xllx'];
			lettersModel::addMessage($messageInfoDen);		
			$messageInfoDen = null;
			$mesageDen = null;
			$msg = null;			
		}
		//发送回城到达战报
		$sql = "DELETE FROM ".$common->tname('jq')." WHERE jq_id = '".$jqInfo['jq_id']."'";
		$db->query($sql);
		$sql = null;		
	}
	
	//支付奖励产出资源数据更新
	public static function jlzy($roleInfo,$jlsj) {
		global $mc, $db, $common, $_SGLOBAL,$G_PlayerMgr,$zyd_lang;
		cityModel::resourceGrowth($roleInfo); //结算军粮
		$playersid = $roleInfo['playersid'];
		$player = $G_PlayerMgr->GetPlayer($playersid);
		if (!is_array($jlsj)) {
			$jlsj = json_decode($jlsj,true);
		}
		/*结算获取的资源*/
		if (isset($jlsj['tq'])) {
			$ldzyAmount = floor($jlsj['tq']); //该次掠夺资源数量
			if ($ldzyAmount > 0) {
				$updateRole['coins'] = $roleInfo['coins'] + $ldzyAmount;
				$common->updatetable('player',$updateRole,"playersid = '$playersid'");
				$common->updateMemCache(MC.$playersid,$updateRole);
			}
		} elseif (isset($jlsj['jl'])) {					
			$ldzyAmount = floor($jlsj['jl']); //该次掠夺资源数量
			if ($ldzyAmount > 0) {
				$updateRole['food'] = $roleInfo['food'] + $ldzyAmount;
				$updateRole['last_update_food'] = $_SGLOBAL['timestamp'];
				$common->updatetable('player',$updateRole,"playersid = '$playersid'");
				$common->updateMemCache(MC.$playersid,$updateRole);
			}				
		} else {
			if ($jlsj != 0) {
				$stat = $player->AddItems($jlsj['dj']);
				if ($stat === false) {  //背包已满
					$fsdjxx = array();
					foreach ($jlsj['dj'] as $key => $zyzsInfovalue) {
						$ldzyAmount = floor($zyzsInfovalue); //该次掠夺资源数量						
						if ($ldzyAmount > 0) {
							//把道具以消息的方式发送;
							$toolsInfo = toolsModel::getItemInfo($key);
							$djmc = $toolsInfo['Name'];
							$djsl = $ldzyAmount;
							$djkey = $key;
							$fsdjxx[] = array('id'=>$djkey,'mc'=>$djmc,'num'=>$djsl);
							$djmc = null;	
							$djsl = null;
							$djkey = null;							
						}
					}
					if (!empty($fsdjxx)) {
						$message = array('playersid'=>$roleInfo['playersid'],'toplayersid'=>$roleInfo['playersid'],'subject'=>$zyd_lang['jlzy_1'],'genre'=>20,'tradeid'=>0,'interaction'=>0,'is_passive'=>0,'type'=>1,'request'=>addcslashes(json_encode(array('tq'=>0,'yp'=>0,'yb'=>0,'jl'=>0,'items'=>$fsdjxx)), '\\'),'message'=>array('xjnr'=>$zyd_lang['zyzsy']));
						lettersModel::addMessage($message);	
					}											
				} else {
					$mc->set(MC.$roleInfo['playersid'].'_xsbb',1,0,3600);
				}
			}			
		}		
		/*结算结束*/				
	}
	
	//执行占领操作
	public static function zxzl($playersid,$zydid,$gid,$zypid,$jq_id,$xhsj,$jqInfo) {
		global $mc, $db, $common, $_SGLOBAL,$G_PlayerMgr, $zyd_lang;
		$nowTime = $jqInfo['ddsj'];
		$zydInfo = zydModel::hqdzydxx($zypid,$zydid);
		if (!empty($zydInfo['status'])) {
			return false;
		} else {
			if ($playersid != $zydInfo['playersid']) {
				socialModel::addEnemy($playersid,$zydInfo['playersid']); //加仇人
			}
			$player = $G_PlayerMgr->GetPlayer($playersid);
			if ($zydInfo['zlpid'] != 0) {
				$G_PlayerMgr->removeplayer($zydInfo['zlpid']);
				$defendplayer = $G_PlayerMgr->GetPlayer($zydInfo['zlpid']);
			}
			$mc->set(MC.$playersid.'_jq',1,0,1800);
			//如遇到自己打自己则直接返回
			if ($playersid == $zydInfo['zlpid']) {
				$hcsj = $nowTime + $jqInfo['xhsj'];			
				$updatehcSql = "UPDATE ".$common->tname('jq')." SET ddsj = '$hcsj',dfpid = 0,dfmc = '',jqlx = '4',createTime = '$nowTime',jlsj = '' WHERE jq_id = '$jq_id' LIMIT 1";
				$db->query($updatehcSql);	
				$updatehcjlSql = "UPDATE ".$common->tname('playergeneral')." SET zydid = '0',act = '4',gohomeTime = '$hcsj' WHERE intID = '$gid' LIMIT 1";
				$db->query($updatehcjlSql);
				$mc->delete(MC.$playersid.'_general');
				$msg['xllx'] = 50;
				$msg['X'] = $jqInfo['wfgname'];
				$msg['L'] = $zydInfo['yd_level'];	
				$msg['R'] = zydmc($zydInfo['yd_type']);					
				//$mesageDen = addcslashes(json_encode($msg),'\\');				
				$mesageDen = $msg;
				$messageInfoDen = array('playersid'=>0,'jsid'=>$playersid,'toplayersid'=>$playersid,'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
				//$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
				$messageInfoDen['genre'] = 50;
				lettersModel::addMessage($messageInfoDen);		
				$messageInfoDen = null;
				$mesageDen = null;
				$msg = null;										
				return false;
			}
			$zlwjid = $zydInfo['zlwjid'];
			if ($zlwjid == 0) {
				$npcInfo = ydsj($zydInfo['yd_level'],$zydInfo['yd_type']);
				$defend_data = $dgeneral = array(array(
					'intID'=>1,
					'mobility'=>actModel::getMobility($npcInfo['bz'],$npcInfo['dj']),
					'general_sort'=>1,
					'professional'=>$npcInfo['bz'],
					'physical_value'=>$npcInfo['tl'],
					'attack_value'=>$npcInfo['gj'],
					'defense_value'=>$npcInfo['fy'],
					'agility_value'=>$npcInfo['mj'],
					'understanding_value'=>10,
					'general_level'=>$npcInfo['dj'],
					'general_name'=>$npcInfo['xm'],
					'avatar'=>$npcInfo['avatar']
				));	
				$value['d_level'] = $npcInfo['dj'];
				$value['d_sex'] = $npcInfo['sex'];
				$value['fsxm'] = $npcInfo['xm'];	
				$sf_jwdj = 0;	
				$isGw = 1;	
				//CEILING(POWER(驻守野地怪物等级,1.6)*0.36+驻守野地怪物等级*2+19,1)
				$incomeExp = ceil(pow($npcInfo['dj'],1.6) * 0.36 + $npcInfo['dj'] * 2 + 19);
				$defendplayer = '';
				$dzms = 0;
				$defendRoleInfo = NULL;
				//print_r($defend_data);
				//exit;					
			} else {
				$isGw = 0;	
				$dzms = 1;			
				$dgeneral = cityModel::getGeneralData($zydInfo['zlpid'],false,$zydInfo['zlwjid'],false);
				//print_r($dgeneral);
				//exit;
				if (empty($dgeneral)) {
					return false;
				}				
				$dvalue = array();			
				$dgeneral[0]['general_sort'] = 1;							
				$dvalue[] = $dgeneral[0];						
				$defend_data = $dvalue;	
				$defendRoleInfo['playersid'] = $zydInfo['zlpid'];
				$res1 = roleModel::getRoleInfo($defendRoleInfo,false);
				if (empty($res1)) {
					return false;
				}		
				$sf_jwdj = $defendRoleInfo['mg_level'];
				$value['d_level'] = $defendRoleInfo['player_level'];
				$incomeExp = ceil(pow($value['d_level'],1.6) * 0.36 + $value['d_level'] * 2 + 19);
				$value['d_sex'] = $defendRoleInfo['sex'];
				$value['fsxm'] = stripcslashes($defendRoleInfo['nickname']);			
				//评分	
				$dfpfArr = fightModel::getPf($defend_data, $defendRoleInfo);
				$value['gkpf'] = $dfpfArr['wjzdlpf'];
				$value['djpfzb'] = $dfpfArr['djpf'];
				$value['tfpfzb'] = $dfpfArr['tfpf'];
				$value['zbpfzb'] = $dfpfArr['zbpf'];
				$value['jwpfzb'] = $dfpfArr['jwpf'];
				$value['jnpfzb'] = $dfpfArr['jnpf'];
				$value['tgsxpf'] = $dfpfArr['wjzdlpf'];												
			}
			$generalAll = cityModel::getGeneralData($playersid,false,'*',false);
			$zlzyd = array();
			foreach ($generalAll as $generalAllValue) {
				if ($generalAllValue['intID'] == $gid) {
					$general[] = $generalAllValue;
					//break;
				}
				/*if ($generalAllValue['act'] == 1) {
					$zlzyd[] = 1;
				}*/
			}
			$sqlldsl = "select count(jq_id) as ldsl FROM ".$common->tname('jq')." WHERE wfpid = $playersid && jqlx = 1";
			$res_ldsl = $db->query($sqlldsl);
			$rows_ldsl = $db->fetch_array($res_ldsl);
			$dqzydsl = $rows_ldsl['ldsl'];
			//$general = cityModel::getGeneralData($playersid,false,$gid,false);
			if (empty($general)) {
				$delJqSql = "DELETE FROM ".$common->tname('jq')." WHERE jq_id = '$jq_id' LIMIT 1";
				$db->query($delJqSql);		
				return false;						
			}
    		$avalue = array();
			if ($general[0]['general_life'] > 0) {
				$general[0]['general_sort'] = 1;							
				$general[0]['command_soldier'] = $general[0]['general_life'];	
				$avalue[] = $general[0];
			} else {
				return false; 
			}
			$attack_data = $avalue;					
			$roleInfo['playersid'] = $playersid;
			$res2 = roleModel::getRoleInfo($roleInfo,false);
			if (empty($res2)) {
				return false;
			}	
			$hqzlz = hqzlz($roleInfo,$defendRoleInfo,$general,$dgeneral,$dzms,$player,$defendplayer);  //理论获取战功值 
			$kczlz = ceil($hqzlz / 4);  //理论扣除战功值	
						
			$value['a_level'] = $roleInfo['player_level'];
			$value['a_sex'] = $roleInfo['sex'];
			$value['wjxm'] = stripcslashes($roleInfo['nickname']);					
			$pfArr = fightModel::getPf($attack_data, $roleInfo);
			$value['wjzdlpf'] = $pfArr['wjzdlpf'];
			$value['djpf'] = $pfArr['djpf'];
			$value['tfpf'] = $pfArr['tfpf'];
			$value['zbpf'] = $pfArr['zbpf'];
			$value['jwpf'] = $pfArr['jwpf'];
			$value['jnpf'] = $pfArr['jnpf'];
			$gf_jwdj = $roleInfo['mg_level'];
			//print_r($attack_data);
			$result = actModel::fight($attack_data,$defend_data,$isGw,'','',0,$gf_jwdj,$sf_jwdj);                               //战斗返回
			$value['begin'] = $result['begin'];                                               //开始士兵情形
			$round = $result['round'];                                               //战斗回合	
			$newround = heroCommon::arr_foreach($round);
			$value['round'] = $newround;
			$round = null;	
			$newround = null;				
			//print_r($result);
			$soldierNum = 0;                                                                      //初始化防守士兵数量
			$attack_result = $result['result'];
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
				}
			}
			$attack_soldier_left = array_sum($attack_soldier);   //攻击方所剩人数	
			//更新攻击方将领数据	   							
			if ($attack_soldier_left <= 0) {
				$G_PlayerMgr->removeplayer($zydInfo['zlpid']);
				$sjkczgz = 0;
				if ($zypid == $playersid) {
					$zglx = 6;
					$zgpname = mysql_escape_string($player->baseinfo_['nickname']);
				} else {
					$zglx = 4;
					$zgpname = mysql_escape_string($zydInfo['nickname']);
				}				
				$xzg = rankModel::decreaseZg($playersid,$kczlz,$sjkczgz,$zglx,mysql_escape_string($zgpname),$zypid);					
		   		$ddsj = $nowTime + $xhsj;
				$updateSql = "UPDATE ".$common->tname('jq')." SET ddsj = '$ddsj',dfpid = 0,dfmc = '',jqlx = '4',createTime='$nowTime' WHERE jq_id = '$jq_id' LIMIT 1";				
				$db->query($updateSql);
				$act = 4;
				$jqid = $jq_id;					
				for ($k = 0; $k < 1; $k++) {
					$id = $attack_data[$k]['intID'];
					$left_command_soldier = $attackGeneralLeftSoldier[$id];                             //剩余士兵数量
					$sssm = $attack_data[$k]['general_life'];                                           //武将生命
					$wjmc = $attack_data[$k]['general_name'];                                           //武将名称
					$updateGeneral['general_life'] = $left_command_soldier;    
					$attack_data[$k]['general_life'] = $left_command_soldier;
					$attack_data[$k]['command_soldier'] = $left_command_soldier;	
					$attack_data[$k]['gohomeTime'] = $updateGeneral['gohomeTime'] = $ddsj;	
					$attack_data[$k]['act']= $updateGeneral['act'] = $act;		
					$attack_data[$k]['jqid']= $updateGeneral['jqid'] = $jqid;			
					$attack_data[$k]['zydid']= $updateGeneral['zydid'] = $zydid;			
					//$whereGeneral['intID'] = $id;
					//$common->updatetable('playergeneral',$updateGeneral,$whereGeneral);               //更新将领数据			  				
					//$newData[$attack_data[$k]['sortid']] = $attack_data[$k];     	     		     
					//$common->updateMemCache(MC.$playersid.'_general',$newData);   
					//$mc->delete(MC.$playersid.'_general');
					//自动补血
					zydModel::ksbx($roleInfo,$attack_data[$k],$id,$updateGeneral,$xzg);
	   			}
				$msg['xllx'] = 35;
				/*if ($isGw == 1) {
					$msg['wjmc1'] = $zydInfo['nickname'];
					//$msg['wjid1'] = $zydInfo['playersid'];
				} else {
					$msg['wjmc1'] = $zydInfo['zlpname'];
					//$msg['wjid1'] = $zydInfo['zlpid'];					
				}
				$msg['wjid1'] = $playersid; 
				$msg['wjwjmc1'] = $general[0]['general_name'];
				$msg['wjwjid1'] = $gid;
				$msg['zydmc'] = zydmc($zydInfo['yd_type']);*/
				$msg['X'] = $general[0]['general_name'];				
				//$msg['Y'] = $zydInfo['nickname'];
				$msg['L'] = $zydInfo['yd_level'];
				$msg['R'] = zydmc($zydInfo['yd_type']);	
				$msg['G'] = $sjkczgz;							
	            //$mesageDen = addcslashes(json_encode($msg),'\\');				
				$mesageDen = $msg;
	            $messageInfoDen = array('playersid'=>0,'jsid'=>$roleInfo['playersid'],'toplayersid'=>$playersid,'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
				$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
				$messageInfoDen['genre'] = 35;
				lettersModel::addMessage($messageInfoDen);	
				$messageInfoDen = null;
				$mesageDen = null;
				$msg = null;		
				if ($isGw == 0) {
					$msg['xllx'] = 37;
					/*$msg['wjmc1'] = $roleInfo['nickname'];
					$msg['wjid1'] = $roleInfo['playersid'];
					$msg['wjwjmc1'] = $general[0]['general_name'];
					$msg['wjwjid1'] = $gid;
					$msg['zydmc'] = zydmc($zydInfo['yd_type']);	*/
					$msg['X'] = $zydInfo['zlwjmc'];
					//$msg['Y'] = $zydInfo['nickname'];
					$msg['L'] = $zydInfo['yd_level'];
					$msg['R'] = zydmc($zydInfo['yd_type']);	
					$msg['Z'] = $roleInfo['nickname'];	
					$msg['ZID'] = $roleInfo['playersid'];	
					$msg['A'] = $attack_data[0]['general_name'];
					$msg['AID'] = $gid;											
		            //$mesageDen = addcslashes(json_encode($msg),'\\');				
					$mesageDen = $msg;
		            $messageInfoDen = array('playersid'=>0,'jsid'=>$roleInfo['playersid'],'toplayersid'=>$zydInfo['zlpid'],'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
					$messageInfoDen['genre'] = 37;
		            $messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
					lettersModel::addMessage($messageInfoDen);		
					//heroCommon::insertLog('111111+'.$zydInfo['playersid']);		
				}/* else {
					if ($roleInfo['playersid'] != $zydInfo['playersid']) {
						$msg['xllx'] = 37;
						$msg['wjmc1'] = $roleInfo['nickname'];
						$msg['wjid1'] = $roleInfo['playersid'];
						$msg['wjwjmc1'] = $general[0]['general_name'];
						$msg['wjwjid1'] = $gid;
						$msg['zydmc'] = zydmc($zydInfo['yd_type']);					
			            $mesageDen = addcslashes(json_encode($msg),'\\');				
			            $messageInfoDen = array('playersid'=>0,'jsid'=>$roleInfo['playersid'],'toplayersid'=>$zydInfo['playersid'],'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
						$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
						$messageInfoDen['genre'] = 37;
						lettersModel::addMessage($messageInfoDen);	
					}
					//heroCommon::insertLog('222222+'.$zydInfo['zlpid']);						
				}*/				   							
				return 2;					       
			} else {
				$sjzgz = 0;
				if ($zypid == $playersid) {
					$zglx = 5;
					$zgpname = mysql_escape_string($player->baseinfo_['nickname']);
				} else {
					$zglx = 3;
					$zgpname = mysql_escape_string($zydInfo['nickname']);
				}
				$xzg = rankModel::addZg($playersid,$hqzlz,$sjzgz,$zglx,mysql_escape_string($zgpname),$zypid);
				$mc->delete(MC.$playersid."_ydxx");
				$mc->delete(MC.$playersid."_zlydxx");	
				$mc->delete(MC.$zydInfo['zlpid']."_ydxx");
				$mc->delete(MC.$zydInfo['zlpid']."_zlydxx");	
				$mc->delete(MC.$zydInfo['playersid']."_ydxx");
				$mc->delete(MC.$zydInfo['playersid']."_zlydxx");								
				/*if ($isGw == 0) {
					$msg['xllx'] = 36;
					$msg['X'] = $zydInfo['zlwjmc'];
					//$msg['Y'] = $zydInfo['nickname'];
					$msg['L'] = $zydInfo['yd_level'];
					$msg['R'] = zydmc($zydInfo['yd_type']);	
					$msg['Z'] = $roleInfo['nickname'];	
					$msg['ZID'] = $roleInfo['playersid'];	
					$msg['A'] = $attack_data[0]['general_name'];
					$msg['AID'] = $gid;									
		            $mesageDen = addcslashes(json_encode($msg),'\\');				
		            $messageInfoDen = array('playersid'=>0,'jsid'=>$roleInfo['playersid'],'toplayersid'=>$zydInfo['zlpid'],'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
					$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
					$messageInfoDen['genre'] = 36;
					lettersModel::addMessage($messageInfoDen);				
				} *//*else {					
					$msg['xllx'] = 36;
					$msg['X'] = $zydInfo['zlwjmc'];
					$msg['Y'] = $zydInfo['nickname'];
					$msg['L'] = $zydInfo['yd_level'];
					$msg['R'] = zydmc($zydInfo['yd_type']);	
					$msg['Z'] = $roleInfo['nickname'];	
					$msg['ZID'] = $roleInfo['playersid'];	
					$msg['A'] = $attack_data[0]['general_name'];
					$msg['AID'] = $gid;							
		            $mesageDen = addcslashes(json_encode($msg),'\\');				
		            $messageInfoDen = array('playersid'=>0,'jsid'=>$roleInfo['playersid'],'toplayersid'=>$zydInfo['playersid'],'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
					$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
					$messageInfoDen['genre'] = 36;
					lettersModel::addMessage($messageInfoDen);						
				}*/
				//武将升级
				$tf = $attack_data[0]['understanding_value'] - $attack_data[0]['llcs']; //武将天赋
				$jbsx = wjsjsx($tf);  //武将级别上限		
				$current_general_level = $attack_data[0]['general_level'];		
				$needJy = cityModel::getGeneralUpgradeExp($current_general_level);                //下级所需经验
				$current_experience = $attack_data[0]['current_experience'];				
				//$incomeExp = 100;
				$hqjy = 0;
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
								$attack_data[0]['current_experience'] = $updateGeneral['current_experience'] = $left_experience = $newExp;
								$hqjy = $g_hqjy;
							} else {
								$attack_data[0]['current_experience'] = $updateGeneral['current_experience'] = $left_experience = $needJy;
								$hqjy = $newExp - $needJy;
							}							
						} else {
							$left_experience = $current_experience;
							$hqjy = 0;
						}				 	 	
					 } else {				 	 	
						$generalUpgrade = fightModel::upgradeRole($current_general_level,$newExp,2);
						$left_experience = $generalUpgrade['left'];
						$new_level = $generalUpgrade['level'];
						if ($new_level > $upLevel) {
							$new_level = $upLevel;
							$left_experience = 0;
						}
						$attack_data[0]['current_experience'] = $updateGeneral['current_experience'] = $left_experience;	
						$upgrade = 0;
						if (intval($new_level) > intval($current_general_level) && intval($current_general_level) > 0) {
							$attack_data[0]['general_level'] = $updateGeneral['general_level'] = $new_level;
						}
						$xhjy = array();
						for ($k = 0; $k < ($new_level - $current_general_level); $k++) {
							$xhjy[] = cityModel::getGeneralUpgradeExp($current_general_level + $k);
						}	
						if (!empty($xhjy)) {
							$totalExp =  array_sum($xhjy);        //所需要的总经验
						    $hqjy = $totalExp - $current_experience + $left_experience;
						} else {
							$hqjy = $g_hqjy;
						}									 	 			 	 	
					 }
					 unset($generalUpgrade);	//清空数值	
					 $current_general_level = '';
					 $get_experience = ''; 
					 $current_experience = '';		                                       		                 	
				} else {
					$hqjy = 0;
				}
				$allowzydsl = $roleInfo['ld_level'];
				//$dqzydsl = count($zlzyd);
				if ($dqzydsl >= $allowzydsl) {
					$msg['xllx'] = 58;
					$updateact = 4;
					$updateGeneral['gohomeTime'] = $jqInfo['ddsj'] + $jqInfo['xhsj'];
					$roleInfo['rwsl'] = $dqzydsl; 
				} else {
					$msg['xllx'] = 34;
					$updateact = 1;
					$roleInfo['rwsl'] = $dqzydsl + 1;
				}
				//questsController::OnFinish ( $roleInfo, "'zyd'" );				
				$msg['X'] = $general[0]['general_name'];
				//$msg['Y'] = $zydInfo['nickname'];
				$msg['L'] = $zydInfo['yd_level'];
				$msg['R'] = zydmc($zydInfo['yd_type']);
				$msg['E'] = $hqjy.$zyd_lang['zxzl_1'];
				$msg['G'] = $sjzgz;
				/*if ($isGw == 1) {
					$msg['wjmc1'] = $zydInfo['nickname'];
				} else {
					$msg['wjmc1'] = $zydInfo['zlpname'];
				}				
				$msg['wjid1'] = $playersid;
				$msg['wjwjmc1'] = $general[0]['general_name'];
				$msg['wjwjid1'] = $gid;
				$msg['zydmc'] = zydmc($zydInfo['yd_type']);	*/			
	            //$mesageDen = addcslashes(json_encode($msg),'\\');				
				$mesageDen = $msg;
	            $messageInfoDen = array('playersid'=>0,'jsid'=>$roleInfo['playersid'],'toplayersid'=>$playersid,'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
				$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
				$messageInfoDen['genre'] = $msg['xllx'];
				lettersModel::addMessage($messageInfoDen);		
				$messageInfoDen = null;
				$mesageDen = null;
				$msg = null;		
				//武将升级结束				
				for ($k = 0; $k < 1; $k++) {
					$id = $attack_data[$k]['intID'];
					$left_command_soldier = $attackGeneralLeftSoldier[$id];                             //剩余士兵数量
					$sssm = $attack_data[$k]['general_life'];                                           //武将生命
					$wjmc = $attack_data[$k]['general_name'];                                           //武将名称
					$updateGeneral['general_life'] = $left_command_soldier;    
					$attack_data[$k]['general_life'] = $left_command_soldier;
					$attack_data[$k]['command_soldier'] = $left_command_soldier;	
					$attack_data[$k]['act']= $updateGeneral['act'] = $updateact;		
					//$whereGeneral['intID'] = $id;
					//$common->updatetable('playergeneral',$updateGeneral,$whereGeneral);               //更新将领数据			  				
					//$newData[$attack_data[$k]['sortid']] = $attack_data[$k];     	     		     
					//$common->updateMemCache(MC.$playersid.'_general',$newData);   
					//$mc->delete(MC.$playersid.'_general');
					//自动补血
					zydModel::ksbx($roleInfo,$attack_data[$k],$id,$updateGeneral,$xzg);
	   			}	   						
				if ($zlwjid != 0) {
					$sjkczgz = 0;					
					$dxzgz = rankModel::decreaseZg($zydInfo['zlpid'],$kczlz,$sjkczgz,10,mysql_escape_string($player->baseinfo_['nickname']),$playersid);
					$zydRoleInfo['playersid'] = $zydInfo['zlpid'];
					$reszyd = roleModel::getRoleInfo($zydRoleInfo,false);					
					if (empty($reszyd)) {
						return false;
					}	
					if ($sjkczgz != 0) {
						$common->updatetable('player',$dxzgz,"playersid = ".$zydInfo['zlpid']);
						$common->updateMemCache(MC.$zydInfo['zlpid'],$dxzgz);
						$G_PlayerMgr->removeplayer($zydInfo['zlpid']);
					}				
					/*结算获取的资源*/
					$jksj = $nowTime - $zydInfo['scsysj'];  //距离上次收益间隔时间
					if ($jksj >= 3600) {
						$jlsj = array();
						$zyzsInfo = zydzy($zydInfo['yd_type'],$zydInfo['yd_level'],$jksj,$zydInfo['ldzyid']);
						if (isset($zyzsInfo['tq'])) {
							$ldzyAmount = floor(($zyzsInfo['tq'] - $zydInfo['ldsl']) / 2); //该次掠夺资源数量
							if ($ldzyAmount > 0) {
								//$updateRole['coins'] = $roleInfo['coins'] + $ldzyAmount;
								//$common->updatetable('player',$updateRole,"playersid = '".$zydInfo['zlpid']."'");
								//$common->updateMemCache(MC.$zydInfo['zlpid'],$updateRole);
								$jlsj = array('tq'=>$ldzyAmount);
								$jlmc = $zyd_lang['scyd_1'];
							}
						} elseif (isset($zyzsInfo['jl'])) {					
							$ldzyAmount = floor(($zyzsInfo['jl'] - $zydInfo['ldsl']) / 2); //该次掠夺资源数量
							if ($ldzyAmount > 0) {
								//$updateRole['food'] = $roleInfo['food'] + $ldzyAmount;
								//$common->updatetable('player',$updateRole,"playersid = '".$zydInfo['zlpid']."'");
								//$common->updateMemCache(MC.$zydInfo['zlpid'],$updateRole);
								$jlsj = array('jl'=>$ldzyAmount);
								$jlmc = $zyd_lang['scyd_2'];
							}					
						} else {
							if ($zyzsInfo != 0) {
								foreach ($zyzsInfo['dj'] as $key => $zyzsInfovalue) {
									$ldzyAmount = floor(($zyzsInfovalue - $zydInfo['ldsl']) / 2); //该次掠夺资源数量
									if ($key == 20009 && $ldzyAmount > 4) {  //当金刚石碎片大于4时，有50%几率转换为玄铁碎片，数量为金刚石产量1/4
										if (rand(1,2) == 1) {
											$key = 20127;
											$ldzyAmount = round($ldzyAmount / 4 ,0);
										}
									}
									/*if ($ldzyAmount > 0) {
										$toolsRes = toolsModel::addPlayersItem($zydRoleInfo,$key,$ldzyAmount);	
										if ($toolsRes['status'] == 0) {
											$mc->set(MC.$zydRoleInfo['playersid'].'_xsbb',1,0,900);		
										}		
									}*/
									$djInfo = toolsModel::getItemInfo($key);
									$jlsj = array('dj'=>array($key => $ldzyAmount));	
									$jlmc = $djInfo['Name'];
								}
							}				
						}
						if (!empty($jlsj)) {
							$jlsj = json_encode($jlsj);
						} else {
							$jlsj = '';
						}
					} else {
						$jlsj = '';
					}		
					/*结算结束*/					
				} else {
					$jlsj = '';
				}	
				if (!empty($jlsj)) {
					$hqtq = isset($jlsj['tq'])?$jlsj['tq']:0;
					zydModel::zydlog('app.php?task=zljs&option=zyd&dj='.$zydInfo['yd_level'].'&lx='.$zydInfo['yd_type'].'&sc='.$jksj,array('wp'=>$jlsj,'hqtq'=>$hqtq),$zydRoleInfo['player_level'],$zydRoleInfo['userid']);
				}
				if ($dqzydsl >= $allowzydsl) {
					$updateZyd = "UPDATE ".$common->tname('zyd')." SET zlpid = 0,zlpname='',zlwjid=0,zlwjmc = '',scsysj=0,zlsj=0,ldsl=0,ldzyid=0,ldbhcs=0 WHERE yd_id = '$zydid' LIMIT 1";
					$db->query($updateZyd);		
					$updatedfSql = "UPDATE ".$common->tname('jq')." SET dfpid = 0,dfmc = '' WHERE zyid = '$zydid' && jqlx != 4";
					$db->query($updatedfSql);	
					$hcsj = $jqInfo['ddsj'] + $jqInfo['xhsj'];	
					$updateSql = "UPDATE ".$common->tname('jq')." SET ddsj = '$hcsj',dfpid = 0,dfmc = '',jqlx = '4',createTime = '".$jqInfo['ddsj']."' WHERE jq_id = '$jq_id' LIMIT 1";
					$db->query($updateSql);		
					$hcsj = null;										
				} else {				
					$updateZyd = "UPDATE ".$common->tname('zyd')." SET zlpid = $playersid,zlpname='".mysql_escape_string($roleInfo['nickname'])."',zlwjid='$gid',zlwjmc = '".$general[0]['general_name']."',scsysj='".$jqInfo['ddsj']."',zlsj='".$jqInfo['ddsj']."',ldsl=0,ldzyid=0,ldbhcs=0 WHERE yd_id = '$zydid' LIMIT 1";
					$db->query($updateZyd);
					$mc->delete(MC.$playersid."_zlydxx");	
					$updatedfSql = "UPDATE ".$common->tname('jq')." SET dfpid = $playersid,dfmc = '".mysql_escape_string($roleInfo['nickname'])."' WHERE zyid = '$zydid' && jqlx != 4";
					$db->query($updatedfSql);							
					$updateSql = "UPDATE ".$common->tname('jq')." SET ddsj = 0,dfpid = 0,dfmc = '',jqlx = '1',createTime = '".$jqInfo['ddsj']."' WHERE jq_id = '$jq_id' LIMIT 1";
					$db->query($updateSql);
				}
				if ($isGw == 0) {
					$mc->set(MC.$zydInfo['zlpid'].'_jq',1,0,1800);
					$jqsql = "SELECT * FROM ".$common->tname('jq')." WHERE wfgid = '".$zydInfo['zlwjid']."' && zyid = '$zydid' LIMIT 1";
					$jqres = $db->query($jqsql);
					$jqrows = $db->fetch_array($jqres);
					if (!empty($jqrows)) {								
						//插入探报		
						//$syjg = $nowTime - $zydInfo['scsysj']; //上次收益间隔时间取消
						$msg['X'] = $jqrows['wfgname'];
						//$msg['Y'] = $zydInfo['nickname'];	
						$msg['L'] = $zydInfo['yd_level'];	
						$msg['R'] = zydmc($zydInfo['yd_type']);			
						if (empty($jlsj) || $ldzyAmount <= 0) {
							$msg['xllx'] = 36;
						} else {
							$msg['xllx'] = 55;
							$msg['B'] = $jlmc.' X '.$ldzyAmount;						
						}
						$msg['Z'] = $roleInfo['nickname'];	
						$msg['ZID'] = $roleInfo['playersid'];	
						$msg['A'] = $attack_data[0]['general_name'];
						$msg['AID'] = $gid;	
						$msg['G'] = $sjkczgz;												
						//$mesageDen = addcslashes(json_encode($msg),'\\');				
						$mesageDen = $msg;
						$messageInfoDen = array('playersid'=>0,'jsid'=>$jqrows['wfpid'],'toplayersid'=>$jqrows['wfpid'],'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
						$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
						$messageInfoDen['genre'] = $msg['xllx'];
						lettersModel::addMessage($messageInfoDen);		
						$messageInfoDen = null;
						$mesageDen = null;
						$msg = null;		
						//执行回城操作					
						$hcsj = $nowTime + $jqrows['xhsj'];			
						$updatehcSql = "UPDATE ".$common->tname('jq')." SET ddsj = '$hcsj',dfpid = 0,dfmc = '',jqlx = '4',createTime = '$nowTime',jlsj = '$jlsj' WHERE jq_id = '".$jqrows['jq_id']."' LIMIT 1";
						$db->query($updatehcSql);	
						$updatehcjlSql = "UPDATE ".$common->tname('playergeneral')." SET zydid = '0',act = '4',gohomeTime = '$hcsj' WHERE intID = '".$zydInfo['zlwjid']."' LIMIT 1";
						$db->query($updatehcjlSql);
						$mc->delete(MC.$zydInfo['zlpid'].'_general');					
					}
				}	
				achievementsModel::check_achieve($playersid,'',array('zyd'));				
				return 1;
			}			
		}
	}
	
	//处理掠夺操作
	public static function zxld($playersid,$zydid,$gid,$zypid,$jqid,$xhsj,$jqInfo) {
		global $mc, $db, $common, $_SGLOBAL, $G_PlayerMgr, $zyd_lang;
		$nowTime = $jqInfo['ddsj'];
		$zydInfo = zydModel::hqdzydxx($zypid,$zydid);
		$jlsj = ''; //初始化奖励数据
		$sl = 0;    //初始化奖励数量
		if (!empty($zydInfo['status'])) {
			return false;
		} else {
			$ql_flag = false;
			if(($is_ql = $mc->get(MC.$playersid.'_'.$zydid.'_attack100'))) {// 判断是否是强掠
				$ql_flag = true;
			}
			$player = $G_PlayerMgr->GetPlayer($playersid);
			$nickname = $player->baseinfo_['nickname'];
			if ($zydInfo['zlpid'] != 0) {
				$G_PlayerMgr->removeplayer($zydInfo['zlpid']);
				$defendplayer = $G_PlayerMgr->GetPlayer($zydInfo['zlpid']);
			} else {
				return false;
			}			
			$mc->set(MC.$playersid.'_jq',1,0,1800);
			$mc->set(MC.$zydInfo['zlpid'].'_jq',1,0,1800);
			$mc->delete(MC.$playersid."_ydxx");
			$mc->delete(MC.$playersid."_zlydxx");	
			$mc->delete(MC.$zydInfo['zlpid']."_ydxx");
			$mc->delete(MC.$zydInfo['zlpid']."_zlydxx");	
			$mc->delete(MC.$zydInfo['playersid']."_ydxx");
			$mc->delete(MC.$zydInfo['playersid']."_zlydxx");				
			//如遇到自己打自己则直接返回
			if ($playersid == $zydInfo['zlpid']) {
				$hcsj = $nowTime + $jqInfo['xhsj'];			
				$updatehcSql = "UPDATE ".$common->tname('jq')." SET ddsj = '$hcsj',dfpid = 0,dfmc = '',jqlx = '4',createTime = '$nowTime',jlsj = '' WHERE jq_id = '$jqid' LIMIT 1";
				$db->query($updatehcSql);	
				$updatehcjlSql = "UPDATE ".$common->tname('playergeneral')." SET zydid = '0',act = '4',gohomeTime = '$hcsj' WHERE intID = '$gid' LIMIT 1";
				$db->query($updatehcjlSql);
				$mc->delete(MC.$playersid.'_general');
				if($ql_flag) {
					$msg['xllx'] = 68;
				} else {
					$msg['xllx'] = 51;
				}
				$msg['X'] = $jqInfo['wfgname'];
				$msg['L'] = $zydInfo['yd_level'];	
				$msg['R'] = zydmc($zydInfo['yd_type']);					
				//$mesageDen = addcslashes(json_encode($msg),'\\');				
				$mesageDen = $msg;
				$messageInfoDen = array('playersid'=>0,'jsid'=>$playersid,'toplayersid'=>$playersid,'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
				//$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
				if($ql_flag) {
					$messageInfoDen['genre'] = 68;
				} else {
					$messageInfoDen['genre'] = 51;
				} //$common->insertLog(var_export($ql_flag) . '  ' . $messageInfoDen['genre'] . '   ' . $msg['xllx']);
				lettersModel::addMessage($messageInfoDen);		
				$messageInfoDen = null;
				$mesageDen = null;
				$msg = null;										
				return false;
			}			
			$zlwjid = $zydInfo['zlwjid'];
			if ($zlwjid == 0) {
				return false;
			}
			$dgeneral = cityModel::getGeneralData($zydInfo['zlpid'],false,$zydInfo['zlwjid'],false);
			if (empty($dgeneral)) {
				return false;
			}				
			$dvalue = array();			
			$dgeneral[0]['general_sort'] = 1;							
			$dvalue[] = $dgeneral[0];						
			$defend_data = $dvalue;	
			$defendRoleInfo['playersid'] = $zydInfo['zlpid'];
			$res1 = roleModel::getRoleInfo($defendRoleInfo,false);
			if (empty($res1)) {
				return false;
			}			
			$sf_jwdj = $defendRoleInfo['mg_level'];
			$value['d_level'] = $defendRoleInfo['player_level'];
			$incomeExp = ceil(pow($value['d_level'],1.6) * 0.36 + $value['d_level'] * 2 + 19);
			$value['d_sex'] = $defendRoleInfo['sex'];
			$value['fsxm'] = stripcslashes($defendRoleInfo['nickname']);			
			//评分	
			$dfpfArr = fightModel::getPf($defend_data, $defendRoleInfo);
			$value['gkpf'] = $dfpfArr['wjzdlpf'];
			$value['djpfzb'] = $dfpfArr['djpf'];
			$value['tfpfzb'] = $dfpfArr['tfpf'];
			$value['zbpfzb'] = $dfpfArr['zbpf'];
			$value['jwpfzb'] = $dfpfArr['jwpf'];
			$value['jnpfzb'] = $dfpfArr['jnpf'];
			$value['tgsxpf'] = $dfpfArr['wjzdlpf'];												
			
			$general = cityModel::getGeneralData($playersid,false,$gid,false);
			if (empty($general)) {
				$delJqSql = "DELETE FROM ".$common->tname('jq')." WHERE jq_id = '$jqid' LIMIT 1";
				$db->query($delJqSql);		
				return false;						
			}			
    		$avalue = array();
			if ($general[0]['general_life'] > 0) {
				$general[0]['general_sort'] = 1;							
				$general[0]['command_soldier'] = $general[0]['general_life'];	
				$avalue[] = $general[0];
			} else {
				return false; 
			}
			$attack_data = $avalue;						
			$roleInfo['playersid'] = $playersid;
			$res2 = roleModel::getRoleInfo($roleInfo,false);
			if (empty($res2)) {
				return false;
			}
			$hqzlz = hqzlz($roleInfo,$defendRoleInfo,$general,$dgeneral,1,$player,$defendplayer);  //理论获取战功值 
			$kczlz = ceil($hqzlz / 4);  //理论扣除战功值					
			$value['a_level'] = $roleInfo['player_level'];
			$value['a_sex'] = $roleInfo['sex'];
			$value['wjxm'] = stripcslashes($roleInfo['nickname']);					
			$pfArr = fightModel::getPf($attack_data, $roleInfo);
			$value['wjzdlpf'] = $pfArr['wjzdlpf'];
			$value['djpf'] = $pfArr['djpf'];
			$value['tfpf'] = $pfArr['tfpf'];
			$value['zbpf'] = $pfArr['zbpf'];
			$value['jwpf'] = $pfArr['jwpf'];
			$value['jnpf'] = $pfArr['jnpf'];
			$gf_jwdj = $roleInfo['mg_level'];
			// 判断是否使用了强掠令
			if($ql_flag) {
				$result = actModel::fight($attack_data,$defend_data,'','','',0,$gf_jwdj,$sf_jwdj,1);
				$mc->delete(MC.$playersid.'_'.$zydid.'_attack100');
			} else {
				$result = actModel::fight($attack_data,$defend_data,'','','',0,$gf_jwdj,$sf_jwdj);
			}
			//战斗返回
			$value['begin'] = $result['begin'];                                               //开始士兵情形
			$round = $result['round'];                                               //战斗回合	
			$newround = heroCommon::arr_foreach($round);
			$value['round'] = $newround;
			$round = null;	
			$newround = null;	
			$soldierNum = 0;                                                                      //初始化防守士兵数量
			$attack_result = $result['result'];
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
				}
			}
			$attack_soldier_left = array_sum($attack_soldier);   //攻击方所剩人数
			if ($attack_soldier_left > 0) {
				//武将升级
				$tf = $attack_data[0]['understanding_value'] - $attack_data[0]['llcs']; //武将天赋
				$jbsx = wjsjsx($tf);  //武将级别上限		
				$current_general_level = $attack_data[0]['general_level'];		
				$needJy = cityModel::getGeneralUpgradeExp($current_general_level);                //下级所需经验
				$current_experience = $attack_data[0]['current_experience'];
				//$incomeExp = 100;
				$hqjy = 0;
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
								$attack_data[0]['current_experience'] = $updateGeneral['current_experience'] = $left_experience = $newExp;
								//$value['hqjy'] = $g_hqjy;
								$hqjy = $g_hqjy;
							} else {
								$attack_data[0]['current_experience'] = $updateGeneral['current_experience'] = $left_experience = $needJy;
								$hqjy = $newExp - $needJy;
							}							
						} else {
							$left_experience = $current_experience;
							$hqjy = 0;
						}				 	 	
					 } else {				 	 	
						$generalUpgrade = fightModel::upgradeRole($current_general_level,$newExp,2);
						$left_experience = $generalUpgrade['left'];
						$new_level = $generalUpgrade['level'];
						if ($new_level > $upLevel) {
							$new_level = $upLevel;
							$left_experience = 0;
						}
						$attack_data[0]['current_experience'] = $updateGeneral['current_experience'] = $left_experience;	
						$upgrade = 0;
						if (intval($new_level) > intval($current_general_level) && intval($current_general_level) > 0) {
							$attack_data[0]['general_level'] = $updateGeneral['general_level'] = $new_level;
						}
						$xhjy = array();
						for ($k = 0; $k < ($new_level - $current_general_level); $k++) {
							$xhjy[] = cityModel::getGeneralUpgradeExp($current_general_level + $k);
						}	
						if (!empty($xhjy)) {
							$totalExp =  array_sum($xhjy);        //所需要的总经验
						    $hqjy = $totalExp - $current_experience + $left_experience;
						} else {
							$hqjy = $g_hqjy;
						}								 	 			 	 	
					 }
					 unset($generalUpgrade);	//清空数值	
					 $current_general_level = '';
					 $get_experience = ''; 
					 $current_experience = '';		                                       		                 	
				} else {
					$hqjy = 0;
				}
				//武将升级结束					
			}		
			//$jlsj = '';//初始化奖励数据
			$zgpname = mysql_escape_string($zydInfo['nickname']);
			$zgpid = $zydInfo['zlpid'];
			if ($attack_soldier_left <= 0) {
				$sjkczgz = 0;
				$xzgz = rankModel::decreaseZg($playersid,$kczlz,$sjkczgz,8,mysql_escape_string($zgpname),$zgpid);
				if($ql_flag) {
					$msg['xllx'] = 65;
				} else {
					$msg['xllx'] = 39;
				}
				/*$msg['wjmc1'] = $zydInfo['zlpname'];
				//$msg['wjid1'] = $zydInfo['zlpid'];
				$msg['wjid1'] = $playersid;
				$msg['wjwjmc1'] = $general[0]['general_name'];
				$msg['wjwjid1'] = $gid;
				$msg['zydmc'] = zydmc($zydInfo['yd_type']);	*/
				$msg['X'] = $attack_data[0]['general_name'];
				//$msg['Y'] = $zydInfo['nickname'];	
				$msg['L'] = $zydInfo['yd_level'];	
				$msg['R'] = zydmc($zydInfo['yd_type']);	
				$msg['G'] = $sjkczgz;				
	            //$mesageDen = addcslashes(json_encode($msg),'\\');				
				$mesageDen = $msg;
	            $messageInfoDen = array('playersid'=>0,'jsid'=>$roleInfo['playersid'],'toplayersid'=>$playersid,'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
				$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
				if($ql_flag) {
					$messageInfoDen['genre'] = 65;
				} else {
					$messageInfoDen['genre'] = 39;
				}
				lettersModel::addMessage($messageInfoDen);		
				$messageInfoDen = null;
				$mesageDen = null;
				$msg = null;
				
				if($ql_flag) {
					$msg['xllx'] = 63;
				} else {
					$msg['xllx'] = 43;
				}
				/*$msg['wjmc1'] = $roleInfo['nickname'];
				$msg['wjid1'] = $roleInfo['playersid'];
				$msg['wjwjmc1'] = $general[0]['general_name'];
				$msg['wjwjid1'] = $gid;
				$msg['zydmc'] = zydmc($zydInfo['yd_type']);	*/
				$msg['X'] = $zydInfo['zlwjmc'];
				//$msg['Y'] = $zydInfo['nickname'];	
				$msg['L'] = $zydInfo['yd_level'];	
				$msg['R'] = zydmc($zydInfo['yd_type']);	
				$msg['Z'] = $roleInfo['nickname'];
				$msg['ZID'] = intval($roleInfo['playersid']);		
				$msg['A'] = $attack_data[0]['general_name'];
				$msg['AID'] = intval($attack_data[0]['intID']);							
	            //$mesageDen = addcslashes(json_encode($msg),'\\');				
				$mesageDen = $msg;
	            $messageInfoDen = array('playersid'=>0,'jsid'=>$roleInfo['playersid'],'toplayersid'=>$zydInfo['zlpid'],'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
				$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
				if($ql_flag) {
					$messageInfoDen['genre'] = 63;
				} else {
					$messageInfoDen['genre'] = 43;
				}
				lettersModel::addMessage($messageInfoDen);		
				$messageInfoDen = null;
				$mesageDen = null;
				$msg = null;
			} else {
				$sjzjzgz = 0;
				$xzgz = rankModel::addZg($playersid,$hqzlz,$sjzjzgz,7,mysql_escape_string($zgpname),$zgpid);				
				$jjsj = $nowTime - $zydInfo['scsysj'];
				if ($jjsj >= 3600) {
					// 成功掠夺时增加被掠夺次数
					/*if($zydInfo['ldbhcs'] < 2) {
						$updateZy['ldbhcs'] = $zydInfo['ldbhcs'] + 1;
					}*/
					$zyzsInfo = zydzy($zydInfo['yd_type'],$zydInfo['yd_level'],$jjsj,$zydInfo['ldzyid']);
					if (isset($zyzsInfo['tq'])) {
						if ($zyzsInfo['tq'] == 1) {
							if (rand(1,2) == 2) {
								$ldzyAmount = 1;
							} else {
								$ldzyAmount = 0;
							}
						} else {
							$ldzyAmount = floor(($zyzsInfo['tq'] - $zydInfo['ldsl']) / 2); //该次掠夺资源数量
						}						
						if ($ldzyAmount > 0) {
							//$updateRole['coins'] = $roleInfo['coins'] + $ldzyAmount;
							//$common->updatetable('player',$updateRole,"playersid = '$playersid'");
							//$common->updateMemCache(MC.$playersid,$updateRole);
							$updateZy['ldsl'] = $zydInfo['ldsl'] + $ldzyAmount;
							$common->updatetable('zyd',$updateZy,"yd_id = '".$zydInfo['yd_id']."'");
							$jlsj = json_encode(array('tq'=>$ldzyAmount));
							$logjl = array('tq'=>$ldzyAmount);
							$jlmc = $zyd_lang['scyd_1'];
							$sl = $ldzyAmount;
						} else {
							$jlsj = '';
						}
					} elseif (isset($zyzsInfo['jl'])) {
						if ($zyzsInfo['jl'] == 1) {
							if (rand(1,2) == 2) {
								$ldzyAmount = 1;
							} else {
								$ldzyAmount = 0;
							}							
						} else {
							$ldzyAmount = floor(($zyzsInfo['jl'] - $zydInfo['ldsl']) / 2); //该次掠夺资源数量
						}						
						if ($ldzyAmount > 0) {
							//$updateRole['food'] = $roleInfo['food'] + $ldzyAmount;
							//$common->updatetable('player',$updateRole,"playersid = '$playersid'");
							//$common->updateMemCache(MC.$playersid,$updateRole);
							$updateZy['ldsl'] = $zydInfo['ldsl'] + $ldzyAmount;
							$common->updatetable('zyd',$updateZy,"yd_id = '".$zydInfo['yd_id']."'");	
							$logjl = array('jl'=>$ldzyAmount);
							$jlsj = json_encode(array('jl'=>$ldzyAmount));		
							$jlmc = $zyd_lang['scyd_2'];	
							$sl = $ldzyAmount;		
						} else {
							$jlsj = '';
						}					
					} else {
						$stop = 0;
						if ($zyzsInfo != 0) {
							foreach ($zyzsInfo['dj'] as $key => $zyzsInfovalue) {
								if ($zyzsInfovalue == 1) {
									if (rand(1,2) == 2) {
										$ldzyAmount = 1;
									} else {
										$ldzyAmount = 0;
									}
								} else {
									$ldzyAmount = floor(($zyzsInfovalue - $zydInfo['ldsl']) / 2); //该次掠夺资源数量
								}
								if ($key == 20009 && $ldzyAmount > 4) {  //当金刚石碎片大于4时，有50%几率转换为玄铁碎片，数量为金刚石产量1/4
									if (rand(1,2) == 1) {
										$key = 20127;
										$ldzyAmount = round($ldzyAmount / 4 ,0);
									}
								}																
								if ($ldzyAmount > 0) {					
									$updateZy['ldsl'] = $zydInfo['ldsl'] + $ldzyAmount;
									$updateZy['ldzyid'] = $key;
									$common->updatetable('zyd',$updateZy,"yd_id = '".$zydInfo['yd_id']."'");
									//$mc->set(MC.$roleInfo['playersid'].'_xsbb',1,0,900);	
									$djInfo = toolsModel::getItemInfo($key);
									$jlmc = $djInfo['Name'];
									$sl = $ldzyAmount;
									$logjl = array('dj'=>array($key=>$ldzyAmount));
									$jlsj = json_encode(array('dj'=>array($key=>$ldzyAmount)));															
								} else {
									$jlsj = '';
								}	
							}	
						} else {
							$jlsj = '';
						}				
					}
				} else {
					$jjsj = '';
				}	
				if (!empty($jlsj)) {
					$hqtq = isset($logjl['tq'])?$logjl['tq']:0;
					zydModel::zydlog('app.php?task=ldjs&option=zyd&dj='.$zydInfo['yd_level'].'&lx='.$zydInfo['yd_type'],array('status'=>0,'wp'=>$logjl,'hqtq'=>$hqtq),$roleInfo['player_level'],$roleInfo['userid']);
				}			
				/*$msg['wjmc1'] = $zydInfo['zlpname'];
				//$msg['wjid1'] = $zydInfo['zlpid'];
				$msg['wjid1'] = $playersid;
				$msg['wjwjmc1'] = $general[0]['general_name'];
				$msg['wjwjid1'] = $gid;
				$msg['zydmc'] = zydmc($zydInfo['yd_type']);	*/
				$msg = array();
				$msg['X'] = $attack_data[0]['general_name'];
				//$msg['Y'] = $zydInfo['nickname'];	
				$msg['L'] = $zydInfo['yd_level'];	
				$msg['R'] = zydmc($zydInfo['yd_type']);	
				if (!empty($jjsj) && $sl > 0) {	
					$msg['B'] = $jlmc.' X '.$sl;
					if($ql_flag) {
						$msg['xllx'] = 64;	
					} else {
						$msg['xllx'] = 38;	
					}
				} else {
					if($ql_flag) {
						$msg['xllx'] = 66;
					} else {
						$msg['xllx'] = 40;
					}
				}	
				$msg['E'] = $hqjy.$zyd_lang['zxzl_1'];	
				$msg['G'] = $sjzjzgz;
	            //$mesageDen = addcslashes(json_encode($msg),'\\');				
				$mesageDen = $msg;
	            $messageInfoDen = array('playersid'=>0,'jsid'=>$roleInfo['playersid'],'toplayersid'=>$playersid,'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
				$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
				$messageInfoDen['genre'] = $msg['xllx'];
				lettersModel::addMessage($messageInfoDen);		
				$messageInfoDen = null;
				$mesageDen = null;
				$msg = null;	

				//$msg['xllx'] = 42;
				/*$msg['wjmc1'] = $roleInfo['nickname'];
				$msg['wjid1'] = $roleInfo['playersid'];
				$msg['wjwjmc1'] = $general[0]['general_name'];
				$msg['wjwjid1'] = $gid;
				$msg['zydmc'] = zydmc($zydInfo['yd_type']);*/	
				$sjkczgz = 0;
				$dxzgz = rankModel::decreaseZg($zydInfo['zlpid'],$kczlz,$sjkczgz,11,mysql_escape_string($nickname),$playersid);
				if ($sjkczgz > 0) {
					$common->updatetable('player',$dxzgz,"playersid=".$zydInfo['zlpid']);
					$common->updateMemCache(MC.$zydInfo['zlpid'],$dxzgz);
					$G_PlayerMgr->removeplayer($zydInfo['zlpid']);
				}
				$msg['X'] = $zydInfo['zlwjmc'];
				//$msg['Y'] = $zydInfo['nickname'];	
				$msg['L'] = $zydInfo['yd_level'];	
				$msg['R'] = zydmc($zydInfo['yd_type']);	
				$msg['Z'] = $roleInfo['nickname'];
				$msg['ZID'] = intval($roleInfo['playersid']);		
				$msg['A'] = $attack_data[0]['general_name'];
				$msg['G'] = $sjkczgz;
				$msg['AID'] = intval($attack_data[0]['intID']);
				if (!empty($jjsj) && $sl > 0) {	
					$msg['B'] = $jlmc.' X '.$sl;
					if($ql_flag) {
						$msg['xllx'] = 62;	
					} else {
						$msg['xllx'] = 42;
					}
				} else {
					if($ql_flag) {
						$msg['xllx'] = 69;
					} else {
						$msg['xllx'] = 56;
					}
				}					
	            //$mesageDen = addcslashes(json_encode($msg),'\\');				
				$mesageDen = $msg;
	            $messageInfoDen = array('playersid'=>0,'jsid'=>$roleInfo['playersid'],'toplayersid'=>$zydInfo['zlpid'],'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
				$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
				$messageInfoDen['genre'] = $msg['xllx'];
				lettersModel::addMessage($messageInfoDen);		
				$messageInfoDen = null;
				$mesageDen = null;
				$msg = null;				
			}
		   	$ddsj = $nowTime + $xhsj;
			$updateSql = "UPDATE ".$common->tname('jq')." SET ddsj = '$ddsj',dfpid = 0,dfmc = '',jqlx = '4',createTime='$nowTime',jlsj = '$jlsj' WHERE jq_id = '$jqid' LIMIT 1";
			$db->query($updateSql);		
			$act = 4;
			$updatejqid = $jqid;	
			$attack_data[0]['gohomeTime'] = $updateGeneral['gohomeTime'] = $ddsj;	
			$attack_data[0]['act'] = $updateGeneral['act'] = 4;	
			$attack_data[0]['jqid'] = $updateGeneral['jqid'] = $jqid;	
			$attack_data[0]['zydid'] = $updateGeneral['zydid'] = $zydid;					
			//更新攻击方将领数据
			for ($k = 0; $k < 1; $k++) {
				$id = $attack_data[$k]['intID'];
				$left_command_soldier = $attackGeneralLeftSoldier[$id];                             //剩余士兵数量
				$sssm = $attack_data[$k]['general_life'];                                           //武将生命
				$wjmc = $attack_data[$k]['general_name'];                                           //武将名称
				$updateGeneral['general_life'] = $left_command_soldier;    
				$attack_data[$k]['general_life'] = $left_command_soldier;
				$attack_data[$k]['command_soldier'] = $left_command_soldier;	
				//$whereGeneral['intID'] = $id;				
				//$common->updatetable('playergeneral',$updateGeneral,$whereGeneral);               //更新将领数据			  				
				//$newData[$attack_data[$k]['sortid']] = $attack_data[$k];     	     		     
				//$common->updateMemCache(MC.$playersid.'_general',$newData);   
				//$mc->delete(MC.$playersid.'_general');
				//自动补血
				zydModel::ksbx($roleInfo,$attack_data[$k],$id,$updateGeneral,$xzgz);
		   	}
		   	return 1;			
		}
	}
	
	//收取资源点收益
	public static function sqzydsy($playersid,$tuid,$zydid) {
		global $mc, $db, $common, $_SGLOBAL, $zyd_lang;
		$nowTime = $_SGLOBAL['timestamp'];
		$zydInfo = zydModel::hqdzydxx($tuid,$zydid);
		if (!empty($zydInfo['status'])) {
			return array('status'=>21,'message'=>$zyd_lang['zydlb_1021_2']);
		} else {
			if ($zydInfo['zlpid'] != $playersid) {
				$value['uid'] = intval($zydInfo['zlpid']);
				$value['un'] = $zydInfo['zlpname'];
				$value['status'] = 1001;
				return $value;
			} else {
				$roleInfo['playersid'] = $playersid;
				$res = roleModel::getRoleInfo($roleInfo);
				$gesc = $nowTime - $zydInfo['scsysj'];
				if ($gesc < 3600) {
					return array('status'=>21,'message'=>$zyd_lang['sqzydsy_21']);
				}
				$zyzsInfo = zydzy($zydInfo['yd_type'],$zydInfo['yd_level'],$gesc,$zydInfo['ldzyid']);
				$value['status'] = 0;
				
				// 资源点收获活动触发点
				hdProcess::run(array('fight_harvestZyd'), $roleInfo, 1);
				
				/*结算获取的资源*/
				if (isset($zyzsInfo['tq'])) {
					$ldzyAmount = floor($zyzsInfo['tq'] - $zydInfo['ldsl']); //该次掠夺资源数量
					if ($ldzyAmount > 0) {
						//$updateRole['coins'] = $roleInfo['coins'] + $ldzyAmount;
						//$common->updatetable('player',$updateRole,"playersid = '$playersid'");
						//$common->updateMemCache(MC.$playersid,$updateRole);
						//$value['tq'] = $updateRole['coins'];
						//$value['hqtq'] = $ldzyAmount;
						//$updateZy['ldsl'] = 0;
						$updateZy['scsysj'] = $nowTime;
						$common->updatetable('zyd',$updateZy,"yd_id = '".$zydInfo['yd_id']."'");	
						$jlsj = json_encode(array('tq'=>$ldzyAmount));	
						$value['hdnr'] = array('mc'=>$zyd_lang['scyd_1'],'sl'=>$ldzyAmount);	
						$shmc = $zyd_lang['scyd_1'];		
						$shsl = $ldzyAmount;	
					} else {
						$jlsj = '';
					}
				} elseif (isset($zyzsInfo['jl'])) {					
					$ldzyAmount = floor($zyzsInfo['jl'] - $zydInfo['ldsl']); //该次掠夺资源数量
					if ($ldzyAmount > 0) {
						//$updateRole['food'] = $roleInfo['food'] + $ldzyAmount;
						//$common->updatetable('player',$updateRole,"playersid = '$playersid'");
						//$common->updateMemCache(MC.$playersid,$updateRole);
						//$value['jl'] = $updateRole['food'];
						//$updateZy['ldsl'] = 0;
						$updateZy['scsysj'] = $nowTime;
						$common->updatetable('zyd',$updateZy,"yd_id = '".$zydInfo['yd_id']."'");	
						$jlsj = json_encode(array('jl'=>$ldzyAmount));	
						$value['hdnr'] = array('mc'=>$zyd_lang['scyd_2'],'sl'=>$ldzyAmount);		
						$shmc = $zyd_lang['scyd_2'];		
						$shsl = $ldzyAmount;									
					} else {
						$jlsj = '';
					}					
				} else {
					$stop = 0;
					//heroCommon::insertLog(json_encode($zyzsInfo));
					if ($zyzsInfo != 0) {
						foreach ($zyzsInfo['dj'] as $key => $zyzsInfovalue) {
							$ldzyAmount = floor($zyzsInfovalue - $zydInfo['ldsl']); //该次掠夺资源数量
							if ($key == 20009 && $ldzyAmount > 4) {  //当金刚石碎片大于4时，有50%几率转换为玄铁碎片，数量为金刚石产量1/4
								if (rand(1,2) == 1) {
									$key = 20127;
									$ldzyAmount = round($ldzyAmount / 4 ,0);
								}
							}							
							if ($ldzyAmount > 0) {
								$jlsj = json_encode(array('dj'=>array($key=>$ldzyAmount)));
								$updateZy['ldsl'] = 0;
								$updateZy['scsysj'] = $nowTime;
								$common->updatetable('zyd',$updateZy,"yd_id = '".$zydInfo['yd_id']."'");	
								$djInfo = toolsModel::getItemInfo($key);
								$djmc = $djInfo['Name'];
								$value['hdnr'] = array('mc'=>$djmc,'sl'=>$ldzyAmount);		
								$shmc = $djmc;		
								$shsl = $ldzyAmount;																
							} else {
								//$value['status'] = 21;
								//$value['message'] = '你没有收到任何收益';
								$jlsj = '';
							}	
						}
					} else {
						$jlsj = '';
						//$value['message'] = '你没有收到任何收益';
					}					
				}		
				/*结算结束*/	
				
				//发布探报
				if (!empty($jlsj) && $shsl > 0) {
					$msg['xllx'] = 45;
					$msg['B'] = $shmc.' X '.$shsl;
				} else {
					$msg['xllx'] = 46;
				}				
				$msg['X'] = $zydInfo['zlwjmc'];
				//$msg['Y'] = $zydInfo['nickname'];	
				$msg['L'] = $zydInfo['yd_level'];				
				$msg['R'] = zydmc($zydInfo['yd_type']);	
								
		        //$mesageDen = addcslashes(json_encode($msg),'\\');				
				$mesageDen = $msg;
		        $messageInfoDen = array('playersid'=>0,'jsid'=>$roleInfo['playersid'],'toplayersid'=>$playersid,'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
				$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
				$messageInfoDen['genre'] = $msg['xllx'];
				lettersModel::addMessage($messageInfoDen);		
				$messageInfoDen = null;
				$mesageDen = null;
				$msg = null;				

				/*撤回该武将*/
				if ($playersid == $zydInfo['playersid']) {
					$needTime = 300;    //需要花费时间
				} else {
					$needTime = 1800;   //需要花费时间
				}	
				$ddsj = $nowTime + $needTime;
				$updateJq = "UPDATE ".$common->tname('jq')." SET jqlx = '4',xhsj = '$needTime',ddsj = '$ddsj',createTime = '$nowTime',dfpid=0,dfmc='',jlsj = '$jlsj' WHERE wfgid = '".$zydInfo['zlwjid']."' && zyid = '$zydid' LIMIT 1";
				$db->query($updateJq); 
				$updateGen = "UPDATE ".$common->tname('playergeneral')." SET gohomeTime = '$ddsj',act = '4' WHERE intID = '".$zydInfo['zlwjid']."' LIMIT 1";
				$db->query($updateGen);  
				//重置资源点数据
				$updateZyd = "UPDATE ".$common->tname('zyd')." SET zlpid = 0,zlpname='',zlwjid=0,zlwjmc='',scsysj=0,zlsj=0,wjmc='',ldsl=0,ldzyid= 0,ldbhcs=0 WHERE yd_id = '$zydid'";
				$db->query($updateZyd);		
				//重置以我为敌对所有军情数据		
				$updatewfJq = "UPDATE ".$common->tname('jq')." SET dfpid=0,dfmc='' WHERE dfpid = '$playersid' && zyid = '$zydid'";
				$db->query($updatewfJq); 
								
				$mc->delete(MC.$playersid."_ydxx");
				$mc->delete(MC.$playersid."_zlydxx");
				$mc->delete(MC.$tuid."_ydxx");
				$mc->delete(MC.$tuid."_zlydxx");				
				$mc->delete(MC.$playersid.'_general');
	 			/*撤回该武将结束*/	
				/*遣返所有掠夺武将*/
				$sql = "SELECT * FROM ".$common->tname('jq')." WHERE zyid = '$zydid' && jqlx = '3'";
				$result = $db->query($sql);
				while ($rows = $db->fetch_array($result)) {
					$sysj = $rows['ddsj'] - $nowTime;
					if ($sysj < 0) {
						$sysj = 0;
					}
					$fhsj = $nowTime + ($rows['xhsj'] - $sysj);
					$updateGen = "UPDATE ".$common->tname('playergeneral')." SET gohomeTime = '$fhsj',act='4',zydid='0' WHERE intID = '".$rows['wfgid']."' LIMIT 1";
					$db->query($updateGen);  
					$mc->delete(MC.$rows['wfpid'].'_general');	
					$updateSql = "UPDATE ".$common->tname('jq')." SET ddsj = '$fhsj',dfpid = 0,dfmc = '',jqlx = '4',createTime='$nowTime' WHERE jq_id = '".$rows['jq_id']."' LIMIT 1";
					$db->query($updateSql);	
					
					$ql_flag = false;
					if(($is_ql = $mc->get(MC.$rows['wfpid'].'_'.$zydid.'_attack100'))) {// 判断是否是强掠
						$ql_flag = true;
						$mc->delete(MC.$rows['wfpid'].'_'.$zydid.'_attack100');
					}
					
					//发放探报
					if($ql_flag) {
						$msg['xllx'] = 67;
					} else {
						$msg['xllx'] = 41;
					}
					$msg['X'] = $rows['wfgname'];
					//$msg['Y'] = $zydInfo['nickname'];	
					$msg['L'] = $zydInfo['yd_level'];	
					$msg['R'] = zydmc($zydInfo['yd_type']);								
					//$mesageDen = addcslashes(json_encode($msg),'\\');				
					$mesageDen = $msg;
					$messageInfoDen = array('playersid'=>0,'jsid'=>$rows['wfpid'],'toplayersid'=>$rows['wfpid'],'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
					$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
					$messageInfoDen['genre'] = $msg['xllx'];
					lettersModel::addMessage($messageInfoDen);		
					$messageInfoDen = null;
					$mesageDen = null;
					$msg = null;													
				} 
				/*遣返所有掠夺武将结束*/	
				$value['lx'] = intval($zydInfo['yd_type']);
				$value['dj'] = intval($zydInfo['yd_level']);	
				$value['sc'] = $gesc;		
				return $value;			
			}
		}		
	}
	//元宝立刻召回
	public static function ldzh($playersid,$gid) {
		global $db, $common, $mc, $_SGLOBAL, $zyd_lang, $sys_lang;
		$sql = "SELECT * FROM ".$common->tname('jq')." WHERE wfgid = '$gid' && wfpid = '$playersid' && jqlx != 1 LIMIT 1";
		$result = $db->query($sql);
		$rows = $db->fetch_array($result);
		if (empty($rows)) {
			return array('status'=>21,'message'=>$zyd_lang['hqjq_1021']);
		} else {
			if ($rows['wfgid'] != $gid) {
				return array('status'=>21,'message'=>$sys_lang[3]);
			} else {
				$roleInfo['playersid'] = $playersid;
				roleModel::getRoleInfo($roleInfo);
				if ($rows['jqlx'] == 4 || $rows['jqlx'] == 7) {
					$sysj = $rows['ddsj'] - $_SGLOBAL['timestamp'];
				} else {
					$sysj = $rows['xhsj'] - ($rows['ddsj'] - $_SGLOBAL['timestamp']);  //剩余时间
				}							   
				if ($sysj > 600) {
					$xyyb = 10;
				} else {
					$xyyb = 5;
				}
				if ($roleInfo['ingot'] < $xyyb) {
					$arr1 = array('{xhyb}','{yb}');
					$arr2 = array($xyyb,$roleInfo['ingot']);					
					return array('status'=>88,'message'=>str_replace($arr1, $arr2, $sys_lang[2]),'yb'=>floor($roleInfo['ingot']));
				} else {
					$updateRole['ingot'] = $roleInfo['ingot'] - $xyyb;
					$value['yb'] = $updateRole['ingot'];
					$value['xhyb'] = $xyyb;
					$updateRoleWhere['playersid'] = $playersid;
					$common->updatetable('player',$updateRole,$updateRoleWhere);
					$common->updateMemCache(MC.$playersid,$updateRole);
					$db->query("DELETE FROM ".$common->tname('jq')." WHERE wfgid = '$gid' && wfpid = '$playersid'");
					//结算资源
					$jlsj = $rows['jlsj'];
					if (!empty($jlsj)) {
						$amount = 0;
						$jlsj = json_decode($jlsj,true);
						zydModel::jlzy($roleInfo,$jlsj);
						if (isset($jlsj['tq'])) {
							$jlmc = $zyd_lang['scyd_1'];
							$amount = $jlsj['tq'];
						} elseif (isset($jlsj['jl'])) {
							$jlmc = $zyd_lang['scyd_2'];
							$amount = $jlsj['jl'];
						} else {
							$jlInfo = $jlsj['dj'];
							foreach ($jlInfo as $key => $amount) {
								$toolsInfo = toolsModel::getItemInfo($key);
								$jlmc = $toolsInfo['Name'];
								if ($amount == 0) {
									$jlsj = '';
								}
							}
						}
					} else {
						$jlsj = '';
					}	
					if (empty($jlsj) || $amount <= 0) {
						$msg['xllx'] = 54;
					} else {
						$msg['xllx'] = 53;
						$msg['B'] = $jlmc.' X '.$amount;
					}
					if ($rows['jqlx'] == 5 || $rows['jqlx'] == 7) {
						$zydInfo = zydModel::hqzlzyd($rows['zyid']);
						$msg['L'] = $zydInfo['dj'];
						$value['zhlx'] = 1;
						if ($rows['jqlx'] == 5) {
							$zlsl = $mc->get(MC.$rows['zyid']."_zlsl"); //获取多少人逐鹿	
							$newsl = $zlsl - 1;
							if ($newsl < 0) {
								$newsl = 0;
							}				
							$mc->set(MC.$rows['zyid']."_zlsl",$newsl,0,1800);	
						}
					} else {
						$zydInfo = zydModel::hqdzydxx($rows['zydowner'],$rows['zyid']);	
						$msg['L'] = $zydInfo['yd_level'];		
					}		
					$msg['X'] = $rows['wfgname'];
					//$msg['Y'] = $zydInfo['nickname'];						
					$msg['R'] = zydmc($zydInfo['yd_type']);					
					//$mesageDen = addcslashes(json_encode($msg),'\\');				
					$mesageDen = $msg;
					$messageInfoDen = array('playersid'=>0,'jsid'=>$playersid,'toplayersid'=>$playersid,'subject'=>'','message'=>$mesageDen,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
					//$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
					$messageInfoDen['genre'] = $msg['xllx'];
					lettersModel::addMessage($messageInfoDen);		
					$messageInfoDen = null;
					$mesageDen = null;
					$msg = null;	
											
					$db->query("UPDATE ".$common->tname('playergeneral')." SET gohomeTime = '0',jqid = '0',zydid = '0',act = '0' WHERE intID = '$gid' LIMIT 1");
					$mc->delete(MC.$playersid.'_general');
					$value['status'] = 0;
					if ($rows['jqlx'] == 5) {
						$rwid = questsController::OnFinish ( $roleInfo, "'lkzh','zllkzh'" );
					} else {
						$rwid = questsController::OnFinish ( $roleInfo, "'lkzh'" );
					}
					if (! empty ( $rwid )) {
						$value ['rwid'] = $rwid;
					}
					return $value;
				}
			}
		}
	}
	//资源点详情
	public static function zydxq($playersid,$tuid,$zydid,$type) {
		global $mc, $db, $common, $_SGLOBAL, $zyd_lang;
		$zydInfo = zydModel::hqdzydxx($tuid,$zydid);
		//$gInfo = cityModel::getGeneralData($tuid,false,'*',false);		
		/*if (empty($gInfo)) {
			return array('status'=>1021,'message'=>'该资源点武将数据有误');
		}*/
		if (empty($zydInfo)) {
			return array('status'=>1021,'message'=>$zyd_lang['zydlb_1021']);
		} else {
			$zlarray = $zsarray = $ldarray = array();
			/*foreach ($gInfo as $gInfoValue) {
				if ($gInfoValue['act'] == 2) {
					$zlarray[] = $gInfoValue['zydid'];
				}
				if ($gInfoValue['act'] == 3) {
					$ldarray[] = $gInfoValue['zydid'];
				}					
			}*/
			$zydsj = ydsj($zydInfo['yd_level'],$zydInfo['yd_type']);
			$value['cc'] = zydcc($zydInfo['yd_type']);
			$value['npcdj'] = $zydsj['dj'];
			$value['twjmc'] = $zydInfo['nickname'];
			$value['wjmc1'] = $zydInfo['zlpname'];
			if ($zydInfo['playersid'] == $zydInfo['zlpid']) {
					$zllx = 1;
				} elseif ($zydInfo['zlpid'] == 0) {
					$zllx = 2;					
					$value['wjmc1'] = $zydsj['xm'];
				} else {
					$zllx = 3;
				}	
				$shsh = $_SGLOBAL['timestamp'] - $zydInfo['scsysj'];
				if ($shsh >= 3600) {
					$sh = 1;
				} else {
					$sh = 0;
				}
				if ($zydInfo['zlpid'] != 0) {
					$zlsj = $_SGLOBAL['timestamp'] -  $zydInfo['zlsj'];
				} else {
					$zlsj = 0;
				}
				$value['lx'] = $zllx;
				$value['tuid'] = $tuid;
				$value['zydid'] = $zydid;
				$value['zydlx'] = intval($zydInfo['yd_type']);
				$value['zydj'] = intval($zydInfo['yd_level']);								
				$value['wjid1'] = intval($zydInfo['zlpid']);
				$value['zssj'] = $zlsj;
				$value['sh'] = $sh;				
				$value['jlmc'] = $zydInfo['zlwjmc'];
				$value['jlid'] = intval($zydInfo['zlwjid']);	
				$value['ldcs'] = intval($zydInfo['ldbhcs']);
				if ($value['jlid'] != 0) {
					$aginfo = cityModel::getGeneralData($zydInfo['zlpid'],false,$zydInfo['zlwjid']);
					$value['jlicon'] = 	$aginfo[0]['avatar'];
					$value['jltf'] = $aginfo[0]['understanding_value'] - $aginfo[0]['llcs'];	
					$value['jlllcs'] =	intval($aginfo[0]['llcs']);	
					$value['jldj'] = intval($aginfo[0]['general_level']);	
				}	
				$sql = "SELECT * FROM ".$common->tname('jq')." WHERE zyid = '".$zydInfo['yd_id']."' && (jqlx = 2 || jqlx = 3)  ORDER BY ddsj ASC LIMIT 1";							
				$result = $db->query($sql);
				$rows = $db->fetch_array($result);
				if (!empty($rows)) {
					$getInfo ['playersid'] = $rows['wfpid'];
					//$ginfo = cityModel::getGeneralList ( $getInfo, 1, false, $rows['wfgid'], false );
					$ginfo = cityModel::getGeneralData($rows['wfpid'],false,$rows['wfgid']);
					//$value ['status'] = $ginfo ['status'];
					if (!empty($ginfo)) {
						$value['jqicon'] = $ginfo[0]['avatar'];
						$value['jqtf'] = $ginfo[0]['understanding_value'] - $ginfo[0]['llcs'];
						$value['jqllcs'] = intval($ginfo[0]['llcs']);
						$value['jqdj'] = intval($ginfo[0]['general_level']);
						//$value ['gen'] = $ginfo ['generals'];
					}
					$ddsj = $rows['ddsj'] - $_SGLOBAL['timestamp'];
					if ($ddsj < 0) {
						$ddsj = 0;
					}
					$value['jqddsj'] = $ddsj;
					$value['jqwjmc'] = $rows['wfmc'];
					$value['jqjlmc'] = $rows['wfgname'];
					if ($rows['jqlx'] == 2) {
						$value['jqcz'] = 1;
					} else {
						$value['jqcz'] = 2;
					}
					$value['jqwjid'] = intval($rows['wfpid']);
					$value['jqjlid'] = intval($rows['wfgid']);					
				} else {
					$value['jqddsj'] = 0;
					$value['jqwjmc'] = 0;
					$value['jqjlmc'] = 0;
					$value['jqcz'] = 0;		
					$value['jqwjid'] = 0;
					$value['jqjlid'] = 0;					
				}					
				$value['status'] = 0;
				return $value;			
		}			
	}
	
	//战斗完后快速补血
	public static function ksbx($roleInfo,$ginfo, $id, $updateGeneral,$xzg = 0) {
		global $common, $db, $mc;
		//$roleInfo ['playersid'] = $getInfo ['playersid'];
		//$playersid = $getInfo ['playersid'];
		//roleModel::getRoleInfo ( $roleInfo );
		$jwdj = $roleInfo['mg_level'];
		$jwInfo = jwmc($jwdj);
		$jwjc = 1 + $jwInfo['jc'] / 100;		
		$coins = $roleInfo ['coins'];
		$updateRole = array();
		if (empty($ginfo) || empty($roleInfo)) {
			//heroCommon::insertLog('跳出');
			return false;
		}
		if (! empty ( $id )) {
			$zbtljcArray = array ();
			$zb1 = $ginfo ['helmet'];
			if ($zb1 != 0) {
				$zb1Info = toolsModel::getZbSx ( $roleInfo ['playersid'], $zb1 );
				$zbtljcArray [] = $zb1Info ['tl'];
			}
			$zb2 = $ginfo ['carapace'];
			if ($zb2 != 0) {
				$zb2Info = toolsModel::getZbSx ( $roleInfo ['playersid'], $zb2 );
				$zbgjjcArray [] = $zb2Info ['gj'];
				$zbfyjcArray [] = $zb2Info ['fy'];
				$zbtljcArray [] = $zb2Info ['tl'];
				$zbmjjcArray [] = $zb2Info ['mj'];
			}
			$zb3 = $ginfo ['arms'];
			if ($zb3 != 0) {
				$zb3Info = toolsModel::getZbSx ( $roleInfo ['playersid'], $zb3 );
				$zbtljcArray [] = $zb3Info ['tl'];
			}
			$zb4 = $ginfo ['shoes'];
			if ($zb4 != 0) {
				$zb4Info = toolsModel::getZbSx ( $roleInfo ['playersid'], $zb4 );
				$zbtljcArray [] = $zb4Info ['tl'];
			}
			if (! empty ( $zbtljcArray )) {
				$zbtljc = array_sum ( $zbtljcArray );
			} else {
				$zbtljc = 0;
			}
			unset ( $zbtljcArray );
			$currenLife = $ginfo ['general_life'];
			$dj = $ginfo ['general_level'];
			$tfz = $ginfo ['understanding_value'];
			$sxxs = genModel::sxxs ( $ginfo ['professional'] );
			//$tlz = ((genModel::wjqx ( $dj ) * 0.4 * $sxxs ['tl']) + genModel::wjtf ( $tfz, $dj ) * $sxxs ['tl']) * $jwjc  + $zbtljc;
			$jj = $ginfo ['professional_level'];
			$tlz = genModel::hqwjsx($dj,$tfz,$jj,$ginfo ['llcs'],$jwjc,$zbtljc,$sxxs ['tl'],$ginfo ['py_tl']);
			unset ( $sxxs );
			$lifeLimitUpValue = round ( $tlz, 0 ) * 10; //血量上限	   
			//echo '<br>'.$lifeLimitUpValue.'<br>';
			if ($currenLife < $lifeLimitUpValue) {				
				$needLife = $lifeLimitUpValue - $currenLife; //需要的血量  	  
				//$lifePrice = addLifeCost($forcelv);
				$lifePrice = addLifeCost($roleInfo['player_level']);
				$cost = $needLife * $lifePrice;
				//heroCommon::insertLog('syybs+'.$cost."+pid+".$roleInfo['playersid']);
				if ($coins > $lifePrice) {					
					if ($coins >= $cost) {
						$value ['status'] = 0;
						$leftCoins = $coins - $cost;
						$updateGeneral ['general_life'] = round ( $lifeLimitUpValue, 0 );
					} else {
						//$value ['status'] = 1021;
						$value ['status'] = 0;
						$value ['yb'] = floor ( $roleInfo ['ingot'] );
						$value ['yp'] = floor ( $roleInfo ['silver'] );
						$value ['jl'] = floor ( $roleInfo ['food'] );
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
					$ginfo ['general_life'] = $updateGeneral ['general_life'];
					$ginfo ['command_soldier'] = $updateGeneral ['general_life'];					
					$updateRole ['coins'] = floor ( $leftCoins );					
					//heroCommon::insertLog('sytqs+'.$updateRole ['coins']."+pid+".$roleInfo['playersid'].'+ylqss+'.$coins);
					$value ['tq'] = $updateRole ['coins'];
					$value ['gid'] = $id;
					$value ['xhtq'] = floor ( $roleInfo ['coins'] - $updateRole ['coins'] );
				}				
			}
			if ($xzg != 0) {
				$updateRole = array_merge($updateRole, $xzg);
			}
			if (!empty($updateRole)) {
				$updateRoleWhere ['playersid'] = $roleInfo['playersid'];
				$common->updatetable ( 'player', $updateRole, $updateRoleWhere );
				$common->updateMemCache ( MC . $roleInfo['playersid'], $updateRole );
			}
			$updateGeneralWhere ['intID'] = $id;
			$common->updatetable ( 'playergeneral', $updateGeneral, $updateGeneralWhere );
			$mc->delete(MC . $roleInfo['playersid'] . '_general');	//清除武将memcache数据		
			//$newData [$ginfo ['sortid']] = $ginfo;
			//$common->updateMemCache ( MC . $roleInfo['playersid'] . '_general', $newData );			
		}
	}	
	
	//获取逐鹿资源点信息
	public static function hqzlzyd($zydid) {
		global $db, $common, $mc;
		if (!($zlzydInfo = $mc->get(MC.$zydid.'_zlzyd'))) {
			$sql = "SELECT * FROM ".$common->tname('zyd_qxzl')." WHERE yd_id = $zydid LIMIT 1";
			$result = $db->query($sql);
			$rows = $db->fetch_array($result);
			if (empty($rows)) {
				return false;
			} else {
				$mc->set(MC.$zydid.'_zlzyd',$rows,0,0);
				return $rows;
			}
		}
		return $zlzydInfo;
	}
	
	//获取逐鹿资源点状态
	public static function zlzydzt() {
		global $db, $common, $mc, $_SGLOBAL;
		$nowTime = $_SGLOBAL['timestamp'];
		$beginTime = strtotime(date('Y-m-d',$nowTime));
		$zydValue = array();
		if (!($zlzyd = $mc->get(MC.'zlzydxx'))) {
			$sql = "SELECT * FROM ".$common->tname('zyd_qxzl');
			$result = $db->query($sql);
			$zydInfo = array();
			while ($rows = $db->fetch_array($result)) {
				$zydInfo[] = $rows;
				$zydValue[] = array('id'=>intval($rows['yd_id']),'mc'=>$rows['zlmc'],'time'=>intval(zyzkssj($rows['yd_id'])),'type'=>intval($rows['yd_type']));
			}
			if (!empty($zydInfo)) {
				$mc->set(MC.'zlzydxx',$zydInfo,0,0);
			}
		} else {
			foreach ($zlzyd as $rows) {
				$zydValue[] = array('id'=>intval($rows['yd_id']),'mc'=>$rows['zlmc'],'time'=>intval(zyzkssj($rows['yd_id'])),'type'=>intval($rows['yd_type']));
			}
		}
		return array('status'=>0,'zllist'=>$zydValue);
	}
	
	//逐鹿资源点详情
	public static function zlzydxq($zydid) {
		global $_SGLOBAL,$mc,$zyd_lang;
		$nowTime = $_SGLOBAL['timestamp'];
		$beginTime = strtotime(date('Y-m-d',$nowTime));
		$zydInfo = zydModel::hqzlzyd($zydid);
		if (empty($zydInfo)) {
			return array('status'=>100,'message'=>$zyd_lang['zydlb_1021']);
		} else {
			$value['status'] = 0;
			$value['wjid'] = intval($zydInfo['zlpid']);
			$value['wjmc'] = $zydInfo['zlpname'];
			$value['jlid'] = intval($zydInfo['zlwjid']);
			$value['jlmc'] = $zydInfo['zlwjmc'];
			$value['jssj'] = $beginTime + zyzkssj($zydInfo['yd_id']) * 3600 - $nowTime;
			if ($zydInfo['zlsj'] > 0) {
				$value['zssj'] = $nowTime - $zydInfo['zlsj'];
			} else {
				$value['zssj'] = 0;
			}
			$value['hdjl'] = $zydInfo['djmc'];
			$value['ms'] = $zydInfo['zljj'];
			$value['dd'] = $zydInfo['zldd'];
			$value['jb'] = intval($zydInfo['yd_level']);
			$value['sj'] = intval(zyzkssj($zydInfo['yd_id']));			
			$value['mc'] = $zydInfo['zlmc'];
			$value['type'] = $zydInfo['yd_type'];
			$value['zydid'] = $zydid;
			if ($value['wjid'] == 0) {
				if ($zydInfo['zy'] == 1) {
					$zy = $zyd_lang['zlzydxq_1'];
				} elseif ($zydInfo['zy'] == 2) {
					$zy = $zyd_lang['zlzydxq_2'];
				} elseif ($zydInfo['zy'] == 3) {
					$zy = $zyd_lang['zlzydxq_3'];
				} elseif ($zydInfo['zy'] == 4) {
					$zy = $zyd_lang['zlzydxq_4'];
				} else {
					$zy = $zyd_lang['zlzydxq_5'];
				}
				$value['npc'] = $zydInfo['yd_level'].$zyd_lang['zlzydxq_6'].$zy.$zyd_lang['zlzydxq_7'].$zydInfo['xm'];
			}
			$dlsl = $mc->get(MC.$zydid.'_zlsl');
			$value['dlsl'] = intval($dlsl);
			return $value;
		}
	}
	
	//急行军
	public static function jxj($playersid,$gid) {
		global $db, $common, $mc, $_SGLOBAL, $zyd_lang, $sys_lang;
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
		$yb = $roleInfo['ingot'];
		$nowTime = $_SGLOBAL['timestamp'];
		if ($yb < 10) {
			$arr1 = array('{xhyb}','{yb}');
			$arr2 = array('10',$roleInfo['ingot']);				
			return array('status'=>88,'message'=>str_replace($arr1, $arr2, $sys_lang[2]),'yb'=>floor($roleInfo['ingot']));
		} else {
			$sql = "SELECT * FROM ".$common->tname('jq')." WHERE wfpid = $playersid && wfgid = $gid && (jqlx = 2 || jqlx = 5 || jqlx = 3) LIMIT 1";
			$result = $db->query($sql);
			$rows = $db->fetch_array($result);
			if (empty($rows)) {
				return array('status'=>21,'message'=>$zyd_lang['jxj_1']);
			}
			$updateRole['ingot'] = $yb - 10;
			$updateRoleWhere['playersid'] = $playersid;
			$common->updatetable('player',$updateRole,$updateRoleWhere);
			$common->updateMemCache(MC.$playersid,$updateRole);	
			$sysj = floor(($rows['ddsj'] - $nowTime) / 2);
			if ($sysj < 0) {
				$sysj = 0;
			}
			$ddsj = $nowTime + $sysj;	
			$jqsql = "UPDATE ".$common->tname('jq')." SET ddsj = $ddsj WHERE wfpid = $playersid && wfgid = $gid LIMIT 1";
			$db->query($jqsql);
			$wjsql = "UPDATE ".$common->tname('playergeneral')." SET gohomeTime = $ddsj WHERE intID = $gid && playerid = $playersid LIMIT 1";
			$db->query($wjsql);
			$mc->delete(MC.$playersid.'_general');
			if ($rows['jqlx']) {
				$value['sfzl'] = 1;
			}
			$value['status'] = 0;
			$value['yb'] = $updateRole['ingot'];
			$value['xhyb'] = 10;
			$value['ddsj'] = $sysj;
			if ($rows['jqlx'] == 5) {
				$rwid = questsController::OnFinish ( $roleInfo, "'jxj','zljxj'" );
			} else {
				$rwid = questsController::OnFinish ( $roleInfo, "'jxj'" );
			}
			if (! empty ( $rwid )) {
				if (! empty ( $rwid )) {
					$value ['rwid'] = $rwid;
				}
			}
			// 急行军加速活动
			hdProcess::run(array('fight_fightJXJ'), $roleInfo, 1);
			return $value;
		}
	}
	
	//处理逐鹿资源站
	public static function zlzyz() {
		global $mc, $db, $common, $_SGLOBAL,$G_PlayerMgr;
		$nowTime = $_SGLOBAL['timestamp'];
		$maxTime = $_SGLOBAL['timestamp'] + 1800;
		$sql = "SELECT * FROM ".$common->tname('jq')." WHERE (ddsj <= $nowTime || ddsj > $maxTime) && jqlx = 5";
		$result = $db->query($sql);
		while ($rows = $db->fetch_array($result)) {	
			$G_PlayerMgr->removeplayer($rows['wfpid']);
			if ($rows['ddsj'] > $maxTime) {
				$db->query("update ".$common->tname('jq')." set ddsj = $maxTime where jq_id = ".$rows['jq_id']);
				continue;
			}
			zydModel::zszlzyd($rows);
			$G_PlayerMgr->removeplayer($rows['wfpid']);
		}	
	}
	
	//逐鹿结算
	public static function zszlzyd($jqInfo) {
		global $mc, $db, $common, $_SGLOBAL, $zyd_lang;
		$sysTime = $_SGLOBAL['timestamp'];
		$nowTime = $jqInfo['ddsj'];
		$zydid = $jqInfo['zyid'];
		$playersid = $jqInfo['wfpid'];
		$xhsj = $jqInfo['xhsj'];
		$gid = $jqInfo['wfgid'];
		$jq_id = $jqInfo['jq_id'];			
		$zydInfo = zydModel::hqzlzyd($jqInfo['zyid']);
		$mc->set(MC.$playersid.'_jq',1,0,1800);
		$m = zyzkssj($zydid);
		$zlsl = $mc->get(MC.$zydid."_zlsl"); //获取多少人逐鹿	
		$newsl = $zlsl - 1;
		if ($newsl < 0) {
			$newsl = 0;
		}				
		$mc->set(MC.$zydid."_zlsl",$newsl,0,1800);		
		/*switch ($zydid) {
			case 1:				
				$endTime = intval($m.'5959');
			break;
			case 2:
				$endTime = 135959;
			break;
			case 3:
				$endTime = 175959;
			break;
			case 4:
				$endTime = 185959;
			break;
			case 5:
				$endTime = 205959;
			break;
			case 6:
				$endTime = 215959;
			break;
			default:
				$endTime = 225959;
			break;																		
		}*/
		$endTime = intval($m.'5959');
		$ddhis = date('His',$jqInfo['ddsj']);
		if ($ddhis > $endTime) {
			$ddsj = $nowTime + 1800;								
			$updateSql = "UPDATE ".$common->tname('jq')." SET ddsj = $ddsj,dfpid = 0,dfmc = '',jqlx = 7,jlsj = '',createTime = '".$sysTime."' WHERE jq_id = $jq_id LIMIT 1";
			$db->query($updateSql);	
			$updatehcjlSql = "UPDATE ".$common->tname('playergeneral')." SET zydid = '0',act = '4',gohomeTime = '$ddsj' WHERE intID = '$gid' LIMIT 1";
			$db->query($updatehcjlSql);
			$mc->delete(MC.$playersid.'_general');								
			$msg['xllx'] = 57;	
			$msg['X'] = $zydInfo['zlwjmc'];
			//$msg['R'] = zydmc($zydInfo['yd_type']);		
			$msg['R'] = zlzydmc($zydInfo['yd_id']);			
			//$mesageDen = addcslashes(json_encode($msg),'\\');				
			$messageInfoDen = array('playersid'=>0,'jsid'=>$playersid,'toplayersid'=>$playersid,'subject'=>'','message'=>$msg,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
			$messageInfoDen['genre'] = $msg['xllx'];
			lettersModel::addMessage($messageInfoDen);		
			$messageInfoDen = null;
			$mesageDen = null;
			$msg = null;	
			return false;			
		}	
		//如遇到自己打自己则直接返回
		if ($playersid == $zydInfo['zlpid']) {
			$hcsj = $nowTime + $jqInfo['xhsj'];			
			$updatehcSql = "UPDATE ".$common->tname('jq')." SET ddsj = '$hcsj',dfpid = 0,dfmc = '',jqlx = '7',createTime = '$nowTime',jlsj = '' WHERE jq_id = '$jq_id' LIMIT 1";
			$db->query($updatehcSql);	
			$updatehcjlSql = "UPDATE ".$common->tname('playergeneral')." SET zydid = '0',act = '4',gohomeTime = '$hcsj' WHERE intID = '$gid' LIMIT 1";
			$db->query($updatehcjlSql);
			$mc->delete(MC.$playersid.'_general');
			$msg['xllx'] = 50;
			$msg['X'] = $jqInfo['wfgname'];
			$msg['L'] = $zydInfo['dj'];	
			$msg['R'] = zydmc($zydInfo['yd_type']);					
			//$mesageDen = addcslashes(json_encode($msg),'\\');				
			$messageInfoDen = array('playersid'=>0,'jsid'=>$playersid,'toplayersid'=>$playersid,'subject'=>'','message'=>$msg,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
			$messageInfoDen['genre'] = 50;
			lettersModel::addMessage($messageInfoDen);		
			$messageInfoDen = null;
			$mesageDen = null;
			$msg = null;										
			return false;
		}
		// 群雄逐鹿战斗活动
		hdProcess::run(array('fight_fightZlz'), array('playersid'=>$playersid), 1);

		$zlpid = $zydInfo['zlpid'];
		if ($zlpid == 0) {
			$defend_data = array(array(
				'intID'=>1,
				'mobility'=>actModel::getMobility($zydInfo['zy'],$zydInfo['yd_level']),
				'general_sort'=>1,
				'professional'=>$zydInfo['zy'],
				'physical_value'=>$zydInfo['tl'],
				'attack_value'=>$zydInfo['gj'],
				'defense_value'=>$zydInfo['fy'],
				'agility_value'=>$zydInfo['mj'],
				'understanding_value'=>10,
				'general_level'=>$zydInfo['yd_level'],
				'general_name'=>$zydInfo['xm'],
				'avatar'=>$zydInfo['avatar']
			));	
			$value['d_level'] = $zydInfo['yd_level'];
			$value['d_sex'] = $zydInfo['sex'];
			$value['fsxm'] = $zydInfo['xm'];	
			$sf_jwdj = 0;	
			$isGw = 1;	
			$incomeExp = ceil(pow($zydInfo['yd_level'],1.6) * 0.36 + $zydInfo['yd_level'] * 2 + 19);
			$isGw = 1;
		} else {
			$isGw = 0;
			$dgeneral = cityModel::getGeneralData($zydInfo['zlpid'],false,$zydInfo['zlwjid'],false);
			if (empty($dgeneral)) {
				$zydInfo['zlpid'] = 0;
				$zydInfo['zlpname'] = '';
				$zydInfo['zlwjid'] = 0;
				$zydInfo['zlwjmc'] = '';
				$zydInfo['zlsj'] = 0;	
				$mc->set(MC.$zydid.'_zlzyd',$zydInfo,0,0);			
				return false;
			}				
			$dvalue = array();			
			$dgeneral[0]['general_sort'] = 1;							
			$dvalue[] = $dgeneral[0];						
			$defend_data = $dvalue;	
			$defendRoleInfo['playersid'] = $zydInfo['zlpid'];
			$res1 = roleModel::getRoleInfo($defendRoleInfo,false);
			if (empty($res1)) {
				$zydInfo['zlpid'] = 0;
				$zydInfo['zlpname'] = '';
				$zydInfo['zlwjid'] = 0;
				$zydInfo['zlwjmc'] = '';
				$zydInfo['zlsj'] = 0;	
				$mc->set(MC.$zydid.'_zlzyd',$zydInfo,0,0);						
				return false;
			}
			$sf_jwdj = $defendRoleInfo['mg_level'];
			$value['d_level'] = $defendRoleInfo['player_level'];
			$incomeExp = ceil(pow($value['d_level'],1.6) * 0.36 + $value['d_level'] * 2 + 19);
			$value['d_sex'] = $defendRoleInfo['sex'];
			$value['fsxm'] = stripcslashes($defendRoleInfo['nickname']);			
			//评分	
			/*$dfpfArr = fightModel::getPf($defend_data, $defendRoleInfo);
			$value['gkpf'] = $dfpfArr['wjzdlpf'];
			$value['djpfzb'] = $dfpfArr['djpf'];
			$value['tfpfzb'] = $dfpfArr['tfpf'];
			$value['zbpfzb'] = $dfpfArr['zbpf'];
			$value['jwpfzb'] = $dfpfArr['jwpf'];
			$value['jnpfzb'] = $dfpfArr['jnpf'];
			$value['tgsxpf'] = $dfpfArr['wjzdlpf'];*/														
		}	
		$general = cityModel::getGeneralData($playersid,false,$gid,false);
    	$avalue = array();
    	if (empty($general)) {
    		$db->query("DELETE FROM ".$common->tname('jq')." WHERE jq_id = $jq_id LIMIT 1");
    		return false;
    	}
		if ($general[0]['general_life'] > 0) {
			$general[0]['general_sort'] = 1;							
			$general[0]['command_soldier'] = $general[0]['general_life'];	
			$avalue[] = $general[0];
		} else {
			$db->query("DELETE FROM ".$common->tname('jq')." WHERE jq_id = $jq_id LIMIT 1");
			return false; 
		}
		$attack_data = $avalue;						
		$roleInfo['playersid'] = $playersid;
		$res2 = roleModel::getRoleInfo($roleInfo,false);
		if (empty($res2)) {
			$db->query("DELETE FROM ".$common->tname('jq')." WHERE jq_id = $jq_id LIMIT 1");
			return false;
		}	
		$value['a_level'] = $roleInfo['player_level'];
		$value['a_sex'] = $roleInfo['sex'];
		$value['wjxm'] = stripcslashes($roleInfo['nickname']);					
		/*$pfArr = fightModel::getPf($attack_data, $roleInfo);
		$value['wjzdlpf'] = $pfArr['wjzdlpf'];
		$value['djpf'] = $pfArr['djpf'];
		$value['tfpf'] = $pfArr['tfpf'];
		$value['zbpf'] = $pfArr['zbpf'];
		$value['jwpf'] = $pfArr['jwpf'];
		$value['jnpf'] = $pfArr['jnpf'];*/
		$gf_jwdj = $roleInfo['mg_level'];
		$result = actModel::fight($attack_data,$defend_data,$isGw,'','',0,$gf_jwdj,$sf_jwdj);                               //战斗返回
		$value['begin'] = $result['begin'];                                               //开始士兵情形
		$round = $result['round'];                                               //战斗回合	
		$newround = heroCommon::arr_foreach($round);
		$value['round'] = $newround;
		$round = null;	
		$newround = null;			
		$soldierNum = 0;                                                                  //初始化防守士兵数量
		$attack_result = $result['result'];
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
			}
		}
		$attack_soldier_left = array_sum($attack_soldier);   //攻击方所剩人数	
		//更新攻击方将领数据	   							
		if ($attack_soldier_left <= 0) {					
	   		$ddsj = $nowTime + $xhsj;
			$updateSql = "UPDATE ".$common->tname('jq')." SET ddsj = '$ddsj',dfpid = 0,dfmc = '',jqlx = 7,createTime='$nowTime' WHERE jq_id = '$jq_id' LIMIT 1";				
			//echo $updateSql;
			$db->query($updateSql);
			$act = 4;
			$jqid = $jq_id;					
			for ($k = 0; $k < 1; $k++) {
				$id = $attack_data[$k]['intID'];
				$left_command_soldier = $attackGeneralLeftSoldier[$id];                             //剩余士兵数量
				$sssm = $attack_data[$k]['general_life'];                                           //武将生命
				$wjmc = $attack_data[$k]['general_name'];                                           //武将名称
				$updateGeneral['general_life'] = $left_command_soldier;    
				$attack_data[$k]['general_life'] = $left_command_soldier;
				$attack_data[$k]['command_soldier'] = $left_command_soldier;	
				$attack_data[$k]['gohomeTime'] = $updateGeneral['gohomeTime'] = $ddsj;	
				$attack_data[$k]['act']= $updateGeneral['act'] = $act;		
				$attack_data[$k]['jqid']= $updateGeneral['jqid'] = $jqid;			
				$attack_data[$k]['zydid']= $updateGeneral['zydid'] = $zydid;			
				//自动补血
				zydModel::ksbx($roleInfo,$attack_data[$k],$id,$updateGeneral);
   			}
			$msg['xllx'] = 35;
			$msg['X'] = $general[0]['general_name'];				
			$msg['L'] = $zydInfo['dj'];
			$msg['R'] = zydmc($zydInfo['yd_type']);								
            //$mesageDen = addcslashes(json_encode($msg),'\\');				
            $messageInfoDen = array('playersid'=>0,'jsid'=>$roleInfo['playersid'],'toplayersid'=>$playersid,'subject'=>'','message'=>$msg,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
			$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
			$messageInfoDen['genre'] = 35;
			lettersModel::addMessage($messageInfoDen);	
			$messageInfoDen = null;
			$mesageDen = null;
			$msg = null;		
			if ($isGw == 0) {
				$msg['xllx'] = 37;
				$msg['X'] = $zydInfo['zlwjmc'];
				$msg['L'] = $zydInfo['dj'];
				$msg['R'] = zydmc($zydInfo['yd_type']);	
				$msg['Z'] = $roleInfo['nickname'];	
				$msg['ZID'] = $roleInfo['playersid'];	
				$msg['A'] = $attack_data[0]['general_name'];
				$msg['AID'] = $gid;											
	            //$mesageDen = addcslashes(json_encode($msg),'\\');				
	            $messageInfoDen = array('playersid'=>0,'jsid'=>$roleInfo['playersid'],'toplayersid'=>$zydInfo['zlpid'],'subject'=>'','message'=>$msg,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
				$messageInfoDen['genre'] = 37;
	            $messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
				lettersModel::addMessage($messageInfoDen);		
			}			   							
			return 2;					       
		} else {
			//echo 'hello';
			//添加系统广播
			//lettersModel::setSysPublicNoice(array($roleInfo['nickname'],$zydInfo['zlmc']),1);
			//武将升级			
			$tf = $attack_data[0]['understanding_value'] - $attack_data[0]['llcs']; //武将天赋
			$jbsx = wjsjsx($tf);  //武将级别上限		
			$current_general_level = $attack_data[0]['general_level'];		
			$needJy = cityModel::getGeneralUpgradeExp($current_general_level);                //下级所需经验
			$current_experience = $attack_data[0]['current_experience'];	
			$mc->set(MC.'zlz_'.$zydid.'_'.$gid,time(),0,3600);  //记录占领该逐鹿点的时间			
			//$incomeExp = 100;
			$hqjy = 0;
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
							$attack_data[0]['current_experience'] = $updateGeneral['current_experience'] = $left_experience = $newExp;
							$hqjy = $g_hqjy;
						} else {
							$attack_data[0]['current_experience'] = $updateGeneral['current_experience'] = $left_experience = $needJy;
							$hqjy = $newExp - $needJy;
						}							
					} else {
						$left_experience = $current_experience;
						$hqjy = 0;
					}				 	 	
				 } else {				 	 	
					$generalUpgrade = fightModel::upgradeRole($current_general_level,$newExp,2);
					$left_experience = $generalUpgrade['left'];
					$new_level = $generalUpgrade['level'];
					if ($new_level > $upLevel) {
						$new_level = $upLevel;
						$left_experience = 0;
					}
					$attack_data[0]['current_experience'] = $updateGeneral['current_experience'] = $left_experience;	
					$upgrade = 0;
					if (intval($new_level) > intval($current_general_level) && intval($current_general_level) > 0) {
						$attack_data[0]['general_level'] = $updateGeneral['general_level'] = $new_level;
					}
					$xhjy = array();
					for ($k = 0; $k < ($new_level - $current_general_level); $k++) {
						$xhjy[] = cityModel::getGeneralUpgradeExp($current_general_level + $k);
					}	
					if (!empty($xhjy)) {
						$totalExp =  array_sum($xhjy);        //所需要的总经验
					    $hqjy = $totalExp - $current_experience + $left_experience;
					} else {
						$hqjy = $g_hqjy;
					}									 	 			 	 	
				 }
				 unset($generalUpgrade);	//清空数值	
				 $current_general_level = '';
				 $get_experience = ''; 
				 $current_experience = '';		                                       		                 	
			} else {
				$hqjy = 0;
			}
			$msg['xllx'] = 34;
			$msg['X'] = $general[0]['general_name'];
			$msg['L'] = $zydInfo['dj'];
			$msg['R'] = zydmc($zydInfo['yd_type']);
			$msg['E'] = $hqjy.$zyd_lang['zxzl_1'];					
            //$mesageDen = addcslashes(json_encode($msg),'\\');				
            $messageInfoDen = array('playersid'=>0,'jsid'=>$roleInfo['playersid'],'toplayersid'=>$playersid,'subject'=>'','message'=>$msg,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
			$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
			$messageInfoDen['genre'] = 34;
			lettersModel::addMessage($messageInfoDen);		
			$messageInfoDen = null;
			$mesageDen = null;
			$msg = null;				
			//武将升级结束				
			for ($k = 0; $k < 1; $k++) {
				$id = $attack_data[$k]['intID'];
				$left_command_soldier = $attackGeneralLeftSoldier[$id];                             //剩余士兵数量
				$sssm = $attack_data[$k]['general_life'];                                           //武将生命
				$wjmc = $attack_data[$k]['general_name'];                                           //武将名称
				$updateGeneral['general_life'] = $left_command_soldier;    
				$attack_data[$k]['general_life'] = $left_command_soldier;
				$attack_data[$k]['command_soldier'] = $left_command_soldier;	
				$attack_data[$k]['act']= $updateGeneral['act'] = 1;		
				//自动补血
				zydModel::ksbx($roleInfo,$attack_data[$k],$id,$updateGeneral);
   			}	
			$zydRoleInfo['playersid'] = $jqInfo['wfpid'];
			$reszyd = roleModel::getRoleInfo($zydRoleInfo,false);		
			//echo 'hello1';			
			if (empty($reszyd)) {
				return false;
			}	
			//echo 'hello2';				
			/*结算结束*/	
			$jlsj = null;
			if ($zlpid != 0) {	
				$jlsj = '';				
				$mc->set(MC.$zydInfo['zlpid'].'_jq',1,0,1800);		
				$zlsj = $mc->get(MC.'zlz_'.$zydid.'_'.$zydInfo['zlwjid']);	
				$mc->delete(MC.'zlz_'.$zydid.'_'.$zydInfo['zlwjid']);
				$zlzsj = time() - $zlsj;   //计算此人占领的总时长
				if ($zlzsj < 0) {
					$zlzsj = 0;
				}
				if ($zlzsj > 0) {
					$common->inserttable('zyd_zlsjlog',array('zydid'=>$zydid,'zlpid'=>$zlpid,'zlpname'=>$zydInfo['zlpname'],'zlsj'=>$zlzsj,'zlrq'=>date('Y-m-d',time())));
				}	
				//插入探报		
				$msg['X'] = $zydInfo['zlwjmc'];
				$msg['L'] = $zydInfo['dj'];	
				$msg['R'] = zydmc($zydInfo['yd_type']);			
				$msg['xllx'] = 36;			
				$msg['Z'] = $roleInfo['nickname'];	
				$msg['ZID'] = $roleInfo['playersid'];	
				$msg['A'] = $attack_data[0]['general_name'];
				$msg['AID'] = $gid;													
				//$mesageDen = addcslashes(json_encode($msg),'\\');				
				$messageInfoDen = array('playersid'=>0,'jsid'=>$zydInfo['zlpid'],'toplayersid'=>$zydInfo['zlpid'],'subject'=>'','message'=>$msg,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
				$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
				$messageInfoDen['genre'] = $msg['xllx'];
				lettersModel::addMessage($messageInfoDen);		
				$messageInfoDen = null;
				$mesageDen = null;
				$msg = null;	
				$sqljq = "select * from ".$common->tname('jq')." where wfgid = '".$zydInfo['zlwjid']."' && zyid = '$zydid' LIMIT 1";
				$resjq = $db->query($sqljq);
				$rowsjq = $db->fetch_array($resjq);
				$ddsj = $nowTime + $rowsjq['xhsj'];					
				//执行回城操作					
				$updatehcSql = "UPDATE ".$common->tname('jq')." SET ddsj = '$ddsj',dfpid = 0,dfmc = '',jqlx = 7,createTime = '$nowTime' WHERE wfgid = '".$zydInfo['zlwjid']."' && zyid = '$zydid' LIMIT 1";
				$db->query($updatehcSql);	
				$updatehcjlSql = "UPDATE ".$common->tname('playergeneral')." SET zydid = '0',act = '4',gohomeTime = '$ddsj' WHERE intID = '".$zydInfo['zlwjid']."' LIMIT 1";
				$db->query($updatehcjlSql);
				$mc->delete(MC.$zydInfo['zlpid'].'_general');		
			}	
			$zydInfo['zlpid'] = $playersid;
			$zydInfo['zlpname'] = $roleInfo['nickname'];
			$zydInfo['zlwjid'] = $gid;
			$zydInfo['zlwjmc'] = $wjmc;
			$zydInfo['zlsj'] = $nowTime;
			$mc->set(MC.$zydid.'_zlzyd',$zydInfo,0,0);		
			/*结算获取的资源*/
			$jksj = 3600;     //固定一小时
			$jlsj = array();
			$zyzsInfo = zydzy($zydInfo['yd_type'],$zydInfo['dj'],$jksj,'');
			if (isset($zyzsInfo['tq'])) {
				$ldzyAmount = floor($zyzsInfo['tq']); //该次掠夺资源数量
				if ($ldzyAmount > 0) {
					$jlsj = array('tq'=>$ldzyAmount);
					$jlmc = $zyd_lang['scyd_1'];
				}
			} elseif (isset($zyzsInfo['jl'])) {					
				$ldzyAmount = floor($zyzsInfo['jl']); //该次掠夺资源数量
				if ($ldzyAmount > 0) {
					$jlsj = array('jl'=>$ldzyAmount);
					$jlmc = $zyd_lang['scyd_2'];
				}					
			} else {
				if ($zyzsInfo != 0) {
					foreach ($zyzsInfo['dj'] as $key => $zyzsInfovalue) {
						$ldzyAmount = floor($zyzsInfovalue); //该次掠夺资源数量
						$djInfo = toolsModel::getItemInfo($key);
						$jlsj = array('dj'=>array($key => $ldzyAmount));	
						$jlmc = $djInfo['Name'];
					}
				}				
			}
			if (!empty($jlsj)) {
				$jlsj = json_encode($jlsj);
			} else {
				$jlsj = '';
			}			
			$updateZyd = "UPDATE ".$common->tname('zyd_qxzl')." SET zlpid = $playersid,zlpname='".mysql_escape_string($roleInfo['nickname'])."',zlwjid='$gid',zlwjmc = '".$general[0]['general_name']."',zlsj='".$jqInfo['ddsj']."' WHERE yd_id = '$zydid' LIMIT 1";
			$db->query($updateZyd);								
			$updateSql = "UPDATE ".$common->tname('jq')." SET ddsj = 0,dfpid = 0,dfmc = '',jqlx = 6,jlsj = '$jlsj',createTime = '".$jqInfo['ddsj']."' WHERE jq_id = '$jq_id' LIMIT 1";
			//echo $updateSql;
			$db->query($updateSql);							
			return 1;
		}							
	}

    //检查大奖是否结算
    public static function checkdaj($zydid,$nowTime) {
    	global $common,$db,$mc;
    	$ymd = date('Y-m-d',$nowTime);
    	if (!$checkNum = $mc->get(MC.'djInfo_'.$ymd.'_'.$zydid)) {
	    	$check = "SELECT count(intID) as checkNum FROM ".$common->tname('zyd_zldj_his')." WHERE zydid = $zydid && dateTime = '".$ymd."' LIMIT 1";
	    	$checkRes = $db->query($check);
			$checkNumRows = $db->fetch_array($checkRes);
			$checkNum = $checkNumRows['checkNum'];
			if ($checkNum > 0) {
				$mc->set(MC.'djInfo_'.$ymd.'_'.$zydid,1,0,36000);
			}
    	}
		if ($checkNum > 0) {
			return true;
		} else {
			return false;
		}
    }
	
	//结算每分钟小奖
	public static function jsdj() {
		global $mc, $db, $common, $zyd_lang;
		$nowTime = $time = time();
		$h = date('H',$nowTime);
		$nowMinite = date('i',$nowTime);
		//$rand = rand(1,1000);
  		$m1 = zyzkssj(1);
  		$m2 = zyzkssj(2);
  		$m3 = zyzkssj(3);
  		$m4 = zyzkssj(4);
  		$m5 = zyzkssj(5);
  		$m6 = zyzkssj(6);
  		$m7 = zyzkssj(7);		
		if ( $h >= $m1 && $h < ($m1 + 1)) {
			$zydtype = 1;
			$noticeName = $zyd_lang['jsdj_1'];
		} elseif ($h >= $m2 && $h < ($m2 + 1)) {
			$zydtype = 2;
			$noticeName = $zyd_lang['jsdj_2'];
		} elseif ($h >= $m3 && $h < ($m3 + 1)) {
			$zydtype = 3;
			$noticeName = $zyd_lang['jsdj_3'];	
		} elseif ($h >= $m4 && $h < ($m4 + 1)) {
			$zydtype = 4;
			$noticeName = $zyd_lang['jsdj_4'];
		} elseif ($h >= $m5 && $h < ($m5 + 1)) {
			$zydtype = 5;
			$noticeName = $zyd_lang['jsdj_5'];
		} elseif ($h >= $m6 && $h < ($m6 + 1)) {
			$zydtype = 6;
			$noticeName = $zyd_lang['jsdj_6'];
		} elseif ($h >= $m7 && $h < ($m7 + 1)) {			
			$zydtype = 7;
			$noticeName = $zyd_lang['jsdj_7'];
		} else {
			return 'sleep';
		}
		$s = date('s',$time);
		$sqldl = "SELECT * FROM ".$common->tname('jq')." WHERE zyid = $zydtype && jqlx = 5 && ddsj > $nowTime ORDER BY ddsj ASC LIMIT 6"; 
		$resdl = $db->query($sqldl);
		while ($rowsdl = $db->fetch_array($resdl)) {
			$rowsdl['sysj'] = $rowsdl['ddsj'] - $nowTime;
			$dlinfo[] = $rowsdl;
			$rowsdl['sysj'] = NULL;
		}
		if(isset($dlinfo)) {
			$mc->set(MC.$zydtype.'_gjdl',$dlinfo,0,3);
		}
		//$djbs = $mc->get(MC.'zldj_'.$zydtype);
		$zydInfo = zydModel::hqzlzyd($zydtype);		
		if ($zydInfo['zlpid'] != 0 && $s == 0) {			
			//给予小奖
			$sql = "SELECT * FROM ".$common->tname('jq')." WHERE wfgid = ".$zydInfo['zlwjid']." && zyid = ".$zydtype." LIMIT 1";
			$result = $db->query($sql);
			$jqrows = $db->fetch_array($result);
			if (empty($jqrows)) {
				return 'sleep';
			}
			$jksj = 7200;     //固定二小时
			$jlsj = array();
			$zyzsInfo = zydzy($zydInfo['yd_type'],$zydInfo['dj'],$jksj,'');
			if (isset($zyzsInfo['tq'])) {
				$ldzyAmount = floor($zyzsInfo['tq']); 
				if ($ldzyAmount > 0) {
					$jlsj = array('tq'=>$ldzyAmount);
				}
			} elseif (isset($zyzsInfo['jl'])) {					
				$ldzyAmount = floor($zyzsInfo['jl']); 
				if ($ldzyAmount > 0) {
					$jlsj = array('jl'=>$ldzyAmount);
				}					
			} else {
				if ($zyzsInfo != 0) {
					foreach ($zyzsInfo['dj'] as $key => $zyzsInfovalue) {
						$ldzyAmount = floor($zyzsInfovalue);
						$djInfo = toolsModel::getItemInfo($key);
						$jlsj = array('dj'=>array($key => $ldzyAmount));	
					}
				}				
			}					
			$old_jlsj = array();
			if (!empty($jqrows['jlsj'])) {
				$old_jlsj = json_decode($jqrows['jlsj'],true);
			}
			if (!empty($jlsj) && !empty($old_jlsj))	{
				foreach ($jlsj as $jlsjKey => $jlsjValue) {
					$newKey = $jlsjKey;
					$newValue = $jlsjValue;
				}
				foreach ($old_jlsj as $old_jlsjKey => $old_jlsjValue) {
					$oldKey = $old_jlsjKey;
					$oldValue = $old_jlsjValue;
				}
				if ($newKey == $oldKey) {
					if ($newKey != 'dj') {
						$newjlsj = array($newKey=>$jlsjValue + $old_jlsjValue);
					} else {
						$olddjinfo = $old_jlsj[$oldKey];
						foreach ($olddjinfo as $olddjinfoKey => $olddjinfoValue) {
							$olddjKeyid = $olddjinfoKey;
							$olddjnum = $olddjinfoValue;
						}
						$newdjinfo = $jlsj['dj'];
						foreach ($newdjinfo as $newdjinfoKey => $newdjinfoValue) {
							$newdjKeyid = $newdjinfoKey;
							$newdjnum = $newdjinfoValue;									
						}
						if ($olddjKeyid == $newdjKeyid) {
							$newjlsj = array('dj'=>array($olddjKeyid=>$olddjnum + $newdjnum));
						} else {
							$newjlsj = array('dj'=>$olddjinfo + $newdjinfo);
						}								
					}
				} else {
					$newjlsj = $jlsj + $old_jlsj;
				}
				$jlsj = json_encode($newjlsj);
			} elseif (empty($jlsj) && empty($old_jlsj))	{
				$jlsj = '';
			} elseif (empty($jlsj) && !empty($old_jlsj)) {
				$jlsj = $jqrows['jlsj'];
			} else {
				$jlsj = json_encode($jlsj);
			}	
			if (!empty($jlsj)) {
				$db->query("UPDATE ".$common->tname('jq')." SET jlsj = '$jlsj' WHERE jq_id = ".$jqrows['jq_id']." LIMIT 1");
				//添加系统广播
				lettersModel::setSysPublicNoice(array($noticeName,date('H:i',$nowTime),$zydInfo['zlpname']),1);					
			}																			
			return 'stop';
		} else {
			$djbs = null;
			$zydInfo = null;
			$min = null;
			$s = null;
			$time = null;					
			return 'sleep';
		}
	}
	//开启大奖	
	public static function kqdj($zydtype,$zydj) {
		global $mc, $db, $common, $G_PlayerMgr, $zyd_lang;
		$nowTime = $time = time();
		if ($zydtype == 1) {
			$itemID = 18508;
			$noticeName = $zyd_lang['jsdj_1'];
		} elseif ($zydtype == 2) {
			$itemID = 18509;	
			$noticeName = $zyd_lang['jsdj_2'];
		} elseif ($zydtype == 3) {
			$itemID = 18510;
			$noticeName = $zyd_lang['jsdj_3'];	
		} elseif ($zydtype == 4) {
			$itemID = 18511;
			$noticeName = $zyd_lang['jsdj_4'];
		} elseif ($zydtype == 5) {
			$itemID = 18512;
			$noticeName = $zyd_lang['jsdj_5'];
		} elseif ($zydtype == 6) {
			$itemID = 18513;
			$noticeName = $zyd_lang['jsdj_6'];
		} elseif ($zydtype == 7) {
			$itemID = 18514;
			$noticeName = $zyd_lang['jsdj_7'];
		} else {
			return 'sleep';
		}
		$sql = "SELECT * FROM ".$common->tname('zyd_zlsjlog')." WHERE zydid = $zydtype ORDER BY rand() LIMIT 1";
		$result = $db->query($sql);
		$rows = $db->fetch_array($result);
		if (empty($rows)) {
			return false;
		}	
		$playersid = $rows['zlpid'];	
		$G_PlayerMgr->removeplayer($playersid);
		$roleInfo['playersid'] = $playersid;
		$res = roleModel::getRoleInfo($roleInfo);
		if (empty($res)) {
			return 'sleep';
		}
		$toolsRes = toolsModel::addPlayersItem($roleInfo,$itemID,1);	
		if ($toolsRes['status'] != 0) {
			//把道具以消息的方式发送;
			$toolsInfo = toolsModel::getItemInfo($itemID);
			$djmc = $toolsInfo['Name'];
			$message = array('playersid'=>$roleInfo['playersid'],'toplayersid'=>$roleInfo['playersid'],'subject'=>$zyd_lang['kqdj_1'],'genre'=>20,'tradeid'=>0,'interaction'=>0,'is_passive'=>0,'type'=>1,'request'=>addcslashes(json_encode(array('tq'=>0,'yp'=>0,'yb'=>0,'jl'=>0,'items'=>array(array('id'=>$itemID,'mc'=>$djmc,'num'=>1)))), '\\'),'message'=>array('xjnr'=>$zyd_lang['zldj'].'”'.$noticeName.'“！'));
			lettersModel::addMessage($message);
			$djmc = null;
			$message = null;						
		} else {
			$mc->set(MC.$roleInfo['playersid'].'_xsbb',1,0,3600);
		}					
		//$mc->set(MC.'zldj_'.$zydtype,1,0,1800);
		zydModel::tjyxb($zydtype,$playersid,$time,1);	
		$cjInfo['zldj'] = 1;
		achievementsModel::check_achieve($playersid,$cjInfo,array('zldj'));
		$mc->delete(MC.$zydtype."_xybsj");
		//添加系统广播
		lettersModel::setSysPublicNoice(array($roleInfo['nickname'],$noticeName,$zydj),2);		
		$djbs = null;
		$zydInfo = null;
		$min = null;	
		$res = null;			
		$s = null;
		$time = null;			
		$roleInfo = null;																	
		return 'stop';
	} 
	
	//开启得分王	
	public static function kqdfw($zydtype) {
		global $mc, $db, $common, $G_PlayerMgr,$zyd_lang;
		$nowTime = $time = time();
  		$yp = 0;
  		$tq = 0;		
		if ($zydtype == 1) {
			$yp = 50;
			$noticeName = $zyd_lang['jsdj_1'];
		} elseif ($zydtype == 2) {
			$yp = 100;
			$itemID = 18509;	
			$noticeName = $zyd_lang['jsdj_2'];
		} elseif ($zydtype == 3) {
			$yp = 200;
			$noticeName = $zyd_lang['jsdj_3'];	
		} elseif ($zydtype == 4) {
			$tq = 50000;
			$noticeName = $zyd_lang['jsdj_4'];
		} elseif ($zydtype == 5) {
			$tq = 100000;
			$noticeName = $zyd_lang['jsdj_5'];
		} elseif ($zydtype == 6) {
			$tq = 150000;
			$noticeName = $zyd_lang['jsdj_6'];
		} elseif ($zydtype == 7) {
			$tq = 200000;
			$noticeName = $zyd_lang['jsdj_7'];
		} else {
			return 'sleep';
		}
		$sql = "SELECT * FROM ".$common->tname('zyd_zlsjlog')." WHERE zydid = $zydtype ORDER BY zlsj DESC LIMIT 1";
		$result = $db->query($sql);
		$rows = $db->fetch_array($result);
		if (empty($rows)) {
			return false;
		}	
		$playersid = $rows['zlpid'];	
		//$G_PlayerMgr->removeplayer($playersid);
		//$roleInfo['playersid'] = $playersid;
		//$res = roleModel::getRoleInfo($roleInfo);
		/*if (empty($res)) {
			return 'sleep';
		}*/
		if ($tq != 0) {
			$db->query("UPDATE ".$common->tname('player')." SET coins = coins + $tq WHERE playersid = $playersid LIMIT 1");
			$jl = $zyd_lang['scyd_1'];
			$sl = $tq;
		} else {
			$db->query("UPDATE ".$common->tname('player')." SET silver = silver + $yp WHERE playersid = $playersid LIMIT 1");
			$jl = $zyd_lang['scyd_3'];
			$sl = $yp;
		}
		$mc->delete(MC.$playersid);
		$db->query("DELETE FROM ".$common->tname('zyd_zlsjlog')." WHERE zydid = $zydtype");  //清除该逐鹿点所有占领玩家数据
		zydModel::tjyxb($zydtype,$playersid,$time,0);
		$mc->delete(MC.$zydtype."_dfwsj");
		//添加系统广播
		lettersModel::setSysPublicNoice(array($rows['zlpname'],$noticeName),6);		
		$msg['xllx'] = 61;
		$msg['X'] = $noticeName;
		$msg['J'] = $jl.' X '.$sl;
        $messageInfoDen = array('playersid'=>0,'jsid'=>$playersid,'toplayersid'=>$playersid,'subject'=>'','message'=>$msg,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
		$messageInfoDen['genre'] = 61;
		lettersModel::addMessage($messageInfoDen);																					
		return 'stop';
	} 	
	
	
	//插入英雄榜数据
	public static function tjyxb($zydid,$playersid,$time,$djlx) {
		global $common,$mc;
		$insert['zydid'] = $zydid;
		$insert['playersid'] = $playersid;
		$insert['hjsj'] = $time;
		$insert['dateTime'] = date('Y-m-d',$time);
		$insert['djlx'] = $djlx;
		$common->inserttable('zyd_zldj_his',$insert);		
		unset($insert); 
	}
	
	//获取英雄榜
	public static function yxb($zydid) {
		global $common, $db, $_SGLOBAL, $mc;
		$nowTime = $_SGLOBAL['timestamp'];
		$yxb = $mc->get(MC.$zydid."_xybsj");
		if (empty($yxb)) {	
			$sql = "SELECT a.* FROM ".$common->tname('player')." a, ".$common->tname('zyd_zldj_his')." b WHERE b.zydid = $zydid && a.playersid = b.playersid && b.djlx = 1 ORDER BY b.intID DESC LIMIT 6";
			$result = $db->query($sql);
			$yxb = array();
			while ($rows = $db->fetch_array($result)) {
				$jsid = intval($rows['playersid']);
				$nc = $rows['nickname'];
				$dj = intval($rows['player_level']);
				$sex = intval($rows['sex']);
				$vip = $rows['vip'];
				$yxb[] = array('jsid'=>$jsid,'mc'=>$nc,'dj'=>$dj,'sex'=>$sex,'vip'=>$vip);			
			}
			$mc->set(MC.$zydid."_xybsj",$yxb,0,0);
		}
		$dfw = $mc->get(MC.$zydid."_dfwsj");
		if (empty($dfw)) {
			$sql = $result = $jsid = $nc = $sex = $rows = $vip1 = $vip2 = $vip2 = null;
			$sql = "SELECT a.* FROM ".$common->tname('player')." a, ".$common->tname('zyd_zldj_his')." b WHERE b.zydid = $zydid && a.playersid = b.playersid && b.djlx = 0 ORDER BY b.intID DESC LIMIT 6";
			$result = $db->query($sql);
			$dfw = array();
			while ($rows = $db->fetch_array($result)) {
				$jsid = intval($rows['playersid']);
				$nc = $rows['nickname'];
				$dj = intval($rows['player_level']);
				$sex = intval($rows['sex']);
				$vip = $rows['vip'];
				$dfw[] = array('jsid'=>$jsid,'mc'=>$nc,'dj'=>$dj,'sex'=>$sex,'vip'=>$vip);					
			}
			$mc->set(MC.$zydid."_dfwsj",$dfw,0,0);	
		}
		return array('status'=>0,'list'=>$yxb,'dfwlist'=>$dfw);		
	}
	
	//时间到踢走逐鹿战占领玩家
	public static function qczlwj($zydid) {
		global $common, $db, $mc,$G_PlayerMgr;
		$sysTime = time();
		$ymd = strtotime(date('Y-m-d',$sysTime));
		$m = zyzkssj($zydid);
		/*switch ($zydid) {
			case 1:
				$nowTime = $ymd + 12 * 3600 + 59 * 60 + 59;
			break;
			case 2:
				$nowTime = $ymd + 13 * 3600 + 59 * 60 + 59;
			break;		
			case 3:
				$nowTime = $ymd + 17 * 3600 + 59 * 60 + 59;
			break;	
			case 4:
				$nowTime = $ymd + 18 * 3600 + 59 * 60 + 59;
			break;		
			case 5:
				$nowTime = $ymd + 19 * 3600 + 59 * 60 + 59;
			break;		
			case 6:
				$nowTime = $ymd + 20 * 3600 + 59 * 60 + 59;
			break;		
			case 7:
				$nowTime = $ymd + 21 * 3600 + 59 * 60 + 59;
			break;																				
		}*/
		$nowTime = $ymd + $m * 3600 + 59 * 60 + 59;		
		if ($sysTime >= $nowTime) {
			$chdj = zydModel::checkdaj($zydid,$sysTime);
			if ($chdj == true) {
				return 'stop';
			}				
			$zydInfo = zydModel::hqzlzyd($zydid);
			$zlpid = $zydInfo['zlpid'];
			$zlwjid = $zydInfo['zlwjid'];
			$checkjq = "SELECT count(jq_id) as jqsl FROM ".$common->tname('jq')." WHERE zyid = $zydid && jqlx = 5 && ddsj < $nowTime";
			$checkRes = $db->query($checkjq);
			$checkRows = $db->fetch_array($checkRes);		
			if ($checkRows['jqsl'] == 0) {	
				$itemSql = " && ((jqlx = 5 && ddsj >= $nowTime) || jqlx = 6)";
				if ($zlpid != 0) {
					$zlsj = $mc->get(MC.'zlz_'.$zydid.'_'.$zydInfo['zlwjid']);	
					$mc->delete(MC.'zlz_'.$zydid.'_'.$zydInfo['zlwjid']);
					$zlzsj = $sysTime - $zlsj;   //计算此人占领的总时长
					if ($zlzsj < 0) {
						$zlzsj = 0;
					}
					if ($zlzsj > 0) {
						$common->inserttable('zyd_zlsjlog',array('zydid'=>$zydid,'zlpid'=>$zlpid,'zlpname'=>$zydInfo['zlpname'],'zlsj'=>$zlzsj,'zlrq'=>date('Y-m-d',time())));
					}
				}									
				zydModel::kqdj($zydid,$zydInfo['dj']); //开启大奖		
				zydModel::kqdfw($zydid);//得分王奖励及清除 该逐鹿点数据	
				$zydInfo['zlpid'] = 0;
				$zydInfo['zlpname'] = '';
				$zydInfo['zlwjid'] = 0;
				$zydInfo['zlwjmc'] = '';
				$zydInfo['zlsj'] = 0;				
				$mc->set(MC.$zydid.'_zlzyd',$zydInfo,0,0);	
				$updateZyd = "UPDATE ".$common->tname('zyd_qxzl')." SET zlpid = 0,zlpname='',zlwjid=0,zlwjmc = '',zlsj=0 WHERE yd_id = '$zydid' LIMIT 1";
				$db->query($updateZyd);									
			} else {
				$itemSql = " && jqlx = 5 && ddsj >= $nowTime";
			}
		} else {
			return false;
		}
		$mc->delete(MC.$zydid."_zlsl");
		$sqljq = "SELECT * FROM ".$common->tname('jq')." WHERE zyid = $zydid".$itemSql;
		$jqres = $db->query($sqljq);		
		while ($rows = $db->fetch_array($jqres)) {
			if ($zlwjid == $rows['wfgid']) {				
				$ddsj = $nowTime + 1800;				
			} else {
				$sysj = 1800 - ($rows['ddsj'] - $nowTime); 
				if ($sysj < 0 || $rows['ddsj'] == 0) {
					$sysj = 0;
				}
				$ddsj = $nowTime + $sysj;
			}
			$gid = $rows['wfgid'];
			$mc->set(MC.$rows['wfpid'].'_jq',1,0,1800);										
			$updateSql = "UPDATE ".$common->tname('jq')." SET dfpid = 0,dfmc = '',jqlx = 7,ddsj = '$ddsj',createTime = '".$sysTime."' WHERE jq_id = ".$rows['jq_id']." LIMIT 1";
			$db->query($updateSql);
			$updatehcjlSql = "UPDATE ".$common->tname('playergeneral')." SET zydid = '0',act = '4',gohomeTime = '$ddsj' WHERE intID = '$gid' LIMIT 1";
			$db->query($updatehcjlSql);
			$mc->delete(MC.$rows['wfpid'].'_general');
			$msg['xllx'] = 57;	
			$msg['X'] = $rows['wfgname'];
			//$msg['R'] = zydmc($zydInfo['yd_type']);	
			$msg['R'] = zlzydmc($zydInfo['yd_id']);					
			//$mesageDen = addcslashes(json_encode($msg),'\\');				
			$messageInfoDen = array('playersid'=>0,'jsid'=>$zlpid,'toplayersid'=>$rows['wfpid'],'subject'=>'','message'=>$msg,'type'=>1,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    					
			//$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
			$messageInfoDen['genre'] = $msg['xllx'];
			lettersModel::addMessage($messageInfoDen);		
			$messageInfoDen = null;
			$mesageDen = null;
			$msg = null;
			$jlsj = null;
			$ddsj = null;
			$sqljq = null;
			$updateSql = null;
			$gid = null;
		}	
		return true; 		
	}
	//资源点结算记录日志
	public static function zydlog($request,$return,$level=0,$userid=0) {
		global $common,$mc, $_LOG_INFO;	
		$nowTime = time();	
		$letters_trade['request_url'] = $request;
	    $letters_trade['create_time'] = $nowTime;	
	    $letters_trade['level'] = $level;
		$letters_trade['userid'] = $userid;
		$return['status'] = 0;
		$letters_trade['result'] = $return;
		$log_path = $_LOG_INFO['path'] . $_LOG_INFO['prefix'] . date('Y_m_d_H',$nowTime).'_'.intval($nowTime/$_LOG_INFO['split_t']);
		$log_json_value = json_encode($letters_trade);
		$flag = false;
		for($i=1; $i<=5; $i++){
			$logHandle = fopen($log_path."_{$i}", 'a');
			if(flock($logHandle, LOCK_EX|LOCK_NB)){
				$w_long = fwrite($logHandle, $log_json_value."\n");
				if(0 < $w_long){
					$flag = true;
					fclose($logHandle);
					break;
				}
			}
			fclose($logHandle);
		}
		if(!$flag){
			$log['logValue'] = $log_json_value;
			$common->inserttable('bck_log', $log);
		} else{
			syslog(LOG_ALERT, 'game log write log need userid');
		}
	}
	
	//逐鹿点攻击队列
	public static function zlzydgjdl($zydid) {
		global $mc, $zyd_lang;
		$gjdl = $mc->get(MC.$zydid.'_gjdl');
		if (empty($gjdl)) {
			return array('status' => 1021,'message' => $zyd_lang['zlzydgjdl_1']);
		} else {
			foreach ($gjdl as $gjdlValue) {
				$gjdldata[] = array('wjmc'=>$gjdlValue['wfmc'],'jlmc'=>$gjdlValue['wfgname'],'time'=>$gjdlValue['sysj']);
			}
		}
		return array('status' => 0, 'list' => $gjdldata);
	}
	
	//获取战况列表 
	public static function zlzydzkxx($zydid,$playersid) {
		global $common,$db,$zyd_lang;
		$nowTime = time();
		$sql = "SELECT * FROM ".$common->tname('jq')." WHERE wfpid = $playersid && jqlx = 5 ORDER BY ddsj ASC";
		$result = $db->query($sql);
		while ($rows = $db->fetch_array($result)) {
			if ($rows['ddsj'] <= $nowTime) {
				continue;
			}
			$sysj = $rows['ddsj'] - $nowTime;
			$zlinfo[] = array('wjmc' => $rows['wfmc'],'jlmc' => $rows['wfgname'],'time' => $sysj,'gid' => intval($rows['wfgid']));
		}
		if (isset($zlinfo)) {
			return array('status' => 0,'list' => $zlinfo);
		} else {
			return array('status' => 1021, 'message' => $zyd_lang['zlzydzkxx_1']);
		}
	}
	
	
	//发现首次登陆更新资源点数据
	public static function loginSzyd() {
		register_shutdown_function(array('zydModel', "execuzyd"));
	}
	
	//执行关闭更新资源点脚本
	public static function execuzyd() {
		zydModel::jszyd($_SESSION['playersid'],1,1,1,1);
	}	
}