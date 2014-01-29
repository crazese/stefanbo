<?php
// 强化公式常量
define('QHXS', 0.1591);
// 完美击杀次数
define('WM_KILL_COUNT', 3);
// 扩包道具
define('KBDJ', 20000);
// BOSS关卡号
define('BOSS_STAGE_NO', 4);
// 杜康酒
define('DKJ_ITEMID', 10031);
// 江湖令itemid
define('JHL_ITEMID', 10014);
// 聚义令itemid
define('JYL_ITEMID', 10015);
// 石材碎片itemid
define('SCSP_ITEMID', 20018);
// 木材碎片itemid
define('MCSP_ITEMID', 20019);
// 陶土碎片itemid
define('TTSP_ITEMID', 20020);
// 铁矿碎片itemid
define('TKSP_ITEMID', 20021);
// 绢布碎片itemid 
define('JBSP_ITEMID', 20022);
// 金刚石碎片itemid
define('JGSSP_ITEMID', 20009);
// 精钢铁碎片itemid
define('JGTSP_ITEMID', 20010);
// 龙鳞片碎片itemid
define('LLPSP_ITEMID', 20011);
// 金缕线碎片itemid
define('JLXSP_ITEMID', 20012);
// 飞羽绒碎片itemid
define('FYRSP_ITEMID', 20013);
// 新手礼包itemid
define('XSLB_ITEMID', 20001);
// VIP1礼包itemid
define('VIP1LB_ITEMID', 20002);
// VIP2礼包itemid
define('VIP2LB_ITEMID', 20003);
// VIP3礼包itemid
define('VIP3LB_ITEMID', 20004);
// VIP4礼包itemid
define('VIP4LB_ITEMID', 20005);
// 大中小铜币包
define('BIGTQB_ITEMID', 20057);
define('MIDTQB_ITEMID', 20056);
define('SMALLTQB_ITEMID', 20055);
// 大中小军粮包
define('BIGJLB_ITEMID', 20060);
define('MIDJLB_ITEMID', 20059);
define('SMALLJLB_ITEMID', 20058);
// 大中小强化材料包
define('BIGQHB_ITEMID', 20065);
define('MIDQHB_ITEMID', 20066);
define('SMALLQHB_ITEMID', 20067);
// 大中小升级材料包
define('BIGJZB_ITEMID', 20068);
define('MIDJZB_ITEMID', 20069);
define('SMALLJZB_ITEMID', 20070);
// 江湖令10,30,50,250
define('JHL10_ITEMID', 20061);
define('JHL30_ITEMID', 20062);
define('JHL50_ITEMID', 20063);
define('JHL250_ITEMID', 20064);
// 强化材料
define('JGS_ITEMID', 10001);
define('JGT_ITEMID', 10002);
define('LLP_ITEMID', 10003);
define('JLX_ITEMID', 10004);
define('FYR_ITEMID', 10005);
// 大中小银票包
define('BIGYPB_ITEMID', 20073);
define('MIDYPB_ITEMID', 20072);
define('SMALLYPB_ITEMID', 20071);
define('SUPERYPB_ITEMID', 20097);
// 大中小玄铁包
define('BIGXTB_ITEMID', 20096);
define('MIDXTB_ITEMID', 20095);
define('SMALLXTB_ITEMID', 20094);

// 遗忘技能所需银票
define('DELETE_SKILL_YINPAO', 50);

// 新手绿装
define('SHDL_ITEMID', 41201);
define('SHSJ_ITEMID', 42201);
define('SHCX_ITEMID', 43201);
define('SHPD_ITEMID', 44201);

// 封测礼包
define('FCLB_ITEMID', 20074);

// 玄铁
define('XT_ITEMID', 10040);

define('YSLB1_ITEMID', 20001);
define('YSLB15_ITEMID', 20002);
define('YSLB30_ITEMID', 20003);
define('YSLB45_ITEMID', 20004);
define('YSLB60_ITEMID', 20005);

define('CZLB1_ITEMID', 20075);
define('CZLB5_ITEMID', 20076);
define('CZLB10_ITEMID', 20077);
define('CZLB15_ITEMID', 20078);
define('CZLB20_ITEMID', 20079);
define('CZLB25_ITEMID', 20080);
define('CZLB30_ITEMID', 20081);
define('CZLB35_ITEMID', 20082);
define('CZLB40_ITEMID', 20083);
define('CZLB45_ITEMID', 20084);
define('CZLB50_ITEMID', 20085);
define('CZLB55_ITEMID', 20086);
define('CZLB60_ITEMID', 20087);
define('CZLB65_ITEMID', 20088);
define('CZLB70_ITEMID', 20089);

