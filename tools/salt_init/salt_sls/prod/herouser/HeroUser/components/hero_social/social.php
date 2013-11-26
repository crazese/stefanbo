<?php
//社交基本文件
include(OP_ROOT.'./hero_social'.DIRECTORY_SEPARATOR.'controller.php');
include(OP_ROOT.'./hero_social'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./ClientView.php');
include(OP_ROOT.'./hero_city'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'./hero_letters'.DIRECTORY_SEPARATOR.'controller.php');
include(OP_ROOT.'./hero_letters'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_letters'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'./hero_city'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_city'.DIRECTORY_SEPARATOR.'model_general.php');
include(OP_ROOT.'./hero_role'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_fight'.DIRECTORY_SEPARATOR.'model.php');//fightModel 策反用到了。
include(OP_ROOT.'./hero_fight'.DIRECTORY_SEPARATOR.'model_fight.php');
include(OP_ROOT.'./hero_tools'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_tools'.DIRECTORY_SEPARATOR.'var.php');
//include(OP_ROOT.'./hero_quests'.DIRECTORY_SEPARATOR.'controller.php');//任务
//include(OP_ROOT.'./hero_social/ucapi/libs/UzoneRestApi.php');
$playersid = $_SESSION['playersid'];
$task = _get('task');
$toplayersid = _get('toplayersid');
$tradeid = _get('tradeid');
$generalid = _get('generalid');
$points = _get('points');
$jf = _get('jf');
$je = _get('je');
$mc = _get('mc');
$delplayersid = _get('delplayerid');
$wxdj = _get('wxdj');
$type = _get('type');
$page = _get('page');
$getInfo = array('playersid'=>$playersid,'userid'=>_get('userId'),'toplayersid'=>$toplayersid,'tradeid'=>$tradeid,'generalid'=>$generalid,'points'=>$points,'jf'=>$jf,'je'=>$je,'mc'=>$mc,'delplayersid'=>$delplayersid,'wxdj'=>$wxdj,'type'=>$type,'page'=>$page);
if (!empty($playersid)) {
	//cityModel::resourceGrowth($playersid);
}

$controller = new socialController;
$controller->$task($getInfo);

$heroCommon = new heroCommon();
//$heroCommon->setQueue();

?>