<?php 
class userModel {
	//检查用户名是否存在
	static function isUserExists($userName,$client,&$getUserInfo='') {
		global $db,$common;	
		switch ($client) {
			case 0:
				 $item = "&& client = '0'"; //flash客户端请求
				 break;
			case 1:
				 $item = "&& client = '1'"; //java版
				 break;
		    case 2:
				 $item = "&& client = '2'"; //iphone版
				 break;	
			case 3:
				 $item = "&& client = '3'"; //android版
				 break;	
			default:
				 $item = "";
				 break;						    	 
		} 
		$result = $db->query("SELECT * FROM ".$common->tname('user')." WHERE `username` = '$userName' LIMIT 1");
		$number = $db->num_rows($result);	
		if ($client == 0 || $client == 2) {
			$getUserInfo = $db->fetch_array($result);
		}	
        if ($number > 0) {
            return true;
        } else {
        	return false;
        }        
	}
	
	//验证用户是否存在
	static function isPlayerExists($ucid) {
		global $db,$common;	
		$result = $db->query("SELECT count(*) as icount FROM ".$common->tname('player')." WHERE `ucid` = '$ucid' ");
		$number = $db->fetch_array($result);	
        if ($number['icount'] > 0) {
            return true;
        } else {
        	return false;
        }        
	}
	 
	//检查用户提交的信息是否合法，比如是否有空值等
	static function checkUserInfo($userInfo) {
		if (empty($userInfo['username']) OR ($userInfo['client'] != 0 && $userInfo['client'] != 1 && $userInfo['client'] != 2 && $userInfo['client'] != 3 && $userInfo['client'] != 4 && $userInfo['client'] != 5)) {
			return false; 
		}
		return true;
	}
	
	/*static function _get($str){
		$magic_quote = get_magic_quotes_gpc();
		if (isset($_REQUEST[$str]))	{
			if (empty($magic_quote)) {
				$val = addslashes($_REQUEST[$str]);
			} else {
				$val = $_REQUEST[$str]; 
			}		
		} else {
			$val = null;
		}	
	    //$val = isset($_GET[$str]) ? $_GET[$str] : null;
	    return $val;
	}*/
	
	//获取uzone_token
	static function getUzone_token($userName) {
		global $db,$common;	
		$result = $db->query("SELECT `uzone_token` FROM ".$common->tname('user')." WHERE `username` = '".$userName."' LIMIT 1");
		$token = $db->fetch_array($result);
		//print_r($token['uzone_token']);
		if($token['uzone_token'] <> "") {
			return $token['uzone_token'];
		}
	}

	//将用户数据存入数据库
	static function saveUserInfo(&$userInfo) {		
		global $common, $user_lang;
		if (!isset($userInfo['client']) || $userInfo['client'] < 0 || $userInfo['client'] > 5) {
			return 4;   //非法的客户端请求
		}		
		$isUserInfoPass = userModel::checkUserInfo($userInfo);
		if (!empty($isUserInfoPass)) {			   
		   $isUserExists = userModel::isUserExists($userInfo['username'],$userInfo['client'],$userInfo['info']);		
		   if (empty($isUserExists)) {		   	
			   if($userInfo <> '' && userModel::checkGameNum() == true) {
			   		$returnValue['status'] = 13; 
			   		$returnValue['message'] = $user_lang['saveUserInfo_1'];	
			   		$returnValue['rsn'] = intval(_get('ssn'));
			   		ClientView::show($returnValue);
					exit;
			   }
			   unset($userInfo['info']);   
			   $userid = $common->inserttable('user',$userInfo);	
			   $userInfo['xyh'] = 1; 
			   $userInfo['info']['userid'] = $userid;
			   /*if (!empty($userid)) {
			   	  $serverInfo = '';
			   	  userModel::getLoginInfo($userInfo,$serverInfo);
			   }*/
			   return 0;
		   } else  {
		   	   $common->updatetable('user',"uzone_token ='".$userInfo['uzone_token']."'","username = '".$userInfo['username']."'");
			   $userInfo['xyh'] = 0;
		   	   return 2;
		   }
		} else {
			return 3;
		}
	}
	
