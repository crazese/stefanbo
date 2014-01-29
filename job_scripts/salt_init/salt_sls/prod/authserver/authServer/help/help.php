<?php
function _get($str){
	//$magic_quote = get_magic_quotes_gpc();
	if (isset($_REQUEST[$str]))	{		
		$val = $_REQUEST[$str];	
	} else {
		$val = null;
	}
    return $val;
}
$jcid = _get('jcid');
$defaultUrl = 'http://z.jofgame.com/helpcenter_V1.35.4/index.html';
$urlData = array(
	6=>'http://z.jofgame.com/helpcenter_V1.35.4_91ios/index.html'
);
if (isset($urlData[$jcid])) {
	header('location:'.$urlData[$jcid]);
} else {
	header('location:'.$defaultUrl);
}