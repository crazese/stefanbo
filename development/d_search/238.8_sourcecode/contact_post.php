<?
session_start();
$Name=$_POST['Name'];
$Email=$_POST['Email'];
$URL=$_POST['URL'];
$Comments=$_POST['Comments'];
$code_y = trim($_POST['code_y']);
if(!$Name)
{
	echo("
		<script>
		window.alert('请输入您的姓名，谢谢！')
		history.go(-1)
		</script>
		");
	exit;
}
if(!$Email)
{
	echo("
		<script>
		window.alert('请输入您的电子邮件，谢谢！')
		history.go(-1)
		</script>
		");
	exit;
}
if(!$Comments)
{
        echo("
                <script>
                window.alert('请输入您的留言，谢谢！')
                history.go(-1)
                </script>
                ");
        exit;
}
if($code_y!=$_SESSION['code'])
{
        echo("
                <script>
                window.alert('请输入正确的验证码，谢谢！')              
				history.go(-1)
                </script>
                ");
        exit;
}


$to="support@sothink.com.cn";
$subject="客户留言";
$headers="From:".$Email;
$contents="姓名:".$Name."\n\n电子邮件:".$Email."\n\n网址:".$URL."\n\n留言:".$Comments;
mail($to,$subject,$contents,$headers);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/page-noleft.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=GB2312" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Sothink-Flash animation Maker, SWF Decompiler, DHTML Menu, SWF Quicker, SWF to FLA converter, SWF Editor, HTML Editor</title>
<meta name="Description" content="Web authoring shareware including Flash animation Maker, SWF to FLA converter, Sothink SWF decompiler, DHTML Menu, SWF Quicker, Glanda, HTML editor, with samples, templates, and tutorials." />
<meta name="Keywords" content="Flash animation Makers, dhtml menu, html editor, flash decompiler, swf decompiler, swf editor, swf maker, swf to fla, flash software, flash animation, animation software, flash movies, flash templates,flash buttons,flash albums,flash tutorials,text effects,animated effects,sothink, sourcetec software" />
<meta content="General" name="rating" />
<meta content="15 days" name="revisit-after" />
<meta content="ALL" name="ROBOTS" />
<meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" />
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-199040-2";
urchinTracker();
</script>

<style type="text/css">
<!--
.style2 {color: #333333}
-->
</style>
<!-- InstanceEndEditable -->
<link href="/css/main.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" language="javascript" src="/include/js_dmenu/js_common/stmenu.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="/include/js_dmenu/js_common/stlib.js"></script>
<!-- InstanceBeginEditable name="head" --><!-- InstanceEndEditable -->
</head>

<body>

<!--page_top-->
<div id="top-key"><!-- InstanceBeginEditable name="top-key" -->
      <!--otherpagekey-->
      &nbsp;&nbsp;&nbsp;&nbsp;Sothink-Flash animation Maker, SWF Decompiler, DHTML Menu, SWF Quicker, SWF to FLA converter, SWF Editor, HTML Editor <!-- InstanceEndEditable --></div>
<div id="top-logo"><script language="JavaScript" type="text/javascript" src="/include/top-logoarea.js"></script></div>
<div id="top-menu">
  <div id="top-dmenu"><script type="text/javascript" language="JavaScript" src="/include/js_dmenu/topmenu.js"></script></div>
  <div id="top-menu2">
    <form id="search" action="http://www.google.com/custom" method="get"  name="search" target="_blank">
        <div style="float:left; width:18px;"><img src="/images/system/arrow_search.gif" alt="arrow" width="13" height="20" /></div>
	    <div id="searchTextDiv">
        <input type="hidden" name="cof1" value="AH:center;AWFID:402f7fba780fe25f;" /> 
        <input type="hidden" name="domains" value="sothink.com.cn" /> 
        <input type="hidden" name="domains1" value="www.sothink.com.cn" /> 
        <input type="hidden" name="sitesearch" value="sothink.com.cn" />
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
	<div id="product-ad">
	  <div id="ad-text">
	    <script language="JavaScript" type="text/javascript" src="/include/left-topbanner.js"></script>
	  </div>
  </div>
	<!--content-->
	<div id="product-box">
	  <div id="main-content2">
		<div id="site-nav2">&nbsp;&nbsp;&nbsp;&nbsp;<span class="bold">您的位置:</span><!-- InstanceBeginEditable name="nav" -->
        <a href="/index.htm">首页</a> | 联系我们 
        <!-- InstanceEndEditable --></div>
		<div id="content2"><!-- InstanceBeginEditable name="content" --><table width="100%" border="0" align="center">
                  <tr>
                  <td colspan="2"><div align="center"><strong>非常感谢您的留言！</strong></div>
                    <br></td>
                </tr>
                  <tr>
                  <td colspan="2">下面是您提交到 support@sothink.com.cn的内容</td>
                </tr>
			     <tr>
                  <td colspan="2">&nbsp;</td>
                </tr>
				<tr>
                  <td width="12%">姓名:</td>
                  <td width="88%"><?php echo ("$Name"); ?></td>
                </tr>
                <tr>
                  <td>网址:</td>
                  <td><?php echo ("$URL"); ?></td>
                </tr>
				 <tr>
                  <td>留言:</td>
                  <td><?php echo ("$Comments"); ?></td>
                </tr>

              </table><!-- InstanceEndEditable --></div>
		
      </div>
	</div>
</div>

<!--page-bottom-->
<div id="bottom-menu"><script language="javascript" type="text/javascript" src="/include/foot-nav.js"></script></div>
<div id="bottom-key">
  <div id="key-text"><!-- InstanceBeginEditable name="bottom-key" -->
      <!--treemenufootkey-->
	    Sothink is a trademark of SourceTec Software Co., LTD. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Flash is a trademark of Adobe. <br />
    Sothink Software: SWF Decompiler, DHTML Menu, SWF Quicker, SWF Easy, SWF to Video Converter, FlashVideo Converter, iPod Video Converter, DVD to iPod Converter, DVD Ripper, DVD EZWorkshop  <!-- InstanceEndEditable -->
    <script language="javascript" type="text/javascript" src="http://js.users.51.la/1608115.js"></script>
<noscript><a href="http://www.51.la/?1608115" target="_blank"><img alt="&#x6211;&#x8981;&#x5566;&#x514D;&#x8D39;&#x7EDF;&#x8BA1;" src="http://img.users.51.la/1608115.asp" style="border:none" /></a></noscript></div>
</div>
<div id="bottom-menu"><a href="http://www.swf-decompiler.com/">Flash Decompiler</a> | <a href="http://mac.sothink.com/">Flash Decompiler Mac</a> | <a href="http://www.swf-to-fla.com/">Convert SWF to FLA</a> | <a href="http://www.dhtml-menu-builder.com/">JavaScript Menu</a> | <a href="http://flash-animation-maker.com/">Flash Banner Maker</a> | <a href="http://www.logo-maker.net/">Logo Design Resources</a></div>
</body>
<!-- InstanceEnd --></html>
