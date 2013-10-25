<?php
/**
 * @author kknd li
 * 公共消息的hash值的根值
 * @var String
 */
define('MEM_PUBLIC_NOTICE_KEY','public_notice_key');
define('MEM_SYSTEM_NOTICE_KEY','system_notice_key');

// 每页显示消息数量
define('SHOW_MESSAGE_BY_PAGE', 2);

/**
 * 负责游戏中信件交互的模块提供相关支持
 *
 */
class lettersModel {
	// 获得信件的分类包含哪些类型
	public static function getLetterTypes(){
		$letterTypes = array(
			// 战斗消息
			0 =>array(1,5,9,12,13,14,15,16,17,18,19,21,22,23,24,25,26,27,29,30,31),
			// 好友消息
			1 =>array(2,3,4,6,7,8,10,11,33,70),
			// 系统消息
			2 =>array(0,20,28,32),
			// 资源消息
			3 =>array(34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69)
		);
		return $letterTypes;
	}
	
	// 帮助所有向我发出历练邀请的玩家
	public function bzsyll($lettersInfo) {
		global $db,$common,$mc,$_SGLOBAL,$_g_lang;
		
		// 获取该玩家所有历练邀请信件id
		$result = $db->query("select id,parameters,status,fromplayersid,practiceid,create_time from ol_letters where playersid = " . $_SESSION['playersid'] . " and genre = 7");
		while($letters[] = $db->fetch_array($result, MYSQL_ASSOC));
		array_pop($letters);
		
		$success_agree = $faild_agree = 0;
		$letters_num = count($letters);
		if(count($letters) > 0) {
			foreach($letters as $key=>$letter) {
				$tojsid = $letter['fromplayersid'];
				$update['status'] = "2";                        // 表示消息已处理
				$where['id'] = $letter['id'];
				$common->updatetable('letters',$update,$where);
				
				$result = $db->query("SELECT * FROM ".$common->tname('practice')." WHERE id = '".$letter['practiceid']."' and status = 0 LIMIT 1");
				$rows = $db->fetch_array($result);
				if(!empty($rows['yq_playersid'])) {
					$yq_arr = explode(',', $rows['yq_playersid']);
					// 如果当前点同意的玩家不在此条历练邀请列表中
					if(!in_array($_SESSION['playersid'], $yq_arr)) {
						//$value['status'] = 2;
						//$value['mesage'] = '非法请求！';
						//return $value;
						++$faild_agree;
						continue;
					}
					$agree_arr = unserialize($rows['is_agree']);
					// fix notice warrning
					if(!array_key_exists($_SESSION['playersid'], $agree_arr)) {
						$value = lettersModel::deleteLetters($letter['id'],$_SESSION['playersid']);				
						++$faild_agree;
						continue;
					}
					$agree_arr[$_SESSION['playersid']] = intval($agree_arr[$_SESSION['playersid']]) + 1;			 
					$update_practice['is_agree'] = serialize($agree_arr);
					$where_practice['id'] = $rows['id'];
					$common->updatetable('practice',$update_practice,$where_practice);
					//$common->deletetable('letters',array('practiceid'=>$rows['id'],'playersid'=>$_SESSION['playersid']));
					$value = lettersModel::deleteLetters($letter['id'],$_SESSION['playersid']);
					socialModel::addFriendFeel($tojsid, $_SESSION['playersid'], 1);			
					++$success_agree;
				} else {
					$value = lettersModel::deleteLetters($letter['id'],$_SESSION['playersid']);
					++$faild_agree;
					continue;
				}
			}
		} else if($letters_num == 0) {
			return array('status'=>21, 'message'=>$_g_lang['letter']['no_exper_help']);
		}
		
		if($success_agree > 0) {
			return array('status'=>0, 'message'=>$_g_lang['letter']['all_help_cpl']);
		} else {			
			return array('status'=>0);
		}
	}
	
	// 同意历练
	static function agreePractice($lettersInfo,$xxlx,$page) {
		global $db,$common,$mc,$_SGLOBAL,$_g_lang;
		$result = $db->query("SELECT id,parameters,status,fromplayersid,practiceid,create_time FROM ".$common->tname('letters')." WHERE id = '".$lettersInfo['lettersid']."' and status <> 2  LIMIT 1");
		$rows = $db->fetch_array($result);
		if(empty($rows)) {
			//$value['status'] = 2;
			//return $value;
			$value = lettersModel::deleteLetters($lettersInfo['lettersid'],$_SESSION['playersid'],$page,$xxlx);
			$value['status'] = 1001;
			$value['message'] = $_g_lang['letter']['invite_false'];
			return $value;
		}
		$tojsid = $rows['fromplayersid'];
		$update['status'] = "2";                        // 表示消息已处理
		$where['id'] = $lettersInfo['lettersid'];
		$common->updatetable('letters',$update,$where);
		
		$result = $db->query("SELECT * FROM ".$common->tname('practice')." WHERE id = '".$rows['practiceid']."' and status = 0 LIMIT 1");
		$rows = $db->fetch_array($result);
		if(!empty($rows['yq_playersid'])) {
			$yq_arr = explode(',', $rows['yq_playersid']);
			// 如果当前点同意的玩家不在此条历练邀请列表中
			if(!in_array($_SESSION['playersid'], $yq_arr)) {
				$value['status'] = 2;
				$value['message'] = $_g_lang['letter']['ill_request'];
				return $value;
			}
			$agree_arr = unserialize($rows['is_agree']);
			// fix notice warrning
			if(!array_key_exists($_SESSION['playersid'], $agree_arr)) {
				$value = lettersModel::deleteLetters($lettersInfo['lettersid'],$_SESSION['playersid'],$page,$xxlx);
				$value['status'] = 1001;
				$value['message'] = $_g_lang['letter']['invite_false'];
				return $value;
			}
			$agree_arr[$_SESSION['playersid']] = intval($agree_arr[$_SESSION['playersid']]) + 1;			 
			$update_practice['is_agree'] = serialize($agree_arr);
			$where_practice['id'] = $rows['id'];
			$common->updatetable('practice',$update_practice,$where_practice);
			//$common->deletetable('letters',array('practiceid'=>$rows['id'],'playersid'=>$_SESSION['playersid']));
			$value = lettersModel::deleteLetters($lettersInfo['lettersid'],$_SESSION['playersid'],$page,$xxlx);
			socialModel::addFriendFeel($tojsid, $_SESSION['playersid'], 1);
			
			// 是否达到历练所需人数
			/* $amount = 0;
			foreach ($agree_arr as $k=>$v) {
				$amount += intval($v);
			}
			// 达到历练人数，发信件提醒
			if(($amount + $rows['tool_num'])  >= $rows['invite_num']) {
				$ginfo = cityModel::getGeneralData($tojsid, 0, $rows['generalid']);
				$wjwjmc1 = $ginfo[0]['general_name'];
				
				$json = array();
				$json['playersid'] = $lettersInfo['playersid'];
				$json['toplayersid'] = $tojsid;
				$json['message'] = array('wjwjmc1'=>$wjwjmc1);
				$json['type'] = 1;
				$json['genre'] = 33;
				$json['interaction'] = 0;
				$json['tradeid'] = 0;
				//$json = json_encode($json);
				$letters_id = lettersModel::addMessage($json);
			} */
			
		} else {
			$value = lettersModel::deleteLetters($lettersInfo['lettersid'],$_SESSION['playersid'],$page,$xxlx);
			$value['status'] = 1001;
			$value['message'] = $_g_lang['letter']['meet_cond_invite_false'];
		}
		 		
		return $value;
	}
	
