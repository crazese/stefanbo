<?php 
set_time_limit(600);
include 'function.php';
$cid = 852;
$key = '2b42a1967731ba51f6091c190dc018ff';
$payInfo['cid'] = _get('cid');
$payInfo['uid'] = _get('uid');
$payInfo['order_id'] = _get('order_id');
$payInfo['amount'] = _get('amount');
$payInfo['verifystring'] = _get('verifystring');
$payInfo['payTime'] = time();
$payInfo['userid'] = _get('userid');
$payInfo['serverid'] = _get('serverid');
//md5(cid+uid+order_id+amount+key)
if (md5($payInfo['cid'].$payInfo['uid'].$payInfo['order_id'].$payInfo['amount'].$key) == $payInfo['verifystring']) {
	if ($payInfo['amount'] > 0) {
	    $checkSql = "SELECT * FROM ".$common->tname('br_payinfo')." WHERE order_id = '".$payInfo['order_id']."' && amount = '". $payInfo['amount']."' LIMIT 1";
	    $res_check = $db->query($checkSql);
	    $checknum = $db->num_rows($res_check);
	    if ($checknum == 0) {
	    	$common->inserttable('br1_payinfo',$payInfo);
	    } else {
	    	echo 1;
	    	exit;
	    }	
	    $sql_user = "SELECT * FROM ".$common->tname('player')." WHERE userid = '".$payInfo['userid']."' LIMIT 1";
	    $res_user = $db->query($sql_user);
	    $rows_user = $db->fetch_array($res_user);
	    $oldYB = 0;
	    $newYB = 0;  
	    $orderStatus = "F"; 
	   	$yb = floor($payInfo['amount'] * 10);    		
    	if (!empty($rows_user)) {
    		$oldYB = $rows_user['ingot'];
    		$newYB = $rows_user['ingot'] + $yb;
    		$playersid = $rows_user['playersid'];
    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
    		$db->query($sql_p);	    		 
    		//writelog('app.php?task=ucchongzhi&option=pay',json_encode(array('orderId'=>$insert['orderId'],'ucid'=>$insert['ucid'],'payWay'=>$insert['payWay'],'amount'=>$insert['amount'],'orderStatus'=>$insert['orderStatus'],'failedDesc'=>$insert['failedDesc'],'createTime'=>$insert['createTime'],'status'=>0)),$rows_user['player_level'],$rows_user['userid']);		
    		vipChongzhi($playersid, $payInfo['amount'], $yb, $payInfo['order_id']);
    		$orderStatus = 'S';
    		writelog('app.php?task=chongzhi&type=br1&option=pay',json_encode(array('orderId'=>$payInfo['order_id'],'ucid'=>$payInfo['userid'],'payWay'=>'br1','amount'=>$payInfo['amount'],'orderStatus'=>$orderStatus,'failedDesc'=>'','createTime'=>time(),'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);	    	
    	}	    		    	    	
	} else {
		$common->inserttable('br1_payinfo_error',$payInfo);
	}
} else {
	$common->inserttable('br1_payinfo_error',$payInfo);
}
echo 1;
?>