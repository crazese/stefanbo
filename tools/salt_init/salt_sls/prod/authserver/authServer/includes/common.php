<?php 

function ver_cmp($ver1, $ver2){
	// 老的版本兼容
	if($ver1 == $ver2){
		return -1;
	}else{
		return 1;
	}

	// 客户端版本更新
	$_ver1 = explode('.', $ver1);
	$_ver2 = explode('.', $ver2);
	for($i=0; $i<count($_ver1)&&$i<count($_ver2); $i++){
		$_cmp1 = intval($_ver1[$i]);
		$_cmp2 = intval($_ver2[$i]);
		if($_cmp1>$_cmp2){
			return 1;
		}else if($_cmp1<$_cmp2){
			return -1;
		}
	}

	if(count($_ver1) > count($_ver2)){
		return 1;
	}else if(count($_ver1) < count($_ver2)){
		return -1;
	}

	return 0;
}

function sendMail($mailUrl, $verifyCode, $userName){
	global $_SC, $lang;
	$include_path = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR;
	include_once($include_path.'class.phpmailer.php');
	include_once($include_path.'class.smtp.php');

	$linkUrl = "{$_SC['email']['callbck']}/password/index.php?user=$userName&ver=$verifyCode";
	$body    = $lang['email_content_1']."<a style='color:blue' href='$linkUrl'>$linkUrl</a>".$lang['email_content_2'];

	$mail = new PHPMailer();

	$mail->IsSMTP();
	$mail->Encoding = "8bit";
	$mail->CharSet = "UTF-8";
	$mail->SMTPAuth = true; // 启用SMTP身份认证
	$mail->SMTPSecure = "ssl"; // 设置服务器前缀，gmail的smtp服务器是ssl://smtp.gmail.com
	$mail->Host = $_SC['email']['smtp']; // 设置gmail的smtp服务器
	$mail->Port = $_SC['email']['port']; // gmail的smtp端口是465

	$mail->Username = $_SC['email']['user']; // gmail用户名
	$mail->Password = $_SC['email']['passwd']; // gmail密码

	$mail->From = "";//发送方地址
	$mail->FromName = $lang['email_fromname'];//昵称
	$mail->Subject = $lang['email_subject'];//标题
	$mail->Body = $body; //邮件正文
	$mail->AltBody = $body; //邮件正文
	$mail->WordWrap = 50; // 50个字自动换行

	//$mail->MsgHTML($body);
	$mail->IsHTML(true); // 以html格式发送邮件
	$mail->AddAddress($mailUrl, $mailUrl);
	$mail->Send();

	//if(!$mail->Send()) {//显示错误
	//	echo "Mailer Error: " . $mail->ErrorInfo;
	//} else {
	//	echo "Message has been sent";
	//}
}

//------------用户中心数据导入代码-------------------
function insertUser($user_info) {
	/*$dsn = 'cassandra:host=59.175.238.4;port=9160';
	$cas_db = 'jofgame_usrcenter';
	$db = new PDO($dsn);
	$db->exec("USE ".$cas_db.";");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	//$guid = UUID::v4();
	$register_date = time();
	$db->exec("INSERT INTO users(id, usr_name, usr_pass, login_date, usr_email, usr_phone, real_name, idcard_number, game_id, game_qd, phone_verify, email_verify, nick_name, sex, register_date) VALUES('{$user_info['guid']}', '{$user_info['usr_name']}', '{$user_info['usr_pass']}', {$user_info['login_date']}, '{$user_info['usr_email']}', '{$user_info['usr_phone']}', '{$user_info['real_name']}', '{$user_info['idcard_number']}', '{$user_info['game_id']}', '{$user_info['game_qd']}', 'false', 'false', '{$user_info['nick_name']}', '{$user_info['sex']}', {$register_date});");*/
	
	return true;
}
function isUcDupUser($userName) {
	/*$dsn = 'cassandra:host=59.175.238.4;port=9160';
	$cas_db = 'jofgame_usrcenter';
	$db = new PDO($dsn);
	$db->exec("USE ".$cas_db.";");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$stmt = $db->prepare("SELECT * FROM users WHERE usr_name = :key;");
	$stmt->bindValue(':key', $userName);
	$stmt->execute();

	$user_info = false;
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if(is_array($result) && isset($result[0]['usr_name'])) {
		$user_info = $result[0];
	}
	
	return $user_info; */
	return false;
}
function updateUserQd($qd, $id) {
	/*$dsn = 'cassandra:host=59.175.238.4;port=9160';
	$cas_db = 'jofgame_usrcenter';
	$db = new PDO($dsn);
	$db->exec("USE ".$cas_db.";");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$db->exec("UPDATE users SET game_qd='{$qd}' where id = '{$id}';");*/

	return true;
}
//-------------------用户中心数据导入代码结束----------------------------