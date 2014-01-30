<?php
// 返回所需战功数值
function needzg($zc) {
	$zgvalues = array(109=>0, 108=>1048, 107=>2494, 106=>4511, 105=>7217, 104=>10735, 103=>15192, 102=>20852, 101=>28028, 100=>36916, 99=>47718, 98=>60640, 97=>76104, 96=>94584, 95=>116363, 94=>141730, 93=>170978, 92=>204699, 91=>243541, 90=>287880, 89=>338096, 88=>394573, 87=>458084, 86=>529462, 85=>609178, 84=>695064, 83=>787375, 82=>886366, 81=>992295, 80=>1105421, 79=>1226541, 78=>1352068, 77=>1482031, 76=>1616458, 75=>1755376, 74=>1898811, 73=>2046789, 72=>2199336, 71=>2356476);
	return isset($zgvalues[$zc]) ? $zgvalues[$zc] : 0;
}

// 返回座次称号
function getZcCh($zc) {
	global $rank_lang;
	
	$zcch = array(1=>$rank_lang['zcch_1'],2=>$rank_lang['zcch_2'],3=>$rank_lang['zcch_3'],4=>$rank_lang['zcch_4'],5=>$rank_lang['zcch_5'],6=>$rank_lang['zcch_6'],7=>$rank_lang['zcch_7'],8=>$rank_lang['zcch_8'],9=>$rank_lang['zcch_9'],10=>$rank_lang['zcch_10'],11=>$rank_lang['zcch_11'],12=>$rank_lang['zcch_12'],13=>$rank_lang['zcch_13'],14=>$rank_lang['zcch_14'],15=>$rank_lang['zcch_15'],16=>$rank_lang['zcch_16'],17=>$rank_lang['zcch_17'],18=>$rank_lang['zcch_18'],19=>$rank_lang['zcch_19'],20=>$rank_lang['zcch_20'],21=>$rank_lang['zcch_21'],22=>$rank_lang['zcch_22'],23=>$rank_lang['zcch_23'],24=>$rank_lang['zcch_24'],25=>$rank_lang['zcch_25'],26=>$rank_lang['zcch_26'],27=>$rank_lang['zcch_27'],28=>$rank_lang['zcch_28'],29=>$rank_lang['zcch_29'],30=>$rank_lang['zcch_30'],31=>$rank_lang['zcch_31'],32=>$rank_lang['zcch_32'],33=>$rank_lang['zcch_33'],34=>$rank_lang['zcch_34'],35=>$rank_lang['zcch_35'],36=>$rank_lang['zcch_36'],37=>$rank_lang['zcch_37'],38=>$rank_lang['zcch_38'],39=>$rank_lang['zcch_39'],40=>$rank_lang['zcch_40'],41=>$rank_lang['zcch_41'],42=>$rank_lang['zcch_42'],43=>$rank_lang['zcch_43'],44=>$rank_lang['zcch_44'],45=>$rank_lang['zcch_45'],46=>$rank_lang['zcch_46'],47=>$rank_lang['zcch_47'],48=>$rank_lang['zcch_48'],49=>$rank_lang['zcch_49'],50=>$rank_lang['zcch_50'],51=>$rank_lang['zcch_51'],52=>$rank_lang['zcch_52'],53=>$rank_lang['zcch_53'],54=>$rank_lang['zcch_54'],55=>$rank_lang['zcch_55'],56=>$rank_lang['zcch_56'],57=>$rank_lang['zcch_57'],58=>$rank_lang['zcch_58'],59=>$rank_lang['zcch_59'],60=>$rank_lang['zcch_60'],61=>$rank_lang['zcch_61'],62=>$rank_lang['zcch_62'],63=>$rank_lang['zcch_63'],64=>$rank_lang['zcch_64'],65=>$rank_lang['zcch_65'],66=>$rank_lang['zcch_66'],67=>$rank_lang['zcch_67'],68=>$rank_lang['zcch_68'],69=>$rank_lang['zcch_69'],70=>$rank_lang['zcch_70'],71=>$rank_lang['zcch_71'],72=>$rank_lang['zcch_72'],73=>$rank_lang['zcch_73'],74=>$rank_lang['zcch_74'],75=>$rank_lang['zcch_75'],76=>$rank_lang['zcch_76'],77=>$rank_lang['zcch_77'],78=>$rank_lang['zcch_78'],79=>$rank_lang['zcch_79'],80=>$rank_lang['zcch_80'],81=>$rank_lang['zcch_81'],82=>$rank_lang['zcch_82'],83=>$rank_lang['zcch_83'],84=>$rank_lang['zcch_84'],85=>$rank_lang['zcch_85'],86=>$rank_lang['zcch_86'],87=>$rank_lang['zcch_87'],88=>$rank_lang['zcch_88'],89=>$rank_lang['zcch_89'],90=>$rank_lang['zcch_90'],91=>$rank_lang['zcch_91'],92=>$rank_lang['zcch_92'],93=>$rank_lang['zcch_93'],94=>$rank_lang['zcch_94'],95=>$rank_lang['zcch_95'],96=>$rank_lang['zcch_96'],97=>$rank_lang['zcch_97'],98=>$rank_lang['zcch_98'],99=>$rank_lang['zcch_99'],100=>$rank_lang['zcch_100'],101=>$rank_lang['zcch_101'],102=>$rank_lang['zcch_102'],103=>$rank_lang['zcch_103'],104=>$rank_lang['zcch_104'],105=>$rank_lang['zcch_105'],106=>$rank_lang['zcch_106'],107=>$rank_lang['zcch_107'],108=>$rank_lang['zcch_108'],109=>$rank_lang['zcch_109']);
	
	return $zcch[$zc];
}

