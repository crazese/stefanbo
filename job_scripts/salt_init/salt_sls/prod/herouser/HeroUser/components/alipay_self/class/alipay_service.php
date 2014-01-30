<?php
require_once (dirname(__FILE__) . "/alipay_function.php");

class alipay_service {
	var $gateway_paychannel = "https://mapi.alipay.com/cooperate/gateway.do?";
	var $gateway_order = "http://wappaygw.alipay.com/service/rest.htm?";
	
	var $mysign;			//签名结果
	var $parameter;			//需要签名的参数数组
	var $format;			//字符编码格式
	var $req_data='';		//post请求数据

	/**构造函数
	 */
	function alipay_service() {		
	}
	
	/**
	 * 创建mobile_merchant_paychannel接口
	 */
	function mobile_merchant_paychannel($parameter) {
		
		//除去数组中的空值和签名参数
		$this->parameter = para_filter($parameter);
		
		//得到从字母a到z排序后的签名参数数组
		$sort_array	= arg_sort($this->parameter);

		//生成签名
		$this->mysign = build_mysign($sort_array);

		//创建请求数据串,注意sign签名需要urlencode
		$this->req_data	= create_linkstring($this->parameter).'&sign='.urlencode($this->mysign).'&sign_type=0001';
		
		//模拟get请求方法
		$result = $this->get($this->gateway_paychannel);
		
		//调用处理Json方法
		$alipay_channel = $this->getJson($result);
		return $alipay_channel;
	}

	/**
	 * 验签并反序列化Json数据
	 */
	function getJson($result)
	{	
		//获取返回的Json
		$json = getDataForXML($result,'/alipay/response/alipay/result');
		//拼装成待签名的数据
		$data = "result=" . $json;// . $this->_key;
		
		//获取返回sign
		$aliSign = getDataForXML($result,'/alipay/sign');

		//转换待签名格式数据，因为此mapi接口统一都是用GBK编码的，所以要把默认UTF-8的编码转换成GBK，否则生成签名会不一致
		$data_GBK = mb_convert_encoding($data, "GBK", "UTF-8");
		
		//返回布尔值，是否验签通过
		$isverify = verify($data_GBK, $aliSign);					

		//判断签名是否正确
		if($isverify)
		{
			//签名相同
			//echo "签名相同";

			//输出返回Json
			//print($json);
			
			//php读取json数据
			return json_decode($json);
		}
		else{
			//验签失败
			//echo "验签失败";exit;
			return "验签失败";
		}
	}

	/**
	 * 模拟https的get请求，并返回默认utf-8的数据返回
	 */
	function get($gateway_url){
		$host = 'ssl://mapi.alipay.com';
		$port = 443;
		$header = "GET /cooperate/gateway.do?{$this->req_data} HTTP/1.1\r\n";
		$header .= "Host: mapi.alipay.com\r\n";
		$header .= "Connection: Close\r\n\r\n";  
		//$header .= $ths->req_data;
		$fp = fsockopen($host,$port,$errno,$errstr,30);
		$ret = '';
		if(!$fp){
			$error = "建立sock连接失败,host:$host,port:$port";
			throw new Exception($error);
		}else{
			fwrite ($fp, $header);  
			while (!feof($fp)) {
				$ret .= fgets($fp, 4096);
			}
			fclose($fp);
			$httpCode = 400;
			$content  = '';
			$response = explode("\r\n\r\n",$ret);
			$content  = $response[1];
	
			$responseCodeStr = explode("\r\n",$response[0]);
			$responseCode    = explode(" ",$responseCodeStr[0]);				
			$httpCode		 = $responseCode[1];
		}
	        
		return $content;
	}

