<?php
set_time_limit(600);
include 'function.php';
$keys = 'f3659a91e9b15ce2002e0942';
$serverkey = '0a669bf1f6422eef0ed20832232f66';
$softcode = '200112';
$result = _get('result');
$paymentid = _get('paymentid');
$errorstr = _get('errorstr');
$company = _get('company');
$channelid = _get('channelid');
$softgood = _get('softgood');
$customer = _get('customer');
$money = _get('money');
$softserver = _get('softserver');
$playername = _get('playername');
$date = _get('date');
$pkey = _get('pkey');
$paytype = _get('paytype');
if ($softserver != $_SC['fwqdm']) {
	if ($softserver == 'zyy02') {
		zfsj('http://sop1.jofgame.com/pay/wopay.php',$_REQUEST);
		exit;
	} elseif ($softserver == 'zjsh_zyy_001') {
		zfsj('http://zj01.jofgame.com/pay/wopay.php',$_REQUEST);
		exit;
	} elseif ($softserver == 'zyy03') {
		zfsj('http://zj03.jofgame.com/pay/wopay.php',$_REQUEST);
		exit;
	}
}
//softcode + keys + money + result + paymentid + customer +serverkey的md5
$mksign = md5($softcode.$keys.$money.$result.$paymentid.$customer.$serverkey);
if ($mksign == $pkey) {
	if ($result == 0) {
		$checkSql = "select count(*) as sl from ".$common->tname('ltsjpayinfo')." where paymentid = '$paymentid'";
		$res_check = $db->query($checkSql);
		$rows_check = $db->fetch_array($res_check);
		if ($rows_check['sl'] == 0) {
			$common->inserttable('ltsjpayinfo',array('result'=>$result,'paymentid'=>$paymentid,'errorstr'=>$errorstr,'company'=>$company,'channelid'=>$channelid,'softgood'=>$softgood,'customer'=>$customer,'money'=>$money,'softserver'=>$softserver,'playername'=>$playername,'date'=>$date,'pkey'=>$pkey,'paytype'=>$paytype));
		    $sql_user = "SELECT * FROM ".$common->tname('player')." WHERE userid = $customer LIMIT 1";
		    $res_user = $db->query($sql_user);
		    $rows_user = $db->fetch_array($res_user);
		    $oldYB = 0;
		    $newYB = 0;
		    $sfcg = 'F';
		    $yb = floor($money / 100 * 5);
	        if (!empty($rows_user)) {
	        	$sfcg = 'S'; 
	    		$oldYB = $rows_user['ingot'];
	    		$newYB = $rows_user['ingot'] + $yb;
	    		$playersid = $rows_user['playersid'];
	    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
	    		$db->query($sql_p);	    		 
	    		vipChongzhi($playersid, $money / 100, $yb, $paymentid);    		
	    	}
	    	writelog('app.php?task=chongzhi&type=ltsj&option=pay',json_encode(array('orderId'=>$paymentid,'ucid'=>$rows_user['userid'],'payWay'=>$paytype,'amount'=>$money / 100,'orderStatus'=>$sfcg,'failedDesc'=>'','createTime'=>time(),'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);	    
		}		
	} else {
		$common->inserttable('ltsjpayinfo_error',array('result'=>$result,'errorstr'=>$errorstr,'paymentid'=>$paymentid));
	}
} else {
	$common->inserttable('ltsjpayinfo_error',array('result'=>$result,'errorstr'=>'签名验证失败','paymentid'=>$paymentid));
}