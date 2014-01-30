<?php
define('IN_HERO', TRUE);
define('D_BUG', '0');
//程序目录
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('OP_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR);

//基本文件
include(S_ROOT.'./config.php');

//header("content-type:application/x-www-form-urlencoded; charset=utf-8");
if(SYPT == 0) {
	header("Content-Type:text/html; charset=utf-8");
} else {
	$header = "Content-Type: application/octet-stream \r\n";
	header($header);
}

//header("content-type:application/octet-stream; charset=utf-8");
//header("Content-Transfer-Encoding: binary"); 
//header("content-type:text/css; charset=utf-8");
//D_BUG?error_reporting(8191):'';
//set_magic_quotes_runtime(0);

$_SGLOBAL = array();
//设置时区
date_default_timezone_set('Asia/Shanghai');
//时间
$mtime = explode(' ', microtime());
$_SGLOBAL['timestamp'] = $mtime[1];
$_SGLOBAL['supe_starttime'] = $_SGLOBAL['timestamp'] + $mtime[0];

if(SYPT == 1) {
	// 获取头信息
	function get_all_headers() {
	    $headers = array();
	
	    foreach($_SERVER as $key => $value) {
	    	if(substr($key, 0, 5) === 'HTTP_') {            	
	        	$key = substr($key, 5);
	            $key = strtoupper($key);
	            $key = str_replace('_', ' ', $key);
	            $key = ucwords($key);
	            $key = str_replace(' ', '-', $key);
				if($key == 'X-OPENID' || $key == 'X-TOKEN') {
	            	$headers[$key] = $value;	            	
				}
	        }
	    }
	    return $headers;
	}
	
	// 获取转发的POST数据
	$raw_post_data1 = file_get_contents('php://input', 'r');
	$raw_post_data = substr($raw_post_data1, 2);
	$raw_post_array = explode('&', $raw_post_data);
	foreach($raw_post_array as $k=>$v) {
		    $post_tmp = explode('=', $v);
	        $_REQUEST[$post_tmp[0]] = $post_tmp[1];
	}
	// 获取openid和token
	$header_info = get_all_headers();
}

//$_GET处理函数
function _get($str){
  static $strkeys=array('loginKey','option','task','r','userName','tun','password','nickname','xm','qpbh','generalId','ssn','msg','userId','token','items','czfsid','kh','mm','sid','payid','yzm','nr','uzone_token','toplayersid','tuid','msgid','mc','apkvs','qd','phone','city','ms','r_code','lpm','code');
	//$magic_quote = get_magic_quotes_gpc();
	if (isset($_REQUEST[$str]))	{		
		$val = $_REQUEST[$str];	
		if (in_array($str,$strkeys)) {
			$val = mysql_escape_string($val);//mysql_escape_string($val);
		}
    else{
			$val = abs(intval($val));
		}
	} else {
		$val = null;
	}
    return $val;
}

$task = _get('task');
$option = _get('option');

require(S_ROOT.'configs/ConfigLoader.php');

if($task=='register') {
  $load = sys_getloadavg();
  if($load[0] > $_SC['loadlimit'])
    die(json_encode(array('status'=>3,'rsn'=>_get('ssn'),'message'=>$sys_lang[13])));
}

$userId = _get('userId');
$op = strtolower($option);
$ta = strtolower($task);
$onxhprof = false;
$xhprofchance = mt_rand(1,1000000);
$xhproftime = intval(date("Hm"));
$xhprofuid = is_null($userId)?0:intval($userId);
if (function_exists('xhprof_enable')  && (
    $xhprofchance<10000 || //百分之一抽样
    (isset($_REQUEST['onxhprof']) && $_REQUEST['onxhprof']==1) //|| // 参数指定的必须强制检测的
    //($xhproftime>310 && $xhproftime< 330 && $xhprofchance<10000) ||
    //($xhprofuid>0  && $xhprofuid%1000<30) ||//抽样3%的用户
    //$xhprofuid == 3227
    // ($option=="user" && $task =="register") ||
  )) {
     xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY, array('ignored_functions' => array('call_user_func', 'call_user_func_array')));
     $onxhprof = true;
     $xhproftime = microtime(1);
 }

