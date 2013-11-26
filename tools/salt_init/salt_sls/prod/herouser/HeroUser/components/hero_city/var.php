<?php 
// RMB与元宝的比值1:xxx
define('RMB_TO_YUANBAO', 10);

// 获得vip每级支付的RMB的金额
function getVipPrice(){
	return array(0, 1, 20, 50, 100, 200, 500, 1000, 2000, 5000, 10000, 20000, 50000);
}

// 获得vip开宝箱信息
function getVipGemBoxInfo(){
	return array(0=>array('tq'=>500, 'jl'=>0, 'yp'=>0, 'tools'=>array()),
				1=>array('tq'=>0, 'jl'=>0, 'yp'=>5, 'tools'=>array()),
				2=>array('tq'=>0, 'jl'=>0, 'yp'=>0, 'tools'=>array(10001=>1)),
				3=>array('tq'=>0, 'jl'=>0, 'yp'=>5, 'tools'=>array(10001=>1)),
				4=>array('tq'=>0, 'jl'=>0, 'yp'=>0, 'tools'=>array(10001=>1, 10040=>1)));
}

//可升级醒目编号
//1市场2领地3铁匠铺4酒馆5点将台6、内力
//根据建筑字段获取字段名
function buildingName($buildId) {
	$nameArray = array(1=>'sc_level',2=>'ld_level',3=>'tjp_level',4=>'jg_level',5=>'djt_level');
	return $nameArray[$buildId];
}

//资源与ID对应表
function iDToRes($resourceId) {
	$itemArray = array(1=>'ingot',2=>'silver',3=>'food',4=>'coins');
	return $itemArray[$resourceId];
}

//军粮上限
function foodUplimit($playerLevel) {
	//CEILING((60+(120*(角色等级/60)))/5)
	//return 15+ floor($playerLevel / 5);
	return ceil((60 + (120 * ($playerLevel / 60))) / 5);
}

//将领姓
function firstName() {
	global $city_var_lang;
	$firstName = $city_var_lang['firstName'];
	return $firstName;
}

//将领名
function lastName() {
	global $city_var_lang;
	$lastName = $city_var_lang['lastName'];
	return $lastName;
}

//绿色名将名称
function lsName($xj,$jwdj) {
	global $city_var_lang;
	$nameInfo = $city_var_lang['mjname'];
	//第一位颜色，第二位职业(1重甲2长枪3弓箭4轻骑5连弩)，第三位性别,第四位星级
	if ($jwdj > 1) {
		$name = array('2112_'.$nameInfo['2112_YX003'].'_YX003','2113_'.$nameInfo['2113_YX009'].'_YX009','2114_'.$nameInfo['2114_YX011'].'_YX011','2211_'.$nameInfo['2211_YX002'].'_YX002','2505_'.$nameInfo['2505_YX014'].'_YX014','2215_'.$nameInfo['2215_YX016'].'_YX016','2313_'.$nameInfo['2313_YX008'].'_YX008','2314_'.$nameInfo['2314_YX015'].'_YX015','2411_'.$nameInfo['2411_YX001'].'_YX001','2414_'.$nameInfo['2414_YX007'].'_YX007','2413_'.$nameInfo['2413_YX004'].'_YX004','2312_'.$nameInfo['2312_YX010'].'_YX010','2212_'.$nameInfo['2212_YX012'].'_YX012');
	} else {
		$name = array('2112_'.$nameInfo['2112_YX003'].'_YX003','2113_'.$nameInfo['2113_YX009'].'_YX009','2114_'.$nameInfo['2114_YX011'].'_YX011','2211_'.$nameInfo['2211_YX002'].'_YX002','2215_'.$nameInfo['2215_YX016'].'_YX016','2313_'.$nameInfo['2313_YX008'].'_YX008','2314_'.$nameInfo['2314_YX015'].'_YX015','2411_'.$nameInfo['2411_YX001'].'_YX001','2414_'.$nameInfo['2414_YX007'].'_YX007','2413_'.$nameInfo['2413_YX004'].'_YX004','2312_'.$nameInfo['2312_YX010'].'_YX010','2212_'.$nameInfo['2212_YX012'].'_YX012');
	}
	/*$chose = array();
    foreach ($name as $nameInfoValue) {
    	if (substr($nameInfoValue, 3, 1) == $xj) {
    		$chose[] = $nameInfoValue;
    	}
    }
    if (empty($chose)) {
    	$newxj = 0;
    	for ($i = 1; $i < 6; $i++) {
            foreach ($name as $nameInfoValue) {
		    	if (substr($nameInfoValue, 3, 1) == $xj - $i) {
		    		$newxj = $xj - $i;
		    		break;
		    	}
    		}
    		if ($newxj != 0) {
    			break;
    		} else {
                foreach ($name as $nameInfoValue) {
			    	if (substr($nameInfoValue, 3, 1) == $xj + $i) {
			    		$newxj = $xj + $i;
			    		break;
			    	}
    			}   			
    		} 
        	if ($newxj != 0) {
    			break;
    		}   		  		
    	}
    	if ($newxj == 0) {
    		$newxj = rand(1,5);
    	}
       	$gen = lsName($newxj,$jwdj);
    	return $gen;
    }
    $chosedKey = array_rand($chose,1);
    $gen = $chose[$chosedKey];	*/
	$chosed = array_rand($name,1);    
	return $name[$chosed];    
}

//蓝色将领名称
function nsName($xj,$jwdj) {
	global $city_var_lang;
	$nameInfo = $city_var_lang['mjname'];		
	if ($jwdj > 6) {
		$name = array('3411_'.$nameInfo['3411_YX006'].'_YX006','3112_'.$nameInfo['3112_YX018'].'_YX018','3113_'.$nameInfo['3113_YX022'].'_YX022','3515_'.$nameInfo['3515_YX026'].'_YX026','3212_'.$nameInfo['3212_YX020'].'_YX020','3214_'.$nameInfo['3214_YX024'].'_YX024','3413_'.$nameInfo['3413_YX021'].'_YX021','3313_'.$nameInfo['3313_YX023'].'_YX023','3314_'.$nameInfo['3314_YX027'].'_YX027','3412_'.$nameInfo['3412_YX019'].'_YX019','3415_'.$nameInfo['3415_YX028'].'_YX028','3301_'.$nameInfo['3301_YX017'].'_YX017','3114_'.$nameInfo['3114_YX025'].'_YX025'); //第一位颜色，第二位职业(1重甲2长枪3弓箭4轻骑5连弩)，第三位性别
	} else {
		$name = array('3411_'.$nameInfo['3411_YX006'].'_YX006','3112_'.$nameInfo['3112_YX018'].'_YX018','3113_'.$nameInfo['3113_YX022'].'_YX022','3212_'.$nameInfo['3212_YX020'].'_YX020','3214_'.$nameInfo['3214_YX024'].'_YX024','3413_'.$nameInfo['3413_YX021'].'_YX021','3313_'.$nameInfo['3313_YX023'].'_YX023','3314_'.$nameInfo['3314_YX027'].'_YX027','3412_'.$nameInfo['3412_YX019'].'_YX019','3415_'.$nameInfo['3415_YX028'].'_YX028','3301_'.$nameInfo['3301_YX017'].'_YX017','3114_'.$nameInfo['3114_YX025'].'_YX025'); //第一位颜色，第二位职业(1重甲2长枪3弓箭4轻骑5连弩)，第三位性别
	}
	/*$chose = array();
	foreach ($name as $nameInfoValue) {
    	if (substr($nameInfoValue, 3, 1) == $xj) {
    		$chose[] = $nameInfoValue;
    	}
    }
    if (empty($chose)) {
    	$newxj = 0;
    	for ($i = 1; $i < 6; $i++) {
            foreach ($name as $nameInfoValue) {
		    	if (substr($nameInfoValue, 3, 1) == $xj - $i) {
		    		$newxj = $xj - $i;
		    		break;
		    	}
    		}
    		if ($newxj != 0) {
    			break;
    		} else {
                foreach ($name as $nameInfoValue) {
			    	if (substr($nameInfoValue, 3, 1) == $xj + $i) {
			    		$newxj = $xj + $i;
			    		break;
			    	}
    			}   			
    		} 
        	if ($newxj != 0) {
    			break;
    		}   		  		
    	}
    	if ($newxj == 0) {
    		$newxj = rand(1,5);
    	}
       	$gen = nsName($newxj,$jwdj);
    	return $gen;
    }  
    $chosedKey = array_rand($chose,1);
    $gen = $chose[$chosedKey];	    
	return $gen;*/
	$chosed = array_rand($name,1);    
	return $name[$chosed]; 	
}

