<?php
@define('IN_HERO', TRUE);
define('D_BUG', '0');
$_REQUEST['client'] = 0;
define('S_ROOT', dirname(dirname(dirname(__FILE__))) . '/');
include(dirname(dirname(dirname(__FILE__))) . '/includes/class_mysql.php');
include(dirname(dirname(dirname(__FILE__))) . '/includes/class_common.php');	
include(dirname(dirname(dirname(__FILE__))) . '/config.php');
require(dirname(dirname(dirname(__FILE__))) .'/configs/ConfigLoader.php');
require(dirname(dirname(dirname(__FILE__))) .'/model/PlayerMgr.php');
require(dirname(dirname(dirname(__FILE__))) .'/includes/class_memcacheAdapter.php');
//include(dirname(dirname(dirname(__FILE__))) .'/lang/components/tenpay/lang.php');
require(dirname(dirname(dirname(__FILE__))) .'/components/hero_letters/var.php');
require(dirname(dirname(dirname(__FILE__))) .'/components/hero_letters/model.php');
require(dirname(dirname(dirname(__FILE__))) .'/components/hero_letters/controller.php');
require(dirname(dirname(dirname(__FILE__))) .'/components/hero_role/model.php');
if($_SC['domain'] == '10.144.132.230') {
	include(dirname(__FILE__) . '/config2.php');
} else {
	include(dirname(__FILE__) . '/config.php');
}

