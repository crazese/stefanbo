<?php
/**
 * 活动调用处理类
 * 这个类由其他需要调用活动的类进行调用
 * @author KKND LI
 */


class hdProcess{

	/**
	 * 处理活动的入口函数
	 *
	 * @param array $handle    由要处理的脚本字符串组成的数组
	 * @param array 
	 * @return array			玩家可见活动列表
	 */
	public static function run($handle, $playerInfo, $actParam=null, $onlyRun=true){
		global $mc, $common, $db, $_SGLOBAL;

		if(!isset($_SGLOBAL['timestamp'])){
			$_SGLOBAL['timestamp'] = time();
		}
		$_current_time = $_SGLOBAL['timestamp'];
		// 获取有效活动列表
		$hdList = hdProcess::getActList();
		
		// 找到活动列表中脚本属于可处理活动的
		$actAbleRunList = array();
		foreach($handle as $actStr){
			foreach($hdList as $actInfo){
				if(in_array($actStr, $actInfo['jbstr'])){
					$actAbleRunList[] = $actInfo;
				}
			}
		}
		
		// 如果只检查分支活动并存在有效的分支活动时检查
		if(empty($actAbleRunList)&&$onlyRun){
			return array();
		}
		
		$myActList = hdProcess::getMyActList($playerInfo['playersid']);
		// 如果发现可用活动不再玩家活动列表中就添加
		$_my_act_id_list = array();
		foreach($myActList as $myAct){
			$_my_act_id_list[] = $myAct['hdid'];
		}
		
		$_new_act_list = array();
		$_un_published_act_list = array();
		
		// 如果是查看活动时调用$onlyRun为false时表示玩家查看自己活动时的调用
		// 这时将把所有未加的活动添加到玩家活动中
		$chkAddActList = $onlyRun?$actAbleRunList:$hdList;
		foreach($chkAddActList as $hdAct){
			if(0 == $hdAct['pub']){
				$_un_published_act_list[] = $hdAct['hdid'];
				continue;
			}
			if(!in_array($hdAct['hdid'], $_my_act_id_list)){
				$_new_act_list[] = $hdAct;
			}
		}
		
		if(!empty($_new_act_list)){
			$myActList = hdProcess::addUserAct($playerInfo['playersid'], $_new_act_list, $myActList);
		}
		
		// 这里先用一个变量另外保存一份玩家活动拷贝，方便玩家活动发生改变时更新缓存
		$myAllActList = $myActList;
		foreach($myActList as $myActId=>$myAct){
			// 过滤封禁的活动
			if(in_array($myAct['hdid'], $_un_published_act_list)){
				unset($myActList[$myActId]);
				break;
			}
			// 已最终完成的活动
			if($myAct['wccs'] <= $myAct['wczt']){
				unset($myActList[$myActId]);
				break;
			}
			// 过期活动无法领取的活动
			if($myAct['endTime'] < $_current_time 
			&& !($myAct['cptTimes'] > $myAct['wczt'] || 0 <= $myAct['jljd'])){
				unset($myActList[$myActId]);
				break;
			}
		}
		
		// 检查玩家对应活动状态看是否需要处理
		if(!empty($actAbleRunList)){
			$_runActList = array();
			foreach($actAbleRunList as $hdAct){
				// 过滤不需要处理的活动
				foreach($myActList as $myAct){
					if($myAct['hdid'] == $hdAct['hdid']){
						// jljd奖励进度(-1开始，每完成一个进度后加1。领奖后如果活动未结束则重新归-1)
						// jslj是否结束时领奖，1是，0不是
						// 已完成次数大于等于可完成次数
						if($myAct['cptTimes'] >= $myAct['wccs']){
							continue;
						}
						// 活动已过期或未开始或活动已关闭
						if($myAct['endTime'] < $_current_time
						|| $_current_time < $myAct['startTime']
						|| $hdAct['pub'] == 0){
							continue;
						}
						// 当前活动已处理过最高进度
						if(!isset($hdAct['jbstr'][$myAct['jljd']+1])){
							continue;
						}
						// 过滤当前活动需要调用脚本与请求脚本句柄不一致的活动
						$secActStr = $hdAct['jbstr'][$myAct['jljd']+1];
						if(!in_array($secActStr, $handle)){
							continue;
						}
						
						$blankSec = 0;
						$lastSection = $myAct['lastSection'];
						for($i = 0; $i<count($hdAct['secTime']); $i++){
							if($_current_time > $hdAct['secTime'][$i]){
								$blankSec ++;
							}
						}
						
						// 是否已完成阶段最大次数,如果到了新活动时间段就修正
						if(isset($hdAct['secCptCount'][$myAct['lastSection']])){
							if($myAct['lastSection'] < $blankSec){
								$myAct['lastSection'] = $blankSec;
								$myAct['secCptTimes'] = 0;
							}else if($myAct['secCptTimes'] >= $hdAct['secCptCount'][$myAct['lastSection']]){
								continue;
							}
						}else{
							continue;
						}
						
						$actStr = $hdAct['jbstr'][$myAct['jljd']+1];
						$_runActList[] = array('usrAct' => $myAct,
											   'runStr'=>$actStr,
											   'jbStr'=>$hdAct['jbstr'],
											   'params'=>$hdAct['params'],
											   'secTime'=>$hdAct['secTime'],
											   'secCptCount'=>$hdAct['secCptCount'],
											   'lastSection'=>$lastSection);
					}
				}
			}
			
			// 如果需要处理就开始调用
			$path = dirname(__FILE__).DIRECTORY_SEPARATOR.'processMode'.DIRECTORY_SEPARATOR;
			foreach($_runActList as $runAct){
				// 根据时间片同步当前活动结算段以计算cptTimes和wczt
				$blankSec = $runAct['usrAct']['lastSection'];
				$passCount = 0;
				for($i=0; $i<$blankSec; $i++){
					$passCount += isset($runAct['secCptCount'][$i])?$runAct['secCptCount'][$i]:1;
				}
				
				$jljd = $runAct['usrAct']['jljd'];
				// 如果活动时间不一致则修正到对应时间段并且要强制更新数据
				$force = false;

				if($runAct['lastSection'] < $blankSec){
					$diffValue = $passCount - $runAct['usrAct']['cptTimes'];

					if($jljd>=0){
						$runAct['usrAct']['wczt'] += ($diffValue -1);
					}else{
						$runAct['usrAct']['wczt'] += $diffValue;
					}
					
					$jljd = -1;
					$runAct['usrAct']['cptTimes'] += $diffValue;
					$runAct['usrAct']['procData'] = array();
					$runAct['usrAct']['jljd'] = -1;
					$force = true;
					
					$myActList[$runAct['usrAct']['ID']] = $runAct['usrAct'];
				}
				
				$actParams = array($playerInfo,
								   $runAct['params'],					// 活动完成条件参数
								   $runAct['usrAct']['procData'],		// 活动处理自定义信息
								   $runAct['usrAct']['jljd'],			// 活动完成进度
								   $actParam							// 活动自定义参数可以为空
								   );

				$callParam = explode('_', $runAct['runStr']);
				$className = $callParam[0].'RunModel';
				include_once($path.$className.'.php');
				$obj = new $className;
				$returnValue = call_user_func_array(array($obj, $callParam[1]), $actParams);

				// 如果当前活动阶段完成并有下一个同样类型的完成状态则检查下个阶段条件是否完成
				if($jljd < $returnValue['jljd']){
					// 检查阶段完成是否到达最大时,如果是更新当前阶段完成状态
					$runAct['usrAct'] = hdProcess::chkCmpltSec($returnValue['jljd'], $returnValue['procData'], $runAct['usrAct']);
					if($returnValue['jljd'] != $runAct['usrAct']['jljd']){
						$returnValue['jljd'] = $runAct['usrAct']['jljd'];
						$force = true;
					}
					while($returnValue['jljd'] < (count($runAct['jbStr']) - 1)){
						// 如果当前活动阶段已完成最大可完成次数,则跳出
						if($runAct['secCptCount'][$runAct['lastSection']] <= $runAct['usrAct']['secCptTimes']){
							break;
						}
						$nextRunStr = $runAct['jbStr'][$returnValue['jljd']+1];
						if($runAct['runStr'] == $nextRunStr){
							$nextActParams = array($playerInfo,
												   $runAct['params'],
												   $returnValue['procData'],
												   $returnValue['jljd'],
												   null
												   );
							$_returnValue = call_user_func_array(array($obj, $callParam[1]), $nextActParams);

							// 如果完成进度没有改变就跳出
							if($_returnValue['jljd'] <= $returnValue['jljd']){
								break;
							}

							// 检查阶段完成是否到达最大时,如果是更新当前阶段完成状态
							$runAct['usrAct'] = hdProcess::chkCmpltSec($_returnValue['jljd'], $_returnValue['procData'], $runAct['usrAct']);
							if($_returnValue['jljd'] != $runAct['usrAct']['jljd']){
								$_returnValue['jljd'] = $runAct['usrAct']['jljd'];
								$force = true;
							}
							$returnValue = $_returnValue;
						}else{
							// 如果下一个完成进度角本和当前不一置则跳出
							break;
						}
					}
				}

				if(isset($returnValue['forceUpdate'])&&$returnValue['forceUpdate']==true){
					$force = true;
				}
				
				// 活动奖励进度变大后才能修改进度，更新用的玩家数据是过滤前的所有玩家活动数据
				if($force || $jljd < $returnValue['jljd']){
					$myAllActList[$runAct['usrAct']['ID']] = $runAct['usrAct'];
					hdProcess::mdfUserAct($playerInfo,
										$runAct['usrAct']['ID'],
										$returnValue['procData'], 
										$returnValue['jljd'], 
										$myAllActList, 
										$runAct['secTime']);
					$myActList[$runAct['usrAct']['ID']] = $myAllActList[$runAct['usrAct']['ID']];
				}
			}
		}
		return $myAllActList;
	}

