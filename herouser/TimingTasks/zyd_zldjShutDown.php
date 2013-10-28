<?php
set_time_limit(0);
//程序目录
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR.'../');
define('OP_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR.'../components'.DIRECTORY_SEPARATOR);
$_SGLOBAL = array();
//设置时区
date_default_timezone_set('Asia/Shanghai');
include(S_ROOT.'config.php');

$mc = new Memcached;
for ($m = 0; $m < 3; $m++) {
   if ($mc->addServer($MemcacheList[0], $Memport) == true) {
   	    $mcConnect = true;
   		break;
   }
}

$stat = $mc->delete(MC.'CLI_ZYD_ZLDJ'); 
echo 'delete stat:'.$stat;
?>