// 根据等级返回获取和减少战功的上限值
function getZgLimit($level) {
	$zglimit = array(1=>array(100, -40),2=>array(200, -80),3=>array(300, -120),4=>array(400, -160),5=>array(500, -200),6=>array(600, -240),7=>array(700, -280),8=>array(800, -320),9=>array(900, -360),10=>array(1000, -400),11=>array(1100, -440),12=>array(1200, -480),13=>array(1300, -520),14=>array(1400, -560),15=>array(1500, -600),16=>array(1600, -640),17=>array(1700, -680),18=>array(1800, -720),19=>array(1900, -760),20=>array(2100, -840),21=>array(2300, -920),22=>array(2500, -1000),23=>array(2700, -1080),24=>array(2900, -1160),25=>array(3100, -1240),26=>array(3300, -1320),27=>array(3500, -1400),28=>array(3700, -1480),29=>array(3900, -1560),30=>array(4200, -1680),31=>array(4500, -1800),32=>array(4800, -1920),33=>array(5100, -2040),34=>array(5400, -2160),35=>array(5700, -2280),36=>array(6000, -2400),37=>array(6300, -2520),38=>array(6600, -2640),39=>array(6900, -2760),40=>array(7300, -2920),41=>array(7700, -3080),42=>array(8100, -3240),43=>array(8500, -3400),44=>array(8900, -3560),45=>array(9300, -3720),46=>array(9700, -3880),47=>array(10100, -4040),48=>array(10500, -4200),49=>array(10900, -4360),50=>array(11400, -4560),51=>array(11900, -4760),52=>array(12400, -4960),53=>array(12900, -5160),54=>array(13400, -5360),55=>array(13900, -5560),56=>array(14400, -5760),57=>array(14900, -5960),58=>array(15400, -6160),59=>array(15900, -6360),60=>array(16500, -6600),61=>array(17100, -6840),62=>array(17700, -7080),63=>array(18300, -7320),64=>array(18900, -7560),65=>array(19500, -7800),66=>array(20100, -8040),67=>array(20700, -8280),68=>array(21300, -8520),69=>array(21900, -8760),70=>array(22600, -9040));
	return $zglimit[$level];
}

