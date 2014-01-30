<?php
$task = _get('task');
$rdata = urldecode(_get('data'));
$xml = simplexml_load_string($rdata);
$xml = json_encode($xml);
$xml = json_decode($xml);
$uid = $xml->uid;
$username = $xml->username;
$time = $xml->time;
$token = $xml->token;
$url = 'http://api.gc.51.com/2/auth/server';
if( $task == null || $task != 'sdkLogin' ) {
	$returnValue['status'] = 3;
	$returnValue['message'] = '非法请求';
	echo json_encode($returnValue);
} else {
	$result = sdKlogin::callUrl($url,"uid=$uid&username=$username&time=$time&token=$token"); 
	$resultInfo = json_decode($result,true);
	if ($resultInfo['errno'] == 0) {
		$userName = '51yx_'.$username;
		$nickName = '';
		$returnValue = sdKlogin::regist($userName,$nickName,$link);
		echo json_encode($returnValue);			
	} else {
		$returnValue['status'] = 1030;
		$returnValue['message'] = '用户验证失败';
		echo json_encode($returnValue);
	}
}