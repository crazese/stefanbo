<?php
$_REQUEST['client'] = 0;
include(dirname(__FILE__) . '/class_mysql.php');
include(dirname(__FILE__) . '/class_common.php');	
include(dirname(dirname(__FILE__)) . '/config.php');

date_default_timezone_set('PRC');

//$payServer = array(0=>array('server'=>'10.144.130.154', 'port'=>'12004'), 1=>array('server'=>'10.144.131.26', 'port'=>'12004'));
$appid = 9;
/* $payServer = array(0=>array('server'=>'221.130.15.242', 'port'=>'16000'), 1=>array('server'=>'221.130.15.242', 'port'=>'16000'));
$appid = 6; */

$mc = new Memcache;
$mc->addServer($MemcacheList[0], $Memport);

$common = new heroCommon;	
$db = new dbstuff;
$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname'],'utf8');
$db->query("set names 'utf8'");

include(dirname(__FILE__). '/vip_control.php');

function syQuery($pid, $ucid) {
	global $mc, $common, $payServer, $appid;

    $username = $ucid;

    $seqnum = 'QJSH' . date('ymdHis') . rand(1, 9999999);
    $token = $mc->get(MC.$username.'_token');
    $pro = "{'seqnum':'$seqnum', 'func':'qcoins', 'token':'$token'}";
  
    $servernum = rand(0, 1);
    $serverIp = $payServer[$servernum]['server'];
    $serverPort = $payServer[$servernum]['port'];
    
    $fp = fsockopen($serverIp, $serverPort, $errno, $errstr, 1000);
    $header = "GET /appid=$appid HTTP/1.1\r\n";
    $header .= "Host:localhost \r\n";
    $header .= "Content-Length: ".strlen($pro)."\r\n";
    $header .= "Content-Type: text/html \r\n";
    $header .= "X-TOKEN: $token\r\n";
    $header .= "Connection: Close\r\n\r\n";
    $header .= $pro."\r\n";
    socket_set_timeout($fp, 1);
    fwrite($fp, $header);

    $str = null;
    $startTime = microtime(true);
    while(!feof($fp))
    {
            $str .= fgets($fp, 1024);

            $endTime = microtime(true);
            if(($endTime - $startTime) * 1000 > 200) {
                      $startPos = strrpos($str, "Content-Length");
                      $len = strlen('Content-Length: ');
                      $endPos = strrpos($str, "Content-Type");
                      $len = substr($str, $startPos + $len, $endPos - ($startPos + $len));
                      $cStartPos = strrpos($str, "{");
                      $cEndPos = $endPos = strrpos($str, "}");
                      $finalLen = strlen(substr($str, $cStartPos, $cEndPos - $cStartPos + 1));
                      if($len == $finalLen) break;
            }
    }
    fclose($fp);
    $str = str_replace('\r\n\r\n', '', stristr($str, "{ "));
	$result = json_decode($str);
	if(is_object($result)) {
		if($result->desc == 'success') {
			$returnValue['status'] = 0;
			$returnValue['coins'] = $result->balance;
		} else {
			$returnValue['status'] = 1021;
		}	
	} else {
		$returnValue['status'] = 1021;
	}
	return $returnValue;
	//file_put_contents('/tmp/syquery.txt', $result, FILE_APPEND);
}

