<?php 
/**
 * 目前是无效的功能
 */

class sySocial{

	public function shareMessage($ucid, $message){}


	public function pushMessage($ucid, $message, $otherId = null){
		global $mc;
		$username = $ucid;
		$seqnum = 'QJSH' . date('ymdHis') . rand(1, 9999999);

		$token = $mc->get(MC.$username.'_token');
		$openid = $mc->get(MC.$username.'_openid');
		$target = 2;
		if(!is_null($otherId)){
			$target = 1;
			$openid = $otherId;
		}
		$pro = "{'seqnum':'$seqnum', 'func':'mtf', 'openid':'$openid', 'msgtype':'1', 'target':'$target' }";

		$result = $this->sendContent($token, $pro);
		if(is_array($result)&&$result['code']==1){
			return true;
		}else{
			return false;
		}
	}

	private function sendContent($token, $pro){
		global $payServer;

		$appid = 9;
		$servernum = rand(0, 1);
		$serverIp = $payServer[$servernum]['server'];
		$serverPort = $payServer[$servernum]['port'];

		$fp = fsockopen($serverIp, $serverPort, $errno, $errstr, 30);

		if(!$fp){
			return false;
		}
		$header = "GET /appid=$appid HTTP/1.1\r\n";
		$header .= "Host:localhost \r\n";
		$header .= "Content-Length: ".strlen($pro)."\r\n";
		$header .= "Content-Type: text/html \r\n";
		$header .= "X-TOKEN: $token\r\n";
		$header .= "Connection: Close\r\n\r\n";
		$header .= $pro."\r\n";

		socket_set_timeout($fp, 1);

		fwrite($fp, $header);

		$str = '';
		$startTime = microtime(true);
		while(!feof($fp)){
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
		$result = json_decode($str, true);
		return $result;
	}

	public function getFriends($roleInfo){
		global $mc;
		$username = $ucid;
		$seqnum = 'QJSH' . date('ymdHis') . rand(1, 9999999);

		$token = $mc->get(MC.$username.'_token');
		$openid = $mc->get(MC.$username.'_openid');

		$pro = "{'seqnum':'$seqnum', 'func':'gflo', 'openid':'$openid'}";
   	
		$result = $this->sendContent($token, $pro);
		
		if(is_array($result)&&$result['code']==1){
			$friends = $result['list'];
			return $friends;
		}else{
			return array();
		}
	}
}