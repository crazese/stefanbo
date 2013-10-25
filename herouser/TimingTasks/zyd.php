<?php
define('IN_HERO', TRUE);
define('D_BUG', '1');
set_time_limit(0);
//程序目录
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR.'../');
define('OP_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR.'../components'.DIRECTORY_SEPARATOR);
$_SGLOBAL = array();
//设置时区
$mtime = explode(' ', microtime());
$_SGLOBAL['timestamp'] = $mtime[1];
$_SGLOBAL['supe_starttime'] = $_SGLOBAL['timestamp'] + $mtime[0];
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
include(OP_ROOT.'hero_rank'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'hero_rank'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'hero_hd'.DIRECTORY_SEPARATOR.'process.php');
require(S_ROOT.'configs/ConfigLoader.php');
require(S_ROOT.'model/PlayerMgr.php');
require(S_ROOT.'includes/class_memcacheAdapter.php');
include(OP_ROOT.'./hero_social'.DIRECTORY_SEPARATOR.'controller.php');
include(OP_ROOT.'./hero_social'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_quests'.DIRECTORY_SEPARATOR.'script.php');
include(OP_ROOT.'./hero_quests'.DIRECTORY_SEPARATOR.'controller.php');
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
$stat = $mc->add(MC.'CLI_ZYD',$memvalue, 0, 0);  //永不过期
if($stat === false)  {
   //error_log(date("Y.m.d H:i:s")." zyd2.php: memcache add error,annother process is running?\n");
   //die("memcache add error,annother process is running?\n");
}
$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname'],'utf8');
$db->query("set names 'utf8'");
$normalrestart = false;

function whenShutdown() {
  global $__dberr,$mc,$normalrestart;
  // 如果是数据库出错或正常的重启
  if($__dberr || $normalrestart)  {
    $normalrestart = false;
    echo "zyd exit\n";
    //重启交由脚本负责
    $mc->delete(MC.'CLI_ZYD'); 
    system('php ' . __FILE__ . ' > /dev/null &');
  }
}
register_shutdown_function('whenShutdown');

$t = microtime(true);
//1小时或mysql,mc出错则终止循环
while (microtime(true) - $t < 1800 && mysql_errno() == 0 && $mc->get(MC.'CLI_ZYD') === $memvalue) {
  //时间
  $mtime = explode(' ', microtime());
  $_SGLOBAL['timestamp'] = $mtime[1];
  $_SGLOBAL['supe_starttime'] = $_SGLOBAL['timestamp'] + $mtime[0];
  zydModel::zydjs();
  usleep(1000000);  //1s
}

$db->close();
if($mc->get(MC.'CLI_ZYD') === $memvalue) $normalrestart = true;
?>