	/**
	 * 玩家回礼
	 *
	 * @param array $lettersInfo		系统产生的数据包含玩家id
	 * @param int $xxlx					消息id
	 * @param int $page					页数
	 * @return array
	 */
	static function agreeGift($lettersInfo,$xxlx,$page) {
		global $db,$common,$_g_lang;
		$social_tools = get_social_tools();
		
		if (empty($lettersInfo['lettersid'])) {
			$value['status'] = 2;
			return $value;
		}
		
		// 查找礼物对应信件信息
		$letterSql = "SELECT id,request,status,fromplayersid,playersid,tradeid,create_time FROM ".$common->tname('letters');
		$letterSql .= " WHERE id='{$lettersInfo['lettersid']}' and playersid={$lettersInfo['playersid']} AND status = '1'  LIMIT 1";
		$result = $db->query($letterSql);
		$row = $db->fetch_array($result);
		if($row['id'] == 0 ) {
			$value['status'] = 2;
			$value['message'] = $_g_lang['letter']['no_find_letter'];
			return $value;
		}
		
		$friendIDList = roleModel::getTableRoleFriendsInfo($lettersInfo['playersid'], 1, true);
		if(!key_exists($row['fromplayersid'], $friendIDList)){
			$value = lettersModel::deleteLetters($lettersInfo['lettersid'],$lettersInfo['playersid'],$page,$xxlx);
			$value['status'] = 1003;
			$value['message'] = $_g_lang['letter']['no_friend_no_gift'];
			return $value;
		}
		
		
		// 判断当天是否送过礼物，正常情况下用户第一天加不会送礼，所以不考虑用户第一天加的情况
		if(socialModel::is_TodayGift($lettersInfo['playersid'], $row['fromplayersid']) != 1) {
			$value = lettersModel::deleteLetters($lettersInfo['lettersid'],$lettersInfo['playersid'],$page,$xxlx);
			$value['status'] = 1001;
			$value['message'] = $_g_lang['letter']['today_gift_no_again'];
			return $value;
		}
		
		$roleInfo['playersid'] = $lettersInfo['playersid'];
		if(!roleModel::getRoleInfo($roleInfo)){
			$returnValue['status']  = 26;
			$returnValue['message'] = $_g_lang['letter']['error_pinfo'];
			return $returnValue;
		}
		
		if (substr($row['request'],0,1) == '{') {
			$itemId = json_decode($row['request'], true);
		} else {
			$itemId = json_decode(gzuncompress(base64_decode($row['request'])), true);
		}	
		$itemId = $itemId['itemId'];
		
		// 写入礼物信息
		$social_trade['type'] = 2;
		$social_trade['fromplayersid'] = $lettersInfo['playersid'];
	    $social_trade['toplayersid'] = $row['fromplayersid'];
	    $social_trade['create_time'] = time();
	    $social_trade['gift_type'] = $itemId;//赠送的材料id
	    $tradeId = $common->inserttable('social_trade',$social_trade);
	    
	    // 更新双方玩家送礼缓冲数据
	    $tradeInfo = array('id'=>$tradeId,
	    					'frompid'=> $social_trade['fromplayersid'],
	    					'topid'  => $social_trade['toplayersid'],
	    					'type'   => $social_trade['type'],
	    					'itemid' => $social_trade['gift_type'],
	    					'status' => 0,
	    					'ctime'  => $social_trade['create_time']);
	    					
	    socialModel::modifyMemTradeInfo($social_trade['fromplayersid'], $tradeInfo);
	    socialModel::modifyMemTradeInfo($social_trade['toplayersid'], $tradeInfo);
		
		// 写入回礼消息
	    $itemInfo = array();
	    foreach($social_tools as $s_tool){
	    	if($s_tool['id']==$itemId){
	    		$itemInfo = $s_tool;
	    		break;
	    	}
	    }
		//$itemInfo = toolsModel::getItemInfo($itemId);
		$json['toplayersid'] = $row['fromplayersid'];
		$json['playersid'] = $lettersInfo['playersid'];
		// 消息表显示信息玩家名称，玩家id，礼物名称，礼物数量
		$json['message'] = array('wjmc1'=>$roleInfo['nickname'],'wjid1'=>$roleInfo['playersid'],'lwmc'=>$itemInfo['mc'], 'lwsl'=>1);
		// 用来区分回赠，这里为2，socialModel::setGift如果不是索要送礼为1
		$json['type'] = 2;
		$json['genre'] = 4;           //好友回礼
		$json['tradeid'] = $tradeId;
		$json['create_time'] = time();
		//$json = json_encode($json);
		$result = lettersModel::addMessage($json);//送礼
		
		// 处理好友度
		socialModel::addFriendFeel($row['fromplayersid'], $lettersInfo['playersid'], 1);
		
		$value = lettersModel::deleteLetters($lettersInfo['lettersid'],$lettersInfo['playersid'],$page,$xxlx);
		// 回礼后的任务触发
		$roleInfo['playersid'] = $lettersInfo['playersid'];
		roleModel::getRoleInfo($roleInfo);
		$rwid = questsController::OnFinish($roleInfo,"'slrs'");
		if (!empty($rwid)) {
			$value['rwid'] = $rwid;
		} 
		return $value;
	}
	
	/**
	 * 玩家领取礼物
	 *
	 * @param array $lettersInfo		系统产生的数据包含玩家id
	 * @param int $sltype				1领取别人主动送的礼，2领取自己索取后别人送的礼
	 * @param int $xxlx					消息id
	 * @param int $page					页数
	 * @return array
	 */
	static function takeGift($lettersInfo,$sltype=1,$xxlx,$page) {
		global $db,$common,$mc, $G_PlayerMgr,$_g_lang;
		// status=0表示没有处理过，以区领取完后可以回礼的信件状态
		$result = "SELECT id,genre,fromplayersid,playersid,tradeid,create_time FROM ".$common->tname('letters');
		$result .= " WHERE id='".$lettersInfo['lettersid']."' AND status = 0  LIMIT 1";
		$query = $db->query($result);
		$rows = $db->fetch_array($query);
		$temp_genre = $rows['genre'];
		$tradeid = $rows['tradeid'];
		$create_time = $rows['create_time'];
		
		// 没有找到对应的信件
		if(empty($rows)){
			$value = lettersModel::getMessageList($lettersInfo['playersid'], $xxlx, $page);
			$value['message'] = $_g_lang['letter']['gift_no_find'];
			$value['status'] = 1002;
			return $value;
		}
		
		// type=1直接送礼，type=2回赠
		$result = $db->query("SELECT * FROM ".$common->tname('social_trade')." where id={$tradeid} and (type=1 or type=2)");
		$rows = $db->fetch_array($result);
		
		// 加资源
		$lettersroleInfo['playersid'] = $lettersInfo['playersid'];
		roleModel::getRoleInfo($lettersroleInfo);
		//$addStatus = toolsModel::addItems($lettersroleInfo['playersid'], array($rows['gift_type']=>1), $djIdList, $oldDjList);
		$player = $G_PlayerMgr->GetPlayer($lettersInfo['playersid']);
		$addStatus = $player->AddItems(array($rows['gift_type']=>1));
		if(false === $addStatus) {
			$value['status'] = 1001;
			$value['message'] = '';
			return $value;
		}
		
		// 修改送礼表数据
		$update['status'] = 1;
		$where['id']      = $rows['id'];
		$common->updatetable('social_trade', $update, $where);
		
	    // 如果送礼是发生在当天就更新数据到缓冲
	    if(date('Y-m-d', $rows['create_time'])==date('Y-m-d')){
		    $tradeInfo = array('id'=>$tradeid,
		    					'frompid'=> $rows['fromplayersid'],
		    					'topid'  => $rows['toplayersid'],
		    					'type'   => $rows['type'],
		    					'itemid' => $rows['gift_type'],
		    					'status' => 1,
		    					'ctime'  => $rows['create_time']);
		    socialModel::modifyMemTradeInfo($rows['fromplayersid'], $tradeInfo);
		    socialModel::modifyMemTradeInfo($rows['toplayersid'], $tradeInfo);
	    }
		
		// 如果是直接送礼就修改信件状态，否则删除信件
		if($rows['type'] == 1) {
			$update['status']  = 1;
			$update['request'] = base64_encode(gzcompress(json_encode(array('itemId'=>$rows['gift_type'])),1));
			$where['id'] = $lettersInfo['lettersid'];			
			$common->updatetable('letters',$update,$where);
			$returnValue = lettersModel::getMessageList($lettersInfo['playersid'], $xxlx, $page);
		}
		else{
			$returnValue = lettersModel::deleteLetters($lettersInfo['lettersid'],$lettersInfo['playersid'],$page,$xxlx);
		}
		
		//$bagList = toolsModel::getBglist($djIdList, $lettersInfo['playersid'], $oldDjList);
		$bagList =  $player->GetClientBag();
		$returnValue['list'] = $bagList;
		
		return $returnValue;
	}
	
