<?php
include(dirname(__FILE__) . '/config.php');

$link = mysql_connect($_SC['dbhost'], $_SC['dbuser'], $_SC['dbpw']);
mysql_selectdb($_SC['db'], $link);
mysql_query("set names 'utf8'", $link);

//账号名从9y0001至9y5000止，总计生成5000个账号
//密码由随机的6位数字构成
$name_prefix = '9y';

for($i = 1; $i <= 5000; $i++) {
	$trans = array('{'=>'', '}'=>'', '-'=>'');
	$res = mysql_query('select UUID()  as guid', $link);
	$guid = mysql_fetch_array($res, MYSQL_ASSOC);
	$guid = strtr($guid['guid'], $trans);
					
	$origin = 1;
	$email = '';

	$loginDate = time();
	
	$passwd = rand(100000, 999999);

	if($i < 10) {
		$name = $name_prefix . '000' . $i;
	} else if($i < 100) {
		$name = $name_prefix . '00' . $i;
	} else if($i <  1000) {
		$name = $name_prefix . '0' . $i;
	} else {
		$name = $name_prefix . $i;
	}

	// 生成令牌
	$token = $name . '-' . $guid . '-' . $loginDate;	

	// 添加用户
	$encrPwd = md5($passwd);
	mysql_query("insert into ol_users(uid, userName, pwd, email, lastLoging, enterServers, verifyCallBack, origin)
	 values('$guid', '$name', '$encrPwd', '$email', '$loginDate', '', '$token', '$origin')", $link);

	 $writeStr = sprintf("account:%s password:%s \r\n", $name, $passwd);

	 file_put_contents('c:\\account.txt', $writeStr, FILE_APPEND);

 }
?>