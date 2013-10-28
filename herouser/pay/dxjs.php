<?php
set_time_limit(600);
include 'function.php';
//orderId=$orderId&msg=$msg&ms=$ms&playersid=$playersid&fwqdm=$fwqdm&orderTime=$orderTime&sign=$sign
$key = '!@#$%^&YTRG12344354?~!!';
$orderId = $inser['orderId'] = _get('orderId');
$msg = $inser['msg'] = _get('msg');
$ms = $inser['ms'] =_get('ms');
$playersid = $inser['playersid'] = _get('playersid');
$fwqdm = $inser['fwqdm'] = _get('fwqdm');
$orderTime = $inser['orderTime'] = _get('orderTime');
$sign = _get('sign');
$mksign = md5("orderId=$orderId&msg=$msg&ms=$ms&playersid=$playersid&fwqdm=$fwqdm&orderTime=$orderTime".$key);
if ($mksign == $sign) {
	$id = $common->inserttable('dx_visitinfo',$inser);
	if ($id > 0) {
		echo 'ok';
	} else {
		echo 'no';
	}
} else {
	echo 'no';
}