	public static function chkCmpltSec($jljd, $procData, $mdfActInfo){
		if(($jljd+1) == count($mdfActInfo['jpsj'])){
			if(isset($mdfActInfo['jpjl'])){
				$jpjl = $mdfActInfo['jpjl']==''?array():explode(',', $mdfActInfo['jpjl']);
			}else{
				$jpjl = array();
			}
			$jpjl[] = $jljd;
			$mdfActInfo['jpjl'] = implode(',', $jpjl);
			$mdfActInfo['cptTimes']++;
			$mdfActInfo['secCptTimes']++;
			$mdfActInfo['jljd'] = -1;
			// 如果没有在自定义存储段上设置保存那么阶段最高级完成时将被重置数据
			if(!(isset($procData['save'])&&$procData['save'])){
				$mdfActInfo['procData'] = array();
			}
		}else{
			$mdfActInfo['jljd'] = $jljd;			
		}

		if(!isset($mdfActInfo['jpjl'])){
			$mdfActInfo['jpjl'] = '';
		}

		return $mdfActInfo;
	}
	
	/**
	 * 修改用户活动状态，用来在用户活动脚本完成后修改对应的用户活动状态
	 *
	 * @param array $playerInfo			玩家信息
	 * @param int $myActId				活动对应ID
	 * @param array $procData			脚本要修改的活动状态数据，如果活动阶段完成请传入“空数组”重新初始化活动脚本状态
	 * @param int $jljd					奖励进度，整个活动的完成状态，默认为 -1
	 * @param array $myActInfo			玩家活动列表信息，方便修改玩家信息用
	 * @param array $secTime			活动结算段的时间配置
	 */
	public static function mdfUserAct($playerInfo, $myActId, $procData, $jljd, &$myActInfo, $secTime){
		global $mc, $common, $db;
		
		$mdfActInfo = $myActInfo[$myActId];
		
		$mdfActInfo['procData'] = $procData;
		$mdfActInfo['jljd'] = $jljd;
		
		// 当活动阶段完成到达最高自动转成一次完成活动，并重置相应数据
		$mdfActInfo = hdProcess::chkCmpltSec($jljd, $procData, $mdfActInfo);
		
		$myActInfo[$myActId] = $mdfActInfo;
		$mc->set(MC.$playerInfo['playersid'].'_actlist', array('list'=>$myActInfo), 0, 3600);
		
		$updateData['cptTimes'] = $mdfActInfo['cptTimes'];
		$updateData['jljd']     = $mdfActInfo['jljd'];
		$updateData['procData'] = serialize($mdfActInfo['procData']);
		$updateData['wczt']     = $mdfActInfo['wczt'];
		$updateData['jslj']     = $mdfActInfo['jslj'];
		$updateData['jpjl']     = $mdfActInfo['jpjl'];
		$updateData['secCptTimes'] = $mdfActInfo['secCptTimes'];
		$updateData['lastSection'] = $mdfActInfo['lastSection'];
		
		$whereArray = array('ID'=>$myActId);
		$common->updatetable('my_huodong', $updateData, $whereArray);
	}
	
