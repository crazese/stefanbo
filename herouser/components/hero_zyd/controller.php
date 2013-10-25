<?php
class zydController {
	//获取资源点列表
	public static function zydlb() {
		$tuid = _get('tuid');
		$type = _get('type');
		$dyc = _get('dyc');
		$result = zydModel::zydlb($tuid,$type,$dyc);
		ClientView::show($result);
	}
	//我方军情
	public static function wfjq($getInfo) {
		$playersid = $getInfo['playersid'];		
		$result = zydModel::hqjq($playersid,1);
		$result['fwqsj'] = time();
		ClientView::show($result);
	}
	//敌方军情
	public static function dfjq($getInfo) {
		$playersid = $getInfo['playersid'];
		$result = zydModel::hqjq($playersid,2);
		$result['fwqsj'] = time();
		ClientView::show($result);
	}
	//用元宝刷资源点
	public static function sxzyd($getInfo) {
		$playersid = $getInfo['playersid'];
		$result = zydModel::sxzyd($playersid);
		ClientView::show($result);
	}

	//资源点战斗、掠夺、中途返回
	public static function zydzd($getInfo) {
		$playersid = $getInfo['playersid'];
		$option = _get('type');
		$zydid = _get('zydid');
		$gid = _get('gid');
		$pid = _get('tuid');
		$result = zydModel::zydzd($playersid,$option,$zydid,$gid,$pid);
		ClientView::show($result);
	}
	
	//资源点撤防
	public static function zydcf($getInfo) {
		$playersid = $getInfo['playersid'];
		$zydid = _get('zydid');
		$gid = _get('gid');
		$pid = _get('tuid');
		$result = zydModel::zydcf($playersid,$zydid,$gid,$pid);
		ClientView::show($result);
	}
	
	//资源点详情
	public static function zydxq($getInfo) {
		$playersid = $getInfo['playersid'];
		$tuid = _get('tuid');
		$zydid = _get('zydid');
		$type = _get('type');
		$result = zydModel::zydxq($playersid,$tuid,$zydid,$type);
		ClientView::show($result);
	}
	
	//收取资源点收益
	public static function sqzydsy($getInfo) {
		$playersid = $getInfo['playersid'];
		$tuid = _get('tuid');
		$zydid = _get('zydid');
		$result = zydModel::sqzydsy($playersid,$tuid,$zydid);
		ClientView::show($result);
	}
	//立刻召回
	public static function ldzh($getInfo) {
		$gid = _get('gid');
		$zydid = _get('zydid');
		$tuid = _get('tuid');
		$playersid = $getInfo['playersid'];
		$result = zydModel::ldzh($playersid,$gid,$zydid,$tuid);
		ClientView::show($result);
	}
	
	//急行军
	public static function jxj($getInfo) {
		$playersid = $getInfo['playersid'];
		$gid = _get('gid');
		$result = zydModel::jxj($playersid,$gid);
		ClientView::show($result);
	}	
	
	//逐鹿资源点详情
	public static function zlzydxq() {
		$zydid = _get('zydid');
		$result = zydModel::zlzydxq($zydid);
		ClientView::show($result);
	}	
	
	//获取逐鹿资源点状态
	public static function zlzydzt() {
		$result = zydModel::zlzydzt();
		ClientView::show($result);
	}	
	
	//获取英雄榜
	public static function yxb() {
		$zydid = _get('zydid');
		$result = zydModel::yxb($zydid);
		ClientView::show($result);		
	}
	
	//获取攻击队列
	public static function zlzydgjdl() {
		$zydid = _get('zydid');
		$result = zydModel::zlzydgjdl($zydid);
		ClientView::show($result);
	}
	//获取逐鹿战状况信息
	public static function zlzydzkxx($getInfo)	{
		$playersid = $getInfo['playersid'];
		$zydid = _get('zydid');
		$result = zydModel::zlzydzkxx($zydid,$playersid);
		ClientView::show($result);
	}
	
	//zydModel::zydjs();
	public static function zydjs(){
		zydModel::zydjs();
	}
}
