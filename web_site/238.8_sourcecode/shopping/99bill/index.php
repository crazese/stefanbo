<?php
   include "../conn.php";
   $ProductID = $_POST['ProductID'];   
   $sql = "select * from `product` where ProductID = '$ProductID'";
   $result = mysql_query($sql);
   $rows = mysql_fetch_array($result);
   //echo $rows['ProductName'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>Sothink Shopping Cart</title>
<link href="/css/main.css" rel="stylesheet" type="text/css" />
<script language="javascript">
function note()
{
  		if (document.payform.name.value=='')
		{
             alert ('请输入您的姓名!'); 
			 document.payform.name.focus();
			 return false;	
		}
		if (document.payform.email.value=='')
		{
             alert ('请输入您的Email地址!'); 
			 document.payform.email.focus();
			 return false;	
		}
		
		     var str = document.payform.email.value;   
             if(str.length!=0){   
             reg=/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;   
             if(!reg.test(str)){   
             alert("请输入正确的Email格式,谢谢!");
	         document.payform.email.focus();
		      return false;   
             }  		  
             }   		
		
}
</script>
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
 &nbsp;<a href="/index.htm">首页</a> &gt; 支付</td>
</tr>
<tr>
  <td><form id="payform" name="payform" method="post" action="send.php" onsubmit="return note();">
  <table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <td height="20" colspan="3" align="left">请输入用户个人信息：</td>
    </tr>
    <tr>
      <td width="54" height="20">&nbsp;</td>
      <td width="83">支付人姓名:</td>
      <td width="603" align="left"><input name="name" type="text" id="name" maxlength="20" /></td>
    </tr>
    <tr>
      <td height="20">&nbsp;</td>
      <td height="20">支付人Email:</td>
      <td height="20" align="left"><input name="email" type="text" id="email" maxlength="50" /></td>
    </tr>
    <tr>
      <td height="20" colspan="3" align="right" style="padding-right:30px;"><input name="ProductPrice" type="hidden" id="ProductPrice" value="<?php echo $rows['ProductPrice'];?>">
        <input name="ProductID" type="hidden" id="ProductID" value="<?php echo $rows['ProductID'];?>">
        <input name="body" type="hidden" id="body" value="<?php echo $rows['Description'];?>">
          <input name="subject" type="hidden" id="subject" value="<?php echo $rows['ProductName'];?>">
<input type="submit" name="Submit" value="支   付" style="height:23px; width:100px" /></td>
    </tr>
    <tr>
      <td height="20" colspan="3">&nbsp;</td>
    </tr>
  </table>
</form></td>
</tr>
</table>

<!--page-bottom-->
<div id="bottom-menu"><script language="javascript" type="text/javascript" src="/include/foot-nav.js"></script></div>
<div id="bottom-key">
  <div id="key-text">
    Sothink is a trademark of SouceTec Software Co.,LTD<br />
    <!-- #BeginLibraryItem "/Library/keywords_bottom.lbi" -->Flash Decompiler, SWF Decompiler, Flash Animation Software, DHTML Menu, Tree Menu, JavaScript Web Scroller, Flash Album<!-- #EndLibraryItem --></div>
</div>
</body>
</html>