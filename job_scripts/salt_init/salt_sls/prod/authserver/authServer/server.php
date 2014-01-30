<?php
include(dirname(__FILE__) . '/config.php');

$link = mysql_connect($_SC['dbhost'], $_SC['dbuser'], $_SC['dbpw']);
mysql_selectdb($_SC['dbname'], $link);
mysql_query("set names 'utf8'", $link);

//$mc = new Memcache;
//$mc->addServer($MemcacheList[0], $Memport);

class mysoapclass {
	var $Authenticated;
	var $Username;
	var $Password;
	var $Ip;
	var $client;
	
	function __construct() {	
		global $rsa,$link;//,$mc;
			
		$hdr = file_get_contents("php://input");	
		if (strpos($hdr,'<ns2:authenticate>')===false) {
		    $hdr = null;
		} else {			
		    $hdr = explode('<username>',$hdr);			    
		    $hdr = str_replace('<password>', '', $hdr[1]);
		    $hdr = str_replace('</password>', '', $hdr);
		    $hdr = explode('</ns2:authenticate>', $hdr);
		    $hdr = explode('</username>', $hdr[0]);
		    $tmp = explode('<ip>', $hdr[1]);
		    $hdr[1] = $tmp[0];
		    $hdr[2] = str_replace('</ip>', '', $tmp[1]);				   
		    unset($tmp);
		}
		
		// Decrypt
		$rsa = new Rsa(KEY_PATH);
		/*$this->Username = $rsa->pubDecrypt($hdr[0]);
		$this->Password = $rsa->pubDecrypt($hdr[1]);
		$this->Ip = $rsa->pubDecrypt($hdr[2]);*/	
		$this->Username = $hdr[0];
		$this->Password = $hdr[1];
		$this->Ip = $hdr[2];	

		// 获取服务器列表
		//if (!($serverList = $mc->get('AUTH_serverList'))) {
			$serverList = array();
			$res = mysql_query('select * from ol_serverlist', $link);
			while($serverList[] = mysql_fetch_array($res, MYSQL_ASSOC));
			array_pop($serverList);
		//	$mc->set('AUTH_serverList', $serverList, 0, 3600);			
		//}
			
		foreach ($serverList as $key=>$value) {
			$this->client[] = $value['serverIp'];
		}
		//$this->client[] = WS_CLIENT;
		if(!strpos(WS_CLIENT, ',')) {
			$this->client[] = WS_CLIENT;
		} else {
			$tmpClient = explode(',', WS_CLIENT);
			foreach($tmpClient as $k=>$v) {
				$this->client[] = $v;
			}
		}	
	}
	
	public function authenticate(){	
		if(in_array($this->Ip, $this->client)) {
			$this->Authenticated = true;
		} else {
			$this->Authenticated = false;
		}		
	}

	public function getuidinfo($token) {
		global $rsa;
		$this->authenticate();
		if($this->Authenticated){
			$raw_token = $rsa->pubDecrypt($token);
			$raw_loginDate = explode('-', $raw_token);
			return $raw_token;		
		} else {
			return 3;
		}
	}
	
	
	public function test($token,$ver=1){
		global $link, $rsa;//,$mc;

		$this->authenticate();
		if($this->Authenticated){				
			//$raw_token = $rsa->pubDecrypt(base64_decode($token));			
			$raw_token = $rsa->pubDecrypt($token);
			$raw_loginDate = explode('-', $raw_token);
			$userInfo = isDupUser($raw_loginDate[0]);
			$raw_loginDate = $raw_loginDate[2];			
					
			$nowTs = time();
			//file_put_contents("/tmp/log.txt", "{$userInfo['verifyCallBack']} == $raw_token");
			if($userInfo['verifyCallBack'] == $raw_token && ($nowTs - $raw_loginDate) < 120) {
				if(strpos($userInfo['enterServers'], ':{') != false) {
					$enterServers = unserialize($userInfo['enterServers']);
				} else {
					$enterServers = false;
				}
				$enterSerArr = array();
				if($enterServers != false) {
					$enterSerArr = $enterServers;						
					$enterSerArr[$this->Ip] = time();
				} else {
					$enterServers = array();
				}
				
				//if(!in_array($this->Ip, $enterServers)) {				
				if(!array_key_exists($this->Ip, $enterServers)) {				
					$enterSerArr[$this->Ip] = time();									
				}
				$enterServers = serialize($enterSerArr);
				
				// 更新DB				
				mysql_query("update ol_users set enterServers='{$enterServers}', verifyCallBack='' where userName = '{$userInfo['userName']}' limit 1", $link);				
											
				// 更新内存				
				//$userInfo['verifyCallBack'] = '';
				//$userInfo['enterServers'] = $enterServers;
				//$mc->set('AUTH_userInfo_'.$userInfo['userName'], $userInfo, 0, 3600);
				if ($ver == 1) {
					return $userInfo['uid'];
				} else {
					return $userInfo;
				}
			} else {
				return 3;
			}			
		} else {
			return 3;
		}
	}
	
