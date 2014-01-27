<?PHP
/*
 * @Description: 快钱人民币支付网关接口范例
 * @Copyright (c) 上海快钱信息服务有限公司
 * @version 2.0
 */


//人民币网关账户号
///请登录快钱系统获取用户编号，用户编号后加01即为人民币网关账户号。
$email = trim($_POST['email']);
$name = trim($_POST['name']);
if ($name=="" || $email=="")
{
   echo "<script language=\"javascript\"> alert('请输入姓名和Email地址!');;history.go(-1);</script>";
   exit;
}
$orderAmount = $_POST['ProductPrice']*100;
$productName = $_POST['subject'];
$ProductID = $_POST['ProductID'];
$productDesc = $_POST['body'];
$ext = base64_encode($ProductID.",".$email.",".$name);
$orderId = date(Ymdhms).round(0,100).$ProductID;
$merchantAcctId="1001036242201";

//人民币网关密钥
///区分大小写.请与快钱联系索取
$key="2WYMEI7J9RTFLSXM";

//字符集.固定选择值。可为空。
///只能选择1、2、3.
///1代表UTF-8; 2代表GBK; 3代表gb2312
///默认值为1
$inputCharset="3";

//接受支付结果的页面地址.与[bgUrl]不能同时为空。必须是绝对地址。
///如果[bgUrl]为空，快钱将支付结果Post到[pageUrl]对应的地址。
///如果[bgUrl]不为空，并且[bgUrl]页面指定的<redirecturl>地址不为空，则转向到<redirecturl>对应的地址
$pageUrl="http://www.sothink.com.cn/shopping/99bill/show.php";

//服务器接受支付结果的后台地址.与[pageUrl]不能同时为空。必须是绝对地址。
///快钱通过服务器连接的方式将交易结果发送到[bgUrl]对应的页面地址，在商户处理完成后输出的<result>如果为1，页面会转向到<redirecturl>对应的地址。
///如果快钱未接收到<redirecturl>对应的地址，快钱将把支付结果post到[pageUrl]对应的页面。
$bgUrl="http://www.sothink.com.cn/shopping/99bill/receive.php";
	
//网关版本.固定值
///快钱会根据版本号来调用对应的接口处理程序。
///本代码版本号固定为v2.0
$version="v2.0";

//语言种类.固定选择值。
///只能选择1、2、3
///1代表中文；2代表英文
///默认值为1
$language="1";

//签名类型.固定值
///1代表MD5签名
///当前版本固定为1
$signType="1";	
   
//支付人姓名
///可为中文或英文字符
$payerName=$name;

//支付人联系方式类型.固定选择值
///只能选择1、2
///1代表Email；2代表手机号
$payerContactType="1";	

//支付人联系方式
///只能选择Email或手机号
$payerContact="";

//商户订单号
///由字母、数字、或[-][_]组成
$orderId=$orderId;		

//订单金额
///以分为单位，必须是整型数字
///比方2，代表0.02元
$orderAmount=$orderAmount;
	
//订单提交时间
///14位数字。年[4位]月[2位]日[2位]时[2位]分[2位]秒[2位]
///如；20080101010101
$orderTime=date('YmdHis');

//商品名称
///可为中文或英文字符
$productName=$productName;

//商品数量
///可为空，非空时必须为数字
$productNum="1";

//商品代码
///可为字符或者数字
$productId=$productId;

//商品描述
$productDesc=$productDesc;
	
//扩展字段1
///在支付结束后原样返回给商户
$ext1="";

//扩展字段2
///在支付结束后原样返回给商户
$ext2=$ext;
	
//支付方式.固定选择值
///只能选择00、10、11、12、13、14
///00：组合支付（网关支付页面显示快钱支持的各种支付方式，推荐使用）10：银行卡支付（网关支付页面只显示银行卡支付）.11：电话银行支付（网关支付页面只显示电话支付）.12：快钱账户支付（网关支付页面只显示快钱账户支付）.13：线下支付（网关支付页面只显示线下支付方式）.14：B2B支付（网关支付页面只显示B2B支付，但需要向快钱申请开通才能使用）
$payType="00";

//银行代码
///实现直接跳转到银行页面去支付,只在payType=10时才需设置参数
///具体代码参见 接口文档银行代码列表
$bankId="";

//同一订单禁止重复提交标志
///固定选择值： 1、0
///1代表同一订单号只允许提交1次；0表示同一订单号在没有支付成功的前提下可重复提交多次。默认为0建议实物购物车结算类商户采用0；虚拟产品类商户采用1
$redoFlag="1";

