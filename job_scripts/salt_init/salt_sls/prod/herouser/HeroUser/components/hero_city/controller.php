<?php
class cityController {
	//升级
	public static function upgradeBuild($getInfo) {
		$getInfo ['buildId'] = _get ( 'buildId' );
		$result = cityModel::upgradeBuilding ( $getInfo );
		ClientView::show ( $result );
	}
	
	//挖矿
	public static function ksck($getInfo) {
		$playersid = $getInfo ['playersid'];
		$result = cityModel::ksck ( $playersid );
		ClientView::show ( $result );
	}
	
	//偷矿
	public static function zlck($getInfo) {
		$playersid = $getInfo ['playersid'];
		$tuid = _get ( 'tuid' );
		$result = cityModel::zlck ( $playersid, $tuid );
		ClientView::show ( $result );
	}
	
	//征收资源
	public static function sczs($getInfo) {
		$getInfo ['hyb'] = _get ( 'hyb' );
		$showValue = cityModel::resourceCollection ( $getInfo );
		ClientView::show ( $showValue );
	}
	
	// 提升银库容量
	public static function tsykrl($getInfo) {
		$showValue = cityModel::tsykrl ( $getInfo );
		ClientView::show ( $showValue );
	}
	
	//城市基本信息
	public static function getCityInfo($getInfo) {
		if ($_SESSION ['client'] == 1) {
			$showValue = cityModelJava::getCityBaseInfo ( $getInfo );
		} else {
			$showValue = cityModel::getCityBaseInfo ( $getInfo );
		}
		ClientView::show ( $showValue );
	}
	
	//获取其它玩家信息
	public static function qqwjccxx() {
		$tuid = _get ( 'tuid' );
		$type = _get ( 'type' );
		$showValue = cityModel::qqwjccxx ( $tuid, $type );
		ClientView::show ( $showValue );
	}
	
	//获取将领列表数据
	public static function getGeneralInfo($getInfo) {
		$showValue = cityModel::getGeneralList ( $getInfo );
		ClientView::show ( $showValue );
	}
	
	//获取可招募将领数据
	public static function getHireGeneralInfo($getInfo) {
		$tuid = _get ( 'tuid' );
		if (! empty ( $tuid )) {
			$playerid = $tuid;
		} else {
			$playerid = $getInfo ['playersid'];
		}
		$gx = _get ( 'gx' );
		$sid = $getInfo ['playersid'];
		$rows = '';
		$showValue = cityModel::getHireGeneralInfo ( $playerid, $rows, $gx, $sid );
		ClientView::show ( $showValue );
	}
	
	//招募将领
	public static function hireGeneral($getInfo) {
		$showValue = cityModel::hireGeneral ( $getInfo );
		ClientView::show ( $showValue );
	}
	
	//设置将领排序
	public static function setGeneralOrder($getInfo) {
		$showValue = cityModel::setGeneralSort ( $getInfo );
		ClientView::show ( $showValue );
	}
	
	//快速刷新可招募将领列表
	public static function quickUpdateHireGeneral($getInfo) {
		$type = _get ( 'type' ) + 1;
		$djid = _get ( 'djid' );
		$showValue = cityModel::quickUpdateHireGeneral ( $getInfo, $type, $djid );
		ClientView::show ( $showValue );
	}
	
	//获取配兵tab将领数据
	public static function getGeneralListForSetArmy($getInfo) {
		$showValue = cityModel::getGeneralListForSetArmy ( $getInfo );
		//$showValue['rw'] = $pubVar['rw'];       
		ClientView::show ( $showValue );
	}
	
	//快速补兵
	public static function quickAddTroops($getInfo) {
		$getInfo ['groupId'] = _get ( 'groupId' );
		$id = _get ( 'gid' );
		$showValue = genModel::quickAddTroops ( $getInfo, $id );
		//$showValue['rw'] = $pubVar['rw'];    	
		ClientView::show ( $showValue );
	}
	
	//设置将领攻防状态
	public static function setADstatus($getInfo) {
		$playersid = $getInfo ['playersid'];
		$targetStatus = _get ( 'targetStatus' );
		$showValue = cityModel::setADstatus ( $playersid, $targetStatus );
		$pubVar = lettersModel::publicParameters ( $getInfo ['playersid'] );
		$showValue ['task'] = $pubVar ['task'];
		$showValue ['mStatus'] = $pubVar ['mStatus'];
		ClientView::show ( $showValue );
	}
	
