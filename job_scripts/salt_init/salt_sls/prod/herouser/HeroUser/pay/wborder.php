<?php
include 'function.php';
$appSecret = '8NedWUrdo3xQm43lfdg23445536546456ocucKqwV';
$id = _get('id');
$playersid = _get('playersid');
$sinauid = _get('sinauid');
$serverid = _get('serverid');
$sign = _get('sign');
if (md5($id.$playersid.$sinauid.$serverid.$appSecret) == $sign) {
	$code = $common->inserttable('wborderid',array('ortime'=>$id,'playersid'=>$playersid,'sinauid'=>$sinauid,'serverid'=>$serverid));
	echo $code;
} else {
	echo 'failed';
}