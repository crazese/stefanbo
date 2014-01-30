<?php
$key = "344839ebbf2fd4232a"; // ConsumerKey
$secret = "6bc1d0d3ffdef9e2572b2fd5b49d4de47ad097"; // ConsumerSecret
$option = _get('option');
$rdata = urldecode(_get('data'));
if (!empty($rdata)) {
$xml = simplexml_load_string($rdata);
$xml = json_encode($xml);
$xml = json_decode($xml);	
$userid = $xml->userid;
$gameid = $xml->gameid;
$code = $xml->code;
}

if( $task == null || $task != 'sdkLogin' ) {
	$returnValue['status'] = 3;
	$returnValue['message'] = '非法请求';
	$returnValue['rsn'] = intval(_get('ssn'));	
	echo json_encode($returnValue);
	exit;
} else {
	$gamebarTime = substr($code,-13);
	$checkCode = substr($code,0,strlen($code) - 13);
	if (md5($userid.$gameid.$serect.$gamebarTime) != $checkCode) {
		$returnValue['status'] = 1030;
		$returnValue['message'] = '身份验证失败';
		$returnValue['rsn'] = intval(_get('ssn'));	
		echo json_encode($returnValue);
		exit;		
	}
	$userName = $nickName = 'yxb_'.$userid;
	$returnValue = sdKlogin::regist($userName,$nickName,$link);
	echo json_encode($returnValue);
}		
		
	