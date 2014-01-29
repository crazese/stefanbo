<?php
class jn {
	//技能增益数值
	public static function jnzy($jnid,$jnjb) {
		$jnInfo = array(
		    1=>array(1=>1.7,2=>1.75,3=>1.8,4=>1.85,5=>1.9,6=>1.95,7=>2,8=>2.05),     //神来一击
		    2=>array(1=>400,2=>450,3=>500,4=>550,5=>600,6=>650,7=>700,8=>750),      //左右开弓
		    3=>array(1=>3.5,2=>3.55,3=>3.6,4=>3.65,5=>3.7,6=>3.75,7=>3.8,8=>3.85),   //玄阴七伤
		    4=>array(1=>1.2,2=>1.25,3=>1.3,4=>1.35,5=>1.4,6=>1.45,7=>1.5,8=>1.55),   //钩挂连环
		    5=>array(1=>0.5,2=>0.55,3=>0.6,4=>0.65,5=>0.7,6=>0.75,7=>0.8,8=>0.85),   //十面绞杀
		    6=>array(1=>array(1.5, 1.5 * 0.4, 1.5 * 0.6),2=>array(1.55, 1.55 * 0.4, 1.55 * 0.6),3=>array(1.6, 1.6 * 0.4, 1.6 * 0.6),4=>array(1.65, 1.65 * 0.4, 1.65 * 0.6),5=>array(1.7, 1.7 * 0.4, 1.7 * 0.6),6=>array(1.75, 1.75 * 0.4, 1.75 * 0.6),7=>array(1.8, 1.8 * 0.4, 1.8 * 0.6),8=>array(1.85, 1.85 * 0.4, 1.85 * 0.6)), //迫在眉睫
		    7=>array(1=>array(1.5, 1.5 * 0.6, 1.5 * 0.4),2=>array(1.55, 1.55 * 0.6, 1.55 * 0.4),3=>array(1.6, 1.6 * 0.6, 1.6 * 0.4),4=>array(1.65, 1.65 * 0.6, 1.65 * 0.4),5=>array(1.7, 1.7 * 0.6, 1.7 * 0.4),6=>array(1.75, 1.75 * 0.6, 1.75 * 0.4),7=>array(1.8, 1.8 * 0.6, 1.8 * 0.4),8=>array(1.85, 1.85 * 0.6, 1.85 * 0.4)), //连环闪电
		    8=>array(1=>3.2,2=>3.25,3=>3.3,4=>3.35,5=>3.4,6=>3.45,7=>3.5,8=>3.55),   //金钟之罩
		    9=>array(1=>1.7,2=>1.8,3=>1.9,4=>2,5=>2.1,6=>2.2,7=>2.3,8=>2.4),     //护佑全军
		    10=>array(1=>1.2,2=>1.25,3=>1.3,4=>1.35,5=>1.4,6=>1.45,7=>1.5,8=>1.55),    //战神
		    11=>array(1=>1.4,2=>1.5,3=>1.6,4=>1.7,5=>1.8,6=>1.9,7=>2,8=>2.1),  //战鼓震天
		    12=>array(1=>1.05,2=>1.1,3=>1.15,4=>1.2,5=>1.25,6=>1.3,7=>1.35,8=>1.4), //疑兵四伏
		    13=>array(1=>array(1.7,40),2=>array(1.75,45),3=>array(1.8,50),4=>array(1.85,55),5=>array(1.9,60),6=>array(1.95,65),7=>array(2,70),8=>array(2.05,75)), //夺魂一击
		    14=>array(1=>1000,2=>1500,3=>2000,4=>2500,5=>3000,6=>3500,7=>4000,8=>4500), //妙手回春
		    15=>array(1=>10,2=>15,3=>20,4=>25,5=>30,6=>35,7=>40,8=>45),  //雨露春风	
		    16=>array(1=>array(0.3,2),2=>array(0.35,2.1),3=>array(0.4,2.2),4=>array(0.45,2.3),5=>array(0.5,2.4),6=>array(0.55,2.5),7=>array(0.6,2.6),8=>array(0.65,2.7)),  //偷天换日
		    17=>array(1=>array(0.8,0.3),2=>array(0.85,0.27),3=>array(0.9,0.24),4=>array(0.95,0.21),5=>array(1,0.18),6=>array(1.05,0.15),7=>array(1.1,0.12),8=>array(1.15,0.09))   //烈火焚身		
	    );
		return $jnInfo[$jnid][$jnjb];
	}
	
	//普通攻击
	public static function ptgj($tag,&$fight_sort,$i) {
		$role = $fight_sort[$tag]['role'];
		$general_sort = $fight_sort[$tag]['sort_id']; 
		$can_attack_object = actModel::findAttackObject($tag,$fight_sort);
		if ($can_attack_object == 20) {	
			$value['act'] = 2;                                      //移动
			$value['hid'] = $role.$general_sort;                    //将领代号						
			$value['pos'] = intval($fight_sort[$tag]['distance']);    //当前位置
			$value['sac'] = intval($fight_sort[$tag]['command_soldier']);
		} else {
			$base_sh = jn::sh($tag,$fight_sort,$can_attack_object,$i);
			$dtx = 0;
			$fight_sort = actModel::attacking($fight_sort,$can_attack_object,$tag,$i,$base_sh,$dtx);			
			//行动方信息
			$value['act'] = 1;
			$value['hid'] = $role.$general_sort;
			$value['pos'] = intval($fight_sort[$tag]['distance']);					
			$value['eff'] = intval($fight_sort[$tag]['animation']);					 
			$value['sac'] = intval($fight_sort[$tag]['command_soldier']);	
			//常规被攻击信息
			$value['dfxg'] = array(array('hid'=>$fight_sort[$can_attack_object]['role'].$fight_sort[$can_attack_object]['sort_id'],'sac'=>$fight_sort[$can_attack_object]['command_soldier'],'eff'=>$dtx));
		}
		return $value;		
	}
	
