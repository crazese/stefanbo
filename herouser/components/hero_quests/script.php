<?php
//级别相关任务接收,当作公共基类处理
class getDataAboutLevel {
	public static  $rcrwsj = ''; 
	public static function getAccept($playerInfo,$QuestID,$AcceptVar2,$FinishScript,$OnFinishParameter,$FinishVar1,$FinishVar2,$FinishVar3,$FinishVar4,$num,$RepeatInterval,$var1='',$var2='',$var3='',$var4='',$showProcess,$mblx) {
		$accInfo = explode(',',$num);
		$ffbh = $accInfo[0]; //方法编号
		$sjbh = $accInfo[1]; //事件编号
		$accept = 'accept_'.$ffbh;
		$playerInfo['sjbh'] = $sjbh;
		return getDataAboutLevel::$accept($playerInfo,$QuestID,$AcceptVar2,$FinishScript,$OnFinishParameter,$FinishVar1,$FinishVar2,$FinishVar3,$FinishVar4,$RepeatInterval,$var1,$var2,$var3,$var4,$showProcess,$mblx);
	}
		
	//通用任务接受
	public static function accept_1($playerInfo,$QuestID,$AcceptVar2,$FinishScript,$OnFinishParameter,$FinishVar1,$FinishVar2,$FinishVar3,$FinishVar4,$RepeatInterval,$var1,$var2,$var3,$var4,$showProcess,$mblx) {
		global $common,$db,$mc,$_SGLOBAL;
		$PlayersId = $playerInfo['playersid'];
		if (empty($showProcess) || $showProcess == 0) {
			$mc->set(MC.$PlayersId.'_'.$QuestID.'_rwjd','nodata',0,3600);
		}
        if ($playerInfo['sjbh'] != 0 && substr($playerInfo['rwsj'],$playerInfo['sjbh']-1,1) == 1) {
    	   $Qstatus = 1;
        } else {
    	   $Qstatus = 0;
        }	
        if ($Qstatus == 1) {
        	getDataAboutLevel::addCompleteQuestsStatus($PlayersId,$QuestID,1);
        } else {       			
			getDataAboutLevel::addNewQuestsStatus($PlayersId,$QuestID);
        }
        if ($RepeatInterval == 2) {
        	$common->updatetable('player',array('dqzmbid'=>$QuestID),array('playersid'=>$PlayersId));
        	$common->updateMemCache(MC.$PlayersId,array('dqzmbid'=>$QuestID));
        }
		$common->inserttable('accepted_quests',array('playersid'=>$PlayersId,'QuestID'=>$QuestID,'Qstatus'=>$Qstatus,'AcceptTime'=>$_SGLOBAL['timestamp'],'mblx'=>$mblx));
	}	
	
   //处理驻防任务
   	public static function accept_2($playerInfo,$QuestID,$AcceptVar2,$FinishScript,$OnFinishParameter,$FinishVar1,$FinishVar2,$FinishVar3,$FinishVar4,$RepeatInterval,$var1,$var2,$var3,$var4,$showProcess,$mblx) {
		global $common,$db,$mc,$_SGLOBAL;
		//$level = $playerInfo[$AcceptVar2];
		$PlayersId = $playerInfo['playersid'];
   		if (empty($showProcess) || $showProcess == 0) {
			$mc->set(MC.$PlayersId.'_'.$QuestID.'_rwjd','nodata',0,3600);
		}		
        if ($playerInfo['is_defend'] != 0) {
    	   $Qstatus = 1;
        } else {
    	   $Qstatus = 0;
        }		
        if ($Qstatus == 1) {
        	getDataAboutLevel::addCompleteQuestsStatus($PlayersId,$QuestID,1);
        } else {
        	getDataAboutLevel::addNewQuestsStatus($PlayersId,$QuestID);
        }
        if ($RepeatInterval == 2) {
        	$common->updatetable('player',array('dqzmbid'=>$QuestID),array('playersid'=>$PlayersId));
        	$common->updateMemCache(MC.$PlayersId,array('dqzmbid'=>$QuestID));
        }        
		$common->inserttable('accepted_quests',array('playersid'=>$PlayersId,'QuestID'=>$QuestID,'Qstatus'=>$Qstatus,'AcceptTime'=>$_SGLOBAL['timestamp'],'ExtraData'=>'','mblx'=>$mblx));        
	}
	
	//升级任务接受处理
	public static function accept_3($playerInfo,$QuestID,$AcceptVar2,$FinishScript,$OnFinishParameter,$FinishVar1,$FinishVar2,$FinishVar3,$FinishVar4,$RepeatInterval,$var1,$var2,$var3,$var4,$showProcess,$mblx) {
		global $common,$db,$mc,$_SGLOBAL;
		$PlayersId = $playerInfo['playersid'];
		if (empty($showProcess) || $showProcess == 0) {
			$mc->set(MC.$PlayersId.'_'.$QuestID.'_rwjd','nodata',0,3600);
		}		
		//$level = $playerInfo[$AcceptVar2];
		$Qstatus = 0;
		$ExtraData = '';
		$OnFinishParameterInfo = explode(',',$OnFinishParameter);
		$AcceptVar2 = str_replace("'","",$AcceptVar2);
		if (!empty($playerInfo[$AcceptVar2])) {
		   $now[0] = intval($playerInfo[$AcceptVar2]);
	       $ExtraData = json_encode($now);		
	       if ($playerInfo[$AcceptVar2] >= $OnFinishParameterInfo[1]) {
	    	   $Qstatus = 1;
	       } else {
	    	   $Qstatus = 0;
	       }
		}
        if ($Qstatus == 1) {
        	getDataAboutLevel::addCompleteQuestsStatus($PlayersId,$QuestID,2); 
        } else {
        	getDataAboutLevel::addNewQuestsStatus($PlayersId,$QuestID);
        }
	    if ($RepeatInterval == 2) {
        	$common->updatetable('player',array('dqzmbid'=>$QuestID),array('playersid'=>$PlayersId));
        	$common->updateMemCache(MC.$PlayersId,array('dqzmbid'=>$QuestID));
        }        	
		$common->inserttable('accepted_quests',array('playersid'=>$PlayersId,'QuestID'=>$QuestID,'Qstatus'=>$Qstatus,'AcceptTime'=>$_SGLOBAL['timestamp'],'ExtraData'=>$ExtraData,'mblx'=>$mblx));        
	}	
	
	//爵位等级接受处理
	public static function accept_4($playerInfo,$QuestID,$AcceptVar2,$FinishScript,$OnFinishParameter,$FinishVar1,$FinishVar2,$FinishVar3,$FinishVar4,$RepeatInterval,$var1,$var2,$var3,$var4,$showProcess,$mblx) {
		global $common,$db,$_SGLOBAL,$mc;
		$PlayersId = $playerInfo['playersid'];		
		if (empty($showProcess) || $showProcess == 0) {
			$mc->set(MC.$PlayersId.'_'.$QuestID.'_rwjd','nodata',0,3600);
		}			
		$mg_level = $playerInfo['mg_level'];
		$now[0] = intval($mg_level);	
		$OnFinishParameterInfo = explode(',',$OnFinishParameter);
		$needNum = $OnFinishParameterInfo[1];
		//$ExtraData = json_encode($now);
	    if ($mg_level >= $needNum) {
	    	$Qstatus = 1;
	    	//$now[0] = $needNum;	           	
	    } else {
	    	$Qstatus = 0;
	    	//$now[0] = $mg_level;
	    }
		//$ExtraData = json_encode($now);	
	    if ($RepeatInterval == 2) {
        	$common->updatetable('player',array('dqzmbid'=>$QuestID),array('playersid'=>$PlayersId));
        	$common->updateMemCache(MC.$PlayersId,array('dqzmbid'=>$QuestID));
        }		
		$common->inserttable('accepted_quests',array('playersid'=>$PlayersId,'QuestID'=>$QuestID,'Qstatus'=>$Qstatus,'AcceptTime'=>$_SGLOBAL['timestamp'],'mblx'=>$mblx));
        if ($Qstatus == 1) {
        	return getDataAboutLevel::addCompleteQuestsStatus($PlayersId,$QuestID,1);
        } else {
        	getDataAboutLevel::addNewQuestsStatus($PlayersId,$QuestID);
        }			
		//$common->inserttable('accepted_quests',array('FinishScript'=>$FinishScript,'OnFinishParameter'=>$OnFinishParameter,'FinishVar1'=>$FinishVar1,'FinishVar2'=>$FinishVar2,'FinishVar3'=>$FinishVar3,'FinishVar4'=>$FinishVar4,'playersid'=>$PlayersId,'QuestID'=>$QuestID,'Qstatus'=>0,'AcceptTime'=>time(),'RepeatInterval'=>$RepeatInterval));
	    //getDataAboutLevel::addNewQuestsStatus($PlayersId,$QuestID);		
	}	
	
	//好友数量接受处理
	public static function accept_5($playerInfo,$QuestID,$AcceptVar2,$FinishScript,$OnFinishParameter,$FinishVar1,$FinishVar2,$FinishVar3,$FinishVar4,$RepeatInterval,$var1,$var2,$var3,$var4,$showProcess,$mblx) {
		global $common,$db,$_SGLOBAL,$mc;
		$PlayersId = $playerInfo['playersid'];		
		if (empty($showProcess) || $showProcess == 0) {
			$mc->set(MC.$PlayersId.'_'.$QuestID.'_rwjd','nodata',0,3600);
		}	
		if (empty($playerInfo['hysl'])) {
			$hysl = 0;
		} else {	
			$hysl = $playerInfo['hysl'];
		}
		$now[0] = intval($hysl);	
		$OnFinishParameterInfo = explode(',',$OnFinishParameter);
		$needNum = $OnFinishParameterInfo[1];
		//$ExtraData = json_encode($now);
	    if ($hysl >= $needNum) {
	    	$Qstatus = 1;
	    	$now[0] = $needNum;	           	
	    } else {
	    	$Qstatus = 0;
	    	$now[0] = $hysl;
	    }
		$ExtraData = json_encode($now);	
	    if ($RepeatInterval == 2) {
        	$common->updatetable('player',array('dqzmbid'=>$QuestID),array('playersid'=>$PlayersId));
        	$common->updateMemCache(MC.$PlayersId,array('dqzmbid'=>$QuestID));
        }		
		$common->inserttable('accepted_quests',array('ExtraData'=>$ExtraData,'playersid'=>$PlayersId,'QuestID'=>$QuestID,'Qstatus'=>$Qstatus,'AcceptTime'=>$_SGLOBAL['timestamp'],'mblx'=>$mblx));
        if ($Qstatus == 1) {
        	return getDataAboutLevel::addCompleteQuestsStatus($PlayersId,$QuestID,1);
        } else {
        	getDataAboutLevel::addNewQuestsStatus($PlayersId,$QuestID);
        }			
		//$common->inserttable('accepted_quests',array('FinishScript'=>$FinishScript,'OnFinishParameter'=>$OnFinishParameter,'FinishVar1'=>$FinishVar1,'FinishVar2'=>$FinishVar2,'FinishVar3'=>$FinishVar3,'FinishVar4'=>$FinishVar4,'playersid'=>$PlayersId,'QuestID'=>$QuestID,'Qstatus'=>0,'AcceptTime'=>time(),'RepeatInterval'=>$RepeatInterval));
	    //getDataAboutLevel::addNewQuestsStatus($PlayersId,$QuestID);		
	}

	//升级任务接受处理
	public static function accept_6($playerInfo,$QuestID,$AcceptVar2,$FinishScript,$OnFinishParameter,$FinishVar1,$FinishVar2,$FinishVar3,$FinishVar4,$RepeatInterval,$var1,$var2,$var3,$var4,$showProcess,$mblx) {
		global $common,$db,$mc,$_SGLOBAL;
		$PlayersId = $playerInfo['playersid'];
		if (empty($showProcess) || $showProcess == 0) {
			$mc->set(MC.$PlayersId.'_'.$QuestID.'_rwjd','nodata',0,3600);
		}		
		//$level = $playerInfo[$AcceptVar2];
		$Qstatus = 0;
		$ExtraData = '';
		$OnFinishParameterInfo = explode(',',$OnFinishParameter);		
		if (!empty($playerInfo['player_level'])) {
			if ($QuestID == 1001308) {
				if ($playerInfo['player_level'] > 1 && $playerInfo['player_level'] < 11) {
					if ($playerInfo['player_level'] == 10) {
						$Qstatus = 1;
					} else {
						$Qstatus = 0;
					}
					$success = 1;
				} else {
					$success = 0;
				}
			} elseif ($QuestID == 1001309) {
				if ($playerInfo['player_level'] > 10 && $playerInfo['player_level'] < 21) {
					if ($playerInfo['player_level'] == 20) {
						$Qstatus = 1;
					} else {
						$Qstatus = 0;
					}
					$success = 1;
				} else {
					$success = 0;
				}			
			} elseif ($QuestID == 1001310) {
				if ($playerInfo['player_level'] > 20 && $playerInfo['player_level'] < 31) {
					if ($playerInfo['player_level'] == 30) {
						$Qstatus = 1;
					} else {
						$Qstatus = 0;
					}
					$success = 1;
				} else {
					$success = 0;
				}			
			} elseif ($QuestID == 1001311) {
				if ($playerInfo['player_level'] > 30 && $playerInfo['player_level'] < 41) {
					if ($playerInfo['player_level'] == 40) {
						$Qstatus = 1;
					} else {
						$Qstatus = 0;
					}
					$success = 1;
				} else {
					$success = 0;
				}				
			} elseif ($QuestID == 1001312) {
				if ($playerInfo['player_level'] > 40 && $playerInfo['player_level'] < 51) {
					if ($playerInfo['player_level'] == 50) {
						$Qstatus = 1;
					} else {
						$Qstatus = 0;
					}
					$success = 1;
				} else {
					$success = 0;
				}			
			} elseif ($QuestID == 1001313) {
				if ($playerInfo['player_level'] > 50 && $playerInfo['player_level'] < 61) {
					if ($playerInfo['player_level'] == 60) {
						$Qstatus = 1;
					} else {
						$Qstatus = 0;
					}
					$success = 1;
				} else {
					$success = 0;
				}			
			} elseif ($QuestID == 1001314) {
				if ($playerInfo['player_level'] > 60 && $playerInfo['player_level'] < 71) {
					if ($playerInfo['player_level'] == 70) {
						$Qstatus = 1;
					} else {
						$Qstatus = 0;
					}
					$success = 1;
				} else {
					$success = 0;
				}			
			} 
			if ($success == 1) {
		        if ($RepeatInterval == 2) {
		        	$common->updatetable('player',array('dqzmbid'=>$QuestID),array('playersid'=>$PlayersId));
		        	$common->updateMemCache(MC.$PlayersId,array('dqzmbid'=>$QuestID));
		        }				     	
				$common->inserttable('accepted_quests',array('playersid'=>$PlayersId,'QuestID'=>$QuestID,'Qstatus'=>$Qstatus,'AcceptTime'=>$_SGLOBAL['timestamp'],'ExtraData'=>$ExtraData,'mblx'=>$mblx));    
		        if ($Qstatus == 1) {
		        	getDataAboutLevel::addCompleteQuestsStatus($PlayersId,$QuestID,1);
		        } else {
		        	getDataAboutLevel::addNewQuestsStatus($PlayersId,$QuestID);
		        }   	        
			}  
		}  
	}

	//通用目标接受
	public static function accept_7($playerInfo,$QuestID,$AcceptVar2,$FinishScript,$OnFinishParameter,$FinishVar1,$FinishVar2,$FinishVar3,$FinishVar4,$RepeatInterval,$var1,$var2,$var3,$var4,$showProcess,$mblx) {
		global $common,$db,$_SGLOBAL,$mc;
		$PlayersId = $playerInfo['playersid'];		
		if (empty($showProcess) || $showProcess == 0) {
			$mc->set(MC.$PlayersId.'_'.$QuestID.'_rwjd','nodata',0,3600);
		}			
		$rank = $playerInfo['rank'];
		$now[0] = intval($rank);	
		$OnFinishParameterInfo = explode(',',$OnFinishParameter);
		$needNum = $OnFinishParameterInfo[1];
		//$ExtraData = json_encode($now);
	    if ($rank <= $needNum) {
	    	$Qstatus = 1;
	    	//$now[0] = $needNum;	           	
	    } else {
	    	$Qstatus = 0;
	    	//$now[0] = $mg_level;
	    }
		//$ExtraData = json_encode($now);	
	    if ($RepeatInterval == 2) {
        	$common->updatetable('player',array('dqzmbid'=>$QuestID),array('playersid'=>$PlayersId));
        	$common->updateMemCache(MC.$PlayersId,array('dqzmbid'=>$QuestID));
        }		
		$common->inserttable('accepted_quests',array('playersid'=>$PlayersId,'QuestID'=>$QuestID,'Qstatus'=>$Qstatus,'AcceptTime'=>$_SGLOBAL['timestamp'],'mblx'=>$mblx));
        if ($Qstatus == 1) {
        	return getDataAboutLevel::addCompleteQuestsStatus($PlayersId,$QuestID,1);
        } else {
        	getDataAboutLevel::addNewQuestsStatus($PlayersId,$QuestID);
        }			
		//$common->inserttable('accepted_quests',array('FinishScript'=>$FinishScript,'OnFinishParameter'=>$OnFinishParameter,'FinishVar1'=>$FinishVar1,'FinishVar2'=>$FinishVar2,'FinishVar3'=>$FinishVar3,'FinishVar4'=>$FinishVar4,'playersid'=>$PlayersId,'QuestID'=>$QuestID,'Qstatus'=>0,'AcceptTime'=>time(),'RepeatInterval'=>$RepeatInterval));
	    //getDataAboutLevel::addNewQuestsStatus($PlayersId,$QuestID);		
	}	
	//单独处理文韬值接受
	/*public static function accept_4($playerInfo,$QuestID,$AcceptVar2,$FinishScript,$OnFinishParameter,$FinishVar1,$FinishVar2,$FinishVar3,$FinishVar4,$RepeatInterval) {
		global $common,$db;
		$PlayersId = $playerInfo['playersid'];
		if (empty($playerInfo['nowSwz']) && !empty($playerInfo['prestige_value'])) {
			$playerInfo['nowSwz'] = $playerInfo['prestige_value'];
		}
		if (!empty($playerInfo['nowSwz'])) {
			$now[0] = intval($playerInfo['nowSwz']);
		} else {
			$now[0] = intval(0);
		}
		$ExtraData = json_encode($now);
		$common->inserttable('accepted_quests',array('FinishScript'=>$FinishScript,'OnFinishParameter'=>$OnFinishParameter,'FinishVar1'=>$FinishVar1,'FinishVar2'=>$FinishVar2,'FinishVar3'=>$FinishVar3,'FinishVar4'=>$FinishVar4,'playersid'=>$PlayersId,'QuestID'=>$QuestID,'Qstatus'=>0,'AcceptTime'=>time(),'ExtraData'=>$ExtraData,'RepeatInterval'=>$RepeatInterval));
	    getDataAboutLevel::addNewQuestsStatus($PlayersId,$QuestID);		
	}*/
		
