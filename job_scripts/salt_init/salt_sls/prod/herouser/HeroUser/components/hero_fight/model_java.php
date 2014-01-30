<?php
class fightModelJava {
    //获取可攻城对象列表
    public static function getAttactCityList($playersid,$level,$page=1,$regionid) {
    	global $common,$db,$mc;
    	$nowTime = time();
    	$minLevel = $level - 5;
    	if ($minLevel < 0) {
    		$minLevel = 0;
    	}
    	$maxLevel = $level + 5;
    	$numRole = $db->query("SELECT playersid FROM ".$common->tname('player')." WHERE player_level BETWEEN $minLevel AND $maxLevel && playersid != '$playersid' && regionid != '$regionid' && aggressor_playersid != '$playersid' && playersid NOT IN (SELECT playersid FROM ".$common->tname('protect')." WHERE protectTime > '$nowTime')");    	
    	$totalNum = $db->num_rows($numRole);    	
    	$db->free_result($numRole);
    	$perPage = 6;
    	if (intval($page) == 0) {
    		$page = 1;
    	}
    	$start = (intval($page) - 1) * $perPage;
    	$resultRole = $db->query("SELECT * FROM ".$common->tname('player')." WHERE player_level BETWEEN $minLevel AND $maxLevel && playersid != '$playersid' && regionid != '$regionid' && aggressor_playersid != '$playersid' && playersid NOT IN (SELECT playersid FROM ".$common->tname('protect')." WHERE protectTime > '$nowTime') ORDER BY player_level DESC LIMIT $start,$perPage");
    	$array = array();
    	while ($rows = $db->fetch_array($resultRole)) {
    		$prestige = $rows['prestige'];
    		$bhxx = $mc->get(MC.$rows['playersid'].'_peace');    //检查被攻击方是否受到保护
    		if (!empty($bhxx)) {
    			$bh = 1;
    		} else {
    			$bh = 0;
    		}
    		$temp_dir = explode("|",socialModel::getDiwei($rows['prestige']));
			$rows['dw'] = $temp_dir[0];
			if ($rows['aggressor_playersid'] == 0 || $rows['aggressor_playersid'] == $rows['playersid']) {
				$is_occupy = 0;
			} else {
				$is_occupy = 1;
			}
    		$array[] = array(
	    		'dw'=>$rows['dw'],
	    		'dj'=>intval($rows['player_level']),
	    		'xm'=>$rows['nickname'],
	    		'did'=>intval($rows['playersid']),
	    		'bh'=>$bh,
    		    'is_occupy'=>$is_occupy
    		);
    	}  
    	$db->free_result($resultRole); 
    	//查询城池保护期
    	$peaceRows = $mc->get(MC.$playersid.'_peace');
    	if (!empty($peaceRows)) {
    		$remainTime = $peaceRows['peaceTime'] - $nowTime;
    	} else {
    		$remainTime = 0;
    	}	
    	if (!empty($array)) {
    		$value['status'] = 0;   
    		$value['remainTime'] = $remainTime;	
    		$value['zs'] = $totalNum;
    		$value['list'] = $array;    		
    	} else {
    		$value['status'] = 39;   
     		$value['message'] = '没有你可攻击的城池！';    				
    	}
    	return $value;
    }
}