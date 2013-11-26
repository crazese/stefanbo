<?php
header("content-type:text/html; charset=utf-8");

include('db.php');
include('../../config.php');
include('../../components/hero_social/model.php');
include('../../ucapi/libs/UzoneRestApi.php');

$playersid = $_GET['playersid'];
$toplayersid = $_GET['toplayersid'];
$clid = $_GET['clid'];
$toplayersid1 = isset($_GET['toplayersid1']) ? $_GET['toplayersid1'] : "";
$type = $_GET['type'];
$uc = isset($_GET['uc']) ? $_GET['uc'] : "";

$temp_dir = explode(",",$toplayersid);
if($_SC['isAPI'] <> '1') {
	return '';
}
switch ($type)
{
	case 1:  //送礼 
		$result = mysql_query("select userid,ucid from ol_player where playersid = '".$playersid."' LIMIT 1");
		$rows_ucid = mysql_fetch_array($result);
				
		$result = mysql_query("select uzone_token from ol_user where userid = '".$rows_ucid['userid']."' LIMIT 1");
		$rows_token = mysql_fetch_array($result);
		
		$result = mysql_query("select ucid from ol_player where playersid = '".$toplayersid."' LIMIT 1");
		$rows_pucid = mysql_fetch_array($result);
			
		$message = '你的好友 {$srcUid:uuuu} 带着满满一车的礼品在你的城池门口等着你呢！快快打开城门迎接啊！<a href="'.$_SC['backUrl'].'/index.php">去看看！</a>';
		$message = str_replace("uuuu",$rows_ucid['ucid'],$message);

		if($uc == '1') {
			$result = sendMessageUc($playersid,$toplayersid,$message,$rows_token['uzone_token']);
		}else{
			$result = sendMessageUc($playersid,$rows_pucid['ucid'],$message,$rows_token['uzone_token']);
		}
		//发送失败
		if($result == 2) {
			$message_send['playersid'] = $playersid;
			$message_send['fucid'] = $toplayersid;
			$message_send['type'] = 1;
		    //$common->inserttable('message_failed',$message_send);
		}
  	break;
	case 2:  //回礼
		$result = mysql_query("select userid,ucid from ol_player where playersid = '".$playersid."' LIMIT 1");
		$rows_ucid = mysql_fetch_array($result);
		
		$result = mysql_query("select uzone_token from ol_user where userid = '".$rows_ucid['userid']."' LIMIT 1");
		$rows_token = mysql_fetch_array($result);
				
		$result = mysql_query("select ucid from ol_player where playersid = '".$toplayersid."' LIMIT 1");
		$rows_pucid = mysql_fetch_array($result);
		
		$message = '{$srcUid:uuuu} 收到您的礼物满心欢喜,特地回赠您1点<a href="'.$_SC['backUrl'].'/index.php">军粮</a>。';
		$message = str_replace("uuuu",$rows_ucid['ucid'],$message);
		$result = sendMessageUc($playersid,$rows_pucid['ucid'],$message,$rows_token['uzone_token']);
		//发送失败
		if($result == 2) {
			$message_send['playersid'] = $playersid;
			$message_send['fucid'] = $toplayersid;
			$message_send['type'] = 1;
		    //$common->inserttable('message_failed',$message_send);
		}
  	break;
	case 3:  //打劫
		$result = mysql_query("select userid,ucid,nickname from ol_player where playersid = '".$playersid."' LIMIT 1");
		$rows_ucid = mysql_fetch_array($result);
		
		$result = mysql_query("select uzone_token from ol_user where userid = '".$rows_ucid['userid']."' LIMIT 1");
		$rows_token = mysql_fetch_array($result);
		
		$result = mysql_query("select ucid from ol_player where playersid = '".$toplayersid."' LIMIT 1");
		$rows_pucid = mysql_fetch_array($result);
		
		//$name = $rows_p1['nickname'];
		$message = '你的手下押送礼物时，路过十字坡，和你的仇人{$srcUid:uuuu}住进一家客栈，就从此没了下落，难道是遇到黑店了？快去<a href="'.$_SC['backUrl'].'/index.php">查看一下！</a>';
		$message = str_replace("uuuu",$rows_ucid['ucid'],$message);
		$result = sendMessageUc($playersid,$rows_pucid['ucid'],$message,$rows_token['uzone_token']);
		//发送失败
		if($result == 2) {
			echo('失败');
			$message_send['playersid'] = $playersid;
			$message_send['fucid'] = $toplayersid;
			$message_send['type'] = 1;
		    //$common->inserttable('message_failed',$message_send);
		}
		
		$result = mysql_query("select userid,ucid from ol_player where playersid = '".$playersid."' LIMIT 1");
		$rows_ucid = mysql_fetch_array($result);
		
		$result = mysql_query("select uzone_token from ol_user where userid = '".$rows_ucid['userid']."' LIMIT 1");
		$rows_token = mysql_fetch_array($result);
		
		$result = mysql_query("select ucid,nickname from ol_player where playersid = '".$toplayersid1."' LIMIT 1");
		$rows_pucid = mysql_fetch_array($result);
		
		$name = $rows_pucid['nickname'];
		$message = '你的好友'.$name.'好心送来一车礼物，不料路过黄泥冈，被你的仇人{$srcUid:uuuu}暗算，抢走了礼物，现在只剩一头拖车的驴跑了回来，<a href="'.$_SC['backUrl'].'/index.php">快去看看吧！</a>';
		$message = str_replace("uuuu",$rows_ucid['ucid'],$message);
		$result = sendMessageUc($playersid,$rows_pucid['ucid'],$message,$rows_token['uzone_token']);
		//发送失败
		if($result == 2) {
			$message_send['playersid'] = $playersid;
			$message_send['fucid'] = $toplayersid1;
			$message_send['type'] = 1;
		    //$common->inserttable('message_failed',$message_send);
		}
  	break;
  	case 4:  //顺手牵羊
		$result = mysql_query("select userid,ucid from ol_player where playersid = '".$playersid."' LIMIT 1");
		$rows_ucid = mysql_fetch_array($result);
		
		$result = mysql_query("select uzone_token from ol_user where userid = '".$rows_ucid['userid']."' LIMIT 1");
		$rows_token = mysql_fetch_array($result);
			
		$result = mysql_query("select ucid from ol_player where playersid = '".$toplayersid."' LIMIT 1");
		$rows_pucid = mysql_fetch_array($result);
		
		$message = '最近城池的收入好像减少了！听说这两天在集市发现你的仇人{$srcUid:uuuu} 鬼鬼祟祟的，可能是他在搞鬼，应该找个手下去<a href="'.$_SC['backUrl'].'/index.php">调查下！</a>';
		$message = str_replace("uuuu",$rows_ucid['ucid'],$message);
		$result = sendMessageUc($playersid,$rows_pucid['ucid'],$message,$rows_token['uzone_token']);
		if($result == 2) {
			$message_send['playersid'] = $playersid;
			$message_send['fucid'] = $toplayersid;
			$message_send['message'] = $message."playersid".$playersid."topid".$toplayersid;
			$message_send['type'] = 1;
		    //$common->inserttable('message_failed',$message_send);
		}
  	break;
  	case 5:  //历练
		$result = mysql_query("select userid,ucid from ol_player where playersid = '".$playersid."' LIMIT 1");
		$rows_ucid = mysql_fetch_array($result);
			
		$result = mysql_query("select uzone_token from ol_user where userid = '".$rows_ucid['userid']."' LIMIT 1");
		$rows_token = mysql_fetch_array($result);
		
		$result = mysql_query("select ucid from ol_player where playersid = '".$toplayersid."' LIMIT 1");
		$rows_pucid = mysql_fetch_array($result);
		
		$message = '你的好友{$srcUid:uuuu} 登门拜访，希望你可以将你的盖世武功传授一点点给他手下的武将！事成之后有重金回报！<a href="'.$_SC['backUrl'].'/index.php"> 查看！</a>';
		$message = str_replace("uuuu",$rows_ucid['ucid'],$message);
		$result = sendMessageUc($playersid,$rows_pucid['ucid'],$message,$rows_token['uzone_token']);
		if($result == 2) {
			$message_send['playersid'] = $playersid;
			$message_send['fucid'] = $toplayersid;
			$message_send['message'] = $message."playersid".$playersid."topid".$toplayersid;
			$message_send['type'] = 1;
		    //$common->inserttable('message_failed',$message_send);
		}
  	break;
  	case 6:
		$result = mysql_query("select userid,ucid from ol_player where playersid = '".$playersid."' LIMIT 1");
		$rows_ucid = mysql_fetch_array($result);
			
		$result = mysql_query("select uzone_token from ol_user where userid = '".$rows_ucid['userid']."' LIMIT 1");
		$rows_token = mysql_fetch_array($result);
		
		$result = mysql_query("select ucid from ol_player where playersid = '".$toplayersid."' LIMIT 1");
		$rows_pucid = mysql_fetch_array($result);
		
		$message = '你的好友{$srcUid:uuuu} 已经接受了你的历练邀请，如果你的武将已经满足历练条件，就可以马上完成历练，提升属性了！<a href="'.$_SC['backUrl'].'/index.php"> 去看看吧！</a>';
		$message = str_replace("uuuu",$rows_ucid['ucid'],$message);
		$result = sendMessageUc($playersid,$rows_pucid['ucid'],$message,$rows_token['uzone_token']);
		if($result == 2) {
			$message_send['playersid'] = $playersid;
			$message_send['fucid'] = $toplayersid;
			$message_send['message'] = $message."playersid".$playersid."topid".$toplayersid;
			$message_send['type'] = 1;
		    //$common->inserttable('message_failed',$message_send);
		}
  	break;
  	case 7:
		$result = mysql_query("select userid,ucid from ol_player where playersid = '".$playersid."' LIMIT 1");
		$rows_ucid = mysql_fetch_array($result);
			
		$result = mysql_query("select uzone_token from ol_user where userid = '".$rows_ucid['userid']."' LIMIT 1");
		$rows_token = mysql_fetch_array($result);
		
		$result = mysql_query("select ucid from ol_player where playersid = '".$toplayersid."' LIMIT 1");
		$rows_pucid = mysql_fetch_array($result);
		
		$message = '在一个月黑风高的夜晚，你的仇人{$srcUid:uuuu} 带领一群强盗偷袭你的城池，打伤你的武将，抢走你钱庄的财物，<a href="'.$_SC['backUrl'].'/index.php">快去看看吧！</a>';
		$message = str_replace("uuuu",$rows_ucid['ucid'],$message);
		$result = sendMessageUc($playersid,$rows_pucid['ucid'],$message,$rows_token['uzone_token']);
		if($result == 2) {
			$message_send['playersid'] = $playersid;
			$message_send['fucid'] = $toplayersid;
			$message_send['message'] = $message."playersid".$playersid."topid".$toplayersid;
			$message_send['type'] = 1;
		    //$common->inserttable('message_failed',$message_send);
		}
		
		$message = '在一个月黑风高的夜晚,{$srcUid:onePerson}带领一群强盗偷袭了{$dstUid:twoPerson}的城池，打伤你的武将，抢走你钱庄的财物。';
		$message = str_replace("onePerson",$rows_ucid['ucid'],$message);
		$message = str_replace("twoPerson",$rows_pucid['ucid'],$message);
		$result = sendMessageUcFeed($playersid,$rows_pucid['ucid'],$message,$rows_token['uzone_token']);
		
  	break;
  	case 8:
		$result = mysql_query("select userid,ucid from ol_player where playersid = '".$playersid."' LIMIT 1");
		$rows_ucid = mysql_fetch_array($result);
			
		$result = mysql_query("select uzone_token from ol_user where userid = '".$rows_ucid['userid']."' LIMIT 1");
		$rows_token = mysql_fetch_array($result);
		
		$result = mysql_query("select ucid from ol_player where playersid = '".$toplayersid."' LIMIT 1");
		$rows_pucid = mysql_fetch_array($result);
		
		$message = '大将军{$srcUid:uuuu} 统帅大军占领了您的城池，抢走了您的财物，<a href="'.$_SC['backUrl'].'/index.php">快去看看吧！</a>';
		$message = str_replace("uuuu",$rows_ucid['ucid'],$message);
		$result = sendMessageUc($playersid,$rows_pucid['ucid'],$message,$rows_token['uzone_token']);
		
		$message = '大将军{$srcUid:onePerson}统帅大军占领了{$dstUid:twoPerson}的城池，抢走了他的财物。';
		$message = str_replace("onePerson",$rows_ucid['ucid'],$message);
		$message = str_replace("twoPerson",$rows_pucid['ucid'],$message);
		$result = sendMessageUcFeed($playersid,$rows_pucid['ucid'],$message,$rows_token['uzone_token']);
		
  	break;
  	case 9:
  		
	default:
  	echo "No number between 1 and 3";
}

	function sendMessageUc($playersid,$toplayersid,$message,$ucid='') {
		$uzone_token = str_replace("%20","+",$ucid);
		
		$UzoneRestApi = new UzoneRestApi($uzone_token);
		
		if (!$UzoneRestApi->checkIsAuth()){
			echo('token 无效');
		    //$backUrl = 'http://hero.iguodong.com:9001/sendMessage.php?m=hello&do=aaa&msg=中文&li=xxxx&gi=xxx&wi=xxx';
		    //$UzoneRestApi->redirect2SsoServer($backUrl);
		}
		
		$header = $UzoneRestApi->callMethod('layout.get', array('type' => 'header'));
		$uid =  intval(trim($toplayersid));
		$content_template = $message;
		echo '<br>';
		echo 'msg:'.$content_template;
		echo '<br>';
		echo 'uid:'.$uid;
		echo '<br>';
		$res = $UzoneRestApi->callMethod('notifications.send', array('content_template' => $content_template, 'uid' => $uid));
		if ($UzoneRestApi->checkIsCallSuccess()){
		    unset($UzoneRestApi);
		    echo('OK');
		    return 0;
		} else {
		    unset($UzoneRestApi);
		    return 2;
		}
	}
	
	function sendMessageUcFeed($playersid,$toplayersid,$message,$ucid='') {
		$uzone_token = str_replace("%20","+",$ucid);
		
		$UzoneRestApi = new UzoneRestApi($uzone_token);
		
		if (!$UzoneRestApi->checkIsAuth()) {
			echo('token 无效');
		}
		
		$header = $UzoneRestApi->callMethod('layout.get', array('type' => 'header'));
		$uid =  intval(trim($toplayersid));
		$content_template = $message;
		echo '<br>';
		echo 'msg:'.$content_template;
		$res = $UzoneRestApi->callMethod('feed.push', array('content_template' => $content_template, 'noveltytype' => 'lvReply'));
		if ($UzoneRestApi->checkIsCallSuccess()) {
		    unset($UzoneRestApi);
		    echo('sendMessageUcFeed ：OK');
		    return 0;
		} else {
		    unset($UzoneRestApi);
		    return 2;
		}
	}
?>