<?php
class checkTag{
	function __construct() {
		global $mc,$getLoginKey,$task;
		$tag = MC.$getLoginKey;	
		if ($task != 'checkMsg' && $task != 'getRWShow') {
			if ($mc->add($tag,1,0,30) == false) {
				$value['status'] = 1;
				$value['message'] = '服务器繁忙，请稍后再试！';
				$value['rsn'] = intval(_get('ssn'));
				echo json_encode($value);
				heroCommon::writelog($_SERVER['REQUEST_URI'],json_encode($value));
				exit;		
			}
		}
	}
}	