function syPay($pid, $ucid, $coins) {
    global $mc, $common, $payServer, $appid, $db;

    $username = $ucid;

    $seqnum = 'QJSH' . date('ymdHis') . rand(1, 9999999);
    $token = $mc->get(MC.$username.'_token');
    $openid = $mc->get(MC.$username.'_openid');
   	$pro = "{'seqnum':'$seqnum', 'func':'ppay', 'token':'$token','propid':'1', 'price':'$coins', 'num':'1' }";
   	
   	$servernum = rand(0, 1);
    $serverIp = $payServer[$servernum]['server'];
    $serverPort = $payServer[$servernum]['port'];

    $fp = fsockopen($serverIp, $serverPort, $errno, $errstr, 1000);
    $header = "GET /appid=$appid HTTP/1.1\r\n";
    $header .= "Host:localhost \r\n";
    $header .= "Content-Length: ".strlen($pro)."\r\n";
    $header .= "Content-Type: text/html \r\n";
    $header .= "X-TOKEN: $token\r\n";
    $header .= "Connection: Close\r\n\r\n";
    $header .= $pro."\r\n";
    socket_set_timeout($fp, 1);
    fwrite($fp, $header);

    $str = null;
    $startTime = microtime(true);
    while(!feof($fp))
    {
            $str .= fgets($fp, 1024);

            $endTime = microtime(true);
            if(($endTime - $startTime) * 1000 > 200) {
                      $startPos = strrpos($str, "Content-Length");
                      $len = strlen('Content-Length: ');
                      $endPos = strrpos($str, "Content-Type");
                      $len = substr($str, $startPos + $len, $endPos - ($startPos + $len));
                      $cStartPos = strrpos($str, "{");
                      $cEndPos = $endPos = strrpos($str, "}");
                      $finalLen = strlen(substr($str, $cStartPos, $cEndPos - $cStartPos + 1));
                      if($len == $finalLen) break;
            }
    }
    fclose($fp);
    $str = str_replace('\r\n\r\n', '', stristr($str, "{ "));
	$result = json_decode($str);
	if(is_object($result)) {
		if($result->desc == 'success') {
			// 记录闪游返回的订单信息 { "code": "1", "desc": "success", "orderid": "ZK2012041215164008130000001", "seqnum": "QJSH1204121516407191" }
			$log_content = date('Y-m-d H:i:s') . '|' . $seqnum . '|' . '9' . '|' . '786' . '|' . $openid . '|' . '1' . '|' . $coins . '|' . '1' . '|' . $serverIp;
			$log_content = sprintf("%s \r\n", $log_content);
			file_put_contents(dirname(dirname(dirname(__FILE__))) . '/' .SYPAYLOG. '/QJSH'. date('Y') . '_' . date('m') . '_' . date('d') . 'ppay.log', $log_content, FILE_APPEND);
	
			// 获取玩家现有元宝数量
			$res = $db->query ( "SELECT ingot FROM " . $common->tname ( 'player' ) . "  WHERE playersid = '" . $pid . "' LIMIT 1" );
			$rows = $db->fetch_array ( $res );
			
			$oderNo = $result->orderid;		
			$addIngot = $coins;
			$updateRole['ingot'] = intval($rows['ingot']) + $addIngot;			
			$whereRole['playersid'] = $pid;
			$common->updatetable('player', $updateRole, $whereRole); 
			$common->updateMemCache(MC.$pid, $updateRole);
										
			// vip
			vipChongzhi($pid, intval($coins)/10, $addIngot, $oderNo);   // 添加最后的参数
	
			$log_content1 = date('Y-m-d H:i:s') . '|' . $seqnum . '|' . '9' . '|' . '786' . '|' . $openid . '|' . '1' . '|' . $coins . '|' . '1' . '|' . $oderNo;
			$log_content1 = sprintf("%s \r\n", $log_content1);
			file_put_contents(dirname(dirname(dirname(__FILE__))) . '/' .SYPAYLOG. '/QJSH'. date('Y') . '_' . date('m') . '_' . date('d') . 'dgoods.log', $log_content1, FILE_APPEND);
		}
	}

	//file_put_contents('/tmp/sypay.txt', $result, FILE_APPEND);
}

function _get($str){
	//$magic_quote = get_magic_quotes_gpc();
	if (isset($_REQUEST[$str]))	{		
		$val = $_REQUEST[$str];			
		if (!is_numeric($val)) {
			$val = mysql_escape_string($val);
		}
	} else {
		$val = null;
	}
    return $val;
}

$pid = _get('pid');
$ucid = _get('ucid');

$ret = syQuery($pid, $ucid);
if($ret['status']  == 0 && $ret['coins'] > 0) {	
	syPay($pid, $ucid, $ret['coins']);
}