define('ZONGZI_ITEMID', 20113);
define('CGCARD_ITEMID', 20114);
define('BLUECARD_ITEMID', 10012);
define('ZISECARD_ITEMID', 10013);

// 专属武器
define('LGBOW_ITEMID', 49000);
define('HALFMOON_ITEMID', 49001);
define('RUNWIND_ITEMID', 49002);
define('HITTIGER_ITEMID',49003);
define('ARCHLORD_ITEMID', 49004);

define('MANAO_ITEMID', 20118);
define('FEICUI_ITEMID', 20124);
define('BAIYU_ITEMID', 20121);

define('KGC_ITEMID', 18588);

// 判断武将是否可以装备专属武器
function canEquipExclusiveWeapon($general_name, $weapon_itemid) {	
	global $tools_lang;
	$zswq_arr = array(LGBOW_ITEMID=>0, HALFMOON_ITEMID=>0, RUNWIND_ITEMID=>0, HITTIGER_ITEMID=>0, ARCHLORD_ITEMID=>0);
	if(array_key_exists($weapon_itemid, $zswq_arr)) {
		if($general_name == $tools_lang['var_gen_name1'] && $weapon_itemid == LGBOW_ITEMID) return true;	
		if(($general_name == $tools_lang['var_gen_name2'] || $general_name == $tools_lang['var_gen_name3']) && $weapon_itemid == HALFMOON_ITEMID) return true;
		if($general_name == $tools_lang['var_gen_name4'] && $weapon_itemid == RUNWIND_ITEMID) return true;
		if($general_name == $tools_lang['var_gen_name5'] && $weapon_itemid == HITTIGER_ITEMID) return true;
		if($general_name == $tools_lang['var_gen_name6'] && $weapon_itemid == ARCHLORD_ITEMID) return true;
		return false;
	}
	
	return true;
}

// 闯关关卡所需道具信息
function getUnlockItem($pDiffcuity, $pStage_no) {
	$need_item_arr = array(
		1=>array( // 难度
			1=>18522, // 大关
			2=>18523,
			3=>18524,
			4=>18525,
			5=>18526,
			6=>18527,
			7=>18528,
			8=>18529
		),
		2=>array( 
			1=>18530, 
			2=>18531,
			3=>18532,
			4=>18533,
			5=>18534,
			6=>18535,
			7=>18536,
			8=>18537
		),
		3=>array( 
			1=>18538, 
			2=>18539,
			3=>18540,
			4=>18541,
			5=>18542,
			6=>18543,
			7=>18544,
			8=>18545
		),
		4=>array( 
			1=>18546, 
			2=>18547,
			3=>18548,
			4=>18549,
			5=>18550,
			6=>18551,
			7=>18552,
			8=>18553
		),
		5=>array( 
			1=>18561, 
			2=>18562,
			3=>18563,
			4=>18564,
			5=>18565,
			6=>18566,
			7=>18567,
			8=>18568
		),
		6=>array( 
			1=>18590, 
			2=>18591,
			3=>18592,
			4=>18593,
			5=>18594,
			6=>18595,
			7=>18596,
			8=>18597
		)//// 备注 增加难度时添加所需的解锁道具
	);
	
	return $need_item_arr[$pDiffcuity][$pStage_no];
}

