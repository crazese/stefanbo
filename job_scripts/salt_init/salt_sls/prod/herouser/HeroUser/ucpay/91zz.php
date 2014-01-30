<?php
function zfsj($url,$data) {
	if (!empty($data)) {
		foreach ($data as $key => $value) {
			$str[] = $key.'='.$value;
		}
		$sendData = implode('&',$str);
		$str = callUrl($url."?".$sendData,'');
		echo $str;
	}
}
function callUrl($endpoint,$postData,$auth_header = '') {
	$ch = curl_init($endpoint);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
	if (!empty($auth_header)) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($auth_header));			
	}	
	$response = curl_exec($ch);
	$errno    = curl_errno($ch);
	$errmsg   = curl_error($ch);
	curl_close($ch);
	if ($errno != 0) {
		return false;
	} else {
		return $response;
	}
}
zfsj('http://127.0.0.1:8090/ucpay/91djpay.php',$_REQUEST);