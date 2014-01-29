<?php
function array_implode($array ,$rt=0) {
  if(!is_array($array)) return $array;
  $sp=str_split(',|:!@_%-#^$');
  $g = $sp[$rt*2];
  $s = $sp[$rt*2+1];
  
  $string = array();
  foreach ( $array as $key => $val ) {
      if ( is_array( $val ) ) $val = array_implode($val,$rt+1 );
      $string[] = "{$key}{$g}{$val}"; 
  }
  return implode( $s, $string );
}

function array_explode($str ,$rt=0 ) {
  $sp=str_split(',|:!@_%-#^$');
  $g = $sp[$rt*2];
  $s = $sp[$rt*2+1];
  if(strpos($str,$g) === false && strpos($str,$s)===false)  return  $str; 
  
  $items = explode($s,$str);
  $arr = array();
  $rt2= $rt+1;
  $g2 = $sp[$rt2*2];
  $s2 = $sp[$rt2*2+1];
  for($i=0,$l=count($items);$i<$l;++$i) {
    $keyvalue=explode($g,$items[$i]);
    if(strpos($keyvalue[1],$g2) || strpos($keyvalue[1],$s2)) 
      $keyvalue[1] = array_explode($keyvalue[1],$rt2 );
    $arr[$keyvalue[0]] = is_numeric($keyvalue[1])?$keyvalue[1]+0:$keyvalue[1];
  }
  return $arr;
}

function findUrlLink($html){
	$html = nl2br($html);
	$html = preg_replace('/<br([\ \/])*>/i', 'n', $html);
	$strCont = array();

	$position = 0;
	while(($_position = strpos($html, '<a', $position)) !== false){
		$front =  substr($html, 0, $_position);
		$front = htmlspecialchars_decode($front);
		$front = strip_tags($front);

		$a_position = strpos($html, 'a>', $_position);
		if($a_position !== false){
			$content = substr($html, $_position, $a_position-$_position);
			$_content = htmlspecialchars_decode($content);
			$_content = strip_tags($_content);
		}else{
			$content = substr($html, $_position);
			$_content = htmlspecialchars_decode($content);
			$_content = strip_tags($_content);
			break;
		}
		$position = $a_position;

		if(preg_match('/href=\'(\S*)\'|href="(\S*)"/i', $content, $match)){
			$front_len = mb_strlen($front, 'utf8');
			$content_len = mb_strlen($_content, 'utf8');
			$content_len = $content_len>0?$content_len-1:0;
			$url = empty($match[1])?$match[2]:$match[1];
			$info = array($url, $front_len, $content_len+$front_len);
			$strCont[] = $info;
		}
	}

	return $strCont;
}

class authentication_header {
	private $username;
	private $password;
	private $ip;

	public function __construct($username, $password, $ip) {
		$this->username = $username;
		$this->password = $password;
		$this->ip = $ip;
	}
}

class heroCommon {	
	/**
	 * 记录操作中调用的sql次数
	 *
	 * @var int
	 */
	private static $sqlnum = 0;
  private static $sqlstrings = "";
	
	/**
	 * 记录sql执行次数
	 *
	 */
	public static function addSqlNum($sql=""){
		 self::$sqlnum ++;
     //if(1 && $sql!="") self::$sqlstrings.=$sql."<br/>";
	}
  
  public static function clearSqlNum(){
		 self::$sqlnum =0;
     self::$sqlstrings="";
	}
  
  public static function getSqlNum(){
		 return self::$sqlnum;
	}
  
