<?php
$_SC = array();
$_SC['dbhost']  		= '192.168.1.10'; //数据库服务器地址
$_SC['dbuser']  		= 'sothink';   //数据库用户名
$_SC['dbpw'] 	 		= 'src8351';   //数据库密码
$_SC['dbname']				= 'authserver';

$MemcacheList = array('127.0.0.1'); //Memcache服务器列表
$Memport = 11211;

define('WS_SERVER', 'http://117.135.138.38/');
define('KEY_PATH', '/var/key');
define('WS_CLIENT', "192.168.1.16"); // 后台管理服务器的地址
//define("SMS_ADDR", 'http://192.168.1.9:8090/sms.asp');
define("SMS_ADDR", 'http://119.97.226.138:5218/sms.asp');
define('SMS_KEY', '123456'); // 短信网关加密key
define('DEBUG', 0); // 正式环境设为0
/*UC游戏接口相关信息*/
$_SC['cpId'] = 690;
$_SC['gameId'] = 64431;
$_SC['channelId'] = 2;
$_SC['serverId'] = 949;
$_SC['apiKey'] = 'f23e3bdea56048a71c5100ee824ca699';
$_SC['apiUrl'] = 'sdk.g.uc.cn';  //sdk.g.uc.cn   sdk.test2.g.uc.cn
$_SC['ucsynUrl'] = '117.135.139.237';  //uc官方账号登陆同步地址
$_SC['port'] = 80;                   //uc官方账号登陆同步端口

/*UC登录同步游戏接口相关信息*/
$_SC['cpId_syn'] = 690;
$_SC['gameId_syn'] = 105057;
$_SC['channelId_syn'] = 2;
$_SC['serverId_syn'] = 949;
$_SC['apiKey_syn'] = 'f23e3bdea56048a71c5100ee824ca699';
$_SC['apiUrl_syn'] = 'sdk.g.uc.cn';  //sdk.g.uc.cn   sdk.test2.g.uc.cn

/*9游页游测试环境发布时须修改为正式环境*/
$_SC['9ycpId'] = 690;
$_SC['9ygameId'] = 75548;
$_SC['9ychannelId'] = 2;
$_SC['9yserverId'] = 1058;
$_SC['9yapiKey'] = 'f23e3bdea56048a71c5100ee824ca699';
$_SC['9yapiUrl'] = 'api.g.uc.cn';
$_SC['redirectUrl'] = 'http://117.135.138.196/index.php';
$_SC['callbackUrl'] = 'http://117.135.138.196/ucpay/9yPagepay.php';
/*UC游戏接口相关信息结束*/

/*91游戏接口相关信息*/
$_SC['AppId'] = 108003;
$_SC['appKey'] = '87c00de7499f766bc71bc2aba90194320f0853818fbe0f8b';
$_SC['91apiUrl'] = 'service.sj.91.com';  //service.sj.91.com:8080/usercenter/AP.aspx   service.sj.91.com/usercenter/AP.aspx
/*91游戏接口相关信息结束*/

/*乐逗相关配置*/
$_SC['secret'] = '860bfcf35e71cefa6a95';
$_SC['ldapiUrl'] = 'http://ly.feed.uu.cc/account/verify_osid';
/*乐逗相关配置信息结束*/

/*QQ游戏大厅配置*/
define("MB_AKEY","O1hX2A586A0F348Mo5LI");
define("MB_SKEY","6RbVJipgiGtZ1Qn0");
define( "APP_ID" , '197' ); 
define( "MB_API_HOST" , 'openapi.3g.qq.com' );
define("useProxy",0);
/*define("MB_AKEY","GDdmIQH6jhtmLUypg82g");
define("MB_SKEY","MCD8BKwGdgPHvAuvgvz4EQpqDAtx89grbuNMRd7Eh98");
define( "APP_ID" , '1' ); 
define( "MB_API_HOST" , 'openapi.3g.qq.com' );
define("useProxy",0);*/
/*QQ游戏大厅配置结束*/

date_default_timezone_set('PRC');

// 语言包配置可以设置cn,en,kr
$_SC['lang'] = 'cn';

// 邮件配置
$_SC['email']['smtp'] = "smtp.gmail.com";
$_SC['email']['port'] = 465;
$_SC['email']['user'] = '';
$_SC['email']['passwd'] = '';
$_SC['email']['callbck'] = "http://localhost/authserver";


class Rsa
{
        private $_privKey;
        
        private $_pubKey; 

        private $_keyPath;

        public function __construct($path)
        {
                if(empty($path) || !is_dir($path)){
                        throw new Exception('Must set the keys save path');
                }
               
                $this->_keyPath = $path;
        }

