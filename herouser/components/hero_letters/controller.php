<?php
class lettersController {	
	//同意历练
	function agreePractice($lettersInfo) {
		global $common;
		$xxlx = _get('xxlx');
		$page = _get('page');
		$returnValue = lettersModel::agreePractice($lettersInfo,$xxlx,$page);			
		ClientView::show($returnValue);
	}
	
	//同意回礼
	function agreeGift($lettersInfo) {
		global $common;
		$xxlx = _get('xxlx');
		$page = _get('page');
		$returnValue = lettersModel::agreeGift($lettersInfo,$xxlx,$page);			
		ClientView::show($returnValue);
	}
	
	//礼物领取
	function takeGift($lettersInfo) {
		global $common,$mc;
		$sltype = _get('sltype');
		$xxlx = _get('xxlx');
		$page = _get('page');
		$returnValue = lettersModel::takeGift($lettersInfo,$sltype,$xxlx,$page);			
		ClientView::show($returnValue);
	}
	
	//删除信息
	function deleteLetters($lettersInfo) {
		global $common;
		$xxid = _get('lettersid');
		$page = _get('page');
		$xxlx = _get('xxlx');
		$playersid = $lettersInfo['playersid'];
		$returnValue = lettersModel::deleteLetters($xxid,$playersid,$page,$xxlx);
		ClientView::show($returnValue);
	}
	
	//消息列表
	function getMessageList($lettersInfo) {
		global $common,$db;
		
		$zt = _get('zt');
		$zt = isset($zt) ? $zt : 0;
		$page = _get('page');
		$page = isset($page) ? $page : 1;		// 当前页面号
		
		$returnValue = lettersModel::getMessageList($lettersInfo['playersid'], $zt, $page);
		ClientView::show($returnValue);
	}
	
	//检查消息状态
	function checkMsg($lettersInfo) {
		$playersid = $lettersInfo['playersid'];
		
		$showValue = lettersModel::publicParameters($playersid);
		ClientView::show($showValue);
	}
	
	/**
	 * 2.8.7	领取系统日志物品
	 *
	 * @param array $lettersInfo
	 */
	public function lqxtriwp($lettersInfo){
		$playersid = $lettersInfo['playersid'];
		$lettersid = _get('lettersid');
		$xxlx      = _get('xxlx');
		$page      = _get('page');
		$returnValue = lettersModel::lqxtriwp($playersid, $lettersid, $xxlx, $page);
		ClientView::show($returnValue);
	}
	
	/**
	 * 2.8.7	请求战斗回放
	 *
	 * @param unknown_type $lettersInfo
	 */
	public function qqzdhf($lettersInfo){
		$playersid = $lettersInfo['playersid'];
		$lettersid = _get('lettersid');
		
		$returnValue = lettersModel::qqzdhf($playersid, $lettersid);
		ClientView::show($returnValue);
	}
	
	/**
	 * 2.8.8	领取所有赠送给我的礼物
	 *
	 * @param unknown_type $lettersInfo
	 */
	public function lqsylw($lettersInfo){
		$playersid = $lettersInfo['playersid'];
		
		$returnValue = lettersModel::lqsylw($playersid);
		ClientView::show($returnValue);
	}
	
	/**
	 * 2.8.9	回赠所有送我礼物的人礼物
	 *
	 * @param unknown_type $lettersInfo
	 */
	public function hzsylw($lettersInfo){
		$playersid = $lettersInfo['playersid'];
		
		$returnValue = lettersModel::hzsylw($playersid);
		ClientView::show($returnValue);
	}
	
	/**
	 * 2.8.11	向所有索要礼物的人赠送礼物
	 *
	 * @param unknown_type $lettersInfo
	 */
	public function zssylw($lettersInfo){
		$playersid = $lettersInfo['playersid'];
		
		$returnValue = lettersModel::zssylw($playersid);
		ClientView::show($returnValue);
	}
	
	/**
	 * 2.8.12 同意收到的所有加好友请求
	 *
	 * @param unknown_type $lettersInfo
	 */
	public function jssyhyqq($lettersInfo){
		$playersid = $lettersInfo['playersid'];
		
		$returnValue = lettersModel::jssyhyqq($playersid);
		ClientView::show($returnValue);
	}
	
	/**
	 * 2.8.13 信件批量删除
	 *
	 * @param array $lettersInfo
	 */
	public function sczdlxtb($lettersInfo){
		$playersid = $lettersInfo['playersid'];
		$lx = _get('lx');
		
		$returnValue = lettersModel::sczdlxtb($playersid, $lx);
		ClientView::show($returnValue);
	}
	
	public function bzsyll($lettersInfo) {
		$playersid = $lettersInfo['playersid'];
		
		$returnValue = lettersModel::bzsyll($playersid);
		ClientView::show($returnValue);
	}
}