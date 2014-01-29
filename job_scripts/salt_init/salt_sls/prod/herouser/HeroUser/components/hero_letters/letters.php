<?php
//社交基本文件
include(OP_ROOT.'./hero_letters'.DIRECTORY_SEPARATOR.'controller.php');
include(OP_ROOT.'./hero_letters'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./ClientView.php');
include(OP_ROOT.'./hero_city'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'./hero_city'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_city'.DIRECTORY_SEPARATOR.'model_general.php');
include(OP_ROOT.'./hero_social'.DIRECTORY_SEPARATOR.'controller.php');
//include(OP_ROOT.'./hero_social/ucapi/libs/UzoneRestApi.php');
include(OP_ROOT.'./hero_social'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_role'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_tools'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_letters'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'./hero_tools'.DIRECTORY_SEPARATOR.'var.php');
$playersid = $_SESSION['playersid'];
//$playersid = 195;
//$checkSession = $common->checkUserSession(_get('userid'),_get('sessionid'),$playersid); //检测有无操作权限
$task = _get('task');
$socialid = _get('socialid');
$toplayersid = _get('toplayersid');
$lettersid = _get('lettersid');
$message = _get('message');
$type = _get('type');
$getInfo = array('playersid'=>$playersid,'userid'=>_get('userId'),'socialid'=>$socialid,'toplayersid'=>$toplayersid,'lettersid'=>$lettersid,'message'=>$message,'type'=>$type);

$controller = new lettersController;
$controller->$task($getInfo);

$heroCommon = new heroCommon();
//$heroCommon->setQueue();