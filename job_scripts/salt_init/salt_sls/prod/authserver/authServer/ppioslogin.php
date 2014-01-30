<?php
$task = _get('task');
$rdata = urldecode(_get('data'));
$xml = simplexml_load_string($rdata);
$xml = json_encode($xml);
$xml = json_decode($xml);
$token = $xml->token;
$username = $xml->username;
$uid = $xml->uid;
$url = 'http://passport_i.25pp.com:8080/index?tunnel-command=2852126756';
if( $task == null || $task != 'sdkLogin' ) {
	$returnValue['status'] = 30;
	$returnValue['message'] = '非法请求';
	echo json_encode($returnValue);
} else {
	include 'ppios_function.php';
	$rs = MySocket::instance()->postCurl($token);
	$userInfo = json_decode($rs,true);
	if (isset($userInfo['username'])) {
		if ($username == $userInfo['username']) {
			$userName = 'ppios_'.$username;
			$nickName = '';
			$returnValue = sdKlogin::regist($userName,$nickName,$link);
			echo json_encode($returnValue);				
		} else {
			$returnValue['status'] = 30;
			$returnValue['message'] = '身份验证失败';
			echo json_encode($returnValue);			
		}
	} else {
		$returnValue['status'] = 30;
		$returnValue['message'] = '身份验证失败';
		echo json_encode($returnValue);					
	}
}