<?php
$partner		= "2088701888903581";	//合作伙伴ID
$seller_email	= "zhanghaitao@jofgame.com";	//签约支付宝账号或卖家支付宝帐户

$key_path = '/var/key/';  // 修改1 放入.pem文件

$notify_url		= "notify_url.php";	 
$call_back_url	= "callback_url.php";	

//↓↓↓↓↓↓↓↓↓↓杜欢这里需要修改↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
//$req_domain = 'http://221.181.193.84:8099/proxy/reqProxy_alipay-1.php'; // 正式环境     修改2
//$req_domain_2f = 'http://221.181.193.84:8099/proxy/reqProxy_alipay-2.php'; // 正式环境 修改2
$req_domain = 'http://'.ALIPAY_SELF_IP.'/components/alipay_self/'; // 测试环境
$req_domain_2f = 'http://'.ALIPAY_SELF_IP.'/components/alipay_self/'; // 测试环境

// 修改3 需在放置reqProxy.php的路径放置alipay下的Images和css目录所有文件
// 修改4 notify_url.php里的闪游的写日志路径分为54 55

//$merchant_url	= "http://fg.imtt.qq.com/p?i=786";	// 正式环境 游戏地带Q将入口 	 修改5
$merchant_url	= "http://117.135.138.248:8080";	// 测试环境 游戏地带Q将入口 修改5
//------------------------------------------------------------------

$subject		= "元宝";	//产品名称
$out_trade_no	= "";	//订单号
$total_fee		= "";	//订单总金额，显示在支付宝收银台里的“应付总额”里
$out_user		= "";	//外部商号 买家在商户系统的唯一标识，当该 out_use支付成功一次后再来支付时，30 元内无需密码

//↓↓↓↓↓↓↓↓↓↓以下是固定参数↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓

//以下内容不需要修改,固定参数
$Service1		= "alipay.wap.trade.create.direct";			//接口1
$Service2		= "alipay.wap.auth.authAndExecute";			//接口2
$format			= "xml";									//http传输格式
$sec_id			= "0001";									//签名方式 不需修改
$_input_charset	= "utf-8";									//字符编码格式
$v				= "2.0";									//版本号

//↓↓↓↓↓↓↓↓↓↓以下是支付前置需要用到的参数↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
$Service_Paychannel = "mobile.merchant.paychannel";			//支付前置接口
$_input_charset_GBK = "GBK";								//支付前置用此编码

?>