	public function getServerList(){
		global $link, $rsa;

		$this->authenticate();
		if($this->Authenticated){				
			// 获取服务器列表
			//if (!($serverList = $mc->get('AUTH_serverList'))) {
				$serverList = array();
				$res = mysql_query('select * from ol_serverlist', $link);
				while($serverList[] = mysql_fetch_array($res, MYSQL_ASSOC));
				array_pop($serverList);
				//$mc->set('AUTH_serverList', $serverList, 0, 3600);			
			//}

			unset($serverList['serverOnlineNum']);					
			return serialize($serverList);
		} else {
			//return "$this->Ip" . json_encode($this->client);
			return 3;
		}
	}
	
	public function setServerList($list){
		global $link, $rsa;

		$this->authenticate();
		if($this->Authenticated){				
			// 获取服务器列表
			//if (!($serverList = $mc->get('AUTH_serverList'))) {
				$serverList = array();
				$res = mysql_query('select * from ol_serverlist', $link);
				while($serverList[] = mysql_fetch_array($res, MYSQL_ASSOC));
				array_pop($serverList);
				//$mc->set('AUTH_serverList', $serverList, 0, 3600);			
			//}	
					
			$cnt = count($serverList);
			$sList = array();
			for($i = 0; $i < $cnt; $i++) {
				$sList[$serverList[$i]['id']] = $serverList[$i];
			}
			
			$list = unserialize($list);
			foreach($list as $key=>$value) {
				if(!array_key_exists($value['id'], $sList)) {
					mysql_query("insert into ol_serverlist values(null, '{$value['serverName']}', '{$value['serverIp']}', '{$value['status']}', '{$value['recommend']}', '{$value['openTime']}', 0,{$value['origin']},{$value['version']})", $link);										
				} else {					
					mysql_query("update ol_serverlist set serverName='{$value['serverName']}', serverIp='{$value['serverIp']}', status='{$value['status']}', recommend='{$value['recommend']}', openTime='{$value['openTime']}', origin='{$value['origin']}', version='{$value['version']}'  where id = '{$value['id']}' limit 1", $link);
				}
			}
			
			/*$mc->delete('AUTH_serverList');
			if (!($serverList = $mc->get('AUTH_serverList'))) {
				$res = mysql_query('select * from ol_serverlist', $link);
				while($serverList[] = mysql_fetch_array($res, MYSQL_ASSOC));
				array_pop($serverList);
				$mc->set('AUTH_serverList', $serverList, 0, 3600);			
			}*/
			return 0;
		} else {
			return 3;
		}
	}
	