//战斗获取战功值公式
//function hqzgz($wfgj,$wffy,$wftl,$wfmj,$wfjndj1,$wfjndj2,$dfgj,$dffy,$dftl,$dfmj,$dfjndj1,$dfjndj2,$dzms) {
/*
$wfRoleInfo  我方玩家信息     
$dfRoleInfo  敌方玩家信息
$wfGinfo     我方武将信息
$dfGinfo     敌方武将信息
$dzms        对战模式  1 玩家对战  2 系统对战
$wfplayer    我方player对象
$dfplayer    敌方player对象
*/
function hqzlz($wfRoleInfo,$dfRoleInfo,$wfGinfo,$dfGinfo,$dzms,$wfplayer,$dfplayer) {
	static $zbslots = array('helmet','carapace','arms','shoes');	
    $wfzbgjjcArray = $wfzbfyjcArray = $wfzbtljcArray = $wfzbmjjcArray = array();
	foreach ($zbslots as $slot) {
        $wfzbid = $wfGinfo [0] [$slot];
        if ($wfzbid != 0) {
          $wfzbInfo = $wfplayer->GetZBSX ($wfzbid );
          $wfzbgjjcArray [] = $wfzbInfo['gj'];
          $wfzbfyjcArray [] = $wfzbInfo['fy'];
          $wfzbtljcArray [] = $wfzbInfo['tl'];
          $wfzbmjjcArray [] = $wfzbInfo['mj'];
        }
    }    
	$wfzbgjjc = array_sum ( $wfzbgjjcArray );
	$wfzbfyjc = array_sum ( $wfzbfyjcArray );
    $wfzbtljc = array_sum ( $wfzbtljcArray );
    $wfzbmjjc = array_sum ( $wfzbmjjcArray );	
    
	$wfjwdj = $wfRoleInfo ['mg_level'];
	$wfjwInfo = jwmc ( $wfjwdj );
	$wfjwjc = 1 + $wfjwInfo ['jc'] / 100;	
	$wfgdj = $wfGinfo[0] ['general_level'];
	$wfjj = $wfGinfo[0] ['professional_level'];
	$wftf = $wfGinfo[0]['understanding_value'];
	$wfllcs = $wfGinfo[0]['llcs'];
	$wfsxxs = genModel::sxxs ( $wfGinfo[0]['professional'] );
	$wfgj = genModel::hqwjsx ( $wfgdj, $wftf, $wfjj, $wfllcs, $wfjwjc, $wfzbgjjc, $wfsxxs ['gj'], $wfGinfo[0]['py_gj'] );
	$wffy = genModel::hqwjsx ( $wfgdj, $wftf, $wfjj, $wfllcs, $wfjwjc, $wfzbfyjc, $wfsxxs ['fy'], $wfGinfo[0]['py_fy'] );
	$wftl = genModel::hqwjsx ( $wfgdj, $wftf, $wfjj, $wfllcs, $wfjwjc, $wfzbtljc, $wfsxxs ['tl'], $wfGinfo[0]['py_tl'] );
	$wfmj = genModel::hqwjsx ( $wfgdj, $wftf, $wfjj, $wfllcs, $wfjwjc, $wfzbmjjc, $wfsxxs ['mj'], $wfGinfo[0]['py_mj'] );	
	$wfjndj1 = $wfGinfo[0]['jn1_level'];
	$wfjndj2 = $wfGinfo[0]['jn2_level'];	
	$wfzsx = $wfgj + $wffy + $wftl + $wfmj + 43 * ($wfjndj1 + $wfjndj2); //攻击方武将总属性
	
	if ($dzms == 1) {
		$dfzbgjjcArray = $dfzbfyjcArray = $dfzbtljcArray = $dfzbmjjcArray = array();
		foreach ($zbslots as $slot) {
	        $dfzbid = $dfGinfo [0] [$slot];
	        if ($dfzbid != 0) {
	          $dfzbInfo = $dfplayer->GetZBSX ($dfzbid );
	          $dfzbgjjcArray [] = $dfzbInfo['gj'];
	          $dfzbfyjcArray [] = $dfzbInfo['fy'];
	          $dfzbtljcArray [] = $dfzbInfo['tl'];
	          $dfzbmjjcArray [] = $dfzbInfo['mj'];
	        }
	    }    
		$dfzbgjjc = array_sum ( $dfzbgjjcArray );
		$dfzbfyjc = array_sum ( $dfzbfyjcArray );
	    $dfzbtljc = array_sum ( $dfzbtljcArray );
	    $dfzbmjjc = array_sum ( $dfzbmjjcArray );	
		$dfjwdj = $dfRoleInfo ['mg_level'];
		$dfjwInfo = jwmc ( $dfjwdj );
		$dfjwjc = 1 + $dfjwInfo ['jc'] / 100;	
		$dfgdj = $dfGinfo[0] ['general_level'];
		$dfjj = $dfGinfo[0] ['professional_level'];
		$dftf = $dfGinfo[0]['understanding_value'];
		$dfllcs = $dfGinfo[0]['llcs'];
		$dfsxxs = genModel::sxxs ( $dfGinfo[0]['professional'] );
		$dfgj = genModel::hqwjsx ( $dfgdj, $dftf, $dfjj, $dfllcs, $dfjwjc, $dfzbgjjc, $dfsxxs ['gj'], $dfGinfo[0]['py_gj'] );
		$dffy = genModel::hqwjsx ( $dfgdj, $dftf, $dfjj, $dfllcs, $dfjwjc, $dfzbfyjc, $dfsxxs ['fy'], $dfGinfo[0]['py_fy'] );
		$dftl = genModel::hqwjsx ( $dfgdj, $dftf, $dfjj, $dfllcs, $dfjwjc, $dfzbtljc, $dfsxxs ['tl'], $dfGinfo[0]['py_tl'] );
		$dfmj = genModel::hqwjsx ( $dfgdj, $dftf, $dfjj, $dfllcs, $dfjwjc, $dfzbmjjc, $dfsxxs ['mj'], $dfGinfo[0]['py_mj'] );	
		$dfjndj1 = $dfGinfo[0]['jn1_level'];
		$dfjndj2 = $dfGinfo[0]['jn2_level'];		
		$dfzsx = $dfgj + $dffy + $dftl + $dfmj + 43 * ($dfjndj1 + $dfjndj2); //防守方武将总属性
	} else {
		$dfzsx = $dfGinfo[0]['attack_value'] + $dfGinfo[0]['defense_value'] + $dfGinfo[0]['physical_value'] + $dfGinfo[0]['agility_value']; //防守方武将总属性
	}
	$sxc = $wfzsx - $dfzsx;  //属性差
	$qx = 1.2;          //曲线
	$yxz = 0.015056;    //影响
	$maxsx = max(array($wfzsx,$dfzsx));  //找出属性大的一方
	$minsx = min(array($wfzsx,$dfzsx));  //找出属性小的一方 
	$jczg = $yxz * pow($minsx,$qx);      //基础战功值
	$x = $sxc / $maxsx * 100;
	$A = 830;
	$cl = 50;
	if ($dzms == 1) {
		$B = 1;
	} else {
		$B = 0.5;
	}
	if ($x <= 0) {
		$p = 1;
	} else {
		$p = pow(2.718,-$x * $x / $A);
	}
	return ceil($jczg * $p * $B + $cl);
}
?>