<?php
function genArr4Str($str) {
	$record_arr_tmp = explode('|', $str);
	$record_cnt = count($record_arr_tmp);
	for($i = 0; $i < $record_cnt; $i++) {
		$info = explode('-',$record_arr_tmp[$i]);
		foreach ($info as $infoValue) {
			$record_arr[$info[0]] = array('result'=>$info[1], 'count'=>$info[2]);
		}
		$info = null;
	}	
	return $record_arr;
}

function genStr4Arr($arr) {
	$content = '';
	foreach ($arr as $key=>$value) {
		$content .= $key . '-' . $value['result'] . '-' . $value['count'] . '|';		
	}
	$content = rtrim($content, '|');	
	return $content;
}

?>