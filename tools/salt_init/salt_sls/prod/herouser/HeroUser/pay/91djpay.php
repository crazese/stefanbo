<?php
set_time_limit(600);
include 'function.php';
$app_key = 'cfafd694f77bcd1b79949fd95fa47fbb';
$order['App_Id'] = $App_Id = _get('App_Id');
$order['Uin'] = $Uin = _get('Uin');
$order['Urecharge_Id'] = $Urecharge_Id = _get('Urecharge_Id');
$order['Extra'] = $Extra = _get('Extra');
$order['Recharge_Money'] = $Recharge_Money = _get('Recharge_Money');
$order['Recharge_Gold_Count'] = $Recharge_Gold_Count = _get('Recharge_Gold_Count');
$order['Pay_Status'] = $Pay_Status = _get('Pay_Status');
$order['Create_Time'] = $Create_Time = _get('Create_Time');
$order['Sign'] = $Sign = _get('Sign');
$fwqInfo = explode('|',$Extra);
$gameid = $fwqInfo[0];
$userid = $fwqInfo[1];
if ($gameid != $_SC['fwqdm']) {
	if ($gameid == 'zyy02') {
		zfsj('http://sop1.jofgame.com/pay/91djpay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zjsh_zyy_001') {
		zfsj('http://zj01.jofgame.com/pay/91djpay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zyy03') {
		zfsj('http://sop3.jofgame.com/pay/91djpay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zj3') {
		zfsj('http://117.135.139.237:8080/pay/91djpay.php',$_REQUEST);
		exit;
	}
}
//(App_Id=9&Create_Time=1351675829&Extra=a123456&Pay_Status=1&Recharge_Gold_Count=1&Recharge_Money=100&Uin=10671&Urecharge_Id=1234567+app_key
$mksign = md5("App_Id=$App_Id&Create_Time=$Create_Time&Extra=$Extra&Pay_Status=$Pay_Status&Recharge_Gold_Count=$Recharge_Gold_Count&Recharge_Money=$Recharge_Money&Uin=$Uin&Urecharge_Id=$Urecharge_Id$app_key");
$backsign = md5("Error_Code=0&Version=1.00$app_key");
$backdata = array('Error_Code'=>'0','Version'=>'1.00','Sign'=>$backsign);
if ($mksign == $Sign) {
	//md5(Error_Code=0&Version=1.00+app_key)
    $checkSql = "SELECT * FROM ".$common->tname('91dj_payinfo')." WHERE Urecharge_Id = '".$Urecharge_Id."' && Pay_Status = '".$Pay_Status."' LIMIT 1";    
    $res_check = $db->query($checkSql);
    $checknum = $db->num_rows($res_check);
    if ($checknum == 0) {
    	$common->inserttable('91dj_payinfo',$order);
    } else {    	
    	echo json_encode($backdata);
    	exit;
    }
    $sql_user = "SELECT * FROM ".$common->tname('player')." WHERE userid = $userid LIMIT 1";
    $res_user = $db->query($sql_user);
    $rows_user = $db->fetch_array($res_user);
    $oldYB = 0;
    $newYB = 0;
    $sfcg = 'F';  
    if ($Pay_Status == 1) { 
    	$yb =  floor($Recharge_Money * 10);
    	//$yb = floor($money * 10);
        if (!empty($rows_user)) {
        	$sfcg = 'S'; 
    		$oldYB = $rows_user['ingot'];
    		$newYB = $rows_user['ingot'] + $yb;
    		$playersid = $rows_user['playersid'];
    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
    		$db->query($sql_p);	    		 
    		//writelog('app.php?task=ucchongzhi&option=pay',json_encode(array('orderId'=>$insert['orderId'],'ucid'=>$insert['ucid'],'payWay'=>$insert['payWay'],'amount'=>$insert['amount'],'orderStatus'=>$insert['orderStatus'],'failedDesc'=>$insert['failedDesc'],'createTime'=>$insert['createTime'],'status'=>0)),$rows_user['player_level'],$rows_user['userid']);		
    		vipChongzhi($playersid, $Recharge_Money, $yb, $Urecharge_Id);    		
    	}
    }    
    writelog('app.php?task=chongzhi&type=91dj&option=pay',json_encode(array('orderId'=>$Urecharge_Id,'ucid'=>$rows_user['userid'],'payWay'=>'91dj','amount'=>$Recharge_Money,'orderStatus'=>$sfcg,'failedDesc'=>'','createTime'=>time(),'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);	    	
}
echo json_encode($backdata);