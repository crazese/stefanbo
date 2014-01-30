<?php
   include "conn.php";
   $ProductID = $_GET['ProductID'];
   $sql = "select * from `product` where ProductID = '$ProductID'";
   $result = mysql_query($sql);
   $rows = mysql_fetch_array($result);
   //echo $rows['ProductName'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>硕思软件安全支付中心</title>
<link href="/css/main.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
#page-contentbox #product-box #main-content-orderform {
	background-color: #CCCCCC;
}
.ordertd {
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: d2d2d2;
}
body,td,th {
	font-family: 宋体;
}
.style1 {color: #DDDDDD}
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
 &nbsp;<a href="/index.htm">首页</a> &gt; <a href="/shopping/order.php">在线定购</a> &gt; 安全支付</td>
</tr>
<tr>
  <td><form id="payform" name="payform" method="post" action="/shopping/alipay/index.php">
  <table width="96%" border="0" align="center" cellpadding="0" cellspacing="5">
    <tr>
      <td colspan="2" align="left">&nbsp;</td>
    </tr>
    <tr>
      <td height="20" colspan="2" align="left"><h1>您订购的产品信息</h1></td>
    </tr>
    <tr>
      <td width="15%" height="20" align="right" class="bold">产品名称：</td>
      <td width="85%" height="20" align="left"><?php echo $rows['ProductName'];?></td>
    </tr>
    <tr>
      <td height="20" align="right" class="bold">产品简介：</td>
      <td height="20" align="left"><?php echo $rows['Description'];?></td>
    </tr>
    <tr>
      <td height="20" align="right" class="bold">付费金额：</td>
      <td height="20" align="left">￥<?php echo $rows['ProductPrice'];?></td>
    </tr>
    
    <tr>
      <td height="20" align="right" class="bold">交易日期：</td>
      <td height="20" align="left"><?php echo date("Y-m-d H:i:s",time());?></td>
    </tr>
    <tr>
      <td height="28" align="right" valign="bottom" class="bold">商家名称：</td>
      <td height="28" align="left" valign="bottom">武汉硕思软件有限公司 <a href="https://www.alipay.com/aip/identity_verify.htm?id=AIP11068857" target="_blank"><img src="/images/system/sign_trust_alipay.gif" alt="已加入支付宝信任商家，点击图标可查看该商户的验证信息" width="74" height="24" /> </a></td>
    </tr>
  </table>
  <hr>
  <table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
    
    
    <tr>
      <td height="20" colspan="2" align="left"><h3>请选择支付方式：</h3></td>
    </tr>
    <tr>
      <td width="54" height="20">&nbsp;</td>
      <td width="646" align="left"><input name="pay" type="radio"  onclick="javascript:payform.action='/shopping/alipay/index.php';" value="zfb" checked="checked" />
        支付宝支付(仅支持IE浏览器)<a href="https://www.alipay.com/aip/identity_verify.htm?id=AIP11068857" target="_blank"><img src="../images/system/alipay_logo.gif" width="251" height="47" /></a></td>
    </tr>
    
	<!--<tr>
      <td height="20">&nbsp;</td>
      <td height="20" align="left"><input name="pay" type="radio" value="kq" onClick="javascript:payform.action='/shopping/99bill/index.php';" />
        快钱支付(仅支持IE浏览器)<img src="/images/system/99bill_logo.gif" width="96" height="65" /><br />
线上即时付款，方便快速，支持快钱帐户支付、银行卡支付、线下支付和电话支付，覆盖全国银行卡，并可享受积分换礼！</td>
    </tr>-->
	
    <tr>
      <td height="20" colspan="2" align="right" style="padding-right:30px;"><input name="ProductPrice" type="hidden" id="ProductPrice" value="<?php echo $rows['ProductPrice'];?>">
        <input name="ProductID" type="hidden" id="ProductID" value="<?php echo $rows['ProductID'];?>">
        <input name="body" type="hidden" id="body" value="<?php echo $rows['Description'];?>">
          <input name="subject" type="hidden" id="subject" value="<?php echo $rows['ProductName'];?>">
<input type="submit" name="Submit" value="支   付" style="height:23px; width:100px" /></td>
    </tr>
    <tr>
      <td height="20" colspan="2">&nbsp;</td>
    </tr>
  </table>
</form></td>
</tr>
</table>

<!--page-bottom-->
<div id="bottom-menu"><script language="javascript" type="text/javascript" src="/include/foot-nav.js"></script></div>
<div id="bottom-key">
  <div id="key-text">
    版权所有 1997-2008, 武汉硕思软件有限公司<br />
    <script language="javascript" type="text/javascript" src="http://js.users.51.la/1608115.js"></script>
<noscript><a href="http://www.51.la/?1608115" target="_blank"><img alt="&#x6211;&#x8981;&#x5566;&#x514D;&#x8D39;&#x7EDF;&#x8BA1;" src="http://img.users.51.la/1608115.asp" style="border:none" /></a></noscript>
  制作动画影片，分解动画，制作动态网页菜单，反编译SWF,将SWF导出成FLA,  自由编辑HTML, SWF编辑器</div>
</div>
<div id="bottom-menu"><a href="http://www.swf-decompiler.com/">Flash Decompiler</a> | <a href="http://mac.sothink.com/">Flash Decompiler Mac</a> | <a href="http://www.swf-to-fla.com/">Convert SWF to FLA</a> | <a href="http://www.dhtml-menu-builder.com/">JavaScript Menu</a> | <a href="http://flash-animation-maker.com/">Flash Banner Maker</a> | <a href="http://www.logo-maker.net/">Logo Design Resources</a></div>
</body>
</html>