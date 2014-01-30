<?php
define('PATH', 'ws/');
define('S_ROOT', dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR);
$_REQUEST['client'] = 0;

include(S_ROOT . 'includes/class_mysql.php');
include(S_ROOT . 'includes/class_common.php');
include(S_ROOT . 'config.php');
include(S_ROOT . 'components'.DIRECTORY_SEPARATOR.'hero_tools'.DIRECTORY_SEPARATOR.'model.php');
include(S_ROOT . 'components'.DIRECTORY_SEPARATOR.'hero_tools'.DIRECTORY_SEPARATOR.'var.php');
include(S_ROOT . 'components'.DIRECTORY_SEPARATOR.'hero_role'.DIRECTORY_SEPARATOR.'model.php');
include(S_ROOT . 'components'.DIRECTORY_SEPARATOR.'hero_letters'.DIRECTORY_SEPARATOR.'model.php');
include(S_ROOT . 'components'.DIRECTORY_SEPARATOR.'hero_city'.DIRECTORY_SEPARATOR.'model.php');
include(S_ROOT . 'components'.DIRECTORY_SEPARATOR.'hero_city'.DIRECTORY_SEPARATOR.'model_general.php');
include_once(S_ROOT . 'components'.DIRECTORY_SEPARATOR.'hero_fight'.DIRECTORY_SEPARATOR.'model_fight.php');
include(S_ROOT . 'configs'.DIRECTORY_SEPARATOR.'ConfigLoader.php');
include(S_ROOT . 'configs'.DIRECTORY_SEPARATOR.LANG_FLAG.DIRECTORY_SEPARATOR.'G_achievements.php');

require(S_ROOT.'includes'.DIRECTORY_SEPARATOR.'class_memcacheAdapter.php');
require(S_ROOT.'model'.DIRECTORY_SEPARATOR.'PlayerMgr.php');

$db = new dbstuff;
$mc = new MemcacheAdapter_Memcached();
$G_PlayerMgr = new PlayerMgr($db,$mc);

$mc->addServer($MemcacheList[0], $Memport);

$common = new heroCommon;
$db->dbconnect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],$_SC['dbname'],'utf8');
$db->query("set names 'utf8'");

include(S_ROOT . 'includes'.DIRECTORY_SEPARATOR.'vip_control.php');

date_default_timezone_set('PRC');

class Rsa
{
        private $_privKey;

        private $_keyPath;

        public function __construct($path)
        {
                if(empty($path) || !is_dir($path)){
                        throw new Exception('Must set the keys save path');
                }
               
                $this->_keyPath = $path;
        }

        public function setupPrivKey()
        {
                if(is_resource($this->_privKey)){
                        return true;
                }
                $file = $this->_keyPath . DIRECTORY_SEPARATOR . 'priv.key';
                $prk = file_get_contents($file);
                $this->_privKey = openssl_pkey_get_private($prk);
                return true;
        }

        public function privEncrypt($data)
        {
                if(!is_string($data)){
                        return null;
                }
               
                $this->setupPrivKey();
               
                $r = openssl_private_encrypt($data, $encrypted, $this->_privKey);
                if($r){
                        return base64_encode($encrypted);
                }
                return null;
        }
 
        public function privDecrypt($encrypted)
        {
                if(!is_string($encrypted)){
                        return null;
                }
               
                $this->setupPrivKey();
               
                $encrypted = base64_decode($encrypted);

                $r = openssl_private_decrypt($encrypted, $decrypted, $this->_privKey);
                if($r){
                        return $decrypted;
                }
                return null;
        }
}

class mytools {
	public static function writelog($request,$return,$level=0,$userid=0) {
		global $common,$mc, $_LOG_INFO;
		$nowTime = time();
		$letters_trade['request_url'] = $request;
		$letters_trade['create_time'] = $nowTime;
		$letters_trade['level'] = $level;
		$letters_trade['userid'] = $userid;
		$letters_trade['result'] = $return;
		$log_path = $_LOG_INFO['path'] . $_LOG_INFO['prefix'] . date('Y_m_d_H',$nowTime).'_'.intval($nowTime/$_LOG_INFO['split_t']);
		$log_json_value = json_encode($letters_trade);
		$flag = false;
		for($i=1; $i<=5; $i++){
			$logHandle = fopen($log_path."_{$i}", 'a');
			if(flock($logHandle, LOCK_EX|LOCK_NB)){
				$w_long = fwrite($logHandle, $log_json_value."\n");
				if(0 < $w_long){
					$flag = true;
					fclose($logHandle);
					break;
				}
			}
			fclose($logHandle);
		}
		if(!$flag){
			$log['logValue'] = $log_json_value;
			$common->inserttable('bck_log', $log);
		} else{
			syslog(LOG_ALERT, 'game log write log need userid');
		}
	}
}

class mysoapclass {
	var $Authenticated;
	var $Username;
	var $Password;
	var $Ip;
	var $client;
	
	function __construct() {
		global $common;
		
		$hdr = file_get_contents("php://input");
		if (strpos($hdr,'<ns2:authenticate>')===false) {
		    $hdr = null;
		} else {
		    $hdr = explode('<username>',$hdr);			    
		    $hdr = str_replace('<password>', '', $hdr[1]);
		    $hdr = str_replace('</password>', '', $hdr);
		    $hdr = explode('</ns2:authenticate>', $hdr);
		    $hdr = explode('</username>', $hdr[0]);
		    $tmp = explode('<ip>', $hdr[1]);
		    $hdr[1] = $tmp[0];
		    $hdr[2] = str_replace('</ip>', '', $tmp[1]);
		    unset($tmp);//$common::insertLog(json_encode($hdr));
		}
		
		// Decrypt
		$rsa = new Rsa(WS_KEY_PATH);
		$this->Username = $rsa->privDecrypt($hdr[0]);
		$this->Password = $rsa->privDecrypt($hdr[1]);
		$this->Ip = $rsa->privDecrypt($hdr[2]); 
				
		if(!strpos(WS_CLIENT, ',')) {
			$this->client = array(WS_CLIENT);
		} else {
			$this->client = explode(',', WS_CLIENT);
		}			
	}
	
	public function authenticate(){		
		if($this->Username == WS_USER_NAME && $this->Password == WS_PASS ) {
			$this->Authenticated = true;
		} else {
			$this->Authenticated = false;
		}		
	}
	
	public function addItemIntoBg($userid, $itemid, $item_num){
		global $mc,$common;
		
		$roleInfo['userid'] = $userid;
		roleModel::getRoleInfo($roleInfo);
		$playersid = $roleInfo['playersid'];
		
		$this->authenticate();
		if($this->Authenticated){
			$ret = toolsModel::addPlayersItem($roleInfo, $itemid, $item_num);			
			if($ret['status'] == 0) {
				$mc->delete(MC.$playersid);   				  //清除角色信息
				$mc->delete(MC.$playersid.'_general');        //清除该角色将领信息
				$mc->delete(MC.$playersid.'_messageStatus');  //清除消息状态
				$mc->delete(MC.$roleInfo['ucid'].'_session'); //清除登录后记录sessionid信息
				$mc->delete(MC.'items_'.$playersid);          //清除道具信息
				$mc->delete(MC.$playersid.'_zllist');         //清除占领列表信息
				$mc->delete(MC.'stageInfo_'.$playersid);      //清除玩家闯关表信息
				session_destroy();
				return 0;
			} else {
				return 1001;
			}
		} else {
			return 3;			
		}
	}
	
	public function sendMsg($playersid, $message, $request, $type) {
		$this->authenticate();
		if($this->Authenticated){	
			$pidArr = json_decode($playersid, true);
			set_time_limit(0);
			for($i = 0; $i < count($pidArr); $i++) {
				$json = array();
				$json['playersid'] = $pidArr[$i];
				$json['toplayersid'] = $pidArr[$i];
				$json['message'] = array('xjnr'=>$message);
				$json['genre'] = $type;
				$json['request'] = $request;
				$json['type'] = 1;
				$json['uc'] = '0';
				$json['is_passive'] = 0;
				$json['interaction'] = 0;
				$json['tradeid'] = 0;
				//$json = json_encode($json);
				$ret = lettersModel::addMessage($json);
				
				if($i % 20 == 0) usleep(200);
			}
			return 0;
			/* if($ret > 0) {
				return 0;
			} else {
				return 1001;
			} */
		} else {
			return 3;
		}
	}
	
	public function sendNotice($content, $startDate, $endDate){
		global $mc,$common;
	
		$this->authenticate();
		if($this->Authenticated){
			$updateNotice['notice'] = $content;
			$updateNotice['start_date'] = $startDate;
			$updateNotice['end_date'] = $endDate;
			$where['id'] = 1;
			$common->updatetable('notice', $updateNotice, $where);
			// 删除当前弹出公告缓存
			$mc->delete(MC. 'serverNotice');
			$mc->set(MC.'NoticeExpFlag', 1, 0, 0);
			return 0;
		} else {
			//return "$this->Username  $this->Password $this->Ip"; 
			return 3;
		}
	}
	
	public function sendShutdownNotice($content){
		global $mc,$common;
	
		$this->authenticate();
		if($this->Authenticated){
			$updateNotice['notice'] = $content;
			$where['id'] = 2;
			$common->updatetable('notice', $updateNotice, $where);
			$mc->delete(MC.'shutdownNotice');
			return 0;
		} else {			
			//return  $this->Username . " "  . $this->Password. " " .$this->Ip;
			return 3;
		}
	}
	
	public function sendActivateFaildMsg($content){
		global $mc,$common;
	
		$this->authenticate();
		if($this->Authenticated){
			$updateNotice['notice'] = $content;
			$where['id'] = 3;
			$common->updatetable('notice', $updateNotice, $where);
			$mc->delete(MC.'activateFaild');
			return 0;
		} else {
			//return  $this->Username . " "  . $this->Password. " " .$this->Ip;
			return 3;
		}
	}
	
	public function addIngot($rmb, $ingot, $order, $payway, $userid, $playersid = null){
		global $mc,$common;
		
		if($playersid == null) {
			$roleInfo['userid'] = $userid;
			roleModel::getRoleInfo($roleInfo);
			$playersid = $roleInfo['playersid'];
		}
		
		if(intval($ingot) < 0 || $rmb < 0) {
			return 3;
		}
		
		$this->authenticate();
		if($this->Authenticated){
			$roleInfo = array('playersid'=>$playersid);
			roleModel::getRoleInfo($roleInfo);
			
			$currYB = $roleInfo['ingot'];
			$updateRole['ingot'] = $currYB + $ingot;
				
			$whereRole['playersid'] = $playersid;
			$common->updatetable('player', $updateRole, $whereRole);
			$common->updateMemCache(MC.$playersid, $updateRole);
			
			vipChongzhi($playersid, $rmb, $ingot, $order);
			
			$orderDate = time();
			mytools::writelog('app.php?task=chongzhi&type=ucweb&option=pay',
			json_encode(array('orderId'=>$order,'ucid'=>'','payWay'=>$payway,'amount'=>$rmb,'orderStatus'=>0,'failedDesc'=>'','createTime'=>$orderDate,'status'=>0)),
			$roleInfo['player_level'],$roleInfo['userid']);
			
			return 0;
		} else {
			//return  $this->Username . " "  . $this->Password. " " .$this->Ip;
			return 3;
		}
	}

