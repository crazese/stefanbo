<?php
/*
 * @Description: ��Ǯ�����֧�����ؽӿڷ���
 * @Copyright (c) �Ϻ���Ǯ��Ϣ�������޹�˾
 * @version 2.0
 */
include "../conn.php";
//��ȡ����������˻���
$merchantAcctId=trim($_REQUEST['merchantAcctId']);

//���������������Կ
///���ִ�Сд
$key="2WYMEI7J9RTFLSXM";

//��ȡ���ذ汾.�̶�ֵ
///��Ǯ����ݰ汾�������ö�Ӧ�Ľӿڴ������
///������汾�Ź̶�Ϊv2.0
$version=trim($_REQUEST['version']);

//��ȡ��������.�̶�ѡ��ֵ��
///ֻ��ѡ��1��2��3
///1�������ģ�2����Ӣ��
///Ĭ��ֵΪ1
$language=trim($_REQUEST['language']);

//ǩ������.�̶�ֵ
///1����MD5ǩ��
///��ǰ�汾�̶�Ϊ1
$signType=trim($_REQUEST['signType']);

//��ȡ֧����ʽ
///ֵΪ��10��11��12��13��14
///00�����֧��������֧��ҳ����ʾ��Ǯ֧�ֵĸ���֧����ʽ���Ƽ�ʹ�ã�10�����п�֧��������֧��ҳ��ֻ��ʾ���п�֧����.11���绰����֧��������֧��ҳ��ֻ��ʾ�绰֧����.12����Ǯ�˻�֧��������֧��ҳ��ֻ��ʾ��Ǯ�˻�֧����.13������֧��������֧��ҳ��ֻ��ʾ����֧����ʽ��.14��B2B֧��������֧��ҳ��ֻ��ʾB2B֧��������Ҫ���Ǯ���뿪ͨ����ʹ�ã�
$payType=trim($_REQUEST['payType']);

//��ȡ���д���
///�μ����д����б�
$bankId=trim($_REQUEST['bankId']);

//��ȡ�̻�������
$orderId=trim($_REQUEST['orderId']);

//��ȡ�����ύʱ��
///��ȡ�̻��ύ����ʱ��ʱ��.14λ���֡���[4λ]��[2λ]��[2λ]ʱ[2λ]��[2λ]��[2λ]
///�磺20080101010101
$orderTime=trim($_REQUEST['orderTime']);

//��ȡԭʼ�������
///�����ύ����Ǯʱ�Ľ���λΪ�֡�
///�ȷ�2 ������0.02Ԫ
$orderAmount=trim($_REQUEST['orderAmount']);

//��ȡ��Ǯ���׺�
///��ȡ�ý����ڿ�Ǯ�Ľ��׺�
$dealId=trim($_REQUEST['dealId']);

//��ȡ���н��׺�
///���ʹ�����п�֧��ʱ�������еĽ��׺š��粻��ͨ������֧������Ϊ��
$bankDealId=trim($_REQUEST['bankDealId']);

//��ȡ�ڿ�Ǯ����ʱ��
///14λ���֡���[4λ]��[2λ]��[2λ]ʱ[2λ]��[2λ]��[2λ]
///�磻20080101010101
$dealTime=trim($_REQUEST['dealTime']);
$dealTime2 = substr($dealTime,0,4)."-".substr($dealTime,4,2)."-".substr($dealTime,6,2)." ".substr($dealTime,8,2).":".substr($dealTime,10,2).":".substr($dealTime,12,2);
//��ȡʵ��֧�����
///��λΪ��
///�ȷ� 2 ������0.02Ԫ
$payAmount=trim($_REQUEST['payAmount']);

//��ȡ����������
///��λΪ��
///�ȷ� 2 ������0.02Ԫ
$fee=trim($_REQUEST['fee']);

//��ȡ��չ�ֶ�1
$ext1=trim($_REQUEST['ext1']);

//��ȡ��չ�ֶ�2
$ext2=trim($_REQUEST['ext2']);

//��ȡ������
///10���� �ɹ�; 11���� ʧ��
///00���� �¶����ɹ������Ե绰����֧���������أ�;01���� �¶���ʧ�ܣ����Ե绰����֧���������أ�
$payResult=trim($_REQUEST['payResult']);

//��ȡ�������
///��ϸ���ĵ���������б�
$errCode=trim($_REQUEST['errCode']);

//��ȡ����ǩ����
$signMsg=trim($_REQUEST['signMsg']);