	//招募任务接受处理
	/*public static function accept_5($playerInfo,$QuestID,$AcceptVar2,$FinishScript,$OnFinishParameter,$FinishVar1,$FinishVar2,$FinishVar3,$FinishVar4,$RepeatInterval) {
		global $common,$db;
		$PlayersId = $playerInfo['playersid'];
		//$level = $playerInfo[$AcceptVar2];
		$OnFinishParameterInfo = explode(',',$OnFinishParameter);
		$generalArray = cityModel::getGeneralData($PlayersId);
		$jlsl = count($generalArray);          //当前将领数	
		$needNum = $OnFinishParameterInfo[1];  //需要的将领数  			
		$Qstatus = 0;
		$ExtraData = '';
	    if ($jlsl >= $needNum) {
	    	$Qstatus = 1;
	    	$now[0] = $needNum;	           	
	    } else {
	    	$Qstatus = 0;
	    	$now[0] = $jlsl;
	    }
		$ExtraData = json_encode($now);	
		$common->inserttable('accepted_quests',array('FinishScript'=>$FinishScript,'OnFinishParameter'=>$OnFinishParameter,'FinishVar1'=>$FinishVar1,'FinishVar2'=>$FinishVar2,'FinishVar3'=>$FinishVar3,'FinishVar4'=>$FinishVar4,'playersid'=>$PlayersId,'QuestID'=>$QuestID,'Qstatus'=>$Qstatus,'AcceptTime'=>time(),'ExtraData'=>$ExtraData,'RepeatInterval'=>$RepeatInterval));
        if ($Qstatus == 1) {
        	return getDataAboutLevel::addCompleteQuestsStatus($PlayersId,$QuestID);
        } else {
        	getDataAboutLevel::addNewQuestsStatus($PlayersId,$QuestID);
        }		
	}*/		
	//添加新任务状态
	public static function addNewQuestsStatus($playersid,$QuestID,$newQuests=2,$is_guide=0) {
		/*global $common, $_SGLOBAL;
		$insert['playersid'] = $playersid;
		$insert['QuestID'] = $QuestID;
		$insert['newQuests'] = $newQuests;
		$insert['addTime'] = $_SGLOBAL['timestamp'];
		$insert['is_guide'] = $is_guide;
		$common->inserttable('quests_status',$insert);*/
		getDataAboutLevel::addQuestsStatus($playersid,$QuestID,$newQuests);
	}
	
	//添加新完成任务状态
	public static function addCompleteQuestsStatus($playersid,$QuestID,$published) {
		/*global $common,$db;
		$num = $db->num_rows($db->query("SELECT intID FROM ".$common->tname('quests_status')." WHERE playersid = '$playersid' && QuestID = '$QuestID' LIMIT 1"));
		if ($num == 0) {
		    $insert['playersid'] = $playersid;
		    $insert['QuestID'] = $QuestID;
		    $insert['newQuests'] = 2;		
		    $insert['addTime'] = time();
		    //$common->updatetable('quests_status',$update,$where);
		    $common->inserttable('quests_status',$insert);			
		} else {
		    $where['playersid'] = $playersid;
		    $where['QuestID'] = $QuestID;
		    $update['newQuests'] = 2;		
		    $update['addTime'] = time();
		    $common->updatetable('quests_status',$update,$where);
		}*/
		if ($published != 6) {  //标识为3也可以完成
			getDataAboutLevel::addQuestsStatus($playersid,$QuestID,1);
		}
		return true;
	}

	//添加新任务标识$status 2主线新任务3主线已看任务4日常已看任务5日常新任务6日常未显示任务
	public static function addQuestsStatus($playersid,$QuestID,$status) {
		global $mc, $db, $common;
		$qStatus = array();
		$list = getDataAboutLevel::$rcrwsj;
		if (empty($list)) {
			if (!($qStatus = $mc->get(MC.$playersid."_qstatus"))) {
				//heroCommon::insertLog(1111);
				$sql = "SELECT * FROM ".$common->tname('quests_new_status')." WHERE playersid = '$playersid' LIMIT 1";
				$result = $db->query($sql);
				$rows = $db->fetch_array($result);
				if (empty($rows['qstatusInfo'])) {
					$qStatus = array($QuestID=>$status);
				} else {
					$list = unserialize($rows['qstatusInfo']);	
					foreach ($list as $listKey => $listValue) {
						if ($listKey == $QuestID && $listValue == 1) {
							return false;
							break;
						}
					}
					$qStatus = array($QuestID=>$status) + $list;			
				}			
			} else {
				//heroCommon::insertLog('222+'.$QuestID);
				$list = unserialize($qStatus);
				foreach ($list as $listKey => $listValue) {
					if ($listKey == $QuestID && $listValue == 1) {
						return false;
						break;
					}
				}			
				$qStatus = array($QuestID=>$status) + $list; 
			}
		} else {
			foreach ($list as $listKey => $listValue) {
				if ($listKey == $QuestID && $listValue == 1) {
					return false;
					break;
				}
			}			
			$qStatus = array($QuestID=>$status) + $list; 					
		}
		getDataAboutLevel::$rcrwsj = $qStatus;
		$updateStatus['qstatusInfo'] = serialize($qStatus);
		$updateStatusWhere['playersid'] = $playersid;		
		$mc->set(MC.$playersid."_qstatus",$updateStatus['qstatusInfo'],0,3600);
		$common->updatetable('quests_new_status',$updateStatus,$updateStatusWhere);		
	}
	
	//获任务状态
	public static function hqrwzt($playersid) {
		global $mc, $db, $common, $_SGLOBAL;
		if (!($qStatus = $mc->get(MC.$playersid."_qstatus"))) {
			$sql = "SELECT * FROM ".$common->tname('quests_new_status')." WHERE playersid = '$playersid' LIMIT 1";
			$result = $db->query($sql);
			$rows = $db->fetch_array($result);
			$qStatus = $rows['qstatusInfo'];		
			$mc->set(MC.$playersid."_qstatus",$qStatus,0,3600);	
		}
		$xrwsl = array();
		if (!empty($qStatus)) {
			$vip = 0;
			if (SYPT == 1) {
				$roleInfo['playersid'] = $playersid;
				roleModel::getRoleInfo($roleInfo);
				$vip = $roleInfo['vip'];
			}			
			$qstatusInfo = unserialize($qStatus);	
			getDataAboutLevel::$rcrwsj = $qstatusInfo;
			if (count($qstatusInfo) > 0) {
				$rw = 0;
				foreach ($qstatusInfo as $qstatusInfoKey=>$qstatusInfoValue) {
					if (in_array($qstatusInfoKey,array(1001308,1001309,1001310,1001311,1001312,1001313,1001314,1001104,1001307))) {
						continue;
					}
					if ($qstatusInfoValue != 1 && $qstatusInfoKey == 1001306) {
						continue;
					}
/*hk*/					if (in_array($qstatusInfoKey,array(1001301,1001302,1001303,1001304,3000000,3000001,3000002,3001000,3002000,3003000,1001401,1001402,1001403,1001409,1001410,1001508,1001439))) {
						continue;
/*hk*///屏蔽尊享VIP任务
					}
					$rwInfo = ConfigLoader::GetQuestProto($qstatusInfoKey);
					if ($qstatusInfoValue == 5 && $rwInfo['RepeatInterval'] == 1 ) {						
						//$rw = 2;
						$xrwsl[] = 1;
						//break;
					}
					if ($qstatusInfoValue == 1 && $rwInfo['RepeatInterval'] == 0) {
						$rw = 1;
					}
					$rwInfo = null;
				}
				$rwsl = (count($xrwsl) > 3) ? 3 : count($xrwsl);
				return array('rw'=>$rw,'xrwsl'=>$rwsl);
			} else {
				return array('rw'=>0,'xrwsl'=>0);
			}
		} else {
			return array('rw'=>0,'xrwsl'=>0);
		}
	}
	
	//删除任务状态
	 public static function scrwzt($playersid,$QuestID,$RepeatInterval) {
	 	global $mc, $db, $common;
	 	//heroCommon::insertLog('3333'.'+'.$QuestID);
 		if (!($qStatus = $mc->get(MC.$playersid."_qstatus"))) {
			$sql = "SELECT * FROM ".$common->tname('quests_new_status')." WHERE playersid = '$playersid' LIMIT 1";
			$result = $db->query($sql);
			$rows = $db->fetch_array($result);
			$qStatus = $rows['qstatusInfo'];			
		}
		if (!empty($qStatus)) {
			$qstatusInfo = unserialize($qStatus);			
			if (!empty($qstatusInfo[$QuestID])) {
				if ($RepeatInterval == 0) {
					$qstatusInfo[$QuestID] = 10;
				} elseif ($RepeatInterval == 2) {
					foreach ($qstatusInfo as $key => $qstatusInfoValue) {
						if ($qstatusInfoValue == 10) {
							unset($qstatusInfo[$key]);
						}
					}
					unset($qstatusInfo[$QuestID]);
				} else {
					unset($qstatusInfo[$QuestID]);
				}
				getDataAboutLevel::$rcrwsj = $qstatusInfo;
				$updateStatus['qstatusInfo'] = serialize($qstatusInfo);
				$updateStatusWhere['playersid'] = $playersid;		
				$mc->set(MC.$playersid."_qstatus",$updateStatus['qstatusInfo'],0,3600);
				$common->updatetable('quests_new_status',$updateStatus,$updateStatusWhere);
			} else {
				return false;
			}
		} else {
			return false;
		}	 	
	 }
	 
	//获取任务基表数据
	public static function hqrwjbsj() {
    global $G_Quests;
    ConfigLoader::GetConfig($G_Quests,'G_Quests'); 
		return $G_Quests;
	}	 
	
	//获取任务进度
	public static function hqrwjd($playersid,$QuestID) {
		global $mc,$db,$common;		
		if (!($rwjd = $mc->get(MC.$playersid.'_'.$QuestID.'_rwjd'))) {
			$sql = "SELECT * FROM ".$common->tname('accepted_quests')." WHERE playersid = '$playersid' && QuestID = '$QuestID' LIMIT 1";
			$result = $db->query($sql);
			$rows = $db->fetch_array($result);
			if (!empty($rows)) {
				$rwjd = $rows['ExtraData'];
				$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$rwjd,0,3600);
			}
		}
		if ($rwjd === 'nodata' || empty($rwjd)) {
			return false;
		} else {
			return $rwjd;	
		}	
	}
}

//通用任务完成脚本
class collectAllRes { 	
    //分配任务完成执行脚本
	public static function getFinish($roleInfo,$intID,$QuestID,$OnFinishParameter,$ExtraData,$published) {
		//echo 'hello<br>';
		//echo $OnFinishParameter.'<br>';
		if (!empty($OnFinishParameter)) {
			$OnFinishParameterInfo = explode(',',$OnFinishParameter);
		} else {
			$OnFinishParameterInfo = '';
		}		
		$finish = 'finish_'.$OnFinishParameterInfo[0];
		return collectAllRes::$finish($roleInfo,$intID,$QuestID,$OnFinishParameterInfo,$ExtraData,$published);
	}	
	
