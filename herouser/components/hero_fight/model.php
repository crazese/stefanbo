<?php
$GLOBALS['zbslots'] = array('helmet','carapace','arms','shoes');

class fightModel {
    //获取防守方将领数据
	public static function getDefendGeneralData($ID,$fightType,$playersid = '') {
		global $common,$db,$mc;
		$data = array();
		switch ($fightType) {
			//普通点打怪
			case 1:
				$result = $db->query("SELECT * FROM ".$common->tname('monster')." WHERE npcid = '$ID' ORDER BY general_sort ASC LIMIT 5");
				$num = $db->num_rows($result);
				if ($num == 0) {
					$result = $db->query("SELECT * FROM ".$common->tname('monster_bak')." WHERE npcid = '$ID' ORDER BY general_sort ASC LIMIT 5");
					$db->query("INSERT INTO ".$common->tname('monster')." SELECT * FROM ".$common->tname('monster_bak')." WHERE npcid = '$ID'");
				}
				while ($rows = $db->fetch_array($result)) {
					$data[] = $rows;
				}	
				break;
			//资源点打怪
			case 2: 
				$i = 0;
				$result = $db->query("SELECT * FROM ".$common->tname('player_boss')." WHERE npcid = '$ID' && playersid = '$playersid' ORDER BY general_sort ASC LIMIT 5");
				$num = $db->num_rows($result);
				if ($num == 0) {
					$result2 = $db->query("SELECT * FROM ".$common->tname('monster_bak')." WHERE npcid = '$ID' ORDER BY general_sort ASC LIMIT 5");
					while ($rows = $db->fetch_array($result2)) {
						$npcid = $rows['npcid'];
						$general_sort = $rows['general_sort'];
						$general_name = $rows['general_name'];
						$general_level = $rows['general_level'];
						$professional = $rows['professional'];
						$attack_value = $rows['attack_value'];
						$defense_value = $rows['defense_value'];
						$physical_value = $rows['physical_value'];
						$agility_value = $rows['agility_value'];
						$understanding_value = $rows['understanding_value'];
						$professional_level = $rows['professional_level'];						
						$mobility = $rows['npcid'];
						$bossLife = $rows['physical_value'] * 10;
						$sex = $rows['sex'];
						$avatar = $rows['avatar'];
						$insert = "INSERT INTO ".$common->tname('player_boss')."(npcid,general_sort,general_name,general_level,professional,attack_value,defense_value,physical_value,agility_value,understanding_value,professional_level,mobility,playersid,bossLife,sex,avatar) values ('$npcid','$general_sort','$general_name','$general_level','$professional','$attack_value','$defense_value','$physical_value','$agility_value','$understanding_value','$professional_level','$mobility','$playersid','$bossLife','$sex','$avatar')";
					    $db->query($insert);
					}
				    $result = $db->query("SELECT * FROM ".$common->tname('player_boss')." WHERE npcid = '$ID' && playersid = '$playersid' ORDER BY general_sort ASC LIMIT 5");
				}				
				while ($rows = $db->fetch_array($result)) {
					$i++;
					$rows['general_sort'] = $i;
					$data[] = $rows;					
				}
				break;				
			//遭遇战
			case 3:
				$data = cityModel::getGeneralInfo($ID,'*',1);
				break;
			//攻城战
			case 4:
				$value = array();
				$value= cityModel::getGeneralData($ID,0,'*',0);
		        $data = array();
		        $j = 0;
		        for ($i = 0; $i < count($value); $i++) {
		        	if ($value[$i]['f_status'] == 1) {
		        	   $j++;
			           $value[$i]['general_sort'] = $j;
			           //$value[$i]['command_soldier'] = $value[$i]['general_life'];
			           $value[$i]['attack_value'] = $value[$i]['attack_value'];
				       $data[] = $value[$i];
		        	}			     
		        }		        
				break;
			default:
				$data = false;
				break;
		}
		return $data;
	}
	
	//计算将领级别之和
	public static function sumGeneralExp($generalData) {
		$totalLevel = 0;
		for ($i = 0; $i < count($generalData); $i++) {
			$totalLevel += $generalData[$i]['general_level'];
		}		
		return $totalLevel;
	}	
	
	//获取攻方将领数据 $studyInfo：士兵学习技能等级信息$gjjc:攻击加成
	public static function getAttackGeneralData($playerid) {
		$data = cityModel::getGeneralData($playerid,'','*');
		$value = array();
		$j = 0;
		for ($i = 0; $i < count($data); $i++) {
			//$i++;
			if ($data[$i]['general_life'] > 0 && $data[$i]['f_status'] == 1) {
				$j++;
				$data[$i]['general_sort_data'] = $data[$i]['general_sort'];
				$data[$i]['general_sort'] = $j;							
				$data[$i]['command_soldier'] = $data[$i]['general_life'];	
				$data[$i]['attack_old_value'] = $data[$i]['attack_value'];		
				$data[$i]['attack_value'] = $data[$i]['attack_value'];				
				$value[] = $data[$i];				
			}
		}
		return $value;
	}

	//战斗经验收益$mlv:怪物级别;$ulv玩家级别(攻城)
	public static function attackExpIncome($mlv,$ulv) {
         //怪物基础经验公式
         //CEILING(((POWER(角色等级,1.08)*3.88+角色等级*5.88)+15.88)*1.88,1)
         //$baseexp = 20 + ($mlv/10 + 0.4) * ($mlv/10 + 0.4) * 10;
         $baseexp = ceil(((pow($ulv,1.08) * 3.88 + $ulv * 5.88) + 15.88) * 1.88);
         //经验级差调整系数公式
         $lvdiff = pow(2.718, -($ulv + 1 -$mlv)*($ulv + 1 -$mlv)/25);
         //获得经验
         $exp= $lvdiff * $baseexp;         
         return $exp;
	}
	
	//打怪经验收益$mlv:怪物级别;$ulv玩家级别（NPC）
	public static function dgExpIncome($mlv,$ulv) {
         //怪物基础经验公式
         //CEILING((POWER(怪物等级,1.08)*3.88+怪物等级*5.88)+15.88,1)
         //$npcbaseexp = 20 + ($mlv/10 + 0.4) * ($mlv/10 + 0.4) * 10;
         $npcbaseexp = ceil(((pow($mlv,1.08) * 3.88 + $mlv * 5.88) + 15.88));
         //经验级差调整系数公式
         $npclvdiff = pow(2.718, -($ulv + 1 -$mlv)*($ulv + 1 -$mlv)/100);
         //获得经验
         $exp= $npclvdiff * $npcbaseexp;                
         return $exp;		
	}
	
	//根据经验升级玩家或者将领等级
    public static function upgradeRole($currentLevel,$exp,$upgradeType) {
  	   $currentLevel++;
  	   if ($upgradeType == 1) {
  	 	  $cost = cityModel::getPlayerUpgradeExp($currentLevel-1);
  	   } else {
  	 	  $cost = cityModel::getGeneralUpgradeExp($currentLevel-1);
  	   }
  	   //heroCommon::insertLog($cost,'c1'); 
  	   //echo "cost:$cost <br>exp:$exp<br>$currentLevel";
  	   if ($exp >= $cost) {
  	 	  $exp = $exp - $cost;  	 	
  	 		  $value = fightModel::upgradeRole($currentLevel,$exp,$upgradeType);
  	   } else {
  	 	  $value['left'] = $exp;
  	      $value['level'] = $currentLevel-1; 
  	   }
  	   return $value;
    }
    
    //获取可攻城对象列表
    public static function getAttactCityList($playersid,$level,$page=1,$regionid) {
    	global $common,$db,$mc,$fight_lang;
    	$nowTime = time();
    	$minLevel = $level - 5;
    	if ($minLevel < 0) {
    		$minLevel = 0;
    	}
    	$maxLevel = $level + 5;
    	$numRole = $db->query("SELECT count(playersid) as gcsl FROM ".$common->tname('player')." WHERE player_level BETWEEN $minLevel AND $maxLevel && playersid != '$playersid' && regionid != '$regionid' && aggressor_playersid != '$playersid' && playersid NOT IN (SELECT playersid FROM ".$common->tname('protect')." WHERE protectTime > '$nowTime')");    	
    	$totalNumRow = $db->fetch_array($numRole);
    	$totalNum = $totalNumRow['gcsl'];
    	//$totalNum = $db->num_rows($numRole);    	
    	$db->free_result($numRole);
    	$perPage = 6;
    	if (intval($page) == 0) {
    		$page = 1;
    	}
    	$start = (intval($page) - 1) * $perPage;
    	$resultRole = $db->query("SELECT * FROM ".$common->tname('player')." WHERE player_level BETWEEN $minLevel AND $maxLevel && playersid != '$playersid' && regionid != '$regionid' && aggressor_playersid != '$playersid' && playersid NOT IN (SELECT playersid FROM ".$common->tname('protect')." WHERE protectTime > '$nowTime') ORDER BY player_level DESC LIMIT $start,$perPage");
    	$array = array();
    	while ($rows = $db->fetch_array($resultRole)) {
    		$prestige = $rows['prestige'];
    		$bhxx = $mc->get(MC.$rows['playersid'].'_peace');    //检查被攻击方是否受到保护
    		if (!empty($bhxx)) {
    			$bh = 1;
    		} else {
    			$bh = 0;
    		}
    		$temp_dir = explode("|",socialModel::getDiwei($rows['prestige']));
			$rows['dw'] = $temp_dir[0];
    		$array[] = array(
	    		'dw'=>$rows['dw'],
	    		'dj'=>intval($rows['player_level']),
	    		'xm'=>$rows['nickname'],
	    		'did'=>intval($rows['playersid']),
	    		'bh'=>$bh
    		);
    	}  
    	$db->free_result($resultRole); 
    	//查询城池保护期
    	$peaceRows = $mc->get(MC.$playersid.'_peace');
    	if (!empty($peaceRows)) {
    		$remainTime = $peaceRows['peaceTime'] - $nowTime;
    	} else {
    		$remainTime = 0;
    	}	
    	if (!empty($array)) {
    		$value['status'] = 0;   
    		$value['remainTime'] = $remainTime;	
    		$value['zs'] = $totalNum;
    		$value['list'] = $array;    		
    	} else {
    		$value['status'] = 39;   
     		$value['message'] = $fight_lang['model_msg_1'];    				
    	}
    	return $value;
    }
   
    //占领
    public static function zl($playersid,$gid,$tuId,$hyp = '',$gjcl = 0) {
    	global $mc,$common, $db, $_SGLOBAL,$zbslots,$G_PlayerMgr,$fight_lang,$sys_lang;
    	$nowTime = $_SGLOBAL['timestamp'];
    	if (!($bzlInfo = $mc->get(MC.$tuId))) {
    		$bzlInfo = $db->fetch_array($db->query("SELECT * FROM ".$common->tname('player')." WHERE `playersid` = '$tuId' LIMIT 1"));
    	} 
    	if (empty($bzlInfo)) {
    		$value = array('status'=>3,'message'=>$fight_lang['model_msg_2']);
    		return $value;
    	}     

		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$sys_lang['7']);		
		
