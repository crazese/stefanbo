<?php
$key = "52de80b8baa2e19867d4"; // ConsumerKey
$secret = "99d8fdefbff3e6ba139fa3499e2f7c5fc5f4617d"; // ConsumerSecret
$task = _get('task');
$option = _get('option');
$rdata = urldecode(_get('data'));
$mode = _get('mode');
include 'includes/dena.php';
include(dirname(__FILE__) .DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'class_memcacheAdapter.php');
//String info = "<root><jcid>19</jcid>" +"<code>0</code><uid>"+currentId+"</uid><verifier>"+verifier+"</verifier></root>"
if (!empty($rdata)) {
	$xml = simplexml_load_string($rdata);
	$xml = json_encode($xml);
	$xml = json_decode($xml);	
	$uid = $xml->uid;
	$verifier = $xml->verifier;
	$serversid = $xml->serversid;
	$mode = $xml->mode;
	session_id($serversid);
	session_start();
	if (!empty($_SESSION['tmp_pass'])) {
		$dataInfo = json_decode($_SESSION['tmp_pass'],true);
		$oauth_token = $dataInfo['oauth_token'];
		$oauth_token_secret = $dataInfo['oauth_token_secret'];		
	} else {
		$returnValue['status'] = 1030;
		$returnValue['message'] = '临时key数据无效，请重新登录';
		$returnValue['rsn'] = intval(_get('ssn'));	
		echo json_encode($returnValue);
		exit;		
	}	
} else {
	$tag = uniqid(mt_rand());
	session_id($tag);
	session_start();	
	$res = OAuthUtil::request_temporary_credential();
	$res['serversid'] = $tag;
	echo json_encode($res);	
	exit;
}

if( $task == null || $task != 'sdkLogin' ) {
	$returnValue['status'] = 1030;
	$returnValue['message'] = '非法请求';
	$returnValue['rsn'] = intval(_get('ssn'));	
	echo json_encode($returnValue);
	exit;
} else {
	//http://sp.sb.mobage-platform.cn/social/api/oauth/v2.01
	if ($mode == 1) {
		$endpoint = "http://sp.mobage-platform.cn/social/api/oauth/v2.01/request_token"; // Endpoint of token credential
	} else {
		$endpoint = "http://sp.sb.mobage-platform.cn/social/api/oauth/v2.01/request_token";
	}
	$sig_method = new OAuthSignatureMethod_HMAC_SHA1(); // signature method
	$consumer = new OAuthConsumer($key, $secret, NULL); // Consumer
	$token = new OAuthToken($oauth_token, $oauth_token_secret); // Token	
	// generate Authentication Header
	$params = array(
		"oauth_verifier" => $verifier,
	);
	$request = OAuthRequest::from_consumer_and_token($consumer, $token, "POST", $endpoint, $params);
	$request->sign_request($sig_method, $consumer, $token);
	$auth_header =  $request->to_header("");
	$body = ' ';
	//请求正式令牌
	$curl = curl_init($endpoint);	
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_FAILONERROR, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_ENCODING , "gzip");
	curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));
	curl_setopt($curl, CURLOPT_VERBOSE, true);	
	$response = curl_exec($curl);
	curl_close($curl);	
	// parse response
	$decoded_response = OAuthUtil::urldecode_rfc3986($response);
	$parsed_parameters = OAuthUtil::parse_parameters($decoded_response);
	//查询用户个人信息
	//$endpoint = "http://sp.mobage-platform.cn/social/api/restful/v2/people/@me/@self?format=json"; // Endpoint of token credential
	//$sig_method = new OAuthSignatureMethod_HMAC_SHA1(); // signature method
	//$consumer = new OAuthConsumer($key, $secret, NULL); // Consumer
	if ($mode == 1) {
		$endpoint = "http://sp.mobage-platform.cn/social/api/restful/v2/people/@me/@self?format=json";
	} else {
		$endpoint = "http://sp.sb.mobage-platform.cn/social/api/restful/v2/people/@me/@self?format=json";
	}
	$token = new OAuthToken($parsed_parameters['oauth_token'], $parsed_parameters['oauth_token_secret']); // Token
	//echo $dataInfo['oauth_token'].'++++'.$dataInfo['oauth_token_secret'].'<br>';
	// generate Authentication Header
	$params = array();
	$request = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $endpoint, $params);
	$request->sign_request($sig_method, $consumer, $token);
	$auth_header =  $request->to_header("");
	//print_r($auth_header);
	$body = ' ';
	$curl = '';
	$response = '';
	// access to platform server
	$curl = curl_init($endpoint);	
	curl_setopt($curl, CURLOPT_POSTFIELDS, '');
	curl_setopt($curl, CURLOPT_POST, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_FAILONERROR, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_ENCODING , "gzip");
	curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));
	curl_setopt($curl, CURLOPT_VERBOSE, true);	
	$response = curl_exec($curl);
	curl_close($curl);
	//file_put_contents('/var/www/gzp4.txt',$response);	
	if (!empty($response)) {
		$dataInfo = json_decode($response,true);
		if (isset($dataInfo['id'])) {
			$mbgUseridInfo = explode(':',$dataInfo['id']);
			$mbgUserid = $mbgUseridInfo[1];
			if ($mbgUserid == $uid) {
				$userName = $dataInfo['id'];
				$nickName = '';
				$returnValue = sdKlogin::regist($userName,$nickName,$link);
				echo json_encode($returnValue);			
				exit;					
			} else {
				$returnValue['status'] = 1030;
				$returnValue['message'] = '身份信息不匹配！';
				echo json_encode($returnValue);
				exit;				
			}
		}
	} else {
		$returnValue['status'] = 1030;
		$returnValue['message'] = '身份验证失败！';
		echo json_encode($returnValue);
		exit;			
	}
}	
		
	