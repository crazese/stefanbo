<?php 
/**
 * 等级相关活动运行脚本
 * 每个函数的传入参数必须定义如下：
 * $playerInfo 触发活动脚本的玩家信息
 * $params     脚本完成条件的各级条件，二维数组
 * $procData   处理过程数据
 * $jljd       奖励进度（活动完成度），-1开始的值，每完成一次加一
 * $actId      触发的对应玩家活动ID
 * $myActList  触发玩家的活动列表
 * $actParam   活动自定义参数
 * 
 * 脚本处理完成后如果处理过程数据或者奖励进度发生改变就需要调用
 * hdProcess::mdfUserAct
 * 方法保存改变的数据
 *
 */

class fightRunModel{
	/**
	 * 每日闯关次数活动（时间跨度不能超过一天）
	 *
	 * @param unknown_type $playerInfo
	 * @param array $params				闯关次数
	 * @param array $procData			闯关活动数据
	 * @param unknown_type $jljd
	 * @param mix $actParam				活动自定义参数
	 */
	public function dailyCg($playerInfo, $params, $procData, $jljd, $actParam=null){
		$_actV = intval($actParam);
		$count = isset($procData['l_count']) ? $procData['l_count'] + $_actV : $_actV;
		
        if($playerInfo['player_level'] < $params[$jljd+1][1]){
          return array('jljd'=>$jljd);
        }
		if($count>=$params[$jljd+1][0]){
			$jljd++;
			$count -= $params[$jljd][0];
		}
		$procData['l_count'] = $count;
		$procData['save']  = true;
		
		return array('jljd'=>$jljd, 'procData'=>$procData, 'forceUpdate'=>true);
	}
	
	/**
	 * 闯关次数活动，按参数中0决定完成次数，1决定开始等级
	 * 该活动将记录累计数据直到活动最大条件完成
	 *
	 * @param unknown_type $playerInfo
	 * @param unknown_type $params
	 * @param unknown_type $procData
	 * @param unknown_type $jljd
	 * @param int $actParam			当前闯关次数
	 */
	public function countCg($playerInfo, $params, $procData, $jljd, $actParam=null){
		$_actV = intval($actParam);
		$count = isset($procData['countCg']) ? $procData['countCg'] + $_actV : $_actV;
		
        if($playerInfo['player_level'] < $params[$jljd+1][1]){
          return array('jljd'=>$jljd);
        }
		if($count>=$params[$jljd+1][0]){
			$jljd++;
		}
		// 完成所有条件时将玩家数据置零
		if(!isset($params[$jljd + 1])){
			$count = 0;
		}
		$procData['countCg'] = $count;
		$procData['save']  = true;
		
		return array('jljd'=>$jljd, 'procData'=>$procData, 'forceUpdate'=>true);
	}


	/**
	 * 打擂活动脚本
	 *
	 * @param unknown_type $playerInfo
	 * @param unknown_type $params
	 * @param unknown_type $procData
	 * @param unknown_type $jljd
	 * @param unknown_type $actParam
	 */
	public function dailyDalei($playerInfo, $params, $procData, $jljd, $actParam = null){
		$actParam = is_integer($actParam)?$actParam:0;
		$count = isset($procData['countDl']) ? $procData['countDl'] + $actParam : $actParam;

		if($count >= $params[$jljd+1][0]){
			$jljd ++;
			$count -= $params[$jljd][0];
		}
		$procData['countDl'] = $count;
		$procData['save']  = true;

		return array('jljd'=>$jljd, 'procData'=>$procData, 'forceUpdate'=>true);
	}


	/**
	 * 打擂次数活动脚本
	 * 该活动将记录累计数据直到活动最大条件完成
	 *
	 * @param unknown_type $playerInfo
	 * @param unknown_type $params
	 * @param unknown_type $procData
	 * @param unknown_type $jljd
	 * @param unknown_type $actParam
	 */
	public function countDalei($playerInfo, $params, $procData, $jljd, $actParam = null){
		$actParam = is_integer($actParam)?$actParam:0;
		$count = isset($procData['countDl']) ? $procData['countDl'] + $actParam : $actParam;

		if($count >= $params[$jljd+1][0]){
			$jljd ++;
		}

		if(!isset($params[$jljd + 1])){
			$count = 0;
		}
		$procData['countDl'] = $count;
		$procData['save']  = true;

		return array('jljd'=>$jljd, 'procData'=>$procData, 'forceUpdate'=>true);
	}
	
	/**
	 * 资源点收获活动脚本
	 *
	 * @param unknown_type $playerInfo
	 * @param unknown_type $params
	 * @param unknown_type $procData
	 * @param unknown_type $jljd
	 * @param unknown_type $actParam
	 */
	public function harvestZyd($playerInfo, $params, $procData, $jljd, $actParam=null){
		$_actV = intval($actParam);
		$count = isset($procData['z_count']) ? $procData['z_count'] + $_actV : $_actV;
		
		if($count>=$params[$jljd+1][0]){
			$jljd++;
			$count -= $params[$jljd][0];
		}
		$procData['z_count'] = $count;
		
		return array('jljd'=>$jljd, 'procData'=>$procData, 'forceUpdate'=>true);
	}

	
	/**
	 * 逐鹿战斗脚本
	 *
	 * @param unknown_type $playerInfo
	 * @param unknown_type $params
	 * @param unknown_type $procData
	 * @param unknown_type $jljd
	 * @param unknown_type $actParam
	 */
	public function fightZlz($playerInfo, $params, $procData, $jljd, $actParam=null){
		$_actV = intval($actParam);
		$count = isset($procData['zlz_count']) ? $procData['zlz_count'] + $_actV : $_actV;
		
		if($count>=$params[$jljd+1][0]){
			$jljd++;
			$count -= $params[$jljd][0];
		}
		$procData['zlz_count'] = $count;
		
		return array('jljd'=>$jljd, 'procData'=>$procData, 'forceUpdate'=>true);
	}

	
	/**
	 * 逐鹿急行军脚本
	 *
	 * @param unknown_type $playerInfo
	 * @param unknown_type $params
	 * @param unknown_type $procData
	 * @param unknown_type $jljd
	 * @param unknown_type $actParam
	 */
	public function fightJXJ($playerInfo, $params, $procData, $jljd, $actParam=null){
		$_actV = intval($actParam);
		$count = isset($procData['jxj_count']) ? $procData['jxj_count'] + $_actV : $_actV;
		
		if($count>=$params[$jljd+1][0]){
			$jljd++;
			$count -= $params[$jljd][0];
		}
		$procData['jxj_count'] = $count;
		
		return array('jljd'=>$jljd, 'procData'=>$procData, 'forceUpdate'=>true);
	}

}