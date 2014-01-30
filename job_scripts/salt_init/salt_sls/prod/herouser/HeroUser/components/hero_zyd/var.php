<?php
//野地npc数据
/* $jb 野地级别
 * $lx 野地类型 (1、草原2、农田3、森林4、山丘5、沼泽)
 * 1，重甲将领  2，长枪将领  3，弓箭将领，4，轻骑将领，5，连弩将领 * 
 * */
function ydsj($jb,$lx) {
	global $zyd_lang;
	$data = array(
		1=>array(
		   1=>array('xm'=>$zyd_lang['xm'][0],'bz'=>1,'gj'=>26,'fy'=>26,'tl'=>32,'mj'=>21,'dj'=>4,'sex'=>0,'avatar'=>'YX016'),
		   2=>array('xm'=>$zyd_lang['xm'][1],'bz'=>1,'gj'=>98,'fy'=>98,'tl'=>117,'mj'=>78,'dj'=>11,'sex'=>0,'avatar'=>'YX032'),
		   3=>array('xm'=>$zyd_lang['xm'][2],'bz'=>1,'gj'=>172,'fy'=>172,'tl'=>206,'mj'=>138,'dj'=>18,'sex'=>0,'avatar'=>'YX041'),
		   4=>array('xm'=>$zyd_lang['xm'][3],'bz'=>1,'gj'=>287,'fy'=>287,'tl'=>344,'mj'=>230,'dj'=>25,'sex'=>1,'avatar'=>'YX031'),
		   5=>array('xm'=>$zyd_lang['xm'][4],'bz'=>1,'gj'=>402,'fy'=>402,'tl'=>482,'mj'=>322,'dj'=>32,'sex'=>0,'avatar'=>'YX038'),
		   6=>array('xm'=>$zyd_lang['xm'][5],'bz'=>1,'gj'=>659,'fy'=>659,'tl'=>790,'mj'=>527,'dj'=>39,'sex'=>0,'avatar'=>'YX031'),
		   7=>array('xm'=>$zyd_lang['xm'][6],'bz'=>1,'gj'=>839,'fy'=>839,'tl'=>1007,'mj'=>671,'dj'=>46,'sex'=>0,'avatar'=>'YX008'),
		   8=>array('xm'=>$zyd_lang['xm'][7],'bz'=>1,'gj'=>1115,'fy'=>1115,'tl'=>1338,'mj'=>892,'dj'=>53,'sex'=>1,'avatar'=>'YX017'),
		   9=>array('xm'=>$zyd_lang['xm'][8],'bz'=>1,'gj'=>1620,'fy'=>1620,'tl'=>1944,'mj'=>1296,'dj'=>60,'sex'=>0,'avatar'=>'YX013'),
		   10=>array('xm'=>$zyd_lang['xm'][9],'bz'=>1,'gj'=>1927,'fy'=>1927,'tl'=>2312,'mj'=>1541,'dj'=>67,'sex'=>0,'avatar'=>'YX027')		   
		),
		2=>array(
		   1=>array('xm'=>$zyd_lang['xm'][10],'bz'=>2,'gj'=>32,'fy'=>21,'tl'=>32,'mj'=>21,'dj'=>4,'sex'=>0,'avatar'=>'YX022'),		   
		   2=>array('xm'=>$zyd_lang['xm'][11],'bz'=>2,'gj'=>117,'fy'=>78,'tl'=>117,'mj'=>78,'dj'=>11,'sex'=>0,'avatar'=>'YX025'),
		   3=>array('xm'=>$zyd_lang['xm'][12],'bz'=>2,'gj'=>206,'fy'=>138,'tl'=>206,'mj'=>138,'dj'=>18,'sex'=>0,'avatar'=>'YX022'),
		   4=>array('xm'=>$zyd_lang['xm'][13],'bz'=>2,'gj'=>344,'fy'=>230,'tl'=>344,'mj'=>230,'dj'=>25,'sex'=>0,'avatar'=>'YX043'),
		   5=>array('xm'=>$zyd_lang['xm'][14],'bz'=>2,'gj'=>482,'fy'=>322,'tl'=>482,'mj'=>322,'dj'=>32,'sex'=>1,'avatar'=>'YX037'),
		   6=>array('xm'=>$zyd_lang['xm'][15],'bz'=>2,'gj'=>790,'fy'=>527,'tl'=>790,'mj'=>527,'dj'=>39,'sex'=>0,'avatar'=>'YX025'),
		   7=>array('xm'=>$zyd_lang['xm'][16],'bz'=>2,'gj'=>1007,'fy'=>671,'tl'=>1007,'mj'=>671,'dj'=>46,'sex'=>0,'avatar'=>'YX031'),
		   8=>array('xm'=>$zyd_lang['xm'][17],'bz'=>2,'gj'=>1338,'fy'=>892,'tl'=>1338,'mj'=>892,'dj'=>53,'sex'=>0,'avatar'=>'YX024'),
		   9=>array('xm'=>$zyd_lang['xm'][18],'bz'=>2,'gj'=>1944,'fy'=>1296,'tl'=>1944,'mj'=>1296,'dj'=>60,'sex'=>0,'avatar'=>'YX011'),
		   10=>array('xm'=>$zyd_lang['xm'][19],'bz'=>2,'gj'=>2312,'fy'=>1541,'tl'=>2312,'mj'=>1541,'dj'=>67,'sex'=>0,'avatar'=>'YX008')		   
		),
		3=>array(
		   1=>array('xm'=>$zyd_lang['xm'][20],'bz'=>3,'gj'=>26,'fy'=>16,'tl'=>26,'mj'=>37,'dj'=>4,'sex'=>1,'avatar'=>'YX014'),		   
		   2=>array('xm'=>$zyd_lang['xm'][21],'bz'=>3,'gj'=>98,'fy'=>59,'tl'=>98,'mj'=>137,'dj'=>11,'sex'=>0,'avatar'=>'YX007'),
		   3=>array('xm'=>$zyd_lang['xm'][22],'bz'=>3,'gj'=>172,'fy'=>103,'tl'=>172,'mj'=>241,'dj'=>18,'sex'=>0,'avatar'=>'YX031'),
		   4=>array('xm'=>$zyd_lang['xm'][23],'bz'=>3,'gj'=>287,'fy'=>172,'tl'=>287,'mj'=>402,'dj'=>25,'sex'=>0,'avatar'=>'YX005'),
		   5=>array('xm'=>$zyd_lang['xm'][24],'bz'=>3,'gj'=>402,'fy'=>241,'tl'=>402,'mj'=>563,'dj'=>32,'sex'=>0,'avatar'=>'YX025'),
		   6=>array('xm'=>$zyd_lang['xm'][25],'bz'=>3,'gj'=>659,'fy'=>395,'tl'=>659,'mj'=>922,'dj'=>39,'sex'=>0,'avatar'=>'YX044'),
		   7=>array('xm'=>$zyd_lang['xm'][26],'bz'=>3,'gj'=>839,'fy'=>504,'tl'=>839,'mj'=>1175,'dj'=>46,'sex'=>0,'avatar'=>'YX004'),
		   8=>array('xm'=>$zyd_lang['xm'][27],'bz'=>3,'gj'=>1115,'fy'=>669,'tl'=>1115,'mj'=>1561,'dj'=>53,'sex'=>0,'avatar'=>'YX031'),
		   9=>array('xm'=>$zyd_lang['xm'][28],'bz'=>3,'gj'=>1620,'fy'=>927,'tl'=>1620,'mj'=>2268,'dj'=>60,'sex'=>0,'avatar'=>'YX018'),
		   10=>array('xm'=>$zyd_lang['xm'][29],'bz'=>3,'gj'=>1927,'fy'=>1156,'tl'=>1927,'mj'=>2697,'dj'=>67,'sex'=>0,'avatar'=>'YX032')		   
		),	
		4=>array(
		   1=>array('xm'=>$zyd_lang['xm'][30],'bz'=>5,'gj'=>37,'fy'=>21,'tl'=>21,'mj'=>26,'dj'=>4,'sex'=>1,'avatar'=>'BJ005'),		   
		   2=>array('xm'=>$zyd_lang['xm'][31],'bz'=>5,'gj'=>137,'fy'=>78,'tl'=>78,'mj'=>98,'dj'=>11,'sex'=>0,'avatar'=>'YX010'),
		   3=>array('xm'=>$zyd_lang['xm'][32],'bz'=>5,'gj'=>241,'fy'=>138,'tl'=>138,'mj'=>172,'dj'=>18,'sex'=>0,'avatar'=>'YX031'),
		   4=>array('xm'=>$zyd_lang['xm'][33],'bz'=>5,'gj'=>402,'fy'=>230,'tl'=>230,'mj'=>287,'dj'=>25,'sex'=>0,'avatar'=>'YX042'),
		   5=>array('xm'=>$zyd_lang['xm'][34],'bz'=>5,'gj'=>563,'fy'=>322,'tl'=>322,'mj'=>402,'dj'=>32,'sex'=>0,'avatar'=>'YX023'),
		   6=>array('xm'=>$zyd_lang['xm'][35],'bz'=>5,'gj'=>922,'fy'=>527,'tl'=>527,'mj'=>659,'dj'=>39,'sex'=>1,'avatar'=>'BJ006'),
		   7=>array('xm'=>$zyd_lang['xm'][36],'bz'=>5,'gj'=>1175,'fy'=>671,'tl'=>671,'mj'=>839,'dj'=>46,'sex'=>0,'avatar'=>'YX031'),
		   8=>array('xm'=>$zyd_lang['xm'][37],'bz'=>5,'gj'=>1561,'fy'=>892,'tl'=>892,'mj'=>1115,'dj'=>53,'sex'=>0,'avatar'=>'YX007'),
		   9=>array('xm'=>$zyd_lang['xm'][38],'bz'=>5,'gj'=>2268,'fy'=>1296,'tl'=>1296,'mj'=>1620,'dj'=>60,'sex'=>0,'avatar'=>'YX015'),
		   10=>array('xm'=>$zyd_lang['xm'][39],'bz'=>5,'gj'=>2697,'fy'=>1540,'tl'=>1540,'mj'=>1927,'dj'=>67,'sex'=>0,'avatar'=>'YX034')		   
		),		
		5=>array(
		   1=>array('xm'=>$zyd_lang['xm'][40],'bz'=>4,'gj'=>32,'fy'=>16,'tl'=>32,'mj'=>26,'dj'=>4,'sex'=>1,'avatar'=>'YX017'),		   
		   2=>array('xm'=>$zyd_lang['xm'][41],'bz'=>4,'gj'=>117,'fy'=>59,'tl'=>117,'mj'=>98,'dj'=>11,'sex'=>0,'avatar'=>'YX013'),
		   3=>array('xm'=>$zyd_lang['xm'][42],'bz'=>4,'gj'=>206,'fy'=>103,'tl'=>206,'mj'=>172,'dj'=>18,'sex'=>0,'avatar'=>'YX022'),
		   4=>array('xm'=>$zyd_lang['xm'][43],'bz'=>4,'gj'=>344,'fy'=>172,'tl'=>344,'mj'=>287,'dj'=>25,'sex'=>0,'avatar'=>'YX008'),
		   5=>array('xm'=>$zyd_lang['xm'][44],'bz'=>4,'gj'=>482,'fy'=>241,'tl'=>482,'mj'=>402,'dj'=>32,'sex'=>0,'avatar'=>'YX018'),
		   6=>array('xm'=>$zyd_lang['xm'][45],'bz'=>4,'gj'=>790,'fy'=>395,'tl'=>790,'mj'=>659,'dj'=>39,'sex'=>1,'avatar'=>'YX014'),
		   7=>array('xm'=>$zyd_lang['xm'][46],'bz'=>4,'gj'=>1007,'fy'=>504,'tl'=>1007,'mj'=>839,'dj'=>46,'sex'=>0,'avatar'=>'YX039'),
		   8=>array('xm'=>$zyd_lang['xm'][47],'bz'=>4,'gj'=>1338,'fy'=>669,'tl'=>1338,'mj'=>1115,'dj'=>53,'sex'=>0,'avatar'=>'YX042'),
		   9=>array('xm'=>$zyd_lang['xm'][48],'bz'=>4,'gj'=>1944,'fy'=>972,'tl'=>1944,'mj'=>1620,'dj'=>60,'sex'=>0,'avatar'=>'YX032'),
		   10=>array('xm'=>$zyd_lang['xm'][49],'bz'=>4,'gj'=>2312,'fy'=>1156,'tl'=>2312,'mj'=>1927,'dj'=>67,'sex'=>0,'avatar'=>'YX013')		   
		)								
	);
	return $data[$lx][$jb]; 
}