	/**
	 * 删除消息
	 *
	 * @param int $xxid				消息id
	 * @param int $playersid		玩家id
	 * @param int $page				需要返回的消息页
	 * @param int $xxlx				需要返回的消息类型
	 * @return NULL|array			如果存在$page和$xxlx就返回删除后该消息页
	 */
	static function deleteLetters($xxid=0,$playersid,$page=null,$xxlx=null) {
		global $db,$common;
		$db->query("delete from ".$common->tname('letters')." WHERE playersid = '".$playersid."' and id = '".$xxid."'" );
		// 对于部分不需要返回信息的调用不执行下面的操作
		if(is_null($page)&&is_null($xxlx)){
			return ;
		}
		
		$returnValue = lettersModel::getMessageList($playersid, $xxlx, $page);
		return $returnValue;
	}
	
	/**
	 * 
	 * 消息列表
	 * @param int $playersid			玩家id
	 * @param int $zttype				表示分类 0战斗，1交友， 2系统, 3资源, 4无指定类型
	 * @param int $page
	 */
	static function getMessageList($playersid, $zttype, $page) {
		global $db,$common, $mc;
		// 临时解决方案，以后需要前端fix后屏蔽该代码
		// 检查是否有未抽奖的memcache信件并处理
		toolsModel::lateCj($playersid);
		
		// $zttype为3时查找最后传入的信件分类和每种分类的数量，如果没有默认为0
		if(4 == $zttype){
			$rows = $mc->get(MC.$playersid.'_messageStatus');
			$returnValue['xzdrz'] = 0;
			$returnValue['xhyrz'] = 0;
			$returnValue['xxxrz'] = 0;
			$returnValue['xzyrz'] = 0;
			
			$zttype = empty($rows)?0:$rows['zxlx'];
			$returnValue['zxlx'] = $zttype;
			
			if(!empty($rows)){
				$returnValue['xzdrz'] = $rows['xzdrz']>99?99:$rows['xzdrz'];
				$returnValue['xhyrz'] = $rows['xhyrz']>99?99:$rows['xhyrz'];
				$returnValue['xxxrz'] = $rows['xxxrz']>99?99:$rows['xxxrz'];
				$returnValue['xzyrz'] = $rows['xzyrz']>99?99:$rows['xzyrz'];
				
				//清除消息状态
				$common->deletetable('message_status',"playersid = '$playersid'");
				$mc->delete(MC.$playersid.'_messageStatus');
			}
		}
		
		
		//$zt = isset($_GET['zt']) ? $_GET['zt'] : 0;				// 消息状态 0 未接收， 1 未处理，2 已处理
		
		if(empty($page)) {
			$page = 1;
		}
		$zs = lettersModel::getMessageCount($playersid, $zttype);		// 信件总数
		$pageRowNum = SHOW_MESSAGE_BY_PAGE;
		
		$page_z     = 0;										// 玩家总页面数
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
		
		$letterType = lettersModel::getLetterTypes();
		$needType   = $letterType[$zttype];			//当前查询的是什么类型的信件
		
		$genreWhere = ' and genre in (';
		$genreWhereTemp = '';
		foreach($needType as $key=>$type){
			$genreWhereTemp .= $type.',';
		}
		$genreWhereTemp = trim($genreWhereTemp, ',');
		$genreWhere .= $genreWhereTemp.')';
		
		if (empty($playersid)) {
			return false;   //非法的客户端请求
		}
		
		$sql = "SELECT * FROM ".$common->tname('letters');
		$sql .= " WHERE playersid = '{$playersid}'{$genreWhere}";
		$sql .= " order by create_time desc,id desc LIMIT {$_start},".SHOW_MESSAGE_BY_PAGE;

		$result = $db->query($sql);
		
		$returnValue['status'] = 0;
		
		// 构造list参数对应的数据
		$value = array();
		while (($row = $db->fetch_array($result))!==false) {
			$tempValue = array();
			$message = trim($row['message'], '"');
			
			$_message = json_decode($message, true);
			if(empty($_message)){
				$message = str_replace('\\\\\\\\', '\\', $message);
				$message = str_replace('\\\\', '\\', $message);
				$message = str_replace("\'", "'", $message);
				$message = str_replace('\"', '"', $message);
				$_message = json_decode($message,true);
			}
			$message = $_message;
			
  			$tempValue['id']     = $row['id'];
  			$tempValue['xllx']   = $row['genre'];
  			
  			// 回礼，需要通过状态来分辨索要回赠和直接送礼回赠
  			if($row['genre']==4){
				// type=1索要后领取，2为回赠领取
  				$tempValue['type'] = $row['type'];
  			}
  			// 送礼
			if($row['genre']==3){
				$tempValue['status'] = $row['status'];
			}
  			
  			// 如果是送礼添加送礼id
  			if(11 == $row['genre']){
  				$tempValue['tradeid'] = $row['tradeid'];
  			}
  			
  			// 返回信件自定义数据数据，战斗类信件只返回有没有数据的状态0或1
  			if(!empty($row['request']) && $row['genre'] != 31 && $row['genre'] != 32 && $row['request'] != 'eAEDAAAAAAE=') {
  				if(in_array($row['genre'], $letterType[0])){
	  				if(strlen(trim($row['request'])) > 0 ){
	  					$tempValue['request'] = 1;
	  				}else{
	  					$tempValue['request'] = 0;
	  				}
  				}else{
	  				if (substr(trim($row['request']),0,1) == '{') {
	  					$tempValue['request'] = json_decode($row['request'],true);
	  				} else {
		  				$request = gzuncompress(base64_decode($row['request'])); 
			 			$request = str_replace('\"', '"', $request);
						$request = str_replace('\\\\\\\\', '\\', $request);
						$request = str_replace('\\\\', '\\', $request); 
						$tempValue['request'] = json_decode($request,true);		
	  				}
  				}
  			}
  			
  			
  			// 是否能抽奖
  			$tempValue['cj'] = 0;
  			if(($row['genre'] == CG_TYPE_VALUE|| $row['genre'] == CJ_TYPE_VALUE)
  				&&$row['status']!=2){
  				$tempValue['cj'] = 1;
  			}
  			
  			// 过去的天数
  			$todayZero = strtotime(date('Y-m-d 0:0:0'));
 			$tempValue['rq']    = $row['create_time']>$todayZero?0:ceil(($todayZero-$row['create_time'])/(3600*24));
 			$tempValue['time']  = date('H:i', $row['create_time']);
 			if(!empty($message)){
	  			foreach($message as $key=>$val){
	  				$tempValue[$key] = $val;
	  			}
 			}
  			array_push($value, $tempValue);
		}//end while
		
		$returnValue['zys']  = $page_z;
		$returnValue['page'] = intval($page);
		$returnValue['xxlist'] = $value;
		
		return $returnValue;
	}
	