	/**
	 * 添加一个新活动到玩家活动列表
	 *
	 * @param array $playerid
	 * @param array $newActList			要添加的新活动信息列
	 * @return array 					更新后的玩家活动信息
	 */
	public static function addUserAct($playerid, $newActList, $myActInfo=null){
		global $mc, $common, $db, $_SGLOBAL;
		if(!isset($_SGLOBAL['timestamp'])){
			$_SGLOBAL['timestamp'] = time();
		}
		$_current_time = $_SGLOBAL['timestamp'];

		if(is_null($myActInfo)){
			$myActInfo = hdProcess::getMyActList($playerid);
		}
		
		foreach($newActList as $actInfo){
			if(0 == $actInfo['pub']){
				continue;
			}
			$_new_my_act = array('playersid'=>$playerid,
								'hdid'=>$actInfo['hdid'],
								'wccs'=>$actInfo['wccs'],
								 'createTime'=>$_current_time,
								'cptTimes'=>0,
								'startTime'=>$actInfo['startT'],
								'endTime'=>$actInfo['endT'],
								'jljd'=>-1,
								'title'=>$actInfo['title'],
								'desc'=>$actInfo['desc'],
								'hddx'=>$actInfo['hddx'],
								'jpsj'=>$actInfo['jpsj'],				// 写入数据库时需要序列化
								'procData'=>array(),					// 自定义处理数据，开始时为空数组
								'wczt'=>0,
								'jslj'=>$actInfo['jslj'],
								'secCptTimes'=>0,
								'lastSection'=>0);
								
			$insertData = $_new_my_act;
			$insertData['jpsj']     = serialize($insertData['jpsj']);
			$insertData['procData'] = serialize($insertData['procData']);
			
			$id = $common->inserttable('my_huodong', $insertData);
			$_new_my_act['ID'] = $id;
			$myActInfo[$id] = $_new_my_act;
		}
		
		$mc->set(MC.$playerid.'_actlist', array('list'=>$myActInfo), 0, 3600);
		// 这里还不能批量插入活动，因为mem写入要id值。而且同一时段有效的活动不会太多
		//if(!empty($valueList)){
		//	$valueStr = implode(',', $valueList);
		//	$db->query($insertSql.$valueStr);
		//}
		
		return $myActInfo;
	}
	
