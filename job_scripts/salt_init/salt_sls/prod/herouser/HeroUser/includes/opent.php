<?php
class OauthKey {
	public $customKey;		//应用的key
	public $customSecrect;	//应用的密钥
	public $tokenKey;		//用户的oauth token
	public $tokenSecrect;	//用户的oauth 密钥

    function __construct() { 
        $this->customKey 		= null;
        $this->customSecrect 	= null;
        $this->tokenKey 		= null;
        $this->tokenSecrect	= null;
        $this->verify		= null;                                
        $this->callbackUrl 	= null;     
        $this->oidString 	= null;                            
        $this->terminalType = null;
        $this->appId 		= null;
        $this->platformId 	= null;
		
		$this->openid 	    = null;
		$this->openkey   	= null;
		$this->appkey 	    = null;
		$this->serviceName 	= null;
    }
}
/**
 * REST接口包装类，包括了所有REST的调用，注意所有的输入参数都无需编码，SDK内部会编码
 * @author teajtang
 * 
 */
class OpenApiSdk 
{
	const SC_MOVED_PERMANENTLY    = 301;
	const SC_MOVED_TEMPORARILY    = 302;
	const SC_OK       = 200;
	const SC_CREATED  = 201;
	const SC_ACCEPTED = 202;
	const STR_ENTRY = "entry";
	const STR_TERMINAL_TYPE = "tt";//终端类型
	const WAP_TYPE			= "2"; //wap终端
	
	const JSON_FORMAT				   = "json";
	const SIMPLEJSON_FORMAT			   = "sjson";	
	
	const VERSION					   = "2.0";
	
	/**
	 * 获取个人资料，url格式：HTTP://open.3g.qq.com/people/{guid}/@self，如果查看自己guid是@me，其他是 对于的用户id，只能查看自己的好友资料
	 * 
	 * @param OauthParam        OAuth认证相关参数
	 * @param url			           服务地址
	 * @param fields            需要的个人资料字段，多个用","分割，使用默认就填null
	 * @param format	 	           返回数据的格式，目前只支持json，，使用默认就填null
	 * @return 个人资料json，如果失败抛异常。
	 */
	public function getSelf(OauthKey $OauthParam, $url, $fields,$format)
	{	
		if ($OauthParam == NULL || !$this->checkString($url))
			throw new SdkCallException(-1 , SdkCallException::INPUT_DATA_ERR,SdkCallException::ERR_MSG_PARAMATER);
			
		$_cc    = new MBOpenTOAuth( $OauthParam->customKey , $OauthParam->customSecrect ,$OauthParam->tokenKey , $OauthParam->tokenSecrect );
		$params = array();
		if ($fields != NULL)
			$params['fields'] = $fields;			
			
		if ($format != NULL)
			$params['format'] = $format;		

		if (isset($OauthParam->appId))
			$params['appId'] = $OauthParam->appId;	
			
		$_r       = $_cc->get($url,$params);
		$httpCode = $_r[MBOpenTOAuth::httpCodeStr];
		$httpRsp  = $_r[MBOpenTOAuth::httpContentStr];
		
		if ($httpCode != OpenApiSdk::SC_OK)
		{
			//解析内部错误码和原因
			throw $this->parseErrRsp($httpCode , $httpRsp);			
		}
		
		return $httpRsp;		
	}

	function checkString($str)
	{
		if ($str == NULL || strlen($str) == 0)
			return false;
		else
			return true;	
					
	}
	
	function appendUrlParam($url, $urlParam , $urlParamName) 
	{
		if (!$this->checkString($url))
			return "";
			
		if (!$this->checkString($urlParam))			
			return $url;
			
		$check = strpos($url, '?'); 
		if ($check !== false)			
		{
			if(substr($url, $check+1) == '') 
			{ 
				return $url.$urlParamName.'='.$urlParam;
			}
			else
			{
				return $url.'&'.$urlParamName.'='.$urlParam;
			}
		}
		else
		{
			return $url.'?'.$urlParamName.'='.$urlParam;
		}
	}

	function parseToken($response, OauthKey &$key, $authrition) 
	{
		if ($response == null || empty($response)) {
			return false;
		}

		$tokenArray = explode("&" , $response);		
		if (count($tokenArray) < 2) {
			return false;
		}

		$strTokenKey     = $tokenArray[0];
		$strTokenSecrect = $tokenArray[1];

		$token1 = explode("=" , $strTokenKey);
		if (count($token1) < 2) {
			return false;
		}

		$key->tokenKey = $token1[1];

		$token2 = explode("=" , $strTokenSecrect);
		if (count($token2) < 2) {
			return false;
		}

		if (!$authrition)
			$key->tokenSecrect = $token2[1];
		else
			$key->verify = $token2[1];
			
				
		if (count($tokenArray)> 2) {
			$token3 = explode("=" , $tokenArray[2]);
			
			if (count($token3) > 1)
			$key->oidString = $token3[1];
		}
		return true;
	}
}

