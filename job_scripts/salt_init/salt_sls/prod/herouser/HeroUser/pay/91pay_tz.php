<?php
$a = $_REQUEST;
$url = '';
$i = 0;
foreach ($a as $key => $value) {
	$i++;
	if($i==1) {
		$url.="$key=".urlencode($value);
	} else {
		$url.="&$key=".urlencode($value);
	}
   
}
function callucpay($endpoint,$postData) {
	$ch = curl_init($endpoint);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
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
$content = callucpay('http://117.135.139.30:9090/ucpay/91pay.php',$url);
echo $content;
//header("LOCATION:http://hud.jofgame.com:9000/xsyd/ucpay/91pay.php".$url);