	/**
	 * 获取指定玩家活动列表
	 *
	 * @param int $playersid		对应玩家id
	 * @return array				玩家活动id为主键的玩家活动列表信息
	 */
	public static function getMyActList($playersid){
		global $mc, $common, $db, $_SGLOBAL;
		if(!isset($_SGLOBAL['timestamp'])){
			$_SGLOBAL['timestamp'] = time();
		}
		$_current_time = $_SGLOBAL['timestamp'];
		
		$key = MC.$playersid.'_actlist';
		
		$actList = $mc->get($key);
		$needSaveMem = false;
		if(empty($actList)){
			$myActSlt = "select * from ".$common->tname('my_huodong');
			$myActSlt .= " where playersid='{$playersid}'";
			$result = $db->query($myActSlt);
			$actList = array();
			while ($rows = $db->fetch_array($result)) {
				$rows['jpsj']     = unserialize($rows['jpsj']);
				$rows['procData'] = unserialize($rows['procData']);
				$actList[] = $rows;
			}
			
			$needSaveMem = true;
		}else{
			$actList = $actList['list'];
		}
		
		$_my_act_list = array();
		$_my_drop_act_ids = array();
		
		// 未过期但停止的活动还是会输出，避免活动状态改变时重复添加
		foreach($actList as $actInfo){
			if( $actInfo['endTime'] < $_current_time){
				// 过滤已过期但没有领奖机会的活动
				if(!(0 <= $actInfo['jljd'] || $actInfo['cptTimes'] > $actInfo['wczt'])){
					$_my_drop_act_ids[] = $actInfo['ID'];
					continue;
				}
				// 过滤已过期同时已领取所有奖励的活动
				if($actInfo['wccs'] <= $actInfo['wczt']){
					$_my_drop_act_ids[] = $actInfo['ID'];
					continue;
				}
			}
			
			$_my_act_list[$actInfo['ID']] = $actInfo;
		}
		
		// 删除过期无效的活动
		if(!empty($_my_drop_act_ids)){
			$del_ids = implode(',', $_my_drop_act_ids);
			$delSql = "delete from ".$common->tname('my_huodong');
			$delSql .= " where playersid='{$playersid}' and ID in ({$del_ids})";
			
			$db->query($delSql);
			$needSaveMem = true;
		}
		
		// 按需更新memcache
		if($needSaveMem){
			$mc->set($key, array('list'=>$_my_act_list), 0, 3600);
		}
		
		return $_my_act_list;
	}
	