//快钱的合作伙伴的账户号
///如未和快钱签订代理合作协议，不需要填写本参数
$pid=""; ///合作伙伴在快钱的用户编号


   
//生成加密签名串
///请务必按照如下顺序和规则组成加密串！
	$signMsgVal=appendParam($signMsgVal,"inputCharset",$inputCharset);
	$signMsgVal=appendParam($signMsgVal,"pageUrl",$pageUrl);
	$signMsgVal=appendParam($signMsgVal,"bgUrl",$bgUrl);
	$signMsgVal=appendParam($signMsgVal,"version",$version);
	$signMsgVal=appendParam($signMsgVal,"language",$language);
	$signMsgVal=appendParam($signMsgVal,"signType",$signType);
	$signMsgVal=appendParam($signMsgVal,"merchantAcctId",$merchantAcctId);
	$signMsgVal=appendParam($signMsgVal,"payerName",$payerName);
	$signMsgVal=appendParam($signMsgVal,"payerContactType",$payerContactType);
	$signMsgVal=appendParam($signMsgVal,"payerContact",$payerContact);
	$signMsgVal=appendParam($signMsgVal,"orderId",$orderId);
	$signMsgVal=appendParam($signMsgVal,"orderAmount",$orderAmount);
	$signMsgVal=appendParam($signMsgVal,"orderTime",$orderTime);
	$signMsgVal=appendParam($signMsgVal,"productName",$productName);
	$signMsgVal=appendParam($signMsgVal,"productNum",$productNum);
	$signMsgVal=appendParam($signMsgVal,"productId",$productId);
	$signMsgVal=appendParam($signMsgVal,"productDesc",$productDesc);
	$signMsgVal=appendParam($signMsgVal,"ext1",$ext1);
	$signMsgVal=appendParam($signMsgVal,"ext2",$ext2);
	$signMsgVal=appendParam($signMsgVal,"payType",$payType);	
	$signMsgVal=appendParam($signMsgVal,"bankId",$bankId);
	$signMsgVal=appendParam($signMsgVal,"redoFlag",$redoFlag);
	$signMsgVal=appendParam($signMsgVal,"pid",$pid);
	$signMsgVal=appendParam($signMsgVal,"key",$key);
$signMsg= strtoupper(md5($signMsgVal));
	
	//功能函数。将变量值不为空的参数组成字符串
	Function appendParam($returnStr,$paramId,$paramValue){

		if($returnStr!=""){
			
				if($paramValue!=""){
					
					$returnStr.="&".$paramId."=".$paramValue;
				}
			
		}else{
		
			If($paramValue!=""){
				$returnStr=$paramId."=".$paramValue;
			}
		}
		
		return $returnStr;
	}
	//功能函数。将变量值不为空的参数组成字符串。结束
	
?>

<!doctype html public "-//w3c//dtd html 4.0 transitional//en" >
<html>
	<head>
		<title>使用快钱支付</title>
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312" >
	<style type="text/css">
<!--
body,td,th {
	font-family: 宋体;
	font-size: 12px;
}
-->
</style></head>
	
<BODY>
	<script language="javascript">
	onload=function()
	{
		document.forms["kqPay"].submit();
	}
</script>

	<div align="center" style="font-size=12px;font-weight: bold;color=red;">
		<form name="kqPay" method="post" action="https://www.99bill.com/gateway/recvMerchantInfoAction.htm">
		  <input type="hidden" name="inputCharset" value="<?php echo $inputCharset; ?>"/>
			<input type="hidden" name="bgUrl" value="<?php echo $bgUrl; ?>"/>
			<input type="hidden" name="pageUrl" value="<?php echo $pageUrl; ?>"/>
			<input type="hidden" name="version" value="<?php echo $version; ?>"/>
			<input type="hidden" name="language" value="<?php echo $language; ?>"/>
			<input type="hidden" name="signType" value="<?php echo $signType; ?>"/>
			<input type="hidden" name="signMsg" value="<?php echo $signMsg; ?>"/>
			<input type="hidden" name="merchantAcctId" value="<?php echo $merchantAcctId; ?>"/>
			<input type="hidden" name="payerName" value="<?php echo $payerName; ?>"/>
			<input type="hidden" name="payerContactType" value="<?php echo $payerContactType; ?>"/>
			<input type="hidden" name="payerContact" value="<?php echo $payerContact; ?>"/>
			<input type="hidden" name="orderId" value="<?php echo $orderId; ?>"/>
			<input type="hidden" name="orderAmount" value="<?php echo $orderAmount; ?>"/>
			<input type="hidden" name="orderTime" value="<?php echo $orderTime; ?>"/>
			<input type="hidden" name="productName" value="<?php echo $productName; ?>"/>
			<input type="hidden" name="productNum" value="<?php echo $productNum; ?>"/>
			<input type="hidden" name="productId" value="<?php echo $productId; ?>"/>
			<input type="hidden" name="productDesc" value="<?php echo $productDesc; ?>"/>
			<input type="hidden" name="ext1" value="<?php echo $ext1; ?>"/>
			<input type="hidden" name="ext2" value="<?php echo $ext2; ?>"/>
			<input type="hidden" name="payType" value="<?php echo $payType; ?>"/>
			<input type="hidden" name="bankId" value="<?php echo $bankId; ?>"/>
			<input type="hidden" name="redoFlag" value="<?php echo $redoFlag; ?>"/>
			<input type="hidden" name="pid" value="<?php echo $pid; ?>"/>
		</form>		
	</div>
	
</BODY>
</HTML>