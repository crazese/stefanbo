<?php
include 'function.php';
//$raw_post_data = file_get_contents('php://input', 'r');
//$payInfo = json_decode($raw_post_data,true);
$appKey = 'ff92aa7dd3a3a36a8cfceb270aa5bd05d55755c5c77e9701';
$appId = '108157';
if (!empty($_REQUEST)) {
	$sign = _get('Sign');
	$insert['AppId'] = _get('AppId');
	$insert['Act'] = _get('Act');
	$insert['ProductName'] = _get('ProductName');
	$insert['ConsumeStreamId'] = _get('ConsumeStreamId');
	$insert['CooOrderSerial'] = _get('CooOrderSerial');
	$insert['Uin'] = _get('Uin');
	$insert['GoodsId'] = _get('GoodsId');
	$insert['GoodsInfo'] = _get('GoodsInfo');
	$insert['GoodsCount'] = _get('GoodsCount');
	$insert['OriginalMoney'] = _get('OriginalMoney');
	$insert['OrderMoney'] = _get('OrderMoney');
	$insert['Note'] = _get('Note');
	$insert['PayStatus'] = _get('PayStatus');
	$insert['CreateTime'] = _get('CreateTime');
	$insert['Sign'] = _get('Sign');
	//$insert['processed'] = 1;
	$insert['sysTime'] = time();
	$makesign = md5($insert['AppId'].$insert['Act'].$insert['ProductName'].$insert['ConsumeStreamId'].$insert['CooOrderSerial'].$insert['Uin'].$insert['GoodsId'].$insert['GoodsInfo'].$insert['GoodsCount'].$insert['OriginalMoney'].$insert['OrderMoney'].$insert['Note'].$insert['PayStatus'].$insert['CreateTime'].$appKey);
	//echo $insert['AppId'].'@@'.$insert['Act'].'@@'.$insert['ProductName'].'@@'.$insert['ConsumeStreamId'].'@@'.$insert['CooOrderSerial'].'@@'.$insert['Uin'].'@@'.$insert['GoodsId'].'@@'.$insert['GoodsInfo'].'@@'.$insert['GoodsCount'].'@@'.$insert['OriginalMoney'].'@@'.$insert['OrderMoney'].'@@'.$insert['Note'].'@@'.$insert['PayStatus'].'@@'.$insert['CreateTime'].'@@'.$_SC['appKey'];
	$insert['Note'] = mysql_escape_string($insert['Note']);	
	$serverInfo = explode('|',$insert['Note']);
	$fwqdm = $serverInfo[1];
	if ($fwqdm != $_SC['fwqdm']) {
		if ($fwqdm == 'zyy02') {
			zfsj('http://sop1.jofgame.com/pay/91pay.php',$_REQUEST);
			exit;
		} elseif ($fwqdm == 'zjsh_zyy_001') {
			zfsj('http://zj02.jofgame.com/pay/91pay.php',$_REQUEST);
			exit;
		} /*elseif ($fwqdm == 'zjsh_zyy_002') {
			zfsj('http://117.135.139.237:8080/pay/91pay.php',$_REQUEST);
			exit;
		}*/ elseif ($fwqdm == 'zyy03') {
			zfsj('http://sop3.jofgame.com/pay/91pay.php',$_REQUEST);
			exit;
		} elseif ($fwqdm == 'zj3') {
			zfsj('http://117.135.139.237:8080/pay/91pay.php',$_REQUEST);
			exit;
		}
	}
	if ($insert['AppId'] != $appId) {
		echo json_encode(array('ErrorCode'=>'2','ErrorDesc'=>'AppId无效'));
		exit;
	} elseif ($insert['Act'] != 1) {
		echo json_encode(array('ErrorCode'=>'3','ErrorDesc'=>'Act无效'));
		exit;		
	} elseif ($insert['OrderMoney'] == 0) {
		echo json_encode(array('ErrorCode'=>'4','ErrorDesc'=>'价格参数无效'));
		exit;				
	} elseif ($insert['Sign'] != $makesign) {
		echo json_encode(array('ErrorCode'=>'5','ErrorDesc'=>'Sign无效'));
		$common->inserttable('91game_payinfo_error',$insert);
		exit;			
	}
    if ($makesign == $sign) {
    	$checkSql = "SELECT * FROM ".$common->tname('91game_payinfo')." WHERE CooOrderSerial = '".$insert['CooOrderSerial']."' && PayStatus = '".$insert['PayStatus']."' LIMIT 1";
    	$res_check = $db->query($checkSql);
    	$checknum = $db->num_rows($res_check);
    	if ($checknum == 0) {
    		$common->inserttable('91game_payinfo',$insert);
    	} else {
    		echo json_encode(array('ErrorCode'=>'1','ErrorDesc'=>'接收成功'));
    		exit;
    	}
    	if ($insert['PayStatus'] == 1) {
	    	$yb = floor($insert['GoodsCount']);
	    	$sql_user = "SELECT a.userid,b.playersid,b.ingot,b.player_level FROM ".$common->tname('user')." a,".$common->tname('player')." b WHERE a.username = '".$insert['Uin']."' && a.userid = b.userid LIMIT 1";
	    	//$sql_user = "SELECT userid,playersid,ingot,player_level FROM ". $common->tname('player')." WHERE ucid = '".$insert['ucid']."' LIMIT 1";
	    	$res_user = $db->query($sql_user);
	    	$rows_user = $db->fetch_array($res_user);
	    	if (!empty($rows_user)) {
	    		$playersid = $rows_user['playersid'];
	    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
	    		$db->query($sql_p);    			
	    		vipChongzhi($playersid, $insert['GoodsCount'], $yb, $insert['CooOrderSerial']);
	    	}
	    	writelog('app.php?task=chongzhi&option=pay&type=91pay&userId='.$rows_user['userid'],json_encode(array('orderId'=>$insert['CooOrderSerial'],'ucid'=>$insert['Uin'],'payWay'=>0,'amount'=>$insert['GoodsCount'],'orderStatus'=>'S','failedDesc'=>'','createTime'=>time(),'status'=>0)),$rows_user['player_level'],$rows_user['userid']); 
    	}
    } else {
    	$common->inserttable('91game_payinfo_error',$insert);
    }
    echo json_encode(array('ErrorCode'=>'1','ErrorDesc'=>'接收成功'));
} else {
	echo json_encode(array('ErrorCode'=>'0','ErrorDesc'=>'接收失败'));
}
