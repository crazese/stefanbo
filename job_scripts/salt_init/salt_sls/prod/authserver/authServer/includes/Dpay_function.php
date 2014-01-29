<?php

/*
 * @description 解包数据并且验证签名
 * @param  string $p_result json格式字符串
 * @param  string $p_key
 * @param  string $p_sign_type
 * @return array  $data 过滤后的数组
 * 
 * */

function handleReturnDate($p_result, $p_key, $p_sign_type) {

	$result = json_decode($p_result, true);
	$para_filter = paraFilter($result);
	$para_filter = arg_sort($para_filter);
	$string = create_linkstring($para_filter);
	$my_sign = buildMysign($string, $p_key, $p_sign_type);
	if ($my_sign == $result['Sign']) {

		$data = $para_filter;
	} else {

		$data = 'sign is error!';
	}
	return $data;
}

/*
 * @description 封装需要发送的数据
 * @param  array  $p_data
 * @param  string $p_key
 * @param  string $p_sign_type
 * @param  string $p_dev_callback_ser_url
 * @return string $result
 *
 * */

function packageData($p_data, $p_key, $p_sign_type, $p_dev_callback_ser_url) {
	
	$p_data    = arg_sort($p_data);
	$string    = create_linkstring($p_data);
	$my_sign   = buildMysign($string, $p_key, $p_sign_type);
	$post_data = $string . '&Sign=' . $my_sign;
	$result	   = dpayPost($p_dev_callback_ser_url, $post_data);
	return $result;
}

/* 
 * @description 对数组排序
 * @param  array $array 排序前的数组
 * @return array $array 排序后的数组
 * 
 * */
function arg_sort($p_array) {
	ksort($p_array);
	reset($p_array);
	return $p_array;
}

/*
 * @description 去掉空值与签名参数后的新签名参数组
 * @param  array $p_data
 * @return array $para
 *
 * */

function paraFilter($p_data) {

	$para = array();
	while (list ($key, $val) = each ($p_data)) {
		if($key == "Sign" || $key === '') continue;
		else $para[$key] = $p_data[$key];
	}
	return $para;
}

/*
 * @description 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
 * @param  array $p_array
 * @return string $data
 * 
 */

function create_linkstring($p_array) {
	
	$arg  = "";
	while (list ($key, $val) = each ($p_array)) {
		$arg.=$key."=".$val."&";
	}
	$data = substr($arg,0,count($arg)-2);
	return $data;
}

/*
 * @description 生成签名结果
 * @param  string $p_string
 * @param  string $p_key
 * @param  string $p_sign_type
 * @return string $data
 * 
 *
 * */

function buildMysign($p_string, $p_key, $p_sign_type) {

	$data = $p_string . $p_key;
	$data = sign($data, $p_sign_type);
	return $data;
}

/*
 * @description 签名字符串
 * @param  string $p_data
 * @param  string $p_sign_type
 * @return string $data
 *
 * */

function sign($p_data, $p_sign_type) {

	$data='';
	if($p_sign_type == 'MD5') {
		$data = md5($p_data);
	} elseif($p_sign_type =='DSA') {
			
		//TODO 后续开发
	} else {
		//无效签名
		$data = false;
	}
	$data = urlencode($data);
	return $data;
}

/*
 * @description PHP Crul库 模拟Post提交至支付宝网关
 * @param  string $gateway_url
 * @param  string $p_data
 * @return string $data json格式
 *
 * */

function dpayPost($gateway_url, $p_data) {

	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $gateway_url );
	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $p_data );
	$data = curl_exec ( $ch );
	curl_close ( $ch );
	return $data;
}