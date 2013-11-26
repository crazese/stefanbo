<?php
//require_once('HttpClient.class.php');
class HanderReq{
	/**
	* @desc  对请求参数进行签名
	*
	* @param  $param = array()    - 请求参数
	* @return string
	*/
	public static function signGenerate($param,$key){
		//按照参数名升序排列
		ksort($param);

		//var_dump($param);
		//用＆把参数连接
		$srcStr = '';
		foreach($param as $k => $v){
			if($k == "sign")
				continue;
			if(!empty($srcStr)) $srcStr .= '&';
			$srcStr .=	$k . '=' . $v;
		}
//echo "-------srcStr---------\n";
//var_dump($srcStr);
//echo "-------key---------\n";
//var_dump($key);
		//echo '@@@@@',$srcStr,'@@@@';
		//md5加密，生存签名
		$sign = md5($srcStr.$key);
		//var_dump($srcStr.UPAY_SIGN_KEY);
		//echo '%%%%%',$key,'%%%%%';
		return $sign;
	}

	public static function getData($param,$signKey){
		$signSrc='';
		$syb='';
		ksort($param);
		foreach($param as $key=>$value){
			$signSrc .= $syb.$key.'='.$value;
			$syb='&';
		}
		$sign=md5($signSrc.$signKey);
		//echo "\n$sign\n";
		$signSrc='';
		$syb='';
		foreach($param as $key=>$value){
			$signSrc .= $syb.$key.'='.urlencode($value);
			$syb='&';
		}
		$data="$signSrc&sign=$sign";
		return $data;
	}

	public static function verifySign($params,$key,$sign){
		$signSrc = HanderReq::signGenerate($params,$key);
//echo "-----compSign--------\r\n";
//var_dump($signSrc);
		return strcasecmp($signSrc,$sign);
	}

	public static function http_post($host, $port, $path, $data)
    {
        $sock = fsockopen($host, $port, $errno, $errstr, 30);
		fwrite($sock, "POST $path HTTP/1.0\r\n");
		fwrite($sock, "Host: $host\r\n");
		fwrite($sock, "Content-type: application/x-www-form-urlencoded\r\n");
		fwrite($sock, "Content-length: " . strlen($data) . "\r\n");
		fwrite($sock, "Accept: */*\r\n");
		fwrite($sock, "\r\n");
		fwrite($sock, "$data\r\n");
		fwrite($sock, "\r\n");
		$headers = "";
		while ($str = trim(fgets($sock, 4096))){
			 $headers .= "$str\n";
//echo "$str...\r\n";
		}
//echo "---------body---------\r\n";
		$body = "";
		while (!feof($sock)){
			 $body .= fgets($sock, 4096);
//echo "$body\r\n";
		}
		fclose($sock);
		return $body;
    }

	public static function sendReqAndGetRes($url,$key,$param){
		$urlArr = parse_url($url);
		$host = $urlArr['host'];
		if(!empty($urlArr['port']))
			$port = $urlArr['port'];
		else
			$port = 80;
		$path = $urlArr['path'];
		$data = HanderReq::getData($param,$key);
		$res = HanderReq::http_post($host, $port, $path, $data);
		//$res=HttpClient::quickPost($url,$data);
		$resArr = HanderReq::urlToArray($res);		
		if($resArr["result"] == '00' ) {
			if(HanderReq::verifySign($resArr,$key,$resArr["sign"])==0)
				return $resArr;
			else 
				return "false";
		} else {
			return $resArr;
		}
		
	}
	
	public static function sendReqAndGetRes1($url,$key,$param){
		$urlArr = parse_url($url);
		$host = $urlArr['host'];
		if(!empty($urlArr['port']))
			$port = $urlArr['port'];
		else
			$port = 80;
		$path = $urlArr['path'];
		$data = HanderReq::getData($param,$key);
		$res = HanderReq::http_post($host, $port, $path, $data);
		//$res=HttpClient::quickPost($url,$data);
		$resArr = HanderReq::urlToArray($res);
		if($resArr["rsp_code"] == '00' ) {
			if(HanderReq::verifySign($resArr,$key,$resArr["sign"])==0)
				return $resArr;
			else
				return "false";
		} else {
			return $resArr;
		}
	
	}

	public static function sendDirectReqAndGetRes($url,$param,$storeKey){
		$urlArr = parse_url($url);
		$host = $urlArr['host'];
		if(!empty($urlArr['port']))
			$port = $urlArr['port'];
		else
			$port = 80;
		$path = $urlArr['path'];

		$data='';
		$syb='';
		foreach($param as $key=>$value){
			$data .= $syb.$key.'='.urlencode($value);
			$syb='&';
		}

//echo "---------data-----\r\n";
//var_dump($data);

		$res = HanderReq::http_post($host, $port, $path, $data);
//echo "---------res-----\r\n";
//var_dump($res);
		$resArr = HanderReq::urlToArray($res);
//echo "---------resArr-----\r\n";
//var_dump($resArr);
		if(HanderReq::verifySign($resArr,$storeKey,$resArr["sign"])==0)
			return $resArr;
		return "false";
	}

	public static function urlToArray($url) {
		if(strchr($url,"?")){
			$kv = explode("?",$url);
			$paramsStr = $kv[1];
			}
		$paramsStr = $url;
		parse_str($paramsStr,$params);
        return $params;
	}
}
?>