	//变更将领兵种
	public static function replaceSoldier($getInfo) {
		$playersid = $getInfo ['playersid'];
		$soldierId = _get ( 'soldierId' );
		$generalId = _get ( 'generalId' );
		$showValue = cityModel::replaceSoldier ( $playersid, $soldierId, $generalId );
		$pubVar = lettersModel::publicParameters ( $getInfo ['playersid'] );
		$showValue ['task'] = $pubVar ['task'];
		$showValue ['mStatus'] = $pubVar ['mStatus'];
		//$showValue['rw'] = $pubVar['rw'];   	
		ClientView::show ( $showValue );
	}
	
	//将领补兵
	public static function addedSoldiers($getInfo) {
		$playersid = $getInfo ['playersid'];
		$generalId = _get ( 'generalId' );
		$showValue = cityModel::addedSoldiers ( $playersid, $generalId );
		ClientView::show ( $showValue );
	}
	
	//解雇武将
	public static function fireGerneral($getInfo) {
		$playersid = $getInfo ['playersid'];
		$generalId = _get ( 'generalId' );
		$socialInfo = array ('playersid' => $playersid, 'generalid' => $generalId );
		socialModel::cancelPractice ( $socialInfo );
		$showValue = genModel::tqwh ( $playersid, $generalId );
		ClientView::show ( $showValue );
	}
	
	//货币兑换
	public static function exchange($getInfo) {
		$playersid = $getInfo ['playersid'];
		$rId = _get ( 'rId' );
		$rAm = _get ( 'tAm' );
		$tId = _get ( 'tId' );
		$showValue = cityModel::exchange ( $playersid, $rId, $rAm, $tId );
		ClientView::show ( $showValue );
	}
	
	//设置驻防
	public static function szzf($getInfo) {
		$gid = _get ( 'gid' );
		$playersid = $getInfo ['playersid'];
		$showValue = genModel::setZf ( $playersid, $gid );
		$showValue ['rsn'] = intval ( _get ( 'ssn' ) );
		ClientView::show ( $showValue );
	}
		
	//取消占领
	public static function qxzl($getInfo) {
		$gid = _get ( 'gid' );
		$playersid = $getInfo ['playersid'];
		$tuId = _get ( 'tuid' );
		$hyp = _get ( 'hyp' );
		$showValue = genModel::cancelZl ( $gid, $playersid, $tuId, $hyp );
		ClientView::show ( $showValue );
	}
	
	//收取占领或者驻守收益
	public static function sqsy($getInfo) {
		$playersid = $getInfo ['playersid'];
		$gid = _get ( 'gid' );
		$pid = _get ( 'pid' );
		$showValue = cityModel::sqsy ( $playersid, $gid, $pid );
		ClientView::show ( $showValue );
	}
	
	//获取自己所有武将驻守占领数据
	public static function sywjzlsj($getInfo) {
		$playersid = $getInfo ['playersid'];
		$showValue = cityModel::hqjlzlsj ( $playersid );
		ClientView::show ( $showValue );
	}
	
	//设置将领出征
	public static function szcz($getInfo) {
		$playersid = $getInfo ['playersid'];
		$intID = _get ( 'gid' );
		$showValue = genModel::setGeneralFight ( $playersid, $intID );
		//$showValue['rsn'] = intval(_get('ssn'));
		ClientView::show ( $showValue );
	}
	
	//请求取消出征
	public static function qxszcz($getInfo) {
		$playersid = $getInfo ['playersid'];
		$intID = _get ( 'gid' );
		$showValue = genModel::setGeneralFree ( $playersid, $intID );
		ClientView::show ( $showValue );
	}
	
	//武将穿戴装备
	public static function wjzb($getInfo) {
		$playersid = $getInfo ['playersid'];
		$gid = _get ( 'gid' );
		$zb1 = '1_' . _get ( 'zb1' );
		$zb2 = '2_' . _get ( 'zb2' );
		$zb3 = '3_' . _get ( 'zb3' );
		$zb4 = '4_' . _get ( 'zb4' );
		$mzsl = _get('mz');
		$showValue = genModel::wjzb ( $playersid, $gid, $zb1, $zb2, $zb3, $zb4, $mzsl );
		ClientView::show ( $showValue );
	}
	
	//武将合成
	public static function wjhc($getInfo) {
		$playersid = $getInfo ['playersid'];
		$mid = _get ( 'mid' );
		$sid = _get ( 'sid' );
		$hyb = _get ( 'hyb' );
		$showValue = genModel::wjhc ( $playersid, $mid, $sid, $hyb );
		ClientView::show ( $showValue );
	}
	