class OAuthConsumer { 
    public $key; 
    public $secret; 

    function __construct($key, $secret) { 
        $this->key = $key; 
        $this->secret = $secret; 
    } 

    function __toString() { 
        return "OAuthConsumer[key=$this->key,secret=$this->secret]"; 
    } 
} 

class OAuthToken { 
    public $key; 
    public $secret; 

    function __construct($key, $secret) { 
        $this->key = $key; 
        $this->secret = $secret; 
    } 

    /** 
	 * 
	 * generates the basic string serialization of a token that a server 
     * would respond to request_token and access_token calls with 
     */ 
    function to_string() { 
        return "oauth_token=" . 
            OAuthUtil::urlencode_rfc3986($this->key) . 
            "&oauth_token_secret=" . 
            OAuthUtil::urlencode_rfc3986($this->secret); 
    } 

    function __toString() { 
        return $this->to_string(); 
    } 
}

//oauth签名方法
class OAuthSignatureMethod { 
    public function check_signature(&$request, $consumer, $token, $signature) { 
        $built = $this->build_signature($request, $consumer, $token); 
        return $built == $signature; 
    } 
}
class OAuthSignatureMethod_HMAC_SHA1 extends OAuthSignatureMethod { 
    function get_name() { 
        return "HMAC-SHA1"; 
    } 

    public function build_signature($request, $consumer, $token) { 
		$base_string = $request->get_signature_base_string(); 
        $request->base_string = $base_string; 
        $key_parts = array( 
            $consumer->secret, 
            ($token) ? $token->secret : "" 
        ); 
		
		$key_parts = OAuthUtil::urlencode_rfc3986($key_parts); 

		$key = implode('&', $key_parts); 
		//error_log('basestring:'.$base_string.' key:'.$key);
        return base64_encode(hash_hmac('sha1', $base_string, $key, true)); 
    } 
} 

/** 
 * @ignore 
 */ 
class OAuthRequest { 
    public $parameters; 
    private $http_method; 
    private $http_url; 
    // for debug purposes 
    public $base_string; 
    public static $version = '1.0'; 
    public static $POST_INPUT = 'php://input'; 

    function __construct($http_method, $http_url, $parameters=NULL) { 
        @$parameters or $parameters = array(); 
        $this->parameters = $parameters; 
        $this->http_method = $http_method; 
        $this->http_url = $http_url; 
    } 

    /** 
     * pretty much a helper function to set up the request 
     */ 
    public static function from_consumer_and_token($consumer, $token, $http_method, $http_url, $parameters=NULL) { 
		@$parameters or $parameters = array();
        $defaults = array("oauth_version" => OAuthRequest::$version, 
            "oauth_nonce" => OAuthRequest::generate_nonce(), 
            "oauth_timestamp" => OAuthRequest::generate_timestamp(), 
            "oauth_consumer_key" => $consumer->key); 
        if ($token) 
            $defaults['oauth_token'] = $token->key; 		
        $parameters = array_merge($defaults, $parameters); 
		//unset($parameters['pic']);
        return new OAuthRequest($http_method, $http_url, $parameters); 
    }      

    public function set_parameter($name, $value, $allow_duplicates = true) { 
        if ($allow_duplicates && isset($this->parameters[$name])) { 
            // We have already added parameter(s) with this name, so add to the list 
            if (is_scalar($this->parameters[$name])) { 
                // This is the first duplicate, so transform scalar (string) 
                // into an array so we can add the duplicates 
                $this->parameters[$name] = array($this->parameters[$name]); 
            } 

            $this->parameters[$name][] = $value; 
        } else { 
            $this->parameters[$name] = $value; 
        } 
    } 

	public function get_signature_base_string() { 
        $parts = array( 
            $this->get_normalized_http_method(), 
            $this->get_normalized_http_url(), 
            $this->get_signable_parameters() 
        ); 
        
        $parts = OAuthUtil::urlencode_rfc3986($parts); 
        return implode('&', $parts); 
    } 

	public function get_normalized_http_method() { 
        return strtoupper($this->http_method); 
    } 
    
