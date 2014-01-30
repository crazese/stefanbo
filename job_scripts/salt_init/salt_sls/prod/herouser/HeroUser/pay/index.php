<?php
include 'function.php';
include 'rsa.php';
$rsa = new Rsa();
$getInfo = file_get_contents('php://input', 'r');
$orderInfo = json_decode($rsa->pubDecrypt($getInfo),true);
$userid = $orderInfo['userid'];
$yb = $orderInfo['yb'];
$amount = $orderInfo['amount'];
$orderid = $orderInfo['orderid'];
$jcid = intval($orderInfo['jcid']);
$sql_user = "select a.*,b.qd from ".$common->tname('player')." a,".$common->tname('user')." b where a.userid = $userid && a.userid = b.userid";
$result_user = $db->query($sql_user);
$rows_user = $db->fetch_array($result_user);
if (empty($rows_user) || $jcid == 0) {
	echo json_encode(array('status'=>'21'));
} else {
	$sql_order = "select count(*) as sl from ".$common->tname('orderinfo')." where orderid = '$orderid' && jcid = '$jcid'";
	$res_order = $db->query($sql_order);
	$rows_order = $db->fetch_array($res_order);
	if ($rows_order['sl'] == 0) {
    	$oldYB = $rows_user['ingot'];
    	$newYB = $rows_user['ingot'] + $yb;
    	$playersid = $rows_user['playersid'];
    	$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
    	$db->query($sql_p);	
    	$common->inserttable('orderinfo',array('orderid'=>$orderid,'jcid'=>$jcid));
    	vipChongzhi($playersid, $amount, $yb, $orderid);    			
	} else {
		$oldYB = $rows_user['ingot'];
		$newYB = $rows_user['ingot'];
	}
	echo json_encode(array('status'=>0,'oldYB'=>$oldYB,'newYB'=>$newYB,'playername'=>$rows_user['nickname'],'playersid'=>$rows_user['playersid'],'wjqd'=>$rows_user['qd'],'username'=>$rows_user['ucid']));		
}
