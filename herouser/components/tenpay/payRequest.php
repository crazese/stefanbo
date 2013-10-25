<?php
@define('IN_HERO', TRUE);
define('D_BUG', '0');
$_REQUEST['client'] = 0;
define('S_ROOT', dirname(dirname(dirname(__FILE__))) . '/');
include(dirname(dirname(dirname(__FILE__))) . '/includes/class_mysql.php');
include(dirname(dirname(dirname(__FILE__))) . '/includes/class_common.php');	
include(dirname(dirname(dirname(__FILE__))) . '/config.php');
//include(dirname(dirname(dirname(__FILE__))) .'/lang/components/tenpay/lang.php');
require(dirname(dirname(dirname(__FILE__))) .'/configs/ConfigLoader.php');

$common = new heroCommon;	
$db = new dbstuff;
$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname'],'utf8');
$db->query("set names 'utf8'");

require_once ("classes/RequestHandler.class.php");
require ("classes/client/ClientResponseHandler.class.php");
require ("classes/client/TenpayHttpClient.class.php");
if($_SC['domain'] == '10.144.132.230') {
	include(dirname(__FILE__) . '/config2.php');
} else {
	include(dirname(__FILE__) . '/config.php');
}

//4位随机数
$randNum = rand(10000, 99999);

//订单号，此处用时间加随机数生成，商户根据自己情况调整，只要保持全局唯一就行
date_default_timezone_set('PRC');
$out_trade_no = "qjsh_" . date("YmdHis") . $randNum;

/* 创建支付请求对象 */
$reqHandler = new RequestHandler();
$reqHandler->init();
$reqHandler->setKey($key);
//设置初始化请求接口，以获得token_id
$reqHandler->setGateUrl($api_init);


$httpClient = new TenpayHttpClient();
//应答对象
$resHandler = new ClientResponseHandler();
//----------------------------------------
//设置支付参数 
//----------------------------------------
$pid = isset($_GET["qjsh_pid"]) ? $_GET["qjsh_pid"] : '';
$total_fee = isset($_GET["amt"]) ? $_GET["amt"] : 0;
$serverid = isset($_GET["qjsh_sid"]) ? $_GET["qjsh_sid"] : '';
if($pid == '' || $total_fee == 0 || $serverid == '')  die($tenpay_lang['param_err']);

if($serverid == 'sy01') {
        $notify_url = $notify_domain;
        $call_back_url = $call_back_url;
} else if($serverid == 'sy02') {
        $notify_url = $notify_domain_2f;
        $call_back_url = $call_back_url_2f;
} else if($serverid == 'sy03') {
        $notify_url = $notify_domain_3f;
        $call_back_url = $call_back_url_3f;
} else if($serverid == 'sy04') {
        $notify_url = $notify_domain_4f;
        $call_back_url = $call_back_url_4f;
} else if($serverid == 'sy05') {
        $notify_url = $notify_domain_5f;
        $call_back_url = $call_back_url_5f;
} else if($serverid == 'sy06') {
        $notify_url = $notify_domain_6f;
        $call_back_url = $call_back_url_6f;
}

$total_fee = $total_fee*100; // 单位为分

$reqHandler->setParameter("total_fee", $total_fee);  //总金额
$reqHandler->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);//客户端IP
$reqHandler->setParameter("ver", "2.0");//版本类型
$reqHandler->setParameter("bank_type", "0"); //银行类型，财付通填写0
$reqHandler->setParameter("callback_url", $call_back_url);//交易完成后跳转的URL
$reqHandler->setParameter("bargainor_id", $partner); //商户号
$reqHandler->setParameter("sp_billno", $out_trade_no); //商户订单号
$reqHandler->setParameter("notify_url", $notify_url);//接收财付通通知的URL，需绝对路径
$reqHandler->setParameter("desc", $goods_descr);
$reqHandler->setParameter("attach", $pid);


$httpClient->setReqContent($reqHandler->getRequestURL());

//后台调用
if($httpClient->call1()) {

	$resHandler->setContent($httpClient->getResContent());
	//获得的token_id，用于支付请求
	$token_id = $resHandler->getParameter('token_id');
	$reqHandler->setParameter("token_id", $token_id);
	
	// 建立订单	
	$sql = "insert into ol_tenpay_ord(playersid, ord_no, ord_amt) values($pid, '$out_trade_no', $total_fee/100)";
	$db->query($sql);

	//请求的URL
	$reqUrl = $api_gate.$token_id;
		
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width = device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta http-equiv="Cache-Control" content="max-age=0" forua="true"/>
<meta http-equiv="Cache-control" content="must-revalidate" />
<meta http-equiv="Cache-control" content="private" />
<meta http-equiv="Cache-control" content="no-cache" />
</head>
<style type="text/css">
body{padding:0px; margin:0px; height:100%; font-size:13px; background-color:#331509; color:#4f1a00;}
p,span,ul,h3,h4{padding:0px; margin:0px;}
ul li{list-style:none;}
img{border:0px;}
a{color:#4f1a00; text-decoration:none;}
html,form{height:100%;}
.Wrap{width:100%; height:100%; margin:0 auto;}
.Header{background:url(Images/bg.jpg) repeat-x; width:100%; height:46px; float:left; text-align:center;}
.Header span{width:48px; height:46px; text-align:center;}
.Main{background-color:#fcd59a; border:3px solid #e6a731; width:98%; height:90%; margin:0px 0px 1% 1%; float:left; padding-bottom:10px;}
.CZ{width:auto; height:auto; line-height:24px; margin:10px 6px 0 6px;}
.chongzhi{width:100%; height:99%; background-color:#ffefdf; border:1px solid #cf7a27; margin-bottom:1px; padding:5px 0;}
.tc_je{width:100%; height:auto; line-height:30px; text-align:center; margin-top:10px;}
.tc_je span{width:149px; height:30px; display:block; margin:0 auto; margin-bottom:10px;}
.tc_je span a{background:url(Images/tb_btn_09.jpg) no-repeat; width:149px; height:30px; text-align:center; line-height:30px; display:block;}
</style>
<body>
<title><?php echo $tenpay_lang['recharge_title'];?></title>
<div class="Wrap">
	<div class="Header"><span><img src="Images/cz1.jpg" width="48" height="46" /></span></div>
	<table border="0" cellspacing="0" cellpadding="0" class="Main">
    <tr><td valign="top">
      <div class="CZ">
		 <table width="100%" border="0" cellspacing="0" cellpadding="6" class="chongzhi">  
			  <tr>
				<td colspan="2" align="center"><?php echo $tenpay_lang['click_goto'];?>
                <div class="tc_je"><span><a href="<?php echo $reqUrl; ?>"><h4><?php echo $tenpay_lang['tenpay_wappay'];?></h4></a></span></div></td>
			  </tr>
		</table>
		</div>
	</td></tr></table>
<div>
</body>
</html>