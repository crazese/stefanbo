<?php
class questsController {
	//任务控制函数$roleInfo:角色基本信息，$changeVar变更的参数
	public static function OnAccept($roleInfo,$changeVar) {
		global $common,$db,$_SGLOBAL,$mc;
		if (empty($roleInfo['playersid']) || empty($changeVar)) {
			return false;
		}		
		$PlayersId = $roleInfo['playersid'];                //玩家ID
		$Level = $roleInfo['player_level'];                 //玩家等级	
		$sqlRegionid = '';	
		$regionid = $roleInfo['regionid'];                  //势力
		if ($regionid == 0) {
			$regionid = 1;                                  //沧州
		} else {
			$regionid = 2;                   //梁山
		}
		$completeQuests = $roleInfo['completeQuests'];      //已完成任务
		$completeQuestsArray = array();
		$item = '';
		if (!empty($completeQuests)) {
			$item = "&& QuestID NOT IN ($completeQuests)";
			$completeQuestsArray = explode(',',$completeQuests);
		} 
		$nowTime = $_SGLOBAL['timestamp']; 		
		if (!($qStatus = $mc->get(MC.$PlayersId."_qstatus"))) {
			$sql = "SELECT * FROM ".$common->tname('quests_new_status')." WHERE playersid = '$PlayersId' LIMIT 1";
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
			foreach ($acceptQarray as $acceptKey => $acceptQarrayValue) {
				$keyArra[] = $acceptKey;
			}			
			$keyArraStr = implode(',',$keyArra);
			if (!empty($completeQuests)) {
				$keyArraStr = $completeQuests.','.$keyArraStr;
			}
			$item = "&& QuestID NOT IN ($keyArraStr)";
		} else {
			$keyArra = array();
		}
		//$pcqid = array_merge($completeQuestsArray,$keyArra);	
	    //$dailyRes = $db->query("SELECT * FROM ".$common->tname('daily_quests')." WHERE playersid = '$PlayersId'");
	    //$outQuestsID = array();
		//$qzQuestsID = array();
		/*while ($dailyRows = $db->fetch_array($dailyRes)) {
			$i_time = $dailyRows['i_time'];         //数据插入时间
		    $q_status = $dailyRows['q_status'];     //是接受异或完成状态
			$intID = $dailyRows['intID'];
			if ($q_status == 1) {
				$qzQuestsID[] = $dailyRows['QuestID'];	
			}                    
			if ($q_status == 2) {
				$outQuestsID[] = $dailyRows['QuestID'];
			}
		}*/	
		//$rwData = getDataAboutLevel::hqrwjbsj();  //获取任务基表数据
		$rwData = ConfigLoader::GetAllQuests();  //获取任务基表数据
		//heroCommon::insertLog($changeVar);
		$changeVar = explode(',',$changeVar);
		//$finishedQuestID = $roleInfo['$finishedQuestID'];
		//$questsListRes = $db->query("SELECT * FROM ".$common->tname('quests')." WHERE Level_Min  <= '$Level' && Level_Max >= '$Level' && (AcceptVar1 in ($changeVar) || AcceptVar2 in ($changeVar) || AcceptVar3 in ($changeVar) || AcceptVar4 in ($changeVar)) $sqlRegionid && `public_status` = '1' && QuestID NOT IN (SELECT QuestID FROM ".$common->tname('accepted_quests')." WHERE playersid = '$PlayersId') $item");
		//$questsListRes = $db->query("SELECT * FROM ".$common->tname('quests')." WHERE Level_Min  <= '$Level' && Level_Max >= '$Level' && (AcceptVar1 in ($changeVar) || AcceptVar2 in ($changeVar) || AcceptVar3 in ($changeVar) || AcceptVar4 in ($changeVar)) $sqlRegionid && `public_status` = '1' $item");
		//$log = "SELECT * FROM ".$common->tname('quests')." WHERE Level_Min  <= '$Level' && Level_Max >= '$Level' && (AcceptVar1 in ($changeVar) || AcceptVar2 in ($changeVar) || AcceptVar3 in ($changeVar) || AcceptVar4 in ($changeVar)) $sqlRegionid && `public_status` = '1' $item";
		//heroCommon::insertLog('acc+'.$log);
		$completeQuestsID = array();
		//while ($questsListRows = $db->fetch_array($questsListRes)) {
		foreach ($rwData as $questsListRows) {
			$AcceptVar1 = $AcceptVar2 = $AcceptVar3 = $AcceptVar4 = null;
			$AcceptVar1 = "'".$questsListRows['AcceptVar1']."'";
			$AcceptVar2 = "'".$questsListRows['AcceptVar2']."'";
			$AcceptVar3 = "'".$questsListRows['AcceptVar3']."'";
			$AcceptVar4 = "'".$questsListRows['AcceptVar4']."'";	
			if ($questsListRows['RepeatInterval'] == 1 || $questsListRows['public_status'] == 0) {
				continue;
			}						
			if ($questsListRows['Level_Min'] <= $Level && $questsListRows['Level_Max'] >= $Level && !in_array($questsListRows['QuestID'],$completeQuestsArray) && !in_array($questsListRows['QuestID'],$keyArra) && (in_array($AcceptVar1,$changeVar) || in_array($AcceptVar2,$changeVar) || in_array($AcceptVar3,$changeVar) || in_array($AcceptVar4,$changeVar))) {			
				$Depended_Quest_ID = $OnAcceptParameter = $AcceptScript = $QuestID = null;
			    $FinishVar1 = $FinishVar2 = $FinishVar3 =$FinishVar4 = $FinishScript = $OnFinishParameter = $RepeatInterval = $q_type = $Qstart = $acceptTimes = $Level_Max = $Level_Min = $showProcess = null;
				$Depended_Quest_ID = $questsListRows['Depended_Quest_ID'];    //前置任务
			    //$Region_ID = $questsListRows['Region_ID'];                    //允许势力		    
			    $OnAcceptParameter = $questsListRows['OnAcceptParameter'];    //传递给函数固定参数   
			    $AcceptScript = $questsListRows['AcceptScript'];              //执行脚本，即对应的类名
			    $QuestID = $questsListRows['QuestID'];                        //任务ID
				/*if (SYPT == 1 && in_array($QuestID,array(1001308,1001309,1001310,1001311,1001312,1001313,1001314,1001305,1001306))) {
					continue;
				}*/		
				if (in_array($QuestID,array(1001308,1001309,1001310,1001311,1001312,1001313,1001314))) {
					if (!empty($roleInfo['vip'])) {
						if ($roleInfo['vip'] > 3) {
							continue;
						}
					}
				}			    	    
			    //$AcceptVar2 = $questsListRows['AcceptVar2'];
			    $FinishVar1 = $questsListRows['FinishVar1'];
			    $FinishVar2 = $questsListRows['FinishVar2'];
			    $FinishVar3 = $questsListRows['FinishVar3'];
			    $FinishVar4 = $questsListRows['FinishVar4'];
			    $FinishScript = $questsListRows['FinishScript'];
			    $OnFinishParameter = $questsListRows['OnFinishParameter'];
			    $RepeatInterval = $questsListRows['RepeatInterval'];
			    $q_type = $questsListRows['QType'];
			    $Qstart = $questsListRows['Qstart'];
			    $acceptTimes = $questsListRows['acceptTimes'];
			    $Level_Max = $questsListRows['Level_Max'];
			    $Level_Min = $questsListRows['Level_Min'];
			    $showProcess = $questsListRows['showProcess'];
			    $mblx = $questsListRows['mblx'];
			    $jcqz = questsController::jcqz($Depended_Quest_ID,$completeQuestsArray);
				if (($RepeatInterval == 0 || $RepeatInterval == 2) && ($jcqz === true || $Depended_Quest_ID == 0)) {
					if ($jcqz === true || $Depended_Quest_ID == 0) {
						//$res = $AcceptScript::getAccept($roleInfo,$QuestID,$AcceptVar2,$FinishScript,$OnFinishParameter,$FinishVar1,$FinishVar2,$FinishVar3,$FinishVar4,$OnAcceptParameter,$RepeatInterval,$Level_Min,$Level_Max,'','',$showProcess);
						$res = call_user_func_array($AcceptScript.'::getAccept', array($roleInfo,$QuestID,$AcceptVar2,$FinishScript,$OnFinishParameter,$FinishVar1,$FinishVar2,$FinishVar3,$FinishVar4,$OnAcceptParameter,$RepeatInterval,$Level_Min,$Level_Max,'','',$showProcess,$mblx));
					    /*if (!empty($res)) {
					    	$completeQuestsID[] = $Region_ID;
					    }*/
					}
				}
				$jcqz = null;				
			}
			/*if (($Region_ID == 0 || $Region_ID == $regionid) && $RepeatInterval == 1) {
				$checkTimes = collectAllRes::checkAccTimes($PlayersId,$QuestID,$acceptTimes);
				if (!in_array($QuestID,$outQuestsID) && !in_array($QuestID,$qzQuestsID) && $checkTimes != 4 && (in_array($Depended_Quest_ID,$qzQuestsID) || in_array($Depended_Quest_ID,$completeQuestsArray) || $Depended_Quest_ID == 0)) {
					if ($Qstart == 2) {
				    	$common->deletetable('daily_quests',"playersid = '$PlayersId' && ((q_type = '22' && q_status = '1') || q_type = '21')");
				        $common->deletetable('quests_status',"playersid = '$PlayersId' && QuestID IN (SELECT QuestID FROM ".$common->tname('quests')." WHERE QType = '21')");
				        $common->deletetable('accepted_quests',"playersid = '$PlayersId' && QuestID IN (SELECT QuestID FROM ".$common->tname('quests')." WHERE QType = '21')");
				    }
				    if ($checkTimes != 6) {	
					    if ($checkTimes == 2) {
					    	collectAllRes::addAccTimes($PlayersId,$QuestID,$Level_Max);
					    } else {
					    	collectAllRes::updateAccTimes($PlayersId,$QuestID,$checkTimes);
					    }
				    }			
					$res = $AcceptScript::getAccept($roleInfo,$QuestID,$AcceptVar2,$FinishScript,$OnFinishParameter,$FinishVar1,$FinishVar2,$FinishVar3,$FinishVar4,$OnAcceptParameter,$RepeatInterval);
					if (!empty($res)) {
				    	$completeQuestsID[] = $Region_ID;
				    }					
					$common->inserttable('daily_quests',array('playersid' => $PlayersId,'QuestID' => $QuestID,'q_status' => '2','i_time'=>$nowTime,'q_type'=>$q_type));						    	
				}					
			}*/			
			//echo $QuestID.'hello<br>';		    
			//检查是否达到接受条件
		}
		//unset($outQuestsID);
		//unset($qzQuestsID);
		/*if (!empty($completeQuestsID)) {
			return $completeQuestsID[0];
		}*/
	}
	//检查前置任务是否正确
	public static function jcqz($Depended_Quest_ID,$completeQuestsArray) {
		if ($Depended_Quest_ID === 0) {
			return false;
		} elseif ($completeQuestsArray == '') {
			return false;			
		} else {
			$Depended_Quest_ID_Array = explode(',',$Depended_Quest_ID);
			if (!is_array($completeQuestsArray)) {
				$completeQuestsArray = array($completeQuestsArray);
			}
			if (is_array($Depended_Quest_ID_Array)) {
				$ok = array();
				foreach ($Depended_Quest_ID_Array as $Depended_Quest_ID_Array_Value) {
					if (in_array($Depended_Quest_ID_Array_Value,$completeQuestsArray)) {
						$ok[] = 1;
					}
				}
				if (count($ok) == count($Depended_Quest_ID_Array)) {
					return true;
				} else {
					return false;
				}
			} else {
				if (in_array($Depended_Quest_ID,$completeQuestsArray)) {
					return true;
				} else {
					return false;
				}
			}
		}
	}
	
