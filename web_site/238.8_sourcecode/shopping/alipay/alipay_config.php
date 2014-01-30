<?php
$partner = "2088001352135437";//合作伙伴ID
$security_code = "86f2se70kl9qbhg6j2au220zxvbef4y9";//安全检验码
$seller_email = "support@sothink.com.cn";//卖家邮箱
$_input_charset = "GBK"; //字符编码格式  目前支持 GBK 或 utf-8
$sign_type = "MD5"; //加密方式  系统默认(不要修改)
$transport= "https";//访问模式,你可以根据自己的服务器是否支持ssl访问而选择http以及https访问模式(系统默认,不要修改)
$notify_url = "http://www.sothink.com.cn/shopping/alipay/notify_url.php";// 异步返回地址
$return_url = "http://www.sothink.com.cn/shopping/alipay/return_url.php"; //同步返回地址
$show_url   ="http://www.sothink.com.cn"  //你网站商品的展示地址,可以为空

/** 提示：如何获取安全校验码和合作ID
1.访问 www.alipay.com，然后登陆您的帐户($seller_email).
2.点商家服务.

*/
?>