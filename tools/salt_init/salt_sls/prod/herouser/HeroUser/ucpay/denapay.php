<?php
set_time_limit(600);
include 'function.php';
include 'dena.php';
$sessionid = _get('sessionid');
$key = "344839ebbf2fd4232a"; // ConsumerKey
$secret = "6bc1d0d3ffdef9e2572b2fd5b49d4de47ad097"; // ConsumerSecret
$userid = _get('userid');
function getUrlInfo($endpoint,$body,$auth_header) {
	$curl = curl_init($endpoint);	
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_FAILONERROR, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_ENCODING , "gzip");
	curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));
	curl_setopt($curl, CURLOPT_VERBOSE, true);	
	$response = curl_exec($curl);
	curl_close($curl);	
	if (!empty($response)) {
		return 	$response;
	} else {
		return false;
	}
}
if (empty($sessionid)) {
	$tag = uniqid(mt_rand());
	session_id($tag);	
	session_start();	
	$res = OAuthUtil::request_temporary_credential();
	$res['serversid'] = $tag;
	echo json_encode($res);	
	exit;
} else {
	session_id($sessionid);	
	session_start();
	$verifier = _get('verifier');	
	if (!empty($_SESSION['tmp_pass'])) {
		$dataInfo = json_decode($_SESSION['tmp_pass'],true);
		$oauth_token = $dataInfo['oauth_token'];
		$oauth_token_secret = $dataInfo['oauth_token_secret'];		
	} else {
		$returnValue['status'] = 1030;
		$returnValue['message'] = '临时key数据无效，请重新登录';
		echo json_encode($returnValue);
		exit;		
	}		
	//生成通行令牌
	$endpoint = "http://sp.mobage-platform.cn/social/api/oauth/v2.01/request_token"; // Endpoint of token credential
	$sig_method = new OAuthSignatureMethod_HMAC_SHA1(); // signature method
	$consumer = new OAuthConsumer($key, $secret, NULL); // Consumer
	$token = new OAuthToken($oauth_token, $oauth_token_secret); // Token	
	// generate Authentication Header
	$params = array(
		"oauth_verifier" => $verifier,
	);
	$request = OAuthRequest::from_consumer_and_token($consumer, $token, "POST", $endpoint, $params);
	$request->sign_request($sig_method, $consumer, $token);
	$auth_header =  $request->to_header("");
	$body = ' ';
	//请求正式令牌
	$response = getUrlInfo($endpoint,$body,$auth_header);
	// parse response
	$decoded_response = OAuthUtil::urldecode_rfc3986($response);
	$parsed_parameters = OAuthUtil::parse_parameters($decoded_response);
	if (!isset($parsed_parameters['oauth_token'])) {
		$returnValue['status'] = 1030;
		$returnValue['message'] = '生成令牌失败，请重新登录';
		echo json_encode($returnValue);
		exit;		
	}
	$response = '';
	//生成交易
	$endpoint = "http://sp.mobage-platform.cn/social/api/restful/v2/bank/debit/@app"; // Endpoint of bank transaction
	$token = new OAuthToken($parsed_parameters['oauth_token'], $parsed_parameters['oauth_token_secret']); // Token
	$num = _get('amount');
	$body = array(
		"items" => array(
			array(
				"item" => array(
					"id" => 'jofgame_gzp_buy_1',
					"name" => "YUANBAO",
					"price" => 1,
					"description" => "YUANBAO",
					"imageUrl" => "https://developer.dena.jp/pub/img/common/dena.png"
				),
				"quantity" => $num
			)
		),
		"comment" => "BUY YUANBAO",
		"state" => "new"
	);
	// generate Authentication Header
	$request = OAuthRequest::from_consumer_and_token($consumer, $token, "POST", $endpoint, NULL);
	$request->sign_request($sig_method, $consumer, $token);
	$auth_header =  $request->to_header("");	
	// access to platform server
	$orderId = getUrlInfo($endpoint,$body,$auth_header);
	if (substr($orderId,0,1) != '"') {
		$returnValue['status'] = 1030;
		$returnValue['message'] = '交易生成失败，请重新登录';
		echo json_encode($returnValue);
		exit;		
	}	
	$orderId = str_replace('"','',$orderId);
	//生成交易结束
	//确认交易
	$endpoint = "http://sp.mobage-platform.cn/social/api/restful/v2/bank/debit/@app/$orderId?fields=state"; 
	$body_authorized = array(
		"state" => "authorized"
	);
	$request = OAuthRequest::from_consumer_and_token($consumer, $token, "PUT", $endpoint, NULL);
	$request->sign_request($sig_method, $consumer, $token);
	$auth_header =  $request->to_header("");
	
	$putString = json_encode($body_authorized); 
	$putData = tmpfile(); 
	fwrite($putData, $putString); 
	fseek($putData, 0); 
	$curl = curl_init($endpoint);
	curl_setopt($curl, CURLOPT_PUT, true);
	curl_setopt($curl, CURLOPT_INFILE, $putData);
	curl_setopt($curl, CURLOPT_INFILESIZE, strlen($putString));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_FAILONERROR, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_ENCODING , "gzip");
	curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));
	$response = curl_exec($curl);
	fclose($putData);
	curl_close($curl);
	
	$authorizedInfo = json_decode($response,true);
	if (isset($authorizedInfo['Error'])) {
		$returnValue['status'] = 1030;
		$returnValue['message'] = '交易确认失败，请重新登录';
		echo json_encode($returnValue);
		exit;			
	}
	$amount = floor($authorizedInfo['items'][0]['quantity']);
	if ($amount == 0) {
		$returnValue['status'] = 1030;
		$returnValue['message'] = '非法操作，请重新登录';
		echo json_encode($returnValue);
		exit;		
	}
	$response = $putString = $putData = '';
	//确认交易完成
	
	//开始交易
	$body_open = array(
		"state" => "open"
	);
	
	$putString = json_encode($body_open); 
	$putData = tmpfile(); 
	fwrite($putData, $putString); 
	fseek($putData, 0); 
	$curl = curl_init($endpoint);
	curl_setopt($curl, CURLOPT_PUT, true);
	curl_setopt($curl, CURLOPT_INFILE, $putData);
	curl_setopt($curl, CURLOPT_INFILESIZE, strlen($putString));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_FAILONERROR, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_ENCODING , "gzip");
	curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));
	$response = curl_exec($curl);
	fclose($putData);
	curl_close($curl);	
	$openInfo = json_decode($response,true);
	if (isset($openInfo['Error'])) {
		$returnValue['status'] = 1030;
		$returnValue['message'] = '你的M币不足，请去用户中心充值！';
		echo json_encode($returnValue);
		exit;			
	}	
	$response = $putString = $putData = '';
	//开始交易结束	
	$body_close = array(
		"state" => "closed"
	);	
	$sucess = 0;
	for ($i=0;$i<3;$i++) {
		$putString = json_encode($body_close); 
		$putData = tmpfile(); 
		fwrite($putData, $putString); 
		fseek($putData, 0); 
		$curl = curl_init($endpoint);
		curl_setopt($curl, CURLOPT_PUT, true);
		curl_setopt($curl, CURLOPT_INFILE, $putData);
		curl_setopt($curl, CURLOPT_INFILESIZE, strlen($putString));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_ENCODING , "gzip");
		curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));
		$response = curl_exec($curl);
		fclose($putData);
		curl_close($curl);
		$closeInfo = json_decode($response,true);		
		if (isset($closeInfo['state'])) {
			if ($closeInfo['state'] == 'closed') {
				//开始发放道具
			    $checkSql = "SELECT * FROM ".$common->tname('dena_payinfo')." WHERE `orderId` = '".$orderId."' LIMIT 1";    
			    $res_check = $db->query($checkSql);
			    $checknum = $db->num_rows($res_check);
			    if ($checknum == 0) {
			    	$common->inserttable('dena_payinfo',array('orderId'=>$orderId,'orderTime'=>time(),'userid'=>$userid,'amount'=>$amount));
			    } else {    	
			    	echo 'success';
			    	exit;
			    }
			    $sql_user = "SELECT * FROM ".$common->tname('player')." WHERE userid = $userid LIMIT 1";
			    $res_user = $db->query($sql_user);
			    $rows_user = $db->fetch_array($res_user);
			    $oldYB = 0;
			    $newYB = 0;
			    $sfcg = 'F';  
		    	$yb =  floor($amount * 10);
		    	//$yb = floor($money * 10);
		        if (!empty($rows_user)) {
		        	$sfcg = 'S'; 
		    		$oldYB = $rows_user['ingot'];
		    		$newYB = $rows_user['ingot'] + $yb;
		    		$playersid = $rows_user['playersid'];
		    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
		    		$db->query($sql_p);	    		 
		    		//writelog('app.php?task=ucchongzhi&option=pay',json_encode(array('orderId'=>$insert['orderId'],'ucid'=>$insert['ucid'],'payWay'=>$insert['payWay'],'amount'=>$insert['amount'],'orderStatus'=>$insert['orderStatus'],'failedDesc'=>$insert['failedDesc'],'createTime'=>$insert['createTime'],'status'=>0)),$rows_user['player_level'],$rows_user['userid']);		
		    		vipChongzhi($playersid, $amount, $yb, $orderId);    		
		    	}
			    writelog('app.php?task=chongzhi&type=dena&option=pay',json_encode(array('orderId'=>$orderId,'ucid'=>$rows_user['userid'],'payWay'=>'dena','amount'=>$amount,'orderStatus'=>'S','failedDesc'=>'','createTime'=>time(),'status'=>0,'newYB'=>$newYB,'oldYB'=>$oldYB)),$rows_user['player_level'],$rows_user['userid']);	    						
				echo json_encode(array('status'=>0));
				$sucess = 1;
			    //发放道具结束
			    break;			    
			}						
		} 
		sleep(1);
	}
	if ($sucess == 0) {
		echo json_encode(array('status'=>1030,'message'=>'支付失败！'));
	}
	// return transaction
	//print("Purchased!!");	
	//echo $response.'<br>';	
}