//紫色将领
function zsName($xj,$jwdj) {
	global $city_var_lang;
	$nameInfo = $city_var_lang['mjname'];	
	if ($jwdj > 11) {
		$name = array('4313_'.$nameInfo['4313_YX005'].'_YX005','4212_'.$nameInfo['4212_YX013'].'_YX013','4412_'.$nameInfo['4412_YX031'].'_YX031','4301_'.$nameInfo['4301_YX030'].'_YX030','4411_'.$nameInfo['4411_YX029'].'_YX029','4113_'.$nameInfo['4113_YX035'].'_YX035','4114_'.$nameInfo['4114_YX040'].'_YX040','4212_'.$nameInfo['4212_YX033'].'_YX033','4515_'.$nameInfo['4515_YX039'].'_YX039','4314_'.$nameInfo['4314_YX038'].'_YX038','4204_'.$nameInfo['4204_YX037'].'_YX037','4415_'.$nameInfo['4415_YX041'].'_YX041','4213_'.$nameInfo['4213_YX036'].'_YX036'); //第一位颜色，第二位职业(1重甲2长枪3弓箭4轻骑5连弩)，第三位性别
	} else {
		$name = array('4313_'.$nameInfo['4313_YX005'].'_YX005','4212_'.$nameInfo['4212_YX013'].'_YX013','4412_'.$nameInfo['4412_YX031'].'_YX031','4301_'.$nameInfo['4301_YX030'].'_YX030','4411_'.$nameInfo['4411_YX029'].'_YX029','4113_'.$nameInfo['4113_YX035'].'_YX035','4114_'.$nameInfo['4114_YX040'].'_YX040','4212_'.$nameInfo['4212_YX033'].'_YX033','4314_'.$nameInfo['4314_YX038'].'_YX038','4204_'.$nameInfo['4204_YX037'].'_YX037','4415_'.$nameInfo['4415_YX041'].'_YX041','4213_'.$nameInfo['4213_YX036'].'_YX036'); //第一位颜色，第二位职业(1重甲2长枪3弓箭4轻骑5连弩)，第三位性别
	}
	//return $name[array_rand($name,1)];
	/*$chose = array();
    foreach ($name as $nameInfoValue) {
    	if (substr($nameInfoValue, 3, 1) == $xj) {
    		$chose[] = $nameInfoValue;
    	}
    }
    if (empty($chose)) {
    	$newxj = 0;
    	for ($i = 1; $i < 6; $i++) {
            foreach ($name as $nameInfoValue) {
		    	if (substr($nameInfoValue, 3, 1) == $xj - $i) {
		    		$newxj = $xj - $i;
		    		break;
		    	}
    		}
    		if ($newxj != 0) {
    			break;
    		} else {
                foreach ($name as $nameInfoValue) {
			    	if (substr($nameInfoValue, 3, 1) == $xj + $i) {
			    		$newxj = $xj + $i;
			    		break;
			    	}
    			}   			
    		} 
        	if ($newxj != 0) {
    			break;
    		}   		  		
    	}
    	if ($newxj == 0) {
    		$newxj = rand(1,5);
    	}
       	$gen = zsName($newxj,$jwdj);
    	return $gen;
    } 
    $chosedKey = array_rand($chose,1);
    $gen = $chose[$chosedKey];	    
	return $gen;*/
	$chosed = array_rand($name,1);    
	return $name[$chosed]; 	
}

//橙色将领
function csName($xj,$jwdj) {
	global $city_var_lang;
	$nameInfo = $city_var_lang['mjname'];		
	if ($jwdj > 15) {
		$name = array('5311_'.$nameInfo['5311_YX032'].'_YX032','5513_'.$nameInfo['5513_YX034'].'_YX034','5112_'.$nameInfo['5112_YX042'].'_YX042','5214_'.$nameInfo['5214_YX043'].'_YX043','5415_'.$nameInfo['5415_YX044'].'_YX044'); //第一位颜色，第二位职业(1重甲2长枪3弓箭4轻骑5连弩)，第三位性别
	} else {
		$name = array('5311_'.$nameInfo['5311_YX032'].'_YX032','5112_'.$nameInfo['5112_YX042'].'_YX042','5214_'.$nameInfo['5214_YX043'].'_YX043','5415_'.$nameInfo['5415_YX044'].'_YX044'); //第一位颜色，第二位职业(1重甲2长枪3弓箭4轻骑5连弩)，第三位性别
	}
	//return $name[array_rand($name,1)];	
	/*$chose = array();
    foreach ($name as $nameInfoValue) {
    	if (substr($nameInfoValue, 3, 1) == $xj) {
    		$chose[] = $nameInfoValue;
    	}
    }
    if (empty($chose)) {
    	$newxj = 0;
    	for ($i = 1; $i < 6; $i++) {
            foreach ($name as $nameInfoValue) {
		    	if (substr($nameInfoValue, 3, 1) == $xj - $i) {
		    		$newxj = $xj - $i;
		    		break;
		    	}
    		}
    		if ($newxj != 0) {
    			break;
    		} else {
                foreach ($name as $nameInfoValue) {
			    	if (substr($nameInfoValue, 3, 1) == $xj + $i) {
			    		$newxj = $xj + $i;
			    		break;
			    	}
    			}   			
    		} 
        	if ($newxj != 0) {
    			break;
    		}   		  		
    	}
    	if ($newxj == 0) {
    		$newxj = rand(1,5);
    	}
       	$gen = csName($newxj,$jwdj);
    	return $gen;
    }
    $chosedKey = array_rand($chose,1);
    $gen = $chose[$chosedKey];	    
	return $gen;*/
	$chosed = array_rand($name,1);    
	return $name[$chosed]; 		
}

//名将名称列表
function mjnamelist($name) {
	global $city_var_lang;
	//第一位颜色，第二位职业(1重甲2长枪3弓箭4轻骑5连弩)，第三位性别,第四位星级
	$name_list = $city_var_lang['mjname'];
	$key = array_search($name,$name_list);
    return $key;
}

//妖将列表
function yjlist() {
	global $city_var_lang;
	$name_list = $city_var_lang['yjlist'];
	$chosed = array_rand($name_list,1);
	return $chosed;
}



//可统领的武将上限 
function guideValue($level) {
	if ($level == 1) {
		return 3;
	}
	return $level * 2;
}