// 非强化分解规则
function getFjRule() {
	$fjRule = array(
		// 帽子
		'helmet'=>array(
			1=>array(
				'rarity'=>array(
					1=>array(
						// 龙鳞片碎片
						'itemid'=>LLPSP_ITEMID,
						'num'=>1
					),
					2=>array(
						// 龙鳞片碎片
						'itemid'=>LLPSP_ITEMID,
						'num'=>array('min'=>2, 'max'=>4)
					)
				)					
			),

			10=>array(
				// 龙鳞片碎片
				'itemid'=>LLPSP_ITEMID,
				'num'=>array('min'=>5, 'max'=>7)					
			),				
			20=>array(
				// 龙鳞片碎片
				'itemid'=>LLPSP_ITEMID,
				'num'=>array('min'=>8, 'max'=>10)					
			),
			30=>array(
				// 龙鳞片碎片
				'itemid'=>LLPSP_ITEMID,
				'num'=>array('min'=>9, 'max'=>11)					
			),
			40=>array(
				// 龙鳞片碎片
				'itemid'=>LLPSP_ITEMID,
				'num'=>array('min'=>12, 'max'=>14)					
			),
			50=>array(
				// 龙鳞片碎片
				'itemid'=>LLPSP_ITEMID,
				'num'=>array('min'=>15, 'max'=>17)					
			),
			60=>array(
				// 龙鳞片碎片
				'itemid'=>LLPSP_ITEMID,
				'num'=>array('min'=>18, 'max'=>20)					
			)
		),
		// 衣服
		'clothes'=>array(
			1=>array(
				'rarity'=>array(
					1=>array(
						'itemid'=>JLXSP_ITEMID,
						'num'=>1
					),
					2=>array(					
						'itemid'=>JLXSP_ITEMID,
						'num'=>array('min'=>2, 'max'=>4)
					)	
				)			
			),
			10=>array(		
				'itemid'=>JLXSP_ITEMID,
				'num'=>array('min'=>5, 'max'=>7)					
			),				
			20=>array(
				'itemid'=>JLXSP_ITEMID,
				'num'=>array('min'=>8, 'max'=>10)						
			),
			30=>array(
				'itemid'=>JLXSP_ITEMID,
				'num'=>array('min'=>9, 'max'=>11)					
			),
			40=>array(
				'itemid'=>JLXSP_ITEMID,
				'num'=>array('min'=>12, 'max'=>14)						
			),
			50=>array(
				'itemid'=>JLXSP_ITEMID,
				'num'=>array('min'=>15, 'max'=>17)					
			),
			60=>array(
				'itemid'=>JLXSP_ITEMID,
				'num'=>array('min'=>18, 'max'=>20)						
			)			
		
		),
		// 鞋子
		'shoes'=>array(
			1=>array(
				'rarity'=>array(
					1=>array(
						'itemid'=>FYRSP_ITEMID,
						'num'=>1
					),
					2=>array(					
						'itemid'=>FYRSP_ITEMID,
						'num'=>array('min'=>2, 'max'=>4)
					)	
				)
			),
			10=>array(
				// 飞羽绒碎片
				'itemid'=>FYRSP_ITEMID,
				'num'=>array('min'=>5, 'max'=>7)
			),
			20=>array(
				// 飞羽绒碎片
				'itemid'=>FYRSP_ITEMID,
				'num'=>array('min'=>8, 'max'=>10)
			),
			30=>array(
				// 飞羽绒碎片
				'itemid'=>FYRSP_ITEMID,
				'num'=>array('min'=>9, 'max'=>11)
			),
			40=>array(
				// 飞羽绒碎片
				'itemid'=>FYRSP_ITEMID,
				'num'=>array('min'=>12, 'max'=>14)
			),
			50=>array(
				// 飞羽绒碎片
				'itemid'=>FYRSP_ITEMID,
				'num'=>array('min'=>15, 'max'=>17)
			),
			60=>array(
				// 飞羽绒碎片
				'itemid'=>FYRSP_ITEMID,
				'num'=>array('min'=>18, 'max'=>20)
			)
		),
		// 武器
		'weapon'=>array(
			1=>array(
				'rarity'=>array(
					1=>array(
						'itemid'=>JGTSP_ITEMID,
						'num'=>1
					),
					2=>array(					
						'itemid'=>JGTSP_ITEMID,
						'num'=>array('min'=>2, 'max'=>4)
					)	
				)
			),
			10=>array(
				// 精钢铁碎片
				'itemid'=>JGTSP_ITEMID,
				'num'=>array('min'=>5, 'max'=>7)
			),
			20=>array(
				// 精钢铁碎片
				'itemid'=>JGTSP_ITEMID,
				'num'=>array('min'=>8, 'max'=>10)
			),
			30=>array(
				// 精钢铁碎片
				'itemid'=>JGTSP_ITEMID,
				'num'=>array('min'=>9, 'max'=>11)
			),
			40=>array(
				// 精钢铁碎片
				'itemid'=>JGTSP_ITEMID,
				'num'=>array('min'=>12, 'max'=>14)
			),
			50=>array(
				// 精钢铁碎片
				'itemid'=>JGTSP_ITEMID,
				'num'=>array('min'=>15, 'max'=>17)
			),
			60=>array(
				// 精钢铁碎片
				'itemid'=>JGTSP_ITEMID,
				'num'=>array('min'=>18, 'max'=>20)
			)	
		),
		'addition'=>array(			
			1=>array(
				// 金刚石碎片
				'itemid'=>JGSSP_ITEMID,
				'num'=>array('min'=>5, 'max'=>7)
			),
			10=>array(
				// 金刚石碎片
				'itemid'=>JGSSP_ITEMID,
				'num'=>array('min'=>8, 'max'=>10)
			),
			20=>array(
				// 金刚石碎片
				'itemid'=>JGSSP_ITEMID,
				'num'=>array('min'=>11, 'max'=>13)
			),
			30=>array(
				// 金刚石碎片
				'itemid'=>JGSSP_ITEMID,
				'num'=>array('min'=>14, 'max'=>16)
			),
			40=>array(
				// 金刚石碎片
				'itemid'=>JGSSP_ITEMID,
				'num'=>array('min'=>17, 'max'=>19)
			),
			50=>array(
				// 金刚石碎片
				'itemid'=>JGSSP_ITEMID,
				'num'=>array('min'=>20, 'max'=>22)
			),
			60=>array(
				// 金刚石碎片
				'itemid'=>JGSSP_ITEMID,
				'num'=>array('min'=>23, 'max'=>25)
			)	
		)
	);
	return $fjRule; 
}

