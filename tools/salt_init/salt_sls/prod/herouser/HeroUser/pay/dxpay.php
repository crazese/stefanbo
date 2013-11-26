<?php
set_time_limit(600);
include 'function.php';
//mobile=&LinkID=&cmd=&spno=&status=
$LinkID = _get('LinkID');
$mobile = _get('mobile');
$cmd = _get('cmd');
$status = _get('status');
$spno = _get('spno');
$key = '!@#$%^&YTRG12344354?~!!';
$sql = "select * from ".$common->tname('dx_visitinfo')." where orderId = '$cmd' order by intID desc limit 1";
$reslut = $db->query($sql);
$rows = $db->fetch_array($reslut);
if (!empty($rows)) {
	$url_1 = 'http://sop1.jofgame.com/ucpay/dxorder.php';
	$url_2 = 'http://sop2.jofgame.com/ucpay/dxorder.php';
	$url_3 = 'http://zj01.jofgame.com/ucpay/dxorder.php';
	$url_4 = 'http://sop3.jofgame.com/ucpay/dxorder.php';
	$fwqdm = $rows['fwqdm'];
	$sysID = $rows['intID'];
	$playersid = $rows['playersid'];
	$sign = md5("mobile=$mobile&LinkID=$LinkID&cmd=$cmd&spno=$spno&status=$status&sysID=$sysID&playersid=$playersid".$key);
	$_REQUEST['sysID'] = $sysID;
	$_REQUEST['playersid'] = $playersid;
	$_REQUEST['sign'] = $sign;
	if ($fwqdm == 'zyy01') {		
		zfsj($url_1,$_REQUEST);
	} elseif ($fwqdm == 'zyy02') {
		zfsj($url_2,$_REQUEST);
	} elseif ($fwqdm == 'zyy03') {
		zfsj($url_4,$_REQUEST);
	} elseif ($fwqdm == 'zjsh_zyy_001') {
		zfsj($url_3,$_REQUEST);
	}
} else {
	echo 'nodata';
}