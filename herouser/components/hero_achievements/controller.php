<?php
class achievementsController{
	//获取大类
	public static function hqcjdlxx($getInfo) {
		$playersid = $getInfo['playersid'];
		$ret_val = achievementsModel::hqcjdlxx($playersid);
		ClientView::show($ret_val);
	}
	//获取小类
	public static function  hqcjxlxx($getInfo) {
		$playersid = $getInfo['playersid'];
		$cjdlbh = _get('cjdlbh') + 1;
		$ret_val = achievementsModel::hqcjxlxx($playersid,$cjdlbh);
		ClientView::show($ret_val);
	}
	//分享成就
	public static function hqcjxlfx($getInfo) {
		$playersid = $getInfo['playersid'];
		$cjxlbh = _get('cjxlbh');
		$ret_val = achievementsModel::hqcjxlfx($playersid,$cjxlbh);
		ClientView::show($ret_val);		
	}	
}