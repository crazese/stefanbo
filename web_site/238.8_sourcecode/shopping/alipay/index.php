<?php
include "../conn.php";
require_once("alipay_service.php");
require_once("alipay_config.php");
$subject = $_POST['subject'];
$body = $_POST['body'];
$ProductID = $_POST['ProductID'];
$ProductPrice = $_POST['ProductPrice'];
$out_trade_no = base64_encode(date(Ymdhms).":".$ProductID);

$parameter = array(
"service" => "create_direct_pay_by_user",
"partner" =>$partner,                                               //合作商户号
"return_url" =>$return_url,  //同步返回
"notify_url" =>$notify_url,  //异步返回
"_input_charset" => $_input_charset,                                //字符集，默认为GBK
"subject" => $subject,                                                //商品名称，必填
"body" => $body,                                           //商品描述，必填
"out_trade_no" => $out_trade_no,                      //商品外部交易号，必填,每次测试都须修改
"total_fee" => $ProductPrice,                                 //商品单价，必填
"payment_type"=>"1",                               // 默认为1,不需要修改

"show_url" => $show_url,            //商品相关网站
"seller_email" => $seller_email                //卖家邮箱，必填
);
$alipay = new alipay_service($parameter,$security_code,$sign_type);
$link=$alipay->create_url();
$regdate = date("Y-m-d H:i:s");
$sql = "insert into out_order (out_trade_no,regdate) values ('$out_trade_no','$regdate')";
mysql_query($sql);
/*print <<<EOT

<a href= $link >支付宝在线支付</a>
EOT;*/
?>
<script language="javascript">
	onload=function()
	{
		document.forms["pay"].submit();
	}
</script>
<form action="<?php echo $link;?>" method="post" name="pay" id="pay"></form>
