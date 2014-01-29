<?php
class toolsController {
	// 获取背包列表
	public static function bglb($getInfo) {		
		$ret_val = toolsModel::getAllItems($getInfo['playersid']);		
		ClientView::show($ret_val);
	}
	
	// 背包扩容
	public static function bgkr($getInfo) {
		$ret_val = toolsModel::kuorong($getInfo['playersid']);
		ClientView::show($ret_val);
	}
	
	// 出售道具
	public static function csdj($getInfo) {
		$ret_val = toolsModel::sellItem($getInfo['playersid']);
		ClientView::show($ret_val);
	}
	
	// 使用道具
	public static function sydj($getInfo) {
		global $common, $db, $mc, $G_PlayerMgr, $tools_lang;
		$ret_val = array();
		
		$playersid = $getInfo['playersid'];
		
		$player = $G_PlayerMgr->GetPlayer($playersid );
		if(!$player)	return array('status'=>21, 'message'=>$tools_lang['model_msg_1']);
		
		// 道具ID
		$djid = intval(_get('djid'));
		// 是否全部使用
		$all = intval(_get('all'));		
		
		// 获取道具对应脚本
		$script = null;
		$param = null;
		$playerBag = $player->GetItems();//null;
	
		if(array_key_exists($djid, $playerBag)) {
			$itemid = $playerBag[$djid]['ItemID'];
			$itemproto = ConfigLoader::GetItemProto($itemid);
			$script = $itemproto['ItemScript'];
			$param  = $itemproto['ItemScript_Parameter'];
			// 判断是否可使用
			$kysy = 0;
			if($itemproto['ItemType'] == 2) {
				if(array_key_exists($itemproto['EquipType'], getDjsyArr())) {
					$kysy = 1;
				}
			}

			if($kysy === 0) {
				$ret_val = array('status'=>21, 'message'=>$tools_lang['controller_msg_64']);				
			} else {
				if($all != 1) {
					//$ret_val = $script::getItemUseMethod($param, $playersid, $djid, $playerBag);
					$ret_val = call_user_func_array($script.'::getItemUseMethod', array($param, $playersid, $djid, $playerBag));
				} else {
					/**/
					// 获取ItemID对应的所有djid
					//set_time_limit(0);
					$raw_item_id = $itemid;
					$item_name = $itemproto['Name'];				
					$djid_array = null;
					foreach($playerBag as $wid=>$djInfo) {
						if($djInfo['ItemID'] == $raw_item_id) 
							$djid_array[] = $wid;
					}				
					// 需累加的
					$hqtq = $hqyp = $hqyb = $hqsw = $hqjl= $hqjy= $bggs = 0;
					// 无需累加
					$hqjf = 0;
					$xjjy = $jlsx = $level = $jy = $tq = $yp = $yb = $sw = $jl = $curr_bggs = $ltjf = 0;
					$list = $upgrade = $complate_list = null;
					$mutil_use_ret = $hqjl_array = array();
					$first = true;
					$item_cnt = 0;				
					$tqb = $ypb = $jlb = null;
					while(true) {
						if(isset($djid_array[0]))
							$djid = $djid_array[0];				
						else 
							$djid = null;
						if(!array_key_exists($djid, $playerBag)) {
							array_shift($djid_array);
							if(isset($djid_array[0]))
								$djid = $djid_array[0];
							else 
								$djid = null;
						}
						
						if($djid != null) {
							if(array_key_exists($djid, $playerBag)) {
								$ret_val = call_user_func_array($script.'::getItemUseMethod', array($param, $playersid, $djid, $playerBag));
							} else {
								$ret_val['status'] = 998;
							}
						} else {
							$ret_val['status'] = 998;
						}
						
						if($ret_val['status'] == 0 	) {
							toolsModel::mdfUseInfo();
							$item_cnt++;
							
							$hqtq = isset($ret_val['hqtq']) ? $hqtq + $ret_val['hqtq'] : $hqtq;
							$hqyp = isset($ret_val['hqyp']) ? $hqyp + $ret_val['hqyp'] : $hqyp;
							$hqyb = isset($ret_val['hqyb']) ? $hqyb + $ret_val['hqyb'] : $hqyb;
							$hqsw = isset($ret_val['hqsw']) ? $hqsw + $ret_val['hqsw'] : $hqsw;
							$hqjl = isset($ret_val['hqjl']) ? $hqjl + $ret_val['hqjl'] : $hqjl;
							$hqjy = isset($ret_val['hqjy']) ? $hqjy + $ret_val['hqjy'] : $hqjy;
							
							if(isset($ret_val['gs'])) {
								$bggs++;
								$curr_bggs = $ret_val['gs'];
							};
							
							if(isset($ret_val['upgrade'])) {
								if($ret_val['upgrade'] == 1) {
									$upgrade = 1;
								} else {
									if($upgrade != 1) {
										$upgrade = 0;
									}
								}
							}
							$xjjy = isset($ret_val['xjjy']) ? $ret_val['xjjy'] : $xjjy;
							$jlsx = isset($ret_val['jlsx']) ? $ret_val['jlsx'] : $jlsx;
							$level = isset($ret_val['level']) ? $ret_val['level'] : $level;
							$jy = isset($ret_val['jy']) ? $ret_val['jy'] : $jy;
							$tq = isset($ret_val['tq']) ? $ret_val['tq'] : $tq;
							$yp = isset($ret_val['yp']) ? $ret_val['yp'] : $yp;
							$yb = isset($ret_val['yb']) ? $ret_val['yb'] : $yb;
							$sw = isset($ret_val['sw']) ? $ret_val['sw'] : $sw;
							$jl = isset($ret_val['jl']) ? $ret_val['jl'] : $jl;
							$ltjf = isset($ret_val['ltjf']) ? $ret_val['ltjf'] : $ltjf;
							$hqjf = isset($ret_val['hqjf']) ? $ret_val['hqjf'] : $hqjf;
							
							$list = isset($ret_val['list']) ? $ret_val['list'] : $list;
							if($list != null) {
								foreach($list as $key=>$value) {
									if(count($value) > 6) {
										$complate_list[$value['djid']] = $value;
									}
								}
							}
							
							// 获取使用道具所获得的道具名及数量
							if(isset($ret_val['hqdj'])) {
								if(count($ret_val['hqdj']) > 0 ) {
									foreach($ret_val['hqdj'] as $key=>$value) {
										if(array_key_exists($value[0], $hqjl_array)) {
											$hqjl_array[$value[0]] = $hqjl_array[$value[0]] + $value[1];
										} else {									
											$hqjl_array[$value[0]] = $value[1];
										}
									}
								}
							}
							
							$first = false;

						} else if($ret_val['status'] == 998 
								  || $ret_val['status'] == 3 
								  || $ret_val['status'] == 1021 ) {						
							if($first && ($raw_item_id == 20114 
										  || $raw_item_id == 20000 
										  || $ret_val['message'] == $tools_lang['controller_msg_65'])) {
								toolsModel::mdfUseInfo();
								ClientView::show($ret_val);
								exit;
							}
							// 使用了部分道具背包满
							$mutil_use_ret['status']  = 0;
							if($ret_val['message'] == $tools_lang['controller_msg_65']) {
								$mutil_use_ret['message']  = $tools_lang['controller_msg_66'] . $item_cnt . "{$tools_lang['model_msg_17']}[" . $item_name . "], {$tools_lang['controller_msg_67']}";
							} else {
								$mutil_use_ret['message']  = "{$tools_lang['controller_msg_79']}[" . $item_name . "], {$tools_lang['controller_msg_67']}";
							}
							if($list != null) {
								if($complate_list != null) {
									foreach($complate_list as $k=>$v) {
										for($i = 0; $i < count($list); $i++) {
											if($k == $list[$i]['djid']) {
												$real_item_mum = $list[$i]['num'];
												$list[$i] = $v;
												$list[$i]['num'] = $real_item_mum;
											}
										}
									}
								}
								$mutil_use_ret['list'] = $list;
							}
							
							if(count($hqjl_array) > 0) {
								foreach($hqjl_array as $key=>$value) {
									$mutil_use_ret['message']  .=  "[{$key}]" . 'X' . $value . '、';
								}
							}

							if($hqtq != 0) {
								$mutil_use_ret['hqtq'] = $hqtq; 
								$mutil_use_ret['message']  .=  "[{$tools_lang['controller_msg_68']}]X" . $hqtq . '、';
							}
							if($hqyp != 0) {
								$mutil_use_ret['hqyp'] = $hqyp;
								$mutil_use_ret['message']  .=  "[{$tools_lang['controller_msg_69']}]X" . $hqyp . '、';
							}
							if($hqjl != 0) {
								$mutil_use_ret['hqjl'] = $hqjl;
								$mutil_use_ret['message']  .=  "[{$tools_lang['controller_msg_70']}]X" . $hqjl . '、';
							}
							
							if($hqyb != 0) {
								$mutil_use_ret['hqyb'] = $hqyb;
								$mutil_use_ret['message']  .=  "[{$tools_lang['controller_msg_71']}]X" . $hqyb . '、';
							}
							if($hqsw != 0) {
								$mutil_use_ret['hqsw'] = $hqsw;
								$mutil_use_ret['message']  .=  "[{$tools_lang['controller_msg_72']}]X" . $hqsw . '、';
							}
							if($hqjy != 0) {
								$mutil_use_ret['hqjy'] = $hqjy;					
								$mutil_use_ret['message']  .=  "[{$tools_lang['controller_msg_73']}]X". $hqjy . '、';							
							}
							if($bggs != 0) {
								$mutil_use_ret['gs'] = $curr_bggs;					
								$mutil_use_ret['message']  .=  "{$tools_lang['controller_msg_74']}X". $bggs . '、';							
							}

							$mutil_use_ret['message'] = substr($mutil_use_ret['message'], 0, strlen($mutil_use_ret['message']) - strlen('、'));
							if(strpos($mutil_use_ret['message'], $tools_lang['controller_msg_74']) != false)
								$mutil_use_ret['message'] = "{$tools_lang['controller_msg_75']}{$item_cnt}个{$item_name}{$tools_lang['controller_msg_76']}{$bggs}{$tools_lang['controller_msg_77']}";
							
							// 背包已满
							if($ret_val['message'] == $tools_lang['controller_msg_65']) {
								$mutil_use_ret['message'] .= '，' . $tools_lang['controller_msg_80'];	
							}
							
							if($upgrade !== null) $mutil_use_ret['upgrade'] = $upgrade;
							if($xjjy != 0) $mutil_use_ret['xjjy'] = $xjjy;
							if($jlsx != 0) $mutil_use_ret['jlsx'] = $jlsx;
							if($level != 0) $mutil_use_ret['level'] = $level;
							if($jy != 0) $mutil_use_ret['jy'] = $jy;
							if($tq != 0) $mutil_use_ret['tq'] = $tq;
							if($yp != 0) $mutil_use_ret['yp'] = $yp;
							if($yb != 0) $mutil_use_ret['yb'] = $yb;
							if($sw != 0) $mutil_use_ret['sw'] = $sw;
							if($jl != 0) $mutil_use_ret['jl'] = $jl;
							if($hqjf != 0) $mutil_use_ret['hqjf'] = $hqjf;
							if($ltjf != 0) $mutil_use_ret['ltjf'] = $ltjf;
							
							$mutil_use_ret['sygs'] = $item_cnt;
							toolsModel::mdfUseInfo();
							ClientView::show($mutil_use_ret);
							exit;
						}
						$playerBag = $player->GetItems();
					}/**/
				}

				// 获取背包数据
				/*if(!isset($ret_val['list'])) {
					$bagDataInfo = toolsModel::getAllItems($playersid);
					$bagData = $bagDataInfo['list'];
					$ret_val['list'] = $bagData;
				}*/				
			}
		} else { // 背包中不存在此物品
			// 获取背包数据						
			$ret_val['list'] = $player->GetClientBag();
			
			$ret_val = array('status'=>21, 'message'=>$tools_lang['controller_msg_78']);
		}
		
		toolsModel::mdfUseInfo();
		ClientView::show($ret_val);
	}
	
