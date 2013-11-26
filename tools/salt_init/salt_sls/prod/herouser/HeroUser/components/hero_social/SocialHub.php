<?php
class SocialHub{
	public static function buildSocial(){
		$buildClassName = SOCIAL_TYPE . 'Social';
		$include_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'social' . DIRECTORY_SEPARATOR . $buildClassName . '.php';
		include_once($include_path);

		return new $buildClassName;
	}

	/*
	 * 社交分享调用
	 */
	public static function shareMessage($userToken, $message){
		$social = SocialHub::buildSocial();
		return call_user_func(array($social, 'shareMessage'), $userToken, $message);
	}

	/**
	 * 将消息发布到社交用户或玩家自己的微博
	 * $playersid
	 * $message    要发送的消息
	 * $otherId    要发送的好友id,如果为空则发送微博
	 */
	public static function pushMessage($userToken, $message, $otherToken = null){
		$social = SocialHub::buildSocial();
		return call_user_func(array($social, 'pushMessage'), $userToken, $message, $otherToken);
	}

	public static function getFriends($userToken){
		$social = SocialHub::buildSocial();
		return call_user_func(array($social, 'getFriends'), $userToken);
	}
}