	//处理兑换银票任务
	public static function finish_1($roleInfo,$intID,$QuestID,$OnFinishParameterInfo,$ExtraData,$published) {
		global $common,$db,$mc;
		$over = 0;
		if (!isset($roleInfo['hdyp'])) {
			return false;
		}
		$nowTimes = $roleInfo['hdyp'];
		$playersid = $roleInfo['playersid'];
		if (!empty($ExtraData)) {
			$ExtraDataInfo = json_decode($ExtraData,true);
			if ($ExtraDataInfo[0] >= $OnFinishParameterInfo[1]) {
				$now[0] = $OnFinishParameterInfo[1];
			    //$common->updatetable('accepted_quests',"Qstatus = '1'","intID = '$intID'");
				getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);		
				$over = 1;		
			} else {
				$nowTimes = $ExtraDataInfo[0] + $nowTimes;    //当前已完成次数
				if ($nowTimes >= $OnFinishParameterInfo[1]) {	
					$now[0] = $OnFinishParameterInfo[1];	
					$updateData = json_encode($now);			
					//$common->updatetable('accepted_quests',"Qstatus = '1',ExtraData = '$updateData'","intID = '$intID'");
					getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);			        	
			        $over = 1;				
				} else {
					$now[0] = $nowTimes;
				}
			}
		} else {
			if ($OnFinishParameterInfo[1] <= $nowTimes) {
				$now[0] = $OnFinishParameterInfo[1];
				$updateData = json_encode($now);
			    //$common->updatetable('accepted_quests',"Qstatus = '1',ExtraData = '$updateData'","intID = '$intID'");
				getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);
				$over = 1;
			} else {
				$now[0] = $nowTimes;
			}
		}
	    $updateData = json_encode($now);
	    if ($over == 0) {
	    	$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);
	    	$common->updatetable('accepted_quests',"ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID'");	
	    } else {
	    	$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);
	    	$common->updatetable('accepted_quests',"Qstatus = '1',ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID'");
	    	return true;
	    }	    	
	}	
	
	//处理征收资源、采矿、偷矿、打劫,装备升级次数，技能升级次数，历练次数，合成次数，送礼人数任务
	public static function finish_2($roleInfo,$intID,$QuestID,$OnFinishParameterInfo,$ExtraData,$published) {
		global $common,$db,$mc;
		$over = 0;
		$nowTimes = 0;
		$playersid = $roleInfo['playersid'];
		if (!empty($roleInfo['wcsl'])) {
			$addsl = $roleInfo['wcsl'];
		} else {
			$addsl = 1;
		}
		if (!empty($ExtraData)) {
			$ExtraDataInfo = json_decode($ExtraData,true);
			if ($ExtraDataInfo[0] >= $OnFinishParameterInfo[1]) {
				$now[0] = $OnFinishParameterInfo[1];
			    //$common->updatetable('accepted_quests',"Qstatus = '1'","intID = '$intID'");
				getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);		
				$over = 1;		
			} else {
				$nowTimes = $ExtraDataInfo[0];    //当前已完成次数
				if (($addsl + $nowTimes) >= $OnFinishParameterInfo[1]) {	
					$now[0] = $OnFinishParameterInfo[1];	
					$updateData = json_encode($now);			
					//$common->updatetable('accepted_quests',"Qstatus = '1',ExtraData = '$updateData'","intID = '$intID'");
					getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);			        	
			        $over = 1;				
				} else {
					$now[0] = $addsl + $nowTimes;
				}
			}
		} else {
			if ($OnFinishParameterInfo[1] <= $addsl) {
				$now[0] = $OnFinishParameterInfo[1];
				$updateData = json_encode($now);
			    //$common->updatetable('accepted_quests',"Qstatus = '1',ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID'");
				getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);
				$over = 1;
			} else {
				$now[0] = $addsl;
			}
		}
	    $updateData = json_encode($now);
	    if ($over == 0) {
	    	$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);
	    	$common->updatetable('accepted_quests',"ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID'");	
	    } else {
	    	$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);
	    	$common->updatetable('accepted_quests',"Qstatus = '1',ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID'");
	    	return true;
	    }	    	
	}
	
	//完成驻守任务
	public static function finish_3($roleInfo,$intID,$QuestID,$OnFinishParameterInfo,$ExtraData,$published) {
		global $common,$mc;
		$playersid = $roleInfo['playersid'];
		$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd','nodata',0,3600);
		if ($roleInfo['is_defend'] != 0) {
	        $common->updatetable('accepted_quests',"Qstatus = '1'","playersid = '$playersid' && QuestID = '$QuestID'");			
			getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);
			return true;			
		}
	}
	
	//完成装备装备、招募武将、武将卡刷新武将、学习技能、武将训练、解救任务、完成升级
	public static function finish_4($roleInfo,$intID,$QuestID,$OnFinishParameterInfo,$ExtraData,$published) {
		global $common,$mc;
		$playersid = $roleInfo['playersid'];
		$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd','nodata',0,3600);
		if (!empty($roleInfo)) {
	        $common->updatetable('accepted_quests',"Qstatus = '1'","playersid = '$playersid' && QuestID = '$QuestID'");			
			getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);
			return true;			
		}
	}	
	
	//完成具体使用某种道具任务
	public static function finish_5($roleInfo,$intID,$QuestID,$OnFinishParameterInfo,$ExtraData,$published) {
		global $common,$mc;
		$playersid = $roleInfo['playersid'];
		$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd','nodata',0,3600);
		if ($roleInfo['itemId'] == $OnFinishParameterInfo[1]) {
			$common->updatetable('accepted_quests',"Qstatus = '1'","playersid = '$playersid' && QuestID = '$QuestID'");
			getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);			
			return true;			
		}
	}	
	
	//1闯关
	public static function finish_7($roleInfo,$intID,$QuestID,$OnFinishParameterInfo,$ExtraData,$published) {
		global $common,$mc;     
		$playersid = $roleInfo['playersid'];   
        if (!empty($ExtraData)) {
        	$ExtraDataInfo = json_decode($ExtraData,true);
        	if ($ExtraDataInfo[0] >= $OnFinishParameterInfo[1]) {
				//$now[0] = $OnFinishParameterInfo[1];
	 			$common->updatetable('accepted_quests',"Qstatus = '1'","playersid = '$playersid' && QuestID = '$QuestID'");
				getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);			
				return true;	
		    } else {
		    	$nowTimes = $ExtraDataInfo[0];    //当前已完成次数
				if ((1 + $nowTimes) >= $OnFinishParameterInfo[1]) {	
					$now[0] = $OnFinishParameterInfo[1];	
					$updateData = json_encode($now);	
					$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);		
					$common->updatetable('accepted_quests',"Qstatus = '1',ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID'");
					getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);			        	
			        return true;			
				} else {
					$now[0] = 1 + $nowTimes;
					$updateData = json_encode($now);
					$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);			
					$common->updatetable('accepted_quests',"ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID'");						
				}			    	
		    }       		
        } else {
        	if ($OnFinishParameterInfo[1] == 1) {
	 			$common->updatetable('accepted_quests',"Qstatus = '1'","playersid = '$playersid' && QuestID = '$QuestID'");
				getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);			
				return true;		        			
        	} else {
				$now[0] = 1;
				$updateData = json_encode($now);	
				$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);		
				$common->updatetable('accepted_quests',"ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID'");		        			
        	}
        }       
	}

	//完成级别
	public static function finish_8($roleInfo,$intID,$QuestID,$OnFinishParameterInfo,$ExtraData,$published) {
		global $common,$mc;
		$playersid = $roleInfo['playersid'];
		$newLevel = $roleInfo['player_level'];
		$over = 0;
		$needJb = $OnFinishParameterInfo[1];
		if ($newLevel >= $needJb ) {
			$now[0] = $needJb;
			$updateData = json_encode($now);
			$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);
			$common->updatetable('accepted_quests',"Qstatus = '1',ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID'");
			getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);		
			$over = 1;	
		}
		if ($newLevel > $needJb) {
			$now[0] = $needJb;
		} else {
			$now[0] = $newLevel;
		}				
	    $updateData = json_encode($now);
	    if ($over == 0) {
	    	$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);
	    	$common->updatetable('accepted_quests',"ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID'");
	    } else {
	    	return true;
	    }	
	}	
	//完成繁荣度
	public static function finish_11($roleInfo,$intID,$QuestID,$OnFinishParameterInfo,$ExtraData,$published) {
		global $common,$mc;
		$playersid = $roleInfo['playersid'];
		if (!isset($roleInfo['frd'])) {
			return false;
		}
		$newLevel = $roleInfo['frd'];
		$over = 0;
		$needJb = $OnFinishParameterInfo[1];
		if ($newLevel >= $needJb ) {
			$now[0] = $needJb;
			$updateData = json_encode($now);
			$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);
			$common->updatetable('accepted_quests',"Qstatus = '1',ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID'");
			getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);		
			$over = 1;	
		}
		if ($newLevel > $needJb) {
			$now[0] = $needJb;
		} else {
			$now[0] = $newLevel;
		}				
	    $updateData = json_encode($now);
	    if ($over == 0) {
	    	$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);
	    	$common->updatetable('accepted_quests',"ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID'");
	    } else {
	    	return true;
	    }	
	}	
	
	//提升座次
	public static function finish_12($roleInfo,$intID,$QuestID,$OnFinishParameterInfo,$ExtraData,$published) {
		global $common,$db,$mc;
		$over = 0;
		if (!isset($roleInfo['rank'])) {
			return false;
		}
		$nowTimes = $roleInfo['rank'];
		$playersid = $roleInfo['playersid'];
		if (!empty($ExtraData)) {
			$ExtraDataInfo = json_decode($ExtraData,true);
			if ($ExtraDataInfo[0] <= $OnFinishParameterInfo[1]) {
				$now[0] = $OnFinishParameterInfo[1];
			    //$common->updatetable('accepted_quests',"Qstatus = '1'","intID = '$intID'");
				getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);		
				$over = 1;		
			} else {				
				if ($nowTimes <= $OnFinishParameterInfo[1]) {	
					$now[0] = $OnFinishParameterInfo[1];	
					$updateData = json_encode($now);			
					//$common->updatetable('accepted_quests',"Qstatus = '1',ExtraData = '$updateData'","intID = '$intID'");
					getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);			        	
			        $over = 1;				
				} else {
					$now[0] = $nowTimes;
				}
			}
		} else {
			if ($OnFinishParameterInfo[1] >= $nowTimes) {
				$now[0] = $OnFinishParameterInfo[1];
				$updateData = json_encode($now);
			    //$common->updatetable('accepted_quests',"Qstatus = '1',ExtraData = '$updateData'","intID = '$intID'");
				getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);
				$over = 1;
			} else {
				$now[0] = $nowTimes;
			}
		}
	    $updateData = json_encode($now);
	    if ($over == 0) {
	    	$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);
	    	$common->updatetable('accepted_quests',"ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID'");	
	    } else {
	    	$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);
	    	$common->updatetable('accepted_quests',"Qstatus = '1',ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID'");
	    	return true;
	    }	    	
	}

	//完成建筑级别任务
	public static function finish_13($roleInfo,$intID,$QuestID,$OnFinishParameterInfo,$ExtraData,$published) {
		global $common,$mc;
		$playersid = $roleInfo['playersid'];
		$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd','nodata',0,3600);		
		if ($roleInfo['sc_level'] >= $OnFinishParameterInfo[1] && $roleInfo['tjp_level'] >= $OnFinishParameterInfo[1] && $roleInfo['jg_level'] >= $OnFinishParameterInfo[1] && $roleInfo['djt_level'] >= $OnFinishParameterInfo[1]) {
			$common->updatetable('accepted_quests',"Qstatus = '1'","playersid = '$playersid' && QuestID = '$QuestID'");
			getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);			
			return true;			
		}
	}	
	//将军数量任务
	public static function finish_14($roleInfo,$intID,$QuestID,$OnFinishParameterInfo,$ExtraData,$published) {
		global $common,$db,$mc;
		$over = 0;
		$nowTimes = 0;
		$playersid = $roleInfo['playersid'];
		$item = $OnFinishParameterInfo[2];
		if (isset($roleInfo[$item])) {
			$newLevel = $roleInfo[$item];
		} else {
			return false;
		}
		$needJb = $OnFinishParameterInfo[1];
		if ($newLevel >= $needJb ) {
			$now[0] = $needJb;
			$updateData = json_encode($now);
			$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);
			$common->updatetable('accepted_quests',"Qstatus = '1',ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID'");
			getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);		
			$over = 1;	
		}
		if ($newLevel > $needJb) {
			$now[0] = $needJb;
		} else {
			$now[0] = $newLevel;
		}				
	    $updateData = json_encode($now);
	    if ($over == 0) {
	    	$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);
	    	$common->updatetable('accepted_quests',"ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID'");
	    } else {
	    	return true;
	    }	
	}	
	
	//完成爵位及好友数量任务
	public static function finish_9($roleInfo,$intID,$QuestID,$OnFinishParameterInfo,$ExtraData,$published) {
		global $common,$mc;
		$playersid = $roleInfo['playersid'];
		$newLevel = $roleInfo['rwsl'];
		$over = 0;
		$needJb = $OnFinishParameterInfo[1];
		if ($newLevel >= $needJb ) {
			$now[0] = $needJb;
			$updateData = json_encode($now);
			$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);
			$common->updatetable('accepted_quests',"Qstatus = '1',ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID'");
			getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);		
			$over = 1;	
		}
		if ($newLevel > $needJb) {
			$now[0] = $needJb;
		} else {
			$now[0] = $newLevel;
		}				
	    $updateData = json_encode($now);
	    if ($over == 0) {
	    	$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);
	    	$common->updatetable('accepted_quests',"ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID'");
	    } else {
	    	return true;
	    }	
	}		

	//完成招募高级武将任务
	public static function finish_10($roleInfo,$intID,$QuestID,$OnFinishParameterInfo,$ExtraData,$published) {
		global $common,$mc;
		$playersid = $roleInfo['playersid'];
		if (isset($roleInfo['wjys'])) {
			$wjsx = $roleInfo['wjys'];
		} else {
			return false;
		}
		$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd','nodata',0,3600);
		if ($wjsx >= $OnFinishParameterInfo[1]) {
			$common->updatetable('accepted_quests',"Qstatus = '1'","playersid = '$playersid' && QuestID = '$QuestID'");
			getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);			
			return true;			
		}
	}		
	//2打boss、3杀boss、完美击杀
	public static function finish_6($roleInfo,$intID,$QuestID,$OnFinishParameterInfo,$ExtraData,$published) {
		global $common,$mc,$mc;  
		$playersid = $roleInfo['playersid'];
		if (empty($roleInfo['boss'])) {
			return false;
		}
		$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd','nodata',0,3600);
		//heroCommon::insertLog($roleInfo['boss'].'++'.$OnFinishParameterInfo[1]);
        if ($roleInfo['boss'] == $OnFinishParameterInfo[1]) {        	
  			$common->updatetable('accepted_quests',"Qstatus = '1'","playersid = '$playersid' && QuestID = '$QuestID'");
			getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);			
			return true;	           		
        }        
	}

	//2打boss、3杀boss、完美击杀
	public static function finish_15($roleInfo,$intID,$QuestID,$OnFinishParameterInfo,$ExtraData,$published) {
		global $common,$mc,$mc;  
		$playersid = $roleInfo['playersid'];
		if (empty($roleInfo['boss'])) {
			return false;
		}
		$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd','nodata',0,3600);
		//heroCommon::insertLog($roleInfo['boss'].'++'.$OnFinishParameterInfo[1]);
		$over = 0;
		$nowTimes = 0;
		$playersid = $roleInfo['playersid'];
		$addsl = 1;
		if ($roleInfo['boss'] != $OnFinishParameterInfo[2]) {
			return false;
		}
		if (!empty($ExtraData)) {
			$ExtraDataInfo = json_decode($ExtraData,true);
			if ($ExtraDataInfo[0] >= $OnFinishParameterInfo[1]) {
				$now[0] = $OnFinishParameterInfo[1];
			    //$common->updatetable('accepted_quests',"Qstatus = '1'","intID = '$intID'");
				getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);		
				$over = 1;		
			} else {
				$nowTimes = $ExtraDataInfo[0];    //当前已完成次数
				if (($addsl + $nowTimes) >= $OnFinishParameterInfo[1]) {	
					$now[0] = $OnFinishParameterInfo[1];	
					$updateData = json_encode($now);			
					//$common->updatetable('accepted_quests',"Qstatus = '1',ExtraData = '$updateData'","intID = '$intID'");
					getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);			        	
			        $over = 1;				
				} else {
					$now[0] = $addsl + $nowTimes;
				}
			}
		} else {
			if ($OnFinishParameterInfo[1] <= $addsl) {
				$now[0] = $OnFinishParameterInfo[1];
				$updateData = json_encode($now);
			    //$common->updatetable('accepted_quests',"Qstatus = '1',ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID'");
				getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);
				$over = 1;
			} else {
				$now[0] = $addsl;
			}
		}
	    $updateData = json_encode($now);
	    if ($over == 0) {
	    	$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);
	    	$common->updatetable('accepted_quests',"ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID'");	
	    } else {
	    	$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);
	    	$common->updatetable('accepted_quests',"Qstatus = '1',ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID'");
	    	return true;
	    }	    	    
	}	
		
	//检测循环任务接受次数
	public static function checkAccTimes($playersid,$QuestID,$allowTimes) {
		global $common,$db;
		$sql = "SELECT * FROM ".$common->tname('quests_times')." WHERE playersid = '$playersid' && QuestID = '$QuestID' LIMIT 1";
		$res = $db->query($sql);
		$rows = $db->fetch_array($res);
		if ($allowTimes == 0) {
			return 6;   //无次数限制
		}
		if (empty($rows)) {
			return 2;   //直接插入数据
		} else {
			$nowTimes = $rows['acceptTimes'];
			$accDate = date('Y-m-d',$rows['acceptDate']);
			$nowDate = date('Y-m-d',time());
			if ($nowTimes < $allowTimes) {
				return 3;  //直接累加
			} elseif ($nowTimes >= $allowTimes && $accDate == $nowDate) {
				return 4;  //同一天达到次数禁止再次接受此任务
			} else {
				return 5;  //如果非同一天则将数据还原
			}
		}
	}
	
	//插入循环任务接受次数表
	public static function addAccTimes($playersid,$QuestID,$Level_Max) {
		global $common;
		$nowTime = time();
		$common->inserttable('quests_times',array('playersid'=>$playersid,'QuestID'=>$QuestID,'acceptDate'=>$nowTime,'acceptTimes'=>'1','Level_Max'=>$Level_Max));
	}
	
	//更新循环任务接受次数表
	public static function updateAccTimes($playersid,$QuestID,$start = 0) {
		global $common;		
		//$common->inserttable('quests_times',array('playersid'=>$playersid,'QuestID'=>$QuestID,'acceptDate'=>$nowTime,'acceptTimes'=>'1'));
		$nowTime = time();
		if ($start == 5) {			
			$common->updatetable('quests_times',"acceptTimes = '1',acceptDate = '$nowTime'","playersid = '$playersid' && QuestID = '$QuestID'");
		} else {
		    $common->updatetable('quests_times',"acceptTimes = acceptTimes + 1,acceptDate = '$nowTime'","playersid = '$playersid' && QuestID = '$QuestID'");
		}
	}	
	
	//清楚无效循环任务接受次数数据
	public static function clearAccTimes($playersid,$player_level) {
		global $common;	
		$common->deletetable('quests_times',"playersid = '$playersid' && Level_Max < '$player_level'");
	}
}

//处理升级任务
class checkUpgrade {
	//分配任务完成执行脚本
	public static function getFinish($roleInfo,$intID,$QuestID,$OnFinishParameter,$ExtraData,$published) {
		checkUpgrade::finish_upgrade($roleInfo,$intID,$QuestID,$OnFinishParameter,$ExtraData,$published);
	}	
	
	//检查建筑学习升级
	public static function finish_upgrade($roleInfo,$intID,$QuestID,$OnFinishParameter,$ExtraData,$published) {
		global $common,$mc;
		$playersid = $roleInfo['playersid'];
		if ($roleInfo['level'] >= $OnFinishParameter) {
			//$common->updatetable('accepted_quests',"Qstatus = '1'","intID = '$intID'");	
			getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);	
			$now[0] = $roleInfo['level'];
	        $updateData = json_encode($now);
	        $mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);
	        $common->updatetable('accepted_quests',"ExtraData = '$updateData'，Qstatus = '1'","playersid = '$playersid' && QuestID = '$QuestID' LIMIT 1");	
	        return true;			
		}
	}	
}

//处理社交任务完成
class processSocial {    
	public static function getFinish($roleInfo,$intID,$QuestID,$OnFinishParameter,$ExtraData,$published) {
		//echo 'hello<br>';
		/*if (!empty($OnFinishParameter)) {
			$OnFinishParameterInfo = explode(',',$OnFinishParameter);
		} else {
			$OnFinishParameterInfo = '';
		}		
		$finish = 'finish_'.$OnFinishParameterInfo[0];*/
		return processSocial::finish($roleInfo,$intID,$QuestID,$OnFinishParameter,$ExtraData,$published);
	}	

	public static function finish($roleInfo,$intID,$QuestID,$OnFinishParameter,$ExtraData,$published) {
		global $common,$db,$mc;
		$playersid = $roleInfo['playersid'];
		$newAmount = $roleInfo['amount'];
		$over = 0;
		if (empty($ExtraData)) {
			$overAmount = 0;
		} else {
			$ExtraDataInfo = json_decode($ExtraData,true);
			$overAmount = $ExtraDataInfo[0];
		}	
		if (($newAmount + $overAmount) >= $OnFinishParameter) {
			$now[0] = $OnFinishParameter;
			$updateData = json_encode($now);
			$common->updatetable('accepted_quests',"Qstatus = '1',ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID' LIMIT 1");
			getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);
			$over = 1;							
		} else {
			$now[0] = $newAmount + $overAmount;
		}
	    $updateData = json_encode($now);
	    if ($over == 0) {
	    	$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);
	    	$common->updatetable('accepted_quests',"ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID' LIMIT 1");
	    } else {
	    	return true;
	    }	    				
	}
}

