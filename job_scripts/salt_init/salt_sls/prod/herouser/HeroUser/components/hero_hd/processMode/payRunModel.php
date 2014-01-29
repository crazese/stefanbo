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

class payRunModel{
	/**
	 * 充值活动
	 *
	 * @param unknown_type $playerInfo
	 * @param array $params				累计充值金额
	 * @param array $procData			充值活动数据
	 * @param unknown_type $jljd
	 * @param unknown_type $actId
	 * @param unknown_type $myActList
	 * @param mix $actParam				活动自定义参数
	 */
	public function endPay($playerInfo, $params, $procData, $jljd, $actParam=null){
		$payMoney = isset($procData['pay'])?$procData['pay']:0;
		$payMoney = is_null($actParam)?$payMoney:$payMoney + $actParam;

		if(isset($params[$jljd+1])&&$payMoney >= $params[$jljd+1][0]){
			$jljd++;
			$payMoney -= $params[$jljd][0];
		}

		$procData['pay'] = $payMoney;
		$procData['save']  = true;

		return array('jljd'=>$jljd, 'procData'=>$procData, 'forceUpdate'=>true);
	}

	/**
	 * 招募武将活动,元宝刷新
	 * @param array $params            array(0=>'num', 1=>'General name');
	 *
	 */
	public function inviteGen($playerInfo, $params, $procData, $jljd, $actParam=null){
		$_actV = intval($actParam);
		$num = isset($procData['fl_num']) ? $procData['fl_num'] + $_actV: $_actV;

		if(isset($params[$jljd+1])&&$num >= $params[$jljd+1][0]){
			$jljd++;
			$num -= $params[$jljd][0];
		}

		$procData['fl_num'] = $num;
		$procData['save'] = true;

		return array('jljd'=>$jljd, 'procData'=>$procData, 'forceUpdate'=>true);
	}
}