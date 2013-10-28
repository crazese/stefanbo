<?php
/*$level 事件级别
 *$showAll 是否显示所有事件
 */
function eventData($level,$showAll = false) {
	$data = array(
	     1=>array(
	        //引导任务图标并打怪
	     	1=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"m":"OwnCityMediator","data":[{"talk":"欢迎来到Q将水浒！这里有江湖义气儿女情长，却也危机四伏步步惊心!如何扬名立万，而非横尸街头？看看“任务”先！","btn":"9","f":true}]},{"data":[{"f":true,"talk":"前往阳谷上任的路上，有家黑店，大哥带领兄弟们一路除暴安良，咱们去除掉它！","btn":"10"}],"m":"MissionMediator"}]}',     	//引导脚本
	     	   'acc1'=>array('player_level'),  //触发条件1
	     	   'fin1'=>array('ckrw'),            //完成条件1 
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>1,                //事件编号  0,1,2,3,4,5,7,10,11,12,14,15,16,17,18,19,20
	     	   'jzxs'=>array('userHome'=>array(0,1,2,3,4,5,7,10,11,12,14,15,16,17,18,19,20),'userHome2'=>array(array(0,1,2,3,7,10),array(3,5,5,5,6,0))),  //只显示闯关和任务按钮，开启闯关和任务功能
	     	   'xstj'=>array('player_level') //显示条件
	     	),
	        //引导闯关
	     	2=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"m":"OwnCityMediator","data":[{"talk":"闯关是增加经验、提升等级、获得战利品的重要手段。","f":true,"btn":"6"}]},{"data":[{"talk":"","f":true,"btn":"1"}],"m":"ChuangGuanZhanMainMapMediator"},{"data":[{"talk":"","f":true,"btn":"60,62"}],"m":"ChuangGuanZhanMainMapMediator"},{"data":[{"talk":"","f":true,"btn":"1"}],"m":"ChuangGuanZhanMinorMapMediator"},{"data":[{"talk":"","f":true,"btn":"63,65"}],"m":"ChuangGuanZhanMainMapMediator"}]}',     	//引导脚本
	     	   'acc1'=>array('ckrw'),  //触发条件1
	     	   'fin1'=>array('cg'),            //完成条件1
	     	   'wccs'=>'',              //完成引导参数
	     	   'sjbh'=>2,                //事件编号
	     	   'jzxs'=>array('userHome'=>array(0,1,2,3,4,5,7,10,11,12,14,15,16,17,18,19,20),'userHome2'=>array(array(0,1,2,3,7,10),array(3,5,5,5,6,0))), //新增探报按钮
	     	   'xstj'=>array('player_level','cg') //显示条件
	     	),		     	
	        //引导领取任务奖励
	     	3=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"m":"RollMediator","data":[{"f":false,"btn":"rw.renwu.btnOk2"}]}]}',	//引导脚本
	     	   'acc1'=>array('oooooo'),  //触发条件1
	     	   'fin1'=>array('rwjl'),            //完成条件1
	     	   'wccs'=>3000000,              //完成引导参数
	     	   'sjbh'=>3,                //事件编号
	     	   'jzxs'=>'',
	     	   'xstj'=>array('player_level','rwjl') //显示条件
	     	),     	
	        //引导抽奖
	     	3=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"data":[{"f":true,"talk":"每次战斗胜利后，还有一次抽奖机会，有机会获得各种宝物。","btn":"21"}],"m":"ChouJiangMediator"},{"data":[{"f":true,"talk":"","btn":"35"}],"m":"ChouJiangMediator"}]}',     	//引导脚本
	     	   'acc1'=>array('cg'),  //触发条件1
	     	   'fin1'=>array('cj'),      //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>4,                //事件编号
	     	   'jzxs'=>'',
	     	   'xstj'=>array('player_level','cj') //显示条件
	     	),	 
	        //引导穿装备  ,{"data":[{"f":true,"talk":"","btn":"18"}],"m":"OwnCityMediator"}  //武将装备处留一个未加入！
	     	4=>array(
	     	   'qz'=>1,  				 //是否强制引导
	     	   'script'=>'{"d":[{"data":[{"f":true,"talk":"大哥，武器可以提升武将的攻击力，咱们去把武器装备起来！","btn":"-2"}],"m":"ChuangGuanZhanMinorMapMediator"},{"data":[{"f":true,"talk":"进入点将台，就能给武将装备武器了！","btn":"4"}],"m":"OwnCityMediator"},{"data":[{"f":true,"talk":"","btn":"18,21,19"}],"m":"OwnCityMediator"},{"data":[{"f":true,"talk":"","btn":"124"}],"m":"Supervise_WarriorScreenMediator"},{"data":[{"f":true,"talk":"","btn":"将0"}],"m":"Supervise_WarriorScreenMediator"},{"data":[{"f":true,"talk":"","btn":"底9","button_id":"中7"}],"m":"Warrior_BagScreenMediator"},{"data":[{"f":true,"talk":"","btn":"b8"}],"m":"Warrior_BagScreenMediator"},{"data":[{"f":true,"talk":"","btn":"1515"}],"m":"Warrior_BagScreenMediator"}]}',     	 //引导脚本
	     	   'acc1'=>array('cj'),  //触发条件1
	     	   'fin1'=>array('czb'),      //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>5,                //事件编号
	     	   'jzxs'=>array('userHome'=>array(0,1,2,3,5,7,12,14,15,16,17,18,19,20),'userHome2'=>array(array(0,1,2,3,18,7,10),array(3,5,5,5,5,6,0))),  //新增显示点将台和背包
	     	   'xstj'=>array('player_level','czb') //显示条件
	     	),
	     	//战斗失败提示
	     	5=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"m":"ZhanDouMediator","data":[{"f":true,"talk":"老大您醒醒啊！敌人已经大伤元气了，我们去休整一番再来搞定他们。","btn":"r.ZhanDouMediator.ZhanDouResultMediator.ok_btn"}]},{"m":"ChuangGuanMediator","data":[{"f":true,"btn":"r.SmallLevelUI.backBtn"}]},{"m":"UserMapMediator","data":[{"btn":"r.userHome.btnMore","f":true,"talk":"人靠衣裳马靠鞍，咱们去商城买几样好装备！"},{"f":true,"btn":"r.userHome.menuMc.btnSC"}]},{"m":"StoreMediator","data":[{"f":true,"btn":"r.store.type2_btn"}]}]}',     	//引导脚本
	     	   'acc1'=>array('dgsb'),  //触发条件1
	     	   'fin1'=>array('gmzb'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>99,                //事件编号
	     	   'jzxs'=>array('userHome'=>array(0,1,2,3,4,7,9,12,13,14,16,17,18),'userHome2'=>array(array(0,1,2,3,18,7),array(3,5,5,5,5,6))),//显示商城
	     	   'xstj'=>array('dgsb','player_level') //显示条件
	     	),
	     	//查看任务2
	     	6=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"m":"OwnCityMediator","data":[{"f":true,"talk":"有新的任务了，江湖百晓生说，行走江湖，先看“任务”","btn":"9"}]},{"data":[{"f":true,"talk":"景阳冈是必经之路，据说景阳岗上有猛虎当道。打败景阳岗上的“猛虎”，向阳谷进发。","btn":"10"}],"m":"MissionMediator"}]}',     	//引导脚本
	     	   'acc1'=>array('czb'),  //触发条件1
	     	   'fin1'=>array('ckrw'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>1000,                //事件编号
	     	   'jzxs'=>array('userHome'=>array(0,1,2,3,4,7,9,12,13,14,16,17,18),'userHome2'=>array(array(0,1,2,3,18,7),array(3,5,5,5,5,6))),//显示商城
	     	   'xstj'=>array('ckrw','player_level') //显示条件
	     	),	   
	     	//引导闯关2
	     	7=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"m":"OwnCityMediator","data":[{"talk":"前面就是景阳冈了，咱们要小心咯。","f":true,"btn":"6"}]},{"data":[{"talk":"","f":true,"btn":"2"}],"m":"ChuangGuanZhanMinorMapMediator"},{"data":[{"talk":"","f":true,"btn":"63,65"}],"m":"ChuangGuanZhanMainMapMediator"},{"m":"ChuangGuanZhanMainMapMediator2","data":[{"talk":"","f":true,"btn":"-2"}]}]}',     	//引导脚本
	     	   'acc1'=>array('ckrw'),  //触发条件1
	     	   'fin1'=>array('cg'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>1001,                //事件编号
	     	   'jzxs'=>array('userHome'=>array(0,1,2,3,4,7,9,12,13,14,16,17,18),'userHome2'=>array(array(0,1,2,3,18,7),array(3,5,5,5,5,6))),//显示商城
	     	   'xstj'=>array('cg','player_level') //显示条件
	     	),	  
	        //引导穿装备2   这里的引导特别处理。。。。。。。
	     	8=>array(
	     	   'qz'=>1,  				 //是否强制引导
	     	   'script'=>'{"d":[{"m":"ChuangGuanZhanMinorMapMediator","data":[{"talk":"打虎获得了战靴，战靴可以提高武将的敏捷度，命中和闪避都会因此提升。让我们去把它装备起来。","f":true,"btn":"-2"}]},{"data":[{"f":true,"talk":"点击点将台，在这里可以给武将配备装备！","btn":"4"}],"m":"OwnCityMediator"},{"data":[{"f":true,"talk":"","btn":"18,21,19"}],"m":"OwnCityMediator"},{"data":[{"f":true,"talk":"","btn":"124"}],"m":"Supervise_WarriorScreenMediator"},{"data":[{"f":true,"talk":"","btn":"将0"}],"m":"Supervise_WarriorScreenMediator"},{"data":[{"f":true,"talk":"","button_id":"中6","btn":"底9"}],"m":"Warrior_BagScreenMediator"},{"data":[{"f":true,"talk":"","btn":"b8"}],"m":"Warrior_BagScreenMediator"},{"data":[{"f":true,"talk":"","btn":"15"}],"m":"Warrior_BagScreenMediator"}]}',     	 //引导脚本
	     	   'acc1'=>array('cg'),  //触发条件1
	     	   'fin1'=>array('czb'),      //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>1002,                //事件编号
	     	   'jzxs'=>array('userHome'=>array(0,1,2,3,4,7,9,10,12,13,14,16,17,18),'userHome2'=>array(array(0,1,2,3,18,7,10),array(3,5,5,5,5,6,0))),  //新增显示点将台和背包
	     	   'xstj'=>array('player_level','czb') //显示条件
	     	),	   
	        //查看任务3
	     	9=>array(
	     	   'qz'=>1,  				 //是否强制引导
	     	   'script'=>'{"d":[{"m":"OwnCityMediator","data":[{"f":true,"talk":"这次是什么“任务”呢？","btn":"9"}]},{"data":[{"f":true,"talk":"潘金莲胆敢杀夫，这案子一定要秉公办理。","btn":"10"}],"m":"MissionMediator"},{"m":"OwnCityMediator","data":[{"talk":"","f":true,"btn":"6"}]}]}',     	 //引导脚本
	     	   'acc1'=>array('czb'),  //触发条件1
	     	   'fin1'=>array('ckrw'),      //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>1003,                //事件编号
	     	   'jzxs'=>array('userHome'=>array(0,1,2,3,4,7,9,10,12,13,14,16,17,18),'userHome2'=>array(array(0,1,2,3,18,7,10),array(3,5,5,5,5,6,0))),  //新增显示点将台和背包
	     	   'xstj'=>array('player_level','ckrw') //显示条件
	     	),	
	     	//引导闯关3
	     	10=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"data":[{"talk":"","f":true,"btn":"3"}],"m":"ChuangGuanZhanMinorMapMediator"},{"data":[{"talk":"","f":true,"btn":"63,65"}],"m":"ChuangGuanZhanMainMapMediator"},{"m":"ChuangGuanZhanMainMapMediator2","data":[{"talk":"","f":true,"btn":"-2"}]}]}',     	//引导脚本
	     	   'acc1'=>array('ckrw'),  //触发条件1
	     	   'fin1'=>array('cg'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>1004,                //事件编号
	     	   'jzxs'=>array('userHome'=>array(0,1,2,3,4,7,9,12,13,14,16,17,18),'userHome2'=>array(array(0,1,2,3,18,7),array(3,5,5,5,5,6))),//显示商城
	     	   'xstj'=>array('cg','player_level') //显示条件
	     	),
	        //引导穿装备3
	     	11=>array(
	     	   'qz'=>1,  				 //是否强制引导
	     	   'script'=>'{"d":[{"m":"ChuangGuanZhanMinorMapMediator","data":[{"talk":"闯关的收获太丰富了，收获了一件胸甲，胸甲可以提升武将的防御力。","f":true,"btn":"-2"}]},{"data":[{"talk":"在城池的“点将台”中装备上胸甲","f":true,"btn":"4"}],"m":"OwnCityMediator"},{"data":[{"f":true,"talk":"","btn":"18,21,19"}],"m":"OwnCityMediator"},{"data":[{"f":true,"talk":"","btn":"124"}],"m":"Supervise_WarriorScreenMediator"},{"data":[{"f":true,"talk":"","btn":"将0"}],"m":"Supervise_WarriorScreenMediator"},{"data":[{"f":true,"button_id":"中5","talk":"","btn":"底9"}],"m":"Warrior_BagScreenMediator"},{"data":[{"f":true,"talk":"","btn":"b8"}],"m":"Warrior_BagScreenMediator"},{"data":[{"f":true,"talk":"","btn":"15"}],"m":"Warrior_BagScreenMediator"}]}',     	 //引导脚本
	     	   'acc1'=>array('cg'),  //触发条件1
	     	   'fin1'=>array('czb'),      //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>1005,                //事件编号
	     	   'jzxs'=>array('userHome'=>array(0,1,2,3,4,7,9,10,12,13,14,16,17,18),'userHome2'=>array(array(0,1,2,3,18,7,10),array(3,5,5,5,5,6,0))),  //新增显示点将台和背包
	     	   'xstj'=>array('player_level','czb') //显示条件
	     	),	
	        //查看任务4
	     	12=>array(
	     	   'qz'=>1,  				 //是否强制引导
	     	   'script'=>'{"d":[{"m":"OwnCityMediator","data":[{"talk":"小潘的靠山是谁呢？真相就在下个任务里面！","f":true,"btn":"9"}]},{"data":[{"talk":"一定要打败西门庆这个大恶霸","f":true,"btn":"10"}],"m":"MissionMediator"}]}',     	 //引导脚本
	     	   'acc1'=>array('czb'),  //触发条件1
	     	   'fin1'=>array('ckrw'),      //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>1006,                //事件编号
	     	   'jzxs'=>array('userHome'=>array(0,1,2,3,4,7,9,10,12,13,14,16,17,18),'userHome2'=>array(array(0,1,2,3,18,7,10),array(3,5,5,5,5,6,0))),  //新增显示点将台和背包
	     	   'xstj'=>array('player_level','ckrw') //显示条件
	     	),		
	     	//引导闯关4
	     	13=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"m":"OwnCityMediator","data":[{"f":true,"talk":"","btn":"6"}]},{"data":[{"talk":"","f":true,"btn":"4"}],"m":"ChuangGuanZhanMinorMapMediator"},{"data":[{"talk":"","f":true,"btn":"63,65"}],"m":"ChuangGuanZhanMainMapMediator"}]}',     	//引导脚本
	     	   'acc1'=>array('ckrw'),  //触发条件1
	     	   'fin1'=>array('cg'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>1007,                //事件编号
	     	   'jzxs'=>array('userHome'=>array(0,1,2,3,4,7,9,12,13,14,16,17,18),'userHome2'=>array(array(0,1,2,3,18,7),array(3,5,5,5,5,6))),//显示商城
	     	   'xstj'=>array('cg','player_level') //显示条件
	     	)		     	     		     		     	     	  	   	  		     		 	     	    	
	     ),
	     2=>array(
          	1=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"m":"ChouJiangMediator","data":[{"talk":"大哥，“任务”是我们闯荡江湖的宝典，记得经常去看任务就知道该怎么做了!","btn":"22","f":true}]}]}',     	//引导脚本
	     	   'acc1'=>array('player_level'),  //触发条件1
	     	   'fin1'=>array('wczs'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>7,                //事件编号
	     	   'jzxs'=>array('userHome'=>array(0,1,2,3,4,7,9,12,13,16,17,18),'userHome2'=>array(array(0,1,2,3,18,7),array(3,5,5,5,5,6))), //显示市场
	     	   'xstj'=>array('player_level') //显示条件	     	   
	     	),
            //引导闯关快活林
	     	2=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"m":"ChuangGuanZhanMinorMapMediator","data":[{"f":true,"talk":"您离胜利已经不远了，加油！","btn":"4"}]},{"data":[{"talk":"","f":true,"btn":"63,65"}],"m":"ChuangGuanZhanMinorMapMediator"}]}',     	//引导脚本
	     	   'acc1'=>array('djms'),  //触发条件1
	     	   'fin1'=>array('djms'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>8,                //事件编号
	     	   'jzxs'=>'',
	     	   'xstj'=>array('player_level','jmssb') //显示条件	     	   
	     	)
	       /* //引导征税
	     	1=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"data":[{"btn":"r.BigLevelUI.backBtn","f":true}],"m":"ChuangGuanMediator"},{"m":"UserMapMediator","data":[{"f":true,"btn":"p.RoleLevelUpInfoMediator.closeBtn"},{"talk":"市场是城池收入的主要来源，记得以后每两小时来收一次税哟！","f":true,"btn":"r.userHome.btnZS","must":false}]}]}',     	//引导脚本
	     	   'acc1'=>array('player_level'),  //触发条件1
	     	   'fin1'=>array('wczs'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>7,                //事件编号
	     	   'jzxs'=>array('userHome'=>array(0,1,2,3,4,7,9,12,13,16,17,18),'userHome2'=>array(array(0,1,2,3,18,7),array(3,5,5,5,5,6))), //显示市场
	     	   'xstj'=>array('player_level') //显示条件	     	   
	     	),
	        //引导闯关快活林
	     	2=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"m":"UserMapMediator","data":[{"f":true,"btn":"r.userHome.btnZhanzheng"}]},{"m":"ChuangGuanMediator","data":[{"f":true,"talk":"这里便是快活林了！老大，出手吧！","btn":"r.BigLevelUI.level2"},{"f":true,"btn":"a.alert.ok_btn"},{"f":true,"talk":"您离胜利已经不远了，加油！","btn":"r.SmallLevelUI.npcContainer.npc4"},{"f":true,"btn":"p.ReadyBattleUI.jlBtn"}]}]}',     	//引导脚本
	     	   'acc1'=>array('djms'),  //触发条件1
	     	   'fin1'=>array('djms'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>8,                //事件编号
	     	   'jzxs'=>'',
	     	   'xstj'=>array('player_level','jmssb') //显示条件	     	   
	     	),
	     	//提示非完美击杀
	     	3=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'fwmjs',     	//引导脚本
	     	   'acc1'=>array('fwmjs'),  //触发条件1
	     	   'fin1'=>array('fwmjs'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>100,                //事件编号
	     	   'jzxs'=>'',
	     	   'xstj'=>array('player_level','fwmjs') //显示条件	     	   
	     	)	*/     		     	
	     ),
	     3=>array(
	        //引导武将训练及完美闯关
	     	/*1=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"data":[{"btn":"r.BigLevelUI.backBtn","f":true,"talk":"想不到这个蒋门神还挺难缠的。老大，我们回城训练一下弟兄们再来收拾他吧！"}],"m":"ChuangGuanMediator"},{"data":[{"btn":"r.userHome.btnJL","f":true},{"btn":"r.userHome.btnJLm.btnXL","f":true}],"m":"UserMapMediator"},{"data":[{"btn":"r.XunLianWuJiangMediator._soldierItem1","f":true},{"btn":"p.XunLian_ChooseHeroMediator._soldierItem0","f":true},{"btn":"p.XunLian_ChooseTypeMediator.bar1","talk":"训练武将是提升武将等级的唯一手段。反正闲着也是闲着，记得每天都要来训练一下！"},{"btn":"r.XunLian_getExpMediator.closeBtn","talk":"训练时武将有一定的几率会获得随机技能。这些技能在战斗中会发挥重要的作用。"}],"m":"XunLianWuJiangMediator"}]}',     	//引导脚本
	     	   'acc1'=>array('player_level'),  //触发条件1
	     	   'fin1'=>array('wcxl'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>9,                //事件编号
	     	   'jzxs'=>array('userHome'=>array(0,1,2,3,4,7,9,12,13,16,17,18),'userHome2'=>array(array(1,2,3,18,7),array(5,5,5,5,6))),//开启训练功能
	     	   'xstj'=>array('player_level','wcxl') //显示条件	     	   
	     	) */    
	     ),
         4=>array(
	        //引导招募武将
	     	/*1=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"m":"ChuangGuanMediator","data":[{"talk":"恭喜老大连升三级！！！"},{"btn":"r.SmallLevelUI.backBtn","talk":"听说咱们城里新开了一家酒馆，聚集了不少英雄好汉。咱们也去看看，顺便招贤纳良吧？","f":true},{"f":true,"btn":"r.BigLevelUI.backBtn","talk":"听说咱们城里新开了一家酒馆，聚集了不少英雄好汉。咱们也去看看，顺便招贤纳良吧？"}]},{"m":"UserMapMediator","data":[{"f":true,"btn":"r.userHome.btnZM"},{"f":true,"btn":"r.userHome.btnZMm.btnZM"}]},{"m":"JiangLingJiuGuanMediator","data":[{"f":true,"talk":"这张顺号称“浪里白条”，水性极佳，豪爽义气，老大何不将他招至麾下？","btn":"r.jiuGuanMc.row_0.btn_zm"},{"f":true,"btn":"a.alert.ok_btn"},{"f":true,"btn":"r.jiuGuanMc.btn_fh"}]}]}',     	//引导脚本
	     	   'acc1'=>array('player_level'),  //触发条件1
	     	   'fin1'=>array('zmwj'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>10,                //事件编号
	     	   'jzxs'=>array('userHome'=>array(0,1,2,3,4,7,9,12,16,17,18),'userHome2'=>array(array(1,2,3,18,7),array(5,5,5,5,6))),  //新增酒馆
	     	   'xstj'=>array('player_level','zmwj') //显示条件	     	   
	     	), 
			//引导再次训练武将
	     	2=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"m":"UserMapMediator","data":[{"talk":"队伍又壮大了！老大，也训练一下这位张顺兄弟吧！","btn":"r.userHome.btnJL"}]}]}',     	//引导脚本
	     	   'acc1'=>array('zmwj'),  //触发条件1
	     	   'fin1'=>array('wcxl'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>101,                //事件编号
	     	   'jzxs'=>'',//开启训练功能
	     	   'xstj'=>array('player_level','wjxl') //显示条件	     	   
	     	),   */	     		     	  
	        //引导升级点将台
	     	/*3=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"m":"UserMapMediator","data":[{"btn":"r.userHome.btnJL","f":true,"must":true,"talk":"武将的数量与点将台等级有关。升级点将台后，您就能在酒馆中招募更多武将了！"},{"btn":"r.userHome.btnJLm.btnSJ","f":true},{"btn":"p.UpgradeMediator.funBtn","f":true},{"btn":"r.userHome.btnZhanzheng","f":true,"talk":"老大，咱们何不继续闯关，也看看新武将的本事？"}]},{"m":"ChuangGuanMediator","data":[{"btn":"r.BigLevelUI.level3","f":true},{"btn":"a.alert.ok_btn","f":true},{"btn":"r.SmallLevelUI.npcContainer.npc1"}]}]}',     	//引导脚本
	     	   'acc1'=>array('wcxl'),  //触发条件1
	     	   'fin1'=>array('sjdjt'),            //完成条件1
	     	   'wccs'=>'djt_level',               //完成引导参数
	     	   'sjbh'=>11,                //事件编号
	     	   'jzxs'=>'',
	     	   'xstj'=>array('player_level','sjdjt') //显示条件	     	   
	     	)	*/     	   	 	     	  	     
	     ),	
	     5=>array(
	        //引导挖矿
	     	/*1=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"m":"UserMapMediator","data":[{"btn":"p.RoleLevelUpInfoMediator.closeBtn"}]},{"m":"ChuangGuanMediator","data":[{"btn":"p.RoleLevelUpInfoMediator.closeBtn"},{"btn":"r.SmallLevelUI.npcContainer.npc1","talk":"这个张都监帮凶众多，咱们可不能放过他！先回城强化一下装备再来！","f":true},{"btn":"r.BigLevelUI.backBtn","talk":"这个张都监帮凶众多，咱们可不能放过他！先回城强化一下装备再来！","f":true}]},{"m":"UserMapMediator","data":[{"f":true,"btn":"r.userHome.btnKS","must":true},{"f":true,"btn":"r.userHome.btnKSm.btnKC","talk":"强化装备需要金刚石，听说这矿山之中就埋藏着不少金刚石！"}]}]}',     	//引导脚本
	     	   'acc1'=>array('player_level'),  //触发条件1
	     	   'fin1'=>array('ydwk'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>12,                //事件编号
	     	   'jzxs'=>array('userHome'=>array(0,1,2,3,4,7,9,18),'userHome2'=>array(array(1,2,3,18,7),array(10,10,10,12,6))), //出现“擂台”建筑，7级时开放，新增建筑“矿山”，开启挖矿和探宝功能，新增“铁匠铺”
	     	   'xstj'=>array('player_level') //显示条件	     	   
	     	), 		     
	        //引导探宝
	     	2=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"data":[{"btn":"r.userHome.btnKS","f":true},{"btn":"r.userHome.btnKSm.btnTB","f":true}],"m":"UserMapMediator"},{"data":[{"btn":"r.TanbaoUI.backBtn","f":true,"talk":"在矿山中探宝也能获得金刚石。探宝要花点元宝，元宝可是好东西。。。"}],"m":"TanbaoMediator"}]}',     	//引导脚本
	     	   'acc1'=>array('ydwk'),  //触发条件1
	     	   'fin1'=>array('ydtb'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>13,                //事件编号
	     	   'jzxs'=>'',
	     	   'xstj'=>array('player_level','ydtb') //显示条件	     	   
	     	), 	*/
	        //引导驻守 ,{"data":[{"talk":"选一条驻守策略，其他人来攻城就没那么容易了！","btn":"15","f":false}],"m":"OwnCityMediator"}
	     	1=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"data":[{"f":true,"btn":"-2","talk":""}],"m":"ChuangGuanZhanMainMapMediator"},{"data":[{"must":true,"f":true,"btn":"18","talk":"老大，城池是咱们的根据地，一定要安排武将驻守，这样还能定时来收钱呢！"}],"m":"OwnCityMediator"},{"data":[{"f":true,"talk":"","btn":"70,71"}],"m":"OwnCityMediator"},{"data":[{"f":true,"talk":"","btn":"武将位1"}],"m":"SelectWarriorScreenMediator"},{"data":[{"f":true,"talk":"","btn":"14,121,129"}],"m":"SelectWarriorScreenMediator"}]}',     	//引导脚本
	     	   'acc1'=>array('player_level'),  //触发条件1
	     	   'fin1'=>array('ydzs'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>16,                //事件编号
	     	   'jzxs'=>array('userHome'=>array(),'userHome2'=>array(array(7),array(6))),
	     	   'xstj'=>array('player_level','ydzs') //显示条件	     	   
	     	), 
	        //引导装备强化	     	
	     	2=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"m":"OwnCityMediator","data":[{"btn":"3","f":true,"talk":"城东有家铁匠铺，在这里您可以强化已有的装备，也可以锻造更高级的装备。"}]},{"m":"OwnCityMediator","data":[{"f":true,"talk":"","btn":"1111"}]},{"m":"TieJiangPuQiangHuaMediator","data":[{"f":true,"talk":"","btn":"zb4"}]},{"m":"TieJiangPuQiangHuaMediator","data":[{"f":true,"talk":"","btn":"zhuangbei_but1"}]},{"m":"TieJiangPuQiangHuaMediator","data":[{"f":true,"talk":"","btn":"QH"}]},{"m":"TieJiangPuQiangHuaMediator","data":[{"f":true,"talk":"如果装备强化成功,穿戴装备武将的实力会大涨！现在咱们再去对付张都监！","btn":"-2"}]}]}',     	//引导脚本
	     	   'acc1'=>array('ydzs'),  //触发条件1
	     	   'fin1'=>array('zbqh'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>14,                //事件编号
	     	   'jzxs'=>'', //出现“擂台”建筑，7级时开放，新增建筑“矿山”，开启挖矿和探宝功能，新增“铁匠铺”
	     	   'xstj'=>array('player_level','zbqh') //显示条件	     	   
	     	)	     	     			     		     	     
	     ),	
	     6=>array(
	        //引导开宝箱
	     	//1=>array(
	     	   //'qz'=>1,  				//是否强制引导
	     	  // 'script'=>'{"must":true,"trunk":{"d":[{"m":"UserMapMediator","data":[{"must":true,"f":true,"talk":"你的等级到达6级，城池增加了新的功能！"},{"f":true,"talk":"你现在可以开启每日宝箱，获得额外奖励了！","btn":"rmc.userHome.rwIcon.iGift"},{"f":true,"btn":"EveryDayGiftMediator.item1.lqBtn"},{"f":true,"btn":"EveryDayGiftMediator.backBtn"}]}]},"branch":{"d":[]}}',     	//引导脚本
	     	  // 'acc1'=>array('ooo','oooo'),  //触发条件1
	     	 //  'fin1'=>array('kbx'),            //完成条件1
	     	 //  'wccs'=>'',               //完成引导参数
	     	 //  'sjbh'=>15,                //事件编号
	     	 //  'jzxs'=>'',
	     	 //  'xstj'=>array('player_level','kbx') //显示条件	     	   
	     	//), 	
	        //引导驻守
	     	/*1=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"must":true,"d":[{"data":[{"f":true,"btn":"r.BigLevelUI.backBtn","must":false}],"m":"ChuangGuanMediator"},{"data":[{"must":true,"f":true,"btn":"r.userHome.btnShou","talk":"老大，城池是咱们的根据地，一定要安排武将驻守，这样还能定时来收钱呢！"},{"f":true,"btn":"a.alert.ok_btn"}],"m":"UserMapMediator"},{"data":[{"f":true,"btn":"r.Occupy_ChooseWuJiangMediator._soldierItem0"},{"f":true,"btn":"r.Occupy_ChooseWuJiangMediator.menuMc.btn0"}],"m":"Occupy_ChooseWuJiangMediator"},{"data":[{"talk":"选一条驻守策略，其他人来攻城就没那么容易了！","btn":null,"f":false}],"m":"UserMapMediator"}]}',     	//引导脚本
	     	   'acc1'=>array('player_level'),  //触发条件1
	     	   'fin1'=>array('ydzs'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>16,                //事件编号
	     	   'jzxs'=>array('userHome'=>array(),'userHome2'=>array(array(1),array(10))),
	     	   'xstj'=>array('player_level','ydzs') //显示条件	     	   
	     	), */	
			/*1=>array(
	     	   'qz'=>0,  				//是否强制引导
	     	   'script'=>'{"d":[{"data":[{"f":true,"talk":"老大，现在您升到了6级，可以占领别人的城池作为领地了！"},{"f":true,"talk":"广袤天地任君驰骋，记得柿子要拣软的捏！","btn":"r.userHome.btnLD","must":true},{"f":true,"btn":"r.userHome.btnLDm.btnZL"}],"m":"UserMapMediator"},{"data":[{"f":true,"btn":"r.lindi.btnZhan"}],"m":"LingdiListMediator"},{"data":[{"f":true,"btn":"r.GongChengMediator.playerItem0"}],"m":"GongChengMediator"},{"data":[{"f":true,"talk":"这座城建设得还不错啊，不如占了它！","btn":"r.otherhome.btnZhan"}],"m":"UserMapOtherMediator"},{"data":[{"talk":"选将出征吧，有可能获得技能书哦！"}],"m":"Occupy_ChooseWuJiangMediator"}]}',     	//引导脚本
	     	   'acc1'=>array('player_level'),  //触发条件1
	     	   'fin1'=>array('ydzl'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>23,                //事件编号
	     	   'jzxs'=>array('userHome'=>array(),'userHome2'=>array(array(1),array(10))),  //需要领地
	     	   'xstj'=>array('player_level','ydzl') //显示条件	     	   
	     	),			
	        //引导加好友
	     	3=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"m":"UserMapMediator","data":[{"btn":"r.userHome.btnMore","must":true,"f":true,"talk":"老大，现在咱们去拜访一下左邻右舍，结交一些新朋友吧！"},{"f":true,"must":true,"btn":"r.userHome.menuMc.btnShejiao"}]},{"m":"FriendsListMediator","data":[{"f":true,"btn":"r.FriendsListMediator.topBtn1"},{"f":true,"btn":"r.FriendsListMediator.bar0","talk":"去这位邻居家看看？"}]},{"m":"UserMapOtherMediator","data":[{"f":true,"btn":"r.otherhome.btnJia"},{"f":true,"btn":"r.otherhome.btnNextF"},{"f":true,"btn":"r.otherhome.btnJia"},{"f":true,"btn":"r.otherhome.btnExit","talk":"您的邀请消息已经发出。如果邻居同意和您成为朋友，您会在探报里得到通知。"}]}]}',     	//引导脚本
	     	   'acc1'=>array('ydzl'),  //触发条件1
	     	   'fin1'=>array('ydjhy'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>17,                //事件编号
	     	   'jzxs'=>'',
	     	   'xstj'=>array('player_level','ydjhy') //显示条件	     	   
	     	), 	*/	
	     	//引导开宝箱和送礼
	     	//3=>array(
	     	  // 'qz'=>1,  				//是否强制引导
	     	  // 'script'=>'{"trunk":{"d":[{"m":"FriendsListMediator","data":[{"must":true,"f":true,"talk":"你已经和其他玩家成为了好友，现在可以去对方的城池进行更多的互动！","btn":"rmc.FriendsListMediator.bar0"}]},{"data":[{"talk":"每天在每一个好友的城池中，都有一次开宝箱的机会，随机获得铜钱或道具，请不要错过！","btn":"rmc.otherhome.btnBao","f":true}],"m":"UserMapOtherMediator"}]},"branch":{"d":[]}}',     	//引导脚本
	     	 //  'acc1'=>array('oooo'),  //触发条件1 'ydkbx'
	     	 //  'fin1'=>array('ydkbxsl'),            //完成条件1
	     	 //  'wccs'=>'',               //完成引导参数
	     	 //  'sjbh'=>18,                //事件编号
	     	 //  'jzxs'=>'',
	     	 //  'xstj'=>array('hylb','ydkbxsl') //显示条件	     	   
	     	//), 	
	     	//引导同意好友
	     	//4=>array(
	     	   //'qz'=>1,  				//是否强制引导
	     	  // 'script'=>'{"trunk":{"d":[{"m":"UserMapMediator","data":[{"must":true,"f":true,"talk":"其他玩家发来好友申请，可以在探报中查看！","btn":"rmc.userHome.btnRizhi"}]}]},"branch":{"d":[]}}',     	//引导脚本
	     	 //  'acc1'=>array('oooo'),  //触发条件1'tyhy'
	     	 //  'fin1'=>array('tyhy'),            //完成条件1
	     	 //  'wccs'=>'',               //完成引导参数
	     	  // 'sjbh'=>19,                //事件编号
	     	  // 'jzxs'=>array(),
	     	 //  'xstj'=>array('tyhy','player_level') //显示条件	     	   
	     	//)		     			     	     			     		     
	     ),	
	     7=>array(		    
	        //引导打擂台
	     	/*1=>array(
	     	   'qz'=>0,  				//是否强制引导
	     	   'script'=>'{"must":true,"d":[{"data":[{"f":true,"btn":"r.BigLevelUI.backBtn","talk":"老大，恭喜您升到7级啦！现在开始您有资格参加擂台比武了，优胜者还能得到爵位册封呢！"}],"m":"ChuangGuanMediator"},{"m":"UserMapMediator","data":[{"btn":"r.userHome.btnLT","f":true,"must":true}]},{"data":[{"f":true,"talk":"擂台每小时开启一次，每次持续30分钟，不要错过机会哟！"}],"m":"LeitaiMainMediator"}]}',     	//引导脚本
	     	   'acc1'=>array('player_level'),  //触发条件1
	     	   'fin1'=>array('dlt'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>20,                //事件编号
	     	   'jzxs'=>array('userHome'=>array(),'userHome2'=>array(array(1),array(10))),
	     	   'xstj'=>array('player_level','dlt') //显示条件	     	   
	     	)*/   
	     ),	
	     8=>array(
		    //升级点将台
	     	/*1=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"must":true,"d":[{"m":"UserMapMediator","data":[{"f":true,"btn":"p.RoleLevelUpInfoMediator.closeBtn","talk":"老大，您的级别已经达到8级，可以回城修缮一下点将台了。"},{"f":true,"btn":"rw.renwu.btnOk2"},{"f":true,"btn":"r.RollUI.rollBtn"},{"f":true,"btn":"r.RollUI.okBtn"}]},{"data":[{"f":true,"btn":"r.SmallLevelUI.backBtn"},{"f":true,"btn":"r.BigLevelUI.backBtn"}],"m":"ChuangGuanMediator"},{"m":"UserMapMediator","data":[{"must":true,"btn":"r.userHome.btnJL","f":true},{"btn":"r.userHome.btnJLm.btnSJ","f":true},{"btn":"p.UpgradeMediator.funBtn","f":true,"talk":"升级点将台之后，会有更多英雄豪杰来投奔你！"}]}]}',     	//引导脚本
	     	   'acc1'=>array('player_level'),  //触发条件1
	     	   'fin1'=>array('sjdjt'),            //完成条件1
	     	   'wccs'=>'djt_level',               //完成引导参数
	     	   'sjbh'=>27,                //事件编号
	     	   'jzxs'=>'',
	     	   'xstj'=>array('sjdjt','player_level') //显示条件	     	   
	     	),  
            //招募新武将
	     	2=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"m":"UserMapMediator","data":[{"f":true,"must":false,"talk":"好汉们常常聚集在酒馆中，大碗喝酒，大块吃肉。老大您要常来看看，才能招到出色的武将。","btn":"r.userHome.btnZM"},{"f":true,"talk":"没有合适的人选也不怕！老大，用武将卡刷新一下，一定会有惊喜！","btn":"r.userHome.btnZMm.btnZM"}]},{"data":[{"f":true,"btn":"r.jiuGuanMc.btn_sx"},{"f":true,"btn":"p.refreshJL.wjk_btn"},{"f":true,"btn":"p.usetool.slot0"},{"f":true,"btn":"a.alert.ok_btn"},{"f":true,"btn":"r.jiuGuanMc.row_0.btn_zm"},{"f":true,"talk":"果然是慧眼识珠，英雄识英雄！","btn":"a.alert.ok_btn"},{"btn":"r.jiuGuanMc.btn_fh"}],"m":"JiangLingJiuGuanMediator"}]}',     	//引导脚本
	     	   'acc1'=>array('sjdjt'),  //触发条件1
	     	   'fin1'=>array('zmwj'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>28,                //事件编号
	     	   'jzxs'=>'',
	     	   'xstj'=>array('zmwj','player_level') //显示条件	     	   
	     	),   			
	        //查看锻造
	     	3=>array(
	     	   'qz'=>0,  				//是否强制引导
	     	   'script'=>'{"d":[{"m":"UserMapMediator","data":[{"f":true,"talk":"老大，我们已经尝试过强化装备，不如去试试锻造一个新装备？","btn":"r.userHome.btnTJP"},{"f":true,"talk":"如果一个装备已经强化到3级或以上，我们就能依照相应图纸，将它锻造为更高一级的装备。","btn":"r.userHome.btnTJPm.btnQH"}]},{"data":[{"f":true,"talk":"每次战斗胜利后都有机会抽奖获得装备图纸。工欲善其事必先利其器！","btn":"r.tjpmc.tab_dz"}],"m":"TieJiangPuMediator"}]}',     	//引导脚本
	     	   'acc1'=>array('zmwj'),  //触发条件1
	     	   'fin1'=>array('zbdz'),            //完成条件1
	     	   'wccs'=>3,               //完成引导参数
	     	   'sjbh'=>21,                //事件编号
	     	   'jzxs'=>'',
	     	   'xstj'=>array('wjxl') //显示条件	     	   
	     	)  */	     
	     ),	
	     10=>array(
	        //引导武将出征
	     	/*1=>array(
	     	   'qz'=>1,  				//是否强制引导
	     	   'script'=>'{"d":[{"data":[{"f":true,"btn":"r.SmallLevelUI.backBtn"},{"f":true,"btn":"r.BigLevelUI.backBtn","talk":"老大，恭喜您升到了10级！我们回城去看看那些辛苦训练的弟兄，犒赏三军！"}],"m":"ChuangGuanMediator"},{"data":[{"f":true,"btn":"r.userHome.btnJL","must":true,"talk":"老大，您现在可以带领三名武将出征了。去点将台物色合适的人选吧！"},{"f":true,"must":true,"btn":"r.userHome.btnJLm.btnWJ"}],"m":"UserMapMediator"},{"data":[{"f":true,"must":true,"btn":"r.jianglingmc.mainListMc.row_2"},{"f":true,"must":true,"btn":"r.jianglingmc.menu1.btn_cz"},{"f":true,"talk":"老大，随着您级别上升、实力增强，您招募的武将数量和带领出征的武将数量就越多。","btn":"r.jianglingmc.tab_cz"}],"m":"JiangLingMediator"}]}',     	//引导脚本
	     	   'acc1'=>array('player_level'),  //触发条件1
	     	   'fin1'=>array('wjcz'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>22,                //事件编号
	     	   'jzxs'=>'',
	     	   'xstj'=>array('player_level','wjcz') //显示条件	     	   
	     	),
	     	//提示历练
	     	2=>array(
	     	   'qz'=>0,  				//是否强制引导
	     	   'script'=>'{"d":[{"m":"UserMapMediator","data":[{"f":true,"must":true,"btn":"r.userHome.btnJL","talk":"对了，还有一件重要的事情忘了告诉您！"},{"f":true,"talk":"武将的天赋是可以通过历练来提升的。天赋越高，武将的能力就越强。","btn":"r.userHome.btnJLm.btnWJ"}]},{"data":[{"f":true,"btn":"r.jianglingmc.mainListMc.row_0"},{"f":true,"btn":"r.jianglingmc.menu2.btn_ck"}],"m":"JiangLingMediator"},{"data":[{"f":true,"btn":"r.infoMc.btn_ll","must":false,"talk":"每成功历练一次，武将天赋就提升一点。"}],"m":"JiangLingInfoMediator"},{"data":[{"talk":"可以邀请好友帮助历练。如果等不及好友响应，也可以用银票来代替好友的帮助。"}],"m":"JiangLingLiLianMediator"}]}',     	//引导脚本
	     	   'acc1'=>array('tsll'),  //触发条件1
	     	   'fin1'=>array('tsll'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>122,                //事件编号
	     	   'jzxs'=>'',
	     	   'xstj'=>array('tsll') //显示条件	     	   
	     	),*/
            //提示交易银票
	     	/*3=>array(
	     	   'qz'=>0,  				//是否强制引导
	     	   'script'=>'{"d":[{"m":"UserMapMediator","data":[{"f":true,"btn":"r.userHome.btnZS","talk":"如果银票不够了，可以去市场里兑换。","mmust":true,"must":true},{"f":true,"must":true,"btn":"r.userHome.btnZSm.btnDH","talk":"在市场中选择交易功能，即可用军粮和铜钱兑换银票。"},{"talk":"小小银票妙用无穷，还可以用来提前结束武将的训练和历练时间！","btn":null}]}]}',     	//引导脚本
	     	   'acc1'=>array('ypjy'),  //触发条件1
	     	   'fin1'=>array('ypjy'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>123,                //事件编号
	     	   'jzxs'=>'',
	     	   'xstj'=>array('ypjy') //显示条件	     	   
	     	)	*/			
	     ),	
	     12=>array(
	        //引导占领
	     	/*1=>array(
	     	   'qz'=>0,  				//是否强制引导
	     	   'script'=>'{"d":[{"data":[{"f":true,"talk":"老大，现在您升到了6级，可以占领别人的城池作为领地了！"},{"f":true,"talk":"广袤天地任君驰骋，记得柿子要拣软的捏！","btn":"r.userHome.btnLD","must":true},{"f":true,"btn":"r.userHome.btnLDm.btnZL"}],"m":"UserMapMediator"},{"data":[{"f":true,"btn":"r.lindi.btnZhan"}],"m":"LingdiListMediator"},{"data":[{"f":true,"btn":"r.GongChengMediator.playerItem0"}],"m":"GongChengMediator"},{"data":[{"f":true,"talk":"这座城建设得还不错啊，不如占了它！","btn":"r.otherhome.btnZhan"}],"m":"UserMapOtherMediator"},{"data":[{"talk":"选将出征吧，有可能获得技能书哦！","btn":null}],"m":"Occupy_ChooseWuJiangMediator"}]}',     	//引导脚本
	     	   'acc1'=>array('player_level'),  //触发条件1
	     	   'fin1'=>array('ydzl'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>23,                //事件编号
	     	   'jzxs'=>'',  //需要领地
	     	   'xstj'=>array('player_level','ydzl') //显示条件	     	   
	     	) */ 	     
	     ),		
	     32=>array(
	        //引导合成武将 
	     	/*1=>array(
	     	   'qz'=>0,  				//是否强制引导
	     	   'script'=>'{"d":[{"m":"XunLianWuJiangMediator","data":[{"f":true,"talk":"你有一个武将达到了40级，已经满足了合成条件，合成后可以提高你的武将属性！现在去查看详情！","btn":"r.XunLian_getExpMediator.closeBtn"},{"f":true,"btn":"r.XunLianWuJiangMediator._closeBtn"}]},{"data":[{"f":true,"btn":"r.userHome.btnJL"},{"f":true,"talk":"当你的武将满足合成条件时，就可以在这里进行合成！","btn":"r.userHome.btnJLm.btnWJ"}],"m":"UserMapMediator"},{"data":[{"talk":"请选择一个40级的绿色武将","btn":null},{"f":true,"btn":"r.jianglingmc.menu2.btn_ck"}],"m":"JiangLingMediator"},{"data":[{"f":true,"btn":"r.infoMc.btn_hc","talk":"当你的武将满足合成条件时，就可以在这里进行合成！"}],"m":"JiangLingInfoMediator"}]}',     	//引导脚本
	     	   'acc1'=>array('hcwj'),  //触发条件1
	     	   'fin1'=>array('hcwj'),            //完成条件1
	     	   'wccs'=>'',               //完成引导参数
	     	   'sjbh'=>26,                //事件编号
	     	   'jzxs'=>'',
	     	   'xstj'=>array('player_level','hcwj') //显示条件	     	   
	     	) */ 	   
	     )	     
	);
	if (!empty($data[$level])) {
		return $data[$level];
	} elseif (!empty($showAll)) {
		foreach ($data as $key => $value) {
			foreach ($value as $showValue) {
				$returnValue[] = $showValue;
			}
		}
		return $returnValue;
	} else {
		return false;
	}
}