	//获取在线人数
	public function getOnlineCount() {
		global $db,$common;
		$this->authenticate();
		if($this->Authenticated){
			$checkTime = time() - 300;
			$db->query("DELETE FROM ".$common->tname('online')." WHERE updateTime <= '$checkTime'");
			$sql = "SELECT count(*) as sl FROM ".$common->tname('online');
			$result = $db->query($sql);
			$rows = $db->fetch_array($result);
			return $rows['sl'];
		} else {		
			return 3;
		}
	}
	
	
	public function genActivateCode($prefix, $account_count_limit) {
		global $mc, $common;
		
		$this->authenticate();
		if($this->Authenticated){
			mysql_connect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],'utf8');
			mysql_select_db($_SC['dbname']);
			mysql_query("set names 'utf8'");
			
			mysql_query('TRUNCATE ol_activatecode');
			
			for($i = 0; $i < $account_count_limit; $i++) {
				$activateCode = $prefix . rand(100000, 999999);
				mysql_query("insert into ol_activatecode values(null, '$activateCode')");
				$err = mysql_error();
				while($err != '') {
					if($i == $account_count_limit - 1) break;
					$activateCode = $prefix . rand(100000, 999999);
					mysql_query("insert into ol_activatecode values(null, '$activateCode')");
					$err = mysql_error();
				}
			}
			
			$mc->delete(MC.'activateCode');
			
