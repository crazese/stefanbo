<?php
include "../conn.php";
$orderId=trim($_REQUEST['orderId']);
$orderAmount=trim($_REQUEST['orderAmount']);
$orderAmount = $orderAmount/100;
$ext2=trim($_REQUEST['ext2']);
$ext = base64_decode($ext2);
$array_ext = split(",",$ext);
$ProductID = $array_ext[0];
$email = $array_ext[1];
$username = $array_ext[2];
$sql_product = "select * from product where ProductID= '$ProductID'";
		echo $sql_product;
$res_product = mysql_query($sql_product);
$rows_product = mysql_fetch_array($res_product);
$ProductName = $rows_product['ProductName'];
$dealTime=trim($_REQUEST['dealTime']);
$dealTime = substr($dealTime,0,4)."-".substr($dealTime,4,2)."-".substr($dealTime,6,2)." ".substr($dealTime,8,2).":".substr($dealTime,10,2).":".substr($dealTime,12,2);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>硕思软件——付款信息</title>
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
		<div id="site-nav2">&nbsp;&nbsp;&nbsp;您的位置<span class="bold">:</span><a href="/index.htm">首页</a> &gt;<strong><span class="style2">购物信息</span></strong></div>
		<div id="content2">
		  <table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#DDDDDD">
                                      <tr>
                                        <td height="20" colspan="2" bgcolor="#FFFFFF">购物信息：</td>
                                      </tr>
                                      <tr>
                                        <td width="13%" height="20" align="right" bgcolor="#FFFFFF">商品名称：</td>
                                        <td width="87%" bgcolor="#FFFFFF"> <?php echo $ProductName;?></td>
                                      </tr>
                                      <tr>
                                        <td height="20" align="right" bgcolor="#FFFFFF">付款金额：</td>
                                        <td height="20" bgcolor="#FFFFFF"> <?php echo $orderAmount;?>元</td>
                                      </tr>
                                      <tr>
                                        <td height="20" align="right" bgcolor="#FFFFFF">订单号：</td>
                                        <td height="20" bgcolor="#FFFFFF"> <?php echo $orderId;?></td>
                                      </tr>
                                      <tr>
                                        <td height="20" align="right" bgcolor="#FFFFFF">商家E-mail：</td>
                                        <td height="20" bgcolor="#FFFFFF"> support@sothink.com.cn </td>
                                      </tr>
                                      <tr>
                                        <td height="20" align="right" bgcolor="#FFFFFF">付款日期：</td>
                                        <td height="20" bgcolor="#FFFFFF"> <?php echo $dealTime;?></td>
                                      </tr>
                                      <tr>
                                        <td height="20" colspan="2" align="right" bgcolor="#FFFFFF"><a href="/index.htm">继续购物&gt;&gt;</a></td>
                                      </tr>
                                    </table>
		</div>
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
