<?php
$task = _get('task');
$rdata = urldecode(_get('data'));
$xml = simplexml_load_string($rdata);
$xml = json_encode($xml);
$xml = json_decode($xml);
//$open_id = $xml->openid;
$username = $xml->username;
$uid = $xml->uid;
$sessionid = $xml->sessionid;
include(dirname(__FILE__) . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "Dpay_function.php");
$data = array();
if ($jcid == 35) {
	$data['AppId']  		= 520;	
	$p_key     				= 'cfafd694f77bcd1b79949fd95fa47fbb';	
} else {
	$data['AppId']  		= 225;	
	$p_key     				= '772d76d1f10e267e248bf9fac112eae5';
}
$p_sign_type			= 'MD5';
$p_dev_callback_ser_url = 'http://pay.mdong.com.cn/phone/index.php/DeveloperServer/Index';

$data['Version']        = '1.00';

//检查用户是否登录  接口 3
$data['Act'] 	   = 3;
$data['Uin']       = $uid;
$data['SessionId'] = $sessionid;



//发送数据
$result = packageData($data, $p_key, $p_sign_type, $p_dev_callback_ser_url);
//获取返回的数据
$return_data = handleReturnDate($result, $p_key, $p_sign_type);
//返回的数据,jason_decode之后的数据，为数组
$pass = $return_data['Error_Code'];
if( $task == null || $task != 'sdkLogin' ) {
	$returnValue['status'] = 3;
	$returnValue['message'] = '非法请求';
	echo json_encode($returnValue);
	exit;
}
if ($pass == 1) {
	$returnValue['status'] = 1030;
	$returnValue['message'] = '验证SID失败！';
	echo json_encode($returnValue);
	exit;		
} else {
	$userName = '91dj_'.$uid;
	$nickName = '';
	$returnValue = sdKlogin::regist($userName,$nickName,$link);
	echo json_encode($returnValue);			
	exit;	
}
//print_r($return_data);