	//神来一击
	public static function jn_1($tag,&$fight_sort,$i,$jn_level) {
		$role = $fight_sort[$tag]['role'];
		$general_sort = $fight_sort[$tag]['sort_id'];
		$act = 3;
		$can_attack_object = jn::randFindAttackObject($tag,$fight_sort,$act);		
		$base_sh = jn::sh($tag,$fight_sort,$can_attack_object);
		$gjjc = jn::jnzy(1,$jn_level);     //攻击加成
		$kill_value = $base_sh * $gjjc;
		$dtx = 0;
		$fight_sort = actModel::attacking($fight_sort,$can_attack_object,$tag,$i,$kill_value,$dtx);			
		//行动方信息
		$value['act'] = $act;
		$value['hid'] = $role.$general_sort;
		$value['pos'] = intval($fight_sort[$tag]['distance']);					
		$value['eff'] = 1;					 
		$value['sac'] = intval($fight_sort[$tag]['command_soldier']);	
		//常规被攻击信息
		//$bAttackInfo = actModel::decodeAttackInfo($fight_sort[$can_attack_object][$fight_sort[$tag]['animation']],$tag,$value['eff'],$i);			
		//$valu['dfxg'] = array('hid'=>$fight_sort[$can_attack_object]['role'].$fight_sort[$can_attack_object]['sort_id'],'sac'=>intval($bAttackInfo['left']),'eff'=>intval($bAttackInfo['tx']));
		$value['dfxg'] = array(array('hid'=>$fight_sort[$can_attack_object]['role'].$fight_sort[$can_attack_object]['sort_id'],'sac'=>$fight_sort[$can_attack_object]['command_soldier'],'eff'=>$dtx));
		/*$cxxg = jn::cqxg($fight_sort); //检查是否有持续效果
		if (!empty($cxxg)) {
			$value['cxxg'] = $cxxg;
		}*/
		$act = '';
		return $value;	
	}
	//左右开弓
	public static function jn_2($tag,&$fight_sort,$i,$jn_level) {
		$role = $fight_sort[$tag]['role'];
		$general_sort = $fight_sort[$tag]['sort_id'];
		$act = 3;
		//$can_attack_object = jn::randFindAttackObject($tag,$fight_sort,$act);	
		$can_attack_object = actModel::txmb2($fight_sort,$tag,2);	
		if ($can_attack_object === 'nodata') {
			return false;
		}
		$base_sh = jn::sh($tag,$fight_sort,$can_attack_object);	
        $gjjc = jn::jnzy(2,$jn_level);
        $kill_value = $base_sh + $gjjc;
		$dtx = 0;
		$fight_sort = actModel::attacking($fight_sort,$can_attack_object,$tag,$i,$base_sh,$dtx);			
		//行动方信息
		$value['act'] = $act;
		$value['hid'] = $role.$general_sort;
		$value['pos'] = intval($fight_sort[$tag]['distance']);					
		$value['eff'] = 2;					 
		$value['sac'] = intval($fight_sort[$tag]['command_soldier']);	
		//常规被攻击信息
		$value['dfxg'] = array(array('hid'=>$fight_sort[$can_attack_object]['role'].$fight_sort[$can_attack_object]['sort_id'],'sac'=>$fight_sort[$can_attack_object]['command_soldier'],'eff'=>$dtx));
		if ($fight_sort[$can_attack_object]['command_soldier'] > 0) {
			$dtx = 0;
			$fight_sort_next = actModel::attacking($fight_sort,$can_attack_object,$tag,$i,$kill_value,$dtx);
		    $value['zdfxg'] = array(array('hid'=>$fight_sort[$can_attack_object]['role'].$fight_sort[$can_attack_object]['sort_id'],'sac'=>$fight_sort[$can_attack_object]['command_soldier'],'eff'=>$dtx));			
		}		
		
		/*$cxxg = jn::cqxg($fight_sort); //检查是否有持续效果
		if (!empty($cxxg)) {
			$value['cxxg'] = $cxxg;
		}*/
		$act = '';		
		return $value;			
	}	
	//玄阴七伤
	public static function jn_3($tag,&$fight_sort,$i,$jn_level) {
		$role = $fight_sort[$tag]['role'];
		if ($role == 2) {
			$enemy = 1;
		} else {
			$enemy = 2;
		}
		$general_sort = $fight_sort[$tag]['sort_id'];
		$act = 3;
		$can_attack_object = jn::randFindAttackObject($tag,$fight_sort,$act);		
		$base_sh = jn::sh($tag,$fight_sort,$can_attack_object);
        $gjjc = jn::jnzy(3,$jn_level);
        $kill_value = $base_sh * $gjjc;
		$dtx = 0;
		$fight_sort = actModel::attacking($fight_sort,$can_attack_object,$tag,$i,$kill_value,$dtx);	
		/*for ($j = 0;$j < count($fight_sort);$j++) {
		    if ($fight_sort[$j]['role'] == $enemy) {
		    	$blood[] = $fight_sort[$j]['command_soldier'];
		    }			
		}
		$blood_amount = array_sum($blood);	
		if ($blood_amount > 1) {
			$hp = round($fight_sort[$tag]['physical_value'],0) * 10;
			$loss_hp = ceil($hp / 2);
			if ($fight_sort[$tag]['command_soldier'] < $loss_hp) {
				$fight_sort[$tag]['command_soldier'] = 0;
			} else {
				$fight_sort[$tag]['command_soldier'] -= $loss_hp;
			}
		}*/
		$fight_sort[$tag]['command_soldier'] = ceil($fight_sort[$tag]['command_soldier'] / 2);
		//行动方信息
		$value['act'] = $act;
		$value['hid'] = $role.$general_sort;
		$value['pos'] = intval($fight_sort[$tag]['distance']);					
		$value['eff'] = 3;					 
		$value['sac'] = intval($fight_sort[$tag]['command_soldier']);
		//常规被攻击信息
		$value['dfxg'] = array(array('hid'=>$fight_sort[$can_attack_object]['role'].$fight_sort[$can_attack_object]['sort_id'],'sac'=>$fight_sort[$can_attack_object]['command_soldier'],'eff'=>$dtx));
		/*$cxxg = jn::cqxg($fight_sort); //检查是否有持续效果
		if (!empty($cxxg)) {
			$value['cxxg'] = $cxxg;
		}*/
		$act = '';		
		return $value;			
	}	
	//钩挂连环
	public static function jn_4($tag,&$fight_sort,$i,$jn_level) {
		$role = $fight_sort[$tag]['role'];
		$general_sort = $fight_sort[$tag]['sort_id'];
		$act = 3;
		//$can_attack_object = jn::randFindAttackObject($tag,$fight_sort,$act);	
		$can_attack_object = actModel::txmb2($fight_sort,$tag,1);	
		$base_sh = jn::sh($tag,$fight_sort,$can_attack_object);
		$gjjc = jn::jnzy(4,$jn_level);
		$kill_value = $base_sh * $gjjc;
		$dtx = 0;
		$fight_sort = actModel::attacking($fight_sort,$can_attack_object,$tag,$i,$kill_value,$dtx);			
		//行动方信息
		$value['act'] = $act;
		$value['hid'] = $role.$general_sort;
		$value['pos'] = intval($fight_sort[$tag]['distance']);					
		$value['eff'] = 4;					 
		$value['sac'] = intval($fight_sort[$tag]['command_soldier']);	
		//常规被攻击信息
		$value['dfxg'] = array(array('hid'=>$fight_sort[$can_attack_object]['role'].$fight_sort[$can_attack_object]['sort_id'],'sac'=>$fight_sort[$can_attack_object]['command_soldier'],'eff'=>$dtx));
		//再次绞杀
		if ($fight_sort[$can_attack_object]['command_soldier'] > 0) {
			$dtx = 0;
			$fight_sort_next = actModel::attacking($fight_sort,$can_attack_object,$tag,$i,$kill_value,$dtx);
		    $value['zdfxg'] = array(array('hid'=>$fight_sort[$can_attack_object]['role'].$fight_sort[$can_attack_object]['sort_id'],'sac'=>$fight_sort[$can_attack_object]['command_soldier'],'eff'=>$dtx));			
		}
		/*$cxxg = jn::cqxg($fight_sort); //检查是否有持续效果
		if (!empty($cxxg)) {
			$value['cxxg'] = $cxxg;
		}*/	
		$act = '';	
		return $value;			
	}	
	//十面绞杀
	public static function jn_5($tag,&$fight_sort,$i,$jn_level) {
		$role = $fight_sort[$tag]['role'];
		$general_sort = $fight_sort[$tag]['sort_id'];
		if ($role == 2) {
			$enemy = 1;
		} else {
			$enemy = 2;
		}
		$gjjc = jn::jnzy(5,$jn_level);
		if (!empty($fight_sort[$tag]['zsft'])) {
			$zsft = $fight_sort[$tag]['zsft'];
		} else {
			$zsft = 0;
		}
		if (!empty($fight_sort[$tag]['zgzt'])) {
			$zgzt = $fight_sort[$tag]['zgzt'];
		} else {
			$zgzt = 0;
		}
		if (!empty($fight_sort[$tag]['zjgj'])) {
			$zjgj = $fight_sort[$tag]['zjgj'];
		} else {
			$zjgj = 0;
		}								
        for ($j = 0; $j < count($fight_sort); $j++) {
        	if ($fight_sort[$j]['role'] == $enemy) {
        		$sm = $fight_sort[$j]['command_soldier'];
        		if ($sm > 0) {  
        			if ($zsft != 0) {
        				$fight_sort[$tag]['zsft'] = $zsft;
        			} 
        			if ($zgzt != 0) {
        				$fight_sort[$tag]['zgzt'] = $zgzt;
        			}   
        			if ($zjgj != 0) {
        				$fight_sort[$tag]['zjgj'] = $zjgj;
        			}          			  			
        			$base_sh = jn::sh($tag,$fight_sort,$j);
        			$kill_value = $base_sh * $gjjc;
        			$mz = jn::mzl($fight_sort[$tag]['agility_value'],$fight_sort[$j]['agility_value']);
        			/*此处随机命中函数*/
        			if ($mz == 0) {
        				$dfxg[] = array('hid'=>$fight_sort[$j]['role'].$fight_sort[$j]['sort_id'],'sac'=>$sm,'eff'=>1);
        			} else {
        				$left_hp = ($sm - $kill_value >= 0) ? (floor($sm - $kill_value)) : 0;
        				$fight_sort[$j]['command_soldier'] = $left_hp; 
        				$dfxg[] = array('hid'=>$fight_sort[$j]['role'].$fight_sort[$j]['sort_id'],'sac'=>$left_hp,'eff'=>0);
        			}
        		}
        	}
        }
		//行动方信息
		$value['act'] = 3;
		$value['hid'] = $role.$general_sort;
		$value['pos'] = intval($fight_sort[$tag]['distance']);					
		$value['eff'] = 5;					 
		$value['sac'] = intval($fight_sort[$tag]['command_soldier']);	
		//常规被攻击信息
		$value['dfxg'] = $dfxg;
		/*$cxxg = jn::cqxg($fight_sort); //检查是否有持续效果
		if (!empty($cxxg)) {
			$value['cxxg'] = $cxxg;
		}*/
		$act = '';		
		return $value;			
	}	
	//迫在眉睫(变化)攻击离自己最近的三人
	public static function jn_6($tag,&$fight_sort,$i,$jn_level) {
		$role = $fight_sort[$tag]['role'];
		$general_sort = $fight_sort[$tag]['sort_id'];
		if ($role == 2) {
			$enemy = 1;
		} else {
			$enemy = 2;
		}
		for ($j = 0; $j < count($fight_sort); $j++) {
        	if ($fight_sort[$j]['role'] == $enemy) {
        		if ($fight_sort[$j]['command_soldier'] > 0) {        			
                    $jl[] = array('key'=>$j,'jl'=>$fight_sort[$j]['distance'],'sortid'=>$fight_sort[$j]['sort_id']);
        		}        		
        	}
        }
        foreach ($jl as $key => $jlvalue) {
            $sort_jl[$key] = $jlvalue['jl'];
            $sort_id[$key] = $jlvalue['sortid'];
            //$sort_id[$key] = $jlvalue['key'];
        }
        array_multisort($sort_jl,SORT_DESC,SORT_NUMERIC,$sort_id,SORT_ASC,SORT_NUMERIC,$jl);		
	    /*for ($j = 0; $j < count($fight_sort); $j++) {
        	if ($fight_sort[$j]['role'] == $enemy) {
        		if ($fight_sort[$j]['command_soldier'] > 0) {        			
                    $jl[$j] = $fight_sort[$j]['distance'];
        		}
        	}
        }
        arsort($jl,SORT_NUMERIC);  //按距离大小排序，最多排最前
		//heroCommon::insertLog('xssss+'.implode(',',$xs)); 
		$dfsl = count($jl);
		//echo '数量'.$dfsl;
		if ($dfsl > 3) { 
			$dfsl = 3;			
		} 
		$chosedKey = array_keys($jl);
		unset($jl); */
		$xs = jn::jnzy(6,$jn_level);
		if (!empty($fight_sort[$tag]['zsft'])) {
			$zsft = $fight_sort[$tag]['zsft'];
		} else {
			$zsft = 0;
		}
		if (!empty($fight_sort[$tag]['zgzt'])) {
			$zgzt = $fight_sort[$tag]['zgzt'];
		} else {
			$zgzt = 0;
		}
		if (!empty($fight_sort[$tag]['zjgj'])) {
			$zjgj = $fight_sort[$tag]['zjgj'];
		} else {
			$zjgj = 0;
		} 		
        $n= 0;
        /*if (!is_array($chosedKey)) {
        	$chosedKey = array($chosedKey);
        }*/
        //foreach ($chosedKey as $jlKey) {
        foreach ($jl as $pxvalue) {
        	$n++;
        	$jlKey = $pxvalue['key'];
        	$sm = $fight_sort[$jlKey]['command_soldier'];
        	if ($zsft != 0) {
        		$fight_sort[$tag]['zsft'] = $zsft;
        	} 
        	if ($zgzt != 0) {
        		$fight_sort[$tag]['zgzt'] = $zgzt;
        	}   
        	if ($zjgj != 0) {
        		$fight_sort[$tag]['zjgj'] = $zjgj;
        	}          	
            $base_sh = jn::sh($tag,$fight_sort,$jlKey);
            $gjxs = $xs[$n-1];
        	$kill_value = $base_sh * $gjxs;
        	$mz = jn::mzl($fight_sort[$tag]['agility_value'],$fight_sort[$jlKey]['agility_value']);
        	if ($mz == 0) {
        		$dfxg[] = array('hid'=>$fight_sort[$jlKey]['role'].$fight_sort[$jlKey]['sort_id'],'sac'=>$sm,'eff'=>1);
        	} else {
        		$left_hp = ($sm - $kill_value >= 0) ? (floor($sm - $kill_value)) : 0;
        		$fight_sort[$jlKey]['command_soldier'] = $left_hp;
        		$dfxg[] = array('hid'=>$fight_sort[$jlKey]['role'].$fight_sort[$jlKey]['sort_id'],'sac'=>$left_hp,'eff'=>0);
        	} 
        	$jlKey = null;      	
        	if ($n == 3) {
        		break;
        	}
        }
		//行动方信息
		$value['act'] = 3;
		$value['hid'] = $role.$general_sort;
		$value['pos'] = intval($fight_sort[$tag]['distance']);					
		$value['eff'] = 6;					 
		$value['sac'] = intval($fight_sort[$tag]['command_soldier']);	
		//常规被攻击信息
		$value['dfxg'] = $dfxg;
		/*$cxxg = jn::cqxg($fight_sort); //检查是否有持续效果
		if (!empty($cxxg)) {
			$value['cxxg'] = $cxxg;
		}*/
		$act = '';		
		return $value;		        				
	}
	