	//武将训练位信息
	public static function wjxlxx($getInfo) {
		$playersid = $getInfo ['playersid'];
		$showValue = genModel::wjxlxx ( $playersid );
		ClientView::show ( $showValue );
	}
	
	//获取武将的训练信息
	public static function hqwjxlxx($getInfo) {
		$playersid = $getInfo ['playersid'];
		$gid = _get ( 'gid' );
		$showValue = genModel::hqwjxlxx ( $playersid, $gid );
		ClientView::show ( $showValue );
	}
	
	//开启武将训练位
	public static function kqxlw($getInfo) {
		$playersid = $getInfo ['playersid'];
		$type = _get ( 'type' );
		//$djid = _get('djid');
		//$bh = _get ( 'bh' );
		$showValue = genModel::openxlw ( $playersid, $type );
		ClientView::show ( $showValue );
	}
	
	//训练武将
	public static function xlwj($getInfo) {
		$playersid = $getInfo ['playersid'];
		$gid = _get ( 'gid' );
		$yb = _get ( 'yb' );
		$xlsc = _get ( 'sc' );
		$px = _get ( 'xlwbh' );
		$showValue = genModel::wjxl ( $playersid, $gid, $yb, $xlsc, $px );
		ClientView::show ( $showValue );
	}
	
	//完成训练武将
	public static function wcxl($getInfo) {
		$playersid = $getInfo ['playersid'];
		$gid = _get ( 'gid' );
		$yp = _get ( 'yp' );
		$showValue = genModel::wcxl ( $playersid, $gid, $yp );
		ClientView::show ( $showValue );
	}
	
	//获取武将合成信息
	public static function wjhcxx($getInfo) {
		$playersid = $getInfo ['playersid'];
		$gid = _get ( 'gid' );
		$showValue = genModel::wjhcxx ( $playersid, $gid );
		ClientView::show ( $showValue );
	}
	
	//秒历练CD
	public static function mllcd($getInfo) {
		$playersid = $getInfo ['playersid'];
		$gid = _get ( 'gid' );
		$showValue = genModel::mllcd ( $playersid, $gid );
		ClientView::show ( $showValue );
	}
	
	//补齐升级所需材料
	public static function bqsjcl($getInfo) {
		$playersid = $getInfo ['playersid'];
		$buildId = _get ( 'buildId' );
		$showValue = cityModel::bqsjcl ( $playersid, $buildId );
		ClientView::show ( $showValue );
	}
	
	//获取武将历练冷却时间
	public static function lllqsh($getInfo) {
		$playersid = $getInfo ['playersid'];
		$gid = _get ( 'gid' );
		$showValue = genModel::lllqsh ( $playersid, $gid );
		ClientView::show ( $showValue );
	}
	
	/**
	 * @author kknd li
	 * 获取VIP信息接口
	 * @param array $getInfo
	 */
	public static function vipxx($getInfo) {
		$playersid = $getInfo ['playersid'];
		$uid = _get ( 'userId' );
		
		$showValue = cityModel::vipxx ( $playersid, $uid );
		ClientView::show ( $showValue );
	}
	
	/**
	 * @author kknd li
	 * 玩家开自己的礼包接口
	 * @param array $getInfo
	 */
	public static function kzjlb($getInfo) {
		$playersid = $getInfo ['playersid'];
		$lblx = _get ( 'lblx' );
		$showValue = cityModel::openGemBox ( $playersid, $lblx );
		ClientView::show ( $showValue );
	}
	
	// 获取每日奖励列表
	public static function 
	hqmrjllb($getInfo) {
		$playersid = $getInfo ['playersid'];
		$roleInfo = array('playersid'=>$playersid);
		roleModel::getRoleInfo($roleInfo);
		$showValue = cityModel::getGemBoxInfo ( $roleInfo );		
		$retValue['bxlist'] = array_values($showValue);
		$retValue['status'] = 0;
		ClientView::show ( $retValue );
	}
	
	
	//获取单个武将信息
	public static function hqdgwj() {
		$tuid = _get ( 'tuid' );
		$gid = _get ( 'gid' );
		if (empty ( $gid )) {
			$gid = '*';
		}
		$showValue = cityModel::hqdgwj ( $tuid, $gid );
		ClientView::show ( $showValue );
	}
	//获取技能升级信息
	public static function hqjnsjxx($getInfo) {
		$playersid = $getInfo ['playersid'];
		$gid = _get ( 'gid' );
		$jnid = _get ( 'jnid' );
		$showValue = cityModel::hqjnsjxx ( $playersid, $gid, $jnid );
		ClientView::show ( $showValue );
	}
	// 设置防守策略
	public static function szgfcl($getInfo) {
		$playersid = $getInfo ['playersid'];
		$st = _get ( 'st' );
		$tuid = _get ( 'tuid' );
		$showValue = genModel::szfscl ( $playersid, $tuid, $st );
		ClientView::show ( $showValue );
	}
	// 使用侦查功能
	public static function zc($getInfo) {
		$playersid = $getInfo ['playersid'];
		$tuid = _get ( 'tuid' );
		$showValue = cityModel::zc ( $playersid, $tuid );
		ClientView::show ( $showValue );
	}
	//秒征税CD
	public static function mzscd($getInfo) {
		$playersid = $getInfo ['playersid'];
		$showValue = cityModel::mzscd ( $playersid );
		ClientView::show ( $showValue );
	}
	
