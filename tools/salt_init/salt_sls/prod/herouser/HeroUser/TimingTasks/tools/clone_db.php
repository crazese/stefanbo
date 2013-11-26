<?php
date_default_timezone_set('PRC');
set_time_limit(0);

$db_user = 'sothink';
$db_pwd = 'src8351';
$db_old = 'huqianchuan_test';
$db_new = 'huqianchuan_test_bak';
$host = "127.0.0.1";
$port = "3306";

echo "导出表结构：$db_old -> $db_old" . "_bak.sql...\n";
system("mysqldump -h $host -P $port -u$db_user -p$db_pwd -d --default-character-set=utf8 $db_old > $db_old" . "_bak.sql", $result);
if ($result != 0)
{
	my_error_log("mysqldump error. exit code is: $result");
	exit(1);
}

echo "删除库：$db_new...\n";
system("mysqladmin -h $host -P $port -u$db_user -p$db_pwd --default-character-set=utf8 -f drop $db_new", $result);

echo "创建库：$db_new...\n";
system("mysqladmin -h $host -P $port -u$db_user -p$db_pwd --default-character-set=utf8 create $db_new", $result);
if ($result != 0)
{
	my_error_log("mysqladmin create database error. exit code is: $result");
	exit(1);
}

echo "导入数据：$db_old" . "_bak.sql -> $db_new...\n";
system("mysql -h $host -P $port -u$db_user -p$db_pwd --default-character-set=utf8 $db_new < $db_old" . "_bak.sql", $result);
if ($result != 0)
{
	my_error_log("mysql import database error. exit code is: $result");
	exit(1);
}

// connect to mysql
echo "连接mysql...\n";
if (!mysql_connect($host.':'.$port, $db_user, $db_pwd))
{
	my_error_log("mysql_connect failed: " . $host.':'.$port. "(" . $db_user . "/" . $db_pwd. ").");
	exit(1);
}

// get all tables' name
echo "获取所有表...\n";
mysql_select_db($db_old);
$result = mysql_query("select table_name from information_schema.tables where table_schema='$db_old' and table_type='BASE TABLE'");
if (!$result)
{
	my_error_log(mysql_error());
	exit(1);
}
echo "表总数: " . mysql_num_rows($result) . "\n";
$tables = array();
while($row = mysql_fetch_row($result))
{
	$tables[] = $row[0];
	echo " $row[0]\n";
}

echo "导入表数据...\n";
$startTimeAll = microtime(true);
foreach ($tables as $table)
{
	echo " $table...";
	$startTime = microtime(true);
	if (!mysql_query("INSERT INTO $db_new.$table (SELECT * FROM $db_old.$table)"))
	{
		echo "\n";
		my_error_log(mysql_error());
		exit(1);
	}
	$span = microtime(true) - $startTime;
	printf(" %.3f秒\n", $span);
}

$span = microtime(true) - $startTimeAll;
printf("总耗时: %.3f 秒\n", $span); 

exit(0);

function my_error_log($error)
{
	error_log(__FILE__ . ": $error");
}

?>