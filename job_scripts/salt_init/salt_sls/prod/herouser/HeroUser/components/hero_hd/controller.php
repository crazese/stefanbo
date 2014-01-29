<?php
class hdController {
	
	// gzp的方法，目前没有使用
	public static function onFinishHD($roleInfo,$wctj) {
		$hqhddata = gethdinfo::hqhddata();
		if (empty($hqhddata)) {
			return false;
		} else {
			foreach ($hqhddata as $hqhddataKey => $hqhddataValue) {
				if ($hqhddataValue['wctj'] == $wctj) {
					$class = $hqhddataValue['jblx'];
					$function = 'function_'.$hqhddataValue['hdjb'];
					//$result = $class::$function($roleInfo,$hqhddataKey,$hqhddata);
				    if (!empty($result)) {
				    	break;
				    }
				}
			}
		}
	}
	
	/**
	 * 2.15.1	获取活动列表
	 *
	 * @param array $getInfo
	 */
	public static function hqhdlb($getInfo){
		$playersid = $getInfo['playersid'];
		$returnValue = hdModel::hqhdlb($playersid);
		ClientView::show($returnValue);
	}
	
	/**
	 * 2.15.2	领取活动奖励
	 *
	 * @param array $getInfo
	 */
	public static function lqhdjl($getInfo){
		$playersid = $getInfo['playersid'];
		$hdid      = _get('hdid');
		$returnValue = hdModel::lqhdjl($hdid, $playersid);
		ClientView::show($returnValue);
	}
	
	/**
	 * 2.15.2	领取活动奖励
	 *
	 * @param array $getInfo
	 */
	public static function dhdj($getInfo){
		$playersid = $getInfo['playersid'];
		$exid      = _get('exid');
		$returnValue = hdModel::dhdj($exid, $playersid);
		ClientView::show($returnValue);
	}
	
	/**
	 * 2.16.4	发评分奖励探报
	 *
	 * @param array $getInfo
	 */
	public static function lqpfjl($getInfo){
		$playersid = $getInfo['playersid'];		
		$returnValue = hdModel::lqpfjl($playersid);
		ClientView::show($returnValue);
	}
}
