<?php
@define('IN_HERO', TRUE);
define('D_BUG', '0');
$_REQUEST['client'] = 0;
define('S_ROOT', dirname(dirname(dirname(__FILE__))) . '/');
include(dirname(dirname(dirname(__FILE__))) . '/includes/class_mysql.php');
include(dirname(dirname(dirname(__FILE__))) . '/includes/class_common.php');	
include(dirname(dirname(dirname(__FILE__))) . '/config.php');
//include(dirname(dirname(dirname(__FILE__))) .'/lang/components/alipay/lang.php');
require(dirname(dirname(dirname(__FILE__))) .'/configs/ConfigLoader.php');

$common = new heroCommon;	
$db = new dbstuff;
$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname'],'utf8');
$db->query("set names 'utf8'");

require_once ("alipay_config.php");
require_once ("class/alipay_service.php");

/******************************mobile_merchant_paychannel**************************************/
$serverid = isset($_REQUEST["qjsh_sid"]) ? $_REQUEST["qjsh_sid"] : null;
$pid = isset($_REQUEST["qjsh_pid"]) ? $_REQUEST["qjsh_pid"] : null;
$amt = isset($_REQUEST["amt"]) ? $_REQUEST["amt"] : null;
$got_ingot = $amt * 10;

$result = $db->query("select nickname,ingot from ol_player where playersid = $pid");
$row =  $db->fetch_array ( $result );
$nick = $row['nickname'];
$ingot = $row['ingot'];

//构造要请求的参数数组，无需改动
$pms0 = array (
	"_input_charset"	=> $_input_charset_GBK,
	"service"		=> $Service_Paychannel,
	"partner"		=> $partner,
	//"out_user"		=>  isset($_REQUEST["pid"]) ? $_REQUEST["pid"] : null	
);

//构造请求函数
$alipay = new alipay_service();

//请求支付前置接口
$result = $alipay->mobile_merchant_paychannel($pms0);

