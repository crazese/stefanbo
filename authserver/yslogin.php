<?php
$task = _get('task');
class easou{
	private $key = '7DA95D6C9E5EFCEDBE83AF7B8D9FEC8B';
	private $appid = 1357;
	private $partnerId = 1000100010001009;
	private $host = 'sso.easou.com';
	//private $host = 'testsso.easou.com';
	private $path = array(
		'registerByName'=>'/api2/registByName.json',
		'requestMobileCode'=>'/api2/requestMobileCode.json',
		'regByMobilecode'=>'/api2/regByMobileCode.json',
		'autoRegist'=>'/api2/autoRegist.json',
		'login'=>'/api2/login.json',
		'validate'=>'/api2/validate.json',
		//'validate'=>'/api2/validateServiceTicket.json',
	);
	
	// 私有工具方法
	private function http_post($host, $port, $path, $data) {
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

	private function genSingn($content, $apiKey) {
		$content = $content['body'];
		ksort($content, SORT_ASC);
		$tmpContent = null;		
		foreach($content as $key=>$value) {
			if($tmpContent == null) {
				$tmpContent .= $key . '=' . $value;
			} else {
				$tmpContent .= '&' . $key . '=' . $value;
			}
		}
		$sign = md5($tmpContent . $apiKey);
		
		return $sign;
	}

	private function getResult($json) {
		$ret = json_decode($json, true);

		if($ret['head']['ret'] != 0) {
			$msg = '';
			if(isset($ret['desc'])) {
				foreach($ret['desc'] as $key=>$value) {
					$msg .= ' ' . $value['d'];
				}
			}
			if($msg != '')
				return $ret['head']['ret'] . ':' . $msg;
			else 
				return $ret['head']['ret'];
		} else {
			return $ret['body'];
		}
	}
	
	private function requestData($body,$func_name) {
		$sign = $this->genSingn($body, $this->key);			
		$head = array(
			'head'=>array('partnerId'=>intval($this->partnerId), 'appId'=>$this->appid,'sign'=>$sign)
		);			
		$data = $head + $body;
		$postdata = json_encode($data);	//echo $postdata;
		return $this->getResult($this->http_post($this->host, 80, $this->path[$func_name], $postdata));
	}
	
	// 用户名注册
	function registerByName($username, $password, $remember = 'false') {
		$body = array(
			'body'=>array('username'=>$username, 'password'=>$password, 'remember'=>$remember)
		);
		$func_name = __FUNCTION__;
		return $this->requestData($body, $func_name);
	}
	
	// 请求手机验证码
	function requestMobileCode($mobile) {
		$body = array(
			'body'=>array('mobile'=>$mobile)
		);
		$func_name = __FUNCTION__;
		return $this->requestData($body, $func_name);			
	}
	
	// 校验验证码
	function regByMobilecode($mobile, $password, $veriCode, $remember='false') {
		$body = array(
			'body'=>array('mobile'=>$mobile, 'password'=>$password, 'veriCode'=>$veriCode, 'remember'=>$remember)
		);
		$func_name = __FUNCTION__;
		return $this->requestData($body, $func_name);			
	}
	
	// 一键注册
	function autoRegist($remember='false') {
		$body = array(
			'body'=>array('remember'=>$remember)
		);
		$func_name = __FUNCTION__;
		return $this->requestData($body, $func_name);			
	}
	
	// 登录
	function login($username, $password, $remember='false') {
		$body = array(
			'body'=>array('userName'=>$username, 'password'=>$password, 'remember'=>$remember)
		);			
		$func_name = __FUNCTION__;
		return $this->requestData($body, $func_name);			
	}
	
	// 验证token
	function validate($token) {
		$body = array(
			'body'=>array('EASOUTGC'=>$token)
		);			
		$func_name = __FUNCTION__;
		return $this->requestData($body, $func_name);			
	}
}

$token = _get('token');
//检测$SessionId合法性以及获取相关信息
function checkSessionId() {
	global $token;

	$easou = new easou();

	$result = $easou->validate($token);
	if(is_array($result)) {
		// [id] [name] [nickName] [passwd] [sex]0男 1女 [status] //1正常 0失败
		$userinfo = $result['user']; 
		// age domain path token
		$token = $result['token'];
		// age domain path u
		//$u = $result['U'];
		//$userinfo;
		//var_export($token);
		//var_export($u);
	} else {// 需重新登录 http://sso.easou.com/quickLogin?gameId=1357&service=&auto=0
		//$msg = explode(':', $result);
		//echo $msg[1];	
		return null;
	}

	$info['nick_name'] = mysql_real_escape_string($userinfo['nickName']);
	$info['sexid'] = $userinfo['sex'];
	$info['id'] = $userinfo['id'];
	
	return $info;
}

if( $task == null || $task != 'sdkLogin' ) {
	$returnValue['status'] = 3;
	$returnValue['message'] = '非法请求';
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
	$userNameInfo = checkSessionId();
	if (empty($userNameInfo)) {
		$returnValue['status'] = 3;
		$returnValue['message'] = '请先登录宜搜';
		echo json_encode($returnValue);
		exit;					
	}
	$userName = $userNameInfo['id'];
	$userInfo = isDupUser($userName);
	$nick_name = $userNameInfo['nick_name'];
	$sex = $userNameInfo['sexid'];
	$pwd = '';
	$newPwd = '';
	$yzm = '';
	$sjh = '';	
	$origin = _get('ly');
	$email = '';
	$version = _get('vn');
	if ($userInfo == false) {
		// 生成guid
		$trans = array('{'=>'', '}'=>'', '-'=>'');
		$guid = $userName;			
		// 生成令牌
		$token = $userName . '-' . $guid . '-' . $loginDate;				
		// 添加用户
		$encrPwd = md5($pwd);
		mysql_query("insert into ol_users(uid, userName, pwd, email, lastLoging, enterServers, verifyCallBack, origin,userName2,sex)
		 values('$guid', '$userName', '$encrPwd', '$email', '$loginDate', '{$serverList[0]['serverIp']}', '$token', '$origin','$nick_name','$sex')", $link);		
		// echo "insert into ol_users(uid, userName, pwd, email, lastLoging, enterServers, verifyCallBack, origin,userName2,sex)		 values('$guid', '$userName', '$encrPwd', '$email', '$loginDate', '{$serverList[0]['serverIp']}', '$token', '$origin','$nick_name','$sex')";
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
		$returnValue['sjhm'] = $userInfo['email'];								
	}	
}
	// 返回结果
	$returnValue['rsn'] = intval(_get('ssn'));	
	echo json_encode($returnValue);