// 银票包
function getYpbRule() {
	$ypbRule = array(
		BIGYPB_ITEMID=>50,
		MIDYPB_ITEMID=>25,
		SMALLYPB_ITEMID=>10,
		SUPERYPB_ITEMID=>500
	);
	return $ypbRule;
}

// 玄铁包
function getXtbRule() {
	$xtbRule = array(
			BIGXTB_ITEMID=>50,
			MIDXTB_ITEMID=>10,
			SMALLXTB_ITEMID=>5
	);
	return $xtbRule;
}

// 封测礼包
function getFcbRule() {
	$fcbRule = array(
		'YB'=>200,
		'YP'=>200,
		'TQ'=>10000,
		'item'=>array(
			0=>array('itemid'=>JYL_ITEMID, 'num'=>2),
			1=>array('itemid'=>MIDQHB_ITEMID, 'num'=>5),
			2=>array('itemid'=>MIDJZB_ITEMID, 'num'=>5),
			3=>array('itemid'=>MIDJLB_ITEMID, 'num'=>5),
			4=>array('itemid'=>SHDL_ITEMID, 'num'=>1),
			5=>array('itemid'=>SHSJ_ITEMID, 'num'=>1),
			6=>array('itemid'=>SHCX_ITEMID, 'num'=>1),
			7=>array('itemid'=>SHPD_ITEMID, 'num'=>1)
		)		
	);
	return $fcbRule;
}

