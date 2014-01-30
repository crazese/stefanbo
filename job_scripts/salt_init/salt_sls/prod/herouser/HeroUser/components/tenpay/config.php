<?php
include(dirname(dirname(dirname(__FILE__))) .'/lang/components/tenpay/lang.php');
$partner = "1214604901";
$key = "4787e80941c11b104700a94055997d85";

$notify_url		= "payNotifyUrl.php";	 
$call_back_url	= "payReturnUrl.php";	
$goods_descr = '元宝充值';

$api_init = "http://wap.tenpay.com/cgi-bin/wappayv2.0/wappay_init.cgi";
$api_gate = "http://wap.tenpay.com/cgi-bin/wappayv2.0/wappay_gate.cgi?token_id=";

$req_domain = 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy_tenpay-1.php';
$notify_domain= 'http://'.SY_PROXY_INTERNET_IP.'/proxy/notifyProxy_tenpay-1.php';
$call_back_url = 'http://'.SY_PROXY_INTERNET_IP.'/proxy/returnProxy_tenpay-1.php';


$req_domain_2f = 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy_tenpay-2.php';
$notify_domain_2f = 'http://'.SY_PROXY_INTERNET_IP.'/proxy/notifyProxy_tenpay-2.php';
$call_back_url_2f = 'http://'.SY_PROXY_INTERNET_IP.'/proxy/returnProxy_tenpay-2.php';

$req_domain_3f = 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy_tenpay-3.php';
$notify_domain_3f = 'http://'.SY_PROXY_INTERNET_IP.'/proxy/notifyProxy_tenpay-3.php';
$call_back_url_3f = 'http://'.SY_PROXY_INTERNET_IP.'/proxy/returnProxy_tenpay-3.php';

$req_domain_4f = 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy_tenpay-4.php';
$notify_domain_4f = 'http://'.SY_PROXY_INTERNET_IP.'/proxy/notifyProxy_tenpay-4.php';
$call_back_url_4f = 'http://'.SY_PROXY_INTERNET_IP.'/proxy/returnProxy_tenpay-4.php';

$req_domain_5f = 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy_tenpay-5.php';
$notify_domain_5f = 'http://'.SY_PROXY_INTERNET_IP.'/proxy/notifyProxy_tenpay-5.php';
$call_back_url_5f = 'http://'.SY_PROXY_INTERNET_IP.'/proxy/returnProxy_tenpay-5.php';

$req_domain_6f = 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy_tenpay-6.php';
$notify_domain_6f = 'http://'.SY_PROXY_INTERNET_IP.'/proxy/notifyProxy_tenpay-6.php';
$call_back_url_6f = 'http://'.SY_PROXY_INTERNET_IP.'/proxy/returnProxy_tenpay-6.php';

// 修改3 需在放置reqProxy.php的路径放置alipay下的Images和css目录所有文件
// 修改4 notify_url.php里的闪游的写日志路径分为54 55

$merchant_url	= "http://fg.imtt.qq.com/p?i=786";	// 正式环境 游戏地带Q将入口 	 修改5
?>