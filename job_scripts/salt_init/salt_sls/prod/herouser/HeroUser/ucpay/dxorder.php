<?php
set_time_limit(600);
include 'function.php';
$LinkID = $order['LinkID'] = _get('LinkID');
$mobile = $order['mobile'] = _get('mobile');
$cmd = $order['cmd'] = _get('cmd');
$status = $order['status'] = _get('status');
$spno = $order['spno'] = _get('spno');
$playersid = $order['playersid'] = _get('playersid');
$sysID = $order['sysID'] = _get('sysID');
$order['orderTime'] = time();
$sign = _get('sign');
$key = '!@#$%^&YTRG12344354?~!!';
$mksign = md5("mobile=$mobile&LinkID=$LinkID&cmd=$cmd&spno=$spno&status=$status&sysID=$sysID&playersid=$playersid".$key);
if ($mksign == $sign && $status == 'DELIVRD') {
	$sql = "select count(intID) as sl from ".$common->tname('dx_payinfo')." WHERE LinkID = '$LinkID' limit 1";
	$result = $db->query($sql);
	$rows = $db->fetch_array($result);
	if ($rows['sl'] == 0) {
		$common->inserttable('dx_payinfo',$order);
		$p_sql = "select * from ".$common->tname('player')." where playersid = $playersid limit 1";
		$p_res = $db->query($p_sql);
		$p_rows = $db->fetch_array($p_res);
		if (!empty($p_rows)) {		    
	    	$yb =  50;
        	$sfcg = 'S'; 
    		$oldYB = $p_rows['ingot'];
    		$newYB = $p_rows['ingot'] + $yb;
    		$playersid = $p_rows['playersid'];
    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
    		$db->query($sql_p);	    		 
    		vipChongzhi($playersid, 5, $yb, $LinkID);	  
	    	writelog('app.php?task=chongzhi&type=dxcz&option=pay',json_encode(array('orderId'=>$LinkID,'ucid'=>$p_rows['userid'],'payWay'=>'dxcz','amount'=>5,'orderStatus'=>$sfcg,'failedDesc'=>'','createTime'=>time(),'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$p_rows['player_level'],$p_rows['userid']);	    					
		}
		echo 'ok';
	} else {
		echo 'ok';
	}
} else {
	echo 'no';
}