    	$messageInfoDen = array(); 
    	$messageBzInfoDen = array();
    	$roleInfo['playersid'] = $playersid;
    	roleModel::getRoleInfo($roleInfo); 
    	cityModel::resourceGrowth($roleInfo);
		$jwdj = $roleInfo['mg_level'];
		$jwInfo = jwmc($jwdj);
		$jwjc = 1 + $jwInfo['jc'] / 100;		    	
    	$minLevel = $roleInfo['player_level'] - 5;
    	$maxLevel = $roleInfo['player_level'] + 5;
    	$lddj = $roleInfo['ld_level'];               //领地等级
    	//$ldsl = fightModel::ldsl($playersid);
    	$general = array();
    	$ldInfo = array();
    	$generalInfo = cityModel::getGeneralData($playersid,'','*'); 
    	foreach ($generalInfo as $generalInfoValue) {
    		if ($generalInfoValue['intID'] == $gid) {
    			$general[] = $generalInfoValue;
    		}
    		if ($generalInfoValue['occupied_playersid'] != 0 && $generalInfoValue['occupied_playersid'] != $playersid && $generalInfoValue['occupied_end_time'] > $nowTime) {
    			$ldInfo[] = 1;
    		}
    	}
    	$ldsl = count($ldInfo);
    	//$ldsl = $roleInfo['ldsl'];                   //当前领地数量
    	$silver = $roleInfo['silver'];
    	$fscl = $bzlInfo['strategy'];                //防守策略
    	if ($roleInfo['zf_aggressor_general'] == $gid) {
       		$value['status'] = 1006;		
    		$value['message'] = $fight_lang['model_msg_3'];     		
    	} elseif ($silver < 5 && !empty($hyp)) {
      		$value['status'] = 68;
			$value ['yp'] = $silver;
			$value['xyxhyp'] = 5;
			$rep_arr1 = array('{xhyp}', '{yp}');
			$rep_arr2 = array($value['xyxhyp'], $value['yp']);
    		$value['message'] = str_replace($rep_arr1, $rep_arr2, $sys_lang[6]);      		
    	} elseif ($bzlInfo['is_reason'] == 1) {
    		$value['status'] = 21;
    		$value['message'] = $fight_lang['model_msg_4'];
    	} elseif ($bzlInfo['player_level'] < 6) {
      		$value['status'] = 1021;
    		$value['message'] = $fight_lang['model_msg_5'];   	   		
    	} elseif ($roleInfo['food'] < 1) {
      		$value['status'] = 888;
    		$value['message'] = $fight_lang['model_msg_6'];    		
    	} elseif ($ldsl >= $lddj) {
      		$value['status'] = 1003;
    		$value['message'] = $fight_lang['model_msg_7'];   	   		
    	} elseif (($bzlInfo['player_level'] < $minLevel || $bzlInfo['player_level'] > $maxLevel)) {
     		$value['status'] = 1003;
    		$value['message'] = $fight_lang['model_msg_8'];   		
    	} elseif ($roleInfo['player_level'] < 6) {
    		$value['status'] = 1003;
    		$value['message'] = $fight_lang['model_msg_9'];
    	} elseif (empty($bzlInfo)) {
    		$value['status'] = 4;
    		$value['message'] = $fight_lang['model_msg_10'];
    	} elseif ($playersid == $tuId) {
     		$value['status'] = 3;
    		$value['message'] = $sys_lang[7];   		
    	} elseif ($mc->add(MC.$playersid.'_zlcd_'.$tuId,$nowTime,0,600) == false && empty($hyp)) {
    		$value['status'] = 1005;
    		$lastTime = $mc->get(MC.$playersid.'_zlcd_'.$tuId);
    		$lt = round(($nowTime - $lastTime) / 60,0);   
    		$llt = 10 - $lt;
    		if ($llt <= 0) {
    			$llt = 1;
    		}	
    		$value['message'] = str_replace('{llt}', $llt, $fight_lang['model_msg_11']);
    	} else {
    	    //并发控制
	    	$bfkz = heroCommon::bfkz(MC.$tuId.'_zlsj',30,$fight_lang['model_msg_12']);
	    	if ($bfkz != 'go') {
	    		$valueBf['status'] = 1003;
	    		$valueBf['message'] = $bfkz;
	    		return $valueBf;
	    	}	
    		//$general = cityModel::getGeneralData($playersid,'',$gid);
    		$stop = 0;    		
    		if ($bzlInfo['aggressor_playersid'] == $playersid && $bzlInfo['end_defend_time'] > $nowTime) {
    			$stop = 1;
    		} else {
   				$stop = 0;
   			}
    		//print_r($general);    		
 			$defendGid = $bzlInfo['aggressor_general'];
			$dgeneral = cityModel::getGeneralData($bzlInfo['aggressor_playersid'],'',$defendGid,0);   	
    	    if (empty($gid)) {
 				$value['status'] = 3;
				$value['message'] = $sys_lang[7];   	   
    	    } elseif ($stop == 1) {
				$value['status'] = 1003;
				$value['message'] = $fight_lang['model_msg_13'];
    		} elseif (empty($general)) {
	     		$value['status'] = 21;
	    		$value['message'] = $fight_lang['model_msg_14'];  	    			
    		} elseif (($general[0]['occupied_playersid'] != 0 && $general[0]['occupied_end_time'] > $nowTime && $general[0]['occupied_playersid'] != $playersid) || ($general[0]['occupied_playersid'] == $playersid && $general[0]['intID'] == $roleInfo['aggressor_general'])) {
				$value['status'] = 1003;
				$value['message'] = $fight_lang['model_msg_15'];    			
    		} elseif (empty($generalInfo)) {
  				$value['status'] = 21;
				$value['message'] = $fight_lang['model_msg_16'];   	   			
    		} elseif ($roleInfo['zf_aggressor_general'] == $gid) {
  				$value['status'] = 1003;
				$value['message'] = $fight_lang['model_msg_17'] ;   	     			
/*hk*/    		} elseif ($general [0] ['gohomeTime'] > $_SGLOBAL['timestamp'] && ($general [0] ['act'] == 4 || $general [0] ['act'] == 7) ) {
				$value ['status'] = 30;
				$value ['message'] = $fight_lang['model_msg_18'];
			} elseif ($general [0] ['gohomeTime'] > $_SGLOBAL['timestamp'] && $general [0] ['act'] == 3 ) {
				$value ['status'] = 30;
				$value ['message'] = $fight_lang['model_msg_19'];
			} elseif ($general [0] ['gohomeTime'] > $_SGLOBAL['timestamp'] && $general [0] ['act'] == 2 ) {
				$value ['status'] = 30;
				$value ['message'] = $fight_lang['model_msg_20'];
			} elseif ( $general [0] ['act'] == 1 || $general [0] ['act'] == 6 ) {
				$value ['status'] = 30;
				$value ['message'] = $fight_lang['model_msg_21'];/*hk*///设置武将占领的条件限制 
    		} elseif (($bzlInfo['is_defend'] == 0 && $bzlInfo['aggressor_playersid'] == 0) || ($bzlInfo['aggressor_playersid'] != 0 && $bzlInfo['aggressor_playersid'] != $tuId && $bzlInfo['end_defend_time'] < $nowTime)) {
				$updateRole['food'] = $roleInfo['food'] - 1;
				$updateRole['last_update_food'] = $nowTime;
	    		$value['jl'] = floor($updateRole['food']); 
	    		if (!empty($hyp)) {
	    			$updateRole['silver'] = $silver - 5;
	    			$value['yp'] = intval($updateRole['silver']);
	    			$value['xhyp'] = 5;
	    		} 	
    			$att_g_info_array = array();	
    			foreach ($generalInfo as $key => $gvalue) {
				   if ($gvalue['intID'] == $gid) {
				   	  $att_g_name = $gvalue['general_name'];
				   	  $att_g_level = $gvalue['general_level'];              //将领级别
				   	  $att_g_tf = round($gvalue['understanding_value'],1);  //将领天赋
				   	  $att_g_info_array[] = array('mc'=>$att_g_name,'lv'=>$att_g_level,'ng'=>0,'tf'=>$att_g_tf,'zdl'=>0);		 		 			
					  $updateCqZlPlayersid = $gvalue['occupied_playersid'];
					 $gvalue['occupied_playersid'] = $updateGen['occupied_playersid'] = $tuId;
					 $gvalue['occupied_player_level'] = $updateGen['occupied_player_level'] = $bzlInfo['player_level'];
					 $gvalue['occupied_player_nickname'] = $updateGen['occupied_player_nickname'] = mysql_escape_string($bzlInfo['nickname']);						 	
					 $gvalue['last_income_time'] = $updateGen['last_income_time'] = $nowTime; 
					 $gvalue['occupied_end_time'] = $updateGen['occupied_end_time'] = $nowTime + 86400;
					 $newData[$gvalue['sortid']] = $gvalue;	
					 $common->updateMemCache(MC.$playersid.'_general',$newData);	
					 break;
				   }  				   				   	         
				}
				

				$updateBzl['aggressor_playersid'] = $playersid;
				$updateBzl['aggressor_nickname'] = mysql_escape_string($roleInfo['nickname']);
				$updateBzl['aggressor_level'] = $roleInfo['player_level'];
				$updateBzl['aggressor_general'] = $gid;
				$updateBzl['is_defend'] = 0;
				$updateBzl['strategy'] = 1;
				$updateBzl['last_zl_collect_time'] = $nowTime;
				$updateBzl['end_defend_time'] = $nowTime + zlsc();
				$updateBzlWhere['playersid'] = $tuId;
				$common->updatetable('player',$updateBzl,$updateBzlWhere);
				$common->updateMemCache(MC.$tuId,$updateBzl);					
				$value['status'] = 1001;
				$where['intID'] = $gid;
				$common->updatetable('playergeneral',$updateGen,$where);    
		        $defMesArra = array('xllx'=>12,'wjmc1'=>$roleInfo['nickname'],'wjid1'=>intval($playersid),'wjwjmc1'=>$att_g_info_array[0]['mc'],'wjwjid1'=>intval($gid),'interaction'=>1);
				$mesageDen = addcslashes(json_encode($defMesArra),'\\');				
		        $messageInfoDen = array('playersid'=>0,'jsid'=>$roleInfo['playersid'],'toplayersid'=>$bzlInfo['playersid'],'subject'=>'您的城池被占领了','message'=>$mesageDen,'type'=>1,'genre'=>12,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		           
    		    $roleInfo['ldsl'] = $ldsl + 1;
		        $rwid = questsController::OnFinish($roleInfo,"'zldz','zl','zlldsl'");
		        if (!empty($rwid)) {
		            if (!empty($rwid)) {
		            	$value['rwid'] = $rwid;
		            } 
		        }	
		        fightModel::setWord($playersid,$tuId,'','');
		        $value['tuid'] = intval($tuId);			        
		        $value['mode'] = 0;    		
		        //$mc->set(MC.'ld_'.$playersid,$ldsl + 1,0,3600);		
		        //增加领地数量
		        //fightModel::addLdsl($playersid,$tuId,$updateBzl['end_defend_time']);
		        $mc->set(MC.$playersid.'_zlcd_'.$tuId,$nowTime,0,600);//设置占领CD    
                //降低好感度
                socialModel::addFriendFeel($tuId,$playersid,-1);		
                socialModel::addEnemy($playersid,$tuId); 	  
                //移除可占领列表内
                socialModel::removeOccupyList($playersid, $tuId);  
                achievementsModel::check_achieve($playersid,'',array('gc'));                    
    		} else {
    			$avalue = array();
				$j = 0;
				$stop = 0;
				//print_r($general);
				for ($i = 0; $i < 1; $i++) {
					if ($general[$i]['general_life'] > 0) {
						$j++;
						$general[$i]['general_sort_data'] = $general[$i]['general_sort'];
						$general[$i]['general_sort'] = $j;
						$general[$i]['command_soldier'] = $general[$i]['general_life'];	
						$att_g_name = $general[$i]['general_name'];
						$avalue[] = $general[$i];
						$att_g_level = $general[$i]['general_level'];              //将领级别
		                $att_g_tf = round($general[$i]['understanding_value'],1);  //将领天赋
			            $att_g_info_array[] = array('mc'=>$att_g_name,'lv'=>$att_g_level,'ng'=>0,'tf'=>$att_g_tf);
					}
				} 

				$attack_data = $avalue;
	            if (empty($attack_data)) {
					$value['status'] = 1048;
					$value['gid'] = $gid;   
					$zbtljcArray = array ();
			        foreach ($zbslots as $slot) {
			          $zbid = $general[0] [$slot];
			          if ($zbid != 0) {
			              $zbInfo = toolsModel::getZbSx ( $general[0] ['playerid'], $zbid );
			              $zbtljcArray [] = $zbInfo ['tl'];
			          }
			        }
			        $zbtljc = array_sum ( $zbtljcArray );

					$sxxs = genModel::sxxs ( $general[0]['professional'] );
					$tl = genModel::hqwjsx($general[0]['general_level'],$general[0] ['understanding_value'],$general[0] ['professional_level'],$general[0] ['llcs'],$jwjc,$zbtljc,$sxxs ['tl'],$general[0] ['py_tl']);
					$value['lshf'] = ceil($tl * 10 * addLifeCost($roleInfo['player_level']));
					$mc->delete(MC.$playersid.'_zlcd_'.$tuId);
					$mc->delete(MC.$playersid.'_zlsj');
				} elseif (empty($dgeneral)) {
	 	     		$value['status'] = 4;
		    		$value['message'] = $fight_lang['model_msg_23'];  	   			
    		    } else {
	    			$updateRole['food'] = $roleInfo['food'] - 1;
	    			$updateRole['last_update_food'] = $nowTime;
		    		$value['jl'] = floor($updateRole['food']); 
		    		if (!empty($hyp)) {
		    			$updateRole['silver'] = $silver - 5;
		    			$value['yp'] = intval($updateRole['silver']);
		    			$value['xhyp'] = 5;
		    		}     		    	
					$k = 0;
					$defendplayer = $G_PlayerMgr->GetPlayer($bzlInfo['aggressor_playersid'] );
					$defendRole['playersid'] = $bzlInfo['aggressor_playersid'];
					$roleRes = roleModel::getRoleInfo($defendRole,false);
					if (empty($roleRes)) {
					   $value = array('status'=>3,'message'=>$fight_lang['model_msg_22']);
					   return $value;
					}					
					$value['d_level'] = $defendRole['player_level'];
					$value['d_sex'] = $defendRole['sex'];
					$value['a_level'] = $roleInfo['player_level'];
					$value['a_sex'] = $roleInfo['sex'];
					$value['wjxm'] = stripcslashes($roleInfo['nickname']);
					$value['fsxm'] = stripcslashes($bzlInfo['aggressor_nickname']);
					$dvalue = array();	
					for ($i = 0; $i < 1; $i++) {
						$k++;
						$dgeneral[$i]['general_sort_data'] = $dgeneral[$i]['general_sort'];
						$dgeneral[$i]['general_sort'] = $k;							
						$dgeneral[$i]['command_soldier'] = $dgeneral[$i]['general_life'];	
						//$dgeneral[$i]['attack_old_value'] = $dgeneral[$i]['attack_value'];		
						//$dgeneral[$i]['attack_value'] = $dgeneral[$i]['attack_value'];	
						$def_g_name = $dgeneral[$i]['general_name'];		
						$dvalue[] = $dgeneral[$i];	
						$def_g_level = $dgeneral[$i]['general_level'];              //将领级别
		                $def_g_tf = round($dgeneral[$i]['understanding_value'],1);  //将领天赋
			            //$def_g_ng = $dgeneral[$i]['professional_level'];            //将领内功			
			            $last_income_time = $dgeneral[$i]['last_income_time'];   //上次收取收益时间
			            $syjg = $nowTime - $last_income_time;                    //收益间隔时间			
						$def_g_info_array[] = array('mc'=>$def_g_name,'lv'=>$def_g_level,'ng'=>0,'tf'=>$def_g_tf);									
					} 
					$hqzlz = hqzlz($roleInfo,$defendRole,$general,$dgeneral,1,$player,$defendplayer);  //理论获取战功值 
					$kczlz = ceil($hqzlz / 4);  //理论扣除战功值
					$defend_data = $dvalue;		
					//开始战斗
					$gf_jwdj = $roleInfo['mg_level'];
					$sf_jwdj = $defendRole['mg_level'];
					$result = actModel::fight($attack_data,$defend_data,'',$gjcl,$fscl,0,$gf_jwdj,$sf_jwdj);                               //战斗返回
					
					// 评分
					$pfArr = fightModel::getPf($attack_data, $roleInfo);
					$value['wjzdlpf'] = $pfArr['wjzdlpf'];
					$value['djpf'] = $pfArr['djpf'];
					$value['tfpf'] = $pfArr['tfpf'];
					$value['zbpf'] = $pfArr['zbpf'];
					$value['jwpf'] = $pfArr['jwpf'];
					$value['jnpf'] = $pfArr['jnpf'];
						
					$dfpfArr = fightModel::getPf($defend_data, $defendRole);
					$value['gkpf'] = $dfpfArr['wjzdlpf'];
					$value['djpfzb'] = $dfpfArr['djpf'];
					$value['tfpfzb'] = $dfpfArr['tfpf'];
					$value['zbpfzb'] = $dfpfArr['zbpf'];
					$value['jwpfzb'] = $dfpfArr['jwpf'];
					$value['jnpfzb'] = $dfpfArr['jnpf'];
					$value['tgsxpf'] = $dfpfArr['wjzdlpf'];
					
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
							//$attack_loss[] = $attack_result[$i]['loss'];
						} else {
							$defend_soldier[] = $attack_result[$i]['command_soldier'];
							//$defendGeneralLeftSoldier[$attack_result[$i]['id']] = $attack_result[$i]['command_soldier'];  //防守方每个将领所剩兵力
						}
					}
					$attack_soldier_left = array_sum($attack_soldier);   //攻击方所剩人数	
    	    		//战斗后自动补血
					$zdbx = fightModel::zdbx($attack_data,$playersid,$attackGeneralLeftSoldier);
					if (!empty($zdbx)) {
						$attackGeneralLeftSoldier = $zdbx['ginfo'];
						$updateRole['coins'] = $zdbx['tq'];
						$returnValue['tq'] = $updateRole['coins'];
					}					
					if ($attack_soldier_left <= 0) {
						$value['status'] = 1002;	
						$value['gfcl'] = $gjcl;
						$value['sfcl'] = $fscl;	
						$sjkczgz = 0;
						$kzgz = rankModel::decreaseZg($playersid,$kczlz,$sjkczgz,2,mysql_escape_string($bzlInfo['nickname']),$tuId);
						$updateRole = array_merge($updateRole, $kzgz); //更新战功值 	
						$value['zg'] = intval($kzgz['ba']);
						$value['zgz'] = intval($sjkczgz);							
						//更新攻击方将领数据
						//$wjhqjy = array();
						for ($k = 0; $k < 1; $k++) {
							$id = $attack_data[$k]['intID'];
							//$current_command_soldier = $attack_data[$k]['command_soldier'];                   //原有士兵数量
							$left_command_soldier = $attackGeneralLeftSoldier[$id];                             //剩余士兵数量
							$sssm = $attack_data[$k]['general_life'];                                           //武将生命
							$wjmc = $attack_data[$k]['general_name'];                                           //武将名称
							$updateGeneral['general_life'] = $left_command_soldier;    
							$attack_data[$k]['general_life'] = $left_command_soldier;
							$attack_data[$k]['command_soldier'] = $left_command_soldier;	
							$attack_data[$k]['general_sort'] =  $attack_data[$k]['general_sort_data']; 
							//$attack_data[$k]['attack_value'] = $attack_data[$k]['attack_old_value'];
							$attack_data[$k]['occupied_playersid'] = $updateGeneral['occupied_playersid'] = 0;
							$attack_data[$k]['occupied_player_level'] = $updateGeneral['occupied_player_level'] = 0;
							$attack_data[$k]['occupied_player_nickname'] = $updateGeneral['occupied_player_nickname'] = '';							
							$attack_data[$k]['last_income_time'] = $updateGeneral['last_income_time'] = 0; 
							$attack_data[$k]['occupied_end_time'] = $updateGeneral['occupied_end_time'] = 0; 
							$value['smz'] = intval($left_command_soldier);
							$whereGeneral['intID'] = $id;
							$common->updatetable('playergeneral',$updateGeneral,$whereGeneral);               //更新将领数据			  				
							$newData[$attack_data[$k]['sortid']] = $attack_data[$k];     	     		     
							$common->updateMemCache(MC.$playersid.'_general',$newData);   
							$zbtljcArray = array ();
				            foreach ($zbslots as $slot) {
				               $zbid = $attack_data[$k] [$slot];
				               if ($zbid != 0) {
				                  $zbInfo = toolsModel::getZbSx ( $attack_data[$k] ['playerid'], $zbid );
				                  $zbtljcArray [] = $zbInfo ['tl'];
				               }
				            }
				            $zbtljc = array_sum ( $zbtljcArray );
							$sxxs = genModel::sxxs ( $attack_data[$k]['professional'] );
							$tl = genModel::hqwjsx($attack_data[$k]['general_level'],$attack_data[$k] ['understanding_value'],$attack_data[$k] ['professional_level'],$attack_data[$k] ['llcs'],$jwjc,$zbtljc,$sxxs ['tl'],$attack_data[$k] ['py_tl']);								  
							$value['lshf'] = ceil(($tl * 10 - $left_command_soldier) * addLifeCost($roleInfo['player_level']));
					   }
					   
					   //发送消息
					   if ($bzlInfo['is_defend'] == 1) {
					    	$subject = $fight_lang['model_msg_24'] ;
					    	$bh = 9;
					    	socialModel::addEnemy($playersid,$tuId);
					   } else {
					    	$subject = $fight_lang['model_msg_25'] ;
					    	$bh = 11;
					    	socialModel::addEnemy($playersid,$bzlInfo['aggressor_playersid']);	
					   }
				       if ($bh == 9) {
				       	    $xllx = 13;
					   		$defMesArra = array('xllx'=>13,'wjmc1'=>$roleInfo['nickname'],'wjid1'=>intval($playersid),'wjwjmc1'=>$att_g_info_array[0]['mc'],'wjwjid1'=>intval($gid));
				       } else {
				       	 	$defMesArra = array('xllx'=>30,'wjmc1'=>$roleInfo['nickname'],'wjid1'=>intval($playersid),'wjwjmc1'=>$att_g_info_array[0]['mc'],'wjwjid1'=>intval($gid),'wjmc2'=>$bzlInfo['nickname'],'wjid2'=>intval($bzlInfo['playersid']),'wjwjmc2'=>$def_g_info_array[0]['mc'],'wjwjid2'=>intval($defendGid));
				            $xllx = 30;
				       }
					   $rwid = questsController::OnFinish($roleInfo,"'zldz'");
			           if (!empty($rwid)) {
			            $value['rwid'] = $rwid;
			           } 
					   $mesageDen = addcslashes(json_encode($defMesArra),'\\');
				       $messageInfoDen = array('playersid'=>0,'jsid'=>$roleInfo['playersid'],'toplayersid'=>$bzlInfo['aggressor_playersid'],'subject'=>$subject,'message'=>$mesageDen,'type'=>1,'genre'=>intval($xllx),'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    
					   $mc->set(MC.$playersid.'_zlcd_'.$tuId,$nowTime,0,600);//设置占领CD
					} else {
						$value['status'] = 0;
						$value['gfcl'] = $gjcl;
						$value['sfcl'] = $fscl;	
						$sjzjzgz = 0;
						$addzgz = rankModel::addZg($playersid,$hqzlz,$sjzjzgz,1,mysql_escape_string($bzlInfo['nickname']),$tuId); //增加战功值						
						$updateRole = array_merge($updateRole, $addzgz); //更新战功值 	
						$value['zg'] = intval($addzgz['ba']);
						$value['zgz'] = intval($sjzjzgz);							
						//$updateRole['ldsl'] = $ldsl + 1;						           		       
						for ($k = 0; $k < 1; $k++) {
							$id = $attack_data[$k]['intID'];
							//$current_command_soldier = $attack_data[$k]['command_soldier'];                   //原有士兵数量
							$left_command_soldier = $attackGeneralLeftSoldier[$id];                             //剩余士兵数量
							$sssmz = $attack_data[$k]['general_life'] - $left_command_soldier;                  //损失的生命值
							$wjmc = $attack_data[$k]['general_name']; 
							$updateGeneral['general_life'] = $left_command_soldier;    
							$attack_data[$k]['general_life'] = $left_command_soldier;
							$attack_data[$k]['command_soldier'] = $left_command_soldier;	
							$attack_data[$k]['general_sort'] =  $attack_data[$k]['general_sort_data']; 
							//$attack_data[$k]['attack_value'] = $attack_data[$k]['attack_value'];	
							$updateGqZlPlayersid = $attack_data[$k]['occupied_playersid'];				
								
						    $attack_data[$k]['occupied_playersid'] = $updateGeneral['occupied_playersid'] = $tuId;
							$attack_data[$k]['occupied_player_level'] = $updateGeneral['occupied_player_level'] = $bzlInfo['player_level'];
							$attack_data[$k]['occupied_player_nickname'] = $updateGeneral['occupied_player_nickname'] = mysql_escape_string($bzlInfo['nickname']);
							$attack_data[$k]['last_income_time'] = $updateGeneral['last_income_time'] = $nowTime; 		
							$attack_data[$k]['occupied_end_time'] = $updateGeneral['occupied_end_time'] = $nowTime + zlsc();		 
							$value['smz'] = intval($left_command_soldier);			
							$whereGeneral['intID'] = $id;
							$common->updatetable('playergeneral',$updateGeneral,$whereGeneral);               //更新将领数据（暂时屏蔽）
							$newData[$attack_data[$k]['sortid']] = $attack_data[$k];     	     		     
							$common->updateMemCache(MC.$playersid.'_general',$newData);      
							$zbtljcArray = array ();
				            foreach ($zbslots as $slot) {
				                $zbid = $attack_data[$k] [$slot];
				                if ($zbid != 0) {
				                  $zbInfo = toolsModel::getZbSx ( $attack_data[$k] ['playerid'], $zbid );
				                  $zbtljcArray [] = $zbInfo ['tl'];
				                }
				            }
				            $zbtljc = array_sum ( $zbtljcArray );

							$sxxs = genModel::sxxs ( $attack_data[$k]['professional'] );
							$tl = genModel::hqwjsx($attack_data[$k]['general_level'],$attack_data[$k] ['understanding_value'],$attack_data[$k] ['professional_level'],$attack_data[$k] ['llcs'],$jwjc,$zbtljc,$sxxs ['tl'],$attack_data[$k] ['py_tl']);								
							$value['lshf'] = ceil(($tl * 10 - $left_command_soldier) * addLifeCost($roleInfo['player_level']));							
							unset($updateGeneral);
							unset($whereGeneral);
							unset($newData);
					   }					    
				  	 	//更新防守方将领数据
				  	 	$dincomeTimes = 0;
					   	foreach ($dgeneral as $key => $dvalue) {
					  	   if ($dvalue['intID'] == $defendGid) {
					 		 $dvalue['occupied_playersid'] = $updateGen['occupied_playersid'] = 0;
					 		 $dvalue['occupied_player_level'] = $updateGen['occupied_player_level'] = 0;
					 		 $dvalue['occupied_player_nickname'] = $updateGen['occupied_player_nickname'] = '';		
							 $dvalue['last_income_time'] = $updateGen['last_income_time'] = 0; 	
					 		 $dvalue['occupied_end_time'] = $updateGen['occupied_end_time'] = 0; 		 
					  	   	 $dwhere['intID'] = $defendGid;
				   	 	     $common->updatetable('playergeneral',$updateGen,$dwhere);
					  	     $newdData[$dvalue['sortid']] = $dvalue;                 
						     $common->updateMemCache(MC.$bzlInfo['aggressor_playersid'].'_general',$newdData);	   	
					  	     break;
					  	   }     
					    }
					    /*更新玩家数据*/
		    			$updateBzl['aggressor_playersid'] = $playersid;
		    			$updateBzl['aggressor_nickname'] = mysql_escape_string($roleInfo['nickname']);
		    			$updateBzl['aggressor_level'] = $roleInfo['player_level'];
		    			$updateBzl['aggressor_general'] = $gid;
					  	$updateBzl['is_defend'] = 0;
					  	$updateBzl['strategy'] = 1;
					  	$updateBzl['last_zl_collect_time'] = $nowTime;
                        $updateBzl['end_defend_time'] = $nowTime + zlsc();
                        if ($bzlInfo['aggressor_playersid'] == $tuId) {
 				            $sjkczgz = 0;
				            $dkzgz = rankModel::decreaseZg($tuId,$kczlz,$sjkczgz,9,mysql_escape_string($roleInfo['nickname']),$playersid); //防守方扣除战功值
				            $updateBzl = array_merge($updateBzl,$dkzgz);                      	
                        }
		    			$updateBzlWhere['playersid'] = $tuId;
		    			$common->updatetable('player',$updateBzl,$updateBzlWhere);
		    			$common->updateMemCache(MC.$tuId,$updateBzl);
		    			/*更新玩家数据结束*/		
				        //增加领地数量
				        //fightModel::addLdsl($playersid,$tuId,$updateBzl['end_defend_time']);
				        if ($bzlInfo['aggressor_playersid'] != $tuId) {
				            $sjkczgz = 0;
				            $dkzgz = rankModel::decreaseZg($defendRole['playersid'],$kczlz,$sjkczgz,14,mysql_escape_string($roleInfo['nickname']),$playersid); //防守方扣除战功值			        	
				        	$defMesArra = array('G'=>$sjkczgz,'xllx'=>27,'wjmc1'=>$roleInfo['nickname'],'wjid1'=>intval($playersid),'wjmc2'=>$bzlInfo['aggressor_nickname'],'wjid2'=>intval($bzlInfo['aggressor_playersid']),'wjwjmc1'=>$att_g_info_array[0]['mc'],'wjwjid1'=>intval($gid));
				            $xllx = 27;
				            //减少领地数量
				            //fightModel::reduceLdsl($bzlInfo['aggressor_playersid'],$tuId);	
				            $zlsy = fightModel::zlsy($bzlInfo['player_level'],2,$syjg);
				            $sj_tq = ceil($zlsy / 2);	  
				            $updateDRole['coins'] = $defendRole['coins'] + $sj_tq;
				            if ($updateDRole['coins'] > COINSUPLIMIT) {
				            	$updateDRole['coins'] = COINSUPLIMIT;
				            	$sj_tq = COINSUPLIMIT - $defendRole['coins']; 
				            }	
				            $updateDRole = array_merge($updateDRole,$dkzgz);
				            $value['sj_tuid'] = intval($bzlInfo['aggressor_playersid']);
				            $value['sj_tq'] = $sj_tq;
				            $updateDRoleWhere['playersid'] = $defendRole['playersid'];
				            $common->updatetable('player',$updateDRole,$updateDRoleWhere);
				            $common->updateMemCache(MC.$defendRole['playersid'],$updateDRole); 
				            socialModel::addEnemy($playersid,$bzlInfo['aggressor_playersid']);	       
				        } else {
		    				$defMesArra = array('G'=>$sjkczgz,'xllx'=>12,'wjmc1'=>$roleInfo['nickname'],'wjid1'=>intval($playersid),'wjwjmc1'=>$att_g_info_array[0]['mc'],'wjwjid1'=>intval($gid));
                            $xllx = 12;
				        }
				        //$mesageDen = addcslashes(json_encode($defMesArra),'\\');				
						$mesageDen = $defMesArra;
				        $messageInfoDen = array('playersid'=>0,'jsid'=>$roleInfo['playersid'],'toplayersid'=>$tuId,'subject'=>$fight_lang['model_msg_27'],'message'=>$mesageDen,'type'=>1,'genre'=>intval($xllx),'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		             
				        if ($bzlInfo['aggressor_playersid'] != $tuId) {
					        $bzMesArra = array('G'=>$sjkczgz,'xllx'=>29,'wjmc1'=>$roleInfo['nickname'],'wjid1'=>intval($playersid),'wjwjmc1'=>$att_g_info_array[0]['mc'],'wjwjid1'=>intval($gid),'wjmc2'=>$bzlInfo['nickname'],'wjid2'=>intval($bzlInfo['playersid']),'wjwjmc2'=>$def_g_info_array[0]['mc'],'wjwjid2'=>intval($defendGid));
					        //$mesageDen = addcslashes(json_encode($bzMesArra),'\\');
							$mesageDen = $bzMesArra;
					        $messageBzInfoDen = array('playersid'=>0,'jsid'=>$roleInfo['playersid'],'toplayersid'=>$bzlInfo['aggressor_playersid'],'subject'=>$fight_lang['model_msg_26'],'message'=>$mesageDen,'type'=>1,'genre'=>29,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);
					        $value['mode'] = 1;		
					        $y = $bzlInfo['aggressor_nickname'];
					        $value['oid'] = intval($bzlInfo['aggressor_playersid']);
				        } else {
				        	$value['mode'] = 0;
				        	$y = '';
				        }	
				        socialModel::addEnemy($playersid,$tuId);
				        fightModel::setWord($playersid,$bzlInfo['playersid'],'','');
				        //$mc->set(MC.'ld_'.$playersid,$ldsl + 1,0,3600);		
				        $mc->set(MC.$playersid.'_zlcd_'.$tuId,$nowTime,0,600);//设置占领CD	
				        //有几率获得取技能书	
				        $sjrq = date('Ymd',$nowTime);
				        $pvpcs = $mc->get(MC.$playersid."_pvp_".$bzlInfo['aggressor_playersid'].'_'.$sjrq);	
				        if (empty($pvpcs)) {
				        	$pvpcs = 1;
				        }
				        if ($pvpcs < 4) {
							// 背包满时不获得技能书
							$bgsygs = toolsModel::getBgSyGs($playersid);
					        if (rand(0,99) < 20 && $bgsygs > 0) {
					        	$jnid = rand(1,15);
					        	$djid = jndyid($jnid);
					        	//$playerBag = toolsModel::getMyItemInfo($playersid); // 背包列表返回协议优化
					        	//$addItem = toolsModel::addPlayersItem($roleInfo,$djid);
								$addItem = $player->AddItems(array($djid=>1));
					        	if ($addItem === false) {
					        		$value['status'] = 1004;
					        	} else {
					        		if (empty($pvpcs)) {
					        			$pvpcs = 0;
					        		}
					        		$jnInfo = jnk($jnid);
					        		$value['dlmc'] = $jnInfo['n'];
					        		$value['dlsl'] = 1;
					        		$value['dliid'] = $jnInfo['iconid'];
					        		//$bbInfo = toolsModel::getAllItems($playersid);
					        		//$value['list'] = $bbInfo['list'];		
					        		//$simBg[] = array('insert'=>1, 'ItemID'=>$djid); // 背包列表返回协议优化
					        		//$bagData = toolsModel::getBglist($simBg, $playersid, $playerBag); // 背包列表返回协议优化
									$bagData = $player->GetClientBag();
					        		$value['list'] = $bagData; // 背包列表返回协议优化
					        	}	
					        }
				        }
				        $mc->set(MC.$playersid."_pvp_".$bzlInfo['aggressor_playersid'].'_'.$sjrq,$pvpcs + 1,0,86400);				        
		    		    $roleInfo['ldsl'] = $ldsl + 1;
				        $rwid = questsController::OnFinish($roleInfo,"'zldz','zl','zlldsl'");
				        if (!empty($rwid)) {
				            if (!empty($rwid)) {
				            	$value['rwid'] = $rwid;
				            } 
				        }				  
		                //降低好感度
		                socialModel::addFriendFeel($tuId,$playersid,-1);
                        //移除可占领列表内
               			socialModel::removeOccupyList($playersid, $tuId);
               			achievementsModel::check_achieve($playersid,'',array('gc'));
					}
				    $value['begin'] = $result['begin'];                                               //开始士兵情形
					$round = $result['round'];                                               //战斗回合	
					$newround = heroCommon::arr_foreach($round);
					$value['round'] = $newround;
					$round = null;	
					$newround = null;				        	
			        $value['gid'] = $gid;   				
				}					 			
    		}    		  
    	}
    	if (!empty($messageBzInfoDen)) {
    		if ($value['status'] != 1001) {
    			$messageBzInfoDen['request'] = addcslashes(json_encode($value),'\\');
    		} else {
    			$messageBzInfoDen['request'] = '';
    		}
    		lettersModel::addMessage($messageBzInfoDen);
    	}
    	if (!empty($messageInfoDen)) {
    		if ($value['status'] != 1001) {
      			$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
    		} else {
    			$messageInfoDen['request'] = '';
    		}
    		lettersModel::addMessage($messageInfoDen);
    	}
    	if (!empty($updateRole)) {
 			$updateRoleWhere['playersid'] = $playersid;
			$common->updatetable('player',$updateRole,$updateRoleWhere);
			$common->updateMemCache(MC.$playersid,$updateRole);	   		
    	}
	     //完成引导脚本
		/*$xyd = guideScript::wcjb($roleInfo,'ydzl',6,6);
		//接收新的引导
		//$xyd = guideScript::jsydsj($roleInfo,'cg');
		$xyd = guideScript::xsjb ( $roleInfo, 'ydjhy', 6 );
		if ($xyd !== false) {
			$value['ydts'] = $xyd;
		}	*/
		$userinfo = $player->GetBaseInfo();
		$value['zg'] = $userinfo['ba'];		
    	return $value;
    }
    