	//Flash客户端用户登录
	static function flashUserLogin(&$userInfo) {
		global $common;
		$userInfo['client'] = 0;                             //客户端为flash
		$userInfo['username'] = $userInfo['username'];       //随机给数据库密码字段分配值
		//if ($userInfo['username'] == '_f') {
			//return 0;   //非法用户名
		//}
	    $getUserInfo = 1;
		$isUserExists = userModel::isUserExists($userInfo['username'],0,$getUserInfo);	
		//echo($isUserExists);
		if (empty($isUserExists)) {
			$userInfo['password'] = rand(10000,90000);           //随机给数据库密码字段分配值
			$userid = $common->inserttable('user',$userInfo);			    
			//return $userid; //插入值成功,但无角色		    	
		} else {
		    $userid = $getUserInfo['userid'];	
		    $userInfo['password'] = $getUserInfo['password'];
		    //print_r($getUserInfo); 	    
		    //$roleInfo = userModel::getRoleInfo('http://hero.iguodong.com',$userid);
		    //return $userid; //用户名存在,并且有角色    	
		}
		//$userInfo['password'] = base64_encode($userInfo['password']);		
	}
		
	//查询登录信息
	static function getLoginInfo(&$userInfo,&$serverInfo) {
		global $common,$db,$mc;
		$nowTime = time();
		$result = $db->query("SELECT * FROM ".$common->tname('user')." WHERE username = '".$userInfo['username']."' LIMIT 1");
		$rows = $db->fetch_array($result);
		if (!isset($userInfo['client']) || $userInfo['client'] < 0 || $userInfo['client'] > 5) {
			return 4;   //非法的客户端请求
		}
		if (empty($rows)) {
			//heroCommon::insertLog("wy--------SELECT userid,username,mobile,last_login_time,password,is_reason,reason_memo FROM ".$common->tname('user')." WHERE username = '".$userInfo['username']."' LIMIT 1");
			return 3; //用户不存在
		} else {
			/*if (strtolower($rows['password']) != strtolower($userInfo['password'])) {
				//echo $userInfo['password'];
				return 2; //密码错
			} else {*/	   
				if (date('Y-m-d',$rows['last_login_time']) == date('Y-m-d',$nowTime)) {
					$_SESSION['qxyt'] = 0;  //是否新的一天
				} else {
					$_SESSION['qxyt'] = 1;  //是否新的一天
				}	
				$_SESSION['loginTime'] = $nowTime;			
				$mc->set(MC."last_login_time_".$rows['userid'], $rows['last_login_time'], 0, 0);
			    $db->query("UPDATE ".$common->tname('user')." SET last_login_time = '".$nowTime."' WHERE userid = '".$rows['userid']."'");
				$userInfo['mobile'] = $rows['mobile']; 	
			    $userInfo['last_login_time'] = $rows['last_login_time'];
			    $userInfo['userid'] = $rows['userid'];
			    $userInfo['username'] = $rows['username'];
			    $userInfo['is_reason'] = $rows['is_reason'];
			    $userInfo['reason_memo'] = $rows['reason_memo'];
			    $userInfo['isActivated'] = $rows['isActivated'];
			    $userInfo['kszc'] = intval($rows['kszc']);
			    /*$resultServerList = $db->query("SELECT * FROM ".$common->tname('serverlist'));
			    while ($rowsServerList = $db->fetch_array($resultServerList)) {
				    $serverName = $rowsServerList['server_name'];     //服务器名				    
				    $roleInfo = userModel::getRoleInfo($rowsServerList['server_url'],$rows['userid']);		
				    if (!empty($roleInfo['playersid']))	{
				    	$_SESSION['playersid'] = $roleInfo['playersid'];
				    }	    
				    if ($roleInfo['playersid']) {
				    	$returnServerInfo['status'] = 1;                      //服务器状态
				    } else {
				    	$returnServerInfo[] = 2;                              //服务器状态
				    }				       				    
				    $roleName = !empty($roleInfo['nickname'])?$roleInfo['nickname']:'无角色';                         //角色名
				    //$returnServerInfo[] = $roleInfo['playersid']?$roleInfo['playersid']:0;           //角色ID					    
				    $url = $rowsServerList['server_url'];  //服务器URL				    	
			        $returnServerInfo[] = array('serverName'=>$serverName,'roleName'=>$roleName,'url'=>$url);
			    }	
		        $serverInfo = $returnServerInfo;*/
			//}
			return 0;
		}
	}
	
