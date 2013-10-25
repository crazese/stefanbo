<?php
include 'function.php';
$userid = _get('userid');
$gameid = _get('gameid');
//$appid = 'OA00387518';
$appid = 'OA00388838';
//$host = 'https://iap.tstore.co.kr/digitalsignconfirm.iap';
$host = 'https://iapdev.tstore.co.kr/digitalsignconfirm.iap';
$auth = stripslashes(_get('auth'));
$authInfo = json_decode($auth,true);
$txid = $authInfo['txid'];
$signdata = $authInfo['signdata'];
$postData = json_encode(array('txid'=>$txid,'appid'=>$appid,'signdata'=>$signdata));
$callRes = callurl($host,$postData,"Content-Type:application/json");
$result = json_decode($callRes,true);
if ($result['status'] == 0) {
    $checkSql = "SELECT * FROM ".$common->tname('korea_payinfo')." WHERE txid = '$txid' LIMIT 1";
    $res_check = $db->query($checkSql);
    $checknum = $db->num_rows($res_check);	
    if ($checknum == 0) {
		$common->inserttable('korea_payinfo',array('txid'=>$txid,'status'=>$result['status'],'message'=>$result['message'],'userid'=>$userid,'detail'=>$result['detail'],'count'=>$result['count'],'log_time'=>$result['product'][0]['log_time'],'appid'=>$result['product'][0]['appid'],'product_id'=>$result['product'][0]['product_id'],'charge_amount'=>$result['product'][0]['charge_amount'],'tid'=>$result['product'][0]['tid'],'detail_pname'=>$result['product'][0]['detail_pname'],'bp_info'=>$result['product'][0]['bp_info']));
    } else {
    	echo json_encode(array('status'=>30,'message'=>'The order has been used!'));
    	exit;
    }
    $sql_user = "SELECT playersid,ingot,player_level,userid FROM ".$common->tname('player')." WHERE userid = $userid LIMIT 1";
    $res_user = $db->query($sql_user);
    $rows_user = $db->fetch_array($res_user);
    $oldYB = 0;
    $newYB = 0;  
    //$yb = floor($insert['amount'] * 10);  
    $p_sql = "SELECT * FROM ".$common->tname('app_productInfo')." WHERE productID = '".$result['product'][0]['product_id']."' LIMIT 1";  		
    $p_result = $db->query($p_sql);
    $p_rows = $db->fetch_array($p_result);
    if (!empty($p_rows)) {
    	$yb = $p_rows['yb'];
    } else {
    	echo json_encode(array('status'=>30,'message'=>'Product information is incorrect!'));
    	exit;
    }
    if (!empty($rows_user)) {
    	$oldYB = $rows_user['ingot'];
    	$newYB = $rows_user['ingot'] + $yb;
    	$playersid = $rows_user['playersid'];
    	$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
    	$db->query($sql_p);	    		 
    	//writelog('app.php?task=ucchongzhi&option=pay',json_encode(array('orderId'=>$insert['orderId'],'ucid'=>$insert['ucid'],'payWay'=>$insert['payWay'],'amount'=>$insert['amount'],'orderStatus'=>$insert['orderStatus'],'failedDesc'=>$insert['failedDesc'],'createTime'=>$insert['createTime'],'status'=>0)),$rows_user['player_level'],$rows_user['userid']);		
    	vipChongzhi($playersid, $result['product'][0]['charge_amount'], $yb, $txid);
    	writelog('app.php?task=chongzhi&type=korea&option=pay',json_encode(array('orderId'=>$txid,'ucid'=>$userid,'payWay'=>'korea','amount'=>$result['product'][0]['charge_amount'],'orderStatus'=>1,'failedDesc'=>'','createTime'=>time(),'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);
    }     
    echo json_encode(array('status'=>0,'yb'=>intval($newYB),'message'=>'Successful recharge'));
} else {
	$common->inserttable('korea_payinfo_err',array('txid'=>$txid,'status'=>$result['status'],'message'=>$result['message'],'userid'=>$userid,'orderTime'=>time()));
	echo json_encode(array('status'=>30,'message'=>$result['message']));
}
