<?php
class MemcacheAdapter_Memcache {
  private $mc_;
  function __construct() {
    $this->mc_ = new Memcache;
  }
  public function pconnect($ip,$port) {
	$this->host = $ip;
	$this->port = $port;
    return $this->mc_ ->pconnect($ip,$port);
  }
  public function addServer($ip,$port) {
	$this->host = $ip;
	$this->port = $port;
    return $this->mc_ ->addServer($ip,$port);
  }
  public function set($key,$value,$flag,$exp){
    return $this->mc_ ->set($key,$value,$flag,$exp);
  }
  public function get($key) {
    return $this->mc_ ->get($key);
  }
  public function add($key,$value,$flag,$exp){
    return $this->mc_ ->add($key,$value,$flag,$exp);
  }
  public function close(){
    return $this->mc_ ->close();
  }
  public function delete($key) {
    return $this->mc_ ->delete($key);
  }
  public function getMulti($keys,&$cas=false) {
    // $arr = array();
    // foreach($keys as $key) {
      // $d = $this->mc_ ->get($key);
      // if($d!==false){
        // $arr[] = $d;
        // if($cas!==false) $cas[$key] = 1;
      // }
    // }
    // return $arr;
    $arr = $this->mc_ ->get($keys);
    foreach($arr as $key=>$val) {
      $cas[$key] = 1;
    }
    return $arr;
  }
  public function getAllKeys() {
    $list = array();
	
	$items=$this->mc_->getExtendedStats ('items');
	$host = $this->host;
	$port = $this->port;
	$items=$items["$host:$port"]['items'];
	foreach($items as $key=>$values){
		$number=$key;;
		$str=$this->mc_->getExtendedStats ("cachedump", $number, 0);
		$line=$str["$host:$port"];
		if( is_array($line) && count($line)>0){
		 foreach($line as $key=>$value){
			 $list[]=$key;
		 }
		}
	}
    return $list;
  }
  public function getResultCode() {
    return 0;
  }
  public function getResultMessage() {
    return '';
  }
  private $host,$port;
}

