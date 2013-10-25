<?php
$task = _get('task');
$appid = '156669974087637154';
$uniquekey = '98ea0cfa601b0437bf9221be48ac5efa';
//$cid = 852;
//$key = '2b42a1967731ba51f6091c190dc018ff';
$zcUrl = 'http://www.lewan.cn/api/account/register/vt/3g';
$dlUrl = 'http://www.lewan.cn/api/account/login/vt/3g';
$userName = _get('zh');
$pwd = _get('mm');	
$qq = '';
$verifystring = md5($appid.$userName.$pwd.$uniquekey);
if ($task == 'zcdyyhfuw') {  //注册
	//appid+ username+password+uniquekey
	$dlRes = sdKlogin::callUrl($zcUrl,"username=$userName&password=$pwd&appid=$appid&qq=$qq&verifystring=$verifystring&json=true&channel_id=100");
} else {
	//登录
	$dlRes = sdKlogin::callUrl($dlUrl,"username=$userName&password=$pwd&appid=$appid&qq=$qq&verifystring=$verifystring&json=true&channel_id=100");	
}
if (empty($dlRes)) {
	$returnValue['status'] = 22;
	$returnValue['message'] = '连接服务器失败！';		
	$returnValue['rsn'] = intval(_get('ssn'));		
	echo json_encode($returnValue);
	exit;
} else {
	$returnInfo = json_decode($dlRes,true);
	if (isset($returnInfo['uid'])) {
		$userName = 'br_'.$returnInfo['uid'];
		$nickName = '';
		$returnValue = sdKlogin::regist($userName,$nickName,$link);
		echo json_encode($returnValue);			
		exit;
	} else {
		echo json_encode(array('status'=>23,'message'=>$returnInfo['error'],'rsn'=>intval(_get('ssn')))); 
		exit;
	}
}