    //自救
    public static function self_help($playersid,$gid,$gjcl = 0) {
    	global $common,$mc,$db,$_SGLOBAL,$zbslots, $G_PlayerMgr, $fight_lang, $sys_lang;
    	$roleInfo['playersid'] = $playersid;
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$sys_lang[7]);
		
    	$nowTime = $_SGLOBAL['timestamp'];
    	roleModel::getRoleInfo($roleInfo);
		$jwdj = $roleInfo['mg_level'];
		$jwInfo = jwmc($jwdj);
		$jwjc = 1 + $jwInfo['jc'] / 100;    	
    	if (!($bzlInfo = $mc->get(MC.$roleInfo['aggressor_playersid']))) {
    		$bzlInfo = $db->fetch_array($db->query("SELECT * FROM ".$common->tname('player')." WHERE `playersid` = '".$roleInfo['aggressor_playersid']."' LIMIT 1"));
    	}   
    	$fscl = $roleInfo['strategy']; //防守策略	
    	$messageInfoDen = array();    	
    	$general = cityModel::getGeneralData($playersid,'',$gid);
        $avalue = array();
		$j = 0;
		for ($i = 0; $i < 1; $i++) {
			if ($general[$i]['general_life'] > 0) {
				$j++;
				$general[$i]['general_sort_data'] = $general[$i]['general_sort'];
				$general[$i]['general_sort'] = $j;							
				$general[$i]['command_soldier'] = $general[$i]['general_life'];	
				$avalue[] = $general[$i];	
        $att_g_level = $general[$i]['general_level'];              //将领级别
        $att_g_tf = round($general[$i]['understanding_value'],1);  //将领天赋
				$att_g_name = $general[$i]['general_name'];		
				$att_g_info_array[] = array('mc'=>$att_g_name,'lv'=>$att_g_level,'ng'=>0,'tf'=>$att_g_tf);						
			}
		} 
		$attack_data = $avalue;
		$dgeneral = cityModel::getGeneralData($roleInfo['aggressor_playersid'],'',$roleInfo['aggressor_general'],0); 
    	if ($bzlInfo['playersid'] == $playersid) {
			$value['status'] = 1002;
			$value['message'] = $fight_lang['model_msg_28'];	
    	} elseif ($roleInfo['player_level'] < 5) {
     		$value['status'] = 1021;
    		$value['message'] = $fight_lang['model_msg_29'];   		
    	} elseif (empty($gid)) {
			$value['status'] = 3;
			$value['message'] = $sys_lang[7];				
		}  elseif ($roleInfo['aggressor_playersid'] == 0 || ($roleInfo['aggressor_playersid'] != 0 && $roleInfo['end_defend_time'] < $nowTime && $roleInfo['aggressor_playersid'] != $playersid)) {
			$value['status'] = 1002;
			$value['message'] = $fight_lang['model_msg_28'];
		} elseif (empty($attack_data)) {
			$value['status'] = 1048;
			$value['gid'] = $gid;
			$zbtljcArray = array ();
      foreach ($zbslots as $slot) {
        $zbid = $general[0] [$slot];
        if ($zbid != 0) {
          $zbInfo = toolsModel::getZbSx ( $general[0] ['playerid'], $zbid );
          $zbtljcArray [] = $zbInfo ['tl'];
        }
      }
      $zbtljc = array_sum ( $zbtljcArray );

			$sxxs = genModel::sxxs ( $general[0]['professional'] );
			$tl = genModel::hqwjsx($general[0]['general_level'],$general[0] ['understanding_value'],$general[0] ['professional_level'],$general[0] ['llcs'],$jwjc,$zbtljc,$sxxs ['tl'],$general[0] ['py_tl']);				
			$value['lshf'] = ceil($tl * 10 * addLifeCost($roleInfo['player_level']));				
		} elseif (($general[0]['occupied_playersid'] != 0 && $general[0]['occupied_playersid'] != $playersid && $general[0]['occupied_end_time'] > $nowTime) || ($general[0]['occupied_playersid'] == $playersid && $general[0]['intID'] == $roleInfo['aggressor_general'])) {
			$value['status'] = 1002;
			$value['message'] = $fight_lang['model_msg_31'];			
/*hk*/		} elseif ($general [0] ['gohomeTime'] > $_SGLOBAL['timestamp'] && ($general [0] ['act'] == 4 || $general [0] ['act'] == 7) ) {
			$value ['status'] = 30;
			$value ['message'] = $fight_lang['model_msg_32'];
		} elseif ($general [0] ['gohomeTime'] > $_SGLOBAL['timestamp'] && $general [0] ['act'] == 3 ) {
			$value ['status'] = 30;
			$value ['message'] = $fight_lang['model_msg_33'];
		} elseif ($general [0] ['gohomeTime'] > $_SGLOBAL['timestamp'] && $general [0] ['act'] == 2 ) {
			$value ['status'] = 30;
			$value ['message'] = $fight_lang['model_msg_34'];
		} elseif ( $general [0] ['act'] == 1 || $general [0] ['act'] == 6 ) {
			$value ['status'] = 30;
			$value ['message'] = $fight_lang['model_msg_35'];/*hk*///设置武将自救的限制条件
		} elseif (empty($dgeneral)) {
			$value['status'] = 4;
			$value['message'] = $fight_lang['model_msg_36'];			
		} else {
			$k = 0;
			$dvalue = array();	
			$value['a_level'] = $roleInfo['player_level'];
			$value['a_sex'] = $roleInfo['sex'];
			$value['d_level'] = $bzlInfo['player_level'];
			$value['d_sex'] = $bzlInfo['sex'];
			$value['wjxm'] = stripcslashes($roleInfo['nickname']);
			$value['fsxm'] = stripcslashes($bzlInfo['nickname']);
			for ($i = 0; $i < 1; $i++) {
				$k++;
				$dgeneral[$i]['general_sort_data'] = $general[$i]['general_sort'];
				$dgeneral[$i]['general_sort'] = $k;							
				$dgeneral[$i]['command_soldier'] = $general[$i]['general_life'];	
				$dvalue[] = $dgeneral[$i];		
				$def_g_level = $dgeneral[$i]['general_level'];              //将领级别
        $def_g_tf = round($dgeneral[$i]['understanding_value'],1);  //将领天赋
				$def_g_name = $dgeneral[$i]['general_name'];	
				$last_income_time = $dgeneral[$i]['last_income_time'];  //上次收益时间
				$syjg = $nowTime - $last_income_time; 
				$def_g_info_array[] = array('mc'=>$def_g_name,'lv'=>$def_g_level,'ng'=>0,'tf'=>$def_g_tf);						
			} 
			$defend_data = $dvalue;		
			//开始战斗
			$gf_jwdj = $roleInfo['mg_level'];
			$sf_jwdj = $bzlInfo['mg_level'];
			$result = actModel::fight($attack_data,$defend_data,'',$gjcl,$fscl,0,$gf_jwdj,$sf_jwdj);		
			
			// 评分
			$pfArr = fightModel::getPf($attack_data, $roleInfo);
			$value['wjzdlpf'] = $pfArr['wjzdlpf'];
			$value['djpf'] = $pfArr['djpf'];
			$value['tfpf'] = $pfArr['tfpf'];
			$value['zbpf'] = $pfArr['zbpf'];
			$value['jwpf'] = $pfArr['jwpf'];
			$value['jnpf'] = $pfArr['jnpf'];
				
			$dfpfArr = fightModel::getPf($defend_data, $bzlInfo);
			$value['gkpf'] = $dfpfArr['wjzdlpf'];
			$value['djpfzb'] = $dfpfArr['djpf'];
			$value['tfpfzb'] = $dfpfArr['tfpf'];
			$value['zbpfzb'] = $dfpfArr['zbpf'];
			$value['jwpfzb'] = $dfpfArr['jwpf'];
			$value['jnpfzb'] = $dfpfArr['jnpf'];
			$value['tgsxpf'] = $dfpfArr['wjzdlpf'];
			
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
			//战斗后自动补血
			$zdbx = fightModel::zdbx($attack_data,$playersid,$attackGeneralLeftSoldier);
			if (!empty($zdbx)) {
				$attackGeneralLeftSoldier = $zdbx['ginfo'];
				$updateRole['coins'] = $zdbx['tq'];
				$value['tq'] = $updateRole['coins'];
			}			
			if ($attack_soldier_left <= 0) {
				$value['status'] = 1001;	
				$value['gfcl'] = $gjcl;
				$value['sfcl'] = $fscl;									
				//更新攻击方将领数据
				//$wjhqjy = array();
				for ($k = 0; $k < 1; $k++) {
					$id = $attack_data[$k]['intID'];
					//$current_command_soldier = $attack_data[$k]['command_soldier'];                   //原有士兵数量
					$left_command_soldier = $attackGeneralLeftSoldier[$id];                             //剩余士兵数量
					$sssm = $attack_data[$k]['general_life'];
					$updateGeneral['general_life'] = $left_command_soldier;    
					$attack_data[$k]['general_life'] = $left_command_soldier;
					$attack_data[$k]['command_soldier'] = $left_command_soldier;	
					$attack_data[$k]['general_sort'] =  $attack_data[$k]['general_sort_data']; 
					//$attack_data[$k]['attack_value'] = $attack_data[$k]['attack_old_value'];
					$value['gid'] = $gid;
					$value['smz'] = intval($left_command_soldier);
					$whereGeneral['intID'] = $id;
					$common->updatetable('playergeneral',$updateGeneral,$whereGeneral);               //更新将领数据			  				
					$newData[$attack_data[$k]['sortid']] = $attack_data[$k];     	     		     
					$common->updateMemCache(MC.$playersid.'_general',$newData);      
					$zbtljcArray = array ();
          foreach ($zbslots as $slot) {
            $zbid = $attack_data[$k] [$slot];
            if ($zbid != 0) {
              $zbInfo = toolsModel::getZbSx ( $attack_data[$k] ['playerid'], $zbid );
              $zbtljcArray [] = $zbInfo ['tl'];
            }
          }
          $zbtljc = array_sum ( $zbtljcArray );
					$sxxs = genModel::sxxs ( $attack_data[$k]['professional'] );  
					$tl = genModel::hqwjsx($attack_data[$k]['general_level'],$attack_data[$k] ['understanding_value'],$attack_data[$k] ['professional_level'],$attack_data[$k] ['llcs'],$jwjc,$zbtljc,$sxxs ['tl'],$attack_data[$k] ['py_tl']);						
					$value['lshf'] = ceil(($tl * 10 - $left_command_soldier) * addLifeCost($roleInfo['player_level']));					
					unset($updateGeneral);
					unset($whereGeneral);
					unset($newData);
			   }
		       //发送消息
		       $mesageDen = array('xllx'=>17,'wjmc1'=>$roleInfo['nickname'],'wjid1'=>intval($playersid),'wjwjmc1'=>$att_g_info_array[0]['mc'],'wjwjid1'=>intval($gid));
			   //$mesageDen = addcslashes(json_encode($defMesArra),'\\');				
		       $messageInfoDen = array('playersid'=>0,'jsid'=>$roleInfo['playersid'],'toplayersid'=>$bzlInfo['playersid'],'subject'=>'您成功守护领地','message'=>$mesageDen,'type'=>1,'genre'=>17,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);
			} else {
			   $value['status'] = 0;
			   $value['gfcl'] = $gjcl;
			   $value['sfcl'] = $fscl;						   
			   $value['time'] = 7200;
		       $zlsy = fightModel::zlsy($roleInfo['player_level'],2,$syjg);
		       $sj_tq = ceil($zlsy / 2);
		       $updateDRole['coins'] = $bzlInfo['coins'] + $sj_tq;
		       if ($updateDRole['coins'] > COINSUPLIMIT) {
		       	   $updateDRole['coins'] = COINSUPLIMIT; 
		       	   $sj_tq = COINSUPLIMIT - $bzlInfo['coins'];
		       }
			   zydModel::zydlog('app.php?task=fksy&option=fight',array('hqtq'=>$sj_tq),$bzlInfo['player_level'],$bzlInfo['userid']);
		       $updateDRoleWhere['playersid'] = $bzlInfo['playersid'];
		       $common->updatetable('player',$updateDRole,$updateDRoleWhere);
		       $common->updateMemCache(MC.$bzlInfo['playersid'],$updateDRole);	
		       $value['sj_tuid'] = intval($bzlInfo['playersid']);
		       $value['sj_tq'] = $sj_tq;
		       //减少领地数量
		       //fightModel::reduceLdsl($roleInfo['aggressor_playersid'],$playersid);   
			   //更新攻击方将领数据
			   //$wjhqjy = array();
			   for ($k = 0; $k < 1; $k++) {
					$id = $attack_data[$k]['intID'];
					//$current_command_soldier = $attack_data[$k]['command_soldier'];                   //原有士兵数量
					$left_command_soldier = $attackGeneralLeftSoldier[$id];                             //剩余士兵数量
					$wjmc = $attack_data[$k]['general_name'];                                           //武将名称
					$sssm = $attack_data[$k]['general_life'] - $left_command_soldier;                   //武将损失的生命
					$updateGeneral['general_life'] = $left_command_soldier;    
					$attack_data[$k]['general_life'] = $left_command_soldier;
					$attack_data[$k]['command_soldier'] = $left_command_soldier;	
					$attack_data[$k]['general_sort'] =  $attack_data[$k]['general_sort_data']; 
					//$attack_data[$k]['attack_value'] = $attack_data[$k]['attack_old_value'];					
					if ($attack_data[$k]['intID'] == $gid) {
						$attack_data[$k]['occupied_playersid'] = $updateGeneral['occupied_playersid'] = $playersid;
						$attack_data[$k]['occupied_player_level'] = $updateGeneral['occupied_player_level'] = $roleInfo['player_level'];
						$attack_data[$k]['occupied_player_nickname'] = $updateGeneral['occupied_player_nickname'] = mysql_escape_string($roleInfo['nickname']);
						$attack_data[$k]['last_income_time'] = $updateGeneral['last_income_time'] = $nowTime; 
						$attack_data[$k]['occupied_end_time'] = $updateGeneral['occupied_end_time'] = $nowTime + zlsc();									 
					}
					$value['gid'] = $gid;
					$value['smz'] = intval($left_command_soldier);
					$whereGeneral['intID'] = $id;
					$common->updatetable('playergeneral',$updateGeneral,$whereGeneral);               //更新将领数据			  				
					$newData[$attack_data[$k]['sortid']] = $attack_data[$k];     	     		     
					$common->updateMemCache(MC.$playersid.'_general',$newData);      
					$zbtljcArray = array ();
          foreach ($zbslots as $slot) {
            $zbid = $general[0] [$slot];
            if ($zbid != 0) {
              $zbInfo = toolsModel::getZbSx ( $attack_data[$k] ['playerid'], $zbid );
              $zbtljcArray [] = $zbInfo ['tl'];
            }
          }
          $zbtljc = array_sum ( $zbtljcArray );
					$sxxs = genModel::sxxs ( $attack_data[$k]['professional'] );
					$tl = genModel::hqwjsx($attack_data[$k]['general_level'],$attack_data[$k] ['understanding_value'],$attack_data[$k] ['professional_level'],$attack_data[$k] ['llcs'],$jwjc,$zbtljc,$sxxs ['tl'],$attack_data[$k] ['py_tl']);						 
					$value['lshf'] = ceil(($tl * 10 - $left_command_soldier) * addLifeCost($roleInfo['player_level']));					
					unset($updateGeneral);
					unset($whereGeneral);
					unset($newData);
			   }					    
			   //$incomeTimes = 0;			   
			   //更新防守方将领数据
			   foreach ($dgeneral as $key => $dvalue) {
				   if ($dvalue['intID'] == $roleInfo['aggressor_general']) {
					 $dvalue['occupied_playersid'] = $updateDGen['occupied_playersid'] = 0;
					 $dvalue['occupied_player_level'] = $updateDGen['occupied_player_level'] = 0;
					 $dvalue['occupied_player_nickname'] = $updateDGen['occupied_player_nickname'] = '';					 		 	
					 $dvalue['last_income_time']= $updateDGen['last_income_time'] = 0; 	
					 $dvalue['occupied_end_time']= $updateDGen['occupied_end_time'] = 0; 			
					 $dwhere['intID'] = $dvalue['intID'];
				     $common->updatetable('playergeneral',$updateDGen,$dwhere);  		 		 
				   }  
				   $newdData[$dvalue['sortid']] = $dvalue;			   	         
				}
				$common->updateMemCache(MC.$roleInfo['aggressor_playersid'].'_general',$newdData);
				
				/*更新玩家数据*/
				$updateRole['aggressor_playersid'] = $playersid;
				$updateRole['aggressor_nickname'] = mysql_escape_string($roleInfo['nickname']);
				$updateRole['aggressor_level'] = $roleInfo['player_level'];
				$updateRole['aggressor_general'] = $gid;
				$updateRole['zf_aggressor_general'] = $gid;
				$updateRole['is_defend'] = 1;
				$updateRole['strategy'] = $roleInfo['zf_strategy'];
			    $updateRole['end_defend_time'] = $nowTime;	
				
		        //发送消息
                $defMesArra = array('xllx'=>16,'wjmc1'=>$roleInfo['nickname'],'wjid1'=>intval($playersid),'wjwjmc1'=>$att_g_info_array[0]['mc'],'wjwjid1'=>intval($gid));
			//$mesageDen = addcslashes(json_encode($defMesArra),'\\');				
			$mesageDen = $defMesArra;
		        $messageInfoDen = array('playersid'=>0,'jsid'=>$roleInfo['playersid'],'toplayersid'=>$bzlInfo['playersid'],'subject'=>$fight_lang['model_msg_37'],'message'=>$mesageDen,'type'=>1,'genre'=>16,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    
		        //有几率获得取技能书	
		        $sjrq = date('Ymd',$nowTime);
		        $pvpcs = $mc->get(MC.$playersid."_pvp_".$roleInfo['aggressor_playersid'].'_'.$sjrq);	
		        if (empty($pvpcs)) {
		        	$pvpcs = 1;
		        }
		        if ($pvpcs < 4) {		        
			        if (rand(0,99) < 20) {
			        	$jnid = rand(1,15);
			        	$djid = jndyid($jnid);
			        	//$playerBag = toolsModel::getMyItemInfo($playersid); // 背包列表返回协议优化
			        	//$addItem = toolsModel::addPlayersItem($roleInfo,$djid);
						$addItem = $player->AddItems(array($djid=>1));
			        	if ($addItem === false) {
			        		$value['status'] = 1004;
			        	} else {
			        		if (empty($pvpcs)) {
			        			$pvpcs = 0;
			        		}
			        		$jnInfo = jnk($jnid);
			        		$value['dlmc'] = $jnInfo['n'];
			        		$value['dlsl'] = 1;
			        		$value['dliid'] = $jnInfo['iconid'];
			        		//$bbInfo = toolsModel::getAllItems($playersid);
			        		//$value['list'] = $bbInfo['list'];
			        		//$simBg[] = array('insert'=>1, 'ItemID'=>$djid); // 背包列表返回协议优化
			        		//$bagData = toolsModel::getBglist($simBg, $playersid, $playerBag); // 背包列表返回协议优化
							$bagData = $player->GetClientBag();
			        		$value['list'] = $bagData; // 背包列表返回协议优化
			        	}	
			        }
		        }
		        $mc->set(MC.$playersid."_pvp_".$roleInfo['aggressor_playersid'].'_'.$sjrq,$pvpcs + 1,0,86400);
			}			
			if (!empty($updateRole)) {
				$updateRoleWhere['playersid'] = $playersid;
				$common->updatetable('player',$updateRole,$updateRoleWhere);
				$common->updateMemCache(MC.$playersid,$updateRole);		
			}		
			$value['begin'] = $result['begin'];                                               //开始士兵情形
			$round = $result['round'];                                               //战斗回合	
			$newround = heroCommon::arr_foreach($round);
			$value['round'] = $newround;
			$round = null;	
			$newround = null;						
		} 
		if (!empty($messageInfoDen)) {
			$messageInfoDen['request'] = addcslashes(json_encode($value),'\\');
			lettersModel::addMessage($messageInfoDen); 
		}
		return $value; 	
    }
    
