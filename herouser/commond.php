<?php
header("content-type:text/html; charset=utf-8");

//程序目录
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('OP_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR);
$_SGLOBAL = array();
//设置时区
date_default_timezone_set('Asia/Shanghai');
//基本文件
include_once(S_ROOT.'./config.php');
include_once(S_ROOT.'./includes/class_mysql.php');
include_once(S_ROOT.'./includes/class_common.php');
$db = new dbstuff;
$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname']);
//时间
$mtime = explode(' ', microtime());
$_SGLOBAL['timestamp'] = $mtime[1];
$_SGLOBAL['supe_starttime'] = $_SGLOBAL['timestamp'] + $mtime[0];

$commond = new heroCommond;

//GPC过滤
$magic_quote = get_magic_quotes_gpc();
if(empty($magic_quote)) {
	$_GET = $commond->saddslashes($_GET);
	$_POST = $commond->saddslashes($_POST);
}

$option = $_GET['optin'];

$commond->getCmd('hero_user');

/*$commond = new model;
//连接数据库
$commond->charset = $_SC['dbcharset'];
$link = $commond->dbconnect();
//$commond->close_mysql();
$userInfo = array('username'=>'gzp','password'=>'19810102','email'=>'gzp@sothink.com','mobile'=>'13707174435','register_time'=>time());
$commond->userRegist($userInfo);
$userID = $commond->insert_id();
$playerInfo = array('userid'=>$userID,'nickname'=>'liyu','sign'=>'低调','sex'=>'1','bagpack'=>'100');
$commond->playersCreate($playerInfo);
//$model->test();
//echo S_ROOT;
//echo 'aaa';
$db = new dbstuff($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname']);
$userInfo = array('username'=>'gzp','password'=>'19810102','email'=>'gzp@sothink.com','mobile'=>'13707174435','register_time'=>time());
$commond->userRegist($userInfo);
$task = $_GET['task'];*/
?>