//资源的产出描述   $zylx 野地类型 (1、草原2、农田3、森林4、山丘5、沼泽)
/*function zydcc($zylx) {
	$mssj = array(1=>'铜钱',2=>'军粮',3=>'石材碎片,绢布碎片,木材碎片,陶土碎片,铁矿碎片,金刚石碎片,玄铁碎片',4=>'绿将卡残片',5=>'红宝石,玛瑙,翡翠,白玉');
	return $mssj[$zylx];
}*/


//资源点资源生成数据
/* $zylx 资源点类型
 * $zyjb 资源点级别
 * $sj   资源成长时间 
 * $zddj 指定道具ID
 * */
function zydzy($zylx,$zyjb,$sj,$zddj = 0) {
	if ($sj > 28800) {
		$sj = 28800;
	}
	if ($sj < 3600) {
		return 0;
	}
	switch ($zylx) {
		case 1:
			$cl = array(1=>100/3600,2=>200/3600,3=>300/3600,4=>400/3600,5=>500/3600,6=>600/3600,7=>700/3600,8=>800/3600,9=>900/3600,10=>1000/3600);
			$sh = array('tq'=>floor($sj * $cl[$zyjb]));
		break;
		case 2:
			$cl = array(1=>0.5/3600,2=>0.75/3600,3=>1/3600,4=>1.25/3600,5=>1.5/3600,6=>1.75/3600,7=>2/3600,8=>2.25/3600,9=>2.5/3600,10=>2.75/3600);
			$sh = array('jl'=>floor($sj * $cl[$zyjb]));
		break;	
		case 3:
			if ($zyjb < 5) {
				$zyInfo = array(1=>20018,2=>20022,3=>20019,4=>20020,5=>20021);
				$zy = array_rand($zyInfo,1);
				$hqdj = $zyInfo[$zy];
			} else {
				$hqdj = 20009;
			}			
			$cl = array(1=>0.25,2=>0.5,3=>0.75,4=>1,5=>0.25,6=>0.5,7=>0.75,8=>1,9=>1.25,10=>1.5);
			$sqsj = floor($sj / 3600);			
			if ($sqsj > 0) {
				$hqsj = array();
				for ($i = 0; $i < $sqsj; $i++) {
					if ($cl[$zyjb] > 1) {
						if (rand(0,99) < ($cl[$zyjb] - 1) * 100) {
							$hqsj[] = 2;
						} else {
							$hqsj[] = 1;
						}
					} else {
						if (rand(0,99) < $cl[$zyjb] * 100) {
							$hqsj[] = 1;
						}						
					}
				}
				if (!empty($hqsj)) {
					$sl = array_sum($hqsj);
					$sh = array('dj'=>array($hqdj => $sl));
				} else {
					$sh = 0;
				}	
			} else {
				$sh = 0;
			}			
		break;	
		case 4:			
			$cl = array(1=>array('jp'=>20006,'cl'=>0.5),2=>array('jp'=>20006,'cl'=>0.75),3=>array('jp'=>20006,'cl'=>1),4=>array('jp'=>20007,'cl'=>0.5),5=>array('jp'=>20007,'cl'=>0.75),6=>array('jp'=>20007,'cl'=>1),7=>array('jp'=>20008,'cl'=>0.5),8=>array('jp'=>20008,'cl'=>0.75),9=>array('jp'=>20008,'cl'=>1),10=>array('jp'=>20008,'cl'=>1.25));
			$sqsj = floor($sj / 3600);	
			$hqdj = $cl[$zyjb]['jp'];		
			if ($sqsj > 0) {
				$hqsj = array();
				for ($i = 0; $i < $sqsj; $i++) {
					if ($cl[$zyjb]['cl'] > 1) {
						if (rand(0,99) < ($cl[$zyjb]['cl'] - 1) * 100) {
							$hqsj[] = 2;
						} else {
							$hqsj[] = 1;
						}
					} else {
						if (rand(0,99) < $cl[$zyjb]['cl'] * 100) {
							$hqsj[] = 1;
						}
					}
				}
				if (!empty($hqsj)) {
					$sl = array_sum($hqsj);
					$sh = array('dj'=>array($hqdj => $sl));
				} else {
					$sh = 0;
				}	
			} else {
				$sh = 0;
			}			
		break;	
		case 5:
			//20115红宝石   20118玛瑙20124翡翠20121白玉
			$cl = array(1=>array('jp'=>20115,'cl'=>0.25),2=>array('jp'=>20115,'cl'=>0.5),3=>array('jp'=>20115,'cl'=>0.75),4=>array('jp'=>20118,'cl'=>0.25),5=>array('jp'=>20118,'cl'=>0.5),6=>array('jp'=>20118,'cl'=>0.75),7=>array('jp'=>20124,'cl'=>0.25),8=>array('jp'=>20124,'cl'=>0.5),9=>array('jp'=>20124,'cl'=>0.75),10=>array('jp'=>20121,'cl'=>0.5));
			$sqsj = floor($sj / 3600);	
			$hqdj = $cl[$zyjb]['jp'];		
			if ($sqsj > 0) {
				$hqsj = array();
				for ($i = 0; $i < $sqsj; $i++) {
					if ($cl[$zyjb]['cl'] > 1) {
						if (rand(0,99) < ($cl[$zyjb]['cl'] - 1) * 100) {
							$hqsj[] = 2;
						} else {
							$hqsj[] = 1;
						}						
					} else {
						if (rand(0,99) < $cl[$zyjb]['cl'] * 100) {
							$hqsj[] = 1;
						}
					}
				}
				if (!empty($hqsj)) {
					$sl = array_sum($hqsj);
					$sh = array('dj'=>array($hqdj => $sl));
				} else {
					$sh = 0;
				}	
			} else {
				$sh = 0;
			}			
		break;	
		default:
			$sh = 0;
		break;				
	}
	return $sh;
}

