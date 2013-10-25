<?php
//基本文件 
include_once(OP_ROOT.'./hero_admin'.DIRECTORY_SEPARATOR.'controller.php');
include_once(OP_ROOT.'./hero_admin'.DIRECTORY_SEPARATOR.'model.php');
include_once(OP_ROOT.'./hero_admin'.DIRECTORY_SEPARATOR.'admin.php');
include_once(OP_ROOT.'./ClientView.php');
$task = _get('task'); 

$adminInfo = array('ucid'=>_get('ucid'));
$controller = new adminController;
$controller->$task($adminInfo);
?>