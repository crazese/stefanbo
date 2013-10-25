<?php
class chongzhiController {
	// 充值
	public static function dycz($getInfo) {		
		if(INGAME_PAY_WAY == 1) { // 易宝
			$czfs = _get('czfsid');
			$czme = _get('je');
			$czkh = _get('kh');
			$czkmm = _get('mm');				
			$ret_val = chongzhiModel::dycz($getInfo['userid'], $getInfo['playersid'], $czfs, $czme, $czkh, $czkmm);
		} else { // 神州付
			$getInfo['payMoney'] = _get('je');
			if(_get('czfsid') == 'SZX') $czfsid = 0;
			else if(_get('czfsid') == 'UNICOM') $czfsid = 1;
			else $czfsid = 2;
			$getInfo['cardType'] = $czfsid;
			//$getInfo['userid'] = $czfsid;
			$getInfo['cardId'] = _get('kh');
			$getInfo['cardPW'] = _get('mm');
			$getInfo['cardMoney'] = _get('je');
			
			$ret_val = chongzhiModel::dyszfcz($getInfo);
		}		
			ClientView::show($ret_val);
	}

	// 获取端游充值方式
	public static function dyczfs($getInfo) {		
			$ret_val = chongzhiModel::dyczfs();		
			ClientView::show($ret_val);
	}
	
	// 获取充值记录
	public static function dyczjl($getInfo) {
		$page = _get('ym');
		if(INGAME_PAY_WAY == 1) { // 易宝		
			$ret_val = chongzhiModel::dyczjl($getInfo['playersid'], $page);
		} else { // 神州付			
			$ret_val = chongzhiModel::dyszfczjl($getInfo['playersid'], $page);
		}
		ClientView::show($ret_val);
		
	}
	
	//uc客户端接口
	public static function ucpayid() {
			$ret_val = chongzhiModel::ucpayid();		
			ClientView::show($ret_val);		
	}
	
    //获取苹果商店产品信息
	public static function iosczcplb() {
		$sfgq = _get('type');
		$ret_val = chongzhiModel::iosczcplb($sfgq);		
		ClientView::show($ret_val);				
	}
	
	//获取ios端游充值记录
	public static function iosczjl() {
		$curPage = _get('ym');
		$czly = _get('czly');
		$ret_val = chongzhiModel::iosczjl($curPage,$czly);		
		ClientView::show($ret_val);				
	}
	
	
	// uc页游支付申请
	public static function uczfsq($getInfo) {
		$je = _get('czje');
		$ret_val = chongzhiModel::uczfsq($getInfo['playersid'], $je);
		ClientView::show($ret_val);
	}
	
	
	// 神州付充值
	public static function dyszfcz($getInfo) {
		$getInfo['payMoney'] = _get('je');
		if(_get('czfsid') == 'SZX') $czfsid = 0;
		else if(_get('czfsid') == 'UNICOM') $czfsid = 1;
		else $czfsid = 2;
		$getInfo['cardType'] = $czfsid;
		//$getInfo['userid'] = $czfsid;
		$getInfo['cardId'] = _get('kh');
		$getInfo['cardPW'] = _get('mm');
		$getInfo['cardMoney'] = _get('je');
	
		$ret_val = chongzhiModel::dyszfcz($getInfo);
		ClientView::show($ret_val);
	}
	
	
	// 获取神州付充值记录
	public static function dyszfczjl($getInfo) {		
		$page = _get('ym');
		$ret_val = chongzhiModel::dyszfczjl($getInfo['playersid'], $page);		
		ClientView::show($ret_val);
	}

	//hqwbxx($playersid, $session_key, $amount) 获取微博支付信息
	public static function hqwbxx($getInfo) {
		$playersid = $getInfo['playersid'];
		$session_key = _get('session_key');
		$amount = _get('amount');
		$ret_val = chongzhiModel::hqwbxx($playersid, $session_key, $amount);
		ClientView::show($ret_val);
	}	
	
	// 宜搜页游支付申请
	public static function yszfsq($getInfo) {		
		$czme = _get('czje');
		$ret_val = chongzhiModel::yszfsq($getInfo['playersid'], $czme);
	
		ClientView::show($ret_val);
	}

	// 充值绑定手机号
	public static function bdsj($getInfo){
		$phone = _get('code');
		$ret_val = chongzhiModel::bdsj($getInfo['playersid'], $phone);
		ClientView::show($ret_val);
	}
	//短信充值
	public static function dxcz($getInfo) {
		$playersid = $getInfo['playersid'];
		$prov = _get('prov');
		$city = urlencode(_get('city'));
		$ms = _get('ms');
		$qid = 125;
		$serviceid = '000060207134';
		$ext = '01';
		$ret_val = chongzhiModel::dxcz($playersid,$prov,$city,$ms,$qid,$serviceid,$ext);	
		ClientView::show($ret_val);
	}	
}