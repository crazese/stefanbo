<?php
if (empty($deviceid)) {
	echo json_encode(array('status'=>21,'message'=>'没有和您的设备对应的账号信息','rsn'=>_get('ssn')));
} else {
	$sql = "select * from ol_users where deviceid = '$deviceid' order by lastLoging desc limit 1";
	$result = mysql_query($sql);
	$rows = mysql_fetch_array($result);
	if (!empty($rows)) {
		if ($rows['email'] != '') {
			echo json_encode(array('status'=>1001,'email'=>$rows['email'],'zh'=>$rows['userName'],'rsn'=>_get('ssn')));
		} else {
			mysql_query("update ol_users set pwd = '".md5('123456')."' where id = ".$rows['id']);
			echo json_encode(array('status'=>0,'zh'=>$rows['userName'],'mm'=>'123456','rsn'=>_get('ssn')));
		}				
	} else {
		echo json_encode(array('status'=>21,'message'=>'没有和您的设备对应的账号信息','rsn'=>_get('ssn')));
	}
}