	public function get_normalized_http_url() { 
        $parts = parse_url($this->http_url); 

        $port = @$parts['port']; 
        $scheme = $parts['scheme']; 
        $host = $parts['host']; 
        $path = @$parts['path']; 

        $port or $port = ($scheme == 'https') ? '443' : '80'; 

        if (($scheme == 'https' && $port != '443') 
            || ($scheme == 'http' && $port != '80')) { 
                $host = "$host:$port"; 
            } 
        return "$scheme://$host$path"; 
    } 

    public function to_header() { 
        $out ='Authorization: OAuth realm=""'; 
        $total = array(); 
        foreach ($this->parameters as $k => $v) { 
            if (substr($k, 0, 5) != "oauth") continue; 
            if (is_array($v)) { 
                throw new MBOAuthExcep('Arrays not supported in headers'); 
            } 
            $out .= ',' . 
                OAuthUtil::urlencode_rfc3986($k) . 
                '="' . 
                OAuthUtil::urlencode_rfc3986($v) . 
                '"'; 
        } 
        return $out; 
    } 

    public function get_signable_parameters() { 
        // Grab all parameters 
        $params = $this->parameters; 
        
        // remove pic 
        if (isset($params['pic'])) { 
            unset($params['pic']); 
        }
        
          if (isset($params['image'])) 
         { 
            unset($params['image']); 
        }

        // Remove oauth_signature if present 
        // Ref: Spec: 9.1.1 ("The oauth_signature parameter MUST be excluded.") 
        if (isset($params['oauth_signature'])) { 
            unset($params['oauth_signature']); 
        } 

        return OAuthUtil::build_http_query($params); 
    } 

    public function get_url_with_query_paramter()
    {
    	return $http_url;
    }

	public function sign_request($signature_method, $consumer, $token) { 
        $this->set_parameter( 
            "oauth_signature_method", 
            $signature_method->get_name(), 
            false 
		);
		$signature = $this->build_signature($signature_method, $consumer, $token); 
		$this->set_parameter("oauth_signature", $signature, false); 
    } 

    public function build_signature($signature_method, $consumer, $token) { 
        $signature = $signature_method->build_signature($this, $consumer, $token); 
        return $signature; 
    } 

    /** 
     * util function: current timestamp 
     */ 
    private static function generate_timestamp() { 
		return time(); 
    } 

    /** 
     * util function: current nonce 
     */ 
    private static function generate_nonce() { 
		$mt = microtime(); 
        $rand = mt_rand(); 

        return md5($mt . $rand); // md5s look nicer than numbers 
    } 
} 

/** 
 * @ignore 
 */ 
class OAuthUtil { 

	public static $boundary = '';

	public static function urlencode_rfc3986($input) { 
        if (is_array($input)) { 
            return array_map(array('OAuthUtil', 'urlencode_rfc3986'), $input); 
        } else if (is_scalar($input)) { 
            return str_replace( 
                '+', 
                ' ', 
                str_replace('%7E', '~', rawurlencode($input)) 
            ); 
        } else { 
            return ''; 
        } 
    } 

    public static function build_http_query($params) { 
        if (!$params) return ''; 

        // Urlencode both keys and values 
        $keys = OAuthUtil::urlencode_rfc3986(array_keys($params)); 
        $values = OAuthUtil::urlencode_rfc3986(array_values($params)); 
        $params = array_combine($keys, $values); 

        // Parameters are sorted by name, using lexicographical byte value ordering. 
        // Ref: Spec: 9.1.1 (1) 
        uksort($params, 'strcmp'); 

        $pairs = array(); 
        foreach ($params as $parameter => $value) { 
            if (is_array($value)) { 
                // If two or more parameters share the same name, they are sorted by their value 
                // Ref: Spec: 9.1.1 (1) 
                natsort($value); 
                foreach ($value as $duplicate_value) { 
                    $pairs[] = $parameter . '=' . $duplicate_value; 
                } 
            } else { 
                $pairs[] = $parameter . '=' . $value; 
            } 
        } 
        // For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61) 
        // Each name-value pair is separated by an '&' character (ASCII code 38) 
        return implode('&', $pairs); 
    } 
} 

class MBOpenTOAuth {
	public $host = 'http://openapi.3g.qq.com/';

	const httpCodeStr    = 'httpcode';
	const httpContentStr = 'content';	

	const tjProxyIp      = '10.172.48.92';
	const tjProxyPort	 = '3300';

	function lastStatusCode() { return $this->http_status; } 

