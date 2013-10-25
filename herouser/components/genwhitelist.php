<?php
include(dirname(dirname(dirname(__FILE__))) . '/config.php');
mysql_connect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw']);
mysql_select_db($_SC['dbname']);
mysql_query("set names 'utf8'");
$res = mysql_query("SELECT GROUP_CONCAT(userid) as uids FROM ol_player where nickname like 'jt%'") or die('查询错误');
$arr = mysql_fetch_array($res);
file_put_contents('whitelist.txt',$arr['uids']);
echo "已写入如下id:<br/>".$arr['uids'];
?>