	//连环闪电攻击离已方最远处的三名敌对武将
	public static function jn_7($tag,&$fight_sort,$i,$jn_level) {
		$role = $fight_sort[$tag]['role'];
		$general_sort = $fight_sort[$tag]['sort_id'];
		$act = 3;
		//$can_attack_object = jn::randFindAttackObject($tag,$fight_sort,$act);		
		if ($role == 2) {
			$enemy = 1;
		} else {
			$enemy = 2;
		}
		for ($j = 0; $j < count($fight_sort); $j++) {
        	if ($fight_sort[$j]['role'] == $enemy) {
        		if ($fight_sort[$j]['command_soldier'] > 0) {        			
                    $jl[] = array('key'=>$j,'jl'=>$fight_sort[$j]['distance'],'sortid'=>$fight_sort[$j]['sort_id']);
        		}        		
        	}
        }
        foreach ($jl as $key => $jlvalue) {
            $sort_jl[$key] = $jlvalue['jl'];
            $sort_id[$key] = $jlvalue['sortid'];
            //$sort_id[$key] = $jlvalue['key'];
        }
        array_multisort($sort_jl,SORT_ASC,SORT_NUMERIC,$sort_id,SORT_ASC,SORT_NUMERIC,$jl);
	    /*if ($role == 2) {
			$enemy = 1;
		} else {
			$enemy = 2;
		}		
	    for ($j = 0; $j < count($fight_sort); $j++) {
        	if ($fight_sort[$j]['role'] == $enemy) {
        		if ($fight_sort[$j]['command_soldier'] > 0) {        			
                    $jl[$j] = $fight_sort[$j]['distance'];
        		}
        	}
        }
        asort($jl,SORT_NUMERIC);  //按距离大小排序，最少排最前*/		
        $xs = jn::jnzy(7,$jn_level);   
		if (!empty($fight_sort[$tag]['zsft'])) {
			$zsft = $fight_sort[$tag]['zsft'];
		} else {
			$zsft = 0;
		}
		if (!empty($fight_sort[$tag]['zgzt'])) {
			$zgzt = $fight_sort[$tag]['zgzt'];
		} else {
			$zgzt = 0;
		}
		if (!empty($fight_sort[$tag]['zjgj'])) {
			$zjgj = $fight_sort[$tag]['zjgj'];
		} else {
			$zjgj = 0;
		}                 
        $n = 0;
        foreach ($jl as $pxvalue) {
            $n++;
            $jlKey = $pxvalue['key'];	
            $sm = $fight_sort[$jlKey]['command_soldier'];
        	if ($zsft != 0) {
        		$fight_sort[$tag]['zsft'] = $zsft;
        	} 
        	if ($zgzt != 0) {
        		$fight_sort[$tag]['zgzt'] = $zgzt;
        	}   
        	if ($zjgj != 0) {
        		$fight_sort[$tag]['zjgj'] = $zjgj;
        	}             
            $base_sh = jn::sh($tag,$fight_sort,$jlKey);
            $gjxs = $xs[$n-1];
        	$kill_value = $base_sh * $gjxs;
        	$mz = jn::mzl($fight_sort[$tag]['agility_value'],$fight_sort[$jlKey]['agility_value']);
        	if ($mz == 0) {
        		$dfxg[] = array('hid'=>$fight_sort[$jlKey]['role'].$fight_sort[$jlKey]['sort_id'],'sac'=>$sm,'eff'=>1);
        	} else {
        		$left_hp = ($sm - $kill_value >= 0) ? (floor($sm - $kill_value)) : 0;
        		$fight_sort[$jlKey]['command_soldier'] = $left_hp;
        		$dfxg[] = array('hid'=>$fight_sort[$jlKey]['role'].$fight_sort[$jlKey]['sort_id'],'sac'=>$left_hp,'eff'=>0);
        	}       	
        	if ($n == 3) {
        		break;
        	}          
        }
		//行动方信息
		$value['act'] = $act;
		$value['hid'] = $role.$general_sort;
		$value['pos'] = intval($fight_sort[$tag]['distance']);					
		$value['eff'] = 7;					 
		$value['sac'] = intval($fight_sort[$tag]['command_soldier']);	
		//常规被攻击信息
		$value['dfxg'] = $dfxg;
		/*$cxxg = jn::cqxg($fight_sort); //检查是否有持续效果
		if (!empty($cxxg)) {
			$value['cxxg'] = $cxxg;
		}*/	
		$act = '';	
		return $value;		        
	}	
	//金钟之罩
	public static function jn_8($tag,&$fight_sort,$i,$jn_level) {
		$role = $fight_sort[$tag]['role'];
		$general_sort = $fight_sort[$tag]['sort_id'];
		/*foreach ($fight_sort as $jlKey => $jlValue) {
			if ($jlValue['role'] == $role && $jlValue['command_soldier'] > 0) {
				$choseKey[$jlKey] = $jlValue;
			}
		}
		$chosedKey = array_rand($choseKey,1);*/
		$chosedKey = actModel::txmb($fight_sort,$tag);		
		$xs = jn::jnzy(8,$jn_level);
		//行动方信息
		$value['act'] = 3;
		$value['hid'] = $role.$general_sort;
		$value['pos'] = intval($fight_sort[$tag]['distance']);					
		$value['eff'] = 8;					 
		$value['sac'] = intval($fight_sort[$tag]['command_soldier']);	
		$fight_sort[$chosedKey]['jzzz'] = 8;
		$jfxg = array();
		if (empty($fight_sort[$chosedKey]['zjfy'])) {
			$fight_sort[$chosedKey]['zjfy'] = $xs;		   //受到防御增益	
			$jfxg[] = array('hid'=>$fight_sort[$chosedKey]['role'].$fight_sort[$chosedKey]['sort_id'],'sac'=>intval($fight_sort[$chosedKey]['command_soldier']));
		} else {
			if ($fight_sort[$chosedKey]['zjfy'] < $xs) {
				$fight_sort[$chosedKey]['zjfy'] = $xs;     //受到防御增益
				$jfxg[] = array('hid'=>$fight_sort[$chosedKey]['role'].$fight_sort[$chosedKey]['sort_id'],'sac'=>intval($fight_sort[$chosedKey]['command_soldier']));
			} else {
				$jfxg[] = array('hid'=>$fight_sort[$chosedKey]['role'].$fight_sort[$chosedKey]['sort_id'],'sac'=>intval($fight_sort[$chosedKey]['command_soldier']));
			}
			if (!empty($fight_sort[$chosedKey]['hyqj'])) {
				unset($fight_sort[$chosedKey]['hyqj']);
			}			
		}
		$value['jfxg'] = $jfxg;
		/*$cxxg = jn::cqxg($fight_sort); //检查是否有持续效果
		if (!empty($cxxg)) {
			$value['cxxg'] = $cxxg;
		}*/	
		$act = '';	
		return $value;
	}
	//护佑全军
	public static function jn_9($tag,&$fight_sort,$i,$jn_level) {
		$role = $fight_sort[$tag]['role'];
		$general_sort = $fight_sort[$tag]['sort_id'];
		$xs = jn::jnzy(9,$jn_level);
		$jfxg = array();	
		foreach ($fight_sort as $jlKey => $jlValue) {
			if ($jlValue['role'] == $role && $jlValue['command_soldier'] > 0) {
				$fight_sort[$jlKey]['hyqj'] = 9;  //护佑全军 
				if (empty($jlValue['zjfy'])) {
					$fight_sort[$jlKey]['zjfy'] = $xs; 
					$jfxg[] = array('hid'=>$fight_sort[$jlKey]['role'].$fight_sort[$jlKey]['sort_id'],'sac'=>intval($fight_sort[$jlKey]['command_soldier']));
				} else {
					if ($fight_sort[$jlKey]['zjfy'] < $xs) {
						$fight_sort[$jlKey]['zjfy'] = $xs; 	
						$jfxg[] = array('hid'=>$fight_sort[$jlKey]['role'].$fight_sort[$jlKey]['sort_id'],'sac'=>intval($fight_sort[$jlKey]['command_soldier']));					
					} else {
						$jfxg[] = array('hid'=>$fight_sort[$jlKey]['role'].$fight_sort[$jlKey]['sort_id'],'sac'=>intval($fight_sort[$jlKey]['command_soldier']));
					}
					if (!empty($fight_sort[$jlKey]['jzzz'])) {
						unset($fight_sort[$jlKey]['jzzz']);
					}
				}				
			}
		}		
		//行动方信息
		$value['act'] = 3;
		$value['hid'] = $role.$general_sort;
		$value['pos'] = intval($fight_sort[$tag]['distance']);					
		$value['eff'] = 9;					 
		$value['sac'] = intval($fight_sort[$tag]['command_soldier']);	
		$value['jfxg'] = $jfxg;
		/*$cxxg = jn::cqxg($fight_sort); //检查是否有持续效果
		if (!empty($cxxg)) {
			$value['cxxg'] = $cxxg;
		}*/	
		$act = '';	
		return $value;	
	}
	//战神
	public static function jn_10($tag,&$fight_sort,$i,$jn_level) {
		$role = $fight_sort[$tag]['role'];
		$general_sort = $fight_sort[$tag]['sort_id'];
		foreach ($fight_sort as $jlKey => $jlValue) {
			if ($jlValue['role'] == $role && $jlValue['command_soldier'] > 0) {
				$choseKey[$jlKey] = $jlValue;
			}
		}
		$chosedKey = array_rand($choseKey,1);
		$xs = jn::jnzy(10,$jn_level);
		//行动方信息
		$value['act'] = 3;
		$value['hid'] = $role.$general_sort;
		$value['pos'] = intval($fight_sort[$tag]['distance']);					
		$value['eff'] = 10;					 
		$value['sac'] = intval($fight_sort[$tag]['command_soldier']);	
		$fight_sort[$chosedKey]['zsft'] = 10;          //战神附体效果	
		$jfxg = array();	
		if (empty($fight_sort[$chosedKey]['zjgj'])) {
			//$fight_sort[$chosedKey]['zjgj'] = array('gj'=>$fight_sort[$tag]['command_soldier'],'xs'=>$xs);		   //接受到攻击增益       
			//$attack_info['attack_value']
			$fight_sort[$chosedKey]['zjgj'] = ($fight_sort[$tag]['attack_value'] + $fight_sort[$chosedKey]['attack_value']) * $xs;  //攻击增益
			$jfxg[] = array('hid'=>$fight_sort[$chosedKey]['role'].$fight_sort[$chosedKey]['sort_id'],'sac'=>intval($fight_sort[$chosedKey]['command_soldier']));     
		} else {
			$newgj = ($fight_sort[$tag]['attack_value'] + $fight_sort[$chosedKey]['attack_value']) * $xs;
			if ($fight_sort[$chosedKey]['zjgj'] < $newgj) {
				$fight_sort[$chosedKey]['zjgj'] = $newgj;     //接受到攻击增益
				$jfxg[] = array('hid'=>$fight_sort[$chosedKey]['role'].$fight_sort[$chosedKey]['sort_id'],'sac'=>intval($fight_sort[$chosedKey]['command_soldier']));
			} else {
				$jfxg[] = array('hid'=>$fight_sort[$chosedKey]['role'].$fight_sort[$chosedKey]['sort_id'],'sac'=>intval($fight_sort[$chosedKey]['command_soldier']));
			}
			if (!empty($fight_sort[$chosedKey]['zgzt'])) {
				unset($fight_sort[$chosedKey]['zgzt']);
		    }			
		}
		$value['jfxg'] = $jfxg;
		/*$cxxg = jn::cqxg($fight_sort); //检查是否有持续效果
		if (!empty($cxxg)) {
			$value['cxxg'] = $cxxg;
		}*/	
		$act = '';	
		return $value;		
	}
	//战鼓震天
	public static function jn_11($tag,&$fight_sort,$i,$jn_level) {
		$role = $fight_sort[$tag]['role'];
		$general_sort = $fight_sort[$tag]['sort_id'];
		$xs = jn::jnzy(11,$jn_level);
		foreach ($fight_sort as $chosedKey => $jlValue) {
			if ($jlValue['role'] == $role && $jlValue['command_soldier'] > 0) {
				$fight_sort[$chosedKey]['zgzt'] = 11;              //战鼓震天效果
				if (empty($fight_sort[$chosedKey]['zjgj'])) {
					$fight_sort[$chosedKey]['zjgj'] = $fight_sort[$chosedKey]['attack_value'] * $xs;		   //接受到攻击增益	
					$jfxg[] = array('hid'=>$fight_sort[$chosedKey]['role'].$fight_sort[$chosedKey]['sort_id'],'sac'=>intval($fight_sort[$chosedKey]['command_soldier']));
					if (!empty($fight_sort[$chosedKey]['zsft'])) {
						unset($fight_sort[$chosedKey]['zsft']);
					}
				} else {
					$newgj = $fight_sort[$chosedKey]['attack_value'] * $xs;
					if ($fight_sort[$chosedKey]['zjgj'] < $newgj) {
						$fight_sort[$chosedKey]['zjgj'] = $newgj;     //接受到攻击增益
						$jfxg[] = array('hid'=>$fight_sort[$chosedKey]['role'].$fight_sort[$chosedKey]['sort_id'],'sac'=>intval($fight_sort[$chosedKey]['command_soldier']));
					} else {
						$jfxg[] = array('hid'=>$fight_sort[$chosedKey]['role'].$fight_sort[$chosedKey]['sort_id'],'sac'=>intval($fight_sort[$chosedKey]['command_soldier']));
					}
					if (!empty($fight_sort[$chosedKey]['zsft'])) {
						unset($fight_sort[$chosedKey]['zsft']);
				    }				
				}
			}
		}
		//行动方信息
		$value['act'] = 3;
		$value['hid'] = $role.$general_sort;
		$value['pos'] = intval($fight_sort[$tag]['distance']);					
		$value['eff'] = 11;					 
		$value['sac'] = intval($fight_sort[$tag]['command_soldier']);
		$value['jfxg'] = $jfxg;	
		/*$cxxg = jn::cqxg($fight_sort); //检查是否有持续效果
		if (!empty($cxxg)) {
			$value['cxxg'] = $cxxg;
		}*/	
		$act = '';	
		return $value;			
	}	
	//疑兵四伏
	public static function jn_12($tag,&$fight_sort,$i,$jn_level) {
		$role = $fight_sort[$tag]['role'];
		$general_sort = $fight_sort[$tag]['sort_id'];
		if ($role == 2) {
			$enemy = 1;
		} else {
			$enemy = 2;
		}
		foreach ($fight_sort as $jlKey => $jlValue) {
			if ($jlValue['role'] == $enemy && $jlValue['command_soldier'] > 0) {
				$choseKey[$jlKey] = $jlValue;
			}
		}	
		$chosedKey = array_rand($choseKey,1);	
		$mz = jn::mzl($fight_sort[$tag]['agility_value'],$fight_sort[$chosedKey]['agility_value']);
		if ($mz == 0) {
			$dfxg[] = array('hid'=>$fight_sort[$chosedKey]['role'].$fight_sort[$chosedKey]['sort_id'],'sac'=>$fight_sort[$chosedKey]['command_soldier'],'eff'=>1);
		} else {
			$fight_sort[$chosedKey]['ybsf'] = 12;   //中疑兵四伏	
			$fight_sort[$chosedKey]['hm'] = $i;      //昏迷
			$dfxg[] = array('hid'=>$fight_sort[$chosedKey]['role'].$fight_sort[$chosedKey]['sort_id'],'sac'=>$fight_sort[$chosedKey]['command_soldier'],'eff'=>2);
		}
		//行动方信息
		$value['act'] = 3;
		$value['hid'] = $role.$general_sort;
		$value['pos'] = intval($fight_sort[$tag]['distance']);					
		$value['eff'] = 12;					 
		$value['sac'] = intval($fight_sort[$tag]['command_soldier']);	
		$value['dfxg'] = $dfxg;
		/*$cxxg = jn::cqxg($fight_sort); //检查是否有持续效果
		if (!empty($cxxg)) {
			$value['cxxg'] = $cxxg;
		}*/	
		$act = '';	
		return $value;				
	}	
	//夺魂一击
	public static function jn_13($tag,&$fight_sort,$i,$jn_level) {
		$role = $fight_sort[$tag]['role'];
		$general_sort = $fight_sort[$tag]['sort_id'];
		$act = 3;
		$can_attack_object = jn::randFindAttackObject($tag,$fight_sort,$act);		
		if ($role == 2) {
			$enemy = 1;
		} else {
			$enemy = 2;
		}
		$base_sh = jn::sh($tag,$fight_sort,$can_attack_object);
		$xs = jn::jnzy(13,$jn_level);
		$kill_value = $base_sh * $xs[0];
		$dtx = 0;
		$fight_sort = actModel::attacking($fight_sort,$can_attack_object,$tag,$i,$kill_value,$dtx);		
		//行动方信息
		$value['act'] = $act;
		$value['hid'] = $role.$general_sort;
		$value['pos'] = intval($fight_sort[$tag]['distance']);					
		$value['eff'] = 13;					 
		$value['sac'] = intval($fight_sort[$tag]['command_soldier']);	
		//常规被攻击信息
		//$bAttackInfo = actModel::decodeAttackInfo($fight_sort[$can_attack_object][$fight_sort[$tag]['animation']],$tag,$value['eff'],$i);			
		//$value['dfxg'] = array(array('hid'=>$fight_sort[$can_attack_object]['role'].$fight_sort[$can_attack_object]['sort_id'],'sac'=>intval($bAttackInfo['left']),'eff'=>intval($bAttackInfo['tx'])));		
		if ($fight_sort[$can_attack_object]['command_soldier'] > 0 && $dtx == 0) {
			if (rand(0,99) < $xs[1]) {
				$fight_sort[$can_attack_object]['dhyj'] = 13;  //中夺魂一击
				$fight_sort[$can_attack_object]['hm'] = $i;
				$dtx = 2;
			}
		}
		$value['dfxg'] = array(array('hid'=>$fight_sort[$can_attack_object]['role'].$fight_sort[$can_attack_object]['sort_id'],'sac'=>$fight_sort[$can_attack_object]['command_soldier'],'eff'=>$dtx));
		/*$cxxg = jn::cqxg($fight_sort); //检查是否有持续效果
		if (!empty($cxxg)) {
			$value['cxxg'] = $cxxg;
		}*/
		$act = '';		
		return $value;					
	}	
	//妙手回春
	public static function jn_14($tag,&$fight_sort,$i,$jn_level) {
		$role = $fight_sort[$tag]['role'];
		$general_sort = $fight_sort[$tag]['sort_id'];		
		/*foreach ($fight_sort as $jlKey => $jlValue) {
			if ($jlValue['role'] == $role && $jlValue['command_soldier'] > 0) {
				$choseKey[$jlKey] = $jlValue;
			}
		}
		$chosedKey = array_rand($choseKey,1);*/
		//$chosedKey = actModel::txmb($fight_sort,$tag);
		$chosedKey = actModel::txmb14($fight_sort,$role);
		if ($chosedKey === 'nodata') {
			return false;
		}
		$xs = jn::jnzy(14,$jn_level);	
		//行动方信息
		$value['act'] = 3;
		$value['hid'] = $role.$general_sort;
		$value['pos'] = intval($fight_sort[$tag]['distance']);					
		$value['eff'] = 14;					 
		$value['sac'] = intval($fight_sort[$tag]['command_soldier']);	
		$sm = $fight_sort[$chosedKey]['command_soldier'];
		$smsx = round($fight_sort[$chosedKey]['physical_value'],0) * 10;
		$zzsm = floor($xs + $sm);
		if ($zzsm > $smsx) {
			$zzsm = $smsx;
		}
		$fight_sort[$chosedKey]['command_soldier'] = $zzsm;
		$value['jfxg'] = array(array('hid'=>$fight_sort[$chosedKey]['role'].$fight_sort[$chosedKey]['sort_id'],'sac'=>$fight_sort[$chosedKey]['command_soldier']));
		/*$cxxg = jn::cqxg($fight_sort); //检查是否有持续效果
		if (!empty($cxxg)) {
			$value['cxxg'] = $cxxg;
		}	*/
		$act = '';	
		return $value;
	}
	//雨露春风
	public static function jn_15($tag,&$fight_sort,$i,$jn_level) {
		$role = $fight_sort[$tag]['role'];
		$general_sort = $fight_sort[$tag]['sort_id'];	
		$xs = jn::jnzy(15,$jn_level);
		foreach ($fight_sort as $jlKey => $jlValue) {
			if ($jlValue['role'] == $role && $jlValue['command_soldier'] > 0) {
				$sm = $jlValue['command_soldier'];
				$smsx = round($jlValue['physical_value'],0) * 10;
				$zzsm = floor($smsx * $xs / 100 + $sm);				
				if ($zzsm > $smsx) {
					$zzsm = $smsx;
				}								
				$fight_sort[$jlKey]['command_soldier'] = $zzsm;
				$jfxg[] = array('hid'=>$fight_sort[$jlKey]['role'].$fight_sort[$jlKey]['sort_id'],'sac'=>$fight_sort[$jlKey]['command_soldier']);
				$sm = null;
				$smsx = null;
				$zzsm = null;
			}
		}			
		//行动方信息
		$value['act'] = 3;
		$value['hid'] = $role.$general_sort;
		$value['pos'] = intval($fight_sort[$tag]['distance']);					
		$value['eff'] = 15;					 
		$value['sac'] = intval($fight_sort[$tag]['command_soldier']);
		$value['jfxg'] = $jfxg;
		/*$cxxg = jn::cqxg($fight_sort); //检查是否有持续效果
		if (!empty($cxxg)) {
			$value['cxxg'] = $cxxg;
		}*/
		$act = '';		
		return $value;		
	}	
	//偷天换日
	public static function jn_16($tag,&$fight_sort,$i,$jn_level) {
		$role = $fight_sort[$tag]['role'];
		$general_sort = $fight_sort[$tag]['sort_id'];
		$act = 0;
		$xs = jn::jnzy(16,$jn_level);
		$can_attack_object = jn::lastAttackObject($tag,$fight_sort,$act);
		$base_sh = jn::sh($tag,$fight_sort,$can_attack_object);		
		$kill_value = $base_sh * $xs[1];
		$dtx = 0;
		$fight_sort = actModel::attacking($fight_sort,$can_attack_object,$tag,$i,$kill_value,$dtx);			
		if ($dtx == 1) {
			$kill_value = 0;
		}
		foreach ($fight_sort as $jlKey => $jlValue) {
			if ($jlValue['role'] == $role && $jlValue['command_soldier'] > 0) {
				$kxwj[] = $jlKey;
			}
		}		
		if (count($kxwj) > 3) {
			$chosedKey = array_rand($kxwj,3);
			foreach ($chosedKey as $chosedKeyValue) {
				$chosedwj[] = $kxwj[$chosedKeyValue];
			}
		} else {
			$chosedwj = $kxwj;
		}
		foreach ($chosedwj as $kxwjValue) {
			$jlValue = $fight_sort[$kxwjValue];
			$sm = $jlValue['command_soldier'];
			$smsx = round($jlValue['physical_value'],0) * 10;
			//$sssm = $smsx - $sm;
			$zzsm = $sm + floor($kill_value * $xs[0]);
			if ($zzsm > $smsx) {
				$zzsm = $smsx;
			}						
			$fight_sort[$kxwjValue]['command_soldier'] = $zzsm;
			$jfxg[] = array('hid'=>$jlValue['role'].$jlValue['sort_id'],'sac'=>$zzsm);
			$sm = null;
			$smsx = null;
			$zzsm = null;
			$sssm = null;
			$jlValue = null;
		}														
		//行动方信息
		$value['act'] = 3;
		$value['hid'] = $role.$general_sort;
		$value['pos'] = intval($fight_sort[$tag]['distance']);					
		$value['eff'] = 16;					 
		$value['sac'] = intval($fight_sort[$tag]['command_soldier']);
		$value['jfxg'] = $jfxg;
		$value['dfxg'] = array(array('hid'=>$fight_sort[$can_attack_object]['role'].$fight_sort[$can_attack_object]['sort_id'],'sac'=>$fight_sort[$can_attack_object]['command_soldier'],'eff'=>$dtx));
		$act = '';		
		return $value;		
	}	
	