	//查询登录信息
	static function getLoginInfoForJava(&$userInfo,&$serverInfo) {
		global $common,$db,$user_lang;
		$result = $db->query("SELECT userid,username,mobile,last_login_time,password,is_reason,reason_memo FROM ".$common->tname('user')." WHERE username = '".$userInfo['username']."' LIMIT 1");
		$rows = $db->fetch_array($result);
		if (!isset($userInfo['client']) || $userInfo['client'] < 0 || $userInfo['client'] > 5) {
			return 4;   //非法的客户端请求
		}
		if (empty($rows)) {
			return 3; //用户不存在
		} else {
			if (strtolower($rows['password']) != strtolower($userInfo['password'])) {
				//echo $userInfo['password'];
				return 2; //密码错
			} else {		    
			    $db->query("UPDATE ".$common->tname('user')." SET last_login_time = '".time()."' WHERE userid = '".$rows['userid']."'");
				$userInfo['mobile'] = $rows['mobile']; 	
			    $userInfo['last_login_time'] = $rows['last_login_time'];
			    $userInfo['userid'] = $rows['userid'];
			    $userInfo['username'] = $rows['username'];
			    $_SESSION['ucid'] = $rows['username'];
			    $_SESSION['client'] = 1;
			    $userInfo['is_reason'] = $rows['is_reason'];
			    $userInfo['reason_memo'] = $rows['reason_memo'];
			    $resultServerList = $db->query("SELECT * FROM ".$common->tname('serverlist'));
			    while ($rowsServerList = $db->fetch_array($resultServerList)) {
				    $serverName = $rowsServerList['server_name'];     //服务器名				    
				    $roleInfo = userModel::getRoleInfo($rowsServerList['server_url'],$rows['userid']);		
				    if (!empty($roleInfo['playersid']))	{
				    	$_SESSION['playersid'] = $roleInfo['playersid'];
				    }	    
				    /*if ($roleInfo['playersid']) {
				    	$returnServerInfo['status'] = 1;                      //服务器状态
				    } else {
				    	$returnServerInfo[] = 2;                              //服务器状态
				    }*/				       				    
				    $roleName = !empty($roleInfo['nickname'])?$roleInfo['nickname']:$user_lang['getLoginInfoForJava_1'];                         //角色名
				    //$returnServerInfo[] = $roleInfo['playersid']?$roleInfo['playersid']:0;           //角色ID					    
				    $url = $rowsServerList['server_url'];  //服务器URL				    	
			        $returnServerInfo[] = array('serverName'=>$serverName,'roleName'=>$roleName,'url'=>$url);
			    }	
		        $serverInfo = $returnServerInfo;
			}
			return 0;
		}
	}	
	
	//获取角色信息
	static function getRoleInfo($url,$userid) {
		//$url = 'http://192.168.1.4';
		$url = 'http://192.168.1.5';
		$content = file_get_contents($url.'/app.php?option=role&client=1&task=returnRoleDataToUser&loginKey=123&userId='.$userid);
		$content = json_decode($content,true);
		if (is_array($content)) {
			return $content;
		} else {
			return false;
		}
		
	}
	
	//修改用户密码
	static function chagePassWord(&$userInfo) {
		global $common,$db;
		$userInfo['newPassword'] = _get('newPassword');
		$userLogin = userModel::getLoginInfo($userInfo);
		if ($userLogin == 2) {
			return 2; //找不到该用户名
		} elseif ($userLogin == 3) {
			return 3; //老密码错误
		} else {
			if (!$userInfo['newPassword']) {
				return 4;
			} else {
			   $db->query("UPDATE ".$common->tname('user')." SET password = '".$userInfo['newPassword']."' WHERE userid = '".$userInfo['userid']."'");
			   $userInfo['password'] = $userInfo['newPassword'];
			   return 1;
			}
		}
	}
	
	//人数是否达到上限
	static function checkGameNum() {
		global $common,$db,$_SC;
		$result = $db->query("SELECT count(*) as icount FROM ".$common->tname('user'));
		$rows = $db->fetch_array($result);
		if($rows['icount'] >= $_SC['gamenum']) {
			return true;
		} else {
			return false;
		}
	}
	
