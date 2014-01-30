<?php
require_once("class/alipay_notify.php");
include(dirname(dirname(dirname(__FILE__))) . '/config.php');
if($_SC['domain'] == '10.144.132.230') {
	require_once("alipay_config2.php");
} else {
	require_once("alipay_config.php");
}
//include(dirname(dirname(dirname(__FILE__))) .'/lang/components/alipay/lang.php');
require(dirname(dirname(dirname(__FILE__))) .'/configs/ConfigLoader.php');

//构造通知函数信息
$alipay = new alipay_notify($partner,$sec_id,$_input_charset);

//计算得出通知验证结果
$verify_result = $alipay->return_verify();

$result = '';
//验签成功
if($verify_result) {
	
    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
    $mydingdan	= $_GET['out_trade_no'];		//外部交易号
	$myresult	= $_GET['result'];				//订单状态，是否成功
	$mytrade_no	= $_GET['trade_no'];			//交易号
	
	//判断交易是否成功
    /*if($_GET['result'] == 'success') {
		echo "交易成功！<br> 订单号：{$mydingdan} <br> 支付宝交易号：{$mytrade_no}";		
    }  else {
      echo $_GET['result'];
    }*/
}
else {

    //验签失败
    //echo "fail";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,minimum-scale=1.0, maximum-scale=2.0"/>
<title><?php echo $alipay_lang['recharge_success']; ?></title>
</head>
<style type="text/css">
body{padding:0px; margin:0px; font-size:13px; background-color:#331509; color:#4f1a00;}
p,span,ul,h3{padding:0px; margin:0px;}
ul li{list-style:none;}
img{border:0px;}
a{color:#4f1a00; text-decoration:none;}
.Wrap{width:100%; height:auto; margin:0 auto;}
.Header{background:url(Images/bg.jpg) repeat-x; width:100%; height:46px; float:left; text-align:center;}
.Header span{width:48px; height:46px; text-align:center;}
.Main{background-color:#fcd59a; border:3px solid #e6a731; width:96.5%; height:auto; margin:0px 0px 10px 1%; float:left; padding-bottom:10px;}
.Tips{width:100%; height:30px; line-height:30px; margin-left:6px;}
.CZ{width:auto; height:auto; margin:6px 6px 0 6px;}
.ListTable{float:left; background-color:#fef0de; border:1px solid #cf7a27; margin-bottom:1px;}
.jine{line-height:22px; text-align:center; margin-bottom:10px; margin-top:5px;}
.green{color:#399b03;}
.tips2{margin:10px 6px;}
.tips2 span{ color:#ff0000;}
h3{margin:10px 0;}
.czleft{margin:0 5px 5px 0;}
.box{width:90%; height:19px; line-height:19px; margin-bottom:5px;}
.fhbtn{width:100%; height:28px; float:left; text-align:center; margin-bottom:10px;}
.fhbtn a{width:76px; height:28px; margin:0 auto; display:block;}
.qd{background:url(Images/qd.jpg) no-repeat; width:50px; height:28px; border:0px; margin-bottom:5px;}
.ddh{margin:0 6px 10px 8px; line-height:22px;}
.ddh span{white-space:normal; word-break:break-all;}
</style>
<body>
<div class="Wrap">
	<div class="Header"><span><img src="Images/cz.jpg" width="48" height="46" /></span></div>
    <div class="Main">
        <div class="CZ">
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="ListTable">
          <tr>
            <td align="center"><h3><?php echo $alipay_lang['recharge_success']; ?></h3></td>
          </tr>
          <tr>
          	<td><div class="ddh"><?php echo $alipay_lang['ordno']; ?><span><?php echo $mydingdan ?></span><br /><?php echo $alipay_lang['alipay_tradeno']; ?><span><?php echo $mytrade_no ?></span><br /><?php echo $alipay_lang['remember_msg']; ?></div></td>
          </tr>
          <tr>
            <td align="center"><div class="fhbtn"><!--<a href="<?php echo $merchant_url ?>">--><a href="http://q.jofgame.com/Index.html"><img src="Images/fhyx.jpg" width="76" height="28" /></a></div></td>
          </tr>
        </table>
        </div>
    </div>
</div>
</body>
</html>