class MemcacheAdapter_Memcached {
  //const  memcachepoolsize = 4;
  private $mc_;
  private $ret_code = array(0=>'Memcached::RES_SUCCESS', 1=>'Memcached::RES_FAILURE', 2=>'Memcached::RES_HOST_LOOKUP_FAILURE', 7=>'Memcached::RES_UNKNOWN_READ_FAILURE', 8=>'Memcached::RES_PROTOCOL_ERROR', 9=>'Memcached::RES_CLIENT_ERROR', 10=>'Memcached::RES_SERVER_ERROR', 5=>'Memcached::RES_WRITE_FAILURE', 12=>'Memcached::RES_DATA_EXISTS', 14=>'Memcached::RES_NOTSTORED', 16=>'Memcached::RES_NOTFOUND', 18=>'Memcached::RES_PARTIAL_READ', 19=>'Memcached::RES_SOME_ERRORS', 20=>'Memcached::RES_NO_SERVERS', 21=>'Memcached::RES_END', 25=>'Memcached::RES_ERRNO', 31=>'Memcached::RES_BUFFERED', 30=>'Memcached::RES_TIMEOUT', 32=>'Memcached::RES_BAD_KEY_PROVIDED', 11=>'Memcached::RES_CONNECTION_SOCKET_CREATE_FAILURE', -1001=>'Memcached::RES_PAYLOAD_FAILURE');
  function __construct() {
    $this->mc_ = new Memcached('m'.MC);//'m'.mt_rand(1, MemcacheAdapter_Memcached::memcachepoolsize));
    if(count($this->mc_->getServerList())<1) {
      //设置某些选项会断开所有已建立的连接。所以不要每次都setoption
      //不要每次都addserver，会建立多个连接。
      $this->mc_->setOptions(array(
        //Memcached::OPT_BINARY_PROTOCOL=>true, //默认false
        Memcached::OPT_NO_BLOCK =>true, //默认false
        Memcached::OPT_TCP_NODELAY=>true, //默认false
        //Memcached::OPT_SERIALIZER=> Memcached::SERIALIZER_IGBINARY, //Memcached::SERIALIZER_PHP,    //Memcached::HAVE_IGBINARY 
       // Memcached::OPT_COMPRESSION=>false, //默认true
        //Memcached::OPT_COMPRESSION_TYPE=> Memcached::COMPRESSION_FASTLZ,
        Memcached::OPT_SOCKET_SEND_SIZE =>32768,
        Memcached::OPT_SOCKET_RECV_SIZE =>32768,
      )); 
      //$this->mc_->setOption(Memcached::OPT_SOCKET_SEND_SIZE ,32768); //18默认f50868
      //$this->mc_->setOption(Memcached::OPT_SOCKET_RECV_SIZE ,32768); //18默认f87744
    
      //$mem->setOption(Memcached::OPT_RECV_TIMEOUT, 1000);
      //$mem->setOption(Memcached::OPT_SEND_TIMEOUT, 3000);
      //$mem->setOption(Memcached::OPT_PREFIX_KEY, "md_");
      //$mem->addServer('127.0.0.1',11211);
      //一致性hash	Memcached::OPT_LIBKETAMA_COMPATIBLE
    }
  }
  public function pconnect($ip,$port) {
    if(count($this->mc_->getServerList())<1) {
      return $this->mc_ ->addServer($ip,$port);
    }
    return true;
  }
  public function addServer($ip,$port) {
    //if(count($this->mc_->getServerList())<1) {
      return $this->mc_ ->addServer($ip,$port);
    //}
  }
  public function set($key,$value,$flag,$exp){
	$result = $this->mc_->set($key,$value,$exp);
	if(!$result) {
		$ret_code = $this->mc_->getResultCode();
		if($ret_code != Memcached::RES_NOTFOUND && $ret_code != Memcached::RES_SUCCESS && isset($this->ret_code[$ret_code])) { // 记录日志
			$logMessage = "status:11211, message:errno:{$ret_code},err:{$this->ret_code[$ret_code]},raw_opt:set({$key},{$value},{$exp}), uri:{$_SERVER['REQUEST_URI']}";
			error_log($logMessage, 0);
		}
	}
    return $result;
  }
  public function get($key) {
	$result = $this->mc_->get($key);
	if(!$result) {
		$ret_code = $this->mc_->getResultCode();
		if($ret_code != Memcached::RES_NOTFOUND && $ret_code != Memcached::RES_SUCCESS && isset($this->ret_code[$ret_code])) { // 记录日志
			$logMessage = "status:11211, message:errno:{$ret_code},err:{$this->ret_code[$ret_code]},raw_opt:get({$key}), uri:{$_SERVER['REQUEST_URI']}";
			error_log($logMessage, 0);
		}
	}
    return $result;
  }
  public function add($key,$value,$flag,$exp){
    return $this->mc_ ->add($key,$value,$exp);
  }
  public function close(){
    return;
  }
  public function delete($key) {
    return $this->mc_ ->delete($key);
  }
  public function getMulti($keys,&$cas=false) {	
	$raw_opt = '';
    if($cas===false) {
		$result = $this->mc_ ->getMulti($keys);
		$str = implode(',', $keys);
		$raw_opt = "getMulti(array({$str}))";
	} else{
		$result = $this->mc_ ->getMulti($keys,$cas);
		$str = implode(',', $keys);
		$raw_opt = "getMulti(array({$str}),{$cas})";
	}
	if(!$result) {
		$ret_code = $this->mc_->getResultCode();
		if($ret_code != Memcached::RES_NOTFOUND && $ret_code != Memcached::RES_SUCCESS && isset($this->ret_code[$ret_code])) { // 记录日志
			$logMessage = "status:11211, message:errno:{$ret_code},err:{$this->ret_code[$ret_code]},raw_opt:{$raw_opt}, uri:{$_SERVER['REQUEST_URI']}";
			error_log($logMessage, 0);
		}
	}	
    return $result;
  }
  public function getAllKeys() {
    return $this->mc_ ->getAllKeys();
  }
  public function getResultCode() {
    return $this->mc_ ->getResultCode();
  }
  public function getResultMessage() {
    return $this->mc_ ->getResultMessage();
  }
}
?>