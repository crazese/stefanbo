<?php

#取得返回串中的所有参数.
function getszfCallBackValue(&$version,&$merId,&$payMoney,&$orderId,&$payResult,&$privateField,&$md5String,&$errcode)
{  
	if( !isset($_REQUEST['version']) || !isset($_REQUEST['merId']) || !isset($_REQUEST['payMoney']) || 
			!isset($_REQUEST['orderId']) || !isset($_REQUEST['payResult']) || !isset($_REQUEST['privateField']) || 
			!isset($_REQUEST['md5String']) || !isset($_REQUEST['errcode']) ) {
		return 0;
	}
	
	$version	= $_REQUEST['version'];
	$merId		= $_REQUEST['merId'];
	$payMoney	= $_REQUEST['payMoney'];
	$orderId	= $_REQUEST['orderId'];
	$payResult	= $_REQUEST['payResult'];
	$privateField	= $_REQUEST['privateField'];
	$md5String	= $_REQUEST['md5String'];
	$errcode	= $_REQUEST['errcode'];
	
	return 1;
	
}


#验证返回参数中的hmac与商户端生成的hmac是否一致.
function CheckszfHmac($version,$merId,$payMoney,$orderId,$payResult,$privateField,$payDetails,$md5String)
{
	$Data = $version . $merId . $payMoney . $orderId . $payResult . $privateField . $payDetails . SZF_PRIVATEKEY;
	$string	= md5($Data);	
/* 	$james=fopen("log.txt","a+");
	fwrite($james, "param:".$Data."\r\n");
	fwrite($james, "SZ:".$md5String."\r\n");
	fwrite($james, "md5:".$string."\r\n"); */
	
	if( $md5String == $string ) {
/* 		fwrite($james, "true"."\r\n");
		fclose($james); */
		return true;
	}
	else {
/* 		fwrite($james, "false"."\r\n");
		fclose($james); */
		return false;
	}	
}

// 加密函数：encrypt
function encrypt($encrypt,$key="") {
	$iv = mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), MCRYPT_RAND );
	$passcrypt = mcrypt_encrypt ( MCRYPT_RIJNDAEL_256, $key, $encrypt, MCRYPT_MODE_ECB, $iv );
	$encode = base64_encode ( $passcrypt );
	return $encode;
}

// 解密函数：decrypt
function decrypt($decrypt,$key="") {
	$decoded = base64_decode ( $decrypt );
	$iv = mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), MCRYPT_RAND );
	$decrypted = mcrypt_decrypt ( MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_ECB, $iv );
	return $decrypted;
}


///////生成通过des加密后的cardinfo，并进行base64加密
function GetDesCardInfo($cardmoney,$cardnum,$cardpwd,$deskey)
{
	$str=$cardmoney."@".$cardnum."@".$cardpwd;
	$size = mcrypt_get_block_size('des', 'ecb');
	$input = pkcs5_pad($str, $size);

	$td = mcrypt_module_open(MCRYPT_DES,'','ecb',''); //使用MCRYPT_DES算法,ecb模式
	$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	$ks = mcrypt_enc_get_key_size($td);
	$key=base64_decode($deskey);
	mcrypt_generic_init($td, $key, $iv); //初始处理
	//加密
	$encrypted_data = mcrypt_generic($td, $input);
	 
	//结束处理
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	/////作base64的编??
	$encode = base64_encode($encrypted_data);
	return $encode;
}
 

function pkcs5_pad ($text, $blocksize)
{
$pad = $blocksize - (strlen($text) % $blocksize);
return $text . str_repeat(chr($pad), $pad);
}
//测试
//echo GetDesCardInfo("50","1111","2222","123456");