include(S_ROOT.'./includes/class_mysql.php');
include(S_ROOT.'./includes/class_common.php');
if ($_SC['platform'] == '9ypage') {
	//if(isset($_COOKIE['herooline']) || isset($_GET['uzone_token'])) {
		include(S_ROOT.'./ucapi/libs/UzoneRestApi.php');
	//}
}


require(S_ROOT.'model/PlayerMgr.php');
require(S_ROOT.'includes/class_memcacheAdapter.php');

$db = new dbstuff;
$mc = new MemcacheAdapter_Memcached;
$G_PlayerMgr = new PlayerMgr($db,$mc);
$op = (empty($op) || !in_array($op, $_OPTION))?'':$op;
if($op <> "") {
	$ta = (empty($ta) || !in_array($ta, $_TASK[$op]))?'':$ta;
	if($ta == "") {
		//heroCommon::writelog($_SERVER['REQUEST_URI'],"","1");
		$returnValue['status'] = 2; 
		$returnValue['message'] = $sys_lang[7];
		$returnValue['rsn'] = intval(_get('ssn'));
		echo json_encode($returnValue);
    $db->close();		
		exit;
	}
} else {
	$returnValue['status'] = 2; 
	$returnValue['message'] = $sys_lang[7];
	$returnValue['rsn'] = intval(_get('ssn'));
	echo json_encode($returnValue);
  $db->close();
	exit;
}

$userName = _get('userName');
$common = new heroCommon;

if($_SC['status'] == 0 && $task != 'register' && $task != 'tjyzm') {
	$sql_check = "SELECT username FROM ".$common->tname('user')." WHERE userid = '$userId' LIMIT 1";//白名单功能
	$rows = $db->fetch_array($db->query($sql_check));
	$ucid = '';
	if (!empty($rows)) {
    $ucid = $rows['username'];//白名单功能
	}
	if (!in_array($ucid,$allowuser) || empty($ucid)) {
		//$mcConnect = $mc->addServer($MemcacheList[0], $Memport);
		$mcConnect = $mc->pconnect ($MemcacheList[0], $Memport);
		if (!$mcConnect) {
			$value['status'] = 3;
			$value['message'] = 'MEMCACHED CONNECT FAILD';
			$value['rsn'] = intval(_get('ssn'));
			$db->close();
			echo json_encode($value);
			exit;		
		}
		if (!($shutdownNotice = $mc->get(MC.'shutdownNotice'))) {
			$shutdownNotice = $db->fetch_array($db->query('select notice from ol_notice where id = 2'));
			$mc->set(MC.'shutdownNotice', $shutdownNotice, 0, 0);
		}	
		$value['status'] = 666;
		$value['message'] = $shutdownNotice['notice'];
		$value['links'] = findUrlLink($shutdownNotice['notice']);
		$value['rsn'] = intval(_get('ssn'));
		echo json_encode($value);
		exit;
	}
}

