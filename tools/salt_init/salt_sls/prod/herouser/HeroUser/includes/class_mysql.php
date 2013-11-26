<?php
$__dberr = false;

class dbstuff {
	private $querynum = 0;
	private $link;
	private $charset = 'utf8';
  public $sqls='';
	function dbconnect($dbhost, $dbuser, $dbpw, $dbname = '',$charset = 'utf8', $pconnect = 0, $halt = TRUE) {
		if($pconnect) {
			if(!$this->link = mysql_pconnect($dbhost, $dbuser, $dbpw)) {
				$halt && $this->halt('Can not connect to MySQL server');
			}
		} else {
			if(!$this->link = mysql_connect($dbhost, $dbuser, $dbpw, 1)) {
				$halt && $this->halt('Can not connect to MySQL server');
			}
		}
    
    mysql_query("set names 'utf8'", $this->link);

		/*if($this->version() > '4.1') {
			if($this->charset) {
				mysql_query("SET character_set_connection=$this->charset, character_set_results=$this->charset, character_set_client=binary", $this->link);
			}
			if($this->version() > '5.0.1') {
				mysql_query("SET sql_mode=''", $this->link);
			}
		}*/
		if($dbname) {
			mysql_select_db($dbname, $this->link);
		}
		//echo $this->link;
	}
	
	function select_db($dbname) {
		return mysql_select_db($dbname, $this->link);
	}

	function fetch_array($query, $result_type = MYSQL_ASSOC) {
		return mysql_fetch_array($query, $result_type);
	}

	function query($sql, $type = '') {
		if(!$this->link) {
			global $_SC;
			$this->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname'],'utf8', true);
		}

		heroCommon::addSqlNum($sql);
		$func = $type == 'UNBUFFERED' && function_exists('mysql_unbuffered_query') ?
			'mysql_unbuffered_query' : 'mysql_query';
		//$this->sqls .= $sql.":";
		$time1 = microtime(true);
		if(!($query = $func($sql, $this->link)) && $type != 'SILENT') {
			dbstuff::halt('MySQL Query Error', $sql);
		}
		//$this->sqls .= (microtime(true)-$time1)."\n";
		$this->querynum++;
    
		return $query;
	}

	function affected_rows() {
		return mysql_affected_rows($this->link);
	}

	function error() {
		return (($this->link) ? mysql_error($this->link) : mysql_error());
	}

	function errno() {
		return intval(($this->link) ? mysql_errno($this->link) : mysql_errno());
	}

	function result($query, $row) {
		$query = mysql_result($query, $row);
		return $query;
	}

	function num_rows($query) {
		$query = mysql_num_rows($query);
		return $query;
	}

	function num_fields($query) {
		return mysql_num_fields($query);
	}

	function free_result($query) {
		return mysql_free_result($query);
	}

	function insert_id() {
		return ($id = mysql_insert_id($this->link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}

	function fetch_row($query) {
		$query = mysql_fetch_row($query);
		return $query;
	}

	function fetch_fields($query) {
		return mysql_fetch_field($query);
	}

	function version() {
		return mysql_get_server_info($this->link);
	}

	function close() {
    if($this->link) return mysql_close($this->link);
		return true;
	}
	
	function halt($message = '', $sql = '') {
    $__dberr = true;
		$dberror = $this->error();
		$dberrno = $this->errno();
		
		$showMessage['rsn'] = intval($_REQUEST['ssn']);// fix notice warrning
		$showMessage['status'] = 5;
		$showMessage['message'] = '未知的错误';
        echo json_encode($showMessage);
		$this->query("rollback");
		$logMessage  = array('status'=>5, 'message'=>"errno:{$dberrno},err:{$dberror},sql:{$sql}");
		heroCommon::writelog($_SERVER['REQUEST_URI'],json_encode($logMessage));

	    if(function_exists('DieNow')) 
	    	DieNow();
	}
}

?>