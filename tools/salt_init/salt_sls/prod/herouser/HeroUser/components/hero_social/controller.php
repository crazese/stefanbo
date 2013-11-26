<?php
class socialController {
	//发送加好友申请
	function jhysq($socialInfo) {
		$playersid = $socialInfo['playersid'];
		$tuid = _get('tuid');
		$showValue = socialModel::jhysq($playersid,$tuid);
		ClientView::show($showValue);
	}
	
	//处理好友请求申请
	function cljhysq($socialInfo) {
		$playersid = $socialInfo['playersid'];
		$sqid = _get('sqid');  //申请加好友消息的ID
		$page = _get('page');  //1同意0拒绝
		$tuid = _get('tuid');  //对方角色id
		$xxlx = _get('xxlx');
		$showValue = socialModel::cljhysq($playersid,$sqid,$page,$xxlx,$tuid);
		ClientView::show($showValue);
	}
	
	//宝箱
	function openBox($socialInfo) {
		$playersid = $socialInfo['playersid'];
		$tuid = _get('toplayersid');
		$showValue = socialModel::openBox($playersid,$tuid);
		ClientView::show($showValue);
	}
	
	//全部送礼
	function setAllgift($socialInfo) {
		$returnValue = socialModel::setAllgift($socialInfo);
		ClientView::show($returnValue);
	}
	
	//查询好友
	function selFriend($socialInfo) {
		global $common;
		$socialInfo['username'] = base64_decode(isset($_GET['username']) ? $_GET['username'] : '');
		$returnValue = socialModelJava::selFriend($socialInfo);
		ClientView::show($returnValue);
	}
	
	//游戏邻居
	function getNeighbor($socialInfo) {
		$returnValue = socialModelJava::getNeighbor($socialInfo);
		ClientView::show($returnValue);
	}
	
	//游戏好友列表
	function getfriendlist($socialInfo){
		$page = _get('page');
		$is_show_deny = _get('ycfjwj');
		$playersid = $socialInfo['playersid'];
		$returnValue = socialModel::getFriendInfo($playersid, $page, $is_show_deny);
		ClientView::show($returnValue);
	}
	
	//随机玩家
	function getRandFriend($socialInfo) {
		global $common;
		$type = _get('type');
		$sx = _get('sx');
		//$returnValue = socialModel::getRandFriend($socialInfo['playersid'],$type,$sx);
		$returnValue = socialModel::findRandFriend($socialInfo['playersid'], $type, $sx);
		ClientView::show($returnValue);
	}
	
	//武将历练详细信息
	function getPracticeInfo($socialInfo) {	
		$returnValue = socialModel::getPracticeInfo1($socialInfo);
		ClientView::show($returnValue);
	}
	
	//历练好友列表 不能包含以邀请的好友
	function getFriendsNotRepeat($socialInfo) {
		global $common;
		$socialInfo['zs'] = socialModel::getFriendsNotRepeatCount($socialInfo);
		$result = socialModel::getFriendsNotRepeat($socialInfo);
		
		if ($result == 2) {
			$returnValue['status'] = 0; 
			$returnValue['list'] = $result;
		} else {
			$returnValue['status'] = 0;                                      
			$returnValue['list'] = $result; 
			$returnValue['zs'] = (int)$socialInfo['zs']; 	  
		}
		ClientView::show($returnValue);
	}
	
	//历练(邀请1个好友或者多个好友)
	function setPractice($socialInfo) {
		if(strpos($socialInfo['toplayersid'], ',') != false) { // 邀请多个好友			
			$returnValue = socialModel::setPractice2($socialInfo);
		} else { // 邀请 1个好友
			$returnValue = socialModel::setPractice1($socialInfo);
		}
		ClientView::show($returnValue);
	}
	
	//历练(挑选申请好友)
	function selPractice($socialInfo) {
		global $common,$_g_lang;
		$result = socialModel::selPractice($socialInfo);
			
		if ($result == 2) {
			$returnValue['status'] = 2; 
			$returnValue['message'] = $_g_lang['social']['err_request'];
		} else {
			$returnValue['status'] = 0;        
			$returnValue['jsid'] = (int)$socialInfo['toplayersid'];                     					  
		}
		ClientView::show($returnValue);
	}
	
