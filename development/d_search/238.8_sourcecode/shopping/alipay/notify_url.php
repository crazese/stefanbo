<?php
include "../conn.php";
require_once("alipay_notify.php");
require_once("alipay_config.php");
$alipay = new alipay_notify($partner,$security_code,$sign_type,$_input_charset,$transport);
$verify_result = $alipay->notify_verify();
if($verify_result) {

 //获取支付宝的反馈参数

  $dingdan=$_POST['trade_no'];    //获取支付宝传递过来的订单号
  $total_fee=$_POST['total_fee'];    //获取支付宝传递过来的总价格

/*    $receive_name    =$_POST['receive_name'];   //获取收货人姓名
	$receive_address =$_POST['receive_address']; //获取收货人地址
	$receive_zip     =$_POST['receive_zip'];  //获取收货人邮编
	$receive_phone   =$_POST['receive_phone']; //获取收货人电话
	$receive_mobile  =$_POST['receive_mobile']; //获取收货人手机*/
    $out_trade_no = $_POST['out_trade_no']; //外部交易号
	$buyer_email = $_POST['buyer_email'];
    $return_time = $_POST['notify_time'];
    $subject = $_POST['subject'];
    $trade_status=$_POST['trade_status'];    //获取支付宝反馈过来的状态,根据不同的状态来更新数据库 WAIT_BUYER_PAY(表示等待买家付款);WAIT_SELLER_SEND_GOODS(表示买家付款成功,等待卖家发货);WAIT_BUYER_CONFIRM_GOODS(卖家已经发货等待买家确认);TRADE_FINISHED(表示交易已经成功结束)
	$check_sql = "select * from receive where out_trade_no = '$out_trade_no'";
	@$result = mysql_query($check_sql);
	@$num = mysql_num_rows($result);
	if (@$num>0)
	{
	      echo "<script language=\"javascript\"> location.href='/index.htm'</script>";
		  exit;		   
	}
	//echo $num;
	
	$check_out_order = "select * from `out_order` where out_trade_no = '$out_trade_no'";
	$order_result = mysql_query($check_out_order);
	@$order_num = mysql_num_rows($order_result);
	if (@$order_num==0)
	{      
	    echo "<script language=\"javascript\"> location.href='/index.htm'</script>";
		exit;   		
	}
 

if($_POST['trade_status'] == 'TRADE_FINISHED') {
   //这里放入你自定义代码,比如根据不同的trade_status进行不同操作
	$sql = "insert into receive (`ProductName`,`order_num`,`total_fee`,`out_trade_no`,`buyer_email`,`return_time`,`url`) values ('$subject','$dingdan','$total_fee','$out_trade_no','$buyer_email','$return_time','alipay')";
	//echo $sql;
	mysql_query($sql);
echo "success";
}
	
	log_result("verify_success"); //将验证结果存入文件	
}
else  {
	echo "fail";
	//这里放入你自定义代码，这里放入你自定义代码,比如根据不同的trade_status进行不同操作
	log_result ("verify_failed");
}
function  log_result($word) {
	$fp = fopen("notifylog.txt","a");	
	flock($fp, LOCK_EX) ;
	fwrite($fp,$word."：执行日期：".strftime("%Y%m%d%H%I%S",time())."\t\n");
	flock($fp, LOCK_UN); 
	fclose($fp);
}
	
?>