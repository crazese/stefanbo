<?php
include 'function.php';
$MIappId = 4630;
$AppKey = 'a807b9b0-1e2c-14ec-e799-50d2bc41f3e7';
/*function SingleDecToHex($dec)
{
    $tmp="";
    $dec=$dec%16;
    if($dec<10)
        return $tmp.$dec;
    $arr=array("a","b","c","d","e","f");
    return $tmp.$arr[$dec-10];
}
function SingleHexToDec($hex)
{
    $v=ord($hex);
    if(47<$v&&$v<58)
        return $v-48;
    if(96<$v&&$v<103)
        return $v-87;
}
function SetToHexString($str)
{
    if(!$str)return false;
    $tmp="";
    for($i=0;$i<strlen($str);$i++)
    {
        $ord=ord($str[$i]);
        $tmp.=SingleDecToHex(($ord-$ord%16)/16);
        $tmp.=SingleDecToHex($ord%16);
    }
    return $tmp;
}
function UnsetFromHexString($str)
{
    if(!$str)return false;
    $tmp="";
    for($i=0;$i<strlen($str);$i+=2)
    {
        $tmp.=chr(SingleHexToDec(substr($str,$i,1))*16+SingleHexToDec(substr($str,$i+1,1)));
    }
    return $tmp;
}
//SetToHexString(hash_hmac('ripemd160', 'dsfdsafasdfasdf', 'aaaaa'));*/
function SetToHexString($str){
	$url="";
	$m1="";
	for($i=0;$i<=strlen($str);$i++){
		$m1=base_convert(ord(substr($str,$i,1)),10,16);
		if ($m1!="0") {
			$url=$url.$m1;
		}
	}
	return $url;
}
$appId = _get('appId');
$cpOrderId = _get('cpOrderId');
$cpUserInfo = _get('cpUserInfo');
$uid = _get('uid');
$orderId = _get('orderId');
$orderStatus = _get('orderStatus');
$payFee = _get('payFee');
$productCode = _get('productCode');
$productName = _get('productName');
$productCount = _get('productCount');
$payTime = _get('payTime');
$signature = _get('signature');
$payStatus = 'FAIL';
//$gameid = _get('gameid');
$fwqInfo = explode('|',$cpUserInfo);
$gameid = $fwqInfo[0];
$userid = $fwqInfo[1];
if ($gameid != $_SC['fwqdm']) {
	if ($gameid == 'zyy02') {
		zfsj('http://sop2.jofgame.com/ucpay/mipay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zjsh_zyy_001') {
		zfsj('http://zj01.jofgame.com/ucpay/mipay.php',$_REQUEST);
		exit;
	} elseif ($gameid == 'zyy03') {
		zfsj('http://zj03.jofgame.com/ucpay/mipay.php',$_REQUEST);
		exit;
	}
}
function urlencode_rfc3986($input) {
	  if (is_array($input)) {
		return array_map("urlencode_rfc3986", $input);
	  } else if (is_scalar($input)) {
		return $input;
	  } else {
		return '';
	  }
}
function build_http_query($params) {
if (!$params) return '';
// Urlencode both keys and values
$keys = urlencode_rfc3986(array_keys($params));
$values = urlencode_rfc3986(array_values($params));
$params = array_combine($keys, $values);


// Parameters are sorted by name, using lexicographical byte value ordering.
// Ref: Spec: 9.1.1 (1)
uksort($params, 'strcmp');
unset($params['signature']);
$pairs = array();
foreach ($params as $parameter => $value) {
  if (is_array($value)) {
	// If two or more parameters share the same name, they are sorted by their value
	// Ref: Spec: 9.1.1 (1)
	// June 12th, 2010 - changed to sort because of issue 164 by hidetaka
	sort($value, SORT_STRING);
	foreach ($value as $duplicate_value) {
	  $pairs[] = $parameter . '=' . $duplicate_value;
	}
  } else {
	$pairs[] = $parameter . '=' . $value;
  }
}
// For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61)
// Each name-value pair is separated by an '&' character (ASCII code 38)
return implode('&', $pairs);
}
//$mkSignature = SetToHexString(hash_hmac('sha1',"appId=$appId&cpOrderId=$cpOrderId&cpUserInfo=$cpUserInfo&uid=$uid&orderId=$orderId&orderStatus=$orderStatus&payFee=$payFee&productCode=$productCode&productName=$productName&payTime=$payTime&signature=$signature",$AppKey));
$parmams = build_http_query($_REQUEST);
$mkSignature = hash_hmac('sha1',$parmams,$AppKey);
//$mkSignature = hash_hmac('sha1',"appId=$appId&cpOrderId=$cpOrderId&cpUserInfo=$cpUserInfo&orderId=$orderId&orderStatus=$orderStatus&payFee=$payFee&payTime=$payTime&productCode=$productCode&productName=$productName&signature=$signature&uid=$uid",$AppKey);
//echo $mkSignature;
if ($signature == $mkSignature) {
    $checkSql = "SELECT * FROM ".$common->tname('mi_payinfo')." WHERE orderId = '".$orderId."' && orderStatus = '".$orderStatus."' LIMIT 1";    
    $res_check = $db->query($checkSql);
    $checknum = $db->num_rows($res_check);
    if ($checknum == 0) {
    	$common->inserttable('mi_payinfo',array('appId'=>$appId,'cpOrderId'=>$cpOrderId,'cpUserInfo'=>$cpUserInfo,'uid'=>$uid,'orderId'=>$orderId,'orderStatus'=>$orderStatus,'payFee'=>$payFee,'productCode'=>$productCode,'productName'=>$productName,'productCount'=>$productCount,'payTime'=>$payTime,'signature'=>$signature));
    } else {
    	echo json_encode(array('errcode'=>'200'));
    	exit;
    }	
    $sql_user = "SELECT * FROM ".$common->tname('player')." WHERE userid = $userid LIMIT 1";
    $res_user = $db->query($sql_user);
    $rows_user = $db->fetch_array($res_user);
    $oldYB = 0;
    $newYB = 0;
    $sfcg = 'F';  
    if ($orderStatus == 'TRADE_SUCCESS') { 
    	$yb = floor($payFee / 100) * 10;
    	//$yb = floor($money * 10);
        if (!empty($rows_user)) {
        	$sfcg = 'S'; 
    		$oldYB = $rows_user['ingot'];
    		$newYB = $rows_user['ingot'] + $yb;
    		$playersid = $rows_user['playersid'];
    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
    		$db->query($sql_p);	    		 
    		//writelog('app.php?task=ucchongzhi&option=pay',json_encode(array('orderId'=>$insert['orderId'],'ucid'=>$insert['ucid'],'payWay'=>$insert['payWay'],'amount'=>$insert['amount'],'orderStatus'=>$insert['orderStatus'],'failedDesc'=>$insert['failedDesc'],'createTime'=>$insert['createTime'],'status'=>0)),$rows_user['player_level'],$rows_user['userid']);		
    		vipChongzhi($playersid, $payFee / 100, $yb, $orderId);    		
    	}
    } 
    echo json_encode(array('errcode'=>'200'));
    writelog('app.php?task=chongzhi&type=xm&option=pay',json_encode(array('orderId'=>$orderId,'ucid'=>$rows_user['userid'],'payWay'=>'xiaomi','amount'=>$payFee / 100,'orderStatus'=>$sfcg,'failedDesc'=>'','createTime'=>time(),'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);	    
} else {
	$common->inserttable('mi_payinfo_error',array('appId'=>$appId,'cpOrderId'=>$cpOrderId,'cpUserInfo'=>$cpUserInfo,'uid'=>$uid,'orderId'=>$orderId,'orderStatus'=>$orderStatus,'payFee'=>$payFee,'productCode'=>$productCode,'productName'=>$productName,'productCount'=>$productCount,'payTime'=>$payTime,'signature'=>$signature));
    echo json_encode(array('errcode'=>'1525'));
    exit;
}

