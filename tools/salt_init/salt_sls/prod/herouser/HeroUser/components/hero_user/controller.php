<?php 
class userController {		
	//用户注册
	static function register(&$userInfo) {
		global $_SC, $_SGLOBAL, $mc, $payServer, $user_lang, $allowuser, $apkv_SC;
		$apkvs = _get('apkvs');
		$qd    = _get('qd');
		$_SESSION['qdxx'] = $qd;
		$userInfo['qd'] = $qd;
		$getUserid = _get('userId');
		$qdbb = qdbb($qd);
		if (isset($qdbb['swf_version'])) {
			$_SC['swf_version'] = $qdbb['swf_version'];
		}		
		if (!empty($apkvs)) {
			if (($_SC['status'] == 0 && in_array($getUserid,$allowuser)) || $_SC['status'] == 1) {				
				/*$apkvsInfo = explode('.',$apkvs);
				if (isset($apkvsInfo[2])) {
					$v3 = $apkvsInfo[2] / 1000;
				} else {
					$v3 = 0;
				}
				$configApkvsInfo = explode('.',$_SC['apkvs']);
				if (isset($configApkvsInfo[2])) {
					$bdv3 = $configApkvsInfo[2] / 1000;
				} else {
					$bdv3 = 0;
				}
				$version1 = $apkvsInfo[0] + $apkvsInfo[1] / 100 + $v3;
				$version2 = $configApkvsInfo[0] + $configApkvsInfo[1] / 100 + $bdv3;*/
				if (isset($qdbb['apkvs'])) {
					$_SC['apkvs'] = $qdbb['apkvs'];
					$apkv_SC['links_a'] = $qdbb['links_a'];
					$apkv_SC['links_i'] = $qdbb['links_i'];
				}				
				if ($apkvs < $_SC['apkvs']) {
					ClientView::show(array('status'=>777,'message'=>$apkv_SC['message'],'links_a'=>$apkv_SC['links_a'],'links_i'=>$apkv_SC['links_i']));
					exit;
				}
			}
		}			
		$loginBlock = $mc->get('loginblock');
		if($loginBlock == 1) {
			$returnValue['status'] = 22;
			$returnValue['message'] = $user_lang['register_1'];
			$returnValue['rsn'] = intval(_get('ssn'));
			$returnValue['vipmode'] = VIP_CREDITS_CONTROL;
			ClientView::show($returnValue);
			exit;
		}
		
		$nowTime = $_SGLOBAL['timestamp'];
		$nowDate = date('Y-m-d', $nowTime);
		if ($userInfo['client'] == 0) {		
			$b = _get('bs');
			if(!empty($b)) {
				if(SYPT == 2) {         //uc平台
	
				} elseif(SYPT == 0) {   //测试QQ方式登录
					/*$other = $_COOKIE['herooline'];
					//$a = unserialize($other);
					list($a0,$a1, $a2) = explode('|',$other);
					$qq_token = $a0;
					$openid = $a1;
					$pt = $a2;
					$uid = $openid;
										
					if(userModel::isPlayerExists($uid)) {
						$userInfo = array('username'=>$uid,'realname'=>'','client'=>0,'register_time'=>$nowTime,'uzone_token'=>'');
						$result = userModel::saveUserInfo($userInfo);
						$userInfoflash = array('username'=>$uid,'realname'=>'','client'=>0,'register_time'=>$nowTime,'uzone_token'=>'','inviteid'=>'','sexid'=>'');
						userModel::flashUserLogin($userInfoflash);						
						userController::authorize($userInfoflash,$userInfo['xyh'],0);
					} else {					
						$userInfo = array('username'=>$uid,'realname'=>trim($uid),'client'=>0,'register_time'=>$nowTime,'uzone_token'=>'');					
						if($userInfo['username'] == '' || $userInfo['realname'] == '') {
							$returnValue['status'] = 21;
							$returnValue['message'] = '请在浏览器设置中关闭云加速或极速模式后刷新游戏页面！';
							$returnValue['rsn'] = intval(_get('ssn'));
							userView::show($returnValue);
						} else {
							$result = userModel::saveUserInfo($userInfo);
							$userInfoflash = array('username'=>$uid,'realname'=>trim($uid),'client'=>0,'register_time'=>$nowTime,'uzone_token'=>'','inviteid'=>'','sexid'=>0);
							userModel::flashUserLogin($userInfoflash);
							userController::authorize($userInfoflash,$userInfo['xyh'],0);
						}
					}*/
			
				} else if(SYPT == 1) {
					$qq_token = $userInfo['uzone_token'];
					$openid = $userInfo['username'];
					$pt = 'QQ';
					$uid = $openid;
					
					$qqnickname = '';
					if(userModel::isPlayerExists($uid)) {
						$userInfo = array('username'=>$uid,'realname'=>'','client'=>0,'register_time'=>$nowTime,'uzone_token'=>'','qd'=>$qd, 'register_date'=>$nowDate);
						$result = userModel::saveUserInfo($userInfo);
						$userInfoflash = array('username'=>$uid,'realname'=>'','client'=>0,'register_time'=>$nowTime,'uzone_token'=>'','inviteid'=>'','sexid'=>'','nc'=>$qqnickname);
						userModel::flashUserLogin($userInfoflash);
						userController::authorize($userInfoflash,$userInfo['xyh'],0);
					} else {
						$userInfo = array('username'=>$uid,'realname'=>trim($uid),'client'=>0,'register_time'=>time(),'uzone_token'=>'', 'qd'=>$qd);
						$result = userModel::saveUserInfo($userInfo);
						
						$appid = 9;
							
						$seqnum = 'QJSH' . date('ymdHis') . rand(1, 9999999);
						//$token = $mc->get(MC.$username.'_token');
						$token = $qq_token;
						$pro = "{'seqnum':'$seqnum', 'func':'gnn'}";
							
						$servernum = rand(0, 1);
						$serverIp = $payServer[$servernum]['server'];
						$serverPort = $payServer[$servernum]['port'];
							
						$fp = fsockopen($serverIp, $serverPort, $errno, $errstr, 1000);
						$header = "GET /appid=$appid HTTP/1.1\r\n";
						$header .= "Host:localhost \r\n";
						$header .= "Content-Length: ".strlen($pro)."\r\n";
						$header .= "Content-Type: text/html \r\n";
						$header .= "X-TOKEN: $token\r\n";
						$header .= "Connection: Close\r\n\r\n";
						$header .= $pro."\r\n";
						socket_set_timeout($fp, 1);
						fwrite($fp, $header);
							
						$str = null;
						$startTime = microtime(true);
						while(!feof($fp))
						{
							$str .= fgets($fp, 1024);
								
							$endTime = microtime(true);
							if(($endTime - $startTime) * 1000 > 200) {
								$startPos = strrpos($str, "Content-Length");
								$len = strlen('Content-Length: ');
								$endPos = strrpos($str, "Content-Type");
								$len = substr($str, $startPos + $len, $endPos - ($startPos + $len));
								$cStartPos = strrpos($str, "{");
								$cEndPos = $endPos = strrpos($str, "}");
								$finalLen = strlen(substr($str, $cStartPos, $cEndPos - $cStartPos + 1));
								if($len == $finalLen) break;
							}
						}
						fclose($fp);
						$str = str_replace('\r\n\r\n', '', stristr($str, "{ "));
						$result = json_decode($str);
							
						$qqnickname = $user_lang['register_2'];
						if(is_object($result)) {
							if($result->code == 1) {
								$qqnickname = $result->nickname;
							}
						}
						
						$userInfoflash = array('username'=>$uid,'realname'=>trim($uid),'client'=>0,'register_time'=>time(),'uzone_token'=>'','inviteid'=>'','sexid'=>0,'nc'=>$qqnickname);
						userModel::flashUserLogin($userInfoflash);
						userController::authorize($userInfoflash,$userInfo['xyh'],0);
					}
				}
			} else {
				//flash编译环境下登陆
				$result = userModel::saveUserInfo($userInfo);
				//userModel::flashUserLogin($userInfo);
				userController::authorize($userInfo,$userInfo['xyh'],0);
			}
			//return true;
		}  elseif($userInfo['client'] == 2) {
				$token = $userInfo['uzone_token'];		
				$openid = $userInfo['username'];
				
				$user = 'admin';
				$pass = 'admin';
				//$ip = $_SERVER['SERVER_ADDR'];
				$ip = $_SC['domain'];
				
				$auth = new authentication_header($user, $pass, $ip);
				$authvalues = new SoapVar($auth, SOAP_ENC_OBJECT, 'authenticate');
				$header = new SoapHeader(WS_SERVER, 'authenticate', $authvalues);
				
				$client = new SoapClient(null,array('location' =>USER_SERVER . "authserver/server.php", 'uri' => "http://127.0.0.1/"));
				$client->__setSoapHeaders(array($header));
				try {
					$ret = $client->test($token,2);
				} catch(Exception $e) {
					$returnValue['status'] = 3; 
				   	$returnValue['message'] = $user_lang['register_3'];
   					if ($_SC['status'] == 0 && !in_array($getUserid,$allowuser)) {
						$returnValue ['gv'] = '1.0';
   					} else {
				   		$returnValue ['gv'] = $_SC ['swf_version'];
   					}
					$returnValue['vipmode'] = VIP_CREDITS_CONTROL;
				   	ClientView::show($returnValue);
				   	exit;
				}
				if($ret === 3) {
					$returnValue['status'] = 3; 
				   	$returnValue['message'] = $user_lang['register_4'];
   					if ($_SC['status'] == 0 && !in_array($getUserid,$allowuser)) {
						$returnValue ['gv'] = '1.0';
   					} else {
				   		$returnValue ['gv'] = $_SC ['swf_version'];
   					}
					$returnValue['vipmode'] = VIP_CREDITS_CONTROL;
				   	ClientView::show($returnValue);
				   	exit;
				} else {
					if (is_array($ret)) {
						$uid = $ret['uid'];
						$nc = $ret['userName2'];
						$sex = $ret['sex'];						
					} else {
						$uid = $ret;
						$nc = '';
						$sex = '';
					}
				}
				
				$pt = 'android';
				//$uid = $openid;
				$kszc = _get('kszc');
				if(userModel::isPlayerExists($uid)) {
					$userInfo = array('username'=>$uid,'realname'=>'','client'=>2,'register_time'=>$nowTime,'uzone_token'=>'', 'qd'=>$qd, 'register_date'=>$nowDate);
					$result = userModel::saveUserInfo($userInfo);
					$userInfoflash = array('username'=>$uid,'realname'=>$nc,'nc'=>$nc,'client'=>2,'register_time'=>$nowTime,'uzone_token'=>'','inviteid'=>'','sexid'=>$sex);
					//userModel::flashUserLogin($userInfoflash);
					userController::authorize($userInfoflash,$userInfo['xyh'],0);
				} else {
					$userInfo = array('username'=>$uid,'realname'=>trim($uid),'client'=>2,'register_time'=>$nowTime,'uzone_token'=>'', 'qd'=>$qd, 'register_date'=>$nowDate, 'kszc'=>$kszc);
					$result = userModel::saveUserInfo($userInfo);
					if ($kszc == 1) {
						$roleInfo['nickname'] = $nc;
						$roleInfo['inviteid'] = 0;
						$roleInfo ['phone'] = 0;
						$roleInfo ['sex'] = rand(0,1);
						$roleInfo ['ucid'] = $userInfo['username'];
						$roleInfo['userid'] = $userInfo['info']['userid'];
						$_SESSION ['client'] = 2;
						$_SESSION ['debug'] = 0;
						roleModel::saveRoleInfo($roleInfo);
				    }					
					$userInfoflash = array('username'=>$uid,'realname'=>$nc,'nc'=>$nc,'client'=>2,'register_time'=>$nowTime,'uzone_token'=>'','inviteid'=>'','sexid'=>$sex);
					//userModel::flashUserLogin($userInfoflash);
					userController::authorize($userInfoflash,$userInfo['xyh'],0);
				}
			} elseif($userInfo['client'] == 3) {	
				$value9y['ucid'] = $userInfo['username'];				
																		
				if(userModel::isPlayerExists($value9y['ucid'])) {
					$userInfo = array('username'=>$value9y['ucid'],'realname'=>'','client'=>3,'register_time'=>$nowTime,'uzone_token'=>'', 'qd'=>$qd, 'register_date'=>$nowDate);
					$result = userModel::saveUserInfo($userInfo);
					$userInfoflash = array('username'=>$value9y['ucid'],'realname'=>'','client'=>3,'register_time'=>$nowTime,'uzone_token'=>'','inviteid'=>'','sexid'=>'');
					//userModel::flashUserLogin($userInfoflash);
					userController::authorize($userInfoflash,$userInfo['xyh'],0);
				} else {
					$userInfo = array('username'=>$value9y['ucid'],'realname'=>trim($value9y['ucid']),'client'=>3,'register_time'=>$nowTime,'uzone_token'=>'', 'qd'=>$qd, 'register_date'=>$nowDate);
					$result = userModel::saveUserInfo($userInfo);
					$nameRet = getFirstName();
					$userInfoflash = array('username'=>$value9y['ucid'],'realname'=>trim($value9y['ucid']),'client'=>3,'register_time'=>$nowTime,'uzone_token'=>'','inviteid'=>'','sexid'=>0, 'boy'=>$nameRet['boy'], 'girl'=>$nameRet['girl']);
					//userModel::flashUserLogin($userInfoflash);
					userController::authorize($userInfoflash,$userInfo['xyh'],0);
				}
			} else if($userInfo['client'] == 4) { //uc平台
				$b = _get('bs');
				if(!empty($b)) {				        
					$uzone_token = $_SESSION['hero_uc_token'];
					$uid = $_SESSION['hero_uc_uid'];
					$inviteid = null;//$_SESSION['hero_uc_inviteid'];					
					if(userModel::isPlayerExists($uid)) {							
						$userInfo = array('username'=>trim($uid),'realname'=>'','client'=>4,'register_time'=>$nowTime,'uzone_token'=>$uzone_token, 'qd'=>$qd, 'register_date'=>$nowDate);
						$result = userModel::saveUserInfo($userInfo);
						$userInfoflash = array('username'=>trim($uid),'realname'=>'','client'=>4,'register_time'=>$nowTime,'uzone_token'=>$uzone_token,'inviteid'=>$inviteid,'sexid'=>'', 'nc'=>"");
						userController::authorize($userInfoflash, $userInfo['xyh'], 0);
					} else {
						$arr['uids'] = intval(trim(str_replace('2_', '', $uid)));
						$UzoneRestApi = new UzoneRestApi($uzone_token);
						$res = $UzoneRestApi->callMethod('user.getInfo', $arr);
						if ($UzoneRestApi->checkIsCallSuccess()) {
							$arr = $res;
							if(count($arr)>0) {
								if($arr[0]['sex'] == 0){
									$sexid = 0;
								}elseif($arr[0]['sex'] == 1){
									$sexid = 0;
								}elseif($arr[0]['sex'] == 2){
									$sexid = 1;
								}	
							}
						} else {
							$returnValue['status'] = 3;
							$returnValue['message'] = $user_lang['register_5'];
							$returnValue['sydz'] = 'http://u.uc.cn/?uc_param_str=sspfligiwinieisive&r=app/redirect&appId=228&s1=i_g_recent';
							$returnValue['rsn'] = intval(_get('ssn'));
							$returnValue['vipmode'] = VIP_CREDITS_CONTROL;
							ClientView::show($returnValue);
							return false;									
						}							
						$userInfo = array('username'=>trim($uid),'realname'=>trim($arr[0]['real_name']),'client'=>4,'register_time'=>$nowTime,'uzone_token'=>$uzone_token, 'qd'=>$qd, 'register_date'=>$nowDate);
						$result = userModel::saveUserInfo($userInfo);
						
						$userInfoflash = array('username'=>trim($uid),'realname'=>trim($arr[0]['real_name']),'client'=>4,'register_time'=>$nowTime,'uzone_token'=>$uzone_token,'inviteid'=>$inviteid,'sexid'=>$sexid,'nc'=>trim($arr[0]['real_name']));
						userController::authorize($userInfoflash, $userInfo['xyh'], 0);
					}
				}
			}  else if($userInfo['client'] == 5) { //QQ游戏中心
				$b = _get('bs');
				if(!empty($b)) {		
					$verify_info = explode('\n', urldecode(_get('sid')));
					$opent_openid = stripslashes($verify_info[0]);
					$opent_token = stripslashes($verify_info[1]);
					$opent_token_secret = stripslashes($verify_info[2]);
					$token_info = $opent_token . '|' . $opent_token_secret;
					
					$inviteid = null;				
					if(userModel::isPlayerExists($opent_openid)) {							
						$userInfo = array('username'=>trim($opent_openid),'realname'=>'','client'=>5,'register_time'=>$nowTime,'uzone_token'=>$token_info, 'qd'=>$qd, 'register_date'=>$nowDate);
						$result = userModel::saveUserInfo($userInfo);
						$userInfoflash = array('username'=>trim($opent_openid),'realname'=>'','client'=>5,'register_time'=>$nowTime,'uzone_token'=>$token_info,'inviteid'=>$inviteid,'sexid'=>'', 'nc'=>"");
						userController::authorize($userInfoflash, $userInfo['xyh'], 0);
					} else {
						/*/define("MB_AKEY","GDdmIQH6jhtmLUypg82g");
						define("MB_SKEY","MCD8BKwGdgPHvAuvgvz4EQpqDAtx89grbuNMRd7Eh98");
						define("APP_ID", '1'); 
						define("MB_API_HOST", 'openapi.sp0309.3g.qq.com');*/

						/**/define("MB_AKEY","O1hX2A586A0F348Mo5LI");
						define("MB_SKEY","6RbVJipgiGtZ1Qn0");
						define( "APP_ID" , '197' ); 
						define( "MB_API_HOST" , 'openapi.3g.qq.com' );

						require_once dirname(dirname(dirname(__FILE__))) .'/includes/opent.php';

						$URI = 'http://'.MB_API_HOST;
						$URL_PEOPLE_SELF = $URI.'/people/';
						$customKey     = MB_AKEY;
						$customSecrect = MB_SKEY;
						$tokenKey = $opent_token;
						$tokenSecrect = $opent_token_secret;

						$url = $URL_PEOPLE_SELF.'@me/@self';

						$fields = "id,nickname,gender"; // 获取的信息字段
						$format = OpenApiSdk::SIMPLEJSON_FORMAT;

						$_cc = new MBOpenTOAuth($customKey, $customSecrect, $tokenKey, $tokenSecrect);
						$params = array('fields'=>$fields, 'format'=>$format, 'appId'=>APP_ID);
						$_r = $_cc->get($url,$params);

						$httpCode = $_r[MBOpenTOAuth::httpCodeStr];
						$httpRsp = $_r[MBOpenTOAuth::httpContentStr];

						if($httpCode == 200) {
							$info = json_decode($httpRsp, true);
							$nick_name = $info['entry'][2];
							$sex = $info['entry'][7];
							if($sex == 'male'){
								$sexid = 0;
							}else{
								$sexid = 1;
							}	
						} else {
							$returnValue['status'] = 3;
							$returnValue['message'] = $user_lang['register_6'];
							$returnValue['sydz'] = ''; // 待添加
							$returnValue['rsn'] = intval(_get('ssn'));
							$returnValue['vipmode'] = VIP_CREDITS_CONTROL;
							ClientView::show($returnValue);
							return false;					
						}
						
						$userInfo = array('username'=>trim($opent_openid),'realname'=>trim($nick_name),'client'=>5,'register_time'=>$nowTime,'uzone_token'=>$token_info, 'qd'=>$qd, 'register_date'=>$nowDate);
						$result = userModel::saveUserInfo($userInfo);
						
						$userInfoflash = array('username'=>trim($opent_openid),'realname'=>trim($nick_name),'client'=>5,'register_time'=>$nowTime,'uzone_token'=>$token_info,'inviteid'=>$inviteid,'sexid'=>$sexid,'nc'=>trim($nick_name));
						userController::authorize($userInfoflash, $userInfo['xyh'], 0);
					}
				}
			} else {
				$result = userModel::saveUserInfo($userInfo);		
		        if ($result == 4) {
				   $returnValue['status'] = 4; 
				   $returnValue['message'] = $user_lang['register_7'];			
			    } elseif ($result == 3) {
				   $returnValue['status'] = 21;
				   $returnValue['message'] = $user_lang['register_8']; //用户名或者密码为空
			    } elseif ($result == 2) {
				   $returnValue['status'] = 30; 
				   $returnValue['message'] = $user_lang['register_9'];         //用户已经存在
			    } else {
				   //$returnValue['status'] = 0;                      //返回注册成功状态			
				   userController::authorize($userInfo,$userInfo['xyh'],1);
				   return true;
				   //return true;
			    }
				if ($_SC['status'] == 0 && !in_array($getUserid,$allowuser)) {
					$returnValue ['gv'] = '1.0';
				} else {			    
			    	$returnValue ['gv'] = $_SC ['swf_version'];	
				}
			    $returnValue['AppStoreURL'] = $_SC['AppStoreURL'];
			    $returnValue['rsn'] = intval(_get('ssn'));
				$returnValue['vipmode'] = VIP_CREDITS_CONTROL;
				$returnValue ['sgpayurl'] = $_SC['sgpayurl'];
				$returnValue ['brpayurl'] = $_SC['brpayurl'];
				$returnValue ['email'] = $_SC['sgemail'];
				$returnValue ['jfpay'] = $_SC['jfpay'];
				$brzf = userModel::brzf($userInfo['userid'],$userInfo['username']);
				if (!empty($brzf)) {
					$returnValue['brzf'] = $brzf;
				}
				$qd = _get('qd');
				if ($qd == 'mbg1_1') {
					$returnValue['mgbzf'] =  'http://'.$_SERVER['HTTP_HOST'].'/pay/denapay.php';
				}	
				$returnValue['lbhd'] = intval($_SC['lbhd']);
				$returnValue['ltdz'] = $_SC['ltdz'];								
				$returnValue ['sggg'] = $_SC['sggg'];
				$returnValue ['zfzx'] = $_SC['zfzx'];
				$returnValue ['krpayurl'] = 'http://'.$_SERVER['HTTP_HOST'].'/pay/hfpay.php';
			    ClientView::show($returnValue);
		}
	}
	