	//任务完成
	public static function OnFinish($playerInfo,$changeVar) {
		global $common,$db,$mc,$_SGLOBAL,$_SC;
		$nowTime = $_SGLOBAL['timestamp'];
		if (!empty($playerInfo['playersid'])) {
			$PlayersId = $playerInfo['playersid'];
			$roleInfo['playersid'] = $PlayersId;
			roleModel::getRoleInfo($roleInfo);
			$vip = $roleInfo['vip']; 			
		} else {
			//$PlayersId = 0;
			return false;
		}
		if (empty($changeVar)) {
			return false;
		}
		if (!empty($playerInfo['completeQuests'])) {
			$completeQuests = $playerInfo['completeQuests'];      //已完成任务
			$completeQuestsArray = explode(',',$completeQuests);
		} else {
			$completeQuests = '';
			$completeQuestsArray = array();
		}
		//$completeQuestsArray = explode(',',$completeQuests);
		$item = '';
		if (!empty($completeQuests) && $completeQuests != 0) {
			$item = "&& QuestID NOT IN ($completeQuests)";
		}		
		//$rwData = getDataAboutLevel::hqrwjbsj();  //获取任务基表数据
		$rwData = ConfigLoader::GetAllQuests();
		//获取已接收任务列表
		if (!($qStatus = $mc->get(MC.$PlayersId."_qstatus"))) {
			$sql = "SELECT * FROM ".$common->tname('quests_new_status')." WHERE playersid = '$PlayersId' LIMIT 1";
			$result = $db->query($sql);
			$rows = $db->fetch_array($result);
			$qStatus = $rows['qstatusInfo'];			
		}
		if (!empty($qStatus)) {
			$qstatusInfo = unserialize($qStatus);
			if (count($qstatusInfo) == 0) {
				return false;
			}
		} else {
			return false;
		}		
		
		//获取已接收任务列表结束
		
		//$finishListRes = $db->query("SELECT * FROM ".$common->tname('accepted_quests')." WHERE playersid = '$PlayersId' && `Qstatus` != 1 $item && (FinishVar1 in ($changeVar) || FinishVar2 in ($changeVar) || FinishVar3 in ($changeVar) || FinishVar4 in ($changeVar)) ORDER BY RepeatInterval ASC");
		//$str = "SELECT * FROM ".$common->tname('accepted_quests')." WHERE playersid = '$PlayersId' && `Qstatus` != 1 $item && (FinishVar1 in ($changeVar) || FinishVar2 in ($changeVar) || FinishVar3 in ($changeVar) || FinishVar4 in ($changeVar))";
		//heroCommon::insertLog('fin+'.$str,'sql');
		$Presult = false;
		$published = false;
		$QuestID = false;
        asort($qstatusInfo);
        $changeVar = explode(',',$changeVar);
		//while ($finishListRows = $db->fetch_array($finishListRes)) {
		foreach ($qstatusInfo as $qstatusInfoKey => $qstatusInfoValue) {
		    if ($qstatusInfoValue != 1 && !in_array($qstatusInfoKey,$completeQuestsArray)) {
		    	if (empty($rwData[$qstatusInfoKey])) {
		    		//return false;
		    		//break;
		    		continue;
		    	}	    	
		    	$finishListRows = $rwData[$qstatusInfoKey];   
		    	if ($finishListRows['public_status'] == 0) {
		    		continue;
		    	} 	
		    	$FinishVar1 = "'".$finishListRows['FinishVar1']."'";
		    	$FinishVar2 = "'".$finishListRows['FinishVar2']."'";
		    	$FinishVar3 = "'".$finishListRows['FinishVar3']."'";
		    	$FinishVar4 = "'".$finishListRows['FinishVar4']."'";
		    	if (in_array($FinishVar1,$changeVar) || in_array($FinishVar2,$changeVar) || in_array($FinishVar3,$changeVar) || in_array($FinishVar4,$changeVar)) {
			    	$intID = '';
				    //$intID = $finishListRows['intID'];
					//$FinishVar1 = $finishListRows['FinishVar1'];
				    //$FinishVar2 = $finishListRows['FinishVar2'];
				    //$FinishVar3 = $finishListRows['FinishVar3'];
				    //$FinishVar4 = $finishListRows['FinishVar4'];
				    $FinishScript = null;		    
				    $FinishScript = $finishListRows['FinishScript'];
				    $ExtraData = null;
				    //$ExtraData = $finishListRows['ExtraData'];     //保存任务进度
				    $ExtraData = getDataAboutLevel::hqrwjd($PlayersId,$qstatusInfoKey);
				    $OnFinishParameter = null;
				    $OnFinishParameter = $finishListRows['OnFinishParameter'];	
				    $QuestID = null;
				    $QuestID = $finishListRows['QuestID'];	
			    	if ($QuestID == 1001306 && $nowTime > $_SC['kfhdjs']) {
			    		continue;
			    	}
			    	if (in_array($QuestID,array(1001308,1001309,1001310,1001311,1001312,1001313,1001314)) && $roleInfo['vip'] > 3) {
			    		continue;
			    	}					    
				    $published = null;
				    //$published = $finishListRows['published'];
				    $published = $qstatusInfoValue;
				    $Presult = null;
				    $Presult = call_user_func_array($FinishScript.'::getFinish', array($playerInfo,$intID,$QuestID,$OnFinishParameter,$ExtraData,$published));	
				    //取消限制一个动作完成多个任务限制
				    if ($Presult == true && ($published == 2 || $published == 3 || $published == 4 || $published == 5) && ($finishListRows['RepeatInterval'] == 2 || $finishListRows['RepeatInterval'] == 0)) {
				    	//break;
				    	$showQuestID = $QuestID;
				    	$mblx = $finishListRows['RepeatInterval'];
				    }
				    $finishListRows = null;
		    	}
		    }	
		    $FinishVar1 = null;
		    $FinishVar2 = null;
		    $FinishVar3 = null;
		    $FinishVar4 = null;	    
			//检查是否达到完成条件
		}
		if (!empty($showQuestID)) {
			if ($vip > 0 && in_array($showQuestID,array(1001301,1001302,1001303,1001304))) {
				return false;
			} else {
				return $mblx.'_'.$showQuestID;
			}
		} else {
			return false;
		}
	}
	
