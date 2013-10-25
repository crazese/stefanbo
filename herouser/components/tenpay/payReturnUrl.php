<?php
error_reporting(0);
define('S_ROOT', dirname(dirname(dirname(__FILE__))) . '/');
require_once ("./classes/ResponseHandler.class.php");
require_once ("./classes/WapResponseHandler.class.php");
include(dirname(dirname(dirname(__FILE__))) . '/config.php');
//include(dirname(dirname(dirname(__FILE__))) .'/lang/components/tenpay/lang.php');
require(dirname(dirname(dirname(__FILE__))) .'/configs/ConfigLoader.php');
if($_SC['domain'] == '10.144.132.230') {
	include(dirname(__FILE__) . '/config2.php');
} else {
	include(dirname(__FILE__) . '/config.php');
}

/* 创建支付应答对象 */
$resHandler = new WapResponseHandler();
$resHandler->setKey($key);

//判断签名
if($resHandler->isTenpaySign()) {

	//商户订单号
	$sp_billno = $resHandler->getParameter("sp_billno");
	$bargainor_id = $resHandler->getParameter("bargainor_id");
	//财付通交易单号
	$transaction_id = $resHandler->getParameter("transaction_id");
	//金额,以分为单位
	$total_fee = $resHandler->getParameter("total_fee");
	//支付结果
	$pay_result = $resHandler->getParameter("pay_result");

	if( "0" == $pay_result  ) {
		
		$string = "<br/>" . $tenpay_lang['return_msg1'] . "<br/>";
	
	} else {
		//当做不成功处理
		$string =  "<br/>" . $tenpay_lang['return_msg2'] . "<br/>";
	}
	
} else {
	$string =  "<br/>" . $tenpay_lang['return_msg3'] . "<br/>";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,minimum-scale=1.0, maximum-scale=2.0"/>
<title><?php echo $tenpay_lang['recharge_title2']; ?></title>
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
</style>
<body>
<div class="Wrap">
	<div class="Header"><span><img src="Images/cz1.jpg" width="48" height="46" /></span></div>
    <div class="Main">
        <div class="CZ">
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="ListTable">
          <tr>
            <td align="center"><h3><?php if( "0" == $pay_result  ) echo $tenpay_lang['recharge_success']; else echo $string?></h3></td>
          </tr>
          <tr>
          	<td><div class="ddh"><?php echo $tenpay_lang['ordno'];?><span><?php echo $sp_billno ?></span><br /><?php echo $tenpay_lang['alipay_tradeno'];?><br /><span><?php echo $transaction_id ?></span><br /><?php echo $tenpay_lang['remember_msg'];?></div></td>
          </tr>
          <tr>
            <td align="center"><div class="fhbtn"><a href="<?php echo $merchant_url ?>"><img src="Images/fhyx.jpg" width="76" height="28" /></a></div></td>
          </tr>
        </table>
        </div>
    </div>
</div>
</body>
</html>