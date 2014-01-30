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
$defaultUrl = 'http://z.jofgame.com/forum/ui/color/boardlist.php';
$urlData = array(
	32=>'http://bbs.anzhi.com/forum-1086-1.html',          //安智
	29=>'http://bbs.9game.cn/forum-825-1.html',			   //指尖UC Android
	33=>'http://bbs.18183.com/forum-zhijianshuihu-1.html',  //指尖91Android
	6=>'http://bbs.18183.com/forum-zhijianshuihu-1.html'
);
if (isset($urlData[$jcid])) {
	header('location:'.$urlData[$jcid]);
} else {
	header('location:'.$defaultUrl);
}