	//用户登录
	static function authorize(&$userInfo,$xyh=0,$client=1) {
		global $_SC, $mc, $db, $user_lang, $allowuser;
		$serverInfo = '';
		$is_reason = false;
		$userInfo['userid'] = 0;
		if ($client == 1) {
			$result = userModel::getLoginInfoForJava($userInfo,$serverInfo);
		} else {
			$result = userModel::getLoginInfo($userInfo,$serverInfo);
		}
		if ($result == 4) {
			$returnValue['status'] = 3; 
			$returnValue['message'] = $user_lang['authorize_1'];
		} elseif ($result == 3) {
			$returnValue['status'] = 29; //账号不存在
			$returnValue['message'] = $user_lang['authorize_2'];
		} elseif ($result == 2) {
			$returnValue['status'] = 29; //密码错误
			$returnValue['message'] = $user_lang['authorize_3'];
		} else {
			if($userInfo['client'] == 4) {
				$returnValue['czdz'] = $_SC['backUrl'] . '/components/uc/php/index.php';
				$returnValue['uzone_token'] = $_SESSION['hero_uc_token'];
				$returnValue['platform'] = 'uc';
				if($userInfo['sexid'] !== '') $returnValue['sex'] = $userInfo['sexid'];
				$returnValue['sydz'] = 'http://u.uc.cn/?uc_param_str=sspfligiwinieisive&r=app/redirect&appId=228&s1=i_g_recent';
			} else if($userInfo['client'] == 3) {
				$returnValue['platform'] = '9y';
				$returnValue['sydz'] = 'http://qjsh.9game.cn';
				if(isset($userInfo['boy']) && isset($userInfo['girl'])) {
					$returnValue['boy'] = $userInfo['boy'];
					$returnValue['girl'] = $userInfo['girl'];
				}
			} else if($userInfo['client'] == 5) {
				$returnValue['sex'] = $userInfo['sexid'];
			} else {
				$returnValue['czdz'] = $_SC['chongzhidizhi'];
				$returnValue['sydz'] = $_SC['shouyedizhi'];
			}
			
			$returnValue['cv'] = $_SC['cv'];
			
			if(SYPT == 1) { // 支付宝充值地址
				$returnValue['zfbdz'] = $_SC['tenpay_callback'];
				$returnValue['paycardurl'] = $_SC['paycardurl'];
			}
						
			if($userInfo['client'] == 2) {
				$returnValue['zfbdz'] = $_SC['alipay_callback_self'];
				$returnValue['paycardurl'] = $_SC['paycardurl'];
			}
			
			$returnValue['iosczdz'] = $_SC['iosczdz'];			
			$returnValue['kfcz'] = $_SC['kaifangchongzhi'];
			$qd = _get('qd');
			$qdbb = qdbb($qd);
			if (isset($qdbb['swf_version'])) {
				$_SC['swf_version'] = $qdbb['swf_version'];
			}			
			if ($qd === 'az1_1') {
				$returnValue['fkdz'] = 'http://bbs.anzhi.com/forum-1041-1.html';
			} else {
				$returnValue['fkdz'] = $_SC['fankuidizhi'];
			}
			
			$returnValue['status'] = 0;                                        //返回注册成功状态
			$returnValue['userId'] = intval($userInfo['userid']);                      //返回用户id
			//$returnValue['password'] = base64_encode($userInfo['password']); //返回密码	
			//$returnValue['servers'] = $serverInfo;                             //返回服务器列表信息
			//$keyvalue = uniqid(rand(15,20));
			//$returnValue['sessionId'] = session_id();	
			$returnValue['inviteId'] = $userInfo['inviteid'];	
			//$_SESSION['keyvalue'] = $keyvalue;		
			$_SESSION['userid'] = $userInfo['userid'];	
			$_SESSION['client'] = $userInfo['client'];
			$_SESSION['uzone_token'] = $userInfo['uzone_token'];
			$_SESSION['inviteid'] = $userInfo['inviteid'];
			//$_SESSION['realname'] = $userInfo['realname'];
			$_SESSION['realname'] = $userInfo['username'];
			$_SESSION['sexid'] = $userInfo['sexid'];			
			$_SESSION['ucid'] = $userInfo['username'];
			$returnValue['servers'] = $serverInfo;                             //返回服务器列表信息
			//session_id(uniqid(rand(15,20)));PHPSESSID
			
			$mc->delete(MC.'UserLastRequest_'.$userInfo['userid']);            // 避免用户登录出错刷新后在其他地方登录问题
			
			// 端游激活码状态判断			
			//if($userInfo['client'] == 2 || $userInfo['client'] == 3) {
				if(NEED_ACTIVATE == 1 && $userInfo['isActivated'] == 0) { // 需要用户激活操作				
					if (!($activateFaild = $mc->get(MC.'activateFaild'))) {
						$activateFaild = $db->fetch_array($db->query('select notice from ol_notice where id = 3'));
						$mc->set(MC.'activateFaild', $activateFaild, 0, 0);
					}
					$returnValue['status'] = 1001;
					$returnValue['message'] = $activateFaild['notice'];
					$mc->set(MC.$userInfo['userid'] . '_realsession', session_id(), 0, 1440);
					//heroCommon::insertLog($mc->get(MC.$userInfo['username'] . '_realsession'));
				} else { // 不需要用户激活操作时返回loginkey
					$returnValue['loginKey'] = session_id();
				}				
			//}				
			//else {
			//	$returnValue['loginKey'] = session_id();				
			//}
			
			// 判断是否有是合服并且有否多个角色
			if(SYPT == 1 || $userInfo['client'] == 4 || $userInfo['client'] == 5 || isset($userInfo['nc'])) {
				$yhnc = $userInfo['nc'];
			}
			if(SYPT == 1) {
				$returnValue['jsxx'] = array(0=>array('userId'=>intval($userInfo['userid']), 'nc'=>$yhnc));
			} else {
				$result = mysql_query("SELECT * FROM ol_player WHERE ucid = '".$_REQUEST['userId']."'");
				$rows_num = mysql_num_rows($result);
				if($rows_num > 1) { // 有多角色
					while($rows[] = mysql_fetch_array($result, MYSQL_ASSOC));
					array_pop($rows);
					/*$other_plist = array();
					foreach($rows as $k1=>$v1) $other_plist[] = $v1['playersid'];
					$other_pinfo_tmp = roleModel::getAllRolesInfo($other_plist);
					foreach($other_pinfo_tmp as $k2=>$v2) $other_pinfo_tmp[$v2['playersid']] = $v2;*/
					$jsxx = array();
					foreach($rows as $key=>$value) {
						$jsxx[] = array('userId'=>intval($value['userid']), 'nc'=>$value['nickname'], 'xb'=>intval($value['sex']), 'dj'=>intval($value['player_level']), 'vip'=>intval($value['vip']));
					}
					$returnValue['jsxx'] = $jsxx;
					$_SESSION['hf_userid'] = json_encode($jsxx);
				} else { // 来自被合服无多角色
					if($rows_num == 1) {
						$rows = mysql_fetch_array($result, MYSQL_ASSOC);
						$returnValue['jsxx'] = array(0=>array('userId'=>intval($rows['userid'])));
						$returnValue['userId'] = intval($rows['userid']);
						$_SESSION['hf_userid'] = json_encode($returnValue['jsxx']);
					} else {
						$returnValue['jsxx'] = array(0=>array('userId'=>intval($userInfo['userid'])));
					}
				}
			}
			
			//heroCommon::insertLog(json_encode($returnValue));
			
			$returnValue['realname'] = $userInfo['realname'];
			//$returnValue['sex'] = intval($userInfo['sexid']);
			if($userInfo['sexid'] != '')
				$returnValue['sex'] = $userInfo['sexid'];
			//$returnValue['token'] = $_SESSION['uzone_token'];
			$returnValue['xyh'] = $xyh;
			if($userInfo['inviteid'] <> "") {
				heroCommon::writeucfriends($_SERVER['REQUEST_URI']."&inviteid=".$userInfo['inviteid'],json_encode($returnValue));
			}
			if($userInfo['is_reason'] == 1) {
				$is_reason = true; 
			}
		}
		//print_r($returnValue);
		//file_get_contents();
		$returnValue['rsn'] = intval(_get('ssn'));
		if($is_reason) {
			$returnValue = array();
			$returnValue['status'] = 555;
			$returnValue['message'] = $user_lang['authorize_4'].$userInfo['reason_memo'].$user_lang['authorize_5'];
			$returnValue['rsn'] = intval(_get('ssn'));
		}
		
		//heroCommon::insertLog(json_encode($returnValue));
		$is_test = intval(_get('debug'));
		$returnValue ['kszc'] = intval($userInfo['kszc']);
		$getUserid = _get('userId');
		if ($_SC['status'] == 0 && !in_array($getUserid,$allowuser)) {
			$returnValue ['gv'] = '1.0';
		} else {	
	   		$returnValue ['gv'] = $_SC ['swf_version'];
		}
	   	$returnValue['AppStoreURL'] = $_SC['AppStoreURL'];
	   	$returnValue['fwqdm'] = $_SC['fwqdm'];
	   	$returnValue['tips'] = '';
	   	$returnValue['tompay'] = $_SC['TOM_NOTICE_URL'];
		$returnValue['vipmode'] = VIP_CREDITS_CONTROL;
		$returnValue['helpdocumenturl'] = $_SC['helpdocumenturl'];
		$returnValue ['sgpayurl'] = $_SC['sgpayurl'];
		$returnValue ['brpayurl'] = $_SC['brpayurl'];
		$returnValue ['email'] = $_SC['sgemail'];
		$returnValue ['jfpay'] = $_SC['jfpay'];
		$brzf = userModel::brzf($userInfo['userid'],$userInfo['username']);
		if (!empty($brzf)) {
			$returnValue['brzf'] = $brzf;
		}
		if ($qd == 'mbg1_1') {
			$returnValue['mgbzf'] =  'http://'.$_SERVER['HTTP_HOST'].'/pay/denapay.php';
		}		
		$returnValue ['sggg'] = $_SC['sggg'];	
		$returnValue ['zfzx'] = $_SC['zfzx'];
		$returnValue ['krpayurl'] = 'http://'.$_SERVER['HTTP_HOST'].'/pay/hfpay.php';
		$returnValue['lbhd'] = intval($_SC['lbhd']);
		$returnValue['ltdz'] = $_SC['ltdz'];			
		ClientView::show($returnValue,$is_test);
	}
	