	//删除历练邀请
	function delPractice($socialInfo) {
		global $common,$_g_lang;
		$result = socialModel::delPractice($socialInfo);
			
		if ($result == 2) {
			$returnValue['status'] = 2; 
			$returnValue['message'] = $_g_lang['social']['err_request'];
		} elseif ($result == 3) {
			$returnValue['status'] = 1116;
			$returnValue['message'] = $_g_lang['social']['user_agree'];
		} else {
			$returnValue['status'] = 0;                                      				  
		}
		ClientView::show($returnValue);
	}
	
	//历练成功
	function submitPractice($socialInfo) {
		global $common,$_g_lang;
		$rwid = '';
		
		$lllq = genModel::lllqsh($socialInfo['playersid'], $socialInfo['generalid']);
		if($lllq['lt'] > 0) {
			$returnValue['status'] = 21; 
			$returnValue['message'] = $_g_lang['social']['expr_cool_wait'];
		} else {			
			$result = socialModel::submitPractice($socialInfo,$rwid);
				
			if ($result == 2) {
				$returnValue['status'] = 2; 
				$returnValue['message'] = $_g_lang['social']['err_request'];
			} elseif ($result == 3) {
				$returnValue['status'] = 21; 
				$returnValue['message'] = $_g_lang['social']['expr_at_times_max'];
			} elseif ($result == 4) {
				$returnValue['status'] = 1001; 
				$returnValue['message'] = $_g_lang['social']['expr_err'];			 
			} elseif ($result == 20) {
				$returnValue['status'] = 21;
				$returnValue['message'] = $_g_lang['social']['expr_general_no_find'];
			} elseif ($result == 22) {
				$returnValue['status'] = 21;
				$returnValue['message'] = $_g_lang['social']['expr_info_no_find'];
			} elseif ($result == 23) {
				$returnValue['status'] = 21;
				$returnValue['message'] = $_g_lang['social']['general_expr_max'];
			} elseif ($result == 24) {
				$returnValue['status'] = 21;
				$returnValue['message'] = $_g_lang['social']['general_expr_max_at_lvl'];
			} elseif ($result == 25) {
				$returnValue['status'] = 21;
				$returnValue['message'] = $_g_lang['social']['bf_30_level_exp_1_per_3'];
			} elseif ($result == 26) {
				$returnValue['status'] = 21;
				$returnValue['message'] = $_g_lang['social']['need_fri_answer_expr'];
			}  else {
				$getInfo = array();
				$getInfo['playersid'] = $socialInfo['playersid'];
				$getInfo['userid'] = $socialInfo['userid'];
				//$returnValue = cityModel::getGeneralList($getInfo);
				$ginfo = cityModel::getGeneralList($getInfo, 0, true ,$socialInfo['generalid']);  //cityModel::getGeneralList ( $getInfo, 0, true ,$ngid);  
				$returnValue['status'] = 0;
				$returnValue['ginfo'] = $ginfo['generals'];   
				$returnValue['gid'] = $socialInfo['generalid'];  
				$returnValue['cs'] = $cjInfo['ll'] = $returnValue['ginfo'][0]['llcs'];
				$roleInfo['playersid'] = $socialInfo['playersid'];	
				achievementsModel::check_achieve($roleInfo['playersid'],$cjInfo,array('ll'));			
				roleModel::getRoleInfo($roleInfo);
				if ($returnValue['cs'] >= 20) {
					$roleInfo['lles'] = $_SESSION['lles'] = $_SESSION['lles'] + 1;				
				}
				if ($returnValue['cs'] >= 30) {
					$roleInfo['llss'] = $_SESSION['llss'] = $_SESSION['llss'] + 1;				
				}
				if ($returnValue['cs'] >= 40) {
					$roleInfo['llmax'] = $_SESSION['llmax'] = $_SESSION['llmax'] + 1;
				}				
				$roleInfo['rwsl'] = $returnValue['cs'];
				/*$xwzt_18 = substr($roleInfo['xwzt'],17,1); //玩家兑换
				if ($xwzt_18 == 0) {
					$updateRole['xwzt'] = substr_replace($roleInfo['xwzt'],'1',17,1);
					$common->updatetable('player',$updateRole,array('playersid'=>$roleInfo['playersid']));
					$common->updateMemCache(MC.$roleInfo['playersid'],$updateRole);
				}*/			
				$rwid = questsController::OnFinish($roleInfo,"'llcs','llwj'");
				if (!empty($rwid)) {
					if (!empty($rwid)) {
						$returnValue['rwid'] = $rwid;
					} 
				}		                         			  
			}	
		}
		ClientView::show($returnValue);
	}
	
