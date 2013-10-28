<?php
set_time_limit(0);
date_default_timezone_set('PRC');

include(dirname(__FILE__) . DIRECTORY_SEPARATOR .'../config.php');
mysql_connect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw']);
mysql_select_db($_SC['dbname']);

$sql = "update ol_dalei_jf set dhcs=0,zdlcs=0,dlcs=0,jf=0,lsh=0,lxsl=0";
//mysql_query($sql);

$sql = "update `ol_dalei_jf_count` set num=0,lsh=0";
//mysql_query($sql);

$sql = "update `ol_dalei_jf_count` set num=(select count(*) from `ol_dalei_jf` where jf=0),lsh=num where jf=0";
//mysql_query($sql);

mysql_close();
?>