	//执行奖励
	public static function OnAward($getInfo) {
		$playersid = $getInfo['playersid'];
		$QuestID = _get('id');                //任务ID
		$page = _get('page');
		$hyb = _get('hyb');
		//$inputInfo = array(1=>1000,6=>1500,7=>500,10=>20000);
		$result = getAward::award($playersid,$QuestID,$page,$hyb);		
		$result['rsn'] = intval(_get('ssn'));
		ClientView::show($result);
	}
    
	//五条任务显示
	/*public static function getRWShow($getInfo) {
		$playersid = $getInfo['playersid'];
		$showValue = clientList::getQuestsList($playersid);
		$showValue['rsn'] = intval(_get('ssn'));
		ClientView::show($showValue);
	}*/
	
	//显示任务列表
	public static function getRWList($getInfo) {
		$playersid = $getInfo['playersid'];
		$page = _get('page');
		$showValue = getAward::getRWList($playersid,$page);
		$showValue['rsn'] = intval(_get('ssn'));
		ClientView::show($showValue);
	}
	
	//显示单个任务信息
	public static function getRWInfo($getInfo) {
		$playersid = $getInfo['playersid'];
		$QuestID = _get('id');
		$ht = _get('ht');
		$showValue = getAward::getRWInfo($playersid,$QuestID,$ht);
		$showValue['rsn'] = intval(_get('ssn'));
		ClientView::show($showValue);
	}

	//调试升级
	public static function upgradeTest($getInfo) {
		$playersid = $getInfo['playersid'];
		$experienceValue = _get('exp');
		$showValue = guideScript::upgradeTest($playersid,$experienceValue);
		ClientView::show($showValue);
	}
	
	//获取主目标信息
	public static function hqmbxx($getInfo) {
		$playersid = $getInfo['playersid'];
		$showValue = getAward::hqmbxx($playersid);
		ClientView::show($showValue);
	}
	
	//开启日常任务宝箱
	public static function openRWBox($getInfo) {
		$playersid = $getInfo['playersid'];
		$id = _get('id');
		$showValue = getAward::hqhyjl($playersid,$id);
		ClientView::show($showValue);
	}
	
	//刷新日常任务列表
	public static function reGetRWList($getInfo) {
		$playersid = $getInfo['playersid'];
		$showValue = getAward::sxrcrw($playersid);
		ClientView::show($showValue);
	}
}
?>