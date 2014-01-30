<?php
include('../../config.php');
include('../../ucapi/libs/UzoneRestApi.php');
include('uc.php');
include('db.php');

$ucid = $_POST['ucid'];
$userid = $_POST['userId'];
$toplayersid = '0';
$playersid = $_POST['playersid'];
$uzone_token = $_POST['uzone_token'];
$type = $_POST['type'];

$result = mysql_query("select uzone_token from ol_user where userid = '".$userid."' LIMIT 1");
$rows_token = mysql_fetch_array($result);

$uzone_token = $rows_token['uzone_token'];
		
if($type == 1) {
	$result = mysql_query("select fucid from ol_uc_friends where ucid = '".$ucid."'");
}else {
	$result = mysql_query("select fucid from ol_message_failed where type = 2 and playersid = '".$ucid."'");
}

while ($rows = mysql_fetch_array($result)) {	
	$toplayersid = $toplayersid.",".$rows['fucid'];
}			
$temp_dir = explode(",",$toplayersid);
for($i = 0; $i < count($temp_dir); $i++) {
	if($temp_dir[$i] == "" || $temp_dir[$i] == 0) {
		continue;
	}
	
	$json = array();
	$json['userid'] = $userid;
	$json['playersid'] = $playersid;
	$json['toplayersid'] = $temp_dir[$i];
	$json['message'] = '';
	$json['type'] = '2';
	$json['genre'] = '3';           //好友送礼
	$json['interaction'] = '0';
	$json['uc'] = '1';
	$json['uzone_token'] = $uzone_token;
	//$json = json_encode($json);    
			
	$result_id = uc::addMessage($json);//送礼 
}
?>