	//Flash客户端用户登录
	static function flashUserLogin($userInfo) {		
		global $user_lang;
		userModel::flashUserLogin($userInfo);
		$serverInfo = '';
		$result = userModel::getLoginInfo($userInfo,$serverInfo);
		if ($result == 4) {
			$returnValue['status'] = 3; 
			$returnValue['message'] = $user_lang['register_7'];			
		} elseif ($result == 3) {
			$returnValue['status'] = 29; //账号不存在
			$returnValue['message'] = $user_lang['authorize_2'];
		} elseif ($result == 2) {
			$returnValue['status'] = 29; //密码错误
			$returnValue['message'] = $user_lang['authorize_3'];
		} else {
			$returnValue['status'] = 0;                                        //返回注册成功状态
			$returnValue['userid'] = intval($userInfo['userid']);                      //返回用户id
			//$returnValue['password'] = base64_encode($userInfo['password']); //返回密码	
			$returnValue['servers'] = $serverInfo;                             //返回服务器列表信息
			//$keyvalue = uniqid(rand(15,20));
			$returnValue['sessionId'] = session_id();	
			//$_SESSION['keyvalue'] = $keyvalue;		
			$_SESSION['userid'] = $userInfo['userid'];	
			$_SESSION['client'] = $userInfo['client'];
			$_SESSION['uzone_token'] = $userInfo['uzone_token'];
			$_SESSION['username'] = $userInfo['username'];
			//print_r($_SESSION);
			//session_id(uniqid(rand(15,20)));
		}
		$result['rsn'] = intval(_get('ssn'));		
        ClientView::show($result);
	}
	
	// 端游提交验证码
	static function tjyzm() {
		$userId = _get('userId');
		$activate = _get('yzm');
		
		$result = userModel::tjyzm($userId, $activate);
		ClientView::show($result);
	}
	
	//调试部分
	static function checksid() {
		$sid = _get('sid');
		$result = userModel::checksid($sid);	
		ClientView::show($result);
	}
    //修改密码
    /*function chagePassWord(&$userInfo) {
		$result = userModel::chagePassWord($userInfo);		
    	if ($result == 2) {
    		ClientView::show('用户名不存在');
    	} elseif ($result == 3) {
    		ClientView::show('输入的旧密码不正确');
    	} elseif ($result == 4) {
    		ClientView::show('密码不能为空');
    	} else {
    		ClientView::show('密码修改成功');
    	}
    }*/  
	//修改快速注册用户信息
	public static function xgksdl() {
		$userid = _get('userId');
		$newname = _get('userName');
		$password = _get('password');
		$phone = _get('phone');
		$nickname = _get('nickname');
		$result = userModel::xgksdl($userid,$newname,$password,$phone,$nickname);	
		ClientView::show($result);		
	}  
}