	// 获取商城物品列表
	public static function sclb($getInfo) {
		$ret_val = toolsModel::sclb($getInfo['playersid']);
		ClientView::show($ret_val);
	}
	
	// 购买商城道具
	public static function gmdj($getInfo) {
		$playersid = $getInfo['playersid'];		
		$djid = _get('djid');
		$djsl = _get('djsl');
		$ret_val = toolsModel::gmdj($playersid, $djid, $djsl);
		ClientView::show($ret_val);
	}
	
	// 获取对应道具的强化信息
	public static function qqqhxx($getInfo) {
		$playersid = $getInfo['playersid'];		
		$djid = _get('djid');		
		$ret_val = toolsModel::qhxx($playersid, $djid);
		ClientView::show($ret_val);
	}
	
	// 强化对应的道具
	public static function qhzb($getInfo) {
		$playersid = $getInfo['playersid'];		
		$djid = _get('djid');
		$yyb  = _get('hyb');	
		$ret_val = toolsModel::qhdj($playersid, $djid, $yyb);
		ClientView::show($ret_val);
	}
	
	// 获取装备锻造信息
	public static function qqdzxx($getInfo) {
		$playersid = $getInfo['playersid'];
		$djid = _get('djid');
		$ret_val = toolsModel::qqdzxx($playersid, $djid);
		ClientView::show($ret_val);
	}
	
