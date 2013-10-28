<?php
set_time_limit(600);
include 'function.php';
$appid = 341;
$appkey = '5fdc61c00f684000afbe9ad7222e56d0';
$app_secret = 'cbacdac1548aad45a0b305b7a5c9894b';
$amount = _get('amount');
$cardtype = _get('cardtype');
$orderid = _get('orderid');
$result = _get('result');
$timetamp = _get('timetamp');
$aid = _get('aid');
$client_secret = _get('client_secret');
$fwqInfo = explode('|',urldecode($aid));
$gameid = $fwqInfo[0];
$userid = $fwqInfo[1];
if ($gameid != $_SC['fwqdm']) {
	if ($gameid == 'zyy02') {
		zfsj('http://sop1.jofgame.com/pay/dkpay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zjsh_zyy_001') {
		zfsj('http://zj01.jofgame.com/pay/dkpay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zyy03') {
		zfsj('http://zj03.jofgame.com/pay/dkpay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zj3') {
		zfsj('http://117.135.139.237:8080/pay/dkpay.php',$_REQUEST);
		exit;
	}
}
//amount值+ cardtype值+ orderid值+ result值+ timetamp 值+app_secret值+urlencode(aid)
$mksign = md5($amount.$cardtype.$orderid.$result.$timetamp.$app_secret.urlencode($aid));
if ($mksign == $client_secret) {
	if ($result == 1) {
		$checkSql = "select count(*) as sl from ".$common->tname('dkpayinfo')." where orderid = '$orderid'";
		$res_check = $db->query($checkSql);
		$rows_check = $db->fetch_array($res_check);
		if ($rows_check['sl'] == 0) {
			$common->inserttable('dkpayinfo',array('amount'=>$amount,'cardtype'=>$cardtype,'orderid'=>$orderid,'result'=>$result,'timetamp'=>$timetamp,'aid'=>$aid,'client_secret'=>$client_secret));
		    $sql_user = "SELECT * FROM ".$common->tname('player')." WHERE userid = $userid LIMIT 1";
		    $res_user = $db->query($sql_user);
		    $rows_user = $db->fetch_array($res_user);
		    $oldYB = 0;
		    $newYB = 0;
		    $sfcg = 'F';
		    $yb = floor($amount * 10);
	        if (!empty($rows_user)) {
	        	$sfcg = 'S'; 
	    		$oldYB = $rows_user['ingot'];
	    		$newYB = $rows_user['ingot'] + $yb;
	    		$playersid = $rows_user['playersid'];
	    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
	    		$db->query($sql_p);	    		 
	    		vipChongzhi($playersid, $amount, $yb, $orderid);    		
	    	}
	    	writelog('app.php?task=chongzhi&type=bddk&option=pay',json_encode(array('orderId'=>$orderid,'ucid'=>$rows_user['userid'],'payWay'=>$cardtype,'amount'=>$amount,'orderStatus'=>$sfcg,'failedDesc'=>'','createTime'=>$timetamp,'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);	    
		}		
	} else {
		$common->inserttable('dkpayinfo_error',array('amount'=>$amount,'cardtype'=>$cardtype,'orderid'=>$orderid,'result'=>$result,'timetamp'=>$timetamp,'aid'=>$aid,'client_secret'=>$client_secret));
	}
	echo 'SUCCESS';
} else {
	$common->inserttable('dkpayinfo_error',array('amount'=>$amount,'cardtype'=>$cardtype,'orderid'=>$orderid,'result'=>$result,'timetamp'=>$timetamp,'aid'=>$aid,'client_secret'=>$client_secret));
	echo 'ERROR_SIGN';
}