	//烈火焚身
	public static function jn_17($tag,&$fight_sort,$i,$jn_level) {
		$role = $fight_sort[$tag]['role'];
		$general_sort = $fight_sort[$tag]['sort_id'];
		if ($role == 2) {
			$enemy = 1;
		} else {
			$enemy = 2;
		}
		$gjjcInfo = jn::jnzy(17,$jn_level);
		$gjjc = $gjjcInfo[0];
		if (!empty($fight_sort[$tag]['zsft'])) {
			$zsft = $fight_sort[$tag]['zsft'];
		} else {
			$zsft = 0;
		}
		if (!empty($fight_sort[$tag]['zgzt'])) {
			$zgzt = $fight_sort[$tag]['zgzt'];
		} else {
			$zgzt = 0;
		}
		if (!empty($fight_sort[$tag]['zjgj'])) {
			$zjgj = $fight_sort[$tag]['zjgj'];
		} else {
			$zjgj = 0;
		}								
        for ($j = 0; $j < count($fight_sort); $j++) {
        	if ($fight_sort[$j]['role'] == $enemy) {
        		$sm = $fight_sort[$j]['command_soldier'];
        		if ($sm > 0) {  
        			if ($zsft != 0) {
        				$fight_sort[$tag]['zsft'] = $zsft;
        			} 
        			if ($zgzt != 0) {
        				$fight_sort[$tag]['zgzt'] = $zgzt;
        			}   
        			if ($zjgj != 0) {
        				$fight_sort[$tag]['zjgj'] = $zjgj;
        			}          			  			
        			$base_sh = jn::sh($tag,$fight_sort,$j);
        			$kill_value = $base_sh * $gjjc;
        			$mz = jn::mzl($fight_sort[$tag]['agility_value'],$fight_sort[$j]['agility_value']);
        			/*此处随机命中函数*/
        			if ($mz == 0) {
        				$dfxg[] = array('hid'=>$fight_sort[$j]['role'].$fight_sort[$j]['sort_id'],'sac'=>$sm,'eff'=>1);
        			} else {
        				$left_hp = ($sm - $kill_value >= 0) ? (floor($sm - $kill_value)) : 0;
        				$fight_sort[$j]['command_soldier'] = $left_hp; 
        				$dfxg[] = array('hid'=>$fight_sort[$j]['role'].$fight_sort[$j]['sort_id'],'sac'=>$left_hp,'eff'=>0);
        			}
        			$base_sh = $kill_value = $mz = $left_hp = null;
        		}
        	}
        }
		//行动方信息
		$fight_sort[$tag]['command_soldier'] = ceil($fight_sort[$tag]['command_soldier'] * (1 - $gjjcInfo[1]));
		$value['act'] = 3;
		$value['hid'] = $role.$general_sort;
		$value['pos'] = intval($fight_sort[$tag]['distance']);					
		$value['eff'] = 17;					 
		$value['sac'] = intval($fight_sort[$tag]['command_soldier']);	
		//常规被攻击信息
		$value['dfxg'] = $dfxg;
		$act = '';		
		return $value;			
	}		
	
