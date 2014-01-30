<?php
set_time_limit(600);
include 'function.php';
//$appid = 'asdf';
$key = '86ad7b3551ef77fb30342a8fdda5e849';
//$app_secret = 'dddfa';
$orderid = _get('orderid');
$outorderid = _get('outorderid');
$amount = _get('amount');
$username = _get('username');
$status = _get('status');
$time = _get('time');
$ext1 = _get('ext1');
$ext2 = _get('ext2');
$sign = _get('sign');
$fwqInfo = explode('|',$ext1);
$gameid = $fwqInfo[0];
$userid = $fwqInfo[1];
if ($gameid != $_SC['fwqdm']) {
	if ($gameid == 'zyy02') {
		zfsj('http://sop1.jofgame.com/pay/kugoupay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zjsh_zyy_001') {
		zfsj('http://zj01.jofgame.com/pay/kugoupay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zyy03') {
		zfsj('http://zj03.jofgame.com/pay/kugoupay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zj3') {
		zfsj('http://117.135.139.237:8080/pay/kugoupay.php',$_REQUEST);
		exit;
	}
}
//orderid+outorderid+amount+username+status+time+ext1+ext2+key
$mksign = md5($orderid.$outorderid.$amount.$username.$status.$time.$ext1.$ext2.$key);
if ($sign == $mksign) {
	if ($status == 1) {
		$checkSql = "select count(*) as sl from ".$common->tname('kugoupayinfo')." where orderid = '$orderid'";
		$res_check = $db->query($checkSql);
		$rows_check = $db->fetch_array($res_check);
		if ($rows_check['sl'] == 0) {
			$common->inserttable('kugoupayinfo',array('orderid'=>$orderid,'outorderid'=>$outorderid,'amount'=>$amount,'username'=>$username,'status'=>$status,'time'=>$time,'ext1'=>$ext1,'ext2'=>$ext2,'sign'=>$sign));
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
	    	writelog('app.php?task=chongzhi&type=kugou&option=pay',json_encode(array('orderId'=>$orderid,'ucid'=>$rows_user['userid'],'payWay'=>'kugou','amount'=>$amount,'orderStatus'=>$sfcg,'failedDesc'=>'','createTime'=>time(),'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);	    
		}			
	}
	echo 'SUCCESS';
} else {
	$common->inserttable('kugoupayinfo_error',array('orderid'=>$orderid,'outorderid'=>$outorderid,'amount'=>$amount,'username'=>$username,'status'=>$status,'time'=>$time,'ext1'=>$ext1,'ext2'=>$ext2,'sign'=>$sign));
	echo 'FAIL';
}
