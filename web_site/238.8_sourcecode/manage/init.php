<?php
session_start();
$BASE_DIR = str_replace('\\','/',substr(__FILE__,0,-9));
$BASE_LOGIC_DIR = str_replace(str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']),'',$BASE_DIR);
require_once($BASE_DIR.'/inc/config.php');
require_once($BASE_DIR.'/inc/const.php');
require_once($BASE_DIR.'/inc/global.php');
require_once($BASE_DIR.'/inc/function.php');

require_once('isadmin.php');
require_once($BASE_DIR.'/class/Db.php');
include_once($BASE_DIR.'/class/Admin.php');
//include_once($BASE_DIR.'/smarty/Smarty.class.php');



$db = new Db();
$db->connect();
$user = new Admin;

?>