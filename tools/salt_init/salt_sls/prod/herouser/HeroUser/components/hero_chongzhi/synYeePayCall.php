<?php 
ignore_user_abort(true);
set_time_limit(0);
include(dirname(dirname(dirname(__FILE__))) . '/config.php');
include(dirname(dirname(dirname(__FILE__))) . '/includes/class_mysql.php');
include(dirname(dirname(dirname(__FILE__))) . '/includes/class_common.php');	
require(dirname(dirname(dirname(__FILE__))) .'/includes/class_memcacheAdapter.php');

include(dirname(__FILE__) . '/YeePayCommon.php');
$common = new heroCommon;	
$db = new dbstuff;
$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname'],'utf8');
$db->query("set names 'utf8'");

$mc = new MemcacheAdapter_Memcached;	
$mc->addServer($MemcacheList[0], $Memport);

$p2_Order = $_GET['p2_Order'];
$p3_Amt   = $_GET['p3_Amt'];
$p4_verifyAmt = $_GET['p4_verifyAmt'];
$p5_Pid = $_GET['p5_Pid'];
$p6_Pcat = $_GET['p6_Pcat'];
$p7_Pdesc = $_GET['p7_Pdesc'];
$p8_Url = $_GET['p8_Url'];
$pa_MP  = $_GET['pa_MP'];
$pa7_cardAmt = $_GET['pa7_cardAmt'];
$pa8_cardNo = $_GET['pa8_cardNo'];
$pa9_cardPwd = $_GET['pa9_cardPwd'];
$pd_FrpId = $_GET['pd_FrpId'];
$pz_userId = $_GET['pz_userId'];
$pz1_userRegTime = date("Y-m-d H:i:s", $_GET['pz1_userRegTime']);

$ret_value = annulCard($p2_Order,
					   $p3_Amt,
					   $p4_verifyAmt,
					   $p5_Pid,
					   $p6_Pcat,
					   $p7_Pdesc,
					   $p8_Url,
					   $pa_MP,
					   $pa7_cardAmt,
					   $pa8_cardNo,
					   $pa9_cardPwd,
					   $pd_FrpId,
					   $pz_userId,
					   $pz1_userRegTime);

if($ret_value['status'] == 0) {
	$updateOrd['hmac'] = $ret_value['hmac'];
	$whereOrd['orderNo'] = $p2_Order;
	$common->updatetable('yeepay_ord', $updateOrd, $whereOrd);
			
	//$returnValue['status'] = 0;
}