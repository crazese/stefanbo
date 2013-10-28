<?php
//时间已到，清除逐鹿资源点玩家数据
set_time_limit(0);
define('D_BUG', '1');
@define('IN_HERO', TRUE);
//程序目录
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR.'../');
define('OP_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR.'../components'.DIRECTORY_SEPARATOR);
$mtime = explode(' ', microtime());
$_SGLOBAL['timestamp'] = $mtime[1];
$_SGLOBAL['supe_starttime'] = $_SGLOBAL['timestamp'] + $mtime[0];
//设置时区
date_default_timezone_set('Asia/Shanghai');
include(S_ROOT.'config.php');
include(S_ROOT.'includes/class_mysql.php');
include(S_ROOT.'includes/class_common.php');

//基本文件
include(OP_ROOT.'hero_fight'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'hero_fight'.DIRECTORY_SEPARATOR.'model_fight.php');
include(OP_ROOT.'hero_fight'.DIRECTORY_SEPARATOR.'model_attack.php');
include(OP_ROOT.'hero_fight'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'hero_city'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'hero_city'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'hero_city'.DIRECTORY_SEPARATOR.'model_java.php');
include(OP_ROOT.'hero_city'.DIRECTORY_SEPARATOR.'model_general.php');
include(OP_ROOT.'ClientView.php');
include(OP_ROOT.'hero_role'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'hero_tools'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'hero_tools'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'hero_zyd'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'hero_zyd'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'hero_zyd'.DIRECTORY_SEPARATOR.'controller.php');
include(OP_ROOT.'hero_letters'.DIRECTORY_SEPARATOR.'model.php'); 
require(S_ROOT.'configs/ConfigLoader.php');
require(S_ROOT.'model/PlayerMgr.php');
require(S_ROOT.'includes/class_memcacheAdapter.php');
include(OP_ROOT.'./hero_social'.DIRECTORY_SEPARATOR.'controller.php');
include(OP_ROOT.'./hero_social'.DIRECTORY_SEPARATOR.'model.php');
include(S_ROOT . 'configs'.DIRECTORY_SEPARATOR.LANG_FLAG.DIRECTORY_SEPARATOR.'G_achievements.php');
include(OP_ROOT.'hero_achievements'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'hero_achievements'.DIRECTORY_SEPARATOR.'model.php');
$db = new dbstuff;
$mc = new MemcacheAdapter_Memcached;
$G_PlayerMgr = new PlayerMgr($db,$mc);
$common = new heroCommon;
for ($m = 0; $m < 3; $m++) {
   if ($mc->addServer($MemcacheList[0], $Memport) == true) {
   	    $mcConnect = true;
   		break;
   }
}
$memvalue = time().'_'.mt_rand();
$stat = $mc->add(MC.'CLI_ZYD_QCZL',$memvalue, 0, 0);  //永不过期
if($stat === false)  {
  error_log(date("Y.m.d H:i:s")." qczl.php: memcache add error,annother process is running?\n");
  die("memcache add error,annother process is running?\n");
}
$normalrestart = false;

function whenShutdown() {
  global $__dberr,$mc,$normalrestart;
  // 如果是数据库出错或正常的重启
  if($__dberr || $normalrestart)  {
    $normalrestart = false;
    $mc->delete(MC.'CLI_ZYD_QCZL'); 
    echo "qczl restart\n";
    system('php ' . __FILE__ . ' > /dev/null &');
  }
}
register_shutdown_function('whenShutdown');

$t = microtime(true);
$m1 = zyzkssj(1);
$m2 = zyzkssj(2);
$m3 = zyzkssj(3);
$m4 = zyzkssj(4);
$m5 = zyzkssj(5);
$m6 = zyzkssj(6);
$m7 = zyzkssj(7);
//从某点开始运行，总计运行15分钟
while (microtime(true) - $t < 1800 && mysql_errno() == 0 && $mc->get(MC.'CLI_ZYD_QCZL') === $memvalue) {	
  //时间
  	$time = time();
  	$m = date('H',$time);
	if ($m >= ($m1 + 1) && $m < ($m1 + 2)) {
		$zydid = 1;
	} elseif ($m >= ($m2 + 1) && $m < ($m2 + 2)) {
		$zydid = 2;
	} elseif ($m >= ($m3 + 1) && $m < ($m3 + 2)) {
		$zydid = 3;
	} elseif ($m >= ($m4 + 1) && $m < ($m4 + 2)) {
		$zydid = 4;
	} elseif ($m >= ($m5 + 1) && $m < ($m5 + 2)) {
		$zydid = 5;
	} elseif ($m >= ($m6 + 1)  && $m < ($m6 + 2)) {
		$zydid = 6;
	} elseif ($m >= ($m7 + 1) && $m < ($m7 + 2)) {
		$zydid = 7;
	} else {
		$zydid = 0;
		sleep(1);
	}   
	if ($zydid > 0) {		
		$result = zydModel::qczlwj($zydid);
		sleep(1);
	}
}	
	//usleep(1000000);  //1s

$db->close();
if($mc->get(MC.'CLI_ZYD_QCZL') === $memvalue) $normalrestart = true;	
?>