<?php
//基本文件 
include(OP_ROOT.'./hero_role'.DIRECTORY_SEPARATOR.'controller.php');
include(OP_ROOT.'./hero_role'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_city'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_fight'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_fight'.DIRECTORY_SEPARATOR.'model_fight.php');
include(OP_ROOT.'./ClientView.php');
include(OP_ROOT.'./hero_city'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'./hero_city'.DIRECTORY_SEPARATOR.'model_general.php');
include(OP_ROOT.'./hero_letters'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_tools'.DIRECTORY_SEPARATOR.'model.php');
//include(OP_ROOT.'./hero_quests'.DIRECTORY_SEPARATOR.'controller.php');
include(OP_ROOT.'./hero_social'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_dalei'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_zyd'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_zyd'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'./hero_tools'.DIRECTORY_SEPARATOR.'var.php');
//include_once(OP_ROOT.'./hero_social/ucapi/libs/UzoneRestApi.php');
global $_SC;
$task = _get('task'); 
if ($task != 'returnRoleDataToUser') {
	$client = $_SESSION['client'];
	//$client = 0;
} else {
	$client = 1;
}
$b = _get('bs');
if(!empty($b)) {
	if(SYPT == 1) {
		$roleInfo = array('userid'=>$_SESSION['userid'],'nickname'=>urldecode(_get('nickname')),'sex'=>_get('sex'),'regionid'=>_get('regionId'),'inviteid'=>$_SESSION['inviteid'],'ucid'=>$_SESSION['ucid']);
	} else {
		$roleInfo = array('userid'=>$_SESSION['userid'],'nickname'=>urldecode(_get('nickname')),'sex'=>_get('sex'),'regionid'=>_get('regionId'),'inviteid'=>$_SESSION['inviteid'],'ucid'=>$_SESSION['ucid']);
	}
}else{
	if ($client == 1) {
		$nickname = heroCommon::decode(_get('roleName'),1);
		$sex = 0;
		$inviteid = 0;
		$ucid = 0;
	} else {
		$nickname = _get('nickname');
		$sex = _get('sex');
		$inviteid = $_SESSION['inviteid'];
		$ucid = $_SESSION['ucid'];
	}
	$roleInfo = array('userid'=>_get('userId'),'nickname'=>$nickname,'sex'=>$sex,'regionid'=>_get('regionId'),'inviteid'=>$inviteid,'ucid'=>$ucid);
}
$controller = new roleController;
$controller->$task($roleInfo);
//heroCommon::insertLog($roleInfo['nickname']);
//$heroCommon = new heroCommon();
//$heroCommon->setQueue();
?>