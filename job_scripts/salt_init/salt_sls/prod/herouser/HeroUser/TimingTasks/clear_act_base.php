<?php
include(dirname(dirname(__FILE__)) . '/config.php');
date_default_timezone_set('PRC');
mysql_connect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw']);
mysql_select_db($_SC['dbname']);

// 清除过期活动数据
$delActSql = "delete from ol_huodong where published=1 and endTime<".time();
mysql_query($delActSql);

// 清除过期未发布活动
$sltActSql = "select hdid from ol_huodong where published=0 and endTime<".(time()-7*24*3600);

$result = mysql_query($sltActSql);
$unpubList = array();
while($rows = mysql_fetch_assoc($result)){
	$unpubList[] = $rows['hdid'];
}

// 如果有就清除对应hdid类型的玩家活动和对应活动基础表
if(!empty($unpubList)){
	$delActIds = implode(',', $unpubList);
	$delMyUnActSql = "delete from ol_my_huodong where hdid in ({$delActIds}) limit 200";
	do{
		$result = mysql_query($delMyUnActSql);
		$count = mysql_affected_rows();
		usleep(200000);
	}while(0<$count);
	
	// 保证玩家数据过期后执行
	// 当然如果玩家在这段时间内激活一个新活动那么有可能会超出这个时间
	sleep(3600);
	$delActSql = "delete from ol_huodong where hdid in ({$delActIds})";
	mysql_query($delActSql);
}

// 清理过期兑换活动
$delExcSql = "delete from ol_exchange where endTime<".time();
mysql_query($delExcSql);

$delUserExcSql = "delete from ol_my_exchange where expire_time<'".(time()-24*3600);
$delUserExcSql .= "' limit 200";
do{
	$result = mysql_query($delUserExcSql);
	$count = mysql_affected_rows();
	usleep(200000);
}while(0<$count);