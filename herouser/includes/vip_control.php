<?php
/**
 * 请自己包含config.php
 * 
 */
$componentPath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR;
include_once($componentPath.'hero_city'.DIRECTORY_SEPARATOR.'model.php');
include_once($componentPath.'hero_city'.DIRECTORY_SEPARATOR.'var.php');
include_once($componentPath.'hero_quests'.DIRECTORY_SEPARATOR.'script.php');
include_once($componentPath.'hero_quests'.DIRECTORY_SEPARATOR.'controller.php');
include_once($componentPath.'hero_role'.DIRECTORY_SEPARATOR.'model.php');
include_once($componentPath.'hero_social'.DIRECTORY_SEPARATOR.'model.php');
include_once($componentPath.'hero_role'.DIRECTORY_SEPARATOR.'controller.php');
include_once($componentPath.'hero_hd'.DIRECTORY_SEPARATOR.'process.php');
include_once($componentPath.'hero_letters'.DIRECTORY_SEPARATOR.'model.php');
$S_ROOT = dirname($componentPath).DIRECTORY_SEPARATOR;
include_once($S_ROOT.'config.php');
include_once($S_ROOT.'lang_'.LANG_FLAG.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'hero_social'.DIRECTORY_SEPARATOR.'lang.php');
include_once($S_ROOT.'configs/ConfigLoader.php');
include_once($S_ROOT.'model/PlayerMgr.php');
include_once($S_ROOT.'includes/class_memcacheAdapter.php');
include_once($S_ROOT.'configs'.DIRECTORY_SEPARATOR.LANG_FLAG.DIRECTORY_SEPARATOR.'G_achievements.php');
include_once($componentPath.'hero_achievements'.DIRECTORY_SEPARATOR.'var.php');
include_once($componentPath.'hero_achievements'.DIRECTORY_SEPARATOR.'model.php');

if(!isset($G_PlayerMgr)){
    $G_PlayerMgr = new PlayerMgr($db,$mc);
}

/**
 * 充值成功后修改vip和可
 *
 * @param int $playersid
 * @param float $rmb
 * @param int $ingot
 * @param string $orderid
 * @param unknown_type $seqnum
 * @param boolean $is_del_mc
 * @param boolean $addCredits    true 增加积分流程, false 发信件流程
 * @return array
 */
function vipChongzhi($playersid, $rmb, $ingot, $orderid, $seqnum=null, $is_del_mc=true, $addCredits=true, $special=null){
    global $mc, $db, $common, $componentPath, $G_PlayerMgr, $sys_lang;
	$playersKey = MC.$playersid;
	$roleInfo = null;
    if($is_del_mc){
		$mc->delete($playersKey);
	}else{
		$roleInfo = $mc->get($playersKey);
	}
	$dateStr = date('Y-m-d H:i:s');

	
	if(empty($roleInfo)){
		$playSltSql = "select * from ".$common->tname('player');
		$playSltSql .= " where playersid = '{$playersid}'";
		$result = $db->query($playSltSql);
		if(!$result){
			return false;
		}
		$roleInfo = $db->fetch_array($result);
	}

	socialModel::completeRequst($roleInfo, 2);

	// 检查是否已绑定手机号,如果没有绑定就将手机号标识为999999
	if(isset($roleInfo['phone'])&&$roleInfo['phone']=='0'){
		$updateArray  = array('phone'=>'999999');
		$whereArray   = array('playersid'=>$playersid);
		$common->updatetable('player', $updateArray, $whereArray);
	}


	// vip 充值积分分枝
	while(defined('VIP_CREDITS_CONTROL')&&VIP_CREDITS_CONTROL==1){
		if($addCredits){
			break;
		}
		$message = sprintf($sys_lang[16], $dateStr, $ingot, $ingot);
		$reqArray['vip'] = array('pid'=>$playersid,
								 'rmb'=>$rmb,
								 'ingot'=>$ingot,
								 'orderid'=>$orderid,
								 'seqnum'=>$seqnum);
		$reqArray['tq'] = 0;
		$reqArray['yp'] = 0;
		$reqArray['yb'] = 0;
		$reqArray['jl'] = 0;

		$json = array();
		$json['playersid'] = $playersid;
		$json['toplayersid'] = $playersid;
		$json['message'] = array('xjnr'=>$message);
		$json['genre'] = 20;
		$json['request'] = json_encode($reqArray);
		$json['type'] = 1;
		$json['uc'] = '0';
		$json['is_passive'] = 0;
		$json['interaction'] = 0;
		$json['tradeid'] = 0;

		$ret = lettersModel::addMessage($json);
		return 0;
	}

	$insArray = array('playersid'=>$playersid,
					  'insertTime'=>$dateStr,
					  'money'=>$rmb,
					  'ingot'=>$ingot,
					  'orderid'=>$orderid);
						
	if(isset($seqnum)){
		$insArray['seqnum'] = $seqnum;
	}
	if(isset($special)){
		$insArray['special'] = $special;
	}
	$common->inserttable('vip_money_log', $insArray);
	
	$sumIngot = roleModel::getPlayerPayYaobao($playersid);

	$vipPrice = getVipPrice();
	$vip = 0;
	foreach($vipPrice as $level=>$price){
		if($sumIngot >= $price*RMB_TO_YUANBAO) $vip=$level;
	}
	//	$vip_end_time = time() + 30*24*3600;

	$cur_vip = $roleInfo['vip'];
	if($vip > $cur_vip){
		$updateArray  = array('vip'=>$vip);//, 'vip_end_time'=>$vip_end_time);
		$whereArray   = array('playersid'=>$playersid);
		$common->updatetable('player', $updateArray, $whereArray);
	}
	else{
		$vip = $cur_vip;
		//$vip_end_time = $roleInfo['vip_end_time'];
	}

	$roleInfo['playersid'] = $playersid;
	roleModel::getRoleInfo($roleInfo);
	$roleInfo['vip'] = $vip;
	//	$roleInfo['vip_end_time'] = $vip_end_time;
	if ($ingot > 0) {
		if (substr($roleInfo['rwsj'],0,1) != 1) {
			$newRwsj = substr_replace($roleInfo['rwsj'],'1',0,1);
			$db->query("UPDATE ".$common->tname('player')." SET rwsj = '$newRwsj' WHERE playersid = $playersid");
			$common->updateMemCache(MC.$playersid,array('rwsj'=>$newRwsj));
		}	
		questsController::OnFinish ( array('playersid'=>$playersid), "'sccz'" );
	}		
	hdProcess::run(array('pay_endPay'), $roleInfo, $rmb);
	$cjInfo['vip'] = $vip;
	achievementsModel::check_achieve($playersid,$cjInfo,array('vip'));	
	return array('vip'=>$vip, 'vip_end_time'=>0);
}