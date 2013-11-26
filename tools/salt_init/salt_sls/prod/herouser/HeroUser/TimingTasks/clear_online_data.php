<?php
date_default_timezone_set('PRC');
include(dirname(__FILE__) . DIRECTORY_SEPARATOR .'../config.php'); ////'../config.php'
mysql_connect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw']);
mysql_select_db($_SC['dbname']);
$checkTime = time() - 300;
mysql_query("DELETE FROM ol_online WHERE updateTime <= '$checkTime'");
mysql_close();