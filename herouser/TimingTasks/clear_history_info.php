<?php
include(dirname(dirname(__FILE__)) . '/config.php');
date_default_timezone_set('PRC');
set_time_limit(0);

if (!mysql_connect($_SC['dbhost'], $_SC['dbuser'], $_SC['dbpw']))
{
	error_log(__FILE__ . ": mysql_connect failed: " . $_SC['dbhost'] . "(" . $_SC['dbuser'] . "/" . $_SC['dbpw'] . ").");
	exit();
}

mysql_select_db($_SC['dbname']);

$tick1 = time() - 259200;
$tick2 = time() - 345600;

// 清除超过3天的非系统信件（系统信件打擂信件除外）
delete_and_sleep("DELETE FROM ol_letters WHERE create_time < $tick1 AND genre NOT IN (0,20,32) LIMIT 100");

// 清除超过4天的抽奖
$cj_limit_time = date('Y-m-d H:i:s', $tick2);
delete_and_sleep("DELETE FROM ol_player_cj WHERE create_time < '$cj_limit_time' LIMIT 100");

// 清除超过4天的送礼打劫信息
delete_and_sleep("DELETE FROM ol_social_trade WHERE create_time < $tick2 LIMIT 100");

function delete_and_sleep($sql)
{
	echo "Query: $sql\n";
	
	$retry = 0;

	while (true)
	{
		$startTime = microtime(true);
		
		if (!mysql_query($sql))
		{
			$retry++;
			error_log(__FILE__ . ": mysql_query failed: $sql (attempt $retry).");
			usleep(1000000);
			if ($retry >= 5)
				break;
			else
				continue;
		}
		
		$rows = mysql_affected_rows();
		if ($rows == 0)
			break;
			
		$span = microtime(true) - $startTime;
		if ($span < 1)
		{
			echo "Deleted $rows rows. Sleep " . (1 - $span) . " seconds.\n";
			usleep((1 - $span) * 300000);
		}
	}
}
?>