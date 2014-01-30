<?php
$task = _get('task');
$rdata = urldecode(_get('data'));
$xml = simplexml_load_string($rdata);
$xml = json_encode($xml);
$xml = json_decode($xml);
$Uin = $xml->uin;
$SessionKey = urlencode($xml->sessionkey);
$MerId = 10000008;
$Act = 4;
$MerchantKey = '2ccbb43f52a9dbc2fd288bd69f850625';
$EncString = md5($MerId.$Act.$Uin.$SessionKey.$MerchantKey);
$url = 'http://210.83.86.176/mpay/index.php/user_center';
if( $task == null || $task != 'sdkLogin' ) {
	$returnValue['status'] = 3;
	$returnValue['message'] = '非法请求';
	echo json_encode($returnValue);
} else {
	$result = sdKlogin::callUrl($url."?MerId=$MerId&Act=$Act&Uin=$Uin&SessionKey=$SessionKey&EncString=$EncString","");
	$rgInfo = json_decode($result,true);
	if ($rgInfo['ErrorCode'] == 1) {
		$userName = 'rg_'.$Uin;
		$nickName = '';
		$returnValue = sdKlogin::regist($userName,$nickName,$link);
		echo json_encode($returnValue);			
	} else {
		$returnValue['status'] = 1030;
		$returnValue['message'] = $rgInfo['ErrorDesc'];
		echo json_encode($returnValue);
	}
}