//处理完成打怪任务
class fightMon {
	public static function getFinish($roleInfo,$intID,$QuestID,$OnFinishParameter,$ExtraData,$published) {
		return fightMon::finishFight_1($roleInfo,$intID,$QuestID,$OnFinishParameter,$ExtraData,$published);
	}
	//检查打怪任务是否完成
	public static function finishFight_1($roleInfo,$intID,$QuestID,$OnFinishParameter,$ExtraData,$published) {
		global $common,$db,$mc;
		$playersid = $roleInfo['playersid'];
		$requestInfo = explode(',',$OnFinishParameter);
		//$mapId = $requestInfo[0];
		$nodeId = $requestInfo[0];
		$winNum = $requestInfo[1];  //要求胜利次数	
		$over = 0;	
		if ($roleInfo['nodeId'] == $nodeId) {
			if (empty($ExtraData)) {
				$winTime = 0;
			} else {
				$ExtraDataInfo = json_decode($ExtraData,true);
				$winTime = $ExtraDataInfo[0];
			}
			if (($winTime + 1) >= $winNum) {
			   $now[0] = $winNum;
			   $updateData = json_encode($now);
			   $common->updatetable('accepted_quests',"Qstatus = '1',ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID' LIMIT 1");	
			   getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);			   			
			   $over = 1;	   			
			} else {
			   $now[0] = $winTime + 1;					
			}
	        $updateData = json_encode($now);
	        if ($over == 0) {
	        	$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);
	        	$common->updatetable('accepted_quests',"ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID' LIMIT 1");
	        }	        				
		}
		if ($over == 1) {
			return true;
		}
	}
}

//处理攻城任务
class fightCity {
	public static function getFinish($roleInfo,$intID,$QuestID,$OnFinishParameter,$ExtraData,$published) {
		return fightCity::finishFight($roleInfo,$intID,$QuestID,$OnFinishParameter,$ExtraData,$published);
	}
	//完成占领任务
	public static function finishFight($roleInfo,$intID,$QuestID,$OnFinishParameter,$ExtraData,$published) {
		global $common,$db,$mc;	
		$playersid = $roleInfo['playersid'];
		$over = 0;	
		if (empty($ExtraData)) {
			$winTime = 0;
		} else {
			$ExtraDataInfo = json_decode($ExtraData,true);
		    $winTime = $ExtraDataInfo[0];
		}
		if (($winTime + 1) >= $OnFinishParameter) {
			$now[0] = $OnFinishParameter;
			$updateData = json_encode($now);
			$common->updatetable('accepted_quests',"Qstatus = '1',ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID' LIMIT 1");	
			getDataAboutLevel::addCompleteQuestsStatus($roleInfo['playersid'],$QuestID,$published);							
			$over = 1;   			
		} else {
			$now[0] = $winTime + 1;					
		}
	    $updateData = json_encode($now);
	    if ($over == 0) {
	    	$mc->set(MC.$playersid.'_'.$QuestID.'_rwjd',$updateData,0,3600);
	        $common->updatetable('accepted_quests',"ExtraData = '$updateData'","playersid = '$playersid' && QuestID = '$QuestID' LIMIT 1");
	    } else {
	    	return true;
	    }
	}		
}

//获取奖励
class getAward {	
	/*处理获取奖励数据$inputInfo奖励收入，数组类型$QuestID任务ID*/
	public static function processAward($playersid,$inputInfo,$QuestID,$accList,$page,$RepeatInterval,$dh,$Qstart,$AcceptTime,$mblx,$hyb) {
		global $common,$db,$mc,$_SGLOBAL,$G_PlayerMgr,$rw_lang,$_SC;
		$nowTime = $_SGLOBAL['timestamp'];
		//1元宝, 2银两,3军粮,4铜钱,5角色经验,6武将经验,7贵族,8道具1,9道具2,10道具3
		//奖励编号与字段对应数组
		static $item = array(
		   1=>'ingot',
		   2=>'silver',
		   3=>'food',
		   4=>'coins',
		   5=>'current_experience_value',
		   6=>'current_experience',
		   7=>'vip',
		   8=>'item',
		   9=>'item1',
		   10=>'item2',
		   11=>'vip2'	   
		);
		static $awardStart = array('ingot'=>0,'silver'=>0,'coins'=>0,'food'=>0,'current_experience_value'=>0,'current_experience'=>0,'vip'=>0,'item'=>0,'item1'=>0,'item2'=>0,'vip2'=>0);
		foreach ($inputInfo as $key => $val) {
			$incomeInfo[$item[$key]] = $val;
		}
		$awardIncome = array_merge($awardStart,$incomeInfo);	
	    $player = $G_PlayerMgr->GetPlayer($playersid);
	    $roleInfo = &$player->GetBaseInfo();
		$value['upgrade'] = 0; //初始化该key
		cityModel::resourceGrowth($roleInfo);
		if (!empty($hyb)) {
			$syyb = $roleInfo['ingot'] - 10;
			$roleInfo['ingot'] = $syyb;
			$updateRole['ingot'] = $syyb;
			$value['xhyb'] = 10;									
		}
		$coins = $roleInfo['coins'];
		$food = $roleInfo['food'];
		$ingot = $roleInfo['ingot'];
		$silver = $roleInfo['silver'];
		$current_experience_value = $roleInfo['current_experience_value'];
		$player_level = $roleInfo['player_level'];
		$onfinistList = "";
		$djjl = array();
		$value['status'] = 0;
    	$rwlxInfo = ConfigLoader::GetQuestProto($QuestID);    	
	    static $itemkeys = array('item','item1','item2');
	    //处理道具1
	    $itemA = array();
	    foreach($itemkeys as $itemkey) {
	      if ($awardIncome[$itemkey] == 0)  continue;
	      $djInfo = explode('_',$awardIncome[$itemkey]);
	      $itemId = $djInfo[0];
	      $amount = $djInfo[1];
	      if ($itemId == 900000) {
	        $jnsArray = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15);
	        $jns = array_rand($jnsArray,$amount);
	        if (is_array($jns)) {        	 
	          foreach ($jns as $jnsKey) {
	            $jndyid = jndyid($jnsArray[$jnsKey]);
	            if(isset($itemA[$jndyid])) $itemA[$jndyid] += 1;
	            else  $itemA[$jndyid] = 1;
	          }
	        } else {
	          $jndyid = jndyid($jnsArray[$jns]);
	          if(isset($itemA[$jndyid])) $itemA[$jndyid] += 1;
	          else  $itemA[$jndyid] = 1;
	        }
	      } else {
	        if(isset($itemA[$itemId])) $itemA[$itemId] += $amount;
	        else  $itemA[$itemId] = $amount;
	      }
	    }
	
	    if (!empty($itemA))	{
	      $stat = $player->AddItems($itemA);
	      if ($stat !== false) {
	        $djjl = array();
	        foreach ($itemA as $itemid => $amount) {
	          $itemproto = ConfigLoader::GetItemProto($itemid);
	          $djjl[] = array('djmc'=>$itemproto['Name'],'djsl'=>$amount,'iid'=>$itemproto['IconID']);
	        }
	        $bagData = $player->GetClientBag();
	        $value['status'] = 0;
	        $value['djjl'] = $djjl;
	        $value['list'] = $bagData;
	        //$value = array('status' => 0,'djjl'=> $djjl,'list' => $bagData);
	      } else {
	        //getDataAboutLevel::addNewQuestsStatus($playersid,$QuestID);	        
	      	if (!empty($hyb)) {
	      		$value['yb'] = intval($syyb);
	        	$updateRoleWhere['playersid'] = $playersid;
		        $common->updatetable('player',$updateRole,$updateRoleWhere);    
		        $common->updateMemCache(MC.$playersid,$updateRole); 	        	
	        	getDataAboutLevel::addCompleteQuestsStatus($playersid,$QuestID,1);
	        	$db->query("UPDATE ".$common->tname('accepted_quests')." SET Qstatus = 1 WHERE playersid = '$playersid' && QuestID = '$QuestID' LIMIT 1");
	        	$status = 1002;
	      	} else {
	      		$status = 1001;
	      	}
	        return array('status' => $status,'message' => $rw_lang['rw_processAward_1']);
	      }
	    }
		//处理铜钱
		if ($awardIncome['coins'] > 0) {
        	$updateRole['coins'] = $coins + $awardIncome['coins'];
        	$value['tq'] =  $updateRole['coins'];	
        	$value['hqtq'] = intval($awardIncome['coins']);		
		}
	    //处理军粮
		/*if ($awardIncome['food'] > 0) {
        	$updateRole['food'] = $food + $awardIncome['food'];
			$updateRole['last_update_food'] = $nowTime;
        	//$income[] = array('id'=>4,'amount'=>$awardIncome['coins']);  
        	$value['jl'] =  floor($updateRole['food']);	
        	$roleInfo['food'] = $value['jl'];	
        	$value['jjl'] = $awardIncome['food'];	
		}*/	
	    //处理银两
		if ($awardIncome['silver'] > 0) {
        	$updateRole['silver'] = $silver + $awardIncome['silver'];
        	$value['yp'] =  $updateRole['silver'];			
        	$value['hqyp'] = $awardIncome['silver'];
		}
        //处理元宝
        if ($awardIncome['ingot'] > 0) {
        	$updateRole['ingot'] = $ingot + $awardIncome['ingot'];
        	//$income[] = array('id'=>1,'amount'=>$awardIncome['ingot']); 
        	$value['yb'] =  $updateRole['ingot'];  	     
        	$value['hqyb'] = $awardIncome['ingot']; 
        	//获取VIP等级
        	$newvip = roleModel::getPayVipLevel($playersid, $awardIncome['ingot']); 
        	$updateRole['vip'] = $newvip;
        	$roleInfo['vip'] = $newvip;	
        	$value['vip'] = intval($newvip);
        	$dateStr = date('Y-m-d H:i:s');
			$insArray = array('playersid'=>$playersid,
							  'insertTime'=>$dateStr,
							  'money'=>$awardIncome['ingot'] / 10,
							  'ingot'=>$awardIncome['ingot'],
							  'orderid'=>'jl_'.time());
			$common->inserttable('vip_money_log', $insArray);
        } 
		//处理玩家经验
	    if ($awardIncome['current_experience_value'] > 0) {
        	$current_experience_value_total = $current_experience_value + $awardIncome['current_experience_value']; //当前玩家总经验
            if ($player_level < 70) {
		       //判断玩家是否能够升级，以及剩余经验值
		       $upgradePlayer = fightModel::upgradeRole($player_level,$current_experience_value_total,1);
		       $updateRole['current_experience_value'] = $upgradePlayer['left'];	
		       //判断角色是否升级
		       if ($upgradePlayer['level'] > $player_level) {
		            $updateRole['player_level'] = $upgradePlayer['level'];
					$updateRole['last_update_level'] = time();
		            $newPlayerLevel = $updateRole['player_level'];
		            $value['upgrade'] = 1;
		            $value['level'] = $upgradePlayer['level'];      //玩家当前级别                      
		            $value['xjjy'] = cityModel::getPlayerUpgradeExp($value['level']);
		            $value['jlsx'] = foodUplimit($newPlayerLevel);
			        //$attackRoleInfo['player_level'] = $updateRole['player_level'];			            	                	    		            	    
		            //questsController::OnAccept($attackRoleInfo,"'player_level'");
		            $roleInfo['player_level'] = $cjInfo['level'] = $upgradePlayer['level'];		            
		            $value['lsdj'] = addLifeCost($roleInfo ['player_level']);
		            $_SESSION['player_level'] = $roleInfo['player_level'];	
		            
		            roleModel::saveDaleiInfo($roleInfo);
					// 每十级送vip+1三天
					/*if($roleInfo['player_level']%10 == 0){
						roleModel::rwVip($roleInfo);
					}*/
		            if ($roleInfo['player_level'] == 8) {
		            	//roleModel::hqrcrw($roleInfo,false);
		            	getAward::sxrcrw($playersid,2);
		            }
					// 大于十级检查玩家手机绑定
					if($roleInfo['player_level']>=10){
						// 检查是否已绑定手机号,如果没有绑定就将手机号标识为999999
						if(isset($roleInfo['phone'])&&$roleInfo['phone']=='0'){
							$updateRole['phone'] = '999999';					
						}
					}
					// 达到完成邀请条件
					if($roleInfo['player_level']==COMPLETE_REQUEST_LEVEL){
						socialModel::completeRequst($roleInfo, 1);
					}
		            /*if ($roleInfo['player_level'] == 2) {
		            	$_SESSION['mz'] = NULL;
		            } */   
		            //collectAllRes::clearAccTimes($playersid,$roleInfo['player_level']);         
		       		if ($roleInfo['food'] < $value['jlsx']) {
		            	$updateRole['food'] = $value['jlsx'];
						$updateRole['last_update_food'] = $nowTime;
		            	$value['jl'] = floor($value['jlsx']);
		            	$roleInfo['food'] = $value['jlsx'];
		            }	
		            $player_level = $roleInfo['player_level'];
		            //$roleInfo['nowSwz'] = $roleInfo['prestige_value'];	  
		            //$roleInfo['Jb_level'] = $updateRole['player_level'];               
		            $onfinistList .= "'player_level'";	
		            //$db->query('UPDATE '.$common->tname('player')." SET `aggressor_level` = '$newPlayerLevel' WHERE `aggressor_playersid` = '".$roleInfo['playersid']."' LIMIT 5");
		            //$db->query('UPDATE '.$common->tname('playergeneral')." SET `occupied_player_level` = '$newPlayerLevel' WHERE `occupied_playersid` = '".$roleInfo['playersid']."' LIMIT 1");		            	         
		            $value['tqzzl'] = roleModel::tqzzl($roleInfo['player_level']); //铜钱增长率
                    if ($roleInfo['player_level'] == 7 || $roleInfo['player_level'] == 2) {
                    	$zt = 2;
                    } else {
                    	$zt = 1;
                    }
 					/*$xyd = guideScript::jsydsj($roleInfo,'player_level','',$zt);
 					if (empty($xyd)) {
                    	$xyd = guideScript::xsjb($roleInfo,'player_level');   
 					}
		 			//$xyd = guideScript::jsydsj($roleInfo,'tsjw');
					if ($xyd !== false) {
						$value['ydts'] = $xyd;
					}*/	                
		       } else {
		           $value['upgrade'] = 0;                                               //玩家未升级		           
		           //$value['xjjy'] = cityModel::getPlayerUpgradeExp($player_level+1);  //下级升级所要经验
		      } 
		      $value['jy'] = $updateRole['current_experience_value'];     			      
        }
      }
        //处理贵族
       if($awardIncome['vip'] != 0) {
        	//$nowTime = time();
        	$vipInfo = explode('_',$awardIncome['vip']);
        	$vip = $vipInfo[0];
        	//$vip_time = $vipInfo[1] * 86400;
         	//$vip_end_time = $roleInfo['rw_vip_end_time'];
        	/*if ($vip_end_time > $nowTime) {
        		$updateRole['rw_vip_end_time'] = $vip_end_time + $vip_time;
        	} else {
        		$updateRole['rw_vip_end_time'] = $nowTime + $vip_time;
        	}*/
         	//$updateRole['rw_vip_end_time'] = $nowTime + $vip_time;
        	$updateRole['vip'] = $vip;
        	//$cz_vip = $roleInfo['vip'];
        	//$cz_vip_end_time = $roleInfo['vip_end_time'];
        	//if ($cz_vip_end_time < $nowTime) {
        		//$cz_vip = 0;
        	//}
        	//if ($cz_vip > $vip) {
        		//$vip = $cz_vip;
        		//$endTime = $cz_vip_end_time;
        	//} else {
        		//$endTime = $updateRole['rw_vip_end_time'];
        	//}
        	$value['vip'] = $vip;
        	//$value['vt'] = $updateRole['rw_vip_end_time'];
        	$value['jlsx'] = foodUplimit($roleInfo['player_level']);
        	$roleInfo['vip'] = $vip;
        	$value['bxlist'] = cityModel::getGemBoxInfo ( $roleInfo );
        } 

       //处理九游贵族
       if($awardIncome['vip2'] != 0) {
        	//$nowTime = time();
        	$vip_time = $awardIncome['vip2'] * 86400;
         	$vip_end_time = $roleInfo['rw_vip_end_time'];   
        	$updateRole['rw_vip_end_time'] = $nowTime + $vip_time;        	
        	$updateRole['rw_vip'] = $roleInfo['vip'] + 1;
        	/*$cz_vip = $roleInfo['vip'];
        	$cz_vip_end_time = $roleInfo['vip_end_time'];
        	if ($cz_vip_end_time < $nowTime) {
        		$cz_vip = 0;
        	}
        	if ($cz_vip > $vip) {
        		$vip = $cz_vip;
        		$endTime = $cz_vip_end_time;
        	} else {
        		$endTime = $updateRole['rw_vip_end_time'];
        	}*/
        	$value['vip'] = $roleInfo['vip'] + 1;
        	$value['vt'] = $updateRole['rw_vip_end_time'];
        	$value['jlsx'] = foodUplimit($roleInfo['player_level']);
        	$roleInfo['vip'] = $value['vip'];
        	$value['bxlist'] = cityModel::getGemBoxInfo ( $roleInfo );
        }        
		$completeQuests = $roleInfo['completeQuests'];     
        $updateRoleWhere['playersid'] = $playersid;  
        $updateRole['completeQuests'] = $completeQuests; //暂时使用
        if ($RepeatInterval == 0 || $RepeatInterval == 2) {
            //$updateRole['completeQuests'] = $completeQuests;            
            if (empty($completeQuests) || $completeQuests == 0) {
        	    $updateRole['completeQuests'] = $QuestID;
        	    //$roleInfo['completeQuests'] = $updateRole['completeQuests'];
            } else {
        	    $updateRole['completeQuests'] = $completeQuests.','.$QuestID;
        	    //$roleInfo['completeQuests'] = $updateRole['completeQuests'];
            }                	
        }

