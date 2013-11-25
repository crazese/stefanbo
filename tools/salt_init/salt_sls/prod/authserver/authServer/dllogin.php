<?php
$task = _get('task');
$rdata = urldecode(_get('data'));
$xml = simplexml_load_string($rdata);
$xml = json_encode($xml);
$xml = json_decode($xml);
$mid = $xml->uid;
$token = $xml->token;
$url = 'http://connect.d.cn/open/member/info/';
if ($jcid == 36) {
	$app_id = 493;
	$app_key = 'FfqQXSoK';	
} else {
	$app_id = 393;
	$app_key = 'GNzoJSPe';
}
if( $task == null || $task != 'sdkLogin' ) {
	$returnValue['status'] = 3;
	$returnValue['message'] = '非法请求';
	echo json_encode($returnValue);
	exit;
} else {
	$sig = md5($token.'|'.$app_key);
	$result = sdKlogin::callUrl($url,"app_id=$app_id&mid=$mid&token=$token&sig=$sig");
	$userinfo = json_decode($result,true);
	if ($userinfo['error_code'] == 0) {
		if ($userinfo['memberId'] == $mid) {
			$userName = 'dl_'.$mid;
			$nickName = $userinfo['nickname'];
			$returnValue = sdKlogin::regist($userName,$nickName,$link);
			echo json_encode($returnValue);			
			exit;				
		} else {
			$returnValue['status'] = 1030;
			$returnValue['message'] = '身份信息不匹配！';
			echo json_encode($returnValue);
			exit;				
		}
	} else {
		$returnValue['status'] = 1030;
		$returnValue['message'] = '验证SID失败！';
		echo json_encode($returnValue);
		exit;			
	}
}