	/**
	 * 获取有效的活动信息和所有比当前时间早的封禁活动
	 * @return array		活动id为主键的活动信息
	 */
	private static $hdList = null;
	
	public static function getActList(){
		global $mc, $common, $db, $_SGLOBAL;
		if(!isset($_SGLOBAL['timestamp'])){
			$_SGLOBAL['timestamp'] = time();
		}

		$nowTime = $_SGLOBAL['timestamp'];
		$hdData = array();
		
		if(!is_null(hdProcess::$hdList)){
			return hdProcess::$hdList;
		}
		
		$hdData = $mc->get(MC.'hddata');
		if (empty($hdData)) {
			$sql = "SELECT * FROM ".$common->tname('huodong');
			$sql .= " WHERE (published = 1 && endTime > $nowTime) ";
			$sql .= " or (published = 0 and (startTime < $nowTime or showTime < $nowTime))";
			$sql .= " ORDER BY startTime DESC";
			$result = $db->query($sql);
			$data = array();
			while ($rows = $db->fetch_array($result)) {
				$secCptCount = empty($rows['secCptCount'])?array():explode('_', $rows['secCptCount']);
				$_add_act['hdid']    = $rows['hdid'];
				$_add_act['title']   = $rows['title'];
				$_add_act['desc']    = $rows['desc'];
				$_add_act['hddx']    = $rows['hddx'];
				$_add_act['showT']   = $rows['showTime'];
				$_add_act['startT']  = $rows['startTime'];
				$_add_act['endT']    = $rows['endTime'];
				$_add_act['jpsj']    = unserialize($rows['jpsj']);    // 奖励数据
				$_add_act['params']  = unserialize($rows['params']);  // 脚本参数
				$_add_act['jbstr']   = unserialize($rows['jbstr']);   // 脚本字串
				$_add_act['pub']     = $rows['published'];
				$_add_act['wccs']    = $rows['wccs'];
				$_add_act['jslj']    = $rows['jslj'];
				$_add_act['secTime'] = unserialize($rows['secTime']); // 活动结算时间点的定义
				$_add_act['secCptCount'] = $secCptCount;
				
				$data[$_add_act['hdid']] = $_add_act;
			}
			$mc->set(MC.'hddata',array('list'=>$data, 't'=>time()),0,1296000);  //数据存放15天，提交新数据时需清空改内存数据
			$hdData = $data;
		}else{
			$hdData = $hdData['list'];
		}
		
		$_tmp_hdData = array();
		foreach($hdData as $hdInfo){
			// 过滤已过期但是未封禁活动，正常结束的活动是不需要对玩家活动检查状态的
			if($hdInfo['endT']<$nowTime && 1 == $hdInfo['pub']){
				continue;
			}
			// 添加已开始活动
			if($hdInfo['startT'] < $nowTime
			||$hdInfo['showT'] < $nowTime){
				$_tmp_hdData[$hdInfo['hdid']] = $hdInfo;
			}
		}
		
		hdProcess::$hdList = $_tmp_hdData;
		return $_tmp_hdData;
	}

}