        /*else {
        	//$common->deletetable('daily_quests',"playersid = '$playersid' && QuestID = '$QuestID' LIMIT 1");
        	//$common->inserttable('daily_quests',array('playersid'=>$playersid,'QuestID'=>$QuestID,'q_status'=>'1','i_time'=>time()));
            if ($Qstart == 1) {
			    $common->deletetable('daily_quests',"playersid = '$playersid' && (q_type = '22'  || q_type = '21')");
				$common->deletetable('quests_status',"playersid = '$playersid' && QuestID IN (SELECT QuestID FROM ".$common->tname('quests')." WHERE QType = '21' || QType = '22')");
				$common->deletetable('accepted_quests',"playersid = '$playersid' && QuestID IN (SELECT QuestID FROM ".$common->tname('quests')." WHERE QType = '21' || QType = '22')");				
			} else {
				$common->updatetable('daily_quests',array('q_status'=>1,'i_time'=>time()),array('playersid'=>$playersid,'QuestID'=>$QuestID));
			}         	
        } */  
        if ($RepeatInterval == 0) {
        	$db->query("UPDATE ".$common->tname('accepted_quests')." SET Qstatus = 3 WHERE playersid = '$playersid' && QuestID = '$QuestID' LIMIT 1");
        } elseif ($RepeatInterval == 2) {
        	$common->deletetable('accepted_quests',"playersid = '$playersid' && (Qstatus = 3 || QuestID = $QuestID) && mblx <= $mblx");       
        } else {
        	$common->deletetable('accepted_quests',"playersid = '$playersid' && QuestID = '$QuestID' LIMIT 1");
        }
        //$common->deletetable('quests_status',"playersid = '$playersid' && QuestID = '$QuestID' LIMIT 1");
        getDataAboutLevel::scrwzt($playersid,$QuestID,$RepeatInterval);         
        $xwzt = '';
		if ($QuestID == 5002030) {  //完成内修经济任务行为
			$xwzt_1 = substr($roleInfo['xwzt'],0,1);
			if ($xwzt_1 == 0) {
				$updateRole['xwzt'] = $xwzt = substr_replace($roleInfo['xwzt'],'1',0,1);
			}
		} elseif ($QuestID == 5002031) {
			$xwzt_2 = substr($roleInfo['xwzt'],1,1);
			if ($xwzt_2 == 0) {
				$updateRole['xwzt'] = $xwzt = substr_replace($roleInfo['xwzt'],'1',1,1);
			}			
		} elseif ($QuestID == 5002032) {
			$xwzt_3 = substr($roleInfo['xwzt'],2,1);
			if ($xwzt_3 == 0) {
				$updateRole['xwzt'] = $xwzt = substr_replace($roleInfo['xwzt'],'1',2,1);
			}			
		} elseif ($QuestID == 5001001) {
			$xwzt_4 = substr($roleInfo['xwzt'],3,1);
			if ($xwzt_4 == 0) {
				$updateRole['xwzt'] = $xwzt = substr_replace($roleInfo['xwzt'],'1',3,1);
			}			
		} elseif ($QuestID == 5002033) {
			$xwzt_5 = substr($roleInfo['xwzt'],4,1);
			if ($xwzt_5 == 0) {
				$updateRole['xwzt'] = $xwzt = substr_replace($roleInfo['xwzt'],'1',4,1);
			}			
		} elseif ($QuestID == 5003001) {
			$xwzt_6 = substr($roleInfo['xwzt'],5,1);
			if ($xwzt_6 == 0) {
				$updateRole['xwzt'] = $xwzt = substr_replace($roleInfo['xwzt'],'1',5,1);
			}			
		} elseif ($QuestID == 5003002) {
			$xwzt_7 = substr($roleInfo['xwzt'],6,1);
			if ($xwzt_7 == 0) {
				$updateRole['xwzt'] = $xwzt = substr_replace($roleInfo['xwzt'],'1',6,1);
			}			
		} elseif ($QuestID == 5003003) {
			$xwzt_8 = substr($roleInfo['xwzt'],7,1);
			if ($xwzt_8 == 0) {
				$updateRole['xwzt'] = $xwzt = substr_replace($roleInfo['xwzt'],'1',7,1);
			}			
		} elseif ($QuestID == 5003007) {
			$xwzt_9 = substr($roleInfo['xwzt'],8,1);
			if ($xwzt_9 == 0) {
				$updateRole['xwzt'] = $xwzt = substr_replace($roleInfo['xwzt'],'1',8,1);
			}			
		} 
		/*if ($QuestID == 1001500) {  //完成内修经济任务行为
			$xwzt_4 = substr($roleInfo['xwzt'],3,1);
			if ($xwzt_4 == 0) {
				$updateRole['xwzt'] = $xwzt = substr_replace($roleInfo['xwzt'],'1',3,1);
			}
		} elseif ($QuestID == 1001507) {  //完成查看学习技能任务行为
			$xwzt_5 = substr($roleInfo['xwzt'],4,1);
			if ($xwzt_5 == 0) {
				$updateRole['xwzt'] = $xwzt = substr_replace($roleInfo['xwzt'],'1',4,1);
			}				
		} elseif ($QuestID == 1001502) {  //完成武将训练任务行为
			$xwzt_6 = substr($roleInfo['xwzt'],5,1);
			if ($xwzt_6 == 0) {
				$updateRole['xwzt'] = $xwzt = substr_replace($roleInfo['xwzt'],'1',5,1);
			}				
		} elseif ($QuestID == 1001503) {  //完成查看背包任务行为
			$xwzt_8 = substr($roleInfo['xwzt'],7,1);
			if ($xwzt_8 == 0) {
				$updateRole['xwzt'] = $xwzt = substr_replace($roleInfo['xwzt'],'1',7,1);
			}				
		} elseif ($QuestID == 1001400) {  //完成购买装备任务行为
			$xwzt_24 = substr($roleInfo['xwzt'],23,1);
			if ($xwzt_24 == 0) {
				$updateRole['xwzt'] = $xwzt = substr_replace($roleInfo['xwzt'],'1',23,1);
			}				
		} elseif ($QuestID == 1001506) {  //完成千锤百炼任务行为
			$xwzt_25 = substr($roleInfo['xwzt'],24,1);
			if ($xwzt_25 == 0) {
				$updateRole['xwzt'] = $xwzt = substr_replace($roleInfo['xwzt'],'1',24,1);
			}				
		} elseif ($QuestID == 1001411) {  //完成扩建铁匠铺行为
			$xwzt_27 = substr($roleInfo['xwzt'],26,1);
			if ($xwzt_27 == 0) {
				$updateRole['xwzt'] = $xwzt = substr_replace($roleInfo['xwzt'],'1',26,1);
			}				
		}*/
        if ($RepeatInterval == 1) {
        	$hyd = $roleInfo['hyd'];
        	$updateRole['hyd'] = $hyd + 5;
        	if ($updateRole['hyd'] > 150) {
        		$updateRole['hyd'] = 150;
        	}
        	achievementsModel::check_achieve($playersid,'',array('rc','rcrw'));
        } else {
        	$cjInfo['rwid'] = $QuestID;
        	if ($value['upgrade'] == 1) {        		
        		achievementsModel::check_achieve($playersid,$cjInfo,array('level','rw'));
        	} else {
        		achievementsModel::check_achieve($playersid,$cjInfo,array('rw'));
        	}
        }		
        $common->updatetable('player',$updateRole,$updateRoleWhere);    
        $common->updateMemCache(MC.$playersid,$updateRole); 
        $roleInfo['completeQuests'] = $updateRole['completeQuests'];
        if ($awardIncome['current_experience_value'] > 0 && isset($value['upgrade'])) {
        	if ($value['upgrade'] == 1) {
        		$value['jzzt'] = cityModel::jzzt($roleInfo['playersid']);
        	}
        }   
        /*$hylist = roleModel::getTableRoleFriendsInfo($playersid,1,true);
        $roleInfo['hysl'] = count($hylist);*/
        $acc_res = questsController::OnAccept($roleInfo,$accList);
        /*if (!empty($acc_res)) {
        	$value['rwid'] = $acc_res;
        }*/
        if (!empty($onfinistList)) {
        	$roleInfo['inQuestID'] = $QuestID;
        	$rwid = questsController::OnFinish($roleInfo,$onfinistList);
			if (!empty($rwid))	{
				$value['rwid'] = $rwid; 
			}	 			
        } 
    	if ($RepeatInterval == 2) {
    		$result = getAward::hqmbxx($playersid);
    		if ($result['status'] == 0) {
    			$value['mb'] = $result;
    		}
    	} 
        if ($RepeatInterval == 1 || !empty($value['upgrade'])) {
        	//获取任务状态信息
			/*if (! ($qStatus = $mc->get ( MC . $roleInfo['playersid'] . "_qstatus" ))) {
				$sql = "SELECT * FROM " . $common->tname ( 'quests_new_status' ) . " WHERE playersid = '".$roleInfo['playersid']."' LIMIT 1";
				$result = $db->query ( $sql );
				$rows = $db->fetch_array ( $result );
				$qStatus = $rows ['qstatusInfo'];
				$mc->set(MC.$playersid."_qstatus",$qStatus,0,3600);
			}*/
        	$qstatusInfo = getDataAboutLevel::$rcrwsj;	
			$Qarray = array();
			$QarrayChose = array();
			$yyQid = array();
			if (! empty ( $qstatusInfo )) {
				//$qstatusInfo = unserialize ( $qStatus );
	            //if (!empty($qstatusInfo)) {
					foreach ( $qstatusInfo as $key => $qValue ) {
						if ($roleInfo['sc_level'] >= 12 && $roleInfo['ld_level'] >= 12 && $roleInfo['tjp_level'] >= 12 && $roleInfo['jg_level'] >= 12 && $roleInfo['djt_level'] >= 12 && $key == 2003000) {
							continue;
						}
						if ($qValue == 1 && ! empty ( $key )) {
							$jcrwInfo = ConfigLoader::GetQuestProto($key);
							if ($jcrwInfo['RepeatInterval'] == 1) {
								$ckrw = "SELECT * FROM ".$common->tname('accepted_quests')." WHERE QuestID = $key && playersid = $playersid LIMIT 1";
								$ckRes = $db->query($ckrw);
								$ckRows = $db->fetch_array($ckRes);
								if (!empty($ckRows)) {
									if ($ckRows['published'] != 1) {
										$qValue = 6;
									}
								}
							}
						}	
						if ($qValue == 6 && ! empty ( $key )) {
							$QarrayChose [] = $key;
						}
						if (in_array($qValue,array(4,5)) && ! empty ( $key )) {
							$yyQid[] = $key;
						}	
					}
	            //}
			}
			$easy = array(2002000,2003001,2003002,2004015,2005001,2004000,2004001,2004003,2004004,2001018,2004017,2004012,2001000,2003000);
			if (!empty($QarrayChose)) {
				$canUseID = array();
				foreach ($QarrayChose as $QarrayChoseValue) {
					$rwinfo = ConfigLoader::GetQuestProto($QarrayChoseValue);
	        		$Level_Min = $rwinfo['Level_Min'];
	        		$Level_Max = $rwinfo['Level_Max'];	
        			if ($player_level >= $Level_Min && $player_level <= $Level_Max && in_array($QarrayChoseValue,$easy)) {
						$canUseID[] = $QarrayChoseValue;
					}	        		
					$rwinfo = null;
					$Level_Min = null;
					$Level_Max = null;
				} 
				if (empty($canUseID)) {
					foreach ($QarrayChose as $QarrayChoseValue) {
						$rwinfo = ConfigLoader::GetQuestProto($QarrayChoseValue);
		        		$Level_Min = $rwinfo['Level_Min'];
		        		$Level_Max = $rwinfo['Level_Max'];	
	        			if ($player_level >= $Level_Min && $player_level <= $Level_Max) {
							$canUseID[] = $QarrayChoseValue;
						}	        		
						$rwinfo = null;
						$Level_Min = null;
						$Level_Max = null;
					}						
				}
				if (!empty($value['upgrade']) && count($yyQid) >= 3) {
					$canUseID = '';
				}
				if (!empty($canUseID)) {						
	        		$checked = array_rand($canUseID,1);
	        		$QuestIDUpdate = $canUseID[$checked];
	        		//$chosedrwinfo = ConfigLoader::GetQuestProto($QuestIDUpdate);									
		        	$rwsql = "SELECT * FROM ".$common->tname('accepted_quests')." WHERE playersid = $playersid  && QuestID = $QuestIDUpdate LIMIT 1";
		        	$rwsqlRows = $db->fetch_array($db->query($rwsql));
	        		//$rwinfo = ConfigLoader::GetQuestProto($QuestIDUpdate);
	        		if (!empty($rwsqlRows)) {
		        		$db->query("UPDATE ".$common->tname('accepted_quests')." SET published = 1 WHERE playersid = $playersid && QuestID = '$QuestIDUpdate' LIMIT 1");
		        		//$aff_rows = $db->affected_rows();
		        		//echo "UPDATE ".$common->tname('accepted_quests')." SET published = 1 WHERE playersid = $playersid && QuestID = '$QuestIDUpdate' LIMIT 1";
		        		if ($rwsqlRows['Qstatus'] == 1) {
		        			getDataAboutLevel::addCompleteQuestsStatus($playersid,$QuestIDUpdate,1);
		        		} else {
		        			getDataAboutLevel::addNewQuestsStatus($playersid,$QuestIDUpdate,5);
		        		}
	                    //$updateRole['rcrws'] = $roleInfo['rcrws'] + 1;
	        			//$roleInfo['rcrws'] = $updateRole['rcrws'];
	        		} /*else {
	        			$db->query('INSERT INTO '.$common->tname('accepted_quests')." SET QuestID = $QuestIDUpdate,playersid = $playersid, Qstatus = 0, Progress = 0 ,AcceptTime = '$nowTime', ExtraData = '', readStatus=0, published=1, RepeatInterval=1");
	        			getDataAboutLevel::addNewQuestsStatus($playersid,$QuestIDUpdate,5);
	                    //$updateRole['rcrws'] = $roleInfo['rcrws'] + 1;
	        			//$roleInfo['rcrws'] = $updateRole['rcrws'];	        			
	        		}*/		        	
				}
			}
        }    	                  
	    if (!empty($page)) {
	    	if ($RepeatInterval == 1) {
		    	$result = getAward::getRWList($playersid,$page);
		        if ($result['status'] == 0) {
		           //$value['curPage'] = $result['curPage'];
		           //$value['totalPage'] = $result['totalPage'];
		           $value['rw'] = $result['rw'];    		
		        }				
	    	}
        	if ($RepeatInterval == 0) {
	    		$result = getAward::hqmbxx($playersid);
	    		if ($result['status'] == 0) {
	    			$value['mb'] = $result;
	    		}
    		}	    	
	        /*$result = getAward::getRWList($playersid,$page);
	        if ($result['status'] == 0) {
	           $value['curPage'] = $result['curPage'];
	           $value['totalPage'] = $result['totalPage'];
	           $value['rw'] = $result['rw'];    		
	        }*/ 
	    }          	
        if (!empty($dh)) {
        	$value['dh'] = $dh;
        }    
       /* if ($QuestID == 3000000 && empty($value['ydts'])) {
	 		//完成引导脚本&&接收新的引导
			$xyd = guideScript::wcjb($roleInfo,'rwjl',1,1,3000000);
			//接收新的引导
			$xyd = guideScript::xsjb($roleInfo,'cj');
			if ($xyd !== false) {
				$value['ydts'] = $xyd;
			}	
        }	
        if ($roleInfo['player_level'] == 8 && empty($value['ydts'])) {
			//接收新的引导
			$xyd = guideScript::xsjb($roleInfo,'zmwj');
			if ($xyd !== false) {
				$value['ydts'] = $xyd;
			}	
        }    
 		//显示按钮ID
		$buttonID = roleModel::hqxsanid($roleInfo['player_level']);
		if ($buttonID != false) {
			$value['xs'] = $buttonID; 
		}*/	              
        /*if (empty($value['ydts'])) {
			$xyd = guideScript::jsydsj($roleInfo,'tsjw');
			if ($xyd !== false) {
				$value['ydts'] = $xyd;
			}	        	
        }  */  
        //$value['sy'] = $income;
        if ($roleInfo['player_level'] == 2)	{
           roleModel::sjhqrcrw(); 
        }
        if (!empty($hyb) && $value['status'] == 0) {
        	$value['yb'] = $syyb;
        }  
        $value['lx'] = intval($rwlxInfo['RepeatInterval']);      
        return $value;
	}

	//用户获得奖励
	public static function award($playersid,$QuestID,$page='',$hyb = '') {
		global $common,$db,$rw_lang,$G_PlayerMgr,$sys_lang;
		$player = $G_PlayerMgr->GetPlayer($playersid);
		if (!$player) {
			return array('status'=>30,'message'=>$sys_lang[1]);
		}
		$roleInfo = $player->baseinfo_;	    
		$questproto = ConfigLoader::GetQuestProto($QuestID);	    
	    if(!$questproto)  return array('status' => 21,'message' => $rw_lang['rw_getRWInfo_21']);		
		if (!empty($hyb) && $questproto['RepeatInterval'] == 1) {
			$result = $db->query("SELECT AcceptTime,QuestID FROM ".$common->tname('accepted_quests')." where playersid = '$playersid'  && QuestID = '$QuestID' LIMIT 1");
		} else {
	    	$result = $db->query("SELECT AcceptTime,QuestID FROM ".$common->tname('accepted_quests')." where playersid = '$playersid'  && QuestID = '$QuestID' && Qstatus = '1' LIMIT 1");
		}
	    $rows = $db->fetch_array($result);	    		
		if (empty($rows)) {
			getDataAboutLevel::scrwzt($playersid,$QuestID,1);
      		return array('status' => 21,'message' => $rw_lang['rw_getRWInfo_21']);
		}
		if (!empty($hyb)) {
			$yb = $roleInfo['ingot'];
			if ($yb < 10) {
				$returnValue['status'] = 88;
				$returnValue['yb'] = $yb;
				$rep_arr1 = array('{xhyb}', '{yb}');
				$rep_arr2 = array(10, $yb);		
				$returnValue['message'] = str_replace($rep_arr1, $rep_arr2, $sys_lang[2]);	
				$returnValue['xyxhyb'] = 10;
				return $returnValue;		
			}
		}	    
	    $AcceptTime = $rows['AcceptTime'];
	    $QawardInfo = json_decode($questproto['Qaward'],true);
	    $Qstart = $questproto['Qstart'];
	    $accList = '';
	    $accvars = array();
	    for($i=1;$i<5;++$i) {
	      if (!empty($questproto['AcceptVar'.$i]))
	        $accvars[] = "'".$questproto['AcceptVar1']."'";
	    }
	    if(!empty($accvars))  $accList=implode(",", $accvars);
	    
	    $RepeatInterval = $questproto['RepeatInterval'];
	    $dh = $questproto['npc_dialog'];
	
	    $value = getAward::processAward($playersid,$QawardInfo,$QuestID,$accList,$page,$RepeatInterval,$dh,$Qstart,$AcceptTime,$questproto['mblx'],$hyb);
	    return $value;
	}

	//获取目标信息
	public static function hqmbxx($playersid,$page=1) {
		global $mc, $db, $common, $G_PlayerMgr, $sys_lang, $rw_lang, $_SC;
		$player = $G_PlayerMgr->GetPlayer($playersid);		 
		if (!$player) {
			return array('status'=>30,'message'=>$sys_lang[1]);
		} else {
			$roleInfo = $player->baseinfo_;
			$completeQuests = $roleInfo['completeQuests'];
			if (!empty($completeQuests)) {
				$wcrw = explode(',',$completeQuests);
			} else {
				$wcrw = array();
			}
			if ($roleInfo['sc_level'] >= 2 && $roleInfo['tjp_level'] >= 2 && $roleInfo['jg_level'] >= 2 && $roleInfo['djt_level'] >= 2)	{
				questsController::OnFinish ( $roleInfo, "'jzdj'");
			} 			
			$dqzmbid = $roleInfo['dqzmbid'];
			$rwsj = getDataAboutLevel::$rcrwsj;
			if (empty($rwsj)) {
				$rwzt = getDataAboutLevel::hqrwzt($playersid);
				$rwsj = getDataAboutLevel::$rcrwsj;
			}			
			if (!empty($rwsj)) {
				//asort($rwsj);
				//$rwinfo = ConfigLoader::GetQuestProto($rows['QuestID']);
				$zmbinfo = ConfigLoader::GetQuestProto($dqzmbid);
				$value['status'] = 0;
				$value['id'] = intval($dqzmbid);
				$value['mbiid'] = zmbiid($dqzmbid);
				//$value['tb'] = '';			
				if (in_array($dqzmbid,array(1001308,1001309,1001310,1001311,1001312,1001313,1001314))) {	
					$value['bt'] = str_replace('XXX','VIP'.($roleInfo['vip']+1),$zmbinfo['QTitle']);
				} else {
					$value['bt'] = $zmbinfo['QTitle'];
				}
				/*if ($dqzmbid == 1001306) {				
					$value['xx'] = str_replace('XXX',date('Y-m-d H:i:s',$_SC['kfhdjs']),$zmbinfo['Description']);
				} else {
					$value['xx'] = $zmbinfo['Description'];
				}*/
				$value['zt'] = intval($rwsj[$dqzmbid]);
				if ($value['zt'] > 1 && $value['zt'] != 10) {
					$value['zt'] = 0;
				}
				$rwjl = array();
				$jlInfo = json_decode($zmbinfo['Qaward'],true);
				foreach ($jlInfo as $key => $jlValue) {
					if ($key == 8 || $key == 9 || $key == 10) {
						$djInfo = explode('_',$jlValue);
						$itemId = $djInfo[0];
						$sl = $djInfo[1];
						if ($itemId == 900000) {
							$djmc = $rw_lang['rw_getRWInfo_1'];
							$iid = 'Ico007';
							$rwjl[] = array('mc'=>$djmc,'sl'=>intval($sl),'iid'=>$iid);
						} else {											
							$getDjInfo = toolsModel::getItemInfo($itemId);
							$djmc = $getDjInfo['Name'];
							$iid = $getDjInfo['IconID'];
							// 修正道具品级，部分道具级别和Rarity字段描述有出入
							$xyd = toolsModel::getRealRarity($getDjInfo);	
							$rwjl[] = array('mc'=>$djmc,'sl'=>intval($sl),'iid'=>$iid,'xyd'=>intval($xyd));
							$djInfo = null;
							$itemId = null;
							$getDjInfo = null;	
						}
						$sl = null;					
						$djmc = null;
						$iid = null;				
					} elseif ($key == 5) {
						$djmc = 'exp';
						$sl = $jlValue;
						$rwjl[] = array('mc'=>$djmc,'sl'=>intval($sl));
						$djmc = null;
						$sl = null;
					} elseif ($key == 7) {					
						$vipInfo = explode('_',$jlValue);
						$jb = $vipInfo[0];
						$sl = $vipInfo[1].$rw_lang['rw_getRWInfo_2'];
						$djmc = 'VIP'.$jb;
						$iid = 'icon_vip_'.$jb;
						$rwjl[] = array('mc'=>$djmc,'sl'=>$sl,'iid'=>$iid);	
						$djmc = null;
						$sl = null;						
					} elseif ($key == 11) {
						$jb = $roleInfo['vip'] + 1;
						$sl = $jlValue.$rw_lang['rw_getRWInfo_2'];
						$djmc = 'VIP'.$jb;
						$iid = 'icon_vip_'.$jb;
						$rwjl[] = array('mc'=>$djmc,'sl'=>$sl,'iid'=>$iid);	
						$djmc = null;
						$sl = null;										
					} elseif ($key == 2) {
						$djmc = $rw_lang['rw_getRWInfo_3'];
						$sl = $jlValue;
						$rwjl[] = array('mc'=>$djmc,'sl'=>intval($sl),'iid'=>'icon_yp');	
						$djmc = null;
						$sl = null;						
					} elseif ($key == 4) {	
						$djmc = $rw_lang['rw_getRWInfo_4'];
						$sl = $jlValue;
						$rwjl[] = array('mc'=>$djmc,'sl'=>intval($sl),'iid'=>'icon_tq');	
						$djmc = null;
						$sl = null;												
					} else {
						$djmc = $rw_lang['rw_getRWInfo_5'];
						$sl = $jlValue;
						$rwjl[] = array('mc'=>$djmc,'sl'=>intval($sl),'iid'=>'yb1');	
						$djmc = null;
						$sl = null;									
					}
				}
				$value['jldj'] = $rwjl;	
				$showProcess = $zmbinfo['showProcess'];
				if ($dqzmbid == 1001306) {
					$wcms = str_replace('XXX',date('Y-m-d H:i:s',$_SC['kfhdjs']),$zmbinfo['completeDesc']); 
				} else {
					$wcms = $zmbinfo['completeDesc'];                            //完成描述
				}
				/*if (!empty($rows['ExtraData'])) {
					$processInfo = json_decode($rows['ExtraData'],true);
				} else {
					$processInfo = '';
				}			
		        if(!empty($showProcess)) {
		        	$wcmsInfo = explode(';',$wcms);
		        	$showProcessInfo = explode(';',$showProcess);
		        	for ($i = 0; $i < count($showProcessInfo); $i++) {
		        		if (!empty($processInfo)) {
		        			$showContent[] = $wcmsInfo[$i].'('.$processInfo[$i].'/'.$showProcessInfo[$i].')';
		        		} else {
		        			$showContent[] = $wcmsInfo[$i].'(0/'.$showProcessInfo[$i].')';
		        		}	        		
		        	}
		        	$rwjd = implode(';',$showContent);
		        } else {
		        	$rwjd = $wcms;
		        }*/
		        $value['xx'] = $wcms; 				
				$rwlistid = array();
				$xszpx = array();
				foreach ($rwsj as $rwKey => $rwsjValue) {
					$xszpx[] = array('k'=>$rwKey,'v'=>$rwsjValue);
				}				
	            foreach ($xszpx as $xszpxkey => $xszpxvalue) {
	              $sort_jl[$xszpxkey] = $xszpxvalue['k'];
	              $sort_id[$xszpxkey] = $xszpxvalue['v'];
	            }	
	            array_multisort($sort_id,SORT_ASC,SORT_NUMERIC,$sort_jl,SORT_ASC,SORT_NUMERIC,$xszpx);
				foreach ($xszpx as $rwKey => $rwsjValue) {
					$rwKey = $rwsjValue['k'];
					$mbInfo = ConfigLoader::GetQuestProto($rwKey);
					if (empty($mbInfo)) {
						continue;
					}
					if ($mbInfo['RepeatInterval'] == 1 || $mbInfo['RepeatInterval'] == 2 || $rwKey == $zmbinfo || $mbInfo['public_status'] == 0 || (in_array($rwKey,$wcrw) && $rwsj[$rwKey] != 10)) {
						continue;
					} else {
						$rwlistid[] = $rwKey;
					}
					$mbInfo = null;
					$rwKey = null;
				}
				if (empty($rwlistid)) {
					return array('status'=>30,'message'=>$rw_lang['sxrcrw_1']);
				} else {
					//sort($rwlistid);					
					$total = count($rwlistid);
					$value ['ys'] = ceil ( $total / 100 );
					if ($page > $value ['ys']) {
						$page = $value ['ys'];
					}
					if (empty ( $page ) || $page < 0) {
						$page = 1;
					}
					$value ['ym'] = $page;
					$start = ($page - 1) * 100;
					$end = $start + 99;	
					for($i = $start; $i < $total; $i ++) {
						if ($i > $end) {
							break;
						}
						$id = intval($rwlistid[$i]);
						$cmbInfo = ConfigLoader::GetQuestProto($id);
						//$icon = $cmbInfo['tb'];
						if (in_array($id,array(1001308,1001309,1001310,1001311,1001312,1001313,1001314))) {	
							$bt = str_replace('XXX','VIP'.($roleInfo['vip']+1),$cmbInfo['QTitle']);
						} else {
							$bt = $cmbInfo['QTitle'];
						}
						/*if ($id == 1001306) {				
							$xx = str_replace('XXX',date('Y-m-d H:i:s',$_SC['kfhdjs']),$cmbInfo['Description']);
						} else {
							$xx = $cmbInfo['Description'];
						}*/
						if ($id == 1001306) {
							$cmbwcms = str_replace('XXX',date('Y-m-d H:i:s',$_SC['kfhdjs']),$cmbInfo['completeDesc']); 
						} else {
							$cmbwcms = $cmbInfo['completeDesc'];                            //完成描述
						}						
						$zt = intval($rwsj[$id]);
						if ($zt > 1 && $zt != 10) {
							$zt = 0;
						}
						$rwjl = array();
						$jlInfo = array();
						$jlInfo = json_decode($cmbInfo['Qaward'],true);
						foreach ($jlInfo as $key => $jlValue) {
							if ($key == 8 || $key == 9 || $key == 10) {
								$djInfo = explode('_',$jlValue);
								$itemId = $djInfo[0];
								$sl = $djInfo[1];
								if ($itemId == 900000) {
									$djmc = $rw_lang['rw_getRWInfo_1'];
									$iid = 'Ico007';
									$rwjl[] = array('mc'=>$djmc,'sl'=>intval($sl),'iid'=>$iid);
								} else {											
									$getDjInfo = toolsModel::getItemInfo($itemId);
									$djmc = $getDjInfo['Name'];
									$iid = $getDjInfo['IconID'];
									// 修正道具品级，部分道具级别和Rarity字段描述有出入
									$xyd = toolsModel::getRealRarity($getDjInfo);	
									$rwjl[] = array('mc'=>$djmc,'sl'=>intval($sl),'iid'=>$iid,'xyd'=>intval($xyd));
									$djInfo = null;
									$itemId = null;
									$getDjInfo = null;	
								}
								$sl = null;					
								$djmc = null;
								$iid = null;				
							} elseif ($key == 5) {
								$djmc = 'exp';
								$sl = $jlValue;
								$rwjl[] = array('mc'=>$djmc,'sl'=>intval($sl));
								$djmc = null;
								$sl = null;
							} elseif ($key == 7) {					
								$vipInfo = explode('_',$jlValue);
								$jb = $vipInfo[0];
								$sl = $vipInfo[1].$rw_lang['rw_getRWInfo_2'];
								$djmc = 'VIP'.$jb;
								$iid = 'icon_vip_'.$jb;
								$rwjl[] = array('mc'=>$djmc,'sl'=>$sl,'iid'=>$iid);	
								$djmc = null;
								$sl = null;						
							} elseif ($key == 11) {
								$jb = $roleInfo['vip'] + 1;
								$sl = $jlValue.$rw_lang['rw_getRWInfo_2'];
								$djmc = 'VIP'.$jb;
								$iid = 'icon_vip_'.$jb;
								$rwjl[] = array('mc'=>$djmc,'sl'=>$sl,'iid'=>$iid);	
								$djmc = null;
								$sl = null;										
							} elseif ($key == 2) {
								$djmc = $rw_lang['rw_getRWInfo_3'];
								$sl = $jlValue;
								$rwjl[] = array('mc'=>$djmc,'sl'=>intval($sl),'iid'=>'icon_yp');	
								$djmc = null;
								$sl = null;						
							}  elseif ($key == 4) {
								$djmc = $rw_lang['rw_getRWInfo_4'];
								$sl = $jlValue;
								$rwjl[] = array('mc'=>$djmc,'sl'=>intval($sl),'iid'=>'icon_tq');	
								$djmc = null;
								$sl = null;									
							} else {
								$djmc = $rw_lang['rw_getRWInfo_5'];
								$sl = $jlValue;
								$rwjl[] = array('mc'=>$djmc,'sl'=>intval($sl),'iid'=>'yb1');	
								$djmc = null;
								$sl = null;									
							}
						}		
						$jldj = $rwjl;						
						$rwlist[] = array('id'=>$id,'bt'=>$bt,'xx'=>$cmbwcms,'zt'=>$zt,'jldj'=>$jldj);
						$id = $cmbInfo = $icon = $bt = $xx = $zt = $jldj = null;						
					}
					$value['rwlist'] = $rwlist;
					return $value;										
				}				
			} else {
				return array('status'=>30,'message'=>$rw_lang['sxrcrw_1']);
			}
		}
	}
	
	//任务列表 //$sfrcrw 是否日常任务
	public static function getRWList($playersid,$curPage=1,$sfrcrw = 1) {
		global $common,$db,$_SGLOBAL,$_SC,$mc,$rw_lang;
		//ConfigLoader::GetQuestProto();
		$perPage = 3;
		if ($curPage == 0) {
			$curPage = 1;
		}
		if ($sfrcrw == 1) {
			$rcItem = "&& RepeatInterval = 1";
		} else {
			$rcItem = "&& RepeatInterval in (0,2)";
		}
		$roleInfo['playersid'] = $playersid;
		roleModel::getRoleInfo($roleInfo);
		if (!empty($_SESSION['loginTime'])) {
			//是否全新一天
			if (date('Y-m-d',$_SESSION['loginTime']) != date('Y-m-d',$_SGLOBAL['timestamp'])) {
				$_SESSION['qxyt'] = 1;
				$_SESSION['loginTime'] = $_SGLOBAL['timestamp'];
				$uprwres = roleModel::hqrcrw($roleInfo);
				/*if ($uprwres == true){
					$updateRole['rcrws'] = 3;//设置更新日常任务的条数
					$common->updatetable('player',$updateRole,"playersid = '".$roleInfo['playersid']."'");
					$common->updatetable('user',"last_login_time = '".$_SGLOBAL['timestamp']."'","userid = '".$roleInfo['userid']."'");
					$common->updateMemCache(MC.$roleInfo['playersid'],$updateRole); 
				}*/
				$updateRole['hyd'] = 0;//充值活跃度
				$updateRole['hyjl'] = '00000';
				$common->updatetable('player',$updateRole,"playersid = '".$roleInfo['playersid']."'");
				$common->updatetable('user',"last_login_time = '".$_SGLOBAL['timestamp']."'","userid = '".$roleInfo['userid']."'");
				$common->updateMemCache(MC.$roleInfo['playersid'],$updateRole); 				
			}
		}		
		$player_level = $roleInfo['player_level'];
		$completeQuests = $roleInfo['completeQuests']; //已完成任务
		if (!empty($completeQuests)) {
			$completeQuestsArray = explode(',',$completeQuests);
		} else {
			$completeQuestsArray = array();
		}
		/*$showlistRes = clientList::getQuestsList($playersid);
		if ($showlistRes['status'] == 0) {
			$showlist = $showlistRes['rw'];
			for ($i = 0 ;$i < count($showlist); $i++) {
				$showQuestID[] = $showlist[$i]['id'];
			}
			$item = "&& a.QuestID NOT IN (".implode(',',$showQuestID).")";
		} else {
			$item = '';
		}*/
		//$vip = $roleInfo['vip'];
		$vip_end_time = $roleInfo['vip_end_time'];
		$outQid = '1001308,1001309,1001310,1001311,1001312,1001313,1001314,1001104';
		if ($vip_end_time > $_SGLOBAL['timestamp']) {
			$vip = true;
		} else {
			$vip = false;
		}
		/*if ($vip == true || SYPT != 1) {
			$vItme = ",1001301,1001302,1001303,1001304";
		} else {
			$vItme = "";
		}*/
		$vItme = ",1001301,1001302,1001303,1001304,1001307,3000000,3000001,3000002,3001000,3002000,3003000,1001401,1001402,1001403,1001409,1001410,1001508,1001439";
		if ($_SGLOBAL['timestamp'] > $_SC['kfhdjs']) {
			$hdsql = "&& (IF ( Qstatus != 1 ,QuestID NOT IN(1001306) , 1))";
		} else {
			$hdsql = '';
		}		
		$pcrwid = $outQid.$vItme;
		$vipItme = "&& QuestID NOT IN ($pcrwid) $hdsql";		
		$array = array();
		//$addPage = 0;
		//$totalNum = $db->num_rows($db->query("SELECT a.QuestID FROM ".$common->tname('accepted_quests')." a INNER JOIN ".$common->tname('quests')." b ON a.playersid = '$playersid' && a.published = 1 $vipItme && a.QuestID = b.QuestID"));
		if ($sfrcrw == 1) {
			$totalPage = 1;
		} else {
			$totalNumRow = $db->fetch_array($db->query("SELECT count(QuestID) as rwsl FROM ".$common->tname('accepted_quests')." WHERE playersid = '$playersid' && published = 1 $rcItem $vipItme "));
			$totalNum = $totalNumRow['rwsl'];
			$totalPage = ceil($totalNum / $perPage);			
		}
		/*if ($addPage == 1 && $totalNum == 9) {
			$totalPage = $totalPage + 1;
		}*/
		if ($curPage > $totalPage && $curPage > 1) {
			$curPage = $totalPage;
		}
		$start = ($curPage - 1) * $perPage;
		//$result = $db->query("SELECT a.QuestID,a.readStatus,a.Qstatus,b.QTitle,a.ExtraData,b.completeDesc,b.showProcess FROM ".$common->tname('accepted_quests')." a INNER JOIN ".$common->tname('quests')." b ON a.playersid = '$playersid' && a.published = 1 $vipItme && a.QuestID = b.QuestID ORDER BY a.readStatus ASC,a.Qstatus DESC,a.intID DESC limit $start,$perPage");
		$result = $db->query("SELECT QuestID,readStatus,Qstatus,ExtraData FROM ".$common->tname('accepted_quests')." WHERE playersid = '$playersid' && published = 1 $rcItem $vipItme  ORDER BY readStatus ASC,Qstatus DESC,intID DESC limit $start,$perPage");
		while ($rows = $db->fetch_array($result)) {
			$rwinfo = ConfigLoader::GetQuestProto($rows['QuestID']);
			if (empty($rwinfo)) {
				continue;
			}
			$QTitle = $rwinfo['QTitle'];			
			$showProcess = $rwinfo['showProcess'];
			$completeDesc = $rwinfo['completeDesc'];
			if ($rows['Qstatus'] == 0 && $rows['readStatus'] == 0) {
				$new = 1;
			} elseif ($rows['Qstatus'] == 1) {
				$new = 2;
			} else {
				$new = 0;
			}
			if (in_array($rows['QuestID'],array(1001308,1001309,1001310,1001311,1001312,1001313,1001314))) {
				$name = str_replace('XXX','VIP'.($roleInfo['vip']+1),$QTitle);
			} else {
				$name = $QTitle;
			}
			
			//$showProcess = $rows['showProcess'];
			if ($rows['QuestID'] == 1001306) {
				$wcms = str_replace('XXX',date('Y-m-d H:i:s',$_SC['kfhdjs']),$completeDesc); 
			} else {
				$wcms = $completeDesc;                            //完成描述
			}
			if (!empty($rows['ExtraData'])) {
				$processInfo = json_decode($rows['ExtraData'],true);
			} else {
				$processInfo = '';
			}			
	        if(!empty($showProcess)) {
	        	$wcmsInfo = explode(';',$wcms);
	        	$showProcessInfo = explode(';',$showProcess);
	        	for ($i = 0; $i < count($showProcessInfo); $i++) {
	        		if (!empty($processInfo)) {
	        			$showContent[] = $wcmsInfo[$i].'('.$processInfo[$i].'/'.$showProcessInfo[$i].')';
	        		} else {
	        			$showContent[] = $wcmsInfo[$i].'(0/'.$showProcessInfo[$i].')';
	        		}	        		
	        	}
	        	$rwjd = implode(';',$showContent);
	        } else {
	        	$rwjd = $wcms;
	        }
	        $rwwc = intval($rows['Qstatus']);
			if ($rwwc == 1) {
				if ($rows['QuestID'] == 1001306) {
					$rwbj = str_replace('XXX',date('Y-m-d H:i:s',$_SC['kfhdjs']),$rwinfo['Description1']);
				} else {
					$rwbj = $rwinfo['Description1'];
				}				
			} else {
				if ($rows['QuestID'] == 1001306) {
					$rwbj = str_replace('XXX',date('Y-m-d H:i:s',$_SC['kfhdjs']),$rwinfo['Description']);
				} else { 				
					$rwbj = $rwinfo['Description'];
				}
			}
			$rwjl = getAward::rwjlms($rwinfo['Qaward']);	        
            //$value['rwjd'] = $rwjd;                         //任务进度						
			$array[] = array('id'=>intval($rows['QuestID']),'status'=>intval($new),'name'=>$name,'rwjd'=>$rwjd,'rwms'=>$rwbj,'zjhyd'=>5,'rwjl'=>$rwjl);	
			$new = NULL;
			$rwjd = NULL;
			$showContent = NULL;
			$wcms = NULL;		
			$processInfo = NULL;
			$showProcess = NULL;
			$wcmsInfo = NULL;
			$name = NULL;
			$QTitle = NULL;
			$completeDesc = NULL;
			$rwwc = NULL;
			$rwbj = NULL;
			$rwjl = NULL;
		}
		if (!empty($array)) {
			$value['status'] = 0;
			$value['totalPage'] = $totalPage;
			$value['curPage'] = $curPage;
			$value['rw'] = $array;
		} else {
			$value['status'] = 1021;
			$value['message'] = $rw_lang['rw_getRWList_1'];			
		}
		return $value;
	}

	//任务奖励描述
	public static function rwjlms($jlsj) {
		global $rw_lang;
		$jlInfo = json_decode($jlsj,true);
		foreach ($jlInfo as $key => $jlValue) {
			if ($key == 8 || $key == 9 || $key == 10) {
				$djInfo = explode('_',$jlValue);
				$itemId = $djInfo[0];
				$sl = $djInfo[1];
				if ($itemId == 900000) {
					$mc = $rw_lang['rw_getRWInfo_1'];
					$iid = 'Ico007';
					$rwjl[] = array('mc'=>$mc,'sl'=>intval($sl),'iid'=>$iid);
				} else {											
					$getDjInfo = toolsModel::getItemInfo($itemId);
					$mc = $getDjInfo['Name'];
					$iid = $getDjInfo['IconID'];
					// 修正道具品级，部分道具级别和Rarity字段描述有出入
					$xyd = toolsModel::getRealRarity($getDjInfo);	
					$rwjl[] = array('mc'=>$mc,'sl'=>intval($sl),'iid'=>$iid,'xyd'=>intval($xyd));
					$djInfo = null;
					$itemId = null;
					$getDjInfo = null;	
				}
				$sl = null;					
				$mc = null;
				$iid = null;				
			} elseif ($key == 5) {
				$mc = 'exp';
				$sl = $jlValue;
				$rwjl[] = array('mc'=>$mc,'sl'=>intval($sl));
				$mc = null;
				$sl = null;
			} /*elseif ($key == 7) {					
				$vipInfo = explode('_',$jlValue);
				$jb = $vipInfo[0];
				$sl = $vipInfo[1].$rw_lang['rw_getRWInfo_2'];
				$mc = 'VIP'.$jb;
				$iid = 'icon_vip_'.$jb;
				$rwjl[] = array('mc'=>$mc,'sl'=>$sl,'iid'=>$iid);	
				$mc = null;
				$sl = null;						
			} elseif ($key == 11) {
				$jb = $roleInfo['vip'] + 1;
				$sl = $jlValue.$rw_lang['rw_getRWInfo_2'];
				$mc = 'VIP'.$jb;
				$iid = 'icon_vip_'.$jb;
				$rwjl[] = array('mc'=>$mc,'sl'=>$sl,'iid'=>$iid);	
				$mc = null;
				$sl = null;										
			}*/ elseif ($key == 2) {
				$mc = $rw_lang['rw_getRWInfo_3'];
				$sl = $jlValue;
				$rwjl[] = array('mc'=>$mc,'sl'=>intval($sl),'iid'=>'icon_yp');	
				$mc = null;
				$sl = null;						
			} else {
				$mc = $rw_lang['rw_getRWInfo_4'];
				$sl = $jlValue;
				$rwjl[] = array('mc'=>$mc,'sl'=>intval($sl),'iid'=>'icon_tq');	
				$mc = null;
				$sl = null;									
			}
		}
		return $rwjl;	
	}	
	
	
	//读取单个任务
	public static function getRWInfo($playersid,$QuestID,$ht = '') {
		global $common,$db,$_SC,$rw_lang;
		//$result = $db->query("SELECT a.RepeatInterval,b.Qaward,b.showProcess,b.QTitle,b.Description,b.completeDesc,b.completeAwardDesc,b.Guide_Script,b.Description1,a.Qstatus,a.ExtraData,a.QuestID,a.readStatus FROM ".$common->tname('accepted_quests')." a,".$common->tname('quests')." b WHERE a.QuestID = b.QuestID && a.playersid = '$playersid' && a.QuestID = '$QuestID' LIMIT 1");
		$result = $db->query("SELECT Qstatus,ExtraData,QuestID,readStatus FROM ".$common->tname('accepted_quests')." WHERE playersid = '$playersid' && QuestID = '$QuestID' LIMIT 1");
		$rows = $db->fetch_array($result);
		if (!empty($rows)) {
			$rwinfo = ConfigLoader::GetQuestProto($rows['QuestID']);
			$value['status'] = 0;			
			$value['id'] = intval($rows['QuestID']);
			$roleInfo['playersid'] = $playersid;
			roleModel::getRoleInfo($roleInfo);			
			if (in_array($QuestID,array(1001308,1001309,1001310,1001311,1001312,1001313,1001314))) {
				$value['name'] = str_replace('XXX','VIP'.($roleInfo['vip']+1),$rwinfo['QTitle']);
			} else {
				$value['name'] = $rwinfo['QTitle'];
			}
			$rwwc = intval($rows['Qstatus']);
			$value['rwwc'] = $rwwc;
			if ($rows['readStatus'] == 0) {
				$value['s'] = 1;
			} else {
				$value['s'] = 0;
			}
			if ($rwwc == 1) {
				if ($QuestID == 1001306) {
					$value['rwbj'] = str_replace('XXX',date('Y-m-d H:i:s',$_SC['kfhdjs']),$rwinfo['Description1']);
				} else {
					$value['rwbj'] = $rwinfo['Description1'];
				}				
			} else {
				if ($QuestID == 1001306) {
					$value['rwbj'] = str_replace('XXX',date('Y-m-d H:i:s',$_SC['kfhdjs']),$rwinfo['Description']);
				} else { 				
					$value['rwbj'] = $rwinfo['Description'];
				}
			}
			//目前有的奖励 铜钱  角色经验 和道具
			////1元宝 2银两3军粮4铜钱5角色经验6武将经验7贵族8道具19道具2
			/*
			 * rwjl任务奖励嵌套
				mc	奖励的名称
				sl	奖励的数量
				iid	奖励内容的图标ID
				rwjl任务奖励嵌套结束
			 * */
			$rwjl = array();
			$jlInfo = json_decode($rwinfo['Qaward'],true);
			foreach ($jlInfo as $key => $jlValue) {
				if ($key == 8 || $key == 9 || $key == 10) {
					$djInfo = explode('_',$jlValue);
					$itemId = $djInfo[0];
					$sl = $djInfo[1];
					if ($itemId == 900000) {
						$mc = $rw_lang['rw_getRWInfo_1'];
						$iid = 'Ico007';
						$rwjl[] = array('mc'=>$mc,'sl'=>intval($sl),'iid'=>$iid);
					} else {											
						$getDjInfo = toolsModel::getItemInfo($itemId);
						$mc = $getDjInfo['Name'];
						$iid = $getDjInfo['IconID'];
						// 修正道具品级，部分道具级别和Rarity字段描述有出入
						$xyd = toolsModel::getRealRarity($getDjInfo);	
						$rwjl[] = array('mc'=>$mc,'sl'=>intval($sl),'iid'=>$iid,'xyd'=>intval($xyd));
						$djInfo = null;
						$itemId = null;
						$getDjInfo = null;	
					}
					$sl = null;					
					$mc = null;
					$iid = null;				
				} elseif ($key == 5) {
					$mc = 'exp';
					$sl = $jlValue;
					$rwjl[] = array('mc'=>$mc,'sl'=>intval($sl));
					$mc = null;
					$sl = null;
				} elseif ($key == 7) {					
					$vipInfo = explode('_',$jlValue);
					$jb = $vipInfo[0];
					$sl = $vipInfo[1].$rw_lang['rw_getRWInfo_2'];
					$mc = 'VIP'.$jb;
					$iid = 'icon_vip_'.$jb;
					$rwjl[] = array('mc'=>$mc,'sl'=>$sl,'iid'=>$iid);	
					$mc = null;
					$sl = null;						
				} elseif ($key == 11) {
					$jb = $roleInfo['vip'] + 1;
					$sl = $jlValue.$rw_lang['rw_getRWInfo_2'];
					$mc = 'VIP'.$jb;
					$iid = 'icon_vip_'.$jb;
					$rwjl[] = array('mc'=>$mc,'sl'=>$sl,'iid'=>$iid);	
					$mc = null;
					$sl = null;										
				} elseif ($key == 2) {
					$mc = $rw_lang['rw_getRWInfo_3'];
					$sl = $jlValue;
					$rwjl[] = array('mc'=>$mc,'sl'=>intval($sl),'iid'=>'icon_yp');	
					$mc = null;
					$sl = null;						
				} else {
					$mc = $rw_lang['rw_getRWInfo_4'];
					$sl = $jlValue;
					$rwjl[] = array('mc'=>$mc,'sl'=>intval($sl),'iid'=>'icon_tq');	
					$mc = null;
					$sl = null;									
				}
			}
			$value['rwjl'] = $rwjl;
			$showProcess = $rwinfo['showProcess'];
			if ($QuestID == 1001306) {
				$wcms = str_replace('XXX',date('Y-m-d H:i:s',$_SC['kfhdjs']),$rwinfo['completeDesc']); 
			} else {
				$wcms = $rwinfo['completeDesc'];                            //完成描述
			}
			if (!empty($rows['ExtraData'])) {
				$processInfo = json_decode($rows['ExtraData'],true);
			} else {
				$processInfo = '';
			}			
	        if(!empty($showProcess)) {
	        	$wcmsInfo = explode(';',$wcms);
	        	$showProcessInfo = explode(';',$showProcess);
	        	for ($i = 0; $i < count($showProcessInfo); $i++) {
	        		if (!empty($processInfo)) {
	        			$showContent[] = $wcmsInfo[$i].'('.$processInfo[$i].'/'.$showProcessInfo[$i].')';
	        		} else {
	        			$showContent[] = $wcmsInfo[$i].'(0/'.$showProcessInfo[$i].')';
	        		}	        		
	        	}
	        	$rwjd = implode(';',$showContent);
	        } else {
	        	$rwjd = $wcms;
	        }
            $value['rwjd'] = $rwjd;                         //任务进度
            //$value['rwjl'] = $rows['completeAwardDesc'];    //任务奖励
            //$value['rwts'] = '';         //任务引导
			if (empty($ht)) {
				if ($rwinfo['RepeatInterval'] == 1) {			
					getDataAboutLevel::addQuestsStatus($playersid,$QuestID,4);
				} else {
					getDataAboutLevel::addQuestsStatus($playersid,$QuestID,3);
				}
			}   
			//完成引导脚本&&接收新的引导
			/*if ($_SESSION['player_level'] == 1) {
				$xyd = guideScript::wcjb($roleInfo,'ckrw',1,1);
				//接收新的引导
				$xyd = guideScript::xsjb($roleInfo,'cg',1);
				if ($xyd !== false) {
					$value['ydts'] = $xyd;
				}	
			}*/	
			$common->updatetable('accepted_quests',"readStatus = '1'","playersid = '$playersid' && QuestID = '$QuestID' LIMIT 1");		
			/*$xwzt = '';
			if ($QuestID == 1001500) {  //内修经济任务信息行为
				$xwzt_1 = substr($roleInfo['xwzt'],0,1);
				if ($xwzt_1 == 0) {
					$xwzt = substr_replace($roleInfo['xwzt'],'1',0,1);
				}
			} elseif ($QuestID == 1001507) {  //学习技能任务信息行为
				$xwzt_2 = substr($roleInfo['xwzt'],1,1);
				if ($xwzt_2 == 0) {
					$xwzt = substr_replace($roleInfo['xwzt'],'1',1,1);
				}				
			} elseif ($QuestID == 1001502) {  //查看武将训练任务信息行为
				$xwzt_3 = substr($roleInfo['xwzt'],2,1);
				if ($xwzt_3 == 0) {
					$xwzt = substr_replace($roleInfo['xwzt'],'1',2,1);
				}				
			}
			if (!empty($xwzt)) {
				$common->updatetable('player',"xwzt = '$xwzt'","playersid = '$playersid' LIMIT 1");
				$common->updateMemCache(MC.$playersid,array('xwzt'=>$xwzt));
				//$value['xwzt'] = $xwzt;				
			} */   
		} else {
			getDataAboutLevel::scrwzt($playersid,$QuestID,1);//如果没有任务则删除此任务状态数据
			$value['status'] = 21;
			$value['message'] = $rw_lang['rw_getRWInfo_21'];
		}
		//$common->deletetable('quests_status',"playersid = '$playersid' && QuestID = '$QuestID' LIMIT 1");
		//getDataAboutLevel::scrwzt($playersid,$QuestID);
		
		//echo json_encode($value);
		//print_r($value);
		return $value;
	}

	//刷新日常任务
	public static function sxrcrw($playersid,$hyp=1) {
		global $mc, $db, $common, $G_PlayerMgr, $sys_lang, $rw_lang;
		$player = $G_PlayerMgr->GetPlayer($playersid);
		if (empty($player)) {
			return array('status'=>30,'message'=>$sys_lang[1]);
		} else {
			$hfyp = 10;  //需要花费的银票
			$roleInfo = $player->baseinfo_;
			$yp = $roleInfo['silver'];
			if ($yp < $hfyp && $hyp == 1) {
				$arr1 = array('{xhyp}','{yp}');
				$arr2 = array($hfyp,$yp);
				return array('status'=>68,'yp'=>floor ( $roleInfo ['silver'] ),'message'=>str_replace($arr1,$arr2,$sys_lang[6]),'xyxhyp' => $hfyp);                                        				
			} else {
				if ($hyp == 2) {
					$roleInfo['player_level'] = 8;
				}
				$easy = array(2002000,2003001,2003002,2004015,2005001,2004000,2004001,2004003,2004004,2001018,2004017,2004012,2001000,2003000);
				//$rwData = ConfigLoader::GetAllQuests();  //获取任务基表数据
				if (!($qStatus = $mc->get(MC.$playersid."_qstatus"))) {
					$sql = "SELECT * FROM ".$common->tname('quests_new_status')." WHERE playersid = '$playersid' LIMIT 1";
					$result = $db->query($sql);
					$rows = $db->fetch_array($result);
					if (empty($rows['qstatusInfo'])) {
						$acceptQarray = array();
					} else {
						$acceptQarray = unserialize($rows['qstatusInfo']);	
					}			
				} else {
					$acceptQarray = unserialize($qStatus);
				}
				if (!empty($acceptQarray)) {
					$canused_1 = array();
					foreach ($acceptQarray as $key => $acceptQarrayValue) {
						$rwxx = ConfigLoader::GetQuestProto($key);
						/*if ($roleInfo['player_level'] > 14) {
							if (in_array($acceptQarrayValue,array(4,5,6)) && $rwxx['Level_Min'] <= $roleInfo['player_level'] && $roleInfo['player_level'] <= $rwxx['Level_Max']) {
								$canused[$key] = $key;
							}
						} else {
							if (in_array($acceptQarrayValue,array(4,5,6)) && $rwxx['Level_Min'] <= $roleInfo['player_level'] && $roleInfo['player_level'] <= $rwxx['Level_Max'] && in_array($key,$easy)) {
								$canused[$key] = $key;
							}							
						}*/
						if (in_array($acceptQarrayValue,array(1,4,5,6)) && $rwxx['Level_Min'] <= $roleInfo['player_level'] && $roleInfo['player_level'] <= $rwxx['Level_Max'] && in_array($key,$easy) && $rwxx['RepeatInterval'] == 1) {
							$canused_1[$key] = $key;
						}							
					}
					$kysl = count($canused_1);
					$canused_2 = array();
					if ($kysl < 3) {
						foreach ($acceptQarray as $key_2 => $acceptQarrayValue_2) {
							$rwxx = ConfigLoader::GetQuestProto($key_2);
							if (in_array($acceptQarrayValue_2,array(1,4,5,6)) && $rwxx['Level_Min'] <= $roleInfo['player_level'] && $roleInfo['player_level'] <= $rwxx['Level_Max'] && !in_array($key_2,$canused_1) && $rwxx['RepeatInterval'] == 1) {
								$canused_2[$key_2] = $key_2;
							}											
						}						
					}
					$canused = $canused_1 + $canused_2;
					if (!empty($canused)) {
						if (count($canused) > 3) {
							$chosedKey = array_rand($canused,3);
						} else {							
							$chosedKey = $canused;
						}
						$xrwid = array();
						foreach ($canused as $kk => $kkvale) {
							if (in_array($kk,$chosedKey)) {
								$rczt[$kk] = 5;
								$xrwid[] = $kkvale;
							} else {
								$rczt[$kk] = 6;
							}
						}
						/*foreach ($chosedKey as $kk => $kkvale) {
							$rczt[$kkvale] = 5;
							$xrwid[] = $kkvale;
						}*/
						
						//print_r($xrwid);
						$xzt = $rczt + $acceptQarray;
						$xzt = serialize($xzt);				
						$db->query("UPDATE ".$common->tname('quests_new_status')." SET qstatusInfo = '$xzt' WHERE playersid = $playersid");
						$mc->set(MC.$playersid."_qstatus",$xzt,0,3600);	
						if (!empty($xrwid)) {
							$db->query("UPDATE ".$common->tname('accepted_quests')." SET published = (IF (QuestID IN (".implode(',',$xrwid)."),1,0)) WHERE playersid = $playersid && RepeatInterval = 1");	
						}
						//echo "select * from ".$common->tname('accepted_quests')." WHERE playersid = $playersid && RepeatInterval = 1 && QuestID IN (".implode(',',$xrwid).")";
						if ($hyp == 1) {	
							$xyp = $yp - $hfyp;					
							$updateRole['silver'] = $xyp;
							$updateRoleWhere['playersid'] = $playersid;
							$common->updatetable('player',$updateRole,$updateRoleWhere);
							$common->updateMemCache(MC.$playersid,$updateRole);		
							$rwInfo = getAward::getRWList($playersid,1,1);
							$rwInfo['yp'] = $xyp;
							$rwInfo['xhyp'] = $hfyp;
							return $rwInfo; 	
						}													
					} else {
						return array('status'=>30,'message'=>$rw_lang['sxrcrw_1']);
					}
				} else {
					return array('status'=>30,'message'=>$rw_lang['sxrcrw_1']);
				}
				/*$rcrwData = array();
				foreach ($rwData as $rwDataValue) {
					if ($rwDataValue['RepeatInterval'] == 1) {
						if (isset($acceptQarray[$rwDataValue['QuestID']])) {
							$rcrwData[] = $rwDataValue['QuestID'];
						}
					}
				}
				if (!empty($rcrwData)) {
					if (count($rcrwData) > 3) {
						$chosedKey = array_rand($rcrwData,3);
						foreach ($chosedKey as $chosedKeyValue) {
							$xrwid[] = $rcrwData[$chosedKeyValue];
						}
						foreach ($rcrwData as $rcrwDataValue) {
							if (in_array($rcrwDataValue,$xrwid)) {
								$rczt[$rcrwDataValue] = 5;
							} else {
								$rczt[$rcrwDataValue] = 6;
							}							
						}
						$xzt = serialize($acceptQarray + $rczt);						
						$db->query("UPDATE ".$common->tname('quests_new_status')." SET qstatusInfo = '$xzt' WHERE playersid = $playersid");
						$mc->set(MC.$playersid."qstatus",$xzt,0,3600);	
						$db->query("UPDATE ".$common->tname('accepted_quests')." SET published = (IF (QuestID IN (".implode(',',$xrwid)."),1,0)) WHERE playersid = $playersid && RepeatInterval = 1");					
					}
					$rwInfo = getAward::getRWList($playersid,1,1);
					$rwInfo['yp'] = $yp - $hfyp;
					$updateRole['silver'] = $rwInfo['yp'];
					$updateRoleWhere['playersid'] = $playersid;
					$common->updatetable('player',$updateRole,$updateRoleWhere);
					$common->updateMemCache(MC.$playersid,$updateRole);
					return $rwInfo; 			
				} else {
					return array('status'=>30,'message'=>$rw_lang['sxrcrw_1']);
				}	*/		
			}
		}		
	}
	
	//获取活跃度奖励
	public static function hqhyjl($playersid,$jlid) {
		global $mc, $db, $common, $G_PlayerMgr, $sys_lang, $rw_lang, $_SGLOBAL;
		$player = $G_PlayerMgr->GetPlayer($playersid);
		if (empty($player)) {
			return array('status'=>30,'message'=>$sys_lang[1]);
		} else {
			$roleInfo = $player->baseinfo_;
			$hyjl = $roleInfo['hyjl'];
			$hyd = array(1=>5,2=>30,3=>60,4=>100,5=>150);
			switch ($jlid) {
				case 1:
					$jlInfo = array('lx'=>1,'id'=>10001,'sl'=>1);
					break;
				case 2:
					$jlInfo = array('lx'=>1,'id'=>20057,'sl'=>8);
					break;
				case 3:
					$jlInfo = array('lx'=>1,'id'=>20057,'sl'=>12);
					break;	
				case 4:
					$jlInfo = array('lx'=>2,'sl'=>1);
					break;	
				case 5:
					$jlInfo = array('lx'=>3,'id'=>18610,'sl'=>1);
					break;	
				default:
					$jlInfo = NULL;
					break;																											
			}
			if (empty($jlInfo)) {
				return array('status'=>30,'message'=>$sys_lang[7]);
			} 			
			$jl = array();
			if ($jlInfo['lx'] === 3) {
				$getDjInfo = toolsModel::getItemInfo($jlInfo['id']);
				$djmc = $getDjInfo['Name'];
				$iid = $getDjInfo['IconID'];
				$jl[] = array('mc'=>$djmc,'sl'=>intval($jlInfo['sl']),'iid'=>$iid);
				$jl[] = array('mc'=>$rw_lang['hqhyjl_3'],'sl'=>100,'iid'=>'icon_sw');				
			} elseif ($jlInfo['lx'] === 2) {
				$djmc = $rw_lang['rw_getRWInfo_1'];
				$iid = 'Ico007';
				$jl[] = array('mc'=>$djmc,'sl'=>intval($jlInfo['sl']),'iid'=>$iid);				
			} else {
				$getDjInfo = toolsModel::getItemInfo($jlInfo['id']);
				$djmc = $getDjInfo['Name'];
				$iid = $getDjInfo['IconID'];	
				$jl[] = array('mc'=>$djmc,'sl'=>intval($jlInfo['sl']),'iid'=>$iid);			
			}
			$value['boxjl']	= $jl;		
			if (!empty($_SESSION['loginTime'])) {
				//是否全新一天
				if (date('Y-m-d',$_SESSION['loginTime']) != date('Y-m-d',$_SGLOBAL['timestamp'])) {
					$_SESSION['qxyt'] = 1;
					$_SESSION['loginTime'] = $_SGLOBAL['timestamp'];
					$uprwres = roleModel::hqrcrw($roleInfo);
					/*if ($uprwres == true){
						$updateRole['rcrws'] = 3;//设置更新日常任务的条数
						$common->updatetable('player',$updateRole,"playersid = '".$roleInfo['playersid']."'");
						$common->updatetable('user',"last_login_time = '".$_SGLOBAL['timestamp']."'","userid = '".$roleInfo['userid']."'");
						$common->updateMemCache(MC.$roleInfo['playersid'],$updateRole); 
					}*/
					$updateRole['hyd'] = 0;//充值活跃度
					$updateRole['hyjl'] = '00000';
					$common->updatetable('player',$updateRole,"playersid = '".$roleInfo['playersid']."'");
					$common->updatetable('user',"last_login_time = '".$_SGLOBAL['timestamp']."'","userid = '".$roleInfo['userid']."'");
					$common->updateMemCache(MC.$roleInfo['playersid'],$updateRole); 	
					$value['status'] = 1001;					
					return $value;		
				}
			}				
			if ($hyd[$jlid] > $roleInfo['hyd']) {
				$value['status'] = 1001;
				return $value;
				//return array('status'=>30,'message'=>$rw_lang['hqhyjl_1']);
			} else {
				if (substr($hyjl,$jlid-1,1) == 1) {
					return array('status'=>30,'message'=>$rw_lang['hqhyjl_2']);
				} else {
					$value['status'] = 0;
					if ($jlInfo['lx'] === 3 || $jlInfo['lx'] === 1) {
						$stat = $player->AddItems(array($jlInfo['id']=>$jlInfo['sl']));
						if ($stat !== false) {
							$bagData = $player->GetClientBag();
							if ($jlInfo['lx'] === 3) {
								$updateRole['prestige'] = $roleInfo['prestige'] + 100;
								$value['sw'] = intval($updateRole['prestige']);
							}
							$value['list'] = $bagData;	
						} else {
							return array('status' => 30,'message' => $rw_lang['rw_processAward_2']);
						}
					} else {
						$djid = rand(1,15);
						$jndyid = jndyid($djid);
						$stat = $player->AddItems(array($jndyid=>$jlInfo['sl']));
						if ($stat !== false) {
							$bagData = $player->GetClientBag();
							$value['list'] = $bagData;								
						} else {
							return array('status' => 30,'message' => $rw_lang['rw_processAward_2']);
						}
					}			
					$updateRole['hyjl'] = substr_replace($hyjl,1,$jlid-1,1);
					$common->updatetable('player',$updateRole,array('playersid'=>$playersid));
					$common->updateMemCache(MC.$playersid,$updateRole);
					return $value;					
				}
			}
		}		
	}
}

