<?php
class uc {	
    //显示消息
	static function show($message) {
		echo urldecode(json_encode($message));
	}
	
	static function sendMessageUc($playersid,$toplayersid,$message,$ucid='') {
		echo($playersid.'/n');
		echo($toplayersid.'/n');
		//echo($message.'/n');
		//echo($ucid.'/n');
		$uzone_token = str_replace("%20","+",$ucid);

		$UzoneRestApi = new UzoneRestApi($uzone_token);
		
		if (!$UzoneRestApi->checkIsAuth()){
			echo('token 错误');
		    //$backUrl = 'http://hero.iguodong.com:9001/sendMessage.php?m=hello&do=aaa&msg=中文&li=xxxx&gi=xxx&wi=xxx';
		    //$UzoneRestApi->redirect2SsoServer($backUrl);
		}
		
		$header = $UzoneRestApi->callMethod('layout.get', array('type' => 'header'));
		
		$uid =  intval(trim($toplayersid));
		$content_template = $message;
		$res = $UzoneRestApi->callMethod('notifications.send', array('content_template' => $content_template, 'uid' => $uid));
		if ($UzoneRestApi->checkIsCallSuccess()){
		    echo('ok');
			unset($UzoneRestApi);
		    return 0;
		} else {
			echo('error');
		    unset($UzoneRestApi);
		    return 2;
		}
	}
	
	static function addMessage($json) { //$playersid,$toplayersid,$message,$type,$genre,$is_interaction
		global $_SC;
		$arr_dir = json_decode($json,true);
		$fp = fsockopen('127.0.0.1', $_SC['port'], $errno, $errstr, 30);
		
		if($arr_dir['type'] == "1" && $arr_dir['genre'] == "3") {			//送礼消息
			$result = $db->query("SELECT userid,nickname FROM ".$common->tname('player')." WHERE playersid = '".$arr_dir['playersid']."'  LIMIT 1");
			$rows = $db->fetch_array($result);
			if($rows['nickname'] == "" || $rows['nickname'] == null) {
				return 2;
			}
			
			$letters_trade['playersid'] = $arr_dir['toplayersid'];
		    $letters_trade['type'] = $arr_dir['type'];
		    $letters_trade['genre'] = $arr_dir['genre'];
		    $letters_trade['subject'] = "您收到了礼物！";
		    $letters_trade['is_interaction'] = $arr_dir['interaction'];
		    $letters_trade['parameters'] = $rows['userid']."|".$arr_dir['toplayersid'];
		    //$letters_trade['parameters'] = $_SESSION['userid']."|".$arr_dir['toplayersid'];
		    $letters_trade['fromplayersid'] = $arr_dir['playersid'];
		    $letters_trade['message'] = "[".$rows['nickname']."]赠送给您1点军粮！礼尚往来才是美德，不要忘记回送份礼给对方哟！";
		    $letters_trade['status'] = "0";
		    $letters_trade['create_time'] = time();
		    
		    mysql_query("insert into ol_letters (playersid,type,genre,subject,is_interaction,parameters,fromplayersid,message,status,create_time)
		     values ('".$letters_trade['playersid']."','".$letters_trade['type']."','".$letters_trade['genre']."','".$letters_trade['subject']."',
		     '".$letters_trade['is_interaction']."','".$letters_trade['parameters']."','".$letters_trade['fromplayersid']."','".$letters_trade['message']."',
		     '".$letters_trade['status']."','".$letters_trade['create_time']."')");
		    //inserttable('letters',$letters_trade);
		}elseif($arr_dir['type'] == "2" && $arr_dir['genre'] == "3") {			//uc送礼
			$letters_trade['playersid'] = $arr_dir['toplayersid'];
		    $letters_trade['type'] = $arr_dir['type'];
		    $letters_trade['genre'] = $arr_dir['genre'];
		    $letters_trade['subject'] = "uc好友送礼";
		    $letters_trade['is_interaction'] = $arr_dir['interaction'];
		    $letters_trade['parameters'] = $arr_dir['userid']."|".$arr_dir['toplayersid'];
		    $letters_trade['fromplayersid'] = $arr_dir['playersid'];
		    $letters_trade['message'] = "";
		    $letters_trade['status'] = "0";
		    $letters_trade['create_time'] = time();
		    
		    mysql_query("insert into ol_letters (playersid,type,genre,subject,is_interaction,parameters,fromplayersid,message,status,create_time)
		     values ('".$letters_trade['playersid']."','".$letters_trade['type']."','".$letters_trade['genre']."','".$letters_trade['subject']."',
		     '".$letters_trade['is_interaction']."','".$letters_trade['parameters']."','".$letters_trade['fromplayersid']."','".$letters_trade['message']."',
		     '".$letters_trade['status']."','".$letters_trade['create_time']."')");
		    $result_id = mysql_insert_id();
		    
		    //$return_id = $common->inserttable('letters',$letters_trade);
			if($result_id > 0) {
			$result = mysql_query("select ucid from ol_player where playersid = '".$arr_dir['playersid']."' LIMIT 1");
			$rows_ucid = mysql_fetch_array($result);
				
			$message = '{$srcUid:uuuu} 给你送了[军粮]  <a href="http://herool.jofgame.com/index.php?inviteid=pppp">去看看！</a>';
			$message = str_replace("uuuu",$rows_ucid['ucid'],$message);
			$message = str_replace("pppp",$result_id,$message);
			try{
				$result = uc::sendMessageUc($arr_dir['playersid'],$arr_dir['toplayersid'],$message,$arr_dir['uzone_token']);
			}catch(Exception $e){}
			}
		}
	    return 0;
	}
	
}
?>