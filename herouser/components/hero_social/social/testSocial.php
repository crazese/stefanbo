<?php
define('S_ROOT', dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR );
include(S_ROOT.'config.php');
require(S_ROOT.'includes/class_memcacheAdapter.php');
include('sySocial.php');

$socail = new sySocial();
$mc = new MemcacheAdapter_Memcache;
$mc->pconnect($MemcacheList[0], $Memport) or die('Memcache server connection error.');

$roleInfo['ucid'] = '';

$result = $socail->getFriends($roleInfo);
var_dump($result);
$result = $socail->pushMessage($roleInfo, 'test');
var_dump($result);