    function __construct($consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL) { 
        $this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1(); 
        $this->consumer = new OAuthConsumer($consumer_key, $consumer_secret); 
        if (!empty($oauth_token) && !empty($oauth_token_secret)) { 
            $this->token = new OAuthConsumer($oauth_token, $oauth_token_secret); 
        } else { 
            $this->token = NULL; 
        }		
	}
	
    /** 
     * 重新封装的get请求. 
     * @return mixed 
     */ 
    function get($url, $parameters,$bUrlWithParam = true) { 
		$response = $this->oAuthRequest($url, 'GET', $parameters,null,$bUrlWithParam); 
        return $response; 
	}

    /**
     * 发送请求的具体类
     * @return string
     */
    function oAuthRequest($url, $method, $parameters ,$postParameters = null, $bNeedUrlWithParam = TRUE , $multi = false) {
    	$http_url = $url;
    	if ($bNeedUrlWithParam)
	    	$http_url .= '?'.OAuthUtil::build_http_query($parameters);
	    	
    	//error_log('b:'.$bNeedUrlWithParam.' url:'.$url.' hurl:'.$http_url);	    	
	    $request = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method == 'POST_JSON' ? 'POST' : $method , $url, $parameters);	
    	//$request = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $parameters);
    	$request->sign_request($this->sha1_method, $this->consumer, $this->token);
    	switch ($method) {
    		case 'GET':
    			return $this->http($http_url, $method,$request->to_header() ,'GET');
			case 'POST_JSON':
    			$postContent = json_encode($postParameters);
    			return $this->http($http_url, $method, $request->to_header(),$postContent , $multi );
    		default:
    			{
    				$postContent = OAuthUtil::build_http_query($postParameters);
    				return $this->http($http_url, $method, $request->to_header(),$postContent , $multi );
       		}
        } 
	}

	function http($url, $method, $headerV = NULL, $postfields = NULL , $multi = false){
		$org_method = $method;
		$method = ($org_method == 'POST_JSON') ? 'POST' : $org_method;
		//判断是否是https请求
		if(strrpos($url, 'https://')===0){
			$port = 443;
			$version = '1.1';
			$host = 'ssl://'.MB_API_HOST;	
			
		}else{
			$port = 80;	
			$version = '1.0';
			$host = MB_API_HOST;
		}
		
		$hostv = explode('/',$host);
		if (count($hostv) == 2)
			$host = $hostv[0];

		$header = "$method $url HTTP/$version\r\n";	
		$header .= "Host: ".$host."\r\n";
		if($multi){
			$header .= "Content-Type: multipart/form-data; boundary=" . OAuthUtil::$boundary . "\r\n";	
		}else{	
			if($org_method != 'POST_JSON') {
				$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
			}
			if ($headerV)
				$header .= $headerV."\r\n";  
		}
		if(strtolower($method) == 'post' ){
			$header .= "Content-Length: ".strlen($postfields)."\r\n";
			$header .= "Connection: Close\r\n\r\n";  
			$header .= $postfields;
		}else{
			$header .= "Connection: Close\r\n\r\n";  
		}
		$ret = '';
		
		$useProxy = 1;
		if ($useProxy == 1)
			$fp = fsockopen(MBOpenTOAuth::tjProxyIp,MBOpenTOAuth::tjProxyPort,$errno,$errstr,30);
		else
			$fp = fsockopen($host,$port,$errno,$errstr,30);

		if(!$fp){
			$error = "建立sock连接失败,host:$host,port:$port";
			throw new Exception($error);
		}else{
			fwrite ($fp, $header);  
			while (!feof($fp)) {
				$ret .= fgets($fp, 4096);
			}
			fclose($fp);
			$httpCode = 400;
			$content  = '';
			if(strrpos($ret,'Transfer-Encoding: chunked')){
				$info = explode("\r\n\r\n",$ret);
				$response = explode("\r\n",$info[1]);
				$t = array_slice($response,1,-1);

				$content = implode('',$t);				
			}else{
				$response = explode("\r\n\r\n",$ret);
				$content  = $response[1];

				$responseCodeStr = explode("\r\n",$response[0]);
				$responseCode    = explode(" ",$responseCodeStr[0]);				
				$httpCode		 = $responseCode[1];
			}
			//转成utf-8编码
			iconv("utf-8","utf-8//ignore",$content);
			$httpRs = array();
			$httpRs[MBOpenTOAuth::httpCodeStr] = $httpCode;
			$httpRs[MBOpenTOAuth::httpContentStr]   = $content;

			return $httpRs;
		}		
	}
 
}

?>
