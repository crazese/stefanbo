<?php
class rankController {
	// 请求座次信息
	public static function qqzcxx($getInfo) {		
		$ret_val = rankModel::qqzcxx($getInfo);		
		ClientView::show($ret_val);
	}
	
	// 请求座次详情
	public static function qqzcxq($getInfo) {	
		$getInfo['rank'] = _get('zc');
		$ret_val = rankModel::qqzcxq($getInfo);		
		ClientView::show($ret_val);
	}
	
	// 请求座次战斗信息
	public static function qqzczd($getInfo) {	
		$getInfo['rank'] = _get('zc');
		$getInfo['hyp'] = _get('hyp');
		$ret_val = rankModel::qqzczd($getInfo);		
		ClientView::show($ret_val);
	}
	
	// 请求座次英雄榜
	public static function qqzcyxb($getInfo) {	
		$getInfo['rank'] = _get('zc');
		$getInfo['lx'] = _get('lx');
		$ret_val = rankModel::qqzcyxb($getInfo);		
		ClientView::show($ret_val);
	}
	
	// 请求英雄榜战斗数据回放
	public static function qqyxbzd($getInfo) {	
		$getInfo['rank'] = _get('zc');
		$getInfo['lx'] = _get('lx');
		$getInfo['mc'] = _get('mc');
		$getInfo['tzid'] = _get('tzid');
		$ret_val = rankModel::qqyxbzd($getInfo);		
		ClientView::show($ret_val);
	}
	
	// 请求获取座次提示
	public static function qqzctsxx($getInfo) {	
		$ret_val = rankModel::qqzctsxx($getInfo);		
		ClientView::show($ret_val);
	}
	
	//获取战功信息
	public static function hqzgxx($getInfo) {
		$playersid = $getInfo['playersid'];
		$page = _get('page');
		$ret_val = rankModel::hqzgxx($playersid,$page);
		ClientView::show($ret_val);
	}
}