//建筑升级别要求数据1市场2领地3铁匠铺4酒馆5点将台
function requestLevel($upgradeId,$requestLevel) {
	global $city_var_lang;
	if ($upgradeId == 1) {           //市场
		$level = array(0=>0,1=>4,2=>7,3=>12,4=>17,5=>22,6=>27,7=>32,8=>37,9=>42,10=>47,11=>52,12=>$city_var_lang['requestLevel'],13=>$city_var_lang['requestLevel']);	
	} elseif ($upgradeId == 2) {     //领地
		$level = array(0=>0,1=>12,2=>17,3=>22,4=>27,5=>32,6=>37,7=>42,8=>47,9=>52,10=>57,11=>62,12=>$city_var_lang['requestLevel'],13=>$city_var_lang['requestLevel']);
	} elseif ($upgradeId == 3)	{    //铁匠铺
		$level = array(0=>0,1=>5,2=>8,3=>11,4=>14,5=>17,6=>20,7=>23,8=>26,9=>29,10=>32,11=>35,12=>$city_var_lang['requestLevel'],13=>$city_var_lang['requestLevel']);
	} elseif ($upgradeId == 4) {     //酒馆
		$level = array(0=>0,1=>6,2=>8,3=>13,4=>18,5=>23,6=>28,7=>33,8=>38,9=>43,10=>48,11=>53,12=>$city_var_lang['requestLevel'],13=>$city_var_lang['requestLevel']);
	} elseif ($upgradeId == 5) {     //点将台
		$level = array(0=>0,1=>5,2=>8,3=>12,4=>16,5=>20,6=>24,7=>28,8=>32,9=>36,10=>40,11=>44,12=>$city_var_lang['requestLevel'],13=>$city_var_lang['requestLevel']);
	} else {
		return 10000;
	}
	return $level[$requestLevel - 1];
}

//升级所需资源
function needMoney($upgradeId,$requestLevel) {
	$costLevel = $requestLevel - 1;	
	if ($upgradeId == 1) {           //市场
       $xytq = ceil((pow($costLevel,3)*3.2+$costLevel*100+50)/50) * 50;;
       //$xycl = ceil(pow($requestLevel,1.2)*1.2+$requestLevel*0.3+0.3);              //绢布
       $xycl = $costLevel * 2;
	} elseif ($upgradeId == 2) {     //领地
       $xytq = ceil((pow($costLevel,3)*3.2+$costLevel*100+50)/50) * 50;;
       $xycl = $costLevel * 2;
       //$xycl = ceil(pow($requestLevel,1.2)*1.2+$requestLevel*0.3+0.3);              //木材
    } elseif ($upgradeId == 3)	{    //铁匠铺
       $xytq = ceil((pow($costLevel,3)*3.2+$costLevel*100+50)/50) * 50;;
       //$xycl = ceil(pow($requestLevel,1.2)*1.2+$requestLevel*0.3+0.3);              //铁矿
       $xycl = $costLevel * 2;
    } elseif ($upgradeId == 4) {     //酒馆
       $xytq = ceil((pow($costLevel,3)*3.2+$costLevel*100+50)/50) * 50;;
       //$xycl = ceil(pow($requestLevel,1.2)*1.2+$requestLevel*0.3+0.3);                 //石材
       $xycl = $costLevel * 2;
    } elseif ($upgradeId == 5) {     //点将台
       $xytq = ceil((pow($costLevel,3)*3.2+$costLevel*100+50)/50) * 50;;
       //$xycl = ceil(pow($requestLevel,1.2)*1.2+$requestLevel*0.3+0.3);             //陶土
       $xycl = $costLevel * 2;
    } else {
		return array(100000000000,100000000000);
	}	
	return array($xytq,$xycl);
}

//征税间隔时间 默认7200
function collectNeedTime() {
	//return 7200;
	if (isset($_SESSION['playersid'])) {
		$roleInfo ['playersid'] = $_SESSION['playersid'];	
		$roleRes = roleModel::getRoleInfo ( $roleInfo );
	} else {
		$roleInfo['ykzjrl'] = 0;
	}	
	$sccd = 120;
	switch(intval($roleInfo['ykzjrl'])) {
		case 10:
			$sccd = 132;
			break;
		case 25:
			$sccd = 150;
			break;
		case 50:
			$sccd = 180;
			break;
		case 75:
			$sccd = 210;
			break;
		case 100:
			$sccd = 240;
			break;
		default:				
			break;
	}
	
	return $sccd * 60;
}


//计算升级需要元宝数量
function getUpgradeNeedYb($yl) {
	//1:1 (<10) 1:0.9 (>=10 && < 20) 1:0.85 (>=20 && < 30) 1:0.8 (>=30 && < 40) 1:0.75 (>=40 && < 50) 1:0.7 (>50)
	if ($yl == 0) {
		return 1;
	}
	/*if ($yl >= 1 && $yl < 10) {
		$rate = 1;
	} elseif ($yl >=10 && $yl < 20) {
		$rate = 0.9;
	} elseif ($yl >= 20 && $yl < 30) {
		$rate = 0.85;
	} elseif ($yl >= 30 && $yl < 40) {
		$rate = 0.8;
	} elseif ($yl >= 40 && $yl < 50) {
		$rate = 0.75;
	} else {
		$rate = 0.7;
	}
	return round($yl * $rate,0);*/
	return $yl;
}

//征收获得的收益
function getCoinsAmount($level) {
	global $city_var_lang;
	if ($level > 12) {
		return $city_var_lang['requestLevel'];
	}
	$resource = ceil((pow($level,2.6)*4.85+$level*100+650) / 50) * 50;	
	return $resource;
}

//补血所需铜钱,$level内力等级
function addLifeCost($level) {
	/*if ($level > 0 && $level < 31) {
		return 0.01;
	} elseif ($level > 30 && $level < 61) {
		return 0.02;
	} else {
		return 0.03;
	}*/
	return 0.01;	
}


//打怪获取金钱数量$mlv怪物等级
function dghqtq($mlv) {
	//CEILING(POWER(怪物等级,0.9)*0.6+怪物等级*0.88+138,1)
	return ceil(pow($mlv,0.9) * 0.6 + $mlv * 0.88 + 138);
}

//历练所需元宝
function llyb($llcs) {
	global $city_var_lang;
	$info = array(0=>0,1=>5,2=>10,3=>15,4=>20,5=>25,6=>30,7=>35,8=>40,9=>45,10=>50,11=>55,12=>60,13=>65,14=>70,15=>75,16=>80,17=>85,18=>90,19=>95,20=>100,21=>105,22=>110,23=>115,24=>120,25=>125,26=>130,27=>135,28=>140,29=>145,30=>150,31=>155,32=>160,33=>165,34=>170,35=>175,36=>180,37=>185,38=>190,39=>195,40=>200,41=>205,42=>210,43=>215,44=>220,45=>225,46=>230,47=>235,48=>240,49=>245,50=>250,51=>$city_var_lang['requestLevel']);
	return $info[$llcs];
}
//历练所消耗银两
function llyl($llcs) {
	global $city_var_lang;
	$info = array(0=>0,1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>2,12=>4,13=>6,14=>8,15=>10,16=>12,17=>14,18=>16,19=>18,20=>20,21=>15,22=>30,23=>45,24=>60,25=>75,26=>90,27=>105,28=>120,29=>135,30=>150,31=>20,32=>40,33=>60,34=>80,35=>100,36=>120,37=>140,38=>160,39=>180,40=>200,41=>25,42=>50,43=>75,44=>100,45=>125,46=>150,47=>175,48=>200,49=>225,50=>250,51=>$city_var_lang['requestLevel']);
	return $info[$llcs];
}
//历练所需人数
function llrs($llcs) {
	global $city_var_lang;
	$info = array(0=>0,1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>2,12=>4,13=>6,14=>8,15=>10,16=>12,17=>14,18=>16,19=>18,20=>20,21=>3,22=>6,23=>9,24=>12,25=>15,26=>18,27=>21,28=>24,29=>27,30=>30,31=>4,32=>8,33=>12,34=>16,35=>20,36=>24,37=>28,38=>32,39=>36,40=>40,41=>5,42=>10,43=>15,44=>20,45=>25,46=>30,47=>35,48=>40,49=>45,50=>50,51=>$city_var_lang['requestLevel']);
	return $info[$llcs];
}

