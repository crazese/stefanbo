<?php
$rdata = urldecode(_get('data'));
$xml = simplexml_load_string($rdata);
$xml = json_encode($xml);
$xml = json_decode($xml);
include(dirname(__FILE__) . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "class_tea.php");
$task = _get('task');
if ($jcid == 34) {
	$channelid = 1399658373;
	$secret = 'ddd4b058fd9126a4';	
} else {
	$channelid = 31645588;
	$secret = '8370d41aba6a15d4';
}
$url = 'http://api.gfan.com/uc1/common/verify_token';
$Agent = "User-Agent:channelID=$channelid";
$token = $xml->token;
/*$zcUrl = 'http://api.gfan.com/uc1/common/register';
$dlUrl = 'http://api.gfan.com/uc1/common/login';
//风卷与人民币比率10:1

$userName = _get('zh');
$pwd = _get('mm');	
$email = _get('e');
if (empty($email)) {
	$email = $userName.'@163.com';
}
if ($task == 'zcdyyhfuw') {
	$str = "<request><username>$userName</username><password>$pwd</password><email>$email</email></request>";
} else {
	$str = "
<request>
<username>$userName</username>
<password>$pwd</password>
</request>";
}
$t = new tea();
$jiamistr = base64_encode($t->encrypt(trim($str), $secret));

if ($task == 'zcdyyhfuw') {  //注册
	//appid+ username+password+uniquekey
	$dlRes = sdKlogin::callUrl($zcUrl,$jiamistr,$Agent);
} else {
	//登录
	$dlRes = sdKlogin::callUrl($dlUrl,$jiamistr,$Agent);	
}*/
$dlRes = sdKlogin::callUrl($url,"token=$token",$Agent);
if (empty($dlRes)) {
	$returnValue['status'] = 22;
	$returnValue['message'] = '用户验证失败或连接服务器失败！';		
	$returnValue['rsn'] = intval(_get('ssn'));		
	echo json_encode($returnValue);
	exit;
} else {
	/*$user_xml = simplexml_load_string($dlRes);
	$user_xml = json_decode(json_encode($user_xml));*/
	$user_xml = json_decode($dlRes);
	if (isset($user_xml->uid)) {
		$userName = 'jf_'.$user_xml->uid;
		$nickName = '';
		$returnValue = sdKlogin::regist($userName,$nickName,$link);
		echo json_encode($returnValue);			
		exit;
	} else {
		echo json_encode(array('status'=>23,'message'=>'用户验证失败','rsn'=>intval(_get('ssn')))); 
		exit;
	}
}