	/**
	 * @author kknd li
	 * 将$letterId对应的信件表示为已处理的
	 * @param int $letterId			信件对应的ID
	 */
	public static function isProcessLetter($letterId){
		global $common;
		
		$whereSql    = array('id'=>$letterId);
		// 消息状态 0 未接收， 1 未处理，2 已处理
		$paramsArray = array('status'=>2);
		$common->updatetable('letters', $paramsArray, $whereSql);
	}
	
	/**
	 * 添加新消息
	 * kknd li 2012-2-2 改写
	 *
	 * @param array $message		包含信件数据的数组。
	 *                              playersid,toplayersid,type,message,genre 是必须的，request是自定义参数
	 *                              其它参数看各自需要，具体参考代码
	 * @return int					信件id
	 */
	public static function addMessage($message) {
		global $db,$common,$_SC;
		
		if(!is_array($message)){
			$arr_dir = json_decode($message,true);
		}
		else{
			$arr_dir = $message;
		}
		
		lettersModel::addMessageStatus($arr_dir['toplayersid'], $arr_dir['genre'], 1);
		
	    // 是否是交互，现在已经废弃
	    $letters_trade['is_interaction'] = isset($arr_dir['interaction'])?$arr_dir['interaction']:0;
	    // 索取用，为1表示索取送礼，现在已经废弃
	    $letters_trade['is_passive'] = isset($arr_dir['is_passive'])?$arr_dir['is_passive']:'';
	    // 过去用来区分UC消息和内部消息，现在被用来区分回赠状态
	    $letters_trade['type'] = $arr_dir['type'];
	    
	    $letters_trade['subject'] = isset($arr_dir['subject'])?$arr_dir['subject']:'';
	    // 历练id
	    $letters_trade['practiceid'] = isset($arr_dir['practiceid'])?$arr_dir['practiceid']:'';
	    // 信件创建时间
	    $letters_trade['create_time'] = isset($arr_dir['create_time'])?$arr_dir['create_time']:time();
	    // 送礼所对应的id
	    $letters_trade['tradeid'] = isset($arr_dir['tradeid'])?$arr_dir['tradeid']:0;
	    // 自定义数据段，由json编码
	    $letters_trade['request'] = isset($arr_dir['request'])?mysql_escape_string(base64_encode(gzcompress($arr_dir['request'],1))):'';
	    // 不知道具体意义，目前在闯关，占领，好友邀请等上面见过但是好像没什么用处
	    $letters_trade['parameters'] = $arr_dir['playersid']."|".$arr_dir['toplayersid'];
	    $letters_trade['fromplayersid'] = $arr_dir['playersid'];
		$letters_trade['playersid'] = $arr_dir['toplayersid'];
		// 信件类型
	    $letters_trade['genre'] = $arr_dir['genre'];
	    // message中需要的结构由文档2.8.2中定义，类别由genre字段区别
		$letters_trade['message'] = mysql_escape_string(json_encode($arr_dir['message']));
		// 信件状态，送礼交互用来区分直接送礼是否收礼的状态
	    $letters_trade['status'] = 0;
	    $letterId = $common->inserttable('letters',$letters_trade);
	    return $letterId;
	}
	
	/**
	 * 
	 * 减少消息数量
	 * @param int $playersid
	 * @param int $mType       0 战斗,1社交,2系统,3资源
	 */
	static function decreaseMessageStatus($playersid, $mType) {
		global $common,$db,$mc;
		
		$messageStatus = $mc->get(MC.$playersid.'_messageStatus');
		if($messageStatus){
			// xzdrz, xhyrz, xxxrz, xzyrz
			switch($mType){
				case 0:
					$messageStatus['xzdrz'] = $messageStatus['xzdrz']>0?$messageStatus['xzdrz']-1:0;
					break;
				case 1:
					$messageStatus['xhyrz'] = $messageStatus['xhyrz']>0?$messageStatus['xhyrz']-1:0;
					break;
				case 2:
					$messageStatus['xxxrz'] = $messageStatus['xxxrz']>0?$messageStatus['xxxrz']-1:0;
					break;
			case 3:
				$messageStatus['xzyrz'] = $messageStatus['xzyrz']>0?$messageStatus['xzyrz']-1:0;
				
			}
			
			$mc->set(MC.$playersid.'_messageStatus', $messageStatus, 0, 900);
			
			$where['playersid'] = $playersid;
			$common->updatetable('message_status', $messageStatus, $where);
		}
	}
	
	/**
	 * 
	 * 消息总数
	 * @param int $playersid
	 * @param int $zt				表示分类 0战斗, 1交友, 2系统, 3资源
	 */
	static function getMessageCount($playersid, $zt) {
		global $db,$common;
		//$zt = _get('zt');
		$zt = isset($zt) ? $zt : 2;
		
		$letterType = lettersModel::getLetterTypes();
		$needType   = $letterType[$zt];			//当前查询的是什么类型的信件0战斗 1好友 2系统
		
		$genreWhere = ' and genre in (';
		$genreWhereTemp = '';
		foreach($needType as $key=>$type){
			$genreWhereTemp .= $type.',';
		}
		$genreWhereTemp = trim($genreWhereTemp, ',');
		$genreWhere .= $genreWhereTemp.')';
		
		$sql = "SELECT count(id) as icount FROM ".$common->tname('letters');
		$sql .= " WHERE playersid = '{$playersid}' {$genreWhere}";
		$sql .= " and type = 1";
		
		$result = $db->query($sql);
		
		$rows = $db->fetch_array($result);
		return $rows['icount'];
	}
	
	/**
	 * 在添加消息的时候改变未读消息数量
	 *
	 * @param int $playersid
	 * @param int $messageStatus			消息类型，由genre定义
	 * @param int $num						要添加的数量
	 */
	static function addMessageStatus($playersid, $messageStatus, $num=1) {
		global $common,$db,$mc;
		$result = $db->query("SELECT xzdrz, xhyrz, xxxrz, xzyrz, zxlx FROM ".$common->tname('message_status')." WHERE playersid = '".$playersid." limit 1'");
		$rows = $db->fetch_array($result);
		
		// 判断当前增加的消息是什么类型
		$types = lettersModel::getLetterTypes();
		$type = 0;
		foreach($types as $tid=>$typeGeners){
			if(in_array($messageStatus, $typeGeners)){
				$type = $tid;
				break;
			}
		}
		
		// 修改数据
		if(!empty($rows)) {
			switch($type){
				case 0:
					$rows['xzdrz'] = isset($rows['xzdrz'])?$rows['xzdrz']+$num:$num;
					break;
				case 1:
					$rows['xhyrz'] = isset($rows['xhyrz'])?$rows['xhyrz']+$num:$num;
					break;
				case 2:
					$rows['xxxrz'] = isset($rows['xxxrz'])?$rows['xxxrz']+$num:$num;
					break;
			case 3:
				$rows['xzyrz'] = isset($rows['xzyrz'])?$rows['xzyrz']+$num:$num;
			}
			$where['playersid'] = $playersid;
			$common->updatetable('message_status',$rows,$where);
		}else{
			// 初始化数据
			$rows['zxlx']         = $type;
			$rows['messageTime']  = time();
			$rows['playersid']    = $playersid;
			$rows['messageType']  = $messageStatus;
			$rows['messageCount'] = 0;
			
			$modifyKey = 'xzdrz';
			$rows['xhyrz'] = 0;
			$rows['xxxrz'] = 0;
			$rows['xzdrz'] = 0;
			$rows['xzyrz'] = 0;
			switch($type){
				case 0:
					$rows['xzdrz'] = $num;
					break;
				case 1:
					$rows['xhyrz'] = $num;
					$modifyKey     = 'xhyrz';
					break;
				case 2:
					$rows['xxxrz'] = $num;
					$modifyKey     = 'xxxrz';
					break;
			case 3:
				$rows['xzyrz'] = $num;
				$modifyKey = 'xzyrz';
			}
			
			$colums = '';
			$insertValue = '';
			foreach($rows as $key=>$value){
				$colums .= '`'.$key.'`,';
				$insertValue .= "'".$value."',";
			}
			$colums = trim($colums, ',');
			$insertValue = trim($insertValue, ',');
			
			$insertSql = "insert into ".$common->tname('message_status')."({$colums}) values($insertValue)";
			$insertSql .= "on duplicate key update {$modifyKey}={$modifyKey}+{$num}";
			$db->query($insertSql);
		}
		
		$mc->set(MC.$playersid.'_messageStatus',$rows,0,900);
	}
	