// 铜币包
function getTqbRule() {
	$tqbRule = array(
		BIGTQB_ITEMID=>array('min'=>450,'max'=>550),
		MIDTQB_ITEMID=>array('min'=>250, 'max'=>350),
		SMALLTQB_ITEMID=>array('min'=>50, 'max'=>100)
	);
	return $tqbRule;
}
// 军粮包
function getJlbRule() {
	$jlbRule = array(
		BIGJLB_ITEMID=>array('min'=>8,'max'=>12),
		MIDJLB_ITEMID=>array('min'=>4,'max'=>6),
		SMALLJLB_ITEMID=>array('min'=>2,'max'=>4)
	);
	return $jlbRule;
}
// 强化包
function getQhbRule() {
	$qhbRule = array(
		// 大强化包
		BIGQHB_ITEMID=>array(
			'info'=>array(
				// 金刚石
				JGS_ITEMID=>1 // 掉落几率
			),			
			'number'=>array('min'=>4,'max'=>6)	          // 掉落数量		
		),
		MIDQHB_ITEMID=>array(
			'info'=>array(
				JGS_ITEMID=>1
			),			
			'number'=>array('min'=>2,'max'=>3)	
		),
		SMALLQHB_ITEMID=>array(
			'info'=>array(
				JGSSP_ITEMID=>1
			),			
			'number'=>array('min'=>8, 'max'=>12)
		)
	);
	return $qhbRule; 
}
// 建筑包
function getJzbRule() {
	$jzbRule = array(
		BIGJZB_ITEMID=>array(
			'info'=>array(
				// 石材碎片
				SCSP_ITEMID=>0.2,
				// 木材碎片
				MCSP_ITEMID=>0.2,
				// 陶土碎片
				TTSP_ITEMID=>0.2,
				// 铁矿碎片
				TKSP_ITEMID=>0.2,
				// 绢布碎片
				JBSP_ITEMID=>0.2				
			),
			'number'=>array('min'=>9, 'max'=>11)
		),
		MIDJZB_ITEMID=>array(
			'info'=>array(
				SCSP_ITEMID=>0.2,
				MCSP_ITEMID=>0.2,
				TTSP_ITEMID=>0.2,
				TKSP_ITEMID=>0.2,
				JBSP_ITEMID=>0.2				
			),
			'number'=>array('min'=>4, 'max'=>6)
		),
		SMALLJZB_ITEMID=>array(
			'info'=>array(
				SCSP_ITEMID=>0.2,
				MCSP_ITEMID=>0.2,
				TTSP_ITEMID=>0.2,
				TKSP_ITEMID=>0.2,
				JBSP_ITEMID=>0.2				
			),
			'number'=>array('min'=>1, 'max'=>3)
		)
	);
	return $jzbRule;
}

// 遗忘技能可以使用的道具ID列表
function getForgetSkillToolsID(){
	return array(DKJ_ITEMID);
}

// 弃牌次数对应消耗江湖令个数
function getJhlRule() {
	$xhjhl = array(
		'1_1_0'=>array('yp'=>0, 'jhl'=>0),
		'1_1_1'=>array('yp'=>3, 'jhl'=>0),
		'1_1_2'=>array('yp'=>0, 'jhl'=>1),
		'1_1_3'=>array('yp'=>0, 'jhl'=>2),
		'1_1_4'=>array('yp'=>0, 'jhl'=>3),
		'1_1_5'=>array('yp'=>0, 'jhl'=>4)
	);
	
	return $xhjhl;
}

// 闯关热点掉落特殊装备表
function cgDlRule() {
	global $tools_lang;
	$cgdlzb = array(
		// 难度
		1=>array(
			// 大关
			1=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_1_1_4'], 'itemid'=>13201)),
			2=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_1_2_4'], 'itemid'=>11201)),
			3=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_1_3_4'], 'itemid'=>12201)),
			4=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_1_4_4'], 'itemid'=>14201)),
			5=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_1_5_4'], 'itemid'=>13202)),
			6=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_1_6_4'], 'itemid'=>11202)),
			7=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_1_7_4'], 'itemid'=>12202)),
			8=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_1_8_4'], 'itemid'=>14202))
		),	
		2=>array(
			// 大关
			1=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_2_1_4'], 'itemid'=>13303)),
			2=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_2_2_4'], 'itemid'=>11303)),
			3=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_2_3_4'], 'itemid'=>12303)),
			4=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_2_4_4'], 'itemid'=>14303)),
			5=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_2_5_4'], 'itemid'=>13304)),
			6=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_2_6_4'], 'itemid'=>11304)),
			7=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_2_7_4'], 'itemid'=>12304)),
			8=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_2_8_4'], 'itemid'=>14304))
		),	
		3=>array(
			// 大关
			1=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_3_1_4'], 'itemid'=>13405)),
			2=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_3_2_4'], 'itemid'=>11405)),
			3=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_3_3_4'], 'itemid'=>12405)),
			4=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_3_4_4'], 'itemid'=>14405)),
			5=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_3_5_4'], 'itemid'=>13406)),
			6=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_3_6_4'], 'itemid'=>11406)),
			7=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_3_7_4'], 'itemid'=>12406)),
			8=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_3_8_4'], 'itemid'=>14406))
		),
		4=>array(
			// 大关
			1=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_4_1_4'], 'itemid'=>13406)),
			2=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_4_2_4'], 'itemid'=>11406)),
			3=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_4_3_4'], 'itemid'=>12406)),
			4=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_4_4_4'], 'itemid'=>14406)),
			5=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_4_5_4'], 'itemid'=>13507)),
			6=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_4_6_4'], 'itemid'=>11507)),
			7=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_4_7_4'], 'itemid'=>12507)),
			8=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_4_8_4'], 'itemid'=>14507))
		),
		5=>array(
			// 大关
			1=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_5_1_4'], 'itemid'=>18555)),
			2=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_5_2_4'], 'itemid'=>18556)),
			3=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_5_3_4'], 'itemid'=>18557)),
			4=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_5_4_4'], 'itemid'=>18558)),
			5=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_5_5_4'], 'itemid'=>18555)),
			6=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_5_6_4'], 'itemid'=>18556)),
			7=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_5_7_4'], 'itemid'=>18557)),
			8=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_5_8_4'], 'itemid'=>18558))
		),
		6=>array(
			// 大关
			1=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_6_1_4'], 'itemid'=>20037)),
			2=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_6_2_4'], 'itemid'=>20037)),
			3=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_6_3_4'], 'itemid'=>20038)),
			4=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_6_4_4'], 'itemid'=>20038)),
			5=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_6_5_4'], 'itemid'=>20039)),
			6=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_6_6_4'], 'itemid'=>20039)),
			7=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_6_7_4'], 'itemid'=>20040)),
			8=>array(BOSS_STAGE_NO=>array('name'=>$tools_lang['var_dl_6_8_4'], 'itemid'=>20040))
		)
	);
	
	return $cgdlzb;
}

