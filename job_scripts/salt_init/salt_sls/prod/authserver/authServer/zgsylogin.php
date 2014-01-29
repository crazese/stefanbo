<?php
$task = _get('task');
$rdata = urldecode(_get('data'));
$xml = simplexml_load_string($rdata);
$xml = json_encode($xml);
$xml = json_decode($xml);
$uid = $xml->uid;
$username = $xml->username;
$timestamp = $xml->timestamp;
$sign = $xml->sign;
$key = '52fbabaff62d5d3423a4528e9943e8eb';
//userId&timestamp&key
$mksign = md5($uid.'&'.$timestamp.'&'.$key);
if( $task == null || $task != 'sdkLogin' ) {
	$returnValue['status'] = 3;
	$returnValue['message'] = '非法请求';
	echo json_encode($returnValue);
} else {
	if ($mksign == $sign) {
		$userName = 'zgsy_'.$username;
		$nickName = '';
		$returnValue = sdKlogin::regist($userName,$nickName,$link);
		echo json_encode($returnValue);			
	} else {
		$returnValue['status'] = 1030;
		$returnValue['message'] = '用户验证失败';
		echo json_encode($returnValue);
	}
}