if(!isset($_REQUEST['submit'])) {
	$common = new heroCommon;	
	$db = new dbstuff;
	$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname'],'utf8');
	$db->query("set names 'utf8'");

	$pid = isset($_REQUEST["qjsh_pid"]) ? $_REQUEST["qjsh_pid"] : null;
	$serverid = isset($_REQUEST["qjsh_sid"]) ? $_REQUEST["qjsh_sid"] : null;
	$server_name = array('sy01'=>$tenpay_lang['server1_name'], 'sy02'=>$tenpay_lang['server2_name'],'sy03'=>$tenpay_lang['server3_name'],'sy04'=>$tenpay_lang['server4_name'],'sy05'=>$tenpay_lang['server5_name'],'sy06'=>$tenpay_lang['server6_name']);
	
	if(!is_numeric($pid)) {echo $tenpay_lang['message_illegal'];exit;}
	$result = $db->query("select nickname,ingot from ol_player where playersid = '$pid'");
	$row =  $db->fetch_array ( $result );
	$nick = $row['nickname'];
	$ingot = $row['ingot'];
} else {
	$amt = $_REQUEST['je_other'];
	if($amt < 1 || !is_numeric($amt)) {
		$amt = 50;
	} else {
		$amt = intval($amt);
	}
	$pid =  $_REQUEST['qjsh_pid'];
	$sid =  $_REQUEST['qjsh_sid'] ;
	if(isset($_REQUEST['myid'])) {
		$common = new heroCommon;	
		$db = new dbstuff;
		$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname'],'utf8');
		$db->query("set names 'utf8'");
		$mc = new MemcacheAdapter_Memcached;	
		$mc->addServer($MemcacheList[0], $Memport);
		$G_PlayerMgr = new PlayerMgr($db,$mc);
		
		$from_pid = $_REQUEST['myid'];
		$ingot = $_REQUEST['ingot'];
		// 对双方发探报
		$player = $G_PlayerMgr->GetPlayer($pid);
		$topalyer_info = $player->baseinfo_;
		
		$player = $G_PlayerMgr->GetPlayer($from_pid);
		$player_info = $player->baseinfo_;
		
		$give_ingot = intval($amt)*10;
		
		$other_recharge = array();
		$other_recharge['playersid'] = $from_pid;
		$other_recharge['toplayersid'] = $from_pid;
		$other_recharge['message'] = array('xjnr'=>"您已经对 {$topalyer_info['nickname']}（ID：{$pid}）执行充值{$give_ingot}元宝的操作，充值结果请核对！");
		$other_recharge['genre'] = 32;
		$other_recharge['request'] ='';
		$other_recharge['type'] = 1;
		$other_recharge['uc'] = '0';
		$other_recharge['is_passive'] = 0;
		$other_recharge['interaction'] = 0;
		$other_recharge['tradeid'] = 0;							
		lettersModel::addMessage($other_recharge);		
		
		$other_recharge = array();
		$other_recharge['playersid'] = $from_pid;
		$other_recharge['toplayersid'] = $pid;
		$other_recharge['message'] = array('xjnr'=>"{$player_info['nickname']}（ID：{$from_pid}）对您执行充值{$give_ingot}元宝的操作，充值结果请核对！");
		$other_recharge['genre'] = 32;
		$other_recharge['request'] ='';
		$other_recharge['type'] = 1;
		$other_recharge['uc'] = '0';
		$other_recharge['is_passive'] = 0;
		$other_recharge['interaction'] = 0;
		$other_recharge['tradeid'] = 0;		
		lettersModel::addMessage($other_recharge);
	}
	Header("Location: payRequest.php?amt=$amt&qjsh_pid=$pid&qjsh_sid=$sid");	
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width = device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title><?php echo $alipay_lang['recharge_title'];?></title>
</head>
<style type="text/css">
body{padding:0px; margin:0px; height:100%; font-size:13px; background-color:#331509; color:#4f1a00;}
p,span,ul,h3,h4{padding:0px; margin:0px;}
ul li{list-style:none;}
img{border:0px;}
a{color:#4f1a00; text-decoration:none;}
html,form{height:100%;}
.Wrap{width:100%; height:100%; margin:0 auto;}
.Header{background:url(Images/bg.jpg) repeat-x; width:100%; height:46px; float:left; text-align:center;}
.Header span{width:48px; height:46px; text-align:center;}
.Main{background-color:#fcd59a; border:3px solid #e6a731; width:98%; height:90%; margin:0px 0px 1% 1%; float:left; padding-bottom:10px;}
.CZ{width:auto; height:auto; line-height:30px; margin:10px 6px 0 6px;}
.ListTable tr td span.czl{margin-left:5px; color:#4f1a00;}
.List{height:auto; line-height:63px; margin:1px 6px 0 6px; width:auto;}
.ListTable{float:left; background-color:#fef0de; border:1px solid #cf7a27; margin-bottom:1px;}
.ybimg{margin-left:5px;}
.ListTable tr td span{color:#399b03;}
.jtimg{margin-right:10px;}
.fhbtn{width:50px; height:28px; float:left; margin:6px 0 0 6px;}
.fhbtn a{width:50px; height:28px; float:left; display:block;}
.chongzhi{width:100%; height:auto; background-color:#ffefdf; border:1px solid #cf7a27; margin-bottom:1px; padding:5px 0;}
.czleft{margin:0 8px 0px 10px;}
.czbtn{background:url(Images/czbtn.png) no-repeat; width:79px; height:23px; border:0px; font-size:12px; display:block; line-height:23px; text-align:center;}
.tips{line-height:18px; margin:0 8px 5px 8px; display:block;}
.xzje{margin:0 0 0 10px; line-height:30px;}
.tc{width:260px; height:auto; margin:0 auto;}
.tc_bg{background-color:#ffefdf; border:1px solid #cf7a27; float:left; width:258px; height:auto;}
.tc_title{width:125px; height:34px; margin:10px auto 5px auto;}
.tc_je{width:200px; height:auto; line-height:30px; margin:0 auto;}
.tc_je span{width:100px; height:40px; float:left; line-height:40px; display:block;}
.qttable{margin:5px 0 0 0px;}
.tc_je label{margin-right:6px;}
.box{width:80%; height:19px; line-height:19px; margin-bottom:5px; background-color:#FFF; border:1px solid #7f9db9;}
.box_nc{width:80%; height:19px; line-height:19px; font-size:12px; margin-bottom:5px; background-color:#eeeeee; border:1px solid #999999; color:#333333;}
.qdbtn{width:62px; height:28px; float:right; margin:6px 6px 0px 0;}
.qd{background:url(Images/xyb1.jpg) no-repeat; width:62px; height:28px; border:0px; margin-bottom:5px;font-size:13px;}
</style>
<body>
<div class="Wrap">
	<div class="Header"><span><img src="Images/cz1.jpg" width="48" height="46" /></span></div>
    <form action="<?php if($_GET['qjsh_sid'] == 'sy01'){ echo $req_domain . '?uri=index2.php';} else if($_GET['qjsh_sid'] == 'sy02') {echo $req_domain_2f . '?uri=index2.php';} else if($_GET['qjsh_sid'] == 'sy03') {echo $req_domain_3f . '?uri=index2.php';} else if($_GET['qjsh_sid'] == 'sy04') {echo $req_domain_4f . '?uri=index2.php';} else if($_GET['qjsh_sid'] == 'sy05') {echo $req_domain_5f . '?uri=index2.php';} else if($_GET['qjsh_sid'] == 'sy06') {echo $req_domain_6f . '?uri=index2.php';} ?>" method="post">	
    <table border="0" cellspacing="0" cellpadding="0" class="Main">
    <tr><td valign="top">
      <div class="CZ">
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="chongzhi">
			<input type="hidden" name="qjsh_pid" value="<?php echo $_GET['qjsh_pid'] ?>">
			<input type="hidden" name="qjsh_sid" value="<?php echo $_GET['qjsh_sid'] ?>">
			<?php 
				if(isset($_GET['qjsh_myid'])) {
					echo "<input type=\"hidden\" name=\"myid\" value=\"{$_GET['qjsh_myid']}\">";
					echo "<input type=\"hidden\" name=\"ingot\" value=\"{$ingot}\">";
				}
			?>
          <tr>
            <td align="right"><span class="czleft"><?php echo $tenpay_lang['recharge_server'];?></span></td>
            <td><h4><?php echo $server_name[$serverid]; ?></h4></td>
          </tr>
          <tr>
            <td width="31%" align="right"><span class="czleft"><?php echo $tenpay_lang['nickname']; ?></span></td>            
			<td width="69%"><?php echo $nick; ?></td>
          </tr>
		  <tr>
            <td width="31%" align="right"><span class="czleft"><?php if(!isset($_GET['qjsh_myid'])) echo $tenpay_lang['balance']; ?></span></td>            
			<td width="69%"><?php if(!isset($_GET['qjsh_myid'])) echo $ingot; ?></td>
          </tr>
          <tr>
            <td colspan="2"><h3 class="xzje"><?php echo $tenpay_lang['input_amt']; ?></h3></td>
          </tr>
          <tr>
          	<td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="qttable">
              <tr>
                <td align="right"><span class="czleft"></span></td>
                <td><input name="je_other" type="text" class="box" value='1' /></td>
              </tr>
            </table></td>
          </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="chongzhi">
          <tr>
            <td><h3 class="xzje"><?php echo $tenpay_lang['tips']; ?></h3></td>
          </tr>
          <tr>
            <td colspan="2"><span class="tips"><?php echo $tenpay_lang['tips2']; ?></span></td>
          </tr>
        </table>
</div>
        <div class="fhbtn"><a href="<?php if($_GET['qjsh_sid'] == 'sy01'){ echo $req_domain . '?uri=index.php&qjsh_pid='.$pid.'&qjsh_sid='.$serverid;} else if($_GET['qjsh_sid'] == 'sy02') {echo $req_domain_2f . '?uri=index.php&qjsh_pid='.$pid.'&qjsh_sid='.$serverid;} else if($_GET['qjsh_sid'] == 'sy03') {echo $req_domain_3f . '?uri=index.php&qjsh_pid='.$pid.'&qjsh_sid='.$serverid;} else if($_GET['qjsh_sid'] == 'sy04') {echo $req_domain_4f . '?uri=index.php&qjsh_pid='.$pid.'&qjsh_sid='.$serverid;} else if($_GET['qjsh_sid'] == 'sy05') {echo $req_domain_5f . '?uri=index.php&qjsh_pid='.$pid.'&qjsh_sid='.$serverid;} else if($_GET['qjsh_sid'] == 'sy06') {echo $req_domain_6f . '?uri=index.php&qjsh_pid='.$pid.'&qjsh_sid='.$serverid;}?>"><img src="Images/fh.jpg" width="50" height="28" /></a></div>
        <div class="qdbtn"><input name="submit" type="submit" class="qd" value="<?php echo $tenpay_lang['next_step']; ?>" /></div>
    </td></tr></table>
    </form>
</div>
</body>
</html>