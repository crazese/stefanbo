<?php
//$url = 'http://gos.uxin.com/gameopt/gameuser/checkOpenid.do';
$task = _get('task');
$rdata = urldecode(_get('data'));
$xml = simplexml_load_string($rdata);
$xml = json_encode($xml);
$xml = json_decode($xml);
$openid = $xml->openid;
$appid = '10006';
$appkey = 'DD40D68FEAEC65B9ED57F8FEE1274967';
$url = 'http://gos.uxin.com/gameopt/gameuser/checkOpenid.do';
if( $task == null || $task != 'sdkLogin' ) {
	$returnValue['status'] = 3;
	$returnValue['message'] = '非法请求';
	echo json_encode($returnValue);
	exit;
} else {
	$sign = md5($appid.$openid.$appkey);
	$result = sdKlogin::callUrl($url,"openid=$openid&appid=$appid&sign=$sign");
	$returnInfo = json_decode($result,true);
	if (isset($returnInfo['result'])) {
		if ($returnInfo['result'] == 0) {
			$userName = 'yx_'.$openid;
			$nickName = '';
			$returnValue = sdKlogin::regist($userName,$nickName,$link);
			echo json_encode($returnValue);					
		} else {
			$returnValue['status'] = 1030;
			$returnValue['message'] = '验证openid失败！';
			echo json_encode($returnValue);			
		}
	} else {
		$returnValue['status'] = 1030;
		$returnValue['message'] = '验证openid失败！';
		echo json_encode($returnValue);		
	}
}