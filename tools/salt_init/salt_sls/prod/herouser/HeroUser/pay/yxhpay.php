<?php
set_time_limit(600);
include 'function.php';
$app_key = 'bf07a6d949b5f841fbd1a1814ecda208';
$insert['username'] = $username = _get('username');
$insert['change_id'] = $change_id = _get('change_id');
$insert['money'] = $money = _get('money');
$insert['hash'] = $hash = _get('hash');
$insert['ad_key'] = $ad_key = _get('ad_key');
$insert['object'] = $object = _get('object');
$insert['orderTime'] = $orderTime = time();
$fwqInfo = explode('|',urldecode($object));
$gameid = $fwqInfo[0];
$userid = $fwqInfo[1];
if ($gameid != $_SC['fwqdm']) {
	if ($gameid == 'zyy02') {
		zfsj('http://sop1.jofgame.com/pay/yxhpay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zjsh_zyy_001') {
		zfsj('http://zj01.jofgame.com/pay/yxhpay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zyy03') {
		zfsj('http://zj03.jofgame.com/pay/yxhpay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zj3') {
		zfsj('http://117.135.139.237:8080/pay/yxhpay.php',$_REQUEST);
		exit;
	}
}
//md5(username|change_id|money |app_key)
$mksign = md5("$username|$change_id|$money|$app_key");
if ($mksign == $hash) {
    $checkSql = "SELECT * FROM ".$common->tname('yxh_payinfo')." WHERE `change_id` = '".$change_id."' LIMIT 1";    
    $res_check = $db->query($checkSql);
    $checknum = $db->num_rows($res_check);
    if ($checknum == 0) {
    	$common->inserttable('yxh_payinfo',$insert);
    } else {    	
    	echo '1';
    	exit;
    }
    $sql_user = "SELECT * FROM ".$common->tname('player')." WHERE userid = $userid LIMIT 1";
    $res_user = $db->query($sql_user);
    $rows_user = $db->fetch_array($res_user);
    $oldYB = 0;
    $newYB = 0;
    $sfcg = 'F';  
    $yb =  floor($money * 10);
    if (!empty($rows_user)) {
        $sfcg = 'S'; 
    	$oldYB = $rows_user['ingot'];
    	$newYB = $rows_user['ingot'] + $yb;
    	$playersid = $rows_user['playersid'];
    	$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
    	$db->query($sql_p);	    		 
    	vipChongzhi($playersid, $money, $yb, $change_id);    		
    }
    writelog('app.php?task=chongzhi&type=yxh&option=pay',json_encode(array('orderId'=>$change_id,'ucid'=>$rows_user['userid'],'payWay'=>'yxh','amount'=>$money,'orderStatus'=>'91dj','failedDesc'=>'','createTime'=>time(),'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);	    		
}
echo '1';