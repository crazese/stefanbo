<?php
include(dirname(__FILE__)."/db.php");

$uzone_token  = isset($_GET['uzone_token']) ? str_replace(' ','+',urldecode($_GET['uzone_token'])) : '';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,minimum-scale=1.0, maximum-scale=2.0"/>
<title>充值</title>
</head>
<style type="text/css">
body{padding:0px; margin:0px; font-size:13px; background-color:#331509; color:#4f1a00;}
p,span,ul{padding:0px; margin:0px;}
ul li{list-style:none;}
img{border:0px;}
a{color:#4f1a00; text-decoration:none;}
.Wrap{width:100%; height:auto; margin:0 auto;}
.Header{background:url(Images/bg.jpg) repeat-x; width:100%; height:46px; float:left; text-align:center;}
.Header span{width:48px; height:46px; text-align:center;}
.Main{background-color:#fcd59a; border:3px solid #e6a731; width:96.5%; height:auto; margin:0px 0px 10px 1%; float:left; padding-bottom:10px;}
.Tips{width:100%; height:30px; line-height:30px; margin-left:6px;}
.CZ{width:auto; height:auto; line-height:30px; margin:1px 6px 0 6px;}
.ListTable tr td span.czl{margin-left:5px; color:#4f1a00;}
.List{height:auto; line-height:63px; margin:1px 6px 0 6px; width:auto;}
.ListTable{float:left; background-color:#fef0de; border:1px solid #cf7a27; margin-bottom:1px;}
.ybimg{margin-left:5px;}
.ListTable tr td span{color:#399b03;}
.jtimg{margin-right:10px;}
.fhbtn{width:50px; height:28px; float:left; margin:6px 0 0 6px;}
</style>
<body>
<div class="Wrap">
	<div class="Header"><span><img src="Images/cz.jpg" width="48" height="46" /></span></div>
    <div class="Main">
    	<div class="Tips">小提示：请选择充值方式</div>
    	<!--<div class="Tips"><a href='history.php?uzone_token=<?php echo $uzone_token; ?>'>[U点兑换历史]</a>&nbsp;&nbsp;<a href='historyPay.php?uzone_token=<?php echo $uzone_token; ?>'>[充值卡充值记录]</a></div>-->
        <div class="CZ">
        <a href="yd.php?uzone_token=<?php echo $uzone_token ?>"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="ListTable">
          <tr>
            <td><span class="czl">移动充值卡</span></td>
            <td align="right"><img class="jtimg" src="Images/jt.jpg" width="13" height="10" /></td>
          </tr>
        </table></a>
        </div>
        <div class="CZ">
        <a href="lt.php?uzone_token=<?php echo $uzone_token ?>"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="ListTable">
          <tr>
            <td><span class="czl">联通充值卡</span></td>
            <td align="right"><img class="jtimg" src="Images/jt.jpg" width="13" height="10" /></td>
          </tr>
        </table></a>
        </div>
        <div class="List">
           	<a href='payU.php?uzone_token=<?php echo $uzone_token."&amount=10"?>'><table width="100%" border="0" cellspacing="0" cellpadding="0" class="ListTable">
              <tr>              	
                <td width="64"><img class="ybimg" src="Images/yb_1.jpg" width="53" height="53" /></td>
                <td>10元宝=<span>10U点</span></td>
                <td align="right"><img class="jtimg" src="Images/jt.jpg" width="13" height="10" /></td>
              </tr>
            </table></a>
            <a href='payU.php?uzone_token=<?php echo $uzone_token."&amount=20"?>'><table width="100%" border="0" cellspacing="0" cellpadding="0" class="ListTable">
              <tr>
                <td width="64"><img class="ybimg" src="Images/yb_2.jpg" width="53" height="53" /></td>
                <td>20元宝=<span>20U点</span></td>
                <td align="right"><img class="jtimg" src="Images/jt.jpg" width="13" height="10" /></td>
              </tr>
            </table></a>
            <a href='payU.php?uzone_token=<?php echo $uzone_token."&amount=50"?>'><table width="100%" border="0" cellspacing="0" cellpadding="0" class="ListTable">
              <tr>
                <td width="64"><img class="ybimg" src="Images/yb_3.jpg" width="53" height="53" /></td>
                <td>50元宝=<span>50U点</span></td>
                <td align="right"><img class="jtimg" src="Images/jt.jpg" width="13" height="10" /></td>
              </tr>
            </table></a>
            <a href='payU.php?uzone_token=<?php echo $uzone_token."&amount=100"?>'><table width="100%" border="0" cellspacing="0" cellpadding="0" class="ListTable">
              <tr>
                <td width="64"><img class="ybimg" src="Images/yb_4.jpg" width="53" height="53" /></td>
                <td>100元宝=<span>100U点</span></td>
                <td align="right"><img class="jtimg" src="Images/jt.jpg" width="13" height="10" /></td>
              </tr>
            </table></a>
            <a href='payU.php?uzone_token=<?php echo $uzone_token."&amount=200"?>'><table width="100%" border="0" cellspacing="0" cellpadding="0" class="ListTable">
              <tr>
                <td width="64"><img class="ybimg" src="Images/yb_5.jpg" width="53" height="53" /></td>
                <td>200元宝=<span>200U点</span></td>
                <td align="right"><img class="jtimg" src="Images/jt.jpg" width="13" height="10" /></td>
              </tr>
            </table></a>
            <a href="payU.php?uzone_token=<?php echo $uzone_token."&amount=500"?>"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="ListTable">
              <tr>
                <td width="64"><img class="ybimg" src="Images/yb_6.jpg" width="53" height="53" /></td>
                <td>500元宝=<span>500U点</span></td>
                <td align="right"><img class="jtimg" src="Images/jt.jpg" width="13" height="10" /></td>
              </tr>
            </table></a>    
        </div>
        <div class="fhbtn"><a href="<?php echo 'http://117.135.138.196/index.php?uzone_token=' . $uzone_token ?>"><img src="Images/fh.jpg" width="50" height="28" /></a></div>
    </div>
</div>
</body>
</html>
