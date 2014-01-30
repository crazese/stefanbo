<?php
require 'src/facebook.php';
if ($jcid == 8 || $jcid == 31) {
	$appId = '577721408916792';
	$secret = 'c6d6698e43792bd520b40cca54f938b8';
} else {
	$appId = '489217221125581';
	$secret = 'b5fb1597b032bb820d5b52168e34f7f8';	
}
$facebook = new Facebook(array(
  'appId'  => $appId,       //510494985668460(test)
  'secret' => $secret,      //af4da245f4ac5c48fc7881725217522d(test)
));
$kszc = _get('kszc');
$task = _get('task');
if (empty($kszc)) {
	$rdata = urldecode(_get('data'));
	$xml = simplexml_load_string($rdata);
	$xml = json_encode($xml);
	$xml = json_decode($xml);
	$token = $xml->token;
}
if( $task == null || $task != 'sdkLogin' ) {
	$returnValue['status'] = 3;
	$returnValue['message'] = '非法请求';
	echo json_encode($returnValue);
	exit;
} else {
	function genUsername($i=1) {
		include(dirname(__FILE__).'/includes/name_tw.php');
		mysql_query("lock table ol_users write");
		$result = mysql_query("select max(id) as maxid from ol_users");
		$rows = mysql_fetch_array($result);
		$maxid = $rows['maxid'] + $i;
		$userName = 'guest'.$maxid;
		$userInfo = isDupUser($userName);
		if ($userInfo != false) {
			$returnValue = genUsername($i+1);
		} else {		
			$randStr = str_shuffle('1234567890');
			$rand = substr($randStr,0,6);					
			$returnValue = array('uname'=>$userName,'pwd'=>$rand,'nickname'=>getFirstName());
		}
		mysql_query("unlock tables");
		return $returnValue;
	}
	if (empty($kszc)) {
		$facebook->setAccessToken($token); 
		$facebook->setExtendedAccessToken();
		$user = $facebook->getUser();
		if (empty($user) || $user == 0) {
			$returnValue['status'] = 3;
			$returnValue['message'] = '身份验证失败';
			echo json_encode($returnValue);
			exit;		
		}
	}
	//$user = $facebook->api('/me/friends');	
	// 初始化加解密工具类
	$rsa = new Rsa(KEY_PATH);	
	// 当前时间戳
	$loginDate = time();	
	// 获取服务器列表
	//if (!($serverList = $mc->get('AUTH_serverList'))) {
		$serverList = array();
		$res = mysql_query('select * from ol_serverlist', $link);
		while($serverList[] = mysql_fetch_array($res, MYSQL_ASSOC));
		array_pop($serverList);
		//$mc->set('AUTH_serverList', $serverList, 0, 3600);			
	//}		
	if ($kszc == 1) {
		$genInfo = genUsername();
		$userName = $genInfo['uname'];
		$pwd = $genInfo['pwd'];
		$userInfo = false;
		$nickName = $genInfo['nickname'];
	} else {
		$userName = $user;
		$nickName = $user;
	}	
	//$userName = $user;
	//$nickName = $user;
	$userInfo = isDupUser($userName);
	$pwd = '';
	$newPwd = '';
	$yzm = '';
	$sjh = '';	
	$origin = _get('yhly');
	$email = '';
	$version = _get('vn');
	$AllList = NULL;	
	if ($userInfo == false) {
		// 生成guid
		$trans = array('{'=>'', '}'=>'', '-'=>'');
		$guid = $userName;				
		// 生成令牌
		$token = $userName . '-' . $guid . '-' . $loginDate;				
		// 添加用户
		$encrPwd = md5($pwd);
		mysql_query("insert into ol_users(uid, userName, pwd, email, lastLoging, enterServers, verifyCallBack, origin,userName2,deviceid)
		 values('$guid', '$userName', '$encrPwd', '$email', '$loginDate', '{$serverList[0]['serverIp']}', '$token', '$origin','','$deviceid')", $link);		

		//---------------将数据写入用户中心---------------------------
		$usrinfo = array();
		$usrinfo['guid'] = $guid;
		$usrinfo['usr_name'] = 'jof_' ._get('jcid'). '_' .$userName;			
		$usrinfo['game_id'] = 1;
		$usrinfo['usr_pass'] = $encrPwd;		
		$usrinfo['login_date'] = time();
		$usrinfo['usr_email'] = null;
		$usrinfo['usr_phone'] = $email != null ? $email : null;
		$usrinfo['real_name'] = null;
		$usrinfo['idcard_number'] = null;
		$usrinfo['game_qd'] =  _get('jcid');
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
				$tag = $serverList[$i]['version'] * 1000;
				$AllList[$tag] = $serverList[$i];
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
		if (empty($slist) && !empty($AllList)) {
			$AllList_min[0] = min($AllList);
			foreach ($AllList_min as $listKey => $listValue) {
				$slist[$listKey]['id'] = str_pad($listValue['id'], 3, 'e', STR_PAD_LEFT);
				$slist[$listKey]['mc'] = $listValue['serverName'];
				$slist[$listKey]['url'] = $listValue['serverIp'];
				$slist[$listKey]['js'] = 0;
				$slist[$listKey]['sj'] = intval($listValue['openTime']);
				if($listValue['status'] == '火爆') {
					$slist[$listKey]['zt'] = 1;
				} else if($listValue['status'] == '良好') {
					$slist[$listKey]['zt'] = 2;
				} else if($listValue['status'] == '维护') {
					$slist[$listKey]['zt'] = 3;
				}
				if($listValue['recommend'] == 1) {
					$slist[$listKey]['tj'] = 1;
				} else {
					$slist[$listKey]['tj'] = 0;
				}	
			}			
		}										
		$returnValue['status'] = 0;		
		$returnValue['userId'] = $guid;
		$returnValue['token'] = urlencode($token);
		$returnValue['slist'] = array_values($slist);
		$returnValue['sjhm'] = $email;			
	} else {
		// 生成令牌
		$token = $userName . '-' . $userInfo['uid'] . '-' . $loginDate;				
		// 获取用户登入服务器信息			
		if(strpos($userInfo['enterServers'], ':{') != false) {
			$userLoginedServer = @unserialize($userInfo['enterServers']);
		} else {
			$userLoginedServer = false;
		}

		//---------------更新用户渠道信息---------------------------	
		$ucenter_un = 'jof_' ._get('jcid'). '_' .$userName;
		$ucenter_uinfo = isUcDupUser($ucenter_un);
		if($ucenter_uinfo != false) { // 用户中心已有数据
			if($ucenter_uinfo['game_qd'] == '') { // 渠道为空则更新渠道信息
				updateUserQd(_get('jcid'), $userName);
			}
		}
		//---------------更新用户渠道信息结束-----------------------

		// 返回服务器列表
		$slist = array();
		$rowNum = count($serverList);
		$newServer = array();
		for($i = 0; $i < $rowNum; $i++) {
			if($serverList[$i]['origin'] == $origin) {
				$tag = $serverList[$i]['version'] * 1000;
				$AllList[$tag] = $serverList[$i];
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
		if (empty($slist) && !empty($AllList)) {
			$AllList_min[0] = min($AllList);
			foreach ($AllList_min as $listKey => $listValue) {
				$slist[$listKey]['id'] = str_pad($listValue['id'], 3, 'e', STR_PAD_LEFT);
				$slist[$listKey]['mc'] = $listValue['serverName'];
				$slist[$listKey]['url'] = $listValue['serverIp'];
				$slist[$listKey]['sj'] = intval($listValue['openTime']);
				if($listValue['status'] == '火爆') {
					$slist[$listKey]['zt'] = 1;
				} else if($listValue['status'] == '良好') {
					$slist[$listKey]['zt'] = 2;
				} else if($listValue['status'] == '维护') {
					$slist[$listKey]['zt'] = 3;
				}
				if($listValue['recommend'] == 1) {
					$slist[$listKey]['tj'] = 1;
				} else {
					$slist[$listKey]['tj'] = 0;
				}
				if($userLoginedServer != false && array_key_exists($listValue['serverIp'], $userLoginedServer)) {
					$slist[$listKey]['js'] = 1;
				} else {
					$slist[$listKey]['js'] = 0;
				}					
			}			
		}						
		// 更新DB			
		mysql_query("update ol_users set lastLoging='$loginDate', verifyCallBack='$token',deviceid='$deviceid' where userName = '$userName' limit 1", $link);			
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
		$returnValue['sjhm'] = $userInfo['email'];						
	}	
}
	// 返回结果
	$returnValue['rsn'] = intval(_get('ssn'));	
	echo json_encode($returnValue);