//引导相关
class guideScript {    
    /*
     * 测试玩家经验升级
     * 
     * **/
   public static function upgradeTest($playersid,$experienceValue) {
   	    global $common;
    	$debug = _get('debug');
    	if (defined("PRESSURE_TEST_DEBUG") && !empty($debug)) {    	
    	    $roleInfo['playersid'] = $playersid;
    	    roleModel::getRoleInfo($roleInfo);
    	    $current_experience_value = $roleInfo['current_experience_value'];
    	    $player_level = $roleInfo['player_level'];
    	    if ($experienceValue > 0) {    	    	
	        	$current_experience_value_total = $current_experience_value + $experienceValue; //当前玩家总经验
	            $value['status'] = 0;
	        	if ($player_level < 70) {
			       //判断玩家是否能够升级，以及剩余经验值
			       $upgradePlayer = fightModel::upgradeRole($player_level,$current_experience_value_total,1);
			       $updateRole['current_experience_value'] = $upgradePlayer['left'];	
			       //判断角色是否升级
			       if ($upgradePlayer['level'] > $player_level) {
			            $updateRole['player_level'] = $upgradePlayer['level'];
			            $newPlayerLevel = $updateRole['player_level'];
			            $value['upgrade'] = 1;
			            $value['level'] = $upgradePlayer['level'];      //玩家当前级别                      
			            $value['jlsx'] = foodUplimit($newPlayerLevel);
			            $roleInfo['player_level'] = $upgradePlayer['level'];
			            $_SESSION['player_level'] = $roleInfo['player_level'];	
	  
			       		if ($roleInfo['food'] < $value['jlsx']) {
			            	$updateRole['food'] = $value['jlsx'];
							$updateRole['last_update_food'] = time();
			            	$value['jl'] = floor($value['jlsx']);
			            	$roleInfo['food'] = $value['jlsx'];
			            }	
			            $player_level = $roleInfo['player_level'];
	                    $value['jzzt'] = cityModel::jzzt($roleInfo['playersid']);
			       } else {
			           $value['upgrade'] = 0;                                               //玩家未升级		           
			      } 
			      $value['jy'] = $updateRole['current_experience_value'];     			      
			   }           	
        	} else {
	     		$value['status'] = 3;
	    		$value['message'] = '经验值有误';       		
        	} 
        	if (!empty($updateRole)) {
        		$updateRoleWhere['playersid'] = $playersid;
        		$common->updateMemCache(MC.$playersid,$updateRole);
        		$common->updatetable('player',$updateRole,$updateRoleWhere);
        	}      	
    	} else {
    		$value['status'] = 3;
    		$value['message'] = '非法请求';
    	}   
    	return $value; 	
    }  
}

?>