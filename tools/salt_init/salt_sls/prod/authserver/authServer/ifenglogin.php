<?php
$task = _get('task');
$rdata = urldecode(_get('data'));
$xml = simplexml_load_string($rdata);
$xml = json_encode($xml);
$xml = json_decode($xml);
$uid = $xml->uid;
$ticket = urlencode($xml->ticket);
$service = 'user.validate';
$partner_id = 1029;
$game_id = 100437;
$server_id = 1;
$formart = 'json';
$partner_key = 'my4rl2hd81zhfutcw7n';
$sign = strtoupper(md5($partner_id.$game_id.$server_id.$ticket.$partner_key)); 
$url = 'http://union.play.ifeng.com/mservice';
if( $task == null || $task != 'sdkLogin' ) {
	$returnValue['status'] = 3;
	$returnValue['message'] = '非法请求';
	echo json_encode($returnValue);
} else {
	$result = sdKlogin::callUrl($url,"service=$service&partner_id=$partner_id&game_id=$game_id&server_id=$server_id&ticket=$ticket&sign=$sign&formart=$formart");
	$returnInfo = json_decode($result,true);
	if ($returnInfo['code'] == 1) {
		$user_id = $returnInfo['data']['user_id'];
		if ($uid == $user_id) {		
			$userName = 'ifeng_'.$user_id;
			$nickName = $returnInfo['data']['nick_name'];;
			$returnValue = sdKlogin::regist($userName,$nickName,$link);
			echo json_encode($returnValue);	
		} else {
			echo json_encode(array('status'=>1030,'message'=>'用户信息不匹配'));
		}		
	} else {
		$returnValue['status'] = 1030;
		$returnValue['message'] = $returnInfo['msg'];
		echo json_encode($returnValue);
	}
}
