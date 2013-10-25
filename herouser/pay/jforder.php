<?php
set_time_limit(600);
include 'function.php';
include 'class_tea.php';
$insert['userid'] = _get('userid');
$insert['gameid'] = _get('gameid');
$order_id = $insert['order_id'] = _get('order_id');
/*$sql = "select * from ".$common->tname('jf_payinfo')." where order_id = '".$insert['order_id']."' && orderStatus = 0 limit 1";
$result = $db->query($sql);
$rows = $db->fetch_array($result);*/
$channelid = '1399658373';
$secret = 'ddd4b058fd9126a4';
$url = 'http://api.gfan.com/sdk/pay/queryMultipleAppPayLog';
$Agent = "User-Agent:channelID=$channelid";
$str = "<request><order><order_id>".$insert['order_id']."</order_id><app_key>$channelid</app_key></order></request>";
$t = new tea();
$senddata = base64_encode($t->encrypt(trim($str), $secret));
$checkres = callurl($url,$senddata,$Agent);
$order_xml = simplexml_load_string($checkres);
$order_xml = json_decode(json_encode($order_xml));
if (!isset($order_xml->order->result)) {
	$checkOrder = "select count(intID) as sl from ".$common->tname('jf1_payinfo')." where order_id = '$order_id'";
	$result = $db->query($checkOrder);
	$rows = $db->fetch_array($result);
	$orderNum = $rows['sl'];
	if ($orderNum == 0) {
		$cost = $insert['cost'] = $order_xml->order->cost;
		$insert['appkey'] = $order_xml->order->appkey;
		$insert['create_time'] = $order_xml->order->create_time;
	    $sql_user = "select * from ".$common->tname('player')." where userid = ".$insert['userid']." limit 1";
	    $res_user = $db->query($sql_user);
	    $rows_user = $db->fetch_array($res_user);
	    $oldYB = 0;
	    $newYB = 0;  
	    $orderStatus = "F"; 
	    $yb = floor($cost);  	  	 		
	    if (!empty($rows_user)) {
		    $oldYB = $rows_user['ingot'];
		    $newYB = $rows_user['ingot'] + $yb;
		    $playersid = $rows_user['playersid'];
		    $sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
		    $db->query($sql_p);	
		    vipChongzhi($playersid, $cost / 10, $yb, $order_id);
		    $orderStatus = 'S';
		    writelog('app.php?task=chongzhi&type=jfpay&option=pay',json_encode(array('orderId'=>$order_id,'ucid'=>$insert['userid'],'payWay'=>'jfpay','amount'=>$cost / 10,'orderStatus'=>$orderStatus,'failedDesc'=>'','createTime'=>time(),'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);	    	   	  	 			
		    $order['orderStatus'] = 1;
		    $common->inserttable('jf1_payinfo',$insert);
	    }
	}
}



