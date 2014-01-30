<?php
set_time_limit(600);
include 'function.php';
require 'lenov/IappDecrypt.php';
$transdata = _get('transdata');
$sign = _get('sign');
$orderInfo = json_decode($transdata,true);
$key = 'OUU3RUU4MjQyMURENDAzRTlEMDY3QzJERTIyNUQ1MDg0MDU5NUY4OE1UZzBNemczTVRnME56RTFOVFk1TWpnMk9Ea3JNVFUwTmpnNU5UY3hOak0wTlRZd05EVXdNemswTmpZMk56QXlPVGs0TWpNMk5qTXhNakEz';
$tools = new IappDecrypt();
$pass = $tools->validsign($transdata,$sign,$key);
/*if($result == 0)
	//验签名成功，添加处理业务逻辑的代码;
	echo 'SUCCESS';
else
	echo 'FAILED';*/
$fwqInfo = explode('|',$orderInfo['cpprivate']);
$gameid = $fwqInfo[0];
$userid = $fwqInfo[1];
if ($gameid != $_SC['fwqdm']) {
	if ($gameid == 'zyy02') {
		zfsj('http://sop1.jofgame.com/pay/lenovpay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zjsh_zyy_001') {
		zfsj('http://zj01.jofgame.com/pay/lenovpay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zyy03') {
		zfsj('http://zj03.jofgame.com/pay/lenovpay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zj3') {
		zfsj('http://117.135.139.237:8080/pay/lenovpay.php',$_REQUEST);
		exit;
	}
}
$transid = $orderInfo['transid'];
if ($orderInfo['result'] == 0) {  //交易成功		
	$checkSql = "select count(*) as sl from ".$common->tname('lenovpayinfo')." where transid = '$transid'";
	$res_check = $db->query($checkSql);
	$rows_check = $db->fetch_array($res_check);
	if ($rows_check['sl'] == 0) {
		if ($pass == 0) {
			$common->inserttable('lenovpayinfo',array('transid'=>$transid,'transdata'=>$transdata,'sign'=>$sign,'payTime'=>time()));
		    $sql_user = "SELECT * FROM ".$common->tname('player')." WHERE userid = $userid LIMIT 1";
		    $res_user = $db->query($sql_user);
		    $rows_user = $db->fetch_array($res_user);
		    $oldYB = 0;
		    $newYB = 0;
		    $sfcg = 'F';
		    $yb = floor($orderInfo['money'] / 100 * 10);
	        if (!empty($rows_user)) {
	        	$sfcg = 'S'; 
	    		$oldYB = $rows_user['ingot'];
	    		$newYB = $rows_user['ingot'] + $yb;
	    		$playersid = $rows_user['playersid'];
	    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
	    		$db->query($sql_p);	    		 
	    		vipChongzhi($playersid, $orderInfo['money'] / 100, $yb, $transid);    		
	    	}
	    	writelog('app.php?task=chongzhi&type=lenov&option=pay',json_encode(array('orderId'=>$transid,'ucid'=>$rows_user['userid'],'payWay'=>'lenov','amount'=>$orderInfo['money'] / 100,'orderStatus'=>$sfcg,'failedDesc'=>'','createTime'=>time(),'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);	    
			echo 'SUCCESS';
		} else {
			zfsj('http://zj02.jofgame.com/pay/lenovpay.php',$_REQUEST);
			$common->inserttable('lenovpayinfo_error',array('transid'=>$transid,'transdata'=>$transdata,'sign'=>$sign,'payTime'=>time()));
			//echo 'SUCCESS';
			sleep(3);			
	   }				
	} else {
		echo 'SUCCESS';
	}
} else {
	$common->inserttable('lenovpayinfo_error',array('transid'=>$transid,'transdata'=>$transdata,'sign'=>$sign,'payTime'=>time()));
	echo 'fail2';
}
