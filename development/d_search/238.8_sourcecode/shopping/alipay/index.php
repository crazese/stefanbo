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
"partner" =>$partner,                                               //�����̻���
"return_url" =>$return_url,  //ͬ������
"notify_url" =>$notify_url,  //�첽����
"_input_charset" => $_input_charset,                                //�ַ�����Ĭ��ΪGBK
"subject" => $subject,                                                //��Ʒ���ƣ�����
"body" => $body,                                           //��Ʒ����������
"out_trade_no" => $out_trade_no,                      //��Ʒ�ⲿ���׺ţ�����,ÿ�β��Զ����޸�
"total_fee" => $ProductPrice,                                 //��Ʒ���ۣ�����
"payment_type"=>"1",                               // Ĭ��Ϊ1,����Ҫ�޸�

"show_url" => $show_url,            //��Ʒ�����վ
"seller_email" => $seller_email                //�������䣬����
);
$alipay = new alipay_service($parameter,$security_code,$sign_type);
$link=$alipay->create_url();
$regdate = date("Y-m-d H:i:s");
$sql = "insert into out_order (out_trade_no,regdate) values ('$out_trade_no','$regdate')";
mysql_query($sql);
/*print <<<EOT

<a href= $link >֧��������֧��</a>
EOT;*/
?>
<script language="javascript">
	onload=function()
	{
		document.forms["pay"].submit();
	}
</script>
<form action="<?php echo $link;?>" method="post" name="pay" id="pay"></form>