  public static function getSqlStrings() {
    return self::$sqlstrings;
  }
	
	
	//SQL ADDSLASHES
	function saddslashes($string) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = $this->saddslashes($val);
			}
		} else {
			$string = addslashes($string);
		}
		return $string;
	}
	
	//取消HTML代码
	function shtmlspecialchars($string) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = $this->shtmlspecialchars($val);
			}
		} else {
			$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1',
				str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
		}
		return $string;
	}
	
	//清空cookie
	function clearcookie() {
		global $_SGLOBAL;	
		obclean();
		ssetcookie('auth', '', -86400 * 365);
		$_SGLOBAL['supe_uid'] = 0;
		$_SGLOBAL['supe_username'] = '';
		$_SGLOBAL['member'] = array();
	}
	
	//cookie设置
	function ssetcookie($var, $value, $life=0) {
		global $_SGLOBAL, $_SC, $_SERVER;
		setcookie($_SC['cookiepre'].$var, $value, $life?($_SGLOBAL['timestamp']+$life):0, $_SC['cookiepath'], $_SC['cookiedomain'], $_SERVER['SERVER_PORT']==443?1:0);
	}
	
	//数据库连接
	function dbconnect() {
		global $_SC;
		$this->charset = $_SC['dbcharset'];
		$link = $this->__construct($_SC['dbhost'], $_SC['dbuser'], $_SC['dbpw'], $_SC['dbname'], $_SC['pconnect']);	    
	}
	
	//关闭数据库连接
	function close_mysql() {
	   global $db;
	   $db->close();
	}
	
	//判断用户的session状态
	public static function checkUserSession($userid,$loginKey,$task) {
		global $session,$mc,$sys_lang;
		$value = array();
		if (!empty($_SESSION)){
			$userLoginInfo = $mc->get(MC.$_SESSION['ucid'].'_session');
			//heroCommon::insertLog('userlogininfo'.$userLoginInfo.'|'.'loginKey:'.$loginKey);
			//heroCommon::insertLog('====================-----------------userid:'.$_SESSION['userid'].'|session_userid'.$_SESSION['userid']);
			if(isset($_SESSION['hf_userid'])) {
				$hf_userids = json_decode($_SESSION['hf_userid'], true);
				foreach($hf_userids as $key=>$value) {
					if($value['userId'] == $userid) {
						$_SESSION['userid'] = $value['userId'];
					}
				}
			}
			if ($userid != $_SESSION['userid'] || empty($_SESSION['userid'])) {
				$value['status'] = 1;
			    $value['message'] = $sys_lang[8];
		    } elseif (substr($userLoginInfo,0,6) == 'closed') {
			    $value['status'] = 1;   //session无效				
				$reason = substr($userLoginInfo,7,strlen($userLoginInfo));				
			    $value['message'] = str_replace('{reason}', $reason, $sys_lang[10]);
		    } elseif ($userLoginInfo == 'reset'){
			    $value['status'] = 1;   //session无效
			    $value['message'] = $sys_lang[9];			    			    	
		    } elseif ($userLoginInfo != $loginKey && $task != 'register' && $task != 'authorize') {
		    	//echo 'hello';
		    	//echo($userLoginInfo);
		    	//echo "|";
		    	//echo $loginKey;
			    $value['status'] = 1;   //session无效
			    $value['message'] = $sys_lang[11];				    	
		    } else {
                if ($task != 'register' && $task != 'authorize' && $task != 'login' && $task != 'createRole' && $task != 'tjyzm' && empty($_SESSION['playersid'])) {
				    $value['status'] = 999;   //session无效
				    $value['message'] = $sys_lang[12];
                } else {			    	
			    	$value['status'] = 0;   //有效登录
                }
			    //echo 'hello2';
			    /*$SessionExpTime = time() + $session->SESS_LIFE;
                $Query = "UPDATE ol_sessions SET SessionExpTime = '".$SessionExpTime."' WHERE SessionKey = '"._get('sessionId')."' AND   SessionExpTime > " . time(); 
                //echo $session->DB_SELECT_DB;
                $Result = mysql_query($Query, $session->DB_SELECT_DB); //如果有效，延长session时间	  */
		    }				
		} else {
			$value['status'] = 999;   //session无效
			$value['message'] = $sys_lang[12];	
			//heroCommon::insertLog('enter');				
		}	
		if ($value['status'] == 1 || $value['status'] == 999) {
			$value['rsn'] = intval(_get('ssn'));
			return $value;
			//echo json_encode($value);
			//exit;
		} else {
			//$mc->replace(MC.$_SESSION['ucid'].'_session',$userLoginInfo,0,10);
			return true;
		}
	}	

	function checkLoginStatus($loginKey,$userId) {
		global $mc,$sys_lang;
		if (!($key = $mc->get(MC.$userId.'_login'))) {
			return true;
		} else {
			if ($key != $loginKey) {
			    $value['status'] = 1;   //session无效
			    $value['message'] = $sys_lang[11];						
			} else {
				$value['status'] = 0;
			}
		}
		if ($value['status'] == 1) {
			$value['rsn'] = intval(_get('ssn'));
			echo json_encode($value);
			exit;
		} else {
			return true;
		}		
	}
	
	//更新session时间，如果长时间不更新认为下线
	function updateSessionTime($userid) {
		global $db;		
		$db->query("UPDATE ".$this->tname('session')." SET sessiontime = '".time()."' WHERE userid = '$userid'");
	}
	
	//判断当前用户登录状态
	function checkauth() {
		global $_SGLOBAL, $_SC, $_SCONFIG, $_SCOOKIE, $_SN,$db;
	
		if($_SGLOBAL['mobile'] && $_GET['m_auth']) $_SCOOKIE['auth'] = $_GET['m_auth'];
		if($_SCOOKIE['auth']) {
			list($password, $uid) = explode("\t", authcode($_SCOOKIE['auth'], 'DECODE'));
			$_SGLOBAL['supe_uid'] = intval($uid);
			if($password && $_SGLOBAL['supe_uid']) {
				$query = $db->query("SELECT * FROM ".$this->tname('session')." WHERE uid='$_SGLOBAL[supe_uid]'");
				if($member = $this->fetch_array($query)) {
					if($member['password'] == $password) {
						$_SGLOBAL['supe_username'] = addslashes($member['username']);
						$_SGLOBAL['session'] = $member;
					} else {
						$_SGLOBAL['supe_uid'] = 0;
					}
				} else {
					$query = $db->query("SELECT * FROM ".$this->tname('member')." WHERE uid='$_SGLOBAL[supe_uid]'");
					if($member = $this->fetch_array($query)) {
						if($member['password'] == $password) {
							$_SGLOBAL['supe_username'] = addslashes($member['username']);
							$session = array('uid' => $_SGLOBAL['supe_uid'], 'username' => $_SGLOBAL['supe_username'], 'password' => $password);
							include_once(S_ROOT.'./source/function_space.php');
							insertsession($session);//登录
						} else {
							$_SGLOBAL['supe_uid'] = 0;
						}
					} else {
						$_SGLOBAL['supe_uid'] = 0;
					}
				}
			}
		}
		if(empty($_SGLOBAL['supe_uid'])) {
			clearcookie();
		} else {
			$_SGLOBAL['username'] = $member['username'];
		}
	}
	
	//获取到表名
	function tname($name) {
		global $_SC;
		return $_SC['tablepre'].$name;
	}
	
	/**
	 * 对单表的多数据写入
	 *
	 * @param string $tableName            不带前缀的表名
	 * @param array $insertArrays          需要写入的数据，数据要求写入的每条数据的键类型是一致的
	 * @return boolean
	 */
	function insertMutilArray($tableName, $insertArrays){
		global $db;
		$colArray  = array_keys($insertArrays[0]);
		foreach($colArray as $key=>$value){
			$colArray[$key] = "`{$value}`";
		}
		$colList   = implode(',', $colArray);
		$insertSql = 'insert into ' . $this->tname($tableName);
		$insertSql .= " ({$colList}) values ";
		foreach($insertArrays as $addArray){
			foreach($addArray as $key=>$value){
				$addArray[$key] = "'$value'";
			}
			$addList = implode(',', array_values($addArray));
			$insertSql .= "({$addList}),";
		}
		$insertSql = trim($insertSql, ',');
		return $db->query($insertSql);
	}
	
	//添加数据
	function inserttable($tablename, $insertsqlarr, $returnid=1, $replace = false, $silent = false) {//return;
		global $_SGLOBAL,$db,$common;	
		$insertkeysql = $insertvaluesql = $comma = '';
		foreach ($insertsqlarr as $insert_key => $insert_value) {
			$insertkeysql .= $comma.'`'.$insert_key.'`';
			$insertvaluesql .= $comma.'\''.$insert_value.'\'';
			$comma = ', ';
		}
		$method = $replace?'REPLACE':'INSERT';
		//$db->query("LOCK TABLE ".$this->tname($tablename)." WRITE");
		$db->query($method.' INTO '.$this->tname($tablename).' ('.$insertkeysql.') VALUES ('.$insertvaluesql.')', $silent?'SILENT':'');
		$id = $method.' INTO '.$this->tname($tablename).' ('.$insertkeysql.') VALUES ('.$insertvaluesql.')';
		//$common->insertLog($id);
		if($returnid && empty($replace)) {
			$id = $db->insert_id();
		}
		//$str = $method.' INTO '.$this->tname($tablename).' ('.$insertkeysql.') VALUES ('.$insertvaluesql.')';
		//heroCommon::insertLog('sql+'.$str);
		//$db->query("UNLOCK TABLES");
		
		//heroCommon::setError($_SERVER['REQUEST_URI'],$method.' INTO '.$this->tname($tablename).' ('.$insertkeysql.') VALUES ('.$insertvaluesql.')', $silent?'SILENT':'');
		
		return $id;
	}
	
	function cleanMemcacheOnlyByWhere($tableName, $whereArray){
		global $_SGLOBAL, $db, $mc;
		
		// 必须要有查询条件才能使用该接口
		if(count($whereArray)<1){
			return false;
		}
		
		// 获得查询where字符串和memcache的后缀
		$where  = '';
		$suffix = $tableName;
		
		ksort($whereArray);
		foreach($whereArray as $colName=>$value){
			if(is_numeric($value)){
				$where .= $colName.'='.$value.' and ';
			}
			else{
				$where .= $colName.'=\''.$value.'\' and ';
			}
			
			$suffix .= '_'.$value;
		}
		
		$mc->delete($suffix);
		
		return $suffix;
	}
	
	/**
	 * @author kknd li
	 * 针对某一个互斥键或者是按默认session_id为互斥键加锁
	 * @param string $mutexKey
	 */
	function userMutex($mutexKey=null){
		global $mc;
		$mutexKey = is_null($mutexKey)?session_id():$mutexKey;
		$key = MC.'_mutex_lock_'.$mutexKey;
		
		// 尝试加上一个10秒的互斥锁
		// 如果不能加上互斥锁那么等待一段时间在尝试加锁
		// 这个累加时间是递增的以防止中断共振
		$interval = 0;
		while(($canLock = $mc->add($key, time(), 0, 10))===false){
			$interval += mt_rand(50000,250000);
			
			usleep($interval);
		}
		
		return true;
	}
	
	/**
	 * @author kknd li
	 * 解除userMutex加上的互斥锁
	 * @param string $mutexKey
	 */
	function unLockMutex($mutexKey=null){
		global $mc;
		$mutexKey = is_null($mutexKey)?session_id():$mutexKey;
		$key = MC.'_mutex_lock_'.$mutexKey;
		
		$mc->delete($key);
	}
	
	
	
	/**
	 * @author kknd li
	 * 获得由where条件确定的键得到的
	 * @param string $tableName
	 * @param array  $whereArray
	 */
	function sltTableByWhereWithMem($tableName, $whereArray){
		global $_SGLOBAL, $db, $mc;
		
		// 必须要有查询条件才能使用该接口
		if(count($whereArray)<1){
			return false;
		}
		
		// 获得查询where字符串和memcache的后缀
		$where  = '';
		$suffix = $tableName;
		
		ksort($whereArray);
		foreach($whereArray as $colName=>$value){
			if(is_numeric($value)){
				$where .= $colName.'='.$value.' and ';
			}
			else{
				$where .= $colName.'=\''.$value.'\' and ';
			}
			
			$suffix .= '_'.$colName.'_'.$value;
		}
	
		// 查询Memcache看是否存在对应键的值，如果有就直接返回
		$result = $mc->get(MC.$suffix);
		if(!empty($result)){
			return $result;
		}
		
		// 查询数据库看是否有对应的数据，如果有就返回并且存储到Memcache
		if($where != ''){
			$where = substr($where, 0, strlen($where)-4);
			$where = ' where '.$where;
		}
		$result = $db->query('select * from '.$this->tname($tableName).$where);
		$returnArray = array();
		while(($row = mysql_fetch_assoc($result))!==false){
			$returnArray[] = $row;
		}
		$db->free_result ( $result );
		
		if(!empty($returnArray)){
			$mc->set(MC.$suffix, $returnArray, 0, 3600);
		}
		
		return $returnArray;
	}
	
	/**
	 * @author kknd li
	 * 兼容sltTableOnlyOneWithMem的方法，提供通过
	 *
	 * @param string $tableName				表名
	 * @param array $whereArray				二维条件数组，第一维的键是查询条件字段
	 * 										第二维是要查询的值可以为字符串或者数组，如果有多个数组则count()时要一致
	 */
	function sltTableInKeyWithMem($tableName, $whereArray){
		global $_SGLOBAL, $db, $mc;
		
		// 必须要有查询条件才能使用该接口
		if(count($whereArray)<1){
			return false;
		}
		
		// 如果有多个数组则count()时要一致
		$aCount = 0;
		foreach ($whereArray as $condition){
			if(is_array($condition)){
				if(0 != $aCount && $aCount != count($condition)){
					return false;
				}
				$aCount = count($condition);
			}
		}
		// 最少也要查一行数据嘛，是不是啊
		$aCount = 0==$aCount?1:$aCount;
		
		// 获得查询memcache的键
		ksort($whereArray);
		$mc_key = array();
		$whereStrList    = array();
		
		for($i=0; $i<$aCount; $i++){
			$mc_key[$i] = MC.$tableName;
			$whereStrList[$i]    = '';
		}
		foreach($whereArray as $colName=>$value){
			for($i=0; $i<$aCount; $i++){
				if(is_array($value)){
					$mc_key[$i] .= '_'.$value[$i];
					$whereStrList[$i] .= $colName.'=\''.$value[$i].'\' and ';
				}
				else{
					$mc_key[$i] .= '_'.$value;
					$whereStrList[$i] .= $colName.'=\''.$value.'\' and ';
				}
			}
		}
		
		// 检查结果并返回数据
		$mcResult = $mc->get($mc_key);
		if($mcResult){
			foreach($mcResult as $key=>$value){
				if(empty($value))
					unset($mcResult[$key]);
			}
		}
		if($aCount == count($mcResult)){
			return $mcResult;
		}
		
		// 过滤已经有的数据
		foreach($mc_key as $k=>$v){
			if(array_key_exists($v, $mcResult)){
				unset($whereArray[$k]);
			}
		}
		
		// 查询并记录到memcache中
		foreach($whereStrList as $k=>$whereStr){
			if($whereStr != ''){
				$whereStr = substr($whereStr, 0, strlen($whereStr)-4);
				$whereStr = ' where '.$whereStr;
			}
			$result = $db->query('select * from '.$this->tname($tableName).$whereStr.' limit 1');
			$rows = $db->fetch_array ( $result );
			$db->free_result ( $result );
			
			if(!empty($rows)){
				$mc->set(MC.$mc_key[$k], $rows, 0, 3600);
			}
			$mcResult[$mc_key[$k]] = $rows;
		}
		
		return $mcResult;
	}
	
	/**
	 * @author kknd li
	 * 从数据库和Memcache中捆绑查询获得一行数据
	 * 查询条件仅且只有通过 and 方式来进行查询
	 * Memcache的主键名为:表名_$whereValue1[_$whereValue2[...]]
	 * 主键名的后缀部分采用ksort($whereArray) 进行排序后的结果
	 * @param String $tableName
	 * @param array $whereArray array(colName=>value,[...])
	 */
	function sltTableOnlyOneWithMem($tableName, $whereArray){
		global $_SGLOBAL, $db, $mc;
		
		// 必须要有查询条件才能使用该接口
		if(count($whereArray)<1){
			return false;
		}
		
		// 获得查询where字符串和memcache的后缀
		$where  = '';
		$suffix = $tableName;
		
		ksort($whereArray);
		foreach($whereArray as $colName=>$value){
			if(is_numeric($value)){
				$where .= $colName.'='.$value.' and ';
			}
			else{
				$where .= $colName.'=\''.$value.'\' and ';
			}
			
			$suffix .= '_'.$value;
		}
		
		
		// 查询Memcache看是否存在对应键的值，如果有就直接返回
		$result = $mc->get(MC.$suffix);
		if(!empty($result)){
			return $result;
		}
		
		// 查询数据库看是否有对应的数据，如果有就返回并且存储到Memcache
		if($where != ''){
			$where = substr($where, 0, strlen($where)-4);
			$where = ' where '.$where;
		}
		$result = $db->query('select * from '.$this->tname($tableName).$where.' limit 1');
		$rows = $db->fetch_array ( $result );
		$db->free_result ( $result );
		
		if(!empty($rows)){
			$mc->set(MC.$suffix, $rows, 0, 3600);
		}
		return $rows;
	} 
	
	/**
	 * @author kknd li
	 * 插入新的数据同时更新memcache
	 * @param string $tableName
	 * @param array $insertArray          要插入数据库的完整数据
	 * @param array $whereArray           查找数据时所用的键及对应值，这是为了生成memcache的键
	 */
	function intTableOnlyOneWithMem($tableName, $insertArray, $whereArray){
		global $_SGLOBAL, $db, $mc;
		
		// 必须要有查询条件才能使用该接口
		if(count($whereArray)<1){
			return false;
		}
	
		
		// 获得查询where字符串和memcache的后缀
		$where  = '';
		$suffix = $tableName;
		
		ksort($whereArray);
		foreach($whereArray as $colName=>$value){
			if(is_numeric($value)){
				$where .= $colName.'='.$value.' and ';
			}
			else{
				$where .= $colName.'=\''.$value.'\' and ';
			}
			
			$suffix .= '_'.$value;
		}
		
		// 更新数据
		$this->inserttable($tableName, $insertArray);
		$mc->set(MC.$suffix, $insertArray, 0, 3600);
	}
	
	function deleteTableOnlyOneWithMem($tableName, $whereArray){
		global $_SGLOBAL, $db, $mc;
		
		// 必须要有查询条件才能使用该接口
		if(count($whereArray)<1){
			return false;
		}
	
		
		// 获得查询where字符串和memcache的后缀
		$where  = '';
		$suffix = $tableName;
		
		ksort($whereArray);
		foreach($whereArray as $colName=>$value){
			if(is_numeric($value)){
				$where .= $colName.'='.$value.' and ';
			}
			else{
				$where .= $colName.'=\''.$value.'\' and ';
			}
			
			$suffix .= '_'.$value;
		}
		
		// 更新数据
		$this->deletetable($tableName, $whereArray);
		$mc->delete(MC.$suffix);
	}
	
	/**
	 * @author kknd li
	 * 同时更新数据库和memcache对应的数据
	 * @param string $tableName
	 * @param array $updateArray           需要更新的数据
	 * @param array $whereArray            更新条件，同时也是memcache键的生成条件
	 */
	function updateTableOnlyOneWithMem($tableName, $updateArray, $whereArray){
		global $_SGLOBAL, $db, $mc;
		
		// 必须要有查询条件才能使用该接口
		if(count($whereArray)<1){
			return false;
		}
	
		
		// 获得查询where字符串和memcache的后缀
		$where  = '';
		$suffix = $tableName;
		
		ksort($whereArray);
		foreach($whereArray as $colName=>$value){
			if(is_numeric($value)){
				$where .= $colName.'='.$value.' and ';
			}
			else{
				$where .= $colName.'=\''.$value.'\' and ';
			}
			
			$suffix .= '_'.$value;
		}
		
		// 更新数据
		$this->updatetable($tableName, $updateArray, $whereArray);
		
		if(($update = $mc->get(MC.$suffix)) !== false){
			foreach($updateArray as $key=>$value){
				$update[$key] = $value;
			}
			$mc->set(MC.$suffix, $update, 0, 3600);
		}
	}
	
	//更新数据
	function updatetable($tablename, $setsqlarr, $wheresqlarr, $silent=0) {//return;
		global $_SGLOBAL,$db;	
		$setsql = $comma = '';
		if (is_array($setsqlarr)) {
		   foreach ($setsqlarr as $set_key => $set_value) {
			   if(is_array($set_value)) {
				   $setsql .= $comma.'`'.$set_key.'`'.'='.$set_value[0];
			   } else {
				   $setsql .= $comma.'`'.$set_key.'`'.'=\''.$set_value.'\'';
			   }
			$comma = ', ';
		   }			
		} else {
			$setsql = $setsqlarr;
		}
		$where = $comma = '';
		$stop = array();
		if(empty($wheresqlarr)) {
			//$where = '1';
			//echo 'hello';
			return false;
		} elseif(is_array($wheresqlarr)) {
			foreach ($wheresqlarr as $key => $value) {
				$where .= $comma.'`'.$key.'`'.'=\''.$value.'\'';
				$comma = ' AND ';
				if (is_null($key) || is_null($value)) {
					$stop[] = 1;
				}
			}
		} else {
			$where = $wheresqlarr;
		}
		if (empty($where) || is_numeric($where) || in_array(1,$stop)) {
			return false;
		}
		//$str = 'UPDATE '.$this->tname($tablename).' SET '.$setsql.' WHERE '.$where;
		//$db->query("LOCK TABLE ".$this->tname($tablename)." WRITE");		
		$db->query('UPDATE '.$this->tname($tablename).' SET '.$setsql.' WHERE '.$where, $silent?'SILENT':'');
		$result = mysql_affected_rows();
		//heroCommon::insertLog('UPDATE '.$this->tname($tablename).' SET '.$setsql.' WHERE '.$where, $silent?'SILENT':'');
		//echo 'UPDATE '.$this->tname($tablename).' SET '.$setsql.' WHERE '.$where, $silent?'SILENT':'';
		//echo '<br>';
		//$db->query("UNLOCK TABLES");

		//heroCommon::setError($_SERVER['REQUEST_URI'],'UPDATE '.$this->tname($tablename).' SET '.$setsql.' WHERE '.$where, $silent?'SILENT':'');
		
		return $result;		
	}
	
	//删除数据
	function deletetable($tablename,$wheresqlarr) {
		global $_SGLOBAL,$db;
		$where = $comma = '';
	    if(empty($wheresqlarr)) {
			//$where = '1';
			return false;
		} elseif(is_array($wheresqlarr)) {
			foreach ($wheresqlarr as $key => $value) {
				$where .= $comma.'`'.$key.'`'.'=\''.$value.'\'';
				$comma = ' AND ';
			}
		} else {
			$where = $wheresqlarr;
		}
		//$db->query("LOCK TABLE ".$this->tname($tablename)." WRITE");
		$db->query('DELETE FROM '.$this->tname($tablename).' WHERE '.$where);		
		//$db->query("UNLOCK TABLES");	
        //return $result;
		//heroCommon::setError($_SERVER['REQUEST_URI'],'DELETE FROM '.$this->tname($tablename).' WHERE '.$where);
	}
	
	//时间格式化
	function sgmdate($dateformat, $timestamp='', $format=0) {
		global $_SCONFIG, $_SGLOBAL;
		if(empty($timestamp)) {
			$timestamp = $_SGLOBAL['timestamp'];
		}
		$timeoffset = strlen($_SGLOBAL['member']['timeoffset'])>0?intval($_SGLOBAL['member']['timeoffset']):intval($_SCONFIG['timeoffset']);
		$result = '';
		if($format) {
			$time = $_SGLOBAL['timestamp'] - $timestamp;
			if($time > 24*3600) {
				$result = gmdate($dateformat, $timestamp + $timeoffset * 3600);
			} elseif ($time > 3600) {
				$result = intval($time/3600).lang('hour').lang('before');
			} elseif ($time > 60) {
				$result = intval($time/60).lang('minute').lang('before');
			} elseif ($time > 0) {
				$result = $time.lang('second').lang('before');
			} else {
				$result = lang('now');
			}
		} else {
			$result = gmdate($dateformat, $timestamp + $timeoffset * 3600);
		}
		return $result;
	}
	
	//字符串时间化
	function sstrtotime($string) {
		global $_SGLOBAL, $_SCONFIG;
		$time = '';
		if($string) {
			$time = strtotime($string);
			if(gmdate('H:i', $_SGLOBAL['timestamp'] + $_SCONFIG['timeoffset'] * 3600) != date('H:i', $_SGLOBAL['timestamp'])) {
				$time = $time - $_SCONFIG['timeoffset'] * 3600;
			}
		}
		return $time;
	}
	
	//获取文件内容
	function sreadfile($filename) {
		$content = '';
		if(function_exists('file_get_contents')) {
			$content = file_get_contents($filename);
		} else {
			if($fp = fopen($filename, 'r')) {
				$content = fread($fp, filesize($filename));
				fclose($fp);
			}
		}
		return $content;
	}
	
	//写入文件
	function swritefile($filename, $writetext, $openmod='w') {
		if($fp = fopen($filename, $openmod)) {
			flock($fp, 2);
			fwrite($fp, $writetext);
			fclose($fp);
			return true;
		} else {
			runlog('error', "File: $filename write error.");
			return false;
		}
	}
	
	//判断字符串是否存在
	function strexists($haystack, $needle) {
		return !(strpos($haystack, $needle) === FALSE);
	}
	
	
	//获取文件名后缀
	function fileext($filename) {
		return strtolower(trim(substr(strrchr($filename, '.'), 1)));
	}
	
	//去掉slassh
	function sstripslashes($string) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = sstripslashes($val);
			}
		} else {
			$string = stripslashes($string);
		}
		return $string;
	}
	
	//编码转换
	function siconv($str, $out_charset, $in_charset='') {
		global $_SC;	
		$in_charset = empty($in_charset)?strtoupper($_SC['charset']):strtoupper($in_charset);
		$out_charset = strtoupper($out_charset);
		if($in_charset != $out_charset) {
			if (function_exists('iconv') && ($outstr = iconv("$in_charset//IGNORE", "$out_charset//IGNORE", $str))) {
				return $outstr;
			} elseif (function_exists('mb_convert_encoding') && ($outstr = mb_convert_encoding($str, $out_charset, $in_charset))) {
				return $outstr;
			}
		}
		return $str;//转换失败
	}
	
	//指向请求的功能模块
	function getCmd($option) {
		global $common, $_SC;
		include(OP_ROOT.'hero_'.$option.DIRECTORY_SEPARATOR.$option.'.php');		
		//echo S_ROOT.'components'.DIRECTORY_SEPARATOR.$option.DIRECTORY_SEPARATOR.$appName.'.php';
	}
	
	//更新资源价格
	function updateResourcePrice() {
	  global $db;
	  $result = $db->query("SELECT * FROM ".$this->tname('resource_price'));
	  $rows = $db->fetch_array($result);
	  $value['last_updateTime'] = time();
	  if (!$rows) {
		  $value['wood_price'] = rand(5,15) / 10;
		  $value['wood_next_price']= rand(5,15) / 10;
		  $value['stone_price'] = rand(5,15) / 10;
		  $value['stone_next_price'] = rand(5,15) / 10;  
		  $value['iron_price'] = rand(5,15) / 10;
		  $value['iron_next_price'] = rand(5,15) / 10;
		  $this->inserttable('resource_price',$value);   	  				 
	  } else {
		  $price['wood_price'] = $rows['wood_price'];
		  $price['wood_next_price'] = $rows['wood_next_price'];
		  $price['stone_price'] = $rows['stone_price'];
		  $price['stone_next_price'] = $rows['stone_next_price'];
		  $price['iron_price'] = $rows['iron_price'];
		  $price['iron_next_price'] = $rows['iron_next_price'];
		  //$last_updateTime = $rows['last_updateTime'];
		  $price['last_updateTime'] = $rows['last_updateTime'];  
		  $nowTime = time();
		  if (($nowTime - $price['last_updateTime']) >= 3600) {
			  $price['wood_price'] = $price['wood_next_price'];
			  $price['wood_next_price'] = rand(5,15) / 10;
			  $price['stone_price'] = $price['stone_next_price'];
			  $price['stone_next_price'] = rand(5,15) / 10;
			  $price['iron_price'] = $price['iron_next_price'];
			  $price['iron_next_price'] = rand(5,15) / 10;
			  $price['last_updateTime'] = $nowTime;
			  $wheresqlarr['intID'] = $rows['intID'];
			  $this->updatetable('resource_price',$price,$wheresqlarr);
		  }
	  }
	}

	//字符编码
	function encode($str,$IsFlash='') {
		if ($IsFlash == 1) {
			return base64_encode($str);
		} else {
			return urlencode($str);
		}
	}
	
	//字符解码
	static function decode($str,$IsJava='') {		
		if ($IsJava == 1) {
			return base64_decode($str);
		} else {
			return urldecode($str);
		}
	}

	//写日志
	static function writelog($request,$return,$type=0,$userid=0) {
		global $common,$mc,$_SGLOBAL, $_LOG_INFO,$G_PlayerMgr;

		// 如果用户没有登录或者登录是没有获取玩家信息等级设为-1
		$level = -1;
		$pid = isset($_SESSION['playersid'])?$_SESSION['playersid']:0;
		if($pid && $player = $G_PlayerMgr->GetPlayer($pid )){
			$level = $player->baseinfo_['player_level'];
		}
	
		// 如果没有定义用户id同时session中没有定义就为-1
		$userid = $userid==0?(isset($_SESSION['userid'])?$_SESSION['userid']:-1):$userid;
	
		if($type != 0) {
			$letters_trade['type'] = 1;
		}
		// 格式化请求的url
		if(strpos($request, '?') === false){
			if(isset($_GET)&&count($_GET)==0){
				$request .= '?';
				foreach($_REQUEST as $key=>$value){
					$request .= $key.'='.$value.'&';
				}
				$request = trim($request, '&?');
			}else{
				$request = 'app.php?task=noget&option=nourl';
			}
		}
			
		$letters_trade['request_url'] = $request;
		$letters_trade['create_time'] = $_SGLOBAL['timestamp'];
		$letters_trade['level'] = $level;
	
		$letters_trade['userid'] = $userid;
		$letters_trade['result'] = $return;

		$letters_trade['serv_add'] = isset($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:'127.0.0.1';
			
		// 获得运行的毫秒数
		$currentTime = explode(' ', microtime());
		$currentTime = floatval($currentTime[1]) + floatval($currentTime[0]);
		$letters_trade['run_time'] = $currentTime - $_SGLOBAL['supe_starttime'];
		$letters_trade['sqltimes'] = self::$sqlnum;
	
		$log_path = $_LOG_INFO['path'] . $_LOG_INFO['prefix'] . date('Y_m_d_H',$_SGLOBAL['timestamp']).'_'.intval($_SGLOBAL['timestamp']/$_LOG_INFO['split_t']);

		$log_json_value = json_encode($letters_trade);
		$flag = false;
		for($i=1; $i<=5; $i++){
			$logHandle = fopen($log_path. "_$i", 'a');
			if(flock($logHandle, LOCK_EX|LOCK_NB)){
				$w_long = fwrite($logHandle, $log_json_value."\n");
				if(0 < $w_long){
					$flag = true;
					flock($logHandle, LOCK_UN);
					fclose($logHandle);
					break;
				}
			}
			fclose($logHandle);
		}

		if(!$flag){
			$log['logValue'] = $log_json_value;
			$common->inserttable('bck_log', $log);
		}
	}
	
	static function writeucfriends($request,$return) {
		global $common,$_SGLOBAL;
		$letters_trade['request_url'] = $request;
		$letters_trade['return_data'] = addslashes($return);
		$letters_trade['create_time'] = $_SGLOBAL['timestamp'];	
		$common->inserttable('log',$letters_trade);
	}
	
	//记录执行错误
	static function setError($request,$sql) {
		global $db;
		$letters_error['request_url'] = $request;
		$letters_error['sql'] = mysql_escape_string($sql);
		$letters_error['create_time'] = time();
		//echo("INSERT INTO ol_error (request_url,sql,create_time) VALUES ('".$letters_error['request_url']."','".$letters_error['sql']."','".$letters_error['create_time']."')");
		
		//$db->query("INSERT INTO ol_error (request_url,sql,create_time) VALUES ('".$letters_error['request_url']."','".$letters_error['sql']."','".$letters_error['create_time']."')");
	}
	
	//文本记录日志
	static function insertLog($str,$path='gb') {
		$date=date("Y-m-d H:i:s");
        $from = $_SERVER['REQUEST_URI']; 
        //$text=trim($gb_text);//去掉留言内容后面的空格.
        $fp=fopen($path.date('Ymd',time()).".txt","a");
        $str =$date."^^^".$from."^^^".$str."\n";
        fwrite($fp,$str);
        fclose($fp);
	}
	
	//更新memcache
	static function updateMemCache($memKey,$updateInfo,$type=false) {
	    global $mc,$G_PlayerMgr;
	    if (!($info = $mc->get($memKey))) {
	    	return false;
	    } else {
	    	$keyInfo = explode('_',$memKey);
	    	if (is_numeric($keyInfo[1]) && count($keyInfo) == 2) {
	    		$player = $G_PlayerMgr->GetPlayer($keyInfo[1]);
	    		$player -> ModifyBaseInfo($updateInfo);
	    	}
	    	if (is_array($info)) {
	    		foreach ($updateInfo as $key => $value) {
	    			//echo $key.'<br>';
	    			$info[$key] = $value;
	    		}
	    		$mc->set($memKey,$info,0,3600);
	    	} else {
	    		$mc->set($memKey,$updateInfo,0,3600);
	    	}
	    }
	}
	
	//写日志
	static function writelog_login($type) {
		/*global $common,$mc;
		
		$level = 0;
		$memory_player = $mc->get(MC.$_SESSION['playersid']);
		if($memory_player <> "") {
			$level = $memory_player['player_level'];
		}
		
		$letters_trade['type'] = $type;
		$letters_trade['create_time'] = time();	
		$letters_trade['level'] = $level;
		$common->inserttable('log_login',$letters_trade);*/
	}
	
	//写队列
	static function setQueue() {
		global $common,$mc;
		if(empty($_SESSION['ucid'])) {
			return '';
		}
		if(!empty($_SESSION['playersid'])) {
			$memory_player = $mc->get(MC.$_SESSION['playersid']);
			if($memory_player <> "") {
				$level = $memory_player['player_level'];
			}else{
				$level = 0;
			}
		}else{
			$memory_player = 0;
			$level = 0;
		}
		
		
		$task = strtolower(_get('task'));
		if ($task != 'returnroledatatouser') {
			$hr = httpsqs_connect("127.0.0.1", 1218);
			$event = json_encode(array('uid'=>_get('userId'),'task'=>$task,'level'=>$level,'ucid'=>$_SESSION['ucid']));
			$putRes = httpsqs_put($hr, "Queue", $event, "UTF-8");
		}
		//heroCommon::insertLog("------"._get('userId')."------tadk:".$task."------");
	}
	
	//清理数据
	static function cleanData() {
		global $db,$mc,$common;
		$date_now=date("Y-m-d H:i:s",time());
		$year=((int)substr($date_now,0,4));//取得年份
		$month=((int)substr($date_now,5,2));//取得月份
		$day=((int)substr($date_now,8,2));//取得几号
		$date_now1 = mktime(0,0,0,$month,$day,$year);
		$temp_time = $date_now1 - (86400 * 2);
		
		$db->query("delete from ".$common->tname('social_reduction')." where create_time <= '".$temp_time."'");
	}
	
	//控制并发事件
	public static function bfkz($key,$outTime,$message) {
		global $mc;
		if ($mc->add($key,1,0,$outTime) === false) {
			return $message;
		} else {
			return 'go';
		}
	}
	
	//数组转xml
	public static function arrtoxml($arr,$dom=0,$item=0){
	    if (!$dom){
	        $dom = new DOMDocument("1.0","utf-8");
	    }
	    if(!$item){
	        $item = $dom->createElement("root"); 
	        $dom->appendChild($item);
	    }
	    foreach ($arr as $key=>$val){
	        $itemx = $dom->createElement(is_string($key)?$key:"item");
	        $item->appendChild($itemx);
	        if (!is_array($val)){
	            $text = $dom->createTextNode(iconv("GBK", "UTF-8",$val));
	            $itemx->appendChild($text);
	        }else {
	            arrtoxml(iconv("GBK", "UTF-8",$val),$dom,$itemx);
	        }
	    }
	    return $dom->saveXML();
	}
	//异步请求
	public static function ybqq ($url) {
        $fp = fsockopen("127.0.0.1", 80, $errno, $errstr, 30);
        $out = "GET /zyd2.php HTTP/1.1\r\n";
        $out .= "Host: 127.0.0.1\r\n";
        $out .= "User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:14.0) Gecko/20100101 Firefox/14.0.1\r\n";
        $out .= "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n";
        $out .= "Accept-Language: zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3\r\n";
        $out .= "Accept-Encoding: gzip, deflate\r\n";
        $out .= "Connection: keep-alive\r\n";
        $out .= "Cache-Control: max-age=0\r\n\r\n";
        fwrite($fp, $out);
        fgets($fp, 128);
        fclose($fp);
}

	
	public static function  ybqq2($url) { 
	    $arrUrl=parse_url($url);
	    $method=strtoupper('GET');//header 里的传参方式严格区分大小写 必须为GET 或POST
	    $host=$arrUrl['host']?$arrUrl['host']:'localhost';
	    $port = $arrUrl['port']? $arrUrl['port'] : 80;
	    $query=$arrUrl['query']?$arrUrl['query']:'';
	    $path=$arrUrl['path']?$arrUrl['path']:'/';
	    //$path=$arrUrl['path']?$arrUrl['path'].'/'.$path:'/';
	    $query=$path."?".$query;
	    $header="";
	    //$query=$path."?".$query;
	    $header.="$method $query";
	    $header .= " HTTP/1.0\r\n";
	    $header .= "Host: ". $host . "\r\n"; //HTTP 1.1 Host域不能省略
	    $header .= "Connection:Close\r\n";
	    $cookie_str='';
	    $post_str="";
	    $header.= "Content-Type: application/x-www-form-urlencoded\r\n";//POST数据
	    $header .= "Content-Length: ". strlen($post_str) ." \r\n";//POST数据的长度
	    $header .= "Pragma: no-cache\r\n";
	    $header .= "Cache-Control: no-cache\r\n\r\n";//此处至少要有两个\r\n 否则将报400错误
	    //$header .= $post_str."\r\n"; //传递POST数据
	    //echo nl2br($header);
	    $fsp=fsockopen($host,$port, $errno, $errstr, 30);	    
	    if(!$fsp){
	        return "101";//链接失败
	    }
	    while (!feof($fsp)) {
	    	fgets($fsp, 128);
	    }
	    //stream_set_blocking($fsp,1);
	    //stream_set_timeout($fsp,3000);
	    fwrite($fsp,$header);
	}	
	//战斗数据格式变更
	public static function arr_foreach ($arr) {
		foreach ($arr as $key => $val2 ) {
			foreach ($val2 as $val) {
				if (!empty($val[0])) {
					//print_r($val);
					foreach ($val as $val3) {
						if (!isset($val3['dfxg']) && !isset($val3['jfxg']) && !isset($val3['zdfxg'])) {
							$str[] = heroCommon::ptxg($val3);
						} else {
							$str[] = heroCommon::dfxg($val3);
						}						
					}
				} else {
					if (!isset($val['dfxg']) && !isset($val['jfxg']) && !isset($val['zdfxg'])) {
						$str[] = heroCommon::ptxg($val);
					} else {
						$str[] = heroCommon::dfxg($val);
					}
				}
				$val3 = null;							
			}
			$return[$key] = $str; 
			$val2 = null;
			$val = null;	
			$str = null;		
		}
		return $return;
	}
	
	//对方效果数值处理
	public static function dfxg($dfxg) {
		foreach ($dfxg as $key => $value) {
			if (!is_array($value)) {
				$v[] = $value;	
			} else {				
				foreach ($value as $value2) {
					foreach ($value2 as $value3) {
						if (is_array($value3)) {
							foreach ($value3 as $value4) {
								$a[] = $value4;
							}
						} else {
							$a[] = $value3;	
						}	
						$value4 = null;					
					}
					$value3 = null;
					$value2 = null;
				}
				if ($key === 'dfxg') {
					$v[] = 'd:'.implode('_',$a).'d:';
				} elseif ($key === 'jfxg') {
					$v[] = 'j:'.implode('_',$a).'j:';
				} elseif ($key === 'zdfxg') {
					$v[] = 'z:'.implode('_',$a).'z:';;
				}
				$a = null;
			}			
			$value2 = null;
			$value = null;		
		}
		return implode('.',$v);
	}

	//普通效果数值处理
	public static function ptxg($dfxg) {		
		foreach ($dfxg as $value) {		
			$v[] = $value;
		}
		return implode('.',$v);
	}		
}
?>
