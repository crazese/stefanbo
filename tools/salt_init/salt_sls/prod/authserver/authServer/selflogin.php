<?php
$option = _get('option');
if( $task == null || $option == null 
	|| ($task != 'zcdyyhfuw' && $option != 'user') 
	|| ($task != 'drdyyhfuq' && $option != 'user')
	|| ($task != 'hqyxfuwlb' && $option != 'user')
	|| ($task != 'xgmm' && $option != 'user')
	|| ($task != 'xgsjhm' && $option != 'user')
	|| ($task != 'hqyzm' && $option != 'user')
	|| ($task != 'zhmm' && $option != 'user')
	) {
	$returnValue['status'] = 3;
	$returnValue['message'] = $lang['illegal_request'];
	echo json_encode($returnValue);
	exit;
} else {
	function genUsername($i=1) {
		global $_SC;
		include(dirname(__FILE__).'/includes/name_'.$_SC['lang'].'.php');
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
	// 初始化加解密工具类
	$rsa = new Rsa(KEY_PATH);
	
	// 当前时间戳
	$loginDate = time();
	
	// 获取服务器列表
	/*if (!($serverList = $mc->get('AUTH_serverList'))) {*/
	$serverList = array();
	$res = mysql_query('select * from ol_serverlist', $link);
	while($serverList[] = mysql_fetch_array($res, MYSQL_ASSOC));
	array_pop($serverList);
		//$mc->set('AUTH_serverList', $serverList, 0, 3600);			
	//}	

	if($task != 'hqyxfuwlb') {
		$userName = _get('zh');
		//$userName = $origin . '_' . $userName;
		$pwd = _get('mm');	
		$newPwd = _get('xmm');
		$yzm = _get('yzm');
		$sjh = _get('sjhm');
		$version = _get('vn');
		$kszc = _get('kszc');
		if ($kszc == 1) {
			$genInfo = genUsername();
			$userName = $genInfo['uname'];
			$pwd = $genInfo['pwd'];
			$userInfo = false;
			$nickname = $genInfo['nickname'];
		} else {
			// 是否是新用户
			$userInfo = isDupUser($userName);
			$nickname = '';
		}
	}
	$AllList = NULL;

	// 注册
	if($task == 'zcdyyhfuw') {
		$email = _get('e');
		$origin = _get('ly');
		
		if($userInfo == false) {
			// 生成guid
			$trans = array('{'=>'', '}'=>'', '-'=>'');
			//$guid = strtr(com_create_guid(), $trans);				
			$res = mysql_query('select UUID()  as guid', $link);
			$guid = mysql_fetch_array($res, MYSQL_ASSOC);
			$guid = strtr($guid['guid'], $trans);
										
			// 生成令牌
			$token = $userName . '-' . $guid . '-' . $loginDate;	
			
			// 添加用户
			$encrPwd = md5($pwd);
			mysql_query("insert into ol_users(uid, userName, pwd, email, lastLoging, enterServers, verifyCallBack, origin,userName2,deviceid)
			 values('$guid', '$userName', '$encrPwd', '$email', '$loginDate', '{$serverList[0]['serverIp']}', '$token', '$origin','$nickname','$deviceid')", $link);

			//---------------将数据写入用户中心---------------------------
			$usrinfo = array();
        	$usrinfo['guid'] = $guid;
			if(_get('jcid') != 0) {
				$usrinfo['usr_name'] = 'jof_' ._get('jcid'). '_' .$userName;
			} else {
				$usrinfo['usr_name'] = 'jof_self_' .$userName;
			}
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
					if($serverList[$i]['status'] == $lang['hot']) {
						$slist[$i]['zt'] = 1;
					} else if($serverList[$i]['status'] == $lang['ok']) {
						$slist[$i]['zt'] = 2;
					} else if($serverList[$i]['status'] == $lang['fix']) {
						$slist[$i]['zt'] = 3;
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
					if($listValue['status'] == $lang['hot']) {
						$slist[$listKey]['zt'] = 1;
					} else if($listValue['status'] == $lang['ok']) {
						$slist[$listKey]['zt'] = 2;
					} else if($listValue['status'] == $lang['fix']) {
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
			if ($kszc == 1) {
				$returnValue['kszc'] = intval($kszc);
				$returnValue['kszh'] = $userName;
				$returnValue['ksmm'] = $pwd;
			} else {
				$returnValue['kszc'] = 0;
			}
			//$returnValue['serverName'] = $serverList[0]['serverName'];
			//$returnValue['url'] = $serverList[0]['serverIp'];
			//$returnValue['token'] = base64_encode($token);
			$returnValue['token'] = urlencode($token);
			$returnValue['slist'] = array_values($slist);
			$returnValue['sjhm'] = $email;
		} else {
			$returnValue['status'] = 1030;
			$returnValue['message'] = $lang['used_account'];		
			$returnValue['rsn'] = intval(_get('ssn'));	
		}
	} else if($task == 'drdyyhfuq') { // 登录
		$origin = _get('ly');

			// 帐号不存在
			if($userInfo == false) {
				$returnValue['status'] = 1021;
				$returnValue['message'] = $lang['no_find_account'];
				$returnValue['rsn'] = intval(_get('ssn'));	
				echo json_encode($returnValue);				
				exit;		
			}
			
			// 身份验证
			$pwd = md5($pwd);			
			if($userName != $userInfo['userName'] || $pwd != $userInfo['pwd']){
				$returnValue['status'] = 1022;
				$returnValue['message'] = $lang['passwd_err'];
				$returnValue['rsn'] = intval(_get('ssn'));	
				echo json_encode($returnValue);				
				exit;
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
					
					if($serverList[$i]['status'] == $lang['hot']) {
						$slist[$i]['zt'] = 1;
					} else if($serverList[$i]['status'] == $lang['ok']) {
						$slist[$i]['zt'] = 2;
					} else if($serverList[$i]['status'] == $lang['fix']) {
						$slist[$i]['zt'] = 3;
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
					if($listValue['status'] == $lang['hot']) {
						$slist[$listKey]['zt'] = 1;
					} else if($listValue['status'] == $lang['ok']) {
						$slist[$listKey]['zt'] = 2;
					} else if($listValue['status'] == $lang['fix']) {
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
			//$returnValue['serverName'] = $serverList[0]['serverName'];
			//$returnValue['url'] = $serverList[0]['serverIp'];
			//$returnValue['token'] = base64_encode($token);
			$returnValue['token'] = urlencode($token);
			$returnValue['slist'] = array_values($slist);
			$returnValue['sjhm'] = $userInfo['email'];			
	} else if($task == 'xgmm'){ // 修改密码				
		// 用户不存在
		if($userInfo == false) {
			$returnValue['status'] = 21;
			$returnValue['message'] = $lang['no_find_account'];
			$returnValue['rsn'] = intval(_get('ssn'));	
			echo json_encode($returnValue);					
			exit;	
		}
		
		$crypt_mm = md5($pwd);
		if($userName == $userInfo['userName'] && $crypt_mm == $userInfo['pwd']) {
			// 更新DB	
			$crypt_xmm = md5($newPwd);
			mysql_query("update ol_users set `pwd`='$crypt_xmm' where id='{$userInfo['id']}' limit 1", $link);
			
			// 更新内存
			//$userInfo['pwd'] = $crypt_xmm;				
			//$mc->set('AUTH_userInfo_'.$userName, $userInfo, 0, 3600);
		} else {
			$returnValue['status'] = 21;
			$returnValue['message'] = $lang['passwd_err'];
			$returnValue['rsn'] = intval(_get('ssn'));	
			echo json_encode($returnValue);					
			exit;	
		}
		
		//$regex = "/13[0-9]{9}|15[0|1|2|3|5|6|7|8|9]\d{8}|18[0|5|6|7|8|9]\d{8}/";
		//$phone = preg_match($regex, $userInfo['email']);
		$phone = strlen($userInfo['email']) == 11 ? true : false;
		
		if($phone) {
			$returnValue['bdsj'] = 1;
		} else {
			$returnValue['bdsj'] = 0;
		}
		
		$returnValue['status'] = 0;	
		
	} else if($task == 'xgsjhm'){ // 修改手机码				
		// 用户不存在
		if($userInfo == false) {
			$returnValue['status'] = 21;
			$returnValue['message'] = $lang['no_find_account'];
			$returnValue['rsn'] = intval(_get('ssn'));	
			echo json_encode($returnValue);					
			exit;	
		}
		
		//$regex = "/13[0-9]{9}|15[0|1|2|3|5|6|7|8|9]\d{8}|18[0|5|6|7|8|9]\d{8}/";
		//$phone = preg_match($regex, $sjh);
		//$phone = strlen($sjh) == 11 ? true : false;
		$phone = true;
		
		if($phone) {
			$crypt_mm = md5($pwd);
			if($userName == $userInfo['userName'] && $crypt_mm == $userInfo['pwd']) {
				// 更新DB				
				mysql_query("update ol_users set `email`='$sjh' where id='{$userInfo['id']}' limit 1", $link);
				
				// 更新内存
				//$userInfo['email'] = $sjh;				
				//$mc->set('AUTH_userInfo_'.$userName, $userInfo, 0, 3600);
			} else {
				$returnValue['status'] = 21;
				$returnValue['message'] = $lang['passwd_err'];
				$returnValue['rsn'] = intval(_get('ssn'));	
				echo json_encode($returnValue);					
				exit;	
			}		
		} else {
			$returnValue['status'] = 21;
			$returnValue['message'] = $lang['need_right_phone'];
			$returnValue['rsn'] = intval(_get('ssn'));	
			echo json_encode($returnValue);					
			exit;	
		}
		
		$returnValue['status'] = 0;	
		
	} else if($task == 'hqyzm'){ // 获取验证码				
		// 用户不存在
		if($userInfo == false) {
			$returnValue['status'] = 21;
			$returnValue['message'] = $lang['no_find_account'];
			$returnValue['rsn'] = intval(_get('ssn'));	
			echo json_encode($returnValue);					
			exit;	
		}
		
		$year = date('Y');
		$month = date('m');
		$day = date('d');
		
		if($userInfo['verify_date'] != '') {
			$players_year = date('Y', $userInfo['verify_date']);
			$players_month = date('m',$userInfo['verify_date']);
			$players_day = date('d', $userInfo['verify_date']);	

			if(!DEBUG) {
				if(($players_year < $year || $players_month < $month) || ($players_year == $year && $players_month == $month && $players_day < $day)) {
					
				} else {
					$returnValue['status'] = 21;
					$returnValue['message'] = $lang['test_again'];
					$returnValue['rsn'] = intval(_get('ssn'));	
					echo json_encode($returnValue);					
					exit;				
				}
			}
		}
		

		//$regex = "/13[0-9]{9}|15[0|1|2|3|5|6|7|8|9]\d{8}|18[0|5|6|7|8|9]\d{8}/";
		//$phone = preg_match($regex, $userInfo['email']);
		//$phone = strlen($userInfo['email']) == 11 ? true : false;
		$phone = false;
		if(preg_match('/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/', $userInfo['email'])){
			$phone = 'email';
		}else if(strlen($userInfo['email']) == 11){
			$phone = 'phone';
		}
		
		if($phone) {
			if('phone' == $phone){
				$param1 = $userInfo['email'];
				$param2 = strtoupper(md5($userInfo['email'] . SMS_KEY));
				
				$url = SMS_ADDR . '?pn=' . $param1 . '&pne=' . $param2;
				$result = vget($url);
			
				list($state, $verify, $verifySjh) = explode('|', $result);
			
				// $state == 0成功
				if($state || ($param1 != $verifySjh)) {
					$returnValue['status'] = 1001;
					$returnValue['message'] = $lang['send_SMS_err'];
					$returnValue['rsn'] = intval(_get('ssn'));	
					echo json_encode($returnValue);					
					exit;	
				} else {
					$currDate = time();
					// 更新DB				
					mysql_query("update ol_users set `verify`='$verify',verify_date='$currDate' where id='{$userInfo['id']}' limit 1", $link);
				
					// 更新内存
					//$userInfo['verify'] = $verify;
					//$userInfo['verify_date'] = $currDate;
					//$mc->set('AUTH_userInfo_'.$userName, $userInfo, 0, 3600);
				}
			}else if('email' == $phone){
				$email_url = $userInfo['email'];
				$uid       = $userInfo['id'];
				$ver_code  = md5(microtime());

				$currDate = time();
				// 更新DB				
				mysql_query("update ol_users set `verify`='$ver_code',verify_date='$currDate' where id='{$uid}' limit 1", $link);
				
				sendMail($email_url, $ver_code, $userInfo['userName']);
			}
		} else {
			$returnValue['status'] = 1001;
			$returnValue['message'] = $lang['need_phone_bind'];
			$returnValue['rsn'] = intval(_get('ssn'));
			echo json_encode($returnValue);
			exit;
		}
		
		$returnValue['status'] = 0;
		
	} else if($task == 'zhmm'){ // 找回密码
		// 用户不存在
		if($userInfo == false) {
			$returnValue['status'] = 21;
			$returnValue['message'] = $lang['no_find_account'];
			$returnValue['rsn'] = intval(_get('ssn'));
			echo json_encode($returnValue);
			exit;
		}
		
		$year = date('Y');
		$month = date('m');
		$day = date('d');
		$players_year = date('Y', $userInfo['verify_date']);
		$players_month = date('m',$userInfo['verify_date']);
		$players_day = date('d', $userInfo['verify_date']);

		if(!DEBUG) {
			if(($players_year < $year || $players_month < $month) || ($players_year == $year && $players_month == $month && $players_day < $day)) {
			
			} else {
				if($userInfo['verify'] == '') {
					$returnValue['status'] = 21;
					$returnValue['message'] = $lang['verify_code_err'];
					$returnValue['rsn'] = intval(_get('ssn'));
					echo json_encode($returnValue);
					exit;
				}
			}
		}
	
		$now = time();
		$expire = ($now - intval($userInfo['verify_date'])) >= 1800 ? true : false;
		if(!$expire) {
			if($yzm == $userInfo['verify']) {
				// 更新DB
				$crypt_xmm = md5($newPwd);
				if(!DEBUG) {
					mysql_query("update ol_users set `pwd`='$crypt_xmm',verify='' where id='{$userInfo['id']}' limit 1", $link);
				} else {
					mysql_query("update ol_users set `pwd`='$crypt_xmm',verify='',verify_date='' where id='{$userInfo['id']}' limit 1", $link);
					$userInfo['verify_date'] = '';
				}
				
				
				// 更新内存
				//$userInfo['verify'] = '';
				//$userInfo['pwd'] = $crypt_xmm;				
				//$mc->set('AUTH_userInfo_'.$userName, $userInfo, 0, 3600);
			} else {
				$returnValue['status'] = 21;
				$returnValue['message'] = $lang['verify_code_err'];
				$returnValue['rsn'] = intval(_get('ssn'));
				echo json_encode($returnValue);
				exit;
			}
		} else {
			$returnValue['status'] = 21;
			$returnValue['message'] = $lang['verify_code_err'];
			$returnValue['rsn'] = intval(_get('ssn'));
			echo json_encode($returnValue);
			exit;
		}
		
		$returnValue['status'] = 0;
	}
	
	// 返回结果
	$returnValue['rsn'] = intval(_get('ssn'));	
	echo json_encode($returnValue);
}