			return 0;
		} else {		
			return 3;
		}

	}
	//获取UC订单详细信息
	public function synPayInfoUC($pid,$startTime,$endTime,$pos,$pcount,$ly = 'uc') {
		global $mc, $common, $db, $_WB;
		$this->authenticate();
		if($this->Authenticated){
			if (!empty($pid)) {
				$itemsql = "playersid = '$pid'";
			} else {
				$itemsql = '';
			}
			if (empty($itemsql)) {
				return 4;         //玩家不存在
			}
			$psql = "SELECT userid,nickname,ucid FROM ".$common->tname('player')." WHERE $itemsql  LIMIT 1";
			$pres = $db->query($psql);
			$prows = $db->fetch_array($pres);
			if (empty($prows)) {
				return 4; //玩家不存在
			}

			$ucid = $prows['ucid'];
			$userid = $prows['userid'];
			if (empty($startTime) 
				|| empty($endTime)
				|| $startTime > $endTime) {
				return 5;
			} else {
				if($ly == 'auto'){// 通过vip_money_log表得到充值数据
					$startTime = date('Y-m-d H:i:s', $startTime);
					$endTime   = date('Y-m-d H:i:s', $endTime);
					$paySql = "select l.playersid playersid, p.ucid ucid, l.insertTime createTime, l.orderid orderId";
					$paySql .= " ,l.money amount, u.qd qd";
					$paySql .= " from ol_vip_money_log l, ol_player p, ol_user u";
					$paySql .= " where l.playersid=p.playersid and p.userid=u.userid";
					$paySql .= " and insertTime >'{$startTime}' and insertTime<'{$endTime}'";
					if(!empty($pid)){
						$paySql .= " and l.playersid='{$pid}'";
					}
					$paySql .= " limit 300";

					$result = $db->query($paySql);
					$orderInfo = array();
					while($row = $db->fetch_array($result)){
						$orderInfo[] = array('orderid'=>$row['orderId'],
											 'ucid' =>$row['ucid'],
											 'playersid'=>$row['playersid'],
											 'payTime'=>strtotime($row['createTime']),
											 'amount'=>$row['amount'],
											 'payWay'=>'',
											 'orderS'=>'S',
											 'reason'=>'',
											 'qd'=>$row['qd']);
					}

					if (empty($orderInfo)) {
						return 5;
					}
					$returnValue['ucid'] = '';
					$returnValue['count'] = 0;
					$returnValue['orderlist'] = $orderInfo;
					return json_encode($returnValue);
				}elseif ($ly == 'uc') {
					if (!empty($startTime)) {
						$orderItme = "&& createTime >= '$startTime' && createTime <= '$endTime'";
					} else {
						$orderItme = '';
					}
					$sqlO = "SELECT * FROM ".$common->tname('uc_payinfo')." WHERE ucid = '$ucid' $orderItme";
					$resO = $db->query($sqlO);
					$rowsnum = $db->num_rows($resO);
					if ($rowsnum == 0) {
						return 5;
					} else {
						$sqlOrder = "SELECT * FROM ".$common->tname('uc_payinfo')." WHERE ucid = '$ucid' $orderItme ORDER BY createTime DESC LIMIT $pos,$pcount";
						$resOrder = $db->query($sqlOrder);
						$orderInfo = array(); 
						while ($rowsOrder = $db->fetch_array($resOrder)) {
							$orderInfo[] = array('orderid'=>$rowsOrder['orderId'],'payTime'=>$rowsOrder['createTime'],'amount'=>$rowsOrder['amount'],'payWay'=>$rowsOrder['payWay'],'orderS'=>$rowsOrder['orderStatus'],'reason'=>$rowsOrder['failedDesc']);
						}
						if (empty($orderInfo)) {
							return 5;
						}
						$returnValue['ucid'] = $ucid;
						$returnValue['count'] = $rowsnum;
						$returnValue['orderlist'] = $orderInfo;
						return json_encode($returnValue);
					}
				} 
				// uc乐园 + 九游端游的特殊服务器，妈的谁想这样搞的
				elseif($ly == 'ucEden'){
					$where = empty($startTime)?"":"e.createTime >= '$startTime' and e.createTime <= '$endTime'";
					$where = empty($where)?" where p.playersid='$pid' ":" where p.playersid='$pid' and ".$where;
						
					$countSql = "select count(1) `orderCount` from ol_v_uc_eden_pay e left join ol_player p on p.ucid=e.ucid ";
					$countSql .= $where;
					$resCount = $db->query($countSql);
					$countR = $db->fetch_array($resCount);
					$rowsnum = $countR['orderCount'];
						
					$sltSql = "select * from ol_v_uc_eden_pay e left join ol_player p on p.ucid=e.ucid ";
					$sltSql .= $where;
					$sltSql .= " order by e.createTime desc limit $pos, $pcount";
					$result = $db->query($sltSql);
					$orderInfo = array();
					while($orderRow = $db->fetch_array($result)){
						$orderStatus = $orderRow['status']==0?'P':($orderRow['status']==1?'S':'F');
						$orderInfo[] = array('orderid'=>$orderRow['order_id'],
											 'payTime'=>$orderRow['createTime'],
											 'amount'=>$orderRow['amount'],
											 'payWay'=>$orderRow['payWay'],
											 'orderS'=>$orderStatus,
											 'reason'=>$orderRow['errorInfo']);
					}
					if (empty($orderInfo)) {
						return 5;
					}
					$returnValue['ucid'] = $ucid;
					$returnValue['count'] = $rowsnum;
					$returnValue['orderlist'] = $orderInfo;
					return json_encode($returnValue);
						
				}
				// 直接易宝，支付宝，财付通支付
				elseif($ly == 'yeebao'){
					$where = empty($startTime)?"":"ordDate >= '$startTime' and ordDate <= '$endTime'";
					$where = empty($where)?" where playersid='$pid' ":" where playersid='$pid' and ".$where;
					$resCount = $db->query("select count(1) `orderCount` from ".$common->tname('yeepay_ord').$where);
					$countR = $db->fetch_array($resCount);
					$rowsnum = $countR['orderCount'];
						
					$sltSql = "select * from ";
					// yeebao
					$sltSql .= "(select verify, orderNo, ordDate, orderAmt, frpId, cardNo, cardPwd, errcode from ";
					$sltSql .= $common->tname('yeepay_ord');
					$sltSql .= $where;

					// alipay
					$where = empty($startTime)?"":" UNIX_TIMESTAMP(gmt_create) >= '$startTime' and  UNIX_TIMESTAMP(gmt_create) <= '$endTime'";
					$where = empty($where)?" where playersid='$pid' ":" where playersid='$pid' and ".$where;
					$sltSql .= " union all \n";
					$sltSql .= " select verify, ord_no orderNo, UNIX_TIMESTAMP(gmt_create) ordDate,";
					$sltSql .= " case when ISNULL(total_fee) THEN ord_amt ELSE total_fee END orderAmt,";
					$sltSql .= ' "alipay" frpId, "" cardNo, "" cardPwd, 0 errcode';
					$sltSql .= " from ".$common->tname("alipay_ord");
					$sltSql .= $where;

					// tenpay
					$where = empty($startTime)?"":" notify_time >= '$startTime' and notify_time <= '$endTime'";
					$where = empty($where)?" where playersid='$pid' ":" where playersid='$pid' and ".$where;
					$sltSql .= " union all \n";
					$sltSql .= " select verify, ord_no orderNo, notify_time ordDate,";
					$sltSql .= " case when ISNULL(total_fee) THEN ord_amt ELSE total_fee END orderAmt,";
					$sltSql .= ' "tenpay" frpId, "" cardNo, "" cardPwd, 0 errcode';
					$sltSql .= " from ".$common->tname("tenpay_ord");
					$sltSql .= $where;

					$sltSql .= ") a order by ordDate desc limit $pos, $pcount";
					$result = $db->query($sltSql);
					$orderInfo = array();
					while($orderRow = $db->fetch_array($result)){
						$orderStatus = $orderRow['verify']==0?'P':($orderRow['verify']==1?'S':'F');
						$orderInfo[] = array('orderid'=>$orderRow['orderNo'],
											 'payTime'=>$orderRow['ordDate'],
											 'amount'=>$orderRow['orderAmt'],
											 'payWay'=>$orderRow['frpId'],
											 'cardNo'=>$orderRow['cardNo'],
											 'cardPwd'=>$orderRow['cardPwd'],
											 'orderS'=>$orderStatus,
											 'reason'=>$orderRow['errcode']);
					}
					if (empty($orderInfo)) {
						return 5;
					}
					$returnValue['ucid'] = $ucid;
					$returnValue['count'] = $rowsnum;
					$returnValue['orderlist'] = $orderInfo;
					return json_encode($returnValue);
				}
				elseif ($ly == 'apple') {
					if (!empty($startTime)) {
						$startTime = $startTime * 1000;
						$endTime = $endTime * 1000 + 999;
						$orderItme = "&& purchase_date_ms >= '$startTime' && purchase_date_ms <= '$endTime'";
					} else {
						$orderItme = '';
					}
					$sqlO = "SELECT * FROM ".$common->tname('app_payinfo')." WHERE uid = '$userid' $orderItme";
					$resO = $db->query($sqlO);
					$rowsnum = $db->num_rows($resO);		
					if ($rowsnum == 0) {
						return 5;
					} else {
						$sqlOrder = "SELECT * FROM ".$common->tname('app_payinfo')." WHERE uid = '$userid' $orderItme ORDER BY createTime DESC LIMIT $pos,$pcount";
						$resOrder = $db->query($sqlOrder);
						$orderInfo = array();
						while ($rowsOrder = $db->fetch_array($resOrder)) {
							$orderInfo[] = array('orderid'=>$rowsOrder['transaction_id'],'payTime'=>floor($rowsOrder['purchase_date_ms'] / 1000),'amount'=>$rowsOrder['price'],'payWay'=>'','orderS'=>'S','reason'=>'');
						}
						if (empty($orderInfo)) {
							return 5;
						}
						$returnValue['ucid'] = $ucid;
						$returnValue['count'] = $rowsnum;
						$returnValue['orderlist'] = $orderInfo;
						return json_encode($returnValue);
					}	
				}
				elseif($ly == 'weifen'){
					include_once(S_ROOT.'includes'.DIRECTORY_SEPARATOR.'sinav.php');
					//					return S_ROOT.'includes'.DIRECTORY_SEPARATOR.'sinav.php';
					$weiyouxi = new WeiyouxiClient( $_WB['source'] , $_WB['secret'] );

					$countSql = "select count(1) `orderCount` from ".$common->tname('wborderid');
					$countSql .= " where playersid='$pid' and ortime>='$startTime' and ortime <= '$endTime'";
					$countSql .= " LIMIT $pos, $pcount";
					$result = $db->query($countSql);
					$countR = $db->fetch_array($result);
					$rowsnum = $countR['orderCount'];
						
					$sql = "select * from ".$common->tname('wborderid');
					$sql .= " where playersid='$pid' and ortime>='$startTime' and ortime <= '$endTime'";
					$sql .= " LIMIT $pos, $pcount";

					$weifenOrder = $db->query($sql);
					$orderInfo = array();
					$orderIds  = array();
					while($row = $db->fetch_array($weifenOrder)){
						$order_id = $_WB['zfid'].$row['intID'];
						$orderIds[] = $order_id;
						$ucid = $row['sinauid'];
						$orderInfo[$order_id] = array('orderid'=>$order_id,
													  'payTime'=>$row['ortime'],
													  'amount'=>0,
													  'payWay'=>'weifen',
													  'cardNo'=>'',
													  'cardPwd'=>'',
													  'orderS'=>'',
													  'reason'=>0);
					}

					if(!empty($orderIds)){
						$orderListStr = implode(',', $orderIds);
						$resultSql = "select * from ".$common->tname('weibo_payinfo');
						$resultSql .= " where order_id in ({$orderListStr})";
						$payResult = $db->query($resultSql);
						while($row = $db->fetch_array($payResult)){
							$orderInfo[$row['order_id']] = array('orderid'=>$row['order_id'],
																 'payTime'=>$orderInfo[$row['order_id']]['payTime'],
																 'amount'=>$row['amount'],
																 'payWay'=>'weifen',
																 'cardNo'=>'',
																 'cardPwd'=>'',
																 'orderS'=>$row['pay_status']==1?'S':'F',
																 'reason'=>$row['errorCode']);
						}
					}

					foreach($orderInfo as $order_id=>$order){
						$sign = md5($order_id.'|'.$_WB['secret']);
						if($order['orderS'] == ''){
							try{
								$orderStatus = $weiyouxi->get('pay/order_status.json',
															  array('ordrid'=>$order_id,
																	'user_id'=>$row['sinauid'],
																	'app_id'=>$_WB['source'],
																	'sign'=>$sign));
								if(isset($orderStatus['order_status'])){
									$orderInfo[$order_id]['amount'] = $orderStatus['amount'];
									$orderInfo[$order_id]['orderS'] = $orderStatus['order_status']==1?'S':'F';
								}else{
									$orderInfo[$order_id]['orderS'] = 'F';
									$orderInfo[$order_id]['reason'] = $orderStatus['error'];
								}
							}catch(Exception $ex){
								$orderInfo[$order_id]['orderS'] = 'F';
								$orderInfo[$order_id]['reason'] = 'query time out';
							}
						}
					}
					$returnValue['ucid'] = $ucid;
					$returnValue['count'] = $rowsnum;
					$returnValue['orderlist'] = array_values($orderInfo);
					return json_encode($returnValue);
				}
			}
		} else {
			return 3;
		}		
	}
	
	//获取APPLE订单详细信息
	public function synPayInfoApple($pid,$startTime,$endTime,$pos,$pcount) {
		global $mc, $common, $db;
		$this->authenticate();
		$startTime = $startTime * 1000;
		$endTime = $endTime * 1000 + 999;
		if($this->Authenticated){
			if (!empty($pid)) {
				$itemsql = "playersid = '$pid'";
			} else {
				$itemsql = '';
			}
			if (empty($itemsql)) {
				return 4;         //玩家不存在
			}
			$psql = "SELECT userid,nickname,ucid FROM ".$common->tname('player')." WHERE $itemsql  LIMIT 1";
			$pres = $db->query($psql);
			$prows = $db->fetch_array($pres);
			if (empty($prows)) {
				return 4; //玩家不存在
			} else {
				$userid = $prows['userid'];
				if ((empty($startTime) && !empty($endTime)) || (!empty($startTime) && empty($endTime)) || $startTime > $endTime) {
					return 5;
				} else {
					if (!empty($startTime)) {
						$orderItme = "&& purchase_date_ms >= '$startTime' && purchase_date_ms <= '$endTime'";
					} else {
						$orderItme = '';
					}
					$sqlO = "SELECT * FROM ".$common->tname('app_payinfo')." WHERE uid = '$userid' $orderItme";
					$resO = $db->query($sqlO);
					$rowsnum = $db->num_rows($resO);
					if ($rowsnum == 0) {
						return 5;
					} else {
						$sqlOrder = "SELECT * FROM ".$common->tname('app_payinfo')." WHERE uid = '$userid' $orderItme ORDER BY createTime DESC LIMIT $pos,$pcount";
						$resOrder = $db->query($sqlOrder);
						$orderInfo = array();
						while ($rowsOrder = $db->fetch_array($resOrder)) {
							$orderInfo[] = array('orderid'=>$rowsOrder['original_transaction_id'],'payTime'=>date('Y-m-d H:i:s',floor($rowsOrder['purchase_date_ms'] / 1000)),'amount'=>$rowsOrder['price'],'payWay'=>1,'orderS'=>'S','reason'=>'');
						}
						if (empty($orderInfo)) {
							return 5;
						}
						$returnValue['userid'] = $userid;
						$returnValue['count'] = $rowsnum;
						$returnValue['orderlist'] = $orderInfo;
						return json_encode($returnValue);
					}
				}
			}
		} else {
			return 3;
		}		
	}	
	//修改及添加苹果商城内产品信息
	/* $method       1、添加新产品 2、修改产品信息
	 * $productID    产品在app内的ID
	 * $mc           产品名
	 * $iid          产品图标地址
	 * $jg           产品价格
	 * $publish      产品是否发布 1 是 0 否
	 * $newProductID 新产品ID
	 * */
	public function modifyAppPro($method,$productID,$cpmc,$iid,$jg,$publish,$newProductID,$yb,$apple_ID,$sfgq) {
		global $mc,$common,$db;
		$this->authenticate();
		if($this->Authenticated){
			$method = trim($method);
			$productID = trim($productID);
			$cpmc = trim($cpmc);
			$iid = trim($iid);
			$jg = trim($jg);
			$publish = trim($publish);
			$newProductID = trim($newProductID);
			$yb = trim($yb);
			$apple_ID = trim($apple_ID);
			$sfgq = trim($sfgq);
			if ($method == 1) {
				$sqlNum = "SELECT * FROM ".$common->tname('app_productInfo')." WHERE productID = '$newProductID'";
				$resNum = $db->query($sqlNum);
				$num = $db->num_rows($resNum);
				if ($num == 0) {
					$db->query("INSERT INTO ".$common->tname('app_productInfo')."(productID,mc,iid,jg,publish,yb,apple_ID,sfgq) VALUES ('$newProductID','$cpmc','$iid','$jg','$publish','$yb','$apple_ID','$sfgq')");
				} else {
					return 4;  //产品ID已存在
				}
			} else {
			    if ($newProductID != $productID) {
					$sqlNum = "SELECT * FROM ".$common->tname('app_productInfo')." WHERE productID = '$newProductID'";
					$resNum = $db->query($sqlNum);
					$num = $db->num_rows($resNum);
					if ($num > 0) {
						return 4;  //产品ID已存在
					}
				}
				$db->query("UPDATE ".$common->tname('app_productInfo')." SET productID = '$newProductID',mc = '$cpmc',iid = '$iid',jg='$jg',publish = '$publish',yb = '$yb',apple_ID = '$apple_ID',sfgq = '$sfgq' WHERE productID = '$productID'");				
			}
			$mc->delete(MC.'app_product_1');
			$mc->delete(MC.'app_product_2');
		} else {
			return 3;
		}
	}
	
	//获取商品在苹果商城内产品信息
	public function getAppInfo() {
		global $mc,$common,$db;
		$this->authenticate();
		if($this->Authenticated){				
			$productInfo = array();
			$sql = "SELECT * FROM ".$common->tname('app_productInfo')." ORDER BY yb ASC";
			$result = $db->query($sql);
			while ($rows = $db->fetch_array($result)) {
				$productInfo[] = array('productid'=>$rows['productID'],'mc'=>$rows['mc'],'iid'=>$rows['iid'],'jg'=>$rows['jg'],'publish'=>$rows['publish'],'yb'=>$rows['yb'],'appleid'=>$rows['apple_ID'],'sfgq'=>$rows['sfgq']);
			}			
			if (!empty($productInfo)) {
				return json_encode($productInfo);
			} else {
				return 4;  //产品信息不存在
			}			
		} else {
			return 3;
		}	
	}
	
	// 获取背包列表
	public function getBgList($playersid){
		global $mc, $common, $db;
	
		$this->authenticate();
		if($this->Authenticated){
			// 获取背包列表
			$playerBag = toolsModel::getMyItemInfo($playersid);
			return json_encode($playerBag);			
		} else {
			return 3;
		}
	}
	
	// 修改背包列表
	public function setBgList($playersid, $bgID, $EquipCount){
		global $mc, $common, $db;
	
		$this->authenticate();
		if($this->Authenticated){
			// 获取背包列表
			$playerBag = toolsModel::getMyItemInfo($playersid);
			mysql_query("update ol_player_items set EquipCount='{$EquipCount}'  where id = '{$bgID}' and playersid={$playersid} limit 1");
									
			// 更新mc
			$playerBag[$bgID]['EquipCount'] = $EquipCount;
			$mc->set(MC.'items_'.$playersid, $playerBag, 0, 3600);
			
			return 0;
		} else {
			return 3;
		}
	}
	
	// 删除背包中道具
	public function delItemFromBg($playersid, $bgId){
		global $mc, $common, $db;
	
		$this->authenticate();
		if($this->Authenticated){
			// 获取背包列表
			$playerBag = toolsModel::getMyItemInfo($playersid);
			
			// 更新玩家当前扩包道具数量
			$whereItem['playersid'] = $playersid;
			$whereItem['ID'] = $bgId;
			$common->deletetable('player_items', $whereItem);

			unset($playerBag[$bgId]);
			// 更新mc
			$mc->set(MC.'items_'.$playersid, $playerBag, 0, 3600);
				
			return 0;
		} else {
			return 3;
		}
	}
	
	// 获取返还列表
	public function getRestitutionList(){
		global $mc, $common, $db;
	
		$this->authenticate();
		if($this->Authenticated){
			// 获取服务器列表
			$restitutionList = 'empty';
			if (!($restitutionList = $mc->get(MC.'restitution'))) {
				mysql_connect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw'],'utf8');
				mysql_select_db($_SC['dbname']);
				mysql_query("set names 'utf8'");
				
				$res = mysql_query('select * from ol_restitution');
				if(mysql_num_rows($res) > 0) {
					while($restitutionList[] = mysql_fetch_array($res, MYSQL_ASSOC));
					array_pop($restitutionList);
					$mc->set(MC.'restitution', $restitutionList, 0, 3600);
				} else {
					$restitutionList = 'empty';
				}
			}
			
			return $restitutionList != 'empty' ? json_encode($restitutionList) : $restitutionList; 			
		} else {
			return 3;
		}
	}
	
	// 修改返还列表
	public function setRestitutionList($ucids, $ingot, $rate, $yp=null, $items=null){
		global $mc, $common, $db;
	
		$this->authenticate();		
		if($this->Authenticated){
			// 构造规则数组
			$ingotStr = '';
			$rule = '';
			$comma = 0;
			if($ingot != '') {
				$comma++;
			
				if(strpos($ucids, ',') == false) {
					$ingotStr .=  '"1":{"' . $ucids . '":{"rate":' . $rate . ', "yb":' . $ingot . '}}';
				} else {
					$ucids = explode(',', $ucids);
					$ingot = explode(',', $ingot);
					for($i = 0; $i < count($ucids); $i++) {
						if($i == count($ucids) - 1) {
							$ingotStr .= ',"' . $ucids[$i] . '":{"rate":' . $rate . ', "yb":' . $ingot[$i] . '}}';
						} else if($i == 0) {
							$ingotStr .=  '"1":{"' . $ucids[$i] . '":{"rate":' . $rate . ', "yb":' . $ingot[$i] . '}';
						} else {
							$ingotStr .=  ',"' . $ucids[$i] . '":{"rate":' . $rate . ', "yb":' . $ingot[$i] . '}';
						}
					}
					$ucids = implode(',', $ucids);
				}
			}
			
			$itemUcids =  $ucids;
			$ypStr = '';
			if($yp != '') {
				$ypStr =  $yp . '<br>';
				$comma++;
			}
			
			$itemStr = '';
			if($items != '') {
				$itemStr = "{" . $items . '}<br>';
				$comma++;
			}
			
			if($comma == 3) {
				$rule = '{' . $ingotStr . ',"2":' . $ypStr . ',"3":' . $itemStr . "}";
			} else if($comma == 2) {
				if($ypStr != '' && $ingotStr != '') {
					$rule = '{' . $ingotStr . ',"2":' . $ypStr . "}";
				} else if($itemStr != '' && $ypStr != '') {
					$rule = '{"2":' . $ypStr . ',"3":' . $itemStr . "}";
				} else {
					$rule = '{' . $ingotStr . ',"3":' . $itemStr . "}";
				}
			} else if($comma == 1) {
				if($ingotStr != '') {
					$rule = '{' . $ingotStr . '}';
				} else if ($ypStr != '') {
					$rule = '{"2":' . $ypStr . '}';
				} else {
					$rule = '{"3":' . $itemStr . '}';
				}
			}
			
			$itemRule = $rule;
			
			$list = array(
					0 => array (
							'id' => 1 ,
							'ucids' => $itemUcids,
							'rule' => $itemRule,
							'current' => 1 )
			);
			
			// 获取服务器列表
			 if (!($restitutionList = $mc->get(MC.'restitution'))) {
				$res = mysql_query('select * from ol_restitution');
				while($restitutionList[] = mysql_fetch_array($res, MYSQL_ASSOC));
				array_pop($restitutionList);
				$mc->set(MC.'restitution', $restitutionList, 0, 3600);
			}
	
			$cnt = count($restitutionList);
			$sList = array();
			for($i = 0; $i < $cnt; $i++) {
				$sList[$restitutionList[$i]['id']] = $restitutionList[$i];
			}
				
			//$list = json_decode($list, true);			
			foreach($list as $key=>$value) {
				if(!array_key_exists($value['id'], $sList)) {
					mysql_query("insert into ol_restitution values(null, '{$value['ucids']}', '{$value['rule']}', '{$value['current']}')");
				} else {
					mysql_query("update ol_restitution set ucids='{$value['ucids']}', rule='{$value['rule']}', current='{$value['current']}'  where id = '{$value['id']}' limit 1");
				}
			}
	
			$mc->delete(MC.'restitution');
			if (!($restitutionList = $mc->get(MC.'restitution'))) {
				$res = mysql_query('select * from ol_restitution');
				while($restitutionList[] = mysql_fetch_array($res, MYSQL_ASSOC));
				array_pop($restitutionList);
				$mc->set(MC.'restitution', $restitutionList, 0, 3600);
			}			
			return 0;
		} else {
			//return "$this->Ip, $this->client";
			return 3;
		}
	}
	
	//获取玩家基本信息
	public function getPlayerInfo($playersid,$nickname = '',$ucid = '') {
		global $db,$common;
		$this->authenticate();
		if($this->Authenticated) {
			if (empty($playersid) && empty($nickname) && empty($ucid)) {
				return false;
			}
			
			$where = '';
			if (!empty($playersid)) {
				$where = " p.playersid = '$playersid'";
			}
			if (!empty($nickname)) {
				$where = empty($where)?" p.nickname like '%$nickname%'":$where." and p.nickname like '%$nickname%'";
			}
			if (!empty($ucid)) {
				$where = empty($where)?" p.ucid = '$ucid'":$where." and p.ucid = '$ucid'";
			} 

			if(empty($where)) return false;

			$sql = "SELECT p.*, ifnull(d.credits, 0) `credits` FROM ".$common->tname('player');
			$sql .= " p LEFT JOIN ol_dalei_2 d on p.playersid = d.playersid";
			$sql .= " WHERE $where";

			$roleInfo = array();
			$result = $db->query($sql);

			while ($rows = $db->fetch_array($result)) {
				$roleInfo[] = array('mc'=>$rows['nickname'],
									'jb'=>$rows['player_level'],
									'id'=>$rows['playersid'],
									'ucid'=>$rows['ucid'],
									'xb'=>$rows['sex'],
									'jy'=>$rows['current_experience_value'],
									'yb'=>$rows['ingot'],
									'yp'=>$rows['silver'],
									'tq'=>$rows['coins'],
									'jl'=>floor($rows['food']),
									'czvip'=>$rows['vip'],
									'czvipend'=>$rows['vip_end_time'],
									'rwvip'=>$rows['rw_vip'],
									'rwvipend'=>$rows['rw_vip_end_time'],
									'cjsj'=>date('Y-m-d H:i:s',$rows['createTime']),
									'sffj'=>$rows['is_reason'],
									'uid'=>$rows['userid'],
									'frd'=>$rows['frd'],
									'mg_level'=>$rows['mg_level'],
									'prestige'=>$rows['prestige'],
									'ba'=>$rows['ba'],
									'rank'=>$rows['rank'],
									'phone'=>$rows['phone'],
									'jf'=>$rows['credits']);
			}
			if (empty($roleInfo)) {
				return false;
			} else {
				
				return json_encode($roleInfo);
			}
		} else {
			return 3;
		}
	}
	
	//获取武将基本信息
	public function getGinfo($playersid,$gid = '') {
		global $db,$common;
		$this->authenticate();		
		if($this->Authenticated) {	
			if (!empty($gid)) {
				$item = " && intID = '$gid'";
			} else {
				$item = '';
			}	

			$sql = "SELECT * FROM ".$common->tname('playergeneral')." WHERE playerid = '$playersid' $item";
			$result = $db->query($sql);
			$occupyInfo = array();
			while ($rows = $db->fetch_array($result)) {
				$occupyInfo[$rows['intID']] = $rows['occupied_player_nickname'];
			}
			$gInfo = array();			
			$_gInfo =  cityModel::getGeneralList(array('playersid'=>$playersid), 1, 1);

			foreach($_gInfo['generals'] as $rows){
				$gInfo[] = array('mc'=>$rows['xm'],
								 'id'=>$rows['gid'],
								 'jb'=>$rows['jb'],
								 'smz'=>$rows['smz'],
								 'zy'=>$rows['zy'],
								 'tf'=>$rows['tf'],
								 'llcs'=>$rows['llcs'],
								 'jj'=>$rows['jj'],
								 'czzt'=>$rows['czzt'],
								 'zb1'=>$rows['zb1'],
								 'zb2'=>$rows['zb2'],
								 'zb3'=>$rows['zb3'],
								 'zb4'=>$rows['zb4'],
								 'jn1'=>$rows['jnmc1'],
								 'jn2'=>$rows['jnmc2'],
								 'jdj1'=>$rows['jndj1'],
								 'jdj2'=>$rows['jndj2'],
								 'occupy'=>$occupyInfo[$rows['gid']]);
			}
			if (!empty($gInfo)) {
				return json_encode($gInfo);
			} else {
				return false;
			}
		} else {
			return 3;
		}
	}
	
	//封禁帐号 
	/*$playersid 玩家ID
	 *$option    1封禁 0解禁	
	 *$fjyy      封禁原因
	*/
	public function reSetUser($playersid,$option,$fjyy) {
		global $db,$common,$mc;
		$this->authenticate();		
		if($this->Authenticated) {
			$sql = "SELECT * FROM ".$common->tname('player')." WHERE playersid = '$playersid' LIMIT 1";
			$rows = $db->fetch_array($db->query($sql));
			if (empty($rows)) {
				return false;
			} else {
				$updateSql = "UPDATE ".$common->tname('player')." SET is_reason = '$option' WHERE playersid = '$playersid' LIMIT 1";
				$db->query($updateSql);						
				$userid = $rows['userid'];
				$userSql = "SELECT ucid FROM ".$common->tname('player')." WHERE userid = '$userid' LIMIT 1";
				$userRows = $db->fetch_array($db->query($userSql));
				$username = $userRows['ucid'];
				//$mc->delete(MC.$username.'_session'); //删除登录session信息
				if ($option == 1) {
					$mc->set(MC.$username.'_session','closed_'.$fjyy,0,21600);  //设置封禁
				} else {
					$mc->set(MC.$username.'_session','reset',0,21600);  //设置封禁
				}
				$mc->delete(MC.$playersid);
				return true;
			}			
		} else {
			return 3;
		}
	}
	
	//修改玩家信息
	public function modifyPlayerInfo($playersid,$nickname,$coins,$silver,$ingot,$food,$player_level,$sex,$vip,$vip_end_time,$frd=null,$prestige=null,$mg_level=null, $phone=null, $jf=null) {
		global $db,$common,$mc;
		$this->authenticate();		
		if($this->Authenticated) {		
			$updateInfo = array('nickname'=>$nickname,
								'coins'=>$coins,
								'silver'=>$silver,
								'ingot'=>$ingot,
								'food'=>$food,
								'player_level'=>$player_level,
								'sex'=>$sex,
								'vip'=>$vip);

			if(!is_null($frd)){
				$updateInfo['frd'] = $frd;
			}
			if(!is_null($prestige)){
				$updateInfo['prestige'] = $prestige;
			}
			if(!is_null($mg_level)){
				$updateInfo['mg_level'] = $mg_level;
			}
			if(!is_null($phone)){
				$updateInfo['phone'] = $phone;
			}
		    $updateKey['playersid'] = $playersid;
		    $common->updatetable('player',$updateInfo,$updateKey);
		    $mc->delete(MC.$playersid);

			if(isset($jf)){
				$updateArr = array('credits'=>intval($jf));
				$common->updatetable('dalei_2', $updateArr, $updateKey);
			}
		    return true;
		} else {
			return 3;
		}
	}
	
	//修改武将相关信息
	public function modifyGinfo($playersid,$gid,$general_level,$professional_level) {
		global $db,$common,$mc;
		$this->authenticate();		
		if($this->Authenticated) {	
			$updateData['general_level'] = $general_level;
			$updateData['professional_level'] = $professional_level;
			$common->updatetable('playergeneral',$updateData,"playerid='$playersid' && intID = '$gid'");
			$mc->delete(MC . $playersid . '_general');
			return true;
		} else {
			return 3;
		}	
	}
	//同步服务器玩家数据到
	public function synplayerdata($pid) {
		global $db, $common;
		$this->authenticate();		
		if($this->Authenticated) {	
			$pdata = array();
			$sql = "SELECT * FROM ".$common->tname('player')." WHERE playersid > $pid ORDER BY playersid ASC LIMIT 100";
			$result = $db->query($sql);
			while ($rows = $db->fetch_array($result)) {
				$pdata[] = $rows;
			}
			return json_encode($pdata);
		} else {
			return 3;
		}		
	}
	
	public function createAct($title, $desc, $hddx, $startT, $endT, $jpsj, $params, $jbstr, $published, $wccs, $jslj, $showT, $secTime, $secCount){
		global $db, $common, $mc;
		$this->authenticate();
		if($this->Authenticated) {
			$insertArray = array('title'=>$title,
								'desc'=>$desc,
								'hddx'=>$hddx,
								'showTime'=>$showT,
								'startTime'=>$startT,
								'endTime'=>$endT,
								'jpsj'=>serialize(json_decode($jpsj, true)),
								'params'=>serialize(json_decode($params, true)),
								'jbstr'=>serialize(json_decode($jbstr, true)),
								'published'=>$published,
								'wccs'=>$wccs,
								'jslj'=>$jslj,
								'secTime'=>serialize(json_decode($secTime, true)),
								'secCptCount'=>$secCount);
			$id = $common->inserttable('huodong', $insertArray);
			if($id<1){
				return 1;
			}
			$mc->delete(MC.'hddata');
			
			return 0;
			
		} else {
			return 3;
		}
	}
	
	public function searchAct($startT, $endT){
		global $db, $common, $mc;
		$this->authenticate();
		if($this->Authenticated) {
			$sltSql = "select hdid,title,`desc`,hddx,startTime,endTime,published,wccs,jslj from ".$common->tname('huodong');
			$sltSql .= " where ({$startT} < startTime and {$endT} > startTime) or ";
			$sltSql .= "({$startT} < endTime and {$endT} > endTime) or ";
			$sltSql .= "({$startT} > startTime and {$endT} < endTime)";
			
			$result = $db->query($sltSql);
			$hdList = array();
			while($rows = $db->fetch_array($result)){
				$hdList[] = $rows;
			}
			return $hdList;
		} else {
			return 3;
		}
	}
	
	public function manageAct($actId, $status){
		global $db, $common, $mc;
		$this->authenticate();
		if($this->Authenticated) {
			$mdfCount = $common->updatetable('huodong', array('published'=>$status), array('hdid'=>$actId));
			if($mdfCount>0){
				$mc->delete(MC.'hddata');
				return 0;
			}
			else{
				return 1;
			}
		} else {
			return 3;
		}
	}

	public function createExchange($title, $desc, $exObj, $showT, $startT, $endT, $count, $cond, $result, $secTimes, $public){
		global $db, $common, $mc;
		$this->authenticate();
		if($this->Authenticated) {
			$secTimes = json_decode($secTimes, true);
			$secTimes[] = $endT;
			$insertArray = array('title'=>$title,
								 'desc'=>$desc,
								 'exObj'=>$exObj,
								 'showTime'=>$showT,
								 'startTime'=>$startT,
								 'endTime'=>$endT,
								 'exCount'=>$count,
								 'cond'=>serialize(json_decode($cond, true)),
								 'result'=>serialize(json_decode($result, true)),
								 'secTimes'=>serialize($secTimes),
								 'public'=>$public);
			$id = $common->inserttable('exchange', $insertArray);
			if($id<1){
				return 1;
			}
			$mc->delete(MC.'exchange_data');
			
			return 0;
			
		} else {
			return 3;
		}
	}
	
	public function searchExchange($startT, $endT){
		global $db, $common, $mc;
		$this->authenticate();
		if($this->Authenticated) {
			$sltSql = "select exid,title,`desc`,exObj,exCount,showTime,startTime,endTime,public from ".$common->tname('exchange');
			$sltSql .= " where ({$startT} < startTime and {$endT} > startTime) or ";
			$sltSql .= "({$startT} < endTime and {$endT} > endTime) or ";
			$sltSql .= "({$startT} > startTime and {$endT} < endTime)";
			
			$result = $db->query($sltSql);
			$exList = array();
			while($rows = $db->fetch_array($result)){
				$exList[] = $rows;
			}
			return $exList;
		} else {
			return 3;
		}
	}
	
	public function manageExchange($exId, $status){
		global $db, $common, $mc;
		$this->authenticate();
		if($this->Authenticated) {
			$mdfCount = $common->updatetable('exchange', array('public'=>$status), array('exid'=>$exId));
			if($mdfCount>0){
				$mc->delete(MC.'exchange_data');
				return 0;
			}
			else{
				return 1;
			}
		} else {
			return 3;
		}
	}
	
	public function modifyOrdStatus($ord_id){
		global $mc,$common;
	
		$this->authenticate();
		if($this->Authenticated){
			$updateData['errcode'] = '';
			$updateData['verify'] = 0;
			$common->updatetable('yeepay_ord', $updateData, "orderNo='$ord_id' and verify=2");
			return 0;
				
		} else {
			return 3;
		}
	}

	public function chongzhi($playersid, $yuanbao){
		global $mc, $common;
		$this->authenticate();
		if($this->Authenticated){
			if($yuanbao < 0){
				return 1;
			}
			$roleInfo = array('playersid'=>$playersid);
			$isOk = roleModel::getRoleInfo($roleInfo);
			if(!$isOk){
				return 1;
			}
			
			$currYB = $roleInfo['ingot'];
			$updateRole['ingot'] = $currYB + $yuanbao;
				
			$whereRole['playersid'] = $playersid;
			$common->updatetable('player', $updateRole, $whereRole);
			$common->updateMemCache(MC.$playersid, $updateRole);
			
			$rmb = $yuanbao / RMB_TO_YUANBAO;
			$order = 'budan-'.$playersid.'-'.date('Y-m-d H:i:s');
			vipChongzhi($playersid, $rmb, $yuanbao, $order, null, true, false, 'houtai');
			
			$orderDate = time();
			$payway = 'budan';
			mytools::writelog('app.php?task=chongzhi&type=budan&option=pay',
			json_encode(array('orderId'=>$order,'ucid'=>'','payWay'=>$payway,'amount'=>$rmb,'orderStatus'=>0,'failedDesc'=>'','createTime'=>$orderDate,'status'=>0)),
			$roleInfo['player_level'],$roleInfo['userid']);
			
			return 0;
        
		}else{
	  		return 3;
		}
	}
	
	//检查占领是否正常
	public function jczlzt($occupied_playersid) {
		global $mc,$common,$db;
		$sql = "select * from ".$common->tname('player')." where playersid = $occupied_playersid limit 1";
		$result = $db->query($sql);
		$rows = $db->fetch_array($result);
		if (empty($rows)) {
			return false;
		} else {
			return $rows['aggressor_general'];
		}
	}
	
	//检查军情
	public function jcjq($gid) {
		global $db, $common;
		$sql = "select count(*) as jqsl from ".$common->tname('jq')." where wfgid = $gid limit 1";
		$result = $db->query($sql);
		$rows = $db->fetch_array($result);
		return $rows['jqsl'];
	}
	
	//纠正武将状态不一致问题
	public function xgwjzt($playersid) {
		global $mc,$common,$db;
		$this->authenticate();
		if($this->Authenticated){
			$sql = "select * from ".$common->tname('playergeneral')." where playerid = $playersid";
			$result = $db->query($sql);
			while ($rows = $db->fetch_array($result)) {
				if (!empty($rows['occupied_playersid']) && $rows['occupied_playersid'] != 0) {
					$jczlzt = $this->jczlzt($rows['occupied_playersid']);
					if ($rows['intID'] != $jczlzt) {
						$db->query("update ".$common->tname('playergeneral')." set occupied_end_time = 0,occupied_playersid = 0,occupied_player_level = 0,occupied_player_nickname = '' where intID = ".$rows['intID']." limit 1");
					}
					$jczlzt = null;
				}
				if (!empty($rows['act']) && $rows['act'] != 0) {
					$jcjq = $this->jcjq($rows['intID']);
					if ($jcjq == 0) {
						$db->query("update ".$common->tname('playergeneral')." set gohomeTime = 0,jqid = 0,zydid = 0,act = 0 where intID = ".$rows['intID']." limit 1");
					}
					$jcjq = null;
				}
			}
			$mc->delete(MC.$playersid."_general");
			return 0;				
		} else {
			return 3;
		}		
	}

	/**
	 * 查询商店商品
	 *
	 */
	public function showShop(){
		global $mc,$common,$db;
		$this->authenticate();
		if($this->Authenticated){
			$sql = "select * from ".$common->tname('shop');
			$result = $db->query($sql);

			$return['list'] = array();
			$currTime = time();
			$flag     = false;
			while($rows = $db->fetch_array($result)){
				$startT = strtotime($rows['TimeStart']);
				$endT   = strtotime($rows['TimeEnd']);

				if($endT < $currTime){
					$delSql = "delete from ".$common->tname("shop");
					$delSql .= " where ItemID = '{$rows['ItemID']}'";
					$db->query($delSql);
					continue;
				}

				$return['list'][] = array('itemID'=> $rows['ItemID'],
										  'cond'  => $rows['Condition'],
										  'startT'=> strtotime($rows['TimeStart']),
										  'endT'  => strtotime($rows['TimeEnd']),
										  'buyLimit' => $rows['BuyOfDay'],
										  'total' => $rows['Total']);
			}

			if(!$flag){
				$mc->delete(MC.'hd_mallItem');
			}

			return $return;
		}else {
			return 3;
		}
		
	}

	/**
	 * 增加新商品 
	 *
	 */
	public function addGoods($itemID, $cond, $startT, $endT, $buyLimit, $total){
		global $mc,$common,$db;
		$this->authenticate();
		if($this->Authenticated){
			$startTimeStr = date('Y-m-d H:i:s', $startT);
			$endTimeStr   = date('Y-m-d H:i:s', $endT);
			$insertArray = array('ItemID'    => $itemID,
								 'Condition' => $cond,
								 'TimeStart' => $startTimeStr,
								 'TimeEnd'   => $endTimeStr,
								 'BuyOfDay'  => $buyLimit,
								 'Total'     => $total);

			$id = $common->inserttable('shop', $insertArray);
			if($id < 0){
				return 1;
			}
			$mc->delete(MC.'hd_mallItem');
			$db->query('TRUNCATE table ol_player_shop;');
			return 0;
		}else {
			return 3;
		}
	}

	/**
	 * 修改商品信息
	 *
	 *
	 */
	public function modifyGoods($itemID, $cond, $startT, $endT, $buyLimit, $total){
		global $mc,$common,$db;
		$this->authenticate();
		if($this->Authenticated){
			$startTimeStr = date('Y-m-d H:i:s', $startT);
			$endTimeStr   = date('Y-m-d H:i:s', $endT);
			$updateArray = array('Condition' => $cond,
								 'TimeStart' => $startTimeStr,
								 'TimeEnd'   => $endTimeStr,
								 'BuyOfDay'  => $buyLimit,
								 'Total'     => $total);

			$whereArray = array('ItemID' => $itemID);
			$value = $common->updatetable('shop', $updateArray, $whereArray);

			$mc->delete(MC.'hd_mallItem');

			return $value;
		}else {
			return 3;
		}
	}
	
	/**
	 * 获取商城道具
	 *
	 */
	public function getShopList(){
		global $mc, $common, $db, $G_Items;
		
		$this->authenticate();
		if($this->Authenticated){
			$returnValue = array();		
			$mallInfo = null;
			$list = null;
			
			// 根据管理后台设置的排序规则对商城的道具排序
			if (!($shop_sortrule = $mc->get(MC.'shop_sortrule'))) {
				$shop_sortrule_Ret = $db->query("SELECT * FROM ".$common->tname('shop_sortrule'));
				$shop_sortrule = $db->fetch_array($shop_sortrule_Ret, MYSQL_ASSOC);
				$shop_sortrule = $shop_sortrule['sort_rule'];
				$mc->set(MC.'shop_sortrule', $shop_sortrule, 0, 0);
			}			
			
			// 加载活动商城道具
			if (!($hd_mallItem = $mc->get(MC.'hd_mallItem'))) {
				$mallItemRet = $db->query("SELECT * FROM ".$common->tname('shop'));
				while($hd_mallItem[] = $db->fetch_array($mallItemRet));
				array_pop($hd_mallItem);
				if(count($hd_mallItem) > 0) $mc->set(MC.'hd_mallItem', $hd_mallItem, 0, 0);
			}
			$hd_itemArr = array();			
			foreach($hd_mallItem as $hd_key=>$hd_value) {
				$hd_itemArr[$hd_value['TimeStart'] . ',' . $hd_value['TimeEnd']] = $hd_value['ItemID'];	
			}
			date_default_timezone_set('PRC');
			ConfigLoader::GetConfig($G_Items,'G_Items');
			$mallInfo = $equipType = $itemType = array();
			foreach($G_Items as $key=>$value) {
				// 活动商城道具
				$hd_key = array_search($key, $hd_itemArr);
				$curr_time = time();		
				$hd_timestart = $hd_timeend = 0;			 
				if($hd_key !== false) {
					$time_limit = explode(',', $hd_key);
					$hd_timestart = strtotime($time_limit[0]);
					$hd_timeend = strtotime($time_limit[1]);			
				}
				if(($hd_key !== false && $curr_time >= $hd_timestart && $curr_time <= $hd_timeend) || $value['IsMallItem'] != 0) {
					$mallInfo[] = $value;
					$itemType[] = $value['ItemType'];
					$equipType[] = $value['EquipType'];								
				}
			}		
			array_multisort($itemType, $equipType, SORT_ASC, SORT_NUMERIC, $mallInfo);
			for($idx = 0; $idx < count($mallInfo); $idx++) {
				if($mallInfo[$idx]['IsMallItem'] == 0) {
					$mallInfo[$idx]['IsMallItem'] = '11';					
				}
			}
			
			$list = array();
			for($i = 0; $i < count($mallInfo); $i++) {
				$scInfo = $mallInfo[$i]['IsMallItem'];
				$sclx = substr($scInfo, 1);			
				if($sclx == 1) {
					$list[] = intval($mallInfo[$i]['ItemID']);
				}
			}
			
			if($shop_sortrule != '0') {
				$shop_sortrule = json_decode($shop_sortrule, true);
				foreach($shop_sortrule as $k=>$v) {
					$shop_rule[$v['id']] = $v;
				}
				
				$no_sort_arr = array();							
				foreach($list as $k1=>$v1) {
					if(!isset($shop_rule[$v1])) {
						$no_sort_arr[] = $v1;
						$new_list[$v1] = array('id'=>$v1, 'hot'=>0);
					} else {
						$new_list[$v1] = array('id'=>$v1, 'hot'=>$shop_rule[$v1]['hot']);
					}
				}

				$list = array();		
				foreach($shop_sortrule as $k1=>$v1) {
					if(in_array($v1['id'], $no_sort_arr) != false || !isset($new_list[$v1['id']])) {
						continue;
					} else {
						$list[] = $new_list[$v1['id']];
					}
				}
								
				if(count($no_sort_arr) > 0) {
					$append_arr = array();
					foreach($no_sort_arr as $k2=>$v2) {
						$append_arr[] = $new_list[$v2];
					}
					$list = array_merge($list, $append_arr);					
				}
				$new_list = $list;
			} else {
				foreach($list as $k1=>$v1) {
					$new_list[] = array('id'=>$v1, 'hot'=>0);
				}
			}
			
			return $new_list;// 例如arry(0=>array('id'=>'18080','hot'=>1),...);
		}else {			
			return 3;
		}		
	}

	/**
	 * 获取商城道具排序
	 *
	 */
	public function setShopSortedList($sortedList){
		global $mc, $common, $db, $G_Items;
		
		$this->authenticate();
		if($this->Authenticated){				
			$update['sort_rule'] = $sortedList;
			$where['id'] = 1;
			$common->updatetable('shop_sortrule', $update, $where);
			
			$mc->set(MC.'shop_sortrule', $sortedList, 0, 0);
					
			return 0;
		}else {
			return 3;
		}		
	}
	
	/*遣返逐鹿点玩家数据*/
	public function qfzlsj($playersid,$sfqbqf,$gid) {
		global $mc,$common,$db;
		$this->authenticate();	
		if($this->Authenticated){
			if (!empty($playersid)) {
				$sql = "select * from ".$common->tname('jq')." where wfpid = $playersid && wfgid = $gid && jqlx = 5 limit 1";
				$result = $db->query($sql);
				$rows = $db->fetch_array($result);
				if (empty($rows)) {
					return 5; //没有该军情
				} else {
					$sqlzl = "select * from ".$common->tname('zyd_qxzl')." where zlwjid = $gid limit 1";
					$reszl = $db->query($sqlzl);
					$rowszl = $db->fetch_array($reszl);
					if (!empty($rowszl)) {
						$db->query("update ".$common->tname('zyd_qxzl')." set zlpid = 0,zlpname = '',zlwjid = 0,zlwjmc = '',zlsj = 0 where zlwjid = $gid");
						$mc->detete(MC.'zlzydxx');
						$mc->detete(MC.'1_zlzyd');
						$mc->detete(MC.'2_zlzyd');
						$mc->detete(MC.'3_zlzyd');
						$mc->detete(MC.'4_zlzyd');
						$mc->detete(MC.'5_zlzyd');
						$mc->detete(MC.'6_zlzyd');
						$mc->detete(MC.'7_zlzyd');
					}
					$db->query("update ".$common->tname('jq')." set jqlx = 7 where jq_id = ".$rows['jq_id']." limit 1");
					return 0;
				}
			} elseif (!empty($sfqbqf)) {
				$H = date('H',time());
				if (in_array($H,array(12,13,17,18,19,20,21))) {
					return 4;   //逐鹿进行中禁止全部遣返
				}
				$db->query("update ".$common->tname('zyd_qxzl')." set zlpid = 0,zlpname = '',zlwjid = 0,zlwjmc = '',zlsj = 0");
				$mc->detete(MC.'zlzydxx');
				$mc->detete(MC.'1_zlzyd');
				$mc->detete(MC.'2_zlzyd');
				$mc->detete(MC.'3_zlzyd');
				$mc->detete(MC.'4_zlzyd');
				$mc->detete(MC.'5_zlzyd');
				$mc->detete(MC.'6_zlzyd');
				$mc->detete(MC.'7_zlzyd');	
				$db->query("update ".$common->tname('jq')." set jqlx = 7 where jqlx = 5");		
				return 0; 	
			} else {
				return 1;//非法请求
			}
		} else {
			return 3;
		}	
	}

	// 获取玩家等级分布
	public function getUserLevelDistr(){
		global $mc,$common,$db;
		$this->authenticate();	
		if($this->Authenticated){
			$sql = 'select count(1) p_count, player_level from ol_player';
			$sql .= ' GROUP BY player_level';
			$sql .= ' ORDER BY player_level desc';

			$result = $db->query($sql);
			$level = array();
			while($rows = $db->fetch_array($result)){
				$level[] = $rows;
			}
			return serialize($level);
		}else{
			return 3;
		}
	}

	public function getUserReport($startT, $endT, $pid=null){
		global $mc,$common,$db;
		$this->authenticate();	
		if($this->Authenticated){
			$whereStr = " where date>='{$startT}' and date<={$endT}";
			$whereStr = empty($pid)?$whereStr:" where jbr_pid='{$pid}'";

			$countSql = "select count(1) as countV from ".$common->tname('chat_report').$whereStr;
			$result = $db->query($countSql);
			$row = $db->fetch_array($result);
			$count = $row['countV'];

			$sql = "select * from ".$common->tname('chat_report') . $whereStr;
			$result = $db->query($sql);
			$reportList = array();
			while($row = $db->fetch_array($result)){
				$reportList[] = $row;
			}
			//return $countSql;
			return json_encode(array('count'=>$count,
									 'reports'=>$reportList));
		}else{
			return 3;
		}
	}

	public function dropUser($playersid){
		global $db;
		$this->authenticate();
		if($this->Authenticated){
			$dropSql = "update ol_player p, ol_user u";
			$dropSql .= " set p.ucid='', p.userid=0, u.username='', u.realname=''";
			$dropSql .= " where p.playersid='{$playersid}' and  p.userid = u.userid";
			$db->query($dropSql);
			return 0;
		}else{
			return 3;
		}
	}
	
	//清理任务状态数值
	public function qlrwzt($playersid) {
		global $mc,$common,$db;
		$this->authenticate();	
		if($this->Authenticated){				
			$sql = "SELECT * FROM ".$common->tname('accepted_quests')." WHERE playersid = $playersid";
			$resutl = $db->query($sql);
			$qarr = array();
			$accrw = array();
			while ($rows = $db->fetch_array($resutl)) {
				$qarr[] = $rows['QuestID'];
				$accrw[] = $rows;
			}
			$sql_p = "SELECT * FROM ".$common->tname('player')." WHERE playersid = $playersid";
			$res_p = $db->query($sql_p);
			$rows_p = $db->fetch_array($res_p);
			$sql_q = "SELECT * FROM ".$common->tname('quests_new_status')." WHERE playersid = $playersid";
			$res_q = $db->query($sql_q);
			$rows_q = $db->fetch_array($res_q);
			$qstatusInfo = $rows_q['qstatusInfo'];
			if (empty($qstatusInfo)) {
				return 0;
			} else {
				$qstatusInfoArr = unserialize($qstatusInfo);
				$bl = array();
				foreach ($qstatusInfoArr as $qkey_1 => $qValue_1) {
					if ($qValue_1 == 10 && in_array($qkey_1,$qarr)) {
						$bl[] = $qkey_1;
					}
				}
				if (!empty($bl)) {
					$blist = implode(',',$bl);
				}
			}			
			if (empty($rows_p)) {
				return 4;
			} else {
				$completeQuests = $rows_p['completeQuests'];
				if (!empty($completeQuests)) {
					if (empty($bl)) {
						$db->query("DELETE FROM ".$common->tname('accepted_quests')." WHERE playersid = $playersid && QuestID in ($completeQuests)");
					} else {
						$db->query("DELETE FROM ".$common->tname('accepted_quests')." WHERE playersid = $playersid && QuestID in ($completeQuests) && QuestID not in ($blist)");
					}
					$completeQuestsArr = explode(',',$completeQuests);
					if (!is_array($completeQuestsArr)) {
						$completeQuestsArr = array($completeQuestsArr);
					}
				} else {
					$completeQuestsArr = array();
				}				
			}				
			if (empty($qarr)) {
				return 0;
			} else {					
				foreach ($qstatusInfoArr as $qkey => $qValue) {
					if (!in_array($qkey,$qarr) || (in_array($qkey,$completeQuestsArr) && $qValue != 10)) {
						unset($qstatusInfoArr[$qkey]);
					}
				}
				$newstr = serialize($qstatusInfoArr);
				$common->updatetable('quests_new_status',"qstatusInfo = '$newstr'","playersid = $playersid");
				$this->sxrcrw($accrw,$rows_p,$qstatusInfoArr);
				$mc->delete(MC.$playersid."_qstatus");
				return 0;				
			}
		} else {
			return 3;
		}
	}
	
	//刷新日常任务
	public function sxrcrw($accrw,$pInfo,$rwztsj) {
		global $mc,$common,$db;
		$rckey = array();
		$nyqid = array();
		$easy = array(2002000,2003001,2003002,2004015,2005001,2004000,2004001,2004003,2004004,2001018,2004017,2004012,2001000,2003000);
		$player_level = $pInfo['player_level'];
		$playersid = $pInfo['playersid'];
		foreach ($accrw as $accrwValue) {
			if ($accrwValue['RepeatInterval'] == 1 && $accrwValue['published'] == 1) {
				$rckey[] = $accrwValue['QuestID'];
			}
			if ($accrwValue['RepeatInterval'] == 1 && $accrwValue['published'] != 1) {
				$rwInfo = ConfigLoader::GetQuestProto($accrwValue['QuestID']);
        		$Level_Min = $rwInfo['Level_Min'];
        		$Level_Max = $rwInfo['Level_Max'];	
				if (!empty($rwInfo) && $player_level >= $Level_Min && $player_level <= $Level_Max) {
					if ($player_level < 15) {
						if (in_array($accrwValue['QuestID'],$easy)) {
							$nyqid[$accrwValue['QuestID']] = 1;
						}
					} else {
						$nyqid[$accrwValue['QuestID']] = 1;
					}					
				}
			}
			$rwInfo = $Level_Min = $Level_Max = null;
		}
		$rwsl = count($rckey);
		if ($rwsl >= 3) {
			return false;
		} else {
			$xytjrw = 3 - $rwsl;
			$nysl = count($nyqid);
			if ($nysl == 0) {
				return false;
			} else {
				if ($nysl < $xytjrw) {
					$xytjrw = $nysl;
				}
				$chosedKey = array_rand($nyqid,$xytjrw);
				if (!is_array($chosedKey)) {
					$chosedKey = array($chosedKey);
				}
				$updateKey = implode(',',$chosedKey);
				$common->updatetable('accepted_quests',"published = 1","playersid = $playersid && QuestID IN ($updateKey)");
				foreach ($rwztsj as $rwKey => $rwValue) {
					if (in_array($rwKey,$chosedKey)) {
						$rwztsj[$rwKey] = 5;
					}
				}
				$newstr = serialize($rwztsj);
				$common->updatetable('quests_new_status',"qstatusInfo = '$newstr'","playersid = $playersid");
			}
		}
		
	}
	
	//设置任务为可完成领取奖励状态
	public function szrwjl($playersid,$qid) {
		global $mc,$common,$db;
		$this->authenticate();	
		if($this->Authenticated){
			if (empty($qid)) {
				return 4;
			}
			$sql = "SELECT * FROM ".$common->tname('accepted_quests')." WHERE playersid = $playersid && QuestID = $qid";
			$result = $db->query($sql);
			$sl = $db->num_rows($result);
			$sql_q = "SELECT * FROM ".$common->tname('quests_new_status')." WHERE playersid = $playersid";
			$res_q = $db->query($sql_q);
			$rows_q = $db->fetch_array($res_q);
			$qstatusInfo = $rows_q['qstatusInfo'];
			$qstatusInfoArr = unserialize($qstatusInfo);	
			if (isset($qstatusInfoArr[$qid])) {
				if ($qstatusInfoArr[$qid] == 10) {
					return 5;
				}
			}					
			if ($sl > 0) {
				$common->updatetable('accepted_quests',"Qstatus = 1","playersid = $playersid && QuestID = $qid && Qstatus != 3");
			} else {
				if (isset($qstatusInfoArr[$qid])) {
					$rwInfo = ConfigLoader::GetQuestProto($qid);
					if (empty($rwInfo)) {
						return 4;
					}
					$insert['QuestID'] = $qid;
					$insert['playersid'] = $playersid;
					$insert['Qstatus'] = 1;
					$insert['Progress'] = 0;
					$insert['AcceptTime'] = time();
					$insert['ExtraData'] = '';
					$insert['readStatus'] = 1;
					$insert['published'] = 1;
					$insert['RepeatInterval'] = $rwInfo['RepeatInterval'];
					$insert['mblx'] = $rwInfo['mblx'];
					$common->inserttable('accepted_quests',$insert);
				} else {
					return 4;  //该任务不存在
				}				
			}		
			if (empty($qstatusInfo)) {
				$newstr = array($qid=>1);
			} else {				
				$newstr = array($qid=>1) + $qstatusInfoArr;	
			}
			$new = serialize($newstr);
			$common->updatetable('quests_new_status',"qstatusInfo = '$new'","playersid = $playersid");
			$mc->delete(MC.$playersid."_qstatus");
			return 0;			
		} else {
			return 3;
		}		
	}
	
	//添加一个新任务
	public function tjxrw($playersid,$qid) {
		global $mc,$common,$db;
		$this->authenticate();	
		if($this->Authenticated){
			if (empty($qid)) {
				return 4;
			}
			$sql_p = "SELECT * FROM ".$common->tname('player')." WHERE playersid = $playersid";
			$res_p = $db->query($sql_p);
			$rows_p = $db->fetch_array($res_p);
			if (empty($rows_p))	{
				return 4;
			} else {
				$completeQuests = $rows_p['completeQuests'];
				if (!empty($completeQuests)) {
					$completeQuestsArr = explode(',',$completeQuests);
					if (!is_array($completeQuestsArr)) {
						$completeQuestsArr = array($completeQuestsArr);
					}
				} else {
					$completeQuestsArr = array();
				}
				$sql_q = "SELECT * FROM ".$common->tname('quests_new_status')." WHERE playersid = $playersid";
				$res_q = $db->query($sql_q);
				$rows_q = $db->fetch_array($res_q);
				$qstatusInfo = $rows_q['qstatusInfo'];
				if (!empty($qstatusInfo)) {
					$qstatusInfoArr = unserialize($qstatusInfo);
				} else {
					$qstatusInfoArr = array();
				}	
				if (in_array($qid,$completeQuestsArr) || isset($qstatusInfoArr[$qid])) {
					return 5;
				} else {
					$rwInfo = ConfigLoader::GetQuestProto($qid);
					if (empty($rwInfo)) {
						return 4;
					}
					$insert['QuestID'] = $qid;
					$insert['playersid'] = $playersid;
					$insert['Qstatus'] = 0;
					$insert['Progress'] = $rwInfo['showProcess'];
					$insert['AcceptTime'] = time();
					$insert['ExtraData'] = '';
					$insert['readStatus'] = 1;
					$insert['published'] = 1;
					$insert['RepeatInterval'] = $rwInfo['RepeatInterval'];
					$insert['mblx'] = $rwInfo['mblx'];
					if ($rwInfo['RepeatInterval'] == 2) { //不能添加主目标任务
						return 6;
					}
					$sql_acc = "SELECT count(intID) as rwsl FROM ".$common->tname('accepted_quests')." WHERE playersid = $playersid && QuestID = $qid";
					$res_acc = $db->query($sql_acc);
					$rwsl = $db->fetch_array($res_acc);
					if ($rwsl['rwsl'] > 0) {
						$common->updatetable('accepted_quests',"published = 1","playersid = $playersid && QuestID = $qid");
					} else {
						$common->inserttable('accepted_quests',$insert);	
					}				
					if ($rwInfo['RepeatInterval'] == 0) {
						$status = 2;
					} else {
						$status = 5;
					}
					if (empty($qstatusInfoArr)) {
						$newstr = array($qid=>$status);
					} else {
						$newstr = array($qid=>$status) + $qstatusInfoArr;
					}
					$new = serialize($newstr);
					$common->updatetable('quests_new_status',"qstatusInfo = '$new'","playersid = $playersid");
					$mc->delete(MC.$playersid."_qstatus");
					return 0;									
				}				
			}	
		} else {
			return 3;
		}	
	}	
	
	//删除任务
	public function scrwsj($playersid,$qid,$jzjs = 1) {
		global $mc,$common,$db;
		$this->authenticate();	
		if($this->Authenticated){
			if (empty($qid)) {
				return 4;
			}
			$rwInfo = ConfigLoader::GetQuestProto($qid);
			if (empty($rwInfo)) {
				return 5;
			}
			if ($rwInfo['RepeatInterval'] == 2) {
				return 6;
			}
			$db->query("DELETE FROM ".$common->tname('accepted_quests')." WHERE playersid = $playersid && QuestID = $qid");
			$sql_q = "SELECT * FROM ".$common->tname('quests_new_status')." WHERE playersid = $playersid";
			$res_q = $db->query($sql_q);
			$rows_q = $db->fetch_array($res_q);
			$qstatusInfo = $rows_q['qstatusInfo'];	
			if (!empty($qstatusInfo)) {
				$qstatusInfoArr = unserialize($qstatusInfo);
				unset($qstatusInfoArr[$qid]);
				if (empty($qstatusInfoArr)) {
					$newstr = '';
				} else {
					$newstr = serialize($qstatusInfoArr);
				}
				$common->updatetable('quests_new_status',"qstatusInfo = '$newstr'","playersid = $playersid");
				$mc->delete(MC.$playersid."_qstatus");				
			}	
			if ($jzjs == 1) {
				$sql_p = "SELECT * FROM ".$common->tname('player')." WHERE playersid = $playersid";
				$res_p = $db->query($sql_p);
				$rows_p = $db->fetch_array($res_p);
				if (empty($rows_p)) {
					return 0;
				} else {
					$completeQuests = $rows_p['completeQuests'];
					if (empty($completeQuests)) {
						$newsCom = $qid;
					} else {
						$newsCom = $completeQuests.','.$qid;
					}
					$common->updatetable('player',"completeQuests = '$newsCom'","playersid = $playersid");
					$mc->delete(MC.$playersid);
				}
			}
			return 0;		
		} else {
			return 3;
		}		
	}
	
	//任务列表
	public function rwlb($playersid) {
		global $mc,$common,$db;
		$this->authenticate();	
		if($this->Authenticated) {
			$sql_acc = "SELECT * FROM ".$common->tname('accepted_quests')." WHERE playersid = $playersid";
			$res_acc = $db->query($sql_acc);
			$accArr = array();
			$acczt = array();
			$publish = array();
			while ($rows_acc = $db->fetch_array($res_acc)) {
				$accArr[] = $rows_acc['QuestID'];
				$acczt[$rows_acc['QuestID']] = $rows_acc['Qstatus'];
				$publish[$rows_acc['QuestID']] = $rows_acc['published'];
			}
			$sql_p = "SELECT * FROM ".$common->tname('player')." WHERE playersid = $playersid";
			$res_p = $db->query($sql_p);
			$rows_p = $db->fetch_array($res_p);	
			$completeQuests = $rows_p['completeQuests'];
			if (!empty($completeQuests)) {
				$completeQuestsArr = explode(',',$completeQuests);
				if (!is_array($completeQuestsArr)) {
					$completeQuestsArr = array($completeQuestsArr);
				}
			} else {
				$completeQuestsArr = array();
			}					
			$sql_q = "SELECT * FROM ".$common->tname('quests_new_status')." WHERE playersid = $playersid";
			$res_q = $db->query($sql_q);
			$rows_q = $db->fetch_array($res_q);
			if (empty($rows_q)) {
				return 4;
			} else {
				$rwsj = unserialize($rows_q['qstatusInfo']);
				$list = array();
				foreach ($rwsj as $key=>$value) {
					$rwInfo = ConfigLoader::GetQuestProto($key);
					if (empty($rwInfo)) {
						continue;
					}
					if (!isset($publish[$key])) {
						$publish[$key] = 1;
					}
					if ($publish[$key] != 1 && $rwInfo['RepeatInterval'] == 1) {
						continue;
					} else {
						if ($rwInfo['RepeatInterval'] == 2) {
							$mblx = '主目标';
						} elseif ($rwInfo['RepeatInterval'] == 1) {
							$mblx = '日常任务';
						} else {
							$mblx = '子目标';
						}
						if (!in_array($rwInfo['QuestID'],$accArr) || (in_array($rwInfo['QuestID'],$completeQuestsArr) && $value != 10)) {
							$ext = '(错误的显示)';
						} else {
							$ext = '';
						}
						if ($value == 10) {
							$sflj = '已领奖';
						} elseif ($acczt[$key] == 1) {
							$sflj = '是';
						} else {
							$sflj = '否';
						}
						$list[] = array('id'=>$rwInfo['QuestID'],'rwlx'=>$mblx,'rwmc'=>$rwInfo['QTitle'].$ext,'rwms'=>$rwInfo['completeDesc'],'sflj'=>$sflj);
					}
					$mblx = null;
					$ext = null;
					$sflj = null;
					$rwInfo = null;
				}
				if (!empty($list)) {
					return json_encode($list);
				} else {
					return 4;
				}
			}			
		} else {
			return 3;
		}		
	}
	
	//任务操作
	public function rwcz($playersid,$czlx,$qid='') {
		$this->authenticate();	
		if($this->Authenticated) {	
			switch ($czlx) {
				case 1:  //添加新任务
					$res = $this->tjxrw($playersid,$qid);
					break;
				case 2:  //删除一个任务（可再次接收到）
					$res = $this->scrwsj($playersid,$qid,2);
					break;
				case 3:  //删除一个任务（禁止再次接收到）
					$res = $this->scrwsj($playersid,$qid,1);
					break;	
				case 4:  //设置任务为完成状态
					$res = $this->szrwjl($playersid,$qid);
					break;
				case 5:  //获取任务列表
					$res = $this->rwlb($playersid);
					break;
				case 6:  //清理任务显示不正确问题
					$res = $this->qlrwzt($playersid);
					break;
				default:		
					$res = 5;
					break;						
			}
			return $res;
		} else {
			return 3;
		}	
	}

	/**
	 * 根据渠道,时间段(int)得到以渠道分组的注册用户数和充值金额
	 * 这里只反回不超过三十一天的数据
	 *
	 */
	public function showRealTimeDate($qd, $startT, $endT, $sepcial=null){
		global $mc,$common,$db;
		$this->authenticate();	
		if($this->Authenticated) {
			// only 31 day
			$startDate = date('Y-m-d H:i:s', $startT);
			$subEndDate = 31*24*3600 + $startT;
			$endT = $endT>$subEndDate?$subEndDate:$endT;
			$endDate   = date('Y-m-d H:i:s', $endT);

			$qdSql = 'select count(1) num, u.qd, u.register_date from ol_user u, ol_player p';
			$qdSql .= " where u.qd='{$qd}' and p.userid=u.userid";
			$qdSql .= " and u.register_date > '{$startDate}' and u.register_date < '{$endDate}'";
			$qdSql .= " GROUP BY u.register_date";

			$result = $db->query($qdSql);
			$qdInfo = array();
			while($row = $db->fetch_array($result)){
				$qdInfo[$row['register_date']] = array('reg'=>$row['num'], 'amount'=>0);
			}

			// 对于像tom充值这样的渠道$isAll需要传入d对应的充值类型
			// 其它普通渠道将不会查寻到这种充值
			if(is_null($sepcial)){
				$paySql = "select sum(v.money) amount, u.qd, DATE_FORMAT(v.insertTime, '%Y-%m-%d') payday";
				$paySql .= " from ol_vip_money_log v, ol_player p, ol_user u";
				$paySql .= " where u.qd='{$qd}' and p.userid=u.userid and v.playersid=p.playersid";
				$paySql .= " and v.insertTime>'{$startDate}' and v.insertTime<'{$endDate}'";
				$paySql .= " and v.special=''";
				$paySql .= " group by payday";
			}else{
				$paySql = "select sum(v.money) amount, '{$sepcial}' qd,  DATE_FORMAT(v.insertTime, '%Y-%m-%d') payday";
				$paySql .= " from ol_vip_money_log v";
				$paySql .= " where v.special='{$sepcial}'";
				$paySql .= " and v.insertTime>'{$startDate}' and v.insertTime<'{$endDate}'";
				$paySql .= " group by payday";
			}
			echo $paySql;
			$result = $db->query($paySql);
			while($row = $db->fetch_array($result)){
				$qdInfo[$row['payday']]['amount'] = $row['amount'];
				$qdInfo[$row['payday']]['reg'] = isset($qdInfo[$row['payday']]['reg'])?$qdInfo[$row['payday']]['reg']:0;
			}

			return json_encode($qdInfo);
		}else{
			return 3;
		}
	}
	
	//检查礼品码是否存在
	public function checkwj($lpm) {
		global $mc,$common,$db;
		$sql = "select * from ol_lpm where lpm = '$lpm'";
		$result = $db->query($sql);
		$rows = $db->fetch_array($result);
		if (!empty($rows)) {
			return false;
		} else {
			return true;
		}
	}
	//礼品码生成
	public function sclpm($pre) {
		$str = str_shuffle('0123456789');
		$num = $pre.substr($str,0,6);
		$check = $this->checkwj($num);
		if ($check === true) {
			return $num;
		} else {
			$num = $this->sclpm($pre);
			return $num;
		}
	}	
	//礼品码入库
	public function lpm($pre, $sl, $emdTime){
		global $mc,$common,$db;
		$this->authenticate();	
		if($this->Authenticated) {			
			for ($i=0;$i<$sl;$i++) {
				$num = $this->sclpm($pre);
				$db->query("insert into ol_lpm(lpm,endTime,lpmlx) values ('$num','$emdTime','$pre')");
			}
			return 0;			
		} else {
			return 3;
		}
	}

	// 添加武将
	public function addGen($pid, $roleName, $genStr){
		global $mc,$common,$db;
		$this->authenticate();	
		if($this->Authenticated) {
			$playersid = intval($pid);
			$roleInfo = array('playersid'=>$playersid);
			$isOk = roleModel::getRoleInfo($roleInfo);
			// 检查玩家信息是否正确
			if(!$isOk){
				return 1;
			}elseif($roleInfo['nickname'] != $roleName){
				return 1;
			}
			$genList = json_decode($genStr, true);

			$modifyIDs = array();
			foreach($genList as $genInfo){
				$sql = "insert into ol_playergeneral ";
				$sql .= "( intID, playerid ,general_sort, general_name, general_level, general_sex, general_life,";
				$sql .= " avatar, professional, f_status, understanding_value, professional_level,";
				$sql .= " mj, llcs, jn1, jn1_level, jn2, jn2_level, py_gj, py_fy, py_tl, py_mj, current_experience) values ";

				$sql .= "('{$genInfo["intID"]}','{$playersid}','{$genInfo["general_sort"]}','{$genInfo["general_name"]}',";
				$sql .= "'{$genInfo["general_level"]}','{$genInfo["general_sex"]}','{$genInfo["general_life"]}',";
				$sql .= "'{$genInfo["avatar"]}','{$genInfo["professional"]}','{$genInfo["f_status"]}','{$genInfo["understanding_value"]}',";
				$sql .= "'{$genInfo["professional_level"]}','{$genInfo["mj"]}','{$genInfo["llcs"]}','{$genInfo["jn1"]}',";
				$sql .= "'{$genInfo["jn1_level"]}','{$genInfo["jn2"]}','{$genInfo["jn2_level"]}','{$genInfo["py_gj"]}','{$genInfo["py_fy"]}',";
				$sql .= "'{$genInfo["py_tl"]}','{$genInfo["py_mj"]}','{$genInfo["current_experience"]}')";

				$result = $db->query($sql);
				$affRow = $db->affected_rows();
				if(0 < $affRow){
					$modifyIDs[] = "{$genInfo["general_name"]}({$genInfo['intID']})";
				}
			}
			if(count($modifyIDs)>0){
				return '"'.implode(',', $modifyIDs).'"';
			}else{
				return 2;
			}
		}else{
			return 3;
		}
	}
}

@ob_clean();

$classExample = array();
$server = new SoapServer(null, array('uri'=>WS_SERVER, 'classExample'=>$classExample));
$server->setClass('mysoapclass');
$server->handle();

