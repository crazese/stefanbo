<?php
   include "conn.php";
   //$ProductID = $_GET['ProductID'];
   $sql = "select * from `product` where IsShow = 'y'";
   $result = mysql_query($sql);
   
   //echo $rows['ProductName'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>˶˼�����Ʒ��������</title>
<link href="/css/main.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
#page-contentbox #product-box #main-content-orderform {
	background-color: #CCCCCC;
}
.ordertd {
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #CCCCCC;	
}
body,td,th {
	font-family: ����;
}
-->
</style>
</head>
<body>
<div id="top-order"></div>
<!--page_top-->
<!--product-content-->
<table width="775" border="1" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr>
<td height="24" background="/images/system/bg_title.gif" align="left">
 &nbsp;<a href="/index.htm">��ҳ</a> &gt; ��Ʒ���߹���</td>
</tr>
<tr>
  <td>
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="font-content">

	  <tr  align="center">
        <td height="29" align="left"  background="/images/system/bg-ordertd.gif" style="padding-left:10px;" class="ordertd"><strong>��Ʒ����</strong></td>
        <td width="16%" align="left"  background="/images/system/bg-ordertd.gif" class="ordertd"><strong>��Ʒ�۸�</strong></td>
        <td align="center"  background="/images/system/bg-ordertd.gif" class="ordertd"><strong>����</strong></td>
        </tr>
    
	   <?php 
	  while ($rows = mysql_fetch_array($result))
	  {
	 ?>
      <tr bgcolor="#ffffff" style="padding-left:" 10; padding-top:="padding-top:" 0; padding-bottom:="padding-bottom:" 0 onMouseOver="this.style.backgroundColor='#bfd8ee'" onMouseOut="this.style.backgroundColor='#ffffff'">
        <td height="25" align="left" class="ordertd" style="padding-left:10px;"><?php echo $rows['ProductName'];?></td>
        <td width="16%" align="left" class="ordertd"><?php if($rows['ProductID']==1) echo "<s>��".$rows['ProductPrice']."</s><span style=\"color:red;\"> (�Ķ���388)</span>"; else echo "��".$rows['ProductPrice'];?></td>
        <td align="center" class="ordertd"><a href="<?php if($rows['ProductID']==1) echo "http://www.sharebank.com.cn/soft/softbuy.php?soid=33826"; elseif($rows['ProductID']==4) echo "http://www.mofacaidan.com/goumai.html" ;else echo "/shopping/?ProductID=".$rows['ProductID'];?>"><img src="/images/system/tool_buy.gif" width="74" height="19" border="0" /></a></td>
        </tr>
       <?php }?>
    </table>
</td>
</tr>
<tr><td height="111" align="center"><p>���̼��Ѽ��� <a href="https://www.alipay.com/aip/identity_verify.htm?id=AIP11068857" target="_blank"><img height="24" alt="�Ѽ���֧���������̼ң����ͼ��ɲ鿴���̻�����֤��Ϣ" src="../images/system/sign_trust_alipay.gif" border="0" /></a> ֧���������̼ҡ�</p></td>
</tr>

</table>

<!--page-bottom-->
<div id="bottom-menu"><script language="javascript" type="text/javascript" src="/include/foot-nav.js"></script></div>
<div id="bottom-key">
  <div id="key-text">
    ��Ȩ���� 1997-2008, �人˶˼������޹�˾<br /><script language="javascript" type="text/javascript" src="http://js.users.51.la/1608115.js"></script>
<noscript><a href="http://www.51.la/?1608115" target="_blank"><img alt="&#x6211;&#x8981;&#x5566;&#x514D;&#x8D39;&#x7EDF;&#x8BA1;" src="http://img.users.51.la/1608115.asp" style="border:none" /></a></noscript>
  ��������ӰƬ���ֽ⶯����������̬��ҳ�˵���������SWF,��SWF������FLA,  ���ɱ༭HTML, SWF�༭��</div>
</div>
<div id="bottom-menu"><a href="http://www.swf-decompiler.com/">Flash Decompiler</a> | <a href="http://mac.sothink.com/">Flash Decompiler Mac</a> | <a href="http://www.swf-to-fla.com/">Convert SWF to FLA</a> | <a href="http://www.dhtml-menu-builder.com/">JavaScript Menu</a> | <a href="http://flash-animation-maker.com/">Flash Banner Maker</a> | <a href="http://www.logo-maker.net/">Logo Design Resources</a></div>
</body>
</html>