	// 锻造装备
	public static function dzzb($getInfo) {
		$playersid = $getInfo['playersid'];
		$djid = _get('djid');
		$hyb = _get('hyb');
		$ret_val = toolsModel::dzzb($playersid, $djid, $hyb);
		ClientView::show($ret_val);
	}
	
	// 装备分解
	public static function zbfj($getInfo) {
		$playersid = $getInfo['playersid'];		
		$wid = _get('djid');
		$ret_val = toolsModel::zbfj($playersid, $wid);
		ClientView::show($ret_val);
	}	
	
	/**
	 * 
	 * 使用技能书
	 * @param array $getInfo 当前请求信息
	 */
	public static function jns($getInfo){
		$playersid = $getInfo['playersid'];
		$jnid = _get('jnid');
		$gid = _get('gid');
		$yb = _get('hyb');
		$djid = _get('djid');
		$ret_val = toolsModel::xxjn($playersid, $gid, $jnid, $yb, $djid);
		ClientView::show($ret_val);
	}
	
	
	/**
	 * 
	 * 杜康酒接口
	 * 新的需求中不再消耗道具
	 * @param array $getInfo
	 */
	public static function dkj($getInfo){
		$playersid = $getInfo['playersid'];
		//$djid = _get('djid');
		$generalID = _get('gid');
		$jnbh = _get('jnbh');
		$ret_val = toolsModel::deleteSkillByTools($playersid, $generalID, $jnbh);
		//$ret_val['djid'] = $djid;
		$ret_val['gid']  = $generalID;
		ClientView::show($ret_val);
	}
	
