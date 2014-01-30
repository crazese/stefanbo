<?php
/**
 * 
 **/
class UpayRC {
    function getHttp($url)
    {
        $data = file_get_contents($url);
        return $data;
    }

    function postHttp($data,$host,$port,$path)
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
		}
		$body = "";
		while (!feof($sock)){
			 $body .= fgets($sock, 4096);
		}
		fclose($sock);
		return $body;
    }
	
	function getData($ary,$signKey){
		$signSrc='';
		$syb='';
		ksort($ary);
		foreach($ary as $key=>$value){
			$signSrc .= $syb.$key.'='.$value;
			$syb='&';
		}
		$sign=md5($signSrc.$signKey);
		$signSrc='';
		$syb='';
		foreach($ary as $key=>$value){
			$signSrc .= $syb.$key.'='.urlencode($value);
			$syb='&';
		}
		$data="$signSrc&sign=$sign";
		return $data;
	}
}
?>