if($result!="验签失败"){	
	$index_url = $req_domain . "index.php?qjsh_sid=$serverid&qjsh_pid=$pid"; 
	$alipayto_url = $req_domain;
		
	$html = '';
	if(isset($result->payChannleResult->lastestPayChannel)) {
		if(!is_null($result->payChannleResult->lastestPayChannel)){
			//有最近使用的支付方式
			//最近使用的cashierCode码
			if(isset($result->payChannleResult->lastestPayChannel->cashierCode)) {
				$lastCode = $result->payChannleResult->lastestPayChannel->cashierCode;
				//最近使用的银行卡
				$lastName = $result->payChannleResult->lastestPayChannel->name;
				
				$html = "<h3>{$alipay_lang['recently_used']}</h3><a href=\"{$alipayto_url}alipayto.php?id=" . $lastCode . "&qjsh_pid={$pid}&amt={$amt}&qjsh_sid={$serverid}\">" + lastName + "</a><br>";
			}
		}
	}
	

	//父类，（区分信用卡还是储蓄卡）
	$topList = $result->payChannleResult->supportedPayChannelList->supportTopPayChannel; // [0] 信用卡 [1] 储蓄卡

	if($topList[0]->cashierCode=="CREDITCARD")
	{		
		$html = $html . "<h3>{$alipay_lang['CREDITCARD']}</h3>";
		//子类，（区分银行）
		$secList=$topList[0]->supportSecPayChannelList->supportSecPayChannel;
		for($j=0; $j<count($secList); $j++)
		{
			$html = $html."<a href=\"{$alipayto_url}alipayto.php?id=".$secList[$j]->cashierCode."&qjsh_pid={$pid}&amt={$amt}&qjsh_sid={$serverid}\">".$secList[$j]->name."信用卡"."</a><br />";
		}
	}
	
	if($topList[1]->cashierCode=="DEBITCARD")
	{
		$html = $html . "<h3>{$alipay_lang['DEBITCARD']}</h3>";
		//子类，（区分银行）
		$secList=$topList[1]->supportSecPayChannelList->supportSecPayChannel;
		for($j=0; $j<count($secList); $j++)
		{
			$html = $html."<a href=\"{$alipayto_url}alipayto.php?id=".$secList[$j]->cashierCode."&qjsh_pid={$pid}&amt={$amt}&qjsh_sid={$serverid}\">".$secList[$j]->name."储蓄卡"."</a><br />";
		}	

	}	
$content =<<<start
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width = device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>{$alipay_lang['choose_rechargeable_way']}</title>
</head>
<style type="text/css">
body{padding:0px; margin:0px; font-size:13px; background-color:#331509; color:#4f1a00;}
p,span,ul,h2,h3,h4{padding:0px; margin:0px;}
h2,h3,h4{color:#000;}
ul li{list-style:none;}
img{border:0px;}
a{color:#4f1a00; }
.Wrap{width:100%; height:auto; margin:0 auto;}
.Header{background:url(Images/bg.jpg) repeat-x; width:100%; height:46px; float:left; text-align:center;}
.Header span{width:48px; height:46px; text-align:center;}
.Main{background-color:#fcd59a; border:3px solid #e6a731; width:96.5%; height:auto; margin:0px 0px 10px 1%; float:left; padding-bottom:10px;}
.CZ{width:auto; height:auto; line-height:30px; margin:0px 6px 0 6px;}
.fhbtn{width:50px; height:28px; float:left; margin:6px 0 0 6px;}
.fhbtn a{width:50px; height:28px; float:left; display:block;}
.chongzhi{width:100%; height:auto; background-color:#ffefdf; border:1px solid #cf7a27; margin-bottom:1px; padding:0px 0;}
.czleft{margin:0 8px 0px 10px;}
.czbtn{background:url(Images/czbtn.png) no-repeat; width:79px; height:23px; border:0px; font-size:12px; display:block; line-height:23px; text-align:center;}
.Tips{height:30px; line-height:30px; margin-left:8px;}
.tips2{line-height:18px; margin:0 8px 5px 8px; display:block;}
.xzje{margin:0 0 0 10px; line-height:30px;}
.qttable{margin:5px 0 0 0px;}
.box{width:80%; height:19px; line-height:19px; margin-bottom:5px; background-color:#FFF; border:1px solid #7f9db9;}
.box_nc{width:80%; height:19px; line-height:19px; font-size:12px; margin-bottom:5px; background-color:#eeeeee; border:1px solid #999999; color:#333333;}
.qdbtn{width:62px; height:28px; float:right; margin:6px 6px 0px 0;}
.qd{background:url(Images/xyb.jpg) no-repeat; width:62px; height:28px; border:0px; margin-bottom:5px; cursor:pointer;}
.zfbzf{padding:0 8px 0 30px; background-color:#eeeeee; display:block; border:1px solid #999999; border-left:0px; border-right:0px;}
.qtzf{padding:6px 8px 0 30px;}
.qtzf a{line-height:22px;}
</style>
<body>
<div class="Wrap">
	<div class="Header"><span><img src="Images/cz.jpg" width="48" height="46" /></span></div>
    <div class="Main">
    <div class="Tips">{$alipay_lang['welcome']}<span>$nick</span></div>
    <!--<div class="tips2">{$alipay_lang['balance']}<strong>{$ingot}</strong>{$alipay_lang['gold_ingot']} </div>-->
    <div class="tips2">{$alipay_lang['curr_recharge']}<strong>{$amt}{$alipay_lang['yuan']}</strong>({$got_ingot}{$alipay_lang['gold_ingot']})</div>
      <div class="CZ">
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="chongzhi">
          <tr>
            <td><h3 class="xzje">{$alipay_lang['choose_rechargeable_way1']}</h3></td>
          </tr>
          <tr>
            <td><span class="zfbzf"><a href="{$alipayto_url}alipayto.php?qjsh_pid={$pid}&amt={$amt}&qjsh_sid={$serverid}">{$alipay_lang['alipay_account']}</a></span></td>
          </tr>
          <tr>
            <td><div class="qtzf">
				$html
            </div></td>
          </tr>
        </table>
</div>
        <div class="fhbtn"><a href="$index_url"><img src="Images/fh.jpg" width="50" height="28" /></a></div>
        <!--<div class="qdbtn"><input name="" type="submit" class="qd" value="" /></div>-->
    </div>
</div>
</body>
</html>
start;

	echo $content;
}
?>