//历练cd时间 小时为 单位
function llcdxs($llcs) {
	global $city_var_lang;
	$info = array(0=>0,1=>2,2=>3,3=>4,4=>5,5=>6,6=>7,7=>8,8=>9,9=>10,10=>11,11=>12,12=>13,13=>14,14=>15,15=>16,16=>17,17=>18,18=>19,19=>20,20=>21,21=>22,22=>23,23=>24,24=>25,25=>26,26=>27,27=>28,28=>29,29=>30,30=>31,31=>32,32=>33,33=>34,34=>35,35=>36,36=>37,37=>38,38=>39,39=>40,40=>41,41=>42,42=>43,43=>44,44=>45,45=>46,46=>47,47=>48,48=>49,49=>50,50=>51,51=>$city_var_lang['requestLevel']);
	return ($llcs==0) ? 0 : $info[$llcs];
}

//历练清楚CD所需银两 
function llcd($difftime) {
	$temp_int = intval($difftime/3600);
	//$temp_int = ($temp_int == 0) ? 1 : $temp_int;
	$temp_float = $difftime%3600;
	if($temp_float > 0) {
		return ($temp_int + 1) * 5;
	} else {
		return ($temp_int) * 5;
	}
}

//计算占领资源收益
function occupied_income($gid, $playersid) {
	global $common, $db;
	$generalInfo = cityModel::getGeneralData ( $playersid, '', $gid, 0 );
	$last_income_time = $generalInfo [0] ['last_income_time'];
	$diffTime = time () - $last_income_time;
	$incomeTimes = floor ( $diffTime / 14400 );
	if ($diffTime >= 14400) {
		$value ['time'] = 0;
	} else {
		$value ['time'] = 14400 - ($diffTime % 14400);
	}
	//$value['income'] = fightModel::zlsy($dj,$dwdj,$time,$type); //占领收益，数值未写入
	return $value;
}
/*
 * 武将要求达到的等级 = 5*内力 + 6
提升内力需要消耗银两和铜钱，其消耗公式为：
消耗铜钱 = 500*(内力-1) + 600
消耗银两 = 10*内力

 * */

//合成花费铜钱
function hctq($jj) {
	$xh = array();
	switch ($jj) {
			case 1:
  				$xh[] = 10000;	//铜钱
  				$xh[] = 100;		//元宝
  				$xh[] = 80;		//几率
  				break;
			case 2:
				$xh[] = 20000;
				$xh[] = 150;
				$xh[] = 70;
				break;
			case 3:
				$xh[] = 40000;
				$xh[] = 200;
				$xh[] = 60;
				break;
			case 4:
				$xh[] = 80000;
				$xh[] = 250;
				$xh[] = 50;
				break;
	}
	return $xh;
}

//技能升级数据
function jnsj($jndj) {
	//CEILING(1-(POWER(F19,$G$14)*$H$14+F19*$I$14+$J$14)/100,0.01)
	//CEILING((1-J21)*100*$N$2,1)
	//$jl = ceil((1-(pow($jndj,0.88)*7.8+$jndj*6+13)/100)/0.01) * 0.01;
	if ($jndj > 8) {
		return false;
	}
	$jl = array(1=>1,2=>0.9,3=>0.8,4=>0.7,5=>0.6,6=>0.5,7=>0.4,8=>0.3);
	if ($jndj >= 1 && $jndj <= 3) {
		$jns = 1;
	} elseif ($jndj >= 4 && $jndj <= 6) {
		$jns = 2;
	} elseif ($jndj >= 7 && $jndj <= 8) {
		$jns = 3;
	} else {
		$jns = 100000000;
	}
    $yb = array(1=>0,2=>10,3=>20,4=>30,5=>40,6=>50,7=>60,8=>70);
	$cost = array('tq' => ($jndj - 1) * 1000,'jl' => $jl[$jndj],'yb'=>$yb[$jndj],'jns'=>$jns);
	return $cost;
}

//技能卡对应信息
function jnk($jnid,$jndj = 1) {
	global $city_var_lang;
	$jnMcData = $city_var_lang['jnk_1'];
	$msData = $city_var_lang['jnk_2'];
	$xxmsData = $city_var_lang['jnk_3'];
	$jnInfo = array(
	  1 => array('n' => $jnMcData[1],'iconid'=>'JN001','djxx'=>jnsjxx($jndj,1),'ms'=>$msData[1],'xxms'=>$xxmsData[1]),
	  2 => array('n' => $jnMcData[2],'iconid'=>'JN002','djxx'=>jnsjxx($jndj,2),'ms'=>$msData[1],'xxms'=>$xxmsData[2]),
	  3 => array('n' => $jnMcData[3],'iconid'=>'JN003','djxx'=>jnsjxx($jndj,3),'ms'=>$msData[2],'xxms'=>$xxmsData[3]),
	  4 => array('n' => $jnMcData[4],'iconid'=>'JN004','djxx'=>jnsjxx($jndj,4),'ms'=>$msData[3],'xxms'=>$xxmsData[4]),
	  5 => array('n' => $jnMcData[5],'iconid'=>'JN005','djxx'=>jnsjxx($jndj,5),'ms'=>$msData[1],'xxms'=>$xxmsData[5]),
	  6 => array('n' => $jnMcData[6],'iconid'=>'JN006','djxx'=>jnsjxx($jndj,6),'ms'=>$msData[1],'xxms'=>$xxmsData[6]),
	  7 => array('n' => $jnMcData[7],'iconid'=>'JN007','djxx'=>jnsjxx($jndj,7),'ms'=>$msData[1],'xxms'=>$xxmsData[7]),
	  8 => array('n' => $jnMcData[8],'iconid'=>'JN008','djxx'=>jnsjxx($jndj,8),'ms'=>$msData[4],'xxms'=>$xxmsData[8]),
	  9 => array('n' => $jnMcData[9],'iconid'=>'JN009','djxx'=>jnsjxx($jndj,9),'ms'=>$msData[4],'xxms'=>$xxmsData[9]),
	  10 => array('n' => $jnMcData[10],'iconid'=>'JN010','djxx'=>jnsjxx($jndj,10),'ms'=>$msData[5],'xxms'=>$xxmsData[10]),
	  11 => array('n' => $jnMcData[11],'iconid'=>'JN011','djxx'=>jnsjxx($jndj,11),'ms'=>$msData[6],'xxms'=>$xxmsData[11]),
	  12 => array('n' => $jnMcData[12],'iconid'=>'JN012','djxx'=>jnsjxx($jndj,12),'ms'=>$msData[7],'xxms'=>$xxmsData[12]),
	  13 => array('n' => $jnMcData[13],'iconid'=>'JN013','djxx'=>jnsjxx($jndj,13),'ms'=>$msData[8],'xxms'=>$xxmsData[13]),
	  14 => array('n' => $jnMcData[14],'iconid'=>'JN014','djxx'=>jnsjxx($jndj,14),'ms'=>$msData[9],'xxms'=>$xxmsData[14]),
	  15 => array('n' => $jnMcData[15],'iconid'=>'JN015','djxx'=>jnsjxx($jndj,15),'ms'=>$msData[9],'xxms'=>$xxmsData[15]),
	  16 => array('n' => $jnMcData[16],'iconid'=>'JN016','djxx'=>jnsjxx($jndj,16),'ms'=>$msData[10],'xxms'=>$xxmsData[16]),
	  17 => array('n' => $jnMcData[17],'iconid'=>'JN017','djxx'=>jnsjxx($jndj,17),'ms'=>$msData[11],'xxms'=>$xxmsData[17])
	);
	return $jnInfo[$jnid];
}

