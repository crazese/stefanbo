<?php
function dbconnect($dbhost, $dbuser, $dbpw, $dbname = '',$charset = 'utf8', $pconnect = 0, $halt = TRUE) {
	if($pconnect) {
		if(!$link = @mysql_pconnect($dbhost, $dbuser, $dbpw)) {
			$halt && halt('Can not connect to MySQL server');
		}
	} else {
		if(!$link = @mysql_connect($dbhost, $dbuser, $dbpw, 1)) {
			$halt && halt('Can not connect to MySQL server');
		}
	}
	if(version($link) > '4.1') {
		if($charset) {
			@mysql_query("SET character_set_connection=$charset, character_set_results=$charset, character_set_client=binary", $link);
		}
		if(version($link) > '5.0.1') {
			@mysql_query("SET sql_mode=''", $link);
		}
	}
	if($dbname) {
		@mysql_select_db($dbname, $link);
	}
	return $link;
	//echo $this->link;
}
function version($link) {
   return mysql_get_server_info($link);
}

function halt($message = '', $sql = '') {
	$dberror = error();
	$dberrno = errno();
	echo "<div style=\"position:absolute;font-size:11px;font-family:verdana,arial;background:#EBEBEB;padding:0.5em;\">
				<b>MySQL Error</b><br>
				<b>Message</b>: $message<br>
				<b>SQL</b>: $sql<br>
				<b>Error</b>: $dberror<br>
				<b>Errno.</b>: $dberrno<br>
				</div>";
	exit();
}

function errno($link) {
	return intval(($link) ? mysql_errno($link) : mysql_errno());
}
function error($link) {
	return (($link) ? mysql_error($link) : mysql_error());
}
$db_session_link = dbconnect($_SC['dbhostSeesion'], $_SC['dbuserSession'], $_SC['dbpwSession'], $_SC['dbnameSession'],$_SC['charset']);
mysql_query("set names 'utf8'",$db_session_link);
?>