<?php 
/**
 * 等级相关活动运行脚本
 * 每个函数的传入参数必须定义如下：
 * $playerInfo 触发活动脚本的玩家信息
 * $params     脚本完成条件
 * $procData   处理过程数据
 * $jljd       奖励进度（活动完成度），-1开始的值，每完成一次加一
 * 
 * 脚本处理完成后直接返回包含jljd（奖励进度），procData（自定义处理数据）的数组
 *
 */

class roleRunModel{
	/**
	 * 活动完成时检查等级活动
	 *
	 * @param unknown_type $playerInfo
	 * @param array $params				玩家等级
	 * @param unknown_type $procData
	 * @param unknown_type $jljd
	 */
	public function endLevel($playerInfo, $params, $procData, $jljd){
		if($playerInfo['player_level'] >= $params[$jljd+1][0]){
			$jljd ++;
		}

		return array('jljd'=>$jljd, 'procData'=>$procData);
	}
	
	/**
	 * 用户登录活动，脚本强制一天只能完成一次
	 *
	 * @param unknown_type $playerInfo
	 * @param array $params				计算登录次数
	 * @param unknown_type $procData
	 * @param unknown_type $jljd
	 */
	public function login($playerInfo, $params, $procData, $jljd, $actParam=null){
		$_actV = intval($actParam);
		$loginTimes = isset($procData['logs'])?$procData['logs'] + $_actV:$_actV;

		if($loginTimes >= $params[$jljd+1][0]){
			$jljd++;
			$loginTimes -= $params[$jljd][0];
		}
		$procData['logs'] = $loginTimes;
		$procData['save'] = true;

		return array('jljd'=>$jljd, 'procData'=>$procData, 'forceUpdate'=>true);

		/* $lastLoginTime = isset($procData['t'])?$procData['t']:'00000000'; */
		/* $curDate = date('Ymd'); */
		
		/* $procData = array(); */
		/* if($curDate != $lastLoginTime){ */
		/* 	$jljd++; */
		/* 	$procData['save'] = true; */
		/* } */
		/* $procData['t'] = $curDate; */
		
		/* return array('jljd'=>$jljd, 'procData'=>$procData); */
	}
	
	/**
	 * 活动完成时检查添加好友活动
	 *
	 * @param unknown_type $playerInfo
	 * @param array $params				完成活动好友数量
	 * @param unknown_type $procData
	 * @param unknown_type $jljd
	 * @param int $actParam				当前好友数量
	 */
	public function endAddFriend($playerInfo, $params, $procData, $jljd, $actParam=null){
		if(is_null($actParam)){
			$friendIds = roleModel::getTableRoleFriendsInfo($playerInfo['playersid'], 1, true);
			$actParam = count($friendIds);
		}
		
		if($params[$jljd + 1][0]<=$actParam){
			$jljd++;
		}
		
		return array('jljd'=>$jljd, 'procData'=>$procData);
	}
	
	/**
	 * 活动完成时检查爵位活动
	 *
	 * @param unknown_type $playerInfo
	 * @param array $params				完成活动爵位值
	 * @param unknown_type $procData
	 * @param unknown_type $jljd
	 * @param unknown_type $actId
	 * @param unknown_type $myActList
	 */
	public function endGrade($playerInfo, $params, $procData, $jljd){
		$gValue = $playerInfo['mg_level'];

		if($params[$jljd + 1][0] <= $gValue){
			$jljd++;
		}

		return array('jljd'=>$jljd, 'procData'=>$procData);
	}
	
	/**
	 * 平台展示活动，不需要嵌入到其他中
	 *
	 * @param unknown_type $playerInfo
	 * @param unknown_type $params
	 * @param unknown_type $procData
	 * @param unknown_type $jljd
	 */
	public function show($playerInfo, $params, $procData, $jljd){
	}
}