	//银票补齐好友
	function submitLessPractice($socialInfo) {
		global $common;
		$returnValue = socialModel::submitLessPractice1($socialInfo);
		ClientView::show($returnValue);
	}
	
	//取消历练 
	function cancelPractice($socialInfo) {
		global $common,$_g_lang;
		$result = socialModel::cancelPractice($socialInfo);
		if ($result == 2) {
			$returnValue['status'] = 2; 
			$returnValue['message'] = $_g_lang['social']['err_request'];
		} elseif ($result == 5) {
			$returnValue['status'] = 1118;
			$returnValue['message'] = $_g_lang['social']['expr_circumstances'];
		} else {
			$returnValue['status'] = 0;  
			$returnValue['gid'] = (int)$socialInfo['generalid'];                                    			  
		}
		ClientView::show($returnValue);
	}
	
	//删除好友
	function deleteFriends($socialInfo){
		global $common;
		$returnValue = socialModel::deleteFriends($_SESSION['playersid'], $socialInfo['toplayersid'], $socialInfo['page']);
		ClientView::show($returnValue);
	}
	
	//送礼
	function setGift(&$socialInfo){
		global $common;
		$sltype = _get('sltype');
		$tradeid = _get('tradeid');
		$xxlx = _get('xxlx');
		$page = _get('page');
		$xxid = _get('xxid');
		$returnValue = socialModel::setGift($socialInfo,$sltype,$tradeid,$xxlx,$page,$xxid);//送礼
		ClientView::show($returnValue);
	}
	
	//打劫
	function setRobbery($socialInfo) {
		global $common;
		$returnValue = socialModel::setRobbery($socialInfo,$rwid);//打劫
		ClientView::show($returnValue);
	}
	
	//仇人列表
	function getBlacklist($socialInfo) {
		global $common;
		$zl = _get('zl');
		$tuid = _get('tuid');
		$page = _get('page');
		$returnValue = socialModel::getBlacklist1($socialInfo['playersid'],$zl,$tuid, $page);
		ClientView::show($returnValue);
	}
	
	//偷将
	function jgtj($socialInfo) {
		$playersid = $socialInfo['playersid'];
		$gid = _get('gid');
		$tuid = _get('tuid');
		if (empty($tuid)) {
			$tuid = 0;
		}
		$result = socialModel::jgtj($playersid,$gid,$tuid);
		ClientView::show($result);			
	}
	
	//索要礼物
	function sylw($socialInfo) {
		$playersid = $socialInfo['playersid'];
		$clid = _get('clid');			//材料 id
		$tuid = _get('tuid');           //角色 id 用','隔开
		$result = socialModel::sylw($playersid,$clid,$tuid);
		ClientView::show($result);			
	}
	
	// 搜索邻居
	function sslj($socialInfo){
		$playersid = $socialInfo['playersid'];
		$tun       = _get('tun');
		$type      = _get('type');
		$page      = _get('page');
		
		$result = socialModel::sslj($playersid, $tun, $type, $page);
		ClientView::show($result);
	}

	// 绑定邀请码
	function bindreqcode($socialInfo){
		$playersid = $socialInfo['playersid'];
		$r_code    = _get('r_code');

		$result = socialModel::bindReqCode($playersid, $r_code);
		ClientView::show($result);
	}
}
?>