	/**
	 * 创建alipay.wap.trade.create.direct接口
	 */
	function alipay_wap_trade_create_direct($parameter) {

		//除去数组中的空值和签名参数
		$this->parameter = para_filter($parameter); 
		
		//参数数组
		$this->req_data = urlencode($parameter['req_data']);
		
		//编码格式，此处为utf-8
		$this->format = $this->parameter['format']; 
		
		//得到从字母a到z排序后的签名参数数组
		$sort_array = arg_sort($this->parameter);

		//生成签名
		$this->mysign = build_mysign($sort_array);

		//配置post请求数据，注意sign签名需要urlencode
		$this->req_data = create_linkstring($this->parameter) . '&sign=' . urlencode($this->mysign);
		//Post提交请求
		$result	= $this->post($this->gateway_order);
		//调用GetToken方法，并返回token
		return $this->getToken($result);
	}

	/**
	 * 调用alipay_Wap_Auth_AuthAndExecute接口
	 */
	function alipay_Wap_Auth_AuthAndExecute($parameter, $show_link=false) {
		
		//参数数组
		$this->parameter = para_filter($parameter);
		
		//排好序的参数数组
		$sort_array	= arg_sort($this->parameter);
		
		//生成签名
		$this->mysign = build_mysign($sort_array);
		
		//生成跳转链接
		$RedirectUrl = $this->gateway_order . create_linkstring($this->parameter) . '&sign=' . urlencode($this->mysign);
		if(!$show_link) {
			//跳转至该地址
			Header("Location: $RedirectUrl");
		} else {
			return $RedirectUrl;
		}
	}

	/**
	 * 返回token参数
	 * 参数 result 需要先urldecode
	 */
	function getToken($result)
	{
		//URL转码
		$result	= urldecode($result);				
		
		//根据 & 符号拆分
		$Arr = explode('&', $result);				
		
		//临时存放拆分的数组
		$temp = array();

		//待签名的数组
		$myArray = array();

		//循环构造key、value数组
		for ($i = 0; $i < count($Arr); $i++) {
			$temp = explode( '=' , $Arr[$i] , 2 );
			$myArray[$temp[0]] = $temp[1];
		}
		
		//需要先解密res_data
		$myArray['res_data'] = decrypt($myArray['res_data']);
		//获取返回的RSA签名
		$sign = $myArray['sign'];

		//去sign，去空值参数
		$myArray = para_filter($myArray);	

		//排序数组
		$sort_array = arg_sort($myArray);	

		//拼凑参数链接 & 连接
		$prestr = create_linkstring($sort_array);	
		
		//返回布尔值，是否验签通过
		$isverify = verify($prestr, $sign);					

		//判断签名是否正确
		if($isverify)
		{
			//返回token
			return getDataForXML($myArray['res_data'],'/direct_trade_create_res/request_token');	
		}
		else
		{
			//当判断出签名不正确，请不要验签通过
			return '签名不正确';
		}
	}

	/**
	 * PHP Crul库 模拟Post提交至支付宝网关
	 * 如果使用Crul 你需要改一改你的php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
	 * 返回 $data
	 */
	function post($gateway) {
		/*$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $gateway);				//配置网关地址
		curl_setopt($ch, CURLOPT_HEADER, 0);						//过滤HTTP头
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);							//设置post提交
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->req_data);		//post传输数据
		$data = curl_exec($ch);
		curl_close($ch);*/
		$host = 'wappaygw.alipay.com';
                $port = 80;
                $header = "POST /service/rest.htm HTTP/1.0\r\n";
                $header .= "Host: wappaygw.alipay.com\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: ".strlen($this->req_data)."\r\n";
		$header .= "Connection: Close\r\n\r\n";  
                $header .= $this->req_data;
                $fp = fsockopen($host,$port,$errno,$errstr,30);
		$ret = '';
                if(!$fp){
                        $error = "建立sock连接失败,host:$host,port:$port";
                        throw new Exception($error);
                }else{
                        fwrite ($fp, $header);
                        while (!feof($fp)) {
                                $ret .= fgets($fp, 4096);
                        }
                        fclose($fp);
                        $httpCode = 400;
                        $content  = '';
                        $response = explode("\r\n\r\n",$ret);
                        $content  = $response[1];

                        $responseCodeStr = explode("\r\n",$response[0]);
                        $responseCode    = explode(" ",$responseCodeStr[0]);
                        $httpCode                = $responseCode[1];
                }
		
		return $content;
	}
}

?>