/*hk*/if(isset($_COOKIE['hero_from']) && (isset($_COOKIE['hero_9y_sid']) || isset($_COOKIE['herooline']) )) {
	if($_COOKIE['hero_from'] == 'uc') {
		$and_client = 4;
	} else if($_COOKIE['hero_from'] == '9y' ){
		$and_client = 3;
	}
}  else {
	$and_client = _get('client');
}/*hk*///支持UC 9Y账号切换
$loginKey = _get('loginKey');
$page9ySid = null;
$uzone_token = null;
if ($task == 'register' || $task == 'registertest' || $task == 'authorize' || $task == 'tjyzm') {
	if (!empty($_SESSION['ucid'])) {
			$userName = $_SESSION['ucid'];
	} 	
	if (SYPT == 1 && $userName == '') {
		$userName = $header_info['X-OPENID'];
	}	
	
	// android
	if($and_client == 2 && $userName == '') {
		$userName =  _get('userId');
	}
	
	if($and_client == 3 /*&& $userName == ''*/) {
		if(isset($_COOKIE['hero_9y_sid']) ){
			$openid = $_COOKIE['hero_9y_sid'];
		} else {
			$openid = _get('sid');
		}
		$page9ySid = $openid;
			
		$id = time();
		$data = array('sid'=>$openid);
		$sign = md5($_SC['9ycpId'].'sid='.$openid.$_SC['9yapiKey']);
		$game = array('cpId'=>intval($_SC['9ycpId']),'gameId'=>intval($_SC['9ygameId']),'channelId'=>intval($_SC['9ychannelId']),'serverId'=>intval($_SC['9yserverId']));
		$postdata = json_encode(array("id"=>$id,"service"=>"ucid.user.sidInfo","data"=>$data,"game"=>$game,"sign"=>$sign,"encrypt"=>"md5"));
			
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://'.$_SC['9yapiUrl'].'/gameservice');
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		$ucdata = curl_exec($ch);
		curl_close($ch);
		
		if (!empty($ucdata)) {
			$userInfo = json_decode($ucdata,true);
			if ($userInfo['state']['code'] == 1) {
				$value9y['ucid'] = $userInfo['data']['ucid'];
				$value9y['username'] = $userInfo['data']['nickName'];
				if(strlen($value9y['ucid']) == 0 || strlen($value9y['ucid']) < 3) {
					$value9y['status'] = 3;
					$value9y['message'] = '9游API服务器返回值异常！';
					$value9y ['gv'] = $_SC ['swf_version'];
					$value9y['rsn'] = intval(_get('ssn'));
/*hk*/					$value9y['sydz'] = 'http://qjsh.9game.cn';//返回9Y首页地址
					echo json_encode($value9y);					
					$db->close();
					exit;
				}
			} else {
				$value9y['status'] = intval($userInfo['state']['code']);
				$value9y['message'] = $userInfo['state']['msg'];
				$value9y ['gv'] = $_SC ['swf_version'];
				$value9y['rsn'] = intval(_get('ssn'));
/*hk*/				$value9y['sydz'] = 'http://qjsh.9game.cn';
				echo json_encode($value9y);				
				$db->close();
				exit;
			}
		} else {
			$value9y['status'] = 3;
			$value9y['message'] = '连接9游API服务器失败！';
			$value9y ['gv'] = $_SC ['swf_version'];
			$value9y['rsn'] = intval(_get('ssn'));
/*hk*/			$value9y['sydz'] = 'http://qjsh.9game.cn';
			echo json_encode($value9y);			
			$db->close();
			exit;
		}
		
		$userName = $value9y['ucid'];
		setcookie("hero_9y_ucid", $userName,  time()+60*60*24*30, '/');		
	}
	
	if ($and_client == 4 && $userName == '') {		
		$other = array();
		if(isset($_COOKIE['herooline']) ){
			$other = $_COOKIE['herooline'];
			list($a0,$a1, $a2) = explode('|',$other);
		} else {
			$other = _get('uzone_token');
		}
		
		if(isset($a2)) {
			$userName = $a1;
			$uzone_token = $a0;
		} else {
			$uzone_token = $other;
			$UzoneRestApi = new UzoneRestApi($uzone_token);
			$userName = $UzoneRestApi->getAuthUid();			
		}
		$userName = '2_' . $userName;
	}
	
	if($and_client == 5 && $userName == '') {
		$verify_info = explode('\n', urldecode(_get('sid')));
		$userName =  stripslashes($verify_info[0]);
	}
    $sessionId = MC.$userName.uniqid(rand(15,20));
    $sessionId = str_replace(array('_','.','%','='),'',$sessionId);
    session_id($sessionId);
} else {
	session_id($loginKey);
}

session_start();

if($and_client == 3) {
	$_SESSION['hero_9y_sid'] = $page9ySid;
	$_SESSION['hero_9y_ucid'] = $userName;	
} else if($and_client == 4) {
	$_SESSION['hero_uc_uid'] = $userName;
	$_SESSION['hero_uc_token'] = $uzone_token;
	//$_SESSION['hero_uc_inviteid'] = $a1;
}

//$hr = httpsqs_connect($_HTTPSQS['host'], $_HTTPSQS['port']);
//$mc->connect('127.0.0.1', 11211) or die('Memcache server connection error.');


