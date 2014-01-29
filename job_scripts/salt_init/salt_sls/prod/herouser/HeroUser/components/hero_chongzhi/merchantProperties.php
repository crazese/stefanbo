<?php
	/* 商户编号p1_MerId,以及密钥merchantKey 需要从易宝支付平台获得*/
	#$p1_MerId	= "10001126856";																#测试使用
	#$merchantKey	= "69cl522AV6q613Ii4W6u8K6XuW8vM1N6bFgyv769220IuYe9u37N4y7rI4Pl";			#测试使用
	$p1_MerId			= "10011285454";												
	$merchantKey	= "8V9E3Rx285UoHV5MnK6G224J8tWi733756KWLj86jVN42358Z4P6M8E2t01n";	

	// 按天记录日志
	date_default_timezone_set('PRC');
	$extName = date('Ymd');
//	if(SYPT == 1) {
		$logName = "/data/pay_log/YeePay_nobank.{$extName}";
//	} else {
//		$logName = dirname(__FILE__) . "/log/YeePay_nobank.{$extName}";
//	}
	
	# 非银行卡支付专业版请求地址,无需更改.
//	if(SYPT == 1) {
		$reqURL_SNDApro = "http://10.144.133.56:8099/proxy/payProxy.php";
//	} else {
//		$reqURL_SNDApro = "https://www.yeepay.com/app-merchant-proxy/command.action";
//	}
?> 
