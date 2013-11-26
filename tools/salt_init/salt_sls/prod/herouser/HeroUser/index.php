<?php
header("content-type:text/html; charset=utf-8");
 //设置此页面的过期时间(用格林威治时间表示)，只要是已经过去的日期即可。    
header("Expires: Mon, 26 Jul 1970 05:00:00 GMT");      
    
 //设置此页面的最后更新日期(用格林威治时间表示)为当天，可以强制浏览器获取最新资料     
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");      
    
 //告诉客户端浏览器不使用缓存，HTTP 1.1 协议     
header("Cache-Control: no-cache, must-revalidate");      
   
//告诉客户端浏览器不使用缓存，兼容HTTP 1.0 协议     
header("Pragma: no-cache");  
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
include(S_ROOT.'./config.php');
require(dirname(__FILE__).'/configs/ConfigLoader.php');
require(dirname(__FILE__).'/model/PlayerMgr.php');
require(dirname(__FILE__).'/includes/class_memcacheAdapter.php');
$mc = new MemcacheAdapter_Memcached;	
$mc->addServer($MemcacheList[0], $Memport);

//设置时区
date_default_timezone_set('Asia/Shanghai');
if($_SC['platform'] == '9ypage') {
	if(!isset($_GET['uzone_token'])) { // 9y
		$sid = null;
		if(isset($_GET['sid']) || !isset($_COOKIE['hero_9y_sid'])) {
			$sid = $_GET['sid'];
			//setcookie("hero_9y_sid", "", time() - 3600);
			//$ret9y = setcookie("hero_9y_sid", $sid, time() + 3600);			
			//setcookie("hero_9y_sid", "", time()- 860000000, '/');
			$ret9y = setcookie("hero_9y_sid", $sid,  time()+60*60*24*30, '/');			
/*hk*/			setcookie("hero_from", '9y',  time()+60*60*24*30, '/');//UC 9Y账号切换
		} else {
			$sid = $_COOKIE['hero_9y_sid'];
		}
	} else { // uc
		require dirname(__FILE__) . '/ucapi/libs/UzoneRestApi.php';		
		$uzone_token  = isset($_GET['uzone_token']) ? str_replace(' ','+',urldecode($_GET['uzone_token'])) : '';
		$inviteid = isset($_GET['inviteid']) ? $_GET['inviteid'] : '';
		$isCheck = isset($_GET['ck']) ? $_GET['ck'] : '';

		$UzoneRestApi = new UzoneRestApi($uzone_token);
		$uid = $UzoneRestApi->getAuthUid();

		$uc_info[0] = $uzone_token;
		$uc_info[1] = $uid;
		$uc_info[2] = 'uc';
		$uc_info = $uc_info[0] . '|' . $uc_info[1] . '|uc';
		//Setcookie("herooline",'',time()-2678400);		
		//$ucret = Setcookie("herooline", $uc_info, time()+14400);
		//setcookie("herooline",'',time()- 860000000, '/');		
		$ucret = setcookie("herooline", $uc_info,  time()+60*60*24*30, '/');
/*hk*/		setcookie("hero_from", 'uc',  time()+60*60*24*30, '/');
		
		if (!$UzoneRestApi->checkIsAuth()){
			$backUrl      = $_SC['backUrl'].'/index.php';
			$UzoneRestApi->redirect2SsoServer($backUrl);
		}
	}
} else {
}
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width = device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" /> 
<title>《Q将水浒》2012手机网游巨制</title>
<style>
body,ul,p{padding:0px; margin:0px;}
ul li{list-style:none;}
*{color:#555;font-size:small; padding:0; margin:0;}
img{border:0;}
a{color:#0b68b6;text-decoration:none;}
p{padding-bottom:8px; margin:0;}
.top_module{ background:#edf5ff; padding:8px 0 8px 5px; border-bottom:1px solid #aabccc;}
.paddingr5px{ padding-right:5px;}
.boldfont{ font-weight:bold;}
.nav_tab{ padding-top:8px;}
.title_nobg{padding:8px 0 8px 5px;font-weight:bold;}
.line{ border-bottom:1px solid #dbe1e5;}
.content{padding:8px 0 8px 5px;}
.content tr td{ padding:5px 0;}
.module,.module4{padding:8px 0 0 5px;}
.module4,.module{line-height:24px; margin-bottom:10px;}
.module4 a{margin:0 6px;}
.module5{padding:8px 0 8px 5px;}
.module2 {padding:4px 0;}
.module2 p {padding:4px 5px;}
.module3{padding:8px 0 0 5px;}
.module3 p{ width:100%; height:40px;}
.album{ padding:8px 5px; text-align:center;}
.lylogo{ background:#6ac8ef; padding:0px 0px;}
.title{ background:#ddecfd; padding:4px 0 4px 5px;font-weight:bold;}
.title a{ color:0b68b6;}
.font_normal{font-weight:normal;}
.orange{ color: #e9834b;}
.btn{background:url(Images/hh2.png) no-repeat; width:130px; height:30px; float:left; border:0px; cursor:pointer;}
.btn_1{background:url(Images/hh_1fu.png) no-repeat; width:130px; height:30px; float:left; border:0px; cursor:pointer;}
.btn_2{background:url(Images/hh_2fu.png) no-repeat; width:130px; height:30px; float:left; border:0px; cursor:pointer;}
.btn_3{background:url(Images/hh_3fu.png) no-repeat; width:130px; height:30px; float:left; border:0px; cursor:pointer;}
.hot{width:22px; height:15px; float:right; margin:6px 8px 0 0;}
.gonggao{margin:0 2px 0 5px;}
.swap_pic{overflow:hidden; width:300px; height:303px; margin:0 auto;}
.scroll_L{text-indent:-100px; background:url(Images/jiantou_left.jpg) no-repeat; width:22px; height:46px; float:left; overflow:hidden; margin-top:128px; cursor:pointer;}
.scroll_R{text-indent:-100px; background:url(Images/jiantou_right.jpg) no-repeat; width:22px; height:46px; float:left; overflow:hidden; margin-top:128px; margin-left:10px; cursor:pointer;}
.Shotbox{position:relative; width:228px; float:left; height:303px; overflow:hidden; margin:0 1px 0 11px;}
.pics{position:absolute; width:5000px; top:0px;}
.pics li{width:226px; float:left; height:303px; overflow:hidden;}
.pics li p{position:relative; float:left; overflow:hidden; margin-right:10px;}
.pics li p img{width:226px; height:303px; border:1px solid #3b3a3a;}
.pics li p span{position:absolute; background-color:#000000; text-indent:10px; width:100%; height:28px; line-height:28px; color:#ffffff; top:0px; cursor:pointer; margin-top:126px; text-align:center; left:0px; filter:alpha(opacity=0); opacity:0;}
</style>
</head>

<body>
<div class="lylogo">&nbsp;</div>
<div class="top_module">
  <table cellpadding="0" cellspacing="0">
   <tr>
     <td class="paddingr5px"  style="width:65px"><img src="Images/logo.png" noselect="true" /></td>
     <td  valign="top">
     <span class="boldfont">Q将水浒</span><br />
     类型：策略<br/>
	 简介：精致Q版画风，美女李师师伴你闯天下，天罡地煞108将，帐下待命，万人激战水泊梁山，巅峰对决勇夺水浒霸主！
     </td>
   </tr>
</table>
<div class="nav_tab">
<a href="http://qjsh.9game.cn/gamenewslist/xinwen">新闻</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="http://qjsh.9game.cn/gameactivitylist">活动</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=" http://bbs.9game.cn/Board.Aspx?Id=24379&P=BBS_INDEX_GAMEBOARDLIST&SessionId=bb45c878576f9fa952c4b84e4b89d98d">论坛</a>&nbsp;&nbsp;
</div>
</div>
<div class="module5">
	<!--<img class="gonggao" src="Images/a_05.gif" width="9" height="9" />《Q将水浒》删档内测火爆进行中，梦回宋朝，体验万人争霸，勇夺水浒霸主的恢弘世界，更有内测充值，公测双倍返还超值活动，超级给力噢！</div>-->
	<img class="gonggao" src="Images/a_05.gif" width="9" height="9" />《Q将水浒》火爆公测刚刚开启，来玩即送大礼包，轻松体验VIP，首充送超值大礼，万人争霸勇夺新人王，一系列缤纷活动，让您玩到High！</div>
<div class="title">游戏入口</div>
<div class="module3">
<p>
<?php
	$loginBlock = $mc->get('loginblock');
	if($loginBlock == 1) {
		echo "<a href='wjgd.html' class='btn'><img class='hot' src='Images/b_hot.gif' width='22' height='15' /></a>";
	} else {
		if($_SC['platform'] == '9ypage') {
			if(!isset($_GET['uzone_token'])) { 	 // 9y
				if(!$ret9y || !isset($_COOKIE['hero_9y_sid']) ) {
					if(isset($_GET['sid']))  {
						echo "<a href='herool.php?platform=9y&sid={$_GET['sid']}' class='btn'><img class='hot' src='Images/b_hot.gif' width='22' height='15' /></a> ";
					} else {
						$order_id = $_GET['order_id'];
						$sid = $mc->get(MC.$order_id.'_9ysid');
						echo "<a href='herool.php?platform=9y&sid={$sid}' class='btn'><img class='hot' src='Images/b_hot.gif' width='22' height='15' /></a>";
					}
				} else {
					echo "<a href='herool.php?platform=9y' class='btn'><img class='hot' src='Images/b_hot.gif' width='22' height='15' /></a>";
				}
			} else { // uc
				if(!$ucret) {
					echo "<a href='http://117.135.138.196:9090/herool.php?platform=uc&uzone_token={$uc_info}' class='btn_3'><img class='hot' src='Images/b_new1.gif' width='22' height='15' /></a><p>";					
					echo "<a href='http://117.135.138.196:8080/herool.php?platform=uc&uzone_token={$uc_info}' class='btn_2'></a><p>";					
					echo "<a href='herool.php?platform=uc&uzone_token={$uc_info}' class='btn_1'></a><p>";					
				} else {
					echo "<a href='http://117.135.138.196:9090/herool.php?platform=uc&uzone_token={$uc_info}' class='btn_3'><img class='hot' src='Images/b_new1.gif' width='22' height='15' /></a><p>";					
					echo "<a href='http://117.135.138.196:8080/herool.php?platform=uc' class='btn_2'></a><p>";
					echo "<a href='herool.php?platform=uc' class='btn_1'></a><p>";					
				}
			}
		} else {
			echo "<a href='herool.php' class='btn'><img class='hot' src='Images/b_hot.gif' width='22' height='15' /></a>";
		}
	}
?>
</p>
</div>
<div class="title">游戏截图</div>
<div class="module">
	<div class="swap_pic">
        <div class="Shotbox">
            <ul style="left:0px" id="pics1" class="pics">
              <li><p><img src="Images/jt.png" width="226" height="303" /><span></span></p></li>
              <li><p><img src="Images/jt2.png" width="226" height="303" /><span></span></p></li>
            </ul>
          </div>
    </div>
</div>
<div class="title">游戏指引</div>
<div class="module4">
<a href="http://qjsh.9game.cn/gameinfocontent/64706/zhiying?P=SPECIAL_ZHIYINN">机型</a>
<a href="http://qjsh.9game.cn/gameinfocontent/63352/zhiying?P=SPECIAL_ZHIYINN">介绍</a>
<a href="http://qjsh.9game.cn/gameinfocontent/63353/zhiying?P=SPECIAL_ZHIYINN">角色</a>
<a href="http://qjsh.9game.cn/gameinfocontent/63354/zhiying?P=SPECIAL_ZHIYINN">市场</a>
<a href="http://qjsh.9game.cn/gameinfocontent/63355/zhiying?P=SPECIAL_ZHIYINN">酒馆</a>
<a href="http://qjsh.9game.cn/gameinfocontent/63356/zhiying?P=SPECIAL_ZHIYINN">武将</a><br/>
<a href="http://qjsh.9game.cn/gameinfocontent/63357/zhiying?P=SPECIAL_ZHIYINN">技能</a>
<a href="http://qjsh.9game.cn/gameinfocontent/63358/zhiying?P=SPECIAL_ZHIYINN">训练</a>
<a href="http://qjsh.9game.cn/gameinfocontent/63359/zhiying?P=SPECIAL_ZHIYINN">历练</a>
<a href="http://qjsh.9game.cn/gameinfocontent/63360/zhiying?P=SPECIAL_ZHIYINN">闯关</a>
<a href="http://qjsh.9game.cn/gameinfocontent/63362/zhiying?P=SPECIAL_ZHIYINN">抽奖</a>
<a href="http://qjsh.9game.cn/gameinfocontent/63361/zhiying?P=SPECIAL_ZHIYINN">BOSS</a><br/>
<a href="http://qjsh.9game.cn/gameinfocontent/63363/zhiying?P=SPECIAL_ZHIYINN">弃牌</a>
<a href="http://qjsh.9game.cn/gameinfocontent/63364/zhiying?P=SPECIAL_ZHIYINN">占领</a>
<a href="http://qjsh.9game.cn/gameinfocontent/63365/zhiying?P=SPECIAL_ZHIYINN">反抗</a>
<a href="http://qjsh.9game.cn/gameinfocontent/63366/zhiying?P=SPECIAL_ZHIYINN">解救</a>
<a href="http://qjsh.9game.cn/gameinfocontent/63367/zhiying?P=SPECIAL_ZHIYINN">擂台</a>
<a href="http://qjsh.9game.cn/gameinfocontent/63368/zhiying?P=SPECIAL_ZHIYINN">爵位</a><br/>
<a href="http://qjsh.9game.cn/gameinfocontent/63369/zhiying?P=SPECIAL_ZHIYINN">好友</a>
<a href="http://qjsh.9game.cn/gameinfocontent/63370/zhiying?P=SPECIAL_ZHIYINN">打劫</a>
<a href="http://qjsh.9game.cn/gameinfocontent/63371/zhiying?P=SPECIAL_ZHIYINN">送礼</a>
<a href="http://qjsh.9game.cn/gameinfocontent/63372/zhiying?P=SPECIAL_ZHIYINN">偷将</a>
<a href="http://qjsh.9game.cn/gameinfocontent/63373/zhiying?P=SPECIAL_ZHIYINN">宝箱</a>
<a href="http://qjsh.9game.cn/gameinfocontent/63375/zhiying?P=SPECIAL_ZHIYINN">商城</a>
</div>
<div class="title">温馨提示</div>
<div class="module">
	<p>1.请妥善保管您的个人信息，不要将账号密码告诉他人，由此带来的损失均由个人承担。<br />
	2.Flash版本更省流量.<br />
	3.建议您关闭其他的应用程序，游戏会更加流畅.<br />
    4.适度游戏益脑，沉迷游戏伤身，合理安排时间，享受健康生活。</p>
</div>

</body>
</html>
