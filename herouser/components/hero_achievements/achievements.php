<?php
//基本文件
//include(OP_ROOT.'./hero_achievements'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'./hero_achievements'.DIRECTORY_SEPARATOR.'controller.php');
//include(OP_ROOT.'./hero_achievements'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./ClientView.php');
include(OP_ROOT.'./hero_role'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_city'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_city'.DIRECTORY_SEPARATOR.'model_general.php');
include(OP_ROOT.'./hero_letters'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_letters'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'./hero_city'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'./hero_social'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_social'.DIRECTORY_SEPARATOR.'SocialHub.php');
include(OP_ROOT.'./hero_fight'.DIRECTORY_SEPARATOR.'model_fight.php');
include(OP_ROOT.'./hero_fight'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_tools'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_tools'.DIRECTORY_SEPARATOR.'var.php');
if (!empty($_SESSION['playersid'])) {
	$playersid = $_SESSION['playersid'];
} else {
	$playersid = '';
}
$task = _get('task');
$test = array();
$getInfo = array('userid'=>_get('userid'),'playersid'=>$playersid);
$controller = new achievementsController();
$controller->$task($getInfo);