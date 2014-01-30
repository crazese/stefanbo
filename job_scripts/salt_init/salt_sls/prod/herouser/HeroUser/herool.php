<?php
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
include(S_ROOT.'./config.php');
//设置时区
date_default_timezone_set('Asia/Shanghai');

$swf = 'HeroOlWeb.swf';
if(isset($_GET['sid'])) {
	$temp = "/{$swf}?v=".$_SC['swf_version']."&sid=".$_GET['sid']."&platform=9y";
} else if(isset($_GET['uzone_token'])) {
	$temp = "/{$swf}?v=".$_SC['swf_version']."&uzone_token=".$_GET['uzone_token']."&platform=uc";
} else if(isset($_GET['platform'])) {	
	$temp = "/{$swf}?v=".$_SC['swf_version']."&platform=".$_GET['platform'];
} else {
	$temp = "/{$swf}?v=".$_SC['swf_version'];
}
$str =<<<xml
<html xmlns="http://www.w3.org/1999/xhtml"> 
<title>Q将水浒</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,minimum-scale=1.0, maximum-scale=2.0"/>
<body bgcolor=''>
<UCF type="ucfocus" value="normol"/> 
<embed id="vv" src="{$temp}" width="100%" height="100%" allowNetworking="all" AllowScriptAccess="always" quality="high" playmode="1" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>
</body>
<html>
xml;
echo $str;
?>