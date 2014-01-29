<?php
set_time_limit(600);
include 'function.php';
require 'lenov/rsa.php';
//require 'lenov/RSAUtil.php';
//$pubKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCd95FnJFhPinpNiE/h4VA6bU1rzRa5+a25BxsnFX8TzquWxqDCoe4xG6QKXMXuKvV57tTRpzRo2jeto40eHKClzEgjx9lTYVb2RFHHFWio/YGTfnqIPTVpi7d7uHY+0FZ0lYL5LlW4E2+CQMxFOPRwfqGzMjs1SDlH7lVrLEVy6QIDAQAB';
$pubKey = '559853778c1163677b19a9bbc2d76064';
$content = _get('content');
$signType = _get('signType');
$sign = _get('sign');
$_REQUEST['sign'] = urlencode($sign);
$payTime = time();
$contentInfo = json_decode($content,true);
$money = $contentInfo['money'];
$chargeType = $contentInfo['chargeType'];
$orderid = $contentInfo['orderId'];
$out_trade_no = $contentInfo['out_trade_no'];
$fwqInfo = explode('|',$out_trade_no);
$gameid = $fwqInfo[0];
$userid = $fwqInfo[1];
if ($gameid != $_SC['fwqdm']) {
	if ($gameid == 'zyy02') {
		zfsj('http://sop1.jofgame.com/pay/wdjpay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zjsh_zyy_001') {
		zfsj('http://zj01.jofgame.com/pay/wdjpay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zyy03') {
		zfsj('http://zj03.jofgame.com/pay/wdjpay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zj3') {
		zfsj('http://117.135.139.237:8080/pay/wdjpay.php',$_REQUEST);
		exit;
	}
}
//验证签名
$rsa = new Rsa();
$rsaRes = $rsa->verify($content,$sign);
if ($rsaRes == 1) {
	$checkSql = "select count(*) as sl from ".$common->tname('wdjpayinfo')." where orderid = '$orderid'";
	$res_check = $db->query($checkSql);
	$rows_check = $db->fetch_array($res_check);
	if ($rows_check['sl'] == 0) {
		$common->inserttable('wdjpayinfo',array('content'=>$content,'signType'=>$signType,'sign'=>$sign,'payTime'=>$payTime,'orderid'=>$orderid));
	    $sql_user = "SELECT * FROM ".$common->tname('player')." WHERE userid = $userid LIMIT 1";
	    $res_user = $db->query($sql_user);
	    $rows_user = $db->fetch_array($res_user);
	    $oldYB = 0;
	    $newYB = 0;
	    $sfcg = 'F';
	    $yb = floor($money / 100 * 10);
        if (!empty($rows_user)) {
        	$sfcg = 'S'; 
    		$oldYB = $rows_user['ingot'];
    		$newYB = $rows_user['ingot'] + $yb;
    		$playersid = $rows_user['playersid'];
    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
    		$db->query($sql_p);	    		 
    		vipChongzhi($playersid, $money / 100, $yb, $orderid);    		
    	}
    	writelog('app.php?task=chongzhi&type=sdj&option=pay',json_encode(array('orderId'=>$orderid,'ucid'=>$rows_user['userid'],'payWay'=>$contentInfo['chargeType'],'amount'=>$money / 100,'orderStatus'=>$sfcg,'failedDesc'=>'','createTime'=>$payTime,'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);	    
	}
	echo 'success';
} else {
	$common->inserttable('wdjpayinfo_error',array('content'=>$content,'signType'=>$signType,'sign'=>$sign,'payTime'=>$payTime,'orderid'=>$orderid));
	echo 'FAILED';
}
