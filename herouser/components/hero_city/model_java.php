<?php 
class cityModelJava {
public static function getCityBaseInfo($getInfo, $showState = 1) {
		global $db, $common, $mc, $_SGLOBAL;
		$nowTime = $_SGLOBAL['timestamp']; 
		$roleInfo ['playersid'] = $getInfo ['playersid'];
		$more = _get ( 'more' );
		$result = roleModel::getRoleInfo ( $roleInfo );
		//cityModel::resourceGrowth($roleInfo,false);
		if (! empty ( $result )) {
			if ($showState == 1) {
				$value ['status'] = 0; //返回数据是否查询成功状态,1为成功
			}
			if ($roleInfo['is_reason'] == 1) {
				$value = array('status'=>21,'message'=>'此帐号已被暂时冻结');
				return $value;
			}
			/*设置回防*/
			if ($roleInfo['aggressor_playersid'] != $roleInfo['playersid'] && $roleInfo['aggressor_playersid'] != 0 && $roleInfo['end_defend_time'] < $nowTime) {
			    $bzlGinfo = cityModel::getGeneralData($roleInfo['playersid'],false,'*');	    
			    $gen = 0;
			    if ($roleInfo['zf_aggressor_general'] != 0) {		
			    	if (!empty($bzlGinfo)) {
			    		foreach ($bzlGinfo as $bzlGinfoValue) {
			    			if ($bzlGinfoValue['intID'] == $roleInfo['zf_aggressor_general']) {
					  			$updateRole['aggressor_playersid'] = $roleInfo['playersid'];
					  			$updateRole['aggressor_nickname'] = mysql_escape_string($roleInfo['nickname']);
					  			$updateRole['aggressor_level'] = $bzlGinfoValue['general_level']; 	  			 
								$updateRole['is_defend'] = 1;				
							    $updateRole['end_defend_time'] = $nowTime;		
							    $updateRole['zf_aggressor_general']	= $bzlGinfoValue['intID'];
							    $gen = 1;
								$bzlGinfoValue['occupied_playersid'] = $bzlupdateGen['occupied_playersid'] = $roleInfo['playersid'];
								$bzlGinfoValue['occupied_player_level'] = $bzlupdateGen['occupied_player_level'] = $roleInfo['player_level'];
								$bzlGinfoValue['occupied_player_nickname'] = $bzlupdateGen['occupied_player_nickname'] = mysql_escape_string($roleInfo['nickname']);					 		 	
								$bzlGinfoValue['last_income_time'] = $bzlupdateGen['last_income_time'] = $nowTime; 
								$bzlGinfoValue['occupied_end_time'] = $bzlupdateGen['occupied_end_time'] = $nowTime;	
								$gzlnewdData[$bzlGinfoValue['sortid']] = $bzlGinfoValue;                 
							    $common->updateMemCache(MC.$roleInfo['playersid'].'_general',$gzlnewdData);	
								$bzlwhere['intID'] = $bzlGinfoValue['intID'];
								$common->updatetable('playergeneral',$bzlupdateGen,$bzlwhere);  	
							    break;	    				
			    			}			    			
			    		}
			    	}	
			    	if ($gen == 1) {
	    		    	$updateRole['strategy'] = $roleInfo['zf_strategy'];
			    		$updateRole['aggressor_general'] = $roleInfo['zf_aggressor_general'];		
			    	} else {
	    		    	$updateRole['strategy'] = 0;
			    		$updateRole['aggressor_general'] = 0;					    		
			    	}		    	    	
			    } else {
			    	$updateRole['strategy'] = 0;
			    	//$updateBzl['aggressor_general'] = 0;			    	
			    	if (!empty($bzlGinfo)) {
			    		foreach ($bzlGinfo as $bzlGinfoValue) {
			    			if ($bzlGinfoValue['occupied_playersid'] == 0 || ($bzlGinfoValue['occupied_playersid'] != 0 && $bzlGinfoValue['occupied_playersid'] != $roleInfo['playersid'] && $bzlGinfoValue['occupied_end_time'] < $nowTime)) {
					  			$updateRole['aggressor_playersid'] = $roleInfo['playersid'];
					  			$updateRole['aggressor_nickname'] = mysql_escape_string($roleInfo['nickname']);
					  			$updateRole['aggressor_level'] = $bzlGinfoValue['general_level']; 	  			 
								$updateRole['is_defend'] = 1;				
							    $updateRole['end_defend_time'] = $nowTime;		
							    $updateRole['zf_aggressor_general']	= $bzlGinfoValue['intID'];
							    $updateRole['aggressor_general'] = $bzlGinfoValue['intID'];
							    $gen = 1;
								$bzlGinfoValue['occupied_playersid'] = $bzlupdateGen['occupied_playersid'] = $roleInfo['playersid'];
								$bzlGinfoValue['occupied_player_level'] = $bzlupdateGen['occupied_player_level'] = $roleInfo['player_level'];
								$bzlGinfoValue['occupied_player_nickname'] = $bzlupdateGen['occupied_player_nickname'] = mysql_escape_string($roleInfo['nickname']);					 		 	
								$bzlGinfoValue['last_income_time'] = $bzlupdateGen['last_income_time'] = $nowTime; 
								$bzlGinfoValue['occupied_end_time'] = $bzlupdateGen['occupied_end_time'] = $nowTime;	
								$gzlnewdData[$bzlGinfoValue['sortid']] = $bzlGinfoValue;                 
							    $common->updateMemCache(MC.$roleInfo['playersid'].'_general',$gzlnewdData);	
								$bzlwhere['intID'] = $bzlGinfoValue['intID'];
								$common->updatetable('playergeneral',$bzlupdateGen,$bzlwhere);  	
							    break;	    				
			    			}			    			
			    		}
			    	}
			    	if ($gen == 0) {
			    		$updateRole['aggressor_general'] = 0;
			    	}
			    }		  			
				$updateBzlWhere['playersid'] = $roleInfo ['playersid'];
				$common->updatetable('player',$updateRole,$updateBzlWhere);
				$common->updateMemCache(MC.$roleInfo ['playersid'],$updateRole);	
			}		
			/*设置回防结束*/							
			$zfInfo = cityModel::getZfInfo ( $roleInfo ['playersid'] ); //驻防信息
			if ($zfInfo ['status'] == 0) {
				$value ['oct'] = 0;
				$value ['gid'] = $zfInfo ['gid'];
				$value ['giid'] = $zfInfo ['giid']; //占领自己城池的将领ICON id
				$value ['gname'] = $zfInfo ['gname']; //占领自己城池的将领名称
				$value ['glevel'] = $zfInfo ['glevel'];
				$value ['gxyd'] = $zfInfo ['gxyd'];
				$value ['time'] = $zfInfo ['time'];
				$value['dzljtq'] = $zfInfo['sy'];
				//$value['mfzzz'] = $zfInfo['cl'];
				$value['zscl'] = $zfInfo['zscl'];  //驻守策略
			} elseif ($zfInfo ['status'] == 1001) {
				$value ['oct'] = 1;
				$value ['tk'] = $zfInfo ['tk'];
				$value ['uid'] = $zfInfo ['uid'];
				$value ['gid'] = $zfInfo ['gid'];
				$value ['giid'] = $zfInfo ['giid']; //占领自己城池的将领ICON id
				$value ['gname'] = $zfInfo ['gname']; //占领自己城池的将领名称
				$value ['glevel'] = $zfInfo ['glevel'];
				$value ['gxyd'] = $zfInfo ['gxyd'];
				$value ['un'] = $zfInfo ['un'];
				$value ['ul'] = $zfInfo ['ul'];
				$value ['time'] = $zfInfo ['time'];
				$value['dzljtq'] = $zfInfo['sy'];
				//$value['mfzzz'] = $zfInfo['cl'];
				if (! empty ( $zfInfo ['zlxy'] )) {
					$value ['zlxy'] = $zfInfo ['zlxy'];
				}
			} else {
				$value ['oct'] = 2;
			}
			$value['gbnr'] = lettersModel::getPublicNotice();		//广播内容(有则反)(暂无)
			//$showValue = lettersModel::publicParameters($roleInfo['playersid']);
			//$value['xrzsl'] = $showValue['sl'];       //新日志数量，没有新日志则返0(暂无)
			$value['tq'] = $roleInfo['coins'];
			$value['jzzt'] = cityModel::jzzt($roleInfo ['playersid']);
			
			$wk_sz = wksjxz();
			$wk_sy_cd = ($roleInfo['last_wk_time'] + intval($wk_sz[0])) - $_SGLOBAL['timestamp'];
			if($wk_sy_cd > 0) {
				$value['kscd'] = $wk_sy_cd < 0 ? 0 : $wk_sy_cd;        //矿山cd剩余时间
			}
			// 获取开矿次数
			$value['ykccs'] = cityModel::checkMineTimes($roleInfo);
			$value['kczcs'] = 10;
			$collectNeedTime = collectNeedTime ();  //间隔时间
			$timeDiff = $_SGLOBAL ['timestamp'] - $roleInfo['last_collect_time']; //与上次征税时间间隔
			$sc_sy_cd = $collectNeedTime - $timeDiff; //还剩时间
			$value['sccd'] = $sc_sy_cd > 0 ? $sc_sy_cd : 0;        //市场cd剩余时间  
			
			$player_level = $roleInfo ['player_level'];
			if (! empty ( $more )) {
				//$value ['kck'] = cityModel::is_Excavation ($roleInfo['last_wk_time'],$roleInfo['wk_count']);   //能否挖矿 1可以 0 不可以
				//$value ['kzs'] = cityModel::is_Collection ($roleInfo['last_collect_time']);   //可征收      1可以 0 不可以
				/*$ksj = cityModel::is_BuildUpgrade ($roleInfo); //是否有建筑可升级
				if($ksj <> '') {
					$value ['ksj'] = $ksj;
				}*/
				
				$value ['binfo'] =  cityModel::getBuildInfo ($roleInfo);
				//$value ['jlsx'] = foodUplimit($roleInfo['player_level']);
			}
			$lysl = cityModel::hqlysl($roleInfo['playersid']);
			$value['xlysl'] = intval($lysl);    //留言数量
			/*获得强征信息*/
			$collectNeedTime = collectNeedTime ();
	        switch ($roleInfo['vip']) {
	        	case 2:
	        		$rate = 1.1;
	        	break;
	        	case 3:
	        		$rate = 1.15;
	        	break;
	        	case 4:
	        		$rate = 1.2;
	        	break;
	        	default:
	        		$rate = 1;
	        	break;
	        }
			
	        $vip = $roleInfo['vip'];	     
	        if ($vip == 4) {
	        	$qzcs = 0;
	        	$cdcs = 4;
	        } elseif ($vip == 3) {
	        	$qzcs = 12;
	        	$cdcs = 3;
	        } elseif ($vip == 2) {
	        	$qzcs = 9;
	        	$cdcs = 2;
	        } elseif ($vip == 1) {
	        	$qzcs = 6;
	        	$cdcs = 1;
	        } else {
	        	$qzcs = 3;
	        	$vip = 0;
	        	$cdcs = 0;
	        }			
			if (date('Y-m-d',$roleInfo['qzsjrq']) == date('Y-m-d',$nowTime)) {
				$yzcs = $roleInfo['qzcs'];
			} else {
				$yzcs = 0;
			}
			if ($yzcs >= $qzcs && $qzcs != 0) {
				$value['qzcs'] = 0;			
			} else {
				if ($qzcs == 0) {
					$value['qzcs'] = -1;
				} else {
					$value['qzcs'] = $qzcs - $yzcs;	
				}				
				$last_update_time = $roleInfo ['last_collect_time'];
				$timeDiff = $nowTime - $last_update_time; //与上次征税时间间隔
				if ($timeDiff > 0) {
					$leftTime = 7200 - $timeDiff;
					$needYp = ceil($leftTime / 3600) * 5;
					if ($needYp < 0) {
						$needYp = 0;
					}
				} else {
					$needYp = 0;
				}	
				$value['qzyp'] = $needYp;
			}				
			/*获得强征信息结束*/		
			//检查是否有需要处理的任务
			//$sql = "SELECT COUNT(a.intID) FROM ".$common->tname('quests_status')." a,".$common->tname('accepted_quests')." b WHERE a.playersid = '".$roleInfo['playersid']."' && a.playersid = b.playersid && b.published = '1'";	
			//$sql = "SELECT COUNT(intID) as rwsl FROM ".$common->tname('quests_status')." WHERE playersid = '".$roleInfo['playersid']."' && QuestID IN (SELECT QuestID FROM ".$common->tname('accepted_quests')." WHERE playersid = ".$roleInfo['playersid']." && published = 1)";
			//$rows = $db->fetch_array($db->query($sql));
			$rwzt = getDataAboutLevel::hqrwzt($roleInfo['playersid']);			
			if ($rwzt !== false) {
				$value['rw'] = $rwzt;
			}
		} else {
			$value ['status'] = 4; //返回数据是否查询成功状态,0为失败
			$value ['message'] = '查询数据失败';
		}
		return $value;
	}
  