    //他救
    public static function friend_help($playersid,$gid,$tuId,$gjcl = 0,$hyp = 0) {
    	global $common,$mc,$db,$_SGLOBAL,$zbslots,$G_PlayerMgr,$fight_lang,$sys_lang; 
		$nowTime = $_SGLOBAL['timestamp'];
    	$roleInfo['playersid'] = $playersid;
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$sys_lang[7]);
    	roleModel::getRoleInfo($roleInfo);
		$jwdj = $roleInfo['mg_level'];
		$jwInfo = jwmc($jwdj);
		$jwjc = 1 + $jwInfo['jc'] / 100;    	
    	if (!($bzlInfo = $mc->get(MC.$tuId))) {
    		$bzlInfo = $db->fetch_array($db->query("SELECT * FROM ".$common->tname('player')." WHERE `playersid` = '$tuId' LIMIT 1"));
    	} 
    	if (empty($bzlInfo)) {
    		$value = array('status'=>3,'message'=>$fight_lang['model_msg_10']);
    		return $value;    		
    	}
    	//$player = $G_PlayerMgr->GetPlayer($bzlInfo['aggressor_playersid']);
    	if (!($defendRoleInfo = $mc->get(MC.$bzlInfo['aggressor_playersid']))) {
    		$defendRoleInfo = $db->fetch_array($db->query("SELECT * FROM ".$common->tname('player')." WHERE `playersid` = '".$bzlInfo['aggressor_playersid']."' LIMIT 1"));
    	}
    	if (empty($defendRoleInfo)) {
    		$value = array('status'=>3,'message'=>$fight_lang['model_msg_38']);
    		return $value;    		    		
    	}
    	$defendplayer = $G_PlayerMgr->GetPlayer($bzlInfo['aggressor_playersid']);
    	$fscl = $bzlInfo['strategy'];  //防守策略 	
    	$messageInfoSz = array();    	
    	$general = cityModel::getGeneralData($playersid,'',$gid);
        $avalue = array();
		$j = 0;
		for ($i = 0; $i < 1; $i++) {
			if ($general[$i]['general_life'] > 0) {
				$j++;
				$general[$i]['general_sort_data'] = $general[$i]['general_sort'];
				$general[$i]['general_sort'] = $j;							
				$general[$i]['command_soldier'] = $general[$i]['general_life'];	
				$avalue[] = $general[$i];	
		        $att_g_level = $general[$i]['general_level'];              //将领级别
		        $att_g_tf = round($general[$i]['understanding_value'],1);  //将领天赋
			    //$att_g_ng = $general[$i]['professional_level'];            //将领内功		
			    $last_income_time = $general[$i]['last_income_time'];      //上次收益时间
			    $syjg = $_SGLOBAL['timestamp'] - $last_income_time;			
				$att_g_name = $general[$i]['general_name'];
			    $att_g_info_array[] = array('mc'=>$att_g_name,'lv'=>$att_g_level,'ng'=>0,'tf'=>$att_g_tf);				
			}
		} 
		$attack_data = $avalue;
		$dgeneral = cityModel::getGeneralData($bzlInfo['aggressor_playersid'],'',$bzlInfo['aggressor_general'],0);     	
		if ($roleInfo['player_level'] < 6) {
     		$value['status'] = 1021;
    		$value['message'] = $fight_lang['model_msg_39'];   		
    	} elseif ($bzlInfo['is_reason'] == 1) {
    		$value['status'] = 21;
    		$value['message'] = $fight_lang['model_msg_4'];
    	} elseif (empty($gid)) {
			$value['status'] = 3;
			$value['message'] = $sys_lang[7];			
		} elseif ($bzlInfo['aggressor_playersid'] == 0 || $bzlInfo['is_defend'] == 1 || ($bzlInfo['aggressor_playersid'] != 0 && $bzlInfo['end_defend_time'] < $_SGLOBAL['timestamp'] && $bzlInfo['aggressor_playersid'] != $tuId)) {
			$value['status'] = 1002;
			$value['message'] = $fight_lang['model_msg_40'];
		} elseif (($general[0]['occupied_playersid'] == $playersid && $general[0]['occupied_playersid'] == $roleInfo['aggressor_general']) || ($general[0]['occupied_playersid'] != 0 && $general[0]['occupied_playersid'] != $playersid && $general[0]['occupied_end_time'] > $_SGLOBAL['timestamp'])) {
			$value['status'] = 1002;
			$value['message'] = $fight_lang['model_msg_41'];			
/*hk*/		} elseif ($general [0] ['gohomeTime'] > $_SGLOBAL['timestamp'] && ($general [0] ['act'] == 4 || $general [0] ['act'] == 7) ) {
			$value ['status'] = 30;
			$value ['message'] = $fight_lang['model_msg_42'];
		} elseif ($general [0] ['gohomeTime'] > $_SGLOBAL['timestamp'] && $general [0] ['act'] == 3 ) {
			$value ['status'] = 30;
			$value ['message'] = $fight_lang['model_msg_43'];
		} elseif ($general [0] ['gohomeTime'] > $_SGLOBAL['timestamp'] && $general [0] ['act'] == 2 ) {
			$value ['status'] = 30;
			$value ['message'] = $fight_lang['model_msg_44'];
		} elseif ( $general [0] ['act'] == 1 || $general [0] ['act'] == 6 ) {
			$value ['status'] = 30;
			$value ['message'] = $fight_lang['model_msg_45'];/*hk*///设置武将救别人的限制条件
		}/* elseif ($roleInfo['regionid'] != $bzlInfo['regionid']) {
			$value['status'] = 3;
			$value['message'] = $sys_lang[7];
		}*/ elseif (empty($attack_data)) {
			$value['status'] = 1048;
			$value['gid'] = $gid;
			$zbtljcArray = array ();
      foreach ($zbslots as $slot) {
        $zbid = $general[0] [$slot];
        if ($zbid != 0) {
          $zbInfo = toolsModel::getZbSx ( $general[0] ['playerid'], $zbid );
          $zbtljcArray [] = $zbInfo ['tl'];
        }
      }
      $zbtljc = array_sum ( $zbtljcArray );
			$sxxs = genModel::sxxs ( $general[0]['professional'] );
			$tl = genModel::hqwjsx($general[0]['general_level'],$general[0] ['understanding_value'],$general[0] ['professional_level'],$general[0] ['llcs'],$jwjc,$zbtljc,$sxxs ['tl'],$general[0] ['py_tl']);				 
			$value['lshf'] = ceil($tl * 10 * addLifeCost($roleInfo['player_level']));				
		} elseif (empty($dgeneral)) {
			$value['status'] = 4;
			$value['message'] = $fight_lang['model_msg_36'];				
		} elseif (empty($defendRoleInfo)) {
			$value['status'] = 4;
			$value['message'] = $fight_lang['model_msg_46'];				
		} elseif ($playersid == $tuId) {
			$value['status'] = 3;
			$value['message'] = $sys_lang[7];					
		} else {
			$pass = 0;
			if ($hyp == 1) {
				$silver = $roleInfo['silver'];
				if ($silver < 5) {
					$valueYp['status'] = 68;
					$valueYp ['yp'] = $silver;
					$valueYp['xyxhyp'] = 5;
					$rep_arr1 = array('{xhyp}', '{yp}');
					$rep_arr2 = array($valueYp['xyxhyp'], $valueYp ['yp']);
		    		$valueYp['message'] = str_replace($rep_arr1, $rep_arr2, $sys_lang[6]);     
		    		return $valueYp;					
				} else {
					$updateRole['silver'] = $silver - 5;
					$value ['yp'] = $updateRole['silver'];
					$value['xhyp'] = 5;	
					$pass = 1;				
				}
			}
			if ($pass == 0) {
				if ($mc->add(MC.$roleInfo['playersid'].'_jbr',1,0,1800) === false) {
					$valueJg['status'] = 1021;
					$valueJg['xyxhyp'] = 5;
					$valueJg['message'] = $fight_lang['model_msg_47'];
					return $valueJg;
				}
			}
			//并发控制
	    	$bfkz = heroCommon::bfkz(MC.$tuId.'_bjj',30,$fight_lang['model_msg_48']);
	    	if ($bfkz != 'go' && $pass == 0) {
	    		$valueBf['status'] = 1002;
	    		$valueBf['message'] = $bfkz;
	    		return $valueBf;
	    	} 			
			$value['d_level'] = $defendRoleInfo['player_level'];
			$value['d_sex'] = $defendRoleInfo['sex'];
			$value['a_level'] = $roleInfo['player_level'];
			$value['a_sex'] = $roleInfo['sex'];
			$value['wjxm'] = stripcslashes($roleInfo['nickname']);
			$value['fsxm'] = stripcslashes($defendRoleInfo['nickname']);		
			$k = 0;
			$dvalue = array();	
			for ($i = 0; $i < 1; $i++) {
				$k++;
				$dgeneral[$i]['general_sort_data'] = $general[$i]['general_sort'];
				$dgeneral[$i]['general_sort'] = $k;							
				$dgeneral[$i]['command_soldier'] = $general[$i]['general_life'];	
				$dvalue[] = $dgeneral[$i];
				$def_g_level = $dgeneral[$i]['general_level'];              //将领级别
		        $def_g_tf = round($dgeneral[$i]['understanding_value'],1);  //将领天赋
				$def_g_name = $dgeneral[$i]['general_name'];
				$def_g_info_array[] = array('mc'=>$def_g_name,'lv'=>$def_g_level,'ng'=>0,'tf'=>$def_g_tf);														
			} 
			$defend_data = $dvalue;		
			//开始战斗
			$gf_jwdj = $roleInfo['mg_level'];
			$sf_jwdj = $defendRoleInfo['mg_level'];
			$result = actModel::fight($attack_data,$defend_data,'',$gjcl,$fscl,0,$gf_jwdj,$sf_jwdj);	

			// 评分
			$pfArr = fightModel::getPf($attack_data, $roleInfo);
			$value['wjzdlpf'] = $pfArr['wjzdlpf'];
			$value['djpf'] = $pfArr['djpf'];
			$value['tfpf'] = $pfArr['tfpf'];
			$value['zbpf'] = $pfArr['zbpf'];
			$value['jwpf'] = $pfArr['jwpf'];
			$value['jnpf'] = $pfArr['jnpf'];
			
			$dfpfArr = fightModel::getPf($defend_data, $defendRoleInfo);
			$value['gkpf'] = $dfpfArr['wjzdlpf'];
			$value['djpfzb'] = $dfpfArr['djpf'];
			$value['tfpfzb'] = $dfpfArr['tfpf'];
			$value['zbpfzb'] = $dfpfArr['zbpf'];
			$value['jwpfzb'] = $dfpfArr['jwpf'];
			$value['jnpfzb'] = $dfpfArr['jnpf'];
			$value['tgsxpf'] = $dfpfArr['wjzdlpf'];
			
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
			//战斗后自动补血
			$zdbx = fightModel::zdbx($attack_data,$playersid,$attackGeneralLeftSoldier);
			if (!empty($zdbx)) {
				$attackGeneralLeftSoldier = $zdbx['ginfo'];
				$updateRole['coins'] = $zdbx['tq'];
				$value['tq'] = $updateRole['coins'];
			}
			$hqzlz = hqzlz($roleInfo,$defendRoleInfo,$general,$dgeneral,1,$player,$defendplayer);  //理论获取战功值 
			$kczlz = ceil($hqzlz / 4);  //理论扣除战功值	
								
			if ($attack_soldier_left <= 0) {
				$value['status'] = 1001;
				$value['gfcl'] = $gjcl;
				$value['sfcl'] = $fscl;	
				$sjkczgz = 0;	
				$kzgz = rankModel::decreaseZg($playersid,$kczlz,$sjkczgz,13,mysql_escape_string($bzlInfo['nickname']),$tuId);
				$updateRole = array_merge($updateRole, $kzgz); //更新战功值 		
				$value['zg'] = intval($kzgz['ba']);
				$value['zgz'] = $sjkczgz;	
				//更新攻击方将领数据
				//$wjhqjy = array();
				for ($k = 0; $k < 1; $k++) {
					$id = $attack_data[$k]['intID'];
					//$current_command_soldier = $attack_data[$k]['command_soldier'];                   //原有士兵数量
					$left_command_soldier = $attackGeneralLeftSoldier[$id];                             //剩余士兵数量
					$updateGeneral['general_life'] = $left_command_soldier;    
					$attack_data[$k]['general_life'] = $left_command_soldier;
					$attack_data[$k]['command_soldier'] = $left_command_soldier;	
					$attack_data[$k]['general_sort'] =  $attack_data[$k]['general_sort_data']; 
					//$attack_data[$k]['attack_value'] = $attack_data[$k]['attack_old_value'];
					$value['gid'] = $gid;
					$value['smz'] = intval($left_command_soldier);
					$whereGeneral['intID'] = $id;
					$common->updatetable('playergeneral',$updateGeneral,$whereGeneral);               //更新将领数据			  				
					$newData[$attack_data[$k]['sortid']] = $attack_data[$k];     	     		     
					$common->updateMemCache(MC.$playersid.'_general',$newData);     
					$zbtljcArray = array ();
			        foreach ($zbslots as $slot) {
			            $zbid = $attack_data[$k] [$slot];
			            if ($zbid != 0) {
			              $zbInfo = toolsModel::getZbSx ( $attack_data[$k] ['playerid'], $zbid );
			              $zbtljcArray [] = $zbInfo ['tl'];
			            }
			        }
			        $zbtljc = array_sum ( $zbtljcArray );
					$sxxs = genModel::sxxs ( $attack_data[$k]['professional'] );
					$tl = genModel::hqwjsx($attack_data[$k]['general_level'],$attack_data[$k] ['understanding_value'],$attack_data[$k] ['professional_level'],$attack_data[$k] ['llcs'],$jwjc,$zbtljc,$sxxs ['tl'],$attack_data[$k] ['py_tl']);					  
					$value['lshf'] = ceil(($tl * 10 - $left_command_soldier) * addLifeCost($roleInfo['player_level']));					 
					unset($updateGeneral);
					unset($whereGeneral);
					unset($newData);
			   }		
		       //发送消息
		       $szMesArra = array('xllx'=>15,'wjmc1'=>$roleInfo['nickname'],'wjid1'=>intval($playersid),'wjwjmc1'=>$att_g_info_array[0]['mc'],'wjwjid1'=>intval($gid),'wjmc2'=>$bzlInfo['nickname'],'wjid2'=>$tuId);
			   //		       $mesagesSz = addcslashes(json_encode($szMesArra),'\\');				
			   $mesagesSz = $szMesArra;
		       $messageInfoSz = array('playersid'=>0,'jsid'=>0,'toplayersid'=>$bzlInfo['aggressor_playersid'],'subject'=>'被好友解救失败','message'=>$mesagesSz,'type'=>1,'genre'=>15,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    
			   //lettersModel::addMessage(json_encode($messageInfoSz));         //添加山寨拥有方消息  		

               $szMesArra_1 = array('xllx'=>19,'wjmc1'=>$roleInfo['nickname'],'wjid1'=>intval($playersid),'wjwjmc2'=>$def_g_info_array[0]['mc'],'wjwjid2'=>intval($bzlInfo['aggressor_general']),'wjmc2'=>$bzlInfo['aggressor_nickname'],'wjid2'=>intval($bzlInfo['aggressor_playersid']));
		       //$mesagesSz_1 = addcslashes(json_encode($szMesArra_1),'\\');				
			   $mesagesSz_1 = $szMesArra_1;
		       $messageInfoSz_1 = array('playersid'=>0,'jsid'=>0,'toplayersid'=>$tuId,'subject'=>'被好友解救失败','message'=>$mesagesSz_1,'type'=>1,'genre'=>19,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);
			} else {				
				$value['status'] = 0;
				$value['gfcl'] = $gjcl;
				$value['sfcl'] = $fscl;	
				$sjzgzgz = 0;
				$addzgz = rankModel::addZg($playersid,$hqzlz,$sjzgzgz,12,mysql_escape_string($bzlInfo['nickname']),$tuId); //增加战功值						
				$updateRole = array_merge($updateRole, $addzgz); //更新战功值 		
				$value['zg'] = intval($addzgz['ba']);
				$value['zgz'] = intval($sjzgzgz);					
				$zlsy = fightModel::zlsy($bzlInfo['player_level'],2,$syjg);
				$sj_tq = ceil($zlsy / 2);
				$updateDRole['coins'] = $defendRoleInfo['coins'] + $sj_tq;
				if ($updateDRole['coins'] > COINSUPLIMIT) {
					$updateDRole['coins'] = COINSUPLIMIT;
					$sj_tq = COINSUPLIMIT - $defendRoleInfo['coins'];
				}
				$sjkczgz = 0;
				$kzgz = rankModel::decreaseZg($defendRoleInfo['playersid'],$kczlz,$sjkczgz,15,mysql_escape_string($roleInfo['nickname']),$playersid);
				$updateDRole = array_merge($updateDRole, $kzgz); //更新战功值 					
				$updateDRoleWhere['playersid'] = $defendRoleInfo['playersid']; 	
				$common->updatetable('player',$updateDRole,$updateDRoleWhere);
				$common->updateMemCache(MC.$defendRoleInfo['playersid'],$updateDRole);	
				$value['sj_tuid'] = intval($defendRoleInfo['playersid']);
				$value['sj_tq'] = $sj_tq;		
				//减少领地数量
				//fightModel::reduceLdsl($bzlInfo['aggressor_playersid'],$bzlInfo['playersid']);					
				for ($k = 0; $k < 1; $k++) {
					$id = $attack_data[$k]['intID'];
					//$current_command_soldier = $attack_data[$k]['command_soldier'];                   //原有士兵数量
					$left_command_soldier = $attackGeneralLeftSoldier[$id];                             //剩余士兵数量
					$sssm = $attack_data[$k]['general_life'] - $left_command_soldier;                   //损失生命
					$wjmc = $attack_data[$k]['general_name'];                                           //武将名称
					$updateGeneral['general_life'] = $left_command_soldier;    
					$attack_data[$k]['general_life'] = $left_command_soldier;
					$attack_data[$k]['command_soldier'] = $left_command_soldier;	
					$attack_data[$k]['general_sort'] =  $attack_data[$k]['general_sort_data']; 
					$value['smz'] = intval($left_command_soldier);
					$value['gid'] = $gid;
					//$attack_data[$k]['attack_value'] = $attack_data[$k]['attack_old_value'];		
					
					$whereGeneral['intID'] = $id;
					$common->updatetable('playergeneral',$updateGeneral,$whereGeneral);               //更新将领数据			  				
					$newData[$attack_data[$k]['sortid']] = $attack_data[$k];     	     		     
					$common->updateMemCache(MC.$playersid.'_general',$newData);      
					$zbtljcArray = array ();
          foreach ($zbslots as $slot) {
            $zbid = $attack_data[$k] [$slot];
            if ($zbid != 0) {
              $zbInfo = toolsModel::getZbSx ( $attack_data[$k] ['playerid'], $zbid );
              $zbtljcArray [] = $zbInfo ['tl'];
            }
          }
          $zbtljc = array_sum ( $zbtljcArray );
					$sxxs = genModel::sxxs ( $attack_data[$k]['professional'] );
					$tl = genModel::hqwjsx($attack_data[$k]['general_level'],$attack_data[$k] ['understanding_value'],$attack_data[$k] ['professional_level'],$attack_data[$k] ['llcs'],$jwjc,$zbtljc,$sxxs ['tl'],$attack_data[$k] ['py_tl']);					  
					$value['lshf'] = ceil(($tl * 10 - $left_command_soldier) * addLifeCost($roleInfo['player_level']));					
					unset($updateGeneral);
					unset($whereGeneral);
					unset($newData);
			   }					    
				//更新防守方将领数据
				foreach ($dgeneral as $key => $dvalue) {
				   if ($dvalue['intID'] == $bzlInfo['aggressor_general']) {
					  $dvalue['occupied_playersid'] = $updateDGen['occupied_playersid'] = 0;
					  $dvalue['occupied_player_level'] = $updateDGen['occupied_player_level'] = 0;
					  $dvalue['occupied_player_nickname'] = $updateDGen['occupied_player_nickname'] = '';					 		 	
					  $dvalue['last_income_time'] = $updateDGen['last_income_time'] = 0; 
					  $dvalue['occupied_end_time'] = $updateDGen['occupied_end_time'] = 0;	
					  $newdData[$dvalue['sortid']] = $dvalue;                 
				      $common->updateMemCache(MC.$bzlInfo['aggressor_playersid'].'_general',$newdData);	
					  $dwhere['intID'] = $dvalue['intID'];
					  $common->updatetable('playergeneral',$updateDGen,$dwhere);  
					  break;					      	 
				   }	         
				}			
				/*更新防守方玩家数据*/
	  			$updateBzl['aggressor_playersid'] = 0;
	  			$updateBzl['aggressor_nickname'] = '';
	  			$updateBzl['aggressor_level'] = 0; 	  			 
				$updateBzl['is_defend'] = 0;				
			    $updateBzl['end_defend_time'] = 0;	
			    $bzlGinfo = cityModel::getGeneralData($bzlInfo['playersid'],false,'*');	    
			    $gen = 0;
			    if ($bzlInfo['zf_aggressor_general'] != 0) {		
			    	if (!empty($bzlGinfo)) {
			    		foreach ($bzlGinfo as $bzlGinfoValue) {
			    			if ($bzlGinfoValue['intID'] == $bzlInfo['zf_aggressor_general'] && $bzlGinfoValue['act'] == 0) {
					  			$updateBzl['aggressor_playersid'] = $bzlInfo['playersid'];
					  			$updateBzl['aggressor_nickname'] = mysql_escape_string($bzlInfo['nickname']);
					  			$updateBzl['aggressor_level'] = $bzlGinfoValue['general_level']; 	  			 
								$updateBzl['is_defend'] = 1;				
							    $updateBzl['end_defend_time'] = $nowTime;		
							    $updateBzl['zf_aggressor_general']	= $bzlGinfoValue['intID'];
							    $gen = 1;
								$bzlGinfoValue['occupied_playersid'] = $bzlupdateGen['occupied_playersid'] = $bzlInfo['playersid'];
								$bzlGinfoValue['occupied_player_level'] = $bzlupdateGen['occupied_player_level'] = $bzlInfo['player_level'];
								$bzlGinfoValue['occupied_player_nickname'] = $bzlupdateGen['occupied_player_nickname'] = mysql_escape_string($bzlInfo['nickname']);					 		 	
								$bzlGinfoValue['last_income_time'] = $bzlupdateGen['last_income_time'] = $nowTime; 
								$bzlGinfoValue['occupied_end_time'] = $bzlupdateGen['occupied_end_time'] = $nowTime;	
								$gzlnewdData[$bzlGinfoValue['sortid']] = $bzlGinfoValue;                 
							    $common->updateMemCache(MC.$bzlInfo['playersid'].'_general',$gzlnewdData);	
								$bzlwhere['intID'] = $bzlGinfoValue['intID'];
								$common->updatetable('playergeneral',$bzlupdateGen,$bzlwhere);  	
							    break;	    				
			    			}			    			
			    		}
			    	}	
			    	if ($gen == 1) {
    			    	$updateBzl['strategy'] = $bzlInfo['zf_strategy'];
			    		$updateBzl['aggressor_general'] = $bzlInfo['zf_aggressor_general'];		
			    	} else {
    			    	$updateBzl['strategy'] = 1;
			    		$updateBzl['aggressor_general'] = 0;					    		
			    	}		    	    	
			    } else {
			    	$updateBzl['strategy'] = 1;
			    	//$updateBzl['aggressor_general'] = 0;			    	
			    	if (!empty($bzlGinfo)) {
			    		foreach ($bzlGinfo as $bzlGinfoValue) {
			    			if (($bzlGinfoValue['occupied_playersid'] == 0 || ($bzlGinfoValue['occupied_playersid'] != 0 && $bzlGinfoValue['occupied_playersid'] != $bzlInfo['playersid'] && $bzlGinfoValue['occupied_end_time'] < $nowTime)) && $bzlGinfoValue['act'] == 0) {
					  			$updateBzl['aggressor_playersid'] = $bzlInfo['playersid'];
					  			$updateBzl['aggressor_nickname'] = mysql_escape_string($bzlInfo['nickname']);
					  			$updateBzl['aggressor_level'] = $bzlGinfoValue['general_level']; 	  			 
								$updateBzl['is_defend'] = 1;				
							    $updateBzl['end_defend_time'] = $nowTime;		
							    $updateBzl['zf_aggressor_general']	= $bzlGinfoValue['intID'];
							    $updateBzl['aggressor_general'] = $bzlGinfoValue['intID'];
							    $gen = 1;
								$bzlGinfoValue['occupied_playersid'] = $bzlupdateGen['occupied_playersid'] = $bzlInfo['playersid'];
								$bzlGinfoValue['occupied_player_level'] = $bzlupdateGen['occupied_player_level'] = $bzlInfo['player_level'];
								$bzlGinfoValue['occupied_player_nickname'] = $bzlupdateGen['occupied_player_nickname'] = mysql_escape_string($bzlInfo['nickname']);					 		 	
								$bzlGinfoValue['last_income_time'] = $bzlupdateGen['last_income_time'] = $nowTime; 
								$bzlGinfoValue['occupied_end_time'] = $bzlupdateGen['occupied_end_time'] = $nowTime;	
								$gzlnewdData[$bzlGinfoValue['sortid']] = $bzlGinfoValue;                 
							    $common->updateMemCache(MC.$bzlInfo['playersid'].'_general',$gzlnewdData);	
								$bzlwhere['intID'] = $bzlGinfoValue['intID'];
								$common->updatetable('playergeneral',$bzlupdateGen,$bzlwhere);  	
							    break;	    				
			    			}			    			
			    		}
			    	}
			    	if ($gen == 0) {
			    		$updateBzl['aggressor_general'] = 0;
			    	}
			    }		  			
				$updateBzlWhere['playersid'] = $tuId;
				$common->updatetable('player',$updateBzl,$updateBzlWhere);
				$common->updateMemCache(MC.$tuId,$updateBzl);
				/*更新防守方玩家数据结束*/	
				
		        $szMesArra = array('G'=>$sjkczgz,'xllx'=>14,'wjmc1'=>$roleInfo['nickname'],'wjid1'=>intval($playersid),'wjwjmc1'=>$att_g_info_array[0]['mc'],'wjwjid1'=>intval($gid),'wjmc2'=>$bzlInfo['nickname'],'wjid2'=>intval($tuId));
				//$mesagesSz = addcslashes(json_encode($szMesArra),'\\');				
				$mesagesSz = $szMesArra;
		        $messageInfoSz = array('playersid'=>0,'jsid'=>0,'toplayersid'=>$bzlInfo['aggressor_playersid'],'subject'=>$fight_lang['model_msg_49'],'message'=>$mesagesSz,'type'=>1,'genre'=>14,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);		    
			    
                $szMesArra_2 = array('xllx'=>18,'wjmc1'=>$roleInfo['nickname'],'wjid1'=>intval($playersid),'wjwjmc2'=>$def_g_info_array[0]['mc'],'wjwjid2'=>intval($bzlInfo['aggressor_general']),'wjmc2'=>$bzlInfo['aggressor_nickname'],'wjid2'=>intval($bzlInfo['aggressor_playersid']));
				//				$mesagesSz_2 = addcslashes(json_encode($szMesArra_2),'\\');				
				$mesagesSz_2 = $szMesArra_2;
		        $messageInfoSz_2 = array('playersid'=>0,'jsid'=>0,'toplayersid'=>$tuId,'subject'=>$fight_lang['model_msg_49'],'message'=>$mesagesSz_2,'type'=>1,'genre'=>18,'tradeid'=>0,'is_passive'=>0,'interaction'=>1);
			    $rwid = questsController::OnFinish($roleInfo,"'jjhy'");         //处理是否完成解救好友任务    	
			    if (!empty($rwid))	{
				   $value['rwid'] = $rwid; 
			    }
			    $jlsy = fightModel::jlsy($playersid);	
			    if ($jlsy == 1) {
			    	$updateRole['food'] = $roleInfo['food'] + 1;
					$updateRole['last_update_food'] = $nowTime;					
					$value['hqjl'] = 1;	
					$value['jl'] = floor($updateRole['food']);
			    } else {
			    	$value['hqjl'] = 0;	
			    }
		        //有几率获得取技能书	
		        $sjrq = date('Ymd',$_SGLOBAL['timestamp']);
		        $pvpcs = $mc->get(MC.$playersid."_pvp_".$bzlInfo['aggressor_playersid'].'_'.$sjrq);	
		        if (empty($pvpcs)) {
		        	$pvpcs = 1;
		        }
		        if ($pvpcs < 4) {		        
			        if (rand(0,99) < 20) {
			        	$jnid = rand(1,15);
			        	$djid = jndyid($jnid);
			        	//$playerBag = toolsModel::getMyItemInfo($playersid); // 背包列表返回协议优化
			        	//$addItem = toolsModel::addPlayersItem($roleInfo,$djid);
						$addItem = $player->AddItems(array($djid=>1));
			        	if ($addItem === false) {
			        		$value['status'] = 1004;
			        	} else {
			        		$jnInfo = jnk($jnid);
			        		$value['dlmc'] = $jnInfo['n'];
			        		$value['dlsl'] = 1;
			        		$value['dliid'] = $jnInfo['iconid'];
			        		//$bbInfo = toolsModel::getAllItems($playersid);
			        		//$value['list'] = $bbInfo['list'];		
			        		//$simBg[] = array('insert'=>1, 'ItemID'=>$djid); // 背包列表返回协议优化
			        		//$bagData = toolsModel::getBglist($simBg, $playersid, $playerBag); // 背包列表返回协议优化
							$bagData = $player->GetClientBag();
			        		$value['list'] = $bagData; // 背包列表返回协议优化
			        	}	
			        }			        
		        }
		        $mc->set(MC.$playersid."_pvp_".$bzlInfo['aggressor_playersid'].'_'.$sjrq,$pvpcs + 1,0,86400);	
                //增加好感度
                socialModel::addFriendFeel($tuId,$playersid,1);		        
			}		
			$value['begin'] = $result['begin'];                                               //开始士兵情形
			$round = $result['round'];                                               //战斗回合	
			$newround = heroCommon::arr_foreach($round);
			$value['round'] = $newround;
			$round = null;	
			$newround = null;									
		} 
    	if (!empty($updateRole)) {
			$updateRoleWhere['playersid'] = $playersid;
			$common->updatetable('player',$updateRole,$updateRoleWhere);
			$common->updateMemCache(MC.$playersid,$updateRole);				    	
	    }		
		if (!empty($messageInfoSz)) {
			$messageInfoSz['request'] = addcslashes(json_encode($value),'\\');
			lettersModel::addMessage($messageInfoSz);
		}
    	if (!empty($messageInfoSz_1)) {
			$messageInfoSz_1['request'] = addcslashes(json_encode($value),'\\');
			lettersModel::addMessage($messageInfoSz_1);
		}	
		if (!empty($messageInfoSz_2)) {
			$messageInfoSz_2['request'] = addcslashes(json_encode($value),'\\');
			lettersModel::addMessage($messageInfoSz_2);			
		}	
		//$userinfo = $player->GetBaseInfo();
		//$value['zg'] = $userinfo['ba'];	
		return $value;   	
    } 

