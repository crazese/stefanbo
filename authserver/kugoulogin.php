<?php
$task = _get('task');
$rdata = urldecode(_get('data'));
$xml = simplexml_load_string($rdata);
$xml = json_encode($xml);
$xml = json_decode($xml);
$uid = $xml->username;
$token = urlencode($xml->token);
$appid = 24;
$appkey = 'kggzjsh%$[3TL5dm69rBY@}5c(}gsO@*{s4d1{%0DG!*67';
$url = 'http://gapi.kugou.com/shouyou/ValidateIsLogined.aspx';
if( $task == null || $task != 'sdkLogin' ) {
	$returnValue['status'] = 3;
	$returnValue['message'] = '非法请求';
	echo json_encode($returnValue);
} else {
	$result = sdKlogin::callUrl($url.'?token='.$token,"");
	$resultInfo = json_decode($result,true);
	$code = $resultInfo['response']['code'];
	if ($code == 0) {
		$userName = 'kugou_'.$uid;
		$nickName = '';
		$returnValue = sdKlogin::regist($userName,$nickName,$link);
		echo json_encode($returnValue);					
	} else {
		$returnValue['status'] = 1030;
		$returnValue['message'] = $resultInfo['response']['message_cn'];
		echo json_encode($returnValue);		
	}
}