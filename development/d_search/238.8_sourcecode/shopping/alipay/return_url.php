<?php
/*session_start();
	  function randoms($stat, $end, $num) 
      {
          $t = range($stat, $end);
          shuffle($t);
          return array_slice($t, -$num);
      }
		    $code = randoms(1000, 9999, 1);
	        $code = $code[0];  
		    $_SESSION['code'] = $code;

       if ($_SESSION['code_y']!="")
	   {
	      echo "<script language=\"javascript\"> location.href='/index.htm'</script>";
		  exit;		  
	   }
	   else
	   {
	      $_SESSION['code_y'] = $code;
		  $_SESSION['code'] = "";
	   }*/


include "../conn.php";
require_once("alipay_notify.php");
require_once("alipay_config.php");
$alipay = new alipay_notify($partner,$security_code,$sign_type,$_input_charset,$transport);
$verify_result = $alipay->return_verify();

 //��ȡ֧�����ķ�������
   $dingdan=$_GET['trade_no'];   //��ȡ������
   $total_fee=$_GET['total_fee'];    //��ȡ�ܼ۸�
   $subject = $_GET['subject'];
   $seller_email = $_GET['seller_email'];
/*  $receive_name    =$_GET['receive_name'];   //��ȡ�ջ�������
	$receive_address =$_GET['receive_address']; //��ȡ�ջ��˵�ַ
	$receive_zip     =$_GET['receive_zip'];  //��ȡ�ջ����ʱ�
	$receive_phone   =$_GET['receive_phone']; //��ȡ�ջ��˵绰
	$receive_mobile  =$_GET['receive_mobile']; //��ȡ�ջ����ֻ�*/
    $out_trade_no = $_GET['out_trade_no']; //�ⲿ���׺�
	$buyer_email = $_GET['buyer_email'];
    $return_time = $_GET['notify_time'];
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
	/*echo $check_out_order."<br>";
	echo $order_num ;*/
	
if($verify_result) {
	//echo "��Ҹ���ɹ�,����ƽ����";
/*	$sql = "insert into receive (`ProductName`,`order_num`,`total_fee`,`out_trade_no`,`buyer_email`,`return_time`) values ('$subject','$dingdan','$total_fee','$out_trade_no','$buyer_email','$return_time')";
	//echo $sql;
	mysql_query($sql);*/
	//����������Զ������,������ݲ�ͬ��trade_status���в�ͬ����
	log_result("verify_success"); //����֤��������ļ�	
}
else  {
	//echo "fail";
	//����������Զ�����룬����������Զ������,������ݲ�ͬ��trade_status���в�ͬ����
	log_result ("verify_failed");
}

//��־��Ϣ,��֧���������Ĳ�����¼����
function  log_result($word) { 
	$fp = fopen("log.txt","a");	
	flock($fp, LOCK_EX) ;
	fwrite($fp,$word."��ִ�����ڣ�".strftime("%Y%m%d%H%I%S",time())."\t\n");
	flock($fp, LOCK_UN); 
	fclose($fp);
}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>˶˼�������������Ϣ</title>
<meta name="Description" content="Flash Decompiler, convert swf to fla, DHTML Menu builder to create DHTML drop down menu, use Sothink SWF Quicker to edit SWF, Video to avi" />
<meta name="Keywords" content="Flash decompiler, swf to fla, convert swf to fla, dhtml menu, drop down menu, dhtml menu builder, flash maker, edit swf, swf editor, video to avi." />
<meta content="all" name="robots" />
<link href="/css/main.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" language="javascript" src="/include/js_dmenu/js_common/stmenu.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="/include/js_dmenu/js_common/stlib.js"></script>

