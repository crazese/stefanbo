<?php
$task = _get('task');
$option = _get('option');
$rdata = urldecode(_get('data'));
if (!empty($rdata)) {
$xml = simplexml_load_string($rdata);
$xml = json_encode($xml);
$xml = json_decode($xml);	
$sid = $xml->sid;
}
if ($jcid == 29) {
	$serverId = 1980;	
	$gameId = 506135;
} else {
	$serverId = 1215;
	$gameId = 64431;
}
//socket访问
function http_post($host, $port, $path, $data) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://'.$host.$path);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}	

//检测UCSID合法性以及获取相关信息
function checksid($sid) {
	global $_SGLOBAL, $_SC,$gameId,$serverId;
	$id = intval($_SGLOBAL['timestamp']);
	$data = array('sid'=>$sid);
	$sign = md5($_SC['cpId'].'sid='.$sid.$_SC['apiKey']);
	$game = array('cpId'=>intval($_SC['cpId']),'gameId'=>$gameId,'channelId'=>intval($_SC['channelId']),'serverId'=>$serverId);
	$postdata = json_encode(array("id"=>$id,"service"=>"ucid.user.sidInfo","data"=>$data,"game"=>$game,"sign"=>$sign,"encrypt"=>"md5"));
	$ucdata = http_post($_SC['apiUrl'],80,'/ss',$postdata);
	if (!empty($ucdata)) {
		$userInfo = json_decode($ucdata,true);
		if ($userInfo['state']['code'] == 1) {
			$value['ucid'] = $userInfo['data']['ucid'];
			$value['username'] = $userInfo['data']['nickName'];
		} else {
			$value['status'] = intval($userInfo['state']['code']);
			$value['message'] = $userInfo['state']['msg'];    			
		}    		
	} else {
		$value['status'] = 3;
		$value['message'] = '连接服务器失败！';    		
	}
	return $value;
}

//通知UC登录同步接口
function loginsynuc($gameUser) {
	global $_SGLOBAL, $_SC;
	$id = intval($_SGLOBAL['timestamp']);
	$sign = md5($_SC['cpId'].'gameUser='.$gameUser.$_SC['apiKey']);
	$game = array('cpId'=>intval($_SC['cpId']),'gameId'=>intval($_SC['gameId']),'channelId'=>intval($_SC['channelId']),'serverId'=>intval($_SC['serverId']));
	$data = array('gameUser'=>$_SC['gameUser']);
	$postdata = json_encode(array("id"=>$id,"service"=>"ucid.bind.create","data"=>$data,"game"=>$game,"sign"=>$sign,"encrypt"=>"md5"));
	$ucdata = http_post($_SC['apiUrl'],80,'/ss',$postdata);
	if (!empty($ucdata['state'])) {
		if ($ucdata['state']['code'] == 1) {
			return $ucdata['data']['ucid'];
		} else {
			return false;
		}
	} else {
		return false;
	}
}

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
	//if (!($serverList = $mc->get('AUTH_serverList'))) {
		$serverList = array();
		$res = mysql_query('select * from ol_serverlist', $link);
		while($serverList[] = mysql_fetch_array($res, MYSQL_ASSOC));
		array_pop($serverList);
		//$mc->set('AUTH_serverList', $serverList, 0, 3600);			
	//}	
	$userNameInfo = checksid($sid);	
	if (empty($userNameInfo['ucid'])) {
		$returnValue['status'] = 1030;
		$returnValue['message'] = '验证SID失败！';
		$returnValue['rsn'] = intval(_get('ssn'));	
		echo json_encode($returnValue);
		exit;					
	}
	$userName = $userNameInfo['ucid'];
	$nickName = $userNameInfo['username'];	
	$userInfo = isDupUser($userName);
	$pwd = '';
	$newPwd = '';
	$yzm = '';
	$sjh = '';	
	$origin = _get('yhly');
	if (empty($origin)) {
		$origin = 1;
	}
	$email = '';
	$version = _get('vn');
	$AllList = NULL;
	if ($userInfo == false) {
		if(!isset($_GET['vn'])) {
			$returnValue['status'] = 22;		
			$returnValue['message'] = sprintf("版本过老，请到专区下载最新安装文件！\n专区地址：http://qjsh.9game.cn");
			$returnValue['rsn'] = intval(_get('ssn'));	
			echo json_encode($returnValue);
			exit;
		}

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
				} else if($serverList[$i]['status'] == '良好') {
					$slist[$i]['zt'] = 2;
				} else if($serverList[$i]['status'] == '维护') {
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
		if(!isset($_GET['vn'])) {
			$returnValue['status'] = 22;		
			$returnValue['message'] = sprintf("版本过老，请到专区下载最新安装文件！\n专区地址：http://qjsh.9game.cn");
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
				} else if($serverList[$i]['status'] == '良好') {
					$slist[$i]['zt'] = 2;
				} else if($serverList[$i]['status'] == '维护') {
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