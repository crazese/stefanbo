<?php
require dirname(__FILE__).'/ucpro.php';
include(dirname(__FILE__)."/db.php");
require dirname(dirname(dirname(dirname(__FILE__)))) . '/ucapi/libs/UzoneRestApi.php';
require dirname(__FILE__).'/UcPayAccessAPI.php';

$name = '联通';

$card = isset($_POST['card']) ? trim($_POST['card']) : "";
$pass = isset($_POST['pass']) ? trim($_POST['pass']) : "";
$sel = isset($_POST['sel']) ? $_POST['sel'] : "";
$post_type = isset($_POST['type']) ? $_POST['type'] : "";
$card_type = $post_type;

$uzone_token  = isset($_GET['uzone_token']) ? str_replace(' ','+',urldecode($_GET['uzone_token'])) : '';
if($uzone_token <> '') {
	$UzoneRestApi = new UzoneRestApi($uzone_token);
	$arr['uid'] = $UzoneRestApi->getAuthUid();
}

$checkmsg = isset($_GET['checkmsg']) ? $_GET['checkmsg'] : "";
if($card != "" && $pass != "") {
	if($post_type <> "") {
		$ucid = isset($_POST['ucid']) ? $_POST['ucid'] : "";
		$uzone_token  = isset($_POST['uzone_token']) ? str_replace(' ','+',urldecode($_POST['uzone_token'])) : '';
		$notify_url = $_SC['backUrl']."/components/uc/php/notifyPay.php";//异步
		$param_cardPay=array(
				"store_nbr"=>STORE_NBR,
				"order_id"=>'herool_cz_'.uniqid().rand(1, 100000),//保持唯一
				"user_id"=>$ucid,// 正式
				"rc_type"=>$card_type,         //1：移动充值卡，2：联通充值卡
				"card_amt"=>$sel,
				"card_no"=>$card,
				"card_pwd"=>$pass,
				"op_type"=>"2",
				"prod_nbr"=>PROD_NBR,
				"prod_name"=>'',
				"pay_amt"=>'',//全部支付 2为部分支付，余额进用户U点账户
				"wp_id"=>"",//请留空，此处商户不需要填写
				"notify_url"=>$notify_url//notifyUrl 表示U 点系统在完成支付流程时，将通过该接口向商户发送支付结果信息
		);
	
		$key = PAYKEY;
	
		$url = "https://pay.uc.cn/trade/recharge/index.htm";
		$resArr = HanderReq::sendReqAndGetRes1($url, $key, $param_cardPay );
	
		if($resArr['rsp_code'] == "00") {
			$sql = "insert into ol_uc_order (order_id,user_id,create_time,card_no,card_pwd,card_amt,rc_type)
			values ('".$param_cardPay['order_id']."','2_".$param_cardPay['user_id']."','".time()."','".$param_cardPay['card_no']."',
			'".$param_cardPay['card_pwd']."','".$param_cardPay['card_amt']."','".$param_cardPay['rc_type']."')";
			mysql_query($sql);
				
			header("Location: cg.php?uzone_token=" . base64_encode($uzone_token) . "&order=" . $param_cardPay['order_id']);
		} else {
			$errArr = array(
					'00'=>'交易创建成功',
					'01'=>'请求数据格式不正确',
					'02'=>'商户不存在',
					'03'=>'商户被冻结',
					'04'=>'请求受限（IP 限制）',
					'05'=>'签名不正确',
					'09'=>'产品不存在',
					'06'=>'订单号重复',
					'07'=>'卡序号或密码不正确',
					'08'=>'账户被锁定',
					'99'=>'系统内部错误'
			);
	
			$errMsg = urlencode("支付失败，{$errArr[$resArr['rsp_code']]}");
			header("Location:shibai.php?err=".$errMsg.'&uzone_token='.base64_encode($uzone_token));
		}
	}
} else if(isset($_POST['Submit'])) {
	$checkmsg = '卡号和密码不能为空！';
	$uzone_token  = isset($_POST['uzone_token']) ? str_replace(' ','+',urldecode($_POST['uzone_token'])) : '';
	header("Location:yd.php?uzone_token=".$uzone_token."&checkmsg=".urlencode($checkmsg));exit;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,minimum-scale=1.0, maximum-scale=2.0"/>
<title>联通充值</title>
</head>
<style type="text/css">
body{padding:0px; margin:0px; font-size:13px; background-color:#331509; color:#4f1a00;}
p,span,ul,h3{padding:0px; margin:0px;}
ul li{list-style:none;}
img{border:0px;}
a{color:#4f1a00; text-decoration:none;}
.Wrap{width:100%; height:auto; margin:0 auto;}
.Header{background:url(Images/bg.jpg) repeat-x; width:100%; height:46px; float:left; text-align:center;}
.Header span{width:48px; height:46px; text-align:center;}
.Main{background-color:#fcd59a; border:3px solid #e6a731; width:96.5%; height:auto; margin:0px 0px 10px 1%; float:left; padding-bottom:10px;}
.Tips{width:100%; height:30px; line-height:30px; margin-left:6px;}
.CZ{width:auto; height:auto; margin:1px 6px 0 6px;}
.ListTable{float:left; background-color:#fef0de; border:1px solid #cf7a27; margin-bottom:1px;}
.jine{line-height:22px; text-align:center; margin-bottom:10px; margin-top:5px;}
.green{color:#399b03;}
.tips2{margin:10px 6px;}
.tips2 span{ color:#ff0000;}
h3{margin:5px 0;}
.czleft{margin:0 5px 5px 0;}
.box{width:90%; height:19px; line-height:19px; margin-bottom:5px;}
.fhbtn{width:50px; height:28px; float:left; margin:6px 0 0 6px;}
.qd{background:url(Images/qd.jpg) no-repeat; width:50px; height:28px; border:0px; margin-bottom:5px;}
</style>
<body>
<div class="Wrap">
	<div class="Header"><span><img src="Images/cz.jpg" width="48" height="46" /></span></div>
    <div class="Main">    	
    	<?php 
    		if($checkmsg != "") {
    			echo '<div class="Tips"><font color="red">' . $checkmsg . '</font></div>';
    		} else {
    			echo '<div class="Tips">小提示：请正确选择您的充值金额</div>';
    		}
    	?>
        <div class="CZ">
        <form id='form1' name='form1' method='post' action='<?php echo $_SERVER['PHP_SELF'];?>'>
        <?php
        	if($post_type == '') {
        		echo "<input name='type' type='hidden' id='type' value='2' />";
		        echo "<input name='ucid' type='hidden' id='ucid' value='".$arr['uid']."' />";
		        echo "<input name='uzone_token' type='hidden' id='uzone_token' value='".$uzone_token."' />";
        	}
        ?>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="ListTable">
          <tr>
            <td>
            <div class="jine"><label><input name="sel" type="radio" value="20" checked="checked" />20元</label> <label><input name="sel" type="radio" value="30" />30元</label> <label><input name="sel" type="radio" value="50" />50元</label> <label><input name="sel" type="radio" value="100" />100元</label> <label><input name="sel" type="radio" value="300" />300元</label> <br /><label><input name="sel" type="radio" value="500" />500元</label></div>
            <p align="center"><span class="green">20</span>元人民币将兑换<span class="green">200</span>元宝</p>
            <div class="tips2"><span>注意：</span>请您正确选择金额，不符将可能导致交易失败，可能造成充值卡失效，并可能无法找回！</div>
            </td>
          </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="ListTable">
          <tr>
            <td colspan="3" align="center"><h3>请输入联通充值卡卡号和密码</h3></td>
          </tr>
          <tr>
            <td width="17%" align="right"><span class="czleft">卡号</span></td>
            <td width="59%"><input name="card" type="text" class="box" /></td>
            <td width="24%">&nbsp;</td>
          </tr>
          <tr>
            <td width="17%" align="right"><span class="czleft">密码</span></td>
            <td width="59%"><input name="pass" type="password" class="box" /></td>
            <td><input name="Submit" type="submit" class="qd" value="" /></td>
          </tr>
        </table>
        </form>
        </div>
        <div class="fhbtn"><a href="index.php?uzone_token=<?php echo isset($_GET['uzone_token']) ? $_GET['uzone_token']: ''; ?>"><img src="Images/fh.jpg" width="50" height="28" /></a></div>
    </div>
</div>
</body>
</html>