//技能升级信息$jndj（技能等级）
function jnsjxx($jndj,$jnid) {
  global $city_var_lang;
  $data = $city_var_lang['jnsjxx'];
  static $info =null;  
  if(!$info)  $info   = array(
		1=>array(1=>$data[1].'170%',
		         2=>$data[1].'175%',
		         3=>$data[1].'180%',
		         4=>$data[1].'185%',
		         5=>$data[1].'190%',
		         6=>$data[1].'195%',
		         7=>$data[1].'200%',
		         8=>$data[1].'205%',
		         9=>$city_var_lang['requestLevel']
		         ),
		2=>array(1=>$data[1].'+400',
		         2=>$data[1].'+450',
		         3=>$data[1].'+500',
		         4=>$data[1].'+550',
		         5=>$data[1].'+600',
		         6=>$data[1].'+650',
		         7=>$data[1].'+700',
		         8=>$data[1].'+750',
		         9=>$city_var_lang['requestLevel']
		         ),	
		3=>array(1=>$data[1].'350%',
		         2=>$data[1].'355%',
		         3=>$data[1].'360%',
		         4=>$data[1].'365%',
		         5=>$data[1].'370%',
		         6=>$data[1].'375%',
		         7=>$data[1].'380%',
		         8=>$data[1].'385%',
		         9=>$city_var_lang['requestLevel']
		         ),		
		4=>array(1=>$data[1].'120%',
		         2=>$data[1].'125%',
		         3=>$data[1].'130%',
		         4=>$data[1].'135%',
		         5=>$data[1].'140%',
		         6=>$data[1].'145%',
		         7=>$data[1].'150%',
		         8=>$data[1].'155%',
		         9=>$city_var_lang['requestLevel']	
		         ),	
		5=>array(1=>$data[1].'50%',
		         2=>$data[1].'55%',
		         3=>$data[1].'60%',
		         4=>$data[1].'65%',
		         5=>$data[1].'70%',
		         6=>$data[1].'75%',
		         7=>$data[1].'80%',
		         8=>$data[1].'85%',
		         9=>$city_var_lang['requestLevel']	
		         ),		
		6=>array(1=>$data[1].'60%，90%，150%',
		         2=>$data[1].'65%，95%，155%',
		         3=>$data[1].'70%，100%，160%',
		         4=>$data[1].'75%，105%，165%',
		         5=>$data[1].'80%，110%，170%',
		         6=>$data[1].'85%，115%，175%',
		         7=>$data[1].'90%，120%，180%',
		         8=>$data[1].'95%，125%，185%',
		         9=>$city_var_lang['requestLevel']
		         ),	  
		7=>array(1=>$data[1].'150%，90%，60%',
		         2=>$data[1].'155%，95%，65%',
		         3=>$data[1].'160%，100%，70%',
		         4=>$data[1].'165%，105%，75%',
		         5=>$data[1].'170%，110%，80%',
		         6=>$data[1].'175%，115%，85%',
		         7=>$data[1].'180%，120%，90%',
		         8=>$data[1].'185%，125%，95%',
		         9=>$city_var_lang['requestLevel']
		         ),	
		8=>array(1=>$data[2].'320%',
		         2=>$data[2].'325%',
		         3=>$data[2].'330%',
		         4=>$data[2].'335%',
		         5=>$data[2].'340%',
		         6=>$data[2].'345%',
		         7=>$data[2].'350%',
		         8=>$data[2].'355%',
		         9=>$city_var_lang['requestLevel']	
		         ),	
		9=>array(1=>$data[2].'170%',
		         2=>$data[2].'175%',
		         3=>$data[2].'180%',
		         4=>$data[2].'185%',
		         5=>$data[2].'190%',
		         6=>$data[2].'195%',
		         7=>$data[2].'200%',
		         8=>$data[2].'205%',
		         9=>$city_var_lang['requestLevel']	
		         ),	
		10=>array(1=>$data[3].'120%'.$data[8],
		         2=>$data[3].'125%'.$data[8],
		         3=>$data[3].'130%'.$data[8],
		         4=>$data[3].'135%'.$data[8],
		         5=>$data[3].'140%'.$data[8],
		         6=>$data[3].'145%'.$data[8],
		         7=>$data[3].'150%'.$data[8],
		         8=>$data[3].'155%'.$data[8],
		         9=>$city_var_lang['requestLevel']
		         ),		
		11=>array(1=>$data[7].'140%',
		         2=>$data[7].'150%',
		         3=>$data[7].'160%',
		         4=>$data[7].'170%',
		         5=>$data[7].'180%',
		         6=>$data[7].'190%',
		         7=>$data[7].'200%',
		         8=>$data[7].'210%',
		         9=>$city_var_lang['requestLevel']	
		         ),		 
		12=>array(1=>$data[4].'2%',
		         2=>$data[4].'4%',
		         3=>$data[4].'6%',
		         4=>$data[4].'8%',
		         5=>$data[4].'10%',
		         6=>$data[4].'12%',
		         7=>$data[4].'14%',
		         8=>$data[4].'16%',
		         9=>$city_var_lang['requestLevel']	
		         ),	
		13=>array(1=>$data[1].'170%',
		         2=>$data[1].'175%',
		         3=>$data[1].'180%',
		         4=>$data[1].'185%',
		         5=>$data[1].'190%',
		         6=>$data[1].'195%',
		         7=>$data[1].'200%',
		         8=>$data[1].'205%',
		         9=>$city_var_lang['requestLevel']	
		         ),		
		14=>array(1=>$data[5].'1000',
		         2=>$data[5].'1500',
		         3=>$data[5].'2000',
		         4=>$data[5].'2500',
		         5=>$data[5].'3000',
		         6=>$data[5].'3500',
		         7=>$data[5].'4000',
		         8=>$data[5].'4500',
		         9=>$city_var_lang['requestLevel']	
		         ),	
		15=>array(1=>$data[6].'10%',
		         2=>$data[6].'15%',
		         3=>$data[6].'20%',
		         4=>$data[6].'25%',
		         5=>$data[6].'30%',
		         6=>$data[6].'35%',
		         7=>$data[6].'40%',
		         8=>$data[6].'45%',
		         9=>$city_var_lang['requestLevel']
		         ),
		16=>array(1=>$data[1].'200%'.$data[9].'30%',
		         2=>$data[1].'210%'.$data[9].'35%',
		         3=>$data[1].'220%'.$data[9].'40%',
		         4=>$data[1].'230%'.$data[9].'45%',
		         5=>$data[1].'240%'.$data[9].'50%',
		         6=>$data[1].'250%'.$data[9].'55%',
		         7=>$data[1].'260%'.$data[9].'60%',
		         8=>$data[1].'270%'.$data[9].'65%',
		         9=>$city_var_lang['requestLevel']
		         ),	
		17=>array(1=>$data[1].'80%'.$data[10].'30%'.$data[11],
		         2=>$data[1].'85%'.$data[10].'27%'.$data[11],
		         3=>$data[1].'90%'.$data[10].'24%'.$data[11],
		         4=>$data[1].'95%'.$data[10].'21%'.$data[11],
		         5=>$data[1].'100%'.$data[10].'18%'.$data[11],
		         6=>$data[1].'105%'.$data[10].'15%'.$data[11],
		         7=>$data[1].'110%'.$data[10].'12%'.$data[11],
		         8=>$data[1].'115%'.$data[10].'9%'.$data[11],
		         9=>$city_var_lang['requestLevel']
		         )		         		         	         		         	         	                 	         	         	         	                         		                  
	);
	return $info[$jnid][$jndj];
}

