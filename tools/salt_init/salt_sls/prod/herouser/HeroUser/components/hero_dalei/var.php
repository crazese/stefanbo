<?php
//积分奖励$lscs（连胜次数）

//积分兑换数据
function jfdhsj() {
	/* dhlx 兑换类型1声望2道具
	 * dhsl 可兑换数量
	 * xhjf 消耗积分
	 * djid 道具id
	 * xzlx 限制类型0无限， 1级别 ，2爵位， 3声望
	 * dhsx 兑换上限
	 */
	$data = array(
		1=>array('intID'=>1,'dhlx'=>1,'dhsl'=>100,'xhjf'=>2000,'djid'=>0,'xzlx'=>0,'dhsx'=>2000),
		2=>array('intID'=>2,'dhlx'=>2,'dhsl'=>1,'xhjf'=>1500,'djid'=>10040,'xzlx'=>0,'dhsx'=>0),
		3=>array('intID'=>3,'dhlx'=>2,'dhsl'=>1,'xhjf'=>1500,'djid'=>10001,'xzlx'=>0,'dhsx'=>0),
		4=>array('intID'=>4,'dhlx'=>2,'dhsl'=>1,'xhjf'=>100000,'djid'=>10013,'xzlx'=>1,'dhsx'=>20),
		5=>array('intID'=>5,'dhlx'=>2,'dhsl'=>1,'xhjf'=>250000,'djid'=>18654,'xzlx'=>2,'dhsx'=>16),
	);
	return $data;
}