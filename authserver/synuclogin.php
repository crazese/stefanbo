<?php
header("Content-Type:text/html; charset=utf-8");
//通知UC登录同步接口
function loginsynuc($gameUser) {
	global $_SGLOBAL, $_SC;
	//print_r($_SC['cpId_syn']);
	$id = time();
	$sign = md5($_SC['cpId_syn'].'gameUser='.$gameUser.$_SC['apiKey_syn']);
	$game = array('cpId'=>intval($_SC['cpId_syn']),'gameId'=>intval($_SC['gameId_syn']),'channelId'=>intval($_SC['channelId_syn']),'serverId'=>intval($_SC['serverId_syn']));
	$data = array('gameUser'=>$gameUser);
	$postdata = json_encode(array("id"=>$id,"service"=>"ucid.bind.create","data"=>$data,"game"=>$game,"sign"=>$sign,"encrypt"=>"md5"));
	$ucdata = http_post($_SC['apiUrl_syn'],80,'/ss',$postdata);
	return $ucdata;
}

//socket访问
function http_post($host, $port, $path, $data) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://'.$host.':'.$port.$path);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}
$name = urldecode($_GET['u']);
$p = urldecode($_GET['p']);
//loginsynuc($name);
// 初始化加解密工具类
$rsa = new Rsa(KEY_PATH);

// 当前时间戳
$loginDate = time();
// 是否是新用户
$userInfo = isDupUser($name);
$stop = 0;
if ($userInfo == false) {
	$stop = 1;
} else {
	if ($userInfo['pwd'] != md5($p)) {
		$stop = 2;
	}
}
if ($stop === 1) {
	echo '{"id":1351237534,"state":{"code":1001,"msg":"该用户不存"},"data":{"sid":"0"}}';
} elseif ($stop === 2) {
	echo '{"id":1351237534,"state":{"code":1002,"msg":"密码错误"},"data":{"sid":"0"}}';
} else {
	$synInfo = loginsynuc($name);
	$synData = json_decode($synInfo,true);
	if ($synData['state']['code'] == 1) {
		$userName = $guid = $synData['data']['ucid'];
		// 生成令牌
		$token = $userName . '-' . $guid . '-' . $loginDate.'-'.$userInfo['uid'];		
		$token = $rsa->privEncrypt($token);
		$userData = http_post($_SC['ucsynUrl'], $_SC['port'], '/TimingTasks/synuclogin.php', $token);
		if ($userData == 1) {
			mysql_query("update ol_users set uid = '$userName',userName = '$userName' where userName = '$name' limit 1");
			mysql_query("insert into ol_change_ucid_logs(ucid,username,uid,lx) values ('$userName','$name','','1')");
		} else {
			die('{"id":1351237534,"state":{"code":1003,"msg":"该用户游戏数据不存在或更新失败，请再试一遍"},"data":{"sid":"0"}}');
			exit;
		}		
	}	
	echo $synInfo;
}
