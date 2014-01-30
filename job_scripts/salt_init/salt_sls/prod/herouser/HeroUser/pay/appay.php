<?php
@define('IN_HERO', TRUE);
define('D_BUG', '1');
$mtime = explode(' ', microtime());
$_SGLOBAL['timestamp'] = $mtime[1];
$_SGLOBAL['supe_starttime'] = $_SGLOBAL['timestamp'] + $mtime[0];
define('S_ROOT', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
header("content-type:text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
//基本文件
include(dirname(dirname(__FILE__)) .'/config.php');
include(dirname(dirname(__FILE__)) .'/includes/class_mysql.php');
require(dirname(dirname(__FILE__)).'/configs/ConfigLoader.php');
require(dirname(dirname(__FILE__)).'/model/PlayerMgr.php');
require(dirname(dirname(__FILE__)).'/includes/class_memcacheAdapter.php');
$mc = new MemcacheAdapter_Memcached;	
$mc->addServer($MemcacheList[0], $Memport);
include(dirname(dirname(__FILE__)).'/includes/class_common.php');

$common = new heroCommon;
$db = new dbstuff;
$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname'],'utf8');
$db->query("set names 'utf8'");
include(dirname(dirname(__FILE__)).'/includes/vip_control.php');
//插入appstore错误订单数据
function errorInfo($uid,$errorno,$errormsg) {
	global $db, $common;
	$nowTime = time();
	$errormsg = mysql_escape_string($errormsg);
	$sql = "INSERT INTO ".$common->tname('app_payinfo_error')."(uid,errorno,errormsg,errorDate) VALUES ('$uid','$errorno','$errormsg','$nowTime')";
	$db->query($sql);
}

//appstore订单数据验证
function validateReceipt($receipt, $uid, $isSandbox = false)
{
	if ($isSandbox) {
		$endpoint = 'https://sandbox.itunes.apple.com/verifyReceipt';
		//print "Environment: Sandbox (use 'sandbox' URL argument to toggle)<br />";
	}
	else {
		$endpoint = 'https://buy.itunes.apple.com/verifyReceipt';
		//print "Environment: Production (use 'sandbox' URL argument to toggle)<br />";
	}

	$postData = json_encode(
		array('receipt-data' => $receipt)
	);
   

	$ch = curl_init($endpoint);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

	$response = curl_exec($ch);
	$errno    = curl_errno($ch);
	$errmsg   = curl_error($ch);
	curl_close($ch);

	if ($errno != 0) {
		//throw new Exception($errmsg, $errno);
		errorInfo($uid,$errno,$errmsg.'['.$isSandbox.']');
		//echo json_encode(array('status'=>99999,'exception'=>'访问错误，错误代码:'.$errno.',错误内容：'.$errmsg));
		//return false;
		if (LANG_FLAG == 'cn') {
			return array('status'=>99999,'exception'=>'访问错误，错误代码:'.$errno.',错误内容：'.$errmsg);
		} else {
			return array('status'=>99999,'exception'=>'訪問錯誤，錯誤代碼:'.$errno.',錯誤內容：'.$errmsg);
		}
	}

	$data = json_decode($response);

	if (!is_object($data)) {
		errorInfo($uid,99999,'Invalid response data'.'['.$isSandbox.']');
		//echo json_encode(array('status'=>99999,'exception'=>'Invalid response data'));
		//return false;
		return array('status'=>99999,'exception'=>'Invalid response data');
		//throw new Exception('Invalid response data');
	}

	if (!isset($data->status) 
	|| $data->status != 0) 
	{
		//print 'Status Code: '. $data->status . '<br/>';
		//throw new Exception('Invalid receipt');
		if (isset($data->status)) {
			$errorstatus = $data->status;
		} else {
			$errorstatus = 99999;
		}
		if (!empty($data->exception)) {
			$exception = $data->exception;
		} else {
			$exception = '信息验证失败';
		}
		errorInfo($uid,99999,'Invalid receipt: '.$errorstatus.' : '.$exception.'['.$isSandbox.']');		
		//echo json_encode(array('status'=>$errorstatus,'exception'=>$exception));
		return array('status'=>$errorstatus,'exception'=>$exception);
		//return false;
	}
    
	if (!empty($data->receipt->app_item_id)) {
		$app_item_id = $data->receipt->app_item_id;
	} else {
		$app_item_id = '';
	}
	if (!empty($data->receipt->version_external_identifier)) {
		$version_external_identifier = $data->receipt->version_external_identifier;
	} else {
		$version_external_identifier = '';
	}	
	
	return array(
		'original_purchase_date_pst' =>  $data->receipt->original_purchase_date_pst,
		'original_transaction_id' =>  $data->receipt->original_transaction_id,
		'original_purchase_date_ms' =>  $data->receipt->original_purchase_date_ms,
		'transaction_id'  =>  $data->receipt->transaction_id,
		'quantity' => $data->receipt->quantity,
		'product_id' => $data->receipt->product_id,
		'bvrs' => $data->receipt->bvrs,
		'purchase_date_ms' =>  $data->receipt->purchase_date_ms,
		'purchase_date' =>  $data->receipt->purchase_date,
		'original_purchase_date' =>  $data->receipt->original_purchase_date,
		'purchase_date_pst' =>  $data->receipt->purchase_date_pst,
		'bid' => $data->receipt->bid,
		'item_id' => $data->receipt->item_id,
	    'app_item_id' => $app_item_id,
	    'version_external_identifier' => $version_external_identifier
	);
}
function _get($str){
	//$magic_quote = get_magic_quotes_gpc();
	if (isset($_POST[$str]))	{		
		$val = $_POST[$str];	
	} else {
		$val = null;
	}
    return $val;
}
function getProductInfo($productid,$apple_ID) {
	global $db, $common;
	$sql = "select * from ".$common->tname('app_productInfo')." where productID = '$productid' && apple_ID = '$apple_ID' limit 1";
	$result = $db->query($sql);
	$rows = $db->fetch_array($result);
	if (!$rows) {
		return false;
	} else {
		return array('productid'=>$rows['productID'],'yb'=>$rows['yb'],'mc'=>$rows['mc'],'jg'=>$rows['jg'],'sfgq'=>$rows['sfgq']);
	}
}
function writelog($request,$return,$level=0,$userid=0) {
	global $common,$mc,$_HTTPSQS, $_LOG_INFO;	
	$nowTime = time();	
	$letters_trade['request_url'] = $request;
    $letters_trade['create_time'] = $nowTime;	
    $letters_trade['level'] = $level;
	$letters_trade['userid'] = $userid;
	$letters_trade['result'] = $return;
	$log_path = $_LOG_INFO['path'] . $_LOG_INFO['prefix'] . date('Y_m_d_H',$nowTime).'_'.intval($nowTime/$_LOG_INFO['split_t']);
	$log_json_value = json_encode($letters_trade);
	$flag = false;
	for($i=1; $i<=5; $i++){
		//$logHandle = fopen($log_path, 'a');
		$logHandle = fopen($log_path. "_$i", 'a');
		if(flock($logHandle, LOCK_EX|LOCK_NB)){
			$w_long = fwrite($logHandle, $log_json_value."\n");
			if(0 < $w_long){
				$flag = true;
				fclose($logHandle);
				break;
			}
		}
		fclose($logHandle);
	}
	if(!$flag){
		$log['logValue'] = $log_json_value;
		$common->inserttable('bck_log', $log);
	} else{
		syslog(LOG_ALERT, 'game log write log need userid');
	}
}
$loginKey = _get('loginKey');
$uid = $info['uid'] = _get('uid'); 
if (empty($loginKey)) {
	errorInfo($uid,99997,'loginKey为空');
	if (LANG_FLAG == 'cn') {
		echo json_encode(array('status'=>99999,'exception'=>'loginKey为空'));
	} else {
		echo json_encode(array('status'=>99999,'exception'=>'loginKey為空'));
	}
	exit; 
}
session_id($loginKey);
session_start();
if (!empty($_SESSION)){
	$userLoginInfo = $mc->get(MC.$_SESSION['ucid'].'_session');
	if ($uid != $_SESSION['userid'] || empty($_SESSION['userid'])) {
		errorInfo($uid,99999,'非法用户');
		if (LANG_FLAG == 'cn') {
			echo json_encode(array('status'=>99999,'exception'=>'非法用户'));
		} else {
			echo json_encode(array('status'=>99999,'exception'=>'非法用戶'));
		}
		exit; 	
	} elseif ($userLoginInfo != $loginKey) {
		errorInfo($uid,99997,'用户在其它地方登录!');
		if (LANG_FLAG == 'cn') {
			echo json_encode(array('status'=>99997,'exception'=>'用户在其它地方登录!'));
		} else {
			echo json_encode(array('status'=>99997,'exception'=>'用戶在其它地方登錄!'));
		}
		exit; 		
	} else {
		if (empty($_SESSION['playersid'])) {
			errorInfo($uid,99998,'该页面信息已过期，请重新进入游戏1！');
			if (LANG_FLAG == 'cn') {
				echo json_encode(array('status'=>99998,'exception'=>'该页面信息已过期，请重新进入游戏！'));
			} else {
				echo json_encode(array('status'=>99998,'exception'=>'該頁面資訊已過期，請重新進入遊戲！'));
			}
			exit; 					
		}
	}				
} else {
	errorInfo($uid,99998,'该页面信息已过期，请重新进入游戏3！');
	if (LANG_FLAG == 'cn') {
		echo json_encode(array('status'=>99998,'exception'=>'该页面信息已过期，请重新进入游戏！'));
	} else {
		echo json_encode(array('status'=>99998,'exception'=>'該頁面資訊已過期，請重新進入遊戲！'));
	}
	exit; 
}
$receipt   = _get('receipt');
//$isSandbox = true;   
if (empty($receipt)) {
	errorInfo($uid,99997,'APP验证参数未传');
	if (LANG_FLAG == 'cn') {
		echo json_encode(array('status'=>99999,'exception'=>'APP验证参数未传'));
	} else {
		echo json_encode(array('status'=>99999,'exception'=>'APP驗證參數未傳'));
	}
	exit;
}
if (empty($uid)) {
	errorInfo($uid,99997,'用户ID参数未传');
	if (LANG_FLAG == 'cn') {
		echo json_encode(array('status'=>99999,'exception'=>'用户ID参数未传'));
	} else {
		echo json_encode(array('status'=>99999,'exception'=>'用戶ID參數未傳'));
	}
	exit;	
}
$infoData = validateReceipt($receipt, $uid, false);
if (isset($infoData['status'])) {
	$infoData = validateReceipt($receipt, $uid, true);
}
if (isset($infoData['status'])) {
	echo json_encode($infoData);
	exit;
}
$info['original_purchase_date_pst'] = $infoData['original_purchase_date_pst'];
$info['original_transaction_id'] = $infoData['original_transaction_id'];
$info['original_purchase_date_ms'] = $infoData['original_purchase_date_ms'];
$info['transaction_id'] = $infoData['transaction_id'];
$info['quantity'] = $infoData['quantity'];
$info['product_id'] = $infoData['product_id'];
$info['bvrs'] = mysql_escape_string($infoData['bvrs']);
$info['purchase_date_ms'] = $infoData['purchase_date_ms'];
$info['purchase_date'] = $infoData['purchase_date'];
$info['original_purchase_date'] = $infoData['original_purchase_date'];
$info['purchase_date_pst'] = $infoData['purchase_date_pst'];
$info['bid'] = mysql_escape_string($infoData['bid']);
$info['item_id'] = $infoData['item_id'];
if (!empty($infoData['app_item_id'])) {
	$info['app_item_id'] = $infoData['app_item_id'];
} else {
	$info['app_item_id'] = '';
}
if (!empty($infoData['version_external_identifier'])) {
	$info['version_external_identifier'] = $infoData['version_external_identifier'];
} else {
	$info['version_external_identifier'] = '';
}
if (!empty($info)) {
	/*判断是否为合法产品信息(产品信息后期修正)具体方式待定*/
	/*if ($info['item_id'] != 527948157) {
		errorInfo($uid,99997,'产品信息有误（'.$info['product_id'].','.$info['item_id'].'）');
		echo 'FAILED';
		exit;
	}*/
	$productinfo = getProductInfo($info['product_id'],$info['item_id']);
	if (empty($productinfo)) {
		errorInfo($uid,99999,'产品信息有误（'.$info['product_id'].','.$info['item_id'].'）');
		if (LANG_FLAG == 'cn') {
			echo json_encode(array('status'=>99999,'exception'=>'产品信息有误'));
		} else {
			echo json_encode(array('status'=>99999,'exception'=>'產品資訊有誤'));
		}
		exit;			
	} else {
		$yb = $productinfo['yb'];
		$info['product_name'] = $productinfo['mc'];
		$info['price'] = $productinfo['jg'];
		$info['sfgq'] = $productinfo['sfgq'];
	}
    $checkSql = "SELECT * FROM ".$common->tname('app_payinfo')." WHERE transaction_id = '".$info['transaction_id']."' LIMIT 1";
    $res_check = $db->query($checkSql);
    $checknum = $db->num_rows($res_check);
    if ($checknum == 0) {
    	$common->inserttable('app_payinfo',$info);
    } else {
    	echo json_encode(array('status'=>0,'duplicated'=>1));  
    	exit;
    }		
    //$yb = floor($info['quantity']);
    if ($yb > 0) {
    	$sql_user = "SELECT a.userid,b.playersid,b.ingot,b.player_level FROM ".$common->tname('user')." a,".$common->tname('player')." b WHERE a.userid = '".$uid."' && a.userid = b.userid LIMIT 1";
    	$res_user = $db->query($sql_user);
    	$rows_user = $db->fetch_array($res_user);
    	if (!empty($rows_user)) {
    		$playersid = $rows_user['playersid'];
    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
    		$db->query($sql_p);    			
    		vipChongzhi($playersid, $info['price'], $yb, $info['transaction_id'],'',true,false,'');
    	}
    }
    if ($productinfo['sfgq'] == 3) {
    	$czType = 'sgapple';
    } else {
    	$czType = 'apple';
    }
    writelog('app.php?task=chongzhi&option=pay&type='.$czType.'&userId='.$uid,json_encode(array('orderId'=>$info['transaction_id'],'ucid'=>$uid,'payWay'=>0,'amount'=>$info['price'],'orderStatus'=>'S','failedDesc'=>'','createTime'=>time(),'status'=>0)),$rows_user['player_level'],$rows_user['userid']);   
    echo json_encode(array('status'=>0));   	
} else {
	if (LANG_FLAG == 'cn') {
		echo json_encode(array('status'=>99999,'exception'=>'解析数据为空'));
	} else {
		echo json_encode(array('status'=>99999,'exception'=>'解析數據為空'));
	}
}



