<?php
$mtime = explode(' ', microtime());
$_SGLOBAL['timestamp'] = $mtime[1];
$_SGLOBAL['supe_starttime'] = $_SGLOBAL['timestamp'] + $mtime[0];
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
header("content-type:text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
//基本文件
include(S_ROOT.'.././config.php');
include(S_ROOT.'.././includes/class_mysql.php');
include(S_ROOT.'.././includes/vip_control.php');
include(S_ROOT.'.././includes/class_common.php');
$common = new heroCommon;
$db = new dbstuff;
$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname'],'utf8');
$db->query("set names 'utf8'");
$insert['orderId'] = mysql_escape_string($_POST['orderId']);
//$insert['callbackInfo'] = mysql_escape_string($_POST['callbackInfo']);
$insert['createTime'] = time();
$checksql = "SELECT * FROM ".$common->tname('uc_client_payinfo');
$res_check = $db->query($checksql);
$checknum = $db->num_rows($res_check);
if ($checknum == 0) {
	$common->inserttable('uc_client_payinfo',$insert);
	//$sql = "SELECT * FROM ".$common->tname('uc_payinfo')." WHERE orderId = '".$insert['orderId']."' && callbackInfo = '".$insert['callbackInfo']."' && processed = '0' LIMIT 1";
	$sql = "SELECT * FROM ".$common->tname('uc_payinfo')." WHERE orderId = '".$insert['orderId']."' && processed = '0' && orderStatus = 'S' LIMIT 1";
	$result = $db->query($sql);
	$rows = $db->fetch_array($result);
	if (!empty($rows)) {
    	$yb = floor($rows['amount'] * 10);
    	//$sql_user = "SELECT a.userid,b.playersid,b.ingot FROM ".$common->tname('user')." a,".$common->tname('player')." b WHERE a.username = '".$rows['ucid']."' && a.userid = b.userid LIMIT 1";
    	$sql_user = "SELECT userid,playersid,ingot,player_level FROM ". $common->tname('player')." WHERE ucid = '".$insert['ucid']."' LIMIT 1";
    	$res_user = $db->query($sql_user);
    	$rows_user = $db->fetch_array($res_user);
    	if (!empty($rows_user)) {
    		$playersid = $rows_user['playersid'];
    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
    		$db->query($sql_p);    			
    		vipChongzhi($playersid, $rows['amount'], $yb, $rows['orderId']);
    		$db->query("UPDATE ".$common->tname('uc_payinfo')." SET processed = 1 WHERE orderId = '".$rows['orderId']."' LIMIT 1");
    		$db->query("UPDATE ".$common->tname('uc_client_payinfo')." SET processed = 1 WHERE orderId = '".$rows['orderId']."' LIMIT 1");
    		$value['status'] = 0;
    		$value['yb'] = $rows_user['ingot'] + $yb;
    		$value['hqyb'] = $yb;
    	} else {
    		$value['status'] = 1001;
    	}		
	} else {
		$value['status'] = 1001;
	}
} else {
	$value['status'] = 1001;
}
echo json_encode($value);