//当天第一秒和最后一秒的时间戳
function jtsjc(&$dataInfo) {
	$date_now = $dataInfo;
	$year=((int)substr($date_now,0,4));//取得年份
	$month=((int)substr($date_now,5,2));//取得月份
	$day=((int)substr($date_now,8,2));//取得几号
	$dataInfo = array();
	$dataInfo[] = mktime(0,0,0,$month,$day,$year);
	$dataInfo[] = mktime(23,59,59,$month,$day,$year);
}

//开宝箱数值
function bxsz($i) {		//铜钱、军粮、材料 1，2，3
	if($i == 1) {
		$info = array(0=>300,1=>60);//60%
	} elseif($i == 2) {
		$info = array(0=>1,1=>30);//30%
	} elseif($i == 3) {
		$info = array(0=>1,1=>10);//10%
	}
	return $info;
}

//vip开宝箱次数
function vipcs($level) {
	if($level == 0) {
		$info = 10;
	} elseif($level == 1) {
		$info = 15;
	} elseif($level == 2) {
		$info = 20;
	} elseif($level == 3) {
		$info = 25;
	} elseif($level == 4) {
		$info = 30;
	}elseif($level == 5){
		$info = 35;
	}elseif($level == 6){
		$info = 40;
	}elseif($level == 7){
		$info = 45;
	}elseif($level >12){//超出最高vip时返回false
		return false;
	}elseif($level >= 8){
		$info = 50;
	}

	return $info;
}
//元宝材料兑换数据$clid材料ID；$sl数量
/**
 * 石材   10032
木材   10033
陶土   10034
铁矿   10035
绢布   10036
 * */
function ybtocl($clid,$sl) {
	return $sl * 5;
}

//送礼材料id
function idtomc($id) {
	global $city_var_lang;
	$data = $city_var_lang['idtomc'];
	$item = array('10032'=>$data[1],'10033'=>$data[2],'10034'=>$data[3],'10035'=>$data[4],'10036'=>$data[5],'20018'=>$data[6],'20019'=>$data[7],'20020'=>$data[8],'20021'=>$data[9],'20022'=>$data[10]);
	return $item[$id];
}

//挖矿时间限制  玩家操作间隔时间秒为单位/次数/偷矿时间限制
function wksjxz() {
	$info = array(180,10,1800);
	return $info;
}

//送礼保护时间
function slbhsj() {
	$info = 10800;
	return $info;
}

//历练增加属性  bz兵种 professional 1，重甲将领  2，长枪将领  3，弓箭将领，4，轻骑将领，5，连弩将领
function llzjsx($bz) {
	switch ($bz) {
		case 1:				//重甲	体力	|攻击|防御|敏捷
			$info = array(0.3,0.25,0.2,0.25);
			break;
		case 2:
			$info = array(0.25,0.25,0.3,0.2);
			break;
		case 3:
			$info = array(0.3,0.35,0.15,0.2);
			break;
		case 4:
			$info = array(0.3,0.3,0.15,0.25);
			break;
		case 4:
			$info = array(0.3,0.3,0.15,0.25);
			break;
	}
	return $info;
}

//技能ID与道具ID对应表
function jndyid($jnid) {
	switch ($jnid) {
		case 1:   //神来一击
			$djid = 10023;
		break;
		case 2:  //左右开工
			$djid = 10030;
		break;
		case 3: //玄阴七伤
			$djid = 10025;
		break;
		case 4: //钩挂连环
			$djid = 10017;
		break;
		case 5: //十面绞杀
			$djid = 10024;
		break;
		case 6: //迫在眉睫
			$djid = 10022;
		break;
		case 7: //连环闪电
			$djid = 10020;
		break;
		case 8: //金钟之罩
			$djid = 10019;
		break;
		case 9: //护佑全军
			$djid = 10018;
		break;
		case 10: //战神
			$djid = 10029;
		break;
		case 11: //战鼓震天
			$djid = 10028;
		break;
		case 12: //疑兵四伏
			$djid = 10026;
		break;
		case 13: //夺魂一击
			$djid = 10016;
		break;
		case 14: //妙手回春
			$djid = 10021;
		break;
		case 15: //雨露春风
			$djid = 10027;
		break;
		case 16: //偷天换日
			$djid = 10060;
		break;	
		case 17: //烈火焚身
			$djid = 10059;
		break;			
		default:
			$djid = 0;
		break;			
	}
	return $djid;
}

//道具ID与技能ID对应表
function iddyjn($itemId) {
	switch ($itemId) {
		case 10023:   //神来一击
			$djid = 1;
		break;
		case 10030:  //左右开工
			$djid = 2;
		break;
		case 10025: //玄阴七伤
			$djid = 3;
		break;
		case 10017: //钩挂连环
			$djid = 4;
		break;
		case 10024: //十面绞杀
			$djid = 5;
		break;
		case 10022: //迫在眉睫
			$djid = 6;
		break;
		case 10020: //连环闪电
			$djid = 7;
		break;
		case 10019: //金钟之罩
			$djid = 8;
		break;
		case 10018: //护佑全军
			$djid = 9;
		break;
		case 10029: //战神
			$djid = 10;
		break;
		case 10028: //战鼓震天
			$djid = 11;
		break;
		case 10026: //疑兵四伏
			$djid = 12;
		break;
		case 10016: //夺魂一击
			$djid = 13;
		break;
		case 10021: //妙手回春
			$djid = 14;
		break;
		case 10027: //雨露春风
			$djid = 15;
		break;
		case 10060: //偷天换日
			$djid = 16;
		break;	
		case 10059: //烈火焚身
			$djid = 17;
		break;			
		default:
			$djid = 0;
		break;			
	}
	return $djid;
}

//加成材料汇率 汇率值是1个材料=多少个元宝
function jcclhl() {
	return 10;
}

//升级材料汇率 汇率值是1个材料=多少个元宝
function sjclhl() {
	return 5;
}

function jjmc($jj) {
	global $city_var_lang;
	$data = $city_var_lang['jjmc'];
	//军士，虞候，先锋，将军，元帅
	return $data[$jj];
}