	/**
	 * 返回未读分类消息总数量
	 *
	 * @param int $playersid
	 * @return array					包含sl数量字段的一个数组
	 */
	static function publicParameters($playersid) {
		global $common,$db,$mc;
		$value['task'] = _get('task');
		
		$rows = $mc->get(MC.$playersid.'_messageStatus');
		
		$msgStatus = 0;
		$msgCount = 0;
		
		$row['xzdrz'] = isset($rows['xzdrz'])?($rows['xzdrz']>99?99:$rows['xzdrz']):0;
		$row['xhyrz'] = isset($rows['xhyrz'])?($rows['xhyrz']>99?99:$rows['xhyrz']):0;
		$row['xxxrz'] = isset($rows['xxxrz'])?($rows['xxxrz']>99?99:$rows['xxxrz']):0;
		$row['xzyrz'] = isset($rows['xzyrz'])?($rows['xzyrz']>99?99:$rows['xzyrz']):0;
		$msgCount = $rows['xzdrz'] + $rows['xhyrz'] + $rows['xxxrz'] + $rows['xzyrz'];
		
		$value['status'] = $msgStatus;	
		$value['sl'] = $msgCount>99?99:$msgCount;
		return $value;
	}
	
	/**
	 * @author kknd li
	 * 添加一条信息到公告消息中，公告存储格式为
	 * array('notices'=>array($notice1[,$notice2,...], 'time'=>$unixTime);
	 * 公告只能包含十条消息
	 * @param String $notice
	 */
	public static function setPublicNotice($notice){
		global $mc;
		$key = MEM_PUBLIC_NOTICE_KEY;
		
		// 得到存储的公告信息
		$notices = $mc->get(MC.$key);
		
		if(empty($notices)){
			$notices = array('notices'=>array());
		}
		
		// 添加新消息，如果添加的时候数据超过10条就删到10条为止
		$notice = '<FONT FACE="新宋体" SIZE="14" COLOR="#FFFFFF">'.$notice.'</FONT>';
		$count = array_push($notices['notices'], $notice);
		
		while($count>10){
			$count--;
			array_shift($notices['notices']);
		}
		
		// 修改保存时间并存储到memcache中
		$notices['time'] = time()+3600;
		$mc->set(MC.$key, $notices, 0, 3600);
	}
	
	/**
	 * @author kknd li
	 * 获取公告消息
	 * @return 返回格式化好的公告消息，每条消息用"      "分隔
	 */
	public static function getPublicNotice(){
		global $mc;
		
		// 获取公告消息结构，具体结构定义在setPublicNotice中
		$keyPub = MEM_PUBLIC_NOTICE_KEY;
		$keySys = MEM_SYSTEM_NOTICE_KEY;
		$notices = $mc->getMulti(array(MC.$keyPub, MC.$keySys));
		
		if(empty($notices)){
			return '';
		}
		$currT = time();
		$ntcPub = isset($notices[MC.$keyPub])?$notices[MC.$keyPub]:array('time'=>$currT + 3600, 'notices'=>array());
		$ntcSys = isset($notices[MC.$keySys])?$notices[MC.$keySys]:array();
		
		// 检查公告时间，如果公告时间快到期就更新公告时间
		if(($currT + 1000) > $ntcPub['time']){
			$ntcPub['time'] = $currT + 3600;
			$mc->set(MC.$keyPub, $ntcPub, 0, 3600);
		}
		
		$sendNotice = array();
		foreach($ntcSys as $_notice){
			if($_notice['t'] > $currT){
				$sendNotice[] = $_notice['m'];
			}
		}
		$numSys = count($sendNotice);
		$numPub = count($ntcPub['notices']);
		$numPub = ($numPub + $numSys)>10?10 - $numSys:count($ntcPub['notices']);
		for($i=0; $i<$numPub; $i++){
			$addNtc = array_shift($ntcPub['notices']);
			array_push($sendNotice, $addNtc);
		}
		
		// 格式化字符串并返回
		$noticeStr = implode('       ', $sendNotice);
		return $noticeStr;
	}
	
	/**
	 * 设置系统广播内容
	 * $params       array    要广播的内容
	 * $type         int      系统广播类型
	 * 					1.逐鹿占领消息[活动名, 时间, 玩家名]
	 * 					2.逐鹿获奖消息[玩家名, 活动名, 获奖等级]
	 * 					3.逐鹿开奖消息[玩家名, 获奖等级]
	 * 					4.逐鹿开奖消息[获奖等级, 物品名]
	 * 					5.管理后台公告
	 *                  6.得分王[玩家名, 活动名]
	 */
	public static function setSysPublicNoice($params, $type){
		global $mc, $_g_lang;
		
		$msgTplList[1] = array('tmp'=>$_g_lang['letter']['sys_occupy_public'], 't'=>30);
		$msgTplList[2] = array('tmp'=>$_g_lang['letter']['sys_big_award_public'], 't'=>300);
		$msgTplList[3] = array('tmp'=>$_g_lang['letter']['sys_open_award_public'], 't'=>300);
		$msgTplList[4] = array('tmp'=>$_g_lang['letter']['sys_git_award_public'], 't'=>300);
		$msgTplList[5] = array('tmp'=>$_g_lang['letter']['sys_admin_public'], 't'=>300);
		$msgTplList[6] = array('tmp'=>$_g_lang['letter']['sys_top_scorer_public'], 't'=>300);
		
		array_unshift($params, $msgTplList[$type]['tmp']);
		$msg = call_user_func_array('sprintf', $params);
		
		$key = MEM_SYSTEM_NOTICE_KEY;
		$sysMsgs = $mc->get(MC.$key);
		
		$currT = time();
		if(!empty($sysMsgs)){
			foreach($sysMsgs as $_key=>$msgInfo){
				if($msgInfo['t'] < $currT){
					unset($sysMsgs[$_key]);
				}
			}
			$sysMsgs = array_values($sysMsgs);
		}
		
		$t = $currT + $msgTplList[$type]['t'];
		$sysMsgs[] = array('m'=>$msg, 't'=>$t);
		
		$mc->set(MC.$key, $sysMsgs, 0, 300);
	}
	