// 武器强化规则
function getWeaponReinforceRule() {
	$weaponReinforceRule = array(
		0=>array('effect'=>1,'specific'=>1,'common'=>1,'coins'=>100,'odds'=>0.95,'ingot'=>5),
		1=>array('effect'=>1.13 ,'specific'=>2,'common'=>1,'coins'=>150,'odds'=>0.9,'ingot'=>10),
		2=>array('effect'=>1.29 ,'specific'=>3,'common'=>1,'coins'=>225,'odds'=>0.85,'ingot'=>15),
		3=>array('effect'=>1.46 ,'specific'=>4,'common'=>2,'coins'=>338,'odds'=>0.8,'ingot'=>20),
		4=>array('effect'=>1.66 ,'specific'=>5,'common'=>2,'coins'=>507,'odds'=>0.75,'ingot'=>25),
		5=>array('effect'=>1.88 ,'specific'=>6,'common'=>2,'coins'=>761,'odds'=>0.7,'ingot'=>30),
		6=>array('effect'=>2.12 ,'specific'=>7,'common'=>3,'coins'=>1142,'odds'=>0.65,'ingot'=>35),
		7=>array('effect'=>2.38 ,'specific'=>8,'common'=>3,'coins'=>1713,'odds'=>0.6,'ingot'=>40),
		8=>array('effect'=>2.66 ,'specific'=>9,'common'=>3,'coins'=>2570,'odds'=>0.55,'ingot'=>45),
		9=>array('effect'=>2.96 ,'specific'=>10,'common'=>4,'coins'=>3855,'odds'=>0.5,'ingot'=>50),
		10=>array('effect'=>3.29 ,'specific'=>11,'common'=>4,'coins'=>5783,'odds'=>0.45,'ingot'=>55),
		11=>array('effect'=>3.63 ,'specific'=>12,'common'=>4,'coins'=>8675,'odds'=>0.4,'ingot'=>60),
		12=>array('effect'=>4.00 ,'specific'=>0,'common'=>0,'coins'=>0,'odds'=>0,'ingot'=>0)
	);
	
	return $weaponReinforceRule;
}

