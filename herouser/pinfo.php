<?php
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('OP_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR);
//基本文件
include(S_ROOT.'./config.php');
header("Content-Type:text/html; charset=utf-8");
$_SGLOBAL = array();
//设置时区
date_default_timezone_set('Asia/Shanghai');
$sql = "select";
include(S_ROOT.'./includes/class_mysql.php');
include(S_ROOT.'./includes/class_common.php');
$db = new dbstuff;
$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname']);
$common = new heroCommon;
function _get($str){
  static $strkeys=array('AccountID','GameServerID','Timestamp','Sign');
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
$Key = '6xmua6$%^&fds5po';
$AccountID = _get('AccountID');
$GameServerID = _get('GameServerID');
$Timestamp = _get('Timestamp');
$Sign = _get('Sign');
//Sign = MD5({AccountID}_{GameServerID}_{Timestamp}_{Key})
if (md5($AccountID."_".$GameServerID."_".$Timestamp."_".$Key) == $Sign) {
	$uid = '91_h5_'.$GameServerID.'_'.$AccountID;
    $sql_user = "SELECT a.userid,b.playersid,b.ingot,b.player_level,b.nickname FROM ".$common->tname('user')." a,".$common->tname('player')." b WHERE a.username = '$uid' && a.userid = b.userid LIMIT 1";
    //echo $sql_user;
    $res_user = $db->query($sql_user);
    $rows_user = $db->fetch_array($res_user);
    if (!empty($rows_user)) {
    	$pname = $rows_user['nickname'];
    	echo "1|$pname";
    } else {
    	echo '0|该玩家未创建角色';
    }
} else {
	echo '0|签名验证失败';
}
?>