        public function setupPrivKey()
        {
                if(is_resource($this->_privKey)){
                        return true;
                }
                $file = $this->_keyPath . DIRECTORY_SEPARATOR . 'priv.key';
                $prk = file_get_contents($file);
                $this->_privKey = openssl_pkey_get_private($prk);
                return true;
        }

        public function privEncrypt($data)
        {
                if(!is_string($data)){
                        return null;
                }
               
                $this->setupPrivKey();
               
                $r = openssl_private_encrypt($data, $encrypted, $this->_privKey);
                if($r){
                        return base64_encode($encrypted);
                }
                return null;
        }
 
        public function privDecrypt($encrypted)
        {
                if(!is_string($encrypted)){
                        return null;
                }
               
                $this->setupPrivKey();
               
                $encrypted = base64_decode($encrypted);

                $r = openssl_private_decrypt($encrypted, $decrypted, $this->_privKey);
                if($r){
                        return $decrypted;
                }
                return null;
        }
 
        public function setupPubKey()
        {
                if(is_resource($this->_pubKey)){
                	return true;
                }
                $file = $this->_keyPath . DIRECTORY_SEPARATOR .  'pub.key';
                $puk = file_get_contents($file);
                $this->_pubKey = openssl_pkey_get_public($puk);
                return true;
        }
             
        public function pubEncrypt($data)
        {
                if(!is_string($data)){
                	return null;
                }
               
                $this->setupPubKey();
               
                $r = openssl_public_encrypt($data, $encrypted, $this->_pubKey);
                if($r){
                	return base64_encode($encrypted);
                }
                return null;
        }

        public function pubDecrypt($crypted)
        {
                if(!is_string($crypted)){
                        return null;
                }
               
                $this->setupPubKey();
               
                $crypted = base64_decode($crypted);

                $r = openssl_public_decrypt($crypted, $decrypted, $this->_pubKey);
                if($r){
                        return $decrypted;
                }
                return null;
        }
}

function isDupUser($userName) {
	global $link;//, $mc;
	
	/*if (!($userInfo = $mc->get('AUTH_userInfo_'.$userName))) {
		$res = mysql_query("select * from ol_users where userName = '$userName' limit 1", $link);
		if(mysql_num_rows($res) != 0) {	
			$userInfo = mysql_fetch_array($res, MYSQL_ASSOC);
			$mc->set('AUTH_userInfo_'.$userName, $userInfo, 0, 3600);
		} else {
			$userInfo = false;
		}
	}*/
	$userInfo = false;
	$res = mysql_query("select * from ol_users where userName = '$userName' limit 1", $link);
	if(mysql_num_rows($res) != 0) {	
		$userInfo = mysql_fetch_array($res, MYSQL_ASSOC);
		//$mc->set('AUTH_userInfo_'.$userName, $userInfo, 0, 3600);
	}
	
	return $userInfo;	 
}

function isDupUser9Y($userName) {
	global $link, $mc;
	
	//if (!($userInfo = $mc->get('AUTH_userInfo_'.$userName))) {
		$res = mysql_query("select * from ol_users where userName2 = '$userName' limit 1", $link);
		if(mysql_num_rows($res) != 0) {	
			$userInfo = mysql_fetch_array($res, MYSQL_ASSOC);
			$mc->set('AUTH_userInfo_'.$userName, $userInfo, 0, 3600);
		} else {
			$userInfo = false;
		}
	//}
	
	return $userInfo;	 
}

//$_GET处理函数
function _get($str){
	//$magic_quote = get_magic_quotes_gpc();
	if (isset($_REQUEST[$str]))	{		
		$val = $_REQUEST[$str];			
		if (!is_numeric($val)) {
			$val = mysql_real_escape_string($val);
		}
	} else {
		$val = null;
	}
    return $val;
}

function vget($url){
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
	    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; GTB6.5; .NET CLR 2.0.50727; .NET CLR 3.0.04506.648; .NET CLR 3.5.21022)'); // 模拟用户使用的浏览器    
	    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); 
	    curl_setopt($curl, CURLOPT_HTTPGET, 1);
	    //curl_setopt($curl, CURLOPT_COOKIEFILE, COOKIE_FILE);
	    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
	    curl_setopt($curl, CURLOPT_HEADER, 0);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    $tmpInfo = curl_exec($curl); // 执行操作    
	    if (curl_errno($curl)) {    
	      // echo 'Errno'.curl_error($curl);    
	    }    
	    curl_close($curl); // 关闭CURL会话    
	    return $tmpInfo; // 返回数据    
}  
	
class authentication_header {
	private $username;
	private $password;
	private $ip;
	
	public function __construct($username, $password, $ip) {
		$this->username = $username;
		$this->password = $password;
		$this->ip = $ip;
	}
}
