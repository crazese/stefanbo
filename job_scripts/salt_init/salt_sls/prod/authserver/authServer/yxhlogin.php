<?php
$task = _get('task');
$rdata = urldecode(_get('data'));
$xml = simplexml_load_string($rdata);
$xml = json_encode($xml);
$xml = json_decode($xml);
$sign = $xml->sign;
$token = $xml->token;
$url = 'http://app.5gwan.com:9000/user/info.php';
$app_id = 'A024';
$app_key = 'bf07a6d949b5f841fbd1a1814ecda208';
if( $task == null || $task != 'sdkLogin' ) {
	$returnValue['status'] = 3;
	$returnValue['message'] = '非法请求';
	echo json_encode($returnValue);
	exit;
} else {
	$sig = md5($token.'|'.$app_key);
	$result = sdKlogin::callUrl($url."?app_id=$app_id&token=$token&sign=$sign","");
	$loginInfo = json_decode($result,true);
	$state = $loginInfo['state'];
	if ($state == 1) {
		$userinfo = $loginInfo['data'];
		$userid = $userinfo['userid'];
		$userName = 'yxh_'.$userid;
		$nickName = '';
		$returnValue = sdKlogin::regist($userName,$nickName,$link);
		echo json_encode($returnValue);			
		exit;	
	} else {
		$returnValue['status'] = 1030;
		$returnValue['message'] = '验证SID失败！';
		echo json_encode($returnValue);
		exit;			
	}
}