	//查看技能信息
	public static function ckjnxx() {
		$jndj = _get ( 'jndj' );
		$jnid = _get ( 'jnid' );
		$showValue = cityModel::ckjnxx ( $jndj, $jnid );
		ClientView::show ( $showValue );
	}
	
	//写留言
	public static function wjly($getInfo) {
		$msg = _get ( 'msg' );
		$msg = urldecode ( $msg );
		$tuid = _get ( 'tuid' );
		$playersid = $getInfo ['playersid'];
		$lylx = _get('hf');
		$showValue = cityModel::wjly ( $msg, $tuid, $playersid, $lylx );
		ClientView::show ( $showValue );
	}
	
	//获取留言
	public static function hqly() {
		$ym = _get ( 'ym' );
		$tuid = _get ( 'tuid' );
		$showValue = cityModel::hqly ( $ym, $tuid );
		ClientView::show ( $showValue );
	}
	
	// 探宝信息
	public static function tbxx($getInfo) {
		$playersid = $getInfo ['playersid'];
		$showValue = cityModel::tbxx ( $playersid );
		ClientView::show ( $showValue );
	}
	
	// 探宝
	public static function tb($getInfo) {
		$playersid = $getInfo ['playersid'];
		$showValue = cityModel::tb ( $playersid );
		ClientView::show ( $showValue );
	}
	
	// 探宝见好就收
	public static function tbjhjs($getInfo) {
		$playersid = $getInfo ['playersid'];
		$showValue = cityModel::tbjhjs($playersid);
		ClientView::show ($showValue);
	}
	
	// 充值成功增加元宝
	public static function czcgjyb($getInfo) {
		$playersid = $getInfo ['playersid'];
		$showValue = cityModel::czcgjyb ( $playersid );
		ClientView::show ( $showValue );
	}
	
	// 发送聊天消息
	public static function fsltxx($getInfo) {
		$nr = _get('nr');
		$playersid = $getInfo ['playersid'];
		$ltlx = _get('lx');
		$ym = _get('ym');		
		$showValue = cityModel::fsltxx ( $nr, $playersid, $ltlx, $ym );
		ClientView::show ( $showValue );
	}
	
	// 获取重名玩家信息列表
	public static function hqltcmxx($getInfo) {
		$name = _get('mc');
		$playersid = $getInfo ['playersid'];		
		$page = _get('page');		
		$showValue = cityModel::hqltcmxx( $name, $playersid, $page );
		ClientView::show ( $showValue );
	}
	
	// 获取好友聊天列表
	public static function hqlthyxx($getInfo) {		
		$playersid = $getInfo ['playersid'];		
		$page = _get('page');		
		$showValue = cityModel::hqlthyxx( $playersid, $page );
		ClientView::show ( $showValue );
	}
	
	// 获取聊天记录
	public static function ltjl($getInfo) {
		$page = _get('ym');
		$ltlx = _get('lx');
		$playersid = $getInfo ['playersid'];
		$showValue = cityModel::ltjl ( $page, $playersid, $ltlx );
		ClientView::show ( $showValue );
	}
	
	// 屏蔽玩家聊天
	public static function pbltwj($getInfo) {
		$page = _get('ym');
		$ltlx = _get('lx');
		$tuid = _get('tuid');
		$playersid = $getInfo ['playersid'];
		$showValue = cityModel::pbltwj( $page, $playersid, $ltlx, $tuid );
		ClientView::show ( $showValue );
	}
	
	// 聊天举报
	public static function jbltxx($getInfo) {
		$pid = _get('pid');
		$nr = _get('nr');
		$lx = _get('lx');
		$playersid = $getInfo ['playersid'];
		$showValue = cityModel::jbltxx( $playersid, $pid, $nr, $lx );
		ClientView::show ( $showValue );
	}

