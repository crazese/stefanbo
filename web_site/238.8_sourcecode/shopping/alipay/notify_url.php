<?php
include "../conn.php";
require_once("alipay_notify.php");
require_once("alipay_config.php");
$alipay = new alipay_notify($partner,$security_code,$sign_type,$_input_charset,$transport);
$verify_result = $alipay->notify_verify();
if($verify_result) {

 //��ȡ֧�����ķ�������

  $dingdan=$_POST['trade_no'];    //��ȡ֧�������ݹ����Ķ�����
  $total_fee=$_POST['total_fee'];    //��ȡ֧�������ݹ������ܼ۸�

/*    $receive_name    =$_POST['receive_name'];   //��ȡ�ջ�������
	$receive_address =$_POST['receive_address']; //��ȡ�ջ��˵�ַ
	$receive_zip     =$_POST['receive_zip'];  //��ȡ�ջ����ʱ�
	$receive_phone   =$_POST['receive_phone']; //��ȡ�ջ��˵绰
	$receive_mobile  =$_POST['receive_mobile']; //��ȡ�ջ����ֻ�*/
    $out_trade_no = $_POST['out_trade_no']; //�ⲿ���׺�
	$buyer_email = $_POST['buyer_email'];
    $return_time = $_POST['notify_time'];
    $subject = $_POST['subject'];
    $trade_status=$_POST['trade_status'];    //��ȡ֧��������������״̬,���ݲ�ͬ��״̬���������ݿ� WAIT_BUYER_PAY(��ʾ�ȴ���Ҹ���);WAIT_SELLER_SEND_GOODS(��ʾ��Ҹ���ɹ�,�ȴ����ҷ���);WAIT_BUYER_CONFIRM_GOODS(�����Ѿ������ȴ����ȷ��);TRADE_FINISHED(��ʾ�����Ѿ��ɹ�����)
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
   //����������Զ������,������ݲ�ͬ��trade_status���в�ͬ����
	$sql = "insert into receive (`ProductName`,`order_num`,`total_fee`,`out_trade_no`,`buyer_email`,`return_time`,`url`) values ('$subject','$dingdan','$total_fee','$out_trade_no','$buyer_email','$return_time','alipay')";
	//echo $sql;
	mysql_query($sql);
echo "success";
}
	
	log_result("verify_success"); //����֤��������ļ�	
}
else  {
	echo "fail";
	//����������Զ�����룬����������Զ������,������ݲ�ͬ��trade_status���в�ͬ����
	log_result ("verify_failed");
}
function  log_result($word) {
	$fp = fopen("notifylog.txt","a");	
	flock($fp, LOCK_EX) ;
	fwrite($fp,$word."��ִ�����ڣ�".strftime("%Y%m%d%H%I%S",time())."\t\n");
	flock($fp, LOCK_UN); 
	fclose($fp);
}
	
?>