	//socket访问
	public static function http_post($host, $port, $path, $data) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://'.$host.$path);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
    }	
    
    //检测UCSID合法性以及获取相关信息
    public static function checksid($sid) {
    	global $_SGLOBAL, $_SC, $user_lang;
    	$id = intval($_SGLOBAL['timestamp']);
    	$data = array('sid'=>$sid);
    	$sign = md5($_SC['cpId'].'sid='.$sid.$_SC['apiKey']);
    	$game = array('cpId'=>intval($_SC['cpId']),'gameId'=>intval($_SC['gameId']),'channelId'=>intval($_SC['channelId']),'serverId'=>intval($_SC['serverId']));
    	//$postdata = "id=$id&service=ucid.user.sidInfo&data=$data&game=$game&sign=$sign&encrypt=md5";
    	$postdata = json_encode(array("id"=>$id,"service"=>"ucid.user.sidInfo","data"=>$data,"game"=>$game,"sign"=>$sign,"encrypt"=>"md5"));
    	$ucdata = userModel::http_post($_SC['apiUrl'],80,'/ss',$postdata);
    	if (!empty($ucdata)) {
    		$userInfo = json_decode($ucdata,true);
    		if ($userInfo['state']['code'] == 1) {
    			$value['uid'] = $userInfo['data']['ucid'];
    			$value['username'] = $userInfo['data']['nickName'];
    			/*if(userModel::isPlayerExists($uid)) {
					$userInfo = array('username'=>$uid,'realname'=>'','client'=>intval(0),'register_time'=>$id,'uzone_token'=>'');
					$result = userModel::saveUserInfo($userInfo);
					$userInfoflash = array('username'=>$uid,'realname'=>'','client'=>trim(0),'register_time'=>$id,'uzone_token'=>'','inviteid'=>'','sexid'=>'');
					userModel::flashUserLogin($userInfoflash);
					userController::authorize($userInfoflash,$userInfo['xyh'],0);
				} else {
					$userInfo = array('username'=>$uid,'realname'=>trim($uid),'client'=>intval(0),'register_time'=>$id,'uzone_token'=>'');
					$result = userModel::saveUserInfo($userInfo);
					$userInfoflash = array('username'=>$uid,'realname'=>trim($uid),'client'=>trim(0),'register_time'=>$id,'uzone_token'=>'','inviteid'=>'','sexid'=>0);
					userModel::flashUserLogin($userInfoflash);
					userController::authorize($userInfoflash,$userInfo['xyh'],0);
				} */
    		} else {
    			$value['status'] = intval($userInfo['state']['code']);
    			$value['message'] = $userInfo['state']['msg'];    			
    		}    		
    	} else {
       		$value['status'] = 3;
    		$value['message'] = $user_lang['checksid_1'];	
    	}
    	return $value;
    }
    
    //通知UC登录同步接口
    public static function loginsynuc($gameUser) {
    	global $_SGLOBAL, $_SC;
    	$id = intval($_SGLOBAL['timestamp']);
    	$sign = md5($_SC['cpId'].'gameUser='.$gameUser.$_SC['apiKey']);
    	$game = array('cpId'=>intval($_SC['cpId']),'gameId'=>intval($_SC['gameId']),'channelId'=>intval($_SC['channelId']),'serverId'=>intval($_SC['serverId']));
    	$data = array('gameUser'=>$_SC['gameUser']);
        $postdata = json_encode(array("id"=>$id,"service"=>"ucid.bind.create","data"=>$data,"game"=>$game,"sign"=>$sign,"encrypt"=>"md5"));
    	$ucdata = userModel::http_post($_SC['apiUrl'],80,'/ss',$postdata);
    }
    
    // 端游提交验证码
    public static function tjyzm($userId, $activate) {
    	global $common,$db,$mc;
    	
    	if (!($activateArr = $mc->get(MC.'activateCode'))) {
    		$activateCodeRet = $db->query("SELECT * FROM ".$common->tname('activatecode'));
    		$activateArr = array();
    		while($tmpArr = $db->fetch_array($activateCodeRet)) {
    			$activateArr[$tmpArr['activateCode']] = $tmpArr['id'];
    		}    		 		
    		$mc->set(MC.'activateCode', $activateArr, 0, 3600);
    	}
    	//$common->insertLog(json_encode($activateArr) . ' ' . $activate);
    	// 激活码正确
    	if(array_key_exists($activate, $activateArr)) {
    		$returnValue['status'] = 0;
    		//$returnValue['loginKey'] = session_id();
    		$returnValue['loginKey'] = $mc->get(MC.$userId . '_realsession');
    		$mc->delete(MC.$userId . '_realsession');
    		
    		// 修改用户状态
    		$whereUser['userid'] = $userId;
    		$userArr['isActivated'] = 1;
    		$common->updatetable('user', $userArr, $whereUser);
    		
    		// 删除数据库中对应数据
    		$whereItem['id'] = $activateArr[$activate];
    		$common->deletetable('activatecode', $whereItem);
    		
    		// 删除相应的激活码
    		unset($activateArr[$activate]);
    		$mc->set(MC.'activateCode', $activateArr, 0, 3600);
    		
    	} else { // 激活码失效或错误
    		if (!($activateFaild = $mc->get(MC.'activateFaild'))) {
    			$activateFaild = $db->fetch_array($db->query('select notice from ol_notice where id = 3'));
    			$mc->set(MC.'activateFaild', $activateFaild, 0, 0);
    		}
    		$returnValue['status'] = 1001;
    		$returnValue['message'] = $activateFaild['notice'];    		
    	}
    	
    	return $returnValue;
    }
    
    //宝软sdk支付地址生成
    public static function brzf($userid,$uid) {
    	global $_SC;
    	/*String token = cid + uid + type + key;
		token = MD5Util.MD5Encode(token);
		String notify = new  String(Base64.encode(notify_url.getBytes()));
		String charge_url = “http://www.baoruan.com/nested/account/login?cid=”+cid+”&uid=”+uid+”&type=”+type+”&token=”+token+”&notify_url=”+notify_url*/
		$publish = 1;
    	$cid = 852;
		$key = '2b42a1967731ba51f6091c190dc018ff';
		$type = 'qjsh'; 	
		$uid = str_replace('br_','',$uid);
    	$token = md5($cid.$uid.$type.$key);
    	$notify = base64_encode('http://'.$_SERVER['HTTP_HOST'].'/ucpay/brpay_1.php?userid='.$userid.'&serverid='.$_SC['fwqdm']);
    	if ($publish == 1) {
    		return "http://www.baoruan.com/nested/account/login?cid=$cid&uid=$uid&type=$type&token=$token&notify_url=$notify";
    	} else {
    		return false;
    	}
    }
    
    //修改快速登录用户登录信息
    public static function xgksdl($userid,$newname,$password,$phone,$nickname) {
    	global $common,$db,$_SC,$user_lang;
    	//$userid = _get('userId');
        if (LANG_FLAG == 'tw') {
        	if (empty($newname)) {
	    		return array('status'=>30,'message'=>$user_lang['xgksdl_6']);
	    	}
			require 'src/facebook.php';
			$jcid = _get('jcid');
			if ($jcid == 8) {
				$appId = '577721408916792';
				$secret = 'c6d6698e43792bd520b40cca54f938b8';
			} else {
				$appId = '489217221125581';
				$secret = 'b5fb1597b032bb820d5b52168e34f7f8';	
			}
			$facebook = new Facebook(array(
			  'appId'  => $appId,       //510494985668460(test)
			  'secret' => $secret,      //af4da245f4ac5c48fc7881725217522d(test)
			));
        	$facebook->setAccessToken($newname); 
			$facebook->setExtendedAccessToken();
			$newname = $facebook->getUser();
			if (empty($newname) || $newname == 0) {
				$returnValue['status'] = 30;
				$returnValue['message'] = $user_lang['authorize_2'];
				return $returnValue;
			}
			$ly = 'sg';				    	
    	} else {
	    	if (empty($newname) || empty($password)) {
	    		return array('status'=>30,'message'=>$user_lang['xgksdl_6']);
	    	}
	    	$ly = '';
    	}
    	$sql = "select * from ".$common->tname('user')." where userid = $userid limit 1";
    	$result = $db->query($sql);
    	$rows = $db->fetch_array($result);
    	if (!empty($rows)) {
    		$username = $rows['username'];
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
				$ret = $client->xgksdl($username,$newname,$password,$phone,$ly);		
			} catch(Exception $e) {
				$returnValue['status'] = 30; 
			   	$returnValue['message'] = $user_lang['register_3'];
			   	return $returnValue;
			}
			if ($ret == 3) {
				$returnValue['status'] = 30; 
			   	$returnValue['message'] = $user_lang['xgksdl_1'];
			   	return $returnValue;				
			} elseif ($ret == 4) {
				$returnValue['status'] = 30; 
			   	$returnValue['message'] = $user_lang['xgksdl_2'];
			   	return $returnValue;					
			} elseif ($ret == 5) {
				$returnValue['status'] = 30; 
			   	$returnValue['message'] = $user_lang['xgksdl_3'];
			   	return $returnValue;					
			} else {
				/*$checkName = roleModel::is_checkNameAll ( $nickname, 1 );
				if ($checkName == 1 || empty($nickname)) {
					return array('status'=>30,'message'=>$user_lang['xgksdl_5']);
				}
				$db->query("update ".$common->tname('player')." set nickname = '$nickname' where userid = $userid");*/
				if (LANG_FLAG == 'tw') {
					$db->query("update ".$common->tname('user')." set kszc = 0,username = '$newname' where userid = $userid");
					$db->query("update ".$common->tname('player')." set ucid = '$newname' where userid = $userid");
				} else {
					$db->query("update ".$common->tname('user')." set kszc = 0 where userid = $userid");	
				}			
				return array('status'=>0);
			}  		
    	} else {
    		return array('status'=>30,'message'=>$user_lang['xgksdl_4']);
    	}
    }
}