<?php
$task = _get('task');
$rdata = urldecode(_get('data'));
$xml = simplexml_load_string($rdata);
$xml = json_encode($xml);
$xml = json_decode($xml);
$sid = $xml->sid;
$appid = 'whyzl001';
$url = 'http://baina.com:8010/bainasdk/secondValidation.jsp';
if( $task == null || $task != 'sdkLogin' ) {
	$returnValue['status'] = 3;
	$returnValue['message'] = '非法请求';
	echo json_encode($returnValue);
	exit;
} else {
	$result = sdKlogin::callUrl($url,"ComeFrom=$appid&SID=$sid");
	$returnInfo = json_decode($result,true);
	if (isset($returnInfo['ResultCode'])) {
		if ($returnInfo['ResultCode'] == 0) {
			$userName = 'bn_'.$returnInfo['UserID'];
			$nickName = '';
			$returnValue = sdKlogin::regist($userName,$nickName,$link);
			echo json_encode($returnValue);					
		} else {
			$returnValue['status'] = 1030;
			$returnValue['message'] = '验证SID失败！';
			echo json_encode($returnValue);			
		}
	} else {
		$returnValue['status'] = 1030;
		$returnValue['message'] = '验证SID失败！';
		echo json_encode($returnValue);		
	}
}