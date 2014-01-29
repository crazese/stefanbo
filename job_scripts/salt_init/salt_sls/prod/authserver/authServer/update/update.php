<?php
$jcid = $_GET['jcid'];
$pInfo = array(
	1=>array('downloadURL'=>'http://ucs1.jofgame.com/update/HeroOlWeb.swf','version'=>1.33), //QQ Android 手机游戏大厅
	2=>array('downloadURL'=>'http://ucs2.jofgame.com/update/HeroOlWeb.swf','version'=>1.05),	//QQ Android 游戏中心
	3=>array('downloadURL'=>'http://ucs3.jofgame.com/update/HeroOlWeb.swf','version'=>1.05),	//乐逗 Android
	4=>array('downloadURL'=>'http://ucs4.jofgame.com/update/HeroOlWeb.swf','version'=>1.05),	//UC iOS
	5=>array('downloadURL'=>'http://ucs5.jofgame.com/update/HeroOlWeb.swf','version'=>1.05),	//UC Android
	6=>array('downloadURL'=>'http://ucs6.jofgame.com/update/HeroOlWeb.swf','version'=>1.05),	//91 iOS
	7=>array('downloadURL'=>'http://ucs7.jofgame.com/update/HeroOlWeb.swf','version'=>1.05),	//Tom Android
	8=>array('downloadURL'=>'http://ucs8.jofgame.com/update/HeroOlWeb.swf','version'=>1.05),	//松岗
	9=>array('downloadURL'=>'http://ucs9.jofgame.com/update/HeroOlWeb.swf','version'=>1.05),	//html5 新浪微博
	10=>array('downloadURL'=>'http://ucs10.jofgame.com/update/HeroOlWeb.swf','version'=>1.05),//小米 Andorid
	11=>array('downloadURL'=>'http://ucs11.jofgame.com/update/HeroOlWeb.swf','version'=>1.05)	//宝软 CPS合作方式 Andorid
);
if (!empty($pInfo[$jcid])) {
	$downLoadInfo = $pInfo[$jcid];
	$downloadURL = $downLoadInfo['downloadURL'];
	$version = $downLoadInfo['version'];
} else {
	$downloadURL = 'http://ucs.jofgame.com/update/HeroOlWeb.swf';
	$version = 1.05;	
}
header("Content-Type: text/xml; charset=UTF-8");
echo "<data>
<downloadURL>$downloadURL</downloadURL>
<version>$version</version>
</data>";