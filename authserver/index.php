<?php
/* * 1 = QQ Android 手机游戏大厅
2 = QQ Android 游戏中心
3 = 乐逗 Android 
4 = UC iOS
5 = UC Android
6 = 91 iOS
obj.yhly = CONFIG::ly;
obj.jcid = herool_as3.jcid;
obj.data = encodeURIComponent(sid);
 * */
header("Content-Type:text/html; charset=utf-8");
include(dirname(__FILE__) . '/config.php');
include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . $_SC['lang'] . ".php");
include(dirname(__FILE__) . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "common.php");
include(dirname(__FILE__) . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "function.php");
$link = mysql_connect($_SC['dbhost'], $_SC['dbuser'], $_SC['dbpw']);
mysql_selectdb($_SC['dbname'], $link);
mysql_query("set names 'utf8'", $link);
//$mc = new Memcache;
//$mc->addServer($MemcacheList[0], $Memport);
$ly = _get('ly');
$sid = _get('sid');
$SessionId = _get('SessionId');
$jcid = _get('jcid'); 
$task = _get('task');
$deviceid = _get('deviceid');
if ($task == 'hqzhxx') {
	include(dirname(__FILE__) . '/hqzhxx.php');
} elseif ($task == 'hqyxfuwlb' || $task == 'drdyyhfuq' || $task == 'zcdyyhfuw') {
	include(dirname(__FILE__) . '/selflogin.php');
} elseif ($ly == 1 || !empty($sid) || $jcid == 4 || $jcid == 5 || $jcid == 29) {
	include(dirname(__FILE__) . '/uclogin.php');	
} elseif ($jcid == 6 || $jcid == 17) {
	include(dirname(__FILE__) . '/91login.php');
} elseif($ly == 3) {
	if(isset($_GET['9ysid'])) {
		$Page9ySid = $_GET['9ysid'];
		setcookie("hero_9y_sid", $Page9ySid , time() + 3600);
	} else {
		$Page9ySid = $_COOKIE['hero_9y_sid'];
	}
	include(dirname(__FILE__) . '/9yPagelogin.php');
} elseif ($ly == 4) {
	include(dirname(__FILE__) . '/j2melogin.php');
} elseif ($ly == 5) {
	header("location:http://ucs.jofgame.com/authserver/index.php?".$_SERVER['QUERY_STRING']);
	//include(dirname(__FILE__) . '/synuclogin.php');
} elseif ($jcid == 3) {
	include(dirname(__FILE__) . '/ldlogin.php');
} elseif ($jcid == 1) {
	include(dirname(__FILE__) . '/qqyxdt.php');
} elseif ($jcid == 9) {
	include(dirname(__FILE__) . '/html5.php');
} elseif ($jcid == 14) {
	include(dirname(__FILE__) . '/yslogin.php');
} elseif ($jcid == 13 || $jcid == 8 || $jcid == 31) {
	include(dirname(__FILE__) . '/sglogin.php');
} elseif ($jcid == 10 || $jcid == 30) {
	include(dirname(__FILE__) . '/milogin.php');
} elseif ($jcid == 11) {
	include(dirname(__FILE__) . '/brlogin.php');
} elseif ($jcid == 18 || $jcid == 34) {
	include(dirname(__FILE__) . '/jflogin.php');
} elseif ($jcid == 19) {
	include(dirname(__FILE__) . '/denalogin.php');
} elseif ($jcid == 21 || $jcid == 35) {
	include(dirname(__FILE__) . '/91djlogin.php');
} elseif ($jcid == 22 || $jcid == 36) {
	include(dirname(__FILE__) . '/dllogin.php');
} elseif ($jcid == 23) {
	include(dirname(__FILE__) . '/yxhlogin.php');
} elseif ($jcid == 26) {
	include(dirname(__FILE__) . '/ifenglogin.php');
} elseif ($jcid == 27) {
	include(dirname(__FILE__) . '/dklogin.php');
} elseif ($jcid == 37) {
	include(dirname(__FILE__) . '/kugoulogin.php');
} elseif ($jcid == 38) {
	include(dirname(__FILE__) . '/wdjlogin.php');
} elseif ($jcid == 42) {
	include(dirname(__FILE__) . '/lenovlogin.php');
} elseif ($jcid == 45) {
	include(dirname(__FILE__) . '/ppioslogin.php');
} elseif ($jcid == 54) {
	include(dirname(__FILE__) . '/mplogin.php');
} elseif ($jcid == 51) {
	include(dirname(__FILE__) . '/51yxlogin.php');
} elseif ($jcid == 46) {
	include(dirname(__FILE__) . '/zgsylogin.php');
} elseif ($jcid == 53) {
	include(dirname(__FILE__) . '/rglogin.php');
} elseif ($jcid == 57) {
	include(dirname(__FILE__) . '/bnlogin.php');
} elseif ($jcid == 58) {
	include(dirname(__FILE__) . '/yxlogin.php');
} else {
	include(dirname(__FILE__) . '/selflogin.php');
}
