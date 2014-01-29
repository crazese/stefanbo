<?php
set_time_limit(600);
include 'function.php';
$Key = '6xmua6$%^&fds5po';
$AccountID = $order['AccountID'] = _get('AccountID');
$GameServerID = $order['GameServerID'] = _get('GameServerID');
$Timestamp = $order['Timestamp'] = _get('Timestamp');
$OrderSerial = $order['OrderSerial'] = _get('OrderSerial');
$Amount = $order['Amount'] = _get('Amount');
$Point = $order['Point'] = _get('Point');
$Sign = $order['Sign'] = _get('Sign');
if ($GameServerID == 100000) {
	zfsj('http://sop2.jofgame.com/ucpay/ldpay.php',$_REQUEST);
	exit;
}
//Sign = MD5({AccountID}_{GameServerID}_{Timestamp}_{OrderSerial}_{Amount}_{Point}_{Key})
if (md5($AccountID."_".$GameServerID."_".$Timestamp."_".$OrderSerial."_".$Amount."_".$Point."_".$Key) == $Sign) {
	$uid = '91_h5_'.$GameServerID.'_'.$AccountID;
    $checkSql = "SELECT * FROM ".$common->tname('ol_91h5_payinfo')." WHERE OrderSerial = '$OrderSerial' LIMIT 1";
    $res_check = $db->query($checkSql);
    $checknum = $db->num_rows($res_check);
    if ($checknum == 0) {
    	$common->inserttable('ol_91h5_payinfo',$order);
    } else {
    	echo '0|该订单不能重复充值';
    	exit;
    }
    $sql_user = "SELECT a.userid,b.playersid,b.ingot,b.player_level FROM ".$common->tname('user')." a,".$common->tname('player')." b WHERE a.username = '$uid' && a.userid = b.userid LIMIT 1";
    $res_user = $db->query($sql_user);
    $rows_user = $db->fetch_array($res_user);
    $oldYB = 0;
    $newYB = 0; 
    $yb = floor($Amount * 10);    		
    if (!empty($rows_user)) {
    	$oldYB = $rows_user['ingot'];
    	$newYB = $rows_user['ingot'] + $yb;
    	$playersid = $rows_user['playersid'];
    	$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
    	$db->query($sql_p);	    		 
    	vipChongzhi($playersid, $Amount, $yb, $OrderSerial);
    	writelog('app.php?task=chongzhi&type=91h5&option=pay',json_encode(array('orderId'=>$OrderSerial,'ucid'=>$uid,'payWay'=>'91h5','amount'=>$Amount,'orderStatus'=>'S','failedDesc'=>'','createTime'=>time(),'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);    	
    	echo '1|成功'; 
    } else {
    	echo '0|未找到角色信息';
    }        	
} else {
	echo '0|签名验证失败';
}