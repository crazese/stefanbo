<?php 
//基本文件 
include(OP_ROOT.'./hero_user'.DIRECTORY_SEPARATOR.'controller.php');
include(OP_ROOT.'./hero_user'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./ClientView.php');
include(OP_ROOT.'./hero_user'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'./hero_role'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_role'.DIRECTORY_SEPARATOR.'controller.php');
include(OP_ROOT.'./hero_tools'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'./hero_city'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'./hero_tools'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_city'.DIRECTORY_SEPARATOR.'model_general.php');
include(OP_ROOT.'./hero_zyd'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_zyd'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'./hero_city'.DIRECTORY_SEPARATOR.'model.php');
global $client,$header_info,$_SC;
//$client = _get('client');
/*hk*/if(isset($_COOKIE['hero_from']) && (isset($_COOKIE['hero_9y_sid']) || isset($_COOKIE['herooline']) )) {
	if($_COOKIE['hero_from'] == 'uc') {
		$client = 4;
	} else if($_COOKIE['hero_from'] == '9y' ){
		$client = 3;
	}
}  else {
	$client = _get('client');
}/*hk*///增加COOKIE内容，支持UC和9Y账号切换
$task = _get('task');
$b = _get('bs');

$uzone_token = "";
if(SYPT == 1) {
	$username = $header_info['X-OPENID'];
	$uzone_token = $header_info['X-TOKEN'];
	//$username = 'DF12XXF5960155F127XABCSS855924B9';
	//$uzone_token = '7Ja7dgkXl0NDNjyipQUB0k1DDU1OkD.kb7UB.HSPR9EJiAwTgoHMkKQBN';
} else {
	$username = trim(_get('userName'));
	$uzone_token = '';
}
if(empty($b)) {
	$username = trim(_get('userName'));
	$uzone_token = '';
}

if($client == 2) {
	$username = trim(_get('userId'));
	$uzone_token = _get('token');
}

if($client == 3) {	
	if(isset($_COOKIE["hero_9y_ucid"])) {
		if($_COOKIE["hero_9y_ucid"] != $_SESSION["hero_9y_ucid"]) {
			$username = $_SESSION["hero_9y_ucid"];
		} else {
			$username = $_COOKIE["hero_9y_ucid"];	
		}
	} else {
		$username = $_SESSION["hero_9y_ucid"];
	}
}

$userInfo = 
array('username'=>mb_convert_encoding($username, "UTF-8", "gb2312"),'password'=>_get('password'),'client'=>$client,'register_time'=>time(),'uzone_token'=>$uzone_token,'inviteid'=>_get('inviteId'),'realname'=>_get('userName'),'sexid'=>_get('sex'));

$controller = new userController();

$controller->$task($userInfo);

$heroCommon = new heroCommon();
//$heroCommon->setQueue();
?>