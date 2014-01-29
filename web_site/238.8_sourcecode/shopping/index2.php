<?php
   include "conn.php";
   $ProductID = $_GET['ProductID'];
   $sql = "select * from `product` where ProductID = '$ProductID'";
   $result = mysql_query($sql);
   $rows = mysql_fetch_array($result);
?>
<html><!-- InstanceBegin template="/Templates/normal-2004.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<!-- InstanceBeginEditable name="doctitle" -->
<title>硕思软件——支付</title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="docmeta" -->
<meta name="description" content="Download shareware including Flash movie maker, SWF Editor, SWF Decompiler, SWF to FLA Converter, DHTML menu tool and free HTML editor flash albums, flash buttons, flash banners, flash animation software  with samples, templates, and tutorials.">
<meta name="KEYWORDs" content="download, shareware, freeware, dhtml menu, html editor, flash decompiler, swf decompiler, swf editor, swf maker, swf to fla, flash software, flash animation, animation software, flash movies, movie makers, free,flash templates,flash buttons,flash albums,flash tutorials,text effects,animated effects,sothink, sourcetec software">

<style type="text/css">
<!--
.style2 {color: #FF9900}
-->
</style>
<!-- InstanceEndEditable -->
<link href="/enstyle.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="JavaScript1.2" src="/image/title/stm31.js"></script>
</head>
<body bgcolor="#FFFFFF" topmargin="0" leftmargin="0" style="background-image: url(/image/back.gif); background-repeat: repeat-x; background-position: top;"><!-- #BeginLibraryItem "/Library/title2003.lbi" --><table width="775" height="25" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td valign="bottom" class="text_12px"><font color="#6666CC"> 制作动画影片，分解动画，制作动态网页菜单，反编译SWF,将SWF导出成FLA,  自由编辑HTML, SWF编辑器.</font></td>
  </tr>
</table>
<table width="775" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#4B6ABA">
  <tr> 
    <td><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="775" height="50">
        <param name="movie" value="/image/logoswf.swf">
        <param name=quality value=high>
		<param name="loop" value="false">
		<param name="menu" value="false">
        <embed src="/image/logoswf.swf" quality=high pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="775" height="50"></embed> 
      </object></td>
  </tr>
</table>
<table width="775" height="21" border="0" align="center" cellpadding="0" cellspacing="0" >
  <tr> 
    <td width="10" valign="top" bgcolor="#E1E8F9"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="775" height="60">
        <param name="movie" value="/image/menu.swf">
        <param name="quality" value="high">
		<param name="loop" value="false">
		<param name="menu" value="false">
        <embed src="/image/menu.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="775" height="60"></embed></object></td>
  </tr>
</table>
<!-- #EndLibraryItem --><table width="775"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="1"><!-- InstanceBeginEditable name="title" --><!-- InstanceEndEditable --></td>
      </tr>
      <tr>
        <td><table width="775"  border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="10"><img src="/image/back_content_1.GIF" width="10" height="36"></td>
                    <td width="756" background="/image/back_content_2.GIF"><p class="title_11px"><strong><!-- InstanceBeginEditable name="Top" --><a href="index.htm">&#39318;&#39029;</a> | <span class="style2">支付</span> <!-- InstanceEndEditable --></strong></p></td>
                    <td width="9"><img src="/image/back_content_3.GIF" width="9" height="36"></td>
                  </tr>
              </table></td>
            </tr>
            <tr>
              <td valign="top" bgcolor="#FAFDFD"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td valign="top"><table width="775"  border="0" align="center" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="6" valign="top" background="/image/back_content_12_2.GIF"><img src="/image/back_content_12.GIF" width="6" height="139"></td>
                        <td valign="top">
						<table width="100%"  border="0" cellpadding="0" cellspacing="0" style="background-image: url(/image/back_content.gif); background-repeat: repeat-x; background-position: top;">
                          <tr>
                            <td height="5" colspan="3"></td>
                          </tr>
						  <tr>
                            <td width="10" height="10"><img src="/image/round_table_1.gif" width="10" height="10"></td>
                            <td><img src="/image/round_table_2.gif" width="744" height="10"></td>
                            <td width="10"><img src="/image/round_table_3.gif" width="10" height="10"></td>
                          </tr>
                          <tr>
                            <td colspan="3"><table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#FAFDFD">
                                <tr>
                                  <td width="1" bgcolor="#CCCCCC"><img src="/image/dot.gif"></td>
                                  <td width="15">&nbsp;</td>
                                  <td><!-- InstanceBeginEditable name="Main" --><form id="payform" name="payform" method="post" action="/shopping/alipay/index.php">
  <table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td height="20" colspan="2">请选择支付方式：</td>
    </tr>
    <tr>
      <td width="54" height="20">&nbsp;</td>
      <td width="646"><input name="pay" type="radio"  onclick="javascript:payform.action='/shopping/alipay/index.php'" value="zfb" checked />
        支付宝支付</td>
    </tr>
    <tr>
      <td height="20">&nbsp;</td>
      <td height="20"><input name="pay" type="radio" value="kq" onClick="javascript:payform.action='/shopping/99bill/send.php'" />
        快钱支付</td>
    </tr>
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
</form>
                         
                                  <!-- InstanceEndEditable --></td>
                                  <td width="15">&nbsp;</td>
                                  <td width="1" bgcolor="#CCCCCC"><img src="/image/dot.gif"></td>
                                </tr>
                            </table></td>
                          </tr>
                          <tr>
                            <td width="10"><img src="/image/round_table_6.gif" width="10" height="10"></td>
                            <td><img src="/image/round_table_7.gif" width="744" height="10"></td>
                            <td width="10"><img src="/image/round_table_8.gif" width="10" height="10"></td>
                          </tr>
                          <tr>
                            <td height="5" colspan="3"></td>
                            </tr>
                        </table></td>
                        <td width="5" valign="top" background="/image/back_content_13_2.GIF"><img src="/image/back_content_13.GIF" width="5" height="139"></td>
                      </tr>
                    </table>
                    </td>
                  </tr>
              </table></td>
            </tr>
            <tr>
              <td valign="top" bgcolor="#FFFFFF"></td>
            </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<!-- #BeginLibraryItem "/Library/bottom2003.lbi" --><table width="775" border="0" cellpadding="0" align="center" nowrap cellspacing="0">
  <tr> 
    <td height="1" align="left" bgcolor="#A5AEB5"></td>
  </tr>
  <tr> 
    <td height="50" align="left" valign="middle" bgcolor="ECEFF3">
<table width="84%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr> 
          <td><div align="center"><a href="/">首页</a> | <a href="/contact.htm">联系我们</a> | <a href="/sitemap.htm">站内地图</a> | <a href="/friendlink.htm"> 友情链接 </a> | <a href="/career.htm" target="_blank">招贤纳士</a><br>
              武汉硕思软件公司 <a href="mailto:webmaster@sothink.com.cn">webmaster@sothink.com.cn</a></div></td>
        </tr>
      </table>
      
    </td>
  </tr>
</table>
<!-- #EndLibraryItem --></body>
<!-- InstanceEnd --></html>

