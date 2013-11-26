<?php
require dirname(__FILE__).'/ucpro.php';

//告诉客户端浏览器不使用缓存，兼容HTTP 1.0 协议     
header("Pragma: no-cache");  
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
require dirname(dirname(dirname(dirname(__FILE__)))) .'/ucapi/libs/UzoneRestApi.php';
include(dirname(__FILE__)."/db.php");

//设置时区
date_default_timezone_set('Asia/Shanghai');
require 'UcPayAccessAPI.php';
$uzone_token  = isset($_REQUEST['uzone_token']) ? str_replace(' ','+',urldecode($_REQUEST['uzone_token'])) : '';
$amount = isset($_REQUEST['amount']) ? $_REQUEST['amount'] : '';

$UzoneRestApi = new UzoneRestApi($uzone_token);
$arr['uid'] = $UzoneRestApi->getAuthUid();
$notify_url = $_SC['backUrl']."/components/uc/php/notify.php";//异步
$return_url = $_SC['backUrl'] . "/index.php?uzone_token=" . base64_encode($uzone_token);//同步
$chongzhi_url = urlencode($_SC['backUrl'] . '/index.php?uzone_token=' . base64_encode($uzone_token));

$sql = "select playersid from ol_player where ucid='2_{$arr['uid']}'";
$sqlRet = mysql_query($sql);
$rowArr = mysql_fetch_array($sqlRet, MYSQL_ASSOC);
if(mysql_num_rows($sqlRet) == 1) {
	$balance = array(
			"store_nbr"=>STORE_NBR,
			"ucid"=>$arr['uid'],
			"pfid"=>'',
			"type"=>0
	);
	
	$key = PAYKEY;
	
	$url = "https://pay.uc.cn/trade/balance.htm";
	$balanceArr = HanderReq::sendReqAndGetRes1($url,$key,$balance );
	if(intval($balanceArr['balance']) == 0) {
		$recharge_url = "http://pay.uc.cn/recharge/recharge.htm?wp=" . STORE_NBR . "&from=" . STORE_NBR . "&link=%E5%85%85%E5%80%BC&burl=". $chongzhi_url ."&uid=".$arr['uid'];
		header("Location: ". $recharge_url);exit;
	}

	$param_cardPay = array(
	"add_info"=>"",
	"amount"=>$amount,
	"notify_url"=>$notify_url,
	"order_id"=>'herool_'.uniqid().rand(1, 100000),
	"prod_name"=>PROD_NAME,
	"prod_nbr"=>PROD_NBR,
	"return_url"=>$return_url,
	"store_nbr"=>STORE_NBR,
	"ucid"=>$arr['uid'], // 正式
	//"ucid"=>111062, // 测试用
	"wp_id"=>""//请留空，此处商户不需要填写
	);
		
	$url = "https://pay.uc.cn/trade/create.htm";
	$resArr = HanderReq::sendReqAndGetRes($url,$key,$param_cardPay );

	if($resArr['result'] == '00') {
		$sql = "insert into ol_uc_order_notify (order_id,ucid,create_time,amount)
		values ('".$param_cardPay['order_id']."','".$param_cardPay['ucid']."','".time()."','".$param_cardPay['amount']."')";
		mysql_query($sql);
		
		header("Location: https://pay.uc.cn/payment/index.htm?token=".$resArr['token']); 
	}else{
		$errArr = array(
				'00'=>'交易创建成功',
				'01'=>'请求数据格式不正确',
				'02'=>'商户不存在',
				'03'=>'商户被冻结',
				'04'=>'签名不正确',
				'05'=>'产品不存在',
				'06'=>'订单号重复',
				'07'=>'用户已冻结',
				'98'=>'非法请求如不在IP 许可范围，接口未授权',
				'99'=>'系统内部错误'
				);
		
		$errMsg = urlencode("支付失败，{$errArr[$resArr['result']]}");		
		header("Location:shibai.php?err=".$errMsg.'&uzone_token='.base64_encode($uzone_token));
	}
} else {
	echo "游戏中无角色信息<p>";
}
?>