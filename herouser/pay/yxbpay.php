<?php
set_time_limit(600);
include 'function.php';
$serect = 'tMk0GvhrtguZuhPJXcoLTz0sJexJ2siX';
$callUrl = 'http://gamercg.hicloud.com:8090/gameserver/confirmOrder';
$get_data = file_get_contents('php://input', 'r');
$xml = simplexml_load_string($get_data);
$xml = json_encode($xml);
$xml = json_decode($xml);	
$usercode = $xml->usercode;
$tradnum = $xml->tradnum;
$account = $xml->account;
$servername = $xml->servername;
$amt = $xml->amt;
$checking = $xml->checking;
$sign = md5($usercode.$tradnum.$account.$servername.$serect);
$insertUsername = 'yxb_'.$usercode;
if ($sign == $checking) {
	$sql = "SELECT count(intID) as sl FROM ".$common->tname('yxbar_payinfo')." WHERE tradnum = '".$tradnum." LIMIT 1";
	$checkResult = $db->query($sql);
	$rows = $db->fetch_array($checkResult);
	if ($rows['sl'] == 0) {
		$postData = "<order><tradnum>$tradnum</tradnum><status>1</status><checking>$checking</checking></order>";
		for ($i = 0; $i < 100; $i++) {		
			$callResult = callUrl($callUrl,$postData);
			if (!empty($callResult)) {
				break;
			}	
			$callResult = null;	
			sleep(1);
		}
		$orderXml = simplexml_load_string($callResult);
		$orderXml = json_encode($orderXml);
		$orderXml = json_decode($orderXml); 
		$tradnumOrder = $orderXml->orderXml;
		$result = $orderXml->result;
		$checkingOrder = $orderXml->checking;		
		if ($tradnumOrder == $tradnum && md5($tradnumOrder.$result,$serect) == $checkingOrder) {
			$sql_user = "SELECT a.userid,b.playersid,b.ingot,b.player_level FROM ".$common->tname('user')." a,".$common->tname('player')." b WHERE a.username = '".$insertUsername."' && a.userid = b.userid LIMIT 1";
	    	$res_user = $db->query($sql_user);
	    	$rows_user = $db->fetch_array($res_user);
	    	$oldYB = 0;
	    	$newYB = 0;
	    	$common->inserttable('yxbar_payinfo',array('usercode'=>$insertUsername,'tradnum'=>$tradnum,'account'=>$account,'servername'=>$servername,'amt'=>$amt,'checking'=>$checking,'time'=>time()));
	    	if (!empty($rows_user))	{
	    		$yb = floor($amt * 10);
	    		$oldYB = $rows_user['ingot'];
	    		$newYB = $rows_user['ingot'] + $yb;
	    		$playersid = $rows_user['playersid'];
	    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
	    		$db->query($sql_p);	    		 
	    		//writelog('app.php?task=ucchongzhi&option=pay',json_encode(array('orderId'=>$insert['orderId'],'ucid'=>$insert['ucid'],'payWay'=>$insert['payWay'],'amount'=>$insert['amount'],'orderStatus'=>$insert['orderStatus'],'failedDesc'=>$insert['failedDesc'],'createTime'=>$insert['createTime'],'status'=>0)),$rows_user['player_level'],$rows_user['userid']);		
	    		vipChongzhi($playersid, $amt, $yb, $tradnum);	    		
	    	}
	    	writelog('app.php?task=chongzhi&type=uc&option=pay',json_encode(array('orderId'=>$tradnum,'ucid'=>$insertUsername,'payWay'=>1,'amount'=>$amt,'orderStatus'=>'S','failedDesc'=>'','createTime'=>time(),'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);
		} else {
			$common->inserttable('yxbar_payinfo_error',array('usercode'=>$insertUsername,'tradnum'=>$tradnum,'account'=>$account,'servername'=>$servername,'amt'=>$amt,'checking'=>$checking,'time'=>time()));
		}
	}	
} else {
	$common->inserttable('yxbar_payinfo_error',array('usercode'=>$insertUsername,'tradnum'=>$tradnum,'account'=>$account,'servername'=>$servername,'amt'=>$amt,'checking'=>$checking,'time'=>time()));
}