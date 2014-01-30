<?php
$ROOT = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR;
include($ROOT.'config.php');
include($ROOT.'lang'.DIRECTORY_SEPARATOR.$_SC['lang'].'.php');
$link = mysql_connect($_SC['dbhost'], $_SC['dbuser'], $_SC['dbpw']);
mysql_selectdb($_SC['dbname'], $link);
mysql_query("set names 'utf8'", $link);

session_start();
$user = isset($_SESSION['wait_modify_info'])?$_SESSION['wait_modify_info']:'';
$verifyTime = isset($_SESSION['wait_modify_time'])?$_SESSION['wait_modify_time']:0;
$currTime = time();

$isOk = false;
if((!empty($user))&&$currTime<$verifyTime){
	$pwd = $_POST['pwd'];
	$repwd = $_POST['repwd'];

	if($pwd == $repwd){
		$pwd = md5($pwd);
		$upVerSql = "update ol_users set `pwd`='{$pwd}' where userName='{$user['userName']}'";
		$result = mysql_query($upVerSql, $link);
		if($result){
			$isOk = true;
			unset($_SESSION['wait_modify_info']);
			unset($_SESSION['wait_modify_time']);
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width = device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title><?php echo $lang['find_pwd']?></title>
</head>
<style type="text/css">
body{padding:0px; margin:0px; height:100%; font-size:13px; background-color:#331509; color:#4f1a00;}
p,span,ul,h3,h4,lable{padding:0px; margin:0px;}
ul li{list-style:none;}
img{border:0px;}
a{color:#4f1a00; text-decoration:none;}
html,form{height:100%;}
.Wrap{width:100%; height:100%; margin:0 auto;}
.Header{background:url(images/bg.jpg) repeat-x; width:100%; height:46px; float:left; text-align:center;}
.Header span{width:48px; height:46px; text-align:center;}
.Main{background-color:#fcd59a; border:3px solid #e6a731; width:98%; height:90%; margin:0px 0px 1% 1%; float:left; padding-bottom:10px;}
.CZ{width:auto; height:auto; line-height:30px; margin:10px 6px 0 6px;}
.fhbtn{width:50px; height:28px; float:left; margin:6px 0 0 6px;}
.fhbtn a{width:50px; height:28px; float:left; display:block;}
.chongzhi{width:100%; height:auto; background-color:#ffefdf; border:1px solid #cf7a27; margin-bottom:1px; padding:5px 0;}
.czleft{margin:0 8px 0px 10px;}
.tips{line-height:18px; margin:0 8px 5px 8px; display:block;}
.xzje{margin:0 0 0 10px; line-height:30px;}
.qttable{margin:5px 0 0 0px;}
.box{width:80%; height:21px; line-height:21px; margin-bottom:4px; background-color:#fcd59a; border:1px solid #3d1d11;}
</style>
<body>
<div class="Wrap">
	<div class="Header"><span><img src="images/<?php echo $_SC['lang']?>/zh.jpg" width="48" height="46" /></span></div>
	<input type="hidden" name="user" value="<?php echo $user?>">
	<input type="hidden" name="verifyCode" value="<?php echo $verifyCode?>">
    <table border="0" cellspacing="0" cellpadding="0" class="Main">
    <tr><td valign="top">
      <div class="CZ">
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="chongzhi">
          <tr>
            <td><h3 class="xzje"><?php echo $lang['prompt']?></h3></td>
          </tr>
          <tr>
            <td colspan="2"><span class="tips">
<?php 
	echo $isOk?$lang['mdy_succ']:$lang['mdy_fail'];
?>
</span></td>
          </tr>
        </table>
</div>
    </td></tr></table>
</div>
</body>
</html>
