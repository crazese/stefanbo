<?php
$task = _get('task');
$rdata = urldecode(_get('data'));
$xml = simplexml_load_string($rdata);
$xml = json_encode($xml);
$xml = json_decode($xml);
$lpsust = $xml->token;
$realm = 'zj.jofgame.com';
//$url = 'https://uss.lenovomm.com/interserver/authen/1.2/getaccountid';
$url = 'http://passport.lenovo.com/interserver/authen/1.2/getaccountid';
if( $task == null || $task != 'sdkLogin' ) {
	$returnValue['status'] = 3;
	$returnValue['message'] = '非法请求';
	echo json_encode($returnValue);
} else {
	$result = sdKlogin::callUrl($url,"realm=$realm&lpsust=$lpsust");
	if ($result === false) {
		$returnValue['status'] = 1030;
		$returnValue['message'] = '链接服务器失败！';
		echo json_encode($returnValue);
		exit;				
	}
	/*$dom = new DOMDocument();
	$dom->loadXML($result);
	$xmlInfo = sdKlogin::getArray($dom->documentElement);*/
	$xmlInfo = json_decode(json_encode(simplexml_load_string($result)),true);
	if (isset($xmlInfo['AccountID'])) {
		$node_name = $xmlInfo['AccountID'];
	} else {
		$returnValue['status'] = 1030;
		$returnValue['message'] = '身份验证失败！';
		echo json_encode($returnValue);
		exit;			
	}
	$pid = $node_name;
	$userName = 'lenov_'.$pid;
	$nickName = '';
	$returnValue = sdKlogin::regist($userName,$nickName,$link);
	echo json_encode($returnValue);		
	/*if ($result == 'true') {
		$userName = 'wdj_'.$uid;
		$nickName = '';
		$returnValue = sdKlogin::regist($userName,$nickName,$link);
		echo json_encode($returnValue);			
	} else {
		$returnValue['status'] = 1030;
		$returnValue['message'] = '验证SID失败！';
		echo json_encode($returnValue);
	}*/
}