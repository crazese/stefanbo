<?php
if(SYPT == 1) {
	//include(dirname(dirname(dirname(__FILE__))) . '/config.php');
	if($_SC['domain'] == '10.144.132.230') {
		define('CALLBACK_URL1', 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy980-1.php');	
		define('CALLBACK_URL2', 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy980-2.php');	
		define('CALLBACK_URL3', 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy980-3.php');
		
		define('SZF_CALLBACK_URL1', 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy_szf980-1.php');
		define('SZF_CALLBACK_URL2', 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy_szf980-2.php');
		define('SZF_CALLBACK_URL3', 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy_szf980-3.php');
	} else {
		define('CALLBACK_URL1', 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy786-1.php');	
		define('CALLBACK_URL2', 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy786-2.php');	
		define('CALLBACK_URL3', 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy786-3.php');
		define('CALLBACK_URL4', 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy786-4.php');
		define('CALLBACK_URL5', 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy786-5.php');
		define('CALLBACK_URL6', 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy786-6.php');
		
		define('SZF_CALLBACK_URL1', 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy_szf786-1.php');
		define('SZF_CALLBACK_URL2', 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy_szf786-2.php');
		define('SZF_CALLBACK_URL3', 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy_szf786-3.php');
		define('SZF_CALLBACK_URL4', 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy_szf786-4.php');
		define('SZF_CALLBACK_URL5', 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy_szf786-5.php');
		define('SZF_CALLBACK_URL6', 'http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy_szf786-6.php');
	}
} else {	
	define('CALLBACK_URL', $_SC['yeepay_callback_self']);
	//define('CALLBACK_URL', 'http://117.135.138.248:8080/components/hero_chongzhi/yp_cbk.php');
	//define('CALLBACK_URL', 'http://119.97.226.138:9000/zc/components/hero_chongzhi/yp_cbk.php');
	define('SZF_CALLBACK_URL', 'http://117.135.138.248:8080/components/hero_chongzhi/szf_cbk.php');
}

define('SHOW_PAY_MESSAGE_NUM', 2);

define('SZF_REQURL_SNDAPRO',"http://pay3.shenzhoufu.com/interface/version3/serverconnszx/entry-noxml.aspx");
define('SZF_P1_MERID',"185432");//"185432" "151525"
define('SZF_PRIVATEKEY',"123456");//"123456"
define('SZF_DES',"l9P3edzj7HA=");//"l9P3edzj7HA=" "QNzVtrZFuXY="

class chongzhiModel {
	// 在数据库中生成充值卡订单并提交到易宝支付网关
	public static function dycz($userid, $playersid, $czfs, $czme, $czkh, $czkmm) {
		global $common,$db,$_SC,$chongzhi_lang,$G_PlayerMgr;
		
		//$common->insertLog("userid=$userid, playersid=$playersid, czfs=$czfs, czme=$czme, czkh=$czkh, czkmm=$czkmm");
		if(is_null($czfs) || empty($czme) || empty($czkh) || empty($czkmm)) {
			$returnValue['status'] = 1021;
			$returnValue['message'] = $chongzhi_lang['model_message_illegal'];
			return $returnValue;
		}
		
		// 获取userid和注册时间
		$result = $db->query("SELECT register_time FROM ".$common->tname('user')." WHERE userid = '". $userid ."' LIMIT 1");
		$rows = $db->fetch_array($result);
		
		date_default_timezone_set('PRC');
		$register_time = $rows['register_time'];
		$curr_month = date('m');
		
		//include(OP_ROOT.'./hero_yeepay'.DIRECTORY_SEPARATOR.'YeePayCommon.php');	
		//$czme1 = $czme / 100;

		#商家设置用户购买商品的支付信息.
		#商户订单号.提交的订单号必须在自身账户交易中唯一.
		$p2_Order			= chongzhiModel::getOrdNo();
		#用户选择支付卡面额
		$p3_Amt				= $czme;
		#较验订单金额和充值卡面额是否一致
		$p4_verifyAmt		= true;  // 如需要全扣，此处填写false
		#产品名称
		$p5_Pid				= " ";
		#iconv("UTF-8","GBK//TRANSLIT",$_POST['p5_Pid']);
		#产品类型
		$p6_Pcat			= " ";
		#iconv("UTF-8","GBK//TRANSLIT",$_POST['p6_Pcat']);
		#产品描述
		$p7_Pdesc			= "YuanBao";
		#iconv("UTF-8","GBK//TRANSLIT",$_POST['p7_Pdesc']);
		#商户接收交易结果通知的地址,易宝支付主动发送支付结果(服务器点对点通讯).通知会通过HTTP协议以GET方式到该地址上.	
		if(SYPT == 1) {
			if($_SC['fwqdm'] == 'sy01') {
				$p8_Url				= CALLBACK_URL1;
			} else if($_SC['fwqdm'] == 'sy02') {
				$p8_Url				= CALLBACK_URL2;
			} else if($_SC['fwqdm'] == 'sy03') {
				$p8_Url				= CALLBACK_URL3;
			} else if($_SC['fwqdm'] == 'sy04') {
				$p8_Url				= CALLBACK_URL4;
			}else if($_SC['fwqdm'] == 'sy05') {
				$p8_Url				= CALLBACK_URL5;
			}else if($_SC['fwqdm'] == 'sy06') {
				$p8_Url				= CALLBACK_URL6;
			}
		} else {
			$p8_Url				= CALLBACK_URL;
		}
		
		// 获取是否是为他人充值
		$topid = _get('dfid');	
		if($topid == null) {
			$topid = 0;
			#玩家ID，支付成功会返回，用于给玩家冲元宝
			$pa_MP				= $playersid . '|' .SYPT;
		} else {
			#对方玩家ID，支付成功会返回，用于给其他玩家冲元宝
			$pa_MP				= $topid . '|' .SYPT . '|' . $playersid;
		}
				
		#玩家ID，支付成功会返回，用于给玩家冲元宝
		//$pa_MP				= $playersid . '|' .SYPT;
		#iconv("UTF-8","GB2312//TRANSLIT",$_POST['pa_MP']);
		#卡面额
		$pa7_cardAmt		=  $czme;
		#支付卡序列号.
		$pa8_cardNo			= $czkh;
		#支付卡密码.
		$pa9_cardPwd		= $czkmm;
		#支付通道编码
		$pd_FrpId			= $czfs;
		#应答机制
		$pr_NeedResponse	= "1";
		#用户唯一标识
		$pz_userId			= $userid;
		#用户的注册时间
		$pz1_userRegTime	= $register_time;

		// 生成订单
		$insertInfo = array(
				'playersid'=>$playersid,
				'type'=>'ChargeCardDirect',
				'memberId'=>'10011285454',
				'orderNo'=>$p2_Order,
				'orderAmt'=>$czme, // 如需要全扣，此处填写$czme
				'currency'=>"",
				'descr'=>$p7_Pdesc,
				'extInfo'=>$pa_MP,
				'cardAmt'=>$pa7_cardAmt,
				'cardNo'=>$czkh,
				'cardPwd'=>$czkmm,
				'frpId'=>$pd_FrpId,
				'hmac'=>'',  // 此处在提交订单API请求成功后会UPDATE
				'verify'=>0,
				'errcode'=>"",
				'trxId'=>"",
				'ordDate'=>time(),
				'toplayersid'=>$topid // 对方pid
		);
		$newID = $common->inserttable('yeepay_ord',$insertInfo);
		
		ob_end_clean();
		header("Connection: close");
		ob_start();

		$returnValue['status'] = 0;
		$returnValue['rsn'] = intval(_get('ssn'));
		echo json_encode($returnValue);
		echo str_pad('', 4086);

		$size=ob_get_length();
		header("Content-Length: $size");
		ob_end_flush();
		ob_flush();
		flush();
	
		$ret_value = annulCard($p2_Order,$p3_Amt,$p4_verifyAmt,$p5_Pid,$p6_Pcat,$p7_Pdesc,$p8_Url,$pa_MP,$pa7_cardAmt,$pa8_cardNo,$pa9_cardPwd,$pd_FrpId,$pz_userId,$pz1_userRegTime);
				
		if($ret_value['status'] == 0) {
			$updateOrd['hmac'] = $ret_value['hmac'];
			$whereOrd['orderNo'] = $p2_Order;
			$common->updatetable('yeepay_ord', $updateOrd, $whereOrd);
			
			//$returnValue['status'] = 0;
		} /*else if($ret_value['status'] == 3) {
			$returnValue['status'] = 3;
			$returnValue['message'] = '非法请求';
		}*/
		
		//return $returnValue; 
		exit;
	}

	public static function synYeePayCall($p2_Order,$p3_Amt,$p4_verifyAmt,$p5_Pid,$p6_Pcat,$p7_Pdesc,$p8_Url,$pa_MP,$pa7_cardAmt,$pa8_cardNo,$pa9_cardPwd,$pd_FrpId,$pz_userId,$pz1_userRegTime){
		$p5_Pid = trim($p5_Pid);
		$p6_Pcat = trim($p6_Pcat);
		$serverIp = $_SERVER['SERVER_ADDR'];
		$serverPort = $_SERVER["SERVER_PORT"];
		$url = "/components/hero_chongzhi/synYeePayCall.php";
		$getStr = "?p2_Order={$p2_Order}&p3_Amt={$p3_Amt}&p4_verifyAmt={$p4_verifyAmt}&p5_Pid={$p5_Pid}&p6_Pcat={$p6_Pcat}&p7_Pdesc={$p7_Pdesc}&p8_Url={$p8_Url}&pa_MP={$pa_MP}&pa7_cardAmt={$pa7_cardAmt}&pa8_cardNo={$pa8_cardNo}&pa9_cardPwd={$pa9_cardPwd}&pd_FrpId={$pd_FrpId}&pz_userId={$pz_userId}&pz1_userRegTime={$pz1_userRegTime}";

		$fp = fsockopen('tcp://127.0.0.1', $serverPort, $errno, $errstr, 1);
		fputs($fp, "GET ".$url.$getStr." HTTP/1.1\r\n");
		fputs($fp, "Host: ".$serverIp."\r\n");
		fputs($fp, "Content-Length: 0\r\n");
		fputs($fp, "Content-Type: text/html \r\n");
		fputs($fp, "Connection: close\r\n\r\n");
		$result = fread($fp, 0);
		//echo $result;

		fclose($fp);
	}
	
	// 获取订单编号
	public static function getOrdNo() {
		date_default_timezone_set('PRC');
		list($s1, $s2) = explode(' ', microtime());
		$millisecond = (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
		return "jofgame_qjsh_" . date('YmdHis') . $millisecond . mt_rand(1, 999999);
	}
	
	public static function dyczfs() {
		global $common,$db;
		
		$czfsInfo = getCzfsInfo();
		$czfs = array();
		foreach ($czfsInfo as $k=>$v) {						
			$jeStr = array();
			foreach ($v['je'] as $key=>$value) {
				$jeStr[] = $value;
			}
			$jeStr = implode(',', $jeStr);
			$czfs[] = array('czfsid'=>$v['czfsid'], 'mc'=>$v['mc'], 'je'=>$jeStr);
		}
		$czfs = array_values($czfs);
		
		$roleInfo['userid'] = _get('userId');
		roleModel::getRoleInfo($roleInfo);
		$returnValue = array('status'=>0, 'czfs'=>$czfs, 'yb'=>$roleInfo['ingot']);
		return  $returnValue;
	}
	
	public static function dyczjl($playersid, $page) {
		global $common, $db, $chongzhi_lang, $G_PlayerMgr;
		
		if (empty($playersid) || empty($page)) {
			$returnValue['status'] = 3;
			$returnValue['message'] = $chongzhi_lang['model_message_illegal'];
			return $returnValue;
		}
				
		if(empty($page)) {
			$page = 1;
		}
		
		//$result = $db->fetch_array($db->query("SELECT count(*) as cnt FROM " . $common->tname ( 'yeepay_ord') . " where playersid = {$playersid}"));		
		$sql = "select count(*) as cnt from (SELECT frpid as mc, orderAmt as je, ordDate as sj, verify as zt, errcode as reason, toplayersid as topid FROM ".$common->tname('yeepay_ord');
		//$sql = "SELECT frpid as mc, orderAmt as je, orderNo as sj, verify as zt, errcode as reason FROM ".$common->tname('yeepay_ord');
		$sql .= " WHERE playersid = {$playersid} ";
		$sql .= " UNION SELECT '财付通' as mc, total_fee as je, notify_time as sj, verify as zt, -1 as reason, 0 as topid FROM ".$common->tname('tenpay_ord');
		$sql .= " WHERE playersid = {$playersid}  AND verify = 1 ";
		$sql .= "UNION SELECT '支付宝' as mc, total_fee as je, notify_time as sj, verify as zt, -1 as reason, 0 as topid FROM ".$common->tname('alipay_ord');
		$sql .= " WHERE playersid = {$playersid}  AND verify = 1) as cz";
		$result = $db->fetch_array($db->query($sql));
		
		$zs = $result['cnt'];		
		$pageRowNum = SHOW_PAY_MESSAGE_NUM;
		
		$page_z = 0;
		if($zs > 0) {
			$page_z = ceil($zs / $pageRowNum);
			if($page > $page_z) {
				$page = $page_z;
			}
		}else{
			$page = 1;
		}
		$_start = ($page-1) * $pageRowNum;
		$_end = 0;
		if($_start + $pageRowNum < $zs) {
			$_end = $pageRowNum;
		}else{
			$_end = $zs;
		}		
		
		/*
		$sql = "SELECT frpid as mc, orderAmt as je, ordDate as sj, verify as zt, errcode as reason, toplayersid as topid FROM ".$common->tname('yeepay_ord');
		//$sql = "SELECT frpid as mc, orderAmt as je, orderNo as sj, verify as zt, errcode as reason FROM ".$common->tname('yeepay_ord');
		$sql .= " WHERE playersid = {$playersid} ORDER BY ordDate DESC LIMIT {$_start},{$_end}";
		*/
		$db->query("set names 'utf8'");
		$sql = "SELECT frpid as mc, orderAmt as je, ordDate as sj, verify as zt, errcode as reason, toplayersid as topid FROM ".$common->tname('yeepay_ord');
		//$sql = "SELECT frpid as mc, orderAmt as je, orderNo as sj, verify as zt, errcode as reason FROM ".$common->tname('yeepay_ord');
		$sql .= " WHERE playersid = {$playersid} ";
		$sql .= " UNION SELECT '财付通' as mc, total_fee as je, notify_time as sj, verify as zt, -1 as reason, 0 as topid FROM ".$common->tname('tenpay_ord');
		$sql .= " WHERE playersid = {$playersid}  AND verify = 1 ";
		$sql .= "UNION SELECT '支付宝' as mc, total_fee as je, notify_time as sj, verify as zt, -1 as reason, 0 as topid FROM ".$common->tname('alipay_ord');
		$sql .= " WHERE playersid = {$playersid}  AND verify = 1 ORDER BY sj DESC LIMIT {$_start},{$_end}";
		//echo $sql;exit;
		$pay_res = $db->query($sql);
		while($pay_result[] = $db->fetch_array($pay_res));
		array_pop($pay_result);
		//print_r($pay_result);exit;
		
		date_default_timezone_set('PRC');
		$czmc = getCzMcInfo();
		$jl = array();		
		foreach ($pay_result as $key=>$value) {
			$value['sj'] = date('Y-m-d H:i:s', $value['sj']);
			//$value['sj'] = substr(str_replace('jofgame_qjsh_', '', $value['sj']), 0, 14);
			//$value['sj'] = substr($value['sj'], 0, 4) . '-' . substr($value['sj'], 4, 2) . '-' . substr($value['sj'], 6, 2) . ' ' . substr($value['sj'], 8, 2) . ':' . substr($value['sj'], 10, 2) . ':' . substr($value['sj'], 12, 2);
			$value['mc'] = isset($czmc[$value['mc']]) ? $czmc[$value['mc']] : $value['mc'];
			$sbyy = $value['zt'] == 2 ? getFailReason($value['reason']) : ' ';
			if($value['zt'] == 0 || $value['zt'] == 2){
				$value['je'] = $chongzhi_lang['model_message_noget'];
			}
			if($value['topid'] != 0) {
				$player = $G_PlayerMgr->GetPlayer($value['topid']);
				if(!$player)  return array('status'=>3, 'message'=>$sys_lang[7]);
				$other_info = $player->baseinfo_;
				$jl[] = array(
				'mc'=>$value['mc'],
				'je'=>$value['je'],
				'sj'=>$value['sj'],
				'zt'=>intval($value['zt']),
				'message'=>$sbyy,
				'df'=>array('id'=>$value['topid'], 'nc'=>$other_info['nickname'])
				);
			} else {
				$jl[] = array(
				'mc'=>$value['mc'],
				'je'=>$value['je'],
				'sj'=>$value['sj'],
				'zt'=>intval($value['zt']),
				'message'=>$sbyy				
				);
			}
		}
		
		$jl = array_values($jl);
		
		$returnValue['status'] = 0;
		$returnValue['zys'] = $page_z;
		$returnValue['page'] = $page;
		$returnValue['czjl'] = $jl;
		
		$roleInfo['userid'] = _get('userId');
		roleModel::getRoleInfo($roleInfo);
		
		$returnValue['yb'] = intval($roleInfo['ingot']);
		
		return $returnValue;
	}
	
	//uc充值通知接口
	public static function ucpayid() {
		global $common, $db, $_SGLOBAL;
		$insert['orderId'] = _get('payid');
		//$insert['callbackInfo'] = mysql_escape_string($_POST['callbackInfo']);
		$insert['createTime'] = $_SGLOBAL['timestamp'];
		$checksql = "SELECT * FROM ".$common->tname('uc_client_payinfo');
		$res_check = $db->query($checksql);
		$checknum = $db->num_rows($res_check);
		if ($checknum == 0) {
			$common->inserttable('uc_client_payinfo',$insert);
			//$sql = "SELECT * FROM ".$common->tname('uc_payinfo')." WHERE orderId = '".$insert['orderId']."' && callbackInfo = '".$insert['callbackInfo']."' && processed = '0' LIMIT 1";
			$sql = "SELECT * FROM ".$common->tname('uc_payinfo')." WHERE orderId = '".$insert['orderId']."' && processed = '0' && orderStatus = 'S' LIMIT 1";
			$result = $db->query($sql);
			$rows = $db->fetch_array($result);
			if (!empty($rows)) {
		    	$yb = floor($rows['amount'] * 10);
		    	$sql_user = "SELECT a.userid,b.playersid,b.ingot FROM ".$common->tname('user')." a,".$common->tname('player')." b WHERE a.username = '".$rows['ucid']."' && a.userid = b.userid LIMIT 1";
		    	$res_user = $db->query($sql_user);
		    	$rows_user = $db->fetch_array($res_user);
		    	if (!empty($rows_user)) {
		    		$playersid = $rows_user['playersid'];
		    		$sql_p = "UPDATE ".$common->tname('player')." SET ingot = ingot + ".$yb." WHERE playersid = '$playersid' LIMIT 1";
		    		$db->query($sql_p);    			
		    		vipChongzhi($playersid, $rows['amount'], $yb, $rows['orderId']);
		    		$db->query("UPDATE ".$common->tname('uc_payinfo')." SET processed = 1 WHERE orderId = '".$rows['orderId']."' LIMIT 1");
		    		$db->query("UPDATE ".$common->tname('uc_client_payinfo')." SET processed = 1 WHERE orderId = '".$rows['orderId']."' LIMIT 1");
		    		$value['status'] = 0;
		    		$value['yb'] = $rows_user['ingot'] + $yb;
		    		$value['hqyb'] = $yb;
		    	} else {
		    		$value['status'] = 1001;
		    	}		
			} else {
				$value['status'] = 1001;
			}
		} else {
			$value['status'] = 1001;
		}	
		return $value;	
	}
	//获取苹果商店产品信息
	public static function iosczcplb($sfgq = 1) {
		global $mc, $common, $db;
		if (!$productInfo = $mc->get(MC.'app_product_'.$sfgq)) {
			$productInfo = array();
			$sql = "SELECT * FROM ".$common->tname('app_productInfo')." WHERE publish = 1 && sfgq = '$sfgq' ORDER BY yb ASC";
			$result = $db->query($sql);
			while ($rows = $db->fetch_array($result)) {
				$productInfo[] = array('productid'=>$rows['productID'],'mc'=>$rows['mc'],'iid'=>$rows['iid'],'jg'=>$rows['jg'],'yb'=>intval($rows['yb']));
			}
			if (!empty($productInfo)) {
				$mc->set(MC.'app_product_'.$sfgq,$productInfo,0,0);
			}
		}
		if (!empty($productInfo)) {
			$value = array('status'=>0,'iosczlb'=>$productInfo);			
		} else {
			$value['status'] = 1022;
		}
		return $value;
	}
	
	//获取ios端游充值记录
	public static function iosczjl($curPage,$czly = 1) {
		global $db, $common, $userId;
		$perPage = 2;
		if ($curPage == 0) {
			$curPage = 1;
		}
		if ($czly == 3) {
			$item = " && sfgq = '3'";
		} else {
			$item = '';
		}
		$totalNum = $db->num_rows($db->query("SELECT uid FROM ".$common->tname('app_payinfo')." WHERE uid = '$userId'$item"));
		$totalPage = ceil($totalNum / $perPage);		
		if ($totalNum > 0) {
			if ($curPage > $totalPage && $curPage > 1) {
				$curPage = $totalPage;
			}
			$start = ($curPage - 1) * $perPage;	
			$result = $db->query("SELECT * FROM ".$common->tname('app_payinfo')." WHERE uid = '$userId'$item ORDER BY intID DESC LIMIT $start, $perPage");
			
			$orderInfo = array();
			while ($rows = $db->fetch_array($result)) {
				$orderInfo[] = array('je'=>$rows['price'],'mc'=>$rows['product_name'],'sj'=>date('Y-m-d H:i:s',floor($rows['purchase_date_ms'] / 1000)),'zt'=>'购买成功');
				$pinfo = null;
			}
			if (!empty($orderInfo)) {
				$returnValue = array('status'=>0,'zys'=>$totalPage,'page'=>$curPage,'czjl'=>$orderInfo);
			} else {
				$returnValue = array('status'=>0,'zys'=>0);
			}
		} else {
			$returnValue = array('status'=>0,'zys'=>0);
		}	
		return $returnValue;
	}
	
	//获取产品金额和名称
	public static function getPrice($productID) {
		global $db, $common, $chongzhi_lang;
		$productInfo = chongzhiModel::iosczcplb();
		$price = 0;
		$title = $chongzhi_lang['model_message_noproduct'];
		if ($productInfo['status'] == 0) {			
			$pinfo = $productInfo['iosczlb']; 
			foreach ($pinfo as $pinfoValue) {
				if ($pinfoValue['productid'] == $productID) {
					$price = $pinfoValue['jg'];
					$title = $pinfoValue['mc'];
					break;
				}
			}
		}
		if ($price == 0) {
			$result = $db->query("SELECT * FROM ".$common->tname('app_productInfo')." WHERE productID = '$productID' LIMIT 1");
			$rows = $db->fetch_array($result);
			if (empty($rows)) {
				return array('title'=>$chongzhi_lang['model_message_noproduct'],'jg'=>0);
			} else {
				$price = $rows['jg'];
			}
		}
		return array('title'=>$title,'jg'=>$price);		
	}
	
	public static function http_post($host, $port, $path, $data) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://'.$host.$path);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}	
	
	public static function genUcSingn($cpid, $content, $apiKey) {
		////md5(cpid+签名内容+apiKey)
		ksort($content, SORT_ASC);
		$tmpContent = null;		
		foreach($content as $key=>$value) {
			$tmpContent .= $key . '=' . $value;
		}
		$sign = strtolower(md5($cpid . $tmpContent . $apiKey));
		
		return $sign;
	}
	
	// uc页游支付申请
	public static function uczfsq($playersid, $je) {
		global $db, $common, $_SC, $mc, $chongzhi_lang;
		
		$id = time() . rand(100000, 999999);
		if(isset($_COOKIE['hero_9y_sid']) ){
			$sid = $_COOKIE['hero_9y_sid'];
		} else {
			$sid = $_SESSION['hero_9y_sid'];
		}		
		//$data = array('sid'=>$sid, 'payAmount'=>$je, 'roleId'=>$playersid, 'roleName'=>'', 'grade'=>'', 'redirectUrl'=>$_SC['redirectUrl'], 'callbackInfo'=>'', 'callbackUrl'=>$_SC['callbackUrl']);
		$data = array('sid'=>$sid, 'payAmount'=>$je, 'roleId'=>$playersid, 'roleName'=>'', 'grade'=>'', 'redirectUrl'=>$_SC['redirectUrl'], 'callbackInfo'=>'', 'callbackUrl'=>'');
		$sign = chongzhiModel::genUcSingn($_SC['9ycpId'], $data, $_SC['9yapiKey']);
		$game = array('cpId'=>intval($_SC['9ycpId']),'gameId'=>intval($_SC['9ygameId']),'channelId'=>intval($_SC['9ychannelId']),'serverId'=>intval($_SC['9yserverId']));

		$postdata = json_encode(array(
				"id"=>$id,
				"service"=>"pay.page.wap.create",
				"data"=>$data,
				"game"=>$game,
				"sign"=>$sign,
				"encrypt"=>"md5"));
		//echo $postdata;
		$ucdata = chongzhiModel::http_post($_SC['9yapiUrl'], 80, '/gameservice', $postdata);
		//print_r($ucdata);
		if (!empty($ucdata)) {
			$userInfo = json_decode($ucdata,true);
			if ($userInfo['state']['code'] == 1) {		
				$value['status'] = 0;		
				$value['sid'] = $userInfo['data']['sid'];
				$orderId = $userInfo['data']['orderId'];
				setcookie("hero_9y_sid", $value['sid'], time()+60*60*24*30, '/');
				$mc->set(MC.$orderId.'_9ysid', $value['sid'], 0, 3600);
				$value['wcczdz'] = $userInfo['data']['payUrl'];
			} else {
				$value['status'] = intval($userInfo['state']['code']);
				//$value['message'] = $userInfo['state']['msg'];
				$value['message'] = $chongzhi_lang['model_message_getordfaild'];
			}
		} else {
			$value['status'] = 3;
			$value['message'] = $chongzhi_lang['model_message_connectfaild'];
		}

		return $value;
	}

	// 生成宜搜交易签名
	public static function genYsSingn($content) {
		$apiKey = '7DA95D6C9E5EFCEDBE83AF7B8D9FEC8B';	
		ksort($content, SORT_ASC);
		$tmpContent = null;		
		foreach($content as $key=>$value) {
			if($tmpContent == null) {
				$tmpContent .= $key . '=' . $value;
			} else {
				$tmpContent .= '&' . $key . '=' . $value;
			}
		}
		$sign = md5($tmpContent . $apiKey);//echo $tmpContent;
		
		return $sign;
	}	

	// 宜搜支付申请
	// 宜搜支付申请
	public static function yszfsq($playersid, $czme) {
		global $db, $common, $_SC, $mc, $chongzhi_lang;
	
		$czme = intval($czme);
		$yb = $czme * 10;
		$ord_no = chongzhiModel::getOrdNo();
		$db->query("insert into ol_easoupay_ord(id, playersid, ord_no, ord_amt, cashier_code, verify) values(null, {$playersid}, '{$ord_no}', {$czme}, 'eft', 0)");
		//echo "insert into ol_easoupay_ord(id, playersid, ord_no, ord_amt, cashier_code, verify) values(null, {$playersid}, '{$ord_no}', {$czme}, 'eft', 0)";
		// 通知地址、回显地址此处需根据情况修改
		$content = array('appId'=>1357,'notifyUrl'=>'http://'.$_SERVER['HTTP_HOST'].'/ucpay/esoupay.php','partnerId'=>'1000100010001009','payerId'=>8,'qn'=>1,'reqFee'=>$czme,'redirectUrl'=>urlencode('http://qjsh.wan.easou.com/HeroOl_easou/herool.html'),'separable'=>'false','tradeId'=>$ord_no,'tradeName'=>$yb.'yuanbao','tradeDesc'=>$yb.'yuanbao');
		
		$sign = chongzhiModel::genYsSingn($content);
		//$show = 'https://service.pay.easou.com/wap/trade.e?';
		$show = 'http://service.pay.easou.com/wap/trade.e?';
		foreach ($content as $key=>$showvalue) {
			$show .= "{$key}={$showvalue}&";	
		}
		$show = rtrim($show,'&');
		$show = $show.'&sign='.$sign;
		
		$value['status'] = 0;
		$value['wcczdz'] = $show;
		return $value;
	}
		
	// 神州付充值
	public static function dyszfcz($getInfo) {
		global $common, $db, $chongzhi_lang;//, $szf_privateKey, $szf_p1_MerId, $szf_reqURL_SNDApro, $szf_DES;
		
		if( empty($getInfo['payMoney']) || is_null($getInfo['cardType']) ||	empty($getInfo['cardId']) || 
				empty($getInfo['cardPW']) || empty($getInfo['cardMoney']) ) {
			$returnValue['status'] = 1021;
			$returnValue['message'] = $chongzhi_lang['model_message_illegal'];
			return $returnValue;
		}
		
		include(dirname(__FILE__). '/ShenZhouCommon.php');
		include_once(dirname(__FILE__) . '/HttpClient.class.php');
		
		//组织支付请求数据
		//版本号 *
		$version = "3";
		
		//商户ID *
		$merId = SZF_P1_MERID;
		
		//支付金额(单位：分) *
		$payMoney = $getInfo['payMoney']*100;
		//$payMoney = 0.1*100;
		
		//订单号（格式：yyyyMMdd-merId-SN） * 推荐格式
		$orderId = chongzhiModel::getszfOrdNo($merId);
		
		//服务器返回地址
		if(SYPT == 1) {
			if($_SC['fwqdm'] == 'sy01') {
				$returnUrl				= SZF_CALLBACK_URL1;
			} else if($_SC['fwqdm'] == 'sy02') {
				$returnUrl				= SZF_CALLBACK_URL2;
			} else if($_SC['fwqdm'] == 'sy03') {
				$returnUrl				= SZF_CALLBACK_URL3;
			} else if($_SC['fwqdm'] == 'sy04') {
				$returnUrl				= SZF_CALLBACK_URL4;
			} else if($_SC['fwqdm'] == 'sy05') {
				$returnUrl				= SZF_CALLBACK_URL5;
			} else if($_SC['fwqdm'] == 'sy06') {
				$returnUrl				= SZF_CALLBACK_URL6;
			}
		} else {
			$returnUrl = SZF_CALLBACK_URL;
		}
				
		//$common->insertLog("{$getInfo['cardMoney']}, {$getInfo['cardId']}, {$getInfo['cardPW']}, " . SZF_DES);exit;
		// 充值卡加密信息
		$cardInfo	= GetDesCardInfo($getInfo['cardMoney'], $getInfo['cardId'], $getInfo['cardPW'], SZF_DES);
		// 保存到DB数据库中的信息
		$cardInfo_EX= encrypt($getInfo['cardMoney']."@".$getInfo['cardId']."@".$getInfo['cardPW'], SZF_DES);
		
		//商户的用户姓名
		$merUserName	= " ";
		
		//商户的用户邮箱
		$merUserMail	= " ";
		
		$topid = _get('dfid');	
		if($topid == null) {
			//商户私有数据
			$privateField	= $getInfo['playersid'] . '|' .SYPT;
		} else {
			//给对方充值
			$privateField	= $topid . '|' .SYPT . '|' . $getInfo['playersid'];
			
			// 对双方发探报
			/*$toplayer = $G_PlayerMgr->GetPlayer($topid);
			$topalyer_info = $toplayer->baseinfo_;
			
			$player = $G_PlayerMgr->GetPlayer($getInfo['playersid']);
			$player_info = $player->baseinfo_;
			
			$give_ingot = ($payMoney/100)*10;			
			
			$other_recharge = array();
			$other_recharge['playersid'] = $getInfo['playersid'];
			$other_recharge['toplayersid'] = $getInfo['playersid'];
			$other_recharge['message'] = array('xjnr'=>"你已经为你的好友 {$topalyer_info['nickname']}（ID：{$topid}）充值{$give_ingot}元宝，请稍后重新登录游戏，在充值记录界面查看充值结果");
			$other_recharge['genre'] = 32;
			$other_recharge['request'] ='';
			$other_recharge['type'] = 1;
			$other_recharge['uc'] = '0';
			$other_recharge['is_passive'] = 0;
			$other_recharge['interaction'] = 0;
			$other_recharge['tradeid'] = 0;							
			lettersModel::addMessage($other_recharge);
			
			$other_recharge = array();
			$other_recharge['playersid'] = $getInfo['playersid'];
			$other_recharge['toplayersid'] = $topid;
			$other_recharge['message'] = array('xjnr'=>"你的好友 {$player_info['nickname']}（ID：{$playersid}）已经为你充值元宝，请稍后重新登录游戏查看充值结果！");
			$other_recharge['genre'] = 32;
			$other_recharge['request'] ='';
			$other_recharge['type'] = 1;
			$other_recharge['uc'] = '0';
			$other_recharge['is_passive'] = 0;
			$other_recharge['interaction'] = 0;
			$other_recharge['tradeid'] = 0;							
			lettersModel::addMessage($other_recharge);*/
		}
		
		//数据校验方式
		$verifyType	= 1;
		
		//充值卡类型		数字 0：移动；1：联通；2：电信
		$cardTypeCombine	= $getInfo['cardType'];
		
		//MD5 校验串
		$string = $version . $merId . $payMoney . $orderId . $returnUrl . $cardInfo . $privateField . $verifyType .
		SZF_PRIVATEKEY;
		$md5String	= md5($string);
		
		// URL转UTF-8
		$returnUrl	= urlencode($returnUrl);
		$cardInfo	= urlencode($cardInfo);
		
		//证书签名(暂时不用传递)
		$signString	=  "";
		
		$sendData = 'version=' . $version . '&merId=' . $merId .  '&payMoney=' . $payMoney . '&orderId=' . $orderId .
		'&returnUrl=' . $returnUrl . '&cardInfo=' . $cardInfo . '&merUserName=' . $merUserName .
		'&merUserMail=' . $merUserMail . '&privateField=' . $privateField . '&verifyType=' . $verifyType .
		'&cardTypeCombine=' . $cardTypeCombine . '&md5String=' . $md5String;
		
		$insertInfo = array(
				'playersid'=>$getInfo['userid'],
				'version'=>$version,
				'payMoney'=>$payMoney,
				'orderId'=>$orderId,
				'cardInfo'=>$cardInfo_EX,
				'verifyType'=>$verifyType,
				'cardTypeCombine'=>$cardTypeCombine,
				'privateField'=>$privateField,
				'hmac'=>$md5String,
				'verify'=>0,
				'errcode'=>"",
				'ordDate'=>time(),
				'toplayersid'=>$topid
		);
		$common->inserttable('shenzhoupay_record', $insertInfo);
				
		$result	= HttpClient::quickPost(SZF_REQURL_SNDAPRO, $sendData);;
		//$data = http_parse_message($result);
		//$common->insertLog(json_encode($data));
		if($result == 200) {
			$db->query("update ol_shenzhoupay_record set verify = '0', errcode = '200',
			ordDate = '". time() ."' where orderId = '" . $orderId . "';");
			$returnValue['status'] = 0;
		} else {
			$db->query("update ol_shenzhoupay_record set verify = '2',
			errcode = '". $result ."', ordDate = '". time() ."' where orderId = '" .
			$orderId . "';");
			$returnValue['status'] = 998;
			$returnValue['message'] = getFailReason($result);
		}
		return $returnValue;
	}
	
	
	// 神州付充值记录
	public static function dyszfczjl($playersid, $page) {
		global $common,$db,$chongzhi_lang, $G_PlayerMgr;

		if (empty($playersid) || empty($page)) {
			$returnValue['status'] = 3;
			$returnValue['message'] = $chongzhi_lang['model_message_illegal'];
			return $returnValue;
		}
	
		if(empty($page)) {
			$page = 1;
		}
	
		$sql = "SELECT count(*) as num FROM ol_shenzhoupay_record where playersid = " . $playersid . ";";
		$result = $db->fetch_array($db->query($sql));
	
		$zs = $result['num'];
		$pageRowNum = SHOW_PAY_MESSAGE_NUM;
	
		$page_z = 0;
		if($zs > 0) {
			$page_z = ceil($zs / $pageRowNum);
			if($page > $page_z) {
				$page = $page_z;
			}
		}else{
			$page = 1;
		}
		$_start = ($page-1) * $pageRowNum;
		$_end = 0;
		if($_start + $pageRowNum < $zs) {
			$_end = $pageRowNum;
		}else{
			$_end = $zs;
		}
	
		$sql = "SELECT cardTypeCombine as mc, round(payMoney/100) as je, ordDate as sj, verify as zt, errcode as reason, toplayersid as topid FROM ol_shenzhoupay_record";
		$sql .= " WHERE playersid = {$playersid} ORDER BY ordDate DESC LIMIT {$_start},{$_end};";
		$pay_res = $db->query($sql);
	
		while($pay_result[] = $db->fetch_array($pay_res, MYSQL_ASSOC));
		array_pop($pay_result);
		//print_r($pay_result);
	
		date_default_timezone_set('PRC');
		$czmc = getCzMcInfo();
		$jl = array();
		foreach ($pay_result as $key=>$value) {
			$value['sj'] = date('Y-m-d H:i:s', $value['sj']);
			if($value['mc'] == 0) $value['mc'] = 'SZX';
			else if($value['mc'] == 1) $value['mc'] = 'UNICOM';
			else $value['mc'] = 'TELECOM';
			$value['mc'] = $czmc[$value['mc']];
			$sbyy = $value['zt'] == 2 ? getFailReason($value['reason']) : '';
			if($value['zt'] == 0 || $value['zt'] == 2){
				$value['je'] = $chongzhi_lang['model_message_noget'];
			}
			if($value['topid'] != 0) {
				$player = $G_PlayerMgr->GetPlayer($value['topid']);
				if(!$player)  return array('status'=>3, 'message'=>$sys_lang[7]);
				$other_info = $player->baseinfo_;
				$jl[] = array(
					'mc'=>$value['mc'],
					'je'=>$value['je'],
					'sj'=>$value['sj'],
					'zt'=>$value['zt'],
					'message'=>$sbyy,
					'df'=>array('id'=>$value['topid'], 'nc'=>$other_info['nickname'])
				);
			} else {
				$jl[] = array(
					'mc'=>$value['mc'],
					'je'=>$value['je'],
					'sj'=>$value['sj'],
					'zt'=>$value['zt'],
					'message'=>$sbyy					
				);
			}
		}
	
		$jl = array_values($jl);
	
		$returnValue['status'] = 0;
		$returnValue['zys'] = $page_z;
		$returnValue['page'] = $page;
		$returnValue['czjl'] = $jl;
	
		return $returnValue;
	}
	

	// 获取神州付订单编号
	public static function getszfOrdNo($merId) {	
		date_default_timezone_set('PRC');
		list($s1, $s2) = explode(' ', microtime());
		$millisecond = (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
		return "jofgame_szf_" . date('YmdHis') . $millisecond . mt_rand(1, 999999);
	}

	//生成订单号
	public static function build_order_no($id,$playersid,$sinauid,$serverid) {
		global $_WB,$db,$common,$mc;
		if ($serverid == 'sina001') {
			$code = $common->inserttable('wborderid',array('ortime'=>$id,'playersid'=>$playersid,'sinauid'=>$sinauid,'serverid'=>$serverid));
			return $code;
		} else {
			$appSecret = '8NedWUrdo3xQm43lfdg23445536546456ocucKqwV';
			$sign = md5($id.$playersid.$sinauid.$serverid.$appSecret);
			$endpoint = "http://h5.jofgame.com/ucpay/wborder.php";
			$ch = curl_init($endpoint);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "id=$id&playersid=$playersid&sinauid=$sinauid&serverid=$serverid&sign=$sign");
			$response = curl_exec($ch);
			$errno    = curl_errno($ch);
			$errmsg   = curl_error($ch);
			curl_close($ch);
			if ($errno != 0) {
				return false;
			} else {
				return $response;
			}			
		}
	    //$pre = sprintf('%02d', $id / 14000000);        // 每1400万的前缀
	    /*$tempcode = sprintf('%09d', sin(($id % 14000000 + 1) / 10000000.0) * 123456789);    // 这里乘以 123456789 一是一看就知道是9位长度，二则是产生的数字比较乱便于隐蔽
	    $seq = '371482506';        // 这里定义 0-8 九个数字用于打乱得到的code
	    $code = '';
	    for ($i = 0; $i < 9; $i++) $code .= $tempcode[ $seq[$i] ];
	    //return $pre.$code;
		return $code;*/
	}
	
	//请求微币支付信息
	public static function hqwbxx($playersid, $session_key, $amount) {
		global $_WB,$db,$common,$mc,$_SC;
		$weiyouxi = new WeiyouxiClient( $_WB['source'] , $_WB['secret'] );
		//$weiyouxi->sessionKey = $session_key;
		$userId = $weiyouxi->getUserId();
		//调用API接口
		//$info = $weiyouxi->get( 'user/show' , array( 'uid' => $userId ));
		//echo '请求接口成功:';
		//var_dump( $info );
		$return_url = 'http://game.weibo.cn/startgame.php?appkey=938920110';		
		$order_id = $_WB['zfid'].chongzhiModel::build_order_no(time(),$playersid,$userId,$_SC['fwqdm']);
		if (empty($order_id)) {
			return array('status'=>30,'message'=>'生成订单号失败');
		}
		$order_uid = $userId;
		$desc = 'YUANBAO';
		$appkey = $_WB['source'];
		$version = '1.0';
		$sign = md5($order_id."|".$amount."|".$desc."|".$_WB['secret']);
		$tokenInfo = $weiyouxi->get( 'pay/wap/get_token.json' , array( 'order_id'=> $order_id , 'amount' => $amount , 'desc' => $desc , 'sign' => $sign ) );		
		//$token = '';
		if (isset($tokenInfo['token'])) {
			$token = $tokenInfo['token'];
			$registOrder = $weiyouxi->http( 'http://i.game.weibo.cn/registerOrder.php' , "order_id=$order_id&order_uid=$order_uid&desc=$desc&source=".$_WB['source']."&amount=$amount&token=$token");
			if ($registOrder['code'] == 0) {
				$db->query("INSERT INTO ".$common->tname('weibo_payinfo')." SET return_url = '$return_url',order_id = '$order_id',order_uid = '".$tokenInfo['order_uid']."',wbdesc = '$desc',appkey = '$appkey',amount = ".($amount / 100).",version = '$version',token = '$token',playersid = $playersid");				
				return array('status'=>0,'return_url'=>$return_url,'order_id'=>$order_id,'order_uid'=>$tokenInfo['order_uid'],'desc'=>$desc,'appkey'=>$appkey,'amount'=>$amount,'token'=>$token,'posturl'=>'http://new.weibo.cn/ipay/payment','sign'=>$sign);
			} else {
				return array('status'=>30,'message'=>'注册订单号失败');
			}
		} else {
			return array('status'=>30,'message'=>'token获取失败');
		}
	}		

	// 绑定手机号
	public static function bdsj($playersid, $phone){
		global $mc, $db, $common;

		$sql = "update ". $common->tname('player')." set `phone`='{$phone}'";
		$sql .= " where `playersid`='{$playersid}'";

		$db->query($sql);

		$mc->delete(MC.$playersid);
		return array('status'=>0);

	}
	//短信充值
	public static function dxcz($playersid,$prov,$city,$ms,$qid,$serviceid,$ext) {
		global $_SC,$db,$common;
		$key = '!@#$%^&YTRG12344354?~!!';
		$fwqdm = $_SC['fwqdm'];
		$sql_diff = "select * from ".$common->tname('dx_payinfo')." where playersid = $playersid order by intID DESC limit 1";
		$res_diff = $db->query($sql_diff);
		$rows_diff = $db->fetch_array($res_diff);
		if (!empty($rows_diff)) {
			if ((time() - $rows_diff['orderTime']) > 3600) {
				return array('status'=>30,'message'=>'两次充值间隔时间必须大于1小时！');
			}
		}		
		//http_post($host, $port, $path, $data)  http://1380808.com/interface/longyin/getmoinfos.php?prov=&city=&qid=&serviceid=&ms=&ext=
		$response = chongzhiModel::http_post("1380808.com/interface/longyin/getmoinfos.php?prov=$prov&city=$city&qid=$qid&serviceid=$serviceid&ms=$ms&ext=$ext",'','','');
		$info = json_decode($response,true);
		if ($info['status'] == 1) {
			$orderTime = time();
			$msg = $info['msg'];
			$ms = $info['ms'];			
			$orderId = substr($msg,-16,16);
			if ($fwqdm == 'zyy01' || $fwqdm == '61' || $fwqdm == 'hud') {
				$id = $common->inserttable('dx_visitinfo',array('orderId'=>$orderId,'msg'=>$msg,'ms'=>$ms,'playersid'=>$playersid,'fwqdm'=>$fwqdm,'orderTime'=>$orderTime));
				if ($id > 0) {
					$returnValue['status'] = 0;
					$returnValue['msg'] = $msg;
					$returnValue['ms'] = $ms;
					return $returnValue;
				} else {
					return array('status'=>30,'message'=>'系统错误！');
				}
			} else {
				$sign = md5("orderId=$orderId&msg=$msg&ms=$ms&playersid=$playersid&fwqdm=$fwqdm&orderTime=$orderTime".$key);
				$noticeInfo = chongzhiModel::http_post("sop1.jofgame.com/ucpay/dxjs.php?orderId=$orderId&msg=$msg&ms=$ms&playersid=$playersid&fwqdm=$fwqdm&orderTime=$orderTime&sign=$sign",'','','');
				if ($noticeInfo == 'ok') {
					$returnValue['status'] = 0;
					$returnValue['msg'] = $msg;
					$returnValue['ms'] = $ms;
					return $returnValue;					
				} else {
					return array('status'=>30,'message'=>'系统错误！');
				}
			}
		} else {
			$returnValue['status'] = 30;
			$errorcode = $info['errorcode'];
			if ($errorcode == 'PROV' || $errorcode == 'CITY') {
				$returnValue['message'] = '不支持该地区短信支付';
			} else {
				$returnValue['message'] = '支付失败！';
			}
			return $returnValue;
		}
	}	
}
