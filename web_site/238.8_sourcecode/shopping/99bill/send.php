<?PHP
/*
 * @Description: ��Ǯ�����֧�����ؽӿڷ���
 * @Copyright (c) �Ϻ���Ǯ��Ϣ�������޹�˾
 * @version 2.0
 */


//����������˻���
///���¼��Ǯϵͳ��ȡ�û���ţ��û���ź��01��Ϊ����������˻��š�
$email = trim($_POST['email']);
$name = trim($_POST['name']);
if ($name=="" || $email=="")
{
   echo "<script language=\"javascript\"> alert('������������Email��ַ!');;history.go(-1);</script>";
   exit;
}
$orderAmount = $_POST['ProductPrice']*100;
$productName = $_POST['subject'];
$ProductID = $_POST['ProductID'];
$productDesc = $_POST['body'];
$ext = base64_encode($ProductID.",".$email.",".$name);
$orderId = date(Ymdhms).round(0,100).$ProductID;
$merchantAcctId="1001036242201";

//�����������Կ
///���ִ�Сд.�����Ǯ��ϵ��ȡ
$key="2WYMEI7J9RTFLSXM";

//�ַ���.�̶�ѡ��ֵ����Ϊ�ա�
///ֻ��ѡ��1��2��3.
///1����UTF-8; 2����GBK; 3����gb2312
///Ĭ��ֵΪ1
$inputCharset="3";

//����֧�������ҳ���ַ.��[bgUrl]����ͬʱΪ�ա������Ǿ��Ե�ַ��
///���[bgUrl]Ϊ�գ���Ǯ��֧�����Post��[pageUrl]��Ӧ�ĵ�ַ��
///���[bgUrl]��Ϊ�գ�����[bgUrl]ҳ��ָ����<redirecturl>��ַ��Ϊ�գ���ת��<redirecturl>��Ӧ�ĵ�ַ
$pageUrl="http://www.sothink.com.cn/shopping/99bill/show.php";

//����������֧������ĺ�̨��ַ.��[pageUrl]����ͬʱΪ�ա������Ǿ��Ե�ַ��
///��Ǯͨ�����������ӵķ�ʽ�����׽�����͵�[bgUrl]��Ӧ��ҳ���ַ�����̻�������ɺ������<result>���Ϊ1��ҳ���ת��<redirecturl>��Ӧ�ĵ�ַ��
///�����Ǯδ���յ�<redirecturl>��Ӧ�ĵ�ַ����Ǯ����֧�����post��[pageUrl]��Ӧ��ҳ�档
$bgUrl="http://www.sothink.com.cn/shopping/99bill/receive.php";
	
//���ذ汾.�̶�ֵ
///��Ǯ����ݰ汾�������ö�Ӧ�Ľӿڴ������
///������汾�Ź̶�Ϊv2.0
$version="v2.0";

//��������.�̶�ѡ��ֵ��
///ֻ��ѡ��1��2��3
///1�������ģ�2����Ӣ��
///Ĭ��ֵΪ1
$language="1";

//ǩ������.�̶�ֵ
///1����MD5ǩ��
///��ǰ�汾�̶�Ϊ1
$signType="1";	
   
//֧��������
///��Ϊ���Ļ�Ӣ���ַ�
$payerName=$name;

//֧������ϵ��ʽ����.�̶�ѡ��ֵ
///ֻ��ѡ��1��2
///1����Email��2�����ֻ���
$payerContactType="1";	

//֧������ϵ��ʽ
///ֻ��ѡ��Email���ֻ���
$payerContact="";

//�̻�������
///����ĸ�����֡���[-][_]���
$orderId=$orderId;		

//�������
///�Է�Ϊ��λ����������������
///�ȷ�2������0.02Ԫ
$orderAmount=$orderAmount;
	
//�����ύʱ��
///14λ���֡���[4λ]��[2λ]��[2λ]ʱ[2λ]��[2λ]��[2λ]
///�磻20080101010101
$orderTime=date('YmdHis');

//��Ʒ����
///��Ϊ���Ļ�Ӣ���ַ�
$productName=$productName;

//��Ʒ����
///��Ϊ�գ��ǿ�ʱ����Ϊ����
$productNum="1";

//��Ʒ����
///��Ϊ�ַ���������
$productId=$productId;

//��Ʒ����
$productDesc=$productDesc;
	
//��չ�ֶ�1
///��֧��������ԭ�����ظ��̻�
$ext1="";

//��չ�ֶ�2
///��֧��������ԭ�����ظ��̻�
$ext2=$ext;
	
//֧����ʽ.�̶�ѡ��ֵ
///ֻ��ѡ��00��10��11��12��13��14
///00�����֧��������֧��ҳ����ʾ��Ǯ֧�ֵĸ���֧����ʽ���Ƽ�ʹ�ã�10�����п�֧��������֧��ҳ��ֻ��ʾ���п�֧����.11���绰����֧��������֧��ҳ��ֻ��ʾ�绰֧����.12����Ǯ�˻�֧��������֧��ҳ��ֻ��ʾ��Ǯ�˻�֧����.13������֧��������֧��ҳ��ֻ��ʾ����֧����ʽ��.14��B2B֧��������֧��ҳ��ֻ��ʾB2B֧��������Ҫ���Ǯ���뿪ͨ����ʹ�ã�
$payType="00";

//���д���
///ʵ��ֱ����ת������ҳ��ȥ֧��,ֻ��payType=10ʱ�������ò���
///�������μ� �ӿ��ĵ����д����б�
$bankId="";

//ͬһ������ֹ�ظ��ύ��־
///�̶�ѡ��ֵ�� 1��0
///1����ͬһ������ֻ�����ύ1�Σ�0��ʾͬһ��������û��֧���ɹ���ǰ���¿��ظ��ύ��Ρ�Ĭ��Ϊ0����ʵ�ﹺ�ﳵ�������̻�����0�������Ʒ���̻�����1
$redoFlag="1";

//��Ǯ�ĺ��������˻���
///��δ�Ϳ�Ǯǩ���������Э�飬����Ҫ��д������
$pid=""; ///��������ڿ�Ǯ���û����


   
//���ɼ���ǩ����
///����ذ�������˳��͹�����ɼ��ܴ���
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
	
	//���ܺ�����������ֵ��Ϊ�յĲ�������ַ���
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
	//���ܺ�����������ֵ��Ϊ�յĲ�������ַ���������
	
?>

<!doctype html public "-//w3c//dtd html 4.0 transitional//en" >
<html>
	<head>
		<title>ʹ�ÿ�Ǯ֧��</title>
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312" >
	<style type="text/css">
<!--
body,td,th {
	font-family: ����;
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