// 装备强化规则
function getEquipReinforceRule() {
	$equipReinforceRule = array(
			0=>array('effect'=>15,'tjp'=>0,'common'=>1,'coins'=>50,'odds'=>0.95,'ingot'=>5),
			1=>array('effect'=>16,'tjp'=>1,'common'=>1,'coins'=>100,'odds'=>0.95,'ingot'=>5),
			2=>array('effect'=>19,'tjp'=>1,'common'=>1,'coins'=>150,'odds'=>0.9,'ingot'=>10),
			3=>array('effect'=>22,'tjp'=>2,'common'=>2,'coins'=>250,'odds'=>0.9,'ingot'=>10),
			4=>array('effect'=>28,'tjp'=>2,'common'=>2,'coins'=>350,'odds'=>0.85,'ingot'=>15),
			5=>array('effect'=>34,'tjp'=>3,'common'=>2,'coins'=>500,'odds'=>0.85,'ingot'=>15),
			6=>array('effect'=>41,'tjp'=>3,'common'=>5,'coins'=>650,'odds'=>0.8,'ingot'=>20),
			7=>array('effect'=>52,'tjp'=>4,'common'=>5,'coins'=>800,'odds'=>0.8,'ingot'=>20),
			8=>array('effect'=>60,'tjp'=>4,'common'=>5,'coins'=>1000,'odds'=>0.75,'ingot'=>25),
			9=>array('effect'=>70,'tjp'=>5,'common'=>5,'coins'=>1250,'odds'=>0.75,'ingot'=>25),
			10=>array('effect'=>87,'tjp'=>5,'common'=>5,'coins'=>1500,'odds'=>0.7,'ingot'=>30),
			11=>array('effect'=>97,'tjp'=>6,'common'=>5,'coins'=>1750,'odds'=>0.7,'ingot'=>30),
			12=>array('effect'=>110,'tjp'=>6,'common'=>10,'coins'=>2050,'odds'=>0.65,'ingot'=>35),
			13=>array('effect'=>132,'tjp'=>7,'common'=>10,'coins'=>2400,'odds'=>0.65,'ingot'=>35),
			14=>array('effect'=>145,'tjp'=>7,'common'=>10,'coins'=>2700,'odds'=>0.6,'ingot'=>40),
			15=>array('effect'=>161,'tjp'=>8,'common'=>10,'coins'=>3100,'odds'=>0.6,'ingot'=>40),
			16=>array('effect'=>186,'tjp'=>8,'common'=>10,'coins'=>3500,'odds'=>0.55,'ingot'=>45),
			17=>array('effect'=>202,'tjp'=>9,'common'=>10,'coins'=>3900,'odds'=>0.55,'ingot'=>45),
			18=>array('effect'=>221,'tjp'=>9,'common'=>20,'coins'=>4350,'odds'=>0.5,'ingot'=>50),
			19=>array('effect'=>252,'tjp'=>10,'common'=>20,'coins'=>4850,'odds'=>0.5,'ingot'=>50),
			20=>array('effect'=>270,'tjp'=>10,'common'=>20,'coins'=>5350,'odds'=>0.45,'ingot'=>55),
			21=>array('effect'=>292,'tjp'=>11,'common'=>20,'coins'=>5850,'odds'=>0.45,'ingot'=>55),
			22=>array('effect'=>340,'tjp'=>11,'common'=>20,'coins'=>6400,'odds'=>0.4,'ingot'=>60),
			23=>array('effect'=>367,'tjp'=>12,'common'=>20,'coins'=>7000,'odds'=>0.4,'ingot'=>60),
			24=>array('effect'=>396,'tjp'=>0,'common'=>0,'coins'=>0,'odds'=>0,'ingot'=>0)
	);

	return $equipReinforceRule;
}

// 武器所需材料
function getWeaponReinforceNeed() {
	$weaponReinforceNeed = array(
		'commonItemid'=>JGS_ITEMID,  // 公共道具金刚石
		'specificItemid'=>JGT_ITEMID // 精钢铁
	);
	
	return $weaponReinforceNeed;
}

// 装备所需材料
function getEquipReinforceNeed() {
	$equipReinforceNeed = array('commonItemid'=>JGS_ITEMID);
	
	return $equipReinforceNeed;
}

// 强化装备分解
function getWeaponFjNeed() {
	$getWeaponFjNeed = array(
		'commonItemid'=>JGSSP_ITEMID,  // 公共道具金刚石
		'specificItemid'=>JGTSP_ITEMID // 精钢铁
	);
	
	return $getWeaponFjNeed;
}

// 强化装备分解 
function getEquipFjNeed() {
	$getEquipFjNeed = array(
		'helmet'=>array('specificItemid'=>LLPSP_ITEMID, 'commonItemid'=>JGSSP_ITEMID),  // 龙鳞片
		'clothes'=>array('specificItemid'=>JLXSP_ITEMID, 'commonItemid'=>JGSSP_ITEMID), // 金缕线
		'shoes'=>array('specificItemid'=>FYRSP_ITEMID, 'commonItemid'=>JGSSP_ITEMID)    // 飞羽绒
	);
	
	return $getEquipFjNeed;
}
?>