	//寻找最后一个攻击对象
	public static function lastAttackObject($tag,$fight_sort,&$act) {
		for ($i=0 ; $i < count($fight_sort); $i++) {
			if ($fight_sort[$i]['command_soldier'] <= 0) {
				continue;
			}			
			if ($fight_sort[$i]['role'] == 2) {
				$hxkey2[] = $i;	
			} else {
				$hxkey1[] = $i;						
			}
		}
		$role = $fight_sort[$tag]['role'];            //发起攻击的是攻击方还是防守方	
		$act = 3;
		if ($role == 2)	{
			return max($hxkey1);
		} else {
			return max($hxkey2);
		}
	}
	
	//随机寻找攻击对象
	public static function randFindAttackObject($tag,$fight_sort,&$act) {
		for ($i=0 ; $i < count($fight_sort); $i++) {
			if ($fight_sort[$i]['command_soldier'] <= 0) {
				continue;
			}			
			if ($fight_sort[$i]['role'] == 2) {
				$hxkey2[] = $i;	
			} else {
				$hxkey1[] = $i;						
			}
		}	
		$role = $fight_sort[$tag]['role'];            //发起攻击的是攻击方还是防守方
		$act = 3;
		if ($role == 2) {
			$randKey = array_rand($hxkey1,1);				
			return $hxkey1[$randKey];
		} else {
			$randKey = array_rand($hxkey2,1);
			return $hxkey2[$randKey];				
		}		
	}
	
