<?php
set_time_limit(600);
include 'function.php';
$key = 'KNMpw57xdFtH';
$insert['result'] = $result = _get('result');
$insert['money'] = $money = _get('money');
$insert['order'] = $order = _get('order');
$insert['mid'] = $mid = _get('mid');
$insert['time'] = $time = _get('time');
$insert['signature'] = $signature = _get('signature');
$insert['ext'] = $ext = _get('ext');
$fwqInfo = explode('|',urldecode($ext));
$gameid = $fwqInfo[0];
$userid = $fwqInfo[1];
if ($gameid != $_SC['fwqdm']) {	
	$_REQUEST['ext'] = urlencode($_REQUEST['ext']);	
	if ($gameid == 'zyy02') {	
		zfsj('http://sop2.jofgame.com/ucpay/dlpay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zjsh_zyy_001') {
		zfsj('http://zj01.jofgame.com/ucpay/dlpay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zyy03') {
		zfsj('http://sop3.jofgame.com/ucpay/dlpay.php',$_REQUEST);
		exit;
	}
}
//order=_&money=_&mid=_&time=_&result=_&ext=_&key=_
$mksign = md5("order=$order&money=$money&mid=$mid&time=$time&result=$result&ext=$ext&key=$key");
if ($mksign == $signature) {
    $checkSql = "SELECT * FROM ".$common->tname('dl_payinfo')." WHERE `order` = '".$order."' && `result` = '".$result."' LIMIT 1";    
    $res_check = $db->query($checkSql);
    $checknum = $db->num_rows($res_check);
    if ($checknum == 0) {
    	$common->inserttable('dl_payinfo',$insert);
    } else {    	
    	echo 'success';
    	exit;
    }
    $sql_user = "SELECT * FROM ".$common->tname('player')." WHERE userid = $userid LIMIT 1";
    $res_user = $db->query($sql_user);
    $rows_user = $db->fetch_array($res_user);
    $oldYB = 0;
    $newYB = 0;
    $sfcg = 'F';  
    if ($result == 1) { 
    	$yb =  floor($money * 10);
    	//$yb = floor($money * 10);
        if (!empty($rows_user)) {
        	$sfcg = 'S'; 
    		$oldYB = $rows_user['ingot'];
    		$newYB = $rows_user['ingot'] + $yb;
    		$playersid = $rows_user['playersid'];
    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
    		$db->query($sql_p);	    		 
    		//writelog('app.php?task=ucchongzhi&option=pay',json_encode(array('orderId'=>$insert['orderId'],'ucid'=>$insert['ucid'],'payWay'=>$insert['payWay'],'amount'=>$insert['amount'],'orderStatus'=>$insert['orderStatus'],'failedDesc'=>$insert['failedDesc'],'createTime'=>$insert['createTime'],'status'=>0)),$rows_user['player_level'],$rows_user['userid']);		
    		vipChongzhi($playersid, $money, $yb, $order);    		
    	}
    }    
    writelog('app.php?task=chongzhi&type=91dj&option=pay',json_encode(array('orderId'=>$order,'ucid'=>$rows_user['userid'],'payWay'=>'91dj','amount'=>$money,'orderStatus'=>'91dj','failedDesc'=>'','createTime'=>time(),'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);	    		
	echo 'success';
} else {
	echo 'failed';
}
