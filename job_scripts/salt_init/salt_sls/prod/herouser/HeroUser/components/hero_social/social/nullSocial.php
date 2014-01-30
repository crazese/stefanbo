<?php
class nullSocial{
	public function pushMessage($roleInfo, $message, $otherId = null){
		return true;
	}

	public function getFriends($roleInfo){
		return array();
	}
}