//爵位  爵位名-加成值-声望上限-需要道具-道具数量(铜钱1 银票2 元宝3)
function jwmc($level) {
	global $city_var_lang;
	$data = $city_var_lang['jwmc'];
	switch ($level) {
		case 1:
			return array('mc'=>$data[$level],'jc'=>jwjc($level),'sw'=>0,'tq'=>0,'yp'=>0,'yb'=>0,'level'=>0,'dj'=>array());
		case 2:
			return array('mc'=>$data[$level],'jc'=>jwjc($level),'sw'=>300,'tq'=>0,'yp'=>0,'yb'=>0,'level'=>10,'dj'=>array(array('id'=>'20115','sl'=>'1')));//红宝石1个
		case 3:
			return array('mc'=>$data[$level],'jc'=>jwjc($level),'sw'=>400,'tq'=>0,'yp'=>0,'yb'=>0,'level'=>13,'dj'=>array(array('id'=>'20115','sl'=>'3')));//红宝石3个
		case 4:
			return array('mc'=>$data[$level],'jc'=>jwjc($level),'sw'=>500,'tq'=>0,'yp'=>0,'yb'=>0,'level'=>16,'dj'=>array(array('id'=>'20115','sl'=>'5')));//红宝石5个
		case 5:
			return array('mc'=>$data[$level],'jc'=>jwjc($level),'sw'=>600,'tq'=>0,'yp'=>0,'yb'=>0,'level'=>19,'dj'=>array(array('id'=>'20115','sl'=>'8')));//红宝石8个
		case 6:
			return array('mc'=>$data[$level],'jc'=>jwjc($level),'sw'=>900,'tq'=>0,'yp'=>0,'yb'=>0,'level'=>22,'dj'=>array(array('id'=>'20115','sl'=>'12')));//红宝石12个
		case 7:
			return array('mc'=>$data[$level],'jc'=>jwjc($level),'sw'=>1200,'tq'=>0,'yp'=>0,'yb'=>0,'level'=>25,'dj'=>array(array('id'=>'20115','sl'=>'16')));//红宝石16个
		case 8:
			return array('mc'=>$data[$level],'jc'=>jwjc($level),'sw'=>1700,'tq'=>0,'yp'=>0,'yb'=>0,'level'=>28,'dj'=>array(array('id'=>'20115','sl'=>'30'),array('id'=>'20118','sl'=>'5')));//红宝石30个,玛瑙5个
		case 9:
			return array('mc'=>$data[$level],'jc'=>jwjc($level),'sw'=>2300,'tq'=>0,'yp'=>0,'yb'=>0,'level'=>31,'dj'=>array(array('id'=>'20115','sl'=>'38'),array('id'=>'20118','sl'=>'7')));//红宝石38个,玛瑙7个
		case 10:
			return array('mc'=>$data[$level],'jc'=>jwjc($level),'sw'=>3100,'tq'=>0,'yp'=>0,'yb'=>0,'level'=>34,'dj'=>array(array('id'=>'20115','sl'=>'45'),array('id'=>'20118','sl'=>'10')));//红宝石45个,玛瑙10个
		case 11:
			return array('mc'=>$data[$level],'jc'=>jwjc($level),'sw'=>4100,'tq'=>0,'yp'=>0,'yb'=>0,'level'=>37,'dj'=>array(array('id'=>'20115','sl'=>'56'),array('id'=>'20118','sl'=>'16')));//红宝石56个,玛瑙16个
		case 12:
			return array('mc'=>$data[$level],'jc'=>jwjc($level),'sw'=>5300,'tq'=>0,'yp'=>0,'yb'=>0,'level'=>40,'dj'=>array(array('id'=>'20115','sl'=>'56'),array('id'=>'20118','sl'=>'30')));//红宝石56个,玛瑙30个
		case 13:
			return array('mc'=>$data[$level],'jc'=>jwjc($level),'sw'=>6800,'tq'=>0,'yp'=>0,'yb'=>0,'level'=>45,'dj'=>array(array('id'=>'20115','sl'=>'110'),array('id'=>'20118','sl'=>'56')));//红宝石110个,玛瑙56个
		case 14:
			return array('mc'=>$data[$level],'jc'=>jwjc($level),'sw'=>8600,'tq'=>0,'yp'=>0,'yb'=>0,'level'=>50,'dj'=>array(array('id'=>'20118','sl'=>'136'),array('id'=>'20124','sl'=>'17')));//玛瑙136个,翡翠17个
		case 15:
			return array('mc'=>$data[$level],'jc'=>jwjc($level),'sw'=>10700,'tq'=>0,'yp'=>0,'yb'=>0,'level'=>55,'dj'=>array(array('id'=>'20118','sl'=>'150'),array('id'=>'20124','sl'=>'40')));//玛瑙150个,翡翠40个
		case 16:
			return array('mc'=>$data[$level],'jc'=>jwjc($level),'sw'=>13300,'tq'=>0,'yp'=>0,'yb'=>0,'level'=>60,'dj'=>array(array('id'=>'20118','sl'=>'200'),array('id'=>'20124','sl'=>'90')));//玛瑙200个,翡翠90个
		case 17:
			return array('mc'=>$data[$level],'jc'=>jwjc($level),'sw'=>16200,'tq'=>0,'yp'=>0,'yb'=>0,'level'=>60,'dj'=>array(array('id'=>'20118','sl'=>'210'),array('id'=>'20124','sl'=>'110')));//玛瑙210个,翡翠110个
		case 18:
			return array('mc'=>$data[$level],'jc'=>jwjc($level),'sw'=>19500,'tq'=>0,'yp'=>0,'yb'=>0,'level'=>60,'dj'=>array(array('id'=>'20118','sl'=>'230'),array('id'=>'20124','sl'=>'130')));//玛瑙230个,翡翠130个
		case 19:
			return array('mc'=>$data[$level],'jc'=>jwjc($level),'sw'=>23300,'tq'=>0,'yp'=>0,'yb'=>0,'level'=>60,'dj'=>array(array('id'=>'20121','sl'=>'25')));//白玉25个
		case 20:
			return array('mc'=>$data[$level],'jc'=>jwjc($level),'sw'=>27700,'tq'=>0,'yp'=>0,'yb'=>0,'level'=>60,'dj'=>array(array('id'=>'20121','sl'=>'35')));//白玉35个
		case 21:
			return array('mc'=>$data[$level],'jc'=>jwjc($level),'sw'=>32500,'tq'=>0,'yp'=>0,'yb'=>0,'level'=>60,'dj'=>array(array('id'=>'20121','sl'=>'55')));  //白玉55个
		default:
			return array('mc'=>$city_var_lang['requestLevel'],'jc'=>0,'sw'=>$city_var_lang['requestLevel'],'tq'=>0,'yp'=>0,'yb'=>0,'level'=>0,'dj'=>array());
	}
}

//爵位加成数值
/*$jwdj 爵位等级 *
 * */
function jwjc($jwdj) {
	global $_SZ;
	static $data =	array(
		1=>array(1=>0,2=>2,3=>4,4=>6,5=>8,6=>10,7=>13,8=>16,9=>19,10=>22,11=>25,12=>30,13=>35,14=>40,15=>45,16=>50,17=>60,18=>70,19=>80,20=>90,21=>100),
		2=>array(1=>0,2=>2,3=>4,4=>6,5=>8,6=>10,7=>12,8=>14,9=>16,10=>18,11=>20,12=>23,13=>26,14=>29,15=>32,16=>35,17=>40,18=>45,19=>50,20=>55,21=>60)
	);
	return $data[$_SZ['jwbb']][$jwdj];
}

//合成军阶上限
function hcsx() {
	return 4;
}

//历练补人口所需银两
function bllyp() {
	return 5;
}

	//武将可出征数量
	/*刚进入游戏可以出征3名武将；
角色等级10级可以出征4名武将；
角色等级20级可以出征5名武将；*/
function wjczsl($player_level) {
	if ($player_level < 10) {
		$sl = 3;
	} elseif ($player_level > 9 && $player_level < 20) {
		$sl = 4;
	} else {
		$sl = 5;
	}
	return $sl;
}