	//计算伤害值
	public static function sh($tag,&$fight_sort,$can_attack_object) {
		$defend_info = $fight_sort[$can_attack_object]; 
		$attack_info = $fight_sort[$tag];
		$zjgj = 0;
		$zjfy = 1;
		if (!empty($fight_sort[$tag]['zsft']) || !empty($fight_sort[$tag]['zgzt'])) {
			$zjgj = $fight_sort[$tag]['zjgj'];
			if (!empty($fight_sort[$tag]['zsft'])) {
				unset($fight_sort[$tag]['zsft']);
			} else {
				unset($fight_sort[$tag]['zgzt']);
			}
			unset($fight_sort[$tag]['zjgj']);
		}
		if (!empty($fight_sort[$can_attack_object]['jzzz']) || !empty($fight_sort[$can_attack_object]['hyqj'])) {
			$zjfy = $fight_sort[$can_attack_object]['zjfy'];
			if (!empty($fight_sort[$can_attack_object]['jzzz'])) {
				unset($fight_sort[$can_attack_object]['jzzz']);
			} else {
				unset($fight_sort[$can_attack_object]['hyqj']);
			}
			unset($fight_sort[$can_attack_object]['zjfy']);
		}	
		if ($zjgj != 0) {
			$attack_value = $zjgj;
		} else {	
			$attack_value = $attack_info['attack_value'];                 //攻击方攻击值
		}		
		$defend_value = $defend_info['defense_value'] * $zjfy;                //防守方防御值 
		$jfbz = $fight_sort[$tag]['professional'];                 //己方兵种
		$dfbz = $fight_sort[$can_attack_object]['professional'];   //对方兵种
		$jl = 23 - ($fight_sort[$tag]['distance'] + $fight_sort[$can_attack_object]['distance']);
		$bzxk = jn::bzxk($jfbz,$dfbz);
		//$kill_value = ($attack_value * 5 + rand(0,ceil(200 * 0.06 * $attack_info['general_level'] * 0.25))) - ($defend_value * 2.5 + rand(0,ceil(200 * 0.06 * $defend_info['general_level'] * 0))) + 0;
	   	$kill_value = ($attack_value * 5 - $defend_value * 2.5) * 0.75 * $bzxk;
	   	if ($jfbz == 3 || $jfbz == 5) {
	   		$kill_value = $kill_value * 0.8;
	   		if ($jfbz == 3 && $jl < 6) {
	   			$kill_value = $kill_value * 1.5;                   //弓箭兵半攻击距离内攻击乘1.5
	   		} elseif ($jfbz == 5 && $jl < 11) {
	   			$kill_value = $kill_value / 1.5;                   //连弩兵半攻击距离内攻击除1.5
	   		}	   		
	   	}
		if ($kill_value < 1) {
		   	$kill_value = 1;
		}	
		return $kill_value;	
	}
	
