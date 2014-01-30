<?php
set_time_limit(600);
include 'function.php';
include(dirname(dirname(__FILE__)) .'/includes/sinav.php');
$order_id = _get('order_id');
$appkey = _get('appkey');
$order_uid = _get('order_uid');
$amount = _get('amount');
//$_WB['zfid']
$svorderid = str_replace($_WB['zfid'],'',$order_id);
$svsql = "select * from ".$common->tname('wborderid')." where intID = $svorderid limit 1";
$sv_res = $db->query($svsql);
$sv_rows = $db->fetch_array($sv_res);
$gameid = $sv_rows['serverid'];
if ($gameid != $_SC['fwqdm']) {
	if ($gameid == 'sina002') {
		zfsj('http://h5s2.jofgame.com/ucpay/wbpay.php',$_REQUEST);
		exit;
	}
}
//$common->insertLog(json_encode($_REQUEST));
//$db->query("insert into ".$common->tname('tmp_nr')." set nr = '".json_encode($_REQUEST)."'");
$weiyouxi = new WeiyouxiClient( $_WB['source'] , $_WB['secret'] );
$params = array
          (
		      'order_id' => $order_id ,//支付ID(汇总信息页面,7位) . 9位数字（不能重复,以免订单号重复)，总长度必须为16位)
		      'user_id' => $order_uid ,    //支付者的SinaUID
		      'app_id' => $appkey ,     //开放平台的应用唯一标识
		      'sign' => md5( $order_id.'|'.$_WB['secret'] ) ,         //md5( $order_id.'|'.$app_secret );
          );
$orderStatus = $weiyouxi->get('pay/order_status', $params);
if ($orderStatus['order_status'] == 1) {
    $checkSql = "SELECT * FROM ".$common->tname('weibo_payinfo')." WHERE order_id = '".$order_id."' && order_uid = '".$order_uid."' LIMIT 1";
    $res_check = $db->query($checkSql);
    $rows = $db->fetch_array($res_check);
    if (!empty($rows)) {
    	if ($rows['pay_status'] == 0) {    		
    		$db->query("UPDATE ".$common->tname('weibo_payinfo')." SET pay_status = 1 WHERE order_id = '$order_id' LIMIT 1");
    		$playersid = $rows['playersid'];
		    $sql_user = "SELECT userid,playersid,ingot,player_level FROM ".$common->tname('player')."  WHERE playersid = $playersid LIMIT 1";
		    $res_user = $db->query($sql_user);
		    $rows_user = $db->fetch_array($res_user);
		    $oldYB = 0;
		    $newYB = 0;
		    $amount = $rows['amount'];
		    if ($amount > 0) {
		    	$yb = floor($amount * 10);    		
		    	if (!empty($rows_user)) {
		    		$oldYB = $rows_user['ingot'];
		    		$newYB = $rows_user['ingot'] + $yb;
		    		$playersid = $rows_user['playersid'];
		    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
		    		$db->query($sql_p);	    		 
		    		//writelog('app.php?task=ucchongzhi&option=pay',json_encode(array('orderId'=>$insert['orderId'],'ucid'=>$insert['ucid'],'payWay'=>$insert['payWay'],'amount'=>$insert['amount'],'orderStatus'=>$insert['orderStatus'],'failedDesc'=>$insert['failedDesc'],'createTime'=>$insert['createTime'],'status'=>0)),$rows_user['player_level'],$rows_user['userid']);		
		    		vipChongzhi($playersid, $amount, $yb, $order_id);
	    		}
	    	}
	    	writelog('app.php?task=chongzhi&type=wb&option=pay',json_encode(array('orderId'=>$order_id,'ucid'=>$rows_user['userid'],'payWay'=>'wbcz','amount'=>$amount,'orderStatus'=>'S','failedDesc'=>'','createTime'=>time(),'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);	   	
    	}
    	echo 'OK';
    } else {
    	echo 'OK';
    }
} else {
	$db->query("UPDATE ".$common->tname('weibo_payinfo')." SET errorCode = '".$orderStatus['error_code']."' WHERE order_id = '$order_id' LIMIT 1");
	echo 'OK';
}