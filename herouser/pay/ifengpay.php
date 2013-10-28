<?php
set_time_limit(600);
include 'function.php';
$partner_id = 1029;
$game_id = 100437;
$server_id = 1;
$partner_key = 'my4rl2hd81zhfutcw7n';
$bill_no = _get('bill_no');
$price = _get('price');
$user_id = _get('user_id');
$trade_status = _get('trade_status');
$partner_bill_no = _get('partner_bill_no');
$extra = _get('extra');
$sign = _get('sign');
$fwqInfo = explode('|',urldecode($extra));
$gameid = $fwqInfo[0];
$userid = $fwqInfo[1];
if ($gameid != $_SC['fwqdm']) {
	if ($gameid == 'zyy02') {
		zfsj('http://sop1.jofgame.com/pay/ifengpay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zjsh_zyy_001') {
		zfsj('http://zj01.jofgame.com/pay/ifengpay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zyy03') {
		zfsj('http://zj03.jofgame.com/pay/ifengpay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zj3') {
		zfsj('http://117.135.139.237:8080/pay/ifengpay.php',$_REQUEST);
		exit;
	}
}
//Upper(MD5(partner_id+game_id+server_id+user_id+bill_no+price+trade_status+partner_key))
$mksign = strtoupper(md5($partner_id.$game_id.$server_id.$user_id.$bill_no.$price.$trade_status.$partner_key));
if ($sign == $mksign) {
	if ($trade_status == 'TRADE_SUCCESS') {
		$checkSql = "select count(*) as sl from ".$common->tname('ifengpayinfo')." where bill_no = '$bill_no'";
		$res_check = $db->query($checkSql);
		$rows_check = $db->fetch_array($res_check);
		if ($rows_check['sl'] == 0) {
			$common->inserttable('ifengpayinfo',array('game_id'=>$game_id,'partner_id'=>$partner_id,'server_id'=>$server_id,'bill_no'=>$bill_no,'price'=>$price,'user_id'=>$user_id,'trade_status'=>$trade_status,'partner_bill_no'=>$partner_bill_no,'extra'=>$extra,'sign'=>$sign,'payTime'=>time()));
		    $sql_user = "SELECT * FROM ".$common->tname('player')." WHERE userid = $userid LIMIT 1";
		    $res_user = $db->query($sql_user);
		    $rows_user = $db->fetch_array($res_user);
		    $oldYB = 0;
		    $newYB = 0;
		    $sfcg = 'F';
		    $yb = floor($price * 10);
	        if (!empty($rows_user)) {
	        	$sfcg = 'S'; 
	    		$oldYB = $rows_user['ingot'];
	    		$newYB = $rows_user['ingot'] + $yb;
	    		$playersid = $rows_user['playersid'];
	    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
	    		$db->query($sql_p);	    		 
	    		vipChongzhi($playersid, $price, $yb, $bill_no);    		
	    	}
	    	writelog('app.php?task=chongzhi&type=ifeng&option=pay',json_encode(array('orderId'=>$bill_no,'ucid'=>$rows_user['userid'],'payWay'=>'ifeng','amount'=>$price,'orderStatus'=>$sfcg,'failedDesc'=>'','createTime'=>time(),'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);	    
		}		
	} else {
		$common->inserttable('ifengpayinfo_error',array('game_id'=>$game_id,'partner_id'=>$partner_id,'server_id'=>$server_id,'bill_no'=>$bill_no,'price'=>$price,'user_id'=>$user_id,'trade_status'=>$trade_status,'partner_bill_no'=>$partner_bill_no,'extra'=>$extra,'sign'=>$sign,'payTime'=>time()));
	}	
} else {
	$common->inserttable('ifengpayinfo_error',array('game_id'=>$game_id,'partner_id'=>$partner_id,'server_id'=>$server_id,'bill_no'=>$bill_no,'price'=>$price,'user_id'=>$user_id,'trade_status'=>$trade_status,'partner_bill_no'=>$partner_bill_no,'extra'=>$extra.'|签名错','sign'=>$sign,'payTime'=>time()));
}
echo 'SUCCESS';