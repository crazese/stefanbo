<?php
$task = _get('task');
$rdata = urldecode(_get('data'));
$xml = simplexml_load_string($rdata);
$xml = json_encode($xml);
$xml = json_decode($xml);
$uid = $xml->uid;
$token = urlencode($xml->token);
$url = 'https://pay.wandoujia.com/api/uid/check';
if( $task == null || $task != 'sdkLogin' ) {
	$returnValue['status'] = 3;
	$returnValue['message'] = '非法请求';
	echo json_encode($returnValue);
} else {
	$result = sdKlogin::callUrl($url,"uid=$uid&token=$token");
	if ($result == 'true') {
		$userName = 'wdj_'.$uid;
		$nickName = '';
		$returnValue = sdKlogin::regist($userName,$nickName,$link);
		echo json_encode($returnValue);			
	} else {
		$returnValue['status'] = 1030;
		$returnValue['message'] = '验证SID失败！';
		echo json_encode($returnValue);
	}
}