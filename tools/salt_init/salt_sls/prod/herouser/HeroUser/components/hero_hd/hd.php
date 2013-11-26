<?php
//基本文件
include(OP_ROOT.'hero_tools'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'ClientView.php');
include(OP_ROOT.'hero_hd'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'hero_hd'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'hero_hd'.DIRECTORY_SEPARATOR.'controller.php');
include(OP_ROOT.'hero_hd'.DIRECTORY_SEPARATOR.'exchange.php');
include(OP_ROOT.'hero_role'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'hero_letters'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'hero_letters'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'hero_tools'.DIRECTORY_SEPARATOR.'var.php');

if (!($playersid = $_SESSION['playersid'])) {
	$playersid = '';
}
$task = _get('task');
$getInfo = array('playersid'=>$playersid,'userid'=>_get('userId'));
$controller = new hdController;
$controller->$task($getInfo);
?>