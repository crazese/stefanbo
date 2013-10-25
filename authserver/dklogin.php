<?php
$task = _get('task');
$rdata = urldecode(_get('data'));
$xml = simplexml_load_string($rdata);
$xml = json_encode($xml);
$xml = json_decode($xml);
$uid = $xml->uid;
$sessionid = urlencode($xml->sessionid);
$appid = 341;
$appkey = '5fdc61c00f684000afbe9ad7222e56d0';
$app_secret = 'cbacdac1548aad45a0b305b7a5c9894b';
$url = 'http://sdk.m.duoku.com/openapi/sdk/checksession';
if( $task == null || $task != 'sdkLogin' ) {
	$returnValue['status'] = 3;
	$returnValue['message'] = '非法请求';
	echo json_encode($returnValue);
} else {
	$clientsecret = md5($appid.$appkey.$uid.$sessionid.$app_secret);
	$result = sdKlogin::callUrl($url,"uid=$uid&appid=$appid&appkey=$appkey&sessionid=$sessionid&clientsecret=$clientsecret");
	$resultInfo = json_decode($result,true);
	if ($resultInfo['error_code'] == 0) {
		$userName = 'dk_'.$uid;
		$nickName = '';
		$returnValue = sdKlogin::regist($userName,$nickName,$link);
		echo json_encode($returnValue);			
	} else {
		$returnValue['status'] = 1030;
		$returnValue['message'] = $resultInfo['error_msg'];
		echo json_encode($returnValue);
	}
}