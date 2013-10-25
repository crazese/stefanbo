<?php
@define('IN_HERO', TRUE);
define('D_BUG', '0');
$_REQUEST['client'] = 0;
include(dirname(dirname(dirname(__FILE__))) . '/includes/class_mysql.php');
include(dirname(dirname(dirname(__FILE__))) . '/includes/class_common.php');	
include(dirname(dirname(dirname(__FILE__))) . '/config.php');
//include(dirname(dirname(dirname(__FILE__))) .'/lang/components/alipay/lang.php');
require(dirname(dirname(dirname(__FILE__))) .'/configs/ConfigLoader.php');

$common = new heroCommon;	
$db = new dbstuff;
$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname'],'utf8');
$db->query("set names 'utf8'");

if($_SC['domain'] == '10.144.132.230') {
	require_once("alipay_config2.php");
} else {
	require_once("alipay_config.php");
}
require_once ("class/alipay_service.php");

/******************************alipay_wap_trade_create_direct**************************************/
$out_trade_no = getOrdNo();
$cashier_code = isset($_GET["id"]) ? $_GET["id"] : '';
$pid = isset($_GET["pid"]) ? $_GET["pid"] : '';
$total_fee = isset($_GET["amt"]) ? $_GET["amt"] : '';
$serverid = isset($_GET["sid"]) ? $_GET["sid"] : '';
if($pid == '' || $total_fee == '' || $serverid == '')  die('参数错误');

if($serverid == 'sy01') {		
	$notify_url = $req_domain . "?uri=$notify_url";
	$call_back_url = $req_domain . "?uri=$call_back_url";
} else if($serverid == 'sy02') { 
	$notify_url = $req_domain_2f . "?uri=$notify_url";
	$call_back_url = $req_domain_2f . "?uri=$call_back_url";
} else if($serverid == 'sy03') {
	$notify_url = $req_domain_3f . "?uri=$notify_url";
	$call_back_url = $req_domain_3f . "?uri=$call_back_url";
} else if($serverid == 'sy04') {
	$notify_url = $req_domain_4f . "?uri=$notify_url";
	$call_back_url = $req_domain_4f . "?uri=$call_back_url";
} else if($serverid == 'sy05') {
	$notify_url = $req_domain_5f . "?uri=$notify_url";
	$call_back_url = $req_domain_5f . "?uri=$call_back_url";
} else if($serverid == 'sy06') {
	$notify_url = $req_domain_6f . "?uri=$notify_url";
	$call_back_url = $req_domain_6f . "?uri=$call_back_url";
}

//构造要请求的参数数组，无需改动
$pms1 = array (
	"req_data"		=> '<direct_trade_create_req><subject>' . 'Q将水浒'.$total_fee*10 .$subject . '</subject><out_trade_no>' .
	$out_trade_no . '</out_trade_no><total_fee>' . $total_fee . "</total_fee><seller_account_name>" . $seller_email .
	"</seller_account_name><notify_url>" . $notify_url . "</notify_url><out_user>" . $out_user .
	"</out_user><merchant_url>" . $merchant_url . "</merchant_url><cashier_code>" . $cashier_code .
	"</cashier_code>" . "<call_back_url>" . $call_back_url . "</call_back_url></direct_trade_create_req>",
	"service"		=> $Service1,
	"sec_id"		=> $sec_id,
	"partner"		=> $partner,
	"req_id"		=> date("Ymdhms"),
	"format"		=> $format,
	"v"				=> $v
);

// 建立订单
$cashier = $cashier_code == '' ? 'alipay account' : $cashier_code;
$sql = "insert into ol_alipay_ord(playersid, ord_no, ord_amt, cashier_code) values($pid, '$out_trade_no', $total_fee, '$cashier')";
$db->query($sql);

//构造请求函数
$alipay = new alipay_service();

//调用alipay_wap_trade_create_direct接口，并返回token返回参数
$token = $alipay->alipay_wap_trade_create_direct($pms1);														
/***************************************************************************************************/
//file_put_contents('/tmp/zzz1.txt', $token);
/*********************************alipay_Wap_Auth_AuthAndExecute************************************/

//构造要请求的参数数组，无需改动
$pms2 = array (
	"req_data"		=> "<auth_and_execute_req><request_token>" . $token . "</request_token></auth_and_execute_req>",
	"service"		=> $Service2,
	"sec_id"		=> $sec_id,
	"partner"		=> $partner,
	"call_back_url" => $call_back_url,
	"format"		=> $format,
	"v"				=> $v
);
//file_put_contents('/tmp/zzz.txt', $cashier_code);
//调用alipay_Wap_Auth_AuthAndExecute接口方法，并重定向页面
if(strpos(strtolower($cashier_code), 'debit') !== false) {
	$link_url = $alipay -> alipay_Wap_Auth_AuthAndExecute($pms2, true);
	echo '<meta http-equiv="content-type" content="text/html;charset=utf-8">';	
	echo '<meta name="viewport" content="width = device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />';
	echo "<title>{$alipay_lang['recharge_title']}</title>";
	echo "<center><a href='$link_url'>{$alipay_lang['click_goto']}</a></center>";
}
else
	$alipay -> alipay_Wap_Auth_AuthAndExecute($pms2);

/***************************************************************************************************/

?>