	/**
	 * 2.8.7	领取系统日志物品
	 * 
	 * @param $playersid		pid
	 * @param $lettersid		信件id
	 * @param $xxlx				消息类型
	 * @param $page				页号
	 *
	 */
	public static function lqxtriwp($playersid, $lettersid, $xxlx, $page){
		global $db,$common,$mc, $G_PlayerMgr, $_g_lang;
		$letterTypes = self::getLetterTypes();
		$typeStr     = implode(',', $letterTypes[2]);
		$sql = "SELECT request FROM ".$common->tname('letters');
		$sql .= " WHERE id = '{$lettersid}' and playersid={$playersid} and genre in ({$typeStr}) LIMIT 1";
		$query = $db->query($sql);
		$rows = $db->fetch_array($query);
		
		if(empty($rows)){
			$value = lettersModel::getMessageList($playersid, $xxlx, $page);
			$value['status'] = 1002;
			return $value;
		}
		
		/**
		 * 返回的数据结构
		 * {'tq'=>$num, 'yp'=>$num, 'yb'=>$num, 'jl'=>$num,
		 * 	'items'=>{{$id, $num, $mc},.....}
		 *  'vip'=>{pid, rmb, ingot, orderid, seqnum}
		 * }
		 */
		if (substr($rows['request'],0,1) == '{') {
			$requestGoods = json_decode($rows['request'],true);
		} else {
			$requestGoods = json_decode(gzuncompress(base64_decode($rows['request'])), TRUE);
		}
		$message = '';
		
		if(!empty($requestGoods)&&isset($requestGoods['items'])){
			// 测试是否有足够背包
			$addItemList = array();
			foreach($requestGoods['items'] as $goods){
				$addItemList[$goods['id']] = $goods['num'];
			}
			//$canAdd = toolsModel::addItems($playersid, $addItemList, $djIdList, $oldDjList);
			$player = $G_PlayerMgr->GetPlayer($playersid);
			$canAdd = $player->AddItems($addItemList);
			if($canAdd === false){
				$returnValue['status'] = 1001;
				$returnValue['message'] = $_g_lang['letter']['pack_full_to_give'];
				return $returnValue;
			}
					
			// 添加物品
			foreach($requestGoods['items'] as $goods){
				$_message = sprintf($_g_lang['letter']['get_from_letter_info'], $goods['num'], $goods['mc']);
				$message .= empty($message)?$_message:$_message;
			}
			
			$bagList = $player->GetClientBag();
			$returnValue['list'] = $bagList;
		}
		if(!empty($requestGoods)&&isset($requestGoods['vip'])){
			include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'vip_control.php');
			$vipCredits = $requestGoods['vip'];

			$vipInfo = vipChongzhi($vipCredits['pid'],
								   $vipCredits['rmb'],
								   $vipCredits['ingot'],
								   $vipCredits['orderid'],
								   $vipCredits['seqnum'],
								   true,
								   true);
			$returnValue['czyb'] = $vipCredits['ingot'];
			$returnValue['vip'] = $vipInfo['vip'];
		}
		// 对玩家添加铜钱，银票，元宝，军粮
		if($requestGoods['tq']>0
			||$requestGoods['yp']>0
			||$requestGoods['yb']>0
			||$requestGoods['jl']>0){
			$playerInfo['playersid'] = $playersid;
			if(!roleModel::getRoleInfo($playerInfo)){
				$returnValue['status']  = 26;
				$returnValue['message'] = $_g_lang['letter']['error_pinfo'];
				return $returnValue;
			}
			
			if($requestGoods['tq']>0){
				$updateRole['coins'] = $playerInfo['coins'] + $requestGoods['tq'];
				if(($upLimit = $updateRole['coins'] - COINSUPLIMIT) > 0){
					$updateRole['coins'] = COINSUPLIMIT;
					$requestGoods['tq'] -= $upLimit;
				}
				
				$returnValue['hqtq'] = $requestGoods['tq'];
				$returnValue['tq']   = $updateRole['coins'];
				$_message = sprintf($_g_lang['letter']['f_get_coins'], $returnValue['hqtq']);
				$message .= empty($message)?$_message:"，{$_message}";
			}
			
			if($requestGoods['yp']>0){
				$updateRole['silver'] = $playerInfo['silver'] + $requestGoods['yp'];
				if(($upLimit = $updateRole['silver'] - COINSUPLIMIT) > 0){
					$updateRole['silver'] = COINSUPLIMIT;
					$requestGoods['yp'] -= $upLimit;
				}
				
				$returnValue['hqyp'] = $requestGoods['yp'];
				$returnValue['yp']   = $updateRole['silver'];
				$_message = sprintf($_g_lang['letter']['f_get_silver'], $returnValue['hqyp']);
				$message .= empty($message)?$_message:"，{$_message}";
			}
			
			if($requestGoods['yb']>0){
				$updateRole['ingot'] = $playerInfo['ingot'] + $requestGoods['yb'];
				if(($upLimit = $updateRole['ingot'] - COINSUPLIMIT) > 0){
					$updateRole['ingot'] = COINSUPLIMIT;
					$requestGoods['yb'] -= $upLimit;
				}
				
				$returnValue['hqyb'] = $requestGoods['yb'];
				$returnValue['yb']   = $updateRole['ingot'];
				$_message = sprintf($_g_lang['letter']['f_get_ingot'], $returnValue['hqyb']);
				$message .= empty($message)?$_message:"，{$_message}";
			}
			
			if($requestGoods['jl']>0){
				$returnValue['hqjl'] = $requestGoods['jl'];
				$returnValue['jl']   = $playerInfo['food'] + $requestGoods['jl'];
				$updateRole['food']  = $returnValue['jl'];
				$_message = sprintf($_g_lang['letter']['f_get_grain'], $returnValue['hqjl']);
				$message .= empty($message)?$_message:"，{$_message}";
			}

			if(isset($requestGoods['sw'])&&$requestGoods['sw']>0){
				$returnValue['hqsw'] = $requestGoods['sw'];
				$returnValue['sw'] = $playerInfo['prestige'] + $requestGoods['sw'];
				$updateRole['prestige'] = $returnValue['sw'];
				$_message = sprintf($_g_lang['letter']['f_get_sw'], $returnValue['hqsw']);
				$message .= empty($message)?$_message:"，{$_message}";
			}

			$whereRole['playersid'] = $playersid;
			$common->updatetable('player', $updateRole, $whereRole); 
			$common->updateMemCache(MC.$playersid, $updateRole);
		}
		