//资源点名称数据
function zydmc($zylx) {
	global $zyd_lang;
	$data = array(1=>$zyd_lang['var_1'],2=>$zyd_lang['var_2'],3=>$zyd_lang['var_3'],4=>$zyd_lang['var_4'],5=>$zyd_lang['var_5']);
	return $data[$zylx];
}
//逐鹿资源点名称
function zlzydmc($zylx) {
	global $zyd_lang;
	$data = array(1=>$zyd_lang['jsdj_1'],2=>$zyd_lang['jsdj_2'],3=>$zyd_lang['jsdj_3'],4=>$zyd_lang['jsdj_4'],5=>$zyd_lang['jsdj_5'],6=>$zyd_lang['jsdj_6'],7=>$zyd_lang['jsdj_7']);
	return $data[$zylx];
}

function zydcc($zylx) { 
	global $zyd_lang;
	$data = array(1=>array(array('mc'=>$zyd_lang['scyd_1'],'icon'=>'icon_tq')),2=>array(array('mc'=>$zyd_lang['scyd_2'],'icon'=>'icon_jl')),3=>array(array('mc'=>$zyd_lang['var_6'],'icon'=>'Ico000'),array('mc'=>$zyd_lang['var_7'],'icon'=>'Ico010')),4=>array(array('mc'=>$zyd_lang['var_8'],'icon'=>'Ico005')),5=>array(array('mc'=>$zyd_lang['var_9'],'icon'=>'Ico037')));
	return $data[$zylx];
}

//逐鹿战开始时间$zlid(逐鹿ID)
function zyzkssj($zlid) {
	//官方时间
	static $zlkssj = array(1=>12,2=>13,3=>17,4=>18,5=>19,6=>20,7=>21);
	//测试数据
	//static $zlkssj = array(1=>10,2=>11,3=>12,4=>13,5=>14,6=>15,7=>16);
	return $zlkssj[$zlid];
}