	public function getUserInfo($userName) {
		global $link;

		$this->authenticate();
		if($this->Authenticated) {				
			$userInfo = false;
			$userName = mysql_escape_string($userName);
			$sql = "select * from ol_users where userName = '$userName' limit 1";					
			$res = mysql_query($sql, $link);
			if(mysql_num_rows($res) != 0) {	
				$userInfo = mysql_fetch_array($res, MYSQL_ASSOC);
				
				if(strpos($userInfo['enterServers'], ':{') != false) {
					$enterServers = unserialize($userInfo['enterServers']);
				} else {
					$enterServers = false;
				}
				
				if($enterServers == false) {
					return 21; // 用户不存在
				} else {
					// 获取服务器列表
					$serverList = array();
					$res = mysql_query('select serverIp, serverName, origin from ol_serverlist', $link);
					while( $row = mysql_fetch_array($res, MYSQL_ASSOC)) {
						$serverList[$row['serverIp'] . '_' . $row['origin']] = $row['serverName'];
					}

					// 构造用户信息				
					$retUserInfo = array();
					$idx = 0;
					foreach ($enterServers as $k=>$v) {
						$retUserInfo[$idx]['ip'] = $k;
						$retUserInfo[$idx]['serverName'] = $serverList[$k  . '_' .  $userInfo['origin']];
						$retUserInfo[$idx]['ucid'] = $userInfo['uid'];
						$idx++;
					}

					// 调用游戏ws获取用户信息
					$user = 'admin';
					$pass = 'admin';
					
					$rsa = new Rsa(KEY_PATH);
					$user = $rsa->pubEncrypt($user);
					$pass = $rsa->pubEncrypt($pass);
					$ip = $rsa->pubEncrypt($_SERVER['SERVER_ADDR']);
						
					$auth = new authentication_header($user, $pass, $ip);
					$authvalues = new SoapVar($auth, SOAP_ENC_OBJECT, 'authenticate');
					
					$wsPath =  'components/hero_tools/';
					$retUserCnt = count($retUserInfo);
					
					for($i = 0; $i < $retUserCnt; $i++) {
						$header = new SoapHeader('http://' . $retUserInfo[$i]['ip'], 'authenticate', $authvalues);
						$client = new SoapClient(null,array('location' =>'http://' . $retUserInfo[$i]['ip'] . '/' . $wsPath . "server.php", 'uri' => "http://127.0.0.1/"));
						$client->__setSoapHeaders(array($header));						
						$playerInfo = json_decode($client->getPlayerInfo(null, null, $retUserInfo[$i]['ucid']), true);
						$retUserInfo[$i]['pid'] = $playerInfo[0]['id'];
						$retUserInfo[$i]['nickname'] = $playerInfo[0]['mc'];
						$retUserInfo[$i]['level'] = $playerInfo[0]['jb'];						
					}
					
					return json_encode($retUserInfo);
				}
				
			} else {
				return 21; // 用户不存在
			}
			
		} else {
			//return "$this->Ip, $this->client";
			return 3;
		}
	}

	
	public function verifyUserInfo($userName, $pwd) {
		global $link;

		$this->authenticate();
		if($this->Authenticated) {				
			$userInfo = false;
			$userName = mysql_escape_string($userName);
			$sql = "select * from ol_users where userName = '$userName' limit 1";					
			$res = mysql_query($sql, $link);
			if(mysql_num_rows($res) != 0) {	
				$userInfo = mysql_fetch_array($res, MYSQL_ASSOC);
				$encyptPwd = md5($pwd);
				if($userInfo['pwd'] == $encyptPwd) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
			
		} else {
			//return "$this->Ip, $this->client";
			return 3;
		}
	}

	public function modifyUserPhone($ucid, $phone){
		global $link;

		$this->authenticate();
		if($this->Authenticated){
			$ucid = mysql_escape_string($ucid);
			$sql = "update ol_users SET email='{$phone}' where uid='{$ucid}'";
			mysql_query($sql, $link);
			if(mysql_affected_rows($link)>0){
				return true;
			}else{
				return false;
			}
		}else{
			return 3;
		}
	}

	public function getUserPhone($ucid){
		global $link;
		$this->authenticate();
		if($this->Authenticated){
			$ucid = mysql_escape_string($ucid);
			$sql = "select * from ol_users where uid = '$ucid' limit 1";

			$res = mysql_query($sql, $link);
			if(mysql_num_rows($res) != 0) {
				$userInfo = mysql_fetch_array($res, MYSQL_ASSOC);
				return $userInfo['email'];
			} else {
				return false;
			}
		}else{
			return 3;
		}
	}
	
   //修改快速登录用户信息
	public function xgksdl($uid,$username,$password,$phone,$ly=''){
		global $link;
		$this->authenticate();
		if($this->Authenticated){
			$sql = "select * from ol_users where uid = '$uid' limit 1";			
			$res = mysql_query($sql, $link);
			$rows = mysql_fetch_array($res);
			if (empty($rows)) {
				return 4;
			} else {
				$id = $rows['id'];
				$userInfo = isDupUser($username);
				if ($userInfo == false) {
					if (empty($password)) {
						$password = time();
					}
					$pwd = md5($password);
					if ($ly == 'sg') {
						mysql_query("update ol_users set userName = '$username',uid = '$username',pwd='$pwd',email='$phone' where id = $id");
					} else {
						mysql_query("update ol_users set userName = '$username',pwd='$pwd',email='$phone' where id = $id");
					}
					return 0;
				} else {
					if ($ly == 'sg') {
						mysql_query("delete from ol_users where uid = '$uid' limit 1");
						return 0;
					}
					return 5;
				}
			}
		} else {
			return 3;
		}
	}

	// 转发邀请码完成消息到指定服务器
	public function sendReqCode($r_code, $message, $request, $genre=20){
		global $link;
		$this->authenticate();
		if($this->Authenticated){
			$serCode = trim(substr($r_code, 0, 3), 'e');
			$pid     = hexdec(substr($r_code, 3));

			$sql = "select * from ol_serverlist where id='{$serCode}'";
			$result = mysql_query($sql, $link);
			$row = mysql_fetch_assoc($result);
			if(empty($row)){
				return 4;
			}
			$url = $row['serverIp']."/components/hero_tools/server.php";
			if(substr($url, 0, 4) != 'http'){
				$url = 'http://'.$url;
			}

			// 向服务器发送消息
			$user = 'admin';
			$pass = 'admin';
			$ip = '127.0.0.1';

			$path   = dirname(__FILE__).DIRECTORY_SEPARATOR.'RSA_KEY'.DIRECTORY_SEPARATOR;
			$rsa    = new Rsa($path);
			$user   = $rsa->pubEncrypt($user);
			$passwd = $rsa->pubEncrypt($pass);
			$ip     = $rsa->pubEncrypt($ip);

			$auth = new authentication_header($user, $passwd, $ip);
			$authvalues = new SoapVar($auth, SOAP_ENC_OBJECT, 'authenticate');
			$header = new SoapHeader(WS_SERVER, 'authenticate', $authvalues);

			$client = new SoapClient(null,array('location' =>$url, 'uri' => "http://127.0.0.1/"));
			$client->__setSoapHeaders(array($header));

			try {
				// 这里参考checkitems.php in admin
				$pidList = array($pid);
				$_message = "测试调用";
				$_message = isset($message)?$message:$_message;
				$_message = str_replace('{server}', $row['serverName'], $_message);

				$sendItems = array('tq'=>0,'yp'=>0,'yb'=>0,'jl'=>0,'items'=>array(array('id'=>'10001', 'num'=>'0', 'mc'=>'金刚石')));
				if(isset($request)){
					$sendItems = $request;
				}else{
					$sendItems = addcslashes(json_encode($sendItems),'\\');
				}
				$result = $client->sendMsg(json_encode($pidList), $_message, $sendItems, $genre);
			} catch(Exception $ex) {
				return 5;
			}

			return 0;
		}else{
			return 3;
		}
	}

	public function sendBindCodeInfo($playersid, $r_code, $message=''){
		global $link;
		$this->authenticate();
		if($this->Authenticated){
			$serCode = trim(substr($r_code, 0, 3), 'e');
			$pid     = hexdec(substr($r_code, 3));
			
			$sql = "select * from ol_serverlist where id='{$serCode}'";
			$result = mysql_query($sql, $link);
			$row = mysql_fetch_assoc($result);
			if(empty($row)){
				return 4;
			}

			
			$url = $row['serverIp']."/components/hero_tools/server.php";
			if(substr($url, 0, 4) != 'http'){
				$url = 'http://'.$url;
			}

			// 构建soap
			$user = 'admin';
			$pass = 'admin';
			$ip = '127.0.0.1';

			$path   = dirname(__FILE__).DIRECTORY_SEPARATOR.'RSA_KEY'.DIRECTORY_SEPARATOR;
			$rsa    = new Rsa($path);
			$user   = $rsa->pubEncrypt($user);
			$passwd = $rsa->pubEncrypt($pass);
			$ip     = $rsa->pubEncrypt($ip);

			$auth = new authentication_header($user, $passwd, $ip);
			$authvalues = new SoapVar($auth, SOAP_ENC_OBJECT, 'authenticate');
			$header = new SoapHeader(WS_SERVER, 'authenticate', $authvalues);

			$client = new SoapClient(null,array('location' =>$url, 'uri' => "http://127.0.0.1/"));
			$client->__setSoapHeaders(array($header));

			try{
				$pInfo = $client->getPlayerInfo($pid);
				$pInfo = json_decode($pInfo, true);
				if(!is_array($pInfo)){
					return 5;
				}

				$_message = str_replace('{server}', $row['serverName'], $message);
				$result = $client->sendMsg(json_encode(array($pid)), $_message, '', 70);
			}catch(Exception $ex){
				return 5;
			}

			return $row['serverName'];
		}else{
			return 3;
		}
	}
}

$classExample = array();
$server = new SoapServer(null, array('uri'=>WS_SERVER, 'classExample'=>$classExample));
$server->setClass('mysoapclass');
$server->handle();