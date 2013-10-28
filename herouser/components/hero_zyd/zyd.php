<?php
//基本文件
include(OP_ROOT.'./hero_fight'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_fight'.DIRECTORY_SEPARATOR.'model_fight.php');
include(OP_ROOT.'./hero_fight'.DIRECTORY_SEPARATOR.'model_attack.php');
include(OP_ROOT.'./hero_fight'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'./hero_city'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'./hero_city'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_city'.DIRECTORY_SEPARATOR.'model_java.php');
include(OP_ROOT.'./hero_city'.DIRECTORY_SEPARATOR.'model_general.php');
include(OP_ROOT.'./hero_letters'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./ClientView.php');
include(OP_ROOT.'./hero_role'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_tools'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_tools'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'./hero_zyd'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_zyd'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'./hero_zyd'.DIRECTORY_SEPARATOR.'controller.php');
if (!($playersid = $_SESSION['playersid'])) {
	$playersid = '';
}
$task = _get('task');
$getInfo = array('playersid'=>$playersid,'userid'=>_get('userId'));
$controller = new zydController;
$controller->$task($getInfo);
?>