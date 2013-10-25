<?php
include 'function.php';
$appKey = '20t64v0Yw17p3Xt8z6t4xNIR';
$appSecret = 'T2zCCbKJpPB8732z1py3px33';
$GET['appKey'] = _get('appKey');
$GET['amount'] = _get('amount');
$GET['orderId'] = _get('orderId');
$GET['payResult'] = _get('payResult');
$GET['ext'] = _get('ext');
$GET['payType'] = _get('payType');
$GET['signStr'] = _get('signStr');
$GET['msg'] = _get('msg');
$GET['payTime'] = time();
//appKey+amount+orderId+payResult+ext+msg+appSecret
$signStr = strtoupper(md5($GET['appKey'].$GET['amount'].$GET['orderId'].$GET['payResult'].$GET['ext'].$GET['msg'].$appSecret));
if ($signStr == $GET['signStr']) {
	$ext = base64_decode($GET['ext']);
	$fwqInfo = explode('|',$ext);
	$gameid = $fwqInfo[0];
	$userid = $fwqInfo[1];
	if ($gameid != $_SC['fwqdm']) {
		if ($gameid == 'zyy02') {
			zfsj('http://sop1.jofgame.com/pay/azpay.php',$_REQUEST);
			exit;
		} elseif ($gameid == 'zjsh_zyy_001') {
			zfsj('http://zj01.jofgame.com/pay/azpay.php',$_REQUEST);
			exit;
		} elseif ($gameid == 'zyy03') {
			zfsj('http://sop3.jofgame.com/pay/azpay.php',$_REQUEST);
			exit;
		} elseif ($gameid == 'zj3') {
			zfsj('http://117.135.139.237:8080/pay/azpay.php',$_REQUEST);
			exit;
		}
	}	
    $checkSql = "SELECT * FROM ".$common->tname('az_payinfo')." WHERE orderId = '".$GET['orderId']."' && payResult = '".$GET['payResult']."' LIMIT 1";    
    $res_check = $db->query($checkSql);
    $checknum = $db->num_rows($res_check);
    if ($checknum == 0) {
    	$common->inserttable('az_payinfo',$GET);
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
    if ($GET['payResult'] == 200) { 
    	$yb = floor($GET['amount'] * 10);
    	//$yb = floor($money * 10);
        if (!empty($rows_user)) {
        	$sfcg = 'S'; 
    		$oldYB = $rows_user['ingot'];
    		$newYB = $rows_user['ingot'] + $yb;
    		$playersid = $rows_user['playersid'];
    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
    		$db->query($sql_p);	    		 
    		//writelog('app.php?task=ucchongzhi&option=pay',json_encode(array('orderId'=>$insert['orderId'],'ucid'=>$insert['ucid'],'payWay'=>$insert['payWay'],'amount'=>$insert['amount'],'orderStatus'=>$insert['orderStatus'],'failedDesc'=>$insert['failedDesc'],'createTime'=>$insert['createTime'],'status'=>0)),$rows_user['player_level'],$rows_user['userid']);		
    		vipChongzhi($playersid, $GET['amount'], $yb, $GET['orderId'], null, true, true, 'az');    		
    	}
    } 
    writelog('app.php?task=chongzhi&type=az&option=pay',json_encode(array('orderId'=>$GET['orderId'],'ucid'=>$rows_user['userid'],'payWay'=>'az_'.$GET['payType'],'amount'=>$GET['amount'],'orderStatus'=>$sfcg,'failedDesc'=>'','createTime'=>time(),'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);	    
} else { 
	$common->inserttable('az_payinfo_error',$GET);	
}
echo 'success';
