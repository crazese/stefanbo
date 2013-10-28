<?php
$task = _get('task');
$rdata = urldecode(_get('data'));
$xml = simplexml_load_string($rdata);
$xml = json_encode($xml);
$xml = json_decode($xml);
$skyid = $xml->skyid;
$ticketid = $xml->ticketid;
$username = $xml->username;
$appid = '6000795';
$appkey = '09e0093d27f22dd0b3d3fe9aa14ef150';  //调试appkey test123456
$url = 'http://sdkpassport.51mrp.com/ticket';
function urlencode_rfc3986($input) {
	  if (is_array($input)) {
		return array_map("urlencode_rfc3986", $input);
	  } else if (is_scalar($input)) {
		return $input;
	  } else {
		return '';
	  }
}
function build_http_query($params) {
	if (!$params) return '';
	// Urlencode both keys and values
	$keys = urlencode_rfc3986(array_keys($params));
	$values = urlencode_rfc3986(array_values($params));
	$params = array_combine($keys, $values);
	
	
	// Parameters are sorted by name, using lexicographical byte value ordering.
	// Ref: Spec: 9.1.1 (1)
	uksort($params, 'strcmp');
	unset($params['signature']);
	$pairs = array();
	foreach ($params as $parameter => $value) {
	  if (is_array($value)) {
		// If two or more parameters share the same name, they are sorted by their value
		// Ref: Spec: 9.1.1 (1)
		// June 12th, 2010 - changed to sort because of issue 164 by hidetaka
		sort($value, SORT_STRING);
		foreach ($value as $duplicate_value) {
		  $pairs[] = $parameter . '=' . $duplicate_value;
		}
	  } else {
		$pairs[] = $parameter . '=' . $value;
	  }
	}
	// For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61)
	// Each name-value pair is separated by an '&' character (ASCII code 38)
	return implode('&', $pairs);
}
//$mkSignature = SetToHexString(hash_hmac('sha1',"appId=$appId&cpOrderId=$cpOrderId&cpUserInfo=$cpUserInfo&uid=$uid&orderId=$orderId&orderStatus=$orderStatus&payFee=$payFee&productCode=$productCode&productName=$productName&payTime=$payTime&signature=$signature",$AppKey));
$parmams = build_http_query(array('skyid'=>$skyid,'ticket'=>$ticketid,'appid'=>$appid));
//sign = md5(prestr+"&sign=密钥")；
$sign = md5($parmams.'&sign='.$appkey);
if( $task == null || $task != 'sdkLogin' ) {
	$returnValue['status'] = 3;
	$returnValue['message'] = '非法请求';
	echo json_encode($returnValue);
} else {
	$result = sdKlogin::callUrl($url,json_encode(array('skyid'=>$skyid,'ticket'=>$ticketid,'appid'=>$appid,'sign'=>$sign)));
	$returnInfo = json_decode($result,true);
	if ($returnInfo['code'] == 200) {
		$userName = 'mp_'.$returnInfo['username'];
		$nickName = '';
		$returnValue = sdKlogin::regist($userName,$nickName,$link);
		echo json_encode($returnValue);			
	} else {
		$returnValue['status'] = 1030;
		$returnValue['message'] = '身份验证失败';
		echo json_encode($returnValue);		
	}
}
