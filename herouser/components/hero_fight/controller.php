<?php
class fightController {
	//获取可攻击城池列表
	public static function getAttactCityList($getInfo) {
		$playersid = $getInfo['playersid'];
		$level = _get('level');
		$page = _get('page');
		$regionid = _get('regionId');
		if ($_SESSION['client'] == 1) {
			$showValue = fightModelJava::getAttactCityList($playersid,$level,$page,$regionid);
		} else {
			$showValue = fightModel::getAttactCityList($playersid,$level,$page,$regionid);
		}
		ClientView::show($showValue);
	}
	 
	//攻城战
	/*public static function attactCity($getInfo) {
		$playersid = $getInfo['playersid'];
		$defendPlayersid = _get('did');
		$isBack = _get('isBack');
		$msgID = _get('msgId');
		$cr = _get('cr');
		//$attactPlayersid,$defendPlayersid,$isBack=0,$msgID=0
		$showValue = fightModel::attactCity($playersid,$defendPlayersid,$isBack,$msgID,$cr);
		socialModel::addEnemy($defendPlayersid,$playersid);
		ClientView::show($showValue);	
	}*/

	//设置城池保护
	public static function setPeace($getInfo) {
		$playersid = $getInfo['playersid'];
		$showValue = fightModel::setPeace($playersid);
		//$showValue['rw'] = $pubVar['rw'];	
		ClientView::show($showValue);
	}
		
	//占领
	public static function occupy($getInfo) {
		$gid = _get('gid');
		$tuId = _get('tuId');
		$playersid = $getInfo['playersid'];
		$hyp = _get('hyp');
		$gjcl = _get('cl');
		if (empty($gjcl)) {
			$gjcl = 0;
		}		
		$showValue = fightModel::zl($playersid,$gid,$tuId,$hyp,$gjcl);
		//socialModel::addEnemy($tuId,$playersid);
		ClientView::show($showValue);
	}
	
	//请求留言内容
	public static function getTalk($getInfo) {
		$showValue = fightModel::getTalk();
		ClientView::show($showValue);
	}
	
	//占领后留言
	public static function setWord($getInfo) {
		$playersid = $getInfo['playersid'];
		$toplayersid = _get('jsid');
		$messageid = _get('mid');
		$mode = _get('mode');
		$oid = _get('oid');
		$msg = urldecode(_get('msg'));
		$showValue = fightModel::setWord($playersid,$toplayersid,$messageid,$msg,$mode,$oid);
		ClientView::show($showValue);
	}
    
	//自救
	public static function zj($getInfo) {
		$playersid = $getInfo['playersid'];
		$gid = _get('gid');	
		$gjcl = _get('cl');
		if (empty($gjcl)) {
			$gjcl = 0;
		}	
		$showValue = fightModel::self_help($playersid,$gid,$gjcl);
		ClientView::show($showValue);
	}	
	
	//救人
	public static function jbr($getInfo) {
		$playersid = $getInfo['playersid'];
		$gid = _get('gid');
		$tuId = _get('tuId');
		$gjcl = _get('cl');
		if (empty($gjcl)) {
			$gjcl = 0;
		}		
		$hyp = _get('hyp');	
		$showValue = fightModel::friend_help($playersid,$gid,$tuId,$gjcl,$hyp);
		ClientView::show($showValue);
	}

	// 请求闯关信息
	public static function qqcgxx($getInfo) {
		$playersid = $getInfo['playersid'];
		$showValue = fightModel::qqcgxx($playersid);
		ClientView::show($showValue);
	}
	
	// 请求小关信息
	public static function qqxgxx($getInfo) {
		$playersid = $getInfo['playersid'];
		$showValue = fightModel::qqxgxx($playersid);
		ClientView::show($showValue);
	}
	
	// 请求小关怪物信息
	public static function qqxggwxx($getInfo) {
		$playersid = $getInfo['playersid'];
		$showValue = fightModel::qqxggwxx($playersid);
		ClientView::show($showValue);
	}
	
	// 闯关
	public static function cg($getInfo) {
		$playersid = $getInfo['playersid'];
		$showValue = fightModel::cg($playersid);
		ClientView::show($showValue);
	}
		
	// 退出闯关
	public static function tccg($getInfo) {
		$playersid = $getInfo['playersid'];
		$showValue = fightModel::tccg($playersid);
		ClientView::show($showValue);
	}
	
	// 直捣黄龙
	public static function zdhl($getInfo) {
		$playersid = $getInfo['playersid'];
		$showValue = fightModel::zdhl($playersid);
		ClientView::show($showValue);
	}
	
	// 用江湖令增加闯关次数
	public static function zjcgcs($getInfo) {
		$nd = _get('nd');
		$dgbh = _get('dgbh');
		$playersid = $getInfo['playersid'];
		$showValue = fightModel::zjcgcs($playersid, $nd, $dgbh);
		ClientView::show($showValue);
	}
}