<?php
include 'function.php';
$raw_post_data = $postData = file_get_contents('php://input', 'r');
$raw_post_data = json_decode($raw_post_data,true);
$cpId = 690;
$gameID = 506135;
$appKey = 'f23e3bdea56048a71c5100ee824ca699';
$serverId = 1980;
if (!empty($raw_post_data)) {
	$nowTime = time();
	$payInfo = $raw_post_data['data'];
	$sign = $raw_post_data['sign'];
	$insert['orderId'] = $payInfo['orderId'];
	$insert['gameId'] = $payInfo['gameId'];
	$insert['serverId'] = $payInfo['serverId'];
	$insert['ucid'] = $payInfo['ucid'];
	$insert['payWay'] = $payInfo['payWay'];
	$insert['amount'] = $payInfo['amount'];
	$insert['callbackInfo'] = $payInfo['callbackInfo'];
	$insert['orderStatus'] = $payInfo['orderStatus'];
	$insert['failedDesc'] = mysql_escape_string($payInfo['failedDesc']);
	$insert['createTime'] = $nowTime;
	$insert['processed'] = 1;
	$backInfo = explode('|',$insert['callbackInfo']);
	$userid = 0;
	if (count($backInfo) > 1) {
		$fwqdm = $backInfo[1];
		$userid = $backInfo[0];
	} else {
		$fwqdm = $insert['callbackInfo']; 
	}
	if ($fwqdm != $_SC['fwqdm']) {
		if ($fwqdm == 'zyy02') {
			zfsj('http://sop1.jofgame.com/pay/ucpay.php',$postData,false);
			exit;
		} elseif ($fwqdm == 'zjsh_zyy_001') {
			zfsj('http://zj02.jofgame.com/pay/ucpay.php',$postData,false);
			exit;
		} /*elseif ($fwqdm == 'zjsh_zyy_002') {
			zfsj('http://117.135.139.237:8080/pay/91pay.php',$_REQUEST);
			exit;
		}*/ elseif ($fwqdm == 'zyy03') {
			zfsj('http://sop3.jofgame.com/pay/ucpay.php',$postData,false);
			exit;
		} elseif ($fwqdm == 'zj3') {
			zfsj('http://117.135.139.237:8080/pay/ucpay.php',$postData,false);
			exit;
		}
	}	
	$makesign = md5($cpId.'amount='.$insert['amount'].'callbackInfo='.$insert['callbackInfo'].'failedDesc='.$insert['failedDesc'].'gameId='.$gameID.'orderId='.$insert['orderId'].'orderStatus='.$insert['orderStatus'].'payWay='.$insert['payWay'].'serverId='.$serverId.'ucid='.$insert['ucid'].$appKey);
    if ($makesign == $sign) {
    	$checkSql = "SELECT * FROM ".$common->tname('uc_payinfo')." WHERE orderId = '".$insert['orderId']."' && orderStatus = '".$insert['orderStatus']."' LIMIT 1";
    	$res_check = $db->query($checkSql);
    	$checknum = $db->num_rows($res_check);
    	if ($checknum == 0) {
    		$common->inserttable('uc_payinfo',$insert);
    	} else {
    		echo 'SUCCESS';
    		exit;
    	}
    	if ($userid > 0) {
    		$sql_user = "SELECT * FROM ".$common->tname('player')." WHERE userid = $userid LIMIT 1";
    	} else {
    		$sql_user = "SELECT a.userid,b.playersid,b.ingot,b.player_level FROM ".$common->tname('user')." a,".$common->tname('player')." b WHERE a.username = '".$insert['ucid']."' && a.userid = b.userid LIMIT 1";
    	}	        
    	$res_user = $db->query($sql_user);
    	$rows_user = $db->fetch_array($res_user);
    	$oldYB = 0;
    	$newYB = 0;
    	if ($insert['orderStatus'] == 'S') {
	    	$yb = floor($insert['amount'] * 10);    		
	    	if (!empty($rows_user)) {
	    		$oldYB = $rows_user['ingot'];
	    		$newYB = $rows_user['ingot'] + $yb;
	    		$playersid = $rows_user['playersid'];
	    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
	    		$db->query($sql_p);	    		 
	    		//writelog('app.php?task=ucchongzhi&option=pay',json_encode(array('orderId'=>$insert['orderId'],'ucid'=>$insert['ucid'],'payWay'=>$insert['payWay'],'amount'=>$insert['amount'],'orderStatus'=>$insert['orderStatus'],'failedDesc'=>$insert['failedDesc'],'createTime'=>$insert['createTime'],'status'=>0)),$rows_user['player_level'],$rows_user['userid']);		
	    		vipChongzhi($playersid, $insert['amount'], $yb, $insert['orderId']);
	    	}
    	}
        writelog('app.php?task=chongzhi&type=uc&option=pay',json_encode(array('orderId'=>$insert['orderId'],'ucid'=>$insert['ucid'],'payWay'=>$insert['payWay'],'amount'=>$insert['amount'],'orderStatus'=>$insert['orderStatus'],'failedDesc'=>$insert['failedDesc'],'createTime'=>$insert['createTime'],'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);	   	
    } else {
    	$common->inserttable('uc_payinfo_error',$insert);
    }
    echo 'SUCCESS';	
} else {
	echo 'FAILURE';
}
