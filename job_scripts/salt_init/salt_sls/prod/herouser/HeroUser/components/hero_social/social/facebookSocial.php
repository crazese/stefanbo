<?php

/*
 * facebook 社交调用
 *
 */
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "facebook" . DIRECTORY_SEPARATOR. "facebook.php";

class facebookSocial{
	private $facebook = null;
	private $link = 'https://itunes.apple.com/tw/app/id645436918';
	public function __construct(){
		if ($_SESSION['jcid'] == 8) {
			$appId = '577721408916792';
			$secret = 'c6d6698e43792bd520b40cca54f938b8';
		} else {
			$appId = '489217221125581';
			$secret = 'b5fb1597b032bb820d5b52168e34f7f8';				
		}
		$this->facebook = new Facebook(array(
											 'appId'  => $appId,
											 'secret' => $secret
											 ));
	}

	public function shareMessage($userToken, $message){
		$this->facebook->setAccessToken($userToken);
		$this->facebook->setExtendedAccessToken();
		try{
			$ret_obj = $this->facebook->api('/me/feed', 'POST',array('link'=>$this->link,
																	 'message'=> $message));
		}catch(FacebookApiException $ex){
			return false;
		}

		return true;
	}

	public static function pushMessage($userToken, $message, $otherToken = null){}

	public static function getFriends($userToken){
		$this->facebook->setAccessToken($userToken);
		$this->facebook->setExtendedAccessToken();

		$friList = array();
		try{
			$ret_obj = $this->facebook->api('/me/friends', 'GET');
			$friList = $ret_obj['data'];
		}catch(FacebookApiException $ex){
			return array();
		}

		return $friList;
	}
}