$mcConnect = $mc->pconnect ($MemcacheList[0], $Memport);

// for ($m = 0; $m < 3; $m++) {
   // if ($mc->addServer($MemcacheList[0], $Memport) == true) {
        // $mcConnect = true;
      // break;
   // }
// }
if (!$mcConnect) {
	$value['status'] = 3;
	$value['message'] = 'MEMCACHED CONNECT FAILD';
	$value['rsn'] = intval(_get('ssn'));
	$db->close();
	echo json_encode($value);
	exit;		
}
//$event = json_encode(array('uid'=>_get('userId'),'task'=>$task,'time'=>$_SGLOBAL['timestamp']));
//$putRes = httpsqs_put($hr, "task", $event, "UTF-8");
//$mc->set(MC.$userName.'_session',session_id(),0,get_cfg_var("session.gc_maxlifetime"));
//if ($task == 'register' || $task == 'login') {
/*if ($task == 'register' || $task == 'authorize') {
	if ($userName == '') {
	    if (!empty($_SESSION['ucid'])) {
			$userName = $_SESSION['ucid'];
		} 
		if ($userName == '') {		
			$other = array();
			$other = $_COOKIE['herooline'];
			$a = unserialize($other);
			$uzone_token = $a[0];
			$UzoneRestApi = new UzoneRestApi($uzone_token);
			$userName = $UzoneRestApi->getAuthUid();
		}		
		
	}
	$mc->set(MC.$userName.'_session',session_id(),0,14400);
}*/
if ($task == 'register' || $task == 'registertest' || $task == 'authorize' || $task == 'tjyzm') {
	if ($userName == '') {
	    if (!empty($_SESSION['ucid'])) {
			$userName = $_SESSION['ucid'];
		} 
		if (SYPT == 1 && $userName == '') {
			$userName = $header_info['X-OPENID'];
		//$userName = 'DF12XXF5960155F127XABCSS855924B9';
		}	
		
		if($and_client == 2 && $userName == '') {
			$userName =  _get('userId');
		}
		
		if($and_client == 3 && $userName == '') {
			
		}
		
		if ($and_client == 4 && $userName == '') {		
			$other = array();
			if(isset($_COOKIE['herooline']) ){
				$other = $_COOKIE['herooline'];
				list($a0,$a1, $a2) = explode('|',$other);
			} else {
				$other = _get('uzone_token');
			}
			
			if(isset($a2)) {
				$userName = $a1;
				$uzone_token = $a0;
			} else {
				$uzone_token = $other;
				$UzoneRestApi = new UzoneRestApi($uzone_token);
				$userName = $UzoneRestApi->getAuthUid();			
			}
			$userName = '2_' . $userName;
		}
		if($and_client == 5 && $userName == '') {
			$verify_info = explode('\n', urldecode(_get('sid')));
			$userName =  stripslashes($verify_info[0]);
		}
	}
	$mc->set(MC.$userName.'_session',session_id(),0,21600);
}

if($task == 'register' || $task == 'login') {
	if(SYPT == 1) {
		// 闪游支付用数据
		$mc->set(MC.$userName.'_token',$header_info['X-TOKEN'], 0, 3600);
		$mc->set(MC.$userName.'_openid',$header_info['X-OPENID'], 0, 3600);
	}
}