//武将升级上限
function wjsjsx($tf) {
	if ($tf > 10 && $tf < 16) {
		$sx = 30;
	} elseif ($tf > 15 && $tf < 21) {
		$sx = 40;
	} elseif ($tf > 20 && $tf < 26) {
		$sx = 50;
	} elseif ($tf > 25 && $tf < 31) {
		$sx = 60;
	} elseif ($tf > 30 && $tf < 36) {
		$sx = 70;
	} else {
		$sx = 0;
	}
	return $sx;
}

//繁荣度度刷将概率 $frd 繁荣度 $lssxcs绿色刷将次数$zssxcs紫色刷将次数$cssxcs橙色刷将次数
function frdtf($frd, $lssxcs, $zssxcs, $cssxcs, &$ys = 0) {
	if ($frd < 201) {
		$maxys = 2;
	} elseif ($frd > 200 && $frd < 1001) {
		$maxys = 3;
	} elseif ($frd > 1000 && $frd < 5001) {
		$maxys = 4;
	} else {
		$maxys = 5;
	}
    $lsjv = $lssxcs * 0.0375;
    $zsjv = $zssxcs * 0.002;
    $csjv = $cssxcs * 0.00002;
	$rand = rand ( 0, 999999 ) / 1000000;
	if ($rand < (0.00002 + $csjv)) {
		$ys = 5;
	} elseif ($rand < (0.002 + $zsjv)) {
		$ys = 4;
	} elseif ($rand < (0.0375 + $lsjv)) {
		$ys = 3;
	} else {
		$ys = 2;
	}
	if ($ys > $maxys) {
		$ys = $maxys;
	}	
	$xj = rand(1,5);
	if ($ys == 2) {
		$ksys = 15;
	} elseif ($ys == 3) {
		$ksys = 20;
	} elseif ($ys == 4) {
		$ksys = 25;
	} else {
		$ksys = 30;
	}
	$tf = $ksys + $xj;
	return $tf;
}
//刷新资源点消耗元宝数
$sxzydyb = 5;
function tjhf($ys) {
	$data = array(
		3=>array('hf'=>20,'hs'=>20,'jl'=>0),
		4=>array('hf'=>200,'hs'=>150,'jl'=>50),
		5=>array('hf'=>999,'hs'=>799,'jl'=>200),
	);
	return $data[$ys];
}

//名将天赋值二次生成概率
function tfecsc($ys) {
	$rand = rand(0,99);
	switch ($ys) {
		case 2:   //绿
			$tfdata = array(16,17,18,19,20);
			$check = array_rand($tfdata,1);
			$tf = $tfdata[$check];
		break;
		case 3:   //蓝
			if ($rand < 15) {
				$tf = 25;
			} elseif ($rand < 30) {
				$tf = 24;
			} elseif ($rand < 50) {
				$tf = 23;
			} elseif ($rand < 75) {
				$tf = 22;
			} else {
				$tf = 21;
			}
		break;
		case 4:   //紫
			if ($rand < 10) {
				$tf = 30;
			} elseif ($rand < 25) {
				$tf = 29;
			} elseif ($rand < 45) {
				$tf = 28;
			} elseif ($rand < 70) {
				$tf = 27;
			} else {
				$tf = 26;
			}
		break;	
		case 5:   //橙
			if ($rand < 1) {
				$tf = 35;
			} elseif ($rand < 15) {
				$tf = 34;
			} elseif ($rand < 35) {
				$tf = 33;
			} elseif ($rand < 65) {
				$tf = 32;
			} else {
				$tf = 31;
			}
		break;	
		default:
			$tf = 11;
			break;				
	}
	return $tf;
}

//获取武将等级培养属性上限 $wjdj : 武将等级
function wjdjpysx($wjdj) {
	$zsx = 0.00022326 * pow($wjdj,4) + $wjdj * 1 + 20;
	return ceil($zsx / 4);
}

//座次对培养随机上限的影响数值 $zc,$whlx武魂类习惯
function zcpysjsx($zc,$whlx) {
	if ($whlx == 4) {
		return 0;
	}
	$zc = 110 - $zc;
	$dsxpysx = $zc * 25;
	static $cs = array(1=>0.9,2=>1.2,3=>1.5);
	static $jh = array(1=>-0.420252563609234,2=>-0.273750893824668,3=>-0.113336345697278);
	return $cs[$whlx] * pow($zc,$jh[$whlx]) * $dsxpysx;
}

//座次对武将单属性的影响
function zcpysx($zc) {
	$zc = 110 - $zc;
	return $zc * 25;
}
//提取武魂数量
function tqwhsl($wjys) {
	$rand = rand(0,999);
	$randData = array(
	  2=>array(250,300),
	  3=>array(125,250),
	  4=>array(60,180),
	  5=>array(15,60)
	);
	$data = $randData[$wjys];
	if ($rand < $data[0]) {
		$sl = 3;
	} elseif ($rand < $data[0] + $data[1]) {
		$sl = 2;
	} else {
		$sl = 1;
	}
	return $sl;
}
//武魂对应元宝$whlx武魂类型 1绿 2蓝3紫4橙
function whyb($whlx) {
	$data = array(1=>5,2=>20,3=>40,4=>120);
	return $data[$whlx];
}
//继承比例提升元宝数值
function jcybhf($bl) {
	if ($bl < 60) {
		$yb = 20;
	} elseif ($bl < 70) {
		$yb = 40;
	} elseif ($bl < 80) {
		$yb = 60;
	} elseif ($bl < 90) {
		$yb = 80;
	} else {
		$yb = 100;
	}
	return $yb;
}

//一键提升比率花费元宝计算
function jcyjts($bl) {
	$a = $b = $c = $d = $e = 0;
	for ($i = $bl + 1;$i <= 100; $i++ ) {
		if ($i < 61) {
			$a++;
		} elseif ($i < 71) {
			$b++;
		} elseif ($i < 81) {
			$c++;
		} elseif ($i < 91) {
			$d++;
		} elseif ($i < 101) {
			$e++;
		}				
	}
	return ceil($a / 2) * 20 + ceil($b / 2) * 40 + ceil($c / 2) * 60 + ceil($d / 2) * 80 + ceil($e / 2) * 100;
}

//主目标图标数值
function zmbiid($mbid) {
	$array = array(
		5001001 => 'mb001',
		5001002 => 'YX018',
		5001003 => 'YX035',
		5001004 => 'YX039',
		5001005 => 'YX011',
		5001006 => 'YX018',
		5001007 => 'YX035',
		5001008 => 'YX039',
		5001009 => 'YX011',
		5001010 => 'YX018',
		5001011 => 'YX035',
		5001012 => 'YX039',
		5001013 => 'YX011',
		5001014 => 'YX023',
		5001015 => 'YX018',
		5001016 => 'YX011',
		5001017 => 'YX035',
		5001018 => 'YX028',
		5001019 => 'YX039',
		5001020 => 'YX035',
		5001021 => 'YX011',
		5001022 => 'YX023',
		5001023 => 'YX018',
		5001024 => 'YX011',
		5001025 => 'YX035',
		5001026 => 'YX028',
		5001027 => 'YX039',
		5001028 => 'YX035',
		5001029 => 'YX011',
		5001030 => 'YX023',
		5001031 => 'YX018',
		5001032 => 'YX011',
		5001033 => 'YX035',
		5001034 => 'YX028',
		5001035 => 'YX039',
		5001036 => 'YX035',
		5001037 => 'YX011'
	);
	if (!empty($array[$mbid])) {
		return $array[$mbid];
	} else {
		return 'mb001';
	}
}
?>