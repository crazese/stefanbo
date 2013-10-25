<?php
class adminModel {
	//用户登出
	public static function clearMemory($playersid,$ucid) {
		global $mc,$common;
		if (!empty($playersid)) {
			$mc->delete(MC.$playersid);   //清除角色信息
			$mc->delete(MC.$playersid.'_general');        //清除该角色将领信息
			$mc->delete(MC.$playersid.'_messageStatus');  //清除消息状态
			$mc->delete(MC.$ucid.'_session');             //清除登录后记录sessionid信息
			$mc->delete(MC.'items_'.$playersid);          //清除道具信息
		}
		session_destroy();
	}
	
	public static function updateMessageMemory($ucid) {
		global $mc,$common,$db;
		$result = $db->query("SELECT playersid FROM ".$common->tname('player')." WHERE ucid = '".$ucid."' LIMIT 1");
		$rows = $db->fetch_array($result);
		$result = $db->query("SELECT count(playersid) as icount FROM ".$common->tname('message_status')." WHERE playersid = '".$rows['playersid']."'");
		$rows_message_status = $db->fetch_array($result);
		if($rows_message_status['icount'] == 0) {
			$db->query("insert into ".$common->tname('message_status')." (playersid,messageType,messageTime) values ('".$rows['playersid']."','1','".time()."')");
		}
		if(($roleInfo = $mc->get(MC.$rows['playersid']))) {
			$mc->set(MC.$rows['playersid'].'_messageStatus',array('messageType'=>1,'messageTime'=>time()),0,7200);	
		}
	}
}

?>