<?php
//set_time_limit(600);
//include 'function.php';
//$time = $order['time'] =_get('time');
//$sign = $order['sign'] =_get('sign');
$returnValue="<response><ErrorCode>1</ErrorCode><ErrorDesc>Success</ErrorDesc></response>";   
echo $returnValue;
/*$devuid = 16075900;
if(!empty($time)&&!empty($sign)){
   $encodeString=md5($devuid.$time);   
   //通过签名
   if($sign==$encodeString){
   	  $response= file_get_contents("php://input");
   	  $orderXml = simplexml_load_string($response);
   	  $orderXml = json_decode(json_encode($orderXml));
   	  $order['order_id'] = $orderXml->order_id;
   	  $order['create_time'] = $orderXml->create_time;
   	  $order['appkey'] = $orderXml->appkey;
   	  $order['cost'] = $orderXml->cost;
   	  $check_order = "select count(intID) as sl from ".$common->tname('jf_payinfo')." where order_id = '".$order['order_id']."' limit 1";
   	  $check_res = $db->query($check_order);
   	  $check_rows = $db->fetch_array($check_res);
   	  if ($check_rows['sl'] == 0) {
   	  	 $error = 1;
   	  	 for ($i = 0; $i < 10; $i++) {
   	  	 	$sql = "select * from ".$common->tname('jforder')." where order_id = '".$order['order_id']."' limit 1";
   	  	 	$result = $db->query($sql);
   	  	 	$rows = $db->fetch_array($result);
   	  	 	if (!empty($rows)) {
   	  	 		$userid = $order['userid'] = $rows['userid'];
   	  	 		$gameid = $order['gameid'] = $rows['gameid'];
   	  	 		$sql_user = "select * from ".$common->tname('player')." where userid = $userid limit 1";
   	  	 		$res_user = $db->query($sql_user);
   	  	 		$rows_user = $db->fetch_array($res_user);
		 	    $oldYB = 0;
			    $newYB = 0;  
			    $orderStatus = "F"; 
			    $yb = floor($order['cost']);  	  	 		
   	  	 		if (!empty($rows_user)) {
		    		$oldYB = $rows_user['ingot'];
		    		$newYB = $rows_user['ingot'] + $yb;
		    		$playersid = $rows_user['playersid'];
		    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
		    		$db->query($sql_p);	
		    		$db->query("delete from ".$common->tname('jforder')." where order_id = '".$order['order_id']."'");  		 
		    		//writelog('app.php?task=ucchongzhi&option=pay',json_encode(array('orderId'=>$insert['orderId'],'ucid'=>$insert['ucid'],'payWay'=>$insert['payWay'],'amount'=>$insert['amount'],'orderStatus'=>$insert['orderStatus'],'failedDesc'=>$insert['failedDesc'],'createTime'=>$insert['createTime'],'status'=>0)),$rows_user['player_level'],$rows_user['userid']);		
		    		vipChongzhi($playersid, floor($order['cost'] / 10), $yb, $order['order_id']);
		    		$orderStatus = 'S';
		    		writelog('app.php?task=chongzhi&type=jfpay&option=pay',json_encode(array('orderId'=>$order['order_id'],'ucid'=>$userid,'payWay'=>'jfpay','amount'=>floor($order['cost'] / 10),'orderStatus'=>$orderStatus,'failedDesc'=>'','createTime'=>time(),'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);	    	   	  	 			
   	  	 			$order['orderStatus'] = 1;		    		
   	  	 		}
   	  	 		$error = 0;
   	  	 		break;
   	  	 	} else {
   	  	 		sleep(1);
   	  	 	}
   	  	 }
   	  	 $common->inserttable('jf_payinfo',$order);   	  	 
   	  }
   }
}
echo $returnValue;*/