	// 开启私聊
	public static function slkqxx($getInfo) {
		$pid = _get('pid');		
		$playersid = $getInfo ['playersid'];
		$showValue = cityModel::slkqxx( $playersid, $pid );
		ClientView::show ( $showValue );
	}
	
	// 获取聊天屏蔽列表每页5个
	public static function hqltpb($getInfo) {
		$page = _get('ym');
		$playersid = $getInfo ['playersid'];
		$showValue = cityModel::hqltpb( $playersid, $page );
		ClientView::show ( $showValue );
	}	

	// 取消聊天屏蔽
	public static function qxltpb($getInfo) {
		$lx = _get('lx');
		if($lx == 0) {
			$tuid = _get('tuid');
			$ym = _get('ym');
		} else {
			$tuid = null;
			$ym = null;		
		}
		$playersid = $getInfo ['playersid'];
		$showValue = cityModel::qxltpb( $playersid, $lx, $tuid, $ym );
		ClientView::show ( $showValue );
	}	
	
	// 培养武将
	public static function pywj($getInfo) {
		$gid = _get('gid');
		$pyfs = _get('type');
		$playersid = $getInfo ['playersid'];
		$showValue = genModel::pywj($playersid,$gid,$pyfs);
		ClientView::show ( $showValue );
	}
	
	// 保存培养
	public static function bcpy($getInfo) {
		$gid = _get('gid');
		$playersid = $getInfo ['playersid'];
		$showValue = genModel::bcpy($playersid,$gid);
		ClientView::show ( $showValue );
	}

	//提升继承比例
	public static function tsjcbl($getInfo) {
		$playersid = $getInfo ['playersid'];
		$yjjc = _get('yjjc');
		$showValue = genModel::tsjcbl($playersid,$yjjc);
		ClientView::show ( $showValue );
	}

	//确定继承
	public static function wjjc($getInfo) {
		$playersid = $getInfo ['playersid'];
		$ccgid = _get('ccgid');
		$jcgid = _get('jcgid');
		$showValue = genModel::sxcc($playersid,$ccgid,$jcgid);
		ClientView::show ( $showValue );
	}

	//获取屏蔽留言玩家列表
	public static function hqlypb($getInfo) {
		$playersid = $getInfo ['playersid'];
		$page = _get('ym');
		$showValue = cityModel::hqlypb($playersid,$page);
		ClientView::show ( $showValue );		
	}
	
	//删除留言
	public static function scly($getInfo) {
		$playersid = $getInfo ['playersid'];
		$lx = _get('lx');
		$tuid = _get('tuid');
		$page = _get('ym');
		$showValue = cityModel::scly($playersid, $lx, $tuid, $page);
		ClientView::show ( $showValue );
	}
	
	//屏蔽玩家
	public static function pbly($getInfo) {
		$playersid = $getInfo ['playersid'];
		$lx = 3;
		$tuid = _get('tuid');
		$page = 1;
		$showValue = cityModel::scly($playersid, $lx, $tuid, $page);
		ClientView::show ( $showValue );
	}

	//取消屏蔽
	public static function qxlypb($getInfo) {
		$playersid = $getInfo ['playersid'];
		$lx = _get('lx');
		$tuid = _get('tuid');
		$page = _get('ym');
		$showValue = cityModel::qxlypb($playersid,$lx,$tuid,$page);
		ClientView::show ( $showValue );
	}
	
	// 连续登陆奖励
	public static function hqqrjl($getInfo){
		$playersid = $getInfo ['playersid'];
		$lx = _get('lx');		
		$showValue = cityModel::getLoginAward($playersid,$lx);
		ClientView::show ( $showValue );
	}

	// 获取排名
	public static function hqpm($getInfo){
		$playersid = $getInfo['playersid'];
		$type = _get('type');
		$page = _get('page');
		$me   = _get('me');

		$showValue = cityModel::showPm($playersid, $type, $page, $me);
		ClientView::show($showValue);
	}
	
	//快速提取武魂
	public static function kstqwh($getInfo) {
		$playersid = $getInfo ['playersid'];
		$tuid = _get('tuid');
		$generalId = _get('generalId');
		$showValue = genModel::kstqwh($playersid, $tuid, $generalId);
		ClientView::show ( $showValue );		
	}
	
	//评分奖励
	public static function sdkpf($getInfo) {
		$playersid = $getInfo ['playersid'];
		$jcid = _get('jcid');
		$showValue = cityModel::sdkpf($playersid, $jcid);
		ClientView::show ( $showValue );		
	}	
}
