<?php
include 'function.php';
$nowTime = time();
$insert['sign'] = $sign = _get('sign');
$insert['orderId'] = _get('orderId');
$insert['openid'] = _get('openid');
$insert['amount'] = _get('amount');
$insert['actualAmount'] = _get('actualAmount');
$insert['extraInfo'] = urldecode(_get('extraInfo'));
$insert['success'] = _get('success');
$insert['msg'] = _get('msg');
$postData = "sign=".$insert['sign']."&orderId=".$insert['orderId']."&openid=".$insert['openid']."&amount=".$insert['amount']."&actualAmount=".$insert['actualAmount']."&extraInfo=".urlencode($insert['extraInfo'])."&success=".$insert['success']."&msg=".$insert['msg'];
$makesign = md5("orderId=".$insert['orderId']."&openid=".$insert['openid']."&amount=".$insert['amount']."&actualAmount=".$insert['actualAmount']."&success=".$insert['success']."&extraInfo=".$insert['extraInfo']."&secret=".$_SC['secret']);
if ($makesign == $sign) {
	 	$serverinfo = json_decode($insert['extraInfo'],true);
	 	$gameid = $serverinfo['game_serverid'];
		if ($gameid != $_SC['fwqdm']) {
			if ($gameid == 'zyy02') {
				zfsj('http://sop2.jofgame.com/ucpay/ldpay.php',$_REQUEST);
				exit;
			} elseif ($gameid == 'zjsh_zyy_001') {
				zfsj('http://zj01.jofgame.com/ucpay/ldpay.php',$_REQUEST);
				exit;
			} elseif ($gameid == 'zyy03') {
				zfsj('http://zj03.jofgame.com/ucpay/ldpay.php',$_REQUEST);
				exit;
			}
		}	 	
	//if ($insert['extraInfo'] == $_SC['fwqdm']) {
	    $checkSql = "SELECT * FROM ".$common->tname('ld_payinfo')." WHERE orderId = '".$insert['orderId']."' && success = '".$insert['success']."' LIMIT 1";
	    $res_check = $db->query($checkSql);
	    $checknum = $db->num_rows($res_check);
	    if ($checknum == 0) {
	    	$common->inserttable('ld_payinfo',$insert);
	    } else {
	    	echo 'ok';
	    	exit;
	    }
	    $sql_user = "SELECT a.userid,b.playersid,b.ingot,b.player_level FROM ".$common->tname('user')." a,".$common->tname('player')." b WHERE a.username = '".$insert['openid']."' && a.userid = b.userid LIMIT 1";
	    $res_user = $db->query($sql_user);
	    $rows_user = $db->fetch_array($res_user);
	    $oldYB = 0;
	    $newYB = 0;
	    if ($insert['success'] == 0) {
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
	    writelog('app.php?task=chongzhi&type=ld&option=pay',json_encode(array('orderId'=>$insert['orderId'],'ucid'=>$insert['openid'],'payWay'=>'ldcz','amount'=>$insert['amount'],'orderStatus'=>$insert['success'],'failedDesc'=>$insert['msg'],'createTime'=>$nowTime,'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);	   	
	//}
} else {	
    $common->inserttable('ld_payinfo_error',$insert);
}
echo 'ok';