//print_r($_SESSION);
if (isset($_REQUEST['ospt'])) {
	include(OP_ROOT.'./hero_quests'.DIRECTORY_SEPARATOR.'var_j2me.php');
} 
include(OP_ROOT.'./hero_quests'.DIRECTORY_SEPARATOR.'script.php');
include(OP_ROOT.'./hero_quests'.DIRECTORY_SEPARATOR.'controller.php');
include(S_ROOT.'./configs'.DIRECTORY_SEPARATOR.LANG_FLAG.DIRECTORY_SEPARATOR.'G_achievements.php');
include(OP_ROOT.'./hero_achievements'.DIRECTORY_SEPARATOR.'var.php');
include(OP_ROOT.'./hero_achievements'.DIRECTORY_SEPARATOR.'model.php');
include(OP_ROOT.'./hero_hd'.DIRECTORY_SEPARATOR.'process.php');
//include(OP_ROOT.'./hero_social/ucapi/libs/UzoneRestApi.php');
if ($task!='returnRoleDataToUser' && $task!='fightTest' && $task!='register' && $task != 'registertest'  && $task!='clearMemory' && $task != 'updateMessageMemory' && $task != 'authorize' && $task != 'checksid' && $task != 'tjyzm') {	
	$checRes = $common->checkUserSession($userId,$loginKey,$task);
	if ($checRes['status'] == 1 || $checRes['status'] == 999) {
		echo json_encode($checRes);
		$mc->close();
        $db->close();		
		exit;
	}
	/*if ($task != 'login') {
		$common->checkLoginStatus(_get('loginKey'),$userId);
	}*/	
	
	

}

if($task !='register') {
	$userid = _get('userId');
	$ssn = _get('ssn');
	if (($UserLastRequest = $mc->get(MC.'UserLastRequest_'.$userid))) {
		$message = null;
		$pre_ssn = $UserLastRequest['ssn'];
		$debug = _get('debug');
		//$defined = defined("PRESSURE_TEST_DEBUG");
		if($pre_ssn == $ssn){//&& (empty($debug) && !$defined)) 
			$message = $UserLastRequest['message'];
      echo $message;
			$mc->close();
			$db->close();
			exit;
		}
	}
}

if (empty($loginKey)) $loginKey = session_id(); 
$tag = $loginKey.'_t';

//并发锁定
//if ($task != 'checkMsg' && $task != 'getRWShow' && $task != 'move') {
if ($task != 'getRWShow') {
	//heroCommon::insertLog($tag,'yyyy');
	if ($mc->add($tag,1,0,3) === false) {
    $mc->close();
    $db->close();
    die(json_encode(array('status'=> 889,'rsn' => _get('ssn'),
      'message' => $sys_lang[14] )));
	}
}

function AppFinal() {
  	global $mc,$tag,$db,$onxhprof,$G_PlayerMgr;  
  	$G_PlayerMgr->SaveAll();
	if ($G_PlayerMgr->dbcommit != 1) {
		$db->query("rollback");
	} 
    //并发解锁
  	$mc->delete($tag);
  	$mc->close();
  	$db->close();
  	if ($onxhprof) {
	   	 global $xhproftime;
	    	__dubug_xhprof($xhproftime);
  	}
}
register_shutdown_function('AppFinal');

function DieNow($return=null) {
  if(!is_null($return)) die(json_encode($return));
  else  exit();
}

function __dubug_xhprof ($start_time) {
     if (function_exists('xhprof_disable') ) {  //&& abs(microtime(1)-$start_time) >= XHPROF_SLOW_QUERY
        global $option,$task,$xhprofuid,$db;
       $xhprof_data = xhprof_disable();
        include_once "xhprof_lib/utils/xhprof_lib.php";
        include_once "xhprof_lib/utils/xhprof_runs.php";
        $dir = S_ROOT;
        if(isset($_REQUEST['xhprofdir']) )  
          $dir .= $_REQUEST['xhprofdir'].'/';
        else {
          $dir .= "xhprof_log/".date("YmdH").'/';
          if(!is_dir($dir)) mkdir($dir,  0777, true);
        }
        $xhprof_runs = new XHProfRuns_Default($dir);
        $id = $_SERVER['SCRIPT_NAME'];
        $id = str_replace('.php', '', $id);
        $id = str_replace('/', '_', $id);
        $id .= '_'.$option.'_'.$task;
        $run_id = $xhprof_runs->save_run($xhprof_data, $id);
        $file = S_ROOT.'sql_log/'.date("YmdH").'.txt';
        if(!file_exists($file)) 
           file_put_contents($file,"create at ".date("Y-m-d H:m:s")." \n");
        error_log(date("H:i:s").' '.$option.'_'.$task."_{$xhprofuid}_$run_id\n".$db->sqls,3,$file);
    }
}

ignore_user_abort(true);
$common->getCmd($option);
?>