<style type="text/css">
<!--
.style1 {color: red}
.style2 {color: #FF9900}
-->
</style>
</head>

<body>

<!--page_top-->
<div id="top-key">Sothink-Flash Movie Maker, SWF Decompiler, DHTML Menu, SWF Quicker, SWF to FLA converter, SWF Editor, HTML Editor</div>
<div id="top-logo"><script language="JavaScript" type="text/javascript" src="/include/top-logoarea.js"></script></div>
<div id="top-menu">
  <div id="top-dmenu"><script type="text/javascript" language="JavaScript" src="/include/js_dmenu/topmenu.js"></script></div>
  <div id="top-menu2">
    <form id="search" action="http://www.google.com/custom" method="get"  name="search" target="_blank">
        <div style="float:left; width:18px;"><img src="/images/system/arrow_search.gif" alt="arrow" width="13" height="20" /></div>
	    <div id="searchTextDiv">
        <input type="hidden" name="cof1" value="AH:center;AWFID:402f7fba780fe25f;" /> 
        <input type="hidden" name="domains" value="sothink.com" /> 
        <input type="hidden" name="domains1" value="www.sothink.com" /> 
        <input type="hidden" name="sitesearch" value="sothink.com" />
        <input id="q" name="q" type="text" class="verdana10" size="16" />
		</div>
		<div style="float:left; width:18px;">
		<input name="image" type="image" id="submit" src="/images/system/bg_formsearch.gif" alt="Go" style="width:18px; height:20px; border:none;" />
		</div>
    </form>
  </div>
</div>

<!--product-content-->
<div id="page-contentbox">
	<!--ad-->
	<!--content-->
	<div id="product-box">
	  <div id="main-content2">
		<div id="site-nav2">&nbsp;&nbsp;&nbsp;����λ��<span class="bold">:</span><a href="/index.htm">��ҳ</a> &gt;<strong><span class="style2">������Ϣ</span></strong></div>
		<div id="content2"> <?php if ($verify_result) {?>
                                    <table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#DDDDDD">
                                      <tr>
                                        <td height="20" colspan="2" bgcolor="#FFFFFF">������Ϣ��</td>
                                      </tr>
                                      <tr>
                                        <td width="13%" height="20" align="right" bgcolor="#FFFFFF">��Ʒ���ƣ�</td>
                                        <td width="87%" bgcolor="#FFFFFF"> <?php echo $subject;?></td>
                                      </tr>
                                      <tr>
                                        <td height="20" align="right" bgcolor="#FFFFFF">�����</td>
                                        <td height="20" bgcolor="#FFFFFF"> <?php echo $total_fee;?></td>
                                      </tr>
                                      <tr>
                                        <td height="20" align="right" bgcolor="#FFFFFF">�����ţ�</td>
                                        <td height="20" bgcolor="#FFFFFF"> <?php echo $dingdan;?></td>
                                      </tr>
                                      <tr>
                                        <td height="20" align="right" bgcolor="#FFFFFF">�̼�E-mail��</td>
                                        <td height="20" bgcolor="#FFFFFF"> <?php echo $seller_email;?></td>
                                      </tr>
                                      <tr>
                                        <td height="20" align="right" bgcolor="#FFFFFF">�������ڣ�</td>
                                        <td height="20" bgcolor="#FFFFFF"> <?php echo $return_time;?></td>
                                      </tr>
                                      <tr>
                                        <td height="20" colspan="2" align="right" bgcolor="#FFFFFF"><a href="/index.htm">��������&gt;&gt;</a></td>
                                      </tr>
                                    </table>
									<?php 
									}
									else
									{
									?>
									<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#DDDDDD">
                                      <tr>
                                        <td height="20" align="center" bgcolor="#FFFFFF">�Բ���������ʧ�ܣ�</td>
                                      </tr>
                                    </table>
		<?php }?></div>
      </div>
	</div>
</div>

<!--page-bottom-->
<div id="bottom-menu"><script language="javascript" type="text/javascript" src="/include/foot-nav.js"></script></div>
<div id="bottom-key">
  <div id="key-text">
    Sothink is a trademark of SourceTec Software Co.,LTD<br />
    <!-- #BeginLibraryItem "/Library/keywords_bottom.lbi" -->Flash Decompiler, SWF Decompiler, Flash Animation Software, DHTML Menu, Tree Menu, JavaScript Web Scroller, Flash Album<!-- #EndLibraryItem --></div>
</div>
</body>


</html>