//���ɼ��ܴ������뱣������˳��
	$merchantSignMsgVal=appendParam($merchantSignMsgVal,"merchantAcctId",$merchantAcctId);
	$merchantSignMsgVal=appendParam($merchantSignMsgVal,"version",$version);
	$merchantSignMsgVal=appendParam($merchantSignMsgVal,"language",$language);
	$merchantSignMsgVal=appendParam($merchantSignMsgVal,"signType",$signType);
	$merchantSignMsgVal=appendParam($merchantSignMsgVal,"payType",$payType);
	$merchantSignMsgVal=appendParam($merchantSignMsgVal,"bankId",$bankId);
	$merchantSignMsgVal=appendParam($merchantSignMsgVal,"orderId",$orderId);
	$merchantSignMsgVal=appendParam($merchantSignMsgVal,"orderTime",$orderTime);
	$merchantSignMsgVal=appendParam($merchantSignMsgVal,"orderAmount",$orderAmount);
	$merchantSignMsgVal=appendParam($merchantSignMsgVal,"dealId",$dealId);
	$merchantSignMsgVal=appendParam($merchantSignMsgVal,"bankDealId",$bankDealId);
	$merchantSignMsgVal=appendParam($merchantSignMsgVal,"dealTime",$dealTime);
	$merchantSignMsgVal=appendParam($merchantSignMsgVal,"payAmount",$payAmount);
	$merchantSignMsgVal=appendParam($merchantSignMsgVal,"fee",$fee);
	$merchantSignMsgVal=appendParam($merchantSignMsgVal,"ext1",$ext1);
	$merchantSignMsgVal=appendParam($merchantSignMsgVal,"ext2",$ext2);
	$merchantSignMsgVal=appendParam($merchantSignMsgVal,"payResult",$payResult);
	$merchantSignMsgVal=appendParam($merchantSignMsgVal,"errCode",$errCode);
	$merchantSignMsgVal=appendParam($merchantSignMsgVal,"key",$key);
$merchantSignMsg= md5($merchantSignMsgVal);


//��ʼ���������ַ
$rtnOk=0;
$rtnUrl="";

//�̼ҽ������ݴ�������ת���̼���ʾ֧�������ҳ��
///���Ƚ���ǩ���ַ�����֤
if(strtoupper($signMsg)==strtoupper($merchantSignMsg)){

	switch($payResult){		  
		  case "10":
		$payAmount = $payAmount/100;
		$ext = base64_decode($ext2);
		$array_ext = split(",",$ext);
		$ProductID = $array_ext[0];
		$email = $array_ext[1];
		$username = $array_ext[2];
		//echo $ext;
		$sql_product = "select * from product where ProductID= '$ProductID'";
		//echo $sql_product;
		$res_product = mysql_query($sql_product);
		$rows_product = mysql_fetch_array($res_product);
		$ProductName = $rows_product['ProductName'];
		
		$check = "select * from receive where order_num = '$orderId'";
		@$check_result = mysql_query($check);
		@$num = mysql_num_rows($check_result);
		//echo $num ;
		if ($num==0)
		{	
			$sql = "insert into receive (`url`,order_num,total_fee,return_time,ProductName,buyer_email,out_trade_no,username) values ('99bill','$orderId','$payAmount','$dealTime2','$ProductName','$email','$ext2','$username')";
			//echo $sql;
			mysql_query($sql);
		}
			/* 
			' �̻���վ�߼������ȷ����¶���֧��״̬Ϊ�ɹ�
			' �ر�ע�⣺ֻ��strtoupper($signMsg)==strtoupper($merchantSignMsg)����payResult=10���ű�ʾ֧���ɹ���
			*/
			
			//�������Ǯ�����������ṩ��Ҫ�ض���ĵ�ַ��
			$rtnOk=1;
			$rtnUrl="http://www.sothink.com.cn/shopping/99bill/show.php?msg=success!";
			
			break;
		  
		  default:

			$rtnOk=1;
			$rtnUrl="http://www.sothink.com.cn/shopping/99bill/show.php?msg=false!";

			break;
	}

}Else{

	$rtnOk=1;
	$rtnUrl="http://www.sothink.com.cn/shopping/99bill/show.php?msg=error!";

} 





	//���ܺ�����������ֵ��Ϊ�ղ�������ַ���
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
	//���ܺ�����������ֵ��Ϊ�ղ�������ַ���������


//���±������Ǯ�����������ṩ��Ҫ�ض���ĵ�ַ
?>
<result><?php echo $rtnOk; ?></result><redirecturl><?php echo $rtnUrl; ?></redirecturl>