    //救人获取军粮
    public static function jlsy($playersid) {
    	global $common,$_SGLOBAL,$db;
    	//$_SGLOBAL['timestamp']
    	$sql = "SELECT * FROM ".$common->tname('hqjl_log')." WHERE `playersid` = '$playersid' LIMIT 1";
    	$rows = $db->fetch_array($db->query($sql));
    	if (empty($rows)) {
    		$insert['jl'] = 1;
    		$insert['jlhq_time'] = $_SGLOBAL['timestamp'];
    		$insert['playersid'] = $playersid;
    		return 1;
    	} else {
    		if (date('Y-m-d',$rows['jlhq_time']) == date('Y-m-d',$_SGLOBAL['timestamp'])) {
    			if ($rows['jl'] >= 10) {
    				return 0;
    			} else {
    				$common->updatetable('hqjl_log',array('jlhq_time'=>$_SGLOBAL['timestamp'],'jl'=>$rows['jl']+1),"playersid = '$playersid'");
    				return 1;
    			}
    		} else {
    			$common->updatetable('hqjl_log',array('jlhq_time'=>$_SGLOBAL['timestamp'],'jl'=>1),"playersid = '$playersid'");
    			return 1;
    		}
    	}
    }
    
    //发消息模版
    public static function getTalk() {
    	global $db,$common;
    	$temp = array();
    	$result = $db->query("SELECT aggressor_message FROM ".$common->tname('aggressor_message')." WHERE aggressor_playersid = '".$_SESSION['playersid']."' order by create_time desc LIMIT 1");
		$rows = $db->fetch_array($result);    	
    	if(!empty($rows)) {
    		$value['status'] = 0;
    		$value['msg'] = $rows['aggressor_message'];
    	}else{
    		$value['status'] = 1001;
    	}
    	return $value;
    }
    
    public static function getMsg() {
		global $fight_lang;
    	/*static*/ $temp = array(0=>$fight_lang['model_msg_50'][0],1=>$fight_lang['model_msg_50'][1],2=>$fight_lang['model_msg_50'][2],3=>$fight_lang['model_msg_50'][3],4=>$fight_lang['model_msg_50'][4],5=>$fight_lang['model_msg_50'][5]);
    	return $temp;
    }
    
    //发留言
    public static function setWord($pid,$topid,$mid,$msg,$mode = 0,$oid = '') {
    	global $db,$common,$mc;
    	$roleInfo['playersid'] = $topid;
    	roleModel::getRoleInfo($roleInfo);
    	$arr = fightModel::getMsg();
    	
    	if($roleInfo['aggressor_playersid'] <> $pid) {
    		$value['status'] = 0;
    	}else{
    		$value['status'] = 0;
    		$update['playersid'] = $topid;
    		$update['aggressor_playersid'] = $pid;
    		if($mid !== '') {
    			$update['type'] = 1;
    			$update['aggressor_message'] = $arr[$mid];
    		}else{
    			$update['type'] = 2;
    			$update['aggressor_message'] = $msg;
    		}
    		
    		$result = $db->query("SELECT count(*) as icount FROM ".$common->tname('aggressor_message')." WHERE playersid = '".$topid."'");
			$rows = $db->fetch_array($result);
			if($rows['icount'] > 0) {
				$update['create_time'] = time();			  			
				$updateWhere['playersid'] = $topid;
				$common->updatetable('aggressor_message',$update,$updateWhere);
			}else{
				$common->deletetable('aggressor_message',"`playersid` = '$topid'");
				$update['create_time'] = time();
				$common->inserttable('aggressor_message',$update);
			}
    	}
    	return $value;
    }
    
    //占领或者驻守收益$dj(玩家等级)$sylx（收益类型1驻守2占领）
    public static function zlsy($dj,$sylx = 1,$diff = 1) {
    	if ($diff > 7200) {
    		$diff = 7200;
    	}
    	if ($diff < 0) {
    		$diff = 0;
    	}	
    	if ($dj > 0 && $dj < 31) {
    		$rate = 0.1;
    	} elseif ($dj > 30 && $dj < 61) {
    		$rate = 0.2;
    	} elseif ($dj > 60 && $dj < 71) {
    		$rate = 0.3;
    	} else {
    		$rate = 0;
    	}
    	$income = $rate * $diff + 50;
    	return $income;
    }    
    
    // 请求闯关信息
    public static function qqcgxx($playersid) {
    	global $db,$common,$mc;

    	$returnValue = array();
    	$returnValue['status'] = 0;
    	if (!($stageInfo = $mc->get(MC.'stageInfo_'.$playersid))) {
		   	$stageInfo = $db->fetch_array($db->query("SELECT * FROM ".$common->tname('player_stage')." WHERE `playersid` = '$playersid'"));		   
		   	$mc->set(MC.'stageInfo_'.$playersid, $stageInfo, 0, 3600);
		}

		$roleInfo = array('playersid'=>$playersid);		
		roleModel::getRoleInfo($roleInfo);
						
		//$dqvipcs = 0;
		$totalCgTimes = 0;
		$totalCgTimes = $stageInfo['timesLimt'];
		
		if(intval($stageInfo['cgTimes']) >= 6)
		{
			if((intval($stageInfo['buyTimes'])) == 0) {
				$returnValue['t'] = 1;				
			} else if((intval($stageInfo['buyTimes'])) == 2 && (intval($totalCgTimes) + intval($stageInfo['addTimes'] - intval($stageInfo['cgTimes']))) == 0 ) {				
				$returnValue['t'] = 3; 
			} else {
				$returnValue['t'] = 2;				
			}
		} else {
			$returnValue['t'] = 1;			
		}
		
		// 已闯关/可闯关次数
		if(intval($stageInfo['buyTimes']) == 1 || intval($stageInfo['buyTimes'] == 2)){
			$returnValue['cgcs'] = 4 - (intval($totalCgTimes) + intval($stageInfo['addTimes']) - intval($stageInfo['cgTimes']));
			$returnValue['cgsx'] = 4;
		} else {
			$returnValue['cgcs'] = intval($stageInfo['cgTimes']);
			$returnValue['cgsx'] = intval($totalCgTimes) + intval($stageInfo['addTimes']);
		}
		
		if(intval($stageInfo['buyTimes']) == 1)
			$returnValue['btimes'] = 1;			
		else if(intval($stageInfo['buyTimes']) == 2)
			$returnValue['btimes'] = 2;
		else if(intval($stageInfo['buyTimes']) == 0)
			$returnValue['btimes'] = 0;
			
		// 已开启难度
		$returnValue['kqnd'] = intval($stageInfo['difficulty']);
		// 当前已开启的大关 备注：增加地狱难度时修改此处的$stageInfo['difficulty'] == 6
		if($stageInfo['difficulty'] == 6 && $stageInfo['unlock'] == 9) $stageInfo['unlock'] = 8;
		$returnValue['kqdgk'] = intval($stageInfo['unlock']);

		// 如果已进入大关卡闯关
		if($stageInfo['curr_stage'] != 0 && $stageInfo['curr_difficulty'] > 0) {
			// 当前正在打的信息
			$returnValue['nd'] = intval($stageInfo['curr_difficulty']);
			$returnValue['dqdgk'] = intval($stageInfo['curr_stage']);
			$returnValue['xgkjd'] = intval($stageInfo['curr_subStage'] + 1);
			
			// 可能掉落装备
			$dlzb = cgDlRule();
			$itemId = $dlzb[$stageInfo['curr_difficulty']][$stageInfo['curr_stage']][BOSS_STAGE_NO]['itemid'];
			$itemInfo = toolsModel::getItemInfo($itemId);
			$returnValue['zbmc'] = $itemInfo['Name'];
			//$returnValue['zbmc'] = $dlzb[$stageInfo['curr_difficulty']][$stageInfo['curr_stage']][BOSS_STAGE_NO]['name'];
			$returnValue['zbiid'] = $itemInfo['IconID'];
			$returnValue['pj'] = intval($itemInfo['Rarity']);
		}
		
		$rwzt = getDataAboutLevel::hqrwzt ( $roleInfo ['playersid'] );
		$returnValue ['rw'] = intval($rwzt['rw']);
		$returnValue['xrwsl'] = intval($rwzt['xrwsl']);
    	$rwid = questsController::OnFinish ( $roleInfo, "'kqcg'" );
		if (! empty ( $rwid )) {
			if (! empty ( $rwid )) {
				$returnValue ['rwid'] = $rwid;
			}
		}
		
		return $returnValue;
    }
    
    // 请求小关信息
    public static function qqxgxx($playersid) {
    	global $db,$common,$mc,$fight_lang,$sys_lang;
    	
    	$difficulty = _get('nd');
    	$stageNo = _get('dgbh');

    	// 备注：增加地狱难度时修改此处的$difficulty > 6
		if(intval($difficulty) < 0 || intval($difficulty) > 6 || intval($stageNo) < 0 || intval($stageNo) > 8 || $stageNo == null) {
			$returnValue = array('status'=>3, 'message'=>$sys_lang[7]);
			return $returnValue;    		
    	}   
    	
    	// 获取玩家信息
    	$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);

        // 获取玩家闯关进度
       	if (!($cgRecord = $mc->get(MC.'stageInfo_'.$playersid))) {
		   	$cgRecord = $db->fetch_array($db->query("SELECT * FROM ".$common->tname('player_stage')." WHERE `playersid` = '$playersid' limit 1"));
		   	$mc->set(MC.'stageInfo_'.$playersid, $cgRecord, 0, 3600);	
		}
        
    	// 闯关次数
    	$totalCgTimes = 0;
    	$totalCgTimes = $cgRecord['timesLimt'];
		
		// 闯关次数用完
		if(intval($cgRecord['cgTimes']) >= intval(($totalCgTimes + $cgRecord['addTimes']))) {
			$returnValue['status'] = 1001;
			$returnValue['message'] = $fight_lang['model_msg_51'];
			
			// 判断第几次增加闯关次数
			if(intval($cgRecord['buyTimes']) == 0) {
				$xyyb = 10;
			} else if(intval($cgRecord['buyTimes']) == 1) {
				$xyyb = 40;
			} else {
				$xyyb = 40;
			}
			$returnValue['xyyb'] = $xyyb;
				
			return $returnValue;
		}
   	
    	$returnValue = array();
    	$returnValue['status'] = 0;
		
		// 可能掉落装备
		$dlzb = cgDlRule();
		$itemId = $dlzb[$difficulty][$stageNo][BOSS_STAGE_NO]['itemid'];
		$itemInfo = toolsModel::getItemInfo($itemId);
		$returnValue['zbmc'] = $itemInfo['Name'];//$dlzb[$difficulty][$stageNo][BOSS_STAGE_NO]['name'];
		$returnValue['zbiid'] = $itemInfo['IconID'];
		$returnValue['pj'] = intval($itemInfo['Rarity']);
    	
    	if (!($stageInfo = $mc->get(MC.'stageInfo_'.$playersid))) {
		   	$stageInfo = $db->fetch_array($db->query("SELECT * FROM ".$common->tname('player_stage')." WHERE `playersid` = '$playersid'"));
		   	$mc->set(MC.'stageInfo_'.$playersid, $stageInfo, 0, 3600);	
		}  
		
		if(intval($stageInfo['buyTimes']) == 1)
			$returnValue['btimes'] = 1;			
		else if(intval($stageInfo['buyTimes']) == 2)
			$returnValue['btimes'] = 2;
		else if(intval($stageInfo['buyTimes']) == 0)
			$returnValue['btimes'] = 0;

		// 请求的即为当前进度
		$returnValue['nd'] = $updateStageInfo['curr_difficulty'] = intval($difficulty);
		$returnValue['dqdgk'] = $updateStageInfo['curr_stage'] = intval($stageNo);	
		// 闯关时更新小关进度		
		$returnValue['xgkjd'] = intval($stageInfo['curr_subStage'] + 1);
				