		$letterValue = lettersModel::deleteLetters($lettersid, $playersid, $page, $xxlx);
		$returnValue = array_merge($letterValue, $returnValue);
		if(!empty($message)){
			$returnValue['message'] = $message;
		}
		return $returnValue;
	}
	
	/**
	 * 获取信件中对应的request信息
	 *
	 * @param int $playersid
	 * @param int $lettersid
	 * @return unknown
	 */
	public static function qqzdhf($playersid, $lettersid){
		global $common, $db;
		$sql = "SELECT request FROM ".$common->tname('letters');
		$sql .= " WHERE id = '{$lettersid}' and playersid={$playersid} LIMIT 1";
		$query = $db->query($sql);
		
		if(0 < mysql_num_rows($query)){
			$row = mysql_fetch_assoc($query);
			
			if (substr(trim($row['request']),0,1) == '{') {
				$tempValue = json_decode($row['request'],true);
			} else {
				$request = gzuncompress(base64_decode($row['request']));
	 			$request = str_replace('\"', '"', $request);
				$request = str_replace('\\\\\\\\', '\\', $request);
				$request = str_replace('\\\\', '\\', $request);
				$tempValue = json_decode($request,true);
			}
			
			return array('status'=>0, 'request'=>$tempValue);
		}else{
			return array('status'=>1001);
		}
	}
	
	/**
	 * 批量删除玩家的某一类信件
	 *
	 * @param int $playersid
	 * @param int $lx			信件类型	1所有需要回赠的信件
	 * 									2所有索取礼物的信件
	 * 									3所有请求帮助历练的信件
	 * 									4所有请求加好友的信件
	 */
	public static function sczdlxtb($playersid, $lx){
		global $common, $db;
		
		$whereStr = " playersid='$playersid'";
		switch($lx){
			case 1:
			$whereStr .= " and status = '1' and genre = '3'";
			break;
			case 2:
			$whereStr .= " and genre = '11'";
			break;
			case 3:
			$whereStr .= " and genre = '7'";
			break;
			case 4:
			$whereStr .= " and genre = '2'";
			break;
		}
		
		$tname = $common->tname('letters');
		$delSql = "delete from {$tname} where {$whereStr}";
		$db->query($delSql);
		
		return array('status'=>0);
	}
	
	/**
	 * 同意所有好友添加
	 *
	 * @param int $playersid
	 */
	public static function jssyhyqq($playersid){
		global $common, $db, $_g_lang;
		
		$selfInfo['playersid'] = $playersid;
		if(!$roleRes = roleModel::getRoleInfo($selfInfo)){
			$returnValue['status']  = 26;
			$returnValue['message'] = $_g_lang['letter']['error_pinfo'];
			return $returnValue;
		}
		
		$whereStr = " playersid='$playersid' and genre = '2'";
		$tname = $common->tname('letters');
		$sltSql   = "select fromplayersid from {$tname} where {$whereStr}";
		$result = $db->query($sltSql);
		
		$addIdList = array();
		while($row = $db->fetch_array($result)){
			$addIdList[] = $row['fromplayersid'];
		}
		
		if(empty($addIdList)){
			return array('status'=>21, 'message'=>$_g_lang['letter']['no_fri_request']);
		}
		
		// 获得玩家好友上限和玩家数量
		$friendIdList = roleModel::getTableRoleFriendsInfo($playersid, 1, true);
		$friendCount  = count($friendIdList);
		$friendLimit  = vipFriendLimit($selfInfo['vip']);
		
		if($friendLimit <= $friendCount){
			lettersModel::sczdlxtb($playersid, 4);
			return array('status'=>21, 'message'=>$_g_lang['letter']['friend_limit']);
		}
		
		foreach($addIdList as $otherPid){
			if($friendLimit <= $friendCount){
				break;
			}
			
			$otherInfo = array('playersid'=>$otherPid);
			if(!$roleRes = roleModel::getRoleInfo($otherInfo)){
				continue;
			}
			
			$otherFriendIds = roleModel::getTableRoleFriendsInfo($otherPid, 1, true);
			
			$otherCount  = count($otherFriendIds);
			$otherLimit  = vipFriendLimit($otherInfo['vip']);
			if($otherLimit <= $otherCount){
				continue;
			}
			if(socialModel::addFriendSocial($playersid, $otherPid)){
				$friendCount++;
			}
		}
		
		lettersModel::sczdlxtb($playersid, 4);
		
		return array('status'=>0);
	}
	
	/**
	 * 回赠全部索要的信件
	 * socailModel::setGift() $sltype=2的批量处理方式
	 *
	 * @param unknown_type $playersid
	 * @return array
	 */
	public static function zssylw($playersid){
		global $common, $db, $_g_lang;
		$social_tools = get_social_tools();
		
		$whereStr = " playersid='{$playersid}' and genre = '11'";
		$tname = $common->tname('letters');
		$sltSql   = "select fromplayersid, tradeid from {$tname} where {$whereStr}";
		$result = $db->query($sltSql);
		
		$addIdList = array();
		while($row = $db->fetch_array($result)){
			$addIdList[] = array('pid'=>$row['fromplayersid'], 'tid'=>$row['tradeid']);
		}
		
		if(empty($addIdList)){
			return array('status'=>21,'message'=>$_g_lang['letter']['no_find_sy_letter']);
		}
		
		$selfInfo['playersid'] = $playersid;
		if(!$roleRes = roleModel::getRoleInfo($selfInfo)){
			$returnValue['status']  = 26;
			$returnValue['message'] = $_g_lang['letter']['error_pinfo'];
			return $returnValue;
		}
		
		if($selfInfo['player_level']<10){
			$value['status'] = 23;   //非法的客户端请求
			$value['message'] = $_g_lang['letter']['error_level'];
			return $value;
		}
		
		$friendIDList = roleModel::getTableRoleFriendsInfo($playersid, 1, true);
		
		$sendList = array();
		$create_time = time();
		foreach($addIdList as $tradeInfo){
			$otherId   = $tradeInfo['pid'];
			$otherInfo = array('playersid'=>$otherId);
			if(!roleModel::getRoleInfo($otherInfo)){
				continue;
			}
			
			if(1==$otherInfo['is_reason']){
				continue;
			}
			
			if(10 > $otherInfo['player_level']){
				continue;
			}
			
			if(!key_exists($otherId, $friendIDList)){
				continue;
			}
			
			if(date('Y-m-d',$friendIDList[$otherId]['atime']) == date('Y-m-d')){
				continue;
			}
			
			if(socialModel::is_TodayGift($playersid, $otherId)!=1){
				continue;
			}
			
			// 送礼物品
			$sql = "select gift_type as itemId from ".$common->tname('social_trade');
			$sql .= " where id={$tradeInfo['tid']} and type=3 and toplayersid={$playersid}";
			$tradeResult = $db->query($sql);
			$giftInfo   = $db->fetch_array($tradeResult);
			$itemId = $giftInfo['itemId'];
			
		    $itemInfo = array();
		    foreach($social_tools as $s_tool){
		    	if($s_tool['id']==$itemId){
		    		$itemInfo = $s_tool;
		    		break;
		    	}
		    }
		    if(empty($itemInfo)){
				continue;
			}
			
			// 写入送礼信息，$sltype=1时直接送礼，$sltype=2时转入回赠方式
			$social_trade['type'] = 2;
			$social_trade['fromplayersid'] = $playersid;
		    $social_trade['toplayersid'] = $otherId;
	
		    $social_trade['gift_type'] = $itemId;//赠送的材料id
		    $social_trade['create_time'] = $create_time;
		    $tradeId = $common->inserttable('social_trade',$social_trade);
		    
		    // 更新双方玩家送礼缓冲数据
		    $tradeInfo = array('id'=>$tradeId,
		    					'frompid'=> $social_trade['fromplayersid'],
		    					'topid'  => $social_trade['toplayersid'],
		    					'type'   => $social_trade['type'],
		    					'itemid' => $social_trade['gift_type'],
		    					'status' => 0,
		    					'ctime'  => $social_trade['create_time']);
		    					
		    socialModel::modifyMemTradeInfo($playersid, $tradeInfo);
		    socialModel::modifyMemTradeInfo($otherId, $tradeInfo);
	    
		    // 增加好友度
		    socialModel::addFriendFeel($otherId, $playersid, 1);
		    
		    // 发信件
			$json = array();
			$json['playersid'] = $playersid;
			$json['toplayersid'] = $otherId;
			$json['message'] = array('wjmc1'=>$selfInfo['nickname'],'wjid1'=>$playersid,'lwmc'=>$itemInfo['mc'], 'lwsl'=>1);
			
			// 用来区分回赠，这里为1，letterModel::agreeGift为2
			$json['type'] = '1';
			//直接送礼类型为3，索要后回礼是4
			$json['genre'] = 4;
			$json['uc'] = '0';
			$json['create_time'] = $create_time;
			$json['tradeid'] = $tradeId;
			$result = lettersModel::addMessage($json);
		}
		
		lettersModel::sczdlxtb($playersid, 2);
		return array('status'=>0);
	}
	
	/**
	 * 2.8.8	领取所有赠送给我的礼物
	 *
	 * @param int $playersid
	 */
	public static function lqsylw($playersid){
		global $db,$common,$mc,$G_PlayerMgr, $_g_lang;
		$social_tools = get_social_tools();
		
		$whereStr = " playersid='{$playersid}' and genre in (3,4) and status=0";
		$tname = $common->tname('letters');
		$sltSql   = "select id, fromplayersid, tradeid from {$tname} where {$whereStr}";
		$result = $db->query($sltSql);
		
		$addIdList = array();
		while($row = $db->fetch_array($result)){
			$addIdList[] = array('pid'=>$row['fromplayersid'], 'tid'=>$row['tradeid'], 'letterId'=>$row['id']);
		}
		
		if(empty($addIdList)){
			return array('status'=>21, 'message'=>$_g_lang['letter']['no_find_gift']);
		}
		
		$selfInfo['playersid'] = $playersid;
		if(!$roleRes = roleModel::getRoleInfo($selfInfo)){
			$returnValue['status']  = 26;
			$returnValue['message'] = $_g_lang['letter']['error_pinfo'];
			return $returnValue;
		}
		
		$bgMdf = false;
		foreach($addIdList as $tradeInfo){
			$tradeid = $tradeInfo['tid'];
			$otherid = $tradeInfo['pid'];
			// type=1直接送礼，type=2回赠
			$result = $db->query("SELECT * FROM ".$common->tname('social_trade')." where id={$tradeid} and (type=1 or type=2)");
			$rows = $db->fetch_array($result);
			
			// 加资源
			$lettersroleInfo = $selfInfo;
			$player = $G_PlayerMgr->GetPlayer($lettersroleInfo['playersid']);
			$addStatus = $player->AddItems(array($rows['gift_type']=>1), true);
			
			if(false === $addStatus) {
				$value['status'] = 1001;
				$value['message'] = $_g_lang['letter']['pack_full_please_to'];
				if($bgMdf){
					$player->SaveItems();
					$bagList = $player->GetClientBag();
					$value['list'] = $bagList;
				}
				return $value;
			}else{
				$bgMdf = true;
			}
			
			// 修改送礼表数据
			$updateArray['status'] = 1;
			$where['id']      = $rows['id'];
			$common->updatetable('social_trade', $updateArray, $where);
			
		    // 如果送礼是发生在当天就更新数据到缓冲
		    if(date('Y-m-d', $rows['create_time'])==date('Y-m-d')){
			    $giftInfo = array('id'=>$tradeid,
			    					'frompid'=> $rows['fromplayersid'],
			    					'topid'  => $rows['toplayersid'],
			    					'type'   => $rows['type'],
			    					'itemid' => $rows['gift_type'],
			    					'status' => 1,
			    					'ctime'  => $rows['create_time']);
			    socialModel::modifyMemTradeInfo($rows['fromplayersid'], $giftInfo);
			    socialModel::modifyMemTradeInfo($rows['toplayersid'], $giftInfo);
		    }
			
			// 如果是直接送礼就修改信件状态，否则删除信件
			if($rows['type'] == 1) {
				$update['status']  = 1;
				$update['request'] = base64_encode(gzcompress(json_encode(array('itemId'=>$rows['gift_type'])),1));
				$where['id'] = $tradeInfo['letterId'];
				$common->updatetable('letters',$update,$where);
			}
			else{
				lettersModel::deleteLetters($tradeInfo['letterId'], $playersid);
			}
			
			socialModel::addFriendFeel($playersid, $otherid, 1);
		}
		
		$value['status'] = 0;
		$value['message'] = $_g_lang['letter']['get_all_gift'];
		if($bgMdf){
			$player->SaveItems();
			$bagList = $player->GetClientBag();
			$value['list'] = $bagList;
		}
		
		return $value;
	}
	
	/**
	 * 2.8.9	回赠所有送我礼物的人礼物
	 *
	 * @param int $playersid
	 */
	public static function hzsylw($playersid){
		global $db,$common,$mc,$_g_lang;
		$social_tools = get_social_tools();
		
		$whereStr = " playersid='{$playersid}'  and status = '1' and genre = '3'";
		$tname = $common->tname('letters');
		$sltSql   = "select id, fromplayersid, request, tradeid from {$tname} where {$whereStr}";
		$result = $db->query($sltSql);
		
		$addIdList = array();
		while($row = $db->fetch_array($result)){
			$addIdList[] = array('pid'=>$row['fromplayersid'], 'tid'=>$row['tradeid'], 'request'=>$row['request'], 'letterId'=>$row['id']);
		}
		
		if(empty($addIdList)){
			return array('status'=>21, 'message'=>$_g_lang['letter']['no_find_gift_letter']);
		}
		
		$roleInfo['playersid'] = $playersid;
		if(!roleModel::getRoleInfo($roleInfo)){
			$returnValue['status']  = 26;
			$returnValue['message'] = $_g_lang['letter']['error_pinfo'];
			return $returnValue;
		}
		
		$friendIDList = roleModel::getTableRoleFriendsInfo($playersid, 1, true);
		
		foreach($addIdList as $tradeInfo){
			if(!key_exists($tradeInfo['pid'], $friendIDList)){
				lettersModel::deleteLetters($tradeInfo['letterId'], $playersid);
				continue;
			}
			
			// 判断当天是否送过礼物，正常情况下用户第一天加不会送礼，所以不考虑用户第一天加的情况
			if(socialModel::is_TodayGift($playersid, $tradeInfo['pid']) != 1) {
				lettersModel::deleteLetters($tradeInfo['letterId'], $playersid);
				continue;
			}
		
			if (substr($row['request'],0,1) == '{') {
				$itemId = json_decode($row['request'], true);
			} else {
				$itemId = json_decode(gzuncompress(base64_decode($tradeInfo['request'])), true);
			}	
			$itemId = $itemId['itemId'];
			
			// 写入礼物信息
			$social_trade['type'] = 2;
			$social_trade['fromplayersid'] = $playersid;
		    $social_trade['toplayersid'] = $tradeInfo['pid'];
		    $social_trade['create_time'] = time();
		    $social_trade['gift_type'] = $itemId;//赠送的材料id
		    $tradeId = $common->inserttable('social_trade',$social_trade);
	    
		    // 更新双方玩家送礼缓冲数据
		    $giftInfo = array('id'=>$tradeId,
		    					'frompid'=> $social_trade['fromplayersid'],
		    					'topid'  => $social_trade['toplayersid'],
		    					'type'   => $social_trade['type'],
		    					'itemid' => $social_trade['gift_type'],
		    					'status' => 0,
		    					'ctime'  => $social_trade['create_time']);
		    					
		    socialModel::modifyMemTradeInfo($social_trade['fromplayersid'], $giftInfo);
		    socialModel::modifyMemTradeInfo($social_trade['toplayersid'], $giftInfo);
		
			// 写入回礼消息
		    $itemInfo = array();
		    foreach($social_tools as $s_tool){
		    	if($s_tool['id']==$itemId){
		    		$itemInfo = $s_tool;
		    		break;
		    	}
		    }
		    
			$json['toplayersid'] = $tradeInfo['pid'];
			$json['playersid']   = $playersid;
			// 消息表显示信息玩家名称，玩家id，礼物名称，礼物数量
			$json['message'] = array('wjmc1'=>$roleInfo['nickname'],'wjid1'=>$roleInfo['playersid'],'lwmc'=>$itemInfo['mc'], 'lwsl'=>1);
			// 用来区分回赠，这里为2，socialModel::setGift如果不是索要送礼为1
			$json['type'] = 2;
			$json['genre'] = 4;           //好友回礼
			$json['tradeid'] = $tradeId;
			$json['create_time'] = time();
			//$json = json_encode($json);
			$result = lettersModel::addMessage($json);//送礼
		
			// 处理好友度
			socialModel::addFriendFeel($tradeInfo['pid'], $playersid, 1);
		
			lettersModel::deleteLetters($tradeInfo['letterId'], $playersid);
			
		}
		
		$value['status'] = 0;
		
		// 回礼后的任务触发
		$rwid = questsController::OnFinish($roleInfo,"'slrs'");
		if (!empty($rwid)) {
			$value['rwid'] = $rwid;
		} 
		return $value;
	}
}