  //获取驻防信息
  public static function getZfInfo($playersid) {
  	 global $db,$common,$mc;
  	 if (!($roleInfo = $mc->get(MC.$playersid))) {
  	 	$roleInfo = $db->fetch_array($db->query("SELECT * FROM ".$common->tname('player')." WHERE playersid = '$playersid' LIMIT 1"));
  	 }
  	 if ($roleInfo['is_defend'] == 1) {
  	 	$value['status'] = 0;
  	 	$diff = $roleInfo['end_defend_time'] - time();
  	 	if ($diff < 0) {
  	 		$diff = 0;
  	 	}
  	 	$value['time'] = $diff;
  	 	$value['gid'] = $roleInfo['aggressor_general'];
  	 	$value['un'] = $roleInfo['aggressor_nickname'];
  	 	$value['ul'] = $roleInfo['aggressor_level'];  	
	 	$ginfo = cityModel::getGeneralData($playersid,0,$value['gid']);
	 	$value['gname'] = $ginfo[0]['general_name'];
	 	$value['glevel'] = $ginfo[0]['general_level'];  	 	 	
  	 } elseif ($roleInfo['is_defend'] == 0 && $roleInfo['aggressor_playersid'] != 0) {  	 	
  	 	$value['status'] = 1001;
  	 	$value['un'] = $roleInfo['aggressor_nickname'];
  	 	$value['ul'] = $roleInfo['aggressor_level'];
  	 	$incomeInfo = occupied_income($roleInfo['aggressor_general'],$roleInfo['aggressor_playersid']);
  	 	$value['time'] = $incomeInfo['time'];
  	 	$value['gid'] = $roleInfo['aggressor_general'];
	 	$ginfo = cityModel::getGeneralData($roleInfo['aggressor_playersid'],0,$value['gid']);
	 	$value['gname'] = $ginfo[0]['general_name'];
	 	$value['glevel'] = $ginfo[0]['general_level'];  	   	 	
  	 	$msgRes = $db->query("SELECT `aggressor_message` FROM ".$common->tname('aggressor_message')." WHERE `playersid` = '".$roleInfo['playersid']."' ORDER BY id DESC LIMIT 1");
	 	$msgInfo = $db->fetch_array($msgRes);
	 	if (!empty($msgInfo)) {
	 		$value['zlxy'] = $msgInfo['aggressor_message'];
	 	}
	 	$value['uid'] = $roleInfo['aggressor_playersid'];
  	 } else {
  	 	$value['status'] = 1002;
  	 }
  	 return $value;
  }  
}