		// 更新玩家闯关进度表
    	$whereStage['playersid'] = $playersid;
		$common->updatetable('player_stage', $updateStageInfo, $whereStage); 
		$common->updateMemCache(MC.'stageInfo_'.$playersid, $updateStageInfo);
		return $returnValue;
    }
    
    // 请求小关怪物信息
    public static function qqxggwxx($playersid) {
    	global $db,$common,$mc,$sys_lang;
    	    
     	$difficulty = intval(_get('nd'));
    	$stageNo = intval(_get('dgbh'));
    	$subStageNo = intval(_get('xgbh'));   	
    	
    	// 备注：增加地狱难度时修改此处的$difficulty > 6
		if($difficulty < 0 || $difficulty > 6 || $stageNo < 0 || $stageNo > 8 || $subStageNo < 0 || $subStageNo > 4) {
			$returnValue = array('status'=>3, 'message'=>$sys_lang[7]);
			return $returnValue;    		
    	} 
    	  
    	$returnValue['status'] = 0;
    	
    	if (!($stageInfo = $mc->get(MC.'stageInfo_'.$playersid))) {
		   	$stageInfo = $db->fetch_array($db->query("SELECT * FROM ".$common->tname('player_stage')." WHERE `playersid` = '$playersid'"));
		   	$mc->set(MC.'stageInfo_'.$playersid, $stageInfo, 0, 3600);	
		}  
		
		$returnValue['nd'] = intval($difficulty);		
		//$returnValue['dqdgk'] = $stageInfo['curr_stage'];
		$returnValue['xgkjd'] = intval($subStageNo);			
		
		if (!($stage = $mc->get(MC.'stage'))) {
			$res = $db->query("SELECT * FROM ".$common->tname('stage'));
	   		while($stage[] = $db->fetch_array($res));
	   		array_pop($stage);
	   		$mc->set(MC.'stage', $stage, 0, 3600);	
		}  
		
		// 根据请求获取热点名
		$npcName = null;
		$npciid  = null;
		$npcdj   = null;
		$khjy    = null;
		foreach($stage as $k=>$v) {
			if($v['difficulty'] == $difficulty && $v['stage'] == $stageNo && $v['subStage'] == $subStageNo) {
				$npcName = $v['name'];	
				$npciid  = $v['iconId'];
				$npcdj   = $v['stageLevel'];
				$khjy    = $v['exp'];
				$dhnr1   = $v['dialog1'];
				$dhnr2   = $v['dialog2'];
				$dhsx    = $v['dialogSort'];
				$maxHP   = $v['maxHP'];
				break;		
			}
		}
		
		$returnValue['npcn'] = $npcName;
		//$returnValue['npciid'] = $npciid;
		$returnValue['npcdj'] = intval($npcdj);
				
		// 获取对话内容
		//$returnValue['dhnr1'] = $dhnr1;
		//$returnValue['dhnr2'] = $dhnr2;
		//$returnValue['dhsx'] = intval($dhsx); 
		$returnValue['hpmax'] = intval($maxHP)*10;
		
		// 关卡可获得经验待定
		$returnValue['khjy'] = intval($khjy);
		
		// 可能掉落装备
		$dlzb = cgDlRule();
		// 是Boss才显示
		if($subStageNo == 4) {
			$itemId = $dlzb[$difficulty][$stageNo][BOSS_STAGE_NO]['itemid'];
			$itemInfo = toolsModel::getItemInfo($itemId);
			//$returnValue['zbmc'] = $dlzb[$difficulty][$stageNo][BOSS_STAGE_NO]['name'];
			$returnValue['zbmc'] = $itemInfo['Name'];
			$returnValue['zbiid'] = $itemInfo['IconID'];
			$returnValue['pj'] = intval($itemInfo['Rarity']);
		}
		
		$returnValue['xyjl'] = 1;

		/*if ($_SESSION['player_level'] < 8) {
			$_SESSION['mz'] = 1;   //设置西门庆第一级闯关中命中为100%
		}*/
		
		return $returnValue;
    }
    
    // 请求小关怪物信息
    public static function qqnextxg($playersid, $difficulty, $stageNo, $subStageNo) {
    	global $db,$common,$mc;
    	
    	if (!($stageInfo = $mc->get(MC.'stageInfo_'.$playersid))) {
		   	$stageInfo = $db->fetch_array($db->query("SELECT * FROM ".$common->tname('player_stage')." WHERE `playersid` = '$playersid'"));
		   	$mc->set(MC.'stageInfo_'.$playersid, $stageInfo, 0, 3600);	
		}  
		
		if (!($stage = $mc->get(MC.'stage'))) {
			$res = $db->query("SELECT * FROM ".$common->tname('stage'));
	   		while($stage[] = $db->fetch_array($res));
	   		array_pop($stage);
	   		$mc->set(MC.'stage', $stage, 0, 3600);	
		}  
		
		// 获取热点名
		$npcName = null;
		$npciid  = null;
		$npcdj   = null;
		$khjy    = null;
		foreach($stage as $k=>$v) {
			if($v['difficulty'] == $difficulty && $v['stage'] == $stageNo && $v['subStage'] == $subStageNo) {
				$npcName = $v['name'];	
				$npciid  = $v['iconId'];
				$npcdj   = $v['stageLevel'];
				$khjy    = $v['exp'];
				break;		
			}
		}
		
		$returnValue['npcn'] = $npcName;
		$returnValue['npciid'] = $npciid;
		$returnValue['npcdj'] = intval($npcdj);	
		
		// 关卡可获得经验
		$returnValue['khjy'] = intval($khjy);
		
		$dlzb = cgDlRule();
		// 是Boss才显示
		if($subStageNo == 4) {
			$itemId = $dlzb[$difficulty][$stageNo][$subStageNo]['itemid'];
			$itemInfo = toolsModel::getItemInfo($itemId);
			//$returnValue['zbmc'] = $dlzb[$difficulty][$stageNo][$subStageNo]['name'];
			$returnValue['zbmc'] = $itemInfo['Name'];
			$returnValue['zbiid'] = $itemInfo['IconID'];
		}
		
		if($difficulty == 1) {		
			$returnValue['xyjl'] = $returnValue['xyyb'] = 1;
		} else if($difficulty == 2) {		
			$returnValue['xyjl'] = $returnValue['xyyb'] = 1;
		} else if($difficulty == 3) {		
			$returnValue['xyjl'] = $returnValue['xyyb'] = 3;
		}
		
		return $returnValue;
    }
    
    public static function getPf($attack_data, $roleInfo) {
      global $zbslots;
    	$levelScore = 0;
    	$tfScore = 0;
    	$zbScore = 0;
    	$jwScore = 0;
    	$jnScore = 0;
    	$jwjcpf = jwmc($roleInfo['mg_level']);
    	
    	for ($k = 0; $k < count($attack_data); $k++) {
    		$zbTotalAttr = array();
			foreach ($zbslots as $slot) {
			  $zbid = $attack_data[$k] [$slot];
			  if ($zbid != 0) {
				$zbInfo = toolsModel::getZbSx ( $attack_data[$k] ['playerid'], $zbid );
				$zbTotalAttr[] = $zbInfo['gj'] > 0 ? $zbInfo['gj'] : ($zbInfo['fy'] > 0 ? $zbInfo['fy'] : ($zbInfo['tl'] > 0 ? $zbInfo['tl'] : $zbInfo['mj']));
			  }
			}
    		// 计算子项评分
    		$levelScore += $attack_data[$k]['general_level'] * 20;
    		$zbScore += array_sum($zbTotalAttr);
    		$wjbronAttr = $attack_data[$k]['general_level'] * ($attack_data[$k]['understanding_value'] - $attack_data[$k]['llcs']);
    		$wjCombinAttr = $attack_data[$k]['general_level'] * ($attack_data[$k]['professional_level'] - 1) * 5;
    		$wjtfAttr = ceil($attack_data[$k]['general_level'] * ($attack_data[$k]['llcs'] * 0.5));
    		$jwScore += ceil((80 + $wjbronAttr + $wjCombinAttr + $wjtfAttr) * ($jwjcpf['jc'] / 100));
    	    	
			$pyAttr = $attack_data[$k]['py_gj'] + $attack_data[$k]['py_fy'] + $attack_data[$k]['py_tl'] + $attack_data[$k]['py_mj']; // 武将培养属性
    		$tftmpScore = 80 + $wjbronAttr + $wjCombinAttr + $wjtfAttr + $pyAttr - ($attack_data[$k]['general_level'] * 20);
    		$tftmpScore = $tftmpScore < 0 ? 0 : $tftmpScore;
    		$tfScore += $tftmpScore;
	 		$jnpfz = $attack_data[$k]['jn1_level'] + $attack_data[$k]['jn2_level'];
			if ($jnpfz == 0) {
			  	 $jnpfz = 1;
			}
			$jnScore += $jnpfz * 43;
			$jnpfz = null;   			
    		//$jnScore +=  ($attack_data[$k]['jn1_level'] + $attack_data[$k]['jn2_level']) * 43;    	
    		unset($zbTotalAttr);
    		$tftmpScore = 0;
    	}
    	
    	
    	$wjScore = ceil($levelScore + $tfScore + $zbScore + $jwScore + $jnScore);
    	
    	$returnValue['wjzdlpf'] = $wjScore;
    	$returnValue['djpf'] = $levelScore;
    	$returnValue['tfpf'] = $tfScore;
    	$returnValue['zbpf'] = $zbScore;
    	$returnValue['jwpf'] = $jwScore;
    	$returnValue['jnpf'] = $jnScore;
    	
    	return $returnValue;
    }
    
    // 闯关
    public static function cg($playersid) {
    	global $db,$common,$mc,$_SGLOBAL, $G_PlayerMgr, $zbslots, $sys_lang, $fight_lang;
    	$fast = _get('fast'); //是否快攻
    	if (empty($fast)) {
    		$returnValue['fst'] = 0;
    	} else {
    		$returnValue['fst'] = 1;
    	}
    	$nowTime = $_SGLOBAL['timestamp'];
    	
    	// 获取请求的难度和大关编号
    	$nd = intval(_get('nd'));
    	$dgbh = intval(_get('dgbh'));          	
		if(($nd <= 0 || $nd > 6) || ($dgbh <= 0 || $dgbh > 8) || $playersid == '') {
			return array('status'=>3, 'message'=>$sys_lang[7]);
		}

    	// 战斗所用消费类型
    	$type = intval(_get('type'));
     	if($type != 1 && $type != 2) {
			return array('status'=>3, 'message'=>$sys_lang[7]);
    	}
    	// 获取玩家信息
		$player = $G_PlayerMgr->GetPlayer($playersid);
		if(!$player)  return array('status'=>3, 'message'=>$sys_lang[7]);
      
		$returnValue = null;
		$roleInfo = $player->baseinfo_;
		$jwdj = $roleInfo['mg_level'];
		$jwInfo = jwmc($jwdj);
		$jwjc = 1 + $jwInfo['jc'] / 100;		
		
    	//$mc->delete(MC.'stageInfo_'.$playersid); //用完删除
    	// 获取玩家闯关进度
       	if (!($cgRecord = $mc->get(MC.'stageInfo_'.$playersid))) {
		   	$cgRecord = $db->fetch_array($db->query("SELECT * FROM ".$common->tname('player_stage')." WHERE `playersid` = '$playersid' limit 1"));
		   	$mc->set(MC.'stageInfo_'.$playersid, $cgRecord, 0, 3600);	
		}
			
		if($nd != $cgRecord['curr_difficulty'] || $dgbh != $cgRecord['curr_stage']) {
			$returnValue = array('status'=>3, 'message'=>$sys_lang[7]);
			return $returnValue;
		}
				
		$curr_date = time();
		// 判断闯关信息是否过期
		$year = date('Y', $curr_date);
		$month = date('m', $curr_date);
		$day = date('d', $curr_date);	
		$hour = intval(date('H', $curr_date));
		$minut = intval(date('i', $curr_date));
		$second = date('s', $curr_date);
		if($cgRecord['last_cg_date'] != 0) {			
			$players_year = date('Y', $cgRecord['last_cg_date']);
			$players_month = date('m',$cgRecord['last_cg_date']);
			$players_day = date('d', $cgRecord['last_cg_date']);		
			if(($players_year < $year || $players_month < $month) || ($players_year == $year && $players_month == $month && $players_day < $day)) {
				// 如果过期就重置
				$whereStage['playersid'] = $playersid;
				// 7级之前不删除闯关进度
				if($roleInfo['player_level'] >= 7) {
					$common->deletetable('player_boss', $whereStage);
				}
				$curr_date = time();
				$updateStageInfo['last_cg_date'] = $curr_date;
				// 7级之前不删除闯关进度
				if($roleInfo['player_level'] >= 7) {					
					$updateStageInfo['curr_difficulty'] = 0;
					$updateStageInfo['attackBossTimes'] = 0;
					$updateStageInfo['curr_stage'] = 0;
					$updateStageInfo['curr_subStage'] = 0;
				}				 
				$tmpCgTimes = $cgRecord['cgTimes'] < 6 ? 6 : $cgRecord['cgTimes'];				
				$updateStageInfo['addTimes'] = ($cgRecord['timesLimt'] + $cgRecord['addTimes']) - $tmpCgTimes;
				$updateStageInfo['buyTimes'] = 0;
				$updateStageInfo['cgTimes'] = 0;
				$common->updatetable('player_stage', $updateStageInfo, $whereStage);
				$mc->delete(MC.'stageInfo_'.$playersid);
				// 7级之前不删除闯关进度
				if($roleInfo['player_level'] >= 7) {
					$mc->delete(MC.$playersid.'_subStage');				
					return array('status'=>1003, 'message'=>$fight_lang['model_msg_52']);
				}
			} else { 
				// 否则更新上次闯关操作时间
				$updateStageInfo['last_cg_date'] = $curr_date;
				$whereStage['playersid'] = $playersid;
				$common->updatetable('player_stage', $updateStageInfo, $whereStage);
				$common->updateMemCache(MC.'stageInfo_'.$playersid, $updateStageInfo);
			}			
		}
       
    	// 闯关次数
		$totalCgTimes = 0;
		$totalCgTimes = $cgRecord['timesLimt'];
		
		// 闯关次数用完
		//$common->insertLog("{$cgRecord['cgTimes']} , $totalCgTimes, {$cgRecord['addTimes']}");
		if($cgRecord['cgTimes'] >= ($totalCgTimes + $cgRecord['addTimes']))
			return array('status'=>998, 'message'=>$fight_lang['model_msg_53']);	
		
		// 如果请求难度大于当前解锁的难度，请求的大关编号大于解锁的大关
		if($nd > $cgRecord['difficulty']) 
			return array('status'=>998, 'message'=>$fight_lang['model_msg_54']);	
		if($nd == $cgRecord['difficulty'] && $dgbh > $cgRecord['unlock']) 
			return array('status'=>998, 'message'=>$fight_lang['model_msg_55']);	
		
		//$mc->delete ( MC . $playersid . '_general' );
		// 玩家将领信息
		$general = cityModel::getGeneralData($playersid,'','*');
		if($general == false) {
			$returnValue['status'] = 21;
			$returnValue['message'] = $fight_lang['model_msg_56'];
			
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
			return array('status'=>998, 'message'=>$fight_lang['model_msg_57']);	;				
		
		$avalue = array();
		$j = 0;		
		for($i = 0; $i < count($general); $i++) {
			if($general[$i]['general_life'] > 0 && $general[$i]['f_status'] == 1) {
/*hk*/			if ($general[$i]['act'] == 1 || $general[$i]['gohomeTime'] > $nowTime || $general[$i]['act'] == 6) {
					continue;
				}/*hk*///闯关时剔除驻守状态的武将
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
			$returnValue['message'] = $fight_lang['model_msg_58'];		
			return $returnValue;
		}
		
		// 判断战斗是否可以跳过
		if(($cgRecord['difficulty'] == $cgRecord['curr_difficulty']) && ($cgRecord['unlock'] == $cgRecord['curr_stage'])){
			if(!($killdBoss = $mc->get(MC.$playersid.'_killBoss'))) {
				$killdBoss = 0;
			}
			if($killdBoss > 0) {
				$returnValue['tg'] = 1;
			} else {
				$returnValue['tg'] = 0;
			}
		} else if(($cgRecord['unlock'] > $cgRecord['curr_stage']) || ($cgRecord['difficulty'] > $cgRecord['curr_difficulty'])) {
			$returnValue['tg'] = 1;
		}
		
		// 总是可以跳过
		$returnValue['tg'] = 1;
		$xhyp = $xhjl = 1;
		
		$currFood = $currYb = 0;
    	if($type == 1) { // 用军粮
    		// 结算军粮
    		cityModel::resourceGrowth($roleInfo);
			// 更新军粮   
			$currFood = $roleInfo['food'];
			$syjl = $currFood - $xhjl;	
			if($syjl < 0){
				$returnValueErr['status'] = 888;
				$returnValueErr['jl'] = floor($currFood);
				$returnValueErr['cgxyyp'] = $xhyp;
				return $returnValueErr;
			}		 					
			$updateRole['food'] = $syjl;
			$updateRole['last_update_food'] = $nowTime;
			$returnValue['jl'] = floor($syjl);
    	} else { // 用元宝 
    		$currYp = $roleInfo['silver']; 
			$syyp = $currYp - $xhyp;	
			if($syyp < 0){
				$returnValueYp['status'] = 68;
				$returnValueYp['yp'] = intval($currYp);
				$returnValueYp['xyxhyp'] = intval($xhyp);
				//$returnValue['message'] = "您需要消耗{$xhyb}元宝，目前您有{$currYb}元宝，请先充值？";
				return $returnValueYp;
			}		 					
			$updateRole['silver'] = $syyp;	
			$returnValue['yp'] = intval($syyp);
    		$returnValue['xhyp'] = intval($xhyp);
    	}   
        // 关卡信息
       	if (!($stage = $mc->get(MC.'stage'))) {
			$res = $db->query("SELECT * FROM ".$common->tname('stage'));
	   		while($stage[] = $db->fetch_array($res));
	   		array_pop($stage);
	   		$mc->set(MC.'stage', $stage, 0, 3600);	
		} 
		
		// 根据请求的难度和大关编号获取对应关卡信息
		$npcName = $npcdj = $khjy = $npcid = $npcSex = $stageName = $dhnr1 = $dhnr2 = $dhsx = 0;
    	foreach($stage as $k=>$v) {
			if($v['difficulty'] == $nd && $v['stage'] == $dgbh && $v['subStage'] == $cgRecord['curr_subStage'] + 1) {
				$npcName = $v['name'];	
				$npcdj   = $v['stageLevel'];
				$khjy    = $v['exp'];
				$npcid   = $v['npcid'];
				$stageName = $v['stageName'];
				$npcSex  = $v['sex'];
				$dhnr1   = $v['dialog3'];
				$dhnr2   = $v['dialog4'];				
				$dhsx    = $v['dialogSort'];
				break;		
			}
		}
		
		if (!($wj_subStage = $mc->get(MC.$playersid.'_subStage'))) {
			$wj_subStage = array('pid'=>$playersid, 'attack_times'=>0);
	   		$mc->set(MC.$playersid.'_subStage', $wj_subStage, 0, 3600);	
		} 
		
		// 判断是否是打小关
		if($cgRecord['curr_subStage'] + 1 < 4) {
			if($cgRecord['attackBossTimes'] > 0) {
				// 清空打boss次数
				$updateStageInfo['attackBossTimes'] = 0;		
				$whereStage['playersid'] = $playersid;
				$common->updatetable('player_stage', $updateStageInfo, $whereStage); 
				$common->updateMemCache(MC.'stageInfo_'.$playersid, $updateStageInfo);
				// 删除boss数据
				if($wj_subStage['attack_times'] == 0) {
					$db->query("DELETE FROM ".$common->tname('player_boss')." WHERE playersid = '$playersid'");
				}
			}
		}
		
		// 获取关卡怪物信息
		// 如果是boss则从player_boss中获取数据，其他情况从monster中获取
		$monsters = null;
		if(($cgRecord['curr_subStage'] + 1 <= 4) && $cgRecord['attackBossTimes'] == 0 && $wj_subStage['attack_times'] == 0) {
			 // 普通关
			
			if (!($monsters = $mc->get(MC.'monsters_' . $npcid))) {
				$res = $db->query("SELECT * FROM ".$common->tname('monster_bak') . " WHERE npcid = '$npcid'");
				while($monsters[] = $db->fetch_array($res));
				array_pop($monsters);
				$mc->set(MC.'monsters_' . $npcid, $monsters, 0, 3600);
			}
		}
		
    	// 如果因意外状况未取到怪物数据，那么重新读取
		if(empty($monsters)) {			
			$result = $db->query("SELECT * FROM ".$common->tname('monster_bak')." WHERE npcid = '$npcid'");			
			while ($monsters[] = $db->fetch_array($result));
			array_pop($monsters);
		}
		
		// 打Boss
		if(($cgRecord['curr_subStage'] + 1 <= 4)) {
			if($cgRecord['attackBossTimes'] == 0 && $wj_subStage['attack_times'] == 0) { // 第一次插入信息到player_boss
				// 防止http请求异常产生的boss怪物重复的问题
				$bossCntRet = $db->query("SELECT count(*) as cnt FROM ".$common->tname('player_boss')." WHERE playersid = '$playersid'");
				$bossCnt = $db->fetch_array($bossCntRet);
				// 第一次打boss如果在player_boss表中已有数据为异常，需删除老数据
				if($bossCnt['cnt'] > 0) {
					$db->query("DELETE FROM ".$common->tname('player_boss')." WHERE playersid = '$playersid'");
				}
				
				foreach($monsters as $k=>$v) {
					$insertInfo = array(
					   'intID'=>null,
					   'playersid'=>$playersid,
					   'npcid'=>$v['npcid'],
					   'general_sort'=>$v['general_sort'],
					   'general_name'=>$v['general_name'],
					   'general_level'=>$v['general_level'],
					   'sex'=>$v['sex'],
					   'professional'=>$v['professional'],
					   'attack_value'=>$v['attack_value'],
					   'defense_value'=>$v['defense_value'],		
					   'Physical_value'=>$v['physical_value'],
					   'Agility_value'=>$v['agility_value'],
					   'understanding_value'=>$v['understanding_value'],
					   'professional_level'=>$v['professional_level'],
					   'mobility'=>$v['mobility'],
					   'bossLife'=>$v['physical_value']*10,
					   'avatar'=>$v['avatar']
					);
					$common->inserttable('player_boss', $insertInfo); 						  // 插入到player_boss表						
					unset($insertInfo);
				}
			} 
			
			$monsters_bak = $monsters;
			$monsters = null;
			$result = $db->query("SELECT * FROM ".$common->tname('player_boss')." WHERE npcid = '$npcid' and playersid = '$playersid' order by general_sort asc");
			while ($monsters[] = $db->fetch_array($result));
			if($monsters[0] == false) { // ol_player_boss表数据丢失时
				foreach($monsters_bak as $k=>$v) {
					$insertInfo = array(
					   'intID'=>null,
					   'playersid'=>$playersid,
					   'npcid'=>$v['npcid'],
					   'general_sort'=>$v['general_sort'],
					   'general_name'=>$v['general_name'],
					   'general_level'=>$v['general_level'],
					   'sex'=>$v['sex'],
					   'professional'=>$v['professional'],
					   'attack_value'=>$v['attack_value'],
					   'defense_value'=>$v['defense_value'],		
					   'Physical_value'=>$v['physical_value'],
					   'Agility_value'=>$v['agility_value'],
					   'understanding_value'=>$v['understanding_value'],
					   'professional_level'=>$v['professional_level'],
					   'mobility'=>$v['mobility'],
					   'bossLife'=>$v['physical_value']*10,
					   'avatar'=>$v['avatar']
					);
					$common->inserttable('player_boss', $insertInfo); 						  // 插入到player_boss表						
					unset($insertInfo);
					
					$monsters = null;
					$result = $db->query("SELECT * FROM ".$common->tname('player_boss')." WHERE npcid = '$npcid' and playersid = '$playersid' order by general_sort asc");
					while ($monsters[] = $db->fetch_array($result));
					array_pop($monsters);
				}
			} else {
				array_pop($monsters);
			}
			
			// 第一次之外打boss时，重排索引
			if($cgRecord['attackBossTimes'] > 0 || $wj_subStage['attack_times'] > 0) { 
				for($idx = 0; $idx < count($monsters); $idx++) {
					$monsters[$idx]['general_sort'] = $idx + 1;
				}
				//array_pop($monsters);
			}
			
		}
		
		// 构造战斗流程相关数据
		$returnValue['wjxm'] = stripcslashes($roleInfo['nickname']);
		$returnValue['gwxm'] = $npcName;
		$returnValue['a_level'] = intval($roleInfo['player_level']);
		$returnValue['a_sex'] = intval($roleInfo['sex']);
		$returnValue['d_level'] = intval($npcdj);
		$returnValue['d_sex'] = intval($npcSex);
		
    	// 开始战斗    	
    	/*if(($cgRecord['curr_subStage'] + 1) == 4) {
    		$fightResult = actModel::fight($attack_data, $monsters, 2); // Boss
    	} else {
    		$fightResult = actModel::fight($attack_data, $monsters, 1); // 普通
    	}*/
		
		// 如果怪物数据为空
		if(empty($monsters) || empty($attack_data)) {
			$returnValueData = array('status'=>3, 'message'=>'闯关数据错误');
			return $returnValueData;			
		}
		$gf_jwdj = $roleInfo['mg_level'];
		$fightResult = actModel::fight($attack_data, $monsters, 2, 0, 0, 0,$gf_jwdj);
    	
        $roleInfo['cg'] = 1;
		$roleInfo['boss'] = $npcid;
        $rwid = questsController::OnFinish($roleInfo,"'sbd'");
		if (!empty($rwid)) {
		     if (!empty($rwid)) {
		       	$returnValue['rwid'] = $rwid;
		     } 
		}
    	    	
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
		$returnValue['begin'] = $fightResult['begin'];                                               // 开始士兵情形		
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
    	/*if ($npcid == 40231 && $_SESSION['player_level'] == 2) {
			guideScript::jsydsj($roleInfo,'djms',2,1);
		}*/
		
		// 获取当前玩家实力评分信息
		$pfIndex = $nd . $dgbh . $cgRecord['curr_subStage'] + 1;
		$cgScores = cgpf($pfIndex);
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
			$returnValue['xhtq'] = $roleInfo['coins'] - $zdbx['tq'];
			$updateRole['coins'] = $zdbx['tq'];
			$returnValue['tq'] = $updateRole['coins'];
		}
    	// 更新玩家信息
    	$whereRole['playersid'] = $playersid;
    	/*$xwzt_10 = substr($roleInfo['xwzt'],9,1);  //完成 闯关行为
		if ($xwzt_10 == 0) {
			$updateRole['xwzt'] = substr_replace($roleInfo['xwzt'],'1',9,1);
			//$returnValue['xwzt'] = $updateRole['xwzt'];
		} */   	
		$common->updatetable('player', $updateRole, $whereRole); 
		$common->updateMemCache(MC.$playersid, $updateRole);		
		if ($attack_soldier_left <= 0 && $defend_soldier_left > 0) {
      		$success = 0;// 战斗失败		
		} else {
			$success = 1;// 战斗胜利		
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
		  $wjtfAttr = ceil($attack_data[$k]['general_level'] * $attack_data[$k]['llcs'] * 0.5);
		  $jwScore += ceil((80 + $wjbronAttr + $wjCombinAttr + $wjtfAttr) * ($jwjcpf['jc'] / 100));		
		  $pyAttr = $attack_data[$k]['py_gj'] + $attack_data[$k]['py_fy'] + $attack_data[$k]['py_tl'] + $attack_data[$k]['py_mj'];
		  
		  $tftmpScore = 80 + $wjbronAttr + $wjCombinAttr + $wjtfAttr + $pyAttr - ($attack_data[$k]['general_level'] * 20);
		  //echo $wjbronAttr.'+'.$wjCombinAttr.'+'.$wjtfAttr.'+'.$pyAttr.'FFFFF';
		  $tftmpScore = $tftmpScore < 0 ? 0 : $tftmpScore;
		  $tfScore += $tftmpScore;
		  $jnpfz = $attack_data[$k]['jn1_level'] + $attack_data[$k]['jn2_level'];
		  if ($jnpfz == 0) {
		  	 $jnpfz = 1;
		  }
		  $jnScore += $jnpfz * 43;
		  $jnpfz = null;
		  unset($zbTotalAttr);
		  $tftmpScore = 0;
		  
		  $sxxs = genModel::sxxs ( $attack_data[$k]['professional'] );
		  //$tl = round ( ((genModel::wjqx ( $attack_data[$k]['general_level'] ) * 0.4 * $sxxs ['tl']) + genModel::wjtf ( $attack_data [$k] ['understanding_value'], $attack_data[$k]['general_level'] ) * $sxxs ['tl']) * $jwjc + $zbtljc, 0 );  
		  $tl = genModel::hqwjsx($attack_data[$k]['general_level'],$attack_data[$k] ['understanding_value'],$attack_data[$k] ['professional_level'],$attack_data[$k] ['llcs'],$jwjc,$zbtljc,$sxxs ['tl'],$attack_data[$k] ['py_tl']);				 
		  $lshf_value[] = ceil(($tl * 10 - $left_command_soldier) * addLifeCost($roleInfo['player_level']));	
		  
		  $whereGeneral['intID'] = $id;
		  $common->updatetable('playergeneral',$updateGeneral,$whereGeneral);               //更新将领数据				  				
		  $newData[$attack_data[$k]['sortid']] = $attack_data[$k];    				
		  unset($updateGeneral);
		  unset($whereGeneral);
		 }
		 if(!empty($newData)) $common->updateMemCache(MC.$playersid.'_general',$newData);      
		
		$wjScore = ceil($levelScore + $tfScore + $zbScore + $jwScore + $jnScore);
		//$returnValue['gzp'] = '等级++'.$levelScore.'+天赋+'.$tfScore.'+装备+'.$zbScore.'+爵位+'.$jwScore.'+技能+'.$jnScore;
		$returnValue['gkpf'] = $cgScores['stageScore'];
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
		$returnValue['jnpfzb'] = $cgScores['jnScore'];
		
		$returnValue['lshf'] = array_sum($lshf_value);		
		//$returnValue['round'] = $fightResult['round'];                                               // 战斗回合
		$round = $fightResult['round'];                                               //战斗回合	
		$newround = heroCommon::arr_foreach($round);
		$returnValue['round'] = $fightResult['round']= $newround;
		$round = null;	
		$newround = null;				
		$returnValue['ginfo'] = array_values($value['ginfo']);
		unset($value);
 		// 战斗信息判断结束
    	
    	$updateStageInfo = null;
    	$currSubstage = 0;
    	if($success == 1) { // 战斗胜利或者完美击杀
    		$returnValue['status'] = $fightResult['status'] = 0;
    		$fightResult['wjxm'] = stripcslashes($roleInfo['nickname']);
			$fightResult['gwxm'] = $npcName;
			$fightResult['a_level'] = intval($roleInfo['player_level']);
			$fightResult['a_sex'] = intval($roleInfo['sex']);
			$fightResult['d_level'] = intval($npcdj);
			$fightResult['d_sex'] = intval($npcSex);
    		//$returnValue['jy'] =  intval($roleInfo['current_experience_value']) + intval($khjy);   
    		// 判断玩家是否升级
    		$upRet = fightModel::upgradeRole($roleInfo['player_level'], $khjy + intval($roleInfo['current_experience_value']), 1);
    		// 备注：等级上限变更70时修改此处
    		$returnValue['upgrade']  = 0;
    		if($roleInfo['player_level'] < 70) {
    			if($upRet['level'] > $roleInfo['player_level']) {
	    			$returnValue['upgrade']  = 1;
    			} else {
    				$returnValue['upgrade']  = 0;
    			}
	    		if($returnValue['upgrade'] == 1) { // 升级
	    			$returnValue['xjjy'] =  intval(cityModel::getPlayerUpgradeExp(intval($roleInfo['player_level']) + 1)); 
	    			$returnValue['jlsx'] =  foodUplimit($roleInfo['player_level']);
	    			$returnValue['level'] = $updateRole['player_level'] = intval($upRet['level']);	    			 
	    			$updateRole['current_experience_value'] = $upRet['left'];
					$updateRole['last_update_level'] = time();
	    			$roleInfo['player_level'] = intval($upRet['level']);
	    			$returnValue['lsdj'] = addLifeCost($roleInfo ['player_level']);
	    			$_SESSION['player_level'] = $roleInfo['player_level'];
/*hk*/	  /*5级打擂改为7级打擂*/  			// 添加到擂台
	    			if($roleInfo['player_level'] == 7) {
	    				roleModel::saveDaleiInfo($roleInfo);
	    			}/*hk*/
					// 每十级送vip+1三天
					/*if($roleInfo['player_level']%10 == 0){
						roleModel::rwVip($roleInfo);
					}*/
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
	    			// 获取日常任务
	    			if ($roleInfo['player_level'] == 8) {
	    				//roleModel::hqrcrw($roleInfo,false);
	    				getAward::sxrcrw($playersid,2);
	    			}
	    			// 升级补满军粮
	    			if ($roleInfo['food'] < $returnValue['jlsx']) {
		            	$updateRole['food'] = $returnValue['jlsx'];
						$updateRole['last_update_food'] = $nowTime;
		            	//$value['jl'] = floor($value['jlsx']);
		            	$roleInfo['food'] = $returnValue['jlsx'];
		            	$returnValue['jl'] = $returnValue['jlsx'];
		            }	 
		            $returnValue['tqzzl'] = roleModel::tqzzl($roleInfo['player_level']); //铜钱增长率
                    if ($roleInfo['player_level'] == 7 || $roleInfo['player_level'] == 2) {
                    	$zt = 2;
                    } else {
                    	$zt = 1;
                    }					
					//更新一日常任务
    	        	//获取任务状态信息
					if (! ($qStatus = $mc->get ( MC . $roleInfo['playersid'] . "_qstatus" ))) {
						$sql = "SELECT * FROM " . $common->tname ( 'quests_new_status' ) . " WHERE playersid = '".$roleInfo['playersid']."' LIMIT 1";
						$result = $db->query ( $sql );
						$rows = $db->fetch_array ( $result );
						$qStatus = $rows ['qstatusInfo'];
						$mc->set(MC.$playersid."_qstatus",$qStatus,0,3600);
					}	
					$qstatusInfo = array();
					$Qarray = array();
					$QarrayChose = array();
					if (! empty ( $qStatus )) {
						$qstatusInfo = unserialize ( $qStatus );
			            if (!empty($qstatusInfo)) {
							foreach ( $qstatusInfo as $key => $qValue ) {
								if (in_array ( $qValue, array (4, 5 ) ) && ! empty ( $key )) {
									$Qarray [] = $key;
								}
								if ($qValue == 6 && ! empty ( $key )) {
									$QarrayChose [] = $key;
								}								
							}
			            }
					}  
					$rcrwsl = count($Qarray);
					if ($rcrwsl < 3 && !empty($QarrayChose)) { 
						$canUseID = array();
						$easy = array(2002000,2003001,2003002,2004015,2005001,2004000,2004001,2004003,2004004,2001018,2004017,2004012,2001000,2003000);
						foreach ($QarrayChose as $QarrayChoseValue) {
							$rwinfo = ConfigLoader::GetQuestProto($QarrayChoseValue);
			        		$Level_Min = $rwinfo['Level_Min'];
			        		$Level_Max = $rwinfo['Level_Max'];							
							if ($returnValue['level'] >= $Level_Min && $returnValue['level'] <= $Level_Max && in_array($QarrayChoseValue,$easy)) {
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
			        			if ($returnValue['level'] >= $Level_Min && $returnValue['level'] <= $Level_Max) {
									$canUseID[] = $QarrayChoseValue;
								}	        		
								$rwinfo = null;
								$Level_Min = null;
								$Level_Max = null;
							}						
						}						 												
			        	if (!empty($canUseID)) {
			        		$checked = array_rand($canUseID,1);
			        		$QuestIDUpdate = $canUseID[$checked];
			        		$rwsql = "SELECT * FROM ".$common->tname('accepted_quests')." WHERE playersid = $playersid && QuestID = $QuestIDUpdate LIMIT 1";
			        		$rwsqlRows = $db->fetch_array($db->query($rwsql));	        		
			        		if (!empty($rwsqlRows)) {
				        		$db->query("UPDATE ".$common->tname('accepted_quests')." SET published = 1 WHERE playersid = $playersid && QuestID = '$QuestIDUpdate' LIMIT 1");
				        		if ($rwsqlRows['Qstatus'] == 1) {
				        			getDataAboutLevel::addCompleteQuestsStatus($playersid,$QuestIDUpdate,1);
				        		} else {
				        			getDataAboutLevel::addNewQuestsStatus($playersid,$QuestIDUpdate,5);
				        		}
	                            //$updateRole['rcrws'] = $roleInfo['rcrws'] + 1;
	        			        //$roleInfo['rcrws'] = $updateRole['rcrws'];	
			        		} else {
			        			$db->query('INSERT INTO '.$common->tname('accepted_quests')." SET QuestID = $QuestIDUpdate,playersid = $playersid, Qstatus = 0, Progress = 0 ,AcceptTime = '$nowTime', ExtraData = '', readStatus=0, published=1, RepeatInterval=1");
			        			getDataAboutLevel::addNewQuestsStatus($playersid,$QuestIDUpdate,5);
			                    //$updateRole['rcrws'] = $roleInfo['rcrws'] + 1;
			        			//$roleInfo['rcrws'] = $updateRole['rcrws'];	        			
			        		}		        		
			        	}
					}
						
					// 提升座次时计算每日战功的上下限					
					$curr_rank = $roleInfo['rank'];	
					$today_zglimit = getZgLimit($updateRole['player_level']);			
					$zglimit = needzg($curr_rank - 2); // 当前战功上限
					$zglimit = $zglimit - $roleInfo['ba'];
					if($today_zglimit[0] > $zglimit)  {
						$hdsx = $zglimit;
					} else {
						$hdsx = $today_zglimit[0];
					}
					if(date('Y-m-d') == date('Y-m-d', $roleInfo['curr_ba_date'])) {
						if($hdsx < $roleInfo['today_addba']) $hdsx += $roleInfo['today_addba'];
					}
					if($hdsx > $today_zglimit[0]) $hdsx = $today_zglimit[0];
					$updateRole['today_ba_uplimit'] = $hdsx;
						
					$zg_lowlimit = $curr_rank >= 109 ? 0 : needzg($curr_rank + 1); // 当前战功下限
					$zg_lowlimit = $roleInfo['ba'] - $zg_lowlimit;
					if(abs($today_zglimit[1]) > $zg_lowlimit) {
						$sxsx = $zg_lowlimit;
					} else {
						$sxsx = abs($today_zglimit[1]);
					}
					if(date('Y-m-d') == date('Y-m-d', $roleInfo['curr_ba_date'])) {
						if($sxsx < $roleInfo['today_reduceba']) $sxsx += $roleInfo['today_reduceba'];
					}
					if($sxsx > abs($today_zglimit[1])) $sxsx = abs($today_zglimit[1]);
					$updateRole['today_ba_downlimit'] = $sxsx;
					
					// 等级成就相关					
					//achievementsModel::check_achieve($playersid, 'level', null, array('level'=>$updateRole['player_level']));
	    		} else {
	    			$updateRole['current_experience_value'] = $roleInfo['current_experience_value'] + $khjy;
	    		}
	    		$returnValue['hqjy'] = intval($khjy);
	    		$returnValue['jy'] =  $updateRole['current_experience_value'];
    			$updateRoleWhere['playersid'] = $playersid;
				$common->updatetable('player',$updateRole,$updateRoleWhere);
				$common->updateMemCache(MC.$playersid,$updateRole);	
				
				// 任务相关(升级)
				if($returnValue['upgrade'] == 1) {
			        $hylist = roleModel::getTableRoleFriendsInfo($playersid,1,true);
        			$roleInfo['hysl'] = count($hylist);
					$acc_res = questsController::OnAccept($roleInfo,"'player_level'");
					$acc_res2 = questsController::OnFinish($roleInfo,"'player_level'");
		            if (!empty($acc_res) || !empty($acc_res2)) {	
		            	if (!empty($acc_res)) {
		            		$acc_val = $acc_res;
		            	} else {
		            		$acc_val = $acc_res2;
		            	}	       
		            	$returnValue['rwid'] = $acc_val;		
		            }
				}
    		}
        	$returnValue['cj'] = 1;
        	
        	// 发信获取信件ID
         	$json = array();
			$json['playersid'] = $playersid;
			$json['toplayersid'] = $playersid;
			$gkndstrs = array(1=>$fight_lang['model_msg_59'][1],2=>$fight_lang['model_msg_59'][2], 3=>$fight_lang['model_msg_59'][3],4=>$fight_lang['model_msg_59'][4],5=>$fight_lang['model_msg_59'][5],6=>$fight_lang['model_msg_59'][6]);
			$gkndstr = isset($gkndstrs[$nd])?$gkndstrs[$nd]:$fight_lang['model_msg_59'][6];

			$json['message'] = array('gkmc'=>$stageName,'gknd'=>$gkndstr,'bossmc'=>$npcName);
          	
        	// 击杀一次Boss闯关次数+1
        	// 完美击杀Boss才开启下关
        	// 计算当前进度
        	if(($cgRecord['curr_subStage'] + 1) < BOSS_STAGE_NO ) { 
				$common->deletetable('player_boss',"playersid = '$playersid'");
				
        		// 普通关，小关卡关数+1
        		$currSubstage = $cgRecord['curr_subStage'] + 1;
        		$updateStageInfo['curr_subStage'] = $currSubstage;        		

        		// 信件信息        		
				$json['genre'] = 26;
				unset($fightResult['result']);				
                if (empty($rwid)) {
					$rwid = questsController::OnFinish($roleInfo,"'dxg'");
				    if (!empty($rwid)) {
				         $returnValue['rwid'] = $rwid;				             
				    }
                }				
        	} else { // Boss战		        
				$json['genre'] = 1;
				unset($fightResult['result']);				
                $rwid = null;
                $wmjs = null;
                
        		// 更新打boss次数	          	
	          	$updateStageInfo['attackBossTimes'] = $cgRecord['attackBossTimes'] + 1;
	          	// 返回打boss次数
	          	$returnValue['cs'] = $updateStageInfo['attackBossTimes'];
	          	
        		// 完美击杀开启下一个大关 
				$add_unlock_Ret = false;
                /*$rwid = questsController::OnFinish($roleInfo,"'wmjscs'");
			    if (!empty($rwid)) {
			         $returnValue['rwid'] = $rwid;				             
			    }*/		
				$addItems_1 = array();		
        		if($updateStageInfo['attackBossTimes'] <= WM_KILL_COUNT ) { // 完美击杀
        			// 完美击杀Boss任务相关
	        		$rwid = questsController::OnFinish($roleInfo,"'cg','dboss','jsboss','wmjs','wmjscs'");
				    if (!empty($rwid)) {
				         $returnValue['rwid'] = $rwid;				             
				    }
        			$wmjs = 1;        	
					if ($npcid == 43241) {
						questsController::OnAccept ( $roleInfo, "'wmjs'" ); //接收烈焰模式任务
					}
        			$unlock_itemid = '';
        			// 判断请求的难度和大关编号是否等于当前解锁信息，相同才解锁下个难度和关卡
					if($cgRecord['unlock'] == 9) $cgRecord['unlock'] = 8;
        			if($nd == $cgRecord['difficulty'] && $dgbh == $cgRecord['unlock']) {		
        				// 重置已打boss信息
        				$mc->set(MC.$playersid.'_killBoss', 0, 0, 0);						
						$show_wmjs = $mc->get(MC.$playersid.'_killBoss_wmjs') ? false : true;
						$returnValue['ywm'] = $show_wmjs ? 0 : 1;
						
        				// 备注：增加地狱难度时修改此处的$nd
		        		if($nd <= 6 && $dgbh < 8) {		        			
		        			// 当前大、小关进度重置
			        		$updateStageInfo['curr_stage'] = 0;
			        		$updateStageInfo['curr_subStage'] = 0;
							if($show_wmjs) {
								// 返回关卡解锁对话
								$returnValue['dhnr1'] = '';//$dhnr1;
								$returnValue['dhnr2'] = '';//$dhnr2;
								$returnValue['dhsx'] = '';//$dhsx; 	      
							}
							$unlock_itemid = getUnlockItem($nd, $dgbh + 1);
		        		} else if($nd < 6 && $dgbh == 8) { // 开启下一难度  备注：增加地狱难度时修改此处的$nd		        					        			
		        			$updateStageInfo['curr_difficulty'] = 0;
		        			$updateStageInfo['curr_stage'] = 0;
			        		$updateStageInfo['curr_subStage'] = 0;
							if($show_wmjs) {
								// 返回关卡解锁对话
								$returnValue['dhnr1'] = '';//$dhnr1;
								$returnValue['dhnr2'] = '';//$dhnr2;
								$returnValue['dhsx'] = '';//$dhsx;
							}
							$unlock_itemid = getUnlockItem($nd + 1, 1);
		        		} else {
		        			// 备注：增加地狱难度时修改此处的$nd
		        			if($nd == 6 && $dgbh == 8) { // 如果是烈焰最后一关返回解锁大关为不存在的9，便于前台处理 
		        				$updateStageInfo['unlock'] = 9;
		        				$updateStageInfo['curr_stage'] = 0;
		        				$updateStageInfo['curr_subStage'] = 0;
								if($show_wmjs) {
									// 返回关卡解锁对话
									$returnValue['dhnr1'] = '';//$dhnr1;
									$returnValue['dhnr2'] = '';//$dhnr2;
									$returnValue['dhsx'] = '';//$dhsx;
								}
		        			} else {
		        				$updateStageInfo['curr_stage'] = 0;
			        			$updateStageInfo['curr_subStage'] = 0;
		        			}	
		        		}
						// 设置不会每次返回完美击杀
						$mc->set(MC.$playersid.'_killBoss_wmjs', 1, 0, 0);
        			} else { // 打当前解锁关之前的关卡
        				$updateStageInfo['curr_stage'] = 0;
			        	$updateStageInfo['curr_subStage'] = 0;	
        			}
					
					// 掉落解锁道具 最后难度的最后一关不需要掉落解锁道具
					if($unlock_itemid != '') {
						// 第一次完美击杀才获得，避免重复获得关卡解锁道具
						$curr_playerBag = $player->GetItems();
						$have_unlock_item = false;
						foreach($curr_playerBag as $bag_kye=>$bag_itemInfo) {
							if($unlock_itemid == $bag_itemInfo['ItemID']) { 
								$have_unlock_item = true;
								break; 
							}
						}						
						if(!$have_unlock_item && $show_wmjs) {
							$addItems_1 = array($unlock_itemid=>1);
							/*$add_unlock_Ret = $player->AddItems(array($unlock_itemid=>1)); // 放入背包
							if($add_unlock_Ret === false) { // 发系统探报
								$unlock_proto = toolsModel::getItemInfo($unlock_itemid);
								$unlock_item_name = $unlock_proto['Name'];
								
								$unlock_json = array();
								$unlock_json['playersid'] = $playersid;
								$unlock_json['toplayersid'] = $playersid;
								$unlock_json['message'] = array('xjnr'=>$fight_lang['model_msg_60']);
								$unlock_json['genre'] = 20;
								$unlock_json['request'] = json_encode(array('tq'=>0,'yp'=>0,'yb'=>0,'jl'=>0,'items'=>array(array('id'=>$unlock_itemid, 'mc'=>$unlock_item_name, 'num'=>1))));
								$unlock_json['type'] = 1;
								$unlock_json['uc'] = '0';
								$unlock_json['is_passive'] = 0;
								$unlock_json['interaction'] = 0;
								$unlock_json['tradeid'] = 0;							
								lettersModel::addMessage($unlock_json);
							}*/
						}
					}
			
        		} else {
         			if($nd == $cgRecord['difficulty'] && $dgbh == $cgRecord['unlock']) {
	    				// 非完美击杀不保留当前大、小关进度
	    				$updateStageInfo['curr_difficulty'] = $nd;	
	        			$updateStageInfo['curr_stage'] = 0;	        			
		        		$updateStageInfo['curr_subStage'] = 0;
						
		        		// 添加已打败boss记录到内存
		        		$mc->set(MC.$playersid.'_killBoss', 1, 0, 0);
        			} else { // 打当前解锁关之前的关卡
        				$updateStageInfo['curr_stage'] = 0;
			        	$updateStageInfo['curr_subStage'] = 0;	
        			}
        			$rwid = questsController::OnFinish($roleInfo,"'cg','dboss','jsboss'");
			        if (!empty($rwid)) {
			            $returnValue['rwid'] = $rwid;		            
			        }         			
        		}
        		// 完成一次闯关触发对应活动
        		hdProcess::run(array('fight_dailyCg', 'fight_countCg'), $roleInfo, 1);
        	    // 击杀Boss任务相关 
        	    /*if (empty($rwid)) {
        	    	if (empty($wmjs)) {
	        	   		$rwid = questsController::OnFinish($roleInfo,"'cg','dboss','jsboss'");
        	    	} else {
        	    		$rwid = questsController::OnFinish($roleInfo,"'dboss','jsboss'");
        	    	}
			        if (!empty($rwid)) {
			            $returnValue['rwid'] = $rwid;		            
			        }   
        	    }*/    		 
	        	// 从player_boss中删除相关信息					        	
				$common->deletetable('player_boss',"playersid = '$playersid'");
        		// 重置打Boss次数
	        	$updateStageInfo['attackBossTimes'] = 0;      		
         				
	        	// 根据不同难度奖励一个低(中、高)级武将卡碎片
	        	// 备注：增加地狱难度的必掉物品
	        	if($nd == 4 || $nd == 5 || $nd == 6) {
	        		$spInfo = toolsModel::getItemInfo(20008);
	        		$spID = 20008;
	        		$spName = $spInfo['Name'];
	        		$spIID = $spInfo['IconID'];
	        	} else if($nd == 3) {
	        		if($dgbh <= 4) {
	        			$spInfo = toolsModel::getItemInfo(20007);
	        			$spID = 20007;	
	        		} else {
	        			$spInfo = toolsModel::getItemInfo(20008);
	        			$spID = 20008;
	        		}       		
	        		$spName = $spInfo['Name'];
					$spIID = $spInfo['IconID'];
	        	} else if($nd == 1) {
	        		$spInfo = toolsModel::getItemInfo(20006);
	        		$spID = 20006;	        		
	        		$spName = $spInfo['Name'];
					$spIID = $spInfo['IconID'];	        		
	        	} else if($nd == 2) {
	        		if($dgbh <= 4) {
		        		$spInfo = toolsModel::getItemInfo(20006);
		        		$spID = 20006;	       
	        		} else {
	        			$spInfo = toolsModel::getItemInfo(20007);
	        			$spID = 20007;
	        		}    
	        		$spName = $spInfo['Name'];
	        		$spIID = $spInfo['IconID'];
	        	}
	        	       		        				
				// 加入玩家背包	
				$addItems = array($spID=>1) + $addItems_1;							
				$addRet = $player->AddItems($addItems);
				if($addRet === false) {
					$unlock_proto = toolsModel::getItemInfo($unlock_itemid);
					$unlock_item_name = $unlock_proto['Name'];
					
					$unlock_json = array();
					$unlock_json['playersid'] = $playersid;
					$unlock_json['toplayersid'] = $playersid;
					$unlock_json['message'] = array('xjnr'=>$fight_lang['model_msg_60']);
					$unlock_json['genre'] = 20;
					$unlock_json['request'] = json_encode(array('tq'=>0,'yp'=>0,'yb'=>0,'jl'=>0,'items'=>array(array('id'=>$unlock_itemid, 'mc'=>$unlock_item_name, 'num'=>1),array('id'=>$spID,'mc'=>$spName,'num'=>1))));
					$unlock_json['type'] = 1;
					$unlock_json['uc'] = '0';
					$unlock_json['is_passive'] = 0;
					$unlock_json['interaction'] = 0;
					$unlock_json['tradeid'] = 0;							
					lettersModel::addMessage($unlock_json);					
					//$returnValue['status'] = 1002;
					//$returnValue['message'] = $fight_lang['model_msg_61'];
				} 
				// 掉落道具的返回信息
				$returnValue['dlmc'] = $spName;					
				$returnValue['dlsl'] = 1;
				$returnValue['dliid'] = $spIID;				
				
				if($addRet !== false || $add_unlock_Ret !== false){ // 添加碎片或者解锁道具成功
					// 获取背包数据				
					$bagData = $player->GetClientBag(); // 背包列表返回协议优化
					$returnValue['list'] = $bagData; // 背包列表返回协议优化
				}	

				// 只有击杀第4小关Boss才更新闯关次数
				if($roleInfo['player_level'] >= 7) {
					$updateStageInfo['cgTimes'] = $cgRecord['cgTimes'] + 1;
				}
				
				// 闯关成就相关					
				//achievementsModel::check_achieve($playersid, 'cg', null, array('cg'=>intval($nd.$dgbh.$cgRecord['curr_subStage'] + 1)));
				$cjInfo['cg'] = intval($nd.$dgbh.$cgRecord['curr_subStage'] + 1); 
				if ($returnValue['upgrade'] == 1) {
					$cjInfo['level'] = $returnValue['level'];
					achievementsModel::check_achieve($playersid, $cjInfo, array('cg','level'));
				} else {
					achievementsModel::check_achieve($playersid, $cjInfo, array('cg'));
				}
        	}
        	if ($returnValue['upgrade'] == 1) {
        		$cjInfo['level'] = $returnValue['level'];
        		achievementsModel::check_achieve($playersid, $cjInfo, array('level'));
        	}
        	
        	// 清空打小怪次数
        	$wj_subStage = $mc->get(MC.$playersid.'_subStage');
    		$wj_subStage['attack_times'] = 0;
			$mc->set(MC.$playersid.'_subStage', $wj_subStage, 0, 3600);
        	
        	// 信件信息
       		$json['type'] = 1;
			$json['uc'] = '0';
			$json['is_passive'] = 0;
			$json['interaction'] = 0;
			$json['tradeid'] = 0;				
			
        	// 关卡抽奖
        	$cjInfo = toolsModel::buildCJInfo($playersid, $nd, $dgbh, $cgRecord['curr_subStage'] + 1);
			$json['request'] = $cjInfo;
			$mc->set(MC.$playersid.'_memCjInfo', $json, 0, 1800);        	
    	} else { // 战斗失败    	
    		$returnValue['status'] = 1001;
    		$returnValue['message'] = $fight_lang['model_msg_62'];
      		if(($cgRecord['curr_subStage'] + 1) <= BOSS_STAGE_NO) {
	          	// 更新打boss次数
	          	if(($cgRecord['curr_subStage'] + 1) == BOSS_STAGE_NO) {
	          		$updateStageInfo['attackBossTimes'] = $cgRecord['attackBossTimes'] + 1;
	          	}
				// 更新boss血到player_boss
          	    for ($m = 0; $m < count($monsters); $m++) {          	    	
					$id = $monsters[$m]['intID'];
					$current_command_soldier = $monsters[$m]['physical_value'];                    // 原有生命数量
					$left_command_soldier = $defendGeneralLeftSoldier[$id];                        // 剩余生命数量
					$whereGeneralDef['intID'] = $id;
					$whereGeneralDef['playersid'] = $playersid;			
					if ($left_command_soldier <= 0) {
						$common->deletetable('player_boss', $whereGeneralDef);				
					} else {						
						$updateGeneralDef['bossLife'] = $left_command_soldier;
				        $whereGeneralDef['intID'] = $id;
				        $whereGeneralDef['playersid'] = $playersid;		        
				        $common->updatetable('player_boss',$updateGeneralDef,$whereGeneralDef);    // 更新将领数据	
				        unset($updateGeneralDef);
				        unset($whereGeneralDef);
					}
			    }
			    
			    // 增加打小怪次数
			    $wj_subStage = $mc->get(MC.$playersid.'_subStage');
    			$wj_subStage['attack_times'] += 1;
				$mc->set(MC.$playersid.'_subStage', $wj_subStage, 0, 3600);
    		}   		
    	}
    	
    	// 更新玩家闯关进度表
    	if(!empty($updateStageInfo)) {
	    	$whereStage['playersid'] = $playersid;
			$common->updatetable('player_stage', $updateStageInfo, $whereStage); 
			$common->updateMemCache(MC.'stageInfo_'.$playersid, $updateStageInfo);
    	}
		
		unset($fightResult);
	
		//如果快攻清理以下两返回
		if (!empty($fast) && ($returnValue['status'] == 0 || $returnValue['status'] == 1001)) {
			unset($returnValue['round']);
			unset($returnValue['begin']);
		}
		
		return $returnValue;
    }
    
    // 退出闯关
    public static function tccg($playersid) {
    	global $db,$common,$mc,$fight_lang;	
    	
    	if (!($stageInfo = $mc->get(MC.'stageInfo_'.$playersid))) {
		   	$stageInfo = $db->fetch_array($db->query("SELECT * FROM ".$common->tname('player_stage')." WHERE `playersid` = '$playersid'"));
		   	$mc->set(MC.'stageInfo_'.$playersid, $stageInfo, 0, 3600);	
		}

    	$roleInfo = array('playersid'=>$playersid);		
		roleModel::getRoleInfo($roleInfo);
		
      	// 闯关次数
		$totalCgTimes = 0;
		$totalCgTimes = $stageInfo['timesLimt'];
				
		if(($stageInfo['curr_subStage'] + 1) <= 4) {
			// 从player_boss中删除相关信息
		    $db->query("DELETE FROM ".$common->tname('player_boss')." WHERE playersid = $playersid");
		} else {
			$bossInfo = $db->fetch_array($db->query("SELECT COUNT(*) as count FROM ".$common->tname('player_boss')." WHERE playersid = $playersid"));
			if($bossInfo['count'] > 0) {
				$db->query("DELETE FROM ".$common->tname('player_boss')." WHERE playersid = $playersid");
			}
		}
		
		// 清空打小怪次数
        $wj_subStage = $mc->get(MC.$playersid.'_subStage');
    	$wj_subStage['attack_times'] = 0;
		$mc->set(MC.$playersid.'_subStage', $wj_subStage, 0, 3600);
		
    	// 更新玩家闯关小关进度
    	$updateStageInfo['attackBossTimes'] = 0;
    	$updateStageInfo['curr_difficulty'] = 0;
    	$updateStageInfo['curr_stage'] = 0;
		$updateStageInfo['curr_subStage'] = 0;
		
		$curr_date = time();
		// 判断闯关信息是否过期
		$year = date('Y');
		$month = date('m');
		$day = date('d');
		if($stageInfo['last_cg_date'] != 0) {
			$players_year = date('Y', $stageInfo['last_cg_date']);
			$players_month = date('m',$stageInfo['last_cg_date']);
			$players_day = date('d', $stageInfo['last_cg_date']);
			if(($players_year < $year || $players_month < $month) || ($players_year == $year && $players_month == $month && $players_day < $day)) {
				// 如果过期就重置
				$whereStage['playersid'] = $playersid;
				$common->deletetable('player_boss', $whereStage);

				$curr_date = time();
				$updateStageInfo['last_cg_date'] = $curr_date;

				$updateStageInfo['curr_difficulty'] = 0;
				$updateStageInfo['attackBossTimes'] = 0;
				$updateStageInfo['curr_stage'] = 0;
				$updateStageInfo['curr_subStage'] = 0;

				$stageInfo['cgTimes'] = $stageInfo['cgTimes'] < 6 ? 6 : $stageInfo['cgTimes'];
				$updateStageInfo['addTimes'] = ($stageInfo['timesLimt'] + $stageInfo['addTimes']) - $stageInfo['cgTimes'];
				$stageInfo['buyTimes'] = 0;
				$updateStageInfo['buyTimes'] = 0;
				$updateStageInfo['cgTimes'] = 0;
				$common->updatetable('player_stage', $updateStageInfo, $whereStage);
				$mc->delete(MC.'stageInfo_'.$playersid);
		
				$mc->delete(MC.$playersid.'_subStage');
				$returnValue['message'] = $fight_lang['model_msg_52'];
				//return $returnValue;
				
				$returnValue['cs'] = intval($updateStageInfo['cgTimes']);
			
				// 可闯关次数
				$returnValue['cgsx'] = intval($totalCgTimes) + $stageInfo['addTimes'];
				
			} else {
				// 否则更新上次闯关操作时间				
				$updateStageInfo['last_cg_date'] = $curr_date;
				// 闯关次数加一
				if(($totalCgTimes + $stageInfo['addTimes']) >= ($stageInfo['cgTimes'] + 1)) {
					$updateStageInfo['cgTimes'] = $stageInfo['cgTimes'] + 1;
					$stageInfo['cgTimes'] = $updateStageInfo['cgTimes'];
					
					// 退出闯关也作为闯关完成条件之一
					hdProcess::run(array('fight_dailyCg', 'fight_countCg'), $roleInfo, 1);
				}
				$whereStage['playersid'] = $playersid;
				$common->updatetable('player_stage', $updateStageInfo, $whereStage);
				$common->updateMemCache(MC.'stageInfo_'.$playersid, $updateStageInfo);
			}
		}	
			
		if(intval($updateStageInfo['cgTimes']) >= 6)
		{
			if((intval($stageInfo['buyTimes'])) == 0) {
				$returnValue['t'] = 1;
			} else if(intval($stageInfo['buyTimes']) == 2 &&  (intval($totalCgTimes) + intval($stageInfo['addTimes'] - intval($stageInfo['cgTimes']))) == 0) {			
				$returnValue['t'] = 3;
			} else {
				$returnValue['t'] = 2;
			}
		} else {
			$returnValue['t'] = 1;
		}
		
		$returnValue['status'] = 0;
		
		$rwzt = getDataAboutLevel::hqrwzt ( $roleInfo ['playersid'] );
		$returnValue ['rw'] = intval($rwzt['rw']);
		$returnValue['xrwsl'] = intval($rwzt['xrwsl']);
		
		return $returnValue;
		
    }   
    
    // 直捣黄龙
	public static function zdhl($playersid) {
  		global $db,$common,$mc,$sys_lang,$fight_lang;			
  		
  		$difficulty = intval(_get('nd'));
    	$stageNo = intval(_get('xgbh')); // 大关编号
    	    	
    	// 备注：增加地狱难度时修改此处的$difficulty > 6
    	if($difficulty < 0 || $difficulty > 6 || $stageNo < 0 || $stageNo > 8) {
			$returnValue = array('status'=>3, 'message'=>$sys_lang[7]);
			return $returnValue;    		
    	} 
    	
		if (!($stageInfo = $mc->get(MC.'stageInfo_'.$playersid))) {
		   	$stageInfo = $db->fetch_array($db->query("SELECT * FROM ".$common->tname('player_stage')." WHERE `playersid` = '$playersid'"));
		   	$mc->set(MC.'stageInfo_'.$playersid, $stageInfo, 0, 3600);	
		}
		
		// 备注：增加地狱难度时修改此处的$difficulty > 6
		if(($difficulty != 3 && $difficulty != 4 && $difficulty != 5 && $difficulty != 6) || ($stageInfo['difficulty'] != 3 && $stageInfo['difficulty'] != 4 && $stageInfo['difficulty'] != 5 && $stageInfo['difficulty'] != 6) || ($stageInfo['curr_difficulty'] != 3 && $stageInfo['curr_difficulty'] != 4 && $stageInfo['curr_difficulty'] != 5 && $stageInfo['curr_difficulty'] != 6)) {
			$returnValue['status'] = 3;
			$returnValue['message'] = $sys_lang[7];
			return $returnValue;	
		}
		
		$bossStage = BOSS_STAGE_NO;
		// 已经是打 Boss，不可使用直捣黄龙
		if($stageInfo['curr_subStage'] == ($bossStage - 1)) {
			$returnValue['status'] = 998;
			$returnValue['message'] = $fight_lang['model_msg_63'];
			return $returnValue;
		}

 		$roleInfo = array('playersid'=>$playersid);		
		roleModel::getRoleInfo($roleInfo);				

		$currYB = $roleInfo['ingot'];
		$subtract = intval($currYB) - 20;
		
		if( intval($currYB) == 0 || $subtract < 0 ) {
			$rep_arr1 = array('{xhyb}', '{yb}');
			$rep_arr2 = array(20, $currYB);
			$msg = str_replace($rep_arr1, $rep_arr2, $sys_lang[2]);
			$returnValue = array('status'=>88, 'yb'=>$currYB, 'message'=>$msg); // 元宝不足
			return $returnValue;
		}
		
		$returnValue['status'] = 0;
		$returnValue['yb'] = $subtract; 
		$returnValue['xhyb'] = 20;
		
		// 更新玩家元宝
		$updateRole['ingot'] = $subtract;		
		$whereRole['playersid'] = $playersid;
		$common->updatetable('player', $updateRole, $whereRole); 
		$common->updateMemCache(MC.$playersid, $updateRole);
		
		// 修改进度
		// 直接修改进度为打Boss		
		$update['curr_subStage'] = $bossStage - 1;												
		$common->updatetable('player_stage', $update, "playersid = '$playersid'");
		$common->updateMemCache(MC.'stageInfo_'.$playersid, $update);	
		
		if(($stageInfo['curr_subStage'] + 1) <= 4) {
			// 从player_boss中删除相关信息
		    $db->query("DELETE FROM ".$common->tname('player_boss')." WHERE playersid = $playersid");
		}
		
		// 清空打小怪次数
        $wj_subStage = $mc->get(MC.$playersid.'_subStage');
    	$wj_subStage['attack_times'] = 0;
		$mc->set(MC.$playersid.'_subStage', $wj_subStage, 0, 3600);
		
		return $returnValue;
	}
	
	// 用银票江湖令增加闯关次数
	public static function zjcgcs($playersid, $nd, $dgbh) {
  		global $db,$common,$mc,$fight_lang,$sys_lang;		

  		$type = intval(_get('type'));

  		$returnValue = null;
  		
  		// 备注：增加地狱难度时修改此处的$difficulty > 3
  		if(intval($nd) > 6 || intval($nd) < 0 || intval($dgbh) > 8 || intval($dgbh) < 0 || intval($nd) == null || intval($dgbh) == null || $type != 1) {
  			$returnValue = array('status'=>3, 'message'=>$sys_lang[7]);
			return $returnValue; 			
  		}
  		
  		// 获取玩家闯关信息
		if (!($stageInfo = $mc->get(MC.'stageInfo_'.$playersid))) {
		   	$stageInfo = $db->fetch_array($db->query("SELECT * FROM ".$common->tname('player_stage')." WHERE `playersid` = '$playersid'"));
		   	$mc->set(MC.'stageInfo_'.$playersid, $stageInfo, 0, 3600);	
		}
		
	    $roleInfo = array('playersid'=>$playersid);		
		roleModel::getRoleInfo($roleInfo);
		
      	// 闯关次数
		$totalCgTimes = 0;
		/* if($roleInfo['vip'] == 1) {
			$totalCgTimes = $stageInfo['timesLimt'] + 1;
		} else if($roleInfo['vip'] == 2) {
			$totalCgTimes = $stageInfo['timesLimt'] + 2;
		} else if($roleInfo['vip'] == 3) {
			$totalCgTimes = $stageInfo['timesLimt'] + 3;
		} else if($roleInfo['vip'] == 4) {
			$totalCgTimes = $stageInfo['timesLimt'] + 4;
		} else {
			$totalCgTimes = $stageInfo['timesLimt'];
		} */
		$totalCgTimes = $stageInfo['timesLimt'];
		
		// 判断免费次数是否已用完
		if(intval($stageInfo['cgTimes']) < intval($totalCgTimes)) {
			$returnValue['status'] = 998;
			$returnValue['message'] = $fight_lang['model_msg_64'];
			return $returnValue;
		}
		
		// 备注：增加地狱难度时需在game_use表中增加对应的新难度和关卡增加闯关次数所需消耗数值
		$xyyp = 0;

		// 判断第几次增加闯关次数，每天只能购买2次闯关
		if(intval($stageInfo['buyTimes']) >= 2) {
			$returnValue['status'] = 998;
			$returnValue['message'] = $fight_lang['model_msg_65'];
			return $returnValue;
		}
		
		$giftFood = 0;
		if(intval($stageInfo['buyTimes']) == 0) {	
			$xyyb = 10; 
			$giftFood = 50;
		} else if(intval($stageInfo['buyTimes']) == 1) {
			$xyyb = 40;
			$giftFood = 100;
		}
	
		$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);
		$currYb = $roleInfo['ingot'];
		$syyb = $currYb - $xyyb;
		if($syyb < 0){
			$returnValueErr['status'] = 88;
			$returnValueErr['xyxhyb'] = $xyyb;
			$rep_arr1 = array('{xhyb}', '{yb}');
			$rep_arr2 = array($xyyb, $currYb);			
			$returnValueErr['message'] = str_replace($rep_arr1, $rep_arr2, $sys_lang[2]);;
			return $returnValueErr;
		} else {
			// 赠送军粮 第一次购买送50 第二次送100
			// 结算军粮
			cityModel::resourceGrowth($roleInfo);
			// 更新军粮
			$currFood = $roleInfo['food'];
			$syjl = $currFood + $giftFood;
			
			$updateRole['food'] = $syjl;
			$updateRole['last_update_food'] = time();
			$updateRole['ingot'] = $syyb;
			
			$returnValue['xhyb'] = intval($xyyb);
			$returnValue['yb'] = intval($syyb);
			$returnValue['jl'] = floor($syjl);
			$returnValue['zjjl'] = $giftFood;
			
			// 更新玩家信息
			$whereRole['playersid'] = $playersid;
			$common->updatetable('player', $updateRole, $whereRole);
			$common->updateMemCache(MC.$playersid, $updateRole);
		}

		// 增加闯关次数		
		$stageInfo['curr_difficulty'] = $nd;
		$stageInfo['curr_stage'] = $dgbh;
		$stageInfo['addTimes'] = $stageInfo['addTimes'] + 4;			
		$update['buyTimes'] = $stageInfo['buyTimes'] + 1;
		$update['addTimes'] = $stageInfo['addTimes'];												
		$common->updatetable('player_stage', $update, "playersid = '$playersid'");
		$common->updateMemCache(MC.'stageInfo_'.$playersid, $update);	
		
		$returnValue['status'] = 0;	
		// 闯关上限 timesLimit + addTimes(增加的闯关次数)
		// 可闯关次数
		if(intval($update['buyTimes']) == 1 || intval($update['buyTimes'] == 2)){
			$returnValue['cgsx'] = 4;
		} else {
			$returnValue['cgsx'] = intval($totalCgTimes) + intval($stageInfo['addTimes']);
		}
		$rwid = questsController::OnFinish($roleInfo,"'zjcgcs'");  //玩家留言任务
	    if (!empty($rwid)) {
	         $value['rwid'] = $rwid;				             
	    }		
		return $returnValue;
	}
	
	//获取玩家领地数量
	public static function ldsl($playersid,&$zllist = array()) {
		global $mc, $db, $common, $_SGLOBAL;
		$nowTime = $_SGLOBAL['timestamp'];		
		/*$zllist = array();
	    if (!($ldslInfo = $mc->get(MC.'ld_'.$playersid))) {
	    	$ldslInfo = array();
	        $ldInfoRes = $db->query("SELECT playersid,end_defend_time FROM ".$common->tname('player')." WHERE `aggressor_playersid` = '$playersid' && `playersid` != `aggressor_playersid` && end_defend_time > '$nowTime' LIMIT 12");
	    	while ($rows = $db->fetch_array($ldInfoRes)) {
	    		$ldslInfo[$rows['playersid']] = $rows['end_defend_time'];
	    	}
	    	if (!empty($ldslInfo)) {
	    		$mc->set(MC.'ld_'.$playersid,$ldslInfo,0,7200);
	    	}
    	}
    	if (!empty($ldslInfo)) {
    		$num = array();
    		foreach ($ldslInfo as $key => $value) {
    			if ($value > $nowTime) {
    				$num[] = 1;
    				$zllist[] = $key;
    			} else {
    				unset($ldslInfo[$key]);
    			}
    		}
    		$ldsl = count($num);
    	} else {
    		$ldsl = 0;
    	}
    	return $ldsl;*/
	  	$zllist = array();
  		$ginfo = cityModel::getGeneralData($playersid,false,'*');
  		if (empty($ginfo)) {
  			return $zllist;
  		}
  		foreach ($ginfo as $ginfoValue) {
  			if ($ginfoValue['occupied_playersid'] != 0 && $ginfoValue['occupied_playersid'] != $playersid && $ginfoValue['occupied_end_time'] > time()) {
  				$zllist[] = $ginfoValue['occupied_playersid'];
  			}
  		}
  		return $zllist;
	}
	
	//增加领地数量
	public static function addLdsl($playersid,$tuid,$endTime) {
		global $mc;
		$ldslInfo = $mc->get(MC.'ld_'.$playersid);
		if (!empty($ldslInfo)) {	
			$save = $ldslInfo + array($tuid=>$endTime);
			$mc->set(MC.'ld_'.$playersid,$save,0,86400); 
		} else {
			$mc->set(MC.'ld_'.$playersid,array($tuid=>$endTime),0,86400);
		}
	}
	
	//减少领地数量
	public static function reduceLdsl($playersid,$tuid) {
		global $mc;
		$ldslInfo = $mc->get(MC.'ld_'.$playersid);
		if (!empty($ldslInfo)) {
			if (!empty($ldslInfo[$tuid])) {
				unset($ldslInfo[$tuid]);	
				$mc->set(MC.'ld_'.$playersid,$ldslInfo,0,86400); 
			}				
		}	
	}
	
	//战斗完毕后自动补血
	public static function zdbx($generalInfo,$playersid,$zhwjsminfo) {
		global $G_PlayerMgr, $common;
        $player = $G_PlayerMgr->GetPlayer($playersid);		
        $jwdj = $player->baseinfo_['mg_level'];
		$jwInfo = jwmc($jwdj);
		$jwjc = 1 + $jwInfo['jc'] / 100;		
		$coins = $player->baseinfo_ ['coins'];        
        static $zbslots = array('helmet','carapace','arms','shoes');
		for($i = 0; $i < count ( $generalInfo ); $i ++) {
			$currenLife = $zhwjsminfo[$generalInfo [$i] ['intID']];
			$sxxs = genModel::sxxs ( $generalInfo [$i] ['professional'] );
			$dj = $generalInfo [$i] ['general_level'];
			$tfz = $generalInfo [$i] ['understanding_value'];
			$zbtljcArray = array ();
	        foreach ($zbslots as $slot) {
	          $zbid = $generalInfo [$i] [$slot];
	          if ($zbid != 0) {
	            $zbInfo = $player->GetZBSX($zbid );
	            $zbtljcArray [] = $zbInfo ['tl'];
	          }
	        }
	        $zbtljc = array_sum ( $zbtljcArray );

			$jj = $generalInfo [$i] ['professional_level'];
			$tlz = genModel::hqwjsx($dj,$tfz,$jj,$generalInfo [$i] ['llcs'],$jwjc,$zbtljc,$sxxs ['tl'],$generalInfo [$i] ['py_tl']);	
			$jj = null;						
			unset ( $sxxs );
			$lifeLimitUpValue = round ( $tlz, 0 ) * 10;
			$needLife = $lifeLimitUpValue - $currenLife; //需要的血量 
			if ($needLife < 0) {
				$needLife = 0;
			} 	  
			$lifePrice = addLifeCost($player->baseinfo_['player_level']);
			$cost = 0;
			$cost = $needLife * $lifePrice;
			if ($coins <= 0 && $i == 0) {
				$bxInfo [$generalInfo [$i] ['intID']] = 0;
			} elseif ($coins <= 0 && $i > 0) {
				$bxInfo [$generalInfo [$i] ['intID']] = 0;
			} else {
				$updateGeneralWhere ['intID'] = $generalInfo [$i] ['intID'];
				if ($coins >= $cost) {
					$updateGeneral ['general_life'] = $lifeLimitUpValue;
				} else {
					$updateGeneral ['general_life'] = floor ( $coins / $lifePrice );
					$coins = 0;
				}
				//$common->updatetable ( 'playergeneral', $updateGeneral, $updateGeneralWhere );
				$generalInfo [$i] ['general_life'] = $updateGeneral ['general_life'];
				$generalInfo [$i] ['command_soldier'] = $updateGeneral ['general_life'];
				//$newData [$generalInfo [$i] ['sortid']] = $generalInfo [$i];
				//$common->updateMemCache ( MC . $playersid . '_general', $newData );
				$bxInfo [$generalInfo [$i] ['intID']] = $updateGeneral ['general_life'];
				unset ( $updateGeneralWhere );
				unset ( $updateGeneral );
				if ($coins < $cost) {
					//break;
				}
			}
			$coins = $coins - $cost;
		}
		$value = array();
		if (! empty ( $bxInfo )) {
			$value ['ginfo'] = $bxInfo;
		} else {
			$value ['ginfo'] = '';
		}
		if ($coins != $player->baseinfo_ ['coins'] || $coins == 0) {
			if ($coins < 0) {
				$coins = 0;
			}
			$value['tq'] = floor($coins);
		} else {
			$value['tq'] = floor($coins);
		}
		return $value;					
	}	
}