	public static function latecj($getInfo){
		$playersid = $getInfo['playersid'];
		
		$ret_val = toolsModel::lateCj($playersid);
		ClientView::show($ret_val);
	}
	
	// 获取抽奖信息
	public static function hqcjxx($getInfo) {
		$playersid = $getInfo['playersid'];
		$xxid = _get('xxid');
		$ret_val = toolsModel::hqcjxx($playersid, $xxid);
		ClientView::show($ret_val);
	}
	
	/**
	 * 
	 * 道具合成接口
	 * @param array $getInfo 
	 */
	public static function djhc($getInfo){
		$playersid = $getInfo['playersid'];
		$djid = _get('djid');
		$all  = _get('all');

		$ret_val = toolsModel::combinTool($playersid, $djid, $all);
		ClientView::show($ret_val);
		
	}
	
	/**
	 * @author kknd li
	 * 抽取奖励接口
	 * @param array $getInfo
	 */
	public static function cqjl($getInfo){
		$playersid = $getInfo['playersid'];
		$xxid = _get('xxid');
		if(isset($_REQUEST['qpbh'])){
			$qpbh = _get('qpbh');
		}
		else{
			$qpbh = '';
		}
		
		$ret_val = toolsModel::getCjResult($playersid, $qpbh, $xxid);
		ClientView::show($ret_val);
	}
	
	/**
	 * 
	 * 测试用接口
	 */
	public static function test($getInfo){
		global $mc, $common;
		
		$debug = _get('debug');
		
		if(defined('PRESSURE_TEST_DEBUG') && $debug){
			$playersid  = $getInfo['playersid'];
			$itemString = _get('items');
			$itemList   = explode('|', $itemString);
			$addItems   = array();
			foreach($itemList as $item){
				$itemArray = explode(',', $item);
				$addItems[$itemArray[0]] = $itemArray[1];
			}
			
			if(toolsModel::addItems($playersid, $addItems, $djIdList)){
				$bgDataInfo = toolsModel::getAllItems($playersid);
				$returnValue['list'] = $bgDataInfo['list'];
				$returnValue['status'] = 0;
				$returnValue['zbids']  = $djIdList;
				
				$bagInfo = toolsModel::getMyItemInfo($playersid);
				// 修改道具强化值
				foreach($itemList as $item){
					$itemArray = explode(',', $item);
					if(!isset($itemArray[2])) continue;
					if($itemArray[2] == 0) continue;
					
					foreach($djIdList as $djId=>$itemKey){
						if($itemArray[0] == $itemKey){
							$bagInfo[$djId]['QhLevel'] = $itemArray[2];
							$whereArray['ID']          = $djId;
							$updateArray['QhLevel']    = $itemArray[2];
							$common->updatetable('player_items', $updateArray, $whereArray);
						}
					}
				}
				
				$mc->set(MC.'items_'.$playersid, $bagInfo, 0, 3600);
				
				ClientView::show($returnValue);
			}
			else{
				$returnValue['status'] = 23;
				$returnValue['message'] = "too full";
				ClientView::show($returnValue);
			}
			
		}
		else{
			ClientView::show(array('status'=>1001, 'message'=>'error'));
		}
	}

}