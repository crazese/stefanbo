<?php
$task = _get('task');
$option = _get('option');
if( $task == null || $option == null || ($task != 'uclogin' && $option != 'user') ) {
	$returnValue['status'] = 3;
	$returnValue['message'] = '非法请求';
	$returnValue['rsn'] = intval(_get('ssn'));	
	echo json_encode($returnValue);
	exit;
} else {
	// 初始化加解密工具类
	$rsa = new Rsa(KEY_PATH);	
	// 当前时间戳
	$loginDate = time();	
	// 获取服务器列表
	$serverList = array();
	$res = mysql_query('select * from ol_serverlist', $link);
	while($serverList[] = mysql_fetch_array($res, MYSQL_ASSOC));
	array_pop($serverList);
	$userName = _get('zh');
	$userName = $ly . '_' . $userName;
	$userInfo = isDupUser($userName);
	$pwd = _get('mm');	
	$newPwd = _get('xmm');
	$yzm = _get('yzm');
	$sjh = _get('sjhm');
	$origin = 4;
	$email = '';
	if ($userInfo == false) {
		// 生成guid
		$trans = array('{'=>'', '}'=>'', '-'=>'');
		$guid = $userName;				
		// 生成令牌
		$token = $userName . '-' . $guid . '-' . $loginDate;				
		// 添加用户
		$encrPwd = md5($pwd);
		mysql_query("insert into ol_users(uid, userName, pwd, email, lastLoging, enterServers, verifyCallBack, origin,userName2)
		 values('$guid', '$userName', '$encrPwd', '$email', '$loginDate', '{$serverList[0]['serverIp']}', '$token', '$origin','')", $link);	

		//---------------将数据写入用户中心---------------------------
		$usrinfo = array();
		$usrinfo['guid'] = $guid;
		$usrinfo['usr_name'] = 'jof_j2me_' .$userName;			
		$usrinfo['game_id'] = 1;
		$usrinfo['usr_pass'] = $encrPwd;		
		$usrinfo['login_date'] = time();
		$usrinfo['usr_email'] = null;
		$usrinfo['usr_phone'] = $email != null ? $email : null;
		$usrinfo['real_name'] = null;
		$usrinfo['idcard_number'] = null;
		$usrinfo['game_qd'] =  'j2me';
		$usrinfo['nick_name'] = $nickname;
		$usrinfo['sex'] = null;
		insertUser($usrinfo);
		//---------------将数据写入用户中心---------------------------

		// 更新内存
		//isDupUser($userName);			
		// 加密token
		$token = $rsa->privEncrypt($token);			
		// 返回服务器列表
		$slist = array();
		$rowNum = count($serverList);
		$newServer = array();
		for($i = 0; $i < $rowNum; $i++) {
			if($serverList[$i]['origin'] == $origin) {
				// 是否传入版本号
				if($version != null && $version != 0) {
					$_ver_cmp_var = ver_cmp($version, $serverList[$i]['version']);
					// 客户端大于服务端
					if(0 < $_ver_cmp_var){
						continue;
					} // 客户端小于服务端
					else if(0 > $_ver_cmp_var){
						$slist[$i]['xysj'] = 1;
					}
				} else if($serverList[$i]['version'] != 0){
					continue;
				}
				$slist[$i]['id'] = str_pad($serverList[$i]['id'], 3, 'e', STR_PAD_LEFT);
				$slist[$i]['mc'] = $serverList[$i]['serverName'];
				$slist[$i]['url'] = $serverList[$i]['serverIp'];
				$slist[$i]['js'] = 0;
				$slist[$i]['sj'] = intval($serverList[$i]['openTime']);
				if($serverList[$i]['status'] == '火爆') {
					$slist[$i]['zt'] = 1;
				} else {
					$slist[$i]['zt'] = 2;
				}
				if($serverList[$i]['recommend'] == 1) {
					$slist[$i]['tj'] = 1;
				} else {
					$slist[$i]['tj'] = 0;
				}
			}
		}							
		$returnValue['status'] = 0;		
		$returnValue['userId'] = $guid;
		$returnValue['token'] = urlencode($token);
		$returnValue['slist'] = array_values($slist);
	} else {
				// 身份验证
		$pwd = md5($pwd);			
		if($userName != $userInfo['userName'] || $pwd != $userInfo['pwd']){
			$returnValue['status'] = 1022;
			$returnValue['message'] = '账号或密码不正确，请重新输入！';
			$returnValue['rsn'] = intval(_get('ssn'));	
			echo json_encode($returnValue);				
			exit;
		}	
		//---------------更新用户渠道信息---------------------------	
		$ucenter_un = 'jof_j2me_' .$userName;
		$ucenter_uinfo = isUcDupUser($ucenter_un);
		if($ucenter_uinfo != false) { // 用户中心已有数据
			if($ucenter_uinfo['game_qd'] == '') { // 渠道为空则更新渠道信息
				updateUserQd('j2me', $userName);
			}
		}
		// 生成令牌
		$token = $userName . '-' . $userInfo['uid'] . '-' . $loginDate;				
		// 获取用户登入服务器信息			
		if(strpos($userInfo['enterServers'], ':{') != false) {
			$userLoginedServer = @unserialize($userInfo['enterServers']);
		} else {
			$userLoginedServer = false;
		}
		// 返回服务器列表
		$slist = array();
		$rowNum = count($serverList);
		$newServer = array();
		for($i = 0; $i < $rowNum; $i++) {
			if($serverList[$i]['origin'] == $origin) {
				// 是否传入版本号
				if($version != null && $version != 0) {
					$_ver_cmp_var = ver_cmp($version, $serverList[$i]['version']);
					// 客户端大于服务端
					if(0 < $_ver_cmp_var){
						continue;
					} // 客户端小于服务端
					else if(0 > $_ver_cmp_var){
						$slist[$i]['xysj'] = 1;
					}
				} else if($serverList[$i]['version'] != 0){
					continue;
				}
				$slist[$i]['id'] = str_pad($serverList[$i]['id'], 3, 'e', STR_PAD_LEFT);
				$slist[$i]['mc'] = $serverList[$i]['serverName'];
				$slist[$i]['url'] = $serverList[$i]['serverIp'];
				$slist[$i]['sj'] = intval($serverList[$i]['openTime']);				
				if($userLoginedServer != false && array_key_exists($serverList[$i]['serverIp'], $userLoginedServer)) {
					$slist[$i]['dl'] = intval($userLoginedServer[$serverList[$i]['serverIp']]);
				}
				
				if($serverList[$i]['status'] == '火爆') {
					$slist[$i]['zt'] = 1;
				} else {
					$slist[$i]['zt'] = 2;
				}
				
				if($serverList[$i]['recommend'] == 1) {
					$slist[$i]['tj'] = 1;
				} else {
					$slist[$i]['tj'] = 0;
				}
				
				if($userLoginedServer != false && array_key_exists($serverList[$i]['serverIp'], $userLoginedServer)) {
					$slist[$i]['js'] = 1;
				} else {
					$slist[$i]['js'] = 0;
				}
			}
		}			
		// 更新DB			
		mysql_query("update ol_users set lastLoging='$loginDate', verifyCallBack='$token' where userName = '$userName' limit 1", $link);			
		// 更新内存
		//$userInfo['lastLoging'] = $loginDate;			
		//$userInfo['verifyCallBack'] = $token;
		//$mc->set('AUTH_userInfo_'.$userName, $userInfo, 0, 3600);			
		// 加密token			
		$token = $rsa->privEncrypt($token);			
		// 返回值
		$returnValue['status'] = 0;		
		$returnValue['userId'] = $userInfo['uid'];
		$returnValue['token'] = urlencode($token);
		$returnValue['slist'] = array_values($slist);
	}
}

	// 返回结果
	$returnValue['rsn'] = intval(_get('ssn'));	
	echo json_encode($returnValue);