	//兵种相克
	public static function bzxk($jfbz,$dfbz) {
		//1，重甲兵 2，长枪兵，3，弓箭兵，4，轻骑兵  5，连弩兵
		$xksj = array(
			1=>array(1=>1,2=>1,3=>1.25,4=>1,5=>1),
			2=>array(1=>0.8,2=>1,3=>1,4=>1.25,5=>1),
			3=>array(1=>0.8,2=>1.25,3=>1,4=>1,5=>1),
			4=>array(1=>0.8,2=>1,3=>1,4=>1,5=>1.25),
			5=>array(1=>0.9,2=>1,3=>1,4=>1,5=>1)
		);
		return $xksj[$jfbz][$dfbz];
	}
	
	//长期效果
	public static function cqxg($fight_sort) {
		$cx1 = $cx2 = $cx3 = $cx4 = $cx5 = $cx6 = '';
		$cx = array();
		$xginfo = array();
		foreach ($fight_sort as $key => $value) {			
			if (!empty($value['thyj'])) {  //夺魂一击
				$cx[] = 13;
			}
			if (!empty($value['sbsf'])) {  //疑兵四伏
				$cx[] = 12;
			}
			if (!empty($value['zgzt'])) {  //战鼓震天
				$cx[] = 11;
			}
			if (!empty($value['zsft'])) {  //战神附体
				$cx[] = 10;
			}
			if (!empty($value['hyqj'])) {  //护佑全军
				$cx[] = 9;
			}
			if (!empty($value['jzzz'])) {  //金钟之罩
				$cx[] = 8;
			}	
			if (!empty($cx)) {
				$xg = implode(',',$cx);
				$xginfo[] = array('hid'=>$value['role'].$value['sort_id'],'xgbh'=>$xg);
			}	
			unset($cx);
			$xg = '';
		}
		if (!empty($xginfo)) {
			return $xginfo;
		} else {
			return false;
		}
	}
	
	//命中率计算
	public static function mzl($attack_agility_value,$defend_agility_value) {
	   $hit_probability_value = ($attack_agility_value / $defend_agility_value - 1) / 2 + 0.8;
	   if ($hit_probability_value > 0.9) {
	   	  $hit_probability_value = 0.9; 
	   } elseif ($hit_probability_value < 0.1) {
	   	  $hit_probability_value = 0.1;
	   }
	   
	   if (rand(1,100) <= $hit_probability_value * 100) {
	   	   $hit_probability = 1